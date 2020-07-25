<?php

use Adianti\Registry\TSession;

class exe_gride_field01_check extends TPage
{
    protected $form; // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    
    // trait com onReload, onSearch, onDelete...
    use Adianti\Base\AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();
        
        //$this->form = new TForm;

        $frm = new TFormDin($this,'Exemplo do Campo Texto');

        $frm->addTextField('TEXT01','Texto tam 10', 10);

        $this->form = $frm->show();
        
        // creates one datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->disableDefaultClick(); // important!
       
        
        // add the columns
        $this->datagrid->addColumn( new TDataGridColumn('check',   'Check',   'right',  '70') );
        $this->datagrid->addColumn( new TDataGridColumn('code',    'Code',    'right',  '10%') );
        $this->datagrid->addColumn( new TDataGridColumn('name',    'Name',    'left',   '30%') );
        $this->datagrid->addColumn( new TDataGridColumn('address', 'Address', 'left',   '30%') );
        $this->datagrid->addColumn( new TDataGridColumn('phone',   'Phone',   'center', '30%') );
        
        // creates the datagrid model
        $this->datagrid->createModel();

        $panel = new TPanelGroup(_t('Datagrids with Checkbutton'));
        $panel->add($this->datagrid)->style = 'overflow-x:auto';
        $this->form->addContent([$panel]);
        
        // creates the action button
        $button = TButton::create('action1', [$this, 'onSave'], 'Save', 'fa:save green');
        $this->form->addField($button);
        $panel->addFooter($button);
        
        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->form);
        parent::add($vbox);
    }

    /**
     * Load the data into the datagrid
     */
    function onReload()
    {
        $this->datagrid->clear();
        
        // add an regular object to the datagrid
        $item = new StdClass;
        $item->check    = new TCheckButton('check1');
        $item->check->setIndexValue('on');
        $item->code     = '1';
        $item->name     = 'Aretha Franklin';
        $item->address  = 'Memphis, Tennessee';
        $item->phone    = '1111-1111';
        $this->datagrid->addItem($item);
        $this->form->addField($item->check); // important!
        
        // add an regular object to the datagrid
        $item = new StdClass;
        $item->check  = new TCheckButton('check2');
        $item->check->setIndexValue('on');
        $item->code     = '2';
        $item->name     = 'Eric Clapton';
        $item->address  = 'Ripley, Surrey';
        $item->phone    = '2222-2222';
        $this->datagrid->addItem($item);
        $this->form->addField($item->check); // important!
        
        // add an regular object to the datagrid
        $item = new StdClass;
        $item->check  = new TCheckButton('check3');
        $item->check->setIndexValue('on');
        $item->code     = '3';
        $item->name     = 'B.B. King';
        $item->address  = 'Itta Bena, Mississippi';
        $item->phone    = '3333-3333';
        $this->datagrid->addItem($item);
        $this->form->addField($item->check); // important!
        
        // add an regular object to the datagrid
        $item = new StdClass;
        $item->check  = new TCheckButton('check4');
        $item->check->setIndexValue('on');
        $item->code     = '4';
        $item->name     = 'Janis Joplin';
        $item->address  = 'Port Arthur, Texas';
        $item->phone    = '4444-4444';
        $this->datagrid->addItem($item);
        $this->form->addField($item->check); // important!
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
     * Simulates an save button
     * Show the form content
     */
    public function onSave($param)
    {
        $data = $this->form->getData(); // optional parameter: active record class
        
        // put the data back to the form
        $this->form->setData($data);

        FormDinHelper::debug($param,'$param');
        FormDinHelper::debug($data,'$data');
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