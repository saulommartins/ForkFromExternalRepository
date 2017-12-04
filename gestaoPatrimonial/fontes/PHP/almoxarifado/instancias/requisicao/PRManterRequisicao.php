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
 * Página de processamento para Requisição
 * Data de criação : 02/03/2006

 * @author Analista: Diego Victoria
 * @author Programador: Tonismar R. Bernardo

 * @ignore

 $Id: PRManterRequisicao.php 63833 2015-10-22 13:05:17Z franver $

 **/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_ALM_NEGOCIO."RAlmoxarifadoRequisicao.class.php";
include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoRequisicaoHomologada.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterRequisicao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";
$pgRel  = "FMRelatorio".$stPrograma.".php";

$stCtrl = $_REQUEST['stCtrl'];

Sessao::setTrataExcecao( true );

$stAcao = $request->get('stAcao');

$obRRequisicao = new RAlmoxarifadoRequisicao;
$obErro = new Erro;

switch ($stAcao) {
    case "incluir":
            $inCount = 1;
            $arItens = Sessao::read('arItens');
            $obRRequisicao->obRAlmoxarifadoAlmoxarifado->setCodigo( $arItens[0]['cod_almoxarifado'] );
            $obRRequisicao->setExercicio( Sessao::getExercicio() );
            $obRRequisicao->obRCGMRequisitante->obRCGM->setNumCGM( Sessao::read('numCgm') );
            $obRRequisicao->obRCGMSolicitante->setNumCGM( $_REQUEST['inCGMSolicitante'] );
            $obRRequisicao->setObservacao( (!empty($_REQUEST['stObservacao']) ? $_REQUEST['stObservacao'] : null) );
            if ( count( $arItens ) > 0 ) {
                foreach ($arItens as $key => $value) {

                    $inValorQuantidade = str_replace(',','.',($_REQUEST['nuQuantidade_'.$inCount]));
                    $inSaldo = str_replace(',','.',($value['saldo']));

                    $obRRequisicao->addRequisicaoItem();
                    $obRRequisicao->roUltimoRequisicaoItem->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->setCodigo( $value['cod_item'] );
                    $obRRequisicao->roUltimoRequisicaoItem->obRAlmoxarifadoEstoqueItem->obRMarca->setCodigo( $value['cod_marca'] );
                    $obRRequisicao->roUltimoRequisicaoItem->obRAlmoxarifadoEstoqueItem->obRCentroDeCustos->setCodigo( $value['cod_centro'] );
                    $obRRequisicao->roUltimoRequisicaoItem->setQuantidade( $_REQUEST['nuQuantidadeLista_'.$inCount] );
                    $nuQuantidade = str_replace( '.', '' , $_REQUEST['nuQuantidadeLista_'.$inCount] );
                    $nuQuantidade = (float) str_replace( ',', '.' , $nuQuantidade );
                    $nuSaldo = str_replace( '.', '' , $value['saldo_formatado'] );
                    $nuSaldo = (float) str_replace( ',', '.' , $nuSaldo );

                    if ($inSaldo < $inValorQuantidade) {
                        $obErro->setDescricao( 'Quantidade '.$_REQUEST['nuQuantidade_'.$inCount].' deve ser menor ou igual ao Saldo em Estoque '.$value['saldo'].' no item '.$inCount.'.');
                        break;
                    } elseif ($nuSaldo < $nuQuantidade) {
                        $obErro->setDescricao( 'Quantidade '.$_REQUEST['nuQuantidadeLista_'.$inCount].' deve ser menor ou igual ao Saldo em Estoque '.$value['saldo'].' no item '.$inCount.'.');
                        break;
                    } elseif (!$nuQuantidade || $nuQuantidade <= 0) {
                        $obErro->setDescricao( 'Quantidade do item '.$inCount.' não pode ser nula.' );
                        break;
                    }

                    if ($value['valores_atributos']) {
                        $obRRequisicao->roUltimoRequisicaoItem->setValoresAtributos( $value['valores_atributos'] );
                    }

                    $inCount++;
                }
            } else {
                $obErro->setDescricao( "Deve existir pelo menos um item na lista." );
            }
            if ( !$obErro->ocorreu() ) {
                $obErro = $obRRequisicao->incluir();

                SistemaLegado::alertaAviso($pgRel.'?'.Sessao::getId()."&stAcao=".$stAcao."&inCodRequisicao=".$obRRequisicao->getCodigo()."&inCodAlmoxarifado=".$arItens[0]['cod_almoxarifado'], "Requisição: ".$obRRequisicao->getCodigo(),"incluir","aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }
    break;
    case "alterar":
            $stMensagem = "";
            $inCount = 1;

            include_once( TALM . "TAlmoxarifadoRequisicaoItens.class.php" );
            include_once( TALM . "TAlmoxarifadoRequisicao.class.php"      );
            include_once( TALM . "TAlmoxarifadoAtributoRequisicaoItem.class.php"      );
            include_once( TALM . "TAlmoxarifadoAtributoRequisicaoItemValor.class.php"      );

            $obTAlmoxarifadoAtributoRequisicaoItemValor = new TAlmoxarifadoAtributoRequisicaoItemValor;
            $obTAlmoxarifadoAtributoRequisicaoItem = & $obTAlmoxarifadoAtributoRequisicaoItemValor->obTAlmoxarifadoAtributoRequisicaoItem;
            $obTAlmoxarifadoRequisicaoItens = & $obTAlmoxarifadoAtributoRequisicaoItem->obTAlmoxarifadoRequisicaoItens;
            $obTAlmoxarifadoRequisicao = & $obTAlmoxarifadoRequisicaoItens->obTAlmoxarifadoRequisicao;

            Sessao::getTransacao()->setMapeamento( $obTAlmoxarifadoRequisicao );
            Sessao::getTransacao()->setMapeamento( $obTAlmoxarifadoRequisicaoItens );

            $arItens = Sessao::read('arItens');

            $obTAlmoxarifadoRequisicao->setDado( 'cod_requisicao' , $_REQUEST['inCodRequisicao'] );
            $obTAlmoxarifadoRequisicao->setDado( 'cod_almoxarifado', $_REQUEST['inCodAlmoxarifado'] );
            $obTAlmoxarifadoRequisicao->setDado( 'exercicio', $_REQUEST['stExercicio'] );
            $obTAlmoxarifadoRequisicao->setDado( 'cgm_solicitante', $_REQUEST['inCGMSolicitante'] );
            $obTAlmoxarifadoRequisicao->setDado( 'cgm_requisitante', Sessao::read('numCgm') );
            $obTAlmoxarifadoRequisicao->setDado( 'observacao', $_REQUEST['stObservacao'] );
            $obTAlmoxarifadoRequisicao->setDado( 'cgm_solicitante', $_REQUEST['inCGMSolicitante'] );

            $obTAlmoxarifadoRequisicaoItens->setDado( 'exercicio', $_REQUEST['stExercicio'] );
            $obTAlmoxarifadoRequisicaoItens->setDado( 'cod_almoxarifado' , $arItens[0]['cod_almoxarifado'] );
            $obTAlmoxarifadoRequisicaoItens->setDado( 'cod_requisicao' , $_REQUEST['inCodRequisicao'] );

            $obTAlmoxarifadoRequisicaoItens->recuperaItens( $rsJaForam );

            $stCampoCod = $obTAlmoxarifadoAtributoRequisicaoItem->getCampoCod();
            $obTAlmoxarifadoAtributoRequisicaoItem->setCampoCod("");
            $obTAlmoxarifadoAtributoRequisicaoItemValor->exclusao();
            $obTAlmoxarifadoAtributoRequisicaoItem->exclusao();
            $obTAlmoxarifadoAtributoRequisicaoItem->setCampoCod( $stCampoCod );

            while ( !$rsJaForam->eof() ) {
                $stKeyDb = $rsJaForam->getCampo('cod_item').'-'.
                           $rsJaForam->getCampo('cod_marca').'-'.
                           $rsJaForam->getCampo('cod_centro');

                $arItensChave[$stKeyDb] = true;
                $rsJaForam->proximo();
            }

            if ( count( $arItens ) > 0  ) {

                foreach ($arItens as $key => $value) {

                    $stKeyNew = $value['cod_item'].'-'.$value['cod_marca'].'-'.$value['cod_centro'];

                    $obTAlmoxarifadoRequisicaoItens->setDado('cod_item'  , $value['cod_item']   );
                    $obTAlmoxarifadoRequisicaoItens->setDado('cod_marca' , $value['cod_marca']  );
                    $obTAlmoxarifadoRequisicaoItens->setDado('cod_centro', $value['cod_centro'] );
                    $obTAlmoxarifadoRequisicaoItens->setDado('quantidade', $_REQUEST['nuQuantidadeLista_'.$inCount] );

                    $saldoEstoque = str_replace(',','.',$value['saldo_formatado']);
                    $quantidadeSelecionadaRequisicao = str_replace(',','.',$_REQUEST['nuQuantidadeLista_'.$inCount]);

                    $saldo = str_replace('.','',$saldoEstoque);
                    $quantidadeSelecionada = str_replace('.','',$quantidadeSelecionadaRequisicao);

                    if ($saldo < $quantidadeSelecionada) {
                        SistemaLegado::LiberaFrames(true,true);
                        Sessao::getExcecao()->setDescricao('Quantidade '.$_REQUEST['nuQuantidadeLista_'.$inCount].' deve ser menor ou igual ao Saldo em Estoque '.$value['saldo'].' no item '.$inCount);
                        break;
                    }

                    if ( abs($quantidadeSelecionadaRequisicao) == 0 ) {
                         $stMensagem = 'Quantidade do item '.$inCount.' não pode ser nula.';
                         break;
                    }

                    if ( !isset( $arItensChave[$stKeyNew] ) ) {
                        $obTAlmoxarifadoRequisicaoItens->inclusao();
                    } else {
                        $obTAlmoxarifadoRequisicaoItens->alteracao();
                        unset( $arItensChave[$stKeyNew] );
                    }

                    if ( count($value['valores_atributos']) > 0 ) {
                        foreach ($value['valores_atributos'] as $valor_atributo) {
                            $obTAlmoxarifadoAtributoRequisicaoItem->setDado( "quantidade", $valor_atributo['quantidade'] );
                            $obTAlmoxarifadoAtributoRequisicaoItem->inclusao( $boTransacao );

                            foreach ($valor_atributo['atributo'] as $atributo) {
                                $obTAlmoxarifadoAtributoRequisicaoItemValor->setDado( "cod_modulo"  , "29" );
                                $obTAlmoxarifadoAtributoRequisicaoItemValor->setDado( "cod_cadastro", "2" );
                                $obTAlmoxarifadoAtributoRequisicaoItemValor->setDado( "cod_atributo", $atributo["cod_atributo"] );
                                $obTAlmoxarifadoAtributoRequisicaoItemValor->setDado( "valor"       , $atributo["valor"] );
                                $obTAlmoxarifadoAtributoRequisicaoItemValor->inclusao( $boTransacao );
                            }
                        }
                    }
                    $inCount++;
                }

                if (!$stMensagem) {
                    foreach ($arItensChave as $stChave => $valor) {
                        $arChave = explode('-',$stChave);
                        include_once( TALM."TAlmoxarifadoLancamentoRequisicao.class.php" );
                        $obTAlmoxarifadoLancamentoRequisicao = new TAlmoxarifadoLancamentoRequisicao();
                        $obTAlmoxarifadoLancamentoRequisicao->setDado('cod_item' , $arChave[0] );
                        $obTAlmoxarifadoLancamentoRequisicao->setDado('cod_marca', $arChave[1] );
                        $obTAlmoxarifadoLancamentoRequisicao->setDado('cod_centro',$arChave[2] );
                        $obTAlmoxarifadoLancamentoRequisicao->setDado('cod_almoxarifado', $arItens[0]['cod_almoxarifado'] );
                        $obTAlmoxarifadoLancamentoRequisicao->setDado('cod_requisicao', $_REQUEST['inCodRequisicao'] );
                        $obTAlmoxarifadoLancamentoRequisicao->setDado('exercicio', $_REQUEST['stExercicio'] );
                        $obTAlmoxarifadoLancamentoRequisicao->recuperaPorChave( $rsLancamento );
                        if ( $rsLancamento->getNumLinhas() > 0 ) {
                            $stMensagem = "Erro ao excluir item. Já existe lançamentos para o item $arChave[0].";
                        } else {
                            $obTAlmoxarifadoRequisicaoItens->setDado('cod_item'  , $arChave[0] );
                            $obTAlmoxarifadoRequisicaoItens->setDado('cod_marca' , $arChave[1] );
                            $obTAlmoxarifadoRequisicaoItens->setDado('cod_centro', $arChave[2] );

                            $obTAlmoxarifadoRequisicaoItens->exclusao();
                        }
                    }
                }
            } else {
                $stMensagem = "Deve existir pelo menos um item na lista.";
            }

            if (!$stMensagem) {
                $boHomologaAutomatico = SistemaLegado::pegaConfiguracao('homologacao_automatica_requisicao', 29);
                $boHomologaAutomatico = ($boHomologaAutomatico == 'true') ? true : false;
                include_once( TALM . "TAlmoxarifadoRequisicaoHomologada.class.php"      );
                $obHomologacao = new TAlmoxarifadoRequisicaoHomologada;
                $obHomologacao->setDado( 'cod_requisicao' , 	$_REQUEST['inCodRequisicao']);
                $obHomologacao->setDado( 'cod_almoxarifado', 	$arItens[0]['cod_almoxarifado']);
                $obHomologacao->setDado( 'exercicio', 			$_REQUEST['stExercicio'] );
                $obHomologacao->setDado( 'cgm_homologador',   	Sessao::read('numCgm'));
                if (!$boHomologaAutomatico) {
                    $obHomologacao->setDado( 'homologada',          $_REQUEST['boHomologar']);
                } else {
                    $obHomologacao->setDado( 'homologada',          $boHomologaAutomatico);
                }

                $obErro = $obHomologacao->inclusao($boTransacao);
                if ( $obErro->ocorreu() ) {
                    $stMensagem = "Erro ao homologar a requisição.";
                }
            }

            if (!$stMensagem) {
                $obTAlmoxarifadoRequisicao->alteracao();
                SistemaLegado::alertaAviso($pgRel."?".$session->id."&stExercicio=".$_REQUEST['stExercicio']."&inCodRequisicao=".$_REQUEST['inCodRequisicao']."&inCodAlmoxarifado=".$arItens[0]['cod_almoxarifado'], "Requisição: ".$_REQUEST['inCodRequisicao'] ,"alterar","aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::exibeAviso(urlencode($stMensagem),"n_incluir","erro");
            }
            SistemaLegado::LiberaFrames(true,true);
    break;

    case "excluir":

        $stFiltro = " WHERE exercicio = '".$_REQUEST['stExercicio']."'
                AND cod_almoxarifado = ".$_REQUEST['inCodAlmoxarifado']."
                AND cod_requisicao = ".$_REQUEST['inCodRequisicao']."
                AND timestamp = (SELECT timestamp
                           FROM almoxarifado.requisicao_homologada table1
                          WHERE requisicao_homologada.exercicio = table1.exercicio
                            AND requisicao_homologada.cod_almoxarifado = table1.cod_almoxarifado
                            AND requisicao_homologada.cod_requisicao = table1.cod_requisicao
                           ORDER BY timestamp DESC
                              LIMIT 1)
                AND homologada = true";
        $obTAlmoxarifadoRequisicaoHomologada = new TAlmoxarifadoRequisicaoHomologada;
        $obTAlmoxarifadoRequisicaoHomologada->recuperaTodos($rsRequisicaoHomologada, $stFiltro);

        if ($rsRequisicaoHomologada->getNumLinhas() > 0) {
            SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao, "Não é possível excluir a requisição, pois está homologada!", "n_excluir", "erro", Sessao::getId(), "../");
        } else {
            $obTAlmoxarifadoAtributoRequisicaoItemValor =  new TAlmoxarifadoAtributoRequisicaoItemValor;
            $obTAlmoxarifadoAtributoRequisicaoItem      =  $obTAlmoxarifadoAtributoRequisicaoItemValor->obTAlmoxarifadoAtributoRequisicaoItem;
            $obTAlmoxarifadoRequisicaoItens             =  $obTAlmoxarifadoAtributoRequisicaoItem->obTAlmoxarifadoRequisicaoItens;
            $obTAlmoxarifadoRequisicao 				    =  $obTAlmoxarifadoRequisicaoItens->obTAlmoxarifadoRequisicao;

            Sessao::getTransacao()->setMapeamento($obTAlmoxarifadoRequisicao);
            $arItens = Sessao::read('arItens');

            $obTAlmoxarifadoRequisicaoItens->setDado('exercicio'        , $_REQUEST['stExercicio'] );
            $obTAlmoxarifadoRequisicaoItens->setDado('cod_almoxarifado' , $arItens[0]['cod_almoxarifado'] );
            $obTAlmoxarifadoRequisicaoItens->setDado('cod_requisicao'   , $_REQUEST['inCodRequisicao'] );
            $stCampoCod = $obTAlmoxarifadoAtributoRequisicaoItem->getCampoCod();
            $obTAlmoxarifadoAtributoRequisicaoItem->setCampoCod("");

            $obTAlmoxarifadoAtributoRequisicaoItemValor->setDado('exercicio'        , $_REQUEST['stExercicio'] );
            $obTAlmoxarifadoAtributoRequisicaoItemValor->setDado('cod_almoxarifado' , $_REQUEST['inCodAlmoxarifado'] );
            $obTAlmoxarifadoAtributoRequisicaoItemValor->setDado('cod_requisicao'   , $_REQUEST['inCodRequisicao'] );
            $obTAlmoxarifadoAtributoRequisicaoItemValor->setDado('cod_requisicao'   , $_REQUEST['inCodRequisicao'] );
            $obTAlmoxarifadoAtributoRequisicaoItemValor->setDado('cod_almoxarifado' , $_REQUEST['inCodAlmoxarifado'] );
            $obTAlmoxarifadoAtributoRequisicaoItemValor->setDado('exercicio'        , $_REQUEST['stExercicio'] );

            $obTAlmoxarifadoAtributoRequisicaoItemValor->exclusao();
            $obTAlmoxarifadoAtributoRequisicaoItem->exclusao();

            $obTAlmoxarifadoAtributoRequisicaoItem->setCampoCod( $stCampoCod );

            $obTAlmoxarifadoRequisicaoItens->exclusao();

            $obTAlmoxarifadoRequisicaoHomologada->setDado('cod_requisicao'   , $_REQUEST['inCodRequisicao'] );
            $obTAlmoxarifadoRequisicaoHomologada->setDado('cod_almoxarifado' , $_REQUEST['inCodAlmoxarifado'] );
            $obTAlmoxarifadoRequisicaoHomologada->setDado('exercicio'        , $_REQUEST['stExercicio'] );
            $obTAlmoxarifadoRequisicaoHomologada->exclusao();

            $obTAlmoxarifadoRequisicao->setDado('cod_requisicao'   , $_REQUEST['inCodRequisicao'] );
            $obTAlmoxarifadoRequisicao->setDado('cod_almoxarifado' , $_REQUEST['inCodAlmoxarifado'] );
            $obTAlmoxarifadoRequisicao->setDado('exercicio'        , $_REQUEST['stExercicio'] );
            $obTAlmoxarifadoRequisicao->exclusao();

            SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao."&stExercicio=".$_REQUEST['stExercicio'], "Requisição: ".$_REQUEST['inCodRequisicao'], "excluir", "aviso", Sessao::getId(), "../");
        }

    break;

    case "anular":

        include_once( TALM. "TAlmoxarifadoRequisicaoItensAnulacao.class.php" );
        include_once( TALM. "TAlmoxarifadoRequisicaoAnulacao.class.php" );
        $obTAlmoxarifadoRequisicaoItensAnulacao = new TAlmoxarifadoRequisicaoItensAnulacao;
        $obTAlmoxarifadoRequisicaoAnulacao      = new TAlmoxarifadoRequisicaoAnulacao;
        Sessao::getTransacao()->setMapeamento( $obTAlmoxarifadoRequisicaoAnulacao );
        Sessao::getTransacao()->setMapeamento( $obTAlmoxarifadoRequisicaoItensAnulacao );
        $obTAlmoxarifadoRequisicaoItensAnulacao->obTAlmoxarifadoRequisicaoAnulacao = &$obTAlmoxarifadoRequisicaoAnulacao;

        $obTAlmoxarifadoRequisicaoAnulacao->setDado('cod_requisicao', $_REQUEST['inCodRequisicao'] );
        $obTAlmoxarifadoRequisicaoAnulacao->setDado('cod_almoxarifado', $_REQUEST['inCodAlmoxarifado'] );
        $obTAlmoxarifadoRequisicaoAnulacao->setDado('exercicio', $_REQUEST['stExercicio'] );
        $obTAlmoxarifadoRequisicaoAnulacao->setDado('motivo', $_REQUEST['stMotivo'] );
        $obTAlmoxarifadoRequisicaoAnulacao->inclusao();

        $boAnula = false;
        $inCount = 1;

        $arItens = Sessao::read('arItens');

        foreach ($arItens as $value) {
            $valor = (float) str_replace('.', '',str_replace(',', '.',$_REQUEST['nuAnular_'.$inCount]));
            if ($valor>0) {
                $boAnula = true;
                break;
            }
            $inCount++;
        }

        if ($boAnula) {
            $inCount = 1;
            if (count($arItens) > 0) {
                foreach ($arItens as $key => $value) {
                    $inRequisitada = number_format(str_replace(',','.',$value['requisitada']), 4, '.', '');
                    $inAtendida  = number_format(str_replace(',','.',$value['atendida']), 4, '.', '');
                    $inAnulada     = number_format(str_replace(',','.',$value['anulada']), 4, '.', '');

                    $inQtdAnular = number_format(str_replace(',','.', str_replace('.','',$_REQUEST['nuAnular_'.$inCount])), 4, '.', '');
                    $inSaldo = number_format((($inRequisitada - $inAtendida) - $inAnulada), 4, '.', '');
                    if (( bccomp($inQtdAnular,$inSaldo,4) <= 0 ) && ( $inQtdAnular > 0 )) {
                        $obTAlmoxarifadoRequisicaoItensAnulacao->setDado('cod_item', $value['cod_item']);
                        $obTAlmoxarifadoRequisicaoItensAnulacao->setDado('cod_marca', $value['cod_marca']);
                        $obTAlmoxarifadoRequisicaoItensAnulacao->setDado('cod_centro', $value['cod_centro']);
                        $obTAlmoxarifadoRequisicaoItensAnulacao->setDado('quantidade', $_REQUEST["nuAnular_".$inCount]);
                        $obTAlmoxarifadoRequisicaoItensAnulacao->inclusao();
                    } else {
                        if ($inQtdAnular > 0) {
                            SistemaLegado::LiberaFrames();
                            Sessao::getExcecao()->setDescricao("Quantidade A Anular do Item:".$inCount." é maior que o permitido.");
                            break 2;
                        }
                    }
                    $inCount++;
                }
            }
            SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao."&stExercicio=".$_REQUEST['stExercicio'], "Requisição: ".$_REQUEST['inCodRequisicao'], "anular", "aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::LiberaFrames();
            Sessao::getExcecao()->setDescricao("É necessário anular ao menos um item.");
        }
    break;

    case "homologar":
        include_once( TALM . "TAlmoxarifadoRequisicaoHomologada.class.php"      );
        $obHomologacao = new TAlmoxarifadoRequisicaoHomologada;
        $obHomologacao->setDado( 'cod_requisicao' , 	$_REQUEST['inCodRequisicao'] );
        $obHomologacao->setDado( 'cod_almoxarifado', 	$_REQUEST['inCodAlmoxarifado'] );
        $obHomologacao->setDado( 'exercicio', 			$_REQUEST['stExercicio'] );
        $obHomologacao->setDado( 'cgm_homologador',   	Sessao::read('numCgm'));
        $obHomologacao->setDado( 'homologada',        	$_REQUEST['boHomologar'] );

        $obErro = $obHomologacao->inclusao($boTransacao);

        if ($_REQUEST['boHomologar'] == 'true') {
            $stMensagemAux =  "Requisição Homologada: ";
        } else {
            $stMensagemAux =  "Requisição Não Homologogada: ";
        }

        if ( !$obErro->ocorreu() )
            sistemaLegado::alertaAviso($pgFilt."?stAcao=homologar",$stMensagemAux.$_REQUEST['inCodRequisicao']."/".$_REQUEST['stExercicio'],"incluir","aviso", Sessao::getId(), "../");
        else
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
    break;
    case "anular_homolog":
        // somente salva se mudou de homologada para não homologada
        if ($_REQUEST['boAnularHomologacao'] == 'true') {
            include_once( TALM . "TAlmoxarifadoRequisicaoHomologada.class.php"      );
            $obHomologacao = new TAlmoxarifadoRequisicaoHomologada;
            $obHomologacao->setDado( 'cod_requisicao' , 	$_REQUEST['inCodRequisicao'] );
            $obHomologacao->setDado( 'cod_almoxarifado', 	$_REQUEST['inCodAlmoxarifado'] );
            $obHomologacao->setDado( 'exercicio', 			$_REQUEST['stExercicio'] );
            $obHomologacao->setDado( 'cgm_homologador',   	Sessao::read('numCgm'));
            $obHomologacao->setDado( 'homologada',        	'f' );

            $obErro = $obHomologacao->inclusao($boTransacao);

            $stMensagemAux =  "Anulada a Homologação da Requisição: ";
        } else {
            $stMensagemAux =  "Não Anulada a Homologação da Requisição: ";
        }

        if ( !$obErro->ocorreu() )
            sistemaLegado::alertaAviso($pgFilt."?stAcao=anular_homolog",$stMensagemAux.$_REQUEST['inCodRequisicao']."/".$_REQUEST['stExercicio'],"incluir","aviso", Sessao::getId(), "../");
        else
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");

    break;
}

Sessao::encerraExcecao();

?>
