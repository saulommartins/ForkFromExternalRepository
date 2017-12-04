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
    * Página de Processamento de Manter Homologação
    * Data de Criação: 23/10/2006

    * @author Analista: Anelise Schwengber
    * @author Desenvolvedor: Andre Almeida

    * @ignore

    * $Id: PRManterHomologacao.php 63178 2015-07-31 20:11:32Z carlos.silva $

    * Casos de uso: uc-03.05.21
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once( CAM_GP_LIC_MAPEAMENTO."TLicitacaoHomologacao.class.php" );
include_once( CAM_GP_LIC_MAPEAMENTO."TLicitacaoAdjudicacao.class.php" );
include_once( CAM_GP_LIC_MAPEAMENTO."TLicitacaoHomologacaoAnulada.class.php" );
include_once( CAM_GP_LIC_MAPEAMENTO."TLicitacaoJustificativaRazao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterHomologacao";
$pgForm     = "FM".$stPrograma.".php";
$pgGera     = "OCGeraDocumentoHomologacao.php";

$obErro = new Erro();
Sessao::setTrataExcecao( true );
$stFiltro = ' WHERE cod_licitacao = '.$_REQUEST["inCodLicitacao"].'
                            AND cod_modalidade = '.$_REQUEST["inCodModalidade"].'
                            AND cod_entidade = '.$_REQUEST["inCodEntidade"].'
                            AND exercicio_licitacao = '.$_REQUEST["stExercicioLicitacao"].'::varchar';

$obTLicitacaoAdjudicacao = new TLicitacaoAdjudicacao();
$obTLicitacaoAdjudicacao->recuperaTodos($rsAdjudicacao, $stFiltro);

$itensHomologacao = Sessao::read('itensHomologacao');
$arDatas = array();     
$itemCodCotacao = '';
foreach($itensHomologacao as $item){

    if($item['status'] == 'Homologado e Autorizado' && $itemCodCotacao != $item['codCotacao']){
        $obTLicitacaoHomologacao = new TLicitacaoHomologacao();
        $obTLicitacaoHomologacao->setDado('cod_cotacao', $item['codCotacao']);
        $obTLicitacaoHomologacao->recuperaEmpenhoPreEmpenhoCotacao($rsDataEmpenho);

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
$stMensagemErro = "";
if (SistemaLegado::comparaDatas($_REQUEST["stDtHomologacao"], date('d/m/Y'))) {
    $stMensagemErro = "A data da Homologação deve ser menor ou igual à data atual.";
}

if (SistemaLegado::comparaDatas(date('d/m/Y', strtotime($rsAdjudicacao->getCampo("timestamp"))), $_REQUEST["stDtHomologacao"]) && $stMensagemErro == '') {
    $stMensagemErro = "A data da Homologação deve ser maior ou igual à data de Adjudicação.";
}

if($mesEmpenho[1] != '') {
    if (SistemaLegado::comparaDatas($_REQUEST["stDtHomologacao"], SistemaLegado::dataToBr($dataMaior) ) && $mesEmpenho[1] != $mesHomologacao[1]   )  {
        $stMensagemErro = " A data da Homologação deve ser igual ou anterior ao mês de emissão do empenho.";
    }
}
if ($stMensagemErro == '') {
    $obTLicitacaoHomologacao = new TLicitacaoHomologacao;
    $obTLicitacaoHomologacao->setComplementoChave( '' );
    $obTLicitacaoHomologacao->proximoCod( $inNumHomologacao );
    unset( $obTLicitacaoHomologacao );

    $itensHomologacao = Sessao::read('itensHomologacao');
    $obHomologado = false;
    
    $obTLicitacaoJustificativaRazao = new TLicitacaoJustificativaRazao;
    $obTLicitacaoJustificativaRazao->setDado( 'cod_licitacao'          , $_REQUEST["inCodLicitacao"]       );
    $obTLicitacaoJustificativaRazao->setDado( 'cod_modalidade'         , $_REQUEST['inCodModalidade']      );
    $obTLicitacaoJustificativaRazao->setDado( 'cod_entidade'           , $_REQUEST['inCodEntidade']        );
    $obTLicitacaoJustificativaRazao->setDado( 'exercicio'              , $_REQUEST['stExercicioLicitacao'] );
    $obTLicitacaoJustificativaRazao->recuperaPorChave($rsJustificativaRazao);
    
    if($rsJustificativaRazao->getNumLinhas() > 0){
        $obTLicitacaoJustificativaRazao->setDado( 'justificativa'       , $_REQUEST['stJustificativa'] );
        $obTLicitacaoJustificativaRazao->setDado( 'razao'               , $_REQUEST['stRazao']         );
        $obTLicitacaoJustificativaRazao->setDado( 'fundamentacao_legal' , $_REQUEST['stFundamentacao'] );
        $obTLicitacaoJustificativaRazao->alteracao();                                                  
    }else{                                                                                             
        $obTLicitacaoJustificativaRazao->setDado( 'justificativa'       , $_REQUEST['stJustificativa'] );
        $obTLicitacaoJustificativaRazao->setDado( 'razao'               , $_REQUEST['stRazao']         );
        $obTLicitacaoJustificativaRazao->setDado( 'fundamentacao_legal' , $_REQUEST['stFundamentacao'] );
        $obTLicitacaoJustificativaRazao->inclusao();
    }
    
    foreach ($itensHomologacao as $item) {
            $obTLicitacaoHomologacao = new TLicitacaoHomologacao;

            $obTLicitacaoHomologacao->setDado( 'num_homologacao'      , $item['numHomologacao']     );
            $obTLicitacaoHomologacao->setDado( 'cod_licitacao'        , $item['codLicitacao']       );
            $obTLicitacaoHomologacao->setDado( 'cod_modalidade'       , $item['codModalidade']      );
            $obTLicitacaoHomologacao->setDado( 'cod_entidade'         , $item['codEntidade']        );
            $obTLicitacaoHomologacao->setDado( 'num_adjudicacao'      , $item['numAdjudicacao']     );
            $obTLicitacaoHomologacao->setDado( 'exercicio_licitacao'  , $item['licitacaoExercicio'] );
            $obTLicitacaoHomologacao->setDado( 'lote'                 , $item['lote']               );
            $obTLicitacaoHomologacao->setDado( 'cod_cotacao'          , $item['codCotacao']         );
            $obTLicitacaoHomologacao->setDado( 'cgm_fornecedor'       , $item['cgmFornecedor']      );
            $obTLicitacaoHomologacao->setDado( 'cod_item'             , $item['codItem']            );
            $obTLicitacaoHomologacao->setDado( 'exercicio_cotacao'    , $item['cotacaoExercicio']   );

            $obTLicitacaoHomologacao->setDado( 'cod_documento'        , 0 );
            $obTLicitacaoHomologacao->setDado( 'cod_tipo_documento'   , 0 );

            $obTLicitacaoHomologacao->setDado( 'autorizado_empenho'   , $request->get('boGerarAutorizacao') );
            switch ($item['status']) {
                    case "Homologado":
                $obHomologado = true;
                            $obTLicitacaoHomologacao->setDado( 'homologado', true );
                            break;
                    case "Homologado e Autorizado":
                $obHomologado = true;
                            $obTLicitacaoHomologacao->setDado( 'homologado', true );
                            break;
                    default:
                            $obTLicitacaoHomologacao->setDado( 'homologado', false );
                            break;
            }
            $data = explode('/',$_REQUEST["stDtHomologacao"]);

            $timestamp = date("Y-m-d H:i:s", strtotime($data[2]."-".$data[1]."-".$data[0]." ".$_REQUEST["stHoraHomologacao"]));

            $obTLicitacaoHomologacao->setDado( 'timestamp', $timestamp );
            if ($item['numHomologacao'] == "") {
                    $obTLicitacaoHomologacao->setDado( 'num_homologacao'    , $inNumHomologacao );
                    $obTLicitacaoHomologacao->inclusao();
            } else {
                    $obTLicitacaoHomologacao->setDado( 'num_homologacao'    , $item['numHomologacao'] );
                    $obTLicitacaoHomologacao->alteracao();
            }

            if ( ( $item['status'] == "Anulado" || $item['status'] == "Revogado" ) && !$item['boAnuladoBanco'] ) {
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
            unset($obTLicitacaoHomologacao);

    }

    // gerar autorização de empenho
    if ( $request->get('boGerarAutorizacao') ) {
            require_once( CAM_GF_EMP_NEGOCIO    . "REmpenhoAutorizacaoEmpenho.class.php" );
            require_once( CAM_GP_LIC_MAPEAMENTO . "TLicitacaoHomologacao.class.php"      );
            require_once( CAM_GF_ORC_MAPEAMENTO . "TOrcamentoReservaSaldos.class.php"    );

            $obTLicHomologacao = new TLicitacaoHomologacao();
            $obTOrcamentoReservaSaldos = new TOrcamentoReservaSaldos();

            $stFiltroHomologacao  = " and licitacao.cod_licitacao = " . $_REQUEST['inCodLicitacao'];
            $stFiltroHomologacao .= " and licitacao.exercicio = " . $_REQUEST['stExercicioLicitacao'].'::varchar' ;
        $stFiltroHomologacao .= " and licitacao.cod_entidade = " . $_REQUEST['inCodEntidade'] ;
        $stFiltroHomologacao .= " and licitacao.cod_modalidade = " . $_REQUEST['inCodModalidade'];

            $obTLicHomologacao->recuperaGrupoAutEmpenho( $rsAutEmpenho, $stFiltroHomologacao );
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
                    $obTLicHomologacao->recuperaItensHomologacaoAutEmpenho( $rsItensAutEmpenho, $stFiltroHomologacao );

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
                                            "inCodDespesa" 		=> $obAutorizacaoEmpenho->obROrcamentoDespesa->getCodDespesa() );
                    $rsAutEmpenho->proximo();
            }
    }
}

$arImpAut = isset($arImpAut) ? $arImpAut : null;
Sessao::write('stImpressaoAutorizacao', $arImpAut);
$stMensagem = '';
if ($_REQUEST['boGerarTermoHomologacao'] && !$obHomologado) {
    $stMensagem = ". Não foi possível gerar o Termo de Homologação pois nenhum item foi homologado.";
}

if ($stMensagemErro != '') {
    SistemaLegado::exibeAviso($stMensagemErro , "n_incluir", "erro");
} else {
    SistemaLegado::alertaAviso($pgForm, "Licitação ".$_POST['inCodLicitacao']."/".$_POST['stExercicioLicitacao'].$stMensagem, "incluir", "aviso", Sessao::getId(), "../");
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
