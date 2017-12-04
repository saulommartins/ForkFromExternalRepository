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
include_once ( CAM_GA_ORGAN_NEGOCIO."ROrganogramaLocal.class.php"                                   );

function preencherCompetencia($inCodMes,$inAno)
{
    $stNameMes               = "inCodMes".$_GET["stCompetenciaComplemento"];
    $stNameAno               = "inAno".$_GET["stCompetenciaComplemento"];
    $arData                  = explode("/",$_GET['dtCompetenciaAtual']);
    $boCompetenciaAnteriores = $_GET['boCompetenciaAnteriores'];
    $inAno                   = $arData[2];
    $inCodMes                = $arData[1];
    $boSentidoPreenchimento  = ($boCompetenciaAnteriores)?($inAno >= $_GET[$stNameAno]):($inAno <= $_GET[$stNameAno]);

    $arMeses = array("1"=>"Janeiro"  ,
                     "2"=>"Fevereiro",
                     "3"=>"Março"    ,
                     "4"=>"Abril"    ,
                     "5"=>"Maio"     ,
                     "6"=>"Junho"    ,
                     "7"=>"Julho"    ,
                     "8"=>"Agosto"   ,
                     "9"=>"Setembro" ,
                     "10"=>"Outubro" ,
                     "11"=>"Novembro",
                     "12"=>"Dezembro");

    $stJs  = "\n limpaSelect(f.".$stNameMes.",0);";
    $stJs .= "\n f.".$stNameMes."[0] = new Option('Selecione','','selected');";
    $inIndex = 1;

    if ($boSentidoPreenchimento) {
        foreach ($arMeses as $stOption=>$stValue) {
            if ($inAno == $_GET[$stNameAno]) {
                if (!$boCompetenciaAnteriores) {
                    if ($stOption >= $inCodMes) {
                        $stJs .= "f.".$stNameMes."[$inIndex] = new Option('$stValue','$stOption','');\n";
                        $inIndex++;
                    }
                } else {
                    if ($stOption <= $inCodMes) {
                        $stJs .= "f.".$stNameMes."[$inIndex] = new Option('$stValue','$stOption','');\n";
                        $inIndex++;
                    }
                }
            } else {
                $stJs .= "f.".$stNameMes."[$inIndex] = new Option('$stValue','$stOption','');\n";
                $inIndex++;
            }
        }
    }

    return $stJs;
}

function processarCompetenciaAno()
{
    $inCodMes = $_GET["inCodMes".$_GET["stCompetenciaComplemento"]];
    $inAno    = $_GET["inAno".$_GET["stCompetenciaComplemento"]];
    $stJs     = preencherCompetencia($inCodMes,$inAno);

    return $stJs;
}

function processarCompetenciaMes()
{
    $stJs     = "";
    $inCodMes = $_GET["inCodMes".$_GET["stCompetenciaComplemento"]];
    $inAno    = $_GET["inAno".$_GET["stCompetenciaComplemento"]];
    Sessao::write("inCodMes", $inCodMes);
    Sessao::write("inAno", $inAno);

    /****************************************************************************
    * Recuperando o organograma em vigor na competência selecionada
    *****************************************************************************/
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
    $obTFolhaPagamentoPeriodoMovimentacao->setDado("mes", $inCodMes);
    $obTFolhaPagamentoPeriodoMovimentacao->setDado("ano", $inAno);
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacaoDaCompetencia($rsPeriodoMovimentacao);
    $inCodPeriodoMovimentacao = $rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");

    include_once(CAM_GRH_PES_MAPEAMENTO."FPessoalOrganogramaVigentePorTimestamp.class.php");
    $obFPessoalOrganogramaVigentePorTimestamp = new FPessoalOrganogramaVigentePorTimestamp();
    $obFPessoalOrganogramaVigentePorTimestamp->setDado("cod_periodo_movimentacao",$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
    $obFPessoalOrganogramaVigentePorTimestamp->recuperaOrganogramaVigentePorTimestamp($rsOrganogramaVigente);
    $inCodOrganograma = $rsOrganogramaVigente->getCampo("cod_organograma");
    $stDataFinal      = $rsOrganogramaVigente->getCampo("dt_final");

    /***************************************************************************************
    * Grava o codigo da competencia, para poder montar o componente da lotação corretamente
    ****************************************************************************************/
    include_once(CAM_GRH_PES_COMPONENTES."IFiltroCompetencia.class.php");
    include_once(CAM_GRH_PES_COMPONENTES."ISelectMultiploLotacao.class.php");
    include_once(CAM_GRH_PES_COMPONENTES."IFiltroTipoFolha.class.php");

    $arFiltroCompetencia = Sessao::read("arFiltroCompetencia");
    $arRetornoFiltroCompetencia = array();
    if (is_array($arFiltroCompetencia) && count($arFiltroCompetencia) > 0) {
        foreach ($arFiltroCompetencia as $key => $obFiltroCompetencia) {
            $obFiltroCompetencia->setCodigoPeriodoMovimentacao($inCodPeriodoMovimentacao);
            array_push($arRetornoFiltroCompetencia,$obFiltroCompetencia);

            if (trim($_GET["stNomeComponente"]) == trim($obFiltroCompetencia->obSeletorAno->getName())) {
                /****************************************************************************
                * Verifica se existe algum objeto de lotação na tela
                * para atualizar a lotação de acordo com o organograma
                *****************************************************************************/
                $arSelectMultiploLotacao = Sessao::read("arSelectMultiploLotacao");
                if (is_array($arSelectMultiploLotacao) && count($arSelectMultiploLotacao) > 0) {
                    foreach ($arSelectMultiploLotacao as $obSelectMultiploLotacao) {
                        $stJs .= $obSelectMultiploLotacao->atualizarLotacao($stDataFinal, $inCodOrganograma);
                    }
                }

                /****************************************************************************
                * Verifica se existe algum objeto de Tipo de Folha
                * para atualizar a complementar de acordo com a competência
                *****************************************************************************/
                $arFiltroTipoFolha = Sessao::read("arFiltroTipoFolha");
                if (is_array($arFiltroTipoFolha) && count($arFiltroTipoFolha) > 0) {
                    foreach ($arFiltroTipoFolha as $obFiltroTipoFolha) {
                        $stJs .= $obFiltroTipoFolha->atualizarComplementar($inCodPeriodoMovimentacao);
                    }
                }
            }
        }
    }
    Sessao::remove("arFiltroCompetencia");
    Sessao::write("arFiltroCompetencia",$arRetornoFiltroCompetencia);

    return $stJs;
}

switch ($_GET["stCtrl"]) {
    case "preencherCompetencia":
        $stJs .= preencherCompetencia();
        break;
    case "processarCompetenciaAno":
        $stJs = processarCompetenciaAno();
        break;
    case "processarCompetenciaMes":
        $stJs = processarCompetenciaMes();
        break;
}
if ($stJs) {
    echo $stJs;
}
?>
