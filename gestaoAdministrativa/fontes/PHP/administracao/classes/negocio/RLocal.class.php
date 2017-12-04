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
* @author Desenvolvedor: Fernando Zank Correa Evangelista

$Revision: 9202 $
$Name$
$Author: fernando $
$Date: 2006-05-03 11:46:13 -0300 (Qua, 03 Mai 2006) $

Casos de uso: uc-01.03.97
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_ADM_MAPEAMENTO."TLocal.class.php"        );
include_once ( CAM_GA_ADM_NEGOCIO."RSetor.class.php"      );

class RLocal extends RSetor
{
var $obSetor;
var $inCodLocal;
var $stNomLocal;
var $chExercicio;
var $obTLocal;

//SETTERS
function setSetor($valor) { $this->obSetor                  = $valor; }
function setCodLocal($valor) { $this->inCodLocal               = $valor; }
function setNomLocal($valor) { $this->stNomLocal               = $valor; }
function setExercicio($valor) { $this->chExercicio              = $valor; }
function setTLocal($valor) { $this->obTLocal                 = $valor; }

//GETTERS
function getSetor() { return $this->obSetor;                        }
function getCodLocal() { return $this->inCodLocal;                     }
function getNomLocal() { return $this->stNomLocal;                     }
function getExercicio() { return $this->chExercicio;                    }
function getTLocal() { return $this->obTLocal;                       }

//Método Construtor
function RLocal()
{
    $this->setTLocal     ( new Tlocal        );
    $this->setSetor      ( new RSetor );
}

function listar(&$rsSetor, $stOrder = "", $boTransacao = "")
{
    $stFiltro  = " WHERE \n";
    $stFiltro .= "    cod_orgao        = ".$this->obSetor->obDepartamento->obUnidade->obOrgao->getCodOrgao()."    AND \n";
    $stFiltro .= "    ano_exercicio    = '".$this->obSetor->obDepartamento->obUnidade->obOrgao->getExercicio()."' AND \n";
    $stFiltro .= "    cod_unidade      = ".$this->obSetor->obDepartamento->obUnidade->getCodUnidade()."           AND \n";
    $stFiltro .= "    cod_departamento = ".$this->obSetor->obDepartamento->getCodDepartamento()."                 AND \n";
    $stFiltro .= "    cod_setor =      ".$this->obSetor->getCodSetor();
    $obErro = $this->obTLocal->recuperaTodos( $rsSetor, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

}
