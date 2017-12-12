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
    * Página de filtro para o cadastro de condomínio
    * Data de Criação   : 18/03/2005

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Marcelo B. Paulino

    * @ignore

    * $Id: FLManterCondominio.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.14
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMCondominio.class.php"       );

//Define o nome dos arquivos PHP
$stPrograma    = "ManterCondominio";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgFormVinculo = "FM".$stPrograma."Vinculo.php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";
include_once( $pgJs );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

Sessao::remove('link');

// DEFINE OBJETOS DAS CLASSES
$obRCIMCondominio = new RCIMCondominio;
$rsTipoCondominio = new RecordSet;

// OBJETOS HIDDEN
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $request->get('stCtrl') );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao );

// DEFINE OBJETOS DO FORMULARIO
$obTxtCodigoCondominio = new TextBox;
$obTxtCodigoCondominio->setRotulo      ( "Código"                            );
$obTxtCodigoCondominio->setName        ( "inCodigoCondominio"                );
$obTxtCodigoCondominio->setId          ( "inCodigoCondominio"                );
$obTxtCodigoCondominio->setValue       ( $request->get("inCodigoCondominio") );
$obTxtCodigoCondominio->setSize        ( 8                                   );
$obTxtCodigoCondominio->setMaxLength   ( 8                                   );
$obTxtCodigoCondominio->setNull        ( true                                );

$obTxtNomCondominio = new TextBox;
$obTxtNomCondominio->setRotulo       ( "Nome"               );
$obTxtNomCondominio->setName         ( "stNomCondominio"    );
$obTxtNomCondominio->setSize         ( 80 );
$obTxtNomCondominio->setMaxLength    ( 80 );
$obTxtNomCondominio->setNull         ( true );

$obTxtTipoCondominio = new TextBox;
$obTxtTipoCondominio->setRotulo      ( "Tipo"                    );
$obTxtTipoCondominio->setName        ( "inCodigoTipo"            );
$obTxtTipoCondominio->setTitle       ( "Tipo de condomínio"      );
$obTxtTipoCondominio->setValue       ( $request->get("inCodigoTipo") );
$obTxtTipoCondominio->setSize        ( 8                         );
$obTxtTipoCondominio->setMaxLength   ( 8                         );
$obTxtTipoCondominio->setNull        ( true                      );
$obTxtTipoCondominio->setInteiro     ( true                      );

$obRCIMCondominio->listarTiposCondominio( $rsTipoCondominio );
$obCmbTipoCondominio = new Select;
$obCmbTipoCondominio->setName        ( "cmbTipoCondominio"       );
$obCmbTipoCondominio->addOption      ( "", "Selecione"           );
$obCmbTipoCondominio->setCampoId     ( "cod_tipo"                );
$obCmbTipoCondominio->setCampoDesc   ( "nom_tipo"                );
$obCmbTipoCondominio->preencheCombo  ( $rsTipoCondominio         );
$obCmbTipoCondominio->setValue       ( $request->get("inCodigoTipo") );
$obCmbTipoCondominio->setNull        ( true                      );
$obCmbTipoCondominio->setStyle       ( "width: 220px"            );

$obBscCGM = new BuscaInner;
$obBscCGM->setRotulo              ( "CGM"                                  );
$obBscCGM->setTitle               ( "CGM de pessoa jurídica do condomínio" );
$obBscCGM->setNull                ( true                                   );
$obBscCGM->setId                  ( "campoInner"                           );
$obBscCGM->obCampoCod->setName    ( "inNumCGM"                             );
$obBscCGM->obCampoCod->setValue   ( $request->get("inNumCGM")              );
$obBscCGM->obCampoCod->obEvento->setOnChange("buscaCGM('tipoCGM');" );
$obBscCGM->setFuncaoBusca("abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumCGM'
                           ,'campoInner','juridica','".Sessao::getId()."','800','550')" );
//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction  ( $pgList    );
$obForm->setTarget  ( "telaPrincipal"   );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm   ( $obForm             );
$obFormulario->setAjuda  ( "UC-05.01.14" );
$obFormulario->addHidden ( $obHdnCtrl          );
$obFormulario->addHidden ( $obHdnAcao          );

$obFormulario->addTitulo ( "Dados para filtro" );

$obFormulario->addComponente         ( $obTxtCodigoCondominio );
$obFormulario->addComponente         ( $obTxtNomCondominio    );
$obFormulario->addComponenteComposto ( $obTxtTipoCondominio, $obCmbTipoCondominio );
$obFormulario->addComponente         ( $obBscCGM              );

$obFormulario->Ok();
$obFormulario->setFormFocus( $obTxtCodigoCondominio->getId() );
$obFormulario->show  ();
?>
