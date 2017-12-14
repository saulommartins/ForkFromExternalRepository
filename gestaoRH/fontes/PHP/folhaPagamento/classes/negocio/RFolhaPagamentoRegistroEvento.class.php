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
    * Classe de regra de negócio para RFolhaPagamentoRegistroEvento
    * Data de Criação: 09/11/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Regra de Negócio

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-10-11 18:10:24 -0300 (Qui, 11 Out 2007) $

    * Casos de uso: uc-04.05.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoEvento.class.php"                                     );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoPeriodo.class.php"                   );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEvento.class.php"                          );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoUltimoRegistroEvento.class.php"                    );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoParcela.class.php"                   );

class RFolhaPagamentoRegistroEvento
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
var $inCodRegistroEvento;
/**
   * @access Private
   * @var Numeric
*/
var $nuValor;
/**
   * @access Private
   * @var Numeric
*/
var $nuQuantidade;
/**
   * @access Private
   * @var Numeric
*/
var $nuParcela;
/**
   * @access Private
   * @var Boolean
*/
var $boProporcional;
/**
   * @access Private
   * @var Boolean
*/
var $boAutomatico;
/**
   * @access Private
   * @var String
*/
var $stTimestamp;

/**
   * @access Private
   * @var Object
*/
var $roRFolhaPagamentoPeriodoContratoServidor;
/**
   * @access Private
   * @var Object
*/
var $obRFolhaPagamentoEvento;

/**
    * @access Public
    * @param Object $valor
*/
function setTransacao($valor) { $this->obTransacao                               = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodRegistroEvento($valor) { $this->inCodRegistroEvento                       = $valor; }
/**
    * @access Public
    * @param Numeric $valor
*/
function setValor($valor) { $this->nuValor                                   = $valor; }
/**
    * @access Public
    * @param Numeric $valor
*/
function setQuantidade($valor) { $this->nuQuantidade                              = $valor; }
/**
    * @access Public
    * @param Numeric $valor
*/
function setParcela($valor) { $this->nuParcela                                 = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setProporcional($valor) { $this->boProporcional                            = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setAutomatico($valor) { $this->boAutomatico                              = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setTimestamp($valor) { $this->stTimestamp                               = $valor; }

/**
    * @access Public
    * @param Object $valor
*/
function setRORFolhaPagamentoPeriodoContratoServidor(&$valor) { $this->roRFolhaPagamentoPeriodoContratoServidor  = &$valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRFolhaPagamentoEvento($valor) { $this->obRFolhaPagamentoEvento                   = $valor; }

/**
    * @access Public
    * @return Object
*/
function getTransacao() { return $this->obTransacao;                               }
/**
    * @access Public
    * @return Integer
*/
function getCodRegistroEvento() { return $this->inCodRegistroEvento;                       }
/**
    * @access Public
    * @return Numeric
*/
function getValor() { return $this->nuValor;                                   }
/**
    * @access Public
    * @return Numeric
*/
function getQuantidade() { return $this->nuQuantidade;                              }
/**
    * @access Public
    * @return Numeric
*/
function getParcela() { return $this->nuParcela;                                 }
/**
    * @access Public
    * @return Boolean
*/
function getProporcional() { return $this->boProporcional;                            }
/**
    * @access Public
    * @return Boolean
*/
function getAutomatico() { return $this->boAutomatico;                              }
/**
    * @access Public
    * @return String
*/
function getTimestamp() { return $this->stTimestamp;                               }

/**
    * @access Public
    * @return Object
*/
function getRORFolhaPagamentoPeriodoContratoServidor() { return $this->roRFolhaPagamentoPeriodoContratoServidor;  }
/**
    * @access Public
    * @return Object
*/
function getRFolhaPagamentoEvento() { return $this->obRFolhaPagamentoEvento;                   }

/**
     * Método construtor
     * @access Private
*/
function RFolhaPagamentoRegistroEvento()
{
    $this->setTransacao                     ( new transacao                              );
    $this->setRFolhaPagamentoEvento         ( new RFolhaPagamentoEvento                  );

}

/**
    * Método incluirRegistroEvento
    * @access Public
*/
function incluirRegistroEvento($boTransacao = "")
{
    $obTFolhaPagamentoRegistroEventoPeriodo     = new TFolhaPagamentoRegistroEventoPeriodo;
    $obTFolhaPagamentoUltimoRegistroEvento      = new TFolhaPagamentoUltimoRegistroEvento;
    $obTFolhaPagamentoRegistroEvento            = new TFolhaPagamentoRegistroEvento;
    $obTFolhaPagamentoRegistroEventoParcela     = new TFolhaPagamentoRegistroEventoParcela;
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() and $this->getCodRegistroEvento() == "" ) {
        $stCampoCod         = $obTFolhaPagamentoRegistroEvento->getCampoCod();
        $stComplementoChave = $obTFolhaPagamentoRegistroEvento->getComplementoChave();
        $obTFolhaPagamentoRegistroEvento->setCampoCod('cod_registro');
        $obTFolhaPagamentoRegistroEvento->setComplementoChave('');
        $obErro = $obTFolhaPagamentoRegistroEvento->proximoCod($inCodRegistro,$boTransacao);
        $this->setCodRegistroEvento( $inCodRegistro );
        $obTFolhaPagamentoRegistroEvento->setCampoCod($stCampoCod);
        $obTFolhaPagamentoRegistroEvento->setComplementoChave($stComplementoChave);

        $obTFolhaPagamentoRegistroEventoPeriodo->setDado('cod_registro',                $this->getCodRegistroEvento()                                       );
        $obTFolhaPagamentoRegistroEventoPeriodo->setDado('cod_contrato',                $this->roRFolhaPagamentoPeriodoContratoServidor->getCodContrato()   );
        $obTFolhaPagamentoRegistroEventoPeriodo->setDado('cod_periodo_movimentacao',    $this->roRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoPeriodoMovimentacao->getCodPeriodoMovimentacao());
        $obErro = $obTFolhaPagamentoRegistroEventoPeriodo->inclusao($boTransacao);
    }
    if ( !$obErro->ocorreu() ) {
        $obErro = $obTFolhaPagamentoRegistroEvento->recuperaNow3($stNow,$boTransacao);
        $this->setTimestamp($stNow);
    }
    if ( !$obErro->ocorreu() ) {
        $obTFolhaPagamentoRegistroEvento->setDado('cod_registro',   $this->getCodRegistroEvento()                   );
        $obTFolhaPagamentoRegistroEvento->setDado('timestamp',      $this->getTimestamp()                           );
        $obTFolhaPagamentoRegistroEvento->setDado('cod_evento',     $this->obRFolhaPagamentoEvento->getCodEvento()  );
        $obTFolhaPagamentoRegistroEvento->setDado('valor',          $this->getValor()                               );
        $obTFolhaPagamentoRegistroEvento->setDado('quantidade',     $this->getQuantidade()                          );
        $obTFolhaPagamentoRegistroEvento->setDado('proporcional',   $this->getProporcional()                        );
        $obTFolhaPagamentoRegistroEvento->setDado('automatico',     $this->getAutomatico()                          );
        $obErro =  $obTFolhaPagamentoRegistroEvento->inclusao($boTransacao);
    }
    if ( !$obErro->ocorreu() ) {
        $obTFolhaPagamentoUltimoRegistroEvento->setDado('cod_registro',     $this->getCodRegistroEvento()                   );
        $obTFolhaPagamentoUltimoRegistroEvento->setDado('timestamp',        $this->getTimestamp()                           );
        $obTFolhaPagamentoUltimoRegistroEvento->setDado('cod_evento',       $this->obRFolhaPagamentoEvento->getCodEvento()  );
        $obErro =  $obTFolhaPagamentoUltimoRegistroEvento->inclusao($boTransacao);
    }
    if ( !$obErro->ocorreu() and $this->getParcela() ) {
        $obTFolhaPagamentoRegistroEventoParcela->setDado('cod_registro',   $this->getCodRegistroEvento()                    );
        $obTFolhaPagamentoRegistroEventoParcela->setDado('timestamp',      $this->getTimestamp()                            );
        $obTFolhaPagamentoRegistroEventoParcela->setDado('cod_evento',     $this->obRFolhaPagamentoEvento->getCodEvento()   );
        $obTFolhaPagamentoRegistroEventoParcela->setDado('parcela',        $this->getParcela()                              );
        $obErro = $obTFolhaPagamentoRegistroEventoParcela->inclusao($boTransacao);
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTFolhaPagamentoRegistroEvento );

    return $obErro;
}

/**
    * Método excluirRegistroEvento
    * @access Public
*/
function excluirRegistroEvento($boTransacao = "")
{
    $obTFolhaPagamentoRegistroEvento        = new TFolhaPagamentoRegistroEvento;
    $obTFolhaPagamentoRegistroEventoParcela = new TFolhaPagamentoRegistroEventoParcela;
    $obTFolhaPagamentoRegistroEventoPeriodo = new TFolhaPagamentoRegistroEventoPeriodo;
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $inCodEvento = $this->obRFolhaPagamentoEvento->getCodEvento();
        $inCodRegistro = $this->getCodRegistroEvento();
        $stTimestamp   = $this->getTimestamp();
        $obErro = $this->excluirUltimoRegistroEvento($inCodEvento,$inCodRegistro,$stTimestamp,$boTransacao);
    }
    if ( !$obErro->ocorreu() ) {
        $obTFolhaPagamentoRegistroEventoParcela->setDado('cod_registro',     $this->getCodRegistroEvento()   );
        $obTFolhaPagamentoRegistroEventoParcela->setDado('cod_evento',       $this->obRFolhaPagamentoEvento->getCodEvento());
        $obErro =  $obTFolhaPagamentoRegistroEventoParcela->exclusao($boTransacao);
    }
    if ( !$obErro->ocorreu() ) {
        $obTFolhaPagamentoRegistroEvento->setDado('cod_registro',     $this->getCodRegistroEvento()   );
        $obErro =  $obTFolhaPagamentoRegistroEvento->exclusao($boTransacao);
    }
    if ( !$obErro->ocorreu() ) {
        $obTFolhaPagamentoRegistroEventoPeriodo->setDado('cod_registro',  $this->getCodRegistroEvento());
        $obErro =  $obTFolhaPagamentoRegistroEventoPeriodo->exclusao($boTransacao);
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTFolhaPagamentoUltimoRegistroEvento );

    return $obErro;
}

/**
    * Método excluirUltimoRegistroEvento
    * @access Public
*/
function excluirUltimoRegistroEvento($inCodEvento,$inCodRegistro,$stTimestamp,$boTransacao = "")
{
    $obTFolhaPagamentoUltimoRegistroEvento      = new TFolhaPagamentoUltimoRegistroEvento;
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ( !is_object($this->roRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoCalculoFolhaPagamento) ) {
            $this->roRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoPeriodoMovimentacao->addRFolhaPagamentoCalculoFolhaPagamento();
        }
        $obErro = $this->roRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoCalculoFolhaPagamento->excluirEventoCalculado($inCodEvento,$inCodRegistro,$stTimestamp,$boTransacao);
        if ( !$obErro->ocorreu() ) {
            $obErro = $this->roRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoCalculoFolhaPagamento->excluirLogErroCalculo($inCodEvento,$inCodRegistro,$stTimestamp,$boTransacao);
        }
        if ( !$obErro->ocorreu() ) {
            $obTFolhaPagamentoUltimoRegistroEvento->setDado('timestamp',        $stTimestamp );
            $obTFolhaPagamentoUltimoRegistroEvento->setDado('cod_evento',       $inCodEvento );
            $obTFolhaPagamentoUltimoRegistroEvento->setDado('cod_registro',     $inCodRegistro);
            $obErro =  $obTFolhaPagamentoUltimoRegistroEvento->exclusao($boTransacao);
        }

    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTFolhaPagamentoUltimoRegistroEvento );

    return $obErro;
}

/**
    * Método listar
    * @access Private
*/
function listar(&$rsRecordSet,$stFiltro,$stOrder,$boTransacao)
{
    $obTFolhaPagamentoRegistroEvento = new TFolhaPagamentoRegistroEvento ;
    $obErro = $obTFolhaPagamentoRegistroEvento->recuperaRelacionamento($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

/**
    * Método listarRegistroEvento
    * @access Private
*/
function listarRegistroEvento(&$rsRecordSet,$boTransacao="")
{
    $stFiltro = "";
    $stOrder  = " order by contrato.cod_contrato,codigo";
    if ($this->roRFolhaPagamentoPeriodoContratoServidor) {
        if ( $this->roRFolhaPagamentoPeriodoContratoServidor->getCodContrato() ) {
            $stFiltro .= " AND contrato.cod_contrato = ".$this->roRFolhaPagamentoPeriodoContratoServidor->getCodContrato();
        }
        if ( $this->roRFolhaPagamentoPeriodoContratoServidor->getRegistro() != "" ) {
            $stFiltro .= " AND registro = ".$this->roRFolhaPagamentoPeriodoContratoServidor->getRegistro();
        }
        if ( $this->roRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoPeriodoMovimentacao->getCodPeriodoMovimentacao() ) {
            $stFiltro .= " AND periodo_movimentacao.cod_periodo_movimentacao = ".$this->roRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoPeriodoMovimentacao->getCodPeriodoMovimentacao();
        }
        if ( $this->roRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoPeriodoMovimentacao->getDtFinal() ) {
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
            $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
            $stFiltroCompetencia = " AND to_char(dt_final, 'mm/yyyy') = '".$this->roRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoPeriodoMovimentacao->getDtFinal()."'";
            $obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacao($rsPeriodo,$stFiltroCompetencia);
            $stFiltro .= " AND registro_evento_periodo.cod_periodo_movimentacao = ".$rsPeriodo->getCampo("cod_periodo_movimentacao");
        }
        if ( $this->roRFolhaPagamentoPeriodoContratoServidor->roPessoalServidor->obRCGMPessoaFisica->getNumCGM() ) {
            $stFiltro .= " AND servidor.numcgm = ".$this->roRFolhaPagamentoPeriodoContratoServidor->roPessoalServidor->obRCGMPessoaFisica->getNumCGM();
        }
        if ( $this->roRFolhaPagamentoPeriodoContratoServidor->obROrganogramaOrgao->getCodOrgao() ) {
            $stFiltro .= " AND cod_orgao IN (".$this->roRFolhaPagamentoPeriodoContratoServidor->obROrganogramaOrgao->getCodOrgao().")";
        }
        if ( $this->roRFolhaPagamentoPeriodoContratoServidor->obROrganogramaLocal->getCodLocal() ) {
            $stFiltro .= " AND cod_local IN (".$this->roRFolhaPagamentoPeriodoContratoServidor->obROrganogramaLocal->getCodLocal().")";
        }
    }
    if ($this->obRFolhaPagamentoEvento) {
        if ( $this->obRFolhaPagamentoEvento->getTipo() ) {
            $stFiltro .= " AND evento.tipo = '".$this->obRFolhaPagamentoEvento->getTipo()."'";
        }
        if ( $this->obRFolhaPagamentoEvento->getCodEvento() ) {
            $stFiltro .= " AND registro_evento.cod_evento = ".$this->obRFolhaPagamentoEvento->getCodEvento();
        }
    }
    if ( $this->getCodRegistroEvento() ) {
        $stFiltro .= " AND registro_evento.cod_registro = ".$this->getCodRegistroEvento();
    }
    $obErro = $this->listar($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

/**
    * Método listarUltimoRegistroEvento
    * @access Private
*/
function listarUltimoRegistroEvento(&$rsRecordSet,$boTransacao)
{
    $obTFolhaPagamentoUltimoRegistroEvento      = new TFolhaPagamentoUltimoRegistroEvento;
    $stFiltro = "";
    $stOrder  = "";
    if ( $this->getCodRegistroEvento() ) {
        $stFiltro .= " WHERE cod_registro = ".$this->getCodRegistroEvento();
    }
    $obErro = $obTFolhaPagamentoUltimoRegistroEvento->recuperaRelacionamento($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

/**
    * Lista eventos configuracao
    * @access Public
*/
function listarEventosConfiguracao(&$rsLista, $boTransacao="")
{
    $obTFolhaPagamentoRegistroEvento = new TFolhaPagamentoRegistroEvento ;
    $stFiltro = "";
    $stOrder  = "";
    if ( $this->obRFolhaPagamentoEvento->getCodigo() ) {
        $stFiltro .= " AND codigo = '".$this->obRFolhaPagamentoEvento->getCodigo()."'";
    }
    if ( is_object($this->roRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao) ) {
        if ( $this->roRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->getCodSubDivisao() ) {
            $stFiltro .= " AND cod_sub_divisao = ".$this->roRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->getCodSubDivisao();
        }
    }
    if ( is_object($this->roRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo) ) {
        if ( $this->roRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo->getCodCargo() ) {
            $stFiltro .= " AND cargo.cod_cargo = ".$this->roRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo->getCodCargo();
        }
    }
    if ( is_object($this->roRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo->roUltimoEspecialidade) ) {
        if ( $this->roRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo->roUltimoEspecialidade->getCodEspecialidade() ) {
            $stFiltro .= " AND cod_especialidade = ".$this->roRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo->roUltimoEspecialidade->getCodEspecialidade();
        }
    }
    if ( is_object($this->obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento) ) {
        if ( $this->obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->getCodConfiguracao() ) {
            $stFiltro .= " AND configuracao_evento_caso.cod_configuracao = ".$this->obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->getCodConfiguracao();
        }
    }

    $obErro = $obTFolhaPagamentoRegistroEvento->recuperaRelacionamentoConfiguracao( $rsLista, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Método listarRegistroEventoParcela
    * @access Private
*/
function listarRegistroEventoParcela(&$rsRecordSet,$boTransacao="")
{
    $stFiltro = "";
    $stOrder  = "";
    $obTFolhaPagamentoRegistroEventoParcela = new TFolhaPagamentoRegistroEventoParcela;
    if ( $this->getCodRegistroEvento() ) {
        $stFiltro .= " AND cod_registro = ".$this->getCodRegistroEvento();
    }
    if ( $this->getTimestamp() ) {
        $stFiltro .= " AND timestamp = '".$this->getTimestamp()."'";
    }
    $stFiltro = ($stFiltro!="")?" WHERE ".substr($stFiltro,4,strlen($stFiltro)):"";
    $obErro = $obTFolhaPagamentoRegistroEventoParcela->recuperaTodos($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

/**
    * Método listarRegistroEventoPeriodo
    * @access Private
*/
function listarRegistroEventoPeriodo(&$rsRecordSet,$boTransacao="")
{
    $stFiltro = "";
    $stOrder  = "";
    $obTFolhaPagamentoRegistroEventoPeriodo = new TFolhaPagamentoRegistroEventoPeriodo;
    if ( $this->roRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoPeriodoMovimentacao->getCodPeriodoMovimentacao() ) {
        $stFiltro .= " AND cod_periodo_movimentacao = ".$this->roRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoPeriodoMovimentacao->getCodPeriodoMovimentacao();
    }
    if ( $this->roRFolhaPagamentoPeriodoContratoServidor->getCodContrato() ) {
        $stFiltro .= " AND cod_contrato = ".$this->roRFolhaPagamentoPeriodoContratoServidor->getCodContrato();
    }
    $stFiltro = " WHERE ".substr($stFiltro,4,strlen($stFiltro));
    $obErro = $obTFolhaPagamentoRegistroEventoPeriodo->recuperaTodos($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

/**
    * Método listarContratosComRegistroDeEvento
    * @access Public
*/
function listarContratosComRegistroDeEvento(&$rsRecordSet,$inCodPeriodoMovimentacao,$boTransacao="")
{
    $obTFolhaPagamentoRegistroEvento = new TFolhaPagamentoRegistroEvento;
    $obTFolhaPagamentoRegistroEvento->setDado("cod_periodo_movimentacao",$inCodPeriodoMovimentacao);
    $obErro = $obTFolhaPagamentoRegistroEvento->recuperaContratosComRegistroDeEvento($rsRecordSet,$stFiltro,$stOrdem,$boTransacao);

    return $obErro;
}

/**
    * Método listarContratosComRegistroDeEventoPorCgm
    * @access Public
*/
function listarContratosComRegistroDeEventoPorCgm(&$rsRecordSet,$inCgm,$inRegistro,$boTransacao="")
{
    $obTFolhaPagamentoRegistroEvento = new TFolhaPagamentoRegistroEvento;
    $stFiltro  = " WHERE numcgm = ".$inCgm;
    $stFiltro .= "   AND registro != ".$inRegistro;
    $obErro = $obTFolhaPagamentoRegistroEvento->recuperaContratosComRegistroDeEventoPorCgm($rsRecordSet,$stFiltro,$stOrdem,$boTransacao);

    return $obErro;
}

}

?>
