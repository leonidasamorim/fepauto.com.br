<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php';
require_once dirname(__DIR__) . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id = (int)($_POST['id'] ?? 0);

if ($id <= 0) {
    header('Location: index.php?erro=' . urlencode('ID inválido.'));
    exit;
}

try {
    $stmt = db()->prepare('DELETE FROM inscricoes WHERE id = ?');
    $stmt->execute([$id]);

    header('Location: index.php?msg=' . urlencode('Inscrição #' . str_pad((string)$id, 6, '0', STR_PAD_LEFT) . ' excluída com sucesso.'));
} catch (PDOException $e) {
    error_log('deletar-inscricao: ' . $e->getMessage());
    header('Location: index.php?erro=' . urlencode('Erro ao excluir inscrição. Tente novamente.'));
}
exit;
