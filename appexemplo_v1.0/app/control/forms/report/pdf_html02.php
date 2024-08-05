<?php

use Adianti\Registry\TSession;

class pdf_html02 extends TPage
{
    protected $form; // registration form
    
    // trait com onReload, onSearch, onDelete...
    use Adianti\Base\AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();
        
        // creates form
        $this->form = new BootstrapFormBuilder('form');
        $this->form->setFormTitle('Report 02 - PDF e HTML com Grid');

        $this->form->addFields( [new TLabel('Customer')], [new TEntry('Customer')]);
        $this->form->addAction('Check status', new TAction(array($this, 'onCheckStatus')), 'far:check-circle green');
        $this->form->addAction('Export to PDF', new TAction(array($this, 'onExportPDF')), 'far:file-pdf red');
        //$this->form->addAction(_t('Save'), new TAction(array($this, 'onSave')), 'far:check-circle green');
        $this->form->addActionLink(_t('Clear'),  new TAction([$this, 'clear']), 'fa:eraser red');


        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->style = 'width:100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->form);
        parent::add($vbox);
    }


    public function onCheckStatus( $param )
    {
        try
        {
            $array_object['id'] = 10;
            $array_object['name'] = 'Paulo Deleo';
            $array_object['phone'] = '1234-5678';
            $array_object['address'] = 'Santa Rosa, Brasil';
            $array_object['data'] = '16/05/2020';
            $array_object['produto'] = 'Sistema Panzo';
            $array_object['quantidade'] = '10';
            $array_object['preco'] = '1.000,52';
            
            // load the html template
            $html = new THtmlRenderer('app/resources/cliente.html');
            $html->enableSection('main',  $array_object);

            parent::add($html);
            
            return $html;
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

    public function onExportPDF($param)
    {
        try
        {
            // process HTML
            $html = $this->onCheckStatus($param);
            
            // string with HTML contents
            $contents = $html->getContents();
            
            // converts the HTML template into PDF
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($contents);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            
            $file = 'app/output/status.pdf';
            
            // write and open file
            file_put_contents($file, $dompdf->output());
            //parent::openFile('tmp/status.pdf');
            
            $window = TWindow::create(_t('Customer Status'), 0.8, 0.8);
            $object = new TElement('object');
            $object->data  = $file;
            $object->type  = 'application/pdf';
            $object->style = "width: 100%; height:calc(100% - 10px)";
            $window->add($object);
            $window->show();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

    /**
     * Clear filters
     */
    public function clear()
    {
        $this->clearFilters();
        $this->onReload();
    }
}