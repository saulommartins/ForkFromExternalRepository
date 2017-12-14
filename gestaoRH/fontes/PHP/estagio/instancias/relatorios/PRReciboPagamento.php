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
    * Página de Processamento do Recibo de Pagamento
    * Data de Criação: 26/10/2006

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-04.07.04

    $Id: PRReciboPagamento.php 66258 2016-08-03 14:25:21Z evandro $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php"                );

//Define o nome dos arquivos PHP
$stPrograma = "ReciboPagamento";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

$stOrdem = ($_POST["stOrdenacao"] == "alfabetica") ? " nom_cgm" : " numero_estagio";
$inCodAtributo = 0;
$boArray = 0;
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
    case "atributo_estagiario":
        $inCodAtributo = $_POST["inCodAtributo"];
        $inCodCadastro = $_POST["inCodCadastro"];
        $stNome = "Atributo_".$inCodAtributo."_".$inCodCadastro;
        if (is_array($_POST[$stNome."_Selecionados"])) {
            $stCodigos = implode(",",$_POST[$stNome."_Selecionados"]);
            $boArray = 1;
        } else {
            $stCodigos = $_POST[$stNome];
        }
        break;
    case "lotacao_grupo":
        $stCodigos = implode(",",$_POST["inCodLotacaoSelecionados"]);
        if ($_POST["boAgrupar"]) {
            $stOrdem = " cod_orgao";
        }
    break;
    case "local_grupo":
        $stCodigos = implode(",",$_POST["inCodLocalSelecionados"]);
        if ($_POST["boAgrupar"]) {
            $stOrdem = " cod_local";
        }
        break;
}
$obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
$inCodMes = $_POST["inCodMes"];
$inAno    = $_POST["inAno"];
$inMes = str_pad($inCodMes, 2, "0", STR_PAD_LEFT);
$stCompetencia = $inMes."/".$inAno;
$stFiltro = " AND to_char(FPM.dt_final,'mm/yyyy') = '".$stCompetencia."'";
$obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacao($rsPeriodoMovimentacao,$stFiltro);
###
###
$preview = new PreviewBirt(4,39,1);
$preview->setVersaoBirt("2.5.0");
$preview->setReturnURL( CAM_GRH_EST_INSTANCIAS."relatorios/FLReciboPagamento.php");
$preview->setTitulo('Recibo de Pagamento');
$preview->setNomeArquivo('reciboPagamento');
$preview->addParametro('stEntidade', Sessao::getEntidade());
$preview->addParametro('entidade', Sessao::getCodEntidade($boTransacao));
$preview->addParametro('stTipoFiltro', $_POST["stTipoFiltro"]);
$preview->addParametro('stCodigos', $stCodigos);
$preview->addParametro('inCodAtributo', $inCodAtributo);
$preview->addParametro('boArray', $boArray);
$preview->addParametro('boCopiaRecibo', ($_POST["boDuplicar"]) ? 1 : 0);
$preview->addParametro('inCodPeriodoMovimentacao', $rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
$preview->addParametro('stOrdem', $stOrdem);
$preview->preview();
?>
