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
    * Página de Formulário para o cadastro de face de quadra
    * Data de Criação   : 13/10/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    * $Id: FMManterFaceQuadra.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.07
*/

/*
$Log$
Revision 1.16  2007/02/28 12:52:43  cercato
Bug #8420#

Revision 1.15  2006/09/18 10:30:35  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMFaceQuadra.class.php");
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLocalizacao.class.php");
include_once ( CAM_GT_CIM_NEGOCIO."RCIMNivel.class.php");
include_once ( CAM_GT_CIM_COMPONENTES."MontaLocalizacao.class.php");
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php");
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMFaceQuadraAliquota.class.php");
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMFaceQuadraValorM2.class.php");

// Define nomes dos arquivos PHP
$stPrograma = "ManterFaceQuadra";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once( $pgJs );

$rsTrecho           = new RecordSet;
$obMontaLocalizacao = new MontaLocalizacao;
$obMontaAtributos   = new MontaAtributos;
$obRCIMLocalizacao  = new RCIMLocalizacao;
$obRCIMNivel        = new RCIMNivel;
$obRCIMFaceQuadra   = new RCIMFaceQuadra;

$arTrechosSessao = array();
Sessao::write('Trechos', $arTrechosSessao);

$stAcao = $request->get('stAcao');

if ( empty( $stAcao ) ) {
    $stAcao = 'incluir';
}

// Definição dos Componentes do Formulário

//------------------------
$obRCIMNivel->recuperaVigenciaAtual( $rsVigenciaAtual );

$obRCIMNivel->setCodigoVigencia( $rsVigenciaAtual->getCampo("cod_vigencia") );
$obRCIMNivel->recuperaUltimoNivel( $rsUltimoNivel );

$inCodNvl = $rsUltimoNivel->getCampo("cod_nivel")+1;
Sessao::write('inCodigoNivel2', $inCodNvl);

//------------------------

$obHdnCd = new Hidden;
$obHdnCd->setName   ( 'inCodNvl' );
$obHdnCd->setValue  ( $inCodNvl );

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( 'oculto' );

$obHdnAcao = new Hidden;
$obHdnAcao->setName	( 'stAcao' );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName	( 'stCtrl' );
$obHdnCtrl->setValue( '' );

$obHdnCodigoVigencia = new Hidden;
$obHdnCodigoVigencia->setName   ( 'inCodigoVigencia' );
$obHdnCodigoVigencia->setValue  ( $obRCIMNivel->getCodigoVigencia() );

$obHdnTrecho = new Hidden;
$obHdnTrecho->setName( "stTrecho" );
$obHdnTrecho->setValue( $stTrecho );

$obButtonIncluirTrecho = new Button;
$obButtonIncluirTrecho->setName             ( "btnIncluirTrecho" );
$obButtonIncluirTrecho->setValue            ( "Incluir" );
$obButtonIncluirTrecho->obEvento->setOnClick ( "return incluirTrecho('incluirTrecho');" );

$obButtonLimparTrecho = new Button;
$obButtonLimparTrecho->setName              ( "btnLimparTrecho" );
$obButtonLimparTrecho->setValue             ( "Limpar" );
$obButtonLimparTrecho->obEvento->setOnClick  ( "limparTrecho('limparTrecho');" );

$obSpnTrechoCadastrado = new Span;
$obSpnTrechoCadastrado->setId ( "spnTrechoCadastrado" );

if ($stAcao == "alterar") {
    $obHdnCodigoFace = new Hidden;
    $obHdnCodigoFace->setName  ( "inCodigoFace"             );
    $obHdnCodigoFace->setValue ( $_REQUEST[ "inCodigoFace" ] );

    $obHdnCodigoLocalizacao = new Hidden;
    $obHdnCodigoLocalizacao->setName  ( "inCodigoLocalizacao"             );
    $obHdnCodigoLocalizacao->setValue ( $_REQUEST[ "inCodigoLocalizacao" ] );

    $obHdnValorComposto = new Hidden;
    $obHdnValorComposto->setName  ( "inValorComposto"             );
    $obHdnValorComposto->setValue ( $_REQUEST[ "inValorComposto" ] );

    $stValorComposto = $_REQUEST["inValorComposto"];
    $obLblValorComposto = new Label;
    $obLblValorComposto->setRotulo ( "Localização" );
    $obLblValorComposto->setTitle  ( "Localização onde a face de quadra está localizada" );
    $obLblValorComposto->setValue  ( $stValorComposto );

    $stCodigoFace = $_REQUEST["inCodigoFace"];
    $obLblCodigoFace = new Label;
    $obLblCodigoFace->setRotulo ( "Código da Face de Quadra" );
    $obLblCodigoFace->setTitle  ( "Código da face de quadra" );
    $obLblCodigoFace->setValue  ( $stCodigoFace );

    $obRCIMFaceQuadra->setCodigoFace( $_REQUEST[ "inCodigoFace" ] );
    $obRCIMFaceQuadra->obRCIMLocalizacao->setCodigoLocalizacao( $_REQUEST[ "inCodigoLocalizacao" ] );

    //DEFINICAO DOS ATRIBUTOS
    $arChaveAtributoFaceQuadra =  array( "cod_face"       => $_REQUEST["inCodigoFace"],
                                         "cod_logradouro" => $_REQUEST["inCodigoLocalizacao"] );

    $obRCIMFaceQuadra->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoFaceQuadra );
    $obRCIMFaceQuadra->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );

    $obRCIMFaceQuadra->listarFaceQuadraTrecho( $rsTrecho );

    $inCount = 0;
    while ( !$rsTrecho->eof() ) {
        $arTMP['inId']                 = ++$inCount;
        $arTMP['inCodigoFace']         = $rsTrecho->getCampo( 'cod_face' );
        $arTMP['inCodigoLocalizacao']  = $rsTrecho->getCampo( 'cod_localizacao' );
        $arTMP['inNumTrecho']          = $rsTrecho->getCampo( 'cod_logradouro' ).".".$rsTrecho->getCampo( 'sequencia' );
        $arTMP['stTrecho']             = $rsTrecho->getCampo( 'tipo_nom_logradouro' );
        $arTMP['inCodigoTrecho']       = $rsTrecho->getCampo( 'cod_trecho' );
        $arTrechosSessao[]             = $arTMP;
        $rsTrecho->proximo();
    }
   Sessao::write('Trechos', $arTrechosSessao);
   SistemaLegado::executaFramePrincipal("buscarValor('ListaTrecho');");
}

$obRCIMConfiguracao = new RCIMConfiguracao;
$obRCIMConfiguracao->consultarConfiguracao();
$rsMDSelecionados = $obRCIMConfiguracao->getRSMD();
$boM2Ativo = false;
while ( !$rsMDSelecionados->Eof() ) {
    if ( $rsMDSelecionados->getCampo( "nome" ) == "Face de Quadra" ) {
        $boM2Ativo = true;
        break;
    }

    $rsMDSelecionados->proximo();
}

$rsAliquotaSelecionados = $obRCIMConfiguracao->getRSAliquota();
$boAliquotaAtivo = false;
while ( !$rsAliquotaSelecionados->Eof() ) {
    if ( $rsAliquotaSelecionados->getCampo( "nome" ) == "Face de Quadra" ) {
        $boAliquotaAtivo = true;
        break;
    }

    $rsAliquotaSelecionados->proximo();
}

//valores m2-------------------
if ($boM2Ativo) {
    $obTCIMFaceQuadraValorM2 = new TCIMFaceQuadraValorM2;
    if ($_REQUEST["inCodigoFace"] && $_REQUEST["inCodigoLocalizacao"]) {
        $stFiltro = " AND face_quadra_valor_m2.cod_face = ".$_REQUEST["inCodigoFace"]." AND face_quadra_valor_m2.cod_localizacao = ".$_REQUEST["inCodigoLocalizacao"];
        $obTCIMFaceQuadraValorM2->listaFaceQuadraValorM2( $rsDadosM2, $stFiltro );
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
    $obTCIMFaceQuadraAliquota = new TCIMFaceQuadraAliquota;

    if ($_REQUEST["inCodigoFace"] && $_REQUEST["inCodigoLocalizacao"]) {
        $stFiltro = " AND face_quadra_aliquota.cod_face = ".$_REQUEST["inCodigoFace"]." AND face_quadra_aliquota.cod_localizacao = ".$_REQUEST["inCodigoLocalizacao"];
        $obTCIMFaceQuadraAliquota->listaFaceQuadraAliquota( $rsDadosM2, $stFiltro );
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

$obBuscaTrecho = new BuscaInner;
$obBuscaTrecho->setId                             ( "stNumTrecho"     );
$obBuscaTrecho->setNull                           ( true              );
$obBuscaTrecho->obCampoCod->setName               ( "inNumTrecho"     );
$obBuscaTrecho->obCampoCod->setValue              ( $inNumTrecho      );
$obBuscaTrecho->obCampoCod->setInteiro		      ( false 			);
$obBuscaTrecho->obCampoCod->obEvento->setOnChange ( "BloqueiaFrames(true,false);buscarTrecho();" );
$obBuscaTrecho->setFuncaoBusca                    ("abrePopUp('../../popups/trecho/FLProcurarTrecho.php','frm','inNumTrecho','stNumTrecho','','".Sessao::getId()."','800','550')");
$obBuscaTrecho->setRotulo                         ( "*Trecho"         );
$obBuscaTrecho->setTitle                          ( "Trecho onde a face de quadra está localizada" );

// mostra atributos selecionados
if ($stAcao == "incluir") {
    $obRCIMFaceQuadra->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributos );
} else {
    $arChaveAtributoFaceQuadra =  array( "cod_face"    => $_REQUEST["inCodigoFace"],
                                         "cod_localizacao" => $_REQUEST["inCodigoLocalizacao"] );
    $obRCIMFaceQuadra->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoFaceQuadra );
    $obRCIMFaceQuadra->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );
}
$obMontaAtributos = new MontaAtributos;
$obMontaAtributos->setTitulo     ( "Atributos"  );
$obMontaAtributos->setName       ( "Atributo_"  );
$obMontaAtributos->setRecordSet  ( $rsAtributos );

// mostra formulario
$obFormulario = new Formulario;
$obFormulario->addForm                  ( $obForm );
$obFormulario->setAjuda                 ( "UC-05.01.07" );
$obFormulario->addHidden                ( $obHdnAcao );
$obFormulario->addHidden                ( $obHdnCtrl );
$obFormulario->addHidden                ( $obHdnCodigoVigencia );
$obFormulario->addHidden                ( $obHdnTrecho );
$obFormulario->addHidden                ( $obHdnCd );
$obFormulario->addHidden                ( $obHdnAliquota );
$obFormulario->addHidden                ( $obHdnMD );
$obFormulario->addTitulo                ( "Dados para Face de Quadra" );
if ($stAcao == "incluir") {
    $obMontaLocalizacao->geraFormulario2 ( $obFormulario, true, $inCodNvl               );
} elseif ($stAcao == "alterar") {
    $obFormulario->addHidden            ( $obHdnCodigoFace            );
    $obFormulario->addHidden            ( $obHdnValorComposto         );
    $obFormulario->addHidden            ( $obHdnCodigoLocalizacao     );
    $obFormulario->addComponente        ( $obLblValorComposto         );
    $obFormulario->addComponente        ( $obLblCodigoFace            );
}

$obMontaAtributos->geraFormulario      ( $obFormulario  );

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

$obFormulario->addTitulo                ( "Trecho"                    );
$obFormulario->addComponente            ( $obBuscaTrecho              );
$obFormulario->defineBarra              ( array($obButtonIncluirTrecho, $obButtonLimparTrecho), "left", "");
$obFormulario->addSpan                  ( $obSpnTrechoCadastrado );

if( $stAcao == "incluir" )
    $obFormulario->Ok       ();
else
    $obFormulario->Cancelar ();

$obFormulario->show         ();

SistemaLegado::executaFrameOculto("f.stChaveLocalizacao.focus();");
