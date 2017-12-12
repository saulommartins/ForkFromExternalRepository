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
* Classe de negócio Grandeza
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3476 $
$Name$
$Author: pablo $
$Date: 2005-12-06 13:51:37 -0200 (Ter, 06 Dez 2005) $

Casos de uso: uc-01.03.97
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_ADM_MAPEAMENTO."TGrandeza.class.php"          );

class RGrandeza
{
/**
    * @var Object
    * @access Private
*/
var $obTGrandeza;
/**
    * @var String
    * @access Private
*/
var $stNome;
/**
    * @var Integer
    * @access Private
*/
var $inCodGrandeza;

/**
     * @access Public
     * @param Object $valor
*/
function setTGrandeza($valor) { $this->obTGrandeza  = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setNome($valor) { $this->stNome       = $valor; }
/**
     * @access Integer
     * @param String $valor
*/
function setCodGrandeza($valor) { $this->inCodGrandeza= $valor; }

/**
     * @access Public
     * @return Object
*/
function getTGrandeza() { return $this->obTGrandeza      ; }
/**
     * @access Public
     * @return String
*/
function getNome() { return $this->stNome           ; }
/**
     * @access Integer
     * @return String
*/
function getCodGrandeza() { return $this->inCodGrandeza    ; }

/**
    * Método Construtor
    * @access Private
*/
function RGrandeza()
{
    $this->setTGrandeza ( new TGrandeza);
}
/**
    * Executa um recuperaTodos na classe Persistente Grandeza
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listar(&$rsLista, $stOrder = "", $boTransacao = "")
{
    $obErro = $this->obTGrandeza->recuperaTodos( $rsLista, '', $stOrder, $boTransacao );

    return $obErro;
}
/**
    * Executa um recuperaPorChave na classe Persistente Grandeza
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultar(&$rsLista, $boTransacao = "")
{
    $this->obTGrandeza->setDado( "cod_grandeza" , $this->getCodGrandeza() );
    $obErro = $this->obTGrandeza->recuperaPorChave( $rsLista, $boTransacao );

    return $obErro;
}
}
