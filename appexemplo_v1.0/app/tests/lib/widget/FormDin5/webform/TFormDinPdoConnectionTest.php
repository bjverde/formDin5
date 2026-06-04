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
require_once  __DIR__.'/../../mockDatabaseApoio.php';

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Error\Warning;

class TFormDinPdoConnectionTest extends TestCase
{

    private $classTest;
    
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->classTest = new TFormDinPdoConnection();
    }
    
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown(): void {
        $this->classTest = null;
        parent::tearDown();
    }

    public function testSetType_null()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->classTest->setType(null);
    }
    public function testSetType_wrongType()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->classTest->setType('abc');
    }
    public function testSetType_correctType()
    {
        $this->expectNotToPerformAssertions();
        $this->classTest->setType(TFormDinPdoConnection::DBMS_SQLITE);
    }

    public function testSetDatabase_fail_null()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->classTest->setDatabase(null);
    }
    public function testSetDatabase_fail_int()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->classTest->setDatabase(10);
    }
    public function testSetDatabase_ok_string()
    {
        $this->expectNotToPerformAssertions();
        $this->classTest->setDatabase('main config');
    }    

    //----------------------------------------------------------------------
    //----------------------------------------------------------------------
    //----------------------------------------------------------------------
    public function testGetDefaulPort_PostGre()
    {   
        $this->classTest->setType(TFormDinPdoConnection::DBMS_POSTGRES);
        $result = $this->classTest->getDefaulPort();
        $this->assertEquals(5432, $result);
    }
    public function testGetDefaulPort_MySQL()
    {   
        $this->classTest->setType(TFormDinPdoConnection::DBMS_MYSQL);
        $result = $this->classTest->getDefaulPort();
        $this->assertEquals(3306, $result);
    }
    public function testGetDefaulPort_SqlServer()
    {   
        $this->classTest->setType(TFormDinPdoConnection::DBMS_SQLSERVER);
        $result = $this->classTest->getDefaulPort();
        $this->assertEquals(1433, $result);
    }
    //---------------------------------------------
    public function testGetConfigConnect_null()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->classTest->getConfigConnect();
    }

    public function testGetConfigConnect_SetDatabase()
    {
        $database = 'formdin';
        $this->classTest->setDatabase($database);
        $result = $this->classTest->getConfigConnect();

        $this->assertCount(2, $result);
        $this->assertEquals($database, $result['database']);
        $this->assertEquals(null, $result['db']);
    }

    public function testGetConfigConnect_nullDatabaseSetTypeOnly()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->classTest->setType(TFormDinPdoConnection::DBMS_SQLITE);
        $result = $this->classTest->getConfigConnect();
    }

    public function testGetConfigConnect_nullDatabaseGetDb()
    {   
        $name = 'bdApoio.s3db';
        $this->classTest->setName($name);
        $this->classTest->setType(TFormDinPdoConnection::DBMS_SQLITE);
        $result = $this->classTest->getConfigConnect();

        $this->assertCount(2, $result);
        $this->assertEquals(null, $result['database']);
        $this->assertCount(7, $result['db']);
        $this->assertEquals(TFormDinPdoConnection::DBMS_SQLITE, $result['db']['type']);
        $this->assertEquals($name, $result['db']['name']);
    }
    //----------------------------------------------------------------------
    //----------------------------------------------------------------------
    //----------------------------------------------------------------------
    public function testValidarQtdParametros_SqlNull()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->classTest->validarQtdParametros(null, null);
    }
    public function testValidarQtdParametros_SqlNotString()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->classTest->validarQtdParametros(1, null);
    }    
    public function testValidarQtdParametros_ParamNull()
    {
        $this->expectNotToPerformAssertions();
        $sql = 'select * from dado_apoio order by seq_dado_apoio';
        $this->classTest->validarQtdParametros($sql, null);
    }

    public function testValidarQtdParametros_Param_NotArray()
    {
        $this->expectException(InvalidArgumentException::class);
        $sql = 'insert into form_exemplo.tipo(
                                            descricao
                                           ,idmeta_tipo
                                           ,sit_ativo
                                        ) values (?,?,?)';
        $arrParams = new stdClass();
        $this->classTest->validarQtdParametros($sql, $arrParams);
    }

    public function testValidarQtdParametros_QtdParam_FailZero()
    {
        $this->expectException(InvalidArgumentException::class);
        $sql = 'insert into form_exemplo.tipo(
                                            descricao
                                           ,idmeta_tipo
                                           ,sit_ativo
                                        ) values (?,?,?)';
        $arrParams = array();
        $this->classTest->validarQtdParametros($sql, $arrParams);
    }

    public function testValidarQtdParametros_QtdParam_FailDois()
    {
        $this->expectException(InvalidArgumentException::class);
        $sql = 'insert into form_exemplo.tipo(
                                            descricao
                                           ,idmeta_tipo
                                           ,sit_ativo
                                        ) values (?,?,?)';
        $arrParams = array();
        $arrParams[]='desc';
        $arrParams[]=10;
        $this->classTest->validarQtdParametros($sql, $arrParams);
    }

    public function testValidarQtdParametros_QtdParam_OK()
    {
        $this->expectNotToPerformAssertions();
        $sql = 'insert into form_exemplo.tipo(
                                            descricao
                                           ,idmeta_tipo
                                           ,sit_ativo
                                        ) values (?,?,?)';
        $arrParams = array();
        $arrParams[]='desc';
        $arrParams[]=10;
        $arrParams[]='S';
        $this->classTest->validarQtdParametros($sql, $arrParams);
    }

    //----------------------------------------------------------------------
    //----------------------------------------------------------------------
    //----------------------------------------------------------------------
    public function testExecuteSql_sqllite_FailSql()
    {   
        $this->expectException(Exception::class);
        $this->classTest->setName(mockDatabaseApoio::getPathDatabaseApoio());
        $this->classTest->setType(TFormDinPdoConnection::DBMS_SQLITE);
        $sql = 'select12 * from dado_apoio order by seq_dado_apoio';
        
        $oldErrorLog = ini_get('error_log');
        ini_set('error_log', DIRECTORY_SEPARATOR === '\\' ? 'nul' : '/dev/null');
        try {
            $this->classTest->executeSql($sql);
        } finally {
            ini_set('error_log', $oldErrorLog);
        }
    }

    public function testExecuteSql_sqllite_upperCase_Adianti()
    {   
        $this->classTest->setName(mockDatabaseApoio::getPathDatabaseApoio());
        $this->classTest->setType(TFormDinPdoConnection::DBMS_SQLITE);
        $this->classTest->setCase(PDO::CASE_UPPER);
        $sql = 'select * from dado_apoio order by seq_dado_apoio';
            $result = $this->classTest->executeSql($sql);

        $this->assertCount(3, $result);
        $this->assertEquals(1, $result[0]->SEQ_DADO_APOIO);
        $this->assertEquals('Metro', $result[1]->TIP_DADO_APOIO);
        $this->assertEquals('KM', $result[2]->SIG_DADO_APOIO);
    }

    public function testExecuteSql_sqllite_lowerCase_Adianti()
    {   
        $this->classTest->setName(mockDatabaseApoio::getPathDatabaseApoio());
        $this->classTest->setType(TFormDinPdoConnection::DBMS_SQLITE);
        $this->classTest->setCase(PDO::CASE_LOWER);
        $sql = 'select * from dado_apoio order by seq_dado_apoio';
        $result = $this->classTest->executeSql($sql);

        $this->assertCount(3, $result);
        $this->assertequals(1, $result[0]->seq_dado_apoio);
        $this->assertequals('Metro', $result[1]->tip_dado_apoio);
        $this->assertequals('KM', $result[2]->sig_dado_apoio);
    }    

    public function testExecuteSql_sqllite_upperCase_pdo()
    {   
        $this->classTest->setName(mockDatabaseApoio::getPathDatabaseApoio());
        $this->classTest->setType(TFormDinPdoConnection::DBMS_SQLITE);
        $this->classTest->setFech(PDO::FETCH_ASSOC);
        $this->classTest->setCase(PDO::CASE_UPPER);
        $sql = 'select * from dado_apoio order by seq_dado_apoio';
        $result = $this->classTest->executeSql($sql);

        $this->assertCount(3, $result);
        $this->assertEquals(1, $result[0]['SEQ_DADO_APOIO']);
        $this->assertEquals('Metro', $result[1]['TIP_DADO_APOIO']);
        $this->assertEquals('KM', $result[2]['SIG_DADO_APOIO']);
    }

    public function testExecuteSql_sqllite_lowerCase_pdo()
    {   
        $this->classTest->setName(mockDatabaseApoio::getPathDatabaseApoio());
        $this->classTest->setType(TFormDinPdoConnection::DBMS_SQLITE);
        $this->classTest->setFech(PDO::FETCH_ASSOC);
        $this->classTest->setCase(PDO::CASE_LOWER);
        $sql = 'select * from dado_apoio order by seq_dado_apoio';
        $result = $this->classTest->executeSql($sql);

        $this->assertCount(3, $result);
        $this->assertequals(1, $result[0]['seq_dado_apoio']);
        $this->assertequals('Metro', $result[1]['tip_dado_apoio']);
        $this->assertequals('KM', $result[2]['sig_dado_apoio']);
    }

    public function testExecuteSql_sqllite_lowerCase_formDin()
    {   
        $this->classTest->setName(mockDatabaseApoio::getPathDatabaseApoio());
        $this->classTest->setType(TFormDinPdoConnection::DBMS_SQLITE);
        $this->classTest->setCase(PDO::CASE_LOWER);
        $this->classTest->setOutputFormat(ArrayHelper::TYPE_FORMDIN);
        $sql = 'select * from dado_apoio order by seq_dado_apoio';
        $result = $this->classTest->executeSql($sql);

        $this->assertCount(3, $result['seq_dado_apoio']);
        $this->assertequals(1, $result['seq_dado_apoio'][0]);
        $this->assertequals('Metro', $result['tip_dado_apoio'][1]);
        $this->assertequals('KM', $result['sig_dado_apoio'][2]);
    }

    public function testExecuteSql_sqllite_upperCase_formDin()
    {   
        $this->classTest->setName(mockDatabaseApoio::getPathDatabaseApoio());
        $this->classTest->setType(TFormDinPdoConnection::DBMS_SQLITE);
        $this->classTest->setCase(PDO::CASE_UPPER);
        $this->classTest->setOutputFormat(ArrayHelper::TYPE_FORMDIN);
        $sql = 'select * from dado_apoio order by seq_dado_apoio';
        $result = $this->classTest->executeSql($sql);

        $this->assertCount(3, $result['SEQ_DADO_APOIO']);
        $this->assertequals(1, $result['SEQ_DADO_APOIO'][0]);
        $this->assertequals('Metro', $result['TIP_DADO_APOIO'][1]);
        $this->assertequals('KM', $result['SIG_DADO_APOIO'][2]);
    }
    public function testGetDbms()
    {
        $this->classTest->setType(TFormDinPdoConnection::DBMS_SQLSERVER);
        $this->assertEquals(TFormDinPdoConnection::DBMS_SQLSERVER, $this->classTest->getDbms());
    }

    public function testGetListDBMS()
    {
        $list = TFormDinPdoConnection::getListDBMS();
        $this->assertIsArray($list);
        $this->assertArrayHasKey(TFormDinPdoConnection::DBMS_MYSQL, $list);
    }

    public function testGetDefaulPort_Oracle()
    {   
        $reflection = new ReflectionClass(TFormDinPdoConnection::class);
        $property = $reflection->getProperty('type');
        $property->setAccessible(true);
        $property->setValue($this->classTest, TFormDinPdoConnection::DBMS_ORACLE);

        $result = $this->classTest->getDefaulPort();
        $this->assertEquals(1521, $result);
    }

    public function testConstructor()
    {
        $conn = new TFormDinPdoConnection(null, ArrayHelper::TYPE_PDO, PDO::FETCH_ASSOC, PDO::CASE_LOWER);
        $this->assertEquals(ArrayHelper::TYPE_PDO, $conn->getOutputFormat());
        $this->assertEquals(PDO::FETCH_ASSOC, $conn->getFech());
        $this->assertEquals(PDO::CASE_LOWER, $conn->getCase());
    }

    public function testPrepareArray()
    {
        $arr = [
            'a' => 'value',
            'b' => 0,
            'c' => '0',
            'd' => null,
            'e' => ''
        ];
        $result = $this->classTest->prepareArray($arr);
        $this->assertEquals('value', $result['a']);
        $this->assertEquals(0, $result['b']);
        $this->assertEquals('0', $result['c']);
        $this->assertNull($result['d']);
        $this->assertNull($result['e']);
        
        $this->assertEquals([], $this->classTest->prepareArray(null));
    }

    public function testGetArrayKeyValueBySql()
    {
        $this->classTest->setName(mockDatabaseApoio::getPathDatabaseApoio());
        $this->classTest->setType(TFormDinPdoConnection::DBMS_SQLITE);
        $this->classTest->setFech(PDO::FETCH_ASSOC);
        $this->classTest->setCase(PDO::CASE_LOWER);

        $sql = 'select * from dado_apoio order by seq_dado_apoio limit 2';
        $result = $this->classTest->getArrayKeyValueBySql('seq_dado_apoio', 'sig_dado_apoio', $sql);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertArrayHasKey(1, $result);
        $this->assertArrayHasKey(2, $result);
    }
    
    public function testHostPortUserPass()
    {
        $this->classTest->setHost('localhost');
        $this->assertEquals('localhost', $this->classTest->getHost());

        $this->classTest->setPort(3306);
        $this->assertEquals(3306, $this->classTest->getPort());

        $this->classTest->setUser('root');
        $this->assertEquals('root', $this->classTest->getUser());

        $this->classTest->setPass('123');
        $this->assertEquals('123', $this->classTest->getPass());
        
        $this->classTest->setOpts('opts');
        $this->assertEquals('opts', $this->classTest->getOpts());
    }
    
    public function testExecuteSql_pragma()
    {
        $this->classTest->setName(mockDatabaseApoio::getPathDatabaseApoio());
        $this->classTest->setType(TFormDinPdoConnection::DBMS_SQLITE);
        $sql = 'PRAGMA table_info(dado_apoio)';
        $result = $this->classTest->executeSql($sql);
        $this->assertIsArray($result);
    }

    public function testConstructorWithDatabase()
    {
        $conn = new TFormDinPdoConnection('dbapoio');
        $this->assertEquals('dbapoio', $conn->getDatabase());
    }

    public function testSetDdms()
    {
        $this->classTest->setDdms(TFormDinPdoConnection::DBMS_SQLITE);
        $this->assertEquals(TFormDinPdoConnection::DBMS_SQLITE, $this->classTest->getType());
    }

    public function testExecuteSql_update()
    {
        $this->classTest->setName(mockDatabaseApoio::getPathDatabaseApoio());
        $this->classTest->setType(TFormDinPdoConnection::DBMS_SQLITE);
        
        $sql = "update dado_apoio set tip_dado_apoio = 'Metro' where seq_dado_apoio = 2";
        $result = $this->classTest->executeSql($sql);
        $this->assertTrue((bool)$result);
    }
    
    public function testExecuteSql_insert()
    {
        $this->classTest->setName(mockDatabaseApoio::getPathDatabaseApoio());
        $this->classTest->setType(TFormDinPdoConnection::DBMS_SQLITE);
        
        $sql = "insert into dado_apoio(tip_dado_apoio, sig_dado_apoio) values ('T', 'TT')";
        $result = $this->classTest->executeSql($sql);
        
        // cleanup so we don't break other tests
        $this->classTest->executeSql("delete from dado_apoio where tip_dado_apoio = 'T'");
        
        $this->assertTrue((bool)$result);
    }

    public function testExecuteSql_returning()
    {
        $this->classTest->setName(mockDatabaseApoio::getPathDatabaseApoio());
        $this->classTest->setType(TFormDinPdoConnection::DBMS_SQLITE);
        $sql = "update dado_apoio set tip_dado_apoio = 'Metro' where seq_dado_apoio = 1 RETURNING *";
        $result = $this->classTest->executeSql($sql);
        $this->assertIsArray($result);
    }

    public function testExecuteSql_with()
    {
        $this->classTest->setName(mockDatabaseApoio::getPathDatabaseApoio());
        $this->classTest->setType(TFormDinPdoConnection::DBMS_SQLITE);
        $sql = "WITH cte AS (SELECT 1 AS n) SELECT * FROM cte";
        $result = $this->classTest->executeSql($sql);
        $this->assertIsArray($result);
    }
}