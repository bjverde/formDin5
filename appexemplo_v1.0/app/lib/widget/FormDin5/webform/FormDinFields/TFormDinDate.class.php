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
 * Classe para criação campo do tipo TFormDinDate
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
class TFormDinDate extends TFormDinGenericField
{
    protected $adiantiObj;
    
    /****
     * Adicona um campo data ou mes/ano ou dia/mes de acordo com o parametro strMaxType
     * Tipo de máscara: DMY, DM, MY
     *  
     * @param string  $strName         - 1: Id do Campo
     * @param string  $strLabel        - 2: Label do Campo
     * @param boolean $boolRequired    - 3: DEFAULT = flase não obrigatório
     * @param boolean $boolNewLine     - 4: Default TRUE = campo em nova linha, FALSE continua na linha anterior
     * @param string  $strValue        - 5: Valor inicial
     * @param string  $strMinValue     - 6: Menor data que o campo aceita
     * @param string  $strMaxValue     - 7: Maior data que o campo aceita
     * @param string  $strMaskType     - 8: DEFAULT = DMY. Tipo de Mascara DMY (dia/mês/ano), DM (dia/mês), MY (mês/ano) 
     * @param boolean $boolButtonVisible - 9: Exibe ou não o botão do calendario.
     * @param string  $strExampleText  - 10: Texto de exmplo
     * @param boolean $boolLabelAbove  - 11: DEFAULT = flase. Label acima do campo = true
     * @param string  $boolNoWrapLabel - 12: NOT_IMPLEMENTED
     * @return TDate
     */
    public function __construct(string $id
                              , string $label=null
                              , $boolRequired=false
                              , $boolNewLine=null
                              , $strValue=null
                              , $strMinValue=null
                              , $strMaxValue=null
                              , $strMaskType=null
                              , $boolButtonVisible=null
                              , $strExampleText=null
                              , $boolLabelAbove=null
                              , $boolNoWrapLabel=null
                              )
    {
        $adiantiObj = new TDate($id);
        parent::__construct($adiantiObj,$id,$label,$boolRequired,null,null);       
        return $this->getAdiantiObj();
    }
}