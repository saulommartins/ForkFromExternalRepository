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

    * $Id: PRManterManutencaoParticipante.php 62309 2015-04-20 19:43:33Z arthur $

    * Casos de uso: uc-03.04.07

*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include(TLIC."TLicitacaoParticipante.class.php");
include(TLIC."TLicitacaoParticipanteConsorcio.class.php");

$stAcao = $request->get('stAcao');

//Define o nome dos arquivos PHP
$stPrograma = "ManterManutencaoParticipante";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

//inicia a transacao
Sessao::setTrataExcecao( true );

$obTLicitacaoParticipante = new TLicitacaoParticipante;
Sessao::getTransacao()->setMapeamento( $obTLicitacaoParticipante );

switch ($stAcao) {

    case "alterar":
        $stMensagem = '';
        $arPart = Sessao::read('part');
            if ( count($arPart) <= 0) {
                $stMensagem = "Pelo menos um participante deve ser informado!";
            }

        if (!$stMensagem) {

            $codLicitacao  = Sessao::read('cod_licitacao');
            $codEntidade   = Sessao::read('cod_entidade');
            $codModalidade = Sessao::read('cod_modalidade');
            $stExercicio   = Sessao::read('exercicio');

            //exclui os possiveis registros de participante_consorcio
            $obTParticipanteConsorcio = new TLicitacaoParticipanteConsorcio();
            $obTParticipanteConsorcio->setDado("cod_licitacao",$codLicitacao);
            $obTParticipanteConsorcio->setDado("cod_modalidade",$codModalidade);
            $obTParticipanteConsorcio->setDado("cod_entidade",$codEntidade);
            $obTParticipanteConsorcio->setDado("exercicio",$stExercicio);
            $obTParticipanteConsorcio->exclusao();

            //exclui todos os registros relacionados aos participantes desta licitacao (tabela participante)
            $obTLicitacaoParticipante->setDado("cod_licitacao",$codLicitacao);
            $obTLicitacaoParticipante->setDado("cod_modalidade",$codModalidade);
            $obTLicitacaoParticipante->setDado("cod_entidade",$codEntidade);
            $obTLicitacaoParticipante->setDado("exercicio",$stExercicio);

            $obTLicitacaoParticipante->recuperaParticipanteLicitacao( $rsParticipanteLicitacao );
            
            //CRIANDO ARRAY PARA MONTAR CHAVE DOS REGISTROS QUE JÁ ESTÃO CADASTRADOS DO BANCO
            $arChaveBd = array();
                        
            while ( !$rsParticipanteLicitacao->eof() ) {
                $stChaveBanco = $rsParticipanteLicitacao->getCampo('cod_licitacao') .'-'.
                                $rsParticipanteLicitacao->getCampo('cod_modalidade').'-'.
                                $rsParticipanteLicitacao->getCampo('cod_entidade')  .'-'.
                                $rsParticipanteLicitacao->getCampo('exercicio')     .'-'.
                                $rsParticipanteLicitacao->getCampo('cgm_fornecedor');
                $arChaveBd[$stChaveBanco] = true;
                $rsParticipanteLicitacao->proximo();
            }
            
            //agora inclui os participantes
            foreach ($arPart as $partAux) {

                $stChaveNew = $codLicitacao.'-'.$codModalidade.'-'.$codEntidade.'-'.$stExercicio.'-'.$partAux['cgmParticipante'];
                
                $obTLicitacaoParticipante->setDado('cgm_fornecedor', $partAux['cgmParticipante']);
                $obTLicitacaoParticipante->setDado('numcgm_representante', $partAux['cgmRepLegal']);
                $obTLicitacaoParticipante->setDado('dt_inclusao',addSlashes($partAux['dataInclusao']));
                $obTLicitacaoParticipante->setDado('exercicio',$stExercicio);
                                
                if ( !isset($arChaveBd[$stChaveNew]) ) {
                    $obTLicitacaoParticipante->inclusao();
                    $boConsorcio = true;
                } else {
                    $obTLicitacaoParticipante->alteracao();
                    $boConsorcio = true;
                    unset( $arChaveBd[$stChaveNew] );
                }

                if ( ($partAux['tipoParticipacao'] == "consorcio") && ( $boConsorcio )) {
                    //se o tipo de participacao eh consorcio,
                    //inclui a referencia em licitacao.participante_consorcio
                    //$obTParticipanteConsorcio->setDado('exercicio', $stExercicio );
                    //$obTParticipanteConsorcio->setDado('cod_entidade', $inEntidade );
                    //$obTParticipanteConsorcio->setDado('cod_modalidade', $inModalidade );
                    $obTParticipanteConsorcio->setDado("cgm_fornecedor", $partAux['cgmParticipante'] );
                    $obTParticipanteConsorcio->setDado("numcgm",$partAux['cgmConsorcio']);
                    $obTParticipanteConsorcio->inclusao();
                }

            }
            include_once(TCOM."TComprasJulgamentoItem.class.php");
            include_once(TLIC."TLicitacaoParticipanteDocumentos.class.php");
            include_once(TLIC."TLicitacaoParticipanteConsorcio.class.php");
            include_once(TLIC."TLicitacaoCotacaoLicitacao.class.php");
            include_once(TCOM."TComprasCotacaoFornecedorItem.class.php");
            include_once(TCOM."TComprasCotacaoFornecedorItemDesclassificacao.class.php");
            $obTComprasJulgamentoItem = new TComprasJulgamentoItem;
            $obTLicitacaoParticipanteDocumento = new TLicitacaoParticipanteDocumentos;
            $obTLicitacaoParticipanteConsorcio = new TLicitacaoParticipanteConsorcio;
            $obTComprasCotacaoFornecedorItemDesclassificacao = new TComprasCotacaoFornecedorItemDesclassificacao;
            $obTComprasCotacaoFornecedorItem = new TComprasCotacaoFornecedorItem;
            $obTLicitacaoCotacaoLicitacao = new TLicitacaoCotacaoLicitacao;

            foreach ($arChaveBd as $stChave => $valor) {
                $arChaveTmp = explode( '-', $stChave );
                list( $inLicitacao, $inModalidade, $inEntidade, $stExercicio, $inFornecedor ) = $arChaveTmp;

                $obTLicitacaoParticipante->setDado( 'cod_licitacao' , $inLicitacao  );
                $obTLicitacaoParticipante->setDado( 'cgm_fornecedor', $inFornecedor );
                $obTLicitacaoParticipante->setDado( 'cod_modalidade', $inModalidade );
                $obTLicitacaoParticipante->setDado( 'cod_entidade'  , $inEntidade   );
                $obTLicitacaoParticipante->setDado( 'exercicio'     , $stExercicio  );

                $inCodMapa = SistemaLegado::pegaDado("cod_mapa","licitacao.licitacao","where cod_licitacao =".$inLicitacao." and cod_modalidade =".$inModalidade." and cod_entidade =".$inEntidade." and exercicio = '".$stExercicio."'");
                $stExercicioMapa = SistemaLegado::pegaDado("exercicio_mapa","licitacao.licitacao","where cod_licitacao =".$inLicitacao." and cod_modalidade =".$inModalidade." and cod_entidade =".$inEntidade." and exercicio ='".$stExercicio."'");
                $inCodCotacao = SistemaLegado::pegaDado("cod_cotacao",'compras.mapa_cotacao',"where cod_mapa =".$inCodMapa."and exercicio_mapa='".$stExercicioMapa."'");

                //$inCodMapa = SistemaLegado::pegaDado('cod_mapa','licitacao.licitacao','where cod_licitacao ='.$inLicitacao.' and cod_modalidade ='.$inModalidade.' and cod_entidade ='.$inEntidade.' and exercicio ='.$stExercicio.'');
                //$stExercicioMapa = SistemaLegado::pegaDado('exercicio_mapa','licitacao.licitacao','where cod_licitacao ='.$inLicitacao.' and cod_modalidade ='.$inModalidade.' and cod_entidade ='.$inEntidade.' and exercicio ='.$stExercicio.'');
                //$inCodCotacao = SistemaLegado::pegaDado("cod_cotacao",'compras.mapa_cotacao',"where cod_mapa =".$inCodMapa."and exercicio_mapa='".$stExercicioMapa."'");

                $stFiltro  = " where cod_licitacao = ".$inLicitacao;
                $stFiltro .= " and cgm_fornecedor = ".$inFornecedor;
                $stFiltro .= " and cod_modalidade = ".$inModalidade;
                $stFiltro .= " and cod_entidade = ".$inEntidade;
                $stFiltro .= " and exercicio = '".$stExercicio."'";

                if ($inCodCotacao) {

                    $obTComprasJulgamentoItem->setDado('cgm_fornecedor',$inFornecedor);
                    $obTComprasJulgamentoItem->setDado('exercicio',$stExercicio);
                    $obTComprasJulgamentoItem->setDado('cod_cotacao',$inCodCotacao);

                    $obTComprasJulgamentoItem->recuperaClassificacaoItens($rsClassificacaoItens);

                    if ($rsClassificacaoItens->getNumLinhas() > 0) {
                       $stMensagem = "Erro ao excluir participante. Já foi efetuado julgamento.";
                    } else {

                        $obTLicitacaoParticipanteDocumento->recuperaTodos($rsParticipanteDocumento,$stFiltro);
                        while (!$rsParticipanteDocumento->eof()) {
                            $obTLicitacaoParticipanteDocumento->setDado('cod_licitacao',$rsParticipanteDocumento->getCampo('cod_licitacao'));
                            $obTLicitacaoParticipanteDocumento->setDado('cgm_fornecedor',$rsParticipanteDocumento->getCampo('cgm_fornecedor'));
                            $obTLicitacaoParticipanteDocumento->setDado('cod_modalidade',$rsParticipanteDocumento->getCampo('cod_modalidade'));
                            $obTLicitacaoParticipanteDocumento->setDado('cod_entidade',$rsParticipanteDocumento->getCampo('cod_entidade'));
                            $obTLicitacaoParticipanteDocumento->setDado('exercicio',$rsParticipanteDocumento->getCampo('exercicio'));
                            $obTLicitacaoParticipanteDocumento->setDado('cod_documento',$rsParticipanteDocumento->getCampo('cod_documento'));
                            $obTLicitacaoParticipanteDocumento->setDado('dt_validade',$rsParticipanteDocumento->getCampo('dt_validade'));
                            $obTLicitacaoParticipanteDocumento->exclusao();

                            $rsParticipanteDocumento->proximo();
                        }

                        $obTLicitacaoParticipanteConsorcio->recuperaTodos($rsParticipanteConsorcio,$stFiltro);
                        while (!$rsParticipanteDocumento->eof()) {
                            $obTLicitacaoParticipanteConsorcio->setDado('cod_licitacao',$rsParticipanteConsorcio->getCampo('cod_licitacao'));
                            $obTLicitacaoParticipanteConsorcio->setDado('cgm_fornecedor',$rsParticipanteConsorcio->getCampo('cgm_fornecedor'));
                            $obTLicitacaoParticipanteConsorcio->setDado('cod_modalidade',$rsParticipanteConsorcio->getCampo('cod_modalidade'));
                            $obTLicitacaoParticipanteConsorcio->setDado('cod_entidade',$rsParticipanteConsorcio->getCampo('cod_entidade'));
                            $obTLicitacaoParticipanteConsorcio->setDado('exercicio',$rsParticipanteConsorcio->getCampo('exercicio'));
                            $obTLicitacaoParticipanteConsorcio->setDado('numcgm',$rsParticipanteConsorcio->getCampo('numcgm'));
                            $obTLicitacaoParticipanteConsorcio->exclusao();

                            $rsParticipanteConsorcio->proximo();
                        }
                        unset($stFiltro);
                        $stFiltro  = " where cgm_fornecedor = ".$inFornecedor;
                        $stFiltro .= " and exercicio_cotacao = '".$stExercicio."'";
                        $stFiltro .= " and cod_cotacao = ".$inCodCotacao;
                        $obTLicitacaoCotacaoLicitacao->recuperaTodos($rsLicitacaoCotacaoLicitacao,$stFiltro);
                        while (!$rsLicitacaoCotacaoLicitacao->eof()) {
                            $obTLicitacaoCotacaoLicitacao->setDado('cgm_fornecedor',$rsLicitacaoCotacaoLicitacao->getCampo('cgm_fornecedor'));
                            $obTLicitacaoCotacaoLicitacao->setDado('cod_cotacao',$rsLicitacaoCotacaoLicitacao->getCampo('cod_cotacao'));
                            $obTLicitacaoCotacaoLicitacao->setDado('exercicio',$rsLicitacaoCotacaoLicitacao->getCampo('exercicio_cotacao'));
                            $obTLicitacaoCotacaoLicitacao->setDado('cod_item',$rsLicitacaoCotacaoLicitacao->getCampo('cod_item'));
                            $obTLicitacaoCotacaoLicitacao->setDado('lote',$rsLicitacaoCotacaoLicitacao->getCampo('lote'));
                            $obTLicitacaoCotacaoLicitacao->exclusao();

                            $rsLicitacaoCotacaoLicitacao->proximo();
                        }
                        unset($stFiltro);
                        $stFiltro  = " where cgm_fornecedor = ".$inFornecedor;
                        $stFiltro .= " and exercicio = '".$stExercicio."'";
                        $stFiltro .= " and cod_cotacao = ".$inCodCotacao;
                        $obTComprasCotacaoFornecedorItemDesclassificacao->recuperaTodos($rsCotacaoFornecedorItemDesclassificacao,$stFiltro);
                        while (!$rsCotacaoFornecedorItemDesclassificacao->eof()) {
                            $obTComprasCotacaoFornecedorItemDesclassificacao->setDado('cgm_fornecedor',$rsCotacaoFornecedorItemDesclassificacao->getCampo('cgm_fornecedor'));
                            $obTComprasCotacaoFornecedorItemDesclassificacao->setDado('cod_cotacao',$rsCotacaoFornecedorItemDesclassificacao->getCampo('cod_cotacao'));
                            $obTComprasCotacaoFornecedorItemDesclassificacao->setDado('exercicio',$rsCotacaoFornecedorItemDesclassificacao->getCampo('exercicio'));
                            $obTComprasCotacaoFornecedorItemDesclassificacao->setDado('cod_item',$rsCotacaoFornecedorItemDesclassificacao->getCampo('cod_item'));
                            $obTComprasCotacaoFornecedorItemDesclassificacao->setDado('lote',$rsCotacaoFornecedorItemDesclassificacao->getCampo('lote'));
                            $obTComprasCotacaoFornecedorItemDesclassificacao->exclusao();

                            $rsCotacaoFornecedorItemDesclassificacao->proximo();
                        }

                        $obTComprasCotacaoFornecedorItem->recuperaTodos($rsCotacaoFornecedorItem,$stFiltro);
                        while (!$rsCotacaoFornecedorItem->eof()) {
                            $obTComprasCotacaoFornecedorItem->setDado('cgm_fornecedor',$rsCotacaoFornecedorItem->getCampo('cgm_fornecedor'));
                            $obTComprasCotacaoFornecedorItem->setDado('cod_cotacao',$rsCotacaoFornecedorItem->getCampo('cod_cotacao'));
                            $obTComprasCotacaoFornecedorItem->setDado('exercicio',$rsCotacaoFornecedorItem->getCampo('exercicio'));
                            $obTComprasCotacaoFornecedorItem->setDado('cod_item',$rsCotacaoFornecedorItem->getCampo('cod_item'));
                            $obTComprasCotacaoFornecedorItem->setDado('lote',$rsCotacaoFornecedorItem->getCampo('lote'));
                            $obTComprasCotacaoFornecedorItem->exclusao();

                            $rsCotacaoFornecedorItem->proximo();
                        }
                        $obTLicitacaoParticipante->exclusao();
                    }
                } else {
                    $obTLicitacaoParticipanteDocumento->recuperaTodos($rsParticipanteDocumento,$stFiltro);
                    while (!$rsParticipanteDocumento->eof()) {
                        $obTLicitacaoParticipanteDocumento->setDado('cod_licitacao',$rsParticipanteDocumento->getCampo('cod_licitacao'));
                        $obTLicitacaoParticipanteDocumento->setDado('cgm_fornecedor',$rsParticipanteDocumento->getCampo('cgm_fornecedor'));
                        $obTLicitacaoParticipanteDocumento->setDado('cod_modalidade',$rsParticipanteDocumento->getCampo('cod_modalidade'));
                        $obTLicitacaoParticipanteDocumento->setDado('cod_entidade',$rsParticipanteDocumento->getCampo('cod_entidade'));
                        $obTLicitacaoParticipanteDocumento->setDado('exercicio',$rsParticipanteDocumento->getCampo('exercicio'));
                        $obTLicitacaoParticipanteDocumento->setDado('cod_documento',$rsParticipanteDocumento->getCampo('cod_documento'));
                        $obTLicitacaoParticipanteDocumento->setDado('dt_validade',$rsParticipanteDocumento->getCampo('dt_validade'));
                        $obTLicitacaoParticipanteDocumento->exclusao();

                        $rsParticipanteDocumento->proximo();
                    }

                    $obTLicitacaoParticipanteConsorcio->recuperaTodos($rsParticipanteConsorcio,$stFiltro);
                    while (!$rsParticipanteConsorcio->eof()) {
                        $obTLicitacaoParticipanteConsorcio->setDado('cod_licitacao',$rsParticipanteConsorcio->getCampo('cod_licitacao'));
                        $obTLicitacaoParticipanteConsorcio->setDado('cgm_fornecedor',$rsParticipanteConsorcio->getCampo('cgm_fornecedor'));
                        $obTLicitacaoParticipanteConsorcio->setDado('cod_modalidade',$rsParticipanteConsorcio->getCampo('cod_modalidade'));
                        $obTLicitacaoParticipanteConsorcio->setDado('cod_entidade',$rsParticipanteConsorcio->getCampo('cod_entidade'));
                        $obTLicitacaoParticipanteConsorcio->setDado('exercicio',$rsParticipanteConsorcio->getCampo('exercicio'));
                        $obTLicitacaoParticipanteConsorcio->setDado('numcgm',$rsParticipanteConsorcio->getCampo('numcgm'));
                        $obTLicitacaoParticipanteConsorcio->exclusao();

                        $rsParticipanteConsorcio->proximo();
                    }
                    $obTLicitacaoParticipante->exclusao();
                }
            }
        }

        if (!$stMensagem) {
          SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId()."&stAcao=alterar","Participantes da licitação gravados com sucesso!","aviso","aviso", Sessao::getId(), "../");
        } else {
          SistemaLegado::exibeAviso(urlencode($stMensagem),"n_incluir","erro");
        }

    break;
}

//encerra a transacao
Sessao::encerraExcecao();

?>
