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

class TFormDinIniFileHandlerTest extends TestCase
{

    private $classTest;
    
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp(): void {
        parent::setUp();
        $filePath = __DIR__.'/../../mockConfigIni.ini';
        $this->classTest = new TFormDinIniFileHandler($filePath);
    }
    
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown(): void {
        $this->classTest = null;
        parent::tearDown();
    }

    public function testGetfilePath()
    {
        $result = $this->classTest->getfilePath();
        $this->assertEquals(__DIR__.'/../../mockConfigIni.ini', $result);
    }

    public function testGetSection_ok()
    {
        $expected =array();
        $expected['ambiente']='test';
        $expected['nome_sistema_acesso']="mock";

        $result = $this->classTest->getSection('config');
        $this->assertEquals($expected, $result);
    }

    public function testGetSection_Exception()
    {
        $this->expectException(LogicException::class);
        $result = $this->classTest->getSection('pauli');
    }

    public function testGetKeyInSection_ok()
    {
        $expected='test';
        $result = $this->classTest->getKeyInSection('config','ambiente');
        $this->assertEquals($expected, $result);
    }
    public function testGetKeyInSection_Exception1()
    {
        $this->expectException(LogicException::class);
        $result = $this->classTest->getKeyInSection('config','pauli');
    }
    public function testGetKeyInSection_Exception2()
    {
        $this->expectException(LogicException::class);
        $result = $this->classTest->getKeyInSection('pauli','pauli');
    }

    public function testFile_Exception(): void {
        $this->expectException(Exception::class);
        $filePath = __DIR__.'/../../mockConfigIni-falha.ini';
        $classTest = new TFormDinIniFileHandler($filePath);
    }

    public function testConstructWithoutPath()
    {
        $handler = new TFormDinIniFileHandler();
        $this->assertNull($handler->getfilePath());

        $handler2 = new TFormDinIniFileHandler(null, INI_SCANNER_TYPED);
        $this->assertNull($handler2->getfilePath());
    }

    public function testSetIniData()
    {
        $handler = new TFormDinIniFileHandler();
        $data = ['sec' => ['key' => 'val']];
        $handler->setIniData($data);
        $this->assertEquals('val', $handler->getKeyInSection('sec', 'key'));
    }

    public function testGetKeyWithBolean()
    {
        $handler = new TFormDinIniFileHandler();
        $data = [
            'sec' => [
                't1' => '1',
                't2' => 'true',
                't3' => 'SIM',
                'f1' => '0',
                'f2' => 'false',
                'f3' => 'N'
            ]
        ];
        $handler->setIniData($data);
        $this->assertTrue($handler->getKeyWithBolean('sec', 't1'));
        $this->assertTrue($handler->getKeyWithBolean('sec', 't2'));
        $this->assertTrue($handler->getKeyWithBolean('sec', 't3'));
        $this->assertFalse($handler->getKeyWithBolean('sec', 'f1'));
        $this->assertFalse($handler->getKeyWithBolean('sec', 'f2'));
        $this->assertFalse($handler->getKeyWithBolean('sec', 'f3'));
    }

    public function testSetValue()
    {
        $handler = new TFormDinIniFileHandler();
        $handler->setIniData([]);
        $handler->setValue('sec', 'key', 'val');
        $this->assertEquals('val', $handler->getKeyInSection('sec', 'key'));

        $handler->setValue('sec', 'key', 'newval');
        $this->assertEquals('newval', $handler->getKeyInSection('sec', 'key'));
    }

    public function testSave()
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'ini_');
        $handler = new TFormDinIniFileHandler();
        $handler->setfilePath($tempFile);
        $handler->setValue('sec1', 'k1', 'v1');
        $handler->setValue('sec2', 'k2', 'v2');
        $handler->save();

        $reader = new TFormDinIniFileHandler($tempFile);
        $this->assertEquals('v1', $reader->getKeyInSection('sec1', 'k1'));
        $this->assertEquals('v2', $reader->getKeyInSection('sec2', 'k2'));

        @unlink($tempFile);
    }

    public function testStaticBolean()
    {
        $this->assertTrue(TFormDinIniFileHandler::testBolean('1'));
        $this->assertTrue(TFormDinIniFileHandler::testBolean('true'));
        $this->assertTrue(TFormDinIniFileHandler::testBolean('TRUE'));
        $this->assertTrue(TFormDinIniFileHandler::testBolean('SIM'));
        $this->assertTrue(TFormDinIniFileHandler::testBolean('S'));
        $this->assertTrue(TFormDinIniFileHandler::testBolean('YES'));
        $this->assertTrue(TFormDinIniFileHandler::testBolean('Y'));

        $this->assertFalse(TFormDinIniFileHandler::testBolean('0'));
        $this->assertFalse(TFormDinIniFileHandler::testBolean('false'));
        $this->assertFalse(TFormDinIniFileHandler::testBolean('FALSE'));
        $this->assertFalse(TFormDinIniFileHandler::testBolean('NAO'));
        $this->assertFalse(TFormDinIniFileHandler::testBolean('N'));
        $this->assertFalse(TFormDinIniFileHandler::testBolean('NO'));
    }
}