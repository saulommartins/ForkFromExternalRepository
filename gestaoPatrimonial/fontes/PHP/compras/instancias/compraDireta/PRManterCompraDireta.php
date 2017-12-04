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
    * Processamento
    * Data de CriaÃ§Ã£o   : 06/12/2006

    * @author Analista: Gelson
    * @author Desenvolvedor: Lucas Stephanou

    * @ignore

    * Casos de uso: uc-03.04.32

    $Id: PRManterCompraDireta.php 59612 2014-09-02 12:00:51Z gelson $

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once TCOM."TComprasCompraDireta.class.php";
include_once TCOM."TComprasCompraDiretaProcesso.class.php";
include_once TCOM."TComprasMapaSolicitacao.class.php";
include_once TCOM."TComprasSolicitacao.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterCompraDireta";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";
$pgJS      = "JS".$stPrograma.".js";

Sessao::setTrataExcecao(true);
$obErro = new Erro;

function validarDatas($entrega, $validade, $dtCompraDireta)
{
    if ($entrega && $validade) {
        $arDataEntrega = explode('/',$entrega);
        $arDataValidade = explode('/',$validade);
        $arDataCompraDireta = explode("/", $dtCompraDireta);

        $inDataEntrega  = $arDataEntrega[2] . $arDataEntrega[1] . $arDataEntrega[0];
        $inDataValidade = $arDataValidade[2] . $arDataValidade[1] . $arDataValidade[0];
        $inDataCompraDireta = $arDataCompraDireta[2].$arDataCompraDireta[1].$arDataCompraDireta[0];

        if ($inDataEntrega < $inDataCompraDireta) {
            return 'A data de entrega deve ser igual ou posterior a data da compra direta.';
        } else {
            if ($inDataValidade < $inDataEntrega) {
                return 'A data de validade deve ser maior ou igual a data de entrega.';
            }
        }

        return false;
    }

    return true;
}

function geraArquivoXML($inCodCompraDireta)
{
    include_once(TCOM."TComprasModalidade.class.php");
    include_once(CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php");
    include_once(TCGM."TCGM.class.php");
    include_once(CAM_GP_COM_MAPEAMENTO.'TComprasMapaItem.class.php');
    include_once(CAM_FW_XML."domxml.php");
    include_once(CLA_ARQUIVO_ZIP);

    $obTComprasModalidade = new TComprasModalidade;
    $obTComprasModalidade->setDado( "cod_modalidade", $_REQUEST["inCodModalidade"] );
    $obTComprasModalidade->recuperaPorChave( $rsModalidade );

    $obTOrcamentoEntidade = new TOrcamentoEntidade;
    $obTOrcamentoEntidade->setDado( "cod_entidade", $_REQUEST["inCodEntidade"] );
    $obTOrcamentoEntidade->setDado( "exercicio"   , Sessao::getExercicio() );
    $obTOrcamentoEntidade->recuperaRelacionamentoNomes( $rsEntidade );

    $obTCGM = new TCGM;
    $obTCGM->recuperaRelacionamento( $rsCgmEntidade, " where CGM.numcgm = ".$rsEntidade->getCampo("numcgm") );

    list( $inCodMapa, $inExercicioMapa ) = explode( "/", $_REQUEST["stMapaCompras"] );

    $obTComprasMapaItem = new TComprasMapaItem();
    $obTComprasMapaItem->setDado( 'exercicio', $inExercicioMapa );
    $obTComprasMapaItem->setDado( 'cod_mapa' , $inCodMapa );
    $obTComprasMapaItem->recuperaItensCompraDireta( $rsMapaItens );

    $domDocument = domxml_open_mem( "<?xml version='1.0' encoding='UTF-8' ?><urbem/>" );

    $eleCompra = $domDocument->create_element("compra");
    $domDocument->replace_child($eleCompra,$domDocument->first_child());

    $eleCodCompra = $domDocument->create_element("codigo");
    $eleCodCompra = $eleCompra->append_child($eleCodCompra);

    $nodCodCompra = $domDocument->create_text_node( $inCodCompraDireta );
    $nodCodCompra = $eleCodCompra->append_child($nodCodCompra);

    $eleModalidade = $domDocument->create_element("modalidade");
    $eleModalidade = $eleCompra->append_child($eleModalidade);

    $eleCodModalidade = $domDocument->create_element("codigo");
    $eleCodModalidade = $eleModalidade->append_child($eleCodModalidade);

    $nodCodModalidade = $domDocument->create_text_node( $_REQUEST["inCodModalidade"] );
    $nodCodModalidade = $eleCodModalidade->append_child($nodCodModalidade);

    $eleDescModalidade = $domDocument->create_element("descricao");
    $eleDescModalidade = $eleModalidade->append_child($eleDescModalidade);

    $nodDescModalidade = $domDocument->create_text_node( utf8_encode($rsModalidade->getCampo("descricao")) );
    $nodDescModalidade = $eleDescModalidade->append_child($nodDescModalidade);

    $eleEntidade = $domDocument->create_element("entidade");
    $eleEntidade = $eleCompra->append_child($eleEntidade);

    $eleCodEntidade = $domDocument->create_element("codigo");
    $eleCodEntidade = $eleEntidade->append_child($eleCodEntidade);

    $nodCodEntidade = $domDocument->create_text_node( $_REQUEST["inCodEntidade"] );
    $nodCodEntidade = $eleCodEntidade->append_child($nodCodEntidade);

    $eleExercicioEntidade = $domDocument->create_element("exercicio");
    $eleExercicioEntidade = $eleEntidade->append_child($eleExercicioEntidade);

    $nodExercicioEntidade = $domDocument->create_text_node( Sessao::getExercicio() );
    $nodExercicioEntidade = $eleExercicioEntidade->append_child($nodExercicioEntidade);

    $eleNomeEntidade = $domDocument->create_element("nome");
    $eleNomeEntidade = $eleEntidade->append_child($eleNomeEntidade);

    $nodNomeEntidade = $domDocument->create_text_node( utf8_encode( $rsEntidade->getCampo("entidade") ) );
    $nodNomeEntidade = $eleNomeEntidade->append_child($nodNomeEntidade);

    $eleCidadeEntidade = $domDocument->create_element("cidade");
    $eleCidadeEntidade = $eleEntidade->append_child($eleCidadeEntidade);

    $nodCidadeEntidade = $domDocument->create_text_node( utf8_encode( $rsCgmEntidade->getCampo("nom_municipio") ) );
    $nodCidadeEntidade = $eleCidadeEntidade->append_child($nodCidadeEntidade);

    $eleEstadoEntidade = $domDocument->create_element("estado");
    $eleEstadoEntidade = $eleEntidade->append_child($eleEstadoEntidade);

    $nodEstadoEntidade = $domDocument->create_text_node( utf8_encode( $rsCgmEntidade->getCampo("nom_uf") ) );
    $nodEstadoEntidade = $eleEstadoEntidade->append_child($nodEstadoEntidade);

    $eleMapa = $domDocument->create_element("mapa");
    $eleMapa = $eleCompra->append_child($eleMapa);

    $eleCodMapa = $domDocument->create_element("codigo");
    $eleCodMapa = $eleMapa->append_child($eleCodMapa);

    $nodCodMapa = $domDocument->create_text_node( $inCodMapa );
    $nodCodMapa = $eleCodMapa->append_child($nodCodMapa);

    $eleCodMapa = $domDocument->create_element("exercicio");
    $eleCodMapa = $eleMapa->append_child($eleCodMapa);

    $nodCodMapa = $domDocument->create_text_node( $inExercicioMapa );
    $nodCodMapa = $eleCodMapa->append_child($nodCodMapa);

    $eleItens = $domDocument->create_element("itens");
    $eleItens = $eleMapa->append_child($eleItens);

    for ( $i=0; $i<count($rsMapaItens->arElementos); $i++ ) {

        $eleItem = $domDocument->create_element("item");
        $eleItem = $eleItens->append_child($eleItem);
        $eleItem->set_attribute( "id", $i+1 );

        $eleCodItem = $domDocument->create_element("codigo");
        $eleCodItem = $eleItem->append_child($eleCodItem);

        $nodCodItem = $domDocument->create_text_node( $rsMapaItens->getCampo('cod_item') );
        $nodCodItem = $eleCodItem->append_child($nodCodItem);

        $eleCodCentro = $domDocument->create_element("cod_centro");
        $eleCodCentro = $eleItem->append_child($eleCodCentro);

        $nodCodCentro = $domDocument->create_text_node( $rsMapaItens->getCampo('cod_centro') );
        $nodCodCentro = $eleCodCentro->append_child($nodCodCentro);

        $eleExercicioSolicitacao = $domDocument->create_element("exercicio_solicitacao");
        $eleExercicioSolicitacao = $eleItem->append_child($eleExercicioSolicitacao);

        $nodExercicioSolicitacao = $domDocument->create_text_node( $rsMapaItens->getCampo('exercicio_solicitacao') );
        $nodExercicioSolicitacao = $eleExercicioSolicitacao->append_child($nodExercicioSolicitacao);

        $eleCodSolicitacao = $domDocument->create_element("cod_solicitacao");
        $eleCodSolicitacao = $eleItem->append_child($eleCodSolicitacao);

        $nodCodSolicitacao = $domDocument->create_text_node( $rsMapaItens->getCampo('cod_solicitacao') );
        $nodCodSolicitacao = $eleCodSolicitacao->append_child($nodCodSolicitacao);

        $eleLote = $domDocument->create_element("lote");
        $eleLote = $eleItem->append_child($eleLote);

        $nodLote = $domDocument->create_text_node( $rsMapaItens->getCampo('lote') );
        $nodLote = $eleLote->append_child($nodLote);

        $eleDescItem = $domDocument->create_element("descricao_resumida");
        $eleDescItem = $eleItem->append_child($eleDescItem);

        $nodDescItem = $domDocument->create_text_node( utf8_encode( $rsMapaItens->getCampo('descricao_resumida') ) );
        $nodDescItem = $eleDescItem->append_child($nodDescItem);

        $eleQuantidadeItem = $domDocument->create_element("quantidade");
        $eleQuantidadeItem = $eleItem->append_child($eleQuantidadeItem);

        $nuQuantidadeItem = str_replace( '.', ',',$rsMapaItens->getCampo('quantidade') );
        $nodQuantidadeItem = $domDocument->create_text_node( $nuQuantidadeItem );
        $nodQuantidadeItem = $eleQuantidadeItem->append_child($nodQuantidadeItem);

        $eleUnidadeItem = $domDocument->create_element("unidade");
        $eleUnidadeItem = $eleItem->append_child($eleUnidadeItem);

        $nodUnidadeItem = $domDocument->create_text_node( utf8_encode( $rsMapaItens->getCampo('nom_unidade') ) );
        $nodUnidadeItem = $eleUnidadeItem->append_child($nodUnidadeItem);

        $rsMapaItens->proximo();
    }

    $nomeArquivoZip = "cotacao_".$inCodCompraDireta.$_REQUEST['inCodEntidade'].$inExercicioMapa.$_REQUEST['inCodModalidade'].".zip";

    $stNomeArquivoCotacao = "cotacao_".$inCodCompraDireta.$_REQUEST['inCodEntidade'].$inExercicioMapa.$_REQUEST['inCodModalidade'].".xml";
    $stCaminhoArquivoCotacao = CAM_FRAMEWORK."tmp/";
    $nome_completo_cotacao = $stCaminhoArquivoCotacao.$stNomeArquivoCotacao;

    $stNomeProgramaExportador = "siamProposta.jar";

    $stNomeHelpProgramaExportador = "LEIAME.pdf";

    $stCaminhoProgramaExportador = CAM_GP_JAVA."propostaFornecedor/";

    $nome_completo_programa = $stCaminhoProgramaExportador.$stNomeProgramaExportador;

    $nome_completo_help_programa = $stCaminhoProgramaExportador.$stNomeHelpProgramaExportador;

    $domDocument->dump_file($nome_completo_cotacao, false, true);

    chmod($nome_completo_cotacao,0777);

    $obArquivoZip = new ArquivoZip;
    $obArquivoZip->AdicionarArquivo($nome_completo_cotacao, $stNomeArquivoCotacao,$stCaminhoArquivoCotacao,0,false);
    $obArquivoZip->AdicionarArquivo($nome_completo_programa, $stNomeProgramaExportador,$stCaminhoProgramaExportador,0,false);
    $obArquivoZip->AdicionarArquivo($nome_completo_help_programa, $stNomeHelpProgramaExportador,$stCaminhoProgramaExportador,0,false);

    $obArquivoZip->setNomeArquivoTmp($nomeArquivoZip);
    $obArquivoZip->FinalizaZip();

    chmod($obArquivoZip->getArquivoTmp(),0777);

    $obArquivoZip->Show();

    return $obArquivoZip->getArquivoTmp();
}

function verificaMapaAnulado($inCodMapa, $stExercicioMapa, $stAcao)
{
    $stErro = null;
    if ($stAcao == 'alterar' || $stAcao == 'incluir') {
        include_once CAM_GP_COM_MAPEAMENTO."TComprasMapa.class.php";
    $stFiltro = "";
        $obTComprasMapa = new TComprasMapa;
        $obTComprasMapa->setDado('exercicio', $stExercicioMapa);
        $obTComprasMapa->setDado('cod_mapa' , $inCodMapa);
        $obTComprasMapa->verificaMapaAnulacoes($rsRecordSet, $stFiltro);
        if ($rsRecordSet->getNumLinhas() > 0) {
            $stErro = "Mapa de Compras anulado (".$inCodMapa."/".$stExercicioMapa.")";
        }
    }

    return $stErro;
}

function verificaCompraDiretaAnulada($inCodModalidade, $stExercicioEntidade, $inCodEntidade, $inCodCompraDireta, $stAcao)
{
    if ($stAcao == "alterar" || $stAcao == "anular") {
        $obTCompraDireta = new TComprasCompraDireta;
        $stFiltro .= " WHERE                                                                                                      \n";
        $stFiltro .= "       compra_direta.cod_entidade       =  ".$inCodEntidade."                                               \n";
        $stFiltro .= "   AND compra_direta.exercicio_entidade = '".$stExercicioEntidade."'                                        \n";
        $stFiltro .= "   AND compra_direta.cod_modalidade     =  ".$inCodModalidade."                                             \n";
        $stFiltro .= "   AND compra_direta.cod_compra_direta  =  ".$inCodCompraDireta."                                           \n";
        $stFiltro .= "   AND NOT EXISTS ( SELECT  1                                                                               \n";
        $stFiltro .= "                      FROM  compras.compra_direta_anulacao                                                  \n";
        $stFiltro .= "                     WHERE  compra_direta_anulacao.cod_modalidade = compra_direta.cod_modalidade            \n";
        $stFiltro .= "                       AND  compra_direta_anulacao.exercicio_entidade = compra_direta.exercicio_entidade    \n";
        $stFiltro .= "                       AND  compra_direta_anulacao.cod_entidade = compra_direta.cod_entidade                \n";
        $stFiltro .= "                       AND  compra_direta_anulacao.cod_compra_direta = compra_direta.cod_compra_direta      \n";
        $stFiltro .= "                  )                                                                                         \n";
        $obTCompraDireta->recuperaCompraDireta($rsRecordSet, $stFiltro);

        if ($rsRecordSet->getNumLinhas() <= 0) {
            if ($stAcao == "alterar") {
                $stErro = "Compra direta anulada (".$inCodCompraDireta.")";
            }
            if ($stAcao == "anular") {
                $stErro = "A compra direta (".$inCodCompraDireta.") já está anulada.";
            }
        }
    }

    return $stErro;
}

function verificaUtilizacaoMapa($inCodMapa, $stExercicioMapa)
{
    include_once CAM_GP_LIC_MAPEAMENTO."TLicitacaoLicitacao.class.php";
    $obTLicitacaoLicitacao = new TLicitacaoLicitacao;
    $stErro = "";

    $stFiltro  = " WHERE cod_mapa       =  ".$inCodMapa."                                                   \n";
    $stFiltro .= "   AND exercicio_mapa = '".$stExercicioMapa."'                                            \n";
    $stFiltro .= "   AND NOT EXISTS ( SELECT 1                                                              \n";
    $stFiltro .= "                      FROM licitacao.licitacao_anulada                                    \n";
    $stFiltro .= "                     WHERE licitacao_anulada.cod_licitacao  = licitacao.cod_licitacao     \n";
    $stFiltro .= "                       AND licitacao_anulada.cod_modalidade = licitacao.cod_modalidade    \n";
    $stFiltro .= "                       AND licitacao_anulada.cod_entidade   = licitacao.cod_entidade      \n";
    $stFiltro .= "                       AND licitacao_anulada.exercicio      = licitacao.exercicio      )  \n";
    $obTLicitacaoLicitacao->recuperaTodos($rsRecordSet, $stFiltro);

    if ($rsRecordSet->getNumLinhas() > 0) {
        $inCodMapa = $rsRecordSet->getCampo('cod_mapa');
        $stExercicioMapa = $rsRecordSet->getCampo('exercicio_mapa');
        $inCodLicitacao = $rsRecordSet->getCampo('cod_licitacao');
        $stExercicioLicitacao = $rsRecordSet->getCampo('exercicio');
        $stErro  = "Mapa de Compras (".$inCodMapa."/".$stExercicioMapa.") já está sendo utilizado ";
        $stErro .= "pela licitação (".$inCodLicitacao."/".$stExercicioLicitacao.").";
    }

    if (empty($stErro)) {
        include_once( CAM_GP_COM_MAPEAMENTO."TComprasMapaCotacao.class.php" );
        $obTComprasMapaCotacao = new TComprasMapaCotacao;
        $obTComprasMapaCotacao->setDado('cod_mapa'      , $inCodMapa      );
        $obTComprasMapaCotacao->setDado('exercicio_mapa', $stExercicioMapa);
        $obTComprasMapaCotacao->recuperaPorChave($rsRecordSet);

        if ($rsRecordSet->getNumLinhas() > 0) {
            include_once( CAM_GP_COM_MAPEAMENTO."TComprasJulgamento.class.php" );
            $obTComprasJulgamento = new TComprasJulgamento;
            $obTComprasJulgamento->setDado('exercicio'  , $rsRecordSet->getCampo('exercicio_cotacao'));
            $obTComprasJulgamento->setDado('cod_cotacao', $rsRecordSet->getCampo('cod_cotacao')      );
            $obTComprasJulgamento->recuperaPorChave($rsRecordSet);

            if ($rsRecordSet->getNumLinhas() > 0) {
                include_once( CAM_GP_COM_MAPEAMENTO."TComprasJulgamentoItem.class.php" );
                $obTComprasJulgamentoItem = new TComprasJulgamentoItem;
                $obTComprasJulgamentoItem->setDado('exercicio'  , $rsRecordSet->getCampo('exercicio')   );
                $obTComprasJulgamentoItem->setDado('cod_cotacao', $rsRecordSet->getCampo('cod_cotacao') );
                $obTComprasJulgamentoItem->recuperaPorChave($rsRecordSet);

                if ($rsRecordSet->getNumLinhas() > 0) {
                    include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoItemPreEmpenhoJulgamento.class.php" );
                    $obTEmpenhoItemPreEmpenhoJulgamento = new TEmpenhoItemPreEmpenhoJulgamento;
                    $stFiltro  = " WHERE exercicio_julgamento = '".$rsRecordSet->getCampo('exercicio')."'";
                    $stFiltro .= "   AND cod_cotacao          =  ".$rsRecordSet->getCampo('cod_cotacao');
                    $stFiltro .= "   AND cod_item             =  ".$rsRecordSet->getCampo('cod_item');
                    $stFiltro .= "   AND lote                 =  ".$rsRecordSet->getCampo('lote');
                    $stFiltro .= "   AND cgm_fornecedor       =  ".$rsRecordSet->getCampo('cgm_fornecedor');
                    $obTEmpenhoItemPreEmpenhoJulgamento->recuperaTodos($rsRecordSet, $stFiltro);

                    if ($rsRecordSet->getNumLinhas() > 0) {
                        $stErro = "Este mapa não pode ser mais utilizado.";
                    }
                }
            }
        }
        if (empty($stErro)) {
            include_once CAM_GP_COM_MAPEAMENTO."TComprasCompraDireta.class.php";
            $obTComprasCompraDireta = new TComprasCompraDireta;
            $obTComprasCompraDireta->setDado('cod_mapa'       , $inCodMapa);
            $obTComprasCompraDireta->setDado('exercicio_mapa' , $stExercicioMapa);
            $obTComprasCompraDireta->recuperaCompraDiretaPorMapa($rsRecordSet);

            if (($rsRecordSet->getNumLinhas() > 0) && ($rsRecordSet->getCampo('cod_compra_direta') != $_REQUEST['inCodCompraDireta'])) {
                $stErro = "Mapa de compras (".$inCodMapa."/".$stExercicioMapa.")  já utilizado por outra compra direta.";
            }
        }
    }

    return $stErro;
}

$obCompraDireta = new TComprasCompraDireta();
Sessao::getTransacao()->setMapeamento( $obCompraDireta );

switch ($_REQUEST['stAcao']) {

    case "incluir":
    Sessao::write('acao', 1720);

    $stErro = "";
    $stErro = validarDatas($_REQUEST['stDataEntregaProposta'], $_REQUEST['stDataValidade'], $_REQUEST['stDtCompraDireta']);
    if (isset($_REQUEST['inCodCompraDireta'])) {
           $obTComprasComprasDireta = new TComprasCompraDireta;
           $obTComprasComprasDireta->setDado('cod_compra_direta' , $_REQUEST['inCodCompraDireta']);
           $obTComprasComprasDireta->setDado('cod_entidade'      , $_REQUEST['inCodEntidade']   );
           $obTComprasComprasDireta->setDado('exercicio_entidade', Sessao::getExercicio()       );
           $obTComprasComprasDireta->setDado('cod_modalidade'    , $_REQUEST['inCodModalidade'] );

           $obTComprasComprasDireta->recuperaPorChave($rsRecordSet);

           if ($rsRecordSet->getNumLinhas() > 0) {
              $stErro = "O código de compra direta já existe, efetue a alteração.";
           }
    }

    if (empty($stErro)) {
        list ( $inCodMapa , $stExercicioMapa )  = explode ( '/' , $_REQUEST['stMapaCompras'] );
        $stErro = verificaUtilizacaoMapa($inCodMapa , $stExercicioMapa);
    }
    if (empty($stErro)) {
        $stErro = verificaMapaAnulado($inCodMapa , $stExercicioMapa, 'incluir');
    }
    if (empty($stErro)) {
        // Valida a data da compra direta que deve ser informada obrigatoriamente.
        if ( !empty($_REQUEST['stDtCompraDireta']) ) {
            // Não pode ser menor que a data da Ultima autorização.
            if (!SistemaLegado::comparaDatas($_REQUEST['stDtCompraDireta'], $_REQUEST['HdnDtCompraDireta'], true)) {
                $stErro = "A data da compra direta não pode ser menor que a data da última autorização (".$_REQUEST['HdnDtCompraDireta'].")";
            }
        } else
            $stErro = "A data da compra direta não pode ser vazia.";
    }

    if (empty($stErro)) {
        include_once( CAM_GP_COM_MAPEAMENTO."TComprasObjeto.class.php" );
        $rsObjeto                         = new RecordSet;
        $obTComprasObjeto                 = new TComprasObjeto();

        list ( $dia, $mes, $ano ) = explode("/", $_REQUEST['stDtCompraDireta']);
        $stDtCompraDireta = $ano."-".$mes."-".$dia;

    if (isset($_REQUEST['inCodCompraDireta'])) {
       $obCompraDireta->setDado('cod_compra_direta' , $_REQUEST['inCodCompraDireta']);
    }

        $obCompraDireta->setDado( 'cod_entidade' , $_REQUEST['inCodEntidade'] );
        $obCompraDireta->setDado( 'exercicio_entidade' , $stExercicioMapa ) ;
        $obCompraDireta->setDado( 'cod_modalidade' , $_REQUEST['inCodModalidade'] );
        $obCompraDireta->setDado( 'cod_tipo_objeto' , $_REQUEST['inCodTipoObjeto'] );
        $obCompraDireta->setDado( 'cod_objeto' , $_REQUEST['hdnObjeto'] );
        $obCompraDireta->setDado( 'exercicio_mapa' , $stExercicioMapa );
        $obCompraDireta->setDado( 'cod_mapa' , $inCodMapa );
        $obCompraDireta->setDado( 'dt_entrega_proposta' , $_REQUEST['stDataEntregaProposta'] );
        $obCompraDireta->setDado( 'dt_validade_proposta' , $_REQUEST['stDataValidade'] );
        $obCompraDireta->setDado( 'condicoes_pagamento' , trim($_REQUEST['stCondicoesPagamento']));
        $obCompraDireta->setDado( 'prazo_entrega' , $_REQUEST['stPrazoEntrega']);
        $obCompraDireta->setDado( 'timestamp' , $stDtCompraDireta." ".date('H:i:s'));
        $obCompraDireta->inclusao();

    $inCodCompraDireta = $obCompraDireta->getDado('cod_compra_direta');

    if ($_REQUEST['stChaveProcesso'] != '') {
            $obTComprasCompraDiretaProcesso = new TComprasCompraDiretaProcesso();
            $obTComprasCompraDiretaProcesso->setDado('cod_compra_direta'  , $obCompraDireta->getDado('cod_compra_direta'));
            $obTComprasCompraDiretaProcesso->setDado('cod_entidade'	  , $obCompraDireta->getDado('cod_entidade'));
        $obTComprasCompraDiretaProcesso->setDado('exercicio_entidade' , $obCompraDireta->getDado('exercicio_entidade'));
        $obTComprasCompraDiretaProcesso->setDado('cod_modalidade'     , $obCompraDireta->getDado('cod_modalidade'));
        $stProcesso = explode ("/", $_REQUEST['stChaveProcesso']);
        $obTComprasCompraDiretaProcesso->setDado('cod_processo'	  , $stProcesso[0]);
        $obTComprasCompraDiretaProcesso->setDado('exercicio_processo' , $stProcesso[1]);
        $obTComprasCompraDiretaProcesso->inclusao();
    }

        $obTComprasMapaSolicitacao = new TComprasMapaSolicitacao();
        $obTComprasMapaSolicitacao->setDado('cod_mapa',	$inCodMapa);
        $obTComprasMapaSolicitacao->setDado('exercicio',$stExercicioMapa);
        $obTComprasMapaSolicitacao->recuperaPorChave( $rsSolicitacaoMapa );

        $soma = 0;

        $obTComprasSolicitacao = new TComprasSolicitacao();

        //// somar o total de valores das solicitações do mapa para poder verificar se o limite não foi ultrapassado
        while (!$rsSolicitacaoMapa->eof()) {
            $obTComprasSolicitacao->setDado('cod_solicitacao',$rsSolicitacaoMapa->getCampo('cod_solicitacao'));
            $obTComprasSolicitacao->setDado('cod_entidade',$rsSolicitacaoMapa->getCampo('cod_entidade'));
            $obTComprasSolicitacao->setDado('exercicio',$rsSolicitacaoMapa->getCampo('exercicio'));
            $obTComprasSolicitacao->recuperaValoresTotaisSolicitacao($rsValoresSolicitacao);

            $soma = $soma + $rsValoresSolicitacao->getCampo("total");

            $rsSolicitacaoMapa->proximo();
        }

        switch ($_REQUEST['stDocumentoFornecedor']) {
            case "imprimir":
                include_once(TCOM."TComprasMapa.class.php");

                $obCompraDiretas = new TComprasCompraDireta();
                $obCompraMapas = new TComprasMapa();

                $stFiltro  = " where cod_compra_direta  = ".$obCompraDireta->getDado('cod_compra_direta');
                $stFiltro .= "   and cod_entidade       = ".$_REQUEST["inCodEntidade"];
                $stFiltro .= "   and cod_modalidade     = ".$_REQUEST["inCodModalidade"];
                $stFiltro .= "   and exercicio_entidade = '".Sessao::getExercicio()."'";

                $obCompraDiretas->recuperaTodos($rsCompraDiretas, $stFiltro);
                unset($stFiltro);

                $stFiltro = " where cod_mapa = ".$rsCompraDiretas->getCampo('cod_mapa');
                $stFiltro .= " and exercicio = '".Sessao::getExercicio()."'";
                $obCompraMapas->recuperaTodos($rsCompraMapas, $stFiltro);
                $inTipoCotacao = $rsCompraMapas->getCampo('cod_tipo_licitacao');
                unset($stFiltro);

                $stFiltro  = "&inCodCompraDireta=".$obCompraDireta->getDado('cod_compra_direta');
                $stFiltro .= "&inCodEntidade=".$_REQUEST["inCodEntidade"];
                $stFiltro .= "&inCodModalidade=".$_REQUEST["inCodModalidade"];
                $stFiltro .= "&stDtEmissao=".$_REQUEST['stDtCompraDireta'];
                $stFiltro .= "&inCodTipoCotacao=".$inTipoCotacao;

                SistemaLegado::alertaAviso("FMPreviewCompraDireta.php?".Sessao::getId().$stFiltro,"Compra Direta ".$obCompraDireta->getDado('cod_compra_direta')."","incluir","incluir_n", Sessao::getId(), "../");
            break;
            case "xml":
                $stCaminhoArquivoXML = geraArquivoXML($inCodCompraDireta);
                SistemaLegado::alertaAviso(  "LSArquivosManterCompraDireta.php?".Sessao::getId()."&stCaminhoArquivoXML=".$stCaminhoArquivoXML ,"Compra Direta ".$obCompraDireta->getDado('cod_compra_direta')."","incluir","incluir_n", Sessao::getId(), "../");
            break;
            case "nao":
                SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=incluir","Compra Direta ".$obCompraDireta->getDado('cod_compra_direta')."","incluir","incluir_n", Sessao::getId(), "../");
            break;
        }

        if ( ($_REQUEST['inCodTipoObjeto'] == 1) || ($_REQUEST['inCodTipoObjeto'] == 2) ) {
            $mensagemAlertaLimite = verficaCompraLimiteEspecieModalidade($_REQUEST['inCodModalidade'],$soma, $_REQUEST['inCodTipoObjeto']);

            if ($mensagemAlertaLimite != "") {
                        SistemaLegado::exibeAviso($mensagemAlertaLimite." (Compra Direta ".$obCompraDireta->getDado('cod_compra_direta').") ","aviso","aviso",Sessao::getId(), "../");
            }
        }
    } else {
        SistemaLegado::exibeAviso(urlencode($stErro), "n_incluir", "erro" );
    }

    break;

    case "alterar":
        Sessao::write('acao', 1721);
        // validar datas
        $stErro = validarDatas($_REQUEST['stDataEntregaProposta'] , $_REQUEST['stDataValidade'], $_REQUEST['HdnDtCompraDireta']);

        if (empty($stErro)) {
            list ( $inCodMapa , $stExercicioMapa )  = explode ( '/' , $_REQUEST['stMapaCompras'] );
            $stErro = verificaUtilizacaoMapa($inCodMapa , $stExercicioMapa);
        }
        if (empty($stErro)) {
            $inCodModalidade     = $_REQUEST['inCodModalidade'];
            $stExercicioEntidade = $stExercicioMapa;
            $inCodEntidade       = $_REQUEST['inCodEntidade'];
            $inCodCompraDireta   = $_REQUEST['inCodCompraDireta'];

            $stErro = verificaCompraDiretaAnulada($inCodModalidade, $stExercicioEntidade, $inCodEntidade, $inCodCompraDireta, 'alterar');
        }
        if (empty($stErro)) {
            $stErro = verificaMapaAnulado($inCodMapa , $stExercicioMapa, 'alterar');
        }
        if (!$stErro) {
        // objeto
        $obCompraDireta->setDado( 'cod_compra_direta' , $_REQUEST['inCodCompraDireta'] );
        $obCompraDireta->setDado( 'cod_entidade' , $_REQUEST['inCodEntidade'] );
        $obCompraDireta->setDado( 'exercicio_entidade' , $stExercicioMapa ) ;
        $obCompraDireta->setDado( 'cod_modalidade' , $_REQUEST['inCodModalidade'] );
        $obCompraDireta->setDado( 'cod_tipo_objeto' , $_REQUEST['inCodTipoObjeto'] );
        $obCompraDireta->setDado( 'cod_objeto' , $_REQUEST['hdnObjeto'] );
        $obCompraDireta->setDado( 'exercicio_mapa' , $stExercicioMapa );
        $obCompraDireta->setDado( 'cod_mapa' , $inCodMapa );
        $obCompraDireta->setDado( 'dt_entrega_proposta' , $_REQUEST['stDataEntregaProposta'] );
        $obCompraDireta->setDado( 'dt_validade_proposta' , $_REQUEST['stDataValidade'] );
        $obCompraDireta->setDado( 'condicoes_pagamento' , trim($_REQUEST['stCondicoesPagamento']));
        $obCompraDireta->setDado( 'prazo_entrega' , $_REQUEST['stPrazoEntrega']);
        $obCompraDireta->alteracao();

        if ($_REQUEST['stChaveProcesso'] != '') {
            $obTComprasCompraDiretaProcesso = new TComprasCompraDiretaProcesso();
            $obTComprasCompraDiretaProcesso->setDado('cod_compra_direta'  , $obCompraDireta->getDado('cod_compra_direta'));
            $obTComprasCompraDiretaProcesso->setDado('cod_entidade'	      , $obCompraDireta->getDado('cod_entidade'));
            $obTComprasCompraDiretaProcesso->setDado('exercicio_entidade' , $obCompraDireta->getDado('exercicio_entidade'));
            $obTComprasCompraDiretaProcesso->setDado('cod_modalidade'     , $obCompraDireta->getDado('cod_modalidade'));
            $obTComprasCompraDiretaProcesso->recuperaTodos($rsCompraDiretaProcesso);
            $stProcesso = explode ("/", $request->get('stChaveProcesso'));
            $obTComprasCompraDiretaProcesso->setDado('cod_processo'	      , $stProcesso[0]);
            $obTComprasCompraDiretaProcesso->setDado('exercicio_processo' , $stProcesso[1]);

            if ($rsCompraDiretaProcesso->inNumLinhas > 0) {
            $obTComprasCompraDiretaProcesso->alteracao();
            } else {
            $obTComprasCompraDiretaProcesso->inclusao();
            }
        }

            switch ($_REQUEST['stDocumentoFornecedor']) {
                case "imprimir":
                    include_once(TCOM."TComprasMapa.class.php");

                $obCompraDiretas = new TComprasCompraDireta();
                $obCompraMapas = new TComprasMapa();

                $stFiltro  = " where cod_compra_direta =".$_REQUEST['inCodCompraDireta'];
                $stFiltro .= " and cod_entidade =".$_REQUEST["inCodEntidade"];
                $stFiltro .= " and cod_modalidade =".$_REQUEST["inCodModalidade"];
                $stFiltro .= " and exercicio_entidade =".Sessao::getExercicio()."::varchar";
                $obCompraDiretas->recuperaTodos($rsCompraDiretas, $stFiltro);
                unset($stFiltro);

                $stFiltro = "where cod_mapa =".$rsCompraDiretas->getCampo('cod_mapa');
                $stFiltro .= "and exercicio =".Sessao::getExercicio()."::varchar";
                $obCompraMapas->recuperaTodos($rsCompraMapas, $stFiltro);
                $inTipoCotacao = $rsCompraMapas->getCampo('cod_tipo_licitacao');
                unset($stFiltro);

                $stFiltro  = "&inCodCompraDireta=".$_REQUEST["inCodCompraDireta"];
                $stFiltro .= "&inCodEntidade=".$_REQUEST["inCodEntidade"];
                $stFiltro .= "&inCodModalidade=".$_REQUEST["inCodModalidade"];
                $stFiltro .= "&stDtEmissao=".$_REQUEST['stDtEmissao'];
                $stFiltro .= "&inCodTipoCotacao=".$inTipoCotacao;

                SistemaLegado::alertaAviso("FMPreviewCompraDireta.php?".Sessao::getId().$stFiltro,"Compra Direta ".$obCompraDireta->getDado('cod_compra_direta')."","incluir","incluir_n", Sessao::getId(), "../");
                break;
                case "xml":
                    $stCaminhoArquivoXML = geraArquivoXML($_REQUEST['inCodCompraDireta']);
                      SistemaLegado::alertaAviso(  "LSArquivosManterCompraDireta.php?".Sessao::getId()."&stCaminhoArquivoXML=".$stCaminhoArquivoXML ,"Compra Direta ".$obCompraDireta->getDado('cod_compra_direta')."","incluir","incluir_n", Sessao::getId(), "../");
                break;
                case "nao":
                    SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=alterar","Compra Direta ".$obCompraDireta->getDado('cod_compra_direta')."","incluir","incluir_n", Sessao::getId(), "../");
                break;
            }
        } else {
            SistemaLegado::exibeAviso(urlencode($stErro), "n_incluir", "erro" );
        }

    break;

    case "anular":

        $obErro = new Erro;

        $inCodModalidade     = $_REQUEST['inCodModalidade'];
        $stExercicioEntidade = Sessao::getExercicio();
        $inCodEntidade       = $_REQUEST['inCodEntidade'];
        $inCodCompraDireta   = $_REQUEST['inCodCompraDireta'];

        $stMensagem = verificaCompraDiretaAnulada($inCodModalidade, $stExercicioEntidade, $inCodEntidade, $inCodCompraDireta, 'anular');

        if (!$stMensagem) {

            //Busca o nome da entidade
            include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEntidade.class.php';
            $obTOrcamentoEntidade = new TOrcamentoEntidade();
            $obTOrcamentoEntidade->setDado('exercicio', Sessao::getExercicio());
            $stFiltro = " and e.cod_entidade = '".$_REQUEST['inCodEntidade']."'";
            $obTOrcamentoEntidade->recuperaEntidades($rsEntidade, $stFiltro);

            //Verifica se a cotação é válida
            include_once CAM_GP_COM_MAPEAMENTO."TComprasMapa.class.php";
            $obTComprasMapa = new TComprasMapa;
            $obTComprasMapa->setDado('cod_mapa'       , $_REQUEST['hdnIdMapaCompras']        );
            $obTComprasMapa->setDado('exercicio_mapa' , $_REQUEST['hdnExercicioMapaCompras'] );
            $obTComprasMapa->recuperaMapaCotacaoValida($rsRecordSet);

            if ($rsRecordSet->getNumLinhas() == 1) {
                include_once CAM_GP_COM_MAPEAMENTO."TComprasJulgamento.class.php";
                $obTComprasJulgamento = new TComprasJulgamento;
                $stFiltro   = "    WHERE julgamento.cod_cotacao =  ".$rsRecordSet->getCampo('cod_cotacao');
                $stFiltro  .= "      AND julgamento.exercicio   = '".Sessao::getExercicio()."'";
                $stGroupBy  = " GROUP BY julgamento.exercicio                                                ";
                $stGroupBy .= "        , julgamento.cod_cotacao                                              ";
                $stGroupBy .= "        , julgamento.observacao                                               ";
                $stGroupBy .= "        , autorizacao_empenho.cod_autorizacao                                 ";
                $stGroupBy .= "        , autorizacao_empenho.exercicio                                       ";
                $stGroupBy .= "        , autorizacao_anulada.dt_anulacao                                     ";
                $stGroupBy .= "        , autorizacao_anulada.motivo                                          ";
                $stOrder    = " ORDER BY autorizacao_empenho.cod_autorizacao                                 ";
                $obTComprasJulgamento->recuperaJulgamentoAutorizacao($rsRecordSet, $stFiltro, $stGroupBy, $stOrder);

                $inCount = 0;

                while (!$rsRecordSet->eof()) {
                    if ($rsRecordSet->getCampo('dt_anulacao') == "") {
                        $cod_autorizacao .= $rsRecordSet->getCampo('cod_autorizacao')."/";
                        $cod_autorizacao .= $rsRecordSet->getCampo('autorizacao_exercicio').",";
                        $inCount++;
                        $boAnula = true;
                    }
                    $rsRecordSet->proximo();
                }

                if ($boAnula) {
                    $cod_autorizacao = substr($cod_autorizacao, 0, -1) ;
                    if ($inCount > 1) {
                        $stMensagem = "As Autorizações (".$cod_autorizacao.") ";
                        $stMensagem.= "da entidade '".$rsEntidade->getCampo('nom_cgm')."', devem ser anuladas ";
                        $stMensagem.= "antes de anular a Compra Direta.";
                    } else {
                        $stMensagem = "A Autorização (".$cod_autorizacao.") ";
                        $stMensagem.= "da entidade '".$rsEntidade->getCampo('nom_cgm')."', deve ser anulada ";
                        $stMensagem.= "antes de anular a Compra Direta.";
                    }
                }
            }

            if (!$boAnula) {

                // Inclusão na tabela de Compra Direta Anulação.
                include_once TCOM."TComprasCompraDiretaAnulacao.class.php";
                $obCompraDiretaAnulacao = new TComprasCompraDiretaAnulacao;
                $obCompraDiretaAnulacao->setDado('cod_compra_direta'  , $_REQUEST['inCodCompraDireta']);
                $obCompraDiretaAnulacao->setDado('cod_entidade'       , $_REQUEST['inCodEntidade']);
                $obCompraDiretaAnulacao->setDado('exercicio_entidade' , Sessao::getExercicio());
                $obCompraDiretaAnulacao->setDado('cod_modalidade'     , $_REQUEST['inCodModalidade']);
                $obCompraDiretaAnulacao->setDado('motivo'             , trim($_REQUEST['stMotivoAnulacao']));
                $obCompraDiretaAnulacao->inclusao();

                // Força o teste para evitar erros.
                if (!empty($_REQUEST['hdnIdMapaCompras']) && !empty($_REQUEST['hdnExercicioMapaCompras'])) {

                    // Inclusão na tabela de Cotação Anulação.
                    include_once(TCOM."TComprasMapaCotacao.class.php");
                    $obTComprasMapaCotacao = new TComprasMapaCotacao;

                    $obTComprasMapaCotacao->setDado('cod_mapa', $_REQUEST['hdnIdMapaCompras']);
                    $obTComprasMapaCotacao->setDado('exercicio_mapa', $_REQUEST['hdnExercicioMapaCompras']);
                    $obTComprasMapaCotacao->recuperaPorChave( $rsComprasMapaCotacao );
                }

                /* Caso encontre uma ou mais cotação para o mesmo mapa, percorre para
                   verificar qual a cotação é válida (não anulada). */
                if ($rsComprasMapaCotacao->getNumLinhas() > 0) {

                    include_once(TCOM."TComprasCotacaoAnulada.class.php");

                    while ( !$rsComprasMapaCotacao->eof() ) {

                        // Verificação de cotações anuladas.
                        $obTComprasCotacaoAnulada = new TComprasCotacaoAnulada;
                        $obTComprasCotacaoAnulada->setDado('cod_cotacao', $rsComprasMapaCotacao->getCampo('cod_cotacao'));
                        $obTComprasCotacaoAnulada->setDado('exercicio', $rsComprasMapaCotacao->getCampo('exercicio_cotacao'));
                        $obTComprasCotacaoAnulada->recuperaPorChave( $rsComprasCotacaoAnulada );

                        // Se não existir nenhuma cotação anulada.
                        if ($rsComprasCotacaoAnulada->getNumLinhas() < 1) {
                            $inCodCotacao       = $rsComprasMapaCotacao->getCampo('cod_cotacao');
                            $stExercicioCotacao = $rsComprasMapaCotacao->getCampo('exercicio_cotacao');
                        }

                        $rsComprasMapaCotacao->proximo();
                    }

                    // Irá anular uma cotação válida que nunca foi anulada.
                    if (!empty($inCodCotacao)) {
                        $obTComprasCotacaoAnulada->setDado('cod_cotacao', $inCodCotacao);
                        $obTComprasCotacaoAnulada->setDado('exercicio', $stExercicioCotacao);

                        $stMotivo = "Anulação de compra direta nro. ".$_REQUEST['inCodCompraDireta']."/".Sessao::getExercicio()." da modalidade ".$_REQUEST['inCodModalidade']." da entidade ".$_REQUEST['inCodEntidade'];
                        $obTComprasCotacaoAnulada->setDado('motivo', "'$stMotivo'");

                        $obTComprasCotacaoAnulada->inclusao();
                    }
                }
            }
        }
        if ($stMensagem) {
            $obErro->setDescricao( '"'.$stMensagem.'"' );
        }

        if (!$obErro->ocorreu()) {
            SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=alterar","Compra Direta ".$_REQUEST['inCodCompraDireta']."","incluir","incluir_n", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }

        break;

    case "reemitir":
        // consultar compra direta
        $stFiltro  = " where compra_direta.cod_compra_direta=" . $_REQUEST['inCodCompraDireta']  ;
        $stFiltro .= " and compra_direta.cod_entidade = " . $_REQUEST['inCodEntidade']  ;
        $stFiltro .= " and compra_direta.cod_modalidade =" . $_REQUEST['inCodModalidade']  ;
        $stFiltro .= " and compra_direta.exercicio_entidade ='". Sessao::getExercicio() ."'";

        $boExiste  = SistemaLegado::pegaDado("cod_compra_direta", "compras.compra_direta", $stFiltro);

        if (!$boExiste) {
            SistemaLegado::alertaAviso("FLManterCompraDireta.php?".Sessao::getId()."&stAcao=" . substr($_REQUEST['stAcao'],0,strlen($_REQUEST['stAcao']))
            ,"Compra Direta ".$_REQUEST['inCodCompraDireta'] . " de Modalidade " . $_REQUEST['inCodModalidade']  . " da Entidade " . $_REQUEST['inCodEntidade'] ." não encontrada!"
            ,"erro"
            ,"erro_n"
            , Sessao::getId()
            , "../");
        } else {
            include_once(TCOM."TComprasMapa.class.php");

            $obCompraDiretas = new TComprasCompraDireta();
            $obCompraMapas = new TComprasMapa();

            $stFiltro  = " where cod_compra_direta =".$_REQUEST['inCodCompraDireta'];
                    $stFiltro .= " and cod_entidade =".$_REQUEST["inCodEntidade"];
                $stFiltro .= " and cod_modalidade =".$_REQUEST["inCodModalidade"];
            $stFiltro .= " and exercicio_entidade ='". Sessao::getExercicio() ."'";
            $obCompraDiretas->recuperaTodos($rsCompraDiretas, $stFiltro);
            unset($stFiltro);

            $stFiltro = "where cod_mapa =".$rsCompraDiretas->getCampo('cod_mapa');
            $stFiltro .= "and exercicio ='". Sessao::getExercicio() ."'";
            $obCompraMapas->recuperaTodos($rsCompraMapas, $stFiltro);
            $inTipoCotacao = $rsCompraMapas->getCampo('cod_tipo_licitacao');
            unset($stFiltro);

            $stFiltro  = "&inCodCompraDireta=".$_REQUEST["inCodCompraDireta"];
            $stFiltro .= "&inCodEntidade=".$_REQUEST["inCodEntidade"];
            $stFiltro .= "&inCodModalidade=".$_REQUEST["inCodModalidade"];
            $stFiltro .= "&stDtEmissao=".$_REQUEST['stDtEmissao'];
            $stFiltro .= "&inCodTipoCotacao=".$inTipoCotacao;

            SistemaLegado::alertaAviso("FMPreviewCompraDireta.php?".Sessao::getId().$stFiltro,"Reemitir Documento de Compra Direta","incluir","incluir_n", Sessao::getId(), "../");
        }
        break;
}

function verficaCompraLimiteEspecieModalidade($codModalidade,$soma, $especie)
{
    $mensagem = "";

    switch ($codModalidade) {
        //convite
        case 1:
            if ($especie == 1) {
                if ( ($soma >= 8000) && ($soma <= 80000) ) {
                    $mensagem = "";
                } else {
                    if ($soma > 80000) {
                        $mensagem = "A compra efetuada ultrapassa o limite máximo para essa Espécie e Modalidade!";
                    }
                }
            } elseif ($especie == 2) {
                if ( ($soma >= 15000) && ($soma <= 150000) ) {
                    $mensagem = "";
                } else {
                    if ($soma > 150000) {
                        $mensagem = "A compra efetuada ultrapassa o limite máximo para essa Espécie e Modalidade!";
                    }
                }
            }
        break;

        //tomada de preços
        case 2:
            if ($especie == 1) {
                if ( ($soma >= 80000) && ($soma <= 650000) ) {
                    $mensagem = "";
                } else {
                    if ($soma > 650000) {
                        $mensagem = "A compra efetuada ultrapassa o limite máximo para essa Espécie e Modalidade!";
                    }
                }
            } elseif ($especie == 2) {
                if ( ($soma >= 150000) && ($soma <= 1500000) ) {
                   $mensagem = "";
                } else {
                    if ($soma > 1500000) {
                        $mensagem = "A compra efetuada ultrapassa o limite máximo para essa Espécie e Modalidade!";
                    }
                }
            }
        break;

        //concorrência
        case 3:
            // Não há limites maximos, apenas minimos ( não necessário informar alertas ao usuário )!
            $mensagem = "";
        break;

        //dispença de licitação
        case 8:
            if ($especie == 1) {
                if ($soma < 8000) {
                    $mensagem = "";
                } else {
                    $mensagem = "A compra efetuada ultrapassa o limite máximo para essa Espécie e Modalidade!";
                }
            } elseif ($especie == 2) {
                if ($soma < 15000) {
                    $mensagem = "";
                } else {
                    $mensagem = "A compra efetuada ultrapassa o limite máximo para essa Espécie e Modalidade!";
                }
            }
        break;
    }

    return $mensagem;
}

Sessao::encerraExcecao();
?>
