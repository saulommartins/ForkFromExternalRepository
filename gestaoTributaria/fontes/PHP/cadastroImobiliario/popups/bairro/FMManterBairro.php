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
    * Página de formulário para o cadastro de bairro
    * Data de Criação   : 24/09/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues

    * @ignore

    * $Id: FMManterBairro.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.05
*/

/*
$Log$
Revision 1.5  2006/10/27 18:38:42  dibueno
Correção Bug #7274

Revision 1.4  2006/09/15 15:03:47  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLogradouro.class.php"            );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMBairro.class.php"                );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php");
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMBairroAliquota.class.php");
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMBairroValorM2.class.php");

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
if ($_REQUEST["stCtrl"] == "incluirNovoBairro") {
    $stReqLogr = "&stAcaoLogr=".$stAcao."&inCodigoLogradouro=".$_REQUEST["inCodigoLogradouro"]."&stNomeAntigo=".$_REQUEST["hdnNomeAntigo"];
    $stAcao = "incluir";
} elseif ( empty( $stAcao ) || $stAcao == "renomear" ) {
    $stAcao = "incluir";
}

Sessao::write('sessao_transf4');
Sessao::remove('link');

if ($_REQUEST["stNomeLogradouro"] or $_REQUEST["inCodigoTipo"]) {
    Sessao::write('stNomeLogradouro', $_REQUEST["stNomeLogradouro"]);
    Sessao::write('inCodigoTipo', $_REQUEST["inCodigoTipo"]);
}

// DEFINE OBJETOS DAS CLASSES
$rsUFs         = new RecordSet;
$rsMunicipios  = new RecordSet;
$obRCIMBairro  = new RCIMBairro;

// Preenche RecordSet
$obRCIMBairro->listarUF( $rsUFs );

if ($stAcao == "incluir") {
    $obRCIMBairro->setCodigoUF ( $_REQUEST["inCodUF"] );
    $obRCIMBairro->listarMunicipios( $rsMunicipios );
}

$obRCIMConfiguracao = new RCIMConfiguracao;
$obRCIMConfiguracao->consultarConfiguracao();
$rsMDSelecionados = $obRCIMConfiguracao->getRSMD();
$boM2Ativo = false;
while ( !$rsMDSelecionados->Eof() ) {
    if ( $rsMDSelecionados->getCampo( "nome" ) == "Bairro" ) {
        $boM2Ativo = true;
        break;
    }

    $rsMDSelecionados->proximo();
}

$rsAliquotaSelecionados = $obRCIMConfiguracao->getRSAliquota();
$boAliquotaAtivo = false;
while ( !$rsAliquotaSelecionados->Eof() ) {
    if ( $rsAliquotaSelecionados->getCampo( "nome" ) == "Bairro" ) {
        $boAliquotaAtivo = true;
        break;
    }

    $rsAliquotaSelecionados->proximo();
}

//valores m2-------------------
if ($boM2Ativo) {
    $obTCIMBairroValorM2 = new TCIMBairroValorM2;

    if ($_REQUEST["inCodMunicipio"] && $_REQUEST["inCodUF"] && $_REQUEST["inCodBairro"]) {
        $stFiltro = " AND bairro_valor_m2.cod_municipio = ".$_REQUEST["inCodMunicipio"]." AND bairro_valor_m2.cod_uf = ".$_REQUEST["inCodUF"]." AND bairro_valor_m2.cod_bairro = ".$_REQUEST["inCodBairro"];
        $obTCIMBairroValorM2->listaBairroValorM2( $rsDadosM2, $stFiltro );
        $rsDadosM2->addFormatacao ('valor_m2_territorial', 'NUMERIC_BR');
        $rsDadosM2->addFormatacao ('valor_m2_predial', 'NUMERIC_BR');
    } else {
        $rsDadosM2 = new RecordSet;
    }

    $obTxtValorTerritorial = new Moeda;
    $obTxtValorTerritorial->setName ( "flValorTerritorial" );
    $obTxtValorTerritorial->setTitle ( "Valor por metro quadrado territorial.");
    $obTxtValorTerritorial->setRotulo ( "Valor Territorial" );
    $obTxtValorTerritorial->setValue ( $rsDadosM2->getCampo( "valor_m2_territorial" ) );
    $obTxtValorTerritorial->setNull ( false );

    $obTxtValorPredial = new Moeda;
    $obTxtValorPredial->setName ( "flValorPredial" );
    $obTxtValorPredial->setTitle ( "Valor por metro quadrado predial" );
    $obTxtValorPredial->setRotulo ( "Valor Predial" );
    $obTxtValorPredial->setValue ( $rsDadosM2->getCampo( "valor_m2_predial" ) );
    $obTxtValorPredial->setNull ( false );

    $obTxtInicioVigencia = new Data;
    $obTxtInicioVigencia->setName ( "dtVigenciaMD" );
    $obTxtInicioVigencia->setTitle ( "Data de início de vigência dos valores." );
    $obTxtInicioVigencia->setRotulo ( "Início Vigência" );
    $obTxtInicioVigencia->setValue ( $rsDadosM2->getCampo( "dt_vigencia" ) );
    $obTxtInicioVigencia->setNull ( false );

    $obBscFundamentacao = new BuscaInner;
    $obBscFundamentacao->setTitle            ( "Norma que regulamenta as alíquotas." );
    $obBscFundamentacao->setNull             ( false                   );
    $obBscFundamentacao->setRotulo           ( "Fundamentação Legal"   );
    $obBscFundamentacao->setId               ( "stFundamentacao" );
    $obBscFundamentacao->setValue            ( $rsDadosM2->getCampo( "nom_norma" )  );
    $obBscFundamentacao->obCampoCod->setName ( "inCodigoFundamentacao" );
    $obBscFundamentacao->obCampoCod->setValue( $rsDadosM2->getCampo( "cod_norma" )  );
    $obBscFundamentacao->obCampoCod->setSize ( 9                       );
    $obBscFundamentacao->obCampoCod->obEvento->setOnChange( "buscaDado('buscaLegal');" );
    $obBscFundamentacao->setFuncaoBusca ( "abrePopUp('".CAM_GA_ADM_POPUPS."../../normas/popups/normas/FLNorma.php','frm','inCodigoFundamentacao','stFundamentacao','todos','".Sessao::getId()."','800','550');" );
}
//----

//aliquota------------
if ($boAliquotaAtivo) {
    $obTCIMBairroAliquota = new TCIMBairroAliquota;

    if ($_REQUEST["inCodMunicipio"] && $_REQUEST["inCodUF"] && $_REQUEST["inCodBairro"]) {
        $stFiltro = " AND bairro_aliquota.cod_municipio = ".$_REQUEST["inCodMunicipio"]." AND bairro_aliquota.cod_uf = ".$_REQUEST["inCodUF"]." AND bairro_aliquota.cod_bairro = ".$_REQUEST["inCodBairro"];
        $obTCIMBairroAliquota->listaBairroAliquota( $rsDadosM2, $stFiltro );
        $rsDadosM2->addFormatacao ('aliquota_territorial', 'NUMERIC_BR');
        $rsDadosM2->addFormatacao ('aliquota_predial', 'NUMERIC_BR');
    } else {
        $rsDadosM2 = new RecordSet;
    }

    $obTxtAliquotaTerritorial = new Moeda;
    $obTxtAliquotaTerritorial->setName ( "flAliquotaTerritorial" );
    $obTxtAliquotaTerritorial->setTitle ( "Alíquota territorial.");
    $obTxtAliquotaTerritorial->setRotulo ( "Alíquota Territorial" );
    $obTxtAliquotaTerritorial->setValue ( $rsDadosM2->getCampo( "aliquota_territorial" ) );
    $obTxtAliquotaTerritorial->setNull ( false );

    $obTxtAliquotaPredial = new Moeda;
    $obTxtAliquotaPredial->setName ( "flAliquotaPredial" );
    $obTxtAliquotaPredial->setTitle ( "Alíquota predial.");
    $obTxtAliquotaPredial->setRotulo ( "Alíquota Predial" );
    $obTxtAliquotaPredial->setValue ( $rsDadosM2->getCampo( "aliquota_predial" ) );
    $obTxtAliquotaPredial->setNull ( false );

    $obTxtInicioVigenciaAliquota = new Data;
    $obTxtInicioVigenciaAliquota->setName ( "dtVigenciaAliquota" );
    $obTxtInicioVigenciaAliquota->setTitle ( "Data de início de vigência dos valores." );
    $obTxtInicioVigenciaAliquota->setRotulo ( "Início Vigência" );
    $obTxtInicioVigenciaAliquota->setValue ( $rsDadosM2->getCampo( "dt_vigencia" ) );
    $obTxtInicioVigenciaAliquota->setNull ( false );

    $obBscFundamentacaoAliquota = new BuscaInner;
    $obBscFundamentacaoAliquota->setTitle            ( "Norma que regulamenta as alíquotas." );
    $obBscFundamentacaoAliquota->setNull             ( false                   );
    $obBscFundamentacaoAliquota->setRotulo           ( "Fundamentação Legal"   );
    $obBscFundamentacaoAliquota->setId               ( "stFundamentacaoAliquota" );
    $obBscFundamentacaoAliquota->setValue            ( $rsDadosM2->getCampo( "nom_norma" ) );
    $obBscFundamentacaoAliquota->obCampoCod->setName ( "inCodigoFundamentacaoAliquota" );
    $obBscFundamentacaoAliquota->obCampoCod->setValue( $rsDadosM2->getCampo( "cod_norma" ) );
    $obBscFundamentacaoAliquota->obCampoCod->setSize ( 9                       );
    $obBscFundamentacaoAliquota->obCampoCod->obEvento->setOnChange( "buscaDado('buscaLegalAliquota');" );
    $obBscFundamentacaoAliquota->setFuncaoBusca ( "abrePopUp('".CAM_GA_ADM_POPUPS."../../normas/popups/normas/FLNorma.php','frm','inCodigoFundamentacaoAliquota','stFundamentacaoAliquota','todos','".Sessao::getId()."','800','550');" );
}
//------------------

$obHdnAliquota = new Hidden;
$obHdnAliquota->setName   ( 'boAliquotaAtivo' );
$obHdnAliquota->setValue  ( $boAliquotaAtivo );

$obHdnMD = new Hidden;
$obHdnMD->setName   ( 'boM2Ativo' );
$obHdnMD->setValue  ( $boM2Ativo );

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
$obHdnCodBairro->setValue ( $_REQUEST["inCodBairro"] );

$obHdnCodUF = new Hidden;
$obHdnCodUF->setName      ( "inCodUF"                );
$obHdnCodUF->setValue     ( $_REQUEST["inCodUF"]     );

$obHdnCodMunicipio = new Hidden;
$obHdnCodMunicipio->setName  ( "inCodMunicipio"            );
$obHdnCodMunicipio->setValue ( $_REQUEST["inCodMunicipio"] );

$obHdnCampoNome = new Hidden;
$obHdnCampoNome->setName  ( "campoNom"            );
$obHdnCampoNome->setValue ( $_REQUEST["campoNom"] );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName  ( "campoNum"            );
$obHdnCampoNum->setValue ( $_REQUEST["campoNum"] );

// DEFINE OBJETOS DO FORMULARIO - INCLUIR
$obBotaoFechar = new Button;
$obBotaoFechar->setName                 ( "botaoFechar" );
$obBotaoFechar->setValue                ( "Fechar"      );
$obBotaoFechar->obEvento->setOnClick    ( "fechar();"   );

$obTxtNomeBairro = new TextBox;
$obTxtNomeBairro->setRotulo    ( "Nome"                    );
$obTxtNomeBairro->setName      ( "stNomeBairro"            );
$obTxtNomeBairro->setValue     ( $_REQUEST["stNomeBairro"] );
$obTxtNomeBairro->setSize      ( 30                        );
$obTxtNomeBairro->setMaxLength ( 30                        );
$obTxtNomeBairro->setNull      ( false                     );

$obTxtCodUF = new TextBox;
$obTxtCodUF->setRotulo             ( "Estado"               );
$obTxtCodUF->setName               ( "inCodUF"              );
$obTxtCodUF->setValue              ( $_REQUEST["inCodUF"]   );
$obTxtCodUF->setSize               ( 8                      );
$obTxtCodUF->setMaxLength          ( 8                      );
$obTxtCodUF->setNull               ( false                  );
$obTxtCodUF->setInteiro            ( true                   );
$obTxtCodUF->obEvento->setOnChange ( "preencheMunicipio('');" );

$obCmbUF = new Select;
$obCmbUF->setName               ( "cmbUF"                );
$obCmbUF->addOption             ( "", "Selecione"        );
$obCmbUF->setCampoId            ( "cod_uf"               );
$obCmbUF->setCampoDesc          ( "nom_uf"               );
$obCmbUF->preencheCombo         ( $rsUFs                 );
$obCmbUF->setValue              ( $_REQUEST["inCodUF"]   );
$obCmbUF->setNull               ( false                  );
$obCmbUF->setStyle              ( "width: 220px"         );
$obCmbUF->obEvento->setOnChange ( "preencheMunicipio('');" );

$obTxtCodMunicipio = new TextBox;
$obTxtCodMunicipio->setRotulo    ( "Munic&iacute;pio"          );
$obTxtCodMunicipio->setName      ( "inCodMunicipio"            );
$obTxtCodMunicipio->setValue     ( $_REQUEST["inCodMunicipio"] );
$obTxtCodMunicipio->setSize      ( 8                           );
$obTxtCodMunicipio->setMaxLength ( 8                           );
$obTxtCodMunicipio->setNull      ( false                       );
$obTxtCodMunicipio->setInteiro   ( true                        );

$obCmbMunicipio = new Select;
$obCmbMunicipio->setName       ( "cmbMunicipio"              );
$obCmbMunicipio->addOption     ( "", "Selecione"             );
$obCmbMunicipio->setCampoId    ( "cod_municipio"             );
$obCmbMunicipio->setCampoDesc  ( "nom_municipio"             );
$obCmbMunicipio->setValue      ( $_REQUEST["inCodMunicipio"] );
$obCmbMunicipio->preencheCombo ( $rsMunicipios               );
$obCmbMunicipio->setNull       ( false                       );
$obCmbMunicipio->setStyle      ( "width: 220px"              );

//DEFINICAO DOS OBJETOS DO FORMULARIO - ALTERAR
$obLblCodBairro = new Label;
$obLblCodBairro->setRotulo ( "C&oacute;digo"          );
$obLblCodBairro->setName   ( "inCodBairro"            );
$obLblCodBairro->setValue  ( $_REQUEST["inCodBairro"] );

$obLblNomeUF = new Label;
$obLblNomeUF->setRotulo   ( "Estado"              );
$obLblNomeUF->setName     ( "stNomeEstado"        );
$obLblNomeUF->setValue    ( $_REQUEST["stNomeUF"] );

$obLblNomeMunicipio = new Label;
$obLblNomeMunicipio->setRotulo ( "Munic&iacute;pio"           );
$obLblNomeMunicipio->setName   ( "stNomeMunicipio"            );
$obLblNomeMunicipio->setValue  ( $_REQUEST["stNomeMunicipio"] );

$obBtnOk = new OK;

$obBtnLimpar = new Limpar;

$obBtnCancelar = new Cancelar;

$arBotaoAcao = array( $obBtnOk, $obBtnLimpar, $obBtnCancelar );

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
$obFormulario->addHidden ( $obHdnAliquota      );
$obFormulario->addHidden ( $obHdnMD            );

$obFormulario->addTitulo   ( "Dados para Bairro" );

if ($stAcao == "incluir") {
    $obFormulario->addComponente         ( $obTxtNomeBairro                    );
    $obFormulario->addComponenteComposto ( $obTxtCodUF, $obCmbUF               );
    $obFormulario->addComponenteComposto ( $obTxtCodMunicipio, $obCmbMunicipio );
} elseif ($stAcao == "alterar") {
    $obFormulario->addHidden ( $obHdnCodMunicipio );
    $obFormulario->addHidden ( $obHdnCodUF        );
    $obFormulario->addHidden ( $obHdnCampoNome	);
    $obFormulario->addHidden ( $obHdnCampoNum	);

    $obFormulario->addComponente ( $obLblCodBairro     );
    $obFormulario->addComponente ( $obTxtNomeBairro    );
    $obFormulario->addComponente ( $obLblNomeUF        );
    $obFormulario->addComponente ( $obLblNomeMunicipio );
}

if ($boM2Ativo) {
    $obFormulario->addTitulo            ( "Valores por M²"            );
    $obFormulario->addComponente        ( $obTxtValorTerritorial      );
    $obFormulario->addComponente        ( $obTxtValorPredial          );
    $obFormulario->addComponente        ( $obTxtInicioVigencia        );
    $obFormulario->addComponente        ( $obBscFundamentacao         );
}

if ($boAliquotaAtivo) {
    $obFormulario->addTitulo            ( "Alíquotas"                   );
    $obFormulario->addComponente        ( $obTxtAliquotaTerritorial     );
    $obFormulario->addComponente        ( $obTxtAliquotaPredial         );
    $obFormulario->addComponente        ( $obTxtInicioVigenciaAliquota  );
    $obFormulario->addComponente        ( $obBscFundamentacaoAliquota   );
}

$obFormulario->defineBarra( $arBotaoAcao );
$obFormulario->addIFrameOculto("oculto");
$obFormulario->obIFrame->setHeight("0");
$obFormulario->obIFrame->setWidth("100%");

$obFormulario->show ();

//DEFINICAO DO IFRAME MENSAGEM
$obIFrame = new IFrame;
$obIFrame->setName("telaMensagem");
$obIFrame->setWidth("100%");
$obIFrame->setHeight("50");
$obIFrame->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
