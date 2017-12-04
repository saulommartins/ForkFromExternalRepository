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
    * Classe de Regra de Plano Conta Geral
    * Data de Criação   : 08/10/2012

    * @author Analista: Tonismar
    * @author Desenvolvedor: Eduardo

    * @package URBEM
    * @subpackage Regra
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_FW_BANCO_DADOS.'Transacao.class.php';
include_once CAM_GA_ADM_NEGOCIO.'RAdministracaoUF.class.php';
include_once CAM_GF_CONT_MAPEAMENTO.'TContabilidadePlanoContaGeral.class.php';

/**
    * Classe de Regra de Plano Conta Geral
*/
class RContabilidadePlanoContaGeral
{
/**
    * @access Private
    * @var Object
*/
var $obRAdministracaoUF;
/**
    * @access Private
    * @var Object
*/
var $obTransacao;
/**
    * @access Private
    * @var Integer
*/
var $inCodPlano;
/**
    *@access Private
    *@var Char
*/
var $stVersao;
/**
    *@access Private
    *@var Date
*/
var $dtVersao;

/**
    * @access Public
    * @param Object $Valor
*/
function setRAdministracaoUF($valor) { $this->obRAdministracaoUF = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setTransacao($valor) { $this->obTransacao = $valor; }
/**
    * @access Public
    * @param Integer $Valor
*/
function setCodPlano($valor) { $this->inCodPlano = $valor; }
/**
    * @access public
    * @param String $valor
*/
function setVersao($valor) { $this->stVersao = $valor; }
/**
    * @access public
    * @param Date $valor
*/
function setDtVersao($valor) { $this->dtVersao = $valor; }

/**
    * @access Public
    * @return Object
*/
function getRAdministracaoUF() { return $this->obRAdministracaoUF; }
/**
    * @access Public
    * @return Object
*/
function getTransacao() { return $this->obTransacao; }
/**
    * @access Public
    * @return Integer
*/
function getCodPlano() { return $this->inCodPlano; }
/**
    * @access public
    * @return String
*/
function getVersao() { return $this->stVersao; }
/**
    * @access public
    * @return Date
*/
function getDtVersao() { return $this->dtVersao; }

/**
     * Método construtor
     * @access Public
*/
function RContabilidadePlanoContaGeral()
{
    $this->obRAdministracaoUF = new RUF;
    $this->obTransacao        = new Transacao;
}

/**
    * Executa um recuperaUFs na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarUFs(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
{
    $obTContabilidadePlanoContaGeral = new TContabilidadePlanoContaGeral;

    if($this->inCodPlano != "")
        $stFiltro .= " cod_plano = " . $this->inCodPlano . "  AND ";
    if($this->obRAdministracaoUF->getCodigoUF() != "")
        $stFiltro .= " plano_conta_geral.cod_uf = " . $this->obRAdministracaoUF->getCodigoUF() . "  AND ";
    if($this->stVersao)
        $stFiltro .= " versao = '" . $this->stVersao . "' AND ";
    if($this->dtVersao)
        $stFiltro .= " dt_versao = '" . $this->dtVersao . "' AND ";

    $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
    $obErro = $obTContabilidadePlanoContaGeral->recuperaUFs( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaVersoes na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarVersoes(&$rsRecordSet, $stOrder = '' , $boTransacao = '')
{
    $obTContabilidadePlanoContaGeral = new TContabilidadePlanoContaGeral;
    $stFiltro = '';

    if($this->inCodPlano != "")
        $stFiltro .= " cod_plano = " . $this->inCodPlano . "  AND ";
    if($this->obRAdministracaoUF->getCodigoUF() != "")
        $stFiltro .= " plano_conta_geral.cod_uf = " . $this->obRAdministracaoUF->getCodigoUF() . "  AND ";
    if($this->stVersao)
        $stFiltro .= " versao = '" . $this->stVersao . "' AND ";
    if($this->dtVersao)
        $stFiltro .= " dt_versao = '" . $this->dtVersao . "' AND ";

    $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
    $obErro = $obTContabilidadePlanoContaGeral->recuperaVersoes($rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}
}
