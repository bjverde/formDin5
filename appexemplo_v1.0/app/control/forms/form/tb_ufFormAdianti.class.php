<?php

class tb_ufFormAdianti extends TPage
{

    protected $form; //Registration form Adianti
    protected $frm;  //Registration component FormDin 5
    protected $datagrid; //Listing
    protected $pageNavigation;

    //private static $database = 'dbapoio';
    //private static $activeRecord = 'tb_uf';
    private static $primaryKey = 'cod_uf';
    private static $formName = 'tb_ufFormAdianti';    

    // trait com onReload, onSearch, onDelete, onClear, onEdit, show
    use Adianti\Base\AdiantiStandardFormTrait;
    // trait com onReload, onSearch, onDelete...
    use Adianti\Base\AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();
        // $this->adianti_target_container = 'adianti_right_panel';
        if(!empty($param['target_container'])){
            $this->adianti_target_container = $param['target_container'];
        }
        $this->setDatabase('dbapoio'); // define the database
        $this->setActiveRecord('tb_uf'); // define the Active Record
        $this->setDefaultOrder('cod_uf', 'asc'); // define the default order

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);

        // define the form title
        $this->form->setFormTitle("listTipoDocumental");
        $this->limit = 20;

        $id = new TEntry('cod_uf');
        $descricao = new TEntry('nom_uf');
        $estado = new TEntry('cod_regiao');

        $id->addValidation("id", new TRequiredValidator()); 
        $descricao->addValidation("Descrição", new TRequiredValidator()); 
        $estado->addValidation("Estado", new TRequiredValidator()); 

        $descricao->setMaxLength(80);
        $id->setSize(100);
        $estado->setSize('100%');
        $descricao->setSize('100%');

        $row1 = $this->form->addFields([new TLabel("Id:", '#F70000', '14px', null)],[$id],[new TLabel("Descrição:", '#FF0000', '14px', null)],[$descricao]);
        $row2 = $this->form->addFields([new TLabel("Estado:", '#FF0000', '14px', null)],[$estado],[],[]);

        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        $btn_onsearch = $this->form->addAction("Buscar", new TAction([$this, 'onSearch']), 'fas:search #ffffff');
        $this->btn_onsearch = $btn_onsearch;
        $btn_onsearch->addStyleClass('btn-primary'); 

        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->disableHtmlConversion();

        $this->datagrid_form = new TForm('datagrid_'.self::$formName);
        $this->datagrid_form->onsubmit = 'return false';

        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->filter_criteria = new TCriteria;

        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(250);

        $column_id = new TDataGridColumn('cod_uf', "Id", 'center' , '70px');
        $column_descricao = new TDataGridColumn('nom_uf', "Descricao", 'left');
        $column_estado = new TDataGridColumn('cod_regiao', "Estado", 'left');

        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);

        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_descricao);
        $this->datagrid->addColumn($column_estado);

        $action_onDelete = new TDataGridAction(array($this, 'onDelete'));
        $action_onDelete->setUseButton(false);
        $action_onDelete->setButtonClass('btn btn-default btn-sm');
        $action_onDelete->setLabel("Excluir");
        $action_onDelete->setImage('fas:trash-alt #dd5a43');
        $action_onDelete->setField(self::$primaryKey);

        $this->datagrid->addAction($action_onDelete);

        // create the datagrid model
        $this->datagrid->createModel();

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup();
        $panel->datagrid = 'datagrid-container';
        $this->datagridPanel = $panel;
        $this->datagrid_form->add($this->datagrid);
        $panel->add($this->datagrid_form);

        $panel->getBody()->class .= ' table-responsive';

        $panel->addFooter($this->pageNavigation);

        $headerActions = new TElement('div');
        $headerActions->class = ' datagrid-header-actions ';
        $headerActions->style = 'justify-content: space-between;';

        $head_left_actions = new TElement('div');
        $head_left_actions->class = ' datagrid-header-actions-left-actions ';

        $head_right_actions = new TElement('div');
        $head_right_actions->class = ' datagrid-header-actions-left-actions ';

        $headerActions->add($head_left_actions);
        $headerActions->add($head_right_actions);

        $panel->getBody()->insert(0, $headerActions);

        $dropdown_button_exportar = new TDropDown("Exportar", 'fas:file-export #2d3436');
        $dropdown_button_exportar->setPullSide('right');
        $dropdown_button_exportar->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown_button_exportar->addPostAction( "CSV", new TAction([$this, 'onExportCsv'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-csv #00b894' );
        $dropdown_button_exportar->addPostAction( "XLS", new TAction([$this, 'onExportXls'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-excel #4CAF50' );
        $dropdown_button_exportar->addPostAction( "PDF", new TAction([$this, 'onExportPdf'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-pdf #e74c3c' );
        $dropdown_button_exportar->addPostAction( "XML", new TAction([$this, 'onExportXml'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-code #95a5a6' );

        $head_right_actions->add($dropdown_button_exportar);

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        if(empty($param['target_container']))
        {
            $container->add(TBreadCrumb::create(["Básico","listTipoDocumental"]));
        }
        $container->add($this->form);
        $container->add($panel);

        parent::add($container);

    }

    public function onShow($param = null)
    {

    }

    /**
     * method show()
     * Shows the page
     */
    public function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'],  $this->showMethods))) )
        {
            if (func_num_args() > 0)
            {
                $this->onReload( func_get_arg(0) );
            }
            else
            {
                $this->onReload();
            }
        }
        parent::show();
    }
    //--------------------------------------------------------------------------------
    /**
     * Close right panel
     */
     /*
    public function onClose()
    {
        TScript::create("Template.closeRightPanel()");
    } //END onClose
     */

    //--------------------------------------------------------------------------------
    public function onSave($param)
    {
        $data = $this->form->getData();
        //Função do FormDin para Debug
        FormDinHelper::d($param,'$param');
        FormDinHelper::debug($data,'$data');
        FormDinHelper::debug($_REQUEST,'$_REQUEST');

        try{
            $this->form->validate();
            $this->form->setData($data);
            $vo = new Tb_ufVO();
            $this->frm->setVo( $vo ,$data ,$param );
            $controller = new Tb_ufController();
            $resultado = $controller->save( $vo );
            if( is_int($resultado) && $resultado!=0 ) {
                //$text = TFormDinMessage::messageTransform($text); //Tranform Array in Msg Adianti
                $this->onReload();
                $this->frm->addMessage( _t('Record saved') );
                //$this->frm->clearFields();
            }else{
                $this->frm->addMessage($resultado);
            }
        }catch (Exception $e){
            new TMessage(TFormDinMessage::TYPE_ERROR, $e->getMessage());
        } //END TryCatch
    } //END onSave
}