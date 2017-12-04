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
include_once (CAM_GP_ALM_MAPEAMENTO. "TAlmoxarifadoPedidoTransferencia.class.php"                    );
include_once (CAM_GP_ALM_MAPEAMENTO. "TAlmoxarifadoEstoqueMaterial.class.php"                        );
include_once (CAM_GP_ALM_MAPEAMENTO. "TAlmoxarifadoPedidoTransferenciaItem.class.php"                );
include_once (CAM_GP_ALM_NEGOCIO . "RAlmoxarifadoAlmoxarifado.class.php"                             );
include_once (CAM_GP_ALM_NEGOCIO . "RAlmoxarifadoPermissaoCentroDeCustos.class.php"                  );
include_once (CAM_GP_ALM_COMPONENTES."IMontaItemUnidade.class.php");

include_once 'OCManterNotaTransferencia.php';

Sessao::write('arItens',array());
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
    $stNomAlmoxarifadoOrigem = $rsPedido->getCampo('nom_almoxarifado_origem');
    $inCodAlmoxarifadoDestino = $rsPedido->getCampo('cod_almoxarifado_destino');
    $stNomAlmoxarifadoDestino = $rsPedido->getCampo('nom_almoxarifado_destino');
    $stObservacao = $rsPedido->getCampo('observacao');

    $obTAlmoxarifadoPedidoTransferenciaItem = new TAlmoxarifadoPedidoTransferenciaItem;
    $stFiltro = " where exercicio = '".$_GET['stExercicio']."'";
    $stFiltro .=  ' and cod_transferencia = '.$_GET['inCodTransferencia'];
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
        $obTEstoqueMaterial = new TAlmoxarifadoEstoqueMaterial;
        $stFiltro = "";
        $stFiltro .= " and aem.cod_marca = ". $rsPedidoItens->getCampo('cod_marca');
        $stFiltro .= " and aem.cod_centro = ". $rsPedidoItens->getCampo('cod_centro');
        $stFiltro .= " and aem.cod_almoxarifado = ". $inCodAlmoxarifadoOrigem;
        $stFiltro .= " and aem.cod_item = ". $rsPedidoItens->getCampo('cod_item');
        $stFiltro .= " group by aem.cod_item ";
        $stOrdem   = " order by aem.cod_item ";
        $obErro = $obTEstoqueMaterial->recuperaSaldoEstoque( $rsRecordSet, $stFiltro );
        $arTmp['saldo'] = $rsRecordSet->getCampo( 'saldo_estoque' );

        $arTemporario[] = $arTmp;
        $rsPedidoItens->proximo();
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
$stExercicio ="";
if (isset($_REQUEST["stExercicio"]) && trim($_REQUEST["stExercicio"]) != "") {
    $stExercicio = $_REQUEST["stExercicio"];
} else {
    $stExercicio = Sessao::getExercicio();
}

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

$obHdnExercicio = new Hidden;
$obHdnExercicio->setName ("stExercicio");
$obHdnExercicio->setValue($stExercicio);

$obLblExercicio = new Label;
$obLblExercicio->setRotulo('Exercício'       );
$obLblExercicio->setId    ('stExercicio'     );
$obLblExercicio->setValue ($stExercicio);

$obLblCodigo = new Label;
$obLblCodigo->setRotulo ('Código');
$obLblCodigo->setId     ('stCodigo');
$obLblCodigo->setValue  ($inCodigo);

$obHdnCodigo = new Hidden;
$obHdnCodigo->setId('inCodTransferencia');
$obHdnCodigo->setName('inCodTransferencia');
$obHdnCodigo->setValue($inCodigo);

$obLblAlmoxarifadoOrigem = new Label;
$obLblAlmoxarifadoOrigem->setRotulo('Almoxarifado de Origem');
$obLblAlmoxarifadoOrigem->setId    ('stAlmoxarifadoOrigem');
$obLblAlmoxarifadoOrigem->setValue($inCodAlmoxarifadoOrigem.'-'.$stNomAlmoxarifadoOrigem);

$obLblAlmoxarifadoDestino = new Label;
$obLblAlmoxarifadoDestino->setRotulo('Almoxarifado de Destino');
$obLblAlmoxarifadoDestino->setId    ('stAlmoxarifadoDestino');
$obLblAlmoxarifadoDestino->setValue($inCodAlmoxarifadoDestino.'-'.$stNomAlmoxarifadoDestino);

$obLblObservacao = new Label;
$obLblObservacao->setRotulo ('Observação');
$obLblObservacao->setId     ('stObservacao');
$obLblObservacao->setValue ( $stObservacao               );

if (Sessao::read('arItens') != null) {
    foreach (Sessao::read('arItens') as $chave => $arItem) {
        $arItens[$chave]['cod_item'] = $arItem['cod_item'];
        $arItens[$chave]['cod_centro'] = $arItem['cod_centro'];
        $arItens[$chave]['cod_marca'] = $arItem['cod_marca'];
        $arItens[$chave]['descricao_item'] = $arItem['descricao_item'];
        $arItens[$chave]['descricao_marca'] = $arItem['descricao_marca'];
        $arItens[$chave]['descricao_centro'] = $arItem['descricao_centro'];
        $arItens[$chave]['quantidade'] = number_format($arItem['quantidade'],4,',','.');
        $arItens[$chave]['saldo'] = number_format($arItem['saldo'],4,',','.');
    }
}
$stHTML =  montaListaItens( $arItens, false);

$obSpnItem = new Span;
$obSpnItem->setValue ($stHTML);

$obFormulario = new Formulario;

$obFormulario->addTitulo    ('Dados da Transferência'                        );
$obFormulario->addForm      ($obForm                                         );
$obFormulario->addHidden    ($obHdnAcao                                      );
$obFormulario->addHidden    ($obHdnCtrl                                      );
$obFormulario->addHidden    ($obHdnExercicio                                 );
$obFormulario->addComponente($obLblExercicio                                 );
$obFormulario->addComponente($obLblCodigo                                    );
$obFormulario->addHidden    ($obHdnCodigo                                    );
$obFormulario->addComponente($obLblAlmoxarifadoOrigem                        );
$obFormulario->addComponente($obLblAlmoxarifadoDestino                       );
$obFormulario->addComponente($obLblObservacao                                );
$stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao;
$obFormulario->addSpan ( $obSpnItem );
$obFormulario->Cancelar( $stLocation );

$obFormulario->Show();
