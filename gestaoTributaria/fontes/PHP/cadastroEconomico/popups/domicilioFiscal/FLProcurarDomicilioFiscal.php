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
    * Página de filtro para o popup de Domicilio Fiscal
    * Data de Criação   : 27/07/2005

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Marcelo B. Paulino

    * @ignore

    * $Id: FLProcurarDomicilioFiscal.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.10
*/

/*
$Log$
Revision 1.4  2006/09/15 13:47:40  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php"     );
include_once( CAM_GT_CIM_NEGOCIO."RCIMBairro.class.php"           );
include_once( CAM_FW_URBEM."MontaLocalizacao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarDomicilioFiscal";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FMManterImovel.php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once($pgJs);

$obRCIMConfiguracao = new RCIMConfiguracao;
$obRCIMConfiguracao->setCodigoModulo( 12 );
$obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCIMConfiguracao->consultarConfiguracao();
$stMascaraLote = $obRCIMConfiguracao->getMascaraLote();
$obRCIMConfiguracao->listaDadosMunicipio( $arConfiguracao );

$obRCIMBairro   = new RCIMBairro;
$inCodUF        = $_REQUEST["inCodUF"] ? $_REQUEST["inCodUF"] : $arConfiguracao["cod_uf"];
$inCodMunicipio = $_REQUEST["inCodMunicipio"] ? $_REQUEST["inCodMunicipio"] : $arConfiguracao["cod_municipio"];
$obRCIMBairro->setCodigoUF       ( $inCodUF        );
$obRCIMBairro->setCodigoMunicipio( $inCodMunicipio );
$obRCIMBairro->listarBairros     ( $rsBairros      );

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgList );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $_REQUEST["stAcao"] );

//Define o objeto HIDDEN para armazenar variavel de controle (stCtrl)
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $_REQUEST["campoNom"] );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $_REQUEST["campoNum"] );

$obIntCodLote = new TextBox;
$obIntCodLote->setName  ( "inCodImob" );
$obIntCodLote->setRotulo( "Inscrição Imobiliária" );
$obIntCodLote->setValue ( $_REQUEST["inCodImob"] );

$obTxtNumeroLote = new TextBox;
$obTxtNumeroLote->setName      ( "stNumeroLote"   );
$obTxtNumeroLote->setMaxLength ( strlen( $stMascaraLote ) );
$obTxtNumeroLote->setSize      ( strlen( $stMascaraLote ) );
$obTxtNumeroLote->setRotulo    ( "Número do Lote" );
$obTxtNumeroLote->obEvento->setOnKeyUp("mascaraDinamico('".$stMascaraLote."', this, event);");

$obIFrame = new IFrame;
$obIFrame->setName("oculto");
$obIFrame->setWidth("100%");
$obIFrame->setHeight("0");

$obIFrame2 = new IFrame;
$obIFrame2->setName("telaMensagem");
$obIFrame2->setWidth("100%");
$obIFrame2->setHeight("50");

$obTxtCodBairro = new TextBox;
$obTxtCodBairro->setRotulo    ( "Bairro"               );
$obTxtCodBairro->setName      ( "inCodBairro"          );
$obTxtCodBairro->setValue     ( $_REQUEST["inCodBairro"] );
$obTxtCodBairro->setSize      ( 8                      );
$obTxtCodBairro->setMaxLength ( 8                      );
$obTxtCodBairro->setNull      ( true                   );
$obTxtCodBairro->setInteiro   ( true                   );

$obCmbBairro = new Select;
$obCmbBairro->setName         ( "cmbBairro"            );
$obCmbBairro->addOption       ( "", "Selecione"        );
$obCmbBairro->setCampoId      ( "cod_bairro"           );
$obCmbBairro->setCampoDesc    ( "nom_bairro"           );
$obCmbBairro->preencheCombo   ( $rsBairros             );
$obCmbBairro->setValue        ( $_REQUEST["inCodBairro"] );
$obCmbBairro->setNull         ( true                   );
$obCmbBairro->setStyle        ( "width: 220px"         );

$obTxtLogradouro = new TextBox;
$obTxtLogradouro->setRotulo   ( "Logradouro"      );
$obTxtLogradouro->setName     ( "stNomLogradouro" );
$obTxtLogradouro->setValue    ( $_REQUEST["stNomLogradouro"]  );
$obTxtLogradouro->setSize     ( 80                );
$obTxtLogradouro->setMaxLength( 80                );
$obTxtLogradouro->setNull     ( true              );

$obTxtNumero = new TextBox;
$obTxtNumero->setRotulo       ( "Número"   );
$obTxtNumero->setName         ( "inNumero" );
$obTxtNumero->setValue        ( $_REQUEST["inNumero"] );
$obTxtNumero->setSize         ( 8          );
$obTxtNumero->setMaxLength    ( 8          );
$obTxtNumero->setNull         ( true       );
$obTxtNumero->setInteiro      ( true       );

$obTxtProprietario = new TextBox;
$obTxtProprietario->setName     ( "stNomCGM"     );
$obTxtProprietario->setRotulo   ( "Proprietário" );
$obTxtProprietario->setValue    ( $_REQUEST["stNomCGM"] );
$obTxtProprietario->setSize     ( 80             );
$obTxtProprietario->setMaxLength( 80             );

$obBtnOK = new OK;
$obBtnOK->obEvento->setOnClick    ( "Salvar();" );

$onBtnLimpar = new Limpar;
$onBtnLimpar->obEvento->setOnClick( "Limpar();" );

//Criação do formulário
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addHidden             ( $obHdnCtrl          );
$obFormulario->addHidden             ( $obHdnAcao          );
$obFormulario->addHidden             ( $obHdnCampoNom      );
$obFormulario->addHidden             ( $obHdnCampoNum      );
$obFormulario->addTitulo             ( "Dados para domicílio fiscal" );
$obFormulario->addComponente         ( $obIntCodLote       );
$obFormulario->addComponente         ( $obTxtNumeroLote    );
$obFormulario->addComponenteComposto ( $obTxtCodBairro, $obCmbBairro );
$obFormulario->addComponente         ( $obTxtLogradouro    );
$obFormulario->addComponente         ( $obTxtNumero        );
$obFormulario->addComponente         ( $obTxtProprietario  );
$obFormulario->defineBarra( array( $obBtnOK , $onBtnLimpar ) );

$obFormulario->show();
$obIFrame->show();
$obIFrame2->show();
