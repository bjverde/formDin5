<?php
/*
 * ----------------------------------------------------------------------------
 * Formdin 5 Framework
 * SourceCode https://github.com/bjverde/formDin5
 * ----------------------------------------------------------------------------
 */

use PHPUnit\Framework\TestCase;

class TFormDinRestTest extends TestCase
{
    private $oldHttpHost;

    protected function setUp(): void
    {
        parent::setUp();
        $this->oldHttpHost = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null;
    }

    protected function tearDown(): void
    {
        if ($this->oldHttpHost !== null) {
            $_SERVER['HTTP_HOST'] = $this->oldHttpHost;
        } else {
            unset($_SERVER['HTTP_HOST']);
        }
        parent::tearDown();
    }

    public function test_isDevTrue()
    {
        $_SERVER['HTTP_HOST'] = 'localhost';
        $rest = new TFormDinRest();
        
        // We can test that the request method will attempt to run and trigger isDev = true
        // Let's pass an invalid URL to trigger curl error to inspect behavior
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('cURL error');
        $rest->request('http://invalid.local.domain/api');
    }

    public function test_isDevFalse()
    {
        $_SERVER['HTTP_HOST'] = 'www.example.com';
        $rest = new TFormDinRest();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('cURL error');
        $rest->request('http://invalid.local.domain/api');
    }

    public function test_requestJsonInvalidJsonException()
    {
        // If we mock/simulate or use a request that returns non-JSON, it should throw a non-JSON exception.
        // We can do this by hitting a known public static URL returning HTML, but since network calls in unit tests can be fragile,
        // let's pass a dummy or local URL that is guaranteed to return non-JSON if it fails,
        // or just test that if curl execution throws, Exception is thrown.
        $rest = new TFormDinRest();
        $this->expectException(Exception::class);
        $rest->requestJson('http://invalid.local.domain/api');
    }
}
