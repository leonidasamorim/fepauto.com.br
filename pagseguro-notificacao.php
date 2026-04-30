<?php
/**
 * Endpoint de notificação do PagSeguro.
 * Suporta Retorno Automático (POST direto) e IPN (notificationCode).
 */
declare(strict_types=1);
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

function gravar_request2(): void
{
    @session_start();
    $parametros  = "GET----------------------------------------------------------------\n";
    $parametros .= var_export($_GET, true);
    $parametros .= "\nPOST----------------------------------------------------------------\n";
    $parametros .= var_export($_POST, true);
    $parametros .= "\nSESSION----------------------------------------------------------------\n";
    $parametros .= var_export($_SESSION, true);
    $parametros .= "\nCOOKIE----------------------------------------------------------------\n";
    $parametros .= var_export($_COOKIE, true);
    $parametros .= "\nSERVER----------------------------------------------------------------\n";
    $parametros .= var_export($_SERVER, true);
    @file_put_contents('logs/' . date('Ymd') . '-' . date('His') . '_notification_pagseguro.txt', $parametros);
}

gravar_request2();

// --- Fluxo 1: Retorno Automático (POST direto com TransacaoID) ---
if (!empty($_POST['TransacaoID']) && !empty($_POST['Referencia'])) {
    $transacaoId     = $_POST['TransacaoID']     ?? '';
    $referencia      = $_POST['Referencia']      ?? '';
    $statusTransacao = $_POST['StatusTransacao'] ?? '';

    if (!preg_match('/^INSCRICAO-(\d+)$/', $referencia, $m)) {
        http_response_code(200);
        exit;
    }
    $inscricaoId = (int)$m[1];

    $novoStatus = match ($statusTransacao) {
        'Aprovado', 'Paga', 'Disponível'     => 'pago',
        'Devolvida', 'Devolvido', 'Debitado' => 'devolvido',
        'Cancelada', 'Cancelado'             => 'cancelado',
        default                              => 'pendente',
    };

    try {
        db()->prepare(
            'UPDATE inscricoes
             SET status_pagamento = ?, pagseguro_transaction_id = ?
             WHERE id = ?'
        )->execute([$novoStatus, $transacaoId, $inscricaoId]);
    } catch (PDOException $e) {
        error_log('Retorno Automático update: ' . $e->getMessage());
        http_response_code(500);
        exit;
    }

    http_response_code(200);
    exit;
}

// --- Fluxo 2: IPN (notificationCode + consulta à API PagSeguro) ---
$notificationCode = $_POST['notificationCode'] ?? '';
$notificationType = $_POST['notificationType'] ?? '';

if ($notificationType !== 'transaction' || empty($notificationCode)) {
    http_response_code(400);
    exit;
}

$apiUrl = PAGSEGURO_SANDBOX
    ? 'https://ws.sandbox.pagseguro.uol.com.br/v3/transactions/notifications/' . $notificationCode
    : 'https://ws.pagseguro.uol.com.br/v3/transactions/notifications/'         . $notificationCode;

$url = $apiUrl . '?' . http_build_query([
    'email' => PAGSEGURO_EMAIL,
    'token' => PAGSEGURO_TOKEN,
]);

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_TIMEOUT        => 15,
]);
$response = curl_exec($ch);
curl_close($ch);

if (!$response) {
    http_response_code(500);
    exit;
}

$xml = simplexml_load_string($response);
if (!$xml) {
    http_response_code(500);
    exit;
}

$transacaoId = (string)($xml->code      ?? '');
$statusCode  = (int)($xml->status       ?? 0);
$referencia  = (string)($xml->reference ?? '');

if (!preg_match('/^INSCRICAO-(\d+)$/', $referencia, $m)) {
    http_response_code(200);
    exit;
}
$inscricaoId = (int)$m[1];

// 1=Aguardando, 2=Em análise, 3=Paga, 4=Disponível, 5=Em disputa, 6=Devolvida, 7=Cancelada
$novoStatus = match ($statusCode) {
    3, 4    => 'pago',
    6       => 'devolvido',
    7       => 'cancelado',
    default => 'pendente',
};

try {
    db()->prepare(
        'UPDATE inscricoes
         SET status_pagamento = ?, pagseguro_transaction_id = ?
         WHERE id = ?'
    )->execute([$novoStatus, $transacaoId, $inscricaoId]);
} catch (PDOException $e) {
    error_log('IPN update: ' . $e->getMessage());
    http_response_code(500);
    exit;
}

http_response_code(200);
