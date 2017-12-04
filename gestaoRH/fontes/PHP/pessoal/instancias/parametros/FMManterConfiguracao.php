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
* Página de Formulário Configuração de Pessoal
* Data de Criação   : 03/01/2005

* @author Analista: Leandro Oliveira
* @author Desenvolvedor: Lucas Leusin Oaigen

* @ignore

$Revision: 30547 $
$Name$
$Author: souzadl $
$Date: 2008-03-11 12:04:07 -0300 (Ter, 11 Mar 2008) $

* Casos de uso: uc-04.04.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GRH_PES_NEGOCIO."RConfiguracaoPessoal.class.php");
include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      );

$stPrograma = "ManterConfiguracao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obRConfiguracaoPessoal = new RConfiguracaoPessoal;
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

$obRConfiguracaoPessoal->Consultar();

$stMascaraRegistro    = $obRConfiguracaoPessoal->getMascaraRegistro();
$boGeracaoRegistro    = $obRConfiguracaoPessoal->getGeracaoRegistro();
$stMascaraCBO		  = $obRConfiguracaoPessoal->getMascaraCBO();
$inCodGrupoPeriodoTxt = $obRConfiguracaoPessoal->getGrupoPeriodo();
$inCodGrupoPeriodo    = $obRConfiguracaoPessoal->getGrupoPeriodo();
$stContagemInicial    = $obRConfiguracaoPessoal->getContagemInicial();

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
if ( empty( $stAcao ) || $stAcao=="incluir") {
    $stAcao = "alterar";

}

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obTxtMascaraRegistro = new TextBox;
$obTxtMascaraRegistro->setName         ( "stMascaraRegistro" );
$obTxtMascaraRegistro->setValue        ( $stMascaraRegistro );
$obTxtMascaraRegistro->setRotulo       ( "Máscara do Registro" );
$obTxtMascaraRegistro->setTitle  	   ( "Informe a máscara do registro do servidor." );
$obTxtMascaraRegistro->setSize         ( 10 );
$obTxtMascaraRegistro->setMaxLength    ( 10 );
$obTxtMascaraRegistro->setNull         ( false );
$obTxtMascaraRegistro->obEvento->setOnKeyPress("return validaMascaraRegistro(this,event);");
$obTxtMascaraRegistro->obEvento->setOnKeyUp("return retiraCaracteresEspeciais(this);");
$obTxtMascaraRegistro->obEvento->setOnChange("validaMascara(this);");

$obRdbAutomatica = new Radio;
$obRdbAutomatica->setRotulo ( "Geração de Registro" );
$obRdbAutomatica->setName   ( "boGeracaoRegistro" );
$obRdbAutomatica->setValue  ( "A" );
$obRdbAutomatica->setTitle  ( "Informe se a geração de registro será automática ou manual." );
$obRdbAutomatica->setLabel  ( "Automática" );
$obRdbAutomatica->setChecked( ($boGeracaoRegistro=='A' || $boGeracaoRegistro=='') );
$obRdbAutomatica->setNull   ( false );

$obRdbManual = new Radio;
$obRdbManual->setRotulo      ( "Geração de Registro" );
$obRdbManual->setName        ( "boGeracaoRegistro" );
$obRdbManual->setValue       ( "M" );
$obRdbManual->setLabel       ( "Manual" );
$obRdbManual->setChecked     ( ($boGeracaoRegistro=='M') );
$obRdbManual->setNull        ( false );

$obTxtMascaraCBO = new TextBox;
$obTxtMascaraCBO->setName         ( "stMascaraCBO" );
$obTxtMascaraCBO->setValue        ( $stMascaraCBO );
$obTxtMascaraCBO->setRotulo       ( "Máscara para o CBO" );
$obTxtMascaraCBO->setTitle  	   ( "Informe a máscara para o campo CBO." );
$obTxtMascaraCBO->setSize         ( 10 );
$obTxtMascaraCBO->setMaxLength    ( 10 );
$obTxtMascaraCBO->setNull         ( false );
$obTxtMascaraCBO->obEvento->setOnKeyPress("return validaMascaraRegistro(this,event);");
$obTxtMascaraCBO->obEvento->setOnKeyUp("return retiraCaracteresEspeciais(this);");
$obTxtMascaraCBO->obEvento->setOnChange("validaMascara(this);");

$obRdbDtPosse = new Radio;
$obRdbDtPosse->setRotulo ( "Contagem de Tempo" );
$obRdbDtPosse->setName   ( "stContagemInicial" );
$obRdbDtPosse->setValue  ( "dtPosse" );
$obRdbDtPosse->setTitle  ( "Informe a data utilizada pelo sistema para iniciar a contagem de tempo para pagamento de salário, férias, rescisão e certidões." );
$obRdbDtPosse->setLabel  ( "Data da Posse" );
$obRdbDtPosse->setChecked( $stContagemInicial != 'dtNomeacao' );
$obRdbDtPosse->setNull   ( false );

$obRdbDtNomeacao = new Radio;
$obRdbDtNomeacao->setName    ( "stContagemInicial" );
$obRdbDtNomeacao->setValue   ( "dtNomeacao" );
$obRdbDtNomeacao->setLabel   ( "Data da Nomeação" );
$obRdbDtNomeacao->setChecked ( $stContagemInicial == 'dtNomeacao' );
$obRdbDtNomeacao->setNull    ( false );

$obRdbDtAdmissao = new Radio;
$obRdbDtAdmissao->setName    ( "stContagemInicial" );
$obRdbDtAdmissao->setValue   ( "dtAdmissao" );
$obRdbDtAdmissao->setLabel   ( "Data Admissão" );
$obRdbDtAdmissao->setChecked ( $stContagemInicial == 'dtAdmissao' );
$obRdbDtAdmissao->setNull    ( false );

$obBtnOk     = new ok;

$obBtnLimpar = new Button;
$obBtnLimpar->setName               ( "btnLimpar"                                       );
$obBtnLimpar->setValue              ( "Limpar"                                          );
$obBtnLimpar->setTipo               ( "button"                                          );
$obBtnLimpar->obEvento->setOnClick  ( "limparForm();"                                   );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction                  ( $pgProc );
$obForm->setTarget                  ( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm );
$obFormulario->addTitulo ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addHidden            ( $obHdnAcao );
$obFormulario->addHidden            ( $obHdnCtrl );

$obFormulario->addTitulo            ( "Dados para Configuração" );

$obFormulario->addComponente        ( $obTxtMascaraRegistro );
$obFormulario->addComponenteComposto( $obRdbAutomatica, $obRdbManual);
$obFormulario->addComponente        ( $obTxtMascaraCBO );
$obFormulario->agrupaComponentes( array($obRdbDtPosse, $obRdbDtNomeacao, $obRdbDtAdmissao ));
$obFormulario->defineBarra          ( array($obBtnOk,$obBtnLimpar) );
$obFormulario->show                 ();

include_once($pgJs);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
