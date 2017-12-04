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
* Classe de regra de negócio para montar o menu do sistema
* Data de Criação: 01/11/2005

* @author Analista: Cassiano de Vasconcellos Ferreira
* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

* @package URBEM
* @subpackage

$Revision: 5772 $
$Name$
$Author: cassiano $
$Date: 2006-01-31 11:32:51 -0200 (Ter, 31 Jan 2006) $

* Casos de uso: uc-01.03.91
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoGestao.class.php" );
include_once( CAM_GA_ADM_NEGOCIO."RAdministracaoGestao.class.php" );

class RAdministracaoMenu
{
///**
//    * @var Object
//    * @access Private
//*/
//var $roRUsuario;
/**
    * @var Object
    * @access Private
*/
var $rsRAdministracaoGestao;

///**
//    * @access Public
//    * @param Object $valor
//*/
//function setRUsuario($valor) { $this->roRUsuario             = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRAdministracaoGestao($valor) { $this->rsRAdministracaoGestao->preenche( $valor ); }

///**
//    * @access Public
//    * @return Object
//*/
//function getRUsuario() { return $this->roRUsuario;             }
/**
    * @access Public
    * @return Object
*/
function getRAdministracaoGestao() { return $this->rsRAdministracaoGestao; }

/**
    * Método Construtor
    * @access Private
    * @param Object &$obRUsuario
*/
function RAdministracaoMenu()
{
    $this->rsRAdministracaoGestao = new RecordSet;
}

function listarGestoes($boTransacao = '')
{
    $obTAdmnistracaoGestao = new TAdministracaoGestao;
    $obErro = $obTAdmnistracaoGestao->listarGestoesPorUsuario( $rsGestao , $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $arGestao = array();
        $obRAdministracaoGestao = new RAdministracaoGestao;
        while ( !$rsGestao->eof() ) {
            $obRAdministracaoGestao = new RAdministracaoGestao;
            $obRAdministracaoGestao->setCodigoGestao ( $rsGestao->getCampo('cod_gestao') );
            $obRAdministracaoGestao->setNomeGestao   ( $rsGestao->getCampo('nom_gestao') );
            $obRAdministracaoGestao->setDiretorio    ( $rsGestao->getCampo('nom_diretorio') );
            $obRAdministracaoGestao->setOrdem        ( $rsGestao->getCampo('ordem') );
            $obRAdministracaoGestao->setVersao       ( $rsGestao->getCampo('versao') );
            $arGestao[] = $obRAdministracaoGestao;
            $rsGestao->proximo();
        }
        $this->setRAdministracaoGestao( $arGestao );
    }

    return $obErro;
}

function listarGestoesPorOrdem($boTransacao = '')
{
    $obTAdmnistracaoGestao = new TAdministracaoGestao;
    $stOrdem = " ORDER BY ordem ";
    $obErro = $obTAdmnistracaoGestao->recuperaTodos( $rsGestao, '', $stOrdem, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $arGestao = array();
        while ( !$rsGestao->eof() ) {
            $obRAdministracaoGestao = new RAdministracaoGestao;
            $obRAdministracaoGestao->setCodigoGestao ( $rsGestao->getCampo('cod_gestao') );
            $obRAdministracaoGestao->setNomeGestao   ( $rsGestao->getCampo('nom_gestao') );
            $obRAdministracaoGestao->setDiretorio    ( $rsGestao->getCampo('nom_diretorio') );
            $obRAdministracaoGestao->setOrdem        ( $rsGestao->getCampo('ordem') );
            $obRAdministracaoGestao->setVersao       ( $rsGestao->getCampo('versao') );
            $arGestao[] = $obRAdministracaoGestao;
            $rsGestao->proximo();
        }
        $this->setRAdministracaoGestao( $arGestao );
    }

    return $obErro;
}

}
?>
