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
* Classe de Regra de Negócio ConfiguracaoConcurso
* Data de Criação   : 22/03/2005

* @author Analista : Leandro Oliveira
* @author Desenvolvedor: Rafael Almeida

* @package URBEM
* @subpackage Regra

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2007-06-13 09:55:05 -0300 (Qua, 13 Jun 2007) $

* Casos de uso: uc-04.01.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php"                            );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAcao.class.php"                                    );
include_once ( CAM_GA_NORMAS_NEGOCIO."RTipoNorma.class.php"                                            );

/**
    * Classe de Regra de Negócio ConfiguracaoCEM
    * Data de Criação   : 22/03/2005

    * @author Analista : Leandro Oliveira
    * @author Desenvolvedor: Rafael Almeida

    * @package URBEM
    * @subpackage Regra
*/
class RConfiguracaoConcurso
{
/**
    * @var Boolean
    * @access Private
*/
var $boParPortariaSequencial;
/**
    * @var String
    * @access Private
*/
var $stParMascaraConcurso;
/**
    * @var Integer
    * @access Private
*/
var $inParTipoPortariaEdital;
/**
    * @var Boolean
    * @access Private
*/
var $boPortariaSequencial;
/**
    * @var String
    * @access Private
*/
var $stMascaraConcurso;
/**
    * @var Integer
    * @access Private
*/
var $inTipoPortariaEdital;
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
    * @var Object
    * @access Private
*/
var $obRTipoNorma;
/**
     * @access Public
     * @param String $valor
*/
function setParPortariaSequencial($valor) { $this->boParPortariaSequencial     	 = $valor; }
/**
     * @access Public
     * @param Boolean $valor
*/
function setPortariaSequencial($valor) { $this->boPortariaSequencial        	 = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setParMascaraNota($valor) { $this->stParMascaraNota     		 = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setMascaraNota($valor) { $this->stMascaraNota    			 = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setParTipoPortariaEdital($valor) { $this->inParTipoPortariaEdital	     = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setTipoPortariaEdital($valor) { $this->inTipoPortariaEdital			 = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setCodModulo($valor) { $this->inCodModulo                   = $valor; }
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
     * @param Object $valor
*/
function setRTipoNorma($valor) { $this->obRTipoNorma                  = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function getParMascaraNota() { return $this->stParMascaraNota;                 }
/**
     * @access Public
     * @param String $valor
*/
function getMascaraNota() { return $this->stMascaraNota;                    }
/**
     * @access Public
     * @param String $valor
*/
function getParTipoPortariaEdital() { return $this->inParTipoPortariaEdital;          }
/**
     * @access Public
     * @param String $valor
*/
function getTipoPortariaEdital() { return $this->inTipoPortariaEdital;          }
/**
     * @access Public
     * @param Integer $valor
*/
function getCodModulo() { return $this->inCodModulo;                   }
/**
     * @access Public
     * @param String $valor
*/
function getExercicio() { return $this->stExercicio;                   }
/**
     * @access Public
     * @param Object $valor
*/
function getTAdministracaoConfiguracao() { return $this->obTAdministracaoConfiguracao;               }
/**
     * @access Public
     * @param Object $valor
*/
function getTransacao() { return $this->obTransacao;                   }
/**
     * @access Public
     * @param Object $valor
*/
function getRTipoNorma() { return $this->obRTipoNorma;                  }
/**
     * @access Public
     * @param Object $valor
*/
function getTAcao() { return $this->obTAcao;                       }

function RConfiguracaoConcurso()
{
    $this->setExercicio              	( Sessao::getExercicio()          );
    $this->setTAdministracaoConfiguracao          	( new TAdministracaoConfiguracao           );
    $this->setTransacao              	( new Transacao               );
    $this->setRTipoNorma              	( new RTipoNorma              );
    $this->setTAcao                  	( new TAdministracaoAcao      );
    $this->setParMascaraNota    	 	( "mascara_nota".Sessao::getEntidade()    		  );
    $this->setParTipoPortariaEdital  	( "tipo_portaria_edital".Sessao::getEntidade()	  );
    $this->setCodModulo   	 		 	( "17"				  		  );
}
function alterarConfiguracao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTAdministracaoConfiguracao->setDado( "cod_modulo", $this->getCodModulo()        );
        $this->obTAdministracaoConfiguracao->setDado( "exercicio" , $this->getExercicio()        );
        $this->obTAdministracaoConfiguracao->setDado( "parametro" , $this->getParMascaraNota()   );
        $this->obTAdministracaoConfiguracao->setDado( "valor"     , $this->getMascaraNota()      );
        $obErro = $this->obTAdministracaoConfiguracao->alteracao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTAdministracaoConfiguracao->setDado( "parametro" , $this->getParTipoPortariaEdital()    );
            $this->obTAdministracaoConfiguracao->setDado( "valor"     , $this->getTipoPortariaEdital()       );
            $obErro = $this->obTAdministracaoConfiguracao->alteracao( $boTransacao );
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );
    }

    return $obErro;
}

function consultarConfiguracao($boTransacao = "")
{
    $obErro = $this->buscaModulo( $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTAdministracaoConfiguracao->setDado( "cod_modulo", $this->getCodModulo()            );
        $this->obTAdministracaoConfiguracao->setDado( "exercicio" , $this->getExercicio()            );
        $obErro = $this->obTAdministracaoConfiguracao->recuperaPorChave( $rsConfiguracao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTAdministracaoConfiguracao->setDado( "parametro" , $this->getParMascaraNota() );
            $obErro = $this->obTAdministracaoConfiguracao->recuperaPorChave( $rsConfiguracao, $boTransacao );
            $this->setMascaraNota( $rsConfiguracao->getCampo( "valor" ) );
            if ( !$obErro->ocorreu() ) {
                $this->obTAdministracaoConfiguracao->setDado( "parametro" , $this->getParTipoPortariaEdital() );
                $obErro = $this->obTAdministracaoConfiguracao->recuperaPorChave( $rsConfiguracao, $boTransacao );
                $this->setTipoPortariaEdital( $rsConfiguracao->getCampo( "valor" ) );
            }
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
