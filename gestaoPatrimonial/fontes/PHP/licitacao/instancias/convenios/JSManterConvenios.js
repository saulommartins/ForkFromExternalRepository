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
    * Formulario de Convenio 
    * Data de Criação   : 03/10/2006
    

    * @author Analista: 
    * @author Desenvolvedor:  Lucas Teixeira Stephanou
    * @ignore

    $Id: JSManterConvenios.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    *Casos de uso: uc-03.05.14
*/

?>
<script type="text/javascript">

function buscaValor(tipoBusca){
    document.frm.stCtrl.value = tipoBusca;
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.action = stAction;
    document.frm.target = stTraget;

}
function alterarConvenio(inNumConvenio,inExercicio){
    var url =  '<?=$pgForm;?>?<?=Sessao::getId();?>&stAcao=alterar&inNumConvenio='+inNumConvenio+'&inExercicio='+inExercicio;
    document.location = url;
}
function consultarConvenio(inNumConvenio){
    var url =  '<?=$pgFormConsulta;?>?<?=Sessao::getId();?>&stAcao=alterar&inNumConvenio='+inNumConvenio;
    document.location = url;
}
function anularConvenio(inNumConvenio){
    var url =  '<?=$pgFormAnular;?>?<?=Sessao::getId();?>&stAcao=anular&inNumConvenio='+inNumConvenio;
    document.location = url;
}
function consultaVoltar(){
    var url =  '<?=$pgList;?>?<?=Sessao::getId();?>&stAcao=consultar';
    document.location = url;
}
function Cancelar(){
<?php
    $stLink = "&pg=".Sessao::read('pg')."&pos=".Sessao::read('pos');
?>
    document.frm.target = "";
    document.frm.action = "<?=$pgList.'?'.Sessao::getId().$stLink;?>";
    document.frm.submit();
}
function Limpar(){
   limpaFormulario();
   buscaValor('LimparSessao');
   document.frm.reset();
}

function validaDatasAssinatura( foco ) {
    // pega valores
    var ass = document.getElementById( 'dtAssinatura' ).value;
    var vig = document.getElementById( 'dtFinalVigencia' ).value;
    var ini = document.getElementById( 'dtInicioExecucao' ).value;
    // passa para formato americano
    ass = new String( ass );
    ass = ass.split("/");
    ass = ass[2]+ass[1]+ass[0];

    vig = new String( vig );
    vig = vig.split("/");
    vig = vig[2]+vig[1]+vig[0];
    
    ini = new String( ini );
    ini = ini.split("/");
    ini = ini[2]+ini[1]+ini[0];
    
    var ass = parseInt( ass ) ;
    var vig = parseInt( vig ) ;
    var ini = parseInt( ini ) ;
    
    //compara
    if ( ass > ini ) {
        var mensagem = 'Assinatura não pode ser maior que o início de execução';
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
        document.getElementById(foco.id).value='';        
        setTimeout('document.getElementById(\''+foco.id+'\').focus();', 500);        
        return false;
    } else if ( ini > vig ) {
        var mensagem = 'Início de Execução não pode ser maior que a vigência';
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
        document.getElementById(foco.id).value='';        
        setTimeout('document.getElementById(\''+foco.id+'\').focus();', 500);        
        return false;
    } 
}

function validaDataRescisao( foco ) {
    // pega valores
    var res = document.getElementById( 'dtRescisao' ).value;
    var adit = document.getElementById( 'dtMaiorAditivo' ).value;
    var pubRes = document.getElementById( 'dtPublicacaoRescisao' ).value;
    
    // passa para formato americano
    dtRescisao = new String( res );
    dtRescisao = dtRescisao.split("/");
    dtRescisao = dtRescisao[2]+dtRescisao[1]+dtRescisao[0];

    dtMaiorAditivo = new String( adit );
    dtMaiorAditivo = dtMaiorAditivo.split("/");
    dtMaiorAditivo = dtMaiorAditivo[2]+dtMaiorAditivo[1]+dtMaiorAditivo[0];
    
    dtPublicacaoRescisao = new String( pubRes );
    dtPublicacaoRescisao = dtPublicacaoRescisao.split("/");
    dtPublicacaoRescisao = dtPublicacaoRescisao[2]+dtPublicacaoRescisao[1]+dtPublicacaoRescisao[0];

    var dtRescisao = parseInt( dtRescisao ) ;
    var dtMaiorAditivo = parseInt( dtMaiorAditivo ) ;
    var dtPublicacaoRescisao = parseInt( dtPublicacaoRescisao );
        
    //compara
    if ( dtMaiorAditivo > dtRescisao ) {
        var mensagem = 'A data de rescisão não pode ser anterior que o último aditivo cadastrado('+adit+') para esse convênio.';
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
        document.getElementById(foco.id).value='';        
        setTimeout('document.getElementById(\''+foco.id+'\').focus();', 500);        
        return false;
    } else if ( dtRescisao > dtPublicacaoRescisao ) {
        var mensagem = 'A data de publicação da não pode ser anterior que a data de rescisão do convênio.';
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
        document.getElementById(foco.id).value='';        
        setTimeout('document.getElementById(\''+foco.id+'\').focus();', 500);        
        return false;
    }
}

function excluirVeiculo ( inCgmVeiculoPublicidade ){
    ajaxJavaScript('<?=$pgOcul."?".Sessao::getId()."&inCgmVeiculoPublicidade="?>'+inCgmVeiculoPublicidade, 'excluirVeiculo');
}
function excluirParticipante ( inCgmParticipante ){
    ajaxJavaScript('<?=$pgOcul."?".Sessao::getId()."&inCgmParticipante="?>'+inCgmParticipante, 'excluirParticipante');
}
function alterarParticipante ( inCgmParticipante ){
    ajaxJavaScript('<?=$pgOcul."?".Sessao::getId()."&inCgmParticipante="?>'+inCgmParticipante, 'alterarParticipante');
}

</script>
