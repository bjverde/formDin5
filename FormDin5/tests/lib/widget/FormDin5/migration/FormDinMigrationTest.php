<?php
/*
 * ----------------------------------------------------------------------------
 * Formdin 5 Framework
 * SourceCode https://github.com/bjverde/formDin5
 * ----------------------------------------------------------------------------
 */

use PHPUnit\Framework\TestCase;

class ConcreteMigrationForTest extends FormDinMigration
{
    private string $version;
    private string $database;

    public function __construct(string $version = 'V001', string $database = 'test_db')
    {
        $this->version = $version;
        $this->database = $database;
    }

    public function getVersion(): string { return $this->version; }
    public function getDescription(): string { return 'Test description'; }
    public function getDatabase(): string { return $this->database; }
    public function getSystemVersion(): string { return '1.0.0'; }
    public function up(): void {}
    public function down(): void {}
}

class FormDinMigrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        require_once dirname(__DIR__, 6) . '/app/lib/widget/FormDin5/migration/FormDinMigration.class.php';
    }

    public function test_constants(): void
    {
        $this->assertEquals('mysql', FormDinMigration::DBMS_MYSQL);
        $this->assertEquals('pgsql', FormDinMigration::DBMS_POSTGRES);
        $this->assertEquals('sqlite', FormDinMigration::DBMS_SQLITE);
        $this->assertEquals('sqlsrv', FormDinMigration::DBMS_SQLSERVER);
    }

    public function test_normalize_driver_names(): void
    {
        $migration = new ConcreteMigrationForTest();

        $this->assertEquals(FormDinMigration::DBMS_SQLITE, $migration->normalizeDriverName('sqlite'));
        $this->assertEquals(FormDinMigration::DBMS_SQLITE, $migration->normalizeDriverName('sqlite3'));
        $this->assertEquals(FormDinMigration::DBMS_POSTGRES, $migration->normalizeDriverName('pgsql'));
        $this->assertEquals(FormDinMigration::DBMS_POSTGRES, $migration->normalizeDriverName('postgres'));
        $this->assertEquals(FormDinMigration::DBMS_POSTGRES, $migration->normalizeDriverName('postgresql'));
        $this->assertEquals(FormDinMigration::DBMS_SQLSERVER, $migration->normalizeDriverName('sqlsrv'));
        $this->assertEquals(FormDinMigration::DBMS_SQLSERVER, $migration->normalizeDriverName('mssql'));
        $this->assertEquals(FormDinMigration::DBMS_SQLSERVER, $migration->normalizeDriverName('sqlserver'));
        $this->assertEquals(FormDinMigration::DBMS_MYSQL, $migration->normalizeDriverName('mysql'));
        $this->assertEquals(FormDinMigration::DBMS_MYSQL, $migration->normalizeDriverName('mariadb'));
    }

    public function test_driver_check_with_pdo_sqlite(): void
    {
        $pdo = new PDO('sqlite::memory:');
        $migration = new ConcreteMigrationForTest();

        $this->assertEquals(FormDinMigration::DBMS_SQLITE, $migration->getDriverName(null, $pdo));
        $this->assertEquals(FormDinMigration::DBMS_SQLITE, $migration->getDriver(null, $pdo));
        $this->assertTrue($migration->isSqlite(null, $pdo));
        $this->assertFalse($migration->isMysql(null, $pdo));
        $this->assertFalse($migration->isPostgres(null, $pdo));
        $this->assertFalse($migration->isSqlServer(null, $pdo));
    }

    public function test_driver_check_with_sqlite_dbapoio(): void
    {
        $migration = new ConcreteMigrationForTest('V001', 'dbapoio');

        $this->assertEquals(FormDinMigration::DBMS_SQLITE, $migration->getDriverName());
        $this->assertTrue($migration->isSqlite());
        $this->assertFalse($migration->isMysql());
        $this->assertFalse($migration->isPostgres());
        $this->assertFalse($migration->isSqlServer());
    }

    public function test_default_fallback_to_mysql(): void
    {
        $migration = new ConcreteMigrationForTest('V001', 'non_existing_db_123');

        $this->assertEquals(FormDinMigration::DBMS_MYSQL, $migration->getDriverName());
        $this->assertTrue($migration->isMysql());
        $this->assertFalse($migration->isPostgres());
        $this->assertFalse($migration->isSqlite());
        $this->assertFalse($migration->isSqlServer());
    }

    public function test_transaction_management_helpers(): void
    {
        $migration = new ConcreteMigrationForTest('V001', 'dbapoio');

        // Testa transaction com callback de sucesso
        $result = $migration->transaction(function(PDO $conn) {
            $this->assertInstanceOf(PDO::class, $conn);
            return 'sucesso';
        });
        $this->assertEquals('sucesso', $result);

        // Testa transaction com rollback automático em caso de exceção
        try {
            $migration->transaction(function(PDO $conn) {
                throw new \DomainException("Erro simulado dentro da transacao");
            });
            $this->fail("Deveria ter lançado DomainException");
        } catch (\DomainException $e) {
            $this->assertEquals("Erro simulado dentro da transacao", $e->getMessage());
        }

        // Garante que não sobrou transação aberta
        $this->assertNull(\Adianti\Database\TTransaction::get());
    }

    public function test_supported_dbms_default_and_custom(): void
    {
        $defaultMigration = new ConcreteMigrationForTest();
        $this->assertEquals(FormDinMigration::DBMS_ALL, $defaultMigration->getSupportedDbms());
        $this->assertTrue($defaultMigration->isDbmsSupported('mysql'));
        $this->assertTrue($defaultMigration->isDbmsSupported('pgsql'));
        $this->assertTrue($defaultMigration->isDbmsSupported('sqlite'));
        $this->assertTrue($defaultMigration->isDbmsSupported('sqlsrv'));

        $mysqlOnly = new class extends FormDinMigration {
            public function getVersion(): string { return 'V099'; }
            public function getDescription(): string { return 'MySQL Only'; }
            public function getDatabase(): string { return 'test_db'; }
            public function getSystemVersion(): string { return '1.0.0'; }
            public function getSupportedDbms(): array|string { return self::DBMS_MYSQL; }
            public function up(): void {}
            public function down(): void {}
        };

        $this->assertEquals(FormDinMigration::DBMS_MYSQL, $mysqlOnly->getSupportedDbms());
        $this->assertTrue($mysqlOnly->isDbmsSupported('mysql'));
        $this->assertTrue($mysqlOnly->isDbmsSupported('mariadb'));
        $this->assertFalse($mysqlOnly->isDbmsSupported('pgsql'));
        $this->assertFalse($mysqlOnly->isDbmsSupported('sqlite'));
        $this->assertFalse($mysqlOnly->isDbmsSupported('sqlsrv'));

        $multiDbms = new class extends FormDinMigration {
            public function getVersion(): string { return 'V100'; }
            public function getDescription(): string { return 'Multi'; }
            public function getDatabase(): string { return 'test_db'; }
            public function getSystemVersion(): string { return '1.0.0'; }
            public function getSupportedDbms(): array|string { return [self::DBMS_MYSQL, self::DBMS_POSTGRES]; }
            public function up(): void {}
            public function down(): void {}
        };

        $this->assertTrue($multiDbms->isDbmsSupported('mysql'));
        $this->assertTrue($multiDbms->isDbmsSupported('pgsql'));
        $this->assertFalse($multiDbms->isDbmsSupported('sqlite'));
        $this->assertFalse($multiDbms->isDbmsSupported('sqlsrv'));
    }

    public function test_schema_helpers_on_sqlite_memory(): void
    {
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $migration = new ConcreteMigrationForTest();
        $migration->setConnection($pdo);

        // 1. Tabela inicial
        $this->assertFalse($migration->tableExists('clientes'));
        $migration->executeSql("CREATE TABLE clientes (id INTEGER PRIMARY KEY, nome VARCHAR(100))");
        $this->assertTrue($migration->tableExists('clientes'));

        // 2. Colunas
        $this->assertTrue($migration->columnExists('clientes', 'nome'));
        $this->assertFalse($migration->columnExists('clientes', 'cpf'));

        // Adiciona coluna
        $migration->addColumn('clientes', 'cpf', 'VARCHAR(14) NULL');
        $this->assertTrue($migration->columnExists('clientes', 'cpf'));

        // 3. Índices
        $this->assertFalse($migration->indexExists('clientes', 'idx_clientes_cpf'));
        $migration->executeSql("CREATE INDEX idx_clientes_cpf ON clientes (cpf)");
        $this->assertTrue($migration->indexExists('clientes', 'idx_clientes_cpf'));

        // Remove índice
        $migration->dropIndex('clientes', 'idx_clientes_cpf');
        $this->assertFalse($migration->indexExists('clientes', 'idx_clientes_cpf'));

        // Remove coluna
        $migration->dropColumn('clientes', 'cpf');
        $this->assertFalse($migration->columnExists('clientes', 'cpf'));

        // 4. Views
        $this->assertFalse($migration->tableExists('vw_clientes_ativos'));
        $migration->createOrReplaceView('vw_clientes_ativos', 'SELECT id, nome FROM clientes');
        $this->assertTrue($migration->tableExists('vw_clientes_ativos'));

        // Remove view
        $migration->dropView('vw_clientes_ativos');
        $this->assertFalse($migration->tableExists('vw_clientes_ativos'));

        // 5. executeByDbms
        $migration->executeByDbms([
            'sqlite' => "INSERT INTO clientes (nome) VALUES ('Fulano')",
            'mysql'  => "INSERT INTO clientes (nome) VALUES ('Ciclano')",
        ]);

        $count = $pdo->query("SELECT count(*) FROM clientes WHERE nome = 'Fulano'")->fetchColumn();
        $this->assertEquals(1, $count);

        // Testa exceção em executeByDbms quando não há chave para o driver
        try {
            $migration->executeByDbms(['mysql' => 'SELECT 1']);
            $this->fail("Deveria ter lançado RuntimeException para SGBD não suportado no array");
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString("Nenhum SQL definido para o SGBD 'sqlite'", $e->getMessage());
        }
    }
}
