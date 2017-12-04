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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GP_PAT_MAPEAMENTO."TPatrimonioBem.class.php"                          );
include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"                                                 );

class RPatrimonioBem
{
/**
    * @Acces Private
    * @var Object
*/

var $obTPatrimonioBem;

/**
    * @access Public
    * @param Object $Valor
*/
function setTPatrimonioBem($valor) { $this->obTPatrimonioBem = $valor; }

/**
    * @access Public
    * @return Object
*/
function getTPatrimonioBem() { return $this->obTPatrimonioBem; }

function RPatrimonioBem()
{
    $this->obTPatrimonioBem           = new TPatrimonioBem;

}

function listarMax(&$rsLista, $stOrder = "", $boTransacao = "")
{
    $obErro = new Erro;

    $obErro = $this->obTPatrimonioBem->recuperaMax( $rsLista, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

function listarTodasSituacoes(&$rsLista, $boTransacao = "")
{
    $obErro = new Erro;
    $obConexao = new Conexao;
    $stOrder = " order by nom_situacao";
    $stSql = " select * from patrimonio.situacao_bem".$stOrder;
    $obErro = $obConexao->executaSql( $rsLista, $stSql, $boTransacao);

    return $obErro;
}

}
