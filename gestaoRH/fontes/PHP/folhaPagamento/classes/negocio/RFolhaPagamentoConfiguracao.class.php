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
    * Classe de Regra de Negócio FolhaPagamentoConfiguracao
    * Data de Criação   : 17/11/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage regra

    $Revision: 30930 $
    $Name$
    $Author: souzadl $
    $Date: 2007-08-06 18:25:26 -0300 (Seg, 06 Ago 2007) $

    * Casos de uso: uc-04.05.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAcao.class.php"         );

class RFolhaPagamentoConfiguracao
{
/**
    * @var String
    * @access Private
*/
var $stMesCalculoDecimo;
/**
    * @var String
    * @access Private
*/
var $stParMesCalculoDecimo;
/**
    * @var String
    * @access Private
*/
var $stImpressao;
/**
    * @var String
    * @access Private
*/
var $stParImpressao;
/**
    * @var String
    * @access Private
*/
var $stImpressora;
/**
    * @var String
    * @access Private
*/
var $stParImpressora;

/**
    * @var String
    * @access Private
*/
var $boApresentaAbaBase;
/**
    * @var String
    * @access Private
*/
var $stParApresentaAbaBase;

/**
    * @var String
    * @access Private
*/
var $stMascaraEvento;
/**
    * @var String
    * @access Private
*/
var $stMensagemAniversariantes;
/**
    * @var String
    * @access Private
*/
var $stParMarcaraEvento;
/**
    * @var Integer
    * @access Private
*/
var $inCodModulo;
/**
    * @var Object
    * @access Private
*/
var $obTAdministracaoConfiguracao;
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
function setMesCalculoDecimo($valor) { $this->stMesCalculoDecimo          	 = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setParMesCalculoDecimo($valor) { $this->stParMesCalculoDecimo        	 = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setImpressao($valor) { $this->stImpressao          	 = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setParImpressao($valor) { $this->stParImpressao        	 = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setImpressora($valor) { $this->stImpressora          	 = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setParImpressora($valor) { $this->stParImpressora        	 = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setApresentaAbaBase($valor) { $this->boApresentaAbaBase          	 = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setParApresentaAbaBase($valor) { $this->stParApresentaAbaBase        	 = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setMascaraEvento($valor) { $this->stMascaraEvento          	 = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setMensagemAniversariantes($valor) { $this->stMensagemAniversariantes 	 = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setParMascaraEvento($valor) { $this->stParMascaraEvento        	 = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setParMensagemAniversariantes($valor) { $this->stParMensagemAniversariantes     	 = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setExercicio($valor) { $this->stExercicio                   = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setTAdministracaoConfiguracao($valor) { $this->obTAdministracaoConfiguracao               = $valor; }
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
     * @param Integer $valor
*/
function setCodModulo($valor) { $this->inCodModulo                   = $valor; }

/**
     * @access Public
     * @param String $valor
*/
function getMesCalculoDecimo() { return $this->stMesCalculoDecimo;	    		 }
/**
     * @access Public
     * @param String $valor
*/
function getParMesCalculoDecimo() { return $this->stParMesCalculoDecimo;			 }
/**
     * @access Public
     * @param String $valor
*/
function getImpressao() { return $this->stImpressao;	    		 }
/**
     * @access Public
     * @param String $valor
*/
function getParImpressao() { return $this->stParImpressao;			 }
/**
     * @access Public
     * @param String $valor
*/
function getImpressora() { return $this->stImpressora;	    		 }
/**
     * @access Public
     * @param String $valor
*/
function getParImpressora() { return $this->stParImpressora;			 }
/**
     * @access Public
     * @param String $valor
*/
function getApresentaAbaBase() { return $this->boApresentaAbaBase;	    		 }
/**
     * @access Public
     * @param String $valor
*/
function getParApresentaAbaBase() { return $this->stParApresentaAbaBase;			 }

/**
     * @access Public
     * @param String $valor
*/
function getMascaraEvento() { return $this->stMascaraEvento;	    		 }
/**
     * @access Public
     * @param String $valor
*/
function getMensagemAniversariantes() { return $this->stMensagemAniversariantes;     }
/**
     * @access Public
     * @param String $valor
*/
function getParMascaraEvento() { return $this->stParMascaraEvento;			 }
/**
     * @access Public
     * @param String $valor
*/
function getParMensagemAniversariantes() { return $this->stParMensagemAniversariantes; 	 }
/**
     * @access Public
     * @param String $valor
*/
function getExercicio() { return $this->stExercicio;                   }
/**
     * @access Public
     * @param Object $valor
*/
function getTAdministracaoConfiguracao() { return $this->obTAdministracaoConfiguracao; }
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
     * @param Integer $valor
*/
function getCodModulo() { return $this->inCodModulo;                   }

function RFolhaPagamentoConfiguracao()
{
    ;
    $this->setExercicio              	( Sessao::getExercicio()            );
    $this->setTAdministracaoConfiguracao( new TAdministracaoConfiguracao);
    $this->setTransacao              	( new Transacao                 );
    $this->setTAcao                  	( new TAdministracaoAcao        );
    $this->setParMascaraEvento  		( "mascara_evento".Sessao::getEntidade()   			);
    $this->setParApresentaAbaBase       ( "apresenta_aba_base".Sessao::getEntidade()          );
    $this->setParMesCalculoDecimo       ( "mes_calculo_decimo".Sessao::getEntidade()          );
    $this->setParMensagemAniversariantes( "aniversariantes".Sessao::getEntidade()             );
    $this->setParImpressao              ( "tipo_impressao_contracheque".Sessao::getEntidade()             );
    $this->setParImpressora             ( "impressora_contracheque".Sessao::getEntidade()             );
    $this->setCodModulo   	 		 	( "27"				  			);
}

function salvar($boTransacao = "")
{
    include_once (CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoEvento.class.php");

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ($this->getMascaraEvento()) {
            if ( !$obErro->ocorreu() ) {
                $this->obTAdministracaoConfiguracao->setDado( "cod_modulo", $this->getCodModulo()       );
                $this->obTAdministracaoConfiguracao->setDado( "exercicio" , $this->getExercicio()       );
                $this->obTAdministracaoConfiguracao->setDado( "parametro" , $this->getParMascaraEvento());
                $this->obTAdministracaoConfiguracao->setDado( "valor"     , $this->getMascaraEvento()   );
                $obErro = $this->obTAdministracaoConfiguracao->alteracao( $boTransacao );
            }
        }
        if ($this->getMensagemAniversariantes()) {
            if ( !$obErro->ocorreu() ) {
                $this->obTAdministracaoConfiguracao->setDado( "cod_modulo", $this->getCodModulo()       );
                $this->obTAdministracaoConfiguracao->setDado( "exercicio" , $this->getExercicio()       );
                $this->obTAdministracaoConfiguracao->setDado( "parametro" , $this->getParMensagemAniversariantes());
                $this->obTAdministracaoConfiguracao->setDado( "valor"     , $this->getMensagemAniversariantes());
                $obErro = $this->obTAdministracaoConfiguracao->alteracao( $boTransacao );
            }
        }
        if ( !$obErro->ocorreu() ) {
            $this->obTAdministracaoConfiguracao->setDado( "cod_modulo", $this->getCodModulo()       );
            $this->obTAdministracaoConfiguracao->setDado( "exercicio" , $this->getExercicio()       );
            $this->obTAdministracaoConfiguracao->setDado( "parametro" , $this->getParApresentaAbaBase());
            $this->obTAdministracaoConfiguracao->setDado( "valor"     , $this->getApresentaAbaBase()   );
            $obErro = $this->obTAdministracaoConfiguracao->alteracao( $boTransacao );
        }
        if ( !$obErro->ocorreu() ) {
            $this->obTAdministracaoConfiguracao->setDado( "cod_modulo", $this->getCodModulo()       );
            $this->obTAdministracaoConfiguracao->setDado( "exercicio" , $this->getExercicio()       );
            $this->obTAdministracaoConfiguracao->setDado( "parametro" , $this->getParMesCalculoDecimo());
            $this->obTAdministracaoConfiguracao->setDado( "valor"     , $this->getMesCalculoDecimo()   );
            $obErro = $this->obTAdministracaoConfiguracao->alteracao( $boTransacao );
        }
        if ( !$obErro->ocorreu() ) {
            $this->obTAdministracaoConfiguracao->setDado( "cod_modulo", $this->getCodModulo()       );
            $this->obTAdministracaoConfiguracao->setDado( "exercicio" , $this->getExercicio()       );
            $this->obTAdministracaoConfiguracao->setDado( "parametro" , $this->getParImpressao());
            $this->obTAdministracaoConfiguracao->setDado( "valor"     , $this->getImpressao()   );
            $obErro = $this->obTAdministracaoConfiguracao->alteracao( $boTransacao );
        }
        if ( !$obErro->ocorreu() ) {
            $this->obTAdministracaoConfiguracao->setDado( "cod_modulo", $this->getCodModulo()       );
            $this->obTAdministracaoConfiguracao->setDado( "exercicio" , $this->getExercicio()       );
            $this->obTAdministracaoConfiguracao->setDado( "parametro" , $this->getParImpressora());
            $this->obTAdministracaoConfiguracao->setDado( "valor"     , $this->getImpressora()   );
            $obErro = $this->obTAdministracaoConfiguracao->alteracao( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTAdministracaoConfiguracao );

    return $obErro;
}

function consultar($boTransacao = "")
{
    //$obErro = $this->buscaModulo( $boTransacao );
    $obErro = new erro;
    if ( !$obErro->ocorreu() ) {
        $this->obTAdministracaoConfiguracao->setDado( "cod_modulo", $this->getCodModulo()            );
        $this->obTAdministracaoConfiguracao->setDado( "exercicio" , $this->getExercicio()            );
        $this->obTAdministracaoConfiguracao->setDado( "parametro" , $this->getParMascaraEvento() );
        $obErro = $this->obTAdministracaoConfiguracao->recuperaPorChave( $rsConfiguracao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->setMascaraEvento( $rsConfiguracao->getCampo( "valor" ) );
        }
    }
    if ( !$obErro->ocorreu() ) {
        $this->obTAdministracaoConfiguracao->setDado( "cod_modulo", $this->getCodModulo()            );
        $this->obTAdministracaoConfiguracao->setDado( "exercicio" , $this->getExercicio()            );
        $this->obTAdministracaoConfiguracao->setDado( "parametro" , $this->getParApresentaAbaBase() );
        $obErro = $this->obTAdministracaoConfiguracao->recuperaPorChave( $rsConfiguracao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->setApresentaAbaBase( $rsConfiguracao->getCampo( "valor" ) );
        }
    }
    if ( !$obErro->ocorreu() ) {
        $this->obTAdministracaoConfiguracao->setDado( "cod_modulo", $this->getCodModulo()            );
        $this->obTAdministracaoConfiguracao->setDado( "exercicio" , $this->getExercicio()            );
        $this->obTAdministracaoConfiguracao->setDado( "parametro" , $this->getParMesCalculoDecimo() );
        $obErro = $this->obTAdministracaoConfiguracao->recuperaPorChave( $rsConfiguracao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->setMesCalculoDecimo( $rsConfiguracao->getCampo( "valor" ) );
        }
    }
    if ( !$obErro->ocorreu() ) {
        $this->obTAdministracaoConfiguracao->setDado( "cod_modulo", $this->getCodModulo()            );
        $this->obTAdministracaoConfiguracao->setDado( "exercicio" , $this->getExercicio()            );
        $this->obTAdministracaoConfiguracao->setDado( "parametro" , $this->getParMensagemAniversariantes() );
        $obErro = $this->obTAdministracaoConfiguracao->recuperaPorChave( $rsConfiguracao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->setMensagemAniversariantes( $rsConfiguracao->getCampo( "valor" ) );
        }
    }
    if ( !$obErro->ocorreu() ) {
        $this->obTAdministracaoConfiguracao->setDado( "cod_modulo", $this->getCodModulo()            );
        $this->obTAdministracaoConfiguracao->setDado( "exercicio" , $this->getExercicio()            );
        $this->obTAdministracaoConfiguracao->setDado( "parametro" , $this->getParImpressao() );
        $obErro = $this->obTAdministracaoConfiguracao->recuperaPorChave( $rsConfiguracao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->setImpressao( $rsConfiguracao->getCampo( "valor" ) );
        }
    }
    if ( !$obErro->ocorreu() ) {
        $this->obTAdministracaoConfiguracao->setDado( "cod_modulo", $this->getCodModulo()            );
        $this->obTAdministracaoConfiguracao->setDado( "exercicio" , $this->getExercicio()            );
        $this->obTAdministracaoConfiguracao->setDado( "parametro" , $this->getParImpressora() );
        $obErro = $this->obTAdministracaoConfiguracao->recuperaPorChave( $rsConfiguracao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->setImpressora( $rsConfiguracao->getCampo( "valor" ) );
        }
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

}
