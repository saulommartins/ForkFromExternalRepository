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
    * Página de Formulário para a baixa de imóvel
    * Data de Criação   : 10/12/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @ignore

    * $Id: FMManterImovelBaixa.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.09
*/

/*
$Log$
Revision 1.13  2006/09/18 10:30:44  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLoteRural.class.php"        );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLoteUrbano.class.php"       );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMImovel.class.php"           );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConstrucaoOutros.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMEdificacao.class.php"       );
include_once ( CAM_GT_CIM_COMPONENTES."MontaLocalizacao.class.php" );

$stLink = Sessao::read('stLink');
$link = Sessao::read('link');

//Define o nome dos arquivos PHP
$stPrograma = "ManterImovel";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId().$stLink."inscricao_municipal=".$_REQUEST['inInscricaoMunicipal'];
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";
//include_once( $pgJs );
include_once( $pgOcul );

if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "baixar";
}

$obRCIMConfiguracao = new RCIMConfiguracao;
$obRCIMConfiguracao->setCodigoModulo( 12 );
$obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCIMConfiguracao->consultarConfiguracao();
$obRCIMConfiguracao->consultarMascaraProcesso( $stMascaraProcesso );

$obRCIMImovel = new RCIMImovel( new RCIMLote );
$obRCIMImovel->roRCIMLote->setCodigoLote( $_REQUEST["inCodigoLote"] );
$obRCIMImovel->roRCIMLote->obRCIMLocalizacao->setCodigoLocalizacao( $_REQUEST["inCodigoLocalizacao"] );
$obRCIMImovel->roRCIMLote->obRCIMLocalizacao->consultarLocalizacao();
$stLocalizacao = $obRCIMImovel->roRCIMLote->obRCIMLocalizacao->getValorComposto();
$obRCIMImovel->roRCIMLote->consultarLote();

$obRCIMImovel->setNumeroInscricao( $_REQUEST["inInscricaoMunicipal"] );
$obErro = $obRCIMImovel->consultarImovel();

//DEFINICAO DOS COMPONENTES
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $_REQUEST['stAcao'] );

$obHdnLote = new Hidden;
$obHdnLote->setName  ( "inCodigoLote" );
$obHdnLote->setValue ( $_REQUEST["inCodigoLote"] );

$obHdnSubLote = new Hidden;
$obHdnSubLote->setName  ( "inCodigoSubLote" );
$obHdnSubLote->setValue ( $_REQUEST["inCodigoSubLote"] );

//COMPONENTES PARA A ABA INSCRICAO IMOBILIARIA
$obLblLocalizacao = new Label;
$obLblLocalizacao->setRotulo    ( "Localização" );
$obLblLocalizacao->setValue     ( $stLocalizacao );

$obLblNumeroLote = new Label;
$obLblNumeroLote->setRotulo ( "Lote" );
$obLblNumeroLote->setValue  ( $obRCIMImovel->roRCIMLote->getNumeroLote() );

$obLblBairroLote = new Label;
$obLblBairroLote->setRotulo ( "Bairro" );
$obLblBairroLote->setValue  ( $obRCIMImovel->roRCIMLote->obRCIMBairro->getNomeBairro() );

$obLblNumeroInscricao = new Label;
$obLblNumeroInscricao->setRotulo    ( "Número da Inscrição" );
$obLblNumeroInscricao->setTitle     ( "Número da inscrição imobiliária" );
$obLblNumeroInscricao->setValue     ( $obRCIMImovel->getNumeroInscricao() );

$obHdnNumeroInscricao = new Hidden;
$obHdnNumeroInscricao->setName      ( "inNumeroInscricao"                 );
$obHdnNumeroInscricao->setValue     ( $obRCIMImovel->getNumeroInscricao() );

$obTxtJustificativa = new TextArea;
if ($_REQUEST['stAcao'] == "reativar") {
    $obTxtJustificativa->setName( "stJustReat" );
    $obTxtJustificativa->setId  ( "stJustReat" );
    $obTxtJustificativa->setTitle ( "Motivo da reativação" );
    $obTxtJustificativa->setRotulo( "Motivo da Reativação" );
} else {
    $obTxtJustificativa->setName( "stJustificativa" );
    $obTxtJustificativa->setId  ( "stJustificativa" );
    $obTxtJustificativa->setTitle ( "Motivo da baixa" );
    $obTxtJustificativa->setRotulo( "Motivo da Baixa" );
}
$obTxtJustificativa->setNull  ( false );
$obTxtJustificativa->setCols  ( 30 );
$obTxtJustificativa->setRows  ( 5 );

$obLblJustificativa = new Label;
$obLblJustificativa->setRotulo ( "Motivo da Baixa" );
$obLblJustificativa->setValue  ( $_REQUEST["stJustificativa"] );

$obHdnJustificativa = new Hidden;
$obHdnJustificativa->setName      ( "stJustificativa" );
$obHdnJustificativa->setValue     ( $_REQUEST["stJustificativa"] );

$obHdnTimestamp = new Hidden;
$obHdnTimestamp->setName      ( "stTimestamp" );
$obHdnTimestamp->setValue     ( $_REQUEST["stTimestamp"] );

$obLblDTInicio = new Label;
$obLblDTInicio->setRotulo ( "Data da baixa" );
$obLblDTInicio->setValue  ( $_REQUEST["stDTInicio"] );

$obBscProcesso = new BuscaInner;
$obBscProcesso->setRotulo ( "Processo" );
$obBscProcesso->setTitle ( "Número do processo que formaliza a baixa deste imóvel" );
$obBscProcesso->setNull ( true );
$obBscProcesso->obCampoCod->setName ("inProcesso");
$obBscProcesso->obCampoCod->setId   ("inProcesso");
//$obBscProcesso->obCampoCod->setValue( $inProcesso );
$obBscProcesso->obCampoCod->obEvento->setOnChange( "buscaValor('buscaProcesso');" );
$obBscProcesso->obCampoCod->obEvento->setOnKeyUp( "mascaraDinamico('".$stMascaraProcesso."', this, event);" );
$obBscProcesso->setFuncaoBusca( "abrePopUp('".CAM_GA_PROT_POPUPS."processo/FLBuscaProcessos.php','frm','inProcesso','campoInner2','','".Sessao::getId()."','800','550')" );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc  );
$obForm->setTarget( "oculto" );

//DEFINICAO DO FORMULARIO

$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm               );
$obFormulario->setAjuda     ( "UC-05.01.09"            );
$obFormulario->addHidden    ( $obHdnAcao            );
$obFormulario->addHidden    ( $obHdnLote            );
$obFormulario->addHidden    ( $obHdnSubLote         );
$obFormulario->addHidden    ( $obHdnNumeroInscricao );
$obFormulario->addTitulo    ( "Dados para imóvel"   );
$obFormulario->addComponente( $obLblLocalizacao     );
$obFormulario->addComponente( $obLblNumeroLote      );
$obFormulario->addComponente( $obLblBairroLote      );
$obFormulario->addComponente( $obLblNumeroInscricao );

if ($_REQUEST['stAcao'] == "reativar") {
    $obFormulario->addHidden    ( $obHdnJustificativa   );
    $obFormulario->addHidden    ( $obHdnTimestamp       );
    $obFormulario->addComponente( $obLblDTInicio        );
    $obFormulario->addComponente( $obLblJustificativa   );
}

$obFormulario->addComponente( $obBscProcesso        );
$obFormulario->addComponente( $obTxtJustificativa   );
$obFormulario->setFormFocus( $obBscProcesso->getId() );

$obFormulario->Cancelar( $pgList );
$obFormulario->show();

?>
