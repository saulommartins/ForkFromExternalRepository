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
    * Página de Processamento do Relação de Diárias
    * Data de Criação: 07/08/2008

    * @author Diego Lemos de Souza

    * Casos de uso: uc-04.09.03

    $Id: PRRelacaoDiarias.php 66258 2016-08-03 14:25:21Z evandro $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "RelacaoDiarias";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
$obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
$inMes = ($_POST["inCodMes"] < 10) ? "0".$_POST["inCodMes"] : $_POST["inCodMes"];
$dtCompetencia = $_POST["inAno"].$inMes;
$stFiltro = " WHERE to_char(dt_final,'yyyymm') = '".$dtCompetencia."'";
$obTFolhaPagamentoPeriodoMovimentacao->recuperaTodos($rsPeriodoMovimentacao,$stFiltro);

$stCodigos = "";
$stAgrupamento = "";
switch ($_POST["stTipoFiltro"]) {
    case "contrato":
    case "cgm_contrato":
        $arContratos = Sessao::read("arContratos");
        foreach ($arContratos as $arContrato) {
            $stCodigos .= $arContrato["cod_contrato"].",";
        }
        $stCodigos = substr($stCodigos,0,strlen($stCodigos)-1);
        break;
    case "lotacao_grupo":
        $stAgrupar = "Lotação: ";
        $stCodigos = implode(",",$_POST["inCodLotacaoSelecionados"]);
        break;
    case "local_grupo":
        $stAgrupar = "Local: ";
        $stCodigos = implode(",",$_POST["inCodLocalSelecionados"]);
        break;
    case "sub_divisao_funcao_grupo":
        $stAgrupar = "Regime / Subdivisão : ";
        $stCodigos = implode(",",$_POST["inCodSubDivisaoSelecionadosFunc"]);
        break;
    case "atributo_servidor_grupo":
        $stAgrupar = "Atributo : ";
        $inCodAtributo = $_POST["inCodAtributo"];
        $stNomeCampoAtributo = "Atributo_".$_POST["inCodAtributo"]."_".$_POST["inCodCadastro"];
        if (is_array($_POST[$stNomeCampoAtributo."_Selecionados"])) {
            $stCodigos = implode(",",$_POST[$stNomeCampoAtributo."_Selecionados"]);
            $boArray = "true";
        } else {
            $stCodigos = $_POST[$stNomeCampoAtributo];
            $boArray = "false";
        }
        break;
    case "cargo_grupo":
        $stAgrupar = "Cargo: ";
        $stCodigos = implode(",",$_POST["inCodCargoSelecionados"]);
        break;
    case "funcao_grupo":
        $stAgrupar = "Função: ";
        $stCodigos = implode(",",$_POST["inCodFuncaoSelecionados"]);
        break;
}

$preview = new PreviewBirt(4,50,1);
$preview->setVersaoBirt("2.5.0");
$preview->setReturnURL( CAM_GRH_DIA_INSTANCIAS."relatorios/FLRelacaoDiarias.php");
$preview->setTitulo('Relação de Diárias');
$preview->addParametro('stEntidade'         , Sessao::getEntidade());
$preview->addParametro('entidade'           , Sessao::getCodEntidade($boTransacao));
$preview->addParametro('stTipoFiltro'       , $_POST["stTipoFiltro"]);
$preview->addParametro('stCodigos'          , $stCodigos);
$preview->addParametro('inCodAtributo'      , $inCodAtributo);
$preview->addParametro('boArray'            , $boArray);
$preview->addParametro('boAgrupar'          , $_REQUEST["boAgrupar"]);
$preview->addParametro('boQuebrar'          , $_REQUEST["boQuebrar"]);
$preview->addParametro('stAgrupar'          , $stAgrupar);
$preview->addParametro('stDataInicial'      , $_REQUEST["stDataInicial"]);
$preview->addParametro('stDataFinal'        , $_REQUEST["stDataFinal"]);
$preview->addParametro('stDataInicialViagem', $_REQUEST["stDataInicialViagem"]);
$preview->addParametro('stDataFinalViagem'  , $_REQUEST["stDataFinalViagem"]);
$preview->addParametro('inCodTipoDiaria'    , $_REQUEST["inCodTipoDiaria"]);
$preview->preview();
?>
