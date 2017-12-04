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
    * Página de Filtro do Relatório Bancário de Pensão Judicial
    * Data de Criação : 21/03/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: André Machado

    * @ignore

    $Revision: 30849 $
    $Name$
    $Autor: $
    $Date: 2007-10-08 09:46:10 -0300 (Seg, 08 Out 2007) $

    * Casos de uso: uc-04.05.09
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ConsultaFichaFinanceira";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
//$obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
//$obTFolhaPagamentoPeriodoMovimentacao->setDado("mes",$_POST["inCodMes"]);
//$obTFolhaPagamentoPeriodoMovimentacao->setDado("ano",$_POST["inAno"]);
//$obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacaoDaCompetencia($rsPeriodoMovimentacao);
$preview = new PreviewBirt(4,27,2);
//$preview->setTitulo('Consulta Ficha Financeira');
//$preview->setNomeArquivo('bancariopensaojudicial');
$preview->addParametro("stEntidade", Sessao::getEntidade());
$preview->addParametro("entidade", Sessao::getCodEntidade($boTransacao));
//$preview->addParametro("cod_periodo_movimentacao",$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
//$preview->addParametro("competencia", $rsPeriodoMovimentacao->getCampo("dt_final"));
//$preview->addParametro("agrupar", $stAgrupar);
//$preview->addParametro("quebrar", $stQuebrar);
//$preview->addParametro("filtro", $_POST["stTipoFiltro"]);
//$preview->addParametro("codigos", $stCodigos);
//$preview->addParametro("bancos", $stBancos);
//$preview->addParametro("agencias", $stAgencias);
//$preview->addParametro("cod_atributo",$_POST["inCodAtributo"]);
//$preview->addParametro("valor", $stValor);
//$preview->addParametro("cod_tipo_atributo", $inCodTipoAtributo);
$preview->preview();
?>
