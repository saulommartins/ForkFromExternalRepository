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
* Classe de negócio Orgao
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 28826 $
$Name$
$Author: rodrigosoares $
$Date: 2008-03-27 16:33:30 -0300 (Qui, 27 Mar 2008) $

Casos de uso: uc-01.03.97
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_ADM_MAPEAMENTO."TOrgao.class.php"        );

class ROrgao
{
var $inCodOrgao;
var $stNomOrgao;
var $inCodResponsavel;
var $chExercicio;
var $obTOrgao;

//SETTERS
function setCodOrgao($valor) { $this->inCodOrgao               = $valor; }
function setNomOrgao($valor) { $this->stNomOrgao               = $valor; }
function setCodResponsavel($valor) { $this->inCodResponsavel         = $valor; }
function setExercicio($valor) { $this->chExercicio              = $valor; }
function setTOrgao($valor) { $this->obTOrgao                 = $valor; }

//GETTERS
function getCodOrgao() { return $this->inCodOrgao;                         }
function getNomOrgao() { return $this->stNomOrgao;                         }
function getCodResponsavel() { return $this->inCodResponsavel;                   }
function getExercicio() { return $this->chExercicio;                        }
function getTOrgao() { return $this->obTOrgao;                           }

//Método Construtor
function ROrgao()
{
    $this->setTOrgao( new TOrgao );
}

function listar(&$rsOrgao, $stOrder = "", $boTransacao = "")
{
    $stFiltro = " WHERE cod_orgao > 0 AND ano_exercicio > '0' ";
    $obErro = $this->obTOrgao->recuperaTodos( $rsOrgao, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

function consultar(&$rsOrgao, $boTransacao = "")
{
    $this->obTOrgao->setDado( "cod_orgao", $this->getCodOrgao() );
    $obErro = $this->obTOrgao->recuperaPorChave( $rsOrgao, $boTransacao );

    return $obErro;
}

function listarTodos(&$rsOrgao, $stOrder = "", $boTransacao = "")
{
    $obErro = $this->obTOrgao->recuperaTodos( $rsOrgao, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}
}
