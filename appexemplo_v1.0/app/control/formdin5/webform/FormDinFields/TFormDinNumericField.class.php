<?php

class TFormDinNumericField
{
    const COMMA = ',';
    const DOT = '.';

    protected $adiantiObj;
    protected $label;
    protected $decimalsSeparator;
    protected $thousandSeparator;

    /**
     * Campo de entrada de dados texto livre
     * Reconstruido FormDin 4 Sobre o Adianti 7
     *
     * @param string $id                 - 1: ID do campo
     * @param string $strLabel           - 2: Label do campo, que irá aparecer na tela do usuario
     * @param integer $intMaxLength      - 3: Quantidade maxima de digitos.
     * @param boolean $boolRequired      - 4: Obrigatorio
     * @param integer $decimalPlaces     - 5: Quantidade de casas decimais.
     * @param boolean $boolNewLine       - 6: Campo em nova linha. Default = true = inicia em nova linha, false = continua na linha anterior 
     * @param string $strValue           - 7: valor inicial do campo
     * @param string $strMinValue        - 8: valor minimo permitido. Null = não tem limite.
     * @param string $strMaxValue        - 9: valor maxima permitido. Null = não tem limite.
     * @param boolean $boolFormatInteger -10: Inteiros com ou sem ponto de separação
     * @param string $strDirection
     * @param boolean $boolAllowZero
     * @param boolean $boolAllowNull
     * @param boolean $boolLabelAbove
     * @param boolean $boolNoWrapLabel
     * @param string $strHint
     * @return TNumber
     */ 
    public function __construct(string $id
                               ,string $strLabel
                               ,int $intMaxLength = null
                               ,$boolRequired = false
                               ,int $decimalPlaces=null
                               ,$boolNewLine=null
                               ,$strValue=null
                               ,$strMinValue=null
                               ,$strMaxValue=null
                               ,$boolFormatInteger=null
                               ,$strDirection=null
                               ,$boolAllowZero=null
                               ,$boolAllowNull=null
                               ,$boolLabelAbove=null
                               ,$boolNoWrapLabel=null
                               ,$strHint=null
                               ,string $strExampleText =null)
    {
        $decimalsSeparator = $this->getDecimalsSeparator();
        $thousandSeparator = $this->getThousandSeparator();
        $this->setLabel($strLabel);
        $this->adiantiObj = new TNumeric($id, $decimalPlaces, $decimalsSeparator, $thousandSeparator, $replaceOnPost = true);
        $this->adiantiObj->setId($id);
        $this->setRequired($boolRequired);
        if(!empty($strValue)){
            $this->adiantiObj->setValue($strValue);
        }
        $this->setMaxLength($intMaxLength);
        $this->setMinValue($strMinValue);
        $this->setMaxValue($strMaxValue);
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

    public function getDecimalsSeparator(){
        $separator = null;
        if(empty($this->decimalsSeparator)){
            $separator = self::COMMA;
        }else{
            $separator = $this->decimalsSeparator;
        }
        return $separator;
    }

    public function setDecimalsSeparator($decimalsSeparator){
        $this->decimalsSeparator = $decimalsSeparator;
    }

    public function getThousandSeparator(){
        $separator = null;
        if(empty($this->thousandSeparator)){
            $separator = self::DOT;
        }else{
            $separator = $this->thousandSeparator;
        }
        return $separator;
    }

    public function setThousandSeparator($thousandSeparator){
        $this->thousandSeparator = $thousandSeparator;
    }

    public function setRequired($boolRequired){
        if($boolRequired){
            $strLabel = $this->getLabel();
            $this->adiantiObj->addValidation($strLabel, new TRequiredValidator);
        }
    }

    public function setExampleText($placeholder)
    {
        if(!empty($placeholder)){
            $this->adiantiObj->placeholder = $placeholder;
        }
    }

    public function setMaxLength($intMaxLength)
    {
        if($intMaxLength>=1){
            $strLabel = $this->getLabel();
            $this->adiantiObj->addValidation($strLabel, new TMaxLengthValidator, array($intMaxLength));
        }
    }
    public function setMinValue($strMinValue)
    {
        if(is_int($strMinValue)){
            $strLabel = $this->getLabel();
            $this->adiantiObj->addValidation($strLabel, new TMinValueValidator, array($strMinValue));
        }
    }

    public function setMaxValue($strMaxValue)
    {
        if(is_int($strMaxValue)){
            $strLabel = $this->getLabel();
            $this->adiantiObj->addValidation($strLabel, new TMaxValueValidator, array($strMaxValue));
        }
    }
}