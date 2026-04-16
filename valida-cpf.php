<?php
header('Content-Type: application/json');
require_once __DIR__ . '/db.php';

$cpf = preg_replace('/\D/', '', $_GET['cpf'] ?? '');

if (strlen($cpf) !== 11) {
    echo json_encode(['valido' => false, 'motivo' => 'formato']);
    exit;
}

// Valida algoritmo CPF
if (preg_match('/^(\d)\1+$/', $cpf)) {
    echo json_encode(['valido' => false, 'motivo' => 'sequencia']);
    exit;
}

for ($t = 9; $t < 11; $t++) {
    $soma = 0;
    for ($i = 0; $i < $t; $i++) {
        $soma += (int)$cpf[$i] * ($t + 1 - $i);
    }
    $resto = (10 * $soma) % 11;
    if ((int)$cpf[$t] !== ($resto >= 10 ? 0 : $resto)) {
        echo json_encode(['valido' => false, 'motivo' => 'digito']);
        exit;
    }
}

// Verifica duplicata no banco
$cpfFormatado = preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);
try {
    $stmt = db()->prepare('SELECT id FROM inscricoes WHERE cpf = ?');
    $stmt->execute([$cpfFormatado]);
    if ($stmt->fetch()) {
        echo json_encode(['valido' => false, 'motivo' => 'duplicado']);
        exit;
    }
} catch (PDOException) {
    // banco indisponível: deixa passar, o processador vai checar de novo
}

echo json_encode(['valido' => true]);
