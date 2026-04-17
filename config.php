<?php
// ─── Banco de dados ───────────────────────────────────────────────────────────
define('DB_HOST', 'db');          // nome do serviço no docker-compose
define('DB_PORT', '3306');
define('DB_NAME', 'rallye_inscricoes');
define('DB_USER', 'rallye');
define('DB_PASS', 'rallye123');

// ─── E-mail ───────────────────────────────────────────────────────────────────
define('MAIL_FROM',       'noreply@fepauto.com.br');
define('MAIL_FROM_NAME',  'FEPAUTO – Rallye do Sol');
define('MAIL_SMTP_HOST',  'smtp.hostinger.com.br');
define('MAIL_SMTP_PORT',  587);
define('MAIL_SMTP_USER',  'noreply@fepauto.com.br');
define('MAIL_SMTP_PASS',  '1=Xv57*oMV!b');

// ─── PagSeguro ────────────────────────────────────────────────────────────────
define('PAGSEGURO_EMAIL',    'rallyedosol2015@gmail.com');
define('PAGSEGURO_TOKEN',    'email=rallyedosol2015@gmail.com&token=5CA2A1DB720E4FC18B16D776C29D73E4');       // preencha com seu token da conta PagSeguro
define('PAGSEGURO_SANDBOX',  false);     // true = sandbox | false = produção

// ─── PIX ─────────────────────────────────────────────────────────────────────
define('PIX_CHAVE',   '15753536000155');       // CNPJ sem pontuação (chave PIX)
define('PIX_TITULAR', 'FEPAUTO');
define('PIX_BANCO',   'Banco do Brasil');

// ─── Evento ───────────────────────────────────────────────────────────────────
define('EVENT_NAME',  'XXVI Rallye do Sol 2026');
define('EVENT_YEAR',  '2026');

// ─── Tabela de preços (Carro/UTV | Moto/Quadriciclo) ─────────────────────────
// Cada lote: ['ate' => 'AAAA-MM-DD', 'carro' => float, 'moto' => float]
define('PRECOS', [
    ['ate' => '2026-04-30', 'carro' => 280.00, 'moto' => 175.00],
    ['ate' => '2026-05-31', 'carro' => 300.00, 'moto' => 195.00],
    ['ate' => '2026-06-30', 'carro' => 320.00, 'moto' => 215.00],
    ['ate' => '2026-07-20', 'carro' => 340.00, 'moto' => 235.00],
]);
