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
    * Página de Formulario de Inclusao de Modalidade

    * Data de Criação   : 16/04/2007

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @ignore

    * $Id: FMManterModalidadeDivida.php 60946 2014-11-25 19:59:14Z evandro $

    *Casos de uso: uc-05.04.07

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GA_NORMAS_CLASSES."componentes/IPopUpNorma.class.php" );
include_once ( CAM_GA_ADM_COMPONENTES."IPopUpFuncao.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoFuncao.class.php" );
include_once ( CAM_GA_ADM_COMPONENTES."ITextBoxSelectDocumento.class.php" );
include_once ( CAM_GT_MON_COMPONENTES."IPopUpCredito.class.php" );
include_once ( CAM_GT_MON_COMPONENTES."IPopUpAcrescimo.class.php" );

if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = 'incluir';
}

//Define o nome dos arquivos PHP
$stPrograma    = "ManterModalidade";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php?".Sessao::getId();
$pgJs          = "JS".$stPrograma.".js";

include_once( $pgJs );

$obTFuncao = new TAdministracaoFuncao;
$stMascaraFuncao = $obTFuncao->recuperaMascaraFuncao();

//fundamentacao legal
$obIPopUpNorma = new IPopUpNorma;
$obIPopUpNorma->obInnerNorma->setRotulo ( "Fundamentação Legal" );
$obIPopUpNorma->obInnerNorma->setTitle ( "Informe a norma que regulamenta a modalidade." );

//regra de utilizacao
$obIPopUpFuncao = new IPopUpFuncao;
$obIPopUpFuncao->obInnerFuncao->setRotulo ( "Regra de Utilização" );
$obIPopUpFuncao->obInnerFuncao->setTitle  ( "Informe qual a regra a ser utilizada pelas parcelas na Inscrição em divida ativa." );
$obIPopUpFuncao->obInnerFuncao->setNull   ( false );
$obIPopUpFuncao->setCodModulo( 33 );
$obIPopUpFuncao->setCodBiblioteca( 1 );

//creditos
$obIPopUpCredito = new IPopUpCredito;
$obIPopUpCredito->setRotulo ( "*Crédito" );
$obIPopUpCredito->setNULL ( true );

//acrescimos
$obIPopUpAcrescimo = new IPopUpAcrescimo;
$obIPopUpAcrescimo->setTitle ( "Informe o acréscimo legal a ser utilizado pela modalidade." );
$obIPopUpAcrescimo->setNULL ( true );
$obIPopUpAcrescimo->setRotulo ( "*Acréscimo" );

//documentos
$obITextBoxSelectDocumento = new ITextBoxSelectDocumento;
$obITextBoxSelectDocumento->setCodAcao( Sessao::read('acao') );
$obITextBoxSelectDocumento->obTextBoxSelectDocumento->setRotulo ( "*Documento" );
$obITextBoxSelectDocumento->obTextBoxSelectDocumento->setNULL ( true );

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $_REQUEST['stAcao']  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $_REQUEST['stCtrl']  );

//incidencia do acrescimo
$obRdbAcrescimoPagamento = new Radio;
$obRdbAcrescimoPagamento->setRotulo   ( "*Incidência" );
$obRdbAcrescimoPagamento->setTitle    ( "Incidência." );
$obRdbAcrescimoPagamento->setName     ( "stAcrescimoIncidencia" );
$obRdbAcrescimoPagamento->setLabel    ( "Pagamentos" );
$obRdbAcrescimoPagamento->setValue    ( "true" );
$obRdbAcrescimoPagamento->setNull     ( true );

//incidencia do acrescimo
$obRdbAcrescimoInscricao = new Radio;
$obRdbAcrescimoInscricao->setRotulo   ( "*Incidência" );
$obRdbAcrescimoInscricao->setTitle    ( "Incidência." );
$obRdbAcrescimoInscricao->setName     ( "stAcrescimoIncidencia" );
$obRdbAcrescimoInscricao->setLabel    ( "Inscrição em Dívida / Cobranças" );
$obRdbAcrescimoInscricao->setValue    ( "false" );
$obRdbAcrescimoInscricao->setNull     ( true );

$obRdbAcrescimoTodos = new Radio;
$obRdbAcrescimoTodos->setRotulo   ( "*Incidência" );
$obRdbAcrescimoTodos->setTitle    ( "Incidência." );
$obRdbAcrescimoTodos->setName     ( "stAcrescimoIncidencia" );
$obRdbAcrescimoTodos->setLabel    ( "Ambos" );
$obRdbAcrescimoTodos->setValue    ( "ambos" );
$obRdbAcrescimoTodos->setNull     ( true );

if ($_REQUEST['stAcao'] == "alterar") {
    $obIPopUpNorma->setCodNorma( $_REQUEST["inCodNorma"] );

    $obHdnCodModalidade =  new Hidden;
    $obHdnCodModalidade->setName   ( "inCodModalidade" );
    $obHdnCodModalidade->setValue  ( $_REQUEST["inCodModalidade"] );

    $obLabelCodModalidade = new Label;
    $obLabelCodModalidade->setValue ( $_REQUEST["inCodModalidade"] );
    $obLabelCodModalidade->setName   ( "CodModalidade" );
    $obLabelCodModalidade->setRotulo ( "Código" );

    $obLabelDescricao = new Label;
    $obLabelDescricao->setValue ( $_REQUEST["stDescricao"] );
    $obLabelDescricao->setName   ( "Descricao" );
    $obLabelDescricao->setRotulo ( "Descrição" );

    $obLabelDescricaoTipo = new Label;
    $obLabelDescricaoTipo->setValue ( $_REQUEST["stTipoModalidade"] );
    $obLabelDescricaoTipo->setName   ( "Tipo" );
    $obLabelDescricaoTipo->setRotulo ( "Tipo" );

    $inTipo = $_REQUEST["inCodTipoModalidade"];
    $dtVigenciaInicio = $_REQUEST[ "stVigenciaInicial" ];
    $dtVigenciaFim = $_REQUEST[ "stVigenciaFinal" ];
    $inForma = $_REQUEST["inCodFormaInscricao"];

    $obIPopUpFuncao->setCodFuncao ( $_REQUEST["inCodModulo"].".".$_REQUEST["inCodBiblioteca"].".".$_REQUEST["inCodFuncao"] );
} else {
    if ($_REQUEST["stTipoModalidade"] == "inscricao") {
        $inTipo = 1;
    }else
        if ($_REQUEST["stTipoModalidade"] == "consolidacao") {
            $inTipo = 2;
        } else {
            $inTipo = 3;
        }
}

$obHdnTipoModalidade = new Hidden;
$obHdnTipoModalidade->setName   ( "inTipo" );
$obHdnTipoModalidade->setValue  ( $inTipo );

$obLabelIntervalo = new Label;
$obLabelIntervalo->setValue ( "até" );

//Data de vigencia
$obDtVigenciaInicio  = new Data;
$obDtVigenciaInicio->setName ( "dtVigenciaInicio" );
$obDtVigenciaInicio->setValue ( $dtVigenciaInicio );
$obDtVigenciaInicio->setRotulo ( "Vigência" );
$obDtVigenciaInicio->setTitle ( "Data de vigência da modalidade." );
$obDtVigenciaInicio->setMaxLength ( 20 );
$obDtVigenciaInicio->setSize ( 10 );
$obDtVigenciaInicio->setNull ( false );
$obDtVigenciaInicio->obEvento->setOnChange ( "validaData1500( this );" );

$obDtVigenciaFim  = new Data;
$obDtVigenciaFim->setName ( "dtVigenciaFim" );
$obDtVigenciaFim->setValue ( $dtVigenciaFim );
$obDtVigenciaFim->setRotulo ( "Vigência" );
$obDtVigenciaFim->setTitle ( "Data de vigência da modalidade." );
$obDtVigenciaFim->setMaxLength ( 20 );
$obDtVigenciaFim->setSize ( 10 );
$obDtVigenciaFim->setNull ( false );
$obDtVigenciaFim->obEvento->setOnChange ( "validaData1500( this );" );

//Descricao
$obTxtDescricao = new TextBox;
$obTxtDescricao->setRotulo ( "Descrição" );
$obTxtDescricao->setTitle ( "Informe a descrição para a modalidade." );
$obTxtDescricao->setName ( "stDescricao" );
$obTxtDescricao->setValue ( $stDescricao );
$obTxtDescricao->setSize ( 80 );
$obTxtDescricao->setMaxLength ( 80 );
$obTxtDescricao->setNull ( false );
$obTxtDescricao->setInteiro ( false );

//Forma de inscricao (valor total)
$obRdbValorTotal = new Radio;
$obRdbValorTotal->setRotulo   ( "Forma de Inscrição" );
$obRdbValorTotal->setTitle    ( "Forma de inscrição." );
$obRdbValorTotal->setName     ( "stFormaInscricao" );
$obRdbValorTotal->setLabel    ( "Valor Total" );
$obRdbValorTotal->setValue    ( "valor_total" );
$obRdbValorTotal->setNull     ( false );

//Forma de inscricao (valor total por credito)
$obRdbValorTotalCredito = new Radio;
$obRdbValorTotalCredito->setRotulo   ( "Forma de Inscrição" );
$obRdbValorTotalCredito->setTitle    ( "Forma de inscrição." );
$obRdbValorTotalCredito->setName     ( "stFormaInscricao" );
$obRdbValorTotalCredito->setLabel    ( "Valor Total por Créditos" );
$obRdbValorTotalCredito->setValue    ( "valor_total_credito" );
$obRdbValorTotalCredito->setNull     ( false );

//Forma de inscricao (parcelas individuais)
$obRdbParcelaIndividual = new Radio;
$obRdbParcelaIndividual->setRotulo   ( "Forma de Inscrição" );
$obRdbParcelaIndividual->setTitle    ( "Forma de inscrição." );
$obRdbParcelaIndividual->setName     ( "stFormaInscricao" );
$obRdbParcelaIndividual->setLabel    ( "Parcelas Individuais" );
$obRdbParcelaIndividual->setValue    ( "parcela_individual" );
$obRdbParcelaIndividual->setNull     ( false );

//Forma de inscricao (parcelas individuais por credito)
$obRdbParcelaIndividualCredito = new Radio;
$obRdbParcelaIndividualCredito->setRotulo   ( "Forma de Inscrição" );
$obRdbParcelaIndividualCredito->setTitle    ( "Forma de inscrição." );
$obRdbParcelaIndividualCredito->setName     ( "stFormaInscricao" );
$obRdbParcelaIndividualCredito->setLabel    ( "Parcelas Individuais por Crédito" );
$obRdbParcelaIndividualCredito->setValue    ( "parcela_individual_credito" );
$obRdbParcelaIndividualCredito->setNull     ( false );

switch ($inForma) {
    case 1:
        $obRdbValorTotal->setChecked( true );
        break;

    case 2:
        $obRdbValorTotalCredito->setChecked( true );
        break;

    case 3:
        $obRdbParcelaIndividual->setChecked( true );
        break;

    case 4:
        $obRdbParcelaIndividualCredito->setChecked( true );
        break;
}

//botoes do credito
$obBtnIncluirCredito = new Button;
$obBtnIncluirCredito->setName              ( "btnIncluirCredito" );
$obBtnIncluirCredito->setValue             ( "Incluir" );
$obBtnIncluirCredito->setTipo              ( "button" );
$obBtnIncluirCredito->obEvento->setOnClick ( "montaParametrosGET('IncluirCredito', 'inCodCredito,stIncidencia', true);" );
$obBtnIncluirCredito->setDisabled          ( false );

$obBtnLimparCredito = new Button;
$obBtnLimparCredito->setName               ( "btnLimparCredito" );
$obBtnLimparCredito->setValue              ( "Limpar" );
$obBtnLimparCredito->setTipo               ( "button" );
$obBtnLimparCredito->obEvento->setOnClick  ( "ajaxJavaScript('".$pgOcul."', 'limpaCredito');" );
$obBtnLimparCredito->setDisabled           ( false );

$botoesCredito = array ( $obBtnIncluirCredito , $obBtnLimparCredito );

$obSpnListaCredito = new Span;
$obSpnListaCredito->setID("spnListaCredito");

//botoes do acrescimo
$obBtnIncluirAcrescimo = new Button;
$obBtnIncluirAcrescimo->setName              ( "btnIncluirAcrescimo" );
$obBtnIncluirAcrescimo->setValue             ( "Incluir" );
$obBtnIncluirAcrescimo->setTipo              ( "button" );
$obBtnIncluirAcrescimo->obEvento->setOnClick ( "montaParametrosGET('IncluirAcrescimo', 'inCodAcrescimo,inCodFuncaoAC,stIncidencia,stAcrescimoIncidencia', true);" );
$obBtnIncluirAcrescimo->setDisabled          ( false );

$obBtnLimparAcrescimo = new Button;
$obBtnLimparAcrescimo->setName               ( "btnLimparAcrescimo" );
$obBtnLimparAcrescimo->setValue              ( "Limpar" );
$obBtnLimparAcrescimo->setTipo               ( "button" );
$obBtnLimparAcrescimo->obEvento->setOnClick  ( "ajaxJavaScript('".$pgOcul."', 'limpaAcrescimo');" );
$obBtnLimparAcrescimo->setDisabled           ( false );

$botoesAcrescimo = array ( $obBtnIncluirAcrescimo , $obBtnLimparAcrescimo );

$obSpnListaAcrescimo = new Span;
$obSpnListaAcrescimo->setID("spnListaAcrescimo");

//busca funcao do acrescimo
$obInnerFuncaoAcrescimo = new BuscaInner;
$obInnerFuncaoAcrescimo->setNull             ( true );
$obInnerFuncaoAcrescimo->setTitle            ( "Informe a regra de utilização para o acréscimo." );
$obInnerFuncaoAcrescimo->setRotulo           ( "*Regra de Utilização" );
$obInnerFuncaoAcrescimo->setId               ( "stFuncaoAC"  );
$obInnerFuncaoAcrescimo->obCampoCod->setName ( "inCodFuncaoAC" );
$obInnerFuncaoAcrescimo->obCampoCod->setInteiro ( true );
$obInnerFuncaoAcrescimo->obCampoCod->obEvento->setOnKeyUp("mascaraDinamico('".$stMascaraFuncao."', this, event);");
$obInnerFuncaoAcrescimo->obCampoCod->setMinLength ( strlen($stMascaraFuncao) );
$obInnerFuncaoAcrescimo->obCampoCod->setSize ( strlen($stMascaraFuncao) );
$obInnerFuncaoAcrescimo->setFuncaoBusca      (  "abrePopUp('".CAM_GA_ADM_POPUPS."funcao/FLBuscarFuncao.php','frm','inCodFuncaoAC','stFuncaoAC','','".Sessao::getId()."&stCodModulo=33&stCodBiblioteca=2&','800','550');" );

$stOnChange = "ajaxJavaScript('".$pgOcul."&inCodFuncaoAC='+this.value+'&stCodModulo=33&stCodBiblioteca=2','preencheFuncaoAC');";
$obInnerFuncaoAcrescimo->obCampoCod->obEvento->setOnChange( $stOnChange );

//Tipo da reducao (valor percentual)
$obRdbTipoReducaoValorPercetual = new Radio;
$obRdbTipoReducaoValorPercetual->setRotulo   ( "*Tipo de Redução" );
$obRdbTipoReducaoValorPercetual->setTitle    ( "Informe o tipo da redução a ser utilizada pela modalidade." );
$obRdbTipoReducaoValorPercetual->setName     ( "stTipoReducao" );
$obRdbTipoReducaoValorPercetual->setLabel    ( "Valor Percentual" );
$obRdbTipoReducaoValorPercetual->setValue    ( "valor_percentual" );
$obRdbTipoReducaoValorPercetual->setNull     ( true );

//Tipo de reducao (valor absoluto)
$obRdbTipoReducaoValorAbsoluto = new Radio;
$obRdbTipoReducaoValorAbsoluto->setRotulo   ( "*Tipo de Redução" );
$obRdbTipoReducaoValorAbsoluto->setTitle    ( "Informe o tipo da redução a ser utilizada pela modalidade." );
$obRdbTipoReducaoValorAbsoluto->setName     ( "stTipoReducao" );
$obRdbTipoReducaoValorAbsoluto->setLabel    ( "Valor Absoluto" );
$obRdbTipoReducaoValorAbsoluto->setValue    ( "valor_absoluto" );
$obRdbTipoReducaoValorAbsoluto->setNull     ( true );

//Valor
$obTxtValor = new Numerico;
$obTxtValor->setRotulo ( "*Valor" );
$obTxtValor->setTitle ( "Informe o valor da redução." );
$obTxtValor->setName ( "stValor" );
$obTxtValor->setValue ( $stValor );
$obTxtValor->setDecimais ( 2 );
$obTxtValor->setMaxValue  ( 99999999999999.99 );
$obTxtValor->setNull      ( true );
$obTxtValor->setNegativo  ( false );
$obTxtValor->setSize    ( 20 );
$obTxtValor->setMaxLength ( 20 );

$obRdbIncidenciaCredito = new Radio;
$obRdbIncidenciaCredito->setRotulo   ( "*Incidência" );
$obRdbIncidenciaCredito->setTitle    ( "Incidência" );
$obRdbIncidenciaCredito->setName     ( "stIncidencia" );
$obRdbIncidenciaCredito->setLabel    ( "Crédito" );
$obRdbIncidenciaCredito->setValue    ( "credito" );
$obRdbIncidenciaCredito->setNull     ( true );
$obRdbIncidenciaCredito->obEvento->setOnChange ( "ajaxJavaScript('".$pgOcul."', 'montaIncidenciaCredito');" );

$obRdbIncidenciaAcrescimo = new Radio;
$obRdbIncidenciaAcrescimo->setRotulo   ( "*Incidência" );
$obRdbIncidenciaAcrescimo->setTitle    ( "Incidência" );
$obRdbIncidenciaAcrescimo->setName     ( "stIncidencia" );
$obRdbIncidenciaAcrescimo->setLabel    ( "Acréscimo" );
$obRdbIncidenciaAcrescimo->setValue    ( "acrescimo" );
$obRdbIncidenciaAcrescimo->setNull     ( true );
$obRdbIncidenciaAcrescimo->obEvento->setOnChange ( "ajaxJavaScript('".$pgOcul."', 'montaIncidenciaAcrescimo');" );

//busca funcao reducao
$obInnerFuncaoReducao = new BuscaInner;
$obInnerFuncaoReducao->setNull             ( true );
$obInnerFuncaoReducao->setTitle            ( "Informe a regra de utilização para a redução." );
$obInnerFuncaoReducao->setRotulo           ( "*Regra de Utilização" );
$obInnerFuncaoReducao->setId               ( "stFuncaoRD"  );
$obInnerFuncaoReducao->obCampoCod->setName ( "inCodFuncaoRD" );
$obInnerFuncaoReducao->obCampoCod->setInteiro ( true );
$obInnerFuncaoReducao->obCampoCod->obEvento->setOnKeyUp("mascaraDinamico('".$stMascaraFuncao."', this, event);");
$obInnerFuncaoReducao->obCampoCod->setMinLength ( strlen($stMascaraFuncao) );
$obInnerFuncaoReducao->obCampoCod->setSize ( strlen($stMascaraFuncao) );
$obInnerFuncaoReducao->setFuncaoBusca      (  "abrePopUp('".CAM_GA_ADM_POPUPS."funcao/FLBuscarFuncao.php','frm','inCodFuncaoRD','stFuncaoRD','','".Sessao::getId()."&stCodModulo=33&stCodBiblioteca=3&','800','550');" );

$stOnChange = "ajaxJavaScript('".$pgOcul."&inCodFuncaoRD='+this.value+'&stCodModulo=33&stCodBiblioteca=3','preencheFuncaoRD');";
$obInnerFuncaoReducao->obCampoCod->obEvento->setOnChange( $stOnChange );

$obBtnIncluirReducaoCredito = new Button;
$obBtnIncluirReducaoCredito->setName              ( "btnIncluirReducaoCredito" );
$obBtnIncluirReducaoCredito->setValue             ( "Incluir" );
$obBtnIncluirReducaoCredito->setTipo              ( "button" );
$obBtnIncluirReducaoCredito->obEvento->setOnClick ( "montaParametrosGET('incluirReducaoCredito', 'cmbAcrescimo,cmbCredito,stIncidencia', true);" );
$obBtnIncluirReducaoCredito->setDisabled          ( false );

$obBtnLimparReducaoCredito = new Button;
$obBtnLimparReducaoCredito->setName               ( "btnLimparReducaoCredito" );
$obBtnLimparReducaoCredito->setValue              ( "Limpar" );
$obBtnLimparReducaoCredito->setTipo               ( "button" );
$obBtnLimparReducaoCredito->obEvento->setOnClick  ( "ajaxJavaScript('".$pgOcul."', 'limpaReducaoCredito');" );
$obBtnLimparReducaoCredito->setDisabled           ( false );

$botoesReducaoCredito = array ( $obBtnIncluirReducaoCredito, $obBtnLimparReducaoCredito );

//botoes da reducao
$obBtnIncluirReducao = new Button;
$obBtnIncluirReducao->setName              ( "btnIncluirReducao" );
$obBtnIncluirReducao->setValue             ( "Incluir" );
$obBtnIncluirReducao->setTipo              ( "button" );

$obBtnIncluirReducao->obEvento->setOnClick ( "montaParametrosGET('incluirReducao', 'inCodFuncaoRD,stValor,stTipoReducao', true);" );
$obBtnIncluirReducao->setDisabled          ( false );

$obBtnLimparReducao = new Button;
$obBtnLimparReducao->setName               ( "btnLimparReducao" );
$obBtnLimparReducao->setValue              ( "Limpar" );
$obBtnLimparReducao->setTipo               ( "button" );
$obBtnLimparReducao->obEvento->setOnClick  ( "ajaxJavaScript('".$pgOcul."', 'limpaReducao');" );
$obBtnLimparReducao->setDisabled           ( false );

$botoesReducao = array ( $obBtnIncluirReducao , $obBtnLimparReducao );

$obSpnListaReducao = new Span;
$obSpnListaReducao->setID("spnListaReducao");

$obSpnListaReducaoIncidencia = new Span;
$obSpnListaReducaoIncidencia->setID("spnListaReducaoIncidencia");

$obSpnListaReducaoCreditos = new Span;
$obSpnListaReducaoCreditos->setID("spnListaReducaoCreditos");

//limite valor inicial
$obTxtLimiteValorInicial = new Moeda;
$obTxtLimiteValorInicial->setRotulo  ( '*Limite Valor Inicial (R$)');
$obTxtLimiteValorInicial->setTitle   ( 'Informe o limite inicial para utilizar a definição de parcelas.');
$obTxtLimiteValorInicial->setName    ( 'flLimiteValorInicial');
$obTxtLimiteValorInicial->setValue   ( $flLimiteValorInicial );
$obTxtLimiteValorInicial->setDecimais ( 2 );
$obTxtLimiteValorInicial->setMaxValue  ( 99999999999999.99 );
$obTxtLimiteValorInicial->setNull      ( true );
$obTxtLimiteValorInicial->setNegativo  ( false );
$obTxtLimiteValorInicial->setNaoZero   ( true );
$obTxtLimiteValorInicial->setSize    ( 20 );
$obTxtLimiteValorInicial->setMaxLength ( 20 );

//limite valor final
$obTxtLimiteValorFinal = new Moeda;
$obTxtLimiteValorFinal->setRotulo  ( '*Limite Valor Final (R$)');
$obTxtLimiteValorFinal->setTitle   ( 'Informe o limite final para utilizar a definição de parcelas.');
$obTxtLimiteValorFinal->setName    ( 'flLimiteValorFinal');
$obTxtLimiteValorFinal->setValue   ( $flLimiteValorFinal );
$obTxtLimiteValorFinal->setDecimais ( 2 );
$obTxtLimiteValorFinal->setMaxValue  ( 99999999999999.99 );
$obTxtLimiteValorFinal->setNull      ( true );
$obTxtLimiteValorFinal->setNegativo  ( false );
$obTxtLimiteValorFinal->setNaoZero   ( true );
$obTxtLimiteValorFinal->setSize    ( 20 );
$obTxtLimiteValorFinal->setMaxLength ( 20 );

//Quantidade de Parcelas
$obTxtQtdParcelas = new TextBox;
$obTxtQtdParcelas->setRotulo ( "*Quantidade de Parcelas" );
$obTxtQtdParcelas->setTitle ( "Informe a quantidade de parcelas a serem utilizadas." );
$obTxtQtdParcelas->setName ( "inQtdParcelas" );
$obTxtQtdParcelas->setValue ( $inQtdParcelas );
$obTxtQtdParcelas->setSize ( 20 );
$obTxtQtdParcelas->setMaxLength ( 10 );
$obTxtQtdParcelas->setNull ( true );
$obTxtQtdParcelas->setInteiro ( true );

//valor minimo
$obTxtValorMinimo = new Moeda;
$obTxtValorMinimo->setRotulo  ( '*Valor Mínimo (R$)');
$obTxtValorMinimo->setTitle   ( 'Informe o valor mínimo para as parcelas.');
$obTxtValorMinimo->setName    ( 'flValorMinimo');
$obTxtValorMinimo->setValue   ( $flValorMinimo );
$obTxtValorMinimo->setDecimais ( 2 );
$obTxtValorMinimo->setMaxValue  ( 99999999999999.99 );
$obTxtValorMinimo->setNull      ( true );
$obTxtValorMinimo->setNegativo  ( false );
$obTxtValorMinimo->setNaoZero   ( true );
$obTxtValorMinimo->setSize    ( 20 );
$obTxtValorMinimo->setMaxLength ( 20 );

//botoes da parcela
$obBtnIncluirParcela = new Button;
$obBtnIncluirParcela->setName              ( "btnIncluirParcela" );
$obBtnIncluirParcela->setValue             ( "Incluir" );
$obBtnIncluirParcela->setTipo              ( "button" );
$obBtnIncluirParcela->obEvento->setOnClick ( "montaParametrosGET('incluirParcela', 'flLimiteValorInicial,flLimiteValorFinal,inQtdParcelas,flValorMinimo', true);" );
$obBtnIncluirParcela->setDisabled          ( false );

$obBtnLimparParcela = new Button;
$obBtnLimparParcela->setName               ( "btnLimparParcela" );
$obBtnLimparParcela->setValue              ( "Limpar" );
$obBtnLimparParcela->setTipo               ( "button" );
$obBtnLimparParcela->obEvento->setOnClick  ( "ajaxJavaScript('".$pgOcul."', 'limpaParcela');" );
$obBtnLimparParcela->setDisabled           ( false );

$botoesParcela = array ( $obBtnIncluirParcela , $obBtnLimparParcela );

$obSpnListaParcela = new Span;
$obSpnListaParcela->setID("spnListaParcela");

//botoes do documento
$obBtnIncluirDocumento = new Button;
$obBtnIncluirDocumento->setName              ( "btnIncluirDocumento" );
$obBtnIncluirDocumento->setValue             ( "Incluir" );
$obBtnIncluirDocumento->setTipo              ( "button" );
$obBtnIncluirDocumento->obEvento->setOnClick ( "montaParametrosGET('IncluirDocumento', 'stCodDocumento', true);" );
$obBtnIncluirDocumento->setDisabled          ( false );

$obBtnLimparDocumento = new Button;
$obBtnLimparDocumento->setName               ( "btnLimparDocumento" );
$obBtnLimparDocumento->setValue              ( "Limpar" );
$obBtnLimparDocumento->setTipo               ( "button" );
$obBtnLimparDocumento->obEvento->setOnClick  ( "ajaxJavaScript('".$pgOcul."', 'limpaDocumento');" );
$obBtnLimparDocumento->setDisabled           ( false );

$botoesDocumento = array ( $obBtnIncluirDocumento, $obBtnLimparDocumento );

$obSpnListaDocumento = new Span;
$obSpnListaDocumento->setID("spnListaDocumento");

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgProc );
$obForm->settarget ( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->setAjuda      ( "UC-05.04.07" );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addHidden     ( $obHdnTipoModalidade );
$obFormulario->addTitulo     ( "Dados para Modalidade" );
if ($_REQUEST['stAcao'] == "alterar") {
    $obFormulario->addHidden     ( $obHdnCodModalidade );
    $obFormulario->addComponente ( $obLabelCodModalidade );
    $obFormulario->addComponente ( $obLabelDescricao );
    $obFormulario->addComponente ( $obLabelDescricaoTipo );
}

$obFormulario->agrupaComponentes ( array($obDtVigenciaInicio, $obLabelIntervalo, $obDtVigenciaFim) );

if ($_REQUEST['stAcao'] == "incluir") {
    $obFormulario->addComponente ( $obTxtDescricao );
}

$obIPopUpNorma->geraFormulario ( $obFormulario );

if ($inTipo == 1) {
    $obFormulario->agrupaComponentes ( array($obRdbValorTotal, $obRdbValorTotalCredito, $obRdbParcelaIndividual, $obRdbParcelaIndividualCredito) );
}

$obIPopUpFuncao->geraFormulario ( $obFormulario );

$obFormulario->addTitulo     ( "Créditos" );
$obIPopUpCredito->geraFormulario ( $obFormulario );
$obFormulario->defineBarra   ( $botoesCredito, 'left', '' );
$obFormulario->addSpan       ( $obSpnListaCredito );

$obFormulario->addTitulo     ( "Acréscimos Legais" );
$obIPopUpAcrescimo->geraFormulario ( $obFormulario );
$obFormulario->addComponente ( $obInnerFuncaoAcrescimo );
$obFormulario->agrupaComponentes ( array( $obRdbAcrescimoPagamento, $obRdbAcrescimoInscricao, $obRdbAcrescimoTodos ) );
$obFormulario->defineBarra   ( $botoesAcrescimo, 'left', '' );
$obFormulario->addSpan       ( $obSpnListaAcrescimo );

if ($inTipo != 1) {
    $obFormulario->addTitulo     ( "Reduções" );
    $obFormulario->agrupaComponentes ( array( $obRdbTipoReducaoValorPercetual, $obRdbTipoReducaoValorAbsoluto ) );
    $obFormulario->addComponente ( $obTxtValor );
    $obFormulario->agrupaComponentes ( array($obRdbIncidenciaCredito, $obRdbIncidenciaAcrescimo ) );
    $obFormulario->addSpan       ( $obSpnListaReducaoIncidencia );
    $obFormulario->defineBarra   ( $botoesReducaoCredito, 'left', '' );
    $obFormulario->addSpan       ( $obSpnListaReducaoCreditos );

    $obFormulario->addComponente ( $obInnerFuncaoReducao );
    $obFormulario->defineBarra   ( $botoesReducao, 'left', '' );
    $obFormulario->addSpan       ( $obSpnListaReducao );

    if ($inTipo != 2) {
        $obFormulario->addTitulo     ( "Parcelas" );
        $obFormulario->addComponente ( $obTxtLimiteValorInicial );
        $obFormulario->addComponente ( $obTxtLimiteValorFinal );
        $obFormulario->addComponente ( $obTxtQtdParcelas );
        $obFormulario->addComponente ( $obTxtValorMinimo );
        $obFormulario->defineBarra   ( $botoesParcela, 'left', '' );
        $obFormulario->addSpan       ( $obSpnListaParcela );
    }
}

$obFormulario->addTitulo     ( "Documentos" );
$obITextBoxSelectDocumento->geraFormulario ( $obFormulario );
$obFormulario->defineBarra   ( $botoesDocumento, 'left', '' );
$obFormulario->addSpan       ( $obSpnListaDocumento );

if ($_REQUEST['stAcao'] == "incluir") {
    $obFormulario->Ok ();
} else {
    $obFormulario->Cancelar ();
}

$obFormulario->setFormFocus( $obDtVigenciaInicio->getId() );

$obFormulario->show();
Sessao::remove('documentos');
Sessao::remove('parcelas');
Sessao::remove('reducao');
Sessao::remove('acrescimo');
Sessao::remove('credito');
Sessao::remove('reducaoinc');
Sessao::write('reducao_alteracao', -1);

if ($_REQUEST['stAcao'] == "alterar") {
    sistemaLegado::executaFrameOculto("ajaxJavaScript('".$pgOcul."&inCodModalidade=".$_REQUEST["inCodModalidade"]."&stTimeStamp=".$_REQUEST["stTimeStamp"]."&inTipo=".$inTipo."', 'preencheListas');");
}
