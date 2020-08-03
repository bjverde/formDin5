<?php

use Adianti\Registry\TSession;

class exe_gride05_paginacao extends TPage
{
    protected $form; // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    
    // trait com onReload, onSearch, onDelete...
    use Adianti\Base\AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();

        $this->setDatabase('dbapoio');        // defines the database
        $this->setActiveRecord('tb_paginacao');       // defines the active record
        $this->setDefaultOrder('id', 'asc');  // define the default order

        $frm = new TFormDin($this,'Exemplo Paginação do Gride');
        $frm->addHiddenField('id'); //POG para evitar problema de noticie
        $frm->addHtmlField('mensagem', "<h3>Este exemplo utiliza a tabela tb_paginacao do banco de dados bdApoio.s3db (sqlite).</h3>");
        $this->form = $frm->show();

        $grid = new TFormDinGrid($this,'grid','Exemplo Paginação do Gride');
        //$grid->setHeight(2500);
        $grid->addColumn('id',  'id', null, 'center');
        $grid->addColumn('descricao',  'Descrição', null, 'left');

        $this->datagrid = $grid->show();
        $this->datagrid = $grid->getPageNavigation();
        $panelGroupGrid = $grid->getPanelGroupGrid();


        //$this->form->setData( TSession::getValue(__CLASS__.'_filter_data'));
        // creates the page structure using a table
        $formDinBreadCrumb = new TFormDinBreadCrumb(__CLASS__);
        $vbox = $formDinBreadCrumb->getAdiantiObj();
        $vbox->add($this->form);
        $vbox->add($panelGroupGrid);
        
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