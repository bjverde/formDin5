<?php

use Adianti\Registry\TSession;

class exe_TextField extends TPage
{
    protected $form; // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    
    // trait com onReload, onSearch, onDelete...
    use Adianti\Base\AdiantiStandardListTrait;

    function __construct()
    {
        parent::__construct();

        $formDin = new TFormDin('Exemplo do Campo Texto');
        $this->form =$formDin->getAdiantiObj();
        
        $formDinText01Label = 'Nome da pessoa sem quebra';
        $formDinText01 = new TFormDinTextField('TEXT01',$formDinText01Label);
        $text01 = $formDinText01->getAdiantiObj();

        $this->form->addFields( [new TLabel($formDinText01Label)],[$text01]);

        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data'));

        // add form actions
        $this->form->addAction('Find', new TAction([$this, 'onSearch']), 'fa:search blue');        
        $this->form->addActionLink('Clear',  new TAction([$this, 'clear']), 'fa:eraser red');

        // creates the page structure using a table
        $formDinBreadCrumb = new TFormDinBreadCrumb(__CLASS__);
        $vbox = $formDinBreadCrumb->getAdiantiObj();
        $vbox->add($this->form);
        
        // add the table inside the page
        parent::add($vbox);
    }

    /**
     * Clear filters
     */
    function clear()
    {
        $this->clearFilters();
        $this->onReload();
    }
}