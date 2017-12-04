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
  * Página de
  * Data de criação : 15/03/2006

    * @author Analista:
    * @author Programador: Fernando Zank Correa Evangelista

    $Revision: 12234 $
    $Name$
    $Author: diego $
    $Date: 2006-07-06 11:08:37 -0300 (Qui, 06 Jul 2006) $

    Caso de uso: uc-03.02.10
**/

/*
$Log$
Revision 1.3  2006/07/06 13:57:42  diego
Retirada tag de log com erro.

Revision 1.2  2006/07/06 12:11:17  diego

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GP_FRO_MAPEAMENTO."TFrotaTipoItem.class.php"                                      );
include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"                                              );

class RFrotaTipoItem
{
/**
    * @Acces Private
    * @var Object
*/
var $obTFrotaTipoItem;

/**
    * @Acces Private
    * @var Object
*/

var $inCodTipo;

/**
    * @access Public
    * @param Object $Valor
*/

function setTFrotaTipoItem($valor) { $this->obTFrotaTipoItem = $valor; }

/**
    * @access Public
    * @param Object $Valor
*/
function setCodTipo($valor) { $this->inCodTipo = $valor; }

/**
    * @access Public
    * @return Object
*/
function getTFrotaTipoItem() { return $this->obTFrotaTipoItem; }
/**
    * @access Public
    * @return Object
*/
function getCodTipo() { return $this->inCodTipo; }

function RFrotaTipoItem()
{
    $this->obTFrotaTipoItem           = new TFrotaTipoItem;

}

function listar(&$rsLista, $stOrder = "nom_marca", $boTransacao = "")
{
    $obErro = new Erro;

    $obErro = $this->obTFrotaTipoItem->recuperaTipoItem( $rsLista, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

}
