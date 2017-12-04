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
    * Página de Oculto do Exportação RAIS
    * Data de Criação: 26/10/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Id: OCExportarRAIS.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-04.08.13
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ExportarRAIS";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

function gerarSpanDataRetificacao()
{
    $stHtml = "";
    $stEval = "";
    if ($_GET["stIndicador"] == "1") {
        $obDtRetificacao = new Data();
        $obDtRetificacao->setRotulo("Data Retificação");
        $obDtRetificacao->setName("dtRetificacao");
        $obDtRetificacao->setTitle("Informe a data da retificação do arquivo da RAIS.");
        $obDtRetificacao->setNull(false);

        $obFormulario = new Formulario();
        $obFormulario->addComponente($obDtRetificacao);
        $obFormulario->montaInnerHTML();
        $obFormulario->obJavaScript->montaJavaScript();
        $stHtml = $obFormulario->getHTML();
        $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    }
    $stJs  = "d.getElementById('spnDataRetificacao').innerHTML = '".$stHtml."'\n";
    $stJs .= "f.hdnDataRetificacao.value = '".$stEval."'\n;";

    return $stJs;
}

function validarConfiguracaoAno()
{
    $obErro = new Erro;
    if ($_GET["inAnoCompetencia"] != "") {
        include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoRais.class.php");
        $obTIMAConfiguracaoRAIS = new TIMAConfiguracaoRais();
        $obTIMAConfiguracaoRAIS->setDado("exercicio",$_GET["inAnoCompetencia"]);
        $obTIMAConfiguracaoRAIS->recuperaPorChave($rsConfiguracao);
        if ($rsConfiguracao->getNumLinhas() < 0) {
            $obErro->setDescricao($obErro->getDescricao()."@A configuração da RAIS no exercício ".$_GET["inAnoCompetencia"]." não foi realizada, essa configuração é necessária para a geração do arquivo.");
        }
    }
    if ($obErro->ocorreu()) {
        $stJs .= "f.inAnoCompetencia.value = '';\n";
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

switch ($_GET['stCtrl']) {
    case "gerarSpanDataRetificacao":
        $stJs .= gerarSpanDataRetificacao();
        break;
    case "validarConfiguracaoAno":
        $stJs = validarConfiguracaoAno();
        break;
}

if ($stJs) {
    echo $stJs;
}

?>
