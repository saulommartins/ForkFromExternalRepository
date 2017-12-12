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
    * Classe de Regra para Ordem de Pagamento do relatório
    * Data de Criação   : 18/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Regra

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2006-11-17 18:20:56 -0200 (Sex, 17 Nov 2006) $

    * Casos de uso: uc-02.03.25
                    uc-02.03.05
*/

/*
$Log$
Revision 1.12  2006/11/17 20:20:36  cako
Bug #7288#

Revision 1.11  2006/09/29 16:36:09  eduardo
Bug #7060#

Revision 1.10  2006/09/28 09:52:29  eduardo
Bug #7060#

Revision 1.9  2006/07/14 16:00:11  jose.eduardo
Bug #6525#

Revision 1.8  2006/07/11 13:40:08  cako
Bug #6525#

Revision 1.7  2006/07/05 20:47:06  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE_RELATORIO );

/**
    * Classe de Regra para emissão do Plano de Contas com Banco/Recurso

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Diego Barbosa Victoria
*/
class REmpenhoRelatorioOrdemPagamentoAnulado extends PersistenteRelatorio
{
/**
    * @var String
    * @access Private
*/
var $stExercicio;

/**
    * @var String
    * @access Private
*/
var $stExercicioOrdem;

/**
    * @var Integer
    * @access Private
*/
var $inCodEntidade;
/**
    * @var Integer
    * @access Private
*/
var $inCodOrdem;
/**
    * @var Boolean
    * @access Private
*/
var $boImplantado;
/**
    * @var String
    * @access Private
*/
var $stTimestamp;

/**
     * @access public
     * @param string $valor
*/
function setExercicio($valor) { $this->stExercicio = $valor; }

/**
     * @access public
     * @param string $valor
*/
function setExercicioOrdem($valor) { $this->stExercicioOrdem = $valor; }

/**
     * @access public
     * @param Integer $valor
*/
function setCodEntidade($valor) { $this->inCodEntidade= $valor; }
/**
     * @access public
     * @param Integer $valor
*/
function setCodOrdem($valor) { $this->inCodOrdem   = $valor; }
/**
     * @access public
     * @param Boolean $valor
*/
function setImplantado($valor) { $this->boImplantado = $valor; }

/**
     * @access public
     * @param string $valor
*/
function setTimestamp($valor) { $this->stTimestamp = $valor; }
/**
     * @access Public
     * @return String
*/
function getExercicio() { return $this->stExercicio; }

/**
     * @access Public
     * @return String
*/
function getExercicioOrdem() { return $this->stExercicioOrdem; }

/**
     * @access Public
     * @return Integer
*/
function getCodEntidade() { return $this->inCodEntidade; }
/**
     * @access Public
     * @return Integer
*/
function getCodOrdem() { return $this->inCodOrdem;    }
/**
     * @access Public
     * @return Boolean
*/
function getImplantado() { return $this->boImplantado;  }

/**
     * @access Public
     * @return String
*/
function getTimestamp() { return $this->stTimestamp; }
/**
    * Método Construtor
    * @access Private
*/
function REmpenhoRelatorioOrdemPagamentoAnulado()
{
    parent::PersistenteRelatorio();
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$arRecordSet , $stOrder = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoOrdemPagamento.class.php" );
    $obTEmpenhoOrdemPagamento = new TEmpenhoOrdemPagamento;

    $arRecordSet = array();
    $stFiltro    = " AND   cod_entidade     IN (" . $this->inCodEntidade." )";    
    $stFiltro   .= " AND   cod_ordem        = ".$this->inCodOrdem;
    $stFiltro   .= " AND   exercicio_ordem  = '".$this->stExercicioOrdem."'";    
    $obTEmpenhoOrdemPagamento->setDado( 'exercicio'    , $this->stExercicioOrdem   );
    $obTEmpenhoOrdemPagamento->setDado( 'cod_ordem'    , $this->inCodOrdem    );
    $obTEmpenhoOrdemPagamento->setDado( 'cod_entidade' , $this->inCodEntidade );
    $obTEmpenhoOrdemPagamento->setDado( 'conta_padrao' , "true" );
    
    if ( trim($this->boImplantado) == 't' ) {
        $obErro = $obTEmpenhoOrdemPagamento->recuperaRelatorioRestos( $rsRecordSet, $stFiltro, $stOrder );
    } else {
        $obErro = $obTEmpenhoOrdemPagamento->recuperaRelatorioOP( $rsRecordSet, $stFiltro, $stOrder );
    }

    if ( !$obErro->ocorreu() ) {
       $obTEmpenhoOrdemPagamento->setDado( 'timestamp', $this->stTimestamp );
       $stFiltro = "
            AND (
                   SELECT coalesce(opla.vl_anulado,0.00)
                     FROM   empenho.ordem_pagamento_liquidacao_anulada as opla
                     WHERE  pl.exercicio    = opla.exercicio
                       AND  pl.cod_entidade = opla.cod_entidade
                       AND  pl.cod_ordem    = opla.cod_ordem
                       AND  pl.exercicio_liquidacao = opla.exercicio_liquidacao
                       AND  pl.cod_nota     = opla.cod_nota
                       AND  opla.timestamp  = to_timestamp('".$this->stTimestamp."','yyyy-mm-dd hh24:mi:ss.us')
            ) > 0
       ";
       $obErro = $obTEmpenhoOrdemPagamento->recuperaLiquidacoesAnulacaoOP( $rsLiquidacoes, $stFiltro );
       //$obErro = $obTEmpenhoOrdemPagamento->recuperaLiquidacoesAnulacaoOP( $rsLiquidacoes );
    }

    if ( !$rsRecordSet->eof() && !$obErro->ocorreu() ) {

        //Linha0
        $arLinha0[0]['entidade']   = $rsRecordSet->getCampo('cod_entidade') . ' - ' . $rsRecordSet->getCampo('nom_entidade');
        $rsNewRecord = new RecordSet;
        $rsNewRecord->preenche($arLinha0);
        $arRecordSet[0] = $rsNewRecord;

    }

    //Bloco4
    $nuQuantia = 0;
    while ( !$rsRecordSet->eof() ) {
        $rsRecordSet->getCampo('cod_empenho');
        //$arBloco4[ ($rsRecordSet->getCorrente()-1) ]['1'] = $rsRecordSet->getCampo('cod_empenho').'/'.$rsRecordSet->getCampo('exercicio_empenho').' - '.$rsRecordSet->getCampo('dotacao_formatada').' - '.$rsRecordSet->getCampo('cod_recurso');
        //$arBloco4[ ($rsRecordSet->getCorrente()-1) ]['2'] = $rsRecordSet->getCampo('cod_nota').'/'.$rsRecordSet->getCampo('exercicio_nota').' - '.$rsRecordSet->getCampo('dt_liquidacao');
        //$arBloco4[ ($rsRecordSet->getCorrente()-1) ]['3'] = $rsRecordSet->getCampo('vl_liquidado');
        //$arBloco4[ ($rsRecordSet->getCorrente()-1) ]['4'] = $rsRecordSet->getCampo('vl_anulado');
        $rsLiquidacoes->setPrimeiroElemento();
        while ( !$rsLiquidacoes->eof() ) {
            $codNota   = $rsLiquidacoes->getCampo('cod_nota');
            $exercicio = $rsLiquidacoes->getCampo('exercicio_liquidacao');

            $codNota1   = $rsRecordSet->getCampo('cod_nota');
            $exercicio1 = $rsRecordSet->getCampo('exercicio_nota');

            if ( ($codNota = $codNota1) && ($exercicio = $exercicio1) ) {
                $arBloco4[ ($rsLiquidacoes->getCorrente()-1) ]['1'] = $rsRecordSet->getCampo('cod_empenho').'/'.$rsRecordSet->getCampo('exercicio_empenho').' - '.$rsRecordSet->getCampo('dotacao_formatada').' - '.$rsRecordSet->getCampo('cod_recurso');
                $arBloco4[ ($rsLiquidacoes->getCorrente()-1) ]['2'] = $rsRecordSet->getCampo('cod_nota').'/'.$rsRecordSet->getCampo('exercicio_nota').' - '.$rsRecordSet->getCampo('dt_liquidacao');
                $arBloco4[ ($rsLiquidacoes->getCorrente()-1) ]['3'] = $rsLiquidacoes->getCampo('vl_pagamento');
                $arBloco4[ ($rsLiquidacoes->getCorrente()-1) ]['4'] = $rsLiquidacoes->getCampo('vl_pagamento') - $rsLiquidacoes->getCampo('vl_anulado_ate_periodo');
                $arBloco4[ ($rsLiquidacoes->getCorrente()-1) ]['5'] = $rsLiquidacoes->getCampo('vl_anulado_atual');
                $rsRecordSet->proximo();
            }
            $rsLiquidacoes->proximo();
        }
        $nuQuantia += $rsRecordSet->getCampo('vl_anulado');
        $rsRecordSet->proximo();
    }
    $nuQuantia = 0;
    if ( ($codNota = $codNota1) && ($exercicio = $exercicio1) ) {
        foreach ($arBloco4 as $totalizador) {
            $nuQuantia += $totalizador['5'];
        }
    }
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arBloco4);
    $rsNewRecord->addFormatacao("3","NUMERIC_BR");
    $rsNewRecord->addFormatacao("4","NUMERIC_BR");
    $rsNewRecord->addFormatacao("5","NUMERIC_BR");
    $arRecordSet[1] = $rsNewRecord;
    $rsRecordSet->setPrimeiroElemento();
    $rsLiquidacoes->setPrimeiroElemento();

    //Bloco5
    $arBloco5[0][1] = "Total ";
    $arBloco5[0][2] = $nuQuantia;
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arBloco5);
    $rsNewRecord->addFormatacao("2","NUMERIC_BR");
    $arRecordSet[2] = $rsNewRecord;

    //Bloco6
    $arBloco6[0][1] = "Ordem de pagamento anulada em: " . $rsLiquidacoes->getCampo('dt_anulacao');
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arBloco6);
    $arRecordSet[3] = $rsNewRecord;

    //Bloco7
    $arBloco7 = array();
    $stMotivo = str_replace( chr(10) , "", $rsLiquidacoes->getCampo('motivo') );
    $stMotivo = wordwrap( $stMotivo , 40, chr(13) );
    $arMotivo = explode( chr(13), $stMotivo );
    foreach ($arMotivo as $stMotivo) {
        $arMot[1] = $stMotivo;
        $arBloco7[] = $arMot;
    }

    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arBloco7);
    $arRecordSet[4] = $rsNewRecord;
}
}
