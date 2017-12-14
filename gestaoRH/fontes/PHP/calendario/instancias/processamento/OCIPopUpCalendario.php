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
/*
 * Oculto do IPopUpCalendario
 * Data de Criação   : 13/10/2008

 * @author Analista      Dagiane Vieira
 * @author Desenvolvedor Diego Lemos de Souza

 * @package URBEM
 * @subpackage

 * @ignore

 $Id:$
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

function preencherCalendario()
{
    if ($_GET["inCodCalendario"] != "") {
        include_once(CAM_GRH_CAL_MAPEAMENTO."TCalendarioCalendarioCadastro.class.php");
        $obTCalendarioCalendarioCadastro = new TCalendarioCalendarioCadastro();
        $stFiltro = " WHERE cod_calendar = ".$_GET["inCodCalendario"];
        $obTCalendarioCalendarioCadastro->recuperaTodos($rsRecordSet,$stFiltro," ORDER BY descricao");
        $stNull = "&nbsp;";
        if ( $rsRecordSet->getNumLinhas() <= 0) {
            $stJs .= "jQuery('#inCodCalendario').attr('value','');\n";
            $stJs .= "jQuery('#inCodCalendario').focus();\n";
            $stJs .= "jQuery('#stCalendario').html('".$stNull."');\n";
            $stJs .= "jQuery('#HdninCodCalendario').attr('value','');\n";
            $stJs .= "jQuery('#stCalendario').attr('value','');\n";
            $stJs .= "alertaAviso('@Campo Calendário inválido. (".$_REQUEST["inCodCalendario"].")','form','erro','".Sessao::getId()."');\n";
        } else {
            $stPopup = "abrePopUp(\'".CAM_GRH_CAL_POPUPS."calendario/FMConsultarCalendario.php\',\'frm\',\'\',\'\',\'\',\'".Sessao::getId()."&inCodCalendario=".$rsRecordSet->getCampo("cod_calendar")."\',\'800\',\'550\')";
            $stJs .= "jQuery('#stCalendario').html('".$rsRecordSet->getCampo('descricao')."');\n";
            $stJs .= "jQuery('#stCalendario').attr('value','".$rsRecordSet->getCampo('descricao')."');\n";
            $stJs .= "jQuery('#HdninCodCalendario').attr('value','".$rsRecordSet->getCampo("cod_calendar")."');\n";
            $stJs .= "jQuery('#linkConsultarCalendario').attr('href','JavaScript: ".$stPopup."');\n";
        }
    } else {
        $stJs .= "jQuery('#stCalendario').html('&nbsp;');";
    }

    return $stJs;
}

switch ($_GET["stCtrl"]) {
    case "preencherCalendario":
        $stJs .= preencherCalendario();
    break;
}
if ($stJs) {
    echo $stJs;
}
?>
