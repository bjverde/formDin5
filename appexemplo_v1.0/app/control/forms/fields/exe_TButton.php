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
        $frm->setAction('Nome Botão','onSave',$this);
        $frm->setAction('YYYY','onSave',$this,true,null,'green');
        $this->form = $frm->show();
        
        $label = new TLabel('TEntry');
        $entry     = new TEntry('entry');
        //$this->form->addFields( [ new TLabel('TEntry') ],   [ $entry ]);
        
        $action = new TAction(array($this, 'onSave'));

        $bt3c = new TButton('bt3c');
        $bt3c->setLabel('Success');
        //$bt3c->class = 'btn btn-success btn-lg';
        $bt3c->setAction($action,'Success');


        $hbox2 = new THBox;
        $hbox2->addRowSet( $label, $entry, $bt3c );
        $frame1 = new TFrame;
        $frame1->setLegend('Font awesome icons');
        $frame1->add($hbox2);

        $this->form->addFields( [ new TLabel('TEntry') ],   [ $entry ],[$bt3c]);
        //$this->form->addFields( [ $hbox2]);
       

        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );

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