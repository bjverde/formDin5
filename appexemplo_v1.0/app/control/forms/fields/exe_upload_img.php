<?php

use Adianti\Registry\TSession;

class exe_upload_img extends TPage
{
    protected $form; // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    
    // trait com onReload, onSearch, onDelete...
    use Adianti\Base\AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();

        $frm = new TFormDin($this,'Exemplo Upload Imagem');
        $frm->addHiddenField('id'); //POG para evitar problema de noticie
        $frm->addVideoStreamPhoto('selfieponto','Nova WebCam',true);

        /*
        $idField = 'selfieponto';

        $adiantiObjHiden = new THidden($idField);
        $adiantiObjHiden->setId($idField);

        $adiantiObjWebCam = new TElement('video');
        $adiantiObjWebCam->class = 'fd5Video';
        $adiantiObjWebCam->setProperty('id',$idField.'_video');
        $adiantiObjWebCam->setProperty('name',$idField.'_video');        
        $adiantiObjWebCam->add('autoplay');
        $adiantiObjWebCam->add('Your browser does not support HTML video.');

        $adiantiObjWebCamCanvas = new TElement('canvas');
        $adiantiObjWebCamCanvas->class = 'fd5WebCamCanvas';
        $adiantiObjWebCamCanvas->setProperty('id',$idField.'_videoCanvas');
        $adiantiObjWebCamCanvas->setProperty('name',$idField.'_videoCanvas');          

        $scriptJswebCam = new TElement('script');
        $scriptJswebCam->setProperty('src', 'app/lib/include/FormDin5WebCams.js?appver='.FormDinHelper::version());


        $btnStart = new TButton('btnStart');
        $btnStart->class = 'btn btn-success btn-sm';
        $btnStart->setLabel('Ligar Câmera');
        $btnStart->setImage('fa:power-off');
        $btnStart->addFunction("fd5VideoStart('".$idField."')");

        $btnChangeCamera = new TButton('btnChangeCamera');
        $btnChangeCamera->class = 'btn btn-light btn-sm';
        $btnChangeCamera->setLabel('Alterar Camera');
        $btnChangeCamera->setImage('fa:sync-alt');
        $btnChangeCamera->addFunction("fd5VideoCampiturar('".$idField."')");

        $btnScreenshot = new TButton('btnScreenshot');
        $btnScreenshot->class = 'btn btn-primary btn-sm';
        $btnScreenshot->setLabel('Capiturar Foto');
        $btnScreenshot->setImage('fa:camera');
        $btnScreenshot->addFunction("fd5VideoCampiturar('".$idField."')");

        $divButton = new TElement('div');
        $divButton->class = 'fd5DivVideoButton';
        $divButton->setProperty('id',$idField.'_videoDivButton');
        $divButton->add($btnStart);
        //$divButton->add($btnChangeCamera);
        $divButton->add($btnScreenshot);


        $divWebCam = new TElement('div');
        $divWebCam->class = 'fd5DivVideo';
        $divWebCam->setProperty('id',$idField.'_videodiv');
        $divWebCam->add($adiantiObjHiden);
        $divWebCam->add($adiantiObjWebCam);
        $divWebCam->add($adiantiObjWebCamCanvas);
        $divWebCam->add($scriptJswebCam);
        $divWebCam->add($divButton);
        */

        //$fd5VideoStream = new TFormDinVideoStreamPhoto('selfieponto','Nova WebCam',true);
        //$divWebCam = $fd5VideoStream->getAdiantiObj();      
        //$row5 = $frm->addFields([new TLabel("Nova WebCam:", '#ff0000', '14px', null, '100%')],[$divWebCam]);

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