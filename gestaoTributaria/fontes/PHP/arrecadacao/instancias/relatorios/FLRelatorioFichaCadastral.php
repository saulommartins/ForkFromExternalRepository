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
    * Página de Filtro para Relatório da Arrecadação Periódico
    * Data de Criação   : 22/05/2007

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Diego Bueno Coelho

    * @ignore

    * $Id: FLRelatorioFichaCadastral.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.03.13
*/

/*
$Log$
Revision 1.1  2007/05/23 19:34:52  dibueno
Bug #9279#

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once ( CAM_GT_ARR_NEGOCIO."RARRGrupo.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMConfiguracao.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php" );
include_once (CAM_GT_ARR_NEGOCIO."RARRParametroCalculo.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioFichaCadastral";
$pgFilt     = "FL".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

include_once( $pgJS );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "consultar";
}

// pegar mascara de credito
$obRARRParametroCalculo = new RARRParametroCalculo;
$obRARRParametroCalculo->obRARRGrupo->obRMONCredito->consultarMascaraCredito();
$stMascaraCredito = $obRARRParametroCalculo->obRARRGrupo->obRMONCredito->getMascaraCredito();

//mascara grupo de credito
$obRARRGrupo = new RARRGrupo;
$stMascaraGrupoCredito = "";
$obRARRGrupo->RecuperaMascaraGrupoCredito( $stMascaraGrupoCredito );
$stMascaraGrupoCredito .= "/9999";

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction($pgOcul );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue ( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
if (isset($stCtrl)) {
    $obHdnCtrl->setValue( $stCtrl );
}

$obHdnNomeGrupo = new Hidden;
$obHdnNomeGrupo->setName    ( "stNomeGrupo" );
$obHdnNomeGrupo->setId      ("stNomeGrupo"	);

$obBtnOK = new OK;
$obBtnOK->obEvento->setOnClick( "submeteFiltro()" );

$onBtnLimpar = new Limpar;

$obSpnFiltro = new Span;
$obSpnFiltro->setId ( 'spnFiltro' );

$obDtInicio  = new Data;
$obDtInicio->setName        ( "dtInicio"                    );
if (isset($dtInicio)) {
    $obDtInicio->setValue   ( $dtInicio                 	);
}
$obDtInicio->setRotulo      ( "Periodo"   );
$obDtInicio->setTitle       ( "Intervalo inicial"           );
$obDtInicio->setMaxLength   ( 20                            );
$obDtInicio->setSize        ( 10                            );

$obLabelIntervalo = new Label;
$obLabelIntervalo->setValue ( "até" );

$obDtFinal  = new Data;
$obDtFinal->setName         ( "dtFinal"                     );
if (isset($dtFinal)) {
    $obDtFinal->setValue    ( $dtFinal                  	);
}
$obDtFinal->setRotulo       ( "Data Final do Lote"          );
$obDtFinal->setTitle        ( "Intervalo fina"              );
$obDtFinal->setMaxLength    ( 20                            );
$obDtFinal->setSize         ( 10                            );

$obBscGrupoCredito = new BuscaInnerIntervalo;
$obBscGrupoCredito->setRotulo           ( "Grupo de Crédito"    					);
$obBscGrupoCredito->setTitle            ( "Informe o intervalo de Grupo de Crédito"	);
$obBscGrupoCredito->obLabelIntervalo->setValue 	( "até"          					);
$obBscGrupoCredito->obCampoCod->setName     	("inCodGrupoInicio"  				);
$obBscGrupoCredito->obCampoCod->setMascara 		( $stMascaraGrupoCredito 			);
$obBscGrupoCredito->obCampoCod->setMaxLength 	( strlen($stMascaraGrupoCredito) 	);
$obBscGrupoCredito->obCampoCod->setMinLength 	( strlen($stMascaraGrupoCredito) 	);
$obBscGrupoCredito->obCampoCod->setNull (true );
$obBscGrupoCredito->setFuncaoBusca( str_replace("'","&quot;","abrePopUp('".CAM_GT_ARR_POPUPS."grupoCreditos/FLProcurarGrupo.php','frm','inCodGrupoInicio','stNomeGrupo','','".Sessao::getId()."','800','450');" ));
$obBscGrupoCredito->obCampoCod2->setName		("inCodGrupoTermino"  				);
$obBscGrupoCredito->obCampoCod2->setMascara 	( $stMascaraGrupoCredito 			);
$obBscGrupoCredito->obCampoCod2->setMaxLength 	( strlen($stMascaraGrupoCredito)	);
$obBscGrupoCredito->obCampoCod2->setMinLength 	( strlen($stMascaraGrupoCredito)	);
$obBscGrupoCredito->setFuncaoBusca2( str_replace("'","&quot;","abrePopUp('".CAM_GT_ARR_POPUPS."grupoCreditos/FLProcurarGrupo.php','frm','inCodGrupoTermino','stNomeGrupo','','".Sessao::getId()."','800','450');" ));
$obBscGrupoCredito->setNull( true );

$obBscCredito = new BuscaInnerIntervalo;
$obBscCredito->setRotulo           ( "Crédito"						 );
$obBscCredito->setTitle            ( "Informe o intervalo de Crédito");
$obBscCredito->obLabelIntervalo->setValue ( "até"          			 );
$obBscCredito->obCampoCod->setName     	("inCodCreditoInicio"  		 );
$obBscCredito->obCampoCod->setNull ( true );
$obBscCredito->setFuncaoBusca( str_replace("'","&quot;","abrePopUp('".CAM_GT_MON_POPUPS."credito/FLProcurarCredito.php','frm','inCodCreditoInicio','stNomeGrupo','','".Sessao::getId()."','800','450');" ));
$obBscCredito->obCampoCod2->setName      ("inCodCreditoTermino"  	 );
$obBscCredito->setFuncaoBusca2( str_replace("'","&quot;","abrePopUp('".CAM_GT_MON_POPUPS."credito/FLProcurarCredito.php','frm','inCodCreditoTermino','stNomeGrupo','','".Sessao::getId()."','800','450');" ));
$obBscCredito->obCampoCod->setMaxLength  ( strlen($stMascaraCredito) );
$obBscCredito->obCampoCod->setMinLength  ( strlen($stMascaraCredito) );
$obBscCredito->obCampoCod->setMascara    ( $stMascaraCredito         );
$obBscCredito->obCampoCod2->setMaxLength ( strlen($stMascaraCredito) );
$obBscCredito->obCampoCod2->setMinLength ( strlen($stMascaraCredito) );
$obBscCredito->obCampoCod2->setMascara   ( $stMascaraCredito         );

$obBscContribuinte = new BuscaInnerIntervalo;
$obBscContribuinte->setRotulo           		( "Contribuinte"    			);
$obBscContribuinte->obLabelIntervalo->setValue	( "até"          				);
$obBscContribuinte->obCampoCod->setName     	("inCodContribuinteInicial"  	);
if (isset($inCodContribuinteInicio)) {
    $obBscContribuinte->obCampoCod->setValue	( $inCodContribuinteInicio		);
}
$obBscContribuinte->obCampoCod->setNull ( true );
$obBscContribuinte->setFuncaoBusca( str_replace("'","&quot;","abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCodContribuinteInicial','stNomeGrupo','','".Sessao::getId()."','800','450');" ));
$obBscContribuinte->obCampoCod2->setName		("inCodContribuinteFinal"  		);
if (isset($inCodContribuinteFinal)) {
    $obBscContribuinte->obCampoCod2->setValue	( $inCodContribuinteFinal  		);
}
$obBscContribuinte->setFuncaoBusca2( str_replace("'","&quot;","abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCodContribuinteFinal','stNomeGrupo','','".Sessao::getId()."','800','450');" ));

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm );

$obFormulario->addHidden    ( $obHdnNomeGrupo 		);
$obFormulario->addHidden    ( $obHdnAcao            );
$obFormulario->addHidden    ( $obHdnCtrl            );
$obFormulario->addTitulo	( "Dados para filtro"   );

$obFormulario->addComponente( $obBscContribuinte	);
$obFormulario->addComponente( $obBscGrupoCredito	);
$obFormulario->addComponente( $obBscCredito 		);
$obFormulario->agrupaComponentes ( array ( $obDtInicio, $obLabelIntervalo, $obDtFinal ) );

$obFormulario->defineBarra( array( $obBtnOK , $onBtnLimpar ) );

$obFormulario->show();

?>
