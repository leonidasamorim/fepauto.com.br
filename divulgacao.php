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

        @keyframes pulse-btn {
            0%   { box-shadow: 0 8px 30px rgba(245,166,35,0.45), 0 0 0 0 rgba(245,166,35,0.55); }
            60%  { box-shadow: 0 8px 30px rgba(245,166,35,0.45), 0 0 0 18px rgba(245,166,35,0); }
            100% { box-shadow: 0 8px 30px rgba(245,166,35,0.45), 0 0 0 0 rgba(245,166,35,0); }
        }

        .btn-inscricao {
            display: inline-block;
            background: linear-gradient(135deg, #f5a623 0%, #e8490a 100%);
            color: #fff;
            font-family: 'Poppins', sans-serif;
            font-weight: 800;
            font-size: 1.5rem;
            padding: 22px 64px;
            border-radius: 50px;
            text-decoration: none;
            box-shadow: 0 8px 30px rgba(245,166,35,0.45);
            animation: pulse-btn 1.8s ease-out infinite;
            transition: transform 0.15s;
            letter-spacing: 0.5px;
        }

        .btn-inscricao:hover {
            transform: translateY(-3px);
            animation: none;
            box-shadow: 0 14px 40px rgba(245,166,35,0.65);
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
            font-size: 1.35rem;
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
            font-size: 1.4rem;
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
            padding: 13px 28px;
            border-radius: 40px;
            font-weight: 600;
            font-size: 1.4rem;
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
            padding: 24px 20px;
            color: rgba(255,255,255,0.25);
            font-size: 1.2rem;
        }

        footer a { color: rgba(255,255,255,0.4); text-decoration: none; }

        /* ── RESPONSIVE ── */
        @media (max-width: 576px) {
            .btn-inscricao { font-size: 1.2rem; padding: 18px 40px; }
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
        <h1 class="hero-title">Rallye do <span>Sol 2026</span></h1>
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


<!-- ── CTA BOTTOM ── -->
<section class="cta-section">
    <h2 class="cta-title">Inscrições <span>Abertas!</span></h2>
    <p class="cta-sub">Não fique de fora — vagas limitadas por categoria.</p>
</section>

<!-- ── REDES SOCIAIS ── -->
<section class="social-section">
    <p class="social-label">Siga a FEPAUTO</p>
    <div class="social-btns">
        <a href="https://web.facebook.com/fepamfepauto/?locale=pt_BR" class="btn-social btn-fb" target="_blank" rel="noopener">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M22 12c0-5.522-4.477-10-10-10S2 6.478 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987H7.898V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.891h-2.33v6.987C18.343 21.128 22 16.991 22 12z"/></svg>
            Facebook
        </a>
        <a href="https://www.instagram.com/fepautofepam/" class="btn-social btn-ig" target="_blank" rel="noopener">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 1.366.062 2.633.326 3.608 1.301.975.975 1.24 2.242 1.301 3.608.058 1.266.07 1.646.07 4.85s-.012 3.584-.07 4.85c-.062 1.366-.326 2.633-1.301 3.608-.975.975-2.242 1.24-3.608 1.301-1.266.058-1.646.07-4.85.07s-3.584-.012-4.85-.07c-1.366-.062-2.633-.326-3.608-1.301-.975-.975-1.24-2.242-1.301-3.608C2.175 15.584 2.163 15.204 2.163 12s.012-3.584.07-4.85c.062-1.366.326-2.633 1.301-3.608C4.509 2.489 5.776 2.225 7.142 2.163 8.408 2.105 8.788 2.163 12 2.163zm0-2.163C8.741 0 8.332.014 7.052.072 5.197.157 3.355.673 2.014 2.014.673 3.355.157 5.197.072 7.052.014 8.332 0 8.741 0 12c0 3.259.014 3.668.072 4.948.085 1.855.601 3.697 1.942 5.038 1.341 1.341 3.183 1.857 5.038 1.942C8.332 23.986 8.741 24 12 24s3.668-.014 4.948-.072c1.855-.085 3.697-.601 5.038-1.942 1.341-1.341 1.857-3.183 1.942-5.038.058-1.28.072-1.689.072-4.948s-.014-3.668-.072-4.948c-.085-1.855-.601-3.697-1.942-5.038C20.645.673 18.803.157 16.948.072 15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zm0 10.162a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z"/></svg>
            Instagram
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
