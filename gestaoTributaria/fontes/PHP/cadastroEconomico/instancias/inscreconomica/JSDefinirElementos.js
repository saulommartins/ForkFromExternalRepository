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
    * Página de JavaScript para Defininir Elementos Inscricao
    * Data de Criação   : 25/11/2004


    * @author Tonismar Régis Bernardo

    * @ignore

	* $Id: JSDefinirElementos.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    * Casos de uso: uc-05.02.10

*/

/*
$Log$
Revision 1.6  2006/09/15 14:33:07  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

?>

<script type="text/javascript">

function buscaValor(tipoBusca){
    document.frm.stCtrl.value = tipoBusca;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function preencheElemento(){
    var d = document.frm;
    var stTMPTarget = d.target;
    var stTMPAction = d.action;
    d.stCtrl.value = 'preencheElemento';
    d.target = "oculto";
    d.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    d.submit();
    d.target = stTMPTarget;
    d.action = stTMPAction;
}

function preencheCodigoElemento(){
    var d = document.frm;
    var stTMPTarget = d.target;
    var stTMPAction = d.action;
    d.stCtrl.value = 'preencheCodigoElemento';
    d.target = "oculto";
    d.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    d.submit();
    d.target = stTMPTarget;
    d.action = stTMPAction;
}

function validaElemento(){
     var erro = false;
     var mensagem = "";
     stCampo = document.frm.inCodigoElemento;
     if( trim( stCampo.value ) == "" ){
         erro = true;
         mensagem += "@Campo Código inválido!()";
     }

     if( erro ){
          alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
     }
     return !erro;
}

function montaAtributosElementos(elemento){
    if(elemento>0){
    document.frm.inCodigoElemento.value = elemento;
    }
    document.frm.stElemento.value = elemento;
    document.frm.stCtrl.value = 'montaAtributosElementos';
    var stTraget        = document.frm.target;
    var stAction        = document.frm.action;
    var cmbProfissao    = document.frm.cmbElemento;

//    document.getElementById("stNomElemento").value = stNomElemento;

    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTraget;
}



function incluirElementos(){
    if( validaElemento() ){
        document.frm.stCtrl.value = 'montaElementos';
        var stTraget = document.frm.target;
        document.frm.target = "oculto";
        var stAction = document.frm.action;
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
        document.frm.submit();
        document.frm.action = stAction;
        document.frm.target = stTraget;
    }
}

function limparElemento(){
    document.frm.inCodigoElemento.value = "";
    document.getElementById('lsElementos').innerHTML = "&nbsp;";
    document.getElementById('spnElementos').innerHTML = "";
    buscaValor('limpaElementos');
    document.frm.reset();
}

function excluirDado( stAcao, inLinha ){
    document.frm.stCtrl.value = stAcao;
    var stTraget = document.frm.target;
    document.frm.target = "oculto";
    var stAction = document.frm.action;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inLinha='+inLinha ;
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTraget;
}
function preencheProxCombo( inPosicao  ){
    document.frm.stCtrl.value = 'preencheProxCombo';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inPosicao='+inPosicao;
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function preencheCombosAtividade(){
    document.frm.stCtrl.value = 'preencheCombosAtividade';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}
/*
function limparAtividades(){
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'limparAtividade';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inIndice='+inIndice;
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
}
*/
function limparAtividade(){
    document.frm.stChaveAtividade.value = '';
    preencheCombosAtividade();
    document.frm.stCtrl.value = 'preecheComboAtividade';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}
</script>

