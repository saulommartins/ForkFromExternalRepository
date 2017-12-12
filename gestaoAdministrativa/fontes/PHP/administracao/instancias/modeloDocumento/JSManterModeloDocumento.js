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
    * Arquivo com funcoes JavaScript para Consulta de Arrecadacao
    * Data de Criação: 09/06/2005


    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Lucas Teixeira Stephanou

    * @ignore

    $Revision: 7967 $
    $Name$
    $Autor: $
    $Date: 2006-03-28 17:57:34 -0300 (Ter, 28 Mar 2006) $

    * Casos de uso: uc-01.03.100
*/
?>

<script type="text/javascript">

function Limpar(){
    document.frm.reset();
}
function limpaArquivo(){   
    destacaElemento("inCodDocumentoTxt");
}
function destacaElemento(elementox){
    parent.frames["telaPrincipal"].document.getElementById(elementox).style.border = "2px solid #FF5109";
    parent.frames["telaPrincipal"].document.getElementById(elementox).addEventListener("onfocus","degrade('"+elementox+"')",false);
}
function degrade(elementox){
    elemento = window.parent.frames["telaPrincipal"].document.getElementById(elementox);
    //var antigaBorda = elemento.style.border;
    var antigaBorda = "";
    setTimeout(elemento.id+".style.border='2px solid #FF5109'",200);
    setTimeout(elemento.id+".style.border='2px solid #FF2d27'",400);
    setTimeout(elemento.id+".style.border='2px solid #FF4e45'",600);
    setTimeout(elemento.id+".style.border='2px solid #FF6d65'",800);
    setTimeout(elemento.id+".style.border='2px solid #FF8d86'",1000);
    setTimeout(elemento.id+".style.border='2px solid #FFaaa6'",1200);
    setTimeout(elemento.id+".style.border='2px solid #FFd9d8'",1400);
    setTimeout(elemento.id+".style.border='"+antigaBorda+"'",1600);
    setTimeout(elemento.id+".focus();",1700);
}

function buscaValor(tipoBusca){
    var stCtrl      = document.frm.stCtrl.value;
    var stAcao      = document.frm.action;
    var stTarget    = document.frm.target;
    document.frm.stCtrl.value = tipoBusca;
    document.frm.target = 'oculto';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTarget; 
    document.frm.action = stAcao
}
function excluiDado(stControle, inId){
    var stCtrl      = document.frm.stCtrl.value;
    var stAcao      = document.frm.action;
    var stTarget    = document.frm.target;
    document.frm.stCtrl.value = stControle;
    document.frm.target = 'oculto';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inId=' + inId;
    document.frm.submit();
    document.frm.target = stTarget; 
    document.frm.action = stAcao
}

function incluirArquivo(){
    var stCtrl      = document.frm.stCtrl.value;
    var stAcao      = document.frm.action;
    var stTarget    = document.frm.target;
    document.frm.stCtrl.value = "incluirArquivo";
    document.frm.target = 'oculto';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTarget; 
    document.frm.action = stAcao
}
function visualizarDetalhes(stCtrl,cod_lancamento,numeracao,exercicio,cod_parcela,data){    
    stPag = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inCodLancamento='+cod_lancamento+'&inNumeracao='+numeracao+'&inExercicio='+exercicio+'&inCodParcela='+cod_parcela+'&dtDataBase='+data;
    ajax(stPag,'detalheParcela','spnDetalhes');
}    
function visualizarDetalhesAtualiza(stCtrl,cod_lancamento,numeracao,exercicio,cod_parcela,data){
    stPag = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inCodLancamento='+cod_lancamento+'&inNumeracao='+numeracao+'&inExercicio='+exercicio+'&inCodParcela='+cod_parcela+'&dtDataBase='+data+"&stTipoData=br";
    ajax(stPag,'detalheParcela','spnDetalhes');
}   

function baixaArquivo(stCodDocumento,stNomeArquivo,stDir){
    //var link = "../../anexos/"+stDir+"/"+stCodDocumento+"___"+stNomeArquivo;
    var link = stDir+"/"+stCodDocumento+"___"+stNomeArquivo;
    if(stCodDocumento != ""){
        parent.frames["oculto"].location = link;
    }else{
        alertaAviso('Este arquivo ainda não foi salvo. Pressione OK para salvá-lo.','form','erro','<?=Sessao::getId();?>', '../');
    }
} 
</script>
