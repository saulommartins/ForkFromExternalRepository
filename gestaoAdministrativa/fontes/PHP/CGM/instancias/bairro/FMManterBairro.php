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

    * Página de formulário para o cadastro de bairro
    * Data de Criação   : 24/09/2004

    * @author Analista: Gelson Wolowski Gonçalves
    * @author Desenvolvedor: Evandro Melos
    * $Id: $
    * Casos de uso: uc-05.01.05
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

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

Sessao::remove('link');
if ($request->get("stNomeLogradouro") or $request->get("inCodigoTipo") ) {
    Sessao::write('stNomeLogradouro', $request->get("stNomeLogradouro") );
    Sessao::write('inCodigoTipo', $request->get("inCodigoTipo") );
}

// OBJETOS HIDDEN
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obHdnReqLogr = new Hidden;
$obHdnReqLogr->setName  ( "stReqLogr" );
$obHdnReqLogr->setValue ( $stReqLogr  );

$obHdnCodBairro = new Hidden;
$obHdnCodBairro->setName  ( "hdnCodigoBairro"        );
$obHdnCodBairro->setValue ( $request->get("inCodBairro") );

$obHdnCodUF = new Hidden;
$obHdnCodUF->setName      ( "inCodUF"                );
$obHdnCodUF->setValue     ( $request->get("inCodUF")     );

$obHdnCodMunicipio = new Hidden;
$obHdnCodMunicipio->setName  ( "inCodMunicipio"            );
$obHdnCodMunicipio->setValue ( $request->get("inCodMunicipio") );

$obHdnCampoNome = new Hidden;
$obHdnCampoNome->setName  ( "campoNom"            );
$obHdnCampoNome->setValue ( $request->get("campoNom") );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName  ( "campoNum"            );
$obHdnCampoNum->setValue ( $request->get("campoNum") );

// DEFINE OBJETOS DO FORMULARIO - INCLUIR
$obBotaoFechar = new Button;
$obBotaoFechar->setName                 ( "botaoFechar" );
$obBotaoFechar->setValue                ( "Fechar"      );
$obBotaoFechar->obEvento->setOnClick    ( "fechar();"   );

$obTxtNomeBairro = new TextBox;
$obTxtNomeBairro->setRotulo    ( "Nome do Bairro"          );
$obTxtNomeBairro->setName      ( "stNomeBairro"            );
$obTxtNomeBairro->setId        ( "stNomeBairro"            );
$obTxtNomeBairro->setValue     ( $request->get("stNomeBairro") );
$obTxtNomeBairro->setSize      ( 30                        );
$obTxtNomeBairro->setMaxLength ( 30                        );
$obTxtNomeBairro->setNull      ( false                     );

$obTxtCodUF = new TextBox;
$obTxtCodUF->setRotulo             ( "Estado"               );
$obTxtCodUF->setName               ( "inCodUF"              );
$obTxtCodUF->setId                 ( "inCodUF"              );
$obTxtCodUF->setValue              ( $request->get("inCodUF")   );
$obTxtCodUF->setSize               ( 8                      );
$obTxtCodUF->setMaxLength          ( 8                      );
$obTxtCodUF->setNull               ( false                  );
$obTxtCodUF->setInteiro            ( true                   );
$obTxtCodUF->obEvento->setOnChange ( " montaParametrosGET('preencheMunicipio'); " );

$obCmbUF = new Select;
$obCmbUF->setName               ( "cmbUF"                );
$obCmbUF->setId                 ( "cmbUF"                );
$obCmbUF->addOption             ( "", "Selecione"        );
$obCmbUF->setCampoId            ( "cod_uf"               );
$obCmbUF->setCampoDesc          ( "nom_uf"               );
$obCmbUF->setValue              ( $request->get("inCodUF")   );
$obCmbUF->setNull               ( false                  );
$obCmbUF->setStyle              ( "width: 220px"         );
$obCmbUF->obEvento->setOnChange ( " montaParametrosGET('preencheMunicipio'); " );

$obTxtCodMunicipio = new TextBox;
$obTxtCodMunicipio->setRotulo    ( "Município"                 );
$obTxtCodMunicipio->setName      ( "inCodMunicipio"            );
$obTxtCodMunicipio->setId        ( "inCodMunicipio"            );
$obTxtCodMunicipio->setValue     ( $request->get("inCodMunicipio") );
$obTxtCodMunicipio->setSize      ( 8                           );
$obTxtCodMunicipio->setMaxLength ( 8                           );
$obTxtCodMunicipio->setNull      ( false                       );
$obTxtCodMunicipio->setInteiro   ( true                        );

$obCmbMunicipio = new Select;
$obCmbMunicipio->setName       ( "cmbMunicipio"              );
$obCmbMunicipio->setId         ( "cmbMunicipio"              );
$obCmbMunicipio->addOption     ( "", "Selecione"             );
$obCmbMunicipio->setCampoId    ( "cod_municipio"             );
$obCmbMunicipio->setCampoDesc  ( "nom_municipio"             );
$obCmbMunicipio->setValue      ( $request->get("inCodMunicipio") );
$obCmbMunicipio->setNull       ( false                       );
$obCmbMunicipio->setStyle      ( "width: 220px"              );

//DEFINICAO DOS OBJETOS DO FORMULARIO - ALTERAR - CONSULTAR
$obLblCodBairro = new Label;
$obLblCodBairro->setRotulo ( "Código"                 );
$obLblCodBairro->setName   ( "inCodBairro"            );
$obLblCodBairro->setId     ( "inCodBairro"            );
$obLblCodBairro->setValue  ( $request->get("inCodBairro") );

$obLblNomeUF = new Label;
$obLblNomeUF->setRotulo   ( "Estado"              );
$obLblNomeUF->setName     ( "stNomeEstado"        );
$obLblNomeUF->setId       ( "stNomeEstado"        );
$obLblNomeUF->setValue    ( $request->get("stNomeUF") );

$obLblNomeMunicipio = new Label;
$obLblNomeMunicipio->setRotulo ( "Município"           );
$obLblNomeMunicipio->setName   ( "stNomeMunicipio"            );
$obLblNomeMunicipio->setId     ( "stNomeMunicipio"            );
$obLblNomeMunicipio->setValue  ( $request->get("stNomeMunicipio") );

$obLblNomeBairro = new Label;
$obLblNomeBairro->setRotulo ( "Nome do Bairro"          );
$obLblNomeBairro->setName   ( "stNomeBairro"            );
$obLblNomeBairro->setId     ( "stNomeBairro"            );
$obLblNomeBairro->setValue  ( $request->get("stNomeBairro") );

$obBtnOk = new OK;
$obBtnLimpar = new Limpar;
$obBtnCancelar = new Cancelar;

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm   ( $obForm         );
$obFormulario->addHidden ( $obHdnCtrl      );
$obFormulario->addHidden ( $obHdnAcao      );
$obFormulario->addHidden ( $obHdnReqLogr   );
$obFormulario->addHidden ( $obHdnCodBairro );
$obFormulario->addTitulo ( "Dados para Bairro" );

switch ($stAcao) {
    case 'incluir':
        $jsOnload = "montaParametrosGET('carregarDadosIncluir');";
        $arBotaoAcao = array( $obBtnOk, $obBtnLimpar );
        $obFormulario->addComponenteComposto ( $obTxtCodUF, $obCmbUF               );
        $obFormulario->addComponenteComposto ( $obTxtCodMunicipio, $obCmbMunicipio );
        $obFormulario->addComponente         ( $obTxtNomeBairro                    );    
    break;
    
    case 'alterar':
        $jsOnload = "montaParametrosGET('carregarDadosAlterar');";
        $arBotaoAcao = array( $obBtnOk, $obBtnCancelar );
        $obFormulario->addHidden ( $obHdnCodMunicipio );
        $obFormulario->addHidden ( $obHdnCodUF        );
        $obFormulario->addHidden ( $obHdnCampoNome  );
        $obFormulario->addHidden ( $obHdnCampoNum   );
        
        $obFormulario->addComponente ( $obLblCodBairro     );
        $obFormulario->addComponente ( $obTxtNomeBairro    );
        $obFormulario->addComponente ( $obLblNomeUF        );
        $obFormulario->addComponente ( $obLblNomeMunicipio );
    break;

    case 'consultar':
        $jsOnload = "montaParametrosGET('carregarDadosConsultar');";
        $obBtnCancelar->setValue("Ok");
        $arBotaoAcao = array( $obBtnCancelar );
        $obFormulario->addHidden ( $obHdnCodMunicipio );
        $obFormulario->addHidden ( $obHdnCodUF        );
        $obFormulario->addHidden ( $obHdnCampoNome  );
        $obFormulario->addHidden ( $obHdnCampoNum   );
        
        $obFormulario->addComponente ( $obLblCodBairro     );
        $obFormulario->addComponente ( $obLblNomeBairro    );
        $obFormulario->addComponente ( $obLblNomeUF        );
        $obFormulario->addComponente ( $obLblNomeMunicipio );
    break;
}

$obFormulario->defineBarra( $arBotaoAcao );
$obFormulario->show ();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
