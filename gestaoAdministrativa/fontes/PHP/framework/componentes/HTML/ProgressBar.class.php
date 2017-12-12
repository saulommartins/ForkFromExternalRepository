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
* Classe de Componente de Barra de Progresso
* Data de Criação: 20/04/2004

* @author Desenvolvedor: Jorge Batista Ribarr

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

include_once ( CLA_OBJETO );

/**
    * Classe de Componente de Barra de Progresso
    * @author Jorge Batista Ribarr
*/
class ProgressBar extends Objeto
{
/**#@+
    * @access Private
    * @var String
*/
var $stHtml;
var $stCelEstilo;
var $stDivEstilo;
var $stCorQuadrinho;
var $stBordaQuadrinho;
/**#@-*/
/**
    * @access Private
    * @var Integer
*/
var $inQtdeQuadrinho;
var $inIntervalo;
/**#@-*/
/**#@+
    * @access Private
    * @var Boolean
*/
var $boNull;
var $boBarraExecucao;
/**#@-*/

//SETTERS
/**#@+
    * @access Public
    * @param String $Valor
*/
function setName($valor) { $this->stName            = $valor;      }
function setDefinicao($valor) { $this->stDefinicao       = $valor;      }
function setHtml($valor) { $this->stHtml            = $valor;      }
function setCelEstilo($valor) { $this->stCelEstilo       = $valor;      }
function setDivEstilo($valor) { $this->stDivEstilo       = $valor;      }
function setCorQuadrinho($valor) { $this->stCorQuadrinho    = $valor;      }
function setBordaQuadrinho($valor) { $this->stBordaQuadrinho  = $valor;      }
/**#@-*/
/**#@+
    * @access Public
    * @param Integer $Valor
*/
function setQtdeQuadrinho($valor) { $this->inQtdeQuadrinho   = (int) $valor; }
function setIntervalo($valor) { $this->inIntervalo       = (int) $valor; }
/**#@-*/
/**#@+
    * @access Public
    * @param Boolean $Valor
*/
function setNull($valor) { $this->boNull            = $valor;      }
function setBarraExecucao($valor) { $this->boBarraExecucao   = $valor;      }
/**#@-*/

//GETTERS
/**#@+
    * @access Public
    * @return String
*/
function getName() { return $this->stName;               }
function getDefinicao() { return $this->stTitle;              }
function getHtml() { return $this->stHtml;               }
function getCelEstilo() { return $this->stCelEstilo;          }
function getDivEstilo() { return $this->stDivEstilo;          }
function getCorQuadrinho() { return $this->stCorQuadrinho;       }
function getBordaQuadrinho() { return $this->stBordaQuadrinho;     }
/**#@-*/
/**#@+
    * @access Public
    * @return Integer
*/
function getQtdeQuadrinho() { return (int) $this->inQtdeQuadrinho; }
function getIntervalo() { return (int) $this->inIntervalo;     }
/**#@-*/
/**#@+
    * @access Public
    * @return Boolean
*/
function getNull() { return $this->boNull;               }
function getBarraExecucao() { return $this->boBarraExecucao;      }
/**#@-*/

//METODO CONSTRUTOR
/**
     * Método construtor
     * @access Public
*/
function ProgressBar()
{
    $this->setNull           ( true );
    $this->setName           ( 'ProgressBar' );
    $this->setDefinicao      ( 'ProgressBar' );
    $this->setQtdeQuadrinho  ( 20 );
    $this->setCorQuadrinho   ( 'orange');
    $this->setBordaQuadrinho ( '1px outset' );
    $this->setIntervalo      ( 200 );
    $this->setCelEstilo      ('
                               background-color : #B6BBB6;
                               border: 1px outset #5F625F;
                               border-bottom : 1 solid #5F625F;
                               border-right : 1 solid #5F625F;
                               border-left : 1 solid #EBF2EB;
                               border-top : 1 solid #EBF2EB;');
    $this->setDivEstilo      ('font-size:8pt;padding:2px;border:1px inset #B6BBB6');
    $this->setBarraExecucao  ( false );
}

//METODOS DA CLASSE
/**
     * Monta o esqueleto do HTML
     * @access Protected
*/
function montaHTML()
{
    $this->setNull      ( true );
    $stHtml  = '<table align="center" cellpadding="3" cellspacing="0">';
    $stHtml .= '<tr><td>';
    $stHtml .= '<img src="'.CAM_FW_IMAGENS.'/loading.gif">';
    $stHtml .= '</td></tr>';

    $this->setHtml( $stHtml );
}

/**
     * Imprime o HTML montado
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
