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
* Arquivo de instância para popup
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 12422 $
$Name$
$Author: cercato $
$Date: 2006-07-10 15:17:40 -0300 (Seg, 10 Jul 2006) $

Casos de uso: uc-01.06.98
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_PROT_MAPEAMENTO."TClassificacao.class.php");
include_once(CAM_GA_PROT_MAPEAMENTO."TAssunto.class.php");
include_once(CAM_GA_PROT_NEGOCIO."RProcesso.class.php"  );
include_once(CAM_GA_ADM_NEGOCIO."RConfiguracaoConfiguracao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "BuscaProcessos";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js" ;

include_once( $pgJS );

Sessao::remove('link');

$campoNom = $_REQUEST['campoNom'];
$campoNum = $_REQUEST['campoNum'];

$obRProcesso = new RProcesso;
$obRConfiguracaoConfiguracao = new RConfiguracaoConfiguracao;

$obRConfiguracaoConfiguracao->setCodModulo( 5 );
$obRConfiguracaoConfiguracao->setExercicio( Sessao::getExercicio() );
$obRConfiguracaoConfiguracao->setParametro( 'mascara_assunto' );
$obRConfiguracaoConfiguracao->consultar();
$stMascaraAssunto = $obRConfiguracaoConfiguracao->getValor();

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgList );

//Define o objeto HIDDEN para armazenar variavel de controle (stCtrl)
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $campoNom );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $campoNum );

$obIFrame2 = new IFrame;
$obIFrame2->setName   ( "telaMensagem");
$obIFrame2->setWidth  ( "100%"        );
$obIFrame2->setHeight ( "50"          );

$obIFrame = new IFrame;
$obIFrame->setName("oculto");
$obIFrame->setWidth("100%");
$obIFrame->setHeight("0");

// monta text para CODIGO DO PROCESSO
$obTxtCGM = new TextBox;
$obTxtCGM->setTitle ( "CGM do interessado pelo processo." );
$obTxtCGM->setRotulo( "CGM" );
$obTxtCGM->setName( "inCGM" );
$obTxtCGM->setValue( isset($inCGM) ? $inCGM : "" );
$obTxtCGM->setSize( 11 );
$obTxtCGM->setMaxLength( 10 );
$obTxtCGM->setInteiro( true );
$obTxtCGM->setNull( true );

$obTxtNome = new TextBox;
$obTxtNome->setTitle ( "Nome do interessado pelo processo." );
$obTxtNome->setRotulo( "Nome" );
$obTxtNome->setName( "stNome" );
$obTxtNome->setValue( isset($stNome) ? $stNome : "");
$obTxtNome->setSize( 51 );
$obTxtNome->setMaxLength( 50 );
$obTxtNome->setNull( true );

// monta combo de CLASSIFICACAO
$obTClassificacao = new TClassificacao;
if ($request->get("tipoBusca") == "recebido") {
    $obTClassificacao->recuperaClassificacaoAlteracao( $rsClassificacao, " WHERE cod_situacao = 3", " GROUP BY sw_classificacao.cod_classificacao" );

    $obHdnCodSituacao = new Hidden;
    $obHdnCodSituacao->setName( "codSituacao" );
    $obHdnCodSituacao->setValue( 3 );
} else {
    $obTClassificacao->recuperaTodos( $rsClassificacao, " ", "cod_classificacao" );
}

$obTxtClassAssunto = new TextBox;
$obTxtClassAssunto->setTitle ( "Classificação e assunto do processo." );
$obTxtClassAssunto->setRotulo ( "Classificação/Assunto" );
$obTxtClassAssunto->setName   ( "inClassAssunto"        );
$obTxtClassAssunto->setValue  ( isset($inClassAssunto) ? $inClassAssunto : "" );
$obTxtClassAssunto->setNull   ( false                   );
$obTxtClassAssunto->obEvento->setOnKeyUp( "mascaraDinamico('".$stMascaraAssunto."', this, event);" );
$obTxtClassAssunto->setSize   ( strlen( $stMascaraAssunto ) + 1 );
$obTxtClassAssunto->setMaxLength( strlen( $stMascaraAssunto ) );
$obTxtClassAssunto->obEvento->setOnChange( "buscaValor( 'preencheCombos' )" );

$obCmbClassificacao = new Select;
$obCmbClassificacao->setName ( "inCodClassificacao" );
$obCmbClassificacao->setValue( isset($inCodClassificacao) ? $inCodClassificacao : "");
$obCmbClassificacao->setRotulo( "Classificação/Assunto" );
$obCmbClassificacao->setNull( true );
$obCmbClassificacao->setStyle("width: 200px");
$obCmbClassificacao->setCampoId("cod_classificacao");
$obCmbClassificacao->setCampoDesc("nom_classificacao");
$obCmbClassificacao->addOption( "", "Selecione Classificação");
$obCmbClassificacao->preencheCombo( $rsClassificacao );
$obCmbClassificacao->obEvento->setOnChange("buscaAssunto();");

// monta combo de Assunto
$obCmbAssunto = new Select;
$obCmbAssunto->setName ( "inCodAssunto" );
$obCmbAssunto->setValue( isset($inCodAssunto) ? $inCodAssunto : "");
$obCmbAssunto->setRotulo( "Classificação/Assunto" );
$obCmbAssunto->setNull( true );
$obCmbAssunto->setStyle("width: 200px");
$obCmbAssunto->addOption( "", "Selecione Assunto");
$obCmbAssunto->obEvento->setOnChange( "buscaValor( 'preencheProcesso' )" );

//Criação do formulário
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

//$obFormulario->obJavaScript->addFuncao( $stFncJavaScript );

$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnCampoNum );
$obFormulario->addHidden( $obHdnCampoNom );

if ($request->get("tipoBusca") == "recebido") {
    $obFormulario->addHidden( $obHdnCodSituacao );
}

$obFormulario->addTitulo( "Dados para Processo" );

$obFormulario->addComponente( $obTxtCGM );
$obFormulario->addComponente( $obTxtNome );
$obFormulario->addComponente( $obTxtClassAssunto );
$obFormulario->addComponente( $obCmbClassificacao );
$obFormulario->addComponente( $obCmbAssunto );

$obFormulario->OK();
$obFormulario->show();
$obIFrame->show();
$obIFrame2->show();
