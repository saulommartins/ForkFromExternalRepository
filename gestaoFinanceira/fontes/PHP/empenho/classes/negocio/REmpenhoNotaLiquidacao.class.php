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
    * Classe de Regra de Nota de Liquidação
    * Data de Criação   : 02/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Regra

    $Id: REmpenhoNotaLiquidacao.class.php 64153 2015-12-09 19:16:02Z evandro $

    * Casos de uso: uc-02.03.18, uc-02.03.20, uc-02.03.03, uc-02.03.04, uc-02.03.14
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GF_EMP_NEGOCIO."REmpenhoEmpenho.class.php";
//INCLUDE DAS CLASSES PARA O TRATAMENTO DOS ATRIBUTOS DINAMICOS
include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoAtributoLiquidacaoValor.class.php";
include_once CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php";
include_once CAM_GF_CONT_NEGOCIO."RContabilidadePlanoContaAnalitica.class.php";
include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoIncorporacaoPatrimonio.class.php";
include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoNotaLiquidacao.class.php";

class REmpenhoNotaLiquidacao
{
/**
    * @access Private
    * @var Object
*/
var $roREmpenhoEmpenho;
/*
    * @var Object
    * @access Private
*/
var $obROrcamentoEntidade;
/**
    * @access Private
    * @var Object
*/
var $obRCadastroDinamico;
/*
    * @var Object
    * @access Private
*/
var $obRContabilidadePlanoContaAnaliticaDebito;
/*
    * @var Object
    * @access Private
*/
var $obRContabilidadePlanoContaAnaliticaCredito;
/**
    * @access Private
    * @var Object
*/
var $obTEmpenhoNotaLiquidacao;
/**
    * @access Private
    * @var Object
*/
var $obTransacao;
/**
    * @access Private
    * @var Integer
*/
var $inCodNota;
/**
    * @access Private
    * @var Integer
*/
var $inCodNotaInicial;
/**
    * @access Private
    * @var Integer
*/
var $inCodNotaFinal;
/**
    * @access Private
    * @var Integer
*/
var $inCodContaContabilFinanc;
/**
    * @access Private
    * @var Integer
*/
var $inCodHistorico;
/**
    * @access Private
    * @var String
*/
var $stComplemento;
/**
    * @access Private
    * @var String
*/
var $stExercicio;
/**
    * @access Private
    * @var String
*/
var $stExercRP;
/**
    * @access Private
    * @var String
*/
var $stDtLiquidacao;
/**
    * @access Private
    * @var String
*/
var $stDtEstornoLiquidacao;
/**
    * @access Private
    * @var String
*/
var $stDtVencimento;
/**
    * @access Private
    * @var String
*/
var $stDtNota;
/**
    * @access Private
    * @var String
*/
var $stTimestamp;
/**
    * @access Private
    * @var String
*/
var $stObservacao;
/**
    * @access Private
    * @var Numeric
*/
var $nuVlTotal;
/**
    * @access Private
    * @var Numeric
*/
var $nuVlPago;
/**
    * @access Private
    * @var Numeric
*/
var $nuVlEstornado;
/**
    * @access Private
    * @var Numeric
*/
var $nuVlAPagar;
/**
    * @access Private
    * @var integer
*/
var $inCodOrdem;
/**
    * @access Private
    * @var integer
*/
var $inCodPlano;
var $inCodPlanoRetencao;
/**
    * @access Private
    * @var String
*/
var $stExercicioPlano;

function setTransacao($valor) { $this->obTransacao           = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setREmpenhoEmpenho($valor) { $this->roREmpenhoEmpenho           = $valor; }
/**
    * @access Public
    * @param Integer $Valor
*/
function setCodNota($valor) { $this->inCodNota                   = $valor; }
/**
    * @access Public
    * @param Integer $Valor
*/
function setCodNotaInicial($valor) { $this->inCodNotaInicial           = $valor; }
/**
    * @access Public
    * @param Integer $Valor
*/
function setCodNotaFinal($valor) { $this->inCodNotaFinal             = $valor; }
/**
    * @access Public
    * @param Integer $Valor
*/
function setCodContaContabilFinanc($valor) { (int) $this->inCodContaContabilFinanc = $valor; }
/**
    * @access Public
    * @param Integer $Valor
*/
function setCodHistorico($valor) { (int) $this->inCodHistorico              = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setComplemento($valor) { $this->stComplemento               = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setExercicio($valor) { $this->stExercicio                 = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setExercRP($valor) { $this->stExercRP                   = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setDtLiquidacao($valor) { $this->stDtLiquidacao             = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setDtEstornoLiquidacao($valor) { $this->stDtEstornoLiquidacao             = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setDtVencimento($valor) { $this->stDtVencimento             = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setDtNota($valor) { $this->stDtNota                   = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setTimestamp($valor) { $this->stTimestamp                = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setObservacao($valor) { $this->stObservacao               = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setValorTotal($valor) { $this->nuVlTotal             = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setValorPago($valor) { $this->nuVlPago             = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setValorEstornado($valor) { $this->nuVlEstornado        = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setValorAPagar($valor) { $this->nuVlAPagar             = $valor; }
/**
    * @access Public
    * @param Integer $Valor
*/
function setCodOrdem($valor) { $this->inCodOrdem             = $valor; }
/**
    * @access Public
    * @param Integer $Valor
*/
function setCodPlano($valor) { $this->inCodPlano             = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setExercicioPlano($valor) { $this->stExercicioPlano           = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setRContabilidadePlanoContaAnaliticaDebito($valor) { $this->obRContabilidadePlanoContaAnaliticaDebito = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setRContabilidadePlanoContaAnaliticaCredito($valor) { $this->obRContabilidadePlanoContaAnaliticaCredito = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setTEmpenhoNotaLiquidacao($valor) { $this->obTEmpenhoNotaLiquidacao = $valor; }
/**
    * @access Public
    * @return Object
*/
function getREmpenhoEmpenho() { return $this->roREmpenhoEmpenho;                      }
/**
    * @access Public
    * @return Integer
*/
function getCodNota() { return $this->inCodNota;                              }
/**
    * @access Public
    * @return Integer
*/
function getCodNotaFinal() { return $this->inCodNotaFinal;                         }
/**
    * @access Public
    * @return Integer
*/
function getCodNotaInicial() { return $this->inCodNotaInicial;                       }
/**
    * @access Public
    * @return Integer
*/
function getCodContaContabilFinanc() { return $this->inCodContaContabilFinanc;              }
/**
    * @access Public
    * @return Integer
*/
function getCodHistorico() { return $this->inCodHistorico;                        }
/**
    * @access Public
    * @return String
*/
function getComplemento() { return $this->stComplemento;                         }
/**
    * @access Public
    * @return String
*/
function getExercicio() { return $this->stExercicio;                           }
/**
    * @access Public
    * @return String
*/
function getExercRP() { return $this->stExercRP;                             }
/**
    * @access Public
    * @return String
*/
function getDtLiquidacao() { return $this->stDtLiquidacao;                        }
/**
    * @access Public
    * @return String
*/
function getDtEstornoLiquidacao() { return $this->stDtEstornoLiquidacao; }
/**
    * @access Public
    * @return String
*/
function getDtVencimento() { return $this->stDtVencimento; }
/**
    * @access Public
    * @return String
*/
function getDtNota() { return $this->stDtNota; }
/**
    * @access Public
    * @return String
*/
function getTimestamp() { return $this->stTimestamp; }
/**
    * @access Public
    * @return String
*/
function getObservacao() { return $this->stObservacao; }

/**
    * @access Public
    * @return Numeric
*/
function getValorTotal() { return $this->nuVlTotal; }
/**
    * @access Public
    * @return Numeric
*/
function getValorPago() { return $this->nuVlPago; }
/**
    * @access Public
    * @return Numeric
*/
function getValorEstornado() { return $this->nuVlEstornado; }
/**
    * @access Public
    * @return Numeric
*/
function getValorAPagar() { return $this->nuVlAPagar; }
/**
    * @access Public
    * @return Integer
*/
function getCodOrdem() { return $this->inCodOrdem; }
/**
    * @access Public
    * @return Integer
*/
function getCodPlano() { return $this->inCodPlano; }
/**
    * @access Public
    * @return String
*/
function getExercicioPlano() { return $this->stExercicioPlano; }

/**
    * @access Public
    * @return Object
*/
function getRContabilidadePlanoContaAnaliticaDebito() { return $this->obRContabilidadePlanoContaAnaliticaDebito; }
/**
    * @access Public
    * @return Object
*/
function getRContabilidadePlanoContaAnaliticaCredito() { return $this->obRContabilidadePlanoContaAnaliticaCredito; }
/**
    * @access Public
    * @return Object
*/
function getTEmpenhoNotaLiquidacao() { return $this->obTEmpenhoNotaLiquidacao; }
/**
     * Método construtor
     * @access Public
*/
function REmpenhoNotaLiquidacao(&$obREmpenhoEmpenho)
{
    $this->roREmpenhoEmpenho = &$obREmpenhoEmpenho;
    $this->obTransacao = new Transacao;
    $this->obRCadastroDinamico = new RCadastroDinamico;
    $this->obRCadastroDinamico->setPersistenteValores ( new TEmpenhoAtributoLiquidacaoValor );
    $this->obRCadastroDinamico->setCodCadastro( 2 );
    $this->obRCadastroDinamico->obRModulo->setCodModulo( 10 );
    $this->obRContabilidadePlanoContaAnaliticaDebito = new RContabilidadePlanoContaAnalitica;
    $this->obRContabilidadePlanoContaAnaliticaCredito = new RContabilidadePlanoContaAnalitica;
    $this->obTEmpenhoNotaLiquidacao = new TEmpenhoNotaLiquidacao;
}

/**
    * Executa um recuperaPorChave na classe Persistente
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultar($boTransacao = "")
{
    $obTEmpenhoNotaLiquidacao                        = new TEmpenhoNotaLiquidacao;
    $obTEmpenhoNotaLiquidacao->setDado( "cod_nota"    , $this->inCodNota  );
    $obTEmpenhoNotaLiquidacao->setDado( "exercicio"   , $this->stExercicio );
    $obTEmpenhoNotaLiquidacao->setDado( "cod_entidade", $this->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade()  );
    $obErro = $obTEmpenhoNotaLiquidacao->recuperaPorChave( $rsRecordSet, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $this->stDtVencimento = $rsRecordSet->getCampo("dt_vencimento");
        $this->stDtLiquidacao = $rsRecordSet->getCampo("dt_liquidacao");
        $this->stObservacao   = $rsRecordSet->getCampo("observacao");
        $this->stDtNota       = $rsRecordSet->getCampo("dt_liquidacao");
        $this->roREmpenhoEmpenho->setExercicio ( $rsRecordSet->getCampo("exercicio_empenho") );
        $this->roREmpenhoEmpenho->setCodEmpenho( $rsRecordSet->getCampo("cod_empenho") );
        $obErro = $this->roREmpenhoEmpenho->consultar( $boTransacao );
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
    $obTEmpenhoNotaLiquidacao                        = new TEmpenhoNotaLiquidacao;

    if($this->inCodNota)
        $stFiltro  = " enl.cod_nota = " . $this->inCodNota . "  AND ";
    if($this->stExercicio)
        $stFiltro .= " exercicio = '" . $this->stExercicio . "' AND ";
    if($this->roREmpenhoEmpenho->getCodEmpenho())
        $stFiltro  = " cod_empenho = " . $this->roREmpenhoEmpenho->getCodEmpenho() . "  AND ";
    if($this->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade())
        $stFiltro  = " cod_entidade = " . $this->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() . "  AND ";
    if( trim($this->stDtVencimento) != "" )
        $stFiltro .= " dt_vencimento = to_char('" .$this->stDtVencimento. "','dd/mm/yyyy') AND ";
    if( trim($this->stObservacao) != "" )
        $stFiltro .= " UPPER(observacao) LIKE UPPER('" . $this->stObservacao . "%') AND ";
    $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
    $stOrder  = ($stOrder)  ? $stOrder : " cod_nota";
    $obErro = $obTEmpenhoNotaLiquidacao->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaNotasDisponiveisImplantadas na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarNotasAPagarDisponiveisImplantadas(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
{
    $obTEmpenhoNotaLiquidacao                        = new TEmpenhoNotaLiquidacao;

    if($this->inCodNota)
        $stFiltro  = " enl.cod_nota = " . $this->inCodNota . "  AND ";
    if($this->stExercicio)
        $stFiltro .= " enl.exercicio = '" . $this->stExercicio . "' AND ";
    if($this->roREmpenhoEmpenho->getExercicio() )
        $stFiltro .= " EE.exercicio = '" . $this->roREmpenhoEmpenho->getExercicio() . "' AND ";
    if($this->roREmpenhoEmpenho->getCodEmpenho())
        $stFiltro .= " EE.cod_empenho = " . $this->roREmpenhoEmpenho->getCodEmpenho() . "  AND ";
    if($this->roREmpenhoEmpenho->getCodEmpenhoInicial())
        $stFiltro .= " EE.cod_empenho >= " . $this->roREmpenhoEmpenho->getCodEmpenhoInicial() . "  AND ";
    if($this->roREmpenhoEmpenho->getCodEmpenhoFinal())
        $stFiltro .= " EE.cod_empenho <= " . $this->roREmpenhoEmpenho->getCodEmpenhoFinal() . "  AND ";
    if($this->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade())
        $stFiltro .= " EE.cod_entidade = " . $this->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() . "  AND ";
    if( trim($this->stDtVencimento) != "" )
        $stFiltro .= " to_char(em.dt_vencimento,'dd/mm/yyyy') = to_char('" .$this->stDtVencimento. "','dd/mm/yyyy') AND ";
    if( trim($this->stObservacao) != "" )
        $stFiltro .= " UPPER(observacao) LIKE UPPER('" . $this->stObservacao . "%') AND ";
    $stFiltro = ($stFiltro) ? " AND " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
    $obErro = $obTEmpenhoNotaLiquidacao->recuperaNotasAPagarDisponiveisImplantadas( $rsRecordSet, $stFiltro, '', $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaRelacionamento na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarNotasDisponiveis(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
{
    global $request;
    $obTEmpenhoNotaLiquidacao                        = new TEmpenhoNotaLiquidacao;

    $stFiltro  = empty($stFiltro) ? "" : $stFiltro;
    if($this->inCodNota)
        $stFiltro .= " enl.cod_nota = " . $this->inCodNota . "  AND ";
    if($this->stExercicio)
        $stFiltro .= " ENL.exercicio = '" . $this->stExercicio . "' AND ";
    if($this->roREmpenhoEmpenho->getExercicio() )
        $stFiltro .= " ENL.exercicio_empenho = '" . $this->roREmpenhoEmpenho->getExercicio() . "' AND ";
    if($this->roREmpenhoEmpenho->getCodEmpenho())
        $stFiltro .= " EE.cod_empenho = " . $this->roREmpenhoEmpenho->getCodEmpenho() . "  AND ";
    if($this->roREmpenhoEmpenho->getCodEmpenhoInicial())
        $stFiltro .= " EE.cod_empenho >= " . $this->roREmpenhoEmpenho->getCodEmpenhoInicial() . "  AND ";
    if($this->roREmpenhoEmpenho->getCodEmpenhoFinal())
        $stFiltro .= " EE.cod_empenho <= " . $this->roREmpenhoEmpenho->getCodEmpenhoFinal() . "  AND ";
    if ( $this->roREmpenhoEmpenho->getDtEmpenhoInicial() or $this->roREmpenhoEmpenho->getDtEmpenhoFinal() ) {
        $this->roREmpenhoEmpenho->stDtEmpenhoInicial = ( $this->roREmpenhoEmpenho->stDtEmpenhoInicial ) ? $this->roREmpenhoEmpenho->stDtEmpenhoInicial : '01/01/'.$this->roREmpenhoEmpenho->getExercicio();
        $this->roREmpenhoEmpenho->stDtEmpenhoFinal   = ( $this->roREmpenhoEmpenho->stDtEmpenhoFinal )   ? $this->roREmpenhoEmpenho->stDtEmpenhoFinal :   '31/12/'.$this->roREmpenhoEmpenho->getExercicio();
        $stFiltro .= " dt_empenho between ";
        $stFiltro .= " TO_DATE('".$this->roREmpenhoEmpenho->getDtEmpenhoInicial()."','dd/mm/yyyy') AND TO_DATE('".$this->roREmpenhoEmpenho->getDtEmpenhoFinal()."','dd/mm/yyyy') AND";
    }

    if ($this->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade()) {
         if ($request->get('tipoBusca')=='buscaEmpenho')
            $stFiltro .= " EE.cod_entidade = " . $this->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() . "  AND " . "ENL.cod_nota = (SELECT max(cod_nota) FROM empenho.nota_liquidacao WHERE cod_empenho = ENL.cod_empenho AND cod_entidade = ENL.cod_entidade AND exercicio = ENL.exercicio )" . "  AND ";
        else
            $stFiltro .= " EE.cod_entidade = " . $this->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() . "  AND ";}
    if( trim($this->stDtVencimento) != "" )
        $stFiltro .= " ENL.dt_vencimento = to_date('" .$this->stDtVencimento. "','dd/mm/yyyy') AND ";
    if( trim($this->stObservacao) != "" )
        $stFiltro .= " UPPER(observacao) LIKE UPPER('" . $this->stObservacao . "%') AND ";

    $stFiltro = ($stFiltro) ? " AND " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
    $obErro = $obTEmpenhoNotaLiquidacao->recuperaRecuperaNotasDisponiveis( $rsRecordSet, $stFiltro, '', $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaNotasDisponiveisImplantadas na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarNotasDisponiveisImplantadas(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
{
    $obTEmpenhoNotaLiquidacao                        = new TEmpenhoNotaLiquidacao;

    if($this->inCodNota)
        $stFiltro  = " enl.cod_nota = " . $this->inCodNota . "  AND ";
    if($this->stExercicio)
        $stFiltro .= " enl.exercicio = '" . $this->stExercicio . "' AND ";
    if($this->roREmpenhoEmpenho->getExercicio() )
        $stFiltro .= " EE.exercicio = '" . $this->roREmpenhoEmpenho->getExercicio() . "' AND ";
    if($this->roREmpenhoEmpenho->getCodEmpenho())
        $stFiltro .= " EE.cod_empenho = " . $this->roREmpenhoEmpenho->getCodEmpenho() . "  AND ";
    if($this->roREmpenhoEmpenho->getCodEmpenhoInicial())
        $stFiltro .= " EE.cod_empenho >= " . $this->roREmpenhoEmpenho->getCodEmpenhoInicial() . "  AND ";
    if($this->roREmpenhoEmpenho->getCodEmpenhoFinal())
        $stFiltro .= " EE.cod_empenho <= " . $this->roREmpenhoEmpenho->getCodEmpenhoFinal() . "  AND ";
    if ( $this->roREmpenhoEmpenho->getDtEmpenhoInicial() or $this->roREmpenhoEmpenho->getDtEmpenhoFinal() ) {
        $this->roREmpenhoEmpenho->stDtEmpenhoInicial = ( $this->roREmpenhoEmpenho->stDtEmpenhoInicial ) ? $this->roREmpenhoEmpenho->stDtEmpenhoInicial : '01/01/'.$this->roREmpenhoEmpenho->getExercicio();
        $this->roREmpenhoEmpenho->stDtEmpenhoFinal   = ( $this->roREmpenhoEmpenho->stDtEmpenhoFinal )   ? $this->roREmpenhoEmpenho->stDtEmpenhoFinal :   '31/12/'.$this->roREmpenhoEmpenho->getExercicio();
        $stFiltro .= " dt_empenho between ";
        $stFiltro .= " TO_DATE('".$this->roREmpenhoEmpenho->getDtEmpenhoInicial()."','dd/mm/yyyy') AND TO_DATE('".$this->roREmpenhoEmpenho->getDtEmpenhoFinal()."','dd/mm/yyyy') AND";
    }
    if($this->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade())
        $stFiltro .= " EE.cod_entidade = " . $this->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() . "  AND ";
    if( trim($this->stDtVencimento) != "" )
        $stFiltro .= " to_char(em.dt_vencimento,'dd/mm/yyyy') = to_char('" .$this->stDtVencimento. "','dd/mm/yyyy') AND ";
    if( trim($this->stObservacao) != "" )
        $stFiltro .= " UPPER(observacao) LIKE UPPER('" . $this->stObservacao . "%') AND ";
    $stFiltro = ($stFiltro) ? " AND " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";

    $obErro = $obTEmpenhoNotaLiquidacao->recuperaNotasDisponiveisImplantadas( $rsRecordSet, $stFiltro, '', $boTransacao );

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
    include_once CAM_GF_EMP_MAPEAMENTO."FEmpenhoEmpenhoLiquidacao.class.php";
    include_once CAM_GF_EMP_MAPEAMENTO."FEmpenhoEmpenhoLiquidacaoTCEMS.class.php";
    include_once CAM_GF_EMP_MAPEAMENTO."FEmpenhoEmpenhoLiquidacaoRestosAPagar.class.php";
    include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeLiquidacao.class.php";
    include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamentoEmpenho.class.php";

    $obTContabilidadeLancamentoEmpenho          = new TContabilidadeLancamentoEmpenho;
    $obTContabilidadeLiquidacao                 = new TContabilidadeLiquidacao;
    $obFEmpenhoEmpenhoLiquidacaoRestosAPagar    = new FEmpenhoEmpenhoLiquidacaoRestosAPagar;
    $obTEmpenhoIncorporacaoPatrimonio           = new TEmpenhoIncorporacaoPatrimonio;
    $this->obTEmpenhoNotaLiquidacao             = new TEmpenhoNotaLiquidacao;    

    if (Sessao::getExercicio() > '2012')
        $obFEmpenhoEmpenhoLiquidacao = new FEmpenhoEmpenhoLiquidacaoTCEMS;
    else
        $obFEmpenhoEmpenhoLiquidacao = new FEmpenhoEmpenhoLiquidacao;

    $boFlagTransacao = false;
    $boFlagNovaClassificacao = true;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao);
    if ( !$obErro->ocorreu() ) {
        $this->obTEmpenhoNotaLiquidacao->setDado("exercicio",$this->stExercicio );
        // Verifica numeração do empenho
        $obErro = $this->roREmpenhoEmpenho->obREmpenhoConfiguracao->consultar( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            if ( $this->roREmpenhoEmpenho->obREmpenhoConfiguracao->getNumeracao() == 'P' ) {
                $this->obTEmpenhoNotaLiquidacao->setComplementoChave( "cod_entidade,exercicio" );
                $this->obTEmpenhoNotaLiquidacao->setDado("cod_entidade" , $this->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade()   );
            } else {
                $this->obTEmpenhoNotaLiquidacao->setComplementoChave( "exercicio" );
                $this->obTEmpenhoNotaLiquidacao->setDado("cod_entidade" , null                                                                  );
            }

            $obErro = $this->obTEmpenhoNotaLiquidacao->proximoCod( $this->inCodNota, $boTransacao );
            $this->obTEmpenhoNotaLiquidacao->setDado("cod_entidade"     , $this->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade()   );
        }
        if ( !$obErro->ocorreu() ) {
            $obErro = $this->roREmpenhoEmpenho->consultar($boTransacao);
            if ( !$obErro->ocorreu() ) {
                $obErro = $this->listarMaiorData( $rsMaiorData,'',$boTransacao );
                if (!$obErro->ocorreu()) {
                    $maiorDataLiquidacao = $rsMaiorData->getCampo('data_liquidacao');
                    if ($maiorDataLiquidacao) {
                        if (SistemaLegado::comparaDatas($maiorDataLiquidacao,$this->stDtLiquidacao)) {
                            $obErro->setDescricao( "A data de liquidação deve ser posterior ou igual a ".$maiorDataLiquidacao );
                        }
                    }

                    if (SistemaLegado::comparaDatas($this->roREmpenhoEmpenho->getDtEmpenho(),$this->stDtLiquidacao)) {
                        $obErro->setDescricao( "A data de liquidação deve ser posterior ou igual à data de empenho." );
                    }

                    if ($this->stDtVencimento == "") {
                        $obErro->setDescricao( "O campo data de vencimento deve ser preenchido." );
                    }

                    if ( !$obErro->ocorreu() ) {
                        $this->obTEmpenhoNotaLiquidacao->setDado("cod_nota"         , $this->inCodNota                          );
                        $this->obTEmpenhoNotaLiquidacao->setDado("cod_empenho"      , $this->roREmpenhoEmpenho->getCodEmpenho() );
                        $this->obTEmpenhoNotaLiquidacao->setDado("exercicio_empenho", $this->roREmpenhoEmpenho->getExercicio()  );
                        $this->obTEmpenhoNotaLiquidacao->setDado("dt_vencimento"    , $this->stDtVencimento                     );
                        $this->obTEmpenhoNotaLiquidacao->setDado("dt_liquidacao"    , $this->stDtLiquidacao                     );
                        $this->obTEmpenhoNotaLiquidacao->setDado("observacao"       , $this->stObservacao                       );

                        // Inclui o registro em nota_liquidacao
                        $obErro = $this->obTEmpenhoNotaLiquidacao->inclusao( $boTransacao );

                        if ( !$obErro->ocorreu() ) {
                            $stTimestamp = substr($this->getDtLiquidacao(),6,4). "-" . substr($this->getDtLiquidacao(),3,2). "-" . substr($this->getDtLiquidacao(),0,2)  . date (" H:i:s.") . str_pad(1, 3, "0", STR_PAD_LEFT);
                            if ($stTimestamp) {
                                $arChaveAtributoLiquidacao =  array( "cod_entidade" => $this->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade(),
                                                                     "cod_nota"     => $this->inCodNota,
                                                                     "exercicio"    => $this->stExercicio,
                                                                     "timestamp"    => $stTimestamp );
                            } else {
                                $arChaveAtributoLiquidacao =  array( "cod_entidade" => $this->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade(),
                                                                     "cod_nota"     => $this->inCodNota,
                                                                     "exercicio"    => $this->stExercicio );
                            }
                            $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoLiquidacao );
                            $obErro = $this->obRCadastroDinamico->salvarValores( $boTransacao );
                            if ( !$obErro->ocorreu() ) {
                                $this->obRCadastroDinamico = new RCadastroDinamico();
                                $this->obRCadastroDinamico->setPersistenteValores       ( new TEmpenhoAtributoLiquidacaoValor   );
                                $this->obRCadastroDinamico->setCodCadastro              ( 2                                     );
                                $this->obRCadastroDinamico->obRModulo->setCodModulo     ( 10                                    );
                                $this->obRCadastroDinamico->setChavePersistenteValores  ( $arChaveAtributoLiquidacao            );
                                $obErro = $this->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributosLiquidacao, '','', $boTransacao );
                                if ( !$obErro->ocorreu() ) {
                                    while ( !$rsAtributosLiquidacao->eof() ) {
                                        if ( in_array( $rsAtributosLiquidacao->getCampo( 'cod_atributo' ), array( 104,105,106,107,108,109 ) ) ) {
                                            if ( $rsAtributosLiquidacao->getCampo('valor') ) {
                                                if ( !$this->inCodHistorico )
                                                    $obErro->setDescricao( 'Para realizar um lançamento patrimonial, é necessário selecionar um histórico!' );
                                            }
                                        }
                                        if ( $obErro->ocorreu() )
                                            break;

                                        $rsAtributosLiquidacao->proximo();
                                    }
                                }
                            }
                            if ( !$obErro->ocorreu() ) {
                                // Trata os itens do empenho liquidado
                                $obErro = $this->liquidarItens( $boTransacao );
                            }

                            if ( !$obErro->ocorreu() ) {
                                if ( $this->roREmpenhoEmpenho->getExercicio() == $this->stExercicio ) {
                                    $obFEmpenhoEmpenhoLiquidacao->setDado( "exercicio"              , $this->stExercicio                                                                                                );
                                    $obFEmpenhoEmpenhoLiquidacao->setDado( "valor"                  , $this->nuVlTotal                                                                                                  );
                                    $obFEmpenhoEmpenhoLiquidacao->setDado( "nom_lote"               , "Liquidação Empenho n° ".$this->roREmpenhoEmpenho->getCodEmpenho().'/'.$this->roREmpenhoEmpenho->getExercicio()   );
                                    $obFEmpenhoEmpenhoLiquidacao->setDado( "tipo_lote"              , "L"                                                                                                               );
                                    $obFEmpenhoEmpenhoLiquidacao->setDado( "complemento"            ,  $this->roREmpenhoEmpenho->getCodEmpenho()."/".$this->stExercicio." ".$this->getComplemento()                     );
                                    $obFEmpenhoEmpenhoLiquidacao->setDado( "dt_lote"                , $this->getDtLiquidacao()                                                                                          );
                                    $obFEmpenhoEmpenhoLiquidacao->setDado( "cod_entidade"           , $this->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade()                                               );
                                    $obFEmpenhoEmpenhoLiquidacao->setDado( "cod_nota"               , $this->inCodNota                                                                                                  );
                                    $obFEmpenhoEmpenhoLiquidacao->setDado( "conta_contabil_financ"  , $this->inCodContaContabilFinanc                                                                                   );

                                    // Contas Débito e Crédito
                                    $obFEmpenhoEmpenhoLiquidacao->setDado( "conta_debito"           , $this->obRContabilidadePlanoContaAnaliticaDebito->getCodEstrutural()  );
                                    $obFEmpenhoEmpenhoLiquidacao->setDado( "conta_credito"          , $this->obRContabilidadePlanoContaAnaliticaCredito->getCodEstrutural() );

                                    $obFEmpenhoEmpenhoLiquidacao->setDado( "cod_historico_patrimon" , $this->inCodHistorico                                                                                                             );
                                    $obFEmpenhoEmpenhoLiquidacao->setDado( "num_orgao"              , $this->roREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->getNumeroOrgao()   );

                                    if (Sessao::getExercicio() > '2012') {
                                        $obFEmpenhoEmpenhoLiquidacao->setDado( "cod_despesa"        , $this->roREmpenhoEmpenho->obROrcamentoDespesa->getCodDespesa()        );
                                        $obFEmpenhoEmpenhoLiquidacao->setDado( "cod_classificacao"  , $this->inCodContaContabilFinanc                                       );
                                    }

                                    // executaFuncao leva a montaInsereLote. Inclui o novo lote na base
                                    $obErro = $obFEmpenhoEmpenhoLiquidacao->executaFuncao( $rsRecordSet, $boTransacao );

                                    // Retorna sequencia e cod_lote para uso posterior
                                    $inSequencia = $obFEmpenhoEmpenhoLiquidacao->getDado( 'sequencia' );
                                    $inCodLote   = $obFEmpenhoEmpenhoLiquidacao->getDado( 'cod_lote'  );
                                } else {
                                    $obFEmpenhoEmpenhoLiquidacaoRestosAPagar->setDado( "exercicio"      , $this->stExercicio                                                                                                    );
                                    $obFEmpenhoEmpenhoLiquidacaoRestosAPagar->setDado( "valor"          , $this->nuVlTotal                                                                                                      );
                                    $obFEmpenhoEmpenhoLiquidacaoRestosAPagar->setDado( "complemento"    , $this->roREmpenhoEmpenho->getCodEmpenho()."/".$this->roREmpenhoEmpenho->getExercicio()                                );
                                    $obFEmpenhoEmpenhoLiquidacaoRestosAPagar->setDado( "nom_lote"       , "Liquidação Empenho RP n° ".$this->roREmpenhoEmpenho->getCodEmpenho().'/'.$this->roREmpenhoEmpenho->getExercicio()    );
                                    $obFEmpenhoEmpenhoLiquidacaoRestosAPagar->setDado( "tipo_lote"      , "L"                                                                                                                   );
                                    $obFEmpenhoEmpenhoLiquidacaoRestosAPagar->setDado( "dt_lote"        , $this->getDtLiquidacao()                                                                                              );
                                    $obFEmpenhoEmpenhoLiquidacaoRestosAPagar->setDado( "cod_entidade"   , $this->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade()                                                   );
                                    $obFEmpenhoEmpenhoLiquidacaoRestosAPagar->setDado( "cod_nota"       , $this->inCodNota                                                                                                      );
                                    $obFEmpenhoEmpenhoLiquidacaoRestosAPagar->setDado( "exerc_rp"       , $this->roREmpenhoEmpenho->getExercicio()                                                                              );

                                    // executaFuncao leva a montaInsereLote
                                    // que inclui o novo lote na base
                                    $obErro = $obFEmpenhoEmpenhoLiquidacaoRestosAPagar->executaFuncao( $rsRecordSet, $boTransacao );

                                    // Retorna sequencia e cod_lote para uso posterior
                                    $inSequencia = $obFEmpenhoEmpenhoLiquidacaoRestosAPagar->getDado( 'sequencia' );
                                    $inCodLote   = $obFEmpenhoEmpenhoLiquidacaoRestosAPagar->getDado( 'cod_lote'  );
                                }
                            }
                            if ( !$obErro->ocorreu() ) {
                                $obTContabilidadeLancamentoEmpenho->setDado( 'cod_lote'     , $inCodLote                                                                    );
                                $obTContabilidadeLancamentoEmpenho->setDado( 'tipo'         , 'L'                                                                           );
                                $obTContabilidadeLancamentoEmpenho->setDado( 'sequencia'    , $inSequencia                                                                  );
                                $obTContabilidadeLancamentoEmpenho->setDado( 'exercicio'    , $this->stExercicio                                                            );
                                $obTContabilidadeLancamentoEmpenho->setDado( 'cod_entidade' , $this->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade()           );
                                $obTContabilidadeLancamentoEmpenho->setDado( 'estorno'      , false                                                                         );
                                $obErro = $obTContabilidadeLancamentoEmpenho->inclusao( $boTransacao );
                            }
                            if ( !$obErro->ocorreu() ) {
                                if (   $this->obRContabilidadePlanoContaAnaliticaCredito->getCodPlano() > 0
                                    && $this->obRContabilidadePlanoContaAnaliticaDebito->getCodPlano() > 0 )
                                {
                                    $obTEmpenhoIncorporacaoPatrimonio->setDado( 'exercicio'         , $this->stExercicio                                                    );
                                    $obTEmpenhoIncorporacaoPatrimonio->setDado( 'cod_entidade'      , $this->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade()   );
                                    $obTEmpenhoIncorporacaoPatrimonio->setDado( 'cod_nota'          , $this->inCodNota                                                      );
                                    $obTEmpenhoIncorporacaoPatrimonio->setDado( 'cod_plano_credito' , $this->obRContabilidadePlanoContaAnaliticaCredito->getCodPlano()      );
                                    $obTEmpenhoIncorporacaoPatrimonio->setDado( 'cod_plano_debito'  , $this->obRContabilidadePlanoContaAnaliticaDebito->getCodPlano()       );
                                    $obErro = $obTEmpenhoIncorporacaoPatrimonio->inclusao( $boTransacao );
                                }
                            }
                            if ( !$obErro->ocorreu() ) {
                                $obTContabilidadeLiquidacao->setDado( 'exercicio'           , $this->stExercicio                                                            );
                                $obTContabilidadeLiquidacao->setDado( 'exercicio_liquidacao', $this->stExercicio                                                            );
                                $obTContabilidadeLiquidacao->setDado( 'sequencia'           , $inSequencia                                                                  );
                                $obTContabilidadeLiquidacao->setDado( 'tipo'                , 'L'                                                                           );
                                $obTContabilidadeLiquidacao->setDado( 'cod_lote'            , $inCodLote                                                                    );
                                $obTContabilidadeLiquidacao->setDado( 'cod_entidade'        , $this->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade()           );
                                $obTContabilidadeLiquidacao->setDado( 'cod_nota'            , $this->inCodNota                                                              );
                                $obErro = $obTContabilidadeLiquidacao->inclusao( $boTransacao );
                            }
                        }
                    }
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTEmpenhoNotaLiquidacao );

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
    $obTEmpenhoNotaLiquidacao                        = new TEmpenhoNotaLiquidacao;

    $boFlagTransacao = false;
    $boFlagNovaClassificacao = true;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTEmpenhoNotaLiquidacao->setDado("cod_nota"     ,$this->inCodNota );
        $obTEmpenhoNotaLiquidacao->setDado("cod_empenho"  ,$this->roREmpenhoEmpenho->getCodEmpenho() );
        $obTEmpenhoNotaLiquidacao->setDado("cod_entidade" ,$this->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() );
        $obTEmpenhoNotaLiquidacao->setDado("exercicio"    ,$this->stExercicio );
        $obTEmpenhoNotaLiquidacao->setDado("dt_vencimento",$this->stDtVencimento );
        $obTEmpenhoNotaLiquidacao->setDado("observacao"   ,$this->stObservacao );
        $obErro = $obTEmpenhoNotaLiquidacao->alteracao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $arChaveAtributoLiquidacao =  array( "cod_entidade" => $this->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade(),
                                                 "cod_nota"     => $this->inCodNota,
                                                 "exercicio"    => $this->stExercicio          );
            $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoLiquidacao );
            $obErro = $this->obRCadastroDinamico->salvarValores( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTEmpenhoNotaLiquidacao );

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
function recuperaTimestampAnuladoLiquidacao(&$rsRecordSet, $stFiltro, $boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoNotaLiquidacaoItemAnulado.class.php"             );
    $obTEmpenhoNotaLiquidacaoItemAnulado             = new TEmpenhoNotaLiquidacaoItemAnulado;

    $stFiltro = " WHERE " . $stFiltro;
    $obErro = $obTEmpenhoNotaLiquidacaoItemAnulado->recuperaTimestampAnuladoLiquidacao( $rsRecordSet, $stFiltro, $boTransacao );

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
    $obTEmpenhoNotaLiquidacao                        = new TEmpenhoNotaLiquidacao;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() and $this->inCodConta ) {
        $arChaveAtributoLiquidacao =  array( "cod_entidade" => $this->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade(),
                                             "cod_nota"     => $this->inCodNota,
                                             "exercicio"    => $this->stExercicio          );
        $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoLiquidacao );
        $obErro = $this->obRCadastroDinamico->excluirValores( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obTEmpenhoNotaLiquidacao->setDado("cod_nota"     ,$this->inCodNota );
            $obTEmpenhoNotaLiquidacao->setDado("cod_empenho"  ,$this->roREmpenhoEmpenho->getCodEmpenho() );
            $obTEmpenhoNotaLiquidacao->setDado("cod_entidade" ,$this->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() );
            $obTEmpenhoNotaLiquidacao->setDado("exercicio"    ,$this->stExercicio );
            $obErro = $obTEmpenhoNotaLiquidacao->exclusao( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro , $obTEmpenhoNotaLiquidacao );

    return $obErro;
}

function liquidarItens($boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoNotaLiquidacaoItem.class.php"                    );
    $obTEmpenhoNotaLiquidacaoItem                    = new TEmpenhoNotaLiquidacaoItem;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->roREmpenhoEmpenho->consultarValorItem( $boTransacao );
        if ( !$obErro->ocorreu() ) {
           //Verifica o total dos itens
           $nuTotalLiquidar = 0;
           $boTotalZero     = false;
            foreach ($this->roREmpenhoEmpenho->arItemPreEmpenho  as $obEmpenhoItemPreEmpenho) {
                if ( $obEmpenhoItemPreEmpenho->getValorALiquidar() ) {
                    $nuValorALiquidar = str_replace( ".","", $obEmpenhoItemPreEmpenho->getValorALiquidar() );
                    $nuValorALiquidar = str_replace( ",",".",$nuValorALiquidar );

                    $nuTotalLiquidar += $nuValorALiquidar;
                }
            }
            if ( (float) $nuTotalLiquidar <= 0.00 ) {
                $boTotalZero = true;
            }
            //fim da verificacao
            $this->nuVlTotal=$nuTotalLiquidar;
            foreach ($this->roREmpenhoEmpenho->arItemPreEmpenho  as $obEmpenhoItemPreEmpenho) {
                if ( $obEmpenhoItemPreEmpenho->getValorALiquidar() ) {

                    $nuValorEmpenhadoReal = bcsub( $obEmpenhoItemPreEmpenho->getValorTotal(), $obEmpenhoItemPreEmpenho->getValorEmpenhadoAnulado(), 2);
                    $nuValorLiquidadoReal = bcsub( $obEmpenhoItemPreEmpenho->getValorLiquidado(), $obEmpenhoItemPreEmpenho->getValorLiquidadoAnulado(),2 );
                    $nuValorTotal = bcsub( $nuValorEmpenhadoReal, $nuValorLiquidadoReal ,2);
                    $nuValorALiquidar = str_replace( ".","", $obEmpenhoItemPreEmpenho->getValorALiquidar() );
                    $nuValorALiquidar = str_replace( ",",".", $nuValorALiquidar );

                    if (!$boTotalZero) {
                        if ( $nuValorTotal >=  (float) $nuValorALiquidar ) {
                            $obTEmpenhoNotaLiquidacaoItem->setDado( "exercicio",       $this->stExercicio );
                            $obTEmpenhoNotaLiquidacaoItem->setDado( "cod_nota",        $this->inCodNota  );
                            $obTEmpenhoNotaLiquidacaoItem->setDado( "cod_entidade",    $this->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() );
                            $obTEmpenhoNotaLiquidacaoItem->setDado( "exercicio_item",  $obEmpenhoItemPreEmpenho->roPreEmpenho->getExercicio() );
                            $obTEmpenhoNotaLiquidacaoItem->setDado( "num_item",        $obEmpenhoItemPreEmpenho->getNumItem()  );
                            $obTEmpenhoNotaLiquidacaoItem->setDado( "cod_pre_empenho", $obEmpenhoItemPreEmpenho->roPreEmpenho->getCodPreEmpenho() );
                            $obTEmpenhoNotaLiquidacaoItem->setDado( "vl_total",        $obEmpenhoItemPreEmpenho->getValorALiquidar() );
                            $obErro = $obTEmpenhoNotaLiquidacaoItem->inclusao( $boTransacao );
                        } else {
                            $obErro->setDescricao( "Valor a liquidar não pode ser maior que o saldo!" );
                        }
                    } else {
                        $obErro->setDescricao( "Valor a liquidar deve ser maior que 0 (zero)!" );
                    }
                    if ( $obErro->ocorreu() ) {
                        break;
                    }
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro , $obTEmpenhoNotaLiquidacaoItem );

    return $obErro;
}

function anularItens($boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoNotaLiquidacaoItemAnulado.class.php" );
    include_once ( CAM_GF_EMP_MAPEAMENTO."FEmpenhoEmpenhoLiquidacaoAnulacao.class.php" );
    include_once ( CAM_GF_EMP_MAPEAMENTO."FEmpenhoEmpenhoLiquidacaoAnulacaoTCEMS.class.php" );
    include_once ( CAM_GF_EMP_MAPEAMENTO."FEmpenhoEmpenhoLiquidacaoRestosAPagarAnulacao.class.php" );
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeLiquidacao.class.php" );
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamentoEmpenho.class.php" );
    $obTContabilidadeLancamentoEmpenho = new TContabilidadeLancamentoEmpenho;
    $obTContabilidadeLiquidacao = new TContabilidadeLiquidacao;
    $obFEmpenhoEmpenhoLiquidacaoRestosAPagarAnulacao = new FEmpenhoEmpenhoLiquidacaoRestosAPagarAnulacao;
    $obTEmpenhoNotaLiquidacaoItemAnulado = new TEmpenhoNotaLiquidacaoItemAnulado;
    $obTEmpenhoIncorporacaoPatrimonio = new TEmpenhoIncorporacaoPatrimonio;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );    

    if (Sessao::getExercicio() > '2012') {
        $obFEmpenhoEmpenhoLiquidacaoAnulacao = new FEmpenhoEmpenhoLiquidacaoAnulacaoTCEMS;
    } else {
        $obFEmpenhoEmpenhoLiquidacaoAnulacao = new FEmpenhoEmpenhoLiquidacaoAnulacao;
    }

    if ( !$obErro->ocorreu() ) {
        $this->roREmpenhoEmpenho->setCodLiquidacaoInicial( $this->inCodNota );
        $obErro = $this->roREmpenhoEmpenho->consultarValorNotaItem( $boTransacao );

        if ( !$obErro->ocorreu() ) {

            $obErro = $this->consultar($boTransacao);

            if ( !$obErro->ocorreu() ) {
                if (SistemaLegado::comparaDatas($this->getDtLiquidacao(),$this->getDtEstornoLiquidacao())) {
                    $obErro->setDescricao( "A data da anulação de liquidação deve ser posterior ou igual à data de liquidação." );
                }
                if ( !$obErro->ocorreu() ) {
                    $milisegundos = 1;
                    $stDataEstornoLiquidacao = substr($this->getDtEstornoLiquidacao(),6,4) . "-" . substr($this->getDtEstornoLiquidacao(),3,2)."-" . substr($this->getDtEstornoLiquidacao(),0,2)  . date (" H:i:s.") . str_pad($milisegundos, 3, "0", STR_PAD_LEFT);
                    $boLiberado = false;
                    while (!$boLiberado) {
                        $obErro = $this->recuperaTimestampAnuladoLiquidacao($rsTimestampAnulado, " timestamp = '$stDataEstornoLiquidacao' ", $boTransacao);
                        if ( !$obErro->ocorreu() ) {
                            if ( $rsTimestampAnulado->getCampo('timestampAnulado') ) {
                                $milisegundos++;
                                if ( $milisegundos > 999 )
                                    $milisegundos = 1;
                                $stDataEstornoLiquidacao = substr($this->getDtEstornoLiquidacao(),6,4) . "-" . substr($this->getDtEstornoLiquidacao(),3,2)."-" . substr($this->getDtEstornoLiquidacao(),0,2)  . date (" H:i:s.") . str_pad($milisegundos, 3, "0", STR_PAD_LEFT);
                            } else {
                                $boLiberado = true;
                            }
                        }
                    }

                    $nuVlTotal      = 0;
                    $nuVlMaxAAnular = 0;
                    $arValorItens   = array();
                    $obErro = $this->listarItensAnulacao( $rsRecordSet, $boTransacao );

                    if (!$obErro->ocorreu()) {
                        while ( !$rsRecordSet->eof() ) {
                            $arValorItens[trim($rsRecordSet->getCampo("num_item"))] = $rsRecordSet->getCampo("total_a_anular");
                            $rsRecordSet->proximo();
                        }
                    }
                    $entrou = 0;

                    foreach ($this->roREmpenhoEmpenho->arItemPreEmpenho  as $obEmpenhoItemPreEmpenho) {
                        $nuValorAAnular = str_replace( ".","", $obEmpenhoItemPreEmpenho->getValorAAnular() );
                        $nuValorAAnular = str_replace( ",",".", $nuValorAAnular );
                        $nuVlMaxAAnular = $arValorItens[trim($obEmpenhoItemPreEmpenho->getNumItem())];
                        if ($nuValorAAnular > 0) {
                            $entrou = 1;
                            if ($nuValorAAnular <= $nuVlMaxAAnular) {
                                $nuValorEmpenhadoReal = bcsub( $obEmpenhoItemPreEmpenho->getValorTotal(), $obEmpenhoItemPreEmpenho->getValorEmpenhadoAnulado(),2 );
                                $nuValorLiquidadoReal = bcsub( $obEmpenhoItemPreEmpenho->getValorLiquidado(), $obEmpenhoItemPreEmpenho->getValorLiquidadoAnulado(),2 );
                                $nuValorTotal = bcsub( $nuValorEmpenhadoReal, $nuValorLiquidadoReal ,2);
                                $nuVlTotal += $nuValorAAnular;
                                if ( $nuValorLiquidadoReal >=  (float) $nuValorAAnular ) {
                                    $obTEmpenhoNotaLiquidacaoItemAnulado->setDado( "exercicio",       $this->stExercicio );
                                    $obTEmpenhoNotaLiquidacaoItemAnulado->setDado( "cod_nota",        $this->inCodNota  );
                                    $obTEmpenhoNotaLiquidacaoItemAnulado->setDado( "cod_entidade",    $this->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() );
                                    $obTEmpenhoNotaLiquidacaoItemAnulado->setDado( "exercicio_item",  $obEmpenhoItemPreEmpenho->roPreEmpenho->getExercicio() );
                                    $obTEmpenhoNotaLiquidacaoItemAnulado->setDado( "num_item",        $obEmpenhoItemPreEmpenho->getNumItem()  );
                                    $obTEmpenhoNotaLiquidacaoItemAnulado->setDado( "cod_pre_empenho", $obEmpenhoItemPreEmpenho->roPreEmpenho->getCodPreEmpenho() );
                                    $obTEmpenhoNotaLiquidacaoItemAnulado->setDado( "vl_anulado",      $nuValorAAnular );
                                    $obTEmpenhoNotaLiquidacaoItemAnulado->setDado( "timestamp",       $stDataEstornoLiquidacao  );
                                    $obErro = $obTEmpenhoNotaLiquidacaoItemAnulado->inclusao( $boTransacao );
                                } else {
                                    $obErro->setDescricao( "Valor a anular não pode ser maior que o valor liquidado!" );
                                }
                                if ( $obErro->ocorreu() ) {
                                    break;
                                }
                            } else {
                                $obErro->setDescricao( "Valor a anular informado é maior que o disponível!" );
                            }
                        }
                    }
                }
                if ( !$obErro->ocorreu() ) {
                    if ($entrou==0) {
                        $obErro->setDescricao( "Valor a anular deve ser maior que zero!" );
                    }
                }
                if ( !$obErro->ocorreu() ) {
                    if( $this->stExercicio )
                        $stFiltro .= " exercicio = '".$this->stExercicio."' AND ";
                     if( $this->inCodEntidade )
                        $stFiltro .= " cod_entidade = ".$this->inCodEntidade." AND ";
                     if( $this->inCodNota )
                        $stFiltro .= " cod_nota = ".$this->inCodNota." AND ";
                    $stFiltro = ( $stFiltro ) ? ' WHERE '.substr( $stFiltro, 0, strlen( $stFiltro ) -4 ) : '' ;
                    $obErro = $obTEmpenhoNotaLiquidacaoItemAnulado->recuperaTodos( $rsRecordSet, $stFiltro, ' ORDER BY timestamp DESC LIMIT 1', $boTransacao );
                    if ( !$obErro->ocorreu() ) {
                        $this->stTimestamp = $rsRecordSet->getCampo( 'timestamp' );
                    }
                }
            }
        }
        $this->nuVlTotal = $nuVlTotal;
        if ( !$obErro->ocorreu() ) {
            if ( $this->roREmpenhoEmpenho->getExercicio() == $this->stExercRP ) {
                $obFEmpenhoEmpenhoLiquidacaoAnulacao->setDado( "exercicio", $this->stExercicio );
                $obFEmpenhoEmpenhoLiquidacaoAnulacao->setDado( "valor", $this->nuVlTotal );
                $obFEmpenhoEmpenhoLiquidacaoAnulacao->setDado( "nom_lote", "Anulação Liquidação Empenho nº ".$this->roREmpenhoEmpenho->getCodEmpenho().'/'.$this->roREmpenhoEmpenho->getExercicio() );
                $obFEmpenhoEmpenhoLiquidacaoAnulacao->setDado( "tipo_lote", "L" );
                $obFEmpenhoEmpenhoLiquidacaoAnulacao->setDado( "complemento", $this->roREmpenhoEmpenho->getCodEmpenho()."/".$this->stExercicio." ".$this->getComplemento()  );
                $obFEmpenhoEmpenhoLiquidacaoAnulacao->setDado( "dt_lote", $this->getDtEstornoLiquidacao() );
                $obFEmpenhoEmpenhoLiquidacaoAnulacao->setDado( "cod_entidade", $this->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() );
                $obFEmpenhoEmpenhoLiquidacaoAnulacao->setDado( "cod_nota", $this->inCodNota );
                $obFEmpenhoEmpenhoLiquidacaoAnulacao->setDado( "conta_contabil_financ", $this->inCodContaContabilFinanc );
                $obFEmpenhoEmpenhoLiquidacaoAnulacao->setDado( "cod_historico_patrimon", $this->inCodHistorico );
                $obFEmpenhoEmpenhoLiquidacaoAnulacao->setDado( "num_orgao", $this->roREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->getNumeroOrgao() );

                if (Sessao::getExercicio() > '2012') {
                    $obFEmpenhoEmpenhoLiquidacaoAnulacao->setDado( "cod_despesa", $this->roREmpenhoEmpenho->obROrcamentoDespesa->getCodDespesa() );
                    $obFEmpenhoEmpenhoLiquidacaoAnulacao->setDado( "cod_classificacao",$this->inCodContaContabilFinanc);
                }

                // Seta as Contas Débito e Crédito
                $obErro = $this->recuperaContasIncorporacaoPatrimonial( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $obFEmpenhoEmpenhoLiquidacaoAnulacao->setDado( "conta_debito", $this->obRContabilidadePlanoContaAnaliticaDebito->getCodEstrutural() );
                    $obFEmpenhoEmpenhoLiquidacaoAnulacao->setDado( "conta_credito", $this->obRContabilidadePlanoContaAnaliticaCredito->getCodEstrutural() );
                }
                Sessao::setTrataExcecao(true);
                $obErro = $obFEmpenhoEmpenhoLiquidacaoAnulacao->executaFuncao( $rsRecordSet, $boTransacao );
                Sessao::encerraExcecao();

                $inSequencia = $obFEmpenhoEmpenhoLiquidacaoAnulacao->getDado( 'sequencia' );
                $inCodLote = $obFEmpenhoEmpenhoLiquidacaoAnulacao->getDado( 'cod_lote' );
            } else {
                $obFEmpenhoEmpenhoLiquidacaoRestosAPagarAnulacao->setDado( "exercicio", $this->stExercRP );
                $obFEmpenhoEmpenhoLiquidacaoRestosAPagarAnulacao->setDado( "valor", $this->nuVlTotal );
                $obFEmpenhoEmpenhoLiquidacaoRestosAPagarAnulacao->setDado( "complemento", $this->roREmpenhoEmpenho->getCodEmpenho()."/".$this->roREmpenhoEmpenho->getExercicio() );
                $obFEmpenhoEmpenhoLiquidacaoRestosAPagarAnulacao->setDado( "nom_lote", "Anulação Liquidação Empenho RP nº ".$this->roREmpenhoEmpenho->getCodEmpenho().'/'.$this->roREmpenhoEmpenho->getExercicio() );
                $obFEmpenhoEmpenhoLiquidacaoRestosAPagarAnulacao->setDado( "tipo_lote", "L" );
                $obFEmpenhoEmpenhoLiquidacaoRestosAPagarAnulacao->setDado( "dt_lote", $this->getDtEstornoLiquidacao() );
                $obFEmpenhoEmpenhoLiquidacaoRestosAPagarAnulacao->setDado( "cod_entidade", $this->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() );
                $obFEmpenhoEmpenhoLiquidacaoRestosAPagarAnulacao->setDado( "cod_nota", $this->inCodNota );
                $obFEmpenhoEmpenhoLiquidacaoRestosAPagarAnulacao->setDado( "exerc_rp", $this->roREmpenhoEmpenho->getExercicio() );
                $obFEmpenhoEmpenhoLiquidacaoRestosAPagarAnulacao->setDado( "exerc_liquidacao" , $this->stExercicio );
                $obErro = $obFEmpenhoEmpenhoLiquidacaoRestosAPagarAnulacao->executaFuncao( $rsRecordSet, $boTransacao );
                $inSequencia = $obFEmpenhoEmpenhoLiquidacaoRestosAPagarAnulacao->getDado( 'sequencia' );
                $inCodLote   = $obFEmpenhoEmpenhoLiquidacaoRestosAPagarAnulacao->getDado( 'cod_lote'  );
            }
            if ( !$obErro->ocorreu() ) {
                $obTContabilidadeLancamentoEmpenho->setDado( 'cod_lote', $inCodLote );
                $obTContabilidadeLancamentoEmpenho->setDado( 'tipo', 'L' );
                $obTContabilidadeLancamentoEmpenho->setDado( 'sequencia'   , $inSequencia );
                $obTContabilidadeLancamentoEmpenho->setDado( 'exercicio', $this->stExercRP );
                $obTContabilidadeLancamentoEmpenho->setDado( 'cod_entidade', $this->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() );
                $obTContabilidadeLancamentoEmpenho->setDado( 'estorno', true );
                $obErro = $obTContabilidadeLancamentoEmpenho->inclusao( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $obTContabilidadeLiquidacao->setDado( 'exercicio', $this->stExercRP );
                    $obTContabilidadeLiquidacao->setDado( 'exercicio_liquidacao', $this->stExercicio );
                    $obTContabilidadeLiquidacao->setDado( 'sequencia', $inSequencia );
                    $obTContabilidadeLiquidacao->setDado( 'tipo', 'L' );
                    $obTContabilidadeLiquidacao->setDado( 'cod_lote', $inCodLote );
                    $obTContabilidadeLiquidacao->setDado( 'cod_entidade', $this->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() );
                    $obTContabilidadeLiquidacao->setDado( 'cod_nota', $this->inCodNota );
                    $obErro = $obTContabilidadeLiquidacao->inclusao( $boTransacao );
                }
            }
        }
    }

    $this->setTimestamp( $stDataEstornoLiquidacao );
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro , $obTEmpenhoNotaLiquidacaoItemAnulado );

    return $obErro;
}

function listarItensAnulacao(&$rsRecordSet, $boTransacao = "")
{
    $obTEmpenhoNotaLiquidacao                        = new TEmpenhoNotaLiquidacao;

    if ($this->inCodNota) {
        $stFiltro .= " AND nl.cod_nota = ".$this->inCodNota;
    }
    if ($this->stExercicio) {
        $stFiltro .= " AND nl.exercicio = '".$this->stExercicio."' ";
    }
    if ( $this->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() ) {
        $stFiltro .= " AND nl.cod_entidade = ".$this->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade()." ";
    }
    $stOrdem = " nli.cod_nota, nli.num_item";
    $obErro = $obTEmpenhoNotaLiquidacao->recuperaItensAnulacao( $rsRecordSet, $stFiltro, $stOrdem , $boTransacao );

    $iAnular = 0;

    foreach ($rsRecordSet->arElementos as $chave => $elemento) {
        if ($elemento['total_a_anular'] == 0.00) {
            $iAnular++;
        }
    }

    if ($iAnular == $rsRecordSet->getNumLinhas()) {
        $rsRecordSet->setCampo('total_a_anular',$rsRecordSet->getCampo('restante_anular'));
    }

    return $obErro;
}

function listarNotaLiquidacaoEmpenho(&$rsRecordSet, $boTransacao = "")
{
    $obTEmpenhoNotaLiquidacao                        = new TEmpenhoNotaLiquidacao;

    if ($this->roREmpenhoEmpenho->inCodPreEmpenho) {
        $stFiltro .= " AND em.cod_pre_empenho = ".$this->roREmpenhoEmpenho->getCodPreEmpenho();
    }
    if ($this->inCodNota) {
        $stFiltro .= " AND nl.cod_nota = ".$this->inCodNota;
    }
    if ($this->stExercicio) {
        $stFiltro .= " AND nl.exercicio = '".$this->stExercicio."' ";
    }
    if ( $this->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() ) {
        $stFiltro .= " AND nl.cod_entidade = ".$this->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade()." ";
    }
    $stOrdem = " nl.cod_nota, li.num_item";
    $obErro = $obTEmpenhoNotaLiquidacao->recuperaNotaLiquidacaoEmpenho( $rsRecordSet, $stFiltro, $stOrdem , $boTransacao );

    return $obErro;
}

function listarNotaLiquidacaoEmpenhoRestos(&$rsRecordSet, $boTransacao = "")
{
    $obTEmpenhoNotaLiquidacao                        = new TEmpenhoNotaLiquidacao;

    if ($this->roREmpenhoEmpenho->inCodPreEmpenho) {
        $stFiltro .= " AND it.cod_pre_empenho = ".$this->roREmpenhoEmpenho->getCodPreEmpenho();
    }
    if ($this->inCodNota) {
        $stFiltro .= " AND nl.cod_nota = ".$this->inCodNota;
    }
    if ($this->stExercicio) {
        $stFiltro .= " AND nl.exercicio = '".$this->stExercicio."' ";
    }
    if ( $this->roREmpenhoEmpenho->getExercicio() ) {
        $stFiltro .= " AND em.exercicio = '".$this->roREmpenhoEmpenho->getExercicio()."' ";
    }
    if ( $this->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() ) {
        $stFiltro .= " AND nl.cod_entidade = ".$this->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade()." ";
    }
    $stOrdem = " nl.cod_nota, li.num_item";
    $obErro = $obTEmpenhoNotaLiquidacao->recuperaNotaLiquidacaoEmpenhoRestos( $rsRecordSet, $stFiltro, $stOrdem , $boTransacao );

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
function listarLiquidados(&$rsRecordSet, $stOrder = "nli.cod_nota, nli.exercicio", $boTransacao = "")
{
    $obTEmpenhoNotaLiquidacao                        = new TEmpenhoNotaLiquidacao;

    if( $this->roREmpenhoEmpenho->getExercicio() )
        $stFiltro .= " AND e.exercicio = '".$this->roREmpenhoEmpenho->getExercicio()."' ";
    if( $this->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() )
        $stFiltro  .= " AND e.cod_entidade = ".$this->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade()."  ";
    if( $this->roREmpenhoEmpenho->getCodEmpenho() )
        $stFiltro  .= " AND e.cod_empenho = ".$this->roREmpenhoEmpenho->getCodEmpenho()." ";

    $stFiltro .= "
        GROUP BY
               nli.cod_nota
              ,nli.exercicio
              ,nli.cod_pre_empenho
              ,nli.exercicio_item
              ,to_char(nl.dt_liquidacao,'dd/mm/yyyy')
        ";

    $stFiltro = ($stFiltro) ? " AND " . substr($stFiltro, 4, strlen($stFiltro)) : "";
    $stOrder = ($stOrder) ? $stOrder : "nli.cod_nota, nli.exercicio";
    $obErro = $obTEmpenhoNotaLiquidacao->recuperaRelacionamentoLiquidados( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

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
function listarPagos(&$rsRecordSet, $stOrder = "op.exercicio, op.cod_ordem", $boTransacao = "")
{
    $obTEmpenhoNotaLiquidacao                        = new TEmpenhoNotaLiquidacao;

    if( $this->roREmpenhoEmpenho->getExercicio() )
        $stFiltro .= " AND e.exercicio = '".$this->roREmpenhoEmpenho->getExercicio()."' ";
    if( $this->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() )
        $stFiltro  .= " AND e.cod_entidade = ".$this->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade()."  ";
    if( $this->roREmpenhoEmpenho->getCodEmpenho() )
        $stFiltro  .= " AND e.cod_empenho = ".$this->roREmpenhoEmpenho->getCodEmpenho()." ";
    $stFiltro = ($stFiltro) ? " AND " . substr($stFiltro, 4, strlen($stFiltro)) : "";
    $stOrder = ($stOrder) ? $stOrder : "op.exercicio, op.cod_ordem";
    $obTEmpenhoNotaLiquidacao->setDado('exercicio_empenho' , $this->roREmpenhoEmpenho->getExercicio() );
    $obTEmpenhoNotaLiquidacao->setDado('exercicio',$this->stExercicio );
    $obErro = $obTEmpenhoNotaLiquidacao->recuperaRelacionamentoPagos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

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
    $obTEmpenhoNotaLiquidacao                   =  new TEmpenhoNotaLiquidacao;

    if( $this->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() )
        $stFiltro  .= " AND cod_entidade IN (".$this->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade()." ) ";

    if( $this->stExercicio )
        $stFiltro .= " AND exercicio = '".$this->stExercicio."' ";

    $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 4, strlen($stFiltro)) : "";

    $obTEmpenhoNotaLiquidacao->setDado('stExercicio',$this->stExercicio);
    $obTEmpenhoNotaLiquidacao->setDado('stDataEmpenho',$this->roREmpenhoEmpenho->getDtEmpenho());

    $obErro = $obTEmpenhoNotaLiquidacao->recuperaMaiorDataLiquidacao( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

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
function listarMaiorDataAnulacao(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    $obTEmpenhoNotaLiquidacao                   =  new TEmpenhoNotaLiquidacao;

    if( $this->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() )
        $stFiltro  .= " AND cod_entidade IN (".$this->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade()." ) ";

    if( $this->stExercicio )
        $stFiltro .= " AND date_part('year', timestamp ) = '".$this->stExercicio."' ";

    $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 4, strlen($stFiltro)) : "";

    $obTEmpenhoNotaLiquidacao->setDado('stExercicio',$this->stExercicio);
    $obTEmpenhoNotaLiquidacao->setDado('stDataLiquidacao',$this->stDtLiquidacao);

    $obErro = $obTEmpenhoNotaLiquidacao->recuperaMaiorDataLiquidacaoAnulacao( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

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
function listarMaiorDataAnulacaoEmpenho(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    $obTEmpenhoNotaLiquidacao                   =  new TEmpenhoNotaLiquidacao;

    $stFiltro = "";

    if( $this->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() )
        $stFiltro  .= " AND e.cod_entidade IN (".$this->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade()." ) ";

    if( $this->roREmpenhoEmpenho->inCodEmpenho )
        $stFiltro .= " AND e.cod_empenho = ".$this->roREmpenhoEmpenho->inCodEmpenho;
    if( $this->roREmpenhoEmpenho->stExercicio )
        $stFiltro .= " AND e.exercicio = '".$this->roREmpenhoEmpenho->stExercicio."' ";

    // Sempre o último registro do exercício logado
    $stFiltro .= " AND e.exercicio = '" . Sessao::getExercicio() . "' ";

    $obErro = $obTEmpenhoNotaLiquidacao->recuperaMaiorDataLiquidacaoAnulacaoEmpenho( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Busca o Valor a pagar de uma Notas Específica
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function buscaValorAPagar($boTransacao)
{
    $obTEmpenhoNotaLiquidacao                   =  new TEmpenhoNotaLiquidacao;

    $stFiltro = "";
    $stOrder  = "";

    if( $this->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() )
        $stFiltro  .= " AND ntpg.cod_entidade IN (".$this->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade()." ) ";

    if( $this->stExercicio )
        $stFiltro .= " AND ntpg.exercicio_liquidacao = '".$this->stExercicio."' ";

    if ( $this->getCodNota() ) {
        $obTEmpenhoNotaLiquidacao->setDado( 'cod_nota' , $this->getCodNota() );
        $stFiltro .= " AND ntpg.cod_nota = ".$this->getCodNota() ." ";
    }

    if( $this->getCodOrdem() )
        $stFiltro .= " AND ntpg.cod_ordem = ".$this->getCodOrdem() ." ";

    $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 4, strlen($stFiltro)) : "";

    $obErro = $obTEmpenhoNotaLiquidacao->recuperaValores( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $rsRecordSet->setUltimoElemento();
        $vlAPagar = $rsRecordSet->getCampo( 'vl_a_pagar' );
        $this->setValorAPagar ( $vlAPagar );
    }

    return $obErro;
}

function recuperaContasIncorporacaoPatrimonial($boTransacao='')
{
    $obTEmpenhoIncorporacaoPatrimonio = new TEmpenhoIncorporacaoPatrimonio;
    $obTEmpenhoIncorporacaoPatrimonio->setDado( 'exercicio' , $this->stExercicio );
    $obTEmpenhoIncorporacaoPatrimonio->setDado( 'cod_entidade' , $this->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() );
    $obTEmpenhoIncorporacaoPatrimonio->setDado( 'cod_nota' , $this->inCodNota );
    $obErro = $obTEmpenhoIncorporacaoPatrimonio->consultar( $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $this->obRContabilidadePlanoContaAnaliticaDebito->setExercicio ( $this->stExercicio );
        $this->obRContabilidadePlanoContaAnaliticaDebito->setCodPlano ( $obTEmpenhoIncorporacaoPatrimonio->getDado('cod_plano_debito') );
        $obErro = $this->obRContabilidadePlanoContaAnaliticaDebito->consultar( $boTransacao );
    }
    if ( !$obErro->ocorreu() ) {
        $this->obRContabilidadePlanoContaAnaliticaCredito->setExercicio ( $this->stExercicio );
        $this->obRContabilidadePlanoContaAnaliticaCredito->setCodPlano ( $obTEmpenhoIncorporacaoPatrimonio->getDado('cod_plano_credito') );
        $obErro = $this->obRContabilidadePlanoContaAnaliticaCredito->consultar( $boTransacao );
    }

    return $obErro;
}

}
