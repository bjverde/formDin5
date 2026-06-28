<?php
use Adianti\Registry\TSession;

class exe_grid21 extends TPage
{
    private $datagrid;
    private static $formName = 'form_exe_grid21';
    
    public function __construct()
    {
        parent::__construct();


        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->setId(__CLASS__.'_datagrid');

        $this->datagrid_form = new TForm('datagrid_'.self::$formName);
        $this->datagrid_form->onsubmit = 'return false';

        $this->filter_criteria = new TCriteria;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->disableDefaultClick();
        $this->datagrid->datatable = 'true';
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(250);
        
        $this->datagrid->enablePopover('Details', '<b>Code:</b> {code} <br> <b>Name:</b> {name} <br> <b>City:</b> {city} <br> <b>State:</b> {state}');
        
        // create the datagrid columns
        $code       = new TDataGridColumn('code',    'Code','center');
        $name       = new TDataGridColumn('name',    'Name','left');
        $date       = new TDataGridColumn('date',    'Date','center');
        $date->setTransformer(function($value, $object, $row) {
            return TFormDinGridTransformer::date($value);
        });
        $dateTime   = new TDataGridColumn('dateTime', 'Date Time','center');
        $dateTime->setTransformer(function($value, $object, $row) {
            return TFormDinGridTransformer::gridDateTime($value, $object, $row);
        });

        $telefone = new TDataGridColumn('telefone', 'telefone','center');
        $telefone->setTransformer(function($value, $object, $row) {
            return TFormDinGridTransformer::linkApiWhatsApp($value, $object, $row, '',true);
        });

        $sim1 = new TDataGridColumn('sim1', 'sim1','center');
        $sim1->setTransformer(function($value, $object, $row) {
            $value = StringHelper::strtoupper_utf8($value);
            return TFormDinGridTransformer::simNaoComLabel($value, $object, $row);
        });

        $numBr = new TDataGridColumn('numBr', 'numBr','center');
        $numBr->setTransformer(function($value, $object, $row) {
            return TFormDinGridTransformer::gridNumeroBrasilFormatStyle($value, $object, $row);
        });
        $lat = new TDataGridColumn('lat', 'Maps','center');
        $lat->setTransformer(function($value, $object, $row) {
            return TFormDinGridTransformer::gridCordLatLonLinkGoogleMaps($object->lat, $object->lon, null,true,'Google Maps');
        });        

        $img    = new TDataGridColumn('img','Image','center');
        $img->setTransformer(function($value, $object, $row) {
            return TFormDinGridTransformer::gridImg($value, $object, $row,'app/images/','100px','app/images/icon.png');
        });
        $texto    = new TDataGridColumn('texto','Text','center');
        $texto->setTransformer(function($value, $object, $row) {
            return TFormDinGridTransformer::gridTextoScroll($value, $object, $row,'100px');
        });        
        
        // add the columns to the datagrid, with actions on column titles, passing parameters
        $this->datagrid->addColumn($code);
        $this->datagrid->addColumn($name);
        $this->datagrid->addColumn($date);
        $this->datagrid->addColumn($dateTime);
        $this->datagrid->addColumn($sim1);
        $this->datagrid->addColumn($numBr);
        $this->datagrid->addColumn($lat);
        $this->datagrid->addColumn($telefone);
        $this->datagrid->addColumn($img);
        $this->datagrid->addColumn($texto);
        
        $code->title  = 'Here is the code';
        $name->title  = 'Here is the name';
        //$city->title  = 'Here is the city';
        //$state->title = 'Here is the state';
        
        // creates two datagrid actions
        $action1 = new TDataGridAction([$this, 'onView'], ['code'=>'{code}',  'name' => '{name}'] );
        
        // custom button presentation
        $action1->setUseButton(TRUE);
        
        // add the actions to the datagrid
        $this->datagrid->addAction($action1, 'View', 'fa:search blue');
        
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
        $listData = mockBanco::getExemploAdianti();
        if ($listData) {
            foreach ($listData as $item) {
                $this->datagrid->addItem( (object) $item );
            }
        }
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