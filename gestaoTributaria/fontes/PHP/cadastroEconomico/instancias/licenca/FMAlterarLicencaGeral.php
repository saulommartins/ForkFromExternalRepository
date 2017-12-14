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
    * Formulario para alteração de elementos de licença geral
    * Data de Criação   : 29/04/2005
    * @author Analista      : Fábio Bertoldi Rodrigues
    * @author Desenvolvedor : Lucas Teixeira Stephanou
    * @package URBEM
    * @subpackage Regra

    * $Id: FMAlterarLicencaGeral.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.12

*/

/*
$Log$
Revision 1.12  2007/02/01 16:40:04  cercato
Bug #7330#

Revision 1.11  2006/09/15 14:33:14  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMLicencaDiversa.class.php"     );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMTipoLicencaDiversa.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMElemento.class.php"           );

//Define o nome dos arquivos PHP
$stPrograma     = "ConcederLicencaGeral";
$pgFilt         = "FL".$stPrograma.".php"       ;
$pgList         = "LSLicencaGeral.php"          ;
$pgForm         = "FM".$stPrograma.".php"       ;
$pgFormTipo     = "FM".$stPrograma."Tipo.php"   ;
$pgProc         = "PR".$stPrograma.".php"       ;
$pgOcul         = "OC".$stPrograma.".php"       ;
$pgJs           = "JS".$stPrograma.".js"        ;
include_once( $pgJs );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

Sessao::write( "lsElementos", array() );
Sessao::write( "inNumElementos", 0 );
// instancia objeto para listagem de elementos
$obRCEMElementos = new RCEMElemento($obAtividadeTmp);

if ($_REQUEST["inCodigoTipo"]) {
    $obRCEMTipoLicencaDiversa = new RCEMTipoLicencaDiversa;
    $obRCEMElementos->referenciaTipoLicencaDiversa( $obRCEMTipoLicencaDiversa );
    $obRCEMElementos->roRCEMTipoLicencaDiversa->setCodigoTipoLicencaDiversa( $_REQUEST["inCodigoTipo"] );
}
$obRCEMElementos->listarElementoTipoLicencaDiversa( $rsElementosLicencaDiversa );

// OBJETOS HIDDEN
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName                      ( "stCtrl"                      );
$obHdnCtrl->setValue                     ( $_REQUEST["stCtrl"]           );

$obHdnAcao = new Hidden;
$obHdnAcao->setName                      ( "stAcao"                      );
$obHdnAcao->setValue                     ( $stAcao                       );

$obHdnCodTipoLicenca = new Hidden;
$obHdnCodTipoLicenca->setName            ( "inCodigoTipoLicenca"            );
$obHdnCodTipoLicenca->setValue           ( $_REQUEST["inCodigoTipoLicenca"] );

//Incluir validação adicional no Salvar
$stEval = "
if (document.frm.boElemento.value == '0') {
    erro = true;
    mensagem += '@Deve haver ao menos 1(um) elemento na licença';
}
";

$obHdnEval = new HiddenEval;
$obHdnEval->setName( "stEval" );
$obHdnEval->setValue( $stEval );

$obHdnNumAtributos = new Hidden;
$obHdnNumAtributos->setName     ( "inNumAtributos"  );
$obHdnNumAtributos->setId       ( "inNumAtributos"  );
$obHdnNumAtributos->setValue    ( 0                 );

$obHdnboElemento = new Hidden;
$obHdnboElemento->setId     ( "boElemento" );
$obHdnboElemento->setName   ( "boElemento" );
$obHdnboElemento->setValue  ("0");

$obHdnCodTipoLicenca= new Hidden;
$obHdnCodTipoLicenca->setName   ( "inCodigoTipoLicenca"      );
$obHdnCodTipoLicenca->setValue  ($_REQUEST["inCodigoTipo"]   );

$obHdnCodigoLicenca = new Hidden;
$obHdnCodigoLicenca->setName           ( "inCodigoLicenca"                 );
$obHdnCodigoLicenca->setValue          ( $_REQUEST["inCodigoLicenca"]      );

$obHdnExercicio= new Hidden;
$obHdnExercicio->setName   ( "stExercicio"              );
$obHdnExercicio->setValue  ($_REQUEST["stExercicio"]    );

// SPANS
$obSpnAtributosElemento = new Span;
$obSpnAtributosElemento->setId      ( "spnAtributosElemento"    );

$obSpnListaElementos    = new Span;
$obSpnListaElementos->setId         ("spnListaElementos"        );

//DEFINIÇÃO DOS COMPONENTES
// DADOS PARA LICENÇA
$obTxtCodigoLicenca = new Label;
$obTxtCodigoLicenca->setRotulo         ( "Número da Licença"               );
$obTxtCodigoLicenca->setTitle          ( "Número da Licença"               );
$obTxtCodigoLicenca->setName           ( "inCodigoLicenca"                 );
$obTxtCodigoLicenca->setValue          ( $_REQUEST["inCodigoLicenca"]." - ".$_REQUEST["stNomTipo"] );

$obLblCGM = new Label;
$obLblCGM->setRotulo( "CGM" );
$obLblCGM->setTitle( "Pessoa fisica ou juridica cadastrada" );
$obLblCGM->setId( "inNomCGM" );
$obLblCGM->setValue ($_REQUEST["inNumCGM"]." - ".$_REQUEST["stNomeCGM"]);

// ABA ****** ELEMENTOS PARA BASE DE CALCULO
$obTxtElementos = new TextBox;
$obTxtElementos->setName  ( "stCodigoElemento"  );
$obTxtElementos->setId    ( "stCodigoElemento"  );
$obTxtElementos->setTitle ( "*Elemento"          );
$obTxtElementos->setRotulo( "*Elemento"          );

$obCmbElementos  = new Select;
$obCmbElementos->setName         ( "cmbElementos"                   );
$obCmbElementos->setId           ( "cmbElementos"                   );
$obCmbElementos->addOption       ( "", "Selecione"                  );
$obCmbElementos->setTitle        ( "Tipo de Licença"                );
$obCmbElementos->setCampoId      ( "cod_elemento"                   );
$obCmbElementos->setCampoDesc    ( "nom_elemento"                   );
$obCmbElementos->preencheCombo   ( $rsElementosLicencaDiversa       );
$obCmbElementos->setValue        ( $_REQUEST["stCodigoElemento"]    );
$obCmbElementos->setNull         ( true                            );
$obCmbElementos->setStyle        ( "width: 220px"                   );
$obCmbElementos->obEvento->setOnChange("montaAtributosElementos();" );

$obHdnNomElemento = new Hidden;
$obHdnNomElemento->setName  ("stNomElemento");
$obHdnNomElemento->setId    ("stNomElemento");

//DEFINICAO DO FORM

$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                  ( $obForm                       );
$obFormulario->setAjuda      ( "UC-05.02.12");
$obFormulario->addTitulo                ( "Dados para Licença"          );
$obFormulario->addHidden                ( $obHdnCtrl                    );
$obFormulario->addHidden                ( $obHdnAcao                    );
$obFormulario->addHidden                ( $obHdnEval,true               );
$obFormulario->addHidden                ( $obHdnboElemento              );
$obFormulario->addHidden                ( $obHdnNomElemento             );
$obFormulario->addHidden                ( $obHdnNumAtributos            );
$obFormulario->addHidden                ( $obHdnCodigoLicenca           );
$obFormulario->addHidden                ( $obHdnCodTipoLicenca          );
$obFormulario->addHidden                ( $obHdnExercicio               );
$obFormulario->addComponente            ( $obTxtCodigoLicenca           );
$obFormulario->addComponente            ( $obLblCGM                     );
$obFormulario->addTitulo                ( "Dados de Elementos para Base de Cálculo" );
$obFormulario->addComponenteComposto    ( $obTxtElementos,$obCmbElementos   );
$obFormulario->addSpan                  ( $obSpnAtributosElemento           );
$obFormulario->addSpan                  ( $obSpnListaElementos              );
$obFormulario->cancelar();
$obFormulario->show();
sistemaLegado::BloqueiaFrames();
$stJs .= "buscaValor('montaAlteracaoAtributosElementos');";
sistemaLegado::executaFrameOculto($stJs);

?>
