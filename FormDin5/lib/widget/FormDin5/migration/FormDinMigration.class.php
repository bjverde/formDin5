<?php

/**
 * Classe base abstrata para migrations.
 *
 * Cada migration deve implementar:
 *  - getVersion()     : identificador único (ex: 'V001')
 *  - getDescription() : descrição legível do que a migration faz
 *  - up()             : aplica a migration
 *  - down()           : reverte a migration (melhor esforço — DDL não é transacional no MySQL)
 *
 * ATENÇÃO: Operações DDL (CREATE TABLE, ALTER TABLE, DROP COLUMN) causam IMPLICIT COMMIT
 * no MySQL/MariaDB e NÃO podem ser revertidas via rollback. O método down() serve como
 * procedimento de desfazer manual, não como rollback transacional.
 */
abstract class FormDinMigration
 
{
    /**
     * Identificador único da migration (ex: 'V001').
     * Usado como chave na tabela db_migrations.
     */
    abstract public function getVersion(): string;

    /**
     * Descrição legível do que esta migration faz.
     */
    abstract public function getDescription(): string;

    /**
     * Nome do banco de dados principal que esta migration atua.
     * Exemplos: 'erpcertificadodigital', 'permission'.
     */
    abstract public function getDatabase(): string;

    /**
     * Versão do sistema à qual esta migration pertence (semântico: ex. '1.1.0').
     * O runner recusará a execução se esta versão for superior à versão corrente do sistema.
     */
    abstract public function getSystemVersion(): string;

    /**
     * Aplica a migration (cria tabelas, altera colunas, migra dados, etc.).
     */
    abstract public function up(): void;

    /**
     * Reverte a migration.
     * ATENÇÃO: DDL não é transacional — use com cuidado em produção.
     */
    abstract public function down(): void;

    /** Constantes com identificadores padronizados dos SGBDs */
    public const DBMS_ALL       = '*';
    public const DBMS_MYSQL     = 'mysql';
    public const DBMS_POSTGRES  = 'pgsql';
    public const DBMS_SQLITE    = 'sqlite';
    public const DBMS_SQLSERVER = 'sqlsrv';

    /**
     * Retorna os SGBDs compatíveis com esta migration.
     * Pode retornar uma string ('mysql', 'pgsql', 'sqlite', 'sqlsrv', ou '*' / DBMS_ALL)
     * ou um array de strings (ex: ['mysql', 'pgsql']).
     *
     * Por padrão retorna self::DBMS_ALL ('*'), indicando que a migration é agnóstica.
     * Se uma migration for feita exclusivamente para MySQL (como V001 a V028),
     * ela pode sobrescrever este método retornando self::DBMS_MYSQL.
     *
     * @return array|string
     */
    public function getSupportedDbms(): array|string
    {
        return self::DBMS_ALL;
    }

    /**
     * Verifica se determinado driver/SGBD é suportado por esta migration.
     *
     * @param string|null $driver Nome do driver a testar (default: driver do banco atual da migration)
     * @param string|null $database Nome da conexão opcional
     * @return bool
     */
    public function isDbmsSupported(?string $driver = null, ?string $database = null): bool
    {
        $driver = $driver ?: $this->getDriverName($database);
        $driver = $this->normalizeDriverName($driver);

        $supported = (array) $this->getSupportedDbms();

        foreach ($supported as $item) {
            $normalized = $this->normalizeDriverName((string) $item);
            if ($normalized === '*' || $normalized === 'all' || $normalized === $driver) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retorna o nome real do banco de dados configurado para uma conexão.
     * Útil para referenciar tabelas de outros bancos em Foreign Keys (onde suportado).
     */
    protected function getDatabaseName(string $connection): string
    {
        $info = \Adianti\Database\TConnection::getDatabaseInfo($connection);
        return $info['name'] ?? $connection;
    }

    /**
     * Normaliza os diferentes nomes de driver/SGBD para um padrão consistente.
     */
    public function normalizeDriverName(string $driver): string
    {
        $driver = strtolower(trim($driver));

        return match ($driver) {
            'sqlite', 'sqlite3'               => self::DBMS_SQLITE,
            'pgsql', 'postgres', 'postgresql' => self::DBMS_POSTGRES,
            'sqlsrv', 'mssql', 'sqlserver'    => self::DBMS_SQLSERVER,
            'mysql', 'mariadb'                => self::DBMS_MYSQL,
            default                           => $driver,
        };
    }

    /**
     * Retorna o identificador normalizado do driver/SGBD conectado.
     * Exemplos de retorno: 'mysql', 'pgsql', 'sqlite', 'sqlsrv'.
     *
     * @param string|null $database Nome da conexão (default: $this->getDatabase())
     * @param PDO|null $conn Conexão PDO ativa opcional
     * @return string
     */
    public function getDriverName(?string $database = null, ?PDO $conn = null): string
    {
        $conn = $conn ?: $this->customConnection;

        try {
            $database = $database ?: $this->getDatabase();
        } catch (\Throwable $e) {
            $database = null;
        }

        // 1. Tenta obter pelo TConnection da Adianti se houver configuração
        if (!empty($database) && class_exists(\Adianti\Database\TConnection::class)) {
            try {
                $info = \Adianti\Database\TConnection::getDatabaseInfo($database);
                if (!empty($info['type'])) {
                    return $this->normalizeDriverName($info['type']);
                }
            } catch (\Throwable $e) {
                // continua para tentar via PDO
            }
        }

        // 2. Tenta obter pelo PDO informado ou customizado
        if ($conn instanceof PDO) {
            try {
                $driver = $conn->getAttribute(PDO::ATTR_DRIVER_NAME);
                if (!empty($driver)) {
                    return $this->normalizeDriverName($driver);
                }
            } catch (\Throwable $e) {
                // fallback
            }
        }

        // 3. Tenta obter pela transação ativa do Adianti se houver
        if (class_exists(\Adianti\Database\TTransaction::class)) {
            try {
                $activeConn = \Adianti\Database\TTransaction::get();
                if ($activeConn instanceof PDO) {
                    $driver = $activeConn->getAttribute(PDO::ATTR_DRIVER_NAME);
                    if (!empty($driver)) {
                        return $this->normalizeDriverName($driver);
                    }
                }
            } catch (\Throwable $e) {
                // fallback
            }
        }

        return self::DBMS_MYSQL;
    }

    /**
     * Alias para getDriverName().
     */
    public function getDriver(?string $database = null, ?PDO $conn = null): string
    {
        return $this->getDriverName($database, $conn);
    }

    /**
     * Verifica se o banco de dados conectado é MySQL ou MariaDB.
     */
    public function isMysql(?string $database = null, ?PDO $conn = null): bool
    {
        return $this->getDriverName($database, $conn) === self::DBMS_MYSQL;
    }

    /**
     * Verifica se o banco de dados conectado é PostgreSQL.
     */
    public function isPostgres(?string $database = null, ?PDO $conn = null): bool
    {
        return $this->getDriverName($database, $conn) === self::DBMS_POSTGRES;
    }

    /**
     * Verifica se o banco de dados conectado é SQLite.
     */
    public function isSqlite(?string $database = null, ?PDO $conn = null): bool
    {
        return $this->getDriverName($database, $conn) === self::DBMS_SQLITE;
    }

    /**
     * Verifica se o banco de dados conectado é SQL Server.
     */
    public function isSqlServer(?string $database = null, ?PDO $conn = null): bool
    {
        return $this->getDriverName($database, $conn) === self::DBMS_SQLSERVER;
    }

    // -------------------------------------------------------------------------
    // Gerenciamento de Conexão e Transações
    // -------------------------------------------------------------------------

    private ?PDO $customConnection = null;

    /**
     * Define uma conexão PDO customizada (útil para testes ou conexões isoladas).
     */
    public function setConnection(?PDO $conn): void
    {
        $this->customConnection = $conn;
    }

    /**
     * Retorna a conexão PDO ativa da transação atual.
     * Se nenhuma transação estiver aberta, abre automaticamente para o banco padrão da migration.
     */
    public function getConnection(?string $database = null): PDO
    {
        if ($this->customConnection instanceof PDO) {
            return $this->customConnection;
        }

        $database = $database ?: $this->getDatabase();

        if (class_exists(\Adianti\Database\TTransaction::class)) {
            $conn = \Adianti\Database\TTransaction::get();
            if ($conn instanceof PDO) {
                return $conn;
            }

            return \Adianti\Database\TTransaction::open($database);
        }

        throw new \RuntimeException("Classe TTransaction do Adianti não encontrada.");
    }

    /**
     * Inicia uma transação explicitamente no banco informado.
     */
    public function beginTransaction(?string $database = null): PDO
    {
        if ($this->customConnection instanceof PDO) {
            return $this->customConnection;
        }

        $database = $database ?: $this->getDatabase();
        return \Adianti\Database\TTransaction::open($database);
    }

    /**
     * Comita a transação ativa tratando commits implícitos em SGBDs como MySQL.
     */
    public function commitTransaction(): void
    {
        if ($this->customConnection instanceof PDO) {
            return;
        }

        if (class_exists(\Adianti\Database\TTransaction::class)) {
            try {
                \Adianti\Database\TTransaction::close();
            } catch (\Throwable $e) {
                // Em MySQL comandos DDL causam commit implícito, fazendo close() disparar "no active transaction"
                if (strpos($e->getMessage(), 'active transaction') === false) {
                    throw $e;
                }
            }
        }
    }

    /**
     * Cancela a transação ativa.
     */
    public function rollbackTransaction(): void
    {
        if ($this->customConnection instanceof PDO) {
            return;
        }

        if (class_exists(\Adianti\Database\TTransaction::class)) {
            try {
                \Adianti\Database\TTransaction::rollback();
            } catch (\Throwable $e) {
                // Ignora se não houver transação ativa
            }
        }
    }

    /**
     * Executa uma função/callback dentro de uma transação com commit e rollback automáticos.
     *
     * Exemplo de uso:
     *   $this->transaction(function(PDO $conn) {
     *       $conn->exec("INSERT INTO ...");
     *       $conn->exec("UPDATE ...");
     *   });
     *
     * @param callable(PDO): mixed $callback
     * @param string|null $database
     * @return mixed
     */
    public function transaction(callable $callback, ?string $database = null): mixed
    {
        $database = $database ?: $this->getDatabase();
        $conn = $this->beginTransaction($database);

        try {
            $result = $callback($conn);
            $this->commitTransaction();
            return $result;
        } catch (\Throwable $e) {
            $this->rollbackTransaction();
            throw $e;
        }
    }

    // -------------------------------------------------------------------------
    // Helpers de Introspecção de Schema (Metadados)
    // -------------------------------------------------------------------------

    /**
     * Verifica se uma tabela ou view existe no banco de dados.
     */
    public function tableExists(string $table, ?string $database = null): bool
    {
        $conn = $this->getConnection($database);
        $driver = $this->getDriverName($database, $conn);
        $tableClean = trim($table, '`"[] ');

        return match ($driver) {
            self::DBMS_SQLITE => (function() use ($conn, $tableClean) {
                $stmt = $conn->prepare("SELECT 1 FROM sqlite_master WHERE type IN ('table', 'view') AND LOWER(name) = LOWER(:table)");
                $stmt->execute([':table' => $tableClean]);
                return (bool) $stmt->fetchColumn();
            })(),
            self::DBMS_POSTGRES => (function() use ($conn, $tableClean) {
                $stmt = $conn->prepare("SELECT 1 FROM information_schema.tables WHERE table_schema NOT IN ('pg_catalog', 'information_schema') AND LOWER(table_name) = LOWER(:table)");
                $stmt->execute([':table' => $tableClean]);
                return (bool) $stmt->fetchColumn();
            })(),
            self::DBMS_SQLSERVER => (function() use ($conn, $tableClean) {
                $stmt = $conn->prepare("SELECT 1 FROM information_schema.tables WHERE LOWER(table_name) = LOWER(:table)");
                $stmt->execute([':table' => $tableClean]);
                return (bool) $stmt->fetchColumn();
            })(),
            default => (function() use ($conn, $tableClean) { // MySQL / MariaDB
                $stmt = $conn->prepare("SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND LOWER(table_name) = LOWER(:table)");
                $stmt->execute([':table' => $tableClean]);
                return (bool) $stmt->fetchColumn();
            })(),
        };
    }

    /**
     * Verifica se uma coluna existe em determinada tabela.
     */
    public function columnExists(string $table, string $column, ?string $database = null): bool
    {
        $conn = $this->getConnection($database);
        $driver = $this->getDriverName($database, $conn);
        $tableClean = trim($table, '`"[] ');
        $columnClean = trim($column, '`"[] ');

        return match ($driver) {
            self::DBMS_SQLITE => (function() use ($conn, $tableClean, $columnClean) {
                $quotedTable = str_replace('"', '""', $tableClean);
                $stmt = $conn->query("PRAGMA table_info(\"{$quotedTable}\")");
                if (!$stmt) {
                    return false;
                }
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    if (strcasecmp($row['name'] ?? '', $columnClean) === 0) {
                        return true;
                    }
                }
                return false;
            })(),
            self::DBMS_POSTGRES => (function() use ($conn, $tableClean, $columnClean) {
                $stmt = $conn->prepare("SELECT 1 FROM information_schema.columns WHERE LOWER(table_name) = LOWER(:table) AND LOWER(column_name) = LOWER(:col)");
                $stmt->execute([':table' => $tableClean, ':col' => $columnClean]);
                return (bool) $stmt->fetchColumn();
            })(),
            self::DBMS_SQLSERVER => (function() use ($conn, $tableClean, $columnClean) {
                $stmt = $conn->prepare("SELECT 1 FROM information_schema.columns WHERE LOWER(table_name) = LOWER(:table) AND LOWER(column_name) = LOWER(:col)");
                $stmt->execute([':table' => $tableClean, ':col' => $columnClean]);
                return (bool) $stmt->fetchColumn();
            })(),
            default => (function() use ($conn, $tableClean, $columnClean) { // MySQL
                $stmt = $conn->prepare("SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND LOWER(table_name) = LOWER(:table) AND LOWER(column_name) = LOWER(:col)");
                $stmt->execute([':table' => $tableClean, ':col' => $columnClean]);
                return (bool) $stmt->fetchColumn();
            })(),
        };
    }

    /**
     * Verifica se um índice existe em determinada tabela.
     */
    public function indexExists(string $table, string $indexName, ?string $database = null): bool
    {
        $conn = $this->getConnection($database);
        $driver = $this->getDriverName($database, $conn);
        $tableClean = trim($table, '`"[] ');
        $indexClean = trim($indexName, '`"[] ');

        return match ($driver) {
            self::DBMS_SQLITE => (function() use ($conn, $tableClean, $indexClean) {
                $quotedTable = str_replace('"', '""', $tableClean);
                $stmt = $conn->query("PRAGMA index_list(\"{$quotedTable}\")");
                if (!$stmt) {
                    return false;
                }
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    if (strcasecmp($row['name'] ?? '', $indexClean) === 0) {
                        return true;
                    }
                }
                return false;
            })(),
            self::DBMS_POSTGRES => (function() use ($conn, $tableClean, $indexClean) {
                $stmt = $conn->prepare("SELECT 1 FROM pg_indexes WHERE LOWER(tablename) = LOWER(:table) AND LOWER(indexname) = LOWER(:idx)");
                $stmt->execute([':table' => $tableClean, ':idx' => $indexClean]);
                return (bool) $stmt->fetchColumn();
            })(),
            self::DBMS_SQLSERVER => (function() use ($conn, $tableClean, $indexClean) {
                $stmt = $conn->prepare("SELECT 1 FROM sys.indexes WHERE object_id = OBJECT_ID(:table) AND LOWER(name) = LOWER(:idx)");
                $stmt->execute([':table' => $tableClean, ':idx' => $indexClean]);
                return (bool) $stmt->fetchColumn();
            })(),
            default => (function() use ($conn, $tableClean, $indexClean) { // MySQL
                $stmt = $conn->prepare("SELECT 1 FROM information_schema.statistics WHERE table_schema = DATABASE() AND LOWER(table_name) = LOWER(:table) AND LOWER(index_name) = LOWER(:idx)");
                $stmt->execute([':table' => $tableClean, ':idx' => $indexClean]);
                return (bool) $stmt->fetchColumn();
            })(),
        };
    }

    // -------------------------------------------------------------------------
    // Helpers de Alteração de Schema (DDL Portável)
    // -------------------------------------------------------------------------

    /**
     * Adiciona uma coluna à tabela de forma portável.
     *
     * @param string $table Nome da tabela
     * @param string $column Nome da coluna
     * @param string $typeDefinition Definição do tipo (ex: 'VARCHAR(100) NULL', 'INT DEFAULT 0')
     * @param string|null $database Conexão opcional
     */
    public function addColumn(string $table, string $column, string $typeDefinition, ?string $database = null): void
    {
        $conn = $this->getConnection($database);
        $driver = $this->getDriverName($database, $conn);
        $tableClean = trim($table, '`"[] ');
        $columnClean = trim($column, '`"[] ');

        $sql = match ($driver) {
            self::DBMS_SQLSERVER => "ALTER TABLE {$tableClean} ADD {$columnClean} {$typeDefinition}",
            default              => "ALTER TABLE {$tableClean} ADD COLUMN {$columnClean} {$typeDefinition}",
        };

        $conn->exec($sql);
    }

    /**
     * Remove uma coluna de uma tabela de forma portável.
     */
    public function dropColumn(string $table, string $column, ?string $database = null): void
    {
        $conn = $this->getConnection($database);
        $tableClean = trim($table, '`"[] ');
        $columnClean = trim($column, '`"[] ');

        $conn->exec("ALTER TABLE {$tableClean} DROP COLUMN {$columnClean}");
    }

    /**
     * Remove um índice de uma tabela de forma portável.
     */
    public function dropIndex(string $table, string $indexName, ?string $database = null): void
    {
        $conn = $this->getConnection($database);
        $driver = $this->getDriverName($database, $conn);
        $tableClean = trim($table, '`"[] ');
        $indexClean = trim($indexName, '`"[] ');

        $sql = match ($driver) {
            self::DBMS_MYSQL     => "ALTER TABLE {$tableClean} DROP INDEX {$indexClean}",
            self::DBMS_SQLSERVER => "DROP INDEX {$indexClean} ON {$tableClean}",
            default              => "DROP INDEX IF EXISTS {$indexClean}", // PostgreSQL, SQLite
        };

        $conn->exec($sql);
    }

    /**
     * Remove uma Foreign Key de uma tabela de forma portável.
     */
    public function dropForeignKey(string $table, string $foreignKeyName, ?string $database = null): void
    {
        $conn = $this->getConnection($database);
        $driver = $this->getDriverName($database, $conn);
        $tableClean = trim($table, '`"[] ');
        $fkClean = trim($foreignKeyName, '`"[] ');

        $sql = match ($driver) {
            self::DBMS_MYSQL     => "ALTER TABLE {$tableClean} DROP FOREIGN KEY {$fkClean}",
            self::DBMS_SQLITE    => throw new \RuntimeException("SQLite não suporta remoção direta de FOREIGN KEY via ALTER TABLE."),
            default              => "ALTER TABLE {$tableClean} DROP CONSTRAINT {$fkClean}", // Postgres, SQL Server
        };

        $conn->exec($sql);
    }

    /**
     * Cria ou substitui uma View de forma compatível com os 5 SGBDs.
     */
    public function createOrReplaceView(string $viewName, string $selectSql, ?string $database = null): void
    {
        $conn = $this->getConnection($database);
        $driver = $this->getDriverName($database, $conn);
        $viewClean = trim($viewName, '`"[] ');

        match ($driver) {
            self::DBMS_SQLITE => (function() use ($conn, $viewClean, $selectSql) {
                $conn->exec("DROP VIEW IF EXISTS {$viewClean}");
                $conn->exec("CREATE VIEW {$viewClean} AS {$selectSql}");
            })(),
            self::DBMS_SQLSERVER => $conn->exec("CREATE OR ALTER VIEW {$viewClean} AS {$selectSql}"),
            default              => $conn->exec("CREATE OR REPLACE VIEW {$viewClean} AS {$selectSql}"), // MySQL, Postgres
        };
    }

    /**
     * Remove uma View se existir.
     */
    public function dropView(string $viewName, ?string $database = null): void
    {
        $conn = $this->getConnection($database);
        $viewClean = trim($viewName, '`"[] ');
        $conn->exec("DROP VIEW IF EXISTS {$viewClean}");
    }

    /**
     * Executa comandos SQL diferenciados por SGBD para casos complexos (ex: UPDATE JOIN).
     *
     * Exemplo:
     *   $this->executeByDbms([
     *       'mysql'  => "UPDATE t1 JOIN t2 ON ... SET t1.x = t2.x",
     *       'pgsql'  => "UPDATE t1 SET x = t2.x FROM t2 WHERE ...",
     *       'sqlite' => "UPDATE t1 SET x = (SELECT x FROM t2 WHERE ...)",
     *   ]);
     *
     * @param array<string, string> $sqlByDbms Mapa com o SQL por driver ('mysql', 'pgsql', 'sqlite', 'sqlsrv' ou 'default')
     * @param string|null $database Conexão opcional
     * @return int Quantidade de linhas afetadas
     */
    public function executeByDbms(array $sqlByDbms, ?string $database = null): int
    {
        $conn = $this->getConnection($database);
        $driver = $this->getDriverName($database, $conn);

        $sql = $sqlByDbms[$driver] ?? $sqlByDbms['default'] ?? $sqlByDbms['*'] ?? null;

        if ($sql === null) {
            $supported = implode(', ', array_keys($sqlByDbms));
            throw new \RuntimeException("Nenhum SQL definido para o SGBD '{$driver}'. Definidos no array: [{$supported}].");
        }

        return (int) $conn->exec($sql);
    }

    /**
     * Executa uma instrução SQL com suporte a parâmetros nomeados ou posicionais.
     *
     * @param string $sql
     * @param array $params
     * @param string|null $database
     * @return int Linhas afetadas
     */
    public function executeSql(string $sql, array $params = [], ?string $database = null): int
    {
        $conn = $this->getConnection($database);

        if (empty($params)) {
            return (int) $conn->exec($sql);
        }

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }
}
