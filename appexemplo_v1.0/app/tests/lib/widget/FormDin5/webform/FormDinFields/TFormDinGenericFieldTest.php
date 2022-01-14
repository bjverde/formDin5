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

require_once  __DIR__.'/../../../mockFormAdianti.php';

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Error\Warning;

class TFormDinGenericFieldTest extends TestCase
{

    private $classTest;
    
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp(): void {
        parent::setUp();
        $id = 'id1';
        $adiantiObj = new TText($id);
        $this->classTest = new TFormDinGenericField($adiantiObj,$id,'Texto1');
    }
    
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown(): void {
        $this->classTest = null;
        parent::tearDown();
    }

    public function testSetAdiantiObj_failNull()
    {
        $this->expectException(InvalidArgumentException::class);
        $id = 'idTest';
        $adiantiObj = null;
        new TFormDinGenericField($adiantiObj,$id,'Texto1',null,'abc');
    }
    
    public function testGetAdiantiObj_instanceOff()
    {
        $adiantiObj = $this->classTest->getAdiantiObj();
        $this->assertInstanceOf(TText::class, $adiantiObj);
    }

    public function testGetAdiantiField_instanceOff()
    {
        $adiantiObj = $this->classTest->getAdiantiField();
        $this->assertInstanceOf(TText::class, $adiantiObj);
    }
    //-----------------------------------------------------
    public function testRequired_false()
    {
        $result = $this->classTest->isRequired();
        $this->assertSame(false, $result);
    }
    public function testRequired_true()
    {
        $this->classTest->setRequired(true);
        $result = $this->classTest->isRequired();
        $this->assertEquals(true, $result);
    }
    //-----------------------------------------------------
    public function testValidationArray_true()
    {
        $this->classTest->addValidation('xx', new TMaxLengthValidator, array(5));
        $arrayValidations = $this->classTest->getValidations();
        $qtdValidations   = CountHelper::count($arrayValidations);
        $this->assertEquals(1, $qtdValidations);
        $this->assertEquals('xx', $arrayValidations[0][0]);
        $this->assertInstanceOf(TMaxLengthValidator::class, $arrayValidations[0][1]);
    }
    //-----------------------------------------------------
    /*
    public function testRemoveValidation()
    {
        

        $this->classTest->addValidation('xx', new TMaxLengthValidator, array(5));
        $this->classTest->setRequired(true);
        //var_dump($this->classTest);
        $arrayValidations = $this->classTest->getValidations();
        $qtdValidations   = CountHelper::count($arrayValidations);
        $this->assertEquals(2, $qtdValidations);
        
        $this->classTest->removeValidation(null);
        $arrayValidations = $this->classTest->getValidations();
        $qtdValidations   = CountHelper::count($arrayValidations);
        $this->assertEquals(0, $qtdValidations);        
    }
    */
    //-----------------------------------------------------
    public function test_SetValueTText()
    {
        $id = 'idTest';
        $adiantiObj = new TText($id);
        $test = new TFormDinGenericField($adiantiObj,$id,'Texto1',null,'abc');
        $result = $test->getValue();
        $this->assertEquals('abc', $result);
    }

    public function test_SetValueTEntry()
    {
        $id = 'idTest';
        $adiantiObj = new TEntry($id);
        $test = new TFormDinGenericField($adiantiObj,$id,'Texto1',null,'abc');
        $result = $test->getValue();
        $this->assertEquals('abc', $result);
    }

    public function test_SetValueTNumericInt()
    {
        $id = 'idTest';
        $adiantiObj = new TNumeric('numeric', 2, ',', '.', true);
        $test = new TFormDinGenericField($adiantiObj,$id,'Texto1',null,123);
        $result = $test->getValue();
        $this->assertEquals('123,00', $result);
    }

    public function test_SetValueTNumericString()
    {
        $id = 'idTest';
        $adiantiObj = new TNumeric('numeric', 2, ',', '.', true);
        $test = new TFormDinGenericField($adiantiObj,$id,'Texto1',null,'123,00');
        $result = $test->getValue();
        $this->assertEquals('123,00', $result);
    }

    public function testSetAdiantiObj_failNullLavel()
    {
        $this->expectNotToPerformAssertions();
        
        $id = 'id1';
        $adiantiObj = new TText($id);
        $classTest = new TFormDinGenericField($adiantiObj,$id,null);
    }

    public function testsetLabel()
    {
        $labelTxtEsperado = 'kkk';
        $this->classTest->setLabel($labelTxtEsperado,true);
        $labelAdiantiObj = $this->classTest->getLabel();
        $label = $this->classTest->getLabelTxt();
        $this->assertInstanceOf(TLabel::class, $labelAdiantiObj);
        $this->assertEquals($labelTxtEsperado, $label);
    }

    public function testPlaceHolder()
    {
        $expect = 'Texto Exemplo';
        $this->classTest->setPlaceHolder($expect);
        $result = $this->classTest->getPlaceHolder();
        $this->assertEquals($expect, $result);
    }

    public function testSetTooltip_title()
    {
        $tooltip = 'aaa';
        $this->classTest->setTooltip($tooltip);
        $restut = $this->classTest->getTooltip();
        $this->assertEquals($tooltip, $restut);
    }

    public function testSetTooltip_text()
    {
        $tooltip = 'bbb';
        $this->classTest->setTooltip(null,$tooltip);
        $restut = $this->classTest->getTooltip();
        $this->assertEquals($tooltip, $restut);
    }

    public function testSetTooltip_TitleAndText()
    {
        $title = 'aaa';
        $text = 'bbb';
        $this->classTest->setTooltip($title,$text);
        $restut = $this->classTest->getTooltip();
        $this->assertEquals($title, $restut);
    }
    public function testExampleText()
    {
        $text = 'bbb';
        $this->classTest->setExampleText($text);
        $restut = $this->classTest->getExampleText();
        $this->assertEquals($text, $restut);
    }

    public function testReadOnly_True()
    {
        $expect = true;
        $this->classTest->setReadOnly($expect);
        $result = $this->classTest->getReadOnly();
        $this->assertEquals($expect, $result);
    }

    public function testReadOnly_False()
    {
        $expect = false;
        $this->classTest->setReadOnly($expect);
        $result = $this->classTest->getReadOnly();
        $this->assertEquals($expect, $result);
    }

    public function testClass()
    {
        $this->classTest->setClass('formdin5');
        $this->classTest->setClass('btn');
        $result = $this->classTest->getClass();
        $this->assertEquals('formdin5 btn', $result);
    }
}