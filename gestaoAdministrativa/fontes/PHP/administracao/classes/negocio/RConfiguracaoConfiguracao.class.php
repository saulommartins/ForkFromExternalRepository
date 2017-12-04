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
* Classe de negócio Configuracao
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3524 $
$Name$
$Author: cassiano $
$Date: 2005-12-07 09:18:33 -0200 (Qua, 07 Dez 2005) $

Casos de uso: uc-01.03.97
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
/**
    * Classe Abstrata de Regra para Configuração.
    * Data de Criação   : 05/12/2004

    * @author Analista : Jorge B. Ribarr
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Regra
*/
class RConfiguracaoConfiguracao
{
/**
    * @var Object
    * @access Private
*/
var $obTConfiguracao;
/**
    * @var Object
    * @access Private
*/
var $obTAcao;
/**
    * @var Object
    * @access Private
*/
var $obTransacao;
/**
    * @var Integer
    * @access Private
*/
var $inCodModulo;
/**
    * @var String
    * @access Private
*/
var $stParametro;
/**
    * @var String
    * @access Private
*/
var $stValor;
/**
    * @var String
    * @access Private
*/
var $stExercicio;

/**
    * @access Public
    * @param Object $valor
*/
function setTConfiguracao($valor) { $this->obTConfiguracao                   = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setTAcao($valor) { $this->obTAcao                           = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setTransacao($valor) { $this->obTransacao                   = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setCodModulo($valor) { $this->inCodModulo                   = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setParametro($valor) { $this->stParametro       	        = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setValor($valor) { $this->stValor           	        = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setExercicio($valor) { $this->stExercicio       	        = $valor; }

/**
    * @access Public
    * @return Object
*/
function getTConfiguracao() { return $this->obTConfiguracao;                 }
/**
    * @access Public
    * @return Object
*/
function getTAcao() { return $this->obTAcao;                    }
/**
     * @access Public
     * @param Object $valor
*/
function getTransacao() { return $this->obTransacao;                  }
/**
     * @access Public
     * @param Integer $valor
*/
function getCodModulo() { return $this->inCodModulo;                  }
/**
     * @access Public
     * @param String $valor
*/
function getParametro() { return $this->stParametro;	       		 	}
/**
     * @access Public
     * @param String $valor
*/
function getValor() { return $this->stValor;             		 	}
/**
     * @access Public
     * @param String $valor
*/
function getExercicio() { return $this->stExercicio;	        		 	}

function RConfiguracaoConfiguracao()
{
    $this->stExercicio      = Sessao::getExercicio();
    $this->inCodModulo      = Sessao::read('modulo');
    include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );
    include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAcao.class.php" );
    include_once( CLA_TRANSACAO );
    $this->obTConfiguracao  = new TAdministracaoConfiguracao;
    $this->obTransacao      = new Transacao;
    $this->obTAcao          = new TAdministracaoAcao;
}

function incluir($boTransacao = "")
{
    $this->obTConfiguracao->setDado("cod_modulo", $this->inCodModulo );
    $this->obTConfiguracao->setDado("exercicio" , $this->stExercicio );
    $this->obTConfiguracao->setDado("parametro" , $this->stParametro );
    $this->obTConfiguracao->setDado("valor"     , $this->stValor );
    $obErro = $this->obTConfiguracao->inclusao( $boTransacao );

    return $obErro;
}

function alterar($boTransacao = "")
{
    $this->obTConfiguracao->setDado("cod_modulo", $this->inCodModulo );
    $this->obTConfiguracao->setDado("exercicio" , $this->stExercicio );
    $this->obTConfiguracao->setDado("parametro" , $this->stParametro );
    $this->obTConfiguracao->setDado("valor"     , $this->stValor );
    $obErro = $this->obTConfiguracao->alteracao( $boTransacao );

    return $obErro;
}

function excluir($boTransacao = "")
{
    $this->obTConfiguracao->setDado("cod_modulo", $this->inCodModulo );
    $this->obTConfiguracao->setDado("exercicio" , $this->stExercicio );
    $this->obTConfiguracao->setDado("parametro" , $this->stParametro );
    $obErro = $this->obTConfiguracao->exclusao( $boTransacao );

    return $obErro;
}

function listar(&$rsRecordSet, $boTransacao = "")
{
    $this->obTConfiguracao->setDado("cod_modulo", $this->inCodModulo );
    if( $this->inCodModulo )
        $stFiltro .= " cod_modulo = ".$this->inCodModulo." AND ";
    if( $this->stExercicio )
        $stFiltro .= " exercicio = '".$this->stExercicio."' AND ";
    if( $this->stParametro )
        $stFiltro .= " parametro = '".$this->stParametro."' AND ";
    if( $this->stValor )
        $stFiltro .= " valor = '".$this->stValor."' AND ";
    $stFiltro = ($stFiltro)?' WHERE '.substr($stFiltro,0,strlen($stFiltro)-4):'';
    $obErro = $this->obTConfiguracao->recuperaTodos( $rsRecordSet, $stFiltro, '', $boTransacao );

    return $obErro;
}

function consultar($boTransacao = "")
{
    $this->obTConfiguracao->setDado("cod_modulo", $this->inCodModulo );
    $this->obTConfiguracao->setDado("exercicio" , $this->stExercicio );
    $this->obTConfiguracao->setDado("parametro" , $this->stParametro );
    $obErro = $this->obTConfiguracao->recuperaPorChave( $rsRecordSet, $boTransacao );
    
    if ( !$obErro->ocorreu() ) {
        $this->stValor = $rsRecordSet->getCampo("valor");
    }

    return $obErro;
}

function verificaParametro(&$boExiste, $boTransacao = "")
{
    $boExiste = false;
    $this->obTConfiguracao->setDado("cod_modulo", $this->inCodModulo );
    $this->obTConfiguracao->setDado("exercicio" , $this->stExercicio );
    $this->obTConfiguracao->setDado("parametro" , $this->stParametro );
    $obErro = $this->obTConfiguracao->recuperaPorChave( $rsRecordSet, $boTransacao );
     if ( !$obErro->ocorreu() && !$rsRecordSet->eof() ) {
        $boExiste = true;
    }

    return $obErro;
}

}
