$(document).ready(function () {

    // ─── Veículo → carrega categorias ────────────────────────────────────────
    $('#fVeiculo').change(function () {
        var veiculo = $(this).val();

        // Esconde todos os blocos condicionais
        $('#info-navegador').hide();
        $('#box-possui-carteira').hide();
        $('#box-num-carteira').hide();
        $('#box-especificar-carro-renovacao').hide();
        $('#box-especificar-moto').hide();
        $('#box-especificar-moto-renovacao').hide();
        $('#valor-carteira-carro').hide();
        $('#valor-carteira-moto-quadriciculo').hide();
        $('#alert-possui-carteira').hide();
        $('#alert-nao-possui-carteira').hide();
        $('#v-atencao').hide();

        $('.fPossuiCarteira').prop('checked', false);
        $('.fCarteiraValida').prop('checked', false);
        limparRequiredEspecificar();

        $('#fCategoria').html('<option value="">-- Selecione uma Categoria --</option>');

        if (!veiculo) return;

        $('#v-atencao').show();
        $('#box-possui-carteira').show();
        $('#fCategoria').prop('required', true);

        if (veiculo === 'Carro') {
            $('#info-navegador').show();
            $('#fNavegadorNome').prop('required', true);
            $('#fNavegadorRg').prop('required', true);
            $('.fTipoSangueNavegador').prop('required', true);
            $('#fCategoria').load('categoria-carro-v2.php');
        }

        if (veiculo === 'Moto') {
            $('#fCategoria').load('categoria-moto-v2.php');
            removerRequiredNavegador();
        }

        if (veiculo === 'Quadriciclo') {
            $('#fCategoria').load('categoria-quadriciclo-v2.php');
            removerRequiredNavegador();
        }
    });

    // ─── Possui carteira ──────────────────────────────────────────────────────
    $('.fPossuiCarteira').on('change', function () {
        var possui = $(this).val();
        var veiculo = $('#fVeiculo').val();

        limparRequiredEspecificar();
        escondeBoxesCarteira();
        $('.fCarteiraValida').prop('checked', false);

        if (possui == 1) {
            $('.box-carteira-valida').show();
            $('#alert-possui-carteira').show();
            $('#alert-nao-possui-carteira').hide();
            $('#box-num-carteira').show();
            $('#fCarteiraValida').prop('required', true);
            $('#fCarteira').prop('required', true);
        } else {
            $('.box-carteira-valida').hide();
            $('#alert-nao-possui-carteira').show();
            $('#alert-possui-carteira').hide();
            $('#fCarteira').prop('required', false);
            $('#fCarteiraValida').prop('required', false);

            if (veiculo === 'Carro') {
                $('#valor-carteira-carro').show();
            }
            if (veiculo === 'Moto' || veiculo === 'Quadriciclo') {
                $('#box-especificar-moto').show();
                $('#valor-carteira-moto-quadriciculo').show();
                $('#fEspecificarMoto').prop('required', true);
            }
        }
    });

    // ─── Validade carteira ────────────────────────────────────────────────────
    $('.fCarteiraValida').on('change', function () {
        var valida = $(this).val();
        var veiculo = $('#fVeiculo').val();

        escondeBoxesCarteira();
        limparRequiredEspecificar();

        if (valida != 1) {
            if (veiculo === 'Carro') {
                $('#box-especificar-carro-renovacao').show();
                $('#valor-carteira-carro-renovacao').show();
                $('#fEspecificarCarro').prop('required', true);
            }
            if (veiculo === 'Moto' || veiculo === 'Quadriciclo') {
                $('#box-especificar-moto-renovacao').show();
                $('#valor-carteira-moto-quadriciculo-renovacao').show();
                $('#fEspecificarMotoRenovacao').prop('required', true);
            }
        } else {
            $('#box-num-carteira').show();
        }
    });

    // ─── Confirmação de e-mail ────────────────────────────────────────────────
    $('#fEmailConf').on('blur', function () {
        if ($('#fEmail').val() !== $(this).val()) {
            alert('E-mail e confirmação não conferem!');
            $(this).val('').focus();
        }
    });

    // ─── Validação CPF via AJAX ───────────────────────────────────────────────
    $('#fCpf').on('blur', function () {
        var cpf = $(this).val().replace(/\D/g, '');
        $('#alert-cpf').hide();
        if (cpf.length !== 11) return;

        $.getJSON('valida-cpf.php?cpf=' + cpf, function (data) {
            if (!data.valido) {
                $('#alert-cpf').show();
                $('#fCpf').val('').focus();
            }
        });
    });

    // ─── Consulta CEP (ViaCEP) ────────────────────────────────────────────────
    $('#zipcode').on('blur', function () {
        var cep = $(this).val().replace(/\D/g, '');
        if (cep.length !== 8) return;

        $('#address, #neighborhood, #city, #state').val('...');

        $.getJSON('https://viacep.com.br/ws/' + cep + '/json/', function (dados) {
            if (dados.erro) {
                $('#address, #neighborhood, #city, #state').val('');
                alert('CEP não encontrado.');
                return;
            }
            $('#address').val(dados.logradouro || '');
            $('#neighborhood').val(dados.bairro || '');
            $('#city').val(dados.localidade || '');
            $('#state').val(dados.uf || '');
            $('#address').focus();
        }).fail(function () {
            $('#address, #neighborhood, #city, #state').val('');
            alert('Erro ao consultar CEP. Preencha manualmente.');
        });
    });

    // ─── Máscaras ─────────────────────────────────────────────────────────────
    $('.whatsapp').mask('(00) 00000-0000');
    $('.zipcode').mask('00000-000');
    $('.data_nasc').mask('00/00/0000');
    $('#fCpf').mask('000.000.000-00');

    // ─── Helpers ──────────────────────────────────────────────────────────────
    function limparRequiredEspecificar() {
        $('#fEspecificarCarro, #fEspecificarMoto, #fEspecificarMotoRenovacao')
            .prop('required', false);
        $('#fCarteira, #fCarteiraValida').prop('required', false);
    }

    function removerRequiredNavegador() {
        $('#fNavegadorNome, #fNavegadorRg').prop('required', false);
        $('.fTipoSangueNavegador').prop('required', false);
    }

    function escondeBoxesCarteira() {
        $('#box-especificar-carro-renovacao, #box-especificar-moto, #box-especificar-moto-renovacao').hide();
        $('#valor-carteira-carro, #valor-carteira-moto-quadriciculo').hide();
        $('#valor-carteira-carro-renovacao, #valor-carteira-moto-quadriciculo-renovacao').hide();
        $('#box-num-carteira').hide();
    }
});
