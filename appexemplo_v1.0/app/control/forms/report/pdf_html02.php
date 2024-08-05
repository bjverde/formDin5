<?php

use Adianti\Registry\TSession;

class pdf_html02 extends TPage
{
    protected $form; // registration form
    private $iva = '7%';
    
    // trait com onReload, onSearch, onDelete...
    use Adianti\Base\AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();
        
        // creates form
        $this->form = new BootstrapFormBuilder('form');
        $this->form->setFormTitle('Report 02 - PDF e HTML com Grid');

        $this->form->addFields( [new TLabel('Customer')], [new TEntry('Customer')]);
        $this->form->addAction('Export to HTML', new TAction(array($this, 'onCheckStatus')), 'far:check-circle green');
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

    private function getHtmlReport(){
        $array_object = $this->getMockDados();
        // load the html template
        $html = new THtmlRenderer('app/resources/mdsoft-fatura.html');
        $html->enableSection('main',  $array_object);
        return $html;
    }

    public function onCheckStatus( $param )
    {
        try {
            $html = $this->getHtmlReport();
            parent::add($html);
            return $html;
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }

    public function onExportPDF($param)
    {
        try {
            $html = $this->getHtmlReport();
            $contents = $html->getContents(); //string with HTML contents

            $dompdf = new \Dompdf\Dompdf(); //converts the HTML template into PDF
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
        } catch (Exception $e) {
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

    private function getMockDados(){
        $mdsoft = new stdClass;
        $mdsoft->nome_empresa='João Silva';
        $mdsoft->endereco='Casa verde, na rua armarela';
        $mdsoft->cidade='azul';
        $mdsoft->telefone_contato='(61) 1234-5678';
        $mdsoft->nome_sistema='FormDin5 com Adianti';
        $mdsoft->img_logo = 'app/images/adianti.png';

        $cliente = new stdClass;
        $cliente->nome='João Silva';
        $cliente->endereco='Casa verde, na rua armarela';
        $cliente->cidade='azul';

        $listItens = array();
        $listItens = $this->getMockDadosItem($listItens,123,'produto',10,15,'10/01/2024');
        $listItens = $this->getMockDadosItem($listItens,132,'serviço',13,23,'10/01/2024');
        $listItens = $this->getMockDadosItem($listItens,213,'coisa',15,33,'10/01/2024');
        $listItens = $this->getMockDadosItem($listItens,231,'outra coisa',10,10,'10/01/2024');
        $listItens = $this->getMockDadosItem($listItens,312,'o que é isso?',1,45,'10/01/2024');
        $listItens = $this->getMockDadosItem($listItens,321,'ultimo item',10,10,'10/01/2024');

        $factura = new stdClass;
        $factura->id = 1010101;
        $factura->data_venda = '2024-08-05 20:12:00';
        $factura->operador = 'operador';

        $dados = array();
        $dados['mdsoft'] = $mdsoft;
        $dados['cliente']= $cliente;
        $dados['items']  = $listItens;
        $dados['factura']= $factura;
        
        return $dados;
    }
    private function getMockDadosItem($arrayList,$id,$nome,$quantidade,$preco_venda,$data){
        $item = array();
        $item['id_venda_item']=$id;
        $item['cod_produto']='prod'.$id;
        $item['descricao']=$nome;
        $item['quantidade']=$quantidade;
        $item['preco_venda']=$preco_venda;
        $item['disconto']=0;
        $item['iva_percen']=0;
        $item['total_item']=$quantidade*$preco_venda;
        $item['data']=$data;
        $arrayList[]=$item;
        return $arrayList;
    }
}