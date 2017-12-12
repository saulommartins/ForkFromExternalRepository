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
    * Data de Criação   : 02/05/2005

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Marcelo B. Paulino

    * @package URBEM
    * @subpackage Regra

    * $Id: RCEMRelatorioServicos.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.16
*/

/*
$Log$
Revision 1.5  2006/09/15 12:13:58  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."PersistenteRelatorio.class.php" );

include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMServico.class.php"         );

/**
    * Classe de Regra para Relatório de Trechos
    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Marcelo B. Paulino
*/
class RCEMRelatorioServicos extends PersistenteRelatorio
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
var $inCodInicioAtividade;
/**
    * @access Private
    * @var Integer
*/
var $inCodInicioVigencia;
/**
    * @access Private
    * @var Integer
*/
var $inCodTermino;
/**
    * @access Private
    * @var Integer
*/
var $inCodTerminoAtividade;
/**
    * @access Private
    * @var Integer
*/
var $inCodTerminoVigencia;
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
    * @access Private
    * @var String
*/
var $stNomServico;
/**
    * @var Object
    * @access Private
*/
var $obTCEMServico;
/**
    * @var Object
    * @access Private
*/
var $obRCadastroDinamico;

/**
    * @access Public
    * @param Integer $valor
*/
function setCodInicio($valor) { $this->inCodInicio          = $valor;  }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodInicioAtividade($valor) { $this->inCodInicioAtividade = $valor;  }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodInicioVigencia($valor) { $this->inCodInicioVigencia  = $valor;  }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodTermino($valor) { $this->inCodTermino         = $valor;  }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodTerminoAtividade($valor) { $this->inCodTerminoAtividade = $valor;  }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodTerminoVigencia($valor) { $this->inCodTerminoVigencia  = $valor;  }
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
    * @param String $valor
*/
function setNomServico($valor) { $this->stNomServico           = $valor; }

/**
    * @access Public
    * @return Integer
*/
function getCodInicio() { return $this->inCodInicio;         }
/**
    * @access Public
    * @return Integer
*/
function getCodInicioAtividade() { return $this->inCodInicioAtividade;}
/**
    * @access Public
    * @return Integer
*/
function getCodInicioVigencia() { return $this->inCodInicioVigencia;  }
/**
    * @access Public
    * @return Integer
*/
function getCodTermino() { return $this->inCodTermino;         }
/**
    * @access Public
    * @return Integer
*/
function getCodTerminoVigencia() { return $this->inCodTerminoVigencia; }
/**
    * @access Public
    * @return Integer
*/
function getCodTerminoAtividade() { return $this->inCodTerminoAtividade;}
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
    * @access Public
    * @return String
*/
function getNomServico() { return $this->stNomServico;        }

/**
    * Método Construtor
    * @access Private
*/
function RCEMRelatorioServicos()
{
    $this->obTCEMServico = new TCEMServico;
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRecordSet, $stOrder = "")
{
    $stFiltro = "";
/*
    //monta filtro de acordo com os valores indicados na tela de filtro
    if ( $this->getCodInicio() AND !$this->getCodTermino() ) {
        $stFiltro .= " AND CGM.numcgm >= ".$this->inCodInicio;
    } elseif ( !$this->getCodInicio() AND $this->getCodTermino() ) {
        $stFiltro .= " AND CGM.numcgm <= ".$this->inCodTermino;
    } elseif ( $this->getCodInicio() AND $this->getCodTermino() ) {
        $stFiltro .= " AND CGM.numcgm between ".$this->inCodInicio." AND ".$this->inCodTermino ;
    }

    if ( $this->getCodInicioVigencia() AND !$this->getCodTerminoVigencia() ) {
        $stFiltro .= " AND CE.inscricao_economica >= ".$this->inCodInicioVigencia;
    } elseif ( !$this->getCodInicioVigencia() AND $this->getCodTerminoVigencia() ) {
        $stFiltro .= " AND CE.inscricao_economica <= ".$this->inCodTerminoVigencia;
    } elseif ( $this->getCodInicioVigencia() AND $this->getCodTerminoVigencia() ) {
        $stFiltro .= " AND CE.inscricao_economica between ".$this->inCodInicioVigencia." AND ".$this->inCodTerminoVigencia ;
    }

    if ( $this->getNomAtividade() ) {
        $stFiltro .= " AND UPPER ( CGM.nom_cgm ) like UPPER ( '%".$this->getNomAtividade()."%' )";
    }

    //monta ordem de acordo com os valores indicados na tela de filtro
    switch ($this->stOrder) {
        case 'codigo' : $stOrder = "CGM.numcgm"; break;
        case 'nome'   : $stOrder = "CGM.nom_cgm"; break;
        default: $stOrder = "CGM.nom_cgm";
    }
*/
    $obErro = $this->obTCEMServico->recuperaServicoRelatorio( $rsRecordSet, $stFiltro, $stOrder );

    $arRecord    = array();
    $inCount     = 0;
    $inFirstLoop = true;

    while ( !$rsRecordSet->eof() ) {
        if ( $inFirstLoop == true OR ( $rsRecordSet->getCampo('cod_servico') == $inCodServicoAnterior ) ) {
            if ($inFirstLoop == true) {
                $arRecord[$inCount]['pagina'      ] = 0;
                $arRecord[$inCount]['masc_servico'] = $rsRecordSet->getCampo('masc_servico');
                $arRecord[$inCount]['nom_servico' ] = $rsRecordSet->getCampo('nom_servico');
                $arRecord[$inCount]['atividade'   ] = $rsRecordSet->getCampo('atividade');
                $arRecord[$inCount]['vigencia'    ] = $rsRecordSet->getCampo('vigencia');
                $arRecord[$inCount]['aliquota'    ] = $rsRecordSet->getCampo('aliquota');
                $arRecord[$inCount]['cod_nivel'   ] = $rsRecordSet->getCampo('cod_nivel');
            } else {
                $arRecord[$inCount]['pagina'      ] = 0;
                $arRecord[$inCount]['masc_servico'] = '';
                $arRecord[$inCount]['nom_servico' ] = '';
                $arRecord[$inCount]['vigencia'    ] = '';
                $arRecord[$inCount]['aliquota'    ] = '';
                $arRecord[$inCount]['cod_nivel'   ] = '';
            }
            $arRecord[$inCount]['atividade'  ] = $rsRecordSet->getCampo('atividade');
        } else {
            $arRecord[$inCount]['pagina'      ] = 0;
            $arRecord[$inCount]['masc_servico'] = $rsRecordSet->getCampo('masc_servico');
            $arRecord[$inCount]['nom_servico' ] = $rsRecordSet->getCampo('nom_servico');
            $arRecord[$inCount]['atividade'   ] = $rsRecordSet->getCampo('atividade');
            $arRecord[$inCount]['vigencia'    ] = $rsRecordSet->getCampo('vigencia');
            $arRecord[$inCount]['aliquota'    ] = $rsRecordSet->getCampo('aliquota');
            $arRecord[$inCount]['cod_nivel'   ] = $rsRecordSet->getCampo('cod_nivel');
        }
        $inCodServicoAnterior = $rsRecordSet->getCampo('cod_servico');
        $inCount++;
        $inFirstLoop = false;
        $rsRecordSet->proximo();
    }
    $rsRecordSet = new RecordSet;
    $rsRecordSet->preenche( $arRecord );

    return $obErro;
}

}
