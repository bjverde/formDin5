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
        $this->assertCount(6, $result['db']);
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
        $path =  __DIR__.'/../../../../../';
        $name = $path.'database/bdApoio.s3db';
        $this->classTest->setName($name);
        $this->classTest->setType(TFormDinPdoConnection::DBMS_SQLITE);
        $sql = 'select12 * from dado_apoio order by seq_dado_apoio';
        
        $this->classTest->executeSql($sql);
    }

    public function testExecuteSql_sqllite_upperCase_Adianti()
    {   
        $path =  __DIR__.'/../../../../../';
        $name = $path.'database/bdApoio.s3db';
        $this->classTest->setName($name);
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
        $path =  __DIR__.'/../../../../../';
        $name = $path.'database/bdApoio.s3db';
        $this->classTest->setName($name);
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
        $path =  __DIR__.'/../../../../../';
        $name = $path.'database/bdApoio.s3db';
        $this->classTest->setName($name);
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
        $path =  __DIR__.'/../../../../../';
        $name = $path.'database/bdApoio.s3db';
        $this->classTest->setName($name);
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
        $path =  __DIR__.'/../../../../../';
        $name = $path.'database/bdApoio.s3db';
        $this->classTest->setName($name);
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
        $path =  __DIR__.'/../../../../../';
        $name = $path.'database/bdApoio.s3db';
        $this->classTest->setName($name);
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


}