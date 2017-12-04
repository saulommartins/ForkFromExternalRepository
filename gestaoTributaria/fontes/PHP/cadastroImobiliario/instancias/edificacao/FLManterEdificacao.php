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
 * Página de filtro para o cadastro de edificação
 * Data de Criação   : 17/11/2004

 * @author Analista: Ricardo Lopes de Alencar
 * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
 * @author Desenvolvedor: Fábio Bertoldi Rodrigues

 * @ignore

 * $Id: FLManterEdificacao.php 59612 2014-09-02 12:00:51Z gelson $

 * Casos de uso: uc-05.01.11
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GT_CIM_NEGOCIO."RCIMEdificacao.class.php";
include_once CAM_GT_CIM_NEGOCIO."RCIMTipoEdificacao.class.php";
include_once CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php";
include_once CAM_GT_CIM_COMPONENTES."MontaLocalizacao.class.php";

//Define o nome dos arquivos PHP
$stPrograma    = "ManterEdificacao";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgFormVinculo = "FM".$stPrograma."Vinculo.php";
$pgFiltVinculo = "FL".$stPrograma."Vinculo.php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

include_once $pgJs;

$stAcao = $request->get('stAcao');
if (empty($stAcao)) {
    $stAcao = "alterar";
}

Sessao::remove('link');

$obRCIMTipoEdificacao = new RCIMTipoEdificacao;
$rsTipoEdificacao     = new RecordSet;

$obRCIMConfiguracao = new RCIMConfiguracao;
$obRCIMConfiguracao->setCodigoModulo( 12 );
$obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCIMConfiguracao->consultarConfiguracao();
$stMascaraLote = $obRCIMConfiguracao->getMascaraLote();

$obMontaLocalizacao = new MontaLocalizacao;
$obMontaLocalizacao->setCadastroLocalizacao( false );
$obMontaLocalizacao->setPopUp              ( true  );
$obMontaLocalizacao->setObrigatorio        ( false );

// Preenche RecordSet
$obRCIMTipoEdificacao->listarTiposEdificacao( $rsTipoEdificacao );

// OBJETOS HIDDEN
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao );

// DEFINE OBJETOS DO FORMULARIO
$obTxtCodigoConstrucao = new TextBox;
$obTxtCodigoConstrucao->setRotulo    ( "Código"             );
$obTxtCodigoConstrucao->setName      ( "inCodigoConstrucao" );
$obTxtCodigoConstrucao->setId        ( "inCodigoConstrucao" );
$obTxtCodigoConstrucao->setValue     ( $request->get("inCodigoConstrucao") );
$obTxtCodigoConstrucao->setSize      ( 8 );
$obTxtCodigoConstrucao->setMaxLength ( 8 );
$obTxtCodigoConstrucao->setNull      ( true );

$obTxtTipoEdificacao = new TextBox;
$obTxtTipoEdificacao->setRotulo    ( "Tipo de Edificação"      );
$obTxtTipoEdificacao->setName      ( "inCodigoTipoEdificacao"  );
$obTxtTipoEdificacao->setValue     ( $request->get("inCodigoTipoEdificacao") );
$obTxtTipoEdificacao->setSize      ( 8    );
$obTxtTipoEdificacao->setMaxLength ( 8    );
$obTxtTipoEdificacao->setNull      ( true );
$obTxtTipoEdificacao->setInteiro   ( true );

$obCmbTipoEdificacao = new Select;
$obCmbTipoEdificacao->setName       ( "cmbTipoEdificacao" );
$obCmbTipoEdificacao->addOption     ( "", "Selecione"     );
$obCmbTipoEdificacao->setCampoId    ( "cod_tipo"          );
$obCmbTipoEdificacao->setCampoDesc  ( "nom_tipo"          );
$obCmbTipoEdificacao->preencheCombo ( $rsTipoEdificacao   );
$obCmbTipoEdificacao->setValue      ( $request->get("inCodigoTipoEdificacao") );
$obCmbTipoEdificacao->setNull       ( true                );
$obCmbTipoEdificacao->setStyle      ( "width: 220px"      );

$obRadioVinculoImovel = new Radio;
$obRadioVinculoImovel->setName    ( "boVinculoEdificacao"   );
$obRadioVinculoImovel->setTitle   ( "Vínculo da Edificação" );
$obRadioVinculoImovel->setRotulo  ( "Vínculo" );
$obRadioVinculoImovel->setValue   ( "Imóvel"  );
$obRadioVinculoImovel->setLabel   ( "Imóvel"  );
$obRadioVinculoImovel->setNull    ( false     );
$obRadioVinculoImovel->setChecked ( true );
$obRadioVinculoImovel->obEvento->setOnChange ( "habilitaSpnImovelCond();" );

$obRadioVinculoCondominio = new Radio;
$obRadioVinculoCondominio->setName     ( "boVinculoEdificacao" );
$obRadioVinculoCondominio->setValue    ( "Condomínio"          );
$obRadioVinculoCondominio->setLabel    ( "Condomínio"          );
$obRadioVinculoCondominio->setNull     ( false                 );
$obRadioVinculoCondominio->obEvento->setOnChange ( "habilitaSpnImovelCond();");

$obTxtNumeroLote = new TextBox;
$obTxtNumeroLote->setName      ( "stNumeroLote"   );
$obTxtNumeroLote->setMaxLength ( strlen( $stMascaraLote ) );
$obTxtNumeroLote->setSize      ( strlen( $stMascaraLote ) );
$obTxtNumeroLote->setRotulo    ( "Número do Lote" );
$obTxtNumeroLote->obEvento->setOnKeyUp("mascaraDinamico('".$stMascaraLote."', this, event);");

$obSpnImovelCond = new Span;
$obSpnImovelCond->setID("spnImovelCond");

$obBtnOK = new OK;
$obBtnOK->obEvento->setOnClick ( "submeteFiltro();" );

$obBtnLimpar = new Button;
$obBtnLimpar->setName              ( "btnLimpar"       );
$obBtnLimpar->setValue             ( "Limpar"          );
$obBtnLimpar->obEvento->setOnClick ( "limparFiltro();" );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction  ( $pgList    );
$obForm->setTarget  ( "telaPrincipal"   );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm   ( $obForm );
$obFormulario->setAjuda ( "UC-05.01.11" );
$obFormulario->addHidden ( $obHdnCtrl );
$obFormulario->addHidden ( $obHdnAcao );

$obFormulario->addTitulo ( "Dados para filtro" );

$obFormulario->addComponente         ( $obTxtCodigoConstrucao           );
$obFormulario->addComponenteComposto ( $obTxtTipoEdificacao , $obCmbTipoEdificacao      );
$obFormulario->addComponenteComposto ( $obRadioVinculoImovel, $obRadioVinculoCondominio );
$obFormulario->addSpan               ( $obSpnImovelCond                 );
$obFormulario->addComponente         ( $obTxtNumeroLote                 );
$obMontaLocalizacao->geraFormulario  ( $obFormulario                    );
$obFormulario->defineBarra           ( array( $obBtnOK , $obBtnLimpar ) );

$obFormulario->show ();

SistemaLegado::executaFrameOculto("habilitaSpnImovelCond();");

?>
