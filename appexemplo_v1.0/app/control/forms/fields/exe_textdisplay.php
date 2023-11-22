<?php

use Adianti\Registry\TSession;

class exe_textdisplay extends TPage
{
    protected $form; // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    
    // trait com onReload, onSearch, onDelete...
    use Adianti\Base\AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();

        $frm = new TFormDin($this,'Exemplo Text Diplay');
        $frm->addHiddenField('id'); //POG para evitar problema de noticie

        $msg = 'Text Diplay é não é um campo de form, é apenas um conjuntos de label e texto html
                <br> os campos formatados facilitam a vida, texto é informado sem formatação o textDisplay resolve';
        $frm->addHtmlField('html1',$msg, null, 'Dica:', null, 200);
        $frm->addTextDisplay('td1','Geral','Mostra um texto simples');
        $frm->addTextDisplayCpfCpnj('td2','CPF','12345678909');
        $frm->addTextDisplayCpfCpnj('td3','CNPJ','36806647000152');
        $frm->addTextDisplayDataTimeBr('td4','Data iso para BR','2022-07-18');
        $frm->addTextDisplayNumeroBrasil('td5','Número em formato BR','123456,78');

        $frm->addGroupField('gpx1', 'ATENÇÃO');
            $frm->addButton('Vai para "Exemplo HTML"',null,['exe_HtmlField','onReload'],null,null,true,false);
            $frm->addHtmlField('html5', 'Existem dois tipos de campos muito pareciddos addHtmlField e addTextDisplay. O Primeiro é livre.', null, null, 100);
        $frm->closeGroup();        

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