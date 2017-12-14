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
    * Página de Formulario de Inclusao/Alteracao de Autoridade

    * Data de Criação   : 14/09/2006

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @ignore

    * $Id: FMManterAutoridade.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.04.08

*/

/*
$Log$
Revision 1.2  2006/09/29 14:48:45  cercato
correcao da obrigatoriedade do campo matricula.

Revision 1.1  2006/09/18 17:18:29  cercato
formularios da autoridade de acordo com interface abstrata.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GA_NORMAS_CLASSES."componentes/IPopUpNorma.class.php" );
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroContrato.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATAutoridade.class.php" );

if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "incluir";
}

//Define o nome dos arquivos PHP
$stPrograma    = "ManterAutoridade";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php?".Sessao::getId();
$pgJs          = "JS".$stPrograma.".js";

include_once( $pgJs );

$obIPopUpNorma = new IPopUpNorma;
$obIPopUpNorma->obInnerNorma->setTitle( "Informe a fundamentação legal que regulamenta o cadastro da autoridade." );
$obIPopUpNorma->obInnerNorma->setRotulo( "Fundamentação Legal" );
if ($_REQUEST['stAcao'] == "alterar") {
    $obIPopUpNorma->setCodNorma( $_REQUEST["inCodNorma"] );
}

$obIFiltroContrato = new IFiltroContrato;
$obIFiltroContrato->obIContratoDigitoVerificador->setNull( false );
$obIFiltroContrato->setInformacoesFuncao( true );
$obIFiltroContrato->setTituloFormulario( "" );

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $_REQUEST['stAcao']  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $_REQUEST['stCtrl']  );

//tipo de autoridade
$obRdbProcurador = new Radio;
$obRdbProcurador->setRotulo   ( "Tipo de Autoridade" );
$obRdbProcurador->setTitle    ( "Informe o tipo de autoridade a ser cadastrada." );
$obRdbProcurador->setName     ( "stTipoAutoridade" );
$obRdbProcurador->setLabel    ( "Procurador Municipal" );
$obRdbProcurador->setValue    ( "procurador" );
$obRdbProcurador->setNull     ( false );
$obRdbProcurador->obEvento->setOnChange( "ajaxJavaScriptSincrono( '".$pgOcul."?".Sessao::getId()."&stTipoAutoridade='+this.value, 'tipoAutoridade' );" );

//Tipo de autoridade
$obRdbAutoridade = new Radio;
$obRdbAutoridade->setRotulo   ( "Tipo de Autoridade" );
$obRdbAutoridade->setTitle    ( "Informe o tipo de autoridade a ser cadastrada." );
$obRdbAutoridade->setName     ( "stTipoAutoridade" );
$obRdbAutoridade->setLabel    ( "Autoridade Competente" );
$obRdbAutoridade->setValue    ( "autoridade" );
$obRdbAutoridade->setNull     ( false );
$obRdbAutoridade->obEvento->setOnChange( "ajaxJavaScriptSincrono( '".$pgOcul."?".Sessao::getId()."&stTipoAutoridade='+this.value, 'tipoAutoridade' );" );

//Assinatura em formato digital
$obFilAssinatura = new FileBox;
$obFilAssinatura->setRotulo        ( "Assinatura em Formato Digital" );
$obFilAssinatura->setTitle         ( "Selecione o arquivo que contém a assinatura do servidor em formato digital." );
$obFilAssinatura->setName          ( "stCaminhoAssinatura" );
$obFilAssinatura->setId            ( "stCaminhoAssinatura" );
$obFilAssinatura->setSize          ( 40 );
$obFilAssinatura->setMaxLength     ( 100 );
if ($_REQUEST['stAcao'] == "incluir") {
    $obFilAssinatura->setNull          ( false );
} else {
    $obFilAssinatura->setNull          ( true );
}

$obSpnAutoridade = new Span;
$obSpnAutoridade->setID("spnAutoridade");

if ($_REQUEST['stAcao'] == "alterar") {
    $obHdnTipoAutoridade =  new Hidden;
    $obHdnTipoAutoridade->setName   ( "stTipoAutoridade" );
    $obHdnTipoAutoridade->setValue  ( $_REQUEST["stTipo"] );

    $obHdnCodAutoridade =  new Hidden;
    $obHdnCodAutoridade->setName   ( "inCodAutoridade" );
    $obHdnCodAutoridade->setValue  ( $_REQUEST["inCodAutoridade"] );

    $obLblCodAutoridade = new Label;
    $obLblCodAutoridade->setRotulo    ( "Código" );
    $obLblCodAutoridade->setName      ( "inCodAut");
    $obLblCodAutoridade->setValue     ( $_REQUEST["inCodAutoridade"] );
    $obLblCodAutoridade->setTitle     ( "Código da autoridade." );

    $obLblTipo = new Label;
    $obLblTipo->setRotulo    ( "Tipo de Autoridade" );
    $obLblTipo->setName      ( "stTipo");
    $obLblTipo->setValue     ( $_REQUEST["stTipo"] );
    $obLblTipo->setTitle     ( "Tipo de autoridade." );

    $obHdnCGM =  new Hidden;
    $obHdnCGM->setName   ( "inCodCGM" );
    $obHdnCGM->setValue  ( $_REQUEST["stNumCGM"] );

    $obLblServidor = new Label;
    $obLblServidor->setRotulo    ( "Servidor" );
    $obLblServidor->setName      ( "stServidor" );
    $obLblServidor->setValue     ( $_REQUEST["stNumCGM"]." - ".$_REQUEST["stNomCGM"] );
    $obLblServidor->setTitle     ( "Servidor." );

    //buscando lista de matriculas possiveis para o servidor
    $stFiltro = " WHERE ps.numcgm = ".$_REQUEST["stNumCGM"];

    $obTDATAutoridade = new TDATAutoridade;
    $obTDATAutoridade->recuperaListaMatricula( $rsListaMatricula, $stFiltro, " ORDER BY pc.registro " );

    Sessao::write('lstMatricula', $rsListaMatricula);

    $stOnChange = "ajaxJavaScript('".$pgOcul."&inMatricula='+this.value,'preencheMatricula');";

    $obCmbMatricula = new Select;
    $obCmbMatricula->setName     ( "inMatricula" );
    $obCmbMatricula->setRotulo   ( "Matrícula" );
    $obCmbMatricula->setTitle    ( "Selecione a matrícula." );
    $obCmbMatricula->setCampoDesc ( "registro" );
    $obCmbMatricula->setCampoID ( "registro" );
    $obCmbMatricula->setNull ( false );
    $obCmbMatricula->setStyle ( "width: 200px" );
    $obCmbMatricula->preencheCombo ( $rsListaMatricula );
    $obCmbMatricula->setValue ( $_REQUEST["inMatricula"] );
    $obCmbMatricula->obEvento->setOnChange( $stOnChange );

    $obLblInfo = new Label;
    $obLblInfo->setRotulo    ( "Informações da Função" );
    $obLblInfo->setName      ( "stInfo" );
    $obLblInfo->setId        ( "stInfo" );
    $obLblInfo->setValue     ( $_REQUEST["stDescricao"]." - ".$_REQUEST["stVigencia"] );
    $obLblInfo->setTitle     ( "Informações da função." );
}

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgProc );
$obForm->settarget ( "oculto" );
$obForm->setEncType ( "multipart/form-data" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->setAjuda      ( "UC-05.04.08" );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addTitulo     ( "Dados para Autoridade" );

if ($_REQUEST['stAcao'] == "incluir") {
    $obFormulario->addComponenteComposto ( $obRdbProcurador, $obRdbAutoridade );
    $obIFiltroContrato->geraFormulario( $obFormulario );
} else {
    $obFormulario->addHidden     ( $obHdnTipoAutoridade );
    $obFormulario->addHidden     ( $obHdnCodAutoridade );
    $obFormulario->addHidden     ( $obHdnCGM );
    $obFormulario->addComponente ( $obLblCodAutoridade );
    $obFormulario->addComponente ( $obLblTipo );
    $obFormulario->addComponente ( $obLblServidor );
    $obFormulario->addComponente ( $obCmbMatricula );
    $obFormulario->addComponente ( $obLblInfo );
}

$obIPopUpNorma->geraFormulario ( $obFormulario );
$obFormulario->addComponente ( $obFilAssinatura );
$obFormulario->addSpan ( $obSpnAutoridade );

if ($_REQUEST['stAcao'] == "incluir") {
    $obFormulario->Ok ();
} else {
    $obFormulario->Cancelar ();
}

$obFormulario->show();
if ($_REQUEST["stTipo"] == "Procurador Municipal") {
    sistemaLegado::executaFrameOculto("ajaxJavaScript('".$pgOcul."&stOAB=".$_REQUEST['stOAB']."&inCodUF=".$_REQUEST['inCodUF']."','preencheOAB');");
}
