<?php
use PHPUnit\Framework\TestCase;

class UploadExceptionTest extends TestCase
{
    public function testExceptionMessages()
    {
        $this->assertEquals("The uploaded file exceeds the upload_max_filesize directive in php.ini", UploadException::codeToMessage(UPLOAD_ERR_INI_SIZE));
        $this->assertEquals("The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form", UploadException::codeToMessage(UPLOAD_ERR_FORM_SIZE));
        $this->assertEquals("The uploaded file was only partially uploaded", UploadException::codeToMessage(UPLOAD_ERR_PARTIAL));
        $this->assertEquals("No file was uploaded", UploadException::codeToMessage(UPLOAD_ERR_NO_FILE));
        $this->assertEquals("Missing a temporary folder", UploadException::codeToMessage(UPLOAD_ERR_NO_TMP_DIR));
        $this->assertEquals("Failed to write file to disk", UploadException::codeToMessage(UPLOAD_ERR_CANT_WRITE));
        $this->assertEquals("File upload stopped by extension", UploadException::codeToMessage(UPLOAD_ERR_EXTENSION));
        $this->assertEquals("Unknown upload error", UploadException::codeToMessage(999));
    }

    public function testExceptionInstance()
    {
        $exception = new UploadException(UPLOAD_ERR_NO_FILE);
        $this->assertEquals("No file was uploaded", $exception->getMessage());
        $this->assertEquals(UPLOAD_ERR_NO_FILE, $exception->getCode());
    }
}
