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
    * Página de processamento
    * Data de criação : 02/03/2006

    * @author Analista: Diego Victoria
    * @author Programador: Diego Victoria

    * @ignore

    Caso de uso: uc-03.03.11

    $Id: PRMovimentacaoRequisicao.php 34796 2008-10-23 11:49:11Z luiz $

    **/

//
// Dica:
// cod_lancamento é o lancamento original (lancamento a ser estornado).
// cod_lancamento_estorno é o novo lancamento.
//
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoConfiguracao.class.php";
include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoNaturezaLancamento.class.php";
include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoLancamentoMaterial.class.php";
include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoLancamentoPerecivel.class.php";
include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoLancamentoRequisicao.class.php";
include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoLancamentoMaterialEstorno.class.php";
include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoAtributoEstoqueMaterialValor.class.php";

include_once CAM_GP_COM_MAPEAMENTO."TComprasNotaFiscalFornecedor.class.php";

function br2us($flNumber, $inDecimal=4)
{
    $flNumber = str_replace( '.', '', $flNumber);

    return number_format( $flNumber, $inDecimal, '.', '');
}

//Define o nome dos arquivos PHP
$stPrograma = "EstornoEntrada";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";
$pgRel  = "OCGera".$stPrograma.".php";

$stAcao     = $_REQUEST['stAcao'];
$stErro     = '';
$arSessao   = Sessao::read('sessao');

Sessao::setTrataExcecao( true );

$obTConfiguracao = new TAlmoxarifadoConfiguracao();
$obTConfiguracao->setDado('parametro','numeracao_lancamento_estoque');
$obTConfiguracao->recuperaPorChave($rsNumLanc);
$stNumeracao = ( trim($rsNumLanc->getCampo('valor'))=="" ? 'N' : $rsNumLanc->getCampo('valor') );

$inNumLancamento = 0;

$obTAlmoxarifadoNaturezaLancamento = new TAlmoxarifadoNaturezaLancamento;
$obTAlmoxarifadoNaturezaLancamento->setDado ( 'tipo_natureza', 'S' );
$obTAlmoxarifadoNaturezaLancamento->setDado ( 'exercicio_lancamento', $arSessao['selecionado']['stExercicioLancamento']  );

# No mínimo estranho.
#$obTAlmoxarifadoNaturezaLancamento->setDado ( 'cod_nature', $arSessao['selecionado']['stExercicioLancamento'] );

# Seta o cod_natureza = 10 (Saída por Estorno de Entrada).
$obTAlmoxarifadoNaturezaLancamento->setDado ( 'cod_natureza' , 10 );

$obTAlmoxarifadoNaturezaLancamento->proximoCod( $inNumLancamento );

$obTAlmoxarifadoNaturezaLancamento->setDado ( 'num_lancamento' , $inNumLancamento       );
$obTAlmoxarifadoNaturezaLancamento->setDado ( 'cgm_almoxarife' , Sessao::read('numCgm') );
$obTAlmoxarifadoNaturezaLancamento->setDado ( 'numcgm_usuario' , Sessao::read('numCgm') );
$obTAlmoxarifadoNaturezaLancamento->inclusao();

$obTAlmoxarifadoLancamentoMaterial =  new TAlmoxarifadoLancamentoMaterial;
$obTAlmoxarifadoLancamentoMaterial->obTAlmoxarifadoNaturezaLancamento = & $obTAlmoxarifadoNaturezaLancamento;

for ($inCount=0; $inCount<count( $arSessao['itensEstorno']['item'] ); $inCount++) {
    $boPerecivel= false;
    $inProx     = 1;
    $arPereciveis = $arSessao['itensEstorno']['item'][$inCount]['pereciveis'];
    if ( count($arPereciveis)>0 ) {
        $boPerecivel= true;
        $inProx     = count($arPereciveis);
    }

    for ($inCountP=0; $inCountP<$inProx; $inCountP++) {

        if ($boPerecivel) {
            $nuValor = $arSessao['itensEstorno']['item'][$inCount]['pereciveis'][$inCountP]['quantidade_informada'];
        } else {
            $nuValor = str_replace('.','',$arSessao['itensEstorno']['item'][$inCount]['quantidade'] );
            $nuValor = str_replace(',','.', $nuValor );
        }

        $obTAlmoxarifadoLancamentoMaterial->setDado( 'cod_item'         , $arSessao['itensEstorno']['item'][$inCount]['cod_item']          );
        $obTAlmoxarifadoLancamentoMaterial->setDado( 'cod_marca'        , $arSessao['itensEstorno']['item'][$inCount]['cod_marca']         );
        $obTAlmoxarifadoLancamentoMaterial->setDado( 'cod_almoxarifado' , $arSessao['selecionado']['inCodAlmoxarifado'] );
        $obTAlmoxarifadoLancamentoMaterial->setDado( 'cod_centro'       , $arSessao['itensEstorno']['item'][$inCount]['cod_centro']        );
        $obTAlmoxarifadoLancamentoMaterial->setDado( 'complemento'      , $arSessao['itensEstorno']['item'][$inCount]['justificativa']       );
        $obTAlmoxarifadoLancamentoMaterial->proximoCod( $inCodLancMat );
        $obTAlmoxarifadoLancamentoMaterial->setDado( 'cod_lancamento' , $inCodLancMat           );
        $obTAlmoxarifadoLancamentoMaterial->setDado( 'quantidade', ( $nuValor * -1 ) );

        # Passa o valor de mercado para negativo.
        $vlTotal = $arSessao['itensEstorno']['item'][$inCount]['valor_unitario'] * ($nuValor * -1);

        # Grava o valor de mercado da requisição que foi solicitada.
        $obTAlmoxarifadoLancamentoMaterial->setDado( 'valor_mercado', $vlTotal );

        $obTAlmoxarifadoLancamentoMaterial->inclusao();

        if ($boPerecivel) {
            $inCodLancamentoOriginal = $arSessao['itensEstorno']['item'][$inCount]['pereciveis'][$inCountP]['cod_lancamento'];

            $obTAlmoxarifadoLancamentoPerecivel = new TAlmoxarifadoLancamentoPerecivel;
            $obTAlmoxarifadoLancamentoPerecivel->setDado( 'cod_almoxarifado'    , $arSessao['selecionado']['inCodAlmoxarifado'] );
            $obTAlmoxarifadoLancamentoPerecivel->setDado( 'cod_item'            , $arSessao['itensEstorno']['item'][$inCount]['cod_item']          );
            $obTAlmoxarifadoLancamentoPerecivel->setDado( 'cod_marca'           , $arSessao['itensEstorno']['item'][$inCount]['cod_marca']         );
            $obTAlmoxarifadoLancamentoPerecivel->setDado( 'cod_centro'          , $arSessao['itensEstorno']['item'][$inCount]['cod_centro']        );
            $obTAlmoxarifadoLancamentoPerecivel->obTAlmoxarifadoLancamentoMaterial = &$obTAlmoxarifadoLancamentoMaterial;
            $obTAlmoxarifadoLancamentoPerecivel->setDado ( 'lote' , $arSessao['itensEstorno']['item'][$inCount]['pereciveis'][$inCountP]['lote'] );
            $obTAlmoxarifadoLancamentoPerecivel->inclusao();

        }

        $stFiltro  = " WHERE 1=1 ";
        $stFiltro .= " AND cod_almoxarifado     = ".$arSessao['selecionado']['inCodAlmoxarifado'];
        $stFiltro .= " AND cod_item             = ".$arSessao['itensEstorno']['item'][$inCount]['cod_item'];
        $stFiltro .= " AND cod_marca            = ".$arSessao['itensEstorno']['item'][$inCount]['cod_marca'];
        $stFiltro .= " AND cod_centro           = ".$arSessao['itensEstorno']['item'][$inCount]['cod_centro'];
        $stFiltro .= " AND exercicio_lancamento = '".$arSessao['selecionado']['stExercicioLancamento']."' ";
        $stFiltro .= " AND num_lancamento       = ".$arSessao['selecionado']['inNumLancamento'];
        $stFiltro .= " AND cod_natureza         = ".$arSessao['selecionado']['inCodNatureza'];
        $stFiltro .= " AND tipo_natureza        = '".$arSessao['selecionado']['stTipoNatureza']."' ";

        $obTAlmoxarifadoLancamentoMaterialOrig =  new TAlmoxarifadoLancamentoMaterial;
        $obTAlmoxarifadoLancamentoMaterialOrig->recuperaTodos($rsLancamentoOriginal, $stFiltro);
        $inCodLancamentoOriginal = $rsLancamentoOriginal->getCampo('cod_lancamento');

        $obTAlmoxarifadoLancamentoMaterialEstorno =  new TAlmoxarifadoLancamentoMaterialEstorno;
        $obTAlmoxarifadoLancamentoMaterialEstorno->setDado( 'cod_almoxarifado'      , $arSessao['selecionado']['inCodAlmoxarifado'] );
        $obTAlmoxarifadoLancamentoMaterialEstorno->setDado( 'cod_item'              , $arSessao['itensEstorno']['item'][$inCount]['cod_item'] );
        $obTAlmoxarifadoLancamentoMaterialEstorno->setDado( 'cod_marca'             , $arSessao['itensEstorno']['item'][$inCount]['cod_marca'] );
        $obTAlmoxarifadoLancamentoMaterialEstorno->setDado( 'cod_centro'            , $arSessao['itensEstorno']['item'][$inCount]['cod_centro'] );
        $obTAlmoxarifadoLancamentoMaterialEstorno->setDado( 'cod_lancamento'        , $inCodLancamentoOriginal );
        $obTAlmoxarifadoLancamentoMaterialEstorno->setDado( 'cod_lancamento_estorno', $obTAlmoxarifadoLancamentoMaterial->getDado('cod_lancamento') );
        $obTAlmoxarifadoLancamentoMaterialEstorno->inclusao();

        if ($inCodLancamentoOriginal <>'') {
          $stFiltro2  = " WHERE 1=1 ";
          $stFiltro2 .= " AND exercicio_lancamento = '".$rsLancamentoOriginal->getCampo('exercicio_lancamento')."' ";
          $stFiltro2 .= " AND num_lancamento       = ".$rsLancamentoOriginal->getCampo('num_lancamento');
          $stFiltro2 .= " AND cod_natureza         = ".$rsLancamentoOriginal->getCampo('cod_natureza');
          $stFiltro2 .= " AND tipo_natureza        = '".$rsLancamentoOriginal->getCampo('tipo_natureza')."' ";
          $obComprasNotaFiscalFornecedorOrig = new TComprasNotaFiscalFornecedor();
          $obComprasNotaFiscalFornecedorOrig->setDado('cod_natureza', $rsLancamentoOriginal->getCampo('cod_natureza'));
          $obComprasNotaFiscalFornecedorOrig->setDado('tipo_natureza', $rsLancamentoOriginal->getCampo('tipo_natureza'));
          $obComprasNotaFiscalFornecedorOrig->recuperaTodos($rsLancamentoOriginalNF, $stFiltro2);

          $obComprasNotaFiscalFornecedorEstorno = new TComprasNotaFiscalFornecedor();

          $obComprasNotaFiscalFornecedorEstorno->setDado('cgm_fornecedor', $rsLancamentoOriginalNF->getCampo('cgm_fornecedor'));
          $obComprasNotaFiscalFornecedorEstorno->proximoCod($inCodNota);
          $obComprasNotaFiscalFornecedorEstorno->setDado('cod_nota', $inCodNota );
          $obComprasNotaFiscalFornecedorEstorno->setDado('tipo_natureza', 'S');
          $obComprasNotaFiscalFornecedorEstorno->setDado('cod_natureza', 10);
          $obComprasNotaFiscalFornecedorEstorno->setDado('num_lancamento', $inNumLancamento);
          $obComprasNotaFiscalFornecedorEstorno->setDado('exercicio_lancamento', Sessao::getExercicio());
          $obComprasNotaFiscalFornecedorEstorno->setDado('num_serie', $rsLancamentoOriginalNF->getCampo('num_serie'));
          $obComprasNotaFiscalFornecedorEstorno->setDado('num_nota', $rsLancamentoOriginalNF->getCampo('num_nota'));
          $obComprasNotaFiscalFornecedorEstorno->setDado('dt_nota', $rsLancamentoOriginalNF->getCampo('dt_nota'));
          $obComprasNotaFiscalFornecedorEstorno->setDado('observacao', $arSessao['itensEstorno']['item'][$inCount]['justificativa']);
          $obComprasNotaFiscalFornecedorEstorno->setDado('tipo', 'C');
          $obComprasNotaFiscalFornecedorEstorno->inclusao();
        }

        $obTAlmoxarifadoAtributoEstoqueMaterialValor = new TAlmoxarifadoAtributoEstoqueMaterialValor();
        $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_modulo',         29);
        $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_item',           $arSessao['itensEstorno']['item'][$inCount]['cod_item'] );
        $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_marca',          $arSessao['itensEstorno']['item'][$inCount]['cod_marca'] );
        $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_centro',         $arSessao['itensEstorno']['item'][$inCount]['cod_centro'] );
        $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_almoxarifado',   $arSessao['selecionado']['inCodAlmoxarifado'] );
        $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_lancamento',     $obTAlmoxarifadoLancamentoMaterial->getDado('cod_lancamento') );
        $arAtributos = $arSessao['itensEstorno']['item'][$inCount]['atributos'];
        $arAtributos = is_array($arAtributos)?$arAtributos:array();

        for ($inCAtr=0; $inCAtr<count($arAtributos); $inCAtr++) {
            $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_cadastro',       $arAtributos[$inCAtr]['cod_cadastro'] );
            $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_atributo',       $arAtributos[$inCAtr]['cod_atributo'] );
            $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('valor',              $arAtributos[$inCAtr]['valor'] );
            $obTAlmoxarifadoAtributoEstoqueMaterialValor->inclusao();
        }
    }
}

if ($inCodLancamentoOriginal=='') {
    $stErro = "Efetue o estorno de pelo menos um item.";
}

Sessao::encerraExcecao();

if ($stErro != '') {
    SistemaLegado::exibeAviso($stErro,'form','erro',Sessao::getId() );
} else {
    sistemaLegado::alertaAviso($pgRel.'?'.Sessao::getId()."&stAcao=".$_REQUEST['stAcao']."&inNumLancamento=".$inNumLancamento."&inCodLancamentoEntrada=".$inCodLancamentoOriginal, "Saída por Estorno de Entrada concluído com sucesso! (Lançamento:".$inNumLancamento.") ", $_REQUEST['stAcao'],"aviso", Sessao::getId(), "../");
}
