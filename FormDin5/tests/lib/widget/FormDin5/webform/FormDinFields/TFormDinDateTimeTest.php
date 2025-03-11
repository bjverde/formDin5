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

use PHPUnit\Framework\Error\Deprecated;
use PHPUnit\Framework\Error\Notice;
use PHPUnit\Framework\Error\Warning;
use PHPUnit\Framework\TestCase;

class TFormDinDateTimeTest extends TestCase
{

    private $classTest;
    
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->classTest = new TFormDinDateTime('t1','Data Test');
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
        $this->assertInstanceOf(TDateTime::class, $adiantiObj);
    }
    
    public function test_readOnly()
    {
        $reflectionProperty = new \ReflectionProperty(TDateTime::class, 'editable');
        $reflectionProperty->setAccessible(true);

        $this->classTest->setReadOnly(true);
        $readOnly = $this->classTest->getReadOnly();
        $adiantiObj = $this->classTest->getAdiantiObj();
        $editable = $reflectionProperty->getValue($adiantiObj);
        
        $this->assertEquals(false,$editable);
        $this->assertEquals(true,$readOnly);
    }    
    //-----------------------------------------------------    
    public function testMask()
    {
        $this->classTest->setMask('yyyy-mm-dd');
        $result = $this->classTest->getMask();
        $this->assertEquals('yyyy-mm-dd',$result);
    }
    public function testDatabaseMaskDefault()
    {
        $result = $this->classTest->getDatabaseMask();
        $this->assertEquals('yyyy-mm-dd hh:ii:ss',$result);
    }     
    public function testDatabaseMask()
    {
        $this->classTest->setDatabaseMask('yyyy-mm-dd');
        $result = $this->classTest->getDatabaseMask();
        $this->assertEquals('yyyy-mm-dd',$result);
    }
    //-----------------------------------------------------
    public function testMaxValue()
    {
        $this->classTest->setMaxValue('10/05/2020');
        $result = $this->classTest->getMaxValue();
        $this->assertEquals('10/05/2020',$result);
    }
    public function testMinValue()
    {
        $this->classTest->setMinValue('2020-04-01');
        $result = $this->classTest->getMinValue();
        $this->assertEquals('2020-04-01',$result);
    }
    //-----------------------------------------------------
    /*
    public function testSetButtonVisible()
    {
        $this->expectWarning();
        $this->classTest->setButtonVisible(true);
    }
    */
    public function testGetButtonVisible()
    {
        $result = $this->classTest->getButtonVisible();
        $this->assertEquals(true,$result);
    }    

}