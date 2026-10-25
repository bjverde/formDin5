<?php
/**
 * Entry point para execução das migrations.
 *
 * Via WEB (somente usuário admin logado + token de confirmação):
 *   Acesse run_migrations.php, informe o token do application.ini e escolha a ação.
 *
 * Via CLI (sem necessidade de token):
 *   php system/run_migrations.php
 *   php system/run_migrations.php --status
 *
 * As migrations ficam em system/migrations/V*.php e são executadas
 * em ordem lexicográfica (V001, V002, ...).
 *
 * O histórico de execuções é registrado na tabela db_migrations
 * no banco erpcertificadodigital.
 */

require_once __DIR__ . '/init.php';
require_once __DIR__ . '/app/database/migrations/Migration.php';
require_once __DIR__ . '/app/database/migrations/MigrationRunner.php';

$isCli = php_sapi_name() === 'cli';

// ------------------------------------------------------------------
// Fluxo CLI — sem autenticação, executa direto
// ------------------------------------------------------------------
if ($isCli) {
    $showStatus = in_array('--status', $argv ?? []);
    try {
        $runner = new MigrationRunner();
        if ($showStatus) {
            echo "=== Status das migrations ===\n\n";
            $runner->status();
        } else {
            echo "=== Executando migrations pendentes ===\n\n";
            $runner->run();
        }
    } catch (Exception $e) {
        echo "\nERRO: " . $e->getMessage() . "\n";
        exit(1);
    }
    exit;
}

// ------------------------------------------------------------------
// Fluxo WEB — autenticação de sessão
// ------------------------------------------------------------------
new TSession;

if (!TSession::getValue('logged')) {
    header('Location: index.php?class=LoginForm');
    exit;
}

if (TSession::getValue('login') !== 'admin') {
    http_response_code(403);
    renderPage('Acesso Negado', '<p class="msg-error">Somente o usuário <strong>admin</strong> pode executar migrations.</p>');
    exit;
}

// ------------------------------------------------------------------
// Token de confirmação — lido do application.ini
// ------------------------------------------------------------------
$ini            = AdiantiApplicationConfig::get();
$expectedToken  = $ini['general']['token'] ?? '';
$submittedToken = $_POST['migration_token'] ?? '';
$action         = $_POST['action'] ?? '';

// Exibe o formulário se o token ainda não foi submetido
if (empty($submittedToken)) {
    renderForm();
    exit;
}

// Valida o token
if (!hash_equals($expectedToken, $submittedToken)) {
    renderForm('Token inválido. Verifique o valor em application.ini.');
    exit;
}

// Token correto — executa a ação escolhida
$showStatus = ($action !== 'run');

ob_start();
try {
    $runner = new MigrationRunner();
    if ($showStatus) {
        $runner->status();
    } else {
        $runner->run();
    }
} catch (Exception $e) {
    ob_end_clean();
    renderPage('Erro na Migration',
        '<p class="msg-error">Erro: ' . htmlspecialchars($e->getMessage()) . '</p>'
        . '<p><a class="btn btn-secondary" href="run_migrations.php">Voltar</a></p>',
        500
    );
    exit;
}
$output = ob_get_clean();
$title  = $showStatus ? 'Status das Migrations' : 'Executar Migrations';

renderPage($title, '
    <div class="toolbar">
        <form method="post" style="display:inline">
            <input type="hidden" name="migration_token" value="' . htmlspecialchars($submittedToken) . '">
            <input type="hidden" name="action" value="status">
            <button type="submit" class="btn btn-secondary">Ver Status</button>
        </form>
        <form method="post" style="display:inline"
              onsubmit="return confirm(\'Confirma execução das migrations pendentes?\')">
            <input type="hidden" name="migration_token" value="' . htmlspecialchars($submittedToken) . '">
            <input type="hidden" name="action" value="run">
            <button type="submit" class="btn btn-primary">Executar Pendentes</button>
        </form>
        <a class="btn btn-secondary" href="index.php">Voltar ao sistema</a>
    </div>
    <pre>' . htmlspecialchars($output) . '</pre>
');

// ------------------------------------------------------------------
// Funções auxiliares de renderização
// ------------------------------------------------------------------

function renderForm(string $error = ''): void
{
    $errorHtml = $error
        ? '<p class="msg-error">' . htmlspecialchars($error) . '</p>'
        : '';

    renderPage('Migrations — Confirmar Acesso', '
        ' . $errorHtml . '
        <form method="post" class="token-form">
            <label for="migration_token">Token de confirmação</label>
            <input type="password" id="migration_token" name="migration_token"
                   placeholder="Cole o token do application.ini" autofocus autocomplete="off">
            <div class="form-actions">
                <button type="submit" name="action" value="status" class="btn btn-secondary">Ver Status</button>
                <button type="submit" name="action" value="run"    class="btn btn-primary"
                        onclick="return confirm(\'Confirma execução das migrations pendentes?\')">
                    Executar Pendentes
                </button>
                <a class="btn btn-secondary" href="index.php">Voltar ao sistema</a>
            </div>
        </form>
    ');
}

function renderPage(string $title, string $body, int $statusCode = 200): void
{
    $runner = new MigrationRunner();
    $migration_version = MigrationRunner::MIGRATION_VERSION;
    $titleVersion      = $runner->getTitleVersion();
    http_response_code($statusCode);
    echo <<<HTML
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Migrations v{$migration_version} — {$title}</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body        { font-family: monospace; background: #1e1e1e; color: #d4d4d4;
                      padding: 2rem; margin: 0; }
        h1          { font-size: 1.1rem; color: #9cdcfe; margin-bottom: 1.5rem; }
        pre         { background: #252526; border: 1px solid #3c3c3c; padding: 1.5rem;
                      border-radius: 4px; white-space: pre-wrap; word-break: break-word;
                      line-height: 1.6; margin-top: 1rem; }
        .token-form { background: #252526; border: 1px solid #3c3c3c; border-radius: 6px;
                      padding: 1.5rem; max-width: 520px; display: flex;
                      flex-direction: column; gap: .75rem; }
        label       { font-family: sans-serif; font-size: .85rem; color: #9cdcfe; }
        input[type=password] { width: 100%; padding: .5rem .75rem; background: #1e1e1e;
                               border: 1px solid #555; border-radius: 4px; color: #d4d4d4;
                               font-size: .95rem; font-family: monospace; }
        input[type=password]:focus { outline: none; border-color: #0e639c; }
        .form-actions { display: flex; gap: .5rem; flex-wrap: wrap; margin-top: .25rem; }
        .toolbar    { display: flex; gap: .5rem; flex-wrap: wrap; margin-bottom: 1rem; }
        .btn        { display: inline-block; padding: .4rem 1rem; border-radius: 4px;
                      font-size: .85rem; font-family: sans-serif; cursor: pointer;
                      border: none; text-decoration: none; }
        .btn-primary   { background: #0e639c; color: #fff; }
        .btn-secondary { background: #3c3c3c; color: #ccc; }
        .btn-primary:hover   { background: #1177bb; }
        .btn-secondary:hover { background: #505050; }
        .msg-error  { color: #f48771; font-family: sans-serif; font-size: .9rem;
                      background: #3a1a1a; border: 1px solid #8b2020; border-radius: 4px;
                      padding: .6rem 1rem; }
    </style>
</head>
<body>
    <h1>Migrations v{$migration_version} — {$title}</h1>
    <h3>{$titleVersion}</h3>
    {$body}
</body>
</html>
HTML;
}
