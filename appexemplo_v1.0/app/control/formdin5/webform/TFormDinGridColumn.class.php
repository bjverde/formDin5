<?php

class TFormDinGridColumn
{
    protected $adiantiObj;
    
    /**
     * Coluna do Grid Padronizado em BoorStrap
     * Reconstruido FormDin 4 Sobre o Adianti 7.1
     *
     * @param  $action Callback to be executed
     * @param  string $name  = Name of the column in the database
     * @param  string $label = Text label that will be shown in the header
     * @param  string $align = Column align (left, center, right)
     * @param  string $width = Column Width (pixels)
     * @return BootstrapFormBuilder
     */
    public function __construct($action
                              , string $name
                              , string $label
                              , string $align='left'
                              , string $width = NULL)
    {
        $this->adiantiObj = new TDataGridColumn($name, $label,$align,$width);
        $tAction = new TAction([$action, 'onReload']);
        $this->adiantiObj->setAction( $tAction , ['order' => $name]);
        return $this->getAdiantiObj();
    }

    public function getAdiantiObj(){
        return $this->adiantiObj;
    }
}