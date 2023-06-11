<?php

use Adianti\Widget\Form\TLabel;

class exe_TFileMulti extends TPage
{
    private $html;

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

        $frm = new TFormDin($this,'Exemplo campo upload arquivo Multiplo');
        //$frm->setMaximize(true);
        //$frm->setHelpOnLine('Titulo', $strHeight, $strWidth, 'ajuda', null);
        
        $fileFormat = 'gif,png,jpg,jpeg,pdf,txt,rar,zip,doc';
        $arrayFileFormat = explode(',',$fileFormat);
        $strMaxFileSize = '300k';
        $msg = 'Arquivo de tamanho maximo é '.$strMaxFileSize.' e nos formatos: '.$fileFormat;
        $msg = $msg.'<br>';
        $msg = $msg.'<br>O upload é feito de forma assincrona e vai para pasta /tmp do aplicativo. Informe um novo setService alterando do padrão AdiantiUploaderService';
        $msg = $msg.'<br>';
        $msg = $msg.'<br>PHP está configurado. post_max_size = '.ini_get('post_max_size').' e upload_max_filesize = '.ini_get('upload_max_filesize');
        
        $frm->addHtmlField('html1', $msg, null, 'Dica:', null, 200)->setClass('notice');

        $file      = new TFile('file');
        $file->setAllowedExtensions( $arrayFileFormat );

        $multifile1 = new TMultiFile('multifilead1');
        $multifile1->setAllowedExtensions( $arrayFileFormat );

        $multifile2 = new TMultiFile('multifilead2');
        $multifile2->setAllowedExtensions( $arrayFileFormat );
        $multifile2->enableImageGallery(100);
        //$multifile2->enablePopover(100,50);
        
        $frm->addContent([new TLabel('Adianti - Campos puros')] );
        $frm->addFields( [new TLabel('Upload Multiplo - básico'), $multifile1 ] );
        $frm->addFields( [new TLabel('Upload Multiplo - galeria'), $multifile2 ] );

        
        $frm->addGroupField('fd5', 'FormDin 5');
        $frm->addFileFieldMulti('multifilefd1', 'Upload Multiplo:', false, $fileFormat);


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

            //AQUI vai processar o array do campo informado

            new TMessage('info', 'Tudo OK!');
        }catch (Exception $e){
            new TMessage('error', $e->getMessage());
        }
    }    
}
