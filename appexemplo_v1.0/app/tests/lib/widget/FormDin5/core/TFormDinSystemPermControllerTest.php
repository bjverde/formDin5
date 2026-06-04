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
}
