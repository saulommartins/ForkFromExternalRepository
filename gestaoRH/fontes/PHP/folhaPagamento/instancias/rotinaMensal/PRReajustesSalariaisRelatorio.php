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
    * Processamento
    * Data de Criação: 28/09/2007

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30849 $
    $Name$
    $Author: alex $
    $Date: 2008-02-12 09:06:47 -0200 (Ter, 12 Fev 2008) $

    * Casos de uso: uc-04.05.26
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = 'ReajustesSalariais';
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
$obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
$obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);

if (trim($stAcao)=="incluir") {
    $preview = new PreviewBirt(4,27,9);
    $preview->setNomeArquivo('reajustesSalariais');
} else {
    list($inCodReajusteExclusao, $stOrigemExclusao) = explode("*_*", Sessao::read("inCodReajuste"));

    $preview = new PreviewBirt(4,27,21);
    $preview->setNomeArquivo('reajusteSalariaisExclusao');
    $preview->addParametro("inCodReajuste",$inCodReajusteExclusao);
}
$preview->setVersaoBirt("2.5.0");
$preview->setReturnURL( CAM_GRH_FOL_INSTANCIAS."rotinaMensal/FMReajustesSalariais.php?stAcao=".$stAcao);

if (is_array(Sessao::read("arRegistros"))) {
    $stContratos = "";
    foreach (Sessao::read("arRegistros") as $arRegistro) {
        $stContratos .= $arRegistro["cod_contrato"].",";
    }
    $stContratos = substr($stContratos,0,strlen($stContratos)-1);
}

if (trim(Sessao::read("inCodigoEvento")) != "") {
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php");
    $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
    $stFiltroEvento = " WHERE codigo = '".trim(Sessao::read("inCodigoEvento"))."'";
    $obTFolhaPagamentoEvento->recuperaTodos($rsEvento,$stFiltroEvento);
}

$preview->addParametro("entidade",Sessao::getCodEntidade($boTransacao));
$preview->addParametro("stEntidade",Sessao::getEntidade());
$preview->addParametro("stObs",Sessao::read("stObservacao"));
$preview->addParametro("stPercentual",str_replace(",",".",str_replace(".","",Sessao::read("nuPercentualReajuste")*1)));
$preview->addParametro("stValor",str_replace(",",".",str_replace(".","",Sessao::read("nuValorReajuste")*1)));
$preview->addParametro("stTipoReajuste",Sessao::read("stTipoReajuste"));
$preview->addParametro("stFaixas",Sessao::read("nuFaixaInicial")." ate ".Sessao::read("nuFaixaFinal"));
$preview->addParametro("stVigencia",Sessao::read("dtVigencia"));
$preview->addParametro("competencia",$dtCompetencia);
$preview->addParametro("stTipoFiltro","contrato");
$preview->addParametro("stValoresFiltro",$stContratos);
if (Sessao::read("stCadastro") == "o") {
    $preview->addParametro("stSituacao","P");
}
if (Sessao::read("stCadastro") == "a") {
    $preview->addParametro("stSituacao","A");
}
if (Sessao::read("stCadastro") == "p") {
    $preview->addParametro("stSituacao","E");
}
$preview->addParametro("inCodPeriodoMovimentacao",$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
if (trim(Sessao::read("inCodigoEvento")) != "") {
    $preview->addParametro("inCodEvento",$rsEvento->getCampo("cod_evento"));
}
$preview->addParametro("stReajuste",Sessao::read("stReajuste"));
$preview->addParametro("inCodConfiguracao",Sessao::read("inCodConfiguracao"));
$preview->addParametro("desdobramento",$stFiltroAdicional);
$preview->preview();
?>
