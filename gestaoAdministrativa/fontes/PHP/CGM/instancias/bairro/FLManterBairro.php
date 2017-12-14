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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLogradouro.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMBairro.class.php"     );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterBairro";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";
include_once( $pgJs );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

if ($request->get("stNomeLogradouro")) {
    Sessao::write('stNomeLogradouro', $request->get("stNomeLogradouro"));
    Sessao::write('inCodigoTipo'    , $request->get("inCodigoTipo"));
}

Sessao::remove('link');

// OBJETOS HIDDEN
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setId    ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setId    ( "stAcao" );
$obHdnAcao->setValue ( $stAcao );

$obHdnCodUF = new Hidden;
$obHdnCodUF->setName  ( "hdnCodUF" );
$obHdnCodUF->setId    ( "hdnCodUF" );
$obHdnCodUF->setValue ( $inCodUF   );

$obHdnCodMunicipio = new Hidden;
$obHdnCodMunicipio->setName  ( "hdnCodMunicipio" );
$obHdnCodMunicipio->setId    ( "hdnCodMunicipio" );
$obHdnCodMunicipio->setValue ( $inCodMunicipio   );

// DEFINE OBJETOS DO FORMULARIO
$obTxtCodigoBairro = new TextBox;
$obTxtCodigoBairro->setRotulo    ( "Código"                 );
$obTxtCodigoBairro->setName      ( "inCodigoBairro"         );
$obTxtCodigoBairro->setId        ( "inCodigoBairro"         );
$obTxtCodigoBairro->setValue     ( $request->get("inCodBairro") );
$obTxtCodigoBairro->setSize      ( 8                        );
$obTxtCodigoBairro->setMaxLength ( 8                        );
$obTxtCodigoBairro->setNull      ( true                     );
$obTxtCodigoBairro->setInteiro   ( true                     );

$obTxtNomeBairro = new TextBox;
$obTxtNomeBairro->setRotulo    ( "Nome"                   );
$obTxtNomeBairro->setName      ( "stNomBairro"            );
$obTxtNomeBairro->setId        ( "stNomBairro"            );
$obTxtNomeBairro->setValue     ( $request->get("stNomBairro") );
$obTxtNomeBairro->setSize      ( 40                       );
$obTxtNomeBairro->setMaxLength ( 40                       );
$obTxtNomeBairro->setNull      ( true                     );

$obTxtCodEstado = new TextBox;
$obTxtCodEstado->setRotulo             ( "Estado"               );
$obTxtCodEstado->setName               ( "inCodUF"              );
$obTxtCodEstado->setId                 ( "inCodUF"              );
$obTxtCodEstado->setValue              ( $inCodUF               );
$obTxtCodEstado->setSize               ( 8                      );
$obTxtCodEstado->setMaxLength          ( 8                      );
$obTxtCodEstado->setNull               ( false                  );
$obTxtCodEstado->setInteiro            ( true                   );
$obTxtCodEstado->obEvento->setOnChange ( "montaParametrosGET('preencheMunicipio');" );

$obCmbEstado = new Select;
$obCmbEstado->setName               ( "cmbUF"                );
$obCmbEstado->setId                 ( "cmbUF"                );
$obCmbEstado->addOption             ( "", "Selecione"        );
$obCmbEstado->setCampoId            ( "cod_uf"               );
$obCmbEstado->setCampoDesc          ( "nom_uf"               );
$obCmbEstado->setValue              ( $inCodUF               );
$obCmbEstado->setNull               ( false                  );
$obCmbEstado->setStyle              ( "width: 220px"         );
$obCmbEstado->obEvento->setOnChange ( "montaParametrosGET('preencheMunicipio');" );

$obTxtCodMunicipio = new TextBox;
$obTxtCodMunicipio->setRotulo    ( "Município" );
$obTxtCodMunicipio->setName      ( "inCodMunicipio"   );
$obTxtCodMunicipio->setId        ( "inCodMunicipio"   );
$obTxtCodMunicipio->setValue     ( $inCodMunicipio    );
$obTxtCodMunicipio->setSize      ( 8                  );
$obTxtCodMunicipio->setMaxLength ( 8                  );
$obTxtCodMunicipio->setNull      ( false              );
$obTxtCodMunicipio->setInteiro   ( true               );

$obCmbMunicipio = new Select;
$obCmbMunicipio->setName       ( "cmbMunicipio"  );
$obCmbMunicipio->setId         ( "cmbMunicipio"  );
$obCmbMunicipio->addOption     ( "", "Selecione" );
$obCmbMunicipio->setCampoId    ( "cod_municipio" );
$obCmbMunicipio->setCampoDesc  ( "nom_municipio" );
$obCmbMunicipio->setValue      ( $inCodMunicipio );
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
$obForm->setAction( $pgList );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm               ( $obForm        );
$obFormulario->addHidden             ( $obHdnCtrl     );
$obFormulario->addHidden             ( $obHdnAcao     );
$obFormulario->addTitulo             ( "Dados para filtro" );
$obFormulario->addComponente         ( $obTxtCodigoBairro                  );
$obFormulario->addComponente         ( $obTxtNomeBairro                    );
$obFormulario->addComponenteComposto ( $obTxtCodEstado, $obCmbEstado       );
$obFormulario->addComponenteComposto ( $obTxtCodMunicipio, $obCmbMunicipio );

$obFormulario->Ok();
$obFormulario->show();

switch ($stAcao) {
    case 'alterar':
    case 'consultar':
    case 'excluir':
        $jsOnload = "montaParametrosGET('carregarDadosAlterar');";
    break;
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
