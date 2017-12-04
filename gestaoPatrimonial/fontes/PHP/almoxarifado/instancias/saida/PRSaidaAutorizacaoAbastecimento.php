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
  * Página de processamento para Movimentação
  * Data de criação : 02/03/2006

  * @author Analista: Diego Barbosa Victoria
  * @author Programador: Diego Barbosa Victoria

  * @ignore

  Caso de uso: uc-03.03.11

  $Id: PRMovimentacaoRequisicao.php 38104 2009-02-12 18:56:17Z diogo.zarpelon $

  **/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_FRO_MAPEAMENTO."TFrotaManutencao.class.php";
include_once CAM_GP_FRO_MAPEAMENTO."TFrotaManutencaoItem.class.php";
include_once CAM_GP_FRO_MAPEAMENTO."TFrotaEfetivacao.class.php";
include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCatalogoItemMarca.class.php";
include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoNaturezaLancamento.class.php";
include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoEstoqueMaterial.class.php";
include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoLancamentoMaterial.class.php";
include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoLancamentoAutorizacao.class.php";
include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoUsuario.class.php";
include_once CAM_GF_CONT_MAPEAMENTO."FContabilidadeAlmoxarifadoLancamento.class.php";
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeConfiguracaoLancamentoContaDespesaItem.class.php";
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeConfiguracaoLancamentoDebito.class.php";
include_once CAM_GP_ALM_NEGOCIO."RAlmoxarifadoCatalogoItem.class.php";

function br2us($flNumber, $inDecimal=4)
{
    $flNumber = str_replace( '.', '', $flNumber);

    return number_format( $flNumber, $inDecimal, '.', '');
}

//Define o nome dos arquivos PHP
$stPrograma = "SaidaAutorizacaoAbastecimento";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";
$pgRel      = "OCGera".$stPrograma.".php";

$stAcao             = $request->get('stAcao');
$nuQuilometragem    = br2us($request->get('nuQuilometragem'));
$inCodMarca         = $request->get('inCodMarca');
$inCodAlmoxarifado  = $request->get('inCodAlmoxarifado');
$inCodCentroCusto   = $request->get('inCodCentroCusto');
$inCodContaDespesa  = $request->get('inCodContaDespesa') ? $request->get('inCodContaDespesa') : $request->get('hdnDesdobramento');
$nuQtdeAutorizada   = $request->get('nuQtdeAutorizada');

$stObservacao = $request->get('stObservacao');
$stObservacao = str_replace("\r\n", "\n", $stObservacao);

$rsLancamento           = Sessao::read('saida');

$stExercicio            = Sessao::getExercicio();
$stExercicioAutorizacao = $rsLancamento->getCampo('exercicio');
$inCodAutorizacao       = $rsLancamento->getCampo('cod_autorizacao');
$inCodVeiculo           = $rsLancamento->getCampo('cod_veiculo');
$inCodModelo            = $rsLancamento->getCampo('cod_modelo');
$stNomModelo            = $rsLancamento->getCampo('nom_modelo');
$flKlmSaida             = $rsLancamento->getCampo('kil_saida');
$flKlmInicial           = $rsLancamento->getCampo('km_inicial');
$nuQuilometragemAtual   = $flKlmSaida ? $flKlmSaida : $flKlmInicial;
$inCGMRespAutorizacao   = $rsLancamento->getCampo('cgm_resp_autorizacao');

$inCodItem              = $rsLancamento->getCampo('cod_item');
$stDescricaoItem        = $rsLancamento->getCampo('descricao_resumida');
$inCodUnidade           = $rsLancamento->getCampo('cod_unidade');
$stNomUnidade           = $rsLancamento->getCampo('nom_unidade');

Sessao::consultarDadosSessao();
$inCGMAlmoxarife = Sessao::read('numCgm');
$stNomAlmoxarife = Sessao::read('nomCgm');

$vlQuantidade = str_replace('.','', $request->get('nuQuantidade'));
$vlQuantidade = str_replace(',','.', $vlQuantidade);

$nuSaldo = str_replace('.','', $request->get('nuHdnSaldoEstoque'));
$nuSaldo = str_replace(',','.', $nuSaldo);

$nuQtdeAutorizada = str_replace('.','', $nuQtdeAutorizada);
$nuQtdeAutorizada = str_replace(',','.', $nuQtdeAutorizada);

# Busca o valor unitário para calcular o valor de mercado.
$obTAlmoxarifadoLancamentoMaterial = new TAlmoxarifadoLancamentoMaterial;

$obTAlmoxarifadoLancamentoMaterial->setDado('cod_item', $inCodItem );
$obTAlmoxarifadoLancamentoMaterial->recuperaSaldoValorUnitarioTruncado($rsItemValorUnitario);

$obTAlmoxarifadoLancamentoMaterial->recuperaRestoValor($rsItemValorUnitarioResto);
$vlResto = $rsItemValorUnitarioResto->getCampo('resto');

$nuValor = $rsItemValorUnitario->getCampo('valor_unitario');
$inValor = ($nuValor * $vlQuantidade)+$vlResto;

$inValor = ($inValor * -1); //inverte sinal pq é saída.

if ($inCodContaDespesa != "") {
    $stFiltroContas = " WHERE configuracao_lancamento_debito.estorno = false
                    AND configuracao_lancamento_debito.tipo = 'almoxarifado'
                    AND configuracao_lancamento_debito.cod_conta_despesa = ".$inCodContaDespesa." ";
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
    $obRAlmoxarifadoCatalogoItem->setCodigo($inCodItem);
    $obRAlmoxarifadoCatalogoItem->consultar();

    $inCodTipoItem = $obRAlmoxarifadoCatalogoItem->obRAlmoxarifadoTipoItem->getCodigo();
    if ($inCodTipoItem == 1 || $inCodTipoItem == 2) {
        $stErro = 'O Desdobramento para lançamento contábil deve ser informado quando a quantidade for maior do que 0.';
    }
}

if (!empty($arCodEstrutural)) {
    $stErro = 'Os desdobramentos ('.implode(', ', $arCodEstrutural).') não estão configurados para lançamento contábil.';
}

# Validações necessárias.
if (empty($vlQuantidade))
    $stErro = "Informe a quantidade de saída";

if ($nuQtdeAutorizada > 0) {
    if ($vlQuantidade != $nuQtdeAutorizada)
        $stErro = "Quantidade deve ser igual a quantidade autorizada para o abastecimento";
}

if ($vlQuantidade > $nuSaldo)
    $stErro = "Quantidade deve ser igual ou menor ao saldo disponível";

if ($nuQuilometragem < $nuQuilometragemAtual) {
    $stErro = "Quilometragem deve ser maior que $nuQuilometragemAtual";
} elseif ($stErro == '') {

    Sessao::setTrataExcecao(true);

    $obTFrotaManutencao = new TFrotaManutencao();
    $obTFrotaManutencao->setDado('exercicio'        , $stExercicio );
    $obTFrotaManutencao->proximoCod($inCodManutencao);
    $obTFrotaManutencao->setDado('cod_manutencao'   ,  $inCodManutencao );
    $obTFrotaManutencao->setDado('km'               ,  $nuQuilometragem );
    $obTFrotaManutencao->setDado('cod_veiculo'      ,  $inCodVeiculo );
    $obTFrotaManutencao->setDado('dt_manutencao'    ,  date("d/m/Y") );
    $obTFrotaManutencao->setDado('observacao'       ,  $stObservacao );
    $obTFrotaManutencao->inclusao();

    $inQuantidadeSaida = number_format(($vlQuantidade * -1),4,'.',',');

    $obTFrotaManutencaoItem = new TFrotaManutencaoItem();
    $obTFrotaManutencaoItem->setDado('exercicio'        , $obTFrotaManutencao->getDado('exercicio') );
    $obTFrotaManutencaoItem->setDado('cod_manutencao'   , $obTFrotaManutencao->getDado('cod_manutencao') );
    $obTFrotaManutencaoItem->setDado('cod_item'         , $inCodItem );
    $obTFrotaManutencaoItem->setDado('quantidade'       , abs($inQuantidadeSaida) );
    $obTFrotaManutencaoItem->setDado('valor'            , abs($inValor) );
    $obTFrotaManutencaoItem->inclusao();

    $obTFrotaEfetivacao = new TFrotaEfetivacao();
    $obTFrotaEfetivacao->setDado('exercicio_autorizacao', $stExercicioAutorizacao );
    $obTFrotaEfetivacao->setDado('cod_autorizacao'      , $inCodAutorizacao );
    $obTFrotaEfetivacao->setDado('exercicio_manutencao' , $obTFrotaManutencao->getDado('exercicio') );
    $obTFrotaEfetivacao->setDado('cod_manutencao'       , $obTFrotaManutencao->getDado('cod_manutencao') );
    $obTFrotaEfetivacao->inclusao();

    if ($_REQUEST['stCGMUsuario']) {
      $obTAdministracaoUsuario = new TAdministracaoUsuario();
      $stFiltro = " WHERE usuario.status = 'A'
                      AND usuario.username = '".$_REQUEST['stCGMUsuario']."' ";
      $obTAdministracaoUsuario->recuperaUsuario($rsUsuario, $stFiltro);
      $rsUsuario->setPrimeiroElemento();
    }

    $inCodLancamento = 0;
    $obTAlmoxarifadoNaturezaLancamento = new TAlmoxarifadoNaturezaLancamento;
    $obTAlmoxarifadoNaturezaLancamento->setDado('tipo_natureza', 'S' );
    $obTAlmoxarifadoNaturezaLancamento->setDado('cod_natureza' , 12 );
    $obTAlmoxarifadoNaturezaLancamento->setDado('exercicio_lancamento', $obTFrotaManutencao->getDado('exercicio')  );
    $obTAlmoxarifadoNaturezaLancamento->proximoCod( $inCodLancamento );
    $obTAlmoxarifadoNaturezaLancamento->setDado('num_lancamento' , $inCodLancamento       );
    $obTAlmoxarifadoNaturezaLancamento->setDado('cgm_almoxarife' , Sessao::read('numCgm') );
    $obTAlmoxarifadoNaturezaLancamento->setDado('numcgm_usuario' , $rsUsuario->getCampo('numcgm') );
    $obTAlmoxarifadoNaturezaLancamento->setDado('timestamp'      , date('Y-m-d H:i:s'));
    $obTAlmoxarifadoNaturezaLancamento->inclusao();

    $obTAlmoxarifadoCatalogoItemMarca = new TAlmoxarifadoCatalogoItemMarca;
    $obTAlmoxarifadoCatalogoItemMarca->setDado('cod_item'  , $inCodItem );
    $obTAlmoxarifadoCatalogoItemMarca->setDado('cod_marca' , $inCodMarca );
    $obTAlmoxarifadoCatalogoItemMarca->recuperaPorChave($rsItemMarca);

    if ($rsItemMarca->eof()) {
        $obTAlmoxarifadoCatalogoItemMarca->inclusao();
    }

    $obTAlmoxarifadoEstoqueMaterial = new TAlmoxarifadoEstoqueMaterial;
    $obTAlmoxarifadoEstoqueMaterial->setDado('cod_item'         , $inCodItem );
    $obTAlmoxarifadoEstoqueMaterial->setDado('cod_marca'        , $inCodMarca );
    $obTAlmoxarifadoEstoqueMaterial->setDado('cod_almoxarifado' , $inCodAlmoxarifado );
    $obTAlmoxarifadoEstoqueMaterial->setDado('cod_centro'       , $inCodCentroCusto );
    $obTAlmoxarifadoEstoqueMaterial->recuperaPorChave($rsEstoqueMaterial);

    if ($rsEstoqueMaterial->eof()) {
        $obTAlmoxarifadoEstoqueMaterial->inclusao();
    }

    $obTAlmoxarifadoLancamentoMaterial =  new TAlmoxarifadoLancamentoMaterial;
    $obTAlmoxarifadoLancamentoMaterial->obTAlmoxarifadoNaturezaLancamento = & $obTAlmoxarifadoNaturezaLancamento;
    $obTAlmoxarifadoLancamentoMaterial->setDado( 'cod_item'         , $inCodItem );
    $obTAlmoxarifadoLancamentoMaterial->setDado( 'cod_marca'        , $inCodMarca );
    $obTAlmoxarifadoLancamentoMaterial->setDado( 'cod_almoxarifado' , $inCodAlmoxarifado );
    $obTAlmoxarifadoLancamentoMaterial->setDado( 'cod_centro'       , $inCodCentroCusto );
    $obTAlmoxarifadoLancamentoMaterial->setDado( 'complemento'      , $stObservacao );
    $obTAlmoxarifadoLancamentoMaterial->proximoCod( $inCodLancMat );
    $obTAlmoxarifadoLancamentoMaterial->setDado( 'cod_lancamento' , $inCodLancMat );
    $obTAlmoxarifadoLancamentoMaterial->setDado( 'quantidade', $inQuantidadeSaida );
    $obTAlmoxarifadoLancamentoMaterial->setDado( 'valor_mercado', $inValor );
    $obTAlmoxarifadoLancamentoMaterial->inclusao();

    $obTAlmoxarifadoLancamentoAutorizacao =  new TAlmoxarifadoLancamentoAutorizacao;
    $obTAlmoxarifadoLancamentoAutorizacao->setDado( 'cod_item'         , $inCodItem );
    $obTAlmoxarifadoLancamentoAutorizacao->setDado( 'cod_marca'        , $inCodMarca );
    $obTAlmoxarifadoLancamentoAutorizacao->setDado( 'cod_almoxarifado' , $inCodAlmoxarifado );
    $obTAlmoxarifadoLancamentoAutorizacao->setDado( 'cod_centro'       , $inCodCentroCusto );
    $obTAlmoxarifadoLancamentoAutorizacao->setDado( 'cod_lancamento'   , $inCodLancMat );
    $obTAlmoxarifadoLancamentoAutorizacao->setDado( 'exercicio'        , $stExercicioAutorizacao );
    $obTAlmoxarifadoLancamentoAutorizacao->setDado( 'cod_autorizacao'  , $inCodAutorizacao );
    $obTAlmoxarifadoLancamentoAutorizacao->inclusao();

    if ($inCodContaDespesa) {
        //lançamento para contabilidade
        include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCentroCustoEntidade.class.php" );
        $obTAlmoxarifadoCentroCustoEntidade = new TAlmoxarifadoCentroCustoEntidade;
        $obErro = $obTAlmoxarifadoCentroCustoEntidade->recuperaTodos($rsCentroCustoEntidade, " WHERE cod_centro = ".$inCodCentroCusto);

        $obTContabilidadeConfiguracaoLancamentoContaDespesaItem = new TContabilidadeConfiguracaoLancamentoContaDespesaItem;
        $obTContabilidadeConfiguracaoLancamentoContaDespesaItem->setDado('cod_item', $inCodItem);
        $obTContabilidadeConfiguracaoLancamentoContaDespesaItem->setDado('exercicio', Sessao::getExercicio());
        $obTContabilidadeConfiguracaoLancamentoContaDespesaItem->setDado('cod_conta_despesa', $inCodContaDespesa);
        $obTContabilidadeConfiguracaoLancamentoContaDespesaItem->salvar();

        if ( !$obErro->ocorreu() ) {
            $obFContabilidadeAlmoxarifadoLancamento = new FContabilidadeAlmoxarifadoLancamento;
            $obFContabilidadeAlmoxarifadoLancamento->setDado( "exercicio"         , Sessao::getExercicio()                                     );
            $obFContabilidadeAlmoxarifadoLancamento->setDado( "cod_conta_despesa" , $inCodContaDespesa                                         );
            $obFContabilidadeAlmoxarifadoLancamento->setDado( "valor"             , number_format(($inValor * -1),2,'.','')                    );
            $obFContabilidadeAlmoxarifadoLancamento->setDado( "complemento"       , 'Saída por Autorização de Abastecimento do item '.$inCodItem.', Autorização '.$inCodAutorizacao );
            $obFContabilidadeAlmoxarifadoLancamento->setDado( "tipo_lote"         , 'X'                                                        );
            $obFContabilidadeAlmoxarifadoLancamento->setDado( "nom_lote"          , 'Saída por Autorização de Abastecimento do item '.$inCodItem.', Autorização '.$inCodAutorizacao );
            $obFContabilidadeAlmoxarifadoLancamento->setDado( "dt_lote"           , date('d/m/Y')                                              );
            $obFContabilidadeAlmoxarifadoLancamento->setDado( "cod_entidade"      , $rsCentroCustoEntidade->getCampo('cod_entidade')           );
            $obErro = $obFContabilidadeAlmoxarifadoLancamento->executaFuncao( $rsRecordSet );
        }
    }

    Sessao::encerraExcecao();
}

if (!empty($stErro)) {
    SistemaLegado::exibeAviso($stErro,'form','erro',Sessao::getId() );
} else {
    SistemaLegado::alertaAviso($pgRel.'?'.Sessao::getId()."&stAcao=".$_REQUEST['stAcao']."&inNumLancamento=".$inCodLancamento."&stExercicio=".$stExercicio, "Saída por Autorização de Abastecimento concluído com sucesso! (Autorização:".$inCodAutorizacao."/".$stExercicioAutorizacao.") ", $_REQUEST['stAcao'],"aviso", Sessao::getId(), "../");
}

?>
