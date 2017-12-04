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
    * Página de Listagem de Pagamentos
    * Data de Criação   : 25/10/2005

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Id: LSManterPagamento.php 59612 2014-09-02 12:00:51Z gelson $

    $Revision: 32130 $
    $Name$
    $Autor:$
    $Date: 2007-07-27 19:04:53 -0300 (Sex, 27 Jul 2007) $

    * Casos de uso: uc-02.04.05
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php"    );

//Define o nome dos arquivos PHP
$stPrograma = "ManterPagamento";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$obRTesourariaBoletim = new RTesourariaBoletim();
$rsLista = new RecordSet;

if ( !Sessao::read('paginando') ) {
    foreach ($_POST as $stCampo => $stValor) {
        $arFiltro[$stCampo] = $stValor;
    }
    Sessao::write('pg', $_GET['pg'] ? $_GET['pg'] : 0);
    Sessao::write('pos',$_GET['pos'] ? $_GET['pos'] : 0);
    Sessao::write('paginando', true);
    Sessao::write('filtro', $arFiltro);
} else {
    Sessao::write('pg', $_GET['pg']);
    Sessao::write('pos',$_GET['pos']);
    $arFiltro = Sessao::read('filtro');
    $_REQUEST['inCodEmpenhoInicial'] = $arFiltro['inCodEmpenhoInicial'];
    $_REQUEST['inCodEmpenhoFinal'  ] = $arFiltro['inCodEmpenhoFinal'  ];
    $_REQUEST['inCodNotaInicial'   ] = $arFiltro['inCodNotaInicial'   ];
    $_REQUEST['inCodNotaFinal'     ] = $arFiltro['inCodNotaFinal'     ];
    $_REQUEST['inCodOrdemInicial'  ] = $arFiltro['inCodOrdemInicial'  ];
    $_REQUEST['inCodOrdemFinal'    ] = $arFiltro['inCodOrdemFinal'    ];
    $_REQUEST['stExercicioEmpenho' ] = $arFiltro['stExercicioEmpenho' ];
    $_REQUEST['inNumCgm'           ] = $arFiltro['inNumCgm'           ];
    $_REQUEST['inCodEntidade'      ] = $arFiltro['inCodEntidade'      ];
    $_REQUEST['inCodTerminal'      ] = $arFiltro['inCodTerminal'      ];
    $_REQUEST['stTimestampTerminal'] = $arFiltro['stTimestampTerminal'];
    $_REQUEST['stTimestampUsuario' ] = $arFiltro['stTimestampUsuario' ];
    $_REQUEST['stAcao'             ] = $arFiltro['stAcao'             ];
}
// Foi neecessário reescrever sobre o Objeto Request para receber os novos parametros.
$request = new Request($_REQUEST);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');

//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'incluir'  : $pgProx = $pgForm; break;
    case 'consultar': $pgProx = $pgForm; break;
    default         : $pgProx = $pgForm;
}

if ( is_array( $_REQUEST['inCodEntidade'] ) ) {
    $stCodEntidade = implode( ',', $_REQUEST['inCodEntidade'] );
}

$obRTesourariaBoletim->setExercicio( Sessao::getExercicio()        );
$obRTesourariaBoletim->addPagamento();
$obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->addNotaLiquidacao();
$obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $stCodEntidade );;
$obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->roREmpenhoEmpenho->obRCGM->setNumCgm( $_REQUEST['inNumCgm'] );
$obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->roREmpenhoEmpenho->setExercicio( $_REQUEST['stExercicioEmpenho'] );
$obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->roREmpenhoEmpenho->setCodEmpenhoInicial( $_REQUEST['inCodEmpenhoInicial'] );
$obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->roREmpenhoEmpenho->setCodEmpenhoFinal( $_REQUEST['inCodEmpenhoFinal'] );
$obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->setCodNotaInicial( $_REQUEST['inCodNotaInicial'] );
$obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->setCodNotaFinal( $_REQUEST['inCodNotaFinal'] );
$obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->setCodigoOrdemInicial( $_REQUEST['inCodOrdemInicial'] );
$obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->setCodigoOrdemFinal( $_REQUEST['inCodOrdemFinal'] );
$obRTesourariaBoletim->obRTesourariaConfiguracao->consultarTesouraria( $boTransacao );

if ($stAcao == 'incluir') {
    $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->listarLiquidacaoNaoPagaTesouraria( $rsLista );
} elseif ($stAcao == 'alterar') {
    $obRTesourariaBoletim->roUltimoPagamento->listarPagamentosNaoAnulados( $rsLista );
}

$rsLista->addFormatacao( 'vl_nota' , 'NUMERIC_BR' );
$rsLista->addFormatacao( 'vl_ordem', 'NUMERIC_BR' );

$obLista = new Lista;
$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Nr. Empenho");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Liquidação");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Nr. OP");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Credor");
$obLista->ultimoCabecalho->setWidth( 40 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
if($stAcao == 'incluir')
    $obLista->ultimoCabecalho->addConteudo("Vlr. a Pagar sem OP");
else
    $obLista->ultimoCabecalho->addConteudo("Valor Pago sem OP");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
if($stAcao == 'incluir')
    $obLista->ultimoCabecalho->addConteudo("Vlr. a Pagar com OP");
else
    $obLista->ultimoCabecalho->addConteudo("Valor Pago com OP");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
if ($stAcao == 'alterar') {
    $obLista->ultimoDado->setCampo("[cod_empenho]/[exercicio_empenho]");
} else {
    $obLista->ultimoDado->setCampo("empenho");
}
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
if ($stAcao == 'alterar') {
    $obLista->ultimoDado->setCampo( "[cod_nota]/[exercicio_liquidacao]" );
} else {
    $obLista->ultimoDado->setCampo( "nota" );
}
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "ordem" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "beneficiario" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "vl_nota" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "vl_ordem" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->addAcao();

$stLink  = "&inCodTerminal=".$_REQUEST['inCodTerminal']."&stTimestampTerminal=".$_REQUEST['stTimestampTerminal'];
$stLink .= "&stTimestampUsuario=".$_REQUEST['stTimestampUsuario'];
if ($stAcao == "incluir") {
    $obLista->ultimaAcao->setAcao( 'Pagar' );
} elseif ($stAcao == 'alterar') {
    $obLista->ultimaAcao->setAcao( 'Estornar' );
}
$obLista->ultimaAcao->addCampo( "&inCodEntidade" , "cod_entidade" );
$obLista->ultimaAcao->addCampo( "stEmpenho"      , "empenho"      );
$obLista->ultimaAcao->addCampo( "stNota"         , "nota"         );
$obLista->ultimaAcao->addCampo( "stOrdem"        , "ordem"        );
$obLista->ultimaAcao->addCampo( "inNumCGM"       , "cgm_beneficiario" );
$obLista->ultimaAcao->addCampo( "boAdiantamento" , "adiantamento" );
if ($stAcao == 'alterar') {
    $obLista->ultimaAcao->addCampo( "stTimestamp", "timestamp" );
}
$obLista->ultimaAcao->setLink ( $pgProx."?stAcao=".$stAcao."&".Sessao::getId().$stLink );

$obLista->commitAcao();
$obLista->show();
?>
