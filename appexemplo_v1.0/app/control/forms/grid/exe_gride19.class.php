<?php

use Adianti\Registry\TSession;

class exe_gride19 extends TPage
{
    protected $form; // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    
    // trait com onReload, onSearch, onDelete...
    use Adianti\Base\AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();

        $this->setDatabase('dbapoio');         // defines the database
        $this->setActiveRecord('tb_paginacao');// defines the active record
        $this->setDefaultOrder('id', 'asc');   // define the default order

        $frm = new TFormDin($this,'Exemplo Exportar Grid somente CSV');
        $frm->addHiddenField('id'); //POG para evitar problema de noticie
        $msg = '<h3>Este exemplo utiliza a tabela tb_paginacao do banco de dados app/database/bdApoio.s3db (sqlite).</h3>';
        $msg = $msg.'<br>';
        $msg = $msg.'<br>Desativando ação Edit';
        $frm->addHtmlField('mensagem', $msg);

        // O Adianti permite a Internacionalização - A função _t('string') serve
        //para traduzir termos no sistema. Veja ApplicationTranslator escrevendo
        //primeiro em ingles e depois traduzindo
        //$frm->setAction( _t('Save'), 'onSave', null, 'fa:save', 'green' );
        $frm->setActionLink( _t('Clear'), 'onClear', null, 'fa:eraser', 'red');
        
        $this->form = $frm->show();

        $grid = new TFormDinGrid($this,'grid','Gride com Paginação');
        //$grid->setHeight(2500);
        $grid->addColumn('id',  'id', null, 'center');
        $grid->addColumn('descricao',  'Descrição', null, 'left');

        $grid->setCreateDefaultEditButton(false);

        $grid->setExportPdf(false);
        $grid->setExportExcel(false);
        $grid->setExportXml(false);

        $this->datagrid = $grid->show();
        $this->pageNavigation = $grid->getPageNavigation();
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

    /**
     * Executed when the user clicks at the delete button
     * STATIC Method, does't reload the page when executed
     */
    public static function onDelete($param)
    {
        // get the parameter and shows the message
        $code = $param['id'];
        new TMessage('error', "Você tentou clicou para deletar o registro <b>{$code}</b> e não será deletado");
    }

}