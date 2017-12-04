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
    * Página de JavaScript para Manter Inscricao
    * Data de Criação   : 25/11/2004


    * @author Tonismar Régis Bernardo

    * @ignore

	* $Id: JSManterInscricao.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    * Casos de uso: uc-05.02.10

*/

/*
$Log$
Revision 1.4  2006/09/15 14:33:07  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

?>

<script type="text/javascript">

function preencheEnquadramento(){
    var d = document.frm;
    var stTMPTarget = d.target;
    var stTMPAction = d.action;
    d.stCtrl.value = 'preencheEnquadramento';
    d.target = "oculto";
    d.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    d.submit();
    d.target = stTMPTarget;
    d.action = stTMPAction;
}

function preencheCodigoEnqgadramento(){
    var d = document.frm;
    var stTMPTarget = d.target;
    var stTMPAction = d.action;
    d.stCtrl.value = 'preencheCodigoEnquadramento';
    d.target = "oculto";
    d.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    d.submit();
    d.target = stTMPTarget;
    d.action = stTMPAction;
}

function buscaValor(tipoBusca){
    BloqueiaFrames(true,false);
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = tipoBusca;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function busca(tipoBusca){
    var stTMPTarget = document.frm.target;
    var stTMPAction = document.frm.action;
    document.frm.stCtrl.value = tipoBusca;
    document.frm.target = 'oculto';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTMPTarget;
    document.frm.action = stTMPAction;
}

function preencheCategoria(){
    var d = document.frm;
    var stTarget = document.frm.target;
    document.frm.stCtrl.value = 'preencheCategoria';
    d.target = "oculto";
    d.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    d.submit();
    d.target = stTarget;
    d.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function preencheCodigoCategoria(){
    var d = document.frm;
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    d.stCtrl.value = 'preencheCodigoCategoria';
    d.target = "oculto";
    d.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    d.submit();
    d.target = stTarget;
    d.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
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

function validaSocio(){
     var erro = false;
     var mensagem = "";
     stCampo = document.frm.inCodigoSocio;
     if( trim( stCampo.value ) == "" ){
         erro = true;
         mensagem += "@Campo Código inválido!()";
     }
     stCampo = document.frm.flQuota;
     if( trim( stCampo.value ) == "" ){
         erro = true;
         mensagem += "@Campo Quota inválido!()";
     }
     if( erro ){
          alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
     }
 return !erro;
}

function incluirSocio(){
    if( validaSocio() ){
        document.frm.stCtrl.value = 'montaSocio';
        var stTraget = document.frm.target;
        document.frm.target = "oculto";
        var stAction = document.frm.action;
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
        document.frm.submit();
        document.frm.action = stAction;
        document.frm.target = stTraget;
    }
}

function limparSocio(){
   document.frm.inCodigoSocio.value = "";
   document.getElementById('stNomeSocio').innerHTML = "&nbsp;";
   document.frm.flQuota.value = "";
}

function incluirHorario(){

    if ( mensagem == '' ){
        buscaValor('montaHorario');
    } else {
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
        return false;
    }
}

function Limpar(){
    document.getElementById("stNomCGM").innerHTML = "&nbsp;";
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

</script>
