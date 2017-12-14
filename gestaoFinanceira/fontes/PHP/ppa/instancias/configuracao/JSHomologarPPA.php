<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
  * Arquivo de Java Script do Homologar PPA
  * Data de Criação: 26/09/2008
  *
  * @author Analista: Heleno Menezes dos Santos
  * @author Desenvolvedor: Janilson Mendes P. da Silva
  * @package URBEM
  * @subpackage
  * @ignore
  * Casos de uso:0
  $Id:$
  */
?>
<script type="text/javascript">
function buscaValor(tipoBusca)
{
    var stTraget = document.frm.target;
    var stAction = document.frm.action;

    document.frm.stCtrl.value = tipoBusca;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.action = stAction;
    document.frm.target = stTraget;

    setTimeout("document.frm.stCtrl.value = 'cadastrarHomologacao'",500);
}

function validarFormulario()
{
    var stMensagem = '';
    var stCampo    = null;
    var boErro     = false;

    stCampo = document.getElementById('inCodPPA');
    if (stCampo) {
        if (trim(stCampo.value) == "") {
            boErro = true;
            stMensagem = '@Campo PPA inválido!';

            return alertaAviso(stMensagem, 'form', 'erro', '&iURLRandomica=20090219162437.022', '../')
        }
    }

    stCampo = document.getElementById('dtDataLegislativo');
    if (stCampo) {
        if (trim(stCampo.value) == "") {
            boErro = true;
            stMensagem = '@Informe a Data de Encaminhamento Legislativo!';

            return alertaAviso(stMensagem, 'form', 'erro', '&iURLRandomica=20090219162437.022', '../')
        }
    }

    stCampo = document.getElementById('inProtocolo');
    if (stCampo) {
        if (trim(stCampo.value) == "") {
            boErro = true;
            stMensagem = '@Informe o Número de Protocolo!';

            return alertaAviso(stMensagem, 'form', 'erro', '&iURLRandomica=20090219162437.022', '../')
        }
    }

    stCampo = document.getElementById('dtDataDevolucaoExecutivo');
    if (stCampo) {
        if (trim(stCampo.value) == "") {
            boErro = true;
            stMensagem = '@Informe a Data de Devolução ao Executivo!';

            return alertaAviso(stMensagem, 'form', 'erro', '&iURLRandomica=20090219162437.022', '../')
        }
    }

    stCampo = document.getElementsByName('inCodTp');
    if (stCampo.length > 0) {
        if (stCampo[0].value == "") {
            boErro = true;
            stMensagem = '@Selecione um Tipo do Veículo de Publicação!';

            return alertaAviso(stMensagem, 'form', 'erro', '&iURLRandomica=20090219162437.022', '../')
        }
    }

    stCampo = document.getElementsByName('inCodEmpresa');
    if (stCampo.length > 0) {
        if (stCampo[0].value == "") {
            boErro = true;
            stMensagem = '@Selecione um Nome do Veículo de Publicação!';

            return alertaAviso(stMensagem, 'form', 'erro', '&iURLRandomica=20090219162437.022', '../')
        }
    }

    stCampo = document.getElementById('inPeriodicidadeApuracaoMetas');
    if (stCampo) {
        if (trim(stCampo.value) == "") {
            boErro = true;
            stMensagem = '@Informe a Periodicidade de Apuracao das Metas!';

            return alertaAviso(stMensagem, 'form', 'erro', '&iURLRandomica=20090219162437.022', '../')
        }
    }

    stCampo = document.getElementById('inCodNorma');
    if (stCampo) {
        if (trim(stCampo.value) == "") {
            boErro = true;
            stMensagem = '@Campo Número da Norma inválido!';

            return alertaAviso(stMensagem, 'form', 'erro', '&iURLRandomica=20090219162437.022', '../')
        }
    }

    if (!boErro) {
        var stTarget = document.frm.target;
        var stAction = document.frm.action;

        document.frm.stCtrl.value = 'cadastrarHomologacao';
        document.frm.target = 'oculto';
        document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
        document.frm.submit();
        document.frm.action = stAction;
        document.frm.target = stTarget;
    }
}

function mascaraProtocolo(obj,funcao)
{
    v_obj = obj;
    v_fun = funcao;
    setTimeout("execMascara()",1);
}

function execMascara()
{
    v_obj.value = v_fun(v_obj.value);
}

function soNumeros(obj)
{
    novo = obj.replace(/\D/g,"");

    return novo;
}

function verificaProtocolo(obj)
{
    obj = obj.replace(/\D/g,"")
    obj = obj.replace(/(\d{6})(\d)/,"$1/$2")

    return obj;
}

/**
 * Limpa o campo do PPA. So usuário escolher a opção "sim"
 * no popup de confirmação de continuar a homologação do PPA,
 * o campo volta a opção original e continua o cadastro,
 * montando o spnHomologacaoPPA
 */
function limparCampoPPA()
{
    $('inCodPPATxt').value = '';
    $('inCodPPA').value = '';
}

function limparSpanPPA()
{
    $('spnHomologacaoPPA').innerHTML = '';
}

function validaDataEncaminhamento()
{
    dtEncaminhamento = jq('#dtDataLegislativo').val();
    arDtEncaminhamento = dtEncaminhamento.split('/');

    dtDevolucao = jq('#dtDataDevolucaoExecutivo').val();
    arDtDevolucao = dtDevolucao.split('/');

    stAnoInicio = jq('#stAnoInicio').val();
    stAnoFinal = jq('#stAnoFinal').val();

    if (arDtEncaminhamento[2] > stAnoFinal) {
        jq('#dtDataLegislativo').val('');

        return alertaAviso('A data de encaminhamento ao Legislativo não pode ultrapassar o período de vigência do PPA.', 'form', 'erro', '&iURLRandomica=20090219162437.022', '../')
    }

    if (dtDevolucao && (arDtEncaminhamento[2] + arDtEncaminhamento[1] + arDtEncaminhamento[0] > arDtDevolucao[2] + arDtDevolucao[1] + arDtDevolucao[0])) {
        jq('#dtDataLegislativo').val('');

        return alertaAviso('A data de encaminhamento ao Legislativo não pode ser maior que a data de devolução ao Executivo.', 'form', 'erro', '&iURLRandomica=20090219162437.022', '../')
    }
}

function validaDataDevolucao()
{
    dtEncaminhamento = jq('#dtDataLegislativo').val();
    arDtEncaminhamento = dtEncaminhamento.split('/');

    dtDevolucao = jq('#dtDataDevolucaoExecutivo').val();
    arDtDevolucao = dtDevolucao.split('/');

    stAnoInicio = jq('#stAnoInicio').val();
    stAnoFinal = jq('#stAnoFinal').val();

    if (arDtDevolucao[2] > stAnoFinal) {
        jq('#dtDataDevolucaoExecutivo').val('');

        return alertaAviso('A data de devolução ao Executivo não pode ultrapassar o período de vigência do PPA.', 'form', 'erro', '&iURLRandomica=20090219162437.022', '../')
    }

    if (dtEncaminhamento && (arDtEncaminhamento[2] + arDtEncaminhamento[1] + arDtEncaminhamento[0] > arDtDevolucao[2] + arDtDevolucao[1] + arDtDevolucao[0])) {
        jq('#dtDataDevolucaoExecutivo').val('');

        return alertaAviso('A data de devolução ao Executivo não pode ser menor que a data de encaminhamento ao Legislativo.', 'form', 'erro', '&iURLRandomica=20090219162437.022', '../')
    }
}

</script>
