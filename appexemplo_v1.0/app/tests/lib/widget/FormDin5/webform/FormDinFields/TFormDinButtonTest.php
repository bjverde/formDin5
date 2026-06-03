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

require_once  __DIR__.'/../../../mockFormAdianti.php';

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Error\Warning;

class TFormDinButtonTest extends TestCase
{

    private $classTest;
    
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp(): void {
        parent::setUp();
        $classForm = new mockFormDinComAdianti();
        $this->classTest = new TFormDinButton($classForm,'Salvar',null,'onSave');
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
        $this->assertInstanceOf(TButton::class, $adiantiObj);
    }

    public function testSetAdiantiObj_failNullObjForm()
    {
        $this->expectException(InvalidArgumentException::class);
        $classTest = new TFormDinButton(null,null,null,null);
    }

    public function testSetAdiantiObj_failNullNameButton()
    {
        $this->expectException(InvalidArgumentException::class);
        
        $classForm = new mockFormDinComAdianti();
        $classTest = new TFormDinButton($classForm,null,null,null);
    }

    public function testSetAdiantiObj_failNullStringName()
    {
        $this->expectException(InvalidArgumentException::class);
        
        $classForm = new mockFormDinComAdianti();
        $classTest = new TFormDinButton($classForm,'Salvar',null,null);
    }

    /*
    public function testSetAdiantiObj_failArrayName()
    {
        $this->expectError();
        
        $classForm = new mockFormDinComAdianti();
        $list = array('Salvar','Buscar');
        $classTest = new TFormDinButton($classForm,$list,null,'onSave');
    }
    */

    public function testConstructor_ActionArray()
    {
        $classForm = new mockFormDinComAdianti();
        $btn = new TFormDinButton($classForm, 'Salvar', null, ['mockFormDinComAdianti', 'onSave']);
        $this->assertInstanceOf(TButton::class, $btn->getAdiantiObj());
    }

    public function testSetObjForm_NotObject()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->classTest->setObjForm('not_an_object');
    }

    public function testSetLabel_Array()
    {
        set_error_handler(function($errno, $errstr) {
            if ($errno === E_USER_ERROR) {
                $this->assertStringContainsString('O parametro $mixValue não recebe mais um array', $errstr);
                return true;
            }
            return false;
        });
        try {
            $this->classTest->setLabel(['Save', 'Cancel']);
        } finally {
            restore_error_handler();
        }
    }

    public function testSetAdiantiObj_Null()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->classTest->setAdiantiObj(null);
    }

    public function testSetAdiantiObj_NotObject()
    {
        set_error_handler(function($errno, $errstr) {
            if ($errno === E_USER_ERROR) {
                $this->assertStringContainsString('o metodo addButton MUDOU!', $errstr);
                return true;
            }
            return false;
        });
        try {
            $this->classTest->setAdiantiObj('not_an_object');
        } finally {
            restore_error_handler();
        }
    }

    public function testSetAction_Conflict()
    {
        $this->classTest->addFunction('alert(1);');
        $this->expectException(InvalidArgumentException::class);
        $this->classTest->setAction('onSave');
    }

    public function testSetAction_Array()
    {
        $this->classTest->setAction(['mockFormDinComAdianti', 'onSave']);
        $this->assertInstanceOf(TAction::class, $this->classTest->getAdiantiObj()->getAction());
    }

    public function testGetAction()
    {
        $this->classTest->getAction();
        $this->assertTrue(true);
    }

    public function testSetImage()
    {
        $this->classTest->setImage('fa:save');
        $this->assertTrue(true);
    }

    public function testSetPopover()
    {
        $this->classTest->setPopover('title', 'top', 'content');
        $this->assertEquals('content', $this->classTest->getAdiantiObj()->popcontent);
    }

    public function testParameters()
    {
        $this->classTest->setParameter('param1', 'val1');
        $this->assertEquals('val1', $this->classTest->getParameter('param1'));
        $this->assertEquals(['param1' => 'val1'], $this->classTest->getParameters());
    }

    public function testAddFunction()
    {
        $this->classTest->addFunction('alert(1);');
        $this->assertEquals('alert(1);', $this->classTest->getStrOnClick());
    }

    public function testSetConfirmMessage_Conflict()
    {
        $this->classTest->addFunction('alert(1);');
        $this->expectException(InvalidArgumentException::class);
        $this->classTest->setConfirmMessage('Are you sure?');
    }

    public function testSetConfirmMessage_Success()
    {
        $this->classTest->setConfirmMessage('Are you sure?');
        $this->assertEquals('Are you sure?', $this->classTest->getConfirmMessage());
    }

    public function testGetConfirmMessage()
    {
        $this->assertNull($this->classTest->getConfirmMessage());
    }

    public function testGetHint()
    {
        $this->assertNull($this->classTest->getHint());
    }

}