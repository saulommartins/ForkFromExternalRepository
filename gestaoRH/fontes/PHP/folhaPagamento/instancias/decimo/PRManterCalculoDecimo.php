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
    * Pï¿½gina de Processamento do Calculo de 13ï¿½ Salï¿½rio
    * Data de Criaï¿½ï¿½o: 09/09/2006

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-04.05.11

    $Id: PRManterCalculoDecimo.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stAcao = $_REQUEST["stAcao"] ? $_REQUEST["stAcao"] : $_GET["stAcao"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterCalculoDecimo";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao;
$pgFilt = "FL".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao;
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao;
$pgOcul = "OC".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao;
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao;
$pgJS   = "JS".$stPrograma.".js";

include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );
$obTAdministracaoConfiguracao = new TAdministracaoConfiguracao();
$obTAdministracaoConfiguracao->setDado( "cod_modulo", "27"                         );
$obTAdministracaoConfiguracao->setDado( "exercicio" , Sessao::getExercicio()           );
$obTAdministracaoConfiguracao->setDado( "parametro" , "mes_calculo_decimo".Sessao::getEntidade()         );
$obTAdministracaoConfiguracao->recuperaPorChave($rsAdministracao)   ;
$inMesPagamentoSaldo = $rsAdministracao->getCampo("valor");

include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoDecimoEvento.class.php" );
$obTFolhaPagamentoDecimoEvento = new TFolhaPagamentoDecimoEvento;
$obTFolhaPagamentoDecimoEvento->recuperaTodos($rsDecimoEvento);
if ($rsDecimoEvento->getNumLinhas() < 0) {
    SistemaLegado::exibeAviso(urlencode("Configuração Cálculo de 13º Salário inexistente!"),"n_incluir","erro");
    SistemaLegado::LiberaFrames();
    exit();
}

include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php"                   );
$obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
$obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsUltimaMovimentacao);
$inMesCompetencia = (int) substr($rsUltimaMovimentacao->getCampo("dt_final"),3,2);

include_once(CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoCalcularFolhas.class.php");
$obRFolhaPagamentoCalcularFolhas = new RFolhaPagamentoCalcularFolhas();
$obRFolhaPagamentoCalcularFolhas->setTipoFiltro($_REQUEST['stTipoFiltro']);
switch ($_REQUEST['stTipoFiltro']) {
    case 'contrato':
    case 'cgm_contrato':
        $obRFolhaPagamentoCalcularFolhas->setCodigos(Sessao::read('arContratos'));
        break;
    case 'local':
        $obRFolhaPagamentoCalcularFolhas->setCodigos($_POST['inCodLocalSelecionados']);
        break;
    case 'lotacao':
        $obRFolhaPagamentoCalcularFolhas->setCodigos($_POST['inCodLotacaoSelecionados']);
        break;
}
$obRFolhaPagamentoCalcularFolhas->setRecalcular(Sessao::read("rsRecalcular"));
$obRFolhaPagamentoCalcularFolhas->setCalcularDecimo();
switch (true) {
    case $inMesCompetencia == 12:
        $obRFolhaPagamentoCalcularFolhas->addDesdobramento("D");
        $obRFolhaPagamentoCalcularFolhas->addDesdobramento("C");
        break;
    case $inMesCompetencia < $inMesPagamentoSaldo:
        $obRFolhaPagamentoCalcularFolhas->addDesdobramento("A");
        break;
    case $inMesCompetencia == $inMesPagamentoSaldo:
        $obRFolhaPagamentoCalcularFolhas->addDesdobramento("D");
        break;
}
$obRFolhaPagamentoCalcularFolhas->calcularFolha();
?>
