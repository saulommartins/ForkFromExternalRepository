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
 * Página que Processa a Emissão de Auto de Infração
 * Data de Criacao: 11/11/2008

 * @author Analista      : Heleno Menezes dos Santos
 * @author Desenvolvedor : Jânio Eduardo Vasconcellos de Magalhães

 * @package URBEM
 * @subpackage
 * @ignore

 * Casos de uso:

 $Id: PRNotificarProcesso.php 59612 2014-09-02 12:00:51Z gelson $

 */

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

require_once( CAM_GT_FIS_NEGOCIO . "RFISNotificarProcesso.class.php" );
require_once( CAM_GT_FIS_VISAO   . "VFISNotificarProcesso.class.php" );

# Instanciando a classe de controle e de visão
$obRegra = new RFISNotificarProcesso();
$obVisao = new VFISNotificarProcesso( $obRegra );
$obVisao->executarAcao( $_REQUEST );
