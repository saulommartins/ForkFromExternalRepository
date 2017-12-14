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
 * Titulo do arquivo Oculto do componente IMontaOrganograma
 * Data de Criação   : 28/11/2008

 * @author Analista      Tonismar Régis Bernardo
 * @author Desenvolvedor Eduardo Paculski Schitz

 * @package URBEM
 * @subpackage

 $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/GA.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GA_ORGAN_COMPONENTES.'IMontaOrganograma.class.php';

$obMontaOrganograma = new IMontaOrganograma;

switch ($_REQUEST["stCtrl"]) {

    case "preencheProxCombo":

        $stNomeComboOrganograma = $_REQUEST['stIdOrganograma']."_".( $_REQUEST["inPosicao"] - 1);
        $stChaveLocal = $_REQUEST[$stNomeComboOrganograma];
        $inPosicao = $_REQUEST["inPosicao"]-1;

        if (empty($stChaveLocal)) {
            $stNomeComboOrganograma = $_REQUEST['stIdOrganograma']."_".( $_REQUEST["inPosicao"] - 2);
            $stChaveLocal = $_REQUEST[$stNomeComboOrganograma];
            $inPosicao = $_REQUEST["inPosicao"]-1 ;
        }

        $arChaveLocal = explode("§" , $stChaveLocal );
        $obMontaOrganograma->obROrganograma->setCodOrganograma($arChaveLocal[3]);

        if ($_REQUEST[$_REQUEST['stIdOrganograma'].'Classificacao']) {
            $stChaveOrganogramaGET = $_REQUEST[$_REQUEST['stIdOrganograma'].'Classificacao'];
        } else {
            $stChaveOrganogramaGET = $_REQUEST['hdn'.$_REQUEST['stIdOrganograma']];
        }

        if ($stChaveOrganogramaGET) {
            $arChaveOrganograma = explode('.', $stChaveOrganogramaGET);
            $inCount = 0;
            $stChave = '';
            foreach ($arChaveOrganograma as $inKey => $inValue) {
                if ($inCount < ($_REQUEST["inPosicao"] - 2)) {
                    $stChave .= $inValue.'.';
                }
                $inCount++;
            }
        }

        if ($arChaveLocal[2] != '') {
            $stChave .= str_pad($arChaveLocal[2], strlen($_REQUEST['mascaraNivel']), '0', STR_PAD_LEFT);
        }

        $obMontaOrganograma->setValorReduzido     ( $stChave );
        $obMontaOrganograma->setIdOrganograma($_REQUEST['stIdOrganograma']);

        if ($_REQUEST['boMostraUltimoNivel']) {
            $obMontaOrganograma->obROrganograma->setMostraUltimoNivel(true);
        }

        $obMontaOrganograma->stMascara = Sessao::read($_REQUEST['stIdOrganograma']."mascaraOrganograma");
        $obMontaOrganograma->setUltimoOrgaoSelecionado($arChaveLocal[1]);
        $obMontaOrganograma->setMostraClassificacao($_REQUEST['boMostraClassificacao']);
        $obMontaOrganograma->preencheProxCombo    ( $inPosicao , $_REQUEST["inNumNiveis"] );

    break;

    case "preencheCombosOrgaos":

        if ($_REQUEST[$_REQUEST['stIdOrganograma'].'Classificacao']) {
            $stChaveOrganogramaGET = $_REQUEST[$_REQUEST['stIdOrganograma'].'Classificacao'];
        }

        if ($stChaveOrganogramaGET) {
            $arChaveOrganograma = explode('.', $stChaveOrganogramaGET);

            $stChaveOrganograma = '';

            foreach ($arChaveOrganograma as $arChaveOrganogramaTMP) {
                if ($arChaveOrganogramaTMP != '') {
                    $stChaveOrganograma .= $arChaveOrganogramaTMP.'.';
                }
            }

            $stChaveOrganograma = substr($stChaveOrganograma, 0, strlen($stChaveOrganograma)-1);
            $obMontaOrganograma->setValorReduzido ( $stChaveOrganograma );

        } else {
            $obMontaOrganograma->setValorReduzido ( '' );
        }

        if (isset($_REQUEST[$_REQUEST['stIdOrganograma'].'Organograma'])) {
            $arOrganograma = explode('§', $_REQUEST[$_REQUEST['stIdOrganograma'].'Organograma']);
            $obMontaOrganograma->obROrganograma->setCodOrganograma($arOrganograma[0]);
        }

        if ($_REQUEST['boMostraUltimoNivel']) {
           $obMontaOrganograma->obROrganograma->setMostraUltimoNivel(true);
        }

        $obMontaOrganograma->setIdOrganograma($_REQUEST['stIdOrganograma']);
        $obMontaOrganograma->setNumNiveis($_REQUEST['inNumNiveis']);
        $obMontaOrganograma->preencheCombosOrgaos($_REQUEST['inNumNiveis']);
    break;

    case "montaCombosOrgaos":

        $arOrganograma = explode('§', $_REQUEST[$_REQUEST['stIdOrganograma'].'Organograma']);

        $obFormulario = new Formulario;
        $obFormulario->setLarguraRotulo($_REQUEST['inLarguraRotulo']);

        $obMontaOrganograma->setStyle($_REQUEST['inLarguraComponente']);
        $obMontaOrganograma->setHiddenEvalName($_REQUEST['hiddenEvalName']);

        if ($arOrganograma[0] != '') {
            $obMontaOrganograma->obROrganograma->setCodOrganograma($arOrganograma[0]);
        } else {
            $obMontaOrganograma->obROrganograma->setCodOrganograma(-1);
        }

        $obMontaOrganograma->setCodigoNivel($_REQUEST['inNumNiveis']);
        $obMontaOrganograma->setMostraComboOrganograma($_REQUEST['boMostraComboOrganograma']);

        if ($_REQUEST['boMostraUltimoNivel']) {
            $obMontaOrganograma->obROrganograma->setMostraUltimoNivel(true);
        }

        $obMontaOrganograma->setIdOrganograma($_REQUEST['stIdOrganograma']);
        $obMontaOrganograma->setNivelObrigatorio     ( $_REQUEST['inCodNivelObrigatorio']);
        $obMontaOrganograma->setMostraClassificacao($_REQUEST['boMostraClassificacao']);

        $obMontaOrganograma->montaCombos($obFormulario,true);

        $obFormulario->obJavaScript->montaJavaScript();
        $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
        $stEval = str_replace("\n","",$stEval);

        $obFormulario->montaInnerHTML();

        # adiciona as combos dentro do span e seus javaScript de validação dentro do hidden
        echo "jq('#".$_REQUEST['stIdOrganograma']."spnCombos').html('".$obFormulario->getHTML()."'); ";

        # Se foi setado um hidden no form principal e deseja-se usa-lo ele colocar o conteudo no hidden do form
        if ($_REQUEST['hiddenEvalName'] == '') {
            echo "document.getElementById('hdnEvalCombos').value='".$stEval."';";
        } else {
            echo "document.getElementById('".$_REQUEST['hiddenEvalName']."').value='".$stEval."';";
        }
    break;
}
?>
