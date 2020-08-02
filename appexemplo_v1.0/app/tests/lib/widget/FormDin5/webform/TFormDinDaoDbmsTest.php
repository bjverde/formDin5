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

$path =  __DIR__.'/../../../../../';
//require_once $path.'tests/initTest.php';

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Error\Warning;

class TFormDinDaoDbmsTest extends TestCase
{

    private $classTest;
    const SQL_SIZE_ACCESS   = 'ACCESS';
    const SQL_SIZE_FIREBIRD = 'ibase';
    const SQL_SIZE_MYSQL    = 'mysql';
    const SQL_SIZE_ORACLE   = 'oracle';
    const SQL_SIZE_POSTGRES = 'pgsql';
    const SQL_SIZE_SQLITE   = 162;
    const SQL_SIZE_SQLSERVER= 'sqlsrv';
    
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->classTest = new TFormDinDaoDbms(null,TFormDinPdoConnection::DBMS_SQLITE);
    }
    
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown(): void {
        $this->classTest = null;
        parent::tearDown();
    }
    
    public function testSetType_fail()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->classTest->setType('xxx');
    }

    public function testGetType()
    {
        $type = TFormDinPdoConnection::DBMS_ORACLE;
        $this->classTest->setType($type);
        $result = $this->classTest->getType();
        $this->assertEquals($type, $result);
    }

    public function testSetConnection_fail()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->classTest->setConnection('xxx');
    }

    public function testGetConnection()
    {
        $connectionResult = $this->classTest->getConnection();
        $this->assertInstanceOf(TFormDinPdoConnection::class, $connectionResult);
    }

    public function test_loadTablesFromDatabaseSqLite()
    {
        $stringSql = $this->classTest->loadTablesFromDatabaseSqLite();
        $length = mb_strlen($stringSql);
        $this->assertEquals(self::SQL_SIZE_SQLITE, $length);
    }

    public function test_loadTablesFromDatabaseMySql()
    {
        $stringSql = $this->classTest->loadTablesFromDatabaseMySql();
        $length = mb_strlen($stringSql);
        $this->assertEquals(1597, $length);
    }

    public function test_loadTablesFromDatabaseSqlServer()
    {
        $stringSql = $this->classTest->loadTablesFromDatabaseSqlServer();
        $length = mb_strlen($stringSql);
        $this->assertEquals(1361, $length);
    }

    public function test_loadTablesFromDatabasePostGres()
    {
        $stringSql = $this->classTest->loadTablesFromDatabasePostGres();
        $length = mb_strlen($stringSql);
        $this->assertEquals(783, $length);
    }

    public function testLoadSqlTablesFromDatabase_sqlLite()
    {
        $this->classTest->setType(TFormDinPdoConnection::DBMS_SQLITE);
        $stringSql = $this->classTest->loadSqlTablesFromDatabase();
        $length = mb_strlen($stringSql);
        $this->assertEquals(self::SQL_SIZE_SQLITE, $length);
    }

}