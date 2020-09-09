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

$path =   __DIR__.'/../../../';
require_once $path.'mockFormDinArray.php';


use PHPUnit\Framework\Error\Deprecated;
use PHPUnit\Framework\Error\Notice;
use PHPUnit\Framework\Error\Warning;
use PHPUnit\Framework\TestCase;

class TFormDinCheckListTest extends TestCase
{

    private $classTest;
    
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp(): void {
        parent::setUp();
        $mock = new mockFormDinArray ();
        $listItems = $mock->generateTablePessoaAdianti();
        $checkList = new TFormDinCheckList('checkPessoa','Selecione a Pessoa',false,$listItems);
        $checkList->addColumn('IDPESSOA','Id Pessoa','center','10%');
        $checkList->addColumn('NMPESSOA','Nome','center','90%');
        $this->classTest = $checkList;
    }
    
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown(): void {
        $this->classTest = null;
        parent::tearDown();
    }

    public function testAddItems_failArrayNullException()
    {
        $this->expectException(InvalidArgumentException::class);
        $checkList = new TFormDinCheckList('checkPessoa','Selecione a Pessoa',false,null);
    }    

    public function testInstanceOff_TCheckList()
    {
        $adiantiObj = $this->classTest->getObjCheck();
        $this->assertInstanceOf(TCheckList::class, $adiantiObj);
    }
    
    public function testConstruct()
    {
        $adiantiObj = $this->classTest->getObjCheck();
        $this->assertEquals(false, $adiantiObj->isScrollable());
        $this->assertEquals('checkPessoa', $this->classTest->getId());
        $this->assertEquals('Selecione a Pessoa', $this->classTest->getLabel());
        $this->assertInstanceOf(TLabel::class, $this->classTest-> getObjLabel());
    }

    public function testGetStringSearch()
    {
        $this->classTest->addColumn('TPPESSOA','Tipo pessoa','center','5%');
        $stringResult = $this->classTest->getStringSearch();
        $expected = 'IDPESSOA,NMPESSOA,TPPESSOA';
        $this->assertEquals($expected , $stringResult);
    }

    public function testAddColumn_InstanceOff_TDataGridColumn()
    {
        $obj = $this->classTest->addColumn('TPPESSOA','Tipo pessoa','center','5%');
        $this->assertInstanceOf(TDataGridColumn::class, $obj);
    }

    public function testaddColumnHidden_InstanceOff_TDataGridColumn()
    {
        $obj = $this->classTest->addColumnHidden('TPPESSOA','Tipo pessoa','center','5%');
        $resultProperty = $obj->getProperty('style');
        $resultDataProperty = $obj->getDataProperty('style');
        $expected = 'display:none';
        $this->assertInstanceOf(TDataGridColumn::class, $obj);
        $this->assertEquals($expected , $resultProperty);
        $this->assertEquals($expected , $resultDataProperty);
    }

    public function testGetInputSearch()
    {
        $adiantiObj = $this->classTest->getInputSearch('x');
        $this->assertEquals('x', $adiantiObj->getName());
        $this->assertInstanceOf(TEntry::class, $adiantiObj);
    }
    
    /*
    public function testInstanceOff_THBox_showTitle()
    {
        $adiantiObj = $this->classTest->showTitle();
        $this->assertInstanceOf(THBox::class, $adiantiObj);
    }
    */
}