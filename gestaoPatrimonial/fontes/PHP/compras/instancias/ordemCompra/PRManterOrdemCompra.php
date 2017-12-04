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
    * Página de Processamento de Ordem de Compra
    * Data de Criação   : 06/12/2006

    * @author Analista: Cleisson Barboza
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @ignore

    $Id: PRManterOrdemCompra.php 66539 2016-09-13 20:54:08Z carlos.silva $
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once TCOM."TComprasOrdem.class.php";
require_once TCOM."TComprasOrdemItem.class.php";
require_once TCOM."TComprasOrdemAnulacao.class.php";
require_once TCOM."TComprasOrdemItemAnulacao.class.php";
include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoItemPreEmpenho.class.php";
include_once TALM."TAlmoxarifadoCatalogoItemMarca.class.php";
include_once CAM_GP_COM_MAPEAMENTO."TComprasCotacaoFornecedorItem.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterOrdemCompra";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";
$pgJS      = "JS".$stPrograma.".js";
$pgRel     = "OCGera".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao;

$obErro = new Erro();
$obTransacao = new Transacao();
$obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

$obTMapeamento = new TComprasOrdem();

$nomAcao = SistemaLegado::pegaDado("nom_acao","administracao.acao"," where cod_acao = ".Sessao::read('acao'), $boTransacao);

$arItensAlmoxarifado = is_array(Sessao::read('arItensAlmoxarifado')) ? Sessao::read('arItensAlmoxarifado') : array();

$stAcao = $request->get('stAcao');
$stTipoOrdem = ( strpos($stAcao,'OS')===false ) ? 'C' : 'S';
$stDesc = ($stTipoOrdem=='C') ? 'Compra' : 'Serviço';

// recebe true caso haja alguma qtde <= 0
$boErro = false;
// recebe o número da listagem dos itens que tiverem a qtde <= 0
$arItens = array();
$itemZerado = 0;
$i = 1;

if ( strpos($stAcao,"anular")===false && strpos($stAcao,"reemitir")===false ) {
    $arItens = Sessao::read('arItens');

    $arItensAlmoxarifado = is_array(Sessao::read('arItensAlmoxarifado')) ? Sessao::read('arItensAlmoxarifado') : array();

    foreach ($arItens as $chave =>$dados) {
        $inQtdItem = str_replace(',','.',str_replace('.','',$request->get('qtdeOC_'.$i)));

        if ($inQtdItem == 0) {
            $itemZerado++;
        }
        $i++;
    }

    if ($itemZerado == count($arItens)) {
        $boErro = true;
    }

    if ( count($arItens) < 1 ) {
       $boErro = true;
    }
}

/* Faz a validação para verificar se a quantidade é maior que zero */
if ($boErro == true && !$obErro->ocorreu()) {
    if ($itemZerado == count($arItens)) {
        $obErro->setDescricao("A quantidade de ao menos um item deve ser maior que zero!");
    } else {
        if (count($arItens) > 1) {
            $obErro->setDescricao("A qtde. dos itens ".implode(",", $arItens)." deve ser maior que zero");
        } elseif ( count($arItens) <= 0 ) {
            $obErro->setDescricao("Deve ser incluído pelo menos um item na lista.");
        }
    }
}

if(!$obErro->ocorreu()) {
    switch ($stAcao) {

    case "incluir":
    case "incluirOS":
        $stAcaoReduzido = "incluir";
        Sessao::write ('stIncluirAssinaturaUsuario', $request->get('stIncluirAssinaturaUsuario'));
        $stExercicioOrdemCompra = Sessao::getExercicio();

        $obTOrdemCompra = new TComprasOrdem();
        $obTOrdemCompra->setDado('exercicio_empenho'    , $request->get('stExercicioEmpenho')   );
        $obTOrdemCompra->setDado('cod_entidade'         , $request->get('inCodEntidade')        );
        $obTOrdemCompra->setDado('exercicio'            , Sessao::getExercicio()                );
        $obTOrdemCompra->setDado('tipo'                 , $stTipoOrdem                          );
        $obErro = $obTOrdemCompra->proximoCod($inCodOrdem, $boTransacao);

        if(!$obErro->ocorreu()) {
            $obTOrdemCompra->setDado('cod_ordem'            , $inCodOrdem                       );
            $obTOrdemCompra->setDado('cod_empenho'          , $request->get('inCodEmpenho')     );
            $obTOrdemCompra->setDado('observacao'           , $request->get('stObservacao')     );
            if($request->get('inEntrega'))
                $obTOrdemCompra->setDado('numcgm_entrega'   , $request->get('inEntrega')        );
            $obErro = $obTOrdemCompra->inclusao($boTransacao);

            $inCount = 1;
        }

        if(!$obErro->ocorreu()) {
            foreach ($arItens as $key => $value) {
                $inQuantidade = str_replace(',','.',str_replace('.','',$request->get('qtdeOC_'.$inCount)));

                if ($inQuantidade > 0) {
                    $obTOrdemCompraItem = new TComprasOrdemItem;
                    $obTOrdemCompraItem->setDado('exercicio'                , Sessao::getExercicio()                    );
                    $obTOrdemCompraItem->setDado('exercicio_pre_empenho'    , $request->get('stExercicioEmpenho')       );
                    $obTOrdemCompraItem->setDado('cod_entidade'             , $request->get('inCodEntidade')            );
                    $obTOrdemCompraItem->setDado('cod_ordem'                , $obTOrdemCompra->getDado('cod_ordem')     );
                    $obTOrdemCompraItem->setDado('num_item'                 , $value['num_item']                        );
                    $obTOrdemCompraItem->setDado('cod_pre_empenho'          , $value['cod_pre_empenho']                 );
                    $obTOrdemCompraItem->setDado('quantidade'               , $inQuantidade                             );
                    $obTOrdemCompraItem->setDado('tipo'                     , $stTipoOrdem                              );
                    $obTOrdemCompraItem->setDado('vl_total'                 , ($value['vl_unitario'] * $inQuantidade)   );
                    $obErro = $obTOrdemCompraItem->inclusao($boTransacao);

                    if(is_array($arItensAlmoxarifado[$value['num_item']]) && !$obErro->ocorreu()) {
                       
                        $obTEmpenhoItemPreEmpenho = new TEmpenhoItemPreEmpenho;
                        $obTAlmoxarifadoCatalogoItemMarca = new TAlmoxarifadoCatalogoItemMarca;

                        $obTEmpenhoItemPreEmpenho->setDado( "exercicio"         , $request->get('stExercicioEmpenho') );
                        $obTEmpenhoItemPreEmpenho->setDado( "cod_pre_empenho"   , $value['cod_pre_empenho']           );
                        $obTEmpenhoItemPreEmpenho->setDado( "num_item"          , $value['num_item']                  );

                        $obErro = $obTEmpenhoItemPreEmpenho->recuperaPorChave($rsItemPreEmpenho, $boTransacao);

                        if(!$obErro->ocorreu()) {
                            while (!$rsItemPreEmpenho->eof()) {
                                $obTEmpenhoItemPreEmpenho->setDado( "cod_unidade"       , $rsItemPreEmpenho->getCampo("cod_unidade")                    );
                                $obTEmpenhoItemPreEmpenho->setDado( "cod_grandeza"      , $rsItemPreEmpenho->getCampo("cod_grandeza")                   );
                                $obTEmpenhoItemPreEmpenho->setDado( "quantidade"        , $rsItemPreEmpenho->getCampo("quantidade")                     );
                                $obTEmpenhoItemPreEmpenho->setDado( "nom_unidade"       , $rsItemPreEmpenho->getCampo("nom_unidade")                    );
                                $obTEmpenhoItemPreEmpenho->setDado( "sigla_unidade"     , $rsItemPreEmpenho->getCampo("sigla_unidade")                  );
                                $obTEmpenhoItemPreEmpenho->setDado( "vl_total"          , $rsItemPreEmpenho->getCampo("vl_total")                       );
                                $obTEmpenhoItemPreEmpenho->setDado( "nom_item"          , $rsItemPreEmpenho->getCampo("nom_item")                       );
                                $obTEmpenhoItemPreEmpenho->setDado( "complemento"       , $rsItemPreEmpenho->getCampo("complemento")                    );
                                $obTEmpenhoItemPreEmpenho->setDado( "cod_centro"        , $arItensAlmoxarifado[$value['num_item']]['inCodCentroCusto']  );

                                /*
                                 *Ticket #22576, NÃO está efetuando o update na tabela empenho.item_pre_empenho->cod_item, pois foi definido
                                 *com o Gelson, que se o empenho não possui codigo de item, a melhor situação é incluir na tabela compras.ordem_item
                                 *E a verificação de cod_item, passa inicialmente a ser feita na tabela compras.ordem_item NÃO anulada.
                                 *Se a tabela empenho.item_pre_empenho já possui cod_item, a tabela compras.ordem_item utilizara o mesmo cod_item.
                                */
                                //$obTEmpenhoItemPreEmpenho->setDado( "cod_item"          , $value['cod_item']                                            );
                                $obErro = $obTEmpenhoItemPreEmpenho->alteracao($boTransacao);

                                $rsItemPreEmpenho->proximo();

                                if($obErro->ocorreu())
                                    break;
                            }
                            
                            $inCodMarca = $request->get('inMarca'.$value['num_item']);

                            if(empty($inCodMarca) OR ($inCodMarca === $arItensAlmoxarifado[$value['num_item']]['inMarca']) ) {
                                $inCodMarca = $arItensAlmoxarifado[$value['num_item']]['inMarca'];                                         
                            }
                            if ( !empty($value['cod_item']) && !$obErro->ocorreu() ){
                                if($arItensAlmoxarifado[$value['num_item']]['inMarca'] != null) {
                                    if($arItensAlmoxarifado[$value['num_item']]['inCodItem'] != null) {
                                        $stFiltro = " AND acim.cod_marca = ".$inCodMarca." AND acim.cod_item = ".$arItensAlmoxarifado[$value['num_item']]['inCodItem'];
                                        $obErro = $obTAlmoxarifadoCatalogoItemMarca->recuperaItemMarca($rsItemMarca, $stFiltro, "", $boTransacao);
                                        if ($rsItemMarca->getNumLinhas() < 1 && !$obErro->ocorreu()) {
                                            $obTAlmoxarifadoCatalogoItemMarca->setDado('cod_item'   , $arItensAlmoxarifado[$value['num_item']]['inCodItem'] );
                                            $obTAlmoxarifadoCatalogoItemMarca->setDado('cod_marca'  , $inCodMarca                                           );
                                            $obErro = $obTAlmoxarifadoCatalogoItemMarca->inclusao($boTransacao);
                                        }
                                    }
                                }
                            }

                            if(!$obErro->ocorreu()) {
                                $obTOrdemCompraItem->setDado('exercicio'            , Sessao::getExercicio()                                        );
                                $obTOrdemCompraItem->setDado('exercicio_pre_empenho', $request->get('stExercicioEmpenho')                           );
                                $obTOrdemCompraItem->setDado('cod_entidade'         , $request->get('inCodEntidade')                                );
                                $obTOrdemCompraItem->setDado('cod_ordem'            , $obTOrdemCompra->getDado('cod_ordem')                         );
                                $obTOrdemCompraItem->setDado('num_item'             , $value['num_item']                                            );
                                $obTOrdemCompraItem->setDado('cod_pre_empenho'      , $value['cod_pre_empenho']                                     );
                                $obTOrdemCompraItem->setDado('quantidade'           , $inQuantidade                                                 );
                                $obTOrdemCompraItem->setDado('tipo'                 , $stTipoOrdem                                                  );
                                $obTOrdemCompraItem->setDado('vl_total'             , ($value['vl_unitario'] * $inQuantidade)                       );
                                $obTOrdemCompraItem->setDado('cod_marca'            , $inCodMarca                                               );
                                $obTOrdemCompraItem->setDado('cod_item'             , $value['cod_item']                                            );
                                $obTOrdemCompraItem->setDado('cod_centro'           , $arItensAlmoxarifado[$value['num_item']]['inCodCentroCusto']  );
                        
                                $obErro = $obTOrdemCompraItem->alteracao($boTransacao);
                            }
                        }
                    }
                }
                $inCount++;

                if($obErro->ocorreu())
                    break;
            }
        }

        if(!$obErro->ocorreu()){
            SistemaLegado::alertaAviso($pgRel."&inCodEntidade=".$request->get('inCodEntidade')."&inCodOrdem=".$inCodOrdem."&stTipo=".$request->get('stTipo')."&stTipoOrdem=".$stTipoOrdem."&stExercicioOrdemCompra=".$stExercicioOrdemCompra,"Ordem de $stDesc - ".$obTOrdemCompra->getDado('cod_ordem'),"incluir","incluir_n", Sessao::getId(), "../");
        }
        $obTMapeamento = $obTOrdemCompra;
    break;

    case "alterar":
    case "alterarOS":
        $stAcaoReduzido = "alterar";
        Sessao::write ('stIncluirAssinaturaUsuario', $request->get('stIncluirAssinaturaUsuario'));

        // altera o campo observacao da tabela
        $obTOrdemCompra = new TComprasOrdem();
        $obTOrdemCompra->setDado('exercicio_empenho'    , $request->get('stExercicioEmpenho')       );
        $obTOrdemCompra->setDado('exercicio'            , $request->get('stExercicioOrdemCompra')   );
        $obTOrdemCompra->setDado('cod_entidade'         , $request->get('inCodEntidade')            );
        $obTOrdemCompra->setDado('cod_ordem'            , $request->get('inCodOrdemCompra')         );
        $obTOrdemCompra->setDado('exercicio_pre_empenho', $request->get('stExercicioEmpenho')       );
        $obTOrdemCompra->setDado('cod_empenho'          , $request->get('inCodEmpenho')             );
        $obTOrdemCompra->setDado('observacao'           , $request->get('stObservacao')             );
        $obTOrdemCompra->setDado('tipo'                 , $stTipoOrdem                              );
        if($request->get('inEntrega'))
            $obTOrdemCompra->setDado('numcgm_entrega'   , $request->get('inEntrega')                );
        $obErro = $obTOrdemCompra->alteracao($boTransacao);

        if(!$obErro->ocorreu()) {
            // exclui os dados para inseri-los novamente na tabela
            $obTOrdemCompraItem = new TComprasOrdemItem();
            $obTOrdemCompraItem->setDado('exercicio'            , $request->get('stExercicioOrdemCompra')   );
            $obTOrdemCompraItem->setDado('cod_entidade'         , $request->get('inCodEntidade')            );
            $obTOrdemCompraItem->setDado('cod_ordem'            , $request->get('inCodOrdemCompra')         );
            $obTOrdemCompraItem->setDado('exercicio_pre_empenho', $request->get('stExercicioEmpenho')       );
            $obTOrdemCompraItem->setDado('tipo'                 , $stTipoOrdem                              );

            $obErro = $obTOrdemCompraItem->recuperaPorChave($rsOrdemCompraItem, $boTransacao);

            if(!$obErro->ocorreu())
                $obErro = $obTOrdemCompraItem->exclusao($boTransacao);

            if(!$obErro->ocorreu()) {
                while (!$rsOrdemCompraItem->eof()) {
                    if(!is_null($rsOrdemCompraItem->getCampo("cod_item"))&&!is_null($rsOrdemCompraItem->getCampo("cod_marca"))){
                        $obTOrdemCompraItem = new TComprasOrdemItem();
                        $stFiltro  = ' WHERE cod_marca='.$rsOrdemCompraItem->getCampo("cod_marca");
                        $stFiltro .= '   AND cod_item='.$rsOrdemCompraItem->getCampo("cod_item");
                        $obErro = $obTOrdemCompraItem->recuperaTodos($rsCatalogoItemMarca, $stFiltro, "", $boTransacao);

                        if(!$obErro->ocorreu()) {
                            $obTComprasCotacaoFornecedorItem = new TComprasCotacaoFornecedorItem;
                            $obErro = $obTComprasCotacaoFornecedorItem->recuperaTodos( $rsCotacaoItemMarca, $stFiltro, "", $boTransacao );
                        }

                        if(!$obErro->ocorreu()) {
                            if($rsCatalogoItemMarca->getNumLinhas() < 1 && $rsCotacaoItemMarca->getNumLinhas() < 1){
                                $obTAlmoxarifadoCatalogoItemMarca = new TAlmoxarifadoCatalogoItemMarca;
                                $obTAlmoxarifadoCatalogoItemMarca->setDado('cod_item'   , $rsOrdemCompraItem->getCampo("cod_item")  );
                                $obTAlmoxarifadoCatalogoItemMarca->setDado('cod_marca'  , $rsOrdemCompraItem->getCampo("cod_marca") );

                                $obErro = $obTAlmoxarifadoCatalogoItemMarca->exclusao($boTransacao);
                            }
                        }
                    }
                    $rsOrdemCompraItem->proximo();

                    if($obErro->ocorreu())
                        break;
                }

                if(!$obErro->ocorreu()) {
                    $inCount = 0;
                 
                    foreach ($arItens as $stChave => $stValor) {
                        $inCount++;
                        $inQuantidade = str_replace(',','.',str_replace('.','',$request->get('qtdeOC_'.$inCount)));
                        if ($inQuantidade > 0) {
                            $obTOrdemCompraItem->setDado('exercicio'            , $request->get('stExercicioOrdemCompra')   );
                            $obTOrdemCompraItem->setDado('exercicio_pre_empenho', $request->get('stExercicioEmpenho')       );
                            $obTOrdemCompraItem->setDado('cod_entidade'         , $request->get('inCodEntidade')            );
                            $obTOrdemCompraItem->setDado('cod_ordem'            , $request->get('inCodOrdemCompra')         );
                            $obTOrdemCompraItem->setDado('num_item'             , $stValor['num_item']                      );
                            $obTOrdemCompraItem->setDado('cod_pre_empenho'      , $stValor['cod_pre_empenho']               );
                            $obTOrdemCompraItem->setDado('quantidade'           , $inQuantidade                             );
                            $obTOrdemCompraItem->setDado('vl_total'             , $inQuantidade * $stValor['vl_unitario']   );
                            $obTOrdemCompraItem->setDado('tipo'                 , $stTipoOrdem                              );
                            $obErro = $obTOrdemCompraItem->inclusao($boTransacao);

                            if(is_array($arItensAlmoxarifado[$stValor['num_item']]) && !$obErro->ocorreu()){
                                $obTEmpenhoItemPreEmpenho  = new TEmpenhoItemPreEmpenho;
                                $obTAlmoxarifadoCatalogoItemMarca = new TAlmoxarifadoCatalogoItemMarca;

                                $obTEmpenhoItemPreEmpenho->setDado( "exercicio"         , $request->get('stExercicioEmpenho') );
                                $obTEmpenhoItemPreEmpenho->setDado( "cod_pre_empenho"   , $stValor['cod_pre_empenho']         );
                                $obTEmpenhoItemPreEmpenho->setDado( "num_item"          , $stValor['num_item']                );

                                $obErro = $obTEmpenhoItemPreEmpenho->recuperaPorChave($rsItemPreEmpenho, $boTransacao);

                                if(!$obErro->ocorreu()) {
                                    while (!$rsItemPreEmpenho->eof()) {
                                        $obTEmpenhoItemPreEmpenho->setDado( "cod_unidade"       , $rsItemPreEmpenho->getCampo("cod_unidade")                        );
                                        $obTEmpenhoItemPreEmpenho->setDado( "cod_grandeza"      , $rsItemPreEmpenho->getCampo("cod_grandeza")                       );
                                        $obTEmpenhoItemPreEmpenho->setDado( "quantidade"        , $rsItemPreEmpenho->getCampo("quantidade")                         );
                                        $obTEmpenhoItemPreEmpenho->setDado( "nom_unidade"       , $rsItemPreEmpenho->getCampo("nom_unidade")                        );
                                        $obTEmpenhoItemPreEmpenho->setDado( "sigla_unidade"     , $rsItemPreEmpenho->getCampo("sigla_unidade")                      );
                                        $obTEmpenhoItemPreEmpenho->setDado( "vl_total"          , $rsItemPreEmpenho->getCampo("vl_total")                           );
                                        $obTEmpenhoItemPreEmpenho->setDado( "nom_item"          , $rsItemPreEmpenho->getCampo("nom_item")                           );
                                        $obTEmpenhoItemPreEmpenho->setDado( "complemento"       , $rsItemPreEmpenho->getCampo("complemento")                        );
                                        $obTEmpenhoItemPreEmpenho->setDado( "cod_centro"        , $arItensAlmoxarifado[$stValor['num_item']]['inCodCentroCusto']    );

                                        /*
                                         *Ticket #22576, NÃO está efetuando o update na tabela empenho.item_pre_empenho->cod_item, pois foi definido
                                         *com o Gelson, que se o empenho não possui codigo de item, a melhor situação é incluir na tabela compras.ordem_item
                                         *E a verificação de cod_item, passa inicialmente a ser feita na tabela compras.ordem_item NÃO anulada.
                                         *Se a tabela empenho.item_pre_empenho já possui cod_item, a tabela compras.ordem_item utilizara o mesmo cod_item.
                                        */
                                        //$obTEmpenhoItemPreEmpenho->setDado( "cod_item"          , $stValor['cod_item']                          );
                                        $obErro = $obTEmpenhoItemPreEmpenho->alteracao($boTransacao);

                                        $rsItemPreEmpenho->proximo();

                                        if($obErro->ocorreu())
                                            break;
                                    }
                                    
                                    $inCodMarca = $request->get('inMarca'.$stValor['num_item']); 
                                    if(empty($inCodMarca) OR ($inCodMarca === $arItensAlmoxarifado[$stValor['num_item']]['inMarca']) ) {
                                        $inCodMarca = $arItensAlmoxarifado[$stValor['num_item']]['inMarca'];                                         
                                    }
                                                                       
                                    if ( !empty($stValor['cod_item']) && !$obErro->ocorreu() ){
                                        if($arItensAlmoxarifado[$stValor['num_item']]['inMarca'] != null) {
                                            if($arItensAlmoxarifado[$stValor['num_item']]['inCodItem'] != null) {
                                                $stFiltro = " AND acim.cod_marca = ".$inCodMarca." AND acim.cod_item = ".$arItensAlmoxarifado[$stValor['num_item']]['inCodItem'];
                                                $obErro = $obTAlmoxarifadoCatalogoItemMarca->recuperaItemMarca($rsItemMarca, $stFiltro, "", $boTransacao);
                                                if ($rsItemMarca->getNumLinhas() < 1 && !$obErro->ocorreu()) {
                                                    $obTAlmoxarifadoCatalogoItemMarca->setDado('cod_item'   , $arItensAlmoxarifado[$stValor['num_item']]['inCodItem'] );
                                                    $obTAlmoxarifadoCatalogoItemMarca->setDado('cod_marca'  , $inCodMarca   );
                                                    $obErro = $obTAlmoxarifadoCatalogoItemMarca->inclusao($boTransacao);
                                                }
                                            }
                                        }
                                    }

                                    if(!$obErro->ocorreu()) {
                                        $obTOrdemCompraItem->setDado('exercicio'            , Sessao::getExercicio()                                            );
                                        $obTOrdemCompraItem->setDado('exercicio_pre_empenho', $request->get('stExercicioEmpenho')                               );
                                        $obTOrdemCompraItem->setDado('cod_entidade'         , $request->get('inCodEntidade')                                    );
                                        $obTOrdemCompraItem->setDado('cod_ordem'            , $obTOrdemCompra->getDado('cod_ordem')                             );
                                        $obTOrdemCompraItem->setDado('num_item'             , $stValor['num_item']                                              );
                                        $obTOrdemCompraItem->setDado('cod_pre_empenho'      , $stValor['cod_pre_empenho']                                       );
                                        $obTOrdemCompraItem->setDado('quantidade'           , $inQuantidade                                                     );
                                        $obTOrdemCompraItem->setDado('tipo'                 , $stTipoOrdem                                                      );
                                        $obTOrdemCompraItem->setDado('vl_total'             , $inQuantidade * $stValor['vl_unitario']                           );
                                        $obTOrdemCompraItem->setDado('cod_marca'            , $inCodMarca             );
                                        $obTOrdemCompraItem->setDado('cod_item'             , $stValor['cod_item']                                              );
                                        $obTOrdemCompraItem->setDado('cod_centro'           , $arItensAlmoxarifado[$stValor['num_item']]['inCodCentroCusto']    );
                                        $obErro = $obTOrdemCompraItem->alteracao($boTransacao);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        if(!$obErro->ocorreu()){
            SistemaLegado::alertaAviso($pgRel."&inCodEntidade=".$request->get('inCodEntidade')."&inCodOrdem=".$request->get('inCodOrdemCompra')."&stTipo=".$request->get('stTipo')."&stTipoOrdem=".$stTipoOrdem."&stExercicioOrdemCompra=".$request->get('stExercicioOrdemCompra'),"Ordem de $stDesc - ".$request->get('inCodOrdemCompra'),"incluir","incluir", Sessao::getId(), "../");
        }
        $obTMapeamento = $obTOrdemCompra;
    break;
    case "anular":
    case "anularOS":
        $stAcaoReduzido = "anular";

        $obTOrdemCompraAnulacao = new TComprasOrdemAnulacao();
        $obTOrdemCompraAnulacao->setDado('exercicio'    , $request->get('stExercicioOrdemCompra')   );
        $obTOrdemCompraAnulacao->setDado('cod_entidade' , $request->get('inCodEntidade')            );
        $obTOrdemCompraAnulacao->setDado('cod_ordem'    , $request->get('inCodOrdemCompra')         );
        $obTOrdemCompraAnulacao->setDado('motivo'       , $request->get('stMotivo')                 );
        $obTOrdemCompraAnulacao->setDado('tipo'         , $stTipoOrdem                              );
        $obErro = $obTOrdemCompraAnulacao->inclusao($boTransacao);

        if(!$obErro->ocorreu()){
            $obErro = $obTOrdemCompraAnulacao->recuperaDados( $rsOrdemCompraAnulacao, "", "", $boTransacao );
        }

        if(!$obErro->ocorreu()){
            $inCount = 0;
            $arItens = Sessao::read('arItens');
            foreach ($arItens as $stChave => $stValor) {
                $inCount++;
                
                if ($stValor["qtde_oc"] > 0) {
                    $obTOrdemCompraItemAnulacao = new TComprasOrdemItemAnulacao();
                    $obTOrdemCompraItemAnulacao->setDado('exercicio'            , $rsOrdemCompraAnulacao->getCampo("exercicio")     );
                    $obTOrdemCompraItemAnulacao->setDado('cod_entidade'         , $rsOrdemCompraAnulacao->getCampo("cod_entidade")  );
                    $obTOrdemCompraItemAnulacao->setDado('cod_ordem'            , $rsOrdemCompraAnulacao->getCampo("cod_ordem")     );
                    $obTOrdemCompraItemAnulacao->setDado('cod_pre_empenho'      , $stValor["cod_pre_empenho"]                       );
                    $obTOrdemCompraItemAnulacao->setDado('num_item'             , $stValor["num_item"]                              );
                    $obTOrdemCompraItemAnulacao->setDado('timestamp'            , $rsOrdemCompraAnulacao->getCampo("timestamp")     );
                    $obTOrdemCompraItemAnulacao->setDado('quantidade'           , $stValor["qtde_oc"]                               );
                    $obTOrdemCompraItemAnulacao->setDado('vl_total'             , $stValor["qtde_oc"] * $stValor['vl_unitario']     );
                    $obTOrdemCompraItemAnulacao->setDado('tipo'                 , $stTipoOrdem                                      );
                    $obTOrdemCompraItemAnulacao->setDado('exercicio_pre_empenho', $request->get('stExercicioEmpenho')               );
                    $obErro = $obTOrdemCompraItemAnulacao->inclusao($boTransacao);
                }

                if($obErro->ocorreu())
                    break;
            }
        }

        if(!$obErro->ocorreu()){
            SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,"Ordem de $stDesc - ".$request->get('inCodOrdemCompra')."","excluir","excluir", Sessao::getId(), "../");
        }
        $obTMapeamento = $obTOrdemCompraAnulacao;
    break;

    case 'reemitir':
    case 'reemitirOS':
        $stAcaoReduzido = "reemitir";
        if(!$obErro->ocorreu()){
            $stIncluirAssinaturaUsuario = Sessao::read('stIncluirAssinaturaUsuario');
            SistemaLegado::alertaAviso($pgRel."&inCodEntidade=".$request->get('inCodEntidade')."&inCodOrdem=".$request->get('inCodOrdemCompra')."&stTipo=".$request->get('stTipo')."&stTipoOrdem=".$request->get('stTipoOrdem')."&stExercicioOrdemCompra=".$request->get('stExercicioOrdemCompra')."&stIncluirAssinaturaUsuario=".$stIncluirAssinaturaUsuario,"Ordem de $stDesc - ".$request->get('inCodOrdemCompra'),"incluir","incluir", Sessao::getId(), "../");
        }
    break;

    }
}

if ( $obErro->ocorreu() )
    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_".$stAcaoReduzido,"erro");

$obErro = $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTMapeamento );

SistemaLegado::LiberaFrames();
?>
