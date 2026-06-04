<?php
use PHPUnit\Framework\TestCase;

class TFormDinGenericDAOTest extends TestCase
{
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
}
