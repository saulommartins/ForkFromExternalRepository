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
    * Página de Filtro do Relatório de Cadastro de Pensão Judicial
    * Data de Criação : 05/03/2007

    * @author Desenvolvedor: André Machado

    * Casos de uso: uc-04.04.49

    $Id: PRCadastroPensaoJudicial.php 66258 2016-08-03 14:25:21Z evandro $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

$ParametroNovo = " , ? ";

//Define o nome dos arquivos PHP
$stPrograma = "CadastroPensaoJudicial";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
$obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
$obTFolhaPagamentoPeriodoMovimentacao->setDado("mes",$_POST["inCodMes"]);
$obTFolhaPagamentoPeriodoMovimentacao->setDado("ano",$_POST["inAno"]);
$obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacaoDaCompetencia($rsPeriodoMovimentacao);

$stCodigos = "";
switch ($_POST["stTipoFiltro"]) {
    case "contrato":
    case "cgm_contrato":
        foreach (Sessao::read('arContratos') as $array ) {
            $stCodigos .=  $array['cod_contrato'].",";
        }
        $stCodigos = substr($stCodigos,0,strlen($stCodigos)-1);
        break;
    case "lotacao_grupo";
        $stCodigos = implode(",",$_POST["inCodLotacaoSelecionados"]);
        break;
    case "local_grupo":
        $stCodigos = implode(",",$_POST["inCodLocalSelecionados"]);
        break;
    case "atributo_servidor_grupo":
        $stNomeAtributo = "Atributo_".$_POST["inCodAtributo"]."_".$_POST["inCodCadastro"];
        if (is_array($_POST[$stNomeAtributo."_Selecionados"])) {
            $stCodigos = implode(",",$_POST[$stNomeAtributo."_Selecionados"]);
            $boArray = "true";
        } else {
            $stCodigos = $_POST[$stNomeAtributo];
            $boArray = "false";
        }
        break;
}

$preview = new PreviewBirt(4,22,2);
$preview->setVersaoBirt( '2.5.0' );
$preview->setReturnURL( CAM_GRH_PES_INSTANCIAS."relatorio/FLCadastroPensaoJudicial.php");
$preview->setTitulo('Cadastro de Pensão Judicial');
$preview->setNomeArquivo('cadastropensaojudicial');
$preview->addParametro("entidade", Sessao::getCodEntidade($boTransacao));
$preview->addParametro("cod_acao", Sessao::read('acao'));
$preview->addParametro("stEntidade", Sessao::getEntidade());
$preview->addParametro("stTipoFiltro", $_POST["stTipoFiltro"]);
$preview->addParametro("stCodigos", $stCodigos);
$preview->addParametro("inCodAtributo", $_POST["inCodAtributo"]);
$preview->addParametro("boArray", $boArray);
$preview->addParametro("boAgrupar",$_POST["boAgrupar"]);
$preview->addParametro("boQuebrar",$_POST["boQuebrar"]);
$preview->addParametro("inCodPeriodoMovimentacao",$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
$preview->preview();
?>
