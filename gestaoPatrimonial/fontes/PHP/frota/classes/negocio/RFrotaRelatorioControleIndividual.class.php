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
  * Página de
  * Data de criação : 13/03/2006

    * @author Analista:
    * @author Programador: Fernando Zank Correa Evangelista

    $Revision: 12234 $
    $Name$
    $Author: diego $
    $Date: 2006-07-06 11:08:37 -0300 (Qui, 06 Jul 2006) $

    Caso de uso: uc-03.02.17
**/

/*
$Log$
Revision 1.7  2006/07/06 13:57:42  diego
Retirada tag de log com erro.

Revision 1.6  2006/07/06 12:11:17  diego

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO                                                           );
include_once( CAM_FW_PDF."RRelatorio.class.php"                                                   );
include_once( CAM_GP_FRO_MAPEAMENTO."TFrotaVeiculo.class.php"                                     );
include_once( CAM_GP_FRO_MAPEAMENTO."TFrotaManutencao.class.php"                                  );

class RFrotaRelatorioControleIndividual extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $inCodVeiculo;
/**
    * @var Object
    * @access Private
*/
var $stPrefixo;
/**
    * @var Object
    * @access Private
*/
var $stPlacaVeiculo;
/**
    * @var Object
    * @access Private
*/
var $stDataInicial;
/**
    * @var Object
    * @access Private
*/
var $stDataFinal;
/**
    * @var Object
    * @access Private
*/
var $stMarca;
/**
    * @var Object
    * @access Private
*/
var $stModelo;
/**
    * @var Object
    * @access Private
*/
var $stTipoVeiculo;
/**
    * @var Object
    * @access Private
*/
var $stTipoCombustivel;
/**
    * @var Object
    * @access Private
*/
var $obTFrotaVeiculo;
/**
    * @var Object
    * @access Private
*/
var $obTFrotaManutencao;

//Setters

/**
     * @access Public
     * @param Object $valor
*/
function setCodVeiculo($valor) { $this->inCodVeiculo      = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function setPrefixo($valor) { $this->stPrefixo      = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function setPlacaVeiculo($valor) { $this->stPlacaVeiculo      = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function setDataInicial($valor) { $this->stDataInicial      = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function setDataFinal($valor) { $this->stDataFinal      = $valor; }

//Getters

/**
     * @access Public
     * @return Object
*/
function getCodVeiculo() { return $this->inCodVeiculo;                }

/**
     * @access Public
     * @return Object
*/
function getPrefixo() { return $this->stPrefixo;                }

/**
     * @access Public
     * @return Object
*/
function getPlacaVeiculo() { return $this->stPlacaVeiculo;                }

/**
     * @access Public
     * @return Object
*/
function getDataInicial() { return $this->stDataInicial;                }

/**
     * @access Public
     * @return Object
*/
function getDataFinal() { return $this->stDataFinal;                }

/**
    * Método Construtor
    * @access Private
*/

function RFrotaRelatorioControleIndividual()
{
    $this->obRRelatorio                 = new RRelatorio;
    $this->obTFrotaVeiculo              = new TFrotaVeiculo;
    $this->obTFrotaManutencao           = new TFrotaManutencao;
}

function listarDadosVeiculo(&$rsLista, $stOrder ="" , $boTransacao = "")
{
    if ($this->inCodVeiculo) {
        $stFiltro = " and v.cod_veiculo = ".$this->inCodVeiculo."";
    }
    if ($this->stPlacaVeiculo) {
        $stFiltro .= " and v.placa = '".$this->stPlacaVeiculo."'";
    }
    if ($this->stPrefixo)
        $stFiltro .= "and v.prefixo = '".$this->stPrefixo."'";
    $this->obTFrotaVeiculo->recuperaControleIndividualVeiculo($rsLista,$stFiltro,$stOrder,$boTransacao);
}

function kmMensal(&$rsLista, $boTransacao)
{
    if ($this->inCodVeiculo)
        $stFiltro = "   m.cod_veiculo = ".$this->inCodVeiculo."";
    if ($this->stDataFinal)
        $stFiltro .= " AND m.dt_manutencao between to_date('".$this->stDataInicial."','dd/mm/yyyy') and to_date('".$this->stDataFinal."','dd/mm/yyyy') ";
    $this->obTFrotaManutencao->recuperaQuilometragemMensal($rsLista,$stFiltro,$stOrder,$boTransacao);
}
function kmAnterior(&$rsLista, $boTransacao)
{
    if ($this->inCodVeiculo)
        $stFiltro = "   m.cod_veiculo = ".$this->inCodVeiculo."";
    if ($this->stDataInicial)
        $stFiltro .= " AND m.dt_manutencao < to_date('".$this->stDataInicial."','dd/mm/yyyy')";
    $this->obTFrotaManutencao->recuperaQuilometragemAnterior($rsLista,$stFiltro,$stOrder,$boTransacao);
}
function listaDadosControleIndividual(&$rsLista, $boTransacao)
{
    if ($this->inCodVeiculo)
        $stFiltro = " AND  m.cod_veiculo = ".$this->inCodVeiculo."";
 if ($this->stDataFinal) {
         $stFiltro .= " AND m.dt_manutencao between to_date('".$this->stDataInicial."','dd/mm/yyyy') and to_date('".$this->stDataFinal."','dd/mm/yyyy')";
 }
    $this->obTFrotaManutencao->recuperaDadosControleIndividual($rsLista,$stFiltro,$stOrder,$boTransacao);
}
function listaValorTotalItemManutencao(&$rsLista, $boTransacao)
{
 $obErro = new Erro;
 if ($this->inCodVeiculo)
        $this->obTFrotaManutencao->setDado("inCodVeiculo", $this->inCodVeiculo);
 if ($this->stDataFinal) {
        $this->obTFrotaManutencao->setDado("stDataInicial", $this->stDataInicial);
        $this->obTFrotaManutencao->setDado("stDataFinal", $this->stDataFinal);
 }
   $obErro = $this->obTFrotaManutencao->recuperaValorTotalItem($rsLista,$stFiltro,$stOrder,$boTransacao);

   return $obErro;
}

}
