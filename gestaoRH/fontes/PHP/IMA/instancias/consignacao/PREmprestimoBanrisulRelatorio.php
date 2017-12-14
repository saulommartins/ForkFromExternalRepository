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
 * Ralatório de confência de empréstimos Banrisul
 * Data de Criação   : 19/10/2009

 * @author Analista      Dagine Rodrigues Vieira
 * @author Desenvolvedor Cassiano de Vasconcellos Ferreira

 * @package URBEM
 * @subpackage

 * @ignore

 $Id:$
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "EmprestimoBanrisul";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgProcArq  = "PR".$stPrograma."Arquivo.php";
$pgFormImp  = "FMImportar".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

$preview = new PreviewBirt(4,40,14);
$preview->setVersaoBirt("2.5.0");
$preview->setFormato('pdf');
$preview->addParametro("stCompetencia", $_POST["stCompetencia"]);
$preview->addParametro("stCadastro", $_POST["stCadastro"]);
$preview->addParametro("stEntidade", Sessao::getEntidade());
$preview->addParametro("stFiltro", Sessao::read('stFiltro'));
$preview->addParametro("inCodPeriodoMovimentacao", $_REQUEST['inCodPeriodoMovimentacao']);

$preview->preview(true);
?>
