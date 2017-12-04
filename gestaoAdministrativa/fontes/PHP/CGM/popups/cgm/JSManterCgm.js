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
?>

<script type="text/javascript">

function buscaValor(valor,comp){
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = valor;
    document.frm.target = 'oculto';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>'+comp;
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
}

function insereCGMpopup(num,nom){
    var sNum;
    var sNom;
    sNum = num;
    sNom = nom;
    var origem = ( window.opener ) ? window.opener.parent : window.parent.window.opener.parent;

    if( origem.frames['telaPrincipal'].document.getElementById('<?=$_REQUEST["campoNom"]?>') ) { origem.frames['telaPrincipal'].document.getElementById('<?=$_REQUEST["campoNom"]?>').innerHTML = sNom; }
    
    origem.frames['telaPrincipal'].document.frm.<?=$_REQUEST["campoNum"]?>.value = sNum;
    origem.frames['telaPrincipal'].document.frm.Hdn<?=$_REQUEST["campoNum"]?>.value = sNum;

<? if ( $_REQUEST["campoNom"]) { ?>
    origem.frames['telaPrincipal'].document.frm.<?=$_REQUEST["campoNom"]?>.value = sNom;
    origem.frames['telaPrincipal'].document.frm.<?=$_REQUEST["campoNum"]?>.focus();
<? } ?>

<? if ($_REQUEST["campoNom"] == "nomSeguradora"){ ?>
    origem.frames['telaPrincipal'].document.frm.dtVencimento.focus();
<? } ?>

<? if ($_REQUEST["campoNom"] == "sFornecedor"){ ?>
    origem.frames['telaPrincipal'].document.frm.valorBem.select();
<? } ?>

<? if ($_REQUEST["campoNom"] == "nomMotorista"){ ?>
    origem.frames['telaPrincipal'].document.frm.<?=$_REQUEST["campoNum"]?>.focus();
<? } ?>

    if ( window.opener ){
        window.close();
    }else{
        window.parent.window.close();
    }
}


</script>
