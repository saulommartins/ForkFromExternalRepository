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
* Classe de negócio Expressões Regulares
* Data de Criação: 25/01/2006

* @author Analista: Cassiano de Vasconcellos Ferreira
* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Desenvolvedor: Lizandro Kirst da Silva

$Revision: 8315 $
$Name$
$Author: rodrigo $
$Date: 2006-04-06 16:33:20 -0300 (Qui, 06 Abr 2006) $

Casos de uso: uc-01.01.00
*/

//include_once( "../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php");

class ExpReg
{
/**
    * @access Private
    * @var String
*/
var $stExpReg;
/**
    * @access Private
    * @var String
*/
var $stContexto;

/**
    * @access Public
    * @param String $Valor
*/
function setExpReg($valor) { $this->stExpReg           = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setContexto($valor) { $this->stContexto           = $valor; }
/**
    * @access Public
    * @return String
*/
function getExpReg() { return $this->stExpReg           ; }
/**
    * @access Public
    * @return String
*/
function getContexto() { return $this->stContexto           ; }

/**
     * Método construtor
     * @access Private
*/
function ExpReg($stExpReg = "", $stContexto = "")
{
    $this->setExpReg( $stExpReg );
    $this->setContexto( $stContexto );
}

/**
    * Verifica a Ocorrencia da Expressão Regular
    * @return Bool
*/
function executarEreg($stExpReg, $stContexto)
{
   return preg_match( "/".$stExpReg."/i", $stContexto  );
}

/**
    * Valida a Existencia de um Contexto
    * @return Bool
*/
function validarContexto()
{
    $stExpReg = $this->getExpReg();
    if ( strpos( $this->getExpReg() , "^") !== 0 ) {
        $stExpReg = "^".$stExpReg;
    }
    if ( substr( $this->getExpReg(), strlen($this->getExpReg()) - 2, 2 ) != "\$" AND strrpos( $this->getExpReg() , "$" ) != strlen($this->getExpReg() ) ) {
        $stExpReg = $stExpReg."$";
    }

    return $this->executarEreg( $stExpReg, $this->getContexto() );
}

/**
    * Verifica a Ocorrencia de uma Expressão Regular
    * @return Bool
*/
function verificarOcorrencia()
{
    return $this->executarEreg( $this->getExpReg(), $this->getContexto() );
}
/**
    * Numera a Ocorrencia de uma Expressão Regular
    * @return Bool
*/
function contarOcorrencias()
{
    return count( $this->buscarOcorrencias() );
}
/**
    * Busca a Ocorrencia de uma Expressão Regular
    * @return Bool
*/
function buscarOcorrencias()
{
    preg_match_all( "|".$this->getExpReg()."|", $this->getContexto(), $arRegistros , PREG_PATTERN_ORDER);

    return $arRegistros[0];
}

/**
    * Altera a Ocorrencia de uma Expressão Regular
    * @return Bool
*/
function alterarOcorrencias($stAlterar)
{
     return preg_replace( "/".$this->getExpReg()."/", $stAlterar, $this->getContexto() );
}
/**
    * Remove a Ocorrencia de uma Expressão Regular
    * @return Bool
*/
function removerOcorrencias()
{
    return $this->alterarOcorrencias( "" );
}
/**
    * Explode a Ocorrencia de uma Expressão Regular
    * @return Bool
*/
function explodirContexto()
{
     return preg_split( "/".$this->getExpReg()."/", $this->getContexto() );
}
}
