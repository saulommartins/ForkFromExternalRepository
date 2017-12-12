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
    * Página de filtro para o cadastro de face de imóvel
    * Data de Criação   : 04/01/2005

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    * $Id: FLProcurarImovel.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.09
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLocalizacao.class.php" );
include_once ( CAM_FW_URBEM."MontaLocalizacao.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMImovel.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarImovel";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FMManterImovel.php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once($pgJs);

//destroi arrays de sessao que armazenam os dados do FILTRO
//unset( $sessao->filtro );
//unset( $sessao->link );

$obRCIMConfiguracao = new RCIMConfiguracao;
$obRCIMConfiguracao->setCodigoModulo( 12 );
$obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCIMConfiguracao->consultarConfiguracao();
$stMascaraLote = $obRCIMConfiguracao->getMascaraLote();

$obRCIMLocalizacao = new RCIMLocalizacao;
$obRCIMLocalizacao->recuperaVigenciaAtual( $rsVigenciaAtual );
$CodigoVigencia = $rsVigenciaAtual->getCampo( "cod_vigencia" ) ;
$obRCIMLocalizacao->setCodigoVigencia( $CodigoVigencia );
$obErro = $obRCIMLocalizacao->listarNiveis( $rsListaNivel );

$numNiveis = 0;
while ( !$rsListaNivel->eof() ) {
    if (isset($stMascara)) {
        $stMascara .= $rsListaNivel->getCampo("mascara").".";
    } else {
        $stMascara = $rsListaNivel->getCampo("mascara").".";
    }
    $rsListaNivel->proximo();
    $numNiveis++;
}
$stMascara = substr( $stMascara, 0 , strlen($stMascara) - 1 );

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgList );
//$obForm->setTarget('oculto' );

$stAcao = $request->get("stAcao");

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto HIDDEN para armazenar variavel de controle (stCtrl)
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $_REQUEST['campoNom'] );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $_REQUEST['campoNum'] );

//Define o objeto para o código do logradouro
$obTCIMImovel = new TCIMImovel;
$obTAdministracaoConfiguracao = new TAdministracaoConfiguracao;
$obTAdministracaoConfiguracao->setDado( "cod_modulo", 12 );
$obTAdministracaoConfiguracao->setDado( "exercicio" , Sessao::getExercicio() );
$obTAdministracaoConfiguracao->setDado( "parametro" , "numero_inscricao");
$obTAdministracaoConfiguracao->recuperaPorChave( $rsConfiguracao );
if ( $rsConfiguracao->getCampo("valor") == false ) {
    $obTAdministracaoConfiguracao->setDado( "parametro" , "mascara_inscricao");
    $obTAdministracaoConfiguracao->recuperaPorChave( $rsConfiguracao );

    $stMascaraImovel = $rsConfiguracao->getCampo( "valor" );
} else {
    $stMascaraImovel = "";
    $obTCIMImovel->recuperaMaxInscricaoImobiliario( $rsConfiguracao );
    for ( $inX=0; $inX< strlen( $rsConfiguracao->getCampo( "total" ) ); $inX++) {
        $stMascaraImovel .= "9";
    }
}

$obIntCodLote = new TextBox;
$obIntCodLote->setName ( "inCodImob" );
$obIntCodLote->setRotulo ( "Inscrição Imobiliária" );
$obIntCodLote->setTitle ( "Informe a inscrição imobiliária do imóvel." );
$obIntCodLote->setValue ( $request->get("inCodImob") );
$obIntCodLote->setInteiro ( true );
$obIntCodLote->setSize ( strlen( $stMascaraImovel ) );
$obIntCodLote->setMaxLength ( strlen( $stMascaraImovel ) );
$obIntCodLote->setMascara ( $stMascaraImovel );

$obTxtNumeroLote = new TextBox;
$obTxtNumeroLote->setName      ( "stNumeroLote"   );
$obTxtNumeroLote->setMaxLength ( strlen( $stMascaraLote ) );
$obTxtNumeroLote->setSize      ( strlen( $stMascaraLote ) );
$obTxtNumeroLote->setTitle     ( "Informe o número do lote do imóvel." );
$obTxtNumeroLote->setRotulo    ( "Número do Lote" );
$obTxtNumeroLote->obEvento->setOnKeyUp("mascaraDinamico('".$stMascaraLote."', this, event);");

$obCodLocalizacao = new Hidden;
$obCodLocalizacao->setName( "inCodigoLocalizacao" );
$obCodLocalizacao->setValue( "" );

$obHdnNumNiveis = new Hidden;
$obHdnNumNiveis->setName( "inNumNiveis" );
$obHdnNumNiveis->setValue( $request->get("numNiveis") );

$obIFrame = new IFrame;
$obIFrame->setName("oculto");
$obIFrame->setWidth("100%");
$obIFrame->setHeight("100");

$obIFrame2 = new IFrame;
$obIFrame2->setName("telaMensagem");
$obIFrame2->setWidth("100%");
$obIFrame2->setHeight("50");

//Criação do formulário
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addHidden             ( $obHdnCtrl          );
$obFormulario->addHidden             ( $obHdnAcao          );
$obFormulario->addHidden             ( $obHdnCampoNom      );
$obFormulario->addHidden             ( $obHdnCampoNum      );
$obFormulario->addHidden             ( $obCodLocalizacao   );
$obFormulario->addHidden             ( $obHdnNumNiveis     );
$obFormulario->addTitulo             ( "Dados para Imóvel" );
$obFormulario->addComponente         ( $obIntCodLote       );
$obFormulario->addComponente         ( $obTxtNumeroLote    );

$obMontaLocalizacao = new MontaLocalizacao;
$obMontaLocalizacao->geraFormulario  ( $obFormulario );

$obFormulario->ok();
$obFormulario->show();
$obIFrame2->show();
$obIFrame->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
