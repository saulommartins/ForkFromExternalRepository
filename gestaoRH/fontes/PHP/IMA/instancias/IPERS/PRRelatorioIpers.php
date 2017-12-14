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
    * Página de Processamento de Relatório Ipers
    * Data de Criação: 25/06/2008

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: <Alex Cardoso>

    * @ignore

    $Id: PRRelatorioIpers.php 66258 2016-08-03 14:25:21Z evandro $

    * Casos de uso: uc-04.08.28
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once (CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php"                 );

//Define o nome dos arquivos PHP
$stPrograma = "ExportarIpers";
$pgForm = "FM".$stPrograma.".php";
$pgFilt = "FL".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$preview = new PreviewBirt(4,40,13);
$preview->setVersaoBirt("2.5.0");
$preview->setTitulo(Sessao::read("stDescTipoEmissao")." de Conveniados IPE/RS");
$preview->setNomeArquivo('conveniadosIpers');
$preview->setReturnURL( CAM_GRH_IMA_INSTANCIAS."IPERS/".$pgFilt."?stAcao=exportar");
$preview->addParametro("entidade"   , Sessao::getCodEntidade($boTransacao));
$preview->addParametro("stEntidade" , Sessao::getEntidade());
$preview->addParametro("stExercicio", Sessao::getExercicio());
$preview->addParametro("inCodPeriodoMovimentacao", Sessao::read("inCodPeriodoMovimentacao"));
$preview->addParametro("inCodFolha"              , Sessao::read("inCodFolha"));
$preview->addParametro("inCodComplementar"       , Sessao::read("inCodComplementar"));
$preview->addParametro("stDesdobramento"         , Sessao::read("stDesdobramento"));
$preview->addParametro("stSituacaoCadastro"      , Sessao::read("stSituacaoCadastro"));
$preview->addParametro("stTipoFiltro"            , Sessao::read("stTipoFiltro"));
$preview->addParametro("stValoresFiltro"         , Sessao::read("stValoresFiltro"));
$preview->addParametro("inCodTipoEmissao"        , Sessao::read("inCodTipoEmissao"));
$preview->addParametro("stCompetenciaTitulo"     , Sessao::read("stCompetenciaTitulo"));
$preview->addParametro("stCodigoOrgao"           , Sessao::read("stCodigoOrgao"));
$preview->addParametro("inValorPerContPatronal"  , Sessao::read("inValorPerContPatronal"));
$preview->addParametro("stDescTipoEmissao"       , Sessao::read("stDescTipoEmissao"));
$preview->addParametro("boAgruparFolhas"         , Sessao::read("boAgruparFolhas"));
$preview->preview();

?>
