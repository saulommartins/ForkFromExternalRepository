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
* Classe de negócio Setor
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
include_once ( CAM_GA_ADM_MAPEAMENTO."TSetor.class.php"        );
include_once ( CAM_GA_ADM_NEGOCIO."RDepartamento.class.php"      );

class RSetor extends RDepartamento
{
var $obDepartamento;
var $inCodSetor;
var $stNomSetor;
var $inCodResponsavel;
var $chExercicio;
var $obTSetor;

//SETTERS
function setDepartamento($valor) { $this->obDepartamento           = $valor; }
function setCodSetor($valor) { $this->inCodSetor               = $valor; }
function setNomSetor($valor) { $this->stNomSetor               = $valor; }
function setCodResponsavel($valor) { $this->inCodResponsavel         = $valor; }
function setExercicio($valor) { $this->chExercicio              = $valor; }
function setTSetor($valor) { $this->obTSetor                 = $valor; }

//GETTERS
function getDepartamento() { return $this->obDepartamento;                     }
function getCodSetor() { return $this->inCodSetor;                         }
function getNomSetor() { return $this->stNomSetor;                         }
function getCodResponsavel() { return $this->inCodResponsavel;                   }
function getExercicio() { return $this->chExercicio;                        }
function getTSetor() { return $this->obTSetor;                           }

//Método Construtor
function RSetor()
{
    $this->setTSetor      ( new TSetor        );
    $this->setDepartamento( new RDepartamento );
}

function listar(&$rsSetor, $stOrder = "", $boTransacao = "")
{
    $stFiltro  = " WHERE \n";
    $stFiltro .= "    cod_orgao        = ".$this->obDepartamento->obUnidade->obOrgao->getCodOrgao()."    AND \n";
    $stFiltro .= "    ano_exercicio    = '".$this->obDepartamento->obUnidade->obOrgao->getExercicio()."' AND \n";
    $stFiltro .= "    cod_unidade      = ".$this->obDepartamento->obUnidade->getCodUnidade()."           AND \n";
    $stFiltro .= "    cod_departamento = ".$this->obDepartamento->getCodDepartamento();
    $obErro = $this->obTSetor->recuperaTodos( $rsSetor, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

function consultar(&$rsSetor, $boTransacao = "")
{
    $this->obTSetor->setDado( "cod_orgao",        $this->obDepartamento->obUnidade->obOrgao->getCodOrgao() );
    $this->obTSetor->setDado( "cod_unidade",      $this->obDepartamento->obUnidade->getCodUnidade       () );
    $this->obTSetor->setDado( "cod_departamento", $this->obDepartamento->getCodDepartamento             () );
    $this->obTSetor->setDado( "cod_setor",        $this->getCodSetor                                    () );
    $obErro = $this->obTSetor->recuperaPorChave( $rsSetor, $boTransacao );

    return $obErro;
}
}
