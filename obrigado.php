<?php
session_start();
require_once __DIR__ . '/config.php';
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Obrigado – <?= EVENT_NAME ?></title>
    <link rel="stylesheet" href="inscricao-inc/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="inscricao-inc/css/style.css"/>
    <link rel="stylesheet" href="inscricao-inc/css/custom.css"/>
    <style>
        .obrigado-box { max-width:600px; margin:60px auto; text-align:center;
                        background:#fff; border-radius:10px; padding:40px;
                        box-shadow:0 4px 20px rgba(0,0,0,.1); }
        .icon-ok { font-size:64px; color:#27ae60; }
    </style>
</head>
<body style="background:#f4f6f9">
<div class="container">
    <div class="obrigado-box">
        <div class="icon-ok">&#10004;</div>
        <h2 class="mt-3">Pagamento Realizado!</h2>
        <p class="lead">Obrigado por se inscrever no <strong><?= EVENT_NAME ?></strong>.</p>
        <p>
            Seu pagamento foi processado pelo PagSeguro. Em breve você receberá a
            confirmação por e-mail. Caso não receba em até 24h, verifique sua caixa de spam
            ou entre em contato: <strong>inscricao@fepauto.com.br</strong>
        </p>
        <hr/>
        <p class="text-muted small">
            FEPAUTO – Federação Paraense de Automobilismo<br/>
            CNPJ: 15.753.536/0001-55
        </p>
    </div>
</div>
</body>
</html>
