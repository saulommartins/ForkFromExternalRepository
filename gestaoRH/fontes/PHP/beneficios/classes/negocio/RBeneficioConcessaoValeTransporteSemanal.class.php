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
* Classe de regra de negócio para RBeneficioConcessaoValeTransporteSemanal
* Data de Criação: 14/10/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @package URBEM
* @subpackage Regra de Negócio

$Id: RBeneficioConcessaoValeTransporteSemanal.class.php 65736 2016-06-10 20:18:11Z michel $

* Casos de uso: uc-04.06.09
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GRH_BEN_MAPEAMENTO."TBeneficioConcessaoValeTransporteSemanal.class.php";
include_once CAM_GRH_BEN_NEGOCIO."RBeneficioConcessaoValeTransporteDiario.class.php";
include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoDiasSemana.class.php";

class RBeneficioConcessaoValeTransporteSemanal
{
/**
   * @access Private
   * @var Object
*/
var $obTransacao;
/**
   * @access Private
   * @var Object
*/
var $obTBeneficioConcessaoValeTransporteSemanal;
/**
   * @access Private
   * @var Object
*/
var $obTAdministracaoDiasSemana;
/**
   * @access Private
   * @var Array
*/
var $arRBeneficioConcessaoValeTransporteDiario;
/**
   * @access Private
   * @var Object
*/
var $roRBeneficioConcessaoValeTransporteDiario;
/**
   * @access Private
   * @var Object
*/
var $roRBeneficioConcessaoValeTransporte;
/**
   * @access Private
   * @var Integer
*/
var $inQuantidade;
/**
   * @access Private
   * @var Boolean
*/
var $boObrigatorio;
/**
   * @access Private
   * @var Integer
*/
var $inCodDia;

/**
    * @access Public
    * @param Object $valor
*/
function setTransacao($valor) { $this->obTransacao                                 = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setTBeneficioConcessaoValeTransporteSemanal($valor) { $this->obTBeneficioConcessaoValeTransporteSemanal  = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setTAdministracaoDiasSemana($valor) { $this->obTAdministracaoDiasSemana                  = $valor; }
/**
    * @access Public
    * @param Array $valor
*/
function setARRBeneficioConcessaoValeTransporteDiario($valor) { $this->arRBeneficioConcessaoValeTransporteDiario   = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRORBeneficioConcessaoValeTransporteDiario(&$valor) { $this->roRBeneficioConcessaoValeTransporteDiario   = &$valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRORBeneficioConcessaoValeTransporte(&$valor) { $this->roRBeneficioConcessaoValeTransporte         = &$valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setQuantidade($valor) { $this->inQuantidade                                = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setObrigatorio($valor) { $this->boObrigatorio                               = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodDia($valor) { $this->inCodDia                                    = $valor; }

/**
    * @access Public
    * @return Object
*/
function getTransacao() { return $this->obTransacao;                                 }
/**
    * @access Public
    * @return Object
*/
function getTBeneficioConcessaoValeTransporteSemanal() { return $this->obTBeneficioConcessaoValeTransporteSemanal;  }
/**
    * @access Public
    * @return Object
*/
function getTAdministracaoDiasSemana() { return $this->obTAdministracaoDiasSemana;                  }
/**
    * @access Public
    * @return Array
*/
function getARRBeneficioConcessaoValeTransporteDiario() { return $this->arRBeneficioConcessaoValeTransporteDiario;   }
/**
    * @access Public
    * @return Object
*/
function getRORBeneficioConcessaoValeTransporteDiario() { return $this->roRBeneficioConcessaoValeTransporteDiario;   }
/**
    * @access Public
    * @return Object
*/
function getRORBeneficioConcessaoValeTransporte() { return $this->roRBeneficioConcessaoValeTransporte;         }
/**
    * @access Public
    * @return Integer
*/
function getQuantidade() { return $this->inQuantidade;                                }
/**
    * @access Public
    * @return Boolean
*/
function getObrigatorio() { return $this->boObrigatorio;                               }
/**
    * @access Public
    * @return Integer
*/
function getCodDia() { return $this->inCodDia;                                    }

/**
     * Método construtor
     * @access Private
*/
function __construct()
{
    $this->setTransacao                                        ( new Transacao                                  );
    $this->setTBeneficioConcessaoValeTransporteSemanal         ( new TBeneficioConcessaoValeTransporteSemanal   );
    $this->setTAdministracaoDiasSemana                         ( new TDiasSemana                                );
    $this->setARRBeneficioConcessaoValeTransporteDiario        ( array()                                        );
}

/**
    * Cadastra Concessao Vale-Transporte Semanal
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação, caso esta exista
    * @return Object Objeto Erro retorna o valor, validando o método
    *
**/
function incluirConcessaoValeTransporteSemanal($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTBeneficioConcessaoValeTransporteSemanal->setDado('cod_concessao',     $this->roRBeneficioConcessaoValeTransporte->getCodConcessao()   );
        $this->obTBeneficioConcessaoValeTransporteSemanal->setDado('exercicio',         $this->roRBeneficioConcessaoValeTransporte->getExercicio()      );
        $this->obTBeneficioConcessaoValeTransporteSemanal->setDado('cod_dia',           $this->getCodDia()                                              );
        $this->obTBeneficioConcessaoValeTransporteSemanal->setDado('cod_mes',           $this->roRBeneficioConcessaoValeTransporte->getCodMes()         );
        $this->obTBeneficioConcessaoValeTransporteSemanal->setDado('quantidade',        $this->getQuantidade()                                          );
        $this->obTBeneficioConcessaoValeTransporteSemanal->setDado('obrigatorio',       $this->getObrigatorio()                                         );
        $obErro = $this->obTBeneficioConcessaoValeTransporteSemanal->inclusao( $boTransacao );
    }
    if ( !$obErro->ocorreu() ) {
        for ($inIndex=0;$inIndex<count($this->arRBeneficioConcessaoValeTransporteDiario);$inIndex++) {
            $obRBeneficioConcessaoValeTransporteDiario = &$this->arRBeneficioConcessaoValeTransporteDiario[$inIndex];
            $obErro = $obRBeneficioConcessaoValeTransporteDiario->incluirConcessaoValeTransporteDiario($boTransacao);
            if ( $obErro->ocorreu() ) {
                break;
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTBeneficioConcessaoValeTransporteSemanal );

    return $obErro;
}

/**
    * Alterar Concessao Vale-Transporte Semanal
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação, caso esta exista
    * @return Object Objeto Erro retorna o valor, validando o método
    *
**/
function alterarConcessaoValeTransporteSemanal($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTBeneficioConcessaoValeTransporteSemanal->setDado('cod_concessao',     $this->roRBeneficioConcessaoValeTransporte->getCodConcessao()   );
        $this->obTBeneficioConcessaoValeTransporteSemanal->setDado('exercicio',         $this->roRBeneficioConcessaoValeTransporte->getExercicio()      );
        $this->obTBeneficioConcessaoValeTransporteSemanal->setDado('cod_dia',           $this->getCodDia()                                              );
        $this->obTBeneficioConcessaoValeTransporteSemanal->setDado('cod_mes',           $this->roRBeneficioConcessaoValeTransporte->getCodMes()         );
        $this->obTBeneficioConcessaoValeTransporteSemanal->setDado('quantidade',        $this->getQuantidade()                                          );
        $this->obTBeneficioConcessaoValeTransporteSemanal->setDado('obrigatorio',       $this->getObrigatorio()                                         );
        $obErro = $this->obTBeneficioConcessaoValeTransporteSemanal->alteracao( $boTransacao );
    }
    if ( !$obErro->ocorreu() ) {
        for ($inIndex=0;$inIndex<count($this->arRBeneficioConcessaoValeTransporteDiario);$inIndex++) {
            $obRBeneficioConcessaoValeTransporteDiario = &$this->arRBeneficioConcessaoValeTransporteDiario[$inIndex];
            $obErro = $obRBeneficioConcessaoValeTransporteDiario->alterarConcessaoValeTransporteDiario($boTransacao);
            if ( $obErro->ocorreu() ) {
                break;
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTBeneficioConcessaoValeTransporteSemanal );

    return $obErro;
}
/**
    * Excluir Concessao Vale-Transporte Semanal
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação, caso esta exista
    * @return Object Objeto Erro retorna o valor, validando o método
    *
**/
function excluirConcessaoValeTransporteSemanal($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->addRBeneficioConcessaoValeTransporteDiario();
        $this->roRBeneficioConcessaoValeTransporteDiario->excluirConcessaoValeTransporteDiario($boTransacao);
    }
    if ( !$obErro->ocorreu() ) {
        $this->obTBeneficioConcessaoValeTransporteSemanal->setDado('cod_concessao', $this->roRBeneficioConcessaoValeTransporte->getCodConcessao());
        $this->obTBeneficioConcessaoValeTransporteSemanal->setDado('cod_mes',       $this->roRBeneficioConcessaoValeTransporte->getCodMes());
        $this->obTBeneficioConcessaoValeTransporteSemanal->setDado('exercicio',     $this->roRBeneficioConcessaoValeTransporte->getExercicio());
        $obErro = $this->obTBeneficioConcessaoValeTransporteSemanal->exclusao($boTransacao);
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTBeneficioConcessaoValeTransporteSemanal );

    return $obErro;
}

/**
    * Adiciona um RBeneficioConcessaoValeTransporteDiario ao array de referencia-objeto
    * @access Public
*/
function addRBeneficioConcessaoValeTransporteDiario()
{
     $this->arRBeneficioConcessaoValeTransporteDiario[] = new RBeneficioConcessaoValeTransporteDiario ();
     $this->roRBeneficioConcessaoValeTransporteDiario   = &$this->arRBeneficioConcessaoValeTransporteDiario[count($this->arRBeneficioConcessaoValeTransporteDiario) - 1 ];
     $this->roRBeneficioConcessaoValeTransporteDiario->setRORBeneficioConcessaoValeTransporteSemanal( $this );
}

/**
    * Método listar
    * @access Private
*/
function listar(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    $obErro = $this->obTBeneficioConcessaoValeTransporteSemanal->recuperaTodos($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

/**
    * Método listarConcessaoValeTransporteSemanal
    * @access Private
*/
function listarConcessaoValeTransporteSemanal(&$rsRecordSet,$boTransacao="")
{
    if ( $this->roRBeneficioConcessaoValeTransporte->getCodConcessao() ) {
        $stFiltro .= " AND cod_concessao = ". $this->roRBeneficioConcessaoValeTransporte->getCodConcessao();
    }
    if ( $this->roRBeneficioConcessaoValeTransporte->getCodMes() ) {
        $stFiltro .= " AND cod_mes = ".$this->roRBeneficioConcessaoValeTransporte->getCodMes();
    }
    if ( $this->roRBeneficioConcessaoValeTransporte->getExercicio() ) {
        $stFiltro .= " AND exercicio = '".$this->roRBeneficioConcessaoValeTransporte->getExercicio()."'";
    }
    if ($stFiltro) {
       $stFiltro = " WHERE ".substr($stFiltro,4,strlen($stFiltro));
    }
    $obErro = $this->listar($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

/**
    * Método listarDiasSemana
    * @access Public
*/
function listarDiasSemana(&$rsRecordSet,$boTransacao="")
{
    $obErro = $this->obTAdministracaoDiasSemana->recuperaTodos($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

}
?>
