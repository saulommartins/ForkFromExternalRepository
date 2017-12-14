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
    * 
    * Data de Criação   : 26/09/2014

    * @author Analista:
    * @author Desenvolvedor:  Lisiane Morais
    * @ignore

    $Id: PRManterOrcamento.php 60054 2014-09-26 14:55:37Z lisiane $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_ADM_NEGOCIO."RConfiguracaoConfiguracao.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterOrcamento";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obRAdministracaoConfiguracao = new RConfiguracaoConfiguracao;
$obRAdministracaoConfiguracao->setCodModulo(63);
$obRAdministracaoConfiguracao->setExercicio(Sessao::getExercicio());

switch ($_REQUEST['stAcao']) {
  default:
    if ( $_REQUEST['ano_vigencia'] ) {
      $obRAdministracaoConfiguracao->setParametro('tcepe_ano_vigencia');
      $obRAdministracaoConfiguracao->setValor($_REQUEST['ano_vigencia']);
      $obErro= $obRAdministracaoConfiguracao->alterar();
    }
    
    if ( $_REQUEST['dtAprovacaoLOA'] ) {
      $obRAdministracaoConfiguracao->setParametro('tcepe_data_aprovacao_LOA');
      $obRAdministracaoConfiguracao->setValor($_REQUEST['dtAprovacaoLOA']);
      $obErro= $obRAdministracaoConfiguracao->alterar();
    }
    
    if ( $_REQUEST['inCodLeiLOA'] ) {
      $obRAdministracaoConfiguracao->setParametro('tcepe_lei_orcamentaria_LOA');
      $obRAdministracaoConfiguracao->setValor($_REQUEST['inCodLeiLOA']);
      $obErro= $obRAdministracaoConfiguracao->alterar();
    }
    
    if ( $_REQUEST['dtAprovacaoLDO'] ) {
      $obRAdministracaoConfiguracao->setParametro('tcepe_data_aprovacao_LDO');
      $obRAdministracaoConfiguracao->setValor($_REQUEST['dtAprovacaoLDO']);
      $obErro= $obRAdministracaoConfiguracao->alterar();
    }
    
    if ( $_REQUEST['inCodLeiLDO'] ) {
      $obRAdministracaoConfiguracao->setParametro('tcepe_lei_orcamentaria_LDO');
      $obRAdministracaoConfiguracao->setValor($_REQUEST['inCodLeiLDO']);
      $obErro= $obRAdministracaoConfiguracao->alterar();
    }
    
    if ( $_REQUEST['dtAprovacaoPPA'] ) {
      $obRAdministracaoConfiguracao->setParametro('tcepe_data_aprovacao_PPA');
      $obRAdministracaoConfiguracao->setValor($_REQUEST['dtAprovacaoPPA']);
      $obErro= $obRAdministracaoConfiguracao->alterar();
    }
    
    if ( $_REQUEST['inCodLeiPPA'] ) {
      $obRAdministracaoConfiguracao->setParametro('tcepe_lei_orcamentaria_PPA');
      $obRAdministracaoConfiguracao->setValor($_REQUEST['inCodLeiPPA']);
      $obErro= $obRAdministracaoConfiguracao->alterar();
    }
    
    if ( !$obErro->ocorreu )
      SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=$stAcao","Configuração ","incluir","incluir_n", Sessao::getId(), "../");
    else
      SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
    
  break;
}

?>
