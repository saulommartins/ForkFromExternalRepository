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
    * Data de Criação   : 16/08/2004

    * @author Eduardo Martins

    * @ignore

    $Revision: 30859 $
    $Name$
    $Author: vandre $
    $Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

    * Casos de uso :uc-04.02.01

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GRH_CAL_NEGOCIO."RCalendarioFeriado.class.php"          );
include_once( CAM_GRH_CAL_NEGOCIO."RCalendarioFeriadoVariavel.class.php"  );

//Define o nome dos arquivos PHP
$stPrograma = "ManterFeriado";
$pgFilt = "FL".$stPrograma.".php?" . Sessao::getId() . '&stAcao=' . $_REQUEST['stAcao'];
$pgList = "LS".$stPrograma.".php?" . Sessao::getId() . '&stAcao=' . $_REQUEST['stAcao'];
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
$stDescricao = str_replace("'", "", $_REQUEST['stDescricao']);

switch ($stAcao) {
    case "incluir":
          switch ($_POST['stTipoFeriado']) {
           case "F":
              $obRFeriado = new RFeriado;
              $obRFeriado->setDtFeriado  ( $_POST['dtData'] );
              $obRFeriado->setDescricao  ( $stDescricao  );
              $obRFeriado->setTipoFeriado( $_POST['stTipoFeriado'] );
              $obRFeriado->setAbrangencia( $_POST['stAbrangencia']);
              $obErro = $obRFeriado->salvar();
           break;
           case "V":
              $obRFeriadoVariavel = new RFeriadoVariavel;
              $obRFeriadoVariavel->setDtFeriado  ( $_POST['dtData'] );
              $obRFeriadoVariavel->setDescricao  ( $stDescricao  );
              $obRFeriadoVariavel->setTipoFeriado( $_POST['stTipoFeriado'] );
              $obRFeriadoVariavel->setAbrangencia( $_POST['stAbrangencia']);
              $obErro =  $obRFeriadoVariavel->salvar();
              if (!$obErro->ocorreu()) {
                  $obErro = $obRFeriadoVariavel->incluirFeriadoVariavel();
              }
            break;
          }

        if ( !$obErro->ocorreu() ) {
          sistemaLegado::alertaAviso($pgFilt,"Feriado: ".$stDescricao ,"incluir","aviso",
            Sessao::getId(), "../");
        } else {
          sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }

    break;

    case "alterar":
        $obRFeriado = new RFeriado;
        $obRFeriado->setCodFeriado ( $_POST['inCodFeriado'] );
        $obRFeriado->setDtFeriado  ( $_POST['dtData'] );
        $obRFeriado->setDescricao  ( $stDescricao  );
        $obRFeriado->setTipoFeriado( $_POST['stTipoFeriado'] );
        $obRFeriado->setAbrangencia( $_POST['stAbrangencia']);

        if ($_REQUEST['boTipoFeriado'] == 'Fixo') {
           $boTipoFeriado = "true";
        } else {
           $boTipoFeriado = "false";
        }

        $obErro = $obRFeriado->Salvar();

        if ( !$obErro->ocorreu() ) {
            if ($_REQUEST['boTipoFeriado'] == 'Fixo') {   // É FIXO
                $obRFeriadoVariavel = new RFeriadoVariavel;
                $obRFeriadoVariavel->setCodFeriado ( $_POST['inCodFeriado'] );
                if ($_REQUEST['stTipoFeriado']=='F') {                       // É FIXO e continua FIXO
                    $obErro = $obRFeriadoVariavel->alterarFeriadoVariavel();
                } else {
                    $obErro = $obRFeriadoVariavel->incluirFeriadoVariavel();
                }
            } else {                    // É VARIAVEL
                $obRFeriadoVariavel = new RFeriadoVariavel;
                $obRFeriadoVariavel->setCodFeriado ( $_POST['inCodFeriado'] );

                if ($_REQUEST['stTipoFeriado'] == 'V') {                      // É VARIÁVEL e continua VARIÁVEL
                    $obErro = $obRFeriadoVariavel->alterarFeriadoVariavel();
                } else {
                    $obErro = $obRFeriadoVariavel->excluirFeriadoVariavel();
                }
            }
        }

        if ( !$obErro->ocorreu() ) {
          sistemaLegado::alertaAviso($pgFilt,"Feriado: ".$stDescricao ,"alterar","aviso",
            Sessao::getId(), "../");
        } else {
          sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }

    break;

    case "excluir":
        $stFiltro = "";

        $arSessaoLink = Sessao::read('link');

        if ( is_array( $arSessaoLink['filtro'] )) {
            foreach ($arSessaoLink['filtro'] as $stCampo => $stValor) {
                $stFiltro .= $stCampo."=".urlencode( $stValor )."&";
            }
        }
        $stFiltro .= "pg=".$arSessaoLink['pg']."&";
        $stFiltro .= "pos=".$arSessaoLink['pos']."&";
        $stFiltro .= "stAcao=".$_REQUEST['stAcao'];

        if ($_REQUEST['stTipoFeriado'] == 'Fixo') {
           $obRFeriado = new RFeriado;
           $obRFeriado->setCodFeriado( $_GET['inCodFeriado'] );
           if ( $obRFeriado->getCodFeriado() ) {
                $obErro = $obRFeriado->excluir();
           }
        } else
          if ($_REQUEST['stTipoFeriado'] == 'Variável') {
            $obRFeriado = new RFeriado;
            $obRFeriado->setCodFeriado( $_GET['inCodFeriado'] );

            $obRFeriadoVariavel = new RFeriadoVariavel;
            $obRFeriadoVariavel->setCodFeriado( $_GET['inCodFeriado'] );

            if ( $obRFeriadoVariavel->getCodFeriado() ) {
                 $obErro = $obRFeriadoVariavel->excluirFeriadoVariavel();
             }
             if (!$obErro->ocorreu()) {
                  $obErro = $obRFeriado->excluir();
             }
          }

        if (!$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgFilt,"Feriado: ".$_REQUEST['stDescQuestao'],"excluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_excluir","erro");
            sistemaLegado::alertaAviso($pgList. '&dtData=' . $_REQUEST['dtData'],"Impossível excluir este feriado: ".$_REQUEST['stDescQuestao'],"n_excluir","erro", Sessao::getId(), "../");
        }

    break;

}
?>
