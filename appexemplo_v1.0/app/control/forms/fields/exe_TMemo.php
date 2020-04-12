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

        $this->form = new BootstrapFormBuilder;
        $this->form->setFormTitle('Exemplo Campo Memo ');
        $this->form->generateAria(); // automatic aria-label

        $listSituacaoCadastral = SituacaoCadastralEmpresa::getList();
        
        $cnpjLabel = 'CNPJ';
        $formDinCnpjField = new TFormDinCnpjField('cnpj',$cnpjLabel);
        $cnpj = $formDinCnpjField->getAdiantiObj();

        $comboMotivoSituacao  = new TCombo('motivo_situacao');
        $comboMotivoSituacao->addItems($listSituacaoCadastral);

        $razao_social = new TEntry('razao_social');
        $nome_fantasia = new TEntry('nome_fantasia');
        $uf = new TEntry('uf');
        $comboMatrizFilial  = new TCombo('matriz_filial');
        $comboMatrizFilial->addItems(TipoMatrizFilial::getList());
        $comboSituacao      = new TCombo('situacao');
        $comboSituacao->addItems(TipoEmpresaSituacao::getList());        
        

        $this->form->addFields( [new TLabel($cnpjLabel)],[$cnpj]
                               ,[new TLabel('Razão Social')],[$razao_social]
                            );
        $this->form->addFields( [new TLabel('Nome Fantasia')], [$nome_fantasia] );
        $this->form->addFields([new TLabel('Situação')], [$comboSituacao]
                              ,[new TLabel('Motivo Situação')], [$comboMotivoSituacao]
                              );        
        $this->form->addFields( [new TLabel('UF')], [$uf]
                               ,[new TLabel('Matriz')], [$comboMatrizFilial]
                              );


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