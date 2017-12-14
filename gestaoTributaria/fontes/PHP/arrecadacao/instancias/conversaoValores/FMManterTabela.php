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
    * Página de Formulario para inclusao de Tabela de Conversão
    * Data de Criacao: 11/09/2007

    * @author Analista      : Fábio Bertoldi Rodrigues
    * @author Desenvolvedor : Vitor Hugo
    * @ignore

    * $Id: FMManterTabela.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.03.23
*/

/*
$Log$
Revision 1.1  2007/09/13 13:37:33  vitor
uc-05.03.23

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRGrupo.class.php"    );
include_once( CAM_GT_ARR_MAPEAMENTO."TARRTabelaConversaoValores.class.php"                                  );

//Define o nome dos arquivos PHP
$stPrograma = "ManterTabela";
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

Sessao::write( 'convval5', array() );
if ($stAcao == "alterar") {

    $stCondicao  = " cod_tabela = ".$_REQUEST['inCodTabela']." AND ";
    $stCondicao .= " exercicio  = '".$_REQUEST['stExercicio']."' AND ";

    $stCondicao = ($stCondicao) ? " WHERE " . substr($stCondicao, 0, strlen($stCondicao)-4) : "";

    $rsListaTabelaConversaoValores = new RecordSet();

    $obTabelaConversaoValores = new TARRTabelaConversaoValores();
    $obTabelaConversaoValores->recuperaListaTabelaConversaoValores( $rsTMP, $stCondicao  );

    Sessao::write( 'convval5', $rsTMP->getElementos() );
    Sessao::write( 'convval4', $rsTMP->getElementos() );

    $montaListaValores = "<script>montaParametrosGET( 'montaListaConversaoValores','',false);</script>";
} else {
    $limparListaValores = "<script>montaParametrosGET( 'limparListaValores','',false);</script>";
}

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc     );
$obForm->setTarget( "oculto"    );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCodTabela = new Hidden;
$obHdnCodTabela->setName( "cod_tabela" );
$obHdnCodTabela->setValue( $_REQUEST['inCodTabela'] );

$obTxtDescricao = new TextBox ;
$obTxtDescricao->setName        ( "stDescricao"     );
$obTxtDescricao->setId          ( "stDescricao"     );
$obTxtDescricao->setTitle       ( "Descrição do grupo de crédito." );
$obTxtDescricao->setMaxLength   ( 80                );
$obTxtDescricao->setSize        ( 80                );
$obTxtDescricao->setRotulo      ( "Descrição"       );
$obTxtDescricao->setNull        ( false             );
$obTxtDescricao->setValue       ( $_REQUEST['stDescricao'] );

$obTxtExercicio = new Exercicio ;
$obTxtExercicio->setName        ( "stExercicio"     );
$obTxtExercicio->setId          ( "stExercicio"     );
$obTxtExercicio->setTitle       ( "Exercício referente ao grupo de crédito." );
$obTxtExercicio->setNull        ( false             );
if ($_REQUEST['stExercicio']) {
$obTxtExercicio->setValue       ( $_REQUEST['stExercicio'] );
} else {
$obTxtExercicio->setValue       ( Sessao::getExercicio() );
}

// busca modulo
$obRegra = new RARRGrupo;
$obRegra->listarModulos($rsModulos);

$obCmbModulo = new Select;
$obCmbModulo->setName         ( "cmbModulos"                 );
$obCmbModulo->addOption       ( "", "Selecione"              );
$obCmbModulo->setRotulo       ( "Módulo"                     );
$obCmbModulo->setTitle        ( "Módulo"                     );
$obCmbModulo->setCampoId      ( "cod_modulo"                 );
$obCmbModulo->setCampoDesc    ( "nom_modulo"                 );
$obCmbModulo->preencheCombo   ( $rsModulos                   );
$obCmbModulo->setValue        ( $_REQUEST["inCodModulo"]     );
$obCmbModulo->setNull         ( false                        );

//Dados para Tabela
$obTxtParametro1 = new TextBox;
$obTxtParametro1->setName   ( "stParametro1" );
$obTxtParametro1->setTitle  ( "Defina o Parâmetro 1 da Tabela." );
$obTxtParametro1->setMaxLength   ( 80                );
$obTxtParametro1->setSize        ( 80                );
$obTxtParametro1->setRotulo ( "Parâmetro 1");
$obTxtParametro1->setValue  ( $_REQUEST["stParametro1"]     );

$obTxtParametro2 = new TextBox;
$obTxtParametro2->setName   ( "stParametro2" );
$obTxtParametro2->setTitle  ( "Defina o Parâmetro 2 da Tabela.") ;
$obTxtParametro2->setMaxLength   ( 80                );
$obTxtParametro2->setSize        ( 80                );
$obTxtParametro2->setRotulo ( "Parâmetro 2" );
$obTxtParametro2->setValue ( $_REQUEST["stParametro2"]     );

$obTxtParametro3 = new TextBox;
$obTxtParametro3->setName   ( "stParametro3" );
$obTxtParametro3->setTitle  ( "Defina o Parâmetro 3 da Tabela." );
$obTxtParametro3->setMaxLength   ( 80                );
$obTxtParametro3->setSize        ( 80                );
$obTxtParametro3->setRotulo ( "Parâmetro 3" );
$obTxtParametro3->setValue ( $_REQUEST["stParametro3"]     );

$obTxtParametro4 = new TextBox;
$obTxtParametro4->setName   ( "stParametro4" );
$obTxtParametro4->setTitle  ( "Defina o Parâmetro 4 da Tabela." );
$obTxtParametro4->setMaxLength   ( 80                );
$obTxtParametro4->setSize        ( 80                );
$obTxtParametro4->setRotulo ("Parâmetro 4");
$obTxtParametro4->setValue ( $_REQUEST["stParametro4"]     );

$obTxtCondParametro1 = new TextBox;
$obTxtCondParametro1->setName  ( "parametro_1" );
$obTxtCondParametro1->setTitle ( "Defina a primeira condição a ser atendida." );
$obTxtCondParametro1->setMaxLength ( 80 );
$obTxtCondParametro1->setSize  ( 80 );
$obTxtCondParametro1->setRotulo( "Condiçao Parâmetro 1" );
$obTxtCondParametro1->obEvento->setOnKeyPress( "return validar(event)" );

$obTxtCondParametro2 = new TextBox;
$obTxtCondParametro2->setName  ( "parametro_2" );
$obTxtCondParametro2->setTitle ( "Defina a segunda condição a ser atendida." );
$obTxtCondParametro2->setMaxLength ( 80 );
$obTxtCondParametro2->setSize  ( 80 );
$obTxtCondParametro2->setRotulo( "Condição Parâmetro 2" );
$obTxtCondParametro2->obEvento->setOnKeyPress( "return validar(event)" );

$obTxtCondParametro3 = new TextBox;
$obTxtCondParametro3->setName  ( "parametro_3");
$obTxtCondParametro3->setTitle ( "Defina a terceira condição a ser atendida. ");
$obTxtCondParametro3->setMaxLength ( 80 );
$obTxtCondParametro3->setSize  ( 80 );
$obTxtCondParametro3->setRotulo( "Condição Parâmetro 3 ");
$obTxtCondParametro3->obEvento->setOnKeyPress( "return validar(event)" );

$obTxtCondParametro4 = new TextBox;
$obTxtCondParametro4->setName  ( "parametro_4" );
$obTxtCondParametro4->setTitle ( "Defina a quarta condição a ser atendida." );
$obTxtCondParametro4->setMaxLength ( 80 );
$obTxtCondParametro4->setSize  ( 80 );
$obTxtCondParametro4->setRotulo( "Condição Parâmetro 4" );
$obTxtCondParametro4->obEvento->setOnKeyPress( "return validar(event)" );

$obTxtValor = new TextBox();
$obTxtValor->setRotulo   ( "*Valor"                     );
$obTxtValor->setTitle    ( "Defina o valor a ser utilizado." );
$obTxtValor->setName     ( "valor"                    );
$obTxtValor->setId       ( "valor"                    );
$obTxtValor->setDecimais ( 2                            );
$obTxtValor->setSize     ( 17                           );
$obTxtValor->setMaxLength( 17                           );
$obTxtValor->obEvento->setOnKeyPress( "return validar(event)" );

$obBtnIncluirCondicao = new Button;
$obBtnIncluirCondicao->setName( "stIncluirCondicao" );
$obBtnIncluirCondicao->setValue( "Incluir" );
$obBtnIncluirCondicao->obEvento->setOnClick( "montaParametrosGET( 'incluirConversaoValores','');" );

$obBtnLimparCondicao= new Button;
$obBtnLimparCondicao->setName( "stLimparCcondicao" );
$obBtnLimparCondicao->setValue( "Limpar" );
$obBtnLimparCondicao->obEvento->setOnClick( "montaParametrosGET( 'limparCondicao','');" );

$obSpnListaValores  = new Span();
$obSpnListaValores->setId( 'spnListaValores' );

$obFormulario = new Formulario;
$obFormulario->addForm          ( $obForm                );
$obFormulario->addTitulo        ( "Dados para Tabela"    );
$obFormulario->addHidden        ( $obHdnAcao             );
$obFormulario->addHidden        ( $obHdnCodTabela        );
$obFormulario->addComponente    ( $obTxtDescricao        );
$obFormulario->addComponente    ( $obTxtExercicio        );
$obFormulario->addComponente    ( $obCmbModulo           );
$obFormulario->addComponente    ( $obTxtParametro1       );
$obFormulario->addComponente    ( $obTxtParametro2       );
$obFormulario->addComponente    ( $obTxtParametro3       );
$obFormulario->addComponente    ( $obTxtParametro4       );

$obFormulario->addTitulo        ( "Dados para Valores"   );
$obFormulario->addComponente    ( $obTxtCondParametro1   );
$obFormulario->addComponente    ( $obTxtCondParametro2   );
$obFormulario->addComponente    ( $obTxtCondParametro3   );
$obFormulario->addComponente    ( $obTxtCondParametro4   );
$obFormulario->addComponente    ( $obTxtValor            );

$obFormulario->defineBarra  ( array( $obBtnIncluirCondicao, $obBtnLimparCondicao ),"","" );
$obFormulario->addSpan          ( $obSpnListaValores     );
$obFormulario->Cancelar($pgList);

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
echo $limparListaValores;
echo $montaListaValores;
?>
