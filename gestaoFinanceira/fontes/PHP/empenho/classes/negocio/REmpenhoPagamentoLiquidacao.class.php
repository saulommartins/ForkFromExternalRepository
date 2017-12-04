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
    * Classe de Regra de Pagamento de Liquidação
    * Data de Criação   : 02/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Regra

    $Id: REmpenhoPagamentoLiquidacao.class.php 65673 2016-06-08 15:04:01Z franver $

    $Revision: 30805 $
    $Name:  $
    $Author: eduardoschitz $
    $Date: 2008-01-23 15:59:37 -0200 (Qua, 23 Jan 2008) $

    * Caso de uso uc-02.03.04,uc-02.03.23,uc-02.04.05, uc-02.03.03, uc-02.04.20, uc-02.03.28, uc-02.03.31
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"                                      );
include_once ( CAM_GF_EMP_NEGOCIO.        "REmpenhoNotaLiquidacao.class.php"                         );
include_once ( CAM_GF_EMP_NEGOCIO.        "REmpenhoOrdemPagamento.class.php"                         );
include_once ( CAM_GF_CONT_NEGOCIO.        "RContabilidadePlanoContaAnalitica.class.php"              );
include_once ( CAM_GF_CONT_NEGOCIO.        "RContabilidadeLancamento.class.php"              );
include_once ( CAM_GF_EMP_MAPEAMENTO.      "TEmpenhoPagamentoLiquidacao.class.php"                         );
include_once ( CAM_GF_CONT_MAPEAMENTO.      "TContabilidadeLancamento.class.php"                         );
include_once ( CAM_GF_CONT_MAPEAMENTO.      "TContabilidadePlanoConta.class.php"                         );

set_time_limit(0);

/**
    * Classe de Regra de Pagamento de Liquidação

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Regra
*/
class REmpenhoPagamentoLiquidacao
{
/**
    * @access Private
    * @var Object
*/
var $obREmpenhoNotaLiquidacao;
/**
    * @access Private
    * @var Object
*/
var $obREmpenhoOrdemPagamento;
/**
    * @access Private
    * @var Object
*/
var $obRContabilidadeLancamento;
/**
    * @access Private
    * @var Object
*/
var $obRContabilidadePlanoContaAnalitica;
/**
    * @access Private
    * @var Object
*/
var $obTransacao;
/**
    * @access Private
    * @var Integer
*/
var $inCodOrdem;
/**
    * @access Private
    * @var String
*/
var $stExercicio;
/**
    * @access Private
    * @var Numeric
*/
var $nuValor;
/**
    * @access Private
    * @var String
*/
var $stDataInicial;
/**
    * @access Private
    * @var String
*/
var $stDataFinal;
/**
    * @access Private
    * @var Integer
*/
var $stDataBoletim;
/**
    * @access Private
    * @var String
*/
var $stDataPagamento;
/**
    * @access Private
    * @var String
*/
var $stDataAnulacao;

/**
    * @access Private
    * @var String
*/
var $stNomLogErros ;
/**
    * @access Private
    * @var String
*/
var $logErros;
/**
    * @access Private
    * @var Boolean
*/
var $boLogErros;
/**
    * @access Private
    * @var Integer
*/
var $inContaTesouraria;

/**
    * @access Private
    * @var string
*/
var $stTimestamp;
/**
    * @access Private
    * @var string
*/
var $stObservacao;
/**
    * @access Private
    * @var Boolean
*/
var $boEstorno;
/**
    * @access Private
    * @var Boolean
*/
var $boNaoListarNota;

/**
    * @access Private
    * @var string
*/
var $stTimestampAnulada;
/**
    * @access Private
    * @var array
*/
var $arValoresPagos;

var $boTesouraria;
var $arPagamentosRetencao;
/**
    * @access Public
    * @param Object $valor
*/
function setREmpenhoNotaLiquidacao($valor) { $this->obREmpenhoNotaLiquidacao    = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setREmpenhoOrdemPagamento($valor) { $this->obREmpenhoOrdemPagamento    = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRContabilidadeLancamento($valor) { $this->obRContabilidadeLancamento  = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodOrdem($valor) { $this->inCodOrdem                  = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setExercicio($valor) { $this->stExercicio                 = $valor; }
/**
    * @access Public
    * @param Numeric $valor
*/
function setValor($valor) { $this->nuValor                     = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setDataInicial($valor) { $this->stDataInicial        = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setDataFinal($valor) { $this->stDataFinal          = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setDataBoletim($valor) { $this->stDataBoletim          = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setDataPagamento($valor) { $this->stDataPagamento        = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setDataAnulacao($valor) { $this->stDataAnulacao        = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setContaTesouraria($valor) { $this->inContaTesouraria      = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setNomLogErros($valor) { $this->stNomLogErros         = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setTimestamp($valor) { $this->stTimestamp        = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setObservacao($valor) { $this->stObservacao        = $valor; }

/**
    * @access Public
    * @param Boolean $valor
*/
function setEstorno($valor) { $this->boEstorno        = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setNaoListarNota($valor) { $this->boNaoListarNota  = $valor; }
/**
    * @access Public
    * @param array $valor
*/
function setValoresPagos($valor) { $this->arValoresPagos  = $valor; }

function setTesouraria($valor) { $this->boTesouraria    = $valor; }
function setLotes($valor) { $this->arLotes         = $valor; }
/**
    * @access Public
    * @return Object
*/
function getREmpenhoNotaLiquidacao() { return $this->obREmpenhoNotaLiquidacao;           }
/**
    * @access Public
    * @return Object
*/
function getREmpenhoOrdemPagamento() { return $this->obREmpenhoOrdemPagamento;           }
/**
    * @access Public
    * @return Object
*/
function getRContabilidadeLancamento() { return $this->obRContabilidadeLancamento;         }
/**
    * @access Public
    * @return Integer
*/
function getCodOrdem() { return $this->inCodOrdem;                         }
/**
    * @access Public
    * @return String
*/
function getExercicio() { return $this->stExercicio;                        }
/**
    * @access Public
    * @return Numeric
*/
function getValor() { return $this->nuValor;                            }
/**
    * @access Public
    * @return String
*/
function getDataInicial() { return $this->stDataInicial;                      }
/**
    * @access Public
    * @return String
*/
function getDataFinal() { return $this->stDataFinal;                        }
/**
    * @access Public
    * @return String
*/
function getDataBoletim() { return $this->stDataBoletim;                      }
/**
    * @access Public
    * @return String
*/
function getDataAnulacao() { return $this->stDataAnulacao;                    }
/**
    * @access Public
    * @return Integer
*/
function getContaTesouraria() { return $this->inContaTesouraria;                  }
/**
    * @access Public
    * @return String
*/
function getNomLogErros() { return $this->stNomLogErros;                      }
/**
    * @access Public
    * @return String
*/

function getTimestamp() { return $this->stTimestamp;                      }
/**
    * @access Public
    * @return String
*/
function getObservacao() { return $this->stObservacao;                      }
/**
    * @access Public
    * @return Boolean
*/
function getEstorno() { return $this->boEstorno;                      }
/**
    * @access Public
    * @return Boolean
*/
function getNaoListarNota() { return $this->boNaoListarNota;                }
/**
    * @access Public
    * @return Array
*/
function getValoresPagos() { return $this->arValoresPagos;                }

function getTesouraria() { return $this->boTesouraria;                  }
function getPagamentosRetencao() { return $this->arPagamentosRetencao;          }
/**
     * Método construtor
     * @access Public
*/
function REmpenhoPagamentoLiquidacao()
{
    $this->obREmpenhoNotaLiquidacao                         =  new REmpenhoNotaLiquidacao(new REmpenhoEmpenho);
    $this->obREmpenhoOrdemPagamento                         =  new REmpenhoOrdemPagamento;
    $this->obRContabilidadeLancamento                       =  new RContabilidadeLancamento;
    $this->obRContabilidadePlanoContaAnalitica              =  new RContabilidadePlanoContaAnalitica;
    $this->obTransacao                                      =  new Transacao;
}

/**
    * Executa um recuperaPorChave na classe Persistente
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultar($boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO.   "TEmpenhoPagamentoLiquidacao.class.php"                    );
    $obTEmpenhoPagamentoLiquidacao                    =  new TEmpenhoPagamentoLiquidacao;

    $obTEmpenhoPagamentoLiquidacao->setDado( "cod_ordem"   , $this->inCodOrdem );
    $obTEmpenhoPagamentoLiquidacao->setDado( "exercicio"   , $this->stExercicio );
    $obTEmpenhoPagamentoLiquidacao->setDado( "cod_nota"    , $this->obREmpenhoNotaLiquidacao->getCodNota()  );
    $obTEmpenhoPagamentoLiquidacao->setDado( "cod_empenho" , $this->obREmpenhoNotaLiquidacao->obREmpenhoEmpenho->getCodEmpenho()  );
    $obTEmpenhoPagamentoLiquidacao->setDado( "cod_entidade", $this->obREmpenhoNotaLiquidacao->obREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade()  );
    $obErro = $obTEmpenhoPagamentoLiquidacao->recuperaPorChave( $rsRecordSet, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->nuValor = $rsRecordSet->getCampo("vl_pagamento");
        $this->obREmpenhoNotaLiquidacao->setExercicio( $this->stExercicio );
        $obErro = $this->obREmpenhoNotaLiquidacao->consultar($boTransacao);
        if ( !$obErro->ocorreu() ) {
            $this->obREmpenhoOrdemPagamento->setExercicio( $this->stExercicio );
            $obErro = $this->obREmpenhoOrdemPagamento->consultar($boTransacao);
        }
    }

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
function listar(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO.   "TEmpenhoPagamentoLiquidacao.class.php"                    );
    $obTEmpenhoPagamentoLiquidacao                    =  new TEmpenhoPagamentoLiquidacao;
    if( $this->obREmpenhoNotaLiquidacao->getCodNota() )
        $stFiltro  = " cod_nota = " . $this->obREmpenhoNotaLiquidacao->getCodNota() . "  AND ";
    if( $this->inCodOrdem )
        $stFiltro .= " cod_ordem = '" . $this->inCodOrdem . "' AND ";
    if( $this->stExercicio )
        $stFiltro .= " exercicio = '" . $this->stExercicio . "' AND ";
    if($this->obREmpenhoNotaLiquidacao->obREmpenhoEmpenho->getCodEmpenho())
        $stFiltro  = " cod_empenho = " . $this->obREmpenhoNotaLiquidacao->obREmpenhoEmpenho->getCodEmpenho() . "  AND ";
    if($this->obREmpenhoNotaLiquidacao->obREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade())
        $stFiltro  = " cod_entidade = " . $this->obREmpenhoNotaLiquidacao->obREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() . "  AND ";
    $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
    $stOrder  = ($stOrder)  ? $stOrder : " cod_ordem";
    $obErro = $obTEmpenhoPagamentoLiquidacao->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

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
function listarBoletimSiam(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
{
    include_once ( CAM_GF_TES_MAPEAMENTO.   "VTesourariaSamlinkSiamNumbol.class.php"                             );
    $obVSamlinkSiamNumbol   =  new VSamlinkSiamNumbol;

    $stFiltro = '';

    if ( $this->getDataInicial() and $this->getDataFinal() ) {
        $stFiltro .= " data between to_date('".$this->getDataInicial()."','dd/mm/yyyy') AND to_date('".$this->getDataFinal()."','dd/mm/yyyy') AND ";
    } elseif ( $this->getDataInicial() ) {
        $stFiltro .= " data >= to_date(".$this->getDataInicial().",'dd/mm/yyyy') AND ";
    } elseif ( $this->getDataFinal() ) {
        $stFiltro .= " data <= to_date(".$this->getDataFinal().",'dd/mm/yyyy') AND ";
    }
    $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
    $stOrder  = ($stOrder)  ? $stOrder : " data, numero";
    $obErro = $obVSamlinkSiamNumbol->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
* Lista as Ordens de Pagamento Utilizadas no Pagamento
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function listarLiquidacaoNaoPaga(&$rsRecordSet, $boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoPagamentoLiquidacao.class.php" );
    $obTEmpenhoPagamentoLiquidacao =  new TEmpenhoPagamentoLiquidacao;

    if ( $this->obREmpenhoOrdemPagamento->getCodigoOrdemInicial() ) {
        $stFiltroOrdem .= " AND  EOP.cod_ordem >= ".$this->obREmpenhoOrdemPagamento->getCodigoOrdemInicial()." ";
        $this->boNaoListarNota = true;
    }
    if ( $this->obREmpenhoOrdemPagamento->getCodigoOrdemFinal() ) {
        $stFiltroOrdem .= " AND EOP.cod_ordem <= ".$this->obREmpenhoOrdemPagamento->getCodigoOrdemFinal()." ";
        $this->boNaoListarNota = true;
    }

    $stFiltro = "";
    if( $this->boNaoListarNota )
        $stFiltroOrdem .= " AND EOP.cod_ordem IS NOT NULL ";
    if( $this->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->getCodNotaInicial() )
        $stFiltro .= " AND ENL.cod_nota >= ".$this->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->getCodNotaInicial()." ";
    if( $this->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->getCodNotaFinal() )
        $stFiltro .= " AND ENL.cod_nota <= ".$this->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->getCodNotaFinal()." ";
    if( $this->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->roREmpenhoEmpenho->getCodEmpenhoInicial() )
        $stFiltro .= " AND EE.cod_empenho >= ".$this->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->roREmpenhoEmpenho->getCodEmpenhoInicial()." ";
    if( $this->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->roREmpenhoEmpenho->getCodEmpenhoFinal() )
        $stFiltro .= " AND EE.cod_empenho <= ".$this->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->roREmpenhoEmpenho->getCodEmpenhoFinal()." ";
    if ( $this->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->roREmpenhoEmpenho->getExercicio() ) {
        $stFiltro .= " AND EE.exercicio  = \'".$this->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->roREmpenhoEmpenho->getExercicio()."\' ";
        $stFiltroOrdem .= " AND EPL.exercicio_empenho = \'".$this->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->roREmpenhoEmpenho->getExercicio()."\' ";
    }
    if ( $this->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() ) {
        $stFiltro .= " AND EE.cod_entidade in (".$this->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade().") ";
        $stFiltroOrdem .= " AND EPL.cod_entidade IN(".$this->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade().") ";
    }
    if( $this->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->roREmpenhoEmpenho->obRCGM->getNumCgm() )
        $stFiltro .= " AND EPE.cgm_beneficiario = ".$this->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->roREmpenhoEmpenho->obRCGM->getNumCgm()." ";

    $obTEmpenhoPagamentoLiquidacao->setDado( 'stFiltroOrdem', $stFiltroOrdem );
    $obErro = $obTEmpenhoPagamentoLiquidacao->recuperaLiquidacaoNaoPaga( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}
function listarLiquidacaoNaoPagaTesouraria(&$rsRecordSet, $boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoPagamentoLiquidacao.class.php" );
    $obTEmpenhoPagamentoLiquidacao =  new TEmpenhoPagamentoLiquidacao;

    if ( $this->obREmpenhoOrdemPagamento->getCodigoOrdemInicial() ) {
        $stFiltroOrdem .= " AND  EOP.cod_ordem >= ".$this->obREmpenhoOrdemPagamento->getCodigoOrdemInicial()." ";
        $this->boNaoListarNota = true;
    }
    if ( $this->obREmpenhoOrdemPagamento->getCodigoOrdemFinal() ) {
        $stFiltroOrdem .= " AND EOP.cod_ordem <= ".$this->obREmpenhoOrdemPagamento->getCodigoOrdemFinal()." ";
        $this->boNaoListarNota = true;
    }

    # Conforme #22858 não pode listar OP que sejam do exercício anterior ao logado.
    $stFiltroOrdem .= " AND EOP.exercicio = ''".Sessao::getExercicio()."''";

    $stFiltro = "";
    if( $this->boNaoListarNota )
        $stFiltroOrdem .= " AND EOP.cod_ordem IS NOT NULL ";
    if ( $this->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->getCodNotaInicial() ) {
        $stFiltro .= " AND ENL.cod_nota >= ".$this->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->getCodNotaInicial()." ";
    }
    if( $this->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->getCodNotaFinal() )
        $stFiltro .= " AND ENL.cod_nota <= ".$this->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->getCodNotaFinal()." ";
    if( $this->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->roREmpenhoEmpenho->getCodEmpenhoInicial() )
        $stFiltro .= " AND EE.cod_empenho >= ".$this->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->roREmpenhoEmpenho->getCodEmpenhoInicial()." ";
    if( $this->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->roREmpenhoEmpenho->getCodEmpenhoFinal() )
        $stFiltro .= " AND EE.cod_empenho <= ".$this->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->roREmpenhoEmpenho->getCodEmpenhoFinal()." ";

    $stFiltroAuxiliar = $stFiltro;

    if ( $this->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->roREmpenhoEmpenho->getExercicio() ) {
        $stFiltro .= " AND EE.exercicio  = ''".$this->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->roREmpenhoEmpenho->getExercicio()."'' ";
        $stFiltroOrdem .= " AND EPL.exercicio_empenho = ''".$this->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->roREmpenhoEmpenho->getExercicio()."'' ";
    }
    if ( $this->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() ) {
        $stFiltro .= " AND EE.cod_entidade in (".$this->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade().") ";
        $stFiltroOrdem .= " AND EPL.cod_entidade IN(".$this->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade().") ";
    }
    if ( $this->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->roREmpenhoEmpenho->obRCGM->getNumCgm() ) {
        $stFiltro .= " AND EPE.cgm_beneficiario = ".$this->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->roREmpenhoEmpenho->obRCGM->getNumCgm()." ";
        $stFiltroAuxiliar .= " AND EPE.cgm_beneficiario = ".$this->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->roREmpenhoEmpenho->obRCGM->getNumCgm()." ";
    }

    $obTEmpenhoPagamentoLiquidacao->setDado( 'stFiltroOrdem', $stFiltroOrdem );
    $obTEmpenhoPagamentoLiquidacao->setDado( 'stFiltroAuxiliar', $stFiltroAuxiliar );
    $obErro = $obTEmpenhoPagamentoLiquidacao->recuperaLiquidacaoNaoPagaTesouraria( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Inclui dados de Nota da Liquidação
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluir($boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO.   "TEmpenhoPagamentoLiquidacao.class.php"                    );
    $obTEmpenhoPagamentoLiquidacao                    =  new TEmpenhoPagamentoLiquidacao;

    $boFlagTransacao = false;
    $boFlagNovaClassificacao = true;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao);
    if ( !$obErro->ocorreu() ) {
        $obTEmpenhoPagamentoLiquidacao->setDado("cod_empenho"  ,$this->obREmpenhoNotaLiquidacao->obREmpenhoEmpenho->getCodEmpenho() );
        $obTEmpenhoPagamentoLiquidacao->setDado("cod_entidade" ,$this->obREmpenhoNotaLiquidacao->obREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() );
        $obTEmpenhoPagamentoLiquidacao->setDado("cod_nota"     ,$this->obREmpenhoNotaLiquidacao->getCodNota() );
        $obTEmpenhoPagamentoLiquidacao->setDado("exercicio"    ,$this->stExercicio );
        $obTEmpenhoPagamentoLiquidacao->setDado("vl_pagamento" ,$this->nuValor );
        $obErro = $obTEmpenhoPagamentoLiquidacao->proximoCod( $this->inCodOrdem, $boTransacao );
        $obTEmpenhoPagamentoLiquidacao->setDado("cod_ordem"    ,$this->inCodOrdem );
        if ( !$obErro->ocorreu() ) {
            $obErro = $obTEmpenhoPagamentoLiquidacao->inclusao( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTEmpenhoPagamentoLiquidacao );

    return $obErro;
}
/**
    * Altera dados de Nota da Liquidação
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterar($boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO.   "TEmpenhoPagamentoLiquidacao.class.php"                    );
    $obTEmpenhoPagamentoLiquidacao                    =  new TEmpenhoPagamentoLiquidacao;

    $boFlagTransacao = false;
    $boFlagNovaClassificacao = true;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao);
    if ( !$obErro->ocorreu() ) {
        $obTEmpenhoPagamentoLiquidacao->setDado("cod_empenho"  ,$this->obREmpenhoNotaLiquidacao->obREmpenhoEmpenho->getCodEmpenho() );
        $obTEmpenhoPagamentoLiquidacao->setDado("cod_entidade" ,$this->obREmpenhoNotaLiquidacao->obREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() );
        $obTEmpenhoPagamentoLiquidacao->setDado("cod_nota"     ,$this->obREmpenhoNotaLiquidacao->getCodNota() );
        $obTEmpenhoPagamentoLiquidacao->setDado("exercicio"    ,$this->stExercicio );
        $obTEmpenhoPagamentoLiquidacao->setDado("vl_pagamento" ,$this->nuValor );
        $obTEmpenhoPagamentoLiquidacao->setDado("cod_ordem"    ,$this->inCodOrdem );
        $obErro = $obTEmpenhoPagamentoLiquidacao->alteracao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTEmpenhoPagamentoLiquidacao );

    return $obErro;
}
/**
    * Exclui dados de Nota da Liquidação
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluir($boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO.   "TEmpenhoPagamentoLiquidacao.class.php"                    );
    $obTEmpenhoPagamentoLiquidacao                    =  new TEmpenhoPagamentoLiquidacao;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() and $this->inCodConta ) {
        $obTEmpenhoPagamentoLiquidacao->setDado("cod_empenho"  ,$this->obREmpenhoNotaLiquidacao->obREmpenhoEmpenho->getCodEmpenho() );
        $obTEmpenhoPagamentoLiquidacao->setDado("cod_entidade" ,$this->obREmpenhoNotaLiquidacao->obREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() );
        $obTEmpenhoPagamentoLiquidacao->setDado("cod_nota"     ,$this->obREmpenhoNotaLiquidacao->getCodNota() );
        $obTEmpenhoPagamentoLiquidacao->setDado("exercicio"    ,$this->stExercicio );
        $obTEmpenhoPagamentoLiquidacao->setDado("cod_ordem"    ,$this->inCodOrdem );
        $obErro = $obTEmpenhoPagamentoLiquidacao->exclusao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro , $obTEmpenhoPagamentoLiquidacao );

    return $obErro;
}
//
function logCabec()
{
    $stHoraLog = date( "dmYHis" );
    $this->setNomLogErros("logErros".$stHoraLog.".txt");
    $this->logErros = fopen( Sessao::raiz."../../tmp/".$this->getNomLogErros(), "w");

    fwrite($this->logErros, "+-------------------------------------------------------------------------+\n");
    fwrite($this->logErros, " CNM - Confederação Nacional dos Municípios\n");
    fwrite($this->logErros, " Baixa de pagamentos.\n");
    fwrite($this->logErros, " Log de erros\n");
    fwrite($this->logErros, " Periodo: ".$this->getDataInicial()." a ". $this->getDataFinal()." \n");
    fwrite($this->logErros, "+-------------------------------------------------------------------------+\n\n");
}
//
function logLinha($stLogObs)
{
    if (!$this->logErros) {
        $this->logCabec() ;
        $this->boLogErros = true ;
    }
    fwrite($this->logErros, $stLogObs."\n");
}

//
/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarAutenticacoes(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
{
    include_once ( CAM_GF_TES_MAPEAMENTO.   "VTesourariaSamlinkSiamAutent.class.php"                             );
    $obVSamlinkSiamAutent                             =  new VSamlinkSiamAutent;

    $stFiltro = '';

    if ( $this->getDataInicial() and $this->getDataFinal() ) {
        $stFiltro .= " data >= to_date('".$this->getDataInicial()."','dd/mm/yyyy') AND ";
        $stFiltro .= " data <= to_date('".$this->getDataFinal()."','dd/mm/yyyy') AND ";
        $stFiltro .= " entidade is not null AND ";
        $stFiltro .= " empen is not null AND empen <> '' AND ";
    }
    $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
    $stOrder  = ($stOrder)  ? $stOrder : " data, numero";
    $obErro = $obVSamlinkSiamAutent->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
* recupera dados do banco do pagamento da ordem de pagamento
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function recuperaDadosBancoPagamento(&$rsRecordSet, $boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO.   "TContabilidadePagamento.class.php"                        );
    $obTContabilidadePagamento                        =  new TContabilidadePagamento;

    $stFiltro = "";

    if ( $this->obREmpenhoNotaLiquidacao->getCodNota() ) {
        $stFiltro .= " AND enp.cod_nota = ".$this->obREmpenhoNotaLiquidacao->getCodNota();
    }
     if ( $this->getExercicio() ) {
        $stFiltro .= " AND enp.exercicio = ".$this->getExercicio();
    }
    if ( $this->getTimestamp() ) {
        $stFiltro .= " AND enp.timestamp = '".$this->getTimestamp() . "' ";
    }
    if ( $this->obREmpenhoOrdemPagamento->obROrcamentoEntidade->getCodigoEntidade() ) {
        $stFiltro .= " AND enp.cod_entidade = (".$this->obREmpenhoOrdemPagamento->obROrcamentoEntidade->getCodigoEntidade().")";
    }
    $obErro = $obTContabilidadePagamento->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
* Lista os dados de uma nota de liquidacao paga
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function listarNotaLiquidacaoPaga(&$rsRecordSet, $boTransacao = "")
{
    $stOrder = "";
    include_once ( CAM_GF_EMP_MAPEAMENTO.   "TEmpenhoNotaLiquidacaoPaga.class.php"                     );
    $obTEmpenhoNotaLiquidacaoPaga                     =  new TEmpenhoNotaLiquidacaoPaga;

    $stFiltro = "";
    if ( $this->obREmpenhoOrdemPagamento->getCodigoOrdem() ) {
        $stFiltro .= " AND eop.cod_ordem = ".$this->obREmpenhoOrdemPagamento->getCodigoOrdem();
    }
    if ( $this->obREmpenhoOrdemPagamento->getExercicio() ) {
        $stFiltro .= " AND eop.exercicio = '".$this->obREmpenhoOrdemPagamento->getExercicio()."' ";
    }
    if ( $this->obREmpenhoOrdemPagamento->obROrcamentoEntidade->getCodigoEntidade() ) {
        $stFiltro .= " AND eop.cod_entidade = (".$this->obREmpenhoOrdemPagamento->obROrcamentoEntidade->getCodigoEntidade().")";
    }

    if ($this->boEstorno) {
        $stFiltro .= " and coalesce(enp.vl_pago,0.00) - coalesce(nlpa.vl_pago_anulado,0.00) > 0.00 \n ";
    }

    $obErro = $obTEmpenhoNotaLiquidacaoPaga->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Método para verificar se o recurso da conta é o mesmo que o da ordem que está sendo paga
    * @acces Private
    * @param Object $boTransacao
    * @return Object $boTransacao
*/
function verificarRecursoConta($boTransacao = "")
{
    $obErro = $this->obRContabilidadePlanoContaAnalitica->listar( $rsContaAnalitica, 'cod_plano', $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $inContaCodRecurso         = $rsContaAnalitica->getCampo('cod_recurso');
        $stTipoRecurso             = $rsContaAnalitica->getCampo('tipo');
        $inCodRecursoContraPartida = $rsContaAnalitica->getCampo('cod_recurso_contrapartida');
        // Se recurso for VINCULADO faz validacao
        if ($stTipoRecurso == "V") {
        SistemaLegado::mostraVar($inContaCodRecurso ." = ".$this->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->getCodRecurso());
        SistemaLegado::mostraVar($inCodRecursoContraPartida ." = ".$this->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->getCodRecurso());
            $boExibeMensagem = true;
            if( $inContaCodRecurso == $this->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->getCodRecurso())
                $boExibeMensagem = false;
            else if( $inCodRecursoContraPartida == $this->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->getCodRecurso())
                $boExibeMensagem = false;
            
            if($boExibeMensagem){    
                $obErro->setDescricao( 'A conta informada deve ter o mesmo recurso que a ordem de pagamento.' );
            }
        }
    }

    return $obErro;
}

/**
    * Método para fazer inclusão das notas da ordem de pagamento
    * @access Private
    * @param Object $obRNotaLiquidacao
    * @return Object $obErro
*/
function incluirNotaLiquidacao($obRNotaLiquidacao, $boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO.   "TEmpenhoNotaLiquidacaoPaga.class.php"                     );
    include_once ( CAM_GF_EMP_MAPEAMENTO.   "TEmpenhoNotaLiquidacaoContaPagadora.class.php"                     );
    $obTEmpenhoNotaLiquidacaoPaga                     =  new TEmpenhoNotaLiquidacaoPaga;
    $obTEmpenhoNotaLiquidacaoContaPagadora            =  new TEmpenhoNotaLiquidacaoContaPagadora;

    $obTEmpenhoNotaLiquidacaoPaga->setDado( 'cod_entidade', $obRNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() );
    $obTEmpenhoNotaLiquidacaoPaga->setDado( 'cod_nota'    , $obRNotaLiquidacao->getCodNota()   );
    $obTEmpenhoNotaLiquidacaoPaga->setDado( 'exercicio'   , $obRNotaLiquidacao->getExercicio() );
    $obTEmpenhoNotaLiquidacaoPaga->setDado( 'timestamp'   , $this->stTimestamp                 );
    $obTEmpenhoNotaLiquidacaoPaga->setDado( 'vl_pago'     , $obRNotaLiquidacao->getValorPago() );
    $obTEmpenhoNotaLiquidacaoPaga->setDado( 'observacao'  , $this->stObservacao                );

    $obErro = $obTEmpenhoNotaLiquidacaoPaga->inclusao( $boTransacao );
    if (!$obErro->ocorreu()) {
        $obTEmpenhoNotaLiquidacaoContaPagadora->setDado("cod_nota"     ,$obRNotaLiquidacao->getCodNota() );
        $obTEmpenhoNotaLiquidacaoContaPagadora->setDado("cod_entidade" ,$obRNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade()  );
        $obTEmpenhoNotaLiquidacaoContaPagadora->setDado("exercicio_liquidacao"    ,$obRNotaLiquidacao->getExercicio() );
        $obTEmpenhoNotaLiquidacaoContaPagadora->setDado("timestamp"    ,$this->stTimestamp );
        $obTEmpenhoNotaLiquidacaoContaPagadora->setDado("exercicio"    ,$this->obRContabilidadePlanoContaAnalitica->getExercicio() );
        $obTEmpenhoNotaLiquidacaoContaPagadora->setDado("cod_plano"    ,$this->obRContabilidadePlanoContaAnalitica->getCodPlano()  );

        $obErro = $obTEmpenhoNotaLiquidacaoContaPagadora->inclusao($boTransacao);
    }

    return $obErro;
}

/**
    * Método para fazer o pagamento das notas de liquidacao
    * @access Private
    * @param Object $obRNotaLiquidacao
    * @return Object $obErro
*/
function incluirPagamentoNotaLiquidacao($obRNotaLiquidacao, $boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO.   "TEmpenhoPagamentoLiquidacaoNotaLiquidacaoPaga.class.php"  );
    $obTEmpenhoPagamentoLiquidacaoNotaLiquidacaoPaga  =  new TEmpenhoPagamentoLiquidacaoNotaLiquidacaoPaga;

    $obTEmpenhoPagamentoLiquidacaoNotaLiquidacaoPaga->setDado( 'cod_entidade', $obRNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() );
    $obTEmpenhoPagamentoLiquidacaoNotaLiquidacaoPaga->setDado( 'cod_nota'    , $obRNotaLiquidacao->getCodNota()                           );
    $obTEmpenhoPagamentoLiquidacaoNotaLiquidacaoPaga->setDado( 'exercicio'   , $this->obREmpenhoOrdemPagamento->getExercicio()            );
    $obTEmpenhoPagamentoLiquidacaoNotaLiquidacaoPaga->setDado( 'timestamp'   , $this->stTimestamp                                         );
    $obTEmpenhoPagamentoLiquidacaoNotaLiquidacaoPaga->setDado( 'exercicio_liquidacao'   , $obRNotaLiquidacao->getExercicio()              );
    $obTEmpenhoPagamentoLiquidacaoNotaLiquidacaoPaga->setDado( 'cod_ordem'   , $this->obREmpenhoOrdemPagamento->getCodigoOrdem()          );

    $obErro = $obTEmpenhoPagamentoLiquidacaoNotaLiquidacaoPaga->inclusao( $boTransacao );

    return $obErro;
}

/**
    * Método para fazer inclusão das notas da ordem de pagamento na tabela de auditoria
    * @access Private
    * @param Object $obRNotaLiquidacao
    * @return Object $obErro
*/
function incluirNotaLiquidacaoAuditoria($obRNotaLiquidacao, $boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO.   "TEmpenhoNotaLiquidacaoPagaAuditoria.class.php"            );
    $obTEmpenhoNotaLiquidacaoPagaAuditoria            =  new TEmpenhoNotaLiquidacaoPagaAuditoria;

    $obTEmpenhoNotaLiquidacaoPagaAuditoria->setDado( 'cod_entidade', $obRNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() );
    $obTEmpenhoNotaLiquidacaoPagaAuditoria->setDado( 'cod_nota'    , $obRNotaLiquidacao->getCodNota()   );
    $obTEmpenhoNotaLiquidacaoPagaAuditoria->setDado( 'exercicio'   , $obRNotaLiquidacao->getExercicio() );
    $obTEmpenhoNotaLiquidacaoPagaAuditoria->setDado( 'timestamp'   , $this->stTimestamp );
    $obTEmpenhoNotaLiquidacaoPagaAuditoria->setDado( 'numcgm'      , $obRNotaLiquidacao->roREmpenhoEmpenho->obRCGM->getNumCGM() );
    $obErro = $obTEmpenhoNotaLiquidacaoPagaAuditoria->inclusao( $boTransacao );

    return $obErro;
}

/**
    * Método para efetivar pagamento da OP
    * @access Publico
    * @param Object $boTransacao
    * @return Object $obErro
*/
function pagarOP($boTransacao = "")
{
    include_once CAM_GF_CONT_NEGOCIO.      "RContabilidadePlanoBanco.class.php";
    include_once CAM_GF_CONT_MAPEAMENTO.   "TContabilidadeLancamentoEmpenho.class.php";
    include_once CAM_GF_CONT_MAPEAMENTO.   "TContabilidadePagamento.class.php";
    include_once CAM_GF_EMP_MAPEAMENTO.   "FEmpenhoEmpenhoPagamento.class.php";
    include_once CAM_GF_EMP_MAPEAMENTO.   "FEmpenhoEmpenhoPagamentoRestosAPagar.class.php";
    include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoConfiguracao.class.php";
    include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecursoDestinacao.class.php";
    include_once CAM_GF_TES_MAPEAMENTO . 'TTesourariaChequeEmissaoOrdemPagamento.class.php';
    include_once CAM_GF_CONT_MAPEAMENTO.'TContabilidadeConfiguracaoLancamentoCredito.class.php';

    $obRContabilidadePlanoBanco               = new RContabilidadePlanoBanco;
    $obFEmpenhoEmpenhoPagamentoRestosAPagar   = new FEmpenhoEmpenhoPagamentoRestosAPagar;
    $obFEmpenhoEmpenhoPagamento               = new FEmpenhoEmpenhoPagamento;
    $obTContabilidadePagamento                = new TContabilidadePagamento;
    $obTContabilidadeLancamentoEmpenho        = new TContabilidadeLancamentoEmpenho;
    $obTTesourariaChequeEmissaoOrdemPagamento = new TTesourariaChequeEmissaoOrdemPagamento();

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao);

    if ( !$obErro->ocorreu() ) {
        $this->obREmpenhoOrdemPagamento->consultar( $boTransacao );
        $obErro = $this->obREmpenhoOrdemPagamento->consultarValorAPagar( $boTransacao );

        if ( !$obErro->ocorreu() ) {
        
        if ($this->obREmpenhoOrdemPagamento->getValorAPagar() > 0) {
            //VERIFICAÇÃO DA ENTIDADE DA OP e ENTIDADE DA CONTA BANCO - NÃO PODEM SER DIFERENTES
            $obRContabilidadePlanoBanco->setCodPlano ( $this->obRContabilidadePlanoContaAnalitica->getCodPlano()  );
            $obRContabilidadePlanoBanco->setExercicio( $this->obRContabilidadePlanoContaAnalitica->getExercicio() );
            $obErro = $obRContabilidadePlanoBanco->consultar( $boTransacao );

            if ( !$obErro->ocorreu() ) {
                if($obRContabilidadePlanoBanco->obROrcamentoEntidade->getCodigoEntidade())
                    $obErro = $this->verificarRecursoConta( $boTransacao );

                if ( !$obErro->ocorreu() ) {
                    $arDataPagamento = explode("/",$this->stDataPagamento);
                    $arDataEmissao   = explode("/",$this->obREmpenhoOrdemPagamento->getDataEmissao());
                    $stData = $arDataPagamento[2].$arDataPagamento[1].$arDataPagamento[0];
                    if ( (!$obErro->ocorreu() ) && ( $stData < $arDataEmissao[2].$arDataEmissao[1].$arDataEmissao[0]) ) {
                        $inCodigoOrdem = $this->obREmpenhoOrdemPagamento->getCodigoOrdem();
                        $obErro->setDescricao( 'A data do pagamento deve ser igual ou superior a da emissão da ordem de pagamento. OP: '.$inCodigoOrdem.' ' );
                    }
                    if ( (!$obErro->ocorreu() ) && ($this->obREmpenhoOrdemPagamento->stPagamentoEstornado == 'Sim')) {
                        if (SistemaLegado::comparaDatas($this->obREmpenhoOrdemPagamento->dtDataEstorno,$this->stDataPagamento)) {
                            $obErro->setDescricao("Há um estorno de pagamento para esta OP em ".$this->obREmpenhoOrdemPagamento->dtDataEstorno.".");
                        }
                    }
                    if ( !$obErro->ocorreu() ) {
                        $inCodEntidade = $this->obREmpenhoOrdemPagamento->obROrcamentoEntidade->getCodigoEntidade();
                        if( $inCodEntidade != $obRContabilidadePlanoBanco->obROrcamentoEntidade->getCodigoEntidade() and $obRContabilidadePlanoBanco->obROrcamentoEntidade->getCodigoEntidade()>0 )
                            $obErro->setDescricao( 'A entidade da Conta Banco é diferente da entidade do pagamento!' );
                    }

                    // FINAL VERIFICAÇÃO
                    if ( !$obErro->ocorreu() ) {
                        $data = explode("/",$this->stDataPagamento);
                        $stDataPag = $data[2] . "-" . $data[1] . "-" . $data[0];
                        if( !$this->stTimestamp )
                            $this->stTimestamp = $stDataPag.' '.date( 'H:i:s.ms' );

                        /*
                         * Atualiza os Valores
                         */
                        $boValidaPagamentoRetencao = 'FALSE';
                        for ( $i=0; $i <= count($this->arValoresPagos)-1; $i++ ) {
                            for ( $j=0; $j <= count($this->obREmpenhoOrdemPagamento->arNotaLiquidacao)-1; $j++ ) {
                                if (    ($this->arValoresPagos[$i]['cod_nota']  == $this->obREmpenhoOrdemPagamento->arNotaLiquidacao[$j]->getCodNota()   )
                                     && ($this->arValoresPagos[$i]['exercicio'] == $this->obREmpenhoOrdemPagamento->arNotaLiquidacao[$j]->getExercicio() )
                                   ){
                                        $obTTesourariaChequeEmissaoOrdemPagamento->setDado('exercicio'   ,$this->obREmpenhoOrdemPagamento->getExercicio());
                                        $obTTesourariaChequeEmissaoOrdemPagamento->setDado('cod_entidade',$this->obREmpenhoOrdemPagamento->obROrcamentoEntidade->getCodigoEntidade());
                                        $obTTesourariaChequeEmissaoOrdemPagamento->setDado('cod_ordem'   ,$this->obREmpenhoOrdemPagamento->getCodigoOrdem());
                                        $obTTesourariaChequeEmissaoOrdemPagamento->recuperaPorChaveNaoAnulada($rsCheque, '', '', $boTransacao);
                                        // Para ir o valor líquido no enlp.vl_pago
                                        if ($this->obREmpenhoOrdemPagamento->getRetencao() && $rsCheque->getNumLinhas() <= 0) {
                                            $this->obREmpenhoOrdemPagamento->arNotaLiquidacao[$j]->setValorPago(bcsub($this->arValoresPagos[$i]['vl_pago'],$this->obREmpenhoOrdemPagamento->nuTotalRetencoes,2));
                                            $boValidaPagamentoRetencao = 'TRUE';
                                        } else {
                                          $this->obREmpenhoOrdemPagamento->arNotaLiquidacao[$j]->setValorPago($this->arValoresPagos[$i]['vl_pago']);
                                        }
                                   }
                            }
                        }

                        foreach ($this->obREmpenhoOrdemPagamento->arNotaLiquidacao as $obRNotaLiquidacao) {
                            if (Sessao::getExercicio() > '2008') {
                                $inCodRecurso = $obRNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->getCodRecurso();

                                $boDestinacao = false;
                                $obTOrcamentoConfiguracao = new TOrcamentoConfiguracao;
                                $obTOrcamentoConfiguracao->setDado("exercicio", Sessao::getExercicio() );
                                $obTOrcamentoConfiguracao->setDado("parametro","recurso_destinacao");
                                $obTOrcamentoConfiguracao->consultar($boTransacao);
                                if($obTOrcamentoConfiguracao->getDado("valor") == 'true')
                                    $boDestinacao = true;

                                if ($boDestinacao && $inCodRecurso != '') {
                                    $obTOrcamentoRecursoDestinacao = new TOrcamentoRecursoDestinacao;

                                    $stFiltro  = ' WHERE recurso_destinacao.cod_recurso = '.$inCodRecurso;
                                    $stFiltro .= '   AND recurso_destinacao.exercicio = '.Sessao::getExercicio();
                                    $obErro = $obTOrcamentoRecursoDestinacao->recuperaTodos($rsDestinacao, $stFiltro, '', $boTransacao);
                                    $inCodEspecificacao = $rsDestinacao->getCampo('cod_especificacao');

                                    if ($inCodEspecificacao != '') {
                                        // Verifica qual o cod_recurso que possui conta contabil vinculada
                                        $obTOrcamentoRecursoDestinacao->setDado('exercicio', Sessao::getExercicio());
                                        $obTOrcamentoRecursoDestinacao->setDado('cod_especificacao', $inCodEspecificacao);
                                        if ( Sessao::getExercicio() > '2012' ) {
                                            $obTOrcamentoRecursoDestinacao->setDado('cod_estrutural', "'8.2.1.1.3.%'");
                                        } else {
                                            $obTOrcamentoRecursoDestinacao->setDado('cod_estrutural', "'2.9.3.2.0.00.00.%'");
                                        }
                                        $obTOrcamentoRecursoDestinacao->recuperaRecursoVinculoConta($rsContaRecurso, '', '', $boTransacao);

                                        $inCodRecurso = $rsContaRecurso->getCampo('cod_recurso');
                                    }
                                }

                                $obRContabilidadePlanoBanco->obROrcamentoRecurso->setCodRecurso($inCodRecurso);
                                if (Sessao::getExercicio() > '2012') {
                                    $obTContabilidadePlanoBancoTeste = new TContabilidadePlanoBanco;
                                    $obTContabilidadePlanoBancoTeste->setDado('cod_recurso', $obRContabilidadePlanoBanco->obROrcamentoRecurso->getCodRecurso());
                                    $obTContabilidadePlanoBancoTeste->setDado('exercicio', Sessao::getExercicio());
                                    $obTContabilidadePlanoBancoTeste->setDado('estrutural_teste', '8.2.1.1.4.%');
                                    $obTContabilidadePlanoBancoTeste->testaRecursoPagamentoTCEMS($rsContasRecurso, $boTransacao);
                                    if ($rsContasRecurso->getNumLinhas() > 1) {
                                        $obErro->setDescricao('Erro ao efetuar pagamento, existe mais de uma conta do grupo 8 cadastradas para este pagamento. Favor verificar.');
                                        break;
                                    }

                                    if (!$obErro->ocorreu()) {
                                        $obTContabilidadePlanoBancoTeste->setDado('estrutural_teste', '8.2.1.1.3.%');
                                        $obTContabilidadePlanoBancoTeste->testaRecursoPagamentoTCEMS($rsContasRecurso, $boTransacao);

                                        if ($rsContasRecurso->getNumLinhas() > 1) {
                                            $obErro->setDescricao('Erro ao efetuar pagamento, existe mais de uma conta do grupo 8 cadastradas para este pagamento. Favor verificar.');
                                            break;
                                        }
                                    }

                                    if (!$obErro->ocorreu()) {
                                        $obErro = $obRContabilidadePlanoBanco->getContasRecursoPagamentoTCEMS($rsContasRecurso, $boTransacao);
                                    }
                                } else {
                                    $obErro = $obRContabilidadePlanoBanco->getContasRecurso($rsContasRecurso, $boTransacao);
                                }
                                $inCodPlanoUm = $rsContasRecurso->getCampo('cod_plano_um');
                                $inCodPlanoDois = $rsContasRecurso->getCampo('cod_plano_dois');
                            } else {
                                $inCodPlanoDois = '';
                                $inCodPlanoUm = '';
                            }

                            if ( !$obErro->ocorreu() ) {

                                if ( $obRNotaLiquidacao->getValorPago() > 0 || $boValidaPagamentoRetencao == 'TRUE') {
                                    $obErro = $this->incluirNotaLiquidacao( $obRNotaLiquidacao, $boTransacao );
                                    if ( $obErro->ocorreu() ) {
                                        break;
                                    }

                                    $obErro = $this->incluirPagamentoNotaLiquidacao( $obRNotaLiquidacao, $boTransacao );

                                    if( $obErro->ocorreu() )
                                        break;

                                    $obErro = $this->incluirNotaLiquidacaoAuditoria( $obRNotaLiquidacao, $boTransacao );

                                    if( $obErro->ocorreu() )
                                        break;

                                    if ( $this->obRContabilidadePlanoContaAnalitica->getCodPlano() )
                                        $obErro = $this->obRContabilidadePlanoContaAnalitica->consultar( $boTransacao );

                                if ( $obRNotaLiquidacao->roREmpenhoEmpenho->getExercicio() == substr($this->stTimestamp,0,4) ) {
                                    $obFEmpenhoEmpenhoPagamento->setDado("exercicio"             , substr($this->stTimestamp,0,4) );
                                    $obFEmpenhoEmpenhoPagamento->setDado("exercicio_liquidacao"  ,$obRNotaLiquidacao->getExercicio() );
                                    $obFEmpenhoEmpenhoPagamento->setDado("cod_entidade"          ,$obRNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() );
                                    $obFEmpenhoEmpenhoPagamento->setDado("valor"                 ,$obRNotaLiquidacao->getValorPago() );

                                    if ($this->obRContabilidadeLancamento->stComplemento) {
                                        $obFEmpenhoEmpenhoPagamento->setDado("complemento"           ,$this->obRContabilidadeLancamento->stComplemento ) ;
                                        $stComplemento = $this->obRContabilidadeLancamento->stComplemento;
                                    } else {
                                        if ($this->stObservacao) {
                                            $obFEmpenhoEmpenhoPagamento->setDado("complemento"           ,$obRNotaLiquidacao->roREmpenhoEmpenho->getCodEmpenho().'/'.$obRNotaLiquidacao->roREmpenhoEmpenho->getExercicio() ." - ".$this->stObservacao) ;
                                            $stComplemento = $obRNotaLiquidacao->roREmpenhoEmpenho->getCodEmpenho().'/'.$obRNotaLiquidacao->roREmpenhoEmpenho->getExercicio() ." - ".$this->stObservacao;
                                        } else {
                                            $obFEmpenhoEmpenhoPagamento->setDado("complemento"           ,$obRNotaLiquidacao->roREmpenhoEmpenho->getCodEmpenho().'/'.$obRNotaLiquidacao->roREmpenhoEmpenho->getExercicio() ) ;
                                            $stComplemento = $obRNotaLiquidacao->roREmpenhoEmpenho->getCodEmpenho().'/'.$obRNotaLiquidacao->roREmpenhoEmpenho->getExercicio();
                                        }
                                    }

                                    if ($this->obRContabilidadeLancamento->obRContabilidadeLote->stNomLote) {
                                        $obFEmpenhoEmpenhoPagamento->setDado("nom_lote"              ,$this->obRContabilidadeLancamento->obRContabilidadeLote->stNomLote );
                                    } else {
                                        $obFEmpenhoEmpenhoPagamento->setDado("nom_lote"              ,"Pagamento de Empenho n° ".$obRNotaLiquidacao->roREmpenhoEmpenho->getCodEmpenho().'/'.$obRNotaLiquidacao->roREmpenhoEmpenho->getExercicio() ) ;
                                    }

                                    if ( Sessao::getExercicio() > '2012' ) {
                                        $stFiltroContaCredito = " WHERE liquidacao.cod_nota = ".$obRNotaLiquidacao->getCodNota()."
                                                                    AND liquidacao.exercicio_liquidacao = '".$obRNotaLiquidacao->roREmpenhoEmpenho->getExercicio()."'
                                                                    AND lancamento.tipo = 'L'
                                                                    AND lancamento.sequencia = 2
                                                                    AND lancamento.cod_entidade = ".$obRNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade();
                                        $obTContabilidadeLancamento = new TContabilidadeLancamento;
                                        $obTContabilidadeLancamento->recuperaLancamentoEmpenhoContaCredito( $rsContaCredito, $stFiltroContaCredito, $boTransacao );

                                        if ( stristr($rsContaCredito->getCampo('cod_estrutural_mascara'), '2.1.1.1') ) {
                                            $stCodEstruturalPagamento = $rsContaCredito->getCampo('cod_estrutural');
                                            $stCodPlanoCredito = $rsContaCredito->getCampo('cod_plano');
                                        } else {
                                            $stFiltroContaFixaCredito = " AND REPLACE(pc.cod_estrutural, '.', '') like '213110100%' AND pc.exercicio = '".Sessao::getExercicio()."'";
                                            $obTContabilidadePlanoConta = new TContabilidadePlanoConta;
                                            $obErro = $obTContabilidadePlanoConta->recuperaContaAnalitica( $rsContaFixaCredito, $stFiltroContaFixaCredito, '', $boTransacao );
                                            $stCodEstruturalPagamento = '213110100';
                                            $stCodPlanoCredito = $rsContaFixaCredito->getCampo('cod_plano');

                                            if ($stCodPlanoCredito == '' && Sessao::getExercicio() >= 2014) {
                                                $obTContabilidadeConfiguracaoLancamentoCredito = new TContabilidadeConfiguracaoLancamentoCredito;
                                                $stFiltroContaCreditoConfiguracao = " where clc.exercicio = '".Sessao::getExercicio()."'
                                                                                       and clc.cod_conta_despesa = ".$obRNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoClassificacaoDespesa->getCodConta()."
                                                                                       and clc.estorno = 'f'";
                                                $obTContabilidadeConfiguracaoLancamentoCredito->recuperaCodigoPlano($rsContaFixaCreditoConfiguracao, $stFiltroContaCreditoConfiguracao, '', $boTransacao);
                                                $stCodPlanoCredito = $rsContaFixaCreditoConfiguracao->getCampo('cod_plano');
                                            }
                                        }

                                        if ($stCodPlanoCredito == '') {
                                            $obErro->setDescricao('Configuração dos lançamentos de despesa não configurados para esta despesa.');
                                            break;
                                        }

                                        $obFEmpenhoEmpenhoPagamento->setDado("tcems"             , 'true' ) ;

                                    } else {
                                        $stCodEstruturalPagamento = $obRNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoClassificacaoDespesa->getMascClassificacao();
                                    }

                                    $obFEmpenhoEmpenhoPagamento->setDado("tipo_lote"             ,"P" ) ;
                                    $obFEmpenhoEmpenhoPagamento->setDado("dt_lote"               ,$this->stDataPagamento ) ;
                                    $obFEmpenhoEmpenhoPagamento->setDado("cod_nota"              ,$obRNotaLiquidacao->getCodNota() );
                                    $obFEmpenhoEmpenhoPagamento->setDado("conta_pagamento_financ",$this->obRContabilidadePlanoContaAnalitica->getCodEstrutural() ) ;
                                    $obFEmpenhoEmpenhoPagamento->setDado("cod_estrutural"        ,$stCodEstruturalPagamento ) ;
                                    $obFEmpenhoEmpenhoPagamento->setDado("num_orgao"             ,$obRNotaLiquidacao->roREmpenhoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNumeroOrgao() ) ;

                                    if ( Sessao::getExercicio() > '2012' ) {
                                        $obFEmpenhoEmpenhoPagamento->setDado("cod_plano_debito"  , $stCodPlanoCredito ) ;
                                        $obFEmpenhoEmpenhoPagamento->setDado("cod_plano_credito" , $this->obRContabilidadePlanoContaAnalitica->getCodPlano() ) ;
                                    }

                                    $obErro = $obFEmpenhoEmpenhoPagamento->executaFuncao( $rsFEmpenhoEmpenhoPagamento, $boTransacao );

                                    $inCodLote = $obFEmpenhoEmpenhoPagamento->getDado("cod_lote");

                                    if (Sessao::getExercicio() > '2008') {
                                        if ( !$obErro->ocorreu() && $inCodPlanoUm != '' && $inCodPlanoDois != '') {
                                            include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeValorLancamento.class.php";
    
                                            $obTContabilidadeValorLancamento = new TContabilidadeValorLancamento;
                                            $obTContabilidadeValorLancamento->setDado("cod_lote", $inCodLote);
                                            $obTContabilidadeValorLancamento->setDado("tipo", 'P');
                                            $obTContabilidadeValorLancamento->setDado("exercicio" , Sessao::getExercicio());//$obRNotaLiquidacao->getExercicio());
                                            $obTContabilidadeValorLancamento->setDado("cod_entidade", $obRNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() );
                                            $obTContabilidadeValorLancamento->setDado("cod_plano_deb", $inCodPlanoDois);
                                            $obTContabilidadeValorLancamento->setDado("cod_plano_cred", $inCodPlanoUm);
                                            $obTContabilidadeValorLancamento->setDado("cod_historico", 903);
                                            $obTContabilidadeValorLancamento->setDado("complemento", $stComplemento);
                                            $obTContabilidadeValorLancamento->setDado("vl_lancamento", $obRNotaLiquidacao->getValorPago());

                                            $obErro = $obTContabilidadeValorLancamento->inclusaoPorPl($rsRecordSet, $boTransacao);
                                            $inSequenciaLancamento = $rsRecordSet->getCampo('sequencia');
                                        } else {
                                            if (!$obErro->getDescricao()) {
                                                $obErro->setDescricao('Contas do compensado não estão cadastradas.');
                                                break;
                                            }
                                        }
                                    }
                                    $inCodHistoricoAdiantamento = 903;
                                } else {
                                    $obFEmpenhoEmpenhoPagamentoRestosAPagar->setDado("exercicio"             ,substr($this->stTimestamp,0,4) );
                                    $obFEmpenhoEmpenhoPagamentoRestosAPagar->setDado("exercicio_liquidacao"  ,$obRNotaLiquidacao->getExercicio() );
                                    $obFEmpenhoEmpenhoPagamentoRestosAPagar->setDado("cod_entidade"          ,$obRNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() );
                                    $obFEmpenhoEmpenhoPagamentoRestosAPagar->setDado("valor"                 ,$obRNotaLiquidacao->getValorPago() );

                                    if ($this->obRContabilidadeLancamento->stComplemento) {
                                        $obFEmpenhoEmpenhoPagamentoRestosAPagar->setDado("complemento"           ,$this->obRContabilidadeLancamento->stComplemento ) ;
                                        $stComplemento = $this->obRContabilidadeLancamento->stComplemento;
                                    } else {
                                        $obFEmpenhoEmpenhoPagamentoRestosAPagar->setDado("complemento"           ,$obRNotaLiquidacao->roREmpenhoEmpenho->getCodEmpenho().'/'.$obRNotaLiquidacao->roREmpenhoEmpenho->getExercicio() ) ;
                                        $stComplemento = $obRNotaLiquidacao->roREmpenhoEmpenho->getCodEmpenho().'/'.$obRNotaLiquidacao->roREmpenhoEmpenho->getExercicio();
                                    }

                                    if ($this->obRContabilidadeLancamento->obRContabilidadeLote->stNomLote) {
                                        $obFEmpenhoEmpenhoPagamentoRestosAPagar->setDado("nom_lote"              ,$this->obRContabilidadeLancamento->obRContabilidadeLote->stNomLote ) ;
                                    } else {
                                        $obFEmpenhoEmpenhoPagamentoRestosAPagar->setDado("nom_lote"              ,"Pagamento de RP n° ".$obRNotaLiquidacao->roREmpenhoEmpenho->getCodEmpenho().'/'.$obRNotaLiquidacao->roREmpenhoEmpenho->getExercicio() ) ;
                                    }

                                    $obFEmpenhoEmpenhoPagamentoRestosAPagar->setDado("tipo_lote"             ,"P" ) ;
                                    $obFEmpenhoEmpenhoPagamentoRestosAPagar->setDado("dt_lote"               ,$this->stDataPagamento ) ;
                                    $obFEmpenhoEmpenhoPagamentoRestosAPagar->setDado("cod_nota"              ,$obRNotaLiquidacao->getCodNota() );
                                    $obFEmpenhoEmpenhoPagamentoRestosAPagar->setDado("conta_pg"              ,$this->obRContabilidadePlanoContaAnalitica->getCodEstrutural() ) ;
                                    $obFEmpenhoEmpenhoPagamentoRestosAPagar->setDado("exerc_rp"              ,$obRNotaLiquidacao->roREmpenhoEmpenho->getExercicio() ) ;

                                    $obFEmpenhoEmpenhoPagamentoRestosAPagar->setDado("exercicio_atual"       ,Sessao::getExercicio() );
                                    $obErro = $obFEmpenhoEmpenhoPagamentoRestosAPagar->recuperaTipoRestosPagar( $rsTipoRestosPagar, '','',$boTransacao);

                                    if ( !$obErro->ocorreu() ) {
                                        $stRestos = $rsTipoRestosPagar->getCampo("tipo_restos");
                                        $obFEmpenhoEmpenhoPagamentoRestosAPagar->setDado("restos"                 , $stRestos );
                                        $obErro = $obFEmpenhoEmpenhoPagamentoRestosAPagar->executaFuncao( $rsFEmpenhoEmpenhoPagamento,'','', $boTransacao );
                                        if ($obErro->ocorreu()) {
                                            if (strstr($obErro->getDescricao(),"Não foi informado o tipo de Restos")) {
                                                $obErro->setDescricao("Impossível realizar os lançamentos. Verificar o atributo de Restos.");
                                            }
                                        }
                                        $inCodLote = $obFEmpenhoEmpenhoPagamentoRestosAPagar->getDado("cod_lote");
                                    }

                                    if (Sessao::getExercicio() > '2008') {
                                        if ( !$obErro->ocorreu() && $inCodPlanoUm != '' && $inCodPlanoDois != '') {
                                            include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeValorLancamento.class.php";

                                            $obTContabilidadeValorLancamento = new TContabilidadeValorLancamento;
                                            $obTContabilidadeValorLancamento->setDado("cod_lote", $inCodLote);
                                            $obTContabilidadeValorLancamento->setDado("tipo", 'P');
                                            $obTContabilidadeValorLancamento->setDado("exercicio", Sessao::getExercicio());
                                            $obTContabilidadeValorLancamento->setDado("cod_entidade", $obRNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() );
                                            $obTContabilidadeValorLancamento->setDado("cod_plano_deb",$inCodPlanoDois);
                                            $obTContabilidadeValorLancamento->setDado("cod_plano_cred",$inCodPlanoUm);
                                            $obTContabilidadeValorLancamento->setDado("cod_historico", 917);
                                            $obTContabilidadeValorLancamento->setDado("complemento", $stComplemento);
                                            $obTContabilidadeValorLancamento->setDado("vl_lancamento", $obRNotaLiquidacao->getValorPago());
    
                                            $obErro = $obTContabilidadeValorLancamento->inclusaoPorPl($rsRecordSet, $boTransacao);
                                            $inSequenciaLancamento = $rsRecordSet->getCampo('sequencia');
                                        } else {
                                            if (!$obErro->getDescricao()) {
                                                $obErro->setDescricao('Contas do compensado não estão cadastradas.');
                                            }
                                        }
                                    }
                                    $inCodHistoricoAdiantamento = 917;
                                }
                                    if ( !$obErro->ocorreu() ) {

                                        $this->arLotes[] = Array( 'cod_lote' => $inCodLote, 'cod_nota' => $obRNotaLiquidacao->getCodNota(), 'timestamp' => $obRNotaLiquidacao->stTimestamp );

                                        $obFEmpenhoEmpenhoPagamento->setDado("cod_lote"           , "" );
                                        $obFEmpenhoEmpenhoPagamentoRestosAPagar->setDado( "cod_lote", "" );
                                        $obTContabilidadeLancamentoEmpenho->setDado("cod_lote"    ,$inCodLote );
                                        $obTContabilidadeLancamentoEmpenho->setDado("tipo"        ,"P" );
                                        $inSequencia = $rsFEmpenhoEmpenhoPagamento->getCampo("sequencia") ;
                                        $obTContabilidadeLancamentoEmpenho->setDado("sequencia"   , $inSequencia );
                                        $obTContabilidadeLancamentoEmpenho->setDado("exercicio"   ,substr($this->stTimestamp,0,4) );
                                        $obTContabilidadeLancamentoEmpenho->setDado("cod_entidade",$obRNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() );
                                        $obTContabilidadeLancamentoEmpenho->setDado("estorno"     ,"false" ) ;
                                        $obErro = $obTContabilidadeLancamentoEmpenho->inclusao( $boTransacao );
                                        if ( !$obErro->ocorreu() ) {
                                            $obTContabilidadePagamento->setDado("exercicio"   ,substr($this->stTimestamp,0,4));
                                            $obTContabilidadePagamento->setDado("exercicio_liquidacao" ,$obRNotaLiquidacao->getExercicio() );
                                            $obTContabilidadePagamento->setDado("sequencia"   ,$inSequencia );
                                            $obTContabilidadePagamento->setDado("tipo"        ,"P" );
                                            $obTContabilidadePagamento->setDado("cod_lote"    ,$inCodLote );
                                            $obTContabilidadePagamento->setDado("cod_entidade",$obRNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() );
                                            $obTContabilidadePagamento->setDado("cod_nota"    ,$obRNotaLiquidacao->getCodNota() );
                                            $obTContabilidadePagamento->setDado("timestamp"   ,$this->stTimestamp ) ;
                                            $obErro = $obTContabilidadePagamento->inclusao( $boTransacao );
                                        }
                                    }

                                    /* PAGAMENTO ADIANTAMENTOS & SUBVENCOES */
                                    $codCategoria = $obRNotaLiquidacao->roREmpenhoEmpenho->getCodCategoria();

                                    if ($codCategoria == 2 || $codCategoria == 3) {
                                        include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeValorLancamento.class.php" );
                                        include_once( TEMP."TEmpenhoResponsavelAdiantamento.class.php");
                                        $obTEmpenhoResponsavelAdiantamento = new TEmpenhoResponsavelAdiantamento();
                                        $stFiltro = " WHERE exercicio = '".Sessao::getExercicio()."' AND numcgm = ".$obRNotaLiquidacao->roREmpenhoEmpenho->obRCGM->getNumCGM();
                                        $obErro = $obTEmpenhoResponsavelAdiantamento->recuperaTodos($rsContas,$stFiltro,'',$boTransacao);

                                        if (!$obErro->ocorreu()) {
    
                                            $stContaContrapartida   = $rsContas->getCampo('conta_contrapartida');
                                            $stContaLancamento      = $rsContas->getCampo('conta_lancamento');
    
                                            $obTContabilidadeValorLancamento    = new TContabilidadeValorLancamento;
                                            $obTContabilidadeValorLancamento->setDado( "cod_lote"      , $inCodLote                              );
                                            $obTContabilidadeValorLancamento->setDado( "tipo"          , 'P'                                     );
                                            $obTContabilidadeValorLancamento->setDado( "exercicio"     , Sessao::getExercicio());//$obRNotaLiquidacao->getExercicio()      );
                                            $obTContabilidadeValorLancamento->setDado( "cod_entidade"  , $obRNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() );
                                            $obTContabilidadeValorLancamento->setDado( "cod_plano_deb" , $stContaLancamento                      );
                                            $obTContabilidadeValorLancamento->setDado( "cod_plano_cred", $stContaContrapartida                   );
                                            $obTContabilidadeValorLancamento->setDado( "cod_historico" , $inCodHistoricoAdiantamento             );
                                            $obTContabilidadeValorLancamento->setDado( "complemento"   , $obRNotaLiquidacao->roREmpenhoEmpenho->getCodEmpenho()."/$arDataPagamento[2]" );
                                            $obTContabilidadeValorLancamento->setDado( "vl_lancamento" , $obRNotaLiquidacao->getValorPago()      );
    
                                            $obErro = $obTContabilidadeValorLancamento->inclusaoPorPl( $rsRecordSet, $boTransacao   );
                                        }
                                    }

                                    /* RETENÇÕES */
                                    if (!$obErro->ocorreu() && $this->obREmpenhoOrdemPagamento->getRetencao() && !$this->obREmpenhoOrdemPagamento->boRetencaoExecutada) {
                                        include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoOrdemPagamentoRetencao.class.php"  );
                                        include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeValorLancamento.class.php" );
                                        include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamentoRetencao.class.php" );
                                        include_once ( CAM_GA_ADM_MAPEAMENTO.'TAdministracaoConfiguracaoEntidade.class.php');
                                        include_once ( CAM_GF_EMP_MAPEAMENTO.   "TEmpenhoNotaLiquidacaoPaga.class.php" );
                                        include_once ( CAM_GF_EMP_MAPEAMENTO.   "TEmpenhoNotaLiquidacaoContaPagadora.class.php" );
                                        include_once ( CAM_GF_EMP_MAPEAMENTO.   "TEmpenhoPagamentoLiquidacaoNotaLiquidacaoPaga.class.php"  );

                                        $obTContabilidadeLancamentoRetencao = new TContabilidadeLancamentoRetencao;
                                        $obTContabilidadeValorLancamento    = new TContabilidadeValorLancamento;
                                        $obTAdministracaoConfiguracaoEntidade = new TAdministracaoConfiguracaoEntidade;
                                        $obTEmpenhoNotaLiquidacaoPagaRet                  =  new TEmpenhoNotaLiquidacaoPaga;
                                        $obTEmpenhoNotaLiquidacaoContaPagadoraRet         =  new TEmpenhoNotaLiquidacaoContaPagadora;
                                        $obTEmpenhoPagamentoLiquidacaoNotaLiquidacaoPagaRet =  new TEmpenhoPagamentoLiquidacaoNotaLiquidacaoPaga;

                                        $inCodEntidade = $obRNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade();
                                        $stFiltroConta = "  WHERE parametro = 'conta_caixa' AND cod_entidade = ".$inCodEntidade." AND exercicio = '".substr($this->stTimestamp,0,4)."' ";
                                        $obErro = $obTAdministracaoConfiguracaoEntidade->recuperaTodos($rsContas, $stFiltroConta, '', $boTransacao);

                                        if (!$obErro->ocorreu() && !$rsContas->EOF() && $rsContas->getNumLinhas() == 1) {
                                            include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadePlanoContaAnalitica.class.php"              );
                                            $obContaAnalitica = new RContabilidadePlanoContaAnalitica;
                                            $obContaAnalitica->setCodPlano( $rsContas->getCampo('valor') );
                                            $obContaAnalitica->setExercicio( substr($this->stTimestamp,0,4) );
                                            $obErro = $obContaAnalitica->consultar( $boTransacao );
                                            $stCodEstruturalCaixa = $obContaAnalitica->getCodEstrutural();
                                            $inCodPlanoCaixa = $obContaAnalitica->getCodPlano();
                                        }

                                        $inCountTime = 0;
                                        $inCountPagamentoRetencao = 0;

                                        if ($stCodEstruturalCaixa && $inCodPlanoCaixa && !$obErro->ocorreu() ) {
                                            // Efetua o lançamento das arrecadações (quando não for pela tesouraria) e pgtos ref. Retenções
                                            foreach ( $this->obREmpenhoOrdemPagamento->getRetencoes() as $arRetencao ) {
                                                // Timestamp diferente para cada pgto de retenção com a conta caixa.
                                                $arTmp = explode(' ',$this->stTimestamp);
                                                $arData = explode('-',$arTmp[0]);
                                                $arHora = explode(':',$arTmp[1]);

                                                // Adiciona 0.010 no milissegundo
                                                $inCountTime = $inCountTime + 0.010;
                                                $arHora[2] = bcadd($arHora[2], $inCountTime, 4);
                                                $arHoraTmp = explode('.', $arHora[2]);
                                                $arHoraTmp[0] = str_pad($arHoraTmp[0], 2, 0, STR_PAD_LEFT);

                                                $stTimestampPagamentoCtaCaixa = $arData[0]."-".$arData[1]."-".$arData[2]." ".$arHora[0].":".$arHora[1].":".$arHoraTmp[0].".".$arHoraTmp[1];

                                                $this->arPagamentosRetencao[$inCountPagamentoRetencao]['cod_nota'] = $obRNotaLiquidacao->getCodNota();
                                                $this->arPagamentosRetencao[$inCountPagamentoRetencao]['timestamp'] = $stTimestampPagamentoCtaCaixa;
                                                $this->arPagamentosRetencao[$inCountPagamentoRetencao]['exercicio'] = $obRNotaLiquidacao->getExercicio();
                                                $this->arPagamentosRetencao[$inCountPagamentoRetencao]['cod_entidade'] = $inCodEntidade;
                                                $this->arPagamentosRetencao[$inCountPagamentoRetencao]['cod_plano'] = $inCodPlanoCaixa;
                                                $inCountPagamentoRetencao++;

                                                // PAGAMENTOS USANDO A CONTA CAIXA
                                                if (!$stRestos) { // Empenho do exercicio
                                                    $obFEmpenhoEmpenhoPagamento->setDado("cod_lote"           , "" );
                                                    $obFEmpenhoEmpenhoPagamento->setDado("conta_pagamento_financ", $stCodEstruturalCaixa );
                                                    if (Sessao::getExercicio() > '2012') {
                                                        $obFEmpenhoEmpenhoPagamento->setDado("cod_plano_credito", $inCodPlanoCaixa );
                                                    }
                                                    $obFEmpenhoEmpenhoPagamento->setDado("valor", $arRetencao['vl_retencao']);
                                                    $obErro = $obFEmpenhoEmpenhoPagamento->executaFuncao( $rsFEmpenhoEmpenhoPagamento, $boTransacao );
                                                    $inCodLotePgto = $obFEmpenhoEmpenhoPagamento->getDado('cod_lote');
                                                    $inSequenciaPgto = $obFEmpenhoEmpenhoPagamento->getDado('sequencia');
                                                    $inCodHistorico = 903;
                                                } else { // Empenho do exercicio anterior
                                                    $obFEmpenhoEmpenhoPagamentoRestosAPagar->setDado("cod_lote", "" );
                                                    $obFEmpenhoEmpenhoPagamentoRestosAPagar->setDado("conta_pg", $stCodEstruturalCaixa );
                                                    $obFEmpenhoEmpenhoPagamentoRestosAPagar->setDado("valor" , $arRetencao['vl_retencao']);
                                                    $obErro = $obFEmpenhoEmpenhoPagamentoRestosAPagar->executaFuncao( $rsFEmpenhoEmpenhoPagamento,'','', $boTransacao );
                                                    $inCodLotePgto = $obFEmpenhoEmpenhoPagamentoRestosAPagar->getDado('cod_lote');
                                                    $inSequenciaPgto = $obFEmpenhoEmpenhoPagamentoRestosAPagar->getDado('sequencia');
                                                    $inCodHistorico = 917;
                                                }

                                                if (Sessao::getExercicio() > '2008') {
                                                    if ( !$obErro->ocorreu() && $inCodPlanoUm != '' && $inCodPlanoDois != '') {
                                                        include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeValorLancamento.class.php";

                                                        $obTContabilidadeValorLancamento = new TContabilidadeValorLancamento;
                                                        $obTContabilidadeValorLancamento->setDado("cod_lote", $inCodLotePgto);
                                                        $obTContabilidadeValorLancamento->setDado("tipo", 'P');
                                                        $obTContabilidadeValorLancamento->setDado("exercicio", Sessao::getExercicio());//$obRNotaLiquidacao->getExercicio());
                                                        $obTContabilidadeValorLancamento->setDado("cod_entidade", $inCodEntidade);
                                                        $obTContabilidadeValorLancamento->setDado("cod_plano_deb", $inCodPlanoDois);
                                                        $obTContabilidadeValorLancamento->setDado("cod_plano_cred", $inCodPlanoUm);
                                                        $obTContabilidadeValorLancamento->setDado("cod_historico", $inCodHistorico);
                                                        $obTContabilidadeValorLancamento->setDado("complemento", $stComplemento);
                                                        $obTContabilidadeValorLancamento->setDado("vl_lancamento", $arRetencao['vl_retencao']);

                                                        $obErro = $obTContabilidadeValorLancamento->inclusaoPorPl($rsRecordSet, $boTransacao);
                                                        $inSequenciaLancamento = $rsRecordSet->getCampo('sequencia');
                                                    } else {
                                                        if (!$obErro->getDescricao()) {
                                                            $obErro->setDescricao('Contas do compensado não estão cadastradas.');
                                                        }
                                                    }
                                                }

                                                if ($inCodLotePgto && !$obErro->ocorreu()) {
                                                    $obTContabilidadeLancamentoEmpenho->setDado("cod_lote"    ,$inCodLotePgto );
                                                    $obTContabilidadeLancamentoEmpenho->setDado("tipo"        ,"P" );
                                                    $obTContabilidadeLancamentoEmpenho->setDado("sequencia"   , $inSequenciaPgto );
                                                    $obTContabilidadeLancamentoEmpenho->setDado("exercicio"   ,substr($this->stTimestamp,0,4) );
                                                    $obTContabilidadeLancamentoEmpenho->setDado("cod_entidade", $inCodEntidade );
                                                    $obTContabilidadeLancamentoEmpenho->setDado("estorno"     ,"false" ) ;
                                                    $obErro = $obTContabilidadeLancamentoEmpenho->inclusao( $boTransacao );

                                                    if ( !$obErro->ocorreu() ) {
                                                        $obTEmpenhoNotaLiquidacaoPagaRet->setDado( 'cod_entidade', $inCodEntidade );
                                                        $obTEmpenhoNotaLiquidacaoPagaRet->setDado( 'cod_nota'    , $obRNotaLiquidacao->getCodNota()   );
                                                        $obTEmpenhoNotaLiquidacaoPagaRet->setDado( 'exercicio'   , $obRNotaLiquidacao->getExercicio() );
                                                        $obTEmpenhoNotaLiquidacaoPagaRet->setDado( 'timestamp'   , $stTimestampPagamentoCtaCaixa      );
                                                        $obTEmpenhoNotaLiquidacaoPagaRet->setDado( 'vl_pago'     , $arRetencao['vl_retencao']         );
                                                        $obTEmpenhoNotaLiquidacaoPagaRet->setDado( 'observacao'  , $this->stObservacao                );
                                                        $obErro = $obTEmpenhoNotaLiquidacaoPagaRet->inclusao( $boTransacao );

                                                        if (!$obErro->ocorreu()) {
                                                            $obTContabilidadePagamento->setDado("exercicio"   ,substr($this->stTimestamp,0,4));
                                                            $obTContabilidadePagamento->setDado("exercicio_liquidacao" ,$obRNotaLiquidacao->getExercicio() );
                                                            $obTContabilidadePagamento->setDado("sequencia"   ,$inSequenciaPgto );
                                                            $obTContabilidadePagamento->setDado("tipo"        ,"P" );
                                                            $obTContabilidadePagamento->setDado("cod_lote"    ,$inCodLotePgto );
                                                            $obTContabilidadePagamento->setDado("cod_entidade", $inCodEntidade );
                                                            $obTContabilidadePagamento->setDado("cod_nota"    ,$obRNotaLiquidacao->getCodNota() );
                                                            $obTContabilidadePagamento->setDado("timestamp"   ,$stTimestampPagamentoCtaCaixa ) ;
                                                            $obErro = $obTContabilidadePagamento->inclusao( $boTransacao );

                                                            if (!$obErro->ocorreu()) {
                                                                $obTContabilidadeLancamentoRetencao->setDado('cod_lote' , $inCodLotePgto   );
                                                                $obTContabilidadeLancamentoRetencao->setDado( "tipo"    , 'P'              );
                                                                $obTContabilidadeLancamentoRetencao->setDado('cod_entidade', $inCodEntidade);
                                                                $obTContabilidadeLancamentoRetencao->setDado('exercicio', $arDataPagamento[2] );
                                                                $obTContabilidadeLancamentoRetencao->setDado('sequencia', $inSequenciaPgto );
                                                                $obTContabilidadeLancamentoRetencao->setDado('cod_ordem', $this->obREmpenhoOrdemPagamento->getCodigoOrdem() );
                                                                $obTContabilidadeLancamentoRetencao->setDado('cod_plano', $arRetencao['cod_plano'] );
                                                                $obTContabilidadeLancamentoRetencao->setDado('exercicio_retencao', $arRetencao['exercicio'] );
                                                                $obTContabilidadeLancamentoRetencao->setDado('sequencial', $arRetencao['sequencial']);
                                                                $obErro = $obTContabilidadeLancamentoRetencao->inclusao( $boTransacao );

                                                                if (!$obErro->ocorreu()) {
                                                                    $obTEmpenhoNotaLiquidacaoContaPagadoraRet->setDado("cod_nota"               ,$obRNotaLiquidacao->getCodNota() );
                                                                    $obTEmpenhoNotaLiquidacaoContaPagadoraRet->setDado("cod_entidade"           ,$obRNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade()  );
                                                                    $obTEmpenhoNotaLiquidacaoContaPagadoraRet->setDado("exercicio_liquidacao"   ,$obRNotaLiquidacao->getExercicio() );
                                                                    $obTEmpenhoNotaLiquidacaoContaPagadoraRet->setDado("timestamp"              ,$stTimestampPagamentoCtaCaixa );
                                                                    $obTEmpenhoNotaLiquidacaoContaPagadoraRet->setDado("exercicio"              ,$this->obRContabilidadePlanoContaAnalitica->getExercicio() );
                                                                    $obTEmpenhoNotaLiquidacaoContaPagadoraRet->setDado("cod_plano"              ,$inCodPlanoCaixa  );
                                                                    $obErro = $obTEmpenhoNotaLiquidacaoContaPagadoraRet->inclusao($boTransacao);

                                                                    if (!$obErro->ocorreu()) {
                                                                        $obTEmpenhoPagamentoLiquidacaoNotaLiquidacaoPagaRet->setDado( 'cod_entidade'            , $obRNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() );
                                                                        $obTEmpenhoPagamentoLiquidacaoNotaLiquidacaoPagaRet->setDado( 'cod_nota'                , $obRNotaLiquidacao->getCodNota() );
                                                                        $obTEmpenhoPagamentoLiquidacaoNotaLiquidacaoPagaRet->setDado( 'exercicio'               , $this->obREmpenhoOrdemPagamento->getExercicio() );
                                                                        $obTEmpenhoPagamentoLiquidacaoNotaLiquidacaoPagaRet->setDado( 'timestamp'               , $stTimestampPagamentoCtaCaixa );
                                                                        $obTEmpenhoPagamentoLiquidacaoNotaLiquidacaoPagaRet->setDado( 'exercicio_liquidacao'    ,  $obRNotaLiquidacao->getExercicio() );
                                                                        $obTEmpenhoPagamentoLiquidacaoNotaLiquidacaoPagaRet->setDado( 'cod_ordem'   , $this->obREmpenhoOrdemPagamento->getCodigoOrdem() );
                                                                        $obErro = $obTEmpenhoPagamentoLiquidacaoNotaLiquidacaoPagaRet->inclusao( $boTransacao );
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                                /* Arrecadações das Retenções */
                                                if (!$this->getTesouraria() && !$obErro->ocorreu()) { // Se não for via Tesouraria
                                                    $obTContabilidadeLancamentoRetencao->setDado( "cod_lote"      , '');
                                                    $obTContabilidadeLancamentoRetencao->setDado( "tipo"          , $arRetencao['tipo'] == 'O' ? 'A' : 'T'      );
                                                    $obTContabilidadeLancamentoRetencao->setDado( "nom_lote"      , $arRetencao['tipo'] == 'O' ? "Arrecadação por Retenção Orçamentária - OP ".$this->obREmpenhoOrdemPagamento->getCodigoOrdem()."/$arDataPagamento[2]" : "Transferência - CD:".$inCodPlanoCaixa." | CC:".$arRetencao['cod_plano'] );
                                                    $obTContabilidadeLancamentoRetencao->setDado( "dt_lote"       , $this->stDataPagamento );
                                                    $obTContabilidadeLancamentoRetencao->setDado( "exercicio"     , Sessao::getExercicio());//$arDataPagamento[2] );
                                                    $obTContabilidadeLancamentoRetencao->setDado( "cod_entidade"  , $inCodEntidade      );
                                                    $obTContabilidadeLancamentoRetencao->setDado( "sequencial"    , $arRetencao['sequencial']      );
                                                    $obErro = $obTContabilidadeLancamentoRetencao->insereLote( $inCodLoteArrecadacao, $boTransacao );
                                                    if ($arRetencao['tipo'] == 'O') { // Retenção Receita Orçamentária
                                                        if (!$obErro->ocorreu()) {
                                                            include_once( CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoReceita.class.php"        );
                                                            $obRContabilidadeLancamentoReceita = new RContabilidadeLancamentoReceita;
                                                            $obRContabilidadeLancamentoReceita->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $inCodEntidade );
                                                            $obRContabilidadeLancamentoReceita->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote( $inCodLoteArrecadacao );
                                                            $obRContabilidadeLancamentoReceita->obRContabilidadeLancamento->obRContabilidadeLote->setNomLote( "Arrecadação por Retenção Orçamentária - OP ".$this->obREmpenhoOrdemPagamento->getCodigoOrdem()."/$arDataPagamento[2]" );
                                                            $obRContabilidadeLancamentoReceita->obRContabilidadeLancamento->obRContabilidadeLote->setDtLote( $this->stDataPagamento );
                                                            $obRContabilidadeLancamentoReceita->setContaDebito( $inCodPlanoCaixa );
                                                            $obRContabilidadeLancamentoReceita->obRContabilidadeLancamento->obRContabilidadeHistoricoPadrao->setCodHistorico( 950 );
                                                            $obRContabilidadeLancamentoReceita->obRContabilidadeLancamento->setBoComplemento( true );
                                                            $obRContabilidadeLancamentoReceita->obRContabilidadeLancamento->setComplemento( $this->obREmpenhoOrdemPagamento->getCodigoOrdem()."/".$arDataPagamento[2] );
                                                            $obRContabilidadeLancamentoReceita->obROrcamentoReceita->setCodReceita( $arRetencao['cod_receita'] );
                                                            $obRContabilidadeLancamentoReceita->setValor(  $arRetencao['vl_retencao'] );
                                                            $obRContabilidadeLancamentoReceita->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio( $arRetencao['exercicio'] );
                                                            $obErro = $obRContabilidadeLancamentoReceita->incluir( $boTransacao, true );
                                                            if (!$obErro->ocorreu()) {
                                                                $inSequenciaRet = $obRContabilidadeLancamentoReceita->obRContabilidadeLancamento->getSequencia();
                                                            }
                                                        }
                                                    } elseif ($arRetencao['tipo'] == 'E') {
                                                        if (Sessao::getExercicio() > '2008') {
                                                            $obRContabilidadePlanoBanco->setCodPlano($arRetencao['cod_plano']);
                                                            $obErro = $obRContabilidadePlanoBanco->getRecursoVinculoConta($rsCodRecurso, $boTransacao);
                                                            $inCodRecursoRet = $rsCodRecurso->getCampo('cod_recurso');

                                                            $boDestinacao = false;
                                                            $obTOrcamentoConfiguracao = new TOrcamentoConfiguracao;
                                                            $obTOrcamentoConfiguracao->setDado("exercicio", Sessao::getExercicio() );
                                                            $obTOrcamentoConfiguracao->setDado("parametro","recurso_destinacao");
                                                            $obTOrcamentoConfiguracao->consultar($boTransacao);
                                                            if($obTOrcamentoConfiguracao->getDado("valor") == 'true')
                                                                $boDestinacao = true;

                                                            if ($boDestinacao && $inCodRecursoRet != '') {
                                                                $obTOrcamentoRecursoDestinacao = new TOrcamentoRecursoDestinacao;
                                                                $obTOrcamentoRecursoDestinacao->setDado("exercicio", Sessao::getExercicio());

                                                                $stFiltro  = ' WHERE recurso_destinacao.cod_recurso = '.$inCodRecursoRet;
                                                                $stFiltro .= '   AND recurso_destinacao.exercicio = '.Sessao::getExercicio();
                                                                $obErro = $obTOrcamentoRecursoDestinacao->recuperaTodos($rsDestinacao, $stFiltro, '', $boTransacao);
                                                                $inCodEspecificacao = $rsDestinacao->getCampo('cod_especificacao');

                                                                if ($inCodEspecificacao != '') {
                                                                    // Verifica qual o cod_recurso que possui conta contabil vinculada C
                                                                    $obTOrcamentoRecursoDestinacao->setDado('exercicio', Sessao::getExercicio());
                                                                    $obTOrcamentoRecursoDestinacao->setDado('cod_especificacao', $inCodEspecificacao);
                                                                    if ( Sessao::getExercicio() > '2012' ) {
                                                                        $obTOrcamentoRecursoDestinacao->setDado('cod_estrutural', "'8.2.1.1.3.%'");
                                                                    } else {
                                                                        $obTOrcamentoRecursoDestinacao->setDado('cod_estrutural', "'2.9.3.2.0.00.00.%'");
                                                                    }
                                                                    $obTOrcamentoRecursoDestinacao->recuperaRecursoVinculoConta($rsContaRecurso, '', '', $boTransacao);

                                                                    $inCodRecursoRet = $rsContaRecurso->getCampo('cod_recurso');
                                                                }
                                                            }

                                                            if (!$obErro->ocorreu() && $inCodRecursoRet != '') {
                                                                $obRContabilidadePlanoBanco->obROrcamentoRecurso->setCodRecurso($inCodRecursoRet);
                                                                if (Sessao::getExercicio() > '2012') {
                                                                    $obErro = $obRContabilidadePlanoBanco->getContasRecursoPagamentoTCEMS($rsContasRecursoRet, $boTransacao);
                                                                } else {
                                                                    $obErro = $obRContabilidadePlanoBanco->getContasRecurso($rsContasRecursoRet, $boTransacao);
                                                                }
                                                                $inCodPlanoRetUm = $rsContasRecursoRet->getCampo('cod_plano_um');
                                                                $inCodPlanoRetDois = $rsContasRecursoRet->getCampo('cod_plano_dois');

                                                                if ($inCodPlanoRetUm != '' && $inCodPlanoRetDois != '') {
                                                                    include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeValorLancamento.class.php";

                                                                    $obTContabilidadeValorLancamento = new TContabilidadeValorLancamento;
                                                                    $obTContabilidadeValorLancamento->setDado("cod_lote", $inCodLoteArrecadacao);
                                                                    $obTContabilidadeValorLancamento->setDado("tipo", 'T');
                                                                    $obTContabilidadeValorLancamento->setDado("exercicio", Sessao::getExercicio());//$arDataPagamento[2]);
                                                                    $obTContabilidadeValorLancamento->setDado("cod_entidade", $inCodEntidade);
                                                                    $obTContabilidadeValorLancamento->setDado("cod_plano_deb", $inCodPlanoRetDois);
                                                                    $obTContabilidadeValorLancamento->setDado("cod_plano_cred", $inCodPlanoRetUm);
                                                                    $obTContabilidadeValorLancamento->setDado("cod_historico", 952);
                                                                    $obTContabilidadeValorLancamento->setDado("complemento", $this->obREmpenhoOrdemPagamento->getCodigoOrdem()."/".$arDataPagamento[2]);
                                                                    $obTContabilidadeValorLancamento->setDado("vl_lancamento", $arRetencao['vl_retencao']);

                                                                    $obErro = $obTContabilidadeValorLancamento->inclusaoPorPl($rsRecordSet, $boTransacao);
                                                                    $inSequenciaRet = $rsRecordSet->getCampo('sequencia');
                                                                } else {
                                                                    if (!$obErro->getDescricao()) {
                                                                        $obErro->setDescricao('Contas do compensado não estão cadastradas.');
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }

                                                    if (!$obErro->ocorreu()) {
                                                            $obTContabilidadeLancamentoRetencao->setDado('tipo'     , $arRetencao['tipo'] == 'O' ? 'A' : 'T'           );
                                                            $obTContabilidadeLancamentoRetencao->setDado('cod_lote' , $inCodLoteArrecadacao );
                                                            $obTContabilidadeLancamentoRetencao->setDado('cod_entidade' , $inCodEntidade );
                                                            $obTContabilidadeLancamentoRetencao->setDado('exercicio', $arDataPagamento[2] );
                                                            $obTContabilidadeLancamentoRetencao->setDado('sequencia', $inSequenciaRet );
                                                            $obTContabilidadeLancamentoRetencao->setDado('cod_ordem', $this->obREmpenhoOrdemPagamento->getCodigoOrdem() );
                                                            $obTContabilidadeLancamentoRetencao->setDado('cod_plano', $arRetencao['cod_plano'] );
                                                            $obTContabilidadeLancamentoRetencao->setDado('exercicio_retencao', $arRetencao['exercicio'] );
                                                            $obTContabilidadeLancamentoRetencao->setDado('sequencial', $arRetencao['sequencial'] );
                                                            $obErro = $obTContabilidadeLancamentoRetencao->inclusao( $boTransacao );
                                                    }
                                                } /* fim se não utiliza Tesouraria. Via tesouraria os lançamentos de
                                                 arrecadações orçamentárias e extra das retenções serão feitos pelas classes da tesouraria */
                                            } // Fim foreach nas retenções da OP
                                        } else {
                                            $obErro->setDescricao('Uma conta de Caixa deve ser configurada para esta Entidade.');
                                        }// fim verificação da configuração da conta de caixa para a entidade da nota.
                                    } /* FIM RETENÇÕES */
                                } // Fim se o valor a pagar da nota for maior que zero
                                }
                        } // Fim foreach nas notas da OP
                    }
                }
            }
        } else {
            $inCodigoOrdem = $this->obREmpenhoOrdemPagamento->getCodigoOrdem();
            $obErro->setDescricao( 'ERRO: Já foi efetuado Pagamento de O.P. para esta ordem. OP: '.$inCodigoOrdem.' ' );
        }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTEmpenhoNotaLiquidacaoPagaAnulada );

    return $obErro;
}

/**
    * Método para fazer inclusão do estorno de notas da ordem de pagamento
    * @access Private
    * @param Object $obRNotaLiquidacao
    * @return Object $obErro
*/
function incluirNotaLiquidacaoAnulada($obRNotaLiquidacao, $boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO.   "TEmpenhoNotaLiquidacaoPagaAnulada.class.php"              );
    $obTEmpenhoNotaLiquidacaoPagaAnulada              =  new TEmpenhoNotaLiquidacaoPagaAnulada;

    $obTEmpenhoNotaLiquidacaoPagaAnulada->setDado( 'cod_entidade', $obRNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() );
    $obTEmpenhoNotaLiquidacaoPagaAnulada->setDado( 'cod_nota'    , $obRNotaLiquidacao->getCodNota()       );
    $obTEmpenhoNotaLiquidacaoPagaAnulada->setDado( 'exercicio'   , $obRNotaLiquidacao->getExercicio()     );
    $obTEmpenhoNotaLiquidacaoPagaAnulada->setDado( 'timestamp_anulada'   , $this->stTimestampAnulada      );
    $obTEmpenhoNotaLiquidacaoPagaAnulada->setDado( 'timestamp'   , $obRNotaLiquidacao->getTimestamp()     );
    $obTEmpenhoNotaLiquidacaoPagaAnulada->setDado( 'vl_anulado'  , $obRNotaLiquidacao->getValorEstornado());
    $obTEmpenhoNotaLiquidacaoPagaAnulada->setDado( 'observacao'  , $this->stObservacao                );

    $obErro = $obTEmpenhoNotaLiquidacaoPagaAnulada->inclusao( $boTransacao );

    return $obErro;
}

/**
    * Método para fazer inclusão das notas da ordem de pagamento na tabela de auditoria
    * @access Private
    * @param Object $obRNotaLiquidacao
    * @return Object $obErro
*/
function incluirNotaLiquidacaoAnuladaAuditoria($obRNotaLiquidacao, $boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO.   "TEmpenhoNotaLiquidacaoPagaAnuladaAuditoria.class.php"     );
    $obTEmpenhoNotaLiquidacaoPagaAnuladaAuditoria     =  new TEmpenhoNotaLiquidacaoPagaAnuladaAuditoria;

    $obTEmpenhoNotaLiquidacaoPagaAnuladaAuditoria->setDado( 'cod_entidade', $obRNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() );
    $obTEmpenhoNotaLiquidacaoPagaAnuladaAuditoria->setDado( 'cod_nota'    , $obRNotaLiquidacao->getCodNota()      );
    $obTEmpenhoNotaLiquidacaoPagaAnuladaAuditoria->setDado( 'exercicio'   , $obRNotaLiquidacao->getExercicio()    );
    $obTEmpenhoNotaLiquidacaoPagaAnuladaAuditoria->setDado( 'timestamp'   , $obRNotaLiquidacao->getTimestamp()    );
    $obTEmpenhoNotaLiquidacaoPagaAnuladaAuditoria->setDado( 'timestamp_anulada'   , $this->stTimestampAnulada    );
    $obTEmpenhoNotaLiquidacaoPagaAnuladaAuditoria->setDado( 'numcgm'      , $obRNotaLiquidacao->roREmpenhoEmpenho->obRCGM->getNumCGM() );
    $obErro = $obTEmpenhoNotaLiquidacaoPagaAnuladaAuditoria->inclusao( $boTransacao );

    return $obErro;
}

/**
    * Método para estornar um pagamento da OP
    * @access Publico
    * @param Object $boTransacao
    * @return Object $obErro
*/
function estornarOP($boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO.   "TEmpenhoNotaLiquidacaoPagaAnulada.class.php"              );
    include_once ( CAM_GF_CONT_MAPEAMENTO.   "TContabilidadeLancamentoEmpenho.class.php"                );
    include_once ( CAM_GF_CONT_MAPEAMENTO.   "TContabilidadePagamento.class.php"                        );
    include_once ( CAM_GF_CONT_MAPEAMENTO.   "TContabilidadePagamentoEstorno.class.php"                        );
    include_once ( CAM_GF_EMP_MAPEAMENTO.   "FEmpenhoEmpenhoPagamentoAnulacao.class.php"               );
    include_once ( CAM_GF_EMP_MAPEAMENTO.   "FEmpenhoEmpenhoPagamentoRestosAPagarAnulacao.class.php"   );
    include_once CAM_GF_CONT_NEGOCIO.'RContabilidadePlanoBanco.class.php';
    include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoConfiguracao.class.php";
    include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecursoDestinacao.class.php";
    include_once CAM_GF_CONT_MAPEAMENTO.'TContabilidadeConfiguracaoLancamentoCredito.class.php';
    $obRContabilidadePlanoBanco                       =  new RContabilidadePlanoBanco;
    $obFEmpenhoEmpenhoPagamentoRestosAPagarAnulacao   =  new FEmpenhoEmpenhoPagamentoRestosAPagarAnulacao;
    $obFEmpenhoEmpenhoPagamentoAnulacao               =  new FEmpenhoEmpenhoPagamentoAnulacao;
    $obTContabilidadePagamento                        =  new TContabilidadePagamento;
    $obTContabilidadePagamentoEstorno               =  new TContabilidadePagamentoEstorno;
    $obTContabilidadeLancamentoEmpenho                =  new TContabilidadeLancamentoEmpenho;
    $obTEmpenhoNotaLiquidacaoPagaAnulada              =  new TEmpenhoNotaLiquidacaoPagaAnulada;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao);
    if ( !$obErro->ocorreu() ) {
        $this->obREmpenhoOrdemPagamento->setEstorno( true );
        $obErro = $this->obREmpenhoOrdemPagamento->consultar($boTransacao);

        if ( !$obErro->ocorreu() ) {
            $obErro = $this->obREmpenhoOrdemPagamento->consultarValorAPagar( $boTransacao );
        }

        if ( !$obErro->ocorreu() ) {

            if ( $this->obREmpenhoOrdemPagamento->getValorPago() > 0 ) {

                $this->boEstorno = true;
                $this->listarNotaLiquidacaoPaga( $rsLiquidacaoPaga, $boTransacao );
                $arDataPagamento = explode("/",$this->stDataPagamento);
                $arDataAnulacao = explode("/",$this->stDataAnulacao);
                if ($arDataAnulacao[2].$arDataAnulacao[1].$arDataAnulacao[0] >= $arDataPagamento[2].$arDataPagamento[1].$arDataPagamento[0]) {
                    if (!$this->stTimestampAnulada) {
                        $data = explode( "/",$this->stDataAnulacao );
                        $stDataAnul = $data[2] . "-" . $data[1] . "-" . $data[0];
                        $this->stTimestampAnulada = $stDataAnul.' '.date( 'H:i:s.ms' );
                    }
                $stTimestampAnuladaBak = $this->stTimestampAnulada;

                    /*
                     * Atualiza os Valores
                     */
                    for ( $i=0; $i <= count($this->arValoresPagos)-1; $i++ ) {
                        for ( $j=0; $j <= count($this->obREmpenhoOrdemPagamento->arNotaLiquidacao)-1; $j++ ) {
                            if (    ($this->arValoresPagos[$i]['cod_nota']  == $this->obREmpenhoOrdemPagamento->arNotaLiquidacao[$j]->getCodNota()   )
                                 && ($this->arValoresPagos[$i]['exercicio'] == $this->obREmpenhoOrdemPagamento->arNotaLiquidacao[$j]->getExercicio() )
                                 && ($this->arValoresPagos[$i]['timestamp'] == $this->obREmpenhoOrdemPagamento->arNotaLiquidacao[$j]->getTimestamp() )
                               )
                               {
                                     $this->obREmpenhoOrdemPagamento->arNotaLiquidacao[$j]->nuVlPago         = $this->arValoresPagos[$i]['vl_pago'];
                                     $this->obREmpenhoOrdemPagamento->arNotaLiquidacao[$j]->nuVlEstornado    = $this->arValoresPagos[$i]['vl_estornado'];
                                     $this->obREmpenhoOrdemPagamento->arNotaLiquidacao[$j]->inCodPlano       = $this->arValoresPagos[$i]['cod_plano'];
                                if ($this->obREmpenhoOrdemPagamento->getRetencao()) {
                                     $this->obREmpenhoOrdemPagamento->arNotaLiquidacao[$j]->inCodPlanoRetencao = $this->arValoresPagos[$i]['cod_plano_retencao'];
                                }
                                     $this->obREmpenhoOrdemPagamento->arNotaLiquidacao[$j]->stExercicioPlano = $this->arValoresPagos[$i]['exercicio_plano'];
                                     $this->obREmpenhoOrdemPagamento->arNotaLiquidacao[$j]->stTimestamp      = $this->arValoresPagos[$i]['timestamp'];

                               }
                        }
                    }
                    $inCountPagamentoRetencao = 0;
                    $inCountTime = 0;
                    $inCodEntidade = isset($inCodEntidade) ? $inCodEntidade : null;
                    foreach ($this->obREmpenhoOrdemPagamento->arNotaLiquidacao as $obRNotaLiquidacao) {
                        if (Sessao::getExercicio() > '2008') {
                            $inCodRecurso = $obRNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->getCodRecurso();

                            $boDestinacao = false;
                            $obTOrcamentoConfiguracao = new TOrcamentoConfiguracao;                            
                            $obTOrcamentoConfiguracao->setDado("exercicio", Sessao::getExercicio() );
                            $obTOrcamentoConfiguracao->setDado("parametro","recurso_destinacao");
                            $obErro = $obTOrcamentoConfiguracao->consultar($boTransacao);
                            
                            //Validacao da configuracao de destino de recurso que poderá vir nulo
                            if ($obErro->ocorreu()){
                                $obErro->setDescricao("Necessário configurar Destinação de Recursos na ação Gestão Financeira :: Orçamento :: Configuração :: Alterar Configuração ");
                                SistemaLegado::LiberaFrames('true','true');
                            }

                            if($obTOrcamentoConfiguracao->getDado("valor") == 'true')
                                $boDestinacao = true;

                            if ($boDestinacao && $inCodRecurso != '') {
                                $obTOrcamentoRecursoDestinacao = new TOrcamentoRecursoDestinacao;
                                $obTOrcamentoRecursoDestinacao->setDado("exercicio", Sessao::getExercicio());

                                $stFiltro  = ' WHERE recurso_destinacao.cod_recurso = '.$inCodRecurso;
                                $stFiltro .= '   AND recurso_destinacao.exercicio = '.Sessao::getExercicio();
                                $obErro = $obTOrcamentoRecursoDestinacao->recuperaTodos($rsDestinacao, $stFiltro, '', $boTransacao);
                                $inCodEspecificacao = $rsDestinacao->getCampo('cod_especificacao');

                                // Verifica qual o cod_recurso que possui conta contabil vinculada
                                $obTOrcamentoRecursoDestinacao->setDado('exercicio', Sessao::getExercicio());
                                $obTOrcamentoRecursoDestinacao->setDado('cod_especificacao', $inCodEspecificacao);
                                if ( Sessao::getExercicio() > '2012' ) {
                                    $obTOrcamentoRecursoDestinacao->setDado('cod_estrutural', "'8.2.1.1.3.%'");
                                } else {
                                    $obTOrcamentoRecursoDestinacao->setDado('cod_estrutural', "'2.9.3.2.0.00.00.%'");
                                }
                                $obTOrcamentoRecursoDestinacao->recuperaRecursoVinculoConta($rsContaRecurso, '', '', $boTransacao);

                                $inCodRecurso = $rsContaRecurso->getCampo('cod_recurso');
                            }

                            $obRContabilidadePlanoBanco->obROrcamentoRecurso->setCodRecurso($inCodRecurso);

                            if ( Sessao::getExercicio() > '2012' ) {
                                $obErro = $obRContabilidadePlanoBanco->getContasRecursoPagamentoTCEMS($rsContasRecurso, $boTransacao);
                            } else {
                                $obErro = $obRContabilidadePlanoBanco->getContasRecurso($rsContasRecurso, $boTransacao);
                            }
                            $inCodPlanoUm = $rsContasRecurso->getCampo('cod_plano_um');
                            $inCodPlanoDois = $rsContasRecurso->getCampo('cod_plano_dois');

                        } else {
                            $inCodPlanoUm = '';
                            $inCodPlanoDois = '';
                        }
                        if ( $obRNotaLiquidacao->getValorEstornado() > 0 ) {
                            if ($obRNotaLiquidacao->inCodPlanoRetencao) {
                                $stTimestampTemp = $this->stTimestampAnulada;
                                // Timestamp diferente para cada estorno de pgto de retenção;
                                $arTmp = explode(' ',$this->stTimestampAnulada);
                                $arData = explode('-',$arTmp[0]);
                                $arHora = explode(':',$arTmp[1]);

                                // Adiciona 0.010 no milissegundo
                                $inCountTime = $inCountTime + 0.010;
                                $arHora[2] = bcadd($arHora[2], $inCountTime, 4);
                                $arHoraTmp = explode('.', $arHora[2]);
                                $arHoraTmp[0] = str_pad($arHoraTmp[0], 2, 0, STR_PAD_LEFT);

                                $stTimestampEstornoRetencao = $arData[0]."-".$arData[1]."-".$arData[2]." ".$arHora[0].":".$arHora[1].":".$arHoraTmp[0].".".$arHoraTmp[1];

                                $this->arPagamentosRetencao[$inCountPagamentoRetencao]['cod_nota'] = $obRNotaLiquidacao->getCodNota();
                                $this->arPagamentosRetencao[$inCountPagamentoRetencao]['timestamp'] = $obRNotaLiquidacao->stTimestamp;
                                $this->arPagamentosRetencao[$inCountPagamentoRetencao]['timestamp_anulada'] = $stTimestampEstornoRetencao;
                                $this->arPagamentosRetencao[$inCountPagamentoRetencao]['exercicio'] = $obRNotaLiquidacao->getExercicio();
                                $this->arPagamentosRetencao[$inCountPagamentoRetencao]['cod_entidade'] = $inCodEntidade;
                                $inCountPagamentoRetencao++;

                                $this->stTimestampAnulada = $stTimestampEstornoRetencao;

                            } else $this->stTimestampAnulada = $stTimestampAnuladaBak;
                            $obErro = $this->incluirNotaLiquidacaoAnulada( $obRNotaLiquidacao, $boTransacao );
                            if( $obErro->ocorreu() )
                                break;
                            $obErro = $this->incluirNotaLiquidacaoAnuladaAuditoria( $obRNotaLiquidacao, $boTransacao );
                            if( $obErro->ocorreu() )
                                break;

                            $this->obRContabilidadePlanoContaAnalitica->setCodPlano( $obRNotaLiquidacao->getCodPlano() );
                            $this->obRContabilidadePlanoContaAnalitica->setExercicio ( $obRNotaLiquidacao->getExercicioPlano() );

                            if ( $this->obRContabilidadePlanoContaAnalitica->getCodPlano() )
                              $obErro = $this->obRContabilidadePlanoContaAnalitica->consultar( $boTransacao );

                            $inCodEstrutural = str_replace('.', '', $this->obRContabilidadePlanoContaAnalitica->getCodEstrutural() );
                            if ( $this->obRContabilidadePlanoContaAnalitica->getExercicio() == $obRNotaLiquidacao->roREmpenhoEmpenho->getExercicio() ) {
                                $obFEmpenhoEmpenhoPagamentoAnulacao->setDado("exercicio"    ,substr($obRNotaLiquidacao->stTimestamp,0,4));
                                $obFEmpenhoEmpenhoPagamentoAnulacao->setDado("exercicio_liquidacao" ,$obRNotaLiquidacao->getExercicio() );
                                $obFEmpenhoEmpenhoPagamentoAnulacao->setDado("cod_entidade" ,$obRNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() );
                                $obFEmpenhoEmpenhoPagamentoAnulacao->setDado("valor"        ,$obRNotaLiquidacao->getValorEstornado() );
                                if ($this->stObservacao) {
                                    $obFEmpenhoEmpenhoPagamentoAnulacao->setDado("complemento"  ,$obRNotaLiquidacao->roREmpenhoEmpenho->getCodEmpenho().'/'.$obRNotaLiquidacao->roREmpenhoEmpenho->getExercicio().' - '.$this->stObservacao  ) ;
                                    $stComplemento = $obRNotaLiquidacao->roREmpenhoEmpenho->getCodEmpenho().'/'.$obRNotaLiquidacao->roREmpenhoEmpenho->getExercicio().' - '.$this->stObservacao;
                                } else {
                                    $obFEmpenhoEmpenhoPagamentoAnulacao->setDado("complemento"  ,$obRNotaLiquidacao->roREmpenhoEmpenho->getCodEmpenho().'/'.$obRNotaLiquidacao->roREmpenhoEmpenho->getExercicio() ) ;
                                    $stComplemento = $obRNotaLiquidacao->roREmpenhoEmpenho->getCodEmpenho().'/'.$obRNotaLiquidacao->roREmpenhoEmpenho->getExercicio();
                                }

                                if ( Sessao::getExercicio() > '2012' ) {
                                    $stFiltroContaCredito = " WHERE liquidacao.cod_nota = ".$obRNotaLiquidacao->getCodNota()."
                                                                AND liquidacao.exercicio_liquidacao = '".$obRNotaLiquidacao->roREmpenhoEmpenho->getExercicio()."'
                                                                AND lancamento.tipo = 'E'
                                                                AND lancamento.sequencia = 2
                                                                AND lancamento.cod_entidade = ".$obRNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade();
                                    $obTContabilidadeLancamento = new TContabilidadeLancamento;
                                    $obTContabilidadeLancamento->recuperaLancamentoEmpenhoContaCredito( $rsContaCredito, $stFiltroContaCredito, $boTransacao );

                                    if ( stristr($rsContaCredito->getCampo('cod_estrutural_mascara'), '2.1.1.1') ) {
                                        $stCodEstruturalPagamento = $rsContaCredito->getCampo('cod_estrutural');
                                        $stCodPlanoCredito = $rsContaCredito->getCampo('cod_plano');
                                    } else {
                                        $stFiltroContaFixaCredito = " AND REPLACE(pc.cod_estrutural, '.', '') LIKE '213110100%' AND pc.exercicio = '".Sessao::getExercicio()."'";
                                        $obTContabilidadePlanoConta = new TContabilidadePlanoConta;
                                        $obTContabilidadePlanoConta->recuperaContaAnalitica( $rsContaFixaCredito, $stFiltroContaFixaCredito, '', $boTransacao );
                                        $stCodEstruturalPagamento = '213110100';
                                        $stCodPlanoCredito = $rsContaFixaCredito->getCampo('cod_plano');

                                        if ($stCodPlanoCredito == '' && Sessao::getExercicio() >= 2014) {
                                            $obTContabilidadeConfiguracaoLancamentoCredito = new TContabilidadeConfiguracaoLancamentoCredito;
                                            $stFiltroContaCreditoConfiguracao = " where clc.exercicio = '".Sessao::getExercicio()."'
                                                                                   and clc.cod_conta_despesa = ".$obRNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoClassificacaoDespesa->getCodConta()."
                                                                                   and clc.estorno = 'f'";
                                            $obTContabilidadeConfiguracaoLancamentoCredito->recuperaCodigoPlano($rsContaFixaCreditoConfiguracao, $stFiltroContaCreditoConfiguracao, '', $boTransacao);
                                            $stCodPlanoCredito = $rsContaFixaCreditoConfiguracao->getCampo('cod_plano');
                                        }
                                    }
                                    if ($stCodPlanoCredito == '') {
                                        $obErro->setDescricao('Configuração dos lançamentos de despesa não configurados para esta despesa.');
                                        break;
                                    }
                                    $obFEmpenhoEmpenhoPagamentoAnulacao->setDado("tcems"             , 'true' ) ;
                                } else {
                                    $stCodEstruturalPagamento = $obRNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoClassificacaoDespesa->getMascClassificacao();
                                }

                                $obFEmpenhoEmpenhoPagamentoAnulacao->setDado("nom_lote"     ,"Estorno de Pagamento do Empenho n° ".$obRNotaLiquidacao->roREmpenhoEmpenho->getCodEmpenho().'/'.$obRNotaLiquidacao->roREmpenhoEmpenho->getExercicio() ) ;
                                $obFEmpenhoEmpenhoPagamentoAnulacao->setDado("tipo_lote"    ,"P" ) ;
                                $obFEmpenhoEmpenhoPagamentoAnulacao->setDado("dt_lote"      ,$this->stDataAnulacao ) ;
                                $obFEmpenhoEmpenhoPagamentoAnulacao->setDado("cod_nota"     ,$obRNotaLiquidacao->getCodNota() );
                                $obFEmpenhoEmpenhoPagamentoAnulacao->setDado("conta_pagamento_financ", $this->obRContabilidadePlanoContaAnalitica->getCodEstrutural() ) ;
                                $obFEmpenhoEmpenhoPagamentoAnulacao->setDado("cod_estrutural", $stCodEstruturalPagamento ) ;
                                $obFEmpenhoEmpenhoPagamentoAnulacao->setDado("num_orgao"     ,$obRNotaLiquidacao->roREmpenhoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNumeroOrgao() ) ;
                                if ( Sessao::getExercicio() > '2012' ) {
                                    $obFEmpenhoEmpenhoPagamentoAnulacao->setDado("cod_plano_debito"  , $this->obRContabilidadePlanoContaAnalitica->getCodPlano() ) ;
                                    $obFEmpenhoEmpenhoPagamentoAnulacao->setDado("cod_plano_credito" , $stCodPlanoCredito ) ;
                                }
                                $obErro = $obFEmpenhoEmpenhoPagamentoAnulacao->executaFuncao( $rsFEmpenhoEmpenhoPagamentoAnulacao,'','', $boTransacao );
                                $inCodLote = $obFEmpenhoEmpenhoPagamentoAnulacao->getDado("cod_lote");
                                if (Sessao::getExercicio() > '2008') {
                                    if ( !$obErro->ocorreu() && $inCodPlanoUm != '' && $inCodPlanoDois != '') {
                                        include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeValorLancamento.class.php";

                                        $obTContabilidadeValorLancamento = new TContabilidadeValorLancamento;
                                        $obTContabilidadeValorLancamento->setDado("cod_lote", $inCodLote);
                                        $obTContabilidadeValorLancamento->setDado("tipo", 'P');
                                        $obTContabilidadeValorLancamento->setDado("exercicio", $obRNotaLiquidacao->getExercicio());
                                        $obTContabilidadeValorLancamento->setDado("cod_entidade", $obRNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() );
                                        $obTContabilidadeValorLancamento->setDado("cod_plano_deb", $inCodPlanoUm);
                                        $obTContabilidadeValorLancamento->setDado("cod_plano_cred", $inCodPlanoDois);
                                        $obTContabilidadeValorLancamento->setDado("cod_historico", 906);
                                        $obTContabilidadeValorLancamento->setDado("complemento", $stComplemento);
                                        $obTContabilidadeValorLancamento->setDado("vl_lancamento", $obRNotaLiquidacao->getValorEstornado());

                                        $obErro = $obTContabilidadeValorLancamento->inclusaoPorPl($rsRecordSet, $boTransacao);
                                        $inSequenciaEstorno = $rsRecordSet->getCampo('sequencia');
                                    } else {
                                        if (!$obErro->getDescricao()) {
                                            $obErro->setDescricao('Contas do compensado não estão cadastradas.');
                                        }
                                    }
                                }
                                $inCodHistoricoAdiantamento = 906;
                            } else {
                                $boRestos = true;
                                $obFEmpenhoEmpenhoPagamentoRestosAPagarAnulacao->setDado( 'exercicio'           , substr($obRNotaLiquidacao->stTimestamp,0,4) );
                                $obFEmpenhoEmpenhoPagamentoRestosAPagarAnulacao->setDado( 'valor'               , $obRNotaLiquidacao->getValorEstornado() );
                                $obFEmpenhoEmpenhoPagamentoRestosAPagarAnulacao->setDado( 'complemento'         , $obRNotaLiquidacao->roREmpenhoEmpenho->getCodEmpenho().'/'.$obRNotaLiquidacao->roREmpenhoEmpenho->getExercicio() );
                                $stComplemento = $obRNotaLiquidacao->roREmpenhoEmpenho->getCodEmpenho().'/'.$obRNotaLiquidacao->roREmpenhoEmpenho->getExercicio();
                                $obFEmpenhoEmpenhoPagamentoRestosAPagarAnulacao->setDado( 'nom_lote'            , "Estorno de Pagamento de RP n° ".$obRNotaLiquidacao->roREmpenhoEmpenho->getCodEmpenho().'/'.$obRNotaLiquidacao->roREmpenhoEmpenho->getExercicio() );
                                $obFEmpenhoEmpenhoPagamentoRestosAPagarAnulacao->setDado( 'tipo_lote'           , 'P' );
                                $obFEmpenhoEmpenhoPagamentoRestosAPagarAnulacao->setDado( 'cod_entidade'        , $obRNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() );
                                $obFEmpenhoEmpenhoPagamentoRestosAPagarAnulacao->setDado( 'cod_nota'            , $obRNotaLiquidacao->getCodNota() );
                                $obFEmpenhoEmpenhoPagamentoRestosAPagarAnulacao->setDado( 'conta_pg'            , $inCodEstrutural );
                                $obFEmpenhoEmpenhoPagamentoRestosAPagarAnulacao->setDado( 'exerc_rp'            , $obRNotaLiquidacao->roREmpenhoEmpenho->getExercicio() );
                                $obFEmpenhoEmpenhoPagamentoRestosAPagarAnulacao->setDado( 'exercicio_liquidacao', $obRNotaLiquidacao->getExercicio() );
                                $obFEmpenhoEmpenhoPagamentoRestosAPagarAnulacao->setDado("dt_lote"              ,$this->stDataAnulacao ) ;
                                $obErro = $obFEmpenhoEmpenhoPagamentoRestosAPagarAnulacao->executaFuncao( $rsFEmpenhoEmpenhoPagamentoAnulacao, '', '', $boTransacao );
                                if ($obErro->ocorreu()) {
                                    if (strstr($obErro->getDescricao(),"Não foi informado o tipo de Restos")) {
                                        $obErro->setDescricao("Impossível realizar os lançamentos. Verificar o atributo de Restos.");
                                    }
                                }
                                $inCodLote = $obFEmpenhoEmpenhoPagamentoRestosAPagarAnulacao->getDado( "cod_lote");
                                if (Sessao::getExercicio() > '2008') {
                                    if ( !$obErro->ocorreu() && $inCodPlanoUm != '' && $inCodPlanoDois != '') {
                                        include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeValorLancamento.class.php";

                                        $obTContabilidadeValorLancamento = new TContabilidadeValorLancamento;
                                        $obTContabilidadeValorLancamento->setDado("cod_lote", $inCodLote);
                                        $obTContabilidadeValorLancamento->setDado("tipo", 'P');
                                        $obTContabilidadeValorLancamento->setDado("exercicio", Sessao::getExercicio());
                                        $obTContabilidadeValorLancamento->setDado("cod_entidade", $obRNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() );
                                        $obTContabilidadeValorLancamento->setDado("cod_plano_deb", $inCodPlanoUm);
                                        $obTContabilidadeValorLancamento->setDado("cod_plano_cred", $inCodPlanoDois);
                                        $obTContabilidadeValorLancamento->setDado("cod_historico", 920);
                                        $obTContabilidadeValorLancamento->setDado("complemento", $stComplemento);
                                        $obTContabilidadeValorLancamento->setDado("vl_lancamento", $obRNotaLiquidacao->getValorEstornado());

                                        $obErro = $obTContabilidadeValorLancamento->inclusaoPorPl($rsRecordSet, $boTransacao);
                                        $inSequenciaEstorno = $rsRecordSet->getCampo('sequencia');
                                    } else {
                                        if (!$obErro->getDescricao()) {
                                            $obErro->setDescricao('Contas do compensado não estão cadastradas.');
                                        }
                                    }
                                }
                                $inCodHistoricoAdiantamento = 920;
                            }

                            if ( !$obErro->ocorreu() ) {
                                  $obFEmpenhoEmpenhoPagamentoAnulacao->setDado("cod_lote"             , "" );
                                  $obFEmpenhoEmpenhoPagamentoRestosAPagarAnulacao->setDado( "cod_lote", "" );
                                  $obTContabilidadeLancamentoEmpenho->setDado("cod_lote"    ,$inCodLote );
                                  $obTContabilidadeLancamentoEmpenho->setDado("tipo"        ,"P" );
                                  $inSequencia = $rsFEmpenhoEmpenhoPagamentoAnulacao->getCampo("sequencia") ;
                                  $obTContabilidadeLancamentoEmpenho->setDado("sequencia"   , $inSequencia );
                                  $obTContabilidadeLancamentoEmpenho->setDado("exercicio"   ,substr($obRNotaLiquidacao->stTimestamp,0,4) );
                                  $obTContabilidadeLancamentoEmpenho->setDado("cod_entidade",$obRNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() );
                                  $obTContabilidadeLancamentoEmpenho->setDado("estorno"     ,"true" ) ;
                                  $obErro = $obTContabilidadeLancamentoEmpenho->inclusao( $boTransacao );
                                  if ( !$obErro->ocorreu() ) {
                                      $obTContabilidadePagamento->setDado("exercicio"   , substr($obRNotaLiquidacao->stTimestamp,0,4) );
                                      $obTContabilidadePagamento->setDado("exercicio_liquidacao" ,$obRNotaLiquidacao->getExercicio() );
                                      $obTContabilidadePagamento->setDado("sequencia"   ,$inSequencia );
                                      $obTContabilidadePagamento->setDado("tipo"        ,"P" );
                                      $obTContabilidadePagamento->setDado("cod_lote"    ,$inCodLote );
                                      $obTContabilidadePagamento->setDado("cod_entidade",$obRNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() );
                                      $obTContabilidadePagamento->setDado("cod_nota"    ,$obRNotaLiquidacao->getCodNota() );
                                      $obTContabilidadePagamento->setDado("timestamp"   ,$obRNotaLiquidacao->stTimestamp ) ;
                                      $obErro = $obTContabilidadePagamento->inclusao( $boTransacao );
                                      if (!$obErro->ocorreu()) {
                                        $obTContabilidadePagamentoEstorno->setDado("exercicio"   , substr($obRNotaLiquidacao->stTimestamp,0,4) );
                                        $obTContabilidadePagamentoEstorno->setDado("exercicio_liquidacao" ,$obRNotaLiquidacao->getExercicio() );
                                        $obTContabilidadePagamentoEstorno->setDado("sequencia"   ,$inSequencia );
                                        $obTContabilidadePagamentoEstorno->setDado("tipo"        ,"P" );
                                        $obTContabilidadePagamentoEstorno->setDado("cod_lote"    ,$inCodLote );
                                        $obTContabilidadePagamentoEstorno->setDado("cod_entidade",$obRNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() );
                                        $obTContabilidadePagamentoEstorno->setDado("cod_nota"    ,$obRNotaLiquidacao->getCodNota() );
                                        $obTContabilidadePagamentoEstorno->setDado("timestamp"   ,$obRNotaLiquidacao->stTimestamp ) ;
                                        $obTContabilidadePagamentoEstorno->setDado("timestamp_anulada", $this->stTimestampAnulada );
                                        $obErro = $obTContabilidadePagamentoEstorno->inclusao( $boTransacao );

                                        /* Lança estorno de pagamento de retenção */
                                        if ($this->obREmpenhoOrdemPagamento->getRetencao() && !$obErro->ocorreu()) {
                                            include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamentoRetencao.class.php" );
                                            $obTContabilidadeLancamentoRetencao = new TContabilidadeLancamentoRetencao;
                                            foreach ($this->obREmpenhoOrdemPagamento->getRetencoes() as $arRetencao ) {
                                                if ($arRetencao['cod_plano'] == $obRNotaLiquidacao->inCodPlanoRetencao && !$obErro->ocorreu()) {
                                                    $obTContabilidadeLancamentoRetencao->setDado('cod_lote', $inCodLote );
                                                    $obTContabilidadeLancamentoRetencao->setDado('exercicio',substr($obRNotaLiquidacao->stTimestamp,0,4)  );
                                                    $obTContabilidadeLancamentoRetencao->setDado('tipo'     , 'P'       );
                                                    $obTContabilidadeLancamentoRetencao->setDado('sequencia', $inSequencia );
                                                    $obTContabilidadeLancamentoRetencao->setDado('cod_entidade', $obRNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade());
                                                    $obTContabilidadeLancamentoRetencao->setDado('cod_ordem', $this->obREmpenhoOrdemPagamento->getCodigoOrdem() );
                                                    $obTContabilidadeLancamentoRetencao->setDado('cod_plano', $arRetencao['cod_plano'] );
                                                    $obTContabilidadeLancamentoRetencao->setDado('exercicio_retencao', $arRetencao['exercicio'] );
                                                    $obTContabilidadeLancamentoRetencao->setDado('sequencial', $arRetencao['sequencial'] );
                                                    $obTContabilidadeLancamentoRetencao->setDado('estorno', 'true' );
                                                    $obErro = $obTContabilidadeLancamentoRetencao->inclusao( $boTransacao );
                                                }
                                            }
                                        }
                                     } // contabilidade.pagamento_estorno
                               } // contabilidade.pagamento
                            } // contabilidade.lancamento_empenho

                             /* ESTORNO ADIANTAMENTOS & SUBVENCOES */
                            if (!$obErro->ocorreu()) {
                                $codCategoria = $obRNotaLiquidacao->roREmpenhoEmpenho->getCodCategoria();
                                if ($codCategoria == 2 || $codCategoria == 3) {
                                    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeValorLancamento.class.php" );
                                    include_once( TEMP."TEmpenhoResponsavelAdiantamento.class.php");
                                    $obTEmpenhoResponsavelAdiantamento = new TEmpenhoResponsavelAdiantamento();
                                    $stFiltro = " WHERE exercicio = '".$obRNotaLiquidacao->getExercicio()."' AND numcgm = ".$obRNotaLiquidacao->roREmpenhoEmpenho->obRCGM->getNumCGM();
                                    $obErro = $obTEmpenhoResponsavelAdiantamento->recuperaTodos($rsContas,$stFiltro,'',$boTransacao);
                                    if (!$obErro->ocorreu()) {

                                        $stContaContrapartida   = $rsContas->getCampo('conta_contrapartida');
                                        $stContaLancamento      = $rsContas->getCampo('conta_lancamento');

                                        $obTContabilidadeValorLancamento    = new TContabilidadeValorLancamento;
                                        $obTContabilidadeValorLancamento->setDado( "cod_lote"      , $inCodLote                              );
                                        $obTContabilidadeValorLancamento->setDado( "tipo"          , 'P'                                     );
                                        $obTContabilidadeValorLancamento->setDado( "exercicio"     , $obRNotaLiquidacao->getExercicio()      );
                                        $obTContabilidadeValorLancamento->setDado( "cod_entidade"  , $obRNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() );
                                        $obTContabilidadeValorLancamento->setDado( "cod_plano_deb" , $stContaContrapartida                   );
                                        $obTContabilidadeValorLancamento->setDado( "cod_plano_cred", $stContaLancamento                      );
                                        $obTContabilidadeValorLancamento->setDado( "cod_historico" , $inCodHistoricoAdiantamento                                     );
                                        $obTContabilidadeValorLancamento->setDado( "complemento"   , $this->obREmpenhoOrdemPagamento->getCodigoOrdem()."/$arDataPagamento[2]" );
                                        $obTContabilidadeValorLancamento->setDado( "vl_lancamento" , $obRNotaLiquidacao->getValorEstornado() );

                                        $obErro = $obTContabilidadeValorLancamento->inclusaoPorPl( $rsRecordSet, $boTransacao   );
                                    }
                                }
                            }
                            /* ESTORNOS DE RETENÇÂO (Arrecadações Extra e Orçamentárias) se não estiver sendo feito via tesouraria */
                            if (!$this->getTesouraria() && !$obErro->ocorreu() && $this->obREmpenhoOrdemPagamento->getRetencao()) {
                                include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoOrdemPagamentoRetencao.class.php"  );
                                include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeValorLancamento.class.php" );
                                include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamentoRetencao.class.php" );
                                include_once(CAM_GA_ADM_MAPEAMENTO.'TAdministracaoConfiguracaoEntidade.class.php');
                                $inCodEntidade = $obRNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade();

                                $obTContabilidadeLancamentoRetencao = new TContabilidadeLancamentoRetencao;
                                $obTContabilidadeValorLancamento    = new TContabilidadeValorLancamento;
                                $obTEmpenhoOrdemPagamentoRetencao   = new TEmpenhoOrdemPagamentoRetencao;
                                $obTAdministracaoConfiguracaoEntidade = new TAdministracaoConfiguracaoEntidade;

                                $inCodEntidade = $obRNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade();
                                $stFiltroConta = " WHERE parametro = 'conta_caixa' AND cod_entidade = $inCodEntidade AND exercicio = '".substr($obRNotaLiquidacao->stTimestamp,0,4)."' ";
                                $obErro = $obTAdministracaoConfiguracaoEntidade->recuperaTodos($rsContas, $stFiltroConta, '', $boTransacao);
                                if (!$obErro->ocorreu() && !$rsContas->EOF() && $rsContas->getNumLinhas() == 1) {
                                    include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadePlanoContaAnalitica.class.php"     );
                                    $obContaAnalitica = new RContabilidadePlanoContaAnalitica;
                                    $obContaAnalitica->setCodPlano( $rsContas->getCampo('valor') );
                                    $obContaAnalitica->setExercicio( substr($obRNotaLiquidacao->stTimestamp,0,4) );
                                    $obErro = $obContaAnalitica->consultar( $boTransacao );
                                    $stCodEstruturalCaixa = $obContaAnalitica->getCodEstrutural();
                                    $inCodPlanoCaixa = $obContaAnalitica->getCodPlano();
                                }
                                if ($stCodEstruturalCaixa && $inCodPlanoCaixa && !$obErro->ocorreu()) {
                                    foreach ( $this->obREmpenhoOrdemPagamento->getRetencoes() as $arRetencao ) {
                                        if (!$obErro->ocorreu() ) {
                                            $obTContabilidadeLancamentoRetencao->setDado( "cod_lote"      , '' );
                                            $obTContabilidadeLancamentoRetencao->setDado( "tipo"          , $arRetencao['tipo'] == 'O' ? 'A' : 'T' );
                                            $obTContabilidadeLancamentoRetencao->setDado( "nom_lote"      , $arRetencao['tipo'] == 'O' ? "Estorno de Arrecadação por Retenção Orçamentária - OP ".$this->obREmpenhoOrdemPagamento->getCodigoOrdem()."/".substr($obRNotaLiquidacao->stTimestamp,0,4) : "Transferência - CD:".$arRetencao['cod_plano']." | CC:".$inCodPlanoCaixa );
                                            $obTContabilidadeLancamentoRetencao->setDado( "dt_lote"       , $this->stDataAnulacao );
                                            $obTContabilidadeLancamentoRetencao->setDado( "exercicio"     , substr($obRNotaLiquidacao->stTimestamp,0,4) );
                                            $obTContabilidadeLancamentoRetencao->setDado( "cod_entidade"  , $inCodEntidade );
                                            $obTContabilidadeLancamentoRetencao->setDado( "sequencial"    , $arRetencao['sequencial'] );
                                            $obErro = $obTContabilidadeLancamentoRetencao->insereLote( $inCodLoteArrecadacao, $boTransacao );
                                            if ($arRetencao['tipo'] == 'O') { // Retenção Receita Orçamentária
                                                 if (!$obErro->ocorreu()) {
                                                     include_once( CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoReceita.class.php"        );
                                                     $obRContabilidadeLancamentoReceita = new RContabilidadeLancamentoReceita;
                                                     $obRContabilidadeLancamentoReceita->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $inCodEntidade );
                                                     $obRContabilidadeLancamentoReceita->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote( $inCodLoteArrecadacao );
                                                     $obRContabilidadeLancamentoReceita->obRContabilidadeLancamento->obRContabilidadeLote->setNomLote( "Estorno de Arrecadação por Retenção ".($arRetencao['tipo'] == 'O' ? 'Orçamentária' : 'Extra-Orçamentária')." - OP ".$this->obREmpenhoOrdemPagamento->getCodigoOrdem()."/".substr($obRNotaLiquidacao->stTimestamp,0,4) );
                                                     $obRContabilidadeLancamentoReceita->obRContabilidadeLancamento->obRContabilidadeLote->setDtLote( $this->stDataAnulacao );
                                                     $obRContabilidadeLancamentoReceita->setContaDebito( $inCodPlanoCaixa );
                                                     $obRContabilidadeLancamentoReceita->obRContabilidadeLancamento->obRContabilidadeHistoricoPadrao->setCodHistorico( 951 );
                                                     $obRContabilidadeLancamentoReceita->obRContabilidadeLancamento->setBoComplemento( true );
                                                     $obRContabilidadeLancamentoReceita->obRContabilidadeLancamento->setComplemento( $this->obREmpenhoOrdemPagamento->getCodigoOrdem()."/".substr($obRNotaLiquidacao->stTimestamp,0,4) );
                                                     $obRContabilidadeLancamentoReceita->obROrcamentoReceita->setCodReceita( $arRetencao['cod_receita'] );
                                                     $obRContabilidadeLancamentoReceita->setValor(  $arRetencao['vl_retencao'] );
                                                     $obRContabilidadeLancamentoReceita->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio( $arRetencao['exercicio'] );
                                                     $obRContabilidadeLancamentoReceita->setEstorno ( true );
                                                     $obErro = $obRContabilidadeLancamentoReceita->alterar( $boTransacao, true );
                                                     if (!$obErro->ocorreu()) {
                                                         $inSequenciaRet = $obRContabilidadeLancamentoReceita->obRContabilidadeLancamento->getSequencia();
                                                     }
                                                 }
                                            } elseif ($arRetencao['tipo'] == 'E') { // Extra-Orçamentária
                                                 if (!$obErro->ocorreu()) {
                                                     $obTContabilidadeValorLancamento->setDado( "cod_lote"      , $inCodLoteArrecadacao                   );
                                                     $obTContabilidadeValorLancamento->setDado( "tipo"          , 'T'                                     );
                                                     $obTContabilidadeValorLancamento->setDado( "exercicio"     , substr($obRNotaLiquidacao->stTimestamp,0,4) );
                                                     $obTContabilidadeValorLancamento->setDado( "cod_entidade"  , $inCodEntidade                          );
                                                     $obTContabilidadeValorLancamento->setDado( "cod_plano_deb" , $arRetencao['cod_plano']                );
                                                     $obTContabilidadeValorLancamento->setDado( "cod_plano_cred", $inCodPlanoCaixa );
                                                     $obTContabilidadeValorLancamento->setDado( "cod_historico" , 953                                     );
                                                     $obTContabilidadeValorLancamento->setDado( "complemento"   , $this->obREmpenhoOrdemPagamento->getCodigoOrdem()."/".substr($obRNotaLiquidacao->stTimestamp,0,4) );
                                                     $obTContabilidadeValorLancamento->setDado( "vl_lancamento" , $arRetencao['vl_retencao'] );
                                                     $obErro = $obTContabilidadeValorLancamento->inclusaoPorPl( $rsRecordSet, $boTransacao   );
                                                     if (!$obErro->ocorreu()) {
                                                         $inSequenciaRet = $rsRecordSet->getCampo('sequencia');
                                                     }
                                                 }
                                                 if (Sessao::getExercicio() > '2008') {
                                                    $obRContabilidadePlanoBanco->setCodPlano($arRetencao['cod_plano']);
                                                    $obErro = $obRContabilidadePlanoBanco->getRecursoVinculoConta($rsCodRecurso, $boTransacao);
                                                    $inCodRecursoRet = $rsCodRecurso->getCampo('cod_recurso');

                                                    $boDestinacao = false;
                                                    $obTOrcamentoConfiguracao = new TOrcamentoConfiguracao;
                                                    $obTOrcamentoConfiguracao->setDado("exercicio", Sessao::getExercicio() );
                                                    $obTOrcamentoConfiguracao->setDado("parametro","recurso_destinacao");
                                                    $obTOrcamentoConfiguracao->consultar($boTransacao);
                                                    if($obTOrcamentoConfiguracao->getDado("valor") == 'true')
                                                        $boDestinacao = true;

                                                    if ($boDestinacao && $inCodRecursoRet != '') {
                                                        $obTOrcamentoRecursoDestinacao = new TOrcamentoRecursoDestinacao;
                                                        $obTOrcamentoRecursoDestinacao->setDado("exercicio", Sessao::getExercicio());

                                                        $stFiltro  = ' WHERE recurso_destinacao.cod_recurso = '.$inCodRecursoRet;
                                                        $stFiltro .= '   AND recurso_destinacao.exercicio = '.Sessao::getExercicio();
                                                        $obErro = $obTOrcamentoRecursoDestinacao->recuperaTodos($rsDestinacao, $stFiltro, '', $boTransacao);
                                                        $inCodEspecificacao = $rsDestinacao->getCampo('cod_especificacao');

                                                        // Verifica qual o cod_recurso que possui conta contabil vinculada
                                                        $obTOrcamentoRecursoDestinacao->setDado('exercicio', Sessao::getExercicio());
                                                        $obTOrcamentoRecursoDestinacao->setDado('cod_especificacao', $inCodEspecificacao);
                                                        if ( Sessao::getExercicio() > '2012' ) {
                                                            $obTOrcamentoRecursoDestinacao->setDado('cod_estrutural', "'8.2.1.1.3.%'");
                                                        } else {
                                                            $obTOrcamentoRecursoDestinacao->setDado('cod_estrutural', "'2.9.3.2.0.00.00.%'");
                                                        }
                                                        $obTOrcamentoRecursoDestinacao->recuperaRecursoVinculoConta($rsContaRecurso, '', '', $boTransacao);

                                                        $inCodRecursoRet = $rsContaRecurso->getCampo('cod_recurso');
                                                    }

                                                    if (!$obErro->ocorreu() && $inCodRecursoRet != '') {
                                                        $obRContabilidadePlanoBanco->obROrcamentoRecurso->setCodRecurso($inCodRecursoRet);
                                                        $obErro = $obRContabilidadePlanoBanco->getContasRecursoPagamentoTCEMS($rsContasRecursoRet, $boTransacao);
                                                        $inCodPlanoRetUm = $rsContasRecursoRet->getCampo('cod_plano_um');
                                                        $inCodPlanoRetDois = $rsContasRecursoRet->getCampo('cod_plano_dois');

                                                        if ($inCodPlanoRetUm != '' && $inCodPlanoRetDois != '') {
                                                            include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeValorLancamento.class.php";

                                                            $obTContabilidadeValorLancamento = new TContabilidadeValorLancamento;
                                                            $obTContabilidadeValorLancamento->setDado("cod_lote", $inCodLoteArrecadacao);
                                                            $obTContabilidadeValorLancamento->setDado("tipo", 'T');
                                                            $obTContabilidadeValorLancamento->setDado("exercicio", substr($obRNotaLiquidacao->stTimestamp,0,4));
                                                            $obTContabilidadeValorLancamento->setDado("cod_entidade", $inCodEntidade);
                                                            $obTContabilidadeValorLancamento->setDado("cod_plano_deb", $inCodPlanoRetDois);
                                                            $obTContabilidadeValorLancamento->setDado("cod_plano_cred", $inCodPlanoRetUm);
                                                            $obTContabilidadeValorLancamento->setDado("cod_historico", 953);
                                                            $obTContabilidadeValorLancamento->setDado("complemento", $this->obREmpenhoOrdemPagamento->getCodigoOrdem()."/".substr($obRNotaLiquidacao->stTimestamp,0,4));
                                                            $obTContabilidadeValorLancamento->setDado("vl_lancamento", $arRetencao['vl_retencao']);

                                                            $obErro = $obTContabilidadeValorLancamento->inclusaoPorPl($rsRecordSet, $boTransacao);
                                                            $inSequenciaRet = $rsRecordSet->getCampo('sequencia');
                                                        }

                                                    }
                                                }
                                            }
                                            if (!$obErro->ocorreu()) {
                                                $obTContabilidadeLancamentoRetencao->setDado('tipo', $arRetencao['tipo'] == 'O' ? 'A' : 'T' );
                                                $obTContabilidadeLancamentoRetencao->setDado('exercicio', substr($obRNotaLiquidacao->stTimestamp,0,4) );
                                                $obTContabilidadeLancamentoRetencao->setDado('cod_lote', $inCodLoteArrecadacao );
                                                $obTContabilidadeLancamentoRetencao->setDado('sequencia', $inSequenciaRet );
                                                $obTContabilidadeLancamentoRetencao->setDado('cod_ordem', $this->obREmpenhoOrdemPagamento->getCodigoOrdem() );
                                                $obTContabilidadeLancamentoRetencao->setDado('cod_plano', $arRetencao['cod_plano'] );
                                                $obTContabilidadeLancamentoRetencao->setDado('exercicio_retencao', $arRetencao['exercicio'] );
                                                $obTContabilidadeLancamentoRetencao->setDado('estorno', 'true' );
                                                $obTContabilidadeLancamentoRetencao->setDado('sequencial', $arRetencao['sequencial'] );
                                                $obErro = $obTContabilidadeLancamentoRetencao->inclusao( $boTransacao );
                                            }
                                        } // Fim se não utiliza tesouraria
                                    } // fim foreach nas retenções da OP
                                } else {
                                    $obErro->setDescricao('Uma conta de Caixa deve ser configurada para esta Entidade.');
                                }// fim verificação da configuração da conta de caixa para a entidade da nota.
                            } // Fim Estorno Retenção
                        } // Fim se o valor a estornar for maior que zero
                    } // Fim foreach em todas notas da OP
                } else {
                    $obErro->setDescricao( 'A data da anulação deve ser igual ou superior à data mais recente de pagamento ('.$this->stDataPagamento.').' );
                }
            } else {
                $obErro->setDescricao( 'ERRO: Este pagamento já foi estornado.' );
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTEmpenhoNotaLiquidacaoPagaAnulada );
    $this->obREmpenhoOrdemPagamento->setEstorno( false );

    return $obErro;
}

/**
    * Pagar Nota da Liquidação Cfe OP.
    * Efetua a baixa e gera o lançamento contábil no URBEM cfe pagamentos no siam.
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
//
function pagar($boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO.   "TEmpenhoPagamentoLiquidacao.class.php"                    );
    include_once ( CAM_GF_EMP_MAPEAMENTO.   "TEmpenhoPagamentoLiquidacaoNotaLiquidacaoPaga.class.php"  );
    include_once ( CAM_GF_EMP_MAPEAMENTO.   "TEmpenhoNotaLiquidacao.class.php"                         );
    include_once ( CAM_GF_EMP_MAPEAMENTO.   "TEmpenhoNotaLiquidacaoPaga.class.php"                     );
    include_once ( CAM_GF_EMP_MAPEAMENTO.   "TEmpenhoNotaLiquidacaoContaPagadora.class.php"            );
    include_once ( CAM_GF_EMP_MAPEAMENTO.   "TEmpenhoNotaLiquidacaoPagaAnulada.class.php"              );
    include_once ( CAM_GF_EMP_MAPEAMENTO.   "TEmpenhoRestosPreEmpenho.class.php"                       );
    include_once ( CAM_GF_CONT_MAPEAMENTO.   "TContabilidadeLancamentoEmpenho.class.php"                );
    include_once ( CAM_GF_CONT_MAPEAMENTO.   "TContabilidadePagamento.class.php"                        );
    include_once ( CAM_GF_EMP_MAPEAMENTO.   "FEmpenhoEmpenhoPagamento.class.php"                       );
    include_once ( CAM_GF_EMP_MAPEAMENTO.   "FEmpenhoEmpenhoPagamentoAnulacao.class.php"               );
    include_once ( CAM_GF_EMP_MAPEAMENTO.   "FEmpenhoEmpenhoPagamentoRestosAPagar.class.php"           );
    include_once ( CAM_GF_EMP_MAPEAMENTO.   "FEmpenhoEmpenhoPagamentoRestosAPagarAnulacao.class.php"   );
    include_once ( CAM_GF_EMP_MAPEAMENTO.   "TEmpenhoPreEmpenhoDespesa.class.php"                       );
    include_once ( CAM_GF_ORC_MAPEAMENTO.   "TOrcamentoDespesa.class.php"                              );
    include_once ( CAM_GF_ORC_MAPEAMENTO.   "TOrcamentoContaDespesa.class.php"                         );
    $obTOrcamentoContaDespesa                         =  new TOrcamentoContaDespesa;
    $obTOrcamentoDespesa                              =  new TOrcamentoDespesa;
    $obFEmpenhoEmpenhoPagamentoRestosAPagarAnulacao   =  new FEmpenhoEmpenhoPagamentoRestosAPagarAnulacao;
    $obFEmpenhoEmpenhoPagamentoRestosAPagar           =  new FEmpenhoEmpenhoPagamentoRestosAPagar;
    $obFEmpenhoEmpenhoPagamentoAnulacao               =  new FEmpenhoEmpenhoPagamentoAnulacao;
    $obFEmpenhoEmpenhoPagamento                       =  new FEmpenhoEmpenhoPagamento;
    $obTContabilidadePagamento                        =  new TContabilidadePagamento;
    $obTContabilidadeLancamentoEmpenho                =  new TContabilidadeLancamentoEmpenho;
    $obTEmpenhoRestosPreEmpenho                       =  new TEmpenhoRestosPreEmpenho;
    $obTEmpenhoNotaLiquidacaoPagaAnulada              =  new TEmpenhoNotaLiquidacaoPagaAnulada;
    $obTEmpenhoNotaLiquidacaoPaga                     =  new TEmpenhoNotaLiquidacaoPaga;
    $obTEmpenhoNotaLiquidacaoContaPagadora            =  new TEmpenhoNotaLiquidacaoContaPagadora;
    $obTEmpenhoNotaLiquidacao                         =  new TEmpenhoNotaLiquidacao;
    $obTEmpenhoPagamentoLiquidacaoNotaLiquidacaoPaga  =  new TEmpenhoPagamentoLiquidacaoNotaLiquidacaoPaga;
    $obTEmpenhoPagamentoLiquidacao                    =  new TEmpenhoPagamentoLiquidacao;
    $obTEmpenhoPreEmpenhoDespesa                      =  new TEmpenhoPreEmpenhoDespesa;

    $rsListaAutenticacoes = new recordset ;

    // TRANSACAO GERAL
    //$boFlagTransacao = false ;
    //$obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao);
    //

    // Formata o sequencial de autenticações com 7 digitos para ser usado como seguntos no timestamp
    $obErro = $this->listarAutenticacoes($rsListaAutenticacoes, 'entidade, empen, valor desc', $boTransacao );

    $this->boLogErros = false ;

    if ( !$rsListaAutenticacoes->eof() && !$obErro->ocorreu()) {

        while ( !$rsListaAutenticacoes->eof() ) {
               //
               //echo "<br><b>".$rsListaAutenticacoes->getCampo("empen")."</b><br>";
               //
               // Pra cada Autenticacao do siam, lista as notas de liquidacao e verifica se ja foram processadas
               // se ainda nao foram, efetua o pagamento/anulacao.
               //
               $boFlagTransacao = false;
               $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao);
               if( $obErro->ocorreu())
                   break ;

               $inCodOrdemAutent    = (int) (substr($rsListaAutenticacoes->getCampo("empen"),0,6));
               $stExercicioAutent   = substr($rsListaAutenticacoes->getCampo( "data"),6,4);
               $inCodEntidadeAutent = (int) ($rsListaAutenticacoes->getCampo( "entidade"));
               $nuValorAutenticado  = $rsListaAutenticacoes->getCampo( "valor");
               //
               // Gera timestamp da seguinte forma:
               // k12_data + k12_hora + abs((2 ultimos digitos do k12_autent)-60 )
               //
               $stAnoAutent         = substr($rsListaAutenticacoes->getCampo( "data"),6,4);
               $stMesAutent         = substr($rsListaAutenticacoes->getCampo( "data"),3,2);
               $stDiaAutent         = substr($rsListaAutenticacoes->getCampo( "data"),0,2);
               $stDataAutent        = $stDiaAutent.'/'.$stMesAutent.'/'.$stAnoAutent ;
               $stHoraAutent        = substr($rsListaAutenticacoes->getCampo( "hora"),0,2);
               $stMinutoAutent      = substr($rsListaAutenticacoes->getCampo( "hora"),3,2);
               $stSegAutent         = $rsListaAutenticacoes->getCampo( "autent");
               $stSegAutent         = substr(str_pad(abs($stSegAutent - 60),7,"0",STR_PAD_LEFT),5,2);
               //
               // ATENÇÃO : NÃO PODE MUDAR A REGRA DO STTIMESTAMPAUTENT, SENÃO VAI DUPLICAR REGISTROS NA BASE !!!
               //
               $stTimestampAutent   = $stAnoAutent."-".$stMesAutent."-".$stDiaAutent." ".$stHoraAutent.':'.$stMinutoAutent.':'.$stSegAutent.'.00';

               $inContaAutent       = (int) ($rsListaAutenticacoes->getCampo("conta")) ;
               // Tem que recuperar a conta do plano a partir da conta da tesouraria.
               $this->setContaTesouraria( $inContaAutent ) ;
               $obErro = $this->listarContasTesouraria($rsListaContasTesouraria, 'conta', $boTransacao );
               if ( !$obErro->ocorreu() ) {
                   if ( !$rsListaContasTesouraria->eof() and $rsListaContasTesouraria->getCampo("plano") ) {
                       //
                       $inContaPlano = $rsListaContasTesouraria->getCampo("plano") ;
                       //
                       $this->obRContabilidadePlanoContaAnalitica->setCodConta("");
                       $this->obRContabilidadePlanoContaAnalitica->setCodEstrutural("");
                       $this->obRContabilidadePlanoContaAnalitica->setExercicio( $stAnoAutent );
                       $this->obRContabilidadePlanoContaAnalitica->setCodPlano( $inContaPlano );
                       $obErro = $this->obRContabilidadePlanoContaAnalitica->consultar ( $boTransacao );
                       //
                       if ( !$obErro->ocorreu() and $this->obRContabilidadePlanoContaAnalitica->getCodConta() ) {

                           $stEstruturalPLano = str_replace(".","",$this->obRContabilidadePlanoContaAnalitica->getCodEstrutural());
                           $obTEmpenhoPagamentoLiquidacao->setDado("cod_ordem"   , $inCodOrdemAutent    ) ;
                           $obTEmpenhoPagamentoLiquidacao->setDado("exercicio"   , $stExercicioAutent   ) ;
                           $obTEmpenhoPagamentoLiquidacao->setDado("cod_entidade", $inCodEntidadeAutent ) ;

                           $obErro = $obTEmpenhoPagamentoLiquidacao->recuperaValorOrdem($rsRecuperaValorOrdem, $boTransacao );

                           if ( !$obErro->ocorreu() ) {
                               $nuValorTotalPagamentoLiquidacao = $rsRecuperaValorOrdem->getCampo( "valor_ordem" );

                               if ($nuValorAutenticado < 0) {
                                  $boAnulacao = true ;
                               } else {
                                  $boAnulacao = False ;
                               }

                               $nuValorAbsolutoAutenticado = abs($nuValorAutenticado) ;

                               if ($nuValorAbsolutoAutenticado == $nuValorTotalPagamentoLiquidacao) {

                                   $stFiltro  = " WHERE ";
                                   $stFiltro .= " EPL.COD_ORDEM    = ".$inCodOrdemAutent."    AND ";
                                   $stFiltro .= " EPL.COD_ENTIDADE = ".$inCodEntidadeAutent." AND ";
                                   $stFiltro .= " EPL.EXERCICIO    = ".$stExercicioAutent ;

                                   $rsPagamentoLiquidacao = new recordset ;
                                   $obErro = $obTEmpenhoPagamentoLiquidacao->recuperaPagamentoLiquidacao($rsPagamentoLiquidacao, $stFiltro, $boTransacao );

                                   if ( !$obErro->ocorreu() ) {
                                       while ( !$rsPagamentoLiquidacao->eof() ) {
                                              $inCodNota             = $rsPagamentoLiquidacao->getCampo("cod_nota") ;
                                              $stExercicioLiquidacao = $rsPagamentoLiquidacao->getCampo("exercicio_liquidacao") ;
                                              $this->obREmpenhoNotaLiquidacao->setExercicio( $stExercicioLiquidacao ) ;
                                              $this->obREmpenhoNotaLiquidacao->setCodNota( $inCodNota ) ;
                                              $this->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade($inCodEntidadeAutent) ;
                                              $this->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->setCodEmpenho("");
                                              $obErro = $this->obREmpenhoNotaLiquidacao->consultar( $boTransacao ) ;
                                              if ( !$obErro->ocorreu() and $this->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->getCodEmpenho() ) {
                                                  $obTEmpenhoPagamentoLiquidacaoNotaLiquidacaoPaga->setDado("cod_nota"             ,$inCodNota );
                                                  $obTEmpenhoPagamentoLiquidacaoNotaLiquidacaoPaga->setDado("exercicio_liquidacao" ,$stExercicioLiquidacao );
                                                  $obTEmpenhoPagamentoLiquidacaoNotaLiquidacaoPaga->setDado("cod_entidade"         ,$inCodEntidadeAutent );
                                                  $obTEmpenhoPagamentoLiquidacaoNotaLiquidacaoPaga->setDado("exercicio"            ,$stExercicioAutent );
                                                  $obTEmpenhoPagamentoLiquidacaoNotaLiquidacaoPaga->setDado("cod_ordem"            ,$inCodOrdemAutent );
                                                  $obTEmpenhoPagamentoLiquidacaoNotaLiquidacaoPaga->setDado("timestamp"            ,$stTimestampAutent );
                                                  //
                                                  $obErro = $obTEmpenhoPagamentoLiquidacaoNotaLiquidacaoPaga->recuperaPorChave( $rsPagamentoLiquidacaoNotaLiquidacaoPaga, $boTransacao );

                                                  if ( !$obErro->ocorreu() and !$boAnulacao and $rsPagamentoLiquidacaoNotaLiquidacaoPaga->eof() ) {

                                                      $obTEmpenhoNotaLiquidacaoPaga->setDado("cod_nota"     ,$inCodNota );
                                                      $obTEmpenhoNotaLiquidacaoPaga->setDado("cod_entidade" ,$inCodEntidadeAutent );
                                                      $obTEmpenhoNotaLiquidacaoPaga->setDado("exercicio"    ,$stExercicioLiquidacao );
                                                      $obTEmpenhoNotaLiquidacaoPaga->setDado("timestamp"    ,$stTimestampAutent );
                                                      $obTEmpenhoNotaLiquidacaoPaga->setDado("vl_pago"      ,$rsPagamentoLiquidacao->getCampo("vl_pagamento") );

                                                      $obErro = $obTEmpenhoNotaLiquidacaoPaga->recuperaPorChave( $rsNotaLiquidacaoPaga, $boTransacao );

                                                      if ( !$obErro->ocorreu() and $rsNotaLiquidacaoPaga->eof() ) {
                                                          $obErro = $obTEmpenhoNotaLiquidacaoPaga->inclusao( $boTransacao );
                                                            if (!$obErro->ocorreu()) {
                                                                $obTEmpenhoNotaLiquidacaoContaPagadora->setDado("cod_nota"     ,$inCodNota );
                                                                $obTEmpenhoNotaLiquidacaoContaPagadora->setDado("cod_entidade" ,$inCodEntidadeAutent );
                                                                $obTEmpenhoNotaLiquidacaoContaPagadora->setDado("exercicio_liquidacao"    ,$stExercicioLiquidacao );
                                                                $obTEmpenhoNotaLiquidacaoContaPagadora->setDado("timestamp"    ,$stTimestampAutent );
                                                                $obTEmpenhoNotaLiquidacaoContaPagadora->setDado("exercicio"    ,$this->obRContabilidadePlanoContaAnalitica->getExercicio() );
                                                                $obTEmpenhoNotaLiquidacaoContaPagadora->setDado("cod_plano"    ,$this->obRContabilidadePlanoContaAnalitica->getCodPlano() );

                                                                $obErro = $obTEmpenhoNotaLiquidacaoContaPagadora->inclusao($boTransacao);

                                                                if ( !$obErro->ocorreu() ) {
                                                                    $obTEmpenhoPagamentoLiquidacaoNotaLiquidacaoPaga->setDado("timestamp",$stTimestampAutent );
                                                                    $obErro = $obTEmpenhoPagamentoLiquidacaoNotaLiquidacaoPaga->inclusao( $boTransacao );
                                                                    if ( !$obErro->ocorreu() ) {
                                                                        // Lancamento contábil de pagamento
                                                                        if ( !$obErro->ocorreu() and $this->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->getCodEmpenho() ) {
                                                                            $inCodEmpenho       = $this->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->getCodEmpenho();
                                                                            $stExercicioEmpenho = $this->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->getExercicio();
                                                                            $inCodEmpenho       = $this->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->getCodEmpenho() ;
                                                                            $obErro = $this->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->checarImplantado( $boImplantado, $boTransacao  );
                                                                            $stClasDespesa = "";
                                                                            $inNumOrgao = 0 ;
                                                                            if ( !$obErro->ocorreu() ) {
                                                                                if (!$boImplantado) {
                                                                                    $obTEmpenhoPreEmpenhoDespesa->setDado("cod_pre_empenho", $this->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->getCodPreEmpenho() );
                                                                                    $obTEmpenhoPreEmpenhoDespesa->setDado("exercicio"      , $stExercicioEmpenho );
                                                                                    $obErro = $obTEmpenhoPreEmpenhoDespesa->recuperaPorChave( $rsEmpenhoPreEmpenhoDespesa, $boTransacao );
                                                                                    if ( !$obErro->ocorreu() and  !$rsEmpenhoPreEmpenhoDespesa->eof() ) {
                                                                                        $obTOrcamentoDespesa->setDado("cod_despesa", $rsEmpenhoPreEmpenhoDespesa->getCampo("cod_despesa"));
                                                                                        $obTOrcamentoDespesa->setDado("exercicio", $stExercicioEmpenho);
                                                                                        $obErro = $obTOrcamentoDespesa->recuperaPorChave( $rsOrcamentoDespesa, $boTransacao ) ;
                                                                                        if ( !$obErro->ocorreu() and !$rsOrcamentoDespesa->eof() ) {
                                                                                            $inNumOrgao = $rsOrcamentoDespesa->getCampo("num_orgao");
                                                                                        } else {
                                                                                            $stLogObs = "Entidade: ".$inCodEntidadeAutent.", OP: ".$inCodOrdemAutent.", erro ao recuperar o orgão do empenho ".$inCodEmpenho."/".$stExercicioEmpenho." no URBEM.";
                                                                                            $this->logLinha( $stLogObs ) ;
                                                                                            break;
                                                                                        }
                                                                                    } else {
                                                                                        $stLogObs = "Entidade: ".$inCodEntidadeAutent.", OP: ".$inCodOrdemAutent.", erro ao recuperar a despesa do empenho ".$inCodEmpenho."/".$stExercicioEmpenho." no URBEM.";
                                                                                        $this->logLinha( $stLogObs ) ;
                                                                                        break;
                                                                                    }
                                                                                    $obTOrcamentoContaDespesa->setDado("cod_conta", $rsEmpenhoPreEmpenhoDespesa->getCampo("cod_conta"));
                                                                                    $obTOrcamentoContaDespesa->setDado("exercicio", $stExercicioEmpenho);
                                                                                    $obErro = $obTOrcamentoContaDespesa->recuperaPorChave( $rsOrcamentoContaDespesa, $boTransacao ) ;
                                                                                    if ( !$obErro->ocorreu() and !$rsOrcamentoContaDespesa->eof() ) {
                                                                                        $stClasDespesa = str_replace(".","",$rsOrcamentoContaDespesa->getCampo("cod_estrutural"));
                                                                                    } else {
                                                                                        $stLogObs = "Entidade: ".$inCodEntidadeAutent.", OP: ".$inCodOrdemAutent.", erro ao recuperar a classificação de despesa do empenho ".$inCodEmpenho."/".$stExercicioEmpenho." no URBEM.";
                                                                                        $this->logLinha( $stLogObs ) ;
                                                                                        break;
                                                                                    }
                                                                                } else {
                                                                                    $obTEmpenhoRestosPreEmpenho->setDado("cod_pre_empenho", $this->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->inCodPreEmpenho );
                                                                                    $obTEmpenhoRestosPreEmpenho->setDado("exercicio"      , $stExercicioEmpenho );
                                                                                    $obErro = $obTEmpenhoRestosPreEmpenho->recuperaPorChave( $rsEmpenhoRestosPreEmpenho, $boTransacao );
                                                                                    if ( !$obErro->ocorreu() and !$rsEmpenhoRestosPreEmpenho->eof() ) {
                                                                                        $inNumOrgao = $rsEmpenhoRestosPreEmpenho->getCampo("num_orgao") ;
                                                                                    } else {
                                                                                        $stLogObs = "Entidade: ".$inCodEntidadeAutent.", OP: ".$inCodOrdemAutent.", erro ao recuperar o orgão do empenho ".$inCodEmpenho."/".$stExercicioEmpenho." no URBEM.";
                                                                                        $this->logLinha( $stLogObs ) ;
                                                                                        break;
                                                                                    }
                                                                                    if ( !$obErro->ocorreu() and !$rsEmpenhoRestosPreEmpenho->eof() ) {
                                                                                        $stClasDespesa = $rsEmpenhoRestosPreEmpenho->getCampo("cod_estrutural") ;
                                                                                    } else {
                                                                                        $stLogObs = "Entidade: ".$inCodEntidadeAutent.", OP: ".$inCodOrdemAutent.", erro ao recuperar a classificação de despesa do empenho ".$inCodEmpenho."/".$stExercicioEmpenho." no URBEM.";
                                                                                        $this->logLinha( $stLogObs ) ;
                                                                                        break;
                                                                                    }
                                                                                }
                                                                            }
                                                                            // Faz cfe exercicio, se exercicio da autenticacao for diferente do execicio do empenho, é Restos a Pagar.
                                                                            if ($stExercicioAutent == $stExercicioEmpenho) {
                                                                                // Quando não é RP
                                                                                $obFEmpenhoEmpenhoPagamento->setDado("exercicio"    ,$stExercicioAutent );
                                                                                $obFEmpenhoEmpenhoPagamento->setDado("exercicio_liquidacao" ,$stExercicioLiquidacao );
                                                                                $obFEmpenhoEmpenhoPagamento->setDado("cod_entidade" ,$inCodEntidadeAutent );
                                                                                $obFEmpenhoEmpenhoPagamento->setDado("valor"        ,$rsPagamentoLiquidacao->getCampo("vl_pagamento") ) ;
                                                                                $obFEmpenhoEmpenhoPagamento->setDado("nom_lote"     ,"Pagamento de Empenho n° ". $this->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->getCodEmpenho() .'/'.$this->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->getExercicio() ) ;
                                                                                $obFEmpenhoEmpenhoPagamento->setDado("complemento"  ,$inCodEmpenho.'/'.$stExercicioEmpenho ) ;
                                                                                $obFEmpenhoEmpenhoPagamento->setDado("tipo_lote"    ,"P" ) ;
                                                                                $obFEmpenhoEmpenhoPagamento->setDado("dt_lote"      ,$stDataAutent ) ;
                                                                                $obFEmpenhoEmpenhoPagamento->setDado("cod_nota"     ,$inCodNota );
                                                                                $obFEmpenhoEmpenhoPagamento->setDado("conta_pagamento_financ",$stEstruturalPLano ) ;
                                                                                $obFEmpenhoEmpenhoPagamento->setDado("cod_estrutural",$stClasDespesa ) ;
                                                                                $obFEmpenhoEmpenhoPagamento->setDado("num_orgao"     ,$inNumOrgao ) ;

                                                                                $obErro = $obFEmpenhoEmpenhoPagamento->executaFuncao( $rsFEmpenhoEmpenhoPagamento, $boTransacao );
                                                                                //
                                                                                if ( !$obErro->ocorreu() ) {
                                                                                    $inCodLote = $obFEmpenhoEmpenhoPagamento->getDado("cod_lote");
                                                                                    $obFEmpenhoEmpenhoPagamento->setDado("cod_lote"           , "") ;
                                                                                    $obTContabilidadeLancamentoEmpenho->setDado("cod_lote"    ,$inCodLote );
                                                                                    $obTContabilidadeLancamentoEmpenho->setDado("tipo"        ,"P" );
                                                                                    $inSequencia = $rsFEmpenhoEmpenhoPagamento->getCampo("sequencia") ;
                                                                                    $obTContabilidadeLancamentoEmpenho->setDado("sequencia"   , $inSequencia );
                                                                                    $obTContabilidadeLancamentoEmpenho->setDado("exercicio"   ,$stExercicioAutent );
                                                                                    $obTContabilidadeLancamentoEmpenho->setDado("cod_entidade",$inCodEntidadeAutent );
                                                                                    $obTContabilidadeLancamentoEmpenho->setDado("estorno"     ,$boAnulacao ) ;
                                                                                    $obErro = $obTContabilidadeLancamentoEmpenho->inclusao( $boTransacao );
                                                                                    if ( !$obErro->ocorreu() ) {
                                                                                        $obTContabilidadePagamento->setDado("exercicio"   ,$stExercicioAutent );
                                                                                        $obTContabilidadePagamento->setDado("exercicio_liquidacao" ,$stExercicioLiquidacao );
                                                                                        $obTContabilidadePagamento->setDado("sequencia"   ,$inSequencia );
                                                                                        $obTContabilidadePagamento->setDado("tipo"        ,"P" );
                                                                                        $obTContabilidadePagamento->setDado("cod_lote"    ,$inCodLote );
                                                                                        $obTContabilidadePagamento->setDado("cod_entidade",$inCodEntidadeAutent );
                                                                                        $obTContabilidadePagamento->setDado("cod_nota"    ,$inCodNota );
                                                                                        $obTContabilidadePagamento->setDado("timestamp"   ,$stTimestampAutent ) ;
                                                                                        $obErro = $obTContabilidadePagamento->inclusao( $boTransacao );
                                                                                    }
                                                                                }
                                                                            } else {
                                                                                // Quando é RP
                                                                                $obFEmpenhoEmpenhoPagamentoRestosAPagar->setDado("exercicio"    ,$stExercicioAutent );
                                                                                $obFEmpenhoEmpenhoPagamentoRestosAPagar->setDado("valor"        ,$rsPagamentoLiquidacao->getCampo("vl_pagamento") ) ;
                                                                                $obFEmpenhoEmpenhoPagamentoRestosAPagar->setDado("complemento"  ,$inCodEmpenho.'/'.$stExercicioEmpenho ) ;
                                                                                $obFEmpenhoEmpenhoPagamentoRestosAPagar->setDado("nom_lote","Pagamento de RP n° ".$this->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->getCodEmpenho().'/'.$this->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->getExercicio() ) ;
                                                                                $obFEmpenhoEmpenhoPagamentoRestosAPagar->setDado("tipo_lote"    ,"P" ) ;
                                                                                $obFEmpenhoEmpenhoPagamentoRestosAPagar->setDado("cod_entidade" ,$inCodEntidadeAutent );
                                                                                $obFEmpenhoEmpenhoPagamentoRestosAPagar->setDado("cod_nota"     ,$inCodNota );
                                                                                $obFEmpenhoEmpenhoPagamentoRestosAPagar->setDado("conta_pg"     ,$stEstruturalPLano ) ;
                                                                                $obFEmpenhoEmpenhoPagamentoRestosAPagar->setDado("exerc_rp"     ,$stExercicioEmpenho );
                                                                                $obFEmpenhoEmpenhoPagamentoRestosAPagar->setDado("exercicio_liquidacao"     ,$stExercicioLiquidacao );
                                                                                // Testa se é: Executivo ou Legislativo
                                                                                // Se for orgao = 1 é 'Legislativo', demais orgãos é 'Executivo'
                                                                                if ($inNumOrgao == 1) {
                                                                                    $obFEmpenhoEmpenhoPagamentoRestosAPagar->setDado("restos","Legislativo" ) ;
                                                                                } else {
                                                                                    $obFEmpenhoEmpenhoPagamentoRestosAPagar->setDado("restos","Executivo" ) ;
                                                                                }
                                                                                $obFEmpenhoEmpenhoPagamentoRestosAPagar->setDado("dt_lote"   ,$stDataAutent ) ;
                                                                                $obErro = $obFEmpenhoEmpenhoPagamentoRestosAPagar->executaFuncao( $rsFEmpenoEmpenhoPagamentoRestosAPagar,'','', $boTransacao );
                                                                                if ($obErro->ocorreu()) {
                                                                                    if (strstr($obErro->getDescricao(),"Não foi informado o tipo de Restos")) {
                                                                                        $obErro->setDescricao("Impossível realizar os lançamentos. Verificar o atributo de Restos.");
                                                                                    }
                                                                                }

                                                                                if ( !$obErro->ocorreu() ) {
                                                                                    $inCodLote = $obFEmpenhoEmpenhoPagamentoRestosAPagar->getDado("cod_lote");
                                                                                    $obFEmpenhoEmpenhoPagamentoRestosAPagar->setDado("cod_lote"           , "") ;
                                                                                    $obTContabilidadeLancamentoEmpenho->setDado("cod_lote"    ,$inCodLote );
                                                                                    $obTContabilidadeLancamentoEmpenho->setDado("tipo"        ,"P" );
                                                                                    $inSequencia = $rsFEmpenoEmpenhoPagamentoRestosAPagar->getCampo("sequencia") ;
                                                                                    $obTContabilidadeLancamentoEmpenho->setDado("sequencia"   ,$inSequencia );
                                                                                    $obTContabilidadeLancamentoEmpenho->setDado("exercicio"   ,$stExercicioAutent );
                                                                                    $obTContabilidadeLancamentoEmpenho->setDado("cod_entidade",$inCodEntidadeAutent );
                                                                                    $obTContabilidadeLancamentoEmpenho->setDado("estorno"     ,$boAnulacao ) ;
                                                                                    $obErro = $obTContabilidadeLancamentoEmpenho->inclusao( $boTransacao );
                                                                                    if ( !$obErro->ocorreu() ) {
                                                                                        $obTContabilidadePagamento->setDado("exercicio"   ,$stExercicioAutent );
                                                                                        $obTContabilidadePagamento->setDado("exercicio_liquidacao" ,$stExercicioLiquidacao );
                                                                                        $obTContabilidadePagamento->setDado("sequencia"   ,$inSequencia );
                                                                                        $obTContabilidadePagamento->setDado("tipo"        ,"P" );
                                                                                        $obTContabilidadePagamento->setDado("cod_lote"    ,$inCodLote );
                                                                                        $obTContabilidadePagamento->setDado("cod_entidade",$inCodEntidadeAutent );
                                                                                        $obTContabilidadePagamento->setDado("cod_nota"    ,$inCodNota );
                                                                                        $obTContabilidadePagamento->setDado("timestamp"   ,$stTimestampAutent ) ;
                                                                                        $obErro = $obTContabilidadePagamento->inclusao( $boTransacao );
                                                                                    }
                                                                                }
                                                                            }
                                                                        } else {
                                                                            $stLogObs = "Entidade: ".$inCodEntidadeAutent.", OP: ".$inCodOrdemAutent.", erro ao recuperar dados do empenho no URBEM.";
                                                                            $this->logLinha( $stLogObs ) ;
                                                                            break;
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                      }
                                                  } elseif ( !$obErro->ocorreu() and $boAnulacao ) {
                                                      // Em caso de anulação:
                                                      // 1) É necessário existir primeiro o pagamento;
                                                      // 2) Pagamento não pode ter anulação.
                                                      //
                                                      // Verifica se já existe a anulação.
                                                      $obTEmpenhoNotaLiquidacaoPagaAnulada->setDado("timestamp"        ,"" );
                                                      $obTEmpenhoNotaLiquidacaoPagaAnulada->setDado("cod_nota"         ,$inCodNota );
                                                      $obTEmpenhoNotaLiquidacaoPagaAnulada->setDado("cod_entidade"     ,$inCodEntidadeAutent );
                                                      $obTEmpenhoNotaLiquidacaoPagaAnulada->setDado("exercicio"        ,$stExercicioLiquidacao );
                                                      $obTEmpenhoNotaLiquidacaoPagaAnulada->setDado("vl_anulado"       ,"" );
                                                      $obTEmpenhoNotaLiquidacaoPagaAnulada->setDado("timestamp_anulada",$stTimestampAutent );
                                                      $obErro = $obTEmpenhoNotaLiquidacaoPagaAnulada->recuperaPorChave( $rsNotaLiquidacaoPagaAnulada, $boTransacao );

                                                      if ( !$obErro->ocorreu() and $rsNotaLiquidacaoPagaAnulada->eof() ) {
                                                          //
                                                          $boInclusao = false ;
                                                          //
                                                          // Pesquisa novamente, desta vez sem timestamp para achar um pagamento.
                                                          //
                                                          $obTEmpenhoPagamentoLiquidacaoNotaLiquidacaoPaga->setDado("cod_nota"             ,$inCodNota );
                                                          $obTEmpenhoPagamentoLiquidacaoNotaLiquidacaoPaga->setDado("exercicio_liquidacao" ,$stExercicioLiquidacao );
                                                          $obTEmpenhoPagamentoLiquidacaoNotaLiquidacaoPaga->setDado("cod_entidade"         ,$inCodEntidadeAutent );
                                                          $obTEmpenhoPagamentoLiquidacaoNotaLiquidacaoPaga->setDado("exercicio"            ,$stExercicioAutent );
                                                          $obTEmpenhoPagamentoLiquidacaoNotaLiquidacaoPaga->setDado("cod_ordem"            ,$inCodOrdemAutent );
                                                          // Limpa o timestamp
                                                          $obTEmpenhoPagamentoLiquidacaoNotaLiquidacaoPaga->setDado("timestamp"            ,"" );
                                                          //
                                                          $obErro = $obTEmpenhoPagamentoLiquidacaoNotaLiquidacaoPaga->recuperaPorChave( $rsPagamentoLiquidacaoNotaLiquidacaoPaga, $boTransacao );
                                                          //
                                                          while ( !$obErro->ocorreu() and !$rsPagamentoLiquidacaoNotaLiquidacaoPaga->eof() and !$boInclusao ) {
                                                              $stTimestampPagamento = $rsPagamentoLiquidacaoNotaLiquidacaoPaga->getCampo("timestamp") ;
                                                              $obTEmpenhoNotaLiquidacaoPaga->setDado("cod_nota"     ,$inCodNota );
                                                              $obTEmpenhoNotaLiquidacaoPaga->setDado("cod_entidade" ,$inCodEntidadeAutent );
                                                              $obTEmpenhoNotaLiquidacaoPaga->setDado("exercicio"    ,$stExercicioLiquidacao );
                                                              $obTEmpenhoNotaLiquidacaoPaga->setDado("timestamp"    ,$stTimestampPagamento );
                                                              $obTEmpenhoNotaLiquidacaoPaga->setDado("vl_pago"      ,$rsPagamentoLiquidacao->getCampo("vl_pagamento") );

                                                              $obErro = $obTEmpenhoNotaLiquidacaoPaga->recuperaPorChave( $rsNotaLiquidacaoPaga, $boTransacao );

                                                              if ( !$obErro->ocorreu() and  !$rsNotaLiquidacaoPaga->eof() ) {

                                                                  $obTEmpenhoNotaLiquidacaoPagaAnulada->setDado("timestamp"        ,$stTimestampPagamento );
                                                                  $obTEmpenhoNotaLiquidacaoPagaAnulada->setDado("cod_nota"         ,$rsPagamentoLiquidacaoNotaLiquidacaoPaga->getCampo("cod_nota") );
                                                                  $obTEmpenhoNotaLiquidacaoPagaAnulada->setDado("cod_entidade"     ,$rsPagamentoLiquidacaoNotaLiquidacaoPaga->getCampo("cod_entidade") );
                                                                  $obTEmpenhoNotaLiquidacaoPagaAnulada->setDado("exercicio"        ,$rsPagamentoLiquidacaoNotaLiquidacaoPaga->getCampo("exercicio_liquidacao") );
                                                                  $obTEmpenhoNotaLiquidacaoPagaAnulada->setDado("vl_anulado"       ,$rsNotaLiquidacaoPaga->getCampo("vl_pago") );
                                                                  // Limpa o timestamp da anulacao
                                                                  //$obTEmpenhoNotaLiquidacaoPagaAnulada->setDado("timestamp_anulada",$stTimestampAutent );
                                                                  $obTEmpenhoNotaLiquidacaoPagaAnulada->setDado("timestamp_anulada","" );

                                                                  $obErro = $obTEmpenhoNotaLiquidacaoPagaAnulada->recuperaPorChave( $rsNotaLiquidacaoPagaAnulada, $boTransacao );

                                                                  // Somente inclui se não encontrar anulacao e encontrar o pagamento.
                                                                  if ( !$obErro->ocorreu() and $rsNotaLiquidacaoPagaAnulada->eof() and !$rsPagamentoLiquidacaoNotaLiquidacaoPaga->eof() ) {
                                                                      $obTEmpenhoNotaLiquidacaoPagaAnulada->setDado("timestamp_anulada",$stTimestampAutent );
                                                                      $obErro = $obTEmpenhoNotaLiquidacaoPagaAnulada->inclusao( $boTransacao );
                                                                      if ( !$obErro->ocorreu() ) {
                                                                           $boInclusao = true;
                                                                           $obTEmpenhoNotaLiquidacao->setDado("cod_nota"     ,$inCodNota );
                                                                           $obTEmpenhoNotaLiquidacao->setDado("cod_entidade" ,$inCodEntidadeAutent );
                                                                           $obTEmpenhoNotaLiquidacao->setDado("exercicio"    ,$stExercicioLiquidacao );
                                                                           $obErro = $this->obREmpenhoNotaLiquidacao->consultar( $boTransacao );
                                                                      }

                                                                      if ( !$obErro->ocorreu() and $this->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->getCodEmpenho() ) {
                                                                          $inCodEmpenho       = $this->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->getCodEmpenho();
                                                                          $stExercicioEmpenho = $this->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->getExercicio();
                                                                          $obErro = $this->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->checarImplantado( $boImplantado, $boTransacao  );
                                                                          //
                                                                          $stClasDespesa = "";
                                                                          $inNumOrgao = 0 ;
                                                                          if ( !$obErro->ocorreu() ) {
                                                                              if (!$boImplantado) {
                                                                                  $obTEmpenhoPreEmpenhoDespesa->setDado("cod_pre_empenho", $this->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->getCodPreEmpenho() );
                                                                                  $obTEmpenhoPreEmpenhoDespesa->setDado("exercicio"      , $stExercicioEmpenho );
                                                                                  $obErro = $obTEmpenhoPreEmpenhoDespesa->recuperaPorChave( $rsEmpenhoPreEmpenhoDespesa, $boTransacao );
                                                                                  if ( !$obErro->ocorreu() and  !$rsEmpenhoPreEmpenhoDespesa->eof() ) {
                                                                                      $obTOrcamentoDespesa->setDado("cod_despesa", $rsEmpenhoPreEmpenhoDespesa->getCampo("cod_despesa"));
                                                                                      $obTOrcamentoDespesa->setDado("exercicio", $stExercicioEmpenho);
                                                                                      $obErro = $obTOrcamentoDespesa->recuperaPorChave( $rsOrcamentoDespesa, $boTransacao ) ;
                                                                                      if ( !$obErro->ocorreu() and !$rsOrcamentoDespesa->eof() ) {
                                                                                          $inNumOrgao = $rsOrcamentoDespesa->getCampo("num_orgao");
                                                                                      } else {
                                                                                          $stLogObs = "Entidade: ".$inCodEntidadeAutent.", OP: ".$inCodOrdemAutent.", erro ao recuperar o orgão do empenho ".$inCodEmpenho."/".$stExercicioEmpenho." no URBEM.";
                                                                                          $this->logLinha( $stLogObs ) ;
                                                                                          break;
                                                                                      }
                                                                                  } else {
                                                                                      $stLogObs = "Entidade: ".$inCodEntidadeAutent.", OP: ".$inCodOrdemAutent.", erro ao recuperar a despesa do empenho ".$inCodEmpenho."/".$stExercicioEmpenho." no URBEM.";
                                                                                      $this->logLinha( $stLogObs ) ;
                                                                                      break;
                                                                                  }
                                                                                  $obTOrcamentoContaDespesa->setDado("cod_conta", $rsEmpenhoPreEmpenhoDespesa->getCampo("cod_conta"));
                                                                                  $obTOrcamentoContaDespesa->setDado("exercicio", $stExercicioEmpenho);
                                                                                  $obErro = $obTOrcamentoContaDespesa->recuperaPorChave( $rsOrcamentoContaDespesa, $boTransacao ) ;
                                                                                  if ( !$obErro->ocorreu() and !$rsOrcamentoContaDespesa->eof() ) {
                                                                                      $stClasDespesa = str_replace(".","",$rsOrcamentoContaDespesa->getCampo("cod_estrutural"));
                                                                                  } else {
                                                                                      $stLogObs = "Entidade: ".$inCodEntidadeAutent.", OP: ".$inCodOrdemAutent.", erro ao recuperar a classificação de despesa do empenho ".$inCodEmpenho."/".$stExercicioEmpenho." no URBEM.";
                                                                                      $this->logLinha( $stLogObs ) ;
                                                                                      break;
                                                                                  }
                                                                              } else {
                                                                                  $obTEmpenhoRestosPreEmpenho->setDado("cod_pre_empenho", $this->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->inCodPreEmpenho );
                                                                                  $obTEmpenhoRestosPreEmpenho->setDado("exercicio"      , $stExercicioEmpenho );
                                                                                  $obErro = $obTEmpenhoRestosPreEmpenho->recuperaPorChave( $rsEmpenhoRestosPreEmpenho, $boTransacao );
                                                                                  if ( !$obErro->ocorreu() and !$rsEmpenhoRestosPreEmpenho->eof() ) {
                                                                                      $inNumOrgao = $rsEmpenhoRestosPreEmpenho->getCampo("num_orgao") ;
                                                                                  } else {
                                                                                      $stLogObs = "Entidade: ".$inCodEntidadeAutent.", OP: ".$inCodOrdemAutent.", erro ao recuperar o orgão do empenho ".$inCodEmpenho."/".$stExercicioEmpenho." no URBEM.";
                                                                                      $this->logLinha( $stLogObs ) ;
                                                                                      break;
                                                                                  }
                                                                                  if ( !$obErro->ocorreu() and !$rsEmpenhoRestosPreEmpenho->eof() ) {
                                                                                      $stClasDespesa = $rsEmpenhoRestosPreEmpenho->getCampo("cod_estrutural") ;
                                                                                  } else {
                                                                                      $stLogObs = "Entidade: ".$inCodEntidadeAutent.", OP: ".$inCodOrdemAutent.", erro ao recuperar a classificação de despesa do empenho ".$inCodEmpenho."/".$stExercicioEmpenho." no URBEM.";
                                                                                      $this->logLinha( $stLogObs ) ;
                                                                                      break;
                                                                                  }
                                                                              }
                                                                          }
                                                                          //
                                                                          if ( !$obErro->ocorreu() ) {
                                                                              $stExercicioEmpenho = $this->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->getExercicio();
                                                                              $inCodEmpenho       = $this->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->getCodEmpenho();
                                                                              // Faz cfe exercicio, se exercicio da autenticacao for diferente do execicio do empenho, é Restos a Pagar.
                                                                              if ($stExercicioAutent == $stExercicioEmpenho) {
                                                                                  // Quando não é RP
                                                                                  $obFEmpenhoEmpenhoPagamentoAnulacao->setDado("exercicio"    ,$stExercicioLiquidacao );
                                                                                  $obFEmpenhoEmpenhoPagamentoAnulacao->setDado("valor"        ,$rsPagamentoLiquidacao->getCampo("vl_pagamento") ) ;
                                                                                  $obFEmpenhoEmpenhoPagamentoAnulacao->setDado("complemento"  ,$inCodEmpenho.'/'.$stExercicioEmpenho ) ;
                                                                                  $obFEmpenhoEmpenhoPagamentoAnulacao->setDado("nom_lote"     ,"Estorno Pagamento de Empenho n° ". $this->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->getCodEmpenho() .'/'.$this->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->getExercicio() ) ;
                                                                                  $obFEmpenhoEmpenhoPagamentoAnulacao->setDado("tipo_lote"    ,"P" ) ;
                                                                                  $obFEmpenhoEmpenhoPagamentoAnulacao->setDado("cod_entidade" ,$inCodEntidadeAutent );
                                                                                  $obFEmpenhoEmpenhoPagamentoAnulacao->setDado("dt_lote"      ,$stDataAutent ) ;
                                                                                  $obFEmpenhoEmpenhoPagamentoAnulacao->setDado("cod_nota"     ,$inCodNota );
                                                                                  $obFEmpenhoEmpenhoPagamentoAnulacao->setDado("conta_pagamento_financ",$stEstruturalPLano ) ;
                                                                                  $obFEmpenhoEmpenhoPagamentoAnulacao->setDado("cod_estrutural",$stClasDespesa ) ;
                                                                                  $obFEmpenhoEmpenhoPagamentoAnulacao->setDado("num_orgao"     ,$inNumOrgao ) ;
                                                                                  $obErro = $obFEmpenhoEmpenhoPagamentoAnulacao->executaFuncao( $rsFEmpenhoEmpenhoPagamentoAnulacao,'','',$boTransacao );
                                                                                  //
                                                                                  if ( !$obErro->ocorreu() ) {
                                                                                      $inCodLote = $obFEmpenhoEmpenhoPagamentoAnulacao->getDado("cod_lote");
                                                                                      $obFEmpenhoEmpenhoPagamentoAnulacao->setDado("cod_lote"   , "") ;
                                                                                      $obTContabilidadeLancamentoEmpenho->setDado("cod_lote"    ,$inCodLote );
                                                                                      $obTContabilidadeLancamentoEmpenho->setDado("tipo"        ,"P" );
                                                                                      $inSequencia = $rsFEmpenhoEmpenhoPagamentoAnulacao->getCampo("sequencia") ;
                                                                                      $obTContabilidadeLancamentoEmpenho->setDado("sequencia"   , $inSequencia );
                                                                                      $obTContabilidadeLancamentoEmpenho->setDado("exercicio"   ,$stExercicioAutent );
                                                                                      $obTContabilidadeLancamentoEmpenho->setDado("cod_entidade",$inCodEntidadeAutent );
                                                                                      $obTContabilidadeLancamentoEmpenho->setDado("estorno"     ,$boAnulacao ) ;
                                                                                      $obErro = $obTContabilidadeLancamentoEmpenho->inclusao( $boTransacao );
                                                                                      if ( !$obErro->ocorreu() ) {
                                                                                          $obTContabilidadePagamento->setDado("exercicio"   ,$stExercicioAutent );
                                                                                          $obTContabilidadePagamento->setDado("exercicio_liquidacao" ,$stExercicioLiquidacao );
                                                                                          $obTContabilidadePagamento->setDado("sequencia"   ,$inSequencia );
                                                                                          $obTContabilidadePagamento->setDado("tipo"        ,"P" );
                                                                                          $obTContabilidadePagamento->setDado("cod_lote"    ,$inCodLote );
                                                                                          $obTContabilidadePagamento->setDado("cod_entidade",$inCodEntidadeAutent );
                                                                                          $obTContabilidadePagamento->setDado("cod_nota"    ,$inCodNota );
                                                                                          $obTContabilidadePagamento->setDado("timestamp"   ,$stTimestampPagamento ) ;
                                                                                          $obErro = $obTContabilidadePagamento->inclusao( $boTransacao );
                                                                                      }
                                                                                  }
                                                                              } else {
                                                                                  // Quando é RP
                                                                                  $obFEmpenhoEmpenhoPagamentoRestosAPagarAnulacao->setDado("exercicio"    ,$stExercicioAutent );
                                                                                  $obFEmpenhoEmpenhoPagamentoRestosAPagarAnulacao->setDado("valor"        ,$rsPagamentoLiquidacao->getCampo("vl_pagamento") ) ;
                                                                                  $obFEmpenhoEmpenhoPagamentoRestosAPagarAnulacao->setDado("complemento"  ,$inCodEmpenho.'/'.$stExercicioEmpenho ) ;
                                                                                  $obFEmpenhoEmpenhoPagamentoRestosAPagarAnulacao->setDado("nom_lote","Estorno de Pagamento de RP n° ".$this->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->getCodEmpenho().'/'.$this->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->getExercicio() ) ;
                                                                                  $obFEmpenhoEmpenhoPagamentoRestosAPagarAnulacao->setDado("tipo_lote"    ,"P" ) ;
                                                                                  $obFEmpenhoEmpenhoPagamentoRestosAPagarAnulacao->setDado("cod_entidade" ,$inCodEntidadeAutent );
                                                                                  $obFEmpenhoEmpenhoPagamentoRestosAPagarAnulacao->setDado("cod_nota"     ,$inCodNota );
                                                                                  $obFEmpenhoEmpenhoPagamentoRestosAPagarAnulacao->setDado("conta_pg"     ,$stEstruturalPLano ) ;
                                                                                  $obFEmpenhoEmpenhoPagamentoRestosAPagarAnulacao->setDado("exerc_rp"     ,$stExercicioEmpenho );
                                                                                  $obFEmpenhoEmpenhoPagamentoRestosAPagarAnulacao->setDado("exercicio_liquidacao"     ,$stExercicioLiquidacao );
                                                                                  // Testa se é: Executivo ou Legislativo
                                                                                  // Se for Orgao = 1 é 'Legislativo', demais orgãos é 'Executivo'
                                                                                  if ($inNumOrgao == 1) {
                                                                                      $obFEmpenhoEmpenhoPagamentoRestosAPagarAnulacao->setDado("restos","Legislativo" ) ;
                                                                                  } else {
                                                                                      $obFEmpenhoEmpenhoPagamentoRestosAPagarAnulacao->setDado("restos","Executivo" ) ;
                                                                                  }
                                                                                  $obFEmpenhoEmpenhoPagamentoRestosAPagarAnulacao->setDado("dt_lote"   ,$stDataAutent ) ;
                                                                                  $obErro = $obFEmpenhoEmpenhoPagamentoRestosAPagarAnulacao->executaFuncao( $rsFEmpenhoEmpenhoPagamentoRestosAPagarAnulacao,'','',$boTransacao );
                                                                                  if ($obErro->ocorreu()) {
                                                                                      if (strstr($obErro->getDescricao(),"Não foi informado o tipo de Restos")) {
                                                                                          $obErro->setDescricao("Impossível realizar os lançamentos. Verificar o atributo de Restos.");
                                                                                      }
                                                                                  }

                                                                                  if ( !$obErro->ocorreu() ) {
                                                                                      $inCodLote = $obFEmpenhoEmpenhoPagamentoRestosAPagarAnulacao->getDado("cod_lote");
                                                                                      $obFEmpenhoEmpenhoPagamentoRestosAPagarAnulacao->setDado("cod_lote"           , "") ;
                                                                                      $obTContabilidadeLancamentoEmpenho->setDado("cod_lote"    ,$inCodLote );
                                                                                      $obTContabilidadeLancamentoEmpenho->setDado("tipo"        ,"P" );
                                                                                      $inSequencia = $rsFEmpenhoEmpenhoPagamentoRestosAPagarAnulacao->getCampo("sequencia") ;
                                                                                      $obTContabilidadeLancamentoEmpenho->setDado("sequencia"   ,$inSequencia );
                                                                                      $obTContabilidadeLancamentoEmpenho->setDado("exercicio"   ,$stExercicioAutent );
                                                                                      $obTContabilidadeLancamentoEmpenho->setDado("cod_entidade",$inCodEntidadeAutent );
                                                                                      $obTContabilidadeLancamentoEmpenho->setDado("estorno"     ,$boAnulacao ) ;
                                                                                      $obErro = $obTContabilidadeLancamentoEmpenho->inclusao( $boTransacao );
                                                                                      if ( !$obErro->ocorreu() ) {
                                                                                          $obTContabilidadePagamento->setDado("exercicio"   ,$stExercicioAutent );
                                                                                          $obTContabilidadePagamento->setDado("exercicio_liquidacao" ,$stExercicioLiquidacao );
                                                                                          $obTContabilidadePagamento->setDado("sequencia"   ,$inSequencia );
                                                                                          $obTContabilidadePagamento->setDado("tipo"        ,"P" );
                                                                                          $obTContabilidadePagamento->setDado("cod_lote"    ,$inCodLote );
                                                                                          $obTContabilidadePagamento->setDado("cod_entidade",$inCodEntidadeAutent );
                                                                                          $obTContabilidadePagamento->setDado("cod_nota"    ,$inCodNota );
                                                                                          $obTContabilidadePagamento->setDado("timestamp"   ,$stTimestampPagamento ) ;
                                                                                          $obErro = $obTContabilidadePagamento->inclusao( $boTransacao );
                                                                                      }
                                                                                  }
                                                                              }
                                                                          }
                                                                      } else {
                                                                         $stLogObs = "Entidade: ".$inCodEntidadeAutent.", OP: ".$inCodOrdemAutent.", erro ao recuperar dados do empenho no URBEM.";
                                                                         $this->logLinha( $stLogObs ) ;
                                                                         break;
                                                                      }
                                                                  }
                                                              }
                                                              $rsPagamentoLiquidacaoNotaLiquidacaoPaga->proximo();
                                                          }
                                                      }
                                                  }
                                              } else {
                                                  $stLogObs = "Entidade: ".$inCodEntidadeAutent.", OP: ".$inCodOrdemAutent.", erro ao recuperar dados do empenho no URBEM.";
                                                  $this->logLinha( $stLogObs ) ;
                                                  break;
                                              }
                                              if( $obErro->ocorreu() )
                                                  break;

                                              $rsPagamentoLiquidacao->proximo();
                                       }
                                   } else {
                                       $stLogObs = "Entidade: ".$inCodEntidadeAutent.", OP: ".$inCodOrdemAutent.", erro ao recuperar as notas de liquidação da OP  no URBEM.";
                                       $this->logLinha( $stLogObs ) ;
                                   }
                               } else {
                                   //$nuErros ++ ;
                                   //if( $nuErros == 1 )
                                   //    $this->logCabec() ;
                                   $stLogObs = "Entidade: ".$inCodEntidadeAutent.", OP: ".$inCodOrdemAutent.", Valor da OP R$ ".$nuValorTotalPagamentoLiquidacao.", Valor Autenticado R$ ".$nuValorAutenticado.". Divergência no valor.";
                                   $this->logLinha( $stLogObs ) ;
                               }
                           } else {
                               $stLogObs = "Entidade: ".$inCodEntidadeAutent.", OP: ".$inCodOrdemAutent.", erro ao recuperar o valor da OP no URBEM.";
                               $this->logLinha( $stLogObs ) ;
                           }
                       } else {
                           $stLogObs = "Entidade: ".$inCodEntidadeAutent.", OP: ".$inCodOrdemAutent.", código reduzido do Plano ".$inContaPlano." não encontrado no URBEM.";
                           $this->logLinha( $stLogObs ) ;
                       }
                   } else {
                       $stLogObs = "Entidade: ".$inCodEntidadeAutent.", OP: ".$inCodOrdemAutent.", código reduzido do Plano ".$inContaPlano." não encontrado no URBEM.";
                       $this->logLinha( $stLogObs ) ;
                   }
               } else {
                 $stLogObs = "Entidade: ".$inCodEntidadeAutent.", OP: ".$inCodOrdemAutent.", erro ao recuperar o reduzido do Plano.";
                 $this->logLinha( $stLogObs ) ;
               }
               $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTEmpenhoPagamentoLiquidacao );

               if( $obErro->ocorreu() )
                   break;

               $rsListaAutenticacoes->proximo();
        }
    }
    // FECHA TRANSACAO GERAL
    //$this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );

    if ($this->nuErros > 0) {
        fclose($this->logErros) ;
    }

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
function listarContasTesouraria(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
{
    include_once ( CAM_GF_TES_MAPEAMENTO.   "VTesourariaSamlinkSiamSaltes.class.php"                             );
    $obVSamlinkSiamSaltes                             =  new VSamlinkSiamSaltes;

    $stFiltro = '';

    if ( $this->getContaTesouraria() ) {
        $stFiltro .= " conta = ".$this->getContaTesouraria()." AND ";
    }
    $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
    $stOrder  = ($stOrder)  ? $stOrder : " conta";
    $obErro = $obVSamlinkSiamSaltes->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

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
function listarMaiorData(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO     ."TEmpenhoNotaLiquidacao.class.php"                    );
    $obTEmpenhoPagamentoLiquidacao           =  new TEmpenhoPagamentoLiquidacao;

    if( $this->obREmpenhoOrdemPagamento->obROrcamentoEntidade->getCodigoEntidade() )
        $stFiltro  .= " AND cod_entidade IN (".$this->obREmpenhoOrdemPagamento->obROrcamentoEntidade->getCodigoEntidade()." ) ";

    if( $this->stExercicio )
        $stFiltro .= " AND exercicio = '".$this->stExercicio."' ";

    $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 4, strlen($stFiltro)) : "";

    $obTEmpenhoPagamentoLiquidacao->setDado('stExercicio',$this->stExercicio);
    $obTEmpenhoPagamentoLiquidacao->setDado('stDataOrdem',$this->obREmpenhoOrdemPagamento->getDataEmissao());

    $obErro = $obTEmpenhoPagamentoLiquidacao->recuperaMaiorData( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

}
