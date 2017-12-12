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
    * Página de funções javascrfipt de CNAE
    * Data de Criação   : 24/11/2004


    * @author Tonismar Régis Bernardo

	* $Id: JSProcurarCnae.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    * @ignore
*/

/*
$Log$
Revision 1.3  2006/09/15 13:46:36  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
?>

<script type="text/javascript">

function preencheProxComboCnae( inPosicao  ){
    document.frm.stCtrl.value = 'preencheProxComboCnae';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inPosicao='+inPosicao;
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function preencheCombosCnae(){
    document.frm.stCtrl.value = 'preencheCombosCnae';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function Insere(cod_cnae,nom_atividade){
    var sNum;
    var sNom;
    sNum = cod_cnae;
    sNom = nom_atividade;
    window.opener.parent.frames['telaPrincipal'].document.frm.inNumCnae.value = sNum;
    window.opener.parent.frames['telaPrincipal'].document.getElementById('inNumCnae').innerHTML = sNom;
    window.close();
}

function filtrar(){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;   
    document.frm.action = '<?=$pgFilt;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTraget;
    document.frm.action = stAction;
}

</script>
