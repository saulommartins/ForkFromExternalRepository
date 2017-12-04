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
    * Classe de Regra de Negócio para Pagamento
    * Data de Criação   : 20/10/2005

    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Regra

    $Id: RTesourariaPagamento.class.php 64371 2016-01-28 16:55:09Z franver $

    $Revision: 32136 $
    $Name:  $
    $Autor:$
    $Date: 2007-10-18 12:08:09 -0200 (Qui, 18 Out 2007) $

    * Casos de uso: uc-02.04.05 , uc-02.04.25
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GF_TES_NEGOCIO   ."RTesourariaAutenticacao.class.php"                               );
include_once ( CAM_GF_EMP_NEGOCIO   ."REmpenhoPagamentoLiquidacao.class.php"                           );
include_once ( CAM_GF_CONT_NEGOCIO  ."RContabilidadePlanoBanco.class.php"                             );

/**
    * Classe de Regra de Pagamento
    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Anderson R. M. Buzo
*/
class RTesourariaPagamento
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
var $obTransacao;
/*
    * @var Object
    * @access Private
*/
var $roRTesourariaBoltim;
/*
    * @var Object
    * @access Private
*/
var $obREmpenhoPagamentoLiquidacao;
/*
    * @var Object
    * @access Private
*/
var $obRContabilidadePlanoBanco;
/*
    * @var String
    * @access Private
*/
var $stTimestamp;
/*
    * @var String
    * @access Private
*/
var $stTimestampEstornado;
var $inCodPlanoRetencao;
var $boRetencaoExecutada;
var $boCheque;
var $stTipoOrdem;
/*
    * @access Public
    * @param Object $valor
*/
function setRTesourariaAutenticacao($valor) { $this->obRTesourariaAutenticacao            = $valor; }
/*
    * @access Public
    * @param Object $valor
*/
function setREmpenhoPagamentoLiquidacao($valor) { $this->obREmpenhoPagamentoLiquidacao       = $valor; }
/*
    * @access Public
    * @param Object $valor
*/
function setRContabilidadePlanoBanco($valor) { $this->obRContabilidadePlanoBanco          = $valor; }
/*
    * @access Public
    * @param String $valor
*/
function setTimestamp($valor) { $this->stTimestamp                = $valor; }
/*
    * @access Public
    * @param String $valor
*/
function setTimestampEstornado($valor) { $this->stTimestampEstornado                = $valor; }
/*
    * @access Public
    * @param Array $valor
*/
function setContaBanco($valor) { $this->arContaBanco                        = $valor; }

/*
    * @access Public
    * @return Object
*/
function getRTesourariaAutenticacao() { return $this->obRTesourariaAutenticacao;                }
/*
    * @access Public
    * @return Object
*/
function getREmpenhoPagamentoLiquidacao() { return $this->obREmpenhoPagamentoLiquidacao;       }
/*
    * @access Public
    * @return Object
*/
function getRContabilidadePlanoBanco() { return $this->obRContabilidadePlanoBanco;          }
/*
    * @access Public
    * @return String
*/
function getTimestamp() { return $this->stTimestamp;                }
/*
    * @access Public
    * @return String
*/
function getTimestampEstornado() { return $this->stTimestampEstornado;                }
/*
    * @access Public
    * @return Array
*/
function getContaBanco() { return $this->arContaBanco;                        }

/**
    * Método Construtor
    * @access Private
*/
function RTesourariaPagamento($roRTesourariaBoletim)
{
    $this->obRTesourariaAutenticacao       = new RTesourariaAutenticacao();
    $this->obREmpenhoPagamentoLiquidacao   = new REmpenhoPagamentoLiquidacao();
    $this->obRContabilidadePlanoBanco      = new RContabilidadePlanoBanco();
    $this->roRTesourariaBoletim            = &$roRTesourariaBoletim;
    $this->boRetencaoExecutada             = false;
}

/**
    * Salva os dados no banco de dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function pagar($boTransacao = "")
{
    include_once ( CAM_FW_BANCO_DADOS    ."Transacao.class.php"            );
    include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaPagamento.class.php" );
    $obErro = new Erro;
    $this->obTransacao                     = new Transacao();
    $arNotasPagas = $this->obREmpenhoPagamentoLiquidacao->getValoresPagos();
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->roRTesourariaBoletim->incluir( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obRContabilidadePlanoBanco->setCodPlano ( $this->obREmpenhoPagamentoLiquidacao->obRContabilidadePlanoContaAnalitica->getCodPlano()  );
            $this->obRContabilidadePlanoBanco->setExercicio( $this->obREmpenhoPagamentoLiquidacao->obRContabilidadePlanoContaAnalitica->getExercicio() );
            $this->obRContabilidadePlanoBanco->consultar( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $inCodEntidade = $this->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->obROrcamentoEntidade->getCodigoEntidade();
                if ( $inCodEntidade != $this->obRContabilidadePlanoBanco->obROrcamentoEntidade->getCodigoEntidade() and $this->obRContabilidadePlanoBanco->obROrcamentoEntidade->getCodigoEntidade()>0) {
                    $obErro->setDescricao( 'A entidade da conta é diferente da entidade do pagamento!' );
                }
            }
            if ( !$obErro->ocorreu() ) {
                foreach ( $this->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->getNotaLiquidacao() as $arNota ) {
                    $nuValor = str_replace( '.', '' , $arNota['valor_pagar'] );
                    $nuValor = str_replace( ',', '.', $nuValor );
                    $nuVlTotal = bcadd( $nuVlTotal, $nuValor, 4 );
                }
            }
        }
        if ( !$obErro->ocorreu() ) {
            $this->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->setDataEmissao( $this->roRTesourariaBoletim->getDataBoletim() );
            $this->obREmpenhoPagamentoLiquidacao->setDataPagamento( $this->roRTesourariaBoletim->getDataBoletim() );
            $this->obREmpenhoPagamentoLiquidacao->setTimestamp( $this->stTimestamp );
            if ( !trim($this->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->getCodigoOrdem()) ) {
                $this->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->setExercicio($this->roRTesourariaBoletim->getExercicio());
                $this->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->setTipo('A');
                $obErro = $this->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->incluir( $boTransacao, false );
            }
            $this->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->setNotaLiquidacao( array() );
            $this->obREmpenhoPagamentoLiquidacao->setTesouraria ( true );
            if ( !$obErro->ocorreu() ) {
                $obErro = $this->obREmpenhoPagamentoLiquidacao->pagarOP( $boTransacao );
            }
            if ( !$obErro->ocorreu() ) {
                $this->obRTesourariaAutenticacao->setTipo("P");
                $this->obRTesourariaAutenticacao->setDataAutenticacao( $this->roRTesourariaBoletim->getDataBoletim() );
                $this->obRTesourariaAutenticacao->obRTesourariaConfiguracao->setExercicio($this->roRTesourariaBoletim->getExercicio());
                $obErro = $this->obRTesourariaAutenticacao->autenticar($boTransacao);
                $inCodAutenticacao = $this->obRTesourariaAutenticacao->getCodAutenticacao();
                if ( !$obErro->ocorreu() ) {
                    foreach ($arNotasPagas as $arNota) {
                        if ($arNota['vl_pago'] > 0.00) {
                            $obTTesourariaPagamento = new TTesourariaPagamento();
                            $obTTesourariaPagamento->setDado( 'cod_nota'           , $arNota['cod_nota'] );
                            $obTTesourariaPagamento->setDado( 'cod_entidade'       , $arNota['cod_entidade'] );
                            $obTTesourariaPagamento->setDado( 'exercicio_boletim'  , $this->roRTesourariaBoletim->getExercicio()          );
                            $obTTesourariaPagamento->setDado( 'exercicio'          , $arNota['exercicio'] );
                            $obTTesourariaPagamento->setDado( 'timestamp'          , $this->stTimestamp );
                            $obTTesourariaPagamento->setDado( 'cod_autenticacao'   , $this->obRTesourariaAutenticacao->getCodAutenticacao()  );
                            $obTTesourariaPagamento->setDado( 'dt_autenticacao'    , $this->roRTesourariaBoletim->getDataBoletim()        );
                            $obTTesourariaPagamento->setDado( 'cod_boletim'        , $this->roRTesourariaBoletim->getCodBoletim()         );
                            $obTTesourariaPagamento->setDado( 'cod_terminal'       , $this->roRTesourariaBoletim->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->getCodTerminal() );
                            $obTTesourariaPagamento->setDado( 'timestamp_terminal' , $this->roRTesourariaBoletim->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->getTimestampTerminal() );
                            $obTTesourariaPagamento->setDado( 'cgm_usuario'        , $this->roRTesourariaBoletim->obRTesourariaUsuarioTerminal->obRCGM->getNumCgm() );
                            $obTTesourariaPagamento->setDado( 'timestamp_usuario'  , $this->roRTesourariaBoletim->obRTesourariaUsuarioTerminal->getTimestampUsuario() );
                            $obTTesourariaPagamento->setDado( 'cod_plano'          , $this->obREmpenhoPagamentoLiquidacao->obRContabilidadePlanoContaAnalitica->getCodPlano() );
                            $obTTesourariaPagamento->setDado( 'exercicio_plano'    , $this->obREmpenhoPagamentoLiquidacao->obRContabilidadePlanoContaAnalitica->getExercicio() );
                            $obErro = $obTTesourariaPagamento->inclusao( $boTransacao );
                            if ( $obErro->ocorreu() ) {
                                break;
                            }
                        }
                    }
                }
            }

            /* RETENÇÕES */
            $boRetencao = $this->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->getRetencao();
            $arDescricao = array();
            if (!$obErro->ocorreu() && $boRetencao && !$this->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->boRetencaoExecutada) {
                include_once(CAM_GA_ADM_MAPEAMENTO.'TAdministracaoConfiguracaoEntidade.class.php');
                include_once(CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamentoRetencao.class.php" );
                $obTAdministracaoConfiguracaoEntidade = new TAdministracaoConfiguracaoEntidade;
            
                $stExercicio = $this->roRTesourariaBoletim->getExercicio();
                $inCodOrdem = $this->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->getCodigoOrdem();
                $inCodEntidade = $this->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->obROrcamentoEntidade->getCodigoEntidade();
            
                $stFiltroConta = " WHERE parametro = 'conta_caixa' AND cod_entidade = ".$inCodEntidade." AND exercicio = '".$stExercicio."' ";
                $obErro = $obTAdministracaoConfiguracaoEntidade->recuperaTodos($rsContas, $stFiltroConta, '', $boTransacao);
            
                if (!$obErro->ocorreu() && !$rsContas->EOF() && $rsContas->getNumLinhas() == 1) {
                    include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadePlanoContaAnalitica.class.php"     );
                    $obContaAnalitica = new RContabilidadePlanoContaAnalitica;
                    $obContaAnalitica->setCodPlano( $rsContas->getCampo('valor') );
                    $obContaAnalitica->setExercicio( $stExercicio );
                    $obErro = $obContaAnalitica->consultar( $boTransacao );
                    $stCodEstruturalCaixa = $obContaAnalitica->getCodEstrutural();
                    $inCodPlanoCaixa = $obContaAnalitica->getCodPlano();
                }
            
                if ($inCodPlanoCaixa && $stCodEstruturalCaixa && !$obErro->ocorreu()) {
                    include_once(CAM_GF_TES_MAPEAMENTO."TTesourariaTransferenciaOrdemPagamentoRetencao.class.php" );
                    include_once(CAM_GF_TES_MAPEAMENTO."TTesourariaArrecadacaoOrdemPagamentoRetencao.class.php" );
                    $obTTesourariaArrecadacaoOPRetencao   = new TTesourariaArrecadacaoOrdemPagamentoRetencao;
                    $obTTesourariaTransferenciaOPRetencao = new TTesourariaTransferenciaOrdemPagamentoRetencao;
                    $obTContabilidadeLancamentoRetencao = new TContabilidadeLancamentoRetencao;
            
                    /* Lança na tesouraria os Pagamentos das Retenções feitos no modulo empennho */
                    foreach (  $this->obREmpenhoPagamentoLiquidacao->getPagamentosRetencao() as $arPgtoRetencao ) {
            
                        $obTTesourariaPagamento->setDado( 'cod_nota'           , $arPgtoRetencao['cod_nota'] );
                        $obTTesourariaPagamento->setDado( 'cod_entidade'       , $arPgtoRetencao['cod_entidade'] );
                        $obTTesourariaPagamento->setDado( 'exercicio_boletim'  , $this->roRTesourariaBoletim->getExercicio()          );
                        $obTTesourariaPagamento->setDado( 'exercicio'          , $arPgtoRetencao['exercicio'] );
                        $obTTesourariaPagamento->setDado( 'timestamp'          , $arPgtoRetencao['timestamp'] );
                        $obTTesourariaPagamento->setDado( 'cod_autenticacao'   , $this->obRTesourariaAutenticacao->getCodAutenticacao()  );
                        $obTTesourariaPagamento->setDado( 'dt_autenticacao'    , $this->roRTesourariaBoletim->getDataBoletim()        );
                        $obTTesourariaPagamento->setDado( 'cod_boletim'        , $this->roRTesourariaBoletim->getCodBoletim()         );
                        $obTTesourariaPagamento->setDado( 'cod_terminal'       , $this->roRTesourariaBoletim->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->getCodTerminal() );
                        $obTTesourariaPagamento->setDado( 'timestamp_terminal' , $this->roRTesourariaBoletim->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->getTimestampTerminal() );
                        $obTTesourariaPagamento->setDado( 'cgm_usuario'        , $this->roRTesourariaBoletim->obRTesourariaUsuarioTerminal->obRCGM->getNumCgm() );
                        $obTTesourariaPagamento->setDado( 'timestamp_usuario'  , $this->roRTesourariaBoletim->obRTesourariaUsuarioTerminal->getTimestampUsuario() );
                        $obTTesourariaPagamento->setDado( 'cod_plano'          , $arPgtoRetencao['cod_plano'] );
                        $obTTesourariaPagamento->setDado( 'exercicio_plano'    , $stExercicio );
                        $obErro = $obTTesourariaPagamento->inclusao( $boTransacao );
            
                        if ( $obErro->ocorreu() ) {
                            break;
                        }
                    }
                }
                $inCount = 1;
                /* Realiza as Arrecadações das Retenções */
                
                foreach (  $this->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->getRetencoes() as $arRetencao ) {
                    if ($this->boCheque == false) {
                        $nuVlTotal = bcsub( $nuVlTotal, $arRetencao['vl_retencao'],4 );
                    }
                    
                    $this->obRTesourariaAutenticacao->setTipo("P");
                    $this->obRTesourariaAutenticacao->setDataAutenticacao( $this->roRTesourariaBoletim->getDataBoletim() );
                    $this->obRTesourariaAutenticacao->obRTesourariaConfiguracao->setExercicio($this->roRTesourariaBoletim->getExercicio());
                    $obErro = $this->obRTesourariaAutenticacao->autenticar($boTransacao);
                    $stDescricao = '';
                    $this->obRContabilidadePlanoBanco->setCodPlano($inCodPlanoCaixa);
                    $this->obRContabilidadePlanoBanco->setCodEstrutural('');
                    $this->obRContabilidadePlanoBanco->setNomConta('');
        
                    $this->montaDescricaoAutenticacao(number_format($arRetencao['vl_retencao'],4,',','.'),$boTransacao);
                    
                    array_unshift($arDescricao,array(
                        'stDescricao' => array( 'texto' => $this->obRTesourariaAutenticacao->getDescricao(),
                                                'acao'  => "OP ".$inCodEntidade."-".$inCodOrdem."/".substr($stExercicio,2,2) . ' RET ' . $inCount++ . ' ' . number_format($arRetencao['vl_retencao'],2,',','.'))
                                                    )
                    );
                    
                    if(!$obErro->ocorreu()&&($arRetencao['exercicio']!=$stExercicio))
                        $obErro->setDescricao( 'É necessário anular a Ordem de Pagamento desse empenho e emitir novamente no exercício atual. OP com retenção de exercício anterior não pode ser paga no exercício atual!  ' );
        
                    
                    switch ($arRetencao['tipo']) {
                        case 'O':  // Retenção Receita Orçamentária
                            $this->roRTesourariaBoletim->addArrecadacao();
                            $this->roRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoReceita->setCodReceita          ( $arRetencao['cod_receita'] );
                            $this->roRTesourariaBoletim->roUltimaArrecadacao->setTimestampArrecadacao                     ( $this->stTimestamp );
                            $this->roRTesourariaBoletim->roUltimaArrecadacao->obRContabilidadePlanoBanco->setCodPlano     ( $inCodPlanoCaixa );
                            $this->roRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoEntidade->setCodigoEntidade     ( $inCodEntidade   );
                            $this->roRTesourariaBoletim->roUltimaArrecadacao->setObservacao                               ( "Arrecadação por Retenção Orçamentária - OP ".$inCodOrdem."/".$stExercicio );
                            $this->roRTesourariaBoletim->roUltimaArrecadacao->setVlArrecadacao                            ( $arRetencao['vl_retencao'] );
                            if (!$obErro->ocorreu()) {
                                $obErro = $this->roRTesourariaBoletim->arrecadar( $boTransacao );
                                if (!$obErro->ocorreu()) {
                                    $obTTesourariaArrecadacaoOPRetencao->setDado('timestamp_arrecadacao' , $this->stTimestamp );
                                    $obTTesourariaArrecadacaoOPRetencao->setDado('exercicio', $arRetencao['exercicio'] );
                                    $obTTesourariaArrecadacaoOPRetencao->setDado('cod_entidade', $arRetencao['cod_entidade'] );
                                    $obTTesourariaArrecadacaoOPRetencao->setDado('cod_arrecadacao', $this->roRTesourariaBoletim->roUltimaArrecadacao->getCodArrecadacao() );
                                    $obTTesourariaArrecadacaoOPRetencao->setDado('cod_plano', $arRetencao['cod_plano'] );
                                    $obTTesourariaArrecadacaoOPRetencao->setDado('cod_ordem', $inCodOrdem );
                                    $obTTesourariaArrecadacaoOPRetencao->setDado('sequencial', $arRetencao['sequencial'] );
                                    $obErro = $obTTesourariaArrecadacaoOPRetencao->inclusao( $boTransacao );
                                }
                            }
                            if ($this->roRTesourariaBoletim->roUltimaArrecadacao->obRTesourariaAutenticacao->getDescricao()) {
                                array_push($arDescricao,array(
                                    'stDescricao' => array( 'texto' => $this->roRTesourariaBoletim->roUltimaArrecadacao->obRTesourariaAutenticacao->getDescricao(),
                                                            'acao'  => "ARR RECEITA ".$arRetencao['cod_receita']."/".substr(Sessao::getExercicio(),2,2))
                                                              )
                                );
                            }
                            unset($this->roRTesourariaBoletim->roUltimaArrecadacao);
                            unset($this->roRTesourariaBoletim->arArrecadacao);
                        break;
        
                        case 'E': // Retenção Receita Extra-Orçamentária
                            $this->obRContabilidadePlanoBanco->setCodPlano($inCodPlanoCaixa);
                            $this->obRContabilidadePlanoBanco->listarContaAnalitica($rsPlanoBanco,'',$boTransacao);
        
                            $this->roRTesourariaBoletim->addTransferencia();
                            $this->roRTesourariaBoletim->roUltimaTransferencia->setNomContaCredito($rsPlanoBanco->getCampo('nom_conta'));
                            $this->roRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio( $stExercicio );
                            $this->roRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $inCodEntidade );
                            $this->roRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValor->setContaDebito   ( $inCodPlanoCaixa );
                            $this->roRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValor->setContaCredito  ( $arRetencao['cod_plano'] );
                            $this->roRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValor->setValor         ( $arRetencao['vl_retencao'] );
                            $this->roRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeHistoricoPadrao->setCodHistorico( 952 );
                            $this->roRTesourariaBoletim->roUltimaTransferencia->setObservacaoTransferencia( $inCodOrdem."/".$stExercicio );
                            $this->roRTesourariaBoletim->roUltimaTransferencia->setTipoTransferencia  ( 2 );
                            $this->roRTesourariaBoletim->roUltimaTransferencia->setCodRecibo($arRetencao['cod_recibo_extra']);
                            $this->roRTesourariaBoletim->roUltimaTransferencia->setTipoRecibo($arRetencao['tipo_recibo']);
                            $this->roRTesourariaBoletim->roUltimaTransferencia->setTimestampTransferencia( $this->stTimestamp );
                            
                            if (! $obErro->ocorreu() ) {
                                $obErro = $this->roRTesourariaBoletim->roUltimaTransferencia->transferir( $boTransacao );
                                $inSequenciaRet = $this->roRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->getSequencia();
                                $inCodLoteRet   = $this->roRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote();
                                
                                if (!$obErro->ocorreu()) {
                                    $obTContabilidadeLancamentoRetencao->setDado('tipo'              , 'T' );
                                    $obTContabilidadeLancamentoRetencao->setDado('cod_lote'          , $inCodLoteRet );
                                    $obTContabilidadeLancamentoRetencao->setDado('cod_entidade'      , $arRetencao['cod_entidade'] );
                                    $obTContabilidadeLancamentoRetencao->setDado('exercicio'         , $stExercicio );
                                    $obTContabilidadeLancamentoRetencao->setDado('sequencia'         , $inSequenciaRet );
                                    $obTContabilidadeLancamentoRetencao->setDado('cod_ordem'         , $arRetencao['cod_ordem'] );
                                    $obTContabilidadeLancamentoRetencao->setDado('cod_plano'         , $arRetencao['cod_plano'] );
                                    $obTContabilidadeLancamentoRetencao->setDado('exercicio_retencao', $arRetencao['exercicio'] );
                                    $obTContabilidadeLancamentoRetencao->setDado('sequencial'        , $arRetencao['sequencial'] );
                                    $obErro = $obTContabilidadeLancamentoRetencao->inclusao( $boTransacao );
                               
                                    if (!$obErro->ocorreu()) {
                                        $obTTesourariaTransferenciaOPRetencao->setDado('tipo' , 'T' );
                                        $obTTesourariaTransferenciaOPRetencao->setDado('exercicio', $arRetencao['exercicio'] );
                                        $obTTesourariaTransferenciaOPRetencao->setDado('cod_entidade', $inCodEntidade );
                                        $obTTesourariaTransferenciaOPRetencao->setDado('cod_lote', $inCodLoteRet );
                                        $obTTesourariaTransferenciaOPRetencao->setDado('cod_plano', $arRetencao['cod_plano'] );
                                        $obTTesourariaTransferenciaOPRetencao->setDado('cod_ordem', $inCodOrdem );
                                        $obTTesourariaTransferenciaOPRetencao->setDado('sequencial', $arRetencao['sequencial'] );
                                        $obErro = $obTTesourariaTransferenciaOPRetencao->inclusao( $boTransacao );
                                    }
                                }
                            }
        
                            if ($this->roRTesourariaBoletim->roUltimaTransferencia->obRTesourariaAutenticacao->getDescricao()) {
                                array_push($arDescricao,array('stDescricao' => array( 'texto' => $this->roRTesourariaBoletim->roUltimaTransferencia->obRTesourariaAutenticacao->getDescricao(), 'acao'  => "ARR_EXT ".$inCodPlanoCaixa."/".substr(Sessao::getExercicio(),2,2)." ".$arRetencao['cod_plano']."/".substr($arRetencao['exercicio'],2,2))));
                            }
                            
                        break;
                    } // Fim switch tipo de Retenção
                } // Fim foreach nas retenções da OP
                
                Sessao::write('stDescricao', $arDescricao);
            } // Fim se achou as contas de caixa configuradas pra entidade
            
            Sessao::write('retencoes', true); // Para uso na montagem das autenticações.
        } // Fim retenções
        
        if ( !$obErro->ocorreu() ) {
            $this->obRContabilidadePlanoBanco->setCodPlano($this->obREmpenhoPagamentoLiquidacao->obRContabilidadePlanoContaAnalitica->getCodPlano());
            $this->obRTesourariaAutenticacao->setCodAutenticacao($inCodAutenticacao);
            $obErro = $this->montaDescricaoAutenticacao(number_format($nuVlTotal,4,',','.'), $boTransacao);
            array_unshift($arDescricao,array('stDescricao' => array( 'texto' => $this->obRTesourariaAutenticacao->getDescricao(), 'acao'  => "OP ".$inCodEntidade."-".$inCodOrdem."/".substr($stExercicio,2,2) . ' ' . number_format($nuVlTotal,2,',','.'))));
        }
            
        Sessao::write('stDescricao', $arDescricao);
    }

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTTesourariaPagamento );

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
    include_once ( CAM_FW_BANCO_DADOS    ."Transacao.class.php"                     );
    include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaPagamentoEstornado.class.php" );
    include_once ( CAM_GF_EMP_MAPEAMENTO ."TEmpenhoOrdemPagamentoLiquidacaoAnulada.class.php" );
    $boFlagTransacao = false;
    $arNotasPagas = $this->obREmpenhoPagamentoLiquidacao->getValoresPagos();
    $this->obTransacao                     = new Transacao();
    $nuVlTotal = 0;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->roRTesourariaBoletim->incluir( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obREmpenhoPagamentoLiquidacao->setDataAnulacao( $this->roRTesourariaBoletim->getDataBoletim() );
            $this->obREmpenhoPagamentoLiquidacao->setTesouraria ( true );

            $obErro = $this->obREmpenhoPagamentoLiquidacao->estornarOP( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obRTesourariaAutenticacao->setTipo("PE");
                $this->obRTesourariaAutenticacao->setDataAutenticacao( $this->roRTesourariaBoletim->getDataBoletim() );
                $this->obRTesourariaAutenticacao->obRTesourariaConfiguracao->setExercicio($this->roRTesourariaBoletim->getExercicio());
                $obErro = $this->obRTesourariaAutenticacao->autenticar($boTransacao);

                $stTimestampEstornadoBak = $this->stTimestampEstornado;

                if ( !$obErro->ocorreu() ) {
                    $arEstornoRetencao = $this->obREmpenhoPagamentoLiquidacao->arPagamentosRetencao;
                    if ( !is_array($arEstornoRetencao) ) {
                      $arEstornoRetencao = array();
                    }
                    foreach ($arNotasPagas as $arNota) {
                        foreach ($arNota as $inKey => $stValue) {
                        }

                        if ($arNota['vl_estornado'] > 0) {
                            $nuVlTotal = bcadd( $nuVlTotal, $arNota['vl_estornado'], 4 );
                            foreach ($arEstornoRetencao as $arEstorno) {
                                if ($arEstorno['cod_nota']  == $arNota['cod_nota'] && $arEstorno['timestamp'] == $arNota['timestamp']
                                    && $arEstorno['exercicio'] == $arNota['exercicio']) {
                                    $this->stTimestampEstornado = $arEstorno['timestamp_anulada'];
                                    break;
                                } else $this->stTimestampEstornado = $stTimestampEstornadoBak;
                            }
                            $obTTesourariaPagamentoEstornado = new TTesourariaPagamentoEstornado();
                            $obTTesourariaPagamentoEstornado->setDado( 'cod_nota'           , $arNota['cod_nota'] );
                            $obTTesourariaPagamentoEstornado->setDado( 'cod_entidade'       , $arNota['cod_entidade'] );
                            $obTTesourariaPagamentoEstornado->setDado( 'exercicio_boletim'  , $this->roRTesourariaBoletim->getExercicio()  );
                            $obTTesourariaPagamentoEstornado->setDado( 'exercicio'          , $arNota['exercicio'] );
                            $obTTesourariaPagamentoEstornado->setDado( 'timestamp_anulado'  , $this->stTimestampEstornado                 );
                            $obTTesourariaPagamentoEstornado->setDado( 'timestamp'          , $arNota['timestamp']                        );
                            $obTTesourariaPagamentoEstornado->setDado( 'cod_autenticacao'   , $this->obRTesourariaAutenticacao->getCodAutenticacao()  );
                            $obTTesourariaPagamentoEstornado->setDado( 'dt_autenticacao'    , $this->roRTesourariaBoletim->getDataBoletim()           );
                            $obTTesourariaPagamentoEstornado->setDado( 'cod_boletim'        , $this->roRTesourariaBoletim->getCodBoletim() );
                            $obTTesourariaPagamentoEstornado->setDado( 'cod_terminal'       , $this->roRTesourariaBoletim->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->getCodTerminal() );
                            $obTTesourariaPagamentoEstornado->setDado( 'timestamp_terminal' , $this->roRTesourariaBoletim->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->getTimestampTerminal() );
                            $obTTesourariaPagamentoEstornado->setDado( 'cgm_usuario'        , $this->roRTesourariaBoletim->obRTesourariaUsuarioTerminal->obRCGM->getNumCgm() );
                            $obTTesourariaPagamentoEstornado->setDado( 'timestamp_usuario'  , $this->roRTesourariaBoletim->obRTesourariaUsuarioTerminal->getTimestampUsuario() );

                            $obOPLA = new TEmpenhoOrdemPagamentoLiquidacaoAnulada();
                            $obOPLA->setDado( 'exercicio'           , $this->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->getExercicio() );
                            $obOPLA->setDado( 'cod_entidade'        , $arNota['cod_entidade'] );
                            $obOPLA->setDado( 'cod_ordem'           , $this->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->getCodigoOrdem() );
                            $obOPLA->setDado( 'exercicio_liquidacao', $arNota['exercicio'] );
                            $obOPLA->setDado( 'cod_nota'            , $arNota['cod_nota'] );
                            $obOPLA->setDado( 'vl_anulado'          , $arNota['vl_estornado'] );

                            $arOPLA[] = $obOPLA;

                            $obErro = $obTTesourariaPagamentoEstornado->inclusao( $boTransacao );
                            if ( $obErro->ocorreu() ) {
                                break;
                            }
                        }
                    }
                    $this->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->setOrdemPagamentoLiquidacaoAnulada( $arOPLA );
                    if( !$obErro->ocorreu() )
                        $obErro = $this->montaDescricaoAutenticacao($this->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->getValorAnulado(),$boTransacao);
                }
            }
            /* ESTORNO DE RETENCOES */
            $boRetencao = $this->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->getRetencao();
            $inCodEntidade = isset($inCodEntidade) ? $inCodEntidade : 0;
            $inCodOrdem    = isset($inCodOrdem)  ? $inCodOrdem  : 0;
            $stExercicio   = isset($stExercicio) ? $stExercicio : null;
            $arDescricao   = isset($arDescricao) ? $arDescricao : array();
            if (!$obErro->ocorreu() && $boRetencao ) {
                include_once(CAM_GA_ADM_MAPEAMENTO.'TAdministracaoConfiguracaoEntidade.class.php');
                include_once(CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamentoRetencao.class.php" );
                include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadePlanoContaAnalitica.class.php"     );
                include_once(CAM_GF_TES_MAPEAMENTO."TTesourariaArrecadacaoEstornadaOrdemPagamentoRetencao.class.php" );
                include_once(CAM_GF_TES_MAPEAMENTO."TTesourariaTransferenciaEstornadaOrdemPagamentoRetencao.class.php" );
                $obTAdministracaoConfiguracaoEntidade = new TAdministracaoConfiguracaoEntidade;
                $obContaAnalitica = new RContabilidadePlanoContaAnalitica;
                $obTTesourariaArrecadacaoEOPRetencao = new TTesourariaArrecadacaoEstornadaOrdemPagamentoRetencao;
                $obTContabilidadeLancamentoRetencao = new TContabilidadeLancamentoRetencao;
                $obTTesourariaTransferenciaEOPRetencao = new TTesourariaTransferenciaEstornadaOrdemPagamentoRetencao;

                $stExercicio = $this->roRTesourariaBoletim->getExercicio();
                $inCodEntidade = $this->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->obROrcamentoEntidade->getCodigoEntidade();
                $inCodOrdem = $this->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->getCodigoOrdem();

                $stFiltroConta = " WHERE parametro = 'conta_caixa' AND cod_entidade = ".$inCodEntidade." AND exercicio = '".$stExercicio."' ";
                $obErro = $obTAdministracaoConfiguracaoEntidade->recuperaTodos($rsContas, $stFiltroConta, '', $boTransacao);
                if (!$obErro->ocorreu() && !$rsContas->EOF() && $rsContas->getNumLinhas() == 1) {
                    $obContaAnalitica->setCodPlano( $rsContas->getCampo('valor') );
                    $obContaAnalitica->setExercicio( Sessao::getExercicio() );
                    $obErro = $obContaAnalitica->consultar( $boTransacao );
                    $stCodEstruturalCaixa = $obContaAnalitica->getCodEstrutural();
                    $inCodPlanoCaixa = $obContaAnalitica->getCodPlano();
                }
                $inCount = 1;
                if ($inCodPlanoCaixa && $stCodEstruturalCaixa && !$obErro->ocorreu()) {
                    foreach (  $this->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->getRetencoes() as $arRetencao ) {

                        $nuVlTotal = bcsub( $nuVlTotal, $arRetencao['vl_retencao'],4 );

                        $this->obRTesourariaAutenticacao->setTipo("PE");
                        $this->obRTesourariaAutenticacao->setDataAutenticacao( $this->roRTesourariaBoletim->getDataBoletim() );
                        $this->obRTesourariaAutenticacao->obRTesourariaConfiguracao->setExercicio($this->roRTesourariaBoletim->getExercicio());
                        $obErro = $this->obRTesourariaAutenticacao->autenticar($boTransacao);

                        $this->obRContabilidadePlanoBanco->setCodPlano($inCodPlanoCaixa);
                        $this->obRContabilidadePlanoBanco->setCodEstrutural('');
                        $this->obRContabilidadePlanoBanco->setNomConta('');

                        $this->montaDescricaoAutenticacao(number_format($arRetencao['vl_retencao'],4,',','.'),$boTransacao);
                        array_unshift($arDescricao,array(
                            'stDescricao' => array( 'texto' => $this->obRTesourariaAutenticacao->getDescricao(),
                                                    'acao'  => "OP ANULADA ".$inCodEntidade."-".$inCodOrdem."/".substr($stExercicio,2,2) . ' RET ' . $inCount++ . ' ' . number_format($arRetencao['vl_retencao'],2,',','.'))
                                                        )
                        );

                        switch ($arRetencao['tipo']) {
                            case 'O': // Retenção Receita Orçamentária
                                $this->roRTesourariaBoletim->addArrecadacao();
                                $this->roRTesourariaBoletim->roUltimaArrecadacao->setCodArrecadacao                      ($arRetencao['cod_arrecadacao']       );
                                $this->roRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoReceita->setCodReceita     ($arRetencao['cod_receita']          );
                                $this->roRTesourariaBoletim->roUltimaArrecadacao->setTimestampArrecadacao                ($arRetencao['timestamp_arrecadacao'] );
                                $this->roRTesourariaBoletim->roUltimaArrecadacao->setTimestampEstornada                  ($this->stTimestampEstornado         );
                                $this->roRTesourariaBoletim->roUltimaArrecadacao->setVlEstornado                         ($arRetencao['vl_retencao']          );
                                $this->roRTesourariaBoletim->roUltimaArrecadacao->obRContabilidadePlanoBanco->setCodPlano($arRetencao['cod_plano']);
                                $obErro = $this->roRTesourariaBoletim->roUltimaArrecadacao->estornar( $boTransacao );
                                if (!$obErro->ocorreu()) {
                                    $obTTesourariaArrecadacaoEOPRetencao->setDado('timestamp_arrecadacao' , $arRetencao['timestamp_arrecadacao'] );
                                    $obTTesourariaArrecadacaoEOPRetencao->setDado('timestamp_estornada', $this->roRTesourariaBoletim->roUltimaArrecadacao->getTimestampEstornada() );
                                    $obTTesourariaArrecadacaoEOPRetencao->setDado('exercicio', $arRetencao['exercicio'] );
                                    $obTTesourariaArrecadacaoEOPRetencao->setDado('cod_entidade', $inCodEntidade );
                                    $obTTesourariaArrecadacaoEOPRetencao->setDado('cod_arrecadacao', $arRetencao['cod_arrecadacao'] );
                                    $obTTesourariaArrecadacaoEOPRetencao->setDado('cod_plano', $arRetencao['cod_plano'] );
                                    $obTTesourariaArrecadacaoEOPRetencao->setDado('cod_ordem', $inCodOrdem );
                                    $obTTesourariaArrecadacaoEOPRetencao->setDado('sequencial', $arRetencao['sequencial'] );
                                    $obErro = $obTTesourariaArrecadacaoEOPRetencao->inclusao( $boTransacao );
                                }
                                $this->roRTesourariaBoletim->roUltimaArrecadacao->obRContabilidadePlanoBanco->setCodPlano($inCodPlanoCaixa);
                                $this->roRTesourariaBoletim->roUltimaArrecadacao->montaDescricaoAutenticacao($boTransacao);
                                if ($this->roRTesourariaBoletim->roUltimaArrecadacao->obRTesourariaAutenticacao->getDescricao()) {
                                    $arAux = $this->roRTesourariaBoletim->roUltimaArrecadacao->obRTesourariaAutenticacao->getDescricao();
                                    array_push($arDescricao,array(
                                        'stDescricao' => array( 'texto' => $this->roRTesourariaBoletim->roUltimaArrecadacao->obRTesourariaAutenticacao->getDescricao(),
                                                                'acao'  => "EARR RECEITA ".$arRetencao['cod_receita']."/".substr(Sessao::getExercicio(),2,2))
                                                                    )
                                    );
                                }
                                unset($this->roRTesourariaBoletim->arArrecadacao);
                                unset($this->roRTesourariaBoletim->roUltimaArrecadacao);
                            break;

                            case 'E': // Retenção Receita Extra-Orçamentária
                                $this->obRContabilidadePlanoBanco->setCodPlano($inCodPlanoCaixa);
                                $this->obRContabilidadePlanoBanco->listarContaAnalitica($rsPlanoBanco,'',$boTransacao);

                                $this->roRTesourariaBoletim->addTransferencia();
                                $this->roRTesourariaBoletim->roUltimaTransferencia->setNomContaCredito($rsPlanoBanco->getCampo('nom_conta'));
                                $this->roRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote( $arRetencao['cod_lote'] );
                                $this->roRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValorEstornada->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio( $stExercicio );
                                $this->roRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValorEstornada->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $inCodEntidade );
                                $this->roRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValorEstornada->setContaDebito   ( $arRetencao['cod_plano']  );
                                $this->roRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValorEstornada->setContaCredito  ( $inCodPlanoCaixa );
                                $this->roRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValorEstornada->setValor         ( $arRetencao['vl_retencao'] );
                                $this->roRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValorEstornada->obRContabilidadeLancamento->obRContabilidadeHistoricoPadrao->setCodHistorico( 953 );
                                $this->roRTesourariaBoletim->roUltimaTransferencia->setObservacaoEstorno( $inCodOrdem."/".$stExercicio );
                                $this->roRTesourariaBoletim->roUltimaTransferencia->setTipoTransferencia( 2 );
                                $this->roRTesourariaBoletim->roUltimaTransferencia->setTimestampEstornada( $this->stTimestampEstornado );
                                $obErro = $this->roRTesourariaBoletim->roUltimaTransferencia->estornar( $boTransacao );
                                $inSequenciaRet = $this->roRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValorEstornada->obRContabilidadeLancamento->getSequencia();
                                $inCodLoteRet   = $this->roRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValorEstornada->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote();
                                if (!$obErro->ocorreu()) {
                                    $obTContabilidadeLancamentoRetencao->setDado('tipo'              , 'T' );
                                    $obTContabilidadeLancamentoRetencao->setDado('cod_lote'          , $inCodLoteRet  );
                                    $obTContabilidadeLancamentoRetencao->setDado('cod_entidade'      , $inCodEntidade );
                                    $obTContabilidadeLancamentoRetencao->setDado('exercicio'         , $stExercicio   );
                                    $obTContabilidadeLancamentoRetencao->setDado('sequencia'         , $inSequenciaRet );
                                    $obTContabilidadeLancamentoRetencao->setDado('cod_ordem'         , $inCodOrdem );
                                    $obTContabilidadeLancamentoRetencao->setDado('cod_plano'         , $arRetencao['cod_plano'] );
                                    $obTContabilidadeLancamentoRetencao->setDado('exercicio_retencao', $arRetencao['exercicio'] );
                                    $obTContabilidadeLancamentoRetencao->setDado('estorno'           , true );
                                    $obTContabilidadeLancamentoRetencao->setDado('sequencial'        , $arRetencao['sequencial']);
                                    $obErro = $obTContabilidadeLancamentoRetencao->inclusao( $boTransacao );

                                    if (!$obErro->ocorreu()) {
                                        $obTTesourariaTransferenciaEOPRetencao->setDado('tipo' , 'T' );
                                        $obTTesourariaTransferenciaEOPRetencao->setDado('exercicio', $arRetencao['exercicio'] );
                                        $obTTesourariaTransferenciaEOPRetencao->setDado('cod_entidade', $arRetencao['cod_entidade'] );
                                        $obTTesourariaTransferenciaEOPRetencao->setDado('cod_lote_estorno', $inCodLoteRet );
                                        $obTTesourariaTransferenciaEOPRetencao->setDado('cod_plano', $arRetencao['cod_plano'] );
                                        $obTTesourariaTransferenciaEOPRetencao->setDado('cod_ordem', $arRetencao['cod_ordem'] );
                                        $obTTesourariaTransferenciaEOPRetencao->setDado('cod_lote', $arRetencao['cod_lote'] );
                                        $obTTesourariaTransferenciaEOPRetencao->setDado('sequencial', $arRetencao['sequencial'] );
                                        $obErro = $obTTesourariaTransferenciaEOPRetencao->inclusao( $boTransacao );
                                    }
                                }
                                if ( !$obErro->ocorreu() ) {
                                    if ($this->roRTesourariaBoletim->roUltimaTransferencia->obRTesourariaAutenticacao->getDescricao()) {
                                        array_push($arDescricao,array(
                                            'stDescricao' => array( 'texto' => $this->roRTesourariaBoletim->roUltimaTransferencia->obRTesourariaAutenticacao->getDescricao(),
                                                                    'acao'  => "EARR_EXT ".$inCodPlanoCaixa."/".substr(Sessao::getExercicio(),2,2)." ".$arRetencao['cod_plano']."/".substr(Sessao::getExercicio(),2,2))
                                                                     )
                                        );
                                    }
                                }
                            break;
                        } // Fim switch tipo de retenção
                    } // Fim foreach nas Retenções
                    //Sessao::write('stDescricao',$arDescricao);
                } // Fim se encontrou a conta de caixa para a entidade da OP
            Sessao::write('retencoes', true); // Para uso na montagem das autenticações.
            } // Fim estorno de retenções
            if ( !$obErro->ocorreu() ) {
                $obErro = $this->consultarTipoOrdem( $boTransacao );
                if ( !$obErro->ocorreu() and $this->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->getTipo() == 'A' ) {
                    $obErro = $this->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->anular( $boTransacao );
                }
                $this->obRContabilidadePlanoBanco->setCodPlano($arNotasPagas[0]['cod_plano']);
                $obErro = $this->montaDescricaoAutenticacao(number_format($nuVlTotal,4,',','.'), $boTransacao);
                array_unshift($arDescricao,array(
                    'stDescricao' => array( 'texto' => $this->obRTesourariaAutenticacao->getDescricao(),
                                            'acao'  => "OP ANULADA ".$inCodEntidade."-".$inCodOrdem."/".substr($stExercicio,2,2) . ' ' . number_format($nuVlTotal,2,',','.'))
                                                )
                );
            }
            Sessao::write('stDescricao', $arDescricao);
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro , $obTTesourariaPagamentoEstornado );

    return $obErro;
}

/**
    * Método para recuperar os dados do pagamento
    * @access Public
    * @param Object $obTransacao
    * @return Object $obErro
*/
function consultarTipoOrdem($boTransacao = "")
{
    include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaPagamento.class.php" );
    $obTTesourariaPagamento = new TTesourariaPagamento();

    $obTTesourariaPagamento->setDado( 'cod_ordem'    , $this->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->getCodigoOrdem() );
    $obTTesourariaPagamento->setDado( 'cod_entidade' , $this->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->obROrcamentoEntidade->getCodigoEntidade() );
    $obTTesourariaPagamento->setDado( 'exercicio'    , $this->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->getExercicio() );
    $obErro = $obTTesourariaPagamento->recuperaTipoOrdem( $rsRecordSet, $boTransacao );

    if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
        $this->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->setTipo( $rsRecordSet->getCampo( 'tipo_ordem' ) );
    }

    return $obErro;
}

function consultar($boTransacao = "")
{
    include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaPagamento.class.php" );
    $obTTesourariaPagamento = new TTesourariaPagamento();

    $obTTesourariaPagamento->setDado( 'cod_nota'     , $this->obREmpenhoPagamentoLiquidacao->obREmpenhoNotaLiquidacao->inCodNota );
    $obTTesourariaPagamento->setDado( 'cod_entidade' , $this->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->obROrcamentoEntidade->getCodigoEntidade() );
    $obTTesourariaPagamento->setDado( 'exercicio'    , $this->obREmpenhoPagamentoLiquidacao->obREmpenhoNotaLiquidacao->getExercicio() );
    $obTTesourariaPagamento->setDado( 'timestamp'    , $this->obREmpenhoPagamentoLiquidacao->obREmpenhoNotaLiquidacao->getTimestamp() );
    $obErro = $obTTesourariaPagamento->recuperaPorChave( $rsRecordSet, $boTransacao );
    if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
        $this->obRContabilidadePlanoBanco->setCodPlano ( $rsRecordSet->getCampo("cod_plano") );
        $this->obRContabilidadePlanoBanco->setExercicio( $rsRecordSet->getCampo("exercicio_plano") );
        $this->obRContabilidadePlanoBanco->consultar ($boTransacao);
        if ($this->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->getRetencao()) {
            $obErro = $obTTesourariaPagamento->recuperaCodPlanoRetencao( $rsRetencao, $boTransacao );
            if (!$obErro->ocorreu() and !$rsRetencao->eof()) {
               $this->inCodPlanoRetencao = $rsRetencao->getCampo('cod_plano');
            }
        }
    }

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
    include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaPagamento.class.php" );
    $obTTesourariaPagamento = new TTesourariaPagamento();
    $obErro = $obTTesourariaPagamento->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaRelacionamento na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarPagamentos(&$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
{
    include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaPagamento.class.php" );
    $obTTesourariaPagamento = new TTesourariaPagamento();
    $stFiltroPagTes = $stFiltro;

    $obTTesourariaPagamento->setDado('exercicio_boletim',$this->roRTesourariaBoletim->getExercicio());
    $obTTesourariaPagamento->setDado('cod_entidade',$this->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade());
    $obTTesourariaPagamento->setDado('cod_ordem_inicial',$this->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->getCodigoOrdemInicial());
    $obTTesourariaPagamento->setDado('cod_ordem_final',$this->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->getCodigoOrdemFinal());
    $obTTesourariaPagamento->setDado('cod_empenho_inicial',$this->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->roREmpenhoEmpenho->getCodEmpenhoInicial());
    $obTTesourariaPagamento->setDado('cod_empenho_final',$this->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->roREmpenhoEmpenho->getCodEmpenhoFinal());
    $obTTesourariaPagamento->setDado('exercicio_empenho',$this->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->roREmpenhoEmpenho->getExercicio());
    $obTTesourariaPagamento->setDado('cod_nota_inicial',$this->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->getCodNotaInicial());
    $obTTesourariaPagamento->setDado('cod_nota_final',$this->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->getCodNotaFinal());
    $obTTesourariaPagamento->setDado('num_cgm',$this->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->roREmpenhoEmpenho->obRCGM->getNumCgm());

    $obErro = $obTTesourariaPagamento->recuperaRelacionamento( $rsRecordSet, '', $stOrder, $boTransacao );

    return $obErro;
}

function listarPagamentosNaoAnulados(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    $obErro = $this->listarPagamentos( $rsRecordSet, $stFiltro, "cod_empenho", $boTransacao );

    return $obErro;
}

function montaDescricaoAutenticacao($nuVlTotal, $boTransacao = "", &$stDescricao = '')
{
    $obErro = new Erro;

    if ($this->obRTesourariaAutenticacao->obRTesourariaConfiguracao->getFormaComprovacao()==2) {
        $stDescricao = chr(15).$this->obRTesourariaAutenticacao->obRTesourariaConfiguracao->getDigitos();
        $inCodAutenticacao = $this->obRTesourariaAutenticacao->getCodAutenticacao();
        $stDescricao .= str_pad($inCodAutenticacao, 6, "0", STR_PAD_LEFT) . " ";
        $stDescricao .= substr($this->obRTesourariaAutenticacao->getDataAutenticacao(),0,6) . substr($this->obRTesourariaAutenticacao->getDataAutenticacao(),8,2)." ";
        if ($this->stTipoOrdem != "A") {
            if ($this->obRTesourariaAutenticacao->getTipo()=="P") {
                $stDescricao .= "OP ";
                $stDescricao .= $this->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->obROrcamentoEntidade->getCodigoEntidade() ."-".$this->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->getCodigoOrdem()."/".substr($this->roRTesourariaBoletim->getExercicio(),2,2)." ";
                $stDescricao = str_pad($stDescricao, (17 - strlen($this->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->obROrcamentoEntidade->getCodigoEntidade()) - strlen($this->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->getCodigoOrdem())) , " ") . " ";
                $nuValor = $nuVlTotal;
                $stDescricao .= str_pad(substr($nuValor,0,strlen($nuValor)-2), 14, "*", STR_PAD_LEFT) . "C\\n \\r";
                $this->obRContabilidadePlanoBanco->listarContaAnalitica($rsPlanoBanco,'',$boTransacao);
                $stDescricao .= $rsPlanoBanco->getCampo('cod_plano') . '-' . tiraAcentos($rsPlanoBanco->getCampo('nom_conta')) . "\\n \\r";

            } else {
                $stDescricao .= "OP ANULADA ";
                $stDescricao .= $this->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->obROrcamentoEntidade->getCodigoEntidade() ."-".$this->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->getCodigoOrdem()."/".substr($this->roRTesourariaBoletim->getExercicio(),2,2)." ";
                $stDescricao = str_pad($stDescricao, (9 - strlen($this->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->obROrcamentoEntidade->getCodigoEntidade()) - strlen($this->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->getCodigoOrdem())) , " ") . " ";
                $nuValor = $nuVlTotal;
                $stDescricao .= str_pad(substr($nuValor,0,strlen($nuValor)-2), 14, "*", STR_PAD_LEFT) . "C\\n \\r";
                $this->obRContabilidadePlanoBanco->listarContaAnalitica($rsPlanoBanco,'',$boTransacao);
                $stDescricao .= $rsPlanoBanco->getCampo('cod_plano') . '-' . tiraAcentos($rsPlanoBanco->getCampo('nom_conta')) . "\\n \\r";
            }
        }
        $this->obRTesourariaAutenticacao->setDescricao(array($stDescricao));
    } else {
        $this->obRTesourariaAutenticacao->montaComprovante($cabecalho, $rodape, $boTransacao );

        $this->obRTesourariaAutenticacao->obRTesourariaConfiguracao->setCodModulo( 2 );
        $this->obRTesourariaAutenticacao->obRTesourariaConfiguracao->setParametro( "nom_prefeitura");
        $this->obRTesourariaAutenticacao->obRTesourariaConfiguracao->setValor( null);
        $obErro = $this->obRTesourariaAutenticacao->obRTesourariaConfiguracao->consultar( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            if ($this->obRTesourariaAutenticacao->getTipo()=="P") {
                $nuVlTotal = substr($nuVlTotal,0,strlen($nuVlTotal)-2);

                if($this->stTipoOrdem!="A")
                    $corpo = wordwrap("Atesto que recebi(emos) da ".$this->obRTesourariaAutenticacao->obRTesourariaConfiguracao->getValor()." o valor de R$ ".number_format($nuVlTotal,2,',','.')."(".extenso($nuVlTotal)." ), relativos ao pagamento da OP ".$this->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->getCodigoOrdem()."/".substr($this->roRTesourariaBoletim->getExercicio(),2,2)." conforme discriminada abaixo:", 60, "\\n")."\\n\\n";
                else
                    $corpo = wordwrap("Atesto que recebi(emos) da ".$this->obRTesourariaAutenticacao->obRTesourariaConfiguracao->getValor()." o valor de R$ ".number_format($nuVlTotal,2,',','.')."(".extenso($nuVlTotal)." ), relativos ao pagamento do(s) empenho(s) conforme discriminado(s) abaixo:", 60, "\\n")."\\n\\n";
            } else {
                if($this->stTipoOrdem!="A")
                    $corpo = wordwrap("Estornado na ".$this->obRTesourariaAutenticacao->obRTesourariaConfiguracao->getValor()." o valor de R$ ".number_format($nuVlTotal,2,',','.')."(".extenso($nuVlTotal)." ), relativos ao cancelamento da OP ".$this->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->getCodigoOrdem()."/".substr($this->roRTesourariaBoletim->getExercicio(),2,2)." conforme discriminada abaixo:", 60, "\\n")."\\n\\n";
                else
                    $corpo = wordwrap("Estornado na ".$this->obRTesourariaAutenticacao->obRTesourariaConfiguracao->getValor()." o valor de R$ ".number_format($nuVlTotal,2,',','.')."(".extenso($nuVlTotal)." ), relativos ao cancelamento do(s) empenho(s) conforme discriminado(s) abaixo:", 60, "\\n")."\\n\\n";
            }

            $corpo .= str_pad("Empenho", 20, " ", STR_PAD_BOTH);
            $corpo .= str_pad("Liquidação", 20, " ", STR_PAD_BOTH);

            if($this->obRTesourariaAutenticacao->getTipo()=="P")
                $corpo .= str_pad("Valor Pago", 20, " ", STR_PAD_BOTH)."\\n";
            else
                $corpo .= str_pad("Valor Estornado", 20, " ", STR_PAD_BOTH)."\\n";

            foreach ( $this->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->getNotaLiquidacao() as $obNotaLiquidacao ) {
                if ($inCount == 0) {
                    $stCredor = $obNotaLiquidacao->roREmpenhoEmpenho->obRCGM->getNomCGM();
                }
                $corpo .= str_pad($obNotaLiquidacao->roREmpenhoEmpenho->getCodEmpenho()."/".substr($obNotaLiquidacao->roREmpenhoEmpenho->getDtEmpenho(),8,2), 20, " ", STR_PAD_BOTH);
                $corpo .= str_pad($obNotaLiquidacao->getCodNota()."/". substr($obNotaLiquidacao->getDtNota(), 8, 2), 20, " ", STR_PAD_BOTH);
                $corpo .= str_pad(number_format($obNotaLiquidacao->getValorTotal(),2,',','.'), 20, " ", STR_PAD_LEFT)."\\n";
            }

            if ($this->obRTesourariaAutenticacao->getTipo()=="P") {
                $corpo .= "\\n".str_pad("Total Pago", 40, " ",STR_PAD_LEFT);
                $corpo .= str_pad(number_format($nuVlTotal,2,',','.'), 20, " ", STR_PAD_LEFT)."\\n\\n";
            } else {
                $corpo .= "\\n".str_pad("Total Estornado", 40, " ",STR_PAD_LEFT);
                $corpo .= str_pad(number_format($nuVlTotal,2,',','.'), 20, " ", STR_PAD_LEFT)."\\n\\n";
            }

            $assinatura .= "\\n\\n\\n".str_pad($stCredor, 60, " ")."\\n \\r";

            if($this->obRTesourariaAutenticacao->getTipo()=="P")
                $stDescricao = chr(15).$cabecalho . $corpo . $rodape . $assinatura;
            else
                $stDescricao = chr(15).$cabecalho . $corpo . $rodape;

            $this->obRTesourariaAutenticacao->setDescricao(array(tiraAcentos($stDescricao)."\\n\\n\\n\\n\\n\\n\\n\\n\\n"));
        }

    }

    return $obErro;
}
}
