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
    * Classe de Regra de Lote
    * Data de Criação   : 11/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo
    * @author Desenvolvedor: Eduardo Martins

    * @package URBEM
    * @subpackage Regra

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.02.04, uc-02.02.31

*/

/*
$Log$
Revision 1.13  2006/07/05 20:50:26  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"       );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"      );

class RContabilidadeLote
{
/**
    * @access Private
    * @var Object
*/
var $obROrcamentoEntidade;
/**
    * @access Private
    * @var Integer
*/
var $inCodLote;
/**
    * @access Private
    * @var Integer
*/
var $inCodLoteInicial;
/**
    * @access Private
    * @var Integer
*/
var $inCodLoteFinal;
/**
    * @access Private
    * @var String
    */
var $stExercicio;
/**
    * @access Private
    * @var String
*/
var $stTipo;
/**
    * @access Private
    * @var String
*/
var $stNomLote;
/**
    * @access Private
    * @var String
*/
var $stDtLote;
/**
    * @access Private
    * @var String
*/
var $stDtLoteInicial;
/**
    * @access Private
    * @var String
*/
var $stDtLoteTermino;

/**
    * @access Public
    * @param Object $Valor
*/
function setROrcamentoEntidade($valor) { $this->obROrcamentoEntidade = $valor; }
/**
    * @access Public
    * @param Integer $Valor
*/
function setCodLote($valor) { $this->inCodLote = $valor; }
/**
    * @access Public
    * @param Integer $Valor
*/
function setCodLoteInicial($valor) { $this->inCodLoteInicial = $valor; }
/**
    * @access Public
    * @param Integer $Valor
*/
function setCodLoteFinal($valor) { $this->inCodLoteFinal = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setTipo($valor) { $this->stTipo = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setExercicio($valor) { $this->stExercicio  = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setNomLote($valor) { $this->stNomLote  = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setDtLote($valor) { $this->stDtLote  = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setDtLoteInicial($valor) { $this->stDtLoteInicial = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setDtLoteTermino($valor) { $this->stDtLoteTermino = $valor;   }

/**
    * @access Public
    * @return Object
*/
function getROrcamentontidade() { return $this->obROrcamentoEntidade; }
/**
    * @access Public
    * @return Integer
*/
function getCodLote() { return $this->inCodLote; }
/**
    * @access Public
    * @return Integer
*/
function getCodLoteInicial() { return $this->inCodLoteInicial; }
/**
    * @access Public
    * @return Integer
*/
function getCodLoteFinal() { return $this->inCodLoteFinal; }
/**
    * @access Public
    * @return String
*/
function getTipo() { return $this->stTipo; }
/**
    * @access Public
    * @return String
*/
function getExercicio() { return $this->stExercicio;   }
/**
    * @access Public
    * @return String
*/
function getNomLote() { return $this->stNomLote;   }
/**
    * @access Public
    * @return String
*/
function getDtLote() { return $this->stDtLote;   }
/**
    * @access Public
    * @return String
*/
function getDtLoteInicial() { return $this->stDtLoteInicial;   }
/**
    * @access Public
    * @return String
*/
function getDtLoteTermino() { return $this->stDtLoteTermino;   }

/**
     * Método construtor
     * @access Public
*/
function RContabilidadeLote()
{
    $this->obROrcamentoEntidade = new ROrcamentoEntidade;
    $this->obTransacao = new Transacao ;
}

/**
    * Executa um recuperaPorChave na classe Persistente
    * @access Public
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultar($boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeLote.class.php" );
    $obTContabilidadeLote = new TContabilidadeLote;

    $obTContabilidadeLote->setDado( "cod_lote"     ,$this->inCodLote   );
    $obTContabilidadeLote->setDado( "exercicio"    ,$this->stExercicio );
    $obTContabilidadeLote->setDado( "tipo"         ,$this->stTipo );
    $obTContabilidadeLote->setDado( "cod_entidade" ,$this->obROrcamentoEntidade->getCodigoEntidade() );
    $obErro = $obTContabilidadeLote->recuperaPorChave( $rsRecordSet, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->stNomLote = $rsRecordSet->getCampo( "nom_lote" );
        $this->stDtLote  = $rsRecordSet->getCampo( "dt_lote"  );
        $obErro = $this->obROrcamentoEntidade->consultarNomes( $rsLista, $boTransacao );
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
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeLote.class.php" );
    $obTContabilidadeLote = new TContabilidadeLote;

    if($this->inCodLote)
        $stFiltro  = " cod_lote = "  . $this->inCodLote . "  AND ";
    if($this->stExercicio)
        $stFiltro .= " exercicio = '"   . $this->stExercicio  . "' AND ";
    if($this->stNomLote)
        $stFiltro .= " nom_lote = '" . $this->stNomLote . "' AND ";
    if($this->stDtLote)
        $stFiltro .= " dt_lote = to_date('" . $this->stDtLote . "','dd/mm/yyyy') AND ";
    if($this->stTipo)
        $stFiltro .= " tipo = '" . $this->stTipo . "' AND ";
     if($this->stNomLote)
        $stFiltro .= " UPPER(nom_lote) like UPPER('%".$this->stNomLote."%') AND ";

    if($this->obROrcamentoEntidade->getCodigoEntidade() )
        $stFiltro .= " cod_entidade = '".$this->obROrcamentoEntidade->getCodigoEntidade()."' AND ";
    $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
    $stOrder = ($stOrder) ? $stOrder : "cod_lote";
    $obErro = $obTContabilidadeLote->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

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
function listarLotes(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeLote.class.php" );
    $obTContabilidadeLote = new TContabilidadeLote;

    if($this->inCodLote)
        $stFiltro  = " cod_lote = "  . $this->inCodLote . "  AND ";
    if($this->stExercicio)
        $stFiltro .= " exercicio = '"   . $this->stExercicio  . "' AND ";
    if($this->stDtLote)
        $stFiltro .= " dt_lote = TO_DATE('" . $this->stDtLote . "','dd/mm/yyyy') AND ";
    if($this->stTipo)
        $stFiltro .= " tipo = '" . $this->stTipo . "' AND ";
     if($this->stNomLote)
        $stFiltro .= " UPPER(nom_lote) like UPPER('%".$this->stNomLote."%') AND ";

    if($this->obROrcamentoEntidade->getCodigoEntidade() )
        $stFiltro .= " cod_entidade = '".$this->obROrcamentoEntidade->getCodigoEntidade()."' AND ";
    $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
    $stOrder = ($stOrder) ? $stOrder : "cod_lote";
    $obErro = $obTContabilidadeLote->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um proximoCodigo na Persistente
    * @access Public
    * @param Object $boTransacao
    * @return Object Objeto Erro
*/
function buscaProximoCodigo($boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeLote.class.php" );
    $obTContabilidadeLote = new TContabilidadeLote;

     $obTContabilidadeLote->setDado( "exercicio"    , $this->stExercicio );
     $obTContabilidadeLote->setDado( "tipo"         , $this->stTipo      );
     $obTContabilidadeLote->setDado( "cod_entidade" , $this->obROrcamentoEntidade->getCodigoEntidade() );
     $obErro = $obTContabilidadeLote->proximoCod( $inCodLote, $boTransacao );
     $this->inCodLote = $inCodLote;

     return $obErro;
}

/**
    * Incluir Lote
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluir($boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeLote.class.php" );
    $obTContabilidadeLote = new TContabilidadeLote;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {

        $obErro = $this->buscaProximoCodigo( $inCodLote, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obTContabilidadeLote->setDado( "cod_lote"     , $this->inCodLote   );
            $obTContabilidadeLote->setDado( "exercicio"    , $this->stExercicio );
            $obTContabilidadeLote->setDado( "tipo"         , $this->stTipo      );
            $obTContabilidadeLote->setDado( "nome_lote"    , $this->stNomLote  );
            $obTContabilidadeLote->setDado( "dt_lote"      , $this->stDtLote   );
            $obTContabilidadeLote->setDado( "cod_entidade" , $this->obROrcamentoEntidade->getCodigoEntidade() );

            $obErro = $obTContabilidadeLote->inclusao( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );

    return $obErro;
}

/**
    * Alterar Lote
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterar($boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeLote.class.php" );
    $obTContabilidadeLote = new TContabilidadeLote;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {

        $obTContabilidadeLote->setDado( "cod_lote"  , $this->inCodLote   );
        $obTContabilidadeLote->setDado( "exercicio" , $this->stExercicio );
        $obTContabilidadeLote->setDado( "tipo"      , $this->stTipo      );
        $obTContabilidadeLote->setDado( "nom_lote"  , $this->stNomLote   );
        $obTContabilidadeLote->setDado( "dt_lote"   , $this->stDtLote    );
        $obTContabilidadeLote->setDado( "cod_lote"  , $this->obROrcamentoEntidade->getCodigoEntidade() );

        $obErro = $obTContabilidadeLote->alteracao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTContabilidadeLote );

    return $obErro;
}

/**
    * Exclui Lote
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluir($boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeLote.class.php" );
    $obTContabilidadeLote = new TContabilidadeLote;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $obTContabilidadeLote->setDado("cod_lote"  , $this->inCodLote   );
        $obTContabilidadeLote->setDado("exercicio" , $this->stExercicio );
        $obTContabilidadeLote->setDado("tipo"      , $this->stTipo      );
        $obTContabilidadeLote->setDado("cod_entidade" , $this->obROrcamentoEntidade->getCodigoEntidade() );
        $obErro = $obTContabilidadeLote->exclusao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTContabilidadeLote);

    return $obErro;
}
/**
    * Exclui Lote Implantado
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirImplantado($boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeLote.class.php" );
    $obTContabilidadeLote = new TContabilidadeLote;

    $obErro = new Erro;
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $obTContabilidadeLote->setCampoCod('');
        $obTContabilidadeLote->setComplementoChave('exercicio,tipo,cod_entidade,cod_lote');
        $obTContabilidadeLote->setDado("cod_lote"  , $this->inCodLote   );
        $obTContabilidadeLote->setDado("exercicio" , $this->stExercicio );
        $obTContabilidadeLote->setDado("tipo"      , $this->stTipo      );
        $obTContabilidadeLote->setDado("cod_entidade" , $this->obROrcamentoEntidade->getCodigoEntidade() );
        $obErro = $obTContabilidadeLote->exclusao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTContabilidadeLote);

    return $obErro;
}

}
