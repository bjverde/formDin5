<?php
use Adianti\Registry\TSession;

class exe_grid21 extends TPage
{
    private $datagrid;
    
    public function __construct()
    {
        parent::__construct();
        
        // creates one datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->enablePopover('Details', '<b>Code:</b> {code} <br> <b>Name:</b> {name} <br> <b>City:</b> {city} <br> <b>State:</b> {state}');
        
        // create the datagrid columns
        $code       = new TDataGridColumn('code',    'Code',    'center', '10%');
        $name       = new TDataGridColumn('name',    'Name',    'left',   '30%');
        $city       = new TDataGridColumn('city',    'City',    'left',   '30%');
        $state      = new TDataGridColumn('state',   'State',   'left',   '30%');
        
        // add the columns to the datagrid, with actions on column titles, passing parameters
        $this->datagrid->addColumn($code,   new TAction([$this, 'onColumnAction'], ['column' => 'code']) );
        $this->datagrid->addColumn($name,   new TAction([$this, 'onColumnAction'], ['column' => 'name']) );
        $this->datagrid->addColumn($city,   new TAction([$this, 'onColumnAction'], ['column' => 'city']) );
        $this->datagrid->addColumn($state,  new TAction([$this, 'onColumnAction'], ['column' => 'state']) );
        
        $code->title  = 'Here is the code';
        $name->title  = 'Here is the name';
        $city->title  = 'Here is the city';
        $state->title = 'Here is the state';
        
        // creates two datagrid actions
        $action1 = new TDataGridAction([$this, 'onView'],   ['code'=>'{code}',  'name' => '{name}'] );
        $action2 = new TDataGridAction([$this, 'onDelete'], ['code'=>'{code}'] );
        
        // custom button presentation
        $action1->setUseButton(TRUE);
        $action2->setUseButton(TRUE);
        
        // add the actions to the datagrid
        $this->datagrid->addAction($action1, 'View', 'fa:search blue');
        $this->datagrid->addAction($action2, 'Delete', 'far:trash-alt red');
        
        // creates the datagrid model
        $this->datagrid->createModel();
        
        $panel = new TPanelGroup(_t('Bootstrap Datagrid'));
        $panel->add($this->datagrid)->style = 'overflow-x:auto';
        $panel->addFooter('footer');
        
        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($panel);

        parent::add($vbox);
    }
    
    /**
     * Load the data into the datagrid
     */
    function onReload()
    {
        $this->datagrid->clear();
        $mixData = mockBanco::getExemploAdianti();
        $this->datagrid->addItem($mixData);
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
     * shows the page
     */
    function show()
    {
        $this->onReload();
        parent::show();
    }
}