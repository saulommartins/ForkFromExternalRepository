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
    * Oculto do componente IFiltroCompetencia
    * Data de Criação: 13/06/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30930 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    * Casos de uso: uc-04.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroCompetencia.class.php"                               );
include_once ( CAM_GRH_PES_COMPONENTES."ISelectMultiploLotacao.class.php"                           );
include_once ( CAM_GRH_PES_COMPONENTES."ISelectAnoCompetencia.class.php"                            );
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroTipoFolha.class.php"                                 );
include_once ( CAM_GRH_PES_MAPEAMENTO."FPessoalOrganogramaVigentePorTimestamp.class.php"            );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php"                );

function processarAnoCompetencia()
{
    $stJs     = "";
    $inAno    = $_GET["inAnoCompetencia".$_GET["stComplemento"]];

    /****************************************************************************
    * Recuperando o organograma em vigor na competência selecionada
    *****************************************************************************/
    $stFiltro = "";
    if ($inAno != "") {
        $stFiltro = " WHERE to_char(dt_final, 'yyyy') = '".$inAno."'";
    }
    $stOrdem  = " ORDER BY cod_periodo_movimentacao DESC LIMIT 1 ";
    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaTodos($rsPeriodoMovimentacao, $stFiltro, $stOrdem);
    $inCodPeriodoMovimentacao = $rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");

    $obFPessoalOrganogramaVigentePorTimestamp = new FPessoalOrganogramaVigentePorTimestamp();
    $obFPessoalOrganogramaVigentePorTimestamp->setDado("cod_periodo_movimentacao",$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
        $obFPessoalOrganogramaVigentePorTimestamp->recuperaOrganogramaVigentePorTimestamp($rsOrganogramaVigente);
    $inCodOrganograma = $rsOrganogramaVigente->getCampo("cod_organograma");
    $stDataFinal      = $rsOrganogramaVigente->getCampo("dt_final");

    /***************************************************************************************
    * Grava o codigo da competencia, para poder montar o componente da lotação corretamente
    ****************************************************************************************/
    $arFiltroAnoCompetencia = Sessao::read("arFiltroAnoCompetencia");
    $arRetornoFiltroCompetencia = array();
    if (is_array($arFiltroAnoCompetencia) && count($arFiltroAnoCompetencia) > 0) {
        foreach ($arFiltroAnoCompetencia as $key => $obFiltroAnoCompetencia) {
            $obFiltroAnoCompetencia->setCodigoPeriodoMovimentacao($inCodPeriodoMovimentacao);
            array_push($arRetornoFiltroCompetencia,$obFiltroAnoCompetencia);

            if (trim($_GET["stNomeComponente"]) == trim($obFiltroAnoCompetencia->obCmbAnoCompetencia->getName())) {
                $arSelectMultiploLotacao = Sessao::read("arSelectMultiploLotacao");
                if (is_array($arSelectMultiploLotacao) && count($arSelectMultiploLotacao) > 0) {
                    foreach ($arSelectMultiploLotacao as $obSelectMultiploLotacao) {
                        $stJs .= $obSelectMultiploLotacao->atualizarLotacao($stDataFinal, $inCodOrganograma);
                    }
                }

                $arFiltroTipoFolha = Sessao::read("arFiltroTipoFolha");
                if (is_array($arFiltroTipoFolha) && count($arFiltroTipoFolha) > 0) {
                    foreach ($arFiltroTipoFolha as $obFiltroTipoFolha) {
                        $stJs .= $obFiltroTipoFolha->atualizarComplementar($inCodPeriodoMovimentacao);
                    }
                }
            }
        }
    }
    Sessao::remove("arFiltroAnoCompetencia");
    Sessao::write("arFiltroAnoCompetencia",$arRetornoFiltroCompetencia);

    return $stJs;
}

switch ($_GET["stCtrl"]) {
    case "processarAnoCompetencia":
        $stJs = processarAnoCompetencia();
        break;
}
if ($stJs) {
    echo $stJs;
}
?>
