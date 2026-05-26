<?php
/*
 * ----------------------------------------------------------------------------
 * Formdin 5 Framework
 * SourceCode https://github.com/bjverde/formDin5
 * ----------------------------------------------------------------------------
 */

use PHPUnit\Framework\TestCase;

class TFormDinMessageTest extends TestCase
{
    private $oldErrorLog;

    protected function setUp(): void
    {
        parent::setUp();
        $this->oldErrorLog = ini_get('error_log');
        // Redirect error log to NUL (Windows equivalent of /dev/null) to prevent console pollution
        ini_set('error_log', 'NUL');
    }

    protected function tearDown(): void
    {
        ini_set('error_log', $this->oldErrorLog);
        parent::tearDown();
    }

    public function test_constructorAndGetters()
    {
        $messageObj = new TFormDinMessage('Minha Mensagem', TFormDinMessage::TYPE_WARING, null, 'Aviso');
        $this->assertInstanceOf(TFormDinMessage::class, $messageObj);
        $this->assertEquals('Minha Mensagem', $messageObj->getMixMessage());
    }

    public function test_messageTransform()
    {
        // String
        $transformedString = TFormDinMessage::messageTransform('Linha única');
        $this->assertEquals('Linha única', $transformedString);

        // Array
        $arrayInput = ['Linha 1', "Linha 2\ncom quebra"];
        $transformedArray = TFormDinMessage::messageTransform($arrayInput);
        $this->assertEquals('Linha 1<br>Linha 2<br>com quebra', $transformedArray);
    }

    public function test_logRecord()
    {
        // Simply assert that executing logRecord does not throw exceptions
        $exception = new Exception('Erro de Teste', 999);
        TFormDinMessage::logRecord($exception);
        $this->assertTrue(true);
    }

    public function test_logRecordSimple()
    {
        TFormDinMessage::logRecordSimple('Mensagem de log simples de teste');
        $this->assertTrue(true);
    }
}
