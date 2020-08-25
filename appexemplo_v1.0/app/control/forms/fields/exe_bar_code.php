<?php

class exe_bar_code extends TPage
{
    protected $form; // registration form
    
    // trait com onSave, onClear, onEdit...
    use Adianti\Base\AdiantiStandardFormTrait;
    // trait com onReload, onSearch, onDelete...
    use Adianti\Base\AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();
        
        // creates form
        $this->form = new BootstrapFormBuilder('form');
        $this->form->setFormTitle('Leitor de código de barras');


        $objLabel = new TLabel('Customer');
        $idField  = 'barcode';
        $objText  = new TEntry($idField);
        $currentUrl = $this->getCurrentUrl();
        $objLink  = new TElement('div');
        $objLink->add('<a href="http://zxing.appspot.com/scan?ret='.$currentUrl.'&'.$idField.'={CODE}">Abrir Barcode Scanner ZXing</a>');

        $this->form->addFields( [$objLabel],[$objText],[$objLink]);
        
        $this->form->addAction(_t('Save'), new TAction(array($this, 'onSave')), 'far:check-circle green');
        $this->form->addActionLink(_t('Clear'),  new TAction([$this, 'onClear']), 'fa:eraser red');


        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->style = 'width:100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->form);
        parent::add($vbox);
    }

    public function getCurrentUrl() 
    {
        $pageURL = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? "https://" : "http://";
        $pageURL = $pageURL.$_SERVER["SERVER_NAME"];
        $pageURL = $pageURL.( ( $_SERVER["SERVER_PORT"] != 80 ) ? ":".$_SERVER["SERVER_PORT"] : "") ;
        $pageURL = $pageURL.$_SERVER["REQUEST_URI"];
        $url = explode('&', $pageURL);
        return $url[0];
    }

    public function onReload()
    {
       var_dump($_REQUEST);
       $code = null;
       //Evita Notices
       if ( is_array($_REQUEST) && array_key_exists('CODE', $_REQUEST) ) {
           $code = $_REQUEST['CODE'];
       }

       // monta um objeto para enviar dados após o GET
       $data = new StdClass;
       $data->barcode = $code;
       $this->form->setData($data);
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