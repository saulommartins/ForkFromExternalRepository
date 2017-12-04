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
* Classe de regra de relatório para Totais por Fornecedor
* Data de Criação: 21/11/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Eduardo Antunez

* @package URBEM
* @subpackage Regra de Relatório

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

* Casos de uso: uc-04.06.12
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE_RELATORIO                                                            );
include_once ( CAM_GRH_BEN_NEGOCIO."RBeneficioContratoServidorConcessaoValeTransporte.class.php"    );

class RRelatorioTotaisPorFornecedor extends PersistenteRelatorio
{
/**
    * @var Boolean
    * @access Private
*/
var $boAgruparPorLotacao;
/**
    * @var Boolean
    * @access Private
*/
var $boAgruparPorLocal;
/**
    * @var Object
    * @access Private
*/
var $obRBeneficioContratoServidorConcessaoValeTransporte;

/**
     * @access Public
     * @param Boolean $valor
*/
function setAgruparPorLotacao($valor) { $this->boAgruparPorLotacao = $valor; }
/**
     * @access Public
     * @param Boolean $valor
*/
function setAgruparPorLocal($valor) { $this->boAgruparPorLocal   = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setRBeneficioContratoServidorConcessaoValeTransporte($valor) { $this->obRBeneficioContratoServidorConcessaoValeTransporte = $valor; }

/**
     * @access Public
     * @return Boolean
*/
function getAgruparPorLotacao() { return $this->boAgruparPorLotacao; }
/**
     * @access Public
     * @return Boolean
*/
function getAgruparPorLocal() { return $this->boAgruparPorLocal; }
/**
     * @access Public
     * @return Object
*/
function getRBeneficioContratoServidorConcessaoValeTransporte() { return $this->obRBeneficioContratoServidorConcessaoValeTransporte;           }
/**
    * Método Construtor
    * @access Private
*/
function RRelatorioTotaisPorFornecedor()
{
    $this->setRBeneficioContratoServidorConcessaoValeTransporte( new RBeneficioContratoServidorConcessaoValeTransporte );
}

//!-----------------------------------------------------------------
// @function    RRelatorioTotaisPorFornecedor::AgrupaPorFornecedor
// @desc        Agrupa os dados do recordset pelo fornecedor
// @param       rsRecordSet RecordSet RecordSet com os dados da consulta
// @return      array Array de dados agrupados por fornecedor
// @access      private
//!-----------------------------------------------------------------
function AgrupaPorFornecedor($rsRecordSet)
{
    $arFornecedor = array();
    $inNumCGM = 0;
    $inCodOrgao = 0;
    while (!$rsRecordSet->eof()) {
        if ($rsRecordSet->getCampo('numcgm') != $inNumCGM) {
            $arSaida[] = $arFornecedor[$inNumCGM];
            $inNumCGM = $rsRecordSet->getCampo('numcgm');
            $stFornecedor = $rsRecordSet->getCampo('numcgm' )." - ".$rsRecordSet->getCampo('nom_cgm');
            $arFornecedor[$inNumCGM]['fornecedor' ][0]['campo1'] = 'Fornecedor: ';
            $arFornecedor[$inNumCGM]['fornecedor' ][0]['campo2'] = $stFornecedor;
            $inCodMes = 0;
            $stExercicio = 0;
            $stItinerario = "";
        }
        if ($rsRecordSet->getCampo('cod_mes')!=$inCodMes || $rsRecordSet->getCampo('exercicio')!=$stExercicio || $rsRecordSet->getCampo('itinerario') != $stItinerario) {
            $inCodMes     = $rsRecordSet->getCampo('cod_mes');
            $stExercicio  = $rsRecordSet->getCampo('exercicio');
            $stItinerario = $rsRecordSet->getCampo('itinerario');
            $arTmp['mes']            = $rsRecordSet->getCampo('cod_mes');
            $arTmp['exercicio']      = $rsRecordSet->getCampo('exercicio');
            $arTmp['itinerario']     = $rsRecordSet->getCampo('itinerario');
            $arTmp['valor_unitario'] = $rsRecordSet->getCampo('valor_unitario');
            $arFornecedor[$inNumCGM]['dados'][] = $arTmp;
        }
        $arFornecedor[$inNumCGM]['dados'][count($arFornecedor[$inNumCGM]['dados'])-1]['quantidade']  += $rsRecordSet->getCampo('total_fornecedor_mes');
        $arFornecedor[$inNumCGM]['dados'][count($arFornecedor[$inNumCGM]['dados'])-1]['valor_total'] += $rsRecordSet->getCampo('valor_total');
        $arFornecedor[$inNumCGM]['totais_fornecedor'][0]['campo1'] = 'Quantidade Total por Fornecedor: ';
        $arFornecedor[$inNumCGM]['totais_fornecedor'][0]['campo2'] += $rsRecordSet->getCampo('total_fornecedor_mes');
        $arFornecedor[$inNumCGM]['totais_fornecedor'][1]['campo1'] = 'Valor Total por Fornecedor: ';
        $arFornecedor[$inNumCGM]['totais_fornecedor'][1]['campo2'] += $rsRecordSet->getCampo('valor_total');

        $rsRecordSet->proximo();
    }
    $arSaida[] = $arFornecedor[$inNumCGM];
    array_shift($arSaida); //Desloca o array, pois o indice 0 fica vazio

    return $arSaida;
}

//!-----------------------------------------------------------------
// @function    RRelatorioTotaisPorFornecedor::geraRecordSet
// @desc        Gera o RecordSet para montagem do PDF
// @param       rsRetorno RecordSet RecordSet de retorno
// @return      object Objecto de erro
// @access      public
//!-----------------------------------------------------------------
function geraRecordSet(&$rsRetorno)
{
    $obErro = $this->obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->listarTotaisPorFornecedor( $rsRecordSet );

    if (!$obErro->ocorreu()) {
        //Quando agrupado por lotação e local
        if ( $this->getAgruparPorLotacao() && $this->getAgruparPorLocal() ) {
            //Separa os registros por lotação
            while (!$rsRecordSet->eof()) {
                $inCodOrgao = $rsRecordSet->getCampo('cod_orgao');
                if ($rsRecordSet->getCampo('cod_local'))
                    $arSeparadosPorLotacao[$inCodOrgao][] = $rsRecordSet->arElementos[$rsRecordSet->getCorrente()-1];
                $rsRecordSet->proximo();
            }
            //Trata cada conjunto de registros separados por lotação, separando por local
            foreach ($arSeparadosPorLotacao as $arLotacao) {
                $rsLotacao = new RecordSet;
                $rsLotacao->preenche($arLotacao);
                $stDescOrgao = $rsLotacao->getCampo('desc_orgao');
                $flValorTotalPorLotacao = 0;
                while (!$rsLotacao->eof()) {
                    $flValorTotalPorLotacao += $rsLotacao->getCampo('valor_total');
                    $rsLotacao->proximo();
                }
                $rsLotacao->setPrimeiroElemento();
                $inTamanhoDoArray = count($arRetorno);
                $arRetorno[$inTamanhoDoArray]['lotacao'][0]['campo1'] = 'Lotação: ';
                $arRetorno[$inTamanhoDoArray]['lotacao'][0]['campo2'] = $stDescOrgao;
                //Separa os registros por local
                while (!$rsLotacao->eof()) {
                    if ($inCodLocal = $rsLotacao->getCampo('cod_local'))
                        $arSeparadosPorLocal[$inCodLocal][] = $rsLotacao->arElementos[$rsLotacao->getCorrente()-1];
                    $rsLotacao->proximo();
                }
                foreach ($arSeparadosPorLocal as $arLocal) {
                    $rsLocal = new RecordSet;
                    $rsLocal->preenche($arLocal);
                    $stDescLocal = $rsLocal->getCampo('desc_local');
                    $flValorTotalPorLocal = 0;
                    while (!$rsLocal->eof()) {
                        if ($rsLocal->getCampo('cod_local'))
                            $flValorTotalPorLocal += $rsLocal->getCampo('valor_total');
                        $rsLocal->proximo();
                    }
                    $rsLocal->setPrimeiroElemento();
                    $arTmpLocal['local'][0]['campo1'] = 'Local: ';
                    $arTmpLocal['local'][0]['campo2'] = $stDescLocal;
                    $arTmpLocal['fornecedores'] = $this->AgrupaPorFornecedor($rsLocal);
                    $arTmpLocal['total_local'][0]['campo1'] = 'Valor Total por Local: ';
                    $arTmpLocal['total_local'][0]['campo2'] = $flValorTotalPorLocal;
                    $arRetorno[$inTamanhoDoArray]['locais'][] = $arTmpLocal;
                }
                $arRetorno[$inTamanhoDoArray]['total_lotacao'][0]['campo1']  = 'Valor Total por Lotação: ';
                $arRetorno[$inTamanhoDoArray]['total_lotacao'][0]['campo2']  = $flValorTotalPorLotacao;
            }
        //Quando agrupado apenas por lotação
        } elseif ($this->getAgruparPorLotacao()) {
            //Separa os registros do RecordSet principal pela lotacao (cod_orgao)
            while (!$rsRecordSet->eof()) {
                $inCodOrgao = $rsRecordSet->getCampo('cod_orgao');
                $arSeparadosPorLotacao[$inCodOrgao][] = $rsRecordSet->arElementos[$rsRecordSet->getCorrente()-1];
                $rsRecordSet->proximo();
            }
            //Transforma cada array separado por lotacao em recordset e agrupa por fornecedor, armazenando no array de retorno
            foreach ($arSeparadosPorLotacao as $arLotacao) {
                $rsLotacao = new RecordSet;
                $rsLotacao->preenche($arLotacao);
                $stDescOrgao  = $rsLotacao->getCampo('desc_orgao');
                $flValorTotalPorLotacao = 0;
                while (!$rsLotacao->eof()) {
                    $flValorTotalPorLotacao += $rsLotacao->getCampo('valor_total');
                    $rsLotacao->proximo();
                }
                $rsLotacao->setPrimeiroElemento();
                $inTamanhoDoArray = count($arRetorno);
                $arRetorno[$inTamanhoDoArray]['lotacao'][0]['campo1'] = 'Lotação ';
                $arRetorno[$inTamanhoDoArray]['lotacao'][0]['campo2'] = $stDescOrgao;
                $arRetorno[$inTamanhoDoArray]['fornecedores'] = $this->AgrupaPorFornecedor($rsLotacao);
                $arRetorno[$inTamanhoDoArray]['total_lotacao'][0]['campo1']  = 'Valor Total por Lotação: ';
                $arRetorno[$inTamanhoDoArray]['total_lotacao'][0]['campo2']  = $flValorTotalPorLotacao;
            }
        //Quando agrupado apenas por local
        } elseif ($this->getAgruparPorLocal()) {
            //Separa os registros do RecordSet principal pelo local (cod_local)
            while (!$rsRecordSet->eof()) {
                if ($inCodLocal = $rsRecordSet->getCampo('cod_local'))
                    $arSeparadosPorLocal[$inCodLocal][] = $rsRecordSet->arElementos[$rsRecordSet->getCorrente()-1];
                $rsRecordSet->proximo();
            }
            //Transforma cada array separado por local em recordset e agrupa por fornecedor, armazenando no array de retorno
            foreach ($arSeparadosPorLocal as $arLocal) {
                $rsLocal = new RecordSet;
                $rsLocal->preenche($arLocal);
                $stDescLocal = $rsLocal->getCampo('desc_local');
                $flValorTotalPorLocal = 0;
                while (!$rsLocal->eof()) {
                    $flValorTotalPorLocal += $rsLocal->getCampo('valor_total');
                    $rsLocal->proximo();
                }
                $rsLocal->setPrimeiroElemento();
                $inTamanhoDoArray = count($arRetorno);
                $arRetorno[$inTamanhoDoArray]['local'][0]['campo1'] = 'Local: ';
                $arRetorno[$inTamanhoDoArray]['local'][0]['campo2'] = $stDescLocal;
                $arRetorno[$inTamanhoDoArray]['fornecedores'] = $this->AgrupaPorFornecedor($rsLocal);
                $arRetorno[$inTamanhoDoArray]['total_local'][0]['campo1']  = 'Valor Total por Local: ';
                $arRetorno[$inTamanhoDoArray]['total_local'][0]['campo2']  = $flValorTotalPorLocal;
            }
        //Não agrupado por lotação nem local
        } else {
            //Agrupa o recordset principal apenas pelo fornecedor
            $arRetorno = $this->AgrupaPorFornecedor($rsRecordSet);
        }
    }
    $rsRetorno = new RecordSet;
    if (count($arRetorno))
        $rsRetorno->preenche($arRetorno);

    return $obErro;
}

}
