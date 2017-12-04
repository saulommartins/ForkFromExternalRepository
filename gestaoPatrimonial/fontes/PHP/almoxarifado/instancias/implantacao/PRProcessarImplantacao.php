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
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    $Id: PRProcessarImplantacao.php 59612 2014-09-02 12:00:51Z gelson $

    Casos de uso: uc-03.03.16
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(TALM."TAlmoxarifadoNaturezaLancamento.class.php"     );
include_once(TALM."TAlmoxarifadoLancamentoMaterial.class.php"     );
include_once(TALM."TAlmoxarifadoEstoqueMaterial.class.php"        );
include_once(TALM."TAlmoxarifadoCatalogoItemMarca.class.php"      );
include_once(TALM."TAlmoxarifadoCatalogoItem.class.php"           );
include_once(TALM."TAlmoxarifadoLancamentoPerecivel.class.php"    );
include_once(TALM."TAlmoxarifadoPerecivel.class.php"              );
include_once(CAM_GP_ALM_NEGOCIO."RAlmoxarifadoCentroDeCustos.class.php"              );

include_once(CAM_GP_ALM_NEGOCIO."RAlmoxarifadoCatalogoItem.class.php"      );
include_once( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoInventarioItens.class.php"   						);

$stAcao = $request->get('stAcao');

$stPrograma = "ProcessarImplantacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

Sessao::setTrataExcecao( true );

//valida entrada de dados feita pelo usuário!
validaDadosDigitados();

//verifica se o item que esta sendo implantado ja não esta em um inventario não processado
verificaItensEmInventarioNaoProcessado();

//verifica se o usuário tem permissões ativadas naquele centro de custo para poder utiliza-lo
verificaPermissãoCentroCusto();

$obTAlmoxarifadoNaturezaLancamento = new TAlmoxarifadoNaturezaLancamento;
$obTAlmoxarifadoLancamentoMaterial = new TAlmoxarifadoLancamentoMaterial;
$obTAlmoxarifadoEstoqueMaterial = new TAlmoxarifadoEstoqueMaterial;
$obTAlmoxarifadoCatalogoItemMarca = new TAlmoxarifadoCatalogoItemMarca;
$obTAlmoxarifadoLancamentoPerecivel = new TAlmoxarifadoLancamentoPerecivel;
$obTAlmoxarifadoPerecivel = new TAlmoxarifadoPerecivel;

Sessao::getTransacao()->setMapeamento( $obTAlmoxarifadoNaturezaLancamento );
Sessao::getTransacao()->setMapeamento( $obTAlmoxarifadoLancamentoMaterial );
Sessao::getTransacao()->setMapeamento( $obTAlmoxarifadoEstoqueMaterial );
Sessao::getTransacao()->setMapeamento( $obTAlmoxarifadoCatalogoItemMarca );
Sessao::getTransacao()->setMapeamento( $obTAlmoxarifadoCatalogoItemMarca );
Sessao::getTransacao()->setMapeamento( $obTAlmoxarifadoLancamentoPerecivel );
Sessao::getTransacao()->setMapeamento( $obTAlmoxarifadoPerecivel );

$obTAlmoxarifadoLancamentoMaterial->obTAlmoxarifadoEstoqueMaterial = &$obTAlmoxarifadoEstoqueMaterial;
$obTAlmoxarifadoLancamentoMaterial->obTAlmoxarifadoEstoqueMaterial->obTAlmoxarifadoCatalogoItemMarca = &$obTAlmoxarifadoCatalogoItemMarca;
$obTAlmoxarifadoPerecivel->obTAlmoxarifadoEstoqueMaterial = &$obTAlmoxarifadoEstoqueMaterial;
$obTAlmoxarifadoLancamentoPerecivel->obTAlmoxarifadoPerecivel = &$obTAlmoxarifadoPerecivel;
$obTAlmoxarifadoLancamentoPerecivel->obTAlmoxarifadoLancamentoMaterial = &$obTAlmoxarifadoLancamentoMaterial;

/*
 *Verifica se o usuário tem permissão para fazer implantações no centro de custo selecionado
 *
 *@return void
 *
 */
function verificaPermissãoCentroCusto()
{
    $obAlmoxarifadoCentroDeCustos = new RAlmoxarifadoCentroDeCustos;
    $obAlmoxarifadoCentroDeCustos->setCodigo($_REQUEST['inCodCentroCusto']);
    $obAlmoxarifadoCentroDeCustos->listarPermissaoUsuario($rsPermissoes);

    if ($rsPermissoes->getNumLinhas() < 1) {
        SistemaLegado::exibeAviso('Você não possui permissão para esse centro de custo ('.$_REQUEST['inCodCentroCusto'].')',"n_incluir", "erro",Sessao::getId() );
        Sessao::encerraExcecao();
        exit;
    }
}

/*
 * Altera a unidade se o cadastro do ítem estiver com Unidade de Medida igual a Não informada.
 * O novo valor foi informado pelo usuário.
 */
function alteraCadastroItem($inCodItem, $stUnidadeMedida, $inCodTipo)
{

    $inCodigosUnidadeMedida = explode( '-', $stUnidadeMedida );

    $obRCatalogoItem = new RAlmoxarifadoCatalogoItem;
    $obRCatalogoItem->setCodigo( $inCodItem );
    $obRCatalogoItem->consultar();

    $TCatalogoItem = new TAlmoxarifadoCatalogoItem;
    $TCatalogoItem->setDado( 'cod_item'          , $inCodItem );
    $TCatalogoItem->setDado( 'cod_catalogo'      , $obRCatalogoItem->obRAlmoxarifadoClassificacao->obRAlmoxarifadoCatalogo->getCodigo() );
    $TCatalogoItem->setDado( 'cod_classificacao' , $obRCatalogoItem->obRAlmoxarifadoClassificacao->getCodigo() );
    $TCatalogoItem->setDado( 'cod_tipo'          , $inCodTipo );
    $TCatalogoItem->setDado( 'cod_unidade'       , $inCodigosUnidadeMedida[0] );
    $TCatalogoItem->setDado( 'cod_grandeza'      , $inCodigosUnidadeMedida[1] );
    $TCatalogoItem->setDado( 'descricao'         , $obRCatalogoItem->getDescricao() );
    $TCatalogoItem->setDado( 'descricao_resumida', $obRCatalogoItem->getDescricaoResumida() );
    $TCatalogoItem->setDado( 'ativo'             , $obRCatalogoItem->getAtivo() );
    $TCatalogoItem->alteracao();
}

function validaDadosDigitados()
{
    if ($_REQUEST['inCodAlmoxarifado'] == "") {
        SistemaLegado::exibeAviso('Deve ser selecionado um almoxarifado!','form','erro',Sessao::getId() );
        Sessao::encerraExcecao();
        exit;
    }

    if ( !$_REQUEST['nuVlTotal'] || ( $_REQUEST['nuVlTotal'] == '0,0000' ) ) {
        $stMensagem = "<b><i>Valor de mercado</i></b> deve ser maior que zero.";
        SistemaLegado::exibeAviso(urlencode($stMensagem), "n_incluir", "erro" );
        Sessao::encerraExcecao();
        exit;
    }

    if ( !$_REQUEST['nuQuantidade'] || ( $_REQUEST['nuQuantidade'] == '0,0000' ) || ( $_REQUEST['nuQuantidade'] == 0 )) {
        $stMensagem = "<b><i>Quantidade</i></b> deve ser maior que zero.";
        SistemaLegado::exibeAviso(urlencode($stMensagem), "n_incluir", "erro" );
        Sessao::encerraExcecao();
        exit;
    }
}

function verificaItensEmInventarioNaoProcessado()
{
    $obTAlmoxarifadoInventario = new TAlmoxarifadoInventarioItens;

    $obTAlmoxarifadoInventario->setDado('cod_item', $_REQUEST['inCodItem']);
    $obTAlmoxarifadoInventario->setDado('cod_almoxarifado', $_REQUEST['inCodAlmoxarifado']);
    $obTAlmoxarifadoInventario->setDado('exercicio', Sessao::getExercicio());
    $obTAlmoxarifadoInventario->setDado('cod_marca',$_REQUEST['inCodMarca'] );
    $obTAlmoxarifadoInventario->setDado('cod_centro',$_REQUEST['inCodCentroCusto'] );

    $obTAlmoxarifadoInventario->verificaItensInventarioNaoProcessado($rsItensInventario);

    if ($rsItensInventario->getNumLinhas()>0) {
        $boIncluir = false;
        SistemaLegado::exibeAviso('O item '.$rsItensInventario->getCampo('cod_item').'-'.$rsItensInventario->getCampo('descricao').' não pode ser utilizado pois está em processo de inventário.','form','erro',Sessao::getId() );
        Sessao::encerraExcecao();
        exit;
    }
}

function verficaItemEntradaMovimentacao()
{
    $boIncluirImplantacao = true;
    $arAtributos = Sessao::read('IMontaAtributosEntradaValores');

    include_once( TALM."TAlmoxarifadoLancamentoMaterial.class.php" );
    $obTAlmoxarifadoLancamentoMaterial = new TAlmoxarifadoLancamentoMaterial;
    $obTAlmoxarifadoLancamentoMaterial->setDado( 'cod_almoxarifado', $_REQUEST['inCodAlmoxarifado'] );
    $obTAlmoxarifadoLancamentoMaterial->setDado( 'cod_item'        , $_REQUEST['inCodItem'] );
    $obTAlmoxarifadoLancamentoMaterial->setDado( 'cod_marca'       , $_REQUEST['inCodMarca'] );
    $obTAlmoxarifadoLancamentoMaterial->setDado( 'cod_centro'      , $_REQUEST['inCodCentroCusto'] );
    $obTAlmoxarifadoLancamentoMaterial->recuperaVerificaAtributosLancamento($rsLancamentos);

    if ( $rsLancamentos->getNumLinhas() > 0 ) {
        return false;
    }

    return $boIncluirImplantacao;
}

$arAtributos = Sessao::read('IMontaAtributosEntradaValores');
$arrayLotes = Sessao::read('lotes');

switch ($stAcao) {
    default:

        $stFiltro = "";
        $stMensagem = "";
        $ids="";
        $rsRecordSet = new RecordSet;

        if (verficaItemEntradaMovimentacao() == true) {

            $obTAlmoxarifadoNaturezaLancamento->setDado('tipo_natureza','E');
            $obTAlmoxarifadoNaturezaLancamento->setDado('cod_natureza' , 6 );
            $obTAlmoxarifadoNaturezaLancamento->setDado('exercicio_lancamento', Sessao::getExercicio() );
            $obTAlmoxarifadoNaturezaLancamento->proximoCod( $inNumLancamento );
            $obTAlmoxarifadoNaturezaLancamento->setDado('num_lancamento', $inNumLancamento);
            $obTAlmoxarifadoNaturezaLancamento->recuperaNaturezaLancamento( $rsNatureza );
            if ( $rsNatureza->getNumLinhas() <= 0 ) {
                $obTAlmoxarifadoNaturezaLancamento->setDado('numcgm_usuario' , Sessao::read('numCgm'));
                $obTAlmoxarifadoNaturezaLancamento->inclusao();
                $inCodImplantacao = $obTAlmoxarifadoNaturezaLancamento->getDado( 'num_lancamento' );
            }

            if ($_REQUEST['inCodItem'] != "") {

                $obTAlmoxarifadoLancamentoMaterial->setDado('cod_item' ,$_REQUEST['inCodItem']  );
                $obTAlmoxarifadoLancamentoMaterial->setDado('cod_marca',$_REQUEST['inCodMarca'] );
                $obTAlmoxarifadoLancamentoMaterial->setDado('cod_almoxarifado',$_REQUEST['inCodAlmoxarifado'] );
                $obTAlmoxarifadoLancamentoMaterial->setDado('cod_centro',$_REQUEST['inCodCentroCusto'] );
                $obTAlmoxarifadoLancamentoMaterial->setDado('quantidade',$_REQUEST['nuQuantidade'] );
                $obTAlmoxarifadoLancamentoMaterial->obTAlmoxarifadoNaturezaLancamento = & $obTAlmoxarifadoNaturezaLancamento;

                // VERIFICACAO DA EXISTENCIA DA CHAVE ITEM,MARCA,ALMOXARIFADO,CENTRO NA TABELA ESTOQUE MATERIAL
                $obTAlmoxarifadoEstoqueMaterial->setDado('cod_item' ,$_REQUEST['inCodItem']  );
                $obTAlmoxarifadoEstoqueMaterial->setDado('cod_marca',$_REQUEST['inCodMarca'] );
                $obTAlmoxarifadoEstoqueMaterial->setDado('cod_almoxarifado',$_REQUEST['inCodAlmoxarifado'] );
                $obTAlmoxarifadoEstoqueMaterial->setDado('cod_centro',$_REQUEST['inCodCentroCusto'] );
                $obTAlmoxarifadoEstoqueMaterial->recuperaPorChave( $rsRecordSetMaterial );

                if ( $rsRecordSetMaterial->getNumLinhas() < 0 ) {
                    $obTAlmoxarifadoEstoqueMaterial->obTAlmoxarifadoCatalogoItemMarca->recuperaPorChave( $rsRecorSetItem );
                    if ( $rsRecorSetItem->getNumLinhas() < 0 ) {
                        $obTAlmoxarifadoLancamentoMaterial->obTAlmoxarifadoEstoqueMaterial->obTAlmoxarifadoCatalogoItemMarca->inclusao();
                    }
                    $obTAlmoxarifadoEstoqueMaterial->inclusao();
                }

                //perecivel
                if (count( $arrayLotes ) > 0 ) {

                    $quantidadeTotalLotes = str_replace(',','.', (str_replace('.','', $_REQUEST['nuQuantidade'])));

                    foreach ($arrayLotes as $keylote => $valuelote) {
                        $obTAlmoxarifadoPerecivel->setDado("lote", $valuelote["stNumLote"]);
                        $obTAlmoxarifadoPerecivel->setDado("dt_validade", $valuelote["stDataValidade"]);
                        $obTAlmoxarifadoPerecivel->setDado("dt_fabricacao", $valuelote["stDataFabricacao"]);

                        $filtro = " WHERE perecivel.lote       = '".$valuelote['stNumLote']."'";
                        $filtro.= "   AND perecivel.cod_centro = ".$_REQUEST['inCodCentroCusto'];
                        $filtro.= "   AND perecivel.cod_almoxarifado = ".$_REQUEST['inCodAlmoxarifado'];
                        $filtro.= "   AND perecivel.cod_item   = ".$_REQUEST['inCodItem'];
                        $filtro.= "   AND perecivel.cod_marca  = ".$_REQUEST['inCodMarca'];

                        $obTAlmoxarifadoPerecivel->recuperaTodos($rsPerecivel,$filtro);

                        if ($rsPerecivel->getNumLinhas()<=0) {
                           $obTAlmoxarifadoPerecivel->inclusao();
                        }
                        $obTAlmoxarifadoLancamentoMaterial->setDado( 'cod_lancamento', '');
                        $obTAlmoxarifadoLancamentoMaterial->setDado( 'quantidade', $valuelote["nmQuantidadeLote"]);

                        $valorTotalMercado = str_replace(',','.', (str_replace('.','', $_REQUEST['nuVlTotal'])));
                        $valorUnitario = ($valorTotalMercado/$quantidadeTotalLotes);
                        $valorMercado  = $valorUnitario * str_replace(',','.', (str_replace('.','',$valuelote["nmQuantidadeLote"])));
                        $valorMercado = number_format($valorMercado,4,',','.');

                        $obTAlmoxarifadoLancamentoMaterial->setDado( 'valor_mercado', $valorMercado );
                        $obTAlmoxarifadoLancamentoMaterial->inclusao();
                        $obTAlmoxarifadoLancamentoPerecivel->inclusao();

                    }

                } elseif ( count($arAtributos) > 0 ) {
                    //Começa Atributos

                    //Busca os atributos do item
                    include_once ( TALM."TAlmoxarifadoAtributoCatalogoItem.class.php" );
                    $obTAlmoxarifadoAtributoCatalogoItem = new TAlmoxarifadoAtributoCatalogoItem;
                    $obTAlmoxarifadoAtributoCatalogoItem->setDado( 'cod_item', $_REQUEST['inCodItem'] );
                    $obTAlmoxarifadoAtributoCatalogoItem->recuperaAtributoItemCatalogoItemSimples($rsAtributos);
                    $rsAtributos->ordena('cod_atributo');

                    $indiceAtributos = 0;
                    while (!$rsAtributos->eof()) {
                        $arAtributosInfo[$indiceAtributos]['cod_atributo'] = $rsAtributos->getCampo('cod_atributo');
                        $arAtributosInfo[$indiceAtributos]['cod_modulo'] = $rsAtributos->getCampo('cod_modulo');
                        $arAtributosInfo[$indiceAtributos]['cod_cadastro'] = $rsAtributos->getCampo('cod_cadastro');
                        $indiceAtributos++;
                        $rsAtributos->proximo();
                    }

                    foreach ($arAtributos as $chave =>$dados) {
                        $totalQuantidadeAtributos += $dados['nuQuantidadeAtributo'];
                    }

                    for ( $i=0; $i<count($arAtributos); $i++ ) {

                        //Inclui o lançamento para cada conjunto de atributos
                        $obTAlmoxarifadoLancamentoMaterial->proximoCod( $inProximo );
                        $obTAlmoxarifadoLancamentoMaterial->setDado("cod_lancamento", $inProximo);
                        $obTAlmoxarifadoLancamentoMaterial->setDado("quantidade", $arAtributos[$i]['nuQuantidadeAtributo']);

                        $valoresAtributos = $arAtributos[$i]['stValoresGrupo'];

                        $indiceAtributosInsert = 0;

                        foreach ($valoresAtributos as $atributoNomes => $valorAtributo) {
                            if ($valorAtributo != "") {
                                //Inclui os lançamentos dos atributos
                                include_once ( TALM."TAlmoxarifadoAtributoEstoqueMaterialValor.class.php" );
                                $obTAlmoxarifadoAtributoEstoqueMaterialValor = new TAlmoxarifadoAtributoEstoqueMaterialValor;
                                $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado( "cod_modulo", $arAtributosInfo[$indiceAtributosInsert]["cod_modulo"] );
                                $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado( "cod_cadastro", $arAtributosInfo[$indiceAtributosInsert]["cod_cadastro"] );
                                $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado( "cod_atributo", $arAtributosInfo[$indiceAtributosInsert]["cod_atributo"] );
                                $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado( "cod_item", $_REQUEST['inCodItem'] );
                                $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado( "cod_centro", $_REQUEST['inCodCentroCusto'] );
                                $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado( "cod_marca", $_REQUEST['inCodMarca'] );
                                $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado( "cod_lancamento", $obTAlmoxarifadoLancamentoMaterial->getDado("cod_lancamento") );
                                $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado( "cod_almoxarifado", $_REQUEST['inCodAlmoxarifado'] );
                                $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado( "valor", $valorAtributo );
                                $obTAlmoxarifadoAtributoEstoqueMaterialValor->inclusao();
                                $indiceAtributosInsert++;

                                $valorTotalMercado = str_replace(',','.', (str_replace('.','', $_REQUEST['nuVlTotal'])));
                                $valorUnitario = ($valorTotalMercado/$totalQuantidadeAtributos);
                                $valorMercado  = $valorUnitario * str_replace(',','.', (str_replace('.','',$arAtributos[$i]['nuQuantidadeAtributo'])));
                                $valorMercado = number_format($valorMercado,4,',','.');

                                $obTAlmoxarifadoLancamentoMaterial->setDado( 'valor_mercado', $valorMercado );

                                $obTAlmoxarifadoLancamentoMaterial->inclusao();

                            }
                        }
                    }
                    //Termina Atributos

                } else {
                    $obTAlmoxarifadoLancamentoMaterial->setDado( 'valor_mercado', $_REQUEST['nuVlTotal'] );
                    $obTAlmoxarifadoLancamentoMaterial->inclusao();
                }

            } else {
                $stMensagem = 'Deve ser selecionado um item';
            }
        } else {
            $stMensagem = "Este item já possui movimentação em estoque. (Item: ".$_REQUEST['inCodItem'].")";
        }

        if (!$stMensagem) {
            SistemaLegado::alertaAviso($pgForm.'?'.Sessao::getId()."&stAcao=$stAcao", "Processar Implantação concluída com sucesso.", "aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($stMensagem), "n_incluir", "erro" );
        }
    break;
}

Sessao::encerraExcecao();
