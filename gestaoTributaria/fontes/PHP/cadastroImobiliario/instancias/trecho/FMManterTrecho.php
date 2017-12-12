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
    * Página de formulário para o cadastro de trecho
    * Data de Criação   : 01/09/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Gustavo Passos Tourinho

    * @ignore

    * $Id: FMManterTrecho.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.06
*/

/*
$Log$
Revision 1.10  2006/09/18 10:31:51  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMTrecho.class.php"  );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMTrechoAliquota.class.php");
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMTrechoValorM2.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterTrecho";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include( $pgJs );

if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "incluir";
}

$obRCIMTrecho   = new RCIMTrecho;
$rsAtributos = new RecordSet;

//DEFINICAO DOS COMPONENTES
$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $_REQUEST['stAcao'] );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnNomLogradouro = new Hidden;
$obHdnNomLogradouro->setName ( "stNomeLogradouro" );
$obHdnNomLogradouro->setValue( $_REQUEST ["stNomeLogradouro"] );

$obHdnCodTrecho = new Hidden;
$obHdnCodTrecho->setName  ( "inCodTrecho"             );
$obHdnCodTrecho->setValue ( $_REQUEST[ "inCodTrecho" ] );

$obHdnCodLogradouro = new Hidden;
$obHdnCodLogradouro->setName  ( "inCodLogradouro"             );
$obHdnCodLogradouro->setValue ( $_REQUEST["inCodLogradouro"] );

$obHdnSequencia = new Hidden;
$obHdnSequencia->setName  ( "inSequencia"             );
$obHdnSequencia->setValue ( $_REQUEST[ "inSequencia" ] );

$obRCIMConfiguracao = new RCIMConfiguracao;
$obRCIMConfiguracao->consultarConfiguracao();
$rsMDSelecionados = $obRCIMConfiguracao->getRSMD();
$boM2Ativo = false;
while ( !$rsMDSelecionados->Eof() ) {
    if ( $rsMDSelecionados->getCampo( "nome" ) == "Trecho" ) {
        $boM2Ativo = true;
        break;
    }

    $rsMDSelecionados->proximo();
}

$rsAliquotaSelecionados = $obRCIMConfiguracao->getRSAliquota();
$boAliquotaAtivo = false;
while ( !$rsAliquotaSelecionados->Eof() ) {
    if ( $rsAliquotaSelecionados->getCampo( "nome" ) == "Trecho" ) {
        $boAliquotaAtivo = true;
        break;
    }

    $rsAliquotaSelecionados->proximo();
}

//valores m2-------------------
if ($boM2Ativo) {
    $obTCIMTrechoValorM2 = new TCIMTrechoValorM2;
    if ($_REQUEST["inCodLogradouro"] && $_REQUEST["inCodTrecho"]) {
        $stFiltro = " AND trecho_valor_m2.cod_logradouro = ".$_REQUEST["inCodLogradouro"]." AND trecho_valor_m2.cod_trecho = ".$_REQUEST["inCodTrecho"];
        $obTCIMTrechoValorM2->listaTrechoValorM2( $rsDadosM2, $stFiltro );
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
    $obTCIMTrechoAliquota = new TCIMTrechoAliquota;

    if ($_REQUEST["inCodLogradouro"] && $_REQUEST["inCodTrecho"]) {
        $stFiltro = " AND trecho_aliquota.cod_logradouro = ".$_REQUEST["inCodLogradouro"]." AND trecho_aliquota.cod_trecho = ".$_REQUEST["inCodTrecho"];
        $obTCIMTrechoAliquota->listaTrechoAliquota( $rsDadosM2, $stFiltro );
        $rsDadosM2->addFormatacao ('aliquota_territorial', 'NUMERIC_BR');
        $rsDadosM2->addFormatacao ('aliquota_predial', 'NUMERIC_BR');
    } else {
        $rsDadosM2 = new RecordSet;
    }

    $obTxtAliquotaTerritorial = new Moeda;
    $obTxtAliquotaTerritorial->setName   ( "flAliquotaTerritorial" );
    $obTxtAliquotaTerritorial->setTitle  ( "Alíquota territorial.");
    $obTxtAliquotaTerritorial->setRotulo ( "Alíquota Territorial" );
    $obTxtAliquotaTerritorial->setValue  ( $rsDadosM2->getCampo( "aliquota_territorial" ) );
    $obTxtAliquotaTerritorial->setNull   ( false );

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

if ($_REQUEST['stAcao'] == "incluir") {
    $obBscLogradouro = new BuscaInner;
    $obBscLogradouro->setRotulo ( "Logradouro"                               );
    $obBscLogradouro->setTitle  ( "Logradouro onde o trecho está localizado" );
    $obBscLogradouro->setId     ( "campoInner"                               );
    $obBscLogradouro->setNull   ( false                                      );
    $obBscLogradouro->obCampoCod->setName  ( "inNumLogradouro"               );
    $obBscLogradouro->obCampoCod->setId    ( "inNumLogradouro"               );
    $obBscLogradouro->obCampoCod->setValue ( $_REQUEST['inNumLogradouro']                );
    $obBscLogradouro->obCampoCod->obEvento->setOnChange( "buscaDado('buscaLogradouro');" );
    $obBscLogradouro->obCampoCod->obEvento->setOnBlur( "buscaDado('buscaLogradouro');" );
    $stBusca  = "abrePopUp('".CAM_GT_CIM_POPUPS."logradouro/FLProcurarLogradouro.php','frm','inNumLogradouro','campoInner',''";
    $stBusca .= " ,'".Sessao::getId()."&stCadastro=trecho','800','550');";
    $obBscLogradouro->setFuncaoBusca                    ( $stBusca );
    SistemaLegado::executaFramePrincipal("d.getElementById('campoInner').innerHTML = '&nbsp;';");

    $obTxtSequencia = new TextBox;
    $obTxtSequencia->setRotulo    ( "Seqüência"           );
    $obTxtSequencia->setTitle     ( "Seção do logradouro" );
    $obTxtSequencia->setSize      ( 10 );
    $obTxtSequencia->setMaxLength ( 10 );
    $obTxtSequencia->setNull      ( false );
    $obTxtSequencia->setInteiro   ( true  );
    $obTxtSequencia->setName      ( "inCodSequencia"      );
    $obTxtSequencia->setValue     ( $_REQUEST['inCodSequencia']       );
    $obTxtSequencia->setNaoZero   ( true                  );
    // Mostra atributos selecionados
    $obRCIMTrecho->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributos );
} elseif ($_REQUEST['stAcao'] == "alterar") {
    $stLogradouroSequencia = $_REQUEST["inCodLogradouro"].".".$_REQUEST["inSequencia"];
    $obLblCodigoTrecho = new Label;
    $obLblCodigoTrecho->setRotulo ( "Código do Trecho");
    $obLblCodigoTrecho->setValue ( $stLogradouroSequencia );

    $stNomeLogradouro = $_REQUEST["stNomeLogradouro"];
    $obLblNomeLogradouro = new Label;
    $obLblNomeLogradouro->setRotulo ( "Nome do Logradouro" );
    $obLblNomeLogradouro->setTitle  ( "Logradouro onde o trecho está localizado" );
    $obLblNomeLogradouro->setValue  ( $stNomeLogradouro );
    //DEFINICAO DOS ATRIBUTOS
    $arChaveAtributoTrecho =  array( "cod_trecho"      => $_REQUEST["inCodTrecho"],
                                     "cod_logradouro" => $_REQUEST["inCodLogradouro"] );
    $obRCIMTrecho->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoTrecho );
    $obRCIMTrecho->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );
}

$obMontaAtributos = new MontaAtributos;
$obMontaAtributos->setTitulo     ( "Atributos"  );
$obMontaAtributos->setName       ( "Atributo_"  );
$obMontaAtributos->setRecordSet  ( $rsAtributos );

$obTxtExtensao = new Moeda;//Numerico;
$obTxtExtensao->setRotulo    ( "Extensão"                     );
$obTxtExtensao->setTitle     ( "Extensão em metros do trecho" );
$obTxtExtensao->setName      ( "flExtensao"                   );
$obTxtExtensao->setId        ( "flExtensao"                   );
$obTxtExtensao->setValue     ( $_REQUEST["flExtensao"] );
$obTxtExtensao->setSize      ( 10 );
$obTxtExtensao->setMaxLength ( 10 );
$obTxtExtensao->setMaxValue  ( 999999.99  );
$obTxtExtensao->setNull      ( false );
$obTxtExtensao->setNegativo  ( false );
$obTxtExtensao->setNaoZero   ( true  );
$obTxtExtensao->setFloat     ( true  );

$obForm = new Form;
$obForm->setAction            ( $pgProc );
$obForm->setTarget            ( "oculto" );

$obBtnOK = new OK;
$obBtnLimpar = new Limpar;
$arBotoes = array( $obBtnOK, $obBtnLimpar );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm   ( $obForm             );
$obFormulario->setAjuda ( "UC-05.01.06" );
$obFormulario->addHidden ( $obHdnAcao          );
$obFormulario->addHidden ( $obHdnCtrl          );
$obFormulario->addHidden ( $obHdnAliquota      );
$obFormulario->addHidden ( $obHdnMD            );
$obFormulario->addHidden ( $obHdnNomLogradouro );
$obFormulario->addHidden ( $obHdnCodTrecho     );
$obFormulario->addHidden ( $obHdnCodLogradouro );
$obFormulario->addHidden ( $obHdnSequencia     );
$obFormulario->addTitulo ( "Dados para Trecho" );
if ($_REQUEST['stAcao'] == "incluir") {
    $obFormulario->addComponente ( $obBscLogradouro );
    $obFormulario->addComponente ( $obTxtSequencia  );
} else {
    $obFormulario->addComponente ( $obLblCodigoTrecho   );
    $obFormulario->addComponente ( $obLblNomeLogradouro );
}

$obFormulario->addComponente      ( $obTxtExtensao   );

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

$obMontaAtributos->geraFormulario ( $obFormulario    );
if ( $_REQUEST['stAcao'] == "incluir" )
    $obFormulario->defineBarra( $arBotoes );
else
    $obFormulario->Cancelar ();
if ($_REQUEST['stAcao'] == "incluir") {
    $obFormulario->setFormFocus( $obBscLogradouro->obCampoCod->getid() );
} elseif ($_REQUEST['stAcao'] == "alterar") {
    $obFormulario->setFormFocus( $obTxtExtensao->getid());
}
$obFormulario->show  ();
?>
