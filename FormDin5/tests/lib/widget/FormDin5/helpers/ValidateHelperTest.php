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

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Error\Warning;

class ValidateHelperTest extends TestCase
{

    public function testObjTypeTFormDinPdoConnection_FailMethod() {
        $this->expectException(InvalidArgumentException::class);
        $tpdo = null;
        $method = null;
        $line = null;
        ValidateHelper::objTypeTFormDinPdoConnection($tpdo,$method,$line);
    }

    public function testisString_FailInputInt() {
        $this->expectException(InvalidArgumentException::class);
        ValidateHelper::isString(1,__METHOD__,__LINE__);
    }
    public function testisString_FailInputArray() {
        $this->expectException(InvalidArgumentException::class);
        ValidateHelper::isString(array(),__METHOD__,__LINE__);
    }
    public function testisString_FailInputObj() {
        $this->expectException(InvalidArgumentException::class);
        ValidateHelper::isString(new StdClass,__METHOD__,__LINE__);
    }
    public function testisString_OK() {
        $this->expectNotToPerformAssertions();
        ValidateHelper::isString('test',__METHOD__,__LINE__);
    }    

    public function testIsNumeric_FailInputStringText() {
        $this->expectException(InvalidArgumentException::class);
        ValidateHelper::isNumeric('1a',__METHOD__,__LINE__);
    }
    public function testIsNumeric_OKStringNumber() {
        $this->expectNotToPerformAssertions();
        ValidateHelper::isNumeric('1',__METHOD__,__LINE__);
    }      
    public function testIsNumeric_FailInputArray() {
        $this->expectException(InvalidArgumentException::class);
        ValidateHelper::isNumeric(array(),__METHOD__,__LINE__);
    }
    public function testIsNumeric_FailInputObj() {
        $this->expectException(InvalidArgumentException::class);
        ValidateHelper::isNumeric(new StdClass,__METHOD__,__LINE__);
    }

    public function testIsObject_InputNullException() {
        $this->expectException(InvalidArgumentException::class);
        ValidateHelper::isObject(null,__METHOD__,__LINE__);
    }

    public function testIsObject_InputStringException() {
        $this->expectException(InvalidArgumentException::class);
        ValidateHelper::isObject('aaa',__METHOD__,__LINE__);
    }

    public function testIsObject_OK() {
        $this->expectNotToPerformAssertions();
        ValidateHelper::isObject(new StdClass(),__METHOD__,__LINE__);
    }

    public function testObjTypeTFormDinPdoConnection_FailLine() {
        $this->expectException(InvalidArgumentException::class);
        $tpdo = null;
        $method = 'testConnect';
        $line = null;
        ValidateHelper::objTypeTFormDinPdoConnection($tpdo,$method,$line);
    }

    public function testObjTypeTFormDinPdoConnection_FailObj() {
        $this->expectException(InvalidArgumentException::class);
        $tpdo = new stdClass();
        $method = 'testConnect';
        $line = 1;
        ValidateHelper::objTypeTFormDinPdoConnection($tpdo,$method,$line);
    }

    public function testObjTypeTPDOConnectionObj_ok() {
        $this->expectNotToPerformAssertions();
        $tpdo = new TFormDinPdoConnection();
        $method = 'testConnect';
        $line = 1;
        ValidateHelper::objTypeTFormDinPdoConnection($tpdo,$method,$line);
    }
    /*
    public function testTriggerError_msgNull_typeErrosNull() {
        $this->expectNotice();
        $msg = null;
        $typeErro = null;
        ValidateHelper::triggerError($msg,$typeErro);
    }

    public function testTriggerError_msgNull_WARNING() {
        $this->expectWarning();
        $msg = null;
        $typeErro = ValidateHelper::WARNING;
        ValidateHelper::triggerError($msg,$typeErro);
    }
    public function testTriggerError_msgNull_ERROR() {
        $this->expectError();
        $msg = null;
        $typeErro = ValidateHelper::ERROR;
        ValidateHelper::triggerError($msg,$typeErro);
    }
    */

    public function testTriggerError_msgNull_Exceptio() {
        $this->expectException(InvalidArgumentException::class);
        $msg = null;
        $typeErro = ValidateHelper::EXECEPTION;
        ValidateHelper::triggerError($msg,$typeErro);
    }

    /*
    public function testMigrarMensage_msgNull_Exceptio() {
        $this->expectError();
        $msg = 'o metodo addButton MUDOU! o primeiro parametro agora recebe $this! o Restante está igual ;-)';
        ValidateHelper::migrarMensage($msg
                                     ,ValidateHelper::ERROR
                                     ,ValidateHelper::MSG_CHANGE
                                     ,__CLASS__,__METHOD__,__LINE__);
    }
    */

    public function testIsSet_Success() {
        $this->expectNotToPerformAssertions();
        ValidateHelper::isSet('some value', __METHOD__, __LINE__);
    }

    public function testIsSet_ExceptionNull() {
        $this->expectException(InvalidArgumentException::class);
        ValidateHelper::isSet(null, __METHOD__, __LINE__);
    }

    public function testIsArray_Success() {
        $this->expectNotToPerformAssertions();
        ValidateHelper::isArray([1, 2], __METHOD__, __LINE__);
    }

    public function testIsArray_ExceptionNotArray() {
        $this->expectException(InvalidArgumentException::class);
        ValidateHelper::isArray('string', __METHOD__, __LINE__);
    }

    public function testIsArray_ExceptionEmptyArray() {
        $this->expectException(InvalidArgumentException::class);
        ValidateHelper::isArray([], __METHOD__, __LINE__, true);
    }

    public function testIsArray_SuccessEmptyArrayNoValidation() {
        $this->expectNotToPerformAssertions();
        ValidateHelper::isArray([], __METHOD__, __LINE__, false);
    }

    public function testValidadeParam_SuccessNoValue() {
        $this->expectNotToPerformAssertions();
        ValidateHelper::validadeParam('param', null, ValidateHelper::EXECEPTION, ValidateHelper::MSG_CHANGE, __CLASS__, __METHOD__, __LINE__);
    }

    public function testValidadeParam_ExceptionWithValue() {
        $this->expectException(InvalidArgumentException::class);
        ValidateHelper::validadeParam('param', 'value', ValidateHelper::EXECEPTION, ValidateHelper::MSG_CHANGE, __CLASS__, __METHOD__, __LINE__);
    }

    public function testValidadeMethod_Exception() {
        $this->expectException(InvalidArgumentException::class);
        ValidateHelper::validadeMethod(ValidateHelper::EXECEPTION, ValidateHelper::MSG_NOT_IMPLEMENTED, __METHOD__, 'complement', __FILE__, __LINE__);
    }

    public function testMigrarMensage_SuccessNoMsg() {
        $this->expectNotToPerformAssertions();
        ValidateHelper::migrarMensage(null, ValidateHelper::EXECEPTION, ValidateHelper::MSG_DECREP, __CLASS__, __METHOD__, __LINE__);
    }

    public function testMigrarMensage_ExceptionWithMsg() {
        $this->expectException(InvalidArgumentException::class);
        ValidateHelper::migrarMensage('some message', ValidateHelper::EXECEPTION, ValidateHelper::MSG_DECREP, __CLASS__, __METHOD__, __LINE__, 'someFile.php');
    }

    public function testTriggerError_Exception() {
        $this->expectException(InvalidArgumentException::class);
        ValidateHelper::triggerError('error message', ValidateHelper::EXECEPTION);
    }

    public function testTypeErrorMsg_VariousCases() {
        $this->assertEquals(' não foi implementado!', ValidateHelper::typeErrorMsg(ValidateHelper::MSG_NOT_IMPLEMENTED));
        $this->assertEquals(' FOI ALTERADO!', ValidateHelper::typeErrorMsg(ValidateHelper::MSG_CHANGE));
        $this->assertEquals(' FOI DESCONTINUADO!!', ValidateHelper::typeErrorMsg('any_other'));
    }

    public function testTriggerError_UserWarning() {
        $msg = 'warning msg';
        $triggered = false;
        set_error_handler(function($errno, $errstr) use (&$triggered, $msg) {
            if ($errno === E_USER_WARNING && $errstr === $msg) {
                $triggered = true;
            }
            return true;
        });
        ValidateHelper::triggerError($msg, ValidateHelper::WARNING);
        restore_error_handler();
        $this->assertTrue($triggered);
    }

    public function testTriggerError_UserError() {
        $msg = 'error msg';
        $triggered = false;
        set_error_handler(function($errno, $errstr) use (&$triggered, $msg) {
            if ($errno === E_USER_ERROR && $errstr === $msg) {
                $triggered = true;
            }
            return true;
        });
        ValidateHelper::triggerError($msg, ValidateHelper::ERROR);
        restore_error_handler();
        $this->assertTrue($triggered);
    }

    public function testTriggerError_UserNotice() {
        $msg = 'notice msg';
        $triggered = false;
        set_error_handler(function($errno, $errstr) use (&$triggered, $msg) {
            if ($errno === E_USER_NOTICE && $errstr === $msg) {
                $triggered = true;
            }
            return true;
        });
        ValidateHelper::triggerError($msg, 'some_other_type');
        restore_error_handler();
        $this->assertTrue($triggered);
    }
}