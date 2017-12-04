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
    * Página de Formulário Oculto Calendario
    * Data de Criação   : 08/09/2004

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Eduardo Martins

    * @ignore

    $Revision: 30895 $
    $Name$
    $Author: tiago $
    $Date: 2007-06-20 15:16:28 -0300 (Qua, 20 Jun 2007) $

    * Casos de uso :uc-04.02.04

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php" );
include_once( CAM_GRH_CAL_NEGOCIO."RCalendarioRelatorioCalendario.class.php" );

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];
$obRRelatorio           = new RRelatorio;
$obRRelatorioCalendario = new RCalendarioRelatorioCalendario;

// Acoes por pagina
switch ($stCtrl) {
    case "buscaCalendario":
        if ($_POST["inCodCalendar"] != "") {
            $obRegra = new RCalendario;
            $obRegra->setCodCalendar( $_POST["inCodCalendar"] );
            $obRegra->consultar();
            $null = "&nbsp;";

            if ( !$obRegra->getDescricao() ) {
                $js .= 'f.inCodCalendar.value = "";';
                $js .= 'f.inCodCalendar.focus();';
                $js .= 'd.getElementById("stDescricao").innerHTML = "'.$null.'";';
                $js .= "alertaAviso('@Valor inválido. (".$_POST["inCodCalendar"].")','form','erro','".Sessao::getId()."');";
            } else {
                $js .= 'd.getElementById("stDescricao").innerHTML = "'.$obRegra->getDescricao().'";';
            }
            $js .= 'f.stCtrl.value = "";';
        }
        sistemaLegado::executaFrameOculto($js);
    break;
    default:
        $arSessaoFiltroRelatorio = Sessao::read('filtroRelatorio');

        $obRRelatorioCalendario->obRCalendario->setCodCalendar( $arSessaoFiltroRelatorio['inCodCalendar'] );
        $obRRelatorioCalendario->obRCalendario->addFeriadoVariavel();
        $obRRelatorioCalendario->obRCalendario->ultimoFeriadoVariavel->setDtInicial( $arSessaoFiltroRelatorio['dtDataInicial'] );
        $obRRelatorioCalendario->obRCalendario->ultimoFeriadoVariavel->setDtFinal  ( $arSessaoFiltroRelatorio['dtDataFinal']   );
        $obRRelatorioCalendario->obRCalendario->commitFeriadoVariavel();

        $obRRelatorioCalendario->geraRecordSet( $rsFeriadoVariavel , $rsFeriadoFixo, $rsPontoFacultativo, $rsDiaCompensado, "" );

        Sessao::write('transf6',  $obRRelatorioCalendario->obRCalendario->getDescricao());
        Sessao::write('transf50', $rsFeriadoFixo);
        Sessao::write('transf51', $rsFeriadoVariavel);
        Sessao::write('transf52', $rsPontoFacultativo);
        Sessao::write('transf53', $rsDiaCompensado);

        $obRRelatorio->executaFrameOculto( "OCGeraRelatorioCalendario.php" );
    break;
}
?>
