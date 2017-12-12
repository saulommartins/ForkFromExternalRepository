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
    * Classe de Regra de Negócio para Transferencia
    * Data de Criação   : 20/10/2005

    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Regra

    $Id: RTesourariaTransferencia.class.php 66259 2016-08-03 17:01:50Z michel $

    * Casos de uso: uc-02.04.09, uc-02.04.28, uc-02.04.27, uc-02.04.26
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_FW_BANCO_DADOS."Transacao.class.php";
include_once CAM_GF_TES_NEGOCIO."RTesourariaAutenticacao.class.php";
include_once CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoValor.class.php";
include_once CAM_FW_LEGADO."funcoesLegado.lib.php";
include_once CAM_GA_CGM_NEGOCIO."RCGM.class.php";

/**
    * Classe de Regra de Transferencia
    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Anderson R. M. Buzo
*/
class RTesourariaTransferencia
{
/*
    * @var Object
    * @access Private
*/
var $obRTesourariaAutenticacao;
/*
    * @var Object
    * @access Private
*/
var $obRContabilidadeLancamentoValor;
/*
    * @var Object
    * @access Private
*/
var $obRContabilidadeLancamentoValorEstornada;
/*
    * @var Object
    * @access Private
*/
var $roRTesourariaBoltim;
/*
    * @var Object
    * @access Private
*/
var $obRCGM;
/*
    * @var String
    * @access Private
*/
var $stNomContaCredito;
/*
    * @var String
    * @access Private
*/
var $stNomContaDebito;
/*
    * @var String
    * @access Private
*/
var $stTimestampTransferencia;
/*
    * @var String
    * @access Private
*/
var $stTimestampEstornada;
/*
    * @var String
    * @access Private
*/
var $stObservacaoTransferencia;
/*
    * @var String
    * @access Private
*/
var $stObservacaoEstorno;

var $inCodRecurso;
var $inCodCredor;
var $inCodRecibo;
var $stTipoRecibo;
var $inCodTipoTransferencia;
var $stDestinacaoRecurso;

/*
    * @access Public
    * @param Object $valor
*/
function setRTesourariaAutenticacao($valor) { $this->obRTesourariaAutenticacao                = $valor; }
/*
    * @access Public
    * @param Object $valor
*/
function setRContabilidadeLancamentoValor($valor) { $this->obRContabilidadeLancamentoValor          = $valor; }
/*
    * @access Public
    * @param Object $valor
*/
function setRContabilidadeLancamentoValorEstornada($valor) { $this->obRContabilidadeLancamentoValorEstornada = $valor; }
/*
    * @access Public
    * @param String $valor
*/
function setNomContaCredito($valor) { $this->stNomContaCredito                        = $valor; }
/*
    * @access Public
    * @param String $valor
*/
function setNomContaDebito($valor) { $this->stNomContaDebito                         = $valor; }
/*
    * @access Public
    * @param String $valor
*/
function setTimestampTransferencia($valor) { $this->stTimestampTransferencia            = $valor; }
/*
    * @access Public
    * @param String $valor
*/
function setTimestampEstornada($valor) { $this->stTimestampEstornada                = $valor; }
/*
    * @access Public
    * @param String $valor
*/
function setObservacaoTransferencia($valor) { $this->stObservacaoTransferencia           = $valor; }
/*
    * @access Public
    * @param String $valor
*/
function setObservacaoEstorno($valor) { $this->stObservacaoEstorno                 = $valor; }
function setCodCredor($valor) { $this->inCodCredor                         = $valor; }
function setCodRecurso($valor) { $this->inCodRecurso                        = $valor; }
function setDestinacaoRecurso($valor) { $this->stDestinacaoRecurso                 = $valor; }
function setCodRecibo($valor) { $this->inCodRecibo                         = $valor; }
function setTipoRecibo($valor) { $this->stTipoRecibo                        = $valor; }
function setTipoTransferencia($valor) { $this->inCodTipoTransferencia                 = $valor; }
/*
    * @access Public
    * @return Object
*/
function getRTesourariaAutenticacao() { return $this->obRTesourariaAutenticacao;                }
/*
    * @access Public
    * @return Object
*/
function getRContabilidadeLancamentoValor() { return $this->obRContabilidadeLancamentoValor;          }
/*
    * @access Public
    * @return Object
*/
function getRContabilidadeLancamentoValorEstornada() { return $this->obRContabilidadeLancamentoValorEstornada; }
/*
    * @access Public
    * @return String
*/
function getNomContaDebito() { return $this->stNomContaDebito;                         }
/*
    * @access Public
    * @return String
*/
function getNomContaCredito() { return $this->stNomContaCredito;                        }
/*
    * @access Public
    * @return String
*/
function getTimestampTransferencia() { return $this->stTimestampTransferencia;            }
/*
    * @access Public
    * @return String
*/
function getTimestampEstornada() { return $this->stTimestampEstornada;                }
/*
    * @access Public
    * @return String
*/
function getObservacaoTransferencia() { return $this->stObservacaoTransferencia;           }
/*
    * @access Public
    * @return String
*/
function getObservacaoEstorno() { return $this->stObservacaoEstorno;                 }
function getCodCredor() { return $this->inCodCredor;                         }
function getCodRecurso() { return $this->inCodRecurso;                        }
function getCodRecibo() { return $this->inCodRecibo;                         }
function getTipoRecibo() { return $this->stTipoRecibo;                        }
function getTipoTransferencia() { return $this->inCodTipoTransferencia;                 }
/**
    * Método Construtor
    * @access Private
*/
function RTesourariaTransferencia($roRTesourariaBoltim)
{
    $this->obRTesourariaAutenticacao                = new RTesourariaAutenticacao();
    $this->obRContabilidadeLancamentoValor          = new RContabilidadeLancamentoValor();
    $this->obRContabilidadeLancamentoValorEstornada = new RContabilidadeLancamentoValor();
    $this->obRCGM                                   = new RCGM();
    $this->roRTesourariaBoletim                     = &$roRTesourariaBoltim;
}

/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function verificaExisteContaBanco(&$boVerificado, $boTransacao = "")
{
    include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaTransferencia.class.php"          );
    $obTTesourariaTransferencia          = new TTesourariaTransferencia();

    if( $this->roRTesourariaBoletim->getExercicio() )
        $stFiltro .= " exercicio = '" .$this->roRTesourariaBoletim->getExercicio()."' AND";

    if ( $this->obRContabilidadeLancamentoValor->getContaCredito() AND $this->obRContabilidadeLancamentoValor->getContaDebito() ) {
        $stFiltro .= " (cod_plano = " .$this->obRContabilidadeLancamentoValor->getContaCredito()." OR ";
        $stFiltro .= " cod_plano = " .$this->obRContabilidadeLancamentoValor->getContaDebito().") AND";
    }

    if(substr($stFiltro,-3)=="AND")
        $stFiltro = " WHERE ".substr($stFiltro,0,strlen($stFiltro)-3);

    $obErro = $obTTesourariaTransferencia->verificaExisteContaBanco( $boVerificado, $stFiltro, $boTransacao );

    return $obErro;
}

/**
    * Salva os dados no banco de dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function transferir($boTransacao = "")
{
    include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaTransferencia.class.php"          );
    $obTransacao                         = new Transacao();
    $obTTesourariaTransferencia          = new TTesourariaTransferencia();
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    $inCodUf = SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(), $boTransacao);

    if ( !$obErro->ocorreu() ) {
        $obErro = $this->roRTesourariaBoletim->incluir( $boTransacao );

        if ( !$obErro->ocorreu() and ($_REQUEST['inCodDocTipo'] == 1 OR $_REQUEST['inCodDocTipo'] == 2 OR $_REQUEST['inCodDocTipo'] == 3 OR $_REQUEST['inCodDocTipo'] == 99 )) {
            if($_REQUEST['nroDoc'] == ''){
                $obErro->setDescricao( 'Necessário preencher o campo Nr. Documento.' );
            }
        }

        if ( !$obErro->ocorreu() ) {
            $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setTipo( "T" );
            $obErro = $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->buscaProximoCodigo($boTransacao);

            if ( !$obErro->ocorreu() ) {
                $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setDtLote($this->roRTesourariaBoletim->getDataBoletim());
                $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setNomLote("Transferência - CD:".$this->obRContabilidadeLancamentoValor->getContaDebito()." | CC:".$this->obRContabilidadeLancamentoValor->getContaCredito());
                $obErro = $this->obRContabilidadeLancamentoValor->incluir($boTransacao);
                if ( !$obErro->ocorreu() ) {
                    $this->obRTesourariaAutenticacao->setTipo("T");
                    $this->obRTesourariaAutenticacao->setDataAutenticacao( $this->roRTesourariaBoletim->getDataBoletim() );
                    $this->obRTesourariaAutenticacao->obRTesourariaConfiguracao->setExercicio($this->roRTesourariaBoletim->getExercicio());
                    $obErro = $this->obRTesourariaAutenticacao->autenticar($boTransacao);
                
                    if ( !$obErro->ocorreu() ) {
                        $obTTesourariaTransferencia->setDado( 'cod_lote'                , $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() );
                        $obTTesourariaTransferencia->setDado( 'exercicio'               , $this->roRTesourariaBoletim->getExercicio()          );
                        $obTTesourariaTransferencia->setDado( 'cod_entidade'            , $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
                        $obTTesourariaTransferencia->setDado( 'tipo'                    , $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getTipo() );
                        $obTTesourariaTransferencia->setDado( 'cod_autenticacao'        , $this->obRTesourariaAutenticacao->getCodAutenticacao()  );
                        $obTTesourariaTransferencia->setDado( 'dt_autenticacao'         , $this->roRTesourariaBoletim->getDataBoletim()        );
                        $obTTesourariaTransferencia->setDado( 'cod_plano_debito'        , $this->obRContabilidadeLancamentoValor->getContaDebito() );
                        $obTTesourariaTransferencia->setDado( 'cod_plano_credito'       , $this->obRContabilidadeLancamentoValor->getContaCredito() );
                        $obTTesourariaTransferencia->setDado( 'cod_boletim'             , $this->roRTesourariaBoletim->getCodBoletim()         );
                        $obTTesourariaTransferencia->setDado( 'cod_historico'           , $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeHistoricoPadrao->getCodHistorico() );
                        $obTTesourariaTransferencia->setDado( 'cod_terminal'            , $this->roRTesourariaBoletim->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->getCodTerminal() );
                        $obTTesourariaTransferencia->setDado( 'timestamp_terminal'      , $this->roRTesourariaBoletim->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->getTimestampTerminal() );
                        $obTTesourariaTransferencia->setDado( 'cgm_usuario'             , $this->roRTesourariaBoletim->obRTesourariaUsuarioTerminal->obRCGM->getNumCgm() );
                        $obTTesourariaTransferencia->setDado( 'timestamp_usuario'       , $this->roRTesourariaBoletim->obRTesourariaUsuarioTerminal->getTimestampUsuario() );
                        $obTTesourariaTransferencia->setDado( 'timestamp_transferencia' , $this->stTimestampTransferencia );
                        $obTTesourariaTransferencia->setDado( 'observacao'              , $this->stObservacaoTransferencia );
                        $obTTesourariaTransferencia->setDado( 'valor'                   , $this->obRContabilidadeLancamentoValor->getValor() );
                        $obTTesourariaTransferencia->setDado( 'cod_tipo'                , $this->inCodTipoTransferencia );
                        $obErro = $obTTesourariaTransferencia->inclusao( $boTransacao );
                   
                        if (!$obErro->ocorreu()) {
                            
                            if (SistemaLegado::isAL($boTransacao)) {
                                include_once ( CAM_GPC_TCEAL_MAPEAMENTO ."TTipoPagamento.class.php" );
                                if ($_REQUEST['cmbTipoPagamento'] == 1) {
                                    $stTipoPagamento = 'Ordem Bancária';
                                } elseif ($_REQUEST['cmbTipoPagamento'] == 2) {
                                    $stTipoPagamento = 'Cheque';
                                }
                                $obTTipoPagamento = new TTipoPagamento();
                                $obTTipoPagamento->setDado( 'cod_lote'       , $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() );
                                $obTTipoPagamento->setDado( 'cod_entidade'   , $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
                                $obTTipoPagamento->setDado( 'exercicio'      , $this->roRTesourariaBoletim->getExercicio()      );
                                $obTTipoPagamento->setDado( 'tipo'           , $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getTipo() );
                                $obTTipoPagamento->setDado( 'tipo_pagamento' , $stTipoPagamento);
                                $obTTipoPagamento->setDado( 'descricao'      , $_REQUEST['stDescricao']);
                                $obErro = $obTTipoPagamento->inclusao( $boTransacao );
                            }

                            //###TCEMG
                            if ( !$obErro->ocorreu() ) {
                                $boTipoDocTCEMG = $_REQUEST['boTipoDocTCEMG'];

                                if ($boTipoDocTCEMG == 'true') {
                                    require_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGTransferenciaTipoDocumento.class.php";
                                    $obTTCEMGTransferenciaTipoDocumento = new TTCEMGTransferenciaTipoDocumento;
                                    $obTTCEMGTransferenciaTipoDocumento->setDado( 'cod_entidade'      , $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
                                    $obTTCEMGTransferenciaTipoDocumento->setDado( 'exercicio'         , $this->roRTesourariaBoletim->getExercicio()      );
                                    $obTTCEMGTransferenciaTipoDocumento->setDado( 'cod_lote'          , $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() );
                                    $obTTCEMGTransferenciaTipoDocumento->setDado( 'tipo'              , $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getTipo() );
                                    $obTTCEMGTransferenciaTipoDocumento->setDado( 'cod_tipo_documento', $_REQUEST['inCodDocTipo'] );
                                    $obTTCEMGTransferenciaTipoDocumento->setDado( 'num_documento'     , $_REQUEST['nroDoc'] );
                                    $obErro = $obTTCEMGTransferenciaTipoDocumento->inclusao($boTransacao);
                                }
                            }

                            if ( !$obErro->ocorreu() ) {
                                // INSERT NO BANCO PARA TOCANTINS
                                if ( $inCodUf == 27 AND $_REQUEST['inCodTipoPagamento'] ) {
                                    include_once CAM_GPC_TCETO_MAPEAMENTO."TTCETOTransferenciaTipoPagamento.class.php";
                                    $obTTCETOTransferenciaTipoPagamento = new TTCETOTransferenciaTipoPagamento();
                                    $obTTCETOTransferenciaTipoPagamento->setDado('cod_lote'     , $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() );
                                    $obTTCETOTransferenciaTipoPagamento->setDado('exercicio'    , $this->roRTesourariaBoletim->getExercicio() );
                                    $obTTCETOTransferenciaTipoPagamento->setDado('cod_entidade' , $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
                                    $obTTCETOTransferenciaTipoPagamento->setDado('tipo'         , $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getTipo() );
                                    $obTTCETOTransferenciaTipoPagamento->setDado('cod_tipo'     , $_REQUEST['inCodTipoPagamento'] );
                                    $obErro = $obTTCETOTransferenciaTipoPagamento->inclusao( $boTransacao );
                                }
                                if ( $inCodUf == 27 AND $_REQUEST['inCodTipoTransferenciaTO'] ) {
                                    
                                    include_once CAM_GPC_TCETO_MAPEAMENTO."TTCETOTransferenciaTipoTransferencia.class.php";
                                    $obTTCETOTransferenciaTipoTransferencia = new TTCETOTransferenciaTipoTransferencia();
                                    $obTTCETOTransferenciaTipoTransferencia->setDado('cod_lote'               , $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() );
                                    $obTTCETOTransferenciaTipoTransferencia->setDado('exercicio'              , $this->roRTesourariaBoletim->getExercicio() );
                                    $obTTCETOTransferenciaTipoTransferencia->setDado('cod_entidade'           , $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
                                    $obTTCETOTransferenciaTipoTransferencia->setDado('tipo'                   , $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getTipo() );
                                    $obTTCETOTransferenciaTipoTransferencia->setDado('cod_tipo_transferencia' , $_REQUEST['inCodTipoTransferenciaTO']        );
                                    $obTTCETOTransferenciaTipoTransferencia->setDado('cod_empenho'            , $_REQUEST['inCodigoEmpenho']                 );
                                    if($_REQUEST['stExercicioEmpenho'] != null){
                                      $obTTCETOTransferenciaTipoTransferencia->setDado('exercicio_empenho'      , $_REQUEST['stExercicioEmpenho']            );
                                    }
                                    $obErro = $obTTCETOTransferenciaTipoTransferencia->inclusao( $boTransacao );
                                }
                            }
                            
                            if ($this->inCodTipoTransferencia == 1) {
                                // INSERT NO BANCO PARA PERNAMBUCO
                                if ( isset($_REQUEST['inCodTransferencia']) ) {
                                    include_once( CAM_GPC_TCEPE_MAPEAMENTO."TTCEPETipoTransferenciaConcedida.class.php" );
                                    $obTTCEPETipoTransferenciaConcedida = new TTCEPETipoTransferenciaConcedida();
                                    $obTTCEPETipoTransferenciaConcedida->setDado( 'cod_lote'                 , $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() );
                                    $obTTCEPETipoTransferenciaConcedida->setDado( 'cod_entidade'             , $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
                                    $obTTCEPETipoTransferenciaConcedida->setDado( 'exercicio'                , $this->roRTesourariaBoletim->getExercicio() );
                                    $obTTCEPETipoTransferenciaConcedida->setDado( 'tipo'                     , $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getTipo() );
                                    $obTTCEPETipoTransferenciaConcedida->setDado( 'cod_tipo_tcepe'           , $_REQUEST['inCodTransferencia'] );
                                    $obTTCEPETipoTransferenciaConcedida->setDado( 'cod_entidade_beneficiada' , $_REQUEST['inCodEntidadeBeneficio'] );
                                    $obErro = $obTTCEPETipoTransferenciaConcedida->inclusao( $boTransacao );
                                }
                            } else {
                                // INSERT NO BANCO PARA PERNAMBUCO
                                if ( isset($_REQUEST['inCodTransferencia']) ) {
                                    include_once( CAM_GPC_TCEPE_MAPEAMENTO."TTCEPETipoTransferenciaRecebida.class.php" );
                                    $obTTCEPETipoTransferenciaRecebida = new TTCEPETipoTransferenciaRecebida();
                                    $obTTCEPETipoTransferenciaRecebida->setDado( 'cod_lote'                   , $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() );
                                    $obTTCEPETipoTransferenciaRecebida->setDado( 'cod_entidade'               , $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
                                    $obTTCEPETipoTransferenciaRecebida->setDado( 'exercicio'                  , $this->roRTesourariaBoletim->getExercicio() );
                                    $obTTCEPETipoTransferenciaRecebida->setDado( 'tipo'                       , $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getTipo() );
                                    $obTTCEPETipoTransferenciaRecebida->setDado( 'cod_tipo_tcepe'             , $_REQUEST['inCodTransferencia'] );
                                    $obTTCEPETipoTransferenciaRecebida->setDado( 'cod_entidade_transferidora' , $_REQUEST['inCodEntidadeTransferidora'] );
                                    $obErro = $obTTCEPETipoTransferenciaRecebida->inclusao( $boTransacao );
                                }
                            }
                            
                            if ($this->inCodCredor) {
                                include_once( CAM_GF_TES_MAPEAMENTO."TTesourariaTransferenciaCredor.class.php" );
                                $obTTesourariaTransferenciaCredor = new TTesourariaTransferenciaCredor();
                                $obTTesourariaTransferenciaCredor->setDado( 'tipo'                  , $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getTipo() );
                                $obTTesourariaTransferenciaCredor->setDado( 'exercicio'             , $this->roRTesourariaBoletim->getExercicio()      );
                                $obTTesourariaTransferenciaCredor->setDado( 'cod_entidade'          , $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
                                $obTTesourariaTransferenciaCredor->setDado( 'cod_lote'              , $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() );
                                $obTTesourariaTransferenciaCredor->setDado( 'numcgm'                , $this->inCodCredor );
                                $obErro = $obTTesourariaTransferenciaCredor->inclusao( $boTransacao );
                            }
                            
                            include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php" );
                            $obRConfiguracaoOrcamento = new ROrcamentoConfiguracao;
                            $obRConfiguracaoOrcamento->setExercicio($this->roRTesourariaBoletim->getExercicio());
                            $obRConfiguracaoOrcamento->consultarConfiguracao($boTransacao);
                            $boDestinacao = $obRConfiguracaoOrcamento->getDestinacaoRecurso();
                            
                            if ($boDestinacao) {
                                
                                if ($this->stDestinacaoRecurso && !$this->inCodRecibo) { // Usa a destinação criada pelo recibo
                                    include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecurso.class.php" );
                                    $obTOrcamentoRecurso = new TOrcamentoRecurso;
                                    $obTOrcamentoRecurso->setDado("exercicio", $this->roRTesourariaBoletim->getExercicio() );
                                    $obTOrcamentoRecurso->proximoCod( $inCodRecurso, $boTransacao );
                                    $obTOrcamentoRecurso->setDado("cod_recurso", $inCodRecurso );
                                    $obErro = $obTOrcamentoRecurso->inclusao( $boTransacao );
                                    
                                    if (!$obErro->ocorreu()) {
                                        $arDestinacaoRecurso = explode('.',$this->stDestinacaoRecurso);
                                        
                                        include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecursoDestinacao.class.php" );
                                        $obTOrcamentoRecursoDestinacao = new TOrcamentoRecursoDestinacao;
                                        $obTOrcamentoRecursoDestinacao->setDado("exercicio",        $this->roRTesourariaBoletim->getExercicio()  );
                                        $obTOrcamentoRecursoDestinacao->setDado("cod_recurso",      $inCodRecurso           );
                                        $obTOrcamentoRecursoDestinacao->setDado("cod_uso",          $arDestinacaoRecurso[0] );
                                        $obTOrcamentoRecursoDestinacao->setDado("cod_destinacao",   $arDestinacaoRecurso[1] );
                                        $obTOrcamentoRecursoDestinacao->setDado("cod_especificacao",$arDestinacaoRecurso[2] );
                                        $obTOrcamentoRecursoDestinacao->setDado("cod_detalhamento", $arDestinacaoRecurso[3] );
                                        $obErro = $obTOrcamentoRecursoDestinacao->inclusao( $boTransacao );
                                    }
                                    
                                    if (!$obErro->ocorreu()) {
                                        include_once( CAM_GF_TES_MAPEAMENTO."TTesourariaTransferenciaRecurso.class.php" );
                                        $obTTesourariaTransferenciaRecurso = new TTesourariaTransferenciaRecurso();
                                        $obTTesourariaTransferenciaRecurso->setDado( 'tipo'                  , $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getTipo() );
                                        $obTTesourariaTransferenciaRecurso->setDado( 'exercicio'             , $this->roRTesourariaBoletim->getExercicio()      );
                                        $obTTesourariaTransferenciaRecurso->setDado( 'cod_entidade'          , $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
                                        $obTTesourariaTransferenciaRecurso->setDado( 'cod_lote'              , $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() );
                                        $obTTesourariaTransferenciaRecurso->setDado( 'cod_recurso'           , $inCodRecurso );
                                        $obErro = $obTTesourariaTransferenciaRecurso->inclusao( $boTransacao );
                                    }
                                    
                                } else { // Destinação ON & Pagamento sem recibo
                                    
                                    if ($this->inCodRecurso && !$obErro->ocorreu()) {
                                        include_once( CAM_GF_TES_MAPEAMENTO."TTesourariaTransferenciaRecurso.class.php" );
                                        $obTTesourariaTransferenciaRecurso = new TTesourariaTransferenciaRecurso();
                                        $obTTesourariaTransferenciaRecurso->setDado( 'tipo'                  , $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getTipo() );
                                        $obTTesourariaTransferenciaRecurso->setDado( 'exercicio'             , $this->roRTesourariaBoletim->getExercicio()      );
                                        $obTTesourariaTransferenciaRecurso->setDado( 'cod_entidade'          , $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
                                        $obTTesourariaTransferenciaRecurso->setDado( 'cod_lote'              , $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() );
                                        $obTTesourariaTransferenciaRecurso->setDado( 'cod_recurso'           , $this->inCodRecurso );
                                        $obErro = $obTTesourariaTransferenciaRecurso->inclusao( $boTransacao );
                                    }
                                }
                                
                            } else { // Destinação OFF :: Recurso já existente.
                                
                                if ($this->inCodRecurso) {
                                    include_once( CAM_GF_TES_MAPEAMENTO."TTesourariaTransferenciaRecurso.class.php" );
                                    $obTTesourariaTransferenciaRecurso = new TTesourariaTransferenciaRecurso();
                                    $obTTesourariaTransferenciaRecurso->setDado( 'tipo'                  , $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getTipo() );
                                    $obTTesourariaTransferenciaRecurso->setDado( 'exercicio'             , $this->roRTesourariaBoletim->getExercicio()      );
                                    $obTTesourariaTransferenciaRecurso->setDado( 'cod_entidade'          , $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
                                    $obTTesourariaTransferenciaRecurso->setDado( 'cod_lote'              , $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() );
                                    $obTTesourariaTransferenciaRecurso->setDado( 'cod_recurso'           , $this->inCodRecurso );
                                    $obErro = $obTTesourariaTransferenciaRecurso->inclusao( $boTransacao );
                                }
                            }
                            
                            if ($this->inCodRecibo && $this->stTipoRecibo) {
                                include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaReciboExtraTransferencia.class.php"          );
                                $obTTesourariaReciboExtraTransferencia = new TTesourariaReciboExtraTransferencia();
                                $obTTesourariaReciboExtraTransferencia->setDado('cod_recibo_extra', $this->getCodRecibo() );
                                $obTTesourariaReciboExtraTransferencia->setDado('exercicio'       , $this->roRTesourariaBoletim->getExercicio() );
                                $obTTesourariaReciboExtraTransferencia->setDado('cod_entidade'    , $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
                                $obTTesourariaReciboExtraTransferencia->setDado('tipo_recibo'     , $this->getTipoRecibo() );
                                $obTTesourariaReciboExtraTransferencia->setDado('cod_lote'        , $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() );
                                $obTTesourariaReciboExtraTransferencia->setDado('tipo'            , $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getTipo() );
                                $obErro = $obTTesourariaReciboExtraTransferencia->inclusao( $boTransacao );
                            }
                        }
                        
                        if ( !$obErro->ocorreu() ) {
                            $this->montaDescricaoAutenticacao( $boTransacao );
                        }
                        
                        if ($this->inCodTipoTransferencia == 1 || $this->inCodTipoTransferencia == 2) {
                            
                        if (Sessao::getExercicio() > '2008' && !$obErro->ocorreu()) {
                            
                            if (!$obErro->ocorreu()) {
                                if ($this->inCodTipoTransferencia == 1) { // Pagamento Extra
                                    $inCodConta = $this->obRContabilidadeLancamentoValor->getContaDebito();
                                    $inCodContaBanco = $this->obRContabilidadeLancamentoValor->getContaCredito();
                                } elseif ($this->inCodTipoTransferencia == 2) { // Arrecadação Extra
                                    $inCodConta = $this->obRContabilidadeLancamentoValor->getContaCredito();
                                    $inCodContaBanco = $this->obRContabilidadeLancamentoValor->getContaDebito();
                                }
                                include_once CAM_GF_CONT_NEGOCIO.'RContabilidadePlanoBanco.class.php';
                                $obRContabilidadePlanoBanco  =  new RContabilidadePlanoBanco;
                                $obRContabilidadePlanoBanco->setCodPlano($inCodConta);
                                $obErro = $obRContabilidadePlanoBanco->getRecursoVinculoConta($rsCodRecurso, $boTransacao);
                                $inCodRecurso = $rsCodRecurso->getCampo('cod_recurso');
                                
                                if ($inCodRecurso == '' && $inCodContaBanco != '') {
                                    $obRContabilidadePlanoBanco->setCodPlano($inCodContaBanco);
                                    $obErro = $obRContabilidadePlanoBanco->getRecursoVinculoConta($rsCodRecurso, $boTransacao);
                                    $inCodRecurso = $rsCodRecurso->getCampo('cod_recurso');
                                }
                                
                                $boDestinacao = false;
                                include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoConfiguracao.class.php";
                                $obTOrcamentoConfiguracao = new TOrcamentoConfiguracao;
                                $obTOrcamentoConfiguracao->setDado("exercicio", Sessao::getExercicio() );
                                $obTOrcamentoConfiguracao->setDado("parametro","recurso_destinacao");
                                $obTOrcamentoConfiguracao->consultar($boTransacao);
                                
                                if($obTOrcamentoConfiguracao->getDado("valor") == 'true')
                                    $boDestinacao = true;
                                    
                                if ($boDestinacao && $inCodRecurso != '') {
                                    include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecursoDestinacao.class.php";
                                    $obTOrcamentoRecursoDestinacao = new TOrcamentoRecursoDestinacao;
                                    $obTOrcamentoRecursoDestinacao->setDado("exercicio", Sessao::getExercicio());
                                    
                                    $stFiltro  = ' WHERE recurso_destinacao.cod_recurso = '.$inCodRecurso;
                                    $stFiltro .= '   AND recurso_destinacao.exercicio = '.Sessao::getExercicio();
                                    $obErro = $obTOrcamentoRecursoDestinacao->recuperaTodos($rsDestinacao, $stFiltro, '', $boTransacao);
                                    $inCodEspecificacao = $rsDestinacao->getCampo('cod_especificacao');
                                    
                                    // Verifica qual o cod_recurso que possui conta contabil vinculada
                                    $obTOrcamentoRecursoDestinacao->setDado('exercicio', Sessao::getExercicio());
                                    $obTOrcamentoRecursoDestinacao->setDado('cod_especificacao', $inCodEspecificacao);
                                    if ( Sessao::getExercicio() > '2012' ) {
                                        $obTOrcamentoRecursoDestinacao->setDado('cod_estrutural', "'8.2.1.1.1.%'");
                                    } else {
                                        $obTOrcamentoRecursoDestinacao->setDado('cod_estrutural', "'2.9.3.2.0.00.00.%'");
                                    }
                                    $obTOrcamentoRecursoDestinacao->recuperaRecursoVinculoConta($rsContaRecurso, '', '', $boTransacao);
                                    
                                    $inCodRecurso = $rsContaRecurso->getCampo('cod_recurso');
                                }
                                
                                if (!$obErro->ocorreu() && $inCodRecurso != '') {
                                    $obRContabilidadePlanoBanco->obROrcamentoRecurso->setCodRecurso($inCodRecurso);
                                    $obErro = $obRContabilidadePlanoBanco->getContasRecurso($rsContasRecurso, $boTransacao);
                                    $inCodPlanoUm = $rsContasRecurso->getCampo('cod_plano_um');
                                    $inCodPlanoDois = $rsContasRecurso->getCampo('cod_plano_dois');
                                    if (!$obErro->ocorreu() && $inCodPlanoUm != '' && $inCodPlanoDois != '') {
                                        if ($this->inCodTipoTransferencia == 1) { // Pagamento Extra
                                            $this->obRContabilidadeLancamentoValor->setContaDebito($inCodPlanoDois);
                                            $this->obRContabilidadeLancamentoValor->setContaCredito($inCodPlanoUm);
                                        } elseif ($this->inCodTipoTransferencia == 2) { // Arrecadacao Extra
                                            $this->obRContabilidadeLancamentoValor->setContaDebito($inCodPlanoUm);
                                            $this->obRContabilidadeLancamentoValor->setContaCredito($inCodPlanoDois);
                                        }
                                        $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setDtLote($this->roRTesourariaBoletim->getDataBoletim());
                                        $obErro = $this->obRContabilidadeLancamentoValor->incluir($boTransacao);
                                    }
                                }
                            }//if recurso
                         } //if 1 ou 2
                        }
                    }
                }
            }
        }
    }
    
    SistemaLegado::LiberaFrames();
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTTesourariaTransferencia );

    return $obErro;
}

/**
    * Estorna Transferencia
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function estornar($boTransacao = "")
{
    include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaTransferenciaEstornada.class.php" );
    $obTransacao                         = new Transacao();
    $obTTesourariaTransferenciaEstornada = new TTesourariaTransferenciaEstornada();
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->roRTesourariaBoletim->incluir( $boTransacao );

        if ( !$obErro->ocorreu() ) {
                $this->obRContabilidadeLancamentoValorEstornada->obRContabilidadeLancamento->obRContabilidadeLote->setTipo( "T" );
                $obErro = $this->obRContabilidadeLancamentoValorEstornada->obRContabilidadeLancamento->obRContabilidadeLote->buscaProximoCodigo($boTransacao);

                if ( !$obErro->ocorreu() ) {
                    $this->obRContabilidadeLancamentoValorEstornada->obRContabilidadeLancamento->obRContabilidadeLote->setDtLote($this->roRTesourariaBoletim->getDataBoletim());
                    $this->obRContabilidadeLancamentoValorEstornada->obRContabilidadeLancamento->obRContabilidadeLote->setNomLote("Transferência - CD:".$this->obRContabilidadeLancamentoValorEstornada->getContaDebito()." | CC:".$this->obRContabilidadeLancamentoValorEstornada->getContaCredito());
                    $obErro = $this->obRContabilidadeLancamentoValorEstornada->incluir($boTransacao);

                    if ( !$obErro->ocorreu() ) {
                        $this->obRTesourariaAutenticacao->setTipo("E");
                        $this->obRTesourariaAutenticacao->setDataAutenticacao( $this->roRTesourariaBoletim->getDataBoletim() );
                        $this->obRTesourariaAutenticacao->obRTesourariaConfiguracao->setExercicio($this->roRTesourariaBoletim->getExercicio());
                        $obErro = $this->obRTesourariaAutenticacao->autenticar($boTransacao);
                        if ( !$obErro->ocorreu() ) {
                            $obTTesourariaTransferenciaEstornada->setDado( 'cod_lote_estorno'      , $this->obRContabilidadeLancamentoValorEstornada->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() );
                            $obTTesourariaTransferenciaEstornada->setDado( 'exercicio'               , $this->roRTesourariaBoletim->getExercicio()          );
                            $obTTesourariaTransferenciaEstornada->setDado( 'cod_entidade'            , $this->obRContabilidadeLancamentoValorEstornada->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
                            $obTTesourariaTransferenciaEstornada->setDado( 'tipo'                    , $this->obRContabilidadeLancamentoValorEstornada->obRContabilidadeLancamento->obRContabilidadeLote->getTipo() );
                            $obTTesourariaTransferenciaEstornada->setDado( 'cod_autenticacao'        , $this->obRTesourariaAutenticacao->getCodAutenticacao()  );
                            $obTTesourariaTransferenciaEstornada->setDado( 'dt_autenticacao'         , $this->roRTesourariaBoletim->getDataBoletim()  );
                            $obTTesourariaTransferenciaEstornada->setDado( 'cod_lote'                , $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() );
                            $obTTesourariaTransferenciaEstornada->setDado( 'cod_boletim'             , $this->roRTesourariaBoletim->getCodBoletim()         );
                            $obTTesourariaTransferenciaEstornada->setDado( 'cod_historico'           , $this->obRContabilidadeLancamentoValorEstornada->obRContabilidadeLancamento->obRContabilidadeHistoricoPadrao->getCodHistorico() );
                            $obTTesourariaTransferenciaEstornada->setDado( 'cod_terminal'            , $this->roRTesourariaBoletim->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->getCodTerminal() );
                            $obTTesourariaTransferenciaEstornada->setDado( 'timestamp_terminal'      , $this->roRTesourariaBoletim->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->getTimestampTerminal() );
                            $obTTesourariaTransferenciaEstornada->setDado( 'cgm_usuario'             , $this->roRTesourariaBoletim->obRTesourariaUsuarioTerminal->obRCGM->getNumCgm() );
                            $obTTesourariaTransferenciaEstornada->setDado( 'timestamp_usuario'       , $this->roRTesourariaBoletim->obRTesourariaUsuarioTerminal->getTimestampUsuario() );
                            $obTTesourariaTransferenciaEstornada->setDado( 'timestamp_estornada'     , $this->stTimestampEstornada );
                            $obTTesourariaTransferenciaEstornada->setDado( 'observacao'              , $this->stObservacaoEstorno );
                            $obTTesourariaTransferenciaEstornada->setDado( 'valor'                   , $this->obRContabilidadeLancamentoValorEstornada->getValor() );
                            $obErro = $obTTesourariaTransferenciaEstornada->inclusao( $boTransacao );
                            if ( !$obErro->ocorreu() ) {
                                $this->montaDescricaoAutenticacao();
                            }

                            if ($this->inCodTipoTransferencia == 1 || $this->inCodTipoTransferencia == 2) {
                            if (!$obErro->ocorreu() && Sessao::getExercicio() > '2008') {
                                if ($this->inCodTipoTransferencia == 1) { // Pagamento Extra
                                    $inCodConta = $this->obRContabilidadeLancamentoValorEstornada->getContaCredito();
                                    $inCodContaBanco = $this->obRContabilidadeLancamentoValorEstornada->getContaDebito();
                                } elseif ($this->inCodTipoTransferencia == 2) { // Arrecadação Extra
                                    $inCodConta = $this->obRContabilidadeLancamentoValorEstornada->getContaDebito();
                                    $inCodContaBanco = $this->obRContabilidadeLancamentoValorEstornada->getContaCredito();
                                }

                                include_once CAM_GF_CONT_NEGOCIO.'RContabilidadePlanoBanco.class.php';
                                $obRContabilidadePlanoBanco  =  new RContabilidadePlanoBanco;
                                $obRContabilidadePlanoBanco->setCodPlano($inCodConta);
                                $obErro = $obRContabilidadePlanoBanco->getRecursoVinculoConta($rsCodRecurso, $boTransacao);
                                $inCodRecurso = $rsCodRecurso->getCampo('cod_recurso');

                                if ($inCodRecurso == '' && $inCodContaBanco != '') {
                                    $obRContabilidadePlanoBanco->setCodPlano($inCodContaBanco);
                                    $obErro = $obRContabilidadePlanoBanco->getRecursoVinculoConta($rsCodRecurso, $boTransacao);
                                    $inCodRecurso = $rsCodRecurso->getCampo('cod_recurso');
                                }

                                $boDestinacao = false;
                                include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoConfiguracao.class.php";
                                $obTOrcamentoConfiguracao = new TOrcamentoConfiguracao;
                                $obTOrcamentoConfiguracao->setDado("exercicio", Sessao::getExercicio() );
                                $obTOrcamentoConfiguracao->setDado("parametro","recurso_destinacao");
                                $obTOrcamentoConfiguracao->consultar($boTransacao);
                                if($obTOrcamentoConfiguracao->getDado("valor") == 'true')
                                    $boDestinacao = true;

                                if ($boDestinacao && $inCodRecurso != '') {
                                    include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecursoDestinacao.class.php";
                                    $obTOrcamentoRecursoDestinacao = new TOrcamentoRecursoDestinacao;
                                    $obTOrcamentoRecursoDestinacao->setDado("exercicio", Sessao::getExercicio());

                                    $stFiltro  = ' WHERE recurso_destinacao.cod_recurso = '.$inCodRecurso;
                                    $stFiltro .= '   AND recurso_destinacao.exercicio = '.Sessao::getExercicio();
                                    $obErro = $obTOrcamentoRecursoDestinacao->recuperaTodos($rsDestinacao, $stFiltro, '', $boTransacao);
                                    $inCodEspecificacao = $rsDestinacao->getCampo('cod_especificacao');

                                    // Verifica qual o cod_recurso que possui conta contabil vinculada
                                    $obTOrcamentoRecursoDestinacao->setDado('exercicio', Sessao::getExercicio());
                                    $obTOrcamentoRecursoDestinacao->setDado('cod_especificacao', $inCodEspecificacao);
                                    if ( Sessao::getExercicio() > '2012' ) {
                                        $obTOrcamentoRecursoDestinacao->setDado('cod_estrutural', "'8.2.1.1.1.%'");
                                    } else {
                                        $obTOrcamentoRecursoDestinacao->setDado('cod_estrutural', "'2.9.3.2.0.00.00.%'");
                                    }
                                    $obTOrcamentoRecursoDestinacao->recuperaRecursoVinculoConta($rsContaRecurso, '', '', $boTransacao);

                                    $inCodRecurso = $rsContaRecurso->getCampo('cod_recurso');
                                }

                                if (!$obErro->ocorreu() && $inCodRecurso != '') {
                                    $obRContabilidadePlanoBanco->obROrcamentoRecurso->setCodRecurso($inCodRecurso);
                                    $obErro = $obRContabilidadePlanoBanco->getContasRecurso($rsContasRecurso, $boTransacao);
                                    $inCodPlanoUm = $rsContasRecurso->getCampo('cod_plano_um');
                                    $inCodPlanoDois = $rsContasRecurso->getCampo('cod_plano_dois');
                                    if (!$obErro->ocorreu() && $inCodPlanoUm != '' && $inCodPlanoDois != '') {
                                        if ($this->inCodTipoTransferencia == 1) { // Pagamento Extra
                                            $this->obRContabilidadeLancamentoValorEstornada->setContaDebito($inCodPlanoUm);
                                            $this->obRContabilidadeLancamentoValorEstornada->setContaCredito($inCodPlanoDois);
                                        } elseif ($this->inCodTipoTransferencia == 2) {
                                            $this->obRContabilidadeLancamentoValorEstornada->setContaDebito($inCodPlanoDois);
                                            $this->obRContabilidadeLancamentoValorEstornada->setContaCredito($inCodPlanoUm);
                                        }

                                        $this->obRContabilidadeLancamentoValorEstornada->obRContabilidadeLancamento->obRContabilidadeLote->setDtLote($this->roRTesourariaBoletim->getDataBoletim());
                                        $obErro = $this->obRContabilidadeLancamentoValorEstornada->incluir($boTransacao);
                                    }
                                }
                            } //if 2008
                         } // if 1 ou 2
                        }
                    }
                }
        }
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTTesourariaTransferenciaEstornada );

    return $obErro;
}

/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listar(&$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
{
    include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaTransferencia.class.php"          );
    $obTTesourariaTransferencia          = new TTesourariaTransferencia();

    if( $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() )
        $stFiltro .= " cod_lote = " .$this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote(). " AND";

    if( $this->roRTesourariaBoletim->getExercicio() )
        $stFiltro .= " exercicio = '" .$this->roRTesourariaBoletim->getExercicio()."' AND";

    if( $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() )
        $stFiltro .= " cod_entidade IN (" .$this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade(). ") AND";

    if( $this->obRContabilidadeLancamentoValorEstornada->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() )
        $stFiltro .= " cod_lote_estorno = " .$this->obRContabilidadeLancamentoValorEstornada->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote(). " AND";

    if( $this->roRTesourariaBoletim->getCodBoletim() )
        $stFiltro .= " cod_boletim = " .$this->roRTesourariaBoletim->getCodBoletim()." AND";

    if( $this->roRTesourariaBoletim->getDataBoletim() )
        $stFiltro .= " dt_boletim = '".$this->roRTesourariaBoletim->getDataBoletim()."' AND";

    if( $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeHistoricoPadrao->getCodHistorico() )
        $stFiltro .= " cod_historico = " .$this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeHistoricoPadrao->getCodHistorico()." AND";

    if( $this->stObservacaoTransferencia )
        $stFiltro .= " observacao ilike '%" .$this->stObservacaoTransferencia."'% AND";

    if( $this->stObservacaoEstorno )
        $stFiltro .= " observacao_estornada ilike '%" .$this->stObservacaoEstorno."'% AND";

    if( $this->obRContabilidadeLancamentoValor->getContaCredito() )
        $stFiltro .= " cod_plano_credito = " .$this->obRContabilidadeLancamentoValor->getContaCredito()." AND";

    if( $this->obRContabilidadeLancamentoValor->getContaDebito() )
        $stFiltro .= " cod_plano_debito = " .$this->obRContabilidadeLancamentoValor->getContaDebito()." AND";

    if(substr($stFiltro,-3)=="AND")
        $stFiltro = " WHERE ".substr($stFiltro,0,strlen($stFiltro)-3);

    $obErro = $obTTesourariaTransferencia->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

function listarTransferenciaAtiva(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro .= " cod_lote_estorno  is null AND ";
    $obErro = $this->listar( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

function montaDescricaoAutenticacao($boTransacao = "")
{
    $stTipo = $this->obRTesourariaAutenticacao->getTipo();
    switch ($this->inCodTipoTransferencia) {
        case 1: // Pagamento Extra
            if ($stTipo == 'T') {
                $stAcao = "PAG_EXT";
                $inSub = 9;
                $stCorpo = wordwrap("Pagamento extra-orçamentário no valor de R$ ".number_format($this->obRContabilidadeLancamentoValor->getValor(),2,',','.')." (".extenso($this->obRContabilidadeLancamentoValor->getValor())." ), efetuado nesta data, entre as seguintes contas:", 60, "\\n")."\\n\\n";
            }
            if ($stTipo == 'E') {
                $stAcao = "EPAG_EXT";
                $inSub = 8;
                $stCorpo = wordwrap("Estorno de pagamento extra-orçamentário no valor de R$ ".number_format($this->obRContabilidadeLancamentoValorEstornada->getValor(),2,',','.')." (".extenso($this->obRContabilidadeLancamentoValorEstornada->getValor())." ), efetuado nesta data, entre as seguintes contas:", 60, "\\n")."\\n\\n";
            }
        break;

        case 2: // Arrecadação Extra
            if ($stTipo == 'T') {
                $stAcao = "ARR_EXT ";
                $inSub = 9;
                $stCorpo = wordwrap("Arrecadação extra-orçamentária no valor de R$ ".number_format($this->obRContabilidadeLancamentoValor->getValor(),2,',','.')." (".extenso($this->obRContabilidadeLancamentoValor->getValor())." ), efetuada nesta data, entre as seguintes contas:", 60, "\\n")."\\n\\n";
            }
            if ($stTipo == 'E') {
                $stAcao = "EARR_EXT ";
                $inSub = 8;
                $stCorpo = wordwrap("Estorno de arrecadação extra-orçamentária no valor de R$ ".number_format($this->obRContabilidadeLancamentoValorEstornada->getValor(),2,',','.')." (".extenso($this->obRContabilidadeLancamentoValorEstornada->getValor())." ), efetuado nesta data, entre as seguintes contas:", 60, "\\n")."\\n\\n";
            }
        break;

        case 3: // Aplicação
            $stAcao = "APLIC";
            $inSub = 11;
            $stCorpo = wordwrap("Aplicação no valor de R$ ".number_format($this->obRContabilidadeLancamentoValor->getValor(),2,',','.')." (".extenso($this->obRContabilidadeLancamentoValor->getValor())." ), efetuada nesta data, entre as seguintes contas:", 60, "\\n")."\\n\\n";
        break;
        case 4: // Resgate
            $stAcao = "RESGATE";
            $inSub = 9;
            $stCorpo = wordwrap("Resgate no valor de R$ ".number_format($this->obRContabilidadeLancamentoValor->getValor(),2,',','.')." (".extenso($this->obRContabilidadeLancamentoValor->getValor())." ), efetuado nesta data, entre as seguintes contas:", 60, "\\n")."\\n\\n";

        break;
        case 5: // Depósito/Retirada
            $stAcao = "DEP_RET";
            $inSub = 9;
            $stCorpo = wordwrap("Deposito/Retirada no valor de R$ ".number_format($this->obRContabilidadeLancamentoValor->getValor(),2,',','.')." (".extenso($this->obRContabilidadeLancamentoValor->getValor())." ), efetuado(a) nesta data, entre as seguintes contas:", 60, "\\n")."\\n\\n";

        break;
        default:
            $stAcao = "TRANSF";
            $inSub = 10;
            $stCorpo = wordwrap("Transferência no valor de R$ ".number_format($this->obRContabilidadeLancamentoValor->getValor(),2,',','.')." (".extenso($this->obRContabilidadeLancamentoValor->getValor())." ), efetuada nesta data, entre as seguintes contas:", 60, "\\n")."\\n\\n";
        break;

    }
    if ($this->obRTesourariaAutenticacao->obRTesourariaConfiguracao->getFormaComprovacao()==2) {
        $stDescricao = chr(15).$this->obRTesourariaAutenticacao->obRTesourariaConfiguracao->getDigitos();
        $inCodAutenticacao = $this->obRTesourariaAutenticacao->getCodAutenticacao();
        $stDescricao .= str_pad($inCodAutenticacao, 6, "0", STR_PAD_LEFT) . " ";
        $stDescricao .= substr($this->obRTesourariaAutenticacao->getDataAutenticacao(),0,6) . substr($this->obRTesourariaAutenticacao->getDataAutenticacao(),8,2)." ";
        $stDescricao .= $stAcao;

        if ($this->obRContabilidadeLancamentoValor->getValor()) {
            $stDescricao .= $this->obRContabilidadeLancamentoValor->getContaDebito()."/". substr($this->roRTesourariaBoletim->getExercicio(), 2, 2) . " ";
            $stDescricao .= $this->obRContabilidadeLancamentoValor->getContaCredito()."/". substr($this->roRTesourariaBoletim->getExercicio(), 2, 2);
            $stDescricao = str_pad($stDescricao, ($inSub - strlen($this->obRContabilidadeLancamentoValor->getContaDebito()) - strlen($this->obRContabilidadeLancamentoValor->getContaCredito())) , " ") . " ";
            $inCodContaCredito = $this->obRContabilidadeLancamentoValor->getContaDebito();
            $nuValor = number_format($this->obRContabilidadeLancamentoValor->getValor(),2,',','.');
        } else {
            $stDescricao .= $this->obRContabilidadeLancamentoValorEstornada->getContaDebito()."/". substr($this->roRTesourariaBoletim->getExercicio(), 2, 2) . " ";
            $stDescricao .= $this->obRContabilidadeLancamentoValorEstornada->getContaCredito()."/". substr($this->roRTesourariaBoletim->getExercicio(), 2, 2);
            $stDescricao = str_pad($stDescricao, ($inSub - strlen($this->obRContabilidadeLancamentoValorEstornada->getContaDebito()) - strlen($this->obRContabilidadeLancamentoValorEstornada->getContaCredito())) , " ") . " ";
            $inCodContaCredito = $this->obRContabilidadeLancamentoValorEstornada->getContaCredito();
            $nuValor = number_format($this->obRContabilidadeLancamentoValorEstornada->getValor(),2,',','.');
        }
        $stDescricao .= str_pad($nuValor, 14, "*", STR_PAD_LEFT) . " \\n \\r";
        $stDescricao .= $inCodContaCredito . '-' . tiraAcentos($this->getNomContaCredito()) . "\\n \\r";

        $this->obRTesourariaAutenticacao->setDescricao(array($stDescricao));
    } else {
        $this->obRTesourariaAutenticacao->montaComprovante($cabecalho, $rodape, $boTransacao );

        $corpo = $stCorpo;

        $corpo .= str_pad("Conta", 10, " ", STR_PAD_BOTH);
        $corpo .= str_pad("Descrição", 35, " ", STR_PAD_BOTH);
        $corpo .= str_pad("Valor", 15, " ", STR_PAD_BOTH)."\\n";

        // CONTA CRÉDITO //
        if($stTipo == 'T') $corpo .= str_pad($this->obRContabilidadeLancamentoValor->getContaCredito()."/". substr($this->roRTesourariaBoletim->getExercicio(), 2, 2), 10, " ", STR_PAD_BOTH);
        if($stTipo == 'E') $corpo .= str_pad($this->obRContabilidadeLancamentoValorEstornada->getContaCredito()."/". substr($this->roRTesourariaBoletim->getExercicio(), 2, 2), 10, " ", STR_PAD_BOTH);
        $contaCredito = wordwrap($this->getNomContaCredito(), 35, "\n");
        $arContaCredito = explode("\n",$contaCredito);
        $corpo .= str_pad($arContaCredito[0], 35, " ");

        if($stTipo == 'T') $corpo .= str_pad(number_format($this->obRContabilidadeLancamentoValor->getValor(),2,',','.')." C", 15, " ", STR_PAD_LEFT)."\\n";
        if($stTipo == 'E') $corpo .= str_pad(number_format($this->obRContabilidadeLancamentoValorEstornada->getValor(),2,',','.')." C", 15, " ", STR_PAD_LEFT)."\\n";

        for ($i = 1; $i<count($arContaCredito); $i++) {
            $corpo .= str_pad("", 10, " ", STR_PAD_BOTH);
            $corpo .= str_pad($arContaCredito[$i], 35, " ");
            $corpo .= str_pad("", 15, " ", STR_PAD_LEFT)."\\n";
        }

        // CONTA DEBITO //
        if($stTipo == 'T') $corpo .= str_pad($this->obRContabilidadeLancamentoValor->getContaDebito()."/". substr($this->roRTesourariaBoletim->getExercicio(), 2, 2), 10, " ", STR_PAD_BOTH);
        if($stTipo == 'E') $corpo .= str_pad($this->obRContabilidadeLancamentoValorEstornada->getContaDebito()."/". substr($this->roRTesourariaBoletim->getExercicio(), 2, 2), 10, " ", STR_PAD_BOTH);

        $contaDebito = wordwrap($this->getNomContaDebito(), 35, "\n");
        $arContaDebito = explode("\n",$contaDebito);
        $corpo .= str_pad($arContaDebito[0], 35, " ");

        if ($stTipo == 'T') $corpo .= str_pad(number_format($this->obRContabilidadeLancamentoValor->getValor(),2,',','.')." D", 15, " ", STR_PAD_LEFT)."\\n";
        if ($stTipo == 'E') $corpo .= str_pad(number_format($this->obRContabilidadeLancamentoValorEstornada->getValor(),2,',','.')." D", 15, " ", STR_PAD_LEFT)."\\n";

        for ($i = 1; $i<count($arContaDebito); $i++) {
            $corpo .= str_pad("", 10, " ", STR_PAD_BOTH);
            $corpo .= str_pad($arContaDebito[$i], 35, " ");
            $corpo .= str_pad("", 15, " ", STR_PAD_LEFT)."\\n";
        }

        $stDescricao = chr(15).$cabecalho . $corpo . "\\n \\r" . $rodape;

        $this->obRTesourariaAutenticacao->setDescricao(array(tiraAcentos($stDescricao)."\\n\\n\\n\\n\\n\\n\\n\\n\\n"));

    }
}
function getBoletimTransferencia(&$rsBoletins , $stOrder = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql  = "      select boletim.cod_boletim \r\n";
    $stSql .= "           , to_char( dt_boletim ,'dd/mm/YYYY') as dt_boletim\r\n";
    $stSql .= "        from tesouraria.transferencia \r\n";
    $stSql .= "  inner join tesouraria.boletim \r\n";
    $stSql .= "          on boletim.cod_boletim  = transferencia.cod_boletim  \r\n";
    $stSql .= "         and boletim.cod_entidade = transferencia.cod_entidade  \r\n";
    $stSql .= "         and boletim.exercicio    = transferencia.exercicio     \r\n";
    $stSql .= "       where transferencia.cod_lote        = " . $this->obRContabilidadeLancamentoValorEstornada->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() . " \r\n";
    $stSql .= "         and transferencia.cod_entidade    = " . $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() . " \r\n";
    $stSql .= "         and transferencia.exercicio       = '" . $this->roRTesourariaBoletim->getExercicio() . "' \r\n";
    $stSql .= "         and transferencia.tipo            = '" . $this->obRContabilidadeLancamentoValorEstornada->obRContabilidadeLancamento->obRContabilidadeLote->getTipo() . "' \r\n";

    $obErro = $obConexao->executaSQL( $rsBoletins , $stSql , $boTransacao );

    return $obErro;
}

}
