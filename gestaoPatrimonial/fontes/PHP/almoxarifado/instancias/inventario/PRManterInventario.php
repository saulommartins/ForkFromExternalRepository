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
    * Página de Processamento de Manter Inventario
    * Data de Criação: 24/10/2007

    * @author Analista: Anelise Schwengber
    * @author Desenvolvedor: Andre Almeida

    * @ignore

    $Id:$

    * Casos de uso: uc-03.03.15
*/

/*
    $Log:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoInventario.class.php" );
include_once( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoInventarioItens.class.php" );
include_once( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoEstoqueMaterial.class.php" );
include_once( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCatalogoItemMarca.class.php" );
include_once( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoInventarioAnulacao.class.php" );
include_once( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoNaturezaLancamento.class.php" );
include_once( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoLancamentoMaterial.class.php" );
include_once( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoLancamentoInventarioItens.class.php" );
include_once( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoAtributoInventarioItemValor.class.php" );
include_once( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoAtributoEstoqueMaterialValor.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterInventario";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";
$pgList     = "LS".$stPrograma.".php";

$arInventario = Sessao::read('inventario');

Sessao::setTrataExcecao( true );

if ($_REQUEST['stAcao'] == "incluir" or $_REQUEST['stAcao'] == "alterar") {
    $stMensagemErro = 'Informe ao menos uma classificação que contenha itens.';
    $inCountClassificacoes = count( $arInventario['classificacoes_bloqueadas'] );
    for ($inIdClassificacao=0; $inIdClassificacao<$inCountClassificacoes; $inIdClassificacao++) {

        foreach ($arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'] as $keyItem) {
            foreach ($keyItem['saldos_centro_custo'] as $key => $keyCentro) {
                $nuQuantidadeApurada = str_replace(",", ".", str_replace(".", "", $keyCentro['quantidade_apurada'] ) );
                if ($nuQuantidadeApurada < 0) {
                    $stMensagemErro = 'Informe a quantidade apurada do Item ('.$keyItem['cod_item'].' - '.$keyItem['descricao_resumida'].' - '.$keyItem['desc_marca'].' - '.$keyCentro['descricao_centro'].') .';

                    $js = " window.parent.frames['telaPrincipal'].alterarItem('".$keyItem['inIdItem']."', 'nuQuantidadeApurada_".($key+1)."' ); \n";

                    SistemaLegado::executaFrameOculto($js);

                    break 3;
                }
            }
        }

        $inCountItens = count( $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'] );
        if ($inCountItens > 0) {
            $stMensagemErro = '';
        }
    }
}

$inCodInventario= $arInventario['inCodInventario'];
$stExercicio    = $arInventario['stExercicio'];

if ($stMensagemErro == '') {
    switch ($_REQUEST['stAcao']) {
        case "alterar":
        case "incluir":
            $obTInventario = new TAlmoxarifadoInventario();
            if ($_POST['stAcao'] == 'incluir') {
                $obTInventario->proximoCod( $inCodInventario );
                $stExercicio = Sessao::getExercicio();
            }

            $obTInventario->setDado( 'exercicio'       , $stExercicio );
            $obTInventario->setDado( 'cod_almoxarifado', $arInventario['inCodAlmoxarifado'] );
            $obTInventario->setDado( 'cod_inventario'  , $inCodInventario );
            $obTInventario->setDado( 'dt_inventario'   , date("d/m/Y") );
            $obTInventario->setDado( 'observacao'      , $_POST['stObservacao'] );
            if ($_POST['stAcao'] == 'alterar') {
                $obTInventario->alteracao( );
            } else {
                $obTInventario->inclusao( );
            }

            $inCountItensExcluidos = count( $arInventario['itens_excluidos'] );
            for ($inIdItemExcluido=0; $inIdItemExcluido<$inCountItensExcluidos; $inIdItemExcluido++) {
                $obTInventarioItens = new TAlmoxarifadoInventarioItens();
                $obTInventarioItens->obTAlmoxarifadoInventario = & $obTInventario;
                $obTInventarioItens->setDado( 'cod_item'  , $arInventario['itens_excluidos'][$inIdItemExcluido]['cod_item'] );
                $obTInventarioItens->setDado( 'cod_marca' , $arInventario['itens_excluidos'][$inIdItemExcluido]['cod_marca'] );
                $obTInventarioItens->setDado( 'cod_centro', $arInventario['itens_excluidos'][$inIdItemExcluido]['saldos_centro_custo'][$inIdCentro]['cod_centro'] );
                $obTInventarioItens->recuperaPorChave( $rsInventarioItem );
                if ( $rsInventarioItem->getNumLinhas() > 0 ) {
                    $obTInventarioItens->exclusao( );
                }
                unset($obTInventarioItens);
            }

            for ($inIdClassificacao=0; $inIdClassificacao<$inCountClassificacoes; $inIdClassificacao++) {
                $inCountItens = count( $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'] );
                for ($inIdItem=0; $inIdItem<$inCountItens; $inIdItem++) {
                    $inCountCentro = count( $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['saldos_centro_custo'] );
                    for ($inIdCentro=0; $inIdCentro<$inCountCentro; $inIdCentro++) {

                        $obTCatalogoItemMarca = new TAlmoxarifadoCatalogoItemMarca();
                        $obTCatalogoItemMarca->setDado( 'cod_item'        , $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['cod_item'] );
                        $obTCatalogoItemMarca->setDado( 'cod_marca'       , $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['cod_marca'] );
                        $obTCatalogoItemMarca->recuperaPorChave( $rsItemMarca );
                        if ( $rsItemMarca->getNumLinhas() <= 0 ) {
                            $obTCatalogoItemMarca->inclusao();
                        }

                        $obTEstoqueMaterial = new TAlmoxarifadoEstoqueMaterial();

                        $obTEstoqueMaterial->setDado( 'cod_item'        , $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['cod_item'] );
                        $obTEstoqueMaterial->setDado( 'cod_marca'       , $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['cod_marca'] );
                        $obTEstoqueMaterial->setDado( 'cod_almoxarifado', $arInventario['inCodAlmoxarifado'] );
                        $obTEstoqueMaterial->setDado( 'cod_centro'      , $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['saldos_centro_custo'][$inIdCentro]['cod_centro'] );
                        $obTEstoqueMaterial->recuperaPorChave( $rsEstoqueMaterial );
                        if ( $rsEstoqueMaterial->getNumLinhas() <= 0 ) {
                            $obTEstoqueMaterial->inclusao();
                        }

                        $obTInventarioItens = new TAlmoxarifadoInventarioItens();
                        $obTInventarioItens->obTAlmoxarifadoInventario = & $obTInventario;

                        $obTInventarioItens->setDado( 'cod_item'     , $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['cod_item'] );
                        $obTInventarioItens->setDado( 'cod_marca'    , $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['cod_marca'] );
                        $obTInventarioItens->setDado( 'cod_centro'   , $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['saldos_centro_custo'][$inIdCentro]['cod_centro'] );
                        $nmQuantidade = $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['saldos_centro_custo'][$inIdCentro]['quantidade_apurada'];
                        $nmQuantidade = str_replace('.','',$nmQuantidade);
                        $nmQuantidade = str_replace(',','.',$nmQuantidade);
                        $obTInventarioItens->setDado( 'quantidade'   , $nmQuantidade );
                        $obTInventarioItens->setDado( 'justificativa', trim($arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['saldos_centro_custo'][$inIdCentro]['justificativa']) );
                        $obTInventarioItens->recuperaPorChave( $rsInventarioItem );
                        if ( $rsInventarioItem->getNumLinhas() <= 0 ) {
                            $obTInventarioItens->inclusao( );
                        } else {
                            $obTInventarioItens->alteracao( );
                        }

                        $arAtributos = $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['saldos_centro_custo'][$inIdCentro]['atributos_valores'];

                        if ( count($arAtributos)>0 ) {
                            $obTAtributoItemValor = new TAlmoxarifadoAtributoInventarioItemValor();
                            $obTAtributoItemValor->setDado( 'exercicio'       , $stExercicio );
                            $obTAtributoItemValor->setDado( 'cod_inventario'  , $inCodInventario );
                            $obTAtributoItemValor->setDado( 'cod_almoxarifado', $arInventario['inCodAlmoxarifado'] );
                            $obTAtributoItemValor->setDado( 'cod_centro'      , $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['saldos_centro_custo'][$inIdCentro]['cod_centro'] );
                            $obTAtributoItemValor->setDado( 'cod_item'  , $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['cod_item'] );
                            $obTAtributoItemValor->setDado( 'cod_marca' , $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['cod_marca'] );
                            $obTAtributoItemValor->setDado( 'cod_centro', $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['saldos_centro_custo'][$inIdCentro]['cod_centro'] );

                            foreach ($arAtributos as $key=>$value) {
                                // Inicializa setando vazio até que o valor existente seja setado.
                                $obTAtributoItemValor->setDado( 'valor', '' );

                                $arKey = explode("_",$key);
                                $inCodAtributo = $arKey[0];
                                $inCodCadastro = $arKey[1];

                                if ($arKey[2] == "Selecionados") {
                                    $stValues = "";
                                    foreach ($value as $c => $k) {
                                        if (!empty($k))
                                            $stValues .= $k.",";
                                    }

                                    if (!empty($stValues)) {
                                        $obTAtributoItemValor->setDado( 'valor', substr($stValues, 0, strlen($stValues)-1));
                                    }

                                } elseif (!is_array($value)) {
                                    $obTAtributoItemValor->setDado( 'valor', $value );
                                }

                                $obTAtributoItemValor->setDado( 'cod_cadastro'  , $inCodCadastro );
                                $obTAtributoItemValor->setDado( 'cod_atributo'  , $inCodAtributo );
                                $obTAtributoItemValor->recuperaPorChave( $rsAtributoItemValor );
                                if ( $rsAtributoItemValor->getNumLinhas() <= 0 ) {
                                    $obTAtributoItemValor->inclusao();
                                } else {
                                    $obTAtributoItemValor->alteracao();
                                }
                            }
                        }
                        unset($obTInventarioItens);
                    }
                }
            }
        break;
        case "anular":
            $obTInventarioAnulacao = new TAlmoxarifadoInventarioAnulacao;
            $obTInventarioAnulacao->setDado( 'exercicio'       , $stExercicio );
            $obTInventarioAnulacao->setDado( 'cod_almoxarifado', $arInventario['inCodAlmoxarifado'] );
            $obTInventarioAnulacao->setDado( 'cod_inventario'  , $inCodInventario );
            $obTInventarioAnulacao->setDado( 'motivo'          , $_POST['stMotivo'] );
            $obTInventarioAnulacao->inclusao( );
        break;
        case "processar":
            $obTInventario = new TAlmoxarifadoInventario();
            $obTInventario->setDado( 'exercicio'       , $stExercicio );
            $obTInventario->setDado( 'cod_almoxarifado', $arInventario['inCodAlmoxarifado'] );
            $obTInventario->setDado( 'cod_inventario'  , $inCodInventario );
            $obTInventario->setDado( 'processado'      , true );
            $obTInventario->alteracao( );
            //processa saídas
            processaItens($inCodInventario, $stExercicio, "S");
            //processa entradas
            processaItens($inCodInventario, $stExercicio, "E");
        break;
    }
}

if ($stMensagemErro != '') {
    SistemaLegado::exibeAviso($stMensagemErro,'form','erro',Sessao::getId() );
} else {
    if ($_REQUEST['stAcao'] == "processar") {
        sistemaLegado::alertaAviso($pgList.'?'.Sessao::getId()."&stAcao=".$_REQUEST['stAcao'], "Processar Inventário concluído com sucesso! ($inCodInventario/$stExercicio Almoxarifado:".$arInventario['inCodAlmoxarifado'].") ", $_REQUEST['stAcao'],"aviso", Sessao::getId(), "../");
    } else {
        $pgProx = ($_REQUEST['stAcao'] == "incluir") ? $pgForm : $pgList;
        sistemaLegado::alertaAviso($pgProx.'?'.Sessao::getId()."&stAcao=".$_REQUEST['stAcao'], "$inCodInventario/$stExercicio"." Almoxarifado:".$arInventario['inCodAlmoxarifado'],$_REQUEST['stAcao'],"aviso", Sessao::getId(), "../");
    }
}

Sessao::encerraExcecao();

function processaItens($inCodInventario, $stExercicio, $stTipo)
{
    $arInventario = Sessao::read('inventario');

    $boPrimeiroLancamento = true;
    $obTAlmoxarifadoNaturezaLancamento = new TAlmoxarifadoNaturezaLancamento;
    $obTAlmoxarifadoNaturezaLancamento->proximoCod( $inNumLancamento );
    $obTAlmoxarifadoNaturezaLancamento->setDado ( 'exercicio_lancamento', Sessao::getExercicio()      );
    $obTAlmoxarifadoNaturezaLancamento->setDado ( 'cod_natureza'        , 5                           );
    $obTAlmoxarifadoNaturezaLancamento->setDado ( 'cgm_almoxarife'      , Sessao::read('numCgm') );
    $obTAlmoxarifadoNaturezaLancamento->setDado ( 'num_lancamento', $inNumLancamento );
    $obTAlmoxarifadoNaturezaLancamento->setDado ( 'tipo_natureza' , $stTipo );
    $obTAlmoxarifadoNaturezaLancamento->setDado ( 'numcgm_usuario' , Sessao::read('numCgm'));
    $inCountClassificacoes = count( $arInventario['classificacoes_bloqueadas'] );
    for ($inIdClassificacao=0; $inIdClassificacao<$inCountClassificacoes; $inIdClassificacao++) {
        $inCountItens = count( $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'] );
        for ($inIdItem=0; $inIdItem<$inCountItens; $inIdItem++) {
            $inCountCentro = count( $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['saldos_centro_custo'] );
            for ($inIdCentro=0; $inIdCentro<$inCountCentro; $inIdCentro++) {
                $nmSaldo             = $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['saldos_centro_custo'][$inIdCentro]['saldo'];
                $nmQuantidadeApurada = $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['saldos_centro_custo'][$inIdCentro]['quantidade_apurada'];
                $nmSaldo = str_replace('.','',$nmSaldo);
                $nmSaldo = str_replace(',','.',$nmSaldo);
                $nmQuantidadeApurada = str_replace('.','',$nmQuantidadeApurada);
                $nmQuantidadeApurada = str_replace(',','.',$nmQuantidadeApurada);
                $nmValorLancamento =  $nmQuantidadeApurada - $nmSaldo;

                if (($stTipo == "S" and $nmValorLancamento< 0) or ($stTipo == "E" and $nmValorLancamento> 0)) {
                    if ($boPrimeiroLancamento) {
                         $boPrimeiroLancamento = false;
                         $obTAlmoxarifadoNaturezaLancamento->inclusao();
                    }
                    $obTLancamentoMaterial = new TAlmoxarifadoLancamentoMaterial();
                    $obTLancamentoMaterial->obTAlmoxarifadoNaturezaLancamento = & $obTAlmoxarifadoNaturezaLancamento;
                    $obTLancamentoMaterial->proximoCod( $inCodLancMat );
                    $obTLancamentoMaterial->setDado( 'cod_lancamento' , $inCodLancMat           );

                    $obTLancamentoMaterial->setDado( 'cod_almoxarifado', $arInventario['inCodAlmoxarifado'] );
                    $obTLancamentoMaterial->setDado( 'cod_item'     , $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['cod_item'] );
                    $obTLancamentoMaterial->setDado( 'cod_marca'    , $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['cod_marca'] );
                    $obTLancamentoMaterial->setDado( 'cod_centro'   , $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['saldos_centro_custo'][$inIdCentro]['cod_centro'] );
                    $obTLancamentoMaterial->setDado( 'quantidade', $nmValorLancamento );

                    # Grava o valor unitário (média de entradas X valores das entradas).
                    $obTLancamentoMaterial->recuperaSaldoValorUnitario($rsItemValorUnitario);
                    $stValorUnitario = number_format($rsItemValorUnitario->getCampo('valor_unitario'), 4, ',', '.');

                    $stValorUnitario = str_replace('.','',$stValorUnitario   );
                    $stValorUnitario = str_replace(',','.', $stValorUnitario );

                    $valorTotal = $stValorUnitario * $nmValorLancamento;

                    $obTLancamentoMaterial->setDado('valor_mercado', $valorTotal);

                    $obTLancamentoMaterial->inclusao( );

                    $obTLancamentoInventarioItens = new TAlmoxarifadoLancamentoInventarioItens();
                    $obTLancamentoInventarioItens->obTAlmoxarifadoLancamentoMaterial = & $obTLancamentoMaterial;
                    $obTLancamentoInventarioItens->setDado( 'cod_inventario', $inCodInventario );
                    $obTLancamentoInventarioItens->setDado( 'exercicio', $stExercicio );
                    $obTLancamentoInventarioItens->inclusao( );

                    $arAtributos = $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['saldos_centro_custo'][$inIdCentro]['atributos_valores'];

                    if ( count($arAtributos)>0 ) {
                        $obTAtributoEstoqueValor = new TAlmoxarifadoAtributoEstoqueMaterialValor();
                        $obTAtributoEstoqueValor->setDado( 'cod_modulo'  , 29 );
                        $obTAtributoEstoqueValor->setDado( 'cod_lancamento'   , $inCodLancMat );
                        $obTAtributoEstoqueValor->setDado( 'cod_almoxarifado' , $arInventario['inCodAlmoxarifado'] );
                        $obTAtributoEstoqueValor->setDado( 'cod_item'    , $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['cod_item'] );
                        $obTAtributoEstoqueValor->setDado( 'cod_marca'   , $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['cod_marca'] );
                        $obTAtributoEstoqueValor->setDado( 'cod_centro'  , $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['saldos_centro_custo'][$inIdCentro]['cod_centro'] );
                        foreach ($arAtributos as $key=>$value) {
                            $arKey = explode("_",$key);
                            $inCodAtributo = $arKey[0];
                            $inCodCadastro = $arKey[1];
                            $obTAtributoEstoqueValor->setDado( 'cod_cadastro', $inCodCadastro );
                            $obTAtributoEstoqueValor->setDado( 'cod_atributo', $inCodAtributo );

                            $obTAtributoEstoqueValor->setDado( 'valor' , $value );
                            $obTAtributoEstoqueValor->inclusao( );
                        }
                    }

                }
            }
        }
    }
}

?>
