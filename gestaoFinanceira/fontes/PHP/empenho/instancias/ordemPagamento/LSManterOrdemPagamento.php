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
    * Lista para Empenho - Ordem de Pagamento
    * Data de Criação   : 17/12/2004

    * @author Analista: Ricardo Lopes
    * @author Desenvolvedor: Fabio Bertoldi Rodrigues

    * @ignore

    * $Id: LSManterOrdemPagamento.php 61002 2014-11-28 11:19:43Z evandro $

    * Casos de uso: uc-02.03.20
                    uc-02.03.19
                    uc-02.03.05
                    uc-02.03.25
                    uc-02.03.22
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include CAM_GF_EMP_NEGOCIO."REmpenhoOrdemPagamento.class.php";

//Define o nome dos arquivos PHP
$stPrograma      = "ManterOrdemPagamento";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FM".$stPrograma.".php";
$pgProc          = "PR".$stPrograma.".php";
$pgOcul          = "OC".$stPrograma.".php";
$pgJs            = "JS".$stPrograma.".js";
include $pgJs;

$stCaminho = CAM_GF_EMP_INSTANCIAS."ordemPagamento/";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}
if ($stAcao == "imprimirAN") {
    $stAcao = "reemitir";
}

$arLink = Sessao::read('link');
if ($_GET["pg"] and  $_GET["pos"]) {
    $arLink["pg"]  = $_GET["pg"];
    $arLink["pos"] = $_GET["pos"];
}
if ( is_array($arLink) ) {
    $_GET = $arLink;
    $_REQUEST = $arLink;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $arLink[$key] = $valor;
    }
}
Sessao::write('link', $arLink);

//DEFINE LISTA
$obREmpenhoOrdemPagamento = new REmpenhoOrdemPagamento;
$rsLista                  = new RecordSet;

if (is_array($_REQUEST['inCodEntidade']) && (!$_REQUEST['stCodigoEntidade'])) {
    $stCodigoEntidade = "";
    foreach ($_REQUEST['inCodEntidade'] as $value) {
        $stCodigoEntidade .= $value . " , ";
    }
    $stCodigoEntidade = substr($stCodigoEntidade,0,strlen($stCodigoEntidade) - 2);
    $_REQUEST['stCodigoEntidade'] = $stCodigoEntidade;
}

//DEFINICAO DO FILTRO PARA CONSULTA
$stLink = "";
if ($_REQUEST["stCodigoEntidade"]) {
    $obREmpenhoOrdemPagamento->obROrcamentoEntidade->setCodigoEntidade( $stCodigoEntidade );
    $stLink .= "&stCodigoEntidade=".$stCodigoEntidade;
}
if ($_REQUEST["inCodigoOrdemPagamentoInicial"]) {
    $obREmpenhoOrdemPagamento->setCodigoOrdemInicial( $_REQUEST["inCodigoOrdemPagamentoInicial"] );
    $stLink .= "&inCodigoOrdemPagamentoInicial=".$_REQUEST["inCodigoOrdemPagamentoInicial"];
}
if ($_REQUEST["inCodigoOrdemPagamentoFinal"]) {
    $obREmpenhoOrdemPagamento->setCodigoOrdemFinal( $_REQUEST["inCodigoOrdemPagamentoFinal"] );
    $stLink .= "&inCodigoOrdemPagamentoFinal=".$_REQUEST["inCodigoOrdemPagamentoFinal"];
}
if ($_REQUEST["inCodEmpenhoInicial"]) {
    $obREmpenhoOrdemPagamento->obREmpenhoEmpenho->setCodEmpenhoInicial( $_REQUEST["inCodEmpenhoInicial"] );
    $stLink .= "&inCodEmpenhoInicial=".$_REQUEST["inCodEmpenhoInicial"];
}
if ($_REQUEST["inCodEmpenhoFinal"]) {
    $obREmpenhoOrdemPagamento->obREmpenhoEmpenho->setCodEmpenhoFinal( $_REQUEST["inCodEmpenhoFinal"] );
    $stLink .= "&inCodEmpenhoFinal=".$_REQUEST["inCodEmpenhoFinal"];
}
if ($_REQUEST["dtDataVencimento"]) {
    $obREmpenhoOrdemPagamento->setDataVencimento( $_REQUEST["dtDataVencimento"] );
    $stLink .= "&dtDataVencimento=".$_REQUEST["dtDataVencimento"];
}
if ($_REQUEST["dtDataInicial"]) {
    $obREmpenhoOrdemPagamento->setDataEmissaoInicial( $_REQUEST["dtDataInicial"] );
    $stLink .= "&dtDataInicial=".$_REQUEST["dtDataInicial"];
}
if ($_REQUEST["dtDataFinal"]) {
    $obREmpenhoOrdemPagamento->setDataEmissaoFinal( $_REQUEST["dtDataFinal"] );
    $stLink .= "&dtDataFinal=".$_REQUEST["dtDataFinal"];
}
if ($_REQUEST["stExercicioEmpenho"]) {
    $obREmpenhoOrdemPagamento->obREmpenhoEmpenho->setExercicio( $_REQUEST["stExercicioEmpenho"] );
    $stLink .= "&stExercicioEmpenho=".$_REQUEST["stExercicioEmpenho"];
}
if ($_REQUEST["inCodFornecedor"]) {
    $obREmpenhoOrdemPagamento->setFornecedor( $_REQUEST["inCodFornecedor"] );
    $stLink .= "&inCodFornecedor=".$_REQUEST["inCodFornecedor"];
}

if ($stAcao == 'anular') {
//    $obREmpenhoOrdemPagamento->setListarNaoPaga( true );
    $obREmpenhoOrdemPagamento->listarAnularOp( $rsLista );
    $rsLista->addFormatacao("valor_anulado","NUMERIC_BR");
    $rsLista->addFormatacao("valor_op","NUMERIC_BR");
}

if ($_REQUEST['stMostrarEstornados'] == 'N') {
    $obREmpenhoOrdemPagamento->setListarNaoAnulada( true );
}

if ($stAcao == "reemitir") {
    $obREmpenhoOrdemPagamento->listarReemitir( $rsLista, $boTransacao );
    $rsLista->addFormatacao("valor","NUMERIC_BR");
} elseif ($stAcao !== 'anular') {
    $obREmpenhoOrdemPagamento->listar( $rsLista );
}
$stLink .= "&stAcao=".$stAcao;

$rsLista->addFormatacao("beneficiario","SLASHES");

Sessao::write('rsListaImpressao', $rsLista);
//DEFINICAO DA LISTA
$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "&nbsp;"     );
$obLista->ultimoCabecalho->setWidth   ( 5            );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Entidade"   );
$obLista->ultimoCabecalho->setWidth   ( 8           );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Ordem"      );
$obLista->ultimoCabecalho->setWidth   ( 10           );
$obLista->commitCabecalho();
$obLista->addCabecalho();
if ($stAcao == "reemitir") {
    $obLista->ultimoCabecalho->addConteudo( "Data Anulação OP"       );
} else {
    $obLista->ultimoCabecalho->addConteudo( "Data"       );
}
$obLista->ultimoCabecalho->setWidth   ( 10           );
$obLista->commitCabecalho();
if ($stAcao != "reemitir") {
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Situação"     );
    $obLista->ultimoCabecalho->setWidth   ( 10           );
    $obLista->commitCabecalho();
}

if ($stAcao == 'anular') {
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Valor OP"      );
    $obLista->ultimoCabecalho->setWidth   ( 10           );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Vl Anulado" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
}

$obLista->addCabecalho();
if ($stAcao == "reemitir") {
    $obLista->ultimoCabecalho->addConteudo( "Credor" );
} else {
    $obLista->ultimoCabecalho->addConteudo( "Fornecedor" );
}
$obLista->ultimoCabecalho->setWidth   ( 45           );
$obLista->commitCabecalho();
if ($stAcao == "reemitir") {
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Valor"      );
    $obLista->ultimoCabecalho->setWidth   ( 10           );
    $obLista->commitCabecalho();
}
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "&nbsp;"     );
$obLista->ultimoCabecalho->setWidth   ( 5            );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo      ( "cod_entidade" );
$obLista->ultimoDado->setAlinhamento( "DIREITA"      );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo      ( "[cod_ordem]/[exercicio]" );
$obLista->ultimoDado->setAlinhamento( "DIREITA"      );
$obLista->commitDado();
$obLista->addDado();
if ($stAcao == "reemitir") {
    $obLista->ultimoDado->setCampo      ( "dt_anulado"   );
} else {
    $obLista->ultimoDado->setCampo      ( "dt_emissao"   );
}
$obLista->ultimoDado->setAlinhamento( "CENTRO"       );
$obLista->commitDado();
if ($stAcao != "reemitir") {
    $obLista->addDado();
    $obLista->ultimoDado->setCampo      ( "situacao" );
    $obLista->ultimoDado->setAlinhamento( "CENTRO"  );
    $obLista->commitDado();
}

if ($stAcao == 'anular') {
    $obLista->addDado();
    $obLista->ultimoDado->setCampo      ( "valor_op" );
    $obLista->ultimoDado->setAlinhamento( "DIREITA"      );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "valor_anulado" );
    $obLista->ultimoDado->setAlinhamento ("CENTRO");
    $obLista->commitDado();
}

$obLista->addDado();
$obLista->ultimoDado->setCampo      ( "beneficiario" );
$obLista->commitDado();
if ($stAcao == "reemitir") {
    $obLista->addDado();
    $obLista->ultimoDado->setCampo      ( "valor" );
    $obLista->ultimoDado->setAlinhamento( "DIREITA"      );
    $obLista->commitDado();
}

$htmlExtra = "";
// Define ACOES
if ($stAcao == "anular") {
    $obLista->addAcao();
    $stAcao = "anular";
    $obLista->ultimaAcao->setAcao( $stAcao );
    $obLista->ultimaAcao->addCampo("&inCodigoOrdem"    , "cod_ordem"        );
    $obLista->ultimaAcao->addCampo("&stExercicio"      , "exercicio"        );
    $obLista->ultimaAcao->addCampo("&inCodEntidade"    , "cod_entidade"     );
    $obLista->ultimaAcao->addCampo("&stNomeEntidade"   , "entidade"         );
    $obLista->ultimaAcao->addCampo("&inNumCGM"         , "cgm_beneficiario" );
    $obLista->ultimaAcao->addCampo("&stNomeCGM"        , "beneficiario"     );
    $obLista->ultimaAcao->addCampo("&dtDataVencimento" , "dt_vencimento"    );
    $obLista->ultimaAcao->addCampo("&inCodigoEmpenho"  , "cod_empenho"      );
    $obLista->ultimaAcao->addCampo("&flValorOP"        , "valor_op"  );
    $obLista->ultimaAcao->addCampo("&flValorAnulado"   , "valor_anulado"    );
    $obLista->ultimaAcao->addCampo("&flValorAnular"    , "vl_nota"          );
    $obLista->ultimaAcao->addCampo("&boImplantado"     , "implantado"       );
    $obLista->ultimaAcao->setLink( $pgForm."?".Sessao::getId().$stLink."&stAcao=".$stAcao );
    $obLista->commitAcao();
} elseif ($stAcao == "imprimir") {
    $obLista->addAcao();
    $stAcao  = "imprimir";
    $pgProx  = CAM_FW_POPUPS."relatorio/OCRelatorio.php";
    $stLink .= "&stCaminho=".CAM_GF_EMP_INSTANCIAS."ordemPagamento/OCRelatorioOrdemPagamento.php";
    $obLista->ultimaAcao->setAcao( $stAcao );
    $obLista->ultimaAcao->addCampo("&inCodigoNota"     , "cod_nota"         );
    $obLista->ultimaAcao->addCampo("&inCodigoOrdem"    , "cod_ordem"        );
    $obLista->ultimaAcao->addCampo("&stExercicio"      , "exercicio"        );
    $obLista->ultimaAcao->addCampo("&inCodEntidade"    , "cod_entidade"     );
    $obLista->ultimaAcao->addCampo("&stNomeEntidade"   , "entidade"         );
    $obLista->ultimaAcao->addCampo("&inNumCGM"         , "cgm_beneficiario" );
    $obLista->ultimaAcao->addCampo("&stNomeCGM"        , "beneficiario"     );
    $obLista->ultimaAcao->addCampo("&dtDataVencimento" , "dt_vencimento"    );
    $obLista->ultimaAcao->addCampo("&inCodigoEmpenho"  , "cod_empenho"      );
    $obLista->ultimaAcao->addCampo("&boImplantado"     , "implantado"       );
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink."&stAcao=".$stAcao );
    $obLista->commitAcao();

    $stLinkBotao = $pgProx."?".Sessao::getId()."&stCtrl=imprimirTodos".$stLink;
    $obBotaoImprimirTodos = new Button;
    $obBotaoImprimirTodos->setId   ("imprimirTodos");
    $obBotaoImprimirTodos->setName ("imprimirTodos");
    $obBotaoImprimirTodos->setValue("Imprimir Todos");
    $obBotaoImprimirTodos->setStyle("color: red;");
    $obBotaoImprimirTodos->setTipo ("button");
    $obBotaoImprimirTodos->setDefinicao("imprimirTodos");
    $obBotaoImprimirTodos->obEvento->setOnClick("javascript:window.open('".$stLinkBotao."', 'oculto');");
    $obBotaoImprimirTodos->montaHTML();

    $obTabelaBtnImprimirTodos = new Tabela;
    $obTabelaBtnImprimirTodos->addLinha();
    $obTabelaBtnImprimirTodos->ultimaLinha->addCelula();
    $obTabelaBtnImprimirTodos->ultimaLinha->ultimaCelula->setColSpan (1);
    $obTabelaBtnImprimirTodos->ultimaLinha->ultimaCelula->setClass   ( $obLista->getClassPaginacao() );
    $obTabelaBtnImprimirTodos->ultimaLinha->ultimaCelula->addConteudo( "<div align=\"center\">".$obBotaoImprimirTodos->getHTML()."&nbsp;</div>");
    $obTabelaBtnImprimirTodos->ultimaLinha->commitCelula();
    $obTabelaBtnImprimirTodos->commitLinha();
    $obTabelaBtnImprimirTodos->montaHTML();

    $htmlExtra = $obTabelaBtnImprimirTodos->getHTML();

} elseif ($stAcao == "reemitir") {
    $obLista->addAcao();
    $pgProx  = CAM_FW_POPUPS."relatorio/OCRelatorio.php";
    $stLink .= "&stCaminho=".CAM_GF_EMP_INSTANCIAS."ordemPagamento/OCRelatorioOrdemPagamentoAnulado.php";
    $obLista->ultimaAcao->setAcao( $stAcao );
    $obLista->ultimaAcao->addCampo("&inCodigoOrdem"      , "cod_ordem"        );
    $obLista->ultimaAcao->addCampo("&stExercicioOrdem"   , "exercicio"        );
    $obLista->ultimaAcao->addCampo("&inCodEntidade"      , "cod_entidade"     );
    $obLista->ultimaAcao->addCampo("&stNomeEntidade"     , "entidade"         );
    $obLista->ultimaAcao->addCampo("&inNumCGM"           , "cgm_beneficiario" );
    $obLista->ultimaAcao->addCampo("&stNomeCGM"          , "beneficiario"     );
    $obLista->ultimaAcao->addCampo("&dtDataVencimento"   , "dt_vencimento"    );
    $obLista->ultimaAcao->addCampo("&stTimestampAnulado" , "timestamp"        );
    $obLista->ultimaAcao->addCampo("&inCodigoEmpenho"    , "cod_empenho"      );
    $obLista->ultimaAcao->addCampo("&flValorTotal"       , "valor_pagamento"  );
    $obLista->ultimaAcao->addCampo("&flValorAnulado"     , "valor_anulada"    );
    $obLista->ultimaAcao->addCampo("&boImplantado"       , "implantado"       );
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
    $obLista->commitAcao();
}

$obLista->montaHTML();
echo $obLista->getHTML().$htmlExtra;

// DEFINE BOTOES
$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao );

//DEFINE FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addHidden ($obHdnAcao          );
$obFormulario->show();
SistemaLegado::LiberaFrames();
?>
