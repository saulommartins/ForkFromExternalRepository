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
    * Include JavaScript para Calendario 
    * Data de Criação   : 19/05/2005


    * @author Desenvolvedor: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @supackage Regras
    * @package Urbem

    * $Id: JSManterCalendario.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    * Casos de uso: uc-05.03.03

*/
?>

<script type="text/javascript">

function mudaMenu(func){
    sPag="../../../../../../gestaoAdministrativa/fontes/PHP/framework/instancias/index/menu.php?<?=Sessao::getId();?>&nivel=2&cod_func_pass="+func;
    parent.parent.frames["telaMenu"].location.replace(sPag);}

function preencheCredito(){
    var d = document.frm;
    var stTMPTarget = d.target;
    var stTMPAction = d.action;
    d.stCtrl.value = 'preencheCredito';
    d.target = "oculto";
    d.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    d.submit();
    d.target = stTMPTarget;
    d.action = stTMPAction;
}

function preencheCodigoCredito(){
    var d = document.frm;
    var stTMPTarget = d.target;
    var stTMPAction = d.action;
    d.stCtrl.value = 'preencheCodigoCredito';
    d.target = "oculto";
    d.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    d.submit();
    d.target = stTMPTarget;
    d.action = stTMPAction;
}

function alterarDado( stAcao, inLinha ){
    document.frm.stCtrl.value = stAcao;
    var stTraget = document.frm.target;
    document.frm.target = "oculto";
    var stAction = document.frm.action;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inLinha='+inLinha ;
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTraget;
}

function incluirGrupo(){
    document.frm.stCtrl.value = 'montaGrupo';
    var stTraget = document.frm.target;
    document.frm.target = "oculto";
    var stAction = document.frm.action;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTraget;
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

function buscaValor(tipoBusca){
    var stTMPTarget = document.frm.target;
    var stTMPAction = document.frm.action;
    document.frm.stCtrl.value = tipoBusca;
    document.frm.target = 'oculto';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTMPTarget;
    document.frm.action = stTMPAction;
}

function Cancelar(){
<?php
    $link = Sessao::read( "link" );
    $stLink = "&pg=".$link["pg"]."&pos=".$link["pos"];
?>
    document.frm.target = "";
    document.frm.action = "<?=$pgList.'?'.Sessao::getId().$stLink;?>";
    document.frm.submit();
}


function Limpar(){
    buscaValor('limparGrupos');
}

</script>
