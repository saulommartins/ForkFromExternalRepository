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
    * Página de funções javascript para o cadastro de logradouro
    * Data de Criação   : 21/09/2004


    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues
                             Gustavo Passos Tourinho
                             Cassiano de Vasconcelos Ferreira

    * @ignore
    
    * $Id: JSProcurarLogradouro.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    * Casos de uso: uc-05.01.04
*/

?>
<script type="text/javascript">

function IniciaSessions(  ){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'IniciaSessions';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTraget;
    document.frm.action = stAction;
}

function buscaValor(tipoBusca){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = tipoBusca;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTraget;
    document.frm.action = stAction;
}

function preencheMunicipio( stLimpar ){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'preencheMunicipio';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&stLimpar=' + stLimpar;
    document.frm.submit();
    document.frm.target = stTraget;
    document.frm.action = stAction;
}

function preencheInner(  ){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'preencheInner';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTraget;
    document.frm.action = stAction;
}

function preencheBairro(){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'preencheBairro';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTraget;
    document.frm.action = stAction;
}

function filtrar(){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;   
    document.frm.action = '<?=$pgFilt;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTraget;
    document.frm.action = stAction;
}

function fechar(){
    window.close ();
}

function incluir(){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.action = '<?=$pgForm;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTraget;
    document.frm.action = stAction;
}

function habilitaSpanBairro() {
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'habilitaSpanBairro';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTraget;
    document.frm.action = stAction;
}

function habilitaSpanCEP() {
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'habilitaSpanCEP';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTraget;
    document.frm.action = stAction;
}

function limparBairro() {
    document.frm.inCodBairro.value = "";
    document.frm.inCodigoBairro.value = "";
}

function limparCEP() {
    document.frm.inCEP.value = "";
    document.frm.inInicial.value = "";
    document.frm.inFinal.value = "";
    document.frm.boNumeracao[0].checked = true;
}

function limparListas(){

    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.reset();
    document.frm.stCtrl.value = 'limparListas';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTraget;
    document.frm.action = stAction;
}


function CancelarFormFL () {

<?php
    $stLink = Sessao::read('stLink');   
?>
    limparListas();
    document.frm.target = "";
    document.frm.action = "<?=$pgFilt.'?'.Sessao::getId().$stLink;?>";
    document.frm.submit();
}

function CancelarForm () {

<?php
    $stLink = Sessao::read('stLink');
?>
    document.frm.target = "";
    document.frm.action = "<?=$pgList.'?'.Sessao::getId().$stLink;?>";
    document.frm.submit();
}

function Limpar(){
    document.frm.reset();
    preencheMunicipio( 'limpar' );
}

function incluirBairro() {
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    var erro = false;
    var mensagem = "";
    stCampo = document.frm.inCodBairro;
    if ( stCampo.value == "" ) {
        erro = true;
        mensagem += "@Campo Bairro inválido!("+stCampo.value+")";
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
    } else {
        document.frm.stCtrl.value = 'incluirBairro';
        document.frm.target = "oculto";
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
        document.frm.submit();
    }
    document.frm.inCodBairro.value = "";
    document.frm.inCodigoBairro.value = "";
    document.frm.target = stTraget;
    document.frm.action = stAction;
}

function incluirNovoBairro() {
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'incluirNovoBairro';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTraget;
    document.frm.action = stAction;
}


                        

function excluirBairro( inIndice ){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'excluirBairro';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inIndice='+inIndice;
    document.frm.submit();
    document.frm.target = stTraget;
    document.frm.action = stAction;
}

function incluirCEP() {
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    var erro = false;
    var mensagem = "";
    stCampo = document.frm.inCEP;
    if ( stCampo.value == "" ) {
        erro = true;
        mensagem += "@Campo CEP inválido!("+stCampo.value+")";
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
    } else {
        document.frm.stCtrl.value = 'incluirCEP';
        document.frm.target = "oculto";
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
        document.frm.submit();
    }
    document.frm.target = stTraget;
    document.frm.action = stAction;
}

function excluirCEP( inIndice ){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'excluirCEP';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inIndice='+inIndice;
    document.frm.submit();
    document.frm.target = stTraget;
    document.frm.action = stAction;
}

<?php
if($_REQUEST["campoNum"]){
?>
function preencheCampos( inCodLogradouro, stNomeLogradouro, inProximaSequencia ){
    if( window.opener.parent.frames['telaPrincipal'] ){
        windowopener = window.opener.parent.frames['telaPrincipal'];
    }else{
        windowopener = window.opener.parent;
    }
    windowopener.document.getElementById('<?=$_REQUEST["campoNom"];?>').innerHTML = stNomeLogradouro;
    windowopener.document.frm.<?=$_REQUEST["campoNum"];?>.value = inCodLogradouro;
    windowopener.document.frm.<?=$_REQUEST["campoNum"];?>.focus();

    var stTraget = window.opener.parent.frames['telaPrincipal'].document.frm.target;
    
    window.opener.parent.frames['telaPrincipal'].document.frm.target = "oculto";
    window.opener.parent.frames['telaPrincipal'].document.frm.controle = 668;
    //window.opener.parent.frames['telaPrincipal'].document.frm.submit();
    window.opener.parent.frames['telaPrincipal'].document.frm.target = stTraget;
   
    window.close();
}
<?php
}
?>



function preencheCamposImovel( inCodLogradouro, stNomeLogradouro, inCodUF, inCodMunicipio ){
    window.opener.parent.frames['telaPrincipal'].document.frm.inNumLogradouro.focus();
    window.opener.parent.frames['telaPrincipal'].document.frm.inNumLogradouro.value = inCodLogradouro;
    window.opener.parent.frames['telaPrincipal'].document.getElementById("campoInnerLogr").innerHTML = stNomeLogradouro;
    window.opener.parent.frames['telaPrincipal'].document.frm.stNomeLogradouro.value = stNomeLogradouro;
    window.opener.parent.frames['telaPrincipal'].document.frm.inCodUF.value = inCodUF;
    window.opener.parent.frames['telaPrincipal'].document.frm.inCodMunicipio.value = inCodMunicipio;
    window.close();    
}

function preencheCamposCgm( inCodLogradouro, stNomeLogradouro, stNomMunicipio, stNomUf, inCodUF, inCodMunicipio, inCodBairro, stCEP ){
    window.opener.parent.frames['telaPrincipal'].document.frm.inNumLogradouro.focus();
    window.opener.parent.frames['telaPrincipal'].document.getElementById("stMunicipio").innerHTML = stNomMunicipio;
    window.opener.parent.frames['telaPrincipal'].document.getElementById("stEstado").innerHTML = stNomUf;
    window.opener.parent.frames['telaPrincipal'].document.frm.inNumLogradouro.value = inCodLogradouro;
    window.opener.parent.frames['telaPrincipal'].document.getElementById("campoInnerLogr").innerHTML = stNomeLogradouro;
    window.opener.parent.frames['telaPrincipal'].document.frm.stNomeLogradouro.value = stNomeLogradouro;
    window.opener.parent.frames['telaPrincipal'].document.frm.inCodUF.value = inCodUF;
    window.opener.parent.frames['telaPrincipal'].document.frm.inCodMunicipio.value = inCodMunicipio;
    window.opener.parent.frames['telaPrincipal'].document.frm.inCodigoBairro.value = inCodBairro;
    window.opener.parent.frames['telaPrincipal'].document.frm.hdnCEP.value = stCEP;
    
    var stTraget = window.opener.parent.frames['telaPrincipal'].document.frm.target;

    window.opener.parent.frames['telaPrincipal'].document.frm.target = "oculto";
    window.opener.parent.frames['telaPrincipal'].document.frm.controle.value = 668;
    window.opener.parent.frames['telaPrincipal'].document.frm.submit();
    window.opener.parent.frames['telaPrincipal'].document.frm.target = stTraget;

    window.close();
}

function preencheCamposCgmCorresp( inCodLogradouro, stNomeLogradouro, stNomeMunicipio, stNomUf, inCodUF, inCodMunicipio, inCodBairro, stCEP ){
    window.opener.parent.frames['telaPrincipal'].document.frm.inNumLogradouroCorresp.focus();
    window.opener.parent.frames['telaPrincipal'].document.getElementById("stMunicipioCorresp").innerHTML = stNomeMunicipio;
    window.opener.parent.frames['telaPrincipal'].document.getElementById("stEstadoCorresp").innerHTML = stNomUf;
    window.opener.parent.frames['telaPrincipal'].document.frm.inNumLogradouroCorresp.value = inCodLogradouro;
    window.opener.parent.frames['telaPrincipal'].document.getElementById("campoInnerLogrCorresp").innerHTML = stNomeLogradouro;
    window.opener.parent.frames['telaPrincipal'].document.frm.stNomeLogradouroCorresp.value = stNomeLogradouro;
    window.opener.parent.frames['telaPrincipal'].document.frm.inCodUFCorresp.value = inCodUF;
    window.opener.parent.frames['telaPrincipal'].document.frm.inCodMunicipioCorresp.value = inCodMunicipio;
    window.opener.parent.frames['telaPrincipal'].document.frm.inCodigoBairroCorresp.value = inCodBairro;
    window.opener.parent.frames['telaPrincipal'].document.frm.hdnCEPCorresp.value = stCEP;

    var stTraget = window.opener.parent.frames['telaPrincipal'].document.frm.target;

    window.opener.parent.frames['telaPrincipal'].document.frm.target = "oculto";
    window.opener.parent.frames['telaPrincipal'].document.frm.controle.value = 669;
    window.opener.parent.frames['telaPrincipal'].document.frm.submit();
    window.opener.parent.frames['telaPrincipal'].document.frm.target = stTraget;

    window.close();    
}

function preencheCamposTrecho( inCodLogradouro, stNomeLogradouro, inProximaSequencia ){
    window.opener.parent.frames['telaPrincipal'].document.frm.inNumLogradouro.focus();
    window.opener.parent.frames['telaPrincipal'].document.frm.inNumLogradouro.value = inCodLogradouro;
    window.opener.parent.frames['telaPrincipal'].document.getElementById("campoInner").innerHTML = stNomeLogradouro;
    window.opener.parent.frames['telaPrincipal'].document.frm.stNomeLogradouro.value = stNomeLogradouro;
    window.opener.parent.frames['telaPrincipal'].document.frm.inCodSequencia.value = inProximaSequencia;
    window.opener.parent.frames['telaPrincipal'].document.frm.inCodSequencia.focus();
    window.close();
}

function desabilitaCampos(){
    if( jQuery('#stCEP').val() != '' ){
        jQuery('#inCodigoUF').attr('disabled','disabled');
        jQuery('#inCodUF').attr('disabled','disabled');
        jQuery('#inCodigoMunicipio').attr('disabled','disabled');
        jQuery('#inCodMunicipio').attr('disabled','disabled');
        jQuery('#inCodigoBairro').attr('disabled','disabled');
        jQuery('#inCodBairro').attr('disabled','disabled');
    }else{
        jQuery('#inCodigoUF').removeAttr('disabled');
        jQuery('#inCodUF').removeAttr('disabled');
        jQuery('#inCodigoMunicipio').removeAttr('disabled');
        jQuery('#inCodMunicipio').removeAttr('disabled');
        jQuery('#inCodigoBairro').removeAttr('disabled');
        jQuery('#inCodBairro').removeAttr('disabled');
    }

}

function verificaCodigoLogradouro() {
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'verificaCodigoLogradouro';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTraget;
    document.frm.action = stAction;
}

</script>
