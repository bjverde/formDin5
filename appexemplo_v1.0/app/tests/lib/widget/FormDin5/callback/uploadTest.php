<?php
use PHPUnit\Framework\TestCase;

class uploadTest extends TestCase
{
    public function testUploadClassExists()
    {
        $this->assertTrue(class_exists('upload'));
    }
}
