<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/db.php';

// Bloqueia se já existe algum admin cadastrado
$adminExiste = (int)db()->query('SELECT COUNT(*) FROM admin_users')->fetchColumn() > 0;
if ($adminExiste) {
    http_response_code(403);
    exit('Acesso negado.');
}

$criado = false;
$erro   = '';

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
                'INSERT INTO admin_users (username, password) VALUES (:u, :p)'
            );
            $stmt->execute([':u' => $username, ':p' => $hash]);
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
    <h4>Criar Usuário Admin</h4>

    <?php if ($criado): ?>
        <div class="alert alert-success">
            Usuário criado com sucesso!<br/>
            <a href="login.php">Ir para o login →</a>
        </div>
    <?php else: ?>
        <?php if ($erro): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label>Usuário</label>
                <input type="text" name="username" class="form-control" value="admin" required/>
            </div>
            <div class="mb-3">
                <label>Senha</label>
                <input type="password" name="password" class="form-control" required minlength="6"/>
            </div>
            <div class="mb-3">
                <label>Confirmar Senha</label>
                <input type="password" name="confirm" class="form-control" required/>
            </div>
            <button type="submit" class="btn btn-danger w-100">Criar Admin</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
