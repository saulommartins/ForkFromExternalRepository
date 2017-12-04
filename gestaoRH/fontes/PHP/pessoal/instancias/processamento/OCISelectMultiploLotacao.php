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
 * Oculto do componente ISelectMultiploLotacao
 * Data de Criação   : 25/11/2008

 * @author Analista      Dagiane Vieira
 * @author Desenvolvedor Diego Lemos de Souza

 * @package URBEM
 * @subpackage

 * @ignore #

 $Id:$
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

function preencherLotacao()
{
    $rsDisponiveis = new recordset;
    if (trim($_GET["inAno"]) != "" and trim($_GET["inCodMes"]) != "") {
        $inDia = date("t",mktime(0,0,0,$_GET["inCodMes"],1,$_GET["inAno"]));
        $dtCompetencia = date("Y-m-d",mktime(0,0,0,$_GET["inCodMes"],$inDia,$_GET["inAno"]));

        include_once ( CAM_GA_ORGAN_NEGOCIO."ROrganogramaOrgao.class.php" );
        $obROrganogramaOrgao = new ROrganogramaOrgao();
        $obROrganogramaOrgao->setVigencia($dtCompetencia);
        $obROrganogramaOrgao->recuperaOrgaos( $rsDisponiveis,""," ORDER BY estruturado" );
    }
    $stJs .= "if (f.inCodLotacaoDisponiveis) {";
    $stJs .= "limpaSelect(f.inCodLotacaoDisponiveis ,0);   \n";
    $stJs .= "limpaSelect(f.inCodLotacaoSelecionados ,0);   \n";
    $inIndex = 0;
    while ( !$rsDisponiveis->eof() ) {
        $stJs .= "f.inCodLotacaoDisponiveis[".$inIndex."] = new Option('".$rsDisponiveis->getCampo('estruturado')." - ". $rsDisponiveis->getCampo( 'descricao'). "','".$rsDisponiveis->getCampo('cod_orgao')."','');\n";
        $inIndex++;
        $rsDisponiveis->proximo();
    }
    $stJs .= "}";

    return $stJs;
}

switch ($_GET["stCtrl"]) {
    case "preencherLotacao":
        $stJs = preencherLotacao();
        break;
}
if ($stJs) {
    echo $stJs;
}
?>
