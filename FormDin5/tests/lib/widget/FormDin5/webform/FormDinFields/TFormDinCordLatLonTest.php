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

class TFormDinCordLatLonTest extends TestCase
{
    private $classTest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->classTest = new TFormDinCordLatLon('cood_test', 'Coordinates');
    }

    protected function tearDown(): void
    {
        $this->classTest = null;
        parent::tearDown();
    }

    public function test_instanceOff()
    {
        $adiantiObj = $this->classTest->getAdiantiObj();
        $this->assertInstanceOf(TElement::class, $adiantiObj);
    }

    public function test_getDefaults()
    {
        $this->assertEquals('cood_test', $this->classTest->getIdDivGeo());
        $this->assertTrue($this->classTest->getShowFields());
        $this->assertTrue($this->classTest->getShowAltitude());
        $this->assertTrue($this->classTest->getFieldsReadOnly());
        $this->assertTrue($this->classTest->getShowAllJson());
    }

    public function test_customSetters()
    {
        $this->classTest->setShowFields(false);
        $this->assertFalse($this->classTest->getShowFields());

        $this->classTest->setShowAltitude(false);
        $this->assertFalse($this->classTest->getShowAltitude());

        $this->classTest->setFieldsReadOnly(false);
        $this->assertFalse($this->classTest->getFieldsReadOnly());

        $this->classTest->setShowAllJson(false);
        $this->assertFalse($this->classTest->getShowAllJson());

        $this->classTest->setButtonClass('btn-danger');
        $this->assertEquals('btn-danger', $this->classTest->getButtonClass());

        $this->classTest->setButtonLabel('Locate');
        $this->assertEquals('Locate', $this->classTest->getButtonLabel());

        $this->classTest->setButtonIcon('fa:map');
        $this->assertEquals('fa:map', $this->classTest->getButtonIcon());

        $this->classTest->setFeedBackIcon('fa:check');
        $this->assertEquals('fa:check', $this->classTest->getFeedBackIcon());

        $this->classTest->setFeedBackColor('blue');
        $this->assertEquals('blue', $this->classTest->getFeedBackColor());

        $this->classTest->setFeedBackSize('15px');
        $this->assertEquals('15px', $this->classTest->getFeedBackSize());
    }

    public function testShowFieldsFalse()
    {
        $latLon = new TFormDinCordLatLon('cood_test', 'Coordinates', false, false, true);
        $this->assertInstanceOf(TElement::class, $latLon->getAdiantiObj());
    }
}
