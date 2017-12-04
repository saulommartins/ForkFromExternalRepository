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
    * Classe de Regra para Relatório de Trechos
    * Data de Criação   : 28/04/2005

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Marcelo B. Paulino

    * @package URBEM
    * @subpackage Regra

    * $Id: RCEMRelatorioContadores.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.14
*/

/*
$Log$
Revision 1.6  2006/09/15 12:13:58  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS."PersistenteRelatorio.class.php"     );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMResponsavelTecnico.class.php"  );

/**
    * Classe de Regra para Relatório de Trechos
    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Marcelo B. Paulino
*/
class RCEMRelatorioContadores extends PersistenteRelatorio
{
/**
    * @access Private
    * @var Integer
*/
var $inCodInicio;
/**
    * @access Private
    * @var Integer
*/
var $inCodInicioCadEconomico;
/**
    * @access Private
    * @var Integer
*/
var $inCodTermino;
/**
    * @access Private
    * @var Integer
*/
var $inCodTerminoCadEconomico;
/**
    * @access Private
    * @var String
*/
var $stOrder;
/**
    * @access Private
    * @var String
*/
var $stNomContador;
/**
    * @var Object
    * @access Private
*/
var $obTCEMContadores;
/**
    * @var Object
    * @access Private
*/
var $obRCadastroDinamico;

/**
    * @access Public
    * @param Integer $valor
*/
function setCodInicio($valor) { $this->inCodInicio               = $valor;  }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodInicioCadEconomico($valor) { $this->inCodInicioCadEconomico   = $valor;  }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodTermino($valor) { $this->inCodTermino              = $valor;  }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodTerminoCadEconomico($valor) { $this->inCodTerminoCadEconomico  = $valor;  }
/**
    * @access Public
    * @param String $valor
*/
function setOrder($valor) { $this->stOrder                    = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setNomContador($valor) { $this->stNomContador             = $valor;  }

/**
    * @access Public
    * @return Integer
*/
function getCodInicio() { return $this->inCodInicio;              }
/**
    * @access Public
    * @return Integer
*/
function getCodInicioCadEconomico() { return $this->inCodInicioCadEconomico;  }
/**
    * @access Public
    * @return Integer
*/
function getCodTermino() { return $this->inCodTermino;             }
/**
    * @access Public
    * @return Integer
*/
function getCodTerminoCadEconomico() { return $this->inCodTerminoCadEconomico; }
/**
    * @access Public
    * @return String
*/
function getOrder() { return $this->stOrder;                  }
/**
    * @access Public
    * @return String
*/
function getNomContador() { return $this->stNomContador;            }

/**
    * Método Construtor
    * @access Private
*/
function RCEMRelatorioContadores()
{
    $this->obTCEMResponsavelTecnico = new TCEMResponsavelTecnico;
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRecordSet, $stOrder = "")
{
    $stFiltro = "";

    //monta filtro de acordo com os valores indicados na tela de filtro
    if ( $this->getCodInicio() AND !$this->getCodTermino() ) {
        $stFiltro .= " AND CGM.numcgm >= ".$this->inCodInicio;
    } elseif ( !$this->getCodInicio() AND $this->getCodTermino() ) {
        $stFiltro .= " AND CGM.numcgm <= ".$this->inCodTermino;
    } elseif ( $this->getCodInicio() AND $this->getCodTermino() ) {
        $stFiltro .= " AND CGM.numcgm between ".$this->inCodInicio." AND ".$this->inCodTermino ;
    }

    if ( $this->getCodInicioCadEconomico() AND !$this->getCodTerminoCadEconomico() ) {
        $stFiltro .= " AND CE.inscricao_economica >= ".$this->inCodInicioCadEconomico;
    } elseif ( !$this->getCodInicioCadEconomico() AND $this->getCodTerminoCadEconomico() ) {
        $stFiltro .= " AND CE.inscricao_economica <= ".$this->inCodTerminoCadEconomico;
    } elseif ( $this->getCodInicioCadEconomico() AND $this->getCodTerminoCadEconomico() ) {
        $stFiltro .= " AND CE.inscricao_economica between ".$this->inCodInicioCadEconomico." AND ".$this->inCodTerminoCadEconomico ;
    }

    if ( $this->getNomContador() ) {
        $stFiltro .= " AND UPPER ( CGM.nom_cgm ) like UPPER ( '%".$this->getNomContador()."%' )";
    }

    //monta ordem de acordo com os valores indicados na tela de filtro
    switch ($this->stOrder) {
        case 'codigo' : $stOrder = "CGM.numcgm"; break;
        case 'nome'   : $stOrder = "CGM.nom_cgm"; break;
        default: $stOrder = "CGM.nom_cgm";
    }

    $obErro = $this->obTCEMResponsavelTecnico->recuperaRelacionamentoRelatorio( $rsRecordSet, $stFiltro, $stOrder );

    return $obErro;
}

}
