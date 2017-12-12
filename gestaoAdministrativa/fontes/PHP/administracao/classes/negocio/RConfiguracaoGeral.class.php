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
* Classe de negócio ConfiguracaoGeral
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
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAcao.class.php"          );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php"  );

class RConfiguracaoGeral
{
var $obTAdministracaoConfiguracao;
var $obTAdministracaoAcao;
var $inCodModulo;
var $inExercicio;
var $inCodUF;
var $inCodMunicipio;

//SETTERS
function setTAdministracaoConfiguracao($valor) { $this->obTAdministracaoConfiguracao      = $valor; }
function setTAdministracaoAcao($valor) { $this->obTAdministracaoAcao              = $valor; }
function setCodModulo($valor) { $this->inCodModulo          = $valor; }
function setExercicio($valor) { $this->inExercicio          = $valor; }
function setCodUF($valor) { $this->inCodUF              = $valor; }
function setCodMunicipio($valor) { $this->inCodMunicipio       = $valor; }

//GETTERS
function getTAdministracaoConfiguracao() { return $this->obTAdministracaoConfiguracao;      }
function getTAdministracaoAcao() { return $this->obTAdministracaoAcao;              }
function getCodModulo() { return $this->inCodModulo;          }
function getExercicio() { return $this->inExercicio;          }
function getCodUF() { return $this->inCodUF;              }
function getCodMunicipio() { return $this->inCodMunicipio;       }

//METODO CONSTRUTOR
function RConfiguracaoGeral()
{
    $this->setTAdministracaoConfiguracao ( new TAdministracaoConfiguracao );
    $this->setTAdministracaoAcao         ( new TAdministracaoAcao         );
    $this->setCodModulo     ( 2 );
}

function salvaConfiguracaoGeral($boTransacao = "")
{
    return $obErro;
}

function consultarConfiguracaoGeral(&$rsConfiguracao, $boTransacao = "")
{
    $this->obTAdministracaoConfiguracao->setDado( "cod_modulo", $this->getCodModulo() );
    $obErro = $this->obTAdministracaoConfiguracao->recuperaPorChave( $rsConfiguracao, $boTransacao );
//    if ( !$obErro->ocorreu() ) {
//        $this->setCodUF             ( $rsConfiguracao->getCampo('cod_uf') );
//        $this->setCodMunicipio      ( $rsConfiguracao->getCampo('cod_municipio') );
//    }
    return $obErro;
}

function buscaModulo($boTransacao = "")
{
    $stFiltro  = " AND A.cod_acao = ".Sessao::read('acao')." ";
    $obErro = $this->obTAdministracaoAcao->recuperaRelacionamento( $rsRelacionamento, $stFiltro, "", $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->setCodModulo( $rsRelacionamento->getCampo("cod_modulo") );
    }

    return $obErro;
}

}
