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
 * ou escreva para a Fundação do Software Livre (FSF) Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA 02111-1301, USA.
 */

require_once  __DIR__.'/../../mockFormDinArray.php';

use PHPUnit\Framework\TestCase;

class TFormDinDateValidatorMinTest extends TestCase
{

    private $classTest;
    
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->classTest = new TFormDinDateValidatorMin();
    }
    
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown(): void {
        $this->classTest = null;
        parent::tearDown();
    }

    public function test_null() {
        $this->expectNotToPerformAssertions();
        $label = 'Teste';
        $value = null;
        $parameters[0]='dd/mm/yyyy hh:ii';
        $parameters[1]='30/08/2020 23:34';
        $this->classTest->validate($label, $value, $parameters);
    }    

    public function testBr_date202008152334limit202008152333() {
        $this->expectNotToPerformAssertions();
        $label = 'Teste';
        $value = '15/08/2020 23:34';
        $parameters[0]='dd/mm/yyyy hh:ii';
        $parameters[1]='15/08/2020 23:33';
        $this->classTest->validate($label, $value, $parameters);
    }

    public function testBr_date202008152333limit202008152332() {
        $this->expectNotToPerformAssertions();
        $label = 'Teste';
        $value = '15/08/2020 23:33';
        $parameters[0]='dd/mm/yyyy hh:ii';
        $parameters[1]='15/08/2020 23:32';
        $this->classTest->validate($label, $value, $parameters);
    }

    public function testBr_date20200815limit20200801() {
        $this->expectNotToPerformAssertions();
        $label = 'Teste';
        $value = '15/08/2020';
        $parameters[0]='dd/mm/yyyy';
        $parameters[1]='01/08/2020';
        $this->classTest->validate($label, $value, $parameters);
    }

    public function testBr_date20200815limit20200830() {
        $this->expectException(InvalidArgumentException::class);
        $label = 'Teste';
        $value = '15/08/2020';
        $parameters[0]='dd/mm/yyyy';
        $parameters[1]='30/08/2020';
        $this->classTest->validate($label, $value, $parameters);
    }

    public function testBr_date202008limit202009() {
        $this->expectException(InvalidArgumentException::class);
        
        $label = 'Teste';
        $value = '08/2020';
        $parameters[0]='mm/yyyy';
        $parameters[1]='09/2020';
        $this->classTest->validate($label, $value, $parameters);
    }

    public function testBr_date202008limit202007() {
        $this->expectNotToPerformAssertions();
        $label = 'Teste';
        $value = '08/2020';
        $parameters[0]='mm/yyyy';
        $parameters[1]='07/2020';
        $this->classTest->validate($label, $value, $parameters);
    }

    public function testBr_date2020limit2021() {
        $this->expectException(InvalidArgumentException::class);        
        $label = 'Teste';
        $value = '2020';
        $parameters[0]='yyyy';
        $parameters[1]='2021';
        $this->classTest->validate($label, $value, $parameters);
    }

    public function testBr_date2020limit2019() {
        $this->expectNotToPerformAssertions();
        $label = 'Teste';
        $value = '2020';
        $parameters[0]='yyyy';
        $parameters[1]='2019';
        $this->classTest->validate($label, $value, $parameters);
    }

    public function testBr_date0815limit0830() {
        $this->expectException(InvalidArgumentException::class);        
        $label = 'Teste';
        $value = '15/08';
        $parameters[0]='dd/mm';
        $parameters[1]='30/08';
        $this->classTest->validate($label, $value, $parameters);
    }

    public function testBr_date0815limit0801() {
        $this->expectNotToPerformAssertions();
        $label = 'Teste';
        $value = '15/08';
        $parameters[0]='dd/mm';
        $parameters[1]='01/08';
        $this->classTest->validate($label, $value, $parameters);
    }

    public function testIso_date202008152333limit202008152334() {
        $this->expectException(InvalidArgumentException::class);        
        $label = 'Teste';
        $value = '2020-08-15 23:33';
        $parameters[0]='yyyy-mm-dd hh:ii';
        $parameters[1]='2020-08-15 23:34';
        $this->classTest->validate($label, $value, $parameters);
    }

    public function testIso_date202008152333limit202008152332() {
        $this->expectNotToPerformAssertions();
        $label = 'Teste';
        $value = '2020-08-15 23:33';
        $parameters[0]='yyyy-mm-dd hh:ii';
        $parameters[1]='2020-08-15 23:32';
        $this->classTest->validate($label, $value, $parameters);
    }


    public function testIso_date20200815limit20200830() {
        $this->expectException(InvalidArgumentException::class);        
        $label = 'Teste';
        $value = '2020-08-15';
        $parameters[0]='yyyy-mm-dd';
        $parameters[1]='2020-08-30';
        $this->classTest->validate($label, $value, $parameters);
    }

    public function testIso_date20200815limit20200801() {
        $this->expectNotToPerformAssertions();
        $label = 'Teste';
        $value = '2020-08-15';
        $parameters[0]='yyyy-mm-dd';
        $parameters[1]='2020-08-01';
        $this->classTest->validate($label, $value, $parameters);
    }

    public function testIso_date202008limit202009() {
        $this->expectException(InvalidArgumentException::class);
        $label = 'Teste';
        $value = '2020-08';
        $parameters[0]='yyyy-mm';
        $parameters[1]='2020-09';
        $this->classTest->validate($label, $value, $parameters);
    }

    public function testIso_date202008limit202007() {
        $this->expectNotToPerformAssertions();        
        $label = 'Teste';
        $value = '2020-08';
        $parameters[0]='yyyy-mm';
        $parameters[1]='2020-07';
        $this->classTest->validate($label, $value, $parameters);
    }

    public function testBr_date202008152333limit202008152334() {
        $this->expectException(InvalidArgumentException::class);
        $label = 'Teste';
        $value = '08/15/2020 23:33';
        $parameters[0]='mm/dd/yyyy hh:ii';
        $parameters[1]='08/15/2020 23:34';
        $this->classTest->validate($label, $value, $parameters);
    }

    public function testEua_date202008152333limit202008152332() {
        $this->expectNotToPerformAssertions();
        $label = 'Teste';
        $value = '08/15/2020 23:33';
        $parameters[0]='mm/dd/yyyy hh:ii';
        $parameters[1]='08/15/2020 23:32';
        $this->classTest->validate($label, $value, $parameters);
    }

    public function testEua_date202008152333limit202008152334() {
        $this->expectException(InvalidArgumentException::class);
        $label = 'Teste';
        $value = '08/15/2020 23:33';
        $parameters[0]='mm/dd/yyyy hh:ii';
        $parameters[1]='08/15/2020 23:34';
        $this->classTest->validate($label, $value, $parameters);
    }
    
}