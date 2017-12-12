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
    * Arquivo JavaScript
    * Data de Criação   : 14/02/2006


    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou

    * @ignore

    * $Id: JSBuscaLocalizacao.js 63760 2015-10-07 13:51:43Z evandro $

    * Casos de uso: uc-05.01.03
*/
?>

<script type="text/javascript">

function insere(inCodigo,stNome){
    if( eval( window.opener.parent.frames['telaPrincipal'].document.getElementById('<?=$_REQUEST["campoNom"];?>') != null ) ) {
        window.opener.parent.frames['telaPrincipal'].document.getElementById('<?=$_REQUEST["campoNom"];?>').innerHTML = stNome;
        var campoHidden = eval( 'window.opener.parent.frames["telaPrincipal"].document.frm.<?=$_REQUEST["campoNom"];?>' );
        if( campoHidden != null ) campoHidden.value=inCodigo;
    }
    window.opener.parent.frames['telaPrincipal'].document.frm.<?=$_REQUEST['campoNum'];?>.value = inCodigo;
    window.opener.parent.frames['telaPrincipal'].document.frm.<?=$_REQUEST['campoNum'];?>.focus();
    window.close();
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
    //BloqueiaFrames(true,false);
    document.frm.stCtrl.value = 'preencheCombos';
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTraget;
}
function limpaCombos(){
    document.frm.stCtrl.value = 'preencheCombos';
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTraget;
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
