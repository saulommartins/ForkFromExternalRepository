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
    * Classe de Regra para Razao
    * Data de Criação   : 26/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson Buzo

    * @package URBEM
    * @subpackage Regra

    $Revision: 32478 $
    $Name$
    $Author: tonismar $
    $Date: 2008-04-09 17:10:32 -0300 (Qua, 09 Abr 2008) $

    * Casos de uso: uc-02.02.27
*/

/*
$Log$
Revision 1.35  2007/06/19 20:19:14  bruce
Correção da identificação de Bug

Revision 1.34  2007/06/19 14:58:42  gelson
Correção na forma do commit, deve ser:
Bug #numerodobug#

Revision 1.33  2007/04/11 21:36:50  luciano
Bug#8824#

Revision 1.32  2007/03/14 15:13:26  luciano
#8478#

Revision 1.31  2007/03/12 18:50:11  luciano
#8483#

Revision 1.30  2007/02/23 13:19:10  cako
Bug #8483#

Revision 1.29  2007/02/23 13:00:03  cako
Bug #8478#

Revision 1.28  2006/10/24 18:10:47  domluc
Correção Bug #7243#

Revision 1.27  2006/09/14 16:35:59  jose.eduardo
Bug #6815#

Revision 1.26  2006/09/14 14:55:42  jose.eduardo
Bug #6832#

Revision 1.25  2006/09/08 10:14:27  jose.eduardo
Bug #6786#

Revision 1.24  2006/09/06 14:52:46  jose.eduardo
Bug #6786#

Revision 1.23  2006/08/23 17:02:52  jose.eduardo
Bug #6765#

Revision 1.22  2006/08/10 19:11:34  jose.eduardo
Bug #6468#

Revision 1.21  2006/08/07 12:10:10  jose.eduardo
Bug #6620#

Revision 1.20  2006/07/31 20:54:56  cako
Bug #4343#

Revision 1.19  2006/07/27 17:17:26  cako
Bug #4343#

Revision 1.18  2006/07/25 14:33:48  jose.eduardo
Bug #4343#

Revision 1.17  2006/07/21 14:31:27  jose.eduardo
Bug #6620#

Revision 1.16  2006/07/05 20:50:26  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE_RELATORIO         );
include_once ( CAM_GF_CONT_MAPEAMENTO."FContabilidadeRelatorioRazao.class.php" );
include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadePlanoContaAnalitica.class.php" );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"             );

/**
    * Classe de Regra para emissão de relatorio de razão
    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson Buzo
*/
class RContabilidadeRelatorioRazao extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $obREntidade;
/**
    * @var Object
    * @access Private
*/
var $obFContabilidadeRelatorioRazao;
/**
    * @var Object
    * @access Private
*/
var $obRContabilidadePlanoContaAnalitica;
/**
    * @var String
    * @access String
*/
var $inCodEntidade;
/**
    * @var Integer
    * @access Private
*/
var $stDtInicial;
/**
    * @var String
    * @access String
*/
var $stDtFinal;
/**
    * @var String
    * @access Integer
*/
var $inCodPlanoInicial;
/**
    * @var String
    * @access Integer
*/
var $inCodPlanoFinal;
/**
    * @var String
    * @access Private
*/
var $stCodEstruturalInicial;
/**
    * @var String
    * @access Private
*/
var $stCodEstruturalFinal;
/**
    * @var String
    * @access Private
*/
var $stExercicio;
/**
    * @var String
    * @access Private
*/
var $stFiltro;
/**
    * @var String
    * @access Private
*/
var $boMovimentacaoConta;
/**
    * @var String
    * @access Boolean
*/
var $boQuebraPaginaConta;
/**
    * @var String
    * @access Boolean
*/
var $boHistoricoCompleto;

/**
     * @access Public
     * @param Object $valor
*/
function setFContabilidadeRelatorioRazao($valor) { $this->obFContabilidadeRelatorioRazao = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setRContabilidadePlanoContaAnalitica($valor) { $this->obRContabilidadePlanoContaAnalitica = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setREntidade($valor) { $this->obREntidade     = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setCodEntidade($valor) { $this->inCodEntidade      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setDtInicial($valor) { $this->stDtInicial = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setDtFinal($valor) { $this->stDtFinal = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setCodPlanoInicial($valor) { $this->inCodPlanoInicial = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setCodPlanoFinal($valor) { $this->inCodPlanoFinal = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setCodEstruturalInicial($valor) { $this->stCodEstruturalInicial = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setCodEstruturalFinal($valor) { $this->stCodEstruturalFinal = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setExercicio($valor) { $this->stExercicio = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setFiltro($valor) { $this->stFiltro = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setMovimentacaoConta($valor) { $this->boMovimentacaoConta = $valor; }
/**
     * @access Public
     * @param Boolean $valor
*/
function setQuebraPaginaConta($valor) { $this->boQuebraPaginaConta = $valor; }
/**
     * @access Public
     * @param Boolean $valor
*/
function setHistoricoCompleto($valor) { $this->boHistoricoCompleto = $valor; }

/**
     * @access Public
     * @return Object
*/
function getFContabilidadeRelatorioRazao() { return $this->obFContabilidadeRelatorioRazao; }
/**
     * @access Public
     * @return Object
*/
function getRContabilidadePlanoContaAnalitica() { return $this->obRContabilidadePlanoContaAnalitica; }
/**
     * @access Public
     * @return String
*/
function getREntidade() { return $this->obREntidade;               }
/**
     * @access Public
     * @return Object
*/
function getCodEntidade() { return $this->inCodEntidade;                }
/**
     * @access Public
     * @return Object
*/

function getDtInicial() { return $this->stDtInicial; }
/**
     * @access Public
     * @return String
*/
function getDtFinal() { return $this->stDtFinal; }
/**
     * @access Public
     * @return Integer
*/
function getCodPlanoInicial() { return $this->inCodPlanoInicial; }
/**
     * @access Public
     * @return Integer
*/
function getCodPlanoFinal() { return $this->inCodPlanoFinal; }
/**
     * @access Public
     * @return String
*/
function getCodEstruturalInicial() { return $this->stCodEstruturalInicial; }
/**
     * @access Public
     * @return String
*/
function getCodEstruturalFinal() { return $this->stCodEstruturalFinal; }
/**
     * @access Public
     * @return String
*/
function getExercicio() { return $this->stExercicio; }
/**
     * @access Public
     * @return String
*/
function getFiltro() { return $this->stFiltro; }
/**
     * @access Public
     * @return String
*/
function getMovimentacaoConta() { return $this->boMovimentacaoConta; }
/**
     * @access Public
     * @return Boolean
*/
function getQuebraPaginaConta() { return $this->boQuebraPaginaConta; }
/**
     * @access Public
     * @return Boolean
*/
function getHistoricoCompleto() { return $this->boHistoricoCompleto; }

/**
    * Método Construtor
    * @access Private
*/
function RContabilidadeRelatorioRazao()
{
    $this->obFContabilidadeRelatorioRazao = new FContabilidadeRelatorioRazao;
    $this->obRContabilidadePlanoContaAnalitica = new RContabilidadePlanoContaAnalitica;
    $this->setREntidade                     ( new ROrcamentoEntidade                );
//$this->boQuebraPaginaConta = true;

}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$arRs , $stOrder = "")
{
    $stFiltro = "";
    if ( $this->getCodEntidade() ) {
        $stEntidade .= $this->getCodEntidade();
    } else {
        $this->obREntidade->listarUsuariosEntidade( $rsEntidades );
        while ( !$rsEntidades->eof() ) {
            $stEntidade .= $rsEntidades->getCampo( 'cod_entidade' ).",";
            $rsEntidades->proximo();
        }
        $stEntidade = substr( $stEntidade, 0, strlen($stEntidade) - 1 );
        $stEntidade = $stEntidade;
    }

/*    if ($this->stExercicio) {
        $stFiltro .= " l.exercicio = \'".$this->stExercicio."\' AND ";
    }*/
    if ($this->inCodPlanoInicial) {
        $stFiltro .= " pa.cod_plano >= ".$this->inCodPlanoInicial." AND ";
    }
    if ($this->inCodPlanoFinal) {
        $stFiltro .= " pa.cod_plano <= ".$this->inCodPlanoFinal." AND ";
    }
/*    if ($this->stDtInicial) {
        $this->obFContabilidadeRelatorioRazao->setDado( "stDtInicial" , $this->stDtInicial );
        $stFiltro .= "lo.dt_lote >= to_date(\'".$this->stDtInicial."\',\'dd/mm/yyyy\') AND ";
    }
    if ($this->stDtFinal) {
        $this->obFContabilidadeRelatorioRazao->setDado( "stDtFinal" , $this->stDtFinal );
        $stFiltro .= "lo.dt_lote <= to_date(\'".$this->stDtFinal."\',\'dd/mm/yyyy\') AND ";
    }*/
    if ($this->stCodEstruturalFinal) {
        $arCodEstruturalFinal = explode( '.' ,$this->stCodEstruturalFinal );
        $inSize = sizeof($arCodEstruturalFinal);
        for ($inSize -1; $inSize >= 0 ; $inSize--) {
            if ($arCodEstruturalFinal[$inSize-1] == 0) {
                $arCodEstruturalFinal[$inSize-1] = str_pad(9,strlen($arCodEstruturalFinal[$inSize-1]),'9',STR_PAD_LEFT);
            } else {
                break;
            }
        }
        $this->stCodEstruturalFinal = implode('.',$arCodEstruturalFinal);
    } else {
        $this->obRContabilidadePlanoContaAnalitica->recuperaMascaraConta( $stMascara );
        $this->stCodEstruturalFinal = $stMascara;
    }
    if (!$this->stCodEstruturalInicial) {
        $this->obRContabilidadePlanoContaAnalitica->recuperaMascaraConta( $stMascara );
        $this->stCodEstruturalInicial = str_replace(9,'0',$stMascara);
    }
    if ($this->stFiltro) {
        $stFiltro .= $this->stFiltro;
    }
    $stFiltro = ( $stFiltro ) ? " AND " . substr( $stFiltro,0,strlen($stFiltro)-4) : '';

    $this->obFContabilidadeRelatorioRazao->setDado( 'exercicio'             , $this->getExercicio()         );
    $this->obFContabilidadeRelatorioRazao->setDado( 'cod_estrutural_inicial', $this->stCodEstruturalInicial );
    $this->obFContabilidadeRelatorioRazao->setDado( 'cod_estrutural_final'  , $this->stCodEstruturalFinal   );
    $this->obFContabilidadeRelatorioRazao->setDado( 'filtro'                , $stFiltro                     );
    $this->obFContabilidadeRelatorioRazao->setDado( 'stDtInicial'           , $this->getDtInicial());
    $this->obFContabilidadeRelatorioRazao->setDado( 'stDtFinal'             , $this->getDtFinal());
    $this->obFContabilidadeRelatorioRazao->setDado( 'dtInicialAnterior'     , date('d/m/Y',mktime (0,0,0,1, 1,substr($this->getDtInicial(),6,4))));
    if ( $this->getDtInicial() == "01/01/".$this->getExercicio() ) {
        $stDtFinalAnterior = date('d/m/Y',mktime (0,0,0,substr($this->getDtinicial(),3,2) ,substr($this->getDtInicial(),0,2),substr($this->getDtInicial(),6,4)));
    } else {
        $stDtFinalAnterior = date('d/m/Y',mktime (0,0,0,substr($this->getDtinicial(),3,2) ,substr($this->getDtInicial(),0,2)-1,substr($this->getDtInicial(),6,4)));
    }
    $this->obFContabilidadeRelatorioRazao->setDado( 'dtFinalAnterior'       , $stDtFinalAnterior);
    $this->obFContabilidadeRelatorioRazao->setDado( 'stEntidade'            , $this->getCodEntidade());
    $this->obFContabilidadeRelatorioRazao->setDado( 'boMovimentacaoConta'   , $this->getMovimentacaoConta());

    if ( $this->getHistoricoCompleto() ) {
        $obErro = $this->obFContabilidadeRelatorioRazao->recuperaRelatorioRazaoHistoricoCompleto( $rsRecordSet, '', '' );
    } else {
        $obErro = $this->obFContabilidadeRelatorioRazao->recuperaTodos( $rsRecordSet, '', '' );
    }

    $arRecordSet = array();

    $inCount = -1;
    $nuTotalGeral = 0;
    $stPrimeiroSaldoAnterior = "";
    $flPrimeiroSaldoAnterior = 0;
    $nuSaldoAnterior = 0;
    while ( !$rsRecordSet->eof() ) {
        $nuValor  = str_replace('-','',$rsRecordSet->getCampo( "vl_lancamento") );

        $inCount++;

        if ( $rsRecordSet->getCampo("cod_estrutural") != $stCodEstruturalOld or $rsRecordSet->getCampo("dt_lote") != $stDtLoteOld ) {
            if ($nuCredito or $nuDebito) {
                $nuTotal        = bcadd($nuTotal        , bcsub($nuDebito , $nuCredito, 4 ), 4 );

                $nuTotalDebito  = bcadd( $nuTotalDebito , $nuDebito                        , 4 );
                $nuTotalCredito = bcadd( $nuTotalCredito, $nuCredito                       , 4 );

                /****
                 * TICKET #12619
                ****/
                $nuTotalCreditoMes = bcadd( $nuTotalCreditoMes , $nuCredito               , 4 );
                $nuTotalDebitoMes  = bcadd( $nuTotalDebitoMes  , $nuDebito                  , 4 );

                /****
                 * TICKET #12619
                ****/

                $arRecordSet[$inCount]['stDescHistorico']  = "DÉBITOS:     ";
                $arRecordSet[$inCount]['stDescHistorico'] .= number_format($nuDebito,2,',','.');
                $arRecordSet[$inCount]['stDescHistorico'] .= "                      CRÉDITOS:     ";
                $arRecordSet[$inCount]['stDescHistorico'] .= number_format($nuCredito,2,',','.');
                $arRecordSet[$inCount]['inContraPartida']  = "      SALDO NO DIA     ";
                if ($nuTotal > 0.00) {
                    $arRecordSet[$inCount]['nuValorDebito'] .= number_format(abs($nuTotal),2,',','.');
                    $inCount++;
                    $arRecordSet[$inCount]['nuValorCredito']   = str_pad('-',201,'-',STR_PAD_LEFT);
                    $inCount++;
                } else {
                    $arRecordSet[$inCount]['nuValorDebito'] .= " ";
                    $arRecordSet[$inCount]['nuValorCredito']   = number_format(abs($nuTotal),2,',','.');
                    $inCount++;
                    $arRecordSet[$inCount]['nuValorCredito']   = str_pad('-',201,'-',STR_PAD_LEFT);
                    $inCount++;
                }

                /****
                 * TICKET #12619
                ****/
                $inMesCorrente = (int) substr($rsRecordSet->arElementos[$rsRecordSet->getCorrente()]['dt_lote'],3,2);
                $inMesProx = (int) substr($rsRecordSet->arElementos[$rsRecordSet->getCorrente()+1]['dt_lote'],3,2);

                if ( ($inMesCorrente != $inMesProx) OR ($rsRecordSet->getCampo("cod_estrutural") != $stCodEstruturalOld) ) {
                    $arRecordSet[$inCount]['stDescHistorico']  = "TOTAL DÉB.:  ";
                    $arRecordSet[$inCount]['stDescHistorico'] .= number_format($nuTotalDebitoMes,2,',','.');
                    $arRecordSet[$inCount]['stDescHistorico'] .= "            TOTAL CRÉD.:  ";
                    $arRecordSet[$inCount]['stDescHistorico'] .= number_format($nuTotalCreditoMes,2,',','.');
                    $arRecordSet[$inCount]['inContraPartida']  = "SALDO FINAL:  ";

                    if ($nuTotal>=0) {
                        $arRecordSet[$inCount]['nuValorDebito']   = number_format($nuTotal,2,',','.');
                    } else {
                        $arRecordSet[$inCount]['nuValorCredito']  = number_format(abs($nuTotal),2,',','.');
                    }

                    $inCount++;
                    $arRecordSet[$inCount]['nuValorCredito']   = str_pad('-',201,'-',STR_PAD_LEFT);
                    $inCount++;

                    $nuTotalDebitoMes = 0;
                    $nuTotalCreditoMes = 0;
                    $nuTotalMes = 0;
                }
                /****
                 * TICKET #12619
                ****/

                $nuCredito = 0;
                $nuDebito  = 0;
            }

            if ( $stCodEstruturalOld != $rsRecordSet->getCampo( "cod_estrutural" )) {
                $nuTotalGeral = bcadd( $nuTotalGeral, $nuTotal, 4 );

                $nuTotal = 0;
                //$nuTotal = $rsRecordSet->getCampo("saldo_anterior");
                if ($inCount > 0) {
                    $arRecordSet[$inCount]['boQuebraPagina'] = true;
                    $inCount++;
                }

                $arRecordSet[$inCount]['stDtLote']           = "";
                $arRecordSet[$inCount]['stDtLoteCabecalho']  = "     CONTA: ";
                $arRecordSet[$inCount]['stLoteSeq']          = "";
                $arRecordSet[$inCount]['stLoteSeqCabecalho'] = $rsRecordSet->getCampo( 'cod_plano' );
                $stDescricao = $rsRecordSet->getCampo( "cod_estrutural" ) . " - " . $rsRecordSet->getCampo("nom_conta");
                $arRecordSet[$inCount]['stDescHistoricoCabecalho'] = $stDescricao;
                $arRecordSet[$inCount]['stDescHistorico'] = "";

                $arRecordSet[$inCount]['inContraPartida']          = "";
                $arRecordSet[$inCount]['inContraPartidaCabecalho'] = " SALDO ANTERIOR:   ";
                if (number_format($rsRecordSet->getCampo("saldo_anterior"),2,',','.')>0.00) {
                    $arRecordSet[$inCount]['nuValorDebito']          = "";
                    $arRecordSet[$inCount]['nuValorDebitoCabecalho'] = number_format($rsRecordSet->getCampo("saldo_anterior"),2,',','.');
                    $nuSaldoAntDebito = str_replace('-','',$rsRecordSet->getCampo("saldo_anterior"));
                    $nuSaldoAnterior = $nuSaldoAntDebito;
                } else {
                    $arRecordSet[$inCount]['nuValorCredito']          = "";
                    $arRecordSet[$inCount]['nuValorCreditoCabecalho'] = number_format(abs($rsRecordSet->getCampo("saldo_anterior")),2,',','.');
                    $nuSaldoAntCredito = str_replace('-','',$rsRecordSet->getCampo("saldo_anterior"));
                    $nuSaldoAnterior = $nuSaldoAntCredito * -1;
                }

                if ( $rsRecordSet->getCampo("num_lancamentos") > 1 ) {
                    if (!$stPrimeiroSaldoAnterior) {
                        $flPrimeiroSaldoAnterior = $rsRecordSet->getCampo("saldo_anterior");
                        $stPrimeiroSaldoAnterior = "S";
                    }
                } else {
                    //$nuTotalDebito = bcadd( $nuTotalDebito , $rsRecordSet->getCampo("saldo_anterior"), 4);
                    $nuTotalDebito = bcadd( $nuTotalDebito , $nuDebito);
                }

                $inCount++;

                //if ($rsRecordSet->getCampo("tipo")<>"I") {
                //  //$nuTotal+=$rsRecordSet->getCampo("saldo_anterior");
                //}

                $nuTotal+=$rsRecordSet->getCampo("saldo_anterior");
            }
            $stCodEstruturalOld = $rsRecordSet->getCampo( "cod_estrutural" );
            $stDtLoteOld        = $rsRecordSet->getCampo("dt_lote");
        }
        if ($rsRecordSet->getCampo("tipo")<>"I") {
            $arRecordSet[$inCount]['stDtLote']         = $rsRecordSet->getCampo("dt_lote");
            $arRecordSet[$inCount]['stLoteSeq']        = $rsRecordSet->getCampo("tipo").' '.$rsRecordSet->getCampo("cod_lote")." - ".$rsRecordSet->getCampo("sequencia");
            $arRecordSet[$inCount]['inContraPartida']  = $rsRecordSet->getCampo("contra_partida");
            if ($rsRecordSet->getCampo("tipo_valor") == 'D' ) {
                $nuDebito = bcadd( $nuDebito, $nuValor, 4 );
                $arRecordSet[$inCount]['nuValorDebito']   = $nuValor ? number_format($nuValor,2,',','.') : "";
            } else {
                $nuCredito = bcadd( $nuCredito, $nuValor, 4 );
                $arRecordSet[$inCount]['nuValorCredito']  = $nuValor ? number_format($nuValor,2,',','.') : "";
            }

            $stHistorico = $rsRecordSet->getCampo("nom_historico") ."  ".$rsRecordSet->getCampo("complemento")." - ".$rsRecordSet->getCampo("observacao");
            $stHistorico = str_replace( chr(10), '',$stHistorico );
            $stHistorico = wordwrap( $stHistorico, 90, chr(13) );
            $arHistorico = explode( chr(13), $stHistorico );
            foreach ($arHistorico as $value) {
                $arRecordSet[$inCount]['stDescHistorico']  = $value;
                $inCount++;
            }
        }
        $inCount--;

        $rsRecordSet->proximo();
    }

    if ($nuCredito or $nuDebito) {
        $inCount++;
//        $nuTotal = 0;

        $nuTotal        = bcadd (bcsub( $nuDebito, $nuCredito, 4 ), $nuTotal , 4);
//        $nuTotal        = bcsub( $nuDebito, $nuCredito, 4 );
//        $nuTotalDebito  = bcadd($nuSaldoAntDebito, bcadd( $nuTotalDebito , $nuDebito  , 4 ), 4);
        $nuTotalDebitoMes  = bcadd( $nuTotalDebitoMes , $nuDebito  , 4 );
//        $nuTotalCredito = bcadd($nuSaldoAntCredito, bcadd( $nuTotalCredito, $nuCredito , 4 ),4);
        $nuTotalCreditoMes = bcadd( $nuTotalCreditoMes, $nuCredito , 4 );

        $arRecordSet[$inCount]['stDescHistorico']  = "DÉBITOS:      ";
        $arRecordSet[$inCount]['stDescHistorico'] .= number_format($nuDebito,2,',','.');
        $arRecordSet[$inCount]['stDescHistorico'] .= "                      CRÉDITOS:     ";
        $arRecordSet[$inCount]['stDescHistorico'] .= number_format($nuCredito,2,',','.');
        $arRecordSet[$inCount]['inContraPartida']  = "      SALDO NO DIA     ";
        if ($nuTotal > 0.00) {
            $arRecordSet[$inCount]['nuValorDebito'] .= number_format(abs($nuTotal),2,',','.');
            $inCount++;
            $arRecordSet[$inCount]['nuValorCredito']   = str_pad('-',201,'-',STR_PAD_LEFT);
            $inCount++;
        } else {
            $arRecordSet[$inCount]['nuValorDebito'] .= " ";
            $arRecordSet[$inCount]['nuValorCredito']   = number_format(abs($nuTotal),2,',','.') ;
            $inCount++;
            $arRecordSet[$inCount]['nuValorCredito']   = str_pad('-',201,'-',STR_PAD_LEFT);
            $inCount++;
        }

        $nuCredito = 0;
        $nuDebito  = 0;

        /****
         * TICKET #12619
        ****/
        $arRecordSet[$inCount]['stDescHistorico']  = "TOTAL DÉB.:  ";
        $arRecordSet[$inCount]['stDescHistorico'] .= number_format($nuTotalDebitoMes,2,',','.');
        $arRecordSet[$inCount]['stDescHistorico'] .= "            TOTAL CRÉD.:  ";
        $arRecordSet[$inCount]['stDescHistorico'] .= number_format($nuTotalCreditoMes,2,',','.');
        $arRecordSet[$inCount]['inContraPartida']  = "SALDO FINAL:  ";

        if ($nuTotal>=0) {
            $arRecordSet[$inCount]['nuValorDebito']   = number_format($nuTotal,2,',','.');
        } else {
            $arRecordSet[$inCount]['nuValorCredito']  = number_format(abs($nuTotal),2,',','.');
        }

        $inCount++;
        $arRecordSet[$inCount]['nuValorCredito']   = str_pad('-',201,'-',STR_PAD_LEFT);
        $inCount++;
        /****
         * TICKET #12619
        ****/

    }

    $inCountArray = 0;
    $inCountLinha = 0;
    foreach ($arRecordSet as $arLinha) {
        if ($arLinha['boQuebraPagina']) {
            $rsRecordSet = new RecordSet();
            $rsRecordSet->preenche( $arRecord );
            $arRs[$inCountArray] = $rsRecordSet;
            $inCountArray++;
            $inCountLinha = 0;
            $arRecord = array();
        } else {
            $arRecord[$inCountLinha] = $arLinha;
            $inCountLinha++;
        }
    }

    if ($arRecord) {
        $rsRecordSet = new RecordSet();
        $rsRecordSet->preenche( $arRecord );
        $arRs[$inCountArray] = $rsRecordSet;
    }

    return $obErro;
}

}
