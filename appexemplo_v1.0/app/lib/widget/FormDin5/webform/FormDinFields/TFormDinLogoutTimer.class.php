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
class TFormDinLogoutTimer extends TFormDinGenericField
{
    // === ATRIBUTOS DE CONFIGURAÇÃO ===
    
    // Tempos
    private $timeout_seconds = 60;
    private $timeout_ms = 60000;
    private $check_interval = 1000;
    
    // Mensagens
    private $msg_final = 'Sessão finalizada por inatividade';
    private $titulo_sessao = 'Sessão Expirada';
    
    // Cores e estilos normais (verde)
    private $normal_fonte_cor = '#28a745';
    private $normal_fonte_size = '1.5em';
    private $normal_fonte_weight = 'normal';
    
    // Configurações de aviso (amarelo/laranja)
    private $aviso_limite_superior = 0.5;
    private $aviso_fonte_cor = '#ffc107';
    private $aviso_fundo = '#fff3cd';
    private $aviso_borda = '4px solid #f39c12';
    private $aviso_fonte_weight = 'bold';
    
    // Configurações críticas (branco/vermelho)
    private $critico_limite_superior = 0.15;
    private $critico_fonte_cor = '#ffffff';
    private $critico_fundo = '#dc3545';
    private $critico_borda = '4px solid #bd2130';
    private $critico_fonte_weight = 'bold';
    
    // URLs
    private $logout_url = 'index.php?class=LoginForm&method=onLogout&static=1';
    
    // Eventos monitorados
    private $events = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click', 'keyup'];
    
    // Configurações de áudio
    private $audio_enabled = true;
    private $audio_frequency_normal = 800;
    private $audio_frequency_critical = 1000;
    private $audio_beeps_normal = 3;
    private $audio_beeps_critical = 5;
    private $audio_volume = 0.2;



    /**
     * Pegar informações geolocalização do navegador
     *
     * @param string  $idField         -01: ID do campo
     * @param string  $label           -02: Label do campo, usado para validações
     * @param boolean $boolRequired    -03: Campo obrigatório ou não. Default FALSE = não obrigatório, TRUE = obrigatório
     * @return TElement
     */
    public function __construct(string $idField
                               ,string $label
                               ,$boolRequired  =null
                               )
    {
        $this->setIdDivGeo($idField);

        
        
        $adiantiObj = $this->getDivGeo($idField,$boolRequired);
        parent::__construct($adiantiObj,$this->getIdDivGeo(),$label,false,null,null);
        $this->setLabel($label,$boolRequired);

        return $this->getAdiantiObj();
    }

    
    /**
     * Método mágico para GET/SET de propriedades
     * Converte camelCase para snake_case (ex: getTimeoutSeconds -> timeout_seconds)
     * 
     * @param string $method Nome do método
     * @param array $args Argumentos do método
     * @return mixed Valor da propriedade ou true para setters
     * @throws Exception Se a propriedade não existir
     */
    public function __call($method, $args)
    {
        // Getter mágico (ex: getTimeoutSeconds -> timeout_seconds)
        if (strpos($method, 'get') === 0) {
            $property = $this->camelToSnake(substr($method, 3));
            
            if (property_exists($this, $property)) {
                return $this->$property;
            }
            
            throw new Exception("Propriedade '{$property}' não existe em TFormDinLogoutTimer");
        }
        
        // Setter mágico (ex: setTimeoutSeconds -> timeout_seconds)
        if (strpos($method, 'set') === 0) {
            $property = $this->camelToSnake(substr($method, 3));
            
            if (property_exists($this, $property)) {
                $this->$property = $args[0];
                
                // Se alterar timeout_seconds, atualizar timeout_ms automaticamente
                if ($property === 'timeout_seconds') {
                    $this->timeout_ms = $args[0] * 1000;
                }
                
                return true;
            }
            
            throw new Exception("Propriedade '{$property}' não existe em TFormDinLogoutTimer");
        }
        
        throw new Exception("Método '{$method}' não existe em TFormDinLogoutTimer");
    }
    
    /**
     * Converte camelCase para snake_case
     * 
     * @param string $input String em camelCase
     * @return string String em snake_case
     */
    private function camelToSnake($input)
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $input));
    }
    
    /**
     * Obtém as configurações completas do totem
     * 
     * @return array Configurações do totem
     */
    public function getConfig()
    {
        return [
            // === TEMPOS ===
            'timeout_seconds' => $this->timeout_seconds,
            'timeout_ms' => $this->timeout_ms,
            'check_interval' => $this->check_interval,
            
            // === MENSAGENS ===
            'msg_final' => $this->msg_final,
            'titulo_sessao' => $this->titulo_sessao,
            
            // === CORES E ESTILOS NORMAIS (VERDE) ===
            'normal_fonte_cor' => $this->normal_fonte_cor,
            'normal_fonte_size' => $this->normal_fonte_size,
            'normal_fonte_weight' => $this->normal_fonte_weight,
            
            // === CONFIGURAÇÕES DE AVISO (AMARELO/LARANJA) ===
            'aviso_limite_superior' => $this->aviso_limite_superior,
            'aviso_fonte_cor' => $this->aviso_fonte_cor,
            'aviso_fundo' => $this->aviso_fundo,
            'aviso_borda' => $this->aviso_borda,
            'aviso_fonte_weight' => $this->aviso_fonte_weight,
            
            // === CONFIGURAÇÕES CRÍTICAS (BRANCO/VERMELHO) ===
            'critico_limite_superior' => $this->critico_limite_superior,
            'critico_fonte_cor' => $this->critico_fonte_cor,
            'critico_fundo' => $this->critico_fundo,
            'critico_borda' => $this->critico_borda,
            'critico_fonte_weight' => $this->critico_fonte_weight,
            
            // === URLS ===
            'logout_url' => $this->logout_url,
            
            // === EVENTOS MONITORADOS ===
            'events' => $this->events,
            
            // === CONFIGURAÇÕES DE ÁUDIO ===
            'audio_enabled' => $this->audio_enabled,
            'audio_frequency_normal' => $this->audio_frequency_normal,
            'audio_frequency_critical' => $this->audio_frequency_critical,
            'audio_beeps_normal' => $this->audio_beeps_normal,
            'audio_beeps_critical' => $this->audio_beeps_critical,
            'audio_volume' => $this->audio_volume,
        ];
    }
    
    /**
     * Obtém configuração específica
     * 
     * @param string $key Chave da configuração
     * @param mixed $default Valor padrão se não encontrar
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if (property_exists($this, $key)) {
            return $this->$key;
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
    public function set($key, $value)
    {
        if (property_exists($this, $key)) {
            $this->$key = $value;
            
            // Se alterar timeout_seconds, atualizar timeout_ms automaticamente
            if ($key === 'timeout_seconds') {
                $this->timeout_ms = $value * 1000;
            }
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Métodos de conveniência para timeout
     */
    public function getTimeoutSeconds()
    {
        return $this->timeout_seconds;
    }
    
    public function setTimeoutSeconds($seconds)
    {
        $this->timeout_seconds = (int)$seconds;
        $this->timeout_ms = $seconds * 1000;
    }
    
    public function getTimeoutMs()
    {
        return $this->timeout_ms;
    }
    
    /**
     * Converte configurações para JavaScript
     * 
     * @return string JSON das configurações
     */
    public function toJavaScript()
    {
        $config = $this->getConfig();
        return json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    
    /**
     * Obtém apenas configurações visuais
     * 
     * @return array
     */
    public function getVisualConfig()
    {
        return [
            'normal' => [
                'fonte_cor' => $this->normal_fonte_cor,
                'fonte_size' => $this->normal_fonte_size,
                'fonte_weight' => $this->normal_fonte_weight,
            ],
            'aviso' => [
                'fonte_cor' => $this->aviso_fonte_cor,
                'fundo' => $this->aviso_fundo,
                'borda' => $this->aviso_borda,
                'fonte_weight' => $this->aviso_fonte_weight,
                'limite' => $this->aviso_limite_superior,
            ],
            'critico' => [
                'fonte_cor' => $this->critico_fonte_cor,
                'fundo' => $this->critico_fundo,
                'borda' => $this->critico_borda,
                'fonte_weight' => $this->critico_fonte_weight,
                'limite' => $this->critico_limite_superior,
            ],
        ];
    }
    
    /**
     * Métodos de conveniência para cores
     */
    public function getNormalColor()
    {
        return $this->normal_fonte_cor;
    }
    
    public function setNormalColor($color)
    {
        $this->normal_fonte_cor = $color;
    }
    
    public function getWarningColor()
    {
        return $this->aviso_fonte_cor;
    }
    
    public function setWarningColor($color)
    {
        $this->aviso_fonte_cor = $color;
    }
    
    public function getCriticalColor()
    {
        return $this->critico_fonte_cor;
    }
    
    public function setCriticalColor($color)
    {
        $this->critico_fonte_cor = $color;
    }
    
    /**
     * Métodos de conveniência para limites
     */
    public function getWarningLimit()
    {
        return $this->aviso_limite_superior;
    }
    
    public function setWarningLimit($limit)
    {
        $this->aviso_limite_superior = (float)$limit;
    }
    
    public function getCriticalLimit()
    {
        return $this->critico_limite_superior;
    }
    
    public function setCriticalLimit($limit)
    {
        $this->critico_limite_superior = (float)$limit;
    }
    
    /**
     * Métodos de conveniência para áudio
     */
    public function isAudioEnabled()
    {
        return $this->audio_enabled;
    }
    
    public function setAudioEnabled($enabled)
    {
        $this->audio_enabled = (bool)$enabled;
    }
    
    public function getAudioVolume()
    {
        return $this->audio_volume;
    }
    
    public function setAudioVolume($volume)
    {
        $this->audio_volume = max(0.0, min(1.0, (float)$volume));
    }
    
    /**
     * Reset para valores padrão
     */
    public function resetToDefaults()
    {
        $this->timeout_seconds = 60;
        $this->timeout_ms = 60000;
        $this->check_interval = 1000;
        $this->msg_final = 'Sessão finalizada por inatividade';
        $this->titulo_sessao = 'Sessão Expirada';
        $this->normal_fonte_cor = '#28a745';
        $this->normal_fonte_size = '1.5em';
        $this->normal_fonte_weight = 'normal';
        $this->aviso_limite_superior = 0.5;
        $this->aviso_fonte_cor = '#ffc107';
        $this->aviso_fundo = '#fff3cd';
        $this->aviso_borda = '4px solid #f39c12';
        $this->aviso_fonte_weight = 'bold';
        $this->critico_limite_superior = 0.15;
        $this->critico_fonte_cor = '#ffffff';
        $this->critico_fundo = '#dc3545';
        $this->critico_borda = '4px solid #bd2130';
        $this->critico_fonte_weight = 'bold';
        $this->logout_url = 'index.php?class=LoginForm&method=onLogout&static=1';
        $this->events = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click', 'keyup'];
        $this->audio_enabled = true;
        $this->audio_frequency_normal = 800;
        $this->audio_frequency_critical = 1000;
        $this->audio_beeps_normal = 3;
        $this->audio_beeps_critical = 5;
        $this->audio_volume = 0.2;
    }
}