<?php

class TFormDinLabelField
{
    protected $adiantiObj;
    

    /**
     * Label do campo de entrada
     * Reconstruido FormDin 4 Sobre o Adianti 7
     *
     * @param string $strLabel      - 1: Label do campo, usado para validações
     * @param boolean $boolRequired - 2: Obrigatorio. DEFAULT = False.
     * @return TEntry
     */
    public function __construct(string $strLabel
                               ,$boolRequired = false)
    {        
        if($boolRequired){
            $this->adiantiObj = new TLabel($strLabel, 'red');
        }else{
            $this->adiantiObj = new TLabel($strLabel);
        }
    }

    public function getAdiantiObj(){
        return $this->adiantiObj;
    }
}