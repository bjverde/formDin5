/**
 * TotemInactivityInit.js - Inicializador do sistema de inatividade do totem
 * 
 * Arquivo responsável por carregar e inicializar o TotemInactivity automaticamente
 * Segue o padrão FormDin5 de carregamento modular
 * 
 * @author Sistema de Controle de Ponto
 * @version 1.0
 */

/**
 * Função global para inicializar o totem com configurações
 * 
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