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
    * Página de Processamento para Relatório de Cargos
    * Data de Criação   : 19/01/2009

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Alex Cardoso

    * @ignore

    $Id: $

    * Casos de uso: uc-04.04.11e
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioHistoricoFerias";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";


$preview = new PreviewBirt(4,22,12);
$preview->setVersaoBirt('2.5.0');
$preview->setTitulo('Relatório de Histórico de Férias');
$preview->setReturnURL( CAM_GRH_PES_INSTANCIAS."relatorio/FLRelatorioHistoricoFerias.php");
$preview->addParametro("stEntidade"          , trim(Sessao::getEntidade()));
$preview->addParametro("stExercicio"         , trim(Sessao::getExercicio()));
$preview->addParametro("stTipoFiltro"        , trim(Sessao::read("stTipoFiltro")));
$preview->addParametro("stValoresFiltro"     , trim(Sessao::read("stValoresFiltro")));
$preview->addParametro("dtDataLimite"        , trim(Sessao::read("dtDataLimite")));
$preview->addParametro("stOrdenacaoLotacao"  , trim(Sessao::read("stOrdenacaoLotacao")));
$preview->addParametro("stOrdenacaoRegime"   , trim(Sessao::read("stOrdenacaoRegime")));
$preview->addParametro("stOrdenacaoContrato" , trim(Sessao::read("stOrdenacaoContrato")));
$preview->preview();
