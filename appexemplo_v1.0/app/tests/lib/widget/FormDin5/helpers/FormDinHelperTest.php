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

$path =  __DIR__.'/../../../../../../';
//require_once $path.'init.php';
//require_once $path.'tests/initTest.php';

require_once  __DIR__.'/../../mockMunicipioVO.class.php';

use PHPUnit\Framework\TestCase;

class FormDinHelperTest extends TestCase
{
    public $formDinVersion = '5.2.0';
    public $adiantiVersion = '7.6.0.4';

    public function testVersion() {
        $expected = $this->formDinVersion;
        $result =  FormDinHelper::version();
        $this->assertEquals( $expected , $result);
    }
    public function testVersionMinimum_false() {
        $expected = false;
        $result = FormDinHelper::versionMinimum('99.99.99');
        $this->assertEquals( $expected , $result);
    }
    public function testVersionMinimum_true() {
        $expected = true;
        $result = FormDinHelper::versionMinimum('1.0.0');
        $this->assertEquals( $expected , $result);
    }
    public function testVersionMinimum_equal() {
        $expected = true;
        $result = FormDinHelper::versionMinimum('5.0.0');
        $this->assertEquals( $expected , $result);
    }
    public function testVersionMinimum_Ref1Menor() {
        $expected = true;
        $result = FormDinHelper::versionMinimum('5.0.0','6.0.0');
        $this->assertEquals( $expected , $result);
    }
    public function testVersionMinimum_Ref1equal() {
        $expected = true;
        $result = FormDinHelper::versionMinimum('6.0.0','6.0.0');
        $this->assertEquals( $expected , $result);
    }
    public function testVersionMinimum_Ref1Maior() {
        $expected = false;
        $result = FormDinHelper::versionMinimum('6.0.0','5.0.0');
        $this->assertEquals( $expected , $result);
    }     

    /**
     * @doesNotPerformAssertions
     */
    public function testAdianti_verifyFormDinMinimumVersion_okOlder() {
        FormDinHelper::verifyFormDinMinimumVersion('1.0.0');
    }     
    /**
     * @doesNotPerformAssertions
     */
    public function testAdianti_verifyFormDinMinimumVersion_ok() {
        FormDinHelper::verifyFormDinMinimumVersion($this->formDinVersion);
    }   
    public function testAdianti_verifyFormDinMinimumVersion_Exception() {
        $this->expectException(DomainException::class);
        FormDinHelper::verifyFormDinMinimumVersion('99.99.99');
    }
    public function testAdianti_verifyFormDinMinimumVersion_ExceptionWrongFormat1() {
        $this->expectException(DomainException::class);
        FormDinHelper::verifyFormDinMinimumVersion('99');
    }
    public function testAdianti_verifyFormDinMinimumVersion_ExceptionWrongFormat2() {
        $this->expectException(DomainException::class);
        FormDinHelper::verifyFormDinMinimumVersion('99.99');
    }
    public function testAdianti_verifyFormDinMinimumVersion_ExceptionWrongFormat5() {
        $this->expectException(DomainException::class);
        FormDinHelper::verifyFormDinMinimumVersion('99.99.99.99.99');
    }
    /**
     * @doesNotPerformAssertions
     */
    public function testAdianti_verifyMinimumVersionAdiantiFrameWorkToFormDin_ok() {
        FormDinHelper::verifyMinimumVersionAdiantiFrameWorkToFormDin();
    }
    /**
     * @doesNotPerformAssertions
     */
    public function testAdianti_verifyMinimumVersionAdiantiFrameWorkToSystem_ok() {
        FormDinHelper::verifyMinimumVersionAdiantiFrameWorkToSystem('7.3.0');
    }
    public function testAdianti_verifyMinimumVersionAdiantiFrameWorkToSystem_Exception() {
        $this->expectException(DomainException::class);
        FormDinHelper::verifyMinimumVersionAdiantiFrameWorkToSystem('99.99.99');
    }
    //-----------------------------------------------------------------------------------
    public function testAdiantiVersion() {
        $expected = $this->adiantiVersion;
        $result =  FormDinHelper::getAdiantiFrameWorkVersion();
        $this->assertEquals( $expected , $result);
    }
    //-----------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------
    public function testSetPropertyVo_noSet(){
        $bodyRequest = array();
        $bodyRequest['IDPESSOA'] = 10;
        $bodyRequest['NMPESSOA'] = 'Paulo Deleo';
        
        $vo = new mockMunicipioVO();
        
        $vo =  FormDinHelper::setPropertyVo($bodyRequest,$vo);
        $expected = null;
        $this->assertEquals( $expected , $vo->getCod_municipio());
        $this->assertEquals( $expected , $vo->getCod_uf());
        $this->assertEquals( $expected , $vo->getNom_municipio());
        $this->assertEquals( $expected , $vo->getSit_ativo());
    }
    //-----------------------------------------------------------------------------------
    public function testSetPropertyVo_setOnlyCodMunicipio_lowerCase(){
        $bodyRequest = array();
        $bodyRequest['cod_municipio'] = 10;
        $bodyRequest['NMPESSOA'] = 'Paulo Deleo';
        
        $vo = new mockMunicipioVO();
        
        $vo =  FormDinHelper::setPropertyVo($bodyRequest,$vo);
        $expected = 10;
        $this->assertEquals( $expected , $vo->getCod_municipio());
        $expected = null;
        $this->assertEquals( $expected , $vo->getCod_uf());
        $this->assertEquals( $expected , $vo->getNom_municipio());
        $this->assertEquals( $expected , $vo->getSit_ativo());
    }

    public function testSetPropertyVo_MixCase(){
        $bodyRequest = array();
        $bodyRequest['COD_MUNICIPIO'] = 10;
        $bodyRequest['COD_UF'] = 'DF';
        $bodyRequest['nom_municipio'] = 'Divinopolis';
        $bodyRequest['sit_ativo'] = 'N';
        
        $vo = new mockMunicipioVO();
        
        $vo =  FormDinHelper::setPropertyVo($bodyRequest,$vo);
        $this->assertEquals( 10 , $vo->getCod_municipio());
        $this->assertEquals( 'DF' , $vo->getCod_uf());
        $this->assertEquals( 'Divinopolis' , $vo->getNom_municipio());
        $this->assertEquals( 'N' , $vo->getSit_ativo());
    }    
    //-----------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------
    public function testConvertVo2ArrayFormDin_failNull(){
        $this->expectException(InvalidArgumentException::class);
        FormDinHelper::convertVo2ArrayFormDin(null);
    }
    
    /**
     * @expectedException InvalidArgumentException
     */
    public function testConvertVo2ArrayFormDin_failString(){
        $this->expectException(InvalidArgumentException::class);
        FormDinHelper::convertVo2ArrayFormDin('xx');        
    }
    
    public function testConvertVo2ArrayFormDin_failInt(){
        $this->expectException(InvalidArgumentException::class);
        FormDinHelper::convertVo2ArrayFormDin(120);
    }
    
    public function testConvertVo2ArrayFormDin_OK(){
        $vo = new mockMunicipioVO();
        $vo->setCod_municipio(1);
        $vo->setCod_uf(2);
        $vo->setNom_municipio('Brasília');
        $vo->setSit_ativo('S');
        
        $arrayFormDin =  FormDinHelper::convertVo2ArrayFormDin($vo);
        $this->assertEquals( 1 , $arrayFormDin['COD_MUNICIPIO'][0]);
        $this->assertEquals( 'Brasília' , $arrayFormDin['NOM_MUNICIPIO'][0]);
        $this->assertEquals( 2 , $arrayFormDin['COD_UF'][0]);
        $this->assertEquals( 'S' , $arrayFormDin['SIT_ATIVO'][0]);
    }
    //-----------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------
    public function testValidateIdIsNumeric_FailNull(){
        $this->expectException(InvalidArgumentException::class);
        FormDinHelper::validateIdIsNumeric(null,__METHOD__,__LINE__);
    }
    public function testValidateIdIsNumeric_OkInteger(){
        $this->assertNull( FormDinHelper::validateIdIsNumeric(10,__METHOD__,__LINE__) );
    }
    public function testValidateIdIsNumeric_OkString(){
        $this->assertNull( FormDinHelper::validateIdIsNumeric('10',__METHOD__,__LINE__) );
    }
    //-----------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------
    public function testIssetOrNotZero_arrayNull() {
        $expected = false;
        $variable = array();
        $result = FormDinHelper::issetOrNotZero($variable);
        $this->assertEquals( $expected , $result);
    }
    
    public function testIssetOrNotZero_arrayNotNull() {
        $expected = true;
        $variable = array(0,1);
        $result = FormDinHelper::issetOrNotZero($variable);
        $this->assertEquals( $expected , $result);
    }
    
    public function testIssetOrNotZero_stringBlank() {
        $expected = false;
        $variable = '';
        $result = FormDinHelper::issetOrNotZero($variable);
        $this->assertEquals( $expected , $result);
    }
    
    public function testIssetOrNotZero_stringNull() {
        $expected = false;
        $variable = null;
        $result = FormDinHelper::issetOrNotZero($variable);
        $this->assertEquals( $expected , $result);
    }
    
    public function testIssetOrNotZero_stringZero() {
        $expected = false;
        $variable = '0';
        $result = FormDinHelper::issetOrNotZero($variable);
        $this->assertEquals( $expected , $result);
    }
    
    public function testIssetOrNotZero_stringZeroNoTest() {
        $expected = true;
        $variable = '0';
        $result = FormDinHelper::issetOrNotZero($variable,false);
        $this->assertEquals( $expected , $result);
    }
    //-----------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------

    public function testValidateSizeWidthAndHeight_okNull()
    {
        $this->expectNotToPerformAssertions();
        FormDinHelper::validateSizeWidthAndHeight(null);
    }

    public function testValidateSizeWidthAndHeight_okPercent()
    {
        $this->expectNotToPerformAssertions();
        FormDinHelper::validateSizeWidthAndHeight('100%');
    }

    public function testValidateSizeWidthAndHeight_okEm()
    {
        $this->expectNotToPerformAssertions();
        FormDinHelper::validateSizeWidthAndHeight('100em');
    }    

    public function testValidateSizeWidthAndHeight_okRem()
    {
        $this->expectNotToPerformAssertions();
        FormDinHelper::validateSizeWidthAndHeight('100rem');
    }

    public function testValidateSizeWidthAndHeight_okVh()
    {
        $this->expectNotToPerformAssertions();
        FormDinHelper::validateSizeWidthAndHeight('100vh');
    }

    public function testValidateSizeWidthAndHeight_okVw()
    {
        $this->expectNotToPerformAssertions();
        FormDinHelper::validateSizeWidthAndHeight('100vw');
    }

    public function testValidateSizeWidthAndHeight_failInt()
    {
        $this->expectException(InvalidArgumentException::class);
        FormDinHelper::validateSizeWidthAndHeight(100);
    }

    public function testValidateSizeWidthAndHeight_failString()
    {
        $this->expectException(InvalidArgumentException::class);
        FormDinHelper::validateSizeWidthAndHeight('100');
    }

    public function testValidateSizeWidthAndHeight_failPx()
    {
        $this->expectException(InvalidArgumentException::class);
        FormDinHelper::validateSizeWidthAndHeight('100px');
    }
    public function testValidateSizeWidthAndHeight_okPx()
    {
        $this->expectNotToPerformAssertions();
        FormDinHelper::validateSizeWidthAndHeight('100px',true);
    }    
    
}