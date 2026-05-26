<?php
/*
 * ----------------------------------------------------------------------------
 * Formdin 5 Framework
 * SourceCode https://github.com/bjverde/formDin5
 * ----------------------------------------------------------------------------
 */

use PHPUnit\Framework\TestCase;

class TFormDinMenuBuilderTest extends TestCase
{
    private $builder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->builder = new TFormDinMenuBuilder();
    }

    protected function tearDown(): void
    {
        $this->builder = null;
        parent::tearDown();
    }

    public function test_addMenuItemAndGetXML()
    {
        $this->builder->addMenuItem('1', null, 'Acesso-Web', null, 'fa:address-card fa-fw');
        $this->builder->addMenuItem('1.1', '1', 'Cadastrar', null, 'fa:list fa-fw');
        $this->builder->addMenuItem('1.1.1', '1.1', 'Sistema', 'acessoSistema', 'fa:minus fa-fw');
        $this->builder->addMenuItem('1.1.2', '1.1', 'Grupo', 'acessoGrupo', 'fa:minus fa-fw');

        $xml = $this->builder->getXML();
        $this->assertStringContainsString('id="1"', $xml);
        $this->assertStringContainsString('id="1.1"', $xml);
        $this->assertStringContainsString('id="1.1.1"', $xml);
        $this->assertStringContainsString('<action>acessoSistema</action>', $xml);
        $this->assertStringContainsString('<icon>fa:address-card fa-fw</icon>', $xml);
    }

    public function test_addDuplicateMenuException()
    {
        $this->builder->addMenuItem('1', null, 'Menu 1');
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("ID '1' já existe");
        $this->builder->addMenuItem('1', null, 'Menu 1 Duplicate');
    }

    public function test_nonExistentParentException()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("ID pai '99' não encontrado");
        $this->builder->addMenuItem('1.1', '99', 'Menu Orphan');
    }

    public function test_filterMenu()
    {
        $this->builder->addMenuItem('1', null, 'Menu Pai');
        $this->builder->addMenuItem('1.1', '1', 'Filho 1', 'action1');
        $this->builder->addMenuItem('1.2', '1', 'Filho 2', 'action2');

        // Filter XML to only allow 'action1'
        $filteredXml = $this->builder->getXML(['action1']);
        
        $this->assertStringContainsString('action1', $filteredXml);
        $this->assertStringNotContainsString('action2', $filteredXml);
        $this->assertStringNotContainsString('Filho 2', $filteredXml);
    }

    public function test_showMenuHtmlRendering()
    {
        $this->builder->addMenuItem('1', null, 'Menu Pai');
        $this->builder->addMenuItem('1.1', '1', 'Filho 1', 'action1');

        $html = $this->builder->show(['action1']);
        $this->assertIsString($html);
        $this->assertStringContainsString('sidebar-nav', $html);
        $this->assertStringContainsString('side-menu', $html);
        $this->assertStringContainsString('Menu Pai', $html);
        $this->assertStringContainsString('Filho 1', $html);
    }
}
