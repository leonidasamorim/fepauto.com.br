<?php
/**
 * Endpoint de retorno automático do PagSeguro (POST direto).
 * Configure este URL no painel do PagSeguro como "URL de Retorno Automático".
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

$transacaoId     = $_POST['TransacaoID']     ?? '';
$referencia      = $_POST['Referencia']      ?? '';
$statusTransacao = $_POST['StatusTransacao'] ?? '';

if (empty($transacaoId) || empty($referencia)) {
    http_response_code(400);
    exit;
}

// Referencia = "INSCRICAO-{id}"
if (!preg_match('/^INSCRICAO-(\d+)$/', $referencia, $m)) {
    http_response_code(200);
    exit;
}
$inscricaoId = (int)$m[1];

// Mapeamento PagSeguro StatusTransacao → nosso status
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
    error_log('Retorno PagSeguro update: ' . $e->getMessage());
    http_response_code(500);
    exit;
}

http_response_code(200);
