<?php

use Adianti\Registry\TSession;

class exe_maskField extends TPage
{
    protected $form; // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    
    // trait com onReload, onSearch, onDelete...
    use Adianti\Base\AdiantiStandardListTrait;

    function __construct()
    {
        parent::__construct();

        $frm = new TFormDin('Exemplo de Entrada de Dados com Máscara');

        $frm->addMaskField('c1', 'Código:', false, '99.99.99', null, null, null, null, '99.99.99');
        $frm->addMaskField('c2', 'Placa do Carro:', false, 'aaa-9999')->setExampleText('aaa-9999');
        $frm->addMaskField('c3', 'Código de Barras:', false, '9 999999 999999')->setExampleText('9 999999 999999');

        $this->form = $frm->show();

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