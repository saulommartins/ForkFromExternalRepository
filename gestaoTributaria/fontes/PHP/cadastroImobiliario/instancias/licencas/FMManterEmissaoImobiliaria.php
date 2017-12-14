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
    * Página de Formulario de Emissao

    * Data de Criação   : 11/10/2006

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho
    * @ignore

    * $Id: FMManterEmissao.php 32939 2008-09-03 21:14:50Z domluc $

    *Casos de uso: uc-05.02.12
*/

/*
$Log$
Revision 1.5  2007/03/05 13:12:21  dibueno
Bug #7676#

Revision 1.4  2007/03/02 14:46:00  dibueno
Bug #7676#

Revision 1.3  2006/12/14 12:58:26  dibueno
Modificações para listagem de licenças

Revision 1.2  2006/11/22 15:58:09  dibueno
Melhoria no procedimento de geração de alvará

Revision 1.1  2006/10/23 16:12:21  dibueno
*** empty log message ***
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "alterar";
}

//Define o nome dos arquivos PHP
$stPrograma    = "ManterEmissaoImobiliaria";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";
$pgOcul        = "OC".$stPrograma.".php?".Sessao::getId();

include_once( $pgJs );

Sessao::remove("link");

if ($_REQUEST['stOrigemFormulario'] == 'conceder_licenca') {

    $inCodigoLicenca = $_REQUEST['inNumeroLicenca'];
    $stNomeArquivoDownload = str_replace ( ' ', '_', $_REQUEST['stNomeDocumento'] )."_";
    $stNomeArquivoDownload .= $_REQUEST["inInscricaoImobiliaria"]."_".$inCodigoLicenca."_".$_REQUEST["inExercicio"].".odt";

    $arDados[] = array (
        "inInscricaoImobiliaria"    => $_REQUEST["inInscricaoImobiliaria"],
        "inCodigoLicenca"           => $_REQUEST['inNumeroLicenca'],
        "inExercicio"               => $_REQUEST['inExercicio'],
        "stTipoLicenca"             => $_REQUEST['stTipoLicenca'],
        "stNomeArquivoDownload"     => $stNomeArquivoDownload,
        "inCodigoTipoDocumento"     => $_REQUEST["inCodigoTipoDocumento"],
        "inCodigoDocumento"         => $_REQUEST["inCodigoDocumento"],
        "stNomeArquivoTemplate"     => $_REQUEST['stNomeArquivo'],
        "stNomeDocumento"           => $_REQUEST['stNomeDocumento'],
        "flAreaLicenca"             => $_REQUEST['flAreaLicenca'],
        "stProcesso"                => $_REQUEST['stProcesso'],
        "inCodAtributoEstrutura"    => $_REQUEST['inCodAtributoEstrutura'],
        "inCodAtributo"    => $_REQUEST['inCodAtributo'],
        "stTipoLicencaUnidade"      => $_REQUEST['stTipoLicenca'],
        "inCodConstrucao"           => $_REQUEST['inCodConstrucao']
    );

}

Sessao::write( "dados", $arDados );

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $_REQUEST['stAcao']  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $_REQUEST["stCtrl"]  );

$obHdnDownLoad = new Hidden;
$obHdnDownLoad->setName   ( "HdnQual" );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgList );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->setAjuda      ( "UC-05.04.03" );
$obFormulario->addHidden     ( $obHdnDownLoad );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addTitulo     ( "Documentos para Download" );

$inTotal = 1;
for ($inX=0; $inX<$inTotal; $inX++) {
    $stDownLoadName = "stArq".$inX;
    $stLblDownLoadName = "stLBArq".$inX;
    $stBtnDownLoadName = "stBtnArq".$inX;

    $obLabelDownLoad = new Label;
    $obLabelDownLoad->setValue  ( $stNomeArquivoDownload );
    $obLabelDownLoad->setName   ( $stLblDownLoadName );

    $obBtnDownLoad = new Button;
    $obBtnDownLoad->setName               ( $stBtnDownLoadName );
    $obBtnDownLoad->setValue              ( "Download" );
    $obBtnDownLoad->setTipo               ( "button" );
    $obBtnDownLoad->obEvento->setOnClick  ( "buscaValor('Download','".$inX."')" );
    $obBtnDownLoad->setDisabled           ( false );

    $obFormulario->defineBarra ( array( $obLabelDownLoad, $obBtnDownLoad ), 'left', '' );
}
$obFormulario->show();
