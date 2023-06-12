<?php

use Adianti\Widget\Form\TLabel;

class exe_fwShowBlobDisco extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;

    // trait com onReload, onSearch, onDelete...
    use Adianti\Base\AdiantiStandardListTrait;

    // importa operações padrão para manipulação de arquivos
    use Adianti\Base\AdiantiFileSaveTrait;
    
    /**
     * Class constructor
     * Creates the page
     */
    function __construct()
    {
        parent::__construct();

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

        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->filter_criteria = new TCriteria;

        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);        

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
