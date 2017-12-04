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
* Classe de regra de relatório para Concessão Vale Transporte
* Data de Criação: 08/12/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Eduardo Antunez

* @package URBEM
* @subpackage Regra de Relatório

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

* Casos de uso: uc-04.06.11
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE_RELATORIO                                                            );
include_once ( CAM_GRH_BEN_NEGOCIO."RBeneficioContratoServidorConcessaoValeTransporte.class.php"    );

class RRelatorioConcessaoValeTransporte extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $obRBeneficioContratoServidorConcessaoValeTransporte;
/**
    * @var String
    * @access Private
*/
var $stTipoFiltro;
/**
    * @access Public
    * @param Object $valor
*/
function setRBeneficioContratoServidorConcessaoValeTransporte($valor) { $this->obRBeneficioContratoServidorConcessaoValeTransporte = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setTipoFiltro($valor) { $this->stTipoFiltro = $valor; }
/**
     * @access Public
     * @return String
*/
function getTipoFiltro() { return $this->stTipoFiltro; }

function RRelatorioConcessaoValeTransporte()
{
    $this->setRBeneficioContratoServidorConcessaoValeTransporte (new RBeneficioContratoServidorConcessaoValeTransporte);
}

//!-----------------------------------------------------------------
// @function    RRelatorioConcessaoValeTransporte::geraRecordSet
// @desc        Gera o RecordSet para geração do relatório
// @param       rsRecordSet RecordSet RecordSet de retorno
// @return      object Objecto de Erro
// @access      public
//!-----------------------------------------------------------------
function geraRecordSet(&$rsRetorno)
{
    $obErro = $this->obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->listarConcessaoValeTransporteRelatorio($rsOriginal);

    switch ($this->getTipoFiltro()) {

        case 'contrato':
        case 'cgm':
        $arRetorno     = array();
        $arPagina      = array();
        $inCodContrato = 0;
        //Loop principal de processamento do recordset original
        while (!$rsOriginal->eof()) {
            $arDadosContrato = $arConcessaoContrato = array();
            //Quando troca o contrato sendo processado
            if ($rsOriginal->getCampo('registro') != $inCodContrato) {
                if ($inCodContrato != 0)
                    $arRetorno[] = $arPagina;
                $arPagina = array();
                $arTotal  = array();
                $inCodContrato = $rsOriginal->getCampo('registro');
                $arDadosContrato[] = array('campo1' => 'CGM: '     , 'campo2' => $rsOriginal->getCampo('numcgm').   " - ".$rsOriginal->getCampo('nom_cgm'));
                $arDadosContrato[] = array('campo1' => 'Matrícula: ', 'campo2' => $rsOriginal->getCampo('registro'));
                $arDadosContrato[] = array('campo1' => 'Lotação: ' , 'campo2' => $rsOriginal->getCampo('orgao').    " - ".$rsOriginal->getCampo('desc_orgao'));
                $arDadosContrato[] = array('campo1' => 'Local: '   , 'campo2' => $rsOriginal->getCampo('cod_local')." - ".$rsOriginal->getCampo('desc_local'));
                $arTotal[] = array('campo1' => 'Total por Matrícula: ', 'campo2' => '');
                $arPagina['dadosContrato'] = $arDadosContrato;
                $arPagina['totalContrato'] = $arTotal;
            }
            if ($inCodGrupo = $rsOriginal->getCampo('cod_grupo')) {
                $inTamanho = count($arPagina['gruposContrato']);
                if ($arPagina['gruposContrato'][$inTamanho-1]['cod_grupo'] != $inCodGrupo) {
                    $arPagina['gruposContrato'][$inTamanho]['mes']        = $rsOriginal->getCampo('mes');
                    $arPagina['gruposContrato'][$inTamanho]['exercicio']  = $rsOriginal->getCampo('exercicio');
                    $arPagina['gruposContrato'][$inTamanho]['grupo']      = $rsOriginal->getCampo('desc_grupo');
                    $arPagina['gruposContrato'][$inTamanho]['quantidade'] = $rsOriginal->getCampo('quantidade');
                    $arPagina['gruposContrato'][$inTamanho]['cod_grupo']  = $rsOriginal->getCampo('cod_grupo');
                } else {
                    $arPagina['gruposContrato'][$inTamanho-1]['quantidade'] += $rsOriginal->getCampo('quantidade');
                }
            } else {
                $arConcessaoContrato['mes']        = $rsOriginal->getCampo('mes');
                $arConcessaoContrato['exercicio']  = $rsOriginal->getCampo('exercicio');
                $arConcessaoContrato['tipo']       = $rsOriginal->getCampo('tipo');
                $arConcessaoContrato['itinerario'] = $rsOriginal->getCampo('itinerario');
                $arConcessaoContrato['quantidade'] = $rsOriginal->getCampo('quantidade');
                $arPagina['concessoesContrato'][]  = $arConcessaoContrato;
            }
            $arPagina['totalContrato'][0]['campo2'] += $rsOriginal->getCampo('quantidade');

            //Aproveita o loop dos contratos para preparar o agrupamento por empresa
            $arTmpFornecedoresPorContrato[$inCodContrato][$rsOriginal->getCampo('fornecedor')][$rsOriginal->getCampo('mes')][$rsOriginal->getCampo('itinerario')] += $rsOriginal->getCampo('quantidade');

            $rsOriginal->proximo();
        }
        $arRetorno[] = $arPagina;

        //Processando dados por empresa
        if (is_array($arTmpFornecedoresPorContrato)) {
            $arCodContrato = array_keys($arTmpFornecedoresPorContrato);
            foreach ($arCodContrato as $inCodContrato) {
                $arNomeFornecedores = array_keys($arTmpFornecedoresPorContrato[$inCodContrato]);
                foreach ($arNomeFornecedores as $stFornecedor) {
                    $inTotalPorFornecedor = 0;
                    $arDadosFornecedor = array();
                    $arDadosFornecedor['cabecalho'][0]['campo1'] = 'Empresa: ';
                    $arDadosFornecedor['cabecalho'][0]['campo2'] = $stFornecedor;
                    $arMeses = array_keys($arTmpFornecedoresPorContrato[$inCodContrato][$stFornecedor]);
                    foreach ($arMeses as $inCodMes) {
                        $arItinerario = array_keys($arTmpFornecedoresPorContrato[$inCodContrato][$stFornecedor][$inCodMes]);
                        foreach ($arItinerario as $stItinerario) {
                            $arConcessao['mes'] = $inCodMes;
                            $arConcessao['itinerario'] = $stItinerario;
                            $arConcessao['quantidade'] = $arTmpFornecedoresPorContrato[$inCodContrato][$stFornecedor][$inCodMes][$stItinerario];
                            $inTotalPorFornecedor += $arTmpFornecedoresPorContrato[$inCodContrato][$stFornecedor][$inCodMes][$stItinerario];
                            $arDadosFornecedor['concessoes'][] = $arConcessao;
                        }
                    }
                    $arTotalPorFornecedor['campo1'] = 'Total por Empresa: ';
                    $arTotalPorFornecedor['campo2'] = $inTotalPorFornecedor;
                    $arDadosFornecedor['total'][] = $arTotalPorFornecedor;
                    $inCount = count($arRetorno)-1;
                    while ($inCount >= 0) {
                        if ($arRetorno[$inCount]['dadosContrato'][1]['campo2'] == $inCodContrato) {
                            $arRetorno[$inCount]['fornecedor'][] = $arDadosFornecedor;
                        }
                        $inCount--;
                    }
                }
            }
        }
        break;

        case 'grupo':
        $inCodGrupo = 0;
        while (!$rsOriginal->eof()) {
            if ($rsOriginal->getCampo('cod_grupo')) {
                if ($rsOriginal->getCampo('cod_grupo') != $inCodGrupo) {
                    if ($inCodGrupo != 0)
                        $arRetorno[] = $arPagina;
                    $arPagina = array();
                    $inCodGrupo = $rsOriginal->getCampo('cod_grupo');
                    $arPagina['dadosGrupo'][0]['campo1'] = 'Grupo: ';
                    $arPagina['dadosGrupo'][0]['campo2'] = $rsOriginal->getCampo('desc_grupo');
                    $arPagina['dadosGrupo'][0]['campo3'] = $rsOriginal->getCampo('cod_grupo');
                    $arPagina['total'][0]['campo1'] = 'Total: ';
                }
                $arConcessao['mes']        = $rsOriginal->getCampo('mes');
                $arConcessao['exercicio']  = $rsOriginal->getCampo('exercicio');
                $arConcessao['tipo']       = $rsOriginal->getCampo('tipo');
                $arConcessao['itinerario'] = $rsOriginal->getCampo('itinerario');
                $arConcessao['quantidade'] = $rsOriginal->getCampo('quantidade');
                $arPagina['concessoesGrupo'][] = $arConcessao;
                $arPagina['total'][0]['campo2'] += $rsOriginal->getCampo('quantidade');

                $inTamanho = count($arPagina['contratosGrupo']);
                if ($arPagina['contratosGrupo'][$inTamanho-1]['contrato'] != $rsOriginal->getCampo('registro')) {
                    $arContratosDoGrupo['cgm']      = $rsOriginal->getCampo('numcgm')." - ".$rsOriginal->getCampo('nom_cgm');
                    $arContratosDoGrupo['contrato'] = $rsOriginal->getCampo('registro');
                    $arContratosDoGrupo['lotacao']  = $rsOriginal->getCampo('orgao')." - ".$rsOriginal->getCampo('desc_orgao');
                    $arContratosDoGrupo['local']    = $rsOriginal->getCampo('cod_local')." - ".$rsOriginal->getCampo('desc_local');
                    $arPagina['contratosGrupo'][] = $arContratosDoGrupo;
                }
                //Por Empresa
                $arTmpFornecedoresPorGrupo[$inCodGrupo][$rsOriginal->getCampo('fornecedor')][$rsOriginal->getCampo('mes')][$rsOriginal->getCampo('itinerario')] += $rsOriginal->getCampo('quantidade');
            }
            $rsOriginal->proximo();
        }
        $arRetorno[] = $arPagina;

        //Processando dados por empresa
        if (is_array($arTmpFornecedoresPorGrupo)) {
            $arCodGrupo = array_keys($arTmpFornecedoresPorGrupo);
            foreach ($arCodGrupo as $inCodGrupo) {
                $arNomeFornecedores = array_keys($arTmpFornecedoresPorGrupo[$inCodGrupo]);
                foreach ($arNomeFornecedores as $stFornecedor) {
                    $inTotalPorFornecedor = 0;
                    $arDadosFornecedor = array();
                    $arDadosFornecedor['cabecalho'][0]['campo1'] = 'Empresa: ';
                    $arDadosFornecedor['cabecalho'][0]['campo2'] = $stFornecedor;
                    $arMeses = array_keys($arTmpFornecedoresPorGrupo[$inCodGrupo][$stFornecedor]);
                    foreach ($arMeses as $inCodMes) {
                        $arItinerario = array_keys($arTmpFornecedoresPorGrupo[$inCodGrupo][$stFornecedor][$inCodMes]);
                        foreach ($arItinerario as $stItinerario) {
                            $arConcessao['mes'] = $inCodMes;
                            $arConcessao['itinerario'] = $stItinerario;
                            $arConcessao['quantidade'] = $arTmpFornecedoresPorGrupo[$inCodGrupo][$stFornecedor][$inCodMes][$stItinerario];
                            $inTotalPorFornecedor += $arTmpFornecedoresPorGrupo[$inCodGrupo][$stFornecedor][$inCodMes][$stItinerario];
                            $arDadosFornecedor['concessoes'][] = $arConcessao;
                        }
                    }
                    $arTotalPorFornecedor['campo1'] = 'Total por Empresa: ';
                    $arTotalPorFornecedor['campo2'] = $inTotalPorFornecedor;
                    $arDadosFornecedor['total'][] = $arTotalPorFornecedor;
                    $inCount = count($arRetorno)-1;
                    while ($inCount >= 0) {
                        if ($arRetorno[$inCount]['dadosGrupo'][0]['campo3'] == $inCodGrupo) {
                            $arRetorno[$inCount]['fornecedor'][] = $arDadosFornecedor;
                        }
                        $inCount--;
                    }
                }
            }
        }
    break;

    }//end switch

    $rsRetorno = new RecordSet;
    if (count($arRetorno))
        $rsRetorno->preenche($arRetorno);

    return $obErro;
}

}
