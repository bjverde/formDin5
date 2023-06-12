<?php

use Adianti\Widget\Form\TLabel;

class exe_fwShowBlobDisco extends TPage
{
    private $form; // form

    private $datagrid;        //DataGrid listing
    private $filter_criteria; //DataGrid criteria
    private $datagrid_form;   //DataGrid Form
    private $datagridPanel;
    private $pageNavigation;

    private static $primaryKey = 'id_arquivo';
    private static $formName = 'form_blobDisco';
    private $showMethods = ['onReload', 'onSearch', 'onRefresh', 'onClearFilters'];    

    // trait com onSave, onClear, onEdit...
    use Adianti\Base\AdiantiStandardFormTrait;
    // trait com onReload, onSearch, onDelete...
    use Adianti\Base\AdiantiStandardListTrait;
    // trait List com onExportCSV, onExportXLS, onExportXML, onExportPDF...
    use Adianti\Base\AdiantiStandardListExportTrait;
    // importa operações padrão para manipulação de arquivos
    use Adianti\Base\AdiantiFileSaveTrait;
    
    /**
     * Class constructor
     * Creates the page
     */
    function __construct()
    {
        parent::__construct();
        $this->filter_criteria = new TCriteria;
        $this->setLimit(20);
        $this->setDatabase('dbapoio'); // define the database
        $this->setActiveRecord('tb_arquivo'); // define the Active Record

        // load the styles
        TPage::include_css('app/resources/css_form02.css');        

        $fileFormat = 'png,jpg,txt,gif,doc,pdf,xls,odt';
        $fileSize = '2M';

        $msg = '<center><h3><b>Este exemplo mostra como salvar o endereço do arquivo no banco de dados e depois visualiza-lo.';
        $msg = $msg.'<br>Está utilizando o banco de dados bdApoio.s3db ( SQLite ) e a tabela é a tb_arquivo.</b></h3></center>';
        $msg = $msg.'Arquivo de tamanho maximo de '.$fileSize.' e nos formatos: '.$fileFormat;
        $msg = $msg.'<br>';
        $msg = $msg.'<br>PHP está configurado. post_max_size = '.ini_get('post_max_size').' e upload_max_filesize = '.ini_get('upload_max_filesize');        

        $frm = new TFormDin($this,'Cadastro e Exibição de LOBS Salvando Caminho no Banco de Dados');
        $frm->addHiddenField('id_arquivo'); // coluna chave da tabela
        $frm->addHtmlField('obs',$msg);
                
        // campo para upload do arquivo
        $file = $frm->addFileField('conteudo_arquivo', 'Anexo:', false, $fileFormat, $fileSize, 60, null, null, 'aoAnexar');
        //var_dump($file);

        // grupo para exibir as informações do arquivo selecionado
        $frm->addGroupField('gpDadosArquivo', 'Informações do Arquivo');
            $frm->addTextField('nome_arquivo', 'Nome do Arquivo:', 60, false, 60);
            $frm->addTextField('tamanho_arquivo', 'Tamanho:', 10);
            $frm->addTextField('tipo_arquivo', 'Tipo:', 60);
        $frm->closeGroup();

        // O Adianti permite a Internacionalização - A função _t('string') serve
        //para traduzir termos no sistema. Veja ApplicationTranslator escrevendo
        //primeiro em ingles e depois traduzindo
        $frm->setAction( _t('Save'), 'onSave', null, 'fa:save', 'green' );
        $frm->setActionLink( _t('Clear'), 'onClear', null, 'fa:eraser', 'red');

        $this->form = $frm->show();
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data'));

        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->disableHtmlConversion();

        $this->datagrid_form = new TForm('datagrid_'.self::$formName);
        $this->datagrid_form->onsubmit = 'return false';

        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);

        $column_id_arquivo = new TDataGridColumn('id_arquivo', "id", 'center' , '70px');
        $column_nome_arquivo = new TDataGridColumn('nome_arquivo', "Nome", 'left');

        $this->datagrid->addColumn($column_id_arquivo);
        $this->datagrid->addColumn($column_nome_arquivo);

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
        $headerActions->style = 'background-color:#fff; justify-content: space-between;';

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

        // creates the page structure using a table
        $formDinBreadCrumb = new TFormDinBreadCrumb(__CLASS__);
        $container = $formDinBreadCrumb->getAdiantiObj();
        $container->add($this->form);
        $container->add($panel);
        
        // add the table inside the page
        parent::add($container);
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
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }    
}
