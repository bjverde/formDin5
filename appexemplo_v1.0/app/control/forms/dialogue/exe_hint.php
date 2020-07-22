<?php

use Adianti\Registry\TSession;

class exe_hint extends TPage
{
    protected $form; // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    
    // trait com onReload, onSearch, onDelete...
    use Adianti\Base\AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();

        $frm = new TFormDin($this,'Exemplo Campo Memo');
        $frm->addMemoField('campo_memo_simples', 'Descrição:', 1000, true, 80, 5);
        $frm->addMemoField('memo2', 'Descrição:', 400, false, 120, 30);
        $frm->addMemoField('memo3', 'Memo3:', 400, false,'30%', '10%')->setPlaceHolder('Texto de exemplo 30% largura com 10% de altura');
        $frm->addMemoField('memo4', 'Memo4:', 400, false,'30%', '10%')->setReadOnly(true);
        $frm->addMemoField('memo5', 'Memo5:', 400, false,'30%', '10%',null,true,null,'Texto já preenchido');
        
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

        //-------------------------------
        $idField = 'text02';
        $strLabel = 'Texto 02';
        $maxlength = 5;
        
        $fieldTxt02 = new TText($idField);
        $fieldTxt02->maxlength =$maxlength;
        $fieldTxt02->setId($idField);
        $fieldTxt02->setProperty('onkeyup', 'fwCheckNumChar(this,'.$maxlength.');');
        $fieldTxt02->addValidation($strLabel, new TRequiredValidator);
        
        $chars02  = new TElement('span');
        $chars02->setProperty('id',$idField.'_counter');
        $chars02->setProperty('name',$idField.'_counter');
        $chars02->add('caracteres: 0 / '.$maxlength);
        
        $script02 = new TElement('script');
        $script02->setProperty('src', 'app/lib/include/FormDin5.js');
        
        $div02    = new TElement('div');
        $div02->add($fieldTxt02);
        $div02->add('<br>');
        $div02->add($chars02);
        $div02->add($script02);

        $this->form->addFields([new TLabel($strLabel,'red')], [$div02]);
        //------------------------


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