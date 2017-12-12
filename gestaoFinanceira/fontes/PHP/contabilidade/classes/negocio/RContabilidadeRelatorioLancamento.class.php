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
    * Classe de Regra para emissão do Plano de Contas
    * Data de Criação   : 26/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson Buzo

    * @package URBEM
    * @subpackage Regra

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2007-06-20 09:50:58 -0300 (Qua, 20 Jun 2007) $

    * Casos de uso: uc-02.02.21
*/

/*
$Log$
Revision 1.14  2007/06/20 12:50:58  vitor
Bug#8839#, Bug#8840#, Bug#8841#

Revision 1.13  2007/06/04 15:14:21  vitor
#8839##8840##8841#

Revision 1.12  2007/05/21 19:27:22  vitor
Bug #8825#

Revision 1.11  2006/07/05 20:50:26  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE_RELATORIO      );
include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoValor.class.php" );

/**
    * Classe de Regra para emissão de relatorio de Lancamento
    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson Buzo
*/
class RContabilidadeRelatorioLancamento extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $obRContabilidadeLancamentoValor;
/**
    * @var String
    * @access Private
*/
var $stFiltro;

/**
     * @access Public
     * @param Object $valor
*/
function setRContabilidadeLancamentoValor($valor) { $this->obRContabilidadeLancamentoValor = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setFiltro($valor) { $this->stFiltro = $valor; }

/**
     * @access Public
     * @return Object
*/
function getRContabilidadeLancamentoValor() { return $this->obRContabilidadeLancamentoValor; }
/**
     * @access Public
     * @return String
*/
function getFiltro() { return $this->stFiltro; }

/**
    * Método Construtor
    * @access Private
*/
function RContabilidadeRelatorioLancamento()
{
    $this->obRContabilidadeLancamentoValor = new RContabilidadeLancamentoValor;
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRecordSet , $stOrder = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeValorLancamento.class.php" );
    $obTContabilidadeLancamentoValor     = new TContabilidadeValorLancamento;

    $stFiltro = "";
    if ( $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio() ) {
        $stFiltro .= " l.exercicio = '".$this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio()."' AND ";
    }
    if ( $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getDtLote() ) {
        $stFiltro .= " lo.dt_lote = TO_DATE ('".$this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getDtLote()."'::varchar,'dd/mm/yyyy') AND ";
    }
    if ( $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getDtLoteInicial() ) {
        $stFiltro .= " lo.dt_lote >= TO_DATE('".$this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getDtLoteInicial()."'::varchar,'dd/mm/yyyy' ) AND ";
    }
    if ( $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getDtLoteTermino() ) {
        $stFiltro .= " lo.dt_lote <= TO_DATE('".$this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getDtLoteTermino()."'::varchar,'dd/mm/yyyy') AND ";
    }
    if ( $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() ) {
        $stFiltro .= " lo.cod_lote = ".$this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote()." AND ";
    }
    if ( $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getNomLote() ) {
        $stFiltro .= " UPPER( lo.nom_lote ) like UPPER('%".$this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getNomLote()."%' ) AND ";
    }

   if ($this->stFiltro) {
        $stFiltro .= $this->stFiltro;
    }
    $stFiltro = ( $stFiltro ) ? " AND " . substr( $stFiltro,0,strlen($stFiltro)-4) : '';

    $stExercicio = "";
    if ( $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio() ) {
        $stExercicio .= $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio();
    }

    $stCodigoEntidade = "";
    if ( $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() ) {
        $stCodigoEntidade .= $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade();
    }

    $obTContabilidadeLancamentoValor->setDado('stExercicio', $stExercicio);
    $obTContabilidadeLancamentoValor->setDado('stCodigoEntidade', $stCodigoEntidade);

    $obErro = $obTContabilidadeLancamentoValor->recuperaRelatorio( $rsRecordSet, $stFiltro, $stOrder );
   $arRecordSet = array( 0 => array( 'inCodLote'      =>  0,
                                      'stDtLote'       => '',
                                      'inSequencia'    =>  0,
                                      'stTipoECodigo'  => '',
                                      'stDescricao'    => '',
                                      'inNivel'        =>  0,
                                      'nuValor'        =>  0
                                    )
                        );
    $inCount = -1;
    while ( !$rsRecordSet->eof() ) {

        $stEntidade      = $rsRecordSet->getCampo( "nom_cgm"        );
        $inCodLote       = $rsRecordSet->getCampo( "cod_lote"       );
        $stNomLote       = $rsRecordSet->getCampo( "nom_lote"       );
        $stDtLote        = $rsRecordSet->getCampo( "dt_lote"        );
        $inSequencia     = $rsRecordSet->getCampo( "sequencia"      );
        $inCodEntidade   = $rsRecordSet->getCampo( "cod_sequencia"  );
        $inCodHistorico  = $rsRecordSet->getCampo( "cod_historico"  );
        $inObservacao    = $rsRecordSet->getCampo( "observacao"  );
        $inCodPlano      = $rsRecordSet->getCampo( "cod_plano"      );
        $stTipoValor     = $rsRecordSet->getCampo( "tipo_valor"     );
        $stNomHistorico  = $rsRecordSet->getCampo( "nom_historico"  );
        $stNomConta      = $rsRecordSet->getCampo( "nom_conta"      );
        $nuValor         = $rsRecordSet->getCampo( "vl_lancamento"  );
        $stCodEstrutural = $rsRecordSet->getCampo( "cod_estrutural" );
        $stTipo          = $rsRecordSet->getCampo( "tipo"           );
        $inCount++;

        if ($inCodLote == $inCodLoteOLD and $stDtLote == $stDtLoteOLD and $inCodEntidade == $inCodEntidadeOLD and $stTipo == $stTipoOLD) {
            if ($stTipoValorOLD == 'C') {
                $nuTotalCredito += $nuValorOLD;
            } elseif ($stTipoValorOLD == 'D') {
                $nuTotalDebito += $nuValorOLD;
            }

            if ($inSequencia != $inSequenciaOLD) {
                if ($inCodHistoricoOLD) {
                    $arRecordSet[$inCount]['stTipoECodigo'] = "HIS:  ".$inCodHistoricoOLD;
                    $stNomHistoricoOLD = str_replace( chr(10), "", $stNomHistoricoOLD );
                    $stNomHistoricoOLD = wordwrap( $stNomHistoricoOLD, 66,chr(13) );
                    $arNomHistoricoOLD = explode( chr(13), $stNomHistoricoOLD );
                    foreach ($arNomHistoricoOLD as $stNomHistoricoOLD) {
                        $arRecordSet[$inCount]['stDescricao'  ] = $stNomHistoricoOLD;
                        $inCount++;
                    }
                    if ($nuValorOLD) {
                        $nuTotal += $nuValorOLD;
                        $arRecordSet[$inCount]['nuValor'] = number_format($nuValorOLD,2,',','.');
                        $inCount++;
                    }
                }
                $arRecordSet[$inCount]['inSequencia'] = $inSequencia;
                $inSequenciaOLD   = $inSequencia;
            }

        } else {
            if ($inCodHistoricoOLD) {
                if ($stTipoValorOLD == 'C') {
                    $nuTotalCredito += $nuValorOLD;
                } elseif ($stTipoValorOLD == 'D') {
                    $nuTotalDebito += $nuValorOLD;
                }

                $arRecordSet[$inCount]['stTipoECodigo'] = "HIS:  ".$inCodHistoricoOLD;
                $stNomHistoricoOLD = str_replace( chr(10), "", $stNomHistoricoOLD );
                $stNomHistoricoOLD = wordwrap( $stNomHistoricoOLD, 66,chr(13) );
                $arNomHistoricoOLD = explode( chr(13), $stNomHistoricoOLD );
                foreach ($arNomHistoricoOLD as $stNomHistoricoOLD) {
                    $arRecordSet[$inCount]['stDescricao'  ] = $stNomHistoricoOLD;
                    $inCount++;
                }
                if ($nuValorOLD) {
                    $nuTotal += $nuValorOLD;
                    $arRecordSet[$inCount]['nuValor'] = number_format($nuValorOLD,2,',','.');
                    $inCount++;
                    if ($nuTotal) {
                        $arRecordSet[$inCount]['stDescricao'] = "TOTAL DÉBITO -----------> ";
                        $arRecordSet[$inCount]['inNivel'] = 10;
                        //$arRecordSet[$inCount]['nuValor'] = number_format($nuTotal,2,',','.');
                        $arRecordSet[$inCount]['nuValor'] = number_format($nuTotalDebito,2,',','.');
                        $inCount++;
                        $arRecordSet[$inCount]['stDescricao'] = "TOTAL CRÉDITO ----------> ";
                        $arRecordSet[$inCount]['inNivel'] = 10;
                        //$arRecordSet[$inCount]['nuValor'] = number_format($nuTotal,2,',','.');
                        $arRecordSet[$inCount]['nuValor'] = number_format($nuTotalCredito,2,',','.');
                        $inCount++;
                        $nuTotalCredito = 0;
                        $nuTotalDebito = 0;
                    }
                }
            }

            $nuTotal = 0;

            if ($stNomEntidadeOLD != $stEntidade) {
                $stNomEntidadeOLD = $stEntidade;
                $arRecordSet[$inCount]['stDescricao'] = 'ENTIDADE: '.$stEntidade;
                $arRecordSet[$inCount]['inCodLote']   = '';
                $arRecordSet[$inCount]['inSequencia'] = '';
                $arRecordSet[$inCount]['nuValor']     = '';
                $inCount++;
            }

            $stNomLote = 'LOTE: '.$stNomLote;
            $stNomLote = str_replace( chr(10), ' ', $stNomLote );
            $stNomLote = wordwrap( $stNomLote, 66, chr(13) );
            $arNomLote = explode( chr(13), $stNomLote );
            foreach ($arNomLote as $value) {
                $arRecordSet[$inCount]['stDescricao'] = $value;
                $inCount++;
            }

            $arRecordSet[$inCount]['inCodLote']   = $inCodLote;
            $arRecordSet[$inCount]['stDtLote']    = $stDtLote;
            $arRecordSet[$inCount]['inSequencia'] = $inSequencia;

            $inCodLoteOLD      = $inCodLote;
            $stDtLoteOLD       = $stDtLote;
            $inSequenciaOLD    = $inSequencia;
            $inCodEntidadeOLD  = $inCodEntidade;
            $stTipoOLD         = $stTipo;
        }

        $stTipo = ( trim($stTipoValor) == 'D' ) ? "DEB: " : $stTipo = "CRE: ";
        $arRecordSet[$inCount]['stTipoECodigo'] = $stTipo.$inCodPlano;
        $stDescricao = $stCodEstrutural."  ".$stNomConta;
        $stDescricao = str_replace( chr(10), " ", $stDescricao );
        $stDescricao = wordwrap( $stDescricao, 66, chr(13) );
        $arDescricao = explode( chr(13), $stDescricao );
        foreach ($arDescricao as $stDescricao) {
            $arRecordSet[$inCount]['stDescricao'  ] = $stDescricao;
            $inCount++;
        }
        $inCount--;
        $inCodHistoricoOLD = $inCodHistorico;
        $stNomHistoricoOLD = $stNomHistorico;
        $stTipoValorOLD = $stTipoValor;
        $nuValorOLD        = str_replace( '-','',$nuValor );

        $rsRecordSet->proximo();
    }

    if ($inCodHistoricoOLD) {
        if ($stTipoValorOLD == 'C') {
            $nuTotalCredito += $nuValorOLD;
        } elseif ($stTipoValorOLD == 'D') {
            $nuTotalDebito += $nuValorOLD;
        }

        $inCount++;
        $arRecordSet[$inCount]['stTipoECodigo'] = "HIS:  ".$inCodHistoricoOLD;
        $stNomHistoricoOLD = str_replace( chr(10), "", $stNomHistoricoOLD );
        $stNomHistoricoOLD = wordwrap( $stNomHistoricoOLD, 66,chr(13) );
        $arNomHistoricoOLD = explode( chr(13), $stNomHistoricoOLD );
        foreach ($arNomHistoricoOLD as $stNomHistoricoOLD) {
            $arRecordSet[$inCount]['stDescricao'  ] = $stNomHistoricoOLD;
            $inCount++;
        }
        if ($nuValorOLD) {
            $nuTotal += $nuValorOLD;
            $arRecordSet[$inCount]['nuValor'] = number_format($nuValorOLD,2,',','.');
            $inCount++;
            if ($nuTotal) {
                $arRecordSet[$inCount]['stDescricao'] = "TOTAL DÉBITO -----------> ";
                $arRecordSet[$inCount]['inNivel'] = 10;
                $arRecordSet[$inCount]['nuValor'] = number_format($nuTotalDebito,2,',','.');
                //$arRecordSet[$inCount]['nuValor'] = number_format($nuTotal,2,',','.');
                $inCount++;
                $arRecordSet[$inCount]['stDescricao'] = "TOTAL CRÉDITO ----------> ";
                $arRecordSet[$inCount]['inNivel'] = 10;
                $arRecordSet[$inCount]['nuValor'] = number_format($nuTotalCredito,2,',','.');
                //$arRecordSet[$inCount]['nuValor'] = number_format($nuTotal,2,',','.');
                $inCount++;

            }
        }
    }

    $rsRecordSet = new RecordSet;
    $rsRecordSet->preenche( $arRecordSet );

    return $obErro;
}

}
