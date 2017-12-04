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
    * Página de filtro para o cadastro de bairro
    * Data de Criação   : 24/09/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues

    * @ignore

    * $Id: FLProcurarBairro.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.05
*/

/*
$Log$
Revision 1.5  2007/07/27 19:39:09  cercato
Bug#9777#

Revision 1.4  2006/09/15 15:03:47  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLogradouro.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMBairro.class.php"     );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php" );

session_regenerate_id();
Sessao::setId( "PHPSESSID=".session_id());
$sessao->geraURLRandomica();

//Sessao::read('acao')   = "784";
//Sessao::read('modulo') = "0";

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

if ($_REQUEST["stNomeLogradouro"]) {
    Sessao::write('stNomeLogradouro', $_REQUEST["stNomeLogradouro"]);
    Sessao::write('inCodigoTipo'    , $_REQUEST["inCodigoTipo"]);
}

Sessao::remove('link');

$inCodUF = $_REQUEST["inCodUF"] ? $_REQUEST["inCodUF"] : Sessao::read('cod_uf');
$inCodMunicipio = $_REQUEST["inCodMunicipio"] ? $_REQUEST["inCodMunicipio"] : Sessao::read('cod_municipio');

$obRCIMConfiguracao = new RCIMConfiguracao;
$obRCIMConfiguracao->listaDadosMunicipio( $arConfiguracao );

$inCodUF = $inCodUF ? $inCodUF : $arConfiguracao["cod_uf"];
$inCodMunicipio = $inCodMunicipio ? $inCodMunicipio : $arConfiguracao["cod_municipio"];

// DEFINE OBJETOS DAS CLASSES
$rsEstados     = new RecordSet;
$rsMunicipios  = new RecordSet;
$obRCIMBairro     = new RCIMBairro;

// Preenche RecordSet
$obRCIMBairro->listarUF( $rsEstados );

// OBJETOS HIDDEN
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao );

$obHdnCodUF = new Hidden;
$obHdnCodUF->setName  ( "hdnCodUF" );
$obHdnCodUF->setValue ( $inCodUF   );

$obHdnCodMunicipio = new Hidden;
$obHdnCodMunicipio->setName  ( "hdnCodMunicipio" );
$obHdnCodMunicipio->setValue ( $inCodMunicipio   );

// DEFINE OBJETOS DO FORMULARIO

$obTxtCodigoBairro = new TextBox;
$obTxtCodigoBairro->setRotulo    ( "C&oacute;digo"          );
$obTxtCodigoBairro->setName      ( "inCodigoBairro"         );
$obTxtCodigoBairro->setValue     ( $_REQUEST["inCodBairro"] );
$obTxtCodigoBairro->setSize      ( 8                        );
$obTxtCodigoBairro->setMaxLength ( 8                        );
$obTxtCodigoBairro->setNull      ( true                     );
$obTxtCodigoBairro->setInteiro   ( true                     );

$obTxtNomeBairro = new TextBox;
$obTxtNomeBairro->setRotulo    ( "Nome"                   );
$obTxtNomeBairro->setName      ( "stNomBairro"            );
$obTxtNomeBairro->setValue     ( $_REQUEST["stNomBairro"] );
$obTxtNomeBairro->setSize      ( 40                       );
$obTxtNomeBairro->setMaxLength ( 40                       );
$obTxtNomeBairro->setNull      ( true                     );

$obTxtCodEstado = new TextBox;
$obTxtCodEstado->setRotulo             ( "Estado"               );
$obTxtCodEstado->setName               ( "inCodUF"              );
$obTxtCodEstado->setValue              ( $inCodUF               );
$obTxtCodEstado->setSize               ( 8                      );
$obTxtCodEstado->setMaxLength          ( 8                      );
$obTxtCodEstado->setNull               ( false                  );
$obTxtCodEstado->setInteiro            ( true                   );
$obTxtCodEstado->obEvento->setOnChange ( "preencheMunicipio('');" );

$obCmbEstado = new Select;
$obCmbEstado->setName               ( "cmbEstado"            );
$obCmbEstado->addOption             ( "", "Selecione"        );
$obCmbEstado->setCampoId            ( "cod_uf"               );
$obCmbEstado->setCampoDesc          ( "nom_uf"               );
$obCmbEstado->preencheCombo         ( $rsEstados             );
$obCmbEstado->setValue              ( $inCodUF               );
$obCmbEstado->setNull               ( false                  );
$obCmbEstado->setStyle              ( "width: 220px"         );
$obCmbEstado->obEvento->setOnChange ( "preencheMunicipio('');" );

if ($inCodUF) {
    $obRCIMBairro->setCodigoUF( $inCodUF );
    $obRCIMBairro->listarMunicipios( $rsMunicipios );
}

$obTxtCodMunicipio = new TextBox;
$obTxtCodMunicipio->setRotulo    ( "Munic&iacute;pio" );
$obTxtCodMunicipio->setName      ( "inCodMunicipio"   );
$obTxtCodMunicipio->setValue     ( $inCodMunicipio    );
$obTxtCodMunicipio->setSize      ( 8                  );
$obTxtCodMunicipio->setMaxLength ( 8                  );
$obTxtCodMunicipio->setNull      ( false              );
$obTxtCodMunicipio->setInteiro   ( true               );

$obCmbMunicipio = new Select;
$obCmbMunicipio->setName       ( "cmbMunicipio"  );
$obCmbMunicipio->addOption     ( "", "Selecione" );
$obCmbMunicipio->setCampoId    ( "cod_municipio" );
$obCmbMunicipio->setCampoDesc  ( "nom_municipio" );
$obCmbMunicipio->setValue      ( $inCodMunicipio );
$obCmbMunicipio->preencheCombo ( $rsMunicipios   );
$obCmbMunicipio->setNull       ( false           );
$obCmbMunicipio->setStyle      ( "width: 220px"  );

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
$obFormulario->addForm         ( $obForm        );
$obFormulario->addHidden       ( $obHdnCtrl     );
$obFormulario->addHidden       ( $obHdnAcao     );
$obFormulario->addIFrameOculto ( "oculto" );
$obFormulario->obIFrame->setHeight("0");
$obFormulario->obIFrame->setWidth("100%");
$obFormulario->addTitulo       ( "Dados para filtro" );
$obFormulario->addComponente         ( $obTxtCodigoBairro                  );
$obFormulario->addComponente         ( $obTxtNomeBairro                    );
$obFormulario->addComponenteComposto ( $obTxtCodEstado, $obCmbEstado       );
$obFormulario->addComponenteComposto ( $obTxtCodMunicipio, $obCmbMunicipio );
//$obFormulario->defineBarra( $arButtom, 'left' );
$obFormulario->Ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
