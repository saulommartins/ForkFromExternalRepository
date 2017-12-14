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
  * Página de Filtro de Emissão de Documentos
  * Data de criação : 23/05/2007

  * @author Analista: Fábio Bertoldi
  * @author Programador: Fernando Piccini Cercato

  * $Id: FLEmitirDocumento.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.03.11

  **/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php";
include_once CAM_GT_CIM_COMPONENTES."IPopUpImovel.class.php";
include_once CAM_GT_CEM_COMPONENTES."IPopUpEmpresa.class.php";
include_once CAM_GT_MON_NEGOCIO."RMONCredito.class.php";
include_once CAM_GT_ARR_MAPEAMENTO."TARRDocumento.class.php";
include_once '../../../../../../gestaoTributaria/fontes/PHP/arrecadacao/classes/componentes/MontaGrupoCredito.class.php';

//Definicao dos nomes de arquivos
$stPrograma = "EmitirDocumento";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if (empty($stAcao)) {
    $stAcao = "emitir";
}

Sessao::write( "link", "" );

$obTARRDocumento = new TARRDocumento;
$obTARRDocumento->recuperaTodos( $rsTipoDocumento );

$obCmbTipoDocumento = new Select;
$obCmbTipoDocumento->setRotulo             ( "Tipo de Documento" );
$obCmbTipoDocumento->setTitle              ( "Tipo de Documento" );
$obCmbTipoDocumento->setName               ( "cmbTipoDocumento" );
$obCmbTipoDocumento->addOption             ( "", "Selecione" );
$obCmbTipoDocumento->setCampoId            ( "[cod_documento]§[cod_modelo_documento]§[cod_tipo_documento]" );
$obCmbTipoDocumento->setCampoDesc          ( "descricao" );
$obCmbTipoDocumento->preencheCombo         ( $rsTipoDocumento );
$obCmbTipoDocumento->setNull               ( false );
$obCmbTipoDocumento->setStyle              ( "width: 220px" );
$obCmbTipoDocumento->obEvento->setOnChange ( "buscaValor('montaFiltro');" );

$obRMONCredito = new RMONCredito;
$obRMONCredito->consultarMascaraCredito();
$stMascaraCredito = $obRMONCredito->getMascaraCredito();
$obMontaGrupoCredito = new MontaGrupoCredito;

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( 'stCtrl' );
$obHdnCtrl->setValue( '' );

$obHdnExercicioGrupo  = new Hidden;
$obHdnExercicioGrupo->setName   ( "inExercicioGrupo" );
$obHdnExercicioGrupo->setValue  ( $request->get("inExercicioGrupo") );

$obTxtExercicio = new Exercicio;
$obTxtExercicio->setName ('inExercicio');
$obTxtExercicio->setValue(Sessao::getExercicio());
$obTxtExercicio->setNull( true );

$obHdnCampoNome  = new Hidden;
$obHdnCampoNome->setName   ( "stNome" );

$obSpnFiltro = new Span;
$obSpnFiltro->setId( "spnFiltro");

$obBscCredito = new BuscaInner;
$obBscCredito->setRotulo    ( "Crédito"        );
$obBscCredito->setTitle     ( "Busca Crédito"   );
$obBscCredito->setId        ( "stCredito"       );
$obBscCredito->obCampoCod->setStyle     ( "width: 80px"   );
$obBscCredito->obCampoCod->setName      ("inCodCredito"             );
$obBscCredito->obCampoCod->setValue     ( $request->get("inCodCredito") );
$obBscCredito->obCampoCod->setMaxLength ( strlen($stMascaraCredito) );
$obBscCredito->obCampoCod->setMinLength ( strlen($stMascaraCredito) );
$obBscCredito->obCampoCod->setMascara   ($stMascaraCredito          );
$obBscCredito->obCampoCod->obEvento->setOnChange("buscaValor('buscaCredito');");
$obBscCredito->setFuncaoBusca("abrePopUp('".CAM_GT_MON_POPUPS."credito/FLProcurarCredito.php','frm','inCodCredito','stCredito','todos','".Sessao::getId()."','800','550');" );

$obBscGrupoCredito = new BuscaInner;
$obBscGrupoCredito->setRotulo    ( "Grupo de Créditos"          );
$obBscGrupoCredito->setTitle     ( "Busca Grupo de Créditos"    );
$obBscGrupoCredito->setId        ( "stGrupo"        );
$obBscGrupoCredito->obCampoCod->setName      ("inCodGrupo"      );
$obBscGrupoCredito->obCampoCod->setValue     ( $request->get("inCodGrupo") );
$obBscGrupoCredito->obCampoCod->obEvento->setOnChange("buscaValor('buscaGrupo');");
$obBscGrupoCredito->setFuncaoBusca( "abrePopUp('".CAM_GT_ARR_POPUPS."grupoCreditos/FLProcurarGrupo.php','frm','inCodGrupo','stGrupo','todos','".Sessao::getId()."','800','350');" );

$obBscInscricaoImobiliaria = new IPopUpImovel;
$obBscInscricaoImobiliaria->obInnerImovel->setNull ( true );

$obBscInscricaoEconomica = new IPopUpEmpresa;
$obBscInscricaoEconomica->setNull ( true );

$obHdnEmissao =  new Hidden;
$obHdnEmissao->setName   ( "emissao_carnes" );
$obHdnEmissao->setValue  ( "local" );

//tipo de emissao
$obRdbEmissao = new Radio;
$obRdbEmissao->setRotulo   ( "Tipo de Emissão" );
$obRdbEmissao->setName     ( "emissao_carnes" );
$obRdbEmissao->setId       ( "emissao_carnes" );
$obRdbEmissao->setLabel    ( "Emissão" );
$obRdbEmissao->setValue    ( "emissao" );
$obRdbEmissao->setNull     ( false );
$obRdbEmissao->setChecked  ( true );

$obRdbREmissao = new Radio;
$obRdbREmissao->setRotulo   ( "Tipo de Emissão" );
$obRdbREmissao->setName     ( "emissao_carnes" );
$obRdbREmissao->setId       ( "emissao_carnes" );
$obRdbREmissao->setLabel    ( "Reemissão" );
$obRdbREmissao->setValue    ( "reemissao" );
$obRdbREmissao->setNull     ( false );
$obRdbREmissao->setChecked  ( false );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgList  );
$obForm->setTarget( "telaPrincipal" );

$obBscContribuinte = new IPopUpCGM( $obForm );
$obBscContribuinte->setNull ( true );
$obBscContribuinte->setRotulo ( "Contribuinte" );
$obBscContribuinte->setTitle ( "Informe o número do Contribuinte." );

$obBtnOK = new OK;
$obBtnOK->obEvento->setOnClick ( "submeteFiltro();" );

$onBtnLimpar = new Limpar;

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnEmissao );
$obFormulario->addHidden ( $obHdnExercicioGrupo );
$obFormulario->addHidden ( $obHdnCampoNome );
$obFormulario->addTitulo( "Dados para o Filtro"  );
$obFormulario->agrupaComponentes( array( $obRdbEmissao, $obRdbREmissao ) );
$obFormulario->addComponente( $obCmbTipoDocumento );
$obFormulario->addSpan ( $obSpnFiltro );

$obFormulario->defineBarra( array( $obBtnOK, $onBtnLimpar ) );
$obFormulario->setFormFocus($obTxtExercicio->getId());
$obFormulario->Show();
