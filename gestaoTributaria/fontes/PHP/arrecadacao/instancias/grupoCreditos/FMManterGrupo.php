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
    * Página de Formulario para inclusao DE GRUPO DE CREDITO
    * Data de Criação   : 23/05/2005

    * @author Analista      : Fabio Bertoldi Rodrigues
    * @author Desenvolvedor : Lucas Teixeira Stephanou

    * @ignore

    * $Id: FMManterGrupo.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.03.02
*/

/*
$Log$
Revision 1.16  2006/10/30 13:23:40  dibueno
Adição da coluna ORDEM

Revision 1.15  2006/10/19 18:41:53  cercato
correcao para recuperar atributos conforme ano.

Revision 1.14  2006/09/15 11:10:42  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRGrupo.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRParametroCalculo.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterGrupo";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";
include_once( $pgJs );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}
//Sessao::write( "link", "" );

// limpa variaveis de sessao de credito e acrescimo
Sessao::write( "creditos", array() );
Sessao::write( "inNumCreditos", 0 );
Sessao::write( "acrescimos", array() );

// pegar mascara de credito
$obRARRParametroCalculo = new RARRParametroCalculo;
$obRARRParametroCalculo->obRARRGrupo->obRMONCredito->consultarMascaraCredito();
$stMascaraCredito = $obRARRParametroCalculo->obRARRGrupo->obRMONCredito->getMascaraCredito();

$stMascaraCalculo = "99.9.999";

$obBscDesoneracao = new BuscaInner;
$obBscDesoneracao->setNull             ( false );
$obBscDesoneracao->setTitle            ( "Regra que será utilizada para conceder desoneração aos lançamentos." );
$obBscDesoneracao->setRotulo           ( "Regra p/ Desoneração" );
$obBscDesoneracao->setId               ( "stFormula"  );
$obBscDesoneracao->setNull ( true );
$obBscDesoneracao->setValue            ( $_REQUEST["stDesDesc"] );
$obBscDesoneracao->obCampoCod->setName ( "inCodigoFormula" );

if ($_REQUEST["inCodDes"]) {
    $stCodDes = sprintf( "25.2.%03d", $_REQUEST["inCodDes"] );
}

$obBscDesoneracao->obCampoCod->setValue( $stCodDes  );
$obBscDesoneracao->obCampoCod->setInteiro ( true );
$obBscDesoneracao->obCampoCod->setNull ( true );
$obBscDesoneracao->obCampoCod->setSize (  9   );
$obBscDesoneracao->obCampoCod->obEvento->setOnChange( "buscaValor('buscaFuncao');" );
$obBscDesoneracao->setFuncaoBusca      (  "abrePopUp('".CAM_GA_ADM_POPUPS."funcao/FLBuscarFuncao.php?".Sessao::getId()."&stCodModulo=25&stCodBiblioteca=2&','frm','inCodigoFormula','stFormula','','".Sessao::getId()."','800','550');" );
$obBscDesoneracao->obCampoCod->obEvento->setOnKeyUp("mascaraDinamico('".$stMascaraCalculo."', this, event);");
$obBscDesoneracao->obCampoCod->setMinLength ( strlen($stMascaraCalculo) );

/* Atributos Dinamicos */
$obRegra = new RARRGrupo;
if ($stAcao == "incluir") {
    $obRegra->obRCadastroDinamico->recuperaAtributosSelecionados( $rsDisponiveis );
    $rsSelecionados = new RecordSet;
} else {
    $obRegra->obRCadastroDinamico->setPersistenteAtributos ( new TARRAtributoGrupo);
    $obRegra->obRCadastroDinamico->setChavePersistenteValores ( array ( "cod_grupo" => $_REQUEST["inCodGrupo"], "ano_exercicio" => $_REQUEST["stExercicio"]));
    $obRegra->obRCadastroDinamico->recuperaAtributosDisponiveis( $rsDisponiveis);
    $obRegra->obRCadastroDinamico->recuperaAtributosSelecionados( $rsSelecionados );
}

$obCmbAtributos = new SelectMultiplo();
$obCmbAtributos->setName  ( 'inCodAtributoSelecionados' );
$obCmbAtributos->setRotulo( "Atributos" );
$obCmbAtributos->setNull  ( true );
$obCmbAtributos->setTitle ( 'Atributos que serão solicitados ao conceder a desoneração.' );

$obCmbAtributos->SetNomeLista1( 'inCodAtributoDisponiveis' );
$obCmbAtributos->setCampoId1  ( 'cod_atributo'      );
$obCmbAtributos->setCampoDesc1( 'nom_atributo'      );
$obCmbAtributos->SetRecord1   ( $rsDisponiveis      );

$obCmbAtributos->SetNomeLista2( 'inCodAtributoSelecionados' );
$obCmbAtributos->setCampoId2  ( 'cod_atributo'      );
$obCmbAtributos->setCampoDesc2( 'nom_atributo'      );
$obCmbAtributos->SetRecord2   ( $rsSelecionados   );

//DEFINICAO DOS COMPONENTES
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnNumCreditos = new Hidden;
$obHdnNumCreditos->setName( "inNumCreditos" );
$obHdnNumCreditos->setValue( 0 );

// spans
$obSpnCreditos = new Span;
$obSpnCreditos->setId  ( "spnCreditos" );

$obSpnAcrescimos = new Span;
$obSpnAcrescimos->setId  ( "spnAcrescimos" );

$obHdnCodigo = new Hidden;
$obHdnCodigo->setName( "inCodGrupo" );
$obHdnCodigo->setValue( $_REQUEST["inCodGrupo"] );

$obHdnExercicio = new Hidden;
$obHdnExercicio->setName( "stExercicio" );
$obHdnExercicio->setValue( $_REQUEST["stExercicio"] );

$obLblCodigo = new Label;
$obLblCodigo->setRotulo ( "Código" );
$obLblCodigo->setTitle  ( "Código do grupo." );
$obLblCodigo->setName   ( "stCodigo"    );
$obLblCodigo->setId     ( "stCodigo"    );
$obLblCodigo->setValue  ( $_REQUEST["inCodGrupo"]     );

$obLblExercicio = new Label;
$obLblExercicio->setRotulo ( "Exercício"    );
$obLblExercicio->setTitle  ( "Exercício referente ao grupo de crédito."    );
$obLblExercicio->setName   ( "stExercicio"  );
$obLblExercicio->setId     ( "stExercicio"  );
$obLblExercicio->setValue  ( $_REQUEST["stExercicio"]   );

$obTxtDescricao = new TextBox ;
$obTxtDescricao->setName        ( "stDescricao"     );
$obTxtDescricao->setId          ( "stDescricao"     );
$obTxtDescricao->setTitle       ( "Descrição do grupo de crédito." );
$obTxtDescricao->setMaxLength   ( 80                );
$obTxtDescricao->setSize        ( 80                );
$obTxtDescricao->setRotulo      ( "Descrição"       );
$obTxtDescricao->setNull        ( false             );
$obTxtDescricao->setValue       ( $_REQUEST["stDescricao"] );

$obTxtExercicio = new Exercicio ;
$obTxtExercicio->setName        ( "stExercicio"     );
$obTxtExercicio->setId          ( "stExercicio"     );
$obTxtExercicio->setTitle       ( "Exercício referente ao grupo de crédito." );
$obTxtExercicio->setNull        ( false             );
$obTxtExercicio->setValue       ( $_REQUEST["stExercicio"] );

// busca modulo
$obRegra->listarModulos($rsModulos);
$obCmbModulo = new Select;
$obCmbModulo->setName         ( "cmbModulos"                 );
$obCmbModulo->addOption       ( "", "Selecione"              );
$obCmbModulo->setRotulo       ( "Módulo"                     );
$obCmbModulo->setTitle        ( "Módulo"                     );
$obCmbModulo->setCampoId      ( "cod_modulo"                 );
$obCmbModulo->setCampoDesc    ( "nom_modulo"                 );
$obCmbModulo->preencheCombo   ( $rsModulos                   );
$obCmbModulo->setValue        ( $_REQUEST["inCodigoModulo"]  );
$obCmbModulo->setNull         ( false                        );
$obCmbModulo->setStyle        ( "width: 220px"               );

// campos para creditos e acrescimos
$obBscCredito = new BuscaInner;
$obBscCredito->setRotulo( "*Crédito" );
$obBscCredito->setTitle( "Busca crédito." );
$obBscCredito->setId( "stCredito" );
$obBscCredito->obCampoCod->setName("inCodCredito");
$obBscCredito->obCampoCod->setValue( $_REQUEST["inCodCredito"] );
$obBscCredito->obCampoCod->setMaxLength ( strlen($stMascaraCredito) );
$obBscCredito->obCampoCod->setMinLength ( strlen($stMascaraCredito) );
$obBscCredito->obCampoCod->setMascara   ($stMascaraCredito          );
$obBscCredito->obCampoCod->obEvento->setOnChange("buscaValor('buscaCredito');");
$obBscCredito->setFuncaoBusca( "abrePopUp('".CAM_GT_MON_POPUPS."credito/FLProcurarCredito.php','frm','inCodCredito','stCredito','todos','".Sessao::getId()."','800','550');" );

$obTxtOrdem = new TextBox;
//$obTxtOrdem->setNull (false);
$obTxtOrdem->setInteiro (true);
$obTxtOrdem->setName  	("inOrdem");
$obTxtOrdem->setTitle 	("Informe a ordem de cálculo para o crédito.");
$obTxtOrdem->setRotulo	("*Ordem");

$obRdbDescontoSim = new Radio;
$obRdbDescontoSim->setRotulo     ( "Aplicar Descontos" );
$obRdbDescontoSim->setName       ( "boDesconto"        );
$obRdbDescontoSim->setLabel      ( "Sim"               );
$obRdbDescontoSim->setValue      ( "Sim"               );
$obRdbDescontoSim->setChecked    ( true                );
$obRdbDescontoSim->setNull       ( true                );

$obRdbDescontoNao = new Radio;
$obRdbDescontoNao->setRotulo     ( "Aplicar Descontos" );
$obRdbDescontoNao->setName       ( "boDesconto"        );
$obRdbDescontoNao->setLabel      ( "Não"               );
$obRdbDescontoNao->setValue      ( "Não"               );
$obRdbDescontoNao->setChecked    ( false               );
$obRdbDescontoNao->setNull       ( true                );

$obBtnIncluirCredito = new Button;
$obBtnIncluirCredito->setName( "stIncluirCredito" );
$obBtnIncluirCredito->setValue( "Incluir" );
$obBtnIncluirCredito->obEvento->setOnClick( "incluirCredito();" );

$obBtnLimparCredito= new Button;
$obBtnLimparCredito->setName( "stLimparCredito" );
$obBtnLimparCredito->setValue( "Limpar" );
$obBtnLimparCredito->obEvento->setOnClick( "limparCredito();" );

$obBscAcrescimo = new BuscaInner;
$obBscAcrescimo->setRotulo( "*Acréscimo" );
$obBscAcrescimo->setTitle( "Busca acréscimo." );
$obBscAcrescimo->setId( "stAcrescimo" );
$obBscAcrescimo->obCampoCod->setName("inCodAcrescimo");
$obBscAcrescimo->obCampoCod->setValue( $_REQUEST["inCodAcrescimo"] );
$obBscAcrescimo->obCampoCod->obEvento->setOnChange("buscaValor('buscaAcrescimo');");
$obBscAcrescimo->setFuncaoBusca( " abrePopUp('".CAM_GT_MON_POPUPS."acrescimo/FLProcurarAcrescimo.php','frm','inCodAcrescimo','stAcrescimo','todos','".Sessao::getId()."','800','550');" );

$obBtnIncluirAcrescimo = new Button;
$obBtnIncluirAcrescimo->setName( "stIncluirAcrescimo" );
$obBtnIncluirAcrescimo->setValue( "Incluir" );
$obBtnIncluirAcrescimo->obEvento->setOnClick( "incluirAcrescimo();" );

$obBtnLimparAcrescimo = new Button;
$obBtnLimparAcrescimo->setName( "stLimparAcrescimo" );
$obBtnLimparAcrescimo->setValue( "Limpar" );
$obBtnLimparAcrescimo->obEvento->setOnClick( "limparAcrescimo();" );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc     );
$obForm->setTarget( "oculto"    );

$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm                       );
$obFormulario->addHidden            ( $obHdnCtrl                    );
$obFormulario->addHidden            ( $obHdnAcao                    );
$obFormulario->addHidden            ( $obHdnCodigo                  );
$obFormulario->addHidden            ( $obHdnNumCreditos             );
$obFormulario->addTitulo            ( "Dados para Grupo de Créditos");
if ($stAcao == "alterar") {
    $obFormulario->addComponente    ( $obLblCodigo          );
    $obFormulario->addComponente    ( $obLblExercicio       );
    $obFormulario->addHidden        ( $obHdnExercicio       );
    $obFormulario->addComponente    ( $obTxtDescricao       );
} else {
    $obFormulario->addComponente    ( $obTxtDescricao       );
    $obFormulario->addComponente    ( $obTxtExercicio       );
}

$obFormulario->addComponente        ( $obBscDesoneracao     );
$obFormulario->addComponente        ( $obCmbModulo          );
$obFormulario->addTitulo            ( "Créditos"            );
$obFormulario->addComponente        ( $obBscCredito         );
$obFormulario->addComponente		( $obTxtOrdem			);
$obFormulario->agrupaComponentes    ( array( $obRdbDescontoSim,$obRdbDescontoNao ) );
$obFormulario->defineBarra  ( array( $obBtnIncluirCredito, $obBtnLimparCredito ),"","" );
$obFormulario->addSpan              ( $obSpnCreditos        );
/*
$obFormulario->addTitulo            ( "Acréscimos"          );
$obFormulario->addComponente        ( $obBscAcrescimo       );
$obFormulario->defineBarra  ( array( $obBtnIncluirAcrescimo, $obBtnLimparAcrescimo ),"","" );
$obFormulario->addSpan              ( $obSpnAcrescimos      );
*/

$obFormulario->addComponente        ( $obCmbAtributos       );
if ( $stAcao == 'incluir')
    $obFormulario->Ok();
else
    $obFormulario->Cancelar();
$obFormulario->setFormFocus( $obTxtDescricao->getId() );
$obFormulario->show();
if ($stAcao == "alterar") {
    SistemaLegado::BloqueiaFrames();
    $stJs .= "setTimeout(\"buscaValor('montaCreditos')\",2000);";
//    $stJs .= "setTimeout(\"buscaValor('montaAcrescimos')\",4000);";
    //SistemaLegado::executaFramePrincipal($stJs);
    SistemaLegado::executaFrameOculto($stJs);
}
SistemaLegado::LiberaFrames();
?>
