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
* Classe de negócio para profissão
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3476 $
$Name$
$Author: pablo $
$Date: 2005-12-06 13:51:37 -0200 (Ter, 06 Dez 2005) $

Casos de uso: uc-01.07.86
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_CSE_MAPEAMENTO."TConselho.class.php");

/**
    * Classe de Regra de Negócio Conselho
    * Data de Criação   : 27/04/2004
    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
*/

class RConselho
{
/**
    * @access Private
    * @var String
*/
var $stNomeConselho;
/**
    * @access Private
    * @var String
*/

var $stNomeRegistro;
/**
    * @access Private
    * @var Integer
*/

var $inCodigoConselho;
/**
    * @access Private
    * @var Object
*/

var $obTConselho;

/**
    * @access Public
    * @param String $valor
*/
function setNomeConselho($valor) { $this->stNomeConselho   = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setNomeRegistro($valor) { $this->stNomeRegistro   = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoConselho($valor) { $this->inCodigoConselho = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setTConselho($valor) { $this->obTConselho      = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function getNomeConselho() { return $this->stNomeConselho;   }
/**
    * @access Public
    * @param String $valor
*/
function getNomeRegistro() { return $this->stNomeRegistro;   }
/**
    * @access Public
    * @param Integer $valor
*/
function getCodigoConselho() { return $this->inCodigoConselho; }
/**
    * @access Public
    * @param Object $valor
*/
function getTConselho() { return $this->obTConselho;      }
/**
    * @access Public
*/
function RConselho()
{
    $this->setTConselho( new TConselho );
}

/**
    * Metodo de Inclusao de Conselho
    * @access Public
    * @return $obErro boolean
*/
function incluirConselho($boTransacao = "")
{
    $obErro = $this->obTConselho->proximoCod( $inCodConselho, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->setCodigoConselho( $inCodConselho );
        $this->obTConselho->setDado( "cod_conselho", $inCodConselho );
        $this->obTConselho->setDado( "nom_conselho", $this->getNomeConselho() );
        $this->obTConselho->setDado( "nom_registro", $this->getNomeRegistro() );
        $obErro = $this->obTConselho->inclusao( $boTransacao );
    }

    return $obErro;
}
/**
    * Metodo de Alteracao de Conselho
    * @access Public
    * @return $obErro boolean
*/

function alterarConselho($boTransacao = "")
{
    $this->obTConselho->setDado( "cod_conselho", $this->getCodigoConselho() );
    $this->obTConselho->setDado( "nom_conselho", $this->getNomeConselho()   );
    $this->obTConselho->setDado( "nom_registro", $this->getNomeRegistro()   );
    $obErro = $this->obTConselho->alteracao( $boTransacao );

    return $obErro;
}
/**
    * Metodo de Exclusao de Conselho
    * @access Public
    * @return $obErro boolean
*/
function excluirConselho($boTransacao = "")
{
    $this->obTConselho->setDado( "cod_conselho", $this->getCodigoConselho() );
    $obErro = $this->obTConselho->exclusao( $boTransacao );

    return $obErro;
}
/**
    * Metodo para consultar conselho de acordo com o codigo setado
    * @access Public
    * @return $obErro boolean
*/
function consultarConselho($boTransacao = "")
{
    $this->obTConselho->setDado( "cod_conselho", $this->getCodigoConselho() );
    $obErro = $this->obTConselho->recuperaPorChave( $rsListaConselho, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->setNomeConselho( $rsListaConselho->getCampo( "nom_conselho" ) );
        $this->setNomeRegistro( $rsListaConselho->getCampo( "nom_registro" ) );
    }

    return $obErro;
}
/**
    * Retorna recordet preenchido com todos os conselhos cadastrados
    * @access Public
    * @return $obErro boolean
*/
function listarConselho(&$rsListaConselho, $boTransacao = "")
{
    $stOrdem = " ORDER BY nom_conselho ";
    $obErro = $this->obTConselho->recuperaTodos( $rsListaConselho ,"" ,$stOrdem ,$boTransacao );

    return $obErro;
}
}
