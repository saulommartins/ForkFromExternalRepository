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
* Montar uma linha de uma tabela de acordo com os valores setados pelo usuário
* Data de Criação: 05/02/2003

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

/**
    * Classe que monta uma linha de uma tabela

    * @package framework
    * @subpackage componentes
*/
class Linha extends Objeto
{
/**
    * @access Private
    * @var String
*/
var $stAlign;

/**
    * @access Private
    * @var String
*/
var $stValign;

/**
    * @access Private
    * @var String
*/
var $hxBgColor;

/**
    * @access Private
    * @var String
*/
var $stId;

/**
    * @access Private
    * @var String
*/
var $stClass;

/**
    * @access Private
    * @var String
*/
var $stStyle;

/**
    * @access Private
    * @var String
*/
var $stTitle;

/**
    * @access Private
    * @var String
*/
var $stLang;

/**
    * @access Private
    * @var String
*/
var $stDir;

/**
    * @access Private
    * @var String
*/
var $ultimaCelula;

/**
    * @access Private
    * @var Array
*/
var $arCelula;

/**
    * @access Private
    * @var Integer
*/
var $inNivel;

/**
    * @access Private
    * @var String
*/
var $stIdent;

/**
    * @access Private
    * @var String
*/
var $stHTML;

//SETTERS
/**
    * @access Public
    * @param String $Valor
*/
function setAlign($valor) { $this->stAlign      = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setValign($valor) { $this->stValign     = $valor; }

/**
    * @access Public
    * @param Boolean $Valor
*/
function setBgColor($valor) { $this->hxBgColor    = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setId($valor) { $this->stId         = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setClass($valor) { $this->stClass      = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setStyle($valor) { $this->stStyle      = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setTitle($valor) { $this->stTitle      = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setLang($valor) { $this->stLang       = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setDir($valor) { $this->stDir        = $valor; }

/**
    * @access Public
    * @param Boolean $Valor
*/
function setUltimaCelula($valor) { $this->ultimaCelula = $valor; }

/**
    * @access Public
    * @param Array $Valor
*/
function setCelula($valor) { $this->arCelula     = $valor; }

/**
    * @access Public
    * @param Integer $Valor
*/
function setNivel($valor) { $this->inNivel      = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setIdent($valor) { $this->stIdent      = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setHTML($valor) { $this->stHTML       = $valor; }

//GETTERS
/**
    * @access Public
    * @return String
*/
function getAlign() { return $this->stAlign;      }

/**
    * @access Public
    * @return String
*/
function getValign() { return $this->stValign;     }

/**
    * @access Public
    * @return Boolean
*/
function getBgColor() { return $this->hxBgColor;    }

/**
    * @access Public
    * @return String
*/
function getId() { return $this->stId;         }

/**
    * @access Public
    * @return String
*/
function getClass() { return $this->stClass;      }

/**
    * @access Public
    * @return String
*/
function getStyle() { return $this->stStyle;      }

/**
    * @access Public
    * @return String
*/
function getTitle() { return $this->stTitle;      }

/**
    * @access Public
    * @return String
*/
function getLang() { return $this->stLang;       }

/**
    * @access Public
    * @return String
*/
function getDir() { return $this->stDir;        }

/**
    * @access Public
    * @return Boolean
*/
function getUltimaCelula() { return $this->ultimaCelula; }

/**
    * @access Public
    * @return Array
*/
function getCelula() { return $this->arCelula;     }

/**
    * @access Public
    * @return Integer
*/
function getNivel() { return $this->inNivel;      }

/**
    * @access Public
    * @return String
*/
function getIdent() { return $this->stIdent;      }

/**
    * @access Public
    * @return String
*/
function getHTML() { return $this->stHTML;       }

/**
    * Método construtor
    * @access Public
*/
function Linha()
{
    $arCelula = array();
    $this->setCelula( $arCelula );
    $this->setIdent("    ");
}

/**
    * FALTA DESCRICAO
    * @access Public
*/
function addCelula()
{
    $obCelula = new Celula;
    $this->setUltimaCelula( $obCelula );
}

/**
    * FALTA DESCRICAO
    * @access Public
*/
function commitCelula()
{
    $arCelula = $this->getCelula();
    $arCelula[] = $this->getUltimaCelula();
    $this->setCelula( $arCelula );
}

/**
    * FALTA DESCRICAO
    * @access Public
    * @param Integer $inNivel
*/
function identa($inNivel = 0)
{
    $stIdent = $this->getIdent();
    $stIdentNivel = "";
    $inCont = 1;
    switch ($inNivel) {
        case 0:
            $inNivel = $this->getNivel();
        break;
        case 1:
            $inNivel = $this->getNivel() + 1 ;
            $this->setNivel( $inNivel );
        break;
        case -1:
            $inNivel = $this->getNivel() - 1 ;
            $this->setNivel( $inNivel );
        break;
    }
    while ($inCont <= $inNivel) {
        $stIdentNivel .= $stIdent;
        $inCont++;
    }

    return $stIdentNivel;
}

/**
    * Monta o HTML do Objeto Linha
    * @access Protected
*/
function montaHTML()
{
    $stTr = "";
    if ( $this->getAlign() ) {
        $stTr .= " align=\"".$this->getAlign()."\"";
    }
    if ( $this->getValign() ) {
        $stTr .= " valign=\"".$this->getValign()."\"";
    }
    if ( $this->getBgColor() ) {
        $stTr .= " bgcolor=\"".$this->getBgColor()."\"";
    }
    if ( $this->getId() ) {
        $stTr .= " id=\"".$this->getId()."\"";
    }
    if ( $this->getClass() ) {
        $stTr .= " class=\"".$this->getClass()."\"";
    }
    if ( $this->getStyle() ) {
        $stTr .= " style=\"".$this->getStyle()."\"";
    }
    if ( $this->getTitle() ) {
        $stTr .= " title=\"".$this->getTitle()."\"";
    }
    if ( $this->getLang() ) {
        $stTr .= " lang=\"".$this->getLang()."\"";
    }
    if ( $this->getDir() ) {
        $stTr .= " dir=\"".$this->getDir()."\"";
    }
    $stTr = $this->identa()."<tr ".trim($stTr).">\n";
    $arCelula = $this->getCelula();
    foreach ($arCelula as $obCelula) {
        $inNivel = $this->getNivel() + 1;
        $obCelula->setNivel( $inNivel );
        $obCelula->montaHTML();
        $stTr .= $obCelula->getHTML();
    }
    $stTr .= $this->identa()."</tr>\n";
    $this->setHTML( $stTr );
}

}
?>
