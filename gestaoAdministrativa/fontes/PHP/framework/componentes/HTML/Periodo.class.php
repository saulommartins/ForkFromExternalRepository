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
* Gerar o componente composto por duas Datas
* Data de Criação: 23/06/2005

* @author Analista: Diego Barbosa Victoria
* @author Desenvolvedor: Lucas Leusin Oaigen

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

/**
    * Cria o composto por duas Datas
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @package framework
    * @subpackage componentes
*/
class Periodo extends Componente
{
/**
    * @access Private
    * @var String
*/
var $stExercicio;
/**
    * @access Private
    * @var Boolean
*/
var $boValidaExercicio;
/**
    * @access Private
    * @var Object
*/
var $obDataInicial;
/**
    * @access Private
    * @var Object
*/
var $obDataFinal;
/**
    * @access Private
    * @var Object
*/
var $obLabel;

/**
    * @access Public
    * @param String $valor
*/
function setExercicio($valor) { $this->stExercicio = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setValidaExercicio($valor) { $this->boValidaExercicio = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setDataInicial($valor) { $this->obDataInicial   = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setDataFinal($valor) { $this->obDataFinal   = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setLabel($valor) { $this->obLabel   = $valor; }

/**
    * @access Public
    * @return String
*/
function getExercicio() { return $this->stExercicio; }
/**
    * @access Public
    * @return Boolean
*/
function getValidaExercicio() { return $this->boValidaExercicio; }
/**
    * @access Public
    * @return Object
*/
function getDataInicial() { return $this->obDataInicial;   }
/**
    * @access Public
    * @return Object
*/
function getDataFinal() { return $this->obDataFinal;   }
/**
    * @access Public
    * @return Object
*/
function getLabel() { return $this->obLabel;   }

/**
    * Método Construtor
    * @access Public
*/
function Periodo()
{
    parent::Componente();
    $this->setDefinicao                 ( "PERIODO"     );

    if(!$this->getRotulo())
        $this->setRotulo("Período");

    if(!$this->getTiTle())
        $this->setTitle( "Informe o período"  );

    $this->setDataInicial               ( new Data      );
    $this->obDataInicial->setName       ("stDataInicial");
    $this->obDataInicial->setId         ("stDataInicial");
    $this->obDataInicial->setRotulo     ( "Período"     );
    $this->obDataInicial->setDefinicao  ( "DATA"        );

    $this->setLabel                     ( new Label     );
    $this->obLabel->setValue            ( " até "       );

    $this->setDataFinal                 ( new Data      );
    $this->obDataFinal->setName         ("stDataFinal"  );
    $this->obDataFinal->setId           ("stDataFinal"  );
    $this->obDataFinal->setRotulo       ( "Período"     );
    $this->obDataFinal->setDefinicao    ( "DATA"        );

}

/**
    * Monta o HTML do Objeto TextBox
    * @access Protected
*/
function montaHtml()
{
    $this->obDataInicial->setName( $this->obDataInicial->getName() );
    $this->obDataFinal->setName( $this->obDataFinal->getName() );

    $this->obDataInicial->montaHTML();
    $stHtml .= $this->obDataInicial->getHTML();

    $this->obLabel->montaHTML();
    $stHtml .= $this->obLabel->getHTML();

    $this->obDataFinal->montaHTML();
    $stHtml .= $this->obDataFinal->getHTML();

    $this->setHtml($stHtml);
}
}

?>
