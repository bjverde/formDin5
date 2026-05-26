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

class mockGridPageTest extends TPage
{
    public function onTurnOnOff() {}
    public function onDelete() {}
    public function onEdit() {}
}

class TFormDinGridTransformerTest extends TestCase
{
    public function testGetDataGridActionTurnOnOff()
    {
        $action = TFormDinGridTransformer::getDataGridActionTurnOnOff('mockGridPageTest', 'id');
        $this->assertInstanceOf(TDataGridAction::class, $action);
        $this->assertEquals('btn btn-default', $action->getButtonClass());
        $this->assertEquals('fa:power-off orange', $action->getImage());
    }

    public function testGetDataGridActionOnDelete()
    {
        $action = TFormDinGridTransformer::getDataGridActionOnDelete('mockGridPageTest', 'id');
        $this->assertInstanceOf(TDataGridAction::class, $action);
        $this->assertEquals('Excluir', $action->getLabel());
    }

    public function testGetDataGridActionOnEdit()
    {
        $action = TFormDinGridTransformer::getDataGridActionOnEdit('mockGridPageTest', 'id');
        $this->assertInstanceOf(TDataGridAction::class, $action);
        $this->assertEquals('Editar', $action->getLabel());
    }

    public function testSimNao()
    {
        $this->assertEquals('Sim', TFormDinGridTransformer::simNao(true));
        $this->assertEquals('Sim', TFormDinGridTransformer::simNao('t'));
        $this->assertEquals('Sim', TFormDinGridTransformer::simNao(1));
        $this->assertEquals('Sim', TFormDinGridTransformer::simNao('1'));
        $this->assertEquals('Sim', TFormDinGridTransformer::simNao('s'));
        $this->assertEquals('Sim', TFormDinGridTransformer::simNao('S'));
        $this->assertEquals('Sim', TFormDinGridTransformer::simNao('T'));

        $this->assertEquals('Não', TFormDinGridTransformer::simNao(false));
        $this->assertEquals('Não', TFormDinGridTransformer::simNao('n'));
        $this->assertEquals('Não', TFormDinGridTransformer::simNao('N'));
        $this->assertEquals('Não', TFormDinGridTransformer::simNao(0));
    }

    public function testSimNaoComLabel()
    {
        $obj = new \stdClass();
        $row = new \stdClass();
        $elementYes = TFormDinGridTransformer::simNaoComLabel('S', $obj, $row);
        $this->assertInstanceOf(TElement::class, $elementYes);
        $this->assertStringContainsString('label-success', $elementYes->class);

        $elementNo = TFormDinGridTransformer::simNaoComLabel('N', $obj, $row);
        $this->assertInstanceOf(TElement::class, $elementNo);
        $this->assertStringContainsString('label-danger', $elementNo->class);
    }

    public function testLinkApiWhatsApp()
    {
        $obj = new \stdClass();
        $row = new \stdClass();
        $result = TFormDinGridTransformer::linkApiWhatsApp('61988887777', $obj, $row, 'Ola', true);
        $this->assertStringContainsString('api.whatsapp.com', $result);

        $this->assertEquals(null, TFormDinGridTransformer::linkApiWhatsApp(null, $obj, $row, 'Ola', true));
    }

    public function testDateAndGridDate()
    {
        $this->assertEquals('26/05/2026', TFormDinGridTransformer::date('2026-05-26'));
        $this->assertNull(TFormDinGridTransformer::date('0000-00-00'));
        $this->assertNull(TFormDinGridTransformer::date(''));

        $obj = new \stdClass();
        $row = new \stdClass();
        $this->assertEquals('26/05/2026', TFormDinGridTransformer::gridDate('2026-05-26', $obj, $row));
    }

    public function testGridImg()
    {
        $obj = new \stdClass();
        $row = new \stdClass();
        
        $resultNoFallback = TFormDinGridTransformer::gridImg('nonexistent.png', $obj, $row, 'app/images/', '100px', null);
        $this->assertEquals('', $resultNoFallback);

        $resultWithFallback = TFormDinGridTransformer::gridImg('nonexistent.png', $obj, $row, 'app/images/', '100px', 'app/images/nonexistent_fallback.png');
        $this->assertInstanceOf(TImage::class, $resultWithFallback);
    }

    public function testDateTimeAndGridDateTime()
    {
        $this->assertEquals('26/05/2026 14:30', TFormDinGridTransformer::dateTime('2026-05-26 14:30:00'));
        $this->assertNull(TFormDinGridTransformer::dateTime('0000-00-00'));

        $obj = new \stdClass();
        $row = new \stdClass();
        $this->assertEquals('26/05/2026 14:30', TFormDinGridTransformer::gridDateTime('2026-05-26 14:30:00', $obj, $row));
    }

    public function testGridNumeroBrasilAndFormatStyle()
    {
        $obj = new \stdClass();
        $row = new \stdClass();

        $this->assertEquals('1.234,56', TFormDinGridTransformer::gridNumeroBrasil('1234.56', $obj, $row));
        $this->assertEquals('0,00', TFormDinGridTransformer::gridNumeroBrasil(null, $obj, $row));

        $positiveStyle = TFormDinGridTransformer::gridNumeroBrasilFormatStyle('1234.56', $obj, $row);
        $this->assertStringContainsString('color:blue', $positiveStyle);
        $this->assertStringContainsString('1.234,56', $positiveStyle);

        $negativeStyle = TFormDinGridTransformer::gridNumeroBrasilFormatStyle('-1234.56', $obj, $row);
        $this->assertStringContainsString('color:red', $negativeStyle);
        $this->assertStringContainsString('-1.234,56', $negativeStyle);
    }

    public function testGridCordLatLonLinkGoogleMaps()
    {
        $this->assertEquals('', TFormDinGridTransformer::gridCordLatLonLinkGoogleMaps(null, null, ''));
        
        $link = TFormDinGridTransformer::gridCordLatLonLinkGoogleMaps('-15.0', '-47.0', 'color:blue', true, 'My Maps');
        $this->assertStringContainsString('https://www.google.com/maps', $link);
        $this->assertStringContainsString('color:blue', $link);
        $this->assertStringContainsString('My Maps', $link);

        $linkText = TFormDinGridTransformer::gridCordLatLonLinkGoogleMaps('-15.0', '-47.0', '', false, '');
        $this->assertStringContainsString('Lat: -15.0, Long: -47.0', $linkText);
    }
}
