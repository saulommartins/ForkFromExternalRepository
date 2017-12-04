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
    * Classe de Regra do Relatório de Situação de Empenho
    * Data de Criação   : 06/02/2006

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Cleisson da Silva Barboza

    * @package URBEM
    * @subpackage Regra

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.04.19
*/

/*
$Log$
Revision 1.5  2006/07/05 20:38:41  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
    * Classe de Regra de Negócios Conciliação Bancária
    * @author Desenvolvedor: Cleisson Barboza
*/
class RTesourariaConciliacaoManual
{
/**
    * @var String
    * @access Private
*/
var $stExercicio;
/**
    * @var String
    * @access Private
*/
var $dtDataLancamento;
/**
    * @var Numeric
    * @access Private
*/
var $nuValorLancamento;
/**
    * @var String
    * @access Private
*/
var $stTipoValor;
/**
    * @var String
    * @access Private
*/
var $stDescricao;
/*
    * @var Integer
    * @access Private
*/
var $inSequencia;
/*
    * @var Object
    * @access Private
*/
var $roRTesourariaConciliacao;

/**
     * @access Public
     * @param String $valor
*/
function setExercicio($valor) { $this->stExercicio           = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setDataLancamento($valor) { $this->dtDataLancamento      = $valor; }
/**
     * @access Public
     * @param Numeric $valor
*/
function setValorLancamento($valor) { $this->nuValorLancamento     = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setTipoValor($valor) { $this->stTipoValor           = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setDescricao($valor) { $this->stDescricao           = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setSequencia($valor) { $this->inSequencia           = $valor; }

/*
    * @access Public
    * @return String
*/
function getExercicio() { return $this->stExercicio;                     }
/*
    * @access Public
    * @return String
*/
function getDataLancamento() { return $this->dtDataLancamento;         }
/*
    * @access Public
    * @return Numeric
*/
function getValorLancamento() { return $this->nuValorLancamento;        }
/*
    * @access Public
    * @return String
*/
function getTipoValor() { return $this->stTipoValor;              }
/*
    * @access Public
    * @return String
*/
function getDescricao() { return $this->stDescricao;              }
/*
    * @access Public
    * @return Integer
*/
function getSequencia() { return $this->inSequencia;              }

/**
    * Método Construtor
    * @access Private
*/
function RTesourariaConciliacaoManual($roRTesourariaConciliacao)
{
    $this->roRTesourariaConciliacao    = &$roRTesourariaConciliacao;
}

/**
    * Salva os dados no banco de dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluir($boTransacao = "")
{
    include_once ( CAM_FW_BANCO_DADOS   ."Transacao.class.php"                      );
    include_once ( CAM_GF_TES_MAPEAMENTO."TTesourariaConciliacaoLancamentoManual.class.php"   );
    $obTransacao      =  new Transacao;
    $obTTesourariaConciliacaoManual =  new TTesourariaConciliacaoLancamentoManual;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->buscaProximaSequencia($boTransacao);
        if ( !$obErro->ocorreu() ) {
            $obTTesourariaConciliacaoManual->setDado( "cod_plano" ,     $this->roRTesourariaConciliacao->obRContabilidadePlanoBanco->getCodPlano());
            $obTTesourariaConciliacaoManual->setDado( "exercicio" ,     $this->roRTesourariaConciliacao->obRContabilidadePlanoBanco->getExercicio());
            $obTTesourariaConciliacaoManual->setDado( "mes",            $this->roRTesourariaConciliacao->getMes());
            $obTTesourariaConciliacaoManual->setDado( "sequencia",      $this->getSequencia());
            $obTTesourariaConciliacaoManual->setDado( "dt_lancamento",  $this->getDataLancamento());
            $obTTesourariaConciliacaoManual->setDado( "tipo_valor",     $this->getTipoValor());
            $obTTesourariaConciliacaoManual->setDado( "vl_lancamento",  $this->getValorLancamento());
            $obTTesourariaConciliacaoManual->setDado( "descricao",      $this->getDescricao());
            $obErro = $obTTesourariaConciliacaoManual->inclusao( $boTransacao );
        }
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTTesourariaConciliacaoManual );

    return $obErro;
}

/**
    * Apaga os dados do banco de dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluir($boTransacao = "")
{
    include_once ( CAM_FW_BANCO_DADOS   ."Transacao.class.php"                      );
    include_once ( CAM_GF_TES_MAPEAMENTO."TTesourariaConciliacaoLancamentoManual.class.php"   );
    $obTransacao      =  new Transacao;
    $obTTesourariaConciliacaoManual =  new TTesourariaConciliacaoLancamentoManual;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTTesourariaConciliacaoManual->setDado( "cod_plano" , $this->roRTesourariaConciliacao->obRContabilidadePlanoBanco->getCodPlano());
        $obTTesourariaConciliacaoManual->setDado( "exercicio" , $this->roRTesourariaConciliacao->obRContabilidadePlanoBanco->getExercicio());
        $obTTesourariaConciliacaoManual->setDado( "mes",        $this->roRTesourariaConciliacao->getMes()     );
        $obTTesourariaConciliacaoManual->setDado( "sequencia" , $this->getSequencia()                         );
        $obErro = $obTTesourariaConciliacaoManual->exclusao( $boTransacao );
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTTesourariaConciliacaoManual );

    return $obErro;
}

/**
    * Executa um proximoCodigo na Persistente
    * @access Public
    * @param Object $boTransacao
    * @return Object Objeto Erro
*/
function buscaProximaSequencia($boTransacao = "")
{
    include_once ( CAM_FW_BANCO_DADOS   ."Transacao.class.php"           );
    include_once ( CAM_GF_TES_MAPEAMENTO."TTesourariaConciliacaoLancamentoManual.class.php" );
    $obTransacao               =  new Transacao;
    $obTTesourariaConciliacaoManual =  new TTesourariaConciliacaoLancamentoManual;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $stFiltro = "WHERE cod_plano IS NOT NULL ";
        if($this->roRTesourariaConciliacao->obRContabilidadePlanoBanco->getCodPlano())
            $stFiltro .= " AND cod_plano = " . $this->roRTesourariaConciliacao->obRContabilidadePlanoBanco->getCodPlano();
        if($this->roRTesourariaConciliacao->obRContabilidadePlanoBanco->getExercicio())
            $stFiltro .= " AND exercicio = '" . $this->roRTesourariaConciliacao->obRContabilidadePlanoBanco->getExercicio()."'";
        if($this->roRTesourariaConciliacao->getMes())
            $stFiltro .= " AND mes = " .$this->roRTesourariaConciliacao->getMes() ;
       $obErro = $obTTesourariaConciliacaoManual->buscaProximaSequencia( $rsRecordSet, $stFiltro, $boTransacao );
       $this->setSequencia( $rsRecordSet->getCampo("sequencia") );
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTTesourariaConciliacaoManual );

    return $obErro;
}

/**
    * Executa um recuperatodos na persistencia
    * @access Public
    * @param Object $rsRecordSet
    * @param String $stOrder
    * @param Object $boTransacao
    * @return Object $obTransacao
*/
function listar(&$rsRecordSet, $stOrder = "", $obTransacao = "")
{
    include_once ( CAM_GF_TES_MAPEAMENTO."TTesourariaConciliacaoLancamentoManual.class.php" );
    $obTTesourariaConciliacaoManual = new TTesourariaConciliacaoLancamentoManual();
    if( $this->roRTesourariaConciliacao->obRContabilidadePlanoBanco->getExercicio() )
        $stFiltro .= " exercicio = '".$this->roRTesourariaConciliacao->obRContabilidadePlanoBanco->getExercicio()."' AND ";
    if( $this->roRTesourariaConciliacao->getDataFinal() )
        $stFiltro .= " dt_lancamento <= TO_DATE('".$this->roRTesourariaConciliacao->getDataFinal()."', 'dd/mm/yyyy' ) AND ";
    if( $this->roRTesourariaConciliacao->obRContabilidadePlanoBanco->getCodPlano() )
        $stFiltro .= " cod_plano = ".$this->roRTesourariaConciliacao->obRContabilidadePlanoBanco->getCodPlano()." AND ";
    if ( $this->roRTesourariaConciliacao->getMes() ) {
        $stFiltro .= " mes::integer = ".$this->roRTesourariaConciliacao->getMes()." AND ";
        $stFiltro .= " TO_CHAR( dt_lancamento, 'mm' )::integer = ".$this->roRTesourariaConciliacao->getMes()." AND ";
    }

    $stOrder = ( $stOrder ) ? $stOrder : " ORDER BY exercicio, dt_lancamento ";
    $stFiltro = ( $stFiltro ) ? " WHERE ".substr($stFiltro, 0, strlen($stFiltro)-4) : "";
    $obErro = $obTTesourariaConciliacaoManual->listLancamentosManuais( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    return $obErro;
}

}
