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
    * Página de Processameto de Controle de Licença Prêmio
    * Data de Criação : 22/10/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: Diego lemos de Souza

    * @ignore

    $Id: PRControleLicencaPremio.php 66258 2016-08-03 14:25:21Z evandro $

    * Casos de uso: uc-04.04.18
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
//include_once (CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php"                 );

//Define o nome dos arquivos PHP
$stPrograma = "ControleLicencaPremio";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stCodigos = "";
switch ($_POST["stTipoFiltro"]) {
    case "contrato":
    case "cgm_contrato":
        foreach (Sessao::read("arContratos") as $arContrato) {
            $stCodigos .= $arContrato["cod_contrato"].",";
        }
        $stCodigos = substr($stCodigos,0,strlen($stCodigos)-1);
        break;
    case "lotacao_grupo":
        $stCodigos = implode(",",$_POST["inCodLotacaoSelecionados"]);
        break;
    case "local_grupo":
        $stCodigos = implode(",",$_POST["inCodLocalSelecionados"]);
        break;
    case "sub_divisao_funcao_grupo":
        $stCodigos = implode(",",$_POST["inCodSubDivisaoSelecionadosFunc"]);
        break;
    case "atributo_servidor_grupo":
        $inCodAtributo = $_POST["inCodAtributo"];
        $inCodCadastro = $_POST["inCodCadastro"];
        $stNome = "Atributo_".$inCodAtributo."_".$inCodCadastro;
        if (is_array($_POST[$stNome."_Selecionados"])) {
            $stCodigos = implode(",",$_POST[$stNome."_Selecionados"]);
        } else {
            $stCodigos = $_POST[$stNome];
        }
        break;
}

$arFinalLeitura = explode("/",$_POST["dtFinalLeitura"]);
$dtFinalLeitura = $arFinalLeitura[2]."-".$arFinalLeitura[1]."-".$arFinalLeitura[0];

$preview = new PreviewBirt(4,22,6);
$preview->setVersaoBirt("2.5.0"); // relatorio desenvolvido na versao 2.1.2 do birt
$preview->setReturnURL( CAM_GRH_PES_INSTANCIAS."relatorio/".$pgFilt);
$preview->addParametro("stEntidade", Sessao::getEntidade());
$preview->addParametro("entidade", Sessao::getCodEntidade($boTransacao));
$preview->addParametro("inCodAssentamento", $_POST["inCodAssentamento"]);
$preview->addParametro("stTipoFiltro", $_POST["stTipoFiltro"]);
$preview->addParametro("stCodigos", $stCodigos);
$preview->addParametro("inCodAtributo", $_POST["inCodAtributo"]);
$preview->addParametro("stDataFinalLeitura", $dtFinalLeitura);
$preview->addParametro("boQuebrar", $_POST["boQuebrar"]);
$preview->addParametro("boAgrupar", $_POST["boAgrupar"]);
$preview->preview();
?>
