<?php
/**
 * Tb_mapcordFormGrid
 *
 * Formulário e Listagem padrão Adianti para tb_mapcord
 */
class Tb_mapcordFormGrid extends TPage
{
    protected $form;            // form
    protected $datagrid;        // datagrid
    protected $loaded;
    protected $pageNavigation;  // pagination component
    
    // trait with onSave, onEdit, onDelete, onReload, onSearch...
    use Adianti\Base\AdiantiStandardFormListTrait {
        onSave as onSaveTrait;
    }
    
    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->setDatabase('dbapoio');             // define the database
        $this->setActiveRecord('Tb_mapcord');      // define the Active Record
        $this->setDefaultOrder('idtmapcord', 'asc'); // define the default order
        $this->setLimit(-1);                       // turn off limit for datagrid
        
        // create the form
        $this->form = new BootstrapFormBuilder('form_tb_mapcord');
        $this->form->setFormTitle('Cadastro de Coordenadas (tb_mapcord)');
        
        // create the form fields
        $idtmapcord  = new TEntry('idtmapcord');
        $txnome      = new TEntry('txnome');
        //$mapcord_lat = new TEntry('mapcord_lat');
        //$mapcord_lon = new TEntry('mapcord_lon');
        //$mapcord_lat->addValidation('Latitude', new TRequiredValidator);

        $formField = new TFormDinMapCord( $this->form
                                                ,'mapcord'
                                                ,'Coordenadas'
                                                ,true
                                                ,true
                                                ,false
                                                );
        $objField = $formField->getAdiantiObj();        
        
        // add the form fields
        $this->form->addFields( [new TLabel('ID')], [$idtmapcord] );
        $this->form->addFields( [new TLabel('Nome / Descrição')], [$txnome] );

        //$this->form->addFields( [new TLabel('Latitude', 'red')], [$mapcord_lat] );
        //$this->form->addFields( [new TLabel('Longitude')], [$mapcord_lon] );
        $this->form->addFields( [new TLabel('lat / lon ')],[$objField ] );
        
        
        // define the form actions
        $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:save green');
        $this->form->addActionLink('Limpar', new TAction([$this, 'onClear']), 'fa:eraser red');
        
        // make id not editable
        $idtmapcord->setEditable(FALSE);
        
        // create the datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->width = '100%';
        
        // add the columns
        $col_id   = new TDataGridColumn('idtmapcord', 'ID', 'center', '10%');
        $col_nome = new TDataGridColumn('txnome', 'Nome / Descrição', 'left', '40%');
        $col_lat  = new TDataGridColumn('mapcord_lat', 'Latitude', 'left', '25%');
        $col_lon  = new TDataGridColumn('mapcord_lon', 'Longitude', 'left', '25%');
        
        $this->datagrid->addColumn($col_id);
        $this->datagrid->addColumn($col_nome);
        $this->datagrid->addColumn($col_lat);
        $this->datagrid->addColumn($col_lon);
        
        $col_id->setAction( new TAction([$this, 'onReload']),   ['order' => 'idtmapcord']);
        $col_nome->setAction( new TAction([$this, 'onReload']), ['order' => 'txnome']);
        $col_lat->setAction( new TAction([$this, 'onReload']),  ['order' => 'mapcord_lat']);
        $col_lon->setAction( new TAction([$this, 'onReload']),  ['order' => 'mapcord_lon']);
        
        // define row actions
        $action1 = new TDataGridAction([$this, 'onEdit'],   ['key' => '{idtmapcord}'] );
        $action2 = new TDataGridAction([$this, 'onDelete'], ['key' => '{idtmapcord}'] );
        
        $this->datagrid->addAction($action1, 'Editar',   'far:edit blue');
        $this->datagrid->addAction($action2, 'Excluir',  'far:trash-alt red');
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // wrap objects inside a table
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->form);
        $vbox->add(TPanelGroup::pack('', $this->datagrid));
        
        // pack the table inside the page
        parent::add($vbox);
    }

    /**
     * Executed whenever the user clicks at the save button
     *
     * @param $param Request parameters
     */
    public function onSave($param = null)
    {
        $data = $this->form->getData(); // get form data as array
        echo "<pre>";
        echo "param: ";
        var_dump($param);
        echo "<hr>";
        echo "data: ";
        var_dump($data);
        echo "</pre>";
        // Custom logic before saving can be placed here

        // Call the trait onSave method
        return $this->onSaveTrait();
    }
}
