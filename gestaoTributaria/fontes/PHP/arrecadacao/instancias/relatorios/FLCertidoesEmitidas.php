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
    * Página de Filtro para o Relatório de Certidões de Documentos
    * Data de Criação   : 09/04/2012

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Davi Ritter Aroldi

    * @ignore

    * $Id:

    * Casos de uso: uc-05.03.13
*/

/*
$Log$

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoTipoDocumento.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRDocumento.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "CertidoesEmitidas";
$pgFilt = "FL".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma."s.js";

//include_once( $pgJS );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "emitir";
}

$obTARRDocumento = new TARRDocumento;
$obTARRDocumento->recuperaTodos($rsTipoDocumento, "", "descricao");

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction  ( $pgProc );
$obForm->setTarget  ( "telaPrincipal" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName     ( "stCtrl" );
$obHdnCtrl->setValue    ( $stCtrl  );

$obDtData = new Periodo();
$obDtData->setName           ( "stData"             );
$obDtData->setNull           ( true                 );
$obDtData->setValidaExercicio( false                );
// $obDtData->setExercicio      (Sessao::getExercicio());

$obTxtNumDocumento = new TextBox;
$obTxtNumDocumento->setName     ('inNumDocumento'               );
$obTxtNumDocumento->setId       ('inNumDocumento'               );
$obTxtNumDocumento->setRotulo   ('Número do Documento'          );
$obTxtNumDocumento->setTitle    ('informe o Número do Documento');
$obTxtNumDocumento->setInteiro  ( true                          );
$obTxtNumDocumento->setMaxLength( 4                             );
$obTxtNumDocumento->setSize     ( 4                             );
$obTxtNumDocumento->setNull     ( true                          );

$obPopUpCGM = new IPopUpCGM ( $obForm                          );
$obPopUpCGM->setNull        ( true                             );
$obPopUpCGM->setRotulo      ( "CGM"                            );
$obPopUpCGM->setTitle       ( "Informe o CGM do contribuinte"  );

$obCmbTipoDocumento = new Select;
$obCmbTipoDocumento->setName      ( "stTipoDocumento"               );
$obCmbTipoDocumento->setRotulo    ( "Tipo de Documento"             );
$obCmbTipoDocumento->setTitle     ( "Selecione o tipo de documento" );
$obCmbTipoDocumento->addOption    ( ""          , "Selecione"       );
$obCmbTipoDocumento->setCampoDesc ( "descricao"                     );
$obCmbTipoDocumento->setCampoId   ( "cod_tipo_documento"            );
$obCmbTipoDocumento->setNull      ( true                            );
$obCmbTipoDocumento->setStyle     ( "width: 200px"                  );
$obCmbTipoDocumento->preencheCombo( $rsTipoDocumento                );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addHidden        ( $obHdnAcao          );
$obFormulario->addHidden        ( $obHdnCtrl          );
$obFormulario->addTitulo        ( "Dados para Filtro" );

$obFormulario->addComponente    ( $obDtData            );
$obFormulario->addComponente    ( $obTxtNumDocumento   );
$obFormulario->addComponente    ( $obPopUpCGM          );
$obFormulario->addComponente    ( $obCmbTipoDocumento  );

$obFormulario->OK();
$obFormulario->show();

?>
