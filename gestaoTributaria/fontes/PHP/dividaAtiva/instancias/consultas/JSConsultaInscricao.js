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
    * Arquivo com funcoes JavaScript para Consulta de Divida Ativa
    * Data de Criação: 23/02/2007


    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @ignore

    * $Id: JSConsultaInscricao.js 66056 2016-07-13 13:38:50Z evandro $


*/

?>

<script type="text/javascript">

function Limpar(){
    document.frm.reset();
    if ( document.frm.inCodInscricao )
        document.frm.inCodInscricao.focus();
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
        document.getElementById('dtDataBase').value = ''; 
    }
}
function visualizarDetalhesAtualizaReemitida( cod_lancamento, numeracao, exercicio,cod_parcela,dtPagamento, data, dtVencimento, ocorrencia, stIdCarregamento, info_parcela, num_parcelamento ){

    stCtrl = 'parcela';

    if ( retornaValidaData(data) ) {
        stPag = '<?=$pgOcul;?>?<?=Sessao::getId();?>&cod_lancamento='+cod_lancamento+'&numeracao='+numeracao+'&inExercicio='+exercicio+'&cod_parcela='+cod_parcela+'&database_br='+dtPagamento+'&pagamento='+dtPagamento+"&boReemitida=true"+'&vencimento='+dtVencimento+'&ocorrencia_pagamento='+ocorrencia+'&stIdCarregamento='+stIdCarregamento+'&info_parcela='+info_parcela+'&num_parcelamento='+num_parcelamento;

        ajax (stPag, 'detalheParcela', stIdCarregamento );

    }else{
        alertaAviso('Data Infomada no Campo DataBase inválida;','form','erro','<?=Sessao::getId();?>');                 
        document.getElementById('dtDataBase').focus();
        // da de hoje
        var currentTime = new Date();
        var hoje =  currentTime.getDate()+'/'+(currentTime.getMonth()+1)+'/'+currentTime.getFullYear();

        document.getElementById('dtDataBase').value = ''; 
    }
}
function visualizarDetalhesAtualizaReemitidaCombo( stCtrl, cod_lancamento, descricao, exercicio,cod_parcela, dtPagamento,data, stIdCarregamento, info_parcela,num_parcelamento ){


    if ( retornaValidaData(data) ) {
        stPag = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inCodLancamento='+cod_lancamento+'&stDescricao='+descricao+'&inExercicio='+exercicio+'&cod_parcela='+cod_parcela+'&dtDataBase='+data+"&stTipoData=br"+'&pagamento='+dtPagamento+"&boReemitida=true"+'&stIdCarregamento='+stIdCarregamento+'&info_parcela='+info_parcela+'&num_parcelamento='+num_parcelamento;

        ajax (stPag, 'detalheParcela', stIdCarregamento );

    }else{
        alertaAviso('Data Infomada no Campo DataBase inválida;','form','erro','<?=Sessao::getId();?>');                 
        document.getElementById('dtDataBase').focus();
        // da de hoje
        var currentTime = new Date();
        var hoje =  currentTime.getDate()+'/'+(currentTime.getMonth()+1)+'/'+currentTime.getFullYear();

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

function consultarParcela( inCodLancamento, inInscricao, inCodModulo, stOrigem, inNumCgm, stNomCgm, stProprietarios, inCodGrupo, stNumeracao, inExercicio, inCodParcela, dtPagamento, dtDataBase, dtVencimentoPR, stDados, inOcorrencia ){

    if ( inCodGrupo == 'cod_grupo' ) { inCodGrupo = ''; }

    var stURL = "<?=$pgForm;?>?<?=Sessao::getId();?>&inCodLancamento="+inCodLancamento+"&inInscricao="+inInscricao+"&inCodModulo="+inCodModulo+"&stOrigem="+stOrigem+"&inNumCgm="+inNumCgm+"&stNomCgm="+stNomCgm+"&stProprietarios="+stProprietarios+"&inCodGrupo="+inCodGrupo+"&stNumeracao="+stNumeracao+"&inExercicio="+inExercicio+"&inCodParcela="+inCodParcela+"&dtPagamento="+dtPagamento+"&dtDataBase="+dtDataBase+"&dtVencimentoPR="+dtVencimentoPR+"&stDados="+stDados+"&inOcorrencia="+inOcorrencia;

    window.open(stURL,'telaPrincipal');
    window.close();
}

function atualizaLancamentos(data){
    stPag = '<?=$pgOcul;?>?<?=Sessao::getId();?>&dtDataBase='+data.value+'&inCodInscricao=<?=$_REQUEST["inCodInscricao"];?>&inInscMunic=<?=$_REQUEST['inInscMunic'];?>&inExercicio=<?=$_REQUEST['inExercicio'];?>';
    ajax(stPag,'atualizaLancamentos','spnLancamentos');
}

function Voltar(){
    document.frm.target = 'telaPrincipal';
    window.location = '<?=$pgList;?>';
}

function imprimirDocumentos(){

   var itens = imprimirDocumentos.arguments;
   var x     = new Number(0);
   var link  = new String();
   var alvo  = new String();
   var acao  = new String();

   var cmp   = new Array( 'inCodDocumento', 'inCodTipoDocumento','stNomeArquivoAGT','stNomeArquivo','stNomeDocumento','inNumDocumento','inNumParcelamento','inExercicio' );

   while(x<itens.length){
       link+= "&"+cmp[x]+"="+itens[x];
       x++;
   }

   alvo = document.frm.target;
   acao = document.frm.action;
   document.frm.stCtrl.value = 'impressaoDocumento';
   document.frm.target = 'oculto';
   document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>'+link;
   document.frm.submit();
   document.frm.target = alvo;
   document.frm.action = acao;
}

function submeteFiltro(){
        if ( document.frm.inNrParcelamento.value || document.frm.inCGM.value || document.frm.inCodInscricaoInicial.value || document.frm.inCodInscricaoFinal.value || document.frm.inLivroFolhaInicial.value || document.frm.inLivroFolhaFinal.value  || document.frm.inCodImovelInicial.value || document.frm.inCodImovelFinal.value || document.frm.inNumInscricaoEconomicaInicial.value || document.frm.inNumInscricaoEconomicaFinal.value ) {
            Salvar();
        }else {
            alertaAviso("@Selecionar ao menos um filtro para esta consulta.",'form','erro','<?=Sessao::getId();?>', '../');
        }
    
}

</script>
