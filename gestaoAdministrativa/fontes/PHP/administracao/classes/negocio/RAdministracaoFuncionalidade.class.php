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
* Classe de regra de negócio para Funcionalidade
* Data de Criação: 01/11/2005

* @author Analista: Cassiano de Vasconcellos Ferreira
* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

* @package URBEM
* @subpackage

$Revision: 6707 $
$Name$
$Author: domluc $
$Date: 2006-03-02 17:20:11 -0300 (Qui, 02 Mar 2006) $

* Casos de uso: uc-01.03.91
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoFuncionalidade.class.php" );

class RAdministracaoFuncionalidade
{
/**
    * @var String
    * @access Private
*/
var $stNomeFuncionalidade;
/**
    * @var String
    * @access Private
*/
var $stCodigoFuncionalidade;
/**
    * @var String
    * @access Private
*/
var $stDiretorio;
/**
    * @var Integer
    * @access Private
*/
var $inOrdem;
/**
    * @var Object
    * @access Private
*/
var $rsAdmnistracaoAcao;
/**
    * @var Object
    * @access Private
*/
var $roAdminstracaoModulo;

/**
    * @access Public
    * @param String $valor
*/
function setNomeFuncionalidade($valor) { $this->stNomeFuncionalidade  = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoFuncionalidade($valor) { $this->inCodigoFuncinalidade = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setDiretorio($valor) { $this->stDiretorio           = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setOrdem($valor) { $this->inOrdem               = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setAdministracaoAcao($valor) { $this->rsAdministracaoAcao   = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setAdministracaoModulo($valor) { $this->roAdministracaoModulo = $valor; }

/**
    * @access Public
    * @return String
*/
function getNomeFuncionalidade() { return $this->stNomeFuncionalidade;  }
/**
    * @access Public
    * @return Integer
*/
function getCodigoFuncionalidade() { return $this->inCodigoFuncinalidade; }
/**
    * @access Public
    * @return String
*/
function getDiretorio() { return $this->stDiretorio;           }
/**
    * @access Public
    * @return Integer
*/
function getOrdem() { return $this->inOrdem;               }
/**
    * @access Public
    * @return Object
*/
function getAdministracaoAcao() { return $this->rsAdministracaoAcao;   }
/**
    * @access Public
    * @return Object
*/
function getAdministracaoModulo() { return $this->roAdministracaoModulo; }

/**
    * Método Construtor
    * @access Private
*/
function RadministracaoFuncionalidade(&$roAdministracaoModulo)
{
    $this->setAdministracaoAcao( new RecordSet );
    $this->roAdministracaoModulo = &$roAdministracaoModulo;
}

function listarAcoesPorFuncionalidade($boTransacao = '')
{
    $obTAdministracaoAcao = new TAdministracaoAcao;
    $stFiltro = " WHERE cod_funcionalidade = ".$this->getCodigoFuncionalidade();
    $stOrdem = " ORDER BY ordem ";
    $boErro = $obTAdministracaoAcao->recuperaTodos( $rsAcao, $stFiltro, $stOrdem, $boTransacao );
    if ( !$boErro->ocorreu() ) {
       // $arAcao = new array();
        $arAcao = array();
        while ( !$rsAcao->eof() ) {
            $obRAdministracaoAcao = new RAdministracaoAcao();
            $obRAdministracaoAcao->setCodigoAcao( $rsAcao->getCampo( 'cod_acao' ) );
            $obErro = $obRAdministracaoAcao->consultar( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $arAcao[]['obAcao'] = $obRAdministracaoAcao;
            } else {
                break;
            }
        }
        $this->rsAdministracaoAcao->preenche( $arAcao );
    }

    return $obErro;
}

function listarFuncionalidades(&$rsFuncionalidades, $boTransacao = '')
{
    $obTAdministarcaoFuncionalidade = new TAdministracaoFuncionalidade;
    $stFiltro = " WHERE cod_modulo=". $this->roAdministracaoModulo->getCodModulo() ;
    $stOrdem = " ORDER BY nom_funcionalidade";
    $obErro = $obTAdministarcaoFuncionalidade->recuperaTodos( $rsFuncionalidades,$stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

function consultarFuncionalidade($boTransacao = '')
{
    $obTAdministarcaoFuncionalidade = new TAdministarcaoFuncionalidade;
    $obTAdministarcaoFuncionalidade->setDado( 'cod_funcionallidade', $this->getCodigoFuncionalidade() );
    $obErro = $obTAdministarcaoFuncionalidade->recuperaPorChave( $rsFuncionalidade, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->setNomeFuncionalidade   ( $rsFuncionalidade->getCampo('nom_funcionalidade') );
        $this->setDiretorio            ( $rsFuncionalidade->getCampo('nom_diretorio') );
        $this->setOrdem                ( $rsFuncionalidade->getCampo('ordem') );
    }

    return $obErro;
}

}
?>
