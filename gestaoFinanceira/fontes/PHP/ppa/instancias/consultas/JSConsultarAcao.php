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
 * Javascript que valida inclusão e alteração de Ação
 * Data de Criação: 04/08/2007

 * @author Analista      : Heleno Menezes da Silva
 * @author Desenvolvedor : Pedro Vaz de Mello de Medeiros
 * @ignore

 $Id: JSManterAcao.php 35778 2008-11-19 18:26:52Z pedro.medeiros $

 * Casos de uso: uc-02.09.04
 */
?>

<script type="text/javascript">

function cancelarAcao()
{
<?php
    $link = Sessao::read('link');
    $stLink = '&stAcao=' . $stAcao . '&pg=' . $link['pg'] . '&pos=' . $link['pos'];
?>
    mudaTelaPrincipal('<?= $pgList . '?' . Sessao::getId() . $stLink; ?>');
}

function buscaValor(stTipoBusca)
{
    var stTarget = document.frm.target;
    var stAction = document.frm.action;

    document.frm.stCtrl.value = stTipoBusca;
    document.frm.target = 'oculto';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTarget;
}

function limparRecurso()
{
    var obLblDescricao;

    document.frm.inCodRecurso.value = '';
    document.frm.flValorAno1.value = '';
    document.frm.flValorAno2.value = '';
    document.frm.flValorAno3.value = '';
    document.frm.flValorAno4.value = '';

    obLblDescricao = document.getElementById('stDescricaoRecurso');

    if (obLblDescricao) {
        obLblDescricao.innerHTML = '&nbsp;';
    }
}

function incluirRecurso()
{
    var flValorAno1;

    if (document.frm.inCodRecurso.value == '') {
        alertaAviso('Campo Recurso obrigatório!()', 'form', 'erro', '<?= Sessao::getId() ?>');

        return;
    }

    flValorAno1 = toFloat(document.frm.flValorAno1.value);

    if (!flValorAno1) {
        alertaAviso('Campo Valor Ano 1 obrigatório!()', 'form', 'erro', '<?= Sessao::getId() ?>');

        return;
    }

    document.frm.stDescricaoRecurso.value = $('stDescricaoRecurso').innerHTML;

    montaParametrosGET('incluirRecurso', null, true);
    atualizaDados();
    limparRecurso();
}

function excluirRecurso(objeto)
{
    var tabela = objeto.parentNode.parentNode.parentNode;
    var linha = objeto.parentNode.parentNode;

    tabela.deleteRow(linha.rowIndex);

    montaParametrosGET('atualizarRecurso', null, true);

    atualizaTotalAcao();

    if (tabela.rows.length == 2) {
        var spnListaRecurso = document.getElementById('spnListaRecurso');

        if (spnListaRecurso) {
            spnListaRecurso.innerHTML = '&nbsp;';
        }
    }
}

function somaTotalAcao()
{
    if (!document.frm.inSizeRecurso) {
        return 0;
    }

    var inSize = document.frm.inSizeRecurso.value;
    var flTotal = 0;

    for (var i = 0; i < inSize; ++i) {
        for (var j = 1; j < 5; ++j) {
            var obElement = document.getElementById('arValorAno' + j + '_' + i);

            if (obElement) {
                flTotal += toFloat(obElement.value);
            }
        }
    }

    return flTotal;
}

function setTotalAcao(flTotal)
{
    $('stTotalAcao').innerHTML = retornaFormatoMonetario(flTotal);
    document.frm.flTotalAcao.value = flTotal;
}

function setTotalPrograma()
{
    var flValor = parseFloat($('flSubtotalProg').value) + parseFloat(document.frm.flTotalAcao.value);
    $('stTotalPrograma').innerHTML = retornaFormatoMonetario(flValor);
}

function setTotalAcumulado()
{
    var flValor = parseFloat($('flSubtotalReceita').value) - parseFloat(document.frm.flTotalAcao.value);
    $('stTotalAcumulado').innerHTML = retornaFormatoMonetario(flValor);
}

function setTotalPPA()
{
    var flValor = parseFloat($('flSubtotalPPA').value) + parseFloat(document.frm.flTotalAcao.value);
    $('stTotalPPA').innerHTML = retornaFormatoMonetario(flValor);
}

function atualizaTotalAcao()
{
    var flTotal = somaTotalAcao();

    setTotalAcao(flTotal);
    /* setTotalPPA(); */
    /* setTotalPrograma(); */
    setTotalAcumulado();
}

function somaQuantidadeTotal()
{
    var flTotal = 0.0;
    var obLblQuantidade;

    obLblQuantidade = document.getElementById('flQuantidadeAno1_0');

    if (obLblQuantidade) {
        flTotal  = toFloat(document.getElementById('flQuantidadeAno1_0').value);
        flTotal += toFloat(document.getElementById('flQuantidadeAno2_0').value);
        flTotal += toFloat(document.getElementById('flQuantidadeAno3_0').value);
        flTotal += toFloat(document.getElementById('flQuantidadeAno4_0').value);
    } else {
        flTotal  = toFloat(document.frm.flQuantidadeAno1.value);
        flTotal += toFloat(document.frm.flQuantidadeAno2.value);
        flTotal += toFloat(document.frm.flQuantidadeAno3.value);
        flTotal += toFloat(document.frm.flQuantidadeAno4.value);
    }

    return flTotal;
}

function setQuantidadeTotal(flTotal)
{
    var obLblQuantidadeTotal;

    obLblQuantidadeTotal = document.getElementById('stQuantidadeTotal');

    if (obLblQuantidadeTotal) {
        obLblQuantidadeTotal.innerHTML = retornaFormatoMonetario(flTotal);
    }
}

function atualizaQuantidade()
{
    var flTotal;

    flTotal = somaQuantidadeTotal();
    setQuantidadeTotal(flTotal);
}

function atualizaDados()
{
    atualizaTotalAcao();
    atualizaQuantidade();
}

function processa(stAcao)
{
    var stTarget = document.frm.target;
    var stAction = document.frm.action;

    document.frm.stCtrl.value = stAcao;
    document.frm.target = 'oculto';
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTarget;
}

function incluirAcao()
{
    var boRegiao;
    var boTipo;

    with (document.frm) {
        boTipo = inCodTipo[0].checked + inCodTipo[1].checked + inCodTipo[2].checked;
    }

    if (!boTipo) {
        return alertaAviso('Campo Tipo inválido!()', 'form', 'erro', '<?= Sessao::getID(); ?>');
    }

    if ((!document.frm.inSizeRecurso) || (document.frm.inSizeRecurso.value == 0)) {
        return alertaAviso('Necessário especificar pelo menos um recurso!()', 'form', 'erro', '<?= Sessao::getID(); ?>');
    }

    for (var i = 0; i < document.frm.inSizeRecurso.value; ++i) {
        var arValorAno1 = document.getElementById('arValorAno1_' + i);

        if (arValorAno1 && arValorAno1.value == '0,00') {
            return alertaAviso('Necessário especificar Valor Ano 1 para Recurso!()', 'form', 'erro', '<?= Sessao::getID(); ?>');
        }
    }

    if (Valida()) {
        processa('incluir');
    }

    //return Salvar();
}

function atualizaDadosPrograma()
{
    montaParametrosGET('atualizarDadosPrograma', null, true);
}

function atualizaComponenteRecurso()
{
    montaParametrosGET('montaRecurso', null, true);
}

function atualizaContrato()
{
    $('inContrato').value = document.frm.inCodContrato.value;
    $('inContrato').blur();
}

function atualiza()
{
    atualizaDados();
    atualizaDadosPrograma();
    atualizaContrato();
    atualizaComponenteRecurso();
}

function confirmaAtualizacao()
{
    document.frm.inCodPPAFinal = document.frm.inCodPPA;
    montaParametrosGET('verificarNorma', null, true);
    atualizaDadosPrograma();
    atualizaComponenteRecurso();
}

function atualizaPrograma()
{
    if (document.frm.inSizeRecurso && document.frm.inSizeRecurso.value > 0) {
        confirmPopUp('Alterando o PPA atual', 'Alterar o PPA agora pode mudar o tipo de vinculação de recurso. Confirma apagar a lista de recursos?', 'confirmaAtualizacao();');
    } else {
        confirmaAtualizacao();
    }
}

function retornarConsulta()
{
<?php
    $link = Sessao::read( "link" );
    $stLink = 'stAcao=consultar&pg=' . $link['pg'] . '&pos=' . $link['pos'];
?>
    mudaTelaPrincipal('<?= 'LSManterAcao.php?' . Sessao::getId() . '&' . $stLink; ?>');
}

</script>
