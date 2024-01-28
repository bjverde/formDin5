<?php
        
class TFormDinRichTextEditor extends TFormDinGenericField
{
    private $showCountChar;
    private $intMaxLength;

    private $adiantiObjFull;  //obj Adianti completo com todos os elementos para fazer o memo

    /**
     * Adicionar campo de entrada de dados de varias linhas (textareas)
     * ------------------------------------------------------------------------
     * Esse é o FormDin 5, que é uma reconstrução do FormDin 4 Sobre o Adianti 7.X
     * os parâmetros do metodos foram marcados veja documentação da classe para
     * saber o que cada marca singinifica.
     * ------------------------------------------------------------------------
     *
     * @param string  $id              -01: ID do campo
     * @param string  $label           -02: Label
     * @param integer $intMaxLength    -03: Tamanho maximos
     * @param boolean $boolRequired    -04: Campo obrigatório ou não. Default FALSE = não obrigatório, TRUE = obrigatório
     * @param integer $intColumns      -05: Largura use unidades responsivas % ou em ou rem ou vh ou vw. Valores inteiros até 100 serão convertidos para % , acima disso será 100%
     * @param integer $intRows         -06: Altura use px ou %, valores inteiros serão multiplicados 4 e apresentado em px
     * @param string  $strValue        -07: Valor padrão
     * @param string  $placeholder     -08: FORMDIN5 PlaceHolder é um Texto de exemplo
     * @return TFormDinRichTextEditor
     */
    public function __construct($id
                               ,string $label
                               ,int|NULL $intMaxLength
                               ,$boolRequired=null
                               ,$intColumns='100%'
                               ,$intRows='100%'
                               ,$value=null
                               ,$placeholder=null
                               )
    {
        $adiantiObj = new THtmlEditor($label);
        parent::__construct($adiantiObj,$id,$label,$boolRequired,$value,$placeholder);
        $this->setMaxLength($label,$intMaxLength);
        //$this->setSize($intColumns, $intRows);
        return $this->getAdiantiObj();       
    }

    /**
     * Seta o tamanho maximo do campo
     * @param string $label     - 01 nome do campo
     * @param int $intMaxLength - 02 tamanho do campo
     * @return void
     */
    public function setMaxLength($label,$intMaxLength)
    {
        $this->intMaxLength = (int) $intMaxLength;
        if($intMaxLength>=1){
            //$this->getAdiantiObj()->setMaxLength($intMaxLength); //Liga um contador porém IMPEDE colar
            $this->getAdiantiObj()->addValidation($label, new TMaxLengthValidator, array($intMaxLength));
        }
    }

    public function getMaxLength()
    {
        return $this->intMaxLength;
    }

    public function setSize($intColumns, $intRows)
    {
        if(is_numeric($intRows)){
            $intRows = $intRows * 4;
        }else{
            FormDinHelper::validateSizeWidthAndHeight($intRows,true);
        }
        $intColumns = FormDinHelper::sizeWidthInPercent($intColumns);
        $this->getAdiantiObj()->setSize($intColumns, $intRows);
    }

    public function disableToolbar(){
        $this->getAdiantiObj()->disableToolbar();
    }

}