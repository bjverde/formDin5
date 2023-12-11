<?php

use Adianti\Registry\TSession;

class exe_grid20 extends TPage
{
    protected $form; //Registration form Adianti
    protected $frm;  //Registration component FormDin 5
    protected $datagrid; // listing
    protected $pageNavigation;
    
    // trait com onSave, onClear, onEdit...
    use Adianti\Base\AdiantiStandardFormTrait;
    // trait com onReload, onSearch, onDelete...
    use Adianti\Base\AdiantiStandardListTrait;
    
    public function __construct()
    {
        parent::__construct();

        //$this->setDatabase('dbapoio');         // defines the database
        //$this->setActiveRecord('tb_paginacao');// defines the active record

        $frm = new TFormDin($this,'Exemplo Grid Simples 20 - setando os dados e Formatando');
        $frm->addHiddenField('id'); //POG para evitar problema de noticie

        $msg = null;
        $msg = $msg.'<br>Para visualizar esse exemplo redimencione a tela';
        $msg = $msg.'<ul>';
        $msg = $msg.'  <li>name - coluna name está oculta</li>';
        $msg = $msg.'  <li>addColumnFormatCpfCnpj - coluna formatando CPF ou CNPJ</li>';
        $msg = $msg.'  <li>addColumnFormatDate - coluna formatando data</li>';
        $msg = $msg.'  <li>setTransformer metodo para facilitar a formatação</li>';
        $msg = $msg.'</ul>';        
        $frm->addHtmlField('dica1',$msg, null, 'Dica:', null, 200);

        $this->form = $frm->show();

        $mixData = mockBanco::getExemploAdianti();
        $grid = new TFormDinGrid($this,'grid','Exemplo Grid Simples 20 - setando os dados');
        $grid->setData($mixData);
        //$grid->setHeight(2500);
        $grid->addColumn('code',  'Code', null, 'center');
        $grid->addColumn('name',  'Name', null, 'left',false,true,false);
        $grid->addColumnFormatCpfCnpj('cpf',   'CPF');
        $grid->addColumn('city',  'City', null, 'left');
        $grid->addColumn('state','State', null, 'left');
        $grid->addColumnFormatDate('date' ,'Data Brasil' , null, 'left',false,true,true,400);
        $grid->addColumn('numero1' ,'Número 1' , null, 'left',false,true,true,600);
        $grid->addColumn('numero2' ,'Número 2' , null, 'left',false,true,true,800);
        $grid->addColumn('sim1' ,'Sim 1' , null, 'left',false,true,true,1000);
        $grid->addColumn('sim2' ,'Sim 2' , null, 'left',false,true,true,1200);
        $grid->enableDefaultButtons(false);
        $this->datagrid = $grid->show();
        $panel = $grid->getPanelGroupGrid();


        //$this->form->setData( TSession::getValue(__CLASS__.'_filter_data'));
        // creates the page structure using a table
        $formDinBreadCrumb = new TFormDinBreadCrumb(__CLASS__);
        $vbox = $formDinBreadCrumb->getAdiantiObj();
        $vbox->add($this->form);
        $vbox->add($panel);
        
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

        /**
     * shows the page
     */
    function show()
    {
        $this->onReload();
        parent::show();
    }
}