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
    * Página de Oculto
    * Data de Criação   : 06/01/2009

    * @author Analista      Gelson Gonçalves
    * @author Desenvolvedor Alexandre Melo

    * @package URBEM
    * @subpackage

    * @ignore

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

$stCtrl = $_REQUEST['stCtrl'];

switch ($stCtrl) {

    case 'verificaStatus':

        include_once( CAM_GA_ORGAN_MAPEAMENTO."TMigraOrganograma.class.php" );
        $obTMigraOrganograma = new TMigraOrganograma;
        $obTMigraOrganograma->recuperaMigraTotalidade($rsSetor);

        include_once( CAM_GA_ORGAN_MAPEAMENTO."TMigraOrganogramaLocal.class.php" );
        $obTMigraOrganogramaLocal = new TMigraOrganogramaLocal;
        $obTMigraOrganogramaLocal->recuperaMigraTotalidade($rsLocal);

        if ($rsSetor->getCampo('finalizado') == "false") {

            $stJs .= 'd.getElementById("stSetor").innerHTML="Não Configurado";';
            $stJs .= 'f.Ok.disabled = true; ';
        } else {
            $stJs .= 'd.getElementById("stSetor").innerHTML="Configurado";';
        }

        if ($rsLocal->getCampo('finalizado') == "false") {
            $stJs .= 'd.getElementById("stLocal").innerHTML="Não Configurado";';
            $stJs .= 'f.Ok.disabled = true; ';
        } else {
            $stJs .= 'd.getElementById("stLocal").innerHTML="Configurado";';
        }

        echo $stJs;

    break;

}
?>
