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

    * $Id: JSConsultaArrecadacao.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    * Casos de uso: uc-05.03.19
*/

/*
$Log$
Revision 1.17  2007/02/13 10:35:43  dibueno
Melhorias da consulta da arrecadacao

Revision 1.16  2007/02/09 12:01:28  dibueno
Melhorias da consulta da arrecadacao

Revision 1.15  2007/02/07 11:12:26  dibueno
Melhorias da consulta da arrecadacao

Revision 1.14  2007/02/05 11:07:30  dibueno
Melhorias da consulta da arrecadacao

Revision 1.13  2006/09/15 11:04:44  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

?>

<script type="text/javascript">

function Limpar(){
    document.frm.reset();
}

function buscaValor(tipoBusca){
    document.frm.stCtrl.value = tipoBusca;
    document.frm.target = 'oculto';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = 'telaPrincipal';
    document.frm.action = '<?=$pgList;?>?<?=Sessao::getId();?>';
}
function visualizarDetalhes(stCtrl,cod_lancamento,numeracao,exercicio,cod_parcela,data,dtPagamento){    
    stPag = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inCodLancamento='+cod_lancamento+'&inNumeracao='+numeracao+'&inExercicio='+exercicio+'&inCodParcela='+cod_parcela+'&dtDataBase='+data+'&dtPagamento='+dtPagamento;
    ajax(stPag,'detalheParcela','spnDetalhes');
    tooltip.init();
}    
function visualizarDetalhesAtualiza(stCtrl,cod_lancamento,numeracao,exercicio,cod_parcela,dtPagamento,data){
    if ( retornaValidaData(data) ) {
        stPag = '<?=$pgOcul;?>?<?=Sessao::getId();?>&cod_lancamento='+cod_lancamento+'&inNumeracao='+numeracao+'&inExercicio='+exercicio+'&inCodParcela='+cod_parcela+'&dtDataBase='+data+"&stTipoData=br"+'&dtPagamento='+dtPagamento;
        ajax(stPag,'detalheParcela','spnDetalhes');
    }else{
        alertaAviso('Data Infomada no Campo DataBase inválida;','form','erro','<?=Sessao::getId();?>');                 
        document.getElementById('dtDataBase').focus();
        // da de hoje
        var currentTime = new Date();
        var hoje =  currentTime.getDate()+'/'+(currentTime.getMonth()+1)+'/'+currentTime.getFullYear();
//        document.getElementById('dtDataBase').value = hoje; 
        document.getElementById('dtDataBase').value = ''; 
    }
}
function visualizarDetalhesAtualizaReemitida( cod_lancamento, numeracao, exercicio,cod_parcela,dtPagamento, data, dtVencimento, ocorrencia, stIdCarregamento, info_parcela ){

    stCtrl = 'parcela';

    if ( retornaValidaData(data) ) {
        stPag = '<?=$pgOcul;?>?<?=Sessao::getId();?>&cod_lancamento='+cod_lancamento+'&numeracao='+numeracao+'&inExercicio='+exercicio+'&cod_parcela='+cod_parcela+'&database_br='+data+'&pagamento='+dtPagamento+"&boReemitida=true"+'&vencimento='+dtVencimento+'&ocorrencia_pagamento='+ocorrencia+'&stIdCarregamento='+stIdCarregamento+'&info_parcela='+info_parcela;
//alert( data );
//alert ( 'oe' + cod_lancamento + ', ' + numeracao + ', ['+cod_parcela+'] ' + ' mam' );
        ajax (stPag, 'detalheParcela', stIdCarregamento );

    }else{
        alertaAviso('Data Infomada no Campo DataBase inválida;','form','erro','<?=Sessao::getId();?>');                 
        document.getElementById('dtDataBase').focus();
        // da de hoje
        var currentTime = new Date();
        var hoje =  currentTime.getDate()+'/'+(currentTime.getMonth()+1)+'/'+currentTime.getFullYear();
//        document.getElementById('dtDataBase').value = hoje; 
        document.getElementById('dtDataBase').value = ''; 
    }
}
function visualizarDetalhesAtualizaReemitidaCombo( stCtrl, cod_lancamento, descricao, exercicio,cod_parcela, dtPagamento,data, stIdCarregamento, info_parcela ){

//alert ( 'oe' + cod_lancamento + ', ' + descricao + ', ['+cod_parcela+'] ' + ' mam' );

    if ( retornaValidaData(data) ) {
        stPag = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inCodLancamento='+cod_lancamento+'&stDescricao='+descricao+'&inExercicio='+exercicio+'&cod_parcela='+cod_parcela+'&dtDataBase='+data+"&stTipoData=br"+'&pagamento='+dtPagamento+"&boReemitida=true"+'&stIdCarregamento='+stIdCarregamento+'&info_parcela='+info_parcela;

        ajax (stPag, 'detalheParcela', stIdCarregamento );

    }else{
        alertaAviso('Data Infomada no Campo DataBase inválida;','form','erro','<?=Sessao::getId();?>');                 
        document.getElementById('dtDataBase').focus();
        // da de hoje
        var currentTime = new Date();
        var hoje =  currentTime.getDate()+'/'+(currentTime.getMonth()+1)+'/'+currentTime.getFullYear();
//        document.getElementById('dtDataBase').value = hoje; 
        document.getElementById('dtDataBase').value = ''; 
    }
}
function teste(e){
    var Executa;
    if (e.which == 13){
        Executa = document.getElementById('imgAtualizar').getAttribute('onclick');
        eval(Executa);
    }else{
        return e;
    }
}

function consultarParcela( inCodLancamento, inInscricao, inCodModulo, stOrigem, inNumCgm, stNomCgm, stProprietarios, inCodGrupo, stNumeracao, inExercicio, inCodParcela, dtPagamento, dtDataBase, dtVencimentoPR, stDados, inOcorrencia, stCompetencia, stTipoCalculo, stTipoVenal ){

    if ( inCodGrupo == 'cod_grupo' ) { inCodGrupo = ''; }

    var stURL = "<?=$pgForm;?>?<?=Sessao::getId();?>&inCodLancamento="+inCodLancamento+"&inInscricao="+inInscricao+"&inCodModulo="+inCodModulo+"&stOrigem="+stOrigem+"&inNumCgm="+inNumCgm+"&stNomCgm="+stNomCgm+"&stProprietarios="+stProprietarios+"&inCodGrupo="+inCodGrupo+"&stNumeracao="+stNumeracao+"&inExercicio="+inExercicio+"&inCodParcela="+inCodParcela+"&dtPagamento="+dtPagamento+"&dtDataBase="+dtDataBase+"&dtVencimentoPR="+dtVencimentoPR+"&stDados="+stDados+"&inOcorrencia="+inOcorrencia+"&stCompetencia="+stCompetencia+"&stTipoCalculo="+stTipoCalculo+"&stTipoVenal="+stTipoVenal;
    //stURL += "&timestamp=" + timestamp;

    //alert ( 'x ' + inCodGrupo + ' x' ); exit;

    window.open(stURL,'telaPrincipal');
    window.close();
}

</script>
