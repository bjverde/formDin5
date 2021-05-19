<?php

use Adianti\Widget\Form\TLabel;

class exe_campo_cpf_cnpj extends TPage
{
    private $html;
    
    /**
     * Class constructor
     * Creates the page
     */
    function __construct()
    {
        parent::__construct();
        
        $frm = new TFormDin($this,'Exemplo Campo CPF/CNPJ');
        //$frm->setMaximize(true);
        //$frm->setHelpOnLine('Titulo', $strHeight, $strWidth, 'ajuda', null);
        
        //$frm->addCpfCnpjField('cpf_cnpj1','CPF/CNPJ:',false)->setExampleText('Mensagem Padrão / Limpar se inconpleto');

        //$frm->addCpfCnpjField('cpf_cnpj2', 'CPF/CNPJ:', false, null, true, null, null, null, null, 'myCb')->setExampleText('Tratamento no callback');
        /**/
        //$frm->addCpfCnpjField('cpf_cnpj3', 'CPF/CNPJ:', false, null, true, null, null, 'CPF/CNPJ Inválido', false)->setExampleText('Mensagem Customizada / Limpar se inconpleto');
        //$frm->addCpfCnpjField('cpf_cnpj4', 'CPF/CNPJ:', false, null, true, null, null, 'CPF/CNPJ Inválido', true)->setExampleText('Mensagem Customizada / Não limpar se inconpleto');
        $frm->addCpfField('cpf', 'CPF:', false);
        //$frm->addCnpjField('cnpj', 'CNPJ:', false);

    
        $this->form = $frm->show();


        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data'));


        // creates the page structure using a table
        $formDinBreadCrumb = new TFormDinBreadCrumb(__CLASS__);
        $vbox = $formDinBreadCrumb->getAdiantiObj();
        $vbox->add($this->form);
        
        // add the table inside the page
        parent::add($vbox);
    }
}
