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
 * Arquivo oculto - Cheques
 *
 * @category    Urbem
 * @package     Tesouraria
 * @author      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id:$
 */

//inclui os arquivos necessarios
include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
//include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
//include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include CAM_GF_TES_CONTROLE . 'CTesourariaCheque.class.php';
include CAM_GF_TES_NEGOCIO . 'RTesourariaCheque.class.php';

$stAcao = $_REQUEST["stCtrl"];
//Instancia o model
$obModel = new RTesourariaCheque();
//instancia o controller
$obController = new CTesourariaCheque($obModel);
//executa de acordo com a acao um metodo do controller
$obController->$stAcao($_REQUEST);

?>
