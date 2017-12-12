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
*
* Data de Criação: 27/10/2005

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Documentor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00
*/

/**
    * Classe que contêm uma estrutura de armazenamento para dados relacionados a erro
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
    * @author Documentor: Diego Barbosa Victoria
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/GA.inc.php';
include_once(CLA_OBJETO);

class Erro extends Objeto
{
/**
    * @var String
    * @access Private
*/
var $stDescricao;
/**
    * @var Integer
    * @access Private
*/
var $inCodigo;

/**
    * @access Public
    * @param String $valor
*/
function setDescricao($valor) { $this->stDescricao      = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodigo($valor) { $this->inCodigo         = $valor; }

/**
    * @access Public
    * @return String
*/
function getDescricao() { return $this->stDescricao;  }
/**
    * @access Public
    * @return Integer
*/
function getCodigo() { return $this->inCodigo;     }

/**
    * Método Construtor
    * @access Private
*/
function Erro() { }

/**
    * Método verificador de erro.
    * @access Public
    * @return Boolean
*/
function ocorreu()
{
    if ( strlen( $this->getDescricao() ) ) {
        $boRetorno = true;
    } else {
        $boRetorno = false;
    }

    return $boRetorno;
}

}
