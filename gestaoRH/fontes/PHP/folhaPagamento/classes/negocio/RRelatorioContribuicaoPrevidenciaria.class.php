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
    * Classe de regra de relatório para Relatório de Contribuição Previdenciária
    * Data de Criação: 02/06/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Regra de Relatório

    $Revision: 30896 $
    $Name$
    $Author: andre $
    $Date: 2007-07-23 10:35:25 -0300 (Seg, 23 Jul 2007) $

    * Casos de uso: uc-04.05.43
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE_RELATORIO                                                                );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPrevidencia.class.php"                               );

class RRelatorioContribuicaoPrevidenciaria extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $obRFolhaPagamentoPeriodoMovimentacao;
/**
    * @var Object
    * @access Private
*/
var $obRFolhaPagamentoPrevidencia;

/**
    * @access Public
    * @param Object $valor
*/
function setRFolhaPagamentoPeriodoMovimentacao($valor) { $this->obRFolhaPagamentoPeriodoMovimentacao = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRFolhaPagamentoPrevidencia($valor) { $this->obRFolhaPagamentoPrevidencia         = $valor; }

/**
    * @access Public
    * @return Object
*/
function getRFolhaPagamentoPeriodoMovimentacao() { return $this->obRFolhaPagamentoPeriodoMovimentacao; }
/**
    * @access Public
    * @return Object
*/
function getRFolhaPagamentoPrevidencia() { return $this->obRFolhaPagamentoPrevidencia;        }

/**
    * Método Construtor
    * @access Private
*/
function RRelatorioContribuicaoPrevidenciaria()
{
    $this->setRFolhaPagamentoPeriodoMovimentacao( new RFolhaPagamentoPeriodoMovimentacao );
    $this->setRFolhaPagamentoPrevidencia        ( new RFolhaPagamentoPrevidencia         );
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRecordset)
{
    $arFiltro = Sessao::read("filtroRelatorio");

    $stCompetencia = $arFiltro['inAno']."-".str_pad($arFiltro['inCodMes'], 2, "0", STR_PAD_LEFT);
    $this->obRFolhaPagamentoPeriodoMovimentacao->setDtFinal( $stCompetencia );

    $this->obRFolhaPagamentoPeriodoMovimentacao->listarPeriodoMovimentacao($rsMovimentacao);
    $stCompetencia = str_pad($arFiltro['inCodMes'], 2, "0", STR_PAD_LEFT)."/".$arFiltro['inAno'];
    $arRelatorio = array();
    $stTipoFiltro = ( isset($arFiltro['stTipoFiltro']) ) ? $arFiltro['stTipoFiltro'] : $arFiltro['hdnTipoFiltro'];

    switch ($stTipoFiltro) {
        case 'contrato':
        case 'cgm_contrato':
            foreach (Sessao::read('arContratos') as $arContrato) {
                $arFiltros['arRegistros'][] = $arContrato['contrato'];
            }
        break;
        case 'lotacao':
                        $arFiltros['arCodLotacaoSelecionados'] = $arFiltro['inCodLotacaoSelecionados'];
        break;
        case 'local':
                $arFiltros['arCodLocalSelecionados']   = $arFiltro['inCodLocalSelecionados'];
        break;
        case 'regime':
                $arFiltros['arCodRegimeSelecionadosFunc']     = $arFiltro['inCodRegimeSelecionadosFunc'];
                $arFiltros['arCodSubDivisaoSelecionadosFunc'] = $arFiltro['inCodSubDivisaoSelecionadosFunc'];
        break;
    }

    $arFiltros['inCodPrevidencia']          = $arFiltro['inCodPrevidencia'];
    $arFiltros['boAtivo']                   = $arFiltro['boAtivo'];
    $arFiltros['boInativo']                 = $arFiltro['boInativo'];
    $arFiltros['boPensionista']             = $arFiltro['boPensionista'];
    $arFiltros['boAgrupar']                                     = $arFiltro['boAgrupar'];
    $arFiltros['boQuebrarPagina']                       = $arFiltro['boQuebrarPagina'];
    $arFiltros['cod_periodo_movimentacao']  = $rsMovimentacao->getCampo('cod_periodo_movimentacao');

    if ($arFiltro['boAgrupar'] != "") {
                if ($arFiltro['stTipoFiltro'] == 'lotacao') {
                        $stOrdem .= "cod_orgao";
                }
    }

    if ($arFiltro['stOrdenacao'] == 'alfabetica') {
        if ($stOrdem != '') {$stOrdem .= ", nom_cgm";} else {$stOrdem = " nom_cgm";}
    } elseif ($arFiltro['stOrdenacao'] == 'numerica') {
        if ($stOrdem != '') {$stOrdem .= " ,nom_cgm";} else {$stOrdem = " registro";}
    }

    $this->obRFolhaPagamentoPrevidencia->listarRelatorioContribuicaoPrevidenciaria($rsContribuicaoPrevidenciaria,$arFiltros,$stOrdem,$boTransacao);
    $this->obRFolhaPagamentoPrevidencia->setCodPrevidencia($arFiltro['inCodPrevidencia']);
    $this->obRFolhaPagamentoPrevidencia->recuperaPrevidencia($rsPrevidencia);

    $arLinha1[0]['campo1'] = "Tipo da Folha:";
    $arLinha1[0]['campo2'] = "Salário/Complementar";
    $arLinha1[0]['campo3'] = "Competência:";
    $arLinha1[0]['campo4'] = $stCompetencia;
    $arLinha1[0]['campo5'] = "Período Movimentação:";
    $arLinha1[0]['campo6'] = $rsMovimentacao->getCampo('dt_inicial') ." até ". $rsMovimentacao->getCampo('dt_final');
    $arLinha1[0]['campo7'] = "Previdência:";
    $arLinha1[0]['campo8'] = $rsPrevidencia->getCampo('descricao');
    $arPagina['linha1']    = $arLinha1;

    $arContratos = (is_array($rsContribuicaoPrevidenciaria->getElementos())) ? $rsContribuicaoPrevidenciaria->getElementos() : array();
    $nuTotalBase        = 0;
    $nuTotalDesconto    = 0;
    $inTotalDependentes = 0;
    $nuTotalFamilia     = 0;
    $nuTotalMaternidade = 0;

    $this->obRFolhaPagamentoPrevidencia->setCodPrevidencia( $arFiltro['inCodPrevidencia'] );
    $this->obRFolhaPagamentoPrevidencia->consultarPrevidencia($boTransacao);

    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculado.class.php");
    $obTFolhaPagamentoEventoCalculado = new TFolhaPagamentoEventoCalculado();
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoComplementarCalculado.class.php");
    $obTFolhaPagamentoEventoComplementarCalculado = new TFolhaPagamentoEventoComplementarCalculado();
    foreach ($arContratos as $inIndex=>$arContrato) {
       // $this->obRFolhaPagamentoPeriodoMovimentacao->addRFolhaPagamentoCalculoFolhaPagamento();
//        $this->obRFolhaPagamentoPeriodoMovimentacao->addRFolhaPagamentoPeriodoContratoServidor();
  //      $this->obRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->setCodContrato( $arContrato['cod_contrato'] );

    //BUSCA VALOR DO EVENTO DE DESCONTO
        if ($arContrato['cod_evento_desconto']) {
            $stFiltro  = "   AND evento_calculado.cod_evento = ".$arContrato['cod_evento_desconto'];
            $stFiltro .= "   AND cod_contrato = ".$arContrato['cod_contrato'];
            $stFiltro .= "   AND cod_periodo_movimentacao = ".$arContrato['cod_periodo_movimentacao'];
            $obTFolhaPagamentoEventoCalculado->recuperaEventosCalculados($rsEventoCalculado,$stFiltro);
            $stFiltro  = " AND registro_evento_complementar.cod_contrato = ".$arContrato['cod_contrato'];
            $stFiltro .= " AND registro_evento_complementar.cod_evento = ".$arContrato['cod_evento_desconto'];
            $stFiltro .= "   AND cod_periodo_movimentacao = ".$arContrato['cod_periodo_movimentacao'];
            $obTFolhaPagamentoEventoComplementarCalculado->recuperaEventoComplementarCalculadoParaRelatorio($rsEventoComplementarCalculado,$stFiltro);

            $nuValorDesconto = $rsEventoCalculado->getCampo('valor');
            while ( !$rsEventoComplementarCalculado->eof() ) {
                $nuValorDesconto += $rsEventoComplementarCalculado->getCampo('valor');
                $rsEventoComplementarCalculado->proximo();
            }
        }
        $arContratos[$inIndex]['valor_desconto'] = number_format($nuValorDesconto,2,',','.');

        //BUSCA VALOR DO EVENTO DE BASE
        if ($arContrato['cod_evento_base']) {
            $stFiltro  = "   AND evento_calculado.cod_evento = ".$arContrato['cod_evento_base'];
            $stFiltro .= "   AND cod_contrato = ".$arContrato['cod_contrato'];
            $stFiltro .= "   AND cod_periodo_movimentacao = ".$arContrato['cod_periodo_movimentacao'];
            $obTFolhaPagamentoEventoCalculado->recuperaEventosCalculados($rsEventoCalculado,$stFiltro);

            $stFiltro  = " AND registro_evento_complementar.cod_contrato = ".$arContrato['cod_contrato'];
            $stFiltro .= " AND registro_evento_complementar.cod_evento = ".$arContrato['cod_evento_base'];
            $stFiltro .= " AND cod_periodo_movimentacao = ".$arContrato['cod_periodo_movimentacao'];
            $obTFolhaPagamentoEventoComplementarCalculado->recuperaEventoComplementarCalculadoParaRelatorio($rsEventoComplementarCalculado,$stFiltro);

            $nuValorBase = $rsEventoCalculado->getCampo('valor');
            while ( !$rsEventoComplementarCalculado->eof() ) {
                $nuValorBase += $rsEventoComplementarCalculado->getCampo('valor');
                $rsEventoComplementarCalculado->proximo();
            }
        }
        $arContratos[$inIndex]['valor_base'] = number_format($nuValorBase,2,',','.');

         //BUSCA VALOR DO EVENTO DE MATERNIDADE
        if ($arContrato['cod_evento_maternidade']) {
            $stFiltro  = "   AND evento_calculado.cod_evento = ".$arContrato['cod_evento_maternidade'];
            $stFiltro .= "   AND cod_contrato = ".$arContrato['cod_contrato'];
            $stFiltro .= "   AND cod_periodo_movimentacao = ".$arContrato['cod_periodo_movimentacao'];
            $obTFolhaPagamentoEventoCalculado->recuperaEventosCalculados($rsEventoCalculado,$stFiltro);

            $stFiltro  = " AND registro_evento_complementar.cod_contrato = ".$arContrato['cod_contrato'];
            $stFiltro .= " AND registro_evento_complementar.cod_evento = ".$arContrato['cod_evento_maternidade'];
            $stFiltro .= "   AND cod_periodo_movimentacao = ".$arContrato['cod_periodo_movimentacao'];
            $obTFolhaPagamentoEventoComplementarCalculado->recuperaEventoComplementarCalculadoParaRelatorio($rsEventoComplementarCalculado,$stFiltro);

            $nuValorMaternidade = $rsEventoCalculado->getCampo('valor');
            while ( !$rsEventoComplementarCalculado->eof() ) {
                $nuValorMaternidade += $rsEventoComplementarCalculado->getCampo('valor');
                $rsEventoComplementarCalculado->proximo();
            }
        }
        $arContratos[$inIndex]['valor_maternidade'] = number_format($nuValorMaternidade,2,',','.');

        //BUSCA VALOR DO EVENTO DE FAMILIA
        if ($arContrato['cod_evento_familia']) {
            $stFiltro  = "   AND evento_calculado.cod_evento = ".$arContrato['cod_evento_familia'];
            $stFiltro .= "   AND cod_contrato = ".$arContrato['cod_contrato'];
            $stFiltro .= "   AND cod_periodo_movimentacao = ".$arContrato['cod_periodo_movimentacao'];
            $obTFolhaPagamentoEventoCalculado->recuperaEventosCalculados($rsEventoCalculado,$stFiltro);

            $stFiltro  = " AND registro_evento_complementar.cod_contrato = ".$arContrato['cod_contrato'];
            $stFiltro .= " AND registro_evento_complementar.cod_evento = ".$arContrato['cod_evento_familia'];
            $stFiltro .= "   AND cod_periodo_movimentacao = ".$arContrato['cod_periodo_movimentacao'];
            $obTFolhaPagamentoEventoComplementarCalculado->recuperaEventoComplementarCalculadoParaRelatorio($rsEventoComplementarCalculado,$stFiltro);

            $nuValorFamilia = $rsEventoCalculado->getCampo('valor');
            while ( !$rsEventoComplementarCalculado->eof() ) {
                $nuValorFamilia += $rsEventoComplementarCalculado->getCampo('valor');
                $rsEventoComplementarCalculado->proximo();
            }
        }
        $arContratos[$inIndex]['valor_familia'] = number_format($nuValorFamilia,2,',','.');

        //BUSCA VALOR PATRONAL
        $nuValorPatronal = ($nuValorBase*$this->obRFolhaPagamentoPrevidencia->getAliquota())/100;
        $arContratos[$inIndex]['valor_patronal'] = number_format($nuValorPatronal,2,',','.');

        //BUSCA VALOR TOAL RECOLHIDO
        $nuValorRecolhido = $nuValorPatronal + $nuValorDesconto;
        $arContratos[$inIndex]['valor_recolhido'] = number_format($nuValorRecolhido,2,',','.');

        $nuTotalBase        += $nuValorBase;
        $nuTotalDesconto    += $nuValorDesconto;
        $inTotalDependentes += $arContrato['num_dependentes'];
        $nuTotalFamilia     += $nuValorFamilia;
        $nuTotalMaternidade += $nuValorMaternidade;
        $nuTotalPatronal    += $nuValorPatronal;
        $nuTotalRecolhido   += $nuValorRecolhido;
    }
    $inIndex++;
    $arContratos[$inIndex]['cod_ocorrencia']    = "TOTAIS";
    $arContratos[$inIndex]['valor_base']        = number_format($nuTotalBase,2,',','.');
    $arContratos[$inIndex]['valor_desconto']    = number_format($nuTotalDesconto,2,',','.');
    $arContratos[$inIndex]['num_dependentes']   = $inTotalDependentes;
    $arContratos[$inIndex]['valor_familia']     = number_format($nuTotalFamilia,2,',','.');
    $arContratos[$inIndex]['valor_maternidade'] = number_format($nuTotalMaternidade,2,',','.');
    $arContratos[$inIndex]['valor_patronal']    = number_format($nuTotalPatronal,2,',','.');
    $arContratos[$inIndex]['valor_recolhido']   = number_format($nuTotalRecolhido,2,',','.');
    $arPagina['contratos'] = $arContratos;

    $nuTotalPatronal   = ($nuTotalBase*$this->obRFolhaPagamentoPrevidencia->getAliquota())/100;
    $nuTotalAliquotaRat= ($nuTotalBase*$this->obRFolhaPagamentoPrevidencia->getAliquotaRat())/100;
    $nuTotalDevido     = $nuTotalPatronal + $nuTotalAliquotaRat + $nuTotalDesconto - $nuTotalFamilia - $nuTotalMaternidade;

    $arTotais[0]['campo1'] = str_pad("Número de Servidores", 50 ,".");
    $arTotais[0]['campo2'] = ":";
    $arTotais[0]['campo3'] = (count($arContratos)-1);
//    $arTotais[1]['campo1'] = str_pad("Total Patronal", 50 ,".");
//    $arTotais[1]['campo2'] = ":";
//    $arTotais[1]['campo3'] = number_format($nuTotalPatronal,2,',','.');
    $arTotais[1]['campo1'] = str_pad("Total Alíquota do RAT", 50 ,".");
    $arTotais[1]['campo2'] = ":";
    $arTotais[1]['campo3'] = number_format($nuTotalAliquotaRat,2,',','.');;
    $arTotais[2]['campo1'] = str_pad("Total Devido de Previdência", 50 ,".");
    $arTotais[2]['campo2'] = ":";
    $arTotais[2]['campo3'] = number_format($nuTotalDevido,2,',','.');
    $arPagina['totais']    = $arTotais;

    $arRelatorio[]         = $arPagina;

    $rsRecordset->preenche( $arRelatorio );

    return $obErro;
}

}
