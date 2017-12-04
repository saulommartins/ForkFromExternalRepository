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
* Classe de regra de negócio Pessoal.FaixaCorrecao
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
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoFaixaCorrecao.class.php"  );

class RPessoalFaixaCorrecao
{
/**
    * @access Private
    * @var Integer
*/
var $inCodFaixa;
/**
    * @access Private
    * @var Integer
*/
var $inCodAssentamento;
/**
    * @access Private
    * @var Integer
*/
var $inQuantMeses;
/**
    * @access Private
    * @var Numeric
*/
var $nuPercentualCorrecao;
/**
    * @access Private
    * @var String
*/
var $stTimestamp;
/**
    * @access Private
    * @var Object
*/
var $obTransacao;
/**
    * @access Private
    * @var Object
*/
var $obTPessoalAssentamentoFaixaCorrecao;

/**
    * @access Private
    * @param Integer $valor
*/
function setCodFaixa($valor) { $this->inCodFaixa                        = $valor; }
/**
    * @access Private
    * @param Integer $valor
*/
function setCodAssentamento($valor) { $this->inCodAssentamento                 = $valor; }
/**
    * @access Private
    * @param Integer $valor
*/
function setQuantMeses($valor) { $this->inQuantMeses                    = $valor; }
/**
    * @access Private
    * @param Numeric $valor
*/
function setPercentualCorrecao($valor) { $this->inPercentualCorrecao              = $valor; }
/**
    * @access Private
    * @param String $valor
*/
function setTimestamp($valor) { $this->stTimestamp                       = $valor; }
/**
    * @access Private
    * @param Object $valor
*/
function setTransacao($valor) { $this->obTransacao                       = $valor; }
/**
    * @access Private
    * @param Object $valor
*/
function setTPessoalAssentamentoFaixaCorrecao($valor) { $this->obTPessoalAssentamentoFaixaCorrecao  = $valor; }

/**
    * @access Private
    * @return Integer
*/
function getCodFaixa() { return $this->inCodFaixa                        ; }
/**
    * @access Private
    * @return Integer
*/
function getCodAssentamento() { return $this->inCodAssentamento                 ; }
/**
    * @access Private
    * @return Integer
*/
function getQuantMeses() { return $this->inQuantMeses                    ; }
/**
    * @access Private
    * @return Numeric
*/
function getPercentualCorrecao() { return $this->inPercentualCorrecao              ; }
/**
    * @access Private
    * @return String
*/
function getTimestamp() { return $this->stTimestamp                       ; }
/**
    * @access Private
    * @return Object
*/
function getTransacao() { return $this->obTransacao                       ; }
/**
    * @access Private
    * @return Object
*/
function getTPessoalAssentamentoFaixaCorrecao() { return $this->obTPessoalAssentamentoFaixaCorrecao  ; }

/**
    * Método construtor
    * @access Private
*/
function RPessoalFaixaCorrecao()
{
    $this->setTransacao                         ( new Transacao                             );
    $this->setTPessoalAssentamentoFaixaCorrecao ( new TPessoalAssentamentoFaixaCorrecao     );
}

/**
    * Inclui dados de FaixaCorrecao do assentamento no banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirFaixaCorrecao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $tmpComplementoChave =  $this->obTPessoalAssentamentoFaixaCorrecao->getComplementoChave();
        $tmpComplementoCod   =  $this->obTPessoalAssentamentoFaixaCorrecao->getCampoCod();
        $this->obTPessoalAssentamentoFaixaCorrecao->setComplementoChave('');
        $this->obTPessoalAssentamentoFaixaCorrecao->setCampoCod('cod_faixa');

        $this->obTPessoalAssentamentoFaixaCorrecao->proximoCod( $inCodFaixa , $boTransacao );
        $this->setCodFaixa( $inCodFaixa );
        $this->obTPessoalAssentamentoFaixaCorrecao->setComplementoChave($tmpComplementoCampo);
        $this->obTPessoalAssentamentoFaixaCorrecao->setCampoCod($tmpComplementoCod);

        $this->obTPessoalAssentamentoFaixaCorrecao->setDado("cod_faixa",            $this->getCodFaixa()            );
        $this->obTPessoalAssentamentoFaixaCorrecao->setDado("cod_assentamento",     $this->getCodAssentamento()     );
        $this->obTPessoalAssentamentoFaixaCorrecao->setDado("quant_meses",          $this->getQuantMeses()        );
        $this->obTPessoalAssentamentoFaixaCorrecao->setDado("percentual_correcao",  $this->getPercentualCorrecao()  );
        $this->obTPessoalAssentamentoFaixaCorrecao->setDado("timestamp",            $this->getTimestamp()           );

        $obErro = $this->obTPessoalAssentamentoFaixaCorrecao->inclusao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalAssentamentoFaixaCorrecao );

    return $obErro;
}

/**
    * Exclui dados de FaixaCorrecao do assentamento do banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirFaixaCorrecao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTPessoalAssentamentoFaixaCorrecao->setDado("cod_faixa",             $this->getCodFaixa()            );
        $this->obTPessoalAssentamentoFaixaCorrecao->setDado("cod_assentamento",      $this->getCodAssentamento() );
        $this->obTPessoalAssentamentoFaixaCorrecao->setDado("timestamp",             $this->getTimestamp()       );
        $obErro = $this->obTPessoalAssentamentoFaixaCorrecao->exclusao($boTransacao);
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalAssentamentoFaixaCorrecao );

    return $obErro;
}

/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarFaixaCorrecao(&$rsRecordSet, $stOrder="", $boTransacao = "")
{
    if ( $this->getCodAssentamento() ) {
        $stFiltro .= " AND ac.cod_assentamento = ".$this->getCodAssentamento();
    }
    $obErro = $this->obTPessoalAssentamentoFaixaCorrecao->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}
}
