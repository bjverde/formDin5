<?php
use PHPUnit\Framework\TestCase;

class TFormDinGenericControllerTest extends TestCase
{
    private $daoMock;
    private $controller;

    protected function setUp(): void
    {
        $this->daoMock = $this->getMockBuilder(TFormDinGenericDAO::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'selectById', 
                'selectCount', 
                'selectAllPagination', 
                'selectAll', 
                'selectByTCriteria', 
                'selectByTCriteriaCount', 
                'getArrayByCriteria', 
                'getListObjByCriteria'
            ])
            ->getMock();
        $this->controller = new TFormDinGenericController($this->daoMock);
    }

    public function testGetSetDao()
    {
        $newDaoMock = $this->createMock(TFormDinGenericDAO::class);
        $this->controller->setDao($newDaoMock);
        $this->assertSame($newDaoMock, $this->controller->getDao());
    }

    public function testSelectById()
    {
        $id = 1;
        $expectedResult = ['id' => 1, 'name' => 'Test'];
        $this->daoMock->expects($this->once())
            ->method('selectById')
            ->with($id)
            ->willReturn($expectedResult);

        $result = $this->controller->selectById($id);
        $this->assertEquals($expectedResult, $result);
    }

    public function testSelectCount()
    {
        $where = 'id > 0';
        $expectedResult = 10;
        $this->daoMock->expects($this->once())
            ->method('selectCount')
            ->with($where)
            ->willReturn($expectedResult);

        $result = $this->controller->selectCount($where);
        $this->assertEquals($expectedResult, $result);
    }

    public function testSelectAllPagination()
    {
        $orderBy = 'id ASC';
        $where = 'status = 1';
        $page = 1;
        $rowsPerPage = 10;
        $expectedResult = [['id' => 1], ['id' => 2]];
        
        $this->daoMock->expects($this->once())
            ->method('selectAllPagination')
            ->with($orderBy, $where, $page, $rowsPerPage)
            ->willReturn($expectedResult);

        $result = $this->controller->selectAllPagination($orderBy, $where, $page, $rowsPerPage);
        $this->assertEquals($expectedResult, $result);
    }

    public function testSelectAll()
    {
        $orderBy = 'name ASC';
        $where = null;
        $expectedResult = [['id' => 1], ['id' => 2]];
        
        $this->daoMock->expects($this->once())
            ->method('selectAll')
            ->with($orderBy, $where)
            ->willReturn($expectedResult);

        $result = $this->controller->selectAll($orderBy, $where);
        $this->assertEquals($expectedResult, $result);
    }

    public function testSelectByTCriteria()
    {
        $criteria = $this->createMock(TCriteria::class);
        $expectedResult = [['id' => 1]];
        
        $this->daoMock->expects($this->once())
            ->method('selectByTCriteria')
            ->with($criteria)
            ->willReturn($expectedResult);

        $result = $this->controller->selectByTCriteria($criteria);
        $this->assertEquals($expectedResult, $result);
    }

    public function testSelectByTCriteriaCount()
    {
        $criteria = $this->createMock(TCriteria::class);
        $expectedResult = 5;
        
        $this->daoMock->expects($this->once())
            ->method('selectByTCriteriaCount')
            ->with($criteria)
            ->willReturn($expectedResult);

        $result = $this->controller->selectByTCriteriaCount($criteria);
        $this->assertEquals($expectedResult, $result);
    }

    public function testGetArrayByCriteria()
    {
        $criteria = $this->createMock(TCriteria::class);
        $showDumpLogTela = true;
        $expectedResult = [['id' => 1]];
        
        $this->daoMock->expects($this->once())
            ->method('getArrayByCriteria')
            ->with($criteria, $showDumpLogTela)
            ->willReturn($expectedResult);

        $result = $this->controller->getArrayByCriteria($criteria, $showDumpLogTela);
        $this->assertEquals($expectedResult, $result);
    }

    public function testGetListObjByCriteria()
    {
        $criteria = $this->createMock(TCriteria::class);
        $showDumpLogTela = false;
        $expectedResult = [new stdClass()];
        
        $this->daoMock->expects($this->once())
            ->method('getListObjByCriteria')
            ->with($criteria, $showDumpLogTela)
            ->willReturn($expectedResult);

        $result = $this->controller->getListObjByCriteria($criteria, $showDumpLogTela);
        $this->assertEquals($expectedResult, $result);
    }
}
