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
        //$mock = new StdClass;
        $mock = new mockFormDinComAdianti();
        $this->classTest = new TFormDinGrid($mock,'grid');
    }
    
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown(): void {
        $this->classTest = null;
        parent::tearDown();
    }
    
    public function testGetId()
    {
        $expected  = 'gdxy';
        $this->classTest->setId('gdxy');
        $result = $this->classTest->getId();
        $this->assertEquals($expected, $result);
    }

    public function testSetId_null()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->classTest->setId(null);
    }

    public function testConstruct_Height()
    {
        $this->expectNotToPerformAssertions();
        $mock = new StdClass;
        $grid = new TFormDinGrid($mock,'grid',null,null,700);
    }
    public function testConstruct_Width()
    {
        $this->expectNotToPerformAssertions();
        $mock = new StdClass;
        $grid = new TFormDinGrid($mock,'grid',null,null,null,700);
    }
    /*
    public function testConstruct_FailOldScript()
    {
        $this->expectError();
        $grid = new TFormDinGrid('grid','grid');
    }
    */

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

    public function testShow_semColunas()
    {
        $this->expectException(InvalidArgumentException::class);
        $objResult = $this->classTest->show();
        $this->assertInstanceOf(BootstrapDatagridWrapper::class, $objResult);
    }

    public function testShow_comColunas()
    {
        $this->classTest->addColumn('id',  'id', null, 'center');
        $this->classTest->addColumn('descricao',  'Descrição', null, 'left');
        $objResult = $this->classTest->show();
        $this->assertInstanceOf(BootstrapDatagridWrapper::class, $objResult);
    }

    public function testShow_withData()
    {
        $this->classTest->addColumn('id', 'id', null, 'center');
        $this->classTest->setData([
            (object)['id' => 1, 'descricao' => 'Item 1'],
            (object)['id' => 2, 'descricao' => 'Item 2']
        ]);
        $objResult = $this->classTest->show();
        $this->assertInstanceOf(BootstrapDatagridWrapper::class, $objResult);
    }

    public function testFooter()
    {
        $this->classTest->addFooter('xxx');
        $result = $this->classTest->getFooter();
        $this->assertInstanceOf(TElement::class, $result);
    }

    public function testSetAdiantiObj_fail()
    {
        $this->expectException(InvalidArgumentException::class);
        $item = new StdClass;
        $this->classTest->setAdiantiObj($item);
    }

    public function testGetWidth_fail()
    {
        $this->expectNotToPerformAssertions();
        $this->classTest->getWidth();
    }

    public function testSetWidth_fail()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->classTest->setWidth('');
    }

    public function testGetHeight_fail()
    {
        $this->expectNotToPerformAssertions();
        $this->classTest->getHeight();
    }

    public function testSetHeight_fail()
    {
        $this->expectNotToPerformAssertions();
        $this->classTest->setHeight(100);
    }

    public function testGetUpdateFields_formDin()
    {
        $expected  = ['code'=>'{code}','nome'=>'{nome}'];
        $arrayData = 'code|code,nome|nome';
        $this->classTest->setUpdateFields($arrayData);
        $result = $this->classTest->getUpdateFields();
        $this->assertEquals($expected, $result);
    }

    public function testClearUpdateFields_formDin()
    {
        $expected  = null;
        $arrayData = 'code|code,nome|nome';
        $this->classTest->setUpdateFields($arrayData);
        $this->classTest->clearUpdateFields();
        $result = $this->classTest->getUpdateFields();
        $this->assertEquals($expected, $result);
    }

    public function testRealTotalRowsSqlPaginator()
    {
        $this->classTest->setRealTotalRowsSqlPaginator(100);
        $this->assertEquals(100, $this->classTest->getRealTotalRowsSqlPaginator());
    }

    public function testQtdColumns()
    {
        $this->classTest->setQtdColumns(5);
        $this->assertEquals(5, $this->classTest->getQtdColumns());
    }

    public function testTitle()
    {
        $this->classTest->setTitle('Test Title');
        $this->assertEquals('Test Title', $this->classTest->getTitle());
    }

    public function testKey()
    {
        $this->classTest->setKey('my_key');
        $this->assertEquals('my_key', $this->classTest->getKey());
    }

    public function testOnDrawActionButton()
    {
        $this->classTest->setOnDrawActionButton('my_function');
        $this->assertEquals('my_function', $this->classTest->getOnDrawActionButton());
    }

    public function testPageNavigation()
    {
        $pageNav = new TPageNavigation();
        $this->classTest->setPageNavigation($pageNav);
        $this->assertSame($pageNav, $this->classTest->getPageNavigation());
    }

    public function testPageNavigation_fail()
    {
        $this->expectException(InvalidArgumentException::class);
        $item = new StdClass;
        $this->classTest->setPageNavigation($item);
    }

    public function testMaxRows()
    {
        $this->classTest->setMaxRows(50);
        $this->assertEquals(50, $this->classTest->getMaxRows());
    }

    public function testSetLimit()
    {
        $this->classTest->setLimit(30);
        $this->assertEquals(30, $this->classTest->getMaxRows());
    }

    public function testEnableActionGroup()
    {
        $this->classTest->enableActionGroup(true);
        $this->assertTrue($this->classTest->getEnableActionGroup());
    }

    public function testData()
    {
        $data = ['row1', 'row2'];
        $this->classTest->setData($data);
        $this->assertEquals($data, $this->classTest->getData());
    }

    public function testExportProperties()
    {
        $this->classTest->setExportCsv(true);
        $this->assertTrue($this->classTest->getExportCsv());

        $this->classTest->setExportExcel(true);
        $this->assertTrue($this->classTest->getExportExcel());

        $this->classTest->setExportPdf(true);
        $this->assertTrue($this->classTest->getExportPdf());

        $this->classTest->setExportXml(true);
        $this->assertTrue($this->classTest->getExportXml());

        $this->classTest->setExportShowGroup(true);
        $this->assertTrue($this->classTest->getExportShowGroup());
    }

    public function testAddColumnFormatDate()
    {
        $col = $this->classTest->addColumnFormatDate('data_nasc', 'Data de Nascimento', '100px', 'center', 'd/m/Y');
        $this->assertInstanceOf(TFormDinGridColumnFormatDate::class, $col);
        $this->assertArrayHasKey('data_nasc', $this->classTest->getListColumn());
    }

    public function testAddColumnFormatCpfCnpj()
    {
        $col = $this->classTest->addColumnFormatCpfCnpj('cpf', 'CPF', '150px', 'center');
        $this->assertInstanceOf(TFormDinGridColumnFormatCpfCnpj::class, $col);
    }

    public function testAddCheckColumn()
    {
        if (!class_exists('TGridCheckColumn')) {
            eval('class TGridCheckColumn { public function __construct($name, $title, $keyField, $descField, $readOnly, $checkAll) {} }');
        }
        $col = $this->classTest->addCheckColumn('check_id', 'Selecionar');
        $this->assertInstanceOf(TGridCheckColumn::class, $col);
    }

    public function testAddElementColumnList()
    {
        $this->classTest->addElementColumnList('simple', 'id', 'Label', '100px', 'left');
        $this->assertNotEmpty($this->classTest->getListColumn());
    }

    public function testAddListColumn_fail()
    {
        $this->expectException(InvalidArgumentException::class);
        $item = new StdClass;
        $this->classTest->addListColumn($item);
    }

    public function testAddButton_withUpdateFields()
    {
        $this->classTest->addColumn('id', 'ID');
        $btn = $this->classTest->addButton('Action', 'onAction');
        $this->assertInstanceOf(TFormDinGridAction::class, $btn);
    }

    public function testAddButton_withoutUpdateFields_fail()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->classTest->addButton('Action', 'onAction');
    }

    public function testAddListGridAction_fail()
    {
        $this->expectException(InvalidArgumentException::class);
        $item = new StdClass;
        $this->classTest->addListGridAction($item);
    }

    public function testSetActionSide()
    {
        $this->classTest->setActionSide('right');
        $this->assertTrue(true);
    }

    public function testSetActionSide_fail()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->classTest->setActionSide('invalid_side');
    }

    public function testDataTable()
    {
        $this->classTest->enableDataTable();
        $this->assertEquals('true', $this->classTest->getAdiantiObj()->datatable);

        $this->classTest->disableDataTable();
        $this->assertEquals('false', $this->classTest->getAdiantiObj()->datatable);
    }

    public function testShowGridAction_defaultButtons()
    {
        $this->classTest->enableDefaultButtons(true);
        $this->classTest->setCreateDefaultEditButton(true);
        $this->classTest->setCreateDefaultDeleteButton(true);
        
        $this->classTest->addColumn('id', 'ID'); // Need column for default buttons to get update fields
        
        $this->classTest->showGridAction();
        // Just verify it doesn't crash, the buttons are added internally
        $this->assertTrue(true);
    }

    public function testShowGridAction_customButtons_noGroup()
    {
        $this->classTest->addColumn('id', 'ID');
        $this->classTest->enableActionGroup(false);
        $this->classTest->addButton('Custom', 'onCustom');
        $this->classTest->showGridAction();
        $this->assertTrue(true);
    }

    public function testShowGridAction_customButtons_withGroup()
    {
        $this->classTest->addColumn('id', 'ID');
        $this->classTest->enableActionGroup(true);
        $this->classTest->addButton('Custom', 'onCustom');
        $this->classTest->showGridAction();
        $this->assertTrue(true);
    }

    public function testShowGridExport()
    {
        $this->classTest->setExportCsv(true);
        $this->classTest->setExportPdf(true);
        $this->classTest->setExportExcel(true);
        $this->classTest->setExportXml(true);

        $this->classTest->setExportShowGroup(true);
        $this->classTest->showGridExport();
        $this->assertTrue(true);

        $this->classTest->setExportShowGroup(false);
        $this->classTest->showGridExport();
        $this->assertTrue(true);
    }

    public function testMinWidth()
    {
        $this->classTest->setMinWidth('200');
        $this->assertEquals('200', $this->classTest->getMinWidth());
    }

    public function testListColumn()
    {
        $this->classTest->setListColumn([]);
        $this->assertEquals([], $this->classTest->getListColumn());
    }

    public function testListGridAction()
    {
        $this->classTest->setListGridAction([]);
        $this->assertEquals([], $this->classTest->getListGridAction());
    }

    public function testCreateDefaultButtonsGetters()
    {
        $this->assertTrue($this->classTest->getCreateDefaultButtons());
        
        $this->classTest->setCreateDefaultEditButton(false);
        $this->assertFalse($this->classTest->getCreateDefaultEditButton());
        
        $this->classTest->setCreateDefaultDeleteButton(false);
        $this->assertFalse($this->classTest->getCreateDefaultDeleteButton());
    }

    public function testActionGroup()
    {
        $this->classTest->setActionGroup('Label', 'fa:icon');
        $this->assertInstanceOf(TDataGridActionGroup::class, $this->classTest->getActionGroup());
    }

    public function testAdiantiObj()
    {
        $this->assertInstanceOf(BootstrapDatagridWrapper::class, $this->classTest->getAdiantiObj());
    }

    public function testSetDataGrid_Private()
    {
        $reflection = new ReflectionClass(TFormDinGrid::class);
        $method = $reflection->getMethod('setDataGrid');
        $method->setAccessible(true);
        
        $dataGrid = new TDataGrid();
        $method->invokeArgs($this->classTest, [$dataGrid]);

        $getMethod = $reflection->getMethod('getDataGrid');
        $getMethod->setAccessible(true);
        $result = $getMethod->invokeArgs($this->classTest, []);

        $this->assertSame($dataGrid, $result);
    }

    public function testSetDataGrid_Private_Fail()
    {
        $this->expectException(InvalidArgumentException::class);
        
        $reflection = new ReflectionClass(TFormDinGrid::class);
        $method = $reflection->getMethod('setDataGrid');
        $method->setAccessible(true);
        
        $method->invokeArgs($this->classTest, ['not_an_object']);
    }

    public function testGetMixUpdateButton_empty()
    {
        $this->classTest->clearUpdateFields();
        $this->classTest->setListColumn([]);
        $this->classTest->addColumn('col1', 'Column 1');
        
        $result = $this->classTest->getMixUpdateButton(null);
        $this->assertIsArray($result);
        $this->assertEquals(['col1' => '{col1}'], $result);
    }

    public function testConstruct_migrationMessage()
    {
        set_error_handler(function() { return true; });
        try {
            $grid = new TFormDinGrid('string_not_object', 'grid_id');
        } finally {
            restore_error_handler();
        }
        $this->assertTrue(true);
    }

    public function testSetWidth_Valid()
    {
        $this->classTest->setWidth('500px');
        $this->assertEquals('500px', $this->classTest->getWidth());
    }

    public function testSetId_Valid()
    {
        $this->classTest->setId('new_id');
        $this->assertEquals('new_id', $this->classTest->getId());
    }

    public function testSetHeight_Empty()
    {
        $this->classTest->setHeight('');
        $this->assertTrue(true); // Should not throw error
    }
}