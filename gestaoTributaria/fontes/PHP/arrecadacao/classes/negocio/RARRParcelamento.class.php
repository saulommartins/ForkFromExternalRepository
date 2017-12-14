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
    * Classe de regra de negócio para Parcelamento
    * Data de Criação: 24/03/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho
    * @package URBEM
    * @subpackage Regra

    * $Id: RARRParcelamento.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.03.20
*/

/*
$Log$
Revision 1.2  2006/09/15 10:48:44  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRParcelamento.class.php"            );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRLancamentoCalculo.class.php"     );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRLancamentoAcrescimo.class.php"   );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRLancamentoDesconto.class.php"    );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRParcela.class.php"               );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRParcelaDesconto.class.php"       );
include_once ( CAM_GT_ARR_MAPEAMENTO."FARRVerificaSuspensao.class.php"     );
include_once ( CAM_GT_ARR_FUNCAO."FFNCalculaDesoneracao.class.php"         );
include_once ( CAM_GT_ARR_NEGOCIO."RARRCalculo.class.php"                  );
include_once ( CAM_GT_ARR_NEGOCIO."RARRSuspensao.class.php"                );
include_once ( CAM_GT_MON_NEGOCIO."RMONCredito.class.php"                  );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMInscricaoEconomica.class.php"       );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMImovel.class.php"                   );
include_once ( CAM_GA_CGM_NEGOCIO."RCGM.class.php"                         );
/**
    * Classe de regra de negócio para lancamento de valores
    * Data de Criação: 01/11/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou
    * @package URBEM
    * @subpackage Regra
*/

class RARRParcelamento
{
/**
    * @access Private
    * @var Integer
*/
var $inCodLancamento;
/**
    * @access Private
    * @var Integer
*/
var $inTotalParcelas;
/**
    * @access Private
    * @var Date
*/
var $dtDataVencimento;
/**
    * @access Private
    * @var Date
*/
var $dtDataVencimentoDesconto;
/**
    * @access Private
    * @var Boolean
*/
var $boAtivo;
/**
    * @access Private
    * @var Boolean
*/
var $boPercentual;
/**
    * @access Private
    * @var String
*/
var $stObservacao;
/**
    * @access Private
    * @var Float
*/
var $flValor;
/**
    * @access Private
    * @var Float
*/
var $flValorDesconto;

// SETTERS
/**
    * @access Public
    * @param Integer $valor
*/
function setCodLancamento($valor) { $this->inCodLancamento           = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setTotalParcelas($valor) { $this->inTotalParcelas           = $valor; }
/**
    * @access Public
    * @param Date $valor
*/
function setDataVencimento($valor) { $this->dtDataVencimento          = $valor; }
/**
    * @access Public
    * @param Date $valor
*/
function setDataVencimentoDesconto($valor) { $this->dtDataVencimentoDesconto  = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setAtivo($valor) { $this->boAtivo                   = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setPercentual($valor) { $this->boPercentual              = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setObservacao($valor) { $this->stObservacao              = $valor; }
/**
    * @access Public
    * @param Float $valor
*/
function setValor($valor) { $this->flValor                   = $valor; }
/**
    * @access Public
    * @param Float $valor
*/
function setValorDesconto($valor) { $this->flValorDesconto           = $valor; }

// GETTERES
/**
    * @access Public
    * @return Integer
*/
function getCodLancamento() { return $this->inCodLancamento;          }
/**
    * @access Public
    * @return Integer
*/
function getTotalParcelas() { return $this->inTotalParcelas;          }
/**
    * @access Public
    * @return Date
*/
function getDataVencimento() { return $this->dtDataVencimento;         }
/**
    * @access Public
    * @return Date
*/
function getDataVencimentoDesconto() { return $this->dtDataVencimentoDesconto; }
/**
    * @access Public
    * @return Boolean
*/
function getAtivo() { return $this->boAtivo;                  }
/**
    * @access Public
    * @return Boolean
*/
function getPercentual() { return $this->boPercentual;             }
/**
    * @access Public
    * @return String
*/
function getObservacao() { return $this->stObservacao;             }
/**
    * @access Public
    * @return Float
*/
function getValor() { return $this->flValor;                  }
/**
    * @access Public
    * @return Float
*/
function getValorDesconto() { return $this->flValorDesconto;          }

/**
     * Método construtor
     * @access Private
*/
function RARRParcelamento(&$obRARRCalculo)
{
    //mapeamento
    //$this->obTARRLancamento          = new TARRLancamento;
    $this->obTARRParcelamento          = new TARRParcelamento;
    $this->obTARRLancamentoCalculo   = new TARRLancamentoCalculo;
    $this->obTARRLancamentoAcrescimo = new TARRLancamentoAcrescimo;
    $this->obTARRAcrescimoCalculo    = new TARRAcrescimoCalculo;
    $this->obTARRLancamentoDesconto  = new TARRLancamentoDesconto;
    $this->obTARRParcela             = new TARRParcela;
    $this->obTARRParcelaDesconto     = new TARRParcelaDesconto;
    //funcoes
    $this->obFFNCalculaDesoneracao   = new FFNCalculaDesoneracao;
    $this->obFARRVerificaSuspensao   = new FARRVerificaSuspensao;
    //regras
    $this->roRARRCalculo             = &$obRARRCalculo;
    $this->obRARRSuspensao           = new RARRSuspensao;
    $this->obRCEMInscricaoEconomica  = new RCEMInscricaoEconomica;
    $this->obRCIMImovel              = new RCIMImovel( new RCIMLote);
    $this->obRCgm                    = new RCgm;
    //transacao
    $this->obTransacao               = new Transacao;
}

/**
    * Listar Lançamentos para Parcelamento
    * @access Public
    * @param  Object RecordSet
    * @param  Object Transação
    * @return Object  Erro
*/
function listarParcelamentoConsulta(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->obRCIMImovel->getNumeroInscricao() ) {
        $stFiltro .= " and ic.inscricao_municipal = ".$this->obRCIMImovel->getNumeroInscricao()." \n";
    }
    if ( $this->obRCEMInscricaoEconomica->getInscricaoEconomica() ) {
        $stFiltro .= " and cec.inscricao_economica = ".$this->obRCEMInscricaoEconomica->getInscricaoEconomica()." \n";
    }
    if ( $this->obRCgm->getNumCgm() ) {
        $stFiltro .= " and cgm.numcgm = ".$this->obRCgm->getNumCgm()." \n";
    }
    if ($_REQUEST['inCodGrupo']) {
        $stFiltro .= " and grupo.cod_grupo = ".$_REQUEST['inCodGrupo']." \n";
    }
    if ($_REQUEST['inCodCredito']) {
        $arValores = explode('.',$_REQUEST["inCodCredito"]);
        // array [0]> cod_credito [1]> cod_especie [2]> cod_genero [3]> cod_natureza
        $stFiltro .= " and c.cod_credito = ".$arValores[0]. " \n";
        $stFiltro .= " and c.cod_especie = ".$arValores[1]. " \n";
        $stFiltro .= " and c.cod_genero = ".$arValores[2]. " \n";
        $stFiltro .= " and c.cod_natureza = ".$arValores[3]. " \n";
        /*$obRARRGrupo->obRMONCredito->setCodEspecie  ($arValores[1]);
        $obRARRGrupo->obRMONCredito->setCodGenero   ($arValores[2]);
        $obRARRGrupo->obRMONCredito->setCodNatureza ($arValores[3]);*/
    }

    $stOrdem = " ORDER BY numcgm,inscricao, dados_complementares";
    $stOrdem = "";

   $obErro = $this->obTARRParcelamento->recuperaListaConsulta( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

   return $obErro;
}

} // end of class
?>
