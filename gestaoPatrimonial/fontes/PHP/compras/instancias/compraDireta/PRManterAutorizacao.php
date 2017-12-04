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
 * Página de Processamento de Aut. Empenho
 * Data de Criação: 10/02/2007

 * @author Analista: Anelise Schwengber
 * @author Desenvolvedor: Andre Almeida

 * @ignore

 $Id: PRManterAutorizacao.php 65614 2016-06-02 13:10:59Z franver $

 * Casos de uso: uc-03.04.32
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stPrograma = "ManterAutorizacao";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgGera     = CAM_GF_EMP_INSTANCIAS."autorizacao/OCGeraRelatorioAutorizacao.php";

$obErro = new Erro();

Sessao::setTrataExcecao(true);

include_once CAM_GF_EMP_NEGOCIO."REmpenhoAutorizacaoEmpenho.class.php";
include_once CAM_GP_COM_MAPEAMENTO."TComprasCompraDireta.class.php";
include_once CAM_GP_COM_MAPEAMENTO."TComprasSolicitacao.class.php";
include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoReservaSaldos.class.php";
include_once CAM_GP_COM_MAPEAMENTO."TComprasMapaItemReserva.class.php";
include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoAutorizacaoEmpenhoAssinatura.class.php";
include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoDespesa.class.php";

$obTCompraDireta           = new TComprasCompraDireta;
$obTOrcamentoReservaSaldos = new TOrcamentoReservaSaldos;

$stFiltroAut  = " AND  compra_direta.cod_compra_direta  = ".$_REQUEST['inCodCompraDireta'];
$stFiltroAut .= " AND  compra_direta.cod_modalidade     = ".$_REQUEST['inCodModalidade'];
$stFiltroAut .= " AND  compra_direta.cod_entidade       = ".$_REQUEST['inCodEntidade'];
$stFiltroAut .= " AND  compra_direta.exercicio_entidade = ".$_REQUEST['stExercicioEntidade']."::VARCHAR";
$stFiltroAut .= " -- Não pode existir uma cotação anulada.
                  AND NOT EXISTS
                  (
                    SELECT  1
                      FROM  compras.cotacao_anulada
                     WHERE  cotacao_anulada.cod_cotacao = cotacao.cod_cotacao
                       AND  cotacao_anulada.exercicio   = cotacao.exercicio
                  ) ";

$obTCompraDireta->recuperaItensAgrupadosAutorizacao( $rsAutEmpenho, $stFiltroAut );

$stDtCompraDireta = $_REQUEST['stDtCompraDireta'];

    /* Validações das datas de autorização de empenho */
    while ((!$rsAutEmpenho->eof()) && (!$obErro->ocorreu())) {

        if (!empty($_REQUEST['dtAutorizacao_'.$rsAutEmpenho->getCampo('cod_entidade')])) {
            // Não pode ser menor que a data da Ultima Compra Direta.
            if (SistemaLegado::comparaDatas($stDtCompraDireta, $_REQUEST['dtAutorizacao_'.$rsAutEmpenho->getCampo('cod_entidade')], false)) {
                $obErro->setDescricao("Data da Compra Direta superior à última data de autorização da entidade ".$rsAutEmpenho->getCampo('cod_entidade').".");
            } else {
                // Não pode ser menor que a data corrente.
                if (SistemaLegado::comparaDatas($_REQUEST['dtAutorizacao_'.$rsAutEmpenho->getCampo('cod_entidade')], date("d/m/Y"), false)) {
                    $obErro->setDescricao("Data da Autorização deve ser menor ou igual a data atual.");
                }

               //Recupera última data da Autorização
               include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php";
               $obTEmpenhoEmpenho = new TEmpenhoEmpenho;

               $stFiltro = "      AND  empenho.cod_entidade = ".$rsAutEmpenho->getCampo('cod_entidade')." \n";
               $stFiltro.= "      AND  empenho.exercicio    = '".Sessao::getExercicio()."'                \n";
               $stOrdem  = " ORDER BY  empenho.dt_empenho DESC LIMIT 1                               	  \n";

               $obTEmpenhoEmpenho->recuperaUltimaDataEmpenho( $rsRecordSet,$stFiltro,$stOrdem );

                if ($dataUltimoEmpenho != "") {
                    $dataUltimoEmpenho = SistemaLegado::dataToBr($rsRecordSet->getCampo('dt_empenho'));
                }

                $obREmpenhoAutorizacaoEmpenho = new REmpenhoAutorizacaoEmpenho;
                $obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $rsAutEmpenho->getCampo('cod_entidade'));
                $obREmpenhoAutorizacaoEmpenho->setExercicio( Sessao::getExercicio() );
                $obREmpenhoAutorizacaoEmpenho->listarMaiorData( $rsMaiorData );

                if ( ($rsMaiorData->getCampo( "data_autorizacao" ) !="") ) {
                    $stDtAutorizacao = $rsMaiorData->getCampo( "data_autorizacao" );
                    $stExercicioDtAutorizacao = substr($stDtAutorizacao, 6, 4);
                } elseif ( ( $dataUltimoEmpenho !="") ) {
                    $stDtAutorizacao = $dataUltimoEmpenho;
                    $stExercicioDtAutorizacao = substr($dataUltimoEmpenho, 6, 4);
                } else {
                    $stDtAutorizacao = "01/01/".Sessao::getExercicio();
                    $stExercicioDtAutorizacao = Sessao::getExercicio();
                }
                // Não pode ser menor que a data da Ultima autorização.
                if (SistemaLegado::comparaDatas($stDtAutorizacao, $_REQUEST['dtAutorizacao_'.$rsAutEmpenho->getCampo('cod_entidade')], false)) {
                    $obErro->setDescricao("A Data da Autorização deve ser superior à última data de autorização "."(".$stDtAutorizacao.")"." da entidade ".$rsAutEmpenho->getCampo('cod_entidade').".");
                }
            }
        } else {
            $obErro->setDescricao("A data da autorização da entidade ".$rsAutEmpenho->getCampo('cod_entidade')." não pode ser vazia.");
        }

        $rsAutEmpenho->proximo();
    }

    if (!$obErro->ocorreu()) {
        $stFiltroAut   = " AND  compra_direta.cod_compra_direta  = ".$_REQUEST['inCodCompraDireta'];
        $stFiltroAut  .= " AND  compra_direta.cod_modalidade     = ".$_REQUEST['inCodModalidade'];
        $stFiltroAut  .= " AND  compra_direta.cod_entidade       = ".$_REQUEST['inCodEntidade'];
        $stFiltroAut  .= " AND  compra_direta.exercicio_entidade = ".$_REQUEST['stExercicioEntidade']."::VARCHAR";
        $stFiltroAut  .= " --   Não pode existir uma cotação anulada.
                           AND  NOT EXISTS
                                (
                                 SELECT  1
                                   FROM  compras.cotacao_anulada
                                  WHERE  cotacao_anulada.cod_cotacao = cotacao.cod_cotacao
                                    AND  cotacao_anulada.exercicio   = cotacao.exercicio
                                ) ";

        $obTCompraDireta->recuperaItensAgrupadosAutorizacao( $rsAutEmpenho, $stFiltroAut );

        //busca Informações da Observacao/Justificativa da solicitação
        $stFiltroSolicitacaoCompra  = " AND  compra_direta.cod_compra_direta  = ".$_REQUEST['inCodCompraDireta'];
        $stFiltroSolicitacaoCompra .= " AND  compra_direta.cod_modalidade     = ".$_REQUEST['inCodModalidade'];
        $stFiltroSolicitacaoCompra .= " AND  compra_direta.cod_entidade       = ".$_REQUEST['inCodEntidade'];
        $stFiltroSolicitacaoCompra .= " AND  compra_direta.exercicio_entidade = ".$_REQUEST['stExercicioEntidade']."::VARCHAR";
        $stFiltroSolicitacaoCompra .= "
                AND NOT EXISTS (
                                 SELECT  1
                                   FROM  compras.cotacao_anulada
                                  WHERE  cotacao_anulada.cod_cotacao = cotacao.cod_cotacao
                                    AND  cotacao_anulada.exercicio   = cotacao.exercicio
                               )

                AND NOT EXISTS (
                                 SELECT 1
                                   FROM compras.solicitacao_anulacao
                                  WHERE solicitacao_anulacao.cod_solicitacao = solicitacao.cod_solicitacao
                                    AND solicitacao_anulacao.exercicio   = solicitacao.exercicio
                                    AND solicitacao_anulacao.cod_entidade   = solicitacao.cod_entidade
                                )

                      GROUP BY  solicitacao.cod_solicitacao
                             ,  solicitacao.observacao
                             ,  solicitacao.exercicio
                             ,  solicitacao.cod_almoxarifado
                             ,  solicitacao.cod_entidade
                             ,  solicitacao.cgm_solicitante
                             ,  solicitacao.cgm_requisitante
                             ,  solicitacao.cod_objeto
                             ,  solicitacao.prazo_entrega
                             ,  solicitacao.timestamp";

        $obTComprasSolicitacao = new TComprasSolicitacao;
        $obTComprasSolicitacao->recuperaSolicitacaoAgrupadaNaoAnulada($rsSolicitacaoAtiva, $stFiltroSolicitacaoCompra);

        while (!$rsSolicitacaoAtiva->EOF()) {
            $observacaoSolicitacao .= $rsSolicitacaoAtiva->getCampo('observacao').'§§';
            $rsSolicitacaoAtiva->proximo();
        }

        Sessao::write('observacaoSolicitacao', $observacaoSolicitacao);

        $arAutorizacao = Sessao::read('arAutorizacao');
        $inCont = 0;

        while ((!$rsAutEmpenho->eof()) and (!$obErro->ocorreu())) {
            $obAutorizacaoEmpenho = new REmpenhoAutorizacaoEmpenho;

            $obAutorizacaoEmpenho->boAutViaPatrimonial = false;
            $obAutorizacaoEmpenho->setExercicio( Sessao::getExercicio() );
            $obAutorizacaoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $rsAutEmpenho->getCampo('cod_entidade') );
            $obAutorizacaoEmpenho->obREmpenhoTipoEmpenho->setCodTipo( 0 );

            $obAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa( $rsAutEmpenho->getCampo("cod_despesa") );
            $obAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->setMascClassificacao( $rsAutEmpenho->getCampo("mascara_classificacao") );
            $obAutorizacaoEmpenho->obRCGM->setNumCGM( $rsAutEmpenho->getCampo("fornecedor") );
            $obAutorizacaoEmpenho->obRUsuario->obRCGM->setNumCGM( Sessao::read('numCgm') );
            $obAutorizacaoEmpenho->obREmpenhoHistorico->setCodHistorico( 0 );
            $obAutorizacaoEmpenho->obROrcamentoReserva->setDtValidadeInicial( $_REQUEST['dtAutorizacao_'.$rsAutEmpenho->getCampo('cod_entidade')] );
            $obAutorizacaoEmpenho->obROrcamentoReserva->setDtValidadeFinal( '31/12/'.date('Y') );
            $obAutorizacaoEmpenho->obROrcamentoReserva->setDtInclusao( $_REQUEST['dtAutorizacao_'.$rsAutEmpenho->getCampo('cod_entidade')] );
            $obAutorizacaoEmpenho->setDescricao( $rsAutEmpenho->getCampo("cod_objeto") . " - " . $rsAutEmpenho->getCampo("desc_objeto"));
            $obAutorizacaoEmpenho->setDtAutorizacao( $_REQUEST['dtAutorizacao_'.$rsAutEmpenho->getCampo('cod_entidade')] );
            $obAutorizacaoEmpenho->obROrcamentoReserva->setVlReserva( $rsAutEmpenho->getCampo("reserva") );
            $obAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao( $rsAutEmpenho->getCampo("num_orgao") );
            $obAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->setNumeroUnidade( $rsAutEmpenho->getCampo("num_unidade") );
            $obAutorizacaoEmpenho->setCodCategoria ( 1 );

            // atributo modalidade
            // array temporario para relação entre modalidade licitacao e atributo modalidade do empenho
            $arModalidade = array(1 => 2, 2 => 3, 3 => 4, 4 => 0, 5 => 1, 6 => 11, 7 => 12,8 => 5,9 => 6, 10 => 13, 11 => 14);
            $inAtribModalidade = $arModalidade[$rsAutEmpenho->getCampo("cod_modalidade")];
            $obAutorizacaoEmpenho->obRCadastroDinamico->addAtributosDinamicos( '101' , $inAtribModalidade );

            // atributo tipo credor
            // segundo Valtair não está sendo utilizado o atributo tipo credor
            //$obAutorizacaoEmpenho->obRCadastroDinamico->addAtributosDinamicos( '103' , 1 );

            // atributo complementar
            $obAutorizacaoEmpenho->obRCadastroDinamico->addAtributosDinamicos( '100' , 2 );

            
            require_once TCOM."TComprasCompraDiretaProcesso.class.php";
            $rsCompraDireta = new RecordSet();
            $obTComprasCompraDiretaProcesso = new TComprasCompraDiretaProcesso();
            $obTComprasCompraDiretaProcesso->setDado('cod_compra_direta' , $_REQUEST['inCodCompraDireta'] );
            $obTComprasCompraDiretaProcesso->setDado('cod_entidade'	     , $_REQUEST['inCodEntidade'] );
            $obTComprasCompraDiretaProcesso->setDado('exercicio_entidade', $_REQUEST['stExercicioEntidade'] );
            $obTComprasCompraDiretaProcesso->setDado('cod_modalidade'    , $_REQUEST['inCodModalidade'] );
            $obTComprasCompraDiretaProcesso->recuperaPorCompraDireta($rsCompraDireta, '','',$boTransacao);
            
            // atributo numero processo administrativo stNumeroProcesso
            $obAutorizacaoEmpenho->obRCadastroDinamico->addAtributosDinamicos( '120' , $rsCompraDireta->getCampo('cod_processo') );
            
            // atributo exercicio processo administrativo stExercicioProcesso
            $obAutorizacaoEmpenho->obRCadastroDinamico->addAtributosDinamicos( '121' , $rsCompraDireta->getCampo('exercicio_processo') );

            // itens
            $stFiltroTmp = $stFiltroAut;
            $stFiltroTmp .= " and cotacao_fornecedor_item.cgm_fornecedor = " . $rsAutEmpenho->getCampo("fornecedor");
            $stFiltroTmp .= " and solicitacao_item_dotacao.cod_despesa = " . $rsAutEmpenho->getCampo("cod_despesa");
            $stFiltroTmp .= " and solicitacao_item_dotacao.cod_conta = " .   $rsAutEmpenho->getCampo("cod_conta" );

            $obTCompraDireta->recuperaItensAutorizacao( $rsItensAutEmpenho, $stFiltroTmp );

            $boReservaRigida = SistemaLegado::pegaConfiguracao('reserva_rigida', '35', Sessao::getExercicio());
            $boReservaRigida = ($boReservaRigida == 'true') ? true : false;

            $boReservaAutorizacao = SistemaLegado::pegaConfiguracao('reserva_autorizacao', '35', Sessao::getExercicio());
            $boReservaAutorizacao = ($boReservaAutorizacao == 'true') ? true : false;

            #Reserva de Saldos por Autorização  
            if(!$boReservaRigida && $boReservaAutorizacao && $rsItensAutEmpenho->eof()){
                $stFiltroSolicitacaoCompra  = " AND  compra_direta.cod_compra_direta  = ".$_REQUEST['inCodCompraDireta'];
                $stFiltroSolicitacaoCompra .= " AND  compra_direta.cod_modalidade     = ".$_REQUEST['inCodModalidade'];
                $stFiltroSolicitacaoCompra .= " AND  compra_direta.cod_entidade       = ".$_REQUEST['inCodEntidade'];
                $stFiltroSolicitacaoCompra .= " AND  compra_direta.exercicio_entidade = ".$_REQUEST['stExercicioEntidade']."::VARCHAR";
                $obTCompraDireta->recuperaInfoItensAgrupadosSolicitacao($rsSolicitacaoReserva, $stFiltroSolicitacaoCompra);

                while (!$rsSolicitacaoReserva->eof()) {
                    $inCodDespesa = $rsSolicitacaoReserva->getCampo('cod_despesa');

                    $obTOrcamentoDespesa = new TOrcamentoDespesa;
                    $obTOrcamentoDespesa->setDado( "cod_despesa", $inCodDespesa );
                    $obTOrcamentoDespesa->setDado( "exercicio", Sessao::getExercicio() );
                    $obTOrcamentoDespesa->recuperaSaldoDotacao( $rsSaldoDotacao );
                    
                    if(!$rsSaldoDotacao->eof()){
                        if(!isset($arSaldoDotacao[$inCodDespesa])){
                            $arSaldoDotacao[$inCodDespesa]['saldo_inicial'] = $rsSaldoDotacao->getCampo('saldo_dotacao');
                            $arSaldoDotacao[$inCodDespesa]['vl_reserva'] = $rsSolicitacaoReserva->getCampo('vl_cotacao');
                        }else
                            $arSaldoDotacao[$inCodDespesa]['vl_reserva'] += $rsSolicitacaoReserva->getCampo('vl_cotacao');
                    }
                
                    # Mensagem do motivo da criação da Reserva de Saldo.
                    $stMsgReserva  = "Entidade: ".$rsSolicitacaoReserva->getCampo('cod_entidade')." - ".ucwords(strtolower($rsSolicitacaoReserva->getCampo('nom_entidade'))).", ";
                    $stMsgReserva .= "Mapa de Compras: ".$rsSolicitacaoReserva->getCampo('cod_mapa')."/".$rsSolicitacaoReserva->getCampo('exercicio_mapa').", ";
                    $stMsgReserva .= "Item: ".$rsSolicitacaoReserva->getCampo('cod_item').", ";
                    $stMsgReserva .= "Centro de Custo: ".$rsSolicitacaoReserva->getCampo('cod_centro')." ";
                    $stMsgReserva .= "(Origem da criação: ".SistemaLegado::pegaDado('nom_acao', 'administracao.acao', 'WHERE cod_acao = '.Sessao::read('acao')).").";

                    # Cria uma nova reserva de saldo que será utilizada agora no Mapa de Compras.
                    $obTOrcamentoReservaSaldos->setDado('exercicio' , $rsSolicitacaoReserva->getCampo('exercicio_mapa'));
                    $obTOrcamentoReservaSaldos->proximoCod($inCodReserva);

                    $obTOrcamentoReservaSaldos->setDado('cod_reserva'         , $inCodReserva);
                    $obTOrcamentoReservaSaldos->setDado('exercicio'           , $rsSolicitacaoReserva->getCampo('exercicio_mapa'));
                    $obTOrcamentoReservaSaldos->setDado('cod_despesa'         , $rsSolicitacaoReserva->getCampo('cod_despesa'));
                    $obTOrcamentoReservaSaldos->setDado('dt_validade_inicial' , date('d/m/Y'));
                    $obTOrcamentoReservaSaldos->setDado('dt_validade_final'   , '31/12/'.Sessao::getExercicio());
                    $obTOrcamentoReservaSaldos->setDado('dt_inclusao'         , date('d/m/Y'));
                    $obTOrcamentoReservaSaldos->setDado('vl_reserva'          , $rsSolicitacaoReserva->getCampo('vl_cotacao'));
                    $obTOrcamentoReservaSaldos->setDado('tipo'                , 'A');
                    $obTOrcamentoReservaSaldos->setDado('motivo'              , $stMsgReserva);

                    # Inclui na tabela compras.mapa_item_reserva, caso consiga fazer a reserva de saldos.
                    if ($obTOrcamentoReservaSaldos->incluiReservaSaldo() == true) {
                        $obTComprasMapaItemReserva = new TComprasMapaItemReserva;
                        $obTComprasMapaItemReserva->setDado('exercicio_mapa'        , $rsSolicitacaoReserva->getCampo('exercicio_mapa'));
                        $obTComprasMapaItemReserva->setDado('cod_mapa'              , $rsSolicitacaoReserva->getCampo('cod_mapa'));
                        $obTComprasMapaItemReserva->setDado('exercicio_solicitacao' , $rsSolicitacaoReserva->getCampo('exercicio_solicitacao'));
                        $obTComprasMapaItemReserva->setDado('cod_entidade'          , $rsSolicitacaoReserva->getCampo('cod_entidade'));
                        $obTComprasMapaItemReserva->setDado('cod_solicitacao'       , $rsSolicitacaoReserva->getCampo('cod_solicitacao'));
                        $obTComprasMapaItemReserva->setDado('cod_centro'            , $rsSolicitacaoReserva->getCampo('cod_centro'));
                        $obTComprasMapaItemReserva->setDado('cod_item'              , $rsSolicitacaoReserva->getCampo('cod_item'));
                        $obTComprasMapaItemReserva->setDado('lote'                  , $rsSolicitacaoReserva->getCampo('lote'));
                        $obTComprasMapaItemReserva->setDado('exercicio_reserva'     , $rsSolicitacaoReserva->getCampo('exercicio_mapa'));
                        $obTComprasMapaItemReserva->setDado('cod_reserva'           , $inCodReserva);
                        $obTComprasMapaItemReserva->setDado('cod_conta'             , $rsSolicitacaoReserva->getCampo('cod_conta'));
                        $obTComprasMapaItemReserva->setDado('cod_despesa'           , $rsSolicitacaoReserva->getCampo('cod_despesa'));

                        $obErro = $obTComprasMapaItemReserva->inclusao( Sessao::getTransacao() );

                        if ($obErro->ocorreu()){
                            break;
                        }
                    } else{
                        $stSolicitacao = $rsSolicitacaoReserva->getCampo('cod_solicitacao').'/'.$rsSolicitacaoReserva->getCampo('exercicio_solicitacao');
                        $obErro->setDescricao('Não foi possível reservar saldo para o item '.$rsSolicitacaoReserva->getCampo('cod_item').' da Solicitação '.$stSolicitacao.". Saldo da Dotação: ".number_format($arSaldoDotacao[$inCodDespesa]['saldo_inicial'],2,',','.'));
                        break;
                    }
                    
                    $rsSolicitacaoReserva->proximo();
                }
            }

            if (!$obErro->ocorreu()) {
                $obTCompraDireta->recuperaItensAutorizacao( $rsItensAutEmpenho, $stFiltroTmp );

                $inNumItemCont = 1;
                while ( !$rsItensAutEmpenho->eof() ) {
                    // atualizar saldo do item na solicitação ou anula caso seja zero
                    // busca info da reserva
                    require_once( CAM_GF_ORC_MAPEAMENTO . "TOrcamentoReservaSaldosAnulada.class.php");
                    $obTOrcamentoReservaSaldosAnulada = new TOrcamentoReservaSaldosAnulada();
                    $obTOrcamentoReservaSaldosAnulada->setDado( 'cod_reserva' , $rsItensAutEmpenho->getCampo( 'cod_reserva') );
                    $obTOrcamentoReservaSaldosAnulada->setDado( 'exercicio'   , $rsItensAutEmpenho->getCampo( 'exercicio_solicitacao' ) );
                    $obTOrcamentoReservaSaldosAnulada->setDado( 'motivo_anulacao' , 'Anulação Automática. Entidade: '.$rsAutEmpenho->getCampo( 'cod_entidade' ).' - '.$rsAutEmpenho->getCampo( 'nom_entidade' ).', Mapa de compras: '. $rsItensAutEmpenho->getCampo( 'cod_mapa' ) . '/'. $rsItensAutEmpenho->getCampo( 'exercicio_mapa' ) . '' );
                    $obTOrcamentoReservaSaldosAnulada->consultar();

                    if (Sessao::getExcecao()->getDescricao() == "Nenhum registro encontrado!") {
                        Sessao::getExcecao()->setDescricao("");
                    }

                    if ( !$obTOrcamentoReservaSaldosAnulada->getDado ( 'dt_anulacao' ) ) {
                        $obTOrcamentoReservaSaldosAnulada->setDado( 'dt_anulacao' , $_REQUEST['dtAutorizacao_'.$rsAutEmpenho->getCampo('cod_entidade')] );
                        $obErro = $obTOrcamentoReservaSaldosAnulada->inclusao( Sessao::getTransacao() );
                    }
                    $rsItensAutEmpenho->proximo();
                }

                //anula reserva de saldo dos itens que não possuem cotação
                $stItens = '';
                $rsItensAutEmpenho->setPrimeiroElemento();
                while (!$rsItensAutEmpenho->eof()) {
                    $stItens .= ','.$rsItensAutEmpenho->getCampo('cod_item');
                    $rsItensAutEmpenho->proximo();
                }

                $rsItensAutEmpenho->setPrimeiroElemento();
                $stItens = substr($stItens, 1);
                $rsItensSemCotacao = new RecordSet();

                //recupera itens que nao tiveram cotacao
                $obTComprasMapaItemReserva = new TComprasMapaItemReserva();
                $stFiltroMapaItemReserva = " LEFT JOIN compras.mapa_cotacao
                                ON mapa_cotacao.cod_mapa = mapa_item_reserva.cod_mapa
                                   AND mapa_cotacao.exercicio_mapa = mapa_item_reserva.exercicio_mapa

                             LEFT JOIN compras.cotacao
                                ON cotacao.cod_cotacao = mapa_cotacao.cod_cotacao
                                   AND cotacao.exercicio = mapa_cotacao.exercicio_cotacao

                             LEFT JOIN compras.cotacao_anulada
                                ON cotacao.cod_cotacao = cotacao_anulada.cod_cotacao
                                   AND cotacao.exercicio = cotacao_anulada.exercicio

                             LEFT JOIN compras.cotacao_item
                                ON cotacao.cod_cotacao = cotacao_item.cod_cotacao
                                   AND cotacao.exercicio = cotacao_item.exercicio

                             LEFT JOIN compras.cotacao_fornecedor_item
                                ON cotacao_item.exercicio = cotacao_fornecedor_item.exercicio
                                   AND cotacao_item.cod_cotacao = cotacao_fornecedor_item.cod_cotacao
                                   AND cotacao_item.cod_item = cotacao_fornecedor_item.cod_item
                                   AND cotacao_item.lote = cotacao_fornecedor_item.lote

                            WHERE mapa_item_reserva.cod_item NOT IN (".$stItens.")
                                   AND mapa_item_reserva.cod_mapa = ".$rsItensAutEmpenho->getCampo('cod_mapa')."
                                   AND mapa_item_reserva.exercicio_mapa = '".$rsItensAutEmpenho->getCampo('exercicio_mapa')."'
                                   AND mapa_item_reserva.cod_solicitacao = ".$rsItensAutEmpenho->getCampo('cod_solicitacao')."
                                   AND mapa_item_reserva.exercicio_mapa = '".$rsItensAutEmpenho->getCampo('exercicio_solicitacao')."'
                                   AND cotacao_fornecedor_item.cod_cotacao IS NULL
                                   AND cotacao_anulada.cod_cotacao IS NULL

                             GROUP BY  mapa_item_reserva.exercicio_reserva
                                ,  mapa_item_reserva.cod_reserva
                                ,  mapa_item_reserva.cod_despesa
                                ,  mapa_item_reserva.cod_conta
                                ,  mapa_item_reserva.cod_item
                                ,  mapa_item_reserva.cod_centro
                                ,  mapa_item_reserva.cod_entidade
                                ,  solicitacao_homologada_reserva.cod_reserva
                                ,  solicitacao_homologada_reserva.exercicio
                                ,  reserva_saldos_solicitacao.vl_reserva
                                ,  reserva_saldos.vl_reserva
                                   ";
                $obTComprasMapaItemReserva->recuperaReservas($rsItensSemCotacao, $stFiltroMapaItemReserva);

                $rsItensSemCotacao->setPrimeiroElemento();
                while (!$rsItensSemCotacao->eof()) {
                    $obTOrcamentoReservaSaldosAnulada = new TOrcamentoReservaSaldosAnulada();
                    $obTOrcamentoReservaSaldosAnulada->setDado( 'cod_reserva' , $rsItensSemCotacao->getCampo( 'cod_reserva') );
                    $obTOrcamentoReservaSaldosAnulada->setDado( 'exercicio'   , $rsItensSemCotacao->getCampo( 'exercicio_reserva' ) );
                    $obErro2 = $obTOrcamentoReservaSaldosAnulada->consultar();
                    if (Sessao::getExcecao()->getDescricao() == "Nenhum registro encontrado!") {
                        Sessao::getExcecao()->setDescricao("");
                    }
                    $obTOrcamentoReservaSaldosAnulada->setDado( 'motivo_anulacao' , 'Anulação Automática. Entidade: '.$rsAutEmpenho->getCampo( 'cod_entidade' ).' - '.$rsAutEmpenho->getCampo( 'nom_entidade' ).', Mapa de compras: '. $rsItensAutEmpenho->getCampo( 'cod_mapa' ) . '/'. $rsItensAutEmpenho->getCampo( 'exercicio_mapa' ) . '' );
                    $obTOrcamentoReservaSaldosAnulada->setDado( 'dt_anulacao' , $_REQUEST['dtAutorizacao_'.$rsAutEmpenho->getCampo('cod_entidade')] );
                    if ($obErro2->ocorreu()) {
                        $obErro = $obTOrcamentoReservaSaldosAnulada->inclusao( Sessao::getTransacao() );
                    }

                    $rsItensSemCotacao->proximo();
                }

                unset($rsItensSemCotacao, $stFiltroMapaItemReserva);

                $stOrdem = " ORDER BY catalogo_item.descricao ";
                $obTCompraDireta->recuperaInfoItensAgrupadosSolicitacao( $rsItensSolicitacaoAgrupados, $stFiltroTmp, $stOrdem );

                unset($stFiltroHomologacao, $stFiltroTmp);

                while (!$rsItensSolicitacaoAgrupados->eof()) {
                    // gerar autorização
                    $obAutorizacaoEmpenho->addItemPreEmpenho();
                    $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setCompra( true );
                    $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setNumItem    	        ( $inNumItemCont++ );
                    $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setCodItem    	        ( $rsItensSolicitacaoAgrupados->getCampo( 'cod_item' )           );
                    $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setCodCotacao 	        ( $rsItensSolicitacaoAgrupados->getCampo( 'cod_cotacao' )        );
                    $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setExercicioJulgamento   ( $rsItensSolicitacaoAgrupados->getCampo( 'exercicio' )          );
                    $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setCgmFornecedor         ( $rsItensSolicitacaoAgrupados->getCampo( 'fornecedor' )         );
                    $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setLoteCompras 	        ( $rsItensSolicitacaoAgrupados->getCampo( 'lote' )               );
                    $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setQuantidade            ( $rsItensSolicitacaoAgrupados->getCampo( 'qtd_cotacao' )        );
                    $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setNomUnidade            ( $rsItensSolicitacaoAgrupados->getCampo( 'nom_unidade' )        );
                    $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setValorTotal            ( $rsItensSolicitacaoAgrupados->getCampo( 'vl_cotacao' )         );
                    $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setNomItem               ( $rsItensSolicitacaoAgrupados->getCampo( 'descricao_completa' ) );
                    $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setCodigoMarca           ( $rsItensSolicitacaoAgrupados->getCampo( 'cod_marca' )          );
                    $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setCodItemPreEmp         ( $rsItensSolicitacaoAgrupados->getCampo( 'cod_item' )           );
                    //descricao_completa do item do catalogo concatenada com complemento do item na solicitacao
                    $complemento = "";
                    if (trim($rsItensSolicitacaoAgrupados->getCampo( 'descricao_completa' ))) {
                        $complemento .= trim($rsItensSolicitacaoAgrupados->getCampo( 'descricao_completa' ))." ";
                    }
                    if (trim($rsItensSolicitacaoAgrupados->getCampo( 'complemento' ))) {
                        $complemento .= trim($rsItensSolicitacaoAgrupados->getCampo( 'complemento' ));
                    }
                    $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setComplemento($complemento);
                    $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->obRUnidadeMedida->setCodUnidade( $rsItensSolicitacaoAgrupados->getCampo( 'cod_unidade' )  );
                    $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->obRUnidadeMedida->obRGrandeza->setCodGrandeza( $rsItensSolicitacaoAgrupados->getCampo( 'cod_grandeza') );
                    $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setSiglaUnidade( $rsItensSolicitacaoAgrupados->getCampo( 'simbolo') );
                    $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setCodCentroCusto( $rsItensSolicitacaoAgrupados->getCampo('cod_centro') );

                    $rsItensSolicitacaoAgrupados->proximo();
                }
                
                $obAutorizacaoEmpenho->setCodEntidade($request->get('inCodEntidade'));
                $obAutorizacaoEmpenho->setTipoEmissao('R');
                $obErro = $obAutorizacaoEmpenho->incluir(Sessao::getTransacao());

                # Salvar Assinaturas configuráveis se houverem
                $arAssinaturas = Sessao::read('assinaturas');

                if (is_array($arAssinaturas) && count($arAssinaturas['selecionadas']) > 0) {
                    $arAssinatura = $arAssinaturas['selecionadas'];

                    $obTEmpenhoAutorizacaoEmpenhoAssinatura = new TEmpenhoAutorizacaoEmpenhoAssinatura;
                    $obTEmpenhoAutorizacaoEmpenhoAssinatura->setDado('exercicio'       , $obAutorizacaoEmpenho->getExercicio());
                    $obTEmpenhoAutorizacaoEmpenhoAssinatura->setDado('cod_entidade'    , $obAutorizacaoEmpenho->obROrcamentoEntidade->getCodigoEntidade());
                    $obTEmpenhoAutorizacaoEmpenhoAssinatura->setDado('cod_autorizacao' , $obAutorizacaoEmpenho->getCodAutorizacao());
                    $arPapel = $obTEmpenhoAutorizacaoEmpenhoAssinatura->arrayPapel();
    
                    foreach ($arAssinatura as $arAssina) {
                        if (isset($arAssina['papel'])) {
                            if (is_numeric($arAssina['papel'])) {
                                $inNumAssina = $arAssina['papel'];
                            } else {
                                $inNumAssina = $arPapel[$arAssina['papel']];
                            }
                        }

                        $obTEmpenhoAutorizacaoEmpenhoAssinatura->setDado('num_assinatura', $inNumAssina);
                        $obTEmpenhoAutorizacaoEmpenhoAssinatura->setDado('numcgm'        , $arAssina['inCGM']);
                        $obTEmpenhoAutorizacaoEmpenhoAssinatura->setDado('cargo'         , $arAssina['stCargo']);
                        $obErro = $obTEmpenhoAutorizacaoEmpenhoAssinatura->inclusao($boTransacao);
                    }

                    unset($obTEmpenhoAutorizacaoEmpenhoAssinatura);
                }

                # Armazena os dados da autorização em array para depois ser usado na impressão.
                $arAutorizacao[$inCont] = array( "inCodAutorizacao"	=> $obAutorizacaoEmpenho->getCodAutorizacao(),
                                                 "inCodPreEmpenho" 	=> $obAutorizacaoEmpenho->getCodPreEmpenho(),
                                                 "inCodEntidade" 	=> $obAutorizacaoEmpenho->obROrcamentoEntidade->getCodigoEntidade(),
                                                 "stDtAutorizacao" 	=> $obAutorizacaoEmpenho->getDtAutorizacao(),
                                                 "inCodDespesa" 	=> $obAutorizacaoEmpenho->obROrcamentoDespesa->getCodDespesa(),
                                                 "stExercicio"      => $obAutorizacaoEmpenho->getExercicio()
                                               );
                $rsAutEmpenho->proximo();
                $inCont++;
            }
        }

        if (!$obErro->ocorreu()) {
            $stMsg  = '';
            $indice = count($arAutorizacao) - 1;

            if (count($arAutorizacao) == 1) {
                $stMsg = $arAutorizacao[0]['inCodAutorizacao']. "/".Sessao::getExercicio() ;
            } else {
                $stMsg = "Autorizações de " . $arAutorizacao[0]['inCodAutorizacao']. "/".Sessao::getExercicio() . " até " . $arAutorizacao[$indice]['inCodAutorizacao']. "/".Sessao::getExercicio() ;
            }

            # Grava no array as autorizações geradas.
            Sessao::write('arAutorizacao', $arAutorizacao);
    
            # Exibe a mensagem e redireciona para a tela de download.
            SistemaLegado::alertaAviso($pgGera.'?'.Sessao::getId(), $stMsg , "incluir", "aviso", Sessao::getId(), "../");
        }
    }

    if ($obErro->ocorreu()) {
        $obErro->setDescricao('Erro ao Emitir Autorização de Empenho! ('.$obErro->getDescricao().')');
        echo "<script>LiberaFrames(true,true);</script>";
    }

Sessao::encerraExcecao();

?>
