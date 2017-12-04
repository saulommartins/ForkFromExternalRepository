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
    * Página de Processamento de Sequência de Cálculo
    * Data de Criação: 05/01/2006

    * @author Analista:
    * @author Desenvolvedor: Leandro André Zis

    * @ignore

    * Casos de uso: uc-03.03.09

    $Id:$

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoTransferenciaAlmoxarifadoItem.class.php";
include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoTransferenciaAlmoxarifadoItemDestino.class.php";
include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoInventarioItens.class.php";
include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoControleEstoque.class.php";

$stAcao = $request->get('stAcao');

# Define o nome dos arquivos PHP
$stPrograma = "MovimentacaoTransferencia";
$pgFilt     = "FL".$stPrograma.".php?stAcao=$stAcao";
$pgList     = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgForm     = "FM".$stPrograma.".php?stAcao=$stAcao";
$pgProc     = "PR".$stPrograma.".php?stAcao=$stAcao";
$pgOcul     = "OC".$stPrograma.".php?stAcao=$stAcao";
$pgJs       = "JS".$stPrograma.".js";
$pgRel      = "OCGera".$stPrograma.".php";

include_once($pgJs);

function montaTabelaLightBox($rsItens, $stLista)
{
    if ($stLista == "Ponto Pedido") {
        $stTitulo = 'Ítens que Entraram em Ponto de Pedido';
        $stConteudo = 'Ponto de Pedido';
        $stDado = '[ponto_pedido]';
    } else {
        $stTitulo = 'Ítens que Entraram no Estoque Mínimo';
        $stConteudo = 'Estoque Mínimo';
        $stDado = '[estoque_minimo]';
    }

    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    $obLista->setTitulo ( $stTitulo );
    $obLista->setRecordSet( $rsItens );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( '&nbsp' );
    $obLista->ultimoCabecalho->setWidth( 4 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( 'Item' );
    $obLista->ultimoCabecalho->setWidth( 21 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( 'Unidade de Medida' );
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( 'Marca' );
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( 'Centro de Custo' );
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( $stConteudo );
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( 'Saldo Atual' );
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[cod_item]-[desc_item]" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[desc_unidade]" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[cod_marca]-[desc_marca]" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[cod_centro]-[desc_centro]" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( $stDado );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[saldo_atual]" );
    $obLista->commitDado();

    $obLista->montaHTML();
    $stHTML = $obLista->getHTML();
    $stHTML = str_replace( "\n" ,"" ,$stHTML );
    $stHTML = str_replace( "  " ,"" ,$stHTML );
    $stHTML = str_replace( "'","\\'",$stHTML );

    return $stHTML;
}

// Códigos para o Lightbox
function lightbox($arItens, $inCodLancamento, $inCodLancamentoEntrada, $total_pagina)
{
    global $request;

    $stAcao = $request->get('stAcao');
    $stPrograma = "MovimentacaoTransferencia";
    $pgRel = "OCGera".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao"."&inNumLancamento=".$inCodLancamento."&boEntradaAutomatica=".$_REQUEST['boEntradaAutomatica']."&inNumLancamentoEntrada=".$inCodLancamentoEntrada."&inTotalPagina=".$total_pagina;

    //Lighbox do Ponto de Pedido
    $arItensPontoPedido = array();
    $inPP = 0;//Iterator ponto pedido
    $inEM = 0;//Iterator estoque mínimo
    $stHTML = "";
    $arItensEstoqueMinimo = array();
    $arItensPontoPedido= array();

    foreach ($arItens as $key => $item) {

        $inQuantidadeAtendida = str_replace(array(".",","),array("","."),$item["quantidade"]);
        $inSaldoAtual = str_replace(array(".",","),array("","."),$item["saldo_atual"]);

        $obTAlmoxarifadoControleEstoque = new TAlmoxarifadoControleEstoque;
        $obTAlmoxarifadoControleEstoque->setDado( "cod_item", $item["cod_item"] );
        $obTAlmoxarifadoControleEstoque->recuperaPorChave( $rsControle );

        if ($_REQUEST['stAcao'] == 'saida') {
            $pontoPedidoSaldo = $inSaldoAtual - $inQuantidadeAtendida;
        } else {
            $pontoPedidoSaldo = $inSaldoAtual + $inQuantidadeAtendida;
        }

        if ( $rsControle->getCampo("estoque_minimo") > 0 && $pontoPedidoSaldo <=  $rsControle->getCampo("estoque_minimo") ) {
            $arItensEstoqueMinimo[$inEM]["cod_item"]     = $item["cod_item"];
            $arItensEstoqueMinimo[$inEM]["desc_item"]    = $item["desc_item"];
            $arItensEstoqueMinimo[$inEM]["desc_unidade"] = $item["desc_unidade"];
            $arItensEstoqueMinimo[$inEM]["cod_marca"]    = $item["cod_marca"];
            $arItensEstoqueMinimo[$inEM]["desc_marca"]   = $item["desc_marca"];
            $arItensEstoqueMinimo[$inEM]["cod_centro"]   = $item["cod_centro"];
            $arItensEstoqueMinimo[$inEM]["desc_centro"]  = $item["desc_centro"];
            $arItensEstoqueMinimo[$inEM]["estoque_minimo"] = number_format($rsControle->getCampo("estoque_minimo"), 4, ',', '.');
            $arItensEstoqueMinimo[$inEM]["saldo_atual"]  = number_format($pontoPedidoSaldo, 4, ',', '.');
            $inEM++;
        } elseif ( ($pontoPedidoSaldo) <= (float) $rsControle->getCampo("ponto_pedido") && $rsControle->getCampo("ponto_pedido") > 0 ) {
            $arItensPontoPedido[$inPP]["cod_item"]     = $item["cod_item"];
            $arItensPontoPedido[$inPP]["desc_item"]    = $item["desc_item"];
            $arItensPontoPedido[$inPP]["desc_unidade"] = $item["desc_unidade"];
            $arItensPontoPedido[$inPP]["cod_marca"]    = $item["cod_marca"];
            $arItensPontoPedido[$inPP]["desc_marca"]   = $item["desc_marca"];
            $arItensPontoPedido[$inPP]["cod_centro"]   = $item["cod_centro"];
            $arItensPontoPedido[$inPP]["desc_centro"]  = $item["desc_centro"];
            $arItensPontoPedido[$inPP]["ponto_pedido"] = number_format($rsControle->getCampo("ponto_pedido"), 4, ',', '.');
            $arItensPontoPedido[$inPP]["saldo_atual"]  = number_format($pontoPedidoSaldo, 4, ',', '.');
            $inPP++;
        }
    }

    if ($inPP > 0) {
        $rsItensPontoPedido = new RecordSet();
        $rsItensPontoPedido->preenche( $arItensPontoPedido );
        $stHTML .= montaTabelaLightBox($rsItensPontoPedido, "Ponto Pedido" );
    }

    if ($inEM >0) {
        $rsItensEstoqueMinimo =  new RecordSet();
        $rsItensEstoqueMinimo->preenche( $arItensEstoqueMinimo );
        $stHTML .= montaTabelaLightBox($rsItensEstoqueMinimo,"Estoque Mínimo" );
    }

    $stCaminhoLighbox = $pgRel;

    $obBtnOk  = new Ok();
    $obBtnOk->obEvento->setOnClick( "window.parent.frames['telaPrincipal'].location.href = '".$stCaminhoLighbox."';" );

    $obFormulario = new Formulario();
    $obFormulario->defineBarra(array($obBtnOk), "left", "");
    $obFormulario->montaInnerHtml();
    $stHTML .= $obFormulario->getHTML();

    $stJs = "d.getElementById('conteudolightbox').innerHTML = '".$stHTML."';";

    if ($inPP > 0 OR $inEM >0) {
        if ($_REQUEST['stAcao'] == 'saida') {
            echo '<script type="text/javascript">criaFundo(); criaLightbox("'.$stCaminhoLighbox.'",\'saida\');'.$stJs.'</script>';
        } else {
            echo '<script type="text/javascript">criaFundo(); criaLightbox("'.$stCaminhoLighbox.'",\'entrada\');'.$stJs.'</script>';
        }

        return True;
    } else {
        return False;
    }
}

switch ($stAcao) {
    case "entrada":
    case "saida":

       include CAM_GP_ALM_MAPEAMENTO.'TAlmoxarifadoAlmoxarife.class.php';
       include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoNaturezaLancamento.class.php";
       include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoLancamentoMaterial.class.php";
       include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoEstoqueMaterial.class.php";
       include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoLancamentoPerecivel.class.php";
       include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoPerecivel.class.php";
       include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoTransferenciaAlmoxarifadoItem.class.php";
       include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoAtributoEstoqueMaterialValor.class.php";

       $boPermissao = false;

       if ($stAcao == "entrada") {
           $inCodAlmoxarifado = $_REQUEST['inAlmoxarifadoDestino'];

           $stFiltroAlmoxarife = "      AND a1.cgm_almoxarife = ".Sessao::read('numCgm')."  \n";
           $stFiltroAlmoxarife = "      AND a2.cod_almoxarifado IN (".$inCodAlmoxarifado.") \n";
           $stFiltroAlmoxarife.= " GROUP BY a1.cgm_almoxarife                               \n";
           $stFiltroAlmoxarife.= "        , a1_cgm.nom_cgm                                  \n";
           $stFiltroAlmoxarife.= "        , a1.ativo                                        \n";
           $stFiltroAlmoxarife.= "        , a2.cod_almoxarifado                             \n";
           $stFiltroAlmoxarife.= "        , a2_cgm.nom_cgm                                  \n";
           $stFiltroAlmoxarife.= "        , p.padrao                                        \n";

           $obTAlmoxarifadoAlmoxarife = new TAlmoxarifadoAlmoxarife();
           $obTAlmoxarifadoAlmoxarife->recuperaAlmoxarifePermissoes($rsAlmoxarifadoAlmoxarife, $stFiltroAlmoxarife);

           $dadosTransferenciaEntrada = ProcessarTransferenciaEntrada($inCodAlmoxarifado);
           $inCodTransferencia = $dadosTransferenciaEntrada[0];
           $inCodLancamento    = $dadosTransferenciaEntrada[1];

           if (count($rsAlmoxarifadoAlmoxarife->arElementos) > 0) {
               $boPermissao = true;
           }
       } else {

            # Caso o usuário escolha Entrada automática, o sistema irá validar
            # se o usuário possui permissão no almoxarifado destino e origem,
            # se sim, irá efetuar a saída e a entrada.

            if ($_REQUEST['boEntradaAutomatica']=='S') {
                $inCodAlmoxarifado = $_REQUEST['inAlmoxarifadoOrigem'];

                $stFiltroAlmoxarife = "      AND a1.cgm_almoxarife = ".Sessao::read('numCgm')." \n";
                $stFiltroAlmoxarife.= " GROUP BY a1.cgm_almoxarife                              \n";
                $stFiltroAlmoxarife.= "        , a1_cgm.nom_cgm                                 \n";
                $stFiltroAlmoxarife.= "        , a1.ativo                                       \n";
                $stFiltroAlmoxarife.= "        , a2.cod_almoxarifado                            \n";
                $stFiltroAlmoxarife.= "        , a2_cgm.nom_cgm                                 \n";
                $stFiltroAlmoxarife.= "        , p.padrao                                       \n";

                $obTAlmoxarifadoAlmoxarife = new TAlmoxarifadoAlmoxarife();
                $obTAlmoxarifadoAlmoxarife->recuperaAlmoxarifePermissoes($rsAlmoxarifadoAlmoxarife, $stFiltroAlmoxarife);

                $boPermissaoOrigem     = false;
                $boPermissaoDestino    = false;

                while (!$rsAlmoxarifadoAlmoxarife->eof()) {
                    $arAlmoxarifado = explode(' - ', $rsAlmoxarifadoAlmoxarife->getCampo('almoxarifados'));
                    if ($arAlmoxarifado[0] == $_REQUEST['inAlmoxarifadoOrigem']) {
                        $boPermissaoOrigem = true;
                    }

                    if ($arAlmoxarifado[0] == $_REQUEST['inAlmoxarifadoDestino']) {
                        $boPermissaoDestino = true;
                    }

                    $rsAlmoxarifadoAlmoxarife->proximo();
                }

                $dadosTransferenciaSaida = ProcessarTransferenciaSaida($inCodAlmoxarifado);
                $inCodTransferencia = $dadosTransferenciaSaida[0];
                $inCodLancamento    = $dadosTransferenciaSaida[1];

                if (($boPermissaoOrigem)&&($boPermissaoDestino)) {
                    $boPermissao = true;

                    $dadosTransferenciaEntrada = ProcessarTransferenciaEntrada($_REQUEST['inAlmoxarifadoDestino']);
                    $inCodLancamentoEntrada    = $dadosTransferenciaEntrada[1];

                    //Recupera o num de páginas do relatório
                    $obTAlmoxarifadoNaturezaLancamento = new TAlmoxarifadoNaturezaLancamento;
                    $obTAlmoxarifadoNaturezaLancamento->setDado('tipo_natureza' , 'E');
                    $obTAlmoxarifadoNaturezaLancamento->setDado('cod_natureza'  , 2);
                    $obTAlmoxarifadoNaturezaLancamento->setDado('num_lancamento', $inCodLancamentoEntrada);
                    $obTAlmoxarifadoNaturezaLancamento->setDado('exercicio_lancamento'  , Sessao::getExercicio());
                    $obTAlmoxarifadoNaturezaLancamento->recuperaTotalPagina($totalPagina);
                    $total_pagina = $totalPagina->getCampo('total_pagina');
                }
            } else {
                $inCodAlmoxarifado = $_REQUEST['inAlmoxarifadoOrigem'];

                $stFiltroAlmoxarife = "      AND a1.cgm_almoxarife = ".Sessao::read('numCgm')."  \n";
                $stFiltroAlmoxarife = "      AND a2.cod_almoxarifado IN (".$inCodAlmoxarifado.") \n";
                $stFiltroAlmoxarife.= " GROUP BY a1.cgm_almoxarife                               \n";
                $stFiltroAlmoxarife.= "        , a1_cgm.nom_cgm                                  \n";
                $stFiltroAlmoxarife.= "        , a1.ativo                                        \n";
                $stFiltroAlmoxarife.= "        , a2.cod_almoxarifado                             \n";
                $stFiltroAlmoxarife.= "        , a2_cgm.nom_cgm                                  \n";
                $stFiltroAlmoxarife.= "        , p.padrao                                        \n";

                $obTAlmoxarifadoAlmoxarife = new TAlmoxarifadoAlmoxarife();
                $obTAlmoxarifadoAlmoxarife->recuperaAlmoxarifePermissoes($rsAlmoxarifadoAlmoxarife, $stFiltroAlmoxarife);

                if (count($rsAlmoxarifadoAlmoxarife->arElementos) > 0) {
                    $boPermissao = true;
                }

                if (!$boPermissao) {
                    SistemaLegado::exibeAviso("O Almoxarife atual nao possui permissões para esta Transferência.","n_incluir","erro");
                    exit;
                }

                $dadosTransferenciaSaida = ProcessarTransferenciaSaida($inCodAlmoxarifado);
                $inCodTransferencia = $dadosTransferenciaSaida[0];
                $inCodLancamento    = $dadosTransferenciaSaida[1];

                //Recupera o num de páginas do relatório
                $obTAlmoxarifadoNaturezaLancamento = new TAlmoxarifadoNaturezaLancamento;
                $obTAlmoxarifadoNaturezaLancamento->setDado('tipo_natureza' , 'S');
                $obTAlmoxarifadoNaturezaLancamento->setDado('cod_natureza'  , 2);
                $obTAlmoxarifadoNaturezaLancamento->setDado('num_lancamento', $inCodLancamento);
                $obTAlmoxarifadoNaturezaLancamento->setDado('exercicio_lancamento'  , Sessao::getExercicio());
                $obTAlmoxarifadoNaturezaLancamento->recuperaTotalPaginaSaida($totalPagina);
                $total_pagina = $totalPagina->getCampo('total_pagina');
            }

        }

       if (!$boPermissao) {
           SistemaLegado::exibeAviso("O Almoxarife atual nao possui permissões para esta Transferência.","n_incluir","erro");
           exit;
       }
       $arItens = Sessao::read('Valores');
       $boLightbox = lightbox($arItens, $inCodLancamento, $inCodLancamentoEntrada, $total_pagina);

       if (!$boLightbox) {
            if ($stAcao == "entrada") {
                SistemaLegado::alertaAviso($pgRel."?".Sessao::getId()."&stAcao=".$stAcao."&inNumLancamento=".$inCodLancamento,"Transferência: ".$inCodTransferencia,"incluir","aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::alertaAviso($pgRel."?".Sessao::getId()."&stAcao=".$stAcao."&inNumLancamento=".$inCodLancamento."&boEntradaAutomatica=".$_REQUEST['boEntradaAutomatica']."&inNumLancamentoEntrada=".$inCodLancamentoEntrada."&inTotalPagina=".$total_pagina,"Transferência: ".$inCodTransferencia,"incluir","aviso", Sessao::getId(), "../");
            }
       } else {
           SistemaLegado::exibeAviso("Transferência: ".$inCodTransferencia,"incluir","aviso");
       }
    break;
}

/**
*ProcessarTransferenciaEntrada
*
*Executa os processos necessaeio para entrada da transferencia
*/
function ProcessarTransferenciaEntrada($inCodAlmoxarifado)
{
    $inCodLancamento = 0;

    $obTAlmoxarifadoNaturezaLancamento = new TAlmoxarifadoNaturezaLancamento;

    $obTAlmoxarifadoNaturezaLancamento->setDado('tipo_natureza' , 'E');
    $obTAlmoxarifadoNaturezaLancamento->setDado('cod_natureza'  , 2  );

    # Recupera o num_lancamento considerando as configurações do Almoxarifado.
    $obTAlmoxarifadoNaturezaLancamento->recuperaNumNaturezaLancamento($rsNumLancamento);

    $inCodLancamento = $rsNumLancamento->getCampo('num_lancamento');

    $obTAlmoxarifadoNaturezaLancamento->setDado('num_lancamento' , $inCodLancamento      );
    $obTAlmoxarifadoNaturezaLancamento->setDado('cgm_almoxarife' , Sessao::read('numCgm'));
    $obTAlmoxarifadoNaturezaLancamento->setDado('numcgm_usuario' , Sessao::read('numCgm'));

    $obTAlmoxarifadoNaturezaLancamento->inclusao();

    $obTAlmoxarifadoLancamentoMaterial =  new TAlmoxarifadoLancamentoMaterial;
    $obTAlmoxarifadoLancamentoMaterial->obTAlmoxarifadoNaturezaLancamento = & $obTAlmoxarifadoNaturezaLancamento;

    $obTAlmoxarifadoTransferenciaAlmoxarifadoItem = new TAlmoxarifadoTransferenciaAlmoxarifadoItem;
    $TAlmoxarifadoTransferenciaAlmoxarifadoItemDestino = new TAlmoxarifadoTransferenciaAlmoxarifadoItemDestino;

    $arValores =  Sessao::read('Valores');

    $inCodItemInvalido = "";

    $valorMercadoUnitario = array();

    $stItemInventario = "";
    $contaItemInventario = 0;

    foreach ($arValores as $key => $arLancamentos) {

        $obTAlmoxarifadoInventarioItens = new TAlmoxarifadoInventarioItens();
        $stFiltro = " AND catalogo_item.cod_item = ".$arLancamentos['cod_item']."  \n";
        $obTAlmoxarifadoInventarioItens->recuperaItensInventarioPorClassificacao($rsItensInventarioPorClassificacao, $stFiltro);

        if (count($rsItensInventarioPorClassificacao->arElementos) > 0) {
            $boItemInventario                                                                                   = true;
            $contaItemInventario++;
            $stItemInventario .= $arLancamentos['cod_item'].",";
        }
    }

    // gerando mensagem para itens em inventario
    if (($boItemInventario)&&($contaItemInventario > 1)) {
        $stItens = substr($stItemInventario, 0, -1);
        $stMensagemInventario = "Os itens ".$stItens." estão em inventário.";
    } elseif (($boItemInventario)&&($contaItemInventario == 1)) {
        $stItem = substr($stItemInventario, 0, -1);
        $stMensagemInventario = "O item ".$stItem." está em inventário.";
    }

    if ((!$boQuantidadeInvalida) && (!$boItemInventario)) {

        foreach ($arValores as $key => $arLancamentos) {

            $obTAlmoxarifadoEstoqueMaterial = new TAlmoxarifadoEstoqueMaterial;
            $obTAlmoxarifadoEstoqueMaterial->setDado( 'cod_item'            , $arLancamentos['cod_item']              );
            $obTAlmoxarifadoEstoqueMaterial->setDado( 'cod_marca'           , $arLancamentos['cod_marca']             );
            $obTAlmoxarifadoEstoqueMaterial->setDado( 'cod_almoxarifado'    , $inCodAlmoxarifado                      );
            $obTAlmoxarifadoEstoqueMaterial->setDado( 'cod_centro'          , $arLancamentos['cod_centro_destino']    );
            $obTAlmoxarifadoEstoqueMaterial->recuperaPorChave($rsEstoqueMaterial);

            if ($rsEstoqueMaterial->getNumLinhas() <= 0) {
                $obTAlmoxarifadoEstoqueMaterial = new TAlmoxarifadoEstoqueMaterial;
                $obTAlmoxarifadoEstoqueMaterial->setDado( 'cod_item'            , $arLancamentos['cod_item']          );
                $obTAlmoxarifadoEstoqueMaterial->setDado( 'cod_marca'           , $arLancamentos['cod_marca']         );
                $obTAlmoxarifadoEstoqueMaterial->setDado( 'cod_almoxarifado'    , $inCodAlmoxarifado                  );
                $obTAlmoxarifadoEstoqueMaterial->setDado( 'cod_centro'          , $arLancamentos['cod_centro_destino']);
                $obTAlmoxarifadoEstoqueMaterial->inclusao();
            }
            $rsAtributos = unserialize($arLancamentos['ValoresAtributos']);

            $obTAlmoxarifadoLancamentoMaterial->setDado( 'cod_item'            , $arLancamentos['cod_item']           );
            $obTAlmoxarifadoLancamentoMaterial->setDado( 'cod_marca'           , $arLancamentos['cod_marca']          );
            $obTAlmoxarifadoLancamentoMaterial->setDado( 'cod_almoxarifado'    , $inCodAlmoxarifado                   );
            $obTAlmoxarifadoLancamentoMaterial->setDado( 'cod_centro'          , $arLancamentos['cod_centro_destino'] );

            if ($rsAtributos->getNumLinhas() > 0) {
                processaItemComAtributos($obTAlmoxarifadoLancamentoMaterial, $obTAlmoxarifadoTransferenciaAlmoxarifadoItem, $rsAtributos, $arLancamentos, $inCodAlmoxarifado, 'entrada');
            } elseif ( count($arLancamentos['ValoresLotes'] ) <= 0 ) {
                $obTAlmoxarifadoLancamentoMaterial->proximoCod( $inCodLancMat );
                $obTAlmoxarifadoLancamentoMaterial->setDado( 'cod_lancamento' , $inCodLancMat           );

                $stValor = str_replace('.','',$arLancamentos['quantidade'] );
                $stValor = str_replace(',','.', $stValor );

                $stValorUnitario = str_replace('.','',$arLancamentos['vl_unitario'] );
                $stValorUnitario = str_replace(',','.', $stValorUnitario );

                $valorTotal = $stValorUnitario * $stValor;

                $obTAlmoxarifadoLancamentoMaterial->setDado( 'quantidade', $stValor );

                $obTAlmoxarifadoLancamentoMaterial->setDado('valor_mercado', $valorTotal);

                $obTAlmoxarifadoLancamentoMaterial->inclusao();

                $TAlmoxarifadoTransferenciaAlmoxarifadoItemDestino->obTAlmoxarifadoLancamentoMaterial = &$obTAlmoxarifadoLancamentoMaterial;
                $TAlmoxarifadoTransferenciaAlmoxarifadoItemDestino->setDado ( 'cod_transferencia', $arLancamentos['inCodTransferencia']   );
                $TAlmoxarifadoTransferenciaAlmoxarifadoItemDestino->setDado ( 'exercicio', $arLancamentos['stExercicio']);
                $TAlmoxarifadoTransferenciaAlmoxarifadoItemDestino->setDado ( 'cod_almoxarifado', $inCodAlmoxarifado );
                $TAlmoxarifadoTransferenciaAlmoxarifadoItemDestino->setDado ( 'cod_marca', $arLancamentos['cod_marca'] );
                $TAlmoxarifadoTransferenciaAlmoxarifadoItemDestino->setDado ( 'cod_centro', $arLancamentos['cod_centro'] );
                $TAlmoxarifadoTransferenciaAlmoxarifadoItemDestino->setDado ( 'cod_centro_destino', $arLancamentos['cod_centro_destino'] );
                $TAlmoxarifadoTransferenciaAlmoxarifadoItemDestino->setDado ( 'cod_item', $arLancamentos['cod_item'] );
                $TAlmoxarifadoTransferenciaAlmoxarifadoItemDestino->setDado ( 'cod_lancamento'  , $inCodLancMat                       );
                $TAlmoxarifadoTransferenciaAlmoxarifadoItemDestino->inclusao();

            } else {

                foreach ($arLancamentos['ValoresLotes'] as $arItensLotes) {
                    $stValorLote = str_replace('.','',$arItensLotes['quantidade'] );
                    $stValorLote = str_replace(',','.', $stValorLote );
                    if ($stValorLote > 0) {
                        $obTAlmoxarifadoLancamentoMaterial->proximoCod( $inCodLancMat );
                        $obTAlmoxarifadoLancamentoMaterial->setDado( 'cod_lancamento' , $inCodLancMat               );

                        $stValorUnitario = str_replace('.','',$arLancamentos['vl_unitario'] );
                        $stValorUnitario = str_replace(',','.', $stValorUnitario );

                        $valorTotal = $stValorUnitario * $stValorLote;

                        $obTAlmoxarifadoLancamentoMaterial->setDado( 'quantidade'     , $stValorLote );

                        $obTAlmoxarifadoLancamentoMaterial->setDado( 'valor_mercado', $valorTotal );

                        $obTAlmoxarifadoLancamentoMaterial->inclusao();

                        $obTAlmoxarifadoPerecivel = new TAlmoxarifadoPerecivel;
                        $obTAlmoxarifadoPerecivel->setDado( 'cod_item'            , $arLancamentos['cod_item']          );
                        $obTAlmoxarifadoPerecivel->setDado( 'cod_marca'           , $arLancamentos['cod_marca']         );
                        $obTAlmoxarifadoPerecivel->setDado( 'cod_almoxarifado'    , $inCodAlmoxarifado                  );
                        $obTAlmoxarifadoPerecivel->setDado( 'cod_centro'          , $arLancamentos['cod_centro_destino']);
                        $obTAlmoxarifadoPerecivel->setDado( 'lote'                , $arItensLotes['lote']               );
                        $obTAlmoxarifadoPerecivel->recuperaPorChave($rsPerecivel);

                        if ($rsPerecivel->getNumLinhas() <= 0) {
                            $obTAlmoxarifadoPerecivel = new TAlmoxarifadoPerecivel;
                            $obTAlmoxarifadoPerecivel->setDado( 'cod_item'            , $arLancamentos['cod_item']          );
                            $obTAlmoxarifadoPerecivel->setDado( 'cod_marca'           , $arLancamentos['cod_marca']         );
                            $obTAlmoxarifadoPerecivel->setDado( 'cod_almoxarifado'    , $inCodAlmoxarifado                  );
                            $obTAlmoxarifadoPerecivel->setDado( 'cod_centro'          , $arLancamentos['cod_centro_destino']);
                            $obTAlmoxarifadoPerecivel->setDado( 'lote'                , $arItensLotes['lote']               );
                            $obTAlmoxarifadoPerecivel->setDado( 'dt_fabricacao'       , $arItensLotes['lote']               );
                            $obTAlmoxarifadoPerecivel->setDado( 'dt_validade'         , $arItensLotes['lote']               );
                            $obTAlmoxarifadoPerecivel->inclusao();
                        }
                        // inclusão na tabela lancamento_perecivel
                        $obTAlmoxarifadoLancamentoPerecivel = new TAlmoxarifadoLancamentoPerecivel;
                        $obTAlmoxarifadoLancamentoPerecivel->obTAlmoxarifadoLancamentoMaterial = &$obTAlmoxarifadoLancamentoMaterial;
                        $obTAlmoxarifadoLancamentoPerecivel->setDado ( 'cod_item'        , $arLancamentos['cod_item']          );
                        $obTAlmoxarifadoLancamentoPerecivel->setDado ( 'cod_marca'       , $arLancamentos['cod_marca']         );
                        $obTAlmoxarifadoLancamentoPerecivel->setDado ( 'cod_almoxarifado', $inCodAlmoxarifado );
                        $obTAlmoxarifadoLancamentoPerecivel->setDado ( 'lote'            , $arItensLotes['lote']               );
                        $obTAlmoxarifadoLancamentoPerecivel->setDado ( 'cod_centro'      , $arLancamentos['cod_centro_destino']        );
                        $obTAlmoxarifadoLancamentoPerecivel->setDado ( 'cod_marca'       , $arLancamentos['cod_marca']         );
                        $obTAlmoxarifadoLancamentoPerecivel->inclusao();

                        $TAlmoxarifadoTransferenciaAlmoxarifadoItemDestino->obTAlmoxarifadoLancamentoMaterial = &$obTAlmoxarifadoLancamentoMaterial;
                        $TAlmoxarifadoTransferenciaAlmoxarifadoItemDestino->setDado ( 'cod_transferencia', $arLancamentos['inCodTransferencia']   );
                        $TAlmoxarifadoTransferenciaAlmoxarifadoItemDestino->setDado ( 'exercicio', $arLancamentos['stExercicio']);
                        $TAlmoxarifadoTransferenciaAlmoxarifadoItemDestino->setDado ( 'cod_almoxarifado', $inCodAlmoxarifado );
                        $TAlmoxarifadoTransferenciaAlmoxarifadoItemDestino->setDado ( 'cod_marca', $arLancamentos['cod_marca'] );
                        $TAlmoxarifadoTransferenciaAlmoxarifadoItemDestino->setDado ( 'cod_centro', $arLancamentos['cod_centro'] );
                        $TAlmoxarifadoTransferenciaAlmoxarifadoItemDestino->setDado ( 'cod_centro_destino', $arLancamentos['cod_centro_destino'] );
                        $TAlmoxarifadoTransferenciaAlmoxarifadoItemDestino->setDado ( 'cod_item', $arLancamentos['cod_item'] );
                        $TAlmoxarifadoTransferenciaAlmoxarifadoItemDestino->setDado ( 'cod_lancamento'  , $inCodLancMat                       );
                        $TAlmoxarifadoTransferenciaAlmoxarifadoItemDestino->inclusao();
                    }
                }
            }
        }
    } elseif ($boQuantidadeInvalida) {
        SistemaLegado::exibeAviso("A soma da quantidade dos lotes do item ".$inCodItemInvalido." é diferente da quantidade total da transferência.","n_incluir","erro");
        exit;
    } elseif ($boItemInventario) {
        SistemaLegado::exibeAviso($stMensagemInventario,"n_incluir","erro");
        exit;
    }

    return array($arLancamentos['inCodTransferencia'], $inCodLancamento) ;

}

/**
*ProcessarTransferenciaSaida
*
*Executa o processo de saida por transferencia
*
**/
function ProcessarTransferenciaSaida($inCodAlmoxarifado)
{
    $inCodLancamento = 0;

    $obTAlmoxarifadoNaturezaLancamento = new TAlmoxarifadoNaturezaLancamento;

    $obTAlmoxarifadoNaturezaLancamento->setDado('tipo_natureza' , 'S');
    $obTAlmoxarifadoNaturezaLancamento->setDado('cod_natureza'  , 2  );

    # Recupera o num_lancamento considerando as configurações do Almoxarifado.
    $obTAlmoxarifadoNaturezaLancamento->recuperaNumNaturezaLancamento($rsNumLancamento);

    $inCodLancamento = $rsNumLancamento->getCampo('num_lancamento');

    $obTAlmoxarifadoNaturezaLancamento->setDado('num_lancamento' , $inCodLancamento      );
    $obTAlmoxarifadoNaturezaLancamento->setDado('cgm_almoxarife' , Sessao::read('numCgm'));
    $obTAlmoxarifadoNaturezaLancamento->setDado('numcgm_usuario' , Sessao::read('numCgm'));

    $obTAlmoxarifadoNaturezaLancamento->inclusao();

    $obTAlmoxarifadoLancamentoMaterial =  new TAlmoxarifadoLancamentoMaterial;
    $obTAlmoxarifadoLancamentoMaterial->obTAlmoxarifadoNaturezaLancamento = & $obTAlmoxarifadoNaturezaLancamento;

    $obTAlmoxarifadoTransferenciaAlmoxarifadoItem = new TAlmoxarifadoTransferenciaAlmoxarifadoItem;
    $TAlmoxarifadoTransferenciaAlmoxarifadoItemDestino = new TAlmoxarifadoTransferenciaAlmoxarifadoItemDestino;

    $arValores =  Sessao::read('Valores') ;

    $inCodItemInvalido = "";

    $valorMercadoUnitario = array();

    $stItemInventario = "";
    $contaItemInventario = 0;

    foreach ($arValores as $key => $arLancamentos) {
        $obTAlmoxarifadoInventarioItens = new TAlmoxarifadoInventarioItens();
        $stFiltro = " AND catalogo_item.cod_item = ".$arLancamentos['cod_item']."  \n";
        $obTAlmoxarifadoInventarioItens->recuperaItensInventarioPorClassificacao($rsItensInventarioPorClassificacao, $stFiltro);

        if (count($rsItensInventarioPorClassificacao->arElementos) > 0) {

            $boItemInventario = true;
            $contaItemInventario++;
            $stItemInventario .= $arLancamentos['cod_item'].",";
        }

        $quantidadeValoresItens = 0.0000;
        if ($arLancamentos['perecivel']) {

            foreach ($arLancamentos['ValoresLotes'] as $chave => $arValoresItem) {

                $valorItem =  str_replace('.','',$arValoresItem['quantidade']);
                $valorItem =  str_replace(',','.',$valorItem);

                $quantidadeValoresItens = $quantidadeValoresItens+$valorItem;
            }
            $quantidade = str_replace('.','',$arLancamentos['quantidade']);
            $quantidade = str_replace(',','.',$quantidade);

            if (($quantidadeValoresItens > $quantidade)||($quantidadeValoresItens < $quantidade)) {
                $boQuantidadeInvalida = true;
                $inCodItemInvalido = $arLancamentos['cod_item'];
            }
        }
    }

    // gerando mensagem para itens em inventario
    if (($boItemInventario)&&($contaItemInventario > 1)) {
        $stItens = substr($stItemInventario, 0, -1);
        $stMensagemInventario = "Os itens ".$stItens." estão em inventário.";
    } elseif (($boItemInventario)&&($contaItemInventario == 1)) {
        $stItem = substr($stItemInventario, 0, -1);
        $stMensagemInventario = "O item ".$stItem." está em inventário.";
    }

    if ((!$boQuantidadeInvalida) && (!$boItemInventario)) {

        foreach ($arValores as $key => $arLancamentos) {

            $obTAlmoxarifadoEstoqueMaterial = new TAlmoxarifadoEstoqueMaterial;
            $obTAlmoxarifadoEstoqueMaterial->setDado( 'cod_item'            , $arLancamentos['cod_item']  );
            $obTAlmoxarifadoEstoqueMaterial->setDado( 'cod_marca'           , $arLancamentos['cod_marca'] );
            $obTAlmoxarifadoEstoqueMaterial->setDado( 'cod_almoxarifado'    , $inCodAlmoxarifado          );
            $obTAlmoxarifadoEstoqueMaterial->setDado( 'cod_centro'          , $arLancamentos['cod_centro']);
            $obTAlmoxarifadoEstoqueMaterial->recuperaPorChave($rsEstoqueMaterial);
            if ($rsEstoqueMaterial->getNumLinhas() <= 0) {
                $obTAlmoxarifadoEstoqueMaterial = new TAlmoxarifadoEstoqueMaterial;
                $obTAlmoxarifadoEstoqueMaterial->setDado( 'cod_item'            , $arLancamentos['cod_item']  );
                $obTAlmoxarifadoEstoqueMaterial->setDado( 'cod_marca'           , $arLancamentos['cod_marca'] );
                $obTAlmoxarifadoEstoqueMaterial->setDado( 'cod_almoxarifado'    , $inCodAlmoxarifado          );
                $obTAlmoxarifadoEstoqueMaterial->setDado( 'cod_centro'          , $arLancamentos['cod_centro']);
                $obTAlmoxarifadoEstoqueMaterial->inclusao();
            }
            $rsAtributos = unserialize($arLancamentos['ValoresAtributos']);

            $obTAlmoxarifadoLancamentoMaterial->setDado( 'cod_item'            , $arLancamentos['cod_item']  );
            $obTAlmoxarifadoLancamentoMaterial->setDado( 'cod_marca'           , $arLancamentos['cod_marca'] );
            $obTAlmoxarifadoLancamentoMaterial->setDado( 'cod_almoxarifado'    , $inCodAlmoxarifado          );
            $obTAlmoxarifadoLancamentoMaterial->setDado( 'cod_centro'          , $arLancamentos['cod_centro']);

            if ($rsAtributos->getNumLinhas() > 0) {
                processaItemComAtributos($obTAlmoxarifadoLancamentoMaterial, $obTAlmoxarifadoTransferenciaAlmoxarifadoItem, $rsAtributos, $arLancamentos, $inCodAlmoxarifado, 'saida');
            } elseif ( count($arLancamentos['ValoresLotes'] ) <= 0 ) {

                $obTAlmoxarifadoLancamentoMaterial->proximoCod( $inCodLancMat );
                $obTAlmoxarifadoLancamentoMaterial->setDado( 'cod_lancamento' , $inCodLancMat);

                $stValor = str_replace('.','',$arLancamentos['quantidade'] );
                $stValor = str_replace(',','.', $stValor );

                $stValorUnitario = str_replace('.','',$arLancamentos['vl_unitario'] );
                $stValorUnitario = str_replace(',','.', $stValorUnitario );

                $valorTotal = $stValorUnitario * $stValor;

                $obTAlmoxarifadoLancamentoMaterial->setDado( 'quantidade', ( $stValor * -1 ) );
                $valorTotal = ($valorTotal * -1);

                $obTAlmoxarifadoLancamentoMaterial->setDado('valor_mercado', $valorTotal);

                $obTAlmoxarifadoLancamentoMaterial->inclusao();

                $obTAlmoxarifadoTransferenciaAlmoxarifadoItem->obTAlmoxarifadoLancamentoMaterial = &$obTAlmoxarifadoLancamentoMaterial;
                $obTAlmoxarifadoTransferenciaAlmoxarifadoItem->setDado ( 'cod_transferencia', $arLancamentos['inCodTransferencia']);
                $obTAlmoxarifadoTransferenciaAlmoxarifadoItem->setDado ( 'exercicio', $arLancamentos['stExercicio']               );
                $obTAlmoxarifadoTransferenciaAlmoxarifadoItem->setDado ( 'cod_almoxarifado', $inCodAlmoxarifado                   );
                $obTAlmoxarifadoTransferenciaAlmoxarifadoItem->setDado ( 'cod_marca', $arLancamentos['cod_marca']                 );
                $obTAlmoxarifadoTransferenciaAlmoxarifadoItem->setDado ( 'cod_centro', $arLancamentos['cod_centro']               );
                //$obTAlmoxarifadoTransferenciaAlmoxarifadoItem->setDado ( 'cod_centro_destino', $arLancamentos['cod_centro_destino']);
                $obTAlmoxarifadoTransferenciaAlmoxarifadoItem->setDado ( 'cod_item', $arLancamentos['cod_item']                   );
                $obTAlmoxarifadoTransferenciaAlmoxarifadoItem->setDado ( 'cod_lancamento'  , $inCodLancMat                        );
                $obTAlmoxarifadoTransferenciaAlmoxarifadoItem->inclusao();

            } else {
                foreach ($arLancamentos['ValoresLotes'] as $arItensLotes) {

                    $stValorLote = str_replace('.','',$arItensLotes['quantidade'] );
                    $stValorLote = str_replace(',','.', $stValorLote );

                    if ($stValorLote > 0) {

                        $obTAlmoxarifadoLancamentoMaterial->proximoCod( $inCodLancMat );
                        $obTAlmoxarifadoLancamentoMaterial->setDado( 'cod_lancamento' , $inCodLancMat);

                        $stValorUnitario = str_replace('.','',$arLancamentos['vl_unitario'] );
                        $stValorUnitario = str_replace(',','.', $stValorUnitario );

                        $valorTotal = $stValorUnitario * $stValorLote;

                        $obTAlmoxarifadoLancamentoMaterial->setDado( 'quantidade'     , ($stValorLote*-1) );
                        $valorTotal = ($valorTotal * -1);

                        $obTAlmoxarifadoLancamentoMaterial->setDado( 'valor_mercado', $valorTotal );

                        $obTAlmoxarifadoLancamentoMaterial->inclusao();

                        $obTAlmoxarifadoPerecivel = new TAlmoxarifadoPerecivel;
                        $obTAlmoxarifadoPerecivel->setDado( 'cod_item'            , $arLancamentos['cod_item']  );
                        $obTAlmoxarifadoPerecivel->setDado( 'cod_marca'           , $arLancamentos['cod_marca'] );
                        $obTAlmoxarifadoPerecivel->setDado( 'cod_almoxarifado'    , $inCodAlmoxarifado          );
                        $obTAlmoxarifadoPerecivel->setDado( 'cod_centro'          , $arLancamentos['cod_centro']);
                        $obTAlmoxarifadoPerecivel->setDado( 'lote'                , $arItensLotes['lote']       );
                        $obTAlmoxarifadoPerecivel->recuperaPorChave($rsPerecivel);

                        if ($rsPerecivel->getNumLinhas() <= 0) {
                            $obTAlmoxarifadoPerecivel = new TAlmoxarifadoPerecivel;
                            $obTAlmoxarifadoPerecivel->setDado( 'cod_item'            , $arLancamentos['cod_item']  );
                            $obTAlmoxarifadoPerecivel->setDado( 'cod_marca'           , $arLancamentos['cod_marca'] );
                            $obTAlmoxarifadoPerecivel->setDado( 'cod_almoxarifado'    , $inCodAlmoxarifado          );
                            $obTAlmoxarifadoPerecivel->setDado( 'cod_centro'          , $arLancamentos['cod_centro']);
                            $obTAlmoxarifadoPerecivel->setDado( 'lote'                , $arItensLotes['lote']       );
                            $obTAlmoxarifadoPerecivel->setDado( 'dt_fabricacao'       , $arItensLotes['lote']       );
                            $obTAlmoxarifadoPerecivel->setDado( 'dt_validade'         , $arItensLotes['lote']       );
                            $obTAlmoxarifadoPerecivel->inclusao();
                        }
                        // inclusão na tabela lancamento_perecivel
                        $obTAlmoxarifadoLancamentoPerecivel = new TAlmoxarifadoLancamentoPerecivel;
                        $obTAlmoxarifadoLancamentoPerecivel->obTAlmoxarifadoLancamentoMaterial = &$obTAlmoxarifadoLancamentoMaterial;
                        $obTAlmoxarifadoLancamentoPerecivel->setDado ( 'cod_item'        , $arLancamentos['cod_item']  );
                        $obTAlmoxarifadoLancamentoPerecivel->setDado ( 'cod_marca'       , $arLancamentos['cod_marca'] );
                        $obTAlmoxarifadoLancamentoPerecivel->setDado ( 'cod_almoxarifado', $inCodAlmoxarifado          );
                        $obTAlmoxarifadoLancamentoPerecivel->setDado ( 'lote'            , $arItensLotes['lote']       );
                        $obTAlmoxarifadoLancamentoPerecivel->setDado ( 'cod_centro'      , $arLancamentos['cod_centro']);
                        $obTAlmoxarifadoLancamentoPerecivel->setDado ( 'cod_marca'       , $arLancamentos['cod_marca'] );
                        $obTAlmoxarifadoLancamentoPerecivel->inclusao();

                        $obTAlmoxarifadoTransferenciaAlmoxarifadoItem->obTAlmoxarifadoLancamentoMaterial = &$obTAlmoxarifadoLancamentoMaterial;
                        $obTAlmoxarifadoTransferenciaAlmoxarifadoItem->setDado ( 'cod_transferencia' , $arLancamentos['inCodTransferencia']);
                        $obTAlmoxarifadoTransferenciaAlmoxarifadoItem->setDado ( 'exercicio'         , $arLancamentos['stExercicio']       );
                        $obTAlmoxarifadoTransferenciaAlmoxarifadoItem->setDado ( 'cod_almoxarifado'  , $inCodAlmoxarifado                  );
                        $obTAlmoxarifadoTransferenciaAlmoxarifadoItem->setDado ( 'cod_marca'         , $arLancamentos['cod_marca']         );
                        $obTAlmoxarifadoTransferenciaAlmoxarifadoItem->setDado ( 'cod_centro'        , $arLancamentos['cod_centro']        );
                        //$obTAlmoxarifadoTransferenciaAlmoxarifadoItem->setDado ( 'cod_centro_destino', $arLancamentos['cod_centro_destino']);
                        $obTAlmoxarifadoTransferenciaAlmoxarifadoItem->setDado ( 'cod_item'          , $arLancamentos['cod_item']          );
                        $obTAlmoxarifadoTransferenciaAlmoxarifadoItem->setDado ( 'cod_lancamento'    ,  $inCodLancMat                      );
                        $obTAlmoxarifadoTransferenciaAlmoxarifadoItem->inclusao();
                    }
                }
            }
        }
    } elseif ($boQuantidadeInvalida) {
        SistemaLegado::exibeAviso("A soma da quantidade dos lotes do item ".$inCodItemInvalido." é diferente da quantidade total da transferência.","n_incluir","erro");
        exit;
    } elseif ($boItemInventario) {
        SistemaLegado::exibeAviso($stMensagemInventario,"n_incluir","erro");
        exit;
    }

    return array($arLancamentos['inCodTransferencia'], $inCodLancamento) ;
}

/**
*processaItemComAtributos
*
*Executa as saidas e entradas com atributos para os itens
*
*/
function processaItemComAtributos(&$obTAlmoxarifadoLancamentoMaterial, &$obTAlmoxarifadoTransferenciaAlmoxarifadoItem, &$rsAtributos, $arLancamentos, $inCodAlmoxarifado, $stAcao)
{
    if ($rsAtributos->getNumLinhas()>0) {
        while (!$rsAtributos->eof()) {
            $obTAlmoxarifadoLancamentoMaterial->proximoCod( $inCodLancMat );
            $obTAlmoxarifadoLancamentoMaterial->setDado( 'cod_lancamento' , $inCodLancMat               );

            $stValor = $rsAtributos->getCampo('quantidade');

            $stValorUnitario = str_replace('.','',$arLancamentos['vl_unitario'] );
            $stValorUnitario = str_replace(',','.', $stValorUnitario );

            $valorTotal = $stValorUnitario * $stValor;

            if ($stAcao == 'saida') {
                $obTAlmoxarifadoLancamentoMaterial->setDado( 'quantidade'     , ($rsAtributos->getCampo('quantidade') *-1) );
                $valorTotal = ($valorTotal * -1);
                $stNomeCampoCentoCusto = 'cod_centro';
            } else {
                $obTAlmoxarifadoLancamentoMaterial->setDado( 'quantidade'     , $rsAtributos->getCampo('quantidade') );
                $stNomeCampoCentoCusto = 'cod_centro_destino';
            }

            $obTAlmoxarifadoLancamentoMaterial->setDado( 'valor_mercado', $valorTotal );

            $obTAlmoxarifadoLancamentoMaterial->inclusao();

            if ($stAcao == 'saida') {
                $obTAlmoxarifadoTransferenciaAlmoxarifadoItem->obTAlmoxarifadoLancamentoMaterial = &$obTAlmoxarifadoLancamentoMaterial;
                $obTAlmoxarifadoTransferenciaAlmoxarifadoItem->setDado ( 'cod_transferencia'  , $arLancamentos['inCodTransferencia']  );
                $obTAlmoxarifadoTransferenciaAlmoxarifadoItem->setDado ( 'exercicio'          , $arLancamentos['stExercicio']         );
                $obTAlmoxarifadoTransferenciaAlmoxarifadoItem->setDado ( 'cod_almoxarifado'   , $inCodAlmoxarifado                    );
                $obTAlmoxarifadoTransferenciaAlmoxarifadoItem->setDado ( 'cod_marca'          , $arLancamentos['cod_marca']           );
                $obTAlmoxarifadoTransferenciaAlmoxarifadoItem->setDado ( 'cod_centro'         , $arLancamentos['cod_centro']          );
                //$obTAlmoxarifadoTransferenciaAlmoxarifadoItem->setDado ( 'cod_centro_destino' , $arLancamentos['cod_centro_destino']  );
                $obTAlmoxarifadoTransferenciaAlmoxarifadoItem->setDado ( 'cod_item'           , $arLancamentos['cod_item']            );
                $obTAlmoxarifadoTransferenciaAlmoxarifadoItem->setDado ( 'cod_lancamento'     , $inCodLancMat                         );
                $obTAlmoxarifadoTransferenciaAlmoxarifadoItem->inclusao();
            } else {
                $TAlmoxarifadoTransferenciaAlmoxarifadoItemDestino->obTAlmoxarifadoLancamentoMaterial = &$obTAlmoxarifadoLancamentoMaterial;
                $TAlmoxarifadoTransferenciaAlmoxarifadoItemDestino->setDado ( 'cod_transferencia' , $arLancamentos['inCodTransferencia'] );
                $TAlmoxarifadoTransferenciaAlmoxarifadoItemDestino->setDado ( 'exercicio'         , $arLancamentos['stExercicio']                );
                $TAlmoxarifadoTransferenciaAlmoxarifadoItemDestino->setDado ( 'cod_almoxarifado'  , $inCodAlmoxarifado                    );
                $TAlmoxarifadoTransferenciaAlmoxarifadoItemDestino->setDado ( 'cod_marca'         , $arLancamentos['cod_marca']                  );
                $TAlmoxarifadoTransferenciaAlmoxarifadoItemDestino->setDado ( 'cod_centro'        , $arLancamentos['cod_centro']                );
                $TAlmoxarifadoTransferenciaAlmoxarifadoItemDestino->setDado ( 'cod_centro_destino', $arLancamentos['cod_centro_destino']);
                $TAlmoxarifadoTransferenciaAlmoxarifadoItemDestino->setDado ( 'cod_item'          , $arLancamentos['cod_item']                    );
                $TAlmoxarifadoTransferenciaAlmoxarifadoItemDestino->setDado ( 'cod_lancamento'    , $inCodLancMat                         );
                $TAlmoxarifadoTransferenciaAlmoxarifadoItemDestino->inclusao();
            }

            $arAtributos = explode(' - ', $rsAtributos->getCampo('cod_atributos'));
            $arValores = explode(' - ', $rsAtributos->getCampo('valor_atributos'));
            $i = 0;

            foreach ($arAtributos as $inCodAtributo) {
                $obTAlmoxarifadoAtributoEstoqueMaterialValor = new TAlmoxarifadoAtributoEstoqueMaterialValor;
                $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_modulo'      , 29);
                $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_cadastro'    , 2);
                $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_atributo'    ,$inCodAtributo);
                $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_item'        ,$arLancamentos['cod_item']);
                $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_centro'      ,$arLancamentos[$stNomeCampoCentoCusto]);
                $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_almoxarifado',$inCodAlmoxarifado );
                $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_lancamento'  ,$inCodLancMat);
                $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_marca'       ,$arLancamentos['cod_marca']);
                $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('valor'           ,$arValores[$i]);
                $obTAlmoxarifadoAtributoEstoqueMaterialValor->inclusao();
                $i++;
            }
            $rsAtributos->proximo();
        }
    }
}

?>
