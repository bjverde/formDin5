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

        $objhtml  = new TElement('div');
        $html = '<a href="http://www.auere.com.br/blog/html/usando-a-camera-do-celular-como-leitor-de-codigo-de-barras-numa-aplicao-web">Link do artigo inicial</a>';
        $html = $html.'<br><a href="http://www.auere.com.br/testes/barcode.php">Link do exemplo do artigo</a>';
        $html = $html.'<hr>';
        $html = $html.'<br>o campo <b>Código de barra</b> deveria funcionar! Porém parece que tem uma falha no Zxing';
        $html = $html.'<br>';
        $html = $html.'<br>use o link do campo <b>POG</b> esse link é uma copia do arquivo do exemplo. O ZXing vai enciar o codigo lido para o arquivo pogbarcode.php na raiz o sistema. Que fará um redirect para esse form no campo <b>Código de barra</b>';
        $html = $html.'<hr>';
        $objhtml->add($html);
        $this->form->addFields( [$objhtml]);

        $objLabel = new TLabel('Código de barra');
        $objText  = new TEntry('barcode');
        $currentUrl = $this->getCurrentUrl();
        $objLink  = new TElement('div');
        $objLink->add('<a href="http://zxing.appspot.com/scan?ret='.$currentUrl.'index.php?class=exe_bar_code&barcode={CODE}">Abrir Barcode Scanner ZXing</a>');

        $this->form->addFields( [$objLabel],[$objText],[$objLink]);

        $objLabel = new TLabel('POG');
        $objText  = new TEntry('cod');
        $currentUrl = $this->getCurrentUrl();
        $objLink  = new TElement('div');
        $objLink->add('<a href="http://zxing.appspot.com/scan?ret='.$currentUrl.'pogbarcode.php?codigo={CODE}">Leitor POG</a>');

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
        $url = explode('engine.php', $pageURL);
        return $url[0];
    }

    public function onReload()
    {
       var_dump($_REQUEST);
       $code = null;
       //Evita Notices
       if ( is_array($_REQUEST) && array_key_exists('barcode', $_REQUEST) ) {
           $code = $_REQUEST['barcode'];
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