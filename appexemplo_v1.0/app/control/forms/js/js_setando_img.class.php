<?php

use Adianti\Registry\TSession;

class js_setando_img extends TPage
{
    protected $form; // registration form
    private static $formName = 'js_setando_imgForm';
    
    // trait com onReload, onSearch, onDelete...
    use Adianti\Base\AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();
        
        // create the form
        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle( 'JS Exemplo - setando Img' );
        
        $options = ['adianti.png'=>'Adianti'
                  , 'builder.png' => 'Builder'
                  , 'template3.png' => 'template3'
                  , 'template4.png' => 'template4'];
        $combo     = new TCombo('combo');
        $combo->addItems($options);
        $combo->setChangeAction(new TAction([$this,'setImg']));

        $entry     = new TEntry('entry');

        $image  = new TFile('image');
        $image->setAllowedExtensions( ['gif', 'png', 'jpg', 'jpeg'] );
        $image->enableFileHandling();
        $image->enableImageGallery();
        //$image->setValue('app/images/adianti.png');

        /*
        $image = new TImageCapture('image');            
        $image->setAllowedExtensions( ['gif', 'png', 'jpg', 'jpeg'] );
        $image->setSize(300, 200);
        $image->setCropSize(300, 200);
        $image->setValue('app/images/adianti.png');
        */

        $this->form->addFields( [ new TLabel('Combo') ],   [ $combo ] );
        $this->form->addFields( [ new TLabel('Entry') ],   [ $entry ] );
        $this->form->addFields( [ new TLabel('Img') ],   [ $image ] );

        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->form);
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

    public static function setImg($param)
    {
        try {
            //var_dump($param);
            $combo = $param['combo'];
            $caminho = 'app/images/'.$combo;
            $obj = new StdClass;
            $obj->entry = $caminho;
            $obj->image = $caminho;
            var_dump($obj);
            TForm::sendData(self::$formName, $obj);
        }
        catch (Exception $e){
            new TMessage('error', $e->getMessage());    
        }
    }

}