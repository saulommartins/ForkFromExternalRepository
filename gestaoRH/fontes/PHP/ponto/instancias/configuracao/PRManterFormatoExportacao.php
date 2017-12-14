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
 * Processamento para Configuração Formato de Exportação
 * Data de Criação   : 21/10/2008

 * @author Analista      Dagiane Vieira
 * @author Desenvolvedor Diego Lemos de Souza

 * @package URBEM
 * @subpackage

 * @ignore

 $Id:$
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stAcao = $_REQUEST["stAcao"] ? $_REQUEST["stAcao"] : $_GET["stAcao"];
$arLink = Sessao::read("link");
$stLink = "&pg=".$arLink["pg"]."&pos=".$arLink["pos"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterFormatoExportacao";
$pgFilt      = "FL".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgList      = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgForm      = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgProc      = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgOcul      = "OC".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgJS        = "JS".$stPrograma.".js";

include_once(CAM_GRH_PON_MAPEAMENTO."TPontoFormatoExportacao.class.php");
include_once(CAM_GRH_PON_MAPEAMENTO."TPontoFormatoFaixasHorasExtras.class.php");
include_once(CAM_GRH_PON_MAPEAMENTO."TPontoFormatoInformacao.class.php");
include_once(CAM_GRH_PON_MAPEAMENTO."TPontoDadosExportacao.class.php");
include_once(CAM_GRH_PON_MAPEAMENTO."TPontoFaixasHorasExtra.class.php");
$obTPontoFaixasHorasExtra = new TPontoFaixasHorasExtra();
$obTPontoDadosExportacao = new TPontoDadosExportacao();
$obTPontoFormatoExportacao = new TPontoFormatoExportacao();
$obTPontoFormatoFaixasHorasExtras = new TPontoFormatoFaixasHorasExtras();
$obTPontoFormatoInformacao = new TPontoFormatoInformacao();
$obTPontoDadosExportacao->obTPontoFormatoExportacao = &$obTPontoFormatoExportacao;
$obTPontoFormatoInformacao->obTPontoDadosExportacao = &$obTPontoDadosExportacao;
$obTPontoFormatoFaixasHorasExtras->obTPontoDadosExportacao = &$obTPontoDadosExportacao;

Sessao::setTrataExcecao(true);
switch ($stAcao) {
    case "incluir":
        $pgRetorno = $pgForm;
        $stMensagem = "Configuração ".$_POST["stDescricao"]." incluída com sucesso.";
        $obTPontoFormatoExportacao->setDado("descricao"         ,$_POST["stDescricao"]);
        $obTPontoFormatoExportacao->setDado("formato_minutos"   ,$_POST["stFormatoMinutos"]);
        $obTPontoFormatoExportacao->inclusao();

        $arDadosExportacao = Sessao::read("arDadosExportacao");
        if (is_array($arDadosExportacao)) {
            foreach ($arDadosExportacao as $arDados) {
                $obTPontoDadosExportacao->setDado("cod_dado","");
                $obTPontoDadosExportacao->setDado("cod_tipo",$arDados["inCodTipo"]);
                $obTPontoDadosExportacao->setDado("cod_evento",$arDados["inCodEvento"]);
                $obTPontoDadosExportacao->inclusao();

                if (in_array($arDados["inCodTipo"],array(1,2,4))) {
                    $obTPontoFormatoInformacao->setDado("formato",$arDados["stFormato"]);
                    $obTPontoFormatoInformacao->inclusao();
                }
                if ($arDados["inCodTipo"] == 7 and is_array($arDados["arFaixas"])) {
                    foreach ($arDados["arFaixas"] as $stCampo) {
                        $arCampo = explode("_",$stCampo);
                        $stFiltro  = "   AND faixas_horas_extra.cod_configuracao = ".$arCampo[1];
                        $stFiltro .= "   AND faixas_horas_extra.cod_faixa = ".$arCampo[2];
                        $obTPontoFaixasHorasExtra->recuperaRelacionamento($rsFaixasHorasExtra,$stFiltro);

                        $obTPontoFormatoFaixasHorasExtras->setDado("cod_configuracao",$arCampo[1]);
                        $obTPontoFormatoFaixasHorasExtras->setDado("cod_faixa",$arCampo[2]);
                        $obTPontoFormatoFaixasHorasExtras->setDado("timestamp",$rsFaixasHorasExtra->getCampo("timestamp"));
                        $obTPontoFormatoFaixasHorasExtras->inclusao();
                    }
                }
            }
        }

        break;
    case "alterar";
        $pgRetorno = $pgList;
        $stMensagem = "Configuração ".$_POST["stDescricao"]." alterada com sucesso.";
        $obTPontoFormatoExportacao->setDado("cod_formato"       ,$_POST["inCodFormato"]);
        $obTPontoFormatoExportacao->setDado("descricao"         ,$_POST["stDescricao"]);
        $obTPontoFormatoExportacao->setDado("formato_minutos"   ,$_POST["stFormatoMinutos"]);
        $obTPontoFormatoExportacao->alteracao();

        $arDadosExportacao = Sessao::read("arDadosExportacao");
        if (is_array($arDadosExportacao)) {
            $obTPontoFormatoFaixasHorasExtras->exclusao();
            $obTPontoFormatoInformacao->exclusao();
            $obTPontoDadosExportacao->setCampoCod("");
            $obTPontoDadosExportacao->exclusao();
            $obTPontoDadosExportacao->setCampoCod("cod_dado");
            foreach ($arDadosExportacao as $arDados) {
                $obTPontoDadosExportacao->setDado("cod_dado","");
                $obTPontoDadosExportacao->setDado("cod_tipo",$arDados["inCodTipo"]);
                $obTPontoDadosExportacao->setDado("cod_evento",$arDados["inCodEvento"]);
                $obTPontoDadosExportacao->inclusao();

                if (in_array($arDados["inCodTipo"],array(1,2,4))) {
                    $obTPontoFormatoInformacao->setDado("formato",$arDados["stFormato"]);
                    $obTPontoFormatoInformacao->inclusao();
                }
                if ($arDados["inCodTipo"] == 7 and is_array($arDados["arFaixas"])) {
                    foreach ($arDados["arFaixas"] as $stCampo) {
                        $arCampo = explode("_",$stCampo);
                        $stFiltro  = "   AND faixas_horas_extra.cod_configuracao = ".$arCampo[1];
                        $stFiltro .= "   AND faixas_horas_extra.cod_faixa = ".$arCampo[2];
                        $obTPontoFaixasHorasExtra->recuperaRelacionamento($rsFaixasHorasExtra,$stFiltro);

                        $obTPontoFormatoFaixasHorasExtras->setDado("cod_configuracao",$arCampo[1]);
                        $obTPontoFormatoFaixasHorasExtras->setDado("cod_faixa",$arCampo[2]);
                        $obTPontoFormatoFaixasHorasExtras->setDado("timestamp",$rsFaixasHorasExtra->getCampo("timestamp"));
                        $obTPontoFormatoFaixasHorasExtras->inclusao();
                    }
                }
            }
        }
        break;
    case "excluir":
        $pgRetorno = $pgList;
        $stMensagem = "Configuração ".$_POST["stDescricao"]." excluída com sucesso.";
        $obTPontoFormatoExportacao->setDado("cod_formato"       ,$_GET["inCodFormato"]);
        $obTPontoFormatoFaixasHorasExtras->exclusao();
        $obTPontoFormatoInformacao->exclusao();
        $obTPontoDadosExportacao->setCampoCod("");
        $obTPontoDadosExportacao->exclusao();
        $obTPontoDadosExportacao->setCampoCod("cod_dado");
        $obTPontoFormatoExportacao->exclusao();
        break;
}
Sessao::encerraExcecao();
sistemaLegado::alertaAviso($pgRetorno,$stMensagem,$stAcao,"aviso",Sessao::getId(),"../");
?>
