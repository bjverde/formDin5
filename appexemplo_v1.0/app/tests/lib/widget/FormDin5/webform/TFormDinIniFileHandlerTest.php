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
        $this->expectException(Exception::class);
        $result = $this->classTest->getSection('pauli');
        //$this->assertEquals($expected, $result);
    }

    public function testGetKeyInSection_ok()
    {
        $expected='test';
        $result = $this->classTest->getKeyInSection('config','ambiente');
        $this->assertEquals($expected, $result);
    }
    public function testGetKeyInSection_Exception1()
    {
        $this->expectException(Exception::class);
        $result = $this->classTest->getKeyInSection('config','pauli');
    }
    public function testGetKeyInSection_Exception2()
    {
        $this->expectException(Exception::class);
        $result = $this->classTest->getKeyInSection('pauli','pauli');
    }
}