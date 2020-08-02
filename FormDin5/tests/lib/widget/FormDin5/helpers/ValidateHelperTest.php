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

    public function testTriggerError_msgNull_Exceptio() {
        $this->expectException(InvalidArgumentException::class);
        $msg = null;
        $typeErro = ValidateHelper::EXECEPTION;
        ValidateHelper::triggerError($msg,$typeErro);
    }

    public function testMigrarMensage_msgNull_Exceptio() {
        $this->expectError();
        $msg = 'o metodo addButton MUDOU! o primeiro parametro agora recebe $this! o Restante está igual ;-)';
        ValidateHelper::migrarMensage($msg
                                     ,ValidateHelper::ERROR
                                     ,ValidateHelper::MSG_CHANGE
                                     ,__CLASS__,__METHOD__,__LINE__);
    }
    
}