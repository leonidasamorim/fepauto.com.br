<?php
require_once __DIR__ . '/config.php';
session_start();

// Restaura dados do formulário após erro de validação
$old = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);

// Helpers de repopulação
function old(string $key, string $default = ''): string {
    global $old;
    return htmlspecialchars((string)($old[$key] ?? $default), ENT_QUOTES, 'UTF-8');
}
function oldIs(string $key, string $value): string {
    global $old;
    return (isset($old[$key]) && $old[$key] === $value) ? 'checked' : '';
}
function oldSel(string $key, string $value): string {
    global $old;
    return (isset($old[$key]) && $old[$key] === $value) ? 'selected' : '';
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf_token'];
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Inscrição – XXVI Rallye do Sol 2026</title>
    <link rel="stylesheet" href="inscricao-inc/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="inscricao-inc/css/font-awesome.min.css"/>
    <link rel="stylesheet" href="inscricao-inc/css/style.css"/>
    <link rel="stylesheet" href="inscricao-inc/css/custom.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet"/>
</head>
<body>
<div class="site">
    <div id="main">

        <!-- Banner -->
        <div style="
            width:100%;
            background:url('banner.jpeg') no-repeat center center / cover;
            position:relative;
            min-height:220px;
            display:flex;
            align-items:flex-end;
        ">
            <div style="
                position:absolute;inset:0;
                background:linear-gradient(to bottom, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.65) 100%);
            "></div>
            <div class="container" style="position:relative;z-index:1;padding-bottom:28px;">
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <h2 style="text-shadow:2px 2px 8px #000;font-size:36px;color:#fff;margin:0;"
                            class="page-title text-center">
                            Inscrição – XXVI Rallye do Sol 2026
                        </h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabela de preços -->
        <div class="section pt-4 pb-2">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>Veículo</th>
                                    <th>23/04 – 31/05</th>
                                    <th>01/06 – 15/06</th>
                                    <th>16/06 – 30/06</th>
                                    <th>01/07 – 15/07</th>
                                    <th>16/07 – 22/07</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <th>Carro / UTV</th>
                                    <td>R$ 350,00</td>
                                    <td>R$ 360,00</td>
                                    <td>R$ 370,00</td>
                                    <td>R$ 380,00</td>
                                    <td>R$ 400,00</td>
                                </tr>
                                <tr>
                                    <th>Quadriciclo</th>
                                    <td>R$ 250,00</td>
                                    <td>R$ 260,00</td>
                                    <td>R$ 270,00</td>
                                    <td>R$ 280,00</td>
                                    <td>R$ 300,00</td>
                                </tr>
                                 <tr>
                                    <th>Moto / Iniciante</th>
                                    <td>R$ 140,00</td>
                                    <td>R$ 150,00</td>
                                    <td>R$ 160,00</td>
                                    <td>R$ 170,00</td>
                                    <td>R$ 190,00</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <p class="text-muted small text-center">Arraste para ver todas as datas e valores.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulário -->
        <div class="section pt-2 pb-10">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <h3 class="text-center section-title" style="color:#000;">
                            Preencha o formulário de inscrição abaixo.
                        </h3>
                        <p class="text-center">
                            Após preencher os dados você receberá um e-mail confirmando sua inscrição.
                        </p>

                        <?php if (!empty($_GET['erro'])): ?>
                            <div class="alert alert-danger">
                                <?= htmlspecialchars($_GET['erro']) ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="processa-inscricao.php" class="contact-form" id="form-inscricao">
                            <input type="hidden" name="csrf_token" value="<?= $csrf ?>"/>

                            <!-- Dados pessoais -->
                            <div class="row">
                                <div class="col-md-4">
                                    <label>NOME COMPLETO <span class="required">*</span></label>
                                    <div class="form-wrap">
                                        <input type="text" name="fNome" class="form-control"
                                               placeholder="Nome Completo" required maxlength="255"
                                               value="<?= old('fNome') ?>"/>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label>CPF <span class="required">*</span></label>
                                    <div class="form-wrap">
                                        <input type="text" id="fCpf" name="fCpf" class="form-control"
                                               placeholder="000.000.000-00" required maxlength="14"
                                               value="<?= old('fCpf') ?>"/>
                                    </div>
                                    <span style="color:red;font-size:11px;display:none" id="alert-cpf">
                                        CPF já cadastrado ou inválido!
                                    </span>
                                </div>
                                <div class="col-md-3">
                                    <label>RG <span class="required">*</span></label>
                                    <div class="form-wrap">
                                        <input type="text" name="fRg" class="form-control"
                                               placeholder="Número" required maxlength="30"
                                               value="<?= old('fRg') ?>"/>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label>Data de Nasc. <span class="required">*</span></label>
                                    <div class="form-wrap">
                                        <input type="text" id="data_nasc" name="fDtNascimento"
                                               class="form-control data_nasc"
                                               placeholder="dd/mm/aaaa" required maxlength="10"
                                               value="<?= old('fDtNascimento') ?>"/>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <label>NOME DO PAI <span class="required">*</span></label>
                                    <div class="form-wrap">
                                        <input type="text" name="fNomePai" class="form-control"
                                               placeholder="Nome Completo" required maxlength="255"
                                               value="<?= old('fNomePai') ?>"/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label>NOME DA MÃE <span class="required">*</span></label>
                                    <div class="form-wrap">
                                        <input type="text" name="fNomeMae" class="form-control"
                                               placeholder="Nome Completo" required maxlength="255"
                                               value="<?= old('fNomeMae') ?>"/>
                                    </div>
                                </div>
                            </div>

                            <!-- Endereço -->
                            <div class="row">
                                <div class="col-md-3">
                                    <label>CEP <span class="required">*</span></label>
                                    <div class="form-wrap">
                                        <input type="text" id="zipcode" name="fCep"
                                               class="form-control zipcode"
                                               placeholder="00000-000" required maxlength="9"
                                               value="<?= old('fCep') ?>"/>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <label>ENDEREÇO <span class="required">*</span></label>
                                    <div class="form-wrap">
                                        <input type="text" id="address" name="fEndereco" class="form-control"
                                               placeholder="Rua, Av..." required maxlength="255"
                                               value="<?= old('fEndereco') ?>"/>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label>BAIRRO <span class="required">*</span></label>
                                    <div class="form-wrap">
                                        <input type="text" id="neighborhood" name="fBairro" class="form-control"
                                               placeholder="Bairro" required maxlength="100"
                                               value="<?= old('fBairro') ?>"/>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <label>CIDADE <span class="required">*</span></label>
                                    <div class="form-wrap">
                                        <input type="text" id="city" name="fCidade" class="form-control"
                                               placeholder="Cidade" required maxlength="100"
                                               value="<?= old('fCidade') ?>"/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label>ESTADO <span class="required">*</span></label>
                                    <div class="form-wrap">
                                        <input type="text" id="state" name="fEstado" class="form-control"
                                               placeholder="UF" required maxlength="50"
                                               value="<?= old('fEstado') ?>"/>
                                    </div>
                                </div>
                            </div>

                            <!-- Contato -->
                            <div class="row">
                                <div class="col-md-6">
                                    <label>E-MAIL <span class="required">*</span></label>
                                    <div class="form-wrap">
                                        <input type="email" id="fEmail" name="fEmail" class="form-control"
                                               placeholder="seu@email.com" required maxlength="255"
                                               value="<?= old('fEmail') ?>"/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label>CONFIRMAR E-MAIL <span class="required">*</span></label>
                                    <div class="form-wrap">
                                        <input type="email" id="fEmailConf" name="fEmailConf" class="form-control"
                                               placeholder="seu@email.com" required maxlength="255"
                                               value="<?= old('fEmailConf') ?>"/>
                                    </div>
                                    <span id="msg-email-conf"
                                          style="display:none;color:#c0392b;font-size:12px">
                                        E-mail e confirmação não conferem.
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <label>CELULAR / WHATSAPP <span class="required">*</span></label>
                                    <div class="form-wrap">
                                        <input type="text" name="fTelefone" class="form-control whatsapp"
                                               placeholder="(00) 00000-0000" required maxlength="20"
                                               value="<?= old('fTelefone') ?>"/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label>TIPO SANGUÍNEO <span class="required">*</span></label>
                                    <div class="form-wrap" style="display:flex;flex-wrap:wrap;gap:6px 14px;padding-top:6px">
                                        <?php foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $ts): ?>
                                            <label style="font-weight:normal;white-space:nowrap;margin:0;display:inline-flex;align-items:center;gap:4px">
                                                <input type="radio" name="fTipoSangue" value="<?= $ts ?>"
                                                       required <?= oldIs('fTipoSangue', $ts) ?>/>
                                                <?= $ts ?>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>

                            <hr/>

                            <!-- Veículo e categoria -->
                            <div class="row">
                                <div class="col-md-6 mt-2">
                                    <label>VEÍCULO <span class="required">*</span></label>
                                    <div class="form-wrap">
                                        <select class="form-control" id="fVeiculo" name="fVeiculo" required>
                                            <option value="">-- Selecione --</option>
                                            <option value="Carro"       <?= oldSel('fVeiculo','Carro') ?>>Carro / UTV</option>
                                            <option value="Moto"        <?= oldSel('fVeiculo','Moto') ?>>Moto</option>
                                            <option value="Quadriciclo" <?= oldSel('fVeiculo','Quadriciclo') ?>>Quadriciclo</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 mt-2">
                                    <label>CATEGORIA <span class="required">*</span></label>
                                    <div class="form-wrap">
                                        <select id="fCategoria" class="form-control" name="fCategoria">
                                            <option value="">-- Selecione um Veículo --</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Dados do navegador (apenas Carro/UTV) -->
                            <div class="row mt-3" id="info-navegador" style="display:none;
                                background:#f5f5f5;padding:12px 0 14px;border-radius:4px;">
                                <div class="col-12"><legend style="margin-left:10px">Dados do Navegador</legend></div>
                                <div class="col-md-4">
                                    <label>NOME <span class="required">*</span></label>
                                    <div class="form-wrap">
                                        <input type="text" id="fNavegadorNome" name="fNavegadorNome"
                                               class="form-control" maxlength="255"
                                               value="<?= old('fNavegadorNome') ?>"/>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label>RG <span class="required">*</span></label>
                                    <div class="form-wrap">
                                        <input type="text" id="fNavegadorRg" name="fNavegadorRg"
                                               class="form-control" maxlength="30"
                                               value="<?= old('fNavegadorRg') ?>"/>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label>TIPO SANGUÍNEO <span class="required">*</span></label>
                                    <div class="form-wrap" style="display:flex;flex-wrap:wrap;gap:6px 14px;padding-top:6px">
                                        <?php foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $ts): ?>
                                            <label style="font-weight:normal;white-space:nowrap;margin:0;display:inline-flex;align-items:center;gap:4px">
                                                <input type="radio" name="fTipoSangueNavegador"
                                                       class="fTipoSangueNavegador" value="<?= $ts ?>"
                                                       <?= oldIs('fTipoSangueNavegador', $ts) ?>/>
                                                <?= $ts ?>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Carteira da federação -->
                            <div class="row mt-2" id="box-possui-carteira" style="display:none">
                                <div class="col-md-6 mt-2">
                                    <label>Possui Carteira da Federação? <span class="required">*</span></label>
                                    <div class="form-wrap">
                                        <label class="mr-4" style="font-weight:normal;font-size:18px;">
                                            Sim <input type="radio" class="fPossuiCarteira"
                                                       name="fPossuiCarteira" value="1"
                                                       <?= oldIs('fPossuiCarteira','1') ?>/>
                                        </label>
                                        <label style="font-weight:normal;font-size:18px;">
                                            Não <input type="radio" class="fPossuiCarteira"
                                                       name="fPossuiCarteira" value="0"
                                                       <?= oldIs('fPossuiCarteira','0') ?>/>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 mt-2 box-carteira-valida" style="display:none">
                                    <label>A carteira é válida para o ano de 2026? <span class="required">*</span></label>
                                    <div class="form-wrap">
                                        <label class="mr-4" style="font-weight:normal;font-size:18px;">
                                            Sim <input type="radio" class="fCarteiraValida valida-sim vigente"
                                                       name="fValida" value="1"
                                                       <?= oldIs('fValida','1') ?>/>
                                        </label>
                                        <label style="font-weight:normal;font-size:18px;">
                                            Não <input type="radio" id="fCarteiraValida"
                                                       class="fCarteiraValida valida-nao vigente"
                                                       name="fValida" value="0"
                                                       <?= oldIs('fValida','0') ?>/>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="row" id="box-num-carteira" style="display:none">
                                <div class="col-md-6">
                                    <label>Nº CARTEIRA DA FEDERAÇÃO <span class="required">*</span></label>
                                    <div class="form-wrap">
                                        <input type="text" name="fCarteira" id="fCarteira"
                                               class="form-control" maxlength="50"
                                               value="<?= old('fCarteira') ?>"/>
                                    </div>
                                </div>
                            </div>

                            <div class="row" id="box-especificar-carro-renovacao" style="display:none">
                                <div class="col-md-6">
                                    <label>ESPECIFICAÇÃO <span class="required">*</span></label>
                                    <div class="form-wrap">
                                        <select id="fEspecificarCarro" class="form-control" name="fEspecificarCarro">
                                            <option value="">-- Selecione --</option>
                                            <option value="Renovação – provas somente no Pará"
                                                    <?= oldSel('fEspecificarCarro','Renovação – provas somente no Pará') ?>>
                                                Renovação – Provas somente no estado do Pará
                                            </option>
                                            <option value="Renovação – outros estados ou provas nacionais"
                                                    <?= oldSel('fEspecificarCarro','Renovação – outros estados ou provas nacionais') ?>>
                                                Renovação – Outros estados ou provas nacionais
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row" id="box-especificar-moto" style="display:none">
                                <div class="col-md-6">
                                    <label>ESPECIFICAÇÃO <span class="required">*</span></label>
                                    <div class="form-wrap">
                                        <select id="fEspecificarMoto" class="form-control" name="fEspecificarMoto">
                                            <option value="">-- Selecione --</option>
                                            <option value="Nunca teve carteira CBM – participará somente do Rallye"
                                                    <?= oldSel('fEspecificarMoto','Nunca teve carteira CBM – participará somente do Rallye') ?>>
                                                Nunca teve carteira CBM e participará somente do Rallye
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row" id="box-especificar-moto-renovacao" style="display:none">
                                <div class="col-md-6">
                                    <label>ESPECIFICAÇÃO <span class="required">*</span></label>
                                    <div class="form-wrap">
                                        <select id="fEspecificarMotoRenovacao" class="form-control"
                                                name="fEspecificarMotoRenovacao">
                                            <option value="">-- Selecione --</option>
                                            <option value="Renovação"
                                                    <?= oldSel('fEspecificarMotoRenovacao','Renovação') ?>>
                                                Renovação
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Valor carteira nova – Carro -->
                            <div id="valor-carteira-carro" class="row mt-1" style="display:none">
                                <div class="col-md-12">
                                    <div class="alert alert-info mb-1">
                                        <strong>+ Carteira nova (Carro/UTV):</strong>
                                        R$ <?= number_format(CARTEIRA_CARRO, 2, ',', '.') ?> será somado ao valor da inscrição.
                                    </div>
                                </div>
                            </div>

                            <!-- Valor carteira nova – Moto/Quadriciclo -->
                            <div id="valor-carteira-moto-quadriciculo" class="row mt-1" style="display:none">
                                <div class="col-md-12">
                                    <div class="alert alert-info mb-1">
                                        <strong>+ Carteira nova (Moto/Quadriciclo):</strong>
                                        R$ <?= number_format(CARTEIRA_MOTO, 2, ',', '.') ?> será somado ao valor da inscrição.
                                    </div>
                                </div>
                            </div>

                            <!-- Valor renovação – Carro -->
                            <div id="valor-carteira-carro-renovacao" class="row mt-1" style="display:none">
                                <div class="col-md-12">
                                    <div class="alert alert-warning mb-1">
                                        <strong>+ Renovação de Carteira (Carro/UTV):</strong>
                                        R$ <?= number_format(CARTEIRA_CARRO_RENOVACAO, 2, ',', '.') ?> será somado ao valor da inscrição.
                                        <small class="d-block mt-1">⚠ Valor exclusivo para as categorias <strong>PTRR1</strong> e <strong>PNRR</strong>.</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Valor renovação – Moto/Quadriciclo -->
                            <div id="valor-carteira-moto-quadriciculo-renovacao" class="row mt-1" style="display:none">
                                <div class="col-md-12">
                                    <div class="alert alert-warning mb-1">
                                        <strong>+ Renovação de Carteira (Moto/Quadriciclo):</strong>
                                        R$ <?= number_format(CARTEIRA_MOTO_RENOVACAO, 2, ',', '.') ?> será somado ao valor da inscrição.
                                    </div>
                                </div>
                            </div>

                            <!-- Alertas de carteira -->
                            <div class="row" id="alert-possui-carteira" style="display:none">
                                <div class="col-md-12 alert alert-warning mt-2">
                                    <strong style="color:red">ATENÇÃO:</strong>
                                    Seu nome será checado em nossa base de dados. Caso não conste informação sobre
                                    filiação na federação, sua inscrição no XXVI Rallye do Sol será rejeitada.
                                </div>
                            </div>

                            <div class="row" id="alert-nao-possui-carteira" style="display:none">
                                <div class="col-md-12 alert alert-warning mt-2">
                                    <strong style="color:red">ATENÇÃO:</strong>
                                    Seu nome será checado em nossa base de dados. Se já constar filiação anterior,
                                    sua inscrição como novato será rejeitada.
                                </div>
                            </div>

                            <!-- Participação + Forma de pagamento -->
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <label>Já participou do Rallye do Sol? <span class="required">*</span></label>
                                    <div class="form-wrap">
                                        <select class="form-control" name="fParticipacao" required>
                                            <option value="">-- Selecione --</option>
                                            <option value="1" <?= oldSel('fParticipacao','1') ?>>Sim</option>
                                            <option value="0" <?= oldSel('fParticipacao','0') ?>>Não</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label>FORMA DE PAGAMENTO <span class="required">*</span></label>
                                    <div class="form-wrap">
                                        <select class="form-control" id="fFormaPagamento"
                                                name="fFormaPagamento" required>
                                            <option value="">-- Selecione --</option>
                                            <option value="pix"    <?= oldSel('fFormaPagamento','pix') ?>>PIX</option>
                                            <option value="cartao" <?= oldSel('fFormaPagamento','cartao') ?>>Cartão de Crédito / Débito</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Aviso sobre documentação -->
                            <div class="row pt-2 mt-2" id="v-atencao" style="display:none">
                                <div class="col-md-12 alert alert-warning">
                                    <strong style="color:red">ATENÇÃO:</strong><br/>
                                    I. Envie cópia da Habilitação para o e-mail
                                    <strong>fepauto@fepauto.com.br</strong><br/>
                                    <span id="v-atestado-carro" style="display:none">
                                    II. Envie também o <strong>Atestado Médico</strong> para o e-mail
                                    <strong>fepauto@fepauto.com.br</strong><br/>
                                    </span>
                                    III. No dia da entrega do kit do XXVI Rallye do Sol o inscrito deverá
                                    apresentar comprovante de pagamento.
                                </div>
                            </div>

                            <!-- Botão -->
                            <div class="row mt-3">
                                <div class="col-md-12 text-center">
                                    <button type="submit" id="btn-submit" class="btn btn-lg" style="background:linear-gradient(135deg,#f5a623 0%,#e8490a 100%);color:#fff;border:none;font-weight:700;">
                                        ENVIAR INSCRIÇÃO
                                    </button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div><!-- #main -->

    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <span style="color:#fff">FEPAUTO – Federação Paraense de Automobilismo<br/>
                    CNPJ: 15.753.536/0001-55</span>
                </div>
            </div>
        </div>
    </footer>

    <div class="copyright">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    Copyright &copy; <?= date('Y') ?> Fepauto
                </div>
            </div>
        </div>
    </div>
</div>

<script src="inscricao-inc/js/jquery.min.js"></script>
<script src="inscricao-inc/js/bootstrap.min.js"></script>
<script src="inscricao-inc/js/jquery.mask.js"></script>
<script src="inscricao-inc/js/general.js?v=4"></script>

<?php if (!empty($old)): ?>
<script>
// Restaura estado dinâmico do formulário após erro de validação
$(function () {
    var fd = <?= json_encode([
        'veiculo'        => $old['fVeiculo']        ?? '',
        'categoria'      => $old['fCategoria']      ?? '',
        'possuiCarteira' => $old['fPossuiCarteira'] ?? '',
        'valida'         => $old['fValida']          ?? '',
        'formaPagamento' => $old['fFormaPagamento'] ?? '',
    ], JSON_HEX_TAG | JSON_HEX_APOS) ?>;

    if (fd.veiculo) {
        // Dispara change do veículo: mostra seções corretas e carrega categorias via AJAX
        $('#fVeiculo').trigger('change');

        // Seleciona a categoria após o AJAX carregar (~700 ms)
        if (fd.categoria) {
            setTimeout(function () {
                $('#fCategoria').val(fd.categoria);
            }, 700);
        }
    }

    if (fd.possuiCarteira !== '') {
        $('input.fPossuiCarteira[value="' + fd.possuiCarteira + '"]').trigger('change');
    }

    if (fd.valida !== '') {
        $('input.fCarteiraValida[value="' + fd.valida + '"]').trigger('change');
    }

    if (fd.formaPagamento) {
        $('#fFormaPagamento').trigger('change');
    }
});
</script>
<?php endif; ?>
</body>
</html>
