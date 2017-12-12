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
    * Página de Formulário baixa para o cadastro de lote
    * Data de Criação   : 29/12/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @ignore

    * $Id: FMManterLoteBaixa.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.08
*/

/*
$Log$
Revision 1.13  2006/09/18 10:30:54  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLoteRural.class.php"        );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLoteUrbano.class.php"       );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php"     );
include_once ( CAM_GT_CIM_COMPONENTES."MontaLocalizacao.class.php" );

$stLink = Sessao::read('stLink');

//Define o nome dos arquivos PHP
$stPrograma = "ManterLote";

$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId().$stLink."&funcionalidade=".$_REQUEST["funcionalidade"];
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once( $pgJs );
//include_once( $pgOcul );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "baixar";
}

//[funcionalidade] => 178 ->Lote Urbano  193 ->Lote Rural
if ($_REQUEST["funcionalidade"] == 178) {
    $obRCIMLote = new RCIMLoteUrbano;
} elseif ($_REQUEST["funcionalidade"] == 193) {
    $obRCIMLote = new RCIMLoteRural;
}

$obMontaLocalizacao = new MontaLocalizacao;
$obMontaLocalizacao->setCadastroLocalizacao( false );

$obRCIMConfiguracao = new RCIMConfiguracao;
$obRCIMConfiguracao->consultarConfiguracao();
$obRCIMConfiguracao->setCodigoModulo( 12 );
$obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCIMConfiguracao->consultarConfiguracao();
$stMascaraIM = $obRCIMConfiguracao->getMascaraIM();
$obRCIMConfiguracao->consultarMascaraProcesso( $stMascaraProcesso );

$obRCIMLote->setCodigoLote( $_REQUEST["inCodigoLote"] );
$obRCIMLote->consultarLote();

//DEFINICAO DOS COMPONENTES
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnFuncionalidade = new Hidden;
$obHdnFuncionalidade->setName  ( "funcionalidade"            );
$obHdnFuncionalidade->setValue ( $_REQUEST["funcionalidade"] );

$obHdnTrecho = new Hidden;
$obHdnTrecho->setName( "stTrecho" );
$obHdnTrecho->setValue( $stTrecho );

$obHdnCodigoUF = new Hidden;
$obHdnCodigoUF->setName ( "inCodigoUF" );
$obHdnCodigoUF->setValue ( $obRCIMLote->obRCIMBairro->getCodigoUF() );

$obHdnCodigoMunicipio = new Hidden;
$obHdnCodigoMunicipio->setName( "inCodigoMunicipio" );
$obHdnCodigoMunicipio->setValue( $obRCIMLote->obRCIMBairro->getCodigoMunicipio() );

$obHdnCodigoLote = new Hidden;
$obHdnCodigoLote->setName  ( "inCodigoLote" );
$obHdnCodigoLote->setValue ( $_REQUEST["inCodigoLote"] );

$obHdnCodigoLocalizacao = new Hidden;
$obHdnCodigoLocalizacao->setName( "inCodigoLocalizacao" );
$obHdnCodigoLocalizacao->setValue( $_REQUEST["inCodigoLocalizacao"] );

$obHdnNumeroLote = new Hidden;
$obHdnNumeroLote->setName  ( "stNumeroLote" );
$obHdnNumeroLote->setValue ( $obRCIMLote->getNumeroLote() );

//DADOS PARA ABA LOTE
$obLblNumeroLote = new Label;
$obLblNumeroLote->setName   ( "stNumeroLote"   );
$obLblNumeroLote->setRotulo ( "Número do Lote" );
$obLblNumeroLote->setValue  ( STR_PAD($obRCIMLote->getNumeroLote(),4,'0',STR_PAD_LEFT));

$obBscProcesso = new BuscaInner;
$obBscProcesso->setRotulo ( "Processo" );
$obBscProcesso->setTitle ( "Número do processo no protocolo que gerou a aprovação do loteamento" );
$obBscProcesso->setNull ( true );
$obBscProcesso->obCampoCod->setName ("inProcesso");
$obBscProcesso->obCampoCod->setId   ("inProcesso");
$obBscProcesso->obCampoCod->setValue( $inProcesso );
$obBscProcesso->obCampoCod->obEvento->setOnChange( "buscaValor('buscaProcesso');" );
$obBscProcesso->obCampoCod->obEvento->setOnKeyUp( "mascaraDinamico('".$stMascaraProcesso."', this, event);" );
$obBscProcesso->setFuncaoBusca( "abrePopUp('".CAM_GA_PROT_POPUPS."processo/FLBuscaProcessos.php','frm','inProcesso','campoInner2','','".Sessao::getId()."','800','550')" );

$obTxtJustificativa = new TextArea;
if ($stAcao == "reativar") {
    $obTxtJustificativa->setName   ( "stJustReat" );
    $obTxtJustificativa->setId     ( "stJustReat" );
    $obTxtJustificativa->setTitle  ( "Motivo da reativação" );
    $obTxtJustificativa->setRotulo ( "Motivo da Reativação" );
} else {
    $obTxtJustificativa->setName   ( "stJustificativa" );
    $obTxtJustificativa->setId     ( "stJustificativa" );
    $obTxtJustificativa->setTitle  ( "Motivo da baixa" );
    $obTxtJustificativa->setRotulo ( "Motivo da Baixa" );
}
$obTxtJustificativa->setRows   ( 5 );
$obTxtJustificativa->setCols   ( 30 );
$obTxtJustificativa->setNull   ( false );

$obLblJustificativa = new Label;
$obLblJustificativa->setName   ( "stJustificativa"             );
$obLblJustificativa->setRotulo ( "Motivo da Baixa"             );
$obLblJustificativa->setValue  ( $_REQUEST["stJustificativa"]  );

$obHdnJustificativa = new Hidden;
$obHdnJustificativa->setName  ( "stJustificativa" );
$obHdnJustificativa->setValue ( $_REQUEST["stJustificativa"] );

$obLblDTInicio = new Label;
$obLblDTInicio->setName   ( "stDTInicio"             );
$obLblDTInicio->setRotulo ( "Data da Baixa"          );
$obLblDTInicio->setValue  ( $_REQUEST["stDTInicio"]  );

$obHdnTimestamp = new Hidden;
$obHdnTimestamp->setName  ( "stTimestamp" );
$obHdnTimestamp->setValue ( $_REQUEST["stTimestamp"] );

$obLblNomeLocalizacao = new Label;
$obLblNomeLocalizacao->setName   ( "stNomeLocalizacao" );
$obLblNomeLocalizacao->setRotulo ( "Localização"       );
$obLblNomeLocalizacao->setValue  ( $_REQUEST["stValorComposto"]." - ".$_REQUEST["stNomLocalizacao"] );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

$obFormulario = new Formulario;
$obFormulario->addForm            ( $obForm    );
$obFormulario->setAjuda           ( "UC-05.01.08" );
$obFormulario->addHidden          ( $obHdnCtrl );
$obFormulario->addHidden          ( $obHdnFuncionalidade  );
$obFormulario->addHidden          ( $obHdnCodigoLote      );
$obFormulario->addHidden          ( $obHdnAcao            );
$obFormulario->addHidden          ( $obHdnTrecho          );
$obFormulario->addHidden          ( $obHdnCodigoUF        );
$obFormulario->addHidden          ( $obHdnCodigoMunicipio );
$obFormulario->addHidden          ( $obHdnNumeroLote      );

$obFormulario->addTitulo          ( "Dados para lote"     );
$obFormulario->addComponente      ( $obLblNumeroLote      );
$obFormulario->addComponente      ( $obLblNomeLocalizacao );

if ($stAcao == "reativar") {
    $obFormulario->addHidden          ( $obHdnTimestamp       );
    $obFormulario->addHidden          ( $obHdnJustificativa   );
    $obFormulario->addComponente      ( $obLblDTInicio        );
    $obFormulario->addComponente      ( $obLblJustificativa   );
}

$obFormulario->addComponente      ( $obBscProcesso        );
$obFormulario->addComponente      ( $obTxtJustificativa   );
$obFormulario->setFormFocus       ( $obBscProcesso->getId() );

$obFormulario->Cancelar( $pgList );
$obFormulario->show();

?>
