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
    * Página de Formulario de Inclusao/Alteracao de Requisitos para Cargo
    * Data de criação   : 22/10/2012

    * @author Davi Ritter Aroldi

    * @ignore

    * Caso de uso: uc-04.04.06
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterRequisito";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stAcao = $_REQUEST["stAcao"] ? $_REQUEST["stAcao"] : $_GET["stAcao"];

if (!$stAcao) {
    $stAcao = 'incluir';
}

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obTxtDescricao = new TextBox;
$obTxtDescricao->setRotulo( "Descrição" );
$obTxtDescricao->setName( "stDescricao" );
$obTxtDescricao->setTitle( "Informe a descrição do cargo." );
$obTxtDescricao->setNull( false );
$obTxtDescricao->setSize( 60 );
$obTxtDescricao->setMaxLength( 200 );

//define barra
$obOk  = new Ok($boBlock);
$obLimpar  = new Limpar;

$obBtnListaExclusao = new Button;
$obBtnListaExclusao->setName      ( "excluirRequisito" );
$obBtnListaExclusao->setId        ( "excluirRequisito" );
$obBtnListaExclusao->setValue     ( "Excluir Requisito" );
$obBtnListaExclusao->setStyle     ( "width: 120px" );
$obBtnListaExclusao->setDefinicao ( "Excluir Requisito" );
$obBtnListaExclusao->obEvento->setOnClick("window.location = '".CAM_GRH_PES_INSTANCIAS."cargo/LSManterRequisito.php?stAcao=".$stAcao."&cod_cargo=".$_REQUEST['cod_cargo']."';");

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addComponente( $obTxtDescricao );

// $obFormulario->ok();
$obFormulario->defineBarra( array( $obOk, $obLimpar, $obBtnListaExclusao ) );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
