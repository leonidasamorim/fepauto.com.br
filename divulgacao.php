<?php require_once __DIR__ . '/config.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>XXVI Rallye do Sol 2026 – Inscrições Abertas | FEPAUTO</title>
    <meta name="description" content="Inscrições abertas para o XXVI Rallye do Sol 2026 – Benevides-Salinópolis. Corrida dia 25/07. Categorias: Carro/UTV, Quadriciclo e Moto/Iniciante.">

    <link rel="stylesheet" href="inscricao-inc/css/bootstrap.min.css">
    <link rel="stylesheet" href="inscricao-inc/css/ionicons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background: #0d0d1a;
            color: #fff;
        }

        /* ── HERO ── */
        .hero {
            position: relative;
            width: 100%;
            min-height: 100vh;
            background: url('banner.jpeg') no-repeat center center / cover;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-end;
            padding-bottom: 60px;
        }

        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(
                to bottom,
                rgba(0,0,0,0.15) 0%,
                rgba(0,0,0,0.0) 30%,
                rgba(13,13,26,0.75) 65%,
                rgba(13,13,26,1) 100%
            );
        }

        .hero-logo {
            position: absolute;
            top: 24px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 80px;
            object-fit: contain;
            border-radius: 50%;
            border: 3px solid rgba(255,255,255,0.9);
            box-shadow: 0 4px 20px rgba(0,0,0,0.5);
            z-index: 2;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
            padding: 0 20px;
        }

        .hero-edition {
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: #f5a623;
            margin-bottom: 6px;
        }

        .hero-title {
            font-size: clamp(2.4rem, 6vw, 4rem);
            font-weight: 800;
            line-height: 1.05;
            margin: 0 0 8px;
            text-shadow: 0 2px 12px rgba(0,0,0,0.6);
        }

        .hero-title span { color: #f5a623; }

        .hero-location {
            font-size: 1.05rem;
            color: rgba(255,255,255,0.8);
            margin-bottom: 6px;
        }

        .hero-date {
            display: inline-block;
            background: #f5a623;
            color: #0d0d1a;
            font-weight: 800;
            font-size: 1.1rem;
            padding: 6px 24px;
            border-radius: 40px;
            margin-bottom: 32px;
            letter-spacing: 1px;
        }

        .btn-inscricao {
            display: inline-block;
            background: linear-gradient(135deg, #f5a623 0%, #e8490a 100%);
            color: #fff;
            font-family: 'Poppins', sans-serif;
            font-weight: 800;
            font-size: 1.25rem;
            padding: 18px 52px;
            border-radius: 50px;
            text-decoration: none;
            box-shadow: 0 8px 30px rgba(245,166,35,0.45);
            transition: transform 0.15s, box-shadow 0.15s;
            letter-spacing: 0.5px;
        }

        .btn-inscricao:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 40px rgba(245,166,35,0.55);
            color: #fff;
            text-decoration: none;
        }

        .btn-inscricao:active { transform: translateY(-1px); }

        .hero-arrow {
            position: absolute;
            bottom: 18px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 2;
            color: rgba(255,255,255,0.4);
            font-size: 1.6rem;
            animation: bounce 1.8s infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateX(-50%) translateY(0); }
            50%       { transform: translateX(-50%) translateY(8px); }
        }

        /* ── BADGES ── */
        .badges-section {
            background: #0d0d1a;
            padding: 40px 20px 32px;
        }

        .badges-wrap {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
            max-width: 800px;
            margin: 0 auto;
        }

        .badge-item {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            padding: 20px 28px;
            text-align: center;
            min-width: 160px;
        }

        .badge-icon { font-size: 1.8rem; margin-bottom: 8px; }
        .badge-label { font-size: 0.7rem; font-weight: 600; letter-spacing: 2px; text-transform: uppercase; color: #f5a623; margin-bottom: 4px; }
        .badge-value { font-size: 1.1rem; font-weight: 700; color: #fff; }

        /* ── PREÇOS ── */
        .precos-section {
            background: #12122a;
            padding: 60px 20px;
        }

        .section-label {
            text-align: center;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: #f5a623;
            margin-bottom: 10px;
        }

        .section-title {
            text-align: center;
            font-size: clamp(1.6rem, 4vw, 2.2rem);
            font-weight: 800;
            margin: 0 0 40px;
            color: #fff;
        }

        .table-wrap {
            max-width: 860px;
            margin: 0 auto;
            overflow-x: auto;
            border-radius: 12px;
            box-shadow: 0 4px 30px rgba(0,0,0,0.4);
        }

        .table-precos {
            width: 100%;
            border-collapse: collapse;
            background: #1a1a36;
            font-size: 0.92rem;
        }

        .table-precos thead tr {
            background: #f5a623;
            color: #0d0d1a;
        }

        .table-precos th {
            padding: 14px 18px;
            font-weight: 700;
            text-align: center;
            white-space: nowrap;
        }

        .table-precos th:first-child { text-align: left; }

        .table-precos tbody tr { border-bottom: 1px solid rgba(255,255,255,0.07); }
        .table-precos tbody tr:last-child { border-bottom: none; }
        .table-precos tbody tr:hover { background: rgba(245,166,35,0.07); }

        .table-precos td, .table-precos tbody th {
            padding: 14px 18px;
            text-align: center;
            color: #e0e0e0;
        }

        .table-precos tbody th {
            text-align: left;
            font-weight: 700;
            color: #fff;
            white-space: nowrap;
        }

        .table-precos .icon { margin-right: 6px; }

        .table-note {
            text-align: center;
            color: rgba(255,255,255,0.35);
            font-size: 0.78rem;
            margin-top: 14px;
        }

        /* ── CTA BOTTOM ── */
        .cta-section {
            background: linear-gradient(135deg, #1a1a2e 0%, #0d0d1a 100%);
            padding: 70px 20px;
            text-align: center;
            border-top: 1px solid rgba(245,166,35,0.2);
        }

        .cta-title {
            font-size: clamp(1.8rem, 4vw, 2.6rem);
            font-weight: 800;
            margin-bottom: 10px;
        }

        .cta-title span { color: #f5a623; }

        .cta-sub {
            color: rgba(255,255,255,0.55);
            font-size: 1rem;
            margin-bottom: 36px;
        }

        /* ── SOCIAL ── */
        .social-section {
            background: #0d0d1a;
            padding: 40px 20px;
            text-align: center;
            border-top: 1px solid rgba(255,255,255,0.06);
        }

        .social-label {
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.35);
            margin-bottom: 16px;
        }

        .social-btns {
            display: flex;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn-social {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 24px;
            border-radius: 40px;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            transition: opacity 0.15s, transform 0.15s;
        }

        .btn-social:hover { opacity: 0.88; transform: translateY(-2px); text-decoration: none; }
        .btn-fb  { background: #1877f2; color: #fff; }
        .btn-ig  { background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888); color: #fff; }

        /* ── FOOTER ── */
        footer {
            background: #080810;
            text-align: center;
            padding: 20px;
            color: rgba(255,255,255,0.25);
            font-size: 0.8rem;
        }

        footer a { color: rgba(255,255,255,0.4); text-decoration: none; }

        /* ── RESPONSIVE ── */
        @media (max-width: 576px) {
            .btn-inscricao { font-size: 1.05rem; padding: 16px 36px; }
            .badges-wrap { gap: 12px; }
            .badge-item { min-width: 140px; padding: 16px 18px; }
        }
    </style>
</head>
<body>

<!-- ── HERO ── -->
<section class="hero">
    <img src="logo.jpeg" alt="FEPAUTO" class="hero-logo">

    <div class="hero-content">
        <p class="hero-edition">XXVI Edição &bull; 2026</p>
        <h1 class="hero-title">Rallye do <span>Sol</span></h1>
        <p class="hero-location">Benevides &rarr; Salinópolis &bull; Pará</p>
        <div class="hero-date">&#127942; Corrida: 25 de Julho de 2026</div>
        <br>
        <a href="inscricao.php" class="btn-inscricao">QUERO ME INSCREVER</a>
    </div>

    <div class="hero-arrow">&#8595;</div>
</section>

<!-- ── BADGES ── -->
<section class="badges-section">
    <div class="badges-wrap">
        <div class="badge-item">
            <div class="badge-icon">&#127942;</div>
            <div class="badge-label">Edição</div>
            <div class="badge-value">XXVI Rallye do Sol</div>
        </div>
        <div class="badge-item">
            <div class="badge-icon">&#128197;</div>
            <div class="badge-label">Corrida</div>
            <div class="badge-value">25 de Julho de 2026</div>
        </div>
        <div class="badge-item">
            <div class="badge-icon">&#128205;</div>
            <div class="badge-label">Percurso</div>
            <div class="badge-value">Benevides – Salinópolis</div>
        </div>
        <div class="badge-item">
            <div class="badge-icon">&#127939;</div>
            <div class="badge-label">Categorias</div>
            <div class="badge-value">Carro, Quadriciclo e Moto</div>
        </div>
    </div>
</section>

<!-- ── TABELA DE PREÇOS ── -->
<section class="precos-section">
    <p class="section-label">Valores por lote</p>
    <h2 class="section-title">Tabela de Inscrição</h2>

    <div class="table-wrap">
        <table class="table-precos">
            <thead>
                <tr>
                    <th>Veículo</th>
                    <th>23/04 – 31/05</th>
                    <th>01/06 – 15/06</th>
                    <th>16/06 – 30/06</th>
                    <th>01/07 – 15/07</th>
                    <th>16/07 – 22/07</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $lotes = PRECOS;
                $veiculos = [
                    'Carro / UTV'    => 'carro',
                    'Quadriciclo'    => 'quadriciclo',
                    'Moto / Iniciante' => 'moto',
                ];
                $icons = ['Carro / UTV' => '&#128664;', 'Quadriciclo' => '&#129683;', 'Moto / Iniciante' => '&#127949;'];
                foreach ($veiculos as $label => $campo):
                ?>
                <tr>
                    <th><?= $icons[$label] ?> <span class="icon"></span><?= $label ?></th>
                    <?php foreach ($lotes as $lote): ?>
                    <td>R$ <?= number_format($lote[$campo], 2, ',', '.') ?></td>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <p class="table-note">* Valores aumentam conforme lote. Garanta o menor preço antecipando sua inscrição.</p>
</section>

<!-- ── CTA BOTTOM ── -->
<section class="cta-section">
    <h2 class="cta-title">Inscrições <span>Abertas!</span></h2>
    <p class="cta-sub">Não fique de fora — vagas limitadas por categoria.</p>
    <a href="inscricao.php" class="btn-inscricao">GARANTIR MINHA VAGA</a>
</section>

<!-- ── REDES SOCIAIS ── -->
<section class="social-section">
    <p class="social-label">Siga a FEPAUTO</p>
    <div class="social-btns">
        <a href="https://web.facebook.com/fepamfepauto/?locale=pt_BR" class="btn-social btn-fb" target="_blank" rel="noopener">
            <i class="ion ion-social-facebook"></i> Facebook
        </a>
        <a href="https://www.instagram.com/fepautofepam/" class="btn-social btn-ig" target="_blank" rel="noopener">
            <i class="ion ion-social-instagram"></i> Instagram
        </a>
    </div>
</section>

<!-- ── FOOTER ── -->
<footer>
    <p style="margin:0">FEPAUTO &ndash; Federação Paraense de Automobilismo &bull; CNPJ: 15.753.536/0001-55</p>
    <p style="margin:4px 0 0"><a href="mailto:fepauto@fepauto.com.br">fepauto@fepauto.com.br</a> &bull; www.fepauto.com.br</p>
</footer>

</body>
</html>
