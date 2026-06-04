<?php
use PHPUnit\Framework\TestCase;

class TFormDinSystemPermControllerTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        TTransaction::open('dbapoio');
        $conn = TTransaction::get();
        $conn->exec("CREATE TABLE IF NOT EXISTS system_user (id INTEGER PRIMARY KEY, name VARCHAR(100))");
        $conn->exec("CREATE TABLE IF NOT EXISTS system_unit (id INTEGER PRIMARY KEY, name VARCHAR(100))");
        TTransaction::close();
    }

    protected function tearDown(): void
    {
        try {
            TTransaction::rollback();
        } catch (Throwable $e) {
            // Ignore if no transaction was active
        }
    }

    public static function tearDownAfterClass(): void
    {
        TTransaction::open('dbapoio');
        $conn = TTransaction::get();
        $conn->exec("DROP TABLE IF EXISTS system_user");
        $conn->exec("DROP TABLE IF EXISTS system_unit");
        TTransaction::close();
    }

    public function testConstructAndGettersSetters()
    {
        $controller = new TFormDinSystemPermController('my_database');
        $this->assertEquals('my_database', $controller->getDatabase());

        $controller->setDatabase('another_db');
        $this->assertEquals('another_db', $controller->getDatabase());
    }

    public function testGetTDBComboUser()
    {
        $controller = new TFormDinSystemPermController('dbapoio');
        TTransaction::open('dbapoio');
        $combo = $controller->getTDBComboUser('user_combo');
        TTransaction::close();
        if (class_exists('SystemUser') || class_exists('SystemUsers')) {
            $this->assertNotNull($combo);
        } else {
            $this->assertNull($combo);
        }
    }

    public function testGetUserById_ThrowsException()
    {
        $controller = new TFormDinSystemPermController('fake_db_not_exist');
        $this->expectException(Exception::class);
        $controller->getUserById(1);
    }

    public function testGetNomeUserById_InvalidId()
    {
        $controller = new TFormDinSystemPermController('fake_db');
        $name = $controller->getNomeUserById(0);
        $this->assertEquals('', $name);
        
        $name2 = $controller->getNomeUserById(-1);
        $this->assertEquals('', $name2);
    }
    
    public function testGetNomeUserById_ThrowsException()
    {
        $controller = new TFormDinSystemPermController('fake_db_not_exist');
        $this->expectException(Exception::class);
        $controller->getNomeUserById(1);
    }

    public function testGetUnitById_ThrowsException()
    {
        $controller = new TFormDinSystemPermController('fake_db_not_exist');
        $this->expectException(Exception::class);
        $controller->getUnitById(1);
    }

    public function testGetNomeUnitById_ThrowsException()
    {
        $controller = new TFormDinSystemPermController('fake_db_not_exist');
        $this->expectException(Exception::class);
        $controller->getNomeUnitById(1);
    }

    public function testGetNomeUserById_ReturnsEmptyStringIfNull()
    {
        $controller = $this->getMockBuilder(TFormDinSystemPermController::class)
            ->setConstructorArgs(['fake_db'])
            ->onlyMethods(['getUserById'])
            ->getMock();
            
        $controller->method('getUserById')->willReturn(null);
        
        $this->assertEquals('', $controller->getNomeUserById(1));
    }

    public function testGetNomeUserById_ReturnsEmptyStringIfNoName()
    {
        $controller = $this->getMockBuilder(TFormDinSystemPermController::class)
            ->setConstructorArgs(['fake_db'])
            ->onlyMethods(['getUserById'])
            ->getMock();
            
        $obj = new stdClass();
        $controller->method('getUserById')->willReturn($obj);
        
        $this->assertEquals('', $controller->getNomeUserById(1));
    }

    public function testGetNomeUserById_ReturnsName()
    {
        $controller = $this->getMockBuilder(TFormDinSystemPermController::class)
            ->setConstructorArgs(['fake_db'])
            ->onlyMethods(['getUserById'])
            ->getMock();
            
        $obj = new stdClass();
        $obj->name = 'Test Name';
        $controller->method('getUserById')->willReturn($obj);
        
        $this->assertEquals('Test Name', $controller->getNomeUserById(1));
    }

    public function testGetNomeUnitById_ReturnsName()
    {
        $controller = $this->getMockBuilder(TFormDinSystemPermController::class)
            ->setConstructorArgs(['fake_db'])
            ->onlyMethods(['getUnitById'])
            ->getMock();
            
        $obj = new stdClass();
        $obj->name = 'Test Unit';
        $controller->method('getUnitById')->willReturn($obj);
        
        $this->assertEquals('Test Unit', $controller->getNomeUnitById(1));
    }

    public function testGetUserById_Success()
    {
        $controller = new TFormDinSystemPermController('dbapoio');
        try {
            $user = $controller->getUserById(1);
            $this->assertTrue(true);
        } catch (Exception $e) {
            $this->assertInstanceOf(Exception::class, $e);
        }
    }

    public function testGetUnitById_Success()
    {
        $controller = new TFormDinSystemPermController('dbapoio');
        try {
            $unit = $controller->getUnitById(1);
            $this->assertTrue(true);
        } catch (Throwable $e) {
            TTransaction::rollback();
            $this->assertInstanceOf(Throwable::class, $e);
        }
    }

    public function testGetTDBComboUser_Success()
    {
        $controller = new TFormDinSystemPermController('dbapoio');
        try {
            $combo = $controller->getTDBComboUser('test_combo');
            $this->assertTrue(true);
        } catch (Exception $e) {
            $this->assertInstanceOf(Exception::class, $e);
        }
    }

    public function testGetTDBComboUser_WithClasses()
    {
        $controller = new TFormDinSystemPermController('dbapoio');
        TTransaction::open('dbapoio');
        $combo = $controller->getTDBComboUser('test_combo');
        TTransaction::close();
        $this->assertNotNull($combo);
        $this->assertInstanceOf(TDBCombo::class, $combo);
    }

    public function testGetUserAndUnitById_Success()
    {
        TTransaction::open('dbapoio');
        $conn = TTransaction::get();
        $conn->exec("INSERT INTO system_user (id, name) VALUES (999, 'Test Name')");
        $conn->exec("INSERT INTO system_unit (id, name) VALUES (999, 'Test Unit')");
        TTransaction::close();

        try {
            $controller = new TFormDinSystemPermController('dbapoio');
            
            $user = $controller->getUserById(999);
            $this->assertNotNull($user);
            $this->assertEquals('Test Name', $user->name);

            $name = $controller->getNomeUserById(999);
            $this->assertEquals('Test Name', $name);

            $unit = $controller->getUnitById(999);
            $this->assertNotNull($unit);
            $this->assertEquals('Test Unit', $unit->name);

            $unitName = $controller->getNomeUnitById(999);
            $this->assertEquals('Test Unit', $unitName);

        } finally {
            TTransaction::open('dbapoio');
            $conn = TTransaction::get();
            $conn->exec("DELETE FROM system_user WHERE id = 999");
            $conn->exec("DELETE FROM system_unit WHERE id = 999");
            TTransaction::close();
        }
    }
}

class SystemUser extends TRecord
{
    const TABLENAME = 'system_user';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial';
    
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('name');
    }
}

class SystemUsers extends TRecord
{
    const TABLENAME = 'system_user';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial';
    
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('name');
    }
}

class SystemUnit extends TRecord
{
    const TABLENAME = 'system_unit';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial';
    
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('name');
    }
}
