<?php

use Adianti\Registry\TSession;

class js_logouttimer extends TPage
{
    protected $form; // registration form
    private static $formName = 'js_logouttimer';
    
    // trait com onReload, onSearch, onDelete...
    use Adianti\Base\AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();

        // load the styles
        TPage::include_css('app/resources/css_form02.css');
        
        // create the form
        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle( 'LogOut por tempo' );

        // Criando timer 50 segundos de timeout e debug ATIVADO para desenvolvimento
        $fd5LogoutTimer = new TFormDinLogoutTimer('logoutTimer','Logout Timer', 50, true);
        
        // === EXEMPLO: CONFIGURANDO CORES PERSONALIZADAS ===
        
        // Cores do estado NORMAL (azul suave)
        $fd5LogoutTimer->setNormalColor('#007bff');                    // Azul suave
        $fd5LogoutTimer->set('normal_fonte_size', '1.8em');           // Fonte maior
        
        // Cores do estado AVISO (laranja vibrante)
        $fd5LogoutTimer->setWarningColor('#fd7e14');                  // Laranja vibrante
        $fd5LogoutTimer->set('aviso_fundo', '#fff3e0');              // Fundo laranja claro
        $fd5LogoutTimer->set('aviso_borda', '4px solid #ff9800');    // Borda laranja
        
        // Cores do estado CRÍTICO (roxo escuro)
        $fd5LogoutTimer->setCriticalColor('#ffffff');                 // Texto branco
        $fd5LogoutTimer->set('critico_fundo', '#6f42c1');            // Fundo roxo
        $fd5LogoutTimer->set('critico_borda', '4px solid #5a32a3');  // Borda roxo escuro
        
        $fieldLogoutTimer = $fd5LogoutTimer->getAdiantiObj();

        $msg = '<div style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">';
        $msg .= '<h3 style="color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px;">📋 Configurações de Eventos - TFormDinLogoutTimer</h3>';
        
        $msg .= '<div style="background: #e8f5e8; padding: 15px; border-left: 4px solid #27ae60; margin: 10px 0;">';
        $msg .= '<h4 style="color: #27ae60; margin-top: 0;">✅ OPÇÃO 1: Configuração Padrão (Recomendada)</h4>';
        $msg .= '<code style="background: #f8f9fa; padding: 5px; border-radius: 3px; font-family: monospace; display: block; margin: 5px 0;">$timer->setEvents([\'mousedown\', \'mousemove\', \'keypress\', \'scroll\', \'touchstart\', \'click\', \'keyup\']);</code>';
        $msg .= '<p><strong>Uso:</strong> Configuração balanceada para a maioria dos casos</p>';
        $msg .= '</div>';
        
        $msg .= '<div style="background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 10px 0;">';
        $msg .= '<h4 style="color: #856404; margin-top: 0;">⚡ OPÇÃO 2: Configuração Mínima (Performance)</h4>';
        $msg .= '<code style="background: #f8f9fa; padding: 5px; border-radius: 3px; font-family: monospace; display: block; margin: 5px 0;">$timer->setEvents([\'mousedown\', \'keydown\', \'touchstart\']);</code>';
        $msg .= '<p><strong>Uso:</strong> Apenas interações essenciais - melhor performance</p>';
        $msg .= '</div>';
        
        $msg .= '<div style="background: #d1ecf1; padding: 15px; border-left: 4px solid #17a2b8; margin: 10px 0;">';
        $msg .= '<h4 style="color: #0c5460; margin-top: 0;">🔍 OPÇÃO 3: Configuração Completa (Máxima Sensibilidade)</h4>';
        $msg .= '<code style="background: #f8f9fa; padding: 5px; border-radius: 3px; font-family: monospace; display: block; margin: 5px 0;">$timer->setEvents([\'mousedown\', \'mouseup\', \'mousemove\', \'click\', \'keydown\', \'keyup\', \'touchstart\', \'touchend\', \'touchmove\', \'scroll\', \'focus\', \'input\']);</code>';
        $msg .= '<p><strong>Uso:</strong> Detecta qualquer interação mínima do usuário</p>';
        $msg .= '</div>';
        
        $msg .= '<div style="background: #f8d7da; padding: 15px; border-left: 4px solid #dc3545; margin: 10px 0;">';
        $msg .= '<h4 style="color: #721c24; margin-top: 0;">🎯 OPÇÃO 4: Configuração Personalizada</h4>';
        $msg .= '<code style="background: #f8f9fa; padding: 5px; border-radius: 3px; font-family: monospace; display: block; margin: 5px 0;">$timer->setEvents([\'mousedown\', \'click\', \'keydown\', \'keyup\']);</code>';
        $msg .= '<p><strong>Uso:</strong> Apenas mouse e teclado - ideal para aplicações desktop</p>';
        $msg .= '</div>';
        
        $msg .= '<div style="background: #e2e3e5; padding: 15px; border-left: 4px solid #6c757d; margin: 10px 0;">';
        $msg .= '<h4 style="color: #383d41; margin-top: 0;">🔧 OPÇÃO 5: Gerenciamento Individual de Eventos</h4>';
        $msg .= '<code style="background: #f8f9fa; padding: 5px; border-radius: 3px; font-family: monospace; display: block; margin: 5px 0;">';
        $msg .= '$timer->addEvent(\'resize\');&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// Redimensionamento<br>';
        $msg .= '$timer->addEvent(\'beforeunload\');&nbsp;&nbsp;// Tentativa de sair<br>';
        $msg .= '$timer->addEvent(\'input\');&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// Digitação';
        $msg .= '</code>';
        $msg .= '<p><strong>Uso:</strong> Adicionar eventos específicos conforme necessidade</p>';
        $msg .= '</div>';
        
        $msg .= '<div style="background: #fff3e0; padding: 15px; border-left: 4px solid #ff9800; margin: 10px 0;">';
        $msg .= '<h4 style="color: #e65100; margin-top: 0;">🛠️ OPÇÃO 6: Verificação e Remoção de Eventos</h4>';
        $msg .= '<code style="background: #f8f9fa; padding: 5px; border-radius: 3px; font-family: monospace; display: block; margin: 5px 0;">';
        $msg .= 'if ($timer->hasEvent(\'mousemove\')) {<br>';
        $msg .= '&nbsp;&nbsp;&nbsp;&nbsp;$timer->removeEvent(\'mousemove\');<br>';
        $msg .= '}';
        $msg .= '</code>';
        $msg .= '<p><strong>Uso:</strong> Controle dinâmico dos eventos monitorados</p>';
        $msg .= '</div>';
        
        $msg .= '<div style="background: #f0f0f0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; margin: 15px 0;">';
        $msg .= '<h4 style="color: #333; margin-top: 0;">📱 Eventos JavaScript Disponíveis:</h4>';
        $msg .= '<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;">';
        $msg .= '<div><strong>🖱️ Mouse:</strong><br><small>mousedown, mouseup, mousemove, click, dblclick, contextmenu, wheel</small></div>';
        $msg .= '<div><strong>⌨️ Teclado:</strong><br><small>keydown, keyup, keypress</small></div>';
        $msg .= '<div><strong>📱 Toque:</strong><br><small>touchstart, touchend, touchmove, touchcancel</small></div>';
        $msg .= '<div><strong>🎯 Foco:</strong><br><small>focus, blur, focusin, focusout</small></div>';
        $msg .= '<div><strong>🌐 Navegação:</strong><br><small>scroll, resize, beforeunload, pagehide, pageshow</small></div>';
        $msg .= '<div><strong>📝 Formulário:</strong><br><small>input, change, select, submit</small></div>';
        $msg .= '</div>';
        $msg .= '</div>';
        
        $msg .= '<div style="background: #e8f4fd; padding: 15px; border: 1px solid #bee5eb; border-radius: 5px; margin: 15px 0;">';
        $msg .= '<h4 style="color: #0c5460; margin-top: 0;">🎨 Personalização de Cores (3 Níveis Visuais)</h4>';
        $msg .= '<p><strong>Este exemplo usa cores personalizadas:</strong></p>';
        
        $msg .= '<div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin: 10px 0;">';
        
        $msg .= '<div style="background: #e3f2fd; padding: 10px; border-radius: 5px; border-left: 4px solid #007bff;">';
        $msg .= '<h5 style="color: #007bff; margin-top: 0;">🟦 NORMAL (Azul)</h5>';
        $msg .= '<code style="font-size: 0.8em; display: block; margin: 2px 0;">setNormalColor(\'#007bff\')</code>';
        $msg .= '<small>Tempo normal de atividade</small>';
        $msg .= '</div>';
        
        $msg .= '<div style="background: #fff3e0; padding: 10px; border-radius: 5px; border-left: 4px solid #fd7e14;">';
        $msg .= '<h5 style="color: #fd7e14; margin-top: 0;">🟠 AVISO (Laranja)</h5>';
        $msg .= '<code style="font-size: 0.8em; display: block; margin: 2px 0;">setWarningColor(\'#fd7e14\')</code>';
        $msg .= '<small>Tempo de aviso (50% restante)</small>';
        $msg .= '</div>';
        
        $msg .= '<div style="background: #f3e5f5; padding: 10px; border-radius: 5px; border-left: 4px solid #6f42c1;">';
        $msg .= '<h5 style="color: #6f42c1; margin-top: 0;">🟣 CRÍTICO (Roxo)</h5>';
        $msg .= '<code style="font-size: 0.8em; display: block; margin: 2px 0;">setCriticalColor(\'#ffffff\')</code>';
        $msg .= '<small>Tempo crítico (15% restante)</small>';
        $msg .= '</div>';
        
        $msg .= '</div>';
        
        $msg .= '<h5 style="color: #0c5460;">🔧 Métodos de Personalização:</h5>';
        $msg .= '<code style="background: #f8f9fa; padding: 10px; border-radius: 3px; font-family: monospace; display: block; margin: 5px 0; font-size: 0.9em;">';
        $msg .= '// Estado NORMAL (Azul suave)<br>';
        $msg .= '$timer->setNormalColor(\'#007bff\');<br>';
        $msg .= '$timer->set(\'normal_fonte_size\', \'1.8em\');<br><br>';
        
        $msg .= '// Estado AVISO (Laranja vibrante)<br>';
        $msg .= '$timer->setWarningColor(\'#fd7e14\');<br>';
        $msg .= '$timer->set(\'aviso_fundo\', \'#fff3e0\');<br>';
        $msg .= '$timer->set(\'aviso_borda\', \'4px solid #ff9800\');<br><br>';
        
        $msg .= '// Estado CRÍTICO (Roxo escuro)<br>';
        $msg .= '$timer->setCriticalColor(\'#ffffff\');<br>';
        $msg .= '$timer->set(\'critico_fundo\', \'#6f42c1\');<br>';
        $msg .= '$timer->set(\'critico_borda\', \'4px solid #5a32a3\');';
        $msg .= '</code>';
        
        $msg .= '<p><small><strong>💡 Dica:</strong> Use cores contrastantes para melhor visibilidade. O estado crítico deve chamar mais atenção!</small></p>';
        $msg .= '</div>';

        
        $msg .= '<div style="background: #d4edda; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px; margin: 15px 0;">';
        $msg .= '<h4 style="color: #155724; margin-top: 0;">💡 Dicas de Uso:</h4>';
        $msg .= '<ul style="margin: 0; padding-left: 20px;">';
        $msg .= '<li><strong>Timeout:</strong> Configure o tempo em segundos no construtor (ex: 60, 120, 300)</li>';
        $msg .= '<li><strong>Cores:</strong> Personalize os 3 níveis visuais (Normal, Aviso, Crítico)</li>';
        $msg .= '<li><strong>Desenvolvimento:</strong> Use debug = true para monitorar eventos</li>';
        $msg .= '<li><strong>Produção:</strong> Use debug = false ou omita o parâmetro</li>';
        $msg .= '<li><strong>Performance:</strong> Menos eventos = melhor performance</li>';
        $msg .= '<li><strong>Mobile:</strong> Inclua eventos de toque (touchstart, touchend)</li>';
        $msg .= '<li><strong>Desktop:</strong> Foque em mouse e teclado</li>';
        $msg .= '<li><strong>Acessibilidade:</strong> Use cores com bom contraste para melhor visibilidade</li>';
        $msg .= '</ul>';
        $msg .= '</div>';
        
        $msg .= '</div>';
        $html = new TFormDinHtmlField('html1', $msg, null, 'Documentação:', null, 200,true);
        $html->setClass('notice');

        $this->form->addFields( [ new TLabel('Logout Timer (Cores Personalizadas)') ],   [ $fieldLogoutTimer ] );
        $this->form->addFields( [ new TLabel('📚 Documentação Completa') ],   [ $html->getAdiantiObj() ] );

        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->form);
        parent::add($vbox);
    }

    /**
     * Clear filters
     */
    public function onClear()
    {
        $this->clearFilters();
        $this->onReload();
    }
}