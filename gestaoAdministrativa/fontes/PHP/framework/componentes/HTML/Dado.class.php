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
* Montar uma célula de uma lista de acordo com os valores setados pelo usuário
* Data de Criação: 09/02/2003

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

class Dado extends Celula
{
/**
    * @access Private
    * @var String
*/
var $stAlinhamento;

/**
    * @access Private
    * @var String
*/
var $stClassDadoE;

/**
    * @access Private
    * @var String
*/
var $stClassDadoD;

/**
    * @access Private
    * @var String
*/
var $stClassDadoC;

/**
    * @access Private
    * @var String
*/
var $stMascara;

/**
    * @access Private
    * @var String
*/
var $stCampo;
/**
    * @access Private
    * @var String
*/
var $stTipoTotalizar;

//SETTERS
/**
    * @access Public
    * @param String $valor
*/
function setAlinhamento($valor) { $this->stAlinhamento    = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setClassDadoE($valor) { $this->stClassDadoE     = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setClassDadoD($valor) { $this->stClassDadoD     = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setClassDadoC($valor) { $this->stClassDadoC     = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setMascara($valor) { $this->stMascara        = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setCampo($valor) { $this->stCampo          = $valor; }
/**
    * @access Public
*/
function setSomar() { $this->stTipoTotalizar = "somar"; }
/**
    * @access Public
*/
function setContar() { $this->stTipoTotalizar = "contar"; }

//GETTERS

/**
    * @access Public
    * @return String
*/
function getAlinhamento() { return $this->stAlinhamento;}

/**
    * @access Public
    * @return String
*/
function getClassDadoE() { return $this->stClassDadoE; }

/**
    * @access Public
    * @return String
*/
function getClassDadoD() { return $this->stClassDadoD; }

/**
    * @access Public
    * @return String
*/
function getClassDadoC() { return $this->stClassDadoC; }

/**
    * @access Public
    * @return String
*/
function getMascara() { return $this->stMascara;    }

/**
    * @access Public
    * @return String
*/
function getCampo() { return $this->stCampo;      }
/**
    * @access Public
*/
function getTipoTotalizar() { return $this->stTipoTotalizar;      }

/**
    * Método Construtor
    * @access Public
*/
function Dado()
{
    parent::Celula();
    $this->setClassDadoE    ( "show_dados" );
    $this->setClassDadoD    ( "show_dados_right" );
    $this->setClassDadoC    ( "show_dados_center" );
    $this->setAlinhamento   ( "ESQUERDA" );
}

/**
    * Monta o HTML do Objeto Dado
    * @access Protected
*/
function montaHTML()
{
    $stAlinhamento = strtoupper( $this->getAlinhamento() );
    switch ( $this->getAlinhamento() ) {
        case "ESQUERDA":
        case "LEFT":
        case "E":
        case "ESQ":
            $this->setClass( $this->getClassDadoE() );
        break;
        case "DIREITA":
        case "DIR":
        case "RIGHT":
            $this->setClass( $this->getClassDadoD() );
        break;
        case "CENTER":
        case "CENTRO":
            $this->setClass( $this->getClassDadoC() );
        break;
        case "CSS":
            $this->setClass( $this->stClass );
        break;
    }

    parent::montaHTML();
}

}
?>
