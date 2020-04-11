<?php

/**
 * Classe para criação de um campo telefone
 * 
 * Esse é o FormDin 5, que é um reconstrução do 
 * FormDin 4.8 sobre o Adianti 7.1
 * 
 * @author Reinaldo A. Barrêto Junior
 */
class TFormDinFoneField
{
    protected $adiantiObj;
    protected $label;    

	/**
	 * Adiciona campo de entrada para telefone e fax
	 *
     * FormDin 5 - Alguns parametros foram DESATIVADO
     * por não funcionar no Adianti 7.1 e foram mantidos
     * para diminuir o impacto sobre a migração
     * 
	 * @param string $strName       - 1: ID do campo
	 * @param string $strLabel      - 2: Label do campo, que irá aparecer na tela do usuario
	 * @param boolean $boolRequired - 3: Obrigatorio
	 * @param boolean $boolNewLine  - 4: Campo em nova linha
	 * @param string $strValue
	 * @param boolean $boolLabelAbove
     * @param boolean $boolNoWrapLabel
     * @return TEntry
     */
    public function __construct(string $id
                               ,string $strLabel
                               ,$boolRequired = false
                               ,$boolSendMask = true
                               ,string $strValue=null
                               ,string $strExampleText =null)
    {
        $this->setLabel($strLabel);
        $this->adiantiObj = new TEntry($id);
        $this->adiantiObj->setId($id);
        $this->adiantiObj->setMask('99999-9999', $boolSendMask);
        $this->setRequired($boolRequired);
        if(!empty($strValue)){
            $this->adiantiObj->setValue($strValue);
        }
        $this->setExampleText($strExampleText);
        return $this->getAdiantiObj();
    }

    public function getAdiantiObj(){
        return $this->adiantiObj;
    }

    public function getLabel(){
        return $this->label;
    }
    public function setLabel($label){
        $this->label = $label;
    }

    public function setRequired($boolRequired){
        if($boolRequired){
            $strLabel = $this->getLabel();
            $this->adiantiObj->addValidation($strLabel, new TRequiredValidator);
        }
    }

    public function setExampleText($placeholder){
        if(!empty($placeholder)){
            $this->adiantiObj->placeholder = $placeholder;
        }
    }
}