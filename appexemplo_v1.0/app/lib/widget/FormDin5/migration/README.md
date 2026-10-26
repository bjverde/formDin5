# FormDin 5 — Subsistema de Migrations de Banco de Dados

O subsistema de migrations do **FormDin 5** fornece uma solução robusta, portável e multi-SGBD para versionamento e evolução de esquemas de banco de dados no ecossistema **Adianti Framework**.

---

## 🚀 Como Utilizar em um Novo Projeto

Quando você copia a pasta `lib/widget/FormDin5` para o seu novo projeto Adianti (`app/lib/widget/FormDin5`), todo o motor de migrations já é incluído automaticamente.

### Passo 1: Copiar o Entry Point para a Raiz
Para ter acesso aos comandos de terminal e à interface web, copie o arquivo `run_migrations.php` desta pasta para a raiz do seu projeto Adianti:

```bash
# Windows (CMD / PowerShell)
copy app\lib\widget\FormDin5\migration\run_migrations.php run_migrations.php

# Linux / macOS
cp app/lib/widget/FormDin5/migration/run_migrations.php run_migrations.php
```

### Passo 2: Configuração Opcional (`application.ini` ou `application.php`)
No seu arquivo de configuração do Adianti (`app/config/application.ini` ou `application.php`), você pode definir:

```ini
[general]
; Banco de dados onde a tabela de controle (db_migrations) será criada/mantida (default: 'permission')
tracking_db = "permission"

; Token secreto obrigatório para autorizar execuções via Navegador Web
token = "seu_token_secreto_aqui"
```

### Passo 3: Criar a Pasta de Migrations
Crie o diretório onde residirão seus arquivos de migration:
```
app/database/migrations/
```

Cada migration deve seguir o padrão de nomenclatura `V001NomeDaMigration.php`, `V002OutraMigration.php`, etc.

---

## 💻 Uso via Linha de Comando (CLI)

No terminal, na raiz do projeto:

```bash
# Ajuda e listagem de todos os comandos
php run_migrations.php --help

# Exibir o status de cada migration (pendente / executada / erro)
php run_migrations.php --status

# Executar todas as migrations pendentes
php run_migrations.php

# Reverter a última migration aplicada
php run_migrations.php --rollback

# Reverter as últimas N migrations
php run_migrations.php --rollback 3

# Reverter até atingir uma versão alvo específica (ex: V010)
php run_migrations.php --to=V010

# Reverter TODAS as migrations aplicadas (Reset completo)
php run_migrations.php --reset

# Especificar um banco de dados de rastreamento customizado
php run_migrations.php --db=outro_banco --status
```

---

## 🌐 Uso via Navegador (Web)

1. Faça login na sua aplicação Adianti com um usuário que tenha login `admin`.
2. Acesse a URL: `http://localhost/seuApp/run_migrations.php`.
3. Informe o token configurado na chave `general[token]` do `application.ini`.
4. Utilize os botões para visualizar o status, executar pendentes ou reverter.

---

## 🗄️ SGBDs Suportados

O runner e os helpers de schema suportam **6 famílias de SGBDs** (além de seus derivados):

| SGBD | Identificador | DDL de Criação da Tabela |
| :--- | :--- | :--- |
| **MySQL / MariaDB** | `mysql` | `CREATE TABLE IF NOT EXISTS ... INT AUTO_INCREMENT` |
| **PostgreSQL** | `pgsql` | `CREATE TABLE IF NOT EXISTS ... SERIAL` |
| **SQLite** | `sqlite` | `CREATE TABLE IF NOT EXISTS ... INTEGER PRIMARY KEY AUTOINCREMENT` |
| **SQL Server** | `sqlsrv` | `IF OBJECT_ID('db_migrations') IS NULL CREATE TABLE ... IDENTITY` |
| **Oracle Database** | `oci` | Bloco PL/SQL com `NUMBER GENERATED AS IDENTITY` |
| **Firebird / Interbase** | `firebird` | `EXECUTE BLOCK` com `INTEGER GENERATED AS IDENTITY` |

---

## 📝 Exemplo de uma Migration Portável

Crie um arquivo em `app/database/migrations/V001CriarClientes.php`:

```php
<?php

class V001CriarClientes extends FormDinMigration
{
    public function getVersion(): string       { return 'V001'; }
    public function getDescription(): string   { return 'Cria tabela de clientes e view'; }
    public function getDatabase(): string      { return 'permission'; }
    public function getSystemVersion(): string { return '1.0.0'; }

    // SGBDs suportados: self::DBMS_ALL ('*') por padrão, ou array de SGBDs específicos
    public function getSupportedDbms(): array|string
    {
        return self::DBMS_ALL;
    }

    public function up(): void
    {
        // 1. Execução de DDL portável via Helpers de Schema
        if (!$this->tableExists('clientes')) {
            $this->executeSql("
                CREATE TABLE clientes (
                    id INTEGER PRIMARY KEY,
                    nome VARCHAR(100) NOT NULL,
                    email VARCHAR(100) NULL
                )
            ");
        }

        // 2. Adicionar coluna se não existir
        if (!$this->columnExists('clientes', 'cpf')) {
            $this->addColumn('clientes', 'cpf', 'VARCHAR(14) NULL');
        }

        // 3. Criar ou alterar View
        $this->createOrReplaceView('vw_clientes_ativos', 'SELECT id, nome, email FROM clientes');
    }

    public function down(): void
    {
        $this->dropView('vw_clientes_ativos');

        if ($this->tableExists('clientes')) {
            $this->executeSql("DROP TABLE clientes");
        }
    }
}
```

---

## 🛠️ Helpers de Schema Disponíveis na `FormDinMigration`

- `$this->tableExists(string $table)`: Verifica existência de tabela ou view no catálogo nativo do SGBD.
- `$this->columnExists(string $table, string $column)`: Verifica existência de coluna na tabela.
- `$this->indexExists(string $table, string $indexName)`: Verifica existência de índice.
- `$this->addColumn(string $table, string $column, string $type)`: Adiciona coluna com sintaxe compatível com os 6 SGBDs.
- `$this->dropColumn(string $table, string $column)`: Remove coluna portavelmente.
- `$this->dropIndex(string $table, string $indexName)`: Remove índice respeitando peculiaridades de cada SGBD.
- `$this->dropForeignKey(string $table, string $foreignKeyName)`: Remove chave estrangeira.
- `$this->createOrReplaceView(string $viewName, string $selectSql)`: Cria ou substitui view (suporta `CREATE OR ALTER VIEW` e `CREATE OR REPLACE VIEW`).
- `$this->dropView(string $viewName)`: Remove view com segurança.
- `$this->executeByDbms(array $sqlByDbms)`: Executa SQL diferenciado por driver para operações complexas (ex: `UPDATE JOIN`).
- `$this->transaction(callable $callback)`: Executa operações dentro de transação com commit e rollback automáticos.
- `$this->isMysql()`, `$this->isPostgres()`, `$this->isSqlite()`, `$this->isSqlServer()`, `$this->isOracle()`, `$this->isFirebird()`: Checagem booleana de SGBD ativo.
