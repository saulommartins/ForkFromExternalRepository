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
* Classe de negócio Unidade
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
include_once ( CAM_GA_ADM_MAPEAMENTO."TUnidade.class.php"   );
include_once ( CAM_GA_ADM_NEGOCIO."ROrgao.class.php"        	);

class RUnidade
{
var $obOrgao;
var $inCodUnidade;
var $stNomUnidade;
var $inCodResponsavel;
var $chExercicio;
var $obTUnidade;

//SETTERS
function setOrgao($valor) { $this->obOrgao                  = $valor; }
function setCodUnidade($valor) { $this->inCodUnidade             = $valor; }
function setNomUnidade($valor) { $this->stNomUnidade             = $valor; }
function setCodResponsavel($valor) { $this->inCodResponsavel         = $valor; }
function setExercicio($valor) { $this->chExercicio              = $valor; }
function setTUnidade($valor) { $this->obTUnidade               = $valor; }

//GETTERS
function getOrgao() { return $this->obOrgao;                            }
function getCodUnidade() { return $this->inCodUnidade;                       }
function getNomUnidade() { return $this->stNomUnidade;                       }
function getCodResponsavel() { return $this->inCodResponsavel;                   }
function getExercicio() { return $this->chExercicio;                        }
function getTUnidade() { return $this->obTUnidade;                         }

//Método Construtor
function RUnidade()
{
    $this->setTUnidade( new TUnidade );
    $this->setOrgao   ( new ROrgao   );
}

function listar(&$rsUnidade, $stOrder = "", $boTransacao = "")
{
    $stFiltro = " WHERE cod_orgao = ".$this->obOrgao->getCodOrgao(). " AND ano_exercicio = '".$this->obOrgao->getExercicio()."'";
    $obErro = $this->obTUnidade->recuperaTodos( $rsUnidade, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

function consultar(&$rsUnidade, $boTransacao = "")
{
    $this->obTUnidade->setDado( "cod_orgao",   $this->obOrgao->getCodOrgao() );
    $this->obTUnidade->setDado( "cod_unidade", $this->getCodUnidade    () );
    $obErro = $this->obTUnidade->recuperaPorChave( $rsUnidade, $boTransacao );

    return $obErro;
}
}
