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
    * Página de Formulário da Caonfiguração do cadastro imobiliario
    * Data de Criação   : 18/03/2005

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Marcelo Boezio Paulinoa

    * @ignore

    * $Id: FMManterCondominioReforma.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.14
*/

/*
$Log$
Revision 1.10  2007/02/06 11:54:00  cercato
Bug #6980#

Revision 1.9  2006/09/18 10:30:12  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMCondominio.class.php"   );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterCondominio";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";
include_once( $pgJs );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "reforma";
}

$obRCIMConfiguracao = new RCIMConfiguracao;

//Recupera mascara do processo
$obRCIMConfiguracao->setCodigoModulo( 12 );
$obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCIMConfiguracao->consultarConfiguracao();
$obRCIMConfiguracao->consultarMascaraProcesso( $stMascaraProcesso );

$arProcesso = preg_split( "/[^a-zA-Z0-9]/", $stMascaraProcesso );
$stProcesso = str_pad( $_REQUEST["inProcesso"], strlen( $arProcesso[0] ), "0", STR_PAD_LEFT );
$stSeparador = preg_replace( "/[a-zA-Z0-9]/","", $stMascaraProcesso );
$stProcesso .= $stSeparador.$_REQUEST["inExercicioProc"];

$stMascaraLote = $obRCIMConfiguracao->getMascaraLote();

//DEFINICAO DOS COMPONENTES
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl"  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao"  );
$obHdnAcao->setValue( $stAcao   );

// HIDDENS PARA GUARDA VALORES

$obHdnCodigoCondominio = new Hidden;
$obHdnCodigoCondominio->setName ( "inCodigoCondominio"  );
$obHdnCodigoCondominio->setValue($_REQUEST["inCodigoCondominio"]);

$obHdnNomCondominio = new Hidden;
$obHdnNomCondominio->setName ( "stNomCondominio" );
$obHdnNomCondominio->setValue( $_REQUEST["stNomCondominio"] );

// LABELS
$obLblCodigoCondominio = new Label;
$obLblCodigoCondominio->setRotulo  ( "Código"                        );
$obLblCodigoCondominio->setName    ( "stCodigoCondominio"            );
$obLblCodigoCondominio->setValue   ( $_REQUEST["inCodigoCondominio"] );

/*$obLblLocalizacao = new Label;
$obLblLocalizacao->setRotulo   ( "Localização" );
$obLblLocalizacao->setId       ( "Localizacao" );
$obLblLocalizacao->setName     ( "stLocalizacao" );
$obLblLocalizacao->setValue    ( $_REQUEST['stLocalizacao']  );

$obLblLote = new Label;
$obLblLote->setRotulo     ( "Lote"      );
$obLblLote->setId         ( "inNumLote" );
$obLblLote->setName       ( "inNumLote" );
$obLblLote->setValue      ( STR_PAD($stValorLote,strlen($stMascaraLote),'0',STR_PAD_LEFT) );
*/
$obLblNomeCondominio = new Label;
$obLblNomeCondominio->setRotulo  ( "Nome"                        );
$obLblNomeCondominio->setName    ( "stNomeCondominio"            );
$obLblNomeCondominio->setValue   ( $_REQUEST["stNomCondominio"]  );

$obLblTipo = new Label;
$obLblTipo->setRotulo  ( "Tipo"                        );
$obLblTipo->setName    ( "stNomCondominio"            );
$obLblTipo->setValue   ( $_REQUEST["stNomTipo"] );

$obLblCGM = new Label;
$obLblCGM->setRotulo  ( "CGM"                        );
$obLblCGM->setName    ( "stCGM"            );
$obLblCGM->setValue   ( $_REQUEST["inNumCGM"]." - ".$_REQUEST["stNomCGM"] );

// components
$obTxtAreaTotalComum = new TextBox;
$obTxtAreaTotalComum->setRotulo       ( "Área Total Comum"    );
$obTxtAreaTotalComum->setName         ( "inAreaTotalComum"    );
$obTxtAreaTotalComum->setId           ( "inAreaTotalComum"    );
$obTxtAreaTotalComum->setValue        ( $_REQUEST['inAreaTotalComum'] );
$obTxtAreaTotalComum->setTitle        ( "Área total comum do condomínio" );
$obTxtAreaTotalComum->setSize         ( 10 );
$obTxtAreaTotalComum->setMaxLength    ( 10 );
$obTxtAreaTotalComum->setFloat        ( true );
$obTxtAreaTotalComum->setNull         ( false );

$obBscProcesso = new BuscaInner;
$obBscProcesso->setRotulo ( "Processo" );
$obBscProcesso->setTitle  ( "Processo do protocolo que formaliza este condomínio" );
$obBscProcesso->obCampoCod->setName ("inProcesso");
if ($_REQUEST['inCodigoProcesso']) {
    $obBscProcesso->obCampoCod->setValue( $_REQUEST['inCodigoProcesso'].'/'.$_REQUEST['inExercicio'] );
}
$obBscProcesso->obCampoCod->obEvento->setOnKeyUp("mascaraDinamico('".$stMascaraProcesso."', this, event);");
$obBscProcesso->obCampoCod->obEvento->setOnChange( "buscaValor('buscaProcesso');" );
$obBscProcesso->obCampoCod->setSize ( strlen($stMascaraProcesso) );
$obBscProcesso->obCampoCod->setMaxLength ( strlen($stMascaraProcesso) );
$obBscProcesso->setFuncaoBusca( "abrePopUp('".CAM_GA_PROT_POPUPS . "processo/FLBuscaProcessos.php','frm','inProcesso','campoInner2','','".Sessao::getId()."','800','550')" );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new FormularioAbas;
$obFormulario->addForm            ( $obForm                     );
$obFormulario->setAjuda           ( "UC-05.01.14"               );
$obFormulario->addHidden          ( $obHdnCtrl                  );
$obFormulario->addHidden          ( $obHdnAcao                  );
$obFormulario->addHidden          ( $obHdnCodigoCondominio      );
$obFormulario->addHidden          ( $obHdnNomCondominio         );
$obFormulario->addTitulo          ( "Dados para Reforma"        );
$obFormulario->addComponente      ( $obLblCodigoCondominio      );
//$obFormulario->addComponente      ( $obLblLocalizacao           );
//$obFormulario->addComponente      ( $obLblLote                  );
$obFormulario->addComponente      ( $obLblNomeCondominio        );
$obFormulario->addComponente      ( $obLblTipo                  );
$obFormulario->addComponente      ( $obLblCGM                   );
$obFormulario->addComponente      ( $obTxtAreaTotalComum        );
$obFormulario->addComponente      ( $obBscProcesso              );
$obFormulario->OK();
$obFormulario->setFormFocus( $obTxtAreaTotalComum->getId() );
$obFormulario->show();

//SistemaLegado::executaFramePrincipal($js);
?>
