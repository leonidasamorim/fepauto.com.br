<?php
// ─── Timezone ─────────────────────────────────────────────────────────────────
date_default_timezone_set('America/Sao_Paulo');

// ─── Banco de dados ───────────────────────────────────────────────────────────
if (($_SERVER['HTTP_HOST'] ?? '') === 'fepauto.com.br' || ($_SERVER['HTTP_HOST'] ?? '') === 'www.fepauto.com.br') {
    define('DB_HOST', 'localhost');
    define('DB_PORT', '3306');
    define('DB_NAME', 'leonid01_fepauto');
    define('DB_USER', 'leonid01_fepauto');
    define('DB_PASS', 'X;}&iExDE_4K');
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
define('PAGSEGURO_TOKEN',    '1eb3cc76-dc52-4fe3-ae37-fb0eb66fc1efb0e1e704461ba89c668c1b327a65026af67c-6797-4be1-bb4f-205ceb3c99c0');       // preencha com seu token da conta PagSeguro
define('PAGSEGURO_SANDBOX',  false);     // true = sandbox | false = produção

// ─── PIX ─────────────────────────────────────────────────────────────────────
define('PIX_CHAVE',   '03.143.496/0001-40');       // CNPJ sem pontuação (chave PIX)
define('PIX_TITULAR', 'FEDERAÇÃO PARAENSE DE MOTOCICLISMO');
define('PIX_BANCO',   'CAIXA ECONOMICA FEDERAL');

// ─── Carteira da Federação ────────────────────────────────────────────────────
define('CARTEIRA_CARRO',           214.00);
define('CARTEIRA_CARRO_RENOVACAO', 528.00); // apenas categorias PTRR1 e PNRR
define('CARTEIRA_MOTO',            100.00);
define('CARTEIRA_MOTO_ANO',        450.00);
define('CARTEIRA_MOTO_RENOVACAO',  450.00);

// ─── Evento ───────────────────────────────────────────────────────────────────
define('EVENT_NAME',  'XXIX Rallye do Sol 2026');
define('EVENT_YEAR',  '2026');

// ─── Tabela de preços (Carro/UTV | Moto-Quadricículo | Moto Iniciante) ───────
// Cada lote: ['ate' => 'AAAA-MM-DD', 'carro' => float, 'moto' => float, 'moto_iniciante' => float]
define('PRECOS', [
    ['ate' => '2026-05-31', 'carro' => 350.00, 'moto' => 250.00, 'moto_iniciante' => 140.00],
    ['ate' => '2026-06-15', 'carro' => 360.00, 'moto' => 260.00, 'moto_iniciante' => 150.00],
    ['ate' => '2026-06-30', 'carro' => 370.00, 'moto' => 270.00, 'moto_iniciante' => 160.00],
    ['ate' => '2026-07-15', 'carro' => 380.00, 'moto' => 280.00, 'moto_iniciante' => 170.00],
    ['ate' => '2026-07-22', 'carro' => 400.00, 'moto' => 300.00, 'moto_iniciante' => 190.00],
]);
