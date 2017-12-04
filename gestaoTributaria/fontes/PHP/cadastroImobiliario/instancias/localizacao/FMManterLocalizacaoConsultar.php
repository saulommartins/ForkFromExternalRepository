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
    * Página de Formulário para p cadastro de localização
    * Data de Criação   : 28/09/2015

    * @author Analista: Luciana
    * @author Desenvolvedor: Carlos Adriano

    * @ignore

    * $Id: FMManterLocalizacaoConsultar.php 63673 2015-09-28 19:31:03Z carlos.silva $

    * Casos de uso: uc-05.01.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLocalizacao.class.php"                                          );
include_once ( CAM_GT_CIM_COMPONENTES."MontaLocalizacao.class.php"                                     );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php");
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMLocalizacaoAliquota.class.php");
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMLocalizacaoValorM2.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterLocalizacao";
$pgFilt      = "FL".$stPrograma.".php";
$pgList      = "LS".$stPrograma.".php";
$pgForm      = "FM".$stPrograma.".php";
$pgFormNivel = "FM".$stPrograma."Nivel.php";
$pgProc      = "PR".$stPrograma.".php";
$pgOcul      = "OC".$stPrograma.".php";
$pgJS        = "JSManterLocalizacao.js";

include_once ($pgJS);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "consultar";
}

$obRCIMLocalizacao  = new RCIMLocalizacao;
$obMontaLocalizacao = new MontaLocalizacao;
$obMontaAtributos   = new MontaAtributos;
$rsAtributos        = new RecordSet;
$obMascara          = new Mascara;
$stNomePai = '';
$stCompostoPai = '';

$inCodigoVigencia = $_REQUEST["inCodigoVigencia"];
$inCodigoNivel    = $_REQUEST["inCodigoNivel"];
$inCodigoLocalizacao = $_REQUEST['inCodigoLocalizacao'];

$obRCIMLocalizacao->setCodigoVigencia    ( $_REQUEST["inCodigoVigencia"]    );
$obRCIMLocalizacao->setCodigoNivel       ( $_REQUEST["inCodigoNivel"]       );
$obRCIMLocalizacao->setCodigoLocalizacao ( $_REQUEST["inCodigoLocalizacao"] );

$obMontaLocalizacao->setCodigoVigencia    ( $_REQUEST["inCodigoVigencia"]    );
$obMontaLocalizacao->setCodigoNivel       ( $_REQUEST["inCodigoNivel"]       );
$obMontaLocalizacao->setCodigoLocalizacao ( $_REQUEST["inCodigoLocalizacao"] );
$obMontaLocalizacao->setValorComposto     ( $_REQUEST["stValorComposto"]     );

$obRCIMLocalizacao->consultarLocalizacao();

//RECUPERA INFORMACOES DA LOCALIZACAO PAI
$stPaiComposto  = substr( $_REQUEST["stValorComposto"], 0, strlen( $_REQUEST["stValorComposto"] ) - ( strlen($obRCIMLocalizacao->getMascara() +1 )));
$stPaiComposto .= ".".str_pad( 0 , strlen($obRCIMLocalizacao->getMascara()) , 0 );
$obRCIMLocalizacao->setValorComposto( $stPaiComposto );

$obRCIMLocalizacao->recuperaPaiLocalizacao( $rsPai );
$stNomePai      = $rsPai->getCampo( "nom_localizacao" );
$stCompostoPai  = $rsPai->getCampo( "codigo_composto" );

//DEFINICAO DOS ATRIBUTOS
$arChavePersistenteValores = array( "cod_nivel"=> $inCodigoNivel,
                                    "cod_vigencia"=> $inCodigoVigencia,
                                    "cod_localizacao"=>$inCodigoLocalizacao
                                  );
$obRCIMLocalizacao->obRCadastroDinamico->setChavePersistenteValores( $arChavePersistenteValores );
$obRCIMLocalizacao->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );

$obRCIMConfiguracao = new RCIMConfiguracao;
$obRCIMConfiguracao->consultarConfiguracao();
$rsMDSelecionados = $obRCIMConfiguracao->getRSMD();
$boM2Ativo = false;
while ( !$rsMDSelecionados->Eof() ) {
    if ( $rsMDSelecionados->getCampo( "nome" ) == "Localização" ) {
        $boM2Ativo = true;
        break;
    }
    $rsMDSelecionados->proximo();
}

$rsAliquotaSelecionados = $obRCIMConfiguracao->getRSAliquota();
$boAliquotaAtivo = false;
while ( !$rsAliquotaSelecionados->Eof() ) {
    if ( $rsAliquotaSelecionados->getCampo( "nome" ) == "Localização" ) {
        $boAliquotaAtivo = true;
        break;
    }

    $rsAliquotaSelecionados->proximo();
}

//valores m2-------------------
if ($boM2Ativo) {
    $obTCIMLocalizacaoValorM2 = new TCIMLocalizacaoValorM2;
    if ($_REQUEST["inCodigoLocalizacao"]) {
        $stFiltro = " AND localizacao_valor_m2.cod_localizacao = ".$_REQUEST["inCodigoLocalizacao"];
        $obTCIMLocalizacaoValorM2->listaLocalizacaoValorM2( $rsDadosM2, $stFiltro );
        $rsDadosM2->addFormatacao ('valor_m2_territorial', 'NUMERIC_BR');
        $rsDadosM2->addFormatacao ('valor_m2_predial', 'NUMERIC_BR');
    } else {
        $rsDadosM2 = new RecordSet;
    }

    $obTxtValorTerritorial = new Label;
    $obTxtValorTerritorial->setName ( "flValorTerritorial" );
    $obTxtValorTerritorial->setTitle ( "Valor por metro quadrado territorial.");
    $obTxtValorTerritorial->setRotulo ( "Valor Territorial" );
    $obTxtValorTerritorial->setValue ( $rsDadosM2->getCampo( "valor_m2_territorial" ) );

    $obTxtValorPredial = new Label;
    $obTxtValorPredial->setName ( "flValorPredial" );
    $obTxtValorPredial->setTitle ( "Valor por metro quadrado predial" );
    $obTxtValorPredial->setRotulo ( "Valor Predial" );
    $obTxtValorPredial->setValue ( $rsDadosM2->getCampo( "valor_m2_predial" ) );

    $obTxtInicioVigencia = new Label;
    $obTxtInicioVigencia->setName ( "dtVigenciaMD" );
    $obTxtInicioVigencia->setTitle ( "Data de início de vigência dos valores." );
    $obTxtInicioVigencia->setRotulo ( "Início Vigência" );
    $obTxtInicioVigencia->setValue ( $rsDadosM2->getCampo( "dt_vigencia" ) );

    $obBscFundamentacao = new Label;
    $obBscFundamentacao->setName ( "dtVigenciaAliquota" );
    $obBscFundamentacao->setTitle ( "Data de início de vigência dos valores." );
    $obBscFundamentacao->setRotulo ( "Início Vigência" );
    $obBscFundamentacao->setValue (  $rsDadosM2->getCampo( "cod_norma" )." - ". $rsDadosM2->getCampo( "nom_norma" ) );
}
//----

//aliquota------------
if ($boAliquotaAtivo) {
    $obTCIMLocalizacaoAliquota = new TCIMLocalizacaoAliquota;

    if ($_REQUEST["inCodigoLocalizacao"]) {
        $stFiltro = " AND localizacao_aliquota.cod_localizacao = ".$_REQUEST["inCodigoLocalizacao"];
        $obTCIMLocalizacaoAliquota->listaLocalizacaoAliquota( $rsDadosM2, $stFiltro );
        $rsDadosM2->addFormatacao ('aliquota_territorial', 'NUMERIC_BR');
        $rsDadosM2->addFormatacao ('aliquota_predial', 'NUMERIC_BR');
    } else {
        $rsDadosM2 = new RecordSet;
    }

    $obTxtAliquotaTerritorial = new Label;
    $obTxtAliquotaTerritorial->setName ( "flAliquotaTerritorial" );
    $obTxtAliquotaTerritorial->setTitle ( "Alíquota territorial.");
    $obTxtAliquotaTerritorial->setRotulo ( "Alíquota Territorial" );
    $obTxtAliquotaTerritorial->setValue ( $rsDadosM2->getCampo( "aliquota_territorial" ) );

    $obTxtAliquotaPredial = new Label;
    $obTxtAliquotaPredial->setName ( "flAliquotaPredial" );
    $obTxtAliquotaPredial->setTitle ( "Alíquota predial.");
    $obTxtAliquotaPredial->setRotulo ( "Alíquota Predial" );
    $obTxtAliquotaPredial->setValue ( $rsDadosM2->getCampo( "aliquota_predial" ) );

    $obTxtInicioVigenciaAliquota = new Label;
    $obTxtInicioVigenciaAliquota->setName ( "dtVigenciaAliquota" );
    $obTxtInicioVigenciaAliquota->setTitle ( "Data de início de vigência dos valores." );
    $obTxtInicioVigenciaAliquota->setRotulo ( "Início Vigência" );
    $obTxtInicioVigenciaAliquota->setValue ( $rsDadosM2->getCampo( "dt_vigencia" ) );

    $obBscFundamentacaoAliquota = new Label;
    $obBscFundamentacaoAliquota->setName ( "dtVigenciaAliquota" );
    $obBscFundamentacaoAliquota->setTitle ( "Data de início de vigência dos valores." );
    $obBscFundamentacaoAliquota->setRotulo ( "Início Vigência" );
    $obBscFundamentacaoAliquota->setValue (  $rsDadosM2->getCampo( "cod_norma" )." - ". $rsDadosM2->getCampo( "nom_norma" ) );
}
//------------------

$obHdnAliquota = new Hidden;
$obHdnAliquota->setName   ( 'boAliquotaAtivo' );
$obHdnAliquota->setValue  ( $boAliquotaAtivo );

$obHdnMD = new Hidden;
$obHdnMD->setName   ( 'boM2Ativo' );
$obHdnMD->setValue  ( $boM2Ativo );

$stNomeNivel        = $obRCIMLocalizacao->getNomeNivel();
$stMascara          = $obRCIMLocalizacao->getMascara();
$inValorLocalizacao = $obRCIMLocalizacao->getValor();
$stNomeLocalizacao  = $obRCIMLocalizacao->getNomeLocalizacao();
$stValorComposto    = $obRCIMLocalizacao->getValorComposto();

$stValorComposto    = substr( $stValorComposto, 0, strlen( $stValorComposto ) - ( strlen( $inValorLocalizacao ) + 1 ) );
$_REQUEST['stValorComposto'] = $stValorComposto;
$_GET['stValorComposto'] = $stValorComposto;
Sessao::write('stValorComposto',$stValorComposto);
$obMontaAtributos->setName ("Atributo_");
$obMontaAtributos->setRecordSet( $rsAtributos );
$obMontaAtributos->recuperaValores();

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( 'consultar' );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $request->get('stCtrl') );

$obHdnCodigoNivel = new Hidden;
$obHdnCodigoNivel->setName  ( "inCodigoNivel" );
$obHdnCodigoNivel->setValue ( $inCodigoNivel  );

$obHdnCodigoVigencia = new Hidden;
$obHdnCodigoVigencia->setName  ( "inCodigoVigencia" );
$obHdnCodigoVigencia->setvalue ( $inCodigoVigencia );

$obLbNomeNivel = new Label;
$obLbNomeNivel->setRotulo( "Nível" );
$obLbNomeNivel->setValue( $stNomeNivel );

$obTxtCodigoLocalizacao = new Label;
$obTxtCodigoLocalizacao->setName      ( "inValorLocalizacao" );
$obTxtCodigoLocalizacao->setId        ( "inValorLocalizacao" );
$obTxtCodigoLocalizacao->setRotulo    ( "Código"             );
$obTxtCodigoLocalizacao->setValue     ( $inValorLocalizacao  );

$obTxtNomeLocalizacao = new Label;
$obTxtNomeLocalizacao->setName      ( "stNomeLocalizacao" );
$obTxtNomeLocalizacao->setId        ( "stNomeLocalizacao" );
$obTxtNomeLocalizacao->setRotulo    ( "Nome"              );
$obTxtNomeLocalizacao->setValue     ( $stNomeLocalizacao  );

//DEFINICAO DOS COMPONENTES PARA ALTERAÇÃO
$obLblValorComposto = new Label;
$obLblValorComposto->setRotulo ( "Localização Superior" );
$obLblValorComposto->setValue  ( $stCompostoPai." - $stNomePai" );

$obHdnValorReduzido = new Hidden;
$obHdnValorReduzido->setName  ( "stValorReduzido" );
$obHdnValorReduzido->setValue ( $stValorComposto  );

$obHdnCodigoLocalizacao = new Hidden;
$obHdnCodigoLocalizacao->setName  ( "inCodigoLocalizacao" );
$obHdnCodigoLocalizacao->setValue ( $inCodigoLocalizacao  );

$obHdnValComposto = new Hidden;
$obHdnValComposto->setName( "stValorComposto" );
$obHdnValComposto->setValue( $stValorComposto );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto"     );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->setAjuda ( "UC-05.01.03" );
$obFormulario->addTitulo      ( "Dados para nível" );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );
$obFormulario->addHidden    ( $obHdnAliquota );
$obFormulario->addHidden    ( $obHdnMD );
$obFormulario->addHidden    ( $obHdnCodigoNivel    );
$obFormulario->addHidden    ( $obHdnCodigoVigencia );
$obFormulario->addHidden    ( $obHdnValorReduzido  );


$obFormulario->addComponente ( $obLbNomeNivel       );
$obFormulario->addHidden     ( $obHdnCodigoLocalizacao );
if ($inCodigoNivel > 1) {
    $obFormulario->addComponente ( $obLblValorComposto     );
}
$obFormulario->addComponente ( $obTxtCodigoLocalizacao );
$obFormulario->addComponente ( $obTxtNomeLocalizacao   );

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

$obMontaAtributos->geraFormulario( $obFormulario );

$onBtnCancelar = new Cancelar;
$onBtnCancelar->setValue('Voltar');
$obFormulario->defineBarra( array( $onBtnCancelar ) );

$obFormulario->show();
