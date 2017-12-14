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
 * Oculto do IPopUpGradeHorario
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

function preencherGradeHorarios()
{
    if ($_GET["inCodGrade"] != "") {
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalGradeHorario.class.php");
        $obTPessoalGradeHorario = new TPessoalGradeHorario();
        $stFiltro = " WHERE cod_grade = ".$_GET["inCodGrade"];
        $obTPessoalGradeHorario->recuperaTodos($rsRecordSet,$stFiltro," ORDER BY descricao");
        $stNull = "&nbsp;";
        if ( $rsRecordSet->getNumLinhas() <= 0) {
            $stJs  = "jQuery('#inCodGrade').attr('value','');\n";
            $stJs .= "jQuery('#inCodGrade').focus();\n";
            $stJs .= "jQuery('#stGrade').html('".$stNull."');\n";
            $stJs .= "jQuery('#HdninCodGrade').attr('value','');\n";
            $stJs .= "jQuery('stGrade').attr('value','');\n";
            $stJs .= "alertaAviso('@Campo Grade de Horários inválido. (".$_REQUEST["inCodGrade"].")','form','erro','".Sessao::getId()."');\n";
        } else {
            $stJs  = "jQuery('#stGrade').html('".trim($rsRecordSet->getCampo('descricao'))."');\n";
            $stJs .= "jQuery('#stGrade').attr('value','".trim($rsRecordSet->getCampo('descricao'))."');\n";
            $stJs .= "jQuery('#HdninCodGrade').attr('value','".$rsRecordSet->getCampo("cod_grade")."');\n";
        }
    } else {
        $stJs .= "jQuery('stGrade').html('&nbsp;');";
    }

    return $stJs;
}

switch ($_GET["stCtrl"]) {
    case "preencherGradeHorarios":
        $stJs .= preencherGradeHorarios();
        break;
}
if ($stJs) {
    echo $stJs;
}
?>
