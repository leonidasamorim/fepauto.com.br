<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/mailer.php';

// ─── Helpers ─────────────────────────────────────────────────────────────────

function redirect(string $url): never {
    header('Location: ' . $url);
    exit;
}

function s(mixed $v): string {
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

function post(string $key): string {
    return trim($_POST[$key] ?? '');
}

function validarCpf(string $cpf): bool {
    $cpf = preg_replace('/\D/', '', $cpf);
    if (strlen($cpf) !== 11 || preg_match('/^(\d)\1+$/', $cpf)) return false;
    for ($t = 9; $t < 11; $t++) {
        $soma = 0;
        for ($i = 0; $i < $t; $i++) {
            $soma += (int)$cpf[$i] * ($t + 1 - $i);
        }
        $resto = (10 * $soma) % 11;
        if ((int)$cpf[$t] !== ($resto >= 10 ? 0 : $resto)) return false;
    }
    return true;
}

function calcularValor(string $veiculo): float {
    $hoje = date('Y-m-d');
    foreach (PRECOS as $lote) {
        if ($hoje <= $lote['ate']) {
            return in_array($veiculo, ['Carro']) ? $lote['carro'] : $lote['moto'];
        }
    }
    // Último lote como fallback
    $ultimo = end(PRECOS);
    return in_array($veiculo, ['Carro']) ? $ultimo['carro'] : $ultimo['moto'];
}

function emailConfirmacao(array $d): string {
    $valor = number_format((float)$d['valor'], 2, ',', '.');
    $status = match ($d['veiculo']) {
        'Carro' => "Carro / UTV",
        default => $d['veiculo'],
    };

    $nav = '';
    if ($d['veiculo'] === 'Carro' && !empty($d['navegador_nome'])) {
        $nav = "
        <tr><td colspan='2' style='background:#f0f0f0;font-weight:bold;padding:8px'>Dados do Navegador</td></tr>
        <tr><td>Nome do Navegador</td><td>" . s($d['navegador_nome']) . "</td></tr>
        <tr><td>RG do Navegador</td><td>" . s($d['navegador_rg']) . "</td></tr>
        <tr><td>Tipo Sanguíneo (Nav.)</td><td>" . s($d['tipo_sangue_navegador']) . "</td></tr>";
    }

    return "<!DOCTYPE html><html lang='pt-BR'><head><meta charset='UTF-8'></head><body
        style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;padding:20px'>
        <div style='background:#1a1a2e;padding:20px;text-align:center;border-radius:8px 8px 0 0'>
            <h1 style='color:#fff;margin:0;font-size:24px'>FEPAUTO</h1>
            <p style='color:#ccc;margin:4px 0 0'>Federação Paraense de Automobilismo</p>
        </div>
        <div style='background:#fff;border:1px solid #ddd;padding:24px;border-radius:0 0 8px 8px'>
            <h2 style='color:#c0392b'>Inscrição Confirmada!</h2>
            <p>Olá, <strong>" . s($d['nome']) . "</strong>!</p>
            <p>Sua inscrição no <strong>" . EVENT_NAME . "</strong> foi recebida com sucesso.</p>
            <p style='background:#fff3cd;padding:12px;border-radius:4px;border-left:4px solid #ffc107'>
                <strong>⚠ Atenção:</strong> Sua inscrição estará confirmada somente após a
                confirmação do pagamento.
            </p>
            <h3>Resumo da Inscrição</h3>
            <table style='width:100%;border-collapse:collapse'>
                <tr style='background:#f8f8f8'>
                    <td style='padding:8px;border:1px solid #ddd;width:40%'><strong>Protocolo</strong></td>
                    <td style='padding:8px;border:1px solid #ddd'>#" . str_pad((string)$d['id'], 6, '0', STR_PAD_LEFT) . "</td>
                </tr>
                <tr><td style='padding:8px;border:1px solid #ddd'><strong>Nome</strong></td>
                    <td style='padding:8px;border:1px solid #ddd'>" . s($d['nome']) . "</td></tr>
                <tr style='background:#f8f8f8'>
                    <td style='padding:8px;border:1px solid #ddd'><strong>CPF</strong></td>
                    <td style='padding:8px;border:1px solid #ddd'>" . s($d['cpf']) . "</td></tr>
                <tr><td style='padding:8px;border:1px solid #ddd'><strong>Veículo</strong></td>
                    <td style='padding:8px;border:1px solid #ddd'>" . s($status) . "</td></tr>
                <tr style='background:#f8f8f8'>
                    <td style='padding:8px;border:1px solid #ddd'><strong>Categoria</strong></td>
                    <td style='padding:8px;border:1px solid #ddd'>" . s($d['categoria']) . "</td></tr>
                <tr><td style='padding:8px;border:1px solid #ddd'><strong>Telefone</strong></td>
                    <td style='padding:8px;border:1px solid #ddd'>" . s($d['telefone']) . "</td></tr>
                <tr style='background:#f8f8f8'>
                    <td style='padding:8px;border:1px solid #ddd'><strong>E-mail</strong></td>
                    <td style='padding:8px;border:1px solid #ddd'>" . s($d['email']) . "</td></tr>
                {$nav}
                <tr style='background:#e8f5e9'>
                    <td style='padding:8px;border:1px solid #ddd'><strong>Valor</strong></td>
                    <td style='padding:8px;border:1px solid #ddd;font-size:18px;color:#2e7d32'>
                        <strong>R$ {$valor}</strong></td></tr>
            </table>
            <p style='margin-top:20px'>
                <a href='http://" . ($_SERVER['HTTP_HOST'] ?? 'fepauto.com.br') . "/pagamento.php?id=" . $d['id'] . "'
                   style='background:#c0392b;color:#fff;padding:12px 24px;
                          text-decoration:none;border-radius:4px;font-weight:bold'>
                    REALIZAR PAGAMENTO
                </a>
            </p>
            <hr style='margin:24px 0;border:none;border-top:1px solid #eee'/>
            <p style='font-size:12px;color:#888'>
                FEPAUTO – Federação Paraense de Automobilismo<br/>
                CNPJ: 15.753.536/0001-55
            </p>
        </div>
        </body></html>";
}

// ─── Validação CSRF ───────────────────────────────────────────────────────────

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('inscricao.php');

if (!hash_equals($_SESSION['csrf_token'] ?? '', post('csrf_token'))) {
    redirect('inscricao.php?erro=' . urlencode('Token inválido. Tente novamente.'));
}
unset($_SESSION['csrf_token']);

// ─── Coleta e sanitização ─────────────────────────────────────────────────────

$campos = [
    'nome'         => post('fNome'),
    'cpf'          => preg_replace('/\D/', '', post('fCpf')),
    'rg'           => post('fRg'),
    'dt_nasc_raw'  => post('fDtNascimento'),
    'nome_pai'     => post('fNomePai'),
    'nome_mae'     => post('fNomeMae'),
    'cep'          => preg_replace('/\D/', '', post('fCep')),
    'endereco'     => post('fEndereco'),
    'bairro'       => post('fBairro'),
    'cidade'       => post('fCidade'),
    'estado'       => post('fEstado'),
    'email'        => strtolower(post('fEmail')),
    'email_conf'   => strtolower(post('fEmailConf')),
    'telefone'     => post('fTelefone'),
    'tipo_sangue'  => post('fTipoSangue'),
    'veiculo'      => post('fVeiculo'),
    'categoria'    => post('fCategoria'),
    'participacao' => post('fParticipacao'),

    // Opcionais
    'nav_nome'              => post('fNavegadorNome'),
    'nav_rg'                => post('fNavegadorRg'),
    'nav_tipo_sangue'       => post('fTipoSangueNavegador'),
    'possui_carteira'       => post('fPossuiCarteira'),
    'carteira_valida'       => post('fValida'),
    'num_carteira'          => post('fCarteira'),
    'esp_carro'             => post('fEspecificarCarro'),
    'esp_moto'              => post('fEspecificarMoto'),
    'esp_moto_renovacao'    => post('fEspecificarMotoRenovacao'),
];

$erros = [];

if (empty($campos['nome']))        $erros[] = 'Nome é obrigatório.';
if (!validarCpf($campos['cpf']))   $erros[] = 'CPF inválido.';
if (empty($campos['rg']))          $erros[] = 'RG é obrigatório.';
if (empty($campos['nome_pai']))    $erros[] = 'Nome do pai é obrigatório.';
if (empty($campos['nome_mae']))    $erros[] = 'Nome da mãe é obrigatório.';
if (empty($campos['cep']))         $erros[] = 'CEP é obrigatório.';
if (empty($campos['endereco']))    $erros[] = 'Endereço é obrigatório.';
if (empty($campos['bairro']))      $erros[] = 'Bairro é obrigatório.';
if (empty($campos['cidade']))      $erros[] = 'Cidade é obrigatória.';
if (empty($campos['estado']))      $erros[] = 'Estado é obrigatório.';
if (!filter_var($campos['email'], FILTER_VALIDATE_EMAIL)) $erros[] = 'E-mail inválido.';
if ($campos['email'] !== $campos['email_conf'])            $erros[] = 'Os e-mails não conferem.';
if (empty($campos['telefone']))    $erros[] = 'Telefone é obrigatório.';
if (empty($campos['tipo_sangue'])) $erros[] = 'Tipo sanguíneo é obrigatório.';
if (empty($campos['veiculo']))     $erros[] = 'Veículo é obrigatório.';
if (empty($campos['categoria']))   $erros[] = 'Categoria é obrigatória.';
if ($campos['participacao'] === '') $erros[] = 'Informe se já participou do Rallye.';

// Conversão data dd/mm/aaaa → AAAA-MM-DD
$dtNasc = null;
if (!empty($campos['dt_nasc_raw'])) {
    $parts = explode('/', $campos['dt_nasc_raw']);
    if (count($parts) === 3) {
        $dtNasc = sprintf('%04d-%02d-%02d', (int)$parts[2], (int)$parts[1], (int)$parts[0]);
        if (!checkdate((int)$parts[1], (int)$parts[0], (int)$parts[2])) {
            $erros[] = 'Data de nascimento inválida.';
            $dtNasc = null;
        }
    } else {
        $erros[] = 'Data de nascimento inválida.';
    }
} else {
    $erros[] = 'Data de nascimento é obrigatória.';
}

if (!empty($erros)) {
    redirect('inscricao.php?erro=' . urlencode(implode(' | ', $erros)));
}

// ─── Verifica CPF duplicado ───────────────────────────────────────────────────

$cpfFormatado = preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $campos['cpf']);

try {
    $stmt = db()->prepare('SELECT id FROM inscricoes WHERE cpf = ?');
    $stmt->execute([$cpfFormatado]);
    if ($stmt->fetch()) {
        redirect('inscricao.php?erro=' . urlencode('Este CPF já possui uma inscrição cadastrada.'));
    }
} catch (PDOException $e) {
    error_log('DB check CPF: ' . $e->getMessage());
    redirect('inscricao.php?erro=' . urlencode('Erro ao verificar CPF. Tente novamente.'));
}

// ─── Calcula valor ────────────────────────────────────────────────────────────

$valor = calcularValor($campos['veiculo']);

// ─── Insere no banco ──────────────────────────────────────────────────────────

$sql = "INSERT INTO inscricoes
    (nome, cpf, rg, dt_nascimento, nome_pai, nome_mae,
     cep, endereco, bairro, cidade, estado,
     email, telefone, tipo_sangue,
     veiculo, categoria,
     navegador_nome, navegador_rg, tipo_sangue_navegador,
     possui_carteira, carteira_valida, num_carteira,
     especificar_carro, especificar_moto, especificar_moto_renovacao,
     participacao, valor)
VALUES
    (:nome, :cpf, :rg, :dt_nasc, :nome_pai, :nome_mae,
     :cep, :endereco, :bairro, :cidade, :estado,
     :email, :telefone, :tipo_sangue,
     :veiculo, :categoria,
     :nav_nome, :nav_rg, :nav_ts,
     :possui_carteira, :carteira_valida, :num_carteira,
     :esp_carro, :esp_moto, :esp_moto_ren,
     :participacao, :valor)";

try {
    $pdo = db();
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nome'          => $campos['nome'],
        ':cpf'           => $cpfFormatado,
        ':rg'            => $campos['rg'],
        ':dt_nasc'       => $dtNasc,
        ':nome_pai'      => $campos['nome_pai'],
        ':nome_mae'      => $campos['nome_mae'],
        ':cep'           => $campos['cep'],
        ':endereco'      => $campos['endereco'],
        ':bairro'        => $campos['bairro'],
        ':cidade'        => $campos['cidade'],
        ':estado'        => $campos['estado'],
        ':email'         => $campos['email'],
        ':telefone'      => $campos['telefone'],
        ':tipo_sangue'   => $campos['tipo_sangue'],
        ':veiculo'       => $campos['veiculo'],
        ':categoria'     => $campos['categoria'],
        ':nav_nome'      => $campos['nav_nome']        ?: null,
        ':nav_rg'        => $campos['nav_rg']          ?: null,
        ':nav_ts'        => $campos['nav_tipo_sangue'] ?: null,
        ':possui_carteira'   => $campos['possui_carteira'] !== '' ? (int)$campos['possui_carteira'] : null,
        ':carteira_valida'   => $campos['carteira_valida'] !== '' ? (int)$campos['carteira_valida'] : null,
        ':num_carteira'      => $campos['num_carteira']     ?: null,
        ':esp_carro'         => $campos['esp_carro']        ?: null,
        ':esp_moto'          => $campos['esp_moto']         ?: null,
        ':esp_moto_ren'      => $campos['esp_moto_renovacao'] ?: null,
        ':participacao'  => (int)$campos['participacao'],
        ':valor'         => $valor,
    ]);
    $inscricaoId = (int)$pdo->lastInsertId();
} catch (PDOException $e) {
    error_log('DB insert inscricao: ' . $e->getMessage());
    if (str_contains($e->getMessage(), 'Duplicate entry')) {
        redirect('inscricao.php?erro=' . urlencode('Este CPF já possui uma inscrição cadastrada.'));
    }
    redirect('inscricao.php?erro=' . urlencode('Erro ao salvar inscrição. Tente novamente.'));
}

// ─── Envia e-mail de confirmação ──────────────────────────────────────────────

$emailData = [
    'id'                  => $inscricaoId,
    'nome'                => $campos['nome'],
    'cpf'                 => $cpfFormatado,
    'email'               => $campos['email'],
    'telefone'            => $campos['telefone'],
    'veiculo'             => $campos['veiculo'],
    'categoria'           => $campos['categoria'],
    'valor'               => $valor,
    'navegador_nome'      => $campos['nav_nome'],
    'navegador_rg'        => $campos['nav_rg'],
    'tipo_sangue_navegador' => $campos['nav_tipo_sangue'],
];

Mailer::send(
    $campos['email'],
    'Inscrição confirmada – ' . EVENT_NAME,
    emailConfirmacao($emailData)
);

// ─── Armazena na sessão e redireciona para pagamento ──────────────────────────

$_SESSION['inscricao_id']    = $inscricaoId;
$_SESSION['inscricao_nome']  = $campos['nome'];
$_SESSION['inscricao_valor'] = $valor;

redirect('pagamento.php?id=' . $inscricaoId);
