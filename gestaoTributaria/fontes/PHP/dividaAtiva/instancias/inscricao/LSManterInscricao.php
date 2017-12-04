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
  * Página de Formulario para Inscrição de Dívida
  * Data de criação : 27/09/2006

  * @author Analista: Fábio Bertoldi
  * @author Programador: Diego Bueno Coelho

    * $Id: LSManterInscricao.php 63376 2015-08-21 18:55:42Z arthur $

  Caso de uso: uc-05.04.02
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';

include_once ( CAM_GT_DAT_MAPEAMENTO."TDATModalidade.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATModalidadeAcrescimo.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaAtiva.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRParcela.class.php" );
include_once ( CAM_GT_DAT_COMPONENTES."IPopUpAutoridade.class.php" );
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterInscricao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ( $pgJS );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "inscrever";
}
$boIncluir = false;
if ($_REQUEST['stAcao'] == "incluir") {
    $boIncluir = true;
}

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $_REQUEST['stAcao']  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $_REQUEST['stCtrl']  );

$obTDATDividaAtiva = new TDATDividaAtiva;

//MONTAGEM DO FILTRO
$obTDATDividaAtiva->stExercicio = $_REQUEST['stExercicio'];
$boCalculoGrupo = false;
if ($_REQUEST['inCodGrupo']) {
    $arGrupo = explode ( '/', $_REQUEST['inCodGrupo'] );
    $obTDATDividaAtiva->inCodGrupo 	= $arGrupo[0];
    $obTDATDividaAtiva->inExercicio = $arGrupo[1];
    $boCalculoGrupo = true;
}

if ($_REQUEST['inCodCredito']) {
    $arCredito = explode ( '.', $_REQUEST['inCodCredito'] );
    $obTDATDividaAtiva->inCodCredito	= $arCredito[0];
    $obTDATDividaAtiva->inCodEspecie	= $arCredito[1];
    $obTDATDividaAtiva->inCodGenero		= $arCredito[2];
    $obTDATDividaAtiva->inCodNatureza	= $arCredito[3];
}

if ($_REQUEST['inCGMInicial'] || $_REQUEST['inCGMFinal']) {
    if ($_REQUEST['inCGMInicial'] != "" && $_REQUEST['inCGMFinal'] != "") {
        $obTDATDividaAtiva->inNumCgmInicial = $_REQUEST['inCGMInicial'];
        $obTDATDividaAtiva->inNumCgmFinal   = $_REQUEST['inCGMFinal'];
    }else if($_REQUEST['inCGMInicial'] != "" && $_REQUEST['inCGMFinal'] == ""){
        $obTDATDividaAtiva->inNumCgmInicial = $_REQUEST['inCGMInicial'];
    }else{
        $obTDATDividaAtiva->inNumCgmInicial = $_REQUEST['inCGMFinal'];
    }
}

if ($_REQUEST['inCodImovelInicial']) {
    $obTDATDividaAtiva->inCodIIInicial 	= $_REQUEST['inCodImovelInicial'];
}
if ($_REQUEST['inCodImovelFinal']) {
    $obTDATDividaAtiva->inCodIIFinal 	= $_REQUEST['inCodImovelFinal'];
}
if ($_REQUEST['inNumInscricaoEconomicaInicial']) {
    $obTDATDividaAtiva->inCodIEInicial 	= $_REQUEST['inNumInscricaoEconomicaInicial'];
}
if ($_REQUEST['inNumInscricaoEconomicaFinal']) {
    $obTDATDividaAtiva->inCodIEFinal 	= $_REQUEST['inNumInscricaoEconomicaFinal'];
}

if ($_REQUEST['stDataInicial']) {
    $arTMP = explode( "/", $_REQUEST['stDataInicial'] );
    $obTDATDividaAtiva->dtDataInicial = $arTMP[2].'-'.$arTMP[1].'-'.$arTMP[0];
}
if ($_REQUEST['stDataFinal']) {
    $arTMP = explode( "/", $_REQUEST['stDataFinal'] );
    $obTDATDividaAtiva->dtDataFinal = $arTMP[2].'-'.$arTMP[1].'-'.$arTMP[0];
}
if ($_REQUEST['flValorInicial']) {
    $obTDATDividaAtiva->flValorInicial = str_replace( '.','',$_REQUEST['flValorInicial']);
    $obTDATDividaAtiva->flValorInicial = str_replace( ',','.',$obTDATDividaAtiva->flValorInicial);
//echo $obTDATDividaAtiva->flValorInicial." inicial <br>";
} else {
    $obTDATDividaAtiva->flValorInicial = 0.01;
}
if ($_REQUEST['flValorFinal']) {
    $obTDATDividaAtiva->flValorFinal = str_replace( '.','',$_REQUEST['flValorFinal']);
    $obTDATDividaAtiva->flValorFinal = str_replace( ',','.',$obTDATDividaAtiva->flValorFinal);
} else {
    $obTDATDividaAtiva->flValorFinal = "999999999999.99";
}
$boEmissaoDocumento = $_REQUEST['boEmissaoDocumento'];

/* CARREGA AS INFORMACOES DA MODALIDADE SELECIONADA */
$inCodModalidade = $_REQUEST['inCodModalidade'];
$obTDATModalidade = new TDATModalidade;
$filtro = " \n dm.cod_modalidade = ". $inCodModalidade ." AND ";
$filtro = " WHERE ". substr ( $filtro, 0, ( strlen ( $filtro ) - 4 ) );
$obTDATModalidade->recuperaInfoModalidade( $rsModalidade, $filtro );

$inNumParcelas			= $rsModalidade->getCampo ('qtd_parcela');
$flValorMinimo			= $rsModalidade->getCampo ('vlr_minimo');
$flLimiteInicial		= $rsModalidade->getCampo ('limite_inicial');
$flLimiteInicial		= $rsModalidade->getCampo ('limite_final');
$inCodTipoModalidade	= $rsModalidade->getCampo ('cod_tipo_modalidade');
$timestamp_modalidade	= $rsModalidade->getCampo ('timestamp');
$dtVigenciaInicial		= $rsModalidade->getCampo ('vigencia_inicial');
$inCodDocumento			= $rsModalidade->getCampo ('cod_documento');
$inCodTipoDocumento		= $rsModalidade->getCampo ('cod_tipo_documento');

/* DADOS PARA INSCRICAO */
if (!$_REQUEST['inCodAutoridade']) {
    $obTDATDividaAtiva->inCodAutoridade = 1;
} else {
    $obTDATDividaAtiva->inCodAutoridade = $_REQUEST['inCodAutoridade'];
}

$obTDATDividaAtiva->dtInscricao		= $_REQUEST['dtInscricao'];
$obTDATDividaAtiva->recuperaListaDivida( $rsListaDividas );

if ( $rsListaDividas->eof() ) {
    SistemaLegado::alertaAviso($pgFilt, "Nenhum registro localizado de acordo com os filtros utilizados!", "n_incluir", "erro" );
    exit;
}

$arTMPDados = $rsListaDividas->getElementos();
$arLancamentos = array();
$arLancamentosLista = array();
$inTotLancs = 0;
for ( $inT=0; $inT<count( $arTMPDados ); $inT++ ) {
    if (!$arLancamentosLista[$arTMPDados[$inT]["cod_lancamento"]]) {
        $arLancamentosLista[$arTMPDados[$inT]["cod_lancamento"]] = 1;
        $arLancamentos[$inTotLancs] = $arTMPDados[$inT];
        $inTotLancs++;
    }
}

unset( $arLancamentosLista );
$rsListaDividas->preenche( $arLancamentos );

/* BUSCA ACRESCIMOS DA MODALIDADE */
$obTDATModalidadeAcrescimo = new TDATModalidadeAcrescimo;
$stFiltro = "\n WHERE dma.cod_modalidade = ".$inCodModalidade;
$obTDATModalidadeAcrescimo->recuperaListaAcrescimo ( $rsModalidadeAcrescimo, $stFiltro );

$arListagemDividas = array();
while ( !$rsListaDividas->eof() ) {
    $inNumCGMDivida			= $rsListaDividas->getCampo('numcgm');
    $flValorDividaInscricao = $rsListaDividas->getCampo('valor_aberto');
    $stTipoDivida			= $rsListaDividas->getCampo('tipo_inscricao');
    $inInscricaoDivida		= $rsListaDividas->getCampo('inscricao');
    $dtVencimentoBase		= $rsListaDividas->getCampo('vencimento_base');
    $dtVencimentoBaseBR		= $rsListaDividas->getCampo('vencimento_base_br');
    $timestampVenalDivida	= $rsListaDividas->getCampo('timestamp_venal' );
    $dtVencimentoParcelaArr	= $obTDATDividaAtiva->dtInscricao;
    $arExercicio 			= explode ( '/', $dtVencimentoBaseBR );
    $exercicio_original		= $arExercicio[2];
    $exercicio_divida		= Sessao::getExercicio();

    /* VERIFICA SE O VALOR DA DIVIDA ESTÁ ACIMA DO LIMITE MÍNIMO DA MODALIDADE */
    if ( $rsListaDividas->getCampo('valor_aberto') >= $flValorMinimo ) {
        $arListagemDividas[] = array (
            "contribuinte" 	   => $rsListaDividas->getCampo('numcgm'). " - ". $rsListaDividas->getCampo('nom_cgm'),
            "cod_lancamento"   => $rsListaDividas->getCampo('cod_lancamento'),
            "imposto"		   => $rsListaDividas->getCampo('vinculo'),
            "parcelas"		   => $rsListaDividas->getCampo('nro_parcelas'),
            "valor_original"   => $rsListaDividas->getCampo('valor_aberto'),
            "valor_aberto"     => $rsListaDividas->getCampo('valor_aberto'),
            "valor_lancamento" => $rsListaDividas->getCampo('valor_lancamento'),
            "cod_lancamento"   => $rsListaDividas->getCampo('cod_lancamento'),
            "numcgm"           => $rsListaDividas->getCampo('numcgm'),
            "nom_cgm"          => $rsListaDividas->getCampo('nom_cgm'),
            "vinculo"          => $rsListaDividas->getCampo('vinculo'),
            "id_vinculo"       => $rsListaDividas->getCampo('id_vinculo'),
            "inscricao"        => $rsListaDividas->getCampo('inscricao'),
            "tipo_inscricao"   => $rsListaDividas->getCampo('tipo_inscricao'),
            "vencimento_base"  => $rsListaDividas->getCampo('vencimento_base'),
            "vencimento_base_br" => $rsListaDividas->getCampo('vencimento_base_br'),
            "timestamp_venal"  => $rsListaDividas->getCampo('timestamp_venal'),
            "incluir"          => 'false'
        );

    }//fim do if vericador do valor minimo para inscrever na divida

    $rsListaDividas->proximo();
}

unset( $rsListaDividas );

Sessao::write('inscricaoDA'           , -1 );
Sessao::write('modalidade'            , $rsModalidade->getElementos() );
Sessao::write('lista_dividas_parcelas', $arListagemDividas);

$obIPopUpAutoridade = new IPopUpAutoridade;
$obIPopUpAutoridade->setRotulo           ( "Autoridade" );
$obIPopUpAutoridade->setId               ( 'stNomAutoridade'  );
$obIPopUpAutoridade->setName             ( 'stNomAutoridade'  );
$obIPopUpAutoridade->obCampoCod->setName ( "inCodAutoridade"  );
$obIPopUpAutoridade->obCampoCod->setID   ( "inCodAutoridade"  );
$obIPopUpAutoridade->setTitle            ( "Informe o código da Autoridade" );
$obIPopUpAutoridade->setNull             ( false );

$obCBoxEmitir = new CheckBox;
$obCBoxEmitir->setName   ( "boEmissaoDocumento" );
$obCBoxEmitir->setLabel ("Emitir termo de inscrição em dívida");
if ( count( $arListagemDividas ) > 5000 )
    $obCBoxEmitir->setDisabled( true );

$obHdnRelatorioVariosLancamentos = new Hidden();
$obHdnRelatorioVariosLancamentos->setName("boRelatorioLancamentos");
$obHdnRelatorioVariosLancamentos->setId("boRelatorioLancamentos");

//Define Span para DataGrid
$obSpnIncluir = new Span;
$obSpnIncluir->setId    ( "spnListaIncluir" );

//Define Span para DataGrid
$obSpnTotalizador = new Span;
$obSpnTotalizador->setId    ( "spnListaTotalizador" );

Sessao::write('arPermissoes', array());

if ( count( $arListagemDividas ) <= 5000 ) {
    $rsListagemDividas = new RecordSet;
    $rsListagemDividas->preenche ( $arListagemDividas );

    $obChkIncluir = new Checkbox;
    $obChkIncluir->setName                        ( "boIncluir_[parcelas]"      );
    $obChkIncluir->setChecked                     ( false         );
    $obChkIncluir->setValue                       ( "[cod_lancamento]" );
    $obChkIncluir->obEvento->setOnChange          ( "verificaMarcados();" );

    $rsListagemDividas->addFormatacao('valor_original','NUMERIC_BR');

    $obLista = new Table();
    $obLista->setRecordset( $rsListagemDividas );
    $obLista->setSummary('Lista de Lançamentos');
    //$obLista->setConditional( true , "#efefef" ); // lista zebrada
    $obLista->Head->addCabecalho( 'Contribuinte', 30  );
    $obLista->Head->addCabecalho( 'Inscrição Origem', 10  );
    $obLista->Head->addCabecalho( 'Imposto', 15  );
    $obLista->Head->addCabecalho( 'Parcelas', 10  );
    $obLista->Head->addCabecalho( 'Valor Original', 10  );
    $obLista->Head->addCabecalho( "", 3  );

    $obLista->Body->addCampo( 'contribuinte', 'E' );
    $obLista->Body->addCampo( 'inscricao', 'C' );
    $obLista->Body->addCampo( 'imposto' );
    $obLista->Body->addCampo( 'parcelas', 'C' );
    $obLista->Body->addCampo( 'valor_original', 'D' );

    $obLista->Body->addComponente( $obChkIncluir );
    $obLista->montaHTML();

    $obChkTodosN = new Checkbox;
    $obChkTodosN->setName                        ( "boTodos" );
    $obChkTodosN->setId                          ( "boTodos" );
    $obChkTodosN->setRotulo                      ( "Selecionar Todas" );
    $obChkTodosN->obEvento->setOnChange          ( "selecionarTodos();" );
    $obChkTodosN->montaHTML();

    $obTabelaCheckboxN = new Tabela;
    $obTabelaCheckboxN->addLinha();
    $obTabelaCheckboxN->ultimaLinha->addCelula();
    $obTabelaCheckboxN->ultimaLinha->ultimaCelula->setColSpan ( 2 );
    $obTabelaCheckboxN->ultimaLinha->ultimaCelula->setClass   ( "label" );
    $obTabelaCheckboxN->ultimaLinha->ultimaCelula->addConteudo( "<div align='right'>Selecionar Todos".$obChkTodosN->getHTML()."&nbsp;</div>");
    $obTabelaCheckboxN->ultimaLinha->commitCelula();
    $obTabelaCheckboxN->commitLinha();
    $obTabelaCheckboxN->montaHTML();

    $stHTML = $obLista->getHTML();
    $stHTML .= $obTabelaCheckboxN->getHTML();
    $stHTML = str_replace( "\n" ,"" ,$stHTML );
    $stHTML = str_replace( "  " ,"" ,$stHTML );
    $stHTML = str_replace( "'","\\'",$stHTML );
    $obSpnIncluir->setValue ($stHTML);
} else {
    $obLabelDescricao = new Label;
    $obLabelDescricao->setValue ( count( $arListagemDividas ) );
    $obLabelDescricao->setName   ( "Total de registros a serem inscritos em divida ativa." );
    $obLabelDescricao->setRotulo ( "Inscrições" );
}

$totalizador 	     = 0;
$countListagemDivida = count($arListagemDividas);
if ($countListagemDivida  > 0) {
    for ($i=0; $i<$countListagemDivida; $i++) {
    $totalizador+= $arListagemDividas[$i]['valor_original'];
    }

    $totalizador      = 'R$'.number_format($totalizador, 2, ',', '.');
    $arrayTotalizador = array(array('registros' => $countListagemDivida, 'totalizador' => $totalizador));

    $rsTotalizador = new RecordSet;
    $rsTotalizador->preenche($arrayTotalizador);

    $obLista2 = new Table();
    $obLista2->setRecordset( $rsTotalizador );
    $obLista2->setSummary('Totalizador');
   // $obLista2->setConditional( true , "#efefef" ); // lista zebrada
    $obLista2->Head->addCabecalho( 'Número de registros', 40  );
    $obLista2->Head->addCabecalho( 'Valor total', 40  );

    $obLista2->Body->addCampo( 'registros', 'C' );
    $obLista2->Body->addCampo( 'totalizador', 'C' );

    $obLista2->montaHTML();

    $stHTML = $obLista2->getHTML();
    $stHTML = str_replace( "\n" ,"" ,$stHTML );
    $stHTML = str_replace( "  " ,"" ,$stHTML );
    $stHTML = str_replace( "'","\\'",$stHTML );
    $obSpnTotalizador->setValue ($stHTML);
}

$obHdnCGM = new Hidden;
$obHdnCGM->setName 	("inNumCGM");
$obHdnCGM->setValue	( $obTDATDividaAtiva->inNumCGM );

$obHdnTotalRegistros = new Hidden; //flag para avisar que total de registros > 1000
$obHdnTotalRegistros->setName     ( "inTotalRegistros" );
$obHdnTotalRegistros->setValue    ( count( $arListagemDividas ) );

$obHdnCodGrupo = new Hidden;
$obHdnCodGrupo->setName 	("inCodGrupo");
$obHdnCodGrupo->setValue	( $_REQUEST['inCodGrupo'] );

$obHdnCodCredito = new Hidden;
$obHdnCodCredito->setName 	("inCodCredito");
$obHdnCodCredito->setValue	( $_REQUEST['inCodCredito']	);

$obHdnIIInicial = new Hidden;
$obHdnIIInicial->setName 	("inCodImovelInicial");
$obHdnIIInicial->setValue	( $obTDATDividaAtiva->inCodIIInicial );

$obHdnIIFinal = new Hidden;
$obHdnIIFinal->setName 	("inCodImovelFinal");
$obHdnIIFinal->setValue	( $obTDATDividaAtiva->inCodIIFinal );

$obHdnIEInicial = new Hidden;
$obHdnIEInicial->setName 	("inNumInscricaoEconomicaInicial");
$obHdnIEInicial->setValue	( $obTDATDividaAtiva->inCodIEInicial );

$obHdnIEFinal = new Hidden;
$obHdnIEFinal->setName 	("inNumInscricaoEconomicaFinal");
$obHdnIEFinal->setValue	( $obTDATDividaAtiva->inCodIEFinal );

$obHdnDataInicial = new Hidden;
$obHdnDataInicial->setName 	("stDataInicial");
$obHdnDataInicial->setValue	( $obTDATDividaAtiva->dtDataInicial );

$obHdnDataFinal = new Hidden;
$obHdnDataFinal->setName 	("stDataFinal");
$obHdnDataFinal->setValue	( $obTDATDividaAtiva->dtDataFinal	);

$obHdnValorInicial = new Hidden;
$obHdnValorInicial->setName 	( "flValorInicial" );
$obHdnValorInicial->setValue	( $_REQUEST['flValorInicial'] );

$obHdnValorFinal = new Hidden;
$obHdnValorFinal->setName 	( "flValorFinal" );
$obHdnValorFinal->setValue	( $_REQUEST['flValorFinal']	);

$obHdnCodModalidade = new Hidden;
$obHdnCodModalidade->setName 	("inCodModalidade");
$obHdnCodModalidade->setValue	( $inCodModalidade );

$obHdnDtInscricao = new Hidden;
$obHdnDtInscricao->setName 	("dtInscricao");
$obHdnDtInscricao->setValue	( $obTDATDividaAtiva->dtInscricao	);

$obBscProcesso = new BuscaInner;
$obBscProcesso->setRotulo ( "Processo" );
$obBscProcesso->setTitle  ( "Processo referente à inscrição em dívida." );
$obBscProcesso->setNull   ( true );
$obBscProcesso->obCampoCod->setName ("inProcesso");
$obBscProcesso->obCampoCod->setId   ("inProcesso");
$obBscProcesso->obCampoCod->setValue( $inProcesso );
$obBscProcesso->obCampoCod->obEvento->setOnChange( "buscaValor('buscaProcesso','');" );
$obBscProcesso->obCampoCod->setSize ( strlen($stMascaraProcesso) );
$obBscProcesso->obCampoCod->setMaxLength( strlen($stMascaraProcesso) );
$obBscProcesso->obCampoCod->obEvento->setOnKeyUp ("mascaraDinamico('".$stMascaraProcesso."', this, event);");
$obBscProcesso->setFuncaoBusca ( "abrePopUp('".CAM_GA_PROT_POPUPS."processo/FLBuscaProcessos.php', 'frm','inProcesso','campoInner2','','".Sessao::getId()."','800','550')" );

$obBtnOK = new OK;
$obBtnOK->setName              ( "btnOk" );
$obBtnOK->obEvento->setOnClick ( "validarListar();" );

$obBtnLimpar = new Limpar;
//$obBtnLimpar->obEvento->setOnClick( "Limpar();" );

$botoesSpanBotoes = array ( $obBtnOK, $obBtnLimpar );

$obForm = new Form;
$obForm->setAction ( $pgProc );
$obForm->setTarget ( 'oculto' );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm );
$obFormulario->setAjuda     ( "UC-05.04.02" );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );

$obFormulario->addHidden    ( $obHdnTotalRegistros  );
$obFormulario->addHidden 	( $obHdnCGM 			);
$obFormulario->addHidden 	( $obHdnCodGrupo 		);
$obFormulario->addHidden 	( $obHdnCodCredito		);
$obFormulario->addHidden 	( $obHdnIIInicial 		);
$obFormulario->addHidden 	( $obHdnIIFinal 		);
$obFormulario->addHidden 	( $obHdnIEInicial 		);
$obFormulario->addHidden 	( $obHdnIEFinal 		);
$obFormulario->addHidden 	( $obHdnDataInicial 	);
$obFormulario->addHidden 	( $obHdnDataFinal 		);
$obFormulario->addHidden 	( $obHdnValorInicial 	);
$obFormulario->addHidden 	( $obHdnValorFinal 		);
$obFormulario->addHidden 	( $obHdnCodModalidade 	);
$obFormulario->addHidden 	( $obHdnDtInscricao		);
$obFormulario->addHidden    ( $obHdnRelatorioVariosLancamentos );
$obFormulario->addTitulo ("Dados para Inscrição");
if ( count( $arListagemDividas ) <= 5000 ) {
    $obFormulario->addSpan      ( $obSpnIncluir );
    $obFormulario->addSpan      ( $obSpnTotalizador );
} else {
    $obFormulario->addComponente ( $obLabelDescricao );
}

$obIPopUpAutoridade->geraFormulario ( $obFormulario );
$obFormulario->addComponente ( $obBscProcesso       );
$obFormulario->addComponente ( $obCBoxEmitir        );
$obFormulario->defineBarra   ( $botoesSpanBotoes, 'left', '' );

$obFormulario->show();
?>