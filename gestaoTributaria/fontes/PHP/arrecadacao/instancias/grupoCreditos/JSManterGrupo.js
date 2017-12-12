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
  * Página de JavaScript para Grupo de Crédito
  * Data de criação : 08/06/2005


  * @author Analista: Fábio Bertoldi
  * @author Programador: tonismar R. Bernardo

  * @ignore

    * $Id: JSManterGrupo.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

* Casos de uso: uc-05.03.02
*/

/*
$Log$
Revision 1.4  2006/09/15 11:10:42  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

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
function validaCreditos(){
    var erro = false;
    var mensagem = "";
    var inNumCreditos = eval(document.frm.inNumCreditos.value);
    if ( inNumCreditos < 1 ) {
        document.frm.inCodCredito.focus();
        erro = true;
        mensagem += "Dever haver ao menos um credito agrupado!";
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
    }
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
function Limpar(){
    document.frm.reset();
    limparCredito();
    document.frm.stDescricao.focus();
    buscaValor("limpar");
}
function limparCredito(){
    document.getElementById('stCredito').innerHTML = '&nbsp;';
    document.frm.inCodCredito.value = '';
    document.frm.inCodCredito.focus();
}
function limparGrupo(){
    document.getElementById('stGrupo').innerHTML = '&nbsp;';
    document.frm.inCodGrupo.value = "";
    document.frm.inCodGrupo.focus();
}
function limparAcrescimo(){
    document.getElementById('stAcrescimo').innerHTML = '&nbsp;';
    document.frm.inCodAcrescimo.value = "";
    document.frm.inCodAcrescimo.focus();
}

function Cancelar () {
//alert("<p>Tamanho: "+document.scrollTop + " !");
<?php
  //    $link = Sessao::read( "link" );
 //   $stLink = "&pg=".$link["pg"]."&pos=".$link["pos"]."&boVinculoEdificacao=".$_REQUEST['boVinculoEdificacao']."";
?>
    document.frm.target = "";
    document.frm.action = "<?=$pgList.'?'.Sessao::getId().$stLink;?>";
    document.frm.submit();
}

function incluirCredito(){
    var html = document.getElementById("stCredito").innerHTML;

    if ( (trim(document.frm.inCodCredito.value) != '') || (html != '&nbsp;') ){
        document.frm.stCtrl.value = 'incluirCredito';
        var stTraget = document.frm.target;
        document.frm.target = "oculto";
        var stAction = document.frm.action;
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
        document.frm.submit();
        document.frm.action = stAction;
        document.frm.target = stTraget;
    }else{
        limparCredito();
        erro = true;
        mensagem = "@Campo Crédito invalido!";
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');      
    }
}
function incluirAcrescimo(){
    var html = document.getElementById("stAcrescimo").innerHTML;
//    alert(html.trim());
    if ( (document.frm.inCodAcrescimo.value != '') && (html != '&nbsp;') ){
        document.frm.stCtrl.value = 'incluirAcrescimo';
        var stTraget = document.frm.target;
        document.frm.target = "oculto";
        var stAction = document.frm.action;
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
        document.frm.submit();
        document.frm.action = stAction;
        document.frm.target = stTraget;
    }else{
        limparAcrescimo();
        erro = true;
        mensagem = "@Campo Acréscimo invalido!";
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');      
    }
}
function incluirGrupo(){
    var html = document.getElementById("stGrupo").innerHTML;
    if ( (document.frm.inCodGrupo.value != '') || (html != '&nbsp;') ){
        document.frm.stCtrl.value = 'incluirGrupo';
        var stTraget = document.frm.target;
        document.frm.target = "oculto";
        var stAction = document.frm.action;
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
        document.frm.submit();
        document.frm.action = stAction;
        document.frm.target = stTraget;
    }else{
        limparGrupo();
        erro = true;
        mensagem += "@Campo Grupo invalido!";
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');      
    }
}
</script>
