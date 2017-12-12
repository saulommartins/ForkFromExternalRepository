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
    * Classe de Regra de Negócio para Transacao
    * Data de Criação   : 24/01/2006

    * @author Analista: Lucas Leusin Oiagen
    * @author Desenvolvedor: Jose Eduardo Porto

    * @package URBEM
    * @subpackage Regra

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.04.20
*/

/*
$Log$
Revision 1.3  2006/07/05 20:38:41  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS   ."Transacao.class.php"           );
include_once ( CAM_GT_MON_NEGOCIO   ."RMONAgencia.class.php"         );
/**
    * Classe de Regra de Transacao
    * @author Analista: Lucas Leusin Oiagen
    * @author Desenvolvedor: Jose Eduardo Porto
*/
class RTesourariaTransacao
{
/*
    * @var Object
    * @access Private
*/
var $obRMONAgencia;
/*
    * @var Integer
    * @access Private
*/
var $inTipo;
/*
    * @var String
    * @access Private
*/
var $stContaCorrente;

/*
    * @access Public
    * @param Object $valor
*/
function setRMONAgencia($valor) { $this->obRMONAgencia                      = $valor; }
/*
    * @access Public
    * @param Integer $valor
*/
function setTipo($valor) { $this->inTipo                             = $valor; }
/*
    * @access Public
    * @param String $valor
*/
function setContaCorrente($valor) { $this->stContaCorrente                    = $valor; }

/*
    * @access Public
    * @return Object
*/
function getRMONAgencia() { return $this->obRMONAgencia;                      }
/*
    * @access Public
    * @return Integer
*/
function getTipo() { return $this->inTipo;                             }
/*
    * @access Public
    * @return String
*/
function getContaCorrente() { return $this->stContaCorrente;                    }

/**
    * Método Construtor
    * @access Private
*/
function RTesourariaTransacao()
{
    $this->obRMONAgencia         = new RMONAgencia();
}

}
