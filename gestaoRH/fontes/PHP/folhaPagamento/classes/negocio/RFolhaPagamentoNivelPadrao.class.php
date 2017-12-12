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
* Classe de regra de negócio para Pessoal-NivelPadrao
* Data de Criação: 00/00/2005

* @author Analista: Leandro Oliveira
* @author Desenvolvedor: Gustavo Passos Tourinho

* @package URBEM
* @subpackage Regra

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2008-03-27 08:33:32 -0300 (Qui, 27 Mar 2008) $

* Casos de uso: uc-04.05.05
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoNivelPadrao.class.php" );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoNivelPadraoNivel.class.php" );
/**
    * Classe de regra de negócio para Pessoal-NivelPadrao
    * Data de Criação: 02/12/2004

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Gustavo Passos Tourinho

    * @package URBEM
    * @subpackage Regra
*/
class RFolhaPagamentoNivelPadrao
{
/**
    * @access Private
    * @var Integer
*/
var $inCodNivelPadrao;
/**
    * @access Private
    * @var String
*/
var $stDescricaoNivelPadrao;
/**
    * @access Private
    * @var float
*/
var $flValor;
/**
    * @access Private
    * @var float
*/
var $flPercentual;
/**
    * @access Private
    * @var integer
*/
var $inQtdMeses;
/**
    * @access Private
    * @var Object
*/
var $roRFolhaPagamentoPadrao;

/**
    * @access Public
    * @param Integer $valor
*/
function setCodNivelPadrao($valor) { $this->inCodNivelPadrao       = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setDescricaoNivelPadrao($valor) { $this->stDescricaoNivelPadrao = $valor; }
/**
    * @access Public
    * @param float $valor
*/
function setValor($valor) { $this->flValor                = $valor; }
/**
    * @access Public
    * @param float $valor
*/
function setPercentual($valor) { $this->flPercentual           = $valor; }
/**
    * @access Public
    * @param integer $valor
*/
function setQtdMeses($valor) { $this->inQtdMeses             = $valor; }

/**
    * @access Public
    * @return Integer
*/
function getCodNivelPadrao() { return $this->inCodNivelPadrao;       }
/**
    * @access Public
    * @return String
*/
function getDescricaoNivelPadrao() { return $this->stDescricaoNivelPadrao; }
/**
    * @access Public
    * @return float
*/
function getValor() { return $this->flValor;                }
/**
    * @access Public
    * @param float
*/
function getPercentual() { return $this->flPercentual;           }
/**
    * @access Public
    * @param integer
*/
function getQtdMeses() { return $this->inQtdMeses;             }

/**
     * Método construtor
     * @access Private
*/
function RFolhaPagamentoNivelPadrao(&$roRFolhaPagamentoPadrao)
{
    $this->obTFolhaPagamentoNivelPadrao = new TFolhaPagamentoNivelPadrao;
    $this->obTFolhaPagamentoNivelPadraoNivel = new TFolhaPagamentoNivelPadraoNivel;
    $this->roRFolhaPagamentoPadrao      = &$roRFolhaPagamentoPadrao;
    $this->obTransacao           = new Transacao;
}

/**
    * Inclui os dados do nivel-padrao
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function salvarNivelPadrao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if (!$this->inCodNivelPadrao) {
            $obErro = $this->obTFolhaPagamentoNivelPadrao->proximoCod ( $this->inCodNivelPadrao, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obTFolhaPagamentoNivelPadrao->setDado ( "cod_nivel_padrao" , $this->inCodNivelPadrao );
                $obErro = $this->obTFolhaPagamentoNivelPadrao->inclusao( $boTransacao );
            }
        }
        if ( !$obErro->ocorreu() ) {
            $this->obTFolhaPagamentoNivelPadraoNivel->setDado ( "cod_nivel_padrao" , $this->inCodNivelPadrao                         );
            $this->obTFolhaPagamentoNivelPadraoNivel->setDado ( "cod_padrao"       , $this->roRFolhaPagamentoPadrao->getCodPadrao () );
            $this->obTFolhaPagamentoNivelPadraoNivel->setDado ( "descricao"        , $this->stDescricaoNivelPadrao                   );
            $this->obTFolhaPagamentoNivelPadraoNivel->setDado ( "valor"            , $this->flValor                                  );
            $this->obTFolhaPagamentoNivelPadraoNivel->setDado ( "percentual"       , $this->flPercentual                             );
            $this->obTFolhaPagamentoNivelPadraoNivel->setDado ( "qtdmeses"         , $this->inQtdMeses                               );
            //$this->obTFolhaPagamentoNivelPadrao->recuperaNow3($stNow, $boTransacao);
            //$this->obTFolhaPagamentoNivelPadrao->setDado ( "timestamp_padrao" , $stNow                                          );
            $obErro = $this->obTFolhaPagamentoNivelPadraoNivel->inclusao ( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTFolhaPagamentoNivelPadrao );

    return $obErro;
}

/**
    * Lista os Padroes segundo o filtro setado
    * @access Public
    * @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarNivelPadrao(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";

    if ($this->roRFolhaPagamentoPadrao->inCodPadrao != "") {
        $stFiltro .= " AND FPNP.cod_padrao = ".$this->roRFolhaPagamentoPadrao->inCodPadrao." ";
    }
    if ( $this->getQtdMeses() !== null ) {
        $stFiltro .= " and FPNP.qtdmeses <=  ".$this->getQtdMeses()." ";
    }

    if ($this->inCodNivelPadrao) {
        $stFiltro .= " AND FPNP.cod_nivel_padrao = ".$this->inCodNivelPadrao." ";
    }
    if ($this->stDescricaoNivelPadrao) {
        $stFiltro .= " AND UPPER(FPNP.descricao) LIKE UPPER('%".$this->stDescricaoNivelPadrao."%') ";
    }
    $stOrdem = " ORDER BY qtdmeses";
    $obErro = $this->obTFolhaPagamentoNivelPadraoNivel->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}
}
?>
