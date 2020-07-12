<?php
/*
 * ----------------------------------------------------------------------------
 * Formdin 5 Framework
 * SourceCode https://github.com/bjverde/formDin5
 * @author Reinaldo A. Barrêto Junior
 * 
 * É uma reconstrução do FormDin 4 Sobre o Adianti 7.X
 * ----------------------------------------------------------------------------
 * This file is part of Formdin Framework.
 *
 * Formdin Framework is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public License version 3
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License version 3
 * along with this program; if not,  see <http://www.gnu.org/licenses/>
 * or write to the Free Software Foundation, Inc., 51 Franklin Street,
 * Fifth Floor, Boston, MA  02110-1301, USA.
 * ----------------------------------------------------------------------------
 * Este arquivo é parte do Framework Formdin.
 *
 * O Framework Formdin é um software livre; você pode redistribuí-lo e/ou
 * modificá-lo dentro dos termos da GNU LGPL versão 3 como publicada pela Fundação
 * do Software Livre (FSF).
 *
 * Este programa é distribuído na esperança que possa ser útil, mas SEM NENHUMA
 * GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer MERCADO ou
 * APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/LGPL em português
 * para maiores detalhes.
 *
 * Você deve ter recebido uma cópia da GNU LGPL versão 3, sob o título
 * "LICENCA.txt", junto com esse programa. Se não, acesse <http://www.gnu.org/licenses/>
 * ou escreva para a Fundação do Software Livre (FSF) Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA 02111-1301, USA.
 */

/**
 * Classe para criação de Grid para apresentar os dados
 * ------------------------------------------------------------------------
 * Esse é o FormDin 5, que é uma reconstrução do FormDin 4 Sobre o Adianti 7.X
 * os parâmetros do metodos foram marcados com:
 * 
 * NOT_IMPLEMENTED = Parâmetro não implementados, talvez funcione em 
 *                   verões futuras do FormDin. Não vai fazer nada
 * DEPRECATED = Parâmetro que não vai funcionar no Adianti e foi mantido
 *              para diminuir o impacto sobre as migrações. Vai gerar um Warning
 * FORMDIN5 = Parâmetro novo disponivel apenas na nova versão
 * ------------------------------------------------------------------------
 * 
 * @author Reinaldo A. Barrêto Junior
 */
class TFormDinGrid
{
    protected $adiantiObj;
    protected $panelGroupGrid;

    protected $action;
    protected $idGrid;
    protected $title;
    protected $key;

    /**
     * Classe para criação de grides, Padronizado em BoorStrap
     * Reconstruido FormDin 4 Sobre o Adianti 7
     * 
     * Parametros do evento onDrawHeaderCell
     * 	1) $th			- objeto TElement
     * 	2) $objColumn 	- objeto TGridColum
     * 	3) $objHeader 	- objeto TElement
     *
     * Parametros do envento onDrawRow
     * 	1) $row 		- objeto TGridRow
     * 	2) $rowNum 		- número da linha corrente
     * 	3) $aData		- o array de dados da linha ex: $res[''][n]
     *
     * Parametros do envento onDrawCell
     * 	1) $rowNum 		- número da linha corrente
     * 	2) $cell		- objeto TTableCell
     * 	3) $objColumn	- objeto TGrideColum
     * 	4) $aData		- o array de dados da linha ex: $res[''][n]
     * 	5) $edit		- o objeto campo quando a coluna for um campo de edição
     *   ex: function ondrawCell($rowNum=null,$cell=null,$objColumn=null,$aData=null,$edit=null)
     *
     * Parametros do evento onDrawActionButton
     * 	1) $rowNum 		- número da linha corrente
     * 	2) $button 		- objeto TButton
     * 	3) $objColumn	- objeto TGrideColum
     * 	4) $aData		- o array de dados da linha ex: $res[''][n]
     *   Ex: function tratarBotoes($rowNum,$button,$objColumn,$aData);
     *
     * Parametros do evento onGetAutocompleteParameters
     * 	1) $ac 			- classe TAutocomplete
     * 	2) $aData		- o array de dados da linha ex: $res[''][n]
     * 	3) $rowNum 		- número da linha corrente
     * 	3) $cell		- objeto TTableCell
     * 	4) $objColumn	- objeto TGrideColum
     *
     *
     * @param string $strName          - 1: ID da grid
     * @param string $strTitle         - 2: Titulo da grip
     * @param array $mixData           - 3: Array de dados. Pode ser form formato Adianti, FormDin ou PDO
     * @param mixed $strHeight         - 4: DEPRECATED Altura 
     * @param mixed $strWidth          - 5: DEPRECATED Largura
     * @param mixed $strKeyField       - 6: NOT_IMPLEMENTED Chave primaria
     * @param array $mixUpdateFields   - 7: NOT_IMPLEMENTED Campos do form origem que serão atualizados ao selecionar o item desejado. Separados por virgulas seguindo o padrão <campo_tabela> | <campo_formulario> , <campo_tabela> | <campo_formulario>
     * @param mixed $intMaxRows        - 8: NOT_IMPLEMENTED Qtd Max de linhas
     * @param mixed $strRequestUrl     - 9: NOT_IMPLEMENTED Url request do form
     * @param mixed $strOnDrawCell     -10: NOT_IMPLEMENTED
     * @param mixed $strOnDrawRow      -11: NOT_IMPLEMENTED
     * @param mixed $strOnDrawHeaderCell   -12: NOT_IMPLEMENTED
     * @param mixed $strOnDrawActionButton -13: NOT_IMPLEMENTED
     * @return TGrid
     */     
    public function __construct( $strName
                               , string $strTitle = null
                               , $mixData = null
                               , $strHeight = null
                               , $strWidth = null
                               , string $strKeyField = null
                               , $mixUpdateFields = null
                               , $intMaxRows = null
                               , $strRequestUrl = null
                               , $strOnDrawCell = null
                               , $strOnDrawRow = null
                               , $strOnDrawHeaderCell = null
                               , $strOnDrawActionButton = null )
    {
        $this->validateDeprecated($strHeight,$strWidth);

        $bootgrid = new BootstrapDatagridWrapper(new TDataGrid);
        $bootgrid->width = '100%';
        $this->setAdiantiObj($bootgrid);
        $this->setId($strName);
        $panel = new TPanelGroup($strTitle);
        $this->setPanelGroupGrid($panel);
    }

    public function validateDeprecated($strHeigh,$strWidth)
    {
        ValidateHelper::validadeParam('strHeigh',$strHeigh
                                     ,ValidateHelper::WARNING
                                     ,ValidateHelper::MSG_DECREP
                                     ,__CLASS__,__METHOD__,__LINE__);

        ValidateHelper::validadeParam('strWidth',$strWidth
                                     ,ValidateHelper::WARNING
                                     ,ValidateHelper::MSG_DECREP
                                     ,__CLASS__,__METHOD__,__LINE__);                                     
    }

    public function setAdiantiObj( $bootgrid )
    {
        if( !($bootgrid instanceof BootstrapDatagridWrapper) ){
            throw new InvalidArgumentException(TFormDinMessage::ERROR_FD5_OBJ_BOOTGRID);
        }
        $this->adiantiObj = $bootgrid;
    }

    public function getAdiantiObj(){
        //$title = $this->getTitle();
        //$panel = new TPanelGroup($title);
        //$panel->add( $this->adiantiObj );
        return $this->adiantiObj;
    }

    public function show()
    {
        $this->getAdiantiObj()->createModel();
        $this->getPanelGroupGrid()->add($this->getAdiantiObj())->style = 'overflow-x:auto';
        return $this->getAdiantiObj();
    }

    public function getAction(){
        return $this->action;
    }

    public function setAction($action){
        $this->action = $action;
    }

    public function getId(){
        return $this->idGrid;
    }

    public function setId(string $idGrid){
        if(empty($idGrid)){
            throw new InvalidArgumentException(TFormDinMessage::ERROR_EMPTY_INPUT);
        }
        $this->getAdiantiObj()->setId($idGrid);
        $this->idGrid = $idGrid;
    }

    public function getTitle(){
        return $this->title;
    }

    public function setTitle(string $title){
        $this->getPanelGroupGrid()->setTitle($title);
        $this->title = $title;
    }

    public function getKey(){
        return $this->key;
    }

    public function setKey(string $key){
        $this->key = $key;
    }

    public function getPanelGroupGrid(){
        return $this->panelGroupGrid;
    }

    public function setPanelGroupGrid($panel){
        if( !($panel instanceof TPanelGroup) ){
            throw new InvalidArgumentException(TFormDinMessage::ERROR_OBJ_TYPE_WRONG.' use TPanelGroup');
        }
        $this->panelGroupGrid = $panel;
    }

    public function getFooter(){
        return $this->getPanelGroupGrid()->getFooter();
    }

    public function addFooter($footer){
        return $this->getPanelGroupGrid()->addFooter($footer);
    }

    public function enableDataTable(){
        $this->getAdiantiObj()->datatable = 'true';
    }

    public function disableDataTable(){
        $this->getAdiantiObj()->datatable = 'false';
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

    //---------------------------------------------------------------------------------------
    /**
     * coluna tipo checkbox. Irá criar no gride uma coluno do tipo checkbox. Quando é feito o POST
     * será criado uma nova variavel com valor de strName
     *
     *
     * @param string $strName       - Nome do variavel no POST
     * @param string $strTitle      - Titulo que aparece no grid
     * @param string $strKeyField   - Valor que será passado no POST
     * @param string $strDescField  - Descrição do campo, valor que irá aparecer o gride
     * @param boolean $boolReadOnly
     * @param boolean $boolAllowCheckAll  - TRUE = pode selecionar todos , FALSE = não permite multiplas seleções
     * @return TGridCheckColumn
     */
    public function addCheckColumn( $strName
                                , $strTitle = null
                                , $strKeyField
                                , $strDescField = null
                                , $boolReadOnly = null
                                , $boolAllowCheckAll = null )
    {
        if ( !$strKeyField )
        {
            $strKeyField = strtoupper( $strName );
        }
        $this->getAdiantiObj()->disableDefaultClick(); //IMPORTANTE DESATIVAR
        $col = new TGridCheckColumn( $strName, $strTitle, $strKeyField, $strDescField, $boolReadOnly, $boolAllowCheckAll );
        $this->columns[ strtolower( $strName )] = $col;
        return $col;
    }


    //------------------------------------------------------------------------------------
    /**
     * @deprecated mantido apenas para diminir o impacto na migração do FormDin 4 para FormDin 5 sobre Adianti 7.1
     * @return void
     */
    public function getWidth()
    {
        ValidateHelper::validadeParam('strWidth','deprecated'
                                    ,ValidateHelper::WARNING
                                    ,ValidateHelper::MSG_DECREP
                                    ,__CLASS__,__METHOD__,__LINE__);
    }

    /**
     * @deprecated mantido apenas para diminir o impacto na migração do FormDin 4 para FormDin 5 sobre Adianti 7.1
     * @return void
     */    
    public function setWidth( $strNewValue = null )
    {
        ValidateHelper::validadeParam('strWidth',$strNewValue
                                     ,ValidateHelper::WARNING
                                     ,ValidateHelper::MSG_DECREP
                                     ,__CLASS__,__METHOD__,__LINE__);
    }

    /**
     * @deprecated mantido apenas para diminir o impacto na migração do FormDin 4 para FormDin 5 sobre Adianti 7.1
     * @return void
     */
    public function getHeight()
    {
        ValidateHelper::validadeParam('strWidth','deprecated'
                                    ,ValidateHelper::WARNING
                                    ,ValidateHelper::MSG_DECREP
                                    ,__CLASS__,__METHOD__,__LINE__);
    }

    /**
     * @deprecated mantido apenas para diminir o impacto na migração do FormDin 4 para FormDin 5 sobre Adianti 7.1
     * @return void
     */    
    public function setHeight( $strNewValue = null )
    {
        ValidateHelper::validadeParam('strHeight',$strNewValue
                                     ,ValidateHelper::WARNING
                                     ,ValidateHelper::MSG_DECREP
                                     ,__CLASS__,__METHOD__,__LINE__);
    }
}