<?php

class exe_link_whatsapp_api extends TPage
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
        $this->form->setFormTitle('Link API WhatsApp');

        $linkVerde = HtmlHelper::linkApiWhatsApp('(61) 91234-5678','Olá Vamos conversar');
        $linkNormal = HtmlHelper::linkApiWhatsApp('(61) 91234-5678','Olá Vamos conversar',false);
        $objhtml  = new TElement('div');
        $html = 'Link verde '.$linkVerde;
        $html = $html.'<br>Link normal '.$linkNormal;
        $html = $html.'<hr>';
        $objhtml->add($html);
        $this->form->addFields( [$objhtml]);
        
        $this->form->addAction(_t('Save'), new TAction(array($this, 'onSave')), 'far:check-circle green');
        $this->form->addActionLink(_t('Clear'),  new TAction([$this, 'onClear']), 'fa:eraser red');


        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->style = 'width:100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->form);
        parent::add($vbox);
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