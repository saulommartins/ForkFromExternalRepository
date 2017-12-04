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
  * Página Oculta do Manter Mapa de Compras
  * Data de Criação   : 19/09/2006

  * @author Analista: Cleisson Barbosa
  * @author Desenvolvedor: Anderson C. Konze

  * @ignore

  * Casos de uso: uc-03.04.05

  $Id: OCManterMapaCompras.php 64888 2016-04-12 12:31:36Z carlos.silva $

  */

# GA
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';
include_once CAM_FW_HTML."IMontaQuantidadeValores.class.php";
include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCatalogoItem.class.php";

# GP
include_once CAM_GP_COM_COMPONENTES."IMontaDotacaoDesdobramento.class.php";
include_once CAM_GP_COM_MAPEAMENTO."TComprasSolicitacao.class.php";
include_once CAM_GP_COM_MAPEAMENTO."TComprasSolicitacaoItem.class.php";
include_once CAM_GP_COM_MAPEAMENTO."TComprasSolicitacaoItemDotacao.class.php";
include_once CAM_GP_COM_MAPEAMENTO.'TComprasMapa.class.php';
include_once CAM_GP_COM_MAPEAMENTO."TComprasMapaItem.class.php";
include_once CAM_GP_COM_MAPEAMENTO.'TComprasMapaItemDotacao.class.php';
include_once CAM_GP_COM_MAPEAMENTO."TComprasCompraDireta.class.php";
include_once CAM_GP_COM_MAPEAMENTO."TComprasCompraDiretaAnulacao.class.php";

# GP / Licitação
include_once CAM_GP_LIC_MAPEAMENTO."TLicitacaoLicitacao.class.php";
include_once CAM_GP_LIC_MAPEAMENTO."TLicitacaoLicitacaoAnulada.class.php";

# GF
include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoPreEmpenho.class.php";
include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoDespesa.class.php";
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoContaDespesa.class.php';
include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecurso.class.php";
include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoReservaSaldos.class.php";
include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoProjetoAtividade.class.php";

$stAcao = $request->get('stAcao');

function salvarDadosItem($inId, $nuVlUnitario, $nuQuantidade, $nuVlTotal, $nuValorReserva, $nuSaldoDotacao, $inCodDespesa, $inCodConta, $inCodLote, $stTipoCotacao, $boRegistroPreco)
{
    $inTipoLicitacao = Sessao::read('inTipoLicitacao');
    $itens           = Sessao::read('itens');
    $solicitacoes    = Sessao::read('solicitacoes');

    $nuVlUnitario   = str_replace(',','.',str_replace('.','',$nuVlUnitario));
    $nuVlTotal      = str_replace(',','.',str_replace('.','',$nuVlTotal));
    $nuQuantidade   = str_replace(',','.',str_replace('.','',$nuQuantidade));
    $nuValorReserva = str_replace(',','.',str_replace('.','',$nuValorReserva));

    # Para saber a forma de execução (GF).
    $boFormaExecucao = SistemaLegado::pegaConfiguracao('forma_execucao_orcamento', '8', Sessao::getExercicio());
    $boFormaExecucao = ($boFormaExecucao == '1' ? true : false);
    
    $boReservaRigida = SistemaLegado::pegaConfiguracao('reserva_rigida', '35', Sessao::getExercicio());
    $boReservaRigida = ($boReservaRigida == 'true') ? true : false;

    $boReservaAutorizacao = SistemaLegado::pegaConfiguracao('reserva_autorizacao', '35', Sessao::getExercicio());
    $boReservaAutorizacao = ($boReservaAutorizacao == 'true') ? true : false;

    if (($nuVlUnitario == 0) || (!$nuVlUnitario)) {
        $stMensagem = "Informe o Valor Unitário.";
    } elseif (($nuQuantidade == 0) || (!$nuQuantidade)) {
        $stMensagem = "Informe a Quantidade.";
    } elseif (($nuVlTotal == 0) || (!$nuVlTotal)) {
        $stMensagem = "Informe o Valor Total.";
    } elseif ( $nuSaldoDotacao > 0 && $nuVlTotal > $nuSaldoDotacao && $boRegistroPreco == 'false' && $boReservaRigida ) {
        $stMensagem = "O Valor de Reserva não pode ser <strong>maior</strong> que o Saldo da Dotação.";
    } elseif ( $nuSaldoDotacao == 0 && $nuValorReserva > 0 && $boRegistroPreco == 'false' && $boReservaRigida ) {
        $stMensagem = "O Valor de Reserva não pode ser <strong>maior</strong> que o Saldo da Dotação.";
    } elseif (($inTipoLicitacao == 2) and (!$inCodLote)) {
        $stMensagem = "Informe o número do Lote.";
    } else {
        foreach ($itens as $item => $valor) {
            if ($itens[$item]['inId'] == $inId) {
                if ($nuQuantidade > floatval($valor['quantidade_maxima'])) {
                    $stMensagem = "A quantidade do mapa deve ser menor ou igual que (solicitada - atendida).";
                } elseif ((is_numeric($inCodDespesa) && !is_numeric($inCodConta)) && $boFormaExecucao == true) {
                    $stMensagem = "Informe o Desdobramento para a Dotação selecionada.";
                } else {
                    # Atualizando o valor total da solicitação no mapa
                    $nuTotalSolicitacaoMapa = $solicitacoes[$valor['inId_solicitacao']]['total_mapa'];
                    $nuTotalSolicitacaoMapa = $nuTotalSolicitacaoMapa - $valor['valor_total_mapa'] + $nuVlTotal;
                    $solicitacoes[$valor['inId_solicitacao']]['total_mapa'] = $nuTotalSolicitacaoMapa;

                    $itens[$item]['valor_unitario']   = $nuVlUnitario;
                    $itens[$item]['valor_total_mapa'] = $nuVlTotal;
                    $itens[$item]['quantidade_mapa']  = $nuQuantidade;
                    if( $boRegistroPreco == 'false' && $boReservaRigida){
                        if($valor['cod_reserva']=='' && $valor['cod_reserva_solicitacao']=='')
                            $itens[$item]['vl_reserva']   = $nuVlTotal;
                        else
                            $itens[$item]['vl_reserva']   = $nuValorReserva;
                    }

                    $itens[$item]['lote']             = $inCodLote;

                    # Caso o item não tenha dotação informada na solicitação
                    # e o usuário informar no Mapa, adiciona no array de item.
                    if (is_numeric($inCodDespesa)) {

                        if (!is_numeric($inCodConta) && $boFormaExecucao == false) {
                            $obTOrcamentoDespesa = new TOrcamentoDespesa;
                            $stFiltro  = " AND D.cod_despesa = ".$inCodDespesa;
                            $stFiltro .= " AND D.exercicio   = '".Sessao::getExercicio()."' ";
                            $obTOrcamentoDespesa->recuperaListaDotacao($rsDotacao, $stFiltro);
                            $inCodConta = $rsDotacao->getCampo('cod_conta');
                        }

                        $itens[$item]['dotacao']        = $inCodDespesa;
                        $itens[$item]['cod_despesa']    = $inCodDespesa;
                        $itens[$item]['cod_conta']      = $inCodConta;

                        # Busca o cod_estrutural da conta (desdobramento) selecionada.
                        $obTOrcamentoContaDespesa = new TOrcamentoContaDespesa;
                        $stFiltro  = "      cod_conta = ".$inCodConta;
                        $stFiltro .= " AND  exercicio = '".Sessao::getExercicio()."' ";
                        $obTOrcamentoContaDespesa->recuperaCodEstrutural($rsDotacao, $stFiltro);

                        $itens[$item]['cod_estrutural'] = $rsDotacao->getCampo('cod_estrutural');

                        # Monta o Hint da tabela de itens.
                        $obTOrcamentoDespesa	      = new TOrcamentoDespesa;
                        $obTOrcamentoRecurso	      = new TOrcamentoRecurso;
                        $obTOrcamentoProjetoAtividade = new TOrcamentoProjetoAtividade;

                        $stFiltro  = " AND OD.exercicio   = '".Sessao::getExercicio()."' \n";
                        $stFiltro .= " AND OD.cod_despesa = ".$inCodDespesa." \n";
                        $obTOrcamentoDespesa->recuperaRelacionamento($rsOrcamentoDespesa, $stFiltro);

                        # Faz a busca do nome do recurso.
                        $stFiltro  = " AND orcamento.recurso.exercicio = '".Sessao::getExercicio()."' \n";
                        $obTOrcamentoRecurso->setDado('cod_recurso', $rsOrcamentoDespesa->getCampo('cod_recurso'));
                        $obTOrcamentoRecurso->recuperaRelacionamento( $rsOrcamentoRecurso, $stFiltro );

                        # Faz a busca do nome do projeto/atividade.
                        $stFiltro  = " WHERE orcamento.pao.exercicio = '".Sessao::getExercicio()."' \n";
                        $stFiltro .= "   AND orcamento.pao.num_pao   = ".$rsOrcamentoDespesa->getCampo('num_pao')." \n";
                        $obTOrcamentoProjetoAtividade->recuperaSemMascara($rsOrcamentoProjetoAtividade, $stFiltro);

                        $itens[$item]['stTitle'] = $inCodDespesa.' - '.$rsOrcamentoDespesa->getCampo('descricao').' - '.$rsDotacao->getCampo('cod_estrutural').' - '.$rsOrcamentoProjetoAtividade->getCampo('num_acao').' - '.$rsOrcamentoProjetoAtividade->getCampo('nom_pao').' - '.$rsOrcamentoRecurso->getCampo('cod_recurso').' - '.$rsOrcamentoRecurso->getCampo('nom_recurso');
                    } else {
                        $itens[$item]['dotacao']        = "";
                        $itens[$item]['cod_despesa']    = "";
                        $itens[$item]['cod_conta']      = "";
                        $itens[$item]['cod_estrutural'] = "";
                    }
                }
            }
        }
    }

    if ($stMensagem) {
        $stJs = "alertaAviso('$stMensagem', 'form', 'erro', '".Sessao::getId()."');";
    } else {
        Sessao::write('itens' , $itens);

        $rsRecordSet = new RecordSet;
        $rsRecordSet->preenche($itens);

        $stJs .= "jQuery('#spnItem').html('&nbsp;');  ";
        $stJs .= "jQuery('#Ok').removeAttr('disabled'); ";
        $stJs .= montaListaItens($rsRecordSet, $stTipoCotacao);
        $stJs .= montaListaSolicitacoes ( $stTipoCotacao );
    }

    Sessao::write('itens'        , $itens);
    Sessao::write('solicitacoes' , $solicitacoes);

    return $stJs;
}

function alterarItem($inId, $stTipoCotacao)
{
    $arItem = array();
    $itens = Sessao::read('itens');
    foreach ($itens as $item => $valor) {
        if ($itens[$item]['inId'] == $inId) {
            $arItem = $itens[$item];
        }
    }

    $obFormItem = new Formulario;
    $obFormItem->addTitulo ('Dados do Item');

    $obLblItem = new Label;
    $obLblItem->setRotulo ('Item');
    $obLblItem->setId     ('lblItem');
    $obLblItem->setValue  ($arItem['nom_item']);

    $obLblUnMedida = new Label;
    $obLblUnMedida->setRotulo ('Unidade de Medida');
    $obLblUnMedida->setId     ('lblUnMedida');
    $obLblUnMedida->setValue  ($arItem['nom_unidade']);

    $obLblComplemento = new Label;
    $obLblComplemento->setRotulo ('Complemento');
    $obLblComplemento->setId     ('lblItem');
    $obLblComplemento->setValue  ($arItem['complemento']);

    $obLblCentroCusto = new Label;
    $obLblCentroCusto->setRotulo ('Centro de Custo');
    $obLblCentroCusto->setId     ('lblCentroCusto');
    $obLblCentroCusto->setValue  ($arItem['cod_centro']." - ".$arItem['centro_custo']);

    $obLblSaldoEstoque = new Label;
    $obLblSaldoEstoque->setRotulo ('Saldo em Estoque');
    $obLblSaldoEstoque->setId     ('lblSaldoEstoque');
    $obLblSaldoEstoque->setValue  (number_format($arItem['quantidade_estoque'], 2, ",","."));

    $obLblQtdeSolicitada = new Label;
    $obLblQtdeSolicitada->setRotulo ('Quantidade Solicitada');
    $obLblQtdeSolicitada->setId     ('lblQtdeSolicitada' );
    $obLblQtdeSolicitada->setValue  (number_format( $arItem['quantidade_solicitada'] , 2, ",","."));

    $obLblQtdeAtendida = new Label;
    $obLblQtdeAtendida->setRotulo ('Quantidade em Outros Mapas');
    $obLblQtdeAtendida->setTitle  ('Quantidade atendida em outros Mapas');
    $obLblQtdeAtendida->setId     ('lblQtdeAtendida' );
    $obLblQtdeAtendida->setValue  (number_format( $arItem['quantidade_atendida'] , 2, ",","."));

    $obHdnEntidadeItem = new Hidden;
    $obHdnEntidadeItem->setName  ('inCodEntidade');
    $obHdnEntidadeItem->setValue ($arItem['cod_entidade']);

    $obIMontaQtdeValores = new IMontaQuantidadeValores;
    $obIMontaQtdeValores->obQuantidade->setRotulo ("Quantidade do Mapa");
    $obIMontaQtdeValores->obQuantidade->setNull   (false);
    $obIMontaQtdeValores->obQuantidade->setTitle  ("Informe a quantidade a ser comprada com no máximo 4 dígitos.");
    $obIMontaQtdeValores->obQuantidade->setValue  ($arItem['quantidade_solicitada']);
    $obIMontaQtdeValores->obQuantidade->obEvento->setOnChange( "montaParametrosGET('calculaValorReserva', 'nuVlUnitario,nuQuantidade' );" );

    # Caso o item não possua dotação, não será possível desmembrá-lo em mais de um mapa.

    if ($arItem['boDotacao'] == 'F') {
        $obIMontaQtdeValores->obQuantidade->setLabel (true);
    }

    $obIMontaQtdeValores->obValorTotal->setDecimais (2);
    $obIMontaQtdeValores->obValorTotal->setValue    (number_format($arItem['valor_total_mapa'], 2, ",","."));
    $obIMontaQtdeValores->obValorTotal->obEvento->setOnChange( "montaParametrosGET('calculaValorReservaXTotal', 'nuVlTotal,nuValorReserva,nuQuantidade, nuVlUnitario' );" );

    $obIMontaQtdeValores->obValorUnitario->setValue (number_format($arItem['valor_unitario'], 4, ",","."));
    $obIMontaQtdeValores->obValorUnitario->obEvento->setOnChange("montaParametrosGET('calculaValorReserva', 'nuVlUnitario,nuQuantidade' );" );

    # Se o usuário já informou a dotação na Solicitação ou anteriormente no Mapa, monta o Label não permitindo a alteração da Dotação.

    $nuSaldoDotacao = "";

    if ($arItem['boDotacao'] == 'T') {
        $obHdnCodDespesa = new Hidden;
        $obHdnCodDespesa->setId    ('inCodDespesa');
        $obHdnCodDespesa->setName  ('inCodDespesa');
        $obHdnCodDespesa->setValue ($arItem['cod_despesa']);

        $obHdnCodConta = new Hidden;
        $obHdnCodConta->setId    ('stCodClassificacao');
        $obHdnCodConta->setName  ('stCodClassificacao');
        $obHdnCodConta->setValue ($arItem['cod_conta']);

        $obLblDotacao = new Label;
        $obLblDotacao->setRotulo ('Dotação Orçamentária');
        $obLblDotacao->setValue  ($arItem['cod_despesa']." - ".$arItem['dotacao_nom_conta']);

        $obLblDesdobramento = new Label;
        $obLblDesdobramento->setRotulo ('Desdobramento');
        $obLblDesdobramento->setValue  ($arItem['cod_estrutural']." - ".$arItem['nom_conta']);

        # Exibe o saldo da dotação para o usuário.
        $obTEmpenhoPreEmpenho = new TEmpenhoPreEmpenho;
        $obTEmpenhoPreEmpenho->setDado('exercicio'   , Sessao::getExercicio());
        $obTEmpenhoPreEmpenho->setDado('cod_despesa' , $arItem['cod_despesa']);
        $obTEmpenhoPreEmpenho->recuperaSaldoAnterior($rsSaldoAnterior);

        $nuSaldoDotacao = $rsSaldoAnterior->getCampo('saldo_anterior');

        $nuVlReserva = 0.00;
        $nuSaldoDisponivelDotacao = 0.00;

        # Busca o valor disponível da Dotação no momento.
        if (is_numeric($arItem['cod_reserva']) && is_numeric($arItem['exercicio_reserva'])) {
            $stFiltro = " WHERE reserva_saldos.cod_reserva = ".$arItem['cod_reserva']."
                            AND exercicio = '".$arItem['exercicio_reserva']."'
                            AND NOT EXISTS ( SELECT *
                                               FROM orcamento.reserva_saldos_anulada AS RSA
                                              WHERE RSA.cod_reserva=reserva_saldos.cod_reserva
                                                AND RSA.exercicio=reserva_saldos.exercicio ) ";

            $obTOrcamentoReservaSaldos = new TOrcamentoReservaSaldos;
            $obTOrcamentoReservaSaldos->recuperaTodos($rsReservaSaldos, $stFiltro);

            $nuSaldoDisponivelDotacao = ($nuSaldoDotacao + $rsReservaSaldos->getCampo('vl_reserva'));
            $nuVlReserva = $rsReservaSaldos->getCampo('vl_reserva');
        } else {
            $nuSaldoDisponivelDotacao = $nuSaldoDotacao;
        }
        
        if (is_numeric($arItem['cod_reserva_solicitacao']) &&
            is_numeric($arItem['exercicio_reserva_solicitacao']) &&
            $arItem['cod_reserva_solicitacao'].$arItem['exercicio_reserva_solicitacao'] != $arItem['cod_reserva'].$arItem['exercicio_reserva']
           ) {

            $stFiltro = " WHERE reserva_saldos.cod_reserva = ".$arItem['cod_reserva_solicitacao']."
                            AND exercicio = '".$arItem['exercicio_reserva_solicitacao']."'
                            AND NOT EXISTS ( SELECT *
                                               FROM orcamento.reserva_saldos_anulada AS RSA
                                              WHERE RSA.cod_reserva=reserva_saldos.cod_reserva
                                                AND RSA.exercicio=reserva_saldos.exercicio ) ";

            $obTOrcamentoReservaSaldos = new TOrcamentoReservaSaldos;
            $obTOrcamentoReservaSaldos->recuperaTodos($rsReservaSaldos, $stFiltro);

            $nuSaldoDisponivelDotacao = ($nuSaldoDisponivelDotacao + $rsReservaSaldos->getCampo('vl_reserva'));
            $nuVlReserva = $nuVlReserva + $rsReservaSaldos->getCampo('vl_reserva');
        }

        $obHdnSaldoDotacao = new Hidden;
        $obHdnSaldoDotacao->setId    ( "nuHdnSaldoDotacao" );
        $obHdnSaldoDotacao->setName  ( "nuHdnSaldoDotacao" );
        $obHdnSaldoDotacao->setValue ( $nuSaldoDisponivelDotacao );

        $obLblSaldoDotacao = new Label;
        $obLblSaldoDotacao->setId     ( "nuSaldoDotacao" );
        $obLblSaldoDotacao->setValue  ( number_format($nuSaldoDotacao,2,',','.') );
        $obLblSaldoDotacao->setRotulo ( "Saldo da Dotação" );

        $obLblSaldoDisponivelDotacao = new Label;
        $obLblSaldoDisponivelDotacao->setRotulo ( "Saldo Disponível para o Item" );
        $obLblSaldoDisponivelDotacao->setId     ( "stSaldoDotacaoItem" );
        $obLblSaldoDisponivelDotacao->setName   ( "stSaldoDotacaoItem" );
        $obLblSaldoDisponivelDotacao->setValue  ( number_format($nuSaldoDisponivelDotacao, 2, ",",".") );

    } else {
        $obIMontaDotacaoDesdobramento = new IMontaDotacaoDesdobramento;
        $obIMontaDotacaoDesdobramento->setMostraSintetico(true);
        $obIMontaDotacaoDesdobramento->obBscDespesa->obCampoCod->setId ('inCodDespesa');
        $obIMontaDotacaoDesdobramento->obBscDespesa->obCampoCod->setValue($arItem['cod_despesa']);
    }

    $obHdnValorReserva = new Hidden;
    $obHdnValorReserva->setId     ( "nuValorReserva" );
    $obHdnValorReserva->setName   ( "nuValorReserva" );
    $obHdnValorReserva->setValue  ( number_format($nuVlReserva, 2, ",",".") );

    $obLblValorReserva = new Label;
    $obLblValorReserva->setRotulo ( "Valor Reservado no Exercício" );
    $obLblValorReserva->setId     ( "stValorReserva" );
    $obLblValorReserva->setName   ( "stValorReserva" );
    $obLblValorReserva->setValue  ( number_format($nuVlReserva, 2, ",",".") );

    if (Sessao::read('inTipoLicitacao') == 2) {
        $obTxtLote = new TextBox;
        $obTxtLote->setRotulo   ( 'Número do Lote' );
        $obTxtLote->setName     ( 'inCodLote'      );
        $obTxtLote->setId       ( 'inCodLote'      );
        $obTxtLote->setInteiro  ( true             );
        $obTxtLote->setValue    ( $arItem['lote']  );
        $obTxtLote->setNull     ( false );
    }else{
        $obHdnInLote = new Hidden;
        $obHdnInLote->setName  ('inCodLote');
        $obHdnInLote->setValue ( $arItem['lote'] );
        $obHdnInLote->setId    ('inCodLote');
    }

    $obHdnInId = new Hidden;
    $obHdnInId->setName  ('inId');
    $obHdnInId->setValue ( $inId );
    $obHdnInId->setId    ('inId');

    $obFormItem->addComponente ( $obLblItem );
    $obFormItem->addHidden     ( $obHdnInId );
    if (Sessao::read('inTipoLicitacao') != 2) 
        $obFormItem->addHidden     ( $obHdnInLote );
    $obFormItem->addComponente ( $obLblUnMedida );
    $obFormItem->addComponente ( $obLblComplemento  );
    $obFormItem->addComponente ( $obLblCentroCusto  );
    $obFormItem->addComponente ( $obLblSaldoEstoque );
    $obFormItem->addComponente ( $obLblQtdeSolicitada );
    $obFormItem->addComponente ( $obLblQtdeAtendida );
    $obFormItem->addHidden     ( $obHdnEntidadeItem );

    $obIMontaQtdeValores->geraFormulario( $obFormItem );

    if ($arItem['boDotacao'] == 'T') {
        $obFormItem->addHidden     ( $obHdnCodDespesa );
        $obFormItem->addHidden     ( $obHdnCodConta );
        $obFormItem->addHidden     ( $obHdnSaldoDotacao );
        $obFormItem->addComponente ( $obLblDotacao );
        $obFormItem->addComponente ( $obLblDesdobramento );
        $obFormItem->addComponente ( $obLblSaldoDotacao );
    } else {
        $obIMontaDotacaoDesdobramento->geraFormulario($obFormItem);
    }

    $obFormItem->addHidden     ( $obHdnValorReserva );
    $obFormItem->addComponente ( $obLblValorReserva );

    if ($arItem['boDotacao'] == 'T') {
        $obFormItem->addComponente ( $obLblSaldoDisponivelDotacao );
    }

    if (Sessao::read('inTipoLicitacao') == 2) {
        $obFormItem->addComponente ($obTxtLote);
    }

    $obBtnSalvar = new Button;
    $obBtnSalvar->setName               ( "btnSalvar"        );
    $obBtnSalvar->setId                 ( "btnSalvar"        );
    $obBtnSalvar->setValue              ( "Salvar"           );
    $obBtnSalvar->setTipo               ( "button"           );
    $obBtnSalvar->setStyle              ( 'padding:0px 10px;');
    $obBtnSalvar->obEvento->setOnClick  ( " montaParametrosGET( 'salvarDadosItem'
                                                                 , 'inId
                                                                 , nuVlUnitario
                                                                 , nuQuantidade
                                                                 , nuVlTotal
                                                                 , nuValorReserva
                                                                 , nuHdnSaldoDotacao
                                                                 , inCodDespesa
                                                                 , stCodClassificacao
                                                                 , inCodLote
                                                                 , inCodTipoLicitacao
                                                                 , boRegistroPreco'); ");
    $obFormItem->defineBarraAba(array($obBtnSalvar ) ,'','');
    $obFormItem->montaInnerHtml();

    # Preenche o Desdobramento caso tenha sido informado a Dotação.
    if (is_numeric($arItem['cod_despesa']) && $arItem['boDotacao'] == 'F') {
        $stParam .= "&stCodEstrutural=".$arItem['cod_estrutural'];
        $stParam .= "&codClassificacao=".$arItem['cod_conta'];
        $stParam .= "&inCodDespesa=".$arItem['cod_despesa'];
        $stParam .= "&inCodEntidade=".$arItem['cod_entidade'];

        $stJs .= "var stTarget = document.frm.target; ";
        $stJs .= "var stAction = document.frm.action; ";
        $stJs .= "f.stCtrl.value = 'buscaDespesaDiverso'; ";

        $stJs .= "f.target ='oculto';                                                                                 ";
        $stJs .= "f.action ='../../instancias/processamento/OCIMontaDotacaoDesdobramento.php?".Sessao::getId().$stParam."'; ";
        $stJs .= "f.submit();                                                                                         ";
        $stJs .= "f.action = '".$pgOcul."?".Sessao::getId()."';                                                       ";
        $stJs .= "f.action = stAction;                                                                                ";
        $stJs .= "f.target = stTarget;                                                                                ";
    }

    $stJs .= "jQuery('#spnItem').html('".$obFormItem->getHTML()."');                                          \n";
    $stJs .= "jQuery('#Ok').attr('disabled', 'disabled');                                                     \n" ;
    $stJs .= "jQuery('#nuVlUnitario').val('".number_format($arItem['valor_unitario'], 4, ",",".")."');        \n";
    $stJs .= "jQuery('#nuQuantidade').val('".number_format($arItem['quantidade_mapa'],4,",",".")."');         \n";
    $stJs .= "jQuery('#nuQuantidade_label').html('".number_format($arItem['quantidade_mapa'],4,",",".")."');  \n";

    $stJs .= "document.frm.inCodCentroCusto = ".$arItem['cod_centro'].";                                      \n";
    $stJs .= "d.getElementById('btnSalvar').focus(); ";

    return $stJs;
}

function delItem($inId, $stTipo, $stTipoCotacao)
{
    $itens           = Sessao::read('itens');
    $itens_excluidos = Sessao::read('itens_excluidos');
    $solicitacoes    = Sessao::read('solicitacoes');

    $arTMP = array();
    $inCodSolicitacao = 0;

    if ($stTipo == 'solicitacao') {
        $inCodSolicitacao = $inId;
    } else {
        //descobrindo o codigo da solicitacao
        foreach ($itens as $item => $valor) {
            if ($itens[$item]['inId'] == $inId) {
                $inCodSolicitacao = $itens[$item]['inId_solicitacao'];
            }
        }
    }

    # Verificando se o item(s) excluido era o ultimo da solicitação,
    # se for a solicitação será excluida
    $inQuanti = 0;
    if (is_array($itens)) {
        foreach ($itens as $registro) {
            if ($registro['inId_solicitacao'] == $inCodSolicitacao) {
                $inQuanti++;
            }
        }
    }

    if (($inQuanti <= 1) && ($stTipo == 'item')) {
        $stJs = delSolicitacao( $inCodSolicitacao, '' );
    } else {
        if (is_array($itens)) {
            $arItensExcluidos = Sessao::read('itens_excluidos');
            $cont = count($arItensExcluidos);

            foreach ($itens as $item => $valor) {
                if ($stTipo == 'item') { // APAGA APENAS UM ITEM
                    if ($itens[$item]['inId'] == $inId) {
                        $itens_excluidos[$cont]['cod_item']                      = $itens[$item]['cod_item'];
                        $itens_excluidos[$cont]['cod_centro']                    = $itens[$item]['cod_centro'];
                        $itens_excluidos[$cont]['cod_despesa']                   = $itens[$item]['cod_despesa'];
                        $itens_excluidos[$cont]['cod_conta']                     = $itens[$item]['cod_conta'];
                        $itens_excluidos[$cont]['exercicio']                     = $itens[$item]['exercicio'];
                        $itens_excluidos[$cont]['cod_entidade']                  = $itens[$item]['cod_entidade'];
                        $itens_excluidos[$cont]['cod_solicitacao']               = $itens[$item]['cod_solicitacao'];
                        $itens_excluidos[$cont]['exercicio_solicitacao']         = $itens[$item]['exercicio_solicitacao'];
                        $itens_excluidos[$cont]['lote']                          = $itens[$item]['lote'];
                        $itens_excluidos[$cont]['quantidade']                    = $itens[$item]['quantidade_mapa'];
                        $itens_excluidos[$cont]['cod_reserva']                   = $itens[$item]['cod_reserva'];
                        $itens_excluidos[$cont]['exercicio_reserva']             = $itens[$item]['exercicio_reserva'];
                        $itens_excluidos[$cont]['cod_reserva_solicitacao']       = $itens[$item]['cod_reserva_solicitacao'];
                        $itens_excluidos[$cont]['exercicio_reserva_solicitacao'] = $itens[$item]['exercicio_reserva_solicitacao'];
                        $itens_excluidos[$cont]['vl_reserva']                    = $itens[$item]['vl_reserva'];
                        $itens_excluidos[$cont]['vl_reserva_homologacao']        = $itens[$item]['vl_reserva_homologacao'];
                        $itens_excluidos[$cont]['vl_reserva_solicitacao']        = $itens[$item]['vl_reserva_solicitacao'];
                        $itens_excluidos[$cont]['vl_total']                      = $itens[$item]['valor_total_mapa'];
                        $solicitacoes[ $inCodSolicitacao ]['total_mapa']        -= $itens[$item]['valor_total_mapa'];
                    } else {
                        $arTMP[] = $itens[$item];
                    }
                }

                if ($stTipo == 'solicitacao') {
                    // APAGA TODOS ITENS DA SOLICITACAO
                    if ($itens[$item]['inId_solicitacao'] == $inId) {
                        $itens_excluidos[$cont]['cod_item']                      = $itens[$item]['cod_item'];
                        $itens_excluidos[$cont]['cod_centro']                    = $itens[$item]['cod_centro'];
                        $itens_excluidos[$cont]['cod_despesa']                   = $itens[$item]['cod_despesa'];
                        $itens_excluidos[$cont]['cod_conta']                     = $itens[$item]['cod_conta'];
                        $itens_excluidos[$cont]['exercicio']                     = $itens[$item]['exercicio'];
                        $itens_excluidos[$cont]['cod_entidade']                  = $itens[$item]['cod_entidade'];
                        $itens_excluidos[$cont]['cod_solicitacao']               = $itens[$item]['cod_solicitacao'];
                        $itens_excluidos[$cont]['exercicio_solicitacao']         = $itens[$item]['exercicio_solicitacao'];
                        $itens_excluidos[$cont]['lote']                          = $itens[$item]['lote'];
                        $itens_excluidos[$cont]['quantidade']                    = $itens[$item]['quantidade_mapa'];
                        $itens_excluidos[$cont]['cod_reserva']                   = $itens[$item]['cod_reserva'];
                        $itens_excluidos[$cont]['exercicio_reserva']             = $itens[$item]['exercicio_reserva'];
                        $itens_excluidos[$cont]['cod_reserva_solicitacao']       = $itens[$item]['cod_reserva_solicitacao'];
                        $itens_excluidos[$cont]['exercicio_reserva_solicitacao'] = $itens[$item]['exercicio_reserva_solicitacao'];
                        $itens_excluidos[$cont]['vl_reserva']                    = $itens[$item]['vl_reserva'];
                        $itens_excluidos[$cont]['vl_reserva_homologacao']        = $itens[$item]['vl_reserva_homologacao'];
                        $itens_excluidos[$cont]['vl_reserva_solicitacao']        = $itens[$item]['vl_reserva_solicitacao'];
                        $itens_excluidos[$cont]['vl_total']                      = $itens[$item]['valor_total_mapa'];
                        $solicitacoes[$inCodSolicitacao]['total_mapa']          -= $itens[$item]['valor_total_mapa'];

                        $cont++;
                        unset($itens[$item]);
                    } else {
                        $arTMP[] = $itens[$item];
                    }
                }
            }
        }

        $itens = $arTMP;

        if(count($itens)==0){
            $stJs .= " jQuery('#boRegistroPrecoSim').removeAttr('disabled'); \n";
            $stJs .= " jQuery('#boRegistroPrecoNao').removeAttr('disabled'); \n"; 
        }

        Sessao::write('itens' , $itens);
        Sessao::write('itens_excluidos', $itens_excluidos);

        $rsRecordSet = new RecordSet;
        $rsRecordSet->preenche($itens);
        $stJs .= montaListaItens($rsRecordSet, $stTipoCotacao );
        $stJs .= montaListaSolicitacoes ( $stTipoCotacao );
        # Função que monta a aba de Totais.
        $stJs .= totais();
    }

    return $stJs;
}

function delSolicitacao($inId, $stTipoCotacao)
{
    $solicitacoes  = Sessao::read('solicitacoes');
    $arSolicitacao = array();

    if (is_array($solicitacoes)) {
        foreach ($solicitacoes as $registro) {
            if ($registro['inId'] == $inId) {
                if (!$registro['incluir']) {
                      # Se o incluir for false significa que esta rodando uma rotina de alteração
                      # e o registro selecionado tem que ser excluido tb no banco de dados
                      $solicitacoes_excluidas[] = $registro;
                }
            } else {
                $arSolicitacao[] = $registro;
            }
        }
    }

    Sessao::write('solicitacoes_excluidas' , $solicitacoes_excluidas);
    Sessao::write('solicitacoes'           , $arSolicitacao);

    $stJs .= delItem($inId, "solicitacao", $stTipoCotacao); /* APAGA TODOS OS ITENS DA SOLICITACAO REMOVIDA */
    $stJs .= montaListaSolicitacoes($stTipoCotacao);

    return $stJs;
}

function addSolicitacao($stExercicio, $inCodEntidade, $inCodSolicitacao, $stTipoCotacao, $stIncluir = true, $boRegistroPreco)
{
    $arSolicitacao = Sessao::read('solicitacoes');
    $ultimoCodigo  = count($arSolicitacao);
    $stErro = $stJs = "";

    $obTComprasSolicitacao = new TComprasSolicitacao;
    $obTComprasSolicitacao->setDado("stExercicio"      , $stExercicio);
    $obTComprasSolicitacao->setDado("inCodEntidade"    , $inCodEntidade);
    $obTComprasSolicitacao->setDado("inCodSolicitacao" , $inCodSolicitacao);
    $obErro = $obTComprasSolicitacao->recuperaSolicitacoesMapaCompras($rsSolicitacao, $boTransacao);

    if (!$obErro->ocorreu()) {
        if ($rsSolicitacao->getNumLinhas() == 1) {
            $arItens['inId']                  = $ultimoCodigo;
            $arItens['exercicio']             = $rsSolicitacao->getCampo('exercicio');
            $arItens['exercicio_solicitacao'] = $rsSolicitacao->getCampo('exercicio');
            $arItens['cod_solicitacao']       = $rsSolicitacao->getCampo('cod_solicitacao');
            $arItens['nom_entidade']          = $rsSolicitacao->getCampo('nom_entidade');
            $arItens['cod_entidade']          = $rsSolicitacao->getCampo('cod_entidade');
            $arItens['valor_total']           = $rsSolicitacao->getCampo('valor_total');
            $arItens['total_mapas']           = $rsSolicitacao->getCampo('total_mapas');
            $arItens['total_anulado']         = $rsSolicitacao->getCampo('total_anulado');
            $arItens['total_mapa']            = bcsub($rsSolicitacao->getCampo('valor_total'),$rsSolicitacao->getCampo('total_mapas'),2);
            $arItens['data']                  = $rsSolicitacao->getCampo('data');
            # Este campo do array será usado para a rotina de alteração saber se o registro deve ou não ser incluido.
            $arItens['incluir']               = $stIncluir;

            # Função que busca os itens da solicitação para adicionar ao Mapa.
            if (addItens($arItens, $stTipoCotacao, $stIncluir, $boRegistroPreco)) {
                $arSolicitacao[] = $arItens;
                Sessao::write('solicitacoes' , $arSolicitacao);

                if($boRegistroPreco=='false')
                    $stJs .= " jQuery('#boRegistroPrecoSim').attr('disabled', 'disabled'); \n";
                else
                    $stJs .= " jQuery('#boRegistroPrecoNao').attr('disabled', 'disabled'); \n";
            } else {
                $stJs .= "alertaAviso('Esta solicitação não contém itens ou seus itens já foram atendidos.','form','erro','".Sessao::getId()."');\n";
            }
        }
    }

    Sessao::write('ultimoCodigo', $ultimoCodigo);

    return $stJs;
}

function addItens($arItem, $stTipoCotacao, $incluir = true, $boRegistroPreco)
{
    $boRetorno = true;

    $rsOrcamentoDespesa	         = new RecordSet;
    $rsOrcamentoRecurso          = new RecordSet;
    $rsOrcamentoProjetoAtividade = new RecordSet;

    $obTOrcamentoDespesa	      = new TOrcamentoDespesa;
    $obTOrcamentoRecurso	      = new TOrcamentoRecurso;
    $obTOrcamentoProjetoAtividade = new TOrcamentoProjetoAtividade;

    $obTComprasMapaItem               = new TComprasMapaItem;
    $obTAlmoxarifadoCatalogoItem      = new TAlmoxarifadoCatalogoItem;

    $inId             = $arItem['inId'];
    $stExercicio      = $arItem['exercicio_solicitacao'];
    $inCodEntidade    = $arItem['cod_entidade'];
    $inCodSolicitacao = $arItem['cod_solicitacao'];

    # Recupera todos os itens da Solicitação que serão importados no Mapa de Compras.
    $obTComprasMapaItem->setDado("cod_entidade"          , $inCodEntidade);
    $obTComprasMapaItem->setDado("cod_solicitacao"       , $inCodSolicitacao);
    $obTComprasMapaItem->setDado("exercicio_solicitacao" , $stExercicio);
    $obErro = $obTComprasMapaItem->recuperaIncluirSolicitacaoMapa($rsItens, $boTransacao);
    
    $boReservaRigida = SistemaLegado::pegaConfiguracao('reserva_rigida', '35', Sessao::getExercicio());
    $boReservaRigida = ($boReservaRigida == 'true') ? true : false;

    $boReservaAutorizacao = SistemaLegado::pegaConfiguracao('reserva_autorizacao', '35', Sessao::getExercicio());
    $boReservaAutorizacao = ($boReservaAutorizacao == 'true') ? true : false;

    if ($obErro->ocorreu()) {
        $boRetorno = false;
    } else {
        if ($rsItens->getNumLinhas() < 1) {
            $boRetorno = false;
        } else {
            $proxId = 0;
            $itens = Sessao::read('itens');
            if (is_array($itens)) {
                $max = 0;
                foreach ($itens as $item => $valor) {
                    if ($itens[$item]['inId'] >= $max) {
                        $max = $itens[$item]['inId'];
                        $proxId = $max + 1;
                    }
                }
            }

            while (!$rsItens->eof()) {
                # Código para montar o Hint com informações dos Desdobramentos.
                if ($rsItens->getCampo('cod_despesa')) {
                    # Recupera o código de recurso, projeto e atividade.
                    $stFiltro  = " AND OD.exercicio   = '".$stExercicio."' \n";
                    $stFiltro .= " AND OD.cod_despesa = ".$rsItens->getCampo('cod_despesa')." \n";
                    $obTOrcamentoDespesa->recuperaRelacionamento($rsOrcamentoDespesa, $stFiltro);

                    # Recupera o nome do recurso.
                    $stFiltro = " AND orcamento.recurso.exercicio = '".$stExercicio."' \n";
                    $obTOrcamentoRecurso->setDado('cod_recurso', $rsOrcamentoDespesa->getCampo('cod_recurso'));
                    $obTOrcamentoRecurso->recuperaRelacionamento($rsOrcamentoRecurso, $stFiltro);

                    # Recupera o nome do projeto, atividade.
                    $stFiltro  = " WHERE orcamento.pao.exercicio = '".$stExercicio."' \n";
                    $stFiltro .= " AND orcamento.pao.num_pao     = ".$rsOrcamentoDespesa->getCampo('num_pao')." \n";
                    $obTOrcamentoProjetoAtividade->recuperaSemMascara($rsOrcamentoProjetoAtividade, $stFiltro);

                    # Com os dados recuperados, seta o Hint na tabela
                    $arItens['stTitle'] = $rsItens->getCampo('cod_despesa').' - '.$rsItens->getCampo('dotacao_nom_conta').' - '.$rsItens->getCampo('cod_estrutural').' - '.$rsOrcamentoProjetoAtividade->getCampo('num_acao').' - '.$rsOrcamentoProjetoAtividade->getCampo('nom_pao').' - '.$rsOrcamentoRecurso->getCampo('cod_recurso').' - '.$rsOrcamentoRecurso->getCampo('nom_recurso');
                } else {
                    $arItens['stTitle'] = "";
                }

                # Recupera a quantidade atendida do item em outros Mapas, desconsiderando o Mapa em edição.
                $stFiltro  = " AND  mapa_solicitacao.cod_solicitacao       = ".$rsItens->getCampo('cod_solicitacao')."      \n";
                $stFiltro .= " AND  mapa_solicitacao.cod_entidade          = ".$rsItens->getCampo('cod_entidade')."         \n";
                $stFiltro .= " AND  mapa_solicitacao.exercicio_solicitacao = '".$rsItens->getCampo('exercicio_solicitacao')."' \n";
                $stFiltro .= " AND  mapa_item.cod_item                     = ".$rsItens->getCampo('cod_item')."             \n";
                $stFiltro .= " AND  mapa_item.cod_centro                   = ".$rsItens->getCampo('cod_centro')."           \n";

                if ($rsItens->getCampo('cod_despesa')) {
                    $stFiltro .= " AND  mapa_item_dotacao.cod_despesa      = ".$rsItens->getCampo('cod_despesa')."          \n";
                }

                if ($rsItens->getCampo('cod_conta')) {
                    $stFiltro .= " AND  mapa_item_dotacao.cod_conta        = ".$rsItens->getCampo('cod_conta')."            \n";
                }

                $obTComprasMapaItem->recuperaQtdeAtendidaEmMapas($rsQtdeAtendidaEmMapas, $stFiltro);

                # Quantidade atendida em outros Mapas.
                $inQtdeAtendida = $rsQtdeAtendidaEmMapas->getCampo('qtde_atendida');

                # Recupera o valor da última compra do item.
                $obTAlmoxarifadoCatalogoItem->setDado('cod_item'  , $rsItens->getCampo('cod_item'));
                $obTAlmoxarifadoCatalogoItem->setDado('exercicio' , $stExercicio);
                $obTAlmoxarifadoCatalogoItem->recuperaValorItemUltimaCompra($rsItemUltimaCompra);

                $rsItens->setCampo('valor_ultima_compra' , $rsItemUltimaCompra->getCampo('vl_unitario_ultima_compra'));

                $arItens['inId']                  = $proxId;
                $arItens['inId_solicitacao']      = $inId;
                # Dados da Solicitação
                $arItens['cod_solicitacao']       = $rsItens->getCampo('cod_solicitacao');
                $arItens['exercicio_solicitacao'] = $rsItens->getCampo('exercicio_solicitacao');
                $arItens['cod_entidade']          = $rsItens->getCampo('cod_entidade');

                # Quantidades.
                $arItens['quantidade_estoque']    = $rsItens->getCampo('quantidade_estoque');
                $arItens['quantidade_solicitada'] = ($rsItens->getCampo('quantidade') - $rsItens->getCampo('quantidade_anulada'));
                $arItens['quantidade_atendida']   = $inQtdeAtendida;
                $arItens['quantidade_mapa']       = ($arItens['quantidade_solicitada'] - $inQtdeAtendida);
                $arItens['quantidade_maxima']     = $arItens['quantidade_mapa'];

                # Dados do Item.
                $arItens['cod_item']              = $rsItens->getCampo('cod_item');
                $arItens['nom_item']              = $rsItens->getCampo('nom_item');
                $arItens['complemento']           = $rsItens->getCampo('complemento');
                $arItens['nom_unidade']           = $rsItens->getCampo('nom_unidade');
                $arItens['centro_custo']          = $rsItens->getCampo('centro_custo');
                $arItens['cod_centro']            = $rsItens->getCampo('cod_centro');
                $arItens['valor_unitario']        = $rsItens->getCampo('valor_unitario');
                $arItens['valor_total_mapa']      = ($rsItens->getCampo('valor_unitario') * ($arItens['quantidade_solicitada'] - $inQtdeAtendida));
                $arItens['valor_ultima_compra']   = $rsItens->getCampo('valor_ultima_compra');
                $arItens['lote']                  = $rsItens->getCampo('lote');

                # Dados da Dotação.
                $arItens['dotacao']               = $rsItens->getCampo('cod_despesa');
                $arItens['cod_despesa']           = $rsItens->getCampo('cod_despesa');
                $arItens['dotacao_nom_conta']     = $rsItens->getCampo('dotacao_nom_conta');
                $arItens['cod_conta']             = $rsItens->getCampo('cod_conta');
                $arItens['cod_estrutural']        = $rsItens->getCampo('cod_estrutural');
                $arItens['nom_conta']             = $rsItens->getCampo('nom_conta');

                # Verifica se a dotação já foi informada na solicitação ou se ainda está pendente.
                $arItens['boDotacao'] = (is_numeric($rsItens->getCampo('cod_despesa'))) ? 'T' : 'F';

                # Dados da Reserva.
                $arItens['cod_reserva']            = $rsItens->getCampo('cod_reserva');
                $arItens['exercicio_reserva']      = $rsItens->getCampo('exercicio_reserva');
                $arItens['vl_reserva']             = $rsItens->getCampo('vl_reserva');
                $arItens['vl_reserva_homologacao'] = $rsItens->getCampo('vl_reserva');
                
                if($rsItens->getCampo('vl_reserva')<$rsItens->getCampo('vl_total')){
                    $arItens['cod_reserva_solicitacao']         = $rsItens->getCampo('cod_reserva');
                    $arItens['exercicio_reserva_solicitacao']   = $rsItens->getCampo('exercicio_reserva');
                }

                # Se existir reserva de saldo para o item, não permite alterar no mapa.
                if (($rsItens->getCampo('vl_reserva') != '0.00') || ($rsItens->getCampo('cod_reserva'))) {
                    $arItens['boReserva'] = 'T';
                } else {
                    $arItens['boReserva']  = 'F';
                    $arItens['vl_reserva'] = $arItens['valor_total_mapa'];
                }

                if($boRegistroPreco=='true' || $boReservaAutorizacao){
                    $arItens['vl_reserva']             = '0.00';
                    $arItens['vl_reserva_homologacao'] = '0.00';
                }

                $arItens['incluir'] = $incluir;

                # Se existir quantidade disponivel para ser incluido no novo Mapa, adiciona no array de itens.
                if ($inQtdeAtendida < $arItens['quantidade_solicitada']) {
                    $proxId++;
                    $itens[] = $arItens;
                }

                $rsItens->proximo();
            }

            # Validação para confirmar se existem itens disponíveis e que não foram atendidos na solicitação informada.
            if (count($itens) == 0) {
                $boRetorno = false;
            } else {
                Sessao::write('itens' , $itens);
            }
        }
    }

    return $boRetorno;
}

function montaListaSolicitacoes($stTipoCotacao, $stAcao = '')
{
    $rsRecordSet = new RecordSet;

    $solicitacoes = Sessao::read('solicitacoes');

    if (is_array( $solicitacoes )) {
        $rsRecordSet->preenche( $solicitacoes );
    }

    $rsRecordSet->addFormatacao('valor_total'     ,'NUMERIC_BR');
    $rsRecordSet->addFormatacao('total_mapas'     ,'NUMERIC_BR');
    $rsRecordSet->addFormatacao('total_anulado'   ,'NUMERIC_BR');
    $rsRecordSet->addFormatacao('total_mapa'      ,'NUMERIC_BR');
    $rsRecordSet->addFormatacao('valor_a_anular'  ,'NUMERIC_BR');

    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    $obLista->setTitulo( 'Solicitações do Mapa');

    $obLista->setRecordSet( $rsRecordSet );
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Exercício" );
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Entidade" );
    $obLista->ultimoCabecalho->setWidth( 30 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Solicitação" );
    $obLista->ultimoCabecalho->setWidth( 4 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Data" );
    $obLista->ultimoCabecalho->setWidth( 8 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Valor Total Solicitado" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Valor em Outros Mapas" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    if ($stAcao != 'incluir') {
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Valor Anulado" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();
    }

    if ($stAcao == 'anular') {
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Valor a Anular" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();
    }

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Valor Neste Mapa" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "exercicio" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "[cod_entidade] - [nom_entidade]" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "cod_solicitacao" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "data" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "valor_total" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "total_mapas" );
    $obLista->commitDado();

    if ($stAcao != 'incluir') {
        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("CENTRO");
        $obLista->ultimoDado->setCampo( "total_anulado" );
        $obLista->commitDado();
    }

    if ($stAcao == 'anular') {
        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("CENTRO");
        $obLista->ultimoDado->setCampo( "valor_a_anular" );
        $obLista->commitDado();
    }

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "total_mapa" );
    $obLista->commitDado();

    if (($stAcao != 'anular') && ($stAcao != 'consultar')) {
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Ação");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "javascript: executaFuncaoAjax('delSolicitacao');" );
        $obLista->ultimaAcao->addCampo("","&inId=[inId]&stTipoCotacao=".$stTipoCotacao."");
        $obLista->commitAcao();
    }
    $obLista->montaHTML();

    $html = $obLista->getHTML();
    $html = str_replace("\n","",$html);
    $html = str_replace("  ","",$html);
    $html = str_replace("'","\\'",$html);

    $stJs  = "jQuery('#spnSolicitacoes').html('');          \n";
    $stJs .= "jQuery('#spnSolicitacoes').html('".$html."'); \n";

    return $stJs;
}

function montaListaItens($rsRecordSet, $stTipoCotacao, $stAcao = '')
{
    if (!is_object($rsRecordSet)) {

        $itens = Sessao::read('itens');

        if (!is_array($itens)) {
            $itens = array();
        }

        $rsRecordSet = new RecordSet;
        $rsRecordSet->preenche($itens);
    }

    $rsRecordSet->addFormatacao('valor_unitario'        , 'NUMERIC_BR');
    $rsRecordSet->addFormatacao('valor_total_mapa'      , 'NUMERIC_BR');
    $rsRecordSet->addFormatacao('valorAnular'           , 'NUMERIC_BR');
    $rsRecordSet->addFormatacao('quantidade_solicitada' , 'NUMERIC_BR_4');
    $rsRecordSet->addFormatacao('quantidadeAnular'      , 'NUMERIC_BR_4');
    $rsRecordSet->addFormatacao('quantidade_mapa'       , 'NUMERIC_BR_4');
    $rsRecordSet->addFormatacao('quantidade_anulada'    , 'NUMERIC_BR_4');

    $table = new Table;
    $table->setRecordset( $rsRecordSet );
    $table->setSummary('Itens do Mapa');

    $table->Head->addCabecalho('Solicitação'           ,  6);
    $table->Head->addCabecalho('Item'                  , 25);
    $table->Head->addCabecalho('Quantidade Solicitada' ,  9);
    $table->Head->addCabecalho('Quantidade no Mapa'    ,  9);

    if ($stAcao == 'consultar') {
        $table->Head->addCabecalho('Quantidade Anulada', 9);
    }

    $table->Head->addCabecalho('Valor de Referência' , 9);
    $table->Head->addCabecalho('Valor Total'         , 9);

    if ($stAcao == 'anular') {
        $table->Head->addCabecalho('Valor a Anular'      , 9);
        $table->Head->addCabecalho('Quantidade a Anular' , 9);
    }

    $stTitle = "[stTitle]";

    $table->Body->addCampo('cod_solicitacao'         , "C", $stTitle);
    $table->Body->addCampo('[cod_item] - [nom_item]' , "E", $stTitle);
    $table->Body->addCampo('quantidade_solicitada'   , "D", $stTitle);
    $table->Body->addCampo('quantidade_mapa'         , "D", $stTitle);

    if ($stAcao == 'consultar') {
        $table->Body->addCampo('quantidade_anulada' , "D", $stTitle);
    }

    $table->Body->addCampo('valor_unitario'        , "D", $stTitle);
    $table->Body->addCampo('valor_total_mapa'      , "D", $stTitle);

    if ($stAcao != 'consultar') {
        if ($stAcao != 'anular') {
            $table->Body->addAcao( 'ALTERAR' , "executaFuncaoAjax('alterarItem' , '&inId=%s&stTipoCotacao=%s')"         , array( 'inId' , $stTipoCotacao ));
            $table->Body->addAcao( 'EXCLUIR' , "executaFuncaoAjax('delItem' , '&inId=%s&stTipo=item&stTipoCotacao=%s')" , array( 'inId' , $stTipoCotacao ));
        } else {
            $table->Body->addCampo( 'valorAnular'      , "D", $stTitle );
            $table->Body->addCampo( 'quantidadeAnular' , "D", $stTitle );
            $table->Body->addAcao( 'ALTERAR' , "javascript: executaFuncaoAjax('anularItem', '&inId=%s&stTipoCotacao=%s');" , array( 'inId' , $stTipoCotacao ) );
        }
    }
    $table->Foot->addSoma( 'valor_total_mapa', 'D');

    $table->montaHTML();
    $html = $table->getHtml();

    $html = str_replace("\n","",$html);
    $html = str_replace("  ","",$html);
    $html = str_replace("'","\\'",$html);

    $stJs  = "jQuery('#spnItens').html('');          \n";
    $stJs .= "jQuery('#spnItens').html('".$html."'); \n";
    $stJs .= totais();

    return $stJs;
}

# Formulario para anulação de itens
function montaAnulacaoItem($inId)
{
    $itens = Sessao::read('itens');

    $arItem = array();

    foreach ($itens as $item => $valor) {
        if ($itens[$item]['inId'] == $inId) {
            $arItem = $itens[$item];
        }
    }

    $obFormItem = new Formulario;
    $obFormItem->addTitulo ('Dados do Item');

    $obHdnInId = new Hidden;
    $obHdnInId->setName  ( 'inId' );
    $obHdnInId->setValue ( $inId  );

    # Descrição do item
    $obLblItem = new Label;
    $obLblItem->setId     ('lblItem');
    $obLblItem->setRotulo ('Item');
    $obLblItem->setValue  ($arItem['cod_item'].' - '.$arItem['nom_item']);

    # Unidade de medida
    $obLblUnidadeMedida = new Label;
    $obLblUnidadeMedida->setId     ('lblUnidadeMedida');
    $obLblUnidadeMedida->setRotulo ('Unidade de Medida');
    $obLblUnidadeMedida->setvalue  ($arItem['nom_unidade']);

    # Centro de Custo
    $obLblCentroCusto = new Label;
    $obLblCentroCusto->setId     ('lblCentroCusto');
    $obLblCentroCusto->setRotulo ('Centro de Custo');
    $obLblCentroCusto->setValue  ($arItem['cod_centro'] . ' - ' . $arItem['centro_custo']);

    # Saldo em estoque
    $obLblSaldoEstoque = new Label;
    $obLblSaldoEstoque->setId     ('lblSaldoEstoque');
    $obLblSaldoEstoque->setRotulo ('Saldo em Estoque');
    $obLblSaldoEstoque->setValue  ($arItem['quantidade_estoque']);

    # Quantidade Solicitada
    $obLblQuantidadeSolicitada = new Label;
    $obLblQuantidadeSolicitada->setId ( 'lblQuantidadeSolicitada' );
    $obLblQuantidadeSolicitada->setRotulo ( 'Quantidade Solicitada' );
    $obLblQuantidadeSolicitada->setValue  (number_format( $arItem['quantidade_solicitada'], 4,',','.') );

    # Valor unitário
    $obLblValorUnitario = new Label;
    $obLblValorUnitario->setId     ('lblValorUnitario');
    $obLblValorUnitario->setName   ('lblValorUnitario');
    $obLblValorUnitario->setRotulo ('Valor Unitário');
    $obLblValorUnitario->setValue  (number_format( $arItem['valor_unitario'], 2, ",",".") );

    # Valor Unitário.
    $obHdnValorUnitario = new Hidden;
    $obHdnValorUnitario->setId    ('hdnValorUnitario');
    $obHdnValorUnitario->setName  ('hdnValorUnitario');
    $obHdnValorUnitario->setValue ($arItem['valor_unitario']);

    # Quantidade do mapa
    $obLblQuantiMapa = new Label;
    $obLblQuantiMapa->setId ( 'lblQuantiMapa' );
    $obLblQuantiMapa->setRotulo ( 'Quantidade do Mapa' );
    $obLblQuantiMapa->setValue  ( $arItem['quantidade_mapa'] );

    # Valor total
    $obLblValorTotal = new Label;
    $obLblValorTotal->setId ( 'lblValorTotal' );
    $obLblValorTotal->setRotulo ( 'Valor total' );
    $obLblValorTotal->setValue (  number_format( $arItem['valor_total_mapa'], 2, ",",".") );

    # Dotação
    $obLblDotacao = new Label ;
    $obLblDotacao->setId     ('lblDotacao');
    $obLblDotacao->setRotulo ('Dotação');
    $obLblDotacao->setValue  ($arItem['dotacao_nom_conta']);

    # Desdobramento
    $obLblDesdobramento = new Label;
    $obLblDesdobramento->setRotulo ('Desdobramento');
    $obLblDesdobramento->setValue  ($arItem['cod_estrutural']." - ".$arItem['nom_conta']);

    # Valor Reservado no exercicio
    $obLblValorReserva = new Label;
    $obLblValorReserva->setRotulo ("Valor Reservado no Exercício");
    $obLblValorReserva->setValue  (number_format( $arItem['vl_reserva'], 2, ",","."));

    # Quantidade a anular
    $obNumQuantidadeAnular = new Numerico;
    $obNumQuantidadeAnular->setId       ('flQuantidadeAnular');
    $obNumQuantidadeAnular->setname     ('flQuantidadeAnular');
    $obNumQuantidadeAnular->setRotulo   ('Quantidade a Anular');
    $obNumQuantidadeAnular->setDefinicao('NUMERIC');
    $obNumQuantidadeAnular->setSize(14);
    $obNumQuantidadeAnular->setMaxLength(13);
    $obNumQuantidadeAnular->setDecimais (4);
    $obNumQuantidadeAnular->obEvento->setOnChange("montaParametrosGET('calculaTotalAnulacao');");

    # Valor a anular
    $obNumValorAnular = new Numerico;
    $obNumValorAnular->setId     ('flValorAnular');
    $obNumValorAnular->setname   ('flValorAnular');
    $obNumValorAnular->setRotulo ('Valor a Anular');
    $obNumValorAnular->obEvento->setOnBlur("montaParametrosGET('calculaQtdeTotalAnulacao'); ");

    $obFormItem->addHidden     ($obHdnInId);
    $obFormItem->addHidden     ($obHdnValorUnitario);
    $obFormItem->addComponente ($obLblItem);
    $obFormItem->addComponente ($obLblUnidadeMedida);
    $obFormItem->addComponente ($obLblCentroCusto);
    $obFormItem->addComponente ($obLblSaldoEstoque);
    $obFormItem->addComponente ($obLblQuantidadeSolicitada);
    $obFormItem->addComponente ($obLblValorUnitario);
    $obFormItem->addComponente ($obLblValorTotal);
    $obFormItem->addComponente ($obLblDotacao);
    $obFormItem->addComponente ($obLblDesdobramento);
    $obFormItem->addComponente ($obLblValorReserva);
    $obFormItem->addComponente ($obNumQuantidadeAnular);
    $obFormItem->addComponente ($obNumValorAnular);

    $obBtnSalvar = new Button;
    $obBtnSalvar->setName             ("btnSalvar");
    $obBtnSalvar->setId               ("btnSalvar");
    $obBtnSalvar->setValue            ("Salvar");
    $obBtnSalvar->setTipo             ("button");
    $obBtnSalvar->setStyle            ('padding:0px 10px;');
    $obBtnSalvar->obEvento->setOnClick("montaParametrosGET('salvaAnularItem','inId,flQuantidadeAnular,flValorAnular,hdnValorUnitario');");

    $obFormItem->defineBarraAba( array($obBtnSalvar) ,'','' );

    $obFormItem->montaInnerHtml();
    $stJs .= "jQuery('#spnItem').html('".$obFormItem->getHTML()."');    \n";

    return $stJs;

}

# Funçao para anular itens do Mapa.
function anularItem($inId, $flValorAnular, $flQuantidadeAnular, $hdnValorUnitario)
{
    # Salva a anulação de itens
    $itens        = Sessao::read('itens');
    $solicitacoes = Sessao::read('solicitacoes');
    $stJs = $stErro = '';

    if (!empty($flValorAnular) && empty($flQuantidadeAnular)) {
        $flQuantidadeAnular = $flValorAnular / $hdnValorUnitario;
        $flQuantidadeAnular = number_format($flQuantidadeAnular, 4, ',', '.');
    } elseif (empty($flValorAnular) && !empty($flQuantidadeAnular)) {
        $flValorAnular = $hdnValorUnitario * $flQuantidadeAnular;
        $flValorAnular = number_format($flValorAnular, 2, ',', '.' );
    }

    foreach ($itens as $item => $valor) {
        if ($itens[$item]['inId'] == $inId) {

            $flValorAnular      = str_replace('.', '', $flValorAnular);
            $flValorAnular      = str_replace(',', '.', $flValorAnular);

            $flQuantidadeAnular = str_replace('.', '', $flQuantidadeAnular);
            $flQuantidadeAnular = str_replace(',', '.', $flQuantidadeAnular);

            $flValorTotalMapa = number_format($itens[$item]['vl_mapa'], 2, ",", ".");
            $flValorTotalMapa = str_replace('.', '', $flValorTotalMapa);
            $flValorTotalMapa = str_replace(',', '.', $flValorTotalMapa);

            if ($flValorAnular > $flValorTotalMapa) {
                $stErro = "O valor a anular deve ser menor ou igual ao valor do item.";
            } elseif ($flQuantidadeAnular > $itens[$item]['quantidade_mapa']) {
                $stErro = "A quantidade a anular deve ser menor ou igual a quantidade do item.";
            } else {
                # procurando a solicitação pra marcar que ela tem itens anulados e atualizar os totais
                foreach ($solicitacoes as $inPos => $solicitacao) {
                    if ($valor['inId_solicitacao'] == $solicitacao['inId']) {
                        $solicitacoes[$inPos]['anulada'] = true;

                        # Atualizando total neste mapa e valor a anular na aba mapa
                        $nuValorAnularSolicitacao = $solicitacoes[$inPos]['valor_a_anular'];
                        $nuValorNesteMapa         = $solicitacoes[$inPos]['total_mapa'];

                        $nuValorAnularSolicitacao =  ( $nuValorAnularSolicitacao - $itens[$item]['valorAnular'] ) + $flValorAnular;
                        $nuValorNesteMapa         =  ( $nuValorNesteMapa + $itens[$item]['valorAnular'] ) - $flValorAnular ;

                        $solicitacoes[$inPos]['valor_a_anular']   = $nuValorAnularSolicitacao;
                        $solicitacoes[$inPos]['total_mapa']       = $nuValorNesteMapa;
                    }
                }
                $itens[$item]['valorAnular']      = $flValorAnular;
                $itens[$item]['quantidadeAnular'] = $flQuantidadeAnular;
                $itens[$item]['anulado']          = true;
                $itens[$item]['valor_total_mapa'] = ($itens[$item]['valor_total_mapa_original'] - $flValorAnular);
            }
        }
    }

    Sessao::write('itens'        , $itens       );
    Sessao::write('solicitacoes' , $solicitacoes);

    if ($stErro) {
        $stJs .= "alertaAviso('".$stErro."','form','erro','".Sessao::getId()."');\n";
    } else {
        $rsRecordSet = new RecordSet;
        $rsRecordSet->preenche( $itens );

        $stJs .= "jQuery('#spnItem').html('&nbsp;');  \n";
        $stJs .= "jQuery('#flQuantidadeAnular').focus(); \n ";
        $stJs .= "jQuery('#Ok').removeAttr('disabled'); \n";
        $stJs .=  montaListaItens($rsRecordSet, ' ', 'anular');
        $stJs .=  montaListaSolicitacoes('', 'anular');
        $stJs .= totais();
    }

    return $stJs;
}

# Preenche os arrays de solicitações e de itens das solicitações vinculadas ao Mapa.
function montaMapa($inCodMapa, $stExercicio)
{
    global $request;

    $boReservaAutorizacao = SistemaLegado::pegaConfiguracao('reserva_autorizacao', '35', Sessao::getExercicio());
    $boReservaAutorizacao = ($boReservaAutorizacao == 'true') ? true : false;

    $stAcao = $request->get('stAcao');

    $rsOrcamentoDespesa	         = new RecordSet;
    $rsOrcamentoRecurso          = new RecordSet;
    $rsOrcamentoProjetoAtividade = new RecordSet;

    $obTAlmoxarifadoCatalogoItem  = new TAlmoxarifadoCatalogoItem;

    $obTComprasMapa               = new TComprasMapa;
    $obTComprasMapaItem           = new TComprasMapaItem;
    $obTComprasMapaItemDotacao    = new TComprasMapaItemDotacao;

    $obTOrcamentoDespesa	      = new TOrcamentoDespesa;
    $obTOrcamentoRecurso	      = new TOrcamentoRecurso;
    $obTOrcamentoProjetoAtividade = new TOrcamentoProjetoAtividade;

    # Recupera os dados do Mapa de Compras.
    $obTComprasMapa->setDado ('cod_mapa'  , $inCodMapa);
    $obTComprasMapa->setDado ('exercicio' , $stExercicio);
    $obTComprasMapa->consultar();

    Sessao::write('inTipoLicitacao' , $obTComprasMapa->getDado('cod_tipo_licitacao'));

    $stFiltro  = "     AND  mapa_solicitacao.cod_mapa  = ".$inCodMapa."   \n";
    $stFiltro .= "     AND  mapa_solicitacao.exercicio = '".$stExercicio."' \n";
    $stOrdem  = " ORDER BY  solicitacao.cod_solicitacao                   \n";
    $obTComprasMapa->recuperaMapaSolicitacoes($rsMapaSolicitacao, $stFiltro, $stOrdem);

    Sessao::write('ultimoCodigo' , 0);
    $proxId = 0;

    $ultimoCodigo = Sessao::read('ultimoCodigo');

    while (!$rsMapaSolicitacao->eof()) {
        $arSolicitacao['inId']                  = $ultimoCodigo++;
        $arSolicitacao['exercicio']             = $rsMapaSolicitacao->getCampo('exercicio');
        $arSolicitacao['exercicio_solicitacao'] = $rsMapaSolicitacao->getCampo('exercicio');
        $arSolicitacao['cod_solicitacao']       = $rsMapaSolicitacao->getCampo('cod_solicitacao');
        $arSolicitacao['nom_entidade']          = $rsMapaSolicitacao->getCampo('nom_entidade');
        $arSolicitacao['cod_entidade']          = $rsMapaSolicitacao->getCampo('cod_entidade');
        $arSolicitacao['valor_total']           = $rsMapaSolicitacao->getCampo('valor_total');
        $arSolicitacao['data']                  = $rsMapaSolicitacao->getCampo('data_solicitacao');
        $arSolicitacao['total_mapa']            = $rsMapaSolicitacao->getCampo('total_mapa');
        $arSolicitacao['total_mapas']           = ($rsMapaSolicitacao->getCampo('total_mapas') - $arSolicitacao['total_mapa']);
        # Total anulado do Mapa em questão.
        $arSolicitacao['total_anulado']         = $rsMapaSolicitacao->getCampo('total_mapa_anulado');
        $arSolicitacao['valor_a_anular']        = 0;   /// este campo só será preenchi na rotina de anulação
        $arSolicitacao['incluir']               = false;
        $arSolicitacao['registro_precos']       = ($rsMapaSolicitacao->getCampo('registro_precos')=='t') ? 'true' : 'false';

        $solicitacoes[] = $arSolicitacao;

        if($rsMapaSolicitacao->getCampo('registro_precos') == 't'){
            $stJs .= " jQuery('#boRegistroPrecoNao').attr('disabled', 'disabled');  \n";
            $stJs .= " jQuery('#boRegistroPrecoSim').attr('checked', true);         \n";
        }else{
            $stJs  = " jQuery('#boRegistroPrecoSim').attr('disabled', 'disabled');  \n";
            $stJs .= " jQuery('#boRegistroPrecoNao').attr('checked', true);         \n";
        }

        # Recupera todos os itens da Solicitação que serão importados no Mapa de Compras.
        $inCodEntidade          = $rsMapaSolicitacao->getCampo('cod_entidade');
        $inCodSolicitacao       = $rsMapaSolicitacao->getCampo('cod_solicitacao');
        $stExercicioSolicitacao = $rsMapaSolicitacao->getCampo('exercicio');

        $obTComprasMapaItem->setDado("cod_mapa"              , $inCodMapa);
        $obTComprasMapaItem->setDado("exercicio_mapa"        , $stExercicio);
        $obTComprasMapaItem->setDado("cod_entidade"          , $inCodEntidade);
        $obTComprasMapaItem->setDado("cod_solicitacao"       , $inCodSolicitacao);
        $obTComprasMapaItem->setDado("exercicio_solicitacao" , $stExercicioSolicitacao);
        $obTComprasMapaItem->recuperaItemSolicitacaoMapa($rsItens, $stFiltro);

        # GRAVA O ARRAY DE ITEM
        while (!$rsItens->eof()) {

            # Recupera as descrições de recurso, projeto e atividade.
            if ($rsItens->getCampo('dotacao')) {
                $stFiltro  = "";
                $stFiltro .= " AND OD.exercicio   = '".$stExercicio."' \n";
                $stFiltro .= " AND OD.cod_despesa = ".$rsItens->getCampo('dotacao')." \n";
                $obTOrcamentoDespesa->recuperaRelacionamento( $rsOrcamentoDespesa, $stFiltro );

                # Faz a busca do nome do recurso.
                $stFiltro  = "";
                $stFiltro .= " AND orcamento.recurso.exercicio = '".$stExercicio."' \n";
                $obTOrcamentoRecurso->setDado('cod_recurso', $rsOrcamentoDespesa->getCampo('cod_recurso'));
                $obTOrcamentoRecurso->recuperaRelacionamento( $rsOrcamentoRecurso, $stFiltro );

                # Faz a busca do nome do projeto/atividade.
                $stFiltro  = "";
                $stFiltro .= " WHERE orcamento.pao.exercicio = '".$stExercicio."' \n";
                $stFiltro .= "   AND orcamento.pao.num_pao   = ".$rsOrcamentoDespesa->getCampo('num_pao')." \n";
                $obTOrcamentoProjetoAtividade->recuperaSemMascara( $rsOrcamentoProjetoAtividade, $stFiltro );

                $arItens['stTitle'] = $rsItens->getCampo('dotacao').' - '.$rsItens->getCampo('dotacao_nom_conta').' - '.$rsItens->getCampo('cod_estrutural').' - '.$rsOrcamentoProjetoAtividade->getCampo('num_acao').' - '.$rsOrcamentoProjetoAtividade->getCampo('nom_pao').' - '.$rsOrcamentoRecurso->getCampo('cod_recurso').' - '.$rsOrcamentoRecurso->getCampo('nom_recurso');
            } else {
                $arItens['stTitle'] = '&nbsp;';
            }

            # Recupera a quantidade atendida do item em outros Mapas, desconsiderando o Mapa em edição.
            $stFiltro  = " AND  mapa_solicitacao.cod_solicitacao       = ".$rsItens->getCampo('cod_solicitacao')."      \n";
            $stFiltro .= " AND  mapa_solicitacao.cod_entidade          = ".$rsItens->getCampo('cod_entidade')."         \n";
            $stFiltro .= " AND  mapa_solicitacao.exercicio_solicitacao = '".$rsItens->getCampo('exercicio_solicitacao')."' \n";
            $stFiltro .= " AND  mapa_item.cod_item                     = ".$rsItens->getCampo('cod_item')."             \n";
            $stFiltro .= " AND  mapa_item.cod_centro                   = ".$rsItens->getCampo('cod_centro')."           \n";
            $stFiltro .= " AND  mapa_solicitacao.cod_mapa             <> ".$inCodMapa."                                 \n";
            $stFiltro .= " AND  mapa_solicitacao.exercicio             = '".$stExercicio."'                             \n";

            if ($rsItens->getCampo('cod_despesa')) {
                $stFiltro .= " AND  mapa_item_dotacao.cod_despesa      = ".$rsItens->getCampo('cod_despesa')."          \n";
            }

            if ($rsItens->getCampo('cod_conta')) {
                $stFiltro .= " AND  mapa_item_dotacao.cod_conta        = ".$rsItens->getCampo('cod_conta')."            \n";
            }

            $obTComprasMapaItem->recuperaQtdeAtendidaEmMapas($rsQtdeAtendidaEmMapas, $stFiltro);

            $inQtdeAtendida = $rsQtdeAtendidaEmMapas->getCampo('qtde_atendida');

            # Recupera o valor unitário do item.
            $obTAlmoxarifadoCatalogoItem->setDado('cod_item'  , $rsItens->getCampo('cod_item'));
            $obTAlmoxarifadoCatalogoItem->setDado('exercicio' , $stExercicio);
            $obTAlmoxarifadoCatalogoItem->recuperaValorItemUltimaCompra($rsItemUltimaCompra);

            $rsItens->setCampo('valor_ultima_compra', $rsItemUltimaCompra->getCampo('vl_unitario_ultima_compra'));

            $arItens['inId']                          = $proxId;
            $arItens['inId_solicitacao']              = $ultimoCodigo-1;

            # Dados da Solicitação.
            $arItens['cod_entidade']                  = $rsItens->getCampo('cod_entidade');
            $arItens['cod_solicitacao']               = $rsItens->getCampo('cod_solicitacao');
            $arItens['exercicio_solicitacao']         = $rsItens->getCampo('exercicio_solicitacao');

            # Dados do Item.
            $arItens['cod_item']                      = $rsItens->getCampo('cod_item');
            $arItens['nom_item']                      = $rsItens->getCampo('nom_item');
            $arItens['complemento']                   = $rsItens->getCampo('complemento');
            $arItens['nom_unidade']                   = $rsItens->getCampo('nom_unidade');
            $arItens['centro_custo']                  = $rsItens->getCampo('centro_custo');
            $arItens['cod_centro']                    = $rsItens->getCampo('cod_centro');
            $arItens['lote']                          = $rsItens->getCampo('lote');

            # Quantidades do Item no Mapa de Compras.
            $arItens['quantidade_estoque']            = $rsItens->getCampo('quantidade_estoque');
            $arItens['quantidade_solicitada']         = $rsItens->getCampo('quantidade_solicitada');
            $arItens['quantidade_mapa']               = $rsItens->getCampo('quantidade_mapa') - $rsItens->getCampo('quantidade_mapa_anulada');
            $arItens['quantidade_mapa_original']      = $rsItens->getCampo('quantidade_mapa');
            $arItens['quantidade_anulada']            = $rsItens->getCampo('quantidade_mapa_anulada');
            $arItens['quantidade_maxima']             = ($rsItens->getCampo('quantidade_solicitada') - $inQtdeAtendida);
            $arItens['quantidade_atendida']           = $inQtdeAtendida;

            $arItens['dotacao']                       = $rsItens->getCampo('dotacao');
            $arItens['cod_despesa']                   = $rsItens->getCampo('cod_despesa');
            $arItens['cod_conta']                     = $rsItens->getCampo('cod_conta');
            $arItens['exercicio']                     = $rsItens->getCampo('exercicio');
            $arItens['vl_mapa']                       = $rsItens->getCampo('valor_total_mapa');
            $arItens['cod_reserva']                   = $rsItens->getCampo('cod_reserva');
            $arItens['exercicio_reserva']             = $rsItens->getCampo('exercicio_reserva');

            # Valores Monetários.
            $arItens['valor_unitario']                = $rsItens->getCampo('valor_unitario');
            $arItens['valor_ultima_compra']           = $rsItens->getCampo('valor_ultima_compra');

            # Total do Mapa corrente deve ser o valor unitário * quantidade em mapa corrente.
            $arItens['valor_total_mapa']              = ($rsItens->getCampo('valor_unitario') * $arItens['quantidade_mapa']);
            $arItens['valor_total_mapa_original']     = $arItens['valor_total_mapa'];

            $arItens['cod_reserva_solicitacao']       = $rsItens->getCampo('cod_reserva_solicitacao');
            $arItens['exercicio_reserva_solicitacao'] = $rsItens->getCampo('exercicio_reserva_solicitacao');
            $arItens['vl_reserva_solicitacao']        = $rsItens->getCampo('vl_reserva_solicitacao');

            # Usado na listagem de itens do mapa de compra como "hint"
            $arItens['num_pao']     = $rsOrcamentoProjetoAtividade->getCampo('num_pao');
            $arItens['nom_pao']     = $rsOrcamentoProjetoAtividade->getCampo('nom_pao');
            $arItens['cod_recurso'] = $rsOrcamentoRecurso->getCampo('cod_recurso');
            $arItens['nom_recurso'] = $rsOrcamentoRecurso->getCampo('nom_recurso');

            # Verifica se a dotação já foi informada na solicitação ou se ainda está pendente.
            $arItens['boDotacao'] = (is_numeric($rsItens->getCampo('dotacao'))) ? 'T' : 'F';

            $arItens['dotacao_nom_conta']      = $rsItens->getCampo('dotacao_nom_conta');
            $arItens['conta_despesa']          = $rsItens->getCampo('conta_despesa');  //-> desdobramento
            $arItens['nom_conta']              = $rsItens->getCampo('nom_conta');
            $arItens['cod_estrutural']         = $rsItens->getCampo('cod_estrutural');
            $arItens['vl_reserva']             = $rsItens->getCampo('vl_reserva');
            $arItens['vl_reserva_homologacao'] = $rsItens->getCampo('vl_reserva');
            $arItens['reservaHomologacao']     = $rsItens->getCampo('quantidade_mapa') < $rsItens->getCampo('quantidade_maxima');

            if ($rsItens->getCampo('vl_reserva') != '0.00') {
                # Se já existir reserva de saldo para o item, não pode ser alterado no mapa.
                $arItens['boReserva'] = 'T';
            } else {
                $arItens['boReserva']  = 'F';
                $arItens['vl_reserva']             = $arItens['valor_total_mapa'];
            }
            
            if($boReservaAutorizacao){
                $arItens['vl_reserva']             = '0.00';
                $arItens['vl_reserva_homologacao'] = '0.00';
            }

            $arItens['incluir'] = false;

            if ($stAcao == 'anular' && $arItens['quantidade_mapa'] > 0) {
                $itens[] = $arItens;
                $proxId++;
            } elseif ($stAcao != 'anular') {
                $itens[] = $arItens;
                $proxId++;
            }

            $rsItens->proximo();
        }

        $rsMapaSolicitacao->proximo();
    }

    if(isset($stJs))
        SistemaLegado::executaFrameOculto($stJs);

    Sessao::write('itens', $itens);
    Sessao::write('solicitacoes', $solicitacoes);
}

function totais()
{
    $itens = Sessao::read('itens');

    if (!is_array($itens)) {
        $itens = array();
    }

    $stItem       = '';
    $nuValor      = 0;
    $nuQuantidade = 0;

    $rsItens  = new RecordSet;
    $arTotais = array();

    $rsItens->preenche( $itens );
    $rsItens->ordena ( 'nom_item', 'ASC', SORT_STRING );
    $rsItens->setPrimeiroElemento();
    
    while ( !$rsItens->eof() ) {
        $arTotais[ $rsItens->getCampo('cod_item') ]['valor']               = $arTotais[ $rsItens->getCampo('cod_item') ]['valor'] + $rsItens->getCampo( 'vl_reserva_solicitacao' );
        $arTotais[ $rsItens->getCampo('cod_item') ]['quantidade']          = $arTotais[ $rsItens->getCampo('cod_item') ]['quantidade'] + $rsItens->getCampo ( 'quantidade_mapa');
        $arTotais[ $rsItens->getCampo('cod_item') ]['nom_item']            = $rsItens->getCampo('nom_item');
        $arTotais[ $rsItens->getCampo('cod_item') ]['valor_ultima_compra'] = $rsItens->getCampo('valor_ultima_compra');
        $arTotais[ $rsItens->getCampo('cod_item') ]['cod_item']            = $rsItens->getCampo('cod_item');
        $rsItens->proximo();
    }

    $total = array();
    foreach ($arTotais as $registro) {
       $total[] =  $registro ;
    }

    $rsTotais = new RecordSet;
    $rsTotais->preenche( $total );

    $stJs = montaListaTotais( $rsTotais );

    return $stJs;
}

function montaListaTotais($rsTotais)
{
    // formata recordset
    $rsTotais->setPrimeiroElemento();

    $rsTotais->addFormatacao ( 'valor'               , 'NUMERIC_BR' );
    $rsTotais->addFormatacao ( 'valor_ultima_compra' , 'NUMERIC_BR' );
    $rsTotais->addFormatacao ( 'quantidade'          , 'NUMERIC_BR_4' );

    $table = new TableTree();
    $table->setRecordset( $rsTotais );
    $table->setSummary('Totais por Item');

    $table->setArquivo( CAM_GP_COM_INSTANCIAS . 'mapaCompras/OCManterMapaCompras.php');
    // parametros do recordSet
    $table->setParametros( array( "cod_item") );
    $table->setComplementoParametros ( "stCtrl=detalhaItem" );

    $table->Head->addCabecalho( 'Item'            , 50 );
    $table->Head->addCabecalho( 'Quantidade Total', 12 );
    $table->Head->addCabecalho( 'Valor da Última Compra', 13 );
    $table->Head->addCabecalho( 'Valor Total'     , 15 );

    $table->Body->addCampo( '[cod_item] - [nom_item]' , 'E' );
    $table->Body->addCampo( 'quantidade', 'D' );
    $table->Body->addCampo( 'valor_ultima_compra' , 'D' );
    $table->Body->addCampo( 'valor' , 'D' );

    $table->Foot->addSoma( 'valor', 'D');

    $table->montaHTML();
    $stHTML = $table->getHtml();
    $stHTML = str_replace("\n","",$stHTML);
    $stHTML = str_replace("  ","",$stHTML);
    $stHTML = str_replace("'","\\'",$stHTML);

    $stJs  = "jQuery('#spnTotaisItens').html('&nbsp;');       \n";
    $stJs .= "jQuery('#spnTotaisItens').html('".$stHTML."'); \n";

    return $stJs;
}

function detalhaItem($inCodItem)
{
    $itens = Sessao::read('itens');

    $arDetalheItem =  array();
    $inId = 0;
    foreach ($itens as $registro) {
        if ($registro['cod_item'] == $inCodItem) {
            $arDetalheItem[$inId]['cod_solicitacao'      ] = $registro['cod_solicitacao'      ];
            $arDetalheItem[$inId]['quantidade_solicitada'] = $registro['quantidade_solicitada'];
            $arDetalheItem[$inId]['quantidade_mapa'      ] = $registro['quantidade_mapa'      ];
            $arDetalheItem[$inId]['valor_unitario'       ] = $registro['valor_unitario'       ];
            $arDetalheItem[$inId]['valor_total_mapa'     ] = $registro['valor_total_mapa'     ];
            $arDetalheItem[$inId]['vl_reserva'           ] = $registro['vl_reserva'           ];
            $arDetalheItem[$inId]['dotacao'              ] = $registro['cod_despesa']." - ".$registro['dotacao_nom_conta'];
            $inId++;
        }
    }

    $rsDetalhesItens = new RecordSet;
    $rsDetalhesItens->preenche( $arDetalheItem );

    $stJs = montaSpamDetalheItem ( $rsDetalhesItens );

    return $stJs;
}

function montaSpamDetalheItem($rsDados)
{
   $rsDados->addFormatacao ( 'quantidade_solicitada' ,'NUMERIC_BR_4' ) ;
   $rsDados->addFormatacao ( 'quantidade_mapa'       ,'NUMERIC_BR_4' ) ;
   $rsDados->addFormatacao ( 'valor_unitario'        ,'NUMERIC_BR' ) ;
   $rsDados->addFormatacao ( 'valor_total_mapa'      ,'NUMERIC_BR' ) ;
   $rsDados->addFormatacao ( 'vl_reserva'            ,'NUMERIC_BR' ) ;

   $table = new Table();

   $table->setRecordset( $rsDados );

   $table->setSummary('Itens');

   $table->Head->addCabecalho( 'Solicitação'           , 5 );
   $table->Head->addCabecalho( 'Quantidade Solicitada' , 10 );
   $table->Head->addCabecalho( 'Quantidade no Mapa'    , 10 );
   $table->Head->addCabecalho( 'Valor de Referência'   , 12 );
   $table->Head->addCabecalho( 'Valor Total'           , 12 );
   $table->Head->addCabecalho( 'Valor Reservado'       , 12 );
   $table->Head->addCabecalho( 'Dotação Orçamentária'  , 10 );

   $table->Body->addCampo( 'cod_solicitacao'      , 'C' );
   $table->Body->addCampo( 'quantidade_solicitada', 'D' );
   $table->Body->addCampo( 'quantidade_mapa'      , 'D' );
   $table->Body->addCampo( 'valor_unitario'       , 'D' );
   $table->Body->addCampo( 'valor_total_mapa'     , 'D' );
   $table->Body->addCampo( 'vl_reserva'           , 'D' );
   $table->Body->addCampo( 'dotacao'              , 'E' );

   $table->montaHTML();
   $stHTML = $table->getHtml();
   $stHTML = str_replace("\n","",$stHTML);
   $stHTML = str_replace("  ","",$stHTML);
   $stHTML = str_replace("'","\\'",$stHTML);

   return $stHTML;
}

function liberaMapaAnulacao($inCodMapa, $stExercicio)
{
    $obTComprasCompraDireta = new TComprasCompraDireta;
    $stFiltro  = " WHERE exercicio_mapa = '".$stExercicio."'";
    $stFiltro .= "   AND cod_mapa       =  ".$inCodMapa;
    $obTComprasCompraDireta->recuperaTodos($rsRecordSet, $stFiltro);

    if ($rsRecordSet->getNumLinhas() > 0) {

        $obTComprasCompraDiretaAnulacao = new TComprasCompraDiretaAnulacao;
        while (!$rsRecordSet->eof()) {
            $stFiltro  = " WHERE cod_compra_direta  =  ".$rsRecordSet->getCampo('cod_compra_direta');
            $stFiltro .= "   AND cod_entidade       =  ".$rsRecordSet->getCampo('cod_entidade');
            $stFiltro .= "   AND exercicio_entidade = '".$rsRecordSet->getCampo('exercicio_entidade')."'";
            $stFiltro .= "   AND cod_modalidade     =  ".$rsRecordSet->getCampo('cod_modalidade');
            $obTComprasCompraDiretaAnulacao->recuperaTodos($rsAnulacoes, $stFiltro);
            if (!$rsAnulacoes->getNumLinhas() > 0) {
                $boExecuta = true;
            }
            $rsRecordSet->proximo();
        }
        if ($boExecuta) {
            $stJs .= "BloqueiaFrames(true,false);";
            $stJs .= "alertPopUp('Mapa de Compras','Mapa de Compras possui vínculo com Compra Direta.','window.location.href=\'LSManterMapaCompras.php\';');";
        }
    }

    if (!$boExecuta) {
        $obTLicitacaoLicitacao = new TLicitacaoLicitacao;
        $stFiltro  = " WHERE exercicio_mapa = '".$stExercicio."'";
        $stFiltro .= "   AND cod_mapa       =  ".$inCodMapa;
        $obTLicitacaoLicitacao->recuperaTodos($rsRecordSet, $stFiltro);

        if ($rsRecordSet->getNumLinhas() > 0) {
            $obTLicitacaoLicitacaoAnulada = new TLicitacaoLicitacaoAnulada;
            while (!$rsRecordSet->eof()) {
                $stFiltro  = " WHERE cod_licitacao  =  ".$rsRecordSet->getCampo('cod_licitacao');
                $stFiltro .= "   AND cod_modalidade =  ".$rsRecordSet->getCampo('cod_modalidade');
                $stFiltro .= "   AND cod_entidade   =  ".$rsRecordSet->getCampo('cod_entidade');
                $stFiltro .= "   AND exercicio      = '".$rsRecordSet->getCampo('exercicio')."'";
                $obTLicitacaoLicitacaoAnulada->recuperaTodos($rsAnulacoes, $stFiltro);
                if (!$rsAnulacoes->getNumLinhas() > 0) {
                    $boExecuta = true;
                }
                $rsRecordSet->proximo();
            }
            if ($boExecuta) {
                $stJs .= "BloqueiaFrames(true,false);";
                $stJs .= "alertPopUp('Mapa de Compras','Mapa de Compras possui vínculo com a Licitação.','window.location.href=\'LSManterMapaCompras.php\';');";
            }
        }
    }

    if (!$boExecuta) {
        $arSolicitacao = Sessao::read('solicitacoes');
        foreach ($arSolicitacao as $key => $value) {
            $stJs .= "jQuery('#boRegistroPreco').val('".$value['registro_precos']."'); \n ";
        }
    }

    return $stJs;

}

function preencheRegistroPrecos($inCodMapa, $stExercicio)
{
    $rsMapaTipoRegistroPrecos = new RecordSet();
    $obTComprasMapa = new TComprasMapa();
    # Recupera os dados do Mapa de Compras.
    $obTComprasMapa->setDado ('cod_mapa'  , $inCodMapa);
    $obTComprasMapa->setDado ('exercicio' , $stExercicio);
    $obTComprasMapa->recuperaTipoMapa($rsMapaTipoRegistroPrecos,'','','');

    if( $rsMapaTipoRegistroPrecos->getCampo('registro_precos') == 't' )
        $stJs .= " jQuery('#stTipoRegistroPrecos').html('Sim');  \n";
    else
        $stJs .= " jQuery('#stTipoRegistroPrecos').html('Não');  \n";
        
    return $stJs;
}

switch ($request->get("stCtrl")) {
    case 'detalhaItem':
        $stJs = detalhaItem( $request->get('cod_item') );
    break;

    case 'calculaTotalAnulacao':
        $flQuantidadeAnular = str_replace('.', '' , $request->get('flQuantidadeAnular'));
        $flQuantidadeAnular = str_replace(',', '.', $flQuantidadeAnular);
        $total = $request->get('hdnValorUnitario') * $flQuantidadeAnular;
        $total = number_format($total, 2, ',', '.' );
        $stJs  = "jQuery('#flValorAnular').val('".$total."');  \n";
    break;

    case 'calculaQtdeTotalAnulacao':
        $flValorAnular = $request->get('flValorAnular');
        $flValorAnular = str_replace('.', '' , $flValorAnular);
        $flValorAnular = str_replace(',', '.', $flValorAnular);
        $total = $flValorAnular / $request->get('hdnValorUnitario');
        $total = number_format($total, 4, ',', '.');
        $stJs = "jQuery('#flQuantidadeAnular').val('".$total."'); \n ";
    break;

    case 'incluirSolicitacao':
        $stExercicioSolicitacao   = $request->get('stExercicioSolicitacao');
        $inCodEntidadeSolicitacao = $request->get('inCodEntidadeSolicitacao');
        $inCodSolicitacao         = $request->get('inCodSolicitacao');
        $inCodTipoLicitacao       = $request->get('inCodTipoLicitacao');
        $boRegistroPreco          = $request->get('boRegistroPreco');

        $boIncluir = true;

        $arSolicitacao = Sessao::read('solicitacoes');

        # Validação antes de incluir a solicitação no Mapa.
        if (!is_numeric($inCodEntidadeSolicitacao)) {
            $stErro = "Selecione uma entidade.";
            $boIncluir = false;
        } elseif (!is_numeric($inCodSolicitacao)) {
            $stErro = "Informe o número da solicitação.";
            $boIncluir = false;
        } elseif (count($arSolicitacao) > 0) {
            foreach ($arSolicitacao as $key => $value) {
                $stCod = $value['cod_solicitacao'];
                $stEnt = $value['cod_entidade'];
                if ($inCodSolicitacao == $stCod && $inCodEntidadeSolicitacao == $stEnt) {
                    $boIncluir = false;
                    $stErro = "Esta solicitação já existe no mapa.";
                    break;
                }
            }
        }

        if ($boIncluir) {
            # Função que adiciona a solicitação no Mapa e seus itens.
            $stJs .= addSolicitacao($stExercicioSolicitacao, $inCodEntidadeSolicitacao, $inCodSolicitacao, $inCodTipoLicitacao, true, $boRegistroPreco);
        } else {
            $stJs .= "alertaAviso('".$stErro."','form','erro','".Sessao::getId()."');\n";
        }

        # Monta a tabela com a solicitação informada e os itens vinculados.
        $stJs .= montaListaSolicitacoes ($stTipoCotacao, $stAcao);
        $stJs .= montaListaItens ($rsRecordSet, $stTipoCotacao);
    break;

    case 'delSolicitacao':
        $stJs = delSolicitacao($request->get('inId'), $request->get('stTipoCotacao'));
    break;

    case 'delItem':
        $stJs = delItem( $request->get('inId'), $request->get('stTipo'), $request->get('stTipoCotacao') );
    break;

    case 'alterarItem':
        $stJs = alterarItem($request->get('inId'), $request->get('stTipoCotacao'));
    break;

    case 'salvarDadosItem':
        $stJs .= salvarDadosItem
                 (    $request->get('inId')
                    , $request->get('nuVlUnitario')
                    , $request->get('nuQuantidade')
                    , $request->get('nuVlTotal')
                    , $request->get('nuValorReserva')
                    , $request->get('nuHdnSaldoDotacao')
                    , $request->get('inCodDespesa')
                    , $request->get('stCodClassificacao')
                    , $request->get('inCodLote')
                    , $request->get('obHdnTipoCotacao')
                    , $request->get('boRegistroPreco')
                 );
    break;

    case 'limpaFormulario':
        Sessao::write('solicitacoes' , array());
        Sessao::write('itens'        , array());

        $stJs .= " f.reset();                                               \n";
        $stJs .= " jQuery('#boRegistroPrecoSim').removeAttr('disabled');    \n";
        $stJs .= " jQuery('#boRegistroPrecoNao').removeAttr('disabled');    \n";        
        $stJs .= " jQuery('#spnSolicitacoes').html('&nbsp;');               \n";
        $stJs .= " jQuery('#spnItens').html('&nbsp;');                      \n";
        $stJs .= " jQuery('#Ok').attr('disabled', '');                      \n";
    break;

    case 'anularItem':
        $stJs = montaAnulacaoItem( $request->get('inId'));
    break;

    case 'salvaAnularItem':
        $stJs = anularItem($request->get('inId'), $request->get('flValorAnular'), $request->get('flQuantidadeAnular'), $request->get('hdnValorUnitario'));
    break;

    case 'tipoLicitacao':
        //// isto foi jogado para a sessão pra ter que refazer toda a lista de itens nem ter
        //  que bloquear o select de tipo de licitação apos a primeira inclusão de solicitação na listagem
        Sessao::write( 'inTipoLicitacao' , $request->get('inCodTipoLicitacao'));
    break;

    case 'calculaValorReservaXTotal':
        $inQuantidade = $request->get('nuQuantidade');

        if ($inQuantidade > 0) {
            $inQuantidade = str_replace(',','.',(str_replace('.','',$request->get('nuQuantidade'))));
            $vlTotal      = str_replace(',','.',(str_replace('.','',$request->get('nuVlTotal'))));
            $vlUnitario = ($vlTotal / $inQuantidade);

            $stJs .= "jQuery('#nuVlTotal').val('".number_format($vlTotal, 2, ',', '.' )."');        \n";
            $stJs .= "jQuery('#nuVlUnitario').val('".number_format($vlUnitario, 4, ',', '.' )."');  \n";
            $stJs .= "jQuery('#nuValorReserva').val('".number_format($vlTotal, 2, ',', '.' )."');   \n";
            $stJs .= "jQuery('#stValorReserva').html('".number_format($vlTotal, 2, ',', '.' )."');  \n";
        }

    break;

    case "calculaValorReserva":
        $quantidade = str_replace(',','.',(str_replace('.','',$request->get('nuQuantidade'))));
        $vlUnitario = str_replace(',','.',(str_replace('.','',$request->get('nuVlUnitario'))));

        $valorTotal = $vlUnitario * $quantidade;

        $stJs .= "jQuery('#nuVlTotal').val('".number_format ( $valorTotal, 2, ',', '.' )."');       \n";
        $stJs .= "jQuery('#nuVlUnitario').val('" .number_format ( $vlUnitario, 4, ',', '.' )."');   \n";
        $stJs .= "jQuery('#nuValorReserva').val('".number_format($valorTotal, 2, ',', '.')."');     \n";
        $stJs .= "jQuery('#stValorReserva').html('".number_format ( $valorTotal, 2, ',', '.' )."'); \n";

    break;

    case "anularTodosItens":
        $arItens = Sessao::read('itens');
        $stJs = "";

        foreach ($arItens as $reg) {
            if ($request->get('obHdnCheck') == 'true') {
                $stJs .= anularItem($reg['inId'], number_format($reg['valor_total_mapa'],2,',','.'), number_format($reg['quantidade_mapa'],2,',','.'), $reg['valor_unitario']);
            } else {
                $stJs .= anularItem($reg['inId'], '', '', $reg['valor_unitario']);
            }
        }

    break;
}

# Imprime os comandos JS.
if (!empty($stJs)) { echo $stJs; }

?>
