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
    * Página de JavaScript de Inclusao/Alteracao programa

    * Data de Criação   : 19/09/2008

    * @author Analista      : Bruno Ferreira
    * @author Desenvolvedor : Jânio Eduardo
    * @ignore

    * $Id:

    *Casos de uso: uc-02.09.02
*/
?>

<script type="text/javascript">

function SalvarPrograma()
{
    if (document.frm.stAcao.value != 'alterar') {
        if (validaPPA()) {
            BloqueiaFrames(true,false);
            document.frm.submit();
        }
    } else {
        document.frm.submit();
    }
}

function CancelarCL()
{
<?php
    $link = Sessao::read( "link" );
    $stLink = "&pg=".$link["pg"]."&pos=".$link["pos"]."&stAcao=".$_REQUEST["stAcao"];
?>
    mudaTelaPrincipal("<?=$pgList.'?'.Sessao::getId().$stLink;?>");
}

// Verifica se data inicial é valida
function verificaPeriodoInicial()
{
    var dtInicio;
    var dtFinal;
    var arDtInicio = Array();
    var arDtFinal = Array();

    if (document.frm.stDataInicial.value == '') {
        alertaAviso('Campo Data inicial vazia','form','erro','<?=Sessao::getId()?>');
        setTimeout('document.frm.stDataInicial.focus();', 1);
    } else {
        dtInicio = document.frm.stDataInicial.value;
        arDtInicio = dtInicio.split("/");

        if (arDtInicio[2] < document.frm.inAnoInicioPPA.value) {
            alertaAviso('Campo período Inválido (Data inicial menor que ano inicial do PPA)','form','erro','<?=Sessao::getId()?>');
            document.frm.stDataInicial.value = '';
            setTimeout('document.frm.stDataInicial.focus();', 1);

            return;
        }

        if (arDtInicio[2] > document.frm.inAnoFinalPPA.value) {
            alertaAviso('Campo período Inválido (Data inicial maior que ano final do PPA)','form','erro','<?=Sessao::getId()?>');
            document.frm.stDataInicial.value = '';
            setTimeout('document.frm.stDataInicial.focus();', 1);

            return;
        }

        dtFinal = document.frm.stDataFinal.value;
        arDtFinal = dtFinal.split("/");

        if (dtFinal && (arDtInicio[2] + arDtInicio[1] + arDtInicio[0] > arDtFinal[2] + arDtFinal[1] + arDtFinal[0])) {
            alertaAviso('Campo período Inválido (Data Final menor que data Inicial)','form','erro','<?=Sessao::getId()?>');
            document.frm.stDataFinal.value = '';
            setTimeout('document.frm.stDataFinal.focus();', 1);
        }

        dtInicioAcao = document.frm.hdnDataMinimaAcao.value;
        arDtInicioAcao = dtInicioAcao.split("/");

        if (dtInicioAcao && (arDtInicio[2] + arDtInicio[1] + arDtInicio[0] > arDtInicioAcao[2] + arDtInicioAcao[1] + arDtInicioAcao[0])) {
            alertaAviso('Campo período Inválido (Data Inicial não pode ser maior que a data inicial da ação ('+dtInicioAcao+') )','form','erro','<?=Sessao::getId()?>');
            document.frm.stDataInicial.value = document.frm.hdnDataInicialTemporario.value;
            setTimeout('document.frm.stDataInicial.focus();', 1);
        }
    }
}

// Verifica se data final e período são validos
function verificaPeriodoFinal()
{
    var dtInicio;
    var dtFinal;
    var arDtInicio = Array();
    var arDtFinal = Array();

    if (document.frm.stDataFinal.value == '') {
        alertaAviso('Campo Data final vazia','form','erro','<?=Sessao::getId()?>');
        setTimeout('document.frm.stDataFinal.focus();', 1);
    } else {
        dtFinal = document.frm.stDataFinal.value;
        arDtFinal = dtFinal.split("/");

        if (arDtFinal[2] > document.frm.inAnoFinalPPA.value) {
            alertaAviso('Campo período Inválido (Data final maior que ano final do PPA)','form','erro','<?=Sessao::getId()?>');
            document.frm.stDataFinal.value = '';
            setTimeout('document.frm.stDataFinal.focus();', 1);

            return;
        }

        dtInicio = document.frm.stDataInicial.value;
        arDtInicio = dtInicio.split("/");

        if (dtInicio && (arDtInicio[2] + arDtInicio[1] + arDtInicio[0] > arDtFinal[2] + arDtFinal[1] + arDtFinal[0])) {
            alertaAviso('Campo período Inválido (Data Final menor que data Inicial)','form','erro','<?=Sessao::getId()?>');
            document.frm.stDataFinal.value = '';
            setTimeout('document.frm.stDataFinal.focus();', 1);
        }

        dtFinalAcao = document.frm.hdnDataMaximaAcao.value;
        arDtFinalAcao = dtFinalAcao.split("/");

        if (dtFinalAcao && (arDtFinal[2] + arDtFinal[1] + arDtFinal[0] < arDtFinalAcao[2] + arDtFinalAcao[1] + arDtFinalAcao[0])) {
            alertaAviso('Campo período Inválido (Data Final não pode ser menor que a data final da ação ('+dtFinalAcao+') )','form','erro','<?=Sessao::getId()?>');
            document.frm.stDataFinal.value = document.frm.hdnDataFinalTemporario.value;
            setTimeout('document.frm.stDataFinal.focus();', 1);
        }
    }
}

function buscaValor(metodo)
{
    document.frm.stCtrl.value = metodo;
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.action = stAction;
    document.frm.target = stTraget;
}

function excluirIndicadores(objeto)
{
    var tabela = objeto.parentNode.parentNode.parentNode;
    var linha = objeto.parentNode.parentNode;
    tabela.deleteRow(linha.rowIndex);

    if (tabela.rows.length == 2) {
        tabela.parentNode.removeChild(tabela);
    }
}

function incluirIndiceLista()
{
    if (jq('#stDescIndicador').val() == '') {
        alertaAviso('Campo Descrição de Indicador é obrigatório para inclusão do Indicador!', 'form', 'erro', '<?=Sessao::getId()?>');

        return;
    }

    if (jq('#stUnidadeMedida').val() == '') {
        alertaAviso('Campo Unidade de Medida é obrigatório para inclusão do Indicador!', 'form', 'erro', '<?=Sessao::getId()?>');

        return;
    }

    if (jq('#stFonteIndice').val() == '') {
        alertaAviso('Campo Fonte do Índice é obrigatório para inclusão do Indicador!', 'form', 'erro', '<?=Sessao::getId()?>');

        return;
    }

    if (jq('#stPeriodicidade').val() == '') {
        alertaAviso('Campo Periodicidade é obrigatório para inclusão do Indicador!', 'form', 'erro', '<?=Sessao::getId()?>');

        return;
    }

    if (jq('#stBaseGeografica').val() == '') {
        alertaAviso('Campo Base Geográfica é obrigatório para inclusão do Indicador!', 'form', 'erro', '<?=Sessao::getId()?>');

        return;
    }

    if (jq('#stFormaCalculo').val() == '') {
        alertaAviso('Campo Forma de Cálculo é obrigatório para inclusão do Indicador!', 'form', 'erro', '<?=Sessao::getId()?>');

        return;
    }

    if (jq('#dtIndiceRecente').val() == '') {
        alertaAviso('Campo Data do Índice Recente é obrigatório para inclusão do Indicador!', 'form', 'erro', '<?=Sessao::getId()?>');

        return;
    }

    if (jq('#flIndiceRecente').val() == '') {
        jq('#flIndiceRecente').val('0,00');
    }

    if (jq('#flIndiceDesejado').val() == '') {
        jq('#flIndiceDesejado').val('0,00');
    }

    montaParametrosGET('buscaListaIndicador', '', true);
}

function atualizaPPA()
{
    document.getElementsByName('stCtrl')[0].value = 'recuperaPPA';
    montaParametrosGET('recuperaPPA', null, true);

}

function buscaMacroObjetivos()
{
    document.getElementsByName('stCtrl')[0].value = 'buscaMacroObjetivos';
    montaParametrosGET('buscaMacroObjetivos', null, true);

    atualizaPPA();
}

function buscaProgramasSetoriais()
{
    document.getElementsByName('stCtrl')[0].value = 'buscaProgramasSetoriais';
    montaParametrosGET('buscaProgramasSetoriais', null, true);

}

function sugereCodPrograma()
{
    if (document.getElementById('hdnProgramaOrcamento').value == 'false') {
        if (validaPPA()) {
            document.getElementsByName('stCtrl')[0].value = 'sugereCodPrograma';
            montaParametrosGET('sugereCodPrograma', null, true);
        } else {
            document.getElementById('inCodPrograma').value = '';
        }
    }
}

function atualizaPrograma()
{
    if (jq('#inCodPrograma').val() != '') {
        if (validaPPA()) {
            document.getElementsByName('stCtrl')[0].value = 'validaCodPrograma';
            montaParametrosGET('validaCodPrograma', null, true);
        } else {
            document.getElementById('inCodPrograma').value = '';
        }
    }
}

function validaPPA()
{
    if (document.getElementById('inCodPPA').value) {
        if (document.getElementById('inCodMacroObjetivo').value) {
            if (document.getElementById('inCodProgramaSetorial').value) {
                return true;
            } else {
                alertaAviso('Selecione o Programa Setorial!', 'form', 'erro', '<?=Sessao::getId()?>');

                return false;
            }
        } else {
            alertaAviso('Selecione o Macro Objetivo!', 'form', 'erro', '<?=Sessao::getId()?>');

            return false;
        }
    } else {
        alertaAviso('Selecione o PPA!', 'form', 'erro', '<?=Sessao::getId()?>');

        return false;
    }
}

function limparIndice()
{
    document.frm.stDescIndicador.value = '';
    document.frm.stUnidadeMedida.value = '';
    document.frm.flIndiceRecente.value = '';
    document.frm.flIndiceDesejado.value = '';
    document.frm.stFonteIndice.value = '';
    document.frm.stPeriodicidade.value = '';
    document.frm.stBaseGeografica.value = '';
    document.frm.stFormaCalculo.value = '';
}

function testarIndiceRecente(textbox)
{
    if (parseToFloat(textbox.value) == 0) {
        alertaAviso('Campo Índice Recente não pode ser nulo!', 'form', 'erro', '<?=Sessao::getId()?>');
        setTimeout("textbox = document.getElementById('" + textbox.id + "'); textbox.value = ''; textbox.focus();", 1);
    }
}

function testarIndiceDesejado(textbox)
{
    if (parseToFloat(textbox.value) == 0) {
        alertaAviso('Campo Índice Desejado não pode ser nulo!', 'form', 'erro', '<?=Sessao::getId()?>');
        setTimeout("textbox = document.getElementById('" + textbox.id + "'); textbox.value = ''; textbox.focus();", 1);
    }
}

function limparPrograma()
{
    montaParametrosGET('limpaListaIndice');
}

</script>
