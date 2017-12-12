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

    * @author Analista: Carlos Adriano
    * @author Desenvolvedor: Carlos Adriano
    * $Id:$ 
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once ( CAM_GP_COM_MAPEAMENTO."TComprasCompraDireta.class.php" );
include_once ( CAM_GP_COM_MAPEAMENTO."TComprasCompraDiretaHomologacao.class.php" );
include_once ( CAM_GP_COM_MAPEAMENTO."TComprasJulgamentoItem.class.php" );
include_once ( CAM_GP_COM_MAPEAMENTO."TComprasJustificativaRazao.class.php" );
require_once( CAM_GF_EMP_NEGOCIO    . "REmpenhoAutorizacaoEmpenho.class.php" );
require_once( CAM_GF_ORC_MAPEAMENTO . "TOrcamentoReservaSaldos.class.php"    );

//Define o nome dos arquivos PHP
$stPrograma = "ManterHomologacao";
$pgForm     = "FM".$stPrograma.".php";
$pgGera     = "OCGeraDocumentoHomologacao.php";
$obErro = new Erro();
Sessao::setTrataExcecao( true );

$stFiltro = " WHERE cod_compra_direta  = ".$_REQUEST["inCodLicitacao"]."
                AND cod_modalidade     = ".$_REQUEST["inCodModalidade"]."
                AND cod_entidade       = ".$_REQUEST["inCodEntidade"]."
                AND exercicio_entidade = '".$_REQUEST["stExercicioCompraDireta"]."' ";

$stMensagemErro = "";
if (SistemaLegado::comparaDatas($_REQUEST["stDtHomologacao"], date('d/m/Y'))) {
    $stMensagemErro = "A data da Homologação deve ser menor ou igual à data atual.";
}
$itensHomologacao = Sessao::read('itensHomologacao');

$arDatas = array();     
$itemCodCotacao = '';
foreach($itensHomologacao as $item){

    if($item['status'] == 'Homologado e Autorizado' && $itemCodCotacao != $item['codCotacao']){
        $obTLicitacaoHomologacao = new TComprasCompraDiretaHomologacao();
        $obTLicitacaoHomologacao->setDado('cod_cotacao', $item['codCotacao']);
        $obTLicitacaoHomologacao-> recuperaEmpenhoPreEmpenhoCotacao($rsDataEmpenho);

        $itemCodCotacao = $item['codCotacao'];
        $arDatas[] = $rsDataEmpenho->getCampo('dt_empenho');
    }
}
$dataMaior="";
$dataComp="";
foreach($arDatas as $data){
    if($dataComp == ""){
        $dataComp = $data;
    }    
    $bolComparaDatas = SistemaLegado::comparaDatas($dataComp , $data);
    if ($bolComparaDatas) {
        $dataMaior = $dataComp;
    }  else {
        $dataMaior = $data;
    }   
}
$mesEmpenho = explode( '/', SistemaLegado::dataToBr($dataMaior) );
$mesHomologacao = explode( '/', $_REQUEST["stDtHomologacao"] );

if ( $mesEmpenho[1] != '' ) {
    if (SistemaLegado::comparaDatas($_REQUEST["stDtHomologacao"], SistemaLegado::dataToBr($dataMaior) ) && $mesEmpenho[1] != $mesHomologacao[1]   )  {
        $stMensagemErro = " A data da Homologação deve ser igual ou anterior ao mês de emissão do empenho."; 
    }
}
if ($stMensagemErro == '') {
    $obTComprasCompraDiretaHomologacao = new TComprasCompraDiretaHomologacao;
    $obTComprasCompraDiretaHomologacao->setComplementoChave( '' );
    $obTComprasCompraDiretaHomologacao->proximoCod( $inNumHomologacao );
    unset( $obTComprasCompraDiretaHomologacao );

    $itensHomologacao = Sessao::read('itensHomologacao');
    $obHomologado = false;
    
    $obTComprasJustificativaRazao = new TComprasJustificativaRazao;
    $obTComprasJustificativaRazao->setDado( 'cod_compra_direta'      , $_REQUEST["inCodCompraDireta"]       );
    $obTComprasJustificativaRazao->setDado( 'cod_modalidade'         , $_REQUEST['inCodModalidade']         );
    $obTComprasJustificativaRazao->setDado( 'cod_entidade'           , $_REQUEST['inCodEntidade']           );
    $obTComprasJustificativaRazao->setDado( 'exercicio_entidade'     , $_REQUEST['stExercicioCompraDireta'] );
    $obTComprasJustificativaRazao->recuperaPorChave($rsJustificativaRazao);
    
    $obTComprasJustificativaRazao->setDado( 'justificativa'          , $_REQUEST['stJustificativa']         );
    $obTComprasJustificativaRazao->setDado( 'razao'                  , $_REQUEST['stRazao']                 );
    $obTComprasJustificativaRazao->setDado( 'fundamentacao_legal'    , $_REQUEST['stFundamentacao']         );
    
    if($rsJustificativaRazao->getNumLinhas() > 0){
        $obTComprasJustificativaRazao->alteracao();
    }else{
        $obTComprasJustificativaRazao->inclusao();
    }

    foreach ($itensHomologacao as $item) {
        $obTComprasCompraDiretaHomologacao = new TComprasCompraDiretaHomologacao;
        $obTComprasCompraDiretaHomologacao->setDado( 'num_homologacao'         , $item['numHomologacao']          );
        $obTComprasCompraDiretaHomologacao->setDado( 'exercicio'               , $item['exercicioJulgamentoItem'] );
        $obTComprasCompraDiretaHomologacao->setDado( 'cod_compra_direta'       , $item['codCompraDireta']         );
        $obTComprasCompraDiretaHomologacao->setDado( 'cod_modalidade'          , $item['codModalidade']           );
        $obTComprasCompraDiretaHomologacao->setDado( 'cod_entidade'            , $item['codEntidade']             );
        $obTComprasCompraDiretaHomologacao->setDado( 'exercicio_compra_direta' , $item['CompraDiretaExercicio']   );
        $obTComprasCompraDiretaHomologacao->setDado( 'lote'                    , $item['lote']                    );
        $obTComprasCompraDiretaHomologacao->setDado( 'cod_cotacao'             , $item['codCotacao']              );
        $obTComprasCompraDiretaHomologacao->setDado( 'cgm_fornecedor'          , $item['cgmFornecedor']           );
        $obTComprasCompraDiretaHomologacao->setDado( 'cod_item'                , $item['codItem']                 );
        $obTComprasCompraDiretaHomologacao->setDado( 'exercicio_cotacao'       , $item['cotacaoExercicio']        );
        $obTComprasCompraDiretaHomologacao->setDado( 'cod_documento'           , 0 );
        $obTComprasCompraDiretaHomologacao->setDado( 'cod_tipo_documento'      , 0 );
        $data      = explode('/',$_REQUEST["stDtHomologacao"]);
        $timestamp = date("Y-m-d H:i:s", strtotime($data[2]."-".$data[1]."-".$data[0]." ".$_REQUEST["stHoraHomologacao"]));
        $obTComprasCompraDiretaHomologacao->setDado( 'timestamp', "'".$timestamp."'" );
        

        switch ($item['status']) {
            case "Homologado":
                $obHomologado = true;
                $obTComprasCompraDiretaHomologacao->setDado( 'homologado', true );
                break;
            case "Homologado e Autorizado":
                $obHomologado = true;
                $obTComprasCompraDiretaHomologacao->setDado( 'homologado', true );
                break;
            default:
                $obTComprasCompraDiretaHomologacao->setDado( 'homologado', false );
                break;
        }

        if ($item['numHomologacao'] == "") {
            $obTComprasCompraDiretaHomologacao->setDado( 'num_homologacao'    , $inNumHomologacao );
            $obTComprasCompraDiretaHomologacao->inclusao();
        } elseif ($obHomologado == true) {
            $obTComprasCompraDiretaHomologacao->setDado( 'num_homologacao'    , $item['numHomologacao'] );
            $obTComprasCompraDiretaHomologacao->alteracao();
        } else {
            $obTComprasCompraDiretaHomologacao->setDado( 'num_homologacao'    , $item['numHomologacao'] );
            $obTComprasCompraDiretaHomologacao->exclusao();
        }
        //$obTComprasCompraDiretaHomologacao->debug();

        /*if ( ( $item['status'] == "Anulado" || $item['status'] == "Revogado" ) && !$item['boAnuladoBanco'] ) {
                $obTLicitacaoHomologacaoAnulada = new TLicitacaoHomologacaoAnulada;
                $obTLicitacaoHomologacao->recuperaNow3( $stNow );

                $obTLicitacaoHomologacaoAnulada->setDado( 'num_homologacao'      , $obTLicitacaoHomologacao->getDado( 'num_homologacao' ) );
                $obTLicitacaoHomologacaoAnulada->setDado( 'num_adjudicacao'      , $item['numAdjudicacao'] );
                $obTLicitacaoHomologacaoAnulada->setDado( 'cod_entidade'         , $item['codEntidade'] );
                $obTLicitacaoHomologacaoAnulada->setDado( 'cod_modalidade'       , $item['codModalidade'] );
                $obTLicitacaoHomologacaoAnulada->setDado( 'cod_licitacao'        , $item['codLicitacao'] );
                $obTLicitacaoHomologacaoAnulada->setDado( 'timestamp_adjudicacao', $item['timestampAdjudicacao'] );
                $obTLicitacaoHomologacaoAnulada->setDado( 'exercicio_licitacao'  , $item['licitacaoExercicio'] );
                $obTLicitacaoHomologacaoAnulada->setDado( 'cod_item'             , $item['codItem'] );
                $obTLicitacaoHomologacaoAnulada->setDado( 'cgm_fornecedor'       , $item['cgmFornecedor'] );
                $obTLicitacaoHomologacaoAnulada->setDado( 'cod_cotacao'          , $item['codCotacao'] );
                $obTLicitacaoHomologacaoAnulada->setDado( 'lote'                 , $item['lote'] );
                $obTLicitacaoHomologacaoAnulada->setDado( 'exercicio_cotacao'    , $item['cotacaoExercicio'] );
                $obTLicitacaoHomologacaoAnulada->setDado( 'timestamp_homologacao', $stNow );
                $obTLicitacaoHomologacaoAnulada->setDado( 'motivo'               , $item['justificativa_anulacao'] );
                if( $item['status'] == "Revogado" )
                $obTLicitacaoHomologacaoAnulada->setDado( 'revogacao'        , true  );
                else
                $obTLicitacaoHomologacaoAnulada->setDado( 'revogacao'        , false );

                $obTLicitacaoHomologacaoAnulada->inclusao();
                unset($obTLicitacaoHomologacaoAnulada);
        }
        unset($obTLicitacaoHomologacao);*/
    }

    // gerar autorização de empenho
    if ($_REQUEST['boGerarAutorizacao']) {
        $obTComprasCompraDiretaHomologacao = new TComprasCompraDiretaHomologacao();
        $obTOrcamentoReservaSaldos         = new TOrcamentoReservaSaldos();

        $stFiltroHomologacao = 'WHERE cod_compra_direta  = '.$_REQUEST["inCodCompraDireta"].'
                                  AND cod_modalidade     = '.$_REQUEST["inCodModalidade"].'
                                  AND cod_entidade       = '.$_REQUEST["inCodEntidade"].'
                                  AND exercicio_entidade = '.$_REQUEST["stExercicioCompraDireta"];

        $obTComprasCompraDiretaHomologacao->recuperaGrupoAutEmpenho( $rsAutEmpenho, $stFiltroHomologacao );

        $arImpAut = array();
        $inCont = 0;

            while (!$rsAutEmpenho->eof()) {
                    $obAutorizacaoEmpenho = new REmpenhoAutorizacaoEmpenho;

                    $obAutorizacaoEmpenho->boAutViaHomologacao = TRUE;
                    $obAutorizacaoEmpenho->setExercicio( Sessao::getExercicio() );
                    $obAutorizacaoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $rsAutEmpenho->getCampo('cod_entidade') );
                    $obAutorizacaoEmpenho->obREmpenhoTipoEmpenho->setCodTipo( 0 );
                    $obAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa( $rsAutEmpenho->getCampo("cod_despesa") );
                    $obAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->setMascClassificacao( $rsAutEmpenho->getCampo("mascara_classificacao") );
                    $obAutorizacaoEmpenho->obRCGM->setNumCGM( $rsAutEmpenho->getCampo("fornecedor") );
                    $obAutorizacaoEmpenho->obRUsuario->obRCGM->setNumCGM( Sessao::read('numCgm') );
                    $obAutorizacaoEmpenho->obREmpenhoHistorico->setCodHistorico( 0 );
                    $obAutorizacaoEmpenho->obROrcamentoReserva->setDtValidadeInicial( date('d/m/Y') );
                    $obAutorizacaoEmpenho->obROrcamentoReserva->setDtValidadeFinal( '31/12/'.date('Y') );
                    $obAutorizacaoEmpenho->obROrcamentoReserva->setDtInclusao( date('d/m/Y'));
                    $obAutorizacaoEmpenho->setDescricao( $rsAutEmpenho->getCampo("cod_objeto") . " - " . $rsAutEmpenho->getCampo("desc_objeto") );
                    $obAutorizacaoEmpenho->setDtAutorizacao( date('d/m/Y') );
                    $obAutorizacaoEmpenho->obROrcamentoReserva->setVlReserva( $rsAutEmpenho->getCampo("reserva") );
                    $obAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao( $rsAutEmpenho->getCampo("num_orgao") );
                    $obAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->setNumeroUnidade( $rsAutEmpenho->getCampo("num_unidade") );

                    // atributo modalidade
                    // array temporario para relação entre modalidade licitacao e atributo modalidade do empenho
                    $arModalidade = array(1 => 2,2 => 3,3 => 4,4 => 0,5 => 1,6 => 10,7 => 10,8 => 5,9 => 6);
                    $inAtribModalidade = $arModalidade[$rsAutEmpenho->getCampo("cod_modalidade")];
                    $obAutorizacaoEmpenho->obRCadastroDinamico->addAtributosDinamicos( '101' , $inAtribModalidade );

                    // atributo tipo credor
                    $obAutorizacaoEmpenho->obRCadastroDinamico->addAtributosDinamicos( '103' , 1 );

                    // atributo complementar
                    $obAutorizacaoEmpenho->obRCadastroDinamico->addAtributosDinamicos( '100' , 2 );

                    // itens
                    $stFiltroHomologacao .= " and cotacao_fornecedor_item.cgm_fornecedor = " . $rsAutEmpenho->getCampo("fornecedor") ;
                    $stFiltroHomologacao .= " and solicitacao_item_dotacao.cod_despesa = " . $rsAutEmpenho->getCampo("cod_despesa") ;
                    $obTComprasCompraDiretaHomologacao->recuperaItensHomologacaoAutEmpenho( $rsItensAutEmpenho, $stFiltroHomologacao );

                    unset($stFiltroHomologacao);

                    $inNumItemCont = 1;
                    while ( !$rsItensAutEmpenho->eof() ) {
                        // gerar autorização
                        $obAutorizacaoEmpenho->addItemPreEmpenho();
                        $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setCompra( true );
                        $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setNumItem    	( $inNumItemCont++ );
                        $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setExercicioMapa ( $rsItensAutEmpenho->getCampo( 'exercicio_mapa' ) );
                        $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setQuantidade ( $rsItensAutEmpenho->getCampo( 'qtd_cotacao' ) );
                        $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setNomUnidade ( $rsItensAutEmpenho->getCampo( 'nom_unidade' ) );
                        $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setValorTotal ( $rsItensAutEmpenho->getCampo( 'vl_cotacao' ) );
                        $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setNomItem    ( $rsItensAutEmpenho->getCampo( 'descricao_resumida' ) );
                        $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setComplemento( $rsItensAutEmpenho->getCampo( 'descricao_completa' ) );
                        $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setCgmFornecedor( $rsItensAutEmpenho->getCampo( 'fornecedor' ) );
                        $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setExercicioJulgamento ( $rsItensAutEmpenho->getCampo( 'exercicio' ) );
                        $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setLoteCompras ( $rsItensAutEmpenho->getCampo( 'lote' ) );
                        $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setCodCotacao  ( $rsItensAutEmpenho->getCampo( 'cod_cotacao' ) );
                        $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setCodItem     ( $rsItensAutEmpenho->getCampo( 'cod_item' ) );
                        $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->obRUnidadeMedida->setCodUnidade( $rsItensAutEmpenho->getCampo( 'cod_unidade' ) );
                        $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->obRUnidadeMedida->obRGrandeza->setCodGrandeza( $rsItensAutEmpenho->getCampo( 'cod_grandeza') );
                        $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setSiglaUnidade( $rsItensAutEmpenho->getCampo( 'simbolo') );

                        // atualizar saldo do item na solicitação ou anula caso seja zero
                        // busca info da reserva
                        $obTOrcamentoReservaSaldos->setDado('exercicio'      	, $rsItensAutEmpenho->getCampo( 'exercicio_solicitacao' ) );
                        $obTOrcamentoReservaSaldos->setDado('cod_reserva'    	, $rsItensAutEmpenho->getCampo( 'cod_reserva') );
                        $obTOrcamentoReservaSaldos->consultar();

                        if ( (integer) $rsItensAutEmpenho->getCampo('nova_reserva_solicitacao') == 0 ) {
                                require_once( CAM_GF_ORC_MAPEAMENTO . "TOrcamentoReservaSaldosAnulada.class.php");
                                $obTOrcamentoReservaSaldosAnulada = new TOrcamentoReservaSaldosAnulada();
                                $obTOrcamentoReservaSaldosAnulada->setDado( 'cod_reserva' , $rsItensAutEmpenho->getCampo( 'cod_reserva') );
                                $obTOrcamentoReservaSaldosAnulada->setDado( 'exercicio'   , $rsItensAutEmpenho->getCampo( 'exercicio_solicitacao' ) );
                                $obTOrcamentoReservaSaldosAnulada->setDado( 'dt_anulacao' , date( 'd/m/Y' ) );
                                $obTOrcamentoReservaSaldosAnulada->setDado( 'motivo_anulacao' , 'Anulação Automática, Solicitação de Compras Atendida: '. $rsAutEmpenho->getCampo( 'cod_entidade' ) . '.'. $rsItensAutEmpenho->getCampo( 'cod_solicitacao' ) . '/'. $rsItensAutEmpenho->getCampo( 'exercicio_solicitacao' ) . ' do Mapa: '. $rsItensAutEmpenho->getCampo( 'cod_mapa' ) . '/'. $rsItensAutEmpenho->getCampo( 'exercicio_mapa' ) . '' );
                                $obTOrcamentoReservaSaldosAnulada->inclusao();
                        } else {
                                $obTOrcamentoReservaSaldos->setDado('vl_reserva'     	, $rsItensAutEmpenho->getCampo( 'nova_reserva_solicitacao' ) );
                                $obTOrcamentoReservaSaldos->alteracao();
                        }

                        $rsItensAutEmpenho->proximo();
                    }
                    $obErro = $obAutorizacaoEmpenho->incluir();

                    // guardar autorizacoes para impressao
                    $arImpAut[$inCont++] = array(   "inCodAutorizacao"	=> $obAutorizacaoEmpenho->getCodAutorizacao(),
                                                    "inCodPreEmpenho" 	=> $obAutorizacaoEmpenho->getCodPreEmpenho(),
                                                    "inCodEntidade" 	=> $obAutorizacaoEmpenho->obROrcamentoEntidade->getCodigoEntidade(),
                                                    "stDtAutorizacao" 	=> $obAutorizacaoEmpenho->getDtAutorizacao(),
                                                    "inCodDespesa" 	=> $obAutorizacaoEmpenho->obROrcamentoDespesa->getCodDespesa() );
                    $rsAutEmpenho->proximo();
            }
    }
}

Sessao::write('stImpressaoAutorizacao', $arImpAut);
$stMensagem = '';
if ($_REQUEST['boGerarTermoHomologacao'] && !$obHomologado) {
    $stMensagem = ". Não foi possível gerar o Termo de Homologação pois nenhum item foi homologado.";
}

if ($stMensagemErro != '') {
    SistemaLegado::exibeAviso($stMensagemErro , "n_incluir", "erro");
} else {
    SistemaLegado::alertaAviso($pgForm, "Compra direta ".$_REQUEST['inCodCompraDireta']."/".$item['CompraDiretaExercicio'].$stMensagem, "incluir", "aviso", Sessao::getId(), "../");
}

// geracao do termo
if ($_REQUEST['boGerarTermoHomologacao'] && $obHomologado) {
    $stValor = sistemalegado::pegaConfiguracao("CGMPrefeito");

    if (!$stValor) {
        $stmensagem = "É preciso preencher o nome do prefeito em Administração :: Configuração, antes de gerar este documento.";
        sistemaLegado::exibeAviso($stmensagem, "n_incluir", "erro");
    } else {
        Sessao::write('request', $_REQUEST);
        SistemaLegado::mudaFrameOculto($pgGera.'?'.Sessao::getId());
    }
}

if ( !$obErro->ocorreu() ) {
    Sessao::encerraExcecao();
}

if ($_REQUEST['boGerarAutorizacao']) {
    $stCaminho = CAM_GF_EMP_INSTANCIAS."autorizacao/OCRelatorioAutorizacao.php";
    $stCampos  =  "?".Sessao::getId()."&stAcao=imprimir&stCaminho=".$stCaminho."";
    SistemaLegado::executaFrameOculto( "var x = window.open('".CAM_FW_POPUPS."relatorio/OCRelatorio.php".$stCampos."','oculto');" );
}

?>
