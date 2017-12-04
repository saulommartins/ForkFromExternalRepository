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
    * Oculto
    * Data de Criação: 08/05/2007

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: André Machado

    * @ignore

    $Revision: 30849 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-05 17:06:51 -0300 (Ter, 05 Jun 2007) $

    * Casos de uso: uc-04.05.40
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterPeriodoMovimentacao";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once($pgJS);

function processarFormFiltro($boExecuta=false)
{
    $rsRecordset = new recordset;
    $rsRecordset = serialize($rsRecordset);
    #sessao->transf = array();
    Sessao::write("boExcluirCalculados",true);
    Sessao::write('contratos',$rsRecordset);
    $stJs .= limparSpans();
    $stJs .= gerarSpan1();
    $stJs .= gerarSpan3();
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function submeter()
{
    $stJs .= "d.getElementById('fundo_carregando').height = document.height;    \n";
    $stJs .= "BloqueiaFrames(true,false);                                       \n";
    $stJs .= "parent.frames[2].Salvar();    \n";

    return $stJs;
}

function excluir()
{
  $stJs = submeter();

  return $stJs;
}

switch ($_POST["stCtrl"]) {
    case "excluir":
        $stJs.= excluir();
    break;
    case "limparForm":
        $stJs.= processarFormFiltro();
    break;
    case "submeter":
        $stJs .= submeter();
    break;
}

if ($stJs) {
    sistemaLegado::executaFrameOculto($stJs);
}

?>
