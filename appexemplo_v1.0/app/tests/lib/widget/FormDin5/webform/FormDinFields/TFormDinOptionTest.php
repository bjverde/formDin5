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

class TFormDinOptionTest extends TestCase
{

    private $classTest;
    
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp(): void {
        parent::setUp();
        $mock = new mockFormDinArray ();
        $mixOptions = $mock->generateTable();
        $id = 'select';
        $adiantiObj = new TSelect($id);
        $select = new TFormDinOption(
                            $adiantiObj      //01: Objeto de campo do Adianti
                           ,$id              //02: ID do campo
                           ,'test Option'    //03: Label do campo
                           ,null             //04: Default FALSE = não obrigatori, TRUE = obrigatorio
                           ,$mixOptions      //05: array no formato "key=>value" ou nome do pacote oracle e da função a ser executada
                           ,null             //06: Default TRUE = cria nova linha , FALSE = fica depois do campo anterior
                           ,null             //07: Label sobre o campo. Default FALSE = Label mesma linha, TRUE = Label acima
                           ,null             //08: Informe o ID do array ou array com a lista de ID's no formato "key=>id" para identificar a(s) opção(ões) selecionada(s)
                           ,null             //09: Default FALSE = SingleSelect, TRUE = MultiSelect
                           ,null             //10: Default 1. Num itens que irão aparecer no MultiSelect
                           ,TFormDinOption::SELECT //11: define o tipo de input a ser gerado. Ex: select, radio ou check
                           ,null             //12: Nome da coluna que será utilizada para preencher os valores das opções
                           ,null             //13: Nome da coluna que será utilizada para preencher as opções que serão exibidas para o usuário
                           ,null
                           ,null             //15: informações extras do banco de dados que deverão ser adicionadas na tag option do campo select
        );
        $this->classTest = $select;
    }
    
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown(): void {
        $this->classTest = null;
        parent::tearDown();
    }

    public function testQtdColumns_Zero()
    {
        $result = $this->classTest->getQtdColumns();
        $this->assertEquals(1, $result);
    }

    public function testQtdColumns_3()
    {
        $this->classTest->setQtdColumns(3);
        $result = $this->classTest->getQtdColumns();
        $this->assertEquals(3, $result);
    }

    public function testFieldType_null()
    {
        $result = $this->classTest->getFieldType();
        $this->assertEquals(TFormDinOption::SELECT, $result);
    }
    public function testFieldType_Radio()
    {
        $this->classTest->setFieldType(TFormDinOption::RADIO);
        $result = $this->classTest->getFieldType();
        $this->assertEquals(TFormDinOption::RADIO, $result);
    }
}