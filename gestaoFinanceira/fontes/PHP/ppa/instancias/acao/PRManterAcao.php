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
 * Página que processa Ação
 * Data de Criacao: 06/10/2008

 * @author Analista      : Heleno Menezes dos Santos
 * @author Desenvolvedor : Pedro Vaz de Mello de Medeiros

 * @package URBEM
 * @subpackage
 * @ignore

 * Casos de uso: uc-02.09.04

 $Id: PRManterAcao.php 39527 2009-04-07 19:49:36Z pedro.medeiros $

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GF_PPA_NEGOCIO . 'RPPAManterAcao.class.php';
include_once CAM_GF_PPA_VISAO   . 'VPPAManterAcao.class.php';

$arParametrosMetas = Sessao::read('arParametrosMetas');

if (is_array($arParametrosMetas)) {
    foreach ($arParametrosMetas as $stCampo => $flValor) {
        if (!isset($_REQUEST[$stCampo])) {
            $_REQUEST[$stCampo] = $flValor;
        }
    }
}

// Instanciando a classe de controle e de visão
$obRegra = new RPPAManterAcao();
$obVisao = new VPPAManterAcao($obRegra);
$obVisao->executarAcao($_REQUEST);
