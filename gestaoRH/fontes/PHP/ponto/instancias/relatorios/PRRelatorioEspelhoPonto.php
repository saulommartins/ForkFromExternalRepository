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
    * Página de Processamento para Relatorio Espelho Ponto
    * Data de Criação: 15/10/2008

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: <Alex Cardoso>

    * @ignore

    $Id: $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioEspelhoPonto";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stAcao = $request->get('stAcao');

switch ($stAcao) {
    case "emitir":
        switch ($_REQUEST["stTipoFiltro"]) {
            case "contrato_todos":
            case "cgm_contrato_todos":
                include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
                foreach (Sessao::read("arContratos") as $arContrato) {
                    $stCodContratos .= $arContrato["cod_contrato"].",";
                }
                $stCodigos = substr($stCodContratos,0,strlen($stCodContratos)-1);
                $stTipoFiltro = "contrato";
                break;
            case "lotacao_grupo":
                $stCodigos = implode(",",$_POST["inCodLotacaoSelecionados"]);
                $stTipoFiltro = "lotacao";
                break;
            case "local_grupo":
                $stCodigos = implode(",",$_POST["inCodLocalSelecionados"]);
                $stTipoFiltro = "local";
                break;
            case "sub_divisao_funcao_grupo":
                $stCodigosRegime      = implode(",",$_POST["inCodRegimeSelecionadosFunc"]);
                $stCodigosSubDivisao  = implode(",",$_POST["inCodSubDivisaoSelecionadosFunc"]);
                if ($_POST["inCodFuncaoSelecionados"]) {
                    $stCodigosFuncao      = implode(",",$_POST["inCodFuncaoSelecionados"]);
                }
                $stTipoFiltro = "sub_divisao_funcao";
                break;
        }

        $preview = new PreviewBirt(4,51,4);
        $preview->setVersaoBirt( '2.5.0' );
        $preview->setReturnURL( CAM_GRH_PON_INSTANCIAS."relatorios/FLRelatorioEspelhoPonto.php");
        $preview->setTitulo('Espelho Ponto');
        $preview->setNomeArquivo('espelhoPonto');
        $preview->addParametro("entidade",Sessao::getCodEntidade($boTransacao));
        $preview->addParametro("stEntidade",Sessao::getEntidade());
        $preview->addParametro("stTipoFiltro", $stTipoFiltro);
        $preview->addParametro("stCodigos", $stCodigos);
        $preview->addParametro("stCodigosRegime", $stCodigosRegime);
        $preview->addParametro("stCodigosSubDivisao", $stCodigosSubDivisao);
        $preview->addParametro("stCodigosFuncao", $stCodigosFuncao);
        $preview->addParametro("dtInicioPeriodo", $_REQUEST['dtInicioPeriodo']);
        $preview->addParametro("dtFimPeriodo", $_REQUEST['dtFimPeriodo']);
        $preview->addParametro("boOrdenacaoAlfabetica", $_REQUEST['boOrdenacaoAlfabetica']);
        $preview->addParametro("boAgrupar", ($_REQUEST['boAgrupar'] == 'true')?"1":"0");
        $preview->preview();
        break;
}

?>
