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
 * Página de Processamento Manter Solicitação de Compra
 * Data de Criação   : 21/09/2006

 $Id: PRManterSolicitacaoCompra.php 64416 2016-02-18 17:35:05Z evandro $

 * @ignore

 * Casos de uso: uc-03.04.01

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once CAM_GP_COM_MAPEAMENTO."TComprasSolicitacao.class.php";
include_once CAM_GP_COM_MAPEAMENTO."TComprasSolicitacaoAnulacao.class.php";
include_once CAM_GP_COM_MAPEAMENTO."TComprasSolicitacaoConvenio.class.php";
include_once CAM_GP_COM_MAPEAMENTO."TComprasSolicitacaoEntrega.class.php";
include_once CAM_GP_COM_MAPEAMENTO."TComprasSolicitacaoItem.class.php";
include_once CAM_GP_COM_MAPEAMENTO."TComprasSolicitacaoItemDotacao.class.php";
include_once CAM_GP_COM_MAPEAMENTO."TComprasSolicitacaoItemAnulacao.class.php";
include_once CAM_GP_COM_MAPEAMENTO."TComprasSolicitacaoItemDotacaoAnulacao.class.php";
include_once CAM_GP_COM_MAPEAMENTO.'TComprasSolicitacaoHomologada.class.php';
include_once CAM_GP_COM_MAPEAMENTO."TComprasSolicitacaoHomologadaAnulacao.class.php";
include_once CAM_GP_COM_MAPEAMENTO.'TComprasSolicitacaoHomologadaReserva.class.php';
include_once CAM_GP_COM_MAPEAMENTO."TComprasObjeto.class.php";

# Includes da GF.
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoReservaSaldos.class.php';
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoReservaSaldosAnulada.class.php';
include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoContaDespesa.class.php";
include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoContaDespesa.class.php";
include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoDespesa.class.php";

$stAcao = $request->get('stAcao');

// Seja o códigoda Solicitação.
$inCodSolicitacao = $_REQUEST['inCodSolicitacao'];

//Define o nome dos arquivos PHP
$stPrograma = "ManterSolicitacaoCompra";
$pgFilt     = "FL".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgList     = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgForm     = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgFormItem = "FM".$stPrograma."Item.php?".Sessao::getId()."&stAcao=$stAcao";
$pgProc     = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul     = "OC".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgRel		= "FMRelatorio".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";

Sessao::setTrataExcecao( true );

$obTComprasSolicitacao     = new TComprasSolicitacao();
$obTComprasSolicitacaoItem = new TComprasSolicitacaoItem();

function homologaSolicitacao($inCodSolicitacao, $inCodEntidade, $stExercicio, $arItem)
{
    $obTComprasSolicitacaoItemDotacao = new TComprasSolicitacaoItemDotacao();
    $obTOrcamentoContaDespesa         = new TOrcamentoContaDespesa();

    $boHomologaAutomatico = SistemaLegado::pegaConfiguracao ( 'homologacao_automatica', 35 			);
    $boReservaRigida = SistemaLegado::pegaConfiguracao		( 'reserva_rigida', 35					);
    $boDotacaoObrigatoria = SistemaLegado::pegaConfiguracao	( 'dotacao_obrigatoria_solicitacao', 35	);

    $boReservaRigida = ( $boReservaRigida == 'true' ) ? true : false;
    $boDotacaoObrigatoria = ( $boDotacaoObrigatoria == 'true' ) ? true : false;
    $boHomologaAutomatico = ( $boHomologaAutomatico == 'true' ) ? true : false;

    $boDotacao = true;
    $boNaoDotacao = true;

    if ( $boDotacaoObrigatoria && ($arItem['inCodDespesa'] == '') ) {
        $boDotacao = false;
    } elseif ($arItem['inCodDespesa'] == '') {
        $boDotacao = false;
        $boNaoDotacao = false;
    }

    if ($boDotacao) {
        $stFiltro= " AND D.cod_despesa     = ".$arItem['inCodDespesa']."\n";
        $stFiltro.= " AND CD.exercicio      ='".$stExercicio."'          \n";

        $obTOrcamentoContaDespesa->recuperaRelacionamento( $rsContaDespesa , $stFiltro );

        $stSql =" WHERE solicitacao_item_dotacao.cod_entidade =".$inCodEntidade."                                \n";
        $stSql.=" AND solicitacao_item_dotacao.cod_solicitacao=".$inCodSolicitacao."    \n";
        $stSql.=" AND solicitacao_item_dotacao.cod_centro=".$arItem['inCodCentroCusto']."\n";
        $stSql.=" AND solicitacao_item_dotacao.cod_item  =".$arItem['inCodItem']."       \n";
        $stSql.=" AND solicitacao_item_dotacao.exercicio ='".$stExercicio."'                                           \n";

        $obTComprasSolicitacaoItemDotacao->recuperaTodos($rsRecordSetItem,$stSql);

        if ($rsRecordSetItem->EOF()) {
            $obTComprasSolicitacaoItemDotacao->setDado('exercicio'      ,$stExercicio                                               );
            $obTComprasSolicitacaoItemDotacao->setDado('cod_entidade'   ,$inCodEntidade                                       );
            $obTComprasSolicitacaoItemDotacao->setDado('cod_solicitacao',$inCodSolicitacao               );
            $obTComprasSolicitacaoItemDotacao->setDado('cod_centro'     ,$arItem['inCodCentroCusto']  );
            $obTComprasSolicitacaoItemDotacao->setDado('cod_item'       ,$arItem['inCodItem']         );
            $obTComprasSolicitacaoItemDotacao->setDado('vl_reserva'     ,str_replace(',','.',str_replace('.','',$arItem['nuVlTotal'])));

            if ($arItem['inCodEstrutural']=="") {
                $obTComprasSolicitacaoItemDotacao->setDado('cod_conta',$rsContaDespesa->getCampo('cod_conta')                         );
            } else {
                $obTComprasSolicitacaoItemDotacao->setDado('cod_conta',$arItem['inCodEstrutural'] );
            }
            $obTComprasSolicitacaoItemDotacao->setDado('cod_despesa'    ,$arItem['inCodDespesa']      );

            $obTComprasSolicitacaoItemDotacao->inclusao();
        }
    } else {
        if ($boNaoDotacao) {
            $stMensagem = 'Existem itens sem dotação.';
        }
    }

    if ($boHomologaAutomatico) {

        $numcgm      = SistemaLegado::pegaDado('numcgm','orcamento.entidade', "where cod_entidade =".$inCodEntidade." and exercicio = '".Sessao::getExercicio()."'");
        $nomEntidade = SistemaLegado::pegaDado('nom_cgm','sw_cgm', "where numcgm =".$numcgm);

        $obReservaSaldo = new TOrcamentoReservaSaldos;

        $nuVlrReserva = 0;
        $nuVlrReserva = str_replace(',','.',str_replace('.','',$arItem['nuVlTotal']));

        if ($arItem['inCodDespesa'] && $nuVlrReserva > 0) {

            //inclusão na tabela orcamento.reserva_saldo
            $obReservaSaldo->setDado( 'exercicio'          , $stExercicio );
            $obReservaSaldo->proximoCod( $inCodReserva );
            $obReservaSaldo->setDado( 'cod_reserva',         $inCodReserva );
            $obReservaSaldo->setDado( 'cod_despesa'        , $arItem['inCodDespesa'] );
            $obReservaSaldo->setDado( 'dt_validade_inicial', date('d/m/Y') );

            $obReservaSaldo->setDado( 'tipo'               , 'A' );
            $obReservaSaldo->setDado( 'dt_inclusao'        , date('d/m/Y') );
            $obReservaSaldo->setDado( 'motivo'             , "Entidade: ".$inCodEntidade." - ".$nomEntidade.", solicitação de compras: ".$inCodSolicitacao."/".$stExercicio);

            $obReservaSaldo->setDado( 'vl_reserva'         ,  $nuVlrReserva );
            $obReservaSaldo->setDado( 'dt_validade_final'  , '31/12/'.$stExercicio	);
            if ( $obReservaSaldo->incluiReservaSaldo() ) {

                /// inclusão na tabela compras.solicitacao_homologada_reserva
                $obTSolicitacaoHomologadaReserva = new TComprasSolicitacaoHomologadaReserva;
                $obTSolicitacaoHomologadaReserva->setDado ( 'exercicio'       , $stExercicio	                 );
                $obTSolicitacaoHomologadaReserva->setDado ( 'cod_entidade'    , $inCodEntidade            );
                $obTSolicitacaoHomologadaReserva->setDado ( 'cod_solicitacao' , $inCodSolicitacao					     );
                $obTSolicitacaoHomologadaReserva->setDado ( 'cod_item'        , $arItem['inCodItem']   );
                $obTSolicitacaoHomologadaReserva->setDado ( 'cod_centro'      , $arItem['inCodCentroCusto'] );
                $obTSolicitacaoHomologadaReserva->setDado ( 'cod_reserva'     , $inCodReserva );
                $obTSolicitacaoHomologadaReserva->obTOrcamentoReservaSaldos = & $obReservaSaldo;
                $obTSolicitacaoHomologadaReserva->inclusao();
            }
        }
    }

    return $stMensagem;
}

switch ($stAcao) {

    case "excluir":

        $rsRecordSet                             = new RecordSet;
        $obTComprasSolicitacaoItemDotacao        = new TComprasSolicitacaoItemDotacao();
        $obTComprasSolicitacaoEntrega            = new TComprasSolicitacaoEntrega();
        $obTComprasSolicitacaoHomologadaAnulacao = new TComprasSolicitacaoHomologadaAnulacao();
        $obTComprasSolicitacaoHomologada         = new TComprasSolicitacaoHomologada();

        Sessao::getTransacao()->setMapeamento( $obTComprasSolicitacao     );

        $obTComprasSolicitacaoItemDotacao->setDado( 'cod_solicitacao' , $_REQUEST['cod_solicitacao'] );
        $obTComprasSolicitacaoItemDotacao->setDado( 'cod_entidade'    , $_REQUEST['cod_entidade']    );
        $obTComprasSolicitacaoItemDotacao->setDado( 'exercicio'       , $_REQUEST['exercicio']    );
        $obTComprasSolicitacaoItemDotacao->exclusao();

        $obTComprasSolicitacaoItem->setDado( 'cod_solicitacao' , $_REQUEST['cod_solicitacao'] );
        $obTComprasSolicitacaoItem->setDado( 'cod_entidade'    , $_REQUEST['cod_entidade']    );
        $obTComprasSolicitacaoItem->setDado( 'exercicio'       , $_REQUEST['exercicio']    );
        $obTComprasSolicitacaoItem->exclusao();

        $obTComprasSolicitacaoEntrega->setDado( 'cod_solicitacao' , $_REQUEST['cod_solicitacao'] );
        $obTComprasSolicitacaoEntrega->setDado( 'cod_entidade'    , $_REQUEST['cod_entidade']    );
        $obTComprasSolicitacaoEntrega->setDado( 'exercicio'       , $_REQUEST['exercicio']           );
        $obTComprasSolicitacaoEntrega->exclusao();

        $obTComprasSolicitacaoHomologadaAnulacao->setDado( 'cod_solicitacao' , $_REQUEST['cod_solicitacao']  );
        $obTComprasSolicitacaoHomologadaAnulacao->setDado( 'cod_entidade'    , $_REQUEST['cod_entidade']     );
        $obTComprasSolicitacaoHomologadaAnulacao->setDado( 'exercicio'       , $_REQUEST['exercicio']        );
        $obTComprasSolicitacaoHomologadaAnulacao->verificaExistenciaHomologacaoAnulada($rsHomologacaoAnulada);

        if ($rsHomologacaoAnulada->getCampo('existe')) {
            $obTComprasSolicitacaoHomologadaAnulacao->setDado( 'cod_solicitacao' , $_REQUEST['cod_solicitacao']  );
            $obTComprasSolicitacaoHomologadaAnulacao->setDado( 'cod_entidade'    , $_REQUEST['cod_entidade']     );
            $obTComprasSolicitacaoHomologadaAnulacao->setDado( 'exercicio'       , $_REQUEST['exercicio']        );
            $obTComprasSolicitacaoHomologadaAnulacao->exclusao();

            $obTComprasSolicitacaoHomologada->setDado( 'cod_solicitacao' , $_REQUEST['cod_solicitacao']  );
            $obTComprasSolicitacaoHomologada->setDado( 'cod_entidade'    , $_REQUEST['cod_entidade']     );
            $obTComprasSolicitacaoHomologada->setDado( 'exercicio'       , $_REQUEST['exercicio']       );
            $obTComprasSolicitacaoHomologada->exclusao();
        }

        $obTComprasSolicitacao->setDado( 'cod_solicitacao' , $_REQUEST['cod_solicitacao']  );
        $obTComprasSolicitacao->setDado( 'cod_entidade'    , $_REQUEST['cod_entidade']     );
        $obTComprasSolicitacao->setDado( 'exercicio'       , $_REQUEST['exercicio']     );
        $obTComprasSolicitacao->exclusao();

        SistemaLegado::alertaAviso($pgList.'?'.Sessao::getId()."&stAcao=$stAcao","Número da solicitação: ".$_REQUEST['cod_solicitacao'],"anular", "aviso", Sessao::getId(),"");

        break;

    case "anular":

        $obErro = new Erro;
        $arValores = Sessao::read('arItens');

        if (count($arValores) > 0) {
            foreach ($arValores as $value) {
                $nuVlAnular = str_replace(",",".",str_replace(".","",$value['vl_anular']));
                if ($nuVlAnular > 0.00) {
                    $boAnula = true;
                }
            }

            if ($boAnula) {
                $obTComprasSolicitacaoAnulacao = new TComprasSolicitacaoAnulacao;
                $obTComprasSolicitacaoAnulacao->setDado( 'cod_entidade'    , $_REQUEST['stCodEntidade']    );
                $obTComprasSolicitacaoAnulacao->setDado( 'cod_solicitacao' , $_REQUEST['stCodSolicitacao'] );
                $obTComprasSolicitacaoAnulacao->setDado( 'exercicio'       , $_REQUEST['stExercicio']      );
                $obTComprasSolicitacaoAnulacao->setDado( 'motivo'          , $_REQUEST['stMotivo']         );
                $obTComprasSolicitacaoAnulacao->setDado( 'timestamp'       , date('Y-m-d H:i:s.ms')        );
                $obErro = $obTComprasSolicitacaoAnulacao->inclusao();

                if (!$obErro->ocorreu()) {
                    foreach ($arValores as $value) {
                        $nuVlAnular = str_replace(",",".",str_replace(".","",$value['vl_anular']));

                        if ($nuVlAnular > 0.00) {
                            if ($value['cod_conta'] && $value['cod_despesa']) {
                                $obTComprasSolicitacaoItemDotacao = new TComprasSolicitacaoItemDotacao;
                                $stFiltro  = " WHERE exercicio       = '".$_REQUEST['stExercicio']."'";
                                $stFiltro .= "   AND cod_entidade    =  ".$_REQUEST['stCodEntidade'];
                                $stFiltro .= "   AND cod_solicitacao =  ".$_REQUEST['stCodSolicitacao'];
                                $stFiltro .= "   AND cod_centro      =  ".$value['cod_centro'];
                                $stFiltro .= "   AND cod_item        =  ".$value['cod_item'];
                                $stFiltro .= "   AND cod_conta       =  ".$value['cod_conta'];
                                $stFiltro .= "   AND cod_despesa     =  ".$value['cod_despesa'];
                                $obErro = $obTComprasSolicitacaoItemDotacao->recuperaTodos($rsRecordSet, $stFiltro);

                                $nuSaldoReserva = $rsRecordSet->getCampo('vl_reserva');
                                $nuVlReserva    = $nuSaldoReserva - $nuVlAnular;
                                $nuVlReserva    = number_format( $nuVlReserva , 2, ".",",");

                                $nuQuantidade   = $rsRecordSet->getCampo('quantidade');
                                $nuQntReserva   = $nuQuantidade - $value['qnt_anular'];
                                $nuQntReserva   = number_format( $nuQntReserva , 4, ".",",");
                            }

                            if (!$obErro->ocorreu()) {
                                $obTComprasSolicitacaoHomologadaReserva = new TComprasSolicitacaoHomologadaReserva;
                                $obTComprasSolicitacaoHomologadaReserva->setDado('cod_solicitacao' , $_REQUEST['stCodSolicitacao'] );
                                $obTComprasSolicitacaoHomologadaReserva->setDado('exercicio'       , $_REQUEST['stExercicio']      );
                                $obTComprasSolicitacaoHomologadaReserva->setDado('cod_entidade'    , $_REQUEST['stCodEntidade']    );
                                $obTComprasSolicitacaoHomologadaReserva->setDado('cod_centro'      , $value['cod_centro']          );
                                $obTComprasSolicitacaoHomologadaReserva->setDado('cod_item'        , $value['cod_item']            );
                                $obErro = $obTComprasSolicitacaoHomologadaReserva->recuperaHomologacaoReservaSaldoAnulada($rsHomologacaoReserva);

                                if ($nuVlReserva > 0.00) {
                                    $obTOrcamentoReservaSaldos = new TOrcamentoReservaSaldos;
                                    $obTOrcamentoReservaSaldos->setDado('cod_reserva' , $value['cod_reserva']    );
                                    $obTOrcamentoReservaSaldos->setDado('exercicio'   , $_REQUEST['stExercicio'] );
                                    $obTOrcamentoReservaSaldos->recuperaPorChave($rsReservas);

                                    if ($rsReservas->getNumLinhas() > 0) {
                                        $obTComprasSolicitacaoItemDotacaoAnulacao = new TComprasSolicitacaoItemDotacaoAnulacao;
                                        $obTComprasSolicitacaoItemDotacaoAnulacao->setDado('exercicio'       ,  $_REQUEST['stExercicio']      );
                                        $obTComprasSolicitacaoItemDotacaoAnulacao->setDado('cod_entidade'    ,  $_REQUEST['stCodEntidade']    );
                                        $obTComprasSolicitacaoItemDotacaoAnulacao->setDado('cod_solicitacao' ,  $_REQUEST['stCodSolicitacao'] );
                                        $obTComprasSolicitacaoItemDotacaoAnulacao->setDado('cod_centro'      ,  $value['cod_centro']          );
                                        $obTComprasSolicitacaoItemDotacaoAnulacao->setDado('cod_item'        ,  $value['cod_item']            );
                                        $obTComprasSolicitacaoItemDotacaoAnulacao->setDado('cod_conta'       ,  $value['cod_conta']           );
                                        $obTComprasSolicitacaoItemDotacaoAnulacao->setDado('cod_despesa'     ,  $value['cod_despesa']         );
                                        $obTComprasSolicitacaoItemDotacaoAnulacao->recuperaTotalAnulado($rsRecordSet);

                                        if ($rsRecordSet->getNumLinhas() > 0) {
                                            $nuVlTotalAnulacoes = $rsRecordSet->getCampo('vl_anulacao');
                                            $nuVlReservar = ($nuSaldoReserva - $nuVlTotalAnulacoes) - $nuVlAnular;
                                        } else {
                                            $nuVlReservar = $nuSaldoReserva - $nuVlAnular;
                                        }

                                        if ($nuVlReservar > 0.00) {
                                            $obTOrcamentoReservaSaldos->setDado('cod_reserva'         , $rsReservas->getCampo('cod_reserva') );
                                            $obTOrcamentoReservaSaldos->setDado('exercicio'           , $rsReservas->getCampo('exercicio') );
                                            $obTOrcamentoReservaSaldos->setDado('cod_despesa'         , $rsReservas->getCampo('cod_despesa') );
                                            $obTOrcamentoReservaSaldos->setDado('dt_validade_inicial' , $rsReservas->getCampo('dt_validade_inicial') );
                                            $obTOrcamentoReservaSaldos->setDado('dt_validade_final'   , $rsReservas->getCampo('dt_validade_final') );
                                            $obTOrcamentoReservaSaldos->setDado('dt_inclusao'         , $rsReservas->getCampo('dt_inclusao') );
                                            $obTOrcamentoReservaSaldos->setDado('tipo'                , $rsReservas->getCampo('tipo') );
                                            $obTOrcamentoReservaSaldos->setDado('motivo'              , $rsReservas->getCampo('motivo') );
                                            $obTOrcamentoReservaSaldos->setDado('vl_reserva'          , $nuVlReservar  );
                                            $obTOrcamentoReservaSaldos->alteracao();
                                        } else {
                                            $obReservaSaldoAnulada = new TOrcamentoReservaSaldosAnulada;
                                            $obReservaSaldoAnulada->setDado('cod_reserva' ,  $value['cod_reserva']    );
                                            $obReservaSaldoAnulada->setDado('exercicio'   ,  $_REQUEST['stExercicio'] );
                                            $obReservaSaldoAnulada->recuperaPorChave($rsRecordSet);

                                            if ($rsRecordSet->getNumLinhas() <= 0) {
                                                $obReservaSaldoAnulada = new TOrcamentoReservaSaldosAnulada;
                                                $obReservaSaldoAnulada->setDado("exercicio",Sessao::getExercicio()      );
                                                $obReservaSaldoAnulada->setDado("motivo_anulacao",$_REQUEST['stMotivo'] );
                                                $obReservaSaldoAnulada->setDado("dt_anulacao", date('d/m/Y')            );
                                                $obReservaSaldoAnulada->setDado("cod_reserva", $value['cod_reserva']);
                                                $obErro = $obReservaSaldoAnulada->inclusao();
                                            }
                                        }
                                    }
                                } else {
                                    if ($value['cod_reserva']) {
                                        $obReservaSaldoAnulada = new TOrcamentoReservaSaldosAnulada;
                                        $obReservaSaldoAnulada->setDado('cod_reserva' ,  $value['cod_reserva']    );
                                        $obReservaSaldoAnulada->setDado('exercicio'   ,  $_REQUEST['stExercicio'] );
                                        $obReservaSaldoAnulada->recuperaPorChave($rsRecordSet);

                                        if ($rsRecordSet->getNumLinhas() <= 0) {
                                            $obReservaSaldoAnulada = new TOrcamentoReservaSaldosAnulada;
                                            $obReservaSaldoAnulada->setDado("exercicio",Sessao::getExercicio()      );
                                            $obReservaSaldoAnulada->setDado("motivo_anulacao",$_REQUEST['stMotivo'] );
                                            $obReservaSaldoAnulada->setDado("dt_anulacao",date( 'd/m/Y' )           );
                                            $obReservaSaldoAnulada->setDado("cod_reserva", $value['cod_reserva']);
                                            $obErro = $obReservaSaldoAnulada->inclusao();
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                # OS LANÇAMENTOS ANTERIORES SÃO FEITOS CONFORME OS ITENS LISTADOS NA TELA DO FORMULÁRIO. A SEGUIR, O SISTEMA AGRUPA
                # OS ITENS DEVIDO AS RESTRIÇÕES DE CHAVES IMPOSTAS PELAS TABELAS.

                # AGRUPA ITENS IGUAIS PARA LANÇAMENTO EM TABELAS
                $arItemAgrupado = array();
                foreach ($arValores as $value) {
                    $nuVlAnular = str_replace(",",".",str_replace(".","",$value['vl_anular']));
                    $nuVlTotal  = number_format($arItemAgrupado[$value['cod_item']."_".$value['cod_centro']]['vl_total'], 2, ".", ",") + $nuVlAnular;
                    $nuVlTotal  = number_format($nuVlTotal, 2, ",", ".");
                    
                    $nuQntAnular = str_replace(",",".",str_replace(".","",$value['qnt_anular']));
                    $nuQntTotal  = number_format($arItemAgrupado[$value['cod_item']."_".$value['cod_centro']]['qnt_total'], 4, ".", ",") + $nuQntAnular;
                    $nuQntTotal  = number_format($nuQntTotal, 4, ",", ".");

                    $arItemAgrupado[$value['cod_item']."_".$value['cod_centro']]['vl_total']    = $nuVlTotal;
                    $arItemAgrupado[$value['cod_item']."_".$value['cod_centro']]['qnt_total']   = $nuQntTotal;
                    $arItemAgrupado[$value['cod_item']."_".$value['cod_centro']]['cod_centro']  = $value['cod_centro'];
                    $arItemAgrupado[$value['cod_item']."_".$value['cod_centro']]['cod_item']    = $value['cod_item'];
                    $arItemAgrupado[$value['cod_item']."_".$value['cod_centro']]['cod_conta']   = $value['cod_conta'];
                    $arItemAgrupado[$value['cod_item']."_".$value['cod_centro']]['cod_despesa'] = $value['cod_despesa'];
                }
                ### FIM AGRUPAMENTO

                # LANÇAMENTO DOS ITENS AGRUPADOS
                foreach ($arItemAgrupado as $value) {
                    if ($value['vl_total'] > 0.00) {
                        $obTComprasSolicitacaoItemAnulacao = new TComprasSolicitacaoItemAnulacao;
                        $obTComprasSolicitacaoItemAnulacao->setDado('exercicio'       , $_REQUEST['stExercicio']                            );
                        $obTComprasSolicitacaoItemAnulacao->setDado('cod_entidade'    , $_REQUEST['stCodEntidade']                          );
                        $obTComprasSolicitacaoItemAnulacao->setDado('cod_solicitacao' , $_REQUEST['stCodSolicitacao']                       );
                        $obTComprasSolicitacaoItemAnulacao->setDado('timestamp'       , $obTComprasSolicitacaoAnulacao->getDado('timestamp'));
                        $obTComprasSolicitacaoItemAnulacao->setDado('cod_centro'      , $value['cod_centro']                                );
                        $obTComprasSolicitacaoItemAnulacao->setDado('cod_item'        , $value['cod_item']                                  );
                        $obTComprasSolicitacaoItemAnulacao->setDado('quantidade'      , $value['qnt_total']                                 );
                        $obTComprasSolicitacaoItemAnulacao->setDado('vl_total'        , $value['vl_total']                                  );
                        $obErro = $obTComprasSolicitacaoItemAnulacao->inclusao();
                    }
                }

                # LANÇA AS ANULAÇÕES DAS DOTAÇÕES SEM AGRUPAMENTO
                foreach ($arValores as $value) {
                    $nuVlAnular = str_replace(",",".",str_replace(".","",$value['vl_anular']));

                    if ($nuVlAnular > 0.00) {
                        if ($value['cod_conta'] && $value['cod_despesa']) {
                            $obTComprasSolicitacaoItemDotacaoAnulacao = new TComprasSolicitacaoItemDotacaoAnulacao();
                            $obTComprasSolicitacaoItemDotacaoAnulacao->setDado('exercicio'      , $_REQUEST['stExercicio']                              );
                            $obTComprasSolicitacaoItemDotacaoAnulacao->setDado('cod_entidade'   , $_REQUEST['stCodEntidade']                            );
                            $obTComprasSolicitacaoItemDotacaoAnulacao->setDado('cod_solicitacao', $_REQUEST['stCodSolicitacao']                         );
                            $obTComprasSolicitacaoItemDotacaoAnulacao->setDado('timestamp'      , $obTComprasSolicitacaoAnulacao->getDado('timestamp')  );
                            $obTComprasSolicitacaoItemDotacaoAnulacao->setDado('cod_centro'     , $value['cod_centro']                                  );
                            $obTComprasSolicitacaoItemDotacaoAnulacao->setDado('cod_item'       , $value['cod_item']                                    );
                            $obTComprasSolicitacaoItemDotacaoAnulacao->setDado('cod_conta'      , $value['cod_conta']                                   );
                            $obTComprasSolicitacaoItemDotacaoAnulacao->setDado('cod_despesa'    , $value['cod_despesa']                                 );
                            $obTComprasSolicitacaoItemDotacaoAnulacao->setDado('quantidade'     , $value['qnt_anular']                                   );
                            $obTComprasSolicitacaoItemDotacaoAnulacao->setDado('vl_anulacao'    , $nuVlAnular                                           );
                            $obErro = $obTComprasSolicitacaoItemDotacaoAnulacao->inclusao();
                        }
                    }
                }

                if ($obErro->ocorreu()) {
                      SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
                } else {
                    SistemaLegado::alertaAviso($pgList.'?'.Sessao::getId()."&stAcao=$stAcao","Número da solicitação: ".$_REQUEST['stCodSolicitacao'],"anular", "aviso", Sessao::getId(),"");
                }
            }
        }
        if (!$boAnula) {
            SistemaLegado::exibeAviso('Deve existir ao menos um item a anular.', "n_incluir", "erro" );
            SistemaLegado::LiberaFrames(true,true);
        }

    break;

    case "alterar":
        $arValores = Sessao::read('arValores');

        $rsRecordSetItem                  = new RecordSet;
        $rsContaDespesa                   = new RecordSet;
        $rsObjeto                         = new RecordSet;
        $obTComprasObjeto                 = new TComprasObjeto;
        $obTOrcamentoContaDespesa         = new TOrcamentoContaDespesa;
        $obTOrcamentoReservaSaldo 		  = new TOrcamentoReservaSaldos;
        $obTComprasSolicitacaoEntrega     = new TComprasSolicitacaoEntrega;
        $obTComprasSolicitacaoConvenio    = new TComprasSolicitacaoConvenio;
        $obTComprasSolicitacaoHomologacao = new TComprasSolicitacaoHomologada;
        $obTComprasSolicitacaoItemDotacao = new TComprasSolicitacaoItemDotacao;
        $obTSolicitacaoHomologadaReserva  = new TComprasSolicitacaoHomologadaReserva;

        Sessao::getTransacao()->setMapeamento( $obTComprasSolicitacao );

        $boHomologaAutomatico = SistemaLegado::pegaConfiguracao('homologacao_automatica', 35);
        $boReservaRigida      = SistemaLegado::pegaConfiguracao('reserva_rigida', 35);
        $boDotacaoObrigatoria = SistemaLegado::pegaConfiguracao('dotacao_obrigatoria_solicitacao', 35);

        $boReservaRigida      = ($boReservaRigida      == 'true') ? true : false;
        $boDotacaoObrigatoria = ($boDotacaoObrigatoria == 'true') ? true : false;
        $boHomologaAutomatico = ($boHomologaAutomatico == 'true') ? true : false;
        
        //Se a Solicitação for Registro de Preço, Não efetua Reserva de Saldo e Dotação Orçamentária Não é Obrigatória
        $boRegistroPreco = $_REQUEST['boRegistroPreco'];
        if($boRegistroPreco=='true'){
            $boReservaRigida = false;
            $boDotacaoObrigatoria = false;
        }

        # Verifica se o array de item não está vazio.
        if (count($arValores) == 0) {
            $stMensagem = 'Deve existir ao menos um item na lista.';
        }

        # Verifica se o Objeto foi preenchido e cadastra o objeto se necessário
        if ($_REQUEST['txtObjeto'] == "") {
            $stMensagem = "Campo objeto obrigatório.";
        } else {
            //Se está preenchido a descrição do objeto mas não tem o código, deve ser inserido no banco o novo objeto
            //Se tiver código informado verificar se é valido

            if (trim($_REQUEST['stObjeto']) == "") {
                //Se o código do objeto foi informado procura se já existe um objeto com esta descrição se não incluiu um novo
                $obTComprasObjeto->setDado( 'descricao',trim($_REQUEST['txtObjeto']) );
                $obTComprasObjeto->recuperaObjeto($rsObjeto);
                if ($rsObjeto->EOF()) {
                    $obNovoTComprasObjeto = new TComprasObjeto();
                    $obNovoTComprasObjeto->setDado( 'descricao', trim($_REQUEST['txtObjeto']) );
                    $obNovoTComprasObjeto->inclusao();
                    $obInObjeto = $obNovoTComprasObjeto->getDado( 'cod_objeto' );
                } else {
                    $obInObjeto = $rsObjeto->getCampo('cod_objeto');
                }
            } else {
                $obTComprasObjeto->setDado( 'cod_objeto',$_REQUEST['stObjeto'] );
                $obTComprasObjeto->recuperaObjeto($rsObjeto);
                if ($rsObjeto->EOF()) {
                    $stMensagem = 'O objeto informado não existe!.';
                } else {
                    $obInObjeto = $_REQUEST['stObjeto'];
                }
            }
        }

        $arDadosDespesa = array();
        foreach ($arValores as $arItens) {
            //verifica se a quantidade e o valor de cada item está preenchido
            if ($arItens['nuQuantidade'] == '') {
                $stMensagem = 'Você deve informar a quantidade.';
                break;
            }

            //se a dotação for obrigatória, verifica se ela foi setada
            if ($boDotacaoObrigatoria && $arItens['inCodDespesa'] == '') {
                $stMensagem = 'Existem itens sem dotação.';
                break;
            }

            if ($boReservaRigida) {
                if ( !isset($arDadosDespesa[$arItens['inCodDespesa']]) ) {
                    $obTOrcamentoDespesa = new TOrcamentoDespesa;
                    $obTOrcamentoDespesa->setDado( "cod_despesa", $arItens['inCodDespesa'] );
                    $obTOrcamentoDespesa->setDado( "exercicio", Sessao::getExercicio() );
                    $obTOrcamentoDespesa->recuperaSaldoDotacao( $rsRecordSet );
                    unset( $obTOrcamentoDespesa );
                    if ( !$rsRecordSet->Eof() ) {
                        $arDadosDespesa[$arItens['inCodDespesa']] = $rsRecordSet->getCampo("saldo_dotacao");
                        unset( $rsRecordSet );
                    }
                }

                $arDadosDespesa[$arItens['inCodDespesa']] -= str_replace( ',', '.', str_replace( '.', '', $arItens['nuVlTotal'] ) );
                if ($arDadosDespesa[$arItens['inCodDespesa']] < 0) {
                    $stMensagem = "Valor da Reserva é Superior ao Saldo da Dotação (".$arItens['inCodDespesa'].").";
                    break;
                }
            }
        }

        if (!$stMensagem) {
            $rsJaForam = new RecordSet;

            # Monta a Data da Solicitação para enviar ao relatório.
            $stDtSolicitacao = SistemaLegado::pegaDado("timestamp", "compras.solicitacao", "where cod_solicitacao = ".$_REQUEST['HdnInSolicitacao']." and cod_entidade = ".$_REQUEST['HdnCodEntidade']." and exercicio = '".$_REQUEST['hdnExercicio']."'");
            list($ano, $mes, $dia) = explode("-", substr($stDtSolicitacao, 0, 10));

            # Monta a Hora para enviar ao relatório.
            $stHoraSolicitacao = substr(str_replace('.', ':', $stDtSolicitacao), 11, strlen($stDtSolicitacao));
            list($hora, $minuto, $segundo) = explode(":", $stHoraSolicitacao);

            $stHoraSolicitacao = $hora.":".$minuto.":".$segundo;

            $stDtSolicitacaoFormatada = $dia."/".$mes."/".$ano;

            $obTComprasSolicitacao->setDado('exercicio'        , $_REQUEST['hdnExercicio']);
            $obTComprasSolicitacao->setDado('cod_entidade'     , $_REQUEST['HdnCodEntidade']);
            $obTComprasSolicitacao->setDado('cod_almoxarifado' , $_REQUEST['inCodAlmoxarifado']);
            $obTComprasSolicitacao->setDado('cod_solicitacao'  , $_REQUEST['HdnInSolicitacao']);
            $obTComprasSolicitacao->setDado('cgm_solicitante'  , $_REQUEST['inCGM']);
            $obTComprasSolicitacao->setDado('cgm_requisitante' , Sessao::read('numCgm'));
            $obTComprasSolicitacao->setDado('cod_objeto'       , $obInObjeto);
            $obTComprasSolicitacao->setDado('observacao'       , $_REQUEST['stObservacao']);
            $obTComprasSolicitacao->setDado('prazo_entrega'    , $_REQUEST['stPrazoEntrega']);
            $obTComprasSolicitacao->setDado('timestamp'        , $stDtSolicitacao);
            $obTComprasSolicitacao->setDado('registro_precos'  , $boRegistroPreco);
            $obTComprasSolicitacao->alteracao();

            $inSolicitacao = $obTComprasSolicitacao->getDado("cod_solicitacao");

            if ($_REQUEST['inCodConvenio']) {
                $stConvenio = explode ("-", $_REQUEST['inCodConvenio']);
                $exercicio_convenio = $stConvenio[0];
                $num_convenio = $stConvenio[1];

                $stFiltroConvenio  = " WHERE cod_solicitacao = ".$inSolicitacao."              \n";
                $stFiltroConvenio .= "   AND cod_entidade    = ".$_REQUEST['HdnCodEntidade']." \n";
                $stFiltroConvenio .= "   AND exercicio       = '".$_REQUEST['hdnExercicio']."'   \n";
                $obTComprasSolicitacaoConvenio->recuperaTodos($rsConvenio, $stFiltroConvenio);

                if ($rsConvenio->getNumLinhas() > 0) {
                    //altera o convênio
                    $obTComprasSolicitacaoConvenio->setDado('exercicio'          , $_REQUEST['hdnExercicio']);
                    $obTComprasSolicitacaoConvenio->setDado('cod_entidade'       , $_REQUEST['HdnCodEntidade']);
                    $obTComprasSolicitacaoConvenio->setDado('cod_solicitacao'    , $inSolicitacao);
                    $obTComprasSolicitacaoConvenio->setDado('num_convenio'       , $num_convenio);
                    $obTComprasSolicitacaoConvenio->setDado('exercicio_convenio' , $exercicio_convenio);
                    $obTComprasSolicitacaoConvenio->alteracao();
                } else {
                    //inclui na tabela compra.solicitacao_convenio se for selecionado na combo
                    $obTComprasSolicitacaoConvenio->setDado('exercicio'          , $_REQUEST['hdnExercicio']);
                    $obTComprasSolicitacaoConvenio->setDado('cod_entidade'       , $_REQUEST['HdnCodEntidade']);
                    $obTComprasSolicitacaoConvenio->setDado('cod_solicitacao'    , $inSolicitacao);
                    $obTComprasSolicitacaoConvenio->setDado('num_convenio'       , $num_convenio);
                    $obTComprasSolicitacaoConvenio->setDado('exercicio_convenio' , $exercicio_convenio);
                    $obTComprasSolicitacaoConvenio->inclusao();
                }
            }

            $obTComprasSolicitacaoEntrega->setDado('exercicio'       , $_REQUEST['hdnExercicio']);
            $obTComprasSolicitacaoEntrega->setDado('cod_entidade'    , $_REQUEST['HdnCodEntidade']);
            $obTComprasSolicitacaoEntrega->setDado('cod_solicitacao' , $inSolicitacao);
            $obTComprasSolicitacaoEntrega->setDado('numcgm'          , $_REQUEST['inEntrega']);
            $obTComprasSolicitacaoEntrega->alteracao();

            //seta informações para modificações na tabela solicitacao homologada
            $obTComprasSolicitacaoHomologacao = new TComprasSolicitacaoHomologada();
            $obTComprasSolicitacaoHomologacao->setDado('exercicio'		 , $_REQUEST['hdnExercicio']);
            $obTComprasSolicitacaoHomologacao->setDado('cod_entidade'	 , $_REQUEST['HdnCodEntidade']);
            $obTComprasSolicitacaoHomologacao->setDado('cod_solicitacao' , $inSolicitacao);
            $obTComprasSolicitacaoHomologacao->setDado('numcgm'		     , Sessao::read('numCgm'));

            # Verifica se a homologação esta anulada.
            $obTComprasSolicitacaoHomologacaoAnulacao = new TComprasSolicitacaoHomologadaAnulacao();
            $obTComprasSolicitacaoHomologacaoAnulacao->setDado('exercicio'		 , $_REQUEST['hdnExercicio']);
            $obTComprasSolicitacaoHomologacaoAnulacao->setDado('cod_entidade'	 , $_REQUEST['HdnCodEntidade']);
            $obTComprasSolicitacaoHomologacaoAnulacao->setDado('cod_solicitacao' , $inSolicitacao);
            $obTComprasSolicitacaoHomologacaoAnulacao->setDado('numcgm'			 , Sessao::read('numCgm'));
            $obTComprasSolicitacaoHomologacaoAnulacao->verificaExistenciaHomologacaoAnulada($rsSolicitacaoAnulacao, $stFiltro);

            $jaExisteSolicitacao = false;

            if ($rsSolicitacaoAnulacao->getNumLinhas() >0) {
                $jaExisteSolicitacao = true;
            }

            if ($boHomologaAutomatico == 'true') {
                if ($jaExisteSolicitacao == true) {
                    $obTComprasSolicitacaoHomologacaoAnulacao->exclusao();
                    $obTComprasSolicitacaoHomologacao->alteracao();
                } else {
                    $obTComprasSolicitacaoHomologacao->inclusao();
                }
            }
            
            $arExclui = Sessao::read('arValoresExcluidos');

            if (is_array($arExclui) && count($arExclui) > 0) {
                # Rotina para tratar os ítens que foram excluidos da solicitação.
                foreach ($arExclui as $stChave => $valor) {

                    if ($valor['inCodDespesa'] && $valor['inCodEstrutural']) {
                        # Busca a quantidade anulada do item (se houver) para deletar na item_dotacao_anulacao.
                        $obTComprasSolicitacaoItemDotacaoAnulacao = new TComprasSolicitacaoItemDotacaoAnulacao;
                        $obTComprasSolicitacaoItemDotacaoAnulacao->setDado("exercicio"       , $obTComprasSolicitacao->getDado('exercicio') );
                        $obTComprasSolicitacaoItemDotacaoAnulacao->setDado("cod_entidade"    , $obTComprasSolicitacao->getDado('cod_entidade') );
                        $obTComprasSolicitacaoItemDotacaoAnulacao->setDado("cod_solicitacao" , $obTComprasSolicitacao->getDado('cod_solicitacao') );
                        $obTComprasSolicitacaoItemDotacaoAnulacao->setDado("cod_centro"      , $valor['inCodCentroCusto'] );
                        $obTComprasSolicitacaoItemDotacaoAnulacao->setDado("cod_item"        , $valor['inCodItem'] );
                        $obTComprasSolicitacaoItemDotacaoAnulacao->setDado("cod_despesa"     , $valor['inCodDespesa'] );
                        $obTComprasSolicitacaoItemDotacaoAnulacao->setDado("cod_conta"       , $valor['inCodEstrutural'] );

                        $stFiltroAnulacaoDotacao  = " WHERE  solicitacao_item_dotacao_anulacao.exercicio       = '".$obTComprasSolicitacao->getDado('exercicio')."' \n";
                        $stFiltroAnulacaoDotacao .= "   AND  solicitacao_item_dotacao_anulacao.cod_entidade    = ".$obTComprasSolicitacao->getDado('cod_entidade')." \n";
                        $stFiltroAnulacaoDotacao .= "   AND  solicitacao_item_dotacao_anulacao.cod_solicitacao = ".$obTComprasSolicitacao->getDado('cod_solicitacao')." \n";
                        $stFiltroAnulacaoDotacao .= "   AND  solicitacao_item_dotacao_anulacao.cod_item        = ".$valor['inCodItem']." \n";
                        $stFiltroAnulacaoDotacao .= "   AND  solicitacao_item_dotacao_anulacao.cod_centro      = ".$valor['inCodCentroCusto']." \n";
                        $stFiltroAnulacaoDotacao .= "   AND  solicitacao_item_dotacao_anulacao.cod_despesa     = ".$valor['inCodDespesa']." \n";
                        $obTComprasSolicitacaoItemDotacaoAnulacao->recuperaTodos($rsRecordSetItemDotacaoAnulado, $stFiltroAnulacaoDotacao);

                        if ($rsRecordSetItemDotacaoAnulado->getNumLinhas() > 0) {
                            $obTComprasSolicitacaoItemDotacaoAnulacao->exclusao();
                        }

                        //Busca para deletar na item_dotacao.
                        $stFiltro = " WHERE  solicitacao_item_dotacao.cod_item        = ".$valor['inCodItem']."                                \n";
                        $stFiltro.= "   AND  solicitacao_item_dotacao.cod_centro      = ".$valor['inCodCentroCusto']."                         \n";
                        $stFiltro.= "   AND  solicitacao_item_dotacao.cod_despesa     = ".$valor['inCodDespesa']."                             \n";
                        $stFiltro.= "   AND  solicitacao_item_dotacao.cod_conta       = ".$valor['inCodEstrutural']."                          \n";
                        $stFiltro.= "   AND  solicitacao_item_dotacao.cod_solicitacao = ".$obTComprasSolicitacao->getDado("cod_solicitacao")." \n";
                        $stFiltro.= "   AND  solicitacao_item_dotacao.exercicio       = '".$_REQUEST['hdnExercicio']."'                        \n";
                        $obTComprasSolicitacaoItemDotacao->recuperaTodos($rsRecordSetItemD, $stFiltro);

                        if ($rsRecordSetItemD->getNumLinhas() > 0) {
                            $obTComprasSolicitacaoItemDotacao->setDado("exercicio" 		 , $_REQUEST['hdnExercicio']);
                            $obTComprasSolicitacaoItemDotacao->setDado("cod_entidade"	 , $_REQUEST['HdnCodEntidade']);
                            $obTComprasSolicitacaoItemDotacao->setDado("cod_solicitacao" , $rsRecordSetItemD->getCampo("cod_solicitacao"));
                            $obTComprasSolicitacaoItemDotacao->setDado("cod_centro"		 , $rsRecordSetItemD->getCampo("cod_centro"));
                            $obTComprasSolicitacaoItemDotacao->setDado("cod_item"		 , $rsRecordSetItemD->getCampo("cod_item"));
                            $obTComprasSolicitacaoItemDotacao->setDado("cod_despesa"     , $rsRecordSetItemD->getCampo("cod_despesa") );
                            $obTComprasSolicitacaoItemDotacao->setDado("cod_conta"		 , $rsRecordSetItemD->getCampo("cod_conta") );
                            $obTComprasSolicitacaoItemDotacao->exclusao();
                        }

                        $stFiltro  = " WHERE  solicitacao_item_dotacao.cod_item        = ".$valor['inCodItem']. "                               \n";
                        $stFiltro .= "   AND  solicitacao_item_dotacao.cod_centro      = ".$valor['inCodCentroCusto']. "                        \n";
                        $stFiltro .= "   AND  solicitacao_item_dotacao.cod_solicitacao = ".$obTComprasSolicitacao->getDado("cod_solicitacao")." \n";
                        $stFiltro .= "   AND  solicitacao_item_dotacao.exercicio       = '".$_REQUEST['hdnExercicio'] ."'                       \n";
                        $obTComprasSolicitacaoItemDotacao->recuperaTodos($rsRecordSetItem,$stFiltro);
                    }

                    # Busca anulada do item (se houver) para deletar na solicitacao_item_anulacao
                    $obTComprasSolicitacaoItemAnulacao = new TComprasSolicitacaoItemAnulacao();
                    $obTComprasSolicitacaoItemAnulacao->setDado("exercicio"      , $obTComprasSolicitacao->getDado('exercicio') );
                    $obTComprasSolicitacaoItemAnulacao->setDado("cod_entidade"   , $obTComprasSolicitacao->getDado('cod_entidade') );
                    $obTComprasSolicitacaoItemAnulacao->setDado("cod_solicitacao", $obTComprasSolicitacao->getDado('cod_solicitacao') );
                    $obTComprasSolicitacaoItemAnulacao->setDado("cod_item"       , $valor['inCodItem'] );
                    $obTComprasSolicitacaoItemAnulacao->setDado("cod_centro"     , $valor['inCodCentroCusto'] );

                    $stFiltroAnulacao = " WHERE solicitacao_item_anulacao.exercicio       = '".$obTComprasSolicitacao->getDado( 'exercicio')."'     \n";
                    $stFiltroAnulacao.= "   AND solicitacao_item_anulacao.cod_entidade    = ".$obTComprasSolicitacao->getDado( 'cod_entidade')."    \n";
                    $stFiltroAnulacao.= "   AND solicitacao_item_anulacao.cod_solicitacao = ".$obTComprasSolicitacao->getDado( 'cod_solicitacao')." \n";
                    $stFiltroAnulacao.= "   AND solicitacao_item_anulacao.cod_item        = ".$valor['inCodItem']." \n";
                    $stFiltroAnulacao.= "   AND solicitacao_item_anulacao.cod_centro      = ".$valor['inCodCentroCusto']." \n";
                    $obTComprasSolicitacaoItemAnulacao->recuperaTodos($rsRecordSetItemAnulado, $stFiltroAnulacao);

                    if ( $rsRecordSetItemAnulado->getNumLinhas() > 0) {
                        $obTComprasSolicitacaoItemAnulacao->exclusao();                        
                    }

                    # Busca do item para deletar na solicitacao_item
                    $stFiltro = " WHERE  solicitacao_item.cod_item        = ".$valor['inCodItem']. "                                  \n";
                    $stFiltro.= "   AND  solicitacao_item.cod_centro      = ".$valor['inCodCentroCusto']. "                           \n";
                    $stFiltro.= "   AND  solicitacao_item.cod_solicitacao = ".$obTComprasSolicitacao->getDado("cod_solicitacao")."    \n";
                    $stFiltro.= "   AND  solicitacao_item.exercicio       = '".$_REQUEST['hdnExercicio'] ."'                          \n";
                    $stFiltro.= "   AND  NOT EXISTS(SELECT *                                                                          \n";
                    $stFiltro.= "                    FROM compras.solicitacao_item_anulacao                                           \n";
                    $stFiltro.= "                   WHERE solicitacao_item.cod_item       =solicitacao_item_anulacao.cod_item         \n";
                    $stFiltro.= "                     AND solicitacao_item.cod_entidade   =solicitacao_item_anulacao.cod_entidade     \n";
                    $stFiltro.= "                     AND solicitacao_item.cod_centro     =solicitacao_item_anulacao.cod_centro       \n";
                    $stFiltro.= "                     AND solicitacao_item.exercicio      =solicitacao_item_anulacao.exercicio        \n";
                    $stFiltro.= "                     AND solicitacao_item.cod_solicitacao=solicitacao_item_anulacao.cod_solicitacao) \n";
                    $obTComprasSolicitacaoItem->recuperaTodos($rsRecordSetItemAnulado,$stFiltro);

                    if ($rsRecordSetItemAnulado->getNumLinhas() > 0) {
                        $obTComprasSolicitacaoItem->setDado("exercicio", $_REQUEST['hdnExercicio']);
                        $obTComprasSolicitacaoItem->setDado("cod_solicitacao", $obTComprasSolicitacao->getDado("cod_solicitacao"));
                        $obTComprasSolicitacaoItem->setDado("cod_item", $valor['inCodItem']);
                        $obTComprasSolicitacaoItem->setDado("cod_centro", $valor['inCodCentroCusto']);
                        $obTComprasSolicitacaoItem->exclusao();
                    }
                }
            }

            $stFiltro = " WHERE solicitacao_item.cod_solicitacao = ".$obTComprasSolicitacao->getDado( 'cod_solicitacao')."   \n";
            $stFiltro.= "   AND solicitacao_item.cod_entidade    = ".$obTComprasSolicitacao->getDado( 'cod_entidade')."      \n";
            $stFiltro.= "   AND solicitacao_item.exercicio       = '".$obTComprasSolicitacao->getDado( 'exercicio')."'       \n";
            $stFiltro.= "   AND NOT EXISTS(SELECT *                                                                          \n";
            $stFiltro.= "                    FROM compras.solicitacao_item_anulacao                                          \n";
            $stFiltro.= "                   WHERE solicitacao_item.cod_item       =solicitacao_item_anulacao.cod_item        \n";
            $stFiltro.= "                     AND solicitacao_item.cod_entidade   =solicitacao_item_anulacao.cod_entidade    \n";
            $stFiltro.= "                     AND solicitacao_item.cod_centro     =solicitacao_item_anulacao.cod_centro      \n";
            $stFiltro.= "                     AND solicitacao_item.exercicio      =solicitacao_item_anulacao.exercicio       \n";
            $stFiltro.= "                     AND solicitacao_item.cod_solicitacao=solicitacao_item_anulacao.cod_solicitacao)\n";

            $obTComprasSolicitacaoItem->recuperaItem($rsJaForam,$stFiltro);

            while (!$rsJaForam->eof()) {
                $stKeyDb = $rsJaForam->getCampo('cod_item').'-'.$rsJaForam->getCampo('cod_centro').'-'.$obTComprasSolicitacao->getDado( 'cod_solicitacao' );
                $arItensChave[$stKeyDb] = true;
                $rsJaForam->proximo();
            }

            $arValoresAux = $arValores;
            $arAgrupadoValores = array();
            if (is_array($arValores)) {
                foreach ($arValores as $inChave => $arDados) {

                    if (!isset($arAgrupadoValores[$arDados['inCodItem'].'-'.$arDados['inCodCentroCusto']])) {
                        $stChave = $arDados['inCodItem'].'-'.$arDados['inCodCentroCusto'];
                        $arAgrupadoValores[$stChave]['valor'] = str_replace(',','.',str_replace('.','',$arDados['nuVlTotal']));
                        $arAgrupadoValores[$stChave]['quantidade'] = str_replace(',','.',str_replace('.','',$arDados['nuQuantidade']));
                        $arAgrupadoValores[$stChave]['complemento'] = $arDados['stComplemento'];
                        foreach ($arValoresAux as $inChaveAux => $arDadosAux) {
                            if (($arDados['id'] != $arDadosAux['id'])
                            && ($arDados['inCodItem'] == $arDadosAux['inCodItem'] && $arDados['inCodCentroCusto'] == $arDadosAux['inCodCentroCusto'])) {
                                $stChave = $arDados['inCodItem'].'-'.$arDados['inCodCentroCusto'];
                                $arAgrupadoValores[$stChave]['valor'] += str_replace(',','.',str_replace('.','',$arDadosAux['nuVlTotal']));
                                $arAgrupadoValores[$stChave]['quantidade'] += number_format(str_replace(',', '.',$arDadosAux['nuQuantidade']),4,'.','');
                                $arAgrupadoValores[$stChave]['complemento'] = $arDadosAux['stComplemento'];
                                unset($arValoresAux[$inChaveAux]);
                            }
                        }
                    }
                }
            }

            # Inclusão dos itens da Solicitação de Compras.
            foreach ($arAgrupadoValores as $inChave => $arDados) {
                $arChave = explode('-',$inChave );
                $inCodItem = $arChave[0];
                $inCodCentroCusto = $arChave[1];

                $stFiltro = " WHERE  solicitacao_item.cod_solicitacao = ".$obTComprasSolicitacao->getDado( 'cod_solicitacao')." \n";
                $stFiltro.= "   AND  solicitacao_item.cod_entidade    = ".$obTComprasSolicitacao->getDado( 'cod_entidade')."    \n";
                $stFiltro.= "   AND  solicitacao_item.exercicio       = '".$obTComprasSolicitacao->getDado( 'exercicio')."'     \n";
                $stFiltro.= "   AND  solicitacao_item.cod_item        = ".$inCodItem."         									\n";
                $stFiltro.= "   AND  solicitacao_item.cod_centro      = ".$inCodCentroCusto."									\n";

                $obTComprasSolicitacaoItem->recuperaTodos($rsRecordSetItem, $stFiltro);

                $stKeyNew = $inCodItem.'-'.$inCodCentroCusto.'-'.$obTComprasSolicitacao->getDado('cod_solicitacao');

                //inclui os itens da solicitação
                $obTComprasSolicitacaoItem->setDado('exercicio'       , $_REQUEST['hdnExercicio']);
                $obTComprasSolicitacaoItem->setDado('cod_entidade'    , $_REQUEST['HdnCodEntidade']);
                $obTComprasSolicitacaoItem->setDado('cod_solicitacao' , $obTComprasSolicitacao->getDado('cod_solicitacao'));
                $obTComprasSolicitacaoItem->setDado('cod_centro'      , $inCodCentroCusto);
                $obTComprasSolicitacaoItem->setDado('cod_item'        , $inCodItem);
                $obTComprasSolicitacaoItem->setDado('complemento'     , stripslashes($arDados['complemento']));

                $obTComprasSolicitacaoItemAnulacao = new TComprasSolicitacaoItemAnulacao();
                $stFiltro = " WHERE solicitacao_item_anulacao.exercicio       = '".$obTComprasSolicitacao->getDado( 'exercicio')."'     \n";
                $stFiltro.= "   AND solicitacao_item_anulacao.cod_entidade    = ".$obTComprasSolicitacao->getDado( 'cod_entidade')."    \n";
                $stFiltro.= "   AND solicitacao_item_anulacao.cod_solicitacao = ".$obTComprasSolicitacao->getDado( 'cod_solicitacao')." \n";
                $stFiltro.= "   AND solicitacao_item_anulacao.cod_item        = ".$inCodItem."         								    \n";
                $stFiltro.= "   AND solicitacao_item_anulacao.cod_centro      = ".$inCodCentroCusto."								    \n";
                $obTComprasSolicitacaoItemAnulacao->recuperaTodos($rsRecordSetItemAnulado, $stFiltro);

                if ($rsRecordSetItemAnulado->EOF()) {
                    $obTComprasSolicitacaoItem->setDado('quantidade' , $arDados['quantidade'] );
                    $obTComprasSolicitacaoItem->setDado('vl_total'   , $arDados['valor']      );
                } else {
                    $quantidade = ($arDados['quantidade'] + $rsRecordSetItemAnulado->getCampo('quantidade'));
                    $valor      = ($arDados['valor']      + $rsRecordSetItemAnulado->getCampo('vl_total'));

                    $obTComprasSolicitacaoItem->setDado('quantidade' , $quantidade);
                    $obTComprasSolicitacaoItem->setDado('vl_total'   , $valor     );
                }

                if ($rsRecordSetItem->EOF()) {
                    $obTComprasSolicitacaoItem->inclusao();
                } else {
                    $obTComprasSolicitacaoItem->alteracao();
                }
            }

            # Faz a exclusão de todas as dotações para inserir posteriormente.
            $obTComprasSolicitacaoItemDotacao = new TComprasSolicitacaoItemDotacao();
            $obTComprasSolicitacaoItemDotacao->setDado('exercicio'       , $_REQUEST['hdnExercicio']);
            $obTComprasSolicitacaoItemDotacao->setDado('cod_entidade'    , $_REQUEST['HdnCodEntidade']);
            $obTComprasSolicitacaoItemDotacao->setDado('cod_solicitacao' , $_REQUEST['HdnInSolicitacao']);
            $obTComprasSolicitacaoItemDotacao->exclusao();

            # Rotina para controlar as dotações, reservas de saldo.
            foreach ($arValores as $arItens) {
                # Caso tenha sido informado dotação é obrigatório informar o desdobramento, não permitindo mais usar o desdobramento padrão.
                if (is_numeric($arItens['inCodDespesa']) && isset($arItens['inCodDespesa'])) {
                    $obTComprasSolicitacaoItemDotacao->setDado('exercicio'       , $_REQUEST['hdnExercicio']);
                    $obTComprasSolicitacaoItemDotacao->setDado('cod_entidade'    , $_REQUEST['HdnCodEntidade']);
                    $obTComprasSolicitacaoItemDotacao->setDado('cod_solicitacao' , $obTComprasSolicitacao->getDado("cod_solicitacao"));
                    $obTComprasSolicitacaoItemDotacao->setDado('cod_centro'      , $arItens['inCodCentroCusto']);
                    $obTComprasSolicitacaoItemDotacao->setDado('cod_item'        , $arItens['inCodItem']);
                    $obTComprasSolicitacaoItemDotacao->setDado('vl_reserva'      , $arItens['nuVlTotal']);
                    $obTComprasSolicitacaoItemDotacao->setDado('cod_despesa'     , $arItens['inCodDespesa']);
                    $obTComprasSolicitacaoItemDotacao->setDado('cod_conta'       , $arItens['inCodEstrutural']);

                    # Busca a quantidade anulada do item (se houver) para inserir na item_dotacao.
                    $obTComprasSolicitacaoItemDotacaoAnulacao = new TComprasSolicitacaoItemDotacaoAnulacao;
                    $stFiltro  = " WHERE  solicitacao_item_dotacao_anulacao.exercicio       = '".$obTComprasSolicitacao->getDado('exercicio')."'       \n";
                    $stFiltro .= "   AND  solicitacao_item_dotacao_anulacao.cod_entidade    = ".$obTComprasSolicitacao->getDado('cod_entidade')."    \n";
                    $stFiltro .= "   AND  solicitacao_item_dotacao_anulacao.cod_solicitacao = ".$obTComprasSolicitacao->getDado('cod_solicitacao')." \n";
                    $stFiltro .= "   AND  solicitacao_item_dotacao_anulacao.cod_item        = ".$arItens['inCodItem']."								 \n";
                    $stFiltro .= "   AND  solicitacao_item_dotacao_anulacao.cod_centro      = ".$arItens['inCodCentroCusto']."						 \n";
                    $stFiltro .= "   AND  solicitacao_item_dotacao_anulacao.cod_despesa     = ".$arItens['inCodDespesa']."							 \n";

                    $obTComprasSolicitacaoItemDotacaoAnulacao->recuperaTodos($rsRecordSetItemDotacaoAnulado, $stFiltro);

                    if ($rsRecordSetItemDotacaoAnulado->EOF()) {
                        $obTComprasSolicitacaoItemDotacao->setDado('quantidade' , $arItens['nuQuantidade']);
                    } else {
                        $quantidade = ($arItens['nuQuantidade'] + $rsRecordSetItemDotacaoAnulado->getCampo('quantidade'));
                        $obTComprasSolicitacaoItemDotacao->setDado('quantidade' , $quantidade);
                    }

                    # Inclusão na tabela compras.solicitacao_item_dotacao
                    $obTComprasSolicitacaoItemDotacao->inclusao();

                    $numcgm      = SistemaLegado::pegaDado('numcgm','orcamento.entidade', "where cod_entidade =".$_REQUEST['HdnCodEntidade']." and exercicio = '".Sessao::getExercicio()."'");
                    $nomEntidade = SistemaLegado::pegaDado('nom_cgm','sw_cgm', "where numcgm =".$numcgm);

                    $nuVlrReserva = 0;
                    $nuVlrReserva = str_replace(',','.',str_replace('.','',$arItens['nuVlTotal']));

                    # Inclusão na tabela orcamento.reserva_saldo (GF)
                    $obTOrcamentoReservaSaldo->setDado('exercicio'          , $_REQUEST['hdnExercicio']);
                    $obTOrcamentoReservaSaldo->proximoCod( $inCodReserva );
                    $obTOrcamentoReservaSaldo->setDado('cod_reserva',         $inCodReserva);
                    $obTOrcamentoReservaSaldo->setDado('cod_despesa'        , $arItens['inCodDespesa']);
                    $obTOrcamentoReservaSaldo->setDado('dt_validade_inicial', $stDtSolicitacaoFormatada);
                    $obTOrcamentoReservaSaldo->setDado('tipo'               , 'A');
                    $obTOrcamentoReservaSaldo->setDado('dt_inclusao'        , $stDtSolicitacaoFormatada);
                    $obTOrcamentoReservaSaldo->setDado('motivo'             , 'Entidade: '.$_REQUEST['HdnCodEntidade'].' - '.$nomEntidade.', solicitação de compras: '.$inSolicitacao.'/'.Sessao::getExercicio());
                    $obTOrcamentoReservaSaldo->setDado('vl_reserva'         , $nuVlrReserva		   );
                    $obTOrcamentoReservaSaldo->setDado('dt_validade_final'  , '31/12/'.Sessao::getExercicio() );

                    if ($boHomologaAutomatico && $boReservaRigida && $nuVlrReserva > 0 && $boRegistroPreco=='false') {
                        if ($obTOrcamentoReservaSaldo->incluiReservaSaldo()) {
                            # Inclusão na tabela compras.solicitacao_homologada_reserva
                            $obTSolicitacaoHomologadaReserva = new TComprasSolicitacaoHomologadaReserva;
                            $obTSolicitacaoHomologadaReserva->setDado('exercicio'       , $_REQUEST['hdnExercicio']);
                            $obTSolicitacaoHomologadaReserva->setDado('cod_entidade'    , $_REQUEST['HdnCodEntidade']);
                            $obTSolicitacaoHomologadaReserva->setDado('cod_solicitacao' , $inSolicitacao);
                            $obTSolicitacaoHomologadaReserva->setDado('cod_item'        , $arItens['inCodItem']);
                            $obTSolicitacaoHomologadaReserva->setDado('cod_centro'      , $arItens['inCodCentroCusto']);
                            $obTSolicitacaoHomologadaReserva->setDado('cod_reserva'     , $inCodReserva);
                            $obTSolicitacaoHomologadaReserva->setDado('cod_conta'       , $arItens['inCodEstrutural']);
                            $obTSolicitacaoHomologadaReserva->setDado('cod_despesa'     , $arItens['inCodDespesa']);
                            $obTSolicitacaoHomologadaReserva->obTOrcamentoReservaSaldos = & $obTOrcamentoReservaSaldo;
                            $obTSolicitacaoHomologadaReserva->inclusao();
                        }
                    }
                }
            }

            if ($_REQUEST['boRelatorio']) {
                $pgDestino = $pgRel;
            } elseif ($boHomologaAutomatico == 'true') {
                $pgDestino = $pgFilt;
            } else {
                $pgDestino = $pgList;
            }

            SistemaLegado::alertaAviso($pgDestino."&dtSolicitacao=".$stDtSolicitacaoFormatada."&stHoraSolicitacao=".$stHoraSolicitacao."&inSolicitacao=".$inSolicitacao."&inEntidade=".$obTComprasSolicitacao->getDado('cod_entidade')."&boRegistroPreco=".$boRegistroPreco,"Número da solicitação: ".$inSolicitacao,"alterar", "aviso", Sessao::getId(),"");
        } else {
            SistemaLegado::exibeAviso(urlencode($stMensagem), "n_alterar", "erro" );
            SistemaLegado::LiberaFrames(true,true);
        }

    break;

    case "incluir":
        $arValores = Sessao::read('arValores');

        $rsRecordSetItem                  = new RecordSet;
        $rsContaDespesa                   = new RecordSet;
        $rsObjeto                         = new RecordSet;
        $obTComprasSolicitacaoItemDotacao = new TComprasSolicitacaoItemDotacao();
        $obTComprasSolicitacaoEntrega     = new TComprasSolicitacaoEntrega();
        $obTComprasObjeto                 = new TComprasObjeto();
        $obTOrcamentoContaDespesa         = new TOrcamentoContaDespesa();
        $obTOrcamentoReservaSaldo 		  = new TOrcamentoReservaSaldos;
        $obTSolicitacaoHomologadaReserva  = new TComprasSolicitacaoHomologadaReserva;
        $obTComprasSolicitacaoHomologacao = new TComprasSolicitacaoHomologada();
        $obTComprasSolicitacaoConvenio    = new TComprasSolicitacaoConvenio();

        Sessao::getTransacao()->setMapeamento( $obTComprasSolicitacao );

        //pega as configurações do sistema
        $inCodUf = SistemaLegado::pegaConfiguracao('cod_uf', 2);
        $boHomologaAutomatico = SistemaLegado::pegaConfiguracao ('homologacao_automatica', 35);
        $boReservaRigida = SistemaLegado::pegaConfiguracao		('reserva_rigida'        , 35);
        $boDotacaoObrigatoria = SistemaLegado::pegaConfiguracao	('dotacao_obrigatoria_solicitacao', 35);

        $boReservaRigida      = ( $boReservaRigida      == 'true' ) ? true : false;
        $boDotacaoObrigatoria = ( $boDotacaoObrigatoria == 'true' ) ? true : false;
        $boHomologaAutomatico = ( $boHomologaAutomatico == 'true' ) ? true : false;
        
        //Se a Solicitação for Registro de Preço, Não efetua Reserva de Saldo e Dotação Orçamentária Não é Obrigatória
        $boRegistroPreco = $request->get('boRegistroPreco');
        if($boRegistroPreco=='true'){
            $boReservaRigida = false;
            $boDotacaoObrigatoria = false;
        }

        # Valida a data da solicitação que deve ser informada obrigatoriamente.
        if ( !empty($_REQUEST['stDtSolicitacao']) ) {
            # Não pode ser menor que a data da Ultima autorização.
            if (!SistemaLegado::comparaDatas($request->get('stDtSolicitacao'), $request->get('HdnDtSolicitacao'), true)) {
                $stMensagem = "A data da solicitação não pode ser menor que a data da última autorização (".$_REQUEST['HdnDtSolicitacao'].")";
            }
            # Não pode ser maior que a data corrente.
            if (!SistemaLegado::comparaDatas(date('d/m/Y'), $request->get('stDtSolicitacao'), true)) {
                $stMensagem = "A data da solicitação não pode ser maior que a data atual.";
            }
        } else {
            $stMensagem = "A data da solicitação não pode ser vazia.";
        }

        # Verifica se o existem itens a serem cadastrados
        if (count($arValores) == 0) {
            $stMensagem = "Deve existir ao menos um item na lista.";
        }

        # Verifica se o Objeto foi preenchido e cadastra o objeto se necessário
        if ($request->get('txtObjeto') == "") {
            $stMensagem = "Campo objeto obrigatório.";
        } else {
            //Se está preenchido a descrição do objeto mas não tem o código, deve ser inserido no banco o novo objeto
            //Se tiver código informado verificar se é valido

            if (trim($request->get('stObjeto')) == "") {
                //Se somente a descrição foi informada procura se já existe um objeto com esta descrição se não, incluiu um novo
                $obTComprasObjeto->setDado( 'descricao',trim($request->get('txtObjeto')) );
                $obTComprasObjeto->recuperaObjeto($rsObjeto);
                if ($rsObjeto->EOF()) {
                    $obNovoTComprasObjeto = new TComprasObjeto();
                    $obNovoTComprasObjeto->setDado( 'descricao', trim($request->get('txtObjeto')) );
                    $obNovoTComprasObjeto->inclusao();
                    $obInObjeto = $obNovoTComprasObjeto->getDado( 'cod_objeto' );
                } else {
                    $obInObjeto = $rsObjeto->getCampo('cod_objeto');
                }
            } else {
                //Valida o código do objeto informado
                $obTComprasObjeto->setDado( 'cod_objeto',$request->get('stObjeto') );
                $obTComprasObjeto->recuperaObjeto($rsObjeto);
                if ( $rsObjeto->EOF() ) {
                    $stMensagem = 'O objeto informado não existe!';
                } else {
                    $obInObjeto = $request->get('stObjeto');
                }
            }
        }

        $arDadosDespesa = array();
        foreach ($arValores as $arItens) {
            //verifica se a quantidade e o valor de cada item está preenchido
            if ($arItens['nuQuantidade'] == '') {
                $stMensagem = 'Você deve informar a quantidade de cada item.';
                break;
            }

            //se a dotação for obrigatória, verifica se ela foi setada
            if ($boDotacaoObrigatoria && $arItens['inCodDespesa'] == '') {
                $stMensagem = 'Existem itens sem dotação.';
                break;
            }

           
            if ($boReservaRigida) {
                if ( !isset($arDadosDespesa[$arItens['inCodDespesa']]) ) {
                    $obTOrcamentoDespesa = new TOrcamentoDespesa;
                    $obTOrcamentoDespesa->setDado( "cod_despesa", $arItens['inCodDespesa'] );
                    $obTOrcamentoDespesa->setDado( "exercicio", Sessao::getExercicio() );
                    $obTOrcamentoDespesa->recuperaSaldoDotacao( $rsRecordSet );
                    unset( $obTOrcamentoDespesa );
                    if ( !$rsRecordSet->Eof() ) {
                        $arDadosDespesa[$arItens['inCodDespesa']] = $rsRecordSet->getCampo("saldo_dotacao");
                        unset( $rsRecordSet );
                    }
                }

                $arDadosDespesa[$arItens['inCodDespesa']] -= str_replace( ',', '.', str_replace( '.', '', $arItens['nuVlTotal'] ) );

                if ($arDadosDespesa[$arItens['inCodDespesa']] < 0) {
                    $stMensagem = "Valor da Reserva é Superior ao Saldo da Dotação (".$arItens['inCodDespesa'].").";
                    break;
                }
            }
            
        }

        if (!$stMensagem) {
            // Trata a data da Solicitação para ser inserida como Timestamp.
            list($dia, $mes, $ano) = explode("/", $request->get('stDtSolicitacao'));
            $stDtSolicitacao   = $ano."-".$mes."-".$dia;
            $stHoraSolicitacao = date('H:i:s.ms');

            //inclui a solicitação
            $obTComprasSolicitacao->setDado('exercicio'         , Sessao::getExercicio());
            $obTComprasSolicitacao->setDado('cod_entidade'      , $request->get('HdnCodEntidade'));
            $obTComprasSolicitacao->setDado('cod_almoxarifado'  , $request->get('inCodAlmoxarifado'));
            $obTComprasSolicitacao->setDado('cgm_solicitante'   , $request->get('inCGM'));
            $obTComprasSolicitacao->setDado('cgm_requisitante'  , Sessao::read('numCgm'));
            $obTComprasSolicitacao->setDado('cod_objeto'        , $obInObjeto);
            $obTComprasSolicitacao->setDado('observacao'        , $request->get('stObservacao'));
            $obTComprasSolicitacao->setDado('prazo_entrega'     , $request->get('stPrazoEntrega'));
            $obTComprasSolicitacao->setDado('timestamp'         , $stDtSolicitacao." ".$stHoraSolicitacao);
            $obTComprasSolicitacao->setDado('registro_precos'   , $boRegistroPreco);
            $obTComprasSolicitacao->inclusao();

            # Monta a Hora para enviar ao relatório.
            $stHoraSolicitacao = substr(str_replace('.', ':', $stHoraSolicitacao), 0, strlen($stHoraSolicitacao));
            list($hora, $minuto, $segundo) = explode(":", $stHoraSolicitacao);

            $stHoraSolicitacao = $hora.":".$minuto.":".$segundo;

            $inSolicitacao = $obTComprasSolicitacao->getDado("cod_solicitacao");

            if ($request->get('inCodConvenio')) {
                //inclui na tabela compra.solicitacao_convenio se for selecionado na combo
                $stConvenio = explode ("-", $request->get('inCodConvenio'));
                $exercicio_convenio = $stConvenio[0];
                $num_convenio = $stConvenio[1];

                $obTComprasSolicitacaoConvenio->setDado( 'exercicio'         , Sessao::getExercicio()         );
                $obTComprasSolicitacaoConvenio->setDado( 'cod_entidade'      , $request->get('HdnCodEntidade')    );
                $obTComprasSolicitacaoConvenio->setDado( 'cod_solicitacao'   , $inSolicitacao                 );
                $obTComprasSolicitacaoConvenio->setDado( 'num_convenio'      , $num_convenio                 );
                $obTComprasSolicitacaoConvenio->setDado( 'exercicio_convenio', $exercicio_convenio            );
                $obTComprasSolicitacaoConvenio->inclusao();
            }

            //inclui na tabela solicitacao_entrega
            $obTComprasSolicitacaoEntrega->setDado( 'exercicio'      ,Sessao::getExercicio()         );
            $obTComprasSolicitacaoEntrega->setDado( 'cod_entidade'   ,$request->get('HdnCodEntidade') );
            $obTComprasSolicitacaoEntrega->setDado( 'cod_solicitacao',$inSolicitacao             );
            $obTComprasSolicitacaoEntrega->setDado( 'numcgm'         ,$request->get('inEntrega')     );
            $obTComprasSolicitacaoEntrega->inclusao();

            $arValoresAux = $arValores;
            $arAgrupadoValores = array();
            if (is_array($arValores)) {
                foreach ($arValores as $inChave => $arDados) {
                    if (!isset($arAgrupadoValores[$arDados['inCodItem'].'-'.$arDados['inCodCentroCusto']])) {
                        $stChave = $arDados['inCodItem'].'-'.$arDados['inCodCentroCusto'];
                        $arAgrupadoValores[$stChave]['valor'] =  str_replace(',','.',str_replace('.','',$arDados['nuVlTotal']));
                        $arAgrupadoValores[$stChave]['quantidade'] = str_replace(',','.',str_replace('.','',$arDados['nuQuantidade']));
                        $arAgrupadoValores[$stChave]['complemento'] = $arDados['stComplemento'];
                        foreach ($arValoresAux as $inChaveAux => $arDadosAux) {
                            if (($arDados['id'] != $arDadosAux['id'])
                                && ($arDados['inCodItem'] == $arDadosAux['inCodItem']
                                    && $arDados['inCodCentroCusto'] == $arDadosAux['inCodCentroCusto'])) {
                                $stChave = $arDados['inCodItem'].'-'.$arDados['inCodCentroCusto'];
                                $arAgrupadoValores[$stChave]['valor'] += str_replace(',','.',str_replace('.','',$arDadosAux['nuVlTotal']));
                                $arAgrupadoValores[$stChave]['quantidade'] += str_replace(',','.',str_replace('.','',$arDadosAux['nuQuantidade']));
                                $arAgrupadoValores[$stChave]['complemento'] = $arDadosAux['stComplemento'];
                                unset($arValoresAux[$inChaveAux]);
                            }
                        }
                    }
                }
            }

            foreach ($arAgrupadoValores as $inChave => $arDados) {
                $arChave = explode('-',$inChave );
                $inCodItem = $arChave[0];
                $inCodCentroCusto = $arChave[1];

                //inclui os itens da solicitação
                $obTComprasSolicitacaoItem->setDado('exercicio'      , Sessao::getExercicio());
                $obTComprasSolicitacaoItem->setDado('cod_entidade'   , $request->get('HdnCodEntidade'));
                $obTComprasSolicitacaoItem->setDado('cod_solicitacao', $inSolicitacao);
                $obTComprasSolicitacaoItem->setDado('cod_centro'     , $inCodCentroCusto);
                $obTComprasSolicitacaoItem->setDado('cod_item'       , $inCodItem);
                $obTComprasSolicitacaoItem->setDado('complemento'    , stripslashes($arDados['complemento']));
                $obTComprasSolicitacaoItem->setDado('quantidade'     , $arDados['quantidade']);
                $obTComprasSolicitacaoItem->setDado('vl_total'       , $arDados['valor']);

                $obTComprasSolicitacaoItem->inclusao();
            }

            $nroItensReservaSaldo = 0;

            foreach( $arValores as $key => $arItens ) {
                //inclui a dotação caso tenha sido selecionado uma
                if ($arItens['inCodDespesa'] != '') {
                    $stFiltro = " AND D.cod_despesa     = ".$arItens['inCodDespesa']."\n";
                    $stFiltro.= " AND CD.exercicio      ='".Sessao::getExercicio()."'          \n";

                    $obTOrcamentoContaDespesa->recuperaRelacionamento( $rsContaDespesa , $stFiltro );

                    $stSql =" WHERE solicitacao_item_dotacao.cod_entidade =".$request->get('HdnCodEntidade')."                                \n";
                    $stSql.=" AND solicitacao_item_dotacao.cod_solicitacao=".$inSolicitacao."    \n";
                    $stSql.=" AND solicitacao_item_dotacao.cod_centro=".$arItens['inCodCentroCusto']."\n";
                    $stSql.=" AND solicitacao_item_dotacao.cod_item  =".$arItens['inCodItem']."       \n";
                    $stSql.=" AND solicitacao_item_dotacao.exercicio ='".Sessao::getExercicio()."'                                           \n";
                    $obTComprasSolicitacaoItemDotacao->recuperaTodos($rsRecordSetItem,$stSql);

                    $obTComprasSolicitacaoItemDotacao->setDado('exercicio'      ,Sessao::getExercicio()		       );
                    $obTComprasSolicitacaoItemDotacao->setDado('cod_entidade'   ,$request->get('HdnCodEntidade')   		       );
                    $obTComprasSolicitacaoItemDotacao->setDado('cod_solicitacao',$inSolicitacao            );
                    $obTComprasSolicitacaoItemDotacao->setDado('cod_centro'     ,$arItens['inCodCentroCusto']  );
                    $obTComprasSolicitacaoItemDotacao->setDado('cod_item'       ,$arItens['inCodItem']         );
                    $obTComprasSolicitacaoItemDotacao->setDado('vl_reserva'     ,str_replace(',','.',str_replace('.','',$arItens['nuVlTotal'])));
                    if ($arItens['inCodEstrutural']=="") {
                        $obTComprasSolicitacaoItemDotacao->setDado('cod_conta',$rsContaDespesa->getCampo('cod_conta')                         );
                    } else {
                        $obTComprasSolicitacaoItemDotacao->setDado('cod_conta',$arItens['inCodEstrutural'] );
                    }
                    $obTComprasSolicitacaoItemDotacao->setDado('cod_despesa'    ,$arItens['inCodDespesa']      );
                    $obTComprasSolicitacaoItemDotacao->setDado('quantidade'     ,$arItens['nuQuantidade']      );
                    $obTComprasSolicitacaoItemDotacao->inclusao();

                    $numcgm = SistemaLegado::pegaDado('numcgm','orcamento.entidade', "where cod_entidade =".$_REQUEST['HdnCodEntidade']." and exercicio = '".Sessao::getExercicio()."'");
                    $nomEntidade = SistemaLegado::pegaDado('nom_cgm','sw_cgm', "where numcgm =".$numcgm);

                    $nuVlReserva = 0;
                    $nuVlReserva = str_replace(',','.',str_replace('.','',$arItens['nuVlTotal']));

                    //Faz a inclusão na tabela orcamento.reserva_saldos se NÃO for Registro de Preço
                    if ($nuVlReserva > 0 && $boRegistroPreco=='false') {
                        //inclusão na tabela orcamento.reserva_saldo
                        $obTOrcamentoReservaSaldo->proximoCod( $inCodReserva );

                        $obTOrcamentoReservaSaldo->setDado( 'exercicio'          , Sessao::getExercicio()	        );
                        $obTOrcamentoReservaSaldo->setDado( 'cod_reserva',         $inCodReserva                    );
                        $obTOrcamentoReservaSaldo->setDado( 'cod_despesa'        , $arItens['inCodDespesa']         );
                        $obTOrcamentoReservaSaldo->setDado( 'dt_validade_inicial', $request->get('stDtSolicitacao')     );
                        $obTOrcamentoReservaSaldo->setDado( 'tipo'               , 'A'                              );
                        $obTOrcamentoReservaSaldo->setDado( 'dt_inclusao'        , $request->get('stDtSolicitacao')     );
                        $obTOrcamentoReservaSaldo->setDado( 'motivo'             , "Entidade: ".$request->get('HdnCodEntidade')." - ".$nomEntidade.", solicitação de compras: ".$inSolicitacao."/".Sessao::getExercicio().', Item:'.$arItens['inCodItem']);
                        $obTOrcamentoReservaSaldo->setDado( 'vl_reserva'         ,  $nuVlReserva	                );
                        $obTOrcamentoReservaSaldo->setDado( 'dt_validade_final'  , '31/12/'.Sessao::getExercicio()  );
                        
                        //Caso for estado de MG não deve validar a homologacao automatica já que lá é tudo manual
                        //Atribuindo o valor de true na variavel de homolagacao para nao afetar outros estados
                        if ( $inCodUf == 11 ) {
                            $boHomologaAutomatico = true;
                        }

                        if ($boHomologaAutomatico && $boReservaRigida) {                            
                            if ( $obTOrcamentoReservaSaldo->incluiReservaSaldo() ) {
                                $arValores[$key]['inCodReserva'] = $inCodReserva;
                                $nroItensReservaSaldo++;
                                $arCodItemReserva[$arItens['inCodItem'].'-'.$arItens['inCodCentroCusto'].'-'.$arItens['inCodDespesa']]['temReserva'] = true;
                                $arCodItemReserva[$arItens['inCodItem'].'-'.$arItens['inCodCentroCusto'].'-'.$arItens['inCodDespesa']]['codReserva'] = $inCodReserva;
                            } else {
                                $arCodItemReserva[$arItens['inCodItem'].'-'.$arItens['inCodCentroCusto'].'-'.$arItens['inCodDespesa']]['temReserva'] = false;
                            }
                        }
                    }
                }
            }

            $boIncluiHomologacaoReserva = false;

            if ($boReservaRigida) {
                if ($boHomologaAutomatico) {
                    if ($nroItensReservaSaldo == count($arValores)) {
                        $obTComprasSolicitacaoHomologacao->setDado( 'exercicio'		 	,Sessao::getExercicio() 		);
                        $obTComprasSolicitacaoHomologacao->setDado( 'cod_entidade'	 	,$request->get('HdnCodEntidade') );
                        $obTComprasSolicitacaoHomologacao->setDado( 'cod_solicitacao'	,$inSolicitacao 			);
                        $obTComprasSolicitacaoHomologacao->setDado( 'numcgm'			,Sessao::read('numCgm') 			);
                        $obTComprasSolicitacaoHomologacao->inclusao();
                        $boIncluiHomologacaoReserva = true;
                    } else {
                        $mensagemHomologacao = "( Essa solicitação não pode ser homologada, pois não foi possivel criar as reservas de saldo para todos os itens)";
                        $boIncluiHomologacaoReserva = false;
                    }
                }
            } else {
                if ($boHomologaAutomatico) {
                    $obTComprasSolicitacaoHomologacao->setDado( 'exercicio'		 	,Sessao::getExercicio() 		);
                    $obTComprasSolicitacaoHomologacao->setDado( 'cod_entidade'	 	,$request->get('HdnCodEntidade') );
                    $obTComprasSolicitacaoHomologacao->setDado( 'cod_solicitacao'	,$inSolicitacao 			);
                    $obTComprasSolicitacaoHomologacao->setDado( 'numcgm'			,Sessao::read('numCgm') 			);
                    $obTComprasSolicitacaoHomologacao->inclusao();
                    $boIncluiHomologacaoReserva = true;
                }
            }

            //Faz a inclusão na tabela compras.solicitacao_homologada_reserva se NÃO for Registro de Preço
            if ($boIncluiHomologacaoReserva == true && $boRegistroPreco=='false' && $boReservaRigida) {
                foreach ($arValores as $arItens) {
                    $nuVlReserva = str_replace(',','.',str_replace('.','',$arItens['nuVlTotal']));
                    if ($nuVlReserva > 0) {
                        if ($arCodItemReserva[$arItens['inCodItem'].'-'.$arItens['inCodCentroCusto'].'-'.$arItens['inCodDespesa']]['temReserva'] == true) {
                            // inclusão na tabela compras.solicitacao_homologada_reserva
                            $obTSolicitacaoHomologadaReserva = new TComprasSolicitacaoHomologadaReserva;
                            $obTSolicitacaoHomologadaReserva->setDado ( 'exercicio'       , Sessao::getExercicio()       );
                            $obTSolicitacaoHomologadaReserva->setDado ( 'cod_entidade'    , $request->get('HdnCodEntidade')  );
                            $obTSolicitacaoHomologadaReserva->setDado ( 'cod_solicitacao' , $inSolicitacao               );
                            $obTSolicitacaoHomologadaReserva->setDado ( 'cod_item'        , $arItens['inCodItem']        );
                            $obTSolicitacaoHomologadaReserva->setDado ( 'cod_centro'      , $arItens['inCodCentroCusto'] );
                            $obTSolicitacaoHomologadaReserva->setDado ( 'cod_reserva'     , $arItens['inCodReserva']     );
                            $obTSolicitacaoHomologadaReserva->setDado ( 'cod_conta'       , $arItens['inCodEstrutural']  );
                            $obTSolicitacaoHomologadaReserva->setDado ( 'cod_despesa'     , $arItens['inCodDespesa']     );
                            $obTSolicitacaoHomologadaReserva->obTOrcamentoReservaSaldos = $obTOrcamentoReservaSaldo;
                            $obTSolicitacaoHomologadaReserva->inclusao();
                        }
                    }
                }
            }

            $pgAux = ($_REQUEST['boRelatorio']) ? $pgRel : $pgForm;
            SistemaLegado::alertaAviso($pgAux."&dtSolicitacao=".$request->get('stDtSolicitacao')."&stHoraSolicitacao=".$stHoraSolicitacao."&inSolicitacao=".$inSolicitacao."&inEntidade=".$obTComprasSolicitacao->getDado('cod_entidade')."&dtSolicitacao=".$request->get('stDtSolicitacao'),"Número da solicitação: ".$inSolicitacao." ".$mensagemHomologacao,"incluir", "aviso", Sessao::getId(),"");
        } else {
            SistemaLegado::exibeAviso(urlencode($stMensagem), "n_incluir", "erro" );
            SistemaLegado::LiberaFrames(true,true);
        }

        break;
}
    Sessao::encerraExcecao();

?>
