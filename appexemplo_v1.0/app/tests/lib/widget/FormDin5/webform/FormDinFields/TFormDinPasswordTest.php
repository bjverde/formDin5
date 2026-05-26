<?php
/*
 * ----------------------------------------------------------------------------
 * Formdin 5 Framework
 * SourceCode https://github.com/bjverde/formDin5
 * ----------------------------------------------------------------------------
 */

use PHPUnit\Framework\TestCase;

class TFormDinPasswordTest extends TestCase
{
    private $classTest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->classTest = new TFormDinPassword('password_test', 'Senha', true, 20, '123456', true);
    }

    protected function tearDown(): void
    {
        $this->classTest = null;
        parent::tearDown();
    }

    public function test_instanceOf()
    {
        $this->assertInstanceOf(TFormDinPassword::class, $this->classTest);
        $this->assertInstanceOf(TFormDinGenericField::class, $this->classTest);
        $this->assertInstanceOf(TPassword::class, $this->classTest->getAdiantiObj());
    }

    public function test_setMaxLength()
    {
        $this->classTest->setMaxLength(15);
        $this->assertEquals(15, $this->classTest->getAdiantiObj()->getProperty('maxlength'));
    }

    public function test_enableToggleVisibility()
    {
        // Simply checking we can call enableToggleVisibility
        $this->classTest->enableToggleVisibility(false);
        $this->classTest->enableToggleVisibility(true);
        $this->assertTrue(true);
    }

    public static function staticActionCallback()
    {
    }

    public function test_setExitAction()
    {
        $action = new TAction(['TFormDinPasswordTest', 'staticActionCallback']);
        $this->classTest->setExitAction($action);
        $this->assertTrue(true);
    }
}
