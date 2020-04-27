<?php

use Adianti\Registry\TSession;

class exe_TMemo extends TPage
{
    protected $form; // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    
    // trait com onReload, onSearch, onDelete...
    use Adianti\Base\AdiantiStandardListTrait;

    function __construct()
    {
        parent::__construct();

        $frm = new TFormDin('Exemplo Campo Memo');
        $frm->addMemoField('campo_memo_simples', 'Descrição:', 1000, true, 80, 5);
        $frm->addMemoField('memo2', 'Descrição:', 400, false, 120, 30);
        $frm->addMemoField('memo3', 'Memo3:', 400, false,'30%', '10%')->setPlaceHolder('Texto de exemplo 30% largura com 10% de altura');
        $frm->addMemoField('memo4', 'Memo4:', 400, false,'30%', '10%')->setReadOnly(true);
        $frm->addMemoField('memo5', 'Memo5:', 400, false,'30%', '10%',null,null,null,'Texto já preenchido');
        
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