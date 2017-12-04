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
* Classe de Regra de Negócio Usuario
* Data de Criação: 05/02/2004

* @author Desenvolvedor: Diego Barbosa Victoria

* @package bancoDados
* @subpackage PostgreSQL

Casos de uso: uc-01.01.00

*/

/**
    * Classe que contem a estrutura das tabelas do banco de dados
    * @author Desenvolvedor: Diego Barbosa Victoria
*/
class CampoTabela extends Objeto
{
/**
    * @var String
    * @access Private
*/
var $stNomeCampo;
/**
    * @var String
    * @access Private
*/
var $stConteudo;
/**
    * @var String
    * @access Private
*/
var $stTipoCampo;
/**
    * @var Boolean
    * @access Private
*/
var $boRequerido;
/**
    * @var Boolean
    * @access Private
*/
var $boPrimaryKey;
/**
    * @var String
    * @access Private
*/
var $stForeignKey;
/**
    * @var String
    * @access Private
*/
var $stCampoForeignKey;
/**
    * @var Integer
    * @access Private
*/
var $inTamanho;

/**
    * @access Public
    * @param String $valor
*/
function setNomeCampo($value) { $this->stNomeCampo           = $value; }
/**
    * @access Public
    * @param String $valor
*/
function setTipoCampo($value) { $this->stTipoCampo           = $value; }
/**
    * @access Public
    * @param String $valor
*/
function setConteudo($value) { $this->stConteudo            = $value; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setRequerido($value) { $this->boRequerido           = $value; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setPrimaryKey($value) { $this->boPrimaryKey          = $value; }
/**
    * @access Public
    * @param String $valor
*/
function setForeignKey($value) { $this->stForeignKey          = $value; }
/**
    * @access Public
    * @param String $valor
*/
function setCampoForeignKey($value) { $this->stCampoForeignKey     = $value; }
/**
    * @access Public
    * @param Integer $valor
*/
function setTamanho($value) { $this->inTamanho             = $value; }

/**
    * @access Public
    * @return String
*/
function getNomeCampo() { return $this->stNomeCampo  ; }
/**
    * @access Public
    * @return String
*/
function getTipoCampo() { return $this->stTipoCampo  ; }
/**
    * @access Public
    * @return String
*/
function getConteudo() { return $this->stConteudo   ; }
/**
    * @access Public
    * @return Boolean
*/
function getRequerido() { return $this->boRequerido  ; }
/**
    * @access Public
    * @return Boolean
*/
function getPrimaryKey() { return $this->boPrimaryKey ; }
/**
    * @access Public
    * @return String
*/
function getForeignKey() { return $this->stForeignKey ; }
/**
    * @access Public
    * @return String
*/
function getCampoForeignKey() { return $this->stCampoForeignKey ; }
/**
    * @access Public
    * @return Integer
*/
function getTamanho() { return $this->inTamanho    ; }

/**
    * Método Construtor
    * @access Private
*/
function CampoTabela()
{
    $this->setNomeCampo ( "" );
    $this->setConteudo  ( "" );
    $this->setTamanho   ( false );
    $this->setTipoCampo ( false );
    $this->setRequerido ( false );
    $this->setPrimaryKey( false );
    $this->setForeignKey( false );
}

/*function geraForeignKey(&$obMapeamento) {

}*/

}
