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

require_once  __DIR__.'/../../mockFormDinArray.php';

use PHPUnit\Framework\TestCase;

/**
 * ArrayHelper test case.
 */
class ArrayHelperTest extends TestCase
{

    public function testValidateUndefined_temValor() {
        $index = 'key';
        $valor = 1500;
        $esperado = $valor;
        $arrayTest = array();
        $arrayTest[$index] = $valor;
        $retorno = ArrayHelper::validateUndefined($arrayTest,$index);        
        $this->assertEquals($esperado, $retorno);
    }
    
    public function testValidateUndefined() {
        $esperado = '';
        $arrayTest = array();
        $retorno = ArrayHelper::validateUndefined($arrayTest,'indexNotExist');
        $this->assertEquals($esperado, $retorno);
    }    
    
    public function testAsArrayNotEmpty_falseString() {
    	$esperado = FALSE;
    	$retorno = ArrayHelper::isArrayNotEmpty('x');
    	$this->assertEquals($esperado, $retorno);
    }

    public function testAsArrayNotEmpty_falseNull() {
    	$esperado = FALSE;
    	$retorno = ArrayHelper::isArrayNotEmpty(null);
    	$this->assertEquals($esperado, $retorno);
    }

    public function testAsArrayNotEmpty_falseArrayEmpty() {
    	$esperado = FALSE;
    	$retorno = ArrayHelper::isArrayNotEmpty(array());
    	$this->assertEquals($esperado, $retorno);
    }

    public function testAsArrayNotEmpty_true() {
    	$esperado = true;
    	$retorno = ArrayHelper::isArrayNotEmpty(array('xx'=>'aa'));
    	$this->assertEquals($esperado, $retorno);
    }

    public function testHas_notArray() {
    	$esperado = FALSE;
    	$arrayTest = null;
    	$retorno = ArrayHelper::has('x',$arrayTest);
    	$this->assertEquals($esperado, $retorno);
    }    
   
    public function testHas_notInArray() {
    	$esperado  = FALSE;
    	$arrayTest = array("foo" => "bar","bar" => "foo",100=> -100,-100=> 100);
    	$retorno = ArrayHelper::has('x',$arrayTest);
    	$this->assertEquals($esperado, $retorno);
    }
    
    public function testHas_InArray() {
    	$esperado  = TRUE;
    	$arrayTest = array("foo" => "bar","x" => "foo",100=> -100,-100=> 100);
    	$retorno = ArrayHelper::has('x',$arrayTest);
    	$this->assertEquals($esperado, $retorno);
    }    
    
    public function testArray_keys2_true() {
        $esperado  = array(0=>'x');
        $arrayTest = array("foo" => "bar","x" => "foo",100=> -100,-100=> 100);
        $retorno = ArrayHelper::array_keys2($arrayTest,'foo');
        $this->assertEquals($esperado, $retorno);
    }
    
    public function testArray_keys2_position2() {
        $esperado  = array(0=>2);
        $arrayTest = array(0 => 'idTest',1=>'nm_test',2=>'tp_test');
        $retorno = ArrayHelper::array_keys2($arrayTest,'tp_test');
        $this->assertEquals($esperado, $retorno);
    }
    
    public function testArray_keys2_position1() {
        $esperado  = array(0=>1);
        $arrayTest = array(0 => 'idTest',1=>'nm_test',2=>'tp_test');
        $retorno = ArrayHelper::array_keys2($arrayTest,'nm_test');
        $this->assertEquals($esperado, $retorno);
    }
    
    public function testArray_keys2_position0() {
        $esperado  = array(0=>0);
        $arrayTest = array(0 => 'idTest',1=>'nm_test',2=>'tp_test');
        $retorno = ArrayHelper::array_keys2($arrayTest,'idTest');
        $this->assertEquals($esperado, $retorno);
    }
    
    public function testArray_keys2_false() {
        $esperado  = array();
        $arrayTest = array("foo" => "bar","x" => "foo",100=> -100,-100=> 100);
        $retorno = ArrayHelper::array_keys2($arrayTest,'xxx');
        $this->assertEquals($esperado, $retorno);
    }
    
    public function testGetDefaultValue_NotInArray() {
    	$esperado  = 'x';
    	$array = array();
    	$array ['y'] = 123;
    	$atributeName = 'k';
    	$DefaultValue = 'x';    	
    	$retorno = ArrayHelper::getDefaultValue($array,$atributeName,$DefaultValue);
    	$this->assertEquals($esperado, $retorno);
    }
    
    public function testGetDefaultVALUE_NotArray() {
    	$esperado  = 'x';
    	$array = 123;
    	$atributeName = 'k';
    	$DefaultValue = 'x';
    	$retorno = ArrayHelper::getDefaultValue($array,$atributeName,$DefaultValue);
    	$this->assertEquals($esperado, $retorno);
    }
    
    public function testGetDefaultValue_InArray() {
    	$esperado  = 123;
    	$array = array();
    	$array ['y'] = 123;
    	$atributeName = 'y';
    	$DefaultValue = 'x';
    	$retorno = ArrayHelper::getDefaultValue($array,$atributeName,$DefaultValue);
    	$this->assertEquals($esperado, $retorno);
    }    
    
    public function testGetArray_true() {
        $mock = new mockFormDinArray();        
        $esperado  = array(0=>'Joao Silva', 1=>'Maria Laranja', 2=>'Dell', 3=>'Microsoft');
        $array = $mock->generateTable();
        $atributeName = 'NMPESSOA';
        $retorno = ArrayHelper::getArray($array,$atributeName);
        $this->assertEquals($esperado, $retorno);
    }
    
    public function testGetArray_false() {
        $mock = new mockFormDinArray();
        $esperado  = array();
        $array = $mock->generateTable();
        $atributeName = 'TIA';
        $retorno = ArrayHelper::getArray($array,$atributeName);
        $this->assertEquals($esperado, $retorno);
    }
    
    public function testArrayFormKey_true() {
        $mock = new mockFormDinArray();
        $esperado  = 'Maria Laranja';
        $array = $mock->generateTable();
        $atributeName = 'NMPESSOA';
        $key = 1;
        $retorno = ArrayHelper::formDinGetValue($array,$atributeName,$key);
        $this->assertEquals($esperado, $retorno);
    }
    
    public function testArrayFormKey_AtributeNameNotFound() {
        $mock = new mockFormDinArray();
        $esperado  = null;
        $array = $mock->generateTable();
        $atributeName = 'TIA';
        $key = 1;
        $retorno = ArrayHelper::formDinGetValue($array,$atributeName,$key);
        $this->assertEquals($esperado, $retorno);
    }
    //-----------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------
    public function testGetType_FailWrongTypeNull(){
        $this->expectException(InvalidArgumentException::class);
        $input = null;
        ArrayHelper::getType($input);
    }
    public function testGetType_FailAnyString(){
        $this->expectException(InvalidArgumentException::class);
        $input = 'any string';
        ArrayHelper::getType($input);
    }
    public function testGetType_FailInt(){
        $this->expectException(InvalidArgumentException::class);
        $input = 10;
        ArrayHelper::getStringType($input);
    }
    public function testGetType_STRING_GRID(){
        $input = 'KEY|VALUE,KEY|VALUE';
        $retorno = ArrayHelper::getType($input);
        $this->assertEquals(ArrayHelper::TYPE_FORMDIN_STRING_GRID_ACTION, $retorno);
    }
    public function testGetType_STRING(){
        $input = 'KEY=VALUE,KEY=VALUE';
        $retorno = ArrayHelper::getType($input);
        $this->assertEquals(ArrayHelper::TYPE_FORMDIN_STRING, $retorno);
    }
    public function testGetType_ADIANTI(){
        $mock = new mockFormDinArray();
        $input = $mock->generateTablePessoaAdianti();;
        $retorno = ArrayHelper::getType($input);
        $this->assertEquals(ArrayHelper::TYPE_ADIANTI, $retorno);
    }
    public function testGetType_PDO(){
        $mock = new mockFormDinArray();
        $input = $mock->generateTablePessoaPDO();;
        $retorno = ArrayHelper::getType($input);
        $this->assertEquals(ArrayHelper::TYPE_PDO, $retorno);
    }
    public function testGetType_FormDin(){
        $mock = new mockFormDinArray();
        $input = $mock->generateTable();
        $retorno = ArrayHelper::getType($input);
        $this->assertEquals(ArrayHelper::TYPE_FORMDIN, $retorno);
    }    
    //-----------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------
    public function testGetStringType_FailWrongTypeNull(){
        $this->expectException(InvalidArgumentException::class);
        $array = null;
        ArrayHelper::getStringType($array);
    }
    public function testGetStringType_FailAnyString(){
        $this->expectException(InvalidArgumentException::class);
        $input = 'any string';
        ArrayHelper::getStringType($input);
    }
    public function testGetStringType_FailInt(){
        $this->expectException(InvalidArgumentException::class);
        $input = 10;
        ArrayHelper::getStringType($input);
    }
    public function testGetStringType_FailArray(){
        $this->expectException(InvalidArgumentException::class);
        $input = array(1=>'vai');
        ArrayHelper::getStringType($input);
    }
    public function testGetStringType_STRING_GRID(){
        $input = 'KEY|VALUE,KEY|VALUE';
        $retorno = ArrayHelper::getStringType($input);
        $this->assertEquals(ArrayHelper::TYPE_FORMDIN_STRING_GRID_ACTION, $retorno);
    }
    public function testGetStringType_STRING(){
        $input = 'KEY=VALUE,KEY=VALUE';
        $retorno = ArrayHelper::getStringType($input);
        $this->assertEquals(ArrayHelper::TYPE_FORMDIN_STRING, $retorno);
    }    
    //-----------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------
    public function testGetArrayType_FailWrongTypeNull(){
        $this->expectException(TypeError::class);
        $array = null;
        ArrayHelper::getArrayType($array);
    }
    public function testGetArrayType_FailWrongTypeString(){
        $this->expectException(TypeError::class);
        $array = 'xxx';
        ArrayHelper::getArrayType($array);
    }
    public function testGetArrayType_FailWrongTypeObjetc(){
        $this->expectException(TypeError::class);
        $array = new stdClass();
        ArrayHelper::getArrayType($array);
    }
    public function testGetArrayType_arrayNull(){
        $array = array();
        $retorno = ArrayHelper::getArrayType($array);
        $esperado = ArrayHelper::TYPE_ADIANTI;
        $this->assertEquals($esperado, $retorno);
    }
    public function testGetArrayType_FormDin(){
        $mock = new mockFormDinArray();
        $array = $mock->generateTable();
        $esperado = ArrayHelper::TYPE_FORMDIN;

        $retorno = ArrayHelper::getArrayType($array);
        $this->assertEquals($esperado, $retorno);
    }
    public function testGetArrayType_PDO(){
        $mock = new mockFormDinArray();
        $array = $mock->generateTablePessoaPDO();
        $esperado = ArrayHelper::TYPE_PDO;

        $retorno = ArrayHelper::getArrayType($array);
        $this->assertEquals($esperado, $retorno);
    }
    public function testGetArrayType_PHP(){
        $array = array();
        $array[0]='Maria';
        $array[1]='Caio';
        $array[2]='Ana';
        $array[3]='Ruy';
        $esperado = ArrayHelper::TYPE_PHP;

        $retorno = ArrayHelper::getArrayType($array);
        $this->assertEquals($esperado, $retorno);
    }    
    public function testGetArrayType_Adianti(){
        $mock = new mockFormDinArray();
        $array = $mock->generateTablePessoaAdianti();
        $esperado = ArrayHelper::TYPE_ADIANTI;

        $retorno = ArrayHelper::getArrayType($array);
        $this->assertEquals($esperado, $retorno);
    }
    //-----------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------
    public function testArrayType_FailString(){
        $this->expectException(TypeError::class);
        $array = 'any string';

        ArrayHelper::arrayTypeIsAdiantiGrid($array);
    }
    public function testArrayType_FailObj(){
        $this->expectException(TypeError::class);
        $array = new stdClass();

        ArrayHelper::arrayTypeIsAdiantiGrid($array);
    }    
    public function testArrayType_FormDin(){
        $mock = new mockFormDinArray();
        $array = $mock->generateTable();

        $retorno = ArrayHelper::arrayTypeIsAdiantiGrid($array);
        $this->assertEquals(false, $retorno);
    }
    public function testArrayType_FalsePDO(){
        $mock = new mockFormDinArray();
        $array = $mock->generateTablePessoaPDO();

        $retorno = ArrayHelper::arrayTypeIsAdiantiGrid($array);
        $this->assertEquals(false, $retorno);
    }    
    public function testArrayType_FalseAdianti(){
        $mock = new mockFormDinArray();
        $array = $mock->generateTablePessoaAdianti();

        $retorno = ArrayHelper::arrayTypeIsAdiantiGrid($array);
        $this->assertEquals(false, $retorno);
    }
    public function testArrayType_FalseArrayPHP(){
        $array = ['code'=>'code','nome'=>'nome'];

        $retorno = ArrayHelper::arrayTypeIsAdiantiGrid($array);
        $this->assertEquals(false, $retorno);
    }
    public function testArrayType_True(){
        $array = ['code'=>'{code}','nome'=>'{nome}'];
        
        $retorno = ArrayHelper::arrayTypeIsAdiantiGrid($array);
        $this->assertEquals(true, $retorno);
    }    
    //-----------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------
    public function testConvertArray2Adianti_FormDin2Adianti(){
        $mock = new mockFormDinArray();
        $array = $mock->generateTablePessoaAdianti();
        $esperado = $mock->generateTablePessoaAdianti();

        $retorno = ArrayHelper::convertArray2Adianti($array);
        $this->assertEquals($esperado, $retorno);
    }
    public function testConvertArray2Adianti_Pdo2Adianti(){
        $mock = new mockFormDinArray();
        $array = $mock->generateTablePessoaPDO();
        $esperado = $mock->generateTablePessoaAdianti();

        $retorno = ArrayHelper::convertArray2Adianti($array);
        $this->assertEquals($esperado, $retorno);
    }
    public function testConvertArray2Adianti_Adianti2Adianti(){
        $mock = new mockFormDinArray();
        $array = $mock->generateTablePessoaAdianti();
        $esperado = $mock->generateTablePessoaAdianti();

        $retorno = ArrayHelper::convertArray2Adianti($array);
        $this->assertEquals($esperado, $retorno);
    }    
    //-----------------------------------------------------------------------------------
    public function testConvertArrayPdo2FormDin_Upcase() {
        $mock = new mockFormDinArray();
        $esperado  = $mock->generateTable();
        $array = $mock->generateTablePessoaPDO();
        $retorno = ArrayHelper::convertArrayPdo2FormDin($array,PDO::CASE_UPPER);
        $this->assertEquals($esperado, $retorno);
    }
        
    public function testConvertArrayFormDin2Pdo_Upcase() {
        $mock = new mockFormDinArray();
        $esperado  = $mock->generateTablePessoaPDO();
        $array = $mock->generateTable();
        $retorno = ArrayHelper::convertArrayFormDin2Pdo($array,PDO::CASE_UPPER);
        $this->assertEquals($esperado, $retorno);
    }

    public function testConvertArrayFormDin2Pdo_lower() {
        $mock = new mockFormDinArray();
        $array = $mock->generateTable();
        $retorno = ArrayHelper::convertArrayFormDin2Pdo($array,PDO::CASE_LOWER);
        $this->assertEquals('Joao Silva', $retorno[0]['nmpessoa']);
    }

    public function testConvertArrayFormDin2Adianti_NoChangeCase() {
        $mock = new mockFormDinArray();
        $esperado  = $mock->generateTablePessoaAdianti();
        $array = $mock->generateTable();
        $retorno = ArrayHelper::convertArrayFormDin2Adianti($array);
        $this->assertEquals($esperado, $retorno);
    }

    public function testConvertArrayPDOAdianti() {
        $mock = new mockFormDinArray();
        $esperado  = $mock->generateTablePessoaAdianti();
        $array = $mock->generateTablePessoaPDO();
        $retorno = ArrayHelper::convertArrayPDO2Adianti($array);
        $this->assertEquals($esperado, $retorno);
    }
    public function testConvertArrayPDOAdianti_lower() {
        $mock = new mockFormDinArray();
        $array = $mock->generateTablePessoaPDO();    
        $retorno = ArrayHelper::convertArrayPDO2Adianti($array,PDO::CASE_LOWER);
        $obj0 = $retorno[0];
        $this->assertEquals(1,  $obj0->idpessoa);
        $this->assertEquals('Joao Silva',  $obj0->nmpessoa);
    }
    public function testConvertArrayPDOAdianti_upper() {
        $dadosPessoa['idpessoa']=1;
        $dadosPessoa['nmpessoa']='Joao Silva';
        $array[] = $dadosPessoa;    
        $retorno = ArrayHelper::convertArrayPDO2Adianti($array,PDO::CASE_UPPER);
        $obj0 = $retorno[0];
        $this->assertEquals(1,  $obj0->IDPESSOA);
        $this->assertEquals('Joao Silva',  $obj0->NMPESSOA);
    }
    //-----------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------
    public function testConvertString2Array_obj() {
        $this->expectException(InvalidArgumentException::class);
        $retorno = ArrayHelper::convertString2Array(new StdClass);
    }    
    public function testConvertString2Array_null() {
        $this->expectException(InvalidArgumentException::class);
        $retorno = ArrayHelper::convertString2Array(null);
    }
    public function testConvertString2Array_int() {
        $this->expectException(InvalidArgumentException::class);
        $retorno = ArrayHelper::convertString2Array(1);
    }
    public function testConvertString2Array_emptyArray() {
        $this->expectException(InvalidArgumentException::class);
        $retorno = ArrayHelper::convertString2Array(array());
    }
    public function testConvertString2Array_anyString() {
        $this->expectException(InvalidArgumentException::class);
        $retorno = ArrayHelper::convertString2Array('xxxx');
    }
    public function testConvertString2Array_anyString_NoError() {
        $retorno = ArrayHelper::convertString2Array('xxxx',false);
        $this->assertEquals('xxxx', $retorno);
    }
    public function testConvertString2Array_Array() {
        $esperado['M']='Maria';
        $esperado['J']='Jose';
        $retorno = ArrayHelper::convertString2Array($esperado);
        $this->assertEquals($esperado, $retorno);
    }
    public function testConvertString2Array_3StringFormDin() {
        $string = 'S=SIM,N=Não,T=Talvez';
        $esperado['S']='SIM';
        $esperado['N']='Não';
        $esperado['T']='Talvez';
        $retorno = ArrayHelper::convertString2Array($string);
        $this->assertEquals($esperado, $retorno);
    }
    public function testConvertString2Array_3StringFormDin_espaço() {
        $string = '   S=SIM,N=Não,T=Talvez  ';
        $esperado['S']='SIM';
        $esperado['N']='Não';
        $esperado['T']='Talvez';
        $retorno = ArrayHelper::convertString2Array($string);
        $this->assertEquals($esperado, $retorno);
    }
    public function testConvertString2Array_S_StringFormDin() {
        $string = 'S';
        $esperado['S']='';
        $retorno = ArrayHelper::convertString2Array($string);
        $this->assertEquals($esperado, $retorno);
    }
    public function testConvertString2Array_N_StringFormDin() {
        $string = 'N';
        $esperado['N']='';
        $retorno = ArrayHelper::convertString2Array($string);
        $this->assertEquals($esperado, $retorno);
    }        
    public function testConvertString2Array_1StringFormDin() {
        $string = 'S=SIM';
        $esperado['S']='SIM';
        $retorno = ArrayHelper::convertString2Array($string);
        $this->assertEquals($esperado, $retorno);
    }
    public function testConvertString2Array_StringSetaFormDin() {
        $string = 'S=>SIM,N=>Não,T=>Talvez';
        $esperado['S']='SIM';
        $esperado['N']='Não';
        $esperado['T']='Talvez';
        $retorno = ArrayHelper::convertString2Array($string);
        $this->assertEquals($esperado, $retorno);
    }
    public function testConvertString2Array_1StringSetaFormDin() {
        $string = 'S=>SIM';
        $esperado['S']='SIM';
        $retorno = ArrayHelper::convertString2Array($string);
        $this->assertEquals($esperado, $retorno);
    }         

    //-----------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------
    public function testValidateIsArray_FailNull(){
        $this->expectException(InvalidArgumentException::class);
        ValidateHelper::isArray(null,__METHOD__,__LINE__);
    }
    public function testValidateIsArray_FailArrayEmpty(){
        $this->expectException(InvalidArgumentException::class);
        $listArray = array();
        $this->assertNull( ValidateHelper::isArray($listArray,__METHOD__,__LINE__) );
    }
    public function testValidateIsArray_FailString(){
        $this->expectException(InvalidArgumentException::class);
        $listArray = 'xxx';
        $this->assertNull( ValidateHelper::isArray($listArray,__METHOD__,__LINE__) );
    }
    public function testValidateIdIsNumeric_OKArrayNotEmpty(){
        $listArray = array();
        $listArray[]=1;
        $listArray[]=2;
        $this->assertNull( ValidateHelper::isArray($listArray,__METHOD__,__LINE__) );
    }
    //-----------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------
    public function testFormDinDeleteRowByKeyIndex_FailNull(){
        $this->expectException(InvalidArgumentException::class);
        ArrayHelper::formDinDeleteRowByKeyIndex(null,10);
    }
    
    public function testFormDinDeleteRowByKeyIndex_FailAttributeNotExist(){
        $mock = new mockFormDinArray();
        $array = $mock->generateTable();
        
        $result = ArrayHelper::formDinDeleteRowByKeyIndex($array,10);
        
        $expected = false;
        $this->assertEquals($expected, $result['result']);
        $expected = TFormDinMessage::ARRAY_KEY_NOT_EXIST;
        $this->assertEquals($expected, $result['message']);
    }
    
    //-----------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------
    public function testFormDinDeleteRowByColumnNameAndKeyIndex_FailNull(){
        $this->expectException(InvalidArgumentException::class);
        ArrayHelper::formDinDeleteRowByColumnNameAndKeyIndex(null,'xx',10);
    }
    
    public function testFormDinDeleteRowByColumnNameAndKeyIndex_FailAttributeNotExist(){
        $mock = new mockFormDinArray();
        $array = $mock->generateTable();
        
        $result = ArrayHelper::formDinDeleteRowByColumnNameAndKeyIndex($array,'idCarro',10);
        
        $expected = false;
        $this->assertEquals($expected, $result['result']);
        $expected = TFormDinMessage::ARRAY_ATTRIBUTE_NOT_EXIST;
        $this->assertEquals($expected, $result['message']);
    }
    
    public function testFormDinDeleteRowByColumnNameAndKeyIndex_FailKeyNotExist(){
        $mock = new mockFormDinArray();
        $array = $mock->generateTable();
        
        $result = ArrayHelper::formDinDeleteRowByColumnNameAndKeyIndex($array,'IDPESSOA',10);
        
        $expected = false;
        $this->assertEquals($expected, $result['result']);
        $expected = TFormDinMessage::ARRAY_KEY_NOT_EXIST;
        $this->assertEquals($expected, $result['message']);
    }
    
    public function testFormDinDeleteRowByColumnNameAndKeyIndex_okQtd(){
        $mock = new mockFormDinArray();
        $array = $mock->generateTable();
        
        $result = ArrayHelper::formDinDeleteRowByColumnNameAndKeyIndex($array,'IDPESSOA',1);
        
        $expected = true;
        $this->assertEquals($expected, $result['result']);
        $expected = 3;
        $resultQtd = CountHelper::count($result['formarray']['IDPESSOA']);
        $this->assertEquals($expected, $resultQtd);
        
        $this->assertEquals(3, $result['formarray']['IDPESSOA'][1]);
        $this->assertEquals('Dell', $result['formarray']['NMPESSOA'][1]);
        $this->assertEquals('J', $result['formarray']['TPPESSOA'][1]);
        $this->assertEquals(null, $result['formarray']['NMCPF'][1]);
        $this->assertEquals('72381189000110', $result['formarray']['NMCNPJ'][1]);
    }
    
    public function testFormDinDeleteRowByColumnNameAndKeyIndex_ok3Times(){
        $mock = new mockFormDinArray();
        $array = $mock->generateTable();
        
        $result = ArrayHelper::formDinDeleteRowByColumnNameAndKeyIndex($array,'IDPESSOA',3);
        $result = ArrayHelper::formDinDeleteRowByColumnNameAndKeyIndex($result['formarray'],'IDPESSOA',2);
        $result = ArrayHelper::formDinDeleteRowByColumnNameAndKeyIndex($result['formarray'],'IDPESSOA',1);
        
        $expected = true;
        $this->assertEquals($expected, $result['result']);
        $expected = 1;
        $resultQtd = CountHelper::count($result['formarray']['IDPESSOA']);
        $this->assertEquals($expected, $resultQtd);
        
        $this->assertEquals(1, $result['formarray']['IDPESSOA'][0]);
        $this->assertEquals('Joao Silva', $result['formarray']['NMPESSOA'][0]);
        $this->assertEquals('F', $result['formarray']['TPPESSOA'][0]);
        $this->assertEquals('12345678909', $result['formarray']['NMCPF'][0]);
    }
    //-----------------------------------------------------------------------
    public function testConvertArrayMixUpdate2OutputFormat_null()
    {
        $this->expectNotToPerformAssertions();
        $result = ArrayHelper::convertArrayMixUpdate2OutputFormat(null);
    }
    public function testConvertArrayMixUpdate2OutputFormat_arrayEmpty()
    {
        $this->expectNotToPerformAssertions();
        ArrayHelper::convertArrayMixUpdate2OutputFormat(array());
    }
    public function testConvertArrayMixUpdate2OutputFormat_string()
    {
        $this->expectException(InvalidArgumentException::class);
        ArrayHelper::convertArrayMixUpdate2OutputFormat('xxx');
    }
    public function testConvertArrayMixUpdate2OutputFormat_obj()
    {
        $this->expectException(InvalidArgumentException::class);
        $obj = new stdClass();
        ArrayHelper::convertArrayMixUpdate2OutputFormat($obj);
    }    
    public function testConvertArrayMixUpdate2OutputFormat_inputFormDinOutPutAdianti()
    {
        $expected = ['code'=>'{code}','nome'=>'{nome}'];
        $arrayData = 'code|code,nome|nome';
        $result = ArrayHelper::convertArrayMixUpdate2OutputFormat($arrayData);
        $this->assertEquals($expected, $result);
    }
    public function testConvertArrayMixUpdate2OutputFormat_inputPHPOutPutAdianti()
    {
        $expected  = ['code'=>'{code}','nome'=>'{nome}'];
        $arrayData = ['code'=>'code','nome'=>'nome'];
        $result = ArrayHelper::convertArrayMixUpdate2OutputFormat($arrayData);
        $this->assertEquals($expected, $result);
    }
    public function testConvertArrayMixUpdate2OutputFormat_inputAdianitOutPutAdianti()
    {
        $expected  = ['code'=>'{code}','nome'=>'{nome}'];
        $arrayData = ['code'=>'{code}','nome'=>'{nome}'];
        $result = ArrayHelper::convertArrayMixUpdate2OutputFormat($arrayData);
        $this->assertEquals($expected, $result);
    }
    
    public function testConvertArrayMixUpdate2OutputFormat_inputFormDinOutPutPHP()
    {
        $expected =  ['code'=>'code','nome'=>'nome'];
        $arrayData = 'code|code,nome|nome';
        $result = ArrayHelper::convertArrayMixUpdate2OutputFormat($arrayData,ArrayHelper::TYPE_PHP);
        $this->assertEquals($expected, $result);
    }
    public function testConvertArrayMixUpdate2OutputFormat_inputPHPOutPutPHP()
    {
        $expected  = ['code'=>'code','nome'=>'nome'];
        $arrayData = ['code'=>'code','nome'=>'nome'];
        $result = ArrayHelper::convertArrayMixUpdate2OutputFormat($arrayData,ArrayHelper::TYPE_PHP);
        $this->assertEquals($expected, $result);
    }
    public function testConvertArrayMixUpdate2OutputFormat_inputAdianitOutPutPHP()
    {
        $expected  = ['code'=>'code','nome'=>'nome'];
        $arrayData = ['code'=>'{code}','nome'=>'{nome}'];
        $result = ArrayHelper::convertArrayMixUpdate2OutputFormat($arrayData,ArrayHelper::TYPE_PHP);
        $this->assertEquals($expected, $result);
    }

    public function testConvertArrayMixUpdate2OutputFormat_inputFormDinOutPutFormDin()
    {
        $expected =  'code|code,nome|nome';
        $arrayData = 'code|code,nome|nome';
        $result = ArrayHelper::convertArrayMixUpdate2OutputFormat($arrayData,ArrayHelper::TYPE_FORMDIN_STRING_GRID_ACTION);
        $this->assertEquals($expected, $result);
    }
    public function testConvertArrayMixUpdate2OutputFormat_inputPHPOutPutFormDin()
    {
        $expected  = 'code|code,nome|nome';
        $arrayData = ['code'=>'code','nome'=>'nome'];
        $result = ArrayHelper::convertArrayMixUpdate2OutputFormat($arrayData,ArrayHelper::TYPE_FORMDIN_STRING_GRID_ACTION);
        $this->assertEquals($expected, $result);
    }
    public function testConvertArrayMixUpdate2OutputFormat_inputAdianitOutPutFormDin()
    {
        $expected  = 'code|code,nome|nome';
        $arrayData = ['code'=>'{code}','nome'=>'{nome}'];
        $result = ArrayHelper::convertArrayMixUpdate2OutputFormat($arrayData,ArrayHelper::TYPE_FORMDIN_STRING_GRID_ACTION);
        $this->assertEquals($expected, $result);
    }

    //-----------------------------------------------------------------------
    public function testGetTypeArrayMixUpdateFields_null()
    {
        $result = ArrayHelper::getTypeArrayMixUpdateFields(null);
        $this->assertEquals(null,$result);
    }

    public function testGetTypeArrayMixUpdateFields_ArrayNull()
    {
        $arrayData = array();
        $result = ArrayHelper::getTypeArrayMixUpdateFields($arrayData);
        $this->assertEquals(null,$result);
    }

    public function testGetTypeArrayMixUpdateFields_Adianti()
    {
        $arrayData = ['code'=>'{code}','nome'=>'{nome}'];
        $result = ArrayHelper::getTypeArrayMixUpdateFields($arrayData);
        $this->assertEquals(ArrayHelper::TYPE_ADIANTI_GRID_ACTION, $result);
    }

    public function testGetTypeArrayMixUpdateFields_PHP()
    {
        $arrayData = ['code'=>'code','nome'=>'nome'];
        $result = ArrayHelper::getTypeArrayMixUpdateFields($arrayData);
        $this->assertEquals(ArrayHelper::TYPE_PHP, $result);
    }

    public function testGetTypeArrayMixUpdateFields_FormDin()
    {
        $arrayData = 'code|code,nome|nome';
        $result = ArrayHelper::getTypeArrayMixUpdateFields($arrayData);
        $this->assertEquals(ArrayHelper::TYPE_FORMDIN_STRING_GRID_ACTION, $result);
    }
    //-----------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------        
    public function testConvertAdianti2Pdo_noChangeCase(){
        $mock = new mockFormDinArray();
        $arrayData = $mock->generateTablePessoaAdianti();
        $esperado = $mock->generateTablePessoaPDO();
        $retorno = ArrayHelper::convertAdianti2Pdo($arrayData);
        $this->assertEquals($esperado, $retorno);
    }
    public function testConvertAdianti2Pdo_lowerCase(){
        $mock = new mockFormDinArray();
        $arrayData = $mock->generateTablePessoaAdianti();
        $retorno = ArrayHelper::convertAdianti2Pdo($arrayData,PDO::CASE_LOWER);
        $this->assertEquals(1, $retorno[0]['idpessoa']);
        $this->assertEquals('Joao Silva', $retorno[0]['nmpessoa']);
    }
    public function testConvertAdianti2Pdo_UpperCase(){
        $dadosPessoa = new StdClass;
        $dadosPessoa->idpessoa = 1;
        $dadosPessoa->nmpessoa = 'Joao Silva';
        $arrayData[] = $dadosPessoa;
        $retorno = ArrayHelper::convertAdianti2Pdo($arrayData,PDO::CASE_UPPER);
        $this->assertEquals(1, $retorno[0]['IDPESSOA']);
        $this->assertEquals('Joao Silva', $retorno[0]['NMPESSOA']);
    }
    //-----------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------     
    public function testConvertAdianti2FormDin_noChangeCase(){
        $mock = new mockFormDinArray();
        $arrayData = $mock->generateTablePessoaAdianti();
        $esperado = $mock->generateTable();
        $retorno = ArrayHelper::convertAdianti2FormDin($arrayData);
        $this->assertEquals($esperado, $retorno);
    }
    public function testConvertAdianti2FormDin_lowerCase(){
        $mock = new mockFormDinArray();
        $arrayData = $mock->generateTablePessoaAdianti();
        $retorno = ArrayHelper::convertAdianti2FormDin($arrayData,PDO::CASE_LOWER);
        $this->assertEquals(1, $retorno['idpessoa'][0]);
        $this->assertEquals('Joao Silva', $retorno['nmpessoa'][0]);
    }
    public function testConvertAdianti2FormDin_UpperCase(){
        $dadosPessoa = new StdClass;
        $dadosPessoa->idpessoa = 1;
        $dadosPessoa->nmpessoa = 'Joao Silva';
        $arrayData[] = $dadosPessoa;
        $retorno = ArrayHelper::convertAdianti2FormDin($arrayData,PDO::CASE_UPPER);
        $this->assertEquals(1, $retorno['IDPESSOA'][0]);
        $this->assertEquals('Joao Silva', $retorno['NMPESSOA'][0]);
    }
    //-----------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------
    public function testConvertArray2OutputFormat_null(){
        $arrayData = null;
        $esperado  = null;
        $retorno   = ArrayHelper::convertArray2OutputFormat($arrayData);
        $this->assertEquals($esperado, $retorno);
    }
    public function testConvertArray2OutputFormat_ArrayEmpty(){
        $arrayData = array();
        $esperado  = null;
        $retorno   = ArrayHelper::convertArray2OutputFormat($arrayData);
        $this->assertEquals($esperado, $retorno);
    }
    public function testConvertArray2OutputFormat_StringEmpty(){
        $arrayData = '' ;
        $esperado  = null;
        $retorno   = ArrayHelper::convertArray2OutputFormat($arrayData);
        $this->assertEquals($esperado, $retorno);
    }
    public function testConvertArray2OutputFormat_String(){
        $this->expectException(InvalidArgumentException::class);
        $arrayData = 'texto blabl' ;
        ArrayHelper::convertArray2OutputFormat($arrayData);
    }
    public function testConvertArray2OutputFormat_Adianti2PDO(){
        $mock = new mockFormDinArray();
        $arrayData = $mock->generateTablePessoaAdianti();
        $esperado  = $mock->generateTablePessoaPDO();
        $retorno   = ArrayHelper::convertArray2OutputFormat($arrayData,ArrayHelper::TYPE_PDO);
        $this->assertEquals($esperado, $retorno);
    }
    public function testConvertArray2OutputFormat_Adianti2FormDin(){
        $mock = new mockFormDinArray();
        $arrayData = $mock->generateTablePessoaAdianti();
        $esperado  = $mock->generateTable();
        $retorno   = ArrayHelper::convertArray2OutputFormat($arrayData,ArrayHelper::TYPE_FORMDIN);
        $this->assertEquals($esperado, $retorno);
    }
    public function testConvertArray2OutputFormat_Adianti2Adianti(){
        $mock = new mockFormDinArray();
        $arrayData = $mock->generateTablePessoaAdianti();
        $esperado  = $arrayData;
        $retorno   = ArrayHelper::convertArray2OutputFormat($arrayData,ArrayHelper::TYPE_ADIANTI);
        $this->assertEquals($esperado, $retorno);
    }
    public function testConvertArray2OutputFormat_Pdo2Adianti(){
        $mock = new mockFormDinArray();
        $arrayData = $mock->generateTablePessoaPDO();
        $esperado  = $mock->generateTablePessoaAdianti();
        $retorno   = ArrayHelper::convertArray2OutputFormat($arrayData,ArrayHelper::TYPE_ADIANTI);
        $this->assertEquals($esperado, $retorno);
    }
    public function testConvertArray2OutputFormat_Pdo2FormDin(){
        $mock = new mockFormDinArray();
        $arrayData = $mock->generateTablePessoaPDO();
        $esperado  = $mock->generateTable();
        $retorno   = ArrayHelper::convertArray2OutputFormat($arrayData,ArrayHelper::TYPE_FORMDIN);
        $this->assertEquals($esperado, $retorno);
    }
    public function testConvertArray2OutputFormat_Pdo2Pdo(){
        $mock = new mockFormDinArray();
        $arrayData = $mock->generateTablePessoaPDO();
        $esperado  = $arrayData;
        $retorno   = ArrayHelper::convertArray2OutputFormat($arrayData,ArrayHelper::TYPE_PDO);
        $this->assertEquals($esperado, $retorno);
    }    
    public function testConvertArray2OutputFormat_FormDin2Adianti(){
        $mock = new mockFormDinArray();
        $arrayData = $mock->generateTable();
        $esperado  = $mock->generateTablePessoaAdianti();
        $retorno   = ArrayHelper::convertArray2OutputFormat($arrayData,ArrayHelper::TYPE_ADIANTI);
        $this->assertEquals($esperado, $retorno);
    }
    public function testConvertArray2OutputFormat_FormDin2Pdo(){
        $mock = new mockFormDinArray();
        $arrayData = $mock->generateTable();
        $esperado  = $mock->generateTablePessoaPDO();
        $retorno   = ArrayHelper::convertArray2OutputFormat($arrayData,ArrayHelper::TYPE_PDO);
        $this->assertEquals($esperado, $retorno);
    }
    public function testConvertArray2OutputFormat_FormDin2FormDin(){
        $mock = new mockFormDinArray();
        $arrayData = $mock->generateTable();
        $esperado  = $arrayData;
        $retorno   = ArrayHelper::convertArray2OutputFormat($arrayData,ArrayHelper::TYPE_FORMDIN);
        $this->assertEquals($esperado, $retorno);
    }
    //-----------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------
    public function testConvertArray2PhpKeyValue_FailKeyColumn(){
        $this->expectException(InvalidArgumentException::class);
        $mock = new mockFormDinArray();
        $arrayData = $mock->generateTablePessoaPDO();
        ArrayHelper::convertArray2PhpKeyValue($arrayData,'ID','NOME');
    }
    public function testConvertArray2PhpKeyValue_FailValueColumn(){
        $this->expectException(InvalidArgumentException::class);
        $mock = new mockFormDinArray();
        $arrayData = $mock->generateTablePessoaPDO();
        ArrayHelper::convertArray2PhpKeyValue($arrayData,'IDPESSOA','NOME');
    }
    public function testConvertArray2PhpKeyValue_OkPDO(){
        $esperado[1]='Joao Silva';
        $esperado[2]='Maria Laranja';
        $esperado[3]='Dell';
        $esperado[4]='Microsoft';

        $mock = new mockFormDinArray();
        $arrayData = $mock->generateTablePessoaPDO();
        $retorno   = ArrayHelper::convertArray2PhpKeyValue($arrayData,'IDPESSOA','NMPESSOA');
        
        $this->assertEquals($esperado, $retorno);
    }
    public function testConvertArray2PhpKeyValue_OkAdianti(){
        $esperado[1]='F';
        $esperado[2]='F';
        $esperado[3]='J';
        $esperado[4]='J';

        $mock = new mockFormDinArray();
        $arrayData = $mock->generateTablePessoaAdianti();
        $retorno   = ArrayHelper::convertArray2PhpKeyValue($arrayData,'IDPESSOA','TPPESSOA');
        
        $this->assertEquals($esperado, $retorno);
    }    
}