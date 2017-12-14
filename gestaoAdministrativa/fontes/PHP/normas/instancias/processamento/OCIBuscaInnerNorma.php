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
    * Oculto do componente INorma
    * Data de Criação:

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Vandré Miguel Ramos

    * @ignore

    $Revision: 16858 $
    $Name$
    $Author: vandre $
    $Date: 2006-10-17 10:16:42 -0300 (Ter, 17 Out 2006) $

    * Casos de uso: uc-01.04.02
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_NORMAS_NEGOCIO."RNorma.class.php"                                   );

function limparNorma()
{
        $stJs .= "document.getElementById('stCodNorma').value = '';                                                           \n";
        $stJs .= "document.getElementById('stNorma').innerHTML = '&nbsp;';";

        return  $stJs;
}

function preencherNormaTipo()
{
    if (isset($_REQUEST["inCodTipoNormaTxt"])) {
        if ($_REQUEST["inCodTipoNormaTxt"]!="") {
            $stJs = preencherNorma();
        } else {
            $stJs .= "document.getElementById('inCodTipoNormaTxt').focus();\n";
            $stJs .= "document.getElementById('stCodNorma').value = '';\n";
            $stJs .= "alertaAviso('@Campo Tipo Norma inválido. ()','form','erro','".Sessao::getId()."');\n";
        }
    } else {
        $stJs = preencherNorma();
    }

    return $stJs;
}

function preencherNorma()
{
    $obRNorma = new RNorma;
    if ($_REQUEST["stNorma"] != "") {
        $arCodPortariaNomeacao = explode("/",$_REQUEST["stNorma"]);
        $obRNorma->setNumNorma($arCodPortariaNomeacao[0]);
        $obRNorma->setExercicio($arCodPortariaNomeacao[1]);
        $obRNorma->obRTipoNorma->setCodTipoNorma($_REQUEST["inCodTipoNormaTxt"]);

        $obRNorma->listar($rsRecordSet,"");
        $stNull = "&nbsp;";
        if ( $rsRecordSet->getNumLinhas() <= 0) {
            $stJs .= "document.getElementById('stCodNorma').value = '';\n";
            $stJs .= "document.getElementById('stCodNorma').focus();\n";
            $stJs .= "document.getElementById('stNorma').innerHTML = '$stNull';                                  \n";
            if ($_REQUEST["inCodTipoNormaTxt"]!='') {
                $stJs .= "alertaAviso('@Campo Norma inválido (".$_REQUEST['stNorma'].") para o tipo de norma (".$_REQUEST["inCodTipoNormaTxt"].")','form','erro','".Sessao::getId()."');\n";
            } else {
                $stJs .= "alertaAviso('@Campo Norma inválido. (".$_REQUEST['stNorma'].")','form','erro','".Sessao::getId()."');\n";
            }
        } else {
            $stJs .= "document.getElementById('stNorma').innerHTML = '".$rsRecordSet->getCampo('nom_norma')."';    \n";
            $stJs .= "document.getElementById('stCodNorma').value = trim('".$rsRecordSet->getCampo('num_norma_exercicio')."');    \n";
            $stJs .= "document.getElementById('hdnCodTipoNorma').value = '".$rsRecordSet->getCampo('cod_tipo_norma')."';    \n";
            $stJs .= "document.getElementById('hdnCodNorma').value = '".$rsRecordSet->getCampo('cod_norma')."';    \n";
        }
    } else {
        $stJs .= "document.getElementById('stNorma').innerHTML = '&nbsp;';";
    }

    return $stJs;
}

switch ($_GET["stCtrl"]) {
    case "preencherNormaTipo":
        $stJs .= preencherNormaTipo();
        break;
    case "limparNorma":
        $stJs .= limparNorma();
        break;
}
if ($stJs) {
    echo $stJs;
}
?>
