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

    /**
     * Retorna o nome real do banco de dados configurado para uma conexão.
     * Útil para referenciar tabelas de outros bancos em Foreign Keys.
     */
    protected function getDatabaseName(string $connection): string
    {
        $info = \Adianti\Database\TConnection::getDatabaseInfo($connection);
        return $info['name'] ?? $connection;
    }
}
