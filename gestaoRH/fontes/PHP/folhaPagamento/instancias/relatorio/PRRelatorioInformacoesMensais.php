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
    * Página de Processamento do Relatório Informações Mensais
    * Data de Criação: 28/12/2006

    * @author Analista: Dagiane
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30840 $
    $Name$
    $Author: alex $
    $Date: 2008-03-10 14:10:50 -0300 (Seg, 10 Mar 2008) $

    * Casos de uso: uc-04.05.58
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php");
include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");

$link = Sessao::read("link");
$stAcao = $_REQUEST["stAcao"] ? $_REQUEST["stAcao"] : $_GET["stAcao"];
$stLink = "&pg=".$link["pg"]."&pos=".$link["pos"];

foreach ($_POST as $key=>$value) {
    $stLink .= $key."=".$value."&";
}

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioInformacoesMensais";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
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
        $stCodigos = implode($_POST["inCodLotacaoSelecionados"],",");
        break;
    case "local_grupo":
        $stCodigos = implode($_POST["inCodLocalSelecionados"],",");
        break;
    case "atributo_servidor_grupo":
        $inCodAtributo = $_POST["inCodAtributo"];
        $inCodCadastro = $_POST["inCodCadastro"];
        $stNome = "Atributo_".$inCodAtributo."_".$inCodCadastro;
        if (is_array($_POST[$stNome."_Selecionados"])) {
            $stCodigos = implode($_POST[$stNome."_Selecionados"],",");
            $boAtributoMultiplo = 1;
        } else {
            $stCodigos = pg_escape_string($_POST[$stNome]);
            $boAtributoMultiplo = 0;
        }

        //Recupera o nome e o tipo do atributo
        include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoCadastro.class.php");
        include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoAtributoDinamico.class.php");
        $obTAdministracaoAtributoDinamico = new TAdministracaoAtributoDinamico();

        $rsAtributoDinamico = new RecordSet();
        $obTAdministracaoAtributoDinamico->setDado("cod_modulo",   22);
        $obTAdministracaoAtributoDinamico->setDado("cod_cadastro", $inCodCadastro);
        $obTAdministracaoAtributoDinamico->setDado("cod_atributo", $inCodAtributo);
        $obTAdministracaoAtributoDinamico->recuperaPorChave($rsAtributoDinamico);

        $stNomeAtributo    = $rsAtributoDinamico->getCampo("nom_atributo");
        $inCodTipoAtributo = $rsAtributoDinamico->getCampo("cod_tipo");
        break;
}

$obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();

if ( is_array($_POST["inCodEventoSelecionados"]) ) {
    foreach ($_POST["inCodEventoSelecionados"] as $inCodEvento) {
        $obTFolhaPagamentoEvento->setDado("cod_evento",$inCodEvento);
        $obTFolhaPagamentoEvento->recuperaPorChave($rsEvento);
        $arRotulosEventos[]   = $rsEvento->getCampo("codigo");

        $legenda = $rsEvento->getCampo("codigo")." - ".trim($rsEvento->getCampo("descricao"));
        if(trim($rsEvento->getCampo("sigla")) != '')
            $legenda .= " (".$rsEvento->getCampo("sigla").")";

        $arLegendasEventos[]  = $legenda;
    }
} else {
    $arRotulosEventos = array();
    $arLegendasEventos = array();
}

    //Adquire Data da Competencia Atual
    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsUltimaMovimentacao);

    if ($rsUltimaMovimentacao->getNumLinhas() > 0 ) {
            $stDataFinal = $rsUltimaMovimentacao->getCampo("dt_final");
            $stDataFinal = explode("/", $stDataFinal);
            if (sizeof($stDataFinal)>0) {
                $inAno = $stDataFinal[2];
                $inMes = $stDataFinal[1];
            }
    }

    $preview = new PreviewBirt(4,27,14);
    $preview->setVersaoBirt('2.5.0');
    $preview->setTitulo('Informações Mensais');
    $preview->setNomeArquivo('informacoesMensais.rtpdesign');
    $preview->setReturnURL( CAM_GRH_FOL_INSTANCIAS."relatorio/".$pgFilt);
    $preview->setExportaExcel(true);
    $preview->addParametro("entidade",Sessao::getCodEntidade($boTransacao));
    $preview->addParametro("stEntidade",Sessao::getEntidade());
    $preview->addParametro("stNomeEntidade",   SistemaLegado::pegaConfiguracao("nom_prefeitura",2,Sessao::getExercicio()));
    $preview->addParametro("stTipoFiltro", 	   $_POST["stTipoFiltro"]);
    $preview->addParametro("stCodigos", 	   $stCodigos);
    $preview->addParametro("inDia",            date("d"));
    $preview->addParametro("inMes",            trim($inMes, " 0"));
    $preview->addParametro("inAno",            $inAno);
    $preview->addParametro("boAgrupar",		   ($_POST["boAgrupar"])?1:0);
    $preview->addParametro("boQuebrar",        ($_POST["boQuebrar"])?1:0);
    $preview->addParametro("stRotulosEventos", implode("#", $arRotulosEventos));
    $preview->addParametro("stLegendasEventos",implode("#", $arLegendasEventos));
    $preview->addParametro("boAtributoMultiplo",$boAtributoMultiplo);
    $preview->addParametro("inCodAtributo",    $_POST["inCodAtributo"]);
    $preview->addParametro("stNomeAtributo",   $stNomeAtributo);
    $preview->addParametro("inCodTipoAtributo",$inCodTipoAtributo);
    $preview->preview();

?>
