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
 * ----------------------------------------------------------------------------
 */

use PHPUnit\Framework\TestCase;

class TFormDinMapCordTest extends TestCase
{
    private $classTest;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->classTest = new TFormDinMapCord('map_test', 'Interactive Map');
    }

    /**
     * Cleans up the environment after running a test.
     */
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
        $this->assertEquals('map_test', $this->classTest->getIdDivMap());
        $this->assertEquals(-15.793889, $this->classTest->getDefaultLat());
        $this->assertEquals(-47.882778, $this->classTest->getDefaultLon());
        $this->assertEquals(12, $this->classTest->getZoom());
        $this->assertEquals(400, $this->classTest->getHeight());
        $this->assertTrue($this->classTest->getShowFields());
        $this->assertFalse($this->classTest->getFieldsReadOnly());
        $this->assertNull($this->classTest->getGeoJsonPath());
    }

    public function test_customSetters()
    {
        $this->classTest->setDefaultLat(-22.906847);
        $this->assertEquals(-22.906847, $this->classTest->getDefaultLat());

        $this->classTest->setDefaultLon(-43.172896);
        $this->assertEquals(-43.172896, $this->classTest->getDefaultLon());

        $this->classTest->setZoom(14);
        $this->assertEquals(14, $this->classTest->getZoom());

        $this->classTest->setHeight(250);
        $this->assertEquals(250, $this->classTest->getHeight());

        $this->classTest->setGeoJsonPath('app/lib/widget/FormDin5/leaflet/ras_df.geojson');
        $this->assertEquals('app/lib/widget/FormDin5/leaflet/ras_df.geojson', $this->classTest->getGeoJsonPath());

        $this->classTest->setShowFields(false);
        $this->assertFalse($this->classTest->getShowFields());

        $this->classTest->setFieldsReadOnly(true);
        $this->assertTrue($this->classTest->getFieldsReadOnly());
    }

    public function test_constructorWithParameters()
    {
        $field = new TFormDinMapCord(
            'map_custom',
            'Custom Map',
            true,          // boolRequired
            false,         // showFields
            true,          // fieldsReadOnly
            -23.550520,    // defaultLat
            -46.633308,    // defaultLon
            10,            // zoom
            350,           // height
            'app/some.geojson' // geoJsonPath
        );

        $this->assertEquals('map_custom', $field->getIdDivMap());
        $this->assertEquals(-23.550520, $field->getDefaultLat());
        $this->assertEquals(-46.633308, $field->getDefaultLon());
        $this->assertEquals(10, $field->getZoom());
        $this->assertEquals(350, $field->getHeight());
        $this->assertFalse($field->getShowFields());
        $this->assertTrue($field->getFieldsReadOnly());
        $this->assertEquals('app/some.geojson', $field->getGeoJsonPath());
    }
}
