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
 * Página oculta de inclusao/alteracao de Ação
 * Data de Criação: 23/09/2009

 * @author Analista      : Heleno Santos
 * @author Desenvolvedor : Pedro Vaz de Mello de Medeiros

 * @ignore

 $Id: OCManterAcao.php 39527 2009-04-07 19:49:36Z pedro.medeiros $

 * Casos de uso: uc-02.09.04
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GF_PPA_CLASSES . 'visao/VPPAManterAcao.class.php';
include_once CAM_GF_PPA_CLASSES . 'negocio/RPPAManterAcao.class.php';

//Define o nome dos arquivos PHP
$stProjeto = 'ManterAcao';
$pgFilt = 'FL' . $stProjeto . '.php';
$pgList = 'LS' . $stProjeto . '.php';
$pgForm = 'FM' . $stProjeto . '.php';
$pgProc = 'PR' . $stProjeto . '.php';
$pgOcul = 'OC' . $stProjeto . '.php';
$pgJS   = 'JS' . $stProjeto . '.js';

$stCtrl = $_GET['stCtrl'] ? $_GET['stCtrl'] : $_POST['stCtrl'];
$stAcao = $_GET['stAcao'] ? $_GET['stAcao'] : $_POST['stAcao'];

$obRegra = new RPPAManterAcao();
$obVisao = new VPPAManterAcao($obRegra);

print $obVisao->$stCtrl($_REQUEST);

?>
