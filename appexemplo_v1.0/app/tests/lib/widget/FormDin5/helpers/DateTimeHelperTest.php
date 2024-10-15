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
 * DateTimeHelper test case.
 */
class DateTimeHelperTest extends TestCase
{

    public function testDate_null() {
        $esperado = null;
        $estrada = null;
        $retorno = DateTimeHelper::dateBr2Iso($estrada);
        $this->assertEquals($esperado, $retorno);
    }

    public function testDate2MySql_null_HoraNaoPermitida() {
        $esperado = null;
        $estrada = null;
        $retorno = DateTimeHelper::dateBr2Iso($estrada,false);
        $this->assertEquals($esperado, $retorno);
    }
    
    public function testDate2MySql_null_HoraPermitida() {
        $esperado = null;
        $estrada = null;
        $retorno = DateTimeHelper::dateBr2Iso($estrada,true);
        $this->assertEquals($esperado, $retorno);
    }

    public function testDate2MySql_white_HoraNaoPermitida() {
        $esperado = null;
        $estrada = '';
        $retorno = DateTimeHelper::dateBr2Iso($estrada,false);
        $this->assertEquals($esperado, $retorno);
    }
    
    public function testDate2MySql_white_HoraPermitida() {
        $esperado = null;
        $estrada = '';
        $retorno = DateTimeHelper::dateBr2Iso($estrada,true);
        $this->assertEquals($esperado, $retorno);
    }
    
    public function testDate2MySql_stringQualquer_HoraNaoPermitida() {
        $esperado = null;
        $estrada = 'fdafdlj';
        $retorno = DateTimeHelper::dateBr2Iso($estrada,false);
        $this->assertEquals($esperado, $retorno);
    }
    
    public function testDate2MySql_stringQualquer_HoraPermitida() {
        $esperado = null;
        $estrada = 'fdafdlj';
        $retorno = DateTimeHelper::dateBr2Iso($estrada,true);
        $this->assertEquals($esperado, $retorno);
    }
    
    public function testDate2MySql_stringParecendoData_HoraNaoPermitida() {
        $esperado = null;
        $estrada = 'dd/mm/yyyy';
        $retorno = DateTimeHelper::dateBr2Iso($estrada,false);
        $this->assertEquals($esperado, $retorno);
    }
    
    public function testDate2MySql_stringParecendoData_HoraPermitida() {
        $esperado = null;
        $estrada = 'dd/mm/yyyy';
        $retorno = DateTimeHelper::dateBr2Iso($estrada,true);
        $this->assertEquals($esperado, $retorno);
    }
    
    public function testDate2MySql_naoAlteraDataSemHora_HoraNaoPermitida() {
        $esperado = '2019-10-02';
        $estrada = '2019-10-02';
        $retorno = DateTimeHelper::dateBr2Iso($estrada,false);
        $this->assertEquals($esperado, $retorno);
    }
    
    public function testDate2MySql_naoAlteraDataSemHora_HoraPermitida() {
        $esperado = '2019-10-02';
        $estrada = '2019-10-02';
        $retorno = DateTimeHelper::dateBr2Iso($estrada,true);
        $this->assertEquals($esperado, $retorno);
    }
    
    public function testDate2MySql_naoAlteraDataComHora_HoraNaoPermitida() {
        $esperado = '2019-10-02';
        $estrada = '2019-10-02 13:45';
        $retorno = DateTimeHelper::dateBr2Iso($estrada,false);
        $this->assertEquals($esperado, $retorno);
    }
    
    public function testDate2MySql_naoAlteraDataComHora_HoraPermitida() {
        $esperado = '2019-10-02 13:45';
        $estrada = '2019-10-02 13:45';
        $retorno = DateTimeHelper::dateBr2Iso($estrada,true);
        $this->assertEquals($esperado, $retorno);
    }
    
    public function testDate2MySql_naoAlteraDataComSegundos_HoraNaoPermitida() {
        $esperado = '2019-10-02';
        $estrada = '2019-10-02 13:45:12';
        $retorno = DateTimeHelper::dateBr2Iso($estrada,false);
        $this->assertEquals($esperado, $retorno);
    }
    
    public function testDate2MySql_naoAlteraDataComSegundos_HoraPermitida() {
        $esperado = '2019-10-02 13:45:12';
        $estrada = '2019-10-02 13:45:12';
        $retorno = DateTimeHelper::dateBr2Iso($estrada,true);
        $this->assertEquals($esperado, $retorno);
    }
    
    public function testDate2MySql_dtBr01_HoraNaoPermitida() {
        $esperado = '2019-10-02';
        $estrada = '02/10/2019';
        $retorno = DateTimeHelper::dateBr2Iso($estrada,false);
        $this->assertEquals($esperado, $retorno);
    }
    
    public function testDate2MySql_dtBr01_HoraPermitida() {
        $esperado = '2019-10-02';
        $estrada = '02/10/2019';
        $retorno = DateTimeHelper::dateBr2Iso($estrada,true);
        $this->assertEquals($esperado, $retorno);
    }
    
    public function testDate2MySql_dtBr01ComHora_HoraNaoPermitida() {
        $esperado = '2019-10-02';
        $estrada = '02/10/2019 15:42';
        $retorno = DateTimeHelper::dateBr2Iso($estrada,false);
        $this->assertEquals($esperado, $retorno);
    }
    
    public function testDate2MySql_dtBr01ComHora_HoraPermitida() {
        $esperado = '2019-10-02 15:42';
        $estrada = '02/10/2019 15:42';
        $retorno = DateTimeHelper::dateBr2Iso($estrada,true);
        $this->assertEquals($esperado, $retorno);
    }
    
    public function testDate2MySql_dtBr01ComSegundo_HoraNaoPermitida() {
        $esperado = '2019-10-02';
        $estrada = '02/10/2019 15:42:30';
        $retorno = DateTimeHelper::dateBr2Iso($estrada,false);
        $this->assertEquals($esperado, $retorno);
    }
    
    public function testDate2MySql_dtBr01ComSegundo_HoraPermitida() {
        $esperado = '2019-10-02 15:42:30';
        $estrada = '02/10/2019 15:42:30';
        $retorno = DateTimeHelper::dateBr2Iso($estrada,true);
        $this->assertEquals($esperado, $retorno);
    }

    public function testDate2MySql_dtBr02_HoraNaoPermitida() {
        $esperado = '1900-01-02';
        $estrada = '02/01/1900';
        $retorno = DateTimeHelper::dateBr2Iso($estrada,false);
        $this->assertEquals($esperado, $retorno);
    }
    
    public function testDate2MySql_dtBr02_HoraPermitida() {
        $esperado = '1900-01-02';
        $estrada = '02/01/1900';
        $retorno = DateTimeHelper::dateBr2Iso($estrada,true);
        $this->assertEquals($esperado, $retorno);
    }
    
    //------------------------------------------------------------
    //------------------------------------------------------------
    //------------------------------------------------------------
    public function testGetDateTimeLong() {
        $esperado = '29 DE ABRIL DE 2019';
        $entrada = '2019-04-29';
        $retorno = DateTimeHelper::getDateTimeLong($entrada);
        $this->assertEquals($esperado, $retorno);
    }
    
    public function testConvertToDateTime() {
        $esperado = DateTime::class;
        $entrada = '28/04/2019';
        $retorno = DateTimeHelper::convertToDateTime($entrada);
        $this->assertInstanceOf($esperado, $retorno);
    }
    
    public function testIsWeekendTrue() {
        $esperado = true;
        $entrada = '28/04/2019';
        $retorno = DateTimeHelper::isWeekend($entrada);
        $this->assertEquals($esperado, $retorno);
    }
    
    public function testIsWeekendFalse() {
        $esperado = false;
        $entrada = '29/04/2019';
        $retorno = DateTimeHelper::isWeekend($entrada);
        $this->assertEquals($esperado, $retorno);
    }
    
    public function testIsWorkingDayTrue() {
        $esperado = true;
        $entrada = '29/04/2019';
        $retorno = DateTimeHelper::isWorkingDay($entrada);
        $this->assertEquals($esperado, $retorno);
    }
    
    public function testIsWorkingDayFalse() {
        $esperado = false;
        $entrada = '28/04/2019';
        $retorno = DateTimeHelper::isWorkingDay($entrada);
        $this->assertEquals($esperado, $retorno);
    }

    //------------------------------------------------------------
    //------------------------------------------------------------
    //------------------------------------------------------------
    public function testMaskDateFormDin4ToAdianit_DMY() {
        $esperado = 'dd-mm-yyyy';
        $entrada = 'DMY';
        $retorno = DateTimeHelper::maskDateFormDin4ToAdianit($entrada);
        $this->assertEquals($esperado, $retorno);
    }
    public function testMaskDateFormDin4ToAdianit_YMD() {
        $esperado = 'yyyy-mm-dd';
        $entrada = 'YMD';
        $retorno = DateTimeHelper::maskDateFormDin4ToAdianit($entrada);
        $this->assertEquals($esperado, $retorno);
    }
    public function testMaskDateFormDin4ToAdianit_MDY() {
        $esperado = 'mm-dd-yyyy';
        $entrada = 'MDY';
        $retorno = DateTimeHelper::maskDateFormDin4ToAdianit($entrada);
        $this->assertEquals($esperado, $retorno);
    }
    public function testMaskDateFormDin4ToAdianit_MD() {
        $esperado = 'mm-dd';
        $entrada = 'MD';
        $retorno = DateTimeHelper::maskDateFormDin4ToAdianit($entrada);
        $this->assertEquals($esperado, $retorno);
    }
    public function testMaskDateFormDin4ToAdianit_DM() {
        $esperado = 'dd-mm';
        $entrada = 'DM';
        $retorno = DateTimeHelper::maskDateFormDin4ToAdianit($entrada);
        $this->assertEquals($esperado, $retorno);
    }
    public function testMaskDateFormDin4ToAdianit_MY() {
        $esperado = 'mm-yyyy';
        $entrada = 'MY';
        $retorno = DateTimeHelper::maskDateFormDin4ToAdianit($entrada);
        $this->assertEquals($esperado, $retorno);
    }
    public function testMaskDateFormDin4ToAdianit_YM() {
        $esperado = 'yyyy-mm';
        $entrada = 'YM';
        $retorno = DateTimeHelper::maskDateFormDin4ToAdianit($entrada);
        $this->assertEquals($esperado, $retorno);
    }
    public function testMaskDateFormDin4ToAdianit_HM() {
        $esperado = 'hh:ii';
        $entrada = 'HM';
        $retorno = DateTimeHelper::maskDateFormDin4ToAdianit($entrada);
        $this->assertEquals($esperado, $retorno);
    }

    //------------------------------------------------------------
    //------------------------------------------------------------
    //------------------------------------------------------------   
    public function testdate1NewerThanDate2_DatasBr_Date1MaisAntiga_minuto() {
        $esperado = false;
        $datahora1 = '17/12/2022 00:30';
        $datahora2 = '17/12/2022 00:31';
        $retorno = DateTimeHelper::date1NewerThanDate2($datahora1,$datahora2);
        $this->assertEquals($esperado, $retorno);
    }

    public function testdate1NewerThanDate2_DatasBr_Date1MaisAntiga_hora() {
        $esperado = false;
        $datahora1 = '17/12/2022 00:30';
        $datahora2 = '17/12/2022 01:00';
        $retorno = DateTimeHelper::date1NewerThanDate2($datahora1,$datahora2);
        $this->assertEquals($esperado, $retorno);
    }

    public function testdate1NewerThanDate2_DatasBr_Date1MaisAntiga_dia() {
        $esperado = false;
        $datahora1 = '17/12/2022 00:30';
        $datahora2 = '18/12/2022 00:00';
        $retorno = DateTimeHelper::date1NewerThanDate2($datahora1,$datahora2);
        $this->assertEquals($esperado, $retorno);
    }

    public function testdate1NewerThanDate2_DatasBr_Date1MaisNova_minuto() {
        $esperado = true;
        $datahora1 = '17/12/2022 00:31';
        $datahora2 = '17/12/2022 00:30';
        $retorno = DateTimeHelper::date1NewerThanDate2($datahora1,$datahora2);
        $this->assertEquals($esperado, $retorno);
    }

    public function testdate1NewerThanDate2_DatasBr_Date1MaisNova_hora() {
        $esperado = true;
        $datahora1 = '17/12/2022 01:30';
        $datahora2 = '17/12/2022 00:00';
        $retorno = DateTimeHelper::date1NewerThanDate2($datahora1,$datahora2);
        $this->assertEquals($esperado, $retorno);
    }

    public function testdate1NewerThanDate2_DatasBr_Date1MaisNova_dia() {
        $esperado = true;
        $datahora1 = '18/12/2022 00:30';
        $datahora2 = '17/12/2022 00:00';
        $retorno = DateTimeHelper::date1NewerThanDate2($datahora1,$datahora2);
        $this->assertEquals($esperado, $retorno);
    }
}