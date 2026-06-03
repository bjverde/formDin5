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

class TFormDinFoneFieldTest extends TestCase
{
    private $classTest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->classTest = new TFormDinFoneField('phone_test', 'Telefone', false, true, '61999999999');
    }

    protected function tearDown(): void
    {
        $this->classTest = null;
        parent::tearDown();
    }

    public function test_instanceOff()
    {
        $adiantiObj = $this->classTest->getAdiantiObj();
        $this->assertInstanceOf(TEntry::class, $adiantiObj);
    }

    public function test_getAndSetLabel()
    {
        $this->assertEquals('Telefone', $this->classTest->getLabel());

        $this->classTest->setLabel('Fone');
        $this->assertEquals('Fone', $this->classTest->getLabel());
    }

    public function test_setValue()
    {
        $adiantiObj = $this->classTest->getAdiantiObj();
        $this->assertEquals('61999-9999', $adiantiObj->getValue());
    }

    public function test_required_and_placeholder()
    {
        $this->classTest->setRequired(true);
        $this->classTest->setExampleText('Digite seu fone');
        $this->assertEquals('Digite seu fone', $this->classTest->getAdiantiObj()->placeholder);
    }
}
