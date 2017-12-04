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
    * Oculto do componente ILocal
    * Data de Criação: 07/06/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30566 $
    $Name$
    $Author: vandre $
    $Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

    * Casos de uso: uc-04.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_ORGAN_NEGOCIO."ROrganogramaLocal.class.php"                                   );

function preencherLocal()
{
    $stExtensao = trim($_REQUEST["stExtensao"]);

    $obROrganogramaLocal = new ROrganogramaLocal;
    if ($_REQUEST["inCodLocal".$stExtensao] != "") {
        $obROrganogramaLocal->setCodLocal( $_REQUEST["inCodLocal".$stExtensao] );
        $obROrganogramaLocal->listarLocal( $rsRecordSet );
        $stNull = "&nbsp;";

        if ( $rsRecordSet->getNumLinhas() <= 0) {
            $stJs .= "document.frm.inCodLocal$stExtensao.value = '';                                                           \n";
            $stJs .= "document.frm.inCodLocal$stExtensao.focus();                                                              \n";
            $stJs .= "document.getElementById('stLocal$stExtensao').innerHTML = '$stNull';                                     \n";
            $stJs .= "document.frm.HdninCodLocal$stExtensao.value = '';\n";
            $stJs .= "document.frm.stLocal$stExtensao.value = '';\n";
            $stJs .= "alertaAviso('@Campo Local inválido. (".$_REQUEST["inCodLocal".$stExtensao].")','form','erro','".Sessao::getId()."');\n";
        } else {
            $stJs .= "document.getElementById('stLocal$stExtensao').innerHTML = '".$rsRecordSet->getCampo('descricao')."';    \n";
            $stJs .= "document.frm.stLocal$stExtensao.value = '".$rsRecordSet->getCampo('descricao')."';                \n";
            $stJs .= "document.frm.HdninCodLocal$stExtensao.value = '".$rsRecordSet->getCampo("cod_orgao")."';    \n";
        }
    } else {
        $stJs .= "document.getElementById('stLocal$stExtensao').innerHTML = '&nbsp;';";
    }

    return $stJs;
}
switch ($_GET["stCtrl"]) {
    case "preencherLocal":
        $stJs .= preencherLocal();
    break;
}
if ($stJs) {
    echo $stJs;
}
?>
