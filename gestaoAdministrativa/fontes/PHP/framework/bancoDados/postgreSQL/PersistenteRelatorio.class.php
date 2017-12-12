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
    * Classe de Regra de Negócio Itens
    * Data de Criação   : 26/07/2004

    * @author Desenvolvedor: Marcelo Boezzio Paulino
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @package URBEM
    * @subpackage Regra

Casos de uso: uc-01.01.00

*/

/**
    * Classe de Regra de Negócio Itens
    * @author Desenvolvedor: Marcelo Boezzio Paulino
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
*/
class PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $rsRelatorio;

/**
     * @access Public
     * @param Object $valor
*/
function setRelatorio($valor) { $this->rsRelatorio     = $valor; }

/**
     * @access Public
     * @return Object
*/
function getRelatorio() { return $this->rsRelatorio;  }

/**
    * Método Construtor
    * @access Private
*/
function PersistenteRelatorio()
{
}

/**
    * Método abstrato
    * @access Public
*/
//function geraRecordSet(&$rsRecordSet , $stOrder = "")
//{
//}
}
