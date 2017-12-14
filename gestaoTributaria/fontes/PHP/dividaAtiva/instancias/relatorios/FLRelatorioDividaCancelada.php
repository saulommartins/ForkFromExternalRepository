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
    * Página de Formulario de Filtro para relatorio de Divida Ativa Cancelada

    * Data de Criação   : 29/10/2009

    * @author Fernando Piccini Cercato
    * @ignore

    *Casos de uso: uc-05.04.10

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php" );
include_once ( CAM_GT_CEM_COMPONENTES."IPopUpEmpresaIntervalo.class.php" );
include_once ( CAM_GT_CIM_COMPONENTES."IPopUpImovelIntervalo.class.php" );
include_once ( CAM_GT_DAT_COMPONENTES."IPopUpDividaIntervalo.class.php" );
include_once ( CAM_GT_ARR_COMPONENTES."MontaGrupoCredito.class.php" );

//Define o nome dos arquivos PHP
$stPrograma    = "RelatorioDividaCancelada";
$pgFilt        = "FL".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OCRelatorioDivida.php";
$pgJs          = "JSRelatorioDivida.js";

include_once( $pgJs );

$jsOnLoad .= "montaParametrosGET('montaCredito'); \n";

Sessao::remove('link');
Sessao::remove('stLink');
Sessao::remove( "arListaGrupoCredito" );

//DEFINICAO DOS COMPONENTES
$obRdbGrupo = new Radio;
$obRdbGrupo->setRotulo     ( "Filtrar por"                                      );
$obRdbGrupo->setName       ( "stRdbGrupo"                                       );
$obRdbGrupo->setLabel      ( "Grupo de Créditos"                                );
$obRdbGrupo->setValue      ( "grupo"                                            );
$obRdbGrupo->setNull       ( false                                              );
$obRdbGrupo->setChecked    ( false                                              );
$obRdbGrupo->obEvento->setOnClick ( "montaParametrosGET('montaGrupoCredito');"  );

$obRdbCredito = new Radio;
$obRdbCredito->setTitle    ( "Informe o filtro a ser utilizado."            );
$obRdbCredito->setRotulo   ( "Filtrar por"                                  );
$obRdbCredito->setName     ( "stRdbGrupo"                                   );
$obRdbCredito->setLabel    ( "Crédito"                                      );
$obRdbCredito->setValue    ( "credito"                                      );
$obRdbCredito->setNull     ( true                                           );
$obRdbCredito->setChecked  ( true                                           );
$obRdbCredito->obEvento->setOnClick ( "montaParametrosGET('montaCredito');" );

$obSpnGrupoCredito = new Span;
$obSpnGrupoCredito->setID( "spnGrupoCredito");

$obSpnListaGrupos = new Span;
$obSpnListaGrupos->setID( "spnListaGrupos"  );

$obIPopUpEmpresa = new IPopUpEmpresaIntervalo;
$obIPopUpImovel	 = new IPopUpImovelIntervalo;
$obIPopUpDividaIntervalo = new IPopUpDividaIntervalo;

$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $_REQUEST['stAcao']  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $_REQUEST['stCtrl']  );

$obDtInicio  = new Data;
$obDtInicio->setName        ( "dtInicio"                    );
$obDtInicio->setValue       ( $dtInicio                     );
$obDtInicio->setRotulo      ( "Período"   );
$obDtInicio->setTitle       ( "Intervalo inicial"           );
$obDtInicio->setMaxLength   ( 20                            );
$obDtInicio->setSize        ( 10                            );
$obDtInicio->setNull        ( true                          );

$obLabelIntervalo = new Label;
$obLabelIntervalo->setValue ( "até" );

$obDtFinal  = new Data;
$obDtFinal->setName         ( "dtFinal"                     );
$obDtFinal->setValue        ( $dtFinal                      );
$obDtFinal->setRotulo       ( "Data Final do Lote"          );
$obDtFinal->setTitle        ( "Intervalo fina"              );
$obDtFinal->setMaxLength    ( 20                            );
$obDtFinal->setSize         ( 10                            );
$obDtFinal->setNull         ( true                          );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->setAjuda      ( "UC-05.04.10" );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addTitulo     ("Dados para Filtro");

$obIPopUpDividaIntervalo->setVerifica( true );
$obIPopUpDividaIntervalo->obInnerDividaIntervalo->setNull( true );
$obIPopUpDividaIntervalo->geraFormulario ( $obFormulario );

$obPopUpCGM = new IPopUpCGM( $obForm );
$obPopUpCGM->setNull ( true );
$obPopUpCGM->setRotulo ( "CGM" );
$obPopUpCGM->setTitle ( "Informe o número do CGM." );

$obFormulario->addComponente( $obPopUpCGM );
$obIPopUpImovel->geraFormulario ( $obFormulario );
$obIPopUpImovel->obInnerImovelIntervalo->obCampoCod->obEvento->setOnChange ( "" );
$obIPopUpImovel->obInnerImovelIntervalo->obCampoCod2->obEvento->setOnChange ( "" );
$obIPopUpEmpresa->geraFormulario ( $obFormulario );
$obIPopUpEmpresa->obInnerEmpresaIntervalo->obCampoCod->obEvento->setOnChange( "" );
$obIPopUpEmpresa->obInnerEmpresaIntervalo->obCampoCod2->obEvento->setOnChange( "" );

$obFormulario->agrupaComponentes ( array ( $obDtInicio, $obLabelIntervalo, $obDtFinal ) );

$obFormulario->agrupaComponentes     ( array( $obRdbCredito, $obRdbGrupo) );
$obFormulario->addSpan( $obSpnGrupoCredito );

$obFormulario->addSpan( $obSpnListaGrupos );

$obBtnOK = new Ok(true);

$onBtnLimpar = new Limpar;
$onBtnLimpar->obEvento->setOnClick  ( "Limpar();" );

$obFormulario->defineBarra( array( $obBtnOK , $onBtnLimpar ) );
$obFormulario->show();

$stJs .= 'f.inCodInscricao.focus();';
sistemaLegado::executaFrameOculto ( $stJs );

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';