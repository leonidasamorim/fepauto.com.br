<?php
// ─── Banco de dados ───────────────────────────────────────────────────────────
if (($_SERVER['HTTP_HOST'] ?? '') === 'fepauto.com.br' || ($_SERVER['HTTP_HOST'] ?? '') === 'www.fepauto.com.br') {
    define('DB_HOST', 'localhost');
    define('DB_PORT', '3306');
    define('DB_NAME', 'u927543591_fepauto');
    define('DB_USER', 'u927543591_fepauto');
    define('DB_PASS', 'nkdOr!NY~8');
} else {
    define('DB_HOST', 'db');
    define('DB_PORT', '3306');
    define('DB_NAME', 'rallye_inscricoes');
    define('DB_USER', 'rallye');
    define('DB_PASS', 'rallye123');
}

// ─── E-mail ───────────────────────────────────────────────────────────────────
define('MAIL_FROM',       'noreply@fepauto.com.br');
define('MAIL_FROM_NAME',  'FEPAUTO – Rallye do Sol');
define('MAIL_SMTP_HOST',  'smtp.hostinger.com.br');
define('MAIL_SMTP_PORT',  587);
define('MAIL_SMTP_USER',  'noreply@fepauto.com.br');
define('MAIL_SMTP_PASS',  '1=Xv57*oMV!b');

// ─── PagSeguro ────────────────────────────────────────────────────────────────
define('PAGSEGURO_EMAIL',    'rallyedosol2015@gmail.com');
define('PAGSEGURO_TOKEN',    'e893db5b-0e44-4fc0-b783-1d5c15cee1bdb8946a7e4aa38925d1d46c5f8b5d7a489d48-6360-4c96-a5ab-ca1308aea2d3');       // preencha com seu token da conta PagSeguro
define('PAGSEGURO_SANDBOX',  false);     // true = sandbox | false = produção

// ─── PIX ─────────────────────────────────────────────────────────────────────
define('PIX_CHAVE',   '03.143.496/0001-40');       // CNPJ sem pontuação (chave PIX)
define('PIX_TITULAR', 'FEDERAÇÃO PARAENSE DE MOTOCICLISMO');
define('PIX_BANCO',   'CAIXA ECONOMICA FEDERAL');

// ─── Carteira da Federação ────────────────────────────────────────────────────
define('CARTEIRA_CARRO',           214.00);
define('CARTEIRA_CARRO_RENOVACAO', 528.00); // apenas categorias PTRR1 e PNRR
define('CARTEIRA_MOTO',            400.00);
define('CARTEIRA_MOTO_RENOVACAO',  400.00);

// ─── Evento ───────────────────────────────────────────────────────────────────
define('EVENT_NAME',  'XXVI Rallye do Sol 2026');
define('EVENT_YEAR',  '2026');

// ─── Tabela de preços (Carro/UTV | Quadriciclo | Moto Iniciante) ─────────────
// Cada lote: ['ate' => 'AAAA-MM-DD', 'carro' => float, 'quadriciclo' => float, 'moto' => float]
define('PRECOS', [
    ['ate' => '2026-05-31', 'carro' => 350.00, 'quadriciclo' => 250.00, 'moto' => 140.00],
    ['ate' => '2026-06-15', 'carro' => 360.00, 'quadriciclo' => 260.00, 'moto' => 150.00],
    ['ate' => '2026-06-30', 'carro' => 370.00, 'quadriciclo' => 270.00, 'moto' => 160.00],
    ['ate' => '2026-07-15', 'carro' => 380.00, 'quadriciclo' => 280.00, 'moto' => 170.00],
    ['ate' => '2026-07-22', 'carro' => 400.00, 'quadriciclo' => 300.00, 'moto' => 190.00],
]);
