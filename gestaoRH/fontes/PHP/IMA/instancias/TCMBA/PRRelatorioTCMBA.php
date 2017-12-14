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
    * Página de Processamento do Exportação TCMBA
    * Data de Criação: 29/08/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30920 $
    $Name$
    $Author: alex $
    $Date: 2008-02-12 09:02:54 -0200 (Ter, 12 Fev 2008) $

    * Casos de uso: uc-04.08.04
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once (CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php"                 );

//Define o nome dos arquivos PHP
$stPrograma = "ExportarTCMBA";
$pgForm = "FM".$stPrograma.".php";
$pgFilt = "FL".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$preview = new PreviewBirt(4,40,2);
$preview->setVersaoBirt("2.5.0");
$preview->setTitulo('Resumo dos Totais Arquivo TCM/BA ');
$preview->setNomeArquivo('resumoTotaisArquivoTCMBA');
$preview->setReturnURL( CAM_GRH_IMA_INSTANCIAS."TCMBA/".$pgFilt);
$preview->addParametro("entidade", Sessao::getCodEntidade($boTransacao));
$preview->addParametro("competencia",Sessao::read("stCompetencia"));
$preview->addParametro("competencia13",Sessao::read("stCompetencia13"));

$preview->addParametro("tipo_envio",Sessao::read("inTipoEnvio"));
$preview->addParametro("total_servidores",Sessao::read("inTotalServidores"));

$preview->addParametro("cargo_efetivo",Sessao::read("inTotalEfetivo"));
$preview->addParametro("cargo_efetivo_admissao",Sessao::read("inTotalAdmissaoEfetivo"));
$preview->addParametro("cargo_efetivo_exclusao",Sessao::read("inTotalDemissaoEfetivo"));

$preview->addParametro("celetista",Sessao::read("inTotalCeletista"));
$preview->addParametro("celetista_admissao",Sessao::read("inTotalAdmissaoCeletista"));
$preview->addParametro("celetista_exclusao",Sessao::read("inTotalDemissaoCeletista"));

$preview->addParametro("cargo_comissao",Sessao::read("inTotalComissao"));
$preview->addParametro("cargo_comissao_admissao",Sessao::read("inTotalAdmissaoCargoComissao"));
$preview->addParametro("cargo_comissao_exclusao",Sessao::read("inTotalDemissaoCargoComissao"));

$preview->addParametro("trabalhador_temporario",Sessao::read("inTotalTemporario"));
$preview->addParametro("trabalhador_temporario_admissao",Sessao::read("inTotalAdmissaoTemporario"));
$preview->addParametro("trabalhador_temporario_exclusao",Sessao::read("inTotalDemissaoTemporario"));

$preview->addParametro("agente_politico",Sessao::read("inTotalPolitico"));
$preview->addParametro("agente_politico_admissao",Sessao::read("inTotalAdmissaoPolitico"));
$preview->addParametro("agente_politico_exclusao",Sessao::read("inTotalDemissaoPolitico"));

$preview->addParametro("aposentado",Sessao::read("inTotalAposentado"));
$preview->preview();

?>
