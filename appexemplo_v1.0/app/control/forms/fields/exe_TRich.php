<?php

use Adianti\Registry\TSession;

class exe_TRich extends TPage
{
    protected $form; // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    
    // trait com onReload, onSearch, onDelete...
    use Adianti\Base\AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();

        $frm = new TFormDin($this,'Exemplo Campo Rich');
        $frm->addHiddenField('id'); //POG para evitar problema de noticie
        $frm->addRichField('rich1NaoObri', 'Não Obrigatório:', 365, false,'100%');
        $frm->addRichField('rich1Obri', 'Obrigatório:', 365, true,'100%');
        $frm->addRichField('rich2', 'Rich2:', 50, false,'100%', '10%')->setPlaceHolder('Texto de exemplo 100% largura com 10% de altura');
        $rich3 = $frm->addRichField('rich3', 'Rich3:', 50, false,'100%', '10%');
        $rich3->setReadOnly(true);
        $rich3->setPlaceHolder('Campo somente leitura com Place Holder. Texto de exemplo 100% largura com 10% de altura');
        $frm->addRichField('rich4', 'Rich4:', 400, false, 120, 30);
        $frm->addRichField('rich5', 'Rich5:', 400, false,'80%', '15%');
        
        
        // O Adianti permite a Internacionalização - A função _t('string') serve
        //para traduzir termos no sistema. Veja ApplicationTranslator escrevendo
        //primeiro em ingles e depois traduzindo
        $frm->setAction( _t('Save'), 'onSave', null, 'fa:save', 'green' );
        $frm->setActionLink( _t('Clear'), 'onClear', null, 'fa:eraser', 'red');

        $this->form = $frm->show();

        $idField = 'text01';
        $maxlength = 10;
        $detalhesolicitacao = new TText($idField);
        $detalhesolicitacao->setId($idField);

        $this->form->addFields([new TLabel('Texto 01')], [$detalhesolicitacao]);

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
    public function onClear()
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