<?php
/*
 * ----------------------------------------------------------------------------
 * Formdin 5 Framework
 * SourceCode https://github.com/bjverde/formDin5
 * @author Reinaldo A. Barrêto Junior
 * ----------------------------------------------------------------------------
 */

use PHPUnit\Framework\TestCase;

class CountHelperTest extends TestCase
{
    public function testCount_withArray()
    {
        $arr = [1, 2, 3];
        $this->assertEquals(3, CountHelper::count($arr));
    }

    public function testCount_withEmptyArray()
    {
        $arr = [];
        $this->assertEquals(0, CountHelper::count($arr));
    }

    public function testCount_withCountableObject()
    {
        $obj = new ArrayObject([1, 2, 3, 4]);
        $this->assertEquals(4, CountHelper::count($obj));
    }

    public function testCount_withString()
    {
        $val = "hello";
        $this->assertEquals(0, CountHelper::count($val));
    }

    public function testCount_withNull()
    {
        $val = null;
        $this->assertEquals(0, CountHelper::count($val));
    }
}
