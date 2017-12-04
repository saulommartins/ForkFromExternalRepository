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
* Classe de negócio ConfiguracaoBanco
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
include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"       );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracaoBanco.class.php" );

/**
    * Classe de Regra de Banco
    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo
*/
class RAdministracaoConfiguracaoBanco
{
/**
    * @access Private
    * @var Object
*/
var $obTAdministracaoConfiguracaoBanco;
/**
    * @access Private
    * @var String
*/
var $stCodBanco;
/**
    * @access Private
    * @var String
*/
var $stNomBanco;

/**
    * @access Public
    * @param Object $Valor
*/
function setTAdministracaoConfiguracaoBanco($valor) { $this->obTAdministracaoConfiguracaoBanco = $valor; }
/**
    * @access Public
    * @param Integer $Valor
*/
function setCodBanco($valor) { $this->stCodBanco = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setNomBanco($valor) { $this->stNomBanco = $valor; }

/**
    * @access Public
    * @return Object
*/
function getTAdministracaoConfiguracaoBanco() { return $this->obTAdministracaoConfiguracaoBanco; }
/**
    * @access Public
    * @return Integer
*/
function getCodBanco() { return $this->stCodBanco; }
/**
    * @access Public
    * @return String
*/
function getNomBanco() { return $this->stNomBanco; }

/**
     * Método construtor
     * @access Public
*/
function RAdministracaoConfiguracaoBanco()
{
    $this->obTAdministracaoConfiguracaoBanco = new TAdministracaoConfiguracaoBanco;
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
    $this->obTAdministracaoConfiguracaoBanco->setDado( "cod_banco", $this->stCodBanco );
    $obErro = $this->obTAdministracaoConfiguracaoBanco->recuperaPorChave( $rsRecordSet, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->stNomBanco = $rsRecordSet->getCampo( "nom_banco" );
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
    if($this->stCodBanco)
        $stFiltro  = " cod_banco = "  . $this->stCodBanco . "  AND ";
    if($this->stNomBanco)
        $stFiltro .= " nom_banco like '" . $this->stNomBanco . "%' AND ";
    $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
    $obErro = $this->obTAdministracaoConfiguracaoBanco->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

}
