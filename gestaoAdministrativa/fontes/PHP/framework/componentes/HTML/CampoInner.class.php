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
* Gerar campo Inner
* Data de Criação: 08/02/2003

* @author Desenvolvedor: Marcelo Boezzio Paulino

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

/**
    * Classe de que monta o HTML do Campo Inner

    * @package framework
    * @subpackage componentes
*/
class CampoInner extends Objeto
{
/**
    * @access Private
    * @var String
*/
var $stNomeBusca;

/**
    * @access Private
    * @var String
*/
var $stHTML;

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
    * @var String
*/
var $boNull;

/**
    * @access Private
    * @var String
*/
var $stDefinicao;

/**
    * @access Private
    * @var String
*/
var $stName;

/**
    * @access Private
    * @var String
*/
var $stId;

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
    * @var String
*/
var $stConteudo;

//SETTERS
/**
    * @access Public
    * @param String $valor
*/
function setNomeBusca($valor) { $this->stNomeBusca      = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setHTML($valor) { $this->stHTML           = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setRotulo($valor) { $this->stRotulo         = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setTitle($valor) { $this->stTitle          = $valor; }

/**
    * @access Public
    * @param Boolean $valor
*/
function setNull($valor) { $this->boNull           = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setDefinicao($valor) { $this->stDefinicao      = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setName($valor) { $this->stName           = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setId($valor) { $this->stId             = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setWidth($valor) { $this->inWidth          = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setHeight($valor) { $this->inHeight         = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setConteudo($valor) { $this->stConteudo       = $valor; }

//GETTERS
/**
    * @access Public
    * @return String
*/
function getNomeBusca() { return $this->stNomeBusca;      }

/**
    * @access Public
    * @return String
*/
function getHTML() { return $this->stHTML;           }

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
    * @access Public
    * @return String
*/
function getDefinicao() { return $this->stDefinicao;      }

/**
    * @access Public
    * @return String
*/
function getName() { return $this->stName;           }

/**
    * @access Public
    * @return String
*/
function getId() { return $this->stId;             }

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
    * @return String
*/
function getConteudo() { return $this->stConteudo;       }

/**
    * Método Construtor
    * @access Public
*/
function CampoInner()
{
    $this->setDefinicao     ( "BUSCAINNER" );
    $this->setNull          ( true         );
    $this->setWidth         ( 100          );
    $this->setHeight        ( 20           );
    $this->setConteudo      ( "&nbsp;"     ) ;
}

/**
    * Monta o HTML do Objeto CampoInner
    * @access Protected
*/
function montaHTML()
{
    $obTabela = new Tabela;
    $obTabela->setCellPadding( 0 );
    $obTabela->setCellSpacing( 0 );
    $obTabela->setWidth( 100 );
    $obTabela->addLinha();

    $obTabela->ultimaLinha->addCelula();
    $obTabela->ultimaLinha->ultimaCelula->setClass( "fakefield" );
    $obTabela->ultimaLinha->ultimaCelula->setWidth( $this->getWidth() );
    $obTabela->ultimaLinha->ultimaCelula->setHeight( $this->getHeight() );
    $obTabela->ultimaLinha->ultimaCelula->setId( $this->getId() );
    $obTabela->ultimaLinha->ultimaCelula->addConteudo( $this->getConteudo() );
    $obTabela->ultimaLinha->commitCelula();

    $obTabela->ultimaLinha->addCelula();
    $obTabela->ultimaLinha->ultimaCelula->setWidth( ( 100 - $this->getWidth() ) );
    $obTabela->ultimaLinha->ultimaCelula->addConteudo( '&nbsp;' );
    $obTabela->ultimaLinha->commitCelula();

    $obTabela->commitLinha();

    $obTabela->montaHTML();
    $this->setHTML( $obTabela->getHTML() );
}

/**
    * Imprime o HTML do Objeto Label na tela (echo)
    * @access Public
*/
function show()
{
    $this->montaHTML();
    $stHTML = $this->getHTML();
    $stHTML = trim( $stHTML )."\n";
}
}
?>
