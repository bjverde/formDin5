<?php
        
class TFormDinRichTextEditor extends TFormDinGenericField
{
    private $showCountChar;
    private $intMaxLength;

    private THtmlEditor $adiantiObjRichEditor; //Somente obj Adianti Customizada
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
     * @param string $boolShowCountChar 09: FORMDIN5 Mostra o contador de caractes.  Default TRUE = mostra, FASE = não mostra
     * @return TFormDinRichTextEditor
     */
    public function __construct($id
                               ,$label
                               ,$intMaxLength
                               ,$boolRequired=null
                               ,$intColumns='100%'
                               ,$intRows='100%'
                               ,$value=null
                               ,$placeholder=null
                               ,$boolShowCountChar=false
                               )
    {
        TScript::importFromFile('app/lib/widget/FormDin5/javascript/FormDin5RichEditor.js?appver='.FormDinHelper::version());
        $this->setAdiantiObjRichEditor($id);
        parent::__construct($this->getAdiantiObjRichEditor(),$id,$label,$boolRequired,$value,$placeholder);
        $this->setSize($intColumns, $intRows);
        $this->setMaxLength($label,$intMaxLength);       

        $this->setAdiantiObjTFull($id,$boolShowCountChar,$intMaxLength);
        
        return $this->getAdiantiObj();       
    }

    public function setAdiantiObjRichEditor($id){
        $this->adiantiObjRichEditor = new THtmlEditor($id);              
    }

    public function getAdiantiObjRichEditor(): THtmlEditor {
        return $this->adiantiObjRichEditor;
    }

    private function setAdiantiObjTFull( $idField, $boolShowCountChar,$intMaxLength )
    {
        $adiantiObjRichEditor = $this->getAdiantiObjRichEditor();                
        $adiantiObj = null;
        $div = new TElement('div');
        $div->add($adiantiObjRichEditor);        

        TScript::create( " setToolBarRichEditor('{$adiantiObjRichEditor->id}', [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']]
          ]); " );

        if( $boolShowCountChar && ($intMaxLength>=1) ){
            $adiantiObjRichEditor->maxlength = $intMaxLength;
            $adiantiObjRichEditor->setId($idField);            

            $charsText  = new TElement('span');
            $charsText->setProperty('id',$idField.'_counter');
            $charsText->setProperty('name',$idField.'_counter');
            $charsText->setProperty('class', 'tformdinmemo_counter');
            $charsText->add('caracteres: 0 / '.$intMaxLength);

              
            $div->add($charsText);
            $div->add($script);           
           
            
        }
        $adiantiObj = $div;
               
        $this->adiantiObjFull = $adiantiObj;
    }

    public function getAdiantiObjFull(){
        return $this->adiantiObjFull;
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

    public function setShowCountChar($showCountChar)
    {
        $this->showCountChar = $showCountChar;
    }

    public function getShowCountChar()
    {
        return $this->showCountChar;
    }

}