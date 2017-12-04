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
* Classe de negócio TipoPrimitivo
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3477 $
$Name$
$Author: pablo $
$Date: 2005-12-06 13:51:38 -0200 (Ter, 06 Dez 2005) $

Casos de uso: uc-01.03.95
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoTipoPrimitivo.class.php" );

class RTipoPrimitivo
{
/**
    * @access Private
    * @var Integer
*/
var $inCodTipo;
/**
    * @access Private
    * @var String
*/
var $stNomeTipo;
/**
    * @access Private
    * @var Object
*/
var $obTTipoPrimitivo;
/**
    * @access Private
    * @var Object
*/
var $obTransacao;

/**
    * @access Public
    * @param Integer $Valor
*/
function setCodTipo($valor) { $this->inCodTipo             = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setNomeTipo($valor) { $this->stNomeTipo            = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setTTipoPrimitivo($valor) { $this->obTTipoPrimitivo      = $valor; }

/**
    * @access Public
    * @return Integer
*/
function getCodTipo() { return $this->inCodTipo             ; }
/**
    * @access Public
    * @return String
*/
function getNomeTipo() { return $this->stNomeTipo            ; }
/**
    * @access Public
    * @return Object
*/
function getTTipoPrimitivo() { return $this->obTTipoPrimitivo      ; }

/**
     * Método construtor
     * @access Private
*/
function RTipoPrimitivo()
{
    $this->setTTipoPrimitivo( new TAdministracaoTipoPrimitivo );
    $this->obTransacao = new Transacao;
}

/**
    * Executa um recuperaTodos na classe Persistente de Tipo Primitivo
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listar(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    if( $this->stNomeTipo )
        $stFiltro .= " AND nom_tipo = '".$this->stNomeTipo."' ";
    $stFiltro = ($stFiltro)?" WHERE cod_tipo IS NOT NULL ".$stFiltro:$stFiltro;
    $stOrder = ($stOrder)?$stOrder:" ORDER BY nom_tipo ";
    $obErro = $this->obTTipoPrimitivo->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaPorChave na classe Persistente Tipo Primitivo
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultar($boTransacao = "")
{
    $this->obTTipoPrimitivo->setDado( "cod_tipo" , $this->getCodTipo() );
    $obErro = $this->obTTipoPrimitivo->recuperaPorChave( $rsRecordSet, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->setNomeTipo  ( $rsRecordSet->getCampo("nom_tipo") );
    }

    return $obErro;
}

}
