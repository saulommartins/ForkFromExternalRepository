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

  * Página de Lista de Emissão de Carnês
  * Data de criação : 11/11/2005

  * @author Analista: Fábio Bertoldi
  * @author Programador: Tonismar R. Bernardo

  * @ignore

    * $Id: LSEmitirCarne.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.03.11
**/



include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_ARR_NEGOCIO."RARRCarne.class.php" );
include_once( CAM_GT_CIM_NEGOCIO."RCIMLocalizacao.class.php" );
include_once( CAM_GT_CEM_NEGOCIO."RCEMAtividade.class.php" );
include_once( CAM_GT_ARR_NEGOCIO."RARRConfiguracao.class.php" );
include_once( CAM_GT_CIM_MAPEAMENTO."TCIMTransferenciaAdquirente.class.php" );

//Definicao dos nomes de arquivos
$stPrograma = "EmitirCarne";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php"; $pgForm = "FM".$stPrograma.".php";
$pgFormVinculo = "FM".$stPrograma."Vinculo.php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

$stCaminho = CAM_GT_ARR_INSTANCIAS."documentos/";

$stAcao = $request->get('stAcao');

//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'incluir'   : $pgProx = $pgForm; break;
    case 'excluir'   : $pgProx = $pgProc; break;
    case 'reemissao' : $pgProx = $pgFormVinculo; break;
    DEFAULT          : $pgProx = $pgForm;
}

$link = Sessao::read( 'link' );
//MANTEM FILTRO E PAGINACAO
$stLink .= "&stAcao=".$stAcao;
$link = Sessao::read( 'link' );
if ( isset($_REQUEST["pg"]) and  isset($_REQUEST["pos"]) ) {
    $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
    $link["pg"]  = $_GET["pg"];
    $link["pos"] = $_GET["pos"];
}

//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA

if ( is_array($link) ) {
    $_REQUEST = $link;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
}

Sessao::write( 'link', $link );

if ($stAcao == "reemitir") {
    if (!$_REQUEST['inCodLocalizacaoInicial'] && !$_REQUEST['inCodLocalizacaoFinal']  &&
         !$_REQUEST['inCodContribuinteInicial'] && !$_REQUEST['inCodContribuinteFinal'] &&
         !$_REQUEST['inNumInscricaoEconomicaInicial'] && !$_REQUEST['inNumInscricaoEconomicaFinal'] &&
         !$_REQUEST['inNumInscricaoImobiliariaInicial'] && !$_REQUEST['inNumInscricaoImobiliariaFinal'] &&
         !$_REQUEST['inCodGrupo'] && !$_REQUEST['inCodCredito']) {
        sistemaLegado::alertaAviso($pgFilt."?stAcao=".$stAcao, "Um dos campos a seguir deve ser preenchido: Grupo de Crédito, Crédito, Contribuinte, Inscrição Imobiliária, Inscrição Econômica ou Localização", "n_erro", "erro", Sessao::getId(), "../");
        exit;
    }
}

$obRARRConfiguracao = new RARRConfiguracao;
$obRARRConfiguracao->consultar();

$obRARRCarne = new RARRCarne;
$obRARRCarne->setExercicio( $_REQUEST['inExercicio']);

if ($_REQUEST['inNumAnterior']!=null && $_REQUEST['inNumAnterior']!='') {
    $obRARRCarne->stNumeracao = $_REQUEST['inNumAnterior'];
}

if ($_REQUEST['inCodCredito']!=null && $_REQUEST['inCodCredito']!='') {
    $arCredito = explode('.',$_REQUEST['inCodCredito'] );
    if (isset($arCredito[0])) {
        $obRARRCarne->inCodCredito    = $arCredito[0] ;
    }
    if (isset($arCredito[1])) {
        $obRARRCarne->inCodEspecie    = $arCredito[1] ;
    }
    if (isset($arCredito[2])) {
        $obRARRCarne->inCodGenero     = $arCredito[2] ;
    }
    if (isset($arCredito[3])) {
        $obRARRCarne->inCodNatureza   = $arCredito[3] ;
    }
}

if ($_REQUEST['inCodGrupo']!=null && $_REQUEST['inCodGrupo']!='') {
    $arDados = explode("/", $_REQUEST['inCodGrupo']);
    $obRARRCarne->setGrupo  ( $arDados[0] );
}

if ($_REQUEST['inCodContribuinteInicial']!= null && $_REQUEST['inCodContribuinteInicial']!= '') {
    $obRARRCarne->setCodContribuinteInicial( $_REQUEST['inCodContribuinteInicial'] );
    if ($_REQUEST['inCodContribuinteFinal']!= '' && $_REQUEST['inCodContribuinteFinal']!= null) {
        $obRARRCarne->setCodContribuinteFinal  ( $_REQUEST['inCodContribuinteFinal']   );
    }
} elseif ($_REQUEST['inCodContribuinteFinal']!= '' && $_REQUEST['inCodContribuinteFinal']!= null) {
    $obRARRCarne->setCodContribuinteInicial  ( $_REQUEST['inCodContribuinteFinal']   );
}

if ($_REQUEST['inNumInscricaoEconomicaInicial']!=null && $_REQUEST['inNumInscricaoEconomicaInicial']!='') {
    $obRARRCarne->setInscricaoEconomicaInicial   ( $_REQUEST['inNumInscricaoEconomicaInicial']   );
    if ($_REQUEST['inNumInscricaoEconomicaFinal']!='' && $_REQUEST['inNumInscricaoEconomicaFinal']!=null) {
        $obRARRCarne->setInscricaoEconomicaFinal     ( $_REQUEST['inNumInscricaoEconomicaFinal']     );
    }
} elseif ($_REQUEST['inNumInscricaoEconomicaFinal']!='' && $_REQUEST['inNumInscricaoEconomicaFinal']!=null) {
    $obRARRCarne->setInscricaoEconomicaInicial     ( $_REQUEST['inNumInscricaoEconomicaFinal']     );
}

if ($_REQUEST['inNumInscricaoImobiliariaInicial']!=null && $_REQUEST['inNumInscricaoImobiliariaInicial']!='') {
    $obRARRCarne->setInscricaoImobiliariaInicial( $_REQUEST['inNumInscricaoImobiliariaInicial'] );
    if ($_REQUEST['inNumInscricaoImobiliariaFinal']!='' && $_REQUEST['inNumInscricaoImobiliariaFinal']!=null) {
        $obRARRCarne->setInscricaoImobiliariaFinal  ( $_REQUEST['inNumInscricaoImobiliariaFinal']  );
    }
} elseif ($_REQUEST['inNumInscricaoImobiliariaFinal']!='' && $_REQUEST['inNumInscricaoImobiliariaFinal']!=null) {
    $obRARRCarne->setInscricaoImobiliariaInicial  ( $_REQUEST['inNumInscricaoImobiliariaFinal']  );
}
if ($_REQUEST['inCodLocalizacaoInicial']!=null && $_REQUEST['inCodLocalizacaoInicial'] !='') {
    $obRARRCarne->setValorCompostoInicial ( $_REQUEST['inCodLocalizacaoInicial'] );
}
if ($_REQUEST['inCodLocalizacaoFinal']!='' && $_REQUEST['inCodLocalizacaoFinal']!=null) {
    $obRARRCarne->setValorCompostoFinal ( $_REQUEST['inCodLocalizacaoFinal'] );
}
$obRARRCarne->novaListaEmissao( $rsCarneTemp );

$arCarne = $rsCarneTemp->arElementos;

$obTransfAdquirente = new TCIMTransferenciaAdquirente;
$inI = 1;
foreach ($rsCarneTemp->arElementos as $campo => $chave) {
    $obTransfAdquirente->setCodLancamento ($chave['cod_lancamento']);
    $obTransfAdquirente->recuperaAdquirentes ($rsAdquirentes);
    if ($rsAdquirentes->inNumLinhas > 0) {
        if ($rsAdquirentes->inNumLinhas > 1) {
            $arCgm = preg_split( "/\//", $arCarne[$campo]['numcgm']);
            foreach ($rsAdquirentes->arElementos as $field => $key) {
                $arCgm[($key['ordem']-1)] = $key['numcgm_adquirente'];
                $arCarne[$campo]['ordem'] .= $key['ordem']."/";
            }
            $arCarne[$campo]['numcgm'] = implode("/", $arCgm);
        }
        if ($arCarne[$campo]['ordem'] == '') {
            $arCarne[$campo]['ordem'] = $rsAdquirentes->getCampo('ordem');
        }
    }
}

$rsCarne = new RecordSet;
$rsCarne->preenche ($arCarne);

if ($_REQUEST["stTipoParcela"] == "unicas") {
    $arTMP = $rsCarne->getElementos();
    $arTMP2 = array();
    for ( $inX=0; $inX<count( $arTMP ); $inX++ ) {
        if ($arTMP[$inX]["info_parcela"] == "Única") {
            $arTMP2[] = $arTMP[$inX];
        }
    }
    $rsCarne->preenche( $arTMP2 );
}else
    if ($_REQUEST["stTipoParcela"] == "normais") {
        $arTMP = $rsCarne->getElementos();
        $arTMP2 = array();
        for ( $inX=0; $inX<count( $arTMP ); $inX++ ) {
            if ($arTMP[$inX]["info_parcela"] != "Única") {
                $arTMP2[] = $arTMP[$inX];
            }
        }

        $rsCarne->preenche( $arTMP2 );
    }

/* VERIFICACOES PARA PODER REALIZAR CONSOLIDACAO */
$boPodeConsolidar = true;
if ( ($_REQUEST['inCodContribuinteInicial'] && $_REQUEST['inCodContribuinteFinal']) && ( $_REQUEST['inCodContribuinteInicial'] != $_REQUEST['inCodContribuinteFinal'])  ) {
    $boPodeConsolidar = false;
} elseif ( ($_REQUEST['inNumInscricaoEconomicaInicial'] && $_REQUEST['inNumInscricaoEconomicaFinal'] ) && ( $_REQUEST['inNumInscricaoEconomicaInicial'] != $_REQUEST['inNumInscricaoEconomicaFinal'] ) ) {
    $boPodeConsolidar = false;
} elseif ( ($_REQUEST['inNumInscricaoImobiliariaInicial'] && $_REQUEST['inNumInscricaoImobiliariaFinal']) &&
    ( $_REQUEST['inNumInscricaoImobiliariaInicial'] != $_REQUEST['inNumInscricaoImobiliariaFinal'] ) ){
    $boPodeConsolidar = false;
}

// Separar Vencidas
$hoje = date('Ymd');
$arTmp = $rsCarne->arElementos;
$arVencidas = array();
$arNormais  = array();
$arVinculos = array();
$inQtdVinculos = 0;

if ($arTmp) {
    foreach ($arTmp as $linha) {
        $boJaExiste = false;
        for ($inX=0; $inX<$inQtdVinculos; $inX++) {
            if ($arVinculos[$inX]["vinculo"] == $linha["vinculo"]) {
                $boJaExiste = true;
                break;
            }
        }

        if (!$boJaExiste) {
            $arVinculos[$inQtdVinculos]["id_vinculo"] = $linha["id_vinculo"];
            $arVinculos[$inQtdVinculos]["vinculo"] = $linha["vinculo"];
            $inQtdVinculos++;
        }

        $arDtCorrente = explode('-',$linha['vencimento_parcela']);
  
        $dtCorrente = $arDtCorrente[0].$arDtCorrente[1].$arDtCorrente[2];
        if ($dtCorrente < $hoje) {
            if (( $linha["info_parcela"] != "Única" ) || (( $linha["info_parcela"] == "Única" ) && ( $obRARRConfiguracao->getBaixaManualUnica() == "sim" )) ) {
                $arVencidas[] = $linha;
            }
        } else {
            $arNormais[]  = $linha;
        }
    }
}

Sessao::write( 'vinculo', $arVinculos );
Sessao::write( 'qtd_vinculo', $inQtdVinculos );

$rsNormais  = new Recordset;
$rsVencidas = new Recordset;
$rsNormais->preenche    ( $arNormais    );
$rsVencidas->preenche   ( $arVencidas   );
$boInscricaoN = $boInscricaoV = false;

while ( !$rsNormais->eof() ) {
    if ( $rsNormais->getCampo('inscricao') ) {
        $boInscricaoN = true;
        break;
    }
    $rsNormais->proximo();
}
while ( !$rsVencidas->eof() ) {
    if ( $rsVencidas->getCampo('inscricao') ) {
        $boInscricaoV = true;
        break;
    }
    $rsVencidas->proximo();
}
$rsNormais->setPrimeiroElemento();
$rsVencidas->setPrimeiroElemento();

$rsNormais->addFormatacao('valor_parcela','NUMERIC_BR');
#$rsNormais->ordena ('inscricao');
$rsVencidas->addFormatacao('valor_parcela','NUMERIC_BR');
#$rsVencidas->ordena ('inscricao');

unset($arNormais,$arVencidas,$rsCarne);

$obLista = new Lista;
$obLista->setTitulo    ("Parcelas a Vencer");
$obLista->setRecordSet( $rsNormais  );
$obLista->setMostraPaginacao(false);
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
if ($boInscricaoN) {
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Inscrição");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
}
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Numeração");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Parcela");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Contribuinte");
$obLista->ultimoCabecalho->setWidth( 25 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Vínculo");
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Valor");
$obLista->ultimoCabecalho->setWidth( 5  );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Vencimento");
$obLista->ultimoCabecalho->setWidth( 6 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Reemitir");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

if ($boInscricaoN) {
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "inscricao" );
    $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
    $obLista->commitDado();
}
$obLista->addDado();
$obLista->ultimoDado->setCampo( "numeracao" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "info_parcela" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_cgm" );
$obLista->ultimoDado->setTitle("CGM: [numcgm] <br> [nom_cgm]");
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "vinculo" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "valor_parcela" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA');
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "vencimento_parcela_br" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obChkReemitir = new Checkbox;
$obChkReemitir->setName                        ( "nboReemitir"                                );
$obChkReemitir->setValue                       ( "[cod_lancamento]§[cod_parcela]§[cod_convenio]§[cod_carteira]§[exercicio_calculo]§[convenio_atual]§[carteira_atual]§[numeracao]§[vencimento_parcela_br]§[valor_parcela]§[info_parcela]§[numcgm]§[impresso]§[chave_vinculo]§[vinculo]§[inscricao]" );

$obLista->addDadoComponente                    ( $obChkReemitir                              );
$obLista->ultimoDado->setAlinhamento           ( 'CENTRO'                                    );
$obLista->ultimoDado->setCampo                 ( "reemitir"                                  );
$obLista->commitDadoComponente                 (                                             );

// checks
$obChkTodosN = new Checkbox;
$obChkTodosN->setName                        ( "boTodos" );
$obChkTodosN->setId                          ( "boTodos" );
$obChkTodosN->setRotulo                      ( "Selecionar Todas" );
$obChkTodosN->obEvento->setOnChange          ( "selecionarTodos('n');" );
$obChkTodosN->montaHTML();

$obChkConsolidar = new Checkbox;
$obChkConsolidar->setName                              ( "boConsolidarN" );
$obChkConsolidar->setId                                     ( "boConsolidar" );
$obChkConsolidar->setDisabled							( !$boPodeConsolidar );
$obChkConsolidar->setValue                               ( true );
$obChkConsolidar->setRotulo                              ( "Consolidar Dívidas" );
$obChkConsolidar->obEvento->setOnChange    ( "ConsolidarDividas( this.checked );" );
$obChkConsolidar->montaHTML();

$txtNovoVencimentoConsolidacaoN = new Data;
$txtNovoVencimentoConsolidacaoN->setName     ( "dtNovoVencimentoN"    );
$txtNovoVencimentoConsolidacaoN->setRotulo   ( "Vencimento para Consolidação"     );
$txtNovoVencimentoConsolidacaoN->setTitle    ( "Novo vencimento para a consolidação" );
$txtNovoVencimentoConsolidacaoN->obEvento->setOnChange    ( "AtualizaDatas( this.value );" );
$txtNovoVencimentoConsolidacaoN->setDisabled ( true );
$txtNovoVencimentoConsolidacaoN->montaHTML();

$obTabelaCheckboxN = new Tabela;
$obTabelaCheckboxN->addLinha();
$obTabelaCheckboxN->ultimaLinha->addCelula();
$obTabelaCheckboxN->ultimaLinha->ultimaCelula->setColSpan ( 2 );
$obTabelaCheckboxN->ultimaLinha->ultimaCelula->setClass   ( $obLista->getClassPaginacao() );
$obTabelaCheckboxN->ultimaLinha->ultimaCelula->addConteudo( "<div align='right'>Selecionar Todos".$obChkTodosN->getHTML()."&nbsp;</div>");
$obTabelaCheckboxN->ultimaLinha->commitCelula();
$obTabelaCheckboxN->commitLinha();

$obTabelaCheckboxN->addLinha();

$obTabelaCheckboxN->ultimaLinha->addCelula();
$obTabelaCheckboxN->ultimaLinha->ultimaCelula->setColSpan ( 1 );
$obTabelaCheckboxN->ultimaLinha->ultimaCelula->setWidth ( "70%" );
$obTabelaCheckboxN->ultimaLinha->ultimaCelula->setClass   ( $obLista->getClassPaginacao() );
$obTabelaCheckboxN->ultimaLinha->ultimaCelula->addConteudo( "<div align='right'>Vencimento para consolidação&nbsp;". $txtNovoVencimentoConsolidacaoN->getHTML()."&nbsp;</div>");
$obTabelaCheckboxN->ultimaLinha->commitCelula();

$obTabelaCheckboxN->ultimaLinha->addCelula();
$obTabelaCheckboxN->ultimaLinha->ultimaCelula->setColSpan ( 1 );
$obTabelaCheckboxN->ultimaLinha->ultimaCelula->setClass   ( $obLista->getClassPaginacao() );
$obTabelaCheckboxN->ultimaLinha->ultimaCelula->addConteudo( "<div align='right'>Consolidar Dívidas".$obChkConsolidar->getHTML()."&nbsp;</div>");
$obTabelaCheckboxN->ultimaLinha->commitCelula();
$obTabelaCheckboxN->commitLinha();

$obTabelaCheckboxN->montaHTML();
$obLista->montaHTML();

$stHtmlN  = $obLista->getHTML();
$stHtmlN .= $obTabelaCheckboxN->getHTML();

// lista de vencidas
$obListaNVencidas = new Lista;
$obListaNVencidas->setTitulo    ("Parcelas Vencidas");
$obListaNVencidas->setRecordSet( $rsVencidas );
$obListaNVencidas->setMostraPaginacao(false);
$obListaNVencidas->addCabecalho();
$obListaNVencidas->ultimoCabecalho->addConteudo("&nbsp;");
$obListaNVencidas->ultimoCabecalho->setWidth( 5 );
$obListaNVencidas->commitCabecalho();
if ($boInscricaoV) {
    $obListaNVencidas->addCabecalho();
    $obListaNVencidas->ultimoCabecalho->addConteudo("Inscrição");
    $obListaNVencidas->ultimoCabecalho->setWidth( 5 );
    $obListaNVencidas->commitCabecalho();
}
$obListaNVencidas->addCabecalho();
$obListaNVencidas->ultimoCabecalho->addConteudo("Numeração");
$obListaNVencidas->ultimoCabecalho->setWidth( 5 );
$obListaNVencidas->commitCabecalho();
$obListaNVencidas->addCabecalho();
$obListaNVencidas->ultimoCabecalho->addConteudo("Parcela");
$obListaNVencidas->ultimoCabecalho->setWidth( 5 );
$obListaNVencidas->commitCabecalho();
$obListaNVencidas->addCabecalho();
$obListaNVencidas->ultimoCabecalho->addConteudo("Contribuinte");
$obListaNVencidas->ultimoCabecalho->setWidth( 25 );
$obListaNVencidas->commitCabecalho();
$obListaNVencidas->addCabecalho();
$obListaNVencidas->ultimoCabecalho->addConteudo("Vínculo");
$obListaNVencidas->ultimoCabecalho->setWidth( 20 );
$obListaNVencidas->commitCabecalho();
$obListaNVencidas->addCabecalho();
$obListaNVencidas->ultimoCabecalho->addConteudo("Valor");
$obListaNVencidas->ultimoCabecalho->setWidth( 5  );
$obListaNVencidas->commitCabecalho();
$obListaNVencidas->addCabecalho();
$obListaNVencidas->ultimoCabecalho->addConteudo("Vencimento");
$obListaNVencidas->ultimoCabecalho->setWidth( 6 );
$obListaNVencidas->commitCabecalho();
$obListaNVencidas->addCabecalho();
$obListaNVencidas->ultimoCabecalho->addConteudo("Reemitir");
$obListaNVencidas->ultimoCabecalho->setWidth( 5 );
$obListaNVencidas->commitCabecalho();

if ($boInscricaoV) {
    $obListaNVencidas->addDado();
    $obListaNVencidas->ultimoDado->setCampo( "inscricao");
    $obListaNVencidas->ultimoDado->setAlinhamento( 'CENTRO' );
    $obListaNVencidas->commitDado();
}
$obListaNVencidas->addDado();
$obListaNVencidas->ultimoDado->setCampo( "numeracao" );
$obListaNVencidas->ultimoDado->setAlinhamento( 'CENTRO' );
$obListaNVencidas->commitDado();
$obListaNVencidas->addDado();
$obListaNVencidas->ultimoDado->setCampo( "info_parcela" );
$obListaNVencidas->ultimoDado->setAlinhamento( 'DIREITA' );
$obListaNVencidas->commitDado();
$obListaNVencidas->addDado();
$obListaNVencidas->ultimoDado->setCampo( "nom_cgm" );
$obListaNVencidas->ultimoDado->setTitle("CGM: [numcgm] <br> [nom_cgm]");
$obListaNVencidas->ultimoDado->setAlinhamento( 'CENTRO' );
$obListaNVencidas->commitDado();
$obListaNVencidas->addDado();
$obListaNVencidas->ultimoDado->setCampo( "vinculo" );
$obListaNVencidas->ultimoDado->setAlinhamento( 'DIREITA' );
$obListaNVencidas->commitDado();
$obListaNVencidas->addDado();
$obListaNVencidas->ultimoDado->setCampo( "valor_parcela" );
$obListaNVencidas->ultimoDado->setAlinhamento( 'DIREITA');
$obListaNVencidas->commitDado();

$txtNovoVencimento = new Data;
$txtNovoVencimento->setName     ( "dtNovoVencimento"    );
$txtNovoVencimento->setRotulo   ( "Novo Vencimento"     );
$txtNovoVencimento->setTitle    ( "Novo vencimento para a parcela" );
$txtNovoVencimento->setStyle    ( "width:100%;");

$obChkReemitir = new Checkbox;
$obChkReemitir->setName                        ( "vboReemitir"                                );
//$obChkReemitir->setValue                       ( "[cod_lancamento]-[cod_parcela]-[cod_convenio]-[cod_carteira]-[exercicio]-[convenio_atual]-[carteira_atual]-[numeracao]-[vencimento_parcela_br]-[valor_parcela]-[info_parcela]-[numcgm]-[impresso]-[chave_vinculo]-[id_vinculo]-[inscricao]" );

$obChkReemitir->setValue                       ( "[cod_lancamento]§[cod_parcela]§[cod_convenio]§[cod_carteira]§[exercicio_calculo]§[convenio_atual]§[carteira_atual]§[numeracao]§[vencimento_parcela_br]§[valor_parcela]§[info_parcela]§[numcgm]§[impresso]§[chave_vinculo]§[vinculo]§[inscricao]" );

$obListaNVencidas->addDadoComponente                    ( $txtNovoVencimento                          );
$obListaNVencidas->ultimoDado->setAlinhamento           ( 'CENTRO'                                    );
$obListaNVencidas->ultimoDado->setCampo                 ( "novo_vencimento"                           );
$obListaNVencidas->commitDadoComponente                 (                                             );

$obListaNVencidas->addDadoComponente                    ( $obChkReemitir                              );
$obListaNVencidas->ultimoDado->setAlinhamento           ( 'CENTRO'                                    );
$obListaNVencidas->ultimoDado->setCampo                 ( "reemitir"                                  );
$obListaNVencidas->commitDadoComponente                 (                                             );

// checks
$obChkTodos = new Checkbox;
$obChkTodos->setName                        ( "boTodos" );
$obChkTodos->setId                          ( "boTodos" );
$obChkTodos->setRotulo                      ( "Selecionar Todas" );
$obChkTodos->obEvento->setOnChange          ( "selecionarTodos('v');" );
$obChkTodos->montaHTML();

$obChkConsolidar = new Checkbox;
$obChkConsolidar->setName                                ( "boConsolidarV" );
$obChkConsolidar->setId                                  ( "boConsolidar" );
$obChkConsolidar->setDisabled							 ( !$boPodeConsolidar );
$obChkConsolidar->setValue                               ( true );
$obChkConsolidar->setRotulo                              ( "Consolidar Dívidas" );
$obChkConsolidar->obEvento->setOnChange    ( "ConsolidarDividas( this.checked );" );
$obChkConsolidar->montaHTML();

$txtNovoVencimentoConsolidacaoV = new Data;
$txtNovoVencimentoConsolidacaoV->setName     ( "dtNovoVencimentoV"    );
$txtNovoVencimentoConsolidacaoV->setRotulo   ( "Vencimento para Consolidação"     );
$txtNovoVencimentoConsolidacaoV->setTitle    ( "Novo vencimento para a consolidação" );
$txtNovoVencimentoConsolidacaoV->obEvento->setOnChange    ( "AtualizaDatas( this.value );" );
$txtNovoVencimentoConsolidacaoV->setDisabled ( true );
$txtNovoVencimentoConsolidacaoV->montaHTML();

$obTabelaCheckbox = new Tabela;
$obTabelaCheckbox->addLinha();
$obTabelaCheckbox->ultimaLinha->addCelula();
$obTabelaCheckbox->ultimaLinha->ultimaCelula->setColSpan ( 2 );
$obTabelaCheckbox->ultimaLinha->ultimaCelula->setClass   ( $obListaNVencidas->getClassPaginacao() );
$obTabelaCheckbox->ultimaLinha->ultimaCelula->addConteudo( "<div align='right'>Selecionar Todos".$obChkTodos->getHTML()."&nbsp;</div>");
$obTabelaCheckbox->ultimaLinha->commitCelula();
$obTabelaCheckbox->commitLinha();

$obTabelaCheckbox->addLinha();

$obTabelaCheckbox->ultimaLinha->addCelula();
$obTabelaCheckbox->ultimaLinha->ultimaCelula->setColSpan ( 1 );
$obTabelaCheckbox->ultimaLinha->ultimaCelula->setWidth ( "70%" );
$obTabelaCheckbox->ultimaLinha->ultimaCelula->setClass   ( $obListaNVencidas->getClassPaginacao() );
$obTabelaCheckbox->ultimaLinha->ultimaCelula->addConteudo( "<div align='right'>Vencimento para consolidação&nbsp;".$txtNovoVencimentoConsolidacaoV->getHTML()."&nbsp;</div>");
$obTabelaCheckbox->ultimaLinha->commitCelula();

$obTabelaCheckbox->ultimaLinha->addCelula();
$obTabelaCheckbox->ultimaLinha->ultimaCelula->setColSpan ( 1 );
$obTabelaCheckbox->ultimaLinha->ultimaCelula->setClass   ( $obListaNVencidas->getClassPaginacao() );
$obTabelaCheckbox->ultimaLinha->ultimaCelula->addConteudo( "<div align='right'>Consolidar Dívidas".$obChkConsolidar->getHTML()."&nbsp;</div>");
$obTabelaCheckbox->ultimaLinha->commitCelula();

$obTabelaCheckbox->commitLinha();

$obTabelaCheckbox->montaHTML();
$obListaNVencidas->montaHTML();

$stHtmlTmp  = $obListaNVencidas->getHTML();
$stHtmlTmp .= $obTabelaCheckbox->getHTML();

$obSpanNormais = new Span;
$obSpanNormais->setId       ( 'spnListaNormais' );
$obSpanNormais->setValue    ( $stHtmlN );

$obSpanVencidas = new Span;
$obSpanVencidas->setId      ( "spnLista"      );
$obSpanVencidas->setValue   ( $stHtmlTmp );

$rsVinculos = new Recordset;
$rsVinculos->preenche    ( $arVinculos );

$obListaModelos = new Lista;
$obListaModelos->setTitulo    ("Lista de Modelos de Carnês");
$obListaModelos->setRecordSet( $rsVinculos );
$obListaModelos->setMostraPaginacao(false);

$obListaModelos->addCabecalho();
$obListaModelos->ultimoCabecalho->addConteudo("&nbsp;");
$obListaModelos->ultimoCabecalho->setWidth( 5 );
$obListaModelos->commitCabecalho();

$obListaModelos->addCabecalho();
$obListaModelos->ultimoCabecalho->addConteudo("Origem");
$obListaModelos->ultimoCabecalho->setWidth( 25 );
$obListaModelos->commitCabecalho();

$obListaModelos->addCabecalho();
$obListaModelos->ultimoCabecalho->addConteudo("Modelo");
$obListaModelos->ultimoCabecalho->setWidth( 35 );
$obListaModelos->commitCabecalho();

$obListaModelos->addDado();
$obListaModelos->ultimoDado->setCampo( "vinculo" );
$obListaModelos->ultimoDado->setAlinhamento( 'DIREITA');
$obListaModelos->commitDado();

$rsModelos = new RecordSet;
$obRARRCarne->listarModeloDeCarne( $rsModelos, Sessao::read('acao') );

$obCmbModelo = new Select;
$obCmbModelo->setRotulo       ( "Modelo"    );
$obCmbModelo->setTitle        ( "Modelo de carne"    );
$obCmbModelo->setName         ( "cmbModelo" );
$obCmbModelo->addOption       ( "", "Selecione" );
$obCmbModelo->setCampoId      ( "[nom_arquivo]§[cod_modelo]" );
$obCmbModelo->setCampoDesc    ( "nom_modelo" );
$obCmbModelo->preencheCombo    ( $rsModelos );
$obCmbModelo->setStyle        ( "width: 100%;" );

$obListaModelos->addDadoComponente                    ( $obCmbModelo );
$obListaModelos->ultimoDado->setAlinhamento           ( 'CENTRO' );
$obListaModelos->ultimoDado->setCampo                 ( "tipo_modelo" );
$obListaModelos->commitDadoComponente                 ();

$obListaModelos->montaHTML();
$stHtmlTmpModelo  = $obListaModelos->getHTML();

$obSpanModelo = new Span;
$obSpanModelo->setId      ( "spnListaModelo" );
$obSpanModelo->setValue   ( $stHtmlTmpModelo );

// OBJETOS HIDDEN
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
if (isset($stCtrl )) {
    $obHdnCtrl->setValue ( $stCtrl  );
}

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( "oculto" );

$obFormulario = new FormularioAbas;
$obFormulario->addForm  ( $obForm       );
//$obFormulario->addTitulo( 'Reemitir Carnês' );
$obFormulario->addHidden( $obHdnCtrl    );
$obFormulario->addHidden( $obHdnAcao    );
$obFormulario->addAba   ( "A Vencer"    );
$obFormulario->addSpan  ( $obSpanNormais);
$obFormulario->addAba   ( "Vencidas" );
$obFormulario->addSpan  ( $obSpanVencidas);
$obFormulario->addAba   ( "Modelos de Carnês" );
$obFormulario->addSpan  ( $obSpanModelo);

$obFormulario->Cancelar();
//$obFormulario->Ok();
$obFormulario->show();
