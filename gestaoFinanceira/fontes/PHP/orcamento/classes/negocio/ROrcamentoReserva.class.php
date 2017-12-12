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
    * Classe de Regra de Negócio de Reserva
    * Data de Criação   : 04/12/2004

    * @author Analista : Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Regra
    *Casos de uso: uc-02.01.23

*/

/*
$Log$
Revision 1.6  2006/07/05 20:42:11  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"      );

class ROrcamentoReserva
{
/**
    * @var Object
    * @access Private
*/
var $obTransacao;
/**
    * @var String
    * @access Private
*/
var $stExercicio;
/**
    * @var Integer
    * @access Private
*/
var $inCodReserva;
/**
    * @var Integer
    * @access Private
*/
var $inCodDespesa;
/**
    * @var String
    * @access Private
*/
var $stDtValidadeInicial;
/**
    * @var String
    * @access Private
*/
var $stDtValidadeFinal;
/**
    * @var String
    * @access Private
*/
var $stDtInclusao;
/**
    * @var Numeric
    * @access Private
*/
var $nuVlReserva;
/**
    * @var Boolean
    * @access Private
*/
var $boAnulada;

/**
     * @access Public
     * @param String $valor
*/
function setExercicio($valor) { $this->stExercicio                   = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setCodReserva($valor) { $this->inCodReserva                  = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setCodDespesa($valor) { $this->inCodDespesa                  = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setDtValidadeInicial($valor) { $this->stDtValidadeInicial           = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setDtValidadeFinal($valor) { $this->stDtValidadeFinal             = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setDtInclusao($valor) { $this->stDtInclusao                  = $valor; }
/**
     * @access Public
     * @param Numeric $valor
*/
function setVlReserva($valor) { $this->nuVlReserva                   = $valor; }
/**
     * @access Public
     * @param Boolean $valor
*/
function setAnulada($valor) { $this->boAnulada                     = $valor; }

/**
     * @access Public
     * @param String $valor
*/
function getExercicio() { return $this->stExercicio;                    }
/**
     * @access Public
     * @param Integer $valor
*/
function getCodReserva() { return $this->inCodReserva;                   }
/**
     * @access Public
     * @param Integer $valor
*/
function getCodDespesa() { return $this->inCodDespesa;                   }
/**
     * @access Public
     * @param String $valor
*/
function getDtValidadeInicial() { return $this->stDtValidadeInicial;            }
/**
     * @access Public
     * @param String $valor
*/
function getDtValidadeFinal() { return $this->stDtValidadeFinal;              }
/**
     * @access Public
     * @param String $valor
*/
function getDtInclusao() { return $this->stDtInclusao;                   }
/**
     * @access Public
     * @param Numeric $valor
*/
function getVlReserva() { return $this->nuVlReserva;                     }
/**
     * @access Public
     * @param Boolean $valor
*/
function getAnulada() { return $this->boAnulada;                       }

/**
    * Método Construtor
    * @access Private
*/
function ROrcamentoReserva()
{
    $this->obTransacao         = new Transacao;
}

/**
    * Inclui dados no banco
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluir($boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoReserva.class.php" );
    $obTOrcamentoReserva = new TOrcamentoReserva;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTOrcamentoReserva->proximoCod( $this->inCodReserva , $boTransacao );

        $obTOrcamentoReserva->setDado( "exercicio"           , $this->stExercicio         );
        $obTOrcamentoReserva->setDado( "cod_reserva"         , $this->inCodReserva        );
        $obTOrcamentoReserva->setDado( "dt_validade_inicial" , $this->stDtValidadeInicial );
        $obTOrcamentoReserva->setDado( "dt_validade_final"   , $this->stDtValidadeFinal   );
        $obTOrcamentoReserva->setDado( "dt_inclusao"         , $this->stDtInclusao        );
        $obTOrcamentoReserva->setDado( "vl_reserva"          , $this->nuVlReserva         );
        $obTOrcamentoReserva->setDado( "anulada"             , $this->boAnulada           );
        $obTOrcamentoReserva->setDado( "cod_despesa"         , $this->inCodDespesa        );

        $obErro = $obTOrcamentoReserva->inclusao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTOrcamentoReserva );

    return $obErro;
}

/**
    * Altera dados do banco
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterar($boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoReserva.class.php" );
    $obTOrcamentoReserva = new TOrcamentoReserva;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTOrcamentoReserva->setDado( "exercicio"           , $this->stExercicio         );
        $obTOrcamentoReserva->setDado( "cod_reserva"         , $this->inCodReserva        );
        $obTOrcamentoReserva->setDado( "dt_validade_inicial" , $this->stDtValidadeInicial );
        $obTOrcamentoReserva->setDado( "dt_validade_final"   , $this->stDtValidadeFinal   );
        $obTOrcamentoReserva->setDado( "dt_inclusao"         , $this->stDtInclusao        );
        $obTOrcamentoReserva->setDado( "vl_reserva"          , $this->nuVlReserva         );
        $obTOrcamentoReserva->setDado( "anulada"             , $this->boAnulada           );
        $obTOrcamentoReserva->setDado( "cod_despesa"         , $this->inCodDespesa        );
        $obErro = $obTOrcamentoReserva->alteracao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTOrcamentoReserva );

    return $obErro;
}

/**
    * Exclui dados de Reserva
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluir($boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoReserva.class.php" );
    $obTOrcamentoReserva = new TOrcamentoReserva;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $obTOrcamentoReserva->setDado("cod_reserva" , $this->inCodReserva );
        $obTOrcamentoReserva->setDado("exercicio"   , $this->stExercicio  );
        $obErro = $obTOrcamentoReserva->exclusao( $boTransacao );
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTOrcamentoReserva );
    }

    return $obErro;
}

/**
    * Lista todas as Reservas de acordo com o filtro
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listar(&$rsLista, $stOrder = "cod_reserva", $obTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoReserva.class.php" );
    $obTOrcamentoReserva = new TOrcamentoReserva;

    $stFiltro = "";

    if( $this->inCodReserva )
        $stFiltro .= " cod_reserva = ".$this->inCodReserva." AND ";
    if( $this->stExercicio )
        $stFiltro .= " exercicio = '".$this->stExercicio."' AND ";
    if( $this->stDtValidadeInicial )
        $stFiltro .= " TO_DATE(TO_CHAR(dt_validade_inicial,'dd/mm/yyyy'),'dd/mm/yyyy'') = TO_DATE('".$this->stDtValidadeInicial."', 'dd-mm-yyyy') AND ";
    if( $this->stDtValidadeFinal )
        $stFiltro .= " TO_DATE(TO_CHAR(dt_validade_final,'dd/mm/yyyy'),'dd/mm/yyyy'') = TO_DATE('".$this->stDtValidadeFinal."', 'dd-mm-yyyy') AND ";
    if( $this->stDtInclusao )
        $stFiltro .= " TO_DATE(TO_CHAR(dt_inclusao,'dd/mm/yyyy'),'dd/mm/yyyy'') = TO_DATE('".$this->stDtInclusao."', 'dd-mm-yyyy') AND ";
    if( $this->nuVlReserva )
        $stFiltro .= " vl_reserva = ".$this->nuVlReserva." AND ";
    if( $this->boAnulada )
        $stFiltro .= " anulada = ".$this->boAnulada." AND ";

    $stFiltro = ($stFiltro) ? ' WHERE '.substr($stFiltro,0,(strlen($stFiltro)-4)):'';
    $stOrder = ($stOrder) ? $stOrder : 'cod_reserva';
    $obErro = $obTOrcamentoReserva->recuperaTodos( $rsLista, $stFiltro, $stOrder, $obTransacao );

    return $obErro;
}

/**
    * Executa um recuperaPorChave na classe Persistente
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultar($boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoReserva.class.php" );
    $obTOrcamentoReserva = new TOrcamentoReserva;

    $obTOrcamentoReserva->setDado( "cod_reserva" , $this->inCodReserva );
    $obTOrcamentoReserva->setDado( "exercicio"   , $this->stExercicio  );
    $obErro = $obTOrcamentoReserva->recuperaPorChave( $rsRecordSet, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->stDtValidadeInicial = $rsRecordSet->getCampo("dt_validade_inicial");
        $this->stDtValidadeFinal   = $rsRecordSet->getCampo("dt_validade_final"  );
        $this->stDtInclusao        = $rsRecordSet->getCampo("dt_inclusao"        );
        $this->nuVlReserva         = $rsRecordSet->getCampo("vl_reserva"         );
        $this->boAnulada           = $rsRecordSet->getCampo("anulada"            );
//        $this->inCodDespesa        = $rsRecordSet->getCampo("cod_despesa"        );
    }

    return $obErro;
}

}
