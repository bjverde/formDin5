<?php

use Adianti\Registry\TSession;

class exe_GroupField01 extends TPage
{
    protected $form; // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    
    // trait com onReload, onSearch, onDelete...
    use Adianti\Base\AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();

        $frm = new TFormDin($this,'Exemplo de Grupos');
        $frm->addGroupField('gpx1', 'Teste Novo Grupo 1');
        //$frm->addGroupField('gpx1', 'Teste Novo Grupo 1')->setLabelsAlign('center');
            $frm->addTextField('nome1', 'Nome 1:', 40, true);
            $frm->addTextField('nome2', 'Nome 2:', 40, true);
            $frm->addTextField('nome3', 'Nome 3:', 40, true);
            //$frm->addButton('Gravar', null, 'btnGravar', 'alert(0)', null, true, false)->setAttribute('align', 'left');
        $frm->closeGroup();
                

        // O Adianti permite a Internacionalização - A função _t('string') serve
        //para traduzir termos no sistema. Veja ApplicationTranslator escrevendo
        //primeiro em ingles e depois traduzindo
        $frm->setAction(_t('Save'),'onSave',null,'far:check-circle','green');
        $frm->setActionLink(_t('Clear'),'clear',null,'fa:eraser','red');
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