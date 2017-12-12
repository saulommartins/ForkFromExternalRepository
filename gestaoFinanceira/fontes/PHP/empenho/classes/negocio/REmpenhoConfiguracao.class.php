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
    * Classe de Regra de Negócio Configuracao do Empenho
    * Data de Criação   : 05/12/2004

    * @author Analista : Jorge B. Ribarr
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Regra

    $Id: REmpenhoConfiguracao.class.php 65471 2016-05-24 18:58:44Z michel $

    * Casos de uso: uc-02.01.23, uc-02.03.01, uc-02.03.03, uc-02.03.04, uc-02.03.05
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GA_ADM_NEGOCIO."RConfiguracaoConfiguracao.class.php";

class REmpenhoConfiguracao extends RConfiguracaoConfiguracao
{
/**
    * @var Date
    * @access Private
*/
var $stNumeracao;

/**
     * @access Public
     * @param String $valor
*/
var $boAnularAutorizacaoAutomatica;
/**
     * @access Public
     * @param String $valor
*/
var $boDataVencimento;
/**
     * @access Public
     * @param String $valor
*/
var $boLiquidacaoAutomatica;
/**
     * @access Public
     * @param String $valor
*/
var $boOPAutomatica;
/**
     * @access Public
     * @param String $valor
*/
var $boEmitirCarneOp;
/**
    * @access Public
    * @param Object $valor
*/
function setNumeracao($valor) { $this->stNumeracao = $valor;}
/**
     * @access Public
     * @param Boolean $valor
*/
function setAnularAutorizacaoAutomatica($valor) { $this->boAnularAutorizacaoAutomatica     = $valor; }
/**
     * @access Public
     * @param Boolean $valor
*/
function setDataVencimento($valor) { $this->boDataVencimento     = $valor; }
/**
     * @access Public
     * @param Boolean $valor
*/
function setLiquidacaoAutomatica($valor) { $this->boLiquidacaoAutomatica     = $valor; }
/**
     * @access Public
     * @param Boolean $valor
*/
function setOPAutomatica($valor) { $this->boOPAutomatica     = $valor; }
/**
     * @access Public
     * @param Boolean $valor
*/
function setEmitirCarneOP($valor) { $this->boEmitirCarneOp     = $valor; }
/**
     * @access Public
     * @return String
*/
function getNumeracao() { return $this->stNumeracao;    	    }
/**
     * @access Public
     * @param Boolean $valor
*/
function getAnularAutorizacaoAutomatica() { return $this->boAnularAutorizacaoAutomatica;                }
/**
     * @access Public
     * @param Boolean $valor
*/
function getDataVencimento() { return $this->boDataVencimento;                }
/**
     * @access Public
     * @param Boolean $valor
*/
function getLiquidacaoAutomatica() { return $this->boLiquidacaoAutomatica;                }
/**
     * @access Public
     * @param Boolean $valor
*/
function getOPAutomatica() { return $this->boOPAutomatica;                }
/**
     * @access Public
     * @param Boolean $valor
*/
function getEmitirCarneOp() { return $this->boEmitirCarneOp;                }

/**
    * Método Construtor
*/
function REmpenhoConfiguracao()
{
    parent::RConfiguracaoConfiguracao();
    $this->setCodModulo ( 10 );
}

function salvar($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao($boFlagTransacao, $boTransacao);
    if (!$obErro->ocorreu()) {
        $this->setParametro("numero_empenho");
        $this->setValor($this->stNumeracao);
        $this->verificaParametro( $boExiste, $boTransacao );
        if ($boExiste) {
            $obErro = parent::alterar($boTransacao);
        } else {
            $obErro = parent::incluir($boTransacao);
        }

        $this->setParametro("anular_autorizacao_automatica");
        $this->setValor($this->boAnularAutorizacaoAutomatica);
        $this->verificaParametro($boExiste, $boTransacao);
        if ($boExiste) {
            $obErro = parent::alterar($boTransacao);
        } else {
            $obErro = parent::incluir($boTransacao);
        }

        $this->setParametro("vencimento_liquidacao");
        $this->setValor($this->boDataVencimento);
        $this->verificaParametro($boExiste, $boTransacao);
        if ($boExiste) {
            $obErro = parent::alterar($boTransacao);
        } else {
            $obErro = parent::incluir($boTransacao);
        }

        $this->setParametro("liquidacao_automatica");
        $this->setValor($this->boLiquidacaoAutomatica);
        $this->verificaParametro($boExiste, $boTransacao);
        if ($boExiste) {
            $obErro = parent::alterar($boTransacao);
        } else {
            $obErro = parent::incluir($boTransacao);
        }

        $this->setParametro("op_automatica");
        $this->setValor($this->boOPAutomatica);
        $this->verificaParametro($boExiste, $boTransacao);
        if ($boExiste) {
            $obErro = parent::alterar($boTransacao);
        } else {
            $obErro = parent::incluir($boTransacao);
        }

        $this->setParametro("emitir_carne_op");
        $this->setValor( $this->getEmitirCarneOp());
        $this->verificaParametro($boExiste, $boTransacao);
        if ($boExiste) {
            $obErro = parent::alterar($boTransacao);
        } else {
            $obErro = parent::incluir($boTransacao);
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTConfiguracao );

    return $obErro;
}

function consultar($boTransacao = "")
{
    $this->setParametro("numero_empenho");
    $obErro = parent::consultar($boTransacao);
    if (!$obErro->ocorreu()) {
        $this->setNumeracao($this->getValor());

        $this->setParametro("anular_autorizacao_automatica");
        $obErro = parent::consultar($boTransacao);
        if (!$obErro->ocorreu()) {
            $this->setAnularAutorizacaoAutomatica($this->getValor());

            $this->setParametro("vencimento_liquidacao");
            $obErro = parent::consultar($boTransacao);
            if (!$obErro->ocorreu()) {
                $this->setDataVencimento($this->getValor());

                $this->setParametro("liquidacao_automatica");
                $obErro = parent::consultar($boTransacao);
                if (!$obErro->ocorreu()) {
                    $this->setLiquidacaoAutomatica($this->getValor());

                    $this->setParametro("op_automatica");
                    $obErro = parent::consultar($boTransacao);
                    if (!$obErro->ocorreu()) {
                        $this->setOPAutomatica($this->getValor());

                        $this->setParametro("emitir_carne_op");
                        $obErro = parent::consultar($boTransacao);
                        if (!$obErro->ocorreu()) {
                            $this->setEmitirCarneOP($this->getValor());
                        }
                    }
                }
            }
        }
    }

    return $obErro;
}

}
