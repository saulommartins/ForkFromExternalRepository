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
    * Classe de regra de negócio para RFolhaPagamentoFolhaComplementar
    * Data de Criação: 13/01/2006

    * @author Analista: Vandrã Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Regra de Negócio

    $Id: RFolhaPagamentoFolhaComplementar.class.php 66307 2016-08-05 20:20:10Z michel $

    * Casos de uso: uc-04.05.31
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoRegistroEventoComplementar.class.php";
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoCalculoFolhaComplementar.class.php";

class RFolhaPagamentoFolhaComplementar
{
/**
   * @access Private
   * @var Object
*/
var $obTransacao;
/**
   * @access Private
   * @var Integer
*/
var $inCodComplementar;
/**
   * @access Private
   * @var String
*/
var $stTimestamp;
/**
   * @access Private
   * @var String
*/
var $stSituacao;
/**
   * @access Private
   * @var Object
*/
var $roRFolhaPagamentoPeriodoMovimentacao;
/**
   * @access Private
   * @var Object
*/
var $roRFolhaPagamentoRegistroEventoComplementar;
/**
   * @access Private
   * @var Array
*/
var $arRFolhaPagamentoRegistroEventoComplementar;
/**
   * @access Private
   * @var Object
*/
var $obRFolhaPagamentoCalculoFolhaComplementar;

/**
    * @access Public
    * @param Object $valor
*/
function setTransacao($valor) { $this->obTransacao                           = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodComplementar($valor) { $this->inCodComplementar                     = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setTimestamp($valor) { $this->stTimestamp                           = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setSituacao($valor) { $this->stSituacao                            = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRORFolhaPagamentoPeriodoMovimentacao(&$valor) { $this->roRFolhaPagamentoPeriodoMovimentacao = &$valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRORFolhaPagamentoRegistroEventoComplementar(&$valor) { $this->roRFolhaPagamentoRegistroEventoComplementar = &$valor; }
/**
    * @access Public
    * @param Array $valor
*/
function setARRFolhaPagamentoRegistroEventoComplementar($valor) { $this->arRFolhaPagamentoRegistroEventoComplementar = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRFolhaPagamentoCalculoFolhaComplementar(&$valor) { $this->obRFolhaPagamentoCalculoFolhaComplementar = &$valor; }

/**
    * @access Public
    * @return Object
*/
function getTransacao() { return $this->obTransacao;                           }
/**
    * @access Public
    * @return Integer
*/
function getCodComplementar() { return $this->inCodComplementar;                     }
/**
    * @access Public
    * @return Integer
*/
function getTimestamp() { return $this->stTimestamp;                           }
/**
    * @access Public
    * @return String
*/
function getSituacao() { return $this->stSituacao;                            }
/**
    * @access Public
    * @return Object
*/
function getRORFolhaPagamentoPeriodoMovimentacao() { return $this->roRFolhaPagamentoPeriodoMovimentacao;  }
/**
    * @access Public
    * @return Object
*/
function getRORFolhaPagamentoRegistroEventoComplementar() { return $this->roRFolhaPagamentoRegistroEventoComplementar;  }
/**
    * @access Public
    * @return Array
*/
function getARRFolhaPagamentoRegistroEventoComplementar() { return $this->arRFolhaPagamentoRegistroEventoComplementar;  }
/**
    * @access Public
    * @return Object
*/
function getRFolhaPagamentoCalculoFolhaComplementar() { return $this->obRFolhaPagamentoCalculoFolhaComplementar;  }

/**
     * Método construtor
     * @access Private
*/
function RFolhaPagamentoFolhaComplementar(&$obRFolhaPagamentoPeriodoMovimentacao)
{
    $this->setTransacao                                ( new Transacao                                      );
    $this->setRORFolhaPagamentoPeriodoMovimentacao     ( $obRFolhaPagamentoPeriodoMovimentacao              );
    $this->setRFolhaPagamentoCalculoFolhaComplementar  ( new RFolhaPagamentoCalculoFolhaComplementar($this) );
    $this->obTransacao = new Transacao();
}

/**
    * incluirRegistroComplementarPorContrato
    * @access Public
*/
function incluirRegistroComplementarPorContrato($boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoContratoServidorComplementar.class.php" );
    $obTFolhaPagamentoContratoServidorComplementar  = new TFolhaPagamentoContratoServidorComplementar;
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if (!$obErro->ocorreu()) {
        $obErro = $this->listarRegistroComplementarPorContrato($rsRegistroComplementar,$boTransacao);
        if (!$obErro->ocorreu() and $rsRegistroComplementar->getNumLinhas() < 0) {
            $obTFolhaPagamentoContratoServidorComplementar->setDado("cod_periodo_movimentacao", $this->roRFolhaPagamentoPeriodoMovimentacao->getCodPeriodoMovimentacao()    );
            $obTFolhaPagamentoContratoServidorComplementar->setDado("cod_complementar",         $this->getCodComplementar() );
            $obTFolhaPagamentoContratoServidorComplementar->setDado("cod_contrato",             $this->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->getCodContrato());
            $obErro = $obTFolhaPagamentoContratoServidorComplementar->inclusao($boTransacao);
        }
        if (!$obErro->ocorreu()) {
            for ($inIndex=0;$inIndex<count($this->arRFolhaPagamentoRegistroEventoComplementar);$inIndex++) {
                $obRFolhaPagamentoRegistroEventoComplementar = $this->arRFolhaPagamentoRegistroEventoComplementar[$inIndex];
                if (!$obErro->ocorreu()) {
                    $obErro = $obRFolhaPagamentoRegistroEventoComplementar->incluirRegistroEventoComplementar($boTransacao);
                }
                if ( $obErro->ocorreu() ) {
                    break;
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTFolhaPagamentoContratoServidorComplementar );

    return $obErro;
}

/**
    * AbrirFolhaComplementar
    * @access Public
*/
function abrirFolhaComplementar($boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoComplementar.class.php"         );
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoComplementarSituacao.class.php" );
    $obTFolhaPagamentoComplementar          = new TFolhaPagamentoComplementar;
    $obTFolhaPagamentoComplementarSituacao  = new TFolhaPagamentoComplementarSituacao;
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if (!$obErro->ocorreu()) {
        if ( $this->getCodComplementar() == "" ) {
            //Codigo executado quando o procedimento não for o reabrir
            $this->setSituacao("a");
            $obErro = $this->listarFolhaComplementar($rsFolhaComplementar,$boTransacao);
            $rsFolhaComplementar->setUltimoElemento();
            if ( !$obErro->ocorreu() ) {
                if ( $rsFolhaComplementar->getCampo('cod_complementar') == "" ) {
                    $this->setCodComplementar(1);
                } else {
                    $this->setCodComplementar($rsFolhaComplementar->getCampo('cod_complementar')+1);
                }
                $obTFolhaPagamentoComplementar->setDado("cod_complementar"          ,$this->getCodComplementar()                                                );
                $obTFolhaPagamentoComplementar->setDado("cod_periodo_movimentacao"  ,$this->roRFolhaPagamentoPeriodoMovimentacao->getCodPeriodoMovimentacao()   );
                $obErro = $obTFolhaPagamentoComplementar->inclusao($boTransacao);
            }
        }
        if ( !$obErro->ocorreu() ) {
            $this->setSituacao("a");
            $obTFolhaPagamentoComplementarSituacao->setDado("cod_periodo_movimentacao"  ,$this->roRFolhaPagamentoPeriodoMovimentacao->getCodPeriodoMovimentacao()   );
            $obTFolhaPagamentoComplementarSituacao->setDado("cod_complementar"          ,$this->getCodComplementar()                                                );
            $obTFolhaPagamentoComplementarSituacao->setDado("situacao"                  ,$this->getSituacao()                                                       );
            $obErro = $obTFolhaPagamentoComplementarSituacao->inclusao($boTransacao);
        }
        if ( !$obErro->ocorreu() ) {
            $obErro = $obTFolhaPagamentoComplementarSituacao->recuperaNow3($stTimestamp,$boTransacao);
            $this->setTimestamp($stTimestamp);
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTFolhaPagamentoComplementar );

    return $obErro;
}

/**
    * fecharFolhaComplementar
    * @access Public
*/
function fecharFolhaComplementar($boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoComplementarSituacao.class.php"         );
    $obTFolhaPagamentoComplementarSituacao = new TFolhaPagamentoComplementarSituacao;
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if (!$obErro->ocorreu()) {
        $obErro = $this->roRFolhaPagamentoPeriodoMovimentacao->obRFolhaPagamentoFolhaSituacao->consultarFolha($boTransacao);
        if (!$obErro->ocorreu()) {
            $obErro = $this->consultarFolhaComplementarAberta($boTransacao);
        }
        if (!$obErro->ocorreu()) {
            $this->setSituacao("f");
            $obTFolhaPagamentoComplementarSituacao->setDado('cod_complementar'          ,$this->getCodComplementar()                                                );
            $obTFolhaPagamentoComplementarSituacao->setDado('cod_periodo_movimentacao'  ,$this->roRFolhaPagamentoPeriodoMovimentacao->getCodPeriodoMovimentacao()   );
            $obTFolhaPagamentoComplementarSituacao->setDado('situacao'                  ,$this->getSituacao()                                                       );
            $obErro = $obTFolhaPagamentoComplementarSituacao->inclusao($boTransacao);
        }        
        if (!$obErro->ocorreu() and $this->roRFolhaPagamentoPeriodoMovimentacao->obRFolhaPagamentoFolhaSituacao->getSituacao() == 'Aberto') {
            $this->roRFolhaPagamentoPeriodoMovimentacao->addRFolhaPagamentoPeriodoContratoServidor();
            $this->roRFolhaPagamentoPeriodoMovimentacao->addRFolhaPagamentoCalculoFolhaPagamento();
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoComplementar.class.php");
            $obTFolhaPagamentoRegistroEventoComplementar = new TFolhaPagamentoRegistroEventoComplementar();
            $obTFolhaPagamentoRegistroEventoComplementar->setDado("cod_periodo_movimentacao",$this->roRFolhaPagamentoPeriodoMovimentacao->getCodPeriodoMovimentacao());
            $obTFolhaPagamentoRegistroEventoComplementar->setDado("cod_complementar",$this->getCodComplementar());
            $obErro = $obTFolhaPagamentoRegistroEventoComplementar->recuperaContratosComRegistroDeEventoReduzido($rsContratosComplementar,"","",$boTransacao);
            if ($obErro->ocorreu()) {
                return $obErro;
            }
            $stCodContratos = "";
            while (!$rsContratosComplementar->eof()) {
                $stCodContratos .= $rsContratosComplementar->getCampo("cod_contrato").",";
                $rsContratosComplementar->proximo();
            }
            $stCodContratos = substr($stCodContratos,0,strlen($stCodContratos)-1);
            $rsRegistroEventoPeriodo = new RecordSet();
            if (trim($stCodContratos) != "") {
                include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEvento.class.php");
                $obTFolhaPagamentoRegistroEvento = new TFolhaPagamentoRegistroEvento();
                $stFiltro  = " AND registro_evento_periodo.cod_periodo_movimentacao = ".$this->roRFolhaPagamentoPeriodoMovimentacao->getCodPeriodoMovimentacao();
                $stFiltro .= " AND registro_evento_periodo.cod_contrato IN (".$stCodContratos.")";
                $obErro = $obTFolhaPagamentoRegistroEvento->recuperaRegistrosDeEventos($rsRegistroEventoPeriodo,$stFiltro,'',$boTransacao);
            }      
            if ($obErro->ocorreu()) {
                return $obErro;
            }
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculadoDependente.class.php");
            $obTFolhaPagamentoEventoCalculadoDependente = new TFolhaPagamentoEventoCalculadoDependente();
            while (!$rsRegistroEventoPeriodo->eof()) {
                $inCodEvento   = $rsRegistroEventoPeriodo->getCampo('cod_evento');
                $inCodRegistro = $rsRegistroEventoPeriodo->getCampo('cod_registro');
                $stTimestamp   = $rsRegistroEventoPeriodo->getCampo('timestamp');

                $obTFolhaPagamentoEventoCalculadoDependente->setDado("cod_registro"       , $inCodRegistro);
                $obTFolhaPagamentoEventoCalculadoDependente->setDado("cod_evento"         , $inCodEvento);
                $obTFolhaPagamentoEventoCalculadoDependente->setDado("timestamp_registro" , $stTimestamp);
                $obErro = $obTFolhaPagamentoEventoCalculadoDependente->exclusao($boTransacao);
                if ( $obErro->ocorreu() ) {
                    return $obErro;
                }
                $obErro = $this->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoCalculoFolhaPagamento->excluirEventoCalculado($inCodEvento,$inCodRegistro,$stTimestamp,$boTransacao);
                if ( $obErro->ocorreu() ) {
                    return $obErro;
                }
                $obErro = $this->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoCalculoFolhaPagamento->excluirLogErroCalculo($inCodEvento,$inCodRegistro,$stTimestamp,$boTransacao);
                if ( $obErro->ocorreu() ) {
                    return $obErro;
                }
                $rsRegistroEventoPeriodo->proximo();
            }
            //Calculo dos contratos na folha salário
            if (!$obErro->ocorreu()) {
                $rsContratosComplementar->setPrimeiroElemento();
                $obErro = $this->adicionarContratoAutomaticosFolhaSalario($rsContratosComplementar,$rsContratos,$boTransacao);                
            }
            if (!$obErro->ocorreu()) {
                $obErro = $this->detetarInformacoesDoCalculoFolhaSalario($rsContratos,$boTransacao);
            }
            if (!$obErro->ocorreu()) {
                include_once(CAM_GRH_FOL_MAPEAMENTO."FFolhaPagamentoCalculaFolha.class.php");
                $obFFolhaPagamentoCalculaFolha = new FFolhaPagamentoCalculaFolha();
                $rsContratos->setPrimeiroElemento();
                while ( !$rsContratos->eof() ) {
                    $obFFolhaPagamentoCalculaFolha->setDado('cod_contrato',$rsContratos->getCampo("cod_contrato"));
                    $obFFolhaPagamentoCalculaFolha->setDado('boErro','f');
                    $obErro = $obFFolhaPagamentoCalculaFolha->calculaFolha($rsCalcula,$boTransacao);
                    if ($obErro->ocorreu()) {
                        return $obErro;
                    }else{
                        $rsContratos->proximo();
                    }
                }
            }
        }
    }    
    if (!$obErro->ocorreu()) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTFolhaPagamentoComplementarSituacao );        
    }

    return $obErro;
}

function adicionarContratoAutomaticosFolhaSalario($rsContratos,&$rsContratosAutomaticos,$boTransacao="")
{
    $rsContratosAutomaticos = new Recordset;

    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
    $obErro = $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao,"","",$boTransacao);

    if (!$obErro->ocorreu()) {
        $arCGMs = array();
        while (!$rsContratos->eof()) {
            $arCGMs[] = $rsContratos->getCampo("numcgm");
            $rsContratos->proximo();
        }
        $rsContratos->setPrimeiroElemento();
        $arCGMs = array_unique($arCGMs);
        $stCGMs = implode(",",$arCGMs);
        if (trim($stCGMs) != "") {
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEvento.class.php");
            $obTFolhaPagamentoRegistroEvento = new TFolhaPagamentoRegistroEvento();
            $obTFolhaPagamentoRegistroEvento->setDado("cod_periodo_movimentacao",$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
            $obTFolhaPagamentoRegistroEvento->setDado("numcgm",$stCGMs);
            $obErro = $obTFolhaPagamentoRegistroEvento->recuperaContratosAutomaticos($rsContratosAutomaticos,"","",$boTransacao);
        }
    }

    return $obErro;
}

function detetarInformacoesDoCalculoFolhaSalario($rsContratos,$boTransacao="")
{
    //Deleta os eventos calculados de uma única vez
    $stCodContratos = "";
    while (!$rsContratos->eof()) {
        $stCodContratos .= $rsContratos->getCampo("cod_contrato").",";
        $rsContratos->proximo();
    }
    $stCodContratos = substr($stCodContratos,0,strlen($stCodContratos)-1);
    include_once(CAM_GRH_FOL_MAPEAMENTO."FFolhaPagamentoDeletarInformacoesCalculo.class.php");
    $obFFolhaPagamentoDeletarInformacoesCalculo = new FFolhaPagamentoDeletarInformacoesCalculo();
    $obFFolhaPagamentoDeletarInformacoesCalculo->setDado("stTipoFolha"          ,"S"            );
    $obFFolhaPagamentoDeletarInformacoesCalculo->setDado("inCodComplementar"    ,0              );
    $obFFolhaPagamentoDeletarInformacoesCalculo->setDado("stCodContratos"       ,$stCodContratos);
    $obErro = $obFFolhaPagamentoDeletarInformacoesCalculo->deletarInformacoesCalculo($rsDeletar,$boTransacao);
    //Deleta os eventos calculados de uma única vez
    return $obErro;
}

/**
    * excluirFolhaComplementar
    * @access Public
*/
function excluirFolhaComplementar($boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoComplementar.class.php");
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoComplementarSituacao.class.php" );
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoComplementarSituacaoFechada.class.php" );
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoContratoServidorComplementar.class.php" );
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoComplementar.class.php" );
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoDeducaoDependente.class.php" );
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoDeducaoDependenteComplementar.class.php" );
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoComplementar.class.php");
    $obTFolhaPagamentoComplementar                  = new TFolhaPagamentoComplementar;
    $obTFolhaPagamentoComplementarSituacao          = new TFolhaPagamentoComplementarSituacao;
    $obTFolhaPagamentoComplementarSituacaoFechada   = new TFolhaPagamentoComplementarSituacaoFechada;
    $obTFolhaPagamentoContratoServidorComplementar  = new TFolhaPagamentoContratoServidorComplementar;
    $obTFolhaPagamentoDeducaoDependente             = new TFolhaPagamentoDeducaoDependente;
    $obTFolhaPagamentoDeducaoDependenteComplementar = new TFolhaPagamentoDeducaoDependenteComplementar;
    $obTFolhaPagamentoRegistroEventoComplementar = new TFolhaPagamentoRegistroEventoComplementar;
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if (!$obErro->ocorreu()) {
        $this->addRFolhaPagamentoRegistroEventoComplementar();
        $obErro = $this->roRFolhaPagamentoRegistroEventoComplementar->listarTodosRegistrosEventoComplementar($rsRegistroEventoComplementar,$boTransacao);
        if (!$obErro->ocorreu()) {
            $arContratos = array();
            while (!$rsRegistroEventoComplementar->eof()) {
                //Monta array de contratos que deverão ser recalculados no salário
                $arContratos[] = array("cod_contrato"=>$rsRegistroEventoComplementar->getCampo("cod_contrato"));

                $obTFolhaPagamentoRegistroEventoComplementar->setDado("cod_registro",$rsRegistroEventoComplementar->getCampo("cod_registro"));
                $obTFolhaPagamentoRegistroEventoComplementar->setDado("cod_evento",$rsRegistroEventoComplementar->getCampo("cod_evento"));
                $obTFolhaPagamentoRegistroEventoComplementar->setDado("desdobramento",$rsRegistroEventoComplementar->getCampo("cod_configuracao"));
                $obTFolhaPagamentoRegistroEventoComplementar->setDado("timestamp",$rsRegistroEventoComplementar->getCampo("timestamp"));
                $obErro = $obTFolhaPagamentoRegistroEventoComplementar->deletarRegistroEvento($boTransacao);
                if ($obErro->ocorreu()) {
                    break;
                }
                $rsRegistroEventoComplementar->proximo();
            }
        }
        $arContratos = array_unique($arContratos);

        if (!$obErro->ocorreu()) {
            $stFiltroDeducaoDependenteComplementar  = " WHERE cod_periodo_movimentacao =  ".$this->roRFolhaPagamentoPeriodoMovimentacao->getCodPeriodoMovimentacao();
            $stFiltroDeducaoDependenteComplementar .= "   AND cod_complementar = ".$this->getCodComplementar();

            $obTFolhaPagamentoDeducaoDependenteComplementar->recuperaTodos($rsDeducaoDependenteComplementar, $stFiltroDeducaoDependenteComplementar, "", $boTransacao);

            while (!$rsDeducaoDependenteComplementar->eof()) {
                $obTFolhaPagamentoDeducaoDependenteComplementar->setDado('numcgm', $rsDeducaoDependenteComplementar->getCampo('numcgm'));
                $obTFolhaPagamentoDeducaoDependenteComplementar->setDado('cod_periodo_movimentacao', $rsDeducaoDependenteComplementar->getCampo('cod_periodo_movimentacao'));
                $obTFolhaPagamentoDeducaoDependenteComplementar->setDado('cod_tipo', $rsDeducaoDependenteComplementar->getCampo('cod_tipo'));
                $obErro = $obTFolhaPagamentoDeducaoDependenteComplementar->exclusao($boTransacao);

                if (!$obErro->ocorreu()) {
                    $obTFolhaPagamentoDeducaoDependente->setDado("cod_periodo_movimentacao" , $rsDeducaoDependenteComplementar->getCampo('cod_periodo_movimentacao') );
                    $obTFolhaPagamentoDeducaoDependente->setDado("numcgm" , $rsDeducaoDependenteComplementar->getCampo('numcgm') );
                    $obTFolhaPagamentoDeducaoDependente->setDado("cod_tipo", $rsDeducaoDependenteComplementar->getCampo('cod_tipo') );
                    $obErro = $obTFolhaPagamentoDeducaoDependente->exclusao($boTransacao);
                }

                if ($obErro->ocorreu()) {
                    break;
                }

                $rsDeducaoDependenteComplementar->proximo();
            }
        }

        if (!$obErro->ocorreu()) {
            $obTFolhaPagamentoContratoServidorComplementar->setDado("cod_periodo_movimentacao"  ,$this->roRFolhaPagamentoPeriodoMovimentacao->getCodPeriodoMovimentacao()    );
            $obTFolhaPagamentoContratoServidorComplementar->setDado("cod_complementar"          ,$this->getCodComplementar());
            $obErro = $obTFolhaPagamentoContratoServidorComplementar->exclusao($boTransacao);
        }
        if (!$obErro->ocorreu()) {
            $obTFolhaPagamentoComplementarSituacaoFechada->setDado("cod_complementar"          ,$this->getCodComplementar()    );
            $obTFolhaPagamentoComplementarSituacaoFechada->setDado("cod_periodo_movimentacao"  ,$this->roRFolhaPagamentoPeriodoMovimentacao->getCodPeriodoMovimentacao());
            $obErro = $obTFolhaPagamentoComplementarSituacaoFechada->exclusao($boTransacao);
        }
        if (!$obErro->ocorreu()) {
            $obTFolhaPagamentoComplementarSituacao->setDado("cod_complementar"          ,$this->getCodComplementar()    );
            $obTFolhaPagamentoComplementarSituacao->setDado("cod_periodo_movimentacao"  ,$this->roRFolhaPagamentoPeriodoMovimentacao->getCodPeriodoMovimentacao());
            $obErro = $obTFolhaPagamentoComplementarSituacao->exclusao($boTransacao);
        }
        if (!$obErro->ocorreu()) {
            $obTFolhaPagamentoComplementar->setDado("cod_complementar"          ,$this->getCodComplementar()        );
            $obTFolhaPagamentoComplementar->setDado("cod_periodo_movimentacao"  ,$this->roRFolhaPagamentoPeriodoMovimentacao->getCodPeriodoMovimentacao());
            $obErro = $obTFolhaPagamentoComplementar->exclusao($boTransacao);
        }
        //Recalculo do salário
        if (!$obErro->ocorreu() and count($arContratos) > 0) {
            $rsContratos = new recordset;
            $rsContratos->preenche($arContratos);

            if (!$obErro->ocorreu()) {
                $obErro = $this->detetarInformacoesDoCalculoFolhaSalario($rsContratos,$boTransacao);
            }
            if (!$obErro->ocorreu()) {
                include_once(CAM_GRH_FOL_MAPEAMENTO."FFolhaPagamentoCalculaFolha.class.php");
                $obFFolhaPagamentoCalculaFolha = new FFolhaPagamentoCalculaFolha();
                $rsContratos->setPrimeiroElemento();
                while ( !$rsContratos->eof() ) {
                    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEvento.class.php");
                    $obTFolhaPagamentoRegistroEvento = new TFolhaPagamentoRegistroEvento();
                    $stFiltro  = " AND registro_evento_periodo.cod_periodo_movimentacao = ".$this->roRFolhaPagamentoPeriodoMovimentacao->getCodPeriodoMovimentacao();
                    $stFiltro .= " AND registro_evento_periodo.cod_contrato = ".$rsContratos->getCampo("cod_contrato");
                    $obTFolhaPagamentoRegistroEvento->recuperaRegistrosDeEventos($rsRegistroEvento,$stFiltro,'',$boTransacao);

                    if ($rsRegistroEvento->getNumLinhas() > 0) {
                        $obFFolhaPagamentoCalculaFolha->setDado('cod_contrato',$rsContratos->getCampo("cod_contrato"));
                        $obFFolhaPagamentoCalculaFolha->setDado('boErro','f');
                        $obErro = $obFFolhaPagamentoCalculaFolha->calculaFolha($rsCalcula,$boTransacao);
                    }
                    $rsContratos->proximo();
                }
            }
        }
    }

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTFolhaPagamentoComplementar );

    return $obErro;
}

/**
    * Método listarFolhaComplementar
    * @access Private
*/
function listarFolhaComplementar(&$rsRecordSet,$boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoComplementar.class.php");
    $obTFolhaPagamentoComplementar = new TFolhaPagamentoComplementar;
    if ( $this->roRFolhaPagamentoPeriodoMovimentacao->getCodPeriodoMovimentacao() ) {
        $stFiltro .= " AND complementar_situacao.cod_periodo_movimentacao = ".$this->roRFolhaPagamentoPeriodoMovimentacao->getCodPeriodoMovimentacao();
    }
    $obErro = $obTFolhaPagamentoComplementar->recuperaRelacionamento($rsRecordSet,$stFiltro," ORDER BY complementar.cod_complementar",$boTransacao);

    return $obErro;
}

/**
    * Método listarFolhaComplementarCalculadaPorContrato
    * @access Private
*/
function listarFolhaComplementarCalculadaPorContrato(&$rsRecordSet,$inCodPeriodoMovimentacao,$inCodContrato="",$boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoComplementar.class.php");
    $obTFolhaPagamentoComplementar = new TFolhaPagamentoComplementar;
    $stFiltro .= " WHERE cod_periodo_movimentacao = ".$inCodPeriodoMovimentacao;
    if (trim($inCodContrato)!="") {
        $stFiltro .= " AND cod_contrato = ".$inCodContrato;
    }
    $obErro = $obTFolhaPagamentoComplementar->recuperaFolhaComplementarCalculadaPorContrato($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

/**
    * Método listarFolhaComplementarFechadaPosteriorSalario
    * @access Private
*/
function listarFolhaComplementarFechadaPosteriorSalario(&$rsRecordSet,$boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoComplementarSituacao.class.php");
    $obTFolhaPagamentoComplementarSituacao = new TFolhaPagamentoComplementarSituacao;
    if ( $this->getSituacao() ) {
        $stFiltro .= " AND complementar_situacao.situacao = '".$this->getSituacao()."'";
    }
    if ( $this->getCodComplementar() ) {
        $stFiltro .= " AND complementar_situacao.cod_complementar = ".$this->getCodComplementar();
    }
    if ( $this->getTimestamp() ) {
        $stFiltro .= " AND complementar_situacao.timestamp  > '".$this->getTimestamp()."'";
    }
    if ( $this->roRFolhaPagamentoPeriodoMovimentacao->getCodPeriodoMovimentacao() ) {
        $stFiltro .= " AND complementar_situacao.cod_periodo_movimentacao = ".$this->roRFolhaPagamentoPeriodoMovimentacao->getCodPeriodoMovimentacao();
    }
    $stOrder = " cod_complementar" ;
    $obErro = $obTFolhaPagamentoComplementarSituacao->recuperaRelacionamentoFechadaPosteriorSalario($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

/**
    * Método listarFolhaComplementarFechadaAnteriorSalario
    * @access Private
*/
function listarFolhaComplementarFechadaAnteriorSalario(&$rsRecordSet,$boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoComplementarSituacao.class.php");
    $obTFolhaPagamentoComplementarSituacao = new TFolhaPagamentoComplementarSituacao;
    if ( $this->getSituacao() ) {
        $stFiltro .= " AND complementar_situacao.situacao = '".$this->getSituacao()."'";
    }
    if ( $this->getCodComplementar() ) {
        $stFiltro .= " AND complementar_situacao.cod_complementar = ".$this->getCodComplementar();
    }
    if ( $this->getTimestamp() ) {
        $stFiltro .= " AND complementar_situacao.timestamp  < '".$this->getTimestamp()."'";
    }
    if ( $this->roRFolhaPagamentoPeriodoMovimentacao->getCodPeriodoMovimentacao() ) {
        $stFiltro .= " AND complementar_situacao.cod_periodo_movimentacao = ".$this->roRFolhaPagamentoPeriodoMovimentacao->getCodPeriodoMovimentacao();
    }
    $stOrder = " cod_complementar" ;
    $obErro = $obTFolhaPagamentoComplementarSituacao->recuperaRelacionamento($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

/**
    * Método listarRegistroComplementarPorContrato
    * @access Private
*/
function listarRegistroComplementarPorContrato(&$rsRecordSet,$boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoContratoServidorComplementar.class.php");
    $obTFolhaPagamentoContratoServidorComplementar = new TFolhaPagamentoContratoServidorComplementar;
    if ( $this->getCodComplementar() ) {
        $stFiltro .= " AND cod_complementar = ".$this->getCodComplementar();
    }
    if ( $this->roRFolhaPagamentoPeriodoMovimentacao->getCodPeriodoMovimentacao() ) {
        $stFiltro .= " AND cod_periodo_movimentacao = ".$this->roRFolhaPagamentoPeriodoMovimentacao->getCodPeriodoMovimentacao();
    }
    if ( is_object($this->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor) ) {
        if ( $this->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->getCodContrato() ) {
            $stFiltro .= " AND cod_contrato = ".$this->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->getCodContrato();
        }
    }
    if ($stFiltro != "") {
        $stFiltro = " WHERE ".substr($stFiltro,4,strlen($stFiltro));
    }
    $obErro = $obTFolhaPagamentoContratoServidorComplementar->recuperaTodos($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

/**
    * Método consultarFolhaComplementar
    * @access Private
*/
function consultarFolhaComplementarAberta($boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoComplementar.class.php");
    $obTFolhaPagamentoComplementar = new TFolhaPagamentoComplementar;
    $stFiltro .= " AND complementar_situacao.cod_periodo_movimentacao = ".$this->roRFolhaPagamentoPeriodoMovimentacao->getCodPeriodoMovimentacao();
    $stFiltro .= " AND complementar_situacao.situacao = 'a'";
    $obErro = $obTFolhaPagamentoComplementar->recuperaRelacionamento($rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    if ( !$obErro->ocorreu() ) {
        $this->setCodComplementar( $rsRecordSet->getCampo('cod_complementar') );
        $this->setTimestamp( $rsRecordSet->getCampo('timestamp') );
        $this->setSituacao( $rsRecordSet->getCampo('situacao') );
    }

    return $obErro;
}

/**
    * Método addRFolhaPagamentoRegistroEventoComplementar
    * @access Public
*/
function addRFolhaPagamentoRegistroEventoComplementar()
{
    $this->arRFolhaPagamentoRegistroEventoComplementar[] = new RFolhaPagamentoRegistroEventoComplementar( $this );
    $this->roRFolhaPagamentoRegistroEventoComplementar = &$this->arRFolhaPagamentoRegistroEventoComplementar[ count($this->arRFolhaPagamentoRegistroEventoComplementar)-1 ];
}

}
?>
