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
 * Classe para criação de Botões 
 * ------------------------------------------------------------------------
 * Esse é o FormDin 5, que é uma reconstrução do FormDin 4 Sobre o Adianti 7.X
 * os parâmetros do metodos foram marcados com:
 * 
 * NOT_IMPLEMENTED = Parâmetro não implementados, talvez funcione em 
 *                   verões futuras do FormDin. Não vai fazer nada
 * DEPRECATED = Parâmetro que não vai funcionar no Adianti e foi mantido
 *              para o impacto sobre as migrações. Vai gerar um Warning
 * FORMDIN5 = Parâmetro novo disponivel apenas na nova versão
 * ------------------------------------------------------------------------
 * 
 * @author Reinaldo A. Barrêto Junior
 */ 
class TFormDinCheckList {

    protected $objCheck;
    protected $objSearch;
    protected $label;

    /**
    * Adicionar botão no layout
    *
    * ------------------------------------------------------------------------
    * Esse é o FormDin 5, que é uma reconstrução do FormDin 4 Sobre o Adianti 7.X
    * os parâmetros do metodos foram marcados veja documentação da classe para
    * saber o que cada marca singinifica.
    * ------------------------------------------------------------------------
    *
    * Para que o botão fique alinhado na frente de um campo com labelAbove=true, basta
    * definir o parametro boolLabelAbove do botão para true tambem.
    *
    * @param object  $id                - 1 : FORMDIN5 Objeto do Form, é só informar $this
    * @param string  $label             - 2 : Label do Botão
    * @param boolean $boolRequired      - 3 : DEFAULT = false não obrigatório
    * @param boolean $boolEnableSearch  - 4 : DEFAULT = false não faz busca
    * @param int     $intHeight         - 5 : Altura 
    * @param boolean $makeScrollable    - 6 : DEFAULT = false lista todos, sem scroll
    * @return TButton|string|array
    */
    public function __construct($id
                              , $label
                              , $boolRequired=false
                              , $boolEnableSearch=false
                              , $intHeight=null
                              , $makeScrollable=null
                              )
    {
        $this->setObjCheck($id);
        $this->setAdiantiObj($adiantiObj);
        $this->setLabel($label);
        $this->setAction($strName);
        $this->setImage($strImage);
        return $this->getAdiantiObj();
    }

    public function setObjCheck($id)
    {
        $orderlist = new TCheckList($id);
        return $this->objCheck=$orderlist;
    }
    public function getObjCheck(){
        return $this->objCheck;
    }

    public function setRequired($boolRequired)
    {
        if($boolRequired==true){            
            $this->getObjCheck()->addValidation('Order list', new TRequiredValidator);
        }
    }
    public function getRequired(){
        return $this->objCheck;
    }

    public function setLabel($label)
    {
        if( is_array($label) ){
            $msg = 'O parametro $mixValue não recebe mais um array! Faça uma chamada por Action';
            ValidateHelper::migrarMensage($msg
                                         ,ValidateHelper::ERROR
                                         ,ValidateHelper::MSG_CHANGE
                                         ,__CLASS__,__METHOD__,__LINE__);
        }else{
            $this->label=$label;
            $this->getAdiantiObj()->setLabel($label);
        }
    }
    public function getLabel(){
        return $this->label;
    }

    public function setAdiantiObj($adiantiObj)
    {
        if( empty($adiantiObj) ){
            throw new InvalidArgumentException(TFormDinMessage::ERROR_FD5_OBJ_ADI);
        }
        if( !is_object($adiantiObj) ){
            $msg = 'o metodo addButton MUDOU! o primeiro parametro agora recebe $this! o Restando está igual ;-)';
            ValidateHelper::migrarMensage($msg
                                         ,ValidateHelper::ERROR
                                         ,ValidateHelper::MSG_CHANGE
                                         ,__CLASS__,__METHOD__,__LINE__);
        }
        return $this->adiantiObj=$adiantiObj;
    }
    public function getAdiantiObj(){
        return $this->adiantiObj;
    }

    public function setAction($strName)
    {
        if( empty($strName) ){
            throw new InvalidArgumentException(TFormDinMessage::ERROR_EMPTY_INPUT.': strName');
        }
        $action = null;
        if( is_array($strName) ){
            $action = new TAction(array($strName[0], $strName[1]));
        }else{
            $objForm = $this->getObjForm();
            $action = new TAction(array($objForm, $strName));
        }        
        $label = $this->getLabel();
        $this->getAdiantiObj()->setAction($action,$label);
    }

    public function setImage($strImage)
    {
        if( !empty($strImage) ){
            $this->getAdiantiObj()->setImage($strImage);
        }
    }

}
?>