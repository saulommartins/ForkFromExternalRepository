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
    * @author Programador: Diego Victoria

    * @ignore

    $Id: .php 35877 2008-11-21 21:25:59Z diogo.zarpelon $

    **/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_ALM_MAPEAMENTO.'TAlmoxarifadoConfiguracao.class.php';
include_once CAM_GP_ALM_MAPEAMENTO.'TAlmoxarifadoNaturezaLancamento.class.php';
include_once CAM_GP_ALM_MAPEAMENTO.'TAlmoxarifadoLancamentoMaterial.class.php';
include_once CAM_GP_ALM_MAPEAMENTO.'TAlmoxarifadoSaidaDiversa.class.php';
include_once CAM_GP_ALM_MAPEAMENTO.'TAlmoxarifadoLancamentoPerecivel.class.php';
include_once CAM_GP_ALM_MAPEAMENTO.'TAlmoxarifadoLancamentoRequisicao.class.php';
include_once CAM_GP_ALM_MAPEAMENTO.'TAlmoxarifadoLancamentoMaterialEstorno.class.php';
include_once CAM_GP_ALM_MAPEAMENTO.'TAlmoxarifadoAtributoEstoqueMaterialValor.class.php';
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeConfiguracaoLancamentoContaDespesaItem.class.php";
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeConfiguracaoLancamentoDebito.class.php";
include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoUsuario.class.php";
include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCentroCustoEntidade.class.php";
include_once CAM_GF_CONT_MAPEAMENTO."FContabilidadeAlmoxarifadoLancamento.class.php";
include_once CAM_GP_ALM_NEGOCIO."RAlmoxarifadoCatalogoItem.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "MovimentacaoDiversa";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";
$pgRel  = "OCGera".$stPrograma.".php";

$stCtrl = $_REQUEST['stCtrl'];

$stAcao = $request->get('stAcao');
$stErro     = '';

$inExercicio    = Sessao::getExercicio();
$inCodNatureza  = 9;
$stTipoNatureza = "S";

$inCodLancamento = 0;

//valida se os centros de custo estão configurados para lançamento contábil
$arCentrosCustoNaoConfigurados = array();
foreach (Sessao::read('arItens') as $key => $arLancamentos) {
    //pega o cod conta despesa referente ao lançamento
    $inCodContaDespesa = ($_REQUEST['inCodContaDespesa_'.($key+1).''] ? $_REQUEST['inCodContaDespesa_'.($key+1).''] : $_REQUEST['inCodContaDespesa_'.($key+1).'_hidden']);

    if ($inCodContaDespesa != "") {
        $stFiltroContas = " WHERE configuracao_lancamento_debito.estorno = false
                        AND configuracao_lancamento_debito.tipo = 'almoxarifado'
                        AND configuracao_lancamento_debito.cod_conta_despesa = ".$inCodContaDespesa."
                        AND configuracao_lancamento_debito.exercicio = '".$inExercicio."' ";
        $obTContabilidadeConfiguracaoLancamentoDebito = new TContabilidadeConfiguracaoLancamentoDebito;
        $obErro = $obTContabilidadeConfiguracaoLancamentoDebito->recuperaContasDebitoCredito( $rsContasDebitoCredito, $stFiltroContas );

        if ($rsContasDebitoCredito->getNumLinhas() < 1) {
            include_once CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php";
            $obROrcamentoDespesa = new ROrcamentoDespesa;
            $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setCodConta($inCodContaDespesa);
            $obROrcamentoDespesa->listarCodEstruturalDespesa($rsContaDespesa, "ORDER BY conta_despesa.cod_estrutural");
            $arCodEstrutural[] = $rsContaDespesa->getCampo('cod_estrutural');
        }
    } else {
        $obRAlmoxarifadoCatalogoItem = new RAlmoxarifadoCatalogoItem();
        $obRAlmoxarifadoCatalogoItem->setCodigo($arLancamentos['cod_item']);
        $obRAlmoxarifadoCatalogoItem->consultar();

        $inCodTipoItem = $obRAlmoxarifadoCatalogoItem->obRAlmoxarifadoTipoItem->getCodigo();
        if ($inCodTipoItem == 1 || $inCodTipoItem == 2) {
            sistemaLegado::exibeAviso(urlencode('O Desdobramento para lançamento deve ser informado quando a quantidade for maior do que 0 (Item '.($key+1).').'),"n_incluir","erro");
            exit;
        }
    }
}

if ( !empty($arCodEstrutural) ) {
    SistemaLegado::exibeAviso(urlencode('Os desdobramentos ('.implode(', ', $arCodEstrutural).') não estão configurados para lançamento contábil.'),"n_incluir","erro");
    exit;
}

Sessao::setTrataExcecao( true );
$obErro = new Erro;

if ($_REQUEST['stCGMUsuario']) {
  $obTAdministracaoUsuario = new TAdministracaoUsuario();
  $stFiltro = " WHERE usuario.status = 'A'
                  AND usuario.username = '".$_REQUEST['stCGMUsuario']."' ";
  $obTAdministracaoUsuario->recuperaUsuario($rsUsuario, $stFiltro);
  $rsUsuario->setPrimeiroElemento();
}

$obTAlmoxarifadoNaturezaLancamento = new TAlmoxarifadoNaturezaLancamento;
$obTAlmoxarifadoNaturezaLancamento->setDado('tipo_natureza' , 'S');
$obTAlmoxarifadoNaturezaLancamento->setDado('cod_natureza'  , $inCodNatureza);

# Recupera o num_lancamento considerando as configurações do Almoxarifado.
$obTAlmoxarifadoNaturezaLancamento->recuperaNumNaturezaLancamento($rsNumLancamento);

$inCodLancamento = $rsNumLancamento->getCampo('num_lancamento');

$obTAlmoxarifadoNaturezaLancamento->setDado('num_lancamento' , $inCodLancamento      );
$obTAlmoxarifadoNaturezaLancamento->setDado('numcgm_usuario' , $rsUsuario->getCampo('numcgm'));
$obTAlmoxarifadoNaturezaLancamento->setDado('cgm_almoxarife' , Sessao::read('numCgm'));
$obTAlmoxarifadoNaturezaLancamento->setDado('timestamp'      , date('Y-m-d H:i:s'));
$obTAlmoxarifadoNaturezaLancamento->inclusao();

$obTAlmoxarifadoLancamentoMaterial =  new TAlmoxarifadoLancamentoMaterial;
$obTAlmoxarifadoLancamentoMaterial->obTAlmoxarifadoNaturezaLancamento = & $obTAlmoxarifadoNaturezaLancamento;

$inCount = 1;
$arItens = Sessao::read('arItens');
$stErro = null;

if ( count( $arItens ) > 0 ) {
    foreach ($arItens as $key => $value) {

        $inProx     = 1;
        $boPerecivel= false;
        if ( count($value['lotes'])>0 ) {
            $inProx     = count($value['lotes']);
            $boPerecivel= true;
        }

        $boAtributo = false;
        if ( count($value['valores_atributos'])>0 && !$boPerecivel ) {
            $inProx     = count($value['valores_atributos']);
            $boAtributo = true;
        }

        for ($inCountA=0; $inCountA<$inProx; $inCountA++) {

            if ($boAtributo) {
                $nuQuantidade = $value['valores_atributos'][$inCountA]['quantidade'];
                $nuSaldo      = str_replace(',','.',str_replace('.','',  $value['valores_atributos'][$inCountA]['saldo_atributo']  ));
                $stMsg        = "no atributo ".$value['valores_atributos'][$inCountA]['NomeAtributos'].".";
            } elseif ($boPerecivel) {
                $nuQuantidade = $value['lotes'][$inCountA]['quantidade'];
                $nuSaldo      = str_replace(',','.',str_replace('.','',  $value['lotes'][$inCountA]['saldo']  ));
                $stMsg        = "no lote ".$value['lotes'][$inCountA]['lote'].".";
            } else {
                $nuQuantidade = str_replace(',','.',str_replace('.','',  $_REQUEST['nuQuantidadeLista_'.$inCount]));
                $nuSaldo      = str_replace(',','.',str_replace('.','',  $value['saldo']  ));
                $stMsg        = "no item ".$_REQUEST['HdninCodItem'];
            }

            if ($nuSaldo < $nuQuantidade) {
                $stErro = 'Quantidade '.$nuQuantidade.' deve ser menor ou igual ao Saldo em Estoque '.$nuSaldo." $stMsg.";
                break 2;
            } elseif (!$nuQuantidade || $nuQuantidade <= 0) {
                $stErro = "Quantidade tem que ser maior que zero $stMsg.";
                break 2;
            }

            # Transforma quantidade em negativo para ser inserido na base.
            $nuQuantidade = ($nuQuantidade * -1);

            # Validação do textarea Observação para evitar erros ao inserir na base.
            $stObservacao = (!empty($_REQUEST['stObservacao']) ? $_REQUEST['stObservacao'] : null);
            $stObservacao = str_replace("\r\n", "\n", $stObservacao);

            $obTAlmoxarifadoLancamentoMaterial->setDado( 'cod_almoxarifado' , $arItens[0]['cod_almoxarifado']  );
            $obTAlmoxarifadoLancamentoMaterial->setDado( 'cod_item'         , $value['cod_item']               );
            $obTAlmoxarifadoLancamentoMaterial->setDado( 'cod_marca'        , $value['cod_marca']              );
            $obTAlmoxarifadoLancamentoMaterial->setDado( 'cod_centro'       , $value['cod_centro']             );
            $obTAlmoxarifadoLancamentoMaterial->setDado( 'complemento'      , $stObservacao                    );

            $obTAlmoxarifadoLancamentoMaterial->proximoCod( $inCodLancMat );
            $arItens[$key]['inCodLancamento'] = $inCodLancMat;

            $obTAlmoxarifadoLancamentoMaterial->setDado( 'cod_lancamento' , $inCodLancMat );
            $obTAlmoxarifadoLancamentoMaterial->setDado( 'quantidade'     , $nuQuantidade );

            # DEVIDO A DIFERENÇAS DE DÍZIMA, A FÓRMULA FOI ALTERADA
            # 1) Recupera o saldo do valor unitário truncado (vl_un_truncado) do item; (apurado pelo TRUNC(sum(valor_mercado)/sum(quantidade),2) dos lancamentos (tanto entrada quanto saida)).;
            # 2) Multiplicar pela quantidade do lançamento atual (vl_un_truncado * qtde_atual);
            # 3) Somar o resto: (sum(valor_mercado)/sum(quantidade))-(sum(vl_un_truncado)*sum(quantidade))

            $obTAlmoxarifadoLancamentoMaterial->recuperaSaldoValorUnitarioTruncado($rsItemValorUnitario);
            $vlItemUnitarioTruncado = $rsItemValorUnitario->getCampo('valor_unitario');

            $obTAlmoxarifadoLancamentoMaterial->recuperaRestoValor($rsItemValorUnitario);
            $vlResto = $rsItemValorUnitario->getCampo('resto');

            $vlResto = ($vlResto * -1); //inverte sinal pq é saída.

            $valorMercado = ($vlItemUnitarioTruncado * $nuQuantidade)+$vlResto;

            $obTAlmoxarifadoLancamentoMaterial->setDado( 'valor_mercado'  , $valorMercado );
            $obTAlmoxarifadoLancamentoMaterial->inclusao();

            //lançamento para contabilidade
            $obTAlmoxarifadoCentroCustoEntidade = new TAlmoxarifadoCentroCustoEntidade;
            $obErro = $obTAlmoxarifadoCentroCustoEntidade->recuperaTodos($rsCentroCustoEntidade, " WHERE cod_centro = ".$value['cod_centro']);

            if ($inCodContaDespesa) {
                $inCodContaDespesa = ($_REQUEST['inCodContaDespesa_'.($key+1).''] ? $_REQUEST['inCodContaDespesa_'.($key+1).''] : $_REQUEST['inCodContaDespesa_'.($key+1).'_hidden']);
                $obTContabilidadeConfiguracaoLancamentoContaDespesaItem = new TContabilidadeConfiguracaoLancamentoContaDespesaItem;
                $obTContabilidadeConfiguracaoLancamentoContaDespesaItem->setDado('cod_item', $value['cod_item']);
                $obTContabilidadeConfiguracaoLancamentoContaDespesaItem->setDado('exercicio', Sessao::getExercicio());
                $obTContabilidadeConfiguracaoLancamentoContaDespesaItem->setDado('cod_conta_despesa', $inCodContaDespesa);
                $obTContabilidadeConfiguracaoLancamentoContaDespesaItem->salvar();

                if ( !$obErro->ocorreu() ) {
                    $obFContabilidadeAlmoxarifadoLancamento = new FContabilidadeAlmoxarifadoLancamento;
                    $obFContabilidadeAlmoxarifadoLancamento->setDado( "exercicio"         , Sessao::getExercicio()                                     );
                    $obFContabilidadeAlmoxarifadoLancamento->setDado( "cod_conta_despesa" , $inCodContaDespesa                                         );
                    $obFContabilidadeAlmoxarifadoLancamento->setDado( "valor"             , $valorMercado * -1                                         );
                    $obFContabilidadeAlmoxarifadoLancamento->setDado( "complemento"       , 'Saída Diversa do item '.$value['cod_item'].', Saída '.$inCodLancamento.'/'.Sessao::getExercicio() );
                    $obFContabilidadeAlmoxarifadoLancamento->setDado( "tipo_lote"         , 'X'                                                        );
                    $obFContabilidadeAlmoxarifadoLancamento->setDado( "nom_lote"          , 'Saída Diversa do item '.$value['cod_item'].', Saída '.$inCodLancamento.'/'.Sessao::getExercicio() );
                    $obFContabilidadeAlmoxarifadoLancamento->setDado( "dt_lote"           , date('d/m/Y')                                              );
                    $obFContabilidadeAlmoxarifadoLancamento->setDado( "cod_entidade"      , $rsCentroCustoEntidade->getCampo('cod_entidade')           );
                    $obErro = $obFContabilidadeAlmoxarifadoLancamento->executaFuncao( $rsRecordSet );
                }
            }

            $obTAlmoxarifadoSaidaDiversa = new TAlmoxarifadoSaidaDiversa;
            $obTAlmoxarifadoSaidaDiversa->setDado('cod_lancamento'   , $inCodLancMat                   );
            $obTAlmoxarifadoSaidaDiversa->setDado('cod_item'         , $value['cod_item']              );
            $obTAlmoxarifadoSaidaDiversa->setDado('cod_marca'        , $value['cod_marca']             );
            $obTAlmoxarifadoSaidaDiversa->setDado('cod_centro'       , $value['cod_centro']            );
            $obTAlmoxarifadoSaidaDiversa->setDado('cod_almoxarifado' , $arItens[0]['cod_almoxarifado'] );
            $obTAlmoxarifadoSaidaDiversa->setDado('cgm_solicitante'  , $_REQUEST['inCGMSolicitante']   );
            $obTAlmoxarifadoSaidaDiversa->setDado('observacao'       , $stObservacao                   );
            $obTAlmoxarifadoSaidaDiversa->inclusao();

            if ($boPerecivel) {
                $obTAlmoxarifadoLancamentoPerecivel = new TAlmoxarifadoLancamentoPerecivel;
                $obTAlmoxarifadoLancamentoPerecivel->obTAlmoxarifadoLancamentoMaterial = &$obTAlmoxarifadoLancamentoMaterial;
                $obTAlmoxarifadoLancamentoPerecivel->setDado ( 'cod_item'        , $value['cod_item']          );
                $obTAlmoxarifadoLancamentoPerecivel->setDado ( 'cod_marca'       , $value['cod_marca']         );
                $obTAlmoxarifadoLancamentoPerecivel->setDado ( 'cod_centro'      , $value['cod_centro']        );
                $obTAlmoxarifadoLancamentoPerecivel->setDado ( 'cod_almoxarifado', $arItens[0]['cod_almoxarifado'] );
                $obTAlmoxarifadoLancamentoPerecivel->setDado ( 'lote'            , $value['lotes'][$inCountA]['lote']    );
                $obTAlmoxarifadoLancamentoPerecivel->inclusao();
            }

            if ($boAtributo) {
                for ($inCountV=0; $inCountV<count($value['valores_atributos'][$inCountA]['atributo']); $inCountV++) {

                    $obTAlmoxarifadoAtributoEstoqueMaterialValor = new TAlmoxarifadoAtributoEstoqueMaterialValor();
                    $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_modulo',         29);
                    $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_item',           $value['cod_item'] );
                    $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_marca',          $value['cod_marca'] );
                    $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_centro',         $value['cod_centro'] );
                    $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_almoxarifado',   $arItens[0]['cod_almoxarifado'] );
                    $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_lancamento',     $inCodLancMat            );
                    $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_cadastro',       2 );
                    $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_atributo',       $value['valores_atributos'][$inCountA]['atributo'][$inCountV]['cod_atributo'] );
                    $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('valor',              $value['valores_atributos'][$inCountA]['atributo'][$inCountV]['valor'] );
                    $obTAlmoxarifadoAtributoEstoqueMaterialValor->inclusao();
                }
            }
            $inCount++;
        }
    }

    //CONTROLA O O LANÇAMENTO DAS MANUTENÇÕES CUJO O ITEM PERTENCE AO FROTA ( item.frota )
    include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoRequisicao.class.php";
    $obTAlmoxarifadoRequisicao = new TAlmoxarifadoRequisicao;
    $stFiltro  = " AND req.exercicio        = '".Sessao::getExercicio()."'         \n";
    $stFiltro .= " AND req.cod_almoxarifado =  ".$_REQUEST['inCodAlmoxarifado']."  \n";
    $obTAlmoxarifadoRequisicao->recuperaRequisicaoAlteracao($rsRecordSet);
    $dtRequisicao = $rsRecordSet->getCampo('dt_requisicao');

    include_once CAM_GP_FRO_MAPEAMENTO."TFrotaManutencao.class.php";
    $obTFrotaManutencao = new TFrotaManutencao;
    $obTFrotaManutencao->proximoCod($inCodManutencao);
    $arItensManutencao = array();
    $arTEMP = array();
    foreach ($arItens as $key => $arLancamentos) {
        if ($arLancamentos['boFrota'] == true) {
            $boInclui = true;
            foreach ($arItensManutencao as $arTEMP) {
                if ($arLancamentos['inCodVeiculo'] == $arTEMP['inCodVeiculo']) {
                    $boInclui = false;
                }
            }
            if ($boInclui) {
                $obTFrotaManutencao = new TFrotaManutencao;
                $obTFrotaManutencao->setDado    ( 'exercicio'      , Sessao::getExercicio()            );
                $obTFrotaManutencao->setDado    ( 'cod_manutencao' , $inCodManutencao                  );
                $obTFrotaManutencao->setDado    ( 'cod_veiculo'    , $arLancamentos['inCodVeiculo']    );
                $obTFrotaManutencao->setDado    ( 'dt_manutencao'  , date('d/m/Y')                     );
                $obTFrotaManutencao->setDado    ( 'km'             , $arLancamentos['nmKm']            );
                $obTFrotaManutencao->setDado    ( 'observacao'     , $arLancamentos['complemento']     );
                $obTFrotaManutencao->inclusao();

                $arTEMP['stExercicio']     = $arLancamentos['stExercicio'];
                $arTEMP['inCodVeiculo']    = $arLancamentos['inCodVeiculo'];
                $arTEMP['inCodManutencao'] = $inCodManutencao;
                $arItensManutencao[] = $arTEMP;
                $inCodManutencao++;
            }
        }
    }

    $arMsgLancamentos = array();

    include_once CAM_GP_FRO_MAPEAMENTO."TFrotaManutencaoItem.class.php";
    include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoLancamentoManutencaoFrota.class.php";

    $obTFrotaManutencaoItem = new TFrotaManutencaoItem;
    $obTAlmoxarifadoLancamentoManutencaoFrota = new TAlmoxarifadoLancamentoManutencaoFrota;
    foreach ($arItens as $key => $arLancamentos) {
        $obTAlmoxarifadoLancamentoMaterial->setDado( 'cod_item'            , $arLancamentos['cod_item']          );
        $obTAlmoxarifadoLancamentoMaterial->setDado( 'cod_marca'           , $arLancamentos['cod_marca']         );
        $obTAlmoxarifadoLancamentoMaterial->setDado( 'cod_almoxarifado'    , $arLancamentos['cod_almoxarifado']  );
        $obTAlmoxarifadoLancamentoMaterial->setDado( 'cod_centro'          , $arLancamentos['cod_centro']        );
        $obTAlmoxarifadoLancamentoMaterial->setDado( 'complemento'         , $arLancamentos['complemento']       );
        if (count($arItensManutencao) > 0) {
            foreach ($arItensManutencao as $arTEMP) {
                if ($arLancamentos['boFrota'] == true) {
                    if ($arLancamentos['inCodVeiculo'] == $arTEMP['inCodVeiculo']) {
                        $obTAlmoxarifadoLancamentoMaterial->recuperaSaldoValorUnitarioTruncado($rsRecordSet);

                        $obTAlmoxarifadoLancamentoMaterial->recuperaRestoValor($rsItemValorUnitario);
                        $vlResto = $rsItemValorUnitario->getCampo('resto');
                        $vlResto = ($vlResto * -1); //inverte sinal pq é saída.

                        $nuVlTotal = ($rsRecordSet->getCampo('valor_unitario') * $arLancamentos['quantidade'])+$vlResto;

                        $obTFrotaManutencaoItem->setDado( 'cod_manutencao' , $arTEMP['inCodManutencao']               );
                        $obTFrotaManutencaoItem->setDado( 'cod_item'       , $arLancamentos['cod_item']               );
                        $obTFrotaManutencaoItem->setDado( 'exercicio'      , Sessao::getExercicio()                   );
                        $obTFrotaManutencaoItem->setDado( 'quantidade'     , $arLancamentos['quantidade']             );
                        $obTFrotaManutencaoItem->setDado( 'valor'          , $nuVlTotal                               );
                        $obTFrotaManutencaoItem->inclusao();

                        $obTAlmoxarifadoLancamentoManutencaoFrota->setDado( 'cod_lancamento'   , $arLancamentos['inCodLancamento']   );
                        $obTAlmoxarifadoLancamentoManutencaoFrota->setDado( 'cod_item'         , $arLancamentos['cod_item']          );
                        $obTAlmoxarifadoLancamentoManutencaoFrota->setDado( 'cod_marca'        , $arLancamentos['cod_marca']         );
                        $obTAlmoxarifadoLancamentoManutencaoFrota->setDado( 'cod_almoxarifado' , $arLancamentos['cod_almoxarifado']  );
                        $obTAlmoxarifadoLancamentoManutencaoFrota->setDado( 'cod_centro'       , $arLancamentos['cod_centro']        );
                        $obTAlmoxarifadoLancamentoManutencaoFrota->setDado( 'cod_manutencao'   , $arTEMP['inCodManutencao']          );
                        $obTAlmoxarifadoLancamentoManutencaoFrota->setDado( 'exercicio'        , Sessao::getExercicio()              );
                        $arMsgLancamentos[] = $arTEMP['inCodManutencao'];
                        $obTAlmoxarifadoLancamentoManutencaoFrota->inclusao();
                    }
                }
            }
        }
    }

    //FIM DOS LANÇAMENTOS DAS MANUTENÇÕES
} else {
    $stErro = "Deve existir pelo menos um item na lista.";
}

Sessao::encerraExcecao();

if ($stErro != null) {
    SistemaLegado::exibeAviso(urlencode($stErro),"n_incluir","erro");
} else {

    if (count($arMsgLancamentos) > 0) {
        $inCount = count($arMsgLancamentos);
        if ($inCount  > 1) {
            $inCount = $inCount - 1;
            $stMsg = $arMsgLancamentos[0]."/".Sessao::getExercicio()." até ".$arMsgLancamentos[$inCount]."/".Sessao::getExercicio();
        } else {
            $stMsg = $arMsgLancamentos[0]."/".Sessao::getExercicio();
        }
        SistemaLegado::alertaAviso($pgRel.'?'.Sessao::getId()."&stAcao=".$stAcao."&inNumLancamento=".$inCodLancamento, "Saída Diversa: ".$inCodLancamento."/".Sessao::getExercicio().") (Manutenção do frota: ".$stMsg,"incluir","aviso", Sessao::getId(), "../");
    } else {
        SistemaLegado::alertaAviso($pgRel.'?'.Sessao::getId()."&stAcao=".$stAcao."&inNumLancamento=".$inCodLancamento, "Saída Diversa: ".$inCodLancamento."/".Sessao::getExercicio()." ","incluir","aviso", Sessao::getId(), "../");
    }
}

?>
