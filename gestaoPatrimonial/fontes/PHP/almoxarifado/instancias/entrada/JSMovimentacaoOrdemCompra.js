<script type="text/javascript">
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
</script>
<?php
/**
* Arquivo Javascript da Entrada por Ordem de Compra
* Data de Criação: 12/07/2007

* @author Analista: Gelson W. Gonçalves
* @author Desenvolvedor: Henrique Girardi dos Santos

   $Id: JSMovimentacaoOrdemCompra.js 65631 2016-06-03 21:06:49Z michel $

*/

?>
<script type="text/javascript">

/* atualizar o item selecionado  */
function selecionaItem( Objeto )
{
    var f = document.frm;
    if ( document.getElementById('spnItensPereciveis') )
        if ( trim(document.getElementById('spnItensPereciveis').innerHTML) == '' ){
            alertaAviso('Inclua o ítem detalhado na lista.','form','erro','<?=$sessao->id;?>');
        }
        for( i=0 ; i<f.elements.length ; i++) {
            if( typeof(f.elements[i]) == 'object' ){
                var idE = new String(f.elements[i].id);
                if( f.elements[i].id != Objeto.id && idE.substring(0,5) == 'item_'){
                    f.elements[i].checked = false;
                }
            }
        }
        // atualiza na sessão item foi selecionado
        //parametro = '&item='+Objeto.id;
        //executaFuncaoAjax( 'montaDetalheItem', parametro, true );
}

// altera os dados da listagem de perecível
function alterarPerecivel( inNumLotePerecivel, dtFabricacaoPerecivel, dtValidadePerecivel, inQtdePerecivel, inNumLinhaListaPerecivel)
{
    $('inNumLotePerecivel').value = inNumLotePerecivel;
    $('dtFabricacaoPerecivel').value = dtFabricacaoPerecivel;
    $('dtValidadePerecivel').value = dtValidadePerecivel;
    $('inQtdePerecivel').value = inQtdePerecivel;
    $('inNumLinhaListaPerecivel').value = inNumLinhaListaPerecivel;
    $('incluirEntrada').value = 'Alterar';
    jQuery('#inQtdeUltimoPerecivel').val(inQtdePerecivel);
    jQuery('#Incluir').val('Alterar Lotes');
    jQuery('#acaoPerecivel').val('alterar');
}

// exclui a linha da listagem de perecível
function excluirPerecivel( inNumLinhaListaPerecivel, inCodItem )
{
    ajaxJavaScript('<?=$pgOcul."?".$sessao->id?>&inNumLinhaListaPerecivel='+inNumLinhaListaPerecivel+'&inCodItem='+inCodItem+'&inQtdeEntrada=' + $("inQtdeEntrada").value, 'excluirPerecivel');
}

// altera os dados da listagem de entrada
function alterarEntrada( inCheckBoxId )
{
    $('item_'+inCheckBoxId).click();
}

// exclui os dados da listagem de entrada
function excluirEntrada( inCodItem )
{
    ajaxJavaScript('<?=$pgOcul."?".$sessao->id?>&inCodItem='+inCodItem, 'excluirEntrada');
}

function setarCheckBox()
{
    checkBox = document.getElementById('inProxItem');

    if(checkBox.checked == true) {
        checkBox.value = 1;
    } else {
        checkBox.value = '';
    }
}

</script>
