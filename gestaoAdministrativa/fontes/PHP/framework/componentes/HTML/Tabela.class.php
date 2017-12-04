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
* Montar o HTML de uma tabela de acordo com os valores setados pelo usuário
* Data de Criação: 05/02/2003

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

/**
    * Classe que gera o HTML da Tabela
*/
class Tabela extends Objeto
{
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
    * @var Integer
*/
var $inCellSpacing;

/**
    * @access Private
    * @var Intege
*/
var $inCellPadding;

/**
    * @access Private
    * @var Integer
*/
var $inBorder;

/**
    * @access Private
    * @var String
*/
var $hxBorderColor;

/**
    * @access Private
    * @var String
*/
var $stAlign;

/**
    * @access Private
    * @var String
*/
var $hxBgColor;

/**
    * @access Private
    * @var String
*/
var $stBackGround;

/**
    * @access Private
    * @var String
*/
var $stFrame;

/**
    * @access Private
    * @var String
*/
var $stClass;

/**
    * @access Private
    * @var String
*/
var $stId;

/**
    * @access Private
    * @var String
*/
var $stStyle;

/**
    * @access Private
    * @var Array
*/
var $arLinha;

/**
    * @access Private
    * @var Object
*/
var $ultimaLinha;

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
    * @param Integer $Valor
*/
function setWidth($valor) { $this->inWidth          = $valor; }

/**
    * @access Public
    * @param Integer $Valor
*/
function setHeight($valor) { $this->inHeight         = $valor; }

/**
    * @access Public
    * @param Integer $Valor
*/
function setCellSpacing($valor) { $this->inCellSpacing    = $valor; }

/**
    * @access Public
    * @param Integer $Valor
*/
function setCellPadding($valor) { $this->inCellPadding    = $valor; }

/**
    * @access Public
    * @param Integer $Valor
*/
function setBorder($valor) { $this->inBorder         = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setBorderColor($valor) { $this->hxBorderColor    = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setAlign($valor) { $this->stAlign          = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setBgColor($valor) { $this->hxBgColor        = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setBackGround($valor) { $this->stBackGround     = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setFrame($valor) { $this->stFrame          = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setClass($valor) { $this->stClass          = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setId($valor) { $this->stId             = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setStyle($valor) { $this->stStyle          = $valor; }

/**
    * @access Public
    * @param Array $Valor
*/
function setLinha($valor) { $this->arLinha          = $valor; }

/**
    * @access Public
    * @param Object $Valor
*/
function setUltimaLinha($valor) { $this->ultimaLinha      = $valor; }

/**
    * @access Public
    * @param Integer $Valor
*/
function setNivel($valor) { $this->inNivel          = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setIdent($valor) { $this->stIdent          = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setHTML($valor) { $this->stHTML           = $valor; }

//GETTERS
/**
    * @access Public
    * @return Integer
*/
function getWidth() { return $this->inWidth;          }

/**
    * @access Public
    * @return Integer
*/
function getHeight() { return $this->inHeight;         }

/**
    * @access Public
    * @return Integer
*/
function getCellSpacing() { return $this->inCellSpacing;    }

/**
    * @access Public
    * @return Integer
*/
function getCellPadding() { return $this->inCellPadding;    }

/**
    * @access Public
    * @return Integer
*/
function getBorder() { return $this->inBorder;         }

/**
    * @access Public
    * @return String
*/
function getBorderColor() { return $this->hxBorderColor;    }

/**
    * @access Public
    * @return String
*/
function getAlign() { return $this->stAlign;          }

/**
    * @access Public
    * @return String
*/
function getBgColor() { return $this->hxBgColor;        }

/**
    * @access Public
    * @return String
*/
function getBackGround() { return $this->stBackGround;     }

/**
    * @access Public
    * @return String
*/
function getFrame() { return $this->stFrame;          }

/**
    * @access Public
    * @return String
*/
function getClass() { return $this->stClass;          }

/**
    * @access Public
    * @return String
*/
function getId() { return $this->stId;             }

/**
    * @access Public
    * @return String
*/
function getStyle() { return $this->stStyle;          }

/**
    * @access Public
    * @return Array
*/
function getLinha() { return $this->arLinha;          }

/**
    * @access Public
    * @return Object
*/
function getUltimaLinha() { return $this->ultimaLinha;      }

/**
    * @access Public
    * @return Integer
*/
function getNivel() { return $this->inNivel;          }

/**
    * @access Public
    * @return String
*/
function getIdent() { return $this->stIdent;          }

/**
    * @access Public
    * @return String
*/
function getHTML() { return $this->stHTML;           }

/**
    * Método construtor
    * @access Public
*/
function Tabela()
{
    $this->setWidth       ( 100 );
    $this->setCellSpacing ( 0 );
    $this->setCellPadding ( 0 );
    $this->setBorder      ( 0 );
    $this->setNivel       ( 0 );
    $this->setIdent       ("    ");
}

//MÉTODOS DA CLASSE
/**
    * FALTA DESCRICAO
    * @access Public
*/
function addLinha()
{
    $obLinha = new Linha;
    $this->setUltimaLinha( $obLinha );
}

/**
    * FALTA DESCRICAO
    * @access Public
*/
function commitLinha()
{
    $arLinha    = $this->getLinha();
    $arLinha[]  = $this->getUltimaLinha();
    $this->setLinha( $arLinha );
}

/**
    * FALTA DESCRICAO
    * @access Public
    * @return String
*/
function identa()
{
    $stIdent        = $this->getIdent();
    $stIdentNivel   = "";
    $inCont         = 1;
    $inNivel        = $this->getNivel();
    while ($inCont <= $inNivel) {
        $stIdentNivel .= $stIdent;
        $inCont++;
    }

    return $stIdentNivel;
}

/**
    * FALTA DESCRICAO
    * @param Integer $inPosicao
    * @param String $stIdDiv
*/
function addDiv($inPosicao, $stIdDiv = "", $stEstilo = "")
{
    $obDiv = new div;
    $obDiv->setId       ( $stIdDiv );
    $obDiv->setPosicao  ( $inPosicao );
    $obDiv->setEstilo   ( $stEstilo );
    $obDiv->setTagTable ( $this->montaTagTable() );
    $this->arLinha[] = $obDiv;
}

/**
    * FALTA DESCRICAO
    * @access Public
*/
function fechaDiv()
{
    $obDiv = new div;
    $obDiv->setTagTable ( $this->montaTagTable() );
    $this->arLinha[] = $obDiv;
}

/**
    * FALTA DESCRICAO
    * @access Public
    * @return String
*/
function montaTagTable()
{
        $stTable = "";
    if ( !is_null( $this->getWidth() ) ) {
        $stTable .= " width=\"".$this->getWidth()."%\"";
    }
    if ( !is_null( $this->getHeight() ) ) {
        $stTable .= " height=\"".$this->getHeight()."%\"";
    }
    if ( !is_null( $this->getCellSpacing() ) ) {
        $stTable .= " cellspacing=\"".$this->getCellSpacing()."\"";
    }
    if ( !is_null( $this->getCellPadding() ) ) {
        $stTable .= " cellpadding=\"".$this->getCellPadding()."\"";
    }
    if ( !is_null( $this->getBorder() ) ) {
        $stTable .= " border=\"".$this->getBorder()."\"";
    }
    if ( $this->getBorderColor() ) {
        $stTable .= " bordercolor=\"".$this->getBorderColor()."\"";
    }
    if ( $this->getAlign() ) {
        $stTable .= " align=\"".$this->getAlign()."\"";
    }
    if ( $this->getBgColor() ) {
        $stTable .= " bgcolor=\"".$this->getBgColor()."\"";
    }
    if ( $this->getBackGround() ) {
        $stTable .= " background=\"".$this->getBackGround()."\"";
    }
    if ( $this->getFrame() ) {
        $stTable .= " frame=\"".$this->getFrame()."\"";
    }
    if ( $this->getClass() ) {
        $stTable .= " class=\"".$this->getClass()."\"";
    }
    if ( $this->getId() ) {
        $stTable .= " id=\"".$this->getId()."\"";
    }
    if ( $this->getStyle() ) {
        $stTable .= " style=\"".$this->getStyle()."\"";
    }

    return trim($stTable);
}

/**
    * FALTA DESCRICAO
    * @access Public
*/
function montaHTML()
{
    $stQuebra = "";
    if ( $this->getNivel() == "" ) {
        $stQuebra = "\n";
    }
    $stTable = $stQuebra.$this->identa()."<table ".$this->montaTagTable().">\n";
    $arLinhas = $this->getLinha();
    if ( is_array( $arLinhas ) ) {
        foreach ($arLinhas as $obLinha) {
            $inNivel = $this->getNivel() + 1;
            $obLinha->setNivel( $inNivel );
            $obLinha->montaHTML();
            $stTable .= $obLinha->getHTML();
        }
    }
    $stTable .= $this->identa()."</table>\n";
    $this->setHTML( $stTable );
}

/**
    * Imprime o HTML do Objeto Tabela na tela (echo)
    * @access Public
*/
function show()
{
    $this->montaHTML();
    echo $this->getHTML();
}
}

/**
    * Classe que gera o HTML do div
*/
class div
{
    /**
        * @access Private
        * @var String
    */
    public $stId;
    /**
        * @access Private
        * @var String
    */
    public $inPosicao;
    /**
        * @access Private
        * @var String
    */
    public $stTagTable;

    /**
        * @access Private
        * @var String
    */
    public $stEstilo;

    /**
        * @access Public
        * @param Integer $Valor
    */
    public function setNivel($valor) { return 0;}

    /**
        * @access Public
        * @param Integer $Valor
    */
    public function setId($valor) { $this->stId = $valor;       }

    /**
        * @access Public
        * @param Integer $Valor
    */
    public function setEstilo($valor) { $this->stEstilo = $valor;       }

    /**
        * @access Public
        * @param Integer $Valor
    */
    public function setPosicao($valor) { $this->inPosicao = $valor;  }

    /**
        * @access Public
        * @param String $Valor
    */
    public function setTagTable($valor) { $this->stTagTable = $valor; }

    /**
        * Monta o HTML do Objeto Submit
        * @access Protected
    */
    public function montaHTML()
    {
        $stHTML  = "</table>\n";
        if ($this->inPosicao > 1) {
            $stHTML .= "</div>\n";
        }
        if ($this->stId != "") {
            $stHTML .= "<div id='".$this->stId."' style=\"".$this->stEstilo."\">\n";
        }
        $stHTML .= "<table ".$this->stTagTable.">\n";

        return $stHTML;
    }

    /**
        * @access Public
        * @return String
    */
    public function getHTML()
    {
        $stHTML = $this->montaHTML();

        return $stHTML;
    }

}

?>
