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
    * Pagina de processamento para Grupos de Credito
    * Data de Criação   : 25/05/2005
    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Texeira Stephanou

    * $Id: JSManterTipoSuspensao.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

* Casos de uso: uc-05.03.07
*/

/*
$Log$
Revision 1.3  2006/09/15 11:23:59  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

?>
<script type="text/javascript">

function focusIncluir(){
    document.frm.inProcesso.focus();
}


function visualizarProcesso(processo, timestamp, cod_construcao, ano_exercicio){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'visualizarProcesso';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&cod_tipo=<?=$_REQUEST["inCodigoTipo"]?>&cod_processo='+processo+'&timestamp='+timestamp+'&cod_construcao='+cod_construcao+'&ano_exercicio='+ano_exercicio;
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}


function mudaMenu(func){
    sPag = "../../../menu.php?<?=Sessao::getId();?>&nivel=2&cod_func_pass="+func;
    parent.parent.frames["telaMenu"].location.replace(sPag);
}

function buscaValor(tipoBusca){
    document.frm.stCtrl.value = tipoBusca;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function preencheProxCombo( inPosicao  ){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'preencheProxCombo';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inPosicao='+inPosicao;
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTraget;
}

function preencheCombos(){
    document.frm.stCtrl.value = 'preencheCombos';
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTraget;
}

function verificaUnidadeAutonoma(){
    document.frm.stCtrl.value = 'verificaUnidadeAutonoma';
    var stTarget = document.frm.target;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.target = stTarget;
}

function verificaAction(){
    if( document.frm.boAdicionarEdificacao[0].checked ) {
        document.frm.action = '<?=$pgFormVinculo;?>?<?=Sessao::getId();?>';
    }else{    
        document.frm.action = '<?=$pgList;?>?<?=Sessao::getId();?>';
    }
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;   
}

function verificaUnidadeAutonomaOnBlur(){
    if( document.frm.stImovelCond.value != "" ){
        verificaUnidadeAutonoma();
    }
}

function habilitaSpnImovelCond(){
    document.frm.stCtrl.value = 'habilitaSpnImovelCond';
    var stTarget = document.frm.target;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgList;?>?<?=Sessao::getId();?>';
    document.frm.target = stTarget;
}

function habilitaSpnNumComp(){
    document.frm.stCtrl.value = 'habilitaSpnNumComp';
    var stTarget = document.frm.target;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgList;?>?<?=Sessao::getId();?>';
    document.frm.target = stTarget;
}

function habilitaSpnUnidadesDependentes(){
    document.frm.stCtrl.value = 'MontaListaUnidadesDependentes';
    var stTarget = document.frm.target;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.target = stTarget;
}

function habilitaSpnEdificacao(){
    document.frm.stCtrl.value = 'MontaListaSelecionarEdificacao';
    var stTarget = document.frm.target;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.target = stTarget;
}

function montaAtributosEdificacao(){
    document.frm.stCtrl.value = 'montaAtributosEdificacao';
    var stTraget = document.frm.target;
    document.frm.target = "oculto";
    var stAction = document.frm.action;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTraget;
}

function validaAreaUnidade(){
    var erro = false;
    var mensagem = "";
    var stAreaUnid = eval(document.frm.flAreaUnidade.value);
    var stAreaConst = eval(document.frm.flAreaConstruida.value);
    if ( stAreaUnid > stAreaConst ) {
        document.frm.flAreaUnidade.value = document.frm.flAreaConstruida.value;
        erro = true;
        mensagem += "A área da unidade deve ser menor ou igual à área da edificação!";
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
    }
}

function verificaAreaUnidade(){
    stCampo = document.frm.flAreaUnidade;
    if ( stCampo.value == "" ) {
        document.frm.flAreaUnidade.value = document.frm.flAreaConstruida.value;
    }
}

function filtrar(){
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.action = '<?=$pgFilt;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
}

function Limpar(){
    document.getElementById('stTipoUnidade').innerHTML = '&nbsp;';
    document.getElementById('lsAtributosEdificacao').innerHTML = '';
    document.frm.reset();
    document.frm.stChaveLocalizacao.value = "";
    preencheCombos();
}

function Cancelar () {
<?php
    $link = Sessao::read( "link" );
    $stLink = "&pg=".$link["pg"]."&pos=".$link["pos"]."&boVinculoEdificacao=".$_REQUEST['boVinculoEdificacao']."";
?>
    document.frm.target = "";
    document.frm.action = "<?=$pgList.'?'.Sessao::getId().$stLink;?>";
    document.frm.submit();
}

function focusFiltro(){
    habilitaSpnImovelCond();
    document.frm.inCodigoConstrucao.focus();
}

function focusAlterarSpnNumComp(){
    habilitaSpnNumComp();
    document.frm.flAreaConstruida.focus();
}

function focusAlterar(){
    document.frm.flAreaConstruida.focus();
}

function focusBaixar(){
    document.frm.inProcesso.focus();
}

function focusIncluir(){
    document.frm.stImovelCond.focus();
}
</script>
