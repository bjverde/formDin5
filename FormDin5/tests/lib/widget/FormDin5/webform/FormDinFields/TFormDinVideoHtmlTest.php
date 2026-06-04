<?php
/*
 * ----------------------------------------------------------------------------
 * Formdin 5 Framework
 * SourceCode https://github.com/bjverde/formDin5
 * ----------------------------------------------------------------------------
 */

use PHPUnit\Framework\TestCase;

class TFormDinVideoHtmlTest extends TestCase
{
    private $classTest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->classTest = new TFormDinVideoHtml('video_id', 'Video HTML', 'app/resources/sample.mp4', true, false, false);
    }

    protected function tearDown(): void
    {
        $this->classTest = null;
        parent::tearDown();
    }

    public function test_instanceOf()
    {
        $this->assertInstanceOf(TFormDinVideoHtml::class, $this->classTest);
        $this->assertInstanceOf(TFormDinGenericField::class, $this->classTest);
        $this->assertInstanceOf(TElement::class, $this->classTest->getAdiantiObj());
    }

    public function test_defaultProperties()
    {
        $adiantiObj = $this->classTest->getAdiantiObj();
        $this->assertEquals('fd5Video', $adiantiObj->class);

        // Child source element verification
        $children = $adiantiObj->getChildren();
        $this->assertNotEmpty($children);
        $source = $children[0];
        $this->assertInstanceOf(TElement::class, $source);
        $this->assertEquals('app/resources/sample.mp4', $source->src);
        $this->assertEquals('video/mp4', $source->type);
    }

    public function test_setProperties()
    {
        $this->classTest->loop(true);
        $this->assertEquals('true', $this->classTest->getAdiantiObj()->getProperty('loop'));

        $this->classTest->controls(true);
        $this->assertEquals('true', $this->classTest->getAdiantiObj()->getProperty('controls'));

        $this->classTest->muted(true);
        $this->assertEquals('true', $this->classTest->getAdiantiObj()->getProperty('muted'));

        $this->classTest->autoplay(true);
        $this->assertEquals('true', $this->classTest->getAdiantiObj()->getProperty('autoplay'));
        $this->assertEquals('true', $this->classTest->getAdiantiObj()->getProperty('muted'));
    }
}
