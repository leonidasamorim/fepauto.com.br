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

// ─── PagBank Checkouts API v4 ────────────────────────────────────────────────────
$pagseguroUrl  = null;
$pagseguroErro = null;

if (!empty(PAGSEGURO_TOKEN) && $ins['status_pagamento'] === 'pendente') {
    $apiUrl = PAGSEGURO_SANDBOX
        ? 'https://sandbox.api.pagseguro.com/checkouts'
        : 'https://api.pagseguro.com/checkouts';

    $host    = $_SERVER['HTTP_HOST'] ?? 'fepauto.com.br';
    $baseUrl = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http') . '://' . $host;

    $body = json_encode([
        'reference_id' => 'INSCRICAO-' . $ins['id'],
        'customer'     => [
            'name'   => $ins['nome'],
            'email'  => $ins['email'],
            'tax_id' => preg_replace('/\D/', '', $ins['cpf']),
        ],
        'items' => [[
            'reference_id' => '1',
            'name'         => mb_substr('Inscrição ' . EVENT_NAME . ' – ' . $ins['veiculo'], 0, 64),
            'quantity'     => 1,
            'unit_amount'  => (int) round((float)$ins['valor'] * 100),
        ]],
        'payment_methods' => [
            ['type' => 'CREDIT_CARD'],
            ['type' => 'DEBIT_CARD']
        ],
        'redirect_url'      => $baseUrl . '/obrigado.php',
        'notification_urls' => [$baseUrl . '/pagseguro-notificacao.php'],
    ], JSON_UNESCAPED_UNICODE);

    $ch = curl_init($apiUrl);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $body,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . PAGSEGURO_TOKEN,
            'Content-Type: application/json',
            'x-api-version: 4.0',
            'Accept: application/json',
        ],
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $data = json_decode($response, true);

    if ($httpCode === 201 && !empty($data['links'])) {
        foreach ($data['links'] as $link) {
            if (($link['rel'] ?? '') === 'PAY') {
                $pagseguroUrl = $link['href'];
                break;
            }
        }
        if ($pagseguroUrl) {
            db()->prepare('UPDATE inscricoes SET pagseguro_code = ? WHERE id = ?')
               ->execute([$data['id'] ?? '', $ins['id']]);
        } else {
            $pagseguroErro = 'Pedido criado, mas link de pagamento não encontrado na resposta.';
            error_log("PagBank sem link PAY: {$response}");
        }
    } else {
        $msgs = $data['error_messages'] ?? ($data['errors'] ?? []);
        $msg  = '';
        foreach ((array)$msgs as $e) {
            $msg .= ($e['description'] ?? $e['message'] ?? json_encode($e)) . ' ';
        }
        $pagseguroErro = trim($msg) ?: "Erro ao iniciar pagamento (HTTP {$httpCode}).";
        error_log("PagBank HTTP {$httpCode}: {$response}");
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
            <div class="row justify-content-center align-items-center">
                <div class="col-md-8 col-lg-12">
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

                        <div class="my-4">
                            <?php
                            $vInscr = (float)($ins['valor_inscricao'] ?? $ins['valor']);
                            $vCart  = (float)($ins['valor_carteira']  ?? 0);
                            ?>
                            <table class="table table-sm table-bordered mb-2">
                                <tr>
                                    <td style="width:60%">Valor da inscrição</td>
                                    <td class="text-right">R$ <?= number_format($vInscr, 2, ',', '.') ?></td>
                                </tr>
                                <?php if ($vCart > 0): ?>
                                <tr>
                                    <td>Carteira / Renovação</td>
                                    <td class="text-right">R$ <?= number_format($vCart, 2, ',', '.') ?></td>
                                </tr>
                                <?php endif; ?>
                                <tr style="background:#e8f5e9;font-weight:700">
                                    <td>Total a pagar</td>
                                    <td class="text-right" style="color:#27ae60;font-size:1.2rem">
                                        R$ <?= $valor ?>
                                    </td>
                                </tr>
                            </table>
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
                                        <li>E-mail: <strong>fepauto@fepauto.com.br</strong></li>
                                        <li>WhatsApp: <strong>(91) 3242-4185</strong></li>
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
                            Dúvidas? Entre em contato: <strong>fepauto@fepauto.com.br</strong>
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
