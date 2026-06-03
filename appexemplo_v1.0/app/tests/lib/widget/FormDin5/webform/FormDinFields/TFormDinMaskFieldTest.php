<?php
/*
 * ----------------------------------------------------------------------------
 * Formdin 5 Framework
 * SourceCode https://github.com/bjverde/formDin5
 * ----------------------------------------------------------------------------
 */

use PHPUnit\Framework\TestCase;

class TFormDinMaskFieldTest extends TestCase
{
    private $classTest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->classTest = new TFormDinMaskField('mask_test', 'Mask Field', false, '999.999.999-99');
    }

    protected function tearDown(): void
    {
        $this->classTest = null;
        parent::tearDown();
    }

    public function test_instanceOff()
    {
        $adiantiObj = $this->classTest->getAdiantiObj();
        $this->assertInstanceOf(TEntry::class, $adiantiObj);
    }

    public function test_getMask()
    {
        $this->assertEquals('999.999.999-99', $this->classTest->getMask());
    }

    public function test_setMaskEmptyException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->classTest->setMask(null);
    }

    public function test_getBoolSendMask()
    {
        $this->assertFalse($this->classTest->getBoolSendMask());
    }
}
