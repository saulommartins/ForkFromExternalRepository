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
 * Página de Oculto para Manter Justificativa
 * Data de Criação: 29/09/2008

 * @author Analista      Dagiane Vieira
 * @author Desenvolvedor Alex Cardoso

 * @package URBEM
 * @subpackage

 * @ignore

 $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoJustificativa.class.php"                             );
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoJustificativaHoras.class.php"                        );

$stAcao = $request->get('stAcao');
$stCtrl = $_REQUEST['stCtrl'];

$stPrograma = "ManterJustificativa";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

function preencherHoras($inCodJustificativa, $boAnularFaltas)
{
    $rsJustificativaHoras = new RecordSet();
    if ($inCodJustificativa) {
        $obTPontoJustificativaHoras = new TPontoJustificativaHoras();
        $obTPontoJustificativaHoras->setDado('cod_justificativa', $inCodJustificativa);
        $obTPontoJustificativaHoras->recuperaPorChave($rsJustificativaHoras);
    }

    $obHorasFalta = new Hora();
    $obHorasFalta->setId('stHorasFalta');
    $obHorasFalta->setName('stHorasFalta');
    $obHorasFalta->setRotulo("Horas de faltas parciais a anular");
    $obHorasFalta->setTitle("Informe a quantidade de horas a anular, em caso de anulação parcial.");
    if ($rsJustificativaHoras->getCampo('horas_falta') != "") {
        $obHorasFalta->setValue($rsJustificativaHoras->getCampo('horas_falta'));
    } else {
        $obHorasFalta->setValue("00:00");
    }

    $obHorasAbono = new Hora();
    $obHorasAbono->setId('stHorasAbono');
    $obHorasAbono->setName('stHorasAbono');
    $obHorasAbono->setRotulo("Horas a abonar");
    $obHorasAbono->setTitle("Informe a quantidade de horas a abonar.");
    if ($rsJustificativaHoras->getCampo('horas_abono') != "") {
        $obHorasAbono->setValue($rsJustificativaHoras->getCampo('horas_abono'));
    } else {
        $obHorasAbono->setValue("00:00");
    }
    $obFormulario = new Formulario;
    $obFormulario->addComponente( $obHorasFalta );
    $obFormulario->addComponente( $obHorasAbono );
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    $stHtml = str_replace("\n","",$stHtml);

    if ($boAnularFaltas) {
        $stHtml = "";
    }

    $stJs .= "jQuery('#spnHoras').html('".$stHtml."');\n";
    $stJs .= "jQuery('#boAnularFaltas').attr('disabled', false);\n";

    return $stJs;
}

switch ($stCtrl) {
    case "preencherHoras":
        $stJs = preencherHoras($_REQUEST['inCodJustificativa'], $_REQUEST['boAnularFaltas']);
        break;
}

if ($stJs) {
    echo $stJs;
}

?>
