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
    * Página de Filtro para Baixa de Débitos
    * Data de Criação   : 30/01/2006

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Lucas Teixeira Stephanou

    * @ignore

    * $Id: FLManterBaixaManual.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.03.19
    * Casos de uso: uc-05.03.19
*/

/*
$Log$
Revision 1.17  2007/07/16 21:10:27  cercato
Bug #9668#

Revision 1.16  2007/03/12 19:30:21  cercato
adicionada opcao para baixa da carne da divida.

Revision 1.15  2006/12/18 15:11:21  dibueno
Alteração nos tamanhos das textbox para numeracao - > 17 caracteres

Revision 1.14  2006/09/15 11:50:21  fabio
corrigidas tags de caso de uso

Revision 1.13  2006/09/15 10:55:12  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRParametroCalculo.class.php" );
include_once '../../../../../../gestaoTributaria/fontes/PHP/arrecadacao/classes/componentes/MontaGrupoCredito.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterBaixaManual";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";
include( $pgJS );
$obMontaGrupoCredito = new MontaGrupoCredito;
$obRARRParametroCalculo = new RARRParametroCalculo;
$obRARRParametroCalculo->obRARRGrupo->obRMONCredito->consultarMascaraCredito();
$stMascaraCredito = $obRARRParametroCalculo->obRARRGrupo->obRMONCredito->getMascaraCredito();

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "baixar";
}

Sessao::write( 'sessao_tranf4', array( 'filtro' => array(), 'pg' => '' , 'pos' => '', 'paginando' => false ) );
Sessao::write( 'link', array() );

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgList );
$obForm->setTarget( "telaPrincipal" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( '' );

$obTxtNumeracao = new TextBox;
$obTxtNumeracao->setId               ( "stNumeracao"             );
$obTxtNumeracao->setName             ( "stNumeracao"             );
$obTxtNumeracao->setSize             ( 19                        );
$obTxtNumeracao->setMaxLength        ( 17                        );
$obTxtNumeracao->setRotulo           ( "Número do Carnê"         );
$obTxtNumeracao->setTitle            ( "Numeração do carnê."      );
$obTxtNumeracao->setValue            ( $request->get('stNumeracao','')  );
$obTxtNumeracao->setInteiro          ( true );

$obTxtNumeracaoMigrada = new TextBox;
$obTxtNumeracaoMigrada->setId               ( "stNumeracaoMigrada"            );
$obTxtNumeracaoMigrada->setName             ( "stNumeracaoMigrada"            );
$obTxtNumeracaoMigrada->setSize             ( 19                              );
$obTxtNumeracaoMigrada->setMaxLength        ( 17                              );
$obTxtNumeracaoMigrada->setRotulo           ( "Número do Carnê Migrado"       );
$obTxtNumeracaoMigrada->setTitle            ( "Numeração do carnê migrado."    );
$obTxtNumeracaoMigrada->setValue            ( $request->get('stNumeracaoMigrada','') );
$obTxtNumeracaoMigrada->setInteiro          ( true );

$obCmbCreditosRef = new Select;
$obCmbCreditosRef->setName               ( "stCreditosRef"                                  );
$obCmbCreditosRef->setRotulo             ( "Créditos Referentes à"                          );
$obCmbCreditosRef->setTitle              ( "Filtrar créditos criados a partir de."          );
$obCmbCreditosRef->addOption             ( ""    , "Selecione"                              );
$obCmbCreditosRef->addOption             ( "cgm" , "CGM"                                    );
$obCmbCreditosRef->addOption             ( "ii"  , "Inscrição Imobiliária"                  );
$obCmbCreditosRef->addOption             ( "ie"  , "Inscrição Econômica"                    );
$obCmbCreditosRef->addOption             ( "da"  , "Divida Ativa"                           );
$obCmbCreditosRef->setStyle              ( "width: 200px"                                   );
$obCmbCreditosRef->setNull               ( false                                            );
$obCmbCreditosRef->obEvento->setOnChange ( "carregaReferencia(this.value)"                  );

$obBscCredito = new BuscaInner;
$obBscCredito->setNull   ( true            );
$obBscCredito->setRotulo( "Crédito" );
$obBscCredito->setTitle( "Busca crédito." );
$obBscCredito->setId( "stCredito" );
$obBscCredito->obCampoCod->setName("inCodCredito");
$obBscCredito->obCampoCod->setValue( '' );
$obBscCredito->obCampoCod->setMaxLength ( strlen($stMascaraCredito) );
$obBscCredito->obCampoCod->setMinLength ( strlen($stMascaraCredito) );
$obBscCredito->obCampoCod->setMascara   ($stMascaraCredito          );
$obBscCredito->obCampoCod->obEvento->setOnChange("buscaValor('buscaCredito');");
$obBscCredito->setFuncaoBusca( "abrePopUp('".CAM_GT_MON_POPUPS."credito/FLProcurarCredito.php','frm','inCodCredito','stCredito','todos','".Sessao::getId()."','800','550');" );

$obHdnModuloGrupo = new Hidden;
$obHdnModuloGrupo->setName ( "inCodModuloGrupo" );
$obHdnModuloGrupo->setValue( '' );

$obBscGrupoCredito = new BuscaInner;
$obBscGrupoCredito->setRotulo    ( "Grupo de Créditos"          );
$obBscGrupoCredito->setTitle     ( "Busca grupo de créditos."    );
$obBscGrupoCredito->setId        ( "stGrupo"        );
$obBscGrupoCredito->obCampoCod->setName      ("inCodGrupo"      );
$obBscGrupoCredito->obCampoCod->setValue     ( '' );
$obBscGrupoCredito->obCampoCod->obEvento->setOnChange("buscaValor('buscaGrupo');");
$obBscGrupoCredito->setFuncaoBusca( "abrePopUp('".CAM_GT_ARR_POPUPS."grupoCreditos/FLProcurarGrupo.php','frm','inCodGrupo','stGrupo','todos','".Sessao::getId()."','800','350');" );

$obSpnReferencia = new Span;
$obSpnReferencia->setId ( "spnReferencia" );

$obBtnOk = new Ok;
$obBtnOk->setName ( "btnOk" );
$obBtnOk->setValue( "Ok" );
$obBtnOk->obEvento->setOnClick("SalvarFormulario();");

$obBtnLimpar = new Limpar;
$obBtnLimpar->setName("btnLimpar");
$obBtnLimpar->obEvento->setOnClick("Limpar();");

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addHidden( $obHdnAcao            );
$obFormulario->addHidden( $obHdnCtrl            );
$obFormulario->addHidden($obHdnModuloGrupo );
$obFormulario->addTitulo( "Dados para Filtro"   );
$obFormulario->addComponente ( $obTxtNumeracao           );

//$obFormulario->addComponente (  $obBscGrupoCredito  );
$obMontaGrupoCredito->geraFormulario( $obFormulario, true, true );

$obFormulario->addComponente ( $obBscCredito );
$obFormulario->addComponente( $obTxtNumeracaoMigrada    );
$obFormulario->addComponente( $obCmbCreditosRef         );
$obFormulario->addSpan      ( $obSpnReferencia          );

$obFormulario->defineBarra( array($obBtnOk, $obBtnLimpar), "left");

//$obFormulario->ok();
$obFormulario->show();

?>
