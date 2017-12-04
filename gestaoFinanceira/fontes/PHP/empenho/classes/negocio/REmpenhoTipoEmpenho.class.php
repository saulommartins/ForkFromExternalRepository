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
    * Classe de Regra de Tipo de Empenho
    * Data de Criação   : 02/12/2004

    * @author Analista : Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Regra

    $Id: REmpenhoTipoEmpenho.class.php 59612 2014-09-02 12:00:51Z gelson $

    $Revision: 30668 $
    $Name$
    $Author: cako $
    $Date: 2007-02-16 10:18:29 -0200 (Sex, 16 Fev 2007) $

    * Casos de uso: uc-02.03.03
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
    * Classe de Regra de Tipo de Empenho
    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo
*/
class REmpenhoTipoEmpenho
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
var $stNomTipo;

/**
    * @access Public
    * @param Integer $Valor
*/
function setCodTipo($valor) { $this->inCodTipo = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setNomTipo($valor) { $this->stNomTipo = $valor; }

/**
    * @access Public
    * @return Integer
*/
function getCodTipo() { return $this->inCodTipo; }
/**
    * @access Public
    * @return String
*/
function getNomTipo() { return $this->stNomTipo; }

/**
     * Método construtor
     * @access Public
*/
function REmpenhoTipoEmpenho()
{
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
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoTipoEmpenho.class.php" );
    $obTEmpenhoTipoEmpenho = new TEmpenhoTipoEmpenho;

    $obTEmpenhoTipoEmpenho->setDado( "cod_tipo", $this->inCodTipo );
    $obErro = $obTEmpenhoTipoEmpenho->recuperaPorChave( $rsRecordSet, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->stNomTipo = $rsRecordSet->getCampo( "nom_tipo" );
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
function listar(&$rsRecordSet, $stFiltro = "", $stOrder = "" , $obTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoTipoEmpenho.class.php" );
    $obTEmpenhoTipoEmpenho = new TEmpenhoTipoEmpenho;

    if( $this->inCodTipo )
        $stFiltro .= "AND cod_tipo = ".$this->inCodTipo;
    if( $this->stNomTipo )
        $stFiltro .= "AND lower(nom_tipo) like lower('%".$this->stNomTipo."%')";
    $stFiltro = ($stFiltro) ? " WHERE " .$stFiltro : "";
    $stOrder = ($stOrder) ? $stOrder : "cod_tipo";
    $obErro = $obTEmpenhoTipoEmpenho->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $obTransacao );

    return $obErro;
}

}
