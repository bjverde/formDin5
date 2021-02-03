<?php

use Adianti\Widget\Form\TLabel;

class exe_TFile extends TPage
{
    private $html;
    
    /**
     * Class constructor
     * Creates the page
     */
    function __construct()
    {
        parent::__construct();
        
        $frm = new TFormDin($this,'Exemplo Campo upload arquivo, POST');
        //$frm->setMaximize(true);
        //$frm->setHelpOnLine('Titulo', $strHeight, $strWidth, 'ajuda', null);
        $fileFormat = 'pdf,gif,txt,jpg,rar,zip,doc';
        $frm->addHtmlField('html1', 'Arquivo de tamanho maximo de 2MB e no formatos: '.$fileFormat, null, 'Dica:', null, 200)->setCss('border', '1px dashed blue');
        // define a largura das colunas verticais do formulario para alinhamento dos campos
        //$frm->setColumns(array(100,100));
        $frm->addFileField('anexo', 'Anexo Async:', true, $fileFormat, '100K', 40, false);
        
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
        $data = $this->form->getData();
        $this->form->setData($data);

        //Função do FormDin para Debug
        FormDinHelper::d($param,'$param');
        FormDinHelper::debug($data,'$data');
        FormDinHelper::debug($_REQUEST,'$_REQUEST');
    }    
}
