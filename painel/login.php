<?php
declare(strict_types=1);
session_start();

// Se já logado, vai pro painel
if (!empty($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit;
}

require_once dirname(__DIR__) . '/db.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        try {
            $stmt = db()->prepare('SELECT id, password FROM admin_users WHERE username = ?');
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_user']      = $username;
                header('Location: index.php');
                exit;
            }
        } catch (PDOException $e) {
            error_log('Admin login: ' . $e->getMessage());
        }
        $erro = 'Usuário ou senha incorretos.';
    } else {
        $erro = 'Preencha usuário e senha.';
    }
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Admin – Rallye do Sol</title>
    <link rel="stylesheet" href="../inscricao-inc/css/bootstrap.min.css"/>
    <style>
        body { background:#1a1a2e; display:flex; align-items:center; justify-content:center;
               min-height:100vh; }
        .login-box { background:#fff; border-radius:10px; padding:40px; max-width:400px;
                     width:100%; box-shadow:0 10px 40px rgba(0,0,0,.4); }
        .login-box h2 { color:#c0392b; font-weight:700; margin-bottom:4px; }
        .login-box p  { color:#666; margin-bottom:24px; }
        .btn-primary  { background:#c0392b; border-color:#c0392b; }
        .btn-primary:hover { background:#a93226; border-color:#a93226; }
    </style>
</head>
<body>
<div class="login-box">
    <h2>FEPAUTO</h2>
    <p>Painel Administrativo – Rallye do Sol</p>

    <?php if ($erro): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group mb-3">
            <label>Usuário</label>
            <input type="text" name="username" class="form-control"
                   autofocus required maxlength="50"/>
        </div>
        <div class="form-group mb-3">
            <label>Senha</label>
            <input type="password" name="password" class="form-control" required/>
        </div>
        <button type="submit" class="btn btn-primary btn-block w-100">Entrar</button>
    </form>
</div>
</body>
</html>
