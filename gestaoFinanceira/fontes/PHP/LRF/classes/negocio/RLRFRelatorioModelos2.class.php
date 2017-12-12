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
* Classe de Regra de Negócios para modelo 2 do modulo LRF
* Data de Criação: 24/05/2005

* @author Analista: Diego Barbosa
* @author Desenvolvedor: Diego Lemos de Souza

* @package URBEM
* @subpackage Regra

$Revision: 30668 $
$Name$
$Author: cleisson $
$Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

* Casos de uso: uc-02.05.04
*/

/*
$Log$
Revision 1.7  2006/07/05 20:44:40  cleisson
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
class RLRFRelatorioModelos2 extends PersistenteRelatorio
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
function RLRFRelatorioModelos2()
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
function geraRecordSet(&$rsRecordSet , &$rsRecordSetTotal, $stOrder = "")
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

    $inCount            = 0;
    $inCount2           = 0;
    $inTotal            = 0;
    $inTotalGeral       = 0;
    $arRecord           = array();
    $arRecordTotal      = array();

    while ( !$rsRecordSet->eof()) {
        $stNomContaTemp = str_replace( chr(10), "", $rsRecordSet->getCampo('nom_conta') );
        $stNomContaTemp = wordwrap( $stNomContaTemp, 95,chr(13) );    // NOTA, o valor 66 eh o q deve ser mudado pra
        $arNomContaOLD = explode( chr(13), $stNomContaTemp );         //maiores ou menores
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

            $flVlContabil                     = bcsub($flVlContabil,$rsRecordSet->getCampo('vl_contabil'),4);
            $flVlAjuste                       = bcsub($flVlAjuste,$rsRecordSet->getCampo('vl_ajuste'),4);
            $flVlAjustado                     = bcsub($flVlAjustado,$rsRecordSet->getCampo('vl_ajustado'),4);
        } else {
            foreach ($arNomContaOLD as $stNomContaTemp) {
                $arRecord[$inCount2]['coluna1']    = $stNomContaTemp;
                $inCount2++;
            }
            $arRecord[$inCount]['coluna2']    = $rsRecordSet->getCampo('cod_estrutural');
            $flVlContabil                     = bcadd($flVlContabil,$rsRecordSet->getCampo('vl_contabil'),4);
            $flVlAjuste                       = bcadd($flVlAjuste,$rsRecordSet->getCampo('vl_ajuste'),4);
            $flVlAjustado                     = bcadd($flVlAjustado,$rsRecordSet->getCampo('vl_ajustado'),4);
        }
        if (!$rsRecordSet->getCampo('nom_conta')) {
            $arRecord[$inCount]['coluna3']    = "";
            $arRecord[$inCount]['coluna4']    = "";
            $arRecord[$inCount]['coluna5']    = "";
        } else {
            $arRecord[$inCount]['coluna3']    = number_format($rsRecordSet->getCampo('vl_contabil'), 2, ',', '.' );
            $arRecord[$inCount]['coluna4']    = number_format($rsRecordSet->getCampo('vl_ajuste'), 2, ',', '.' );
            $arRecord[$inCount]['coluna5']    = number_format($rsRecordSet->getCampo('vl_ajustado'), 2, ',', '.' );
        }

        $arRecordTotal[0]['coluna1']    = 'TOTAL DA DESPESA COM PESSOAL ATIVO/INATIVO DA ENTIDADE';
        $arRecordTotal[0]['coluna2']    =  number_format($flVlContabil, 2, ',', '.' );
        $arRecordTotal[0]['coluna3']    =  number_format($flVlAjuste, 2, ',', '.' );
        $arRecordTotal[0]['coluna4']    =  number_format($flVlAjustado, 2, ',', '.' );
        $inCount = $inCount2 - 1;
        $inCount++;
        $rsRecordSet->proximo();
    }
    $rsRecordSet      = new RecordSet;
    $rsRecordSetTotal = new RecordSet;

    $rsRecordSet->preenche( $arRecord );
    $rsRecordSetTotal->preenche( $arRecordTotal );

    return $obErro;
}

}
