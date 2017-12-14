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
   * Página de Processamento de Configuração para Relatórios MODELOS
   * Data de Criação   : 22/05/2006

   * @author Analista: Jorge B. Ribarr
   * @author Desenvolvedor: Anderson R. M. Buzo

   * @ignore

   $Id: PRManterVeiculosPublicidade.php 59612 2014-09-02 12:00:51Z gelson $

   * Casos de uso: uc-03.05.11

*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(TLIC."TLicitacaoVeiculosPublicidade.class.php");
include_once(TLIC."TLicitacaoPublicacaoConvenio.class.php" );
include_once(TLIC."TLicitacaoPublicacaoEdital.class.php"   );
include_once(TLIC."TLicitacaoPublicacaoContrato.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterVeiculosPublicidade";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";
$pgJS      = "JS".$stPrograma.".js";

$obTLicitacaoVeiculosPublicidade = new TLicitacaoVeiculosPublicidade();
$obTLicitacaoPublicacaoConvenio  = new TLicitacaoPublicacaoConvenio();
$obTLicitacaoPublicacaoEdital    = new TLicitacaoPublicacaoEdital();

$inCGM  = $_REQUEST["inCGM"];
$inCodTipoVeiculoPublicidade = $_REQUEST['inCodTipoVeiculoPublicidade'];

switch ($_REQUEST['stAcao']) {
case 'incluir':
   $obTLicitacaoVeiculosPublicidade->setDado("cod_tipo_veiculos_publicidade", $inCodTipoVeiculoPublicidade);
   $obTLicitacaoVeiculosPublicidade->setDado("numcgm", $inCGM);

   $rsVeiculosPublicidade = new RecordSet;
   $obTLicitacaoVeiculosPublicidade->recuperaPorChave($rsVeiculosPublicidade);

   if ($rsVeiculosPublicidade->getNumLinhas() > 0) {
      $stErro = "Já existe um veículo de publicidade cadastrado para esse CGM - $inCGM";
   }

   if (!$stErro) {
      $obTLicitacaoVeiculosPublicidade->inclusao();
      SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$_REQUEST['stAcao'],$inCGM." - ".$_REQUEST['stNomCGM'],"incluir","aviso", Sessao::getId(), "../");
   } else {
      SistemaLegado::exibeAviso(urlencode($stErro), "n_incluir", "erro" );
   }
   break;

case 'excluir':
   $boExcluir = true;

   $rsTLicitacaoPublicacaoConvenio = new RecordSet();
   $rsTLicitacaoPublicacaoEdital   = new RecordSet();

   $obTLicitacaoPublicacaoConvenio->setDado("numcgm", $inCGM);
   $obTLicitacaoPublicacaoConvenio->recuperaPorChave($rsTLicitacaoPublicacaoConvenio);
   if ($rsTLicitacaoPublicacaoConvenio->getNumLinhas() > 0) {
      $boExcluir = false;
   }

   $obTLicitacaoPublicacaoEdital->setDado("numcgm", $inCGM);
   $obTLicitacaoPublicacaoEdital->recuperaPorChave($rsTLicitacaoPublicacaoEdital);
   if ( $rsTLicitacaoPublicacaoEdital->getNumLinhas() > 0 ) {
      $boExcluir = false;
   }

   if ($boExcluir) {
      $obTLicitacaoPublicacaoContrato = new TLicitacaoPublicacaoContrato;
      $obTLicitacaoPublicacaoContrato->setDado("numcgm", $inCGM);
      $obTLicitacaoPublicacaoContrato->recuperaPorChave( $rsContratos );
      $boExcluir = ( $rsContratos->getNumLinhas() <= 0 );
   }

   if ($boExcluir) {
      $obTLicitacaoVeiculosPublicidade->setDado("cod_tipo_veiculos_publicidade",$inCodTipoVeiculoPublicidade);
      $obTLicitacaoVeiculosPublicidade->setDado("numcgm", $inCGM);
      $obTLicitacaoVeiculosPublicidade->exclusao();
      SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$_REQUEST['stAcao'],$obTLicitacaoVeiculosPublicidade->getDado('numcgm')." - ".$_REQUEST['stNomCGM'],"excluir","aviso", Sessao::getId(), "../");
   } else {
      $stMsg = "Erro ao excluir Veículos de Publicidade. Este Veículo está sendo utilizado pelo sistema.";
      SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$_REQUEST['stAcao'],$stMsg,"aviso","erro",Sessao::getId(),"../");
   }
   break;

}

//Sessao::encerraExcecao();

?>
