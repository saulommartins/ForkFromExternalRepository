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
    * Página de Listagem de Consulta de Empenho
    * Data de Criação   : 06/12/2004

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore

    * $Id: LSConsultarEmpenho.php 59620 2014-09-02 17:25:32Z arthur $

    * Casos de uso: uc-02.03.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoEmpenho.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ConsultarEmpenho";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stCaminho   = CAM_GF_EMP_INSTANCIAS."empenho/";
$stAcao = $request->get('stAcao');

$obRegra = new REmpenhoEmpenho;

if ( !Sessao::read('paginando') ) {
    $arFiltro = array();
    foreach ($_POST as $stCampo => $stValor) {
        $arFiltro[$stCampo] = $stValor;
    }
    Sessao::write('filtro', $arFiltro);
    Sessao::write('pg', $_GET['pg'] ? $_GET['pg'] : 0);
    Sessao::write('pos', $_GET['pos']? $_GET['pos'] : 0);
    Sessao::write('paginando', true);
} else {
    Sessao::write('pg', $_GET['pg']);
    Sessao::write('pos', $_GET['pos']);
}

$stLink = "";
foreach ($_REQUEST as $stCampo => $stValor) {
    if ($stValor != "") {
        if (is_array($stValor)) {
            $stValor = implode(',', $stValor);
        }
        $stLink .= "&".$stCampo."=".$stValor;
    }
}
$stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "consultar";
}

//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'alterar'  : $pgProx = $pgForm; break;
    case 'excluir'  : $pgProx = $pgProc; break;
    case 'consultar': $pgProx = $pgForm; break;
    default         : $pgProx = $pgForm;
}

if (is_array($_REQUEST['inCodEntidade'])) {
    $stCodEntidade = implode(",", $_REQUEST['inCodEntidade']);
} else {
    $stCodEntidade = $_REQUEST['inCodEntidade'];
}

$stExercicio = ( $_REQUEST['stExercicio'] ) ? $_REQUEST['stExercicio'] : Sessao::getExercicio();

if ( $_REQUEST['inCodDespesa'] != '' AND (strlen($_REQUEST['inCodDespesa']) != strlen($_REQUEST['stMascClassificacao'])) ) {
    SistemaLegado::alertaAviso($pgFilt, " Elemento de Despesa inválido.", "n_incluir", "erro", Sessao::getId(), "../");
}

$obRegra->obROrcamentoEntidade->setCodigoEntidade( $stCodEntidade   );
$obRegra->obROrcamentoEntidade->obRCGMPessoaFisica->setCPF ( preg_replace('/[^a-zA-Z0-9]/','', $_REQUEST['inCPF']) );

$obRegra->obROrcamentoEntidade->obRCGMPessoaJuridica->setCNPJ ( preg_replace('/[^a-zA-Z0-9]/','', $_REQUEST['inCNPJ']));
$obRegra->setExercicio          ( $stExercicio                      );
$obRegra->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->setNumeroOrgao( $_REQUEST['inNumOrgao'] );
$obRegra->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->setNumeroUnidade( $_REQUEST['inNumUnidade'] );
$obRegra->setCodDespesa         ( $_REQUEST['inCodDotacao'] );
$obRegra->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setCodEstrutural( $_REQUEST['inCodDespesa'] );
$obRegra->setCodEmpenhoInicial  ( $_REQUEST['inCodEmpenhoInicial']  );
$obRegra->setCodEmpenhoFinal    ( $_REQUEST['inCodEmpenhoFinal']    );
$obRegra->obREmpenhoAutorizacaoEmpenho->setCodAutorizacao     ( $_REQUEST['inCodAutorizacao']     );
$obRegra->obRCGM->setNumCGM     ( $_REQUEST['inCodFornecedor']      );
$obRegra->obROrcamentoDespesa->obROrcamentoRecurso->setCodRecurso( $_REQUEST['inCodRecurso']      );

if($_REQUEST['inCodUso'] && $_REQUEST['inCodDestinacao'] && $_REQUEST['inCodEspecificacao'])
    $obRegra->obROrcamentoDespesa->obROrcamentoRecurso->setDestinacaoRecurso ( $_REQUEST['inCodUso'].".".$_REQUEST['inCodDestinacao'].".".$_REQUEST['inCodEspecificacao'] );

$obRegra->obROrcamentoDespesa->obROrcamentoRecurso->setCodDetalhamento   ( $_REQUEST['inCodDetalhamento']      );
$obRegra->setDtEmpenhoInicial   ( $_REQUEST['stDtInicial']          );
$obRegra->setDtEmpenhoFinal     ( $_REQUEST['stDtFinal']            );
$obRegra->obREmpenhoHistorico->setCodHistorico( $_REQUEST['inCodHistorico'] );
$obRegra->setSituacao           ( $_REQUEST['inSituacao']           );

$obRegra->setBoEmpenhoCompraLicitacao( true );
$obRegra->setCodModalidadeCompra     ( $_REQUEST['inCodModalidadeCompra'] );
$obRegra->setCompraInicial           ( $_REQUEST['inCompraInicial'] );
$obRegra->setCompraFinal             ( $_REQUEST['inCompraFinal'] );
$obRegra->setCodModalidadeLicitacao  ( $_REQUEST['inCodModalidadeLicitacao'] );
$obRegra->setLicitacaoInicial        ( $_REQUEST['inLicitacaoInicial'] );
$obRegra->setLicitacaoFinal          ( $_REQUEST['inLicitacaoFinal'] );

//adicionado filtro por atributos dinamicos
$inCount = 0;
foreach ($_REQUEST as $stKey=>$stValue) {
    //vare o request e verifica se a chave é de atributo dinamico
    $stValue = ( trim($stValue[0]) == '' ) ? '' : $stValue;
    if ( strstr( $stKey, 'atributos_' ) AND ( trim($stValue) != '' ) ) {
        $arAux = explode( '_', $stKey );
        $arAtributosDinamicos[ $inCount ][ 'cod_modulo'   ] = 10;
        $arAtributosDinamicos[ $inCount ][ 'cod_atributo' ] = $arAux[1];
        $arAtributosDinamicos[ $inCount ][ 'cod_cadastro' ] = 1;
        $arAtributosDinamicos[ $inCount ][ 'tipo'		  ] = is_array( $stValue ) ? 'multiplo' : '';
        $arAtributosDinamicos[ $inCount ][ 'valor'		  ] = is_array( $stValue ) ? implode( ',', $stValue ) : $stValue;
        $inCount++;
    }

}

if ( is_array( $arAtributosDinamicos ) ) {
    $obRegra->setAtributosDinamicos( $arAtributosDinamicos );
}

if ( $stExercicio == Sessao::getExercicio() ) {
    $obRegra->listarConsultaEmpenho( $rsLista );
} else {
    $obRegra->listarRestosConsultaEmpenho( $rsLista );
}

$rsLista->addFormatacao("vl_empenhado","NUMERIC_BR");

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
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Data do Empenho");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Fornecedor");
$obLista->ultimoCabecalho->setWidth( 60 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Valor");
$obLista->ultimoCabecalho->setWidth( 60 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_entidade" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_empenho]/[exercicio]" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "dt_empenho" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_fornecedor" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "vl_empenhado" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo( "&inCodEmpenho"     , "cod_empenho"     );
$obLista->ultimaAcao->addCampo( "&inCodPreEmpenho"  , "cod_pre_empenho" );
$obLista->ultimaAcao->addCampo( "&inCodAutorizacao" , "cod_autorizacao" );
$obLista->ultimaAcao->addCampo( "&inCodEntidade"    , "cod_entidade"    );
$obLista->ultimaAcao->addCampo( "&inCodReserva"     , "cod_reserva"     );
$obLista->ultimaAcao->addCampo( "&boImplantado"     , "implantado"      );
$obLista->ultimaAcao->addCampo( "&stExercicioEmpenho" ,"exercicio"      );

if ($stAcao == "anular") {
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
} elseif ($stAcao == "imprimir") {
    $pgProx = CAM_FW_POPUPS."relatorio/OCRelatorio.php";
    $stLink .= "&stCaminho=".CAM_GF_EMP_INSTANCIAS."empenho/OCRelatorioEmpenhoOrcamentario.php";
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
} elseif ($stAcao == "consultar") {
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}

$obLista->commitAcao();
$obLista->show();
?>
