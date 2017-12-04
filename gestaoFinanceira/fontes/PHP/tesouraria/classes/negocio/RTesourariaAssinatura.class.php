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
    * Classe de Regra de Negócio para Assinatura
    * Data de Criação   : 01/09/2005

    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Regra

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.04.01,uc-02.04.20
*/

/*
$Log$
Revision 1.12  2006/07/05 20:38:41  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS       ."Transacao.class.php"              );
include_once ( CAM_GF_TES_MAPEAMENTO    ."TTesourariaAssinatura.class.php"  );
include_once ( CAM_GA_CGM_NEGOCIO       ."RCGM.class.php"                   );
include_once ( CAM_GF_ORC_NEGOCIO       ."ROrcamentoEntidade.class.php"     );

/**
    * Classe de Regra de Assinatura
    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Anderson R. M. Buzo
*/
class RTesourariaAssinatura
{
/*
    * @var Object
    * @access Private
*/
var $obRCGM;
/*
    * @var Object
    * @access Private
*/

var $obROrcamentoEntidade;

/*
    * @var String
    * @access Private
*/
var $stExercicio;
/*
    * @var String
    * @access Private
*/
var $stCargo;
/*
    * @var Boolean
    * @access Private
*/
var $boSituacao;
/*
    * @var String
    * @access Private
*/
var $stEntidades;
/*
    * @var String
    * @access Private
*/
var $stTipo;
/*
    * @var String
    * @access Private
*/
var $stNumMatricula;

/*
    * @access Public
    * @param Object $valor
*/
function setRCGM($valor) { $this->obRCGM                  = $valor; }
/*
    * @access Public
    * @param Object $valor
*/
function setROrcamentoEntidade($valor) { $this->obROrcamentoEntidade    = $valor; }
/*
    * @access Public
    * @param String $valor
*/
function setExercicio($valor) { $this->stExercicio             = $valor; }
/*
    * @access Public
    * @param String $valor
*/
function setCargo($valor) { $this->stCargo                 = $valor; }
/*
    * @access Public
    * @param Boolean $valor
*/
function setSituacao($valor) { $this->boSituacao              = $valor; }
/*
    * @access Public
    * @param String $valor
*/
function setEntidades($valor) { $this->stEntidades             = $valor; }
/*
    * @access Public
    * @param String $valor
*/
function setTipo($valor) { $this->stTipo                  = $valor; }
/*
    * @access Public
    * @param String $valor
*/
function setNumMatricula($valor) { $this->stNumMatricula          = $valor; }

/*
    * @access Public
    * @return Object
*/
function getRCGM() { return $this->obRCGM;                  }
/*
    * @access Public
    * @return Object
*/
function getROrcamentoEntidade() { return $this->obROrcamentoEntidade;    }

/*
    * @access Public
    * @return String
*/
function getExercicio() { return $this->stExercicio;             }
/*
    * @access Public
    * @return String
*/
function getCargo() { return $this->stCargo;                 }
/*
    * @access Public
    * @return Boolean
*/
function getSituacao() { return $this->boSituacao;              }
/*
    * @access Public
    * @return String
*/
function getEntidades() { return $this->stEntidades;             }
/*
    * @access Public
    * @return String
*/
function getTipo() { return $this->stTipo;                  }
/*
    * @access Public
    * @return String
*/
function getNumMatricula() { return $this->stNumMatricula;          }

/**
    * Método Construtor
    * @access Private
*/
function RTesourariaAssinatura()
{
    $this->obRCGM                   = new RCGM;
    $this->obROrcamentoEntidade     = new ROrcamentoEntidade;
}

/**
    * Salva os dados no banco de dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluir($boTransacao = "")
{
    $obTransacao              =  new Transacao;
    $obTTesourariaAssinatura  =  new TTesourariaAssinatura;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTTesourariaAssinatura->setDado( "cod_entidade" , $this->obROrcamentoEntidade->getCodigoEntidade());
        $obTTesourariaAssinatura->setDado( "numcgm"       , $this->obRCGM->getNumCGM() );
        $obTTesourariaAssinatura->setDado( "exercicio"    , $this->stExercicio         );
        $obTTesourariaAssinatura->setDado( "tipo"         , $this->stTipo              );
        $obTTesourariaAssinatura->setDado( "cargo"        , $this->stCargo             );
        $obTTesourariaAssinatura->setDado( "num_matricula", $this->stNumMatricula      );
        $obTTesourariaAssinatura->setDado( "situacao"     , $this->boSituacao          );
        $obErro = $obTTesourariaAssinatura->inclusao( $boTransacao );
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTTesourariaAssinatura );

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
function listar(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    $stFiltro = "";
    if( $this->stExercicio )
        $stFiltro .= " AND exercicio = '".$this->stExercicio."' ";
    if( $this->stCargo )
        $stFiltro .= " AND LOWER( cargo ) = LOWER( '".$this->stCargo."' ) ";
    if( $this->boSituacao )
        $stFiltro .= " AND situacao = ".$this->boSituacao;
    if( $this->obRCGM->getNumCGM() )
        $stFiltro .= " AND numcgm = ".$this->obRCGM->getNumCGM();
    if( $this->obROrcamentoEntidade->getCodigoEntidade() )
        $stFiltro .= " AND cod_entidade = ".$this->obROrcamentoEntidade->getCodigoEntidade();
    if( $this->getEntidades() )
        $stFiltro .= " AND cod_entidade in (".$this->getEntidades().") ";
    if( $this->stTipo )
        $stFiltro .= " AND tipo = '".$this->stTipo."' ";
    if( $this->stNumMatricula )
        $stFiltro .= " AND num_matricula = '".$this->stNumMatricula."' ";

    $stFiltro = ($stFiltro) ? $stFiltro : "";
    $stOrder = ($stOrder) ? $stOrder : "exercicio,numcgm";
    $obTTesourariaAssinatura  =  new TTesourariaAssinatura;
    $obErro = $obTTesourariaAssinatura->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Exclui dados da Assinatura do banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluir($boTransacao = "")
{
    $boFlagTransacao = false;
    $obTransacao              =  new Transacao;
    $obTTesourariaAssinatura  =  new TTesourariaAssinatura;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTTesourariaAssinatura->setDado( "cod_entidade", $this->obROrcamentoEntidade->getCodigoEntidade());
        $obTTesourariaAssinatura->setDado( "numcgm"   , $this->obRCGM->getNumCGM() );
        $obTTesourariaAssinatura->setDado( "exercicio", $this->stExercicio  );
        $obTTesourariaAssinatura->setDado( "tipo"     , $this->stTipo       );
        $obErro = $obTTesourariaAssinatura->exclusao( $boTransacao );
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro , $obTTesourariaAssinatura );

    return $obErro;
}

}
