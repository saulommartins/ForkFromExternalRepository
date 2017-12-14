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
    * Arquivo JavaScript
    * Data de Criação   : 08/11/2006


    * @author Analista: Gelson
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @ignore


    * Casos de uso: uc-03.05.26

*/

/*

*/
?>
<script type="text/javascript">


function alterar( ordem, codItem ) {
    abrePopUp ( 'FMManterJulgamentoParticipante.php', 'frm', '', '', '', '<?=Sessao::getId()?>&inOrdem=' + ordem + '&inCodItem='+codItem , '800', '500' );
}

function fecha( ordem, cod_item ,  inStatus, obs  ) {

    if ( (obs == '') && ( inStatus == 1) )   {
       alertaAviso( 'É necessário digitar uma justificativa para a desclassificação!' ,'form','erro','<?=Sessao::getId();?>');
    }else{
        var acao = window.opener.parent.frames['telaPrincipal'].document.frm.action;
        var target =  window.opener.parent.frames['telaPrincipal'].document.frm.target;
        window.opener.parent.frames['telaPrincipal'].document.frm.stCtrl.value =  'alterar_participante' ;
        window.opener.parent.frames['telaPrincipal'].document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&ordem=' + ordem + '&inStatus='+ inStatus +'&stOBS='+obs+'&inCodItem='+cod_item;
        window.opener.parent.frames['telaPrincipal'].document.frm.target = 'oculto';
        window.opener.parent.frames['telaPrincipal'].document.frm.submit();
        window.opener.parent.frames['telaPrincipal'].document.frm.target = target;
        window.opener.parent.frames['telaPrincipal'].document.frm.action = acao;
        window.close();
    }
}

// atualiza particpante
// na proposta
function selecionaItem_Lote( Objeto , stTipo   ){
    var f = document.frm;
    for( i=0 ; i<f.elements.length ; i++) {
        if( typeof(f.elements[i]) == 'object' ){
            var idE = new String(f.elements[i].id);
            if( f.elements[i].id != Objeto.id && idE.substring(0,13) == 'chkseleciona_'){
                f.elements[i].checked = false;
            }
        }
    }
    // atualiza na sessão participante selecionado
    parametro = '&codigo='+Objeto.value+'&tipoBusca='+stTipo+'&stAcao=<?=$_REQUEST['stAcao'];?>';
    executaFuncaoAjax( 'montaSpanFornecedores', parametro, true );


}

function selecionaItemLoteFornecedor ( objeto ){


    parametro = '&loteFornecedor='+objeto.value;
    executaFuncaoAjax( 'montaSpanLoteFornecedor', parametro, true );



}




</script>
