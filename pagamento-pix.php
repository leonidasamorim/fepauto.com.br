<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

$id = (int)($_GET['id'] ?? $_SESSION['inscricao_id'] ?? 0);
if ($id <= 0) { header('Location: inscricao.php'); exit; }

try {
    $stmt = db()->prepare('SELECT * FROM inscricoes WHERE id = ?');
    $stmt->execute([$id]);
    $ins = $stmt->fetch();
} catch (PDOException $e) {
    error_log('pagamento-pix.php: ' . $e->getMessage());
    $ins = null;
}

if (!$ins) {
    header('Location: inscricao.php?erro=' . urlencode('Inscrição não encontrada.'));
    exit;
}

$protocolo = '#' . str_pad((string)$ins['id'], 6, '0', STR_PAD_LEFT);
$valor      = number_format((float)$ins['valor'], 2, ',', '.');

// Formata chave PIX com máscara de CNPJ: 00.000.000/0000-00
$chaveRaw     = PIX_CHAVE;
$chaveFormatada = preg_replace('/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/', '$1.$2.$3/$4-$5', $chaveRaw);
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Pagamento via PIX – <?= EVENT_NAME ?></title>
    <link rel="stylesheet" href="inscricao-inc/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="inscricao-inc/css/style.css"/>
    <link rel="stylesheet" href="inscricao-inc/css/custom.css"/>
    <style>
        body { background:#f4f6f9; }
        .card-pix {
            max-width: 660px;
            margin: 40px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,.1);
            overflow: hidden;
        }
        .card-pix-header {
            background: #1a1a2e;
            color: #fff;
            padding: 20px 28px;
        }
        .card-pix-header h2 { margin: 0; font-size: 22px; }
        .card-pix-header p  { margin: 4px 0 0; color: #aaa; font-size: 14px; }
        .card-pix-body { padding: 28px; }

        .valor-box {
            background: #e8f5e9;
            border: 2px solid #27ae60;
            border-radius: 8px;
            text-align: center;
            padding: 16px;
            margin-bottom: 24px;
        }
        .valor-box small { color: #555; display: block; margin-bottom: 4px; }
        .valor-box strong { font-size: 2.2rem; color: #1b5e20; }

        .pix-box {
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .pix-chave {
            font-size: 1.4rem;
            font-weight: 700;
            letter-spacing: 1px;
            color: #1a1a2e;
            word-break: break-all;
        }
        .btn-copiar {
            background: #27ae60;
            color: #fff;
            border: none;
            padding: 8px 20px;
            border-radius: 6px;
            font-size: 15px;
            cursor: pointer;
            margin-top: 10px;
        }
        .btn-copiar:hover { background: #1e8449; }

        .steps { counter-reset: step; list-style: none; padding: 0; }
        .steps li {
            counter-increment: step;
            display: flex;
            gap: 14px;
            margin-bottom: 14px;
            align-items: flex-start;
        }
        .steps li::before {
            content: counter(step);
            background: #1a1a2e;
            color: #fff;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            flex-shrink: 0;
            font-size: 13px;
        }

        .badge-pendente  { background:#f39c12; color:#fff; padding:3px 10px; border-radius:10px; font-size:13px; }
        .badge-pago      { background:#27ae60; color:#fff; padding:3px 10px; border-radius:10px; font-size:13px; }
        .badge-cancelado { background:#e74c3c; color:#fff; padding:3px 10px; border-radius:10px; font-size:13px; }
    </style>
</head>
<body>
<div class="container">
    <div class="card-pix">
        <div class="card-pix-header">
            <h2>Pagamento via PIX</h2>
            <p><?= EVENT_NAME ?> &nbsp;·&nbsp; Protocolo <?= $protocolo ?></p>
        </div>
        <div class="card-pix-body">

            <?php if ($ins['status_pagamento'] === 'pago'): ?>
                <div class="alert alert-success text-center">
                    <strong style="font-size:18px">✅ Pagamento confirmado!</strong><br/>
                    Sua inscrição está garantida. Obrigado!
                </div>

            <?php elseif ($ins['status_pagamento'] === 'cancelado'): ?>
                <div class="alert alert-danger text-center">
                    Esta inscrição foi cancelada. Entre em contato: <strong>fepauto@fepauto.com.br</strong>
                </div>

            <?php else: ?>

                <!-- Valor -->
                <div class="valor-box">
                    <small>Valor a pagar</small>
                    <strong>R$ <?= $valor ?></strong>
                </div>

                <!-- Dados PIX -->
                <div class="pix-box">
                    <p class="mb-1 text-muted" style="font-size:13px">CHAVE PIX (CNPJ)</p>
                    <div class="pix-chave" id="pix-chave-texto"><?= htmlspecialchars($chaveFormatada) ?></div>
                    <button class="btn-copiar" id="btn-copiar"
                            data-chave="<?= htmlspecialchars($chaveRaw) ?>">
                        📋 &nbsp;Copiar chave PIX
                    </button>
                    <hr style="margin:16px 0"/>
                    <table style="width:100%;font-size:15px">
                        <tr>
                            <td style="color:#666;padding:4px 0;width:40%">Titular:</td>
                            <td><strong><?= htmlspecialchars(PIX_TITULAR) ?></strong></td>
                        </tr>
                        <tr>
                            <td style="color:#666;padding:4px 0">Banco:</td>
                            <td><?= htmlspecialchars(PIX_BANCO) ?></td>
                        </tr>
                        <tr>
                            <td style="color:#666;padding:4px 0">Valor:</td>
                            <td><strong style="color:#1b5e20">R$ <?= $valor ?></strong></td>
                        </tr>
                    </table>
                </div>

                <!-- Passo a passo -->
                <h6 style="margin-bottom:12px">Como pagar:</h6>
                <ol class="steps">
                    <li>Abra o aplicativo do seu banco e acesse a área de <strong>PIX</strong>.</li>
                    <li>Escolha <strong>Pagar / Transferir via chave PIX</strong> e cole a chave acima.</li>
                    <li>Confira os dados do beneficiário (<strong><?= htmlspecialchars(PIX_TITULAR) ?></strong>)
                        e o valor <strong>R$ <?= $valor ?></strong>.</li>
                    <li>Finalize o pagamento e <strong>salve o comprovante</strong>.</li>
                    <li>Envie o comprovante para
                        <a href="mailto:fepauto@fepauto.com.br"><strong>fepauto@fepauto.com.br</strong></a>
                        informando seu nome completo e CPF.</li>
                </ol>

                <div class="alert alert-warning mt-3" style="font-size:13px">
                    <strong>⚠ Atenção:</strong> O pagamento será confirmado manualmente pela organização
                    em até <strong>24 horas úteis</strong> após o envio do comprovante.
                    Sua vaga só estará garantida após essa confirmação.
                </div>

            <?php endif; ?>

            <!-- Resumo da inscrição -->
            <hr/>
            <h6>Resumo da inscrição</h6>
            <table class="table table-sm table-bordered">
                <tr><th style="width:40%">Protocolo</th><td><strong><?= $protocolo ?></strong></td></tr>
                <tr><th>Nome</th><td><?= htmlspecialchars($ins['nome']) ?></td></tr>
                <tr><th>CPF</th><td><?= htmlspecialchars($ins['cpf']) ?></td></tr>
                <tr><th>Veículo</th><td><?= htmlspecialchars($ins['veiculo']) ?></td></tr>
                <tr><th>Categoria</th><td><?= htmlspecialchars($ins['categoria']) ?></td></tr>
                <tr><th>Status pagamento</th>
                    <td><span class="badge-<?= $ins['status_pagamento'] ?>">
                        <?= strtoupper($ins['status_pagamento']) ?>
                    </span></td>
                </tr>
            </table>

            <p class="text-muted text-center" style="font-size:12px;margin-top:8px">
                FEPAUTO – Federação Paraense de Automobilismo &nbsp;·&nbsp; CNPJ: 15.753.536/0001-55
            </p>
        </div>
    </div>
</div>

<script src="inscricao-inc/js/jquery.min.js"></script>
<script>
document.getElementById('btn-copiar').addEventListener('click', function () {
    var chave = this.dataset.chave;
    var btn   = this;

    navigator.clipboard.writeText(chave).then(function () {
        btn.textContent = '✅  Copiado!';
        setTimeout(function () { btn.innerHTML = '📋 &nbsp;Copiar chave PIX'; }, 2500);
    }).catch(function () {
        var tmp = document.createElement('input');
        tmp.value = chave;
        document.body.appendChild(tmp);
        tmp.select();
        document.execCommand('copy');
        document.body.removeChild(tmp);
        btn.textContent = '✅  Copiado!';
        setTimeout(function () { btn.innerHTML = '📋 &nbsp;Copiar chave PIX'; }, 2500);
    });
});
</script>
</body>
</html>
