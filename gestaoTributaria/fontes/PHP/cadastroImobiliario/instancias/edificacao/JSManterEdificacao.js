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
    * Página de funções javascript para o cadastro de edificação
    * Data de Criação   : 24/11/2004


    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues

    * @ignore

    * $Id: JSManterEdificacao.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    * Casos de uso: uc-05.01.11
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
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&cod_tipo=<?=$request->get("inCodigoTipo");?>&cod_processo='+processo+'&timestamp='+timestamp+'&cod_construcao='+cod_construcao+'&ano_exercicio='+ano_exercicio;
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}


function mudaMenu(func){
    sPag = "<?=CAM_FW_INSTANCIAS;?>index/menu.php?<?=Sessao::getId();?>&nivel=3&cod_modulo_pass=12&cod_gestao_pass=5&stNomeGestao=Tributária&modulos=Cadastro Imobiliário&cod_func_pass="+func;
    parent.parent.frames["telaMenu"].location.replace(sPag);
}

function buscaValor(tipoBusca){
    document.frm.stCtrl.value = tipoBusca;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function buscaDado(tipoBusca){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = tipoBusca;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTraget;
    document.frm.action = stAction;
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
    BloqueiaFrames(true,false);
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

function habilitaVinculo( boHabilita ){
    if( boHabilita == 'false' ){
        document.frm.boAdicionarEdificacao[0].disabled = true;
        document.frm.boAdicionarEdificacao[1].disabled = true;
    } else {
        document.frm.boAdicionarEdificacao[0].disabled = false;
        document.frm.boAdicionarEdificacao[1].disabled = false;
    }
}

function verificaAction(){
    if( document.frm.boAdicionarEdificacao[0].checked ) {
        document.frm.action = '<?=$pgFormVinculo;?>?<?=Sessao::getId();?>';
    }else{
        document.frm.action = '<?=$pgFiltVinculo;?>?<?=Sessao::getId();?>';
    }
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
}

function LimparFL(){
    document.frm.boAdicionarEdificacao[0].checked = true;
    document.frm.boAdicionarEdificacao[0].disabled = false;
    document.frm.boAdicionarEdificacao[1].disabled = false;
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


function habilitaSpnTotalEdificacao(){
    document.frm.stCtrl.value = 'habilitaSpnTotalEdificacao';
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
    if( document.frm.getElementById('stTipoUnidade').innerHTML == 'Autônoma'){
        var erro = false;
        var mensagem = "";
        var stAreaUnid = eval(document.frm.flAreaUnidade.value);
        var stAreaConst = eval(document.frm.flAreaTotalEdificada.value);
        if ( stAreaUnid > stAreaConst ) {
            document.frm.flAreaUnidade.value = document.frm.flAreaTotalEdificada.value;
            erro = true;
            mensagem += "A área da unidade deve ser menor ou igual à área total edificada!";
            alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
        }
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

function LimparInsc(){
    document.getElementById('stTipoUnidade').innerHTML = '&nbsp;';
    document.getElementById('lsAtributosEdificacao').innerHTML = '';
    document.frm.reset();
}

function LimparCond(){
    document.getElementById('lsAtributosEdificacao').innerHTML = '';
    document.getElementById('campoInnerCond').innerHTML = '&nbsp;';
    document.frm.reset();
}

function limparFiltro(){
    document.frm.reset();
    preencheCombos();
    habilitaSpnImovelCond();
}

function submeteFiltro(){
    stLocalizacao = document.frm.stChaveLocalizacao.value;
    stRadio = document.frm.boVinculoEdificacao.value;
    if ( stRadio == 'Condomínio' ){
       stCondominio = document.frm.inCodigoCondominio.value;
       if ( stCondominio == "" && stLocalizacao == "" ){
           mensagem = "Campos Condomínio ou Localização não foram preenchidos!";
           alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
       } else {
           Salvar();
       }
    } else {
       stInscricao = document.frm.inInscricaoMunicipal.value;

       if ( stInscricao == "" && stLocalizacao == "" ){
           mensagem = "Campos Inscrição Imobiliária ou Localização não foram preenchidos!";
           alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
       } else {
           Salvar();
       }
    }
}

function Cancelar () {
<?php
    $stLink = Sessao::read('stLink');
?>
    document.frm.target = "";
    document.frm.action = "<?=$pgList.'?'.Sessao::getId().$stLink;?>";
    document.frm.submit();
}

function validaDataConstrucao() {
    dtDataConstrucao = document.frm.stDtConstrucao.value;
    DiaData  = dtDataConstrucao.substring(0,2);
    MesData  = dtDataConstrucao.substring(3,5);
    AnoData  = dtDataConstrucao.substr(6);

    var dataValidar = 15000422;
    var dataConstrInvert = AnoData+MesData+DiaData;

    if( dataConstrInvert < dataValidar ){
        document.frm.stDtConstrucao.value = "";
        erro = true;
        mensagem = "@Campo Data de Construção deve ser posterior a 21/04/1500!";
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
    }
}

function atualizaComponente(){
    HabilitaLayer("");
}
</script>
