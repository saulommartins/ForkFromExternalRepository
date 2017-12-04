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
    * Página de Processamento do Objeto
    * Data de Criação   : 04/07/2006

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Leandro André Zis
    * @ignore

    * Casos de uso: uc-03.05.26

    $Id: PRManterJulgamentoProposta.php 66389 2016-08-22 20:37:03Z carlos.silva $

    */

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stAcao = $request->get('stAcao');

//Define o nome dos arquivos PHP
$stPrograma = "ManterJulgamentoProposta";
$pgFilt = "FL".$stPrograma.".php";

if ( strstr($stAcao,'dispensaLicitacao') ) {
    $pgList = CAM_GP_COM_INSTANCIAS."compraDireta/LSManterJulgamento.php";
} else {
    $pgList = "LS".$stPrograma.".php";
}

$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgGera = "OCGera".$stPrograma.".php";

if ( !$stAcao ) $stAcao = 'manter';

if ( !$_POST['stMapaCompras'] && !strstr($stAcao,'excluir') && !strstr($stAcao,'reemitir')) {

    sistemaLegado::exibeAviso('Escolha um mapa.'   ,"n_incluir","erro");

} else {

  if ( strstr($stAcao,'excluir') ) {

    Sessao::settrataExcecao( true );
    include_once CAM_GP_COM_MAPEAMENTO. 'TComprasJulgamento.class.php';
    include_once CAM_GP_COM_MAPEAMENTO. 'TComprasJulgamentoItem.class.php';
    include_once CAM_GP_COM_MAPEAMENTO. 'TComprasCotacaoFornecedorItemDesclassificacao.class.php';
    include_once CAM_GP_COM_MAPEAMENTO. 'TComprasCotacaoAnulada.class.php';
    include_once CAM_GP_COM_MAPEAMENTO. 'TComprasCotacaoFornecedorItem.class.php';
    include_once CAM_GP_LIC_MAPEAMENTO. 'TLicitacaoCotacaoLicitacao.class.php';

    $obTComprasJulgamentoItem = new TComprasJulgamentoItem;
    $obTComprasJulgamentoItem->setComplementoChave('exercicio,cod_cotacao');
    $obTComprasJulgamentoItem->setDado ( 'exercicio'   , $_REQUEST['inExercicioCotacao'] );
    $obTComprasJulgamentoItem->setDado ( 'cod_cotacao' , $_REQUEST['inCodCotacao'] );
    $obTComprasJulgamentoItem->exclusao();

    $obTComprasJulgamento = new TComprasJulgamento;
    $obTComprasJulgamento->setDado ( 'exercicio'   , $_REQUEST['inExercicioCotacao'] );
    $obTComprasJulgamento->setDado ( 'cod_cotacao' , $_REQUEST['inCodCotacao'] );
    $obTComprasJulgamento->exclusao();

    $obTComprasCotacaoFornecedorItemDesclassificacao = new TComprasCotacaoFornecedorItemDesclassificacao;
    $obTComprasCotacaoFornecedorItemDesclassificacao->setDado ( 'exercicio'   , $_REQUEST['inExercicioCotacao'] );
    $obTComprasCotacaoFornecedorItemDesclassificacao->setDado ( 'cod_cotacao' , $_REQUEST['inCodCotacao'] );
    $obTComprasCotacaoFornecedorItemDesclassificacao->exclusao();

    $stMotivo = "Exclusão do Julgamento da ".($_REQUEST['stTipoJulgamento'] == "compra_direta" ? "Compra Direta" : "Licitação")." do Mapa ".$_REQUEST['inCodMapa']."/".$_REQUEST['stExercicio'];

    $obTComprasCotacaoAnulada = new TComprasCotacaoAnulada;
    $obTComprasCotacaoAnulada->setDado('exercicio'   , $_REQUEST['inExercicioCotacao'] );
    $obTComprasCotacaoAnulada->setDado('cod_cotacao' , $_REQUEST['inCodCotacao']       );
    $obTComprasCotacaoAnulada->setDado("motivo", $stMotivo);
    $obTComprasCotacaoAnulada->inclusao();

    if (!$stMensagemErro) {
        if ($_POST['stMapaCompras']) {
            $arMapa = explode ('/', $_POST['stMapaCompras'] );
            $stMensagem = 'Mapa de Compras ' . $arMapa[0].'/'.$arMapa[1]  ;
        } elseif ($_REQUEST['inNumEdital']) {
            $stMensagem = 'Edital ' . $_REQUEST['inNumEdital'];
        } elseif ($_REQUEST['inCodMapa']) {
            $stMensagem = 'Mapa de Compras ' . $_REQUEST['inCodMapa'] .'/'. $_REQUEST['stExercicio'];
        }

        sistemaLegado::alertaAviso ( $pgList ."?".Sessao::getId()."&stAcao=excluir",$stMensagem ,"excluir","aviso", Sessao::getId(), "../" );
    } else {
        sistemaLegado::exibeAviso( $stMensagemErro  , "unica","erro");
    }
    Sessao::encerraExcecao();
  } else {

    switch ($stAcao) {
        case "manter":
        case "dispensaLicitacao":

            Sessao::setTrataExcecao ( true );
            Sessao::getTransacao()->setMapeamento ( $obTComprasObjeto );
            include_once ( CAM_GP_COM_MAPEAMENTO. 'TComprasJulgamento.class.php'            );
            include_once ( CAM_GP_COM_MAPEAMENTO. 'TComprasMapaCotacao.class.php'           );
            include_once ( CAM_GP_COM_MAPEAMENTO. 'TComprasMapa.class.php'                  );
            include_once ( CAM_GP_COM_MAPEAMENTO. 'TComprasCotacaoFornecedorItem.class.php' );

            $arMapa = explode ('/', $_POST['stMapaCompras'] );

            $stFiltro = " exercicio_mapa = '" . $arMapa[1] . "' and cod_mapa        = ". $arMapa[0]  ;

            $obTComprasMapa = new TComprasMapa;
            $obTComprasMapa->setDado( 'cod_mapa'  , $arMapa[0] );
            $obTComprasMapa->setDado( 'exercicio' , $arMapa[1] );
            $obTComprasMapa->consultar();

            $obTComprasMapa->recuperaMapaLicitacao ( $rsMapa , ' and '.$stFiltro );

            if ($rsMapa->getElementos > 0) {
                 $stMensagemErro = "Já existe um adjudicação para este mapa ".$arMapa[0]."/".$arMapa[1];
            } else {
                $arFornecedores = Sessao::read('arFornecedores');
                $arEmpate = array();
                
                $inCount = 0;
                $arFornecedoresCompras = array();
                foreach ($arFornecedores as $arFornecedoresTMP) {
                    $rsFornecedores = new RecordSet;
                    $rsFornecedores->preenche ( $arFornecedoresTMP );
                    $rsFornecedores->setPrimeiroElemento();
                    $inCodCgmFornecedor = $rsFornecedores->getCampo('cgm_fornecedor');

                    include_once CAM_GP_COM_MAPEAMENTO.'TComprasFornecedor.class.php';
                    $obTComprasFornecedor = new TComprasFornecedor();
                    $obTComprasFornecedor->setDado("cgm_fornecedor", $inCodCgmFornecedor);
                    $obTComprasFornecedor->recuperaListaFornecedor( $rsFornecedor );

                    if ($rsFornecedor->getCampo('status') == 'Inativo') {
                        $arFornecedoresCompras[$inCount]['cgm_fornecedor'] = $inCodCgmFornecedor;
                        $arFornecedoresCompras[$inCount]['nom_cgm'] = $rsFornecedor->getCampo('nom_cgm');
                        $inCount++;
                    }
                }

                if (count($arFornecedoresCompras) > 0) {
                    if (count($arFornecedoresCompras) == 1) {
                        if (Sessao::read('acao') == 1716) {
                            $stMensagemErro = 'O Participante ('.$arFornecedoresCompras[0]['cgm_fornecedor'].' - '.$arFornecedoresCompras[0]['nom_cgm'].') está inativo! Efetue a Manutenção de Propostas para retirar este Participante.';
                        } elseif (Sessao::read('acao') == 1590) {
                            $stMensagemErro = 'O Participante ('.$arFornecedoresCompras[0]['cgm_fornecedor'].' - '.$arFornecedoresCompras[0]['nom_cgm'].') está inativo! Efetue a Manutenção de Participantes para retirar este Participante.';
                        }
                    } elseif (count($arFornecedoresCompras) > 1) {
                        foreach ($arFornecedoresCompras as $arFornecedoresAuxiliar) {
                            $stCodNomFornecedores .= $arFornecedoresAuxiliar['cgm_fornecedor'].' - '.$arFornecedoresAuxiliar['nom_cgm'].', ';
                        }
                        $stCodNomFornecedores = substr($stCodNomFornecedores, 0, strlen($stCodNomFornecedores)-2);
                        if (Sessao::read('acao') == 1716) {
                            $stMensagemErro = 'Os Participantes ('.$stCodNomFornecedores.') estão inativos! Efetue a Manutenção de Propostas para retirar estes Participantes.';
                        } elseif (Sessao::read('acao') == 1590) {
                            $stMensagemErro = 'Os Participantes ('.$stCodNomFornecedores.') estão inativos! Efetue a Manutenção de Participantes para retirar este Participante.';
                        }
                    }
                }

                /// verificando se houve empate na primeira posição
                // Julgamento por item

                if ( $obTComprasMapa->getDado('cod_tipo_licitacao') == 1 ) {
                    foreach ($arFornecedores as $registro) {
                       $rsFornecedores = new RecordSet;
                       $rsFornecedores->preenche ( $registro );
                       $rsFornecedores->setPrimeiroElemento();
                       $inCodItem = $registro['item'];
                       $vlVencedor = $rsFornecedores->getCampo( 'vl_total' );
                       $rsFornecedores->proximo();
                       $boEmpate = false;

                       while ( (!$rsFornecedores->eof()) and ( !$boEmpate )  ) {
                            if (($vlVencedor == $rsFornecedores->getCampo('vl_total')) && ($rsFornecedores->getCampo('cod_status') == 0) && ($rsFornecedores->getCampo('status') == "empatado")) {
                                $arEmpate[] = $rsFornecedores->getCampo( 'item' ) ;
                                $boEmpate = true;
                            }
                            $rsFornecedores->proximo();
                       }
                    }
                // Julgameno por Lote
                } elseif ( $obTComprasMapa->getDado('cod_tipo_licitacao') == 2 ) {

                    foreach ($arFornecedores as $registro) {
                       $rsFornecedores = new RecordSet;
                       $rsFornecedores->preenche ( $registro );
                       $rsFornecedores->setPrimeiroElemento();
                       $inCodItem  = $registro['item'];
                       $vlVencedor = $rsFornecedores->getCampo( 'vl_total' );
                       $ordem      = $rsFornecedores->getCampo('ordem');
                       $rsFornecedores->proximo();
                       $boEmpate = false;
                       while ( (!$rsFornecedores->eof()) and ( !$boEmpate )  ) {
                            if ($rsFornecedores->getCampo( 'lote' )) {
                                if (($vlVencedor == $rsFornecedores->getCampo('vl_total')) && ($rsFornecedores->getCampo('cod_status') == 0) && ($rsFornecedores->getCampo('status') == "empatado")) {
                                    $arEmpate[] = $rsFornecedores->getCampo( 'lote' );
                                    $boEmpate = true;
                                }
                            }

                            $rsFornecedores->proximo();
                       }
                    }
                // Julgamento por Preço Global
                } elseif ($obTComprasMapa->getDado('cod_tipo_licitacao') == 3) {
                    foreach ($arFornecedores as $registro) {
                        $rsFornecedores = new RecordSet;
                        $rsFornecedores->preenche ( $registro );
                        $rsFornecedores->setPrimeiroElemento();
                        $inCodItem  = $registro['item'];
                        $vlVencedor = $rsFornecedores->getCampo('vl_total');
                        $ordem      = $rsFornecedores->getCampo('ordem');
                        $rsFornecedores->proximo();
                        $boEmpate = false;

                        while ((!$rsFornecedores->eof()) && (!$boEmpate)) {
                            if (($vlVencedor == $rsFornecedores->getCampo('vl_total')) && ($rsFornecedores->getCampo('cod_status') == 0) && ($rsFornecedores->getCampo('status') == "empatado")) {
                                $arEmpate[] = $rsFornecedores->getCampo( 'lote' );
                                $boEmpate = true;
                            }
                            $rsFornecedores->proximo();
                        }
                    }
                }

                $rsFornecedores->setPrimeiroElemento();

                $inTotalEmpatados = count ( $arEmpate );
                if ($inTotalEmpatados > 0) {
                     if ($inTotalEmpatados == 1) {
                         $stMensagemErro  = ( $obTComprasMapa->getDado ('cod_tipo_licitacao') == 1 ) ? " O item " : "O lote ";
                         $stMensagemErro .=  implode ( ', ', $arEmpate ) . " está ";
                     } else {
                         $stMensagemErro = ( $obTComprasMapa->getDado ('cod_tipo_licitacao') == 1 ) ? " Os itens " : "Os lotes ";
                         $stMensagemErro .=  implode ( ', ', $arEmpate ) . " estão ";
                     }
                     
                     $stMensagemErro .=  "com fornecedores empatados. É preciso <strong>reclassificar participantes</strong> para que apenas um vença.";
                }
            }

            if (!$stMensagemErro) {

                $stFiltro .= "
                    AND
                        NOT EXISTS
                        (
                            SELECT  1
                              FROM  compras.cotacao_anulada
                             WHERE  cotacao_anulada.cod_cotacao = mapa_cotacao.cod_cotacao
                               AND  cotacao_anulada.exercicio = mapa_cotacao.exercicio_cotacao
                        )";

                $obTComprasMapaCotacao = new TComprasMapaCotacao;
                $obTComprasMapaCotacao->recuperaTodos( $rsMapaCotacao ,' where '. $stFiltro ) ;

                $inCodCotacao = $rsMapaCotacao->getCampo( 'cod_cotacao');
                $stExercicioCotacao = $rsMapaCotacao->getCampo( 'exercicio_cotacao');

                // incluindo ou alterando compras.julgamento
                $obTComprasJulgamento = new TComprasJulgamento;
                $obTComprasJulgamento->setDado ( 'exercicio'   , $rsMapaCotacao->getCampo( 'exercicio_cotacao'));
                $obTComprasJulgamento->setDado ( 'cod_cotacao' , $rsMapaCotacao->getCampo( 'cod_cotacao' )     );
                $obTComprasJulgamento->consultar();

                if (Sessao::getExcecao()->getDescricao() == "Nenhum registro encontrado!") {
                    Sessao::getExcecao()->setDescricao("");
                }

                $boIncluir = ( !$obTComprasJulgamento->getDado('timestamp') );
                $obTComprasJulgamento->setDado ('observacao', '');

                list ( $dia, $mes, $ano ) = explode("/", $_REQUEST['stDataEmissao']);
                $stDataEmissao = $ano."-".$mes."-".$dia;
                $obTComprasJulgamento->setDado( 'timestamp' , "".$stDataEmissao." ".$_REQUEST['stHoraEmissao']."");

                if ($boIncluir) {
                    $obTComprasJulgamento->inclusao();
                } else {
                    $obTComprasJulgamento->alteracao();
                }
                if (is_array($arFornecedores)) {
                    /// o indice principal desta array é o codigo do item
                    include_once ( CAM_GP_COM_MAPEAMENTO.'TComprasJulgamentoItem.class.php'                        );
                    include_once ( CAM_GP_COM_MAPEAMENTO.'TComprasCotacaoFornecedorItemDesclassificacao.class.php' );
                    $obTComprasJulgamentoItem = new TComprasJulgamentoItem;
                    $obTComprasCotacaoFornecedorItemDesclassificacao = new TComprasCotacaoFornecedorItemDesclassificacao;

                    /// apagando as desclassificações, no caso de uma alteraração estar acontecendo os fornecedores podem mudar de status
                    $obTComprasCotacaoFornecedorItemDesclassificacao->setDado ( 'cod_cotacao' , $rsMapaCotacao->getCampo( 'cod_cotacao' )      );
                    $obTComprasCotacaoFornecedorItemDesclassificacao->setDado ( 'exercicio'   , $rsMapaCotacao->getCampo( 'exercicio_cotacao') );
                    $obTComprasCotacaoFornecedorItemDesclassificacao->exclusao();

                    $obTComprasJulgamentoItem->setDado ( 'cod_cotacao' , $rsMapaCotacao->getCampo( 'cod_cotacao' )      );
                    $obTComprasJulgamentoItem->setDado ( 'exercicio'   , $rsMapaCotacao->getCampo( 'exercicio_cotacao') );
                    $obTComprasJulgamentoItem->exclusao();

                    if ($obTComprasMapa->getDado('cod_tipo_licitacao') == 1) {
                        foreach ($arFornecedores as $chave => $registro) {
                            $rsFornecedores = new RecordSet;
                            $rsFornecedores->preenche ( $registro );
                            $rsFornecedores->setPrimeiroElemento();
                            $inCodItem = $chave;
                            while ( !$rsFornecedores->eof() ) {
                                if ( $rsFornecedores->getCampo('cod_status') == 0 ) {
                                    $obTComprasJulgamentoItem->setDado ( 'exercicio'      , $rsMapaCotacao->getCampo( 'exercicio_cotacao') );
                                    $obTComprasJulgamentoItem->setDado ( 'cod_cotacao'    , $rsMapaCotacao->getCampo( 'cod_cotacao' )      );
                                    $obTComprasJulgamentoItem->setDado ( 'cod_item'       , $inCodItem                                     );
                                    $obTComprasJulgamentoItem->setDado ( 'cgm_fornecedor' , $rsFornecedores->getCampo( 'cgm_fornecedor')   );
                                    $obTComprasJulgamentoItem->setDado ( 'ordem'          , $rsFornecedores->getCampo( 'ordem')            );
                                    $obTComprasJulgamentoItem->setDado ( 'lote'           , $rsFornecedores->getCampo( 'lote')             );
                                    $obTComprasJulgamentoItem->setDado ( 'justificativa'  , $rsFornecedores->getCampo( 'justificativa')    );
                                    $obTComprasJulgamentoItem->inclusao();

                                } elseif ( $rsFornecedores->getCampo('cod_status') == 1 ) {
                                    /// desclassificado
                                    $obTComprasCotacaoFornecedorItemDesclassificacao->setDado( 'cgm_fornecedor', $rsFornecedores->getCampo( 'cgm_fornecedor')   );
                                    $obTComprasCotacaoFornecedorItemDesclassificacao->setDado( 'cod_item'      , $inCodItem                                     );
                                    $obTComprasCotacaoFornecedorItemDesclassificacao->setDado( 'cod_cotacao'   , $rsMapaCotacao->getCampo( 'cod_cotacao' )      );
                                    $obTComprasCotacaoFornecedorItemDesclassificacao->setDado( 'exercicio'     , $rsMapaCotacao->getCampo( 'exercicio_cotacao') );
                                    $obTComprasCotacaoFornecedorItemDesclassificacao->setDado( 'justificativa' , $rsFornecedores->getCampo('justificativa')               );
                                    $obTComprasCotacaoFornecedorItemDesclassificacao->setDado( 'lote', $rsFornecedores->getCampo( 'lote')                       );
                                    $obTComprasCotacaoFornecedorItemDesclassificacao->inclusao();
                                }
                                $rsFornecedores->proximo();
                            }
                        }
                    } elseif ($obTComprasMapa->getDado ('cod_tipo_licitacao') == 2) {
                        ///// julgamento por lote

                        /// descobrindo os fornecedores que foram desclassificados
                        $arDesclassificados = array();
                        $inId = 0;
                        $obTComprasCotacaoFornecedorItem = new TComprasCotacaoFornecedorItem;

                        foreach ($arFornecedores as $chave =>$registro) {

                            $rsFornecedores = new RecordSet;
                            $rsFornecedores->preenche ( $registro );
                            $rsFornecedores->setPrimeiroElemento();

                            /////// fornecedor classificado para o lote
                            while ( !$rsFornecedores->eof() ) {
                               $stFiltroItens = " where cotacao_fornecedor_item.exercicio      = '" . $rsMapaCotacao->getCampo( 'exercicio_cotacao')  . "'" .
                                                "  and cotacao_fornecedor_item.cod_cotacao    = "   . $rsMapaCotacao->getCampo( 'cod_cotacao' ).
                                                "  and cotacao_fornecedor_item.cgm_fornecedor = "   . $rsFornecedores->getCampo ( 'cgm_fornecedor') .
                                                "  and cotacao_fornecedor_item.lote           = "   . $rsFornecedores->getCampo ( 'lote' );
                               $obTComprasCotacaoFornecedorItem->recuperaTodos ( $rsItens , $stFiltroItens );
                               if ( $rsFornecedores->getCampo( 'cod_status' ) == 0 ) {

                                    while ( !$rsItens->eof() ) {
                                        $obTComprasJulgamentoItem->setDado ( 'exercicio'      , $rsItens->getCampo( 'exercicio')          );
                                        $obTComprasJulgamentoItem->setDado ( 'cod_cotacao'    , $rsItens->getCampo( 'cod_cotacao' )       );
                                        $obTComprasJulgamentoItem->setDado ( 'cod_item'       , $rsItens->getCampo( 'cod_item' )          );
                                        $obTComprasJulgamentoItem->setDado ( 'cgm_fornecedor' , $rsItens->getCampo( 'cgm_fornecedor')     );
                                        $obTComprasJulgamentoItem->setDado ( 'ordem'          , $rsFornecedores->getCampo( 'ordem' )      );
                                        $obTComprasJulgamentoItem->setDado ( 'justificativa'  , $rsFornecedores->getCampo('justificativa'));
                                        $obTComprasJulgamentoItem->setDado ( 'lote'           , $rsItens->getCampo( 'lote')               );
                                        $obTComprasJulgamentoItem->inclusao();
                                        $rsItens->proximo();
                                    }
                               } elseif ( $rsFornecedores->getCampo('cod_status') == 1 ) {
                                   while ( !$rsItens->eof() ) {

                                        $obTComprasCotacaoFornecedorItemDesclassificacao->setDado( 'cgm_fornecedor', $rsItens->getCampo('cgm_fornecedor') );
                                        $obTComprasCotacaoFornecedorItemDesclassificacao->setDado( 'cod_item'      , $rsItens->getCampo('cod_item'      ) );
                                        $obTComprasCotacaoFornecedorItemDesclassificacao->setDado( 'cod_cotacao'   , $rsItens->getCampo('cod_cotacao'   ) );
                                        $obTComprasCotacaoFornecedorItemDesclassificacao->setDado( 'exercicio'     , $rsItens->getCampo('exercicio'     ) );
                                        $obTComprasCotacaoFornecedorItemDesclassificacao->setDado( 'lote'          , $rsItens->getCampo('lote'          ) );
                                        $obTComprasCotacaoFornecedorItemDesclassificacao->setDado( 'justificativa' , $rsFornecedores->getCampo( 'justificativa' ) );
                                        $obTComprasCotacaoFornecedorItemDesclassificacao->inclusao();
                                        $rsItens->proximo();
                                   }
                               }
                               $rsFornecedores->proximo();
                            }
                        }

                // Julgamento por Preço Global
                } elseif ($obTComprasMapa->getDado ('cod_tipo_licitacao') == 3) {

                    /// descobrindo os fornecedores que foram desclassificados
                    $arDesclassificados = array();
                    $inId = 0;
                    $obTComprasCotacaoFornecedorItem = new TComprasCotacaoFornecedorItem;

                    unset($arFornecedores['arFornecedores']);

                    $arFornecedoresAux = $arFornecedores[0];

                    foreach ($arFornecedoresAux as $chave =>$registro) {

                        $arAux = array();
                        $arAux[] = $registro;
                        $rsFornecedores = new RecordSet;
                        $rsFornecedores->preenche ( $arAux );
                        $rsFornecedores->setPrimeiroElemento();

                        /////// fornecedor classificado para o lote
                        while ( !$rsFornecedores->eof() ) {

                           $stFiltroItens = " where cotacao_fornecedor_item.exercicio     = '". $rsMapaCotacao->getCampo( 'exercicio_cotacao')  . "'" .
                                            "  and cotacao_fornecedor_item.cod_cotacao    = " . $rsMapaCotacao->getCampo( 'cod_cotacao' ).
                                            "  and cotacao_fornecedor_item.cgm_fornecedor = " . $rsFornecedores->getCampo ( 'cgm_fornecedor') .
                                            "  and cotacao_fornecedor_item.lote           = " . $rsFornecedores->getCampo ( 'lote' );

                           $obTComprasCotacaoFornecedorItem->recuperaTodos($rsItens , $stFiltroItens);

                            if ($rsFornecedores->getCampo('cod_status') == 0) {
                                while ( !$rsItens->eof() ) {
                                    $obTComprasJulgamentoItem->setDado ( 'exercicio'      , $rsItens->getCampo( 'exercicio')          );
                                    $obTComprasJulgamentoItem->setDado ( 'cod_cotacao'    , $rsItens->getCampo( 'cod_cotacao' )       );
                                    $obTComprasJulgamentoItem->setDado ( 'cod_item'       , $rsItens->getCampo( 'cod_item' )          );
                                    $obTComprasJulgamentoItem->setDado ( 'cgm_fornecedor' , $rsItens->getCampo( 'cgm_fornecedor')     );
                                    $obTComprasJulgamentoItem->setDado ( 'ordem'          , $rsFornecedores->getCampo( 'ordem' )      );
                                    $obTComprasJulgamentoItem->setDado ( 'justificativa'  , $rsFornecedores->getCampo('justificativa'));
                                    $obTComprasJulgamentoItem->setDado ( 'lote'           , $rsItens->getCampo( 'lote')               );
                                    $obTComprasJulgamentoItem->inclusao();
                                    $rsItens->proximo();
                                }
                            } elseif ( $rsFornecedores->getCampo('cod_status') == 1 ) {
                               while ( !$rsItens->eof() ) {

                                    $obTComprasCotacaoFornecedorItemDesclassificacao->setDado( 'cgm_fornecedor', $rsItens->getCampo('cgm_fornecedor') );
                                    $obTComprasCotacaoFornecedorItemDesclassificacao->setDado( 'cod_item'      , $rsItens->getCampo('cod_item'      ) );
                                    $obTComprasCotacaoFornecedorItemDesclassificacao->setDado( 'cod_cotacao'   , $rsItens->getCampo('cod_cotacao'   ) );
                                    $obTComprasCotacaoFornecedorItemDesclassificacao->setDado( 'exercicio'     , $rsItens->getCampo('exercicio'     ) );
                                    $obTComprasCotacaoFornecedorItemDesclassificacao->setDado( 'lote'          , $rsItens->getCampo('lote'          ) );
                                    $obTComprasCotacaoFornecedorItemDesclassificacao->setDado( 'justificativa' , $rsFornecedores->getCampo( 'justificativa' ) );
                                    $obTComprasCotacaoFornecedorItemDesclassificacao->inclusao();
                                    $rsItens->proximo();
                                }
                            }
                            $rsFornecedores->proximo();
                        }

                    }

                }

                $stMensagem = 'Mapa de Compras '. $arMapa[0].'/'.$arMapa[1];
                /* buscando forncedores que tiveram totais de cotação abaixo do seu valor minimo de nota fiscal*/

                $obTComprasJulgamento = new TComprasJulgamento;
                $stFiltro = "where julgamento.exercicio   = '". $rsMapaCotacao->getCampo( 'exercicio_cotacao') . "' ".
                            "  and julgamento.cod_cotacao = ".$rsMapaCotacao->getCampo( 'cod_cotacao' )  ;
                $obTComprasJulgamento->recuperaTotalCotacaoFornecedor( $rsTotalCotacao , $stFiltro );

                while ( !$rsTotalCotacao->eof() ) {
                    if ( $rsTotalCotacao->getCampo( 'vl_minimo_nf' ) > $rsTotalCotacao->getCampo( 'vl_total_cotacao') ) {
                        $stMensagem .= " Total dos itens para o fornecedor ". $rsTotalCotacao->getCampo( 'cgm_fornecedor') . " ficou abaixo do valor mínimo de nota fiscal.";
                    }
                    $rsTotalCotacao->proximo();
                }

            }

            }

            Sessao::encerraExcecao();

            if (!$stMensagemErro) {

                $link  = "stAcao=$stAcao&inCodCotacao=".$inCodCotacao."&stExercicioCotacao=".$stExercicioCotacao;
                $link .= "&stDtEmissao=".$_REQUEST['stDataEmissao']."&stHrEmissao=".$_REQUEST['stHoraEmissao']."&inCodCompraDireta=".$_REQUEST['inCodCompraDireta'];
                $link .= "&inCodModalidade=".$_REQUEST['inCodModalidade']."&stEntidade=".$_REQUEST['stEntidade'];
                $link .= "&inCodLicitacao=".$_REQUEST['inCodLicitacao']."&inCodEntidade=".$_REQUEST['inCodEntidade']."&stIncluirAssinaturas=".$_REQUEST['stIncluirAssinaturas'];

                sistemaLegado::alertaAviso ( $pgGera ."?".Sessao::getId()."&".$link ,$stMensagem ,"incluir","aviso", Sessao::getId(), "../" );
            } else {
                sistemaLegado::exibeAviso( $stMensagemErro  , "unica","erro");
            }

        break;

        case 'reemitir':
            include_once ( CAM_GP_COM_MAPEAMENTO. 'TComprasJulgamento.class.php' );
            $obTComprasJulgamento = new TComprasJulgamento();
            $obTComprasJulgamento->setDado('cod_cotacao', $_REQUEST['inCodCotacao']);
            $obTComprasJulgamento->setDado('exercicio', "'".$_REQUEST['inExercicioCotacao']."'");
            $obTComprasJulgamento->recuperaPorCotacao( $rsComprasJulgamento );

            $stDtEmissao = SistemaLegado::dataToBr(substr($rsComprasJulgamento->getCampo('timestamp'), 0, 10));
            $stHrEmissao = substr($rsComprasJulgamento->getCampo('timestamp'), 11, 5);

            $link  = "stAcao=".$_REQUEST['stAcao']."&inCodCotacao=".$_REQUEST['inCodCotacao']."&stExercicioCotacao=".$_REQUEST['inExercicioCotacao'];
            $link .= "&stDtEmissao=".$stDtEmissao."&stHrEmissao=".$stHrEmissao."&inCodCompraDireta=".$_REQUEST['inCodCompraDireta'];
            $link .= "&inCodModalidade=".$_REQUEST['inCodModalidade']."&stEntidade=".$_REQUEST['stEntidade'];
            $link .= "&inCodLicitacao=".$_REQUEST['inCodLicitacao']."&inCodEntidade=".$_REQUEST['inCodEntidade']."&stIncluirAssinaturas=".Sessao::read('stIncluirAssinaturas');

            SistemaLegado::alertaAviso($pgGera."?".Sessao::getId()."&".$link,"Cotação: ".$_REQUEST['inCodCotacao']." Mapa: ".$_REQUEST['inCodMapa']."/".$_REQUEST['stExercicio'],"incluir","aviso", Sessao::getId(), "../" );
        break;

    }
  }
}

?>
