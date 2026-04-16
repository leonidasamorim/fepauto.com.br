<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php';
require_once dirname(__DIR__) . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id     = (int)($_POST['id'] ?? 0);
$status = $_POST['status'] ?? '';
$redirect = $_POST['redirect'] ?? 'index.php';

// Garante redirect local (previne open redirect)
if (!preg_match('/^[a-z0-9_\-\.\/\?=&%]+$/i', $redirect)) {
    $redirect = 'index.php';
}

$statusValidos = ['pendente', 'pago', 'cancelado'];

if ($id <= 0 || !in_array($status, $statusValidos, true)) {
    header('Location: ' . $redirect . (str_contains($redirect, '?') ? '&' : '?') .
           'erro=' . urlencode('Dados inválidos.'));
    exit;
}

try {
    $stmt = db()->prepare('UPDATE inscricoes SET status_pagamento = ? WHERE id = ?');
    $stmt->execute([$status, $id]);

    $msg = match ($status) {
        'pago'      => 'Inscrição marcada como PAGA.',
        'cancelado' => 'Inscrição cancelada.',
        'pendente'  => 'Status revertido para PENDENTE.',
        default     => 'Status atualizado.',
    };

    header('Location: ' . $redirect . (str_contains($redirect, '?') ? '&' : '?') .
           'msg=' . urlencode($msg));
} catch (PDOException $e) {
    error_log('atualiza-status: ' . $e->getMessage());
    header('Location: ' . $redirect . (str_contains($redirect, '?') ? '&' : '?') .
           'erro=' . urlencode('Erro ao atualizar status. Tente novamente.'));
}
exit;
