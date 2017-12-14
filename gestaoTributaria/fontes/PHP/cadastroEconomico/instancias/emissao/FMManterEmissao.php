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

    * $Id: FMManterEmissao.php 63390 2015-08-24 19:17:05Z arthur $

    *Casos de uso: uc-05.02.12
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

if ( $request->get('stAcao') ) {
    $_REQUEST['stAcao'] = "alterar";
}

//Define o nome dos arquivos PHP
$stPrograma    = "ManterEmissao";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";
$pgOcul        = "OC".$stPrograma.".php?".Sessao::getId();

include_once( $pgJs );

Sessao::remove("link");

if ( $request->get('stOrigemFormulario') == 'conceder_licenca' ) {

    $inCodigoLicenca = $request->get('inNumeroLicenca');
    $stNomeArquivoDownload = str_replace ( ' ', '_', $request->get('stNomeDocumento') )."_";
    $stNomeArquivoDownload .= $request->get("inInscricaoEconomica")."_".$inCodigoLicenca."_".$request->get("inExercicio").".odt";

    $arDados[] = array (
        "inInscricaoEconomica"      => $request->get("inInscricaoEconomica"),
        "inCodigoLicenca"           => $request->get('inNumeroLicenca'),
        "inExercicio"               => $request->get('inExercicio'),
        "stTipoLicenca"             => $request->get('stTipoLicenca'),
        "stNomeArquivoDownload"     => $stNomeArquivoDownload,

        "inCodigoTipoDocumento"     => $request->get("inCodigoTipoDocumento"),
        "inCodigoDocumento"         => $request->get("inCodigoDocumento"),
        "stNomeArquivoTemplate"     => $request->get('stNomeArquivo'),
        "stNomeDocumento"           => $request->get('stNomeDocumento')
    );

}

Sessao::write( "dados", $arDados );

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $request->get('stAcao')  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $request->get("stCtrl")  );

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

?>