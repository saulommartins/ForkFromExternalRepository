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

 * @author Analista      : Cleisson
 * @author Desenvolvedor : Bruce Cruz de Sena

 * @ignore

 * Casos de uso: uc-03.04.02

 $Id: PRManterHomologacaoSolicitacaoCompra.php 62986 2015-07-14 18:08:54Z michel $

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_COM_MAPEAMENTO."TComprasSolicitacao.class.php";
include_once CAM_GP_COM_MAPEAMENTO."TComprasSolicitacaoItem.class.php";
include_once CAM_GP_COM_MAPEAMENTO.'TComprasSolicitacaoHomologada.class.php';
include_once CAM_GP_COM_MAPEAMENTO.'TComprasSolicitacaoHomologadaAnulacao.class.php';
include_once CAM_GP_COM_MAPEAMENTO.'TComprasSolicitacaoHomologadaReserva.class.php';
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoReservaSaldos.class.php';
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoReservaSaldosAnulada.class.php';
include_once CAM_GA_ADM_MAPEAMENTO.'TAdministracaoConfiguracao.class.php';

$stAcao = $request->get('stAcao');

//Define o nome dos arquivos PHP
$stPrograma = "ManterHomologacaoSolicitacaoCompra";
$pgFilt     = "FL".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgList     = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgForm     = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgFormItem = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgProc     = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul     = "OC".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";

 switch ($stAcao) {

    case 'incluir':
        Sessao::setTrataExcecao( true );

        $numcgm = SistemaLegado::pegaDado('numcgm','orcamento.entidade', "where cod_entidade =".$_REQUEST['stCodEntidade']." and exercicio = '".Sessao::getExercicio()."'");
        $nomEntidade = SistemaLegado::pegaDado('nom_cgm','sw_cgm', "where numcgm =".$numcgm);
        
        $obTComprasSolicitacao = new TComprasSolicitacao;
        $obTComprasSolicitacao->setDado( 'cod_solicitacao', $_REQUEST['stCodSolicitacao'] );
        $obTComprasSolicitacao->setDado( 'exercicio'      , $_REQUEST['stExercicio']      );
        $obTComprasSolicitacao->setDado( 'cod_entidade'   , $_REQUEST['stCodEntidade']    );
        $obTComprasSolicitacao->consultar();
        
        ///////////// fazendo as reservas de saldos
        //// inclusão de reserva de saldo pra cada item da solicitação
        $obTCompraSolicitacaoItem = new TComprasSolicitacaoItem;
        $obTCompraSolicitacaoItem->setDado('cod_entidade'       , $_REQUEST['stCodEntidade']    );
        $obTCompraSolicitacaoItem->setDado('cod_solicitacao'    , $_REQUEST['stCodSolicitacao'] );
        $obTCompraSolicitacaoItem->setDado('exercicio'          , $_REQUEST['stExercicio']      );
        $obTCompraSolicitacaoItem->recuperaRelacionamentoItemHomologacao( $rsListaItens );

        $stDtSolicitacao = SistemaLegado::pegaDado("timestamp", "compras.solicitacao", "where cod_solicitacao = ".$_REQUEST['stCodSolicitacao']." and cod_entidade = ".$_REQUEST['stCodEntidade']." and exercicio = '".$_REQUEST['stExercicio']."'");
        list($ano, $mes, $dia) = explode("-", substr($stDtSolicitacao,0, 10));

        $obTOrcamentoReservaSaldos = new TOrcamentoReservaSaldos;

        $boReservaRigida = SistemaLegado::pegaConfiguracao('reserva_rigida', 35, Sessao::getExercicio());
        $boReservaRigida = ($boReservaRigida == 'true') ? true : false;
        
        if($obTComprasSolicitacao->getDado('registro_precos') == 't')
            $boReservaRigida = false;

        include_once CAM_GP_COM_MAPEAMENTO.'TComprasSolicitacaoItemDotacao.class.php';
        $obTComprasSolicitacaoItemDotacao = new TComprasSolicitacaoItemDotacao;

        /// se for reserva rigida este laço deve parar assim que não conseguir fazer uma reserva
        while (!$rsListaItens->eof() and ($boReservaRigida) and (!$stMensagem)) {
            if ($rsListaItens->getCampo('cod_despesa') && $rsListaItens->getCampo('vl_item_solicitacao') > 0.00) {
                //inclusão na tabela orcamento.reserva_saldo
                $obTOrcamentoReservaSaldos->setDado( 'exercicio'          , $rsListaItens->getCampo ( 'exercicio'   ) );
                $obTOrcamentoReservaSaldos->proximoCod( $inCodReserva );
                $obTOrcamentoReservaSaldos->setDado( 'cod_reserva',         $inCodReserva                             );
                $obTOrcamentoReservaSaldos->setDado( 'cod_despesa'        , $rsListaItens->getCampo ( 'cod_despesa' ) );
                $obTOrcamentoReservaSaldos->setDado( 'dt_validade_inicial', $dia."/".$mes."/".$ano                    );
                $obTOrcamentoReservaSaldos->setDado( 'tipo'               , 'A'                                       );
                $obTOrcamentoReservaSaldos->setDado( 'dt_inclusao'        , $dia."/".$mes."/".$ano                    );
                $obTOrcamentoReservaSaldos->setDado( 'motivo'             , "Entidade: ".$_REQUEST['stCodEntidade']." - ".$nomEntidade.", solicitação de compras: ".$_REQUEST['stCodSolicitacao']."/".$rsListaItens->getCampo ( 'exercicio').', Item:'.$rsListaItens->getCampo( 'cod_item' ));
                $obTOrcamentoReservaSaldos->setDado( 'vl_reserva'         , $rsListaItens->getCampo ( 'vl_item_solicitacao' )    );
                $obTOrcamentoReservaSaldos->setDado( 'dt_validade_final'  , '31/12/'.$rsListaItens->getCampo('exercicio'));

                if ( $obTOrcamentoReservaSaldos->incluiReservaSaldo() ) {
                    $rsListaItens->setCampo( 'codigo_reserva' , $inCodReserva );
                } else {
                    $stMensagem  = " Não foi possivel efetuar reserva de saldos para o item ";
                    $stMensagem .= $rsListaItens->getCampo( 'cod_item' ) . " - " . $rsListaItens->getCampo( 'descricao_resumida' );
                    $stMensagem .= " a dotação ".  $rsListaItens->getCampo( 'cod_despesa' ). ' não possui saldo suficiente.' ;
                }
            }
            $rsListaItens->proximo();
        }

        /////////////// resevas feitas

        if ((!$boReservaRigida) or ($boReservaRigida and !$stMensagem)) {
            $obTComprasSolicitacaoHomologada = new TComprasSolicitacaoHomologada;
            $obTComprasSolicitacaoHomologada->setDado('exercicio'       , $_REQUEST['stExercicio']      );
            $obTComprasSolicitacaoHomologada->setDado('cod_entidade'    , $_REQUEST['stCodEntidade']    );
            $obTComprasSolicitacaoHomologada->setDado('cod_solicitacao' , $_REQUEST['stCodSolicitacao'] );
            $obTComprasSolicitacaoHomologada->setDado('numcgm'          , Sessao::read('numCgm')     );

            // verifica se ja existe homologação inclusa para solicitação
            $obTComprasSolicitacaoHomologada->verificaExistenciaHomologacaoInclusa($rsDadosHomologacao);

            if ($rsDadosHomologacao->getNumLinhas() == -1) {
                $obTComprasSolicitacaoHomologada->inclusao();
            } else {
                $obTComprasSolicitacaoHomologadaAnulacao = new TComprasSolicitacaoHomologadaAnulacao;
                $obTComprasSolicitacaoHomologadaAnulacao->setDado('exercicio'       , $_REQUEST['stExercicio']      );
                $obTComprasSolicitacaoHomologadaAnulacao->setDado('cod_entidade'    , $_REQUEST['stCodEntidade']    );
                $obTComprasSolicitacaoHomologadaAnulacao->setDado('cod_solicitacao' , $_REQUEST['stCodSolicitacao'] );
                $obTComprasSolicitacaoHomologadaAnulacao->setDado('numcgm'          , Sessao::read('numCgm')     );

                $obTComprasSolicitacaoHomologadaAnulacao->verificaExistenciaHomologacaoAnulada($rsDadosHomologacao);

                if ($rsDadosHomologacao->getNumLinhas() > 0) {
                    $obTComprasSolicitacaoHomologadaAnulacao->exclusao();
                    $obTComprasSolicitacaoHomologada->alteracao();
                } else {
                    $stErro = " Essa solicitação já foi homologada";
                    SistemaLegado::alertaAviso($pgForm."&exercicio=".$_REQUEST['stExercicio']."&cod_entidade=".$_REQUEST['stCodEntidade']."&cod_solicitacao=".$_REQUEST['stCodSolicitacao'],urlencode($stErro).'!',"n_excluir","erro", Sessao::getId(),"");
                    break;
                }
            }

            /// inclusão na tabela compras.solicitacao_homologada_reserva
            $rsListaItens->setPrimeiroElemento();
            while (!$rsListaItens->eof()) {
                if ($rsListaItens->getCampo('codigo_reserva')) {
                    $obTComprasSolicitacaoHomologadaReserva = new TComprasSolicitacaoHomologadaReserva;
                    $obTComprasSolicitacaoHomologadaReserva->setDado('exercicio'       , $_REQUEST['stExercicio']);
                    $obTComprasSolicitacaoHomologadaReserva->setDado('cod_entidade'    , $_REQUEST['stCodEntidade']);
                    $obTComprasSolicitacaoHomologadaReserva->setDado('cod_solicitacao' , $_REQUEST['stCodSolicitacao']);
                    $obTComprasSolicitacaoHomologadaReserva->setDado('cod_item'        , $rsListaItens->getCampo('cod_item'));
                    $obTComprasSolicitacaoHomologadaReserva->setDado('cod_centro'      , $rsListaItens->getCampo('cod_centro'));
                    $obTComprasSolicitacaoHomologadaReserva->setDado('cod_reserva'     , $rsListaItens->getCampo('codigo_reserva'));
                    $obTComprasSolicitacaoHomologadaReserva->setDado('cod_conta'       , $rsListaItens->getCampo('cod_conta'));
                    $obTComprasSolicitacaoHomologadaReserva->setDado('cod_despesa'     , $rsListaItens->getCampo('cod_despesa'));
                    $obTComprasSolicitacaoHomologadaReserva->inclusao();
                }
                $rsListaItens->proximo();
            }
        }
        Sessao::encerraExcecao();
        SistemaLegado::alertaAviso($pgList.'?'.Sessao::getId()."&stAcao=$stAcao","Número da solicitação: ".$_REQUEST['stCodSolicitacao'] . $stMensagem , "excluir", "aviso", Sessao::getId(),"");

    break;

    case 'anular':

        $inCodSolicitacao = $_REQUEST['stCodSolicitacao'];
        $inCodEntidade    = $_REQUEST['stCodEntidade'];
        $stExercicio      = $_REQUEST['stExercicio'];

        if (is_numeric($inCodSolicitacao) && is_numeric($inCodEntidade) && !empty($stExercicio)) {

            Sessao::setTrataExcecao(true);

            $rsPermissaoExcluir = new RecordSet;

            $obTComprasSolicitacaoHomologada = new TComprasSolicitacaoHomologada;
            $obTComprasSolicitacaoHomologada->setDado('exercicio'       , $_REQUEST['stExercicio']    );
            $obTComprasSolicitacaoHomologada->setDado('cod_entidade'    , $_REQUEST['stCodEntidade']  );
            $obTComprasSolicitacaoHomologada->setDado('cod_solicitacao' , $_REQUEST['stCodSolicitacao']);
            $obTComprasSolicitacaoHomologada->recuperaPermissaoAnularHomologacao($rsPermissaoExcluir);

            # Se tiver permissão para anular homologação.
            $boPermissaoExcluir = ($rsPermissaoExcluir->getCampo('permissao_excluir') == 'true') ? true : false;

            if ($boPermissaoExcluir) {

                $obErro = new Erro;

                $obTComprasSolicitacaoHomologadaReserva = new TComprasSolicitacaoHomologadaReserva;
                $stFiltro  = " WHERE  solicitacao_homologada_reserva.exercicio       = '".$_REQUEST['stExercicio']."'     \n";
                $stFiltro .= "   AND  solicitacao_homologada_reserva.cod_entidade    =  ".$_REQUEST['stCodEntidade']."    \n";
                $stFiltro .= "   AND  solicitacao_homologada_reserva.cod_solicitacao =  ".$_REQUEST['stCodSolicitacao']." \n";
                $obTComprasSolicitacaoHomologadaReserva->recuperaTodosNomEntidade($rsReservas, $stFiltro);

                # Exclui da tabela compras.solicitacao_homologada_reserva.
                $obTComprasSolicitacaoHomologadaReserva->setDado('exercicio'       , $_REQUEST['stExercicio']     );
                $obTComprasSolicitacaoHomologadaReserva->setDado('cod_entidade'    , $_REQUEST['stCodEntidade']   );
                $obTComprasSolicitacaoHomologadaReserva->setDado('cod_solicitacao' , $_REQUEST['stCodSolicitacao']);
                $obTComprasSolicitacaoHomologadaReserva->exclusao();

                $obReservaSaldoAnulada = new TOrcamentoReservaSaldosAnulada;

                # Recupera os itens que estão ativos na Solicitação para
                # incluir a anulação da reserva de saldo e também a anulação
                # da dotação do item.
                while (!$rsReservas->eof()) {
                    $stMsgAnulacao  = "";
                    $stMsgAnulacao .= " Anulação Automática. Entidade: ".$rsReservas->getCampo('cod_entidade')." - ".$rsReservas->getCampo('nom_entidade').",";
                    $stMsgAnulacao .= " Solicitação de Compras: ".$rsReservas->getCampo('cod_solicitacao')."/".$rsReservas->getCampo('exercicio');

                    $obReservaSaldoAnulada->setDado('cod_reserva'     , $rsReservas->getCampo( 'cod_reserva') );
                    $obReservaSaldoAnulada->setDado('exercicio'       , $rsReservas->getCampo( 'exercicio')   );
                    $obReservaSaldoAnulada->setDado('motivo_anulacao' , $stMsgAnulacao                        );
                    $obReservaSaldoAnulada->recuperaPorChave( $rsReserva );

                    # Inclui na orcamento.reserva_saldo_anulada
                    if ($rsReserva->getNumLinhas() <= 0) {
                        $obReservaSaldoAnulada->setDado ('dt_anulacao' , date('d/m/Y'));
                        $obErro = $obReservaSaldoAnulada->inclusao();
                    }
                    $rsReservas->proximo();
                }

                if (!$obErro->ocorreu()) {
                    # Inclui o registro de anulação da homologação da solicitação
                    $obTComprasSolicitacaoHomologadaAnulacao = new TComprasSolicitacaoHomologadaAnulacao;
                    $obTComprasSolicitacaoHomologadaAnulacao->setDado('exercicio'       , $_REQUEST['stExercicio']      );
                    $obTComprasSolicitacaoHomologadaAnulacao->setDado('cod_entidade'    , $_REQUEST['stCodEntidade']    );
                    $obTComprasSolicitacaoHomologadaAnulacao->setDado('cod_solicitacao' , $_REQUEST['stCodSolicitacao'] );
                    $obTComprasSolicitacaoHomologadaAnulacao->setDado('numcgm'          , Sessao::read('numCgm')     );
                    $obTComprasSolicitacaoHomologadaAnulacao->inclusao();
                }

                if (!$obErro->ocorreu()) {
                    SistemaLegado::alertaAviso($pgList.'?'.Sessao::getId()."&stAcao=$stAcao","Número da solicitação: ".$_REQUEST['stCodSolicitacao']."/".$_REQUEST['stExercicio'], "excluir", "aviso", Sessao::getId(),"");
                }
            } else {
                $stErro = "Essa Homologação não pode ser anulada porque possui mapa de compras e itens que não estão anulados!";
                SistemaLegado::alertaAviso($pgForm."&exercicio=".$_REQUEST['stExercicio']."&cod_entidade=".$_REQUEST['stCodEntidade']."&cod_solicitacao=".$_REQUEST['stCodSolicitacao'], $stErro,"n_excluir","erro", Sessao::getId(),"");
                SistemaLegado::LiberaFrames(true,true);
            }

            Sessao::encerraExcecao();
        }

    break;
}

?>
