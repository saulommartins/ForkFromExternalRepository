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
<script>
/**
 * Javascript do 02.10.03 - Manter Ação
 * Data de Criação: 16/02/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Fellipe Esteves dos Santos <fellipe.santos>
 * @package GF
 * @subpackage LDO
 * @uc 02.10.03 - Manter Ação
 */

function limparAcao()
{
    limparDadosCadastro();
    limparClassificacaoInstitucional();
    limparClassificacaoEconomica();
}

function limparDadosCadastro()
{
    $('inNumAcao').value = '';
    $('stNomAcao').innerHTML = '&nbsp;';
    $('spnPrograma').innerHTML = '&nbsp;';
}

function limparClassificacaoInstitucional()
{
    $('inCodEntidade').value = '';
    $('stNomEntidade').value = '';

    document.frm.inMontaCodOrgaoM.selectedIndex = 0;
    document.frm.inMontaCodUnidadeM.selectedIndex = 0;
}

function limparClassificacaoEconomica()
{
    $('stCodReceita').value = '';
    $('inCodRecurso').value = '';
    $('flValorRecurso').value = '';
    $('stDescricaoReceita').innerHTML = '&nbsp;';
    $('stDescricaoRecurso').innerHTML = '&nbsp;';

    $('spnListaRecurso').innerHTML = '&nbsp;';

    $('flTotalDisponivel').value = $('flTotalDisponivel').value + $('flTotalAcao').value;
    $('flTotalAcoes').value = $('flTotalAcoes').value - $('flTotalAcao').value;

    atualizarValores(true);
}

function cancelarAcao()
{
    <?php $stLink = $pgList.'?'.Sessao::getId(); ?>

    mudaTelaPrincipal('<?= $stLink ?>');
}

function inserirRecurso()
{
    if (validarRecurso() && validarValores()) {
        montaParametrosGET('inserirRecurso');
        inserirValor();
    }
}

function inserirValor()
{
    var flValorRecurso = document.getElementById('flValorRecurso').value;
    var flValorTotal   = document.getElementById('flTotalAcoes').value;
    var flValorAtual   = document.getElementById('flTotalAcao').value;

    flTotalAcao  = toFloat(flValorAtual) + toFloat(flValorRecurso);
    flTotalAcoes = toFloat(flValorTotal) + toFloat(flValorRecurso);

    mostrarTotalAcao(flTotalAcao);
    mostrarTotalAcoes(flTotalAcoes);

    atualizarValores(false);
}

function atualizarValores(refresh)
{
    var flTotalAcao       = calcularTotalAcao();
    var flTotalDisponivel = calcularTotalDisponivel();

    if (refresh) {
        mostrarTotalAcao(flTotalAcao);
        mostrarTotalDisponivel(flTotalDisponivel);
    }

    var flTotalDiferenca  = calcularTotalDiferenca();

    mostrarTotalDiferenca(flTotalDiferenca);
}

function calcularTotalAcao()
{
    if (!document.frm.inSizeRecurso || document.frm.inSizeRecurso == '0') {
        return toFloat(document.getElementById('flValorRecurso').value);
    }

    var inSize = document.frm.inSizeRecurso.value;
    var flTotalAcao = 0;

    for (var i = 0; i < inSize; ++i) {
        var obElement = document.getElementById('arValorRecurso_' + i);

        if (obElement) {
            flTotalAcao += toFloat(obElement.innerHTML);
        }
    }

    return flTotalAcao;
}

function calcularTotalDisponivel()
{
    var flTotalReceita = document.getElementById('flTotalReceita').value;
    var flTotalAcoes   = document.getElementById('flTotalAcoes').value;
    flTotalDisponivel = flTotalReceita - flTotalAcoes;

    return flTotalDisponivel;
}

function calcularTotalDiferenca()
{
    var flTotalReceita = document.getElementById('flTotalReceita').value;
    var flTotalAcoes   = document.getElementById('flTotalAcoes').value;

    flTotalDiferenca   = flTotalReceita - flTotalAcoes;

    return flTotalDiferenca;
}

function mostrarTotalAcao(flTotalAcao)
{
    if (!flTotalAcao) {
        $('lbTotalAcao').innerHTML = '0,0';
        document.frm.flTotalAcao.value = '0.0';
    } else {
        $('lbTotalAcao').innerHTML = retornaFormatoMonetario(flTotalAcao);
        document.frm.flTotalAcao.value = flTotalAcao;
    }
}

function mostrarTotalAcoes(flTotalAcoes)
{
    document.frm.flTotalAcoes.value = flTotalAcoes;
}

function mostrarTotalDisponivel(flTotalDisponivel)
{
    if (!flTotalDisponivel) {
        $('lbTotalDisponivel').innerHTML = '0,0';
        document.frm.flTotalDisponivel.value = '0.0';
    } else {
        $('lbTotalDisponivel').innerHTML = retornaFormatoMonetario(flTotalDisponivel);
        document.frm.flTotalDisponivel.value = flTotalDisponivel;
    }
}

function mostrarTotalDiferenca(flTotalDiferenca)
{
    if (!flTotalDiferenca) {
        $('lbTotalDiferenca').innerHTML = '0,0';
        document.frm.flTotalDiferenca.value = '0.0';
    } else {
        $('lbTotalDiferenca').innerHTML = retornaFormatoMonetario(flTotalDiferenca);
        document.frm.flTotalDiferenca.value = flTotalDiferenca;
    }
}

function validarRecurso()
{
    if (validarCampoInvalido(document.getElementById('stCodReceita').value, 'Rubrica'))
        return false;

    if (validarCampoInvalido(document.getElementById('inCodRecurso').value, 'Recurso'))
        return false;

    if (validarCampoInvalido(document.frm.flValorRecurso.value, 'Valor'))
        return false;

    if (validarDuplicidadeCodigo(document.frm.inCodRecurso.value))
        return false;

    return true;
}

function validarNorma()
{
    if (validarCampoInvalido(document.getElementById('inCodNorma').value, 'Norma'))
        return false;

    return true;
}

function validarValores()
{
    if (toFloat(document.getElementById('flTotalDiferenca').value) <= 0) {
        alertaAviso('Não há receitas cadastradas para este LDO(<?= $arParametros["stAno"] ?>)!', 'form', 'erro', '<?= Sessao::getId() ?>');

        return false;
    }

    var valor1 = new Array('Total da Ação', document.getElementById('flValorRecurso').value);
    var valor2 = new Array('Total de Receita Disponível', document.getElementById('flTotalDiferenca').value);

    if (validarCampoFuturo(valor1, valor2)) {
        return false;
    }

    return true;
}

function excluirRecurso(objeto)
{
    var span   = objeto.parentNode.parentNode.parentNode.parentNode.parentNode.id;
    var tabela = objeto.parentNode.parentNode.parentNode;
    var linha  = objeto.parentNode.parentNode;
    var valor  = linha.childNodes[3].childNodes[1].innerHTML;

    tabela.deleteRow(linha.rowIndex);

    if (tabela.rows.length == 2) {
        var spnListaRecurso = document.getElementById(span);

        if (spnListaRecurso) {
            spnListaRecurso.innerHTML = '&nbsp;';
        }
    }

    excluirValor(valor);
}

function excluirValor(flValor)
{
    var flValorAtual = document.getElementById('flTotalAcao').value;
    var flValorTotal = document.getElementById('flTotalAcoes').value;

    flTotalAcao  = toFloat(flValorAtual) - toFloat(flValor);
    flTotalAcoes = toFloat(flValorTotal) - toFloat(flValor);

    mostrarTotalAcao(flTotalAcao);
    mostrarTotalAcoes(flTotalAcoes);

    atualizarValores(false);
}

function limparRecurso()
{
    document.frm.inCodRecurso.value           = '';
    document.frm.flValorRecurso.value         = '';
    $('stDescricaoRecurso').innerHTML         = '&nbsp;';
}

function validarCampoInvalido(campo, label)
{
    if (campo == '' || campo == '0,00') {
        alertaAviso('Campo ' + label + ' obrigatório!', 'form', 'erro', '<?= Sessao::getId() ?>');

        return true;
    }

    return false;
}

function validarCampoFuturo(array1, array2)
{
    if (toFloat(array1[1]) > toFloat(array2[1])) {
        alertaAviso('Valor ' + array1[0] + ' maior que o ' + array2[0] + ' (Valor disponível: ' + retornaFormatoMonetario(array2[1]) + ')!', 'form', 'erro', '<?= Sessao::getId() ?>');

        return true;
    }

    return false;
}

function validarDuplicidadeCodigo(codigo)
{
    var span = document.getElementById('spnListaRecurso');
    var elementosSpan = span.childNodes;

    if ((!document.frm.inSizeRecurso) || (document.frm.inSizeRecurso.value == 0)) {
        return false;
    }

    for (x=0 ;x < elementosSpan.length ; x++) {

        var elementoAtual = elementosSpan[x];

        if (elementoAtual.nodeName == "TABLE") {
            var tabela = elementosSpan[x];
            for (k=0;k< tabela.rows.length;k++) {
                if (k <= 1)
                    continue;
                var linhaAtual = tabela.rows[k];
                for (j=0;j< linhaAtual.cells.length;j++) {
                    if (j == 0 || j==4 )
                        continue;

                    var celulaAtual = linhaAtual.cells[j];
                    if (j==1) {
                        var codigoTabela = parseInt(celulaAtual.childNodes[1].innerHTML);
                        if (codigoTabela ==  codigo) {
                            alertaAviso('Código já informado!()', 'form', 'erro', '<?= Sessao::getId() ?>');

                            return true;
                        } else {
                            return false;
                        }
                    }
                }
            }
        }
    }
}
</script>
