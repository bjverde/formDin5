<?php

use Adianti\Registry\TSession;

class exe_TMemo extends TPage
{
    protected $form; // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    
    // trait com onReload, onSearch, onDelete...
    use Adianti\Base\AdiantiStandardListTrait;

    function __construct()
    {
        parent::__construct();

        $frm = new TFormDin('Exemplo Campo Memo');
        $frm->addMemoField('campo_memo_simples', 'Descrição:', 1000, true, 80, 5);
        $frm->addMemoField('memo2', 'Descrição:', 400, false, 120, 30);
        $frm->addMemoField('memo3', 'Memo3:', 400, false,'30%', '10%')->setPlaceHolder('Texto de exemplo 30% largura com 10% de altura');
        $frm->addMemoField('memo4', 'Memo4:', 400, false,'30%', '10%')->setReadOnly(true);
        $frm->addMemoField('memo5', 'Memo5:', 400, false,'30%', '10%',null,null,null,'Texto já preenchido');
        
        $this->form = $frm->show();

        $idField = 'text01';
        $maxlength = 10;
        $detalhesolicitacao = new TText($idField);
        $detalhesolicitacao->setId($idField);
        $detalhesolicitacao->maxlength = $maxlength;
        $chars              = new TElement('span');
        $chars->id          = $idField.'_counter';
        $script             = new TElement('script');
        $script->type = 'text/javascript';
        $script->add("var maxLength = ".$maxlength.";
                        $('#".$idField."').keyup(function() {
                          var length = $(this).val().length;
                          var length = maxLength-length;
                          $('#".$idField."_counter').text(length+' Caracteres Restantes');
                        });");
        $container = new TVBox;
        $container->add($detalhesolicitacao);
        $container->add($chars);
        $container->add($script);

        $this->form->addFields([new TLabel('Texto 01')], [$container]);

        $idField = 'det02';
        $maxlength = 5;
        $det02    = new TText($idField);
        $det02->maxlength =$maxlength;
        $det02->setId($idField);
        $det02->setProperty('onkeyup', 'fwCheckNumChar(this,'.$maxlength.');');
        $chars02  = new TElement('span');
        $chars02->setProperty('id',$idField.'_counter');
        $chars02->setProperty('name',$idField.'_counter');
        $chars02->add('caracteres: 0 / '.$maxlength);
        $script02 = new TElement('script');
        $script02->setProperty('src', 'app/lib/include/FormDin5.js');
        $div02    = new TElement('div');
        $div02->add($det02);
        $div02->add('<br>');
        $div02->add($chars02);
        $div02->add($script02);

        $this->form->addFields([new TLabel('xxxx22')], [$div02]);


        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data'));

        // add form actions
        $this->form->addAction('Find', new TAction([$this, 'onSearch']), 'fa:search blue');        
        $this->form->addActionLink('Clear',  new TAction([$this, 'clear']), 'fa:eraser red');

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
    function clear()
    {
        $this->clearFilters();
        $this->onReload();
    }
}