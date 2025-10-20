/*
 * ----------------------------------------------------------------------------
 * Formdin 5 Framework
 * SourceCode https://github.com/bjverde/formDin5
 * @author Reinaldo A. Barrêto Junior
 * 
 * É uma reconstrução do FormDin 4 Sobre o Adianti 7.X
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
 * Este programa é distribuí1do na esperança que possa ser útil, mas SEM NENHUMA
 * GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer MERCADO ou
 * APLICAÇÃO EM PARTICULAR. Veja a Licen?a Pública Geral GNU/LGPL em portugu?s
 * para maiores detalhes.
 *
 * Você deve ter recebido uma cópia da GNU LGPL versão 3, sob o título
 * "LICENCA.txt", junto com esse programa. Se não, acesse <http://www.gnu.org/licenses/>
 * ou escreva para a Fundação do Software Livre (FSF) Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA 02111-1301, USA.
 */

/**
 * Controle de inatividade por tempo.
 * Arquivo responsável por carregar e inicializar o FormDin5LogoutTimer.js automaticamente
 * 
 * @author Reinaldo A. Barrêto Junior
 * @param {Object} config - Configurações vindas do PHP
 */
function initTotemInactivity(config) {
    // Proteção contra múltiplas inicializações
    if (window.totemInactivityControl && window.totemInactivityControl.timer) {
        console.log('TotemInactivity já está ativo, parando instância anterior...');
        window.totemInactivityControl.stop();
    }
    
    // Aguarda o DOM estar pronto
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            initTotemInactivity(config);
        });
        return;
    }
    
    // Verifica se a classe TotemInactivity está disponível
    if (typeof TotemInactivity === 'undefined') {
        console.error('TotemInactivity.js não foi carregado');
        return false;
    }
    
    // Verifica se o método init existe
    if (typeof TotemInactivity.init !== 'function') {
        console.error('TotemInactivity.init() não encontrado');
        return false;
    }
    
    // Inicializa o totem
    try {
        TotemInactivity.init(config);
        console.log('✅ Totem inicializado com sucesso');
        return true;
    } catch (error) {
        console.error('Erro ao inicializar totem:', error);
        return false;
    }
}

/**
 * Auto-inicialização se existir configuração global
 * (Para compatibilidade com implementações antigas)
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('TotemInactivityInit.js carregado');
    
    // Aguarda um pouco para garantir que todos os scripts foram carregados
    setTimeout(function() {
        console.log('=== VERIFICANDO INICIALIZAÇÃO ===');
        
        // Verifica se existe configuração global definida
        if (typeof TOTEM_CONFIG !== 'undefined') {
            console.log('TOTEM_CONFIG encontrado:', TOTEM_CONFIG);
            console.log('Verificando se TotemInactivity está disponível...');
            
            if (typeof TotemInactivity !== 'undefined') {
                console.log('TotemInactivity disponível! Inicializando...');
                initTotemInactivity(TOTEM_CONFIG);
            } else {
                console.error('TotemInactivity não carregado ainda, aguardando mais tempo...');
                // Tenta novamente após mais tempo
                setTimeout(function() {
                    if (typeof TotemInactivity !== 'undefined') {
                        console.log('TotemInactivity carregado na segunda tentativa!');
                        initTotemInactivity(TOTEM_CONFIG);
                    } else {
                        console.error('TotemInactivity não foi carregado após segunda tentativa');
                    }
                }, 500);
            }
        } else {
            console.log('TOTEM_CONFIG não encontrado - aguardando inicialização manual');
        }
    }, 200);
});

/**
 * Função para reconfigurar o totem em runtime
 * 
 * @param {Object} newConfig - Novas configurações
 */
function reconfigureTotem(newConfig) {
    if (window.totemInactivityControl) {
        window.totemInactivityControl.stop();
    }
    return initTotemInactivity(newConfig);
}

// Torna as funções disponíveis globalmente
window.initTotemInactivity = initTotemInactivity;
window.reconfigureTotem = reconfigureTotem;