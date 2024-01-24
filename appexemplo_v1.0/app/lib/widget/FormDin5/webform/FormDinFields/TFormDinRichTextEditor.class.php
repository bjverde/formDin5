<?php
        
class TFormDinRichTextEditor extends TFormDinMemoField
{
    /**
     * Adicionar campo de entrada de dados de varias linhas (textareas)
     * ------------------------------------------------------------------------
     * Esse é o FormDin 5, que é uma reconstrução do FormDin 4 Sobre o Adianti 7.X
     * os parâmetros do metodos foram marcados veja documentação da classe para
     * saber o que cada marca singinifica.
     * ------------------------------------------------------------------------
     *
     * @param string  $id              - 1: ID do campo
     * @param string  $label           - 2: Label
     * @param integer $intMaxLength    - 3: Tamanho maximos
     * @param boolean $boolRequired    - 4: Campo obrigatório ou não. Default FALSE = não obrigatório, TRUE = obrigatório
     * @param integer $intColumns      - 5: Largura use unidades responsivas % ou em ou rem ou vh ou vw. Valores inteiros até 100 serão convertidos para % , acima disso será 100%
     * @param integer $intRows         - 6: Altura use px ou %, valores inteiros serão multiplicados 4 e apresentado em px
     * @param boolean $boolNewLine     - 7: NOT_IMPLEMENTED nova linha
     * @param boolean $boolLabelAbove  - 8: NOT_IMPLEMENTED Label sobre o campo
     * @return TFormDinRichTextEditor
     */
    public function __construct($id,
                               $label,
                               $intMaxLength,
                               $boolRequired=null,
                               $intColumns='100%',
                               $intRows='100%',
                               $boolNewLine=null,
   		                       $boolLabelAbove=false )
    {
        return parent::__construct($id,$label,$intMaxLength,$boolRequired,$intColumns,$intRows,$boolNewLine,$boolLabelAbove);
    }

}