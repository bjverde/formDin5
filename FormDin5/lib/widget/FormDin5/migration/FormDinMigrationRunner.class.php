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

    /** SGBD padrão assumido para migrations legadas que não sobrescrevem getSupportedDbms() */
    private ?string $defaultLegacyDbms = null;

    /**
     * Construtor da classe FormDinMigrationRunner.
     *
     * @param string|null $migrationsPath Caminho do diretório contendo as migrations (default: PATH/app/database/migrations)
     * @param string|null $trackingDb Nome da conexão onde a tabela db_migrations é mantida (default: 'permission')
     */
    public function __construct(?string $migrationsPath = null, ?string $trackingDb = null)
    {
        $this->setMigrationsPath($migrationsPath);
        $this->setTrackingDb($trackingDb);
    }

    /**
     * Retorna o banco onde a tabela db_migrations é mantida.
     *
     * @return string Nome/alias da conexão de rastreamento
     */
    public function getTrackingDb(): string
    {
        return $this->trackingDb;
    }

    /**
     * Define o banco onde a tabela db_migrations é mantida.
     * Regra: parâmetro -> application.ini -> fallback 'permission'
     *
     * @param string|null $trackingDb Nome da conexão de rastreamento
     * @return void
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
     * Define o SGBD padrão para migrations legadas que não sobrescrevem getSupportedDbms().
     * Exemplo: $runner->setDefaultLegacyDbms(FormDinMigration::DBMS_MYSQL);
     *
     * @param string|null $dbms Identificador do SGBD padrão (ex: 'mysql') ou null para desabilitar
     * @return void
     */
    public function setDefaultLegacyDbms(?string $dbms): void
    {
        $this->defaultLegacyDbms = $dbms !== null ? strtolower(trim($dbms)) : null;
    }

    /**
     * Retorna o SGBD padrão para migrations legadas.
     *
     * @return string|null Identificador do SGBD configurado ou null se não definido
     */
    public function getDefaultLegacyDbms(): ?string
    {
        return $this->defaultLegacyDbms;
    }

    /**
     * Retorna a lista de SGBDs suportados pela migration, aplicando o default legado se configurado.
     *
     * @param FormDinMigration $migration Instância da migration
     * @return string[] Lista de drivers suportados
     */
    public function getMigrationSupportedDbms(FormDinMigration $migration): array
    {
        if ($this->defaultLegacyDbms !== null) {
            $reflector = new \ReflectionMethod($migration, 'getSupportedDbms');
            if ($reflector->getDeclaringClass()->getName() === FormDinMigration::class) {
                return [$this->defaultLegacyDbms];
            }
        }

        return (array) $migration->getSupportedDbms();
    }

    /**
     * Verifica se a migration é suportada no banco de dados alvo.
     *
     * @param FormDinMigration $migration Instância da migration
     * @param string|null $targetDbms Identificador do driver alvo (default: obtido do banco da migration)
     * @return bool True se o SGBD for compatível com a migration, false caso contrário
     */
    public function isMigrationSupported(FormDinMigration $migration, ?string $targetDbms = null): bool
    {
        $targetDbms = $targetDbms ?: $this->getDatabaseType($migration->getDatabase());
        $supported = $this->getMigrationSupportedDbms($migration);

        foreach ($supported as $item) {
            $normalized = $migration->normalizeDriverName((string) $item);
            if ($normalized === '*' || $normalized === 'all' || $normalized === $targetDbms) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retorna o diretório onde ficam os arquivos de migration.
     *
     * @return string Caminho absoluto do diretório de migrations
     */
    public function getMigrationsPath(): string
    {
        return $this->migrationsPath;
    }

    /**
     * Define o diretório onde ficam os arquivos de migration.
     * Regra: parâmetro -> PATH/app/database/migrations
     * Valor Default: PATH/app/database/migrations
     *
     * @param string|null $migrationsPath Caminho relativo ou absoluto para as migrations
     * @return void
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

    // -------------------------------------------------------------------------
    // API pública
    // -------------------------------------------------------------------------

    /**
     * Executa todas as migrations pendentes em ordem.
     *
     * @return void
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
     *
     * @return void
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

            // Se ainda não foi executada, verifica se o banco configurado é suportado
            if (!$row) {
                $targetDbms = $this->getDatabaseType($m->getDatabase());
                if (!$this->isMigrationSupported($m, $targetDbms)) {
                    $status = 'incompativel';
                }
            }

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
     * @return void
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
     * Reverte todas as migrations executadas até atingir a versão alvo especificada,
     * deixando a versão alvo como a última versão ativa no banco de dados.
     *
     * As migrations superiores à versão alvo têm seu método down() executado em
     * ordem decrescente (cronológica inversa). Se a reversão for bem-sucedida, o
     * registro de cada uma é removido da tabela de controle (db_migrations), fazendo
     * com que voltem ao status 'pendente' e fiquem disponíveis para uma nova aplicação via run().
     *
     * -------------------------------------------------------------------------
     * Exemplo Detalhado de Uso:
     * -------------------------------------------------------------------------
     * Suponha que o banco esteja atualmente na versão V015 (com V001 até V015
     * executadas com sucesso registradas em db_migrations):
     *
     *   $runner = new FormDinMigrationRunner();
     *   $runner->rollbackTo('V010');
     *
     * Fluxo de Execução:
     *   1. O runner identifica as migrations ativas com versão estritamente maior que 'V010':
     *      [V015, V014, V013, V012, V011].
     *   2. Executa o rollback em ordem cronológica inversa:
     *      - V015: executa down() -> commit -> remove V015 de db_migrations
     *      - V014: executa down() -> commit -> remove V014 de db_migrations
     *      - V013: executa down() -> commit -> remove V013 de db_migrations
     *      - V012: executa down() -> commit -> remove V012 de db_migrations
     *      - V011: executa down() -> commit -> remove V011 de db_migrations
     *   3. A migration alvo 'V010' NÃO é revertida; ela permanece intacta e ativa.
     *
     * Estado Resultante:
     *   - Versão corrente ativa do banco: V010.
     *   - Ao chamar $runner->status():
     *       V001 a V010 : status 'success'
     *       V011 a V015 : status 'pendente' (registro removido de db_migrations)
     *   - Ao chamar $runner->run() posteriormente:
     *       O runner reaplicará V011, V012, V013, V014 e V015 via up() normalmente.
     *
     * Observações:
     *   - Para reverter TODAS as migrations do sistema (zerar o banco), utilize
     *     $runner->rollbackTo('V000') ou o atalho $runner->reset().
     *   - Se $targetVersion for igual ou superior à versão atual executada, nenhuma ação é realizada.
     *
     * @param string $targetVersion Versão alvo limite (ex: 'V010' para parar nela, ou 'V000' para reverter todas)
     * @return void
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
     * Reverte todas as migrations executadas no sistema (atalho para rollbackTo('V000')).
     *
     * @return void
     */
    public function reset(): void
    {
        $this->rollbackTo('V000');
    }

    /**
     * Retorna a lista de instâncias das migrations já executadas com sucesso,
     * em ordem decrescente de versão.
     *
     * @return FormDinMigration[] Lista de instâncias de migration em ordem decrescente
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
     * Normaliza os diferentes nomes de driver/SGBD para um padrão consistente.
     *
     * @param string $driver Nome ou alias do driver recebido
     * @return string Nome padronizado do driver (mysql, pgsql, sqlite, sqlsrv, oci, firebird)
     */
    public function normalizeDriverName(string $driver): string
    {
        $driver = strtolower(trim($driver));

        return match ($driver) {
            'sqlite', 'sqlite3'                       => FormDinMigration::DBMS_SQLITE,
            'pgsql', 'postgres', 'postgresql'         => FormDinMigration::DBMS_POSTGRES,
            'sqlsrv', 'mssql', 'sqlserver'            => FormDinMigration::DBMS_SQLSERVER,
            'mysql', 'mariadb'                        => FormDinMigration::DBMS_MYSQL,
            'oci', 'oci8', 'oracle', 'pdo_oci'        => FormDinMigration::DBMS_ORACLE,
            'firebird', 'ibase', 'interbase', 'fbird' => FormDinMigration::DBMS_FIREBIRD,
            default                                   => $driver,
        };
    }

    /**
     * Identifica o tipo de SGBD (driver) da conexão informada de forma normalizada.
     * Retorna um dos identificadores padronizados: 'mysql', 'pgsql', 'sqlite', 'sqlsrv', 'oci', 'firebird'.
     *
     * @param string|null $database Nome da conexão (default: trackingDb)
     * @param PDO|null $conn Conexão PDO ativa opcional
     * @return string Identificador normalizado do driver
     */
    public function getDatabaseType(?string $database = null, ?PDO $conn = null): string
    {
        $database = $database ?: $this->getTrackingDb();

        if (class_exists(\Adianti\Database\TConnection::class)) {
            try {
                $info = \Adianti\Database\TConnection::getDatabaseInfo($database);
                if (!empty($info['type'])) {
                    return $this->normalizeDriverName($info['type']);
                }
            } catch (\Throwable $e) {
                // fallback para PDO
            }
        }

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

        return FormDinMigration::DBMS_MYSQL;
    }

    /**
     * Retorna o comando SQL DDL para criar a tabela de controle db_migrations
     * de acordo com o SGBD configurado.
     *
     * Suporta: MySQL/MariaDB, PostgreSQL, SQLite, SQL Server, Oracle e Firebird/Interbase.
     *
     * @param string $dbType Identificador do SGBD ('mysql', 'pgsql', 'sqlite', 'sqlsrv', 'oci', 'firebird')
     * @return string Instrução DDL CREATE TABLE compatível com o SGBD
     */
    public function getCreateTableSql(string $dbType): string
    {
        $dbType = $this->normalizeDriverName($dbType);

        switch ($dbType) {
            case FormDinMigration::DBMS_SQLITE:
                return "CREATE TABLE IF NOT EXISTS db_migrations (
                    id             INTEGER PRIMARY KEY AUTOINCREMENT,
                    script_id      VARCHAR(50)  NOT NULL UNIQUE,
                    description    VARCHAR(255) NOT NULL DEFAULT '',
                    db_name        VARCHAR(100) NOT NULL DEFAULT '',
                    system_version VARCHAR(20)  NOT NULL DEFAULT '',
                    executed_at    TEXT         NOT NULL,
                    status         TEXT         NOT NULL CHECK (status IN ('success','error'))
                )";

            case FormDinMigration::DBMS_POSTGRES:
                return "CREATE TABLE IF NOT EXISTS db_migrations (
                    id             SERIAL PRIMARY KEY,
                    script_id      VARCHAR(50)  NOT NULL UNIQUE,
                    description    VARCHAR(255) NOT NULL DEFAULT '',
                    db_name        VARCHAR(100) NOT NULL DEFAULT '',
                    system_version VARCHAR(20)  NOT NULL DEFAULT '',
                    executed_at    TIMESTAMP    NOT NULL,
                    status         VARCHAR(10)  NOT NULL CHECK (status IN ('success','error'))
                )";

            case FormDinMigration::DBMS_SQLSERVER:
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

            case FormDinMigration::DBMS_ORACLE:
                return "BEGIN
    EXECUTE IMMEDIATE 'CREATE TABLE db_migrations (
        id             NUMBER GENERATED BY DEFAULT AS IDENTITY PRIMARY KEY,
        script_id      VARCHAR2(50)  NOT NULL UNIQUE,
        description    VARCHAR2(255) DEFAULT NULL,
        db_name        VARCHAR2(100) DEFAULT NULL,
        system_version VARCHAR2(20)  DEFAULT NULL,
        executed_at    TIMESTAMP     NOT NULL,
        status         VARCHAR2(10)  NOT NULL CHECK (status IN (''success'',''error''))
    )';
EXCEPTION
    WHEN OTHERS THEN
        IF SQLCODE != -955 THEN
            RAISE;
        END IF;
END;";

            case FormDinMigration::DBMS_FIREBIRD:
                return "EXECUTE BLOCK AS
BEGIN
    IF (NOT EXISTS(SELECT 1 FROM rdb\$relations WHERE TRIM(rdb\$relation_name) = 'DB_MIGRATIONS')) THEN
        EXECUTE STATEMENT 'CREATE TABLE db_migrations (
            id             INTEGER GENERATED BY DEFAULT AS IDENTITY PRIMARY KEY,
            script_id      VARCHAR(50)  NOT NULL UNIQUE,
            description    VARCHAR(255) DEFAULT '''' NOT NULL,
            db_name        VARCHAR(100) DEFAULT '''' NOT NULL,
            system_version VARCHAR(20)  DEFAULT '''' NOT NULL,
            executed_at    TIMESTAMP    NOT NULL,
            status         VARCHAR(10)  NOT NULL CHECK (status IN (''success'',''error''))
        )';
END";

            case FormDinMigration::DBMS_MYSQL:
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
     *
     * @return void
     * @throws \Throwable Se ocorrer erro na criação da tabela
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
     * @return array<string, array{script_id: string, status: string, executed_at: string}> Mapa indexado por script_id
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
     * Escaneia a pasta e retorna metadados de todos os arquivos V*.php em ordem lexicográfica.
     *
     * @return array<int, array{version: string, class: string, file: string}> Lista de metadados dos arquivos de migration
     * @throws \InvalidArgumentException Se o diretório de migrations não existir
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

    /**
     * Retorna as configurações da aplicação a partir de AdiantiApplicationConfig.
     *
     * @return array|null Array com as configurações do application.ini ou null se indisponível
     */
    public function getInfoApplication(): array|null
    {
        $ini = class_exists(\Adianti\Core\AdiantiApplicationConfig::class)
            ? \Adianti\Core\AdiantiApplicationConfig::get()
            : null;
        return $ini;
    }

    /**
     * Retorna o título da aplicação configurado em application.ini.
     *
     * @return string Título da aplicação
     */
    public function getTitleApplication(): string
    {
        $ini = $this->getInfoApplication();
        return (string) ($ini['general']['title'] ?? '');
    }

    /**
     * Retorna a versão corrente do sistema configurada em application.ini (ex: '1.0.0').
     *
     * @return string Versão do sistema
     */
    public function getSystemVersion(): string
    {
        $ini = $this->getInfoApplication();
        return (string) ($ini['system']['system_version'] ?? '0.0.0');
    }

    /**
     * Retorna o título concatenado com a versão do sistema.
     *
     * @return string Título e versão (ex: 'MeuApp 1.0.0')
     */
    public function getTitleVersion(): string
    {
        return $this->getTitleApplication() . ' ' . $this->getSystemVersion();
    }    

    /**
     * Retorna a versão corrente do sistema a partir de application.ini.
     *
     * @return string Versão semântica corrente do sistema
     */
    private function getCurrentSystemVersion(): string
    {
        return $this->getSystemVersion();
    }

    /**
     * Verifica se a versão da migration é compatível com a versão corrente do sistema.
     * Retorna false (e imprime aviso) se a migration exige uma versão ainda não implantada.
     *
     * @param FormDinMigration $migration Instância da migration a ser verificada
     * @return bool True se for compatível com a versão do sistema, false caso contrário
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
     * @return FormDinMigration[] Lista de instâncias de migration pendentes
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
     *
     * @param string $version Identificador da versão (ex: 'V001')
     * @param string $desc Descrição da migration
     * @param string $database Nome do banco onde atuou
     * @param string $systemVersion Versão do sistema da migration
     * @param string $status Status da execução ('success' ou 'error')
     * @return void
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
     *
     * @return void
     * @throws \Throwable Se ocorrer erro inesperado no fechamento de transação ativa
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
     *
     * @return void
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
     *
     * @param FormDinMigration $migration Instância da migration a ser executada
     * @return void
     * @throws \Throwable Relança a exceção para interromper o runner em caso de erro
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

        $targetDbms = $this->getDatabaseType($database);
        if (!$this->isMigrationSupported($migration, $targetDbms)) {
            $supportedList = implode(', ', $this->getMigrationSupportedDbms($migration));
            echo "  -> IGNORADA: migration desenvolvida para [{$supportedList}],"
                . " incompativel com o banco '{$database}' ({$targetDbms}).\n\n";
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
     *
     * @param string $version Identificador da versão a ser removida (ex: 'V001')
     * @return void
     * @throws \Throwable Se ocorrer erro ao fechar transação no banco de rastreamento
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
     *
     * @param FormDinMigration $migration Instância da migration a ser revertida
     * @return void
     * @throws \Throwable Relança a exceção em caso de falha na reversão
     */
    private function executeDown(FormDinMigration $migration): void
    {
        $version  = $migration->getVersion();
        $desc     = $migration->getDescription();
        $database = $migration->getDatabase();

        echo "[{$version}] Revertendo: {$desc}\n";

        $targetDbms = $this->getDatabaseType($database);
        if (!$this->isMigrationSupported($migration, $targetDbms)) {
            $supportedList = implode(', ', $this->getMigrationSupportedDbms($migration));
            echo "  -> IGNORADA: migration desenvolvida para [{$supportedList}],"
                . " incompativel com o banco '{$database}' ({$targetDbms}).\n\n";
            return;
        }

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
