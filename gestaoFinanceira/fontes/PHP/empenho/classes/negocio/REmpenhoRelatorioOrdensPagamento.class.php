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
    * Classe de Regra do Relatório de Balancete de Receita
    * Data de Criação   : 13/05/2005

    * @author Desenvolvedor: João Rafael Tissot

    * @package URBEM
    * @subpackage Regra

    $Revision: 30805 $
    $Name$
    $Author: cako $
    $Date: 2007-12-04 12:08:26 -0200 (Ter, 04 Dez 2007) $

    * Casos de uso: uc-02.03.12
*/

/*
$Log$
Revision 1.16  2007/08/21 21:10:55  luciano
Bug#9830#

Revision 1.15  2007/05/31 20:56:14  vitor
#8953#

Revision 1.14  2007/04/11 19:43:42  vitor
8953

Revision 1.13  2007/03/28 19:56:13  vitor
#8411#

Revision 1.12  2007/03/02 15:05:56  vitor
BUG #8390#

Revision 1.11  2006/11/27 17:54:27  cako
Bug #7610#

Revision 1.10  2006/10/20 12:15:57  larocca
Bug #6862#

Revision 1.8  2006/07/05 20:47:06  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO        );
include_once( CAM_FW_PDF."RRelatorio.class.php"           );
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoEmpenho.class.php");
/**
    * Classe de Regra de Negócios Relatório para Ordens de Pagamento
    * @author João Rafael Tissot
*/
class REmpenhoRelatorioOrdensPagamento extends PersistenteRelatorio
{
var $inCodOrdemInicial;
var $inCodOrdemFinal;
var $stExercicioEmpenho;
var $stCodEntidade;
var $inCodRecurso;
var $inCodDetalhamento;
var $stDestinacaoRecurso;
var $stDtInicial;
var $stDtFinal;
var $inSitucao;
var $stTipo;

function setCodOrdemInicial($valor) { $this->inCodOrdemInicial = $valor; }
function setCodOrdemFinal($valor) { $this->inCodOrdemFinal = $valor; }
function setCodEmpenhoInicial($valor) { $this->inCodEmpenhoInicial = $valor; }
function setCodEmpenhoFinal($valor) { $this->inCodEmpenhoFinal = $valor; }
function setExercicioEmpenho($valor) { $this->stExercicioEmpenho = $valor; }
function setCodEntidade($valor) { $this->stCodEntidade = $valor; }
function setCodRecurso($valor) { $this->inCodRecurso = $valor; }
function setDestinacaoRecurso($valor) { $this->stDestinacaoRecurso = $valor; }
function setCodDetalhamento($valor) { $this->inCodDetalhamento = $valor; }
function setDtInicial($valor) { $this->stDtInicial = $valor; }
function setDtFinal($valor) { $this->stDtFinal   = $valor; }
function setCodCredor($valor) { $this->inCodCredor = $valor; }
function setSituacao($valor) { $this->inSituacao = $valor; }
function setTipo($valor) { $this->stTipo = $valor; }

function REmpenhoRelatorioOrdensPagamento()
{
    $this->obRRelatorio                 = new RRelatorio;
    $this->obREmpenhoEmpenho            = new REmpenhoEmpenho;
    $this->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
}
/**
    * Método abstrato
    * @access Public
*/
    public function geraRecordSet(&$rsVT , $stFiltro = "", $stOrdem = "")
    {
        include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoOrdemPagamento.class.php");
        $obFEmpenhoRelatorioOrdensPagamento = new TEmpenhoOrdemPagamento;
        if ($this->inCodOrdemInicial) {
            $obFEmpenhoRelatorioOrdensPagamento->setDado( 'cod_ordem_inicial', $this->inCodOrdemInicial);
        }
        if ($this->inCodOrdemFinal) {
            $obFEmpenhoRelatorioOrdensPagamento->setDado( 'cod_ordem_final', $this->inCodOrdemFinal );
        }
        if ($this->inCodEmpenhoInicial) {
            $obFEmpenhoRelatorioOrdensPagamento->setDado( 'cod_empenho_inicial', $this->inCodEmpenhoInicial);
        }
        if ($this->inCodEmpenhoFinal) {
            $obFEmpenhoRelatorioOrdensPagamento->setDado( 'cod_empenho_final', $this->inCodEmpenhoFinal );
        }
        if ($this->stExercicioEmpenho) {
            $obFEmpenhoRelatorioOrdensPagamento->setDado( 'exercicio_empenho', $this->stExercicioEmpenho );
        }
        if ($this->stCodEntidade) {
            $obFEmpenhoRelatorioOrdensPagamento->setDado( 'cod_entidade', $this->stCodEntidade );
        }
        if ($this->inCodRecurso) {
            $obFEmpenhoRelatorioOrdensPagamento->setDado( 'cod_recurso', $this->inCodRecurso );
        }
        if ($this->stDestinacaoRecurso) {
            $obFEmpenhoRelatorioOrdensPagamento->setDado( 'masc_recurso_red', $this->stDestinacaoRecurso );
        }
        if ($this->inCodDetalhamento) {
            $obFEmpenhoRelatorioOrdensPagamento->setDado( 'cod_detalhamento', $this->inCodDetalhamento );
        }
        if ($this->stDtInicial) {
            $obFEmpenhoRelatorioOrdensPagamento->setDado( 'dt_inicial', $this->stDtInicial );
        }
        if ($this->stDtFinal) {
            $obFEmpenhoRelatorioOrdensPagamento->setDado( 'dt_final', $this->stDtFinal );
        }
        if ($this->inCodCredor) {
            $obFEmpenhoRelatorioOrdensPagamento->setDado( 'numcgm', $this->inCodCredor );
        }
        if ($this->inSituacao) {
            $obFEmpenhoRelatorioOrdensPagamento->setDado( 'situacao', $this->inSituacao );
        }
        if ($this->stTipo) {
            $obFEmpenhoRelatorioOrdensPagamento->setDado( 'tipo', $this->stTipo );
        }

        $obFEmpenhoRelatorioOrdensPagamento->relatorioOrdensPagamento( $rsRecordSet, $stOrdem, $boTransacao );

        $arVT    = array();
        $inCount = 0;
        $totalAnulada = 0;
        $totalApagar = 0;
        $totalPaga = 0;
        $totalGeral = 0;

        while ( !$rsRecordSet->eof() ) {

            $situacaoAtual=$rsRecordSet->getCampo('situacao');
            $credorAtual=$rsRecordSet->getCampo('credor');
            $dataAtual=$rsRecordSet->getCampo('dt_pagamento');

            $arVT[$inCount]['cod_ordem']            = $rsRecordSet->getCampo('cod_ordem');
            $arVT[$inCount]['dt_emissao']        	= $rsRecordSet->getCampo('dt_emissao');
            $arVT[$inCount]['cod_empenho']    		= $rsRecordSet->getCampo('cod_empenho');
            $arVT[$inCount]['dt_empenho'] 			= $rsRecordSet->getCampo('dt_empenho');
            $arVT[$inCount]['credor']               = $rsRecordSet->getCampo('credor');
            $arVT[$inCount]['valor']   		        = number_format($rsRecordSet->getCampo('valor'),2,',','.');
            $arVT[$inCount]['situacao']   		    = $rsRecordSet->getCampo('situacao');
            $arVT[$inCount]['dt_pagamento']         = $rsRecordSet->getCampo('dt_pagamento');
            $arVT[$inCount]['dt_anulado']           = $rsRecordSet->getCampo('dt_anulado');
            $arVT[$inCount]['saldo_op']             = number_format($rsRecordSet->getCampo('saldo_op'),2,',','.');
            $arVT[$inCount]['valor_pago']           = number_format($rsRecordSet->getCampo('valor_pago'),2,',','.');
            $arVT[$inCount]['valor_anulado']        = number_format($rsRecordSet->getCampo('valor_anulado'),2,',','.');

            $totalGeral = $totalGeral+$rsRecordSet->getCampo('valor');
            $tmpGeral = $tmpGeral + $rsRecordSet->getCampo('valor');

            if ($rsRecordSet->getCampo('valor_anulado') > 0 ) {
                $totalAnulada=$totalAnulada+$rsRecordSet->getCampo('valor_anulado');
                $tmpAnulada=$tmpAnulada + $rsRecordSet->getCampo('valor_anulado');
            }
            if ($rsRecordSet->getCampo('valor_pago') > 0 ) {
                $totalPaga=$totalPaga+$rsRecordSet->getCampo('valor_pago');
                $tmpPaga=$tmpPaga + $rsRecordSet->getCampo('valor_pago');
            }
            if ($rsRecordSet->getCampo('saldo_op') > 0 ) {
                $totalApagar=$totalApagar+$rsRecordSet->getCampo('saldo_op');
                $tmpApagar=$tmpApagar + $rsRecordSet->getCampo('saldo_op');
            }

            $inCount++;
            $rsRecordSet->proximo();

            $arFiltro = Sessao::read('filtroRelatorio');
            if ($arFiltro['ordenacao'] == "credor") {
                if ($credorAtual!=$rsRecordSet->getCampo('credor')) {

                    $arVT[$inCount]['cod_ordem']        = '';
                    $arVT[$inCount]['dt_emissao']           = '';
                    $arVT[$inCount]['cod_empenho']          = '';
                    $arVT[$inCount]['dt_empenho']           = '';
                    $arVT[$inCount]['credor']               = '';
                    $arVT[$inCount]['valor']                = '';
                    $arVT[$inCount]['situacao']             = '';
                    $arVT[$inCount]['dt_pagamento']         = '';

                    $inCount++;

                    $arVT[$inCount]['cod_ordem']        = '';
                    $arVT[$inCount]['dt_emissao']           = '';
                    $arVT[$inCount]['cod_empenho']          = '';
                    $arVT[$inCount]['dt_empenho']           = '';
                    $arVT[$inCount]['credor']               = 'Total do Credor';
                    $arVT[$inCount]['valor']                = number_format($tmpGeral,2,',','.');
                    $arVT[$inCount]['saldo_op']             = number_format($tmpApagar,2,',','.');
                    $arVT[$inCount]['valor_pago']           = number_format($tmpPaga,2,',','.');
                    $arVT[$inCount]['valor_anulado']        = number_format($tmpAnulada,2,',','.');
                    $tmpGeral = 0;
                    $tmpApagar = 0;
                    $tmpPaga = 0;
                    $tmpAnulada = 0;

                    $inCount++;
                    $arVT[$inCount]['cod_ordem']        = '';
                    $arVT[$inCount]['dt_emissao']           = '';
                    $arVT[$inCount]['cod_empenho']          = '';
                    $arVT[$inCount]['dt_empenho']           = '';
                    $arVT[$inCount]['credor']               = '';
                    $arVT[$inCount]['valor']                = '';
                    $arVT[$inCount]['situacao']             = '';
                    $arVT[$inCount]['dt_pagamento']         = '';

                    $inCount++;

                }
            }

            if ($arFiltro['ordenacao'] == "pagamento") {
                if ($dataAtual!=$rsRecordSet->getCampo('dt_pagamento')) {

                    $arVT[$inCount]['cod_ordem']        = '';
                    $arVT[$inCount]['dt_emissao']           = '';
                    $arVT[$inCount]['cod_empenho']          = '';
                    $arVT[$inCount]['dt_empenho']           = '';
                    $arVT[$inCount]['credor']               = '';
                    $arVT[$inCount]['valor']                = '';
                    $arVT[$inCount]['situacao']             = '';
                    $arVT[$inCount]['dt_pagamento']         = '';

                    $inCount++;

                    $arVT[$inCount]['cod_ordem']        = '';
                    $arVT[$inCount]['dt_emissao']           = '';
                    $arVT[$inCount]['cod_empenho']          = '';
                    $arVT[$inCount]['dt_empenho']           = '';
                    $arVT[$inCount]['credor']               = 'Total do Dia  ';
                    $arVT[$inCount]['valor']                = number_format($tmpGeral,2,',','.');
                    $arVT[$inCount]['saldo_op']             = number_format($tmpApagar,2,',','.');
                    $arVT[$inCount]['valor_pago']           = number_format($tmpPaga,2,',','.');
                    $arVT[$inCount]['valor_anulado']        = number_format($tmpAnulada,2,',','.');
                    $tmpGeral = 0;
                    $tmpApagar = 0;
                    $tmpPaga = 0;
                    $tmpAnulada = 0;

                    $inCount++;
                    $arVT[$inCount]['cod_ordem']        = '';
                    $arVT[$inCount]['dt_emissao']           = '';
                    $arVT[$inCount]['cod_empenho']          = '';
                    $arVT[$inCount]['dt_empenho']           = '';
                    $arVT[$inCount]['credor']               = '';
                    $arVT[$inCount]['valor']                = '';
                    $arVT[$inCount]['situacao']             = '';
                    $arVT[$inCount]['dt_pagamento']         = '';

                    $inCount++;

                }
            }

        }

        $arVT[$inCount]['cod_ordem']        = '';
        $arVT[$inCount]['dt_emissao']        	= '';
        $arVT[$inCount]['cod_empenho']    		= '';
        $arVT[$inCount]['dt_empenho'] 			= '';
        $arVT[$inCount]['credor']   			= '';
        $arVT[$inCount]['valor']   				= '';
        $arVT[$inCount]['situacao']   			= '';
        $arVT[$inCount]['dt_pagamento']         = '';

        $inCount=$inCount+1;
        $arVT[$inCount]['cod_ordem']        = '';
        $arVT[$inCount]['dt_emissao']        	= '';
        $arVT[$inCount]['cod_empenho']    		= '';
        $arVT[$inCount]['dt_empenho'] 			= '';
        $arVT[$inCount]['credor']   			= 'Total Geral';
        $arVT[$inCount]['valor']   				= number_format($totalGeral,2,',','.');
        $arVT[$inCount]['saldo_op']             = number_format($totalApagar,2,',','.');
        $arVT[$inCount]['valor_pago']   		= number_format($totalPaga,2,',','.');
        $arVT[$inCount]['valor_anulado'] 		= number_format($totalAnulada,2,',','.');

        $rsVT = new RecordSet;
        $rsVT->preenche( $arVT );

        return $obErro;
    }
}
