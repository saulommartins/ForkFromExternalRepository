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
  * Data de criação : 03/11/2005

    * @author Analista:
    * @author Programador: Fernando Zank Correa Evangelista

    $Revision: 12234 $
    $Name$
    $Author: diego $
    $Date: 2006-07-06 11:08:37 -0300 (Qui, 06 Jul 2006) $

    Caso de uso: uc-03.01.09
**/

/*
$Log$
Revision 1.5  2006/07/06 14:07:04  diego
Retirada tag de log com erro.

Revision 1.4  2006/07/06 12:11:27  diego

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GP_PAT_MAPEAMENTO."TPatrimonioEspecie.class.php"                                       );
include_once ( CAM_GP_PAT_NEGOCIO."RPatrimonioGrupo.class.php"                                       );
include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"                                                 );

class RPatrimonioEspecie
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
function setTPatrimonioEspecie($valor) { $this->obTPatrimonioEspecie = $valor; }

/**
    * @access Public
    * @param Object $Valor
*/
function setCodEspecie($valor) { $this->inCodEspecie = $valor; }

/**
    * @access Public
    * @return Object
*/
function getTPatrimonioEspecie() { return $this->obTPatrimonioEspecie; }

/**
    * @access Public
    * @return Object
*/
function getCodEspecie() { return $this->inCodNatureza; }

//método Construtor
function RPatrimonioEspecie()
{
    $this->obTPatrimonioEspecie           = new TPatrimonioEspecie;
    $this->obRPatrimonioGrupo         = new RPatrimonioGrupo;

}

function listar(&$rsLista, $stOrder = "nom_grupo", $boTransacao = "")
{
    $obErro = new Erro;
    if ($this->obRPatrimonioGrupo->obRPatrimonioNatureza->getCodNatureza()) {
        $stFiltro .= " and n.cod_natureza = ".$this->obRPatrimonioGrupo->obRPatrimonioNatureza->getCodNatureza()."";
    }
    if ($this->obRPatrimonioGrupo->getCodGrupo()) {
        $stFiltro .= " and g.cod_grupo = ".$this->obRPatrimonioGrupo->getCodGrupo()."";
    }
    $obErro = $this->obTPatrimonioEspecie->recuperaEspecie( $rsLista, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

}
