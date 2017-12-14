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
/*
 * Filtro para configuração do ponto
 * Data de Criação   : 15/10/2008

 * @author Analista      Dagiane Vieira
 * @author Desenvolvedor Diego Lemos de Souza

 * @package URBEM
 * @subpackage

 * @ignore

 $Id:$
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao( new RFolhaPagamentoPeriodoMovimentacao );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoPonto";
$pgFilt      = "FL".$stPrograma.".php";
$pgList      = "LS".$stPrograma.".php";
$pgForm      = "FM".$stPrograma.".php";
$pgProc      = "PR".$stPrograma.".php";
$pgOcul      = "OC".$stPrograma.".php";
$pgJS        = "JS".$stPrograma.".js";

Sessao::remove('link');
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                                 ( "stAcao"                                          );
$obHdnAcao->setValue                                ( $stAcao                                           );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                                  ( $pgList                                           );

$obTxtCodigo = new Inteiro();
$obTxtCodigo->setName("inCodigo");
$obTxtCodigo->setId("inCodigo");
$obTxtCodigo->setRotulo("Código");
$obTxtCodigo->setTitle("informe o código da configuração.");

$obTxtDescricao = new TextBox();
$obTxtDescricao->setName('stDescricao');
$obTxtDescricao->setId('stDescricao');
$obTxtDescricao->setRotulo('Descrição');
$obTxtDescricao->setTitle('Informe o nome da configuração.');
$obTxtDescricao->setSize('100');
$obTxtDescricao->setMaxLength('100');

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addTitulo                            ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"  );
$obFormulario->addForm                              ( $obForm                                           );
$obFormulario->addHidden                            ( $obHdnAcao                                        );
$obFormulario->addTitulo("Dados para Filtro");
$obFormulario->addComponente($obTxtCodigo);
$obFormulario->addComponente($obTxtDescricao);
$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
