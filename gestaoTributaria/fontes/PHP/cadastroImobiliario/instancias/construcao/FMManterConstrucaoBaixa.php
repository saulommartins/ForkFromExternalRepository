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
    * Página de formulário  para baixa de construção
    * Data de Criação   : 10/01/2005

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues

    * @ignore

    * $Id: FMManterConstrucaoBaixa.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.12
*/

/*
$Log$
Revision 1.10  2006/09/18 10:30:16  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConstrucaoOutros.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMImovel.class.php"           );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLote.class.php"             );

$stLink = Sessao::read('stLink');

//Define o nome dos arquivos PHP
$stPrograma = "ManterConstrucao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";
include_once( $pgJs );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

$obRCIMConstrucao = new RCIMConstrucaoOutros;

$obRCIMImovel = new RCIMImovel( new RCIMLote );
$obRCIMImovel->obRCIMConfiguracao->consultarConfiguracao();
$obRCIMImovel->obRCIMConfiguracao->setCodigoModulo( 12 );
$obRCIMImovel->obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCIMImovel->obRCIMConfiguracao->consultarConfiguracao();
$stMascaraIM = $obRCIMImovel->obRCIMConfiguracao->getMascaraIM();
$obRCIMImovel->obRCIMConfiguracao->consultarMascaraProcesso( $stMascaraProcesso );

$obRCIMConstrucao->setCodigoConstrucao( $_REQUEST["inCodigoConstrucao"] );
$obRCIMConstrucao->consultarConstrucao();

//DEFINICAO DOS ATRIBUTOS DE LOTE
$arChaveAtributoConstrucao =  array( "cod_construcao" => $_REQUEST["inCodigoConstrucao"] );
$obRCIMConstrucao->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoConstrucao );
$obRCIMConstrucao->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );

$obMontaAtributos = new MontaAtributos;
$obMontaAtributos->setTitulo     ( "Atributos"  );
$obMontaAtributos->setName       ( "Atributo_"  );
$obMontaAtributos->setRecordSet  ( $rsAtributos );

//DEFINICAO DOS COMPONENTES
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCodigoConstrucao = new Hidden;
$obHdnCodigoConstrucao->setName  ( "inCodigoConstrucao" );
$obHdnCodigoConstrucao->setValue ( $obRCIMConstrucao->getCodigoConstrucao() );

$obHdnNumeroInscricao = new Hidden;
$obHdnNumeroInscricao->setName  ( "inNumeroInscricao" );
$obHdnNumeroInscricao->setValue ( $_REQUEST["inNumeroInscricao"] );

$obLblCodigoConstrucao = new Label;
$obLblCodigoConstrucao->setName     ( "inCodigoConstrucao" );
$obLblCodigoConstrucao->setRotulo   ( "Código" );
$obLblCodigoConstrucao->setValue    ( $obRCIMConstrucao->getCodigoConstrucao() );

$obLblNumeroInscricao = new Label;
$obLblNumeroInscricao->setName      ( "inNumeroInscricao" );
$obLblNumeroInscricao->setRotulo    ( "Inscrição Imobiliária" );
$obLblNumeroInscricao->setTitle     ( "Inscrição imobiliária com a qual a edificação está vinculada" );
$obLblNumeroInscricao->setValue     ( $_REQUEST["inNumeroInscricao"] );

$obLblDescricaoConstrucao = new Label;
$obLblDescricaoConstrucao->setName      ( "stDescricaoConstrucao" );
$obLblDescricaoConstrucao->setRotulo    ( "Descrição" );
$obLblDescricaoConstrucao->setValue     ( $obRCIMConstrucao->getDescricao() );

$obBscProcesso = new BuscaInner;
$obBscProcesso->setRotulo ( "Processo" );
$obBscProcesso->setTitle  ( "Número do processo no protocolo que gerou a aprovação do loteamento" );
$obBscProcesso->obCampoCod->setName ("inProcesso");
$obBscProcesso->obCampoCod->setId   ("inProcesso");
$obBscProcesso->obCampoCod->setValue( $inProcesso );
$obBscProcesso->obCampoCod->obEvento->setOnChange( "buscaValor('buscaProcesso');" );
$obBscProcesso->obCampoCod->obEvento->setOnKeyUp( "mascaraDinamico('".$stMascaraProcesso."', this, event);" );
$obBscProcesso->setFuncaoBusca( "abrePopUp('".CAM_GA_PROT_POPUPS."processo/FLBuscaProcessos.php','frm','inProcesso','campoInner2','','".Sessao::getId()."','800','550')" );

$obTxtJustificativa = new TextArea;
if ($stAcao == "reativar") {
    $obTxtJustificativa->setRotulo( "Motivo da Reativação" );
    $obTxtJustificativa->setName( "stJustReat" );
    $obTxtJustificativa->setTitle ( "Motivo da reativação" );
} else {
    $obTxtJustificativa->setRotulo( "Motivo da Baixa" );
    $obTxtJustificativa->setName( "stJustificativa" );
    $obTxtJustificativa->setTitle ( "Motivo da baixa" );
}
$obTxtJustificativa->setNull  ( false );
$obTxtJustificativa->setCols  ( 30 );
$obTxtJustificativa->setRows  ( 5 );

$obLblDTConstrucao = new Label;
$obLblDTConstrucao->setName      ( "stDtConstrucao" );
$obLblDTConstrucao->setRotulo    ( "Data de Construção" );
$obLblDTConstrucao->setValue     ( $_REQUEST["stDtConstrucao"] );

$obLblJustificativa = new Label;
$obLblJustificativa->setName      ( "stJustificativa" );
$obLblJustificativa->setRotulo    ( "Motivo da Baixa" );
$obLblJustificativa->setValue     ( $_REQUEST["stJustificativa"] );

$obHdnJustificativa = new Hidden;
$obHdnJustificativa->setName  ( "stJustificativa" );
$obHdnJustificativa->setValue ( $_REQUEST["stJustificativa"] );

$obLblDTInicio = new Label;
$obLblDTInicio->setName      ( "stDTInicio" );
$obLblDTInicio->setRotulo    ( "Data da Baixa" );
$obLblDTInicio->setValue     ( $_REQUEST["stDTInicio"] );

$obHdnTimestamp = new Hidden;
$obHdnTimestamp->setName  ( "stTimestamp" );
$obHdnTimestamp->setValue ( $_REQUEST["stTimestamp"] );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm            ( $obForm                   );
$obFormulario->setAjuda           ( "UC-05.01.12"               );
$obFormulario->addHidden          ( $obHdnCtrl                );
$obFormulario->addHidden          ( $obHdnAcao                );
$obFormulario->addHidden          ( $obHdnCodigoConstrucao    );
$obFormulario->addHidden          ( $obHdnNumeroInscricao     );
$obFormulario->addTitulo          ( "Dados para construção"   );
$obFormulario->addComponente      ( $obLblCodigoConstrucao    );
$obFormulario->addComponente      ( $obLblNumeroInscricao     );
$obFormulario->addComponente      ( $obLblDTConstrucao        );
$obFormulario->addComponente      ( $obLblDescricaoConstrucao );

if ($stAcao == "reativar") {
    $obFormulario->addHidden         ( $obHdnJustificativa       );
    $obFormulario->addHidden         ( $obHdnTimestamp           );
    $obFormulario->addComponente     ( $obLblDTInicio            );
    $obFormulario->addComponente     ( $obLblJustificativa       );
}

$obFormulario->addComponente      ( $obBscProcesso            );
$obFormulario->addComponente      ( $obTxtJustificativa       );
$obFormulario->setFormFocus       ( $obBscProcesso->obCampoCod->getId() );
$obFormulario->Cancelar( $pgList );
$obFormulario->show();
?>
