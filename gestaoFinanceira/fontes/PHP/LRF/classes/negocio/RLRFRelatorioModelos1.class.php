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
* Classe de Regra de Negócios para modelo 1 do modulo LRF
* Data de Criação: 25/05/2005

* @author Analista: Diego Barbosa
* @author Desenvolvedor: Diego Lemos de Souza

* @package URBEM
* @subpackage Regra

$Revision: 30668 $
$Name$
$Author: cleisson $
$Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

* Casos de uso: uc-02.05.03
*/

/*
$Log$
Revision 1.6  2006/07/05 20:44:40  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO    );
include_once( CAM_GF_LRF_MAPEAMENTO."FLRFModelosExecutivo.class.php"   );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"          );
include_once( CAM_FW_PDF."RRelatorio.class.php"                  );

/**
    * Classe de Regra de Negócios
    * @author Desenvolvedor: Diego Lemos de Souza
*/
class RLRFRelatorioModelos1 extends PersistenteRelatorio
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
function RLRFRelatorioModelos1()
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
function geraRecordSet(&$rsRecordSet , &$rsRecordSet1, &$rsRecordSet2, &$rsRecordSet3, &$rsRecordSet4, &$rsRecordSet5, &$rsRecordSet6, &$rsRecordSet7, &$rsRecordSet8, &$rsRecordSet9, &$rsRecordSetTotal, $stOrder = "")
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
//    $this->obFLRFModelosExecutivo->debug();

    $inCount            = 0;
    $inCount2           = 0;
    $inCount3           = 0;
    $inCount4           = 0;
    $inCount5           = 0;
    $inCount6           = 0;
    $inCount7           = 0;
    $inCount8           = 0;
    $inCount9           = 0;
    $inCount10          = 0;
    $inCount11          = 0;
    $inTotal            = 0;
    $inTotalGeral       = 0;
    $arRecord           = array();
    $arRecord1          = array();
    $arRecord2          = array();
    $arRecord3          = array();
    $arRecord4          = array();
    $arRecord5          = array();
    $arRecord6          = array();
    $arRecord7          = array();
    $arRecord8          = array();
    $arRecord9          = array();
    $arRecordTotal      = array();

    while ( !$rsRecordSet->eof()) {
        $stNomContaTemp = str_replace( chr(10), "", $rsRecordSet->getCampo('nom_conta') );
        $stNomContaTemp = wordwrap( $stNomContaTemp, 95,chr(13) );    // NOTA, o valor 66 eh o q deve ser mudado pra
        $arNomContaOLD = explode( chr(13), $stNomContaTemp );         //maiores ou menores
        if ($rsRecordSet->getCorrente() == 1) {
            $inCount2 = $inCount;
            if ( $rsRecordSet->getCampo('redutora') == 't' ) {
                foreach ($arNomContaOLD as $stNomContaTemp) {
                    if ($inCount == $inCount2) {
                        $stNomContaTemp = "   (-)".$stNomContaTemp;
                    } else {
                        $stNomContaTemp = "   ".$stNomContaTemp;
                    }
                    $arRecord[$inCount2]['coluna1']    = $stNomContaTemp;;
                    $inCount2++;
                }
                $arRecord[$inCount]['coluna2']    = "(".$rsRecordSet->getCampo('cod_estrutural').")";

                $flVlContabil1                     = bcsub($flVlContabil1,$rsRecordSet->getCampo('vl_contabil'),4);
                $flVlAjuste1                       = bcsub($flVlAjuste1,$rsRecordSet->getCampo('vl_ajuste'),4);
                $flVlAjustado1                     = bcsub($flVlAjustado1,$rsRecordSet->getCampo('vl_ajustado'),4);
            } else {
                foreach ($arNomContaOLD as $stNomContaTemp) {
                    $arRecord[$inCount2]['coluna1']    = $stNomContaTemp;
                    $inCount2++;
                }
                $arRecord[$inCount]['coluna2']    = $rsRecordSet->getCampo('cod_estrutural');
                $flVlContabil1                     = bcadd($flVlContabil1,$rsRecordSet->getCampo('vl_contabil'),4);
                $flVlAjuste1                       = bcadd($flVlAjuste1,$rsRecordSet->getCampo('vl_ajuste'),4);
                $flVlAjustado1                     = bcadd($flVlAjustado1,$rsRecordSet->getCampo('vl_ajustado'),4);
            }
            $arRecord[$inCount]['coluna3']    = number_format($rsRecordSet->getCampo('vl_contabil'), 2, ',', '.' );
            $arRecord[$inCount]['coluna4']    = number_format($rsRecordSet->getCampo('vl_ajuste'), 2, ',', '.' );
            $arRecord[$inCount]['coluna5']    = number_format($rsRecordSet->getCampo('vl_ajustado'), 2, ',', '.' );
            $inCount = $inCount2 - 1;
            $inCount++;
        } elseif ( $rsRecordSet->getCorrente() < 7 ) {
            $inCount2 = $inCount3;
            if ( $rsRecordSet->getCampo('redutora') == 't' ) {
                foreach ($arNomContaOLD as $stNomContaTemp) {
                    if ($inCount3 == $inCount2) {
                        $stNomContaTemp = "   (-)".$stNomContaTemp;
                    } else {
                        $stNomContaTemp = "   ".$stNomContaTemp;
                    }
                    $arRecord1[$inCount2]['coluna1']    = $stNomContaTemp;;
                    $inCount2++;
                }
                $arRecord1[$inCount3]['coluna2']    = "(".$rsRecordSet->getCampo('cod_estrutural').")";

                $flVlContabil                     = bcsub($flVlContabil,$rsRecordSet->getCampo('vl_contabil'),4);
                $flVlAjuste                       = bcsub($flVlAjuste,$rsRecordSet->getCampo('vl_ajuste'),4);
                $flVlAjustado                     = bcsub($flVlAjustado,$rsRecordSet->getCampo('vl_ajustado'),4);
            } else {
                foreach ($arNomContaOLD as $stNomContaTemp) {
                    $arRecord1[$inCount2]['coluna1']    = $stNomContaTemp;
                    $inCount2++;
                }
                $arRecord1[$inCount3]['coluna2']    = $rsRecordSet->getCampo('cod_estrutural');
                $flVlContabil                     = bcadd($flVlContabil,$rsRecordSet->getCampo('vl_contabil'),4);
                $flVlAjuste                       = bcadd($flVlAjuste,$rsRecordSet->getCampo('vl_ajuste'),4);
                $flVlAjustado                     = bcadd($flVlAjustado,$rsRecordSet->getCampo('vl_ajustado'),4);
            }
            $arRecord1[$inCount3]['coluna3']  = number_format($rsRecordSet->getCampo('vl_contabil'), 2, ',', '.' );
            $arRecord1[$inCount3]['coluna4']  = number_format($rsRecordSet->getCampo('vl_ajuste'), 2, ',', '.' );
            $arRecord1[$inCount3]['coluna5']  = number_format($rsRecordSet->getCampo('vl_ajustado'), 2, ',', '.' );
            $inCount3 = $inCount2 - 1;
            $inCount3++;
        } elseif ( $rsRecordSet->getCorrente() < 9 ) {
            $inCount2 = $inCount4;
            if ( $rsRecordSet->getCampo('redutora') == 't' ) {
                foreach ($arNomContaOLD as $stNomContaTemp) {
                    if ($inCount4 == $inCount2) {
                        $stNomContaTemp = "   (-)".$stNomContaTemp;
                    } else {
                        $stNomContaTemp = "   ".$stNomContaTemp;
                    }
                    $arRecord2[$inCount2]['coluna1']    = $stNomContaTemp;;
                    $inCount2++;
                }
                $arRecord2[$inCount4]['coluna2']    = "(".$rsRecordSet->getCampo('cod_estrutural').")";

                $flVlContabil                     = bcsub($flVlContabil,$rsRecordSet->getCampo('vl_contabil'),4);
                $flVlAjuste                       = bcsub($flVlAjuste,$rsRecordSet->getCampo('vl_ajuste'),4);
                $flVlAjustado                     = bcsub($flVlAjustado,$rsRecordSet->getCampo('vl_ajustado'),4);
            } else {
                foreach ($arNomContaOLD as $stNomContaTemp) {
                    $arRecord2[$inCount2]['coluna1']    = $stNomContaTemp;
                    $inCount2++;
                }
                $arRecord2[$inCount4]['coluna2']    = $rsRecordSet->getCampo('cod_estrutural');
                $flVlContabil                     = bcadd($flVlContabil,$rsRecordSet->getCampo('vl_contabil'),4);
                $flVlAjuste                       = bcadd($flVlAjuste,$rsRecordSet->getCampo('vl_ajuste'),4);
                $flVlAjustado                     = bcadd($flVlAjustado,$rsRecordSet->getCampo('vl_ajustado'),4);
            }
            $arRecord2[$inCount4]['coluna3']  = number_format($rsRecordSet->getCampo('vl_contabil'), 2, ',', '.' );
            $arRecord2[$inCount4]['coluna4']  = number_format($rsRecordSet->getCampo('vl_ajuste'), 2, ',', '.' );
            $arRecord2[$inCount4]['coluna5']  = number_format($rsRecordSet->getCampo('vl_ajustado'), 2, ',', '.' );
            $inCount4 = $inCount2 - 1;
            $inCount4++;
        } elseif ( $rsRecordSet->getCorrente() < 12 ) {
            $inCount2 = $inCount5;
            if ( $rsRecordSet->getCampo('redutora') == 't' ) {
                foreach ($arNomContaOLD as $stNomContaTemp) {
                    if ($inCount5 == $inCount2) {
                        $stNomContaTemp = "   (-)".$stNomContaTemp;
                    } else {
                        $stNomContaTemp = "   ".$stNomContaTemp;
                    }
                    $arRecord3[$inCount2]['coluna1']    = $stNomContaTemp;;
                    $inCount2++;
                }
                $arRecord3[$inCount5]['coluna2']    = "(".$rsRecordSet->getCampo('cod_estrutural').")";
                $flVlContabil                     = bcsub($flVlContabil,$rsRecordSet->getCampo('vl_contabil'),4);
                $flVlAjuste                       = bcsub($flVlAjuste,$rsRecordSet->getCampo('vl_ajuste'),4);
                $flVlAjustado                     = bcsub($flVlAjustado,$rsRecordSet->getCampo('vl_ajustado'),4);
            } else {
                foreach ($arNomContaOLD as $stNomContaTemp) {
                    $arRecord3[$inCount2]['coluna1']    = $stNomContaTemp;
                    $inCount2++;
                }
                $arRecord3[$inCount5]['coluna2']    = $rsRecordSet->getCampo('cod_estrutural');
                $flVlContabil                     = bcadd($flVlContabil,$rsRecordSet->getCampo('vl_contabil'),4);
                $flVlAjuste                       = bcadd($flVlAjuste,$rsRecordSet->getCampo('vl_ajuste'),4);
                $flVlAjustado                     = bcadd($flVlAjustado,$rsRecordSet->getCampo('vl_ajustado'),4);
            }
            $arRecord3[$inCount5]['coluna3']  = number_format($rsRecordSet->getCampo('vl_contabil'), 2, ',', '.' );
            $arRecord3[$inCount5]['coluna4']  = number_format($rsRecordSet->getCampo('vl_ajuste'), 2, ',', '.' );
            $arRecord3[$inCount5]['coluna5']  = number_format($rsRecordSet->getCampo('vl_ajustado'), 2, ',', '.' );
            $inCount5 = $inCount2 - 1;
            $inCount5++;
        } elseif ( $rsRecordSet->getCorrente() < 14 ) {
            $inCount2 = $inCount6;
            if ( $rsRecordSet->getCampo('redutora') == 't' ) {
                foreach ($arNomContaOLD as $stNomContaTemp) {
                    if ($inCount6 == $inCount2) {
                        $stNomContaTemp = "   (-)".$stNomContaTemp;
                    } else {
                        $stNomContaTemp = "   ".$stNomContaTemp;
                    }
                    $arRecord4[$inCount2]['coluna1']    = $stNomContaTemp;;
                    $inCount2++;
                }
                $arRecord4[$inCount6]['coluna2']    = "(".$rsRecordSet->getCampo('cod_estrutural').")";
                $flVlContabil                     = bcsub($flVlContabil,$rsRecordSet->getCampo('vl_contabil'),4);
                $flVlAjuste                       = bcsub($flVlAjuste,$rsRecordSet->getCampo('vl_ajuste'),4);
                $flVlAjustado                     = bcsub($flVlAjustado,$rsRecordSet->getCampo('vl_ajustado'),4);
            } else {
                foreach ($arNomContaOLD as $stNomContaTemp) {
                    $arRecord4[$inCount2]['coluna1']    = $stNomContaTemp;
                    $inCount2++;
                }
                $arRecord4[$inCount6]['coluna2']    = $rsRecordSet->getCampo('cod_estrutural');
                $flVlContabil                     = bcadd($flVlContabil,$rsRecordSet->getCampo('vl_contabil'),4);
                $flVlAjuste                       = bcadd($flVlAjuste,$rsRecordSet->getCampo('vl_ajuste'),4);
                $flVlAjustado                     = bcadd($flVlAjustado,$rsRecordSet->getCampo('vl_ajustado'),4);
            }
            $arRecord4[$inCount6]['coluna3']  = number_format($rsRecordSet->getCampo('vl_contabil'), 2, ',', '.' );
            $arRecord4[$inCount6]['coluna4']  = number_format($rsRecordSet->getCampo('vl_ajuste'), 2, ',', '.' );
            $arRecord4[$inCount6]['coluna5']  = number_format($rsRecordSet->getCampo('vl_ajustado'), 2, ',', '.' );
            $inCount6 = $inCount2 - 1;
            $inCount6++;
        } elseif ( $rsRecordSet->getCorrente() == 14 ) {
            $inCount2 = $inCount7;
            if ( $rsRecordSet->getCampo('redutora') == 't' ) {
                foreach ($arNomContaOLD as $stNomContaTemp) {
                    if ($inCount7 == $inCount2) {
                        $stNomContaTemp = "   (-)".$stNomContaTemp;
                    } else {
                        $stNomContaTemp = "   ".$stNomContaTemp;
                    }
                    $arRecord5[$inCount2]['coluna1']    = $stNomContaTemp;;
                    $inCount2++;
                }
                $arRecord5[$inCount7]['coluna2']    = "(".$rsRecordSet->getCampo('cod_estrutural').")";
                $flVlContabil                     = bcsub($flVlContabil,$rsRecordSet->getCampo('vl_contabil'),4);
                $flVlAjuste                       = bcsub($flVlAjuste,$rsRecordSet->getCampo('vl_ajuste'),4);
                $flVlAjustado                     = bcsub($flVlAjustado,$rsRecordSet->getCampo('vl_ajustado'),4);
            } else {
                foreach ($arNomContaOLD as $stNomContaTemp) {
                    $arRecord5[$inCount2]['coluna1']    = $stNomContaTemp;
                    $inCount2++;
                }
                $arRecord5[$inCount7]['coluna2']    = $rsRecordSet->getCampo('cod_estrutural');
                $flVlContabil                     = bcadd($flVlContabil,$rsRecordSet->getCampo('vl_contabil'),4);
                $flVlAjuste                       = bcadd($flVlAjuste,$rsRecordSet->getCampo('vl_ajuste'),4);
                $flVlAjustado                     = bcadd($flVlAjustado,$rsRecordSet->getCampo('vl_ajustado'),4);
            }
            $arRecord5[$inCount7]['coluna3']  = number_format($rsRecordSet->getCampo('vl_contabil'), 2, ',', '.' );
            $arRecord5[$inCount7]['coluna4']  = number_format($rsRecordSet->getCampo('vl_ajuste'), 2, ',', '.' );
            $arRecord5[$inCount7]['coluna5']  = number_format($rsRecordSet->getCampo('vl_ajustado'), 2, ',', '.' );
            $inCount7 = $inCount2 - 1;
            $inCount7++;
        } elseif ( $rsRecordSet->getCorrente() < 19 ) {
            $inCount2 = $inCount8;
            if ( $rsRecordSet->getCampo('redutora') == 't' ) {
                foreach ($arNomContaOLD as $stNomContaTemp) {
                    if ($inCount8 == $inCount2) {
                        $stNomContaTemp = "   (-)".$stNomContaTemp;
                    } else {
                        $stNomContaTemp = "   ".$stNomContaTemp;
                    }
                    $arRecord6[$inCount2]['coluna1']    = $stNomContaTemp;;
                    $inCount2++;
                }
                $arRecord6[$inCount8]['coluna2']    = "(".$rsRecordSet->getCampo('cod_estrutural').")";
                $flVlContabil                     = bcsub($flVlContabil,$rsRecordSet->getCampo('vl_contabil'),4);
                $flVlAjuste                       = bcsub($flVlAjuste,$rsRecordSet->getCampo('vl_ajuste'),4);
                $flVlAjustado                     = bcsub($flVlAjustado,$rsRecordSet->getCampo('vl_ajustado'),4);
            } else {
                foreach ($arNomContaOLD as $stNomContaTemp) {
                    $arRecord6[$inCount2]['coluna1']    = $stNomContaTemp;
                    $inCount2++;
                }
                $arRecord6[$inCount8]['coluna2']    = $rsRecordSet->getCampo('cod_estrutural');
                $flVlContabil                     = bcadd($flVlContabil,$rsRecordSet->getCampo('vl_contabil'),4);
                $flVlAjuste                       = bcadd($flVlAjuste,$rsRecordSet->getCampo('vl_ajuste'),4);
                $flVlAjustado                     = bcadd($flVlAjustado,$rsRecordSet->getCampo('vl_ajustado'),4);
            }
            $arRecord6[$inCount8]['coluna3']  = number_format($rsRecordSet->getCampo('vl_contabil'), 2, ',', '.' );
            $arRecord6[$inCount8]['coluna4']  = number_format($rsRecordSet->getCampo('vl_ajuste'), 2, ',', '.' );
            $arRecord6[$inCount8]['coluna5']  = number_format($rsRecordSet->getCampo('vl_ajustado'), 2, ',', '.' );
            $inCount8 = $inCount2 - 1;
            $inCount8++;
        } elseif ( $rsRecordSet->getCorrente() < 21 ) {
            $inCount2 = $inCount9;
            if ( $rsRecordSet->getCampo('redutora') == 't' ) {
                foreach ($arNomContaOLD as $stNomContaTemp) {
                    if ($inCount9 == $inCount2) {
                        $stNomContaTemp = "   (-)".$stNomContaTemp;
                    } else {
                        $stNomContaTemp = "   ".$stNomContaTemp;
                    }
                    $arRecord7[$inCount2]['coluna1']    = $stNomContaTemp;;
                    $inCount2++;
                }
                $arRecord7[$inCount9]['coluna2']    = "(".$rsRecordSet->getCampo('cod_estrutural').")";
                $flVlContabil                     = bcsub($flVlContabil,$rsRecordSet->getCampo('vl_contabil'),4);
                $flVlAjuste                       = bcsub($flVlAjuste,$rsRecordSet->getCampo('vl_ajuste'),4);
                $flVlAjustado                     = bcsub($flVlAjustado,$rsRecordSet->getCampo('vl_ajustado'),4);
            } else {
                foreach ($arNomContaOLD as $stNomContaTemp) {
                    $arRecord7[$inCount2]['coluna1']    = $stNomContaTemp;
                    $inCount2++;
                }
                $arRecord7[$inCount9]['coluna2']    = $rsRecordSet->getCampo('cod_estrutural');
                $flVlContabil                     = bcadd($flVlContabil,$rsRecordSet->getCampo('vl_contabil'),4);
                $flVlAjuste                       = bcadd($flVlAjuste,$rsRecordSet->getCampo('vl_ajuste'),4);
                $flVlAjustado                     = bcadd($flVlAjustado,$rsRecordSet->getCampo('vl_ajustado'),4);
            }
                $arRecord7[$inCount9]['coluna3']  = number_format($rsRecordSet->getCampo('vl_contabil'), 2, ',', '.' );
                $arRecord7[$inCount9]['coluna4']  = number_format($rsRecordSet->getCampo('vl_ajuste'), 2, ',', '.' );
                $arRecord7[$inCount9]['coluna5']  = number_format($rsRecordSet->getCampo('vl_ajustado'), 2, ',', '.' );

            $inCount9 = $inCount2 - 1;
            $inCount9++;
        } elseif ( $rsRecordSet->getCorrente() < 23 ) {
            $inCount2 = $inCount10;
            if ( $rsRecordSet->getCampo('redutora') == 't' ) {
                foreach ($arNomContaOLD as $stNomContaTemp) {
                    if ($inCount10 == $inCount2) {
                        $stNomContaTemp = "   (-)".$stNomContaTemp;
                    } else {
                        $stNomContaTemp = "   ".$stNomContaTemp;
                    }
                    $arRecord8[$inCount2]['coluna1']    = $stNomContaTemp;;
                    $inCount2++;
                }
                $arRecord8[$inCount10]['coluna2']    = "(".$rsRecordSet->getCampo('cod_estrutural').")";
                $flVlContabil                     = bcsub($flVlContabil,$rsRecordSet->getCampo('vl_contabil'),4);
                $flVlAjuste                       = bcsub($flVlAjuste,$rsRecordSet->getCampo('vl_ajuste'),4);
                $flVlAjustado                     = bcsub($flVlAjustado,$rsRecordSet->getCampo('vl_ajustado'),4);
            } else {
                foreach ($arNomContaOLD as $stNomContaTemp) {
                    $arRecord8[$inCount2]['coluna1']    = $stNomContaTemp;
                    $inCount2++;
                }
                $arRecord8[$inCount10]['coluna2']    = $rsRecordSet->getCampo('cod_estrutural');
                $flVlContabil                     = bcadd($flVlContabil,$rsRecordSet->getCampo('vl_contabil'),4);
                $flVlAjuste                       = bcadd($flVlAjuste,$rsRecordSet->getCampo('vl_ajuste'),4);
                $flVlAjustado                     = bcadd($flVlAjustado,$rsRecordSet->getCampo('vl_ajustado'),4);
            }
            if (!$rsRecordSet->getCampo('nom_conta')) {
                $arRecord8[$inCount9]['coluna3']  = "";
                $arRecord8[$inCount9]['coluna4']  = "";
                $arRecord8[$inCount9]['coluna5']  = "";
            } else {
                $arRecord8[$inCount10]['coluna3']  = number_format($rsRecordSet->getCampo('vl_contabil'), 2, ',', '.' );
                $arRecord8[$inCount10]['coluna4']  = number_format($rsRecordSet->getCampo('vl_ajuste'), 2, ',', '.' );
                $arRecord8[$inCount10]['coluna5']  = number_format($rsRecordSet->getCampo('vl_ajustado'), 2, ',', '.' );
            }
            $inCount10 = $inCount2 - 1;
            $inCount10++;
        } else {
            $inCount2 = $inCount11;
            if ( $rsRecordSet->getCampo('redutora') == 't' ) {
                foreach ($arNomContaOLD as $stNomContaTemp) {
                    if ($inCount11 == $inCount2) {
                        $stNomContaTemp = "   (-)".$stNomContaTemp;
                    } else {
                        $stNomContaTemp = "   ".$stNomContaTemp;
                    }
                    $arRecord9[$inCount2]['coluna1']    = $stNomContaTemp;;
                    $inCount2++;
                }
                $arRecord9[$inCount11]['coluna2']    = "(".$rsRecordSet->getCampo('cod_estrutural').")";
                $flVlContabil                     = bcsub($flVlContabil,$rsRecordSet->getCampo('vl_contabil'),4);
                $flVlAjuste                       = bcsub($flVlAjuste,$rsRecordSet->getCampo('vl_ajuste'),4);
                $flVlAjustado                     = bcsub($flVlAjustado,$rsRecordSet->getCampo('vl_ajustado'),4);
            } else {
                foreach ($arNomContaOLD as $stNomContaTemp) {
                    $arRecord9[$inCount2]['coluna1']    = $stNomContaTemp;
                    $inCount2++;
                }
                $arRecord9[$inCount11]['coluna2']    = $rsRecordSet->getCampo('cod_estrutural');
                $flVlContabil                     = bcadd($flVlContabil,$rsRecordSet->getCampo('vl_contabil'),4);
                $flVlAjuste                       = bcadd($flVlAjuste,$rsRecordSet->getCampo('vl_ajuste'),4);
                $flVlAjustado                     = bcadd($flVlAjustado,$rsRecordSet->getCampo('vl_ajustado'),4);
            }
            $arRecord9[$inCount11]['coluna3']  = number_format($rsRecordSet->getCampo('vl_contabil'), 2, ',', '.' );
            $arRecord9[$inCount11]['coluna4']  = number_format($rsRecordSet->getCampo('vl_ajuste'), 2, ',', '.' );
            $arRecord9[$inCount11]['coluna5']  = number_format($rsRecordSet->getCampo('vl_ajustado'), 2, ',', '.' );
            $inCount11 = $inCount2 - 1;
            $inCount11++;
        }
        $arRecordTotal[0]['coluna1']    = 'III - RECEITA CORRENTE LÍQUIDA DA ENTIDADE (III = I-II)';
        $arRecordTotal[0]['coluna2']    =  number_format($flVlContabil1 - $flVlContabil, 2, ',', '.' );
        $arRecordTotal[0]['coluna3']    =  number_format($flVlAjuste1 - $flVlAjuste, 2, ',', '.' );
        $arRecordTotal[0]['coluna4']    =  number_format($flVlAjustado1 - $flVlAjustado, 2, ',', '.' );
        $rsRecordSet->proximo();
    }

    $rsRecordSet      = new RecordSet;
    $rsRecordSet1     = new RecordSet;
    $rsRecordSet2     = new RecordSet;
    $rsRecordSet3     = new RecordSet;
    $rsRecordSet4     = new RecordSet;
    $rsRecordSet5     = new RecordSet;
    $rsRecordSet6     = new RecordSet;
    $rsRecordSet7     = new RecordSet;
    $rsRecordSet8     = new RecordSet;
    $rsRecordSet9     = new RecordSet;
    $rsRecordSetTotal = new RecordSet;

    $rsRecordSet->preenche( $arRecord );
    $rsRecordSet1->preenche( $arRecord1 );
    $rsRecordSet2->preenche( $arRecord2 );
    $rsRecordSet3->preenche( $arRecord3 );
    $rsRecordSet4->preenche( $arRecord4 );
    $rsRecordSet5->preenche( $arRecord5 );
    $rsRecordSet6->preenche( $arRecord6 );
    $rsRecordSet7->preenche( $arRecord7 );
    $rsRecordSet8->preenche( $arRecord8 );
    $rsRecordSet9->preenche( $arRecord9 );
    $rsRecordSetTotal->preenche( $arRecordTotal );

    return $obErro;
}

}
