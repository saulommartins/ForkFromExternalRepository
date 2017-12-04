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
    * Página de Processamento de Feriados
    * Data de Criação   : 24/04/2005

    * @author Vandré Miguel Ramos

    * @ignore

    $Revision: 30566 $
    $Name$
    $Author: vandre $
    $Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

    * Casos de uso :uc-04.02.06

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GRH_CAL_NEGOCIO."RCalendarioFeriado.class.php"          );
include_once( CAM_GRH_CAL_NEGOCIO."RCalendarioPontoFacultativo.class.php"  );

//Define o nome dos arquivos PHP
$stPrograma = "ManterPontoFacultativo";
$pgFilt = "FL".$stPrograma.".php?" . Sessao::getId() . '&stAcao=' . $_REQUEST['stAcao'];
$pgList = "LS".$stPrograma.".php?" . Sessao::getId() . '&stAcao=' . $_REQUEST['stAcao'];
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
$stDescricao = addslashes($_POST['stDescricao']);

switch ($stAcao) {
    case "incluir":
              $obRPontoFacultativo = new RCalendarioPontoFacultativo;
              $obRPontoFacultativo->setDtFeriado  ( $_POST['dtData'] );
              $obRPontoFacultativo->setDescricao  ( $stDescricao );
              $obRPontoFacultativo->setTipoFeriado('P');
              $obErro = $obRPontoFacultativo->incluirPontoFacultativo($boTransacao);

        if ( !$obErro->ocorreu() ) {
          sistemaLegado::alertaAviso($pgFilt,"Ponto Facultativo: ".$stDescricao,"incluir","aviso",
            Sessao::getId(), "../");
        } else {
          sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;

    case "alterar":
        $obRPontoFacultativo = new RCalendarioPontoFacultativo;
        $obRPontoFacultativo->setCodFeriado ( $_POST['inCodFeriado'] );
        $obRPontoFacultativo->setDtFeriado  ( $_POST['dtData'] );
        $obRPontoFacultativo->setDescricao  ( $stDescricao );
        $obRPontoFacultativo->setTipoFeriado('P');
        $obErro = $obRPontoFacultativo->alterarPontoFacultativo($boTransacao);

        if (!$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgFilt,"Ponto facultativo: ".$stDescricao,"alterar","aviso",Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }

    break;

    case "excluir":

        $obRPontoFacultativo = new RCalendarioPontoFacultativo;
        $obRPontoFacultativo->setCodFeriado( $_REQUEST['inCodFeriado'] );
        $obErro = $obRPontoFacultativo->excluirPontoFacultativo($boTransacao);
        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgFilt,"Ponto facultativo: ".$_REQUEST['stDescQuestao'],"excluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_excluir","erro");
            sistemaLegado::alertaAviso($pgList. '&dtData=' . $_REQUEST['dtData'],"Impossível excluir este ponto facultativo: ".$_REQUEST['stDescQuestao'],"n_excluir","erro", Sessao::getId(), "../");
        }

    break;
}
?>
