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
    * Formulário
    * Data de Criação: 19/01/2009

    * @author Desenvolvedor: Rafael Garbin

    * Casos de uso: uc-04.08.14

    $Id:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GRH_IMA_MAPEAMENTO."TIMACodigoDirf.class.php" );

function validaParametros()
{
    $obErro = new Erro();

    if ($_GET["inCodDIRF"] == "") {
        $obErro->setDescricao($obErro->getDescricao()."@Campo Código de Retenção da DIRF inválido!.");
    }

    if ($_GET["inExercicio"] == "") {
        $obErro->setDescricao($obErro->getDescricao()."@Campo Exercício inválido!");
    }

    if ($_GET["stTipoPrestador"] == "") {
        $obErro->setDescricao($obErro->getDescricao()."@Campo Tipo de Prestador inválido!");
    }

    return $obErro;
}

function preencheRetencaoDIRF()
{
    $obErro = validaParametros();

    if (!$obErro->ocorreu()) {
        $stFiltro  = " AND cod_dirf = ".$_GET["inCodDIRF"];
        $stFiltro .= " AND exercicio = '".$_GET["inExercicio"]."'";
        $stFiltro .= " AND tipo = '".trim($_GET["stTipoPrestador"])."'";
        $stFiltro = " WHERE ".substr($stFiltro,4,strlen($stFiltro));

        $obTIMACodigoDirf = new TIMACodigoDirf();
        $obTIMACodigoDirf->recuperaTodos($rsLista,$stFiltro);

        if ($rsLista->getNumLinhas() > 0) {
            $stJs .= "d.getElementById('stCodDIRF').innerHTML = '".$rsLista->getCampo("descricao")."';\n";
        } else {
            $stJs .= "d.getElementById('stCodDIRF').innerHTML = '&nbsp;';\n";
            $stJs .= "alertaAviso('Código de Retenção da DIRF é inválido para o exercício e tipo de prestador informado.','form','erro','".Sessao::getId()."');\n";
        }
    } else {
        $stJs .= "d.getElementById('inCodDIRF').innerHTML = '&nbsp;';\n";
        $stJs .= "d.getElementById('stCodDIRF').innerHTML = '&nbsp;';\n";
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

switch ($_GET['stCtrl']) {
    case "preencheRetencaoDIRF":
        $stJs = preencheRetencaoDIRF();
        break;
}

if ($stJs) {
    echo $stJs;
}

?>
