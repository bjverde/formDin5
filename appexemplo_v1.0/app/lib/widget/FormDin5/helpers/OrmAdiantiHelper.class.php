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
     * @param mixed  $param       05: valor do $param que será testado
     * @param string $sql         06: String Sql para um sub select.
     * @return array
     */
    public static function addFilter($arrayFilter,$filde,$conector,$data,$param,$sql) 
    {
        $data = empty($data)?$param:$data;
        if( self::valueTest($data) ){
            if( empty($sql) ){
                $arrayFilter[] = new TFilter($filde,$conector,$data);// create the filter 
            }else{
                $arrayFilter[] = new TFilter($filde,$conector,$sql);// create the filter 
            }
        }
    	return $arrayFilter;
    }    
}
?>