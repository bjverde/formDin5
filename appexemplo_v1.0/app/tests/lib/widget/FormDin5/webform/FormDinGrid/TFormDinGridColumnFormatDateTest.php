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
use PHPUnit\Framework\Error\Notice;
use PHPUnit\Framework\Error\Warning;

class TFormDinGridColumnFormatDateTest extends TestCase
{

    private $classTest;
    private $objForm;
    
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->objForm = new mockFormDinComAdianti();
        $this->classTest = new TFormDinGridColumnFormatDate($this->objForm,'TEST', 'TEST');
    }
    
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown(): void {
        $this->classTest = null;
        parent::tearDown();
    }

    public function testTypeObje()
    {
        $result = $this->classTest->getAdiantiObj();
        $this->assertInstanceOf(TDataGridColumn::class, $result);
    }

    public function testTransformer()
    {
        $arrayTransformer = $this->classTest->getAdiantiObj()->getTransformer();
        $this->assertInstanceOf(TFormDinGridColumnFormatDate::class, $arrayTransformer[0]);
    }

    public function testFormatDateBrasilPHP()
    {
        $result = $this->classTest->formatDate('2020-05-07');
        $this->assertEquals('07/05/2020', $result);
    }

    public function testFormatDateBrasilAdianti()
    {
        $this->classTest->setFormat('dd/mm/yyyy');
        $result = $this->classTest->formatDate('2020-05-07');
        $this->assertEquals('07/05/2020', $result);
    }

    public function testFormatDateEuaPHP()
    {
        $this->classTest->setFormat('m/d/Y');
        $result = $this->classTest->formatDate('2020-05-07');
        $this->assertEquals('05/07/2020', $result);
    }

    public function testFormatDateEuaAdianti()
    {
        $this->classTest->setFormat('mm/dd/yyyy');
        $result = $this->classTest->formatDate('2020-05-07');
        $this->assertEquals('05/07/2020', $result);
    }

    public function testFormatDateTimeEuaPHP()
    {
        $this->classTest->setFormat('m/d/Y hh:ii');
        $result = $this->classTest->formatDate('2020-05-07 23:43');
        $this->assertEquals('05/07/2020 23:43', $result);
    }

    public function testFormatDateTimeEuaAdianti()
    {
        $this->classTest->setFormat('mm/dd/yyyy hh:ii');
        $result = $this->classTest->formatDate('2020-05-07 23:43');
        $this->assertEquals('05/07/2020 23:43', $result);
    }

    public function testFormatDateFinlandPHP()
    {
        $this->classTest->setFormat('d-m-Y');
        $result = $this->classTest->formatDate('2020-05-07');
        $this->assertEquals('07-05-2020', $result);
    }

    public function testFormatDateFinlandAdianti()
    {
        $this->classTest->setFormat('dd-mm-yyyy');
        $result = $this->classTest->formatDate('2020-05-07');
        $this->assertEquals('07-05-2020', $result);
    }
}