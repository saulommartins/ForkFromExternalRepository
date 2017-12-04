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
 * Processamento para Exportação de Pontos
 * Data de Criação   : 22/10/2008

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
$stPrograma = "ManterExportacao";
$pgFilt      = "FL".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgList      = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgForm      = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgProc      = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgOcul      = "OC".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgJS        = "JS".$stPrograma.".js";

switch ($_REQUEST["stTipoFiltro"]) {
    case "contrato":
    case "cgm_contrato":
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
        foreach (Sessao::read("arContratos") as $arContrato) {
            $stCodContratos .= $arContrato["cod_contrato"].",";
        }
        $stCodigos = substr($stCodContratos,0,strlen($stCodContratos)-1);
        break;
    case "lotacao":
        $stCodigos = implode(",",$_POST["inCodLotacaoSelecionados"]);
        break;
    case "local":
        $stCodigos = implode(",",$_POST["inCodLocalSelecionados"]);
        break;
    case "reg_sub_fun_esp":
         $stCodigos  = implode(",",$_REQUEST["inCodRegimeSelecionadosFunc"])."#";
         $stCodigos .= implode(",",$_REQUEST["inCodSubDivisaoSelecionadosFunc"])."#";
         $stCodigos .= implode(",",$_REQUEST["inCodFuncaoSelecionados"])."#";
         if (is_array($_REQUEST["inCodEspecialidadeSelecionadosFunc"])) {
             $stCodigos .= implode(",",$_REQUEST["inCodEspecialidadeSelecionadosFunc"]);
         }
        break;
}

Sessao::write("stTipoFiltro",$_REQUEST["stTipoFiltro"]);
Sessao::write("stCodigos",$stCodigos);
Sessao::write("dtInicial",$_POST["stDataInicial"]);
Sessao::write("dtFinal",$_POST["stDataFinal"]);

include_once(CAM_GRH_PON_MAPEAMENTO."TPontoFormatoExportacao.class.php");
$obTPontoFormatoExportacao = new TPontoFormatoExportacao();
$obTPontoFormatoExportacao->setDado("cod_formato",$_POST["inCodFormato"]);
$obTPontoFormatoExportacao->setDado("dt_inicial",$_POST["stDataInicial"]);
$obTPontoFormatoExportacao->setDado("dt_final",$_POST["stDataFinal"]);
$obTPontoFormatoExportacao->setDado("filtro",$_REQUEST["stTipoFiltro"]);
$obTPontoFormatoExportacao->setDado("codigos",$stCodigos);
$obTPontoFormatoExportacao->exportarPonto($rsPontos);

################################## EXPORTADOR ###################################
include_once(CLA_EXPORTADOR);
$obExportador = new Exportador();
$obExportador->setRetorno($pgForm);
$obExportador->addArquivo("eventosponto.txt");
$obExportador->roUltimoArquivo->setTipoDocumento("ExportacaoPontos");

$obExportador->roUltimoArquivo->addBloco($rsPontos);
$obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(";");
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("registro");
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codigo_evento");
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor");
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("quantidade");
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("quantidade_parcelas");

$obExportador->Show();

?>
