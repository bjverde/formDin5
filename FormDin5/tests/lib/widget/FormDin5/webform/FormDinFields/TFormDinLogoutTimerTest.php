<?php
/*
 * ----------------------------------------------------------------------------
 * Formdin 5 Framework
 * SourceCode https://github.com/bjverde/formDin5
 * ----------------------------------------------------------------------------
 */

use PHPUnit\Framework\TestCase;

class TFormDinLogoutTimerTest extends TestCase
{
    private $classTest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->classTest = new TFormDinLogoutTimer('timer_test', 'Logout Timer', 120, true);
    }

    protected function tearDown(): void
    {
        $this->classTest = null;
        parent::tearDown();
    }

    public function test_instanceOf()
    {
        $this->assertInstanceOf(TFormDinLogoutTimer::class, $this->classTest);
        $this->assertInstanceOf(TFormDinGenericField::class, $this->classTest);
    }

    public function test_getDefaultsAndConstructor()
    {
        $this->assertEquals('timer_test', $this->classTest->getIdDivLogoutTimer());
        $this->assertEquals(120, $this->classTest->getTimeoutSeconds());
        $this->assertEquals(120000, $this->classTest->getTimeoutMs());
        $this->assertTrue($this->classTest->get('debug'));
    }

    public function test_magicMethodsCall()
    {
        // Test camelToSnake conversion and magic methods __call
        $this->classTest->setNormalFonteCor('#112233');
        $this->assertEquals('#112233', $this->classTest->getNormalFonteCor());

        $this->classTest->setTimeoutSeconds(90);
        $this->assertEquals(90000, $this->classTest->getTimeoutMs());

        $this->expectException(Exception::class);
        $this->classTest->nonExistentMethod();
    }

    public function test_directSetGet()
    {
        $this->classTest->set('logout_url', 'custom_logout.php');
        $this->assertEquals('custom_logout.php', $this->classTest->get('logout_url'));

        $this->assertNull($this->classTest->get('non_existent_property'));
        $this->assertFalse($this->classTest->set('non_existent_property', 'value'));
    }

    public function test_convenienceMethods()
    {
        $this->classTest->setNormalColor('#00ff00');
        $this->assertEquals('#00ff00', $this->classTest->getNormalColor());

        $this->classTest->setWarningColor('#ffff00');
        $this->assertEquals('#ffff00', $this->classTest->getWarningColor());

        $this->classTest->setCriticalColor('#ff0000');
        $this->assertEquals('#ff0000', $this->classTest->getCriticalColor());

        $this->classTest->setWarningLimit(0.40);
        $this->assertEquals(0.40, $this->classTest->getWarningLimit());

        $this->classTest->setCriticalLimit(0.10);
        $this->assertEquals(0.10, $this->classTest->getCriticalLimit());

        $this->classTest->setAudioEnabled(false);
        $this->assertFalse($this->classTest->isAudioEnabled());

        $this->classTest->setAudioVolume(0.5);
        $this->assertEquals(0.5, $this->classTest->getAudioVolume());

        $this->classTest->setIconClass('fa fa-user');
        $this->assertEquals('fa fa-user', $this->classTest->getIconClass());

        $this->classTest->setIconColor('#ffffff');
        $this->assertEquals('#ffffff', $this->classTest->getIconColor());

        $this->classTest->setIconSize('2em');
        $this->assertEquals('2em', $this->classTest->getIconSize());

        $this->classTest->setIconMargin('5px');
        $this->assertEquals('5px', $this->classTest->getIconMargin());
    }

    public function test_eventsManipulation()
    {
        $this->classTest->setEvents(['click', 'keydown']);
        $this->assertEquals(['click', 'keydown'], $this->classTest->getEvents());

        $this->classTest->addEvent('mousedown');
        $this->assertTrue($this->classTest->hasEvent('mousedown'));

        $this->classTest->removeEvent('keydown');
        $this->assertFalse($this->classTest->hasEvent('keydown'));

        $this->expectException(InvalidArgumentException::class);
        $this->classTest->setEvents([]);
    }

    public function test_invalidEventException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->classTest->addEvent('invalid_js_event');
    }

    public function test_getConfigAndReset()
    {
        $configBefore = $this->classTest->getConfig();
        $this->assertEquals(120, $configBefore['timeout_seconds']);

        $this->classTest->resetToDefaults();
        $configAfter = $this->classTest->getConfig();
        $this->assertEquals(60, $configAfter['timeout_seconds']);
        $this->assertEquals('#28a745', $configAfter['normal_fonte_cor']);
    }

    public function test_toJavaScript()
    {
        $js = $this->classTest->toJavaScript();
        $decoded = json_decode($js, true);
        $this->assertIsArray($decoded);
        $this->assertEquals('timer_test', $this->classTest->getIdDivLogoutTimer());
    }

    public function test_getVisualConfig()
    {
        $visual = $this->classTest->getVisualConfig();
        $this->assertArrayHasKey('normal', $visual);
        $this->assertArrayHasKey('aviso', $visual);
        $this->assertArrayHasKey('critico', $visual);
    }

    public function testMagicMethodsExceptions()
    {
        try {
            $this->classTest->getNonExistentProperty();
            $this->fail('Expected Exception was not thrown');
        } catch (\Exception $e) {
            $this->assertStringContainsString("Propriedade 'non_existent_property' não existe", $e->getMessage());
        }

        try {
            $this->classTest->setNonExistentProperty('value');
            $this->fail('Expected Exception was not thrown');
        } catch (\Exception $e) {
            $this->assertStringContainsString("Propriedade 'non_existent_property' não existe", $e->getMessage());
        }

        try {
            $this->classTest->nonExistentMethod();
            $this->fail('Expected Exception was not thrown');
        } catch (\Exception $e) {
            $this->assertStringContainsString("Método 'nonExistentMethod' não existe", $e->getMessage());
        }
    }

    public function testMagicTimeoutSeconds()
    {
        $this->classTest->__call('setTimeoutSeconds', [90]);
        $this->assertEquals(90000, $this->classTest->getTimeoutMs());
    }

    public function testSetConfigTimeoutSeconds()
    {
        $this->classTest->set('timeout_seconds', 70);
        $this->assertEquals(70000, $this->classTest->get('timeout_ms'));
    }

    public function testIconStylesReflection()
    {
        $refObj = new ReflectionObject($this->classTest);
        
        $propColor = $refObj->getProperty('icon_color');
        $propColor->setAccessible(true);
        $propColor->setValue($this->classTest, '#ffffff');

        $propSize = $refObj->getProperty('icon_size');
        $propSize->setAccessible(true);
        $propSize->setValue($this->classTest, '2em');

        $propMargin = $refObj->getProperty('icon_margin');
        $propMargin->setAccessible(true);
        $propMargin->setValue($this->classTest, '5px');

        $methodGetIconStyle = $refObj->getMethod('getIconStyle');
        $methodGetIconStyle->setAccessible(true);
        $style = $methodGetIconStyle->invoke($this->classTest);
        
        $this->assertStringContainsString('color: #ffffff', $style);
        $this->assertStringContainsString('font-size: 2em', $style);
        $this->assertStringContainsString('margin-right: 5px', $style);
    }

    public function testInvalidEventsException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->classTest->setEvents(['click', 'invalid_event_name']);
    }

    public function testRemoveNonExistentEvent()
    {
        $result = $this->classTest->removeEvent('non_existent_event');
        $this->assertFalse($result);
    }
}
