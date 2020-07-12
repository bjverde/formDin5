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

class TFormDinGridTest extends TestCase
{

    private $classTest;
    
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp(): void {
        parent::setUp();
        $mock = new StdClass;
        $this->classTest = new TFormDinGrid($mock,'grid');
    }
    
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown(): void {
        $this->classTest = null;
        parent::tearDown();
    }
    

    public function testConstruct_Height()
    {
        $this->expectWarning();
        $mock = new StdClass;
        $grid = new TFormDinGrid($mock,'grid',null,null,700);
    }
    public function testConstruct_Width()
    {
        $this->expectWarning();
        $mock = new StdClass;
        $grid = new TFormDinGrid($mock,'grid',null,null,null,700);
    }
    public function testConstruct_FailOldScript()
    {
        $this->expectError();
        $grid = new TFormDinGrid('grid','grid');
    }

    public function testSetPanelGroupGrid_fail()
    {
        $this->expectException(InvalidArgumentException::class);
        $item = new StdClass;
        $result = $this->classTest->setPanelGroupGrid($item);
    }

    public function testSetObjForm_empty()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->classTest->setObjForm(null);
    }

    public function testSetObjForm_noObjectString()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->classTest->setObjForm('xxx');
    }

    public function testSetObjForm_noObjectArray()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->classTest->setObjForm(array('a'=>1));
    }

    public function testGetObjForm()
    {
        $objEntradaz = new mockFormDinComAdianti();
        $this->classTest->setObjForm($objEntradaz);
        $objResult = $this->classTest->getObjForm();
        $this->assertInstanceOf(mockFormDinComAdianti::class, $objResult);
    }

    public function testFooter()
    {
        $this->classTest->addFooter('xxx');
        $result = $this->classTest->getFooter();
        $this->assertInstanceOf(TElement::class, $result);
        //$this->assertEquals('xxx',$result);
    }

    public function testSetAdiantiObj_fail()
    {
        $this->expectException(InvalidArgumentException::class);
        $item = new StdClass;
        $result = $this->classTest->setAdiantiObj($item);
    }

    public function testGetWidth_fail()
    {
        $this->expectWarning();
        $result = $this->classTest->getWidth();
    }

    public function testSetWidth_fail()
    {
        $this->expectWarning();
        $result = $this->classTest->setWidth(100);
    }

    public function testGetHeight_fail()
    {
        $this->expectWarning();
        $result = $this->classTest->getHeight();
    }

    public function testSetHeight_fail()
    {
        $this->expectWarning();
        $result = $this->classTest->setHeight(100);
    }

}