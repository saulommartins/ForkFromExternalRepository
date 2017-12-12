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
    * Página de funções javascript para o cadastro de Convenio
    * Data de Criação   :04/11/2005


    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho

    * @ignore

    * $Id: JSManterConvenio.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    * Casos de uso: uc-05.05.04
*/

/*
$Log$
Revision 1.5  2006/09/15 14:57:44  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
?>
<script type="text/javascript">

function preencheAgencia(){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'preencheAgencia';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTraget;
    document.frm.action = stAction;
}

function preencheCamposCodigos(){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'preencheCamposCodigos';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTraget;
    document.frm.action = stAction;
}

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

function Limpar(){

    buscaValor('limpaTudo');

}

function excluirConta( inIndice ){
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'excluirConta';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inIndice='+inIndice;
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
}

function limparContas(){
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'limparContas';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
}

function incluirConta() {
    var stTarget   = document.frm.target;
    var stAction   = document.frm.action;
    var erro       = false;
    var mensagem   = "";

    stCampoConta      = document.frm.inNumConta;
    if ( document.frm.inNumConta.value == "" ) {
        erro = true;
        mensagem += "@Campo Conta Corrente inválido!";
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
    } else {
        document.frm.stCtrl.value = 'incluirConta';
        document.frm.target = "oculto";
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
        document.frm.submit();
    }
    document.frm.target = stTarget;
    document.frm.action = stAction;
}

function abrePopUp(arquivo,nomeform,camponum,camponom,tipodebusca,sessao,width,height){
    var stTarget = document.frm.target;
    var stAction = document.frm.action;

    var Num_agencia = document.getElementById('stNumAgenciaTxt');
    var Num_banco = document.getElementById('inCodBanco');

    camponum+= '&NumAgencia='+Num_agencia.value;
    camponum+= '&NumBanco='+Num_banco.value;

    if (width == '') {
        width = 800;
    }
    if (height == '') {
        height = 550;
    }

    var x = 0;
    var y = 0;
    var sessaoid = sessao.substr(15,6);
    var sArq = ''+arquivo+'?'+sessao+'&nomForm='+nomeform+'&campoNum='+camponum+'&campoNom='+camponom;
    var sAux = "prcgm"+ sessaoid +" = window.open(sArq,'prcgm"+ sessaoid +"','width="+width+",height="+height+",resizable=1,scrollbars=1,left="+x+",top="+y+"');";
    eval(sAux);
}

</script>
