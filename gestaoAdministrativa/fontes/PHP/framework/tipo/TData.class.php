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

class TData
{
/**
    * @var String
    * @access Private
*/
var $stData;

/**
     * @access Public
     * @param String $valor
*/
function setData($valor)
{
    $obErro = $this->validaData( $valor );

    if ( !$obErro->ocorreu() ) {
        $this->stData  = $valor;
    }

    return $obErro;
}

/**
     * @access Public
     * @param String $valor
*/
function getData() { return $this->stData; }

/**
    * Método Construtor
    * @access Private
*/
function TData()
{
}

/**
    * Valida Data Informada
    * @access Private
    * @param String DATA
    * @return Object Erro
*/
function validaData($valor)
{
    $obErro = new Erro;

    if ($valor) {
    list( $dia,$mes,$ano ) = explode( '/', $valor );
    if(!checkdate ( $mes, $dia, $ano ))
        $obErro->setDescricao("Data Inválida");
    }

    return $obErro;
}

}
