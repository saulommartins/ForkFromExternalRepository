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
  * Página de Formulário para emissão de carnê
  * Data de criação : 07/11/2005

  * @author Analista: Fábio Bertoldi
  * @author Programador: tonismar R. Bernardo

  * $Id: FMEmitirCarne.php 44959 2010-10-22 17:14:57Z diogo.zarpelon $

  Caso de uso: uc-05.03.11

  **/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GT_MON_NEGOCIO."RMONCredito.class.php";
include_once '../../../../../../gestaoTributaria/fontes/PHP/arrecadacao/classes/componentes/MontaGrupoCredito.class.php';
include_once CAM_GT_ARR_NEGOCIO."RARRCarne.class.php";

//Define o nome dos arquivos PHP
$stPrograma      = "EmitirCarneIPTUDesoneradoMata";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FM".$stPrograma.".php";
$pgFormVinculo   = "FM".$stPrograma."Vinculo.php";
$pgProc          = "PR".$stPrograma.".php";
$pgOcul          = "OC".$stPrograma.".php";
$pgJs            = "JS".$stPrograma.".js";

include_once( $pgJs );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( 'stCtrl' );
$obHdnCtrl->setValue( '' );

$obMontaGrupoCredito = new MontaGrupoCredito;
$obTxtExercicio = new Exercicio;

$obRMONCredito = new RMONCredito;
$obRMONCredito->consultarMascaraCredito();
$stMascaraCredito = $obRMONCredito->getMascaraCredito();

$obHdnExercicioGrupo = new Hidden;
$obHdnExercicioGrupo->setName   ( "inExercicio" );
$obHdnExercicioGrupo->setValue  ( $_REQUEST["inExercicio"]  );

$obCmbTipo = new Select;
$obCmbTipo->setName 		( "stPadraoCodBarra"         	);
$obCmbTipo->setRotulo    	( "Padrão do Código de Barra" 	);
$obCmbTipo->setTitle       	( "Selecione o padrão do Código de Barra" );
$obCmbTipo->addOption    ( ""          , "Selecione"   );
$obCmbTipo->addOption    ( "febraban" , "FEBRABAN"     );
$obCmbTipo->addOption    ( "febrabanCompBBAnexo5" , "FEBRABAN Compensacao BB Anexo 5"     );
$obCmbTipo->addOption    ( "compensacao" , "Ficha de Compensação"     );
$obCmbTipo->setCampoDesc ( "stTipo"                      );
$obCmbTipo->setNull      ( false                         );
$obCmbTipo->setStyle     ( "width: 200px"                );

$obBscCredito = new BuscaInner;
$obBscCredito->setRotulo    ( "Crédito"        );
$obBscCredito->setTitle     ( "Busca Crédito"   );
$obBscCredito->setId        ( "stCredito"       );
$obBscCredito->obCampoCod->setStyle     ( "width: 80px"   );
$obBscCredito->obCampoCod->setName      ("inCodCredito"             );
$obBscCredito->obCampoCod->setValue     ( $_REQUEST["inCodCredito"] );
$obBscCredito->obCampoCod->setMaxLength ( strlen($stMascaraCredito) );
$obBscCredito->obCampoCod->setMinLength ( strlen($stMascaraCredito) );
$obBscCredito->obCampoCod->setMascara   ($stMascaraCredito          );
$obBscCredito->obCampoCod->obEvento->setOnChange("buscaValor('buscaCredito');");
$obBscCredito->setFuncaoBusca("abrePopUp('".CAM_GT_MON_POPUPS."credito/FLProcurarCredito.php','frm','inCodCredito','stCredito','todos','".Sessao::getId()."','800','550');" );

$obRdbEmissaoLocal = new Radio;
$obRdbEmissaoLocal->setRotulo   ( "Emissão de Carnês" );
$obRdbEmissaoLocal->setName     ( "emissao_carnes" );
$obRdbEmissaoLocal->setId       ( "emissao_carnes" );
$obRdbEmissaoLocal->setLabel    ( "Impressão Local" );
$obRdbEmissaoLocal->setValue    ( "local" );
$obRdbEmissaoLocal->setNull     ( false );
$obRdbEmissaoLocal->setChecked  ( false );
$obRdbEmissaoLocal->obEvento->setOnChange ( "montaParametrosGET('montaOpcoesGrafica')" 	);

$obRdbEmissaoGrafica = new Radio;
$obRdbEmissaoGrafica->setId       ( "emissao_carnes" );
$obRdbEmissaoGrafica->setName     ( "emissao_carnes" );
$obRdbEmissaoGrafica->setLabel    ( "Gráfica" );
$obRdbEmissaoGrafica->setValue    ( "grafica" );
$obRdbEmissaoGrafica->obEvento->setOnChange	( "montaParametrosGET('montaOpcoesGrafica')" );

$obSpnTipoCarne = new Span;
$obSpnTipoCarne->setId    ( "spnTipoCarne" );
$obSpnTipoCarne->setValue ( "" );

$obSpnEmissao = new Span;
$obSpnEmissao->setId    ( "spnEmissao" );
$obSpnEmissao->setValue ( "" );

$obSpnAtributos = new Span;
$obSpnAtributos->setId    ( "spnAtributos" );
$obSpnAtributos->setValue ( "" );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction ( $pgProc );
$obForm->setTarget ( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm               );
$obFormulario->addHidden    ( $obHdnAcao            );
$obFormulario->addHidden    ( $obHdnCtrl            );
$obFormulario->addHidden    ( $obHdnExercicioGrupo  );
$obFormulario->addTitulo    ( "Dados para Emissão"  );
$obFormulario->addComponente( $obTxtExercicio       );

$obFormulario->addComponente( $obBscCredito         );
$obMontaGrupoCredito->geraFormulario( $obFormulario, true, true );
$obFormulario->addComponente ( $obCmbTipo );

$obFormulario->agrupaComponentes( array($obRdbEmissaoLocal,$obRdbEmissaoGrafica));
$obFormulario->addSpan ( $obSpnTipoCarne );
$obFormulario->addSpan ( $obSpnEmissao );
$obFormulario->addSpan ( $obSpnAtributos );

$obButtonOK = new OK;
$obButtonOK->setName  ( "BtnEmitir" );
$obButtonOK->setValue ( "OK" );
$obButtonOK->obEvento->setOnClick( "javascript: EnviaFormulario();");

$obBtnLimpar = new Limpar;
$obBtnLimpar->obEvento->setOnClick( "Limpar();" );

$obFormulario->defineBarra( array( $obButtonOK, $obBtnLimpar ), "left", "" );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
