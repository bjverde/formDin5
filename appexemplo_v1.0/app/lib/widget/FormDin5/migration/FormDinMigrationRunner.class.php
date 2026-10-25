<?php

/**
 * FormDinMigrationRunner
 *
 * Responsável por:
 *  1. Garantir que a tabela de controle (db_migrations) exista no banco DATABASE_PERMISSION.
 *  2. Escanear os arquivos V*.php nesta pasta e determinar quais ainda não foram executados.
 *  3. Executar as migrations pendentes em ordem e registrar o resultado.
 *
 * Regras:
 *  - A migration só roda se sua system_version for <= à versão corrente do sistema
 *    (application[system][system_version]) usando versionamento semântico.
 *
 * Uso:
 *   $runner = new FormDinMigrationRunner();
 *   $runner->run();
 *
 * Para listar o status sem executar:
 *   $runner->status();
 */
class FormDinMigrationRunner
{
    /** Versão do runner de migrations */
    public const MIGRATION_VERSION = '1.0.0';

    /** Banco padrão de rastreamento caso não seja informado */
    public const DEFAULT_TRACKING_DB = 'permission';

    /** Banco onde a tabela db_migrations é mantida */
    private string $trackingDb;

    /** Diretório onde ficam os arquivos de migration */
    private string $migrationsPath;

    /** Flag indicando se a tabela de controle já foi verificada/criada */
    private bool $tableEnsured = false;

    public function __construct(?string $migrationsPath = null, ?string $trackingDb = null)
    {
        $this->setMigrationsPath($migrationsPath);
        $this->setTrackingDb($trackingDb);
    }

    /**
     * Retorna o banco onde a tabela db_migrations é mantida.
     */
    public function getTrackingDb(): string
    {
        return $this->trackingDb;
    }

    /**
     * Define o banco onde a tabela db_migrations é mantida.
     * Regra: parâmetro -> application.php -> fallback 'permission'
     */
    public function setTrackingDb(?string $trackingDb = null): void
    {
        $trackingDb = trim((string) $trackingDb);

        if (empty($trackingDb)) {
            $ini = $this->getInfoApplication();
            $trackingDb = $ini['general']['tracking_db'] 
                ?? $ini['system']['tracking_db'] 
                ?? self::DEFAULT_TRACKING_DB;
        }

        $this->trackingDb = $trackingDb;
        $this->tableEnsured = false;
    }

    /**
     * Retorna o diretório onde ficam os arquivos de migration.
     */
    public function getMigrationsPath(): string
    {
        return $this->migrationsPath;
    }

    /**
     * Define o diretório onde ficam os arquivos de migration.
     * Regra: parâmetro -> PATH/app/database/migrations
     * Valor Default: PATH/app/database/migrations
     */
    public function setMigrationsPath(?string $migrationsPath = null): void
    {
        $migrationsPath = trim((string) $migrationsPath);

        if (empty($migrationsPath)) {
            $this->migrationsPath = FileHelper::getCaminhoSistema() . '/app/database/migrations';
            return;
        }

        // Se for caminho relativo, anexa ao caminho do sistema
        if (!preg_match('#^([a-zA-Z]:|[\\\\/])#', $migrationsPath)) {
            $migrationsPath = FileHelper::getCaminhoSistema() . '/' . ltrim($migrationsPath, '/\\');
        }

        $this->migrationsPath = $migrationsPath;
    }
    // Métodos run(), status(), scanMigrationFiles(), execute()...
    // Gravando o histórico de forma agnóstica a qualquer banco (compatível com MySQL, PgSQL, etc.)

    // -------------------------------------------------------------------------
    // API pública
    // -------------------------------------------------------------------------

    /**
     * Executa todas as migrations pendentes em ordem.
     */
    public function run(): void
    {
        $pending = $this->getPendingMigrations();

        if (empty($pending)) {
            echo "Nenhuma migration pendente. Banco de dados ja esta atualizado.\n";
            return;
        }

        echo count($pending) . " migration(s) pendente(s) encontrada(s).\n\n";

        foreach ($pending as $migration) {
            $this->execute($migration);
        }

        echo "\nTodas as migrations foram processadas.\n";
    }

    /**
     * Exibe o status de todas as migrations (executadas e pendentes) sem executar nada.
     */
    public function status(): void
    {
        $executed  = $this->ensureTableAndGetExecuted();
        $allFiles  = $this->scanMigrationFiles();

        echo str_pad('Versao', 8)
            . str_pad('Status', 12)
            . str_pad('Banco', 26)
            . str_pad('Sys Ver', 10)
            . str_pad('Executado em', 22)
            . "Descricao\n";
        echo str_repeat('-', 100) . "\n";

        foreach ($allFiles as ['version' => $version, 'class' => $class, 'file' => $file]) {
            require_once $file;
            /** @var FormDinMigration $m */
            $m = new $class();

            $row = $executed[$version] ?? null;

            $status     = $row ? $row['status'] : 'pendente';
            $executedAt = $row ? $row['executed_at'] : '-';

            echo str_pad($version, 8)
                . str_pad($status, 12)
                . str_pad($m->getDatabase(), 26)
                . str_pad($m->getSystemVersion(), 10)
                . str_pad($executedAt, 22)
                . $m->getDescription() . "\n";
        }
    }

    /**
     * Reverte as últimas N migrations executadas (default: 1).
     * Executa o método down() de cada migration em ordem reversa.
     *
     * @param int|null $steps Quantidade de migrations para reverter
     */
    public function rollback(?int $steps = 1): void
    {
        $steps = $steps ?? 1;
        if ($steps <= 0) {
            echo "Nenhum passo de rollback especificado.\n";
            return;
        }

        $executed = $this->getExecutedMigrationsDescending();

        if (empty($executed)) {
            echo "Nenhuma migration executada encontrada para reverter.\n";
            return;
        }

        $toRollback = array_slice($executed, 0, $steps);

        echo "Iniciando rollback de " . count($toRollback) . " migration(s)...\n\n";

        foreach ($toRollback as $migration) {
            $this->executeDown($migration);
        }

        echo "\nRollback concluido com sucesso.\n";
    }

    /**
     * Reverte todas as migrations executadas até atingir a versão alvo especificada.
     * Exemplo: rollbackTo('V010') reverte todas as versões > V010, deixando V010 como a última ativa.
     *
     * @param string $targetVersion Versão alvo (ex: 'V010', ou 'V000' para reverter todas)
     */
    public function rollbackTo(string $targetVersion): void
    {
        $targetVersion = strtoupper(trim($targetVersion));
        $executed = $this->getExecutedMigrationsDescending();

        $toRollback = [];
        foreach ($executed as $migration) {
            // Se a versão da migration for maior que a versão alvo, deve ser revertida
            if (strnatcasecmp($migration->getVersion(), $targetVersion) > 0) {
                $toRollback[] = $migration;
            }
        }

        if (empty($toRollback)) {
            echo "Nenhuma migration com versao superior a '{$targetVersion}' para reverter.\n";
            return;
        }

        echo "Iniciando rollback de " . count($toRollback) . " migration(s) ate a versao '{$targetVersion}'...\n\n";

        foreach ($toRollback as $migration) {
            $this->executeDown($migration);
        }

        echo "\nRollback ate a versao '{$targetVersion}' concluido com sucesso.\n";
    }

    /**
     * Reverte todas as migrations executadas no sistema.
     */
    public function reset(): void
    {
        $this->rollbackTo('V000');
    }

    /**
     * Retorna a lista de instâncias das migrations já executadas com sucesso,
     * em ordem decrescente de versão.
     *
     * @return FormDinMigration[]
     */
    public function getExecutedMigrationsDescending(): array
    {
        $executed = $this->ensureTableAndGetExecuted();
        $allFiles = $this->scanMigrationFiles();

        $fileMap = [];
        foreach ($allFiles as $item) {
            $fileMap[$item['version']] = $item;
        }

        // Filtra apenas as que tiveram status 'success'
        $executedVersions = [];
        foreach ($executed as $version => $row) {
            if (($row['status'] ?? '') === 'success') {
                $executedVersions[] = $version;
            }
        }

        // Ordena em ordem decrescente natural (ex: V028, V027, ..., V001)
        usort($executedVersions, fn($a, $b) => strnatcasecmp($b, $a));

        $migrations = [];
        foreach ($executedVersions as $version) {
            if (isset($fileMap[$version])) {
                $file  = $fileMap[$version]['file'];
                $class = $fileMap[$version]['class'];
                require_once $file;
                $migrations[] = new $class();
            }
        }

        return $migrations;
    }

    // -------------------------------------------------------------------------
    // Internos
    // -------------------------------------------------------------------------

    /**
     * Identifica o tipo de SGBD (driver) da conexão informada.
     * Retorna em minúsculo: 'mysql', 'pgsql', 'sqlite', 'sqlsrv', etc.
     */
    public function getDatabaseType(?string $database = null, ?PDO $conn = null): string
    {
        $database = $database ?: $this->getTrackingDb();

        if (class_exists(\Adianti\Database\TConnection::class)) {
            try {
                $info = \Adianti\Database\TConnection::getDatabaseInfo($database);
                if (!empty($info['type'])) {
                    return strtolower($info['type']);
                }
            } catch (\Throwable $e) {
                // fallback para PDO
            }
        }

        if ($conn instanceof PDO) {
            try {
                $driver = $conn->getAttribute(PDO::ATTR_DRIVER_NAME);
                if (!empty($driver)) {
                    return strtolower($driver);
                }
            } catch (\Throwable $e) {
                // fallback
            }
        }

        return 'mysql';
    }

    /**
     * Retorna o comando SQL DDL para criar a tabela de controle db_migrations
     * de acordo com o SGBD configurado.
     */
    public function getCreateTableSql(string $dbType): string
    {
        $dbType = strtolower($dbType);

        switch ($dbType) {
            case 'sqlite':
                return "CREATE TABLE IF NOT EXISTS db_migrations (
                    id             INTEGER PRIMARY KEY AUTOINCREMENT,
                    script_id      VARCHAR(50)  NOT NULL UNIQUE,
                    description    VARCHAR(255) NOT NULL DEFAULT '',
                    db_name        VARCHAR(100) NOT NULL DEFAULT '',
                    system_version VARCHAR(20)  NOT NULL DEFAULT '',
                    executed_at    TEXT         NOT NULL,
                    status         TEXT         NOT NULL CHECK (status IN ('success','error'))
                )";

            case 'pgsql':
            case 'postgres':
            case 'postgresql':
                return "CREATE TABLE IF NOT EXISTS db_migrations (
                    id             SERIAL PRIMARY KEY,
                    script_id      VARCHAR(50)  NOT NULL UNIQUE,
                    description    VARCHAR(255) NOT NULL DEFAULT '',
                    db_name        VARCHAR(100) NOT NULL DEFAULT '',
                    system_version VARCHAR(20)  NOT NULL DEFAULT '',
                    executed_at    TIMESTAMP    NOT NULL,
                    status         VARCHAR(10)  NOT NULL CHECK (status IN ('success','error'))
                )";

            case 'sqlsrv':
            case 'mssql':
            case 'sqlserver':
                return "IF OBJECT_ID('db_migrations', 'U') IS NULL
                CREATE TABLE db_migrations (
                    id             INT IDENTITY(1,1) PRIMARY KEY,
                    script_id      VARCHAR(50)  NOT NULL UNIQUE,
                    description    VARCHAR(255) NOT NULL DEFAULT '',
                    db_name        VARCHAR(100) NOT NULL DEFAULT '',
                    system_version VARCHAR(20)  NOT NULL DEFAULT '',
                    executed_at    DATETIME2    NOT NULL,
                    status         VARCHAR(10)  NOT NULL CHECK (status IN ('success','error'))
                )";

            case 'mysql':
            case 'mariadb':
            default:
                return "CREATE TABLE IF NOT EXISTS db_migrations (
                    id             INT AUTO_INCREMENT PRIMARY KEY,
                    script_id      VARCHAR(50)  NOT NULL UNIQUE,
                    description    VARCHAR(255) NOT NULL DEFAULT '',
                    db_name        VARCHAR(100) NOT NULL DEFAULT '',
                    system_version VARCHAR(20)  NOT NULL DEFAULT '',
                    executed_at    DATETIME     NOT NULL,
                    status         VARCHAR(10)  NOT NULL CHECK (status IN ('success','error'))
                )";
        }
    }

    /**
     * Garante que a tabela de controle db_migrations exista no SGBD configurado.
     */
    public function ensureTable(): void
    {
        if ($this->tableEnsured) {
            return;
        }

        TTransaction::open($this->getTrackingDb());
        $conn = TTransaction::get();

        $dbType    = $this->getDatabaseType($this->getTrackingDb(), $conn);
        $createSql = $this->getCreateTableSql($dbType);

        $conn->exec($createSql);

        try {
            TTransaction::close();
        } catch (\Throwable $e) {
            // Em PHP 8+, comandos DDL causam commit implícito no MySQL.
            if (strpos($e->getMessage(), 'active transaction') === false) {
                throw $e;
            }
        }

        $this->tableEnsured = true;
    }

    /**
     * Garante que a tabela de controle existe (conforme o SGBD configurado) e retorna
     * as versões já executadas.
     *
     * @return array indexado por script_id com dados das migrations executadas
     */
    public function ensureTableAndGetExecuted(): array
    {
        $this->ensureTable();

        TTransaction::open($this->getTrackingDb());
        $conn = TTransaction::get();

        $rows = $conn->query(
            "SELECT script_id, status, executed_at FROM db_migrations"
        )->fetchAll(PDO::FETCH_ASSOC);

        try {
            TTransaction::close();
        } catch (\Throwable $e) {
            if (strpos($e->getMessage(), 'active transaction') === false) {
                throw $e;
            }
        }

        $map = [];
        foreach ($rows as $row) {
            $map[$row['script_id']] = $row;
        }
        return $map;
    }

    /**
     * Escaneia a pasta e retorna metadados de todos os arquivos V*.php em ordem.
     * Retorna array de ['version' => 'V001', 'class' => 'V001InitialSchema', 'file' => '...']
     */
    private function scanMigrationFiles(): array
    {
        $path = $this->getMigrationsPath();
        if (!is_dir($path)) {
            throw new InvalidArgumentException("Diretório de migrations inválido ou não encontrado: '{$path}'.");
        }

        $files = glob(rtrim($path, '/\\') . '/V*.php') ?: [];
        sort($files); // garante ordem lexicográfica (V001 < V002 < ...)

        $result = [];
        foreach ($files as $file) {
            $basename = basename($file, '.php');

            // Extrai prefixo de versão: V001, V002, etc.
            if (!preg_match('/^(V\d+)/i', $basename, $matches)) {
                continue;
            }

            $result[] = [
                'version' => strtoupper($matches[1]),
                'class'   => $basename,
                'file'    => $file,
            ];
        }

        return $result;
    }

    public function getInfoApplication(): array|null
    {
        $ini = class_exists(\Adianti\Core\AdiantiApplicationConfig::class)
            ? \Adianti\Core\AdiantiApplicationConfig::get()
            : null;
        return $ini;
    }

    public function getTitleApplication(): string
    {
        $ini = $this->getInfoApplication();
        return (string) ($ini['general']['title'] ?? '');
    }

    public function getSystemVersion(): string
    {
        $ini = $this->getInfoApplication();
        return (string) ($ini['system']['system_version'] ?? '0.0.0');
    }

    public function getTitleVersion(): string
    {
        return $this->getTitleApplication() . ' ' . $this->getSystemVersion();
    }    

    /**
     * Retorna a versão corrente do sistema a partir de application.ini.
     */
    private function getCurrentSystemVersion(): string
    {
        return $this->getSystemVersion();
    }

    /**
     * Verifica se a versão da migration é compatível com a versão corrente do sistema.
     * Retorna false (e imprime aviso) se a migration exige uma versão ainda não implantada.
     */
    private function isCompatibleWithSystemVersion(FormDinMigration $migration): bool
    {
        $migrationSysVersion = $migration->getSystemVersion();
        $currentVersion      = $this->getCurrentSystemVersion();

        if (version_compare($migrationSysVersion, $currentVersion, '>')) {
            echo "  -> IGNORADA: migration pertence a versao {$migrationSysVersion}"
                . " mas o sistema esta na versao {$currentVersion}."
                . " Atualize o sistema antes de executar esta migration.\n\n";
            return false;
        }

        return true;
    }

    /**
     * Retorna instâncias das migrations ainda não executadas com sucesso.
     *
     * @return FormDinMigration[]
     */
    private function getPendingMigrations(): array
    {
        $executed = $this->ensureTableAndGetExecuted();
        $allFiles = $this->scanMigrationFiles();

        $pending = [];
        foreach ($allFiles as ['version' => $version, 'class' => $class, 'file' => $file]) {
            $row = $executed[$version] ?? null;

            // Pula apenas se já executada com sucesso
            if ($row && $row['status'] === 'success') {
                continue;
            }

            require_once $file;
            $pending[] = new $class();
        }

        return $pending;
    }

    /**
     * Registra o resultado da migration na tabela de controle db_migrations
     * de forma agnóstica a qualquer SGBD (sem ON DUPLICATE KEY e sem NOW()).
     */
    public function recordMigration(string $version, string $desc, string $database, string $systemVersion, string $status): void
    {
        $this->ensureTable();

        TTransaction::open($this->getTrackingDb());
        $conn = TTransaction::get();

        $checkStmt = $conn->prepare("SELECT script_id FROM db_migrations WHERE script_id = :script_id");
        $checkStmt->execute([':script_id' => $version]);
        $exists = (bool) $checkStmt->fetch(PDO::FETCH_ASSOC);

        $now = date('Y-m-d H:i:s');

        if ($exists) {
            $stmt = $conn->prepare("
                UPDATE db_migrations
                SET description    = :description,
                    db_name        = :db_name,
                    system_version = :system_version,
                    executed_at    = :executed_at,
                    status         = :status
                WHERE script_id    = :script_id
            ");
        } else {
            $stmt = $conn->prepare("
                INSERT INTO db_migrations
                    (script_id, description, db_name, system_version, executed_at, status)
                VALUES
                    (:script_id, :description, :db_name, :system_version, :executed_at, :status)
            ");
        }

        $stmt->execute([
            ':script_id'      => $version,
            ':description'    => $desc,
            ':db_name'        => $database,
            ':system_version' => $systemVersion,
            ':executed_at'    => $now,
            ':status'         => $status,
        ]);

        try {
            TTransaction::close();
        } catch (\Throwable $e) {
            // Em MySQL comandos DDL anteriores podem ter causado commit implícito
            if (strpos($e->getMessage(), 'active transaction') === false) {
                throw $e;
            }
        }
    }

    /**
     * Executa com segurança o commit/fechamento de todas as transações que
     * possam ter ficado abertas pela migration ou pelo runner.
     */
    public function closeOpenTransactions(): void
    {
        if (!class_exists(\Adianti\Database\TTransaction::class)) {
            return;
        }

        while (\Adianti\Database\TTransaction::get() instanceof PDO) {
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
     * Executa com segurança o rollback de todas as transações abertas em caso de falha.
     */
    public function rollbackOpenTransactions(): void
    {
        if (!class_exists(\Adianti\Database\TTransaction::class)) {
            return;
        }

        try {
            \Adianti\Database\TTransaction::rollbackAll();
        } catch (\Throwable $ignored) {
            while (\Adianti\Database\TTransaction::get() instanceof PDO) {
                try {
                    \Adianti\Database\TTransaction::rollback();
                } catch (\Throwable $e) {
                    break;
                }
            }
        }
    }

    /**
     * Executa uma migration e registra o resultado em db_migrations.
     * O ciclo de transação é gerenciado automaticamente para evitar que a migration
     * precise abrir/fechar transações manualmente ou deixe transações pendentes.
     */
    private function execute(FormDinMigration $migration): void
    {
        $version       = $migration->getVersion();
        $desc          = $migration->getDescription();
        $database      = $migration->getDatabase();
        $systemVersion = $migration->getSystemVersion();

        echo "[{$version}] {$desc}\n";

        if (!$this->isCompatibleWithSystemVersion($migration)) {
            return;
        }

        // Abre transação no banco alvo se não houver nenhuma aberta
        if (class_exists(\Adianti\Database\TTransaction::class) && !\Adianti\Database\TTransaction::get()) {
            \Adianti\Database\TTransaction::open($database);
        }

        try {
            $migration->up();

            // Garante o fechamento/commit de todas as transações abertas
            $this->closeOpenTransactions();

            $this->recordMigration($version, $desc, $database, $systemVersion, 'success');
            echo "  -> Concluida com sucesso.\n\n";

        } catch (\Throwable $e) {
            // Em caso de falha, cancela qualquer transação pendente em qualquer banco
            $this->rollbackOpenTransactions();

            try {
                $this->recordMigration($version, $desc, $database, $systemVersion, 'error');
            } catch (\Throwable $recordEx) {
                echo "  -> AVISO: Não foi possível registrar o erro na tabela db_migrations: " . $recordEx->getMessage() . "\n";
            }

            echo "  -> ERRO: " . $e->getMessage() . "\n\n";

            // Relança para interromper o runner
            throw $e;
        }
    }

    /**
     * Remove o registro de uma migration revertida da tabela de controle db_migrations.
     */
    public function deleteMigrationRecord(string $version): void
    {
        $this->ensureTable();

        TTransaction::open($this->getTrackingDb());
        $conn = TTransaction::get();

        $stmt = $conn->prepare("DELETE FROM db_migrations WHERE script_id = :script_id");
        $stmt->execute([':script_id' => $version]);

        try {
            TTransaction::close();
        } catch (\Throwable $e) {
            if (strpos($e->getMessage(), 'active transaction') === false) {
                throw $e;
            }
        }
    }

    /**
     * Executa a reversão (down) de uma migration e remove seu registro de db_migrations.
     */
    private function executeDown(FormDinMigration $migration): void
    {
        $version  = $migration->getVersion();
        $desc     = $migration->getDescription();
        $database = $migration->getDatabase();

        echo "[{$version}] Revertendo: {$desc}\n";

        // Abre transação no banco alvo se não houver nenhuma aberta
        if (class_exists(\Adianti\Database\TTransaction::class) && !\Adianti\Database\TTransaction::get()) {
            \Adianti\Database\TTransaction::open($database);
        }

        try {
            $migration->down();

            // Garante o fechamento/commit de todas as transações abertas
            $this->closeOpenTransactions();

            // Remove o registro da tabela db_migrations para que possa ser executada novamente no futuro
            $this->deleteMigrationRecord($version);
            echo "  -> Revertida com sucesso.\n\n";

        } catch (\Throwable $e) {
            $this->rollbackOpenTransactions();

            echo "  -> ERRO no rollback de [{$version}]: " . $e->getMessage() . "\n\n";
            throw $e;
        }
    }
}
