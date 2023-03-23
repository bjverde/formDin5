<?php

use Adianti\Registry\TSession;

class exe_css_form02 extends TPage
{
    protected $form; // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    
    // trait com onReload, onSearch, onDelete...
    use Adianti\Base\AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();

        // load the styles
        TPage::include_css('app/resources/css_form02.css');

        $frm = new TFormDin($this,'CSS Form 02 - Exemplo de uso do CSS ');

        $html ='Esse form mostra o exemplo do uso de um css separado sobre alguns elmentos.<br>Evite usar a funão setCSS ! O melhor é utilizar setClass com o addCssFile.';
        $frm->addHtmlField('html1', $html, null, 'Dica:', null, 300)->setClass('notice');
        
        $html2 ='Font Awesome Icons !! <i class="fab fa-php"></i> <i class="fab fa-bitcoin"></i>'
                .'<br><i class="fa fa-cloud"></i><i class="fa fa-heart"></i> '
                .'<i class="fa fa-bars"></i>  <i class="fa fa-file"></i>';
                
        $frm->addHtmlField('html2', $html2, null,null, null, 300)->setClass('notice');
        
        //$frm->addDateField('data_pedido', 'Data:', false);
        //$frm->getLabel('data_pedido')->setClass('label', true);
        
        $x=$frm->addTextField('nome_comprador', 'Comprador:', 60, false, null)->setClass('textyellow', true);
        //$label = $x->getLabel();
        //$label->setClass('label', true);
        $frm->addTextField('nome_comprador1', 'Comprador:', 60, false, null)->setClass('text-center-green', true);
        //$frm->addSelectField('forma_pagamento', 'Forma Pagamento:', false, '1=Dinheiro,2=Cheque,3=Cartão');
        $frm->addTextField('QTD', 'Quantidade de Itens', 50, false);

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