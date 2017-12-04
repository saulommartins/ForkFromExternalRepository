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
    * Oculto de Filtro para Relatório Banco de Horas
    * Data de Criação   : 10/12/2008

    * @author Analista      Dagiane Vieira
    * @author Desenvolvedor Diego Lemos de Souza

    * @package URBEM
    * @subpackage

    * @ignore

    $Id:$
    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "BancoHoras";
$pgFilt      = "FL".$stPrograma.".php";
$pgList      = "LS".$stPrograma.".php";
$pgForm      = "FM".$stPrograma.".php";
$pgProc      = "PR".$stPrograma.".php";
$pgOcul      = "OC".$stPrograma.".php";
$pgJS        = "JS".$stPrograma.".js";

function processarDataSaldo()
{
    $arCompetencia = explode("/",$_GET["stDataInicial"]);
    $dtSaldo   = date("d/m/Y",mktime (0, 0, 0, $arCompetencia[1]  , "01"-1, $arCompetencia[2]));
    $stJs = "jQuery('#dtSaldoBanco').val('".$dtSaldo."');";

    return $stJs;
}

function validarDataSaldo()
{
    if (sistemalegado::comparaDatas($_GET["dtSaldoBanco"],$_GET["stDataInicial"])) {
        $arCompetencia = explode("/",$_GET["stDataInicial"]);
        $dtSaldo   = date("d/m/Y",mktime (0, 0, 0, $arCompetencia[1]  , "01"-1, $arCompetencia[2]));
        $stJs  = "jQuery('#dtSaldoBanco').val('".$dtSaldo."');";
        $stJs .= "alertaAviso('@Campo Saldo do Banco de Horas Desde não pode ser maior que a data inicial de Período Leitura do Banco Dados.','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

switch ($_GET["stCtrl"]) {
    case "processarDataSaldo":
        $stJs = processarDataSaldo();
        break;
    case "validarDataSaldo":
        $stJs = validarDataSaldo();
        break;
}

if ($stJs) {
    echo $stJs;
}

?>
