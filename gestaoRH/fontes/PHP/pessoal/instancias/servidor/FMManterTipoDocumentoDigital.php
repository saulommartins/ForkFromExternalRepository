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
    * Página de Formulario de Inclusao/Alteracao de Tipo Documento Digital
    * Data de criação   : 05/06/2016

    * @author Michel Teixeira

    * @ignore

    $Id: FMManterTipoDocumentoDigital.php 66017 2016-07-07 17:31:31Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterTipoDocumentoDigital";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stAcao = $request->get("stAcao", "incluir");

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obTxtDescricao = new TextBox;
$obTxtDescricao->setRotulo( "Descrição" );
$obTxtDescricao->setName( "stDescricao" );
$obTxtDescricao->setTitle( "Informe a descrição do tipo de documento digital." );
$obTxtDescricao->setNull( false );
$obTxtDescricao->setSize( 60 );
$obTxtDescricao->setMaxLength( 200 );

//define barra
$obOk = new Ok();
$obLimpar  = new Limpar;

$obBtnListaExclusao = new Button;
$obBtnListaExclusao->setName      ( "excluirTipoDocumentoDigital" );
$obBtnListaExclusao->setId        ( "excluirTipoDocumentoDigital" );
$obBtnListaExclusao->setValue     ( "Excluir Tipo de Documento Digital" );
$obBtnListaExclusao->setDefinicao ( "Excluir Tipo de Documento Digital" );
$obBtnListaExclusao->obEvento->setOnClick("window.location = '".CAM_GRH_PES_INSTANCIAS."servidor/LSManterTipoDocumentoDigital.php?stAcao=".$stAcao."';");

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addComponente( $obTxtDescricao );

$obFormulario->defineBarra( array( $obOk, $obLimpar, $obBtnListaExclusao ) );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
