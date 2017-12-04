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
    * Filtro para Bairro
    * Data de Criação   : 14/12/2004
    * @author Analista: Ricardo Lopes
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
    * @package URBEM
    * @subpackage Regra

    * @ignore

    * $Id: FLProcurarBairro.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.05

*/

/*
$Log$
Revision 1.6  2006/09/15 15:03:51  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLogradouro.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMBairro.class.php"     );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarBairro";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FMManterBairro.php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";
include_once( $pgJs );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

Sessao::remove('link');

$obRCIMConfiguracao = new RCIMConfiguracao;
$obRCIMConfiguracao->listaDadosMunicipio( $arConfiguracao );

$inCodUF = $inCodUF ? $inCodUF : $arConfiguracao["cod_uf"];
$inCodMunicipio = $inCodMunicipio ? $inCodMunicipio : $arConfiguracao["cod_municipio"];

// DEFINE OBJETOS DAS CLASSES
$rsEstados     = new RecordSet;
$rsMunicipios  = new RecordSet;
$obRCIMBairro     = new RCIMBairro;

// Preenche RecordSet
$obRCIMBairro->setCodigoUF( $inCodUF );
$obRCIMBairro->listarUF( $rsEstados );

$obRCIMBairro->setCodigoUF( $inCodUF );
$obRCIMBairro->setCodigoMunicipio( $inCodMunicipio );
$obRCIMBairro->listarMunicipios( $rsMunicipios );

// OBJETOS HIDDEN
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao );

$obHdnCodUF = new Hidden;
$obHdnCodUF->setName  ( "inCodUF" );
$obHdnCodUF->setValue ( $inCodUF   );

$obHdnCodMunicipio = new Hidden;
$obHdnCodMunicipio->setName  ( "inCodMunicipio" );
$obHdnCodMunicipio->setValue ( $inCodMunicipio   );

$obHdnCampoCodigo = new Hidden;
$obHdnCampoCodigo->setName  ( "campoNum" );
$obHdnCampoCodigo->setValue ( $_REQUEST["campoNum"] );

$obHdbCampoDescricao = new Hidden;
$obHdbCampoDescricao->setName  ( "campoNom" );
$obHdbCampoDescricao->setValue ( $_REQUEST["campoNom"] );

// DEFINE OBJETOS DO FORMULARIO

$obTxtCodigoBairro = new TextBox;
$obTxtCodigoBairro->setRotulo    ( "C&oacute;digo"          );
$obTxtCodigoBairro->setName      ( "inCodigoBairro"         );
$obTxtCodigoBairro->setValue     ( $_REQUEST["inCodBairro"] );
$obTxtCodigoBairro->setSize      ( 8                        );
$obTxtCodigoBairro->setInteiro   ( true                     );
$obTxtCodigoBairro->setMaxLength ( 8                        );
$obTxtCodigoBairro->setNull      ( true                     );

$obTxtNomeBairro = new TextBox;
$obTxtNomeBairro->setRotulo    ( "Nome"                   );
$obTxtNomeBairro->setName      ( "stNomBairro"            );
$obTxtNomeBairro->setValue     ( $_REQUEST["stNomBairro"] );
$obTxtNomeBairro->setSize      ( 40                       );
$obTxtNomeBairro->setMaxLength ( 40                       );
$obTxtNomeBairro->setNull      ( true                     );

$obLblEstado = new Label;
$obLblEstado->setRotulo( "Estado" );
$obLblEstado->setValue( $rsEstados->getCampo("nom_uf") );

$obLblMunicipio = new Label;
$obLblMunicipio->setRotulo( "Município" );
$obLblMunicipio->setValue( $rsMunicipios->getCampo("nom_municipio") );

$obBtnOk = new OK;

$obBtnLimpar = new Limpar;

$obBtnFechar = new Button;
$obBtnFechar->setName              ( "botaoFechar" );
$obBtnFechar->setValue             ( "Fechar"      );
$obBtnFechar->obEvento->setOnClick ( "fechar();"   );

$arButtom = array( $obBtnOk, $obBtnLimpar, $obBtnFechar );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction   ( $pgList    );
//$obForm->setTarget   ( "oculto"   );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm         ( $obForm              );
$obFormulario->addHidden       ( $obHdnCtrl           );
$obFormulario->addHidden       ( $obHdnAcao           );
$obFormulario->addHidden       ( $obHdnCodUF          );
$obFormulario->addHidden       ( $obHdnCodMunicipio   );
$obFormulario->addHidden       ( $obHdnCampoCodigo    );
$obFormulario->addHidden       ( $obHdbCampoDescricao );
$obFormulario->addIFrameOculto ( "oculto" );
$obFormulario->obIFrame->setHeight("0");
$obFormulario->obIFrame->setWidth("100%");
$obFormulario->addTitulo       ( "Dados para filtro" );
$obFormulario->addComponente         ( $obTxtCodigoBairro                  );
$obFormulario->addComponente         ( $obTxtNomeBairro                    );
$obFormulario->addComponente( $obLblEstado );
$obFormulario->addComponente( $obLblMunicipio );
//$obFormulario->defineBarra( $arButtom, 'left' );
$obFormulario->Ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
