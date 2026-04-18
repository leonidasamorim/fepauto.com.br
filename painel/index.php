<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php';
require_once dirname(__DIR__) . '/db.php';

// ─── Filtros ──────────────────────────────────────────────────────────────────
$filtroStatus  = $_GET['status']  ?? '';
$filtroBusca   = trim($_GET['busca'] ?? '');
$filtroVeiculo = $_GET['veiculo'] ?? '';
$pagina        = max(1, (int)($_GET['p'] ?? 1));
$porPagina     = 100;
$offset        = ($pagina - 1) * $porPagina;

$where  = ['1=1'];
$params = [];

if ($filtroStatus !== '') {
    $where[]  = 'status_pagamento = :status';
    $params[':status'] = $filtroStatus;
}
if ($filtroVeiculo !== '') {
    $where[]  = 'veiculo = :veiculo';
    $params[':veiculo'] = $filtroVeiculo;
}
if ($filtroBusca !== '') {
    $where[]  = '(nome LIKE :busca1 OR cpf LIKE :busca2 OR email LIKE :busca3)';
    $params[':busca1'] = '%' . $filtroBusca . '%';
    $params[':busca2'] = '%' . $filtroBusca . '%';
    $params[':busca3'] = '%' . $filtroBusca . '%';
}

$whereSQL = implode(' AND ', $where);

try {
    $total = (int)db()->prepare("SELECT COUNT(*) FROM inscricoes WHERE {$whereSQL}")
                      ->execute($params) ? db()->prepare("SELECT COUNT(*) FROM inscricoes WHERE {$whereSQL}")
                      ->execute($params) : 0;

    // Recalcula porque execute() retorna bool
    $stmtCount = db()->prepare("SELECT COUNT(*) FROM inscricoes WHERE {$whereSQL}");
    $stmtCount->execute($params);
    $total = (int)$stmtCount->fetchColumn();

    $stmtList = db()->prepare(
        "SELECT id, nome, cpf, email, telefone, veiculo, categoria,
                valor, status_pagamento, created_at
         FROM inscricoes
         WHERE {$whereSQL}
         ORDER BY created_at DESC
         LIMIT :limit OFFSET :offset"
    );
    foreach ($params as $k => $v) $stmtList->bindValue($k, $v);
    $stmtList->bindValue(':limit',  $porPagina, PDO::PARAM_INT);
    $stmtList->bindValue(':offset', $offset,    PDO::PARAM_INT);
    $stmtList->execute();
    $inscricoes = $stmtList->fetchAll();

    // Totais por status
    $statsStmt = db()->query(
        "SELECT status_pagamento, COUNT(*) as qtd, SUM(valor) as total
         FROM inscricoes GROUP BY status_pagamento"
    );
    $stats = [];
    foreach ($statsStmt->fetchAll() as $row) {
        $stats[$row['status_pagamento']] = $row;
    }

    // Totais por veículo
    $veiculoRows = db()->query(
        "SELECT veiculo, COUNT(*) as qtd FROM inscricoes GROUP BY veiculo"
    )->fetchAll();
    $statsVeiculo = [];
    foreach ($veiculoRows as $row) {
        $statsVeiculo[$row['veiculo']] = (int)$row['qtd'];
    }

    // Totais por categoria (agrupado por veículo)
    $categoriaRows = db()->query(
        "SELECT veiculo, categoria, COUNT(*) as qtd
         FROM inscricoes
         GROUP BY veiculo, categoria
         ORDER BY veiculo, qtd DESC"
    )->fetchAll();
    $statsCategoria = [];
    foreach ($categoriaRows as $row) {
        $statsCategoria[$row['veiculo']][] = $row;
    }
} catch (PDOException $e) {
    error_log('Painel index: ' . $e->getMessage());
    $inscricoes = [];
    $total = 0;
    $stats = [];
}

$totalPaginas = max(1, (int)ceil($total / $porPagina));

function qStr(array $extra = []): string {
    $q = array_merge($_GET, $extra);
    return '?' . http_build_query(array_filter($q, fn($v) => $v !== ''));
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Painel Admin – Rallye do Sol</title>
    <link rel="stylesheet" href="../inscricao-inc/css/bootstrap.min.css"/>
    <style>
        body { background:#f4f6f9; font-family:Arial,sans-serif; }
        .navbar-admin { background:#1a1a2e; padding:12px 24px; }
        .navbar-admin a, .navbar-admin span { color:#fff; text-decoration:none; }
        .card-stat { border-radius:8px; padding:16px 20px; color:#fff; margin-bottom:16px; }
        .stat-pendente  { background:#e67e22; }
        .stat-pago      { background:#27ae60; }
        .stat-cancelado { background:#c0392b; }
        .badge-pendente  { background:#e67e22; color:#fff; padding:3px 8px; border-radius:10px; font-size:12px; }
        .badge-pago      { background:#27ae60; color:#fff; padding:3px 8px; border-radius:10px; font-size:12px; }
        .badge-cancelado { background:#c0392b; color:#fff; padding:3px 8px; border-radius:10px; font-size:12px; }
        .table th { background:#1a1a2e; color:#fff; }
        .btn-sm { padding:3px 8px; font-size:12px; }
        .card-veiculo { border-radius:8px; padding:16px 20px; color:#fff; margin-bottom:16px; }
        .stat-carro       { background:#2980b9; }
        .stat-moto        { background:#8e44ad; }
        .stat-quadriciclo { background:#16a085; }
        .cat-table th { font-size:12px; background:#f0f0f0; color:#333; padding:6px 10px; }
        .cat-table td { font-size:13px; padding:5px 10px; }
        .cat-table .qtd-badge { background:#1a1a2e; color:#fff; border-radius:10px;
                                padding:2px 8px; font-size:12px; font-weight:700; }
        .section-title { font-size:14px; font-weight:700; color:#555;
                         text-transform:uppercase; letter-spacing:.5px; margin-bottom:8px; }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar-admin" style="display:flex;justify-content:space-between;align-items:center">
    <span style="display:flex;align-items:center;gap:12px;line-height:1.2">
        <img src="../logo.jpeg" alt="FEPAUTO" style="height:44px;border-radius:4px;display:block"/>
        <span>
            <strong style="font-size:16px">FEPAUTO</strong><br>
            <span style="font-size:12px;color:#aaa">Painel Administrativo</span>
        </span>
    </span>
    <div style="display:flex;align-items:center;gap:16px">
        <span>Olá, <strong><?= htmlspecialchars($_SESSION['admin_user']) ?></strong></span>
        <a href="logout.php">Sair</a>
    </div>
</nav>

<div class="container-fluid mt-4">
    <br>
    <!-- Cards de resumo -->
    <div class="row">
        <div class="col-md-4">
            <div class="card-stat stat-pendente">
                <h5>Pendentes</h5>
                <h2><?= (int)($stats['pendente']['qtd'] ?? 0) ?></h2>
                <small>R$ <?= number_format((float)($stats['pendente']['total'] ?? 0), 2, ',', '.') ?></small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-stat stat-pago">
                <h5>Pagos</h5>
                <h2><?= (int)($stats['pago']['qtd'] ?? 0) ?></h2>
                <small>R$ <?= number_format((float)($stats['pago']['total'] ?? 0), 2, ',', '.') ?></small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-stat stat-cancelado">
                <h5>Cancelados</h5>
                <h2><?= (int)($stats['cancelado']['qtd'] ?? 0) ?></h2>
                <small>R$ <?= number_format((float)($stats['cancelado']['total'] ?? 0), 2, ',', '.') ?></small>
            </div>
        </div>
    </div>

    <!-- Cards por veículo -->
    <div class="row mt-2">
        <div class="col-md-4">
            <div class="card-veiculo stat-carro">
                <h5>🚗 Carro / UTV</h5>
                <h2><?= $statsVeiculo['Carro'] ?? 0 ?></h2>
                <small>inscrições</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-veiculo stat-moto">
                <h5>🏍 Moto</h5>
                <h2><?= $statsVeiculo['Moto'] ?? 0 ?></h2>
                <small>inscrições</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-veiculo stat-quadriciclo">
                <h5>🚜 Quadriciclo</h5>
                <h2><?= $statsVeiculo['Quadriciclo'] ?? 0 ?></h2>
                <small>inscrições</small>
            </div>
        </div>
    </div>

    <!-- Inscrições por categoria -->
    <div class="row mb-4">
        <?php
        $veiculoLabels = ['Carro' => '🚗 Carro / UTV', 'Moto' => '🏍 Moto', 'Quadriciclo' => '🚜 Quadriciclo'];
        uksort($veiculoLabels, fn($a, $b) => ($statsVeiculo[$b] ?? 0) <=> ($statsVeiculo[$a] ?? 0));
        foreach ($veiculoLabels as $vKey => $vLabel):
            if (empty($statsCategoria[$vKey])) continue;
            $cats    = $statsCategoria[$vKey];
            $colId   = 'cat-' . strtolower($vKey);
            $total   = count($cats);
        ?>
        <div class="col-md-4">
            <div class="section-title"><?= $vLabel ?></div>
            <table class="table table-sm cat-table mb-0">
                <thead><tr><th>Categoria</th><th class="text-right">Qtd</th></tr></thead>
                <tbody>
                <?php foreach (array_slice($cats, 0, 3) as $cat): ?>
                    <tr>
                        <td><?= htmlspecialchars($cat['categoria'] ?: '(não informada)') ?></td>
                        <td class="text-right"><span class="qtd-badge"><?= (int)$cat['qtd'] ?></span></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <?php if ($total > 3): ?>
                <tbody id="<?= $colId ?>" style="display:none">
                <?php foreach (array_slice($cats, 3) as $cat): ?>
                    <tr>
                        <td><?= htmlspecialchars($cat['categoria'] ?: '(não informada)') ?></td>
                        <td class="text-right"><span class="qtd-badge"><?= (int)$cat['qtd'] ?></span></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <?php endif; ?>
            </table>
            <?php if ($total > 3): ?>
            <button type="button" onclick="toggleCat('<?= $colId ?>', this)"
                    style="background:none;border:none;color:#2980b9;font-size:12px;cursor:pointer;padding:4px 0;margin-bottom:12px">
                ▼ Ver todas (<?= $total ?> categorias)
            </button>
            <?php else: ?>
            <div style="margin-bottom:12px"></div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <script>
    function toggleCat(id, btn) {
        var el = document.getElementById(id);
        var open = el.style.display !== 'none';
        el.style.display = open ? 'none' : '';
        btn.innerHTML = open ? '▼ Ver todas' : '▲ Fechar';
    }
    </script>

    <!-- Filtros -->
    <form method="GET" class="row g-2 mb-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label">Buscar (nome, CPF ou e-mail)</label>
            <input type="text" name="busca" class="form-control"
                   value="<?= htmlspecialchars($filtroBusca) ?>" placeholder="Digite..."/>
        </div>
        <div class="col-md-2">
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
                <option value="">Todos</option>
                <option value="pendente"  <?= $filtroStatus === 'pendente'  ? 'selected' : '' ?>>Pendente</option>
                <option value="pago"      <?= $filtroStatus === 'pago'      ? 'selected' : '' ?>>Pago</option>
                <option value="cancelado" <?= $filtroStatus === 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Veículo</label>
            <select name="veiculo" class="form-control">
                <option value="">Todos</option>
                <option value="Carro"       <?= $filtroVeiculo === 'Carro'       ? 'selected' : '' ?>>Carro/UTV</option>
                <option value="Moto"        <?= $filtroVeiculo === 'Moto'        ? 'selected' : '' ?>>Moto</option>
                <option value="Quadriciclo" <?= $filtroVeiculo === 'Quadriciclo' ? 'selected' : '' ?>>Quadriciclo</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">&nbsp;</label>
           <div class="form-controlx"> <button type="submit" class="btn btn-primary w-100">Filtrar</button> 
           <a href="index.php" class="btn btn-secondary w-100">Limpar</a></div>
        </div>
       
    </form><br>

    <!-- Mensagens de feedback -->
    <?php if (!empty($_GET['msg'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
    <?php endif; ?>
    <?php if (!empty($_GET['erro'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_GET['erro']) ?></div>
    <?php endif; ?>

    <!-- Tabela -->
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-sm">
            <thead>
            <tr>
                <th>#</th>
                <th>Nome</th>
                <th>CPF</th>
                <th>E-mail</th>
                <th>Telefone</th>
                <th>Veículo</th>
                <th>Categoria</th>
                <th>Valor</th>
                <th>Status</th>
                <th>Data</th>
                <th>Ações</th>
            </tr>
            </thead>
            <tbody>
            <?php if (empty($inscricoes)): ?>
                <tr><td colspan="11" class="text-center text-muted">Nenhuma inscrição encontrada.</td></tr>
            <?php else: ?>
                <?php foreach ($inscricoes as $ins): ?>
                    <tr>
                        <td><?= str_pad((string)$ins['id'], 6, '0', STR_PAD_LEFT) ?></td>
                        <td><?= htmlspecialchars($ins['nome']) ?></td>
                        <td><?= htmlspecialchars($ins['cpf']) ?></td>
                        <td><?= htmlspecialchars($ins['email']) ?></td>
                        <td><?= htmlspecialchars($ins['telefone']) ?></td>
                        <td><?= htmlspecialchars($ins['veiculo']) ?></td>
                        <td><?= htmlspecialchars($ins['categoria']) ?></td>
                        <td>R$ <?= number_format((float)$ins['valor'], 2, ',', '.') ?></td>
                        <td>
                            <span class="badge-<?= $ins['status_pagamento'] ?>">
                                <?= strtoupper($ins['status_pagamento']) ?>
                            </span>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($ins['created_at'])) ?></td>
                        <td>
                            <a href="detalhes.php?id=<?= $ins['id'] ?>"
                               class="btn btn-info btn-sm">Ver</a>

                            <?php if ($ins['status_pagamento'] !== 'pago'): ?>
                                <form method="POST" action="atualiza-status.php"
                                      style="display:inline"
                                      onsubmit="return confirm('Marcar como PAGO?')">
                                    <input type="hidden" name="id" value="<?= $ins['id'] ?>"/>
                                    <input type="hidden" name="status" value="pago"/>
                                    <input type="hidden" name="redirect" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>"/>
                                    <button type="submit" class="btn btn-success btn-sm">Pago</button>
                                </form>
                            <?php endif; ?>

                            <?php if ($ins['status_pagamento'] !== 'cancelado'): ?>
                                <form method="POST" action="atualiza-status.php"
                                      style="display:inline"
                                      onsubmit="return confirm('Cancelar esta inscrição?')">
                                    <input type="hidden" name="id" value="<?= $ins['id'] ?>"/>
                                    <input type="hidden" name="status" value="cancelado"/>
                                    <input type="hidden" name="redirect" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>"/>
                                    <button type="submit" class="btn btn-danger btn-sm">Cancelar</button>
                                </form>
                            <?php endif; ?>

                            <?php if ($ins['status_pagamento'] !== 'pendente'): ?>
                                <form method="POST" action="atualiza-status.php"
                                      style="display:inline"
                                      onsubmit="return confirm('Reverter para PENDENTE?')">
                                    <input type="hidden" name="id" value="<?= $ins['id'] ?>"/>
                                    <input type="hidden" name="status" value="pendente"/>
                                    <input type="hidden" name="redirect" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>"/>
                                    <button type="submit" class="btn btn-warning btn-sm">Pendente</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Paginação -->
    <?php if ($totalPaginas > 1): ?>
    <nav>
        <ul class="pagination justify-content-center">
            <li class="page-item <?= $pagina <= 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= qStr(['p' => $pagina - 1]) ?>">Anterior</a>
            </li>
            <?php for ($i = max(1, $pagina - 2); $i <= min($totalPaginas, $pagina + 2); $i++): ?>
                <li class="page-item <?= $i === $pagina ? 'active' : '' ?>">
                    <a class="page-link" href="<?= qStr(['p' => $i]) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?= $pagina >= $totalPaginas ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= qStr(['p' => $pagina + 1]) ?>">Próxima</a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>

   
</div>

<script src="../inscricao-inc/js/jquery.min.js"></script>
<script src="../inscricao-inc/js/bootstrap.min.js"></script>
</body>
</html>
