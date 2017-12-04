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
  * Página de Formulario para Nota de Transferência
  * Data de criação : 17/04/2006

  * @author Analista   : Diego Victoria
  * @author Programador: Rodrigo

  * @ignore

  Caso de uso: uc-03.03.08

  $Id:$

  **/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GP_ALM_MAPEAMENTO. "TAlmoxarifadoPedidoTransferencia.class.php"                    );
include_once(CAM_GP_ALM_MAPEAMENTO. "TAlmoxarifadoEstoqueMaterial.class.php"                        );
include_once(CAM_GP_ALM_MAPEAMENTO. "TAlmoxarifadoPedidoTransferenciaItem.class.php"                );
include_once(CAM_GP_ALM_MAPEAMENTO. "TAlmoxarifadoAtributoPedidoTransferenciaItemValor.class.php"   );
include_once(CAM_GP_ALM_NEGOCIO . "RAlmoxarifadoAlmoxarifado.class.php"                             );
include_once(CAM_GP_ALM_NEGOCIO . "RAlmoxarifadoPermissaoCentroDeCustos.class.php"                  );
include_once(CAM_GP_ALM_COMPONENTES."IMontaItemQuantidade.class.php");

$stAcao = $request->get('stAcao');
$obRAlmoxarifado = new RAlmoxarifadoAlmoxarifado;
$obRAlmoxarifado->listar($rsAlmoxarifados);

if ($stAcao != 'incluir') {
    $obTAlmoxarifadoPedidoTransferencia = new TAlmoxarifadoPedidoTransferencia;
    $obTAlmoxarifadoPedidoTransferencia->setDado ('exercicio', $_GET['stExercicio']);
    $obTAlmoxarifadoPedidoTransferencia->setDado ('cod_transferencia', $_GET['inCodTransferencia']);
    $obTAlmoxarifadoPedidoTransferencia->recuperaTransferencias($rsPedido);

    $inCodigo = $rsPedido->getCampo('cod_transferencia');
    $inCodAlmoxarifadoOrigem = $rsPedido->getCampo('cod_almoxarifado_origem');
    $inCodAlmoxarifadoDestino = $rsPedido->getCampo('cod_almoxarifado_destino');
    $stObservacao = $rsPedido->getCampo('observacao');

    $obTAlmoxarifadoPedidoTransferenciaItem = new TAlmoxarifadoPedidoTransferenciaItem;
    $stFiltro = " where exercicio = '".$_GET['stExercicio']."'";
    $stFiltro .=  " and cod_transferencia = ".$_GET['inCodTransferencia'];
    $obTAlmoxarifadoPedidoTransferenciaItem->recuperaPedidoItens($rsPedidoItens, $stFiltro);
    $inCount = 0;
    while (!$rsPedidoItens->eof()) {
       $arTmp = array();
       $arTmp['id'              ] = $inCount+1;
       $arTmp['cod_item'        ] = $rsPedidoItens->getCampo('cod_item');
       $arTmp['cod_centro'      ] = $rsPedidoItens->getCampo('cod_centro');
       $arTmp['cod_marca'       ] = $rsPedidoItens->getCampo('cod_marca');
       $arTmp['quantidade'      ] = $rsPedidoItens->getCampo('quantidade');
       $arTmp['descricao_item'  ] = $rsPedidoItens->getCampo('desc_item');
       $arTmp['descricao_marca' ] = $rsPedidoItens->getCampo('desc_marca');
       $arTmp['descricao_centro'] = $rsPedidoItens->getCampo('desc_centro');
       $arTmp['cod_centro_destino'] = $rsPedidoItens->getCampo('cod_centro_destino');

        $obTEstoqueMaterial = new TAlmoxarifadoEstoqueMaterial;
        $stFiltro = "  and alm.cod_marca = ". $rsPedidoItens->getCampo('cod_marca');
        $stFiltro .= " and alm.cod_centro = ". $rsPedidoItens->getCampo('cod_centro');
        $stFiltro .= " and alm.cod_almoxarifado = ".$inCodAlmoxarifadoOrigem;
        $stFiltro .= " and alm.cod_item = ". $rsPedidoItens->getCampo('cod_item');
        $stFiltro .= " group by alm.cod_item ";
        $stOrdem   = " order by alm.cod_item ";
        $obErro = $obTEstoqueMaterial->recuperaSaldoEstoque( $rsRecordSet, $stFiltro );
        $arTmp['saldo'] = $rsRecordSet->getCampo( 'saldo_estoque' );
        $obTAlmoxarifadoAtributoPedidoTransferenciaItemValor = new TAlmoxarifadoAtributoPedidoTransferenciaItemValor;
        $obTAlmoxarifadoAtributoPedidoTransferenciaItemValor->setDado( 'exercicio', $_GET['stExercicio'] );
        $obTAlmoxarifadoAtributoPedidoTransferenciaItemValor->setDado( 'cod_transferencia', $inCodigo );
        $obTAlmoxarifadoAtributoPedidoTransferenciaItemValor->setDado( 'cod_item',   $rsPedidoItens->getCampo('cod_item') );
        $obTAlmoxarifadoAtributoPedidoTransferenciaItemValor->setDado( 'cod_marca',  $rsPedidoItens->getCampo('cod_marca') );
        $obTAlmoxarifadoAtributoPedidoTransferenciaItemValor->setDado( 'cod_centro', $rsPedidoItens->getCampo('cod_centro') );
        $obTAlmoxarifadoAtributoPedidoTransferenciaItemValor->recuperaValoresAtributoPedidoTransferencia( $rsAtributos );

        if ( $rsAtributos->getNumLinhas() > 0 ) {
            $inCodSequencial = "";
            $inIdValoresAtributos = -1;
            $inIdAtributos = -1;
            foreach ($rsAtributos->arElementos as $atributo) {
                if ($inCodSequencial != $atributo['cod_sequencial']) {
                    $inCodSequencial = $atributo['cod_sequencial'];
                    $inIdValoresAtributos += 1;
                    $inIdAtributos = 0;
                    $arTmp['valores_atributos'][$inIdValoresAtributos] = array();
                    $arTmp['valores_atributos'][$inIdValoresAtributos]['atributo'] = array();

                    if ($inIdValoresAtributos > 0) {
                        $arTmp['valores_atributos'][$inIdValoresAtributos-1]['stValoresAtributos'] = substr( $arTmp['valores_atributos'][$inIdValoresAtributos-1]['stValoresAtributos'], 3 );
                    }
                } else {
                    $inIdAtributos += 1;
                }
                $arTmp['valores_atributos'][$inIdValoresAtributos]['inId'] = $inIdValoresAtributos;
                $arTmp['valores_atributos'][$inIdValoresAtributos]['atributo'][$inIdAtributos]['cod_atributo'] = $atributo['cod_atributo'];
                $arTmp['valores_atributos'][$inIdValoresAtributos]['atributo'][$inIdAtributos]['nom_atributo'] = $atributo['nom_atributo'];
                $arTmp['valores_atributos'][$inIdValoresAtributos]['atributo'][$inIdAtributos]['valor'] = $atributo['valor'];
                $arTmp['valores_atributos'][$inIdValoresAtributos]['stValoresAtributos'] .= " - ".$atributo['valor'];
                $arTmp['valores_atributos'][$inIdValoresAtributos]['quantidade'] = $atributo['quantidade'];

            }
       }
       $arTmp['valores_atributos'][$inIdValoresAtributos]['stValoresAtributos'] = substr( $arTmp['valores_atributos'][$inIdValoresAtributos]['stValoresAtributos'], 3 );
       $arTemporario[] = $arTmp;
       $rsPedidoItens->proximo();
       $inCount++;
    }
    Sessao::write('arItens',$arTemporario);
}

//Define o nome dos arquivos PHP
$stPrograma = "ManterNotaTransferencia";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

include_once ($pgJS);

//DEFINICAO DOS COMPONENTES DO FORMULARIO
$obForm = new Form;
$obForm->setAction($pgProc );
$obForm->setTarget('oculto');

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ("stCtrl");
$obHdnCtrl->setValue($stCtrl );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ("stAcao");
$obHdnAcao->setValue ($stAcao );

$obHdnValidaAlmoxarifados = new HiddenEval;
$obHdnValidaAlmoxarifados->setName ("stValida");
$obHdnValidaAlmoxarifados->setValue("");

$obHdnIdItem = new Hidden;
$obHdnIdItem->setName ("inIdItem");

$obHdnLblAlmoxarifadoOrigem = new Hidden;
$obHdnLblAlmoxarifadoOrigem->setName ("LblAlmoxarifadoOrigem");

$obHdnLblAlmoxarifadoDestino = new Hidden;
$obHdnLblAlmoxarifadoDestino->setName ("LblAlmoxarifadoDestino");

$stExercicio =  $_REQUEST['stExercicio'] ? $_REQUEST['stExercicio'] : Sessao::getExercicio();

$obHdnExercicio = new Hidden;
$obHdnExercicio->setName  ("stExercicio");
$obHdnExercicio->setValue ($stExercicio );

$obLblExercicio = new Label;
$obLblExercicio->setRotulo('Exercício'       );
$obLblExercicio->setId    ('stExercicio'     );
$obLblExercicio->setValue ($stExercicio);

if ($stAcao != 'incluir') {
   $obLblCodigo = new Label;
   $obLblCodigo->setRotulo ('Código'           );
   $obLblCodigo->setId     ('stCodigo'         );
   $obLblCodigo->setValue  ($inCodigo         );

   $obHdnCodigo = new Hidden;
   $obHdnCodigo->setName   ('inCodTransferencia');
   $obHdnCodigo->setValue  ($inCodigo);

}

$obSpnAlmoxarifado = new Span;
$obSpnAlmoxarifado->setId ("spnAlmoxarifado");
if ($stAcao == 'incluir') {

  SistemaLegado::executaFramePrincipal("buscaValor('montaAlmoxarifados');");
} else {
  SistemaLegado::executaFramePrincipal("montaAlmoxarifadoLabel(".$inCodAlmoxarifadoOrigem.",".$inCodAlmoxarifadoDestino.");");
}

$obTxtObservacao = new TextArea;
$obTxtObservacao->setName  ("stObservacao"              );
$obTxtObservacao->setRotulo("Observação"                );
$obTxtObservacao->setTitle ("Informe a observação.");
$obTxtObservacao->setCols  (30                          );
$obTxtObservacao->setRows  (5                           );
$obTxtObservacao->setValue ( $stObservacao               );

$obHdnCodAlmoxarifadoOrigem = new Hidden;
$obHdnCodAlmoxarifadoOrigem->setName ('inCodAlmoxarifadoOrigem');

Sessao::write('stAcaoTela', 'IncluirNotaTransferencia');
$obBscItem = new IMontaItemQuantidade($obForm, $obHdnCodAlmoxarifadoOrigem);
$obBscItem->obIMontaItemUnidade->obIPopUpCatalogoItem->setObrigatorioBarra(true);
$obBscItem->obIMontaItemUnidade->obIPopUpCatalogoItem->setAlmoxarifadoOrigem($obHdnCodAlmoxarifadoOrigem);
$obBscItem->obIMontaItemUnidade->obIPopUpCatalogoItem->setComSaldo(true);
$obBscItem->obTxtQuantidade->setObrigatorioBarra(true);
$obBscItem->obCmbCentroCusto->setObrigatorioBarra(true);
$obBscItem->obCmbCentroCusto->obEvento->setOnChange( "buscaValor('mostraSaldo'); " );
$obBscItem->obCmbMarca->setObrigatorioBarra(true);

$obCmbCentroCustoDestino = new Select;
$obCmbCentroCustoDestino->setRotulo    ( 'Centro de Custo Destino' );
$obCmbCentroCustoDestino->setTitle     ( 'Selecione o centro de custo de destino do item.');
$obCmbCentroCustoDestino->setName      ( 'inCodCentroCustoDestino' );
$obCmbCentroCustoDestino->setID        ( 'inCodCentroCustoDestino' );
$obCmbCentroCustoDestino->setCampoID   ( 'cod_centro'  );
$obCmbCentroCustoDestino->setCampoDesc ( 'descricao'  );
$obCmbCentroCustoDestino->addOption    ( "", "Selecione" );
$obCmbCentroCustoDestino->setNull(true);

$obBtnIncluir = new Button;
$obBtnIncluir->setName ("btnIncluir");
$obBtnIncluir->setValue("Incluir"   );
$obBtnIncluir->setTipo ("button"    );
$obBtnIncluir->obEvento->setOnClick("buscaValor('incluirItem');");

$obBtnLimpar = new Button;
$obBtnLimpar->setName ("btnLimpar");
$obBtnLimpar->setValue("Limpar"   );
$obBtnLimpar->setTipo ("button"   );
$obBtnLimpar->obEvento->setOnClick("limparItem();");

$obSpnItens = new Span;
$obSpnItens->setId("spnItens");

$obFormulario = new Formulario;

$obFormulario->addTitulo    ('Dados da Nota de Transferência'                           );
$obFormulario->addForm      ($obForm                                         );
//$obFormulario->setAjuda             ("UC-03.03.08");
$obFormulario->addHidden    ($obHdnAcao                                      );
$obFormulario->addHidden    ($obHdnCtrl                                      );
if ($stAcao != 'incluir') {
   $obFormulario->addHidden    ($obHdnCodigo                                    );
}
$obFormulario->addHidden    ($obHdnIdItem                                    );
$obFormulario->addHidden    ($obHdnLblAlmoxarifadoOrigem                     );
$obFormulario->addHidden    ($obHdnLblAlmoxarifadoDestino                    );
$obFormulario->addHidden    ($obHdnValidaAlmoxarifados, true                 );
$obFormulario->addHidden    ($obHdnExercicio                                 );
if ($stAcao != 'incluir') {
   $obFormulario->addComponente($obLblCodigo                                    );
}
$obFormulario->addComponente($obLblExercicio                                 );
$obFormulario->addSpan      ($obSpnAlmoxarifado                              );
$obFormulario->addComponente($obTxtObservacao                                );
$obFormulario->addTitulo    ('Dados do Ítem'                                 );
$obBscItem->geraFormulario($obFormulario);
$obFormulario->addTitulo    ('Centro de Custo Destino'                       );
$obFormulario->addComponente($obCmbCentroCustoDestino                        );
$obFormulario->defineBarra  (array( $obBtnIncluir, $obBtnLimpar), "left", "" );
$obFormulario->addSpan      ($obSpnItens                                     );

if ($stAcao == "incluir") {
   $obOk = new Ok(true);
   $obLimpar = new Limpar;
   $obLimpar->obEvento->setOnClick("buscaValor('limpaFormulario');");
   $obFormulario->defineBarra(array($obOk, $obLimpar));
} else {
   $stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao;
   $obFormulario->Cancelar( $stLocation,true);
}

$obFormulario->Show();

$rsAlmoxarifados->bof();

while ( !$rsAlmoxarifados->eof() ) {
    if ( $rsAlmoxarifados->getCampo( 'padrao' ) == 't' ) {
        $stJs = 'document.frm.inCodAlmoxarifado.value = '.$rsAlmoxarifados->getCampo( 'codigo' );
    }
    $rsAlmoxarifados->proximo();
}
