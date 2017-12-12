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
* Classe de Regra de Negócios para modelo 4 do modulo LRF
* Data de Criação: 24/05/2005

* @author Analista: Diego Barbosa Victoria
* @author Desenvolvedor: Cleisson Barboza

* @package URBEM
* @subpackage Regra

$Revision: 30668 $
$Name$
$Author: cleisson $
$Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

* Casos de uso: uc-02.05.06
*/

/*
$Log$
Revision 1.8  2006/07/05 20:44:40  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO    );
include_once( CAM_GF_LRF_MAPEAMENTO."FLRFModelosExecutivo.class.php"   );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"          );
include_once( CAM_FW_PDF."RRelatorio.class.php"                  );

/**
    * Classe de Regra de Negócios Modelos Executivo
    * @author Desenvolvedor: Lucas Leusin Oaigen
*/
class RLRFRelatorioModelos4 extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $obFLRFModelosExecutivo;
/*
    * @var Object
    * @access Private
*/
var $obROrcamentoEntidade;
/**
    * @var Object
    * @access Private
*/
var $inCodModelo;
/**
    * @var Object
    * @access Private
*/
var $inCodEntidade;
/**
    * @var Integer
    * @access Private
*/
var $inExercicio;
/**
    * @var String
    * @access Private
*/
var $stDataInicial;
/**
    * @var String
    * @access Private
*/
var $stDataFinal;
/**
    * @var String
    * @access Private
*/
var $stFiltro;
/**
    * @var String
    * @access Private
*/
var $stTipoValorDespesa;

/**
    * @var Integer
    * @access Private
*/
function setFLRFModelosExecutivo($valor) { $this->obFLRFModelosExecutivo  = $valor; }
/*
    * @access Public
    * @param Object $valor
*/
function setROrcamentoEntidade($valor) { $this->obROrcamentoEntidade = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setCodModelo($valor) { $this->inCodModelo      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setCodEntidade($valor) { $this->inCodEntidade      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setExercicio($valor) { $this->inExercicio        = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setDataInicial($valor) { $this->stDataInicial              = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setDataFinal($valor) { $this->stDataFinal               = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setFiltro($valor) { $this->stFiltro           = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setTipoValorDespesa($valor) { $this->stTipoValorDespesa           = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function getFLRFModelosExecutivo() { return $this->obFLRFModelosExecutivo;   }
/*
    * @access Public
    * @return Object
*/
function getROrcamentoEntidade() { return $this->obROrcamentoEntidade  ;        }
/**
     * @access Public
     * @param Object $valor
*/
function getCodModelo() { return $this->inCodModelo;                     }
/**
     * @access Public
     * @param Object $valor
*/
function getCodEntidade() { return $this->inCodEntidade;                 }
/**
     * @access Public
     * @return Object
*/
function getExercicio() { return $this->inExercicio;                   }
/**
     * @access Public
     * @param Object $valor
*/
function getDataInicial() { return $this->stDataInicial;            }
/**
     * @access Public
     * @param Object $valor
*/
function getDataFinal() { return $this->stDataFinal;              }
/**
     * @access Public
     * @return Object
*/
function getFiltro() { return $this->stFiltro;                      }
/**
     * @access Public
     * @return Object
*/
function getTipoValorDespesa() { return $this->stTipoValorDespesa;           }

/**
     * @access Public
     * @return Object
*/
function RLRFRelatorioModelos4()
{
    $sessao = $_SESSION ['sessao'];
    $this->setFLRFModelosExecutivo       ( new FLRFModelosExecutivo         );
    $this->obROrcamentoEntidade          = new ROrcamentoEntidade;
    $this->obRRelatorio                  = new RRelatorio;
    $this->obROrcamentoEntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRecordSet ,&$rsRecordSet1, &$rsRecordSet2, &$rsRecordSet3, &$rsRecordSet4, $stOrder = "")
{
    $stFiltro = "";
    if ( $this->getCodEntidade() ) {
        $stEntidade .= $this->getCodEntidade();
    } else {
        $this->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidades );
        while ( !$rsEntidades->eof() ) {
            $stEntidade .= $rsEntidades->getCampo( 'cod_entidade' ).",";
            $rsEntidades->proximo();
        }
        $stEntidade = substr( $stEntidade, 0, strlen($stEntidade) - 1 );
        $stEntidade = $stEntidade;
    }

    $this->obFLRFModelosExecutivo->setDado("inCodModelo",$this->getCodModelo());
    $this->obFLRFModelosExecutivo->setDado("stDataInicial",$this->getDataInicial());
    $this->obFLRFModelosExecutivo->setDado("stDataFinal",$this->getDataFinal());
    $this->obFLRFModelosExecutivo->setDado("exercicio",$this->getExercicio());
    $this->obFLRFModelosExecutivo->setDado("stEntidade",$this->getCodEntidade());
    $this->obFLRFModelosExecutivo->setDado("stFiltro",$this->getFiltro());
    $this->obFLRFModelosExecutivo->setDado("stTipoValorDespesa",$this->getTipoValorDespesa());
    $obErro = $this->obFLRFModelosExecutivo->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder );
   // $this->obFLRFModelosExecutivo->debug();

    $inCount                = 0;
    $inCount1               = 0;
    $inCount2               = 0;
    $inCount3               = 0;
    $inCountLinha           = 0;
    $total_divida_contabil  = 0;
    $total_divida_ajuste    = 0;
    $total_divida_ajustado  = 0;
    $total_AC_ARLP_contabil = 0;
    $total_AC_ARLP_ajuste   = 0;
    $total_AC_ARLP_ajustado = 0;
    $total_PC_contabil      = 0;
    $total_PC_ajuste        = 0;
    $total_PC_ajustado      = 0;
    $arRecord           = array();
    $arRecord1          = array();
    $arRecord2          = array();
    $arRecord3          = array();
    $arRecord4          = array();

    while ( !$rsRecordSet->eof()) {
        $stNomContaTemp = str_replace( chr(10), "", $rsRecordSet->getCampo('nom_conta') );
        $stNomContaTemp = wordwrap( $stNomContaTemp, 95,chr(13) );    // NOTA, o valor 66 eh o q deve ser mudado pra
        $arNomContaOLD = explode( chr(13), $stNomContaTemp );         //maiores ou menores

        if ($rsRecordSet->getCorrente() < 5) {
            $inCountLinha = $inCount;
            if ( $rsRecordSet->getCampo('redutora') == 't' ) {
                foreach ($arNomContaOLD as $stNomContaTemp) {
                    if ($inCount == $inCountLinha) {
                        $stNomContaTemp = "   (-)".$stNomContaTemp;
                    } else {
                        $stNomContaTemp = "   ".$stNomContaTemp;
                    }
                    $arRecord[$inCountLinha]['coluna1']    = $stNomContaTemp;;
                    $inCountLinha++;
                }
                $arRecord[$inCount]['coluna2']    = "(".$rsRecordSet->getCampo('cod_estrutural').")";

                $total_divida_contabil  = bcsub($total_divida_contabil,$rsRecordSet->getCampo('vl_contabil'),4);
                $total_divida_ajuste    = bcsub($total_divida_ajuste,$rsRecordSet->getCampo('vl_ajuste'),4);
                $total_divida_ajustado  = bcsub($total_divida_ajustado,$rsRecordSet->getCampo('vl_ajustado'),4);
            } else {
                foreach ($arNomContaOLD as $stNomContaTemp) {
                    $arRecord[$inCountLinha]['coluna1']    = $stNomContaTemp;
                    $inCountLinha++;
                }
                $arRecord[$inCount]['coluna2']    = $rsRecordSet->getCampo('cod_estrutural');
                $total_divida_contabil  = bcadd($total_divida_contabil,$rsRecordSet->getCampo('vl_contabil'),4);
                $total_divida_ajuste    = bcadd($total_divida_ajuste,$rsRecordSet->getCampo('vl_ajuste'),4);
                $total_divida_ajustado  = bcadd($total_divida_ajustado,$rsRecordSet->getCampo('vl_ajustado'),4);
            }
            $arRecord[$inCount]['coluna3']    = number_format($rsRecordSet->getCampo('vl_contabil'), 2, ',', '.' );
            $arRecord[$inCount]['coluna4']    = number_format($rsRecordSet->getCampo('vl_ajuste'), 2, ',', '.' );
            $arRecord[$inCount]['coluna5']    = number_format($rsRecordSet->getCampo('vl_ajustado'), 2, ',', '.' );

            $inCount = $inCountLinha-1;
            $inCount++;

        } elseif ( $rsRecordSet->getCorrente() < 13) {
            $inCountLinha = $inCount1;
            if ( $rsRecordSet->getCampo('redutora') == 't' ) {
                foreach ($arNomContaOLD as $stNomContaTemp) {
                    if ($inCount1 == $inCountLinha) {
                        $stNomContaTemp = "   (-)".$stNomContaTemp;
                    } else {
                        $stNomContaTemp = "   ".$stNomContaTemp;
                    }
                    $arRecord1[$inCountLinha]['coluna1']    = $stNomContaTemp;;
                    $inCountLinha++;
                }
                $arRecord1[$inCount1]['coluna2']    = "(".$rsRecordSet->getCampo('cod_estrutural').")";

                $total_AC_ARLP_contabil = bcsub($total_AC_ARLP_contabil,$rsRecordSet->getCampo('vl_contabil'),4);
                $total_AC_ARLP_ajuste   = bcsub($total_AC_ARLP_ajuste,$rsRecordSet->getCampo('vl_ajuste'),4);
                $total_AC_ARLP_ajustado = bcsub($total_AC_ARLP_ajustado,$rsRecordSet->getCampo('vl_ajustado'),4);
            } else {
                foreach ($arNomContaOLD as $stNomContaTemp) {
                    $arRecord1[$inCountLinha]['coluna1']    = $stNomContaTemp;
                    $inCountLinha++;
                }
                $arRecord1[$inCount1]['coluna2']    = $rsRecordSet->getCampo('cod_estrutural');
                $total_AC_ARLP_contabil = bcadd($total_AC_ARLP_contabil,$rsRecordSet->getCampo('vl_contabil'),4);
                $total_AC_ARLP_ajuste = bcadd($total_AC_ARLP_ajuste,$rsRecordSet->getCampo('vl_ajuste'),4);
                $total_AC_ARLP_ajustado = bcadd($total_AC_ARLP_ajustado,$rsRecordSet->getCampo('vl_ajustado'),4);
            }
            $arRecord1[$inCount1]['coluna3']    = number_format($rsRecordSet->getCampo('vl_contabil'), 2, ',', '.' );
            $arRecord1[$inCount1]['coluna4']    = number_format($rsRecordSet->getCampo('vl_ajuste'), 2, ',', '.' );
            $arRecord1[$inCount1]['coluna5']    = number_format($rsRecordSet->getCampo('vl_ajustado'), 2, ',', '.' );
            $inCount1 = $inCountLinha-1;
            $inCount1++;

        } elseif ( $rsRecordSet->getCorrente() < 18) {
            $inCountLinha = $inCount2;
            if ( $rsRecordSet->getCampo('redutora') == 't' ) {
                foreach ($arNomContaOLD as $stNomContaTemp) {
                    if ($inCount2 == $inCountLinha) {
                        $stNomContaTemp = "   (-)".$stNomContaTemp;
                    } else {
                        $stNomContaTemp = "   ".$stNomContaTemp;
                    }
                    $arRecord2[$inCountLinha]['coluna1']    = $stNomContaTemp;;
                    $inCountLinha++;
                }
                $arRecord2[$inCount2]['coluna2']    = "(".$rsRecordSet->getCampo('cod_estrutural').")";

                $total_PC_contabil   = bcsub($total_PC_contabil,$rsRecordSet->getCampo('vl_contabil'),4);
                $total_PC_ajuste     = bcsub($total_PC_ajuste,$rsRecordSet->getCampo('vl_ajuste'),4);
                $total_PC_ajustado   = bcsub($total_PC_ajustado,$rsRecordSet->getCampo('vl_ajustado'),4);
            } else {
                foreach ($arNomContaOLD as $stNomContaTemp) {
                    $arRecord2[$inCountLinha]['coluna1']    = $stNomContaTemp;
                    $inCountLinha++;
                }
                $arRecord2[$inCount2]['coluna2']    = $rsRecordSet->getCampo('cod_estrutural');
                $total_PC_contabil   = bcadd($total_PC_contabil,$rsRecordSet->getCampo('vl_contabil'),4);
                $total_PC_ajuste     = bcadd($total_PC_ajuste,$rsRecordSet->getCampo('vl_ajuste'),4);
                $total_PC_ajustado   = bcadd($total_PC_ajustado,$rsRecordSet->getCampo('vl_ajustado'),4);
            }
           $arRecord2[$inCount2]['coluna3']    = number_format($rsRecordSet->getCampo('vl_contabil'), 2, ',', '.' );
           $arRecord2[$inCount2]['coluna4']    = number_format($rsRecordSet->getCampo('vl_ajuste'), 2, ',', '.' );
           $arRecord2[$inCount2]['coluna5']    = number_format($rsRecordSet->getCampo('vl_ajustado'), 2, ',', '.' );

           $inCount2 = $inCountLinha-1;
           $inCount2++;

        } elseif ( $rsRecordSet->getCorrente() < 21) {
           $inCountLinha = $inCount3;
            if ( $rsRecordSet->getCampo('redutora') == 't' ) {
                foreach ($arNomContaOLD as $stNomContaTemp) {
                    if ($inCount3 == $inCountLinha) {
                        $stNomContaTemp = "   (-)".$stNomContaTemp;
                    } else {
                        $stNomContaTemp = "   ".$stNomContaTemp;
                    }
                    $arRecord3[$inCountLinha]['coluna1']    = $stNomContaTemp;;
                    $inCountLinha++;
                }
                $arRecord3[$inCount3]['coluna2']    = "(".$rsRecordSet->getCampo('cod_estrutural').")";
            } else {
                foreach ($arNomContaOLD as $stNomContaTemp) {
                    $arRecord3[$inCountLinha]['coluna1']    = $stNomContaTemp;
                    $inCountLinha++;
                }
                $arRecord3[$inCount3]['coluna2']    = $rsRecordSet->getCampo('cod_estrutural');
            }
           $arRecord3[$inCount3]['coluna3']    = number_format($rsRecordSet->getCampo('vl_contabil'), 2, ',', '.' );
           $arRecord3[$inCount3]['coluna4']    = number_format($rsRecordSet->getCampo('vl_ajuste'), 2, ',', '.' );
           $arRecord3[$inCount3]['coluna5']    = number_format($rsRecordSet->getCampo('vl_ajustado'), 2, ',', '.' );

           $inCount3 = $inCountLinha-1;
           $inCount3++;

        }
        $rsRecordSet->proximo();
    }

    if ($total_AC_ARLP_ajustado < $total_PC_ajustado) {
        $arRecord3[$inCount3]['coluna1']    = "INSUFICIÊNCIA FINANCEIRA";
        $arRecord3[$inCount3]['coluna2']    = "";
        $arRecord3[$inCount3]['coluna3']    = number_format($total_AC_ARLP_contabil - $total_PC_contabil,   2, ',', '.' );
        $arRecord3[$inCount3]['coluna4']    = number_format($total_AC_ARLP_ajuste   - $total_PC_ajuste,     2, ',', '.' );
        $arRecord3[$inCount3]['coluna5']    = number_format($total_AC_ARLP_ajustado - $total_PC_ajustado,   2, ',', '.' );
    }
    if ($total_divida_contabil  < ($total_AC_ARLP_contabil - $total_PC_contabil)) {
        $divida_contabil=0;
        $divida_ajuste  =0;
        $divida_ajustado=0;
    } else {
        $divida_contabil=($total_divida_contabil)  - ($total_AC_ARLP_contabil - $total_PC_contabil  );
        $divida_ajuste  =($total_divida_ajuste)    - ($total_AC_ARLP_ajuste   - $total_PC_ajuste    );
        $divida_ajustado=($total_divida_ajustado)  - ($total_AC_ARLP_ajustado - $total_PC_ajustado  );
    }

        $arRecord4[0]['coluna1']    = "TOTAL";
        $arRecord4[0]['coluna2']    = "";
        $arRecord4[0]['coluna3']    = abs( number_format($divida_contabil, 2, ',', '.' ) );
        $arRecord4[0]['coluna4']    = abs( number_format($divida_ajuste  , 2, ',', '.' ) );
        $arRecord4[0]['coluna5']    = abs( number_format($divida_ajustado, 2, ',', '.' ) );

    $rsRecordSet      = new RecordSet;
    $rsRecordSet1     = new RecordSet;
    $rsRecordSet2     = new RecordSet;
    $rsRecordSet3     = new RecordSet;
    $rsRecordSet4     = new RecordSet;

    $rsRecordSet->preenche( $arRecord );
    $rsRecordSet1->preenche( $arRecord1 );
    $rsRecordSet2->preenche( $arRecord2 );
    $rsRecordSet3->preenche( $arRecord3 );
    $rsRecordSet4->preenche( $arRecord4 );

    return $obErro;
}

}
