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
    * Javascript para Licenca
    * Data de Criação   : 02/12/2004
    * @author Analista: Ricardo Lopes
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues
    * @package URBEM
    * @subpackage Regra

	* $Id: JSAlterarLicencaGeral.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    * Casos de uso: uc-05.02.12

*/

/*
$Log$
Revision 1.4  2006/09/15 14:33:14  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

?>
<script type="text/javascript">
/* ALTERAR LICENCA GERAL*/
function focusIncluir(){
    document.frm.inProcesso.focus();
}


function buscaValor(tipoBusca){
    document.frm.stCtrl.value = tipoBusca;
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTraget;
    document.frm.action = stAction;
}

function montaAtributosUf(){
    document.frm.stCtrl.value = 'montaAtributosUf';
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTraget;
}
function validaDataLicenca() {
    var erro       = false;
    var mensagem   = "";
    stDataInicio  = document.frm.dtDataInicio.value;
    DiaInicio     = stDataInicio.substring(0,2);
    MesInicio     = stDataInicio.substring(3,5);
    AnoInicio     = stDataInicio.substr(6);

    stDataTermino = document.frm.dtDataTermino.value;
    DiaTermino    = stDataTermino.substring(0,2);
    MesTermino    = stDataTermino.substring(3,5);
    AnoTermino    = stDataTermino.substr(6);

    var dataInicio  = "";
    var dataTermino = "";
    dataInicio  += AnoInicio+MesInicio+DiaInicio;
    dataTermino += AnoTermino+MesTermino+DiaTermino;

    if ( dataTermino < dataInicio ) {
        erro = true;
        mensagem += "@Campo Data de Término deve ser posterior ao campo Data de Início!";
        document.frm.dtDataTermino.value = "";
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
        setTimeout("document.getElementById('dtDataTermino').focus()",500);
    }
}

function montaAtributosElementos(){
    BloqueiaFrames(true,false);
    document.frm.stCtrl.value = 'montaAtributosElementos';
    var stTraget        = document.frm.target;
    var stAction        = document.frm.action;
    var cmbProfissao    = document.frm.cmbElemento;
    var x = document.getElementById("cmbElementos")
    var stNomElemento   = x.options[x.selectedIndex].text;

    document.getElementById("stNomElemento").value = stNomElemento;

    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTraget;
}

function Limpar(){
   limpaFormulario();
   buscaValor('LimparSessao');
   limparElementos();
   document.frm.reset();
}

function Cancelar(){
<?php
    $link = Sessao::read( "link" );
    $stLink = "&pg=".$link["pg"]."&pos=".$link["pos"];
?>
    document.frm.target = "telaPrincipal";
        document.frm.action = "<?=$pgList.'?'.Sessao::getId().$stLink;?>";
            document.frm.submit();
}

//FUNCAO GENERICA DE EXCLUSAO DE LINHAS DAS LISTAS
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

//FUNCOES PARA A LISTA DE PROPRIETARIOS
function validaProprietario(){
     var erro = false;
     var mensagem = "";
     stCampo = document.frm.inNumCGM;
     if( trim( stCampo.value ) == "" ){
         erro = true;
         mensagem += "@Campo CGM inválido!()";
     }
     stCampo = document.frm.flQuota;
     if( trim( stCampo.value ) == "" ){
         erro = true;
         mensagem += "@Campo Quota inválido!()";
     }
     if( numericToFloat( stCampo.value ) == 0 ){
         erro = true;
         mensagem += "@Campo Quota deve ter valor maior que zero!";
         stCampo.focus();
     }
     if( numericToFloat( stCampo.value ) > 100 ){
         erro = true;
         mensagem += "@Campo Quota deve ter valor menor ou igual a 100!";
         stCampo.focus();
     }
     if( erro ){
          alertaAviso(mensagem,'form','n_incluir','<?=Sessao::getId();?>', '../');
     }
 return !erro;
}

function incluirElemento(){
    document.frm.stCtrl.value = 'incluirElemento';
    var stTraget = document.frm.target;
    document.frm.target = "oculto";
    var stAction = document.frm.action;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTraget;
}

function limparElementos(){
    document.frm.stCodigoElemento.value = '';
    document.getElementById('spnAtributosElemento').innerHTML = '';
    document.getElementById('cmbElementos').selectedIndex = '0';
    document.getElementById('inNomCGM').innerHTML = '&nbsp;';
}
</script>
