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
    * Arquivo de Relatório para exportação do PASEP
    * Data de Criação: 30/05/2008

    * @author Diego Lemos de Souza

    * Casos de uso: uc-04.08.23

    $Id: PRRelatorioPASEP.php 66258 2016-08-03 14:25:21Z evandro $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ExportarPASEP";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$arSessaoFiltroRelatorio = Sessao::read('filtroRelatorio');

switch ($arSessaoFiltroRelatorio["stTipoFiltro"]) {
    case "contrato_todos":
    case "cgm_contrato_todos":
        foreach (Sessao::read("arContratos") as $arContrato) {
            $stCodigos .= $arContrato["cod_contrato"].",";
        }
        $stCodigos = substr($stCodigos,0,strlen($stCodigos)-1);
        break;
    case "lotacao":
        $stCodigos = implode($arSessaoFiltroRelatorio["inCodLotacaoSelecionados"],",");
        break;
    case "local":
        $stCodigos = implode($arSessaoFiltroRelatorio["inCodLocalSelecionados"],",");
        break;
    case "atributo_servidor":
        $inCodAtributo = $arSessaoFiltroRelatorio["inCodAtributo"];
        $inCodCadastro = $arSessaoFiltroRelatorio["inCodCadastro"];
        $stNome = "Atributo_".$inCodAtributo."_".$inCodCadastro;
        if (is_array($arSessaoFiltroRelatorio[$stNome."_Selecionados"])) {
            $stCodigos = implode($arSessaoFiltroRelatorio[$stNome."_Selecionados"],",");
            $boAtributoMultiplo = 1;
        } else {
            $stCodigos = pg_escape_string($arSessaoFiltroRelatorio[$stNome]);
            $boAtributoMultiplo = 0;
        }
        break;
}

switch ($arSessaoFiltroRelatorio["inEtapaProcessamento"]) {
    case 1:
        $preview = new PreviewBirt(4,40,7);
        $preview->setPopup(true);
        $preview->setVersaoBirt("2.5.0");
        $preview->setTitulo('Conferência FPS900');
        $preview->setNomeArquivo('conferenciaFPS900');
        $preview->addParametro('stCodigos',$stCodigos);
        $preview->addParametro("inCodAtributo",    $arSessaoFiltroRelatorio["inCodAtributo"]);
        $preview->addParametro("boAtributoMultiplo",$boAtributoMultiplo);
        $preview->addParametro('stTipoFiltro', $arSessaoFiltroRelatorio["stTipoFiltro"]);
        break;
    case 2:
        $preview = new PreviewBirt(4,40,6);
        $preview->setPopup(true);
        $preview->setVersaoBirt("2.5.0");
        $preview->setTitulo('Erros FPS909');
        $preview->setNomeArquivo('errosPasep909');
        $preview->addParametro('errosPasep909',implode(",",$arSessaoFiltroRelatorio["arErrosRegistroHeader"]));
        break;
    case 3:
        if ($_GET["stRelatorio"] == "erro") {
            $preview = new PreviewBirt(4,40,10);
            $preview->setPopup(true);
            $preview->setVersaoBirt("2.5.0");
            $preview->setTitulo('Erros FPS910');
            $preview->setNomeArquivo('errosPasep910');
            $preview->addParametro('errosPasep910',implode(",",$arSessaoFiltroRelatorio["arErrosRegistroHeader"]));
        } else {
            $preview = new PreviewBirt(4,40,8);
            $preview->setPopup(true);
            $preview->setVersaoBirt("2.5.0");
            $preview->setTitulo('Conferência FPS910');
            $preview->setNomeArquivo('conferenciaFPS910');
        }
        break;
    case 4:
        // Recupera ultimo periodo de movimentacao
        include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoFolhaSituacao.class.php" );
        $obTFolhaSituacao = new TFolhaPagamentoFolhaSituacao;
        $obTFolhaSituacao->recuperaUltimaFolhaSituacao($rsPeriodoMovimentacao);

        // Recupera em qual folha foi pago o PASEP
        include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAPagamento910.class.php");
        $obTIMAPagamento910 = new TIMAPagamento910();
        $stFiltro = " WHERE cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
        $obTIMAPagamento910->recuperaTodos($rsPagamento910, $stFiltro);

        $preview = new PreviewBirt(4,40,9);
        $preview->setPopup(true);
        $preview->setVersaoBirt("2.5.0");
        $preview->setTitulo("Não Pagos FPS950");
        $preview->setNomeArquivo("naoPagosFPS950");
        $preview->addParametro("inFolhaPagamento", $rsPagamento910->getCampo("cod_tipo"));
        break;
    case 5:
        $preview = new PreviewBirt(4,40,12);
        $preview->setPopup(true);
        $preview->setVersaoBirt("2.5.0");
        $preview->setTitulo('Erros FPS959');
        $preview->setNomeArquivo('errosPasep959');
        $preview->addParametro('errosPasep959',implode(",",$arSessaoFiltroRelatorio["arErrosRegistroHeader"]));
        break;
    case 6:
        $preview = new PreviewBirt(4,40,11);
        $preview->setPopup(true);
        $preview->setVersaoBirt("2.5.0");
        $preview->setTitulo('Erros FPS952');
        $preview->setNomeArquivo('errosPasep952');
        $preview->addParametro('errosPasep952',implode(",",$arSessaoFiltroRelatorio["arErrosRegistroHeader"]));
        break;

}
$preview->addParametro('entidade', Sessao::getCodEntidade($boTransacao));
$preview->addParametro('stEntidade', Sessao::getEntidade());
$preview->addParametro('inEtapaProcessamento', $arSessaoFiltroRelatorio["inEtapaProcessamento"]);
$preview->preview();
?>
