<?php
use PHPUnit\Framework\TestCase;

class uploadTest extends TestCase
{
    public function testUploadClassExists()
    {
        ob_start();
        $exists = class_exists('upload');
        ob_end_clean();
        
        $this->assertTrue($exists);
    }
}
