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
* Classe de negócios ConfiguracaoAgencia
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
include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"         );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracaoAgencia.class.php" );
include_once ( CAM_GA_ADM_NEGOCIO."RAdministracaoConfiguracaoBanco.class.php"        );

/**
    * Classe de Regra de Agencia
    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo
*/
class RAdministracaoConfiguracaoAgencia
{
/**
    * @access Private
    * @var Object
*/
var $obTAdministracaoConfiguracaoAgencia;
/**
    * @access Private
    * @var Object
*/
var $obRAdministracaoConfiguracaoBanco;
/**
    * @access Private
    * @var Integer
*/
var $stCodAgencia;
/**
    * @access Private
    * @var String
*/
var $stNomAgencia;

/**
    * @access Public
    * @param Object $Valor
*/
function setTAdministracaoConfiguracaoAgencia($valor) { $this->obTAdministracaoConfiguracaoAgencia = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setRAdministracaoConfiguracaoAgencia($valor) { $this->obRAdministracaoConfiguracaoBanco = $valor; }
/**
    * @access Public
    * @param Integer $Valor
*/
function setCodAgencia($valor) { $this->stCodAgencia = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setNomAgencia($valor) { $this->stNomAgencia = $valor; }

/**
    * @access Public
    * @return Object
*/
function getTAdministracaoConfiguracaoAgencia() { return $this->obTAdministracaoConfiguracaoAgencia; }
/**
    * @access Public
    * @return Object
*/
function getRAdministracaoConfiguracaoAgencia() { return $this->obRAdministracaoConfiguracaoBanco; }
/**
    * @access Public
    * @return Integer
*/
function getCodAgencia() { return $this->stCodAgencia; }
/**
    * @access Public
    * @return String
*/
function getNomAgencia() { return $this->stNomAgencia; }

/**
     * Método construtor
     * @access Public
*/
function RAdministracaoConfiguracaoAgencia()
{
    $this->obRAdministracaoConfiguracaoBanco   = new RAdministracaoConfiguracaoBanco;
    $this->obTAdministracaoConfiguracaoAgencia = new TAdministracaoConfiguracaoAgencia;
}

/**
    * Executa um recuperaPorChave na classe Persistente
    * @access Public
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultar($boTransacao = "")
{
    $this->obTAdministracaoConfiguracaoAgencia->setDado( "cod_agencia", $this->stCodAgencia );
    $obErro = $this->obTAdministracaoConfiguracaoAgencia->recuperaPorChave( $rsRecordSet, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->stNomAgencia = $rsRecordSet->getCampo( "nom_agencia" );
        $this->obRAdministracaoConfiguracaoBanco->setCodBanco( $rsRecordSet->getCampo( "cod_banco" ) );
        $obErro = $this->obRAdministracaoConfiguracaoBanco->consultar( $boTransacao );
    }

    return $obErro;
}

/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listar(&$rsRecordSet, $stOrder = "" , $obTransacao = "")
{
    if($this->obRAdministracaoConfiguracaoBanco->getCodBanco())
        $stFiltro .= " cod_banco = '" . $this->obRAdministracaoConfiguracaoBanco->getCodBanco() . "'  AND ";
    if($this->stCodAgencia)
        $stFiltro .= " cod_agencia = '" . $this->stCodAgencia . "'  AND ";
    if($this->stNomAgencia)
        $stFiltro .= " nom_agencia like '" . $this->stNomAgencia . "%' AND ";
    $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
    $obErro = $this->obTAdministracaoConfiguracaoAgencia->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

}
