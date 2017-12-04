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
    * Pagina de Oculto Almoxarife
    * Data de Criação   : 07/11/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Leandro André Zis

    * @package URBEM
    * @subpackage Regra

    * Casos de uso: uc-03.03.02
*/

/*
$Log$
Revision 1.6  2006/07/06 14:00:21  diego
Retirada tag de log com erro.

Revision 1.5  2006/07/06 12:09:52  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterAlmoxarife";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

function preencheComboPadrao($arCodAlmoxarifado, $inCodPadrao)
{
    $stJs .= "limpaSelect(f.inCodPadrao, 1); \n";

    $inCount = 1;

    if (is_array($arCodAlmoxarifado)) {

        foreach ($arCodAlmoxarifado as $stCodAlmoxarifado) {

            $arDados = explode("-", $stCodAlmoxarifado);
            $stJs .= "f.inCodPadrao[".$inCount."] = new Option('".$arDados[0]."-".$arDados[1]."','".$arDados[0]."',''); \n";
            if ($inCodPadrao == $arDados[0]) {
                $stJs .= 'f.inCodPadrao.selectedIndex = '.$inCount.';';
            }
            $inCount++;
        }
    }

    return $stJs;
}

switch ($stCtrl) {
    case "preencheComboPadrao":
        $stJs .= preencheComboPadrao($_POST['inCodAlmoxarifado'], $_POST['inCodPadrao']);

    break;
}

if ( $stJs )
    SistemaLegado::executaFrameOculto($stJs);

?>
