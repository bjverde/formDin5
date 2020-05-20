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

$path =  __DIR__.'/../../../../../';
//require_once $path.'tests/initTest.php';

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Error\Warning;

class TFormDinTest extends TestCase
{

    private $classTest;
    
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->classTest = new TFormDin('Phpunit');
    }
    
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown(): void {
        $this->classTest = null;
        parent::tearDown();
    }
      
    public function testValidateDeprecated_null()
    {
        $this->expectNotToPerformAssertions();
        $this->classTest->validateDeprecated(null,null);
    }

    public function testValidateDeprecated_Heigh()
    {
        $this->expectException(Warning::class);
        $this->classTest->validateDeprecated(200,null);
    }

    public function testValidateDeprecated_Width()
    {
        $this->expectException(Warning::class);
        $this->classTest->validateDeprecated(null,200);
    }

    public function mockAddElementFormList($listFormElements
                                         ,$obj
                                         ,$type = TFormDin::TYPE_FIELD
                                         ,$label=null
                                         ,$boolNewLine=true
                                         ,$boolLabelAbove=false)
    {
        if(is_null($listFormElements) || !is_array($listFormElements) ){
            $listFormElements = array();
        }
        $element = array();
        $element['obj']=$obj;
        $element['type']=$type;
        $element['label']=$label;
        $element['boolNewLine']=$boolNewLine;
        $element['boolLabelAbove']=$boolLabelAbove;
        $listFormElements[]=$element;
        return $listFormElements;
    }

    public function testNextElementHaveNewLine_0Element()
    {
        $result = $this->classTest->nextElementHaveNewLine(0);
        $this->assertEquals(null, $result);
    }

    public function testNextElementHaveNewLine_1Element()
    {
        $campo = new stdClass();
        $label = 'teste';
        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label);
        $result = $this->classTest->nextElementHaveNewLine(0);
        $this->assertEquals(null, $result);
    }

    public function testNextElementHaveNewLine_2Element_resultTrue()
    {
        $campo = new stdClass();
        $label = 'teste';
        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label);
        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label.'01',true);
        $result = $this->classTest->nextElementHaveNewLine(0);
        $this->assertEquals(true, $result);
    }

    public function testNextElementHaveNewLine_2Element_resultFalse()
    {
        $campo = new stdClass();
        $label = 'teste';
        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label);
        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label.'01',false);
        $result = $this->classTest->nextElementHaveNewLine(0);
        $this->assertEquals(false, $result);
    }

    public function testNextElementHaveNewLine_3Element_resultTrue_start01()
    {
        $campo = new stdClass();
        $label = 'teste';
        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label);
        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label.'01',true);
        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label.'02',true);
        $result = $this->classTest->nextElementHaveNewLine(1);
        $this->assertEquals(true, $result);
    }

    public function testNextElementHaveNewLine_3Element_resultFalse_start01()
    {
        $campo = new stdClass();
        $label = 'teste';
        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label);
        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label.'01',false);
        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label.'02',false);
        $result = $this->classTest->nextElementHaveNewLine(1);
        $this->assertEquals(false, $result);
    }

    public function testAddFieldsRow_1Element()
    {
        $campo = new stdClass();
        $label = 'teste';
        $expected = array([$label], [$campo]);

        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label);
        $result = $this->classTest->addFieldsRow(0);
        $this->assertEquals(0, $result['key']);
        $this->assertEquals($expected, $result['row']);        
    }

    public function testAddFieldsRow_2Elements()
    {
        $campo = new stdClass();
        $label = 'teste';
        $label1 = 'teste1';
        $expected = array([$label], [$campo],[$label1], [$campo]);


        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label);
        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label1,FALSE);

        $result = $this->classTest->addFieldsRow(0);
        $this->assertEquals(1, $result['key']);
        $this->assertEquals($expected, $result['row']); 
    }

    public function testGetListFormElements_null()
    {
        $list = $this->classTest->getListFormElements();
        $this->assertEmpty($list);
    }

    public function testGetListFormElements_qtd1()
    {
        $this->classTest->addElementFormList('1');
        $list = $this->classTest->getListFormElements();
        $qtd = CountHelper::count($list);
        $this->assertEquals(1, $qtd);
    }

    public function testGetListFormElements_qtd2()
    {
        $this->classTest->addElementFormList('1');
        $this->classTest->addElementFormList('2');
        $list = $this->classTest->getListFormElements();
        $qtd = CountHelper::count($list);
        $this->assertEquals(2, $qtd);
    }
}