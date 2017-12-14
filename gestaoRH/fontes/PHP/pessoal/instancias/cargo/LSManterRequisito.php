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
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalRequisito.class.php" );

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

$obTPessoalRequisito = new TPessoalRequisito;
$obTPessoalRequisito->recuperaTodos($rsRequisitos, '', ' ORDER BY cod_requisito ');

$obLista = new Table();
$obLista->setRecordset($rsRequisitos);
$obLista->setSummary("Requisitos Cadastrados");

$obLista->Head->addCabecalho( "Código", 40 );
$obLista->Head->addCabecalho( "Descrição", 60 );

$obLista->Body->addCampo( 'cod_requisito', 'C' );
$obLista->Body->addCampo( 'descricao', 'E' );

$obLista->Body->addAcao("excluir","executaFuncaoAjax('%s','&cod_requisito=%s&cod_cargo=%s')",array('excluirRequisito','cod_requisito', $_REQUEST['cod_cargo']));

$obLista->montaHTML();
$stHtml = $obLista->getHtml();
$stHtml = str_replace("\n","",$stHtml);

$obSpnLista = new Span;
$obSpnLista->setValue($stHtml);

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addSpan($obSpnLista);

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
