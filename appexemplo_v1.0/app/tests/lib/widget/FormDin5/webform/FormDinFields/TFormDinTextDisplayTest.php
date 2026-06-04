<?php
/*
 * ----------------------------------------------------------------------------
 * Formdin 5 Framework
 * SourceCode https://github.com/bjverde/formDin5
 * ----------------------------------------------------------------------------
 */

use PHPUnit\Framework\TestCase;

class TFormDinTextDisplayTest extends TestCase
{
    public function test_constructorAndDefaults()
    {
        $display = new TFormDinTextDisplay('text_id', 'Label Teste', 'Valor Inicial', '#ff0000', '14px', 'bold');
        $this->assertInstanceOf(TFormDinTextDisplay::class, $display);
        $this->assertInstanceOf(TTextDisplay::class, $display->getAdiantiObj());
    }

    private function getTextDisplayValue($obj)
    {
        $reflection = new ReflectionClass(get_class($obj));
        $property = $reflection->getProperty('value');
        $property->setAccessible(true);
        return $property->getValue($obj);
    }

    public function test_cnpjCpf()
    {
        // Test CPF formatting
        $displayCpf = TFormDinTextDisplay::cnpjCpf('cpf_id', 'CPF Label', '12345678909');
        $this->assertInstanceOf(TFormDinTextDisplay::class, $displayCpf);
        $this->assertEquals('123.456.789-09', $this->getTextDisplayValue($displayCpf->getAdiantiObj()));

        // Test CNPJ formatting
        $displayCnpj = TFormDinTextDisplay::cnpjCpf('cnpj_id', 'CNPJ Label', '12345678000199');
        $this->assertEquals('12.345.678/0001-99', $this->getTextDisplayValue($displayCnpj->getAdiantiObj()));
    }

    public function test_phoneNumber()
    {
        $displayPhone = TFormDinTextDisplay::phoneNumber('phone_id', 'Phone Label', '61988887777');
        $this->assertInstanceOf(TFormDinTextDisplay::class, $displayPhone);
        $this->assertEquals('(61) 98888-7777', $this->getTextDisplayValue($displayPhone->getAdiantiObj()));
    }

    public function test_dataTimeBr()
    {
        $displayDate = TFormDinTextDisplay::dataTimeBr('date_id', 'Date Label', '2026-05-26');
        $this->assertInstanceOf(TFormDinTextDisplay::class, $displayDate);
        $this->assertEquals('26/05/2026', $this->getTextDisplayValue($displayDate->getAdiantiObj()));

        $displayDateTime = TFormDinTextDisplay::dataTimeBr('datetime_id', 'DateTime Label', '2026-05-26 14:30:15', true, true);
        $this->assertEquals('26/05/2026 14:30:15', $this->getTextDisplayValue($displayDateTime->getAdiantiObj()));
    }

    public function test_numeroBrasil()
    {
        $displayNum = TFormDinTextDisplay::numeroBrasil('num_id', 'Number Label', '1234567.89');
        $this->assertInstanceOf(TFormDinTextDisplay::class, $displayNum);
        $this->assertEquals('1.234.567,89', $this->getTextDisplayValue($displayNum->getAdiantiObj()));
    }
}
