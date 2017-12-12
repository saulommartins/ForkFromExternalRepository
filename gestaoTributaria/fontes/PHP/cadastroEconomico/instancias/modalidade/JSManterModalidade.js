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
<?
/**

    * @author Analista: Ricardo Lopes
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues

    * @ignore

	* $Id: JSManterModalidade.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    *Casos de uso: uc-05.02.13
**/

/*
$Log$
Revision 1.6  2006/11/08 10:34:57  fabio
alteração do uc_05.02.13

Revision 1.5  2006/09/15 14:33:18  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
?>
<script type="text/javascript">
function buscaDado( BuscaDado ){
    document.frm.stCtrl.value = BuscaDado;
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
}

function atualizaFormularioFiltro(){
    document.frm.stCtrl.value = 'atualizaFormularioFiltro';
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
}

function filtrar(){
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.action = '<?=$pgFilt;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
}

//function carregarAtividade( valorComposto , inCodigoAtividade , stNomeAtividade , inCodigoModalidade ){
function carregarAtividade( BuscaDado, inCodigoAtividade, inCodigoModalidade ){
    //document.frm.stValorComposto.value         = valorComposto;
    document.frm.inCodigoAtividade.value       = inCodigoAtividade;
    //document.frm.stNomeAtividade.value         = stNomeAtividade;
    //if ( inCodigoModalidade || "" ) {
    //    document.frm.inCodigoModalidade.value  = inCodigoModalidade;
        //document.frm.cmbCodigoModalidade.value = inCodigoModalidade;
    //}
    document.frm.stCtrl.value = BuscaDado;
    var stTarget = document.frm.target;
    document.frm.target = 'oculto';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.target = stTarget;
}

function definirModalidade() {
    var stTarget   = document.frm.target;
    var stAction   = document.frm.action;
    var erro       = false;
    var mensagem   = "";
    inCodAtiv = document.frm.inCodigoAtividade;
    //stValComp = document.frm.stValorComposto;
    //stNomAtiv = document.frm.stNomeAtividade;
    if ( inCodAtiv == "" ) { //|| stValComp.value == "" || stNomAtiv == "" ) {
        erro = true;
        mensagem += "@Campo Atividade inválido!";
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
    } else {
        document.frm.stCtrl.value = 'definirModalidade';
        document.frm.target = "oculto";
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
        document.frm.submit();
    }
    document.frm.target = stTarget;
    document.frm.action = stAction;
}

function montaMoedaIndicador( tipo ){
    document.frm.stCtrl.value = tipo;
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTarget;
}

function limparDef(){
    document.frm.inCodigoAtividade.value  = '';
    //document.frm.stValorComposto.value    = '';
    //document.frm.stNomeAtividade.value    = '';
    document.frm.inCodigoModalidade.value = '';
    document.frm.cmbCodigoModalidade.options[0].selected = true;
}

function Limpar( boVinculoModalidade ){
    <?php Sessao::write( 'atividades', array() ); ?>
    if ( boVinculoModalidade == "inscricao" ){
        //document.getElementById("spnAtividadeInscricao").innerHTML = " ";
        document.getElementById("spnVisualizarAtividade").innerHTML = "";
        document.getElementById("spnAtividadeInscricao" ).innerHTML = "";
    }
    document.frm.reset();
}

function Cancelar () {
    <?php
     $link = Sessao::read( "link" );
     $stLink = "&pg=".$link["pg"]."&pos=".$link["pos"];
    ?>
    document.frm.target = "";
    document.frm.action = "<?=$pgList.'?'.Sessao::getId().$stLink;?>";
    document.frm.submit();
}

function focusEspecial(){
    recuperaAtividades();
    document.frm.dtDataInicio.focus();
}

function preencheProxCombo( inPosicao  ){
    var stTarget   = document.frm.target;
    var stAction   = document.frm.action;
    document.frm.stCtrl.value = 'preencheProxCombo';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inPosicao='+inPosicao;
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;    
}

function preencheCombosAtividade(){
    var stTarget   = document.frm.target;
    var stAction   = document.frm.action;
    document.frm.stCtrl.value = 'preencheCombosAtividade';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;   
}

</script>
