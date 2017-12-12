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
    * Classe de regra de negócio ContaPlano
    * Data de Criação: 12/05/2005

    * @author Analista: Diego Barbosa
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Regra

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso uc-02.05.01

*/

/*
$Log$
Revision 1.4  2006/07/05 20:44:40  cleisson
Adicionada tag Log aos arquivos

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GF_LRF_MAPEAMENTO."TLRFTCERSPlanoContaModeloLRF.class.php"       );
include_once ( CAM_GF_LRF_MAPEAMENTO."TLRFTCERSAjustePlanoContaModeloLRF.class.php" );
include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadePlanoConta.class.php"             );

/**
    * Classe de Regra de ContaPlano
    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Anderson R. M. Buzo
*/
class RLRFTCERSContaPlano extends RContabilidadePlanoConta
{
/**
    * @var Object
    * @access Private
*/
var $obTLRFTCERSPlanoConta;
/**
    * @var Object
    * @access Private
*/
var $obTLRFTCERSAjustePlanoConta;
/**
    * @var Integer
    * @access Private
*/
var $inMes;
/**
    * @var Numeric
    * @access Private
*/
var $nuValor;
/**
    * @var Boolean
    * @access Private
*/
var $boRedutora;

/**
    * @access Public
    * @param Object $valor
*/
function setTLRFTCERSPlanoConta($valor) { $this->obTLRFTCERSPlanoConta       = $valor;  }
/**
    * @access Public
    * @param Object $valor
*/
function setTLRFTCERSAjustePlanoConta($valor) { $this->obTLRFTCERSAjustePlanoConta = $valor;  }
/**
    * @access Public
    * @param Integer $valor
*/
function setMes($valor) { $this->inMes              = $valor;  }
/**
    * @access Public
    * @param Integer $valor
*/
function setValor($valor) { $this->nuValor            = $valor;  }
/**
    * @access Public
    * @param Boolean $valor
*/
function setRedutora($valor) { $this->boRedutora         = $valor;  }

/**
    * @access Public
    * @return Object
*/
function getTLRFTCERSPlanoConta() { return $this->obTLRFTCERSPlanoConta;        }
/**
    * @access Public
    * @return Object
*/
function getTLRFTCERSAjustePlanoConta() { return $this->obTLRFTCERSAjustePlanoConta;  }
/**
    * @access Public
    * @return Integer
*/
function getMes() { return $this->inMes;                     }
/**
    * @access Public
    * @return Numeric
*/
function getValor() { return $this->nuValor;                   }
/**
    * @access Public
    * @return Boolean
*/
function getRedutora() { return $this->boRedutora;                }

/**
     * Método construtor
     * @access Private
*/
function RLRFTCERSContaPlano(&$roRLRFTCERSQuadro)
{
    parent::RContabilidadePlanoConta();
    $this->obTransacao              = new Transacao();
    $this->obTLRFTCERSPlanoConta       = new TLRFTCERSPlanoContaModeloLRF();
    $this->obTLRFTCERSAjustePlanoConta = new TLRFTCERSAjustePlanoContaModeloLRF();
    $this->roRLRFTCERSQuadro           = &$roRLRFTCERSQuadro;
}

/**
    * Executa recuperaRelacionamento na persistente
    * @access Public
    * @param Object &$rsContaPlano
    * @param String $stOrdem
    * @param Object $obTransacao
    * @return Object $obErro
*/
function listar(&$rsContaPlano, $stOrdem = '', $boTransacao = "")
{
    if( $this->roRLRFTCERSQuadro->roRLRFTCERSModelo->getExercicio() )
        $stFiltro .= " PCM.exercicio = '".$this->roRLRFTCERSQuadro->roRLRFTCERSModelo->getExercicio()."' AND ";
    if( $this->roRLRFTCERSQuadro->roRLRFTCERSModelo->getCodModelo() )
        $stFiltro .= " PCM.cod_modelo = ".$this->roRLRFTCERSQuadro->roRLRFTCERSModelo->getCodModelo()." AND ";
    if( $this->roRLRFTCERSQuadro->getCodQuadro() )
        $stFiltro .= " PCM.cod_quadro = ".$this->roRLRFTCERSQuadro->getCodQuadro()." AND ";
    if( $this->obROrcamentoEntidade->getCodigoEntidade() )
        $this->obTLRFTCERSPlanoConta->setDado('cod_entidade', $this->obROrcamentoEntidade->getCodigoEntidade() );
    if( $this->inCodConta )
        $stFiltro .= " PCM.cod_conta = ".$this->inCodConta." AND ";
    if( $this->inMes )
        $this->obTLRFTCERSPlanoConta->setDado( 'mes', $this->inMes );
    if( $this->boRedutora )
        $stFiltro .= " PCM.redutora = ".$this->boRedutora." AND ";

    $stFiltro = ( $stFiltro ) ? ' AND '.substr( $stFiltro, 0, strlen( $stFiltro )-4 ) : '';
    $stOrder = " PCM.exercicio,APCM.cod_entidade,PCM.cod_modelo,PCM.cod_quadro,PCM.cod_conta ";
    $obErro = $this->obTLRFTCERSPlanoConta->recuperaRelacionamento( $rsContaPlano, $stFiltro, $stOrder, $boTransacao );

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
    $this->obTLRFTCERSPlanoConta->setDado( 'exercicio' , $this->roRLRFTCERSQuadro->roRLRFTCERSModelo->getExercicio()  );
    $this->obTLRFTCERSPlanoConta->setDado( 'cod_modelo', $this->roRLRFTCERSQuadro->roRLRFTCERSModelo->getCodModelo()  );
    $this->obTLRFTCERSPlanoConta->setDado( 'cod_quadro', $this->roRLRFTCERSQuadro->getCodQuadro()                  );
    $this->obTLRFTCERSPlanoConta->setDado( 'cod_conta' , $this->inCodConta                                      );
    $obErro = $this->obTLRFTCERSPlanoConta->recuperaPorChave( $rsRecordSet, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->inCodConta = $rsRecordSet->getCampo( 'cod_conta_plano' );
        $this->boRedutora = $rsRecordSet->getCampo( 'redutora'        );
        $this->obTLRFTCERSAjustePlanoConta->setDado( 'mes'         , $this->inMes                                           );
        $this->obTLRFTCERSAjustePlanoConta->setDado( 'exercicio'   , $this->roRLRFTCERSQuadro->roRLRFTCERSModelo->getExercicio()  );
        $this->obTLRFTCERSAjustePlanoConta->setDado( 'cod_conta'   , $this->inCodConta                                      );
        $this->obTLRFTCERSAjustePlanoConta->setDado( 'cod_modelo'  , $this->roRLRFTCERSQuadro->roRLRFTCERSModelo->getCodModelo()  );
        $this->obTLRFTCERSAjustePlanoConta->setDado( 'cod_quadro'  , $this->roRLRFTCERSQuadro->getCodQuadro()                  );
        $this->obTLRFTCERSAjustePlanoConta->setDado( 'cod_entidade', $this->obROrcamentoEntidade->getCodigoEntidade()       );
        $obErro = $this->obTLRFTCERSAjustePlanoConta->recuperaPorChave( $rsRecordSet, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->nuValor = $rsRecordSet->getCampo( 'vl_ajuste' );
        }
    }

    return $obErro;
}

/**
    * Método para incluir ou alterar valores do banco
    * @access Publico
    * @param Object $boTransacao
    * @return Object $obErro
*/
function salvarValores($boTransacao)
{
    $this->obTLRFTCERSAjustePlanoConta->setDado( 'mes'         , $this->inMes                                           );
    $this->obTLRFTCERSAjustePlanoConta->setDado( 'exercicio'   , $this->roRLRFTCERSQuadro->roRLRFTCERSModelo->getExercicio()  );
    $this->obTLRFTCERSAjustePlanoConta->setDado( 'cod_conta'   , $this->inCodConta                                      );
    $this->obTLRFTCERSAjustePlanoConta->setDado( 'cod_modelo'  , $this->roRLRFTCERSQuadro->roRLRFTCERSModelo->getCodModelo()  );
    $this->obTLRFTCERSAjustePlanoConta->setDado( 'cod_quadro'  , $this->roRLRFTCERSQuadro->getCodQuadro()                  );
    $this->obTLRFTCERSAjustePlanoConta->setDado( 'cod_entidade', $this->obROrcamentoEntidade->getCodigoEntidade()       );
    $obErro = $this->obTLRFTCERSAjustePlanoConta->recuperaPorChave( $rsRecordSet, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTLRFTCERSAjustePlanoConta->setDado( 'vl_ajuste' , $this->nuValor );
        if ( $rsRecordSet->eof() ) {
            $obErro = $this->obTLRFTCERSAjustePlanoConta->inclusao( $boTransacao );
        } else {
            $obErro = $this->obTLRFTCERSAjustePlanoConta->alteracao( $boTransacao );
        }
    }

    return $obErro;
}

/**
    * Método para incluir ou alterar dados no banco
    * @access Public
    * @param Object $boTransacao
    * @return Object $obErro
*/
function salvar($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTLRFTCERSPlanoConta->setDado( "exercicio"      , $this->roRLRFTCERSQuadro->roRLRFTCERSModelo->getExercicio() );
        $this->obTLRFTCERSPlanoConta->setDado( "cod_quadro"     , $this->roRLRFTCERSQuadro->getCodQuadro()                  );
        $this->obTLRFTCERSPlanoConta->setDado( "cod_modelo"     , $this->roRLRFTCERSQuadro->roRLRFTCERSModelo->getCodModelo() );
        $this->obTLRFTCERSPlanoConta->setDado( "cod_conta"      , $this->roRLRFTCERSQuadro->getCodConta()                   );
        $this->obTLRFTCERSPlanoConta->setDado( "cod_conta_plano", $this->inCodConta                                      );
        $this->obTLRFTCERSPlanoConta->setDado( "redutora"       , $this->boRedutora                                      );
        $obErro = $this->obTLRFTCERSPlanoConta->inclusao( $boTransacao );
        if (!$obErro->ocorreu and $this->nuValor > 0) {
            $this->salvarValores( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTLRFTCERSPlanoConta );

    return $obErro;
}

}
?>
