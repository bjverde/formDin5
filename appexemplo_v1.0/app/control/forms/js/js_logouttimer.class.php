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

        // Criando timer com debug ATIVADO para desenvolvimento
        $fd5LogoutTimer = new TFormDinLogoutTimer('logoutTimer','Logout Timer', true);
        
        // === EXEMPLOS DE CONFIGURAÇÃO DE EVENTOS ===
        
        // OPÇÃO 1: Configuração padrão (já vem assim por padrão)
        // $fd5LogoutTimer->setEvents(['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click', 'keyup']);
        
        // OPÇÃO 2: Configuração mínima (apenas interações essenciais)
        // $fd5LogoutTimer->setEvents(['mousedown', 'keydown', 'touchstart']);
        
        // OPÇÃO 3: Configuração completa (máxima sensibilidade)
        // $fd5LogoutTimer->setEvents(['mousedown', 'mouseup', 'mousemove', 'click', 'keydown', 'keyup', 'touchstart', 'touchend', 'touchmove', 'scroll', 'focus', 'input']);
        
        // OPÇÃO 4: Configuração personalizada - apenas teclado e mouse
        // $fd5LogoutTimer->setEvents(['mousedown', 'click', 'keydown', 'keyup']);
        
        // OPÇÃO 5: Adicionando eventos individualmente
        // $fd5LogoutTimer->addEvent('resize');        // Monitora redimensionamento da janela
        // $fd5LogoutTimer->addEvent('beforeunload');  // Monitora tentativa de sair da página
        // $fd5LogoutTimer->addEvent('input');         // Monitora digitação em campos
        
        // OPÇÃO 6: Verificando e removendo eventos
        // if ($fd5LogoutTimer->hasEvent('mousemove')) {
        //     $fd5LogoutTimer->removeEvent('mousemove'); // Remove monitoramento de movimento do mouse
        // }
        
        // Para produção, use: new TFormDinLogoutTimer('logoutTimer','Logout Timer', false);
        // Ou simplesmente: new TFormDinLogoutTimer('logoutTimer','Logout Timer'); // false é padrão
        
        $fieldLogoutTimer = $fd5LogoutTimer->getAdiantiObj();

        $msg = 'Botao 02 - TFormDinGeo';
        $msg = $msg.'<br>';
        $msg = $msg.'<br>Escondendo as informações de coordenada e colocando um feedback ver deu certo';
        $html = new TFormDinHtmlField('html1', $msg, null, 'Documentação:', null, 200,true);
        $html->setClass('notice');

        $this->form->addFields( [ new TLabel('Logout Timer') ],   [ $fieldLogoutTimer ] );
        $this->form->addFields( [ new TLabel('Dica') ],   [ $html->getAdiantiObj() ] );

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