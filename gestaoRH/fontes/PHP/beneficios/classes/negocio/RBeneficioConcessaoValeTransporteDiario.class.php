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
* Classe de regra de negócio para RBeneficioConcessaoValeTransporteDiario
* Data de Criação: 18/10/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @package URBEM
* @subpackage Regra de Negócio

$Id: RBeneficioConcessaoValeTransporteDiario.class.php 65736 2016-06-10 20:18:11Z michel $

* Casos de uso: uc-04.06.09
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GRH_BEN_MAPEAMENTO."TBeneficioConcessaoValeTransporteDiario.class.php";

class RBeneficioConcessaoValeTransporteDiario
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
var $obTBeneficioConcessaoValeTransporteDiario;
/**
   * @access Private
   * @var Object
*/
var $roRBeneficioConcessaoValeTransporteSemanal;
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
   * @var Date
*/
var $dtDia;

/**
    * @access Public
    * @param Object $valor
*/
function setTransacao($valor) { $this->obTransacao                                 = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setTBeneficioConcessaoValeTransporteDiario($valor) { $this->obTBeneficioConcessaoValeTransporteDiario   = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRORBeneficioConcessaoValeTransporteSemanal(&$valor) { $this->roRBeneficioConcessaoValeTransporteSemanal  = &$valor; }
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
    * @param Date $valor
*/
function setDia($valor) { $this->dtDia                                       = $valor; }

/**
    * @access Public
    * @return Object
*/
function getTransacao() { return $this->obTransacao;                                 }
/**
    * @access Public
    * @return Object
*/
function getTBeneficioConcessaoValeTransporteDiario() { return $this->obTBeneficioConcessaoValeTransporteDiario;   }
/**
    * @access Public
    * @return Object
*/
function getRBeneficioConcessaoValeTransporteSemanal() { return $this->roRBeneficioConcessaoValeTransporteSemanal;  }
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
    * @return Date
*/
function getDia() { return $this->dtDia;                                       }

/**
     * Método construtor
     * @access Private
*/
function __construct()
{
    $this->setTransacao                               ( new Transacao                               );
    $this->setTBeneficioConcessaoValeTransporteDiario ( new TBeneficioConcessaoValeTransporteDiario );
}

/**
    * Cadastra Concessao Vale-Transporte Diario
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação, caso esta exista
    * @return Object Objeto Erro retorna o valor, validando o método
    *
**/
function incluirConcessaoValeTransporteDiario($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obCalendario = new Calendario;
        $arData = explode('/',$this->getDia());
        $inDiaSemana = ($obCalendario->retornaDiaSemana($arData[0],$arData[1],$arData[2])) + 1;
        $this->obTBeneficioConcessaoValeTransporteDiario->setDado('cod_concessao',  $this->roRBeneficioConcessaoValeTransporteSemanal->roRBeneficioConcessaoValeTransporte->getCodConcessao());
        $this->obTBeneficioConcessaoValeTransporteDiario->setDado('dt_dia',         $this->getDia()                                                                                          );
        $this->obTBeneficioConcessaoValeTransporteDiario->setDado('exercicio',      $this->roRBeneficioConcessaoValeTransporteSemanal->roRBeneficioConcessaoValeTransporte->getExercicio()   );
        $this->obTBeneficioConcessaoValeTransporteDiario->setDado('cod_dia',        $inDiaSemana                                                                                             );
        $this->obTBeneficioConcessaoValeTransporteDiario->setDado('cod_mes',        $this->roRBeneficioConcessaoValeTransporteSemanal->roRBeneficioConcessaoValeTransporte->getCodMes()      );
        $this->obTBeneficioConcessaoValeTransporteDiario->setDado('obrigatorio',    $this->getObrigatorio()                                                                                  );
        $this->obTBeneficioConcessaoValeTransporteDiario->setDado('quantidade',     $this->getQuantidade()                                                                                   );
        $obErro = $this->obTBeneficioConcessaoValeTransporteDiario->inclusao($boTransacao);
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro,$this->obTBeneficioConcessaoValeTransporteDiario );

    return $obErro;
}

/**
    * Alterar Concessao Vale-Transporte Diario
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação, caso esta exista
    * @return Object Objeto Erro retorna o valor, validando o método
    *
**/
function alterarConcessaoValeTransporteDiario($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obCalendario = new Calendario;
        $arData = explode('/',$this->getDia());
        $inDiaSemana = ($obCalendario->retornaDiaSemana($arData[0],$arData[1],$arData[2])) + 1;
        $this->obTBeneficioConcessaoValeTransporteDiario->setDado('cod_concessao',  $this->roRBeneficioConcessaoValeTransporteSemanal->roRBeneficioConcessaoValeTransporte->getCodConcessao());
        $this->obTBeneficioConcessaoValeTransporteDiario->setDado('dt_dia',         $this->getDia()                                                                                          );
        $this->obTBeneficioConcessaoValeTransporteDiario->setDado('exercicio',      $this->roRBeneficioConcessaoValeTransporteSemanal->roRBeneficioConcessaoValeTransporte->getExercicio()   );
        $this->obTBeneficioConcessaoValeTransporteDiario->setDado('cod_dia',        $inDiaSemana                                                                                             );
        $this->obTBeneficioConcessaoValeTransporteDiario->setDado('cod_mes',        $this->roRBeneficioConcessaoValeTransporteSemanal->roRBeneficioConcessaoValeTransporte->getCodMes()      );
        $this->obTBeneficioConcessaoValeTransporteDiario->setDado('obrigatorio',    $this->getObrigatorio()                                                                                  );
        $this->obTBeneficioConcessaoValeTransporteDiario->setDado('quantidade',     $this->getQuantidade()                                                                                   );
        $obErro = $this->obTBeneficioConcessaoValeTransporteDiario->alteracao($boTransacao);
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro,$this->obTBeneficioConcessaoValeTransporteDiario );

    return $obErro;
}

/**
    * Excluir Concessao Vale-Transporte Diario
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação, caso esta exista
    * @return Object Objeto Erro retorna o valor, validando o método
    *
**/
function excluirConcessaoValeTransporteDiario($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTBeneficioConcessaoValeTransporteDiario->setDado('cod_concessao', $this->roRBeneficioConcessaoValeTransporteSemanal->roRBeneficioConcessaoValeTransporte->getCodConcessao());
        $this->obTBeneficioConcessaoValeTransporteDiario->setDado('cod_mes',       $this->roRBeneficioConcessaoValeTransporteSemanal->roRBeneficioConcessaoValeTransporte->getCodMes());
        $this->obTBeneficioConcessaoValeTransporteDiario->setDado('exercicio',     $this->roRBeneficioConcessaoValeTransporteSemanal->roRBeneficioConcessaoValeTransporte->getExercicio());
        $obErro = $this->obTBeneficioConcessaoValeTransporteDiario->exclusao($boTransacao);
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTBeneficioConcessaoValeTransporteDiario);

    return $obErro;
}

/**
    * Método listar
    * @access Private
*/
function listar(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    $obErro = $this->obTBeneficioConcessaoValeTransporteDiario->recuperaTodos($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

/**
    * Método listarConcessaoValeTransporteDiario
    * @access Private
*/
function listarConcessaoValeTransporteDiario(&$rsRecordSet,$boTransacao="")
{
    if ( $this->roRBeneficioConcessaoValeTransporteSemanal->roRBeneficioConcessaoValeTransporte->getCodConcessao() ) {
        $stFiltro .= " AND cod_concessao = ". $this->roRBeneficioConcessaoValeTransporteSemanal->roRBeneficioConcessaoValeTransporte->getCodConcessao();
    }
    if ( $this->roRBeneficioConcessaoValeTransporteSemanal->roRBeneficioConcessaoValeTransporte->getCodMes() ) {
        $stFiltro .= " AND cod_mes = ".$this->roRBeneficioConcessaoValeTransporteSemanal->roRBeneficioConcessaoValeTransporte->getCodMes();
    }
    if ( $this->roRBeneficioConcessaoValeTransporteSemanal->roRBeneficioConcessaoValeTransporte->getExercicio() ) {
        $stFiltro .= " AND exercicio = '".$this->roRBeneficioConcessaoValeTransporteSemanal->roRBeneficioConcessaoValeTransporte->getExercicio()."'";
    }
    if ($stFiltro) {
       $stFiltro = " WHERE ".substr($stFiltro,4,strlen($stFiltro));
    }
    $obErro = $this->listar($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

}
?>
