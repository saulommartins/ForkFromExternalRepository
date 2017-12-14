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
* Oculto para configuração de totais da folha
* Data de Criação   : 04/03/2009

* @author Analista      Dagiane Vieira
* @author Desenvolvedor Diego Lemos de Souza

* @package URBEM
* @subpackage

* @ignore # só use se for paginas que o cliente visualiza, se for mapeamento ou classe de negocio não se usa
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

$stPrograma = "TotaisFolha";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

function incluirConfiguracao()
{
    $arConfiguracoes = Sessao::read("arConfiguracoes");

    $arConfiguracao["inId"]      = count($arConfiguracoes);
    $arConfiguracao["descricao"] = utf8_decode($_REQUEST["descricao"]);
    $arConfiguracao["eventos"]   = $_REQUEST["inCodEventoSelecionados"];

    $arConfiguracoes[] = $arConfiguracao;
    Sessao::write("arConfiguracoes",$arConfiguracoes);
    $stJs = montaListaConfiguracoes();

    return $stJs;
}

function alterarConfiguracao()
{
    $arConfiguracoes = Sessao::read("arConfiguracoes");

    $arConfiguracao["inId"]      = Sessao::read("inId");
    $arConfiguracao["descricao"] = utf8_decode($_REQUEST["descricao"]);
    $arConfiguracao["eventos"]   = $_REQUEST["inCodEventoSelecionados"];

    $arConfiguracoes[Sessao::read("inId")] = $arConfiguracao;
    Sessao::write("arConfiguracoes",$arConfiguracoes);
    $stJs = montaListaConfiguracoes();
    $stJs .= "jQuery('#btAlterarConfiguracao').attr('disabled','disabled');";
    $stJs .= "jQuery('#btIncluirConfiguracao').removeAttr('disabled');";

    return $stJs;
}

function excluirConfiguracao()
{
    $arConfiguracoes = Sessao::read("arConfiguracoes");

    $inId = $_REQUEST["inId"];
    $arTemp = array();
    foreach ($arConfiguracoes as $arConfiguracao) {
        if ($arConfiguracao["inId"] != $inId) {
            $arConfiguracao["inId"] = count($arTemp);
            $arTemp[] = $arConfiguracao;
        }
    }
    Sessao::write("arConfiguracoes",$arTemp);
    $stJs = montaListaConfiguracoes();

    return $stJs;
}

function montaListaConfiguracoes()
{
    $arRecordSet = Sessao::read("arConfiguracoes");
    $rsRecordSet = new Recordset;
    $rsRecordSet->preenche( is_array($arRecordSet) ? $arRecordSet : array() );
    if ($rsRecordSet->getNumLinhas() != 0) {
        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setTitulo( "Lista de Configuração - Relatório de Totais da Folha" );
        $obLista->setRecordSet( $rsRecordSet );

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Descrição" );
        $obLista->ultimoCabecalho->setWidth( 60 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Ação" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "[descricao]");
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "ALTERAR" );
        $obLista->ultimaAcao->setLinkId("alterar");
        $obLista->ultimaAcao->setFuncaoAjax( true );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('montaAlterarConfiguracao');");
        $obLista->commitAcao();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncaoAjax( true );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('excluirConfiguracao');");
        $obLista->commitAcao();

        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);
    }
    $stJs = "jQuery('#spnConfiguracoes').html('".$stHtml."');";
    $stJs .= "jQuery('#btAlterarConfiguracao').attr('disabled', 'disabled');\n";
    $stJs .= "jQuery('#btIncluirConfiguracao').removeAttr('disabled');\n";

    return $stJs;
}

function montaAlterarConfiguracao()
{
    $arConfiguracoes = Sessao::read("arConfiguracoes");
    $inId = $_REQUEST["inId"];
    Sessao::write("inId",$inId);
    $arConfiguracao = $arConfiguracoes[$inId];

    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php");
    $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();

    $stJs = "jQuery('#descricao').attr('value','".$arConfiguracao["descricao"]."');";
    $stJs .= "jQuery('#inCodEventoDisponiveis').removeOption(/./);";
    $stJs .= "jQuery('#inCodEventoSelecionados').removeOption(/./);";
    $stFiltro = " WHERE natureza = 'P' OR natureza = 'D'";
    $obTFolhaPagamentoEvento->recuperaTodos($rsEventos,$stFiltro," ORDER BY codigo");
    while (!$rsEventos->eof()) {
        $stJs .= "jQuery('#inCodEventoDisponiveis').addOption('".$rsEventos->getCampo("cod_evento")."','".$rsEventos->getCampo("codigo")."-".$rsEventos->getCampo("descricao")."',false);";
        $rsEventos->proximo();
    }

    foreach ($arConfiguracao["eventos"] as $inCodEvento) {
        $obTFolhaPagamentoEvento->setDado("cod_evento",$inCodEvento);
        $obTFolhaPagamentoEvento->recuperaPorChave($rsEvento);
        $stJs .= "jQuery('#inCodEventoDisponiveis').removeOption('".$inCodEvento."');";
        $stJs .= "jQuery('#inCodEventoSelecionados').addOption('".$inCodEvento."','".$rsEvento->getCampo("codigo")."-".$rsEvento->getCampo("descricao")."',false);";
    }
    $stJs .= "jQuery('#btIncluirConfiguracao').attr('disabled','disabled');";
    $stJs .= "jQuery('#btAlterarConfiguracao').removeAttr('disabled');";

    return $stJs;
}

switch ($_REQUEST['stCtrl']) {
    case "incluirConfiguracao":
        $stJs = incluirConfiguracao();
        break;
    case "alterarConfiguracao":
        $stJs = alterarConfiguracao();
        break;
    case "excluirConfiguracao":
        $stJs = excluirConfiguracao();
        break;
    case "montaAlterarConfiguracao":
        $stJs = montaAlterarConfiguracao();
        break;
    case "montaListaConfiguracoes":
        $stJs = montaListaConfiguracoes();
        break;
}

if ($stJs != "") {
    echo $stJs;
}

?>
