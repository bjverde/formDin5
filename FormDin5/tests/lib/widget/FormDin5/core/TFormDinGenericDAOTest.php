<?php
use PHPUnit\Framework\TestCase;

class TFormDinGenericDAOTest extends TestCase
{
    protected function tearDown(): void
    {
        try {
            TTransaction::rollback();
        } catch (Throwable $e) {
            // Ignore if no transaction was active
        }
    }

    public function testConstructAndGettersSetters()
    {
        $tpdoMock = $this->createMock(TFormDinPdoConnection::class);
        $dao = new TFormDinGenericDAO('my_db', 'MyRepository', $tpdoMock);

        $this->assertEquals('my_db', $dao->getDatabase());
        $this->assertEquals('MyRepository', $dao->getRepository());
        $this->assertSame($tpdoMock, $dao->getTPDOConnection());

        $dao->setDatabase('new_db');
        $this->assertEquals('new_db', $dao->getDatabase());

        $dao->setRepository('NewRepo');
        $this->assertEquals('NewRepo', $dao->getRepository());
    }

    public function testGetDatabaseInfo()
    {
        $dao = new TFormDinGenericDAO('dbapoio');
        ob_start();
        $dao->getDatabaseInfo();
        $output = ob_get_clean();
        $this->assertNotEmpty($output);
    }

    public function testGetDatabaseInfoException()
    {
        $this->expectException(Exception::class);
        $dao = new TFormDinGenericDAO('dbapoio');
        $dao->setDatabase('invalid_database');
        $dao->getDatabaseInfo();
    }

    public function testExecuteSelect()
    {
        $dao = new TFormDinGenericDAO('dbapoio');
        $result = $dao->executeSelect('SELECT * FROM dado_apoio');
        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(3, count($result));
    }

    public function testExecuteSelectException()
    {
        $dao = new TFormDinGenericDAO('dbapoio');
        $this->expectException(Exception::class);
        $dao->executeSelect('SELECT * FROM invalid_table');
    }

    public function testExecuteSelectCount()
    {
        $dao = new TFormDinGenericDAO('dbapoio');
        $result = $dao->executeSelectCount('SELECT count(*) as total FROM dado_apoio');
        $this->assertIsArray($result);
        $this->assertArrayHasKey('total', $result);
    }

    public function testExecuteSelectCountException()
    {
        $dao = new TFormDinGenericDAO('dbapoio');
        $this->expectException(Exception::class);
        $dao->executeSelectCount('SELECT count(*) as total FROM invalid_table');
    }

    public function testExecuteWriteAndExceptions()
    {
        $dao = new TFormDinGenericDAO('dbapoio');
        
        // Test Insert
        $insertSql = "INSERT INTO dado_apoio (tip_dado_apoio, sig_dado_apoio) VALUES (?, ?)";
        $newId = $dao->execute($insertSql, ['TempType', 'TT']);
        $this->assertNotNull($newId);
        $this->assertGreaterThan(0, (int)$newId);

        // Test Update
        $updateSql = "UPDATE dado_apoio SET sig_dado_apoio = ? WHERE seq_dado_apoio = ?";
        $affectedRows = $dao->execute($updateSql, ['TT2', $newId]);
        $this->assertEquals(1, $affectedRows);

        // Test Delete
        $deleteSql = "DELETE FROM dado_apoio WHERE seq_dado_apoio = ?";
        $deletedRows = $dao->execute($deleteSql, [$newId]);
        $this->assertEquals(1, $deletedRows);

        // Test Exception
        $this->expectException(Exception::class);
        $dao->execute("INSERT INTO invalid_table (col) VALUES (?)", ['val']);
    }

    public function testGetArrayByCriteria()
    {
        $dao = new TFormDinGenericDAO('dbapoio', 'ApoioRecord');
        $criteria = new TCriteria();
        $criteria->add(new TFilter('seq_dado_apoio', '=', 1));
        
        ob_start();
        $result = $dao->getArrayByCriteria($criteria, true);
        $output = ob_get_clean();
        
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('1', $result[0]->seq_dado_apoio);
    }

    public function testGetArrayByCriteriaException()
    {
        $dao = new TFormDinGenericDAO('dbapoio', 'InvalidRecordClass');
        $criteria = new TCriteria();
        $this->expectException(Exception::class);
        $dao->getArrayByCriteria($criteria);
    }

    public function testGetListObjByCriteria()
    {
        $dao = new TFormDinGenericDAO('dbapoio', 'ApoioRecord');
        $criteria = new TCriteria();
        $criteria->add(new TFilter('seq_dado_apoio', '=', 1));
        
        ob_start();
        $result = $dao->getListObjByCriteria($criteria, true);
        $output = ob_get_clean();
        
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(ApoioRecord::class, $result[0]);
    }

    public function testGetListObjByCriteriaException()
    {
        $dao = new TFormDinGenericDAO('dbapoio', 'InvalidRecordClass');
        $criteria = new TCriteria();
        $this->expectException(Exception::class);
        $dao->getListObjByCriteria($criteria);
    }
}

class ApoioRecord extends TRecord
{
    const TABLENAME = 'dado_apoio';
    const PRIMARYKEY = 'seq_dado_apoio';
    const IDPOLICY = 'serial';

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('tip_dado_apoio');
        parent::addAttribute('sig_dado_apoio');
    }
}
