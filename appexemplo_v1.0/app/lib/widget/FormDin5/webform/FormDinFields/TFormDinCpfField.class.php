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

class TFormDinCpfField extends TFormDinMaskField
{
    protected $adiantiObj;
    
    /**
     * Campo de entrada de dados do tipo CPF
     * Reconstruido FormDin 4 Sobre o Adianti 7
     *
     * @param string  $id             - 1: id do campo
     * @param string  $strLabel       - 2: Rotulo do campo que irá aparece na tela
     * @param boolean $boolRequired   - 3: Obrigatorio
     * @param string  $strMask        - 4: A mascara
     * @param boolean $boolNewLine    - 5: NOT_IMPLEMENTED Nova linha
     * @param string  $strValue       - 6: Valor inicial do campo
     * @param boolean $boolLabelAbove - 7: NOT_IMPLEMENTED Label sobre o campo. Default FALSE = Label mesma linha, TRUE = Label acima
     * @param boolean $boolNoWrapLabel- 8: NOT_IMPLEMENTED true ou false para quebrar ou não o valor do label se não couber na coluna do formulario
     * @param string  $strExampleText - 9: PlaceHolder é um Texto de exemplo
     * @param boolean $boolSendMask  - 10: Se as mascara deve ser enviada ou não para o post. DEFAULT = False.
     * @return void
     */
    public function __construct( $id
                               , $label=null
                               , $boolRequired=false
                               , $strMask=null
                               , $boolNewLine=null
                               , $value=null
                               , $boolLabelAbove=null
                               , $boolNoWrapLabel=null
                               , $placeholder=null
                               , $boolSendMask=false )
    {
        $this->adiantiObj = new TEntry($id);
        $this->adiantiObj->setId($id);
        $this->adiantiObj->addValidation($strLabel, new TCNPJValidator);
        $this->adiantiObj->setMask('99.999.999/9999-99', $boolSendMask);
        if($boolRequired){
            $strLabel = empty($strLabel)?$id:$strLabel;
            $this->adiantiObj->addValidation($strLabel, new TRequiredValidator);
        }
        if(!empty($strExampleText)){
            $this->adiantiObj->placeholder = $strExampleText;
        } 
        return $this->getAdiantiObj();
    }

    public function getAdiantiObj(){
        return $this->adiantiObj;
    }
}