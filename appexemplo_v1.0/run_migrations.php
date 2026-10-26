<?php
/**
 * Entry point para execução e gerenciamento de migrations do FormDin 5.
 *
 * INSTALAÇÃO EM UM NOVO PROJETO:
 *   Copie este arquivo da pasta do FormDin5 para a raiz do seu projeto Adianti:
 *   copy app/lib/widget/FormDin5/migration/run_migrations.php run_migrations.php
 *
 * VIA CLI (Terminal — sem necessidade de token):
 *   php run_migrations.php                  (executa migrations pendentes)
 *   php run_migrations.php --status         (ou -s: lista o status de cada migration)
 *   php run_migrations.php --rollback [N]   (ou -r: reverte as últimas N migrations, default: 1)
 *   php run_migrations.php --to=V010        (reverte até atingir a versão alvo)
 *   php run_migrations.php --reset          (reverte todas as migrations cadastradas)
 *   php run_migrations.php --db=crm_db      (especifica o banco de rastreamento)
 *   php run_migrations.php --help           (ou -h: exibe menu de ajuda)
 *
 * VIA WEB (Navegador — usuário admin logado + token de confirmação em application.ini):
 *   Acesse run_migrations.php no navegador, informe o token e execute ações.
 *
 * LOCALIZAÇÃO DAS MIGRATIONS:
 *   As migrations ficam em app/database/migrations/V*.php e são executadas
 *   em ordem lexicográfica (V001, V002, ...).
 */

// Localiza o init.php do Adianti Framework
$initCandidates = [
    __DIR__ . '/init.php',
    __DIR__ . '/../../../../../init.php',
    getcwd() . '/init.php',
];
$initFound = null;
foreach ($initCandidates as $candidate) {
    if (file_exists($candidate)) {
        $initFound = realpath($candidate);
        break;
    }
}

if (!$initFound) {
    echo "ERRO: O arquivo 'init.php' do Adianti Framework nao foi encontrado.\n";
    echo "Execute este script a partir da raiz da aplicacao ou copie-o para a raiz.\n";
    exit(1);
}

require_once $initFound;
$projectRoot = dirname($initFound);

// Garante o carregamento da classe do FormDin5 caso não esteja no classmap
if (!class_exists('FormDinMigrationRunner')) {
    $fallbackPath = $projectRoot . '/app/lib/widget/FormDin5/migration/FormDinMigrationRunner.class.php';
    if (file_exists($fallbackPath)) {
        require_once $fallbackPath;
    }
}

$isCli = (php_sapi_name() === 'cli');

// ------------------------------------------------------------------
// Fluxo CLI — Execução direta via terminal
// ------------------------------------------------------------------
if ($isCli) {
    $args = $argv ?? [];

    // Ajuda CLI
    if (in_array('--help', $args) || in_array('-h', $args)) {
        echo "\n=== FormDin 5 Migration CLI Runner v" . FormDinMigrationRunner::MIGRATION_VERSION . " ===\n\n";
        echo "Uso:\n";
        echo "  php run_migrations.php [opcoes]\n\n";
        echo "Opcoes:\n";
        echo "  --status, -s               Exibe o status de cada migration no banco\n";
        echo "  --run                      Aplica todas as migrations pendentes (padrao)\n";
        echo "  --rollback [N], -r [N]     Reverte as ultimas N migrations (padrao: 1)\n";
        echo "  --to=VERSAO                Reverte ate a versao informada (ex: --to=V010)\n";
        echo "  --reset                    Reverte TODAS as migrations aplicadas\n";
        echo "  --db=NOME                  Define o banco de conexao de rastreamento\n";
        echo "  --legacy-dbms=DRIVER       Define o SGBD padrao para migrations legadas (ex: mysql)\n";
        echo "  --help, -h                 Exibe esta ajuda\n\n";
        exit(0);
    }

    // Identifica banco customizado via --db=nome
    $customDb = null;
    $legacyDbms = null;
    $targetTo = null;
    $rollbackSteps = null;
    $isRollback = false;
    $isReset = in_array('--reset', $args);
    $showStatus = in_array('--status', $args) || in_array('-s', $args);

    foreach ($args as $index => $arg) {
        if (str_starts_with($arg, '--db=')) {
            $customDb = substr($arg, 5);
        }
        if (str_starts_with($arg, '--legacy-dbms=')) {
            $legacyDbms = substr($arg, 14);
        }
        if (str_starts_with($arg, '--to=')) {
            $targetTo = substr($arg, 5);
        }
        if ($arg === '--rollback' || $arg === '-r') {
            $isRollback = true;
            $nextArg = $args[$index + 1] ?? null;
            if ($nextArg !== null && is_numeric($nextArg)) {
                $rollbackSteps = (int) $nextArg;
            } else {
                $rollbackSteps = 1;
            }
        }
    }

    try {
        $runner = new FormDinMigrationRunner(null, $customDb);
        if ($legacyDbms) {
            $runner->setDefaultLegacyDbms($legacyDbms);
        }

        echo "=== " . $runner->getTitleVersion() . " [Migrations v" . FormDinMigrationRunner::MIGRATION_VERSION . "] ===\n";
        echo "Banco de Rastreamento: " . $runner->getTrackingDb() . "\n\n";

        if ($showStatus) {
            echo "--- Status das migrations ---\n\n";
            $runner->status();
        } elseif ($isReset) {
            echo "--- Revertendo TODAS as migrations (Reset) ---\n\n";
            $runner->reset();
        } elseif ($targetTo !== null) {
            echo "--- Revertendo migrations ate a versao '{$targetTo}' ---\n\n";
            $runner->rollbackTo($targetTo);
        } elseif ($isRollback) {
            echo "--- Revertendo ultimas {$rollbackSteps} migration(s) ---\n\n";
            $runner->rollback($rollbackSteps);
        } else {
            echo "--- Executando migrations pendentes ---\n\n";
            $runner->run();
        }

        exit(0);
    } catch (\Throwable $e) {
        echo "\nERRO FATAL: " . $e->getMessage() . "\n";
        if (defined('DEBUG') && DEBUG) {
            echo "\nTrace:\n" . $e->getTraceAsString() . "\n";
        }
        exit(1);
    }
}

// ------------------------------------------------------------------
// Fluxo WEB — Autenticação de Sessão e Token
// ------------------------------------------------------------------
new TSession;

if (!TSession::getValue('logged')) {
    header('Location: index.php?class=LoginForm');
    exit;
}

// Valida permissão de usuário (admin ou login em sessao)
$currentLogin = (string) TSession::getValue('login');
if ($currentLogin !== 'admin') {
    http_response_code(403);
    renderPage('Acesso Negado', '<p class="msg-error">Somente o usuário <strong>admin</strong> pode gerenciar migrations.</p>');
    exit;
}

// Configurações do application.ini
$ini           = AdiantiApplicationConfig::get();
$expectedToken = (string) ($ini['general']['token'] ?? '');
$trackingDb    = (string) ($ini['general']['tracking_db'] ?? $ini['system']['tracking_db'] ?? FormDinMigrationRunner::DEFAULT_TRACKING_DB);

if (empty($expectedToken)) {
    http_response_code(500);
    renderPage('Configuração Pendente', '
        <p class="msg-error">A chave <strong>general[token]</strong> não está configurada no seu <code>application.ini</code>.</p>
        <p>Defina um token secreto para autorizar operações de migration via interface web.</p>
        <p><a class="btn btn-secondary" href="index.php">Voltar ao sistema</a></p>
    ');
    exit;
}

$isAuthSession  = (bool) TSession::getValue('formdin_migration_authorized');
$submittedToken = (string) ($_POST['migration_token'] ?? '');
$action         = (string) ($_POST['action'] ?? '');

// Se ainda não autenticado e submeteu token, valida
if (!$isAuthSession && !empty($submittedToken)) {
    if (hash_equals($expectedToken, $submittedToken)) {
        TSession::setValue('formdin_migration_authorized', true);
        $isAuthSession = true;
    } else {
        renderForm('Token inválido. Verifique o valor configurado em application.ini.');
        exit;
    }
}

// Se não está autenticado, exibe formulário de token
if (!$isAuthSession) {
    renderForm();
    exit;
}

// Processa logout do módulo de migrations
if ($action === 'logout') {
    TSession::setValue('formdin_migration_authorized', false);
    header('Location: run_migrations.php');
    exit;
}

// Execução da Ação Solicitada
ob_start();
try {
    $runner = new FormDinMigrationRunner(null, $trackingDb);

    switch ($action) {
        case 'run':
            $runner->run();
            $pageTitle = 'Executar Migrations';
            break;

        case 'rollback':
            $steps = max(1, (int) ($_POST['rollback_steps'] ?? 1));
            $runner->rollback($steps);
            $pageTitle = "Rollback ({$steps} passo)";
            break;

        case 'status':
        default:
            $runner->status();
            $pageTitle = 'Status das Migrations';
            break;
    }
} catch (\Throwable $e) {
    ob_end_clean();
    renderPage('Erro na Migration',
        '<p class="msg-error">Erro: ' . htmlspecialchars($e->getMessage()) . '</p>'
        . '<p><a class="btn btn-secondary" href="run_migrations.php">Voltar</a></p>',
        500
    );
    exit;
}

$output = ob_get_clean();

// Renderiza Painel de Controle Principal
renderPage($pageTitle, '
    <div class="toolbar">
        <form method="post" style="display:inline">
            <input type="hidden" name="action" value="status">
            <button type="submit" class="btn btn-secondary">🔍 Ver Status</button>
        </form>
        <form method="post" style="display:inline" onsubmit="return confirm(\'Confirma execução de todas as migrations pendentes?\')">
            <input type="hidden" name="action" value="run">
            <button type="submit" class="btn btn-primary">⚡ Executar Pendentes</button>
        </form>
        <form method="post" style="display:inline" onsubmit="return confirm(\'ATENÇÃO: Confirma reversão (rollback) da última migration?\')">
            <input type="hidden" name="action" value="rollback">
            <input type="hidden" name="rollback_steps" value="1">
            <button type="submit" class="btn btn-warning">⏪ Reverter 1 Passo</button>
        </form>
        <form method="post" style="display:inline">
            <input type="hidden" name="action" value="logout">
            <button type="submit" class="btn btn-danger">🔒 Bloquear</button>
        </form>
        <a class="btn btn-secondary" href="index.php">Voltar ao sistema</a>
    </div>
    <div class="meta-info">
        <strong>Banco de Rastreamento:</strong> <code>' . htmlspecialchars($trackingDb) . '</code>
    </div>
    <pre>' . htmlspecialchars($output) . '</pre>
');

// ------------------------------------------------------------------
// Funções Auxiliares de Renderização Web
// ------------------------------------------------------------------

function renderForm(string $error = ''): void
{
    $errorHtml = $error ? '<p class="msg-error">' . htmlspecialchars($error) . '</p>' : '';

    renderPage('Migrations — Confirmar Acesso', '
        ' . $errorHtml . '
        <form method="post" class="token-form">
            <label for="migration_token">Token de Confirmação (application.ini)</label>
            <input type="password" id="migration_token" name="migration_token"
                   placeholder="Informe o token configurado" autofocus autocomplete="off" required>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Entrar no Gerenciador</button>
                <a class="btn btn-secondary" href="index.php">Voltar ao sistema</a>
            </div>
        </form>
    ');
}

function renderPage(string $title, string $body, int $statusCode = 200): void
{
    $migrationVersion = FormDinMigrationRunner::MIGRATION_VERSION;
    $ini = AdiantiApplicationConfig::get();
    $titleApp = (string) ($ini['general']['title'] ?? 'Aplicação');
    $sysVersion = (string) ($ini['system']['system_version'] ?? '0.0.0');

    http_response_code($statusCode);
    echo <<<HTML
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Migrations v{$migrationVersion} — {$title}</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body        { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, monospace;
                      background: #18181b; color: #e4e4e7; padding: 2rem; margin: 0; }
        .header     { margin-bottom: 1.5rem; border-bottom: 1px solid #27272a; padding-bottom: 1rem; }
        h1          { font-size: 1.25rem; color: #38bdf8; margin: 0 0 .3rem 0; }
        .subtitle   { font-size: .85rem; color: #a1a1aa; }
        pre         { background: #09090b; border: 1px solid #27272a; padding: 1.25rem;
                      border-radius: 6px; white-space: pre-wrap; word-break: break-word;
                      line-height: 1.6; font-size: .85rem; color: #f4f4f5; }
        .token-form { background: #27272a; border: 1px solid #3f3f46; border-radius: 6px;
                      padding: 1.5rem; max-width: 480px; display: flex;
                      flex-direction: column; gap: .75rem; }
        label       { font-size: .85rem; color: #38bdf8; font-weight: 500; }
        input[type=password] { width: 100%; padding: .55rem .75rem; background: #18181b;
                               border: 1px solid #52525b; border-radius: 4px; color: #f4f4f5;
                               font-size: .95rem; }
        input[type=password]:focus { outline: none; border-color: #0284c7; }
        .form-actions { display: flex; gap: .5rem; flex-wrap: wrap; margin-top: .5rem; }
        .toolbar    { display: flex; gap: .5rem; flex-wrap: wrap; margin-bottom: 1rem; }
        .meta-info  { font-size: .85rem; color: #a1a1aa; margin-bottom: 1rem; }
        .btn        { display: inline-flex; align-items: center; padding: .45rem 1rem; border-radius: 4px;
                      font-size: .85rem; font-weight: 500; cursor: pointer; border: none;
                      text-decoration: none; transition: background .15s; }
        .btn-primary   { background: #0284c7; color: #fff; }
        .btn-primary:hover   { background: #0369a1; }
        .btn-secondary { background: #3f3f46; color: #e4e4e7; }
        .btn-secondary:hover { background: #52525b; }
        .btn-warning   { background: #d97706; color: #fff; }
        .btn-warning:hover   { background: #b45309; }
        .btn-danger    { background: #dc2626; color: #fff; }
        .btn-danger:hover    { background: #b91c1c; }
        .msg-error  { color: #f87171; font-size: .9rem; background: #450a0a;
                      border: 1px solid #7f1d1d; border-radius: 4px; padding: .65rem 1rem; }
    </style>
</head>
<body>
    <div class="header">
        <h1>FormDin 5 Migrations v{$migrationVersion} — {$title}</h1>
        <div class="subtitle">{$titleApp} (Versão do Sistema: {$sysVersion})</div>
    </div>
    {$body}
</body>
</html>
HTML;
}

