<?php
/*
 * ----------------------------------------------------------------------------
 * Formdin 5 Framework
 * SourceCode https://github.com/bjverde/formDin5
 * ----------------------------------------------------------------------------
 */

use PHPUnit\Framework\TestCase;

class TFormDinVideoStreamPhotoTest extends TestCase
{
    private $oldServerHttps;
    private $oldServerProto;

    protected function setUp(): void
    {
        parent::setUp();
        // Backup SERVER variables
        $this->oldServerHttps = isset($_SERVER['HTTPS']) ? $_SERVER['HTTPS'] : null;
        $this->oldServerProto = isset($_SERVER['HTTP_X_FORWARDED_PROTO']) ? $_SERVER['HTTP_X_FORWARDED_PROTO'] : null;
    }

    protected function tearDown(): void
    {
        // Restore SERVER variables
        if ($this->oldServerHttps !== null) {
            $_SERVER['HTTPS'] = $this->oldServerHttps;
        } else {
            unset($_SERVER['HTTPS']);
        }

        if ($this->oldServerProto !== null) {
            $_SERVER['HTTP_X_FORWARDED_PROTO'] = $this->oldServerProto;
        } else {
            unset($_SERVER['HTTP_X_FORWARDED_PROTO']);
        }

        parent::tearDown();
    }

    public function test_constructorHttpsActive()
    {
        // Force HTTPS active
        $_SERVER['HTTPS'] = 'on';

        $widget = new TFormDinVideoStreamPhoto(
            'photo_field',
            'Sua Foto',
            true,
            true,
            '100%',
            300,
            'app/images/custom-feedback.png',
            '0.50'
        );

        $this->assertInstanceOf(TFormDinVideoStreamPhoto::class, $widget);
        $this->assertInstanceOf(TFormDinGenericField::class, $widget);
        
        $adiantiObj = $widget->getAdiantiObj();
        $this->assertInstanceOf(TElement::class, $adiantiObj);
        $this->assertEquals('fd5DivVideo', $adiantiObj->class);

        // Check if correct elements were added (video, canvas, button block)
        $html = $adiantiObj->getContents();
        $this->assertStringContainsString('photo_field_video', $html);
        $this->assertStringContainsString('photo_field_videoCanvas', $html);
        $this->assertStringContainsString('photo_field_videoCanvasUpload', $html);
        $this->assertStringContainsString('Ligar Câmera', $html);
        $this->assertStringContainsString('Capturar Foto', $html);
    }

    public function test_constructorHttpsInactive()
    {
        // Force HTTPS inactive
        unset($_SERVER['HTTPS']);
        unset($_SERVER['HTTP_X_FORWARDED_PROTO']);

        $widget = new TFormDinVideoStreamPhoto(
            'photo_field_non_ssl',
            'Sua Foto Sem SSL',
            false
        );

        $this->assertInstanceOf(TFormDinVideoStreamPhoto::class, $widget);
        
        $adiantiObj = $widget->getAdiantiObj();
        $this->assertInstanceOf(TElement::class, $adiantiObj);

        // Should display the HTTPS warning message
        $html = $adiantiObj->getContents();
        $this->assertStringContainsString('Para usar o recurso de captura de foto da câmera', $html);
        $this->assertStringContainsString('é necessário que a página esteja sendo acessada via HTTPS', $html);
    }

    public function test_additionalProperties()
    {
        $_SERVER['HTTPS'] = 'on';

        $widget = new TFormDinVideoStreamPhoto('photo_test', 'Foto Label');
        $widget->loop(true);
        $widget->controls(true);
        $widget->muted(true);
        $widget->autoplay(true);

        $this->assertTrue(true);
    }
}
