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
    * Classe de Regra de Negócio Itens
    * Data de Criação   : 29/10/2008

    * @author Analista: Heleno Santos
    * @author Desenvolvedor: Aldo Jean

    * @package URBEM
    * @subpackage Regra
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once(CAM_GA_ADM_NEGOCIO."ROrgao.class.php");
include_once(CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php");
include_once(CAM_GF_ORC_MAPEAMENTO."TOrcamentoOrgao.class.php" );

class ROrcamentoOrgaoOrcamentario
{
var $obTOrcamentoOrgao;
var $stMascara;
var $obRConfiguracaoOrcamento;
var $obROrgao;
var $stExercicio;
var $inNumeroOrgao;

function setRConfiguracaoOrcamento($valor) { $this->obRConfiguracaoOrcamento = $valor; }
function setMascara($valor) { $this->stMascara                               = $valor; }
function setTOrcamentoOrgao($valor) { $this->obTOrcamentoOrgao               = $valor; }
function setROrgao($valor) { $this->obROrgao                                 = $valor; }
function setExercicio($valor) { $this->stExercicio                           = $valor; }
function setNumeroOrgao($valor) { $this->inNumeroOrgao                       = $valor; }
function getMascara() { return $this->stMascara;                                       }
function getRConfiguracaoOrcamento() { return $this->obRConfiguracaoOrcamento;         }
function getTOrcamentoOrgao() { return $this->obTOrcamentoOrgao;                       }
function getROrgao() { return $this->obROrgao;                                         }
function getExercicio() { return $this->stExercicio;                                   }
function getNumeroOrgao() { return $this->inNumeroOrgao;                               }

function ROrcamentoOrgaoOrcamentario()
{
    $this->setRConfiguracaoOrcamento(new ROrcamentoConfiguracao);
    $this->setROrgao(new ROrgao);
    $this->setExercicio(Sessao::getExercicio());
}

function listar(&$rsLista, $stOrder = "", $obTransacao = "")
{
    $obTOrcamentoOrgao = new TOrcamentoOrgao;
    $stFiltro = "";
    if ($this->getNumeroOrgao()) {
        $stFiltro .= " AND OO.num_orgao = ".$this->getNumeroOrgao();
    }if ($this->getExercicio()) {
        $stFiltro .= " AND OO.exercicio = '".$this->getExercicio()."'";
//        $stFiltro .= " AND OO.ano_exercicio = '".$this->getExercicio()."'";
    }if ($this->obROrgao->getNomOrgao()) {
        $stFiltro .= " AND lower(O.nom_orgao)  like lower('%".$this->obROrgao->getNomOrgao()."%')";
    }
    $obErro = $obTOrcamentoOrgao->recuperaRelacionamento($rsLista, $stFiltro, $stOrder, $obTransacao);

    return $obErro;
}

function pegarMascara(&$obTOrcamentoOrgaoParametro)
{
    $obErro    = $this->obRConfiguracaoOrcamento->consultarConfiguracao();
    $stMascara = $this->obRConfiguracaoOrcamento->getMascDespesa();
    $arMarcara = preg_split( "/[^a-zA-Z0-9]/", $stMascara);

    // Grupo O;
    $stMascara = $arMarcara[0];
    $this->setMascara($stMascara);
    $obTOrcamentoOrgaoParametro->setDado("stMascara"  , $this->getMascara());

    return $obErro;
}

}
