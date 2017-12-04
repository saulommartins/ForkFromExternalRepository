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
<script type="text/javascript">

function buscaDado( BuscaDado ){
    var stTarget = document.frm.target;
    var stAction = document.frm.action; 
    document.frm.target = 'oculto';
    document.frm.stCtrl.value = BuscaDado;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTarget;
}

function buscaUnidade(objeto){
    if(objeto.value != ''){
        buscaDado ('buscaUnidade');
    }
}

function buscaDividas(objeto){
    if(objeto.value != ''){
        buscaDado ('buscaDividas');
    }
}

function limparDivida(){
    document.getElementById( 'inOrgao' ).value           = '';
    document.getElementById( 'inUnidade' ).value         = '';
    document.getElementById( 'inTipoLancamento' ).value  = '';
    document.getElementById( 'stLeiAutorizacao' ).value  = '';
    document.getElementById( 'dtLeiAutorizacao' ).value  = '';
    document.getElementById( 'inCGM' ).value             = '';
    document.getElementById( 'stCGM' ).innerHTML         = '';
    document.getElementById( 'vlSaldoAnterior' ).value   = '';
    document.getElementById( 'vlContratacao' ).value     = '';
    document.getElementById( 'vlAmortizacao' ).value     = '';
    document.getElementById( 'vlCancelamento' ).value    = '';
    document.getElementById( 'vlEncampacao' ).value      = '';
    document.getElementById( 'vlAtualizacao' ).value     = '';
    document.getElementById( 'vlSaldoAtual' ).value      = '';
}

function excluirListaItens( id ){
    ajaxJavaScript('<?=$pgOcul."?".Sessao::getId()?>&id='+id,'excluirListaItens');
}

function montaAlteracaoLista( id ){
    ajaxJavaScript('<?=$pgOcul."?".Sessao::getId()?>&id='+id,'montaAlteracaoLista');
}

</script>