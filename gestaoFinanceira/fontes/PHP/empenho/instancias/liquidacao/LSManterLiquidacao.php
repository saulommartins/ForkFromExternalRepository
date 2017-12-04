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
    * Página de Listagem de Anulacao de Empenho
    * Data de Criação   : 06/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 32038 $
    $Name$
    $Author: eduardoschitz $
    $Date: 2008-02-27 07:17:08 -0300 (Qua, 27 Fev 2008) $

    * Casos de uso: uc-02.03.04
                    uc-02.03.24
                    uc-02.03.21
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoEmpenhoAutorizacao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterLiquidacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgCons = "FMConsultarLiquidacao.php";

$stCaminho   = CAM_GF_EMP_INSTANCIAS."liquidacao/";
$obREmpenhoEmpenhoAutorizacao = new REmpenhoEmpenhoAutorizacao;

if (isset($_REQUEST['inCodTipoDocumento'])) {
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setCodTipoDocumento( $_REQUEST['inCodTipoDocumento'] );
}

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}
if ($stAcao == "imprimirAN") {
    $stAcao = "reemitir";
}

//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'alterar'  : $pgProx = $pgForm; break;
    case 'excluir'  : $pgProx = $pgProc; break;
    case 'consultar': $pgProx = $pgCons; break;
    case 'anular'   : $pgProx = "FMAnularLiquidacao.php"; break;
    DEFAULT         : $pgProx = $pgForm;
}

(isset($_GET['pg'])) AND Sessao::write('pg', $_GET['pg'] ? $_GET['pg'] : 0);
(isset($_GET['pos'])) AND Sessao::write('pos', $_GET['pos']? $_GET['pos'] : 0);

if ( Sessao::read('paginando') ) {
    $arFiltro = Sessao::read('filtro');
    foreach ($arFiltro AS $stKey=>$stValue) {
        $_REQUEST[$stKey] = $stValue;
    }
} else {
    foreach ($_POST as $stCampo => $stValor) {
        $arFiltro[$stCampo] = $stValor;
    }
    Sessao::write('filtro', $arFiltro);
    Sessao::write('paginando', true);
}

$stCodEntidade = implode(',', $_REQUEST['inCodEntidade']);

// usado um modelo alternativo de IFs para simplificar e diminuir as linhas do código
(isset($_REQUEST['inCodDespesa']))           AND $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setCodDespesa( $_REQUEST['inCodDespesa'] );
(isset($_REQUEST['dtExercicioEmpenho']))     AND $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setExercicio( $_REQUEST['dtExercicioEmpenho'] );
(isset($_REQUEST['inCodEmpenhoInicial']))    AND $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setCodEmpenhoInicial( $_REQUEST['inCodEmpenhoInicial'] );
(isset($_REQUEST['inCodEmpenhoFinal']))      AND $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setCodEmpenhoFinal( $_REQUEST['inCodEmpenhoFinal'] );
(isset($_REQUEST['stDtVencimento']))         AND $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setDtVencimento( $_REQUEST['stDtVencimento'] );
(isset($_REQUEST['stDtInicial']))            AND $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setDtEmpenhoInicial( $_REQUEST['stDtInicial'] );
(isset($_REQUEST['stDtFinal']))              AND $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setDtEmpenhoFinal( $_REQUEST['stDtFinal'] );
(isset($_REQUEST['inCodLiquidacaoInicial'])) AND $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setCodLiquidacaoInicial( $_REQUEST['inCodLiquidacaoInicial'] );
(isset($_REQUEST['inCodLiquidacaoFinal']))   AND $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setCodLiquidacaoFinal( $_REQUEST['inCodLiquidacaoFinal'] );
(isset($_REQUEST['inCodFornecedor']))        AND $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obRCGM->setNumCGM( $_REQUEST['inCodFornecedor'] );
(isset($_REQUEST['inSituacao']))             AND $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setSituacao( $_REQUEST['inSituacao'] );

$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $stCodEntidade );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setSomar( true );

if ($stAcao == "reemitir") {
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->listarReemitirLiquidacao( $rsLista );
    $rsLista->addFormatacao("valor","NUMERIC_BR");
} else {
    if ( $_REQUEST['dtExercicioEmpenho'] == Sessao::getExercicio() ) {
        $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->listar( $rsLista );
    } else {
        $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->listarRestosAPagar( $rsLista );
    }
}

$stLink = "&stAcao=".$stAcao;

if ( isset($_GET["pg"]) and  isset($_GET["pos"]) ) {
    $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
}

$obLista = new Lista;
$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Entidade");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Empenho");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
if ($stAcao == "reemitir") {
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Liquidação");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
}
$obLista->addCabecalho();
if ($stAcao == "reemitir") {
    $obLista->ultimoCabecalho->addConteudo("Data Anulação Liquidação");
} else {
    $obLista->ultimoCabecalho->addConteudo("Data do Empenho");
}
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
if ($stAcao == "reemitir") {
    $obLista->ultimoCabecalho->addConteudo("Credor");
} else {
    $obLista->ultimoCabecalho->addConteudo("Credor");
}
$obLista->ultimoCabecalho->setWidth( 70 );
$obLista->commitCabecalho();
if ($stAcao == "reemitir") {
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Valor");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
}
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_entidade" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_empenho]/[exercicio_empenho]" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();

if ($stAcao == "reemitir") {
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "cod_nota" );
    $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
    $obLista->commitDado();
}
$obLista->addDado();
if ($stAcao == "reemitir") {
    $obLista->ultimoDado->setCampo( "dt_anulacao" );
} else {
    $obLista->ultimoDado->setCampo( "dt_empenho" );
}
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_fornecedor" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
if ($stAcao == "reemitir") {
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "valor" );
    $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
    $obLista->commitDado();
}
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo( "&inCodEmpenho"     , "cod_empenho"     );
$obLista->ultimaAcao->addCampo( "&inCodPreEmpenho"  , "cod_pre_empenho" );
$obLista->ultimaAcao->addCampo( "&inCodEntidade"    , "cod_entidade"    );
$obLista->ultimaAcao->addCampo( "&inCodReserva"     , "cod_reserva"     );
$obLista->ultimaAcao->addCampo( "&inCodAutorizacao" , "cod_autorizacao" );
$obLista->ultimaAcao->addCampo( "&dtExercicioEmpenho" , "exercicio_empenho" );
$obLista->ultimaAcao->addCampo( "&boImplantado"     , "implantado"      );
$obLista->ultimaAcao->addCampo( "&stExercicioNota"  ,"exercicio"        );
$obLista->ultimaAcao->addCampo( "&inCodNota"        ,"cod_nota"         );
$obLista->ultimaAcao->addCampo( "&stTimestamp"      ,"timestamp"        );

if ( isset($_REQUEST['acaoOrdem']) ) {
    Sessao::write('acao', $_REQUEST['acaoOrdem']);
    Sessao::montaTituloPagina( "3", "Ordem de Pagamento" );
}

if ($stAcao == "imprimir") {
    $pgProx = CAM_FW_POPUPS."relatorio/OCRelatorio.php";
    $stLink .= "&stCaminho=".CAM_GF_EMP_INSTANCIAS."liquidacao/OCRelatorioNotaLiquidacaoEmpenho.php";
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
} elseif ($stAcao == "reemitir") {
    $pgProx = CAM_FW_POPUPS."relatorio/OCRelatorio.php";
    $stLink .= "&stCaminho=".CAM_GF_EMP_INSTANCIAS."liquidacao/OCRelatorioNotaLiquidacaoEmpenhoAnulado.php";
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
} else {
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}

$obLista->commitAcao();
$obLista->show();

SistemaLegado::liberaFrames(true,true);
?>