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
* Classe de regra de negócio para montar o menu de gestões
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
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoGestao.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoModulo.class.php" );

class RAdministracaoGestao
{
/**
    * @var Integer
    * @access Private
*/
var $inCodigoGestao;
/**
    * @var String
    * @access Private
*/
var $stNomeGestao;
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
    * @var String
    * @access Private
*/
var $stVersao;
/**
    * @var Object
    * @access Private
*/
var $rsRModulo;

/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoGestao($valor) { $this->inCodigoGestao = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setNomeGestao($valor) { $this->stNomeGestao   = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setDiretorio($valor) { $this->stDiretorio    = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setOrdem($valor) { $this->inOrdem        = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setVersao($valor) { $this->stVersao       = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRModulo($valor) { $this->rsRModulo      = $valor; }

/**
    * @access Public
    * @return Integer
*/
function getCodigoGestao() { return $this->inCodigoGestao; }
/**
    * @access Public
    * @return String
*/
function getNomeGestao() { return $this->stNomeGestao;   }
/**
    * @access Public
    * @return String
*/
function getDiretorio() { return $this->stDiretorio;    }
/**
    * @access Public
    * @return Integer
*/
function getOrdem() { return $this->inOrdem;        }
/**
    * @access Public
    * @return String
*/
function getVersao() { return $this->stVersao;       }
/**
    * @access Public
    * @return Object
*/
function getRModulo() { return $this->rsRModulo;      }

/**
    * Método Construtor
    * @access Private
*/
function RAdministracaoGestao()
{
   $this->setRModulo( new RecordSet );
}

function consultarGestao($boTransacao = '')
{
    $obTAdministracaoGestao = new TAdministracaoGestao;
    $obTAdministracaoGestao->setDado( "cod_gestao", $this->getCodigoGestao() );
    $obErro = $obTAdministracaoGestao->recuperaPorChave( $rsGestao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->setCodigoGestao ( $rsGestao->getCampo('cod_gestao')    );
        $this->setNomeGestao   ( $rsGestao->getCampo('nom_gestao')    );
        $this->setDiretorio    ( $rsGestao->getCampo('nom_diretorio') );
        $this->setOrdem        ( $rsGestao->getCampo('ordem')         );
        $this->setVersao       ( $rsGestao->getCampo('versao')        );
    }

    return $obErro;
}

function listarModulos($boTransacao = '')
{
    $obTAdministracaoModulo = new TModulo;
    $stFiltro = " WHERE cod_gestao = ".$this->getCodigoGestao();
    $stOdem   = " ORDER BY nom_modulo ";
    $obErro = $obTAdministracaoModulo->recuperaTodos( $rsModulo, $stFiltro, $stOdem, $boTransacao );
    $arModulo = array();
    if ( !$obErro->ocorreu() ) {
        while ( !$rsModulo->eof() ) {
            include_once(CAM_GA_ADM_NEGOCIO."RModulo.class.php");
            $obRModulo = new RModulo();
            $obRModulo->setCodModulo( $rsModulo->getCampo( 'cod_modulo' ) );
            $obErro = $obRModulo->consultar( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $arModulo[] = $obRModulo;
            }
            $rsModulo->proximo();
        }
    }
    $this->rsRModulo->preenche( $arModulo );

    return $obErro;
}

}
?>
