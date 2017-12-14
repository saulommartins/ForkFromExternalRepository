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
    * Página de Filtro do Relatório Bancário de Pensão Judicial
    * Data de Criação : 21/03/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: André Machado

    * @ignore

    $Revision: 31509 $
    $Name$
    $Autor: $
    $Date: 2008-03-31 14:50:43 -0300 (Seg, 31 Mar 2008) $

    * Casos de uso: uc-04.05.57
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "BancarioPensaoJudicial";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

switch ($_POST["stTipoFiltro"]) {
    case "contrato":
    case "cgm_contrato":
        if (count(Sessao::read('arContratos'))>0) {
            foreach (Sessao::read('arContratos') as $array ) {
                $stCodigos .=  $array['cod_contrato'].",";
            }
        }
        break;
    case "lotacao":
    case "lotacao_grupo":
        if (count($_REQUEST['inCodLotacaoSelecionados'])>0) {
            foreach ($_REQUEST['inCodLotacaoSelecionados'] as $inCodLotacao) {
                $stCodigos .= $inCodLotacao.",";
            }
        }
        break;
    case "local":
    case "local_grupo":
        if (count($_REQUEST['inCodLocalSelecionados'])>0) {
            foreach ($_REQUEST['inCodLocalSelecionados'] as $inCodLocal) {
                $stCodigos .= $inCodLocal.",";
            }
        }
        break;
    case "atributo_servidor":
    case "atributo_servidor_grupo":
        include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoCadastro.class.php");
        include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoAtributoDinamico.class.php");
        $obTAdministracaoAtributoDinamico = new TAdministracaoAtributoDinamico();
        $obTAdministracaoAtributoDinamico->setDado("cod_atributo",$_POST["inCodAtributo"]);
        $obTAdministracaoAtributoDinamico->setDado("cod_modulo",22);
        $obTAdministracaoAtributoDinamico->setDado("cod_cadastro",$_POST["inCodCadastro"]);
        $obTAdministracaoAtributoDinamico->recuperaPorChave($rsAtributoTipo);
        $inCodTipoAtributo = $rsAtributoTipo->getCampo("cod_tipo");
        $stNomeAtributo = "Atributo_".$_POST["inCodAtributo"]."_".$_POST["inCodCadastro"];
        $stValor = "";
        if ($rsAtributoTipo->getCampo("cod_tipo") == 4) {
            foreach ($_POST[$stNomeAtributo."_Selecionados"] as $inCodValor) {
                $stValor .= $inCodValor.",";
            }
            $stValor = substr($stValor,0,strlen($stValor)-1);
        } else {
            $stValor = $_POST[$stNomeAtributo];
        }
        break;
}
$stCodigos = substr($stCodigos,0,strlen($stCodigos)-1);
$stAgrupar = ($_POST["boAgrupar"]) ? "true" : "false";
$stQuebrar = ($_POST["boQuebrar"]) ? "true" : "false";

if (count($_REQUEST['inCodBancoSelecionados'])>0) {
    foreach ($_REQUEST['inCodBancoSelecionados'] as $inCodBanco) {
        $stBancos .= $inCodBanco.",";
    }
}
$stBancos = substr($stBancos,0,strlen($stBancos)-1);

if (count($_REQUEST['inCodAgenciaSelecionados'])>0) {
    foreach ($_REQUEST['inCodAgenciaSelecionados'] as $inCodAgencia) {
        $stAgencias .= $inCodAgencia.",";
    }
}
$stAgencias = substr($stAgencias,0,strlen($stAgencias)-1);

include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
$obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
$obTFolhaPagamentoPeriodoMovimentacao->setDado("mes",$_POST["inCodMes"]);
$obTFolhaPagamentoPeriodoMovimentacao->setDado("ano",$_POST["inAno"]);
$obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacaoDaCompetencia($rsPeriodoMovimentacao);

$preview = new PreviewBirt(4,27,2);
$preview->setVersaoBirt( '2.5.0' );
$preview->setReturnURL( CAM_GRH_FOL_INSTANCIAS."relatorio/FLBancarioPensaoJudicial.php");
$preview->setTitulo('Bancário de Pensao Judicial');
$preview->setNomeArquivo('bancariopensaojudicial');
$preview->addParametro("stEntidade", Sessao::getEntidade());
$preview->addParametro("entidade", Sessao::getCodEntidade($boTransacao));
$preview->addParametro("cod_periodo_movimentacao",$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
$preview->addParametro("competencia", $rsPeriodoMovimentacao->getCampo("dt_final"));
$preview->addParametro("agrupar", $stAgrupar);
$preview->addParametro("quebrar", $stQuebrar);
$preview->addParametro("filtro", $_POST["stTipoFiltro"]);
$preview->addParametro("codigos", $stCodigos);
$preview->addParametro("bancos", $stBancos);
$preview->addParametro("agencias", $stAgencias);
$preview->addParametro("cod_atributo",$_POST["inCodAtributo"]);
$preview->addParametro("valor", $stValor);
$preview->addParametro("cod_tipo_atributo", $inCodTipoAtributo);
$preview->addParametro("inCodConfiguracao", $_REQUEST["inCodConfiguracao"]);
$preview->addParametro("inCodComplementar", ($_REQUEST["inCodComplementar"]) ? $_REQUEST["inCodComplementar"] : 0);
$preview->preview();
?>
