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
* Página de Processamento
* Data de Criacão   : 05/12/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @ignore

$Revision: 30840 $
$Name$
$Author: souzadl $
$Date: 2007-11-20 13:28:18 -0200 (Ter, 20 Nov 2007) $

* Casos de uso: uc-04.05.09
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

$stPrograma = "ManterCalculoSalario";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

//Ticket #13869
if ($_REQUEST["stAcao"] == "imprimirErro") {
    $preview = new PreviewBirt(4,27,12);
    $preview->setVersaoBirt("2.5.0");
    $preview->addParametro("stEntidade", Sessao::getEntidade());
    $preview->addParametro("entidade", Sessao::getCodEntidade($boTransacao));
    $preview->addParametro("inCodConfiguracao", "1");
    $preview->preview();
} else {

    $arMeses["01"] = "Janeiro";
    $arMeses["02"] = "Fevereiro";
    $arMeses["03"] = "Março";
    $arMeses["04"] = "Abril";
    $arMeses["05"] = "Maio";
    $arMeses["06"] = "Junho";
    $arMeses["07"] = "Julho";
    $arMeses["08"] = "Agosto";
    $arMeses["09"] = "Setembro";
    $arMeses["10"] = "Outubro";
    $arMeses["11"] = "Novembro";
    $arMeses["12"] = "Dezembro";

    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao;
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodo);
    $arDtFinal = explode("/",$rsPeriodo->getCampo("dt_final"));
    $stCompetencia = $arMeses[$arDtFinal[1]]."/".$arDtFinal[2];

    $preview = new PreviewBirt(4,27,10);
    $preview->setVersaoBirt("2.5.0");
    $preview->addParametro("stEntidade", Sessao::getEntidade());
    $preview->addParametro("entidade", Sessao::getCodEntidade($boTransacao));
    $preview->addParametro("stTipoFiltro","contrato");
    $preview->addParametro("stCodigos",Sessao::read("cod_contrato"));
    $preview->addParametro("inCodPeriodoMovimentacao",$rsPeriodo->getCampo("cod_periodo_movimentacao"));
    $preview->addParametro("stCompetencia",$stCompetencia);
    $preview->addParametro("inCodConfiguracao","1");
    $preview->preview();
}

?>
