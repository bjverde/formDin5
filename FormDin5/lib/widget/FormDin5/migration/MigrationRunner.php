<?php

/**
 * MigrationRunner
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
 *   $runner = new MigrationRunner();
 *   $runner->run();
 *
 * Para listar o status sem executar:
 *   $runner->status();
 */
class MigrationRunner
{
    /** Versão do runner de migrations */
    public const MIGRATION_VERSION = '1.0.0';

    /** Banco onde a tabela db_migrations é mantida */
    private string $trackingDb = Constantes::DATABASE_PERMISSION;

    /** Diretório onde ficam os arquivos de migration */
    private string $migrationsPath;

    public function __construct()
    {
        $this->migrationsPath = __DIR__;
    }

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
            /** @var Migration $m */
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

    // -------------------------------------------------------------------------
    // Internos
    // -------------------------------------------------------------------------

    /**
     * Garante que a tabela de controle existe (com o schema atual) e retorna
     * as versões já executadas. Inclui migração automática do schema legado.
     *
     * @return array indexado por script_id com dados das migrations executadas
     */
    private function ensureTableAndGetExecuted(): array
    {
        TTransaction::open($this->trackingDb);
        $conn = TTransaction::get();

        // Cria a tabela com o schema completo caso ainda não exista
        $conn->exec("
            CREATE TABLE IF NOT EXISTS db_migrations (
                id                INT AUTO_INCREMENT PRIMARY KEY,
                script_id         VARCHAR(50)  NOT NULL UNIQUE,
                description       VARCHAR(255) NOT NULL DEFAULT '',
                db_name           VARCHAR(100) NOT NULL DEFAULT '',
                system_version    VARCHAR(20)  NOT NULL DEFAULT '',
                executed_at       DATETIME     NOT NULL,
                status            ENUM('success','error') NOT NULL
            ) ENGINE=InnoDB COMMENT 'Tabela de controle de migrations';
        ");

        $rows = $conn->query(
            "SELECT script_id, status, executed_at FROM db_migrations"
        )->fetchAll(PDO::FETCH_ASSOC);

        try {
            TTransaction::close();
        } catch (Exception $e) {
            // Em PHP 8+, comandos DDL (CREATE TABLE) causam um commit implícito no MySQL.
            // Isso faz com que o PDO lance uma exceção ao tentar commitar (TTransaction::close()).
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
        $files = glob($this->migrationsPath . '/V*.php');
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
        $ini = \Adianti\Core\AdiantiApplicationConfig::get();
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
    private function isCompatibleWithSystemVersion(Migration $migration): bool
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
     * @return Migration[]
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
     * Executa uma migration e registra o resultado em db_migrations.
     */
    private function execute(Migration $migration): void
    {
        $version       = $migration->getVersion();
        $desc          = $migration->getDescription();
        $database      = $migration->getDatabase();
        $systemVersion = $migration->getSystemVersion();

        echo "[{$version}] {$desc}\n";

        if (!$this->isCompatibleWithSystemVersion($migration)) {
            return;
        }

        try {
            $migration->up();

            TTransaction::open($this->trackingDb);
            $conn = TTransaction::get();
            $stmt = $conn->prepare("
                INSERT INTO db_migrations
                    (script_id, description, db_name, system_version, executed_at, status)
                VALUES
                    (:script_id, :description, :db_name, :system_version, NOW(), 'success')
                ON DUPLICATE KEY UPDATE
                    description    = VALUES(description),
                    db_name        = VALUES(db_name),
                    system_version = VALUES(system_version),
                    executed_at    = NOW(),
                    status         = 'success'
            ");
            $stmt->execute([
                ':script_id'         => $version,
                ':description'       => $desc,
                ':db_name'           => $database,
                ':system_version'    => $systemVersion,
            ]);
            TTransaction::close();

            echo "  -> Concluida com sucesso.\n\n";

        } catch (Exception $e) {
            // Tenta fechar qualquer transação aberta antes de registrar o erro
            try { TTransaction::rollback(); } catch (Exception $ignored) {}

            TTransaction::open($this->trackingDb);
            $conn = TTransaction::get();
            $stmt = $conn->prepare("
                INSERT INTO db_migrations
                    (script_id, description, db_name, system_version, executed_at, status)
                VALUES
                    (:script_id, :description, :db_name, :system_version, NOW(), 'error')
                ON DUPLICATE KEY UPDATE
                    executed_at = NOW(),
                    status      = 'error'
            ");
            $stmt->execute([
                ':script_id'         => $version,
                ':description'       => $desc,
                ':db_name'           => $database,
                ':system_version'    => $systemVersion,
            ]);
            TTransaction::close();

            echo "  -> ERRO: " . $e->getMessage() . "\n\n";

            // Relança para interromper o runner
            throw $e;
        }
    }
}
