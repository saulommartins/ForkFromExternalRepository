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
/** Página JS do Consultar Escrituração

    * Data de Criação   : 13/12/2006


    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Márson Luís Oliveira de Paula 
    * @ignore

    * $Id: JSConsultarEscrituracao.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    * Casos de uso: uc-05.03.19
*/

/*
$Log$
Revision 1.2  2007/02/22 12:21:43  cassiano
Consulta escrituração

Revision 1.1  2007/01/02 12:27:58  marson
Inclusão Consulta de Escrituração de Receita.

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

function Cancelar(){
<?php
    $link = Sessao::read( "link" );
    $stLink = "&pg=".$link["pg"]."&pos=".$link["pos"];
?>
    document.frm.target = "";
    document.frm.action = "<?=$pgList.'?'.Sessao::getId().$stLink;?>";
    document.frm.submit();
}

function incluirNota() {
    var stTarget   = document.frm.target;
    var stAction   = document.frm.action;

    document.frm.stCtrl.value = 'incluirNota';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
}

function excluirNota( inIndice1, inIndice2 ) {
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'excluirNota';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inIndice1='+inIndice1+'&inIndice2='+inIndice2;
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
}

function incluirServico() {
    var stTarget   = document.frm.target;
    var stAction   = document.frm.action;

    document.frm.stCtrl.value = 'incluirServico';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
}

function alterarServico( inIndice1, inIndice2, inIndice3, inIndice4, inIndice5, inIndice6 ) {
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'alterarServico';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inIndice1='+inIndice1+'&inIndice2='+inIndice2+'&inIndice3='+inIndice3+'&inIndice4='+inIndice4+'&inIndice5='+inIndice5+'&inIndice6='+inIndice6;
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
}

function excluirServico( inIndice1, inIndice2, inIndice3, inIndice4, inIndice5, inIndice6 ) {
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'excluirServico';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inIndice1='+inIndice1+'&inIndice2='+inIndice2+'&inIndice3='+inIndice3+'&inIndice4='+inIndice4+'&inIndice5='+inIndice5+'&inIndice6='+inIndice6;
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
}

function preencheProxComboServico( inPosicao  ){
    document.frm.stCtrl.value = 'preencheProxComboServico';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inPosicao='+inPosicao;
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function preencheCombosServico(){
    document.frm.stCtrl.value = 'preencheCombosServico';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function LimparForm(){
   document.frm.dtEmissao.value = "";

   buscaValor('LimparFormulario');
}

function consultarEscrituracao(inCodModalidade, inCodAtividade, inInscricaoEconomica,inNumCGM, timestamp, competencia){
    var stURL = "<?=$pgForm;?>&inCodModalidade="+inCodModalidade+"&inCodAtividade="+inCodAtividade+"&inInscricaoEconomica="+inInscricaoEconomica;
    stURL += "&inNumCGM=" + inNumCGM + "&timestamp=" + timestamp;
    stURL += "&competencia=" + competencia;
    window.open(stURL,'telaPrincipal');
    window.close();
}

function listarServicos(stLink, inCodNota){
    stLink +='&inCodNota=' + inCodNota;
    ajaxJavaScript(stLink,'listaServico');
}

function buscarDetalhamentoValoresDataBase(){
    if( document.getElementById('dtDataBase').value.length > 0 ){
        var stURL = '<?=$pgOcul.'?'.Sessao::getId();?>&inCodCalculo=<?=$inCodCalculo;?>';
        stURL += '&dtDataBase='+document.getElementById('dtDataBase').value;
        ajaxJavaScript(stURL,'consultarDetalheValoresDataBase');
    }
}

function VoltarLista(){
    document.frm.action ='<?=$pgList.'?'.Sessao::getId();?>';
    document.frm.submit();
}
</script>