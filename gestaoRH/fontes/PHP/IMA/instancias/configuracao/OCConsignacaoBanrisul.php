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
    * Arquivo de Oculto para consignação Banrisul
    * Data de Criação: 09/06/2008

    * @author Alex Cardoso

    * Casos de uso: uc-04.08.27

    $Id: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ConsignacaoBanrisul";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

function preencherDados()
{
    $stJs  = montaJavaScriptComboEventosRemuneracao();
    $stJs  .= montaJavaScriptComboEventosLiquido();

    return $stJs;
}

function montaJavaScriptComboEventosRemuneracao()
{
    include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAConsignacaoBanrisulRemuneracao.class.php");
    $obTIMAConsignacaoBanrisulRemuneracao = new TIMAConsignacaoBanrisulRemuneracao();
    $obTIMAConsignacaoBanrisulRemuneracao->recuperaRelacionamento($rsEventosGravados);
    $stJs .= "limpaSelect(f.inCodEventoSelecionadosRemuneracao,0);\n";
    $stJs .= "limpaSelect(f.inCodEventoDisponiveisRemuneracao,0);\n";

    $inIndex = 0;
    $stCodEventos = "";
    while (!$rsEventosGravados->eof()) {
        $stJs .= "f.inCodEventoSelecionadosRemuneracao[".$inIndex."] = new Option('".$rsEventosGravados->getCampo("codigo")."-".trim($rsEventosGravados->getCampo("descricao"))."','".$rsEventosGravados->getCampo("cod_evento")."','');\n";
        $stCodEventos .= $rsEventosGravados->getCampo("cod_evento").",";
        $inIndex++;
        $rsEventosGravados->proximo();
    }
    $stCodEventos = substr($stCodEventos,0,strlen($stCodEventos)-1);

    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php");
    $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
    $stFiltro  = " WHERE (natureza = 'P' OR natureza = 'B')";
    if ($stCodEventos!="") {
        $stFiltro .= "   AND cod_evento NOT IN (".$stCodEventos.")";
    }
    $obTFolhaPagamentoEvento->recuperaTodos($rsEventos,$stFiltro," descricao");
    $inIndex = 0;
    while (!$rsEventos->eof()) {
        $stJs .= "f.inCodEventoDisponiveisRemuneracao[".$inIndex."] = new Option('".$rsEventos->getCampo("codigo")."-".trim($rsEventos->getCampo("descricao"))."','".$rsEventos->getCampo("cod_evento")."','');\n";
        $stCodEventos .= $rsEventos->getCampo("cod_evento").",";
        $inIndex++;
        $rsEventos->proximo();
    }

    return $stJs;
}

function montaJavaScriptComboEventosLiquido()
{
    include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAConsignacaoBanrisulLiquido.class.php");
    $obTIMAConsignacaoBanrisulLiquido = new TIMAConsignacaoBanrisulLiquido();
    $obTIMAConsignacaoBanrisulLiquido->recuperaRelacionamento($rsEventosGravados);
    $stJs .= "limpaSelect(f.inCodEventoSelecionadosLiquido,0);\n";
    $stJs .= "limpaSelect(f.inCodEventoDisponiveisLiquido,0);\n";

    $inIndex = 0;
    $stCodEventos = "";
    while (!$rsEventosGravados->eof()) {
        $stJs .= "f.inCodEventoSelecionadosLiquido[".$inIndex."] = new Option('".$rsEventosGravados->getCampo("codigo")."-".trim($rsEventosGravados->getCampo("descricao"))."','".$rsEventosGravados->getCampo("cod_evento")."','');\n";
        $stCodEventos .= $rsEventosGravados->getCampo("cod_evento").",";
        $inIndex++;
        $rsEventosGravados->proximo();
    }
    $stCodEventos = substr($stCodEventos,0,strlen($stCodEventos)-1);

    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php");
    $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
    $stFiltro  = " WHERE (natureza = 'D')";
    if ($stCodEventos!="") {
        $stFiltro .= "   AND cod_evento NOT IN (".$stCodEventos.")";
    }
    $obTFolhaPagamentoEvento->recuperaTodos($rsEventos,$stFiltro," descricao");
    $inIndex = 0;
    while (!$rsEventos->eof()) {
        $stJs .= "f.inCodEventoDisponiveisLiquido[".$inIndex."] = new Option('".$rsEventos->getCampo("codigo")."-".trim($rsEventos->getCampo("descricao"))."','".$rsEventos->getCampo("cod_evento")."','');\n";
        $stCodEventos .= $rsEventos->getCampo("cod_evento").",";
        $inIndex++;
        $rsEventos->proximo();
    }

    return $stJs;
}

switch ($_GET['stCtrl']) {
    case "preencherDados":
        $stJs = preencherDados();
        break;
}
if ($stJs) {
    echo $stJs;
}

?>
