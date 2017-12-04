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
    * Arquivo de Processamento para consignação Banrisul
    * Data de Criação: 09/06/2008

    * @author Alex Cardoso

    * Casos de uso: uc-04.08.27

    $Id: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAConsignacaoBanrisulRemuneracao.class.php");
include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAConsignacaoBanrisulLiquido.class.php");
include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php");

$obTIMAConsignacaoBanrisulRemuneracao = new TIMAConsignacaoBanrisulRemuneracao();
$obTIMAConsignacaoBanrisulLiquido = new TIMAConsignacaoBanrisulLiquido();
$obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
//Define o nome dos arquivos PHP
$stPrograma = "ConsignacaoBanrisul";
$pgFilt = "FL".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgOcul = "OC".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgJS   = "JS".$stPrograma.".js";

switch ($stAcao) {
    case "configurar":
        Sessao::setTrataExcecao(true);

        validaFiltros();

        $obTIMAConsignacaoBanrisulRemuneracao->excluirTodos();
        $obTIMAConsignacaoBanrisulLiquido->excluirTodos();

        foreach ($_POST["inCodEventoSelecionadosRemuneracao"] as $inCodEvento) {
            $obTIMAConsignacaoBanrisulRemuneracao->setDado("cod_evento",$inCodEvento);
            $obTIMAConsignacaoBanrisulRemuneracao->inclusao();
        }

        foreach ($_POST["inCodEventoSelecionadosLiquido"] as $inCodEvento) {
            $obTIMAConsignacaoBanrisulLiquido->setDado("cod_evento",$inCodEvento);
            $obTIMAConsignacaoBanrisulLiquido->inclusao();
        }

           Sessao::encerraExcecao();
        sistemaLegado::alertaAviso($pgForm,"Configuração da Consignação Banrisul concluída com sucesso!","incluir","aviso", Sessao::getId(), "../");
    break;
}

function validaFiltros()
{
    if(is_array($_REQUEST['inCodEventoSelecionadosRemuneracao']) &&
       sizeof($_REQUEST['inCodEventoSelecionadosRemuneracao']) > 0 ){

        $stFiltro = implode(",",$_REQUEST['inCodEventoSelecionadosRemuneracao']);
        $stFiltro = " WHERE cod_evento IN ($stFiltro) AND (natureza = 'P' OR natureza = 'B') ORDER BY codigo, natureza ";

        $rsEventos = new RecordSet();

        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php");
        $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
        $obTFolhaPagamentoEvento->recuperaTodos($rsEventos,$stFiltro);

        if ($rsEventos->getNumLinhas() > 0) {
            $inIndice = 0;
            while (!$rsEventos->eof()) {
                if ($inIndice == 0) {
                    $stNaturezaEventoReferencia = $rsEventos->getCampo('natureza');
                    $inIndice = 1;
                } elseif ($stNaturezaEventoReferencia != $rsEventos->getCampo('natureza')) {
                    $stMensagem = "A natureza dos eventos selecionados para o campo Eventos Remuneração deve ser unicamente de Proventos ou Bases. (Codigo Evento - ".$rsEventos->getCampo('codigo')."; Descrição - ".$rsEventos->getCampo('descricao')."; Natureza - ".($rsEventos->getCampo('natureza') == 'P'?"Provento":"Base").")!";
                    Sessao::getExcecao()->setDescricao($stMensagem);
                    break;
                }
                $rsEventos->proximo();
            }
        }
    }

    if(is_array($_REQUEST['inCodEventoSelecionadosLiquido']) &&
       sizeof($_REQUEST['inCodEventoSelecionadosLiquido']) > 0 ){

        $stFiltro = implode(",",$_REQUEST['inCodEventoSelecionadosLiquido']);
        $stFiltro = " WHERE cod_evento IN ($stFiltro) AND (natureza = 'D' OR natureza = 'B') ORDER BY codigo, natureza ";

        $rsEventos = new RecordSet();

        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php");
        $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
        $obTFolhaPagamentoEvento->recuperaTodos($rsEventos,$stFiltro);

        if ($rsEventos->getNumLinhas() > 0) {
            $inIndice = 0;
            while (!$rsEventos->eof()) {
                if ($inIndice == 0) {
                    $stNaturezaEventoReferencia = $rsEventos->getCampo('natureza');
                    $inIndice = 1;
                } elseif ($stNaturezaEventoReferencia != $rsEventos->getCampo('natureza')) {
                    $stMensagem = "A natureza dos eventos selecionados para o campo Eventos Líquido deve ser uniforme de Descontos ou de Bases. (Codigo Evento - ".$rsEventos->getCampo('codigo')."; Descrição - ".$rsEventos->getCampo('descricao')."; Natureza - ".($rsEventos->getCampo('natureza') == 'P'?"Provento":"Base").")!";
                    Sessao::getExcecao()->setDescricao($stMensagem);
                    break;
                }
                $rsEventos->proximo();
            }
        }
    }
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
