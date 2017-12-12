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
    * Página de inclusão de reforma para o cadastro de edificação
    * Data de Criação   : 31/05/2005

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Lucas Stephanou

    * @ignore

    * $Id: FMManterEdificacaoReforma.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.11
*/

/*
$Log$
Revision 1.8  2006/09/18 10:30:30  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConstrucaoOutros.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMImovel.class.php"           );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLote.class.php"             );

//Define o nome dos arquivos PHP
$stPrograma = "ManterEdificacao";
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

$obRCIMConstrucao = new RCIMConstrucaoOutros;

$obRCIMImovel = new RCIMImovel( new RCIMLote );
$obRCIMImovel->obRCIMConfiguracao->consultarConfiguracao();
$obRCIMImovel->obRCIMConfiguracao->setCodigoModulo( 12 );
$obRCIMImovel->obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCIMImovel->obRCIMConfiguracao->consultarConfiguracao();
$stMascaraIM = $obRCIMImovel->obRCIMConfiguracao->getMascaraIM();
$obRCIMImovel->obRCIMConfiguracao->consultarMascaraProcesso( $stMascaraProcesso );

//DEFINICAO DOS COMPONENTES
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl"  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao"  );
$obHdnAcao->setValue( $stAcao   );

// HIDDENS PARA GUARDA VALORES
$obHdnVinculoEdificacao = new Hidden;
$obHdnVinculoEdificacao->setName         ( "hdnVinculoEdificacao"           );
$obHdnVinculoEdificacao->setValue        ( $_REQUEST["boVinculoEdificacao"] );

$obHdnAreaTotal = new Hidden;
$obHdnAreaTotal->setName                 ( "hdnAreaTotal"                    );
$obHdnAreaTotal->setValue                ( $_REQUEST['flAreaTotalEdificada'] );

$obHdnAreaTotalOriginal = new Hidden;
$obHdnAreaTotalOriginal->setName         ( "hdnAreaTotalOriginal"            );
$obHdnAreaTotalOriginal->setValue        ( $_REQUEST['flAreaTotalEdificada'] );

$obHdnUnidadeOriginal = new Hidden;
$obHdnUnidadeOriginal->setName           ( "hdnUnidadeOriginal"          );
$obHdnUnidadeOriginal->setValue          ( $_REQUEST['flAreaUnidade']    );

$obHdnCodigoTipo = new Hidden;
$obHdnCodigoTipo->setName                ( "hdnCodigoTipo"           );
$obHdnCodigoTipo->setValue               ( $_REQUEST["inCodigoTipo"] );

$obHdnCodigoTipo = new Hidden;
$obHdnCodigoTipo->setName                ( "hdnCodigoTipo"           );
$obHdnCodigoTipo->setValue               ( $_REQUEST["inCodigoTipo"] );

$obHdnCodigoConstrucaoAut = new Hidden;
$obHdnCodigoConstrucaoAut->setName       ( "hdnCodigoConstrucaoAut"           );
$obHdnCodigoConstrucaoAut->setValue      ( $_REQUEST["inCodigoConstrucaoAut"] );

$obHdnCodigoConstrucao = new Hidden;
$obHdnCodigoConstrucao->setName          ( "inCodigoConstrucao"            );
$obHdnCodigoConstrucao->setValue         ( $_REQUEST["inCodigoConstrucao"] );

$obHdnTipo = new Hidden;
$obHdnTipo->setName                      ( "stTipo"                      );
$obHdnTipo->setValue                     ( $_REQUEST["stTipoEdificacao"] );

$obHdnImovelCond= new Hidden;
$obHdnImovelCond->setName                ( "stImovelCond"            );
$obHdnImovelCond->setValue               ( $_REQUEST["stImovelCond"] );

$obHdnTipoUnidade= new Hidden;
$obHdnTipoUnidade->setName               ( "stTipoUnidade"            );
$obHdnTipoUnidade->setValue              ( $_REQUEST["stTipoUnidade"] );

// LABELS
$obLblCodigoConstrucao = new Label;
$obLblCodigoConstrucao->setName     ( "stCodigoConstrucao"  );
$obLblCodigoConstrucao->setRotulo   ( "Código"              );
$obLblCodigoConstrucao->setValue    ( $_REQUEST["inCodigoConstrucao"] );

$obLblTipoEdificao = new Label;
$obLblTipoEdificao->setName         ( "stCodigoConstrucao" );
$obLblTipoEdificao->setRotulo       ( "Tipo de Edificação" );
$obLblTipoEdificao->setValue        ( $_REQUEST["stTipoEdificacao"] );

$obLblNumeroInscricao = new Label;
$obLblNumeroInscricao->setName      ( "stImovelCond" );
$obLblNumeroInscricao->setRotulo    ( "Inscrição Imobiliária" );
$obLblNumeroInscricao->setTitle     ( "Inscrição imobiliária com a qual a construção está vinculada" );
$obLblNumeroInscricao->setValue     ( $_REQUEST["stImovelCond"] );

$obLblNomeCondominio = new Label;
$obLblNomeCondominio->setName       ( "stImovelCond" );
$obLblNomeCondominio->setRotulo     ( "Condomínio"   );
$obLblNomeCondominio->setTitle      ( "Nome do Condomínio a qual a construção esta vinculada" );
$obLblNomeCondominio->setValue      ( $_REQUEST["stImovelCond"] );

$obLblTipoUnidade = new Label;
$obLblTipoUnidade->setName          ( "stTipoUnidade"        );
$obLblTipoUnidade->setRotulo        ( "Tipo de Unidade"      );
$obLblTipoUnidade->setValue         ( $_REQUEST["stTipoUnidade"] );

$obBscProcesso = new BuscaInner;
$obBscProcesso->setRotulo           ( "Processo" );
$obBscProcesso->setTitle            ( "Número do processo no protocolo que gerou a aprovação do loteamento" );
$obBscProcesso->obCampoCod->setName ("inProcesso");
$obBscProcesso->obCampoCod->setId   ("inProcesso");
if ($_REQUEST['inCodigoProcesso']) {
    $obBscProcesso->obCampoCod->setValue( $_REQUEST['inCodigoProcesso'].'/'.$_REQUEST['hdnAnoExercicioProcesso'] );
}
$obBscProcesso->obCampoCod->obEvento->setOnChange( "buscaValor('buscaProcesso');" );
$obBscProcesso->obCampoCod->obEvento->setOnKeyUp( "mascaraDinamico('".$stMascaraProcesso."', this, event);" );
$obBscProcesso->setFuncaoBusca( "abrePopUp('".CAM_GA_PROT_POPUPS."processo/FLBuscaProcessos.php','frm','inProcesso','campoInner2','','".Sessao::getId()."','800','550')" );

$obTxtAreaTotalEdificacao = new Numerico;
$obTxtAreaTotalEdificacao->setRotulo     ( "Área Total Edificada"  );
$obTxtAreaTotalEdificacao->setName       ( "flAreaTotalEdificada"  );
$obTxtAreaTotalEdificacao->setMaxValue   ( 999999999999.99         );
$obTxtAreaTotalEdificacao->setSize       ( 18                      );
$obTxtAreaTotalEdificacao->setMaxLength  ( 18                      );
$obTxtAreaTotalEdificacao->setNull       ( false                   );
$obTxtAreaTotalEdificacao->setNegativo   ( false                   );
$obTxtAreaTotalEdificacao->setNaoZero    ( true                    );

$obLblAreaTotalEdificada = new Label;
$obLblAreaTotalEdificada->setRotulo( "Área Total Edificada"             );
$obLblAreaTotalEdificada->setName  ( "flAreaTotalEdificada"             );
$obLblAreaTotalEdificada->setID    ( "flAreaTotalEdificada"             );
$obLblAreaTotalEdificada->setValue ( $_REQUEST["flAreaConstruida"]  );

if ($_REQUEST["hdnVinculoEdificacao"] == "Condomínio") {
    $stRotulo = "Área da Edificação";
} else {
    $stRotulo = "Área da Unidade";
}

$obTxtAreaUnidade = new Numerico;
$obTxtAreaUnidade->setName      ( "flAreaUnidade" );
$obTxtAreaUnidade->setRotulo    ( $stRotulo );
$obTxtAreaUnidade->setMaxValue  ( 999999999999.99    );
$obTxtAreaUnidade->setSize      ( 18 );
$obTxtAreaUnidade->setMaxLength ( 18 );
$obTxtAreaUnidade->setNull      ( false );
$obTxtAreaUnidade->setNegativo  ( false );
$obTxtAreaUnidade->setNaoZero   ( true );
$obTxtAreaUnidade->setTitle     ( "Área construída pertencente a unidade" );
$obTxtAreaUnidade->setValue ($_REQUEST['flAreaUnidade']);
//$obTxtAreaUnidade->obEvento->setOnChange ( "buscaValor('calculaAreaTotal');" );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new FormularioAbas;
$obFormulario->addForm            ( $obForm                     );
$obFormulario->setAjuda ( "UC-05.01.11" );
$obFormulario->addHidden          ( $obHdnCtrl                  );
$obFormulario->addHidden          ( $obHdnAcao                  );
$obFormulario->addHidden          ( $obHdnTipo                  );
$obFormulario->addHidden          ( $obHdnAreaTotal             );
$obFormulario->addHidden          ( $obHdnAreaTotalOriginal     );
$obFormulario->addHidden          ( $obHdnUnidadeOriginal       );
$obFormulario->addHidden          ( $obHdnCodigoTipo            );
$obFormulario->addHidden          ( $obHdnCodigoConstrucaoAut   );
$obFormulario->addHidden          ( $obHdnVinculoEdificacao     );
$obFormulario->addHidden          ( $obHdnTipoUnidade           );
$obFormulario->addHidden          ( $obHdnImovelCond            );
$obFormulario->addHidden          ( $obHdnCodigoConstrucao      );
$obFormulario->addAba             ( "Construção"                );
$obFormulario->addTitulo          ( "Dados para Reforma"        );
$obFormulario->addComponente      ( $obLblCodigoConstrucao      );
$obFormulario->addComponente      ( $obLblTipoEdificao          );
if ($_REQUEST["hdnVinculoEdificacao"] == "Condomínio") {
    $obFormulario->addComponente  ( $obLblNomeCondominio        );
} else {
    $obFormulario->addComponente  ( $obLblNumeroInscricao       );
}
$obFormulario->addComponente      ( $obLblTipoUnidade           );
if ($_REQUEST["hdnVinculoEdificacao"] == "Autônoma" OR $_REQUEST["hdnVinculoEdificacao"] == "Dependente") {
    $obFormulario->addComponente( $obLblAreaTotalEdificada );
}
$obFormulario->addComponente( $obTxtAreaUnidade  );

$obFormulario->addComponente( $obBscProcesso );
$obFormulario->addAba( "Características" );

$obFormulario->Cancelar();
$obFormulario->setFormFocus( $obBscProcesso->obCampoCod->getId() );
$obFormulario->show();

?>
