<?php
/**
 * Execute este arquivo UMA VEZ para criar ou redefinir o usuário admin.
 * Apague-o em seguida por segurança.
 *
 * Acesse: http://seu-site/painel/criar-admin.php
 */
declare(strict_types=1);
require_once dirname(__DIR__) . '/db.php';

$criado  = false;
$erro    = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? 'admin');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm']  ?? '';

    if (strlen($password) < 6) {
        $erro = 'A senha deve ter pelo menos 6 caracteres.';
    } elseif ($password !== $confirm) {
        $erro = 'As senhas não conferem.';
    } else {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        try {
            $stmt = db()->prepare(
                'INSERT INTO admin_users (username, password)
                 VALUES (:u, :p)
                 ON DUPLICATE KEY UPDATE password = :p2'
            );
            $stmt->execute([':u' => $username, ':p' => $hash, ':p2' => $hash]);
            $criado = true;
        } catch (PDOException $e) {
            $erro = 'Erro no banco: ' . $e->getMessage();
        }
    }
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8"/>
    <title>Criar Usuário Admin</title>
    <link rel="stylesheet" href="../inscricao-inc/css/bootstrap.min.css"/>
    <style>
        body { background:#1a1a2e; display:flex; align-items:center;
               justify-content:center; min-height:100vh; }
        .box { background:#fff; border-radius:10px; padding:36px;
               max-width:420px; width:100%; }
    </style>
</head>
<body>
<div class="box">
    <h4>Criar / Redefinir Usuário Admin</h4>
    <p class="text-muted small">
        ⚠ Apague este arquivo após usar!
    </p>

    <?php if ($criado): ?>
        <div class="alert alert-success">
            Usuário criado/atualizado com sucesso!<br/>
            <a href="login.php">Ir para o login →</a>
        </div>
    <?php elseif ($erro): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label>Usuário</label>
            <input type="text" name="username" class="form-control" value="admin" required/>
        </div>
        <div class="mb-3">
            <label>Nova Senha</label>
            <input type="password" name="password" class="form-control" required minlength="6"/>
        </div>
        <div class="mb-3">
            <label>Confirmar Senha</label>
            <input type="password" name="confirm" class="form-control" required/>
        </div>
        <button type="submit" class="btn btn-danger w-100">Criar / Atualizar Admin</button>
    </form>
</div>
</body>
</html>
