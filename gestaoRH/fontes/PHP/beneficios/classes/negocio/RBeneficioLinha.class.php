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
* Classe de regra de negócio da tabela BENEFICIO.LINHA
* Data de Criação: 07/07/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @package URBEM
* @subpackage regra de negócio

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

* Casos de uso: uc-04.06.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_BEN_MAPEAMENTO."TBeneficioLinha.class.php"        );

class RBeneficioLinha
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
var $obTBeneficioLinha;
/**
    * @access Private
    * @var Integer
*/
var $inCodLinha;
/**
    * @access Private
    * @var String
*/
var $stDescricao;

/**
    * @access Public
    * @param Object $Valor
*/
function setTBeneficioLinha($valor) { $this->obTBeneficioLinha      = $valor  ; }
/**
    * @access Public
    * @param Integer $Valor
*/
function setCodLinha($valor) { $this->inCodLinha      = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setDescricao($valor) { $this->stDescricao  = $valor ; }

/**
    * @access Public
    * @return Object
*/
function getTBeneficioLinha() { return $this->obTBeneficioLinha      ; }
/**
    * @access Public
    * @return Integer
*/
function getCodLinha() { return $this->inCodLinha             ; }
/**
    * @access Public
    * @return String
*/
function getDescricao() { return $this->stDescricao            ; }

/**
     * Método construtor
     * @access Private
*/
function RBeneficioLinha()
{
    $this->setTBeneficioLinha  ( new TBeneficioLinha );
    $this->obTransacao         = new Transacao;
}

/**
    * Inclui dados da linha no banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirLinha($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro =  $this->obTBeneficioLinha->proximoCod( $inCodLinha , $boTransacao );
        $this->setCodLinha( $inCodLinha );
        if ( !$obErro->ocorreu() ) {
             $this->obTBeneficioLinha->setDado("cod_linha"   , $this->getCodLinha() );
             $this->obTBeneficioLinha->setDado("descricao"   , $this->getDescricao() );
             $obErro = $this->obTBeneficioLinha->inclusao( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTBeneficioLinha );

    return $obErro;
}

/**
    * Altera dados da linha no banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterarLinha($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTBeneficioLinha->setDado("cod_linha"   , $this->getCodLinha() );
        $this->obTBeneficioLinha->setDado("descricao"   , $this->getDescricao() );
        $obErro = $this->obTBeneficioLinha->alteracao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTBeneficioLinha );

    return $obErro;
}

/**
    * Exclui dados da linha no banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirLinha($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTBeneficioLinha->setDado("cod_linha"   , $this->getCodLinha() );
        $obErro = $this->obTBeneficioLinha->validaExclusao("", $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obErro = $this->obTBeneficioLinha->exclusao( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTBeneficioLinha );

    return $obErro;
}

/**
    * Executa um recuperaLista na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarLinha(&$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
{
    if ( $this->getCodLinha() ) {
       $this->obTBeneficioLinha->setDado('cod_lista',$this->getCodLinha());
    } else {
       $this->obTBeneficioLinha->setDado('cod_lista','null');
    }
    $obErro = $this->obTBeneficioLinha->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

}
