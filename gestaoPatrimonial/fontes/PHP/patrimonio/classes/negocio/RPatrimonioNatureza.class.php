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
  * Data de criação : 01/11/2005

    * @author Analista:
    * @author Programador: Fernando Zank Correa Evangelista

    $Revision: 16398 $
    $Name$
    $Author: domluc $
    $Date: 2006-10-04 14:36:38 -0300 (Qua, 04 Out 2006) $

    Caso de uso: uc-03.01.09
    Caso de uso: uc-03.01.21
**/

/*
$Log$
Revision 1.6  2006/10/04 17:36:38  domluc
Add caso de uso

Revision 1.5  2006/07/06 14:07:04  diego
Retirada tag de log com erro.

Revision 1.4  2006/07/06 12:11:27  diego

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GP_PAT_MAPEAMENTO."TPatrimonioNatureza.class.php"                          );
include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"                                                 );

class RPatrimonioNatureza
{
/**
    * @Acces Private
    * @var Object
*/

var $obTPatrimonioNatureza;

/**
    * @Acces Private
    * @var Object
*/

var $inCodNatureza;

/**
    * @access Public
    * @param Object $Valor
*/
function setTPatrimonioNatureza($valor) { $this->obTPatrimonioNatureza = $valor; }

/**
    * @access Public
    * @param Object $Valor
*/
function setCodNatureza($valor) { $this->inCodNatureza = $valor; }

/**
    * @access Public
    * @return Object
*/
function getTPatrimonioNatureza() { return $this->obTPatrimonioNatureza; }

/**
    * @access Public
    * @return Object
*/
function getCodNatureza() { return $this->inCodNatureza; }

function RPatrimonioNatureza()
{
    $this->obTPatrimonioNatureza           = new TPatrimonioNatureza;

}

function listar(&$rsLista, $stOrder = "nom_natureza", $boTransacao = "")
{
    $obErro = new Erro;

    $obErro = $this->obTPatrimonioNatureza->recuperaNatureza( $rsLista, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

}
