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
    * Data de Criação   : 05/04/2005

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Lucas Leusin Oiagen

    * @ignore

    * $Id: FMManterCondominioCaracteristica.php 63230 2015-08-05 20:49:42Z arthur $

    * Casos de uso: uc-05.01.14
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

//Instancia objetos
$obRCIMCondominio   = new RCIMCondominio;
$rsTipoCondominio   = new RecordSet;
$obMontaAtributos   = new MontaAtributos;
$obRCIMConfiguracao = new RCIMConfiguracao;

//Recupera mascara do processo
$obRCIMConfiguracao->setCodigoModulo( 12 );
$obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCIMConfiguracao->consultarConfiguracao();
$obRCIMConfiguracao->consultarMascaraProcesso( $stMascaraProcesso );
$stSeparador = preg_replace( "/[a-zA-Z0-9]/","", $stMascaraProcesso );

$stMascaraLote = $obRCIMConfiguracao->getMascaraLote();

$obRCIMCondominio->setCodigoCondominio( $request->get('inCodigoCondominio') );
$obRCIMCondominio->listarProcessos( $rsListaProcesso );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

// OBJETOS HIDDEN
$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnCodigoCondominio = new Hidden;
$obHdnCodigoCondominio->setName( "inCodigoCondominio" );
$obHdnCodigoCondominio->setValue( $request->get('inCodigoCondominio') );

$obHdnNomCondominio = new Hidden;
$obHdnNomCondominio->setName( "stNomCondominio" );
$obHdnNomCondominio->setValue( $request->get('stNomCondominio') );

//DEFINICAO DO FORMULARIO
$obForm = new Form;
$obForm->setAction            ( $pgProc );
$obForm->setTarget            ( "oculto" );

include_once 'FMManterCondominioCaracteristicaAbaCaracteristica.php';
include_once 'FMManterCondominioCaracteristicaAbaProcesso.php';

$obFormulario = new FormularioAbas;
$obFormulario->addForm        ( $obForm      );
$obFormulario->setAjuda       ( "UC-05.01.14" );
$obFormulario->addHidden      ( $obHdnAcao   );
$obFormulario->addHidden      ( $obHdnCtrl   );
$obFormulario->addHidden      ( $obHdnCodigoCondominio );
$obFormulario->addHidden      ( $obHdnNomCondominio );

$obFormulario->addAba       ( "Características"       );
$obFormulario->addTitulo    ( "Dados para Condomínio" );
$obFormulario->addComponente( $obLblCodigoCondominio  );
$obFormulario->addComponente( $obLblLocalizacao       );
$obFormulario->addComponente( $obLblLote              );
$obFormulario->addComponente( $obLblNomCondominio     );
$obFormulario->addComponente( $obLblTipoCondominio    );

if( $request->get('inNumCGM') )
    $obFormulario->addComponente  ( $obLblCGM          );

$obFormulario->addComponente( $obBscProcesso        );
$obMontaAtributosCondominio->geraFormulario( $obFormulario      );

$obFormulario->addAba       ( "Processos" );
$obFormulario->addTitulo    ( "Dados para Condomínio" );
$obFormulario->addComponente( $obLblCodigoCondominio  );
$obFormulario->addComponente( $obLblLocalizacao       );
$obFormulario->addComponente( $obLblLote              );
$obFormulario->addComponente( $obLblNomCondominio     );
$obFormulario->addComponente( $obLblTipoCondominio    );

if( $request->get('inNumCGM') )
    $obFormulario->addComponente  ( $obLblCGM          );
    
$obFormulario->addSpan      ( $obSpnProcesso        );
$obFormulario->addSpan      ( $obSpnAtributosProcesso );

$obFormulario->Cancelar ();
$obFormulario->setFormFocus( $obBscProcesso->obCampoCod->getId() );
$obFormulario->show();

?>