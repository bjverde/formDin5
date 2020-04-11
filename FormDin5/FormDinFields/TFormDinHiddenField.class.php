<?php

class TFormDinHiddenField
{
    protected $adiantiObj;
    

    /**
    * Adiciona um campo oculto ao layout
    * Reconstruido FormDin 4 Sobre o Adianti 7
    *
    * @param string $strName       - 1: Id do Campo
    * @param string $strValue      - 2: Valor inicial
    * @param boolean $boolRequired - 3: True = Obrigatorio; False (Defalt) = Não Obrigatorio  
    * @return THidden
    */
    public function __construct(string $id
                               ,string $strValue=null
                               ,$boolRequired = false)
    {
        $this->adiantiObj = new THidden($id);
        $this->adiantiObj->setId($id);
        if($boolRequired){
            $this->adiantiObj->addValidation($id, new TRequiredValidator);
        }
        if(!empty($strValue)){
            $this->adiantiObj->setValue($strValue);
        }
        return $this->getAdiantiObj();
    }

    public function getAdiantiObj(){
        return $this->adiantiObj;
    }
}