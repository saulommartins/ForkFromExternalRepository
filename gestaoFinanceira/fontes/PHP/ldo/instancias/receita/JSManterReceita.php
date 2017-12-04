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

function cancelarReceita()
{
    <?php $stLink = $pgList.'?'.Sessao::getId(); ?>

    mudaTelaPrincipal('<?= $stLink ?>');
}

function excluirRecursos(objeto)
{
    var stSpan = objeto.parentNode.parentNode.parentNode.parentNode.parentNode.id;
    var tabela = objeto.parentNode.parentNode.parentNode;
    var linha  = objeto.parentNode.parentNode;
    var valorLinha = linha.cells[3].childNodes[1].innerHTML;
    var cont = document.frm.inSizeRecursos.value;

    valorLinha = parseInt(valorLinha);
    atualizaTotalRecurso(valorLinha);
    tabela.deleteRow(linha.rowIndex);
    cont--;
    document.frm.inSizeRecursos.value = cont;

    if (tabela.rows.length == 2) {
        var spnListaRecurso = $(stSpan);
        var inCodReceitaLista = document.frm.inCodReceitaLista;

        if (spnListaRecurso) {
            spnListaRecurso.innerHTML = '&nbsp;';

            inCodReceitaLista.value = '';
        }
    }
}

function validarNorma()
{
    if (document.frm.inCodNorma.value == '') {
        alertaAviso('Campo Norma obrigatório!', 'form', 'erro', '<?= Sessao::getId() ?>');

        return false;
    }

    return true;
}

function inserirRecurso()
{
    var stAcao =  "<?php echo $_REQUEST['stAcao'];?>";
    var inNumReceita = document.frm.inNumReceita;

    if (stAcao == 'incluir') {
        if ($('inCodReceitaLista').value != inNumReceita.value && $('inCodReceitaLista').value != '') {
                alertaAviso('Receita não pode ser alterada. Já Existe(m) Recurso(s) vinculado(s) para receita ('+$('inCodReceitaLista').value+')', 'form', 'erro', '<?= Sessao::getId() ?>');

                return;
        }
    }

    if (document.frm.inNumRecurso=='') {
        alertaAviso('Campo Recurso deve ser informado!()', 'form', 'erro', '<?= Sessao::getId() ?>');

        return;
    }

    if (document.frm.flValorRecurso.value == '' || document.frm.flValorRecurso.value == '0,00') {
        alertaAviso('Campo Valor Recurso obrigatório!()', 'form', 'erro', '<?= Sessao::getId() ?>');

        return;
    }

    if (validaValorRecursoReceita()) {
        alertaAviso('Valor do Recurso maior que o valor da Receita! (informe um valor menor ou igual)', 'form', 'erro', '<?= Sessao::getId() ?>');

        return;
    }

    if (recursoExistente(document.frm.inNumRecurso.value)) {
        montaParametrosGET('inserirRecurso', null, false);
        atualizaTotalRecurso();
    } else {
        $('inNumRecurso').value = '';
        $('flValorRecurso').value = '';
        $('stNomRecurso').innerHTML = '&nbsp;';
    }
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

function validaValorRecursoReceita()
{
    var flTotalRecurso = toFloat($('lbTotalRecurso').innerHTML) + toFloat(document.frm.flValorRecurso.value);
    var flTotalReceita = toFloat($('lbTotalReceita').innerHTML);

    if (flTotalRecurso > flTotalReceita) {
        return true;
    }

    return false;
}

function recursoExistente(codigoRecurso)
{
    var obSpan = $('spnListaRecurso');
    var obElementos = obSpan.childNodes;

    if ((!document.frm.inSizeRecursos) || (document.frm.inSizeRecursos.value == 0) ) {
        //PARA 1ª INCLUSÃO
        return true;
    }

    for (x = 0; x < obElementos.length; x++) {
        var elementoAtual = obElementos[x];

        if (elementoAtual.nodeName == "TABLE") {
            var tabela = obElementos[x];

            for (k=0; k < tabela.rows.length; k++) {
                var linhaAtual = tabela.rows[k];

                if (k <= 1) {
                    continue;
                }

                for (j = 0; j < linhaAtual.cells.length; j++) {
                    var celulaAtual = linhaAtual.cells[j];

                    if (j == 0 || j == 4)
                        continue;

                    if (j == 1) {
                        var codigoRecursoTabela = celulaAtual.childNodes[1].innerHTML;

                        codigoRecursoTabela = parseInt(codigoRecursoTabela);

                        if (codigoRecursoTabela ==  codigoRecurso) {
                            alertaAviso('Recurso já vinculado!('+codigoRecurso+')', 'form', 'erro', '<?= Sessao::getId() ?>');

                            return false;
                        } else {
                            return true;
                        }
                    }
                }
            }
        }
    }
}

function exibirRecurso()
{
    var inNumReceita = document.frm.inNumReceita;
    var inCodReceitaLista = document.frm.inCodReceitaLista;

    if (inCodReceitaLista.value != inNumReceita.value && inCodReceitaLista.value != '') {
        alertaAviso('Para alterar esta receita a lista de recursos deve estar vazia', 'form', 'erro', '<?= Sessao::getId() ?>');
        $('inNumReceita').value = $('inCodReceitaLista').value;

        return;
    }

    montaParametrosGET('exibirRecurso');
}

function limparRecurso()
{
    if (document.frm.inNumRecurso) {
        $('inNumRecurso').value = '';
        $('stNomRecurso').innerHTML = '&nbsp;';
    }

    $('flValorRecurso').value = '';
}
function limparReceita()
{
    inCodReceitaLista.value = '';
    limpaFormulario();
}
function atualizaTotalRecurso(flSubtrai)
{
    var flTotalRecurso = toFloat($('lbTotalRecurso').innerHTML);
    var flValorRecurso = toFloat(document.frm.flValorRecurso.value);
    var flNovoTotal = 0.0;

    if (flValorRecurso.value !== '') {
        if (flSubtrai) {
            flNovoTotal = flTotalRecurso - flSubtrai;
        } else {
            flNovoTotal = flValorRecurso + flTotalRecurso;
        }
    }

    $('lbTotalRecurso').innerHTML = retornaFormatoMonetario(flNovoTotal);
}

</script>
