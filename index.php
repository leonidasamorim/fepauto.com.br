<?php
/**
 * Página de espera – fepauto.com.br
 * Baseada em inscricao.php, exibindo a mensagem "Voltaremos em breve".
 */
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Em breve | Fepauto</title>

    <link rel="stylesheet" href="inscricao-inc/css/bootstrap.min.css">
    <link rel="stylesheet" href="inscricao-inc/css/ionicons.min.css">
    <link rel="stylesheet" href="inscricao-inc/css/style.css">
    <link rel="stylesheet" href="inscricao-inc/css/custom.css">

    <style>
        body.espera-page {
            min-height: 100vh;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            background-color: #1a1a2e;
        }

        .espera-hero {
            width: 100%;
            height: 260px;
            background: url('imagem-bg.jpg') no-repeat center center;
            background-size: cover;
            position: relative;
        }

        .espera-hero::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, rgba(0,0,0,0.25), rgba(26,26,46,1));
        }

        .espera-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.18);
            max-width: 460px;
            width: 90%;
            margin: -60px auto 40px;
            padding: 40px 36px 32px;
            position: relative;
            z-index: 10;
            text-align: center;
        }

        .espera-logo {
            width: 110px;
            height: 110px;
            object-fit: contain;
            border-radius: 50%;
            border: 4px solid #fff;
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
            margin-bottom: 24px;
        }

        .espera-title {
            font-size: 1.9rem;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 12px;
            line-height: 1.2;
        }

        .espera-subtitle {
            font-size: 1.1rem;
            color: #666;
            line-height: 1.7;
            margin-bottom: 28px;
        }

        .espera-divider {
            border: none;
            border-top: 1px solid #eee;
            margin: 0 0 24px;
        }

        .espera-social-label {
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #999;
            margin-bottom: 14px;
        }

        .espera-social-buttons {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .espera-social-buttons .btn {
            border-radius: 8px;
            font-size: 0.95rem;
            padding: 10px 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-weight: 600;
            transition: opacity 0.2s, transform 0.1s;
        }

        .espera-social-buttons .btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .espera-social-buttons .btn-facebook {
            background-color: #1877f2;
            color: #fff;
            border: none;
        }

        .espera-social-buttons .btn-instagram {
            background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888);
            color: #fff;
            border: none;
        }

        .espera-footer {
            text-align: center;
            color: rgba(255,255,255,0.4);
            font-size: 0.82rem;
            padding: 0 0 24px;
        }

        .ion-social-facebook,
        .ion-social-instagram {
            font-size: 1.1rem;
        }
    </style>
</head>
<body class="espera-page">

    <div class="espera-hero"></div>

    <div class="espera-card">
        <img src="logo.jpeg" alt="FepAuto Logo" class="espera-logo">

        <h1 class="espera-title">Voltaremos em breve!</h1>
        <p class="espera-subtitle">
            Estamos passando por manutenção.<br>
            Enquanto isso, nos acompanhe nas redes sociais.
        </p>

        <hr class="espera-divider">

        <p class="espera-social-label">Redes Sociais</p>

        <div class="espera-social-buttons">
            <a href="https://web.facebook.com/fepamfepauto/?locale=pt_BR" class="btn btn-facebook">
                <i class="ion ion-social-facebook"></i> Facebook
            </a>
            <a href="https://www.instagram.com/fepautofepam/" class="btn btn-instagram">
                <i class="ion ion-social-instagram"></i> Instagram
            </a>
        </div>
    </div>

    <p class="espera-footer">www.fepauto.com.br</p>

</body>
</html>
