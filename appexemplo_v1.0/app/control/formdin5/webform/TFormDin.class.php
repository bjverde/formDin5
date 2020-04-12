<?php

/**
 * Classe para criação de formulários web para entrada de dados
 * 
 * Esse é o FormDin 5, que é um reconstrução do 
 * FormDin 4.8 sobre o Adianti 7.1
 * 
 * @author Reinaldo A. Barrêto Junior
 */
class TFormDin
{
    protected $adiantiObj;
    
    /**
     * Formulario Padronizado em BoorStrap
     *
     * @param string $strName       - 1: Name do Form
     * @param string $strTitle      - 2: Titulo que irá aparecer no Form
     * @param boolean $boolRequired - 3: Se vai fazer validação no Cliente (Navegador)
     * @return BootstrapFormBuilder
     */
    public function __construct(string $strName
                               ,string $strTitle
                               ,$boolClientValidation = true)
    {
        $this->adiantiObj = new BootstrapFormBuilder($strName);
        $this->adiantiObj->setFormTitle($strTitle);
        $this->adiantiObj->setClientValidation($boolClientValidation);
        return $this->getAdiantiObj();
    }

    public function getAdiantiObj(){
        return $this->adiantiObj;
    }

    /**
     * Inclusão 
     * @param array $label - label que será incluido com o campo
     * @param array $campo - campo que será incluido
     */
    public function addFields(array $label, array $campo){
        $this->adiantiObj->addFields($label, $campo);
    }

    protected function getLabelField($strLabel,$boolRequired)
    {
        $formDinLabelField = new TFormDinLabelField($strLabel,$boolRequired);
        $label = $formDinLabelField->getAdiantiObj();
        return $label;
    }

    /**
    * Adiciona um campo oculto ao layout
    * 
    * FormDin 5 - Alguns parametros foram DESATIVADO
    * por não funcionar no Adianti 7.1 e foram mantidos
    * para diminuir o impacto sobre a migração
    *
    * @param string $strName       - 1: Id do Campo
    * @param string $strValue      - 2: Valor inicial
    * @param boolean $boolRequired - 3: True = Obrigatorio; False (Defalt) = Não Obrigatorio  
    * @return THidden
    */
    public function addHiddenField(string $id
                                ,string $strValue=null
                                ,$boolRequired = false)
    {
        $formField = new TFormDinHiddenField($id,$strValue,$boolRequired);
        $objField = $formField->getAdiantiObj();
        $this->adiantiObj->addFields([$objField]);
        return $objField;
    }

    /**
     * Adicionar campo entrada de dados texto livre.
     * 
     * FormDin 5 - Alguns parametros foram DESATIVADO
     * por não funcionar no Adianti 7.1 e foram mantidos
     * para diminuir o impacto sobre a migração
     *
     * @param string $id              -  1: ID do campo
     * @param string $strLabel        -  2: Label do campo
     * @param integer $intMaxLength   -  3: tamanho máximo de caracteres
     * @param boolean $boolRequired   -  4: Obrigatorio ou não. DEFAULT = False.
     * @param integer $intSize        -  5: DESATIVADO quantidade de caracteres visíveis
     * @param string $strValue        -  6: texto preenchido
     * @param boolean $boolNewLine    -  7: DESATIVADO Nova linha
     * @param string $strHint         -  9: DESATIVADO
     * @param string $strExampleText  -  9: PlaceHolder um Texto de exemplo
     * @param boolean $boolLabelAbove - 10: DESATIVADO - Label sobre
     * @param boolean $boolNoWrapLabel- 11: DESATIVADO
     * @return TEntry
     */
    public function addTextField(string $id
                                ,string $strLabel
                                ,int $intMaxLength = null
                                ,$boolRequired = false
                                ,int $intSize=null
                                ,string $strValue=null
                                ,$boolNewLine = true
                                ,string $strHint = null
                                ,string $strExampleText =null
                                ,$boolLabelAbove=null
                                ,$boolNoWrapLabel = null)
    {
        $formDinTextField = new TFormDinTextField($id
                                                 ,$strLabel
                                                 ,$intMaxLength
                                                 ,$boolRequired
                                                 ,$strValue);
        $formDinTextField->setExampleText($strExampleText);
        $objField = $formDinTextField->getAdiantiObj();
        $label = $this->getLabelField($strLabel,$boolRequired);
        $this->addFields([$label], [$objField]);
        return $objField;
    }

    /**
     * Cria um RadioGroup com efeito visual de Switch dp BootStrap
     * 
     * FormDin 5 - Alguns parametros foram DESATIVADO
     * por não funcionar no Adianti 7.1 e foram mantidos
     * para diminuir o impacto sobre a migração
     * 
     * @param string $id            - 1: ID do campo
     * @param string $strLabel      - 2: Label do campo
     * @param boolean $boolRequired - 3: Obrigatorio
     * @param array $itens          - 4: Informe um array do tipo "chave=>valor", com maximo de 2 elementos
     * @return TRadioGroup
     */
    public function addSwitchField(string $id
                                  ,string $strLabel
                                  ,$boolRequired = false
                                  ,array $itens= null)
    {
        $formDinSwitch = new TFormDinSwitch($id,$strLabel,$boolRequired,$itens);
        $objField = $formDinSwitch->getAdiantiObj();
        $label = $this->getLabelField($strLabel,$boolRequired);
        $this->addFields([$label], [$objField]);
        return $objField;
    }

    /**
    * Adicionar campo tipo combobox ou menu select
    *
    * FormDin 5 - Alguns parametros foram DESATIVADO
    * por não funcionar no Adianti 7.1 e foram mantidos
    * para diminuir o impacto sobre a migração
    *
    * $mixOptions = array no formato "key=>value". No FormDin 5 só permite array PHP
    * $strKeyColumn = nome da coluna que será utilizada para preencher os valores das opções
    * $strDisplayColumn = nome da coluna que será utilizada para preencher as opções que serão exibidas para o usuário
    * $strDataColumns = informações extras do banco de dados que deverão ser adicionadas na tag option do campo select
    *
    * <code>
    * 	// exemplos
    * 	$frm->addSelectField('tipo','Tipo:',false,'1=Tipo 1,2=Tipo 2');
    * 	$frm->addSelectField('tipo','Tipo:',false,'tipo');
    * 	$frm->addSelectField('tipo','Tipo:',false,'select * from tipo order by descricao');
    * 	$frm->addSelectField('tipo','Tipo:',false,'tipo|descricao like "F%"');
    *
    *  //Exemplo espcial - Campo obrigatorio e sem senhum elemento pre selecionado.
    *  $frm->addSelectField('tipo','Tipo',true,$tiposDocumentos,null,null,null,null,null,null,' ','');
    * </code>
    *
    * @param string  $strName        - 1: ID do campo
    * @param string  $strLabel       - 2: Label do campo
    * @param boolean $boolRequired   - 3: Obrigatorio. Default FALSE
    * @param mixed   $mixOptions     - 4: array dos valores. no formato "key=>value". No FormDin 5 só permite array PHP
    * @param boolean $boolNewLine    - 5: DESATIVADO Default TRUE = cria nova linha , FALSE = fica depois do campo anterior
    * @param boolean $boolLabelAbove - 6: DESATIVADO Default FALSE = Label mesma linha, TRUE = Label acima
    * @param mixed   $mixValue       - 7: DESATIVADO Valor DEFAULT, informe o ID do array
    * @param boolean $boolMultiSelect- 8: DESATIVADO Default FALSE = SingleSelect, TRUE = MultiSelect
    * @param integer $intSize             - 9: DESATIVADO Default 1. Num itens que irão aparecer. 
    * @param integer $intWidth           - 10: DESATIVADO Largura em Pixels
    * @param string  $strFirstOptionText - 11: DESATIVADO First Key in Display
    * @param string  $strFirstOptionValue- 12: DESATIVADO Frist Valeu in Display, use value NULL for required. Para o valor DEFAULT informe o ID do $mixOptions e $strFirstOptionText = '' e não pode ser null
    * @param string  $strKeyColumn       - 13: DESATIVADO
    * @param string  $strDisplayColumn   - 14: DESATIVADO
    * @param string  $boolNoWrapLabel    - 15: DESATIVADO
    * @param string  $strDataColumns     - 16: DESATIVADO
    * @return TCombo
    */     
    public function addSelectField(string $id
                                  ,string $strLabel
                                  ,$boolRequired = false
                                  ,array $mixOptions)
    {
        $formDinSelectField = new TFormDinSelectField($id,$strLabel,$boolRequired,$mixOptions);
        $objField = $formDinSelectField->getAdiantiObj();
        $label = $this->getLabelField($strLabel,$boolRequired);
        $this->addFields([$label], [$objField]);
        return $objField;
    }

    //----------------------------------------------------------------
    //----------------------------------------------------------------
    //----------------------------------------------------------------
    //----------------------------------------------------------------s    

    /**
     * @deprecated mantido apenas para diminir o impacto na migração do FormDin 4 para FormDin 5 sobre Adianti 7.1
     * @return void
     */
    public function setShowCloseButton(){        
    }

    /**
     * @deprecated mantido apenas para diminir o impacto na migração do FormDin 4 para FormDin 5 sobre Adianti 7.1
     * @return void
     */
    public function setFlat(){        
    }

    /**
     * @deprecated mantido apenas para diminir o impacto na migração do FormDin 4 para FormDin 5 sobre Adianti 7.1
     * @return void
     */
    public function setMaximize(){        
    }

    /**
     * @deprecated mantido apenas para diminir o impacto na migração do FormDin 4 para FormDin 5 sobre Adianti 7.1
     * @return void
     */
    public function setHelpOnLine(){        
    }
}