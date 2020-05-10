<?php

use Adianti\Registry\TSession;

class exe_TButton extends TPage
{
    protected $form; // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    
    // trait com onReload, onSearch, onDelete...
    use Adianti\Base\AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();

        $frm = new TFormDin('Exemplo Mensagem apenas PHP');
        $this->form = $frm->show();
        
        $entry     = new TEntry('entry');
        $this->form->addFields( [ new TLabel('TEntry') ],   [ $entry ]);
        
        $bt3c = new TButton('bt3c');
        $bt3c->setLabel('Success');
        $bt3c->class = 'btn btn-success btn-lg';
       

        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data'));

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
    public function clear()
    {
        $this->clearFilters();
        $this->onReload();
    }

    public function onSave($param)
    {
        $data = $this->form->getData();
        $this->form->setData($data);

        //Função do FormDin para Debug
        FormDinHelper::d($param,'$param');
        FormDinHelper::debug($data,'$data');
        FormDinHelper::debug($_REQUEST,'$_REQUEST');
    }

}