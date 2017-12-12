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
  * Página de JavaScript para Cálculo
  * Data de criação : 08/06/2005


  * @author Analista: Fábio Bertoldi
  * @author Programador: tonismar R. Bernardo

  * @ignore

    * $Id: JSManterCalculo.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

* Casos de uso: uc-05.03.05
*/

?>

<script type="text/javascript">
function buscaValor(tipoBusca){

    //alert ( 'tipo: ' + tipoBusca );

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
function montaModeloCarne(){
    document.frm.stCtrl.value = "montaModeloCarne";
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.action = stAction;
    document.frm.target = stTraget;
}

function montaModeloCarne2(){
    document.frm2.stCtrl.value = "montaModeloCarne";
    var stTraget = document.frm2.target;
    var stAction = document.frm2.action;
    document.frm2.target = "oculto";
    document.frm2.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm2.submit();
    document.frm2.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm2.action = stAction;
    document.frm2.target = stTraget;

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

function limpaLancamento() {
    document.frm.reset();
    buscaValor("limpaLancamento");
}

function Limpar(){
    buscaValor("limpaSessao");
    document.frm.reset();
    limparCredito();
    if (document.frm.stDescricao)
        document.frm.stDescricao.focus();
}
function limparCredito(){
    if ( document.frm.stCredito )
        document.getElementById('stCredito').innerHTML = '&nbsp;';

    if ( document.frm.inCodCredito ) {
        document.frm.inCodCredito.value = "";
        document.frm.inCodCredito.focus();
    }
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
<?php
    $link = Sessao::read( "link" );
 //   $stLink = "&pg=".$link["pg"]."&pos=".$link["pos"]."&boVinculoEdificacao=".$_REQUEST['boVinculoEdificacao']."";
?>
    document.frm.target = "";
    document.frm.action = "<?=$pgList.'?'.Sessao::getId().$stLink;?>";
    document.frm.submit();
}

function incluirCredito(){
    var html = document.getElementById("stCredito").innerHTML;
//    alert(html.trim());
    if ( (document.frm.inCodCredito.value != '') || (html != '&nbsp;') ){
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
        mensagem += "@Campo Crédito invalido!";
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
        mensagem += "@Campo Acréscimo invalido!";
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

function incluirParcela(){
    var stTarget   = document.frm.target;
    var stAction   = document.frm.action;
    document.frm.stCtrl.value = 'incluirParcela';
    document.frm.target = 'oculto';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
    document.frm.stTipoParcela[1].checked = true;
    document.frm.stTipoDesconto[1].checked = true;
}

function limparParcela(){
    document.frm.flDesconto.value = '';
    document.frm.dtVencimento.value = '';
    document.frm.stTipoParcela[0].checked = true;
    document.frm.stTipoDesconto[1].checked = true;    
    
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    
    document.frm.target = "oculto";
    document.frm.stCtrl.value = 'limparParcela';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;    
}

function excluirParcela( inIndice ){
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'excluirParcela';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inIndice='+inIndice;
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
}

function habilitaTipoLancamento( boHabilita ){

    if( boHabilita == 'false' ){
        document.frm.boTipoLancamentoManual[0].disabled = true;
        document.frm.boTipoLancamentoManual[1].disabled = true;
    } else {
        document.frm.boTipoLancamentoManual[0].disabled = false;
        document.frm.boTipoLancamentoManual[1].disabled = false;
    }
}

function verificaAction(){
    if( document.frm.boTipoLancamento[0].checked ) {
        document.frm.action = '<?=$pgFormAutomatico;?>?<?=Sessao::getId();?>';
    }else{
        document.frm.action = '<?=$pgFormManual;?>?<?=Sessao::getId();?>';
    }
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
}

function verificaActionManterParametro(){
    if( document.frm.boTipoParametro[0].checked ) {
        document.frm.action = '<?=$pgForm;?>?<?=Sessao::getId();?>';
    }else{
        document.frm.action = '<?=$pgFormGrupo;?>?<?=Sessao::getId();?>';
    }
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
}

function LimparFL(){
    document.frm.boTipoLancamento[0].checked = true;
    document.frm.boTipoLancamentoManual[0].disabled = false;
    document.frm.boTipoLancamentoManual[1].disabled = false;
}
function lancarCalculos(){
    //document.frm2.stCtrl.value = 'lancar_calculos';    
    document.frm2.stAcao = 'lanc_via_relat_exec';
    document.frm2.boLancar = '0';
    document.frm2.target = "oculto";
    document.frm2.action = 'PREfetuarLancamentos.php?<?=Sessao::getId();?>';
    document.frm2.submit();
}


function validarGrupoCredito( grupo ){    
    if( grupo.value.length > 0 ){
        var stTarget = document.frm.target;
        var stAction = document.frm.action;        
        
        document.frm.target = "oculto";
        document.frm.stCtrl.value = "validarGrupoCredito";
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&stChaveGrupo='+grupo.value;
        document.frm.submit();         
        
        document.frm.target = stTarget;
        document.frm.action = stAction;              
    }    
}

function validarCredito( credito ){
    if( credito.value.length > 0 ){
        var stTarget = document.frm.target;
        var stAction = document.frm.action;
    
        document.frm.target = "oculto";
        document.frm.stCtrl.value = "validarCredito";
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&stChaveGrupo='+credito.value;
        document.frm.submit();
    
        document.frm.target = stTarget;
        document.frm.action = stAction;
    }
}

function mudaAcao(codGrupo) {
    var stTarget = document.frm2.target;
    var stAction = document.frm2.action;
    var param = 'stAcao=validar&hidden=1&boInlcuir_l='+codGrupo;    
    
    document.frm2.target = "oculto";         
    document.frm2.action = "PRManterCalculo.php?<?=Sessao::getId();?>&"+param;           
    document.frm2.submit();
    
    document.frm2.target = stTarget;
    document.frm2.action = stAction;    
}

</script>