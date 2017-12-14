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
  * Página de Formulário para emissão de carnê
  * Data de criação : 07/11/2005

  * @author Analista: Fábio Bertoldi
  * @author Programador: Diego Bueno Coelho

    * $Id: FMEmitirCarneGrafica.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.03.11
**/

/*
$Log$
Revision 1.1  2006/11/14 15:57:29  dibueno
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_MON_NEGOCIO."RMONCredito.class.php" );
include_once '../../../../../../gestaoTributaria/fontes/PHP/arrecadacao/classes/componentes/MontaGrupoCredito.class.php';

//Define o nome dos arquivos PHP
$stPrograma      = "EmitirCarne";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FM".$stPrograma.".php";
$pgFormVinculo   = "FM".$stPrograma."Vinculo.php";
$pgProc          = "PR".$stPrograma.".php";
$pgOcul          = "OC".$stPrograma.".php";
$pgJs            = "JS".$stPrograma.".js";

include_once( $pgJs );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

$stNomeArquivoCompleto = $_REQUEST['stNomeArquivo'];
$arNomeArquivo = explode ("/", $_REQUEST['stNomeArquivo'] );
$stNomeArquivo = $arNomeArquivo[count($arNomeArquivo)-1];

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( 'stCtrl' );
$obHdnCtrl->setValue( '' );

$obHdnDownLoadCompleto = new Hidden;
$obHdnDownLoadCompleto->setName ( "nome_arquivo_completo");
$obHdnDownLoadCompleto->setValue( $stNomeArquivoCompleto );

$obHdnDownLoad = new Hidden;
$obHdnDownLoad->setName ( "nome_arquivo");
$obHdnDownLoad->setValue( $stNomeArquivo );

$obForm = new Form;
$obForm->setAction ( $pgList );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm );
$obFormulario->setAjuda     ( "UC-05.04.03" );
$obFormulario->addHidden    ( $obHdnDownLoad );
$obFormulario->addHidden	( $obHdnDownLoadCompleto );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );
$obFormulario->addTitulo    ( "Documentos para Download" );

$obLabelDownLoad = new Label;
$obLabelDownLoad->setValue ( "<b>Arquivo:</b> ".$stNomeArquivo.'&nbsp;&nbsp;' );
$obLabelDownLoad->setName   ( "stNomeArquivo" );

$obBtnDownLoad = new Button;
$obBtnDownLoad->setName               ( "btnDownloadArquivo" );
$obBtnDownLoad->setValue              ( "Download" );
$obBtnDownLoad->setTipo               ( "button" );
$obBtnDownLoad->obEvento->setOnClick  ( "buscaValor('Download','".$stNomeArquivoCompleto."')" );
$obBtnDownLoad->setDisabled           ( false );

$obFormulario->defineBarra ( array( $obLabelDownLoad, $obBtnDownLoad ), 'left', '' );

$obFormulario->show();
