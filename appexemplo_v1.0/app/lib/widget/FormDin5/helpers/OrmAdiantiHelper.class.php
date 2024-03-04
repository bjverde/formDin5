<?php
/*
 * ----------------------------------------------------------------------------
 * Formdin 5 Framework
 * SourceCode https://github.com/bjverde/formDin5
 * @author Reinaldo A. Barrêto Junior
 * 
 * É uma reconstrução do FormDin 4 Sobre o Adianti 7.X
 * @author Luís Eugênio Barbosa do FormDin 4
 * 
 * Adianti Framework é uma criação Adianti Solutions Ltd
 * @author Pablo Dall'Oglio
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

class OrmAdiantiHelper
{

    /**
     * Recurpera o valor de um atributo de um Objeto
     *
     * @param object $obj - 01: Objeto Adianti
     * @param string $objPropertyName - 02: Nome da atributo do objeto
     * @return object
     */
    public static function objPropertyValue($obj,$objPropertyName)
    {
        ValidateHelper::isObject($obj,__METHOD__,__LINE__);
        ValidateHelper::isString($objPropertyName,__METHOD__,__LINE__);
        $result = null;
        if (property_exists($obj,$objPropertyName)) {
            $result = $obj->{$objPropertyName};
        }
    	return $result;
    }


    /**
     * Verifica se uma propriedade foi setada em objeto, se não foi setada vai
     * setar NULL ou valor informado do array. Retornando um objto com valor
     * peenchido
     *
     * @param object $obj - 01: Objeto Adianti
     * @param string|null $objPropertyName - 02: Nome da atributo do objeto
     * @param array|null|mixed $arrayParam - 03: Array ou valor diretamente com possíveis valores
     * @param string|null $arrayParamName  - 04: Nome do atributo do array que será usado para preencher o valor do objeto
     * @return object
     */
    public static function objPropertyExistsSetValue($obj=null,$objPropertyName=null,$arrayParam=null,$arrayParamName=null)
    {        
        if( !is_object($obj)){
            $obj = new stdClass();
        }
        $newValue = null;
        if( is_array($arrayParam) ){
            ValidateHelper::isString($arrayParamName,__METHOD__,__LINE__);
            $newValue = ArrayHelper::get($arrayParam,$arrayParamName);
        }else{
            $newValue = $arrayParam;
        }

        if( !empty($objPropertyName) ){
            ValidateHelper::isString($objPropertyName,__METHOD__,__LINE__);
            if (property_exists($obj,$objPropertyName)) {
                $obj->{$objPropertyName} = empty($obj->{$objPropertyName})?$newValue:$obj->{$objPropertyName};
            }else{
                $obj->{$objPropertyName} = $newValue;
            }
        }
    	return $obj;
    }

    /**
     * Verificar se o valor informado foi preenchido
     *
     * @param mixed $param
     * @return void
     */
    public static function valueTest($param) 
    {
        $result = false;
        if (isset($param) AND ( (is_scalar($param) AND $param !== '') OR (is_array($param) AND (!empty($param)) )) )
        {
            $result = true;// create the filter 
        }
    	return $result;
    }

    /**
     * Se $data ou $param tiver valor, inclui um novo elemento do tipo TFilter
     * no ArrayFilter para ser usado como critério de filtro no sql
     *
     * @param array  $arrayFilter 01: array com os filtros já incluiso
     * @param string $filde       02: campo do banco que será usado
     * @param string $conector    03: conectores SQL: like, =, !=, in, not in, >=, <=, >, <
     * @param mixed  $data        04: valor do $data que será testado
     * @param object $obj         05: Objeto Adianti
     * @param string|null $objPropertyName 06: Nome da atributo do objeto
     * @param array|null|mixed $arrayParam 07: Array ou valor diretamente com possíveis valores
     * @param string|null $arrayParamName  08: Nome do atributo do array que será usado para preencher o valor do objeto
     * @param string $sql         09: String Sql para um sub select.
     * @return array
     */
    public static function addFilter($arrayFilter,$filde,$conector,$obj=null,$objPropertyName=null,$arrayParam=null,$arrayParamName=null,$sql=null) 
    {
        if( is_object($obj) ){
            $obj   = self::objPropertyExistsSetValue($obj,$objPropertyName,$arrayParam,$arrayParamName);
            $value = self::objPropertyValue($obj,$objPropertyName);
        }else{
            $value = ArrayHelper::get($arrayParam,$arrayParamName);
        }
        if( self::valueTest($value) ){
            if( empty($sql) ){
                $arrayFilter[] = new TFilter($filde,$conector,$value);// create the filter 
            }else{
                $arrayFilter[] = new TFilter($filde,$conector,$sql);// create the filter 
            }
        }
    	return $arrayFilter;
    }    
}
?>