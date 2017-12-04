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
* Página de Filtro de Procura de Recurso
* Data de Criação   : 26/09/2008

* @author Analista: Heleno Santos
* @author Desenvolvedor: Fellipe Esteves dos Santos
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php");

//Define o nome dos arquivos PHP
$stPrograma	= "ProcurarRecurso";
$pgFilt 	= "FL".$stPrograma.".php";
$pgList 	= "LS".$stPrograma.".php";
$pgOcul 	= "OC".$stPrograma.".php";
$pgJS   	= "JS".$stPrograma.".js";

$obRConfiguracaoOrcamento = new ROrcamentoConfiguracao;
$obRConfiguracaoOrcamento->consultarConfiguracao();

$sessao->link = "";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

//Instancia o formulário
$obForm = new Form;
$obForm->setAction($pgList);

// Definicao dos objetos hidden
$obHdnAcao = new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue($stAcao);

$obHdnForm = new Hidden();
$obHdnForm->setName( "nomForm" );
$obHdnForm->setValue( $_REQUEST['nomForm'] );

$obHdnCampoNum = new Hidden();
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $_REQUEST[ 'campoNum' ] );

$obHdnCampoNom = new Hidden();
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $_REQUEST[ 'campoNom' ] );

$obHdnCampoTipo = new Hidden();
$obHdnCampoTipo->setName( "boUtilizaDestinacao" );
$obHdnCampoTipo->setValue( $_REQUEST[ 'tipoBusca' ] );

$obTxtCodRecurso = new TextBox;
$obTxtCodRecurso->setName     ( "inCodRecurso" );
$obTxtCodRecurso->setRotulo   ( "Código" );
$obTxtCodRecurso->setSize     ( 10 );
$obTxtCodRecurso->setMaxLength( strlen($obRConfiguracaoOrcamento->getMascRecurso()) );
$obTxtCodRecurso->setNull     ( true );
$obTxtCodRecurso->setTitle    ( 'Informe um código' );
$obTxtCodRecurso->setInteiro  ( true );

$obTxtDescRecurso = new TextBox;
$obTxtDescRecurso->setName     ( "stDescricaoRecurso" );
$obTxtDescRecurso->setRotulo   ( "Descrição" );
$obTxtDescRecurso->setSize     ( 80 );
$obTxtDescRecurso->setMaxLength( 80 );
$obTxtDescRecurso->setNull     ( true );
$obTxtDescRecurso->setTitle    ( 'Informe uma descrição' );

$obFormulario = new Formulario;
$obFormulario->addForm($obForm);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnForm);
$obFormulario->addHidden($obHdnCampoNum);
$obFormulario->addHidden($obHdnCampoNom);
$obFormulario->addHidden($obHdnCampoTipo);

$obFormulario->addTitulo('Dados para Filtro');
$obFormulario->addComponente($obTxtCodRecurso);
$obFormulario->addComponente($obTxtDescRecurso);

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
