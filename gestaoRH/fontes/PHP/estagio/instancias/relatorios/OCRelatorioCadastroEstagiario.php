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
 * Página de Filtro do Relatório de Cadastro de Estagiários
 * Data de Criação: 28/12/2009
 * @author Desenvolvedor: Diego Mancilha
    $Id:
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioCadastroEstagiario";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

function preencherSpanContrato()
{
    $stHTML = "";

    if ($_GET['stContrato'] == 'dtFimEstagio') {

        $stMsgHint = 'Informe um período para filtro dos cadastros com data fim de estágio.';
        $stRotulo = 'Data Fim do Estágio de';

        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsMovimento);

        $obPeriodo = new Periodo();
        $obPeriodo->setRotulo($stRotulo);
        $obPeriodo->obLabel->setValue("&nbsp;a&nbsp;");
        $obPeriodo->setTitle($stMsgHint);

        $obPeriodo->obDataInicial->setId('stDtInicial');
        $obPeriodo->obDataFinal->setId('stDtFinal');

        $obPeriodo->obDataInicial->setValue($rsMovimento->getCampo('dt_inicial'));
        $obPeriodo->obDataFinal->setValue($rsMovimento->getCampo('dt_final'));

        $obPeriodo->setNull(false);

        $obFormulario = new Formulario();
        $obFormulario->addComponente($obPeriodo);

        $obFormulario->obJavaScript->montaJavaScript();

        $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
        $stEval = str_replace("\n","",$stEval);

        $obFormulario->montaInnerHTML();
        $stHTML = $obFormulario->getHTML();
    } else {
        $stJs .= "jQuery('#stDtInicial').remove();";
        $stJs .= "jQuery('#stDtFinal').remove();";
    }

    $stJs .= "jQuery('#spnContrato').html('".$stHTML."');";
    $stJs .= "jQuery('#hdnContrato').val('".$stEval."');";

    return $stJs;
}

switch ($_GET['stCtrl']) {
    case "preencherSpanContrato":
        $stJs .= preencherSpanContrato();
        break;
}

if ($stJs) {
    echo $stJs;
}

?>
