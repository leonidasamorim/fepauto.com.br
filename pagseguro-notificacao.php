<?php
/**
 * Endpoint de notificação do PagSeguro (IPN).
 * Configure este URL no painel do PagSeguro como notificationURL.
 */
declare(strict_types=1);
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

$notificationCode = $_POST['notificationCode'] ?? '';
$notificationType = $_POST['notificationType'] ?? '';

 function gravar_request2(){
	
  @session_start();
  $parametros="";
  $parametros .= "GET----------------------------------------------------------------\n";
  $parametros .= var_export($_GET, true);
  $parametros .= "POST----------------------------------------------------------------\n";
  $parametros .= var_export($_POST, true);
  $parametros .= "SESSION----------------------------------------------------------------\n";
  $parametros .= var_export($_SESSION, true);
  $parametros .= "COOKIE----------------------------------------------------------------\n";
  $parametros .= var_export($_COOKIE, true);
  $parametros .= "SERVER----------------------------------------------------------------\n";
  $parametros .= var_export($_SERVER, true);
   //return $parametros;
   	@file_put_contents("logs/". date("Ymd").'-'.date("His") ."_notification_pagseguro.txt", $parametros);

	 return nl2br($parametros);

}
gravar_request2();



if ($notificationType !== 'transaction' || empty($notificationCode)) {
    http_response_code(400);
    exit;
}

// Consulta o status da transação na API PagSeguro
$apiUrl = PAGSEGURO_SANDBOX
    ? 'https://ws.sandbox.pagseguro.uol.com.br/v3/transactions/notifications/' . $notificationCode
    : 'https://ws.pagseguro.uol.com.br/v3/transactions/notifications/' . $notificationCode;

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

$transactionId = (string)($xml->code ?? '');
$statusCode    = (int)($xml->status ?? 0);
$reference     = (string)($xml->reference ?? '');

// reference = "INSCRICAO-{id}"
if (!preg_match('/^INSCRICAO-(\d+)$/', $reference, $m)) {
    http_response_code(200);
    exit;
}
$inscricaoId = (int)$m[1];

// Mapeamento PagSeguro status → nosso status
// 1=Aguardando, 2=Em análise, 3=Paga, 4=Disponível, 5=Em disputa, 6=Devolvida, 7=Cancelada
$novoStatus = match ($statusCode) {
    3, 4    => 'pago',
    6, 7    => 'cancelado',
    default => 'pendente',
};

try {
    db()->prepare(
        'UPDATE inscricoes
         SET status_pagamento = ?, pagseguro_transaction_id = ?
         WHERE id = ?'
    )->execute([$novoStatus, $transactionId, $inscricaoId]);
} catch (PDOException $e) {
    error_log('IPN update: ' . $e->getMessage());
    http_response_code(500);
    exit;
}

http_response_code(200);
