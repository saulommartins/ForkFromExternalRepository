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
 * Página de Configuração de Empréstimos do Banrisul
 * Data de Criação   : 06/09/2009

 * @author Analista      Dagiane
 * @author Desenvolvedor Cassiano de Vasconcellos Ferreira

 * @package URBEM
 * @subpackage

 * @ignore

 $Id:$
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/*
* Preenche um evento quando solicitado
*/
function preencherInnerEvento()
{
    $inCodEvento = trim($_GET['inCodEvento']);

    if ( !empty($inCodEvento)) {
        include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoEvento.class.php";
        include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoConfiguracao.class.php";

        $obRFolhaPagamentoEvento = new RFolhaPagamentoEvento;
        $obRFolhaPagamentoEvento->setCodEvento    ( $inCodEvento );
        $obRFolhaPagamentoEvento->consultarEvento ( );

        $stJs  = "jQuery('#inCodigoEvento').val('".$obRFolhaPagamentoEvento->getCodigo()."'); \n";
        $stJs .= "jQuery('#stIdEvento').html('".$obRFolhaPagamentoEvento->getDescricao()."'); \n";
    } else {
        $stJs  = "jQuery('#inCodigoEvento').val('');      \n";
        $stJs .= "jQuery('#stIdEvento').html('&nbsp;');   \n";
    }

    return $stJs;
}

$stJs = "";
switch ($_REQUEST['stCtrl']) {
    case "preencherInnerEvento":
        $stJs = preencherInnerEvento();
        break;
}

if ($stJs != "") {
    echo $stJs;
}

?>
