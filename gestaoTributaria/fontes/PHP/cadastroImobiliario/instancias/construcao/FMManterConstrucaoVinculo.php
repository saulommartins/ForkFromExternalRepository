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
    * Página de formulário para inclusão de construção
    * Data de Criação   : 10/01/2005

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues

    * @ignore

    * $Id: FMManterConstrucaoVinculo.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.12
*/

/*
$Log$
Revision 1.11  2006/09/18 10:30:16  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConstrucaoOutros.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMImovel.class.php"           );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLote.class.php"             );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConstrucao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";
include_once( $pgJs );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$obRCIMConstrucao = new RCIMConstrucaoOutros;

$obRCIMImovel = new RCIMImovel( new RCIMLote );
$obRCIMImovel->obRCIMConfiguracao->consultarConfiguracao();
$obRCIMImovel->obRCIMConfiguracao->setCodigoModulo( 12 );
$obRCIMImovel->obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCIMImovel->obRCIMConfiguracao->consultarConfiguracao();
$stMascaraIM = $obRCIMImovel->obRCIMConfiguracao->getMascaraIM();
$obRCIMImovel->obRCIMConfiguracao->consultarMascaraProcesso( $stMascaraProcesso );

if ($stAcao == "incluir") {
    $obRCIMConstrucao->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributos );
}

$obMontaAtributos = new MontaAtributos;
$obMontaAtributos->setTitulo     ( "Atributos"  );
$obMontaAtributos->setName       ( "Atributo_"  );
$obMontaAtributos->setRecordSet  ( $rsAtributos );

//DEFINICAO DOS COMPONENTES
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl"  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao"  );
$obHdnAcao->setValue( $stAcao   );

$obHdnTipo = new Hidden;
$obHdnTipo->setName ( "stTipo"  );
$obHdnTipo->setValue($_REQUEST["boVinculoConstrucao"]);

$obHdnCampoNumDom = new Hidden;
$obHdnCampoNumDom->setName( "stNumeroDomicilio" );
$obHdnCampoNumDom->setID  ( "stNumeroDomicilio" );

$obBscCondominio = new BuscaInner;
$obBscCondominio->setRotulo              ( "Condomínio"                           );
$obBscCondominio->setTitle               ( "Condomínio com o qual a construção está vinculada" );
$obBscCondominio->setNull                ( true                                   );
$obBscCondominio->setId                  ( "campoInner"                           );
$obBscCondominio->obCampoCod->setName    ( "inCodigoCondominio"                   );
$obBscCondominio->obCampoCod->setId      ( "inCodigoCondominio"                   );
$obBscCondominio->obCampoCod->setValue   ( $_REQUEST["inCodigoCondominio"]        );
$obBscCondominio->obCampoCod->obEvento->setOnChange("buscaValor('buscaCondominio');" );
$obBscCondominio->setFuncaoBusca("abrePopUp('".CAM_GT_CIM_POPUPS."condominio/FLProcurarCondominio.php','frm','inCodigoCondominio' ,'campoInner','','".Sessao::getId()."','800','550')" );

$obBscInscricaoMunicipal = new BuscaInner;
$obBscInscricaoMunicipal->setNull             ( false );
$obBscInscricaoMunicipal->setRotulo           ( "Inscrição Imobiliária" );
$obBscInscricaoMunicipal->setTitle            ( "Inscrição imobiliária com a qual a edificação está vinculada" );
$obBscInscricaoMunicipal->obCampoCod->setName ( "inNumeroInscricao" );
$obBscInscricaoMunicipal->obCampoCod->setId   ( "inNumeroInscricao" );
$obBscInscricaoMunicipal->obCampoCod->setSize ( strlen($stMascaraIM) );
$obBscInscricaoMunicipal->obCampoCod->setValue( $inNumeroInscricao );
$obBscInscricaoMunicipal->obCampoCod->setMaxLength ( strlen($stMascaraIM) );
$obBscInscricaoMunicipal->setFuncaoBusca      ( "abrePopUp('".CAM_GT_CIM_POPUPS."imovel/FLProcurarImovel.php','frm','inNumeroInscricao', 'stNumeroDomicilio','todos','".Sessao::getId()."','800','550');");

$obDtConstrucao = new Data;
$obDtConstrucao->setName     ( "stDtConstrucao" );
$obDtConstrucao->setRotulo   ( "Data de Construção" );
$obDtConstrucao->setTitle    ( 'Data de contrução da edificação' );
$obDtConstrucao->setValue    (  $_REQUEST["stDtConstrucao"]    );
$obDtConstrucao->setNull     ( false );
$obDtConstrucao->obEvento->setOnChange( "validaDataConstrucao();" );

$obTxtDescricaoConstrucao = new TextBox;
$obTxtDescricaoConstrucao->setName      ( "stDescricaoConstrucao" );
$obTxtDescricaoConstrucao->setId        ( "stDescricaoConstrucao" );
$obTxtDescricaoConstrucao->setRotulo    ( "Descrição" );
$obTxtDescricaoConstrucao->setNull      ( false );
$obTxtDescricaoConstrucao->setSize      ( 80 );
$obTxtDescricaoConstrucao->setMaxLength ( 160 );

$obTxtAreaConstrucao = new Numerico;
$obTxtAreaConstrucao->setName      ( "flAreaConstrucao" );
$obTxtAreaConstrucao->setRotulo    ( "Área" );
$obTxtAreaConstrucao->setMaxValue  ( 999999999999.99    );
$obTxtAreaConstrucao->setSize      ( 18 );
$obTxtAreaConstrucao->setMaxLength ( 18 );
$obTxtAreaConstrucao->setNull      ( false );
$obTxtAreaConstrucao->setNegativo  ( false );
$obTxtAreaConstrucao->setNaoZero   ( true );
$obTxtAreaConstrucao->setTitle     ( "Área construída em metros quadrados" );

$obBscProcesso = new BuscaInner;
$obBscProcesso->setRotulo ( "Processo" );
$obBscProcesso->setTitle  ( "Número do processo no protocolo que gerou a aprovação do loteamento" );
$obBscProcesso->obCampoCod->setName ("inProcesso");
$obBscProcesso->obCampoCod->setValue( $inProcesso );
$obBscProcesso->obCampoCod->obEvento->setOnChange( "buscaValor('buscaProcesso');" );
$obBscProcesso->obCampoCod->obEvento->setOnKeyUp( "mascaraDinamico('".$stMascaraProcesso."', this, event);" );
$obBscProcesso->setFuncaoBusca( "abrePopUp('".CAM_GA_PROT_POPUPS."processo/FLBuscaProcessos.php','frm','inProcesso','campoInner2','','".Sessao::getId()."','800','550')" );

/** Comentado para dar lugar ao novo campo processo, com pop-up buscaInner
*   Alterado por Lucas Stephanou em  29/03/2005
*/

/*
$obTxtProcesso = new TextBox;
$obTxtProcesso->setName                      ( "inProcesso"                                );
$obTxtProcesso->setSize                      ( strlen($stMascaraProcesso)                  );
$obTxtProcesso->setMaxLength                 ( strlen($stMascaraProcesso)                  );
$obTxtProcesso->setInteiro                   ( false                                       );
$obTxtProcesso->setNull                      ( true                                        );
$obTxtProcesso->setRotulo                    ( "Processo"                                  );
if ($stAcao == "alterar") {
    $obTxtProcesso->setValue                 ( $stProcesso                                 );
    $obTxtProcesso->setPreencheComZeros      ( true                                        );
}
$obTxtProcesso->setTitle                     ( "Processo do protocolo que formaliza esta construção"     );
$obTxtProcesso->obEvento->setOnKeyUp         ( "mascaraDinamico('".$stMascaraProcesso."', this, event);" );
$obTxtProcesso->obEvento->setOnChange        ( "buscaValor('buscaProcesso');"              );
*/

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new FormularioAbas;
$obFormulario->setAjuda             ( "UC-05.01.12" );
$obFormulario->addForm            ( $obForm                   );
$obFormulario->addHidden          ( $obHdnCtrl                );
$obFormulario->addHidden          ( $obHdnAcao                );
$obFormulario->addHidden          ( $obHdnTipo                );
$obFormulario->addHidden          ( $obHdnCampoNumDom         );
$obFormulario->addAba             ( "Construção"              );
$obFormulario->addTitulo          ( "Dados para construção"   );
//$obFormulario->addComponente    ( $obTxtNumeroInscricao     );
if ($_REQUEST["boVinculoConstrucao"] == "condominio") {
    $obFormulario->addComponente  ( $obBscCondominio          );
} else {
    $obFormulario->addComponente  ( $obBscInscricaoMunicipal  );
}
$obFormulario->addComponente      ( $obDtConstrucao           );
$obFormulario->addComponente      ( $obTxtDescricaoConstrucao );
$obFormulario->addComponente      ( $obTxtAreaConstrucao      );
$obFormulario->addComponente      ( $obBscProcesso            );
$obFormulario->addAba             ( "Características"         );
$obMontaAtributos->geraFormulario ( $obFormulario );
$obFormulario->OK();

if ($_REQUEST['boVinculoConstrucao'] == "condominio" AND $_REQUEST["inCodigoCondominio"] AND $stAcao == "incluir") {
    $js .= "buscaValor('buscaCondominio');";
    $obFormulario->setFormFocus( $obTxtDescricaoConstrucao->getId() );
} elseif ($_REQUEST['boVinculoConstrucao'] == "condominio" AND !$_REQUEST["inCodigoCondominio"] AND $stAcao == "incluir") {
    $obFormulario->setFormFocus( $obBscCondominio->obCampoCod->getId() );
} else {
    $obFormulario->setFormFocus( $obBscInscricaoMunicipal->obCampoCod->getId() );
}
SistemaLegado::executaFramePrincipal($js);
$obFormulario->show();
?>
