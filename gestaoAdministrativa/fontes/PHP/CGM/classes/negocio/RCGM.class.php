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
* Classe de negócio para tratamento de CGM
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3476 $
$Name$
$Author: pablo $
$Date: 2005-12-06 13:51:37 -0200 (Ter, 06 Dez 2005) $

Casos de uso: uc-01.02.92, uc-01.02.93
*/

//include_once    ( "../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php");
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_CGM_MAPEAMENTO."TCGM.class.php"       );

class RCGM
{
var $inNumCGM;
var $stNomCGM;
var $stTipoPessoa;
var $stTipoBusca;
var $obTCGM;

//SETTERS
function setNumCGM($valor) { $this->inNumCGM     = $valor;              }
function setNomCGM($valor) { $this->stNomCGM     = $valor;              }
function setTipoPessoa($valor) { $this->stTipoPessoa = $valor;              }
function setTipoBusca($valor) { $this->stTipoBusca  = $valor;              }
function setTCGM($valor) { $this->obTCGM       = $valor;              }

//GETTERS
function getNumCGM() { return $this->inNumCGM;                            }
function getNomCGM() { return $this->stNomCGM;                            }
function getTipoPessoa() { return $this->stTipoPessoa;                        }
function getTipoBusca() { return $this->stTipoBusca;                         }
function getTCGM() { return $this->obTCGM;                              }

//METODO CONSTRUTOR
/**
     * Método construtor
     * @access Private
*/
function RCGM()
{
    $this->setTCGM( new TCGM );
}

function listar(&$rsCGM, $stOrder = "", $boTransacao = "")
{
    if ( $this->getNumCGM() ) {
        $stFiltro  = "";
        $stFiltro .= " CGM.numcgm <> 0 AND ";
        $stFiltro .= " CGM.numcgm = ".$this->getNumCGM()." AND ";
    }
    if ( $this->getNomCGM() ) {
        $stFiltro .= " UPPER (CGM.nom_cgm) like UPPER ('%".str_replace("'","\'",$this->getNomCGM())."%') AND ";
    }

    if (isset($_REQUEST["stTipoBusca"])) {
       if ($_REQUEST["stTipoBusca"] == "usuario") {
           $stFiltro .= " CGM.numcgm IN (select numcgm from administracao.usuario where status='A') AND ";
               $stLink .= '&stTipoBusca='.$_REQUEST['stTipoBusca'];
       } else {
           if ( $this->getTipoPessoa()=="F" ) {
               $stFiltro .= " CGM.numcgm IN (select numcgm from sw_cgm_pessoa_fisica ) AND ";
           } else {
               if ( $this->getTipoPessoa()=="J" ) {
                   $stFiltro .= " CGM.numcgm IN (select numcgm from sw_cgm_pessoa_juridica ) AND ";
               }
           }
       }
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".SUBSTR( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    }
    $obErro = $this->obTCGM->recuperaRelacionamento( $rsCGM, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

function consultar(&$rsCGM, $boTransacao = "")
{
    $this->obTCGM->setDado( "numcgm", $this->getNumCGM() );
    $obErro = $this->obTCGM->recuperaPorChave( $rsCGM, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->setNomCGM( $rsCGM->getCampo('nom_cgm') );
    }

    return $obErro;
}

function consultarCGM(&$rsCGM, $boTransacao = "")
{
    return $this->consultar($rsCGM, $boTransacao = "" );
}

function listarFisicoJuridico(&$rsRecordSet, $inCNPJ = '', $inCPF = '', $inRG = '', $boTransacao = '')
{
    $stFiltro .= " AND CGM.numcgm <> 0 ";
    if ($inCNPJ) {
        $stFiltro .= " AND PJ.cnpj = '".$inCNPJ."'";
    }
    if ($inCPF) {
        $stFiltro .= " AND PF.cpf = '".$inCPF."' ";
    }
    if ($inRG) {
        $stFiltro.= " AND PF.rg = '".$inRG."' ";
    }
    if ( $this->getNumCGM() ) {
        $stFiltro .= " AND CGM.numcgm = ".$this->getNumCGM()." ";
    }
    if ( $this->getNomCGM() ) {
        $stFiltro .= " AND UPPER (CGM.nom_cgm) like UPPER ('".str_replace("'","\'",$this->getNomCGM())."%') ";
    }
    $stOrdem = ' ORDER BY CGM.nom_cgm ';
    $obErro = $this->obTCGM->recuperaRelacionamentoSintetico( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

function listarOrgaoGerenciador(&$rsCGM, $stOrder = "", $boTransacao = "")
{
    if ( $this->getNumCGM() ) {
        $stFiltro  = "";
        $stFiltro .= " CGM.numcgm <> 0 AND ";
        $stFiltro .= " CGM.numcgm = ".$this->getNumCGM()." AND ";
    }
    if ( $this->getNomCGM() ) {
        $stFiltro .= " UPPER (CGM.nom_cgm) like UPPER ('%".str_replace("'","\'",$this->getNomCGM())."%') AND ";
    }

    if (isset($_REQUEST["stTipoBusca"])) {
       if ($_REQUEST["stTipoBusca"] == "usuario") {
           $stFiltro .= " CGM.numcgm IN (select numcgm from administracao.usuario where status='A') AND ";
               $stLink .= '&stTipoBusca='.$_REQUEST['stTipoBusca'];
       } else {
           if ( $this->getTipoPessoa()=="F" ) {
               $stFiltro .= " CGM.numcgm IN (select numcgm from sw_cgm_pessoa_fisica ) AND ";
           } else {
               if ( $this->getTipoPessoa()=="J" ) {
                   $stFiltro .= " CGM.numcgm IN (select numcgm from sw_cgm_pessoa_juridica ) AND ";
               }
           }
       }
    }
    if ($stFiltro) {
        $stFiltro = " AND ".SUBSTR( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    }
    $obErro = $this->obTCGM->recuperaOrgaoGerenciador( $rsCGM, $stFiltro, $stOrder, $boTransacao);

    return $obErro;
}


}
