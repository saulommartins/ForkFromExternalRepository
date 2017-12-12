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
    * Página de unções em javascript para o cadastro de face de quadra
    * Data de Criação   : 10/11/2004


    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    * $Id: JSManterFaceQuadra.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    * Casos de uso: uc-05.01.07
*/

/*
$Log$
Revision 1.6  2006/09/18 10:30:35  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
?>
<script type="text/javascript">

function preencheProxCombo( inPosicao  ){
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'preencheProxCombo';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inPosicao='+inPosicao;
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTarget;
}

function preencheCombos(){
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'preencheCombos';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.action = stAction;
    document.frm.target = stTarget;

}

function buscarTrecho(){
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.btnIncluirTrecho.disabled = true;
    document.frm.stCtrl.value = 'buscarTrecho';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.action = stAction;
    document.frm.target = stTarget;
    document.frm.btnIncluirTrecho.disabled = false;
}

function buscaLogradouro(){
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'buscarLogradouro';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.action = stAction;
    document.frm.target = stTarget;

}

function excluiDado(stControle, inId){
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = stControle;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inId=' + inId;
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.action = stAction;
    document.frm.target = stTarget;
}

function incluirTrecho(){
    var mensagem = validarTrecho();

    if ( mensagem == '' ){
        buscarValor('MontaTrecho');
    } else {
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
        return false;
    }
}

function validarTrecho(){
    var mensagem = '';
    var d = document.frm;

    if ( d.stTrecho.value == 0) {
        mensagem += "@Campo Descrição do Trecho inválido!( )";
    }
    if ( d.inNumTrecho.value == 0) {
        mensagem += "@Campo Trecho inválido!( )";
    }
    return mensagem;
}

function buscarValor(tipoBusca){
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = tipoBusca;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.action = stAction;
    document.frm.target = stTarget;
}

function buscaDado(tipoBusca){
    if (tipoBusca == 'buscaLocalizacao') {
        BloqueiaFrames(true,false);
    }

    document.frm.stCtrl.value = tipoBusca;
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTraget;
}

function Cancelar(){
<?php
    $stLink = Sessao::read('stLink');
?>
    document.frm.target = "telaPrincipal";
    document.frm.action = "<?=$pgList.'?'.Sessao::getId().$stLink;?>";
    document.frm.submit();
}

function limparTrecho() {
    var d = document.frm;
    d.inNumTrecho.value = "";
    document.getElementById("stNumTrecho").innerHTML = "&nbsp;";
}

function LimparFL(){
    document.frm.reset();
    document.getElementById("campoInner").innerHTML = "&nbsp;";
    preencheCombos();
}

function Limpar(){
    buscarValor('limparSessaoTrechos');
    document.frm.reset();
    preencheCombos();
    document.getElementById("inNumTrecho").innerHTML = "&nbsp;";
    limparSpnTrecho();
    document.frm.btnIncluirTrecho.disabled = false;
}

function limparSpnTrecho(){
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    limparTrecho();
    document.frm.stCtrl.value = 'limparSpnTrecho';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.getElementById("spnTrechoCadastrado").innerHTML = "";
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.action = stAction;
    document.frm.target = stTarget;
}

function submeteFiltro(){
    stLocalizacao = document.frm.stChaveLocalizacao.value;
    stFaceQuadra  = document.frm.inCodigoFace.value;
    if ( stFaceQuadra == "" && stLocalizacao == "" ){
        mensagem = "Campos Código Face de Quadra ou Localização não foram preenchidos!";
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
    } else {
        Salvar();
    }
}
</script>

