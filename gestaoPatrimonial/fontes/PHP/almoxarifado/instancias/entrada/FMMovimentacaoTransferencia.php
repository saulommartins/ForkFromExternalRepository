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
    * Página de Formulario de Transferencia
    * Data de Criação: 05/01/2006

    * @author Analista:
    * @author Desenvolvedor: Leandro André Zis

    * @ignore

    * Casos de uso: uc-03.03.09

    $Id:$

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoPedidoTransferencia.class.php";
include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoPerecivel.class.php";
include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoPedidoTransferenciaItem.class.php";
include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoAtributoCatalogoItem.class.php";
include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoAtributoEstoqueMaterialValor.class.php";
include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoAtributoPedidoTransferenciaItem.class.php";
include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoLancamentoMaterial.class.php";

//Define a função do arquivo, ex: incluir ou alterar
$stAcao = $request->get('stAcao');

if(empty($stAcao))
    $stAcao = "incluir";

$stLink = "";
foreach ($_GET as $stCampo=>$stValor) {
    if ($stCampo != 'PHPSESSID' and $stCampo != 'iURLRandomica' and $stCampo != 'stAcao') {
        $stLink .= "&".$stCampo."=".$stValor;
    }
}

//Define o nome dos arquivos PHP
$stPrograma = "MovimentacaoTransferencia";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao.$stLink;
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

$obTAlmoxarifadoTransferencia = new TAlmoxarifadoPedidoTransferencia;
$obTAlmoxarifadoTransferencia->setDado('exercicio', $_REQUEST['stExercicio']);
$obTAlmoxarifadoTransferencia->setDado('cod_transferencia', $_REQUEST['inCodTransferencia']);
$obTAlmoxarifadoTransferencia->recuperaTransferencia($rsTransferencia);

$inCodigo = $rsTransferencia->getCampo('cod_transferencia');
$inCodAlmoxarifadoOrigem = $rsTransferencia->getCampo('cod_almoxarifado_origem');
$stNomAlmoxarifadoOrigem = $rsTransferencia->getCampo('nom_almoxarifado_origem');
$inCodAlmoxarifadoDestino = $rsTransferencia->getCampo('cod_almoxarifado_destino');
$stNomAlmoxarifadoDestino = $rsTransferencia->getCampo('nom_almoxarifado_destino');
$stObservacao  = $rsTransferencia->getCampo('observacao');

$obTAlmoxarifadoTransferenciaItens = new TAlmoxarifadoPedidoTransferenciaItem;
$obTAlmoxarifadoTransferenciaItens->setDado('exercicio', $_REQUEST['stExercicio']);
$obTAlmoxarifadoTransferenciaItens->setDado('cod_transferencia', $_REQUEST['inCodTransferencia']);
$obTAlmoxarifadoTransferenciaItens->recuperaPedidoItens($rsTransferenciaItens);

if($stAcao == 'entrada')
  $inCodAlmoxarifado = $inCodAlmoxarifadoDestino;
else
  $inCodAlmoxarifado = $inCodAlmoxarifadoOrigem;

Sessao::write('Valores',array());
$inPos = 0;

while (!$rsTransferenciaItens->eof()) {

    include_once( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCatalogoItem.class.php" );
    $obTAlmoxarifadoCatalogoItem = new TAlmoxarifadoCatalogoItem;
    $obTAlmoxarifadoCatalogoItem->setDado("cod_item", $rsTransferenciaItens->getCampo('cod_item'));
    $obTAlmoxarifadoCatalogoItem->recuperaPorChave($rsRecordSet);

    $boDisabled = $rsRecordSet->getCampo("ativo");
    $boDisabled = ($boDisabled == "") ? true : false;

    $arElementos = array();
    $arElementos['inId']                = $inPos++;;
    $arElementos['stExercicio']         = $_REQUEST['stExercicio'];
    $arElementos['inCodTransferencia']  = $_REQUEST['inCodTransferencia'];
    $arElementos['inCodAlmoxarifado']   = $inCodAlmoxarifado;
    $arElementos['cod_item']            = $rsTransferenciaItens->getCampo('cod_item');
    $arElementos['desc_item']           = $rsTransferenciaItens->getCampo('desc_item');
    $arElementos['cod_marca']           = $rsTransferenciaItens->getCampo('cod_marca');
    $arElementos['desc_marca']          = $rsTransferenciaItens->getCampo('desc_marca');
    $arElementos['cod_centro']          = $rsTransferenciaItens->getCampo('cod_centro');
    $arElementos['cod_centro_destino']  = $rsTransferenciaItens->getCampo('cod_centro_destino');
    $arElementos['desc_centro_destino'] = $rsTransferenciaItens->getCampo('desc_centro_destino');
    $arElementos['desc_centro']         = $rsTransferenciaItens->getCampo('desc_centro');
    $arElementos['desc_unidade']        = $rsTransferenciaItens->getCampo('desc_unidade');
    $arElementos['perecivel']           = $rsTransferenciaItens->getCampo('perecivel');
    $arElementos['disabled']            = $boDisabled;

    $inCodItem = $rsTransferenciaItens->getCampo('cod_item');

    $boUsarMarca = true;
    include_once ( TALM."TAlmoxarifadoCatalogoItemMarca.class.php" );
    $obTAlmoxarifadoCatalogoItemMarca = new TAlmoxarifadoCatalogoItemMarca;
    $stFiltro = " and acim.cod_item = ".$inCodItem;
    $stFiltro .= " and spfc.cod_almoxarifado = ".$inCodAlmoxarifado;

    $obTAlmoxarifadoCatalogoItemMarca->recuperaItemMarcaComSaldo( $rsMarcas, $stFiltro );
    $boUsarMarca = count($rsMarcas)-1;

    include_once(CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoEstoqueMaterial.class.php");
    $obTAlmoxarifadoEstoqueMaterial = new TAlmoxarifadoEstoqueMaterial;
    $stFiltro  = " and aem.cod_item = ".$inCodItem;
    if($boUsarMarca)
        $stFiltro .= " and aem.cod_marca = ".$rsTransferenciaItens->getCampo('cod_marca');
    if ($stAcao == 'entrada')
        $stFiltro .= " and aem.cod_centro= ".$rsTransferenciaItens->getCampo('cod_centro_destino');
    else
        $stFiltro .= " and aem.cod_centro= ".$rsTransferenciaItens->getCampo('cod_centro');

    $stFiltro .= " and aem.cod_almoxarifado = ".$inCodAlmoxarifado;

    $obTAlmoxarifadoEstoqueMaterial->recuperaSaldoEstoque($rsSaldo, $stFiltro);

    $arElementos['saldo_atual'] = number_format($rsSaldo->getCampo('saldo_estoque'), 4, ',', '.');
    $arElementos['quantidade']  = number_format($rsTransferenciaItens->getCampo('quantidade'),  4, ',', '.');

    if (($arElementos['saldo_atual'] - $arElementos['quantidade']) <0) { //caso algum item fique com saldo negativo, deverá impedir a nota de ser processada
      $boBloqueiaOk =  true;
    }

    // Recupera o valor unitário do Item (média de entradas X valores de todas as entradas)
    $obTAlmoxarifadoLancamentoMaterial = new TAlmoxarifadoLancamentoMaterial;
    $obTAlmoxarifadoLancamentoMaterial->setDado('cod_item', $rsTransferenciaItens->getCampo('cod_item'));
    $obTAlmoxarifadoLancamentoMaterial->recuperaSaldoValorUnitario($rsItemValorUnitario);
    $arElementos['vl_unitario'] = number_format($rsItemValorUnitario->getCampo('valor_unitario'), 4, ',', '.');

    $arElementos['ValoresLotes'] = array();

    $obTAlmoxarifadoPerecivel = new TAlmoxarifadoPerecivel;
    $obTAlmoxarifadoPerecivel->setDado('cod_item', $rsTransferenciaItens->getCampo('cod_item'));
    $obTAlmoxarifadoPerecivel->setDado('cod_marca', $rsTransferenciaItens->getCampo('cod_marca'));
    $obTAlmoxarifadoPerecivel->setDado('cod_centro', $rsTransferenciaItens->getCampo('cod_centro'));
    $obTAlmoxarifadoPerecivel->setDado('cod_almoxarifado', $inCodAlmoxarifadoOrigem);
    $obTAlmoxarifadoPerecivel->recuperaPereciveis($rsPerecivel,'', 'ORDER BY dt_validade');

    $inPosLotes=0;
    $nuQuantidade = $rsTransferenciaItens->getCampo('quantidade');
    $boPerecivel = false;

    while (!$rsPerecivel->eof()) {
        $obTAlmoxarifadoPerecivel = new TAlmoxarifadoPerecivel;
        $obTAlmoxarifadoPerecivel->setDado('cod_item', $rsTransferenciaItens->getCampo('cod_item'));
        $obTAlmoxarifadoPerecivel->setDado('cod_marca', $rsTransferenciaItens->getCampo('cod_marca'));
        $obTAlmoxarifadoPerecivel->setDado('cod_centro', $rsTransferenciaItens->getCampo('cod_centro'));
        $obTAlmoxarifadoPerecivel->setDado('lote', $rsPerecivel->getCampo('lote'));
        $obTAlmoxarifadoPerecivel->setDado('cod_almoxarifado', $inCodAlmoxarifadoOrigem);

        $stFiltro = " AND (cod_transferencia != ".$_REQUEST['inCodTransferencia']." OR cod_transferencia IS NULL)" ;

        $obTAlmoxarifadoPerecivel->recuperaSaldoLote($rsSaldoLote, $stFiltro);

        $nuSaldoLote = $rsSaldoLote->getCampo('saldo_lote');
        $nuQuantidadeLote = 0;
        if ($nuQuantidade > 0) {
             if ($nuSaldoLote >= $nuQuantidade) {
                  $nuQuantidadeLote = $nuQuantidade ;
                  $nuQuantidade = 0;
             } else {
                  $nuQuantidadeLote = $nuSaldoLote;
                  $nuQuantidade = $nuQuantidade - $nuSaldoLote;
             }
        }

        if (($stAcao == 'entrada') && ($nuQuantidadeLote > 0)) {

            $obTAlmoxarifadoLancamentoMaterial = new TAlmoxarifadoLancamentoMaterial();
            $obTAlmoxarifadoLancamentoMaterial->setDado( 'cod_item'            , $rsTransferenciaItens->getCampo('cod_item'));
            $obTAlmoxarifadoLancamentoMaterial->setDado( 'cod_marca'           , $rsTransferenciaItens->getCampo('cod_marca'));
            $obTAlmoxarifadoLancamentoMaterial->setDado( 'cod_almoxarifado'    , $inAlmoxarifadoOrigem );
            $obTAlmoxarifadoLancamentoMaterial->setDado( 'cod_centro'          , $rsTransferenciaItens->getCampo('cod_centro'));
            $stFiltro = "            AND tai.cod_transferencia = ".$_REQUEST['inCodTransferencia']."   \n";
            $stFiltro.= "            AND lp.lote  = ".$rsPerecivel->getCampo('lote')."   \n";
            $obTAlmoxarifadoLancamentoMaterial->recuperaAlmoxarifadoLancamentoMaterialValor($rsLancamentoMaterialValor, $stFiltro);

            $arLotes['inId']          = $inPosLotes++;

            if ($rsLancamentoMaterialValor->getCampo('lote') == '') {
                $arLotes['lote']      = $rsPerecivel->getCampo('lote');
            } else {
                $arLotes['lote']      = $rsLancamentoMaterialValor->getCampo('lote');
            }

            $arLotes['dt_validade']   = $rsPerecivel->getCampo('dt_validade');
            $arLotes['dt_fabricacao'] = $rsPerecivel->getCampo('dt_fabricacao');
            $arLotes['saldo']         = number_format($nuSaldoLote, 4, ',', '.');
            $arLotes['quantidade']    = number_format($rsLancamentoMaterialValor->getCampo('quantidade')*-1, 4, ',', '.');
            $arElementos['ValoresLotes'][] = $arLotes;
            $boPerecivel = true;

        } elseif ($nuQuantidadeLote > 0) {
            $arLotes['inId']          = $inPosLotes++;
            $arLotes['lote']          = $rsPerecivel->getCampo('lote');
            $arLotes['dt_validade']   = $rsPerecivel->getCampo('dt_validade');
            $arLotes['dt_fabricacao'] = $rsPerecivel->getCampo('dt_fabricacao');
            $arLotes['saldo']         = number_format($nuSaldoLote, 4, ',', '.');
            $arLotes['quantidade']    = number_format($nuQuantidadeLote, 4, ',', '.');
            $arElementos['ValoresLotes'][] = $arLotes;
            $boPerecivel = true;
        }
        $rsPerecivel->proximo();
    }

    $obTAlmoxarifadoAtributoPedidoTransferenciaItem = new TAlmoxarifadoAtributoPedidoTransferenciaItem;
    $obTAlmoxarifadoAtributoPedidoTransferenciaItem->setDado('cod_item', $rsTransferenciaItens->getCampo('cod_item'));
    $obTAlmoxarifadoAtributoPedidoTransferenciaItem->setDado('cod_marca', $rsTransferenciaItens->getCampo('cod_marca'));
    $obTAlmoxarifadoAtributoPedidoTransferenciaItem->setDado('cod_centro', $rsTransferenciaItens->getCampo('cod_centro'));
    $obTAlmoxarifadoAtributoPedidoTransferenciaItem->setDado('cod_transferencia', $rsTransferenciaItens->getCampo('cod_transferencia'));
    $obTAlmoxarifadoAtributoPedidoTransferenciaItem->setDado('exercicio', $_REQUEST['stExercicio']);
    $obTAlmoxarifadoAtributoPedidoTransferenciaItem->recuperaAtributosValores( $rsValoresAtributos);
    $obTAlmoxarifadoAtributoPedidoTransferenciaItem->recuperaAtributos( $rsAtributos );

    while (!$rsValoresAtributos->eof()) {
       $obTAlmoxarifadoAtributoEstoqueMaterialValor = new TAlmoxarifadoAtributoEstoqueMaterialValor;
       $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('valor_atributos', $rsValoresAtributos->getCampo('valor_atributos'));
       $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_item', $rsTransferenciaItens->getCampo('cod_item'));
       $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_marca', $rsTransferenciaItens->getCampo('cod_marca'));
       $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_centro', $rsTransferenciaItens->getCampo('cod_centro'));
       $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_almoxarifado', $inCodAlmoxarifadoOrigem);

       $obTAlmoxarifadoAtributoEstoqueMaterialValor->recuperaSaldo($rsSaldoAtributo);

       $rsValoresAtributos->setCampo('saldo', $rsSaldoAtributo->getCampo('saldo'));
       $rsValoresAtributos->proximo();
    }
    $rsValoresAtributos->setPrimeiroElemento();
    $arElementos['ValoresAtributos'] = serialize($rsValoresAtributos);
    $arElementos['stAtributos'] = $rsAtributos->getCampo('atributos');
    $boTemAtributos = $rsValoresAtributos->getNumLinhas() > 0;
    $arElementos['boDesabilitaDetalhar'] = !($boTemAtributos || $boPerecivel);
    $arElementos['perecivel'] = $boPerecivel;

    $arValoresSessao[] = $arElementos;

    $rsTransferenciaItens->proximo();
}

Sessao::write('Valores',$arValoresSessao);

include_once( $pgJS );

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnInCodSequencia = new Hidden;
$obHdnInCodSequencia->setName ( "inCodSequencia" );
$obHdnInCodSequencia->setValue( $inCodSequencia );

$obLblExercicio = new Label;
$obLblExercicio->setRotulo        ( "Exercício"                      );
$obLblExercicio->setName          ( "stExercicio"                    );
$obLblExercicio->setId            ( "stExercicio"                    );
$obLblExercicio->setValue         ( $_REQUEST['stExercicio']         );

$obLblCodigo = new Label;
$obLblCodigo->setRotulo        ( "Código"                      );
$obLblCodigo->setName          ( "inCodigo"                    );
$obLblCodigo->setId            ( "inCodigo"                    );
$obLblCodigo->setValue         ( $inCodigo);

$obHdninIDPos = new Hidden;
$obHdninIDPos->setName          ( "inIDPos" );
$obHdninIDPos->setId            ( "inIDPos" );

$obLblAlmoxarifadoOrigem = new Label;
$obHdnAlmoxarifadoOrigem = new Hidden;
$obHdnAlmoxarifadoOrigem->setName          ( "inAlmoxarifadoOrigem"                    );
$obHdnAlmoxarifadoOrigem->setId            ( "inAlmoxarifadoOrigem"                    );
$obHdnAlmoxarifadoOrigem->setValue         ( $inCodAlmoxarifadoOrigem );

$obLblAlmoxarifadoOrigem = new Label;
$obLblAlmoxarifadoOrigem->setRotulo        ( "Almoxarifado Origem"                );
$obLblAlmoxarifadoOrigem->setName          ( "stAlmoxarifadoOrigem"                    );
$obLblAlmoxarifadoOrigem->setId            ( "stAlmoxarifadoOrigem"                    );
$obLblAlmoxarifadoOrigem->setValue         ( $inCodAlmoxarifadoOrigem."-".$stNomAlmoxarifadoOrigem);

$obHdnAlmoxarifadoDestino = new Hidden;
$obHdnAlmoxarifadoDestino->setName          ( "inAlmoxarifadoDestino"                    );
$obHdnAlmoxarifadoDestino->setId            ( "inAlmoxarifadoDestino"                    );
$obHdnAlmoxarifadoDestino->setValue         ( $inCodAlmoxarifadoDestino );

$obLblAlmoxarifadoDestino = new Label;
$obLblAlmoxarifadoDestino->setRotulo        ( "Almoxarifado Destino"             );
$obLblAlmoxarifadoDestino->setName          ( "stAlmoxarifadoDestino"            );
$obLblAlmoxarifadoDestino->setId            ( "stAlmoxarifadoDestino"            );
$obLblAlmoxarifadoDestino->setValue         ( $inCodAlmoxarifadoDestino."-".$stNomAlmoxarifadoDestino);

$obLblObservacao = new Label;
$obLblObservacao->setRotulo        ( "Observação"                      );
$obLblObservacao->setName          ( "stObservação"                    );
$obLblObservacao->setId            ( "stObservação"                    );
$obLblObservacao->setValue         ( $stObservacao);

$obRdEntradaAutomatica = new SimNao;
$obRdEntradaAutomatica->setRotulo ("Entrada Automática");
$obRdEntradaAutomatica->setTitle  ("Informe se a entrada será efetuada automaticamente.");
$obRdEntradaAutomatica->setName   ("boEntradaAutomatica");
$obRdEntradaAutomatica->setId     ("boEntradaAutomatica");

$obSpnDadosItem = new Span();
$obSpnDadosItem->setId("spnDadosItem");

$obSpnListaItens = new Span();
$obSpnListaItens->setId("spnListaItens");

$obSpnListaLotes = new Span();
$obSpnListaLotes->setId("spnListaLotes");

$obFormulario = new Formulario;
$obFormulario->addForm          ( $obForm              );
$obFormulario->addHidden        ( $obHdnAcao           );
$obFormulario->addHidden        ( $obHdnCtrl           );
$obFormulario->addHidden        ( $obHdnInCodSequencia );
$obFormulario->addHidden        ( $obHdninIDPos        );

$obFormulario->addTitulo        ( "Dados da Transferência" );
$obFormulario->addComponente ( $obLblExercicio    );
$obFormulario->addComponente ( $obLblCodigo       );
$obFormulario->addComponente ( $obLblAlmoxarifadoOrigem );
$obFormulario->addHidden     ( $obHdnAlmoxarifadoOrigem );
$obFormulario->addComponente ( $obLblAlmoxarifadoDestino );
$obFormulario->addHidden     ( $obHdnAlmoxarifadoDestino );
$obFormulario->addComponente ( $obLblObservacao   );
if ($stAcao == "saida") {
   $obFormulario->addComponente ( $obRdEntradaAutomatica   );
}

$obFormulario->addSpan ( $obSpnListaItens );
$obFormulario->addSpan ( $obSpnDadosItem );
$obFormulario->addSpan ( $obSpnListaLotes );

if ($stAcao == "incluir") {
   $obFormulario->OK(true);
} else {
   $obFormulario->Cancelar( $pgList, true);
}

$obFormulario->show();

# Validação para só montar a listagem de itens, se realmente tiver itens.
if (count($arValoresSessao) > 0) {
   $jsOnLoad = "montaParametrosGET('preencheSpanListaItens')";
   if ($boBloqueiaOk) {
      $stJs = "<script type='text/javascript'>";
      $stJs.= "alertaAviso('Não é possível processar a Nota de Transferência pois existe item sem saldo.','form','erro','".Sessao::getId()."');";
      $stJs.= "jQuery('#Ok').attr('disabled', 'disabled')";
      $stJs.= "</script>";
      echo $stJs;
   }
} else {
    $jsOnLoad = "jQuery('#Ok').attr('disabled', 'disabled')";
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
