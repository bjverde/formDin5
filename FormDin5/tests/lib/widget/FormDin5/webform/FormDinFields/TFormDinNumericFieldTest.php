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

class TFormDinNumericFieldTest extends TestCase
{

    private $classTest;
    
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->classTest = new TFormDinNumericField('testN','Test Numeric');
    }
    
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown(): void {
        $this->classTest = null;
        parent::tearDown();
    }     
    
    public function test_instanceOff()
    {
        $adiantiObj = $this->classTest->getAdiantiObj();
        $this->assertInstanceOf(TNumeric::class, $adiantiObj);
    }
    //------------------------------------------------------------------------
    public function testThousandSeparator_null()
    {
        $thousandSeparatorReturn = $this->classTest->getThousandSeparator();
        $this->assertEquals(null, $thousandSeparatorReturn);
    }
    public function testThousandSeparator_FalseNull()
    {
        $test = new TFormDinNumericField('testN','Test Numeric',null,null,null,null,null,null,null,false);
        $thousandSeparatorReturn = $test->getThousandSeparator();
        $this->assertEquals(null, $thousandSeparatorReturn);
    }
    public function testThousandSeparator_TrueDot()
    {
        $test = new TFormDinNumericField('testN','Test Numeric',null,null,null,null,null,null,null,true);
        $thousandSeparatorReturn = $test->getThousandSeparator();
        $this->assertEquals(TFormDinNumericField::DOT, $thousandSeparatorReturn);
    }
    public function testThousandSeparator_Dot()
    {
        $test = new TFormDinNumericField('testN','Test Numeric',null,null,null,null,null,null,null,'.');
        $thousandSeparatorReturn = $test->getThousandSeparator();
        $this->assertEquals(TFormDinNumericField::DOT, $thousandSeparatorReturn);
    }
    public function testThousandSeparator_Comma()
    {
        $test = new TFormDinNumericField('testN','Test Numeric',null,null,null,null,null,null,null,',');
        $thousandSeparatorReturn = $test->getThousandSeparator();
        $this->assertEquals(TFormDinNumericField::COMMA, $thousandSeparatorReturn);
    }

    //------------------------------------------------------------------------
    public function testDecimalsSeparator_null()
    {
        $decimalsSeparator = $this->classTest->getDecimalsSeparator();
        $this->assertEquals(TFormDinNumericField::COMMA, $decimalsSeparator);
    }
    public function testDecimalsSeparator_FalseNull()
    {
        $test = new TFormDinNumericField('testN','Test Numeric');
        $test->setDecimalsSeparator(false);
        $decimalsSeparator = $test->getDecimalsSeparator();
        $this->assertEquals(TFormDinNumericField::COMMA, $decimalsSeparator);
    }
    public function testDecimalsSeparator_TrueComma()
    {
        $test = new TFormDinNumericField('testN','Test Numeric');
        $test->setDecimalsSeparator(true);        
        $decimalsSeparator = $test->getDecimalsSeparator();
        $this->assertEquals(TFormDinNumericField::COMMA, $decimalsSeparator);
    }
    public function testDecimalsSeparator_Dot()
    {
        $test = new TFormDinNumericField('testN','Test Numeric');
        $test->setDecimalsSeparator('.');  
        $decimalsSeparator = $test->getDecimalsSeparator();
        $this->assertEquals(TFormDinNumericField::DOT, $decimalsSeparator);
    }
    public function testDecimalsSeparator_Comma()
    {
        $test = new TFormDinNumericField('testN','Test Numeric');
        $test->setDecimalsSeparator(',');
        $decimalsSeparator = $test->getDecimalsSeparator();
        $this->assertEquals(TFormDinNumericField::COMMA, $decimalsSeparator);
    }

    //------------------------------------------------------------------------
    public function test_readOnly()
    {
        $reflectionProperty = new \ReflectionProperty(TNumeric::class, 'editable');
        $reflectionProperty->setAccessible(true);

        $this->classTest->setReadOnly(true);
        $readOnly = $this->classTest->getReadOnly();
        $adiantiObj = $this->classTest->getAdiantiObj();
        $editable = $reflectionProperty->getValue($adiantiObj);
        
        $this->assertEquals(false,$editable);
        $this->assertEquals(true,$readOnly);
    }

}