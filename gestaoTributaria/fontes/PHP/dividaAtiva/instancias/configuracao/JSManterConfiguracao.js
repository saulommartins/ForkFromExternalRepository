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
    * Arquivo com funcoes JavaScript para Configuração Divida Ativa
    * Data de Criação: 05/05/2006


    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @ignore

    * $Id: JSManterConfiguracao.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    * Casos de uso: uc-05.04.01
*/

/*
$Log$
Revision 1.7  2007/02/28 17:13:01  cercato
Bug #8515#

Revision 1.6  2006/09/15 14:36:02  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

?>

<script type="text/javascript">

function buscaValor(tipoBusca){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;

    document.frm.stCtrl.value = tipoBusca;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTraget;
}

function habilitaValorReferencia ( boHabilita ) {
    if( boHabilita == 'false' ){
        buscaValor("desativarSpam");
        document.frm.stTipoValorReferencia[0].disabled = true;
        document.frm.stTipoValorReferencia[1].disabled = true;
    } else {
        document.frm.stTipoValorReferencia[0].disabled = false;
        document.frm.stTipoValorReferencia[1].disabled = false;
        buscaValor("desativarSpam");
    }
}

function ControleLeiDA() {
    document.frm.stLeiDA.disabled = !document.frm.boLeiDA.checked;
    if ( !document.frm.boLeiDA.checked ) {
        document.frm.stLeiDA.value = '';
    }
}

function ControleMetCalc() {
    document.frm.stMetodologiaCalculo.disabled = !document.frm.boMetCalc.checked;
    if ( !document.frm.boMetCalc.checked ) {
        document.frm.stMetodologiaCalculo.value = '';
    }
}

function ControleResp2() {
    document.frm.stChefeDepartamento.disabled = !document.frm.boResp2.checked;
    if ( !document.frm.boResp2.checked ) {
        document.frm.stChefeDepartamento.value = '';
    }
}

function ControleMsg() {
    document.frm.stMensagem.disabled = !document.frm.boMsg.checked;
    if ( !document.frm.boMsg.checked ) {
        document.frm.stMensagem.value = '';
    }
}

function habilitaCredito( boHabilita ){
    if( boHabilita == 'false' ){
        document.frm.inCreditoDivida.disabled = true;
    } else {
        document.frm.inCreditoDivida.disabled = false;
    }
}

function LimparInscricao(){
    var x;
    //------
    document.frm.reset();
    document.getElementById("stCreditoDivida").innerHTML = "&nbsp;";
    document.frm.inCreditoDivida.value = "";
    document.frm.stValorReferencia.value = "";
    document.frm.stTipoValorReferencia.value = "";
    document.frm.stUtilizarCreditoDividaAtiva.value = "";
    document.frm.stNumeracaoInscricao.value = "";
    for (x=0; x<2; x++) {
        document.frm.stNumeracaoInscricao[x].checked = false;
        document.frm.stUtilizarCreditoDividaAtiva[x].checked = false;
        document.frm.stTipoValorReferencia[x].checked = false;
        document.frm.stValorReferencia[x].checked = false;
    }

    habilitaCredito('false');
    habilitaValorReferencia('false');
    buscaValor("limpaArray");

    document.frm.stValorReferencia[0].focus();
}

function LimparLivro(){
    var x;
    //------
    document.frm.reset();
    document.frm.stNumFolSeq.value = "";
    document.frm.inNumFolLivro.value = "";
    document.frm.inNumIniLivro.value = "";
    for (x=0; x<2; x++) {
        document.frm.stNumFolSeq[x].checked = false;
    }

    document.frm.inNumIniLivro.focus();
}

</script>
