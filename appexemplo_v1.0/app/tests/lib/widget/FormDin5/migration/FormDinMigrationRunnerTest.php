<?php
/*
 * ----------------------------------------------------------------------------
 * Formdin 5 Framework
 * SourceCode https://github.com/bjverde/formDin5
 * ----------------------------------------------------------------------------
 */

use PHPUnit\Framework\TestCase;

class FormDinMigrationRunnerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        require_once dirname(__DIR__, 6) . '/app/lib/widget/FormDin5/migration/FormDinMigrationRunner.class.php';
    }

    public function test_default_values(): void
    {
        $runner = new FormDinMigrationRunner();
        $this->assertEquals('permission', $runner->getTrackingDb());
        $this->assertStringContainsString('database/migrations', str_replace('\\', '/', $runner->getMigrationsPath()));
    }

    public function test_constructor_with_parameters(): void
    {
        $runner = new FormDinMigrationRunner('/custom/migrations', 'custom_database');
        $this->assertEquals('custom_database', $runner->getTrackingDb());
        $this->assertEquals('/custom/migrations', $runner->getMigrationsPath());
    }

    public function test_set_get_tracking_db(): void
    {
        $runner = new FormDinMigrationRunner();
        $runner->setTrackingDb('crm_db');
        $this->assertEquals('crm_db', $runner->getTrackingDb());

        // Test fallback when set to null
        $runner->setTrackingDb(null);
        $this->assertEquals('permission', $runner->getTrackingDb());
    }

    public function test_set_get_migrations_path(): void
    {
        $runner = new FormDinMigrationRunner();
        $runner->setMigrationsPath('/var/www/migrations');
        $this->assertEquals('/var/www/migrations', $runner->getMigrationsPath());

        // Test fallback when set to null
        $runner->setMigrationsPath(null);
        $this->assertStringContainsString('database/migrations', str_replace('\\', '/', $runner->getMigrationsPath()));
    }
}
