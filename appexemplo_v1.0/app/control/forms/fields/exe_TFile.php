<?php

use Adianti\Widget\Form\TLabel;

class exe_campo_ajuda extends TPage
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
        
        $this->form = $frm->show();


        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data'));


        // creates the page structure using a table
        $formDinBreadCrumb = new TFormDinBreadCrumb(__CLASS__);
        $vbox = $formDinBreadCrumb->getAdiantiObj();
        $vbox->add($this->form);
        
        // add the table inside the page
        parent::add($vbox);
    }
}
