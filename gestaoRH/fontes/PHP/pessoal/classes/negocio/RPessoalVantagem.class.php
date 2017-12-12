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
* Classe de regra de negócio Pessoal.Vantagem
* Data de Criação: 12/08/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @package URBEM
* @subpackage Regra

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

Caso de uso: uc-04.04.08
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalFaixaCorrecao.class.php"                   );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoVantagem.class.php"       );

class RPessoalVantagem
{
/**
    * @access Private
    * @var Integer
*/
var $inCodAssentamento;
/**
    * @access Private
    * @var String
*/
var $stTimestamp;
/**
    * @access Private
    * @var Date
*/
var $dtInicio;
/**
    * @access Private
    * @var Date
*/
var $dtFim;
/**
    * @access Private
    * @var Array
*/
var $arRPessoalFaixaCorrecao;
/**
    * @access Private
    * @var Object
*/
var $roUltimoPessoalFaixaCorrecao;
/**
    * @access Private
    * @var Object
*/
var $obTransacao;
/**
    * @access Private
    * @var Object
*/
var $obTPessoalAssentamentoVantagem;

/**
    * @access Private
    * @param Integer $valor
*/
function setCodAssentamento($valor) { $this->inCodAssentamento                 = $valor; }
/**
    * @access Private
    * @param String $valor
*/
function setTimestamp($valor) { $this->stTimestamp                       = $valor; }
/**
    * @access Private
    * @param Date $valor
*/
function setDataInicial($valor) { $this->dtInicio                          = $valor; }
/**
    * @access Private
    * @param Date $valor
*/
function setDataFinal($valor) { $this->dtFim                             = $valor; }
/**
    * @access Private
    * @param Array $valor
*/
function setPessoalFaixaCorrecao($valor) { $this->arRPessoalFaixaCorrecao           = $valor; }
/**
    * @access Private
    * @param Object $valor
*/
function setUltimoPessoalFaixaCorrecao($valor) { $this->roUltimoRPessoalFaixaCorrecao     = $valor; }
/**
    * @access Private
    * @param Object $valor
*/
function setTransacao($valor) { $this->obTransacao                       = $valor; }
/**
    * @access Private
    * @param Object $valor
*/
function setTPessoalAssentamentoVantagem($valor) { $this->obTPessoalAssentamentoVantagem    = $valor; }

/**
    * @access Private
    * @return Integer
*/
function getCodAssentamento() { return $this->inCodAssentamento                 ; }
/**
    * @access Private
    * @return String
*/
function getTimestamp() { return $this->stTimestamp                       ; }
/**
    * @access Private
    * @return Date
*/
function getDataInicial() { return $this->dtInicio                          ; }
/**
    * @access Private
    * @return Date
*/
function getDataFinal() { return $this->dtFim                             ; }
/**
    * @access Private
    * @return Array
*/
function getPessoalFaixaCorrecao() { return $this->arRPessoalFaixaCorrecao           ; }
/**
    * @access Private
    * @return Object
*/
function getUltimoPessoalFaixaCorrecao() { return $this->roUltimoRPessoalFaixaCorrecao     ; }
/**
    * @access Private
    * @return Object
*/
function getTransacao() { return $this->obTransacao                       ; }
/**
    * @access Private
    * @return Object
*/
function getTPessoalAssentamentoVantagem() { return $this->obTPessoalAssentamentoVantagem     ; }

/**
    * Método construtor
    * @access Private
*/
function RPessoalVantagem()
{
    $this->setTransacao                         ( new Transacao                         );
    $this->setPessoalFaixaCorrecao              ( array()                               );
    $this->setTPessoalAssentamentoVantagem      ( new TPessoalAssentamentoVantagem      );
}

/**
    * Adiciona um array de referencia-objeto
    * @access Public
*/
function addPessoalFaixaCorrecao()
{
   $this->arRPessoalFaixaCorrecao[]     =  new RPessoalFaixaCorrecao($this);
   $this->roUltimoPessoalFaixaCorrecao  = &$this->arRPessoalFaixaCorrecao[ count($this->arRPessoalFaixaCorrecao) - 1 ];
}

/**
    * Inclui dados de vantagem do assentamento no banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirVantagem($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTPessoalAssentamentoVantagem->setDado("cod_assentamento",      $this->getCodAssentamento() );
        $this->obTPessoalAssentamentoVantagem->setDado("timestamp",             $this->getTimestamp()       );
        $this->obTPessoalAssentamentoVantagem->setDado("dt_inicial",            $this->getDataInicial()     );
        $this->obTPessoalAssentamentoVantagem->setDado("dt_final",              $this->getDataFinal()       );
        $obErro = $this->obTPessoalAssentamentoVantagem->inclusao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            foreach ($this->arRPessoalFaixaCorrecao as $obRPessoalFaixaCorrecao) {
                $obRPessoalFaixaCorrecao->setCodAssentamento( $this->getCodAssentamento()   );
                $obRPessoalFaixaCorrecao->setTimestamp      ( $this->getTimestamp()         );
                $obErro = $obRPessoalFaixaCorrecao->incluirFaixaCorrecao();
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalAssentamentoVantagem );

    return $obErro;
}

/**
    * Exclui dados de vantagem do assentamento do banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirVantagem($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    $this->addPessoalFaixaCorrecao();
    $this->roUltimoPessoalFaixaCorrecao->setCodAssentamento( $this->getCodAssentamento()   );
    $obErro = $this->roUltimoPessoalFaixaCorrecao->excluirFaixaCorrecao($boTransacao);
    if ( !$obErro->ocorreu() ) {
        $this->obTPessoalAssentamentoVantagem->setDado("cod_assentamento",      $this->getCodAssentamento() );
        $obErro = $this->obTPessoalAssentamentoVantagem->exclusao($boTransacao);
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalAssentamentoVantagem );

    return $obErro;
}
}
