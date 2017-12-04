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
    * Arquivo de negócio da PL que replica os dados do orçamento para os 4 exercícios do ppa
    * Data de Criação   : 18/08/2009

    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Eduardo Paculski Schitz

    * @package URBEM
    * @subpackage

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_FW_BANCO_DADOS.'Transacao.class.php';

class RPPAGerarDadosPPA
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
var $stExercicioInicioPPA;
/**
    * @var String
    * @access Private
*/
var $stExercicioReplicar;

/**
     * @access Public
     * @param String $valor
*/
function setExercicioInicioPPA($valor) { $this->stExercicioInicioPPA = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setExercicioReplicar($valor) { $this->stExercicioReplicar = $valor;  }

/**
     * @access Public
     * @param String $valor
*/
function getExercicioInicioPPA() { return $this->stExercicioInicioPPA;           }
/**
     * @access Public
     * @param String $valor
*/
function getExercicioReplicar() { return $this->stExercicioReplicar;             }

/**
    * Método Construtor
    * @access Private
*/
function RPPAGerarDadosPPA()
{
    $this->obTransacao = new Transacao;
}

/**
    * Inclui dados no banco
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluir(&$rsRecordSet, $boTransacao = '')
{
    include_once CAM_GF_PPA_MAPEAMENTO.'FPPAGerarDadosPPA.class.php';
    $obFPPAGerarDadosPPA = new FPPAGerarDadosPPA;

    $obFPPAGerarDadosPPA->setDado('stExercicioInicioPPA', $this->stExercicioInicioPPA);
    $obFPPAGerarDadosPPA->setDado('stExercicioReplicar' , $this->stExercicioReplicar);
    $obErro = $obFPPAGerarDadosPPA->recuperaTodos($rsRecordSet, '', '', $boTransacao);

    return $obErro;
}

}
