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
 * Este programa é distribuí1do na esperança que possa ser útil, mas SEM NENHUMA
 * GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer MERCADO ou
 * APLICAÇÃO EM PARTICULAR. Veja a Licen?a Pública Geral GNU/LGPL em portugu?s
 * para maiores detalhes.
 *
 * Você deve ter recebido uma cópia da GNU LGPL versão 3, sob o título
 * "LICENCA.txt", junto com esse programa. Se não, acesse <http://www.gnu.org/licenses/>
 * ou escreva para a Fundação do Software Livre (FSF) Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA 02111-1301, USA.
 */

/**
 * Classe para criação campo texto simples
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
class TFormDinMemoField extends TFormDinGenericField
{
    /**
     * Adicionar campo de entrada de texto com multiplas linhas ( memo ) equivalente ao html textarea
     * ------------------------------------------------------------------------
     * Esse é o FormDin 5, que é uma reconstrução do FormDin 4 Sobre o Adianti 7.X
     * os parâmetros do metodos foram marcados veja documentação da classe para
     * saber o que cada marca singinifica.
     * ------------------------------------------------------------------------
     *
     * @param string  $strName         - 1: ID do campo
     * @param string  $strLabel        - 2: Label
     * @param integer $intMaxLength    - 3: Tamanho maximos
     * @param boolean $boolRequired    - 4: Obrigatorio
     * @param integer $intColumns      - 5: Qtd colunas
     * @param integer $intRows         - 6: Qtd linhas
     * @param boolean $boolNewLine     - 7: NOT_IMPLEMENTED nova linha
     * @param boolean $boolLabelAbove  - 8: NOT_IMPLEMENTED Label sobre o campo
     * @param boolean $boolShowCounter - 9: NOT_IMPLEMENTED Contador de caracteres ! Só funciona em campos não RichText
     * @param string  $strValue       - 10: texto preenchido
     * @param string $boolNoWrapLabel - 11: NOT_IMPLEMENTED
     * @return TFormDinMemoField
     */
    public function __construct($id
                              , $label=null
                              , $intMaxLength
                              , $boolRequired=null
                              , $intColumns=null
                              , $intRows=null
                              , $boolNewLine=null
                              , $boolLabelAbove=null
                              , $boolShowCounter=null
                              , $value=null
                              , $boolNoWrapLabel=null
                              , $placeholder=null)
    {
        $adiantiObj = new TText($id);
        parent::__construct($adiantiObj,$id,$label,$boolRequired,$value,$placeholder);
        $this->setMaxLength($label,$intMaxLength);
        $this->setSize($intColumns, $intRows);
        return $this->getAdiantiObj();
    }

    public function setMaxLength($label,$intMaxLength){
        if($intMaxLength>=1){
            $this->getAdiantiObj()->addValidation($label, new TMaxLengthValidator, array($intMaxLength));
        }
    }

    public function setSize($intColumns, $intRows){
        $intRows = $intRows * 10;
        $intColumns = $intColumns * 3;
        $this->getAdiantiObj()->setSize($intColumns, $intRows);
    }
}