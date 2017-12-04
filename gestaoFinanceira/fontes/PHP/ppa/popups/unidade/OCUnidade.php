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
* Arquivo instância para popup de Regiao
* Data de Criação: 16/10/2008

* @author Analista: Anelise
* @author Desenvolvedor: Heleno Santos
* @author Desenvolvedor: Aldo Jean

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once(CAM_GF_PPA_MAPEAMENTO."TPPAUnidadeOrcamentaria.class.php");

$stCampoCod  	= $_REQUEST['stNomCampoCod'];
$stCampoDesc 	= $_REQUEST['stIdCampoDesc'];
$inCodigo   	= $_REQUEST[$stCampoCod];
$exercicio = sessao::read('exercicio');

switch ($_REQUEST["stCtrl"]) {
    case "buscaUnidade":
        $obTPPAUnidadeOrcamentaria = new TPPAUnidadeOrcamentaria();
        $rsUnidadeOrc = new RecordSet();

        $stFiltro = " WHERE OU.cod_unidade = ".$inCodigo." AND OU.exercicio = '".$exercicio."'   \n";
        $stOrder =  " ORDER BY nom_unidade";

        $obTPPAUnidadeOrcamentaria->recuperaUnidadeOrcamentaria( $rsUnidadeOrc, $stFiltro, $stOrder);  //??

        if ($rsUnidadeOrc->inNumLinhas > 0) {
            $stJS  = "document.getElementById('".$stCampoDesc."').innerHTML = '".$rsUnidadeOrc->getCampo('nom_unidade')."';";
            $stJS .= "document.getElementById('inExercicioUnidadeOrc').value = '".$rsUnidadeOrc->getCampo('exercicio')."';";
            $stJS .= "document.getElementById('inCodOrgao').value = '".$rsUnidadeOrc->getCampo('cod_orgao')."';";
        } else {
            $stJS  = "document.getElementById('".$stCampoDesc."').innerHTML = '&nbsp;';";
            $stJS .= "document.getElementById('inExercicioUnidadeOrc').value = '';";
            $stJS .= "document.getElementById('inCodOrgao').value = '';";
            $stJS .= "alertaAviso('@Código da Unidade (". $inCodigo .") não encontrado.', 'form','erro','".Sessao::getId()."');";
        }
        echo $stJS;
    break;
}

?>
