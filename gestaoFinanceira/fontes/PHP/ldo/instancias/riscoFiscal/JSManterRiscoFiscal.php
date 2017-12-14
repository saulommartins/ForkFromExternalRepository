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

function excluirProvidencia(objeto)
{
    var span   = objeto.parentNode.parentNode.parentNode.parentNode.parentNode.id;
    var tabela = objeto.parentNode.parentNode.parentNode;
    var linha  = objeto.parentNode.parentNode;
    var valor  = linha.childNodes[2].childNodes[1].innerHTML;

    tabela.deleteRow(linha.rowIndex);

    if (tabela.rows.length == 2) {
        var span = document.getElementById(span);

        if (span) {
            span.innerHTML = '&nbsp;';
            $('lbTotalProvidencia').innerHTML = '&nbsp;';
        }
    } else {
        var flValor = toFloat(valor);
        atualizaTotalProvidencia(flValor, 'subtrair');
    }
}

function inserirProvidencia()
{

    if ($('stDescProvidencia').value == '') {
        alertaAviso('Campo Providência obrigatório!()', 'form', 'erro', '<?= Sessao::getId() ?>');

        return;
    }
    if ($('flValorProvidencia').value == '' || $('flValorProvidencia').value == '0,00') {
        alertaAviso('Campo Valor obrigatório!()', 'form', 'erro', '<?= Sessao::getId() ?>');

        return;
    }

    if (verificarDuplicidade($('stDescProvidencia').value)) {
        montaParametrosGET('inserirProvidencia');
        var flValorProvidencia = toFloat($('flValorProvidencia').value);
        atualizaTotalProvidencia(flValorProvidencia, 'somar');
    } else {
        $('stDescProvidencia').value = '';
        $('flValorProvidencia').value = '';
    }

}

function verificarDuplicidade(novoRegistro)
{
    var span = document.getElementById('spnListaProvidencia');
    var elementosSpan = span.childNodes;

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
                        var registroTabela = celulaAtual.childNodes[1].innerHTML;
                        novoRegistro   = trim(novoRegistro);
                        novoRegistro   = novoRegistro.toLowerCase();
                        registroTabela = trim(registroTabela);
                        registroTabela = registroTabela.toLowerCase();
                        if (registroTabela == novoRegistro) {
                            alertaAviso('Registro já informado!()', 'form', 'erro', '<?= Sessao::getId() ?>');

                            return false;
                        }
                    }
                }
            }
        }
    }

    return true;
}

function limparProvidencia()
{
    $('stDescProvidencia').value = '';
    $('flValorProvidencia').value = '';
}

function atualizaTotalProvidencia(flValorProvidencia, operacao)
{
    var totalProvidencia = $('lbTotalProvidencia').innerHTML;
    var flTotalProvidencia = toFloat(totalProvidencia);

    if (operacao == 'somar') {
        flTotalProvidencia += flValorProvidencia;
    } else if (operacao == 'subtrair') {
        flTotalProvidencia -= flValorProvidencia;
    }

    $('lbTotalProvidencia').innerHTML = retornaFormatoMonetario(flTotalProvidencia);
}

function recuperarProvidenciaFiscal()
{
    montaParametrosGET('recuperarProvidenciaFiscal');
}

/**
 * Limpa espaçoes em branco a esquerda e a
 * direita da string informada.
 * Equivalente a função trim() do PHP
 *
 * @param string str [, string charlist]
 */
function trim(str, charlist)
{
    var whitespace, l = 0, i = 0;
    str += '';

    if (!charlist) {
        // default list
        whitespace = " \n\r\t\f\x0b\xa0\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u200b\u2028\u2029\u3000";
    } else {
        // preg_quote custom list
        charlist += '';
        whitespace = charlist.replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, '\$1');
    }

    l = str.length;
    for (i = 0; i < l; i++) {
        if (whitespace.indexOf(str.charAt(i)) === -1) {
            str = str.substring(i);
            break;
        }
    }

    l = str.length;
    for (i = l - 1; i >= 0; i--) {
        if (whitespace.indexOf(str.charAt(i)) === -1) {
            str = str.substring(0, i + 1);
            break;
        }
    }

    return whitespace.indexOf(str.charAt(0)) === -1 ? str : '';
}

</script>
