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
    public const DBMS_MYSQL     = 'mysql';
    public const DBMS_POSTGRES  = 'pgsql';
    public const DBMS_SQLITE    = 'sqlite';
    public const DBMS_SQLSERVER = 'sqlsrv';

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

        // 2. Tenta obter pelo PDO informado
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

    /**
     * Retorna a conexão PDO ativa da transação atual.
     * Se nenhuma transação estiver aberta, abre automaticamente para o banco padrão da migration.
     */
    public function getConnection(?string $database = null): PDO
    {
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
        $database = $database ?: $this->getDatabase();
        return \Adianti\Database\TTransaction::open($database);
    }

    /**
     * Comita a transação ativa tratando commits implícitos em SGBDs como MySQL.
     */
    public function commitTransaction(): void
    {
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
}
