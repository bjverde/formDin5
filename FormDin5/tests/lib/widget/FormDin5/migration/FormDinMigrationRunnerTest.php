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

    protected function tearDown(): void
    {
        $dbFile = dirname(__DIR__, 6) . '/app/database/bdApoio.s3db';
        if (file_exists($dbFile)) {
            @exec('git checkout -- ' . escapeshellarg($dbFile));
        }
        parent::tearDown();
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

    public function test_close_and_rollback_open_transactions(): void
    {
        $runner = new FormDinMigrationRunner();

        // 1. Abre transação e testa fechamento automático
        TTransaction::open('dbapoio');
        $this->assertInstanceOf(PDO::class, TTransaction::get());

        $runner->closeOpenTransactions();
        $this->assertNull(TTransaction::get());

        // 2. Abre transação novamente e testa rollback automático
        TTransaction::open('dbapoio');
        $this->assertInstanceOf(PDO::class, TTransaction::get());

        $runner->rollbackOpenTransactions();
        $this->assertNull(TTransaction::get());
    }

    public function test_delete_migration_record(): void
    {
        $runner = new FormDinMigrationRunner(null, 'dbapoio');
        $runner->recordMigration('V998_DEL', 'Descricao', 'dbapoio', '1.0.0', 'success');

        TTransaction::open('dbapoio');
        $conn = TTransaction::get();
        $row = $conn->query("SELECT * FROM db_migrations WHERE script_id = 'V998_DEL'")->fetch(PDO::FETCH_ASSOC);
        TTransaction::close();
        $this->assertNotEmpty($row);

        // Deleta registro
        $runner->deleteMigrationRecord('V998_DEL');

        TTransaction::open('dbapoio');
        $conn = TTransaction::get();
        $row = $conn->query("SELECT * FROM db_migrations WHERE script_id = 'V998_DEL'")->fetch(PDO::FETCH_ASSOC);
        $conn->exec("DROP TABLE IF EXISTS db_migrations");
        TTransaction::close();

        $this->assertFalse($row);
    }

    public function test_rollback_steps_and_rollback_to(): void
    {
        $tempDir = sys_get_temp_dir() . '/migrations_test_' . uniqid();
        mkdir($tempDir, 0777, true);

        // Cria V001
        file_put_contents($tempDir . '/V001First.php', '<?php
            class V001First extends FormDinMigration {
                public function getVersion(): string { return "V001"; }
                public function getDescription(): string { return "First"; }
                public function getDatabase(): string { return "dbapoio"; }
                public function getSystemVersion(): string { return "1.0.0"; }
                public function up(): void { $conn = TTransaction::get(); $conn->exec("CREATE TABLE IF NOT EXISTS tbl_m1 (id INT)"); }
                public function down(): void { $conn = TTransaction::get(); $conn->exec("DROP TABLE IF EXISTS tbl_m1"); }
            }
        ');

        // Cria V002
        file_put_contents($tempDir . '/V002Second.php', '<?php
            class V002Second extends FormDinMigration {
                public function getVersion(): string { return "V002"; }
                public function getDescription(): string { return "Second"; }
                public function getDatabase(): string { return "dbapoio"; }
                public function getSystemVersion(): string { return "1.0.0"; }
                public function up(): void { $conn = TTransaction::get(); $conn->exec("CREATE TABLE IF NOT EXISTS tbl_m2 (id INT)"); }
                public function down(): void { $conn = TTransaction::get(); $conn->exec("DROP TABLE IF EXISTS tbl_m2"); }
            }
        ');

        $runner = new FormDinMigrationRunner($tempDir, 'dbapoio');

        // Executa ambas
        $runner->run();

        TTransaction::open('dbapoio');
        $conn = TTransaction::get();
        $hasTbl1 = (bool) $conn->query("SELECT name FROM sqlite_master WHERE type='table' AND name='tbl_m1'")->fetch();
        $hasTbl2 = (bool) $conn->query("SELECT name FROM sqlite_master WHERE type='table' AND name='tbl_m2'")->fetch();
        TTransaction::close();

        $this->assertTrue($hasTbl1);
        $this->assertTrue($hasTbl2);

        // Rollback de 1 passo (deve reverter V002)
        $runner->rollback(1);

        TTransaction::open('dbapoio');
        $conn = TTransaction::get();
        $hasTbl1 = (bool) $conn->query("SELECT name FROM sqlite_master WHERE type='table' AND name='tbl_m1'")->fetch();
        $hasTbl2 = (bool) $conn->query("SELECT name FROM sqlite_master WHERE type='table' AND name='tbl_m2'")->fetch();
        $rowV2 = $conn->query("SELECT * FROM db_migrations WHERE script_id = 'V002'")->fetch();
        TTransaction::close();

        $this->assertTrue($hasTbl1);
        $this->assertFalse($hasTbl2);
        $this->assertFalse($rowV2);

        // Reset (reverte V001 restante)
        $runner->reset();

        TTransaction::open('dbapoio');
        $conn = TTransaction::get();
        $hasTbl1 = (bool) $conn->query("SELECT name FROM sqlite_master WHERE type='table' AND name='tbl_m1'")->fetch();
        $conn->exec("DROP TABLE IF EXISTS db_migrations");
        TTransaction::close();

        $this->assertFalse($hasTbl1);

        // Limpeza dos arquivos temporários
        @unlink($tempDir . '/V001First.php');
        @unlink($tempDir . '/V002Second.php');
        @rmdir($tempDir);
    }

    public function test_default_legacy_dbms_configuration(): void
    {
        $runner = new FormDinMigrationRunner();
        $this->assertNull($runner->getDefaultLegacyDbms());

        $runner->setDefaultLegacyDbms(FormDinMigration::DBMS_MYSQL);
        $this->assertEquals('mysql', $runner->getDefaultLegacyDbms());

        // Migration padrão que não sobrescreve getSupportedDbms()
        $migration = new class extends FormDinMigration {
            public function getVersion(): string { return 'V001'; }
            public function getDescription(): string { return 'Desc'; }
            public function getDatabase(): string { return 'dbapoio'; }
            public function getSystemVersion(): string { return '1.0.0'; }
            public function up(): void {}
            public function down(): void {}
        };

        // Quando defaultLegacyDbms está ativo, deve retornar ['mysql']
        $supported = $runner->getMigrationSupportedDbms($migration);
        $this->assertEquals(['mysql'], $supported);

        // Se a migration sobrescrever explicitamente, prevalece a sobrescrita
        $customMigration = new class extends FormDinMigration {
            public function getVersion(): string { return 'V002'; }
            public function getDescription(): string { return 'Desc'; }
            public function getDatabase(): string { return 'dbapoio'; }
            public function getSystemVersion(): string { return '1.0.0'; }
            public function getSupportedDbms(): array|string { return [self::DBMS_SQLITE, self::DBMS_POSTGRES]; }
            public function up(): void {}
            public function down(): void {}
        };

        $this->assertEquals(['sqlite', 'pgsql'], $runner->getMigrationSupportedDbms($customMigration));
    }

    public function test_runner_skips_incompatible_migration(): void
    {
        $tempDir = sys_get_temp_dir() . '/test_mig_incompat_' . uniqid();
        mkdir($tempDir, 0777, true);

        // Cria uma migration exclusiva para MySQL, tentando rodar no banco SQLite 'dbapoio'
        $code = '<?php
class V001MysqlOnly extends FormDinMigration {
    public function getVersion(): string { return "V001"; }
    public function getDescription(): string { return "MySQL Only Feature"; }
    public function getDatabase(): string { return "dbapoio"; }
    public function getSystemVersion(): string { return "1.0.0"; }
    public function getSupportedDbms(): array|string { return self::DBMS_MYSQL; }
    public function up(): void {
        throw new \RuntimeException("NÃO DEVERIA TER EXECUTADO NO SQLITE!");
    }
    public function down(): void {}
}';
        file_put_contents($tempDir . '/V001MysqlOnly.php', $code);

        $runner = new FormDinMigrationRunner($tempDir, 'dbapoio');

        ob_start();
        $runner->run();
        $output = ob_get_clean();

        // Deve ter ignorado sem lançar exceção
        $this->assertStringContainsString('IGNORADA: migration desenvolvida para [mysql]', $output);
        $this->assertStringContainsString('incompativel com o banco \'dbapoio\' (sqlite)', $output);

        // Status deve indicar incompatível
        ob_start();
        $runner->status();
        $statusOutput = ob_get_clean();
        $this->assertStringContainsString('incompativel', $statusOutput);

        // Limpeza
        @unlink($tempDir . '/V001MysqlOnly.php');
        @rmdir($tempDir);
    }
}
