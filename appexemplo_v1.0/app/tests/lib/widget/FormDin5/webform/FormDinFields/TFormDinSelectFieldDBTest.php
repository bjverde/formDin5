<?php
/*
 * ----------------------------------------------------------------------------
 * Formdin 5 Framework
 * SourceCode https://github.com/bjverde/formDin5
 * ----------------------------------------------------------------------------
 */

use PHPUnit\Framework\TestCase;

class TFormDinSelectFieldDBTest extends TestCase
{
    private $classTest;

    protected function setUp(): void
    {
        parent::setUp();
        // Set up the widget with dummy parameters
        $this->classTest = new TFormDinSelectFieldDB(
            'select_db_test',
            'Select DB',
            true,
            '1',
            'samples',
            'Category',
            'id',
            'name',
            'name',
            null,
            true,
            true
        );
    }

    protected function tearDown(): void
    {
        $this->classTest = null;
        parent::tearDown();
    }

    public function test_instanceOf()
    {
        $this->assertInstanceOf(TFormDinSelectFieldDB::class, $this->classTest);
        $this->assertInstanceOf(TFormDinGenericField::class, $this->classTest);
        $this->assertInstanceOf(TDBCombo::class, $this->classTest->getAdiantiObj());
    }

    public function test_enableSearch()
    {
        $this->classTest->enableSearch(true);
        $this->classTest->enableSearch(false);
        $this->assertTrue(true);
    }
}
