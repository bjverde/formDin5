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
 * or write to the Free Software Foundation, Inc., 51 Franklin Street,
 * Fifth Floor, Boston, MA  02110-1301, USA.
 * ----------------------------------------------------------------------------
 */

use PHPUnit\Framework\TestCase;

class TFormDinFileFieldMultiTest extends TestCase
{
    private $classTest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->classTest = new TFormDinFileFieldMulti('file_test', 'Upload Files', false, 'jpg,png,pdf');
    }

    protected function tearDown(): void
    {
        $this->classTest = null;
        parent::tearDown();
    }

    public function test_instanceOff()
    {
        $adiantiObj = $this->classTest->getAdiantiObj();
        $this->assertInstanceOf(TMultiFile::class, $adiantiObj);
    }

    public function test_getAndSetAllowedFileTypes()
    {
        $this->assertEquals(array('jpg', 'png', 'pdf'), $this->classTest->getAllowedFileTypes());

        $this->classTest->setAllowedFileTypes('gif,txt');
        $this->assertEquals(array('gif', 'txt'), $this->classTest->getAllowedFileTypes());
    }

    public function test_getAndSetId()
    {
        $this->assertEquals('file_test', $this->classTest->getId());

        $this->classTest->setId('custom_file_id');
        $this->assertEquals('custom_file_id', $this->classTest->getId());
    }

    public function testConstructor_ExtraArgs()
    {
        $field = new TFormDinFileFieldMulti('testFileExtra', 'Upload', false, 'jpg', null, null, null, false, false, null);
        $field->setValue('default_val');
        $this->assertEquals('default_val', $field->getAdiantiObj()->getValue());
    }

    public function testSetAllowedFileTypes_Empty()
    {
        $this->classTest->setAllowedFileTypes(null);
        $this->assertEquals('', $this->classTest->getAllowedFileTypes());
    }

    public function testCompleteAndErrorActions()
    {
        $action = new TAction(array('mockFormDinComAdianti', 'onSaveStatic'));
        $this->classTest->setCompleteAction($action);
        
        try {
            $this->classTest->setErrorAction($action);
            $this->fail('Expected exception for missing setErrorAction on TMultiFile was not thrown');
        } catch (Exception $e) {
            $this->assertStringContainsString('setErrorAction', $e->getMessage());
        }
    }

    public function testEnableFileHandling()
    {
        $this->classTest->enableFileHandling(true);
        $this->assertTrue(true);
    }

    public function testEnablePopover()
    {
        $this->classTest->enablePopover('Title', 'Content');
        $this->assertTrue(true);
    }

    public function testSetService()
    {
        $this->classTest->setService('MyService');
        $this->assertTrue(true);
    }

    public function testEnableImageGallery()
    {
        $this->classTest->enableImageGallery(150, 150);
        $this->assertTrue(true);
    }
}
