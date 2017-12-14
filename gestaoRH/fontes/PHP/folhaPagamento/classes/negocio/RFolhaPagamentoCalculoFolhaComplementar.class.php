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
    * Classe de regra de negócio para RFolhaPagamentoCalculoFolhaComplementar
    * Data de Criação: 24/01/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Regra de Negócio

    $Revision: 30711 $
    $Name$
    $Author: souzadl $
    $Date: 2008-04-02 09:44:37 -0300 (Qua, 02 Abr 2008) $

    * Casos de uso: uc-04.05.10
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class RFolhaPagamentoCalculoFolhaComplementar
{
/**
   * @access Private
   * @var Object
*/
var $obTransacao;
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
   * @var Array
*/
var $arRegistros;
/**
   * @access Private
   * @var Object
*/
var $roRFolhaPagamentoFolhaComplementar;
/**
    * @access Private
    * @var Recordset
*/
var $rsContratos;

/**
    * @access Public
    * @param Object $valor
*/
function setTransacao($valor) { $this->obTransacao    = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setErro($valor) { $this->stErro         = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setTipoCalculo($valor) { $this->stTipoCalculo  = $valor; }
/**
    * @access Public
    * @param Array $valor
*/
function setRegistros($valor) { $this->arRegistros    = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRORFolhaPagamentoFolhaComplementar(&$valor) { $this->roRFolhaPagamentoFolhaComplementar  = &$valor; }
/**
    * @access Public
    * @param Recordset $valor
*/
function setContratos($valor) { $this->rsContratos                           = $valor; }

/**
    * @access Public
    * @return Object
*/
function getTransacao() { return $this->obTransacao;    }
/**
    * @access Public
    * @return String
*/
function getErro() { return $this->stErro;         }
/**
    * @access Public
    * @return String
*/
function getTipoCalculo() { return $this->stTipoCalculo;  }
/**
    * @access Public
    * @return Array
*/
function getRegistros() { return $this->arRegistros;    }
/**
    * @access Public
    * @return Object
*/
function getRORFolhaPagamentoFolhaComplementar() { return $this->roRFolhaPagamentoFolhaComplementar;  }
/**
    * @access Public
    * @return Recordset
*/
function getContratos() { return $this->rsContratos;                           }

/**
     * Método construtor
     * @access Private
*/
function RFolhaPagamentoCalculoFolhaComplementar(&$obRFolhaPagamentoFolhaComplementar)
{
    $this->setTransacao                                ( new Transacao                          );
    $this->setRORFolhaPagamentoFolhaComplementar       ( $obRFolhaPagamentoFolhaComplementar    );
}

/**
    * calcularFolhaComplementar
    * @access Public
*/
function calcularFolhaComplementar()
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoComplementarCalculado.class.php");
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoLogErroCalculoComplementar.class.php");
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoComplementarCalculado.class.php");
    include_once(CAM_GRH_FOL_MAPEAMENTO."FFolhaPagamentoCalculaFolhaComplementar.class.php");
    include_once(CAM_GRH_FOL_MAPEAMENTO."FFolhaPagamentoDeletarInformacoesCalculo.class.php" );
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php"                );
    $obTFolhaPagamentoEventoComplementarCalculado = new TFolhaPagamentoEventoComplementarCalculado;
    $obTFolhaPagamentoLogErroCalculoComplementar  = new TFolhaPagamentoLogErroCalculoComplementar;
    $obTFolhaPagamentoEventoComplementarCalculado = new TFolhaPagamentoEventoComplementarCalculado;
    $obFFolhaPagamentoCalculaFolhaComplementar    = new FFolhaPagamentoCalculaFolhaComplementar;
    $obFFolhaPagamentoDeletarInformacoesCalculo = new FFolhaPagamentoDeletarInformacoesCalculo;
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
                //$obFFolhaPagamentoCalculaFolhaComplementar->setDado('cod_contrato',$this->rsContratos->getCampo("cod_contrato"));
                //$obFFolhaPagamentoCalculaFolhaComplementar->setDado('boErro',$this->getErro());
                //$obFFolhaPagamentoCalculaFolhaComplementar->setDado('cod_entidade',Sessao::getEntidade());
                //$obErro = $obFFolhaPagamentoCalculaFolhaComplementar->calculaFolhaComplementar($rsCalculaFolha,$boTransacao);

                $obFFolhaPagamentoCalculaFolhaComplementar->setDado('inCodContrato'       ,$this->rsContratos->getCampo("cod_contrato"));
                $obFFolhaPagamentoCalculaFolhaComplementar->setDado('inCodComplementar'   ,$this->roRFolhaPagamentoFolhaComplementar->getCodComplementar());
                $obFFolhaPagamentoCalculaFolhaComplementar->setDado('boErro',$this->getErro());
                $obErro = $obFFolhaPagamentoCalculaFolhaComplementar->calculaFolhaComplementar($rsCalculaFolha,$boTransacao);
            }
            $arContrato["inContrato"]   = ($this->rsContratos->getCampo("inContrato")!="")?$this->rsContratos->getCampo("inContrato"):$this->rsContratos->getCampo("registro");
            $arContrato["cod_contrato"] = $this->rsContratos->getCampo("cod_contrato");
            $arContrato["numcgm"]       = $this->rsContratos->getCampo("numcgm");
            $arContrato["nom_cgm"]      = $this->rsContratos->getCampo("nom_cgm");
            if ($obErro->ocorreu() OR $rsCalculaFolha->getCampo("retorno") == "f") {
                if ($this->getErro() == "f") {
                    $stFiltro  = " AND cod_contrato = ".$this->rsContratos->getCampo("cod_contrato");
                    $stFiltro .= " AND cod_complementar = ".$this->roRFolhaPagamentoFolhaComplementar->getCodComplementar();
                    $obErro = $obTFolhaPagamentoLogErroCalculoComplementar->recuperaLogErroCalculadoComplementar($rsLogErro,$stFiltro,"",$boTransacao);
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
                    $stFiltro  = " AND cod_contrato = ".$this->rsContratos->getCampo("cod_contrato");
                    $stFiltro .= " AND cod_complementar = ".$this->roRFolhaPagamentoFolhaComplementar->getCodComplementar();
                    $obErro = $obTFolhaPagamentoLogErroCalculoComplementar->recuperaLogErroCalculadoComplementar($rsLogErro,$stFiltro,"",$boTransacao);
                    if (trim($stErro) != "") {
                        $inIndexErro = count($arContratosCalculadosErro)-1;
                        $arContratosCalculadosErro[$inIndexErro]["erro"] = $stErro;
                        $arContratosCalculadosErro[$inIndexErro]["codigo"] = $rsLogErro->getCampo("codigo");
                    }
                }
                if ( !$obErro->ocorreu() ) {
                    if ($stErro != "") {
                        $obTFolhaPagamentoLogErroCalculoComplementar->setDado("cod_registro",$rsLogErro->getCampo("cod_registro"));
                        $obTFolhaPagamentoLogErroCalculoComplementar->setDado("cod_evento",$rsLogErro->getCampo("cod_evento"));
                        $obTFolhaPagamentoLogErroCalculoComplementar->setDado("timestamp",$rsLogErro->getCampo("timestamp"));
                        $obTFolhaPagamentoLogErroCalculoComplementar->setDado("cod_configuracao",$rsLogErro->getCampo("cod_configuracao"));
                        $obTFolhaPagamentoLogErroCalculoComplementar->setDado("erro",$stErro);
                        $obErro = $obTFolhaPagamentoLogErroCalculoComplementar->alteracao($boTransacao);
                    } else {
                        $obTFolhaPagamentoLogErroCalculoComplementar->setDado("cod_registro",$rsLogErro->getCampo("cod_registro"));
                        $obTFolhaPagamentoLogErroCalculoComplementar->setDado("cod_evento",$rsLogErro->getCampo("cod_evento"));
                        $obTFolhaPagamentoLogErroCalculoComplementar->setDado("timestamp",$rsLogErro->getCampo("timestamp"));
                        $obTFolhaPagamentoLogErroCalculoComplementar->setDado("cod_configuracao",$rsLogErro->getCampo("cod_configuracao"));
                        $obErro = $obTFolhaPagamentoLogErroCalculoComplementar->exclusao($boTransacao);
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

/*
function calcularFolhaComplementar($boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoComplementarCalculado.class.php");
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoLogErroCalculoComplementar.class.php");
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoComplementarCalculado.class.php");
    include_once(CAM_GRH_FOL_MAPEAMENTO."FFolhaPagamentoCalculaFolhaComplementar.class.php");
    include_once(CAM_GRH_FOL_MAPEAMENTO."FFolhaPagamentoDeletarInformacoesCalculo.class.php" );
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php"                );
    $obTFolhaPagamentoEventoComplementarCalculado = new TFolhaPagamentoEventoComplementarCalculado;
    $obTFolhaPagamentoLogErroCalculoComplementar  = new TFolhaPagamentoLogErroCalculoComplementar;
    $obTFolhaPagamentoEventoComplementarCalculado = new TFolhaPagamentoEventoComplementarCalculado;
    $obFFolhaPagamentoCalculaFolhaComplementar    = new FFolhaPagamentoCalculaFolhaComplementar;
    $obFFolhaPagamentoDeletarInformacoesCalculo = new FFolhaPagamentoDeletarInformacoesCalculo;
    $obTPessoalContrato               = new TPessoalContrato;
    $obErro  = new erro;
    if (count($this->roRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoPeriodoMovimentacao->arRFolhaPagamentoPeriodoContratoServidor) === 0) {
        $obErro->setDescricao("Não há contratos a serem calculados.");
    }
    if (!$obErro->ocorreu()) {
        for ($inIndex = 0; $inIndex < count($this->roRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoPeriodoMovimentacao->arRFolhaPagamentoPeriodoContratoServidor); $inIndex++ ) {
            $obRFolhaPagamentoPeriodoContratoServidor = &$this->roRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoPeriodoMovimentacao->arRFolhaPagamentoPeriodoContratoServidor[$inIndex];
            //$stFiltro = " WHERE registro = ".$obRFolhaPagamentoPeriodoContratoServidor->getRegistro();
            //$obErro = $obTPessoalContrato->recuperaTodos($rsContrato,$stFiltro,"",$boTransacao);
            //$stCodContratos .= $rsContrato->getCampo("cod_contrato").",";
            $stCodContratos .= $obRFolhaPagamentoPeriodoContratoServidor->getCodContrato();
            if ($obErro->ocorreu()) {
                break;
            }
        }
        $stCodContratos = substr($stCodContratos,0,strlen($stCodContratos)-1);
        $obFFolhaPagamentoDeletarInformacoesCalculo->setDado("stTipoFolha"          ,"C"            );
        $obFFolhaPagamentoDeletarInformacoesCalculo->setDado("inCodComplementar"    ,$obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoFolhaComplementar->getCodComplementar());
        $obFFolhaPagamentoDeletarInformacoesCalculo->setDado("stCodContratos"       ,$stCodContratos);
        $obErro = $obFFolhaPagamentoDeletarInformacoesCalculo->deletarInformacoesCalculo($rsDeletar,$boTransacao);
    }
    if (!$obErro->ocorreu()) {
        $boFlagTransacao = false;
        $boTransacao = "";
        for ($inIndex = 0; $inIndex < count($this->roRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoPeriodoMovimentacao->arRFolhaPagamentoPeriodoContratoServidor); $inIndex++ ) {
            $boFlagTransacao = false;
            $boTransacao = "";
            $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $obRFolhaPagamentoPeriodoContratoServidor = &$this->roRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoPeriodoMovimentacao->arRFolhaPagamentoPeriodoContratoServidor[$inIndex];
                $obErro = $obRFolhaPagamentoPeriodoContratoServidor->consultarContrato($boTransacao);
            }
            if ( !$obErro->ocorreu() ) {
                $obFFolhaPagamentoCalculaFolhaComplementar->setDado('inCodContrato'       ,$obRFolhaPagamentoPeriodoContratoServidor->getCodContrato());
                $obFFolhaPagamentoCalculaFolhaComplementar->setDado('inCodComplementar',$obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoFolhaComplementar->getCodComplementar());
                $obFFolhaPagamentoCalculaFolhaComplementar->setDado('boErro','f');
                $obErro = $obFFolhaPagamentoCalculaFolhaComplementar->calculaFolhaComplementar($rsCalculaFolha,$boTransacao);
            }
            if ( !$obErro->ocorreu() and $rsCalculaFolha->getCampo('retorno') == 'f' and $this->getTipoCalculo() == 'padrao' ) {
                $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );

                $boFlagTransacao = false;
                $boTransacao = "";
                $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao,  $boTransacao);
                $obFFolhaPagamentoCalculaFolhaComplementar->setDado('boErro','t');
                $obErro = $obFFolhaPagamentoCalculaFolhaComplementar->calculaFolhaComplementar($rsCalculaFolha,$boTransacao);
                $this->setErro( $obErro->getDescricao() );
                $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );

                $boFlagTransacao = false;
                $boTransacao = "";
                $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao,  $boTransacao);
                $obTFolhaPagamentoLogErroCalculoComplementar = new TFolhaPagamentoLogErroCalculoComplementar;
                $stFiltro = " AND cod_contrato = ".$obRFolhaPagamentoPeriodoContratoServidor->getCodContrato();
                $obErro = $obTFolhaPagamentoLogErroCalculoComplementar->recuperaLogErroCalculadoComplementar($rsLogErro,$stFiltro,"",$boTransacao);
                $obTFolhaPagamentoLogErroCalculoComplementar->setDado('cod_evento',$rsLogErro->getCampo('cod_evento'));
                $obTFolhaPagamentoLogErroCalculoComplementar->setDado('cod_registro',$rsLogErro->getCampo('cod_registro'));
                $obTFolhaPagamentoLogErroCalculoComplementar->setDado('cod_configuracao',$rsLogErro->getCampo('cod_configuracao'));
                $obTFolhaPagamentoLogErroCalculoComplementar->setDado('erro',$this->getErro());
                $obErro = $obTFolhaPagamentoLogErroCalculoComplementar->alteracao($boTransacao);
            }
            $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro,$obTFolhaPagamentoEventoCalculado);
        }
        if ( count($arErro) ) {
            $obErro->setDescricao('Um ou mais contratos obtiveram algum erro durante o seu calculo.');
        }

    }

    return $obErro;
}
*/
/**
    * alterarLogErroCalculadoComplementar
    * @access Public
*/
function alterarLogErroCalculadoComplementar($boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."T.class.php");
    $obT = new T;
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if (!$obErro->ocorreu()) {

    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obT );

    return $obErro;
}

/**
    * excluirLogErroCalculadoComplementar
    * @access Public
*/
function excluirLogErroCalculadoComplementar($boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoLogErroCalculoComplementar.class.php");
    $obTFolhaPagamentoLogErroCalculoComplementar = new TFolhaPagamentoLogErroCalculoComplementar;
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if (!$obErro->ocorreu()) {
        $obTFolhaPagamentoLogErroCalculoComplementar->setDado("cod_registro", $this->roRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoRegistroEventoComplementar->getCodRegistro());
        $obErro = $obTFolhaPagamentoLogErroCalculoComplementar->exclusao($boTransacao);
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTFolhaPagamentoLogErroCalculoComplementar );

    return $obErro;
}

/**
    * excluirEventoComplementarCalculado
    * @access Public
*/
function excluirEventoComplementarCalculado($boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoComplementarCalculado.class.php");
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoComplementarCalculadoDependente.class.php");
    $obTFolhaPagamentoEventoComplementarCalculado = new TFolhaPagamentoEventoComplementarCalculado;
    $obTFolhaPagamentoEventoComplementarCalculadoDependente = new TFolhaPagamentoEventoComplementarCalculadoDependente;
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if (!$obErro->ocorreu()) {
        $obTFolhaPagamentoEventoComplementarCalculadoDependente->setDado("cod_registro", $this->roRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoRegistroEventoComplementar->getCodRegistro());
        $obErro = $obTFolhaPagamentoEventoComplementarCalculadoDependente->exclusao($boTransacao);
    }

    if (!$obErro->ocorreu()) {
        $obTFolhaPagamentoEventoComplementarCalculado->setDado("cod_registro", $this->roRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoRegistroEventoComplementar->getCodRegistro());
        $obErro = $obTFolhaPagamentoEventoComplementarCalculado->exclusao($boTransacao);
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTFolhaPagamentoEventoComplementarCalculado );

    return $obErro;
}

/**
    * Método listar
    * @access Private
*/
function listar(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoComplementarCalculado.class.php");
    $obTFolhaPagamentoEventoComplementarCalculado = new TFolhaPagamentoEventoComplementarCalculado;
    $obErro = $obTFolhaPagamentoEventoComplementarCalculado->recuperaRelacionamento($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

/**
    * Método listarEventoComplementarCalculado
    * @access Public
*/
function listarEventoComplementarCalculado(&$rsRecordSet,$boTransacao="")
{
    if ( $this->getRegistros() ) {
        $stRegistro = "";
        foreach ( $this->getRegistros() as $inRegistro ) {
            $stRegistro .= $inRegistro.",";
        }
        $stRegistro = substr($stRegistro,0,strlen($stRegistro)-1);
        $stFiltro .= " AND registro IN (".$stRegistro.")";
    }
    if ( $this->roRFolhaPagamentoFolhaComplementar->getCodComplementar() ) {
        $stFiltro .= " AND cod_complementar = ".$this->roRFolhaPagamentoFolhaComplementar->getCodComplementar();
    }
    if ($stFiltro != "") {
        $stFiltro = " WHERE " . substr($stFiltro,4,strlen($stFiltro));
    }
    $stOrdem = " nom_cgm,registro";
    $obErro = $this->listar($rsRecordSet,$stFiltro,$stOrdem,$boTransacao);

    return $obErro;
}

/**
    * Método listarEventoComplementarCalculadoParaRelatorio
    * @access Private
*/
function listarEventoComplementarCalculadoParaRelatorio(&$rsRecordSet,$inCodContrato,$inCodEvento,$boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoComplementarCalculado.class.php");
    $obTFolhaPagamentoEventoComplementarCalculado = new TFolhaPagamentoEventoComplementarCalculado;
    $stFiltro .= " AND registro_evento_complementar.cod_contrato = ".$inCodContrato;
    $stFiltro .= " AND registro_evento_complementar.cod_evento = ".$inCodEvento;
    $obErro = $obTFolhaPagamentoEventoComplementarCalculado->recuperaEventoComplementarCalculadoParaRelatorio($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

/**
    * Método listarLogErroCalculo
    * @access Private
*/
function listarLogErroCalculo(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoLogErroCalculoComplementar.class.php");
    $obTFolhaPagamentoLogErroCalculoComplementar = new TFolhaPagamentoLogErroCalculoComplementar;
    if ( $this->getRegistros() ) {
        $stRegistro = "";
        foreach ( $this->getRegistros() as $inRegistro ) {
            $stRegistro .= $inRegistro.",";
        }
        $stRegistro = substr($stRegistro,0,strlen($stRegistro)-1);
        $stFiltro .= " AND registro IN (".$stRegistro.")";
    }
    if ($stFiltro != "") {
        $stFiltro = " WHERE " . substr($stFiltro,4,strlen($stFiltro));
    }

    $obErro = $obTFolhaPagamentoLogErroCalculoComplementar->recuperaRelacionamento($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

/**
    * Método listarEventoFichaFinanceira
    * @access Private
*/
function listarEventoFichaFinanceira(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoComplementarCalculado.class.php");
    $obTFolhaPagamentoEventoComplementarCalculado = new TFolhaPagamentoEventoComplementarCalculado;
    if ($stOrder != "") {
        $stOrder = " ORDER BY contrato_servidor_complementar.cod_contrato,sequencia,".$stOrder;
    }

    if ( $this->roRFolhaPagamentoFolhaComplementar->getCodComplementar() ) {
        $stFiltro .= " AND complementar.cod_complementar = ".$this->roRFolhaPagamentoFolhaComplementar->getCodComplementar();
    }
    if ( $this->roRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoPeriodoMovimentacao->getDtFinal() ) {
        $stFiltro .= " AND to_char(dt_final,'mm/yyyy') = '".$this->roRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoPeriodoMovimentacao->getDtFinal()."'";
    }
    if ( is_object($this->roRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor) ) {
        if ( $this->roRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->getRegistro() ) {
            $stFiltro .= " AND registro = ".$this->roRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->getRegistro();
        }
    }
    if ( is_object($this->roRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo) ) {
        if ( is_object($this->roRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo->roUltimoEspecialidade) ) {
            if ( $this->roRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo->roUltimoEspecialidade->getCodEspecialidade() ) {
                $stFiltro .= " AND cod_especialidade = ".$this->roRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo->roUltimoEspecialidade->getCodEspecialidade();
            }
        } else {
            if ( $this->roRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo->getCodCargo() ) {
                $stFiltro .= " AND contrato_servidor.cod_cargo = ".$this->roRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo->getCodCargo();
            }
        }
    }
    if ( is_object( $this->roRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor ) ) {
        if ( $this->roRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->obROrganogramaOrgao->getCodOrgao() ) {
            $stFiltro .= " AND cod_orgao IN (".$this->roRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->obROrganogramaOrgao->getCodOrgao().")";
        }
        if ($this->roRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->obROrganogramaLocal->getCodLocal()) {
            $stFiltro .= " AND cod_local IN (".$this->roRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->obROrganogramaLocal->getCodLocal().")";
        }
    }

    $obErro = $obTFolhaPagamentoEventoComplementarCalculado->recuperaEventoFichaFinanceira($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

/**
    * Método listarRelatorioFolhaAnaliticaSintetica
    * @access Private
*/
function listarRelatorioFolhaAnaliticaSintetica(&$rsRecordSet,$arFiltros="",$stOrdem="",$boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoComplementarCalculado.class.php");
    $obTFolhaPagamentoEventoComplementarCalculado = new TFolhaPagamentoEventoComplementarCalculado;
    if ( isset($arFiltros['cod_complementar']) ) {
        $stFiltro .= " AND complementar.cod_complementar = ".$arFiltros['cod_complementar'];
    }
    if ( isset($arFiltros['cod_periodo_movimentacao']) ) {
        $stFiltro .= " AND contrato_servidor_complementar.cod_periodo_movimentacao = ".$arFiltros['cod_periodo_movimentacao'];
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
    $obErro = $obTFolhaPagamentoEventoComplementarCalculado->recuperaRelatorioFolhaAnaliticaSintetica($rsRecordSet,$stFiltro,$stOrdem,$boTransacao);

    return $obErro;
}

/**
    * Lista eventos para o relatório customivável de eventos
    * @access Public
*/
function listarRelatorioCustomizavelEventos(&$rsRecordset, $arFiltros, $stOrdem, $boTransacao="")
{
    include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoComplementarCalculado.class.php"                   );
    $obTFolhaPagamentoEventoComplementarCalculado = new TFolhaPagamentoEventoComplementarCalculado;
    $stOrdem = " ORDER BY ".$stOrdem;
    if ( isset($arFiltros['cod_complementar']) ) {
        $stFiltro .= " AND complementar.cod_complementar = ".$arFiltros['cod_complementar'];
    }
    if ( isset($arFiltros['arEventos']) ) {
        foreach ($arFiltros['arEventos'] as $inCodEvento) {
            $stEventos .= $inCodEvento . ",";
        }
        $stEventos = substr($stEventos,0,strlen($stEventos)-1);
        $stFiltro .= " AND evento.cod_evento IN (".$stEventos.")";
    }
    if ($arFiltros['boAtivo']) {
        $stFiltro .= " AND ativo = true";
    }
    if ($arFiltros['boInativo']) {
        $stFiltro .= " AND ativo = false";
    }
    //if ($arFiltros['boPensionista']) {
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
    if ( isset($arFiltros['cod_periodo_movimentacao']) ) {
        $stFiltro .= " AND contrato_servidor_complementar.cod_periodo_movimentacao = ".$arFiltros['cod_periodo_movimentacao'];
    }
    if ( is_array($arFiltros['arEspecialidades']) ) {
        $stEspecialidades = "";
        foreach ($arFiltros['arEspecialidades'] as $inEspecialidade) {
            $stEspecialidades .= $inEspecialidade.",";
        }
        $stEspecialidades = substr($stEspecialidades,0,strlen($stEspecialidades)-1);
        $stFiltro .= " AND especialidade.cod_especialidade IN (".$stEspecialidades.")";
    }
    if ( is_array($arFiltros['arCargos']) ) {
        $stCargos = "";
        foreach ($arFiltros['arCargos'] as $inCargo) {
            $stCargos .= $inCargo.",";
        }
        $stCargos = substr($stCargos,0,strlen($stCargos)-1);
        $stFiltro .= " AND cargo.cod_cargo IN (".$stCargos.")";
    }
    if ( is_array($arFiltros['arFuncoes']) ) {
        $stCargos = "";
        foreach ($arFiltros['arFuncoes'] as $inCargo) {
            $stCargos .= $inCargo.",";
        }
        $stCargos = substr($stCargos,0,strlen($stCargos)-1);
        $stFiltro .= " AND funcao.cod_funcao IN (".$stCargos.")";
    }
    if ( is_array($arFiltros['arEspecialidadesFunc']) ) {
        $stEspecialidades = "";
        foreach ($arFiltros['arEspecialidadesFunc'] as $inEspecialidade) {
            $stEspecialidades .= $inEspecialidade.",";
        }
        $stEspecialidades = substr($stEspecialidades,0,strlen($stEspecialidades)-1);
        $stFiltro .= " AND especialidade_funcao.cod_especialidade_funcao IN (".$stEspecialidades.")";
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
    $obErro = $obTFolhaPagamentoEventoComplementarCalculado->recuperaRelatorioCustomizavelEventos( $rsRecordset, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Método listarEventosBaseRelatorioFichaFinanceira
    * @access Private
*/
function listarEventosBaseDescontoRelatorioFichaFinanceira(&$rsRecordSet,$inNumCgm,$stNatureza,$inCodPeriodoMovimentacao,$inCodPrevidencia,$boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoComplementarCalculado.class.php");
    $obTFolhaPagamentoEventoComplementarCalculado = new TFolhaPagamentoEventoComplementarCalculado;
    $stFiltro .= " WHERE numcgm = ".$inNumCgm;
    $stFiltro .= " AND natureza = '".$stNatureza."'";
    $stFiltro .= " AND cod_periodo_movimentacao = ".$inCodPeriodoMovimentacao;
    $stOrdem   = " registro";
    $obTFolhaPagamentoEventoComplementarCalculado->setDado("cod_previdencia",$inCodPrevidencia);
    $obErro = $obTFolhaPagamentoEventoComplementarCalculado->recuperaEventosBaseDescontoRelatorioFichaFinanceira($rsRecordSet,$stFiltro,$stOrdem,$boTransacao);

    return $obErro;
}

}
?>
