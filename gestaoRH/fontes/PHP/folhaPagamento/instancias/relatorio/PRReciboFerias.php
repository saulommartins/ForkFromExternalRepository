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
    * Página de Processamento do Relatório Recibo Férias
    * Data de Criação: 24/01/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30840 $
    $Name$
    $Author: rgarbin $
    $Date: 2008-03-26 15:10:17 -0300 (Qua, 26 Mar 2008) $

    * Casos de uso: uc-04.05.56
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once (CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php"                 );

//Define o nome dos arquivos PHP
$stPrograma = "ReciboFerias";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$inMes = ( $_POST["inCodMes"] < 10 ) ? "0".$_POST["inCodMes"] : $_POST["inCodMes"];
$dtCompetencia = $inMes."/".$_POST["inAno"];
$stValorPeriodo = " AND to_char(dt_final,'mm/yyyy') = '".$dtCompetencia."'";
$obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
$obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacao($rsPeriodoMovimentacao,$stValorPeriodo);

switch ($_POST["stTipoFiltro"]) {
    case "lotacao":
        foreach ($_POST["inCodLotacaoSelecionados"] as $inCodOrgao) {
            $stValor .= $inCodOrgao.",";
        }
        $stValor = substr($stValor,0,strlen($stValor)-1);
        break;
    case "local":
        foreach ($_POST["inCodLocalSelecionados"] as $inCodLocal) {
            $stValor .= $inCodLocal.",";
        }
        $stValor = substr($stValor,0,strlen($stValor)-1);
        break;
    case "contrato":
    case "cgm_contrato":
        foreach (Sessao::read("arContratos") as $arContrato) {
            $stValor .= $arContrato["cod_contrato"].",";
        }
        $stValor = substr($stValor,0,strlen($stValor)-1);
        break;
}

$preview = new PreviewBirt(4,27,22);
$preview->setVersaoBirt ( "2.5.0" );
$preview->setReturnURL  ( CAM_GRH_FOL_INSTANCIAS."relatorio/".$pgFilt);
$preview->setTitulo     ( "Recibo de Férias" );
$preview->setNomeArquivo( "reciboDeFerias" );
$preview->addParametro("entidade"                , Sessao::getCodEntidade($boTransacao));
$preview->addParametro("stEntidade"              , Sessao::getEntidade());
$preview->addParametro("dtFinalCompetencia"      , $rsPeriodoMovimentacao->getCampo("dt_final"));
$preview->addParametro("inCodPeriodoMovimentacao", $rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
$preview->addParametro("stAnoCompetencia"        , $_POST["inAno"]);
$preview->addParametro("stMesCompetencia"        , sprintf("%02d", $_POST["inCodMes"]));
$preview->addParametro("boOrdenacaoLotacao"      , (($_POST["boOrdenacaoLotacao"]) ? "true" : "false"));
$preview->addParametro("boOrdenacaoLocal"        , (($_POST["boOrdenacaoLocal"]) ? "true" : "false"));
$preview->addParametro("boOrdenacaoCGM"          , (($_POST["boOrdenacaoCGM"]) ? "true" : "false"));
$preview->addParametro("stOrdenacaoLotacao"      , $_POST["stOrdenacaoLotacao"]);
$preview->addParametro("stOrdenacaoLocal"        , $_POST["stOrdenacaoLocal"]);
$preview->addParametro("stOrdenacaoCGM"          , $_POST["stOrdenacaoCGM"]);
$preview->addParametro("stTipoFiltro"            , $_POST["stTipoFiltro"]);
$preview->addParametro("stValor"                 , $stValor);
$preview->preview();
?>
