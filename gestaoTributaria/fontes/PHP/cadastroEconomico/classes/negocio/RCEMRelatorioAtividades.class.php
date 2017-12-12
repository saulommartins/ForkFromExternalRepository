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
    * Data de Criação   : 29/04/2005

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Marcelo B. Paulino

    * @package URBEM
    * @subpackage Regra

    * $Id: RCEMRelatorioAtividades.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.15
*/

/*
$Log$
Revision 1.6  2006/09/15 12:13:58  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS."PersistenteRelatorio.class.php" );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMAtividade.class.php"       );

/**
    * Classe de Regra para Relatório de Trechos
    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Marcelo B. Paulino
*/
class RCEMRelatorioAtividades extends PersistenteRelatorio
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
var $inCodTermino;
/**
    * @access Private
    * @var Integer
*/
var $inCodVigencia;
/**
    * @access Private
    * @var String
*/
var $stOrder;
/**
    * @access Private
    * @var String
*/
var $stNomAtividade;
/**
    * @var Object
    * @access Private
*/
var $obTCEMAtividade;
/**
    * @var Object
    * @access Private
*/
var $obRCadastroDinamico;

/**
    * @access Public
    * @param Integer $valor
*/
function setCodInicio($valor) { $this->inCodInicio           = $valor;  }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodVigencia($valor) { $this->inCodVigencia   = $valor;  }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodTermino($valor) { $this->inCodTermino          = $valor;  }
/**
    * @access Public
    * @param String $valor
*/
function setOrder($valor) { $this->stOrder                = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setNomAtividade($valor) { $this->stNomAtividade         = $valor; }

/**
    * @access Public
    * @return Integer
*/
function getCodInicio() { return $this->inCodInicio;          }
/**
    * @access Public
    * @return Integer
*/
function getCodTermino() { return $this->inCodTermino;         }
/**
    * @access Public
    * @return Integer
*/
function getCodVigencia() { return $this->inCodVigencia; }
/**
    * @access Public
    * @return String
*/
function getOrder() { return $this->stOrder;              }
/**
    * @access Public
    * @return String
*/
function getNomAtividade() { return $this->stNomAtividade;       }

/**
    * Método Construtor
    * @access Private
*/
function RCEMRelatorioAtividades()
{
    $this->obTCEMAtividade = new TCEMAtividade;
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
        $stFiltro .= " AND A.cod_estrutural >= '".$this->inCodInicio."'";
    } elseif ( !$this->getCodInicio() AND $this->getCodTermino() ) {
        $stFiltro .= " AND A.cod_estrutural <= '".$this->inCodTermino."'";
    } elseif ( $this->getCodInicio() AND $this->getCodTermino() ) {
        $stFiltro .= " AND A.cod_estrutural between '".$this->inCodInicio."' AND '".$this->inCodTermino."'" ;
    }

    if ( $this->getCodVigencia()) {
        $stFiltro .= " AND eva.cod_vigencia = ".$this->inCodVigencia;
    }

    if ( $this->getNomAtividade() ) {
        $stFiltro .= " AND UPPER ( A.nom_atividade ) like UPPER ( '%".$this->getNomAtividade()."%' )";
    }

    //monta ordem de acordo com os valores indicados na tela de filtro
    switch ($this->stOrder) {
        case 'codigo'     : $stOrder = "A.cod_estrutural"; break;
        case 'descricao'  : $stOrder = "A.nom_atividade"; break;
        default: $stOrder = "A.cod_atividade";
    }

    $stOrder = "\n ORDER BY \n\t".$stOrder;
    $obErro = $this->obTCEMAtividade->recuperaAtividadeRelatorio( $rsRecordSet, $stFiltro, $stOrder );
    $arRecord    = array();
    $inCount     = 0;
    $inFirstLoop = true;

    while ( !$rsRecordSet->eof() ) {
        if ( $inFirstLoop == true OR ( $rsRecordSet->getCampo('cod_atividade') == $inCodAtividadeAnterior ) ) {
            if ($inFirstLoop == true) {
                $arRecord[$inCount]['pagina'        ] = 0;
                $arRecord[$inCount]['cod_estrutural'] = $rsRecordSet->getCampo('cod_estrutural');
                $arRecord[$inCount]['nom_atividade' ] = $rsRecordSet->getCampo('nom_atividade');
                $arRecord[$inCount]['servico'       ] = $rsRecordSet->getCampo('servico');
                $arRecord[$inCount]['dt_inicio'     ] = $rsRecordSet->getCampo('dt_inicio');
                $arRecord[$inCount]['aliquota'      ] = $rsRecordSet->getCampo('aliquota');
                $arRecord[$inCount]['cod_nivel'     ] = $rsRecordSet->getCampo('cod_nivel');
            } else {
                $arRecord[$inCount]['cod_estrutural'] = "";
                $arRecord[$inCount]['nom_atividade' ] = "";
                $arRecord[$inCount]['dt_inicio'     ] = "";
                $arRecord[$inCount]['aliquota'      ] = "";
                $arRecord[$inCount]['cod_nivel'     ] = "";
            }

            $arRecord[$inCount]['servico'       ] = $rsRecordSet->getCampo('servico');
            $arRecord[$inCount]['servico_completo'] = $rsRecordSet->getCampo('servico_completo');
        } else {
            $arRecord[$inCount]['pagina'        ] = 0;
            $arRecord[$inCount]['cod_estrutural'] = $rsRecordSet->getCampo('cod_estrutural');
            $arRecord[$inCount]['nom_atividade' ] = $rsRecordSet->getCampo('nom_atividade');
            $arRecord[$inCount]['servico'       ] = $rsRecordSet->getCampo('servico');
            $arRecord[$inCount]['dt_inicio'     ] = $rsRecordSet->getCampo('dt_inicio');
            $arRecord[$inCount]['aliquota'      ] = $rsRecordSet->getCampo('aliquota');
            $arRecord[$inCount]['cod_nivel'     ] = $rsRecordSet->getCampo('cod_nivel');
            $arRecord[$inCount]['servico_completo'] = $rsRecordSet->getCampo('servico_completo');
        }
        $inCodAtividadeAnterior = $rsRecordSet->getCampo('cod_atividade');
        $inCount++;
        $inFirstLoop = false;
        $rsRecordSet->proximo();
    }
    $rsRecordSet = new RecordSet;
    $rsRecordSet->preenche( $arRecord );

    return $obErro;
}

}
