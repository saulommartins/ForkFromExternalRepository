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
 * Formulario de Emissão de Cheques
 *
 * @category    Urbem
 * @package     Tesouraria
 * @author      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id:$
 */

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include CAM_GF_TES_CONTROLE . 'CTesourariaCheque.class.php';
include CAM_GF_TES_NEGOCIO . 'RTesourariaCheque.class.php';

$stAcao = $request->get('stAcao');

$pgOcul = 'OCManterEmitirCheque.php';

//Instancia o model e o controller
$obModel      = new RTesourariaCheque();
$obController = new CTesourariaCheque($obModel);

$arFiltro = Sessao::read('filtro');
if ($_POST OR $_GET['pg']) {
    foreach ($_REQUEST as $stCampo => $stValor) {
        $arFiltro['filtro'][$stCampo] = $stValor;
    }
    $boPaginando = true;

    Sessao::write('filtro',$arFiltro);
    Sessao::write('paginando',$boPaginando);
} else {
    foreach ($arFiltro['filtro'] AS $stKey=>$stValue) {
        $_REQUEST[$stKey] = $stValue;
    }
    $_GET['pg']  = $_REQUEST['pg' ];
    $_GET['pos'] = $_REQUEST['pos'];
}

//Lista de acordo com o filtro
$obController->listEmitirCheque($rsLista, $_REQUEST);

$rsLista->addFormatacao('valor'       , 'NUMERIC_BR');
$rsLista->addFormatacao('vl_retencao' , 'NUMERIC_BR');

$obLista = new Lista;

$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Entidade");
$obLista->ultimoCabecalho->setWidth( 25 );
$obLista->commitCabecalho();

switch ($_REQUEST['stTipoPagamento']) {
case 'ordem_pagamento':
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Nr.OP");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Credor");
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();

    break;
case 'transferencia':
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Conta Crédito");
    $obLista->ultimoCabecalho->setWidth(20);
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Conta Débito");
    $obLista->ultimoCabecalho->setWidth(20);
    $obLista->commitCabecalho();

    break;
default:
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Nr. Recibo Extra");
    $obLista->ultimoCabecalho->setWidth(10);
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Credor");
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();
    break;
}

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Valor");
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();

if ($_REQUEST['stTipoPagamento'] == 'ordem_pagamento') {
     $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Vl. Retenção");
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();
}

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_entidade" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();

switch ($_REQUEST['stTipoPagamento']) {
case 'ordem_pagamento':
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[cod_ordem]/[exercicio]" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "nom_credor" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();

    break;
case 'transferencia':
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[cod_plano_credito] - [nom_conta_credito]" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[cod_plano_debito] - [nom_conta_debito]" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();

    break;
default:
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[cod_recibo_extra]/[exercicio]" );
    $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "nom_credor" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();
    break;
}

$obLista->addDado();
$obLista->ultimoDado->setCampo( "valor" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();

if ($_REQUEST['stTipoPagamento'] == 'ordem_pagamento') {
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "vl_retencao" );
    $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
    $obLista->commitDado();
}

$obLista->addAcao();
$obLista->ultimaAcao->setAcao ($stAcao                            );
switch ($_REQUEST['stTipoPagamento']) {
case 'ordem_pagamento':
    $obLista->ultimaAcao->addCampo('&inCodOrdem'      , 'cod_ordem'  );
    $obLista->ultimaAcao->addCampo('&flValorRetencao' , 'vl_retencao');

    break;
case 'transferencia':
    $obLista->ultimaAcao->addCampo('&inCodPlanoCredito'   , 'cod_plano_credito');
    $obLista->ultimaAcao->addCampo('&stNomPlanoCredito'   , 'nom_conta_credito');
    $obLista->ultimaAcao->addCampo('&inCodPlanoDebito'    , 'cod_plano_debito' );
    $obLista->ultimaAcao->addCampo('&stNomPlanoDebito'    , 'nom_conta_debito' );
    $obLista->ultimaAcao->addCampo('&inCodLote'           , 'cod_lote'         );
    $obLista->ultimaAcao->addCampo('&stTipo'              , 'tipo'             );

    break;
default:
    $obLista->ultimaAcao->addCampo('&inCodReciboExtra'   , 'cod_recibo_extra');

    break;
}
$obLista->ultimaAcao->addCampo('&inCodEntidade', 'cod_entidade');
$obLista->ultimaAcao->addCampo('&stExercicio'  , 'exercicio'   );
$obLista->ultimaAcao->addCampo('&stNomCredor'  , 'nom_credor'  );
$obLista->ultimaAcao->addCampo('&stDataCheque' , 'data_cheque' );
$obLista->ultimaAcao->addCampo('&flValor'      , 'valor'       );
$obLista->ultimaAcao->setLink ('FMManterEmitirCheque.php?stAcao=' . $stAcao . '&stTipoEmissaoCheque=' . $_REQUEST['stTipoPagamento'] . '&' . Sessao::getId());

$obLista->commitAcao();
$obLista->show();

Sessao::write('inCodTerminal',$_REQUEST['inCodTerminal']);
Sessao::write('stTimestampTerminal',$_REQUEST['stTimestampTerminal']);

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
