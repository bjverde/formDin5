<?php
/*
 * Formdin Framework
 * Copyright (C) 2012 Ministério do Planejamento
 * Criado por Luís Eugênio Barbosa
 * Essa versão é um Fork https://github.com/bjverde/formDin
 *
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

/**
 * paginationSQLHelper test case.
 */
class HtmlHelperTest extends TestCase
{	

	public function testGetViewPort() {
	    $expected = '<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">';
        $result = HtmlHelper::getViewPort();
        $this->assertEquals( $expected , $result);
	}
	public function testValidateHtmlColorHexa_FailNumber() {
		$this->expectException(InvalidArgumentException::class);
	    $string = 1;
	    HtmlHelper::validateHtmlColorHexa( $string );
	}
	//--------------------------------------------------------------------------------
	public function testValidateHtmlColorHexa_FailString() {
		$this->expectException(InvalidArgumentException::class);
	    $string = 'xxx';
	    HtmlHelper::validateHtmlColorHexa( $string );
    }
	//--------------------------------------------------------------------------------
	/*
	public function testValidateHtmlColorHexa_FailArray() {
		$this->expectError();
	    $string = array(1,2);
	    HtmlHelper::validateHtmlColorHexa( $string );
    }
	*/
	//--------------------------------------------------------------------------------
	public function testValidateHtmlColorHexa_FailWrongSizeLess() {
		$this->expectException(InvalidArgumentException::class);
	    $string = '#1';
	    HtmlHelper::validateHtmlColorHexa( $string );
    }
    //--------------------------------------------------------------------------------
	public function testValidateHtmlColorHexa_FailWrongSizeBig() {
		$this->expectException(InvalidArgumentException::class);
	    $string = '#12345678';
	    HtmlHelper::validateHtmlColorHexa( $string );
    }
    public function testValidateHtmlColorHexa_PassNull() {
	    $string = null;
	    $this->assertNull( HtmlHelper::validateHtmlColorHexa( $string ) );
    }
    public function testValidateHtmlColorHexa_Pass() {
	    $string = '#123456';
	    $this->assertNull( HtmlHelper::validateHtmlColorHexa( $string ) );
	}

    public function testLinkApiWhatsApp_WithGreenIcon() {
        $result = HtmlHelper::linkApiWhatsApp('+55 (61) 99999-9999', 'Olá', true);
        $this->assertStringContainsString('fab fa-whatsapp green', $result);
        $this->assertStringContainsString('phone=5561999999999', $result);
        $this->assertStringContainsString('text=Olá', $result);
    }

    public function testLinkApiWhatsApp_WithoutGreenIcon() {
        $result = HtmlHelper::linkApiWhatsApp('+55 (61) 99999-9999', 'Olá', false);
        $this->assertStringContainsString('fab fa-whatsapp', $result);
        $this->assertStringNotContainsString('green', $result);
        $this->assertStringContainsString('phone=5561999999999', $result);
        $this->assertStringContainsString('text=Olá', $result);
    }

    public function testGetListDdd() {
        $result = HtmlHelper::getListDdd();
        $this->assertIsArray($result);
        $this->assertArrayHasKey(61, $result);
        $this->assertEquals('Distrito Federal - 61', $result[61]);
    }

    public function testGetListDdi() {
        $result = HtmlHelper::getListDdi();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('+55', $result);
        $this->assertEquals('+55 🇧🇷 Brasil', $result['+55']);
    }

    public function testHighlight_nuloOuVazio() {
        $this->assertEquals('', HtmlHelper::highlight(null));
        $this->assertEquals('', HtmlHelper::highlight(''));
    }

    public function testHighlight_simples() {
        $texto = 'O FormDin5 facilita o desenvolvimento em PHP';
        $resultado = HtmlHelper::highlight($texto, 'FormDin5');
        $this->assertEquals('O <mark>FormDin5</mark> facilita o desenvolvimento em PHP', $resultado);
    }

    public function testHighlight_preservaCaseOriginal() {
        $texto = 'PORTARIA MINISTERIAL número 123';
        $resultado = HtmlHelper::highlight($texto, ['portaria', 'ministerial']);
        $this->assertEquals('<mark>PORTARIA</mark> <mark>MINISTERIAL</mark> número 123', $resultado);
    }

    public function testHighlight_tagCustomizadaEClasseCss() {
        $texto = 'Texto com destaque especial';
        $resultado = HtmlHelper::highlight($texto, 'especial', 'span', 'badge bg-warning');
        $this->assertEquals('Texto com destaque <span class="badge bg-warning">especial</span>', $resultado);
    }

    public function testHighlight_protegeXss() {
        $texto = 'Texto perigoso <script>alert("xss")</script> com termo';
        $resultado = HtmlHelper::highlight($texto, 'termo');
        $this->assertStringNotContainsString('<script>', $resultado);
        $this->assertStringContainsString('&lt;script&gt;', $resultado);
        $this->assertStringContainsString('<mark>termo</mark>', $resultado);
    }

    public function testHighlight_acentuacaoUtf8() {
        $texto = 'Publicação de nova eleição para o órgão colegiado';
        $resultado = HtmlHelper::highlight($texto, ['órgão', 'eleição']);
        $this->assertEquals('Publicação de nova <mark>eleição</mark> para o <mark>órgão</mark> colegiado', $resultado);
    }

    public function testHighlightTexto_completo() {
        $texto = 'O rato roeu a roupa do rei de Roma e fugiu rapidamente para a toca escondida no jardim';
        $resultado = HtmlHelper::highlightTexto($texto, 'rei', 3);
        $this->assertEquals('... a roupa do <mark>rei</mark> de Roma e ...', $resultado);
    }

    public function testHighlightTexto_fraseExata() {
        $texto = 'Primeira parte do documento oficial com texto longo sobre a portaria ministerial número cem do ano corrente';
        $resultado = HtmlHelper::highlightTexto($texto, [], 2, 'portaria ministerial');
        $this->assertEquals('... sobre a <mark>portaria ministerial</mark> número cem ...', $resultado);
    }

    public function testHighlightHtml_naoAlteraAtributosHtml() {
        $html = '<div class="portaria"><a href="http://site.com/portaria">Texto da portaria</a></div>';
        $resultado = HtmlHelper::highlightHtml($html, 'portaria');
        $this->assertStringContainsString('class="portaria"', $resultado);
        $this->assertStringContainsString('href="http://site.com/portaria"', $resultado);
        $this->assertStringContainsString('Texto da <mark>portaria</mark>', $resultado);
    }

    public function testHighlightTexto_cenario1LimpoComHtmlEntrada() {
        $html = '<div class="card"><p>O documento <b>oficial</b> sobre a portaria ministerial foi publicado</p></div>';
        $resultado = HtmlHelper::highlightTexto($html, 'portaria', 2);
        $this->assertStringNotContainsString('<div', $resultado);
        $this->assertStringNotContainsString('<p', $resultado);
        $this->assertEquals('... sobre a <mark>portaria</mark> ministerial foi ...', $resultado);
    }

    public function testHighlightTextoHtml_cenario2PreservaHtml() {
        $html = '<p>Primeira parte com <b>texto oficial</b> sobre a <i>portaria ministerial</i> número cem de teste</p>';
        $resultado = HtmlHelper::highlightTextoHtml($html, 'portaria', 2);
        $this->assertStringContainsString('<mark>portaria</mark>', $resultado);
        $this->assertStringContainsString('<i>', $resultado);
        $this->assertStringContainsString('</i>', $resultado);
    }

    public function testBalancearTagsHtml() {
        $htmlIncompleto = '<div class="alerta"><p>Texto com <b>negrito';
        $resultado = HtmlHelper::balancearTagsHtml($htmlIncompleto);
        $this->assertEquals('<div class="alerta"><p>Texto com <b>negrito</b></p></div>', $resultado);
    }

    public function testBalancearTagsHtml_comVoidTags() {
        $htmlComImg = '<p>Imagem: <img src="foto.jpg"><br>Legenda';
        $resultado = HtmlHelper::balancearTagsHtml($htmlComImg);
        $this->assertEquals('<p>Imagem: <img src="foto.jpg"><br>Legenda</p>', $resultado);
    }

    public function testExtrairTrechoHtml_preservaTagsHtml() {
        $html = '<p>Primeira parte com <b>texto em negrito</b> sobre a <i>portaria ministerial</i> número cem de teste</p>';
        $resultado = HtmlHelper::extrairTrechoHtml($html, 'portaria', 2);
        $this->assertStringContainsString('portaria', $resultado);
        $this->assertStringContainsString('<i>', $resultado);
        $this->assertStringContainsString('</i>', $resultado);
    }
}