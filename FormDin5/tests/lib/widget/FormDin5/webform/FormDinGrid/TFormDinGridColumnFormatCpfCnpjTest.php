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

class TFormDinGridColumnFormatCpfCnpjTest extends TestCase
{
    private $classTest;
    private $objForm;

    protected function setUp(): void {
        parent::setUp();
        $this->objForm = new mockFormDinComAdianti();
        $this->classTest = new TFormDinGridColumnFormatCpfCnpj($this->objForm, 'cpf_cnpj', 'CPF/CNPJ');
    }

    protected function tearDown(): void {
        $this->classTest = null;
        parent::tearDown();
    }

    public function testConstructor()
    {
        $result = $this->classTest->getAdiantiObj();
        $this->assertInstanceOf(TDataGridColumn::class, $result);
        $this->assertEquals('cpf_cnpj', $result->getName());
        
        $transformer = $result->getTransformer();
        $this->assertIsArray($transformer);
        $this->assertInstanceOf(TFormDinGridColumnFormatCpfCnpj::class, $transformer[0]);
        $this->assertEquals('format', $transformer[1]);
    }

    public function testFormatCpf()
    {
        $formatted = $this->classTest->format('12345678909');
        $this->assertEquals('123.456.789-09', $formatted);
    }

    public function testFormatCnpj()
    {
        $formatted = $this->classTest->format('12345678000199');
        $this->assertEquals('12.345.678/0001-99', $formatted);
    }

    public function testFormatInvalid()
    {
        $formatted = $this->classTest->format('123');
        $this->assertEquals('123', $formatted);
    }
}
