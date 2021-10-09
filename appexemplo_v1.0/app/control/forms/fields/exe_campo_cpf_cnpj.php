<?php

use Adianti\Registry\TSession;

class exe_campo_cpf_cnpj extends TPage
{
    protected $form; // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    
    // trait com onReload, onSearch, onDelete...
    use Adianti\Base\AdiantiStandardListTrait;

    public function __construct()
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
        $frm->addGroupField('gpx1', 'Basico');
            $frm->addCpfField('cpf1', 'CPF 1:', false);
            $frm->addCnpjField('cnpj1', 'CNPJ 1:', false);
        $frm->closeGroup();

        $frm->addGroupField('gpx2', 'Valor inicial do campo');
            $frm->addCpfField('cpf2', 'CPF 2:', false,'51263679005');
            $frm->addCnpjField('cnpj2', 'CNPJ 2:', false,'62781227000179');
        $frm->closeGroup();

        $frm->addGroupField('gpx3', 'Não valida!');
            $frm->addCpfField('cpf3', 'CPF 3:', false,'111111111111',null,null,null,null,false);
            $frm->addCnpjField('cnpj3', 'CNPJ 3:', false,'62781227000100',null,null,null,null,false);
        $frm->closeGroup();

        $frm->addGroupField('gpx4', 'Envia mascará para o banco');
            $frm->addCpfField('cpf4', 'CPF 4:', false,'51263679005',null,null,null,null,null,null,null,true);
            $frm->addCnpjField('cnpj4', 'CNPJ 4:', false,'62781227000179',null,null,null,null,null,null,null,true);
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
