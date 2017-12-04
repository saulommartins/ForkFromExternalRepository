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
* Classe de regra de negócio para RFolhaPagamentoSequencia
* Data de Criação: 24/11/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Eduardo Antunez

* @package URBEM
* @subpackage Regra de Negócio

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2007-06-05 17:06:51 -0300 (Ter, 05 Jun 2007) $

* Casos de uso: uc-04.05.27
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class RFolhaPagamentoSequencia
{
/**
   * @access Private
   * @var Integer
*/
var $inCodSequencia;
/**
   * @access Private
   * @var Integer
*/
var $inSequencia;
/**
   * @access Private
   * @var String
*/
var $stDescricao;
/**
   * @access Private
   * @var String
*/
var $stComplemento;

/**
    * @access Public
    * @param Integer $valor
*/
function setCodSequencia($valor) { $this->inCodSequencia                           = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setSequencia($valor) { $this->inSequencia                              = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setDescricao($valor) { $this->stDescricao                              = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setComplemento($valor) { $this->stComplemento                            = $valor; }

/**
    * @access Public
    * @return Integer
*/
function getCodSequencia() { return $this->inCodSequencia;                                     }
/**
    * @access Public
    * @return Integer
*/
function getSequencia() { return $this->inSequencia;                                        }
/**
    * @access Public
    * @return String
*/
function getDescricao() { return $this->stDescricao;                                        }
/**
    * @access Public
    * @return String
*/
function getComplemento() { return $this->stComplemento;                                      }

/**
     * Método construtor
     * @access Private
*/
function RFolhaPagamentoSequencia()
{
}

function incluirSequencia($boTransacao)
{
    include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoSequenciaCalculo.class.php"  );
    $boFlagTransacao = false;
    $obTransacao = new Transacao;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->verificaSequencia( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obTFolhaPagamentoSequenciaCalculo = new TFolhaPagamentoSequenciaCalculo;
            $obErro = $obTFolhaPagamentoSequenciaCalculo->proximoCod( $this->inCodSequencia, $boTransacao );
        }
        if ( !$obErro->ocorreu() ) {
            $obTFolhaPagamentoSequenciaCalculo->setDado("cod_sequencia", $this->getCodSequencia() );
            $obTFolhaPagamentoSequenciaCalculo->setDado("sequencia"    , $this->getSequencia()    );
            $obTFolhaPagamentoSequenciaCalculo->setDado("descricao"    , $this->getDescricao()    );
            $obTFolhaPagamentoSequenciaCalculo->setDado("complemento"  , $this->getComplemento()  );
            $obErro = $obTFolhaPagamentoSequenciaCalculo->inclusao( $boTransacao );
        }
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTFolhaPagamentoSequenciaCalculo );

    return $obErro;
}

function alterarSequencia($boTransacao)
{
    include_once( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoSequenciaCalculo.class.php"  );
    $boFlagTransacao = false;
    $obTransacao = new Transacao;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTFolhaPagamentoSequenciaCalculo = new TFolhaPagamentoSequenciaCalculo;
        $obTFolhaPagamentoSequenciaCalculo->setDado("cod_sequencia", $this->getCodSequencia() );
        $obTFolhaPagamentoSequenciaCalculo->setDado("sequencia"    , $this->getSequencia()    );
        $obTFolhaPagamentoSequenciaCalculo->setDado("descricao"    , $this->getDescricao()    );
        $obTFolhaPagamentoSequenciaCalculo->setDado("complemento"  , $this->getComplemento()  );
        $obErro = $obTFolhaPagamentoSequenciaCalculo->alteracao( $boTransacao );
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTFolhaPagamentoSequenciaCalculo );

    return $obErro;
}

function excluirSequencia($boTransacao)
{
    include_once( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoSequenciaCalculo.class.php"  );
    $boFlagTransacao = false;
    $obTransacao = new Transacao;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTFolhaPagamentoSequenciaCalculo = new TFolhaPagamentoSequenciaCalculo;
        $obTFolhaPagamentoSequenciaCalculo->setDado("cod_sequencia", $this->getCodSequencia() );
        $obTFolhaPagamentoSequenciaCalculo->validaExclusao();
        if ( !$obErro->ocorreu() ) {
            $obErro = $obTFolhaPagamentoSequenciaCalculo->exclusao( $boTransacao );
        }
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTFolhaPagamentoSequenciaCalculo );

    return $obErro;
}

/**
    * Lista sequencias
    * @access Private
*/
function listar(&$rsRecordSet, $stFiltro="", $stOrder="", $boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoSequenciaCalculo.class.php");
    $obTFolhaPagamentoSequenciaCalculo = new TFolhaPagamentoSequenciaCalculo;
    $obErro = $obTFolhaPagamentoSequenciaCalculo->recuperaTodos($rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Lista sequencias filtrando código, descrição, complemento
    * @access Public
*/
function listarSequencia(&$rsLista, $boTransacao="")
{
    $stFiltro = "";
    $stOrder  = " ORDER BY sequencia ";
    if ( $inCodSequencia = $this->getCodSequencia() )
        $stFiltro .= " AND cod_sequencia = ".$inCodSequencia." ";
    if ( $inSequencia = $this->getSequencia() )
        $stFiltro .= " AND sequencia = ".$inSequencia." ";
    if( $stDescricao = $this->getDescricao() )
        $stFiltro .= " AND LOWER(descricao) LIKE '%".strtolower($stDescricao)."%' ";
    if( $stComplemento = $this->getComplemento() )
        $stFiltro .= " AND LOWER(complemento) LIKE '%".strtolower($stComplemento)."%' ";
    if ($stFiltro)
        $stFiltro = " WHERE ".substr($stFiltro,4,strlen($stFiltro)-4);
    $obErro = $this->listar( $rsLista, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

function listarSequenciaSemPadroes(&$rsLista, $boTransacao="")
{
    $stFiltro  = " WHERE sequencia <> 1   \n";
    $stFiltro .= "   AND sequencia <> 100 \n";
    $stFiltro .= "   AND sequencia <> 200 \n";
    $stFiltro .= "   AND sequencia <> 300 \n";
    $stFiltro .= "   AND sequencia <> 400 \n";
    if ( $inCodSequencia = $this->getCodSequencia() )
        $stFiltro .= " AND cod_sequencia = ".$inCodSequencia." ";
    if ( $inSequencia = $this->getSequencia() )
        $stFiltro .= " AND sequencia = ".$inSequencia." ";
    if( $stDescricao = $this->getDescricao() )
        $stFiltro .= " AND LOWER(descricao) LIKE '%".strtolower($stDescricao)."%' ";
    if( $stComplemento = $this->getComplemento() )
        $stFiltro .= " AND LOWER(complemento) LIKE '%".strtolower($stComplemento)."%' ";
    $stOrder  = " ORDER BY sequencia ";
    $obErro = $this->listar( $rsLista, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Verifica se existe uma sequencia para aquele número de sequência.
    * @access Public
*/
function verificaSequencia($boTransacao="", $stAcao="incluir")
{
    $rsLista  = new RecordSet;
    $stOrder  = " ORDER BY sequencia ";
    $stFiltro = " WHERE sequencia = ". $this->getSequencia() ." ";
    $obErro = $this->listar( $rsLista, $stFiltro, $stOrder, $boTransacao );
    if ( $rsLista->getNumLinhas() > 0 ) {
        $obErro->setDescricao("Já existe uma Sequência com o número ". $this->getSequencia() .".");
    }

    return $obErro;
}

}//end class
?>
