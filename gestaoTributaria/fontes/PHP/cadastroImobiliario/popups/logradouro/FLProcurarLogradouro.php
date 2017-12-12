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
    * Página de filtro para o cadastro de logradouro
    * Data de Criação   : 08/09/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues
                             Gustavo Passos Tourinho
                             Cassiano de Vasconcelos Ferreira

    * @ignore

    * $Id: FLProcurarLogradouro.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.04
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLogradouro.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarLogradouro";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FMManterLogradouro.php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once( $pgJs );

$acao   = Sessao::read('acao');
$modulo = Sessao::read('modulo');

Sessao::write('acao', "783");
Sessao::write('modulo', "0");

// Istancia objetos das classes utilizadas
$rsMunicipios     = new RecordSet;
$rsUF             = new RecordSet;
$obRCIMLogradouro = new RCIMLogradouro;

if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "incluir";
}

Sessao::remove('link');
Sessao::remove('stLink');

// Preenche RecordSet
$obRCIMLogradouro->listarUF( $rsUF, "", $_REQUEST["inCodPais"] );

$obRCIMConfiguracao = new RCIMConfiguracao;
$obRCIMConfiguracao->listaDadosMunicipio( $arConfiguracao );

$boAchou = false;
while ( !$rsUF->eof() ) {
    if ( $rsUF->getCampo("cod_uf") == $arConfiguracao["cod_uf"] ) {
        $boAchou = true;
        break;
    }

    $rsUF->proximo();
}

$rsUF->setPrimeiroElemento();

if ( $_REQUEST["inCodigoUF"] )
    $_REQUEST['inCodigoUF'] = $_REQUEST["inCodigoUF"];
else
if ($boAchou)
    $_REQUEST['inCodigoUF'] = $arConfiguracao["cod_uf"];
else
    $_REQUEST['inCodigoUF'] = "";

if ( $_REQUEST["inCodigoMunicipio"] )
    $_REQUEST['inCodigoMunicipio'] = $_REQUEST["inCodigoMunicipio"];
else
if ($boAchou) {
    $_REQUEST['inCodigoMunicipio'] = $arConfiguracao["cod_municipio"];
} else {
    $_REQUEST['inCodigoMunicipio'] = "";
}

if ($_REQUEST['inCodigoUF']) {
    $obRCIMLogradouro->setCodigoUF( $_REQUEST['inCodigoUF'] );
    $obRCIMLogradouro->listarMunicipios( $rsMunicipios );
}

// DefiniÃ§Ã£o dos objetos para o formulÃ¡rio
$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $_REQUEST['stAcao'] );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );

$obHdnPais = new Hidden;
$obHdnPais->setName  ( "inCodPais" );
$obHdnPais->setValue ( $_REQUEST["inCodPais"] );

$obHdnCadastro = new Hidden;
$obHdnCadastro->setName  ( "stCadastro"            );
$obHdnCadastro->setValue ( $_REQUEST["stCadastro"] );

$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $_REQUEST['campoNom'] );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $_REQUEST['campoNum'] );

$obIFrame = new IFrame;
$obIFrame->setName   ( "telaMensagem");
$obIFrame->setWidth  ( "100%"        );
$obIFrame->setHeight ( "50"          );

$obIFrame2 = new IFrame;
$obIFrame2->setName   ( "oculto" );
$obIFrame2->setWidth  ( "100%"   );
$obIFrame2->setHeight ( "0"      );

$obTxtNome = new TextBox;
$obTxtNome->setRotulo         ( "Nome"             );
$obTxtNome->setName           ( "stNomeLogradouro" );
$obTxtNome->setValue          ( $_REQUEST['stNomeLogradouro'] );
$obTxtNome->setSize           ( 80                 );
$obTxtNome->setMaxLength      ( 80                 );
$obTxtNome->setNull           ( true               );

//Componente que define o tipo de busca
$obTipoBuscaLogradouro = new TipoBusca( $obTxtNome );

$obTxtCEP = new TextBox;
$obTxtCEP->setRotulo    ( "CEP"  );
$obTxtCEP->setName      ( "stCEP" );
$obTxtCEP->setId        ( "stCEP" );
$obTxtCEP->setValue     ( $_REQUEST['stCEP'] );
$obTxtCEP->setSize      ( 8 );
$obTxtCEP->setInteiro   ( true );
$obTxtCEP->setMaxLength ( 8 );
$obTxtCEP->setNull      ( true );
$obTxtCEP->obEvento->setOnChange('desabilitaCampos();');

$obTxtCodUF = new TextBox;
$obTxtCodUF->setRotulo             ( "Estado"                );
$obTxtCodUF->setName               ( "inCodigoUF"            );
$obTxtCodUF->setId                 ( "inCodigoUF"            );
$obTxtCodUF->setValue              ( $_REQUEST['inCodigoUF'] );
$obTxtCodUF->setSize               ( 8                       );
$obTxtCodUF->setMaxLength          ( 8                       );
$obTxtCodUF->setNull               ( false                   );
$obTxtCodUF->obEvento->setOnChange ( "preencheMunicipio('')" );

$obCmbUF = new Select;
$obCmbUF->setName               ( "inCodUF"               );
$obCmbUF->setId                  ( "inCodUF"               );
$obCmbUF->addOption             ( "", "Selecione"         );
$obCmbUF->setCampoId            ( "cod_uf"                );
$obCmbUF->setCampoDesc          ( "nom_uf"                );
$obCmbUF->preencheCombo         ( $rsUF                   );
$obCmbUF->setValue              ( $_REQUEST['inCodigoUF'] );
$obCmbUF->setNull               ( false                   );
//$obCmbUF->setStyle              ( "width: 220px"          );
$obCmbUF->obEvento->setOnChange ( "preencheMunicipio('')" );

$obTxtCodMunicipio = new TextBox;
$obTxtCodMunicipio->setRotulo    ( "Munic&iacute;pio"  );
$obTxtCodMunicipio->setName      ( "inCodigoMunicipio" );
$obTxtCodMunicipio->setId        ( "inCodigoMunicipio" );
$obTxtCodMunicipio->setValue     ( $_REQUEST['inCodigoMunicipio']  );
$obTxtCodMunicipio->setSize      ( 8                   );
$obTxtCodMunicipio->setMaxLength ( 8                   );
$obTxtCodMunicipio->setNull      ( false               );
$obTxtCodMunicipio->obEvento->setOnChange ( "preencheBairro('')" );

$obCmbMunicipio = new Select;
$obCmbMunicipio->setName       ( "inCodMunicipio"   );
$obCmbMunicipio->setId         ( "inCodMunicipio"   );
$obCmbMunicipio->addOption     ( "", "Selecione"    );
$obCmbMunicipio->setCampoId    ( "cod_municipio"    );
$obCmbMunicipio->setCampoDesc  ( "nom_municipio"    );
$obCmbMunicipio->setValue      ( $_REQUEST['inCodigoMunicipio'] );
$obCmbMunicipio->preencheCombo ( $rsMunicipios      );
$obCmbMunicipio->setNull       ( false              );
$obCmbMunicipio->obEvento->setOnChange ( "preencheBairro('')" );

$obRCIMBairro = new RCIMBairro;
$obRCIMBairro->setCodigoUF( $_REQUEST['inCodigoUF'] );
$obRCIMBairro->setCodigoMunicipio( $_REQUEST['inCodigoMunicipio'] );
$obRCIMBairro->listarBairros( $rsBairros );

$obTxtCodBairro = new TextBox;
$obTxtCodBairro->setRotulo    ( "Bairro"                    );
$obTxtCodBairro->setName      ( "inCodigoBairro"            );
$obTxtCodBairro->setId        ( "inCodigoBairro"            );
$obTxtCodBairro->setValue     ( $_REQUEST["inCodigoBairro"] );
$obTxtCodBairro->setSize      ( 8                           );
$obTxtCodBairro->setMaxLength ( 8                           );
$obTxtCodBairro->setInteiro   ( true                        );
$obTxtCodBairro->setNull      ( true                        );

$obCmbBairro = new Select;
$obCmbBairro->setName       ( "inCodBairro"               );
$obCmbBairro->setId         ( "inCodBairro"               );
$obCmbBairro->addOption     ( "", "Selecione"             );
$obCmbBairro->setCampoId    ( "cod_bairro"                );
$obCmbBairro->setCampoDesc  ( "nom_bairro"                );
$obCmbBairro->setValue      ( $_REQUEST["inCodigoBairro"] );
$obCmbBairro->preencheCombo ( $rsBairros                  );
$obCmbBairro->setStyle      ( "width: 220px"              );
$obCmbBairro->setNull       ( true                        );

//DEFINICAO DA BARRA DE BOTOES
$obBtnFechar = new Button;
$obBtnFechar->setName    ( "stFechar" );
$obBtnFechar->setValue   ( "Fechar"   );
$obBtnFechar->obEvento->setOnClick ( "fechar()" );

$obBtnIncluir = new Button;
$obBtnIncluir->setName              ( "btnIncluir"   );
$obBtnIncluir->setValue             ( "Incluir Novo" );
$obBtnIncluir->setTipo              ( "button"       );
$obBtnIncluir->obEvento->setOnClick ( "incluir();"   );
$obBtnIncluir->setDisabled          ( false          );

$obOk  = new Ok;

$obLimpar  = new Limpar;

$arBotoes = array( $obOk, $obLimpar, $obBtnIncluir );

//DEFINICAO DO FORMULARIO
$obForm = new Form;
$obForm->setAction            ( $pgList );

$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm        );
$obFormulario->addHidden     ( $obHdnAcao     );
$obFormulario->addHidden     ( $obHdnCtrl     );
$obFormulario->addHidden     ( $obHdnPais     );
$obFormulario->addHidden     ( $obHdnCampoNom );
$obFormulario->addHidden     ( $obHdnCampoNum );
$obFormulario->addHidden     ( $obHdnCadastro );
$obFormulario->addTitulo     ( "Dados para filtro" );
//$obFormulario->addComponente ( $obTxtCodigo   );
$obFormulario->addComponente ( $obTxtCEP );
$obFormulario->addComponente ( $obTipoBuscaLogradouro );
$obFormulario->addComponenteComposto ( $obTxtCodUF, $obCmbUF );
$obFormulario->addComponenteComposto ( $obTxtCodMunicipio, $obCmbMunicipio );
$obFormulario->addComponenteComposto ( $obTxtCodBairro, $obCmbBairro );
$obFormulario->defineBarra( $arBotoes, "left" );
//$obFormulario->Ok();
$obFormulario->show();
$obIFrame->show();
$obIFrame2->show();

Sessao::write('acao'  , $acao);
Sessao::write('modulo', $modulo);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
