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
* Classe de Regra de Negócios para modelo 10 do modulo LRF
* Data de Criação: 25/05/2005

* @author Analista: Diego Barbosa
* @author Desenvolvedor: Diego Lemos de Souza

* @package URBEM
* @subpackage Regra

$Revision: 30668 $
$Name$
$Author: cleisson $
$Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

* Casos de uso: uc-02.05.11
*/

/*
$Log$
Revision 1.6  2006/07/05 20:44:40  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO );
include_once( CAM_GF_LRF_MAPEAMENTO."FLRFModelosLegislativo.class.php"   );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"          );
include_once( CAM_FW_PDF."RRelatorio.class.php"                  );
include_once (CAM_GA_ADM_NEGOCIO."RConfiguracaoConfiguracao.class.php"   );

/**
    * Classe de Regra de Negócios
    * @author Desenvolvedor: Diego Lemos de Souza
*/
class RLRFRelatorioModelos10 extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $obFLRFModelosLegislativo;
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
function setFLRFModelosLegislativo($valor) { $this->obFLRFModelosLegislativo  = $valor; }
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
function getFLRFModelosLegislativo() { return $this->obFLRFModelosLegislativo;   }
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
function RLRFRelatorioModelos10()
{
    $sessao = $_SESSION ['sessao'];
    $this->setFLRFModelosLegislativo       ( new FLRFModelosLegislativo         );
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
    $obRConfiguracaoConfiguracao            =   new RConfiguracaoConfiguracao() ;
    $obRConfiguracaoConfiguracao->setCodModulo  ( 8                  );
    $obRConfiguracaoConfiguracao->setExercicio  ( $this->getExercicio() );
    $obRConfiguracaoConfiguracao->setParametro  ( "cod_entidade_camara" );
    $obRConfiguracaoConfiguracao->consultar( $boTransacao );

    $this->obFLRFModelosLegislativo->setDado("inCodModelo",$this->getCodModelo());
    $this->obFLRFModelosLegislativo->setDado("stDataInicial",$this->getDataInicial());
    $this->obFLRFModelosLegislativo->setDado("stDataFinal",$this->getDataFinal());
    $this->obFLRFModelosLegislativo->setDado("exercicio",$this->getExercicio());
    $this->obFLRFModelosLegislativo->setDado("stEntidade",$obRConfiguracaoConfiguracao->getValor());
    $this->obFLRFModelosLegislativo->setDado("stFiltro",$this->getFiltro());
    $this->obFLRFModelosLegislativo->setDado("stTipoValorDespesa",$this->getTipoValorDespesa());
    $obErro = $this->obFLRFModelosLegislativo->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder );

    $inCount            = 0;
    $inTotal            = 0;
    $inTotalGeral       = 0;
    $arRecord           = array();
    $arRecordTotal      = array();

    while ( !$rsRecordSet->eof()) {
        $stNomContaTemp = str_replace( chr(10), "", $rsRecordSet->getCampo('nom_conta') );
        $stNomContaTemp = wordwrap( $stNomContaTemp, 95,chr(13) );    // NOTA, o valor 66 eh o q deve ser mudado pra
        $arNomContaOLD = explode( chr(13), $stNomContaTemp );         //maiores ou menores
        $inCountAux = $inCount;
        if ( $rsRecordSet->getCampo('redutora') == 't' ) {
            foreach ($arNomContaOLD as $stNomContaTemp) {
                if ($inCount == $inCountAux) {
                    $stNomContaTemp = "   (-)".$stNomContaTemp;
                } else {
                    $stNomContaTemp = "   ".$stNomContaTemp;
                }
                $arRecord[$inCountAux]['coluna1']    = $stNomContaTemp;;
                $inCountAux++;
            }
            $flVlContabil                     = bcsub($flVlContabil,$rsRecordSet->getCampo('vl_contabil'),4);
            $flVlAjuste                       = bcsub($flVlAjuste,$rsRecordSet->getCampo('vl_ajuste'),4);
            $flVlAjustado                     = bcsub($flVlAjustado,$rsRecordSet->getCampo('vl_ajustado'),4);

            $arRecord[$inCount]['coluna2']    = "(".$rsRecordSet->getCampo('cod_estrutural').")";
        } else {
            foreach ($arNomContaOLD as $stNomContaTemp) {
                $arRecord[$inCountAux]['coluna1']    = $stNomContaTemp;
                $inCountAux++;
            }
            $arRecord[$inCount]['coluna2']    = $rsRecordSet->getCampo('cod_estrutural');
            $flVlContabil                     = bcadd($flVlContabil,$rsRecordSet->getCampo('vl_contabil'),4);
            $flVlAjuste                       = bcadd($flVlAjuste,$rsRecordSet->getCampo('vl_ajuste'),4);
            $flVlAjustado                     = bcadd($rsRecordSet->getCampo('vl_ajustado'),$flVlAjustado,4);
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

        $stNomTotalTemp = str_replace( chr(10), "", 'TOTAL DA DESPESA COM PESSOAL ATIVO/INATIVO DO PODER LEGISLATIVO MUNICIPAL' );
        $stNomTotalTemp = wordwrap( $stNomTotalTemp, 95,chr(13) );
        $arNomTotalOLD = explode( chr(13), $stNomTotalTemp );
        $inCountAux2 = 0;
        foreach ($arNomTotalOLD as $stNomTotalTemp) {
            $arRecordTotal[$inCountAux2]['coluna1']    = $stNomTotalTemp;
            $inCountAux2++;
        }
        $arRecordTotal[0]['coluna2']    =  number_format($flVlContabil, 2, ',', '.' );
        $arRecordTotal[0]['coluna3']    =  number_format($flVlAjuste, 2, ',', '.' );
        $arRecordTotal[0]['coluna4']    =  number_format($flVlAjustado, 2, ',', '.' );

        $inCount = $inCountAux - 1;
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
