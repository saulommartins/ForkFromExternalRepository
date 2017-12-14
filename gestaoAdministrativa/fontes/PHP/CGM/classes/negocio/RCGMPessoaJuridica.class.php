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
* Classe de negócio para tratamento de CGM Pessoa Jurídica
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 23721 $
$Name$
$Author: bruce $
$Date: 2007-07-03 18:35:23 -0300 (Ter, 03 Jul 2007) $

Casos de uso: uc-01.02.92, uc-01.02.93
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_CGM_MAPEAMENTO."TCGMPessoaJuridica.class.php" );
include_once ( CAM_GA_CGM_NEGOCIO."RCGM.class.php"       				);

class RCGMPessoaJuridica extends RCGM
{
var $obTCGMPessoaJuridica;
var $stNomCGM;
var $stCNPJ;
var $stInscricaoEstadual;
var $stNomFantasia;

//SETTERS
function setTCGMPessoaJuridica($valor) { $this->obTCGMPessoaJuridica = $valor;  }
function setNomCGM($valor) { $this->stNomCGM             = $valor;  }
function setCNPJ($valor) { $this->stCNPJ               = $valor;  }
function setInscricaoEstadual($valor) { $this->stInscricaoEstadual  = $valor;  }
function setNomFantasia($valor) { $this->stNomFantasia        = $valor;  }

//GETTERS
function getTCGMPessoaJuridica() { return $this->obTCGMPessoaJuridica; }
function getNomCGM() { return $this->stNomCGM;             }
function getCNPJ() { return $this->stCNPJ;               }
function getInscricaoEstadual() { return $this->stInscricaoEstadual;  }
function getNomFantasia() { return $this->stNomFantasia;        }

//METODO CONSTRUTOR
/**
     * Método construtor
     * @access Private
*/
function RCGMPessoaJuridica()
{
    parent::RCGM();
    $this->setTCGMPessoaJuridica( new TCGMPessoaJuridica );
}

function listarCGM(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    $obErro = $this->obTCGMPessoaJuridica->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

function consultarCGM(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = '';
    if ( count( $this->getNumCGM() ) ) {
        $stFiltro .= " AND CGM.numcgm = ".$this->getNumCGM();
    }
    $obErro = $this->obTCGMPessoaJuridica->recuperaRelacionamento( $rsRecordSet, $stFiltro, '', $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->setNumCGM            ( $rsRecordSet->getCampo('numcgm')       );
        $this->setNomCGM            ( $rsRecordSet->getCampo('nom_cgm')       );
        $this->setCNPJ              ( $rsRecordSet->getCampo('cpf')           );
        $this->setInscricaoEstadual ( $rsRecordSet->getCampo('insc_estadual') );
        $this->setNomFantasia       ( $rsRecordSet->getCampo('nom_fantasia')  );
    }

    return $obErro;
}

}
?>
