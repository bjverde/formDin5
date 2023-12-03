<?php

use Adianti\Registry\TSession;

class exe_SelectFielddb extends TPage
{
    protected $form; // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    
    // trait com onSave, onClear, onEdit...
    use Adianti\Base\AdiantiStandardFormTrait;
    // trait com onReload, onSearch, onDelete...
    use Adianti\Base\AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();

        $frm = new TFormDin($this,'Select via banco');

        $frm->addSelectFieldDB('s1','Consumidor sem Pesquisa',false,false,false,null,'samples','Customer','id','name','name',null,false);

        $frm->addSelectFieldDB('s2','Consumidor com Pesquisa',false,false,false,null,'samples','Customer','id','name','name');


        $msg = null;
        $msg = $msg.'<ul>';
        $msg = $msg.'  <li><a href="index.php?class=exe_SelectField_01">Exemplo Campo Select Simples</a></li>';
        $msg = $msg.'  <li><a href="index.php?class=exe_SelectFielddb">Exemplo Campo Select via banco</a></li>';
        $msg = $msg.'  <li><a href="index.php?class=js_setando_img">Exemplo Campo Select setando valores</a></li>';
        $msg = $msg.'</ul>';
        $frm->addGroupField('gpx1', 'Veja outros exemplos relacionados');
        $frm->addHtmlField('aviso_relacionado',$msg, null, 'Lista de exemplos', null, 200);

        // O Adianti permite a Internacionalização - A função _t('string') serve
        //para traduzir termos no sistema. Veja ApplicationTranslator escrevendo
        //primeiro em ingles e depois traduzindo
        $frm->setAction( _t('Save'), 'onSave', null, 'fa:save', 'green' );
        $frm->setActionLink( _t('Clear'), 'onClear', null, 'fa:eraser', 'red');

        $this->form = $frm->show();

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