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
 * Arquivo de Processamento de Entrada por Ordem de Compra
 * Data de Criação   : 28/11/2007

 * @author Analista: Diego Barbosa Victoria
 * @author Desenvolvedor: Henrique Girardi dos Santos

 * @ignore

 * Casos de uso: uc-03.03.18

 $Id: PRMovimentacaoOrdemCompra.php 65755 2016-06-15 18:25:17Z jean $

**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

require_once CAM_GP_COM_MAPEAMENTO."TComprasNotaFiscalFornecedor.class.php";
require_once CAM_GP_COM_MAPEAMENTO."TComprasNotaFiscalFornecedorOrdem.class.php";
require_once TALM."TAlmoxarifadoNaturezaLancamento.class.php";
require_once TALM."TAlmoxarifadoCatalogoItem.class.php";
require_once TALM."TAlmoxarifadoCatalogoItemMarca.class.php";
require_once TALM."TAlmoxarifadoEstoqueMaterial.class.php";
require_once TALM."TAlmoxarifadoLancamentoMaterial.class.php";
require_once TALM."TAlmoxarifadoLancamentoPerecivel.class.php";
require_once TALM."TAlmoxarifadoPerecivel.class.php";
include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoLancamentoBem.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioBem.class.php";
require_once TALM."TAlmoxarifadoAtributoEstoqueMaterialValor.class.php";
include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoConfiguracao.class.php";
include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoInventarioItens.class.php";
require_once TALM."TAlmoxarifadoLancamentoOrdem.class.php";

// Define a função do arquivo, ex: excluir ou alterar
$stAcao = $request->get('stAcao');

// Define o nome dos arquivos PHP
$stPrograma = "MovimentacaoOrdemCompra";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";
$pgRel  = "OCGeraMovimentacaoDiversa.php";

function validaEntradaPorOrdemCompra()
{
    $stMensagem = "";

    $arItensEntrada = Sessao::read('arItensEntrada');

    if (SistemaLegado::ComparaDatas($_REQUEST['dtNotaFiscal'], date("d/m/Y"))) {
        $stMensagem .= "A Data da Nota Fiscal não pode ser posterior a data de hoje.";

    } elseif ( count($arItensEntrada) <= 0 ) {
        $stMensagem .= "Não foi atribuído nenhum item para a entrada.";
    }

    $arItensEntrada = Sessao::read('arItensEntrada');

    foreach ($arItensEntrada as $chave => $dados) {
        verificaItensEmInventarioNaoProcessado($dados);
    }

    return $stMensagem;
}

/**
 *
 * Faz a inclusão na tabela almoxarifado.catalogo_item_marca
 *
 * @param int $inNumLancamento - recebe o numero do lancamento
 *
 */
function insereComprasNotaFiscalFornecedor($inNumLancamento)
{
    $obComprasNotaFiscalFornecedor = new TComprasNotaFiscalFornecedor();
    $obComprasNotaFiscalFornecedor->setDado('cgm_fornecedor', $_REQUEST['inCodFornecedor']);
    $obComprasNotaFiscalFornecedor->proximoCod($inCodNota);
    $obComprasNotaFiscalFornecedor->setDado('cod_nota', $inCodNota );
    $obComprasNotaFiscalFornecedor->setDado('tipo_natureza', 'E');
    $obComprasNotaFiscalFornecedor->setDado('cod_natureza', 1);
    $obComprasNotaFiscalFornecedor->setDado('num_lancamento', $inNumLancamento);
    $obComprasNotaFiscalFornecedor->setDado('exercicio_lancamento', Sessao::getExercicio());
    $obComprasNotaFiscalFornecedor->setDado('num_serie', $_REQUEST['inNumSerie']);
    $obComprasNotaFiscalFornecedor->setDado('num_nota', $_REQUEST['inNotaFiscal']);
    $obComprasNotaFiscalFornecedor->setDado('dt_nota', $_REQUEST['dtNotaFiscal']);
    $obComprasNotaFiscalFornecedor->setDado('observacao', $_REQUEST['stObservacao']);
    $obComprasNotaFiscalFornecedor->setDado('tipo', 'C');
    $obComprasNotaFiscalFornecedor->inclusao($boTransacao);

    insereComprasNotaFiscalFornecedorOrdem( $inCodNota );
}

/**
 *
 * Faz a inclusão na tabela compras.nota_fiscal_fornecedor_ordem
 *
 * @param int $inCodNota- recebe o numero da nota
 *
 */
function insereComprasNotaFiscalFornecedorOrdem($inCodNota)
{
    $obComprasNotaFiscalFornecedorOrdem = new TComprasNotaFiscalFornecedorOrdem();

    $obComprasNotaFiscalFornecedorOrdem->setDado('cgm_fornecedor', $_REQUEST['inCodFornecedor']);
    $obComprasNotaFiscalFornecedorOrdem->setDado('cod_entidade', $_REQUEST['inCodEntidade']);
    $obComprasNotaFiscalFornecedorOrdem->setDado('cod_nota', $inCodNota );
    $obComprasNotaFiscalFornecedorOrdem->setDado('exercicio', $_REQUEST['stExercicio']);
    $obComprasNotaFiscalFornecedorOrdem->setDado('tipo', 'C');
    $obComprasNotaFiscalFornecedorOrdem->setDado('cod_ordem', $_REQUEST['inOrdemCompra']);
    $obComprasNotaFiscalFornecedorOrdem->inclusao($boTransacao);
}

/**
 *
 * Faz a inclusão na tabela almoxarifado.catalogo_item_marca
 *
 * @param array $valor - recebe um array com os valores do item de entrada
 *
 */
function insereAlmoxarifadoCatalogoItemMarca($valor)
{
    $obAlmoxarifadoCatalogoItemMarca = new TAlmoxarifadoCatalogoItemMarca();
    $obAlmoxarifadoCatalogoItemMarca->setDado('cod_item', $valor['inCodItem']);
    $obAlmoxarifadoCatalogoItemMarca->setDado('cod_marca', $valor['inCodMarca']);
    $obAlmoxarifadoCatalogoItemMarca->recuperaPorChave( $rsAlmoxarifadoCatalogoItemMarca, $boTransacao );
    if ($rsAlmoxarifadoCatalogoItemMarca->getNumLinhas() <= 0 ) {
        $obAlmoxarifadoCatalogoItemMarca->inclusao($boTransacao);
    }
}

/**
 *
 * Faz a inclusão na tabela almoxarifado.estoque_material
 *
 * @param array $valor - recebe um array com os valores do item de entrada
 *
 */
function insereAlmoxarifadoEstoqueMaterial($valor)
{
    $obAlmoxarifadoEstoqueMaterial = new TAlmoxarifadoEstoqueMaterial();
    $obAlmoxarifadoEstoqueMaterial->setDado('cod_item', $valor['inCodItem']);
    $obAlmoxarifadoEstoqueMaterial->setDado('cod_marca', $valor['inCodMarca']);
    $obAlmoxarifadoEstoqueMaterial->setDado('cod_almoxarifado', $valor['inCodAlmoxarifado']);
    $obAlmoxarifadoEstoqueMaterial->setDado('cod_centro', $valor['inCodCentroCusto']);
    $obAlmoxarifadoEstoqueMaterial->recuperaPorChave( $rsAlmoxarifadoEstoqueMaterial, $boTransacao );
    if ($rsAlmoxarifadoEstoqueMaterial->getNumLinhas() <= 0) {
        $obAlmoxarifadoEstoqueMaterial->inclusao($boTransacao);
    }
}

/**
 *
 * Faz a inclusão na tabela almoxarifado.lancamento_material e retorna o código do lancamento
 *
 * @param array $valor - recebe um array com os valores do item de entrada
 * @param int $quantidade - recebe a quantidade de itens
 *
 */
function insereAlmoxarifadoLancamentoMaterial($inNumLancamento, $valor, $quantidade)
{
    $obAlmoxarifadoLancamentoMaterial = new TAlmoxarifadoLancamentoMaterial();
    $obAlmoxarifadoLancamentoMaterial->setDado('cod_item', $valor['inCodItem']);
    $obAlmoxarifadoLancamentoMaterial->setDado('cod_marca', $valor['inCodMarca']);
    $obAlmoxarifadoLancamentoMaterial->setDado('cod_almoxarifado', $valor['inCodAlmoxarifado']);
    $obAlmoxarifadoLancamentoMaterial->setDado('cod_centro', $valor['inCodCentroCusto']);
    $obAlmoxarifadoLancamentoMaterial->proximoCod($inCodLancamento, $boTransacao);
    $obAlmoxarifadoLancamentoMaterial->setDado('cod_lancamento', $inCodLancamento);
    $obAlmoxarifadoLancamentoMaterial->setDado('exercicio_lancamento', Sessao::getExercicio());
    $obAlmoxarifadoLancamentoMaterial->setDado('num_lancamento', $inNumLancamento);
    $obAlmoxarifadoLancamentoMaterial->setDado('cod_natureza', 1);
    $obAlmoxarifadoLancamentoMaterial->setDado('tipo_natureza', 'E');
    $obAlmoxarifadoLancamentoMaterial->setDado('quantidade', $quantidade);
    $obAlmoxarifadoLancamentoMaterial->setDado('complemento', $valor['stComplemento']);
    $obAlmoxarifadoLancamentoMaterial->setDado('valor_mercado', $valor['vlTotalItem']);
    $obAlmoxarifadoLancamentoMaterial->inclusao($boTransacao);

    return $inCodLancamento;
}

/**
 *
 * Faz a inclusão na tabela almoxarifado.lancamento_ordem
 *
 * @param int $inCodLancamento - recebe o código do lancamento
 * @param array $valor - recebe um array com os valores do item de entrada
 *
 */
function insereAlmoxarifadoLancamentoOrdem($inCodLancamento, $valor)
{
    $arItens = Sessao::read('arItens');
    foreach($arItens AS $key => $campo){
        if($campo['cod_ordem'] == $valor['inOrdemCompra'] && $campo['num_item'] == $valor['inNumItem'] ){
            $stExercicioPreEmpenho = $campo['exercicio_empenho'];
            $inCodPreEmpenho       = $campo['cod_pre_empenho'];
        }
    }

    $obTAlmoxarifadoLancamentoOrdem = new TAlmoxarifadoLancamentoOrdem();
    $obTAlmoxarifadoLancamentoOrdem->setDado('cod_lancamento'  , $inCodLancamento            );#lancamento_material
    $obTAlmoxarifadoLancamentoOrdem->setDado('cod_item'        , $valor['inCodItem']         );#lancamento_material
    $obTAlmoxarifadoLancamentoOrdem->setDado('cod_marca'       , $valor['inCodMarca']        );#lancamento_material
    $obTAlmoxarifadoLancamentoOrdem->setDado('cod_almoxarifado', $valor['inCodAlmoxarifado'] );#lancamento_material
    $obTAlmoxarifadoLancamentoOrdem->setDado('cod_centro'      , $valor['inCodCentroCusto']  );#lancamento_material

    $obTAlmoxarifadoLancamentoOrdem->setDado('exercicio'            , $valor['stExercicio']   );#ordem_item
    $obTAlmoxarifadoLancamentoOrdem->setDado('cod_entidade'         , $valor['inCodEntidade'] );#ordem_item
    $obTAlmoxarifadoLancamentoOrdem->setDado('cod_ordem'            , $valor['inOrdemCompra'] );#ordem_item
    $obTAlmoxarifadoLancamentoOrdem->setDado('tipo'                 , 'C'                     );#ordem_item
    $obTAlmoxarifadoLancamentoOrdem->setDado('cod_pre_empenho'      , $inCodPreEmpenho        );#ordem_item
    $obTAlmoxarifadoLancamentoOrdem->setDado('exercicio_pre_empenho', $stExercicioPreEmpenho  );#ordem_item
    $obTAlmoxarifadoLancamentoOrdem->setDado('num_item'             , $valor['inNumItem']     );#ordem_item

    $obTAlmoxarifadoLancamentoOrdem->inclusao($boTransacao);
}

/**
 *
 * Faz a inclusão na tabela almoxarifado.lancamento_perecivel
 *
 * @param int $inCodLancamento - recebe o código do lancamento
 * @param array $valor - recebe um array com os valores do item de entrada
 * @param int $inNumLotePerecivel - recebe o numero do lote do item perecivel
 *
 */
function insereAlmoxarifadoLancamentoPerecivel($inCodLancamento, $valor, $inNumLotePerecivel)
{
    $obAlmoxarifadoLancamentoPerecivel = new TAlmoxarifadoLancamentoPerecivel();
    $obAlmoxarifadoLancamentoPerecivel->setDado('cod_lancamento', $inCodLancamento);
    $obAlmoxarifadoLancamentoPerecivel->setDado('cod_item', $valor['inCodItem']);
    $obAlmoxarifadoLancamentoPerecivel->setDado('cod_marca', $valor['inCodMarca']);
    $obAlmoxarifadoLancamentoPerecivel->setDado('cod_almoxarifado', $valor['inCodAlmoxarifado']);
    $obAlmoxarifadoLancamentoPerecivel->setDado('cod_centro', $valor['inCodCentroCusto']);
    $obAlmoxarifadoLancamentoPerecivel->setDado('lote', $inNumLotePerecivel);
    $obAlmoxarifadoLancamentoPerecivel->inclusao($boTransacao);
}

/**
 *
 * Faz a inclusão na tabela almoxarifado.perecivel
 *
 * @param array $valor - recebe um array com os valores do item de entrada
 * @param array $vlrPerecivel - recebe um array com os valores do dados perecivel do item
 *
 */
function insereAlmoxarifadoPerecivel($valor, $vlrPerecivel)
{
    $obAlmoxarifadoPerecivel = new TAlmoxarifadoPerecivel();
    $obAlmoxarifadoPerecivel->setDado('lote', $vlrPerecivel['inNumLotePerecivel']);
    $obAlmoxarifadoPerecivel->setDado('cod_item', $valor['inCodItem']);
    $obAlmoxarifadoPerecivel->setDado('cod_marca', $valor['inCodMarca']);
    $obAlmoxarifadoPerecivel->setDado('cod_almoxarifado', $valor['inCodAlmoxarifado']);
    $obAlmoxarifadoPerecivel->setDado('cod_centro', $valor['inCodCentroCusto']);
    $obAlmoxarifadoPerecivel->setDado('dt_fabricacao', $vlrPerecivel['inNumLotePerecivel']);
    $obAlmoxarifadoPerecivel->setDado('dt_validade', $vlrPerecivel['dtValidadePerecivel']);
    $obAlmoxarifadoPerecivel->setDado('dt_fabricacao', $vlrPerecivel['dtFabricacaoPerecivel']);
    $obAlmoxarifadoPerecivel->recuperaPorChave($rsPerecivel, $boTransacao);
    if ( !$rsPerecivel->eof() ) {
        $obAlmoxarifadoPerecivel->alteracao($boTransacao);
    } else {
        $obAlmoxarifadoPerecivel->inclusao($boTransacao);
    }
}

$obErro = new Erro;

switch ($stAcao) {

case "incluir":
    $stRetorno = validaEntradaPorOrdemCompra();

    $obTransacao = new Transacao;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if (!$obErro->ocorreu()) {

        if ( empty($stRetorno) ) {
    
            // Faz a inserção na tabela almoxarifado.natureza_lancamento
            // Entrada por Ordem de Compra pela tabela almoxarifado.natureza
            // é representada por 'tipo_natureza = E e cod_Natureza = 1'
    
            $obTAlmoxarifadoNaturezaLancamento = new TAlmoxarifadoNaturezaLancamento;
            $obTAlmoxarifadoNaturezaLancamento->setDado('tipo_natureza' , 'E');
            $obTAlmoxarifadoNaturezaLancamento->setDado('cod_natureza'  , 1  );
    
            # Recupera o num_lancamento considerando as configurações do Almoxarifado.
            $obTAlmoxarifadoNaturezaLancamento->recuperaNumNaturezaLancamento($rsNumLancamento,"","",$boTransacao);
    
            $inNumLancamento = $rsNumLancamento->getCampo('num_lancamento');
    
            $obTAlmoxarifadoNaturezaLancamento->setDado('num_lancamento' , $inNumLancamento);
            $obTAlmoxarifadoNaturezaLancamento->setDado('cgm_almoxafire' , Sessao::read('numCgm'));
            $obTAlmoxarifadoNaturezaLancamento->setDado('numcgm_usuario' , Sessao::read('numCgm'));
    
            $obErro = $obTAlmoxarifadoNaturezaLancamento->inclusao($boTransacao);

            if (!$obErro->ocorreu()) {
    
                // Faz a inclusão na tabela compras.nota_fiscal_fornecedor
                insereComprasNotaFiscalFornecedor( $inNumLancamento );
        
                $arItensEntrada = Sessao::read('arItensEntrada');
        
                $arBens = array();
        
                foreach ($arItensEntrada as $chave => $valor) {

                    if (!$obErro->ocorreu()) {
                        // Verifica se há dados na tabela almoxarifado.catalogo_item_marca
                        // relacionados com o item e a marca, caso não tenha, insere na tabela
                        insereAlmoxarifadoCatalogoItemMarca($valor);
            
                        // Faz a inclusão na tabela almoxarifado.estoque_material
                        insereAlmoxarifadoEstoqueMaterial($valor);
            
                        $arItensPerecivel = Sessao::read('arItensPerecivel');
            
                        if (isset($arItensPerecivel[$valor['inCodItem']])) {
                            foreach ($arItensPerecivel[$valor['inCodItem']] as $chv => $vlrPerecivel) {
                                // Faz a inclusão na tabela almoxarifado.lancamento_material
                                // e retorna o código do lancamento para a variável que está recebendo o método
                                $valorPerecivel = $valor;
            
                                $vlUnitarioItem = str_replace('.',  '', $valorPerecivel['flValorTotalMercado'] );
                                $vlUnitarioItem = str_replace(",", ".", $vlUnitarioItem );
                                $vlUnitarioItem = number_format($vlUnitarioItem, 2, ".", "");
            
                                $inQtdePerecivel = str_replace('.',  '', $vlrPerecivel['inQtdePerecivel'] );
                                $inQtdePerecivel = str_replace(",", ".", $inQtdePerecivel );
                                $inQtdePerecivel = number_format($inQtdePerecivel, 4, ".", "");
            
                                $vlTotalItem = number_format(($vlUnitarioItem * $inQtdePerecivel), 2, ",", ".");
            
                                $valorPerecivel['vlTotalItem']   = $vlTotalItem;
                                $valorPerecivel['inQtdeEntrada'] = $vlrPerecivel['inQtdePerecivel'];
            
                                $inCodLancamento = insereAlmoxarifadoLancamentoMaterial($inNumLancamento, $valorPerecivel, $vlrPerecivel['inQtdePerecivel']);
            
                                // Faz a inclusão na tabela almoxarifado.perecivel
                                insereAlmoxarifadoPerecivel($valor, $vlrPerecivel);
            
                                // Faz a inclusão na tabela almoxarifado.lancamento_perecivel
                                insereAlmoxarifadoLancamentoPerecivel($inCodLancamento, $valor, $vlrPerecivel['inNumLotePerecivel']);
                                
                                // Faz a inclusão na tabela almoxarifado.lancamento_ordem
                                insereAlmoxarifadoLancamentoOrdem($inCodLancamento, $valor);
                            }
                        } else {
                            // Faz a inclusão na tabela almoxarifado.lancamento_material
                            // e retorna o código do lancamento para a variável que está recebendo o método
                            $inCodLancamento = insereAlmoxarifadoLancamentoMaterial($inNumLancamento, $valor, $valor['inQtdeEntrada']);
            
                            if ($valor['inCodTipoItem'] == 4) {
                                inserirBemPatrimonial($valor, $inCodLancamento, $arBens);
                            }
            
                            // Faz a inclusão na tabela almoxarifado.lancamento_ordem
                            insereAlmoxarifadoLancamentoOrdem($inCodLancamento, $valor);
                        }
            
                        $arItensAtributo = Sessao::read('arItensAtributo');
            
                        if (isset($arItensAtributo[$valor['inCodItem']])) {
                            foreach ($arItensAtributo[$valor['inCodItem']] as $chv => $vlrAtributo) {
                                // Faz a inclusão na tabela almoxarifado.atributo_estoque_material_valor
                                $obAlmoxarifadoAtributoEstoqueMaterialValor = new TAlmoxarifadoAtributoEstoqueMaterialValor();
                                $obAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_modulo', $vlrAtributo['inCodModulo']);
                                $obAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_cadastro', $vlrAtributo['inCodCadastro']);
                                $obAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_atributo', $vlrAtributo['inCodAtributo']);
                                $obAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_item', $valor['inCodItem']);
                                $obAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_centro', $valor['inCodCentroCusto']);
                                $obAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_almoxarifado', $valor['inCodAlmoxarifado']);
                                $obAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_marca', $valor['inCodMarca']);
                                $obAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_lancamento', $inCodLancamento);
                                $obAlmoxarifadoAtributoEstoqueMaterialValor->setDado('valor', $vlrAtributo['stValor']);
                                $obErro = $obAlmoxarifadoAtributoEstoqueMaterialValor->inclusao($boTransacao);
                            }
                        }
                    }
                }

                if (!$obErro->ocorreu()) {
        
                    if (count($arBens) == 1) {
                        $stDescItem = SistemaLegado::pegaDado('descricao', 'patrimonio.bem', 'WHERE cod_bem = '.$arBens[0]);
                        $stMsgBem = "Bem de Patrimônio incluso (".$arBens[0]." - ".trim($stDescItem).").";
                    } elseif (count($arBens) > 1) {
                        $stMsgBem = "Bens de Patrimônio inclusos: ".$arBens[0]." à ".$arBens[count($arBens) - 1].".";
                    }

                    SistemaLegado::alertaAviso($pgRel.'?'.Sessao::getId()."&stAcao=".$stAcao."&inNumLancamento=".$inNumLancamento."&inCodNatureza=1&inCodOrdem=".$_REQUEST['inOrdemCompra'],"Entrada por Ordem de Compra ".$_REQUEST['inOrdemCompra']."/".$_REQUEST['stExercicio']." finalizada com sucesso. ".$stMsgBem."", "aviso", Sessao::getId(), "../");
                }
            }
        } else {
            SistemaLegado::exibeAviso($stRetorno, 'form', 'erro', Sessao::getId() );
        }
    }

    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obAlmoxarifadoAtributoEstoqueMaterialValor );

    break;
}



function verificaItensEmInventarioNaoProcessado($arItemVerificacao)
{
    $obTAlmoxarifadoInventario = new TAlmoxarifadoInventarioItens;

    $obTAlmoxarifadoInventario->setDado('cod_item', $arItemVerificacao['inCodItem']);
    $obTAlmoxarifadoInventario->setDado('cod_almoxarifado', $arItemVerificacao['inCodAlmoxarifado']);
    $obTAlmoxarifadoInventario->setDado('exercicio', Sessao::getExercicio());
    $obTAlmoxarifadoInventario->setDado('cod_marca',$arItemVerificacao['inCodMarca'] );
    $obTAlmoxarifadoInventario->setDado('cod_centro',$arItemVerificacao['inCodCentroCusto'] );

    $obTAlmoxarifadoInventario->verificaItensInventarioNaoProcessado($rsItensInventario);

    if ($rsItensInventario->getNumLinhas()>0) {
        $boIncluir = false;
        SistemaLegado::exibeAviso('O item '.$rsItensInventario->getCampo('cod_item').'-'.$rsItensInventario->getCampo('descricao').' não pode ser utilizado pois está em processo de inventário.','form','erro',Sessao::getId() );
        exit;
    }
}

function inserirBemPatrimonial($arItens, $codLancamento, &$arBens)
{
    $obTPatrimonioBem = new TPatrimonioBem;
    $TAlmoxarifadoLancamentoBem = new TAlmoxarifadoLancamentoBem;

    $obTAlmoxarifadoCatalagoItem = new TAlmoxarifadoCatalogoItem;
    $obTAlmoxarifadoCatalagoItem->setDado('cod_item', $arItens['inCodItem']);
    $obTAlmoxarifadoCatalagoItem->recuperaPorChave($rsCatalogoItem, $boTransacao);

    $detalhamento = SistemaLegado::pegaDado("descricao","almoxarifado.catalogo_item", "where cod_item=".$arItens['inCodItem']);

    if ($arItens['stPlacaIdentificacao'] != 'nao') {
        $identificacao = 't';
        $indice = 0;

        $numeroPlaca = $arItens['stNumeroPlaca'];

        for ($i = 0; $indice < $arItens['inQtdeEntrada']; $i++) {
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

    $inValorItem = str_replace(',','.',str_replace('.','',$arItens['vlTotalItem']));
    $inQuantidade = str_replace(',','.',str_replace('.','',$arItens['inQtdeEntrada']));

    $ValorBem = $inValorItem/$inQuantidade;

    for ($i = 0; $i<$arItens['inQtdeEntrada'];$i++) {
        $obTPatrimonioBem->proximoCod( $inCodBem );

        $obTPatrimonioBem->setDado( 'cod_bem'      , $inCodBem );
        $obTPatrimonioBem->setDado( 'cod_natureza' , 0 );
        $obTPatrimonioBem->setDado( 'cod_grupo'    , 0 );
        $obTPatrimonioBem->setDado( 'cod_especie'  , 0 );
        $obTPatrimonioBem->setDado( 'numcgm'       , $_REQUEST['inCodFornecedor'] );
        $obTPatrimonioBem->setDado( 'descricao'    , $rsCatalogoItem->getCampo('descricao_resumida'));
        $obTPatrimonioBem->setDado( 'detalhamento' , $rsCatalogoItem->getCampo('descricao'));
        $obTPatrimonioBem->setDado( 'dt_aquisicao' , date('d/m/Y'));

        $obTPatrimonioBem->setDado( 'vl_bem', $ValorBem );
        $obTPatrimonioBem->setDado( 'vl_depreciacao', 0.00);

        $obTPatrimonioBem->setDado( 'identificacao', $identificacao );

        if ($identificacao != 'f') {
            $obTPatrimonioBem->setDado( 'num_placa', $arrayPlacas[$i] );
        } else {
            $obTPatrimonioBem->setDado( 'num_placa', null );
        }
        $obTPatrimonioBem->inclusao($boTransacao);

        $TAlmoxarifadoLancamentoBem->setDado('cod_lancamento',$codLancamento);
        $TAlmoxarifadoLancamentoBem->setDado('cod_item',$arItens['inCodItem']);
        $TAlmoxarifadoLancamentoBem->setDado('cod_marca',$arItens['inCodMarca']);
        $TAlmoxarifadoLancamentoBem->setDado('cod_almoxarifado',$arItens['inCodAlmoxarifado']);
        $TAlmoxarifadoLancamentoBem->setDado('cod_centro',$arItens['inCodCentroCusto']);
        $TAlmoxarifadoLancamentoBem->setDado('cod_bem',$inCodBem);
        $TAlmoxarifadoLancamentoBem->inclusao($boTransacao);

        # Armazena o cod_bem e descricao do item no array
        $arBens[] = $inCodBem;
    }

    return $arBens;
}

?>
