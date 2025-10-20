<?php
<?php
/*
 * ----------------------------------------------------------------------------
 * Formdin 5 Framework
 * SourceCode https://github.com/bjverde/formDin5
 * @author Reinaldo A. Barrêto Junior
 * 
 * É uma reconstrução do FormDin 4 Sobre o Adianti 7.X
 * @author Luís Eugênio Barbosa do FormDin 4
 * 
 * Adianti Framework é uma criação Adianti Solutions Ltd
 * @author Pablo Dall'Oglio
 * ----------------------------------------------------------------------------
 * This file is part of Formdin Framework.
 *
 * Formdin Framework is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public License version 3
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License version 3
 * along with this program; if not,  see <http://www.gnu.org/licenses/>
 * or write to the Free Software Foundation, Inc., 51 Franklin Street,
 * Fifth Floor, Boston, MA  02110-1301, USA.
 * ----------------------------------------------------------------------------
 * Este arquivo é parte do Framework Formdin.
 *
 * O Framework Formdin é um software livre; você pode redistribuí-lo e/ou
 * modificá-lo dentro dos termos da GNU LGPL versão 3 como publicada pela Fundação
 * do Software Livre (FSF).
 *
 * Este programa é distribuído na esperança que possa ser útil, mas SEM NENHUMA
 * GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer MERCADO ou
 * APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/LGPL em português
 * para maiores detalhes.
 *
 * Você deve ter recebido uma cópia da GNU LGPL versão 3, sob o título
 * "LICENCA.txt", junto com esse programa. Se não, acesse <http://www.gnu.org/licenses/>
 * ou escreva para a Fundação do Software Livre (FSF) Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA 02111-1301, USA.
 */

/**
 * Classe para um relogio de logout por tempo de inatividade.
 * Verifica se o usuário está inativo e, após um tempo configurado,
 * exibe avisos visuais e sonoros antes de efetuar o logout automático.
 * 
 * 
 * @author Reinaldo A. Barrêto Junior
 */
class TFormDinLogoutTimer
{
    // === ATRIBUTOS DE CONFIGURAÇÃO ===
    
    // Tempos
    private static $timeout_seconds = 60;
    private static $timeout_ms = 60000;
    private static $check_interval = 1000;
    
    // Mensagens
    private static $msg_final = 'Sessão finalizada por inatividade';
    private static $titulo_sessao = 'Sessão Expirada';
    
    // Cores e estilos normais (verde)
    private static $normal_fonte_cor = '#28a745';
    private static $normal_fonte_size = '1.5em';
    private static $normal_fonte_weight = 'normal';
    
    // Configurações de aviso (amarelo/laranja)
    private static $aviso_limite_superior = 0.5;
    private static $aviso_fonte_cor = '#ffc107';
    private static $aviso_fundo = '#fff3cd';
    private static $aviso_borda = '4px solid #f39c12';
    private static $aviso_fonte_weight = 'bold';
    
    // Configurações críticas (branco/vermelho)
    private static $critico_limite_superior = 0.15;
    private static $critico_fonte_cor = '#ffffff';
    private static $critico_fundo = '#dc3545';
    private static $critico_borda = '4px solid #bd2130';
    private static $critico_fonte_weight = 'bold';
    
    // URLs
    private static $logout_url = 'index.php?class=LoginForm&method=onLogout&static=1';
    
    // Eventos monitorados
    private static $events = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click', 'keyup'];
    
    // Configurações de áudio
    private static $audio_enabled = true;
    private static $audio_frequency_normal = 800;
    private static $audio_frequency_critical = 1000;
    private static $audio_beeps_normal = 3;
    private static $audio_beeps_critical = 5;
    private static $audio_volume = 0.2;
    
    /**
     * Método mágico para GET/SET de propriedades
     * Converte camelCase para snake_case (ex: getTimeoutSeconds -> timeout_seconds)
     * 
     * @param string $method Nome do método
     * @param array $args Argumentos do método
     * @return mixed Valor da propriedade ou true para setters
     * @throws Exception Se a propriedade não existir
     */
    public static function __callStatic($method, $args)
    {
        // Getter mágico (ex: getTimeoutSeconds -> timeout_seconds)
        if (strpos($method, 'get') === 0) {
            $property = self::camelToSnake(substr($method, 3));
            
            if (property_exists(__CLASS__, $property)) {
                return self::$$property;
            }
            
            throw new Exception("Propriedade '{$property}' não existe em TotemConfig");
        }
        
        // Setter mágico (ex: setTimeoutSeconds -> timeout_seconds)
        if (strpos($method, 'set') === 0) {
            $property = self::camelToSnake(substr($method, 3));
            
            if (property_exists(__CLASS__, $property)) {
                self::$$property = $args[0];
                
                // Se alterar timeout_seconds, atualizar timeout_ms automaticamente
                if ($property === 'timeout_seconds') {
                    self::$timeout_ms = $args[0] * 1000;
                }
                
                return true;
            }
            
            throw new Exception("Propriedade '{$property}' não existe em TotemConfig");
        }
        
        throw new Exception("Método '{$method}' não existe em TotemConfig");
    }
    
    /**
     * Converte camelCase para snake_case
     * 
     * @param string $input String em camelCase
     * @return string String em snake_case
     */
    private static function camelToSnake($input)
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $input));
    }
    
    /**
     * Obtém as configurações completas do totem
     * 
     * @return array Configurações do totem
     */
    public static function getConfig()
    {
        return [
            // === TEMPOS ===
            'timeout_seconds' => self::$timeout_seconds,
            'timeout_ms' => self::$timeout_ms,
            'check_interval' => self::$check_interval,
            
            // === MENSAGENS ===
            'msg_final' => self::$msg_final,
            'titulo_sessao' => self::$titulo_sessao,
            
            // === CORES E ESTILOS NORMAIS (VERDE) ===
            'normal_fonte_cor' => self::$normal_fonte_cor,
            'normal_fonte_size' => self::$normal_fonte_size,
            'normal_fonte_weight' => self::$normal_fonte_weight,
            
            // === CONFIGURAÇÕES DE AVISO (AMARELO/LARANJA) ===
            'aviso_limite_superior' => self::$aviso_limite_superior,
            'aviso_fonte_cor' => self::$aviso_fonte_cor,
            'aviso_fundo' => self::$aviso_fundo,
            'aviso_borda' => self::$aviso_borda,
            'aviso_fonte_weight' => self::$aviso_fonte_weight,
            
            // === CONFIGURAÇÕES CRÍTICAS (BRANCO/VERMELHO) ===
            'critico_limite_superior' => self::$critico_limite_superior,
            'critico_fonte_cor' => self::$critico_fonte_cor,
            'critico_fundo' => self::$critico_fundo,
            'critico_borda' => self::$critico_borda,
            'critico_fonte_weight' => self::$critico_fonte_weight,
            
            // === URLS ===
            'logout_url' => self::$logout_url,
            
            // === EVENTOS MONITORADOS ===
            'events' => self::$events,
            
            // === CONFIGURAÇÕES DE ÁUDIO ===
            'audio_enabled' => self::$audio_enabled,
            'audio_frequency_normal' => self::$audio_frequency_normal,
            'audio_frequency_critical' => self::$audio_frequency_critical,
            'audio_beeps_normal' => self::$audio_beeps_normal,
            'audio_beeps_critical' => self::$audio_beeps_critical,
            'audio_volume' => self::$audio_volume,
        ];
    }
    
    /**
     * Obtém configuração específica
     * 
     * @param string $key Chave da configuração
     * @param mixed $default Valor padrão se não encontrar
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        if (property_exists(__CLASS__, $key)) {
            return self::$$key;
        }
        
        return $default;
    }
    
    /**
     * Define configuração específica
     * 
     * @param string $key Chave da configuração
     * @param mixed $value Valor da configuração
     * @return bool
     */
    public static function set($key, $value)
    {
        if (property_exists(__CLASS__, $key)) {
            self::$$key = $value;
            
            // Se alterar timeout_seconds, atualizar timeout_ms automaticamente
            if ($key === 'timeout_seconds') {
                self::$timeout_ms = $value * 1000;
            }
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Métodos de conveniência para timeout
     */
    public static function getTimeoutSeconds()
    {
        return self::$timeout_seconds;
    }
    
    public static function setTimeoutSeconds($seconds)
    {
        self::$timeout_seconds = (int)$seconds;
        self::$timeout_ms = $seconds * 1000;
    }
    
    public static function getTimeoutMs()
    {
        return self::$timeout_ms;
    }
    
    /**
     * Converte configurações para JavaScript
     * 
     * @return string JSON das configurações
     */
    public static function toJavaScript()
    {
        $config = self::getConfig();
        return json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    
    /**
     * Obtém apenas configurações visuais
     * 
     * @return array
     */
    public static function getVisualConfig()
    {
        return [
            'normal' => [
                'fonte_cor' => self::$normal_fonte_cor,
                'fonte_size' => self::$normal_fonte_size,
                'fonte_weight' => self::$normal_fonte_weight,
            ],
            'aviso' => [
                'fonte_cor' => self::$aviso_fonte_cor,
                'fundo' => self::$aviso_fundo,
                'borda' => self::$aviso_borda,
                'fonte_weight' => self::$aviso_fonte_weight,
                'limite' => self::$aviso_limite_superior,
            ],
            'critico' => [
                'fonte_cor' => self::$critico_fonte_cor,
                'fundo' => self::$critico_fundo,
                'borda' => self::$critico_borda,
                'fonte_weight' => self::$critico_fonte_weight,
                'limite' => self::$critico_limite_superior,
            ],
        ];
    }
    
    /**
     * Métodos de conveniência para cores
     */
    public static function getNormalColor()
    {
        return self::$normal_fonte_cor;
    }
    
    public static function setNormalColor($color)
    {
        self::$normal_fonte_cor = $color;
    }
    
    public static function getWarningColor()
    {
        return self::$aviso_fonte_cor;
    }
    
    public static function setWarningColor($color)
    {
        self::$aviso_fonte_cor = $color;
    }
    
    public static function getCriticalColor()
    {
        return self::$critico_fonte_cor;
    }
    
    public static function setCriticalColor($color)
    {
        self::$critico_fonte_cor = $color;
    }
    
    /**
     * Métodos de conveniência para limites
     */
    public static function getWarningLimit()
    {
        return self::$aviso_limite_superior;
    }
    
    public static function setWarningLimit($limit)
    {
        self::$aviso_limite_superior = (float)$limit;
    }
    
    public static function getCriticalLimit()
    {
        return self::$critico_limite_superior;
    }
    
    public static function setCriticalLimit($limit)
    {
        self::$critico_limite_superior = (float)$limit;
    }
    
    /**
     * Métodos de conveniência para áudio
     */
    public static function isAudioEnabled()
    {
        return self::$audio_enabled;
    }
    
    public static function setAudioEnabled($enabled)
    {
        self::$audio_enabled = (bool)$enabled;
    }
    
    public static function getAudioVolume()
    {
        return self::$audio_volume;
    }
    
    public static function setAudioVolume($volume)
    {
        self::$audio_volume = max(0.0, min(1.0, (float)$volume));
    }
    
    /**
     * Reset para valores padrão
     */
    public static function resetToDefaults()
    {
        self::$timeout_seconds = 60;
        self::$timeout_ms = 60000;
        self::$check_interval = 1000;
        self::$msg_final = 'Sessão finalizada por inatividade';
        self::$titulo_sessao = 'Sessão Expirada';
        self::$normal_fonte_cor = '#28a745';
        self::$normal_fonte_size = '1.5em';
        self::$normal_fonte_weight = 'normal';
        self::$aviso_limite_superior = 0.5;
        self::$aviso_fonte_cor = '#ffc107';
        self::$aviso_fundo = '#fff3cd';
        self::$aviso_borda = '4px solid #f39c12';
        self::$aviso_fonte_weight = 'bold';
        self::$critico_limite_superior = 0.15;
        self::$critico_fonte_cor = '#ffffff';
        self::$critico_fundo = '#dc3545';
        self::$critico_borda = '4px solid #bd2130';
        self::$critico_fonte_weight = 'bold';
        self::$logout_url = 'index.php?class=LoginForm&method=onLogout&static=1';
        self::$events = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click', 'keyup'];
        self::$audio_enabled = true;
        self::$audio_frequency_normal = 800;
        self::$audio_frequency_critical = 1000;
        self::$audio_beeps_normal = 3;
        self::$audio_beeps_critical = 5;
        self::$audio_volume = 0.2;
    }
}