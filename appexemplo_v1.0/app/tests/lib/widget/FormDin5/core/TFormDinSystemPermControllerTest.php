<?php
use PHPUnit\Framework\TestCase;

class TFormDinSystemPermControllerTest extends TestCase
{
    public function testConstructAndGettersSetters()
    {
        $controller = new TFormDinSystemPermController('my_database');
        $this->assertEquals('my_database', $controller->getDatabase());

        $controller->setDatabase('another_db');
        $this->assertEquals('another_db', $controller->getDatabase());
    }

    public function testGetTDBComboUser()
    {
        $controller = new TFormDinSystemPermController('my_database');
        $combo = $controller->getTDBComboUser('user_combo');
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
}
