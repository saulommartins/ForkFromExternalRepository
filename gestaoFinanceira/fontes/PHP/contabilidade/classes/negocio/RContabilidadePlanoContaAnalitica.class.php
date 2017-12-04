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
    * Classe de Regra de Plano Conta Analitica
    * Data de Criação   : 04/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    $Id: RContabilidadePlanoContaAnalitica.class.php 65479 2016-05-25 12:07:26Z franver $

    * Casos de uso: uc-02.02.02, uc-02.02.19, uc-02.04.03, uc-02.04.09, uc-02.03.23
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_FW_BANCO_DADOS."Transacao.class.php";
include_once CAM_GF_CONT_NEGOCIO."RContabilidadePlanoConta.class.php";
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoRecurso.class.php";
include_once CAM_GT_MON_NEGOCIO."RMONCredito.class.php";

/**
    * Classe de Regra de Plano Conta
    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo
*/
class RContabilidadePlanoContaAnalitica extends RContabilidadePlanoConta
{
/**
    * @access Private
    * @var Object
*/
var $obROrcamentoRecurso;
/**
    * @access Private
    * @var Object
*/
var $roUltimoCredito;
/**
    * @access Private
    * @var Integer
*/
var $inCodPlano;
/**
    * @access Private
    * @var Integer
*/
var $inCodPlanoInicial;
/**
    * @access Private
    * @var Integer
*/
var $inCodPlanoFinal;
/**
    * @access Private
    * @var Array
*/
var $arCredito;
/**
    * @access Private
    * @var Array
*/
var $arContaAnaliticaCredito;
/**
    * @access Private
    * @var Array
*/
var $stCodIniEstrutural;

/**
    * @access Private
    * @var String
*/
var $stNatSaldo;

/**
    * @access Public
    * @param Object $Valor
*/
var $stCodEstruturalInicial;

/**
    * @access Public
    * @param Object $Valor
*/
var $stCodEstruturalFinal;

/**
    * @access Public
    * @param Integer
*/
var $inCodGrupo;

/**
    * @access Public
    * @param Integer
*/
var $inCodRecurso;

/**
 *   dtSaldo
 *   Propriedade da classe onde recebe a data limite para fazer a pesquisa do saldo da conta contábil
 *
 *   @access Public
 *   @var date
 */
var $dtSaldo;

var $boFiltraReceitasPrimarias;

/**
    * @access Public
    * @param Object $Valor
*/
function setROrcamentoRecurso($valor) { $this->obROrcamentoRecurso = $valor;        }
/**
    * @access Public
    * @param Integer $Valor
*/
function setCodPlano($valor) { $this->inCodPlano  = $valor; }
/**
    * @access Public
    * @param Integer $Valor
*/
function setCodPlanoInicial($valor) { $this->inCodPlanoInicial  = $valor; }
/**
    * @access Public
    * @param Integer $Valor
*/
function setCodPlanoFinal($valor) { $this->inCodPlanoFinal  = $valor; }
/**
    * @access Public
    * @param Array $Valor
*/
function setCredito($valor) { $this->arCredito  = $valor; }
/**
    * @access Public
    * @param Array $Valor
*/
function setCodIniEstrutural($valor) { $this->stCodIniEstrutural  = $valor; }
/**
    * @access Public
    * @param Array $Valor
*/
function setCodEstruturalInicial($valor) { $this->stCodEstruturalInicial  = $valor; }
/**
    * @access Public
    * @param Array $Valor
*/
function setCodEstruturalFinal($valor) { $this->stCodEstruturalFinal  = $valor; }
/**
    * @access Public
    * @param Array $Valor
*/
function setContaAnaliticaCredito($valor) { $this->arContaAnaliticaCredito  = $valor; }

/**
    * @access Public
    * @param Array $Valor
*/
function setDtSaldo($valor) { $this->dtSaldo  = $valor; }

/**
    * @access Public
    * @param Array $Valor
*/
function setNatSaldo($valor)
{
    $this->stNatSaldo  = $valor;
}

/**
    * @access Public
    * @param Integer $Valor
*/
function setCodGrupo($valor)
{
    $this->inCodGrupo = $valor;
}

/**
    * @access Public
    * @return Integer
*/
function setCodRecurso($valor) { $this->inCodRecurso = $valor;  }

/**
    * @access Public
    * @return Object
*/
function getROrcamentoRecurso() { return $this->obROrcamentoRecurso; }
/**
    * @access Public
    * @return Integer
*/
function getCodPlano() { return $this->inCodPlano;  }
/**
    * @access Public
    * @return Integer
*/
function getCodPlanoInicial() { return $this->inCodPlanoInicial;  }
/**
    * @access Public
    * @return Integer
*/
function getCodPlanoFinal() { return $this->inCodPlanoFinal;  }
/**
    * @access Public
    * @return Array
*/
function getCredito() { return $this->arCredito;  }
/**
    * @access Public
    * @return Array
*/
function getContaAnaliticaCredito() { return $this->arContaAnaliticaCredito;  }

/**
    * @access Public
    * @return Array
*/
function getDtSaldo() { return $this->dtSaldo;  }

/**
    * @access Public
    * @return String
*/
function getNatSaldo()
{
    return $this->stNatSaldo;
}

/**
    * @access Public
    * @return Integer
*/
function getCodGrupo()
{
    return $this->inCodGrupo;
}

/**
    * @access Public
    * @return Integer
*/
function getCodRecurso() { return $this->inCodRecurso;  }


/**
     * Método construtor
     * @access Public
*/
function RContabilidadePlanoContaAnalitica()
{
    parent::RContabilidadePlanoConta();
    $this->obROrcamentoRecurso                   = new ROrcamentoRecurso;
}

/**
    * Método para adicionar creditos
    * @access Public
*/
function addCredito()
{
    $this->arCredito[] = new RMONCredito();
    $this->roUltimoCredito = &$this->arCredito[ count( $this->arCredito )-1 ];
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
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php"        );
    $obTContabilidadePlanoAnalitica        = new TContabilidadePlanoAnalitica;
    $stOrder = '';

    if (!$this->inCodPlano) {
        $obErro = parent::consultar( $boTransacao );
        if ( !$obErro->ocorreu() and $this->inCodConta ) {
            $stFiltro  = " WHERE ";
            $stFiltro .= " cod_conta = ".$this->inCodConta." AND ";
            $stFiltro .= " exercicio = '".$this->stExercicio."' ";
            $obErro = $obTContabilidadePlanoAnalitica->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
            $this->inCodPlano = $rsRecordSet->getCampo( "cod_plano" );
         }
    } else {
        $obTContabilidadePlanoAnalitica->setDado( "cod_plano", $this->inCodPlano );
        $obTContabilidadePlanoAnalitica->setDado( "exercicio", $this->stExercicio );
        $obErro = $obTContabilidadePlanoAnalitica->recuperaPorChave( $rsRecordSet, $boTransacao );
        if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
            $this->inCodConta = $rsRecordSet->getCampo( "cod_conta" );
            $this->stNatSaldo = $rsRecordSet->getCampo( "natureza_saldo" );
            $obErro = parent::consultar( $boTransacao );
        }
    }
    if( !$obErro->ocorreu() )
        $obErro = $this->consultarRecurso( $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaPorChave na classe Persistente
    * @access Public
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarRecurso($boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoRecurso.class.php"          );
    $obTContabilidadePlanoRecurso          = new TContabilidadePlanoRecurso;

    $stOrder = "";
    $obErro = new Erro();
    if ($this->inCodPlano) {
        $stFiltro  = " WHERE ";
        $stFiltro .= " cod_plano = ".$this->inCodPlano." AND ";
        $stFiltro .= " exercicio = '".$this->stExercicio."' ";
        $obErro = $obTContabilidadePlanoRecurso->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
        if (( !$obErro->ocorreu() ) && ( $rsRecordSet->getNumLinhas() > 0 )) {
            $this->obROrcamentoRecurso->setCodRecurso( $rsRecordSet->getCampo( "cod_recurso" ) );
            $this->obROrcamentoRecurso->setCodRecursoContraPartida( $rsRecordSet->getCampo( "cod_recurso_contrapartida" ) );
            $obErro = $this->obROrcamentoRecurso->consultar( $rsRecurso, $boTransacao );
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
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php"        );
    $obTContabilidadePlanoAnalitica        = new TContabilidadePlanoAnalitica;

    if($this->inCodPlano)
        $stFiltro  = " pa.cod_plano = " . $this->inCodPlano . "  AND ";
    if($this->stExercicio)
        $stFiltro .= " pa.exercicio = '" . $this->stExercicio . "' AND ";
    if($this->inCodConta)
        $stFiltro .= " pa.cod_conta = " . $this->inCodConta . " AND ";
    if($this->obROrcamentoRecurso->getCodRecurso() )
        $stFiltro .= " pr.cod_recurso = " . $this->obROrcamentoRecurso->getCodRecurso() . " AND ";
    $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
    $obTContabilidadePlanoAnalitica->setDado("exercicio",     $this->getExercicio());
    $obErro = $obTContabilidadePlanoAnalitica->recuperaRelacionamentoRecurso( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um montaContaBorderoTransferencia, utilizado para trazer contas banco e com cod_estrutural iniciado por 2
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarPlanoContaConsignacao(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php"        );
    $obTContabilidadePlanoAnalitica        = new TContabilidadePlanoAnalitica;

    if($this->inCodPlano)
        $stFiltro  = " tabela.cod_plano = " . $this->inCodPlano . "  AND ";
    if($this->stExercicio)
        $stFiltro .= " tabela.exercicio = '" . $this->stExercicio . "' AND ";
    if ( $this->obROrcamentoEntidade->getCodigoEntidade() ) {
        $stFiltro .= " ( tabela.cod_entidade in ( ".$this->obROrcamentoEntidade->getCodigoEntidade()." ) OR ";
        $stFiltro .= " tabela.cod_entidade is null ) AND ";
    }
    if($this->stNomConta)
        $stFiltro .= " lower(tabela.nom_conta) like lower('".$this->stNomConta."%') AND ";

    if( $this->stCodEstrutural )
        $stFiltro .= " tabela.cod_estrutural like ( publico.fn_mascarareduzida('".$this->stCodEstrutural."')||'%' ) AND ";

    $stFiltro .= " (publico.fn_mascarareduzida(tabela.cod_estrutural) like (publico.fn_mascarareduzida('2')||'%') OR ";
    $stFiltro .= "  publico.fn_mascarareduzida(tabela.cod_estrutural) != (publico.fn_mascarareduzida('2')||'%') ) AND ";

    $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
    $stOrder = ( $stOrder ) ?  $stOrder : 'tabela.cod_estrutural';
    $obErro = $obTContabilidadePlanoAnalitica->recuperaContaBorderoTransferencia( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}
/**
    * Executa um montaContaBorderoTransferencia, utilizado para trazer contas banco e com cod_estrutural iniciado por 5 e 6
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarPlanoContaTransferenciaEntidadeDiferente(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php"        );
    $obTContabilidadePlanoAnalitica        = new TContabilidadePlanoAnalitica;

    if($this->inCodPlano)
        $stFiltro  = " tabela.cod_plano = " . $this->inCodPlano . "  AND ";
    if($this->stExercicio)
        $stFiltro .= " tabela.exercicio = '" . $this->stExercicio . "' AND ";
    if ( $this->obROrcamentoEntidade->getCodigoEntidade() ) {
        $stFiltro .= " ( tabela.cod_entidade in ( ".$this->obROrcamentoEntidade->getCodigoEntidade()." ) OR ";
        $stFiltro .= " tabela.cod_entidade is null ) AND ";
    }
    if($this->stNomConta)
        $stFiltro .= " lower(tabela.nom_conta) like lower('".$this->stNomConta."%') AND ";

    if( $this->stCodEstrutural )
        $stFiltro .= " tabela.cod_estrutural like ( publico.fn_mascarareduzida('".$this->stCodEstrutural."')||'%' ) AND ";

    $stFiltro .= " (publico.fn_mascarareduzida(tabela.cod_estrutural) like (publico.fn_mascarareduzida('5')||'%') OR ";
    $stFiltro .= "  publico.fn_mascarareduzida(tabela.cod_estrutural) like (publico.fn_mascarareduzida('6')||'%') OR ";
    $stFiltro .= "  publico.fn_mascarareduzida(tabela.cod_estrutural) != (publico.fn_mascarareduzida('5')||'%') OR ";
    $stFiltro .= "  publico.fn_mascarareduzida(tabela.cod_estrutural) != (publico.fn_mascarareduzida('6')||'%') ) AND ";

    $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
    $stOrder = ( $stOrder ) ?  $stOrder : 'tabela.cod_estrutural';
    $obErro = $obTContabilidadePlanoAnalitica->recuperaContaBorderoTransferencia( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}
/**
    * Executa um montaRelacionamento na classe de Tabela
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarPlanoContaTransferencia(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php"        );
    $obTContabilidadePlanoAnalitica        = new TContabilidadePlanoAnalitica;

    if($this->inCodPlano)
        $stFiltro  = " pa.cod_plano = " . $this->inCodPlano . "  AND ";
    if($this->stExercicio)
        $stFiltro .= " pc.exercicio = '" . $this->stExercicio . "' AND ";
    if($this->stNomConta)
        $stFiltro .= " lower(pc.nom_conta) like lower('".$this->stNomConta."%') AND ";

    if( $this->stCodEstrutural )
        $stFiltro .= " pc.cod_estrutural like ( publico.fn_mascarareduzida('".$this->stCodEstrutural."')||'%' ) AND ";

    if ( $this->obROrcamentoEntidade->getCodigoEntidade() ) {
        $stFiltro .= " ( pb.cod_entidade in ( ".$this->obROrcamentoEntidade->getCodigoEntidade()." ) OR ";
        $stFiltro .= " pb.cod_entidade is null ) AND ";
    }

    $stFiltro .= " (publico.fn_mascarareduzida(pc.cod_estrutural) like (publico.fn_mascarareduzida('1')||'%') OR ";
    $stFiltro .= "  publico.fn_mascarareduzida(pc.cod_estrutural) like (publico.fn_mascarareduzida('2')||'%') OR ";
    $stFiltro .= "  publico.fn_mascarareduzida(pc.cod_estrutural) like (publico.fn_mascarareduzida('5')||'%') OR ";
    $stFiltro .= "  publico.fn_mascarareduzida(pc.cod_estrutural) like (publico.fn_mascarareduzida('6')||'%') OR ";
    $stFiltro .= "  pb.cod_banco is not null ) AND ";

    $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
    $stOrder = ( $stOrder ) ?  $stOrder : 'cod_estrutural';
    $obErro = $obTContabilidadePlanoAnalitica->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um montaRelacionamento na classe de Tabela
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarPlanoContaArrecadacao(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php"        );
    $obTContabilidadePlanoAnalitica        = new TContabilidadePlanoAnalitica;

    if($this->inCodPlano)
        $stFiltro  = " pa.cod_plano = " . $this->inCodPlano . "  AND ";
    if($this->stExercicio)
        $stFiltro .= " pc.exercicio = '" . $this->stExercicio . "' AND ";
    if($this->stNomConta)
        $stFiltro .= " lower(pc.nom_conta) like lower('".$this->stNomConta."%') AND ";
    if( $this->stCodEstrutural )
        $stFiltro .= " pc.cod_estrutural like publico.fn_mascarareduzida('".$this->stCodEstrutural."')||'%' AND ";

    if ( Sessao::getExercicio() > '2012' ) {
        $stFiltro .= " pb.cod_banco is not null AND ";
        if ( $this->obROrcamentoEntidade->getCodigoEntidade() ) {
            $stFiltro .= " pb.cod_entidade in ( ".$this->obROrcamentoEntidade->getCodigoEntidade()." ) AND ";
        }
    } else {
        $stFiltro .= "  (publico.fn_mascarareduzida(pc.cod_estrutural) like (publico.fn_mascarareduzida('2')||'%') OR ";
        $stFiltro .= "  ( pb.cod_banco is not null ";
        if( $this->obROrcamentoEntidade->getCodigoEntidade() )
            $stFiltro .= " AND pb.cod_entidade in ( ".$this->obROrcamentoEntidade->getCodigoEntidade()." ) ";
        $stFiltro .= " ) ) AND ";
    }

    $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
    $stOrder = ( $stOrder ) ?  $stOrder : 'cod_estrutural';
    $obErro = $obTContabilidadePlanoAnalitica->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um montaRelacionamento na classe de Tabela
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarPlanoConta(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php"        );
    $obTContabilidadePlanoAnalitica        = new TContabilidadePlanoAnalitica;

    if($this->inCodPlano)
        $stFiltro  = " pa.cod_plano = " . $this->inCodPlano . "  AND ";
    if($this->stExercicio)
        $stFiltro .= " pc.exercicio = '" . $this->stExercicio . "' AND ";
    if($this->stCodEstrutural)
        $stFiltro .= " publico.fn_mascarareduzida(pc.cod_estrutural) like (publico.fn_mascarareduzida('".$this->stCodEstrutural."')||'%') AND ";
    if($this->stNomConta)
        $stFiltro .= " lower(pc.nom_conta) like lower('".$this->stNomConta."%') AND ";
    $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
    $stOrder = ( $stOrder ) ?  $stOrder : 'cod_estrutural';
    $obErro = $obTContabilidadePlanoAnalitica->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um montaRelacionamento na classe de Tabela
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarRelatorioPlanoConta(&$rsRecordSet, $stFiltro = "", $stOrder = "" , $boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php"        );
    $obTContabilidadePlanoAnalitica        = new TContabilidadePlanoAnalitica;
    $obTContabilidadePlanoAnalitica->setDado( "cod_entidade" , $this->obROrcamentoEntidade->getCodigoEntidade() );

    $obErro = $obTContabilidadePlanoAnalitica->recuperaRelacionamentoRelatorioConta( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaRelacionamentoContaCredito na classe de Tabela
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarRelacionamentoContaCredito(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php"        );
    $obTContabilidadePlanoAnalitica        = new TContabilidadePlanoAnalitica;

    if($this->inCodPlano )
        $stFiltro  = " CPA.cod_plano = " . $this->inCodPlano . "  AND ";
    if($this->inCodPlanoInicial )
        $stFiltro  = " CPA.cod_plano >= " . $this->inCodPlanoInicial . "  AND ";
    if($this->inCodPlanoFinal )
        $stFiltro .= " CPA.cod_plano <= " . $this->inCodPlanoFinal . "  AND ";
    if($this->stExercicio)
        $stFiltro .= " CPA.exercicio = '" . $this->stExercicio . "' AND ";
    if ($this->stCodEstrutural) {
       $stFiltro .= " CPC.cod_estrutural like '".$this->stCodEstrutural."%' AND ";
    }
    $stFiltro = ( $stFiltro ) ? " AND " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
    //$stOrder  = ( $stOrder ) ?  $stOrder : 'CPA.cod_plano';
    $obErro = $obTContabilidadePlanoAnalitica->recuperaRelacionamentoContaCredito( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaPlanoContaAnalitica na classe de Tabela para Implantacao de Saldo
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarPlanoContaAnalitica(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php"        );
    $obTContabilidadePlanoAnalitica        = new TContabilidadePlanoAnalitica;

    $obTContabilidadePlanoAnalitica->setDado('cod_lote',1) ;
    if($this->inCodPlano)
        $stFiltro  = " cod_plano = " . $this->inCodPlano . "  AND ";
    if($this->stExercicio)
        $stFiltro .= " exercicio = '" . $this->stExercicio . "' AND ";
    if(trim($this->stCodEstrutural))
        $stFiltro .= " publico.fn_mascarareduzida(cod_estrutural) like (publico.fn_mascarareduzida('".$this->stCodEstrutural."')||'%') AND ";
    if(trim($this->stNomConta))
        $stFiltro .= " lower(nom_conta) like lower('".$this->stNomConta."%') AND ";
    $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
    $stOrder  = ($stOrder)  ? $stOrder : "cod_estrutural";
    $obErro = $obTContabilidadePlanoAnalitica->recuperaPlanoContaAnalitica( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

function listarContaAnalitica(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php"        );
    $obTContabilidadePlanoAnalitica        = new TContabilidadePlanoAnalitica;
    $stFiltro = "";

    if($this->inCodPlano)
        $stFiltro .= " pa.cod_plano = " . $this->inCodPlano . "  AND ";
    if($this->stExercicio)
        $stFiltro .= " pa.exercicio = '" . $this->stExercicio . "' AND ";
    if(trim($this->stCodEstrutural))
        $stFiltro .= " publico.fn_mascarareduzida(pc.cod_estrutural) like (publico.fn_mascarareduzida('".$this->stCodEstrutural."')||'%') AND ";
    if(trim($this->stNomConta))
        $stFiltro .= " lower(pc.nom_conta) like lower('".$this->stNomConta."%') AND ";
    if ($this->boFiltraReceitasPrimarias == true) {
        $stFiltro .= " NOT EXISTS (                                                                                             \n";
        $stFiltro .= " SELECT   1                                                                                               \n";
        $stFiltro .= " FROM     orcamento.conta_receita                as cr                                                    \n";
        $stFiltro .= "         ,orcamento.receita                      as re                                                    \n";
        $stFiltro .= "         ,contabilidade.desdobramento_receita    as dr                                                    \n";
        $stFiltro .= " WHERE   cr.exercicio    = re.exercicio                                                                   \n";
        $stFiltro .= " AND     cr.cod_conta    = re.cod_conta                                                                   \n";
        $stFiltro .= " AND     re.exercicio    = dr.exercicio                                                                   \n";
        $stFiltro .= " AND     re.cod_receita  = dr.cod_receita_secundaria                                                      \n";
        $stFiltro .= " AND     pc.exercicio     = cr.exercicio                                                                  \n";
        $stFiltro .= " AND     pc.cod_estrutural= '4.'||cr.cod_estrutural                                                       \n";
        $stFiltro .= " AND     cr.exercicio='".$this->stExercicio."'                                                            \n";
        if(trim($this->stNomConta))
            $stFiltro .= " AND     lower(pc.nom_conta) like lower('".$this->stNomConta."%')   \n";
        $stFiltro .= " ) AND ";
    }
    if (trim($this->stCodIniEstrutural)) {
        $stFiltro .= " pc.cod_estrutural like ('".$this->stCodIniEstrutural.".%') AND ";
    }

    $stFiltro = ($stFiltro) ? " AND " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
    $stOrder  = ($stOrder)  ? $stOrder : "pc.cod_estrutural";
    $obErro = $obTContabilidadePlanoAnalitica->recuperaContaAnalitica( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

function listarContaAnaliticaFiltro(&$rsRecordSet, $stFiltro = "" , $stOrder = "" , $boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php"        );
    $obTContabilidadePlanoAnalitica        = new TContabilidadePlanoAnalitica;

    if($this->inCodPlano)
        $stFiltro  .= " pa.cod_plano = " . $this->inCodPlano . "  AND ";
    if($this->stExercicio)
        $stFiltro .= " pa.exercicio = '" . $this->stExercicio . "' AND ";
    if(trim($this->stCodEstrutural))
        $stFiltro .= " publico.fn_mascarareduzida(pc.cod_estrutural) like (publico.fn_mascarareduzida('".$this->stCodEstrutural."')||'%') AND ";
    if(trim($this->stNomConta))
        $stFiltro .= " lower(pc.nom_conta) like lower('".$this->stNomConta."%') AND ";
    if ($this->boFiltraReceitasPrimarias == true) {
        $stFiltro .= " NOT EXISTS (                                                                                             \n";
        $stFiltro .= " SELECT   1                                                                                               \n";
        $stFiltro .= " FROM     orcamento.conta_receita                as cr                                                    \n";
        $stFiltro .= "         ,orcamento.receita                      as re                                                    \n";
        $stFiltro .= "         ,contabilidade.desdobramento_receita    as dr                                                    \n";
        $stFiltro .= " WHERE   cr.exercicio    = re.exercicio                                                                   \n";
        $stFiltro .= " AND     cr.cod_conta    = re.cod_conta                                                                   \n";
        $stFiltro .= " AND     re.exercicio    = dr.exercicio                                                                   \n";
        $stFiltro .= " AND     re.cod_receita  = dr.cod_receita_secundaria                                                      \n";
        $stFiltro .= " AND     pc.exercicio     = cr.exercicio                                                                  \n";
        $stFiltro .= " AND     pc.cod_estrutural= '4.'||cr.cod_estrutural                                                       \n";
        $stFiltro .= " AND     cr.exercicio='".$this->stExercicio."'                                                            \n";
        if(trim($this->stNomConta))
            $stFiltro .= " AND lower(pc.nom_conta) like lower('".$this->stNomConta."%') \n";
        $stFiltro .= " ) AND ";
    }
    if (trim($this->stCodIniEstrutural)) {
        $stFiltro.= "( ";
        $arCodIniEstrutural = explode( ',', $this->stCodIniEstrutural );
        foreach ($arCodIniEstrutural as $arAux) {
            $stFiltro.= " pc.cod_estrutural like ('".$arAux.".%') OR ";
        }
        $stFiltro = substr($stFiltro,0,strlen($stFiltro)-3)." ) AND ";
    }

    $stFiltro = ($stFiltro) ? " AND " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
    $stOrder  = ($stOrder)  ? $stOrder : "pc.cod_estrutural";
    $obErro = $obTContabilidadePlanoAnalitica->recuperaContaAnalitica( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Verifica se a Conta é Analitica
    * @access Public
    * @param Integer $inCodPlano
    * @param Integer $inCodConta
    * @param Boolean $boTransacao
    * @return Object Objeto Erro
*/
function checarContaAnalitica(&$inCodPlano, $inCodConta, $boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoConta.class.php"        );
    $obTContabilidadePlanoConta        = new TContabilidadePlanoConta;

    $stFiltro = " AND pa.exercicio = '".$this->stExercicio."' AND pa.cod_conta = ".$inCodConta;
    $obErro = $obTContabilidadePlanoConta->recuperaContaAnalitica( $rsRecordSet, $stFiltro, '', $boTransacao );

    if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
        $inCodPlano = $rsRecordSet->getCampo( "cod_plano" );
    }

    return $obErro;
}

function listarContaAnaliticaAtivoPermanente(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoConta.class.php"        );
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/funcoesLegado.lib.php';
    $obTContabilidadePlanoConta        = new TContabilidadePlanoConta;
    if( $this->stExercicio )
        $stFiltro  .= " AND pa.exercicio = '".$this->stExercicio."' ";
    if( $this->stNomConta )
        $stFiltro .= " AND pc.nom_conta ilike '%".$this->stNomConta."%' ";
    if( $this->stCodEstrutural )
        $stFiltro .= " AND pc.cod_estrutural like publico.fn_mascarareduzida('".$this->stCodEstrutural."') || '%' ";
    $obTContabilidadePlanoConta->setDado('cod_lote',1) ;
    $this->listarValorContaAtivoPermanente($rsLista,"",$boTransacao);
    $codEstrutural = mascaraReduzida($rsLista->getCampo("valor"));
    if ($this->codEstrutural > 0) {
        $stFiltro .= " AND cod_estrutural like '".$codEstrutural."%'";
    }
    if ($this->inCodPlano) {
        $stFiltro .= " AND pa.cod_plano = ".$this->inCodPlano;
    }
    $obErro = $obTContabilidadePlanoConta->recuperaContaAnaliticaAtivoPermanente( $rsRecordSet, $stFiltro, '', $boTransacao);

    return $obErro;
}

function listarValorContaAtivoPermanente(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoConta.class.php"        );
    $obTContabilidadePlanoConta        = new TContabilidadePlanoConta;

    $stFiltro = " AND exercicio = '".$this->stExercicio."'";
    $obTContabilidadePlanoConta->setDado('cod_lote',1) ;
    $obErro = $obTContabilidadePlanoConta->recuperaValorContaAtivoPermanente( $rsRecordSet, $stFiltro, '', $boTransacao);

    return $obErro;
}

function listarValorContaDepreciacao(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoConta.class.php"        );
    $obTContabilidadePlanoConta        = new TContabilidadePlanoConta;

    $stFiltro = " AND exercicio = '".$this->stExercicio."'";
    $obTContabilidadePlanoConta->setDado('cod_lote',1) ;
    $obErro = $obTContabilidadePlanoConta->recuperaValorContaDepreciacao( $rsRecordSet, $stFiltro, '', $boTransacao);

    return $obErro;
}

/**
    * Salva dados do Plano de Conta Analitica no banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function salvar($boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php"        );
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoRecurso.class.php"          );
    $obTContabilidadePlanoRecurso          = new TContabilidadePlanoRecurso;
    $obTContabilidadePlanoAnalitica        = new TContabilidadePlanoAnalitica;

    $boFlagTransacao = false;
    $obErro = parent::salvar( $boTransacao );
    if ( !$obErro->ocorreu() and $this->boContaAnalitica ) {
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao);
        if ( !$obErro->ocorreu() ) {
            $obTContabilidadePlanoAnalitica->setDado( "exercicio" , $this->stExercicio );
            $obTContabilidadePlanoAnalitica->setDado( "cod_conta" , $this->inCodConta  );
            $obTContabilidadePlanoAnalitica->setDado( "natureza_saldo" , $this->stNatSaldo );
            if ($this->inCodPlano) {
                $obTContabilidadePlanoAnalitica->setDado( "cod_plano", $this->inCodPlano );
                $obErro = $obTContabilidadePlanoAnalitica->alteracao( $boTransacao );
            } else {
                // Valida se conta tem filhas
                $obErro = $this->validarCodigoEstruturalFilho( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $obTContabilidadePlanoAnalitica->proximoCod( $inCodPlano, $boTransacao );
                    $this->setCodPlano( $inCodPlano );
                    $obTContabilidadePlanoAnalitica->setDado( "cod_plano" , $this->inCodPlano );
                    $obErro = $obTContabilidadePlanoAnalitica->inclusao( $boTransacao  );
                } else {
                    $obErro->setDescricao( "Esta conta possui contas filhas." );
                }
            }

            if ( !$obErro->ocorreu() ) {
                $obTContabilidadePlanoRecurso->setDado( "cod_plano"  , $this->inCodPlano  );
                $obTContabilidadePlanoRecurso->setDado( "exercicio"  , $this->stExercicio );
                $obTContabilidadePlanoRecurso->setDado( "natureza_saldo"  , $this->stNatSaldo );
                $obErro = $obTContabilidadePlanoRecurso->recuperaPorChave( $rsPlanoRecurso, $boTransacao );
                $inCodRecursoOld = trim((string) $rsPlanoRecurso->getCampo( "cod_recurso" ));
                if ( !$obErro->ocorreu() ) {
                    if ( $this->obROrcamentoRecurso->getCodRecurso() != null and $this->obROrcamentoRecurso->getCodRecurso() >= 0 and $this->obROrcamentoRecurso->getCodRecurso()!="") {
                        $obTContabilidadePlanoRecurso->setDado( "cod_recurso"              , $this->obROrcamentoRecurso->getCodRecurso() );
                        $obTContabilidadePlanoRecurso->setDado( "cod_recurso_contrapartida", $this->obROrcamentoRecurso->getCodRecursoContraPartida() );
                        if ( $inCodRecursoOld != null and $this->obROrcamentoRecurso->getCodRecurso() >= 0 and $this->obROrcamentoRecurso->getCodRecurso()!="") {
                            $obErro = $obTContabilidadePlanoRecurso->alteracao( $boTransacao );
                        } else {
                            $obErro = $obTContabilidadePlanoRecurso->inclusao( $boTransacao );
                        }
                    } else {
                        $obErro = $obTContabilidadePlanoRecurso->exclusao( $boTransacao );
                    }
                }
            }

        }
    } elseif ( !$obErro->ocorreu() ) {
        $obErro = $this->checarContaAnalitica( $inCodPlano, $this->inCodConta, $boTransacao );
        if ( !$obErro->ocorreu() and $inCodPlano ) {
           $this->inCodPlano = $inCodPlano;
           $inCodContaTMP = $this->inCodConta;
           $this->inCodConta = null;
           $obErro = $this->excluir( $boTransacao );
           $this->inCodConta = $inCodContaTMP;
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );

    return $obErro;
}

/**
    * Salva dados do Plano de Conta Analitica no banco de dados sem verificar níveis de conta
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function salvarEscolhaPlanoConta($boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php"        );
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoRecurso.class.php"          );
    $obTContabilidadePlanoRecurso          = new TContabilidadePlanoRecurso;
    $obTContabilidadePlanoAnalitica        = new TContabilidadePlanoAnalitica;

    $boFlagTransacao = false;
    $obErro = parent::salvarEscolhaPlanoConta( $boTransacao );
    if ( !$obErro->ocorreu() and $this->boContaAnalitica ) {
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao);
        if ( !$obErro->ocorreu() ) {
            $obTContabilidadePlanoAnalitica->setDado( "exercicio" , $this->stExercicio );
            $obTContabilidadePlanoAnalitica->setDado( "cod_conta" , $this->inCodConta  );
            $obTContabilidadePlanoAnalitica->setDado( "natureza_saldo" , $this->stNatSaldo );
            if ($this->inCodPlano) {
                $obTContabilidadePlanoAnalitica->setDado( "cod_plano", $this->inCodPlano );
                $obErro = $obTContabilidadePlanoAnalitica->alteracao( $boTransacao );
            } else {
                $obTContabilidadePlanoAnalitica->proximoCod( $inCodPlano, $boTransacao );
                $this->setCodPlano( $inCodPlano );
                $obTContabilidadePlanoAnalitica->setDado( "cod_plano" , $this->inCodPlano );
                $obErro = $obTContabilidadePlanoAnalitica->inclusao( $boTransacao  );
            }

            if ( !$obErro->ocorreu() ) {
                $obTContabilidadePlanoRecurso->setDado( "cod_plano"  , $this->inCodPlano  );
                $obTContabilidadePlanoRecurso->setDado( "exercicio"  , $this->stExercicio );
                $obTContabilidadePlanoRecurso->setDado( "natureza_saldo"  , $this->stNatSaldo );
                $obErro = $obTContabilidadePlanoRecurso->recuperaPorChave( $rsPlanoRecurso, $boTransacao );
                $inCodRecursoOld = trim((string) $rsPlanoRecurso->getCampo( "cod_recurso" ));
                if ( !$obErro->ocorreu() ) {
                    if ( $this->obROrcamentoRecurso->getCodRecurso() != null and $this->obROrcamentoRecurso->getCodRecurso() >= 0 and $this->obROrcamentoRecurso->getCodRecurso()!="") {
                        $obTContabilidadePlanoRecurso->setDado( "cod_recurso", $this->obROrcamentoRecurso->getCodRecurso() );
                        if ( $inCodRecursoOld != null and $this->obROrcamentoRecurso->getCodRecurso() >= 0 and $this->obROrcamentoRecurso->getCodRecurso()!="") {
                            $obErro = $obTContabilidadePlanoRecurso->alteracao( $boTransacao );
                        } else {
                            $obErro = $obTContabilidadePlanoRecurso->inclusao( $boTransacao );
                        }
                    } else {
                        $obErro = $obTContabilidadePlanoRecurso->exclusao( $boTransacao );
                    }
                }
            }

        }
    } elseif ( !$obErro->ocorreu() ) {
        $obErro = $this->checarContaAnalitica( $inCodPlano, $this->inCodConta, $boTransacao );
        if ( !$obErro->ocorreu() and $inCodPlano ) {
           $this->inCodPlano = $inCodPlano;
           $inCodContaTMP = $this->inCodConta;
           $this->inCodConta = null;
           $obErro = $this->excluir( $boTransacao );
           $this->inCodConta = $inCodContaTMP;
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );

    return $obErro;
}

/**
    * Salva dados do Plano de Conta Analitica no banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function salvarContasAnaliticaCredito($boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnaliticaCredito.class.php" );
    $obTContabilidadePlanoAnaliticaCredito = new TContabilidadePlanoAnaliticaCredito;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ( is_array( $this->arContaAnaliticaCredito ) ) {
            foreach ($this->arContaAnaliticaCredito as $obRContabilidadePlanoContaAnalitica) {
                $this->inCodPlano  = $obRContabilidadePlanoContaAnalitica->getCodPlano();
                $this->stExercicio = $obRContabilidadePlanoContaAnalitica->getExercicio();
                $this->arCredito   = $obRContabilidadePlanoContaAnalitica->getCredito();
                $obErro = $this->salvarCredito( $boTransacao );
                if( $obErro->ocorreu() )
                    break;
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro,  $obTContabilidadePlanoAnaliticaCredito );

    return $obErro;

}

/**
    * Salva dados do Plano de Conta Analitica no banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function salvarCredito($boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnaliticaCredito.class.php" );
    $obTContabilidadePlanoAnaliticaCredito = new TContabilidadePlanoAnaliticaCredito;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTContabilidadePlanoAnaliticaCredito->setDado( 'exercicio', $this->stExercicio );
        $obTContabilidadePlanoAnaliticaCredito->setDado( 'cod_plano', $this->inCodPlano  );
        $obErro = $obTContabilidadePlanoAnaliticaCredito->exclusao( $boTransacao );
        if ( !$obErro->ocorreu() and count( $this->arCredito ) > 0 ) {
            foreach ($this->arCredito as $obRMONCredito) {
                $obTContabilidadePlanoAnaliticaCredito->setDado( 'cod_credito' , $obRMONCredito->getCodCredito()  );
                $obTContabilidadePlanoAnaliticaCredito->setDado( 'cod_especie' , $obRMONCredito->getCodEspecie()  );
                $obTContabilidadePlanoAnaliticaCredito->setDado( 'cod_genero'  , $obRMONCredito->getCodGenero()   );
                $obTContabilidadePlanoAnaliticaCredito->setDado( 'cod_natureza', $obRMONCredito->getCodNatureza() );
                $obErro = $obTContabilidadePlanoAnaliticaCredito->inclusao( $boTransacao );
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro,  $obTContabilidadePlanoAnaliticaCredito );

    return $obErro;
}

/**
    * Exclui dados de Plano de Conta Analitica do banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluir($boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php"        );
    $obTContabilidadePlanoAnalitica        = new TContabilidadePlanoAnalitica;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ($this->inCodPlano) {
            $obErro = $this->excluirPlanoRecurso( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $obTContabilidadePlanoAnalitica->setDado( "cod_plano", $this->inCodPlano );
                $obTContabilidadePlanoAnalitica->setDado( "exercicio", $this->stExercicio  );
                $obErro = $obTContabilidadePlanoAnalitica->exclusao( $boTransacao );

                if ($obErro->ocorreu()) {
                    $obErro->setDescricao('Conta não pode ser excluída porque possui lançamentos.');
                }
            }
        }
        if( !$obErro->ocorreu() )
            $obErro = parent::excluir( $boTransacao );

        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro,  $this->obTContabilidadePlanoConta );
    }

    return $obErro;
}

/**
    * Exclui dados de Plano de Conta Analitica do banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirEscolhaPlanoConta($boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php"        );
    $obTContabilidadePlanoAnalitica        = new TContabilidadePlanoAnalitica;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ($this->inCodPlano) {
            $obErro = $this->excluirPlanoRecurso( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $obTContabilidadePlanoAnalitica->setDado( "cod_plano", $this->inCodPlano );
                $obTContabilidadePlanoAnalitica->setDado( "exercicio", $this->stExercicio  );
                $obErro = $obTContabilidadePlanoAnalitica->exclusao( $boTransacao );

                if ($obErro->ocorreu()) {
                    $obErro->setDescricao('Conta não pode ser excluída porque possui lançamentos.');
                }
            }
        }
        if( !$obErro->ocorreu() )
            $obErro = parent::excluirEscolhaPlanoConta( $boTransacao );

        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro,  $this->obTContabilidadePlanoConta );
    }

    return $obErro;
}

/**
    * Exclui dados de Plano Recurso do banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirPlanoRecurso($boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoRecurso.class.php"          );
    $obTContabilidadePlanoRecurso          = new TContabilidadePlanoRecurso;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTContabilidadePlanoRecurso->setDado( "cod_plano", $this->inCodPlano  );
        $obTContabilidadePlanoRecurso->setDado( "exercicio", $this->stExercicio );
        $obErro = $obTContabilidadePlanoRecurso->exclusao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTContabilidadePlanoRecurso );

    return $obErro;
}

/**
    * Busca Saldo no banco de dados
    * @access Public
    * @param Object $rsRecordSet
    * @param Object $boTransacao
    * @return Object Objeto Erro
*/
function buscaSaldo(&$rsRecordSet, $boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php"        );
    $obTContabilidadePlanoAnalitica        = new TContabilidadePlanoAnalitica;

    if ($this->stExercicio) {
        $stFiltro .= " U.exercicio = '".$this->stExercicio."' AND ";
    }
    if ( $this->obROrcamentoEntidade->obRCGMPessoaFisica->getNumCGM() ) {
        $stFiltro .= " U.numcgm = ".$this->obROrcamentoEntidade->obRCGMPessoaFisica->getNumCGM()." AND ";
    }
    $stFiltro = ( $stFiltro ) ? " AND ".substr($stFiltro,0,strlen($stFiltro)-4) : '';
    $stOrdem = "cod_entidade";
    $obTContabilidadePlanoAnalitica->setDado("cod_plano"      , $this->inCodPlano      );
    $obTContabilidadePlanoAnalitica->setDado("cod_estrutural" , $this->stCodEstrutural );
    $obTContabilidadePlanoAnalitica->setDado("exercicio"      , $this->stExercicio     );
    $obTContabilidadePlanoAnalitica->setDado('dtSaldo'        , $this->dtSaldo         );
    $obErro = $obTContabilidadePlanoAnalitica->recuperaRelacionamentoEntidade( $rsRecordSet, $stFiltro, $stOrdem);

    return $obErro;
}

/**
    * Executa um listarLoteImplantacao na classe de Tabela para Implantacao de Saldo
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarLoteImplantacaoAux(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php"        );
    $obTContabilidadePlanoAnalitica        = new TContabilidadePlanoAnalitica;

    $obTContabilidadePlanoAnalitica->setDado('cod_lote',1) ;
    if($this->inCodPlano)
        $stFiltro  = " pa.cod_plano = " . $this->inCodPlano . "  AND ";
    if($this->inCodPlanoInicial)
        $stFiltro .= " pa.cod_plano >= " . $this->inCodPlanoInicial . "  AND ";
    if($this->inCodPlanoFinal)
        $stFiltro .= " pa.cod_plano <= " . $this->inCodPlanoFinal . "  AND ";
    if($this->stExercicio)
        $stFiltro .= " pc.exercicio = '" . $this->stExercicio . "' AND ";
    if($this->obROrcamentoEntidade->getCodigoEntidade())
        $obTContabilidadePlanoAnalitica->setDado('cod_entidade',$this->obROrcamentoEntidade->getCodigoEntidade());
    if(trim($this->stCodEstrutural))
        $stFiltro .= " publico.fn_mascarareduzida(cod_estrutural) like (publico.fn_mascarareduzida('".$this->stCodEstrutural."')||'%') AND ";
    if(trim($this->stCodEstruturalInicial))
        $stFiltro .= " pc.cod_estrutural >= (publico.fn_mascarareduzida('".$this->stCodEstruturalInicial."')||'%') AND ";
    if(trim($this->stCodEstruturalFinal))
        $stFiltro .= " publico.fn_mascarareduzida(cod_estrutural) <= (publico.fn_mascarareduzida('".$this->stCodEstruturalFinal."')||'%') AND ";
    if(trim($this->stNomConta))
        $stFiltro .= " lower(nom_conta) like lower('".$this->stNomConta."%') AND ";
    if(trim($this->inCodGrupo))
        $stFiltro .= " SUBSTR(pc.cod_estrutural, 1, 1) = '".$this->inCodGrupo."' AND ";
    if(trim($this->inCodRecurso))
        $stFiltro .= " pr.cod_recurso = '" . $this->inCodRecurso . "' AND ";
        
    $stFiltro = ($stFiltro) ? " AND " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
    $stOrder  = ($stOrder)  ? $stOrder : "cod_estrutural";
    
    $obErro = $obTContabilidadePlanoAnalitica->recuperaPlanoContaAnalitica( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    
    return $obErro;
}

function listarLoteImplantacaoAuxPlanoBanco(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php"        );
    $obTContabilidadePlanoAnalitica        = new TContabilidadePlanoAnalitica;

    $obTContabilidadePlanoAnalitica->setDado('cod_lote',1) ;
    if($this->inCodPlano)
        $stFiltro  = " pa.cod_plano = " . $this->inCodPlano . "  AND ";
    if($this->inCodPlanoInicial)
        $stFiltro .= " pa.cod_plano >= " . $this->inCodPlanoInicial . "  AND ";
    if($this->inCodPlanoFinal)
        $stFiltro .= " pa.cod_plano <= " . $this->inCodPlanoFinal . "  AND ";
    if($this->stExercicio)
        $stFiltro .= " pc.exercicio = '" . $this->stExercicio . "' AND ";
    if($this->obROrcamentoEntidade->getCodigoEntidade())
        $obTContabilidadePlanoAnalitica->setDado('cod_entidade',$this->obROrcamentoEntidade->getCodigoEntidade());
    if(trim($this->stCodEstrutural))
        $stFiltro .= " publico.fn_mascarareduzida(cod_estrutural) like (publico.fn_mascarareduzida('".$this->stCodEstrutural."')||'%') AND ";
    if(trim($this->stCodEstruturalInicial))
        $stFiltro .= " pc.cod_estrutural >= (publico.fn_mascarareduzida('".$this->stCodEstruturalInicial."')||'%') AND ";
    if(trim($this->stCodEstruturalFinal))
        $stFiltro .= " publico.fn_mascarareduzida(cod_estrutural) <= (publico.fn_mascarareduzida('".$this->stCodEstruturalFinal."')||'%') AND ";
    if(trim($this->stNomConta))
        $stFiltro .= " lower(nom_conta) like lower('".$this->stNomConta."%') AND ";
    if(trim($this->inCodGrupo))
        $stFiltro .= " SUBSTR(pc.cod_estrutural, 1, 1) = '".$this->inCodGrupo."' AND ";
    $stFiltro = ($stFiltro) ? " AND " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
    $stOrder  = ($stOrder)  ? $stOrder : "cod_estrutural";
    $obErro = $obTContabilidadePlanoAnalitica->recuperaPlanoBancoAnalitica( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

}
