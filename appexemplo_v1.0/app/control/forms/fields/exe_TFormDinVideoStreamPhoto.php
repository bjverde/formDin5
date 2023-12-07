<?php

use Adianti\Registry\TSession;

class exe_TFormDinVideoStreamPhoto extends TPage
{
    protected $form; // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    
    // trait com onReload, onSearch, onDelete...
    use Adianti\Base\AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();

        $frm = new TFormDin($this,'Exemplo TFormDinVideoStreamPhoto');
        $frm->addHiddenField('id'); //POG para evitar problema de noticie

        $msg = null;
        $msg = $msg.'<br>No Adianti tem os exemplos FormImageUploader com TImageCapture que pegar da Camera';
        $msg = $msg.'<br>Porém no celular o TImageCapture permite fazer upload de imagens, porém o TFormDinVideoStreamPhoto só usar Camera';
        $msg = $msg.'<br><a href="https://framework.adianti.me//tutor/index.php?class=FormImageUploader">Adianti Tutor - FormImageUploader</a>';
        $frm->addHtmlField('dica1',$msg, null, 'Dica:', null, 200);


        $frm->addVideoStreamPhoto('webcam1','Nova WebCam',true);

        //$fd5VideoStream = new TFormDinVideoStreamPhoto('webcam2','WebCam modo Adianti',true);
        //$divWebCam = $fd5VideoStream->getAdiantiObj();      
        //$row5 = $frm->addFields([new TLabel("WebCam modo Adianti:", '#ff0000', '14px', null, '100%')],[$divWebCam]);

        $msg = null;
        $msg = $msg.'<br>Exemplo com upload de imagens';
        $msg = $msg.'<ul>';
        $msg = $msg.'  <li><a href="index.php?class=exe_fwShowBlobDisco">Exemplo Campo Select Simples</a></li>';
        $msg = $msg.'  <li><a href="index.php?class=exe_TFileMulti">Exemplo campo upload arquivo Multiplo</a></li>';
        $msg = $msg.'  <li><a href="index.php?class=exe_TFile">Exemplo campo upload arquivo</a></li>';
        $msg = $msg.'</ul>';
        $frm->addGroupField('gpx1', 'Veja outros exemplos relacionados');
        $frm->addHtmlField('aviso_relacionado',$msg, null, 'Lista de exemplos', null, 200);        

        $this->form = $frm->show();
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data'));

        // add form actions
        // O Adianti permite a Internacionalização - A função _t('string') serve
        //para traduzir termos no sistema. Veja ApplicationTranslator escrevendo
        //primeiro em ingles e depois traduzindo
        $this->form->addAction(_t('Save'), new TAction(array($this, 'onSave')), 'far:check-circle green');
        $this->form->addActionLink(_t('Clear'),  new TAction([$this, 'clear']), 'fa:eraser red');

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