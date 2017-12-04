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
    * Arquivo de Relatório para exportação do CAGED
    * Data de Criação: 18/04/2008

    * @author Diego Lemos de Souza

    * Casos de uso: uc-04.08.21

    $Id: PRRelatorioCAGED.php 30829 2008-07-07 19:59:54Z alex $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ExportarCAGED";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$preview = new PreviewBirt(4,40,3);
$preview->setVersaoBirt("2.5.0");
$preview->setTitulo('Totais Arquivo CAGED');
$preview->setNomeArquivo('totalArquivoCaged');
$preview->setReturnURL( CAM_GRH_IMA_INSTANCIAS."caged/".$pgFilt);
$preview->addParametro('entidade', Sessao::getCodEntidade($boTransacao));

$preview->addParametro('inContratoPrazoDeterminado',Sessao::read("inContratoPrazoDeterminado"));
$preview->addParametro('inPrimeiroEmprego',Sessao::read("inPrimeiroEmprego"));
$preview->addParametro('inReemprego',Sessao::read("inReemprego"));
$preview->addParametro('inReintegracao',Sessao::read("inReintegracao"));
$preview->addParametro('inTranferenciaEntrada',Sessao::read("inTranferenciaEntrada"));
$preview->addParametro('inAposentado',Sessao::read("inAposentado"));
$preview->addParametro('inDispensaPedido',Sessao::read("inDispensaPedido"));
$preview->addParametro('inDispensaJustaCausa',Sessao::read("inDispensaJustaCausa"));
$preview->addParametro('inDispensaSemJustaCausa',Sessao::read("inDispensaSemJustaCausa"));
$preview->addParametro('inFimContrato',Sessao::read("inFimContrato"));
$preview->addParametro('inMorte',Sessao::read("inMorte"));
$preview->addParametro('inTerminoContrato',Sessao::read("inTerminoContrato"));
$preview->addParametro('inTranferenciaSaida',Sessao::read("inTranferenciaSaida"));
$preview->addParametro('inTotalDesligamentosMes',Sessao::read("inTotalDesligamentosMes"));
$preview->addParametro('inTotalMovimentacoes',Sessao::read("inTotalMovimentacoes"));
$preview->addParametro('stCompetencia',Sessao::read("stCompetencia"));
$preview->addParametro('stTipoArquivo',Sessao::read("stTipoArquivo"));
$preview->addParametro('inTotalPrimeiroDia',Sessao::read("inTotalPrimeiroDia"));

$preview->preview();
?>
