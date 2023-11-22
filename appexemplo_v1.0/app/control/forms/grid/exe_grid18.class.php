<?php

use Adianti\Registry\TSession;

class exe_grid18 extends TPage
{
    protected $form; // registration form
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
        
        $mixData = mockBanco::getExemploAdianti();
        $grid = new TFormDinGrid($this,'grid','Exemplo Grid Simples 18 - setando os dados');
        $grid->setData($mixData);
        //$grid->setHeight(2500);
        $grid->addColumn('code',  'Code', null, 'center');
        $grid->addColumn('name',  'Name', null, 'left');
        $grid->addColumn('city',  'City', null, 'left');
        $grid->addColumn('state','State', null, 'left');
        $grid->addColumnFormatDate('date' ,'Data Brasil' , null, 'left');
        $numero = $grid->addColumn('numero1' ,'Número' , null, 'right');
        $numero->setTransformer( function($value, $object, $row){return TFormDinGridTransformer::gridNumeroBrasil($value, $object, $row);} );
        //$grid->addColumnFormatDate('date' ,'Data' , null, 'left','Y');
        //$grid->enableDefaultButtons(false);
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
     * Executed when the user clicks at the column title
     */
    public function onColumnAction($param)
    {
        // get the parameter and shows the message
        $key = $param['column'];
        new TMessage('info', "You clicked at the column <b>{$key}</b>");
    }

        /**
     * Executed when the user clicks at the view button
     */
    public function onView($param)
    {
        // get the parameter and shows the message
        $code = $param['code'];
        $name = $param['name'];
        new TMessage('info', "The code is: <b>$code</b> <br> The name is : <b>$name</b>");
    }
    
    /**
     * Executed when the user clicks at the delete button
     * STATIC Method, does't reload the page when executed
     */
    public static function onDelete($param)
    {
        // get the parameter and shows the message
        $code = $param['code'];
        new TMessage('error', "The register <b>{$code}</b> may not be deleted");
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