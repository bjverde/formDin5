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

use PHPUnit\Framework\TestCase;

/**
 * paginationSQLHelper test case.
 */
class StringHelperTest extends TestCase
{
    const STRING_ORIGIN = 'Você deve ter recebido uma cópia da GNU LGPL versão 3';
	
    public function testStr2Lower_nochange() {
        $expected = 'você deve ter recebido uma cópia da gnu lgpl versão 3';
		$result = StringHelper::strtolower_utf8($expected) ;		
		$this->assertEquals( $expected , $result );
	}

	public function testStr2Lower_change() {
        $expected = 'você deve ter recebido uma cópia da gnu lgpl versão 3';
		$result = StringHelper::strtolower_utf8(self::STRING_ORIGIN) ;		
		$this->assertEquals( $expected , $result );
	}

	public function testStr2Upper_nochange() {
        $expected = 'VOCÊ DEVE TER RECEBIDO UMA CÓPIA DA GNU LGPL VERSÃO 3';
		$result = StringHelper::strtoupper_utf8($expected) ;		
		$this->assertEquals( $expected , $result );
	}

	public function testStr2Upper_change() {
        $expected = 'VOCÊ DEVE TER RECEBIDO UMA CÓPIA DA GNU LGPL VERSÃO 3';
		$result = StringHelper::strtoupper_utf8(self::STRING_ORIGIN) ;		
		$this->assertEquals( $expected , $result );
	}

    public function testStr2utf8_notUtf8ISO88591() {
        $str = self::STRING_ORIGIN;
		$str = mb_convert_encoding($str, "ISO-8859-1");
		$str = StringHelper::str2utf8($str);
		$result = mb_detect_encoding($str, 'UTF-8', true);
		
		$this->assertEquals( self::STRING_ORIGIN ,$str);		
		$this->assertEquals( 'UTF-8' , $result);
	}
	
	public function testStr2utf8_notUtf8CP1252() {
	    $str = self::STRING_ORIGIN;
	    $str = mb_convert_encoding($str, "Windows-1252");
	    $str = StringHelper::str2utf8($str);
	    $result = mb_detect_encoding($str, 'UTF-8', true);
	    
	    $this->assertEquals( self::STRING_ORIGIN ,$str);
	    $this->assertEquals( 'UTF-8' , $result);
	}
	
	public function testStr2utf8_Decode() {
	    $str = self::STRING_ORIGIN;
	    $str = mb_convert_encoding($str, "ISO-8859-1", "UTF-8");
	    $str = StringHelper::str2utf8($str);
	    $result = mb_detect_encoding($str, 'UTF-8', true);
	    
	    $this->assertEquals( self::STRING_ORIGIN ,$str);
	    $this->assertEquals( 'UTF-8' , $result);
	}

	public function testFormatCnpjCpf_forapadrao() {
        $expected = '123.456.789-09';
		$result = StringHelper::formatCnpjCpf('abc 123.456.789-09 flajk') ;		
		$this->assertEquals( $expected , $result );
	}

	public function testFormatCnpjCpf_cpf() {
        $expected = '123.456.789-09';
		$result = StringHelper::formatCnpjCpf('12345678909') ;		
		$this->assertEquals( $expected , $result );
	}

	public function testFormatCnpjCpf_cnpj() {
        $expected = '09.344.726/0001-16';
		$result = StringHelper::formatCnpjCpf('09344726000116') ;		
		$this->assertEquals( $expected , $result );
	}

	public function testLimpaCnpjCpf() {
        $expected = '12345678909';
		$result = StringHelper::limpaCnpjCpf('abc 123.456.789-09 flajk') ;		
		$this->assertEquals( $expected , $result );
	}

	public function testFormatPhoneNumber_celularComDdd() {
        $expected = '(61) 99982-2045';
		$result = StringHelper::formatPhoneNumber('61999822045') ;		
		$this->assertEquals( $expected , $result );
	}

	public function testFormatPhoneNumber_fixoComDdd() {
        $expected = '(61) 1234-5678';
		$result = StringHelper::formatPhoneNumber('611234-5678') ;		
		$this->assertEquals( $expected , $result );
	}

	public function testFormatPhoneNumber_celularSemDdd() {
        $expected = '99982-2045';
		$result = StringHelper::formatPhoneNumber('999822045') ;		
		$this->assertEquals( $expected , $result );
	}

	public function testFormatPhoneNumber_fixoSemDdd() {
        $expected = '1234-5678';
		$result = StringHelper::formatPhoneNumber('12345678') ;		
		$this->assertEquals( $expected , $result );
	}
	//-------------------------------------------------------------
	public function testIsNumeroBrasil_12() {
        $expected = true;
		$result = StringHelper::is_numeroBrasil('12');
		$this->assertEquals( $expected , $result );
	}
	public function testIsNumeroBrasil_12Centavos() {
        $expected = true;
		$result = StringHelper::is_numeroBrasil('12,34');
		$this->assertEquals( $expected , $result );
	}
	public function testIsNumeroBrasil_12milSemCentavos() {
        $expected = true;
		$result = StringHelper::is_numeroBrasil('12.123');
		$this->assertEquals( $expected , $result );
	}
	public function testIsNumeroBrasil_12milComCentavos() {
        $expected = true;
		$result = StringHelper::is_numeroBrasil('12.123,56');
		$this->assertEquals( $expected , $result );
	}
	public function testIsNumeroBrasil_BilhõesCemCentavos() {
        $expected = true;
		$result = StringHelper::is_numeroBrasil('20.123.456.789,56');
		$this->assertEquals( $expected , $result );
	}
	public function testIsNumeroBrasil_BilhoesSemCentavos() {
        $expected = true;
		$result = StringHelper::is_numeroBrasil('20.123.456.789');
		$this->assertEquals( $expected , $result );
	}
	public function testIsNumeroBrasil_variasCasasDecimais() {
        $expected = true;
		$result = StringHelper::is_numeroBrasil('20123456789,123456');
		$this->assertEquals( $expected , $result );
	}
	public function testIsNumeroBrasil_null() {
        $expected = false;
		$result = StringHelper::is_numeroBrasil(null);
		$this->assertEquals( $expected , $result );
	}
	public function testIsNumeroBrasil_maria() {
        $expected = false;
		$result = StringHelper::is_numeroBrasil('maria');
		$this->assertEquals( $expected , $result );
	}
	public function testIsNumeroBrasil_eua12() {
        $expected = false;
		$result = StringHelper::is_numeroBrasil('12.00');
		$this->assertEquals( $expected , $result );
	}
	public function testIsNumeroBrasil_euaSimples() {
        $expected = false;
		$result = StringHelper::is_numeroBrasil('12345678.00');
		$this->assertEquals( $expected , $result );
	}	
	public function testIsNumeroBrasil_eua12Mil() {
        $expected = false;
		$result = StringHelper::is_numeroBrasil('12,000.00');
		$this->assertEquals( $expected , $result );
	}
	public function testIsNumeroBrasil_eua12MilCom1Centavo() {
        $expected = false;
		$result = StringHelper::is_numeroBrasil('12,000.1');
		$this->assertEquals( $expected , $result );
	}
	public function testIsNumeroBrasil_eua12MilSemMilharCom1Centavo() {
        $expected = false;
		$result = StringHelper::is_numeroBrasil('12000.1');
		$this->assertEquals( $expected , $result );
	}
	public function testIsNumeroBrasil_eua12MilSemMilharCom15Centavo() {
        $expected = false;
		$result = StringHelper::is_numeroBrasil('12000.15');
		$this->assertEquals( $expected , $result );
	}	
	public function testIsNumeroBrasil_euaMilhoesComCentavos() {
        $expected = false;
		$result = StringHelper::is_numeroBrasil('123,456,789.99');
		$this->assertEquals( $expected , $result );
	}
	public function testIsNumeroBrasil_euaMilhoesSemCentavos() {
        $expected = false;
		$result = StringHelper::is_numeroBrasil('123,456,789');
		$this->assertEquals( $expected , $result );
	}	
	//-------------------------------------------------------------
	public function testIsNumeroEua_12() {
        $expected = true;
		$result = StringHelper::is_numeroEua('12');
		$this->assertEquals( $expected , $result );
	}
	public function testIsNumeroEua_12Centavos() {
        $expected = true;
		$result = StringHelper::is_numeroEua('12.34');
		$this->assertEquals( $expected , $result );
	}
	public function testIsNumeroEua_12milSemCentavos() {
        $expected = true;
		$result = StringHelper::is_numeroEua('12,123');
		$this->assertEquals( $expected , $result );
	}
	public function testIsNumeroEua_12milCemCentavos() {
        $expected = true;
		$result = StringHelper::is_numeroEua('12,123.56');
		$this->assertEquals( $expected , $result );
	}
	public function testIsNumeroEua_BilhoesComCentavos() {
        $expected = true;
		$result = StringHelper::is_numeroEua('20,123,456,789.56');
		$this->assertEquals( $expected , $result );
	}
	public function testIsNumeroEua_BilhoesSemCentavos() {
        $expected = true;
		$result = StringHelper::is_numeroEua('20,123,456,789');
		$this->assertEquals( $expected , $result );
	}
	public function testIsNumeroEua_variasCasasDecimais() {
        $expected = true;
		$result = StringHelper::is_numeroEua('20123456789.123456');
		$this->assertEquals( $expected , $result );
	}
	public function testIsNumeroEua_null() {
        $expected = false;
		$result = StringHelper::is_numeroEua(null);
		$this->assertEquals( $expected , $result );
	}
	public function testIsNumeroEua_StringMaria() {
        $expected = false;
		$result = StringHelper::is_numeroEua('maria');
		$this->assertEquals( $expected , $result );
	}
	public function testIsNumeroEua_br12ComCentavos() {
        $expected = false;
		$result = StringHelper::is_numeroEua('12,00');
		$this->assertEquals( $expected , $result );
	}
	public function testIsNumeroEua_brSimples() {
        $expected = false;
		$result = StringHelper::is_numeroEua('12345678,00');
		$this->assertEquals( $expected , $result );
	}
	public function testIsNumeroEua_br12Mil() {
        $expected = false;
		$result = StringHelper::is_numeroEua('12.000,00');
		$this->assertEquals( $expected , $result );
	}
	public function testIsNumeroEua_br12MilCom1Centavo() {
        $expected = false;
		$result = StringHelper::is_numeroEua('12.000,1');
		$this->assertEquals( $expected , $result );
	}
	public function testIsNumeroEua_br12MilSemMilharCom1Centavo() {
        $expected = false;
		$result = StringHelper::is_numeroEua('12000,1');
		$this->assertEquals( $expected , $result );
	}
	public function testIsNumeroEua_br12MilSemMilharCom15Centavo() {
        $expected = false;
		$result = StringHelper::is_numeroEua('12000,15');
		$this->assertEquals( $expected , $result );
	}
	public function testIsNumeroEua_brMilhoesCemCentavos() {
        $expected = false;
		$result = StringHelper::is_numeroEua('123.456.789,99');
		$this->assertEquals( $expected , $result );
	}
	public function testIsNumeroEua_brMilhoesSemCentavos() {
        $expected = false;
		$result = StringHelper::is_numeroEua('123.456.789');
		$this->assertEquals( $expected , $result );
	}
	//-------------------------------------------------------------
	public function testNumeroBrasil_stringEua12Mil() {
        $expected = '12.000,00';
		$result = StringHelper::numeroBrasil('12,000.00');
		$this->assertEquals( $expected , $result );
	}	
	public function testNumeroBrasil_stringBr12Mil() {
        $expected = '12.000,00';
		$result = StringHelper::numeroBrasil('12.000,00');
		$this->assertEquals( $expected , $result );
	}
	public function testNumeroBrasil_stringSimples() {
        $expected = '12.345.678,00';
		$result = StringHelper::numeroBrasil('12345678');
		$this->assertEquals( $expected , $result );
	}
	public function testNumeroBrasil_stringBrasil() {
        $expected = '12.345.678,00';
		$result = StringHelper::numeroBrasil('12345678,00');
		$this->assertEquals( $expected , $result );
	}
	public function testNumeroBrasil_stringEua() {
        $expected = '12.345.678,00';
		$result = StringHelper::numeroBrasil('12345678.00');
		$this->assertEquals( $expected , $result );
	}		
	public function testNumeroBrasil_number() {
        $expected = '12.345.678,00';
		$result = StringHelper::numeroBrasil(12345678);
		$this->assertEquals( $expected , $result );
	}
	public function testNumeroBrasil_text() {
        $expected = null;
		$result = StringHelper::numeroBrasil('maria');
		$this->assertEquals( $expected , $result );
	}
	public function testNumeroBrasil_12Int() {
        $expected = '12,00';
		$result = StringHelper::numeroBrasil(12);
		$this->assertEquals( $expected , $result );
	}
	public function testNumeroBrasil_12String() {
        $expected = '12,00';
		$result = StringHelper::numeroBrasil('12');
		$this->assertEquals( $expected , $result );
	}	
	public function testNumeroBrasil_ZeroInt() {
        $expected = '0,00';
		$result = StringHelper::numeroBrasil(0);
		$this->assertEquals( $expected , $result );
	}
	public function testNumeroBrasil_ZeroString() {
        $expected = '0,00';
		$result = StringHelper::numeroBrasil('0');
		$this->assertEquals( $expected , $result );
	}
	//-------------------------------------------------------------
	public function testNumeroEua_stringEua12Mil() {
        $expected = '12,000.00';
		$result = StringHelper::numeroEua('12,000.00');
		$this->assertEquals( $expected , $result );
	}	
	public function testNumeroEua_stringBr12Mil() {
        $expected = '12,000.00';
		$result = StringHelper::numeroEua('12.000,00');
		$this->assertEquals( $expected , $result );
	}	
	public function testNumeroEua_stringSimples() {
        $expected = '12,345,678.00';
		$result = StringHelper::numeroEua('12345678');
		$this->assertEquals( $expected , $result );
	}
	public function testNumeroEua_stringBrasil() {
        $expected = '12,345,678.00';
		$result = StringHelper::numeroEua('12345678,00');
		$this->assertEquals( $expected , $result );
	}
	public function testNumeroEua_stringEua() {
        $expected = '12,345,678.00';
		$result = StringHelper::numeroEua('12345678.00');
		$this->assertEquals( $expected , $result );
	}		
	public function testNumeroEua_number() {
        $expected = '12,345,678.00';
		$result = StringHelper::numeroEua(12345678);
		$this->assertEquals( $expected , $result );
	}
	public function testNumeroEua_text() {
        $expected = null;
		$result = StringHelper::numeroEua('maria');
		$this->assertEquals( $expected , $result );
	}
	public function testNumeroEua_12Int() {
        $expected = '12.00';
		$result = StringHelper::numeroEua(12);
		$this->assertEquals( $expected , $result );
	}
	public function testNumeroEua_12String() {
        $expected = '12.00';
		$result = StringHelper::numeroEua('12');
		$this->assertEquals( $expected , $result );
	}		
	public function testNumeroEua_ZeroInt() {
        $expected = '0.00';
		$result = StringHelper::numeroEua(0);
		$this->assertEquals( $expected , $result );
	}
	public function testNumeroEua_ZeroString() {
        $expected = '0.00';
		$result = StringHelper::numeroEua('0');
		$this->assertEquals( $expected , $result );
	}	
	//-------------------------------------------------------------
    public function testTirarAcentos() {
        $expected = 'Voce deve ter recebido uma copia da GNU LGPL versao 3';
		$result = StringHelper::tirarAcentos(self::STRING_ORIGIN) ;		
		$this->assertEquals( $expected , $result );
	}

	public function testTirarAcentos2() {
        $expected = 'acao ACAO Nao nAo';
		$result = StringHelper::tirarAcentos('ação AÇÃO Não nÃo') ;		
		$this->assertEquals( $expected , $result );
	}

	public function testString2PascalCase() {
        $expected = 'AcaoDeletarMao';
		$result = StringHelper::string2PascalCase('ação deLEtar MÃO') ;		
		$this->assertEquals( $expected , $result );
	}

	public function testString2PascalCaseWithSeparator_UnderLine() {
        $expected = 'AcaoDeletarMao';
		$result = StringHelper::string2PascalCaseWithSeparator('ação_deLEtar_MÃO','_') ;
		$this->assertEquals( $expected , $result );
	}

	public function testString2PascalCaseWithSeparator_menos() {
        $expected = 'AcaoDeletarMao';
		$result = StringHelper::string2PascalCaseWithSeparator('ação-deLEtar-MÃO','-') ;
		$this->assertEquals( $expected , $result );
	}

	public function testString2PascalCaseWithSeparator_Exception() {
		$this->expectException(InvalidArgumentException::class);
        $expected = 'AcaoDeletarMao';
		$result = StringHelper::string2PascalCaseWithSeparator('ação-deLEtar-MÃO','@') ;
		$this->assertEquals( $expected , $result );
	}	

	public function testString2CamelCase() {
        $expected = 'acaoDeletarMao';
		$result = StringHelper::string2CamelCase('ação deLEtar MÃO') ;		
		$this->assertEquals( $expected , $result );
	}

	public function testString2KebabCase() {
        $expected = 'acao-deletar-mao';
		$result = StringHelper::string2KebabCase('ação deLEtar MÃO') ;		
		$this->assertEquals( $expected , $result );
	}

	public function testString2SnakeCase() {
        $expected = 'acao_deletar_mao';
		$result = StringHelper::string2SnakeCase('ação deLEtar MÃO') ;		
		$this->assertEquals( $expected , $result );
	}

}