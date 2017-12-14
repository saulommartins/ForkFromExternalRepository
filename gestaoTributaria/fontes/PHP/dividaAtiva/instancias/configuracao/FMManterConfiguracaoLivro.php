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
    * Página de Formulário da Configuração do modulo divida ativa
    * Data de Criação   : 04/05/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @ignore

    * $Id: FMManterConfiguracaoLivro.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.04.01
*/

/*
$Log$
Revision 1.6  2007/02/28 20:24:45  cercato
Bug #8514#

Revision 1.5  2006/09/15 14:36:02  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_DAT_NEGOCIO."RDATConfiguracao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracao";
$pgForm = "FM".$stPrograma."Livro.php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

$obRDATConfiguracao = new RDATConfiguracao;
$obErro = $obRDATConfiguracao->consultar();
if ( !$obErro->ocorreu() ) {
    $arFolhaLivro = explode(';', $obRDATConfiguracao->getLivroFolha());
    $inNumIniLivro = $arFolhaLivro[0];
    $inNumFolLivro = $arFolhaLivro[2];
    $stNumeroFolha = $arFolhaLivro[3];
    $stSeqLivro    = $arFolhaLivro[1];
}

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) || $stAcao == "alterar" ) {
    $stAcao = "livro";
}

//DEFINICAO DOS COMPONENTES
$obForm  = new Form;
$obForm->setAction ( $pgProc );
$obForm->settarget ( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue ( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );

//numero inicial para livro
$obTxtNumeroInicialLivro = new TextBox;
$obTxtNumeroInicialLivro->setRotulo ( "Número Inicial para Livro" );
$obTxtNumeroInicialLivro->setTitle ( "Número a ser utilizado no primeiro livro de dívida ativa utilizado no sistema" );
$obTxtNumeroInicialLivro->setName ( "inNumIniLivro" );
$obTxtNumeroInicialLivro->setValue ( $inNumIniLivro );
$obTxtNumeroInicialLivro->setNull ( false );
$obTxtNumeroInicialLivro->setInteiro ( true );

//Livro sequencial ou por exercicio
$obRdbLivroSequencial = new Radio;
$obRdbLivroSequencial->setRotulo   ( "Sequência do Livro" );
$obRdbLivroSequencial->setTitle    ( "Seqüencia a ser seguida na numeração do livro" );
$obRdbLivroSequencial->setName     ( "stSeqLivro" );
$obRdbLivroSequencial->setLabel    ( "Seqüencial" );
$obRdbLivroSequencial->setValue    ( "sequencial" );
$obRdbLivroSequencial->setChecked  ( $stSeqLivro == "sequencial" );
$obRdbLivroSequencial->setNull     ( false );

//numeracao de folha
$obRdbLivroExercicio = new Radio;
$obRdbLivroExercicio->setRotulo   ( "Sequênia do Livro" );
$obRdbLivroExercicio->setTitle    ( "Seqüencia a ser seguida na numeração do livro" );
$obRdbLivroExercicio->setName     ( "stSeqLivro" );
$obRdbLivroExercicio->setLabel    ( "Seqüencial por Exercício" );
$obRdbLivroExercicio->setValue    ( "exercicio" );
$obRdbLivroExercicio->setChecked  ( $stSeqLivro == "exercicio" );
$obRdbLivroExercicio->setNull     ( false );

//numero de folhas por livro
$obTxtNumeroFolhaLivro = new TextBox;
$obTxtNumeroFolhaLivro->setRotulo ( "Número de Folhas por Livro" );
$obTxtNumeroFolhaLivro->setTitle ( "Número de folhas que cada livro de dívida ativa terá" );
$obTxtNumeroFolhaLivro->setName ( "inNumFolLivro" );
$obTxtNumeroFolhaLivro->setValue ( $inNumFolLivro );
$obTxtNumeroFolhaLivro->setNull ( false );
$obTxtNumeroFolhaLivro->setInteiro ( true );

//numeracao de folha
$obRdbNumeroFolhaSequencial = new Radio;
$obRdbNumeroFolhaSequencial->setRotulo   ( "Numeração de Folha" );
$obRdbNumeroFolhaSequencial->setTitle    ( "Seqüencia a ser seguida na numeração das folhas" );
$obRdbNumeroFolhaSequencial->setName     ( "stNumFolSeq" );
$obRdbNumeroFolhaSequencial->setLabel    ( "Seqüencial" );
$obRdbNumeroFolhaSequencial->setValue    ( "sequencial" );
$obRdbNumeroFolhaSequencial->setChecked  ( $stNumeroFolha == "sequencial" );
$obRdbNumeroFolhaSequencial->setNull     ( false );

//numeracao de folha
$obRdbNumeroFolhaExercicio = new Radio;
$obRdbNumeroFolhaExercicio->setRotulo   ( "Numeração de Folha" );
$obRdbNumeroFolhaExercicio->setTitle    ( "Seqüencia a ser seguida na numeração das folhas" );
$obRdbNumeroFolhaExercicio->setName     ( "stNumFolSeq" );
$obRdbNumeroFolhaExercicio->setLabel    ( "Seqüencial por Livro" );
$obRdbNumeroFolhaExercicio->setValue    ( "exercicio" );
$obRdbNumeroFolhaExercicio->setChecked  ( $stNumeroFolha == "exercicio" );
$obRdbNumeroFolhaExercicio->setNull     ( false );

$obBtnLimpar = new Button;
$obBtnLimpar->setName               ( "btnLimpar" );
$obBtnLimpar->setValue              ( "Limpar" );
$obBtnLimpar->setTipo               ( "button" );
$obBtnLimpar->obEvento->setOnClick  ( "LimparLivro();" );
$obBtnLimpar->setDisabled           ( false );

$obBtnOK = new OK;

$arBotoes = array( $obBtnOK, $obBtnLimpar );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm ( $obForm );
$obFormulario->addHidden ( $obHdnCtrl );
$obFormulario->addHidden ( $obHdnAcao );

$obFormulario->addTitulo ( "Dados para Livro de Dívida Ativa" );
$obFormulario->addComponente ( $obTxtNumeroInicialLivro );
$obFormulario->addComponenteComposto ( $obRdbLivroSequencial, $obRdbLivroExercicio );
$obFormulario->addComponente ( $obTxtNumeroFolhaLivro );
$obFormulario->addComponenteComposto ( $obRdbNumeroFolhaSequencial, $obRdbNumeroFolhaExercicio );
$obFormulario->defineBarra( $arBotoes );
$obFormulario->show();

?>
