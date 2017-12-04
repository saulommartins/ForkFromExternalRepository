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
    * Página Oculta de Processar Implantacao
    * Data de Criação   : 08/06/2006

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Rodrigo

    $Id: PRMovimentacaoDiversa.php 59612 2014-09-02 12:00:51Z gelson $

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once TALM."TAlmoxarifadoCatalogoItem.class.php";
include_once TALM."TAlmoxarifadoNaturezaLancamento.class.php";
include_once TALM."TAlmoxarifadoLancamentoMaterial.class.php";
include_once TALM."TAlmoxarifadoDoacaoEmprestimo.class.php";
include_once TALM."TAlmoxarifadoEstoqueMaterial.class.php";
include_once TALM."TAlmoxarifadoPerecivel.class.php";
include_once TALM."TAlmoxarifadoLancamentoPerecivel.class.php";
include_once TALM."TAlmoxarifadoCatalogoItemMarca.class.php";
include_once TALM."TAlmoxarifadoAtributoEstoqueMaterialValor.class.php";
include_once TALM."TAlmoxarifadoAtributoCatalogoItem.class.php";
include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCatalogoItemBarras.class.php";
include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoLancamentoPerecivel.class.php";
include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoConfiguracao.class.php";
include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoInventarioItens.class.php";
include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoLancamentoBem.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioBem.class.php";
include_once CAM_GP_COM_MAPEAMENTO."TComprasNotaFiscalFornecedor.class.php";
include_once CAM_GP_COM_MAPEAMENTO."TComprasNotaFiscalFornecedorOrdem.class.php";

$stAcao = $request->get('stAcao');

$stPrograma = "MovimentacaoDiversa";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";
$pgRel  = "OCGera".$stPrograma.".php";

$arrayItens = Sessao::read('itens');

foreach ($arrayItens as $chave =>$dadosItem) {
    verificaItensEmInventarioNaoProcessado($dadosItem);
}

Sessao::setTrataExcecao( true );

switch ($stAcao) {
    default:

        $stFiltro = "";
        $boInclui = true;
        $rsRecordSetCatalogoItemMarca = new RecordSet;
        $rsRecordSetEstoqueMaterial   = new RecordSet;
        
        $obTAlmoxarifadoNaturezaLancamento = new TAlmoxarifadoNaturezaLancamento;
        
        $obTAlmoxarifadoNaturezaLancamento->setDado('tipo_natureza' , 'E');
        $obTAlmoxarifadoNaturezaLancamento->setDado('cod_natureza'  , $stAcao == 'doacao' ? 3 : 9 );

        # Recupera o num_lancamento considerando as configurações do Almoxarifado.
        $obTAlmoxarifadoNaturezaLancamento->recuperaNumNaturezaLancamento($rsNumLancamento);

        $inNumLancamento = $rsNumLancamento->getCampo('num_lancamento');
        $obTAlmoxarifadoNaturezaLancamento->setDado('num_lancamento' , $inNumLancamento);
        $obTAlmoxarifadoNaturezaLancamento->setDado('numcgm_usuario' , Sessao::read('numCgm'));

        $obTAlmoxarifadoNaturezaLancamento->inclusao();

        $inCodNaturezaLancamento = $inNumLancamento;

        if ($stAcao != 'doacao') {
            // Faz a inclusão na tabela compras.nota_fiscal_fornecedor_ordem
            insereComprasNotaFiscalFornecedor( $inCodNaturezaLancamento );
        }

        $arrayItens = Sessao::read('itens');

        if (count($arrayItens) > 0) {
            $contador = 1;
            $arBens = array();

            foreach ($arrayItens as $key => $value) {

                $quantidadeItem = str_replace('.','',$_REQUEST['nuQuantidade_'.$contador]);
                $quantidadeItem = str_replace(',','.',$quantidadeItem);

                $valorTotalItem = str_replace('.','',$_REQUEST['nuVlTotal_'.$contador]);
                $valorTotalItem = str_replace(',','.',$valorTotalItem);

                if ($quantidadeItem <= 0 || $valorTotalItem <= 0) {
                    $boInclui = false;
                    break;
                }

                $obTAlmoxarifadoCatalagoItem 		         = new TAlmoxarifadoCatalogoItem;
                $obTAlmoxarifadoLancamentoMaterial           = new TAlmoxarifadoLancamentoMaterial;
                $obTAlmoxarifadoDoacaoEmprestimo             = new TAlmoxarifadoDoacaoEmprestimo;
                $obTAlmoxarifadoCatalogoItemMarca            = new TAlmoxarifadoCatalogoItemMarca;
                $obTAlmoxarifadoEstoqueMaterial              = new TAlmoxarifadoEstoqueMaterial;
                $obTAlmoxarifadoPerecivel                    = new TAlmoxarifadoPerecivel;
                $obTAlmoxarifadoLancamentoPerecivel          = new TAlmoxarifadoLancamentoPerecivel;
                $obTAlmoxarifadoAtributoEstoqueMaterialValor = new TAlmoxarifadoAtributoEstoqueMaterialValor;
                $obTAlmoxarifadoCatalogoItemBarras           = new TAlmoxarifadoCatalogoItemBarras;

                $obTAlmoxarifadoLancamentoMaterial->obTAlmoxarifadoEstoqueMaterial          = &$obTAlmoxarifadoEstoqueMaterial;
                $obTAlmoxarifadoLancamentoMaterial->obTAlmoxarifadoNaturezaLancamento       = &$obTAlmoxarifadoNaturezaLancamento;
                $obTAlmoxarifadoDoacaoEmprestimo->obTAlmoxarifadoLancamentoMaterial         = &$obTAlmoxarifadoLancamentoMaterial;
                $obTAlmoxarifadoEstoqueMaterial->obTAlmoxarifadoCatalogoItemMarca           = &$obTAlmoxarifadoCatalogoItemMarca;
                $obTAlmoxarifadoPerecivel->obTAlmoxarifadoEstoqueMaterial                   = &$obTAlmoxarifadoEstoqueMaterial;
                $obTAlmoxarifadoLancamentoPerecivel->obTAlmoxarifadoLancamentoMaterial      = &$obTAlmoxarifadoLancamentoMaterial;
                $obTAlmoxarifadoLancamentoPerecivel->obTAlmoxarifadoPerecivel               = &$obTAlmoxarifadoPerecivel;
                $obTAlmoxarifadoAtributoEstoqueMaterialValor->obTAlmoxarifadoLancamentoMaterial = &$obTAlmoxarifadoLancamentoMaterial;

                if ($value['inCodTipo'] != '') {
                    $obTAlmoxarifadoCatalagoItem->setDado('cod_item', $value['inCodItem']);
                    $obTAlmoxarifadoCatalagoItem->recuperaPorChave( $rsItem );

                    $obTAlmoxarifadoCatalagoItem->setDado('cod_unidade', $value['inCodUnidade'] );
                    $obTAlmoxarifadoCatalagoItem->setDado('cod_grandeza', $value['inCodGrandeza'] );
                    $obTAlmoxarifadoCatalagoItem->setDado('cod_tipo', $value['inCodTipo'] );
                    $obTAlmoxarifadoCatalagoItem->setDado('cod_catalogo', $rsItem->getCampo('cod_catalogo') );
                    $obTAlmoxarifadoCatalagoItem->setDado('cod_classificacao', $rsItem->getCampo('cod_classificacao') );
                    $obTAlmoxarifadoCatalagoItem->alteracao();
                }

                //inserido em 24/07/2006 para tratar o valor do campo valor total de mercado quando o mesmo estava vazio. Por Fernando Zank Correa Evangelista
                $_REQUEST['nuVlTotal_'.$contador] = $_REQUEST['nuVlTotal_'.$contador] != '' ? $_REQUEST['nuVlTotal_'.$contador] : '0,00';

                $obTAlmoxarifadoLancamentoMaterial->setDado('cod_item' ,$value['inCodItem']                  );
                $obTAlmoxarifadoLancamentoMaterial->setDado('cod_marca',$value['inCodMarca']                 );
                $obTAlmoxarifadoLancamentoMaterial->setDado('cod_almoxarifado',$_REQUEST['inCodAlmoxarifado']);
                $obTAlmoxarifadoLancamentoMaterial->setDado('cod_centro',$value['inCodCentroCusto']          );
                $obTAlmoxarifadoLancamentoMaterial->setDado('quantidade',$_REQUEST['nuQuantidade_'.$contador]);

                if ($stAcao == 'doacao') {
                    //pega os dados do processo
                    $arProcesso = explode('/',$_REQUEST['stChaveProcesso']);

                    //seta na base
                    $obTAlmoxarifadoDoacaoEmprestimo->setDado( 'cod_processo', $arProcesso[0] );
                    $obTAlmoxarifadoDoacaoEmprestimo->setDado( 'ano_exercicio', $arProcesso[1] );
                }

                $obTAlmoxarifadoCatalogoItemMarca->recuperaPorChave($rsRecordSetCatalogoItemMarca);
                if ($rsRecordSetCatalogoItemMarca->getNumLinhas()<=0) {
                   $obTAlmoxarifadoCatalogoItemMarca->inclusao();
                }

                //// codigo de barras
                $obTAlmoxarifadoCatalogoItemBarras->setDado( 'cod_item'  , $value['inCodItem']  );
                $obTAlmoxarifadoCatalogoItemBarras->setDado( 'cod_marca' , $value['inCodMarca'] );
                $obTAlmoxarifadoCatalogoItemBarras->exclusao();
                if ($value['codigoBarras']) {
                    $obTAlmoxarifadoCatalogoItemBarras->setDado( 'codigo_barras', $value['codigoBarras'] );
                    $obTAlmoxarifadoCatalogoItemBarras->inclusao();
                }

                $filtro = " WHERE estoque_material.cod_item         = ".$value['inCodItem'];
                $filtro.= "   AND estoque_material.cod_marca        = ".$value['inCodMarca'];
                $filtro.= "   AND estoque_material.cod_almoxarifado = ".$_REQUEST['inCodAlmoxarifado'];
                $filtro.= "   AND estoque_material.cod_centro       = ".$value['inCodCentroCusto'];

                $obTAlmoxarifadoEstoqueMaterial->recuperaTodos($rsRecordSetEstoqueMaterial,$filtro);
                if ($rsRecordSetEstoqueMaterial->getNumLinhas()<=0) {
                   $obTAlmoxarifadoEstoqueMaterial->inclusao();
                }

                if (count( $value['lotes']) > 0 ) {
                    $quantidadeTotalLotes = str_replace(',','.', (str_replace('.','', $_REQUEST['nuQuantidade_'.$contador])));

                    //perecivel
                    foreach ($value['lotes'] as $keylote => $valuelote) {
                        $obTAlmoxarifadoPerecivel->setDado("lote", $valuelote["stNumLote"]);
                        $obTAlmoxarifadoPerecivel->setDado("cod_item",$value['inCodItem']);
                        $obTAlmoxarifadoPerecivel->setDado("cod_marca",$value['inCodMarca']);
                        $obTAlmoxarifadoPerecivel->setDado("cod_almoxarifado",$_REQUEST['inCodAlmoxarifado']);
                        $obTAlmoxarifadoPerecivel->setDado("cod_centro",$value['inCodCentroCusto']);
                        $obTAlmoxarifadoPerecivel->setDado("dt_validade", $valuelote["stDataValidade"]);
                        $obTAlmoxarifadoPerecivel->setDado("dt_fabricacao", $valuelote["stDataFabricacao"]);

                        $obTAlmoxarifadoPerecivel->recuperaPorChave($rsPerecivel);

                        if ($rsPerecivel->getNumLinhas()<=0) {
                           $obTAlmoxarifadoPerecivel->inclusao();
                        }

                        $obTAlmoxarifadoLancamentoMaterial->setDado("cod_lancamento", '');
                        $obTAlmoxarifadoLancamentoMaterial->setDado("quantidade", $valuelote["nmQuantidadeLote"]);

                        $valorTotalMercado = str_replace(',','.', (str_replace('.','', $_REQUEST['nuVlTotal_'.$contador])));
                        $valorUnitario = ($valorTotalMercado/$quantidadeTotalLotes);
                        $valorMercado  = $valorUnitario * str_replace(',','.', (str_replace('.','', $valuelote["nmQuantidadeLote"])));
                        $valorMercado = number_format($valorMercado,4,',','.');

                        $obTAlmoxarifadoLancamentoMaterial->setDado( 'valor_mercado', $valorMercado );

                        $obTAlmoxarifadoLancamentoMaterial->inclusao();

                        $obTAlmoxarifadoLancamentoPerecivel = new TAlmoxarifadoLancamentoPerecivel;
                        $obTAlmoxarifadoLancamentoPerecivel->obTAlmoxarifadoLancamentoMaterial = &$obTAlmoxarifadoLancamentoMaterial;
                        $obTAlmoxarifadoLancamentoPerecivel->setDado ( 'cod_item'        , $value['inCodItem']            );
                        $obTAlmoxarifadoLancamentoPerecivel->setDado ( 'cod_marca'       , $value['inCodMarca']           );
                        $obTAlmoxarifadoLancamentoPerecivel->setDado ( 'cod_almoxarifado', $_REQUEST['inCodAlmoxarifado'] );
                        $obTAlmoxarifadoLancamentoPerecivel->setDado ( 'lote'            , $valuelote["stNumLote"]        );
                        $obTAlmoxarifadoLancamentoPerecivel->setDado ( 'cod_centro'      , $value['inCodCentroCusto']     );
                        $obTAlmoxarifadoLancamentoPerecivel->inclusao();
                    }
                }

                if (count($value['lotes']) < 1) {
                    $obTAlmoxarifadoLancamentoMaterial->setDado( 'cod_lancamento', '');
                    $obTAlmoxarifadoLancamentoMaterial->setDado( 'valor_mercado', $_REQUEST['nuVlTotal_'.$contador]);
                    $obTAlmoxarifadoLancamentoMaterial->inclusao();
                }

                ///inserir os bens patrimoniais na tabela de bens
                if ($value['inCodTipo'] == 4) {
                    inserirBemPatrimonial($value, $obTAlmoxarifadoLancamentoMaterial->getDado('cod_lancamento'), $arBens);
                }

                $countInsertsLancamentoMaterial = 0;
                if (count($value['atributos']) > 0) {
                    if (is_array($value['atributos']) == true) {

                        foreach ($value['atributos'] as $chave => $dados) {

                            $countAtributos = 0;
                            if ( !empty($dados['valor']) ) {
                                $obTAlmoxarifadoAtributoCatalogoItem = new TAlmoxarifadoAtributoCatalogoItem();
                                $obTAlmoxarifadoAtributoCatalogoItem->setDado('cod_atributo'	,$value['atributos'][$countInsertsLancamentoMaterial]['cod_atributo']);
                                $obTAlmoxarifadoAtributoCatalogoItem->recuperaTipoAtributo($rsTipoAtributo);

                                $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_modulo'		,$value['atributos'][$countInsertsLancamentoMaterial]['cod_modulo']);
                                $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_cadastro'	,$value['atributos'][$countInsertsLancamentoMaterial]['cod_cadastro']);
                                $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_atributo'	,$value['atributos'][$countInsertsLancamentoMaterial]['cod_atributo']);
                                $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_item'		,$value['inCodItem']);
                                $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_centro'		,$value['inCodCentroCusto']);
                                $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_almoxarifado',$_REQUEST['inCodAlmoxarifado']);
                                $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_marca'		,$value['inCodMarca']);
                                $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_lancamento'  ,$obTAlmoxarifadoLancamentoMaterial->getDado('cod_lancamento'));
                                if ($rsTipoAtributo->getCampo('cod_tipo') == 4) {
                                    $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('valor' , str_replace(' ','',trim($value['atributos'][$countInsertsLancamentoMaterial]['valor'])));
                                } else {
                                    $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('valor' , $value['atributos'][$countInsertsLancamentoMaterial]['valor']);
                                }
                                $obTAlmoxarifadoAtributoEstoqueMaterialValor->inclusao();
                            }
                            $countInsertsLancamentoMaterial++;
                        }
                    }
                }

                if ($stAcao == 'doacao') {
                    $obTAlmoxarifadoDoacaoEmprestimo->inclusao();
                }

                $contador++;
            }

            if (count($arBens) == 1) {
                $stDescItem = SistemaLegado::pegaDado('descricao', 'patrimonio.bem', 'WHERE cod_bem = '.$arBens[0]);
                $stMsgBem = "Bem de Patrimônio incluso (".$arBens[0]." - ".trim($stDescItem).").";
            } elseif (count($arBens) > 1) {
                $stMsgBem = "Bens de Patrimônio inclusos: ".$arBens[0]." à ".$arBens[count($arBens) - 1].".";
            }

            if ($boInclui) {
                if ($stAcao == 'doacao') {
                    SistemaLegado::alertaAviso($pgRel.'?'.Sessao::getId()."&stAcao=".$stAcao."&inNumLancamento=".$inCodNaturezaLancamento."&inCodNatureza=3&inCodAlmoxarifado=".$_REQUEST['inCodAlmoxarifado'],"Movimentação por doação concluída com sucesso. ".$stMsgBem, "aviso", Sessao::getId(), "../");
                } else
                    SistemaLegado::alertaAviso($pgRel.'?'.Sessao::getId()."&stAcao=".$stAcao."&inNumLancamento=".$inCodNaturezaLancamento."&inCodNatureza=9&inCodAlmoxarifado=".$_REQUEST['inCodAlmoxarifado'],"Movimentação por Entradas Diversas concluída com sucesso. ".$stMsgBem, "aviso", Sessao::getId(), "../");
            } else {
                    SistemaLegado::exibeAviso('A <b>Quantidade</b> e o <b>Valor de Mercado</b> do <i><b>item '.$contador.'</b></i> deve ser maior que zero.','form','erro',Sessao::getId() );
            }
        } else {
            SistemaLegado::exibeAviso('Deve existir ao menos um ítem na lista.','form','erro',Sessao::getId() );
            SistemaLegado::LiberaFrames();
        }
    break;
}

Sessao::encerraExcecao();

function verificaItensEmInventarioNaoProcessado($arItemVerificacao)
{
    $obTAlmoxarifadoInventario = new TAlmoxarifadoInventarioItens;

    $obTAlmoxarifadoInventario->setDado('cod_item', $arItemVerificacao['inCodItem']);
    $obTAlmoxarifadoInventario->setDado('cod_almoxarifado', $_REQUEST['inCodAlmoxarifado']);
    //$obTAlmoxarifadoInventario->setDado('exercicio', Sessao::getExercicio());
    $obTAlmoxarifadoInventario->setDado('cod_marca',$arItemVerificacao['inCodMarca'] );
    $obTAlmoxarifadoInventario->setDado('cod_centro',$arItemVerificacao['inCodCentroCusto'] );

    $obTAlmoxarifadoInventario->verificaItensInventarioNaoProcessado($rsItensInventario);

    if ($rsItensInventario->getNumLinhas()>0) {
        $boIncluir = false;
        SistemaLegado::exibeAviso('O item '.$rsItensInventario->getCampo('cod_item').'-'.$rsItensInventario->getCampo('descricao').' não pode ser utilizado pois está em processo de inventário.','form','erro',Sessao::getId() );
        SistemaLegado::LiberaFrames();
    exit;
    }
}

/**
 *
 * Faz a inclusão na tabela almoxarifado.compras_nota_fiscal_fornecedor_item
 *
 * @param int $inNumLancamento - recebe o numero do lancamento
 *
 */
function insereComprasNotaFiscalFornecedor($inNumLancamento)
{
    $obComprasNotaFiscalFornecedor = new TComprasNotaFiscalFornecedor();
    $obComprasNotaFiscalFornecedor->setDado('cgm_fornecedor', $_REQUEST['inCGM']);
    $obComprasNotaFiscalFornecedor->proximoCod($inCodNota);
    $obComprasNotaFiscalFornecedor->setDado('cod_nota', $inCodNota );
    $obComprasNotaFiscalFornecedor->setDado('tipo_natureza', 'E');

    if ($_REQUEST['stAcao'] == 'doacao') {
        $obComprasNotaFiscalFornecedor->setDado('cod_natureza', 3);
    } else {
        $obComprasNotaFiscalFornecedor->setDado('cod_natureza', 9);
    }

    $obComprasNotaFiscalFornecedor->setDado('num_lancamento', $inNumLancamento);
    $obComprasNotaFiscalFornecedor->setDado('exercicio_lancamento', Sessao::getExercicio());

    $obComprasNotaFiscalFornecedor->setDado('num_serie', $_REQUEST['inNumSerieNota']);
    $obComprasNotaFiscalFornecedor->setDado('num_nota', $_REQUEST['inNumNota']);
    $obComprasNotaFiscalFornecedor->setDado('dt_nota', $_REQUEST['dtNotaFiscal']);
    $obComprasNotaFiscalFornecedor->setDado('observacao', $_REQUEST['stObservacao']);

    $obComprasNotaFiscalFornecedor->setDado('tipo', 'C');
    $obComprasNotaFiscalFornecedor->inclusao();

}

function inserirBemPatrimonial($arItens, $codLancamento, &$arBens)
{
    $obTPatrimonioBem = new TPatrimonioBem;
    $TAlmoxarifadoLancamentoBem = new TAlmoxarifadoLancamentoBem;

    $obTAlmoxarifadoCatalagoItem = new TAlmoxarifadoCatalogoItem;
    $obTAlmoxarifadoCatalagoItem->setDado('cod_item', $arItens['inCodItem']);
    $obTAlmoxarifadoCatalagoItem->recuperaPorChave($rsCatalogoItem);

    if ($arItens['stPlacaIdentificacao'] != 'nao') {

        $identificacao = 't';
        $indice = 0;

        $numeroPlaca = $arItens['stNumeroPlaca'];

        for ($i = 0; $indice < $arItens['quantidade']; $i++) {
            //verifica se o numero da placa já existe
            $stFiltro = " WHERE num_placa like '%".$numeroPlaca."'";

            $obTPatrimonioBem->recuperaTodos( $rsBem, $stFiltro );

            if ( $rsBem->getNumLinhas() > 0 ) {
                $numeroPlaca++;
            } else {
                $arrayPlacas[$indice] = $numeroPlaca;
                $indice++;
                $numeroPlaca++;
            }
        }
    } else {
        $identificacao = 'f';
    }

    $inValorItem = str_replace(',','.',str_replace('.','',$arItens['vtotal']));
    $inQuantidade = str_replace(',','.',str_replace('.','',$arItens['quantidade']));

    $ValorBem = $inValorItem/$inQuantidade;

    for ($i = 0; $i<$arItens['quantidade'];$i++) {

        $obTPatrimonioBem->proximoCod( $inCodBem );

        $obTPatrimonioBem->setDado( 'cod_bem'      , $inCodBem );
        $obTPatrimonioBem->setDado( 'cod_natureza' , 0 );
        $obTPatrimonioBem->setDado( 'cod_grupo'    , 0 );
        $obTPatrimonioBem->setDado( 'cod_especie'  , 0 );
        $obTPatrimonioBem->setDado( 'numcgm'       , $_REQUEST['inCGM'] );
        $obTPatrimonioBem->setDado( 'descricao'    , $rsCatalogoItem->getCampo('descricao_resumida'));
        $obTPatrimonioBem->setDado( 'detalhamento' , $rsCatalogoItem->getCampo('descricao') );
        $obTPatrimonioBem->setDado( 'dt_aquisicao' , date('d/m/Y'));

        $obTPatrimonioBem->setDado( 'vl_bem', $ValorBem );
        $obTPatrimonioBem->setDado( 'vl_depreciacao', 0.00);

        $obTPatrimonioBem->setDado( 'identificacao', $identificacao );

        if ($identificacao != 'f') {
            $obTPatrimonioBem->setDado( 'num_placa', $arrayPlacas[$i] );
        } else {
            $obTPatrimonioBem->setDado( 'num_placa', null );
        }

        $obTPatrimonioBem->inclusao();

        $TAlmoxarifadoLancamentoBem->setDado('cod_lancamento',$codLancamento);
        $TAlmoxarifadoLancamentoBem->setDado('cod_item',$arItens['inCodItem']);
        $TAlmoxarifadoLancamentoBem->setDado('cod_marca',$arItens['inCodMarca']);
        $TAlmoxarifadoLancamentoBem->setDado('cod_almoxarifado',$_REQUEST['inCodAlmoxarifado']);
        $TAlmoxarifadoLancamentoBem->setDado('cod_centro',$arItens['inCodCentroCusto']);
        $TAlmoxarifadoLancamentoBem->setDado('cod_bem',$inCodBem);
        $TAlmoxarifadoLancamentoBem->inclusao();

        # Armazena o cod_bem e descricao do item no array
        $arBens[] = $inCodBem;
    }

    return $arBens;
}

?>
