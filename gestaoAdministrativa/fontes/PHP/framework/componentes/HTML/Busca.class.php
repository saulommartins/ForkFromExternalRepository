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
* Gerar o componente composto com a opcao de busca em POPUP
* Data de Criação: 08/02/2003

* @author Desenvolvedor: Diego Barbosa Victoria

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

/**
    * Classe que monta o HTML da Busca
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package framework
    * @subpackage componentes
*/
class Busca
{
/**
    * @access Private
    * @var Object
*/
var $obCampoCod;

/**
    * @access Private
    * @var Object
*/
var $obCampoDesc;

/**
    * @access Private
    * @var Object
*/
var $obImagem;

/**
    * @access Private
    * @var String
*/
var $stFuncaoCod;

/**
    * @access Private
    * @var String
*/
var $stFuncaoBusca;

/**
    * @access Private
    * @var String
*/
var $stNomeBusca;

/**
    * @access Private
    * @var String
*/
var $stHtml;

/**
    * @access Private
    * @var String
*/
var $stRotulo;

/**
    * @access Private
    * @var String
*/
var $stTitle;

/**
    * @access Private
    * @var Boolean
*/
var $boNull;

//SETTERS
/**
    * @access Public
    * @param Object $Valor
*/
function setCampoCod($valor) { $this->obCampoCod       = $valor; }

/**
    * @access Public
    * @param Object $Valor
*/
function setCampoDesc($valor) { $this->obCampoDesc      = $valor; }

/**
    * @access Public
    * @param Object $Valor
*/
function setImagem($valor) { $this->obImagem         = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setFuncaoCod($valor) { $this->stFuncaoCod      = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setFuncaoBusca($valor) { $this->stFuncaoBusca    = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setNomeBusca($valor) { $this->stNomeBusca      = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setHtml($valor) { $this->stHtml           = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setRotulo($valor) { $this->stRotulo         = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setTitle($valor) { $this->stTitle          = $valor; }

/**
    * @access Public
    * @param Boolean $Valor
*/
function setNull($valor) { $this->boNull           = $valor; }

//GETTERS
/**
    * @access Public
    * @return Object
*/
function getCampoCod() { return $this->obCampoCod;       }

/**
    * @access Public
    * @return Object
*/
function getCampoDesc() { return $this->obCampoDesc;      }

/**
    * @access Public
    * @return Object
*/
function getImagem() { return $this->obImagem;         }

/**
    * @access Public
    * @return String
*/
function getFuncaoCod() { return $this->stFuncaoCod;      }

/**
    * @access Public
    * @return String
*/
function getFuncaoBusca() { return $this->stFuncaoBusca;    }

/**
    * @access Public
    * @return String
*/
function getNomeBusca() { return $this->stNomeBusca;      }

/**
    * @access Public
    * @return String
*/
function getHtml() { return $this->stHtml;           }

/**
    * @access Public
    * @return String
*/
function getRotulo() { return $this->stRotulo;         }

/**
    * @access Public
    * @return String
*/
function getTitle() { return $this->stTitle;          }

/**
    * @access Public
    * @return Boolean
*/
function getNull() { return $this->boNull;           }

/**
    * Método construtor
    * @access Public
*/
function Busca()
{
    //DEFINICAO DO CAMPO COD
    $obCampoCod  = new TextBox;
    $obCampoCod->obEvento->setOnChange( $this->getFuncaoCod() );
    $obCampoCod->setSize        (8);
    $obCampoCod->setMaxLength   (8);
    $obCampoCod->setInteiro     ( true );
    $obCampoCod->setExpReg      ('[^0-9/.]');
    $obCampoCod->setName        ( "inCampoCod" );
    $this->setCampoCod          ( $obCampoCod );
    //DEFINICAO DO CAMPO DESC
    $obCampoDesc = new TextBox;
    $obCampoDesc->setReadOnly   ( true );
    $obCampoDesc->setSize       ( 30 );
    $obCampoDesc->setMaxLength  ( 30 );
    $obCampoDesc->setName       ( "stCampoDesc" );
    $this->setCampoDesc         ( $obCampoDesc );
    //DEFINICAO DA IMAGEM
    $obImagem    = new Img;
    $obImagem->setCaminho   ( CAM_FW_IMAGENS."botao_popup.png");
    $this->setImagem        ( $obImagem );
    $this->setNull          ( true );
}

/**
    * Monta o HTML do Objeto Busca
    * @access Protected
*/
function montaHtml()
{
    if ( $this->getNomeBusca() ) {
        $stNomeBusca = $this->getNomeBusca();
        $this->obCampoCod->setName  ( "inCod".$stNomeBusca );
        $this->obCampoDesc->setName ( "stNom".$stNomeBusca );
    }
    $this->obCampoCod->montaHtml    ();
    $this->obCampoDesc->montaHtml   ();
    $this->obImagem->montaHtml      ();

    $stHtml  = $this->obCampoCod->getHtml   ();
    $stHtml .= $this->obCampoDesc->getHtml  ();
    $stHtml .= "&nbsp;<a href=\"JavaScript: ".$this->getFuncaoBusca().";\">";
    $stHtml .= $this->obImagem->getHtml     ();
    $stHtml .= "</a>";
    $this->setHtml( $stHtml );
}

/**
    * Imprime o HTML do Objeto Busca na tela (echo)
    * @access Public
*/
function show()
{
    $this->montaHtml();
    $stHtml = $this->getHtml();
    $stHtml =  trim( $stHtml )."\n";
    echo $stHtml;
}
}
?>
