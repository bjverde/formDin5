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
        
        //echo getcwd().'<br>';
        //echo dirname(__FILE__).'<br>';
        //echo basename(__DIR__).'<br>';
        //FileHelper::move(getcwd().DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR .'imprensa.png',getcwd().DIRECTORY_SEPARATOR.'app/images/pessoas/imprensa.png');

        $imagecapture = new TImageCapture('imagecapture');
        $imagecapture->setAllowedExtensions( ['gif', 'png', 'jpg', 'jpeg'] );
        $imagecapture->setSize(300, 200);
        $imagecapture->setCropSize(300, 200);
        $imagecapture->enableFileHandling(false);

        $frm->addFields( [new TLabel('Image Capture')], [$imagecapture] );

        $idField = 'selfieponto';

        $adiantiObjWebCam = new TElement('video');
        $adiantiObjWebCam->class = 'fd5WebCam';
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


        $btPause = new TButton('btnPause');
        $btPause->class = 'btn btn-success btn-sm';
        $btPause->setLabel('Ligar');
        $btPause->setImage('fa:power-off');
        $btPause->addFunction("fd5VideoStart()");

        $btnChangeCamera = new TButton('btnChangeCamera');
        $btnChangeCamera->class = 'btn btn-light btn-sm';
        $btnChangeCamera->setLabel('Alterar Camera');
        $btnChangeCamera->setImage('fa:sync-alt');

        $btnScreenshot = new TButton('btnScreenshot');
        $btnScreenshot->class = 'btn btn-primary btn-sm';
        $btnScreenshot->setLabel('Salvar');
        $btnScreenshot->setImage('fa:camera');
        $btnScreenshot->addFunction("fd5WebCamCampiturar()");

        $divButton = new TElement('div');
        $divButton->class = 'fd5DivWebCamButton';
        $divButton->add($btPause);
        //$divButton->add($btnChangeCamera);
        $divButton->add($btnScreenshot);


        $divWebCam = new TElement('div');
        $adiantiObjWebCam->class = 'fd5DivWebCam';
        $divWebCam->add($adiantiObjWebCam);
        $divWebCam->add($adiantiObjWebCamCanvas);
        $divWebCam->add($scriptJswebCam);
        $divWebCam->add($divButton);
      
        $row5 = $frm->addFields([new TLabel("Nova WebCam:", '#ff0000', '14px', null, '100%')],[$divWebCam]);

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