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
    const SQL_SIZE_MYSQL    = 1648;
    const SQL_SIZE_ORACLE   = 'oracle';
    const SQL_SIZE_POSTGRES = 804;
    const SQL_SIZE_SQLITE   = 167;
    const SQL_SIZE_SQLSERVER= 1407;
    
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

    public function testSchema()
    {
        $expect = 'lalala';
        $type = TFormDinPdoConnection::DBMS_MYSQL;
        $this->classTest->setType($type);        
        $this->classTest->setSchema($expect);
        $result = $this->classTest->getSchema();
        $this->assertEquals($expect, $result);
    }

    public function testTableName()
    {
        $expect = 'lalala';
        $type = TFormDinPdoConnection::DBMS_MYSQL;
        $this->classTest->setType($type);        
        $this->classTest->setTableName($expect);
        $result = $this->classTest->getTableName();
        $this->assertEquals($expect, $result);
    }
    
    public function testSetType_fail()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->classTest->setType('xxx');
    }

    public function testGetType()
    {
        $type = TFormDinPdoConnection::DBMS_MYSQL;
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
        $this->assertEquals(self::SQL_SIZE_MYSQL, $length);
    }

    public function test_loadTablesFromDatabaseSqlServer()
    {
        $stringSql = $this->classTest->loadTablesFromDatabaseSqlServer();
        $length = mb_strlen($stringSql);
        $this->assertEquals(self::SQL_SIZE_SQLSERVER, $length);
    }

    public function test_loadTablesFromDatabasePostGres()
    {
        $stringSql = $this->classTest->loadTablesFromDatabasePostGres();
        $length = mb_strlen($stringSql);
        $this->assertEquals(self::SQL_SIZE_POSTGRES, $length);
    }

    public function testLoadSqlTablesFromDatabase_access()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->classTest->setType(TFormDinPdoConnection::DBMS_ACCESS);
        $stringSql = $this->classTest->loadSqlTablesFromDatabase();
    }

    public function testLoadSqlTablesFromDatabase_oracle()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->classTest->setType(TFormDinPdoConnection::DBMS_ORACLE);
        $stringSql = $this->classTest->loadSqlTablesFromDatabase();
    }

    public function testLoadSqlTablesFromDatabase_sqlLite()
    {
        $this->classTest->setType(TFormDinPdoConnection::DBMS_SQLITE);
        $stringSql = $this->classTest->loadSqlTablesFromDatabase();
        $length = mb_strlen($stringSql);
        $this->assertEquals(self::SQL_SIZE_SQLITE, $length);
    }

    public function testLoadSqlTablesFromDatabase_mysql()
    {
        $this->classTest->setType(TFormDinPdoConnection::DBMS_MYSQL);
        $stringSql = $this->classTest->loadSqlTablesFromDatabase();
        $length = mb_strlen($stringSql);
        $this->assertEquals(self::SQL_SIZE_MYSQL, $length);
    }

    public function testLoadSqlTablesFromDatabase_sqlserver()
    {
        $this->classTest->setType(TFormDinPdoConnection::DBMS_SQLSERVER);
        $stringSql = $this->classTest->loadSqlTablesFromDatabase();
        $length = mb_strlen($stringSql);
        $this->assertEquals(self::SQL_SIZE_SQLSERVER, $length);
    }

    public function testLoadSqlTablesFromDatabase_postgres()
    {
        $this->classTest->setType(TFormDinPdoConnection::DBMS_POSTGRES);
        $stringSql = $this->classTest->loadSqlTablesFromDatabase();
        $length = mb_strlen($stringSql);
        $this->assertEquals(self::SQL_SIZE_POSTGRES, $length);
    }

    public function testGetMsSqlShema_ShemaNull()
    {
        $expect = 'lalala';
        $type = TFormDinPdoConnection::DBMS_MYSQL;
        $this->classTest->setType($type);        
        $this->classTest->setSchema(null);
        $result = $this->classTest->getMsSqlShema();
        $length = mb_strlen($result);

        $this->assertEquals(null, $result);
        $this->assertEquals(0, $length);
    }

    public function testGetMsSqlShema_SetString()
    {
        $type = TFormDinPdoConnection::DBMS_MYSQL;
        $this->classTest->setType($type);        
        $this->classTest->setSchema('lalala');
        $result = $this->classTest->getMsSqlShema();
        $length = mb_strlen($result);

        $this->assertEquals(" AND upper(c.TABLE_SCHEMA) = upper('lalala') ", $result);
        $this->assertEquals(45, $length);
    }

    public function testGetSqlToFieldsFromOneStoredProcedureMySQL_mysql()
    {
        $this->classTest->setType(TFormDinPdoConnection::DBMS_MYSQL);
        $this->classTest->setSchema('lalala');
        $this->classTest->setTableName('xxx');
        $stringSql = $this->classTest->getSqlToFieldsFromOneStoredProcedureMySQL();
        $length = mb_strlen($stringSql);
        $this->assertEquals(1282, $length);
    }

    public function testGetSqlToFieldsFromOneStoredProcedureMySQL_sqlserver()
    {
        $this->classTest->setType(TFormDinPdoConnection::DBMS_SQLSERVER);
        $this->classTest->setSchema('lalala');
        $this->classTest->setTableName('xxx');
        $stringSql = $this->classTest->getSqlToFieldsFromOneStoredProcedureSqlServer();
        $length = mb_strlen($stringSql);
        $this->assertEquals(1249, $length);
    }

    public function testGetSqlToFieldsOneStoredProcedureFromDatabase_mysql()
    {
        $this->classTest->setType(TFormDinPdoConnection::DBMS_MYSQL);
        $this->classTest->setSchema('lalala');
        $this->classTest->setTableName('xxx');
        $result = $this->classTest->getSqlToFieldsOneStoredProcedureFromDatabase();
        $length = mb_strlen($result['sql']);
        $this->assertEquals(1282, $length);
    }

    public function testGetSqlToFieldsOneStoredProcedureFromDatabase_sqlserver()
    {
        $this->classTest->setType(TFormDinPdoConnection::DBMS_SQLSERVER);
        $this->classTest->setSchema('lalala');
        $this->classTest->setTableName('xxx');
        $result = $this->classTest->getSqlToFieldsOneStoredProcedureFromDatabase();
        $length = mb_strlen($result['sql']);
        $this->assertEquals(1249, $length);
    }

    public function testGetSqlToFieldsFromDatabaseMySQL()
    {
        $this->classTest->setType(TFormDinPdoConnection::DBMS_SQLSERVER);
        $this->classTest->setSchema('lalala');
        $this->classTest->setTableName('xxx');
        $result = $this->classTest->getSqlToFieldsFromDatabaseMySQL();
        $length = mb_strlen($result);
        $this->assertEquals(1054, $length);
    }

    public function testGetSqlToFieldsFromDatabaseSqlServer()
    {
        $this->classTest->setType(TFormDinPdoConnection::DBMS_SQLSERVER);
        $this->classTest->setSchema('lalala');
        $this->classTest->setTableName('xxx');
        $result = $this->classTest->getSqlToFieldsFromDatabaseSqlServer();
        $length = mb_strlen($result);
        $this->assertEquals(3865, $length);
    }

    public function testGetSqlToFieldsFromDatabasePostGres()
    {
        $this->classTest->setType(TFormDinPdoConnection::DBMS_POSTGRES);
        $this->classTest->setSchema('lalala');
        $this->classTest->setTableName('xxx');
        $result = $this->classTest->getSqlToFieldsFromDatabasePostGres();
        $length = mb_strlen($result);
        $this->assertEquals(2457, $length);
    }
}