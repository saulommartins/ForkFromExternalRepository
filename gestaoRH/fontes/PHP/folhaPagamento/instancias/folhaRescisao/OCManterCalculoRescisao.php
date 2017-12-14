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
    * Página de Oculto do Calculo de Rescisão
    * Data de Criação: 18/10/2006

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-04.05.18

    $Id: OCManterCalculoRescisao.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GRH_PES_COMPONENTES."IFiltroContrato.class.php"                                       );
include_once( CAM_GRH_PES_COMPONENTES."IFiltroCGMContrato.class.php"                                    );
include_once( CAM_GRH_PES_COMPONENTES."ISelectMultiploLotacao.class.php"                                );

//Define o nome dos arquivos PHP
$stPrograma = "ManterCalculoRescisao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

function gerarSpanSucessoErro()
{
    include_once(CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoCalcularFolhas.class.php");
    $obRFolhaPagamentoCalcularFolhas = new RFolhaPagamentoCalcularFolhas();
    $obRFolhaPagamentoCalcularFolhas->setCalcularRescisao();

    return $obRFolhaPagamentoCalcularFolhas->gerarSpanSucessoErro();
}

function gerarSpanSucessoCalculo()
{
    include_once(CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoCalcularFolhas.class.php");
    $obRFolhaPagamentoCalcularFolhas = new RFolhaPagamentoCalcularFolhas();
    $obRFolhaPagamentoCalcularFolhas->setCalcularRescisao();

    return $obRFolhaPagamentoCalcularFolhas->gerarSpanSucessoCalculo();
}

function gerarSpanErroCalculo()
{
    include_once(CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoCalcularFolhas.class.php");
    $obRFolhaPagamentoCalcularFolhas = new RFolhaPagamentoCalcularFolhas();
    $obRFolhaPagamentoCalcularFolhas->setCalcularRescisao();

    return $obRFolhaPagamentoCalcularFolhas->gerarSpanErroCalculo();
}

function imprimirFichaFinanceira()
{
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorPrevidencia.class.php");
    $obTPessoalContratoServidorPrevidencia = new TPessoalContratoServidorPrevidencia();
    $stFiltro  = " AND contrato_servidor_previdencia.cod_contrato = ".$_REQUEST["cod_contrato"];
    $stFiltro .= " AND bo_excluido is false ";
    $obTPessoalContratoServidorPrevidencia->recuperaPrevidencias($rsPrevidencia,$stFiltro);
    $inCodPrevidencia = ($rsPrevidencia->getNumLinhas() == 1)?$rsPrevidencia->getCampo("cod_previdencia"): 0;
    Sessao::write("cod_contrato",$_REQUEST["cod_contrato"]);
    Sessao::write("numcgm",$_REQUEST["numcgm"]);
    Sessao::write("cod_previdencia",$inCodPrevidencia);
    $stJs .= "f.stAcao.value = 'imprimirFichaFinanceira';\n";
    $stJs .= "parent.frames[2].Salvar();    \n";

    return $stJs;
}

function submeter()
{
    $stJs .= "BloqueiaFrames(true,false);";
    $stJs .= "jQuery('#showLoading',parent.frames[2].document).css('width','500px');";
    $stJs .= "jQuery('#showLoading',parent.frames[2].document).css('margin','-25px 0px 0px -250px;');";
    $stJs .= "parent.frames[2].Salvar();\n";

    return $stJs;
}

function recalcular()
{
    $rsListaErro = Sessao::read("rsListaErro");
    Sessao::clean();
    Sessao::write('rsRecalcular',$rsListaErro);
    Sessao::write("boExcluirCalculados",false);
    $stJs .= "stAction = f.action;";
    $stJs .= "stTarget = f.target;";
    $stJs .= "f.action ='PRManterCalculoRescisao.php?".Sessao::getId()."&stAcao=calcular&stTipoFiltro=recalcular';";
    $stJs .= "f.target = 'oculto';";
    $stJs .= submeter();
    $stJs .= "f.action = stAction;";
    $stJs .= "f.target = stTarget;";

    return $stJs;
}

function imprimirErro()
{
    $stJs .= "f.stAcao.value = 'imprimirErro';\n";
    $stJs .= "parent.frames[2].Salvar();    \n";

    return $stJs;
}

switch ($_REQUEST['stCtrl']) {
    case "submeter":
        $stJs .= submeter();
        break;
    case "recalcular":
        $stJs = recalcular();
        break;
    case "imprimirErro":
        $stJs = imprimirErro();
        break;
    case "imprimirFichaFinanceira":
        $stJs = imprimirFichaFinanceira();
        break;
    case "gerarSpanSucessoCalculo":
        $stJs  = gerarSpanSucessoCalculo();
        break;
    case "gerarSpanErroCalculo":
        $stJs  = gerarSpanErroCalculo();
        break;
    case "gerarSpanSucessoErro":
        $stJs = gerarSpanSucessoErro();
        break;
}

if ($stJs) {
   echo $stJs;
}

?>
