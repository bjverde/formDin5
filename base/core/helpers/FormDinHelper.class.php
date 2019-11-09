<?php
/*
 * FormDin 5 Framework
 * Created by Reinaldo A. Barrêto Jr in 2019
 * Based on the FormDin 4 of Luís Eugênio Barbosa
 * https://github.com/bjverde/formDin5
 *
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
class FormDinHelper
{
    /**
     * Return FormDin version
     * @return string
     */
    public static function version()
    {
        return FORMDIN_VERSION;
    }
    /***
     * Returns if the current formDin version meets the minimum requirements
     * 
     * Retorna se a versão atual do formDin atende aos requisitos mínimos 
     * @param string $version
     * @return boolean
     */
    public static function versionMinimum($version)
    {
        $formVersion = explode("-", self::version());
        $formVersion = $formVersion[0];
        return version_compare($formVersion,$version,'>=');
    }
    
    //--------------------------------------------------------------------------------
    /**
     * Recebe o corpo de um request e um objeto VO. Quando o id do array bodyRequest for
     * igual ao nome de um atributo no objeto Vo esse deverá ser setado.
     *
     * @param array $bodyRequest - Array com corpo do request
     * @param object $vo - objeto VO para setar os valores
     * @return object
     */
    public static function setPropertyVo($bodyRequest,$vo)
    {
        $class = new \ReflectionClass($vo);
        $properties   = $class->getProperties();        
        foreach ($properties as $attribut) {
            $name =  $attribut->getName();
            if (array_key_exists($name, $bodyRequest)) {
                $reflection = new \ReflectionProperty(get_class($vo), $name);
                $reflection->setAccessible(true);
                $reflection->setValue($vo, $bodyRequest[$name]);
                //echo $bodyRequest[$name];
            }
        }
        return $vo;
    }
    //--------------------------------------------------------------------------------
    /***
     * Convert Object Vo to Array FormDin 
     * @param object $vo
     * @throws InvalidArgumentException
     * @return array
     */
    public static function convertVo2ArrayFormDin($vo)
    {
        $isObject = is_object( $vo );
        if( !$isObject ){
            throw new InvalidArgumentException('Not Object .class:'.__METHOD__);
        }
        $class = new \ReflectionClass($vo);
        $properties   = $class->getProperties();
        $arrayFormDin = array();
        foreach ($properties as $attribut) {
            $name =  $attribut->getName();
            $property = $class->getProperty($name);
            $property->setAccessible(true);
            $arrayFormDin[strtoupper($name)][0] = $property->getValue($vo);
        }
        return $arrayFormDin;
    }
    //--------------------------------------------------------------------------------
    /**
     * Validate Object Type is Instance Of TPDOConnectionObj
     *
     * @param object $tpdo instanceof TPDOConnectionObj
     * @param string $method __METHOD__
     * @return void
     */    
    public static function validateObjType($tpdo,$method)
    {
        if( empty($method) ){
            throw new InvalidArgumentException(TMessage::ERROR_EMPTY_INPUT.'class:'.__METHOD__);
        }        
        $typeObjWrong = !($tpdo instanceof TPDOConnectionObj);
        if( !is_null($tpdo) && $typeObjWrong ){
            throw new InvalidArgumentException('class:'.$method);
        }
    }
    /***
     * 
     * @param mixed $variable
     * @param boolean $testZero
     * @return boolean
     */
    public static function issetOrNotZero($variable,$testZero=true)
    {
        $result = false;
        if( is_array($variable) ){
            if( !empty($variable) ) {
                $result = true;
            }
        }else{
            if(isset($variable) && !($variable==='') ) {
                if($testZero) {
                    if($variable<>'0' ) {
                        $result = true;
                    }
                }else{
                    $result = true;
                }
            }
        }
        return $result;
    }
    

    public static function debug( $mixExpression,$strComentario='Debug', $boolExit=FALSE, $showTrace = false ) {
        if (defined('DEBUGAR') && !DEBUGAR){
            return;
        }
        $arrBacktrace = debug_backtrace();
        if( isset($_REQUEST['ajax']) && $_REQUEST['ajax'] ){
            echo '<pre>';
            foreach ( $arrBacktrace[0] as $strAttribute => $mixValue ){
                if ( !is_array($mixValue) ){
                    echo $strAttribute .'='. $mixValue ."\n";
                }
            }
            echo "---------------\n";
               print_r( $mixExpression );
               echo '</pre>';
    
        } else {
            echo "<script>try{fwUnblockUI();}catch(e){try{top.app_unblockUI();}catch(e){}}</script>";
            echo '<fieldset class="fwDebugArea">';
            echo '<legend  class="fwDebugHeader">'.$strComentario.'</legend>';
            echo '<pre>';
            foreach ( $arrBacktrace[0] as $strAttribute => $mixValue ) {
                if( is_string( $mixValue ) ) {
                    echo "<b>" . $strAttribute . "</b> ". $mixValue ."\n";
                }else{
                    if($strAttribute != 'args'){                        
                        echo "<b>" . $strAttribute . "</b> ". strval($mixValue) ."\n";
                    }
                }
            }
            echo '</pre>';
            echo '<hr>';
            echo '<legend  class="fwDebugHeader">Trace</legend>';
            foreach ( $arrBacktrace as $key => $step ) {
                echo '<br>'.$step['file'].' '.strval($step['line']);
            }
            echo '<hr>';
            
            echo '<legend  class="fwDebugBody">'.$strComentario.'</legend>'."\n";
            ini_set('xdebug.var_display_max_depth', -1);
            ini_set('xdebug.var_display_max_children', -1);
            ini_set('xdebug.var_display_max_data', -1);
            echo '<pre>';
            if( is_object($mixExpression) ) {
                var_dump( $mixExpression );
            } else {
                var_dump( $mixExpression );
            }
            echo '</pre>';
            echo '</fieldset>';

            if ( $boolExit ) {
                echo '<br /><font color="#700000" size="4"><b>DIE!!!!</b></font>';
                exit();
            }
        }
    }

}
?>
