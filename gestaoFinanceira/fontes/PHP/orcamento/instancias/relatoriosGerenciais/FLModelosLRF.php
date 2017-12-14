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
    * Página de Filtro dos Relatórios Gerenciais
    * Data de Criação   : 22/08/2006

    * @author Analista: Cleisson Barbosa
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @ignore

    $Revision: 30668 $
    $Name$
    $Author: fernando $
    $Date: 2006-08-25 14:59:23 -0300 (Sex, 25 Ago 2006) $

    * Casos de uso : uc-02.01.35
*/

/*
$Log$
Revision 1.1  2006/08/25 17:56:30  fernando
Bug #6773#

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

switch ($_REQUEST['stAcao']) {

    case '10':
        $stTelaFiltro = "FLModelosLegislativo.php";
    break;

    case '14':
        $stTelaFiltro = "FLModelosLegislativo.php";
    break;

    case 'ajustar':
        $stTelaFiltro = "FLAjustesModelos.php";
    break;

    default:
        $stTelaFiltro =  "FLModelosExecutivo.php";
    break;
}
    include_once '../../../../../../gestaoFinanceira/fontes/PHP/LRF/instancias/tceRS/'.$stTelaFiltro);

?>
