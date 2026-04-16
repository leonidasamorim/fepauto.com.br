var _____WB$wombat$assign$function_____=function(name){return (self._wb_wombat && self._wb_wombat.local_init && self._wb_wombat.local_init(name))||self[name];};if(!self.__WB_pmw){self.__WB_pmw=function(obj){this.__WB_source=obj;return this;}}{
let window = _____WB$wombat$assign$function_____("window");
let self = _____WB$wombat$assign$function_____("self");
let document = _____WB$wombat$assign$function_____("document");
let location = _____WB$wombat$assign$function_____("location");
let top = _____WB$wombat$assign$function_____("top");
let parent = _____WB$wombat$assign$function_____("parent");
let frames = _____WB$wombat$assign$function_____("frames");
let opens = _____WB$wombat$assign$function_____("opens");
$(document).ready(function () {

    $('#fVeiculo').change(function () {

        var veiculo = $(this).val();

        $('#box-especificar-moto').hide();
        $('#box-num-carteira').hide();
        $('#box-especificar-carro-renovacao').hide();
        $('#valor-carteira-carro').hide();
        $('#valor-carteira-moto-quadriciculo').hide();

        $(".fPossuiCarteira").removeAttr("checked");
        $(".fCarteiraValida").removeAttr("checked");

        limparRequestSelectEspecificar();

        if (veiculo) {
            $('#v-atencao').show();
            $("#fCategoria").prop('required', true);
            $('#box-possui-carteira').show();
        }

        if (veiculo == 'Carro') {
            $('#info-navegador').show();

            if ($('.valida-nao').attr('checked') == 'true' || $('.valida-nao').attr('checked') == true) {
                $('#fValorQuadriciclo').hide();
            }
            $('#fCategoria').load('categoria-carro-v2.php');


            $("#fNavegadorNome").prop('required',true);
            $("#fNavegadorRg").prop('required',true);
            $(".fTipoSangueNavegador").prop('required',true);


        }

        if (veiculo == 'Moto') {
            $('#fCategoria').load('categoria-moto-v2.php');
            $('#info-navegador').hide();
        }

        if (veiculo == 'Quadriciclo') {
            $('#fCategoria').load('categoria-quadriciclo-v2.php');
            $('#info-navegador').hide();
        }


    });


    $('.fPossuiCarteira').change(function () {
        var possui_carteira = $(this).val();
        var veiculo = $('#fVeiculo').val();

        limparRequestSelectEspecificar();

        //carro
        $('#valor-carteira-carro').hide();
        $('#box-especificar-carro-renovacao').hide();
        $('#valor-carteira-carro-renovacao').hide();
        $('#valor-carteira-carro-renovacao-preco').hide();

        //moto
        $('#box-especificar-moto').hide();
        $('#valor-carteira-moto-quadriciculo').hide();
        $('#box-especificar-moto-renovacao').hide();
        $('#valor-carteira-moto-quadriciculo-renovacao').hide();

        $(".fCarteiraValida").removeAttr("checked");


        if (possui_carteira == 1) {
            $('.box-carteira-valida').show();
            $('#alert-possui-carteira').show();
            $('#alert-nao-possui-carteira').hide();
            $('#box-num-carteira').show();
            $("#fCarteiraValida").prop('required',true);
            $("#fCarteira").prop('required',true);

        } else {

            $("#fCarteiraValida").prop('required',false);
            $("#fCarteira").prop('required',false);


            $('.box-carteira-valida').hide();
            $('#alert-possui-carteira').hide();
            $('#alert-nao-possui-carteira').show();
            $('#box-num-carteira').hide();

            if (veiculo == 'Carro') {
                $('#valor-carteira-carro').show();
            }

            if (veiculo == 'Moto' || veiculo == 'Quadriciclo') {

                $('#box-especificar-moto').show();
                $('#valor-carteira-moto-quadriciculo').show();
                $("#fEspecificarMoto").prop('required',true);

            }

        }
    });

    $('.fCarteiraValida').change(function () {
        var carteira_valida_ano = $(this).val();
        var veiculo = $('#fVeiculo').val();

        //carro
        $('#valor-carteira-carro').hide();
        $('#box-especificar-carro-renovacao').hide();
        $('#valor-carteira-carro-renovacao').hide();
        $('#valor-carteira-carro-renovacao-preco').hide();

        //moto
        $('#box-especificar-moto').hide();
        $('#valor-carteira-moto-quadriciculo').hide();
        $('#box-especificar-moto-renovacao').hide();
        $('#valor-carteira-moto-quadriciculo-renovacao').hide();

        $("#fEspecificarCarro").prop('required',false);
        $("#fEspecificarMoto").prop('required',false);

        if (carteira_valida_ano != 1) {

            if (veiculo == 'Carro') {
                $('#box-especificar-carro-renovacao').show();
                $('#valor-carteira-carro-renovacao').show();
                $('#valor-carteira-carro-renovacao-preco').show();
                $("#fEspecificarCarro").prop('required',true);

            }
            if (veiculo == 'Moto' || veiculo == 'Quadriciclo') {
                $('#box-especificar-moto-renovacao').show();
                $('#valor-carteira-moto-quadriciculo-renovacao').show();
                $("#fEspecificarMotoRenovacao").prop('required',true);
            }


        } else {

            $('#box-num-carteira').show();
        }

    });

    function limparRequestSelectEspecificar() {
        $("#fEspecificarCarro").prop('required',false);
        $("#fEspecificarMotoRenovacao").prop('required',false);
        $("#fEspecificarMoto").prop('required',false);
        $("#fCarteiraValida").prop('required',false);
        $("#fCarteira").prop('required',false);
        $("#fNavegadorNome").prop('required',false);
        $("#fNavegadorRg").prop('required',false);
        $(".fTipoSangueNavegador").prop('required',false);

    }

    $('#fEspecificarCarro').change(function () {
        var valor_carro_renovacao = $(this).val();
        if (valor_carro_renovacao) {
            $('#valor-carteira-carro-renovacao-preco').html(parseFloat(valor_carro_renovacao).toFixed(2));
        }else{
            $('#valor-carteira-carro-renovacao-preco').html('');
        }

    });


    $('#fEmailConf').change(function () {
        var fEmailConf = $(this).val();
        var fEmail = $("#fEmail").val();

        if (fEmail != fEmailConf) {
            $('#fEmailConf').val('');
            alert('E-mail e confirmação não conferem!');
            $('#fEmailConf').focus();
        }

    });


    $('#fCpf').click(function () {
        $('#alert-cpf').hide();
    });


    $('#fCpf').change(function () {
        var cpf = $(this).val();
        $('#alert-cpf').hide();

        $.getJSON("https://web.archive.org/web/20230425215534/http://www.fepauto.com.br/wp-content/themes/Womack/validaCPF.php?tipo=rallye&validateId=fCpf&validateValue=" + cpf, function (dados) {
            if (!("erro" in dados)) {
                if (dados["jsonValidateReturn"][2] != "true") {
                    $('#alert-cpf').show();
                }
            } //end if.
            else {
                alert('Não foi possível processar seu cpf, informe novamente.');
                $('#fCpf').focus();
            }
        });

    });



    var behavior = function (val) {
            return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
        },
        options = {
            onKeyPress: function (val, e, field, options) {
                field.mask(behavior.apply({}, arguments), options);
            }
        };

    $(".phone").mask(behavior, options);
    $(".whatsapp").mask("(99) 99999-9999");
    $(".zipcode").mask("99999-999");
    $(".data_nasc").mask("99/99/9999");


    var options = {
        onKeyPress: function (cpfcnpj, e, field, options) {
            var masks = ['000.000.000-009', '00.000.000/0000-00'];
            var mask = (cpfcnpj.length > 14) ? masks[1] : masks[0];
            $('#cpf_cnpj').mask(mask, options);
        }
    };

    $('#fCpf').mask('000.000.000-009', options);


    //Consulta de cep
    function limpa_formulário_cep() {
        // Limpa valores do formulário de cep.
        $("#address").val("");
        $("#neighborhood").val("");
        $("#city").val("");
        $("#state").val("");
    }

    //Quando o campo cep perde o foco.
    $("#zipcode").blur(function () {

        //Nova variável "cep" somente com dígitos.
        var cep = $(this).val().replace(/\D/g, '');

        //Verifica se campo cep possui valor informado.
        if (cep != "") {

            //Expressão regular para validar o CEP.
            var validacep = /^[0-9]{8}$/;

            //Valida o formato do CEP.
            if (validacep.test(cep)) {

                //Preenche os campos com "..." enquanto consulta webservice.
                $("#address").val("...");
                $("#neighborhood").val("...");
                $("#city").val("...");
                $("#state").val("...");

                //Consulta o webservice viacep.com.br/
                $.getJSON("https://web.archive.org/web/20230425215534/https://viacep.com.br/ws/" + cep + "/json/?callback=?", function (dados) {

                    if (!("erro" in dados)) {
                        //Atualiza os campos com os valores da consulta.
                        $("#address").val(dados.logradouro);
                        $("#neighborhood").val(dados.bairro);
                        $("#city").val(dados.localidade);
                        $("#state").val(dados.uf);
                        $("#address").focus();
                    } //end if.
                    else {
                        //CEP pesquisado não foi encontrado.
                        limpa_formulário_cep();
                        alert("CEP não encontrado.");
                    }
                });
            } //end if.
            else {
                //cep é inválido.
                limpa_formulário_cep();
                alert("Formato de CEP inválido.");
            }
        } //end if.
        else {
            //cep sem valor, limpa formulário.
            limpa_formulário_cep();
        }
    });


});

}

/*
     FILE ARCHIVED ON 21:55:34 Apr 25, 2023 AND RETRIEVED FROM THE
     INTERNET ARCHIVE ON 12:24:26 Apr 15, 2026.
     JAVASCRIPT APPENDED BY WAYBACK MACHINE, COPYRIGHT INTERNET ARCHIVE.

     ALL OTHER CONTENT MAY ALSO BE PROTECTED BY COPYRIGHT (17 U.S.C.
     SECTION 108(a)(3)).
*/
/*
playback timings (ms):
  capture_cache.get: 0.385
  load_resource: 581.762
  PetaboxLoader3.resolve: 359.105
  PetaboxLoader3.datanode: 221.302
*/