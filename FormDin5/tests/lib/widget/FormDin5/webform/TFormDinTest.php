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
        $classForm = new mockFormDinComAdianti();
        $this->classTest = new TFormDin($classForm,'Phpunit');
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
        $this->expectWarning();
        $this->classTest->validateDeprecated(200,null);
    }

    public function testValidateDeprecated_Width()
    {
        $this->expectWarning();
        $this->classTest->validateDeprecated(null,200);
    }
    //-----------------------------------------------------------------------
    public function testConstruct_failNotObject()
    {
        $this->expectError();
        $classTest = new TFormDin('xx','Phpunit');
    }    
    public function testSetObjForm_FailNull()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->classTest->setObjForm(null);
    }
    public function testSetObjForm_FailNotObjectString()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->classTest->setObjForm('string');
    }
    public function testSetObjForm_FailNotObjectInt()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->classTest->setObjForm(11);
    }
    public function testGetObjForm()
    {
        $objForm = new mockFormDinComAdianti();
        $this->classTest->setObjForm($objForm);
        $objResult = $this->classTest->getObjForm();
        $this->assertInstanceOf(TPage::class, $objResult);
        $this->assertInstanceOf(mockFormDinComAdianti::class, $objResult);
    }    
    //-----------------------------------------------------------------------
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

    //-----------------------------------------------------------------------
    public function testNextElementNewLine_0Element()
    {
        $result = $this->classTest->nextElementNewLine(0);
        $this->assertEquals(null, $result);
    }

    public function testNextElemenNewLine_1Element()
    {
        $campo = new stdClass();
        $label = 'teste';
        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label);
        $result = $this->classTest->nextElementNewLine(0);
        $this->assertEquals(null, $result);
    }

    public function testNextElementHNewLine_2Element_start0ResultTrue()
    {
        $campo = new stdClass();
        $label = 'teste';
        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label);
        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label.'01',true);
        $result = $this->classTest->nextElementNewLine(0);
        $this->assertEquals(true, $result);
    }

    public function testNextElementHNewLine_2Element_start0ResultFalse()
    {
        $campo = new stdClass();
        $label = 'teste';
        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label);
        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label.'01',false);
        $result = $this->classTest->nextElementNewLine(0);
        $this->assertEquals(false, $result);
    }

    public function testNextElementHNewLine_2Element_start1ResultTrue()
    {
        $campo = new stdClass();
        $label = 'teste';
        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label);
        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label.'01',true);
        $result = $this->classTest->nextElementNewLine(1);
        $this->assertEquals(null, $result);
    }

    public function testNextElementHNewLine_2Element_start1ResultFalse()
    {
        $campo = new stdClass();
        $label = 'teste';
        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label);
        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label.'01',false);
        $result = $this->classTest->nextElementNewLine(1);
        $this->assertEquals(null, $result);
    }

    public function testNextElementHNewLine_3Element_resultTrue_start00()
    {
        $campo = new stdClass();
        $label = 'teste';
        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label);
        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label.'01',true);
        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label.'02',true);
        $result = $this->classTest->nextElementNewLine(0);
        $this->assertEquals(true, $result);
    }

    public function testNextElementHNewLine_3Element_resultFalse_start00()
    {
        $campo = new stdClass();
        $label = 'teste';
        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label);
        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label.'01',false);
        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label.'02',false);
        $result = $this->classTest->nextElementNewLine(0);
        $this->assertEquals(false, $result);
    }

    public function testNextElementHNewLine_3Element_resultTrue_start01()
    {
        $campo = new stdClass();
        $label = 'teste';
        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label);
        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label.'01',true);
        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label.'02',true);
        $result = $this->classTest->nextElementNewLine(1);
        $this->assertEquals(true, $result);
    }

    public function testNextElementHNewLine_3Element_resultFalse_start01()
    {
        $campo = new stdClass();
        $label = 'teste';
        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label);
        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label.'01',false);
        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label.'02',false);
        $result = $this->classTest->nextElementNewLine(1);
        $this->assertEquals(false, $result);
    }

    public function testNextElementHNewLine_3Element_resultNull_start02()
    {
        $campo = new stdClass();
        $label = 'teste';
        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label);
        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label.'01',true);
        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label.'02',true);
        $result = $this->classTest->nextElementNewLine(2);
        $this->assertEquals(null, $result);
    }
    //-----------------------------------------------------------------------
    public function testGetArrayElementLabelAbove_ArrayNull_Exception()
    {
        $this->expectException(InvalidArgumentException::class);
        $result = $this->classTest->getArrayElementLabelAbove(null);
    }

    public function testGetArrayElementLabelAbove_ArrayEmpty_Exception()
    {
        $this->expectException(InvalidArgumentException::class);
        $result = $this->classTest->getArrayElementLabelAbove(array());
    }

    public function testGetArrayElementLabelAbove_true()
    {
        $element = array();
        $element['obj']=new stdClass();;
        $element['type']=TFormDin::TYPE_FIELD;
        $element['label']='Teste';
        $element['boolNewLine']=true;
        $element['boolLabelAbove']=true;

        $expected = array([$element['label'],$element['obj']]);

        $result = $this->classTest->getArrayElementLabelAbove($element);
        $this->assertEquals($expected, $result);
    }

    public function testGetArrayElementLabelAbove_false()
    {
        $element = array();
        $element['obj']=new stdClass();;
        $element['type']=TFormDin::TYPE_FIELD;
        $element['label']='Teste';
        $element['boolNewLine']=true;
        $element['boolLabelAbove']=false;

        $expected = array([$element['label']],[$element['obj']]);

        $result = $this->classTest->getArrayElementLabelAbove($element);
        $this->assertEquals($expected, $result);
    }

    //-----------------------------------------------------------------------
    public function testAddFieldsRow_0Element()
    {
        $keyStart = 0;
        $result = $this->classTest->addFieldsRow($keyStart);
        $this->assertEquals(0, $result['key']);
        $this->assertEquals(null, $result['row']);  
    }

    public function testAddFieldsRow_1Element()
    {
        $campo = new stdClass();
        $label = 'teste';
        $expected = array([$label], [$campo]);

        $keyStart = 0;


        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label);
        $result = $this->classTest->addFieldsRow($keyStart);
        $this->assertEquals(0, $result['key']);
        $this->assertEquals($expected, $result['row']);        
    }

    public function testAddFieldsRow_2Elements_nextNewLine()
    {
        $campo = new stdClass();
        $label = 'teste';
        $label1 = 'teste1';
        $expected = array([$label], [$campo]);

        $keyStart = 0;


        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label);
        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label1,true);

        $result = $this->classTest->addFieldsRow($keyStart);
        $this->assertEquals(0, $result['key']);
        $this->assertEquals($expected, $result['row']); 
    }

    public function testAddFieldsRow_2Elements_nextSameLine()
    {
        $campo = new stdClass();
        $label = 'teste';
        $label1 = 'teste1';
        $expected = array([$label], [$campo],[$label1], [$campo]);

        $keyStart = 0;


        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label);
        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label1,FALSE);

        $result = $this->classTest->addFieldsRow($keyStart);
        $this->assertEquals(2, $result['key']);
        $this->assertEquals($expected, $result['row']); 
    }

    public function testAddFieldsRow_2Elements_nextSameLine_2LabelAbove()
    {
        $campo = new stdClass();
        $label = 'teste';
        $label1 = 'teste1';
        $expected = array([$label,$campo],[$label1,$campo]);

        $keyStart = 0;


        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label,null,true);
        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label1,FALSE,true);

        $result = $this->classTest->addFieldsRow($keyStart);
        $this->assertEquals(2, $result['key']);
        $this->assertEquals($expected, $result['row']); 
    }

    public function testAddFieldsRow_2Elements_nextSameLine_FristLabelAbove()
    {
        $campo = new stdClass();
        $label = 'teste';
        $label1 = 'teste1';
        $expected = array([$label,$campo],[$label1],[$campo]);

        $keyStart = 0;


        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label,null,true);
        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label1,FALSE);

        $result = $this->classTest->addFieldsRow($keyStart);
        $this->assertEquals(2, $result['key']);
        $this->assertEquals($expected, $result['row']); 
    }

    public function testAddFieldsRow_2Elements_nextSameLine_SecondLabelAbove()
    {
        $campo = new stdClass();
        $label = 'teste';
        $label1 = 'teste1';
        $expected = array([$label],[$campo],[$label1,$campo]);

        $keyStart = 0;


        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label);
        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label1,FALSE,true);

        $result = $this->classTest->addFieldsRow($keyStart);
        $this->assertEquals(2, $result['key']);
        $this->assertEquals($expected, $result['row']); 
    }

    public function testAddFieldsRow_3Elements_start0_1PerLine()
    {
        $campo = new stdClass();
        $label = 'teste';
        $label1 = 'teste1';
        $label2 = 'teste2';
        $expected = array([$label], [$campo]);

        $keyStart = 0;


        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label);
        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label1);
        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label2);

        $result = $this->classTest->addFieldsRow($keyStart);
        $this->assertEquals(0, $result['key']);
        $this->assertEquals($expected, $result['row']); 
    }

    public function testAddFieldsRow_3Elements_start1_1PerLine()
    {
        $campo = new stdClass();
        $label = 'teste';
        $label1 = 'teste1';
        $label2 = 'teste2';
        $expected = array([$label1], [$campo]);

        $keyStart = 1;


        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label);
        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label1);
        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label2);

        $result = $this->classTest->addFieldsRow($keyStart);
        $this->assertEquals(1, $result['key']);
        $this->assertEquals($expected, $result['row']); 
    }

    public function testAddFieldsRow_3Elements_start2_1PerLine()
    {
        $campo = new stdClass();
        $label = 'teste';
        $label1 = 'teste1';
        $label2 = 'teste2';
        $expected = array([$label2], [$campo]);

        $keyStart = 2;


        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label);
        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label1);
        $this->classTest->addElementFormList($campo,TFormDin::TYPE_FIELD,$label2);

        $result = $this->classTest->addFieldsRow($keyStart);
        $this->assertEquals(2, $result['key']);
        $this->assertEquals($expected, $result['row']); 
    }

    public function testAddFieldsRow_3Elements_start00_2FieldSamelines()
    {        
        $label0 = 'teste0';
        $campo0 = new stdClass();
        $label1 = 'teste1';
        $campo1 = new stdClass();
        $label2 = 'teste2';
        $campo2 = new stdClass();
        $expected = array([$label0], [$campo0],[$label1], [$campo1]);

        $keyStart = 0;


        $this->classTest->addElementFormList($campo0,TFormDin::TYPE_FIELD,$label0,true);
        $this->classTest->addElementFormList($campo1,TFormDin::TYPE_FIELD,$label1,false);
        $this->classTest->addElementFormList($campo2,TFormDin::TYPE_FIELD,$label2,true);

        $result = $this->classTest->addFieldsRow($keyStart);
        $this->assertEquals(1, $result['key']);
        $this->assertEquals($expected, $result['row']); 
    }

    public function testAddFieldsRow_4Elements_start02_2FieldSamelines()
    {        
        $label0 = 'teste0';
        $campo0 = new stdClass();
        $label1 = 'teste1';
        $campo1 = new stdClass();
        $txtLabel = 'teste2';
        $label2 = new TFormDinLabelField($txtLabel);
        $campo2 = new TFormDinTextField('02',$txtLabel,20);
        $txtLabel = 'teste3';
        $label3 = new TFormDinLabelField($txtLabel);
        $campo3 = new TFormDinTextField('03',$txtLabel,20);
        $expected = array([$label2], [$campo2],[$label3], [$campo3]);

        $keyStart = 2;


        $this->classTest->addElementFormList($campo0,TFormDin::TYPE_FIELD,$label0,true);
        $this->classTest->addElementFormList($campo1,TFormDin::TYPE_FIELD,$label1,false);
        $this->classTest->addElementFormList($campo2,TFormDin::TYPE_FIELD,$label2,true);
        $this->classTest->addElementFormList($campo3,TFormDin::TYPE_FIELD,$label3,false);

        $result = $this->classTest->addFieldsRow($keyStart);
        $this->assertEquals(4, $result['key']);
        $this->assertEquals($expected, $result['row']); 
    }

    public function testAddFieldsRow_6Elements_start0_2Group2FieldSameline()
    {        
        $campo0 = new TFormSeparator('FormDin - 2 campos');
        $txtLabel = 'teste1';
        $label1 = new TFormDinLabelField($txtLabel);
        $campo1 = new TFormDinTextField('02',$txtLabel,20);
        $txtLabel = 'teste2';
        $label2 = new TFormDinLabelField($txtLabel);
        $campo2 = new TFormDinTextField('02',$txtLabel,20);


        $campo3 = new TFormSeparator('FormDin - 2 campos');
        $txtLabel = 'teste4';
        $label4 = new TFormDinLabelField($txtLabel);
        $campo4 = new TFormDinTextField('02',$txtLabel,20);
        $txtLabel = 'teste5';
        $label5 = new TFormDinLabelField($txtLabel);
        $campo5 = new TFormDinTextField('02',$txtLabel,20);
        $expected = array([$campo0]);

        $keyStart = 0;

        $this->classTest->addElementFormList($campo0,TFormDin::TYPE_LAYOUT);
        $this->classTest->addElementFormList($campo1,TFormDin::TYPE_FIELD,$label1,true);
        $this->classTest->addElementFormList($campo2,TFormDin::TYPE_FIELD,$label2,false);
        $this->classTest->addElementFormList($campo3,TFormDin::TYPE_LAYOUT);
        $this->classTest->addElementFormList($campo4,TFormDin::TYPE_FIELD,$label4,true,true);
        $this->classTest->addElementFormList($campo5,TFormDin::TYPE_FIELD,$label5,false,true);

        $result = $this->classTest->addFieldsRow($keyStart);
        $this->assertEquals(0, $result['key']); 
    }

    public function testAddFieldsRow_6Elements_start01_2Group2FieldSameline()
    {        
        $campo0 = new TFormSeparator('FormDin - 2 campos');
        $txtLabel = 'teste1';
        $label1 = new TFormDinLabelField($txtLabel);
        $campo1 = new TFormDinTextField('02',$txtLabel,20);
        $txtLabel = 'teste2';
        $label2 = new TFormDinLabelField($txtLabel);
        $campo2 = new TFormDinTextField('02',$txtLabel,20);


        $campo3 = new TFormSeparator('FormDin - 2 campos');
        $txtLabel = 'teste4';
        $label4 = new TFormDinLabelField($txtLabel);
        $campo4 = new TFormDinTextField('02',$txtLabel,20);
        $txtLabel = 'teste5';
        $label5 = new TFormDinLabelField($txtLabel);
        $campo5 = new TFormDinTextField('02',$txtLabel,20);
        $expected = array([$label1], [$campo1],[$label2], [$campo2]);

        $keyStart = 1;

        $this->classTest->addElementFormList($campo0,TFormDin::TYPE_LAYOUT);
        $this->classTest->addElementFormList($campo1,TFormDin::TYPE_FIELD,$label1,true);
        $this->classTest->addElementFormList($campo2,TFormDin::TYPE_FIELD,$label2,false);
        $this->classTest->addElementFormList($campo3,TFormDin::TYPE_LAYOUT);
        $this->classTest->addElementFormList($campo4,TFormDin::TYPE_FIELD,$label4,true,true);
        $this->classTest->addElementFormList($campo5,TFormDin::TYPE_FIELD,$label5,false,true);

        $result = $this->classTest->addFieldsRow($keyStart);
        $this->assertEquals(2, $result['key']);
        $this->assertEquals($expected, $result['row']); 
    }
    public function testAddFieldsRow_6Elements_start04_2Group2FieldSameline()
    {        
        $campo0 = new TFormSeparator('FormDin - 2 campos');
        $txtLabel = 'teste1';
        $label1 = new TFormDinLabelField($txtLabel);
        $campo1 = new TFormDinTextField('02',$txtLabel,20);
        $txtLabel = 'teste2';
        $label2 = new TFormDinLabelField($txtLabel);
        $campo2 = new TFormDinTextField('02',$txtLabel,20);


        $campo3 = new TFormSeparator('FormDin - 2 campos');
        $txtLabel = 'teste4';
        $label4 = new TFormDinLabelField($txtLabel);
        $campo4 = new TFormDinTextField('02',$txtLabel,20);
        $txtLabel = 'teste5';
        $label5 = new TFormDinLabelField($txtLabel);
        $campo5 = new TFormDinTextField('02',$txtLabel,20);
        $expected = array([$label4,$campo4],[$label5,$campo5]);

        $keyStart = 4;

        $this->classTest->addElementFormList($campo0,TFormDin::TYPE_LAYOUT);
        $this->classTest->addElementFormList($campo1,TFormDin::TYPE_FIELD,$label1,true);
        $this->classTest->addElementFormList($campo2,TFormDin::TYPE_FIELD,$label2,false);
        $this->classTest->addElementFormList($campo3,TFormDin::TYPE_LAYOUT);
        $this->classTest->addElementFormList($campo4,TFormDin::TYPE_FIELD,$label4,true,true);
        $this->classTest->addElementFormList($campo5,TFormDin::TYPE_FIELD,$label5,false,true);

        $result = $this->classTest->addFieldsRow($keyStart);
        $this->assertEquals(6, $result['key']);
        $this->assertEquals($expected, $result['row']); 
    }    
    //-------------------------------------------------------------------------
    public function testSetAdiantiObj_wrongObj()
    {
        $this->expectException(InvalidArgumentException::class);

        $classForm = new stdClass();
        $formDin = new TFormDin($classForm,'Phpunit');        
        $WrongObj = new stdClass();
        $formDin->setAdiantiObj($WrongObj);
    }
    public function testSetAdiantiObj_wrongString()
    {
        $this->expectException(InvalidArgumentException::class);

        $classForm = new stdClass();
        $formDin = new TFormDin($classForm,'Phpunit');
        $formDin->setAdiantiObj('string');
    }
    public function testSetAdiantiObj_setNull()
    {
        $this->classTest->setAdiantiObj();
        $adiantiObj = $this->classTest->getAdiantiObj();
        $name = $adiantiObj->getName();
        $this->assertInstanceOf(BootstrapFormBuilder::class, $adiantiObj);
        $this->assertEquals(null, $name);
    }
    public function testSetAdiantiObj_setName()
    {
        $bootForm = new BootstrapFormBuilder('bootForm');
        $this->classTest->setAdiantiObj($bootForm);
        $adiantiObj = $this->classTest->getAdiantiObj();
        $name = $adiantiObj->getName();
        $this->assertInstanceOf(BootstrapFormBuilder::class, $adiantiObj);
        $this->assertEquals('bootForm', $name);
    }
    public function testSetAdiantiObj_setTitle()
    {
        $bootForm = new BootstrapFormBuilder('bootForm');
        $reflectionProperty = new \ReflectionProperty(BootstrapFormBuilder::class, 'title');
        $reflectionProperty->setAccessible(true);

        $this->classTest->setAdiantiObj($bootForm,'b1','title form');
        $adiantiObj = $this->classTest->getAdiantiObj();
        $title = $reflectionProperty->getValue($bootForm);

        $this->assertInstanceOf(BootstrapFormBuilder::class, $adiantiObj);
        $this->assertEquals('title form', $title);
    }    
    //-------------------------------------------------------------------------
    public function testgetAdiantiObj()
    {
        $adiantiObj = $this->classTest->getAdiantiObj();
        $name = $adiantiObj->getName();
        $this->assertInstanceOf(BootstrapFormBuilder::class, $adiantiObj);
        $this->assertEquals('formdin', $name);
    }        
    //-------------------------------------------------------------------------
    public function testGetAdiantiObj2GetAdiantiObj2_null(){
        $this->expectNotToPerformAssertions();
        $this->classTest->getAdiantiObj2();
    }
    public function testGetAdiantiObj2GetAdiantiObj2_2FieldText1Content(){
        $bootForm = new BootstrapFormBuilder('bootForm');
        $reflectionProperty = new \ReflectionProperty(BootstrapFormBuilder::class, 'tabcontent');
        $reflectionProperty->setAccessible(true);

        $this->classTest->setAdiantiObj( $bootForm );


        $strLegend = null;
        $objField = new TFormSeparator($strLegend);
        $this->classTest->addElementFormList($objField,TFormDin::TYPE_LAYOUT);
        
        $formField = new TFormDinTextField('TEXT01','Texto 1 tam 10', 10);
        $label = $formField->getLabel();
        $objField = $formField->getAdiantiObj();        
        $this->classTest->addElementFormList($objField,TFormDin::TYPE_FIELD,$label);
        
        $strLegend = null;
        $objField = new TFormSeparator($strLegend);
        $this->classTest->addElementFormList($objField,TFormDin::TYPE_LAYOUT);

        $formField = new TFormDinTextField('TEXT02','Texto 2 tam 10', 10);
        $label = $formField->getLabel();
        $objField = $formField->getAdiantiObj();  
        $this->classTest->addElementFormList($objField,TFormDin::TYPE_FIELD,$label);

        $adiantiObjForm = $this->classTest->getAdiantiObj2();
        $tabcontent = $reflectionProperty->getValue($adiantiObjForm);
        $list = array_values($tabcontent);
        $list = $list[0];

        $qtd = CountHelper::count($list);

        $this->assertEquals(4, $qtd);
        $this->assertEquals('content', $list[0]->type);
        $this->assertEquals('fields' , $list[1]->type);
        $this->assertEquals('content', $list[2]->type);
        $this->assertEquals('fields' , $list[3]->type);
    }

    public function testGetAdiantiObj2GetListFormElements_null()
    {
        $list = $this->classTest->getListFormElements();
        $this->assertEmpty($list);
    }
    //-------------------------------------------------------------------------
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
    //-------------------------------------------------------------------------
    public function testAddGroupField(){
        $bootForm = new BootstrapFormBuilder('bootForm');
        $reflectionProperty = new \ReflectionProperty(BootstrapFormBuilder::class, 'tabcontent');
        $reflectionProperty->setAccessible(true);

        $this->classTest->setAdiantiObj( $bootForm );
        $this->classTest->addGroupField(null,'Grupo Texto');
        $this->classTest->addTextField('txe','Campo texto');
        $this->classTest->closeGroup();

        $adiantiObjForm = $this->classTest->getAdiantiObj2();
        $tabcontent = $reflectionProperty->getValue($adiantiObjForm);
        $list = array_values($tabcontent);
        $list = $list[0];

        $qtd = CountHelper::count($list);

        $this->assertEquals(3, $qtd);
        $this->assertEquals('content', $list[0]->type);
        $this->assertEquals('fields' , $list[1]->type);
        $this->assertEquals('content', $list[2]->type);
    }
    //-------------------------------------------------------------------------
    public function testSetAction_failArrayLabel(){
        $this->expectError();

        $actionsLabel = array('action01','action02');
        $this->classTest->setAction( $actionsLabel);
    }
    //-------------------------------------------------------------------------
    public function testSetAction_failActionsName(){
        $this->expectException(InvalidArgumentException::class);

        $this->classTest->setAction( 't1');
    }

    public function testSetAction_methods(){
        $this->classTest->setAction(_t('Save'), 'onSave', false,'far:check-circle green');
        $this->classTest->setAction(_t('Clear'), 'onClear', false,'fa:eraser red');

        $objForm = $this->classTest->show();
        $listAction = $objForm->getActions();
        $qtd = CountHelper::count($listAction);
        $this->assertEquals(2, $qtd);
        //FormDinHelper::debug($listAction[0]);
        //$this->assertEquals('Salvar', $listAction[0]->Label);
        //$this->assertEquals('Limpar', $listAction[0]->Label);
    }
    //-------------------------------------------------------------------------
    public function testSetColumns()
    {
        $this->expectWarning();
        $this->expectErrorMessageMatches('/Falha na migração do FormDin 4 para 5./');
        $classForm = new stdClass();
        $formDin = new TFormDin($classForm,'Phpunit');        
        $formDin->setColumns();
    }
    public function testGetColumns()
    {
        $this->expectWarning();
        $this->expectErrorMessageMatches('/Falha na migração do FormDin 4 para 5./');
        $classForm = new stdClass();
        $formDin = new TFormDin($classForm,'Phpunit');        
        $formDin->getColumns(true);
    }
    public function testGetcolumnWidth()
    {
        $this->expectWarning();
        $this->expectErrorMessageMatches('/Falha na migração do FormDin 4 para 5./');
        $classForm = new stdClass();
        $formDin = new TFormDin($classForm,'Phpunit');        
        $formDin->getcolumnWidth(true);
    }            
    public function testSetShowCloseButton()
    {
        $this->expectWarning();
        $this->expectErrorMessageMatches('/Falha na migração do FormDin 4 para 5./');
        $classForm = new stdClass();
        $formDin = new TFormDin($classForm,'Phpunit');        
        $formDin->setShowCloseButton(true);
    }
    public function testSetFlat()
    {
        $this->expectWarning();
        $this->expectErrorMessageMatches('/Falha na migração do FormDin 4 para 5./');
        $classForm = new stdClass();
        $formDin = new TFormDin($classForm,'Phpunit');        
        $formDin->setFlat(true);
    }
    public function testSetMaximize()
    {
        $this->expectWarning();
        $this->expectErrorMessageMatches('/Falha na migração do FormDin 4 para 5./');
        $classForm = new stdClass();
        $formDin = new TFormDin($classForm,'Phpunit');        
        $formDin->setMaximize(true);
    }
    public function testSetHelpOnLine()
    {
        $this->expectWarning();
        $this->expectErrorMessageMatches('/Falha na migração do FormDin 4 para 5./');
        $classForm = new stdClass();
        $formDin = new TFormDin($classForm,'Phpunit');        
        $formDin->setHelpOnLine();
    }        

}