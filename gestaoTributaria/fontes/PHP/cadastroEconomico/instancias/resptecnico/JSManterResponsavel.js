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
    * Pagina de processamento para Responsavel Tecnico
    * Data de Criação   : 15/04/2005


    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou

    * @ignore

	* $Id: JSManterResponsavel.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    *Casos de uso: uc-05.02.04
*/

/*
$Log$
Revision 1.6  2006/09/15 14:33:35  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

?>
<script type="text/javascript">
/*
function focusIncluir(){
    document.frm.inProcesso.focus();
}
*/

function incluirResponsavel() {
    var stTarget   = document.frm.target;
    var stAction   = document.frm.action;
    var erro       = false;
    var mensagem   = "";

    selecionaTodosSelect(document.frm.inCodProfissoesSelecionadas); //funcao que seleciona todos no combo multiplo

    stCampoResponsavelCGM = document.frm.inNumResponsavelCGM;
    if ( stCampoResponsavelCGM.value == "" ) {
        erro = true;
        mensagem += "@Campo Responsável inválido!";
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
    } else {
        document.frm.stCtrl.value = 'incluirResponsavel';
        document.frm.target = "oculto";
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
        document.frm.submit();
    }
    document.frm.target = stTarget;
    document.frm.action = stAction;
}

function excluirResponsavel( inIndice1, inIndice2 ) {
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'excluirResponsavel';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inIndice1='+inIndice1+'&inIndice2='+inIndice2;
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
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

function montaAtributosProfissao(){
    document.frm.stCtrl.value = 'montaAtributosProfissao';
    var stTraget        = document.frm.target;
    var stAction        = document.frm.action;
    var cmbProfissao    = document.frm.cmbProfissao;
//    document.frm.inCodigoProfissao.value = cmbProfissao.options[cmbProfissao.selectedIndex].value;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTraget;
}

function Limpar(){
   limpaFormulario();
   buscaValor('LimparSessao');
   document.frm.reset();
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


function abrePopUp(arquivo,nomeform,camponum,camponom,tipodebusca,sessao,width,height){
    if (arquivo == "../../../../../../gestaoTributaria/fontes/PHP/cadastroEconomico/popups/responsaveltecnico/FLProcurarResponsavel.php") {
        var stTarget = document.frm.target;
        var stAction = document.frm.action;

        selecionaTodosSelect(document.frm.inCodProfissoesSelecionadas); //funcao que seleciona todos no combo multiplo

        var elemento = document.getElementById('inCodProfissoesSelecionadas');
        var size = elemento.length;
        var i = 0;
        var stValores="";
        while( i < size){
            stValores = stValores+elemento.options[i].value+",";
            i++;
        }
        document.frm.stAtividades.value = stValores;
        document.frm.stCtrl.value = 'preparaProfissoes';
        document.frm.target = "oculto";
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
        document.frm.submit();
        document.frm.target = stTarget;
        document.frm.action = stAction;

        camponum+= '&Profissoes='+document.frm.stAtividades.value;
    }

    if (width == '') {
        width = 800;
    }
    if (height == '') {
        height = 550;
    }
    var x = 0;
    var y = 0;
    var sessaoid = sessao.substr(15,6);
    var sArq = ''+arquivo+'?'+sessao+'&nomForm='+nomeform+'&campoNum='+camponum+'&campoNom='+camponom+'&tipoBusca='+tipodebusca;
    var sAux = "prcgm"+ sessaoid +" = window.open(sArq,'prcgm"+ sessaoid +"','width="+width+",height="+height+",resizable=1,scrollbars=1,left="+x+",top="+y+"');";
    eval(sAux);
}

</script>
