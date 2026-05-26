<?php
/*
 * ----------------------------------------------------------------------------
 * Formdin 5 Framework
 * SourceCode https://github.com/bjverde/formDin5
 * ----------------------------------------------------------------------------
 */

use PHPUnit\Framework\TestCase;

class TFormDinLogFileTest extends TestCase
{
    private $logPath;
    private $oldErrorLog;

    protected function setUp(): void
    {
        parent::setUp();
        $this->logPath = __DIR__ . '/log_test_file.txt';
        if (file_exists($this->logPath)) {
            unlink($this->logPath);
        }
        $this->oldErrorLog = ini_get('error_log');
        ini_set('error_log', 'NUL');
    }

    protected function tearDown(): void
    {
        if (file_exists($this->logPath)) {
            unlink($this->logPath);
        }
        ini_set('error_log', $this->oldErrorLog);
        parent::tearDown();
    }

    public function test_getSetFilePath()
    {
        $logger = new TFormDinLogFile($this->logPath);
        $this->assertEquals($this->logPath, $logger->getFilePath());

        $this->expectException(InvalidArgumentException::class);
        $logger->setFilePath('');
    }

    public function test_gravarMsgNormal()
    {
        $logger = new TFormDinLogFile($this->logPath);
        $logger->gravarMsgNormal('Mensagem Normal 1');
        $logger->gravarMsg('Mensagem Normal 2');

        $this->assertFileExists($this->logPath);
        $content = file_get_contents($this->logPath);
        $this->assertStringContainsString('Mensagem Normal 1', $content);
        $this->assertStringContainsString('Mensagem Normal 2', $content);
    }

    public function test_gravarMsgInvertido()
    {
        $logger = new TFormDinLogFile($this->logPath);
        $logger->gravarMsgInvertido('Mensagem A');
        $logger->gravarMsgInvertido('Mensagem B');

        $this->assertFileExists($this->logPath);
        $content = file_get_contents($this->logPath);

        // Mensagem B should be before Mensagem A
        $posA = strpos($content, 'Mensagem A');
        $posB = strpos($content, 'Mensagem B');

        $this->assertNotFalse($posA);
        $this->assertNotFalse($posB);
        $this->assertTrue($posB < $posA, 'Mensagem B deve aparecer antes de Mensagem A no arquivo invertido');
    }
}
