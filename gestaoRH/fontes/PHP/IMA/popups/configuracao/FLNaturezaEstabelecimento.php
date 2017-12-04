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
    * Arquivo de Filtro
    * Data de Criação: 26/10/2007

    * @author Desenvolvedor: Diego Lemos de Souza

    * $Id: FLNaturezaEstabelecimento.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-04.08.14
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao( new RFolhaPagamentoPeriodoMovimentacao );

//Define o nome dos arquivos PHP
$stPrograma = "NaturezaEstabelecimento";
$pgFilt      = "FL".$stPrograma.".php";
$pgList      = "LS".$stPrograma.".php";
$pgForm      = "FM".$stPrograma.".php";
$pgProc      = "PR".$stPrograma.".php";
$pgOcul      = "OC".$stPrograma.".php";
$pgJS        = "JS".$stPrograma.".js";

Sessao::write('link', '');

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                                 ( "stAcao"                                          );
$obHdnAcao->setValue                                ( $stAcao                                           );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             	( "stCtrl"                                                              );
$obHdnCtrl->setValue                            	( $stCtrl                                                               );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $_REQUEST["campoNum"] );

//Define HIDDEN com o o nome do campo texto
$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $_REQUEST["campoNom"] );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                                  ( $pgList                                           );

$obIntExercicio = new Inteiro();
$obIntExercicio->setRotulo("Exercício");
$obIntExercicio->setTitle("Informe o exercício da tabela de natureza do estabelecimento.");
$obIntExercicio->setName("inExercicio");
$obIntExercicio->setMaxLength(4);
$obIntExercicio->setSize(5);

$obTxtDescricao = new TextBox();
$obTxtDescricao->setRotulo("Descrição");
$obTxtDescricao->setName("stDescricao");
$obTxtDescricao->setTitle("Informe a descrição da natureza do estabelecimento.");
$obTxtDescricao->setSize(50);

$obBtnOk = new Ok;
//$obBtnOk->obEvento->setOnClick("montaParametrosGET('submeterExcluir', '', true);");

$obBtnLimpar = new Limpar();
$obBtnLimpar->setName								( "btnLimpar" 															);
$obBtnLimpar->setValue("Limpar");
$obBtnLimpar->setTitle								( "Clique para limpar os dados dos campos." 							);

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addTitulo                            ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"  );
$obFormulario->addForm                              ( $obForm                                           );
$obFormulario->addHidden                            ( $obHdnAcao                                        );
$obFormulario->addHidden( $obHdnCampoNum );
$obFormulario->addHidden( $obHdnCampoNom );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addTitulo("Dados para filtro");
$obFormulario->addComponente($obIntExercicio);
$obFormulario->addComponente($obTxtDescricao);
$obFormulario->defineBarra( array( $obBtnOk,$obBtnLimpar ));
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
