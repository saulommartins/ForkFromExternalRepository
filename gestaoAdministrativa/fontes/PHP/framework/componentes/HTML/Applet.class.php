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
* Gerar o componente tipo applet de acordo com os valores setados pelo Usuário
* Data de Criação: 10/11/2005

* @author Analista: Lucas Leusin
* @author Desenvolvedor: Anderson R. M. Buzo

* @package framework
* @subpackage componentes
*
* $Id: Applet.class.php 59612 2014-09-02 12:00:51Z gelson $

Casos de uso: uc-01.01.00, uc-02.04.18

*/

/**
    * Gera o componente tipo applet de acordo com os valores setados pelo Usuário
    * @author Analista: Lucas Leusin
    * @author Documentor: Anderson R. M. Buzo

    * @package Interface
    * @subpackage Componente
*/
class Applet extends Componente
{
//PROPRIEDADES DA CLASSE
/**
    * @access Private
    * @var String
*/
var $stArchive;
/**
    * @access Private
    * @var String
*/
var $stCode;
/**
    * @access Private
    * @var String
*/
var $stCodeBase;
/**
    * @access Private
    * @var Integer
*/
var $inWidth;
/**
    * @access Private
    * @var Integer
*/
var $inHeight;
/**
    * @access Private
    * @var Boolean
*/
var $boMayScript;
/**
    * @access Private
    * @var Array
*/
var $arParam;

/**
    * @access Public
    * @param String $valor
*/
function setArchive($valor) { $this->stArchive   = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setCode($valor) { $this->stCode      = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setCodeBase($valor) { $this->stCodeBase  = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setWidth($valor) { $this->inWidth     = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setHeight($valor) { $this->inHeight    = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setMayScript($valor) { $this->boMayScript = $valor; }
/**
    * @access Public
    * @param Array $valor
*/
function setParam($valor) { $this->arParam     = $valor; }

/**
    * @access Private
    * @return String
*/
function getArchive() { return $this->stArchive;  }
/**
    * @access Private
    * @return String
*/
function getCode() { return $this->stCode;       }
/**
    * @access Private
    * @return String
*/
function getCodeBase() { return $this->stCodeBase;   }
/**
    * @access Private
    * @return Integer
*/
function getWidth() { return $this->inWidth;      }
/**
    * @access Private
    * @return Integer
*/
function getHeight() { return $this->inHeight;     }
/**
    * @access Private
    * @return Boolean
*/
function getMayScript() { return $this->boMayScript;  }
/**
    * @access Private
    * @return Array
*/
function getParam() { return $this->arParam;      }

/**
    * Método Construtor
    * @access Public
*/
function Applet()
{
    parent::Componente();//CHAMA O METODO CONSTRUTOR DA CLASSE BASE
    $this->setWidth         ( 400        );
    $this->setHeight        ( 30         );
    $this->setName          ( "obApplet" );
    $this->setDefinicao     ( "applet"   );
    $this->setParam         ( array()    );
}

/**
    * Método para adicionar parametros a serem passados para o Applet
    * @access Public
*/
function addParam($stNomParametro, $stValorParametro)
{
    $this->arParam[count($this->arParam)-1]['stNomParametro'  ] = $stNomParametro;
    $this->arParam[count($this->arParam)-1]['stValorParametro'] = $stValorParametro;
}

/**
    * Monta o HTML do Objeto TextBox
    * @access Protected
*/
function montaHtml()
{
    $stHtml = "<applet ";
    if( $this->stName )
        $stHtml .= " name=\"".$this->stName."\" ";
    if( $this->stCodeBase )
        $stHtml .= " codbase=\"".$this->stCodeBase."\" ";
    if( $this->stCode )
        $stHtml .= " code=\"".$this->stCode."\" ";
    if( $this->stArchive )
        $stHtml .= " archive=\"".$this->stArchive."\" ";
    if( $this->boMayScript )
        $stHtml .= " MAYSCRIPT ";
    if( $this->stAlt )
        $stHtml .= " alt=\"".$this->stAlt."\" ";

    $stHtml .= " width=\"".$this->inWidth."\" ";
    $stHtml .= " height=\"".$this->inHeight."\" ";
    $stHtml .= " >";
    if ( count( $this->arParam ) ) {
        foreach ($this->arParam as $arParam) {
            $stHtml .= "\t<PARAM name='".$arParam['stNomParametro']."' value='".$arParam['stValorParametro']."' >\n";
        }
    }

    $stHtml .= " Este navegador não está habilitado para utilizar Java Applets, por favor, habilite e tente novamente!\n";
    $stHtml .= "</applet>\n ";
    $this->setHtml( $stHtml );
}

}
?>
