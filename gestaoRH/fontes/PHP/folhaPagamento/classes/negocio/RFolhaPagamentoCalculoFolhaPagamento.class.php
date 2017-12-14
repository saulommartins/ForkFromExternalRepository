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
* Classe de regra de negócio para RFolhaPagamentoCalculoFolhaPagamento
* Data de Criação: 05/12/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @package URBEM
* @subpackage Regra de Negócio

$Revision: 30711 $
$Name$
$Author: rgarbin $
$Date: 2008-01-28 13:18:19 -0200 (Seg, 28 Jan 2008) $

* Casos de uso: uc-04.05.09
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
//include_once (CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                   );

class RFolhaPagamentoCalculoFolhaPagamento
{
/**
   * @access Private
   * @var Object
*/
var $obTransacao;
/**
   * @access Private
   * @var Object
*/
//var $obRFolhaPagamentoPeriodoMovimentacao;
/**
   * @access Private
   * @var Object
*/
var $roRFolhaPagamentoPeriodoMovimentacao;
/**
    * @access Private
    * @var Array
*/
var $arRegistros;
/**
    * @access Private
    * @var String
*/
var $stErro;
/**
    * @access Private
    * @var String
*/
var $stTipoCalculo;
/**
    * @access Private
    * @var Recordset
*/
var $rsContratos;

/**
    * @access Public
    * @param Object $valor
*/
function setTransacao($valor) { $this->obTransacao                           = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
//function setRFolhaPagamentoPeriodoMovimentacao($valor) { $this->roRFolhaPagamentoPeriodoMovimentacao  = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRORFolhaPagamentoPeriodoMovimentacao(&$valor) { $this->roRFolhaPagamentoPeriodoMovimentacao = &$valor; }
/**
    * @access Public
    * @param Array $valor
*/
function setRegistros($valor) { $this->arRegistros                           = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setErro($valor) { $this->stErro                                = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setTipoCalculo($valor) { $this->stTipoCalculo                         = $valor; }
/**
    * @access Public
    * @param Recordset $valor
*/
function setContratos($valor) { $this->rsContratos                           = $valor; }

/**
    * @access Public
    * @return Object
*/
function getTransacao() { return $this->obTransacao;                           }
/**
    * @access Public
    * @return Object
*/
function getRFolhaPagamentoPeriodoMovimentacao() { return $this->roRFolhaPagamentoPeriodoMovimentacao;  }
/**
    * @access Public
    * @return Object
*/
function getRORFolhaPagamentoPeriodoMovimentacao() { return $this->roRFolhaPagamentoPeriodoMovimentacao;  }
/**
    * @access Public
    * @return Array
*/
function getRegistros() { return $this->arRegistros;                           }
/**
    * @access Public
    * @return String
*/
function getErro() { return $this->stErro;                                }
/**
    * @access Public
    * @return String
*/
function getTipoCalculo() { return $this->stTipoCalculo;                         }
/**
    * @access Public
    * @return Recordset
*/
function getContratos() { return $this->rsContratos;                           }

/**
     * Método construtor
     * @access Private
*/
function RFolhaPagamentoCalculoFolhaPagamento()
{
    $this->setTransacao                                  ( new Transacao                          );
//    $this->setRFolhaPagamentoPeriodoMovimentacao         ( new RFolhaPagamentoPeriodoMovimentacao );
}

/**
    * Método calcular
    * @access Private
*/
function calcularFolhaPagamento()
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."FFolhaPagamentoCalculaFolha.class.php"    );
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoLogErroCalculo.class.php"  );
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php"                );
    $obFFolhaPagamentoCalculaFolha    = new FFolhaPagamentoCalculaFolha;
    $obTFolhaPagamentoLogErroCalculo  = new TFolhaPagamentoLogErroCalculo;
    $obTPessoalContrato               = new TPessoalContrato;
    $obErro = new erro;
    if ( !$obErro->ocorreu() ) {
        $boFlagTransacao = false;
        $boTransacao = "";
        $obConexao   = new Conexao;
        $arContratosCalculadosErro = Sessao::read("arContratosCalculadosErro");
        $arContratosCalculadosSucesso = Sessao::read("arContratosCalculadosSucesso");
        while (!$this->rsContratos->eof()) {
            $boFlagTransacao = false;
            $boTransacao = "";
            $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $obFFolhaPagamentoCalculaFolha->setDado('cod_contrato',$this->rsContratos->getCampo("cod_contrato"));
                $obFFolhaPagamentoCalculaFolha->setDado('boErro',$this->getErro());
                $obFFolhaPagamentoCalculaFolha->setDado('cod_entidade',Sessao::getEntidade());
                $obErro = $obFFolhaPagamentoCalculaFolha->calculaFolha($rsCalculaFolha,$boTransacao);
            }
            $arContrato["inContrato"]   = ($this->rsContratos->getCampo("inContrato")!="")?$this->rsContratos->getCampo("inContrato"):$this->rsContratos->getCampo("registro");
            $arContrato["cod_contrato"] = $this->rsContratos->getCampo("cod_contrato");
            $arContrato["numcgm"]       = $this->rsContratos->getCampo("numcgm");
            $arContrato["nom_cgm"]      = $this->rsContratos->getCampo("nom_cgm");
            if ($obErro->ocorreu() OR $rsCalculaFolha->getCampo("retorno") == "f") {
                if ($this->getErro() == "f") {
                    $stFiltro = " AND cod_contrato = ".$this->rsContratos->getCampo("cod_contrato");
                    $obErro = $obTFolhaPagamentoLogErroCalculo->recuperaLogErroCalculo($rsLogErro,$stFiltro,"",$boTransacao);
                    $arContrato["erro"] = $rsLogErro->getCampo("erro");
                    $arContrato["codigo"] = $rsLogErro->getCampo("codigo");
                }
                $arContratosCalculadosErro[] = $arContrato;
            } else {
                $arContratosCalculadosSucesso[] = $arContrato;
            }
            if ($this->getErro() == "t") {
                $stErro = $obErro->getDescricao();
                $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro,$obTFolhaPagamentoEventoCalculado);
                $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $stFiltro = " AND cod_contrato = ".$this->rsContratos->getCampo("cod_contrato");
                    $obErro = $obTFolhaPagamentoLogErroCalculo->recuperaLogErroCalculo($rsLogErro,$stFiltro,"",$boTransacao);
                    if (trim($stErro) != "") {
                        $inIndexErro = count($arContratosCalculadosErro)-1;
                        $arContratosCalculadosErro[$inIndexErro]["erro"] = $stErro;
                        $arContratosCalculadosErro[$inIndexErro]["codigo"] = $rsLogErro->getCampo("codigo");
                    }
                }
                if ( !$obErro->ocorreu() ) {
                    if ($stErro != "") {
                        $obTFolhaPagamentoLogErroCalculo->setDado("cod_registro",$rsLogErro->getCampo("cod_registro"));
                        $obTFolhaPagamentoLogErroCalculo->setDado("cod_evento",$rsLogErro->getCampo("cod_evento"));
                        $obTFolhaPagamentoLogErroCalculo->setDado("timestamp",$rsLogErro->getCampo("timestamp"));
                        $obTFolhaPagamentoLogErroCalculo->setDado("erro",$stErro);
                        $obErro = $obTFolhaPagamentoLogErroCalculo->alteracao($boTransacao);
                    } else {
                        $obTFolhaPagamentoLogErroCalculo->setDado("cod_registro",$rsLogErro->getCampo("cod_registro"));
                        $obTFolhaPagamentoLogErroCalculo->setDado("cod_evento",$rsLogErro->getCampo("cod_evento"));
                        $obTFolhaPagamentoLogErroCalculo->setDado("timestamp",$rsLogErro->getCampo("timestamp"));
                        $obErro = $obTFolhaPagamentoLogErroCalculo->exclusao($boTransacao);
                    }
                }
            }
            $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro,$obTFolhaPagamentoEventoCalculado);
            $this->rsContratos->proximo();
        }
        Sessao::write("arContratosCalculadosErro",$arContratosCalculadosErro);
        Sessao::write("arContratosCalculadosSucesso",$arContratosCalculadosSucesso);
        if ( count($arErro) ) {
            $obErro->setDescricao('Um ou mais contratos obtiveram algum erro durante o seu calculo.');
        }
    }

    return $obErro;
}

/**
    * Método excluirLogErroCalculo
    * @access Priveta
*/
function excluirLogErroCalculo($inCodEvento,$inCodRegistro,$stTimestamp,$boTransacao="")
{
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoLogErroCalculo.class.php"  );
        $obTFolhaPagamentoLogErroCalculo  = new TFolhaPagamentoLogErroCalculo;
        $obTFolhaPagamentoLogErroCalculo->setDado('cod_registro',$inCodRegistro);
        $obTFolhaPagamentoLogErroCalculo->setDado('cod_evento'  ,$inCodEvento  );
        $obTFolhaPagamentoLogErroCalculo->setDado('timestamp'   ,$stTimestamp  );
        $obErro = $obTFolhaPagamentoLogErroCalculo->exclusao($boTransacao);
    }
    if ($obErro->ocorreu()) {
        return $obErro;    
    }else{
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTFolhaPagamentoLogErroCalculo);
        return $obErro;
    }
}

/**
    * Método excluirEventoCalculado
    * @access Priveta
*/
function excluirEventoCalculado($inCodEvento,$inCodRegistro,$stTimestamp,$boTransacao="")
{    
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculado.class.php"  );
        $obTFolhaPagamentoEventoCalculado  = new TFolhaPagamentoEventoCalculado;
        $obTFolhaPagamentoEventoCalculado->setDado('cod_evento'                 ,$inCodEvento       );
        $obTFolhaPagamentoEventoCalculado->setDado('cod_registro'               ,$inCodRegistro     );
        $obTFolhaPagamentoEventoCalculado->setDado('timestamp_registro'         ,$stTimestamp       );
        $obErro = $obTFolhaPagamentoEventoCalculado->exclusao($boTransacao);
    }
    if ($obErro->ocorreu()) {
        return $obErro;    
    }else{
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTFolhaPagamentoEventoCalculado);
        return $obErro;
    }

}

/**
    * Método alterarLogErroCalculo
    * @access Priveta
*/
function alterarLogErroCalculo($boTransacao="")
{
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoLogErroCalculo.class.php"  );
        $obTFolhaPagamentoLogErroCalculo  = new TFolhaPagamentoLogErroCalculo;
        $obRFolhaPagamentoPeriodoContratoServidor = $this->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor;
        $obRFolhaPagamentoPeriodoContratoServidor->consultarContrato($boTransacao);
        $this->roRFolhaPagamentoPeriodoMovimentacao->listarUltimaMovimentacao($rsUltimaMovimentacao,$boTransacao);
        $inCodContrato              = $obRFolhaPagamentoPeriodoContratoServidor->getCodContrato();
        $inCodPeriodoMovimentacao   = $rsUltimaMovimentacao->getCampo('cod_periodo_movimentacao');
        $obTFolhaPagamentoLogErroCalculo->setDado('cod_contrato'            ,$inCodContrato             );
        $obTFolhaPagamentoLogErroCalculo->setDado('cod_periodo_movimentacao',$inCodPeriodoMovimentacao  );
        $obTFolhaPagamentoLogErroCalculo->setDado('erro'                    ,$this->getErro()           );
        $obErro = $obTFolhaPagamentoLogErroCalculo->alteracao($boTransacao);
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTFolhaPagamentoLogErroCalculo);

    return $obErro;
}

/**
    * Método listar
    * @access Private
*/
function listar(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculado.class.php");
    $obTFolhaPagamentoEventoCalculado = new TFolhaPagamentoEventoCalculado;
    $obErro = $obTFolhaPagamentoEventoCalculado->recuperaRelacionamento($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

/**
    * Método listarContratosCalculados
    * @access Private
*/
function listarContratosCalculados(&$rsRecordSet,$arRegistros="",$boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculado.class.php");
    $obTFolhaPagamentoEventoCalculado = new TFolhaPagamentoEventoCalculado;
    if ( is_array($arRegistros) ) {
        $stRegistro = "";
        foreach ($arRegistros as $inRegistro) {
            $stRegistro .= $inRegistro.",";
        }
        $stRegistro = substr($stRegistro,0,strlen($stRegistro)-1);
        $stFiltro .= " AND registro IN (".$stRegistro.")";
    }
    if ( is_object($this->roRFolhaPagamentoPeriodoMovimentacao) ) {
        if ( $this->roRFolhaPagamentoPeriodoMovimentacao->getCodPeriodoMovimentacao() ) {
            $stFiltro .= " AND cod_periodo_movimentacao = ".$this->roRFolhaPagamentoPeriodoMovimentacao->getCodPeriodoMovimentacao();
        }
    }
    $stFiltro = " WHERE ".substr($stFiltro,4,strlen($stFiltro));
    $obErro = $obTFolhaPagamentoEventoCalculado->recuperaContratosCalculados($rsRecordSet,$stFiltro,$stOrdem,$boTransacao);

    return $obErro;
}
/**
    * Método listarEventoCalculado
    * @access Private
*/
function listarEventoCalculado(&$rsRecordSet,$boTransacao="",$inCodEvento="")
{
    $stFiltro = "";
    $stOrder  = "nom_cgm";
    if ( is_object($this->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor) ) {
        if ( $this->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->getRegistro() ) {
            $stFiltro .= " AND registro = " . $this->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->getRegistro();
        }
        if ( $this->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->getCodContrato() ) {
            $stFiltro .= " AND cod_contrato = " . $this->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->getCodContrato();
        }
    }
    if ( $this->getRegistros() ) {
        $stRegistro = "";
        foreach ( $this->getRegistros() as $inRegistro ) {
            $stRegistro .= $inRegistro.",";
        }
        $stRegistro = substr($stRegistro,0,strlen($stRegistro)-1);
        $stFiltro .= " AND registro IN (".$stRegistro.")";
    }
    if ( is_object($this->roRFolhaPagamentoPeriodoMovimentacao) ) {
        if ( $this->roRFolhaPagamentoPeriodoMovimentacao->getCodPeriodoMovimentacao() ) {
            $stFiltro .= " AND cod_periodo_movimentacao = ".$this->roRFolhaPagamentoPeriodoMovimentacao->getCodPeriodoMovimentacao();
        }
    }
    $stFiltro .= ( $inCodEvento != "" ) ? " AND cod_evento = ".$inCodEvento : "";
    if ($stFiltro != "") {
        $stFiltro = " WHERE " . substr($stFiltro,4,strlen($stFiltro));
    }
    $obErro = $this->listar($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

/**
    * Método listarLogErroCalculo
    * @access Private
*/
function listarLogErroCalculo(&$rsRecordSet,$boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoLogErroCalculo.class.php");
    $obTFolhaPagamentoLogErroCalculo = new TFolhaPagamentoLogErroCalculo;
    $stFiltro = "";
    $stOrder  = "";
    if ( is_object($this->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor) ) {
        if ( $this->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->getCodContrato() ) {
            $stFiltro .= " AND contrato.cod_contrato IN (" . $this->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->getCodContrato() .")";
        }
        if ( $this->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->getRegistro() ) {
            $stFiltro .= " AND contrato.registro IN (".$this->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->getRegistro().")";
        }
    }
    if ( is_object($this->roRFolhaPagamentoPeriodoMovimentacao) ) {
        if ( $this->roRFolhaPagamentoPeriodoMovimentacao->getCodPeriodoMovimentacao() ) {
            $stFiltro .= " AND contrato_servidor_periodo.cod_periodo_movimentacao = ".$this->roRFolhaPagamentoPeriodoMovimentacao->getCodPeriodoMovimentacao();
        }
    }
    $obErro = $obTFolhaPagamentoLogErroCalculo->recuperaRelacionamento($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

/**
    * Método listarEventoRelatorioFichaFinanceira
    * @access Private
*/
function listarEventoRelatorioFichaFinanceira(&$rsRecordSet,$stOrder,$boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculado.class.php");
    $obTFolhaPagamentoEventoCalculado = new TFolhaPagamentoEventoCalculado;
    if ($stOrder != "") {
        $stOrder = " registro_evento_periodo.cod_contrato,".$stOrder;
    }
    if ( is_object($this->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento) ) {
        if ( $this->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->getCodConfiguracao() ) {
            $stFiltro .= " AND evento_configuracao_evento.cod_configuracao = ".$this->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->getCodConfiguracao();
        }
    }
    if ( is_object($this->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor) ) {
        if ( $this->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->getRegistro() ) {
            $stFiltro .= " AND registro = ".$this->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->getRegistro();
        }
    }

    if ( is_object($this->roRFolhaPagamentoPeriodoMovimentacao) ) {
        $inCodPeriodoMovimentacao = $this->roRFolhaPagamentoPeriodoMovimentacao->getCodPeriodoMovimentacao();
        if (trim($inCodPeriodoMovimentacao)!="") {
            $stFiltro .= " AND contrato_servidor_periodo.cod_periodo_movimentacao = ".$inCodPeriodoMovimentacao;
        } else {
            if ( $this->roRFolhaPagamentoPeriodoMovimentacao->getDtFinal() ) {
                $stFiltro .= " AND to_char(dt_final,'mm/yyyy') = '".$this->roRFolhaPagamentoPeriodoMovimentacao->getDtFinal()."'";
            }
        }
    }
    if ( is_object($this->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo) ) {
        if ( is_object($this->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo->roUltimoEspecialidade) ) {
            if ( $this->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo->roUltimoEspecialidade->getCodEspecialidade() ) {
                $stFiltro .= " AND cod_especialidade = ".$this->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo->roUltimoEspecialidade->getCodEspecialidade();
            }
        } else {
            if ( $this->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo->getCodCargo() ) {
                $stFiltro .= " AND contrato_servidor_funcao.cod_cargo = ".$this->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo->getCodCargo();
            }
        }
    }
    if ( is_object( $this->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor ) ) {
        if ( $this->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->obROrganogramaOrgao->getCodOrgao() ) {
            $stFiltro .= " AND cod_orgao IN (".$this->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->obROrganogramaOrgao->getCodOrgao().")";
        }
        if ($this->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->obROrganogramaLocal->getCodLocal()) {
            $stFiltro .= " AND cod_local IN (".$this->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->obROrganogramaLocal->getCodLocal().")";
        }
    }
    $obErro = $obTFolhaPagamentoEventoCalculado->recuperaRelatorioFichaFinanceira($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

/**
    * Método listarEventosBaseRelatorioFichaFinanceira
    * @access Private
*/
function listarEventosBaseDescontoRelatorioFichaFinanceira(&$rsRecordSet,$inNumCgm,$stNatureza,$inCodPeriodoMovimentacao,$inCodPrevidencia,$boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculado.class.php");
    $obTFolhaPagamentoEventoCalculado = new TFolhaPagamentoEventoCalculado;
    $stFiltro .= " WHERE numcgm = ".$inNumCgm;
    $stFiltro .= " AND natureza = '".$stNatureza."'";
    $stFiltro .= " AND cod_periodo_movimentacao = ".$inCodPeriodoMovimentacao;
    $stFiltro .= " AND ((desdobramento ='A' or desdobramento ='F' or desdobramento IS NULL) OR (desdobramento = 'D' AND cod_tipo = 7))";
    $stOrdem   = " registro";
    $obTFolhaPagamentoEventoCalculado->setDado("cod_previdencia",$inCodPrevidencia);
    $obErro = $obTFolhaPagamentoEventoCalculado->recuperaEventosBaseDescontoRelatorioFichaFinanceira($rsRecordSet,$stFiltro,$stOrdem,$boTransacao);

    return $obErro;
}

/**
    * Método listarEventosCalculadoParaExclusão
    * @access Private
*/
function listarEventosCalculadosParaExclusao(&$rsRecordSet,$boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculado.class.php");
    $obTFolhaPagamentoEventoCalculado = new TFolhaPagamentoEventoCalculado;
    if ( is_object($this->roRFolhaPagamentoPeriodoMovimentacao) ) {
        if ( $this->roRFolhaPagamentoPeriodoMovimentacao->getCodPeriodoMovimentacao() ) {
            $stFiltro .= " AND cod_periodo_movimentacao = ".$this->roRFolhaPagamentoPeriodoMovimentacao->getCodPeriodoMovimentacao();
        }
    }
    if ( is_object($this->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor) ) {
        if ( $this->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->getCodContrato() ) {
            $stFiltro .= " AND cod_contrato = " . $this->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->getCodContrato();
        }
    }

    $obErro = $obTFolhaPagamentoEventoCalculado->recuperaEventosCalculadosParaExclusao($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

function listarRelatorioFolhaAnaliticaSintetica(&$rsRecordSet,$arFiltros="",$stOrdem="",$boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculado.class.php");
    $obTFolhaPagamentoEventoCalculado = new TFolhaPagamentoEventoCalculado;
    if ( isset($arFiltros['cod_periodo_movimentacao']) ) {
        $stFiltro .= " AND contrato_servidor_periodo.cod_periodo_movimentacao = ".$arFiltros['cod_periodo_movimentacao'];
    }
    if ( isset($arFiltros['boAtivo']) ) {
        $stFiltro .= " AND ativo = true";
    }
    if ( isset($arFiltros['boInativo']) ) {
        $stFiltro .= " AND ativo = false";
    }
    //if ( isset($arFiltros['boPensionista']) ) {
    //    $stFiltro .= " AND ativo = true";
    //}
    if ( is_array($arFiltros['arRegistros']) ) {
        $stRegistros = "";
        foreach ($arFiltros['arRegistros'] as $inRegistros) {
            $stRegistros .= $inRegistros.",";
        }
        $stRegistros = substr($stRegistros,0,strlen($stRegistros)-1);
        $stFiltro .= " AND registro IN (".$stRegistros.")";
    }
    if ( is_array($arFiltros['arEspecialidades']) ) {
        $stEspecialidades = "";
        foreach ($arFiltros['arEspecialidades'] as $inEspecialidade) {
            $stEspecialidades .= $inEspecialidade.",";
        }
        $stEspecialidades = substr($stEspecialidades,0,strlen($stEspecialidades)-1);
        $stFiltro .= " AND contrato_servidor_especialidade_cargo.cod_especialidade IN (".$stEspecialidades.")";
    }
    if ( is_array($arFiltros['arCargos']) ) {
        $stCargos = "";
        foreach ($arFiltros['arCargos'] as $inCargo) {
            $stCargos .= $inCargo.",";
        }
        $stCargos = substr($stCargos,0,strlen($stCargos)-1);
        $stFiltro .= " AND contrato_servidor_especialidade_cargo.cod_cargo IN (".$stCargos.")";
    }
    if ( is_array($arFiltros['arFuncoes']) ) {
        $stCargos = "";
        foreach ($arFiltros['arFuncoes'] as $inCargo) {
            $stCargos .= $inCargo.",";
        }
        $stCargos = substr($stCargos,0,strlen($stCargos)-1);
        $stFiltro .= " AND contrato_servidor_especialidade_funcao.cod_funcao IN (".$stCargos.")";
    }
    if ( is_array($arFiltros['arEspecialidadesFunc']) ) {
        $stEspecialidades = "";
        foreach ($arFiltros['arEspecialidadesFunc'] as $inEspecialidade) {
            $stEspecialidades .= $inEspecialidade.",";
        }
        $stEspecialidades = substr($stEspecialidades,0,strlen($stEspecialidades)-1);
        $stFiltro .= " AND contrato_servidor_especialidade_funcao.cod_especialidade_funcao IN (".$stEspecialidades.")";
    }
    if ( is_array($arFiltros['arPadrao']) ) {
        $stPadrao = "";
        foreach ($arFiltros['arPadrao'] as $inPadrao) {
            $stPadrao .= $inPadrao.",";
        }
        $stPadrao = substr($stPadrao,0,strlen($stPadrao)-1);
        $stFiltro .= " AND cod_padrao IN (".$stPadrao.")";
    }
    if ( is_array($arFiltros['arLotacao']) ) {
        $stLotacao = "";
        foreach ($arFiltros['arLotacao'] as $inLotacao) {
            $stLotacao .= $inLotacao.",";
        }
        $stLotacao = substr($stLotacao,0,strlen($stLotacao)-1);
        $stFiltro .= " AND cod_orgao IN (".$stLotacao.")";
    }
    if ( is_array($arFiltros['arLocal']) ) {
        $stLocal = "";
        foreach ($arFiltros['arLocal'] as $inLocal) {
            $stLocal .= $inLocal.",";
        }
        $stLocal = substr($stLocal,0,strlen($stLocal)-1);
        $stFiltro .= " AND cod_local IN (".$stLocal.")";
    }
    $stOrdem .= " contrato.cod_contrato";
    $obErro = $obTFolhaPagamentoEventoCalculado->recuperaRelatorioFolhaAnaliticaSintetica($rsRecordSet,$stFiltro,$stOrdem,$boTransacao);

    return $obErro;
}

function listarRelatorioFolhaAnalitica(&$rsRecordSet,$inCodContrato,$boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculado.class.php");
    $obTFolhaPagamentoEventoCalculado = new TFolhaPagamentoEventoCalculado;
    $stFiltro = " AND contrato.cod_contrato = ".$inCodContrato;
    $obErro = $obTFolhaPagamentoEventoCalculado->recuperaRelatorioFolhaAnalitica($rsRecordSet,$stFiltro,$stOrdem,$boTransacao);

    return $obErro;
}

function listarRelatorioFolhaAnaliticaEventoPrevidencia(&$rsRecordSet,$inCodContrato,$inCodEvento,$boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculado.class.php");
    $obTFolhaPagamentoEventoCalculado = new TFolhaPagamentoEventoCalculado;
    $stFiltro  = " AND contrato_servidor_previdencia.cod_contrato = ".$inCodContrato;
    $stFiltro .= " AND previdencia_evento.cod_evento = ".$inCodEvento;
    $obErro = $obTFolhaPagamentoEventoCalculado->recuperaRelatorioFolhaAnaliticaEventoPrevidencia($rsRecordSet,$stFiltro,$stOrdem,$boTransacao);

    return $obErro;
}

function listarRelatorioFolhaAnaliticaEventoIRRF(&$rsRecordSet,$inCodEvento,$inCodTipo,$boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculado.class.php");
    $obTFolhaPagamentoEventoCalculado = new TFolhaPagamentoEventoCalculado;
    $stFiltro  = " AND cod_tipo = ".$inCodTipo;
    $stFiltro .= " AND cod_evento = ".$inCodEvento;
    $obErro = $obTFolhaPagamentoEventoCalculado->recuperaRelatorioFolhaAnaliticaEventoIRRF($rsRecordSet,$stFiltro,$stOrdem,$boTransacao);

    return $obErro;
}

function listarRelatorioFolhaAnaliticaEventoFGTS(&$rsRecordSet,$inCodEvento,$inCodTipo,$boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculado.class.php");
    $obTFolhaPagamentoEventoCalculado = new TFolhaPagamentoEventoCalculado;
    $stFiltro  = " AND cod_tipo = ".$inCodTipo;
    $stFiltro .= " AND cod_evento = ".$inCodEvento;
    $obErro = $obTFolhaPagamentoEventoCalculado->recuperaRelatorioFolhaAnaliticaEventoFGTS($rsRecordSet,$stFiltro,$stOrdem,$boTransacao);

    return $obErro;
}

function listarRelatorioFolhaAnaliticaOutrasFolhas(&$rsRecordSet,$inCodContrato,$inCodPeriodoMovimentacao,$boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoComplementarCalculado.class.php");
    $obTFolhaPagamentoEventoComplementarCalculado = new TFolhaPagamentoEventoComplementarCalculado;
    $stFiltro   = " AND contrato_servidor_complementar.cod_contrato = ".$inCodContrato;
    $stFiltro  .= " AND contrato_servidor_complementar.cod_periodo_movimentacao = ".$inCodPeriodoMovimentacao;
    $obErro = $obTFolhaPagamentoEventoComplementarCalculado->recuperaRelatorioFolhaAnaliticaOutrasFolhas($rsRecordSet,$stFiltro,$stOrdem,$boTransacao);

    return $obErro;
}

}
?>
