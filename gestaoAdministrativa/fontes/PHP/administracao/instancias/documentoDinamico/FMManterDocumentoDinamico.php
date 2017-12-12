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
* Arquivo de instância para manutenção de documentos dinâmicos
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 5854 $
$Name$
$Author: lizandro $
$Date: 2006-02-01 16:38:53 -0200 (Qua, 01 Fev 2006) $

Casos de uso: uc-01.03.99
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GA_ADM_NEGOCIO."RDocumentoDinamicoDocumento.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterDocumentoDinamico";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obRDocumentoDinamico = new RDocumentoDinamicoDocumento;

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

//DEFINICAO DOS COMPONENTES
$obHdnCodModulo = new Hidden;
$obHdnCodModulo->setName( "inCodModulo" );
$obHdnCodModulo->setValue( $_REQUEST['stModulo'] );

$obHdnCodDocumento = new Hidden;
$obHdnCodDocumento->setName( "inCodDocumento" );
$obHdnCodDocumento->setValue( $inCodDocumento );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnControleTextArea = new Hidden;
$obHdnControleTextArea->setName ( "stControleTextArea" );
$obHdnControleTextArea->setValue( $stControleTextArea );

$obHdnRemove = new Hidden;
$obHdnRemove->setName ( "stRemove" );
$obHdnRemove->setValue( $stRemove );

$obHdnInclui = new Hidden;
$obHdnInclui->setName ( "stInclui" );
$obHdnInclui->setValue( $stInclui );

$obHdnBloco = new Hidden;
$obHdnBloco->setName ( "inBloco" );
$obHdnBloco->setValue( 1 );

$obSpnBloco = new Span;
$obSpnBloco->setId ( "spnBloco" );

$obForm = new Form;
$obForm->setAction            ( $pgProc );
$obForm->setTarget            ( "oculto" );

$obBtnOK = new OK;
$obBtnLimpar = new Limpar;
$arBotoes = array( $obBtnOK, $obBtnLimpar );

include_once 'FMManterDocumentoDinamicoAbaIdentificacaoDocumento.php';
include_once 'FMManterDocumentoDinamicoAbaTabulacao.php';
include_once 'FMManterDocumentoDinamicoAbaFonte.php';
include_once 'FMManterDocumentoDinamicoAbaTagBanco.php';

sistemaLegado::executaFrameOculto("modificaDado('mostraBlocoBanco')");

$obBtnIncluirBloco = new Button;
$obBtnIncluirBloco->setName              ( "btIncluiBloco"                                );
$obBtnIncluirBloco->setValue             ( "Incluir Bloco "                               );
$obBtnIncluirBloco->obEvento->setOnClick ( "modificaDado('incluiBloco');"                   );

//DEFINICAO DO FORMULARIO

$obFormulario = new FormularioAbas;
$obFormulario->addForm       ( $obForm                   );

//Aba Indentificação do Documento
$obFormulario->addAba        ( "Identificação do Documento"      );
$obFormulario->addFuncaoAba  ( "HabilitaLayer('');" );
$obFormulario->addComponente ( $obLblModulo           );
$obFormulario->addComponente ( $obTxtDocumento           );
$obFormulario->addComponente ( $obTxtTitulo              );

// Aba Tabulação
$obFormulario->addAba        ( "Tabulação"      );
$obFormulario->addFuncaoAba  ( "HabilitaLayer('');" );
$obFormulario->addComponente ( $obTxtMargenEsq           );
$obFormulario->addComponente ( $obTxtMargenTop           );
$obFormulario->addComponente ( $obTxtMargenDir           );

//Aba Fonte
$obFormulario->addAba        ( "Fonte"      );
$obFormulario->addFuncaoAba  ( "HabilitaLayer('');" );
$obFormulario->addComponente ( $obCmbFontes              );
$obFormulario->addComponente ( $obTxtTamFonte            );
$obFormulario->addTitulo     ( "Efeito da fonte"         );
$obFormulario->addComponente ( $obCmbTagFormatacao       );

//Aba tags de banco
$obFormulario->addAba        ( "Tag's de banco"  );
$obFormulario->addFuncaoAba  ( "HabilitaLayer('');" );
$obFormulario->addComponenteComposto ( $obTxtCombo, $obCmbTagRS) ;

$obFormulario->addHidden     ( $obHdnAcao                );
$obFormulario->addHidden     ( $obHdnRemove              );
$obFormulario->addHidden     ( $obHdnInclui              );
$obFormulario->addHidden     ( $obHdnControleTextArea    );
$obFormulario->addHidden     ( $obHdnCtrl                );
$obFormulario->addHidden     ( $obHdnCodDocumento        );
$obFormulario->addHidden     ( $obHdnCodModulo           );
$obFormulario->addHidden     ( $obHdnBloco               );
$obFormulario->addHidden     ( $obHdnModulo              );
$obFormulario->addDiv(5, "Bloco" );
//$obFormulario->defineBarra           ( array ($obBtnIncluirBloco),"" );
$obFormulario->addSpan       ( $obSpnBloco               );
$obFormulario->fechaDiv();

$obFormulario->OK    ();
$obFormulario->show  ();

include( $pgJs );

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
