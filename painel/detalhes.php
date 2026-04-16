<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php';
require_once dirname(__DIR__) . '/db.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: index.php'); exit; }

try {
    $stmt = db()->prepare('SELECT * FROM inscricoes WHERE id = ?');
    $stmt->execute([$id]);
    $ins = $stmt->fetch();
} catch (PDOException $e) {
    $ins = null;
}

if (!$ins) { header('Location: index.php?erro=' . urlencode('Inscrição não encontrada.')); exit; }

function row(string $label, mixed $value): string {
    if ($value === null || $value === '') return '';
    return "<tr>
        <th style='width:35%;background:#f8f8f8'>" . htmlspecialchars($label) . "</th>
        <td>" . htmlspecialchars((string)$value) . "</td>
    </tr>";
}
function rowBool(string $label, mixed $value): string {
    if ($value === null) return '';
    return row($label, $value ? 'Sim' : 'Não');
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Detalhes #<?= str_pad((string)$ins['id'], 6, '0', STR_PAD_LEFT) ?></title>
    <link rel="stylesheet" href="../inscricao-inc/css/bootstrap.min.css"/>
    <style>
        body { background:#f4f6f9; }
        .navbar-admin { background:#1a1a2e; padding:12px 24px; }
        .navbar-admin a { color:#fff; text-decoration:none; }
        .card { border-radius:8px; padding:24px; background:#fff; margin-bottom:20px; }
        .badge-pendente  { background:#e67e22; color:#fff; padding:4px 12px; border-radius:12px; }
        .badge-pago      { background:#27ae60; color:#fff; padding:4px 12px; border-radius:12px; }
        .badge-cancelado { background:#c0392b; color:#fff; padding:4px 12px; border-radius:12px; }
    </style>
</head>
<body>
<nav class="navbar-admin d-flex justify-content-between align-items-center">
    <span><strong>FEPAUTO</strong> – Detalhes da Inscrição</span>
    <a href="index.php">← Voltar</a>
</nav>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Inscrição #<?= str_pad((string)$ins['id'], 6, '0', STR_PAD_LEFT) ?></h3>
        <span class="badge-<?= $ins['status_pagamento'] ?>" style="font-size:16px">
            <?= strtoupper($ins['status_pagamento']) ?>
        </span>
    </div>

    <div class="card">
        <h5>Dados Pessoais</h5>
        <table class="table table-bordered">
            <?= row('Nome', $ins['nome']) ?>
            <?= row('CPF', $ins['cpf']) ?>
            <?= row('RG', $ins['rg']) ?>
            <?= row('Data de Nascimento', $ins['dt_nascimento'] ? date('d/m/Y', strtotime($ins['dt_nascimento'])) : '') ?>
            <?= row('Nome do Pai', $ins['nome_pai']) ?>
            <?= row('Nome da Mãe', $ins['nome_mae']) ?>
            <?= row('Tipo Sanguíneo', $ins['tipo_sangue']) ?>
        </table>
    </div>

    <div class="card">
        <h5>Endereço e Contato</h5>
        <table class="table table-bordered">
            <?= row('CEP', $ins['cep']) ?>
            <?= row('Endereço', $ins['endereco']) ?>
            <?= row('Bairro', $ins['bairro']) ?>
            <?= row('Cidade', $ins['cidade']) ?>
            <?= row('Estado', $ins['estado']) ?>
            <?= row('E-mail', $ins['email']) ?>
            <?= row('Telefone/WhatsApp', $ins['telefone']) ?>
        </table>
    </div>

    <div class="card">
        <h5>Dados do Veículo</h5>
        <table class="table table-bordered">
            <?= row('Veículo', $ins['veiculo']) ?>
            <?= row('Categoria', $ins['categoria']) ?>
            <?= row('Já participou do Rallye?', $ins['participacao'] ? 'Sim' : 'Não') ?>
            <?= rowBool('Possui Carteira da Federação', $ins['possui_carteira']) ?>
            <?= rowBool('Carteira Válida para 2026', $ins['carteira_valida']) ?>
            <?= row('Nº Carteira', $ins['num_carteira']) ?>
            <?= row('Especificação Carro', $ins['especificar_carro']) ?>
            <?= row('Especificação Moto', $ins['especificar_moto']) ?>
            <?= row('Especificação Moto/Renovação', $ins['especificar_moto_renovacao']) ?>
        </table>
    </div>

    <?php if ($ins['veiculo'] === 'Carro' && $ins['navegador_nome']): ?>
    <div class="card">
        <h5>Dados do Navegador</h5>
        <table class="table table-bordered">
            <?= row('Nome', $ins['navegador_nome']) ?>
            <?= row('RG', $ins['navegador_rg']) ?>
            <?= row('Tipo Sanguíneo', $ins['tipo_sangue_navegador']) ?>
        </table>
    </div>
    <?php endif; ?>

    <div class="card">
        <h5>Pagamento</h5>
        <table class="table table-bordered">
            <?= row('Valor', 'R$ ' . number_format((float)$ins['valor'], 2, ',', '.')) ?>
            <?= row('Status', strtoupper($ins['status_pagamento'])) ?>
            <?= row('Código PagSeguro', $ins['pagseguro_code']) ?>
            <?= row('Transaction ID', $ins['pagseguro_transaction_id']) ?>
            <?= row('Data de Inscrição', date('d/m/Y H:i:s', strtotime($ins['created_at']))) ?>
            <?= row('Última Atualização', date('d/m/Y H:i:s', strtotime($ins['updated_at']))) ?>
        </table>
    </div>

    <!-- Ações rápidas -->
    <div class="card">
        <h5>Alterar Status</h5>
        <div class="d-flex gap-2">
            <?php foreach (['pago' => 'success', 'pendente' => 'warning', 'cancelado' => 'danger'] as $st => $cor): ?>
                <?php if ($ins['status_pagamento'] !== $st): ?>
                <form method="POST" action="atualiza-status.php"
                      onsubmit="return confirm('Alterar status para <?= strtoupper($st) ?>?')">
                    <input type="hidden" name="id" value="<?= $ins['id'] ?>"/>
                    <input type="hidden" name="status" value="<?= $st ?>"/>
                    <input type="hidden" name="redirect" value="detalhes.php?id=<?= $ins['id'] ?>"/>
                    <button type="submit" class="btn btn-<?= $cor ?>" style="margin-right:8px">
                        Marcar como <?= strtoupper($st) ?>
                    </button>
                </form>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script src="../inscricao-inc/js/jquery.min.js"></script>
<script src="../inscricao-inc/js/bootstrap.min.js"></script>
</body>
</html>
