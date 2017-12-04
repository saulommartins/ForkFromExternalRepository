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
* Classe de negócio Departamento
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
include_once ( CAM_GA_ADM_MAPEAMENTO."TDepartamento.class.php"      );
include_once ( CAM_GA_ADM_NEGOCIO."RUnidade.class.php"              );

class RDepartamento extends RUnidade
{
var $obUnidade;
var $inCodDepartamento;
var $stNomDepartamento;
var $inCodResponsavel;
var $chExercicio;
var $obTDepartamento;

//SETTERS
function setUnidade($valor) { $this->obUnidade                = $valor; }
function setCodDepartamento($valor) { $this->inCodDepartamento        = $valor; }
function setNomDepartamento($valor) { $this->stNomDepartamento        = $valor; }
function setCodResponsavel($valor) { $this->inCodResponsavel         = $valor; }
function setExercicio($valor) { $this->chExercicio              = $valor; }
function setTDepartamento($valor) { $this->obTDepartamento          = $valor; }

//GETTERS
function getUnidade() { return $this->obUnidade;                          }
function getCodDepartamento() { return $this->inCodDepartamento;                  }
function getNomDepartamento() { return $this->stNomDepartamento;                  }
function getCodResponsavel() { return $this->inCodResponsavel;                   }
function getExercicio() { return $this->chExercicio;                        }
function getTDepartamento() { return $this->obTDepartamento;                    }

//Método Construtor
function RDepartamento()
{
    $this->setTDepartamento( new TDepartamento );
    $this->setUnidade      ( new RUnidade      );
}

function listar(&$rsDepartamento, $stOrder = "", $boTransacao = "")
{
    $stFiltro  = " WHERE \n";
    $stFiltro .= "     cod_orgao = ".$this->obUnidade->obOrgao->getCodOrgao()." AND \n";
    $stFiltro .= "     ano_exercicio = '".$this->obUnidade->obOrgao->getExercicio()."' AND \n";
    $stFiltro .= "     cod_unidade = ".$this->obUnidade->getCodUnidade();
    $obErro = $this->obTDepartamento->recuperaTodos( $rsDepartamento, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

function consultar(&$rsDepartamento, $boTransacao = "")
{
    $this->obTDepartamento->setDado( "cod_orgao",        $this->obUnidade->obOrgao->getCodOrgao() );
    $this->obTDepartamento->setDado( "cod_unidade",      $this->obUnidade->getCodUnidade       () );
    $this->obTDepartamento->setDado( "cod_departamento", $this->getCodDepartamento             () );
    $obErro = $this->obTDepartamento->recuperaPorChave( $rsDepartamento, $boTransacao );

    return $obErro;
}
}
