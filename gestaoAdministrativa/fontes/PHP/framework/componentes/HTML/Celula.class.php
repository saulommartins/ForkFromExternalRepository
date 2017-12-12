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
* Montar uma célula de uma tabela de acordo com os valores setados pelo usuário
* Data de Criação: 05/02/2003

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

/**
    * Classe de que monta o HTML da celula

    * @package framework
    * @subpackage componentes
*/
class Celula extends Objeto
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
var $inColSpan;

/**
    * @access Private
    * @var Integer
*/
var $inHeight;

/**
    * @access Private
    * @var Integer
*/
var $inRowSpan;

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
    * @var Boolean
*/
var $boNoWrap;

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
var $stTitleValue;

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
    * @var Object
*/
var $obTabela;

/**
    * @access Private
    * @var Boolean
*/
var $boTabela;

/**
    * @access Private
    * @var Array
*/
var $arComponente;

/**
    * @access Private
    * @var Boolean
*/
var $boComponente;

/**
    * @access Private
    * @var String
*/
var $stConteudo;

/**
    * @access Private
    * @var Boolean
*/
var $boConteudo;

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
    * @param Integer $valor
*/
function setWidth($valor) { $this->inWidth      = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setColSpan($valor) { $this->inColSpan    = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setHeight($valor) { $this->inHeight     = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setRowSpan($valor) { $this->inRowSpan  = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setAlign($valor) { $this->stAlign      = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setValign($valor) { $this->stValign     = $valor; }

/**
    * @access Public
    * @param Boolean $valor
*/
function setNoWrap($valor) { $this->boNoWrap     = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setId($valor) { $this->stId         = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setClass($valor) { $this->stClass      = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setStyle($valor) { $this->stStyle      = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setTitle($valor) { $this->stTitle      = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setTitleValue($valor) { $this->stTitleValue      = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setLang($valor) { $this->stLang       = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setDir($valor) { $this->stDir        = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setTabela($valor) { $this->obTabela     = $valor; }

/**
    * @access Public
    * @param Boolean $valor
*/
function setBoTabela($valor) { $this->boTabela     = $valor; }

/**
    * @access Public
    * @param Array $valor
*/
function setComponente($valor) { $this->arComponente = $valor; }

/**
    * @access Public
    * @param Boolean $valor
*/
function setBoComponente($valor) { $this->boComponente = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setConteudo($valor) { $this->stConteudo   = $valor; }

/**
    * @access Public
    * @param Boolean $valor
*/
function setBoConteudo($valor) { $this->boConteudo   = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setNivel($valor) { $this->inNivel      = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setIdent($valor) { $this->stIdent      = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setHTML($valor) { $this->stHTML       = $valor; }

//GETTERS
/**
    * @access Public
    * @return Integer
*/
function getWidth() { return $this->inWidth;      }

/**
    * @access Public
    * @return Integer
*/
function getColSpan() { return $this->inColSpan;    }

/**
    * @access Public
    * @return Integer
*/
function getHeight() { return $this->inHeight;     }

/**
    * @access Public
    * @return Integer
*/
function getRowSpan() { return $this->inRowSpan;    }

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
function getNoWrap() { return $this->boNoWrap;     }

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
function getTitleValue() { return $this->stTitleValue;      }

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
    * @return Object
*/
function getTabela() { return $this->obTabela;     }

/**
    * @access Public
    * @return Boolean
*/
function getBoTabela() { return $this->boTabela;     }

/**
    * @access Public
    * @return Array
*/
function getComponente() { return $this->arComponente; }

/**
    * @access Public
    * @return Boolean
*/
function getBoComponente() { return $this->boComponente; }

/**
    * @access Public
    * @return String
*/
function getConteudo() { return $this->stConteudo;   }

/**
    * @access Public
    * @return Boolean
*/
function getBoConteudo() { return $this->boConteudo;   }

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
    * Método Construtor
    * @access Public
*/
function Celula()
{
    $this->setNoWrap(false);
    $this->setIdent("    ");
    $arComponente = array();
    $this->setComponente( $arComponente );
    $this->obEvento = new Evento;
}

/**
    * FALTA DESCRICAO
    * @access Public
    * @param String $valor
*/
function addConteudo($valor)
{
    $this->setBoTabela(false);
    $this->setBoComponente(false);
    $this->setBoConteudo(true);
    $this->setConteudo($valor);

}

/**
    * FALTA DESCRICAO
    * @access Public
    * @param Object $obTabela
*/
function addTabela($obTabela)
{
    $this->setBoTabela(true);
    $this->setBoConteudo(false);
    $this->setBoComponente(false);
    $this->setTabela($obTabela);
}

/**
    * FALTA DESCRICAO
    * @access Public
    * @param Object $obComponente
*/
function addComponente(&$obComponente)
{
    $this->setBoComponente(true);
    $this->setBoConteudo(false);
    $this->setBoTabela(false);
    $arComponente = $this->getComponente();
    $arComponente[] = $obComponente;
    $this->setComponente( $arComponente );
}

/**
    * FALTA DESCRICAO
    * @access Public
    * @param Integer $inNivel
    * @return String
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
    * Monta o HTML do Objeto Celula
    * @access Protected
*/
function montaHTML()
{
    $stTd = "";
    if ( $this->getWidth() ) {
        $stTd .= " width=\"".$this->getWidth()."%\"";
    }
    if ( $this->getHeight() ) {
        $stTd .= " height=\"".$this->getHeight()."\"";
    }
    if ( $this->getColSpan() ) {
        $stTd .= " colspan=\"".$this->getColSpan()."\"";
    }
    if ( $this->getRowSpan() ) {
        $stTd .= " rowspan=\"".$this->getRowSpan()."\"";
    }
    if ( $this->getAlign() ) {
        $stTd .= " align=\"".$this->getAlign()."\"";
    }
    if ( $this->getValign() ) {
        $stTd .= " valign=\"".$this->getValign()."\"";
    }
    if ( $this->getNoWrap() ) {
        $stTd .= " nowrap";
    }
    if ( $this->getId() ) {
        $stTd .= " id=\"".$this->getId()."\"";
    }
    if ( $this->getClass() ) {
        $stTd .= " class=\"".$this->getClass()."\"";
    }
    if ( $this->getStyle() ) {
        $stTd .= " style=\"".$this->getStyle()."\"";
    }
    if ( $this->getTitleValue() ) {
        $stTd .= " title=\"".$this->getTitleValue()."\"";
    }
    else if ( $this->getTitle() ) {
        $stTd .= " title=\"".htmlentities($this->getTitle())."\"";
    }
    if ( $this->getLang() ) {
        $stTd .= " lang=\"".$this->getLang()."\"";
    }
    if ( $this->getDir() ) {
        $stTd .= " dir=\"".$this->getDir()."\"";
    }
    if ( $this->obEvento->getOnClick() ) {
        $stTd .= " onclick=\"JavaScript:".$this->obEvento->getOnClick()."\" ";
    }
    $stTd = $this->identa()."<td ".trim($stTd).">\n";
    $this->identa(1);
    if ( $this->getBoTabela() ) {
        $inNivel = $this->getNivel();
        $obTabela = $this->getTabela();
        $obTabela->setNivel( $inNivel );
        $obTabela->montaHTML();
        $stTd .= $obTabela->getHTML();
    } elseif ( $this->getBoComponente() ) {
        $arComponente = $this->getComponente();
        foreach ($arComponente as $obComponente) {
            $obComponente->montaHTML();
            $stTd .= $this->identa(  ).trim($obComponente->getHTML())."\n";
        }
    } elseif ( $this->getBoConteudo() ) {
        $stConteudo = $this->getConteudo();
        if ( is_null( $stConteudo ) ) {
            $stConteudo = "&nbsp;";
        }
        $stTd .= $this->identa(  ).$stConteudo."\n";
    } else {
        $stTd .= "&nbsp;";
    }
//    if ( $this->getTitle() ) {
    $stTd .= $this->identa( -1 )."</td>\n";
    $this->setHTML( $stTd );
}

}
?>
