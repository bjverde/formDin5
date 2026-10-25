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

        $runner->setMigrationsPath(null);
        $this->assertStringContainsString('database/migrations', str_replace('\\', '/', $runner->getMigrationsPath()));
    }

    public function test_get_create_table_sql_for_sqlite(): void
    {
        $runner = new FormDinMigrationRunner();
        $sql = $runner->getCreateTableSql('sqlite');

        $this->assertStringContainsString('INTEGER PRIMARY KEY AUTOINCREMENT', $sql);
        $this->assertStringContainsString('CHECK (status IN (\'success\',\'error\'))', $sql);
        $this->assertStringNotContainsString('ENGINE=InnoDB', $sql);
        $this->assertStringNotContainsString('COMMENT', $sql);
    }

    public function test_get_create_table_sql_for_postgres(): void
    {
        $runner = new FormDinMigrationRunner();
        $sql = $runner->getCreateTableSql('pgsql');

        $this->assertStringContainsString('id             SERIAL PRIMARY KEY', $sql);
        $this->assertStringContainsString('executed_at    TIMESTAMP    NOT NULL', $sql);
        $this->assertStringContainsString('CHECK (status IN (\'success\',\'error\'))', $sql);
        $this->assertStringNotContainsString('ENGINE=InnoDB', $sql);
    }

    public function test_get_create_table_sql_for_sqlserver(): void
    {
        $runner = new FormDinMigrationRunner();
        $sql = $runner->getCreateTableSql('sqlsrv');

        $this->assertStringContainsString("IF OBJECT_ID('db_migrations', 'U') IS NULL", $sql);
        $this->assertStringContainsString('IDENTITY(1,1) PRIMARY KEY', $sql);
        $this->assertStringContainsString('DATETIME2', $sql);
        $this->assertStringNotContainsString('ENGINE=InnoDB', $sql);
    }

    public function test_get_create_table_sql_for_mysql(): void
    {
        $runner = new FormDinMigrationRunner();
        $sql = $runner->getCreateTableSql('mysql');

        $this->assertStringContainsString('INT AUTO_INCREMENT PRIMARY KEY', $sql);
        $this->assertStringContainsString('DATETIME', $sql);
        $this->assertStringNotContainsString('ENGINE=InnoDB', $sql);
    }

    public function test_sqlite_ddl_execution_in_memory(): void
    {
        $runner = new FormDinMigrationRunner();
        $sql = $runner->getCreateTableSql('sqlite');

        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Executa DDL no SQLite
        $pdo->exec($sql);

        // Insere registro de teste
        $stmt = $pdo->prepare("INSERT INTO db_migrations (script_id, description, db_name, system_version, executed_at, status)
                               VALUES (:script_id, :description, :db_name, :system_version, :executed_at, :status)");
        $stmt->execute([
            ':script_id'      => 'V001',
            ':description'    => 'Test Migration',
            ':db_name'        => 'test_db',
            ':system_version' => '1.0.0',
            ':executed_at'    => '2026-10-25 00:00:00',
            ':status'         => 'success',
        ]);

        $row = $pdo->query("SELECT * FROM db_migrations WHERE script_id = 'V001'")->fetch(PDO::FETCH_ASSOC);
        $this->assertNotEmpty($row);
        $this->assertEquals('V001', $row['script_id']);
        $this->assertEquals('success', $row['status']);
    }

    public function test_runner_on_sqlite_temporary_db(): void
    {
        $tempDb = sys_get_temp_dir() . '/test_migration_' . uniqid() . '.db';
        $pdo = new PDO('sqlite:' . $tempDb);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $runner = new FormDinMigrationRunner();
        $sql = $runner->getCreateTableSql('sqlite');
        $pdo->exec($sql);

        // Testa inserção
        $stmt = $pdo->prepare("INSERT INTO db_migrations (script_id, description, db_name, system_version, executed_at, status)
                               VALUES (:script_id, :description, :db_name, :system_version, :executed_at, :status)");
        $stmt->execute([
            ':script_id'      => 'V999_TEST',
            ':description'    => 'Descricao de teste',
            ':db_name'        => 'temp_db',
            ':system_version' => '1.0.0',
            ':executed_at'    => date('Y-m-d H:i:s'),
            ':status'         => 'success',
        ]);

        $row = $pdo->query("SELECT * FROM db_migrations WHERE script_id = 'V999_TEST'")->fetch(PDO::FETCH_ASSOC);
        $this->assertNotEmpty($row);
        $this->assertEquals('V999_TEST', $row['script_id']);
        $this->assertEquals('success', $row['status']);

        // Testa update
        $stmt = $pdo->prepare("UPDATE db_migrations
                               SET description = :description, system_version = :system_version
                               WHERE script_id = :script_id");
        $stmt->execute([
            ':script_id'      => 'V999_TEST',
            ':description'    => 'Descricao atualizada',
            ':system_version' => '1.0.1',
        ]);

        $row = $pdo->query("SELECT * FROM db_migrations WHERE script_id = 'V999_TEST'")->fetch(PDO::FETCH_ASSOC);
        $this->assertEquals('Descricao atualizada', $row['description']);
        $this->assertEquals('1.0.1', $row['system_version']);

        unset($pdo);
        if (file_exists($tempDb)) {
            @unlink($tempDb);
        }
    }
}
