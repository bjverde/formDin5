<?php

use Adianti\Registry\TSession;

class exe_TextField extends TPage
{
    protected $form; // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    
    // trait com onReload, onSearch, onDelete...
    use Adianti\Base\AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();



        $frm = new TFormDin($this,'Exemplo do Campo Texto');

        $frm->addTextField('TEXT01','Texto tam 10', 10);
        $frm->addTextField('TEXT02','Texto obrigatorio', 10,true,null,'inicial',true,null,null,false);
        $frm->addTextField('TEXT03', 'Com valor inicial', 50,true,null,'inicial',false,null,null,true);
        $frm->addTextField('TEXT04', 'Place Holder', 50, null,null,null,null,null,'Place Holder');
        $frm->addTextField('TEXT05', 'Leitura 01', 50, null,null,'inicial')->setReadOnly(true);        
        $x = $frm->addTextField('TEXT06', 'Leitura 02', 50, null,null,'inicial');
        $x->setReadOnly(true);
        $frm->addTextField('TEXT07', 'Leitura 03', 50, null,null,'Label Sobre',null,null,null,true);

        // O Adianti permite a Internacionalização - A função _t('string') serve
        //para traduzir termos no sistema. Veja ApplicationTranslator escrevendo
        //primeiro em ingles e depois traduzindo
        $frm->setAction( _t('Save'), 'onSave', null, 'fa:save', 'green' );
        $frm->setActionLink( _t('Clear'), 'onClear', null, 'fa:eraser', 'red');

        $this->form = $frm->show();

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
    public function onClear()
    {
        $this->clearFilters();
        $this->onReload();
    }

    public function onSave($param)
    {
        try
        {
            $data = $this->form->getData();
            $this->form->setData($data);
            $this->form->validate();            
    
            //Função do FormDin para Debug
            FormDinHelper::d($param,'$param');
            FormDinHelper::debug($data,'$data');
            FormDinHelper::debug($_REQUEST,'$_REQUEST');

            new TMessage('info', 'Tudo OK!');
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
}