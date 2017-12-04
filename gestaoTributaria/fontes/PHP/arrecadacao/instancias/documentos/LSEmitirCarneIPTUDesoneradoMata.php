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
  * Página de Lista de Emissão de Carnês
  * Data de criação : 11/11/2005

  * @author Analista: Fábio Bertoldi
  * @author Programador: Tonismar R. Bernardo

  * @ignore

  * $Id: LSEmitirCarne.php 45716 2011-08-23 14:47:34Z davi.aroldi $

  Caso de uso: uc-05.03.11

  **/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GT_ARR_NEGOCIO."RARRCarne.class.php";
include_once CAM_GT_CIM_NEGOCIO."RCIMLocalizacao.class.php";
include_once CAM_GT_CEM_NEGOCIO."RCEMAtividade.class.php";
include_once CAM_GT_ARR_NEGOCIO."RARRConfiguracao.class.php";

//Definicao dos nomes de arquivos
$stPrograma = "EmitirCarneIPTUDesoneradoMata";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php"; $pgForm = "FM".$stPrograma.".php";
$pgFormVinculo = "FM".$stPrograma."Vinculo.php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

$stCaminho = CAM_GT_ARR_INSTANCIA."documentos/";

$stAcao = 'reemitir';//$_REQUEST['stAcao'];

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
if ($_GET["pg"] and  $_GET["pos"]) {
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
    if ( ($_REQUEST['inNumInscricaoImobiliariaInicial'] == '') || ($_REQUEST['inCodGrupo'] == '') || ($_REQUEST['inNumInscricaoImobiliariaInicial'] == '') ) {
        sistemaLegado::alertaAviso($pgFilt."?stAcao=".$stAcao, "Os campos a seguir deve ser preenchidos: Exercício, Grupo de Crédito, Inscrição Imobiliária", "n_erro", "erro", Sessao::getId(), "../");
        exit;
    }
}

$obRARRConfiguracao = new RARRConfiguracao;
$obRARRConfiguracao->consultar();

$obRARRCarne = new RARRCarne;
$obRARRCarne->setExercicio( $_REQUEST["inExercicio"]);

$arDados = explode("/", $_REQUEST['inCodGrupo']);
$obRARRCarne->setGrupo  ( $arDados[0] );

$obRARRCarne->setInscricaoImobiliariaInicial( $_REQUEST['inNumInscricaoImobiliariaInicial'] );
$obRARRCarne->setInscricaoImobiliariaFinal  ( $_REQUEST['inNumInscricaoImobiliariaFinal']  );

$obRARRCarne->novaListaEmissaoIPTUDesoneradoMata( $rsCarne );

$arTmp = $rsCarne->arElementos;
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
    }
}

Sessao::write( 'vinculo', $arVinculos );
Sessao::write( 'qtd_vinculo', $inQtdVinculos );

$rsCarne->setPrimeiroElemento();

$rsCarne->ordena ('inscricao');
$rsCarne->addFormatacao('valor_parcela','NUMERIC_BR');

$obLista = new Lista;
$obLista->setTitulo    ("Parcelas a Vencer");
$obLista->setRecordSet( $rsCarne  );
$obLista->setMostraPaginacao(false);
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Inscrição");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

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

$obLista->addDado();
$obLista->ultimoDado->setCampo( "inscricao" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "numeracao" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "info_parcela" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
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
$obHdnCtrl->setValue ( $stCtrl  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( "oculto" );

$obFormulario = new FormularioAbas;
$obFormulario->addForm  ( $obForm       );
$obFormulario->addHidden( $obHdnCtrl    );
$obFormulario->addHidden( $obHdnAcao    );
$obFormulario->addAba   ( "A Vencer"    );
$obFormulario->addSpan  ( $obSpanNormais);
$obFormulario->addAba   ( "Vencidas" );
$obFormulario->addSpan  ( $obSpanVencidas);
$obFormulario->addAba   ( "Modelos de Carnês" );
$obFormulario->addSpan  ( $obSpanModelo);

$obFormulario->Cancelar();
$obFormulario->show();
