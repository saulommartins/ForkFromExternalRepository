<?php
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
?>
<?php
/**
    * Página de processamento oculto e geração do relatório para CONSULTA DA DIVIDA
    * Data de Criação   : 08/08/2007

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho

    * @ignore

    * $Id: OCRelatorioConsultaDivida.php 66052 2016-07-12 20:16:26Z evandro $

    Caso de uso: uc-05.04.09
*/

/*
$Log$
Revision 1.1  2007/08/09 20:25:05  dibueno
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"                                                         );
include_once( CAM_FW_PDF."ListaPDF.class.php"                                                           );
include_once( CAM_GT_DAT_MAPEAMENTO."TDATDividaAtiva.class.php"                                         );

$boTransacao = false;
$arrInformacoes = array();

/* RECEBE AS VARIAVEIS DE REQUEST */
$arrInformacoes['inCodInscricao']   = $request->get('inCodInscricao');
$arrInformacoes['inExercicio']      = $request->get('inExercicio');
$arrInformacoes['dtDataBase_br']    = $request->get('dtDataBase');
$stDataBase = $request->get('dtDataBase');
$arDataBase = explode ( '/', $stDataBase );
$arrInformacoes['dtDataBase']       = $arDataBase[2].'-'.$arDataBase[1].'-'.$arDataBase[0];
$arrInformacoes['inNumCGMContrib']  = $request->get('inNumCGMContrib');
$arrInformacoes['inNomCGMContrib']  = $request->get('inNomCGMContrib');
$arrInformacoes['inNumCGMAutorid']  = $request->get('inNumCGMAutorid');
$arrInformacoes['inNomCGMAutorid']  = $request->get('inNomCGMAutorid');
$arrInformacoes['inInscMunic']      = $request->get('inInscMunic');
$arrInformacoes['inInscEcon']       = $request->get('inInscEcon');
$arrInformacoes['stSituacao']       = $request->get('stSituacao');
$stDataCancelada = $request->get('dtCancelada');
$arrInformacoes['dtCancelada']      = substr($stDataCancelada,8,2).'/'.substr($stDataCancelada,5,2).'/'.substr($stDataCancelada,0, 4);
$arrInformacoes['stNomCgmCancelada'] = $request->get('stNomCgmCancelada');
$arrInformacoes['inNumCgmCancelada'] = $request->get('inNumCgmCancelada');

$obTDATDividaAtiva = new TDATDividaAtiva;
$dtDataBase = $request->get('stDataInscDiv');
$obTDATDividaAtiva->setDado('data_base', $arrInformacoes['dtDataBase_br'] );

$stFiltroLancamentos = "WHERE inscricao.cod_inscricao = ".$arrInformacoes["inCodInscricao"];
$stFiltroLancamentos .=" AND inscricao.exercicio = '".$arrInformacoes["inExercicio"]."'";
$obTDATDividaAtiva->listaConsultaLancamentosSimples( $rsListaLancamentos , $stFiltroLancamentos );

$stFiltro = "";
if ($arrInformacoes["inCodInscricao"] && $arrInformacoes["inExercicio"]) {
    $stFiltro = " WHERE ddp.cod_inscricao = ".$arrInformacoes["inCodInscricao"]." AND ddp.exercicio = '".$arrInformacoes["inExercicio"]."'";
}
$obTDATDividaAtiva->ListaConsultaCobrancas( $rsListaCobrancas, $stFiltro, $boTransacao );

$rsListaParcelas = new Recordset;
if($rsListaCobrancas->getNumlinhas() > 0) {
    $stFiltro = " AND dp.num_parcelamento = ".$rsListaCobrancas->getCampo("num_parcelamento");
    $stFiltro .=" ORDER BY num_parcela ASC ";
    $obTDATDividaAtiva->ListaConsultaParcelas( $rsListaParcelas, $stFiltro );
}

$stFiltro = "";
$obTDATDividaAtiva->setDado( 'data_base', $arrInformacoes['dtDataBase_br'] );
$stFiltro = " AND inscricao.num_parcelamento = ".$rsListaCobrancas->getCampo ("num_parcelamento");
$obTDATDividaAtiva->listaConsultaInscricoesSimples( $rsListaInscricoesVinculadas, $stFiltro );

Sessao::write('arrInformacoes', $arrInformacoes);
Sessao::write('rsListaLancamentos', $rsListaLancamentos);
Sessao::write('rsListaCobrancas', $rsListaCobrancas);
Sessao::write('rsListaParcelas', $rsListaParcelas);
Sessao::write('rsListaInscricoesVinculadas', $rsListaInscricoesVinculadas);

$obRRelatorio = new RRelatorio;
$obRRelatorio->executaFrameOculto( "OCGeraRelatorioConsultaDivida.php" );

?>
