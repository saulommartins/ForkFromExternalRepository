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
    * Classe de Regra de Negócio para geração de relatótio
    * Data de Criação   : 29/12/2004

    * @author Fernando Zank Correa Evangelista

    * @ignore

   Caso de uso: uc-03.01.09, uc-03.01.19
*/

/*
$Log$
Revision 1.7  2007/02/08 19:06:16  tonismar
bug #6946

Revision 1.6  2006/07/06 14:07:04  diego
Retirada tag de log com erro.

Revision 1.5  2006/07/06 12:11:27  diego

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GP_PAT_MAPEAMENTO."TPatrimonioAtributoPatrimonio.class.php"                          );
include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"                                                 );

class RPatrimonioAtributoPatrimonio
{
/**
    * @Acces Private
    * @var Object
*/

var $obTPatrimonioAtributoPatrimonio;

/**
    * @access Public
    * @param Object $Valor
*/
function setTPatrimonioAtributoPatrimonio($valor) { $this->obTPatrimonioAtributoPatrimonio = $valor; }

/**
    * @access Public
    * @return Object
*/
function getTPatrimonioAtributoPatrimonio() { return $this->obTPatrimonioAtributoPatrimonio; }

function RPatrimonioAtributoPatrimonio()
{
    $this->obTPatrimonioAtributoPatrimonio           = new TPatrimonioAtributoPatrimonio;

}

function listar(&$rsLista, $stOrder = "nom_atributo", $boTransacao = "")
{
    $obErro = new Erro;

    $obErro = $this->obTPatrimonioAtributoPatrimonio->recuperaNomeAtributo( $rsLista, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

}
