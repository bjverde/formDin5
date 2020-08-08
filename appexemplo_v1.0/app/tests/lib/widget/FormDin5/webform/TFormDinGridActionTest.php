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

require_once  __DIR__.'/../../mockFormAdianti.php';

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Error\Notice;
use PHPUnit\Framework\Error\Warning;

class TFormDinGridActionTest extends TestCase
{

    private $classTest;
    
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp(): void {
        parent::setUp();
        $objForm = new mockFormDinComAdianti();
        $actionName = 'onSave';
        $arrayMixUpdateFields = ['code'=>'{code}'];
        $this->classTest = new TFormDinGridAction($objForm,$actionName, $arrayMixUpdateFields);
    }
    
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown(): void {
        $this->classTest = null;
        parent::tearDown();
    }

    public function testTypeObje()
    {
        $result = $this->classTest->getAdiantiObj();
        $this->assertInstanceOf(TDataGridAction::class, $result);
    }

    public function testAlignCenter()
    {
        $column = new TFormDinGridColumn('TEST', 'TEST',null,'center');
        $result =  $column->getAdiantiObj();
        $this->assertInstanceOf(TDataGridColumn::class, $result);
        $this->assertEquals('center', $result->getAlign());
    }
    //-----------------------------------------------------------------------
    public function testConvertArray2OutputFormat_null()
    {
        $this->expectNotToPerformAssertions();
        $result = $this->classTest->convertArray2OutputFormat(null);
    }
    public function testConvertArray2OutputFormat_arrayEmpty()
    {
        $this->expectNotToPerformAssertions();
        $this->classTest->convertArray2OutputFormat(array());
    }
    public function testConvertArray2OutputFormat_string()
    {
        $this->expectException(InvalidArgumentException::class);
        $result = $this->classTest->convertArray2OutputFormat('xxx');
    }
    public function testConvertArray2OutputFormat_obj()
    {
        $this->expectException(InvalidArgumentException::class);
        $obj = new stdClass();
        $result = $this->classTest->convertArray2OutputFormat($obj);
    }    
    public function testConvertArray2OutputFormat_inputFormDinOutPutAdianti()
    {
        $expected = ['code'=>'{code}','nome'=>'{nome}'];
        $arrayData = 'code|code,nome|nome';
        $result = $this->classTest->convertArray2OutputFormat($arrayData);
        $this->assertEquals($expected, $result);
    }
    public function testConvertArray2OutputFormat_inputPHPOutPutAdianti()
    {
        $expected  = ['code'=>'{code}','nome'=>'{nome}'];
        $arrayData = ['code'=>'code','nome'=>'nome'];
        $result = $this->classTest->convertArray2OutputFormat($arrayData);
        $this->assertEquals($expected, $result);
    }
    public function testConvertArray2OutputFormat_inputAdianitOutPutAdianti()
    {
        $expected  = ['code'=>'{code}','nome'=>'{nome}'];
        $arrayData = ['code'=>'{code}','nome'=>'{nome}'];
        $result = $this->classTest->convertArray2OutputFormat($arrayData);
        $this->assertEquals($expected, $result);
    }

    
    public function testConvertArray2OutputFormat_inputFormDinOutPutPHP()
    {
        $expected =  ['code'=>'code','nome'=>'nome'];
        $arrayData = 'code|code,nome|nome';
        $result = $this->classTest->convertArray2OutputFormat($arrayData,TFormDinGridAction::TYPE_PHP);
        $this->assertEquals($expected, $result);
    }
    public function testConvertArray2OutputFormat_inputPHPOutPutPHP()
    {
        $expected  = ['code'=>'code','nome'=>'nome'];
        $arrayData = ['code'=>'code','nome'=>'nome'];
        $result = $this->classTest->convertArray2OutputFormat($arrayData,TFormDinGridAction::TYPE_PHP);
        $this->assertEquals($expected, $result);
    }
    public function testConvertArray2OutputFormat_inputAdianitOutPutPHP()
    {
        $expected  = ['code'=>'code','nome'=>'nome'];
        $arrayData = ['code'=>'{code}','nome'=>'{nome}'];
        $result = $this->classTest->convertArray2OutputFormat($arrayData,TFormDinGridAction::TYPE_PHP);
        $this->assertEquals($expected, $result);
    }

    public function testConvertArray2OutputFormat_inputFormDinOutPutFormDin()
    {
        $expected =  'code|code,nome|nome';
        $arrayData = 'code|code,nome|nome';
        $result = $this->classTest->convertArray2OutputFormat($arrayData,TFormDinGridAction::TYPE_FORMDIN);
        $this->assertEquals($expected, $result);
    }
    public function testConvertArray2OutputFormat_inputPHPOutPutFormDin()
    {
        $expected  = 'code|code,nome|nome';
        $arrayData = ['code'=>'code','nome'=>'nome'];
        $result = $this->classTest->convertArray2OutputFormat($arrayData,TFormDinGridAction::TYPE_FORMDIN);
        $this->assertEquals($expected, $result);
    }
    public function testConvertArray2OutputFormat_inputAdianitOutPutFormDin()
    {
        $expected  = 'code|code,nome|nome';
        $arrayData = ['code'=>'{code}','nome'=>'{nome}'];
        $result = $this->classTest->convertArray2OutputFormat($arrayData,TFormDinGridAction::TYPE_FORMDIN);
        $this->assertEquals($expected, $result);
    }

    //-----------------------------------------------------------------------
    public function testGetTypeArrayMixUpdateFields_null()
    {
        $result = $this->classTest->getTypeArrayMixUpdateFields(null);
        $this->assertEquals(null,$result);
    }

    public function testGetTypeArrayMixUpdateFields_ArrayNull()
    {
        $arrayData = array();
        $result = $this->classTest->getTypeArrayMixUpdateFields($arrayData);
        $this->assertEquals(null,$result);
    }

    public function testGetTypeArrayMixUpdateFields_Adianti()
    {
        $arrayData = ['code'=>'{code}','nome'=>'{nome}'];
        $result = $this->classTest->getTypeArrayMixUpdateFields($arrayData);
        $this->assertEquals(TFormDinGridAction::TYPE_ADIANTI, $result);
    }

    public function testGetTypeArrayMixUpdateFields_PHP()
    {
        $arrayData = ['code'=>'code','nome'=>'nome'];
        $result = $this->classTest->getTypeArrayMixUpdateFields($arrayData);
        $this->assertEquals(TFormDinGridAction::TYPE_PHP, $result);
    }

    public function testGetTypeArrayMixUpdateFields_FormDin()
    {
        $arrayData = 'code|code,nome|nome';
        $result = $this->classTest->getTypeArrayMixUpdateFields($arrayData);
        $this->assertEquals(TFormDinGridAction::TYPE_FORMDIN, $result);
    }
}