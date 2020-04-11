<?php

class TFormDinGrid
{
    protected $adiantiObj;

    protected $action;
    protected $idGrid;
    protected $title;
    protected $key;
    
    /**
     * Grid Padronizado em BoorStrap
     * Reconstruido FormDin 4 Sobre o Adianti 7
     *
     * @param $action Callback to be executed
     * @param string $strName       - 1: ID do Grid
     * @param string $strTitle      - 2: Titulo do Grid
     * @param string $strKeyField   - 3: Id da chave primaria
     * 
     * @return BootstrapFormBuilder
     */

    /**
     * Grid Padronizado em BoorStrap
     * Reconstruido FormDin 4 Sobre o Adianti 7
     *
     * @param [type] $action         - 1: função callback $this na classe origem
     * @param string $idGrid         - 2: ID do Grid recebe __CLASS__
     * @param string $title          - 3: Titulo do Grid
     * @param string $key            - 4: Id da chave primaria
     * @param boolean $boolDataTable
     * @param boolean $boolDefaultClick
     */
    public function __construct($action
                               ,string $idGrid
                               ,string $title
                               ,string $key
                               ,$boolDataTable = false
                               ,$boolDefaultClick = true
                               )
    {
        $this->adiantiObj = new BootstrapDatagridWrapper(new TDataGrid);
        $this->adiantiObj->width = '100%';
        if($boolDataTable){
            $this->adiantiObj->datatable = 'true';
        }
        if(!$boolDefaultClick){
            $this->adiantiObj->disableDefaultClick();
        }
        $this->setAction($action);
        $this->setIdGrid($idGrid);
        $this->setTitle($title);
        $this->setKey($key);
    }

    public function getAdiantiObj(){
        //$title = $this->getTitle();
        //$panel = new TPanelGroup($title);
        //$panel->add( $this->adiantiObj );
        return $this->adiantiObj;
    }

    /**
     * Coluna do Grid Padronizado em BoorStrap
     * Reconstruido FormDin 4 Sobre o Adianti 7.1
     *
     * @param  string $name  = Name of the column in the database
     * @param  string $label = Text label that will be shown in the header
     * @param  string $align = Column align (left, center, right)
     * @param  string $width = Column Width (pixels)
     * @return TDataGridColumn
     */
    public function addColumn(string $name
                            , string $label
                            , string $align='left'
                            , string $width = NULL){
        $action = $this->getAction();
        $formDinGridColumn = new TFormDinGridColumn($action, $name, $label,$align,$width);
        $column = $formDinGridColumn->getAdiantiObj();
        $this->adiantiObj->addColumn($column);
        return $column;
    }

    public function getAction(){
        return $this->action;
    }

    public function setAction($action){
        $this->action = $action;
    }

    public function getIdGrid(){
        return $this->idGrid;
    }

    public function setIdGrid(string $idGrid){
        $this->idGrid = $idGrid;
    }

    public function getTitle(){
        return $this->title;
    }

    public function setTitle(string $title){
        $this->title = $title;
    }

    public function getKey(){
        return $this->key;
    }

    public function setKey(string $key){
        $this->key = $key;
    }
}