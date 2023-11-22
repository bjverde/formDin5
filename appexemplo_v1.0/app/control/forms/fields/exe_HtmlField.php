<?php

use Adianti\Registry\TSession;

class exe_HtmlField extends TPage
{
    protected $form; // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    
    // trait com onReload, onSearch, onDelete...
    use Adianti\Base\AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();

        $frm = new TFormDin($this,'Exemplo do Campo HTML');
        $frm->addHiddenField('id'); //POG para evitar problema de noticie
        $frm->addHtmlField('html1', '<b>O campo html é um campo livre</b>. Você poderá adicionar qualquer conteúdo na página.', null, 'Dica:', null, 200);
        $frm->addHtmlField('html2', null, 'ajuda/texto.txt', 'Arquivo: texto.txt:', 200, 650);
        $frm->addHtmlField('html3', 'Esta campo html não possui largura definda, portando se ajustará à largura do form.', null, null, 100);
        $frm->addHtmlField('html4', 'Esta campo html não possui largura definda, portando se ajustará à largura do form.', null, null, 100,null,false);

        $frm->addGroupField('gpx1', 'ATENÇÃO');
            $frm->addButton('Vai para "Exemplo Text Diplay"',null,['exe_textdisplay','onReload'],null,null,true,false);
            $frm->addHtmlField('html5', 'Existem dois tipos de campos muito pareciddos addHtmlField e addTextDisplay. O Segundo tem algumas facilidades.', null, null, 100);
        $frm->closeGroup();

        // O Adianti permite a Internacionalização - A função _t('string') serve
        //para traduzir termos no sistema. Veja ApplicationTranslator escrevendo
        //primeiro em ingles e depois traduzindo
        $frm->setAction( _t('Save'), 'onSave', null, 'fa:save', 'green' );
        $frm->setActionLink( _t('Clear'), 'onClear', null, 'fa:eraser', 'red');

        $this->form = $frm->show();


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