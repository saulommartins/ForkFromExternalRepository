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
    * Arquivo com funcoes JavaScript para Consulta de Imóveis
    * Data de Criação: 09/06/2005


    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Marcelo Boezzio Paulino

    * @ignore

    * $Id: JSConsultaImovel.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    * Casos de uso: uc-05.01.18
*/

/*
$Log$
Revision 1.4  2007/03/01 18:27:29  cercato
Bug #6460#

Revision 1.3  2006/09/18 10:30:20  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
?>

<script type="text/javascript">

function Limpar(){
    document.getElementById('innerCondominio').innerHTML = "&nbsp;";
    document.getElementById('campoInnerLogr').innerHTML = "&nbsp;";
    document.getElementById('innerBairro').innerHTML = "&nbsp;";
    document.getElementById('innerCGM').innerHTML = "&nbsp;";
    document.getElementById('innerCreci').innerHTML = "&nbsp;";
    document.frm.reset();
    preencheCombos();
}

//FUNCOES PARA PREENCHIMENTO DA LOCALIZACAO
function preencheProxCombo( inPosicao  ){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'preencheProxCombo';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inPosicao='+inPosicao;
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTraget;
}

function preencheCombos(){
    BloqueiaFrames(true,false);
    document.frm.stCtrl.value = 'preencheCombos';
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTraget;
}

function buscaValor(tipoBusca){
    document.frm.stCtrl.value = tipoBusca;
    document.frm.target = 'oculto';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = 'telaPrincipal';
    document.frm.action = '<?=$pgList;?>?<?=Sessao::getId();?>';
}

function visualizarProcessoLote(tipo_processo, processo, timestamp, cod_lote, ano_exercicio){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = 'oculto';
    document.frm.stCtrl.value = tipo_processo;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&cod_processo='+processo+'&timestamp='+timestamp+'&cod_lote='+cod_lote+'&ano_exercicio='+ano_exercicio;
    document.frm.submit();
    document.frm.target = 'telaPrincipal';
    document.frm.action = '<?=$pgList;?>?<?=Sessao::getId();?>';
}

function visualizarProcessoImovel(tipo_processo, processo, timestamp, inscricao_municipal, ano_exercicio){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = 'oculto';
    document.frm.stCtrl.value = tipo_processo;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&cod_processo='+processo+'&timestamp='+timestamp+'&inscricao_municipal='+inscricao_municipal+'&ano_exercicio='+ano_exercicio;
    document.frm.submit();
    document.frm.target = 'telaPrincipal';
    document.frm.action = '<?=$pgList;?>?<?=Sessao::getId();?>';
}

function visualizarProcessoCondominio(tipo_processo, processo, timestamp, cod_condominio, ano_exercicio, area){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = 'oculto';
    document.frm.stCtrl.value = tipo_processo;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&cod_processo='+processo+'&timestamp='+timestamp+'&cod_condominio='+cod_condominio+'&ano_exercicio='+ano_exercicio+'&area='+area;
    document.frm.submit();
    document.frm.target = 'telaPrincipal';
    document.frm.action = '<?=$pgList;?>?<?=Sessao::getId();?>';
}

function visualizarProcessoConstrucao(tipo_processo, processo, timestamp, cod_construcao, cod_processo_ano, area){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = 'oculto';
    document.frm.stCtrl.value = tipo_processo;
    document.frm.action = '<?=$pgOculCons;?>?<?=Sessao::getId();?>&processo='+processo+'&timestamp='+timestamp+'&cod_construcao='+cod_construcao+'&cod_processo_ano='+cod_processo_ano+'&area='+area;
    document.frm.submit();
    document.frm.target = 'telaPrincipal';
    document.frm.action = '<?=$pgList;?>?<?=Sessao::getId();?>';
}


function visualizarConstrucao(tipo_construcao, cod_construcao, inscricao_municipal, tipo_vinculo){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = 'oculto';
    document.frm.stCtrl.value = tipo_construcao;
    document.frm.action = '<?=$pgOculCons;?>?<?=Sessao::getId();?>&cod_construcao='+cod_construcao+'&inscricao_municipal='+inscricao_municipal+'&tipo_vinculo='+tipo_vinculo;
    document.frm.submit();
    document.frm.target = 'telaPrincipal';
    document.frm.action = '<?=$pgList;?>?<?=Sessao::getId();?>';
}

function visualizarTransferencia(cod_transferencia,cod_processo,exercicio_proc,creci,nomcgm,dt_efetivacao,observacao){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = 'oculto';
    document.frm.stCtrl.value = 'visualizarTransferencia';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&cod_transferencia='+cod_transferencia+'&cod_processo='+cod_processo+'&exercicio_proc='+exercicio_proc+'&creci='+creci+'&nomcgm='+nomcgm+'&dt_efetivacao='+dt_efetivacao+'&stObservacao='+observacao;
    document.frm.submit();
    document.frm.target = 'telaPrincipal';
    document.frm.action = '<?=$pgList;?>?<?=Sessao::getId();?>';
}

function relatorio(){
    var link = new String();

    link ="<?=CAM_FW_POPUPS?>relatorio/OCRelatorio.php?<?=Sessao::getId()?>";
    link+="&inCodInicioInscricao="+relatorio.arguments[0];
    link+="&inCodTerminoInscricao="+relatorio.arguments[0];
    link+="&stCaminho="+document.forms[0].stCaminho.value;
    link+="&stTipoRelatorio=analitico";
    link+="&stAcao=Incluir";
    link+="&inNumCopias=1";
    link+="&stTipoRelatorioSubmit=analitico";
    link+="&stOrder=inscricao";
    link+="&stImoEd=2";

    window.parent.frames["oculto"].document.location.href=link;
}

function buscaDado(tipoBusca){
    document.frm.stCtrl.value = tipoBusca;
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTraget;
}

</script>
