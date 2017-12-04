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
* Classe de negócio UnidadeMedida
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3477 $
$Name$
$Author: pablo $
$Date: 2005-12-06 13:51:38 -0200 (Ter, 06 Dez 2005) $

Casos de uso: uc-01.03.97
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_ADM_MAPEAMENTO."TUnidadeMedida.class.php"          );
include_once ( CAM_GA_ADM_NEGOCIO."RGrandeza.class.php"                  );

class RUnidadeMedida
{
/**#@+
    * @var Object
    * @access Private
*/
var $obTUnidadeMedida;
var $obRGrandeza;
/**#@-*/
/**#@+
    * @var String
    * @access Private
*/
var $stNome;
var $stSimbolo;
/**#@-*/
/**
    * @var Integer
    * @access Private
*/
var $inCodUnidade;

/**#@+
     * @access Public
     * @param Object $valor
*/
function setTUnidadeMedida($valor) { $this->obTUnidadeMedida = $valor; }
function setRGrandeza($valor) { $this->obRGrandeza      = $valor; }
/**#@-*/
/**#@+
     * @access Public
     * @param String $valor
*/
function setNome($valor) { $this->stNome       = $valor; }
function setSimbolo($valor) { $this->stSimbolo    = $valor; }
/**#@-*/
/**#@+
     * @access Public
     * @param Integer $valor
*/
function setCodUnidade($valor) { $this->inCodUnidade = $valor; }
/**#@-*/

/**#@+
     * @access Public
     * @return Object
*/
function getTUnidadeMedida() { return $this->obTUnidadeMedida ; }
function getRGrandeza() { return $this->obRGrandeza      ; }
/**#@-*/
/**#@+
     * @access Public
     * @return String
*/
function getNome() { return $this->stNome       ; }
function getSimbolo() { return $this->stSimbolo    ; }
/**#@-*/
/**#@+
     * @access Public
     * @return Integer
*/
function getCodUnidade() { return $this->inCodUnidade ; }
/**#@-*/

/**
    * Método Construtor
    * @access Private
*/
function RUnidadeMedida()
{
    $this->setTUnidadeMedida( new TUnidadeMedida );
    $this->setRGrandeza     ( new RGrandeza      );
}
/**
    * Executa um recuperaTodos na classe Persistente Unidade Medida
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listar(&$rsLista, $stOrder = "", $boTransacao = "")
{
    $stFiltro = "";
    if ($this->stSimbolo) {
        $stFiltro = " LOWER(simbolo) = LOWER('".$this->stSimbolo."') AND ";
    }

    if ($this->obRGrandeza->getCodGrandeza()) {
        $stFiltro .= " cod_grandeza IN (".$this->obRGrandeza->getCodGrandeza().") AND ";
    }
    $stFiltro = ( $stFiltro ) ? " WHERE ".substr($stFiltro,0,strlen($stFiltro)-4):'' ;
    $obErro = $this->obTUnidadeMedida->recuperaTodos( $rsLista, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}
/**
    * Executa um recuperaPorChave na classe Persistente Unidade Medida
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultar(&$rsLista, $boTransacao = "")
{
    $this->obTUnidadeMedida->setDado( "cod_unidade"  , $this->getCodUnidade() );
    $this->obTUnidadeMedida->setDado( "cod_grandeza" , $this->obRGrandeza->getCodGrandeza() );
    $obErro = $this->obTUnidadeMedida->recuperaPorChave( $rsLista, $boTransacao );

    return $obErro;
}
}
