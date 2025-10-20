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
        
        // create the form
        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle( 'LogOut por tempo' );

        $fd5LogoutTimer = new TFormDinLogoutTimer('logoutTimer','Logout Timer');
        $fieldLogoutTimer = $fd5LogoutTimer->getAdiantiObj();

        $this->form->addFields( [ new TLabel('Logout Timer') ],   [ $fieldLogoutTimer ] );

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