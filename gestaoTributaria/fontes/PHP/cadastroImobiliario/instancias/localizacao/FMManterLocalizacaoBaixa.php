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
    * Página de baixa para o cadastro de localização
    * Data de Criação   : 19/10/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @ignore

    * $Id: FMManterLocalizacaoBaixa.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.03
*/

/*
$Log$
Revision 1.10  2006/09/18 10:30:48  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLocalizacao.class.php" );
include_once ( CAM_GT_CIM_COMPONENTES."MontaLocalizacao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterLocalizacao";
$pgFilt      = "FL".$stPrograma.".php";
$pgList      = "LS".$stPrograma.".php";
$pgForm      = "FM".$stPrograma.".php";
$pgFormNivel = "FM".$stPrograma."Nivel.php";
$pgFormBaixa = "FM".$stPrograma."Baixa.php";
$pgProc      = "PR".$stPrograma.".php";
$pgOcul      = "OC".$stPrograma.".php";
$pgJS        = "JS".$stPrograma.".js";

include_once ($pgJS);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "incluir";
}

//$obRCIMLocalizacao = new RCIMLocalizacao;
$inCodigoVigencia    = $_REQUEST["inCodigoVigencia"];
$inCodigoNivel       = $_REQUEST["inCodigoNivel"];
$inCodigoLocalizacao = $_REQUEST["inCodigoLocalizacao"];
$stValorComposto     = $_REQUEST["stValorComposto"];
$stValorReduzido     = $_REQUEST["stValorReduzido"];

/*
$obRCIMLocalizacao->setCodigoVigencia    ( $inCodigoVigencia    );
$obRCIMLocalizacao->setCodigoNivel       ( $inCodigoNivel       );
$obRCIMLocalizacao->setCodigoLocalizacao ( $inCodigoLocalizacao );

$obRCIMLocalizacao->consultarLocalizacao();

$stNomeNivel        = $obRCIMLocalizacao->getNomeNivel();
$stMascara          = $obRCIMLocalizacao->getMascara();
$inValorLocalizacao = $obRCIMLocalizacao->getValor();
*/

$stNomeNivel = $_REQUEST["stNomeNivel"];

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $_REQUEST['stAcao'] );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $_REQUEST['stCtrl'] );

$obHdnCodigoNivel = new Hidden;
$obHdnCodigoNivel->setName  ( "inCodigoNivel" );
$obHdnCodigoNivel->setValue ( $inCodigoNivel  );

$obHdnCodigoVigencia = new Hidden;
$obHdnCodigoVigencia->setName  ( "inCodigoVigencia" );
$obHdnCodigoVigencia->setvalue ( $inCodigoVigencia );

$obHdnCodigoLocalizacao = new Hidden;
$obHdnCodigoLocalizacao->setName  ( "inCodigoLocalizacao" );
$obHdnCodigoLocalizacao->setValue ( $inCodigoLocalizacao  );

$obHdnValorReduzido = new Hidden;
$obHdnValorReduzido->setName  ( "stValorReduzido" );
$obHdnValorReduzido->setValue ( $stValorReduzido  );

$obHdnNomeLocalizacao = new Hidden;
$obHdnNomeLocalizacao->setName      ( "stNomeLocalizacao" );
$obHdnNomeLocalizacao->setValue     ( $_REQUEST['stNomeLocalizacao']  );

$obLbNomeNivel = new Label;
$obLbNomeNivel->setRotulo( "Nível" );
$obLbNomeNivel->setValue( $stNomeNivel );

$obLblNomeLocalizacao = new Label;
$obLblNomeLocalizacao->setName      ( "stNomeLocalizacao" );
$obLblNomeLocalizacao->setRotulo    ( "Nome"              );
$obLblNomeLocalizacao->setValue     ( $_REQUEST['stNomeLocalizacao']  );

$obLblValorComposto = new Label;
$obLblValorComposto->setRotulo ( "Código" );
$obLblValorComposto->setValue  ( $stValorComposto );

$obTxtJustificativa = new TextArea;
if ($_REQUEST['stAcao'] == "reativar") {
    $obTxtJustificativa->setName   ( "stJustReat" );
    $obTxtJustificativa->setId     ( "stJustReat" );
    $obTxtJustificativa->setTitle  ( "Motivo da reativação" );
    $obTxtJustificativa->setRotulo ( "Motivo da Reativação" );
} else {
    $obTxtJustificativa->setName   ( "stJustificativa" );
    $obTxtJustificativa->setId     ( "stJustificativa" );
    $obTxtJustificativa->setTitle  ( "Motivo da baixa" );
    $obTxtJustificativa->setRotulo ( "Motivo da Baixa" );
}
$obTxtJustificativa->setCols   ( 30 );
$obTxtJustificativa->setRows   ( 5 );
$obTxtJustificativa->setNull   ( false );

$obLblJustificativa = new Label;
$obLblJustificativa->setRotulo ( "Motivo da Baixa" );
$obLblJustificativa->setTitle  ( "Motivo da baixa" );
$obLblJustificativa->setValue  ( $_REQUEST["stJustificativa"] );

$obHdnJustificativa = new Hidden;
$obHdnJustificativa->setName  ( "stJustificativa" );
$obHdnJustificativa->setValue ( $_REQUEST["stJustificativa"] );

$obHdnTimestamp = new Hidden;
$obHdnTimestamp->setName  ( "stTimeStamp" );
$obHdnTimestamp->setValue ( $_REQUEST["stTimeStamp"] );

$obLblDtInicio = new Label;
$obLblDtInicio->setRotulo ( "Data de Baixa" );
$obLblDtInicio->setTitle  ( "Data de Baixa" );
$obLblDtInicio->setValue  ( $_REQUEST["stDTInicio"] );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto"     );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->setAjuda ( "UC-05.01.03" );
$obFormulario->addTitulo     ( "Dados para nível" );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addHidden     ( $obHdnCodigoNivel    );
$obFormulario->addHidden     ( $obHdnCodigoVigencia );
$obFormulario->addHidden     ( $obHdnCodigoLocalizacao );
$obFormulario->addHidden     ( $obHdnValorReduzido     );
$obFormulario->addHidden     ( $obHdnNomeLocalizacao   );
$obFormulario->addComponente ( $obLbNomeNivel          );
$obFormulario->addComponente ( $obLblValorComposto     );
$obFormulario->addComponente ( $obLblNomeLocalizacao   );
if ($_REQUEST['stAcao'] == "reativar") {
    $obFormulario->addComponente ( $obLblDtInicio          );
    $obFormulario->addComponente ( $obLblJustificativa     );
    $obFormulario->addHidden     ( $obHdnJustificativa     );
    $obFormulario->addHidden     ( $obHdnTimestamp         );
}

$obFormulario->addComponente ( $obTxtJustificativa     );
$obFormulario->Cancelar();
$obFormulario->setFormFocus  ( $obTxtJustificativa->getId() );
$obFormulario->show();
?>
