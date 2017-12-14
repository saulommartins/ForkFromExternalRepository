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
    * Página de Processamento do Relatório de Cadastro de Estagiários
    * Data de Criação : 07/02/2007

    * @author Desenvolvedor: Alexandre Melo

    * Casos de uso: uc-04.07.02

    $Id: PRRelatorioCadastroEstagiario.php 66258 2016-08-03 14:25:21Z evandro $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php"                );

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioPagamentoEstagiarios";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

$stCodigos = "";
switch ($_POST["stTipoFiltro"]) {
    case "cgm_codigo_estagio":
        $stCodigos = "";
        foreach (Sessao::read("arEstagios") as $arEstagio) {
            $stCodigos .= $arEstagio["inCodigoEstagio"].",";
        }
        $stCodigos = substr($stCodigos,0,strlen($stCodigos)-1);
    break;
    case "instituicao_ensino":
    case "entidade_intermediadora":
        $stCodigos = $_POST["inCGM"];
        break;
    case "lotacao":
        $stCodigos = implode(",",$_POST["inCodLotacaoSelecionados"]);
        break;
    case "local":
        $stCodigos = implode(",",$_POST["inCodLocalSelecionados"]);
        break;
}

$obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
$inCodMes = $_POST["inCodMes"];
$inAno    = $_POST["inAno"];
$inMes = str_pad($inCodMes, 2, "0", STR_PAD_LEFT);
$stCompetencia = $inMes."/".$inAno;
$stFiltro = " AND to_char(FPM.dt_final,'mm/yyyy') = '".$stCompetencia."'";
$obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacao($rsPeriodoMovimentacao,$stFiltro);

$preview = new PreviewBirt(4,39,3);
$preview->setVersaoBirt("2.5.0");
$preview->setReturnURL( CAM_GRH_EST_INSTANCIAS."relatorios/FLRelatorioCadastroEstagiario.php");
$preview->setTitulo('Cadastro de Estagiários');
$preview->setNomeArquivo('cadastroEstagiarios');
$preview->addParametro('stEntidade', Sessao::getEntidade());
$preview->addParametro('entidade', Sessao::getCodEntidade($boTransacao));
$preview->addParametro('stTipoFiltro', $_POST["stTipoFiltro"]);
$preview->addParametro('stCodigos', $stCodigos);
$preview->addParametro('stContrato', $_POST['stContrato']);
$preview->addParametro('stDataInicial', $_POST['stDataInicial']);
$preview->addParametro('stDataFinal', $_POST['stDataFinal']);
$preview->addParametro('inCodPeriodoMovimentacao', $rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
$preview->preview();

?>
