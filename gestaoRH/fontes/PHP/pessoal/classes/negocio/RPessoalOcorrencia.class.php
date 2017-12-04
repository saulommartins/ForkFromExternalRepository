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
    * Classe de regra de negócio para Pessoal-Ocorrencia
    * Data de Criação: 20/12/2004

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Rafael Almeida

    * @package URBEM
    * @subpackage Regra

    Caso de uso: uc-04.04.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalOcorrencia.class.php"   );

/**
    * Classe de regra de negócio para Pessoal-Ocorrencia
    * Data de Criação: 20/12/2004

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Rafael Almeida

    * @package URBEM
    * @subpackage Regra
*/
class RPessoalOcorrencia
{
/**
    * @access Private
    * @var integer
*/
var $inCodOcorrencia;
/**
    * @access Private
    * @var integer
*/
var $inNumOcorrencia;
/**
    * @access Private
    * @var String
*/
var $stDescricao;
/**
    * @access Private
    * @var Object
*/
var $obTPessoalOcorrencia;

/**
    * @access Public
    * @param Integer $valor
*/
function setCodOcorrencia($valor) { $this->inCodOcorrencia = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setNumOcorrencia($valor) { $this->inNumOcorrencia = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setDescricao($valor) { $this->stDescricao = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setTPessoalOcorrencia($valor) { $this->obTPessoalOcorrencia = $valor; }
/**
    * @access Public
    * @return String
*/
function getCodOcorrencia() { return $this->inCodOcorrencia;   }
/**
    * @access Public
    * @return String
*/
function getNumOcorrencia() { return $this->inNumOcorrencia;   }
/**
    * @access Public
    * @return String
*/
function getDescricao() { return $this->stDescricao;   }
/**
    * @access Public
    * @return String
*/
function getTPessoalOcorrencia() { return $this->obTPessoalOcorrencia;   }

/**
    * Método Construtor
*/
function RPessoalOcorrencia()
{
    $this->setTPessoalOcorrencia( new TPessoalOcorrencia );
}

function listar(&$rsRecordSet, $stFiltro="", $stOrdem= "", $boTransacao = "")
{
    $obErro = $this->obTPessoalOcorrencia->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem , $boTransacao);

    return $obErro;

}

function listarOcorrencia(&$rsRecordSet,$boTransacao = "")
{
    $stFiltro = "";
    $stOrdem  = " ocorrencia.descricao ";
    if ( $this->getNumOcorrencia() != "" ) {
        $stFiltro .= " Where num_ocorrencia = ".$this->getNumOcorrencia();
    }
    if ( $this->getCodOcorrencia() != "" ) {
        $stFiltro .= " Where cod_ocorrencia = ".$this->getCodOcorrencia();
    }
    $obErro = $this->listar( $rsRecordSet,$stFiltro,$stOrdem,$boTransacao );

    return $obErro;
}

}
?>
