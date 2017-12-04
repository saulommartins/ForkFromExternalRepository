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
     * Classe de regra de Relatório de Corretagem
     * Data de Criação: 30/03/2005

     * @author Analista: Fábio Bertoldi Rodrigues
     * @author Desenvolvedor: Marcelo B. Paulino

     * @package URBEM
     * @subpackage Regra

    * $Id: RCIMRelatorioCorretagem.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.21
*/

/*
$Log$
Revision 1.6  2006/09/18 09:12:40  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS."PersistenteRelatorio.class.php"  );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMCorretagem.class.php"       );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMCorretagem.class.php"            );

/**
    * Classe de Regra para emissão de relatorio de razão
    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Marcelo B. Paulino
*/
class RCIMRelatorioCorretagem extends PersistenteRelatorio
{
/**
    * @access Private
    * @var Integer
*/
var $inCGMInicio;
/**
    * @access Private
    * @var Integer
*/
var $inCGMTermino;
/**
    * @access Private
    * @var Integer
*/
var $stOrder;
/**
    * @access Private
    * @var Integer
*/
var $stTipoCorretagem;
/**
    * @var Object
    * @access Private
*/
var $obTCorretagem;
/**
    * @var Object
    * @access Private
*/
var $obRCorretagem;

/**
    * @access Public
    * @param String $valor
*/
function setCGMInicio($valor) { $this->inCGMInicio    = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setCGMTermino($valor) { $this->inCGMTermino   = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setOrder($valor) { $this->stOrder        = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setTipoCorretagem($valor) { $this->stTipoCorretagem = $valor; }

/**
    * @access Public
    * @return Integer
*/
function getCGMInicio() { return $this->inCGMInicio;  }
/**
    * @access Public
    * @return Integer
*/
function getCGMTermino() { return $this->inCGMTermino; }
/**
    * @access Public
    * @return Integer
*/
function getOrder() { return $this->stOrder;      }
/**
    * @access Public
    * @return Integer
*/
function getTipoCorretagem() { return $this->stTipoCorretagem; }

/**
    * Método Construtor
    * @access Private
*/
function RCIMRelatorioCorretagem()
{
    $this->obTCIMCorretagem = new TCIMCorretagem;
    $this->obRCIMCorretagem = new RCIMCorretagem;
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRecordSet , $stOrder = "")
{
    $stFiltro = "";
    if ( $this->obRCIMCorretagem->obRCGM->getNomCGM() ) {
        $stFiltro .= " AND UPPER ( CGM.nom_cgm ) like UPPER ( '".$this->obRCIMCorretagem->obRCGM->getNomCGM()."%' )";
    }
    if ( $this->getCGMInicio() AND !$this->getCGMTermino() ) {
        $stFiltro .= " AND CGM.numcgm >= ".$this->inCGMInicio;
    } elseif ( !$this->getCGMInicio() AND $this->getCGMTermino() ) {
        $stFiltro .= " AND CGM.numcgm =< ".$this->inCGMTermino;
    } elseif ( $this->getCGMInicio() AND $this->getCGMTermino() ) {
        $stFiltro .= " AND CGM.numcgm between ".$this->inCGMInicio." AND ".$this->inCGMTermino ;
    }

    if ( $this->getTipoCorretagem() == 'corretor' ) {
        $stFiltro .= " AND IM.numcgm IS NULL ";
    } elseif ( $this->getTipoCorretagem() == 'imobiliaria' ) {
        $stFiltro .= " AND COR.numcgm IS NULL ";
    }

    switch ($this->stOrder) {
        case 'cgm': $stOrder = "CGM.nom_cgm";       break;
        case 'resp': $stOrder = "CGM_RESP.nom_cgm"; break;
        default: $stOrder = "CGM.nom_cgm";
    }

    $obErro = $this->obTCIMCorretagem->recuperaRelacionamentoRelatorio( $rsRecordSet, $stFiltro, $stOrder );

    $arRecord = array();
    $inCount = 0;
    while ( !$rsRecordSet->eof() ) {
        $arRecord[$inCount]['pagina']      = 0;
        if ( $this->getTipoCorretagem() == 'todos' ) {
            $arRecord[$inCount]['tipo'] = $rsRecordSet->getCampo('tipo_corretagem');
        }
        $arRecord[$inCount]['cgm']         = $rsRecordSet->getCampo('numcgm')." - ".$rsRecordSet->getCampo('nom_cgm');
        $arRecord[$inCount]['creci']       = $rsRecordSet->getCampo('creci');
        if ( $rsRecordSet->getCampo('numcgm_resp') ) {
            $arRecord[$inCount]['responsavel'] = $rsRecordSet->getCampo('numcgm_resp')." - ".$rsRecordSet->getCampo('nom_cgm_resp');
        } else {
            $arRecord[$inCount]['responsavel'] = "";
        }
        $inCount++;
        $rsRecordSet->proximo();
    }
    $rsRecordSet = new RecordSet;
    $rsRecordSet->preenche( $arRecord );

    return $obErro;
}

}
