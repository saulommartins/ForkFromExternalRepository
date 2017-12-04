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
  * Página de Processamento da Suspensão
  * Data de criação : 14/09/2006

  * @author Analista: Fábio Bertoldi
  * @author Programador: Márson Luís Oliveira de Paula

    * $Id: PRManterSuspensao.php 59612 2014-09-02 12:00:51Z gelson $

    Caso de uso: uc-05.03.08
**/

/*
$Log$
Revision 1.4  2006/11/24 16:13:10  marson
Adição do caso de uso de Suspensão.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRSuspensao.class.php" );

$stAcao                = $_REQUEST["stAcao"];
$inCGM                 = $_REQUEST["inCGM"];
$inCodGrupo            = $_REQUEST["inCodGrupo"];
$inCodCredito          = $_REQUEST["inCodCredito"];
$inCodigoTipoSuspensao = $_REQUEST["inCodigoTipoSuspensao"];

$stLink = "&stAcao=".$stAcao;
//Define o nome dos arquivos PHP
$stPrograma = "ManterSuspensao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$obRARRSuspensao = new RARRSuspensao;
if($stAcao == "alterar")
   $obRARRSuspensao->setCodSuspensao( $_REQUEST['inCodSuspensao']);
// Para suspensao
$obRARRSuspensao->obRARRTipoSuspensao->setCodigoTipoSuspensao( $_REQUEST['inCodigoTipoSuspensao'] ) ;
$obRARRSuspensao->setInicio( $_REQUEST['dtInicio'] );
$obRARRSuspensao->setObservacao( $_REQUEST['stObservacao'] ); // Também para suspensao_termino
$obRARRSuspensao->setCodLancamento( $_REQUEST['inCodLancamento']); // Também para processo_suspensao e suspensao_termino

// Para processo_suspensao
list($inCodProcesso,$stAnoExercicio) = preg_split( '/\//',$_REQUEST['stChaveProcesso']);
$obRARRSuspensao->obRProcesso->setCodigoProcesso( (int) $inCodProcesso );
$obRARRSuspensao->obRProcesso->setExercicio( $stAnoExercicio );

// Para suspensao_termino
$obRARRSuspensao->setTermino( $_REQUEST['dtTermino'] );
if ($stAcao == "incluir") {
   $obErro = $obRARRSuspensao->suspenderCredito();
} else {
   if ($_REQUEST['dtTermino']) {
      $obErro = $obRARRSuspensao->alterarSuspensao();
   }
}

if ( !$obErro->ocorreu() ) {
  SistemaLegado::alertaAviso($pgList."?$stLink","Código da Suspensão: ".$obRARRSuspensao->getCodSuspensao(),"baixar","aviso", Sessao::getId(), "../");
} else {
  SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_baixar","erro");
}
