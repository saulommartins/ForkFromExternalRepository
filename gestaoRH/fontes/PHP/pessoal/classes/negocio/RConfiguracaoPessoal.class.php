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
* Classe de Regra de Negócio ConfiguracaoPessoal
* Data de Criação   : 03/01/2005

* @author Analista: Leandro Oliveira
* @author Desenvolvedor: Lucas Leusin Oaigen

* @package URBEM
* @subpackage regra

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2008-03-11 12:04:07 -0300 (Ter, 11 Mar 2008) $

* Casos de uso: uc-04.04.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracaoEntidade.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAcao.class.php"         );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalGrupoPeriodo.class.php"         );

class RConfiguracaoPessoal
{
var $stMascaraRegistro;
/**
    * @var String
    * @access Private
*/
var $boGeracaoRegistro;
/**
    * @var String
    * @access Private
*/
var $stMascaraCBO;
/**
    * @var String
    * @access Private
*/
var $stParMascaraRegistro;
/**
    * @var Boolean
    * @access Private
*/
var $boParGeracaoRegistro;
/**
    * @var String
    * @access Private
*/
var $stParGrupoPeriodo;
/**
    * @var String
    * @access Private
*/
var $stExercicio;
/**
    * @var Integer
    * @access Private
*/
var $inCodModulo;
/**
    * @var Integer
    * @access Private
*/
var $inCodEntidade;
/**
    * @var Integer
    * @access Private
*/
var $inGrupoPeriodo;
/**
    * @var String
    * @access Private
*/
var $stContagemInicial;
/**
    * @var String
    * @access Private
*/
var $stParContagemInicial;
/**
    * @var Object
    * @access Private
*/
var $obTAdministracaoConfiguracao;
/**
    * @var Object
    * @access Private
*/
var $obTAdministracaoConfiguracaoEntidade;
/**
    * @var Object
    * @access Private
*/
var $obTPessoalGrupoPeriodo;

/**
    * @var Object
    * @access Private
*/
var $obTransacao;
/**
    * @var Object
    * @access Private
*/
var $obTAcao;
/**
     * @access Public
     * @param String $valor
*/

function setCodEntidade($valor) { $this->inCodEntidade           = $valor; }

function setMascaraRegistro($valor) { $this->stMascaraRegistro        	 = $valor; }
/**
     * @access Public
     * @param Boolean $valor
*/
function setGeracaoRegistro($valor) { $this->boGeracaoRegistro        	 = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setMascaraCBO($valor) { $this->stMascaraCBO        	 	= $valor; }
/**
     * @access Public
     * @param Boolean $valor
*/
function setParMascaraRegistro($valor) { $this->stParMascaraRegistro        	 = $valor; }
/**
     * @access Public
     * @param Boolean $valor
*/
function setParGeracaoRegistro($valor) { $this->boParGeracaoRegistro        	 = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setParMascaraCBO($valor) { $this->stParMascaraCBO        	 = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setParGrupoPeriodo($valor) { $this->stParGrupoPeriodo           = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setParContagemInicial($valor) { $this->stParContagemInicial          = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setContagemInicial($valor) { $this->stContagemInicial             = $valor; }
/**
     * @access Public
     * @param Boolean $valor
*/
function setExercicio($valor) { $this->stExercicio                   = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setGrupoPeriodo($valor) { $this->inGrupoPeriodo                   = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setTAdministracaoConfiguracao($valor) { $this->obTAdministracaoConfiguracao               = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setTAdministracaoConfiguracaoEntidade($valor) { $this->obTAdministracaoConfiguracaoEntidade = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setTPessoalGrupoPeriodo($valor) { $this->obTPessoalGrupoPeriodo               = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setTransacao($valor) { $this->obTransacao                   = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setTAcao($valor) { $this->obTAcao                       = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setCodModulo($valor) { $this->inCodModulo                   = $valor; }
/**
     * @access Public
     * @param String $valor
*/

function getCodEntidade() { return $this->inCodEntidade; }

function getMascaraRegistro() { return $this->stMascaraRegistro;			 }
/**
     * @access Public
     * @param Boolean $valor
*/
function getGeracaoRegistro() { return $this->boGeracaoRegistro;			 	}
/**
     * @access Public
     * @param Object $valor
*/
function getMascaraCBO() { return $this->stMascaraCBO;			 }
/**
     * @access Public
     * @param Boolean $valor
*/
function getParMascaraRegistro() { return $this->stParMascaraRegistro;			 }
/**
     * @access Public
     * @param Boolean $valor
*/
function getParGeracaoRegistro() { return $this->boParGeracaoRegistro;			 	}
/**
     * @access Public
     * @param Object $valor
*/
function getParMascaraCBO() { return $this->stParMascaraCBO;			 }
/**
     * @access Public
     * @param String $valor
*/
function getParGrupoPeriodo() { return $this->stParGrupoPeriodo;           }
/**
     * @access Public
     * @param String $valor
*/
function getParContagemInicial() { return $this->stParContagemInicial;           }
/**
     * @access Public
     * @param String $valor
*/
function getContagemInicial() { return $this->stContagemInicial;           }
/**
     * @access Public
     * @param Boolean $valor
*/
function getExercicio() { return $this->stExercicio;                   }
/**
     * @access Public
     * @param Integer $valor
*/
function getGrupoPeriodo() { return $this->inGrupoPeriodo;                   }
/**
     * @access Public
     * @param Object $valor
*/
function getTAdministracaoConfiguracao() { return $this->obTAdministracaoConfiguracao; }
/**
     * @access Public
     * @param Object $valor
*/
function getTAdministracaoConfiguracaoEntidade() { return $this->obTAdministracaoConfiguracaoEntidade; }
/**
     * @access Public
     * @param Object $valor
*/
function getTPessoalGrupoPeriodo() { return $this->obTPessoalGrupoPeriodo; }
/**
     * @access Public
     * @param Object $valor
*/
function getTransacao() { return $this->obTransacao;                   }
/**
     * @access Public
     * @param Object $valor
*/
function getTAcao() { return $this->obTAcao;                       }
/**
     * @access Public
     * @param Object $valor
*/
function getCodModulo() { return $this->inCodModulo;                   }
/**
     * @access Public
     * @param Object $valor
*/

function RConfiguracaoPessoal()
{
    ;

    $this->setExercicio              	( Sessao::getExercicio()            );
    $this->setTAdministracaoConfiguracao          	( new TAdministracaoConfiguracao             );
    $this->setTAdministracaoConfiguracaoEntidade ( new TAdministracaoConfiguracaoEntidade() );
    $this->setTPessoalGrupoPeriodo          	( new TPessoalGrupoPeriodo             );
    $this->setTransacao              	( new Transacao                 );
    $this->setTAcao                  	( new TAdministracaoAcao        );
    $this->setParMascaraRegistro  		( "mascara_registro".Sessao::getEntidade()   			);
    $this->setParGeracaoRegistro   	 	( "geracao_registro".Sessao::getEntidade()  			);
    $this->setParMascaraCBO 	 		( "mascara_cbo".Sessao::getEntidade()	  			  	);
    $this->setParGrupoPeriodo 	 		( "cod_grupo_periodo".Sessao::getEntidade()    	  	);
    $this->setParContagemInicial        ( "dtContagemInicial".Sessao::getEntidade()    	  	);
    $this->setCodModulo   	 		 	( "22"				  			);    
}

function salvar($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTAdministracaoConfiguracao->setDado( "cod_modulo", $this->getCodModulo() );
        $this->obTAdministracaoConfiguracao->setDado( "exercicio" , $this->getExercicio() );

        $this->obTAdministracaoConfiguracao->setDado( "parametro" , $this->getParMascaraRegistro()        );
        $this->obTAdministracaoConfiguracao->setDado( "valor"     , $this->getMascaraRegistro() );
        $obErro = $this->obTAdministracaoConfiguracao->alteracao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTAdministracaoConfiguracao->setDado( "parametro" , $this->getParGeracaoRegistro()        );
            $this->obTAdministracaoConfiguracao->setDado( "valor"     , $this->getGeracaoRegistro() );
            $obErro = $this->obTAdministracaoConfiguracao->alteracao( $boTransacao );
        }
    if ( !$obErro->ocorreu() ) {
            $this->obTAdministracaoConfiguracao->setDado( "parametro" , $this->getParMascaraCBO()        );
            $this->obTAdministracaoConfiguracao->setDado( "valor"     , $this->getMascaraCBO() );
            $obErro = $this->obTAdministracaoConfiguracao->alteracao( $boTransacao );
    }
        if ( !$obErro->ocorreu() ) {
            $this->obTAdministracaoConfiguracao->setDado( "parametro" , $this->getParGrupoPeriodo() );
            $this->obTAdministracaoConfiguracao->setDado( "valor"     , $this->getGrupoPeriodo() );
            $obErro = $this->obTAdministracaoConfiguracao->alteracao( $boTransacao );
        }
        if ( !$obErro->ocorreu() ) {
            $this->obTAdministracaoConfiguracao->setDado( "parametro" , $this->getParContagemInicial() );
            $this->obTAdministracaoConfiguracao->setDado( "valor"     , $this->getContagemInicial() );
            $obErro = $this->obTAdministracaoConfiguracao->alteracao( $boTransacao );
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );
    }

    return $obErro;
}

function consultarEntidade($boTransacao = "")
{
    $this->obTAdministracaoConfiguracaoEntidade->setDado( "cod_modulo"   , $this->getCodModulo()            );
    $this->obTAdministracaoConfiguracaoEntidade->setDado( "exercicio"    , $this->getExercicio()            );
    $this->obTAdministracaoConfiguracaoEntidade->setDado( "parametro"    , $this->getParMascaraRegistro() );
    $this->obTAdministracaoConfiguracaoEntidade->setDado( "cod_entidade" , Sessao::getCodEntidade($boTransacao) );
    $obErro = $this->obTAdministracaoConfiguracaoEntidade->recuperaPorChave( $rsConfiguracao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->setMascaraRegistro( $rsConfiguracao->getCampo( "valor" ) );
        $this->obTAdministracaoConfiguracaoEntidade->setDado( "parametro" , $this->getParGeracaoRegistro() );
        $obErro = $this->obTAdministracaoConfiguracaoEntidade->recuperaPorChave( $rsConfiguracao, $boTransacao );
        $this->setGeracaoRegistro( $rsConfiguracao->getCampo( "valor" ) );
    }
    if ( !$obErro->ocorreu() ) {
        $this->obTAdministracaoConfiguracaoEntidade->setDado( "parametro" , $this->getParMascaraCBO() );
        $obErro = $this->obTAdministracaoConfiguracaoEntidade->recuperaPorChave( $rsConfiguracao, $boTransacao );
        $this->setMascaraCBO( $rsConfiguracao->getCampo( "valor" ) );
    }
    if ( !$obErro->ocorreu() ) {
        $this->obTAdministracaoConfiguracaoEntidade->setDado( "parametro" , $this->getParGrupoPeriodo() );
        $obErro = $this->obTAdministracaoConfiguracaoEntidade->recuperaPorChave( $rsConfiguracao, $boTransacao );
        $this->setGrupoPeriodo( $rsConfiguracao->getCampo( "valor" ) );
    }
    if ( !$obErro->ocorreu() ) {
        $this->obTAdministracaoConfiguracaoEntidade->setDado( "parametro" , $this->getParContagemInicial() );
        $obErro = $this->obTAdministracaoConfiguracaoEntidade->recuperaPorChave( $rsConfiguracao, $boTransacao );
        $this->setContagemInicial( $rsConfiguracao->getCampo( "valor" ) );
    }

    return $obErro;
}

function consultar($boTransacao = "")
{
    $this->obTAdministracaoConfiguracao->setDado( "cod_modulo"   , $this->getCodModulo()            );
    $this->obTAdministracaoConfiguracao->setDado( "exercicio"    , $this->getExercicio()            );
    $this->obTAdministracaoConfiguracao->setDado( "parametro"    , $this->getParMascaraRegistro() );    
    $obErro = $this->obTAdministracaoConfiguracao->recuperaPorChave( $rsConfiguracao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->setMascaraRegistro( $rsConfiguracao->getCampo( "valor" ) );
        $this->obTAdministracaoConfiguracao->setDado( "parametro" , $this->getParGeracaoRegistro() );
        $obErro = $this->obTAdministracaoConfiguracao->recuperaPorChave( $rsConfiguracao, $boTransacao );
        $this->setGeracaoRegistro( $rsConfiguracao->getCampo( "valor" ) );
    }
    if ( !$obErro->ocorreu() ) {
        $this->obTAdministracaoConfiguracao->setDado( "parametro" , $this->getParMascaraCBO() );
        $obErro = $this->obTAdministracaoConfiguracao->recuperaPorChave( $rsConfiguracao, $boTransacao );
        $this->setMascaraCBO( $rsConfiguracao->getCampo( "valor" ) );
    }
    if ( !$obErro->ocorreu() ) {
        $this->obTAdministracaoConfiguracao->setDado( "parametro" , $this->getParGrupoPeriodo() );
        $obErro = $this->obTAdministracaoConfiguracao->recuperaPorChave( $rsConfiguracao, $boTransacao );
        $this->setGrupoPeriodo( $rsConfiguracao->getCampo( "valor" ) );
    }
    if ( !$obErro->ocorreu() ) {
        $this->obTAdministracaoConfiguracao->setDado( "parametro" , $this->getParContagemInicial() );
        $obErro = $this->obTAdministracaoConfiguracao->recuperaPorChave( $rsConfiguracao, $boTransacao );
        $this->setContagemInicial( $rsConfiguracao->getCampo( "valor" ) );
    }

    return $obErro;
}

function buscaModulo($boTransacao = "")
{
    $stFiltro  = " AND A.cod_acao = ".Sessao::read('acao')." ";
    $obErro = $this->obTAcao->recuperaRelacionamento( $rsRelacionamento, $stFiltro, "", $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->setCodModulo( $rsRelacionamento->getCampo("cod_modulo") );
    }

    return $obErro;
}

function listarGruposPeriodo(&$rsRecordSet, $boTransacao = "")
{
    $obErro = $this->obTPessoalGrupoPeriodo->recuperaTodos($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

}
