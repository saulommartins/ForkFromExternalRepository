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
    * Página de Processamento de Norma
    * Data de Criação   : 01/12/2004

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Gelson W. Gonçalves

    * @ignore

    $Revision: 30805 $
    $Name$
    $Author: luciano $
    $Date: 2007-02-06 09:55:26 -0200 (Ter, 06 Fev 2007) $

    * Casos de uso: uc-02.03.01
*/

/*
$Log$
Revision 1.6  2007/02/06 11:54:50  luciano
#8281#

Revision 1.5  2006/07/17 14:14:34  andre.almeida
Bug #6084#

Revision 1.4  2006/07/05 20:47:34  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GF_EMP_NEGOCIO. "REmpenhoHistorico.class.php");

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterHistorico";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php";

$obRegra = new REmpenhoHistorico;

//Trecho de código do filtro
$stFiltro = '';
if ($stAcao != 'incluir') {
   $stFiltro = '';
   if ( Sessao::read('filtro') ) {
      $arFiltro = Sessao::read('filtro');
      $stFiltro = '';
      foreach ($arFiltro as $stCampo => $stValor) {
         $stFiltro .= "&".$stCampo."=".@urlencode( $stValor );
      }
      $stFiltro .= '&pg='.Sessao::read('pg').'&pos='.Sessao::read('pos').'&paginando'.Sessao::read('paginando');
   }
}

switch ($stAcao) {

    case "incluir":

        $obRegra->setCodHistoricoInclusao( $_POST['inCodHistoricoInclusao'] );
        $obRegra->setNomHistorico ( str_replace('\\',"", ($_POST['stNomHistorico'])));

        $obRegra->setExercicio           ( Sessao::getExercicio()       );

        $obErro = $obRegra->salvar();

        if ( !$obErro->ocorreu() )
            SistemaLegado::alertaAviso($pgForm,"Histórico: ".$_POST['stNomHistorico'],"incluir","aviso", Sessao::getId(), "../");
        else
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");

    break;
    case "alterar":

        $obRegra->setCodHistorico    ( $_POST['inCodHistorico'] );
        $obRegra->setNomHistorico ( str_replace('\\',"",($_POST['stNomHistorico'])));
        $obRegra->setExercicio       ( Sessao::getExercicio()       );

        $obErro = $obRegra->salvar();

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList.$stFiltro,"Histórico: ".$_POST['stNomHistorico'],"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }

    break;
    case "excluir";
        $obRegra->setCodHistorico    ( $_REQUEST['inCodHistorico'] );
        $obRegra->setExercicio       ( Sessao::getExercicio()          );

        $obErro = $obRegra->excluir();

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList.$stFiltro,"Histórico: ".$_REQUEST['stNomHistorico'],"excluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::alertaAviso($pgList,urlencode( $obErro->getDescricao() ),"n_excluir","erro", Sessao::getId(), "../");
        }

    break;
}

?>
