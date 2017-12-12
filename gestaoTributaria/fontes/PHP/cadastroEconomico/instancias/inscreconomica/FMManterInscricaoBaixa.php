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
    * Página de Formulario de Baixa de Inscrição Econômica
    * Data de Criação   : 12/01/2005

    * @author  Tonismar Régis Bernardo

    * @ignore

    * $Id: FMManterInscricaoBaixa.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.10

*/

/*
$Log$
Revision 1.14  2007/03/05 13:58:25  rodrigo
Bug #8563#

Revision 1.13  2007/02/09 18:33:00  rodrigo
#8342#

Revision 1.12  2006/09/15 14:33:01  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMInscricaoEconomica.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMEmpresaDeFato.class.php"      );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMEmpresaDeDireito.class.php"   );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMAutonomo.class.php"           );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMCategoria.class.php"          );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMConfiguracao.class.php" );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMCadastroEconomico.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterInscricao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgFormNivel = "FM".$stPrograma."Nivel.php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ($pgJS);

//print_r($_REQUEST);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    (!($stAcao=="reativar"))? $stAcao = "incluir" : $stAcao = $stAcao;
}

switch ($_REQUEST[ "inCodigoEnquadramento" ]) {
    case 1:
        $obRCEMInscricaoEconomica = new RCEMEmpresaDeFato;
    break;
    case 2:
        $obRCEMInscricaoEconomica = new RCEMEmpresaDeDireito;
    break;
    case 3:
        $obRCEMInscricaoEconomica = new RCEMAutonomo;
    break;
}

$obRCEMConfiguracao = new RCEMConfiguracao;
$obRCEMConfiguracao->setCodigoModulo( 14 );
$obRCEMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCEMConfiguracao->consultarConfiguracao();
$obRCEMConfiguracao->consultarMascaraProcesso( $stMascaraProcesso );

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( 'stCtrl' );
$obHdnCtrl->setValue( $_REQUEST["stCtrl"] );

$inInscricao = $_REQUEST["inInscricaoEconomica"];
$obHdnInscricaoEconomica = new Hidden;
$obHdnInscricaoEconomica->setName ( 'inInscricaoEconomica' );
$obHdnInscricaoEconomica->setValue( $inInscricao );

$inEnquadramento = $_REQUEST[ "inCodigoEnquadramento" ];
$obHdnEnquadramento = new Hidden;
$obHdnEnquadramento->setName ( 'inCodigoEnquadramento' );
$obHdnEnquadramento->setValue( $inEnquadramento        );

$inCGM = $_REQUEST[ "inCGM" ];
$obHdnCGM = new Hidden;
$obHdnCGM->setName ( 'inNumCGM' );
$obHdnCGM->setValue( $inCGM     );

$inFuncionalidade = $_REQUEST[ "funcionalidade" ];
$obHdnFuncionalidade = new Hidden;
$obHdnFuncionalidade->setName ( 'inFunc' );
$obHdnFuncionalidade->setValue( $inFuncionalidade );

$obHdnDataAbertura = new Hidden;
$obHdnDataAbertura->setName  ( 'stDtAbertura'          );
$obHdnDataAbertura->setValue ( $_REQUEST['stDtAbertura'] );

$stCGM = $_REQUEST[ "stCGM" ];
$obLblCGM = new Label;
$obLblCGM->setRotulo( "Nome"  );
$obLblCGM->setValue ( $stCGM );

$obLblInscricao = new Label;
$obLblInscricao->setRotulo( "Inscrição Econômica" );
$obLblInscricao->setValue ( $inInscricao          );
$obRCEMInscricaoEconomica->setInscricaoEconomica( $inInscricao );

if ($stAcao!="reativar") {
    $obTxtDataBaixa = new Data;
    $obTxtDataBaixa->setName   ( "dtDataBaixa" );
    $obTxtDataBaixa->setRotulo ( "Data da Baixa"   );
    $obTxtDataBaixa->setNull   ( false );
    $obTxtDataBaixa->setValue  ( $_REQUEST["dtDataBaixa"] );
} else {
    $obLblDataBaixa = new Label;
    $obLblDataBaixa->setRotulo( "Data de Baixa" );
    $obLblDataBaixa->setValue ( $_REQUEST["stDtTermino"]    );

    $obHdnDataInicio = new Hidden;
    $obHdnDataInicio->setName  ( 'stDtInicio' );
    $obHdnDataInicio->setValue ( $_REQUEST["stDtTermino"] );

    $obTxtDataTermino = new Data;
    $obTxtDataTermino->setName   ( "dtDataTermino"   );
    $obTxtDataTermino->setRotulo ( "Data de Término" );
    $obTxtDataTermino->setNull   ( false             );
}

$obBscProcesso = new BuscaInner;
$obBscProcesso->setRotulo ( "Processo" );
$obBscProcesso->obCampoCod->setName  ( "inCodProcesso"  );
$obBscProcesso->obCampoCod->setValue ( $_REQUEST["inCodProcesso"]   );
$obBscProcesso->obCampoCod->obEvento->setOnChange ( "buscaBairro();" );
$obBscProcesso->obCampoCod->setSize  ( strlen($stMascaraProcesso) );
$obBscProcesso->obCampoCod->setMascara( $stMascaraProcesso );
$stBusca  = "abrePopUp('".CAM_GA_PROT_POPUPS."processo/FLBuscaProcessos.php','frm','inCodProcesso','stProcesso',''";
$stBusca .= " ,'".Sessao::getId()."','800','550')";
$obBscProcesso->setFuncaoBusca ( $stBusca );

$obTxtMotivo = new TextArea;
$obTxtMotivo->setRotulo        ( "Motivo"          );
$obTxtMotivo->setTitle         ( "Motivo da Baixa" );
$obTxtMotivo->setName          ( "stMotivo" );
$obTxtMotivo->setNull          ( false             );

$obRCEMInscricaoEconomica->listaTiposDeBaixa( $rsTipoDeBaixa );

$obCmbTipo = new Select;
$obCmbTipo->setRotulo        ( "Tipo da Baixa" );
$obCmbTipo->setTitle         ( "Tipo da Baixa" );
$obCmbTipo->setName          ( "cmbTipo"                    );
$obCmbTipo->addOption        ( "", "Selecione"              );
$obCmbTipo->setCampoId       ( "cod_tipo"                   );
$obCmbTipo->setCampoDesc     ( "nom_tipo"                   );
$obCmbTipo->preencheCombo    ( $rsTipoDeBaixa               );
$obCmbTipo->setNull          ( false                        );

$obRCEMConfiguracao = new RCEMConfiguracao;
$obRCEMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCEMConfiguracao->consultarConfiguracao();
if ( $obRCEMConfiguracao->getEmissaoCertidaoBaixa() == "sim" ) {
    $stFiltro = " WHERE modelo_arquivos_documento.cod_acao = ".Sessao::read("acao");

    $obTCEMCadastroEconomico = new TCEMCadastroEconomico;
    $obTCEMCadastroEconomico->recuperaDocumentosBaixaInscricaoEconomica( $rsDocumento, $stFiltro );

    $obCmbDocumento = new Select;
    $obCmbDocumento->setRotulo ( "Documento" );
    $obCmbDocumento->setTitle ( "Selecione modelo de documento para emissão" );
    $obCmbDocumento->setName ( "cmbDocumento" );
    $obCmbDocumento->addOption ( "", "Selecione" );
    $obCmbDocumento->setCampoId ( "[nome_interno]-[arquivo_ooffice]" );
    $obCmbDocumento->setCampoDesc ( "nome_pro_combo" );
    $obCmbDocumento->preencheCombo ( $rsDocumento );
    $obCmbDocumento->setNull ( false );
}

$obForm = new Form;
$obForm->setAction            ( $pgProc );
$obForm->setTarget            ( "oculto" );

$obFormulario = new Formulario;
$obFormulario->addForm  ( $obForm    );
$obFormulario->setAjuda ( "UC-05.02.10");
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnInscricaoEconomica );
$obFormulario->addHidden( $obHdnEnquadramento );
$obFormulario->addHidden( $obHdnCGM );
$obFormulario->addHidden( $obHdnFuncionalidade );
$obFormulario->addHidden( $obHdnDataAbertura );
$obFormulario->addTitulo( "Dados para Inscrição Econômica" );
$obFormulario->addComponente( $obLblInscricao );
$obFormulario->addComponente( $obLblCGM );
if ($stAcao!="reativar") {
    $obFormulario->addComponente( $obTxtDataBaixa );
    $obFormulario->addComponente( $obBscProcesso  );
    $obFormulario->addComponente( $obTxtMotivo    );
    $obFormulario->addComponente( $obCmbTipo      );
    if ( $obRCEMConfiguracao->getEmissaoCertidaoBaixa() == "sim" ) {
        $obFormulario->addComponente( $obCmbDocumento );
    }
} else {
    $obFormulario->addHidden    ( $obHdnDataInicio  );
    $obFormulario->addComponente( $obLblDataBaixa   );
    $obFormulario->addComponente( $obTxtDataTermino );
}
$obFormulario->Cancelar();
$obFormulario->Show();
