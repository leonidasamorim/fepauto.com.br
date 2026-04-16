<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

$id = (int)($_GET['id'] ?? $_SESSION['inscricao_id'] ?? 0);
if ($id <= 0) {
    header('Location: inscricao.php');
    exit;
}

// Busca inscrição
try {
    $stmt = db()->prepare('SELECT * FROM inscricoes WHERE id = ?');
    $stmt->execute([$id]);
    $ins = $stmt->fetch();
} catch (PDOException $e) {
    error_log('pagamento.php: ' . $e->getMessage());
    $ins = null;
}

if (!$ins) {
    header('Location: inscricao.php?erro=' . urlencode('Inscrição não encontrada.'));
    exit;
}

$protocolo = '#' . str_pad((string)$ins['id'], 6, '0', STR_PAD_LEFT);
$valor      = number_format((float)$ins['valor'], 2, ',', '.');
$valorPag   = number_format((float)$ins['valor'], 2, '.', '');   // formato PagSeguro

// ─── PagSeguro Checkout v2 ────────────────────────────────────────────────────
$pagseguroUrl = null;
$pagseguroErro = null;

if (!empty(PAGSEGURO_TOKEN) && $ins['status_pagamento'] === 'pendente') {
    $apiUrl = PAGSEGURO_SANDBOX
        ? 'https://ws.sandbox.pagseguro.uol.com.br/v2/checkout'
        : 'https://ws.pagseguro.uol.com.br/v2/checkout';

    $params = http_build_query([
        'email'               => PAGSEGURO_EMAIL,
        'token'               => PAGSEGURO_TOKEN,
        'currency'            => 'BRL',
        'itemId1'             => '1',
        'itemDescription1'    => 'Inscrição ' . EVENT_NAME . ' – ' . $ins['veiculo'],
        'itemAmount1'         => $valorPag,
        'itemQuantity1'       => '1',
        'reference'           => 'INSCRICAO-' . $ins['id'],
        'senderName'          => $ins['nome'],
        'senderEmail'         => $ins['email'],
        'shippingAddressRequired' => 'false',
        'redirectURL'         => 'http://' . ($_SERVER['HTTP_HOST'] ?? 'fepauto.com.br') . '/obrigado.php',
        'notificationURL'     => 'http://' . ($_SERVER['HTTP_HOST'] ?? 'fepauto.com.br') . '/pagseguro-notificacao.php',
    ]);

    $ch = curl_init($apiUrl);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $params,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded; charset=UTF-8'],
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200 && $response) {
        $xml = simplexml_load_string($response);
        $code = (string)($xml->code ?? '');
        if ($code) {
            $redirectBase = PAGSEGURO_SANDBOX
                ? 'https://sandbox.pagseguro.uol.com.br/v2/checkout/payment.html'
                : 'https://pagseguro.uol.com.br/v2/checkout/payment.html';
            $pagseguroUrl = $redirectBase . '?code=' . $code;

            // Salva o code
            db()->prepare('UPDATE inscricoes SET pagseguro_code = ? WHERE id = ?')
               ->execute([$code, $ins['id']]);
        }
    } else {
        $pagseguroErro = 'Não foi possível conectar ao PagSeguro. Tente novamente em instantes.';
        error_log("PagSeguro HTTP {$httpCode}: {$response}");
    }
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Pagamento – <?= EVENT_NAME ?></title>
    <link rel="stylesheet" href="inscricao-inc/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="inscricao-inc/css/style.css"/>
    <link rel="stylesheet" href="inscricao-inc/css/custom.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet"/>
    <style>
        .card-resumo { border:1px solid #ddd; border-radius:8px; padding:24px; background:#fff; }
        .valor-destaque { font-size:2.5rem; color:#27ae60; font-weight:700; }
        .badge-pendente  { background:#f39c12; color:#fff; padding:4px 10px; border-radius:12px; }
        .badge-pago      { background:#27ae60; color:#fff; padding:4px 10px; border-radius:12px; }
        .btn-pagseguro   { background:#009c3b; color:#fff; border:none; padding:14px 32px;
                           font-size:18px; border-radius:6px; font-weight:700; cursor:pointer; }
        .btn-pagseguro:hover { background:#007a2f; color:#fff; }
    </style>
</head>
<body>
<div class="site">
<div id="main">

    <div class="section section-bg-8">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 text-center">
                    <h2 style="color:#fff;text-shadow:2px 2px 8px #000">Pagamento da Inscrição</h2>
                    <p style="color:#eee"><?= EVENT_NAME ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="section pt-4 pb-10">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card-resumo">

                        <h3>Resumo da Inscrição</h3>
                        <table class="table table-bordered">
                            <tr><th style="width:40%">Protocolo</th><td><strong><?= $protocolo ?></strong></td></tr>
                            <tr><th>Nome</th><td><?= htmlspecialchars($ins['nome']) ?></td></tr>
                            <tr><th>CPF</th><td><?= htmlspecialchars($ins['cpf']) ?></td></tr>
                            <tr><th>Veículo</th><td><?= htmlspecialchars($ins['veiculo']) ?></td></tr>
                            <tr><th>Categoria</th><td><?= htmlspecialchars($ins['categoria']) ?></td></tr>
                            <tr><th>Status</th>
                                <td>
                                    <?php if ($ins['status_pagamento'] === 'pago'): ?>
                                        <span class="badge-pago">PAGO</span>
                                    <?php elseif ($ins['status_pagamento'] === 'cancelado'): ?>
                                        <span class="badge-pendente" style="background:#e74c3c">CANCELADO</span>
                                    <?php else: ?>
                                        <span class="badge-pendente">PENDENTE</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>

                        <div class="text-center my-4">
                            <p class="mb-1">Valor a pagar</p>
                            <div class="valor-destaque">R$ <?= $valor ?></div>
                        </div>

                        <?php if ($ins['status_pagamento'] === 'pago'): ?>
                            <div class="alert alert-success text-center">
                                <strong>Pagamento confirmado!</strong> Sua inscrição está garantida.
                            </div>

                        <?php elseif ($ins['status_pagamento'] === 'cancelado'): ?>
                            <div class="alert alert-danger text-center">
                                Esta inscrição foi cancelada. Entre em contato com a organização.
                            </div>

                        <?php else: ?>

                            <?php if ($pagseguroErro): ?>
                                <div class="alert alert-warning"><?= htmlspecialchars($pagseguroErro) ?></div>
                            <?php endif; ?>

                            <?php if ($pagseguroUrl): ?>
                                <div class="text-center">
                                    <p>Clique abaixo para finalizar o pagamento com segurança via PagSeguro:</p>
                                    <a href="<?= htmlspecialchars($pagseguroUrl) ?>"
                                       class="btn-pagseguro d-inline-block">
                                        🔒 &nbsp;Pagar com PagSeguro
                                    </a>
                                    <p class="mt-3 text-muted small">
                                        Você será redirecionado ao ambiente seguro do PagSeguro.<br/>
                                        Aceitamos cartão de crédito, boleto e Pix.
                                    </p>
                                </div>

                            <?php elseif (empty(PAGSEGURO_TOKEN)): ?>
                                <!-- Token não configurado: exibe instruções de pagamento manual -->
                                <div class="alert alert-info">
                                    <strong>Como realizar o pagamento:</strong><br/><br/>
                                    Entre em contato com a organização para receber os dados de pagamento:<br/>
                                    <ul class="mt-2">
                                        <li>E-mail: <strong>inscricao@fepauto.com.br</strong></li>
                                        <li>WhatsApp: <strong>(91) 99999-9999</strong></li>
                                    </ul>
                                    Informe seu protocolo: <strong><?= $protocolo ?></strong>
                                </div>

                            <?php else: ?>
                                <div class="text-center">
                                    <form method="GET" action="pagamento.php">
                                        <input type="hidden" name="id" value="<?= $ins['id'] ?>"/>
                                        <button type="submit" class="btn btn-warning">
                                            Tentar novamente
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>

                        <?php endif; ?>

                        <hr/>
                        <p class="text-center text-muted small">
                            Dúvidas? Entre em contato: <strong>inscricao@fepauto.com.br</strong>
                        </p>

                    </div>
                </div>
            </div>
        </div>
    </div>

</div><!-- #main -->

<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center">
                <span style="color:#fff">FEPAUTO – Federação Paraense de Automobilismo<br/>
                CNPJ: 15.753.536/0001-55</span>
            </div>
        </div>
    </div>
</footer>
<div class="copyright">
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center">
                Copyright &copy; <?= date('Y') ?> Fepauto
            </div>
        </div>
    </div>
</div>
</div>

<script src="inscricao-inc/js/jquery.min.js"></script>
<script src="inscricao-inc/js/bootstrap.min.js"></script>
</body>
</html>
