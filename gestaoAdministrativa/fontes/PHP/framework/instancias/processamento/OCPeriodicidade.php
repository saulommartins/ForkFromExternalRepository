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
    * Data de Criação: 17/12/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * $Id: OCPeriodicidade.php 63993 2015-11-16 17:16:46Z jean $

    * Casos de uso: uc-01.01.00
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

$obComboPeriodicidade = Sessao::read('obPeriodicidade'.$_REQUEST['inIdComponente']);

$stHTML = '';
$stJs   = '';
$stDataInicial = "";
$stDataFinal   = "";

switch ($_REQUEST["stCtrl"]) {
    case 'montaSpan' :
        switch ($_REQUEST['inCodPeriodo']) {
            //dia
            case 1 :
                $obComboPeriodicidade->obDia->montaHTML();
                $stHTML = $obComboPeriodicidade->obDia->getHTML();

                break;
            //mes
            case 2 :
                if ( $obComboPeriodicidade->getValidaExercicio() ) {
                    $obComboPeriodicidade->obLblAnoMes->setValue ( $obComboPeriodicidade->getExercicio() );
                    $obComboPeriodicidade->obLblAnoMes->montaHTML();
                    $stHTML .= $obComboPeriodicidade->obLblAnoMes->getHTML();

                    $obComboPeriodicidade->obHdnAnoMes->setValue ( $obComboPeriodicidade->getExercicio() );
                    $obComboPeriodicidade->obHdnAnoMes->montaHTML();
                    $stHTML .= $obComboPeriodicidade->obHdnAnoMes->getHTML();
                } else {
                    $obComboPeriodicidade->obAnoMes->setValue ( $obComboPeriodicidade->getExercicio() );
                    $obComboPeriodicidade->obAnoMes->montaHTML();
                    $stHTML .= $obComboPeriodicidade->obAnoMes->getHTML();
                }
                
                $obComboPeriodicidade->obMes->montaHTML();
                $stHTML .= '&nbsp;/&nbsp;'.$obComboPeriodicidade->obMes->getHTML();

                break;
            //ano
            case 3 :
                if ( $obComboPeriodicidade->getValidaExercicio() ) {
                    $obComboPeriodicidade->obLblAnoMes->setValue( $obComboPeriodicidade->getExercicio() );
                    $obComboPeriodicidade->obLblAnoMes->montaHTML();
                    $stHTML = $obComboPeriodicidade->obLblAnoMes->getHTML();

                } else {
                    $obComboPeriodicidade->obAno->setValue ( $obComboPeriodicidade->getExercicio() );
                    $obComboPeriodicidade->obAno->montaHTML();
                    $stHTML = $obComboPeriodicidade->obAno->getHTML();

                }
                if ( $obComboPeriodicidade->getExercicio() ) {
                    $stDataInicial = '01/01/'.$obComboPeriodicidade->getExercicio();
                    $stDataFinal = '31/12/'.$obComboPeriodicidade->getExercicio();
                }

                break;
            //periodo
            case 4 :
                $stHTML  = $obComboPeriodicidade->obPeriodoInicial->getHTML();
                $stHTML .= $obComboPeriodicidade->obPeriodoLabel->getHTML();
                $stHTML .= $obComboPeriodicidade->obPeriodoFinal->getHTML();

                break;
            }

        //$obComboPeriodicidade->obAnoMes->setTitle("Informe o ano e mês. Se quiser recuperar todos os anos, deixe o campo ano em branco.");
        $stHTML = nl2br(addslashes(str_replace("\r\n", "\n", preg_replace("/(\r\n|\n|\r)/", "", $stHTML))));
        $stJs .= "$('".$obComboPeriodicidade->obSpan->getId()."').innerHTML = '".$stHTML."';\n";
        $stJs .= "document.frm.".$obComboPeriodicidade->obDataInicial->getName().".value = '".$stDataInicial."';\n";
        $stJs .= "document.frm.".$obComboPeriodicidade->obDataFinal->getName().".value = '".$stDataFinal."';\n";
        $stJs .= "jq('#stAnoMes').prop('title', 'Se você quiser recuperar todos os anos, deixe este campo em branco.');\n";
        break;

    case 'preencheDia' :
        $arData = explode('/',$_REQUEST[$obComboPeriodicidade->obDia->getName()]);
        if ( checkdate((int) $arData[1],(int) $arData[0],(int) $arData[2] ) ) {
            $stJs .= "document.forms[0].".$obComboPeriodicidade->obDataInicial->getName().".value = '".$_REQUEST[$obComboPeriodicidade->obDia->getName()]."'; ";
            $stJs .= "document.forms[0].".$obComboPeriodicidade->obDataFinal->getName().".value = '".$_REQUEST[$obComboPeriodicidade->obDia->getName()]."';";
        } else {
            $stJs .= "document.forms[0].".$obComboPeriodicidade->obDataInicial->getName().".value = ''; ";
            $stJs .= "document.forms[0].".$obComboPeriodicidade->obDataFinal->getName().".value = '';";
        }
        break;
    case 'preencheMes' :
        if ( $_REQUEST[$obComboPeriodicidade->obMes->getName()] != '' AND $_REQUEST[$obComboPeriodicidade->obAnoMes->getName()] != '' ) {
            $inUltDia = cal_days_in_month(CAL_GREGORIAN,$_REQUEST[$obComboPeriodicidade->obMes->getName()],$_REQUEST[$obComboPeriodicidade->obAnoMes->getName()] );
            $stJs .= "document.forms[0].".$obComboPeriodicidade->obDataInicial->getName().".value = '01/".$_REQUEST[$obComboPeriodicidade->obMes->getName()]."/".$_REQUEST[$obComboPeriodicidade->obAnoMes->getName()]."'; ";
            $stJs .= "document.forms[0].".$obComboPeriodicidade->obDataFinal->getName().".value = '".$inUltDia."/".$_REQUEST[$obComboPeriodicidade->obMes->getName()]."/".$_REQUEST[$obComboPeriodicidade->obAnoMes->getName()]."';";
        } else if ($obComboPeriodicidade->getAnoVazio() == true AND $_REQUEST[$obComboPeriodicidade->obAnoMes->getName()] == '') {
            $stJs .= "document.forms[0].".$obComboPeriodicidade->obDataInicial->getName().".value = '".$_REQUEST[$obComboPeriodicidade->obMes->getName()]."'; ";
            $stJs .= "document.forms[0].".$obComboPeriodicidade->obDataFinal->getName().".value = '".$_REQUEST[$obComboPeriodicidade->obMes->getName()]."';";
        }
        break;
    case 'preencheAno' :
        if ( $_REQUEST[$obComboPeriodicidade->obAno->getName()] != '' ) {
            $stJs .= "document.forms[0].".$obComboPeriodicidade->obDataInicial->getName().".value = '01/01/".$_REQUEST[$obComboPeriodicidade->obAno->getName()]."'; ";
            $stJs .= "document.forms[0].".$obComboPeriodicidade->obDataFinal->getName().".value = '31/12/".$_REQUEST[$obComboPeriodicidade->obAno->getName()]."'; ";
        } else {
            $stJs .= "document.forms[0].".$obComboPeriodicidade->obDataInicial->getName().".value = ''; ";
            $stJs .= "document.forms[0].".$obComboPeriodicidade->obDataFinal->getName().".value = ''; ";
        }
        break;
    case 'preenchePeriodo' :
        if ($_REQUEST['stTipo'] == 'inicial') {
            $arData = explode('/',$_REQUEST[$obComboPeriodicidade->obPeriodoInicial->getName()]);
            if ( checkdate((int) $arData[1],(int) $arData[0],(int) $arData[2] ) ) {
                $stJs .= "document.forms[0].".$obComboPeriodicidade->obDataInicial->getName().".value = '".$_REQUEST[$obComboPeriodicidade->obPeriodoInicial->getName()]."'; ";
            }
        } else {
            $arData = explode('/',$_REQUEST[$obComboPeriodicidade->obPeriodoFinal->getName()]);
            if ( checkdate((int) $arData[1],(int) $arData[0],(int) $arData[2] ) ) {
                $stJs .= "document.forms[0].".$obComboPeriodicidade->obDataFinal->getName().".value = '".$_REQUEST[$obComboPeriodicidade->obPeriodoFinal->getName()]."'; ";
            }
        }
        break;

}
echo $stJs;
?>
