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
    * Página de filtro para o cadastro de edificação vinculada a edificação
    * Data de Criação   : 11/08/2005

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Marcelo B. Paulino

    * @ignore

    * $Id: FLManterEdificacaoVinculo.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.11
*/

/*
$Log$
Revision 1.10  2006/09/18 10:30:30  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMEdificacao.class.php"       );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMTipoEdificacao.class.php"   );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php"     );
include_once ( CAM_GT_CIM_COMPONENTES."MontaLocalizacao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma    = "ManterEdificacao";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma."Vinculo.php";
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
//$obRCIMEdificacao = new RCIMEdificacao;
$obRCIMTipoEdificacao = new RCIMTipoEdificacao;
$rsTipoEdificacao     = new RecordSet;
$obRCIMConfiguracao   = new RCIMConfiguracao;
$obRCIMConfiguracao->setCodigoModulo( 12 );
$obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCIMConfiguracao->consultarConfiguracao();
$stMascaraLote = $obRCIMConfiguracao->getMascaraLote();
$stMascaraIM   = $obRCIMConfiguracao->getMascaraIM();

// Preenche RecordSet
$obRCIMTipoEdificacao->listarTiposEdificacao( $rsTipoEdificacao );

// OBJETOS HIDDEN
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao );

$obHdnVinculoEdificacao = new Hidden;
$obHdnVinculoEdificacao->setName  ( "boVinculoEdificacao" );
$obHdnVinculoEdificacao->setValue ( $_REQUEST['boVinculoEdificacao'] );

$obHdnCampoNumDom = new Hidden;
$obHdnCampoNumDom->setName( "stNumeroDomicilio" );
$obHdnCampoNumDom->setID  ( "stNumeroDomicilio" );

// DEFINE OBJETOS DO FORMULARIO
$obTxtCodigoConstrucao = new TextBox;
$obTxtCodigoConstrucao->setRotulo      ( "Código"                            );
$obTxtCodigoConstrucao->setName        ( "inCodigoConstrucao"                );
$obTxtCodigoConstrucao->setId          ( "inCodigoConstrucao"                );
$obTxtCodigoConstrucao->setValue       ( $_REQUEST["inCodigoConstrucao"]     );
$obTxtCodigoConstrucao->setSize        ( 8                                   );
$obTxtCodigoConstrucao->setMaxLength   ( 8                                   );
$obTxtCodigoConstrucao->setNull        ( true                                );

$obTxtTipoEdificacao = new TextBox;
$obTxtTipoEdificacao->setRotulo        ( "Tipo de Edificação"                );
$obTxtTipoEdificacao->setName          ( "inCodigoTipoEdificacao"            );
$obTxtTipoEdificacao->setValue         ( $_REQUEST["inCodigoTipoEdificacao"] );
$obTxtTipoEdificacao->setSize          ( 8                                   );
$obTxtTipoEdificacao->setMaxLength     ( 8                                   );
$obTxtTipoEdificacao->setNull          ( true                                );
$obTxtTipoEdificacao->setInteiro       ( true                                );

$obCmbTipoEdificacao = new Select;
$obCmbTipoEdificacao->setName          ( "cmbTipoEdificacao"                 );
$obCmbTipoEdificacao->addOption        ( "", "Selecione"                     );
$obCmbTipoEdificacao->setCampoId       ( "cod_tipo"                          );
$obCmbTipoEdificacao->setCampoDesc     ( "nom_tipo"                          );
$obCmbTipoEdificacao->preencheCombo    ( $rsTipoEdificacao                   );
$obCmbTipoEdificacao->setValue         ( $_REQUEST["inCodigoTipoEdificacao"] );
$obCmbTipoEdificacao->setNull          ( true                                );
$obCmbTipoEdificacao->setStyle         ( "width: 220px"                      );

$obBscCondominio = new BuscaInner;
$obBscCondominio->setRotulo              ( "Condomínio"                              );
$obBscCondominio->setTitle               ( "Condomínio com o qual a construção está vinculada" );
$obBscCondominio->setNull                ( true                                      );
$obBscCondominio->setId                  ( "campoInnerCond"                          );
$obBscCondominio->obCampoCod->setName    ( "stImovelCond"                            );
$obBscCondominio->obCampoCod->setValue   ( $_REQUEST["inCodigoCondominio"]           );
$obBscCondominio->obCampoCod->obEvento->setOnChange("buscaValor('buscaCondominio');" );
$obBscCondominio->setFuncaoBusca("abrePopUp('../popups/condominio/FLProcurarCondominio.php','frm','stImovelCond', 'campoInnerCond','','".Sessao::getId()."','800','550')" );

$obBscInscricaoMunicipal = new BuscaInner;
$obBscInscricaoMunicipal->setNull             ( true );
$obBscInscricaoMunicipal->setRotulo           ( "Inscrição Imobiliária" );
$obBscInscricaoMunicipal->setTitle            ( "Inscrição imobiliária com a qual a edificação está vinculada" );
$obBscInscricaoMunicipal->obCampoCod->setName ( "inInscricaoMunicipal" );
$obBscInscricaoMunicipal->obCampoCod->setId   ( "inInscricaoMunicipal" );
$obBscInscricaoMunicipal->obCampoCod->setValue( $_REQUEST["inInscImovel"] );
$obBscInscricaoMunicipal->obCampoCod->setSize ( strlen($stMascaraIM) );
$obBscInscricaoMunicipal->obCampoCod->setMaxLength( strlen($stMascaraIM) );
$obBscInscricaoMunicipal->setFuncaoBusca      ( "abrePopUp('".CAM_GT_CIM_POPUPS."imovel/FLProcurarImovel.php','frm','inInscricaoMunicipal','stNumeroDomicilio','todos','".Sessao::getId()."','800','550');");

$obBtnOK = new OK;
//$obBtnOK->obEvento->setOnClick    ( "submeteFiltro();" );

$obBtnLimpar = new Button;
$obBtnLimpar->setName             ( "btnLimpar"        );
$obBtnLimpar->setValue            ( "Limpar"           );
$obBtnLimpar->obEvento->setOnClick( "limparFiltro();"  );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction  ( $pgList    );
$obForm->setTarget  ( "telaPrincipal"   );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm   ( $obForm                 );
$obFormulario->setAjuda ( "UC-05.01.11" );
$obFormulario->addHidden ( $obHdnCtrl              );
$obFormulario->addHidden ( $obHdnAcao              );
$obFormulario->addHidden ( $obHdnVinculoEdificacao );
$obFormulario->addHidden ( $obHdnCampoNumDom       );

$obFormulario->addTitulo ( "Dados para filtro" );
$obFormulario->addComponente         ( $obTxtCodigoConstrucao                           );
$obFormulario->addComponenteComposto ( $obTxtTipoEdificacao , $obCmbTipoEdificacao      );
if ($_REQUEST['boVinculoEdificacao'] == "Imóvel") {
    $obFormulario->addComponente         ( $obBscInscricaoMunicipal                     );
} else {
    $obFormulario->addComponente         ( $obBscCondominio                             );
}
$obFormulario->defineBarra           ( array( $obBtnOK , $obBtnLimpar )                 );
$obFormulario->setFormFocus          ( $obTxtCodigoConstrucao->getId()                  );
$obFormulario->show();
?>
