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
    * Classe de regra de negócio para lancamento de valores
    * Data de Criação: 01/11/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Marcelo Boezzio Paulino
    * @package URBEM
    * @subpackage Regra

    * $Id: RARRLancamento.class.php 63509 2015-09-04 14:32:22Z michel $

    * Casos de uso: uc-05.03.05
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRLancamento.class.php"            );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRLancamentoCalculo.class.php"     );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRLancamentoAcrescimo.class.php"   );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRLancamentoDesconto.class.php"    );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRLancamentoProcesso.class.php"    );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRLancamentoConcedeDesoneracao.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRParcela.class.php"               );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRParcelaDesconto.class.php"       );
include_once ( CAM_GT_ARR_MAPEAMENTO."FARRVerificaSuspensao.class.php"     );
include_once ( CAM_GT_ARR_FUNCAO."FNumeracaoBradesco.class.php"            );
include_once ( CAM_GT_ARR_FUNCAO."FFNCalculaDesoneracao.class.php"         );
include_once ( CAM_GT_ARR_NEGOCIO."RARRCalculo.class.php"                  );
include_once ( CAM_GT_ARR_NEGOCIO."RARRParcela.class.php"                  );
include_once ( CAM_GT_ARR_NEGOCIO."RARRSuspensao.class.php"                );
include_once ( CAM_GT_MON_NEGOCIO."RMONCredito.class.php"                  );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMInscricaoEconomica.class.php"       );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMImovel.class.php"                   );
include_once ( CAM_GA_CGM_NEGOCIO."RCGM.class.php"                         );
include_once ( CAM_GA_PROT_NEGOCIO."RProcesso.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );
require_once (CAM_GT_ARR_NEGOCIO."RARRDesoneracao.class.php");

/**
    * Classe de regra de negócio para lancamento de valores
    * Data de Criação: 01/11/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou
    * @package URBEM
    * @subpackage Regra
*/

class RARRLancamento
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
var $inTotalParcelasUnicas;
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
    * @var String
*/
var $stObservacaoSistema;
/**
    * @access Private
    * @var Float
*/
var $flValor;
var $flValorADesc;
/**
    * @access Private
    * @var Float
*/
var $flValorDesconto;
/**
    * @access Private
    * @var String
*/
var $stCodigoProcesso;
var $obTARRLancamentoUsaDesoneracao;
var $obRARRDesoneracao;

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
function setTotalParcelasUnicas($valor) { $this->inTotalParcelasUnicas          = $valor; }
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
    * @param String $valor
*/
function setObservacaoSistema($valor) { $this->stObservacaoSistema        = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setCodigoProcesso($valor) { $this->stCodigoProcesso = $valor; }
/**
    * @access Public
    * @param Float $valor
*/
function setValor($valor) { $this->flValor                   = $valor; }
function setValorADesc($valor) { $this->flValorADesc              = $valor; }
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
function getTotalParcelasUnicas() { return $this->inTotalParcelasUnicas;          }
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
    * @return String
*/
function getObservacaoSistema() { return $this->stObservacaoSistema;     }
/**
    * @access Public
    * @return String
*/
function getCodigoProcesso() { return $this->stCodigoProcesso;             }
/**
    * @access Public
    * @return Float
*/
function getValor() { return $this->flValor;                  }
function getValorADesc() { return $this->flValorADesc;             }
/**
    * @access Public
    * @return Float
*/
function getValorDesconto() { return $this->flValorDesconto;          }

/**
     * Método construtor
     * @access Private
*/
function RARRLancamento(&$obRARRCalculo)
{
    //mapeamento
    $this->obTARRLancamento                   = new TARRLancamento;
    $this->obTARRLancamentoCalculo            = new TARRLancamentoCalculo;
    $this->obTARRLancamentoAcrescimo          = new TARRLancamentoAcrescimo;
    $this->obTARRLancamentoConcedeDesoneracao = new TARRLancamentoConcedeDesoneracao;
    $this->obTARRAcrescimoCalculo             = new TARRAcrescimoCalculo;
    $this->obTARRLancamentoDesconto           = new TARRLancamentoDesconto;
    $this->obTARRLancamentoProcesso           = new TARRLancamentoProcesso;
    $this->obTARRParcela                      = new TARRParcela;
    $this->obTARRParcelaDesconto              = new TARRParcelaDesconto;
    //funcoes
    $this->obFFNCalculaDesoneracao = new FFNCalculaDesoneracao;
    $this->obFARRVerificaSuspensao = new FARRVerificaSuspensao;
    //regras
    $this->roRARRCalculo             = &$obRARRCalculo;
    $obParcela                       = new RARRParcela ($this);
    $this->obRARRCarne               = new RARRCarne($obParcela);
    $this->obRARRSuspensao           = new RARRSuspensao;
    $this->obRCEMInscricaoEconomica  = new RCEMInscricaoEconomica;
    $this->obRCIMImovel              = new RCIMImovel( new RCIMLote);
    $this->obRCgm                    = new RCgm;
    $this->obRProcesso               = new RProcesso;
    //transacao
    $this->obTransacao               = new Transacao;
}

function refCalculo(&$obRARRCalculo)
{
    $this->roRARRCalculo             = &$obRARRCalculo;

    return true;
}

/**
    * Efetuar Lancamento
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function efetuarLancamento($boTransacao = "" , $arCalculo = "")
{
    ;
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    $obErro = new Erro ;
    Sessao::write( "lancamentos_cods", "" );
    if ( !$obErro->ocorreu() ) {

        //busca creditos do Grupo de Credito selecionado
        $this->roRARRCalculo->obRARRGrupo->listarCreditos( $rsCreditosGrupo, $boTransacao );

        while ( !$rsCreditosGrupo->eof() ) {
            $stCreditos .= $rsCreditosGrupo->getCampo('cod_credito').",";
            $rsCreditosGrupo->proximo();
        }
        $stCreditos = substr( $stCreditos, 0 , -1 );

        if ( is_array( $arCalculo ) ) {
            $rsCalculos = new RecordSet;
            $rsCalculos->preenche( $arCalculo );
            $inNumCGM = $this->roRARRCalculo->obRCGM->getNumCgm();
        } else {
            $this->roRARRCalculo->obRARRGrupo->obRMONCredito->setCodCredito( $stCreditos );
            $this->roRARRCalculo->listarCalculos( $rsCalculos, $boTransacao );
            $inNumCGM = $rsCalculos->getCampo('numcgm');
        }

        //recupera informacoes de vencimento do grupo de credito
        $this->roRARRCalculo->obRARRGrupoVencimento->roRARRCalendarioFiscal->setCodigoGrupo( $this->roRARRCalculo->obRARRGrupo->getCodGrupo() );
        $obErro = $this->roRARRCalculo->obRARRGrupoVencimento->listarGrupoVencimento($rsVencimento, $boTransacao );

        if ( $rsVencimento->getNumLinhas() < 1 ) {
            $obErro->setDescricao( "<b>Calendário Fiscal não definido</b> para este Grupo/Crédito!" );
        }

        if ( !$obErro->ocorreu() ) {

            //recupera informacoes do calendario fiscal
            $obErro = $this->roRARRCalculo->obRARRGrupoVencimento->roRARRCalendarioFiscal->listarCalendario( $rsCalendario, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $arCalendario['valor_minimo']            = $rsCalendario->getCampo('valor_minimo');
                $arCalendario['valor_minimo_lancamento'] = $rsCalendario->getCampo('valor_minimo_lancamento');
                $arCalendario['valor_minimo_parcela']    = $rsCalendario->getCampo('valor_minimo_parcela');

                $nuRetorno = $rsCalculos->getCampo('valor');

                while ( !$rsVencimento->eof() ) {
                    if ( ($nuRetorno > $rsVencimento->getCampo ('limite_inicial') && $nuRetorno < $rsVencimento->getCampo ('limite_final')) ) {
                        $this->roRARRCalculo->obRARRGrupoVencimento->setCodigoVencimento ( $rsVencimento->getCampo ('cod_vencimento') );
                        break;
                    }
                    $rsVencimento->proximo();
                }
                $rsVencimento->setPrimeiroElemento();
                //recupera o numero total de parcelas para o vencimento
                $obErro = $this->roRARRCalculo->obRARRGrupoVencimento->listarParcela( $rsParcelas, $boTransacao );

                if ( !$obErro->ocorreu() ) {
                    $inCountParcelas = 0;
                    $inParcela       = 1;
                    $arConfParcelas = array();
                    while ( !$rsParcelas->eof() ) {
                        $arConfParcelas[$inParcela]['nr_parcela'     ]      = $inParcela;
                        $arConfParcelas[$inParcela]['data_vencimento'] = $rsParcelas->getCampo( "data_vencimento" );
                        if ( $rsParcelas->getCampo('valor') > 0 ) {
                            $arConfParcelas[$inParcela]['desconto']         = $rsParcelas->getCampo('valor');
                            $arConfParcelas[$inParcela]['tipo_desconto']  = $rsParcelas->getCampo('percentual');
                            $arConfParcelas[$inParcela]['data_vencimento_desconto'] = $rsParcelas->getCampo('data_vencimento_desconto');
                        } else {
                            $arConfParcelas[$inParcela]['desconto']                 = 0;
                            $arConfParcelas[$inParcela]['tipo_desconto']            = '';
                            $arConfParcelas[$inParcela]['data_vencimento_desconto'] = '';
                        }
                        $inParcela++;
                        $inCountParcelas++;
                        $rsParcelas->proximo();
                    }
                }
            }
        }
        //recupera descontos do grupo de credito
        $this->roRARRCalculo->obRARRGrupoVencimento->listarDesconto( $rsDescontos , $boTransacao );
        $inFirstLoop    = TRUE;
        $nuValorCalculo = 0;
        while ( !$rsCalculos->eof() ) {

            $nuValorCalculo = 0;
            if ( ( $inFirstLoop == TRUE ) OR ( $stIMAnterior !== $rsCalculos->getCampo("inscricao_municipal") ) ) {

                //recupera valor total do calculo
                unset($this->roRARRCalculo->inCodCalculo);
                $this->roRARRCalculo->obRCIMImovel->setNumeroInscricao( $rsCalculos->getCampo("inscricao_municipal" ) );
                $this->roRARRCalculo->buscaValorCalculo( $rsValorCalculo , $boTransacao );

                $arCreditoValor = array();
                while ( !$rsValorCalculo->eof() ) {
                    $nuValorCalculo += $rsValorCalculo->getCampo( "valor" );
                    $arCreditoValor[$rsValorCalculo->getCampo('cod_credito')]['valor']    = $rsValorCalculo->getCampo( "valor" );
                    $arCreditoValor[$rsValorCalculo->getCampo('cod_credito')]['desconto'] = $rsValorCalculo->getCampo( "desconto" );
                    $rsValorCalculo->proximo();
                }
                //insere novo lancamento
                $obErro = $this->obTARRLancamento->proximoCod( $this->inCodLancamento, $boTransacao );

                Sessao::write( "lancamentos_cods", Sessao::read( "lancamentos_cods" ).$this->inCodLancamento."," );

                $this->obTARRLancamento->setDado( "cod_lancamento" , $this->inCodLancamento                                   );
                $this->obTARRLancamento->setDado( "vencimento"     , $rsVencimento->getCampo("data_vencimento_parcela_unica") );
                $this->obTARRLancamento->setDado( "total_parcelas" , $inCountParcelas                                         );
                $this->obTARRLancamento->setDado( "ativo"          , $boAtivo                                                 );
                $this->obTARRLancamento->setDado( "valor"          , $nuValorCalculo                                          );
                $this->obTARRLancamento->setDado( "observacao"     , $stObservacao                                            );
                $this->obTARRLancamento->setDado( "observacao_sistema", $stObservacaoSistema );

                $obErro = $this->obTARRLancamento->inclusao( $boTransacao );

                //faz a verificação de suspensão
                //se existe suspensão cadastrada para esta inscricao municipal soma com o valor do calculo
                //se o valor minimo for atingido a suspensão é suspensa e o valor lançado
                $this->obFARRVerificaSuspensao->setDado( 'stFiltro', $rsCalculos->getCampo("inscricao_municipal") );
                $obErro = $this->obFARRVerificaSuspensao->recuperaTodos( $rsSuspensoes, $stFiltro, $stOrder, $boTransacao );
                $nuValorSuspenso = 0;
                $boSuspensoes = FALSE;
                while ( !$rsSuspensoes->eof() ) {
                    $nuValorSuspenso += $rsSuspensoes->getCampo('valor');
                    $rsSuspensoes->proximo();
                    $boSuspensoes = TRUE;
                }
                $rsSuspensoes->setPrimeiroElemento();
                $nuValorCalculo += $nuValorSuspenso;

                $boAtivo      = TRUE;
                $stObservacao = "";
                //faz a validacao do valor minimo do lancamento
                if ($nuValorCalculo < $arCalendario['valor_minimo_lancamento']) {
                    $boAtivo      = FALSE;
                    $stObservacao = "Valor mínimo do lançamento(".$arCalendario['valor_minimo_lancamento'].") não foi atingido.(".$nuValorCalculo.")!";

                    //cadastra suspensao para o lancamento
                    $this->obRARRSuspensao->setCodLancamento                 ( $this->inCodLancamento );
                    $this->obRARRSuspensao->obRARRTipoSuspensao->setCodigoTipoSuspensao( 5                      );
                    $this->obRARRSuspensao->setInicio                                  ( date( "d/m/Y" )        );
                    $this->obRARRSuspensao->setObservacao                        ( $stObservacao          );
                    $obErro = $this->obRARRSuspensao->suspenderCredito  ( $boTransacao );
                }

                //se possuir suspensoes anteriores, insere data de termino para as mesmas
                if ( $boSuspensoes == TRUE AND $boAtivo == TRUE AND !$obErro->ocorreu() ) {
                    while ( !$rsSuspensoes->eof() ) {
                        $this->obRARRSuspensao->setCodLancamento( $rsSuspensoes->getCampo('cod_lancamento') );
                        $this->obRARRSuspensao->setCodSuspensao ( $rsSuspensoes->getCampo('cod_suspensao')  );
                        $this->obRARRSuspensao->setTermino      ( date( "d/m/Y" )                           );
                        $this->obRARRSuspensao->setObservacao   ( ''                                        );
                        $obErro = $this->obRARRSuspensao->terminarSuspensao( $boTransacao );
                        $rsSuspensoes->proximo();
                    }
                }

                //insere os descontos
                $arParcelaUnica = array();
                $inCount = 1;

                while ( !$rsDescontos->eof() ) {
                    if ( $rsDescontos->getCampo("percentual") == 't' ) {
                        $nuValorDesconto = $nuValorCalculo - (( $nuValorCalculo * $rsDescontos->getCampo("valor") ) / 100);
                        $nuValorDesconto = round( $nuValorDesconto , 2 );
                    } else {
                        $nuValorDesconto = $nuValorCalculo - $rsDescontos->getCampo("valor");
                    }
                    $this->obTARRLancamentoDesconto->setDado( "cod_lancamento" , $this->inCodLancamento                    );
                    $this->obTARRLancamentoDesconto->setDado( "cod_desconto"   , $rsDescontos->getCampo("cod_desconto")    );
                    $this->obTARRLancamentoDesconto->setDado( "vencimento"     , $rsDescontos->getCampo("data_vencimento") );
                    $this->obTARRLancamentoDesconto->setDado( "valor"          , $nuValorDesconto                          );

                    $arParcelaUnica[$inCount]["vencimento"]     = $rsDescontos->getCampo("data_vencimento");
                    $arParcelaUnica[$inCount]["valor"]          = $nuValorCalculo;
                    $arParcelaUnica[$inCount]["valor_desconto"] = $nuValorDesconto;

                    $obErro = $this->obTARRLancamentoDesconto->inclusao( $boTransacao );
                    $inCount++;
                    $rsDescontos->proximo();
                }

                $rsDescontos->setPrimeiroElemento();
                if ($boAtivo == TRUE) {
                    //insere parcelas
                    $nuValorParcela        = round( ( $nuValorCalculo / $inCountParcelas ) , 2 );
                    $inCountReparcelamento = 0;
                    $boBloqueiaParcelas    = FALSE;

                    //faz validacao do valor minimo por parcela
                    //se o valor minimo nao for atingido faz update do lancamento para FALSE
                    if ( ( $nuValorParcela < $arCalendario['valor_minimo_parcela'] ) ) {

                        //refaz parcelamento para o valor da parcela se adequar ao valor minimo
                        for ($i = $inCountParcelas; $i > 0; $i--) {
                            $nuValorParcela = round( ( $nuValorCalculo / $i ) , 2 );
                            if ( ( $nuValorParcela >= $arCalendario['valor_minimo_parcela'] ) ) {
                                $inCountReparcelamento = $i;
                                break;
                            }
                        }

                        if ($inCountReparcelamento == 0) {
                            $boBloqueiaParcelas = TRUE;
                            $boAtivo            = FALSE;
                        } else {
                            $boAtivo            = TRUE;
                        }

                        $this->obTARRLancamento->setDado( "cod_lancamento" , $this->inCodLancamento );
                        $this->obTARRLancamento->setDado( "ativo"          , $boAtivo               );
                        $obErro = $this->obTARRLancamento->alteracao( $boTransacao );
                    }

                    //monta array com o valor das parcelas
                    if ($inCountReparcelamento > 0) {
                        $inCountParcelasTemp = $inCountReparcelamento + 1;
                    } else {
                        $inCountParcelasTemp = $inCountParcelas + 1;
                    }
                    $arParcelas   = array();
                    $nuValorTotal = 0;
                    $arParcela[1] = $nuValorCalculo;
                    for ($inNumParcela = 1; $inNumParcela < $inCountParcelasTemp; $inNumParcela++) {
                        $arConfParcelas[$inNumParcela]['valor'] = $nuValorParcela;
                        $nuValorTotal += $nuValorParcela;
                    }
                    if ($nuValorTotal != $nuValorCalculo) {
                        $nuDiffParcelas = $nuValorTotal - $nuValorCalculo;
                        $arConfParcelas[1]['valor'] = $nuValorParcela - $nuDiffParcelas;
                    }

                    /*********************************************************************************/
                    // verificar convenio do grupo
                    include_once ( CAM_GT_ARR_NEGOCIO."RARRCarne.class.php"                             );
                    $this->roRARRCalculo->obRARRGrupo->listarCreditosFuncao($rsTmp,$boTransacao);

                    $this->obRARRCarne->setGrupo      ( $this->roRARRCalculo->obRARRGrupo->getCodGrupo() );
                    $this->obRARRCarne->setExercicio  ( $this->roRARRCalculo->obRARRGrupo->getExercicio() );
                    $stExercicioCarne = $this->obRARRCarne->getExercicio();

                    $this->obRARRCarne->obRMONConvenio->setNumeroConvenio( $rsTmp->getCampo('num_convenio') );
                    $this->obRARRCarne->obRMONCarteira->setCodigoCarteira( $rsTmp->getCampo('cod_carteira') );

                    $this->obRARRCarne->obRMONConvenio->listarConvenioBanco( $rsConvenioBanco, $boTransacao );
                    $this->obRARRCarne->obRMONConvenio->obRFuncao->setCodFuncao( $rsConvenioBanco->getCampo( "cod_funcao" ) );
                    $this->obRARRCarne->obRMONConvenio->obRFuncao->obRBiblioteca->setCodigoBiblioteca($rsConvenioBanco->getCampo( "cod_biblioteca" ) );
                    $this->obRARRCarne->obRMONConvenio->obRFuncao->obRBiblioteca->roRModulo->setCodModulo(25);
                    $this->obRARRCarne->obRMONConvenio->obRFuncao->consultar($boTransacao);

                    $stFNumeracao = "F".$this->obRARRCarne->obRMONConvenio->obRFuncao->getNomeFuncao();
                    $stFNumeracaoMap = "../../classes/funcao/".$stFNumeracao.".class.php";
                    include_once ( $stFNumeracaoMap );
                    $obFNumeracao = new $stFNumeracao;

                    $stParametros = "'".$rsConvenioBanco->getCampo( "cod_carteira" )."','".$rsConvenioBanco->getCampo("cod_convenio" )."'";
                    /********************************** fim da verificação *******************************/

                    for ($inNumParcela = 0; $inNumParcela < $inCountParcelasTemp; $inNumParcela++) {
                        if ($boBloqueiaParcelas == TRUE AND $inNumParcela > 0) {
                            break;
                        }

                        //insere parcela referente a parcela unica
                        if ($inNumParcela == 0) {
                            //insere parcelas referente aos descontos da parcela unica
                            if ( count( $arParcelaUnica ) > 0 ) {
                                foreach ($arParcelaUnica AS $key => $valor) {
                                    $obErro = $this->obTARRParcela->proximoCod( $this->inCodParcela, $boTransacao );
                                    $this->obTARRParcela->setDado( "cod_parcela"    , $this->inCodParcela                 );
                                    $this->obTARRParcela->setDado( "cod_lancamento" , $this->inCodLancamento              );
                                    $this->obTARRParcela->setDado( "nr_parcela"     , 0                                   );
                                    $this->obTARRParcela->setDado( "vencimento"     , $arParcelaUnica[$key]["vencimento"] );
                                    $this->obTARRParcela->setDado( "valor"          , $arParcelaUnica[$key]["valor"]      );
                                    $obErro = $this->obTARRParcela->inclusao   ( $boTransacao );

                                    //insere o desconto da parcela unica
                                    if ($arParcelaUnica[$key]["valor_desconto"] != $arParcelaUnica[$key]["valor"]) {
                                        $this->obTARRParcelaDesconto->setDado ( "cod_parcela"    , $this->inCodParcela                           );
                                        $this->obTARRParcelaDesconto->setDado ( "vencimento"     , $arParcelaUnica[$key]["vencimento"] );
                                        $this->obTARRParcelaDesconto->setDado ( "valor"          , $arParcelaUnica[$key]["valor_desconto"] );
                                        $obErro = $this->obTARRParcelaDesconto->inclusao( $boTransacao );
                                    }
                                    /********** EMISSAO DE CARNE PARA A PARCELA UNICA ******/
                                    if ($this->inCodParcela) {
                                        $obErro = $obFNumeracao->executaFuncao($rsRetorno,$stParametros,$boTransacao);
                                        if ( !$obErro->ocorreu() ) {
                                            $inNumeracao = $rsRetorno->getCampo( "valor" );
                                            $this->obRARRCarne->setNumeracao( $inNumeracao );
                                            $this->obRARRCarne->setExercicio( $stExercicioCarne );
                                            $this->obRARRCarne->obRARRParcela->setCodParcela( $this->inCodParcela );
                                            $this->obRARRCarne->obRMONConvenio->setCodigoConvenio( $rsConvenioBanco->getCampo( "cod_convenio" ) );
                                            if ($rsConvenioBanco->getCampo( "cod_carteira" ) > 0)
                                                $this->obRARRCarne->obRMONCarteira->setCodigoCarteira( $rsConvenioBanco->getCampo( "cod_carteira" ) );
                                            $obErro = $this->obRARRCarne->incluirCarne( $boTransacao );
                                            if ( $obErro->ocorreu() ) {
                                                break;
                                            }
                                        }
                                    }
                                }
                            }
                        //insere demais parcelas
                        } else {
                            $obErro = $this->obTARRParcela->proximoCod( $this->inCodParcela, $boTransacao );
                            $this->obTARRParcela->setDado( "cod_parcela"    , $this->inCodParcela                               );
                            $this->obTARRParcela->setDado( "cod_lancamento" , $this->inCodLancamento                            );
                            $this->obTARRParcela->setDado( "nr_parcela"     , $arConfParcelas[$inNumParcela]['nr_parcela']      );
                            $this->obTARRParcela->setDado( "vencimento"     , $arConfParcelas[$inNumParcela]['data_vencimento'] );
                            $this->obTARRParcela->setDado( "valor"          , $arConfParcelas[$inNumParcela]['valor']           );
                            $obErro = $this->obTARRParcela->inclusao( $boTransacao );
                            /********** EMISSAO DE CARNE PARA A PARCELA ***********/
                            if ($this->inCodParcela) {
                                $obErro = $obFNumeracao->executaFuncao($rsRetorno,$stParametros,$boTransacao);
                                if ( !$obErro->ocorreu() ) {
                                    $inNumeracao = $rsRetorno->getCampo( "valor" );
                                    $this->obRARRCarne->setNumeracao( $inNumeracao );
                                    $this->obRARRCarne->setExercicio( $stExercicioCarne );
                                    $this->obRARRCarne->obRARRParcela->setCodParcela( $this->inCodParcela );
                                    $this->obRARRCarne->obRMONConvenio->setCodigoConvenio( $rsConvenioBanco->getCampo( "cod_convenio" ) );
                                    if ($rsConvenioBanco->getCampo( "cod_carteira" ) > 0)
                                        $this->obRARRCarne->obRMONCarteira->setCodigoCarteira( $rsConvenioBanco->getCampo( "cod_carteira" ) );
                                    $obErro = $this->obRARRCarne->incluirCarne( $boTransacao );
                                    if ( $obErro->ocorreu() ) {
                                        break;
                                    }
                                }
                            }
                        }

                        //inclui desconto por parcela na tabela parcela_desconto
                        if ( !$obErro->ocorreu() ) {
                            if ($arConfParcelas[$inNumParcela]['desconto'] > 0 AND $arConfParcelas[$inNumParcela]['data_vencimento_desconto']) {
                                $nuValorParcelaDesconto = 0;

                                //aplica desconto somente nos creditos que possuem desconto setado como TRUE na tabela credito_grupo
                                foreach ($arCreditoValor as $key => $valor) {
                                    if ($arCreditoValor[$key]['desconto'] == 't') {
                                        if ($arConfParcelas[$inNumParcela]['tipo_desconto'] == 't') {
                                            $nuValorParcelaDescontoTmp = $arCreditoValor[$key]['valor'] - (( $arCreditoValor[$key]['valor'] * $arConfParcelas[$inNumParcela]['desconto'] ) / 100);
                                            $nuValorParcelaDesconto += round( $nuValorParcelaDescontoTmp , 2 );
                                            $boTipoDesconto         = 'TRUE';
                                        } else {
                                            $nuValorParcelaDesconto = $arCreditoValor[$key]['valor'] - $arConfParcelas[$inNumParcela]['desconto'];
                                            $boTipoDesconto         = 'FALSE';
                                        }
                                    } else {
                                        $nuValorParcelaDesconto += $arCreditoValor[$key]['valor'];
                                    }
                                }

                                $nuValorParcelaDesconto = round ( ( $nuValorParcelaDesconto / ($inCountParcelasTemp-1) ) ,2 );

                                $this->obTARRParcelaDesconto->setDado( "cod_parcela"    , $this->inCodParcela                                        );
                                $this->obTARRParcelaDesconto->setDado( "vencimento"     , $arConfParcelas[$inNumParcela]['data_vencimento_desconto'] );
                                $this->obTARRParcelaDesconto->setDado( "valor"          , $nuValorParcelaDesconto                                    );
                                $obErro = $this->obTARRParcelaDesconto->inclusao( $boTransacao );
                            }
                        }
                    }
                }
            }

            $stIMAnterior = $rsCalculos->getCampo("inscricao_municipal");
            $inFirstLoop  = FALSE;

            //insere relacao lancamento/calculo
            $this->obTARRLancamentoCalculo->setDado( "cod_lancamento" , $this->inCodLancamento               );
            $this->obTARRLancamentoCalculo->setDado( "cod_calculo"    , $rsCalculos->getCampo("cod_calculo") );
            $this->obTARRLancamentoCalculo->setDado( "valor"          , $rsCalculos->getCampo("valor") );

            $obErro = $this->obTARRLancamentoCalculo->inclusao( $boTransacao );

            $rsCalculos->proximo();
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRLancamento );

    return $obErro;
}

/**
    * Efetuar Lancamento por Credito
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function efetuarLancamentoCredito($boTransacao = "")
{
    ;
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    $obErro = new Erro ;

    if ( !$obErro->ocorreu() ) {

        $this->roRARRCalculo->listarCalculosLancamento( $rsCalculos, $boTransacao );

        //recupera valor total do calculo
        $this->roRARRCalculo->buscaValorCalculoCredito( $rsValorCalculo , $boTransacao );
        $nuValorCalculo = $rsValorCalculo->getCampo( "valor" );

        $arParcelas = Sessao::read( "parcelas" );
        $inTotalDados = count( $arParcelas );
        $inTotalParcelas = 0;
        $stDataVencimentoUnica = "";
        $stDataVencimentoPrimeira = "";
        $inDesconto = "";
        $stTipoDesconto = "";
        $inValPrimeira = 10000;
        if ( !$this->getDataVencimento()) {
            for ($inX=0; $inX < $inTotalDados; $inX++) {
                if ($arParcelas[$inX]["stTipoParcela"] == "Única") {
                    $stDataVencimentoUnica = $arParcelas[$inX]["dtVencimento"];
                    $inDesconto = $arParcelas[$inX]["flDesconto"];
                    $stTipoDesconto = $arParcelas[$inX]["stTipoDesconto"];
                } else {
                    $arData = explode( "/", $arParcelas[$inX]["dtVencimento"] );
                    $inVal = $arData[0] + $arData[1] + $arData[2];
                    if ($inVal < $inValPrimeira) {
                        $stDataVencimentoPrimeira = $arParcelas[$inX]["dtVencimento"];
                        $inValPrimeira = $inVal;
                    }

                    $arParcelas[$inTotalParcelas] = $arParcelas[$inX];
                    $inTotalParcelas++;
                }
            }

            Sessao::write( "parcelas", $arParcelas );
        } else {
            $stDataVencimentoUnica = $this->getDataVencimento();
            $inTotalParcelas = 1;
        }
        //insere novo lancamento
        $obErro = $this->obTARRLancamento->proximoCod( $this->inCodLancamento, $boTransacao );

        Sessao::write( "lancamentos_cods", Sessao::read( 'lancamentos_cods').$this->inCodLancamento."," );

        $this->obTARRLancamento->setDado( "cod_lancamento" , $this->inCodLancamento     );
        $this->obTARRLancamento->setDado( "numcgm"         , $this->roRARRCalculo->obRCGM->getNumCGM() );

        if ( $stDataVencimentoUnica )
            $this->obTARRLancamento->setDado( "vencimento"     , $stDataVencimentoUnica );
        else
            $this->obTARRLancamento->setDado( "vencimento"     , $stDataVencimentoPrimeira );

        $this->obTARRLancamento->setDado( "total_parcelas" , $inTotalParcelas );
        $this->obTARRLancamento->setDado( "valor"          , $nuValorCalculo            );
        $this->obTARRLancamento->setDado( "ativo"          , TRUE                       );
        $this->obTARRLancamento->setDado( "observacao_sistema"  , $this->getObservacaoSistema() );
        $this->obTARRLancamento->setDado( "observacao"  , $this->getObservacao()       );
        $obErro = $this->obTARRLancamento->inclusao( $boTransacao );

        //insere tabela lancamento processo
        if ( !$obErro->ocorreu() && $this->obRProcesso->getCodigoProcesso() ) {
                $this->obTARRLancamentoProcesso->setDado( "cod_lancamento" , $this->inCodLancamento     );
                $this->obTARRLancamentoProcesso->setDado( "cod_processo", $this->obRProcesso->getCodigoProcesso() );
                $this->obTARRLancamentoProcesso->setDado( "ano_exercicio",  $this->obRProcesso->getExercicio() );

                $obErro = $this->obTARRLancamentoProcesso->inclusao( $boTransacao );
        }

        //insere o desconto da parcela unica
        if ($stDataVencimentoUnica AND $inDesconto) {
            if ($stTipoDesconto == 'Percentual') {
                $nuValorDesconto = $nuValorCalculo - (( $nuValorCalculo * $inDesconto ) / 100);
                $nuValorDesconto = round( $nuValorDesconto , 2 );
            } else {
                $nuValorDesconto = $nuValorCalculo - $inDesconto;
            }

            $this->obTARRLancamentoDesconto->setDado( "cod_lancamento" , $this->inCodLancamento             );
            $this->obTARRLancamentoDesconto->setDado( "cod_desconto"   , 1                                  );
            $this->obTARRLancamentoDesconto->setDado( "vencimento"     , $stDataVencimentoUnica             );
            $this->obTARRLancamentoDesconto->setDado( "valor"          , $nuValorDesconto                   );
            $obErro = $this->obTARRLancamentoDesconto->inclusao( $boTransacao );
        }

        //insere parcelas
        if ( $inTotalParcelas > 0 )
            $nuValorParcela        = round( ( $nuValorCalculo / $inTotalParcelas ) , 2 );

        //monta array com o valor das parcelas
        $inCountParcelasTemp = $inTotalParcelas + 1;
        $arParcelas     = array();
        $arConfParcelas = array();
        $nuValorTotal   = 0;
        $arParcela[1]   = $nuValorCalculo;
        for ($inNumParcela = 1; $inNumParcela < $inCountParcelasTemp; $inNumParcela++) {
            $arConfParcelas[$inNumParcela]['valor'] = $nuValorParcela;
            $nuValorTotal += $nuValorParcela;
        }

        if ($nuValorTotal != $nuValorCalculo) {
            $nuDiffParcelas = $nuValorTotal - $nuValorCalculo;
            $arConfParcelas[1]['valor'] = $nuValorParcela - $nuDiffParcelas;
        }

        // verificar convenio do grupo
        include_once ( CAM_GT_ARR_NEGOCIO."RARRCarne.class.php"                             );
        $this->roRARRCalculo->obRARRGrupo->listarCreditosFuncao($rsTmp,$boTransacao);

        $this->obRARRCarne->setExercicio  ( $this->roRARRCalculo->getExercicio() );
        $stExercicioCarne = $this->obRARRCarne->getExercicio();

        $this->obRARRCarne->obRMONConvenio->setNumeroConvenio( $rsTmp->getCampo('num_convenio') );
        $this->obRARRCarne->obRMONCarteira->setCodigoCarteira( $rsTmp->getCampo('cod_carteira') );

        $this->obRARRCarne->obRMONConvenio->listarConvenioBanco( $rsConvenioBanco, $boTransacao );
        $this->obRARRCarne->obRMONConvenio->obRFuncao->setCodFuncao( $rsConvenioBanco->getCampo( "cod_funcao" ) );
        $this->obRARRCarne->obRMONConvenio->obRFuncao->obRBiblioteca->setCodigoBiblioteca($rsConvenioBanco->getCampo( "cod_biblioteca" ) );
        $this->obRARRCarne->obRMONConvenio->obRFuncao->obRBiblioteca->roRModulo->setCodModulo(25);
        $this->obRARRCarne->obRMONConvenio->obRFuncao->consultar($boTransacao);

        $stFNumeracao = "F".$this->obRARRCarne->obRMONConvenio->obRFuncao->getNomeFuncao();
        $stFNumeracaoMap = "../../classes/funcao/".$stFNumeracao.".class.php";
        include_once ( $stFNumeracaoMap );
        $obFNumeracao = new $stFNumeracao;

        $stParametros = "'".$rsConvenioBanco->getCampo( "cod_carteira" )."','".$rsConvenioBanco->getCampo("cod_convenio" )."'";
        /********************************** fim da verificação *******************************/

        $inNumeroParcela = 1;
        $arParcelas = Sessao::read( "parcelas" );
        for ( $inNumParcela = 0; $inNumParcela < count($arParcelas); $inNumParcela++ ) {

            $obErro = $this->obTARRParcela->proximoCod( $this->inCodParcela, $boTransacao );
            $this->obTARRParcela->setDado( "cod_parcela"    , $this->inCodParcela );
            $this->obTARRParcela->setDado( "cod_lancamento" , $this->inCodLancamento );
            if ($arParcelas[$inNumParcela]["stTipoParcela"] == "Única") {
                $this->obTARRParcela->setDado( "nr_parcela" , 0 );
                if ($stDataVencimentoUnica AND $inDesconto) {
                    $this->obTARRParcela->setDado( "vencimento" , $stDataVencimentoUnica );
                    $this->obTARRParcela->setDado( "valor"      , $nuValorDesconto                   );
                } else {
                    $this->obTARRParcela->setDado( "vencimento" , $this->getDataVencimento() );
                    $this->obTARRParcela->setDado( "valor"      , $nuValorParcela            );
                }
            } else {
                $this->obTARRParcela->setDado( "nr_parcela" , $inNumeroParcela );
                $this->obTARRParcela->setDado( "vencimento" , $arParcelas[$inNumParcela]["dtVencimento"] );
                $this->obTARRParcela->setDado( "valor"      , $arConfParcelas[$inNumeroParcela]['valor'] );

                $inNumeroParcela++;
            }

            $obErro = $this->obTARRParcela->inclusao( $boTransacao );
            if ($this->inCodParcela) {
                $obErro = $obFNumeracao->executaFuncao($rsRetorno,$stParametros,$boTransacao);
                if ( !$obErro->ocorreu() ) {
                    $inNumeracao = $rsRetorno->getCampo( "valor" );
                    $this->obRARRCarne->setNumeracao( $inNumeracao );
                    $this->obRARRCarne->setExercicio( $stExercicioCarne );
                    $this->obRARRCarne->obRARRParcela->setCodParcela( $this->inCodParcela );
                    $this->obRARRCarne->obRMONConvenio->setCodigoConvenio( $rsConvenioBanco->getCampo( "cod_convenio" ) );
                    if ($rsConvenioBanco->getCampo( "cod_carteira" ) > 0)
                        $this->obRARRCarne->obRMONCarteira->setCodigoCarteira( $rsConvenioBanco->getCampo( "cod_carteira" ) );

                    $obErro = $this->obRARRCarne->incluirCarne( $boTransacao );
                    if ( $obErro->ocorreu() ) {
                        break;
                    }
                }
            }
        }
    }
    //insere relacao lancamento/calculo
    $this->obTARRLancamentoCalculo->setDado( "cod_lancamento" , $this->inCodLancamento               );
    $this->obTARRLancamentoCalculo->setDado( "cod_calculo"    , $rsCalculos->getCampo("cod_calculo") );
    $this->obTARRLancamentoCalculo->setDado( "valor"          , $rsCalculos->getCampo("valor")       );
    $obErro = $this->obTARRLancamentoCalculo->inclusao( $boTransacao );

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRLancamento );

    return $obErro;
}

/**
    * Efetuar Lancamento MANUAL DE CREDITO
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function efetuarLancamentoManualCredito($boTransacao = "")
{
    ;
    $boFlagTransacao = false;
    $boLancamentoImobiliario = $boLancamentoEconomico = $boLancamentoCGM = false;
    $obErro = new Erro ;
    Sessao::write( "lancamentos_cods", "" );
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        //insere novo lancamento
        $obErro = $this->obTARRLancamento->proximoCod( $this->inCodLancamento, $boTransacao );
        Sessao::write( "lancamentos_cods", Sessao::read( "lancamentos_cods" ).$this->inCodLancamento."," );

        $arParcelas = Sessao::read( "parcelas" );
        $dtDataVencimento = $arParcelas[0]['data_vencimento'];

        if ( !$obErro->ocorreu() ) {
            $this->obTARRLancamento->setDado( "cod_lancamento"  , $this->inCodLancamento    );
            $this->obTARRLancamento->setDado( "numcgm"          , $this->obRCgm->getNumCGM());
            $this->obTARRLancamento->setDado( "vencimento"      , $dtDataVencimento         );
            $this->obTARRLancamento->setDado( "total_parcelas"  , $this->getTotalParcelas() );
            $this->obTARRLancamento->setDado( "valor"           , $this->getValor()         );
            $this->obTARRLancamento->setDado( "ativo"           , TRUE                      );
            $this->obTARRLancamento->setDado( "observacao_sistema" , $this->getObservacaoSistema() );
            $this->obTARRLancamento->setDado( "observacao"      , $this->getObservacao()    );
            $obErro = $this->obTARRLancamento->inclusao( $boTransacao );
        }

        //insere tabela lancamento processo
        if ( !$obErro->ocorreu() ) {
            if ( $this->obRProcesso->getCodigoProcesso() ) {
                $this->obTARRLancamentoProcesso->setDado( "cod_lancamento" , $this->inCodLancamento     );
                $this->obTARRLancamentoProcesso->setDado( "cod_processo", $this->obRProcesso->getCodigoProcesso() );
                $this->obTARRLancamentoProcesso->setDado( "ano_exercicio",$this->obRProcesso->getExercicio());
                $obErro = $this->obTARRLancamentoProcesso->inclusao( $boTransacao );
            }
        }

        //INSERE NOVO CALCULO
        $obErro = $this->roRARRCalculo->obTARRCalculo->proximoCod( $this->roRARRCalculo->obTARRCalculo->inCodCalculo, $boTransacao );
        if ( !$obErro->ocorreu() ) {

            $this->roRARRCalculo->obTARRCalculo->setDado ( "cod_calculo", $this->roRARRCalculo->obTARRCalculo->inCodCalculo );
            $this->roRARRCalculo->obTARRCalculo->setDado ( "cod_credito", $this->roRARRCalculo->obRMONCredito->getCodCredito() );
            $this->roRARRCalculo->obTARRCalculo->setDado ( "cod_especie", $this->roRARRCalculo->obRMONCredito->getCodEspecie() );
            $this->roRARRCalculo->obTARRCalculo->setDado ( "cod_genero", $this->roRARRCalculo->obRMONCredito->getCodGenero() );
            $this->roRARRCalculo->obTARRCalculo->setDado ( "cod_natureza", $this->roRARRCalculo->obRMONCredito->getCodNatureza() );
            $this->roRARRCalculo->obTARRCalculo->setDado ( "exercicio", $this->obRARRCarne->getExercicio());
            $this->roRARRCalculo->obTARRCalculo->setDado ( "valor", $this->getValor() );
            $this->roRARRCalculo->obTARRCalculo->setDado ( "nro_parcelas", $this->getTotalParcelas() );
            $this->roRARRCalculo->obTARRCalculo->setDado ( "ativo", true );
            $this->roRARRCalculo->obTARRCalculo->setDado ( "calculado", false );

            $obErro = $this->roRARRCalculo->obTARRCalculo->inclusao($boTransacao);

        }

        //insere novo lancamento calculo
        if ( !$obErro->ocorreu() ) {
            $this->obTARRLancamentoCalculo->setDado( "cod_lancamento" , $this->inCodLancamento     );
            $this->obTARRLancamentoCalculo->setDado( "cod_calculo", $this->roRARRCalculo->obTARRCalculo->inCodCalculo );
            $this->obTARRLancamentoCalculo->setDado ( "valor", $this->getValor() );
            $obErro = $this->obTARRLancamentoCalculo->inclusao( $boTransacao );
        }

        if ( !$obErro->ocorreu() ) {
            //INSERE CALCULO PERTENCENTE
            if ( $this->obRCgm->getNumCGM( ) ) {

                $boLancamentoCGM = true;

                $this->roRARRCalculo->obTARRCalculoCgm = new TARRCalculoCgm;
                $this->roRARRCalculo->obTARRCalculoCgm->setDado ( "cod_calculo", $this->roRARRCalculo->obTARRCalculo->inCodCalculo );
                $this->roRARRCalculo->obTARRCalculoCgm->setDado ( "numcgm", $this->obRCgm->getNumCGM( ) );
                $obErro = $this->roRARRCalculo->obTARRCalculoCgm->inclusao($boTransacao);

            } elseif ( $this->obRCEMInscricaoEconomica->getInscricaoEconomica( ) ) {

                $boLancamentoEconomico = true;

                $this->roRARRCalculo->obTARRCadEconomicoCalculo = new TARRCadastroEconomicoCalculo;
                $this->roRARRCalculo->obTARRCadEconomicoCalculo->setDado ( "cod_calculo", $this->roRARRCalculo->obTARRCalculo->inCodCalculo );
                $this->roRARRCalculo->obTARRCadEconomicoCalculo->setDado ( "inscricao_economica", $this->obRCEMInscricaoEconomica->getInscricaoEconomica( ) );

                $this->recuperaTimestampCadastroEconomicoFaturamento( $rsEconomico, $boTransacao);

                if ( $rsEconomico->getNumLinhas() < 1 || $rsEconomico->getCampo('timestamp') == null ) {

                    include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCadastroEconomicoFaturamento.class.php" );
                    $obTARRCEFaturamento = new TARRCadastroEconomicoFaturamento;

                    $obTARRCEFaturamento->setDado ('inscricao_economica', $this->obRCEMInscricaoEconomica->getInscricaoEconomica() );
                    $arVencimento = $this->getDataVencimento();
                    $obTARRCEFaturamento->setDado ('competencia', $arVencimento[1].'/'.$arVencimento[0]);
                    $obErro = $obTARRCEFaturamento->inclusao( $boTransacao );

                    $this->recuperaTimestampCadastroEconomicoFaturamento( $rsEconomico, $boTransacao);

                }

                $this->roRARRCalculo->obTARRCadEconomicoCalculo->setDado ( "timestamp", $rsEconomico->getCampo( 'timestamp' ) );

                $obErro = $this->roRARRCalculo->obTARRCadEconomicoCalculo->inclusao($boTransacao);

                //busca o cgm da empresa
                if ( !$obErro->ocorreu() ) {

                    $obErro = $this->obRCEMInscricaoEconomica->consultarInscricaoEconomica ( $rsInscricao, $boTransacao );
                    //consultarInscricaoEconomica

                    while ( !$rsInscricao->eof() && !$obErro->ocorreu() ) {

                        $this->roRARRCalculo->obTARRCalculoCgm = new TARRCalculoCgm;
                        $this->roRARRCalculo->obTARRCalculoCgm->setDado ( "cod_calculo", $this->roRARRCalculo->obTARRCalculo->inCodCalculo );
                        $this->roRARRCalculo->obTARRCalculoCgm->setDado ( "numcgm", $rsInscricao->getCampo('numcgm') );
                        $obErro = $this->roRARRCalculo->obTARRCalculoCgm->inclusao($boTransacao);
                        $rsInscricao->proximo();

                    }
                }

            } elseif ( $this->obRCIMImovel->getNumeroInscricao() ) {

                $boLancamentoImobiliario = true;

                $this->roRARRCalculo->obTARRImovelCalculo = new TARRImovelCalculo;

                $this->roRARRCalculo->obTARRImovelCalculo->setDado ( "cod_calculo", $this->roRARRCalculo->obTARRCalculo->inCodCalculo );
                $this->roRARRCalculo->obTARRImovelCalculo->setDado ( "inscricao_municipal", $this->obRCIMImovel->getNumeroInscricao() );
                $this->roRARRCalculo->obRCIMImovel->setNumeroInscricao ( $this->obRCIMImovel->getNumeroInscricao() );
                $this->roRARRCalculo->recuperaTimestampImovelVenal( $rsImovel, $boTransacao);
                $this->roRARRCalculo->obTARRImovelCalculo->setDado ( "timestamp", $rsImovel->getCampo("timestamp")  );
                $obErro = $this->roRARRCalculo->obTARRImovelCalculo->inclusao($boTransacao);

                if ( !$obErro->ocorreu() ) {
                    $obRCIMProprietario  = new RCIMProprietario ( $this->obRCIMImovel );
                    $obRCIMProprietario->listarProprietariosPorImovel($rsProprietarios , $boTransacao );

                    while ( !$rsProprietarios->eof() ) {

                        $this->roRARRCalculo->obTARRCalculoCgm = new TARRCalculoCgm;
                        $this->roRARRCalculo->obTARRCalculoCgm->setDado ( "cod_calculo", $this->roRARRCalculo->obTARRCalculo->inCodCalculo );
                        $this->roRARRCalculo->obTARRCalculoCgm->setDado ( "numcgm", $rsProprietarios->getCampo('numcgm') );
                        $obErro = $this->roRARRCalculo->obTARRCalculoCgm->inclusao($boTransacao);
                        $rsProprietarios->proximo();

                    }
                }
            }
            //FIM PERTENCENTE
        }

        # VERIFICACOES SE ATUALIZA LANCAMENTO ANTERIOR COMO RECALCULO
        # ocorre quando:
        #   a ) exercicio for o mesmo
        #   b b) Se NAO for GRUPO, busca calculo para estre credito na calculo
        # FIM VERIFICACOES SE ATUALIZA LANCAMENTO ANTERIOR COMO RECALCULO

        //PARCELAS
        if ( !$obErro->ocorreu() ) {
            //insere parcelas

            if ($this->getTotalParcelas() > 0 ) {
                $nuValorParcelaTMP = number_format ( ( $this->getValor() / $this->getTotalParcelas() ) , 2 );
            }
            $nuValorParcelaTMP = str_replace (',','', $nuValorParcelaTMP );

            //-------------CALCULA VALOR DA PRIMEIRA PARCELA
            if ( ($nuValorParcelaTMP * $this->getTotalParcelas()) != $this->getValor() ) {
                $nuValorPrimeiraParcela = $nuValorParcelaTMP + ($this->getValor() - ( $nuValorParcelaTMP *  $this->getTotalParcelas()  ));
            } else {
                $nuValorPrimeiraParcela = $nuValorParcelaTMP;
            }
            //-------------CALCULA VALOR DA PRIMEIRA PARCELA

            $cont = 0;
            $contParcelaNormal = 1;
            $arrParcelasNovas = array();
            $boFlagPrimeiraParcela = true;
            while ( $cont < count ( $arParcelas )) {
                $dtDataVencimento = $arParcelas[$cont]['data_vencimento'];
                if ($arParcelas[$cont]['stTipoParcela'] == 'Única') {
                    $numeroParcela = 0;
                    $nuValorParcela = $this->getValor();
                } else {
                    if ($boFlagPrimeiraParcela) {
                        $nuValorParcela = $nuValorPrimeiraParcela;
                        $boFlagPrimeiraParcela = false;
                    } else {
                        $nuValorParcela = $nuValorParcelaTMP;
                    }
                    $numeroParcela = $arParcelas[$cont]['stTipoParcela'];
                }

                $obErro = $this->obTARRParcela->proximoCod( $this->inCodParcela, $boTransacao );
                $arrParcelasNovas[$cont] = $this->inCodParcela;
                $this->obTARRParcela->setDado( "cod_parcela"    , $this->inCodParcela            );
                $this->obTARRParcela->setDado( "cod_lancamento" , $this->inCodLancamento );
                $this->obTARRParcela->setDado( "nr_parcela" , $numeroParcela                        );
                $this->obTARRParcela->setDado( "vencimento" , $dtDataVencimento                 );
                $this->obTARRParcela->setDado( "valor"      , $nuValorParcela                            );

                $obErro = $this->obTARRParcela->inclusao( $boTransacao );
                //FIM INSERCAO PARCELA

                //INSERCAO NA TABELA DESCONTO #####################
                if ($arParcelas[$cont]['valor'] > 0.00) {

                 $nuValorDescontoTMP = $arParcelas[$cont]['valor'] ;

                 if ($arParcelas[$cont]['stTipoParcela'] == 'Única') {

                        if ($arParcelas[$cont]['stTipoDesconto'] == 'Percentual') {

                            $totalDescontos = 0.00;
                            $valorCreditoTMP = $this->getValor();
                            $totalDescontos = $valorCreditoTMP - ($valorCreditoTMP*$nuValorDescontoTMP/100);
                            $nuValorDesconto = number_format ( $totalDescontos , 2, '.', '' );
                        } else {

                            $totalDescontos = 0.00;
                            $valorCreditoTMP = $this->getValor();
                            $totalDescontos += ($valorCreditoTMP - $nuValorDescontoTMP);
                            $nuValorDesconto = number_format ( $totalDescontos , 2, '.', '' );
                        }

                     } else {  //parcela normal

                        if ($arParcelas[$cont]['stTipoDesconto'] == 'Percentual') {

                            $totalDescontos = 0.00;

                            $totalDescontos = ( $nuValorParcela - ($nuValorParcela *$nuValorDescontoTMP/100));

                            $nuValorDesconto = number_format ( $totalDescontos , 2, '.', '' );

                        } else {

                            $totalDescontos = 0.00;

                            $totalDescontos = ($nuValorParcela - $nuValorDescontoTMP);

                            $nuValorDesconto = number_format ( $totalDescontos , 2, '.', '' );
                        }

                     } //fim descontos

                    $this->obTARRParcelaDesconto->setDado ("cod_parcela", $this->inCodParcela );
                    $this->obTARRParcelaDesconto->setDado ("vencimento", $dtDataVencimento);
                    $this->obTARRParcelaDesconto->setDado ("valor", $nuValorDesconto );
                    $obErro = $this->obTARRParcelaDesconto->inclusao ( $boTransacao );
                }
                //############################################

                if ( !$obErro->ocorreu() ) {
                    //############################################# INSERCAO CARNE
                    $this->roRARRCalculo->obRARRGrupo->obRMONCredito->setCodNatureza( $this->roRARRCalculo->obRMONCredito->getCodNatureza() );
                    $this->roRARRCalculo->obRARRGrupo->obRMONCredito->setCodCredito( $this->roRARRCalculo->obRMONCredito->getCodCredito() );
                    $this->roRARRCalculo->obRARRGrupo->obRMONCredito->setCodGenero( $this->roRARRCalculo->obRMONCredito->getCodGenero() );
                    $this->roRARRCalculo->obRARRGrupo->obRMONCredito->setCodEspecie( $this->roRARRCalculo->obRMONCredito->getCodEspecie() );
                    $this->roRARRCalculo->obRARRGrupo->obRMONCredito->listarCreditos( $rsListaCreditos, $boTransacao );
                    $inCodConvenio = $rsListaCreditos->getCampo("cod_convenio");

                    if(empty($inCodConvenio)){
                        $obErro->setDescricao ( "Obrigatório configurar o Convênio para o Crédito (".$this->roRARRCalculo->obRMONCredito->getCodCredito() .")." );
                        break;
                    }

                    $this->obRARRCarne->obRMONConvenio->setCodigoConvenio( $inCodConvenio );

                    $this->obRARRCarne->obRARRParcela->setCodParcela( $arrParcelasNovas[$cont] );
                    $this->obRARRCarne->obRMONConvenio->listarConvenioBanco( $rsConvenioBanco, $boTransacao );
                    $this->obRARRCarne->obRMONConvenio->obRFuncao->setCodFuncao ($rsConvenioBanco->getCampo("cod_funcao") );
                    $this->obRARRCarne->obRMONConvenio->obRFuncao->obRBiblioteca->roRModulo->setCodModulo( ($rsConvenioBanco->getCampo("cod_modulo") ));
                    $this->obRARRCarne->obRMONConvenio->obRFuncao->obRBiblioteca->setCodigoBiblioteca( ($rsConvenioBanco->getCampo("cod_biblioteca") ));
                    $this->obRARRCarne->obRMONConvenio->obRFuncao->consultar( $boTransacao );

                    $stFNumeracao = "F".$this->obRARRCarne->obRMONConvenio->obRFuncao->getNomeFuncao();
                    $stFNumeracaoMap = CAM_GT_ARR_FUNCAO.$stFNumeracao.".class.php";

                    include_once ( $stFNumeracaoMap );
                    $obFNumeracao = new $stFNumeracao;

                    $stParametros = "'".$rsConvenioBanco->getCampo( "cod_carteira" )."','".$rsConvenioBanco->getCampo( "cod_convenio" )."'";
                    $obFNumeracao->executaFuncao($rsRetorno,$stParametros,$boTransacao);

                    $inNumeracao = $rsRetorno->getCampo( "valor" );
                    $this->obRARRCarne->setNumeracao( $inNumeracao );

                    $obErro = $this->obRARRCarne->incluirCarne( $boTransacao );

                }
                $cont++;
            }
        }
    }

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRLancamento );

    return $obErro;

}

/**
    * Efetuar Lancamento MANUAL GRUPO DE CREDITO
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function efetuarLancamentoManualGrupoCredito($boTransacao = "")
{
    ;
    $boFlagTransacao                     = false;
    $obErro                              = new Erro ;
    Sessao::write( "lancamentos_cods", "" );
    $boDesoneracao                       = false;
    $boLancamentoEconomico = $boLancamentoImobiliario = $boLancamentoCGM = false;

    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    $inAnoExercicio = $this->roRARRCalculo->obRARRGrupo->getExercicio();
    if ( !$obErro->ocorreu() ) {

        $this->roRARRCalculo->obRARRGrupo->listarCreditos($rsCreditos, $boTransacao);
        $this->roRARRCalculo->obRARRGrupo->setExercicio( $inAnoExercicio );

        //insere novo lancamento
        $obErro = $this->obTARRLancamento->proximoCod( $this->inCodLancamento, $boTransacao );
        Sessao::write( "lancamentos_cods", Sessao::read( "lancamentos_cods" ).$this->inCodLancamento."," );
        if ( !$obErro->ocorreu() ) {
            $this->obTARRLancamento->setDado( "cod_lancamento" , $this->inCodLancamento     );
            $this->obTARRLancamento->setDado( "numcgm"         , $this->obRCgm->getNumCGM() );
            $this->obTARRLancamento->setDado( "vencimento"     , $this->getDataVencimento() );
            $this->obTARRLancamento->setDado( "total_parcelas" , $this->getTotalParcelas()  );
            $this->obTARRLancamento->setDado( "valor"          , $this->getValor()          );
            $this->obTARRLancamento->setDado( "observacao_sistema", $this->getObservacaoSistema() );
            $this->obTARRLancamento->setDado( "observacao"     , $this->getObservacao()     );
            if ($boDesoneracao) {
                $this->obTARRLancamento->setDado( "ativo" , false );
            } else {
                $this->obTARRLancamento->setDado( "ativo" , true );
            }
            $obErro = $this->obTARRLancamento->inclusao( $boTransacao );
        }

        //insere tabela lancamento processo
        if ( !$obErro->ocorreu() ) {
            if ( $this->obRProcesso->getCodigoProcesso() ) {
                $this->obTARRLancamentoProcesso->setDado( "cod_lancamento" , $this->inCodLancamento     );
                $this->obTARRLancamentoProcesso->setDado( "cod_processo", $this->obRProcesso->getCodigoProcesso());
                $this->obTARRLancamentoProcesso->setDado( "ano_exercicio", $this->obRProcesso->getExercicio() );

                $obErro = $this->obTARRLancamentoProcesso->inclusao( $boTransacao );
            }
        }

        $contCredito =1;
        while ( !$rsCreditos->eof() ) {
            $arValoresCreditos = Sessao::read( "ValoresCreditos" );
            $flValorCreditoAtual = $arValoresCreditos[$contCredito];

            //INSERE NOVO CALCULO
            $obErro = $this->roRARRCalculo->obTARRCalculo->proximoCod( $this->roRARRCalculo->obTARRCalculo->inCodCalculo, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->roRARRCalculo->obTARRCalculo->setDado ( "cod_calculo" , $this->roRARRCalculo->obTARRCalculo->inCodCalculo );
                $this->roRARRCalculo->obTARRCalculo->setDado ( "cod_credito" , $rsCreditos->getCampo("cod_credito") );
                $this->roRARRCalculo->obTARRCalculo->setDado ( "cod_especie" , $rsCreditos->getCampo("cod_especie") );
                $this->roRARRCalculo->obTARRCalculo->setDado ( "cod_genero"  , $rsCreditos->getCampo("cod_genero"));
                $this->roRARRCalculo->obTARRCalculo->setDado ( "cod_natureza", $rsCreditos->getCampo("cod_natureza") );
                $this->roRARRCalculo->obTARRCalculo->setDado ( "exercicio"   , $this->roRARRCalculo->obRARRGrupo->getExercicio() );
                $this->roRARRCalculo->obTARRCalculo->setDado ( "valor"       , $flValorCreditoAtual );
                $this->roRARRCalculo->obTARRCalculo->setDado ( "nro_parcelas", $this->getTotalParcelas() );
                $this->roRARRCalculo->obTARRCalculo->setDado ( "ativo" , true );
                $this->roRARRCalculo->obTARRCalculo->setDado ( "calculado", false );
                $obErro = $this->roRARRCalculo->obTARRCalculo->inclusao($boTransacao);

            }

            //insere novo lancamento calculo
            if ( !$obErro->ocorreu() ) {
                $this->obTARRLancamentoCalculo->setDado( "cod_lancamento" , $this->inCodLancamento     );
                $this->obTARRLancamentoCalculo->setDado( "cod_calculo"    , $this->roRARRCalculo->obTARRCalculo->inCodCalculo );
                $this->obTARRLancamentoCalculo->setDado( "valor"          , $flValorCreditoAtual );
                $obErro = $this->obTARRLancamentoCalculo->inclusao( $boTransacao );
            }

            if ( !$obErro->ocorreu() AND $boDesoneracao ) {
                $this->obTARRLancamentoConcedeDesoneracao->setDado( "cod_lancamento"  , $this->inCodLancamento  );
                $this->obTARRLancamentoConcedeDesoneracao->setDado( "cod_calculo"     , $this->roRARRCalculo->obTARRCalculo->inCodCalculo );
                $this->obTARRLancamentoConcedeDesoneracao->setDado( "cod_desoneracao" , 1                       );
                $this->obTARRLancamentoConcedeDesoneracao->setDado( "numcgm"          , $obDesoneracao->obRCGM->getNumCGM()               );
                $this->obTARRLancamentoConcedeDesoneracao->setDado( "ocorrencia"      , $obDesoneracao->inOcorrencia                      );
                $obErro = $this->obTARRLancamentoConcedeDesoneracao->inclusao( $boTransacao );
            }

            //insere novo calculo_grupo_vencimento
            if ( !$obErro->ocorreu() ) {
                $this->roRARRCalculo->obTARRCalculoGrupoCredito->setDado( "cod_calculo" , $this->roRARRCalculo->obTARRCalculo->inCodCalculo     );
                $this->roRARRCalculo->obTARRCalculoGrupoCredito->setDado( "cod_grupo", $this->roRARRCalculo->obRARRGrupo->getCodGrupo() );
                $this->roRARRCalculo->obTARRCalculoGrupoCredito->setDado( "ano_exercicio", $inAnoExercicio );
                $obErro = $this->roRARRCalculo->obTARRCalculoGrupoCredito->inclusao( $boTransacao );
            }

            //INSERE CALCULO PERTENCENTE
            if ( $this->obRCgm->getNumCGM( ) ) {

                $boLancamentoCGM = true;

                $this->roRARRCalculo->obTARRCalculoCgm = new TARRCalculoCgm;
                $this->roRARRCalculo->obTARRCalculoCgm->setDado ( "cod_calculo", $this->roRARRCalculo->obTARRCalculo->inCodCalculo );
                $this->roRARRCalculo->obTARRCalculoCgm->setDado ( "numcgm", $this->obRCgm->getNumCGM( ) );
                $obErro = $this->roRARRCalculo->obTARRCalculoCgm->inclusao($boTransacao);

            } elseif ( $this->obRCEMInscricaoEconomica->getInscricaoEconomica() ) {

                $boLancamentoEconomico = true;
                $this->roRARRCalculo->obTARRCadEconomicoCalculo = new TARRCadastroEconomicoCalculo;

                $this->obRCEMInscricaoEconomica->consultarInscricaoEconomicaBaixa($rsEmpresaBaixa,$boTransacao );
                if ( $rsEmpresaBaixa->getNumLinhas() > 0 ) {
                    $obErro->setDescricao ( "Código de inscrição econômica inválido. <b>Empresa Baixada</b>  (". $this->obRCEMInscricaoEconomica->getInscricaoEconomica() .")" );
                } else {

                    $this->roRARRCalculo->obTARRCadEconomicoCalculo->setDado ( "cod_calculo", $this->roRARRCalculo->obTARRCalculo->inCodCalculo );
                    $this->roRARRCalculo->obTARRCadEconomicoCalculo->setDado ( "inscricao_economica", $this->obRCEMInscricaoEconomica->getInscricaoEconomica() );

                    $this->recuperaTimestampCadastroEconomicoFaturamento( $rsEconomico, $boTransacao);

                    if ( $rsEconomico->getNumLinhas() < 1 || $rsEconomico->getCampo('timestamp') == null ) {

                        include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCadastroEconomicoFaturamento.class.php" );
                        $obTARRCEFaturamento = new TARRCadastroEconomicoFaturamento;

                        $obTARRCEFaturamento->setDado ('inscricao_economica', $this->obRCEMInscricaoEconomica->getInscricaoEconomica() );
                        $arVencimento = $this->getDataVencimento();
                        $obTARRCEFaturamento->setDado ('competencia', $arVencimento[1].'/'.$arVencimento[0]);
                        $obErro = $obTARRCEFaturamento->inclusao( $boTransacao );

                        $this->recuperaTimestampCadastroEconomicoFaturamento( $rsEconomico, $boTransacao);

                    }

                    $this->roRARRCalculo->obTARRCadEconomicoCalculo->setDado ( "timestamp", $rsEconomico->getCampo( 'timestamp' ) );

                    $obErro = $this->roRARRCalculo->obTARRCadEconomicoCalculo->inclusao($boTransacao);

                    //busca o cgm da empresa
                    if ( !$obErro->ocorreu() ) {

                        $obErro = $this->obRCEMInscricaoEconomica->consultarInscricaoEconomica ( $rsInscricao, $boTransacao );
                        //consultarInscricaoEconomica

                        while ( !$rsInscricao->eof() && !$obErro->ocorreu() ) {

                            $this->roRARRCalculo->obTARRCalculoCgm = new TARRCalculoCgm;
                            $this->roRARRCalculo->obTARRCalculoCgm->setDado ( "cod_calculo", $this->roRARRCalculo->obTARRCalculo->inCodCalculo );
                            $this->roRARRCalculo->obTARRCalculoCgm->setDado ( "numcgm", $rsInscricao->getCampo('numcgm') );

                            $obErro = $this->roRARRCalculo->obTARRCalculoCgm->inclusao($boTransacao);
                            $rsInscricao->proximo();

                        }
                    }
                }
            } elseif ( $this->obRCIMImovel->getNumeroInscricao() ) {

                $boLancamentoImobiliario = true;

                $this->roRARRCalculo->obTARRImovelCalculo = new TARRImovelCalculo;
                $rsImovelBaixa = new RecordSet;
                $this->obRCIMImovel->verificaBaixaImovel ( $rsImovelBaixa, $boTransacao );

                if ( $rsImovelBaixa->getNumLinhas() > 0 ) {
                    $obErro->setDescricao( "Código de inscrição imobiliária inválido. <b>Imóvel Baixado</b>  (". $this->obRCIMImovel->getNumeroInscricao() .")" );
                } else {

                    $this->roRARRCalculo->obTARRImovelCalculo->setDado ( "cod_calculo", $this->roRARRCalculo->obTARRCalculo->inCodCalculo );
                    $this->roRARRCalculo->obTARRImovelCalculo->setDado ( "inscricao_municipal", $this->obRCIMImovel->getNumeroInscricao() );
                    $this->roRARRCalculo->obRCIMImovel->setNumeroInscricao ($this->obRCIMImovel->getNumeroInscricao());
                    $this->roRARRCalculo->recuperaTimestampImovelVenal( $rsImovel, $boTransacao);
                    $this->roRARRCalculo->obTARRImovelCalculo->setDado ( "timestamp", $rsImovel->getCampo("timestamp")  );
                    $obErro = $this->roRARRCalculo->obTARRImovelCalculo->inclusao($boTransacao);

                    if ( !$obErro->ocorreu() ) {
                        $obRCIMProprietario  = new RCIMProprietario ( $this->obRCIMImovel );
                        $obRCIMProprietario->listarProprietariosPorImovel($rsProprietarios , $boTransacao );

                        while ( !$rsProprietarios->eof() ) {

                            $this->roRARRCalculo->obTARRCalculoCgm = new TARRCalculoCgm;
                            $this->roRARRCalculo->obTARRCalculoCgm->setDado ( "cod_calculo", $this->roRARRCalculo->obTARRCalculo->inCodCalculo );
                            $this->roRARRCalculo->obTARRCalculoCgm->setDado ( "numcgm", $rsProprietarios->getCampo('numcgm') );
                            $obErro = $this->roRARRCalculo->obTARRCalculoCgm->inclusao($boTransacao);
                            $rsProprietarios->proximo();

                        }
                    }
                }
            }

            $contCredito++;
            $rsCreditos->proximo();
        } // fim do RecordSet dos CREDITOS

        # VERIFICACOES SE ATUALIZA LANCAMENTO ANTERIOR COMO RECALCULO
        # ocorre quando:
        #   a ) exercicio for o mesmo
        #   b b) Se for GRUPO, busca calculo para este credito na calculo_grupo_credito
        # FIM VERIFICACOES SE ATUALIZA LANCAMENTO ANTERIOR COMO RECALCULO

        //PARCELAS
        if ( !$obErro->ocorreu() AND !$boDesoneracao ) {

            if ($this->getTotalParcelas() > 0 ) {
                $nuValorParcelaTMP = number_format ( ( $this->getValor()   / $this->getTotalParcelas() )   , 2 );
            }
            $nuValorParcelaTMP = str_replace (',','', $nuValorParcelaTMP );

            //-------------CALCULA VALOR DA PRIMEIRA PARCELA
            if ( ($nuValorParcelaTMP * $this->getTotalParcelas()) != $this->getValor() ) {
                $nuValorPrimeiraParcela = $nuValorParcelaTMP + ($this->getValor() - ( $nuValorParcelaTMP *  $this->getTotalParcelas()  ));
                $nuValorPrimeiraParcela = number_format ( $nuValorPrimeiraParcela, 2 );
            } else {
                $nuValorPrimeiraParcela = $nuValorParcelaTMP;
            }
            //-------------CALCULA VALOR DA PRIMEIRA PARCELA

            $cont = 0;
            $contParcelaNormal = 1;
            $arrParcelasNovas = array();
            $boFlagPrimeiraParcela = true;
            $arParcelas = Sessao::read( "parcelas" );
            while ( $cont < count ( $arParcelas ) ) {
                $dtDataVencimento = $arParcelas[$cont]['data_vencimento'];
                if ($arParcelas[$cont]['stTipoParcela'] == 'Única') {
                    $numeroParcela = 0;
                    $nuValorParcela = $this->getValor();
                } elseif ($arParcelas[$cont]['stTipoParcela'] == 'Baixa') {
                    $numeroParcela = 1;
                    $nuValorParcela = $this->getValor();
                } else {
                    if ($boFlagPrimeiraParcela) {
                        $nuValorParcela = str_replace(',','',$nuValorPrimeiraParcela);
                        $boFlagPrimeiraParcela = false;
                    } else {
                        $nuValorParcela = $nuValorParcelaTMP;
                    }
                    $numeroParcela = $arParcelas[$cont]['stTipoParcela'];
                }

                $obErro = $this->obTARRParcela->proximoCod( $this->inCodParcela, $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $arrParcelasNovas[$cont] = $this->inCodParcela;
                    $this->obTARRParcela->setDado( "cod_parcela"    , $this->inCodParcela            );
                    $this->obTARRParcela->setDado( "cod_lancamento" , $this->inCodLancamento );
                    $this->obTARRParcela->setDado( "nr_parcela" , $numeroParcela                        );
                    $this->obTARRParcela->setDado( "vencimento" , $dtDataVencimento                 );
                    $this->obTARRParcela->setDado( "valor"      , $nuValorParcela                            );
                    $obErro = $this->obTARRParcela->inclusao( $boTransacao );
                    //FIM INSERCAO PARCELA
                }

                //INSERCAO NA TABELA DESCONTO #####################
                if ( !$obErro->ocorreu() && $arParcelas[$cont]['valor'] > 0.00 ) {
                    $nuValorDescontoTMP = $arParcelas[$cont]['valor'] ;

                    if ($arParcelas[$cont]['stTipoParcela'] == 'Única') {

                        $rsCreditos->setPrimeiroElemento();
                        $somaValoresDescontados = 0.00;
                        $conValorCredito = 1;
                        $inTotalCredito = 0;
                        while ( !$rsCreditos->eof() ) {
                            if ( $rsCreditos->getCampo ('desconto') != 'f' ) {
                                $arValoresCreditos = Sessao::read( "ValoresCreditos" );
                                $valorTMP = str_replace ( ',', '.', str_replace ('.', '', $arValoresCreditos[$conValorCredito] ) );
                                $somaValoresDescontados += $valorTMP;
                                $inTotalCredito++;
                            }
                            $conValorCredito++;
                            $rsCreditos->proximo();
                        }

                        $totalDescontos = 0.00;
                        $rsCreditos->setPrimeiroElemento();
                        $conValorCredito = 1;

                        $inX = 0;

                        while ( !$rsCreditos->eof() ) {
                            $arValoresCreditos = Sessao::read( "ValoresCreditos" );
                            $valorCreditoTMP = str_replace(',','.', str_replace ( '.','', $arValoresCreditos[$conValorCredito] ) );

                            if ( $rsCreditos->getCampo ('desconto') != 'f' ) {
                                if ($arParcelas[$cont]['stTipoDesconto'] == 'Percentual') {
                                    $valorDescontar = ( $valorCreditoTMP * $nuValorDescontoTMP/100  );
                                } else {
                                    $valorDescontar = ( $valorCreditoTMP * 100 ) / $somaValoresDescontados;
                                    $valorDescontar = ( $valorDescontar * $nuValorDescontoTMP ) / 100;
                                }

                                $totalDescontos += $valorCreditoTMP - $valorDescontar;
                            } else {
                                $totalDescontos += $valorCreditoTMP;
                            }
                            $totalDescontos = str_replace ( ',', '', number_format ( $totalDescontos , 2 ));

                            $conValorCredito++;
                            $rsCreditos->proximo();
                        }

                        $nuValorDesconto = number_format ( $totalDescontos , 2 );
                        $nuValorDesconto = str_replace ( ',', '', $nuValorDesconto );
                } else {  //parcela normal

                        $rsCreditos->setPrimeiroElemento();
                        $somaValoresDescontados = 0.00;
                        $conValorCredito = 1;

                        $inTotalCredito = 0;
                        $inX = 0;
                        while ( !$rsCreditos->eof() ) {
                            if ( $rsCreditos->getCampo ('desconto') != 'f' ) {
                                $arValoresCreditos = Sessao::read( "ValoresCreditos" );
                                $valorTMP = str_replace ( ',', '.', str_replace ('.', '', $arValoresCreditos[$conValorCredito] ) );
                                $somaValoresDescontados += $valorTMP;
                                $inTotalCredito++;
                            }
                            $conValorCredito++;
                            $rsCreditos->proximo();
                        }
                        $somaValoresDescontados = $somaValoresDescontados / $this->getTotalParcelas();

                        $totalDescontos = 0.00;
                        $rsCreditos->setPrimeiroElemento();
                        $conValorCredito = 1;
                        while ( !$rsCreditos->eof() ) {
                            $arValoresCreditos = Sessao::read( 'ValoresCreditos' );
                            $valorCreditoTMP = str_replace(',','.', str_replace ( '.','', $arValoresCreditos[$conValorCredito] ) );
                            $valorCreditoTMP = $valorCreditoTMP/$this->getTotalParcelas();
                            if ( $rsCreditos->getCampo ('desconto') != 'f' ) {

                                if ($arParcelas[$cont]['stTipoDesconto'] == 'Percentual') {
                                    $valorDescontar = ( $valorCreditoTMP*$nuValorDescontoTMP/100  );
                                } else {
                                    $valorDescontar = ( $valorCreditoTMP * 100 ) / $somaValoresDescontados;
                                    $valorDescontar = ( $valorDescontar * $nuValorDescontoTMP ) / 100;

                                }

                                $totalDescontos += $valorCreditoTMP - $valorDescontar;
                            } else {
                                $totalDescontos += $valorCreditoTMP;
                            }

                            $totalDescontos = str_replace ( ',', '', number_format ( $totalDescontos , 2 ));
                            $conValorCredito++;
                            $rsCreditos->proximo();
                        }

                        $nuValorDesconto = number_format ( $totalDescontos , 2 );
                        $nuValorDesconto = str_replace ( ',', '', $nuValorDesconto );

                     } //fim descontos
                    $this->obTARRParcelaDesconto->setDado ("cod_parcela", $this->inCodParcela );
                    $this->obTARRParcelaDesconto->setDado ("vencimento", $dtDataVencimento);
                    $this->obTARRParcelaDesconto->setDado ("valor", $nuValorDesconto );
                    $obErro = $this->obTARRParcelaDesconto->inclusao ( $boTransacao );

                }

                if ( !$obErro->ocorreu() ) {

                    //############################################# INSERCAO CARNE

                    $rsCreditos->setPrimeiroElemento();
                    $inCodConvenio = $rsCreditos->getCampo( "cod_convenio" );
                    $inAnoExercicio = $rsCreditos->getCampo( "ano_exercicio" );

                    $this->obRARRCarne->setExercicio( $inAnoExercicio );

                    $this->obRARRCarne->obRMONConvenio->setCodigoConvenio( $inCodConvenio );
                    $this->obRARRCarne->obRARRParcela->setCodParcela( $arrParcelasNovas[$cont] );
                    $this->obRARRCarne->obRMONConvenio->listarConvenioBanco( $rsConvenioBanco, $boTransacao );

                    $this->obRARRCarne->obRMONConvenio->obRFuncao->setCodFuncao ($rsConvenioBanco->getCampo("cod_funcao"));
                    $this->obRARRCarne->obRMONConvenio->obRFuncao->obRBiblioteca->roRModulo->setCodModulo( ($rsConvenioBanco->getCampo("cod_modulo") ));
                    $this->obRARRCarne->obRMONConvenio->obRFuncao->obRBiblioteca->setCodigoBiblioteca( ($rsConvenioBanco->getCampo("cod_biblioteca") ));
                    $this->obRARRCarne->obRMONConvenio->obRFuncao->consultar( $boTransacao );

                    $stFNumeracao = "F".$this->obRARRCarne->obRMONConvenio->obRFuncao->getNomeFuncao();
                    $stFNumeracaoMap = "../../classes/funcao/".$stFNumeracao.".class.php";
                    include_once ( $stFNumeracaoMap );
                    $obFNumeracao = new $stFNumeracao;

                    $stParametros = "'".$rsConvenioBanco->getCampo( "cod_carteira" )."','".$rsConvenioBanco->getCampo( "cod_convenio" )."'";
                    $obFNumeracao->executaFuncao($rsRetorno,$stParametros,$boTransacao);

                    $inNumeracao = $rsRetorno->getCampo( "valor" );
                    $this->obRARRCarne->setNumeracao( $inNumeracao );

                    $obErro = $this->obRARRCarne->incluirCarne( $boTransacao );
                }
                $cont++;
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRLancamento );

    return $obErro;

}

/**
    * Efetuar Lancamento por PARCELAMENTO
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function efetuarLancamentoParcelamento($boTransacao = "")
{
    ;
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    $obErro = new Erro ;

    if ( !$obErro->ocorreu() ) {

        //insere novo lancamento
        $obErro = $this->obTARRLancamento->proximoCod( $this->inCodLancamento, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTARRLancamento->setDado( "cod_lancamento" , $this->inCodLancamento     );
            $this->obTARRLancamento->setDado( "numcgm"         ,      $this->obRCgm->getNumCGM() );
            $this->obTARRLancamento->setDado( "vencimento"     , $this->getDataVencimento() );
            $this->obTARRLancamento->setDado( "total_parcelas" , $this->getTotalParcelas()  );
            $this->obTARRLancamento->setDado( "valor"          , $this->getValor()            );
            $this->obTARRLancamento->setDado( "ativo"          , TRUE                       );
            $this->obTARRLancamento->setDado( "observacao"     , ''                         );
            $obErro = $this->obTARRLancamento->inclusao( $boTransacao );
        }

        //salva as parcelas originais utilizadas
        if ( !$obErro->ocorreu() ) {
            include_once ( CAM_GT_ARR_MAPEAMENTO."TARRParcelamentoLancamento.class.php"     );
            $obTARRParcelamento = new TARRParcelamentoLancamento;

            $cont = 0;
            $arSessaoTransf5 = Sessao::read( "sessao_transf5" );
            while ( $cont < count ( $arSessaoTransf5 )) {

                $obTARRParcelamento->setDado ('cod_lancamento', $arSessaoTransf5[$cont]['cod_lancamento']);
                $obTARRParcelamento->setDado ('cod_parcela', $arSessaoTransf5[$cont]['cod_parcela']);
                $obTARRParcelamento->inclusao( $boTransacao );
                $cont++;
            }
        }
        //---------------------------
        if ( !$obErro->ocorreu() ) {
            //insere parcelas
            if ($this->getTotalParcelas() > 0 ) {
                $nuValorParcela  = round(  ( $this->getValor()  / $this->getTotalParcelas() ) , 2 );
            }

            $cont = 0;
            $arrParcelasNovas = array();
            $arSessaoTransf6 = Sessao::read( "sessao_transf6" );
            while ( $cont < count ( $arSessaoTransf6 )) {

                $dtDataVencimento = $arSessaoTransf6[$cont];

                $obErro = $this->obTARRParcela->proximoCod( $this->inCodParcela, $boTransacao );
                $arrParcelasNovas[$cont] = $this->inCodParcela;
                $this->obTARRParcela->setDado( "cod_parcela"    , $this->inCodParcela );
                $this->obTARRParcela->setDado( "cod_lancamento" , $this->inCodLancamento );
                $this->obTARRParcela->setDado( "nr_parcela" , $cont + 1                           );
                $this->obTARRParcela->setDado( "vencimento" , $dtDataVencimento                       );
                $this->obTARRParcela->setDado( "valor"      , $nuValorParcela );

                $obErro = $this->obTARRParcela->inclusao( $boTransacao );

                //FIM INSERCAO PARCELA

                if ( !$obErro->ocorreu() ) {

                    //############################################# INSERCAO CARNE
                    include_once ( CAM_GT_ARR_NEGOCIO."RARRConfiguracao.class.php"     );
                    $obRARRConfiguracao = new RARRConfiguracao;
                    $obRARRConfiguracao->setCodModulo( 25 );
                    $obRARRConfiguracao->setExercicio( Sessao::getExercicio() );
                    $obRARRConfiguracao->consultar( $boTransacao );
                    $inCodConvenio = $obRARRConfiguracao->getConvenioParcelamento();
                    $inCodConvenio = 100;

                    $this->obRARRCarne->setExercicio( Sessao::getExercicio() );
                    $this->obRARRCarne->obRMONConvenio->setCodigoConvenio( $inCodConvenio );

                    $this->obRARRCarne->obRARRParcela->setCodParcela( $arrParcelasNovas[$cont] );
                    $this->obRARRCarne->obRMONConvenio->listarConvenioBanco( $rsConvenioBanco, $boTransacao );

                    $this->obRARRCarne->obRMONConvenio->obRFuncao->setCodFuncao ($rsConvenioBanco->getCampo("cod_funcao"));
                    $this->obRARRCarne->obRMONConvenio->obRFuncao->obRBiblioteca->roRModulo->setCodModulo( ($rsConvenioBanco->getCampo("cod_modulo") ));
                    $this->obRARRCarne->obRMONConvenio->obRFuncao->obRBiblioteca->setCodigoBiblioteca( ($rsConvenioBanco->getCampo("cod_biblioteca") ));
                    $this->obRARRCarne->obRMONConvenio->obRFuncao->consultar( $boTransacao );

                    $stFNumeracao = "F".$this->obRARRCarne->obRMONConvenio->obRFuncao->getNomeFuncao();
                    $stFNumeracaoMap = "../../classes/funcao/".$stFNumeracao.".class.php";
                    include_once ( $stFNumeracaoMap );
                    $obFNumeracao = new $stFNumeracao;

                    $stParametros = "'".$rsConvenioBanco->getCampo( "cod_carteira" )."','".$rsConvenioBanco->getCampo( "cod_convenio" )."'";
                    $obFNumeracao->executaFuncao($rsRetorno,$stParametros,$boTransacao);

                    $inNumeracao = $rsRetorno->getCampo( "valor" );
                    $this->obRARRCarne->setNumeracao( $inNumeracao );

                    $obErro = $this->obRARRCarne->incluirCarne( $boTransacao );
                }
                $cont++;
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRLancamento );

    return $obErro;

}
/*******************************************************************************************************************************************
/*******************************************************************************************************************************************
*/
function efetuarLancamentoParcialIndividual($boTransacao = "" , $arCalculo = "", $boLnEc)
{
    $obErro = new Erro ;
    set_time_limit(0);
    if ( !$obErro->ocorreu() ) {

        if (!$boLnEc) {
            $boLancamentoEconomico = false;

        }

        if ( !$obErro->ocorreu() ) { // chave mestre 1

            if ( is_array( $arCalculo ) ) {
                $rsCalculos = new RecordSet;
                $rsCalculos->preenche( $arCalculo );
                $inNumCGM = $this->roRARRCalculo->obRCGM->getNumCgm();

                if ($boLnEc) {
                    $boLancamentoEconomico = true;
                    $stTipoInscricao = "inscricao_economica";
                    $arCredito = explode( ".", $this->roRARRCalculo->getChaveCredito() );
                    $this->roRARRCalculo->obRMONCredito->setCodCredito( $arCredito[0] );
                    $this->roRARRCalculo->obRMONCredito->setCodEspecie( $arCredito[1] );
                    $this->roRARRCalculo->obRMONCredito->setCodGenero( $arCredito[2] );
                    $this->roRARRCalculo->obRMONCredito->setCodNatureza( $arCredito[3] );
                } else {
                    //busca creditos do Grupo de Credito selecionado
                    $stTipoInscricao = "inscricao_municipal";
                    $this->roRARRCalculo->obRARRGrupo->listarCreditos( $rsCreditosGrupo, $boTransacao );
                }
            } else {
                //busca creditos do Grupo de Credito selecionado
                $this->roRARRCalculo->obRARRGrupo->listarCreditos( $rsCreditosGrupo, $boTransacao );

                // Vamos buscar todos os calculos do grupo de credito
                $this->roRARRCalculo->listarCalculosGrupo( $rsCalculos, $boTransacao );
                $inNumCGM = $rsCalculos->getCampo('numcgm');
            }

            if (!$boLancamentoEconomico) {
                //recupera descontos do grupo de credito
                $this->roRARRCalculo->obRARRGrupoVencimento->roRARRCalendarioFiscal->setCodigoGrupo($rsCreditosGrupo->getCampo('cod_grupo'));
                //o rsDescontos nao esta sendo usado em nenhum lugar

                //recupera o numero total de parcelas para o vencimento
                $obErro = $this->roRARRCalculo->obRARRGrupoVencimento->listarParcela($rsParcelas, $boTransacao);
            }

            //$rsParcelas = new RecordSet; ???
            if ( !$obErro->ocorreu() && $this->roRARRCalculo->getTipoCalculo() == 1 ) { //o tipo do calculo soh eh um no total
                $inCountParcelas = 0;
                $inParcela       = 1;
                $arConfParcelas = array();
                while ( !$rsParcelas->eof() ) {
                    $arConfParcelas[$inParcela]['stTipoParcela'  ]      = $inParcela;
                    $arConfParcelas[$inParcela]['data_vencimento'] = $rsParcelas->getCampo( "data_vencimento" );
                    if ( $rsParcelas->getCampo('valor') > 0 ) {
                        $arConfParcelas[$inParcela]['valor']         = $rsParcelas->getCampo('valor');

                        if ( $rsParcelas->getCampo('percentual') == 't' ) $stTipoDesconto = 'Percentual';
                        else $stTipoDesconto = 'Absoluto';

                        $arConfParcelas[$inParcela]['stTipoDesconto']  = $stTipoDesconto;
                        $arConfParcelas[$inParcela]['data_vencimento_desconto'] = $rsParcelas->getCampo('data_vencimento_desconto');
                    } else {
                        $arConfParcelas[$inParcela]['desconto']                 = 0;
                        $arConfParcelas[$inParcela]['tipo_desconto']            = '';
                        $arConfParcelas[$inParcela]['data_vencimento_desconto'] = '';
                    }
                    $inParcela++;
                    $inCountParcelas++;
                    $rsParcelas->proximo();
                }
                Sessao::write( "parcelas", $arConfParcelas );
            }

            $inFirstLoop    = TRUE;
            $boLancPago     = FALSE;
            $nuValorCalculo = 0;
            $inCountCalc    = 0;
            if ( !is_object($this->obRARRDesoneracao) )
                $this->obRARRDesoneracao = new RARRDesoneracao;
            while ( !$rsCalculos->eof() ) {

                if (!$boTransacao) {
                    $boFlagTransacao = false;
                    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
                }

                $boUsaDesoneracao = FALSE;
                $nuValorCalculo = 0;
                $nuValorCalculoADesc = 0; // VALOR A QUAL SE APLICA O DESCONTO

                if ( ( $inFirstLoop == TRUE ) OR ( $stIMAnterior !== $rsCalculos->getCampo($stTipoInscricao ))) {
                    //recupera valor total do calculo
                    unset( $this->roRARRCalculo->inCodCalculo );

                    if ($boLancamentoEconomico) {
                        $this->roRARRCalculo->obRCEMInscricaoEconomica->setInscricaoEconomica( $rsCalculos->getCampo( $stTipoInscricao ) );
                        $inTmpVal = $this->roRARRCalculo->obRModulo->getCodModulo();
                        $this->roRARRCalculo->obRModulo->setCodModulo(14);
                        $this->roRARRCalculo->buscaValorCalculoCredito( $rsValorCalculo , $boTransacao );
                        $this->roRARRCalculo->obRModulo->setCodModulo($inTmpVal);
                    } else {
                        $this->roRARRCalculo->obRCIMImovel->setNumeroInscricao( $rsCalculos->getCampo( $stTipoInscricao ) );
                        $obErro = $this->roRARRCalculo->buscaValorCalculo( $rsValorCalculo , $boTransacao );
                    }

                    // busca valor do lancamento anterior
                    if (!$boLancamentoEconomico) {
                        $this->obRCIMImovel->setNumeroInscricao( $rsCalculos->getCampo('inscricao_municipal'));
                        $obErro = $this->buscaValorLancamentoAnteriorGrupo( $rsLancAnt , $boTransacao);
                    } else {
                        $this->obRCEMInscricaoEconomica->setInscricaoEconomica( $rsCalculos->getCampo("inscricao_economica") );
                        $obErro = $this->buscaValorLancamentoAnteriorCreditoCadEco( $rsLancAnt , $boTransacao);
                    }

                    if ( !$obErro->ocorreu() && $rsLancAnt->getCampo("valor") ) {
                        $arLancamentoAnterior = explode( "-", $rsLancAnt->getCampo("valor") );
                        $nuVlrLancAnterior       = $arLancamentoAnterior[0];
                        $inCodLancamentoAnterior = $arLancamentoAnterior[1];
                        if ( !$obErro->ocorreu() ) {
                            $obErro = $this->buscaSomaParcelasPagasLancamento( $rsSoma , $inCodLancamentoAnterior, $boTransacao);
                            if ( !$obErro->ocorreu() ) {
                                $nuSomaPagamentos = $rsSoma->getCampo('valor');
                                $obErro = $this->buscaSomaParcelasUnicasPagasLancamento( $rsSomaU , $inCodLancamentoAnterior, $boTransacao);
                                if ( !$obErro->ocorreu() ) {
                                    $nuSomaPagUnicas  = $rsSomaU->getCampo('valor');
                                }
                            }
                        }
                    }

                    /*
                     * Retorno valor do calculo com desoneração aplicada
                     * */
                    $nuDiffDesoneraCredito = 0;
                    $nuValorCalculoCredito = $rsCalculos->getCampo( 'valor' );

                    $arDesoneracoes = array();
                    if ($boLancamentoEconomico) {
                        $this->usaDesoneracaoCadEco(    $arDesoneracoes
                                                        , $nuDiffDesoneraCredito
                                                        , $nuValorCalculoCredito
                                                        , $inCodLancamentoAnterior
                                                        , $rsCalculos->getCampo( 'inscricao_economica' )
                                                        , $rsCalculos->getCampo( 'cod_calculo' )
                                                        , $boTransacao );
                    } else {
                        $this->usaDesoneracao(    $arDesoneracoes
                                                , $nuDiffDesoneraCredito
                                                , $nuValorCalculoCredito
                                                , $inCodLancamentoAnterior
                                                , $rsCalculos->getCampo( 'inscricao_municipal' )
                                                , $rsCalculos->getCampo( 'cod_calculo' )
                                                , $boTransacao );
                    }

                    if ( !is_object($obDesoneracao))
                        $obDesoneracao = new RARRDesoneracao;

                    if ($nuDiffDesoneraCredito > 0) {
                        // prepara desoneração
                        $obDesoneracao->setCodigo         ( 1 );

                        if ( !$obErro->ocorreu() ) {
                            //pegar cgm do calculo_cgm
                            include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCalculoCgm.class.php" );
                            $obTARRCalculoCgm = new TARRCalculoCgm;
                            $stFiltro = " WHERE cod_calculo = ".$rsCalculos->getCampo( 'cod_calculo' );
                            $obTARRCalculoCgm->recuperaTodos( $rsListCGM, $stFiltro, "", $boTransacao );

                            //inserir cgm do calculo_cgm
                            $obDesoneracao->obRCGM->setNumCGM ( $rsListCGM->getCampo('numcgm')  );
                        }

                        if ($boLancamentoEconomico) {
                            $obDesoneracao->setInscricaoEconomica( $this->obRCEMInscricaoEconomica->getInscricaoEconomica() );
                        } else {
                            $obDesoneracao->setInscricaoImovel( $this->obRCIMImovel->getNumeroInscricao() );
                        }

                        $obDesoneracao->obRCadastroDinamico->addAtributosDinamicos( 1 , $nuDiffDesoneraCredito );
                        $obDesoneracao->setCodigoLancamento( $this->inCodLancamento );
                        $boDesoneracaodeUso = TRUE;
                        $boUsaDesoneracao = TRUE;
                    } else {
                        $boUsaDesoneracao = TRUE;
                    }

                    $rsCalculos->setCampo ( 'valor' , $nuValorCalculoCredito );

                    if ( !$obErro->ocorreu() ) { // chave 10
                        $arCreditoValor = array();
                        $arTmp = $nuTmp1 = $nuTmp2 = 0;
                        while ( !$rsValorCalculo->eof() ) {
                            $nuTmp2 = $rsValorCalculo->getCampo( "valor" );
                            if ($boLancamentoEconomico) {
                                $this->usaDesoneracaoCadEco(    $arTmp
                                                                , $nuTmp1
                                                                , $nuTmp2
                                                                , $inCodLancamentoAnterior
                                                                , $rsValorCalculo->getCampo( 'inscricao_economica' )
                                                                , $rsValorCalculo->getCampo( 'cod_calculo' )
                                                                , $boTransacao );
                            } else {
                                $this->usaDesoneracao(    $arTmp
                                                        , $nuTmp1
                                                        , $nuTmp2
                                                        , $inCodLancamentoAnterior
                                                        , $rsValorCalculo->getCampo( 'inscricao_municipal' )
                                                        , $rsValorCalculo->getCampo( 'cod_calculo' )
                                                        , $boTransacao );
                            }

                            $rsValorCalculo->setCampo( 'valor' , $nuTmp2 ) ;

                            $nuValorCalculo += $rsValorCalculo->getCampo( "valor" );
                            if ( $rsValorCalculo->getCampo( "desconto") == 't' ) {
                                $nuValorCalculoADesc += $rsValorCalculo->getCampo( "valor" );
                            }
                            $arCreditoValor[$rsValorCalculo->getCampo('cod_credito')]['valor']    = $rsValorCalculo->getCampo( "valor" );
                            $arCreditoValor[$rsValorCalculo->getCampo('cod_credito')]['desconto'] = $rsValorCalculo->getCampo( "desconto" );
                            $rsValorCalculo->proximo();
                        }
                        $arContar = Sessao::read( 'parcelas' );
                        $inC = 0;
                        foreach ($arContar as $value) {
                            if ( $value['stTipoParcela'] <> 'Única' )
                                $inC++;
                        }

                        //verifica se o grupo de credito e itbi
                        $obTAdministracaoConfiguracao = new TAdministracaoConfiguracao;
                        $obTAdministracaoConfiguracao->setDado("exercicio",$rsCalculos->getCampo("exercicio"));
                        $obTAdministracaoConfiguracao->pegaConfiguracao($stValor, 'grupo_credito_itbi', $boTransacao);

                        $stGrupo = explode( '/', $stValor );

                        //verifca se ja existem pagamentos no lancamento anterior e se o grupo de credito e itbi
                        if ( ( $nuSomaPagUnicas > 0 ) || ( $nuSomaPagamentos > 0 ) && ( $stGrupo[0] != $rsCalculos->getCampo("cod_grupo") || $stGrupo[1] != $rsCalculos->getCampo("exercicio") ) ) {
                            /*
                                VERIFICA VALORES PARA CONCEDER DESONERACAO
                            */

                            if ( ($this->obRCIMImovel->getNumeroInscricao() && !$boLancamentoEconomico) || ( $this->obRCEMInscricaoEconomica->getInscricaoEconomica() && $boLancamentoEconomico) ) {

                                $inCodLancamentoAtual = $this->getCodLancamento();
                                $this->setCodLancamento( $inCodLancamentoAnterior );

                                if ($boLancamentoEconomico) {
                                    $this->listarCalculosPorCredito( $rsCalculoLancAnterior, $boTransacao );
                                } else {
                                    $this->listarCalculosCredito( $rsCalculoLancAnterior, $boTransacao );
                                }

                                $this->setCodLancamento( $inCodLancamentoAtual );
                                $inCount = count($arCalculo);

                                $boLancaCredito       = FALSE;
                                $nuLancaCredito       = 0;
                                $inCountDesoneracao   = 0;
                                $inCountNaoDesonerado = 0;

                                $rsCalculoLancAnterior->setPrimeiroElemento();
                                while ( !$rsCalculoLancAnterior->eof() ) {
                                    $nuValorLancAnterior += $rsCalculoLancAnterior->getCampo('valor');
                                    $rsCalculoLancAnterior->proximo();
                                }
                                $rsCalculoLancAnterior->setPrimeiroElemento();
                                for ($i = 0; $i < $inCount; $i++) {
                                    if ($nuSomaPagamentos > 0) {
                                        $inPercentCalc = $rsCalculoLancAnterior->getCampo('valor') / $nuValorLancAnterior;
                                        $inValorPago = round( $nuSomaPagamentos * $inPercentCalc, 2 );
                                        $nuDifCalculo = ( $arCalculo[$i]['valor'] - $inValorPago );
                                    } else {
                                        $nuDifCalculo = ( $arCalculo[$i]['valor'] - $rsCalculoLancAnterior->getCampo('valor_calculado') );
                                    }
                                    $nuDifCalculo = round( $nuDifCalculo , 2 );
                                    if ( ( ( $nuDifCalculo ) < 0 )) {
                                        $arCalculosDesoneracao[$inCountDesoneracao]['cod_calculo'] = $arCalculo[$i]['cod_calculo'];
                                        $arCalculosDesoneracao[$inCountDesoneracao]['valor'] = $nuDifCalculo * -1;
                                        $boDesoneracao = TRUE;
                                        $arCalculo[$i]['valor'] = 0;
                                        $nuDifPag += $nuDifCalculo;
                                        $inCountDesoneracao++;
                                    } else {
                                        $boLancaCredito = TRUE;
                                        $arCalculosNaoDesonerado[$inCountNaoDesonerado]['cod_calculo'] = $arCalculo[$i]['cod_calculo'];
                                        $arCalculosNaoDesonerado[$inCountNaoDesonerado]['cod_credito'] = $arCalculo[$i]['cod_credito'];
                                        if ($nuSomaPagamentos > 0) {
                                            $arCalculosNaoDesonerado[$inCountNaoDesonerado]['valor'] = $nuDifCalculo;
                                        } else {
                                            $arCalculo[$i]['valor'] = $arCalculosNaoDesonerado[$inCountNaoDesonerado]['valor'] = $nuDifCalculo;
                                        }
                                        $inCountNaoDesonerado++;
                                        $nuLancaCredito += $nuDifCalculo;
                                    }
                                    $rsCalculoLancAnterior->proximo();
                                }
                                $nuDifPag *= -1;

                                //insere novo lancamento
                                $obErro = $this->obTARRLancamento->proximoCod( $this->inCodLancamento, $boTransacao );
                                if ($boDesoneracao AND $boLancaCredito == FALSE) {
                                    $this->obTARRLancamento->setDado( "valor" , 0 );
                                    $boAtivo = FALSE;
                                } else {
                                    $this->obTARRLancamento->setDado( "valor" , $nuLancaCredito );
                                }

                                Sessao::write( "lancamentos_cods", Sessao::read ( "lancamentos_cods" ).$this->inCodLancamento."," );
                                $this->obTARRLancamento->setDado( "cod_lancamento" , $this->inCodLancamento );

                                $arParcelas = Sessao::read("parcelas");
                                if ( $arParcelas[0] )
                                    $dtTmpVencimento =  $arParcelas[0]['data_vencimento'];
                                else
                                    $dtTmpVencimento =  $arParcelas[1]['data_vencimento'];

                                $this->obTARRLancamento->setDado( "vencimento"     , $dtTmpVencimento          );
                                $this->obTARRLancamento->setDado( "total_parcelas" , $inC                      );
                                $this->obTARRLancamento->setDado( "ativo"          , $boAtivo                  );

                                $stObsInt = Sessao::read( 'stObsInt' ) ? Sessao::read( 'stObsInt' ) : $this->getObservacaoSistema();
                                $this->obTARRLancamento->setDado( "observacao_sistema", $stObsInt );
                                $stObs = Sessao::read( 'stObs' ) ? Sessao::read( 'stObs' ) : $this->getObservacao();
                                $this->obTARRLancamento->setDado( "observacao"     , $stObs                );

                                if ($boDesoneracao) {
                                    $obErro = $this->obTARRLancamento->inclusao( $boTransacao );
                                    require_once (CAM_GT_ARR_NEGOCIO."RARRDesoneracao.class.php");
                                    $obDesoneracao = new RARRDesoneracao;

                                    $obDesoneracao->setCodigo         ( 1 );
                                    if ( !$obErro->ocorreu() ) {
                                        //pegar cgm do calculo_cgm
                                        include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCalculoCgm.class.php" );
                                        $obTARRCalculoCgm = new TARRCalculoCgm;
                                        $stFiltro = " WHERE cod_calculo = ".$rsCalculos->getCampo( 'cod_calculo' );
                                        $obTARRCalculoCgm->recuperaTodos( $rsListCGM, $stFiltro, "", $boTransacao );

                                        //inserir cgm do calculo_cgm
                                        $obDesoneracao->obRCGM->setNumCGM ( $rsListCGM->getCampo('numcgm')  );
                                    }

                                    if ($boLancamentoEconomico) {
                                        $obDesoneracao->setInscricaoEconomica( $this->obRCEMInscricaoEconomica->getInscricaoEconomica() );
                                    } else {
                                        $obDesoneracao->setInscricaoImovel( $this->obRCIMImovel->getNumeroInscricao() );
                                    }

                                    $obDesoneracao->obRCadastroDinamico->addAtributosDinamicos( 1 , $nuDifPag );
                                    $obDesoneracao->setCodigoLancamento( $this->inCodLancamento );
                                } else {
                                    $nuDifPag = $nuSomaPagamentos - $nuValorCalculo;
                                    $nuValorCalculoADesc = 0;
                                    if ($nuDifPag < 0) {
                                        $nuDifPag *= -1;
                                    }

                                    $boLancPago = TRUE;
                                    if ($nuSomaPagUnicas > 0) {
                                        $obErro = $this->obTARRLancamento->inclusao( $boTransacao );
                                        $inCodLancamentoAtual = $this->getCodLancamento();
                                        $this->setCodLancamento( $inCodLancamentoAnterior );

                                        if ($boLancamentoEconomico) {
                                            $this->listarCalculosPorCredito( $rsCalculoLancAnterior, $boTransacao );
                                        } else {
                                            $this->listarCalculosCredito( $rsCalculoLancAnterior, $boTransacao );
                                        }

                                        $this->setCodLancamento( $inCodLancamentoAtual );
                                        $inCount = count($arCalculo);
                                        $nuValorCalculo = $nuDifPag;
                                    } else {
                                        $rsCalculoLancAnterior->setPrimeiroElemento();
                                        $inCount = count($arCalculo);
                                        for ($i = 0; $i < $inCount; $i++) {
                                            $inPercentCalc = $rsCalculoLancAnterior->getCampo('valor') / $nuValorLancAnterior;
                                            //$inPercentCalc = $arCalculo[$i]['valor'] / $nuValorCalculo;
                                            $inValorPago = round( $nuSomaPagamentos * $inPercentCalc, 2 );
                                            $inValorCalc = $arCalculo[$i]['valor'] - $inValorPago;
                                            $arCalculo[$i]['valor'] = $inValorCalc;
                                            $nuValorCalculoADesc += $inValorCalc;
                                            $rsCalculoLancAnterior->proximo();
                                        }
                                        $this->obTARRLancamento->setDado( "valor" , $nuDifPag );
                                        $obErro = $this->obTARRLancamento->inclusao( $boTransacao );

                                    }
                                    $nuValorCalculo = $nuDifPag;
                                }
                            }
                            /*
                                FIM DESONERACAO
                            */
                        } else {
                            //insere novo lancamento
                            $obErro = $this->obTARRLancamento->proximoCod( $this->inCodLancamento, $boTransacao );

                            $this->obTARRLancamento->setDado( "valor" , $nuValorCalculo );
                            Sessao::write( "lancamentos_cods", Sessao::read( "lancamentos_cods" ).$this->inCodLancamento."," );
                            $this->obTARRLancamento->setDado( "cod_lancamento" , $this->inCodLancamento       );
                            $arParcelas = Sessao::read( "parcelas" );
                            if ( $arParcelas[0] )
                                $dtTmpVencimento =  $arParcelas[0]['data_vencimento'];
                            else
                                $dtTmpVencimento =  $arParcelas[1]['data_vencimento'];

                            $contP = $inC = 0;
                            while ( $contP < count($arParcelas) ) {
                                if ($arParcelas[$contP]['stTipoParcela'] != "Única") {
                                    $inC++;
                                }
                                $contP++;
                            }

                            $this->obTARRLancamento->setDado( "vencimento"     , $dtTmpVencimento             );
                            $this->obTARRLancamento->setDado( "total_parcelas" , $inC                         );
                            $this->obTARRLancamento->setDado( "ativo"          , true                         );

                            $stObsInt = Sessao::read( 'stObsInt' ) ? Sessao::read( 'stObsInt' ) : $this->getObservacaoSistema();
                            $this->obTARRLancamento->setDado( "observacao_sistema", $stObsInt );
                            $stObs = Sessao::read( 'stObs' ) ? Sessao::read( 'stObs' ) : $this->getObservacao();
                            $this->obTARRLancamento->setDado( "observacao"     , $stObs                                             );
                            $obErro = $this->obTARRLancamento->inclusao( $boTransacao );
                        }

                        if ( !$obErro->ocorreu() ) { // chave 11

                            // como nao tem pagamentos,
                            // qualquer calculo  a menor cancela os antigos
                            // cancelar parcelas do lancamento anterior
                            if ($inCodLancamentoAnterior) {

                                $stFiltro = "\n and l.cod_lancamento= ".$inCodLancamentoAnterior;
                                $obErro =  $this->obTARRLancamento->recuperaParcelasPorLancamentoNPagos($rsParc,$stFiltro,'',$boTransacao);
                                $stFiltro = '';
                                include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCarneDevolucao.class.php"   );
                                while ( !$rsParc->eof() ) {
                                    $obTARRParcelaDelete = new TARRCarneDevolucao;;
                                    $obTARRParcelaDelete->setDado('numeracao'       , $rsParc->getCampo('numeracao')    );
                                    $obTARRParcelaDelete->setDado('cod_convenio'    , $rsParc->getCampo('cod_convenio') );
                                    $obTARRParcelaDelete->setDado('dt_devolucao'    , date('d/m/Y')                     );
                                    $obTARRParcelaDelete->setDado('cod_motivo'      , '109'                              );
                                    $obErro = $obTARRParcelaDelete->inclusao( $boTransacao );
                                    if ( $obErro->ocorreu() )break;
                                    $rsParc->proximo();
                                }
                            }

                            //insere tabela lancamento processo
                            if ( !$obErro->ocorreu() && Sessao::read( 'inProcesso' ) && Sessao::read( 'inExercicio' ) ) {
                                $this->obRProcesso->setCodigoProcesso ( Sessao::read( 'inProcesso' ) );
                                $this->obRProcesso->setExercicio      ( Sessao::read( 'inExercicio' ) );
                                $this->obTARRLancamentoProcesso->setDado( "cod_lancamento" , $this->inCodLancamento     );
                                $this->obTARRLancamentoProcesso->setDado( "cod_processo", $this->obRProcesso->getCodigoProcesso() );
                                $this->obTARRLancamentoProcesso->setDado( "ano_exercicio",  $this->obRProcesso->getExercicio() );
                                $obErro = $this->obTARRLancamentoProcesso->inclusao( $boTransacao );
                            }

                            if ( !$obErro->ocorreu() ) { // chave 15
                                $this->setValor        ( $nuValorCalculo      );
                                $this->setValorADesc   ( $nuValorCalculoADesc ); // seta valor a ser aplicado o desconto
                                $this->setTotalParcelas( $inC                 );

                                 //faz a verificação de suspensão
                                //se existe suspensão cadastrada para esta inscricao municipal soma com o valor do calculo
                                //se o valor minimo for atingido a suspensão é suspensa e o valor lançado
                                include_once ( CAM_GT_ARR_MAPEAMENTO."FARRVerificaSuspensao.class.php"   );
                                $this->obFARRVerificaSuspensao = new FARRVerificaSuspensao;
                                if ($boLancamentoEconomico) {
                                    $obErro = $this->obFARRVerificaSuspensao->recuperaSuspensao( $rsSuspensoes, $rsCalculos->getCampo("inscricao_economica"), $boTransacao );
                                } else {
                                    $this->obFARRVerificaSuspensao->setDado( 'stFiltro', $rsCalculos->getCampo("inscricao_municipal") );
                                    $obErro = $this->obFARRVerificaSuspensao->recuperaTodos( $rsSuspensoes, $stFiltro, $stOrder, $boTransacao );
                                }

                                if ( !$obErro->ocorreu() ) { // chave 16
                                    $nuValorSuspenso = 0;
                                    $boSuspensoes = FALSE;
                                    while ( !$rsSuspensoes->eof() ) {
                                        $nuValorSuspenso += $rsSuspensoes->getCampo('valor');
                                        $rsSuspensoes->proximo();
                                        $boSuspensoes = TRUE;
                                    }
                                    $rsSuspensoes->setPrimeiroElemento();
                                    $nuValorCalculo += $nuValorSuspenso;

                                    $boAtivo      = TRUE;
                                    $stObservacao = "";

                                    //faz a validacao do valor minimo do lancamento
                                    if ($nuValorCalculo < $arCalendario['valor_minimo_lancamento']) {
                                        $boAtivo      = FALSE;
                                        $stObservacao = "Valor mínimo do lançamento(".$arCalendario['valor_minimo_lancamento'].") não foi atingido.(".$nuValorCalculo.")!";

                                        //cadastra suspensao para o lancamento
                                        $this->obRARRSuspensao->setCodLancamento                 ( $this->inCodLancamento );
                                        $this->obRARRSuspensao->obRARRTipoSuspensao->setCodigoTipoSuspensao( 5                      );
                                        $this->obRARRSuspensao->setInicio                                  ( date( "d/m/Y" )        );
                                        $this->obRARRSuspensao->setObservacao                        ( $stObservacao          );

                                        $obErro = $this->obRARRSuspensao->suspenderCredito  ( $boTransacao );
                                    }

                                    /*
                                        PARCELAS
                                    */

                                    if ($boLancamentoEconomico) {
                                        $arDadosTMP = $rsValorCalculo->getElementos();
                                        for ($inLacoX=0; $inLacoX < count( $arDadosTMP); $inLacoX++) {
                                            $arDadosTMP[$inLacoX]["desconto"] = 't';
                                        }

                                        $rsCreditos = new RecordSet;
                                        $rsCreditos->preenche( $arDadosTMP );
                                        $rsCreditos->setPrimeiroElemento();
                                    } else {
                                        $rsCreditosGrupo->setPrimeiroElemento();
                                        $rsCreditos = $rsCreditosGrupo;
                                    }

                                    if ( !$obErro->ocorreu() AND ( !$boDesoneracao OR $boLancaCredito ) ) {

                                        if ($boLancaCredito) {
                                            $this->setValor( $nuLancaCredito );
                                        } elseif ($nuSomaPagamentos > 0) {
                                            $this->setValor( $nuValorCalculo );
                                        }
                                        if ($this->getTotalParcelas() > 0 ) {
                                            $nuValorParcelaTMP = number_format ( ( $this->getValor()   / $this->getTotalParcelas() )   , 2 );
                                        }
                                        $nuValorParcelaTMP = str_replace (',','', $nuValorParcelaTMP );

                                        //-------------CALCULA VALOR DA PRIMEIRA PARCELA
                                        if ( ($nuValorParcelaTMP * $this->getTotalParcelas()) != $this->getValor() ) {
                                            $nuValorPrimeiraParcela = $nuValorParcelaTMP + ($this->getValor() - ( $nuValorParcelaTMP *  $this->getTotalParcelas() ));
                                            $nuValorPrimeiraParcela = number_format ( $nuValorPrimeiraParcela, 2 );
                                        } else {
                                            $nuValorPrimeiraParcela = $nuValorParcelaTMP;
                                        }
                                        //-------------CALCULA VALOR DA PRIMEIRA PARCELA
                                        if ( $this->roRARRCalculo->getTipoCalculo() == 1 )
                                            $cont = 1;
                                        else
                                            $cont = 0;
                                        $aux = $cont;
                                        $contParcelaNormal = 1;
                                        $arrParcelasNovas = array();
                                        $boFlagPrimeiraParcela = true;
                                        $arParcelas = Sessao::read( "parcelas" );
                                        while ( $cont < ( count ( $arParcelas ) + $aux ) ) {
                                            $dtDataVencimento = $arParcelas[$cont]['data_vencimento'];
                                            if ($arParcelas[$cont]['stTipoParcela'] == 'Única') {
                                                $numeroParcela = 0;
                                                $nuValorParcela = $this->getValor();
                                            } else {
                                                if ($boFlagPrimeiraParcela) {
                                                    $nuValorParcela = str_replace(',','',$nuValorPrimeiraParcela);
                                                    $boFlagPrimeiraParcela = false;
                                                } else {
                                                    $nuValorParcela = $nuValorParcelaTMP;
                                                }
                                                $numeroParcela = $arParcelas[$cont]['stTipoParcela'];
                                            }

                                            $obErro = $this->obTARRParcela->proximoCod( $this->inCodParcela, $boTransacao );
                                            $arrParcelasNovas[$cont] = $this->inCodParcela;
                                            $this->obTARRParcela->setDado( "cod_parcela"    , $this->inCodParcela            );
                                            $this->obTARRParcela->setDado( "cod_lancamento" , $this->inCodLancamento );
                                            $this->obTARRParcela->setDado( "nr_parcela" , $numeroParcela                        );
                                            $this->obTARRParcela->setDado( "vencimento" , $dtDataVencimento                 );
                                            $this->obTARRParcela->setDado( "valor"      , $nuValorParcela                            );
                                            $obErro = $this->obTARRParcela->inclusao( $boTransacao );
                                            if ( !$obErro->ocorreu() ) { // chave 17
                                                //INSERCAO NA TABELA DESCONTO #####################
                                                if ($arParcelas[$cont]['valor'] > 0.00) {

                                                    $nuValorDescontoTMP = $arParcelas[$cont]['valor'];

                                                    if ($arParcelas[$cont]['stTipoParcela'] == 'Única') {
                                                        $rsCreditos->setPrimeiroElemento();
                                                        $somaValoresDescontados = 0.00;
                                                        $inCount = count( $arCalculo );
                                                        while ( !$rsCreditos->eof() ) {
                                                            if ( $rsCreditos->getCampo ('desconto') != 'f' ) {
                                                                if ($boDesoneracao && $boLancaCredito) {
                                                                    foreach ($arCalculosNaoDesonerado as $key => $valor) {
                                                                        if ( $valor['cod_credito'] == $rsCreditos->getCampo('cod_credito') ) {
                                                                            $somaValoresDescontados += $valor['valor'];
                                                                            break;
                                                                        }
                                                                    }
                                                                } else {
                                                                    for ($i=0; $i<$inCount; $i++) {
                                                                            $somaValoresDescontados += $arCalculo[$i]['valor'];
                                                                    }
                                                                }
                                                            }
                                                            $rsCreditos->proximo();
                                                        }
                                                        $totalDescontos = 0.00;
                                                        $rsCreditos->setPrimeiroElemento();
                                                        $i = 0;
                                                        while ( !$rsCreditos->eof() ) {
                                                            if ($boDesoneracao && $boLancaCredito) {
                                                                foreach ($arCalculosNaoDesonerado as $key => $valor) {
                                                                    $valorCreditoTMP = 0;
                                                                    if ( $valor['cod_credito'] == $rsCreditos->getCampo('cod_credito') ) {
                                                                        $valorCreditoTMP = $valor['valor'];
                                                                        break;
                                                                    }
                                                                }
                                                            } else {
                                                                $valorCreditoTMP = $arCalculo[$i]['valor'];
                                                            }
                                                            if ( $rsCreditos->getCampo ('desconto') != 'f' ) {
                                                                if ($arParcelas[$cont]['stTipoDesconto'] == 'Percentual') {
                                                                    $valorDescontar = ( $valorCreditoTMP * $nuValorDescontoTMP / 100  );
                                                                } else {
                                                                    $valorDescontar=( ($valorCreditoTMP / $somaValoresDescontados) * $nuValorDescontoTMP );
                                                                }
                                                                $totalDescontos += $valorCreditoTMP - $valorDescontar;
                                                            } else {
                                                                $totalDescontos += $valorCreditoTMP;
                                                            }
                                                            $totalDescontos = str_replace ( ',', '', number_format ( $totalDescontos , 2 ));
                                                            $i++;
                                                            $rsCreditos->proximo();
                                                        }
                                                        $nuValorDesconto = number_format ( $totalDescontos , 2 );
                                                        $nuValorDesconto = str_replace ( ',', '', $nuValorDesconto );
                                                     } else {  //parcela normal
                                                        $rsCreditos->setPrimeiroElemento();
                                                        $somaValoresDescontados = 0.00;
                                                        $inCount = count( $arCalculo );
                                                        while ( !$rsCreditos->eof() ) {
                                                            if ( $rsCreditos->getCampo ('desconto') != 'f' ) {
                                                                for ($i=0; $i<$inCount; $i++) {
                                                                    if ( $arCalculo[$i]['cod_credito'] == $rsCreditos->getCampo('cod_credito') ) {
                                                                       $somaValoresDescontados += $arCalculo[$i]['valor'];
                                                                    }
                                                                }
                                                            }
                                                            $rsCreditos->proximo();
                                                        }
                                                        $totalDescontos = 0.00;
                                                        $rsCreditos->setPrimeiroElemento();
                                                        $i = 0;
                                                        while ( !$rsCreditos->eof() ) {
                                                            $valorCreditoTMP = $arCalculo[$i]['valor'];
                                                            $valorCreditoTMP = $valorCreditoTMP/$this->getTotalParcelas();
                                                            if ( $rsCreditos->getCampo ('desconto') != 'f' ) {
                                                                if ($arParcelas[$cont]['stTipoDesconto'] == 'Percentual') {
                                                                    $valorDescontar = ( $valorCreditoTMP*$nuValorDescontoTMP/100  );
                                                                } else {
                                                                    $valorDescontar=(($valorCreditoTMP / $somaValoresDescontados)*$nuValorDescontoTMP);
                                                                }
                                                                $totalDescontos += $valorCreditoTMP - $valorDescontar;
                                                            } else {
                                                                $totalDescontos += $valorCreditoTMP;
                                                            }
                                                            $totalDescontos = str_replace ( ',', '', number_format ( $totalDescontos , 2 ));
                                                            $i++;
                                                            $rsCreditos->proximo();
                                                        }
                                                        $nuValorDesconto = number_format ( $totalDescontos , 2 );
                                                        $nuValorDesconto = str_replace ( ',', '', $nuValorDesconto );
                                                    }
                                                    //fim descontos
                                                    $this->obTARRParcelaDesconto->setDado ("cod_parcela" , $this->inCodParcela );
                                                    $this->obTARRParcelaDesconto->setDado ("vencimento"  , $dtDataVencimento);
                                                    $this->obTARRParcelaDesconto->setDado ("valor"       , $nuValorDesconto );
                                                    $obErro = $this->obTARRParcelaDesconto->inclusao ( $boTransacao );
                                                }
                                            } // chave 17

                                            if ( !$obErro->ocorreu() ) {

                                                // INSERCAO CARNE

                                                if ( !$obErro->ocorreu() ) { // chave 18
                                                    if ($boLancamentoEconomico) {
                                                        $this->roRARRCalculo->obRARRGrupo->obRMONCredito->listarCreditos( $rsListaCreditos, $boTransacao );
                                                        $inCodConvenio = $rsListaCreditos->getCampo("cod_convenio");
                                                    } else {
                                                        $rsCreditos->setPrimeiroElemento();
                                                        $this->roRARRCalculo->obRARRGrupo->obRMONCredito->setCodNatureza( $rsCreditos->getCampo("cod_natureza") );
                                                        $this->roRARRCalculo->obRARRGrupo->obRMONCredito->setCodCredito( $rsCreditos->getCampo("cod_credito") );
                                                        $this->roRARRCalculo->obRARRGrupo->obRMONCredito->setCodGenero( $rsCreditos->getCampo("cod_genero") );
                                                        $this->roRARRCalculo->obRARRGrupo->obRMONCredito->setCodEspecie( $rsCreditos->getCampo("cod_especie") );
                                                        $this->roRARRCalculo->obRARRGrupo->obRMONCredito->listarCreditos( $rsListaCreditos, $boTransacao );

                                                        $inCodConvenio = $rsListaCreditos->getCampo("cod_convenio");
                                                    }

                                                    $this->obRARRCarne->setExercicio( $this->roRARRCalculo->getExercicio() ); #$rsCreditosGrupo->getCampo('ano_exercicio') );
                                                    $this->obRARRCarne->obRMONConvenio->setCodigoConvenio( $inCodConvenio );
                                                    $this->obRARRCarne->obRARRParcela->setCodParcela( $arrParcelasNovas[$cont] );
                                                    $obErro = $this->obRARRCarne->obRMONConvenio->listarConvenioBanco( $rsConvenioBanco, $boTransacao );
                                                    if ( !$obErro->ocorreu() ) { // chave 19
                                                        $this->obRARRCarne->obRMONConvenio->obRFuncao->setCodFuncao ($rsConvenioBanco->getCampo("cod_funcao"));
                                                        $this->obRARRCarne->obRMONConvenio->obRFuncao->obRBiblioteca->roRModulo->setCodModulo( ($rsConvenioBanco->getCampo("cod_modulo") ));
                                                        $this->obRARRCarne->obRMONConvenio->obRFuncao->obRBiblioteca->setCodigoBiblioteca( ($rsConvenioBanco->getCampo("cod_biblioteca") ));
                                                        $obErro = $this->obRARRCarne->obRMONConvenio->obRFuncao->consultar( $boTransacao );
                                                        if ( !$obErro->ocorreu() ) { // chave 20
                                                            $stFNumeracao = "F".$this->obRARRCarne->obRMONConvenio->obRFuncao->getNomeFuncao();
                                                            $stFNumeracaoMap = "../../classes/funcao/".$stFNumeracao.".class.php";
                                                            include_once ( $stFNumeracaoMap );
                                                            $obFNumeracao = new $stFNumeracao;
                                                            if ( !$rsConvenioBanco->getCampo("cod_convenio")) {
                                                                $obErro->setDescricao('Convênio não cadastrado para Emissao do Crédito/Grupo de Créditos');
                                                            }
                                                            $stParametros = "'".$rsConvenioBanco->getCampo( "cod_carteira" )."','".$rsConvenioBanco->getCampo( "cod_convenio" )."'";
                                                            if( !$obErro->ocorreu() )
                                                                $obErro = $obFNumeracao->executaFuncao($rsRetorno,$stParametros,$boTransacao);
                                                            if ( !$obErro->ocorreu() ) { // chave 21
                                                                $inNumeracao = $rsRetorno->getCampo( "valor" );
                                                                $this->obRARRCarne->setNumeracao( $inNumeracao );

                                                                $obErro = $this->obRARRCarne->incluirCarne( $boTransacao );
                                                            } // chave 21
                                                        } //chave 20
                                                    } // chave 19
                                                } // chave 18
                                            }
                                            $cont++;
                                        }
                                    } // chave 17
                                } // chave 16
                            } //chave 13
                        } // chave 11
                    } // chave 10
                }
                $stIMAnterior = $rsCalculos->getCampo($stTipoInscricao);
                $inFirstLoop  = FALSE;

                //insere relacao lancamento/calculo
                if ( !$obErro->ocorreu() ) { // chave 22
                    $this->obTARRLancamentoCalculo->setDado( "cod_lancamento" , $this->inCodLancamento               );
                    $this->obTARRLancamentoCalculo->setDado( "cod_calculo"    , $rsCalculos->getCampo("cod_calculo") );

                    if ($boLancPago == TRUE) {
                        $this->obTARRLancamentoCalculo->setDado( "valor"  , $arCalculo[$inCountCalc]['valor'] );
                    } elseif ($boDesoneracao) {
                        foreach ($arCalculosDesoneracao as $key => $valor) {
                            if ( $valor['cod_calculo'] == $rsCalculos->getCampo("cod_calculo") ) {
                                $this->obTARRLancamentoCalculo->setDado( "valor" , 0 );
                                break;
                            }
                        }
                        if ($boLancaCredito) {
                            foreach ($arCalculosNaoDesonerado as $key => $valor) {
                                if ( $valor['cod_calculo'] == $rsCalculos->getCampo("cod_calculo") ) {
                                    $this->obTARRLancamentoCalculo->setDado( "valor" , $valor['valor'] );
                                    break;
                                }
                            }
                        }
                    } else {
                        $this->obTARRLancamentoCalculo->setDado( "valor"  , $rsCalculos->getCampo('valor'));
                    }
                    $obErro = $this->obTARRLancamentoCalculo->inclusao( $boTransacao );
                    $inCountCalc++;

                    if ( !$obErro->ocorreu() && $boUsaDesoneracao ) {
                        $inNumDes = count( $arDesoneracoes ) -1 ;
                        for ($i = 0 ; $i <= $inNumDes ; $i++) {
                            $this->obTARRLancamentoUsaDesoneracao->setDado ( 'cod_lancamento' , $this->inCodLancamento );
                            $this->obTARRLancamentoUsaDesoneracao->setDado ( 'cod_calculo' , $rsCalculos->getCampo( 'cod_calculo' ) );
                            $this->obTARRLancamentoUsaDesoneracao->setDado ( 'cod_desoneracao' , $arDesoneracoes[ $i ][ 'cod_desoneracao' ] ) ;
                            $this->obTARRLancamentoUsaDesoneracao->setDado ( 'numcgm' , $arDesoneracoes[ $i ][ 'numcgm' ] );
                            $this->obTARRLancamentoUsaDesoneracao->setDado ( 'ocorrencia' , $arDesoneracoes[ $i ][ 'ocorrencia' ] );
                            $obErro = $this->obTARRLancamentoUsaDesoneracao->inclusao ( $boTransacao ) ;
                        }
                    }
                } // chave 22
                $rsCalculos->proximo();
                $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRLancamento );
            }
            if ($boDesoneracao || $boDesoneracaodeUso) {
                $obErro = $obDesoneracao->concederDesoneracao( $boTransacao, $arCalculosDesoneracao );
            }
        } // chave mestre 1
    }

    return $obErro;
}

/*******************************************************************************************************************************************
/********************************************************************************************************************************************/

/********************************************************************************************* */
function efetuarLancamentoParcialIndividualCalculo($boTransacao = "" , $arCalculo = "")
{
    $obErro = new Erro ;

    set_time_limit (0);

    $boCalculoGrupo = $boCalculoCredito = false;
    $rsCreditosGrupo = new RecordSet;
    if ( !$obErro->ocorreu() ) { // chave mestre 1
        //busca creditos do Grupo de Credito selecionado
        if ( $this->roRARRCalculo->obRARRGrupo->getCodGrupo() ) {
            $boCalculoGrupo = true;
            $this->roRARRCalculo->obRARRGrupo->listarCreditos( $rsCreditosGrupo, $boTransacao );
            $this->obRARRCarne->setExercicio( $rsCreditosGrupo->getCampo('ano_exercicio') );
            $inCodConvenio = $rsCreditosGrupo->getCampo("cod_convenio");
        } else {
            $boCalculoCredito = true;
            $this->obRARRCarne->setExercicio( Sessao::getExercicio() );
            $arCredito = explode ('.', $this->roRARRCalculo->getChaveCredito());

            $this->roRARRCalculo->obRMONCredito->setCodCredito  ( $arCredito[0] );
            $this->roRARRCalculo->obRMONCredito->setCodEspecie  ( $arCredito[1] );
            $this->roRARRCalculo->obRMONCredito->setCodGenero   ( $arCredito[2] );
            $this->roRARRCalculo->obRMONCredito->setCodNatureza ( $arCredito[3] );
            $this->roRARRCalculo->obRMONCredito->listarCreditos ( $rsCredito, $boTransacao );

            $inCodConvenio = $rsCredito->getCampo('cod_convenio');

        }

        if ( Sessao::read('arquivo_calculos_lancamentos') ) {
            $nome_arquivo = Sessao::read('arquivo_calculos_lancamentos');
            if ( $arquivo = fopen ( $nome_arquivo, 'r' ) ) {
                $arCalculo = array ();
                while (!feof($arquivo)) {
                    if ($stLinha = fgets($arquivo)) {
                        $arLinha = explode ('&', $stLinha);
                        //echo '<br>'.$stLinha;
                        $arCalculo [] = array (
                            "cod_calculo" => $arLinha[0],
                            "valor" => $arLinha[1],
                            "inscricao_municipal" => $arLinha[2]
                        );
                    }
                }
            } else {
                $obErro->setDescricao ("Não foi possível abrir o arquivo com os calculos para lançamento.");
            }
        }

        if ( is_array( $arCalculo ) ) {
            $rsCalculos = new RecordSet;
            $rsCalculos->preenche( $arCalculo );
        } else {
            // Vamos buscar todos os calculos do grupo de credito
            $this->roRARRCalculo->listarCalculosGrupo( $rsCalculos, $boTransacao );
        }

        //recupera descontos do grupo de credito
        $boUsaCalendarioFiscal = false;

        if ( Sessao::read( 'TipoLancamento' ) == "Parcial" || Sessao::read( 'TipoLancamento' ) == "ParcialUsa"  || Sessao::read( 'TipoLancamento' ) == "Geral" ) {
            if ( !Sessao::read( 'parcelas' ) )
                $boUsaCalendarioFiscal = true;
        }

        if (!$boUsaCalendarioFiscal) {
            $rsParcelas = new RecordSet;
            $rsParcelas->preenche ( Sessao::read( 'parcelas' ) );
        } else {
            $boUsaCalendarioFiscal = true;

            //recupera informacoes do calendario fiscal
            $rsCreditosGrupo->setPrimeiroElemento();
            $this->roRARRCalculo->obRARRGrupoVencimento->roRARRCalendarioFiscal->setCodigoGrupo ( $rsCreditosGrupo->getCampo('cod_grupo') );
            $this->roRARRCalculo->obRARRGrupoVencimento->roRARRCalendarioFiscal->setAnoExercicio( $rsCreditosGrupo->getCampo('ano_exercicio') );
            $obErro = $this->roRARRCalculo->obRARRGrupoVencimento->roRARRCalendarioFiscal->listarCalendario( $rsCalendario, $boTransacao );

            if ( !$obErro->ocorreu() ) {
                $arCalendario['valor_minimo']            = $rsCalendario->getCampo('valor_minimo');
                $arCalendario['valor_minimo_lancamento'] = $rsCalendario->getCampo('valor_minimo_lancamento');
                $arCalendario['valor_minimo_parcela']    = $rsCalendario->getCampo('valor_minimo_parcela');
            }

            $obErro = $this->roRARRCalculo->obRARRGrupoVencimento->listarGrupoVencimento( $rsVencimento, $boTransacao);
            if ( $obErro->ocorreu() ) { $obErro->setDescricao ( 'ERRO NO LISTA GRUPO VENCIMENTO' ); }
            if ( $rsVencimento->getNumLinhas() < 1) {
                $obErro->setDescricao( "<b>Calendário Fiscal não definido</b> para este Grupo/Crédito!" );
            }
        }

        $inFirstLoop    = TRUE;
        $boLancPago     = FALSE;
        $nuValorCalculo = 0;
        $inCountCalc    = 0;
        if ( !is_object($this->obRARRDesoneracao) )
            $this->obRARRDesoneracao = new RARRDesoneracao;

        $rsCalculos->setPrimeiroElemento();
        $boAtivo = true;
        include_once ( CAM_GT_CIM_MAPEAMENTO."FCIMSituacaoImovel.class.php" );
        $obFCIMSituacaoImovel = new FCIMSituacaoImovel;
        include_once ( CAM_GT_ARR_MAPEAMENTO."TARRDesconto.class.php");
        $obTARRDesconto = new TARRDesconto;
        include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCarne.class.php");
        $obTARRCarne = new TARRCarne;

        $boFlagTransacao = false;
        unset($boTransacao);
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

        /** ============================================ */
        /** ============================================ */
        while ( !$rsCalculos->eof() ) { //para cada calculo
            $inInscricao = $rsCalculos->getCampo( 'inscricao_municipal' );
            if (!$inInscricao) {
                $rsCalculos->proximo();
            } else {

            /** ****************************************************** PARCELAS DO LANCAMENTO */
            $nuRetorno = $rsCalculos->getCampo('valor');
            if ($boUsaCalendarioFiscal) {
                $this->roRARRCalculo->obRARRGrupo->setExercicio ($rsCreditosGrupo->getCampo('ano_exercicio'));
                $this->roRARRCalculo->obRARRGrupo->setCodGrupo ( $rsCreditosGrupo->getCampo('cod_grupo'));
                $this->roRARRCalculo->setCodCalculo ( $rsCalculos->getCampo('cod_calculo') );
                $this->roRARRCalculo->buscaSomaCalculos( $rsSomaCalculos , $boTransacao );

                $nuValorSomadoCalculos = $rsSomaCalculos->getCampo('valor');
                $nuRetorno = $nuValorSomadoCalculos;
                while ( !$rsVencimento->eof() ) {

                    if ( ($nuRetorno > $rsVencimento->getCampo ('limite_inicial') && $nuRetorno < $rsVencimento->getCampo ('limite_final')) ) {
                        $this->roRARRCalculo->obRARRGrupoVencimento->setCodigoVencimento ( $rsVencimento->getCampo ('cod_vencimento') );
                        break;
                    }
                    $rsVencimento->proximo();
                }
                $rsVencimento->setPrimeiroElemento();
                $this->roRARRCalculo->obRARRGrupoVencimento->roRARRCalendarioFiscal->setCodigoGrupo( $rsCreditosGrupo->getCampo('cod_grupo') );
                $this->roRARRCalculo->obRARRGrupoVencimento->roRARRCalendarioFiscal->setAnoExercicio( $rsCreditosGrupo->getCampo('ano_exercicio') );
                //recupera o numero total de parcelas para o vencimento
                $obErro = $this->roRARRCalculo->obRARRGrupoVencimento->listarParcela($rsParcelas,$boTransacao);
                $rsParcelas->setPrimeiroElemento();
                $dtVencimentoLancamento = $rsParcelas->getCampo('data_vencimento');
                $arParcelas = $rsParcelas->arElementos;

                //verifica a parcela de cota unica

                $stFiltro = " where cod_grupo = ".$rsCreditosGrupo->getCampo ('cod_grupo');
                $stFiltro .=" and ano_exercicio = '".$rsCreditosGrupo->getCampo ('ano_exercicio')."'";
                $stOrdem = " \n ORDER BY data_vencimento ";
                $obTARRDesconto->recuperaTodos( $rsTDesconto, $stFiltro, $stOrdem, $boTransacao );
                $numParcelasUnicas = $rsTDesconto->getNumLinhas();
                $rsTDesconto->setPrimeiroElemento();
                $contParcelasNormais = count( $arParcelas );

                while ( !$rsTDesconto->eof() ) {

                    $arParcelas[$contParcelasNormais]['cod_grupo'] = $rsTDesconto->getCampo ('cod_grupo');
                    $arParcelas[$contParcelasNormais]['cod_vencimento'] = $rsTDesconto->getCampo ('cod_vencimento');
                    $arParcelas[$contParcelasNormais]['cod_parcela'] = 0;
                    $arParcelas[$contParcelasNormais]['data_vencimento'] = $rsTDesconto->getCampo('data_vencimento');
                    $arParcelas[$contParcelasNormais]['data_vencimento_desconto'] = $rsTDesconto->getCampo ('data_vencimento');

                    $arParcelas[$contParcelasNormais]['valor'] = $rsTDesconto->getCampo ('valor');
                    $arParcelas[$contParcelasNormais]['percentual'] = $rsTDesconto->getCampo ('percentual');

                    $arParcelas[$contParcelasNormais]['ano_exercicio'] = $rsTDesconto->getCampo ('ano_exercicio');

                    $rsTDesconto->proximo();
                    $contParcelasNormais++;
                }

                $rsParcelas->preenche($arParcelas);

            } else {
                $nuValorSomadoCalculos = $rsCalculos->getCampo('valor');
            }

            $inCountParcelas = 0;
            $inParcela       = 1;
            $arConfParcelas = array();
            $contP = 0;
            $rsParcelas->setPrimeiroElemento();

            $inTotalParcelasNormais = 0;
            while ( !$rsParcelas->eof() ) {

                if (!$boUsaCalendarioFiscal) {
                    if ( $rsParcelas->getCampo('stTipoParcela') == "Única" ) {
                        $inParcela = "0";
                        $stTipoParcela = "Única";
                    } else {
                        $inParcela = $rsParcelas->getCampo('stTipoParcela');

                        $stTipoParcela = "Normal";
                        $inTotalParcelasNormais++;
                    }
                } else {
                    if ( $rsParcelas->getCampo('cod_parcela') == "0" ) {
                        $inParcela = "0";
                        $stTipoParcela = "Única";
                    } else {
                        $inParcela = $rsParcelas->getCampo('cod_parcela');

                        $stTipoParcela = "Normal";
                        $inTotalParcelasNormais++;
                    }
                }

                if ( $rsParcelas->getCampo("data_vencimento") ) {
                    $dtVencimentoAtual = $rsParcelas->getCampo("data_vencimento");
                } else {
                    $dtVencimentoAtual = $rsParcelas->getCampo("dtVencimento");
                }
                if ( $contP == 0 ) $dtVencimentoLancamento = $dtVencimentoAtual;
                $contP++;

                $flValorDescontoAtual = 0.00;
                if ( $rsParcelas->getCampo('valor') > 0 ) {
                    $flValorDescontoAtual = $rsParcelas->getCampo('valor');
                } elseif ( $rsParcelas->getCampo('flDesconto') > 0 ) {
                    $flValorDescontoAtual = $rsParcelas->getCampo('flDesconto');
                }

                $dtVencimentoDescontoAtual = null;
                if ( $rsParcelas->getCampo('data_vencimento_desconto') ) {
                    $dtVencimentoDescontoAtual = $rsParcelas->getCampo('data_vencimento_desconto');
                } else {
                    $dtVencimentoDescontoAtual = $rsParcelas->getCampo('dtVencimento');
                }

                $stTipoDescontoAtual = null;
                if ( $rsParcelas->getCampo('percentual') ) {
                    if ( $rsParcelas->getCampo('percentual') == 't' )
                        $stTipoDescontoAtual = "Percentual";
                    else
                        $stTipoDescontoAtual = "Absoluto";
                } else {
                    $stTipoDescontoAtual = $rsParcelas->getCampo('stTipoDesconto');
                }

                $arConfParcelas[$inCountParcelas]['cod_parcela']      = $inParcela;
                $arConfParcelas[$inCountParcelas]['stTipoParcela']    = $stTipoParcela;
                $arConfParcelas[$inCountParcelas]['dtVencimento' ]    = $dtVencimentoAtual;
                if ($flValorDescontoAtual > 0) {
                    $arConfParcelas[$inCountParcelas]['flDesconto']       = $flValorDescontoAtual;
                    $arConfParcelas[$inCountParcelas]['stTipoDesconto']   = $stTipoDescontoAtual;
                    $arConfParcelas[$inCountParcelas]['data_vencimento_desconto']=$dtVencimentoDescontoAtual;
                } else {
                    $arConfParcelas[$inCountParcelas]['flDesconto']                 = 0;
                    $arConfParcelas[$inCountParcelas]['stTipoDesconto']            = '';
                    $arConfParcelas[$inCountParcelas]['data_vencimento_desconto'] = '';
                }

                $inParcela++;
                $inCountParcelas++;
                $rsParcelas->proximo();

            }
            $rsParcelas->setPrimeiroElemento();

            /***************************************************************************/

            $boUsaDesoneracao = FALSE;
            $nuValorCalculo = 0;
            $nuValorCalculoADesc = 0; // VALOR A QUAL SE APLICA O DESCONTO
            if ( ( $inFirstLoop == TRUE ) || ( $stIMAnterior != $inInscricao ) ) {
                //recupera valor total do calculo
                unset( $this->roRARRCalculo->inCodCalculo );
                $this->roRARRCalculo->obRCIMImovel->setNumeroInscricao( $inInscricao );
                $obErro = $this->roRARRCalculo->buscaValorCalculo( $rsValorCalculo , $boTransacao );
                $inValorPagar = 0;
                while ( !$rsValorCalculo->Eof() ) {
                    $inValorPagar += $rsValorCalculo->getCampo("valor");
                    $rsValorCalculo->proximo();
                }

                $rsValorCalculo->setPrimeiroElemento();

                $inTemp = 0;
                $inPrimeiro = 0;
                if ($inTotalParcelasNormais > 0) {
                    while ($inTemp < $arCalendario['valor_minimo_parcela']) {
                        if ( $inValorPagar <$arCalendario['valor_minimo_parcela'])
                            break;

                        $inTemp = $inValorPagar / $inTotalParcelasNormais;
                        if (($inPrimeiro == 1) && ($inTemp < $arCalendario['valor_minimo_parcela']) ) {
                            $inTotalParcelasNormais--;
                        }

                        $inPrimeiro = 1;
                    }
                }
                $inCountParcelas = 0;
                $inParcela       = 1;
                $arConfParcelas = array();

                $contP = 0;
                $rsParcelas->setPrimeiroElemento();

                while ( !$rsParcelas->eof() ) {

                    if (!$boUsaCalendarioFiscal) {
                        if ( $rsParcelas->getCampo('stTipoParcela') == "Única" ) {
                            $inParcela = "0";
                            $stTipoParcela = "Única";
                        } else {
                            $inParcela = $rsParcelas->getCampo('stTipoParcela');
                            $stTipoParcela = "Normal";
                            if ($inTotalParcelasNormais <= 0) {
                                $rsParcelas->proximo();
                                continue;
                            }

                            $inTotalParcelasNormais--;
                        }
                    } else {
                        if ( $rsParcelas->getCampo('cod_parcela') == "0" ) {
                            $inParcela = "0";
                            $stTipoParcela = "Única";
                        } else {
                            $inParcela = $rsParcelas->getCampo('cod_parcela');
                            $stTipoParcela = "Normal";
                            if ($inTotalParcelasNormais <= 0) {
                                $rsParcelas->proximo();
                                continue;
                            }

                            $inTotalParcelasNormais--;
                        }
                    }
                    if ( $rsParcelas->getCampo("data_vencimento") ) {
                        $dtVencimentoAtual = $rsParcelas->getCampo("data_vencimento");
                    } else {
                        $dtVencimentoAtual = $rsParcelas->getCampo("dtVencimento");
                    }
                    if ( $contP == 0 ) $dtVencimentoLancamento = $dtVencimentoAtual;
                    $contP++;

                    $flValorDescontoAtual = 0.00;
                    if ( $rsParcelas->getCampo('valor') > 0 ) {
                        $flValorDescontoAtual = $rsParcelas->getCampo('valor');
                    } elseif ( $rsParcelas->getCampo('flDesconto') > 0 ) {
                        $flValorDescontoAtual = $rsParcelas->getCampo('flDesconto');
                    }

                    $dtVencimentoDescontoAtual = null;
                    if ( $rsParcelas->getCampo('data_vencimento_desconto') ) {
                        $dtVencimentoDescontoAtual = $rsParcelas->getCampo('data_vencimento_desconto');
                    } else {
                        $dtVencimentoDescontoAtual = $rsParcelas->getCampo('dtVencimento');
                    }

                    $stTipoDescontoAtual = null;
                    if ( $rsParcelas->getCampo('percentual') ) {
                        if ( $rsParcelas->getCampo('percentual') == 't' )
                            $stTipoDescontoAtual = "Percentual";
                        else
                            $stTipoDescontoAtual = "Absoluto";
                    } else {
                        $stTipoDescontoAtual = $rsParcelas->getCampo('stTipoDesconto');
                    }

                    $arConfParcelas[$inCountParcelas]['cod_parcela']      = $inParcela;
                    $arConfParcelas[$inCountParcelas]['stTipoParcela']    = $stTipoParcela;
                    $arConfParcelas[$inCountParcelas]['dtVencimento' ]    = $dtVencimentoAtual;
                    if ($flValorDescontoAtual > 0) {
                        $arConfParcelas[$inCountParcelas]['flDesconto']       = $flValorDescontoAtual;
                        $arConfParcelas[$inCountParcelas]['stTipoDesconto']   = $stTipoDescontoAtual;
                        $arConfParcelas[$inCountParcelas]['data_vencimento_desconto']=$dtVencimentoDescontoAtual;
                    } else {
                        $arConfParcelas[$inCountParcelas]['flDesconto']                 = 0;
                        $arConfParcelas[$inCountParcelas]['stTipoDesconto']            = '';
                        $arConfParcelas[$inCountParcelas]['data_vencimento_desconto'] = '';
                    }

                    $inParcela++;
                    $inCountParcelas++;
                    $rsParcelas->proximo();

                }
                $rsParcelas->setPrimeiroElemento();

                #=================== VERIFICACAO DE DESONERACAO
                // busca valor do lancamento anterior
                $this->obRCIMImovel->setNumeroInscricao( $inInscricao );

                if ($boCalculoGrupo) {

                    $obErro = $this->buscaValorLancamentoAnteriorGrupo( $rsLancAnt , $boTransacao);

                    if ( !$obErro->ocorreu() && $rsLancAnt->getCampo("valor") ) {
                        $arLancamentoAnterior = explode( "-", $rsLancAnt->getCampo("valor") );
                        $nuVlrLancAnterior       = $arLancamentoAnterior[0];
                        $inCodLancamentoAnterior = $arLancamentoAnterior[1];
                        if ( !$obErro->ocorreu() ) {
                            $obErro = $this->buscaSomaParcelasPagasLancamento( $rsSoma , $inCodLancamentoAnterior, $boTransacao);
                            if ( !$obErro->ocorreu() ) {
                                $nuSomaPagamentos = $rsSoma->getCampo('valor');
                                $obErro = $this->buscaSomaParcelasUnicasPagasLancamento( $rsSomaU , $inCodLancamentoAnterior, $boTransacao);
                                if ( !$obErro->ocorreu() ) {
                                    $nuSomaPagUnicas  = $rsSomaU->getCampo('valor');
                                }
                            }
                        }
                    }
                }


                /*
                * Retorno valor do calculo com desoneração aplicada
                * */
                $nuDiffDesoneraCredito = 0;

                $nuValorCalculoCredito = $rsCalculos->getCampo( 'valor' );

                $arDesoneracoes = array();

                $this->usaDesoneracao(    $arDesoneracoes
                                        , $nuDiffDesoneraCredito
                                        , $nuValorCalculoCredito
                                        , $inCodLancamentoAnterior
                                        , $inInscricao
                                        , $rsCalculos->getCampo( 'cod_calculo' )
                                        , $boTransacao
                                    );
                if ( !is_object($obDesoneracao))
                    $obDesoneracao = new RARRDesoneracao;

                if ($nuDiffDesoneraCredito > 0) {
                    // prepara desoneração
                    $obDesoneracao->setCodigo         ( 1 );
                    $obDesoneracao->setInscricaoImovel( $this->obRCIMImovel->getNumeroInscricao() );
                    $obDesoneracao->setInscricaoImovel( $inInscricao );

                    //recupera um proprietario do imovel
                    if ( !$obErro->ocorreu() ) {
                        if ( !is_object( $obRCIMProprietario ))
                            $obRCIMProprietario  = new RCIMProprietario ( $this->obRCIMImovel );
                        $obRCIMProprietario->listarProprietariosPorImovel($rsProprietarios , $boTransacao );
                        $obDesoneracao->obRCGM->setNumCGM ( $rsProprietarios->getCampo('numcgm')  );
                    }

                    $obDesoneracao->obRCadastroDinamico->addAtributosDinamicos( 1 , $nuDiffDesoneraCredito);
                    $obDesoneracao->setCodigoLancamento( $this->inCodLancamento );
                    $boDesoneracaodeUso = TRUE;
                    $boUsaDesoneracao = TRUE;

                } else {
                    $boUsaDesoneracao = TRUE;
                }

                $rsCalculos->setCampo ( 'valor' , $nuValorCalculoCredito );

                if ( !$obErro->ocorreu() ) { // chave 10
                    $arCreditoValor = array();
                    $arTmp = $nuTmp1 = $nuTmp2 = 0;
                    while ( !$rsValorCalculo->eof() ) {
                        $nuTmp2 = $rsValorCalculo->getCampo( "valor" );

                        $this->usaDesoneracao(    $arTmp
                                                , $nuTmp1
                                                , $nuTmp2
                                                , $inCodLancamentoAnterior
                                                , $inInscricao
                                                , $rsValorCalculo->getCampo( 'cod_calculo' )
                                                , $boTransacao
                                            );
                        $rsValorCalculo->setCampo( 'valor' , $nuTmp2 ) ;

                        $nuValorCalculo += $rsValorCalculo->getCampo( "valor" );
                        if ( $rsValorCalculo->getCampo( "desconto") == 't' ) {
                            $nuValorCalculoADesc += $rsValorCalculo->getCampo( "valor" );
                        }
                        $arCreditoValor[$rsValorCalculo->getCampo('cod_credito')]['valor']    = $rsValorCalculo->getCampo( "valor" );
                        $arCreditoValor[$rsValorCalculo->getCampo('cod_credito')]['desconto'] = $rsValorCalculo->getCampo( "desconto" );
                        $rsValorCalculo->proximo();
                    }

                    $arContar = $arConfParcelas;
                    $inC = 0;
                    foreach ($arContar as $value) {
                        if ( $value['stTipoParcela'] <> 'Única' )
                            $inC++;
                    }

                }

                    //verifca se ja existem pagamentos no lancamento anterior
                    if ( ( $nuSomaPagUnicas > 0 ) || ( $nuSomaPagamentos > 0 ) ) {
                        /*
                            VERIFICA VALORES PARA CONCEDER DESONERACAO
                        */
                        if ( $this->obRCIMImovel->getNumeroInscricao() ) {
                            $inCodLancamentoAtual = $this->getCodLancamento();
                            $this->setCodLancamento( $inCodLancamentoAnterior );
                            $this->listarCalculosCredito( $rsCalculoLancAnterior, $boTransacao );
                            $this->setCodLancamento( $inCodLancamentoAtual );
                            $inCount = count($arCalculo);

                            $boLancaCredito       = FALSE;
                            $nuLancaCreido        = 0;          
                            $inCountDesoneracao   = 0;
                            $inCountNaoDesonerado = 0;

                            $rsCalculoLancAnterior->setPrimeiroElemento();
                            while ( !$rsCalculoLancAnterior->eof() ) {
                                $nuValorLancAnterior += $rsCalculoLancAnterior->getCampo('valor');
                                $rsCalculoLancAnterior->proximo();
                            }
                            $rsCalculoLancAnterior->setPrimeiroElemento();
                            for ($i = 0; $i < $inCount; $i++) {
                                if ($nuSomaPagamentos > 0) {
                                    $inPercentCalc = $rsCalculoLancAnterior->getCampo('valor') / $nuValorLancAnterior;
                                    $inValorPago = round( $nuSomaPagamentos * $inPercentCalc, 2 );
                                    $nuDifCalculo = ( $arCalculo[$i]['valor'] - $inValorPago );
                                } else {
                                    $nuDifCalculo = ( $arCalculo[$i]['valor'] - $rsCalculoLancAnterior->getCampo('valor_calculado') );
                                }
                                $nuDifCalculo = round( $nuDifCalculo , 2 );
                                if ( ( ( $nuDifCalculo ) < 0 )) {
                                    $arCalculosDesoneracao[$inCountDesoneracao]['cod_calculo'] = $arCalculo[$i]['cod_calculo'];
                                    $arCalculosDesoneracao[$inCountDesoneracao]['valor'] = $nuDifCalculo * -1;
                                    $boDesoneracao = TRUE;
                                    $arCalculo[$i]['valor'] = 0;
                                    $nuDifPag += $nuDifCalculo;
                                    $inCountDesoneracao++;
                                } else {
                                    $boLancaCredito = TRUE;
                                    $arCalculosNaoDesonerado[$inCountNaoDesonerado]['cod_calculo'] = $arCalculo[$i]['cod_calculo'];
                                    $arCalculosNaoDesonerado[$inCountNaoDesonerado]['cod_credito'] = $arCalculo[$i]['cod_credito'];
                                    if ($nuSomaPagamentos > 0) {
                                        $arCalculosNaoDesonerado[$inCountNaoDesonerado]['valor'] = $nuDifCalculo;
                                    } else {
                                        $arCalculo[$i]['valor'] = $arCalculosNaoDesonerado[$inCountNaoDesonerado]['valor'] = $nuDifCalculo;
                                    }
                                    $inCountNaoDesonerado++;
                                    $nuLancaCredito += $nuDifCalculo;
                                }
                                $rsCalculoLancAnterior->proximo();
                            }
                            $nuDifPag *= -1;

                            //insere novo lancamento
                            $obErro = $this->obTARRLancamento->proximoCod( $this->inCodLancamento, $boTransacao );
                            if ($boDesoneracao AND $boLancaCredito == FALSE) {
                                $this->obTARRLancamento->setDado( "valor" , 0.00 );
                                $boAtivo = FALSE;
                            } else {
                                $this->obTARRLancamento->setDado( "valor" , $nuLancaCredito );
                            }

                            Sessao::write( 'lancamentos_cods', Sessao::read( 'lancamentos_cods' ).$this->inCodLancamento."," );

                            $this->obTARRLancamento->setDado( "cod_lancamento" , $this->inCodLancamento   );
                            $this->obTARRLancamento->setDado( "vencimento"     , $dtVencimentoLancamento  );
                            $this->obTARRLancamento->setDado( "total_parcelas" , $inC               );
                            $this->obTARRLancamento->setDado( "ativo"          , $boAtivo           );

                            $stObsInt = Sessao::read( 'stObsInt' ) ? Sessao::read( 'stObsInt' ) : $this->getObservacaoSistema();
                            $this->obTARRLancamento->setDado( "observacao_sistema", $stObsInt );

                            $stObs = Sessao::read( 'stObs' ) ? Sessao::read( 'stObs' ) : $this->getObservacao();
                            $this->obTARRLancamento->setDado( "observacao"     , $stObs             );

                            if ($boDesoneracao) {
                                $obErro = $this->obTARRLancamento->inclusao( $boTransacao );

                                require_once (CAM_GT_ARR_NEGOCIO."RARRDesoneracao.class.php");
                                $obDesoneracao = new RARRDesoneracao;

                                $obDesoneracao->setCodigo         ( 1 );
                                $obDesoneracao->setInscricaoImovel( $this->obRCIMImovel->getNumeroInscricao());
                                $obDesoneracao->setInscricaoImovel( $inInscricao );
                                //recupera um proprietario do imovel
                                if ( !$obErro->ocorreu() ) {
                                    $obRCIMProprietario  = new RCIMProprietario ( $this->obRCIMImovel );
                                    $obRCIMProprietario->listarProprietariosPorImovel($rsProprietarios , $boTransacao );
                                    $obDesoneracao->obRCGM->setNumCGM ( $rsProprietarios->getCampo('numcgm'));
                                }
                                $obDesoneracao->obRCadastroDinamico->addAtributosDinamicos( 1 , $nuDifPag );
                                $obDesoneracao->setCodigoLancamento( $this->inCodLancamento );
                            } else {

                                $nuDifPag = $nuSomaPagamentos - $nuValorCalculo;
                                $nuValorCalculoADesc = 0;
                                if ($nuDifPag < 0) {
                                    $nuDifPag *= -1;
                                }

                                $boLancPago = TRUE;
                                if ($nuSomaPagUnicas > 0) {
                                    $obErro = $this->obTARRLancamento->inclusao( $boTransacao );

                                    $inCodLancamentoAtual = $this->getCodLancamento();
                                    $this->setCodLancamento( $inCodLancamentoAnterior );
                                    $this->listarCalculosCredito( $rsCalculoLancAnterior, $boTransacao );
                                    $this->setCodLancamento( $inCodLancamentoAtual );
                                    $inCount = count($arCalculo);
                                    $nuValorCalculo = $nuDifPag;

                                } else {
                                    $rsCalculoLancAnterior->setPrimeiroElemento();
                                    $inCount = count($arCalculo);
                                    for ($i = 0; $i < $inCount; $i++) {
                                        $inPercentCalc = $rsCalculoLancAnterior->getCampo('valor') / $nuValorLancAnterior;
                                        $inValorPago = round( $nuSomaPagamentos * $inPercentCalc, 2 );
                                        $inValorCalc = $arCalculo[$i]['valor'] - $inValorPago;
                                        $arCalculo[$i]['valor'] = $inValorCalc;
                                        $nuValorCalculoADesc += $inValorCalc;
                                        $rsCalculoLancAnterior->proximo();
                                    }
                                    $this->obTARRLancamento->setDado( "valor" , $nuDifPag );
                                    $obErro = $this->obTARRLancamento->inclusao( $boTransacao );
                                }
                                $nuValorCalculo = $nuDifPag;
                            }

                        }elseif ( $this->obRCEMInscricaoEconomica->getInscricaoEconomica() ) {
                            
                            } else {
                            
                            }
                

                    /*
                        FIM DESONERACAO
                    */
            } else {
                include_once ( CAM_GT_ARR_MAPEAMENTO."TARRDesonerado.class.php" );

                $rsCalculos2 = new RecordSet;
                $rsCalculos2->preenche( $rsCalculos->getElementos() );
                $rsCalculos2->setPrimeiroElemento();
                $nuValorCalculo = 0;
                $boUsaDesoneracaoFuncao = FALSE;
                $arDesoneracoesFuncao = array();
                while ( !$rsCalculos2->Eof() ) {
                    if ( $rsCalculos->getCampo("inscricao_municipal") != $rsCalculos2->getCampo("inscricao_municipal") ) {
                        $rsCalculos2->proximo();
                        continue;
                    }
                    $stSql = "select * from arrecadacao.calculo where cod_calculo = ".$rsCalculos2->getCampo("cod_calculo");
                    $obCon = new Conexao;
                    $obCon->executaSQL ( $rsCre , $stSql , $boTransacao );
                    if ( !$rsCre->Eof() ) {
                        $obTARRDesoneracao = new TARRDesonerado;
                        $obTARRDesoneracao->aplicaDesoneracao( $rsDesoneracao, $rsCre->getCampo ( 'cod_credito' ), $rsCre->getCampo ( 'cod_especie' ), $rsCre->getCampo ( 'cod_genero' ), $rsCre->getCampo ( 'cod_natureza' ), $rsCre->getCampo ( 'valor' ), date("Y-m-d"), $rsCalculos2->getCampo( 'inscricao_municipal' ), "null", $boTransacao);
                        if ( !$rsDesoneracao->Eof() ) {
                            if ( strlen( $rsDesoneracao->getCampo("valor") ) > 0 ) {
                                $arDesoneracao = explode( "§", $rsDesoneracao->getCampo("valor") );
                                $arDesoneracoesFuncao[] = array ( 'cod_desoneracao' => $arDesoneracao[1]
                                                        ,'numcgm' => $arDesoneracao[3]
                                                        ,'ocorrencia' => $arDesoneracao[2]
                                                        ,'cod_calculo' => $rsCalculos2->getCampo("cod_calculo")
                                                        ,'valor' => $arDesoneracao[0] );

                                $nuValorCalculo += $arDesoneracao[0];
                            } else {
                                $nuValorCalculo += $rsCre->getCampo ( 'valor' );
                            }
                        } else {
                            $nuValorCalculo += $rsCre->getCampo ( 'valor' );
                        }
                    }

                    $rsCalculos2->proximo();
                }

                    if ( count( $arDesoneracoesFuncao ) > 0 ) {
                        if ( $nuValorCalculo <= 0 ){
                            $inC = 0; //total de parcelas vira 0
                        }
                        $boUsaDesoneracaoFuncao = TRUE;
                        $boAtivo = FALSE;
                    }
                    //insere novo lancamento
                    $obErro = $this->obTARRLancamento->proximoCod( $this->inCodLancamento, $boTransacao );

                    $this->obTARRLancamento->setDado( "valor" , $nuValorCalculo );
                    Sessao::write( "lancamentos_cods", Sessao::read( "lancamentos_cods").$this->inCodLancamento."," );
                    $this->obTARRLancamento->setDado( "cod_lancamento" , $this->inCodLancamento   );
                    $this->obTARRLancamento->setDado( "vencimento"     , $dtVencimentoLancamento  );
                    $this->obTARRLancamento->setDado( "total_parcelas" , $inC                   );
                    $this->obTARRLancamento->setDado( "ativo"          , $boAtivo               );
                    $stObsInt = Sessao::read( "stObsInt") ? Sessao::read( "stObsInt") : $this->getObservacaoSistema();
                    $this->obTARRLancamento->setDado( "observacao_sistema", $stObsInt );
                    $stObs = Sessao::read( "stObs") ? Sessao::read( "stObs") : $this->getObservacao();
                    $this->obTARRLancamento->setDado( "observacao"     , $stObs                 );
                    $obErro = $this->obTARRLancamento->inclusao( $boTransacao );
                }

                    if ( !$obErro->ocorreu() ) { // chave 11
                        // como nao tem pagamentos,
                        // qualquer calculo  a menor cancela os antigos
                        // cancelar parcelas do lancamento anterior
                        if ($inCodLancamentoAnterior) { //novidade 24_07_07 quando ja existir um lancamento nao deve ser possiver executar outro lancamento
                            $obErro = new Erro ;

                            $obTARRLancamento = new TARRLancamento;
                            $stFilt = " AND COALESCE( imovel_calculo.inscricao_municipal, cadastro_economico_calculo.inscricao_economica, calculo_cgm.numcgm ) = ".$inInscricao;
                            if ( $this->roRARRCalculo->obRARRGrupo->getCodGrupo() ) {
                                $stFilt .= " AND calculo_grupo_credito.cod_grupo = ".$this->roRARRCalculo->obRARRGrupo->getCodGrupo();
                                $stFilt .= " AND calculo_grupo_credito.ano_exercicio = '".$this->roRARRCalculo->obRARRGrupo->getExercicio()."'";
                            } else {
                                $arCredito = explode ('.', $this->roRARRCalculo->getChaveCredito());
                                $stFilt .= " AND calculo.cod_credito = ".$arCredito[0];
                                $stFilt .= " AND calculo.cod_especie = ".$arCredito[1];
                                $stFilt .= " AND calculo.cod_genero = ".$arCredito[2];
                                $stFilt .= " AND calculo.cod_natureza = ".$arCredito[3];
                            }

                            $obTARRLancamento->VerificaLancamentos( $rsLancAnt, $stFilt, "", $boTransacao );
                            if ( !$rsLancAnt->Eof() ) {
                                $obErro->setDescricao('A inscrição selecionada já possui cálculos para o grupo de crédito/crédito selecionado no exercício de '.$this->roRARRCalculo->obRARRGrupo->getExercicio().'!');

                                return $obErro;
                            }
                        }elseif (( $inCodLancamentoAnterior ) && ($this->obRCIMImovel->getNumeroInscricao())) { //soh entra se for imobiliario novidade de 28_05_07
                            //$obTARRLancamento = new TARRLancamento;
                            $stFiltro = "\n and l.cod_lancamento= ".$inCodLancamentoAnterior;
                            $obErro =  $this->obTARRLancamento->recuperaParcelasPorLancamentoNPagos($rsParc,$stFiltro,'',$boTransacao);

                            $stFiltro = '';
                            include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCarneDevolucao.class.php"   );
                            while ( !$rsParc->eof() ) {
                                $obTARRParcelaDelete = new TARRCarneDevolucao;
                                $obTARRParcelaDelete->setDado('numeracao'     , $rsParc->getCampo('numeracao')  );
                                $obTARRParcelaDelete->setDado('cod_convenio'  , $rsParc->getCampo('cod_convenio'));
                                $obTARRParcelaDelete->setDado('dt_devolucao'  , date('d/m/Y')        );
                                $obTARRParcelaDelete->setDado('cod_motivo'    , '109'                );
                                $obErro = $obTARRParcelaDelete->inclusao( $boTransacao );
                                if ( $obErro->ocorreu() )break;
                                $rsParc->proximo();
                            }
                        }

                        //insere tabela lancamento processo
                        if ( !$obErro->ocorreu() && Sessao::read( 'inProcesso' ) && Sessao::read( 'inExercicio' ) ) {
                            $this->obRProcesso->setCodigoProcesso ( Sessao::read( 'inProcesso' ) );
                            $this->obRProcesso->setExercicio      ( Sessao::read( 'inExercicio' ) );
                            $this->obTARRLancamentoProcesso->setDado( "cod_lancamento" , $this->inCodLancamento );
                            $this->obTARRLancamentoProcesso->setDado( "cod_processo", $this->obRProcesso->getCodigoProcesso() );
                            $this->obTARRLancamentoProcesso->setDado( "ano_exercicio",  $this->obRProcesso->getExercicio() );
                            $obErro = $this->obTARRLancamentoProcesso->inclusao( $boTransacao );
                        }

                        if ( !$obErro->ocorreu() ) { // chave 15

                            $this->setValor        ( $nuValorCalculo      );
                            $this->setValorADesc   ( $nuValorCalculoADesc );//valor a ser aplicado o desconto
                            $this->setTotalParcelas( $inC                 );

                            //faz a verificação de suspensão
                            //se existe suspensão cadastrada para esta insc_municipal soma com o valor do calculo
                            //se o valor minimo for atingido a suspensão é suspensa e o valor lançado
                            $stFiltro = '';
                            $this->obFARRVerificaSuspensao->setDado( 'stFiltro', $inInscricao );

                            $obErro = $this->obFARRVerificaSuspensao->recuperaTodos( $rsSuspensoes, $stFiltro, $stOrder, $boTransacao );
                            if ( !$obErro->ocorreu() ) { // chave 16
                                $nuValorSuspenso = 0;
                                $boSuspensoes = FALSE;
                                while ( !$rsSuspensoes->eof() ) {
                                    $nuValorSuspenso += $rsSuspensoes->getCampo('valor');
                                    $rsSuspensoes->proximo();
                                    $boSuspensoes = TRUE;
                                }
                                $rsSuspensoes->setPrimeiroElemento();
                                $nuValorCalculo += $nuValorSuspenso;

                                $boAtivo      = TRUE;
                                $stObservacao = "";

                                //faz a validacao do valor minimo do lancamento
                                if ($nuValorCalculo < $arCalendario['valor_minimo_lancamento']) {
                                    $boAtivo      = FALSE;
                                    $stObservacao = "Valor mínimo do lançamento(".$arCalendario['valor_minimo_lancamento'].") não foi atingido.(".$nuValorCalculo.")!";

                                    //cadastra suspensao para o lancamento
                                    $this->obRARRSuspensao->setCodLancamento    ( $this->inCodLancamento    );
                                    $this->obRARRSuspensao->obRARRTipoSuspensao->setCodigoTipoSuspensao ( 5 );
                                    $this->obRARRSuspensao->setInicio           ( date( "d/m/Y" )           );
                                    $this->obRARRSuspensao->setObservacao       ( $stObservacao             );
                                    $obErro = $this->obRARRSuspensao->suspenderCredito  ( $boTransacao      );
                                }

                                //se possuir suspensoes anteriores, insere data de termino para as mesmas
                                if ( $boSuspensoes == TRUE AND $boAtivo == TRUE AND !$obErro->ocorreu() ) {
                                    while ( !$rsSuspensoes->eof() ) {
                                        $this->obRARRSuspensao->setCodLancamento( $rsSuspensoes->getCampo('cod_lancamento') );
                                        $this->obRARRSuspensao->setCodSuspensao ( $rsSuspensoes->getCampo('cod_suspensao')  );
                                        $this->obRARRSuspensao->setTermino      ( date( "d/m/Y" )   );
                                        $this->obRARRSuspensao->setObservacao   ( ''                );
                                        $rsSuspensoes->proximo();
                                        if ( $obErro->ocorreu() )
                                            break;
                                    }
                                }

                                /*
                                    PARCELAS
                                */
                                $rsCreditosGrupo->setPrimeiroElemento();
                                $rsCreditos = $rsCreditosGrupo;

                                if ( $boAtivo && !$obErro->ocorreu() && ( !$boDesoneracao OR $boLancaCredito ) ) {
                                    if ($boLancaCredito) {
                                        $this->setValor( $nuLancaCredito );
                                    } elseif ($nuSomaPagamentos > 0) {
                                        $this->setValor( $nuValorCalculo );
                                    }
                                    if ($this->getTotalParcelas() > 0 ) {
                                        $nuValorParcelaTMP = number_format ( ( $this->getValor()   / $this->getTotalParcelas() )   , 2 );
                                    }
                                    $nuValorParcelaTMP = str_replace (',','', $nuValorParcelaTMP );

                                        //-------------CALCULA VALOR DA PRIMEIRA PARCELA
                                        if ( ($nuValorParcelaTMP * $this->getTotalParcelas()) != $this->getValor() ) {
                                            $nuValorPrimeiraParcela = $nuValorParcelaTMP + ($this->getValor() - ( $nuValorParcelaTMP *  $this->getTotalParcelas() ));
                                            $nuValorPrimeiraParcela = number_format ( $nuValorPrimeiraParcela, 2 );
                                        } else {
                                            $nuValorPrimeiraParcela = $nuValorParcelaTMP;
                                        }
                                        if ( $boUsaCalendarioFiscal )
                                            $cont = 0;
                                        else
                                            $cont = 0;

                                        $aux = $cont;
                                        $contParcelaNormal = 1;
                                        $arrParcelasNovas = array();
                                        $boFlagPrimeiraParcela = true;
                                        $rsConfParcelas = new RecordSet;
                                        $rsConfParcelas->preenche($arConfParcelas);

                                        while ( $cont < ( count ( $arConfParcelas ) + $aux ) ) {
                                            $dtDataVencimento = $arConfParcelas[$cont]['dtVencimento'];
                                            if ($arConfParcelas[$cont]['stTipoParcela'] == "Única") {
                                                $numeroParcela = 0;
                                                $nuValorParcela = $this->getValor();
                                            } else {
                                                if ($boFlagPrimeiraParcela) {
                                                    $nuValorParcela = str_replace(',','',$nuValorPrimeiraParcela);
                                                    $boFlagPrimeiraParcela = false;
                                                } else {
                                                    $nuValorParcela = $nuValorParcelaTMP;
                                                }
                                                $numeroParcela = $arConfParcelas[$cont]['cod_parcela'];
                                            }

                                            $obErro = $this->obTARRParcela->proximoCod( $this->inCodParcela,$boTransacao );
                                            $arrParcelasNovas[$cont] = $this->inCodParcela;
                                            $this->obTARRParcela->setDado( "cod_parcela"  , $this->inCodParcela );
                                            $this->obTARRParcela->setDado( "cod_lancamento",$this->inCodLancamento );
                                            $this->obTARRParcela->setDado( "nr_parcela" , $numeroParcela      );
                                            $this->obTARRParcela->setDado( "vencimento" , $dtDataVencimento   );
                                            $this->obTARRParcela->setDado( "valor"      , $nuValorParcela     );
                                            $obErro = $this->obTARRParcela->inclusao( $boTransacao );

                                            if ( !$obErro->ocorreu() ) { // chave 17

                                                //INSERCAO NA TABELA DESCONTO #####################
                                                if ($arConfParcelas[$cont]['flDesconto'] > 0.00) {

                                                    $nuValorDescontoTMP = $arConfParcelas[$cont]['flDesconto'];
                                                    if ($arConfParcelas[$cont]['stTipoParcela'] == "Única") {
                                                        $rsCreditos->setPrimeiroElemento();
                                                        $somaValoresDescontados = 0.00;
                                                        $inCount = count( $arCalculo );
                                                        $contTemp = 0;
                                                        $contCalculoTEMP = 0;
                                                        $arCalculoTEMP = array();
                                                        while ($contTemp < $inCount) {
                                                            if ($arCalculo[$contTemp]['inscricao_municipal'] == $inInscricao) {
                                                                $arCalculoTEMP[$contCalculoTEMP]['cod_calculo'] = $arCalculo[$contTemp]['cod_calculo'];
                                                                $arCalculoTEMP[$contCalculoTEMP]['valor'] = $arCalculo[$contTemp]['valor'];
                                                                $arCalculoTEMP[$contCalculoTEMP]['inscricao_municipal'] = $arCalculo[$contTemp]['inscricao_municipal'];
                                                                $contCalculoTEMP++;
                                                            }

                                                            $contTemp++;
                                                        }

                                                        while ( !$rsCreditos->eof() ) {
                                                            if ( $rsCreditos->getCampo ('desconto') != 'f' ) {
                                                                if ($boDesoneracao && $boLancaCredito) {
                                                                    foreach ($arCalculosNaoDesonerado as $key => $valor) {
                                                                        if ( $valor['cod_credito'] == $rsCreditos->getCampo('cod_credito') ) {
                                                                            $somaValoresDescontados += $valor['valor'];
                                                                            break;
                                                                        }
                                                                    }
                                                                } else {
                                                                    for ($i=0; $i<$inCount; $i++) {
                                                                            $somaValoresDescontados += $arCalculoTEMP[$i]['valor'];
                                                                    }
                                                                }
                                                            }
                                                            $rsCreditos->proximo();
                                                        }

                                                        $totalDescontos = 0.00;
                                                        $rsCreditos->setPrimeiroElemento();
                                                        $i = 0;
                                                        while ( !$rsCreditos->eof() ) {
                                                            if ($boDesoneracao && $boLancaCredito) {
                                                                foreach ($arCalculosNaoDesonerado as $key => $valor) {
                                                                    $valorCreditoTMP = 0;
                                                                    if ( $valor['cod_credito'] == $rsCreditos->getCampo('cod_credito') ) {
                                                                        $valorCreditoTMP = $valor['valor'];
                                                                        break;
                                                                    }
                                                                }
                                                            } else {
                                                                $obTARRCalculo = new TARRCalculo;
                                                                for ( $inT=0; $inT<count( $arCalculoTEMP ); $inT++ ) {
                                                                    $stFiltro = " where cod_calculo = ".$arCalculoTEMP[$inT]["cod_calculo"];
                                                                    $obTARRCalculo->recuperaTodos( $rsDadosCalculo, $stFiltro, "", $boTransacao );
                                                                    if ( !$rsDadosCalculo->eof() ) {
                                                                        if (
                                                                                ( $rsDadosCalculo->getCampo("cod_credito") == $rsCreditos->getCampo('cod_credito') ) &&
                                                                                ( $rsDadosCalculo->getCampo("cod_genero") == $rsCreditos->getCampo('cod_genero') ) &&
                                                                                ( $rsDadosCalculo->getCampo("cod_especie") == $rsCreditos->getCampo('cod_especie') ) &&
                                                                                ( $rsDadosCalculo->getCampo("cod_natureza") == $rsCreditos->getCampo('cod_natureza') )
                                                                           ) {
                                                                            $valorCreditoTMP = $arCalculoTEMP[$inT]['valor'];
                                                                            break;
                                                                        }
                                                                    }
                                                                }
                                                            }

                                                            if ( $rsCreditos->getCampo ('desconto') != 'f' ) {
                                                                if ($arConfParcelas[$cont]['stTipoDesconto'] == 'Percentual') {
                                                                    $valorDescontar = ( $valorCreditoTMP * $nuValorDescontoTMP / 100  );
                                                                } else {
                                                                    $valorDescontar = ( ($valorCreditoTMP / $somaValoresDescontados) * $nuValorDescontoTMP );
                                                                }
                                                                $totalDescontos += $valorCreditoTMP - $valorDescontar;
                                                            } else {
                                                                $totalDescontos += $valorCreditoTMP;
                                                            }
                                                            $totalDescontos = str_replace ( ',', '', number_format ( $totalDescontos , 2 ));
                                                            $i++;
                                                            $rsCreditos->proximo();
                                                        }

                                                        $nuValorDesconto = number_format ( $totalDescontos , 2 );
                                                        $nuValorDesconto = str_replace ( ',', '',$nuValorDesconto);

                                                    } else {  //parcela normal

                                                        $rsCreditos->setPrimeiroElemento();
                                                        $somaValoresDescontados = 0.00;
                                                        $inCount = count( $arCalculo );
                                                        while ( !$rsCreditos->eof() ) {
                                                            if ( $rsCreditos->getCampo ('desconto') != 'f') {
                                                                for ($i=0; $i<$inCount; $i++) {
                                                                    if ( $arCalculo[$i]['cod_credito'] == $rsCreditos->getCampo('cod_credito') ) {
                                                                    $somaValoresDescontados += $arCalculo[$i]['valor'];
                                                                    }
                                                                }
                                                            }
                                                            $rsCreditos->proximo();
                                                        }
                                                        $totalDescontos = 0.00;
                                                        $rsCreditos->setPrimeiroElemento();
                                                        $i = 0;

                                                        while ( !$rsCreditos->eof() ) {
                                                            $valorCreditoTMP = $arCalculo[$i]['valor'];
                                                            $valorCreditoTMP = $valorCreditoTMP/$this->getTotalParcelas();
                                                            if ( $rsCreditos->getCampo ('desconto') != 'f' ) {
                                                                if ($arConfParcelas[$cont]['stTipoDesconto'] == 'Percentual') {
                                                                    $valorDescontar = ( $valorCreditoTMP*$nuValorDescontoTMP/100  );
                                                                } else {
                                                                    $valorDescontar=(($valorCreditoTMP / $somaValoresDescontados)*$nuValorDescontoTMP);
                                                                }
                                                                $totalDescontos += $valorCreditoTMP - $valorDescontar;
                                                            } else {
                                                                $totalDescontos += $valorCreditoTMP;
                                                            }
                                                            $totalDescontos = str_replace ( ',', '', number_format ( $totalDescontos , 2 ));
                                                            $i++;
                                                            $rsCreditos->proximo();
                                                        }

                                                        $nuValorDesconto = number_format ( $totalDescontos , 2 );
                                                        $nuValorDesconto = str_replace ( ',', '',$nuValorDesconto );
                                                    }
                                                    //fim descontos
                                                    $this->obTARRParcelaDesconto->setDado ("cod_parcela" , $this->inCodParcela );
                                                    $this->obTARRParcelaDesconto->setDado ("vencimento"  , $dtDataVencimento);
                                                    $this->obTARRParcelaDesconto->setDado ("valor"       , $nuValorDesconto );
                                                    $obErro = $this->obTARRParcelaDesconto->inclusao ( $boTransacao );
                                                }// fim se tem desconto na parcela
                                            } // chave 17

                                            if ( !$obErro->ocorreu() ) { // chave 18

                                                $this->obRARRCarne->obRMONConvenio->setCodigoConvenio($inCodConvenio);

                                                $this->obRARRCarne->obRARRParcela->setCodParcela( $arrParcelasNovas[$cont]);
                                                $obErro = $this->obRARRCarne->obRMONConvenio->listarConvenioBanco( $rsConvenioBanco, $boTransacao );

                                                if ( $rsConvenioBanco->getNumLinhas() < 1 ) {
                                                    $obErro->setDescricao("Configuração inválida para o Convênio/Função!");
                                                }

                                                if ( !$obErro->ocorreu() ) { // chave 19
                                                    $this->obRARRCarne->obRMONConvenio->obRFuncao->setCodFuncao ($rsConvenioBanco->getCampo("cod_funcao"));
                                                    $this->obRARRCarne->obRMONConvenio->obRFuncao->obRBiblioteca->roRModulo->setCodModulo( ($rsConvenioBanco->getCampo("cod_modulo") ));
                                                    $this->obRARRCarne->obRMONConvenio->obRFuncao->obRBiblioteca->setCodigoBiblioteca( ($rsConvenioBanco->getCampo("cod_biblioteca") ));
                                                    $obErro = $this->obRARRCarne->obRMONConvenio->obRFuncao->consultar( $boTransacao );

                                                    if ( !$obErro->ocorreu() ) { // chave 20
                                                        $stFNumeracao = "F".$this->obRARRCarne->obRMONConvenio->obRFuncao->getNomeFuncao();
                                                        $stFNumeracaoMap = "../../classes/funcao/".$stFNumeracao.".class.php";
                                                        include_once ( $stFNumeracaoMap );
                                                        $obFNumeracao = new $stFNumeracao;

                                                        if ( !$rsConvenioBanco->getCampo("cod_convenio")) {
                                                            $obErro->setDescricao('Convênio não cadastrado para Emissao do Crédito/Grupo de Créditos');
                                                        }

                                                        $stParametros = "'".$rsConvenioBanco->getCampo( "cod_carteira" )."','".$rsConvenioBanco->getCampo( "cod_convenio" )."'";

                                                        if ( !$obErro->ocorreu() ) {
                                                            $obErro = $obFNumeracao->executaFuncao($rsRetorno,$stParametros,$boTransacao);
                                                        }

                                                        if ( !$obErro->ocorreu() ) { // chave 21
                                                            $inNumeracao = $rsRetorno->getCampo( "valor" );
                                                            $this->obRARRCarne->setNumeracao( $inNumeracao );

                                                            #$obErro = $this->obRARRCarne->incluirCarne( $boTransacao);

                                                            $obFNumeracao = null;
                                                            $obTARRCarne->setDado( 'numeracao', $inNumeracao );
                                                            $obTARRCarne->setDado( 'cod_convenio', $inCodConvenio );
                                                            $obTARRCarne->setDado( 'cod_parcela', $this->obRARRCarne->obRARRParcela->getCodParcela());
                                                            $obTARRCarne->setDado( 'exercicio', $this->obRARRCarne->getExercicio() );
                                                            $obTARRCarne->setDado( 'cod_carteira', $this->obRARRCarne->obRMONCarteira->getCodigoCarteira() );
                                                            $obTARRCarne->inclusao( $boTransacao );
                                                        } // chave 21
                                                    } //chave 20
                                                } // chave 19
                                        } // chave 18
                                        $cont++;
                                    }
                                } // chave 17
                            } // chave 16
                        } //chave 13
                    } // chave 11
                } // chave 10
            #}

            $stIMAnterior = $inInscricao;
            $inFirstLoop  = FALSE;
            if ( !$obErro->ocorreu() ) { // chave 22
                $this->obTARRLancamentoCalculo->setDado( "cod_lancamento" , $this->inCodLancamento        );
                $this->obTARRLancamentoCalculo->setDado( "cod_calculo" ,$rsCalculos->getCampo("cod_calculo"));

                if ($boLancPago == TRUE) {
                    $this->obTARRLancamentoCalculo->setDado( "valor"  , $arCalculo[$inCountCalc]['valor'] );
                }else
                if ($boUsaDesoneracaoFuncao) {
                    foreach ($arDesoneracoesFuncao as $key => $valor) {
                        if ( $valor['cod_calculo'] == $rsCalculos->getCampo("cod_calculo") ) {
                            $this->obTARRLancamentoCalculo->setDado( "valor" , $valor['valor'] );
                            break;
                        }
                    }
                }else
                if ($boDesoneracao) {
                    foreach ($arCalculosDesoneracao as $key => $valor) {
                        if ( $valor['cod_calculo'] == $rsCalculos->getCampo("cod_calculo") ) {
                            $this->obTARRLancamentoCalculo->setDado( "valor" , 0 );
                            break;
                        }
                    }
                    if ($boLancaCredito) {
                        foreach ($arCalculosNaoDesonerado as $key => $valor) {
                            if ( $valor['cod_calculo'] == $rsCalculos->getCampo("cod_calculo") ) {
                                $this->obTARRLancamentoCalculo->setDado( "valor" , $valor['valor'] );
                                break;
                            }
                        }
                    }
                } else {
                    $this->obTARRLancamentoCalculo->setDado( "valor"  , $rsCalculos->getCampo('valor'));
                }
                $obErro = $this->obTARRLancamentoCalculo->inclusao( $boTransacao );

                $inCountCalc++;

                if ( !$obErro->ocorreu() && $boUsaDesoneracao ) {
                    $inNumDes = count( $arDesoneracoes ) -1 ;
                    require_once ( CAM_GT_ARR_MAPEAMENTO . "TARRLancamentoUsaDesoneracao.class.php");
                    if ( !is_object( $this->obTARRLancamentoUsaDesoneracao ) )
                        $this->obTARRLancamentoUsaDesoneracao = new TARRLancamentoUsaDesoneracao;
                        for ($i = 0 ; $i <= $inNumDes ; $i++) {
                            $this->obTARRLancamentoUsaDesoneracao->setDado ( 'cod_lancamento',$this->inCodLancamento );
                            $this->obTARRLancamentoUsaDesoneracao->setDado ( 'cod_calculo' , $rsCalculos->getCampo( 'cod_calculo' ) );
                            $this->obTARRLancamentoUsaDesoneracao->setDado ( 'cod_desoneracao' , $arDesoneracoes[ $i ][ 'cod_desoneracao' ] ) ;
                            $this->obTARRLancamentoUsaDesoneracao->setDado ( 'numcgm' , $arDesoneracoes[ $i ][ 'numcgm' ] );
                            $this->obTARRLancamentoUsaDesoneracao->setDado ( 'ocorrencia' , $arDesoneracoes[ $i ][ 'ocorrencia' ] );
                            $obErro = $this->obTARRLancamentoUsaDesoneracao->inclusao ( $boTransacao ) ;
                        }
                }

                if ($boUsaDesoneracaoFuncao) {
                    $inNumDes = count( $arDesoneracoesFuncao ) -1 ;
                    require_once ( CAM_GT_ARR_MAPEAMENTO . "TARRLancamentoUsaDesoneracao.class.php");
                    if ( !is_object( $this->obTARRLancamentoUsaDesoneracao ) )
                    $this->obTARRLancamentoUsaDesoneracao = new TARRLancamentoUsaDesoneracao;
                    for ($i = 0 ; $i <= $inNumDes ; $i++) {
                        if ($arDesoneracoesFuncao[ $i ] ['cod_calculo' ] == $rsCalculos->getCampo( 'cod_calculo' )) {
                            $this->obTARRLancamentoUsaDesoneracao->setDado ( 'cod_lancamento',$this->inCodLancamento );
                            $this->obTARRLancamentoUsaDesoneracao->setDado ( 'cod_calculo' , $arDesoneracoesFuncao[ $i ] ['cod_calculo' ] );
                            $this->obTARRLancamentoUsaDesoneracao->setDado ( 'cod_desoneracao' , $arDesoneracoesFuncao[ $i ][ 'cod_desoneracao' ] ) ;
                            $this->obTARRLancamentoUsaDesoneracao->setDado ( 'numcgm' , $arDesoneracoesFuncao[ $i ][ 'numcgm' ] );
                            $this->obTARRLancamentoUsaDesoneracao->setDado ( 'ocorrencia' , $arDesoneracoesFuncao[ $i ][ 'ocorrencia' ] );
                            $obErro = $this->obTARRLancamentoUsaDesoneracao->inclusao ( $boTransacao ) ;
                        }
                    }
                    //deleta da tabela de parcelas quando for desoneracao
                    $this->obTARRParcela->recuperaListaConsulta($rsParcelaDeletar, $this->inCodLancamento, "", $boTransacao);
                    if ($rsParcelaDeletar->getNumLinhas() > 0) {
                        $this->obTARRParcela->setDado( "cod_parcela"  , $this->inCodParcela );
                        $this->obTARRParcela->setDado( "cod_lancamento",$this->inCodLancamento );
                        $this->obTARRParcela->setDado( "nr_parcela" , $numeroParcela      );
                        $this->obTARRParcela->setDado( "vencimento" , $dtDataVencimento   );
                        $this->obTARRParcela->setDado( "valor"      , $nuValorParcela     );    
                        $obErro = $this->obTARRParcela->exclusao( $boTransacao );
                    }
                }
            } // chave 22
        }

        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRLancamento );

        if ($boDesoneracao || $boDesoneracaodeUso) {

            $obErro = $obDesoneracao->concederDesoneracao( $boTransacao, $arCalculosDesoneracao );
        }

        $rsCalculos->proximo();
        }
    } // chave mestre 1

    return $obErro;
}

//======================================================================================================================
//========================================================== LANCAMENTO ECONOMICO
/**
    * Efetuar Lancamento Econômico
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function efetuarLancamentoParcialIndividualEconomico($boTransacao = "", $arCalculos)
{
    if (!$boTransacao) {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    }
    $obErro = new Erro ;

    if ( !$obErro->ocorreu() ) {
        $rsCalculos = new RecordSet;
        $rsCalculos->preenche ( $arCalculos );

        $this->roRARRCalculo->obRModulo->setCodModulo ( 14 );
        $this->roRARRCalculo->setCodCalculo( $rsCalculos->getCampo('cod_calculo') );
        $this->roRARRCalculo->listarCalculosLancamento( $rsCalculos, $boTransacao );

        //recupera valor total do calculo
        $nuValorCalculo = $rsCalculos->getCampo('valor');
        $arParcelasSessao = Sessao::read( "parcelas" );
        $inTotalDados = count( $arParcelasSessao );
        $inTotalParcelas = 0;
        $stDataVencimentoUnica = "";
        $stDataVencimentoPrimeira = "";
        $inDesconto = "";
        $stTipoDesconto = "";
        $inValPrimeira = 10000;
        if ( !$this->getDataVencimento()) {
            for ($inX=0; $inX < $inTotalDados; $inX++) {
                if ($arParcelasSessao[$inX]["stTipoParcela"] == "Única") {
                    $stDataVencimentoUnica = $arParcelasSessao[$inX]["dtVencimento"];
                    $inDesconto = $arParcelasSessao[$inX]["flDesconto"];
                    $stTipoDesconto = $arParcelasSessao[$inX]["stTipoDesconto"];
                } else {
                    $arData = explode("/", $arParcelasSessao[$inX]["dtVencimento"]);
                    $inVal = $arData[0] + $arData[1] + $arData[2];
                    if ($inVal < $inValPrimeira) {
                        $stDataVencimentoPrimeira = $arParcelasSessao[$inX]["dtVencimento"];
                        $inValPrimeira = $inVal;
                    }

                    $arParcelas[$inTotalParcelas] = $arParcelasSessao[$inX];
                    $inTotalParcelas++;
                }
            }
        } else {
            $stDataVencimentoUnica = $this->getDataVencimento();
            $inTotalParcelas = 1;
        }
        //insere novo lancamento
        $obErro = $this->obTARRLancamento->proximoCod( $this->inCodLancamento, $boTransacao );

        Sessao::write( "lancamentos_cods", Sessao::read( 'lancamentos_cods' ).$this->inCodLancamento."," );

        $this->obTARRLancamento->setDado( "cod_lancamento" , $this->inCodLancamento     );
        $this->obTARRLancamento->setDado( "numcgm"         , $this->roRARRCalculo->obRCGM->getNumCGM() );

        if ( $stDataVencimentoUnica )
            $this->obTARRLancamento->setDado( "vencimento"     , $stDataVencimentoUnica );
        else
            $this->obTARRLancamento->setDado( "vencimento"     , $stDataVencimentoPrimeira );

        $this->obTARRLancamento->setDado( "total_parcelas" , $inTotalParcelas );
        $this->obTARRLancamento->setDado( "valor"          , $nuValorCalculo            );
        $this->obTARRLancamento->setDado( "ativo"          , TRUE                       );
        $this->obTARRLancamento->setDado( "observacao_sistema"  , $this->getObservacaoSistema() );
        $this->obTARRLancamento->setDado( "observacao"  , $this->getObservacao()       );
        $obErro = $this->obTARRLancamento->inclusao( $boTransacao );

        //insere tabela lancamento processo
        if ( !$obErro->ocorreu() && $this->obRProcesso->getCodigoProcesso() ) {
                $this->obTARRLancamentoProcesso->setDado( "cod_lancamento" , $this->inCodLancamento     );
                $this->obTARRLancamentoProcesso->setDado( "cod_processo", $this->obRProcesso->getCodigoProcesso() );
                $this->obTARRLancamentoProcesso->setDado( "ano_exercicio",  $this->obRProcesso->getExercicio() );

                $obErro = $this->obTARRLancamentoProcesso->inclusao( $boTransacao );
        }

        //insere o desconto da parcela unica
        if ($stDataVencimentoUnica && $inDesconto) {
            if ($stTipoDesconto == 'Percentual') {
                $nuValorDesconto = $nuValorCalculo - (( $nuValorCalculo * $inDesconto ) / 100);
                $nuValorDesconto = round( $nuValorDesconto , 2 );
            } else {
                $nuValorDesconto = $nuValorCalculo - $inDesconto;
            }

            $this->obTARRLancamentoDesconto->setDado( "cod_lancamento" , $this->inCodLancamento             );
            $this->obTARRLancamentoDesconto->setDado( "cod_desconto"   , 1                                  );
            $this->obTARRLancamentoDesconto->setDado( "vencimento"     , $stDataVencimentoUnica             );
            $this->obTARRLancamentoDesconto->setDado( "valor"          , $nuValorDesconto                   );
            $obErro = $this->obTARRLancamentoDesconto->inclusao( $boTransacao );
        }

        //insere parcelas
        if ( $inTotalParcelas > 0 )
            $nuValorParcela        = round( ( $nuValorCalculo / $inTotalParcelas ) , 2 );

        //monta array com o valor das parcelas
        $inCountParcelasTemp = $inTotalParcelas + 1;
        $arParcelas     = array();
        $arConfParcelas = array();
        $nuValorTotal   = 0;
        $arParcela[1]   = $nuValorCalculo;
        for ($inNumParcela = 1; $inNumParcela < $inCountParcelasTemp; $inNumParcela++) {
            $arConfParcelas[$inNumParcela]['valor'] = $nuValorParcela;
            $nuValorTotal += $nuValorParcela;
        }

        if ($nuValorTotal != $nuValorCalculo) {
            $nuDiffParcelas = $nuValorTotal - $nuValorCalculo;
            $arConfParcelas[1]['valor'] = $nuValorParcela - $nuDiffParcelas;
        }

        // verificar convenio do grupo

        $this->roRARRCalculo->obRARRGrupo->listarCreditosFuncao($rsTmp,$boTransacao);

        $this->obRARRCarne->setExercicio  ( $this->roRARRCalculo->getExercicio() );
        $stExercicioCarne = $this->obRARRCarne->getExercicio();

        $this->obRARRCarne->obRMONConvenio->setNumeroConvenio( $rsTmp->getCampo('num_convenio') );
        $this->obRARRCarne->obRMONCarteira->setCodigoCarteira( $rsTmp->getCampo('cod_carteira') );

        $this->obRARRCarne->obRMONConvenio->listarConvenioBanco( $rsConvenioBanco, $boTransacao );
        $this->obRARRCarne->obRMONConvenio->obRFuncao->setCodFuncao( $rsConvenioBanco->getCampo( "cod_funcao" ) );
        $this->obRARRCarne->obRMONConvenio->obRFuncao->obRBiblioteca->setCodigoBiblioteca($rsConvenioBanco->getCampo( "cod_biblioteca" ) );
        $this->obRARRCarne->obRMONConvenio->obRFuncao->obRBiblioteca->roRModulo->setCodModulo(25);
        $this->obRARRCarne->obRMONConvenio->obRFuncao->consultar($boTransacao);

        $stFNumeracao = "F".$this->obRARRCarne->obRMONConvenio->obRFuncao->getNomeFuncao();
        $stFNumeracaoMap = "../../classes/funcao/".$stFNumeracao.".class.php";
        include_once ( $stFNumeracaoMap );
        $obFNumeracao = new $stFNumeracao;

        $stParametros = "'".$rsConvenioBanco->getCampo( "cod_carteira" )."','".$rsConvenioBanco->getCampo("cod_convenio" )."'";
        /********************************** fim da verificação *******************************/

        $inNumeroParcela = 1;
        $arParcelasSessao = Sessao::read( "parcelas" );
        for ( $inNumParcela = 0; $inNumParcela < count( $arParcelasSessao ); $inNumParcela++ ) {
            $obErro = $this->obTARRParcela->proximoCod( $this->inCodParcela, $boTransacao );
            $this->obTARRParcela->setDado( "cod_parcela"    , $this->inCodParcela );
            $this->obTARRParcela->setDado( "cod_lancamento" , $this->inCodLancamento );
            if ($arParcelasSessao[$inNumParcela]["stTipoParcela"] == "Única") {
                $this->obTARRParcela->setDado( "nr_parcela" , 0 );
                if ($stDataVencimentoUnica AND $inDesconto) {
                    $this->obTARRParcela->setDado( "vencimento" , $stDataVencimentoUnica );
                    $this->obTARRParcela->setDado( "valor"      , $nuValorDesconto                   );
                } else {
                    $this->obTARRParcela->setDado( "vencimento" , $this->getDataVencimento() );
                    $this->obTARRParcela->setDado( "valor"      , $nuValorParcela            );
                }
            } else {
                $this->obTARRParcela->setDado( "nr_parcela" , $inNumeroParcela );
                $this->obTARRParcela->setDado( "vencimento" , $arParcelasSessao[$inNumParcela]["dtVencimento"] );
                $this->obTARRParcela->setDado( "valor"      , $arConfParcelas[$inNumeroParcela]['valor'] );

                $inNumeroParcela++;
            }

            $obErro = $this->obTARRParcela->inclusao( $boTransacao );
            if ($this->inCodParcela) {
                $obErro = $obFNumeracao->executaFuncao($rsRetorno,$stParametros,$boTransacao);
                if ( !$obErro->ocorreu() ) {
                    $inNumeracao = $rsRetorno->getCampo( "valor" );
                    $this->obRARRCarne->setNumeracao( $inNumeracao );
                    $this->obRARRCarne->setExercicio( $stExercicioCarne );
                    $this->obRARRCarne->obRARRParcela->setCodParcela( $this->inCodParcela );
                    $this->obRARRCarne->obRMONConvenio->setCodigoConvenio( $rsConvenioBanco->getCampo( "cod_convenio" ) );
                    if ($rsConvenioBanco->getCampo( "cod_carteira" ) > 0)
                        $this->obRARRCarne->obRMONCarteira->setCodigoCarteira( $rsConvenioBanco->getCampo( "cod_carteira" ) );

                    $obErro = $this->obRARRCarne->incluirCarne( $boTransacao );
                    if ( $obErro->ocorreu() ) {
                        break;
                    }
                }
            }
        }
    }
    //insere relacao lancamento/calculo
    $this->obTARRLancamentoCalculo->setDado( "cod_lancamento" , $this->inCodLancamento               );
    $this->obTARRLancamentoCalculo->setDado( "cod_calculo"    , $rsCalculos->getCampo("cod_calculo") );
    $this->obTARRLancamentoCalculo->setDado( "valor"          , $rsCalculos->getCampo("valor")       );
    $obErro = $this->obTARRLancamentoCalculo->inclusao( $boTransacao );

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRLancamento );

    return $obErro;
}
//======================================================================================================================
//========================================================== LANCAMENTO ECONOMICO

/**
    * Listar Lançamentos para Consulta de Arrecadação
    * @access Public
    * @param  Object RecordSet
    * @param  Object Transação
    * @return Object  Erro
*/
function listarLancamentoConsulta(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->obRCIMImovel->getNumeroInscricao() ) {
        $stFiltro .= "ic.inscricao_municipal = ".$this->obRCIMImovel->getNumeroInscricao()." AND ";
    }
    if ( $this->obRCEMInscricaoEconomica->getInscricaoEconomica() ) {
        $stFiltro .= "cec.inscricao_economica = ".$this->obRCEMInscricaoEconomica->getInscricaoEconomica()." AND ";
    }
    if ( $this->obRCgm->getNumCgm() ) {
        $stFiltro .= "cgm.numcgm = ".$this->obRCgm->getNumCgm()." AND ";
    }
    if ( $this->roRARRCalculo->getExercicio() ) {
        $stFiltro .= "ac.exercicio = '".$this->roRARRCalculo->getExercicio()."' AND ";
    }
    if ($stFiltro) {
        $stFiltro = "\r\n\t WHERE \r\n".substr($stFiltro,0, -4)."";
    }
    $stOrdem = " ORDER BY numcgm,inscricao, dados_complementares";
    $stOrdem = "";

   $obErro = $this->obTARRLancamento->recuperaListaConsulta( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

   return $obErro;
}

/**
    * Listar Calculos de Credito
    * @access Public
    * @param  Object RecordSet
    * @param  Object Transação
    * @return Object Erro
*/
function listarCalculosCredito(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->roRARRCalculo->obRARRGrupo->getCodGrupo() ) {
        $stFiltro .= " acgc.cod_grupo = ".$this->roRARRCalculo->obRARRGrupo->getCodGrupo()." AND\n";
    }
    if ( $this->getCodLancamento() ) {
        $stFiltro .= " al.cod_lancamento= ".$this->getCodLancamento()." AND\n";
    }
    if ( $this->roRARRCalculo->obRARRGrupo->getExercicio() ) {
        $stFiltro .= " acgc.ano_exercicio= '".$this->roRARRCalculo->obRARRGrupo->getExercicio()."' AND\n";
    }
    if ($stFiltro) {
        $stFiltro = "\r\n\t WHERE \r\n".substr($stFiltro,0,-4)."";
    }

    $stOrdem = "\n ORDER BY mc.cod_credito";

    $obErro = $this->obTARRLancamento->recuperaCalculosCredito( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

function listarCalculosPorCredito(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->roRARRCalculo->obRMONCredito->getCodCredito() ) {
        $stFiltro .= " ac.cod_credito = ".$this->roRARRCalculo->obRMONCredito->getCodCredito()." AND";
    }

    if ( $this->roRARRCalculo->obRMONCredito->getCodNatureza() ) {
        $stFiltro .= " ac.cod_natureza = ".$this->roRARRCalculo->obRMONCredito->getCodNatureza()." AND";
    }

    if ( $this->roRARRCalculo->obRMONCredito->getCodEspecie() ) {
        $stFiltro .= " ac.cod_especie = ".$this->roRARRCalculo->obRMONCredito->getCodEspecie()." AND";
    }

    if ( $this->roRARRCalculo->obRMONCredito->getCodGenero() ) {
        $stFiltro .= " ac.cod_genero = ".$this->roRARRCalculo->obRMONCredito->getCodGenero()." AND";
    }

    if ( $this->getCodLancamento() ) {
        $stFiltro .= " alan.cod_lancamento= ".$this->getCodLancamento()." AND";
    }

    if ($stFiltro) {
        $stFiltro = "\r\n\t WHERE \r\n".substr($stFiltro,0,-4)."";
    }

    $stOrdem = " ORDER BY mc.cod_credito";

    $obErro = $this->obTARRLancamento->recuperaCalculosPorCredito( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

function listarCalculosCreditoIndividual(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->getCodLancamento() ) {
        $stFiltro .= " and l.cod_lancamento= ".$this->getCodLancamento();
    }
    $stOrdem = " ORDER BY mc.cod_credito";

   $obErro = $this->obTARRLancamento->recuperaCalculosCreditoIndividual( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

   return $obErro;
}

        /**
     * Recupera max(timestamp) do imovel na tabela cadastro_economico_calculo
     * @access Public
     * @param  Object RecordSet
     * @param  Object Transação
     * @return Object Erro
    */
    public function recuperaTimestampCadastroEconomicoFaturamento(&$rsRecordSet , $boTransacao = "")
    {
        $stFiltro = "";
        $stOrdem = " ORDER BY timestamp DESC LIMIT 1 ";
        include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCadastroEconomicoFaturamento.class.php"       );
        $obTARRCEFaturamento = new TARRCadastroEconomicoFaturamento;

        if ( $this->obRCEMInscricaoEconomica->getInscricaoEconomica() ) {
            $stFiltro = " WHERE CEF.inscricao_economica =  ".$this->obRCEMInscricaoEconomica->getInscricaoEconomica();
        }
        $obErro = $obTARRCEFaturamento->recuperaTimestampCadastroEconomicoFaturamento( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

        return $obErro;
    }

    public function buscaSomaParcelasPagasImovel(&$rsRecordSet , $boTransacao = "")
    {
        if ( $this->obRCIMImovel->getNumeroInscricao() ) {
            $stFiltro  = $this->obRCIMImovel->getNumeroInscricao();
        }
        if ( $this->roRARRCalculo->obRARRGrupo->getExercicio() ) {
            $stFiltro .= ",'".$this->roRARRCalculo->obRARRGrupo->getExercicio()."'";
        }
        $obErro = $this->obTARRLancamento->recuperaSomaParcelasPagasImovel( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

        return $obErro;
    }

    public function buscaSomaParcelasPagasLancamento(&$rsRecordSet , $inCodLancamentoAnterior, $boTransacao = "")
    {
        if ($inCodLancamentoAnterior) {
            $stFiltro = $inCodLancamentoAnterior;
        }
        if ( $this->roRARRCalculo->obRARRGrupo->getExercicio() ) {
            $stFiltro .= ",'".$this->roRARRCalculo->obRARRGrupo->getExercicio()."'";
        }
        $obErro = $this->obTARRLancamento->recuperaSomaParcelasPagasLancamento( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
        
        return $obErro;
    }

    public function buscaSomaParcelasUnicasPagasLancamento(&$rsRecordSet , $inCodLancamentoAnterior, $boTransacao = "")
    {
        if ($inCodLancamentoAnterior) {
            $stFiltro = $inCodLancamentoAnterior;
        }
        if ( $this->roRARRCalculo->obRARRGrupo->getExercicio() ) {
            $stFiltro .= ",'".$this->roRARRCalculo->obRARRGrupo->getExercicio()."'";
        }
        $obErro = $this->obTARRLancamento->recuperaSomaParcelasUnicasPagasLancamento( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

        return $obErro;
    }

    public function buscaSomaParcelasUnicasPagasImovel(&$rsRecordSet , $boTransacao = "")
    {
        if ( $this->obRCIMImovel->getNumeroInscricao() ) {
            $stFiltro  = $this->obRCIMImovel->getNumeroInscricao();
        }
        if ( $this->roRARRCalculo->obRARRGrupo->getExercicio() ) {
            $stFiltro .= ",'".$this->roRARRCalculo->obRARRGrupo->getExercicio()."'";
        }
        $obErro = $this->obTARRLancamento->recuperaSomaParcelasUnicasPagasImovel( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

        return $obErro;
    }

    public function buscaValorLancamentoAnterior(&$rsRecordSet , $boTransacao = "")
    {
        if ( $this->obRCIMImovel->getNumeroInscricao() ) {
            $stFiltro = $this->obRCIMImovel->getNumeroInscricao();
        }
        if ( $this->roRARRCalculo->obRARRGrupo->getExercicio() ) {
            $stFiltro .= ",'".$this->roRARRCalculo->obRARRGrupo->getExercicio()."'";
        }
        $obErro = $this->obTARRLancamento->recuperaValorLancamentoAnterior( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

        return $obErro;
    }

    public function buscaValorLancamentoAnteriorGrupo(&$rsRecordSet , $boTransacao = "")
    {
        if ( $this->obRCIMImovel->getNumeroInscricao() ) {
            $stFiltro = $this->obRCIMImovel->getNumeroInscricao();
        }
        if ( $this->roRARRCalculo->obRARRGrupo->getCodGrupo() ) {
            $stFiltro .= ",".$this->roRARRCalculo->obRARRGrupo->getCodGrupo();
        }
        if ( $this->roRARRCalculo->obRARRGrupo->getExercicio() ) {
            $stFiltro .= ",'".$this->roRARRCalculo->obRARRGrupo->getExercicio()."'";
        }
        $obErro = $this->obTARRLancamento->recuperaValorLancamentoAnteriorGrupo( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

        return $obErro;
    }

    public function buscaValorLancamentoAnteriorCreditoCadEco(&$rsRecordSet , $boTransacao = "")
    {
        $stFiltro = "";

        if ( $this->obRCEMInscricaoEconomica->getInscricaoEconomica() ) {
            $stFiltro = " a.inscricao_economica = ".$this->obRCEMInscricaoEconomica->getInscricaoEconomica(). " and ";
        }

        if ( $this->roRARRCalculo->obRMONCredito->getCodNatureza() ) {
            $stFiltro .= " b.cod_genero = ".$this->roRARRCalculo->obRMONCredito->getCodNatureza()." and ";
        }

        if ( $this->roRARRCalculo->obRMONCredito->getCodGenero() ) {
            $stFiltro .= " b.cod_genero = ".$this->roRARRCalculo->obRMONCredito->getCodGenero()." and ";
        }

        if ( $this->roRARRCalculo->obRMONCredito->getCodEspecie() ) {
            $stFiltro .= " b.cod_especie = ".$this->roRARRCalculo->obRMONCredito->getCodEspecie()." and ";
        }

        if ( $this->roRARRCalculo->obRMONCredito->getCodCredito() ) {
            $stFiltro .= " b.cod_credito = ".$this->roRARRCalculo->obRMONCredito->getCodCredito()." and ";
        }

        if ( $this->roRARRCalculo->obRARRGrupo->getExercicio() ) {
            $stFiltro .= " b.exercicio = '".$this->roRARRCalculo->obRARRGrupo->getExercicio()."' and ";
        }

        if ($stFiltro) {
            $stFiltro = "\r\n\t WHERE \r\n".substr($stFiltro, 0, -4)." AND d.cod_lancamento != ".$this->inCodLancamento." ";
        }

        $obErro = $this->obTARRLancamento->recuperaLancamentoAnteriorCreditoCadEco( $rsRecordSet, $stFiltro, $boTransacao );

        return $obErro;
    }

    public function buscaValorLancamentoAnteriorGrupoEco(&$rsRecordSet , $boTransacao = "")
    {
        $stFiltro = "";

        if ( $this->obRCEMInscricaoEconomica->getInscricaoEconomica() ) {
            $stFiltro = " cec.inscricao_economica = ".$this->obRCEMInscricaoEconomica->getInscricaoEconomica(). " and ";
        }

        if ( $this->roRARRCalculo->obRMONCredito->getCodNatureza() ) {
            $stFiltro .= " calc.cod_genero = ".$this->roRARRCalculo->obRMONCredito->getCodNatureza()." and ";
        }

        if ( $this->roRARRCalculo->obRMONCredito->getCodGenero() ) {
            $stFiltro .= " calc.cod_genero = ".$this->roRARRCalculo->obRMONCredito->getCodGenero()." and ";
        }

        if ( $this->roRARRCalculo->obRMONCredito->getCodEspecie() ) {
            $stFiltro .= " calc.cod_especie = ".$this->roRARRCalculo->obRMONCredito->getCodEspecie()." and ";
        }

        if ( $this->roRARRCalculo->obRMONCredito->getCodCredito() ) {
            $stFiltro .= " calc.cod_credito = ".$this->roRARRCalculo->obRMONCredito->getCodCredito()." and ";
        }

        if ( $this->roRARRCalculo->obRARRGrupo->getExercicio() ) {
            $stFiltro .= " calc.exercicio = '".$this->roRARRCalculo->obRARRGrupo->getExercicio()."' and ";
        }

        if ($stFiltro) {
            $stFiltro = "\r\n\t WHERE \r\n".substr($stFiltro, 0, -4)." AND al.cod_lancamento != ".$this->inCodLancamento." ";
        }

        $obErro = $this->obTARRLancamento->recuperaLancamentoAnteriorGrupoEco( $rsRecordSet, $stFiltro, $boTransacao );

        return $obErro;
    }

    public function buscaValorLancamentoAnteriorCreditoImovel(&$rsRecordSet , $boTransacao = "")
    {
        $stFiltro = "";

        if ( $this->obRCIMImovel->getNumeroInscricao() ) {
            $stFiltro = " a.inscricao_municipal = ".$this->obRCIMImovel->getNumeroInscricao(). " and ";
        }

        if ( $this->roRARRCalculo->obRMONCredito->getCodNatureza() ) {
            $stFiltro .= " b.cod_genero = ".$this->roRARRCalculo->obRMONCredito->getCodNatureza()." and ";
        }

        if ( $this->roRARRCalculo->obRMONCredito->getCodGenero() ) {
            $stFiltro .= " b.cod_genero = ".$this->roRARRCalculo->obRMONCredito->getCodGenero()." and ";
        }

        if ( $this->roRARRCalculo->obRMONCredito->getCodEspecie() ) {
            $stFiltro .= " b.cod_especie = ".$this->roRARRCalculo->obRMONCredito->getCodEspecie()." and ";
        }

        if ( $this->roRARRCalculo->obRMONCredito->getCodCredito() ) {
            $stFiltro .= " b.cod_credito = ".$this->roRARRCalculo->obRMONCredito->getCodCredito()." and ";
        }

        if ( $this->roRARRCalculo->obRARRGrupo->getExercicio() ) {
            $stFiltro .= " b.exercicio = '".$this->roRARRCalculo->obRARRGrupo->getExercicio()."' and ";
        }

        if ($stFiltro) {
            $stFiltro = "\r\n\t WHERE \r\n".substr($stFiltro, 0, -4)." AND d.cod_lancamento != ".$this->inCodLancamento." ";
        }

        $obErro = $this->obTARRLancamento->recuperaLancamentoAnteriorCreditoImovel( $rsRecordSet, $stFiltro, $boTransacao );

        return $obErro;
    }

    public function buscaValorLancamentoAnteriorGrupoImovel(&$rsRecordSet , $boTransacao = "")
    {
        $stFiltro = "";

        if ( $this->obRCIMImovel->getNumeroInscricao() ) {
            $stFiltro = " aic.inscricao_municipal = ".$this->obRCIMImovel->getNumeroInscricao(). " AND ";
        }

        if ( $this->roRARRCalculo->obRMONCredito->getCodNatureza() ) {
            $stFiltro .= " calc.cod_genero = ".$this->roRARRCalculo->obRMONCredito->getCodNatureza()." AND ";
        }

        if ( $this->roRARRCalculo->obRMONCredito->getCodGenero() ) {
            $stFiltro .= " calc.cod_genero = ".$this->roRARRCalculo->obRMONCredito->getCodGenero()." AND ";
        }

        if ( $this->roRARRCalculo->obRMONCredito->getCodEspecie() ) {
            $stFiltro .= " calc.cod_especie = ".$this->roRARRCalculo->obRMONCredito->getCodEspecie()." AND ";
        }

        if ( $this->roRARRCalculo->obRMONCredito->getCodCredito() ) {
            $stFiltro .= " calc.cod_credito = ".$this->roRARRCalculo->obRMONCredito->getCodCredito()." and ";
        }

        if ( $this->roRARRCalculo->obRARRGrupo->getExercicio() ) {
            $stFiltro .= " calc.exercicio = '".$this->roRARRCalculo->obRARRGrupo->getExercicio()."' and ";
        }

        if ($stFiltro) {
            $stFiltro = "\r\n\t WHERE \r\n".substr($stFiltro, 0, -4)." AND al.cod_lancamento != ".$this->inCodLancamento." ";
        }

        $obErro = $this->obTARRLancamento->recuperaLancamentoAnteriorGrupoImovel( $rsRecordSet, $stFiltro, $boTransacao );

        return $obErro;
    }

    public function buscaValorLancamentoAnteriorCreditoCGM(&$rsRecordSet , $boTransacao = "")
    {
        $stFiltro = "";

        if ( $this->obRCgm->getNumCGM () ) {
            $stFiltro = " ccgm.numcgm = ".$this->obRCIMImovel->getNumeroInscricao(). " and ";
        }

        if ( $this->roRARRCalculo->obRMONCredito->getCodNatureza() ) {
            $stFiltro .= " b.cod_genero = ".$this->roRARRCalculo->obRMONCredito->getCodNatureza()." and ";
        }

        if ( $this->roRARRCalculo->obRMONCredito->getCodGenero() ) {
            $stFiltro .= " b.cod_genero = ".$this->roRARRCalculo->obRMONCredito->getCodGenero()." and ";
        }

        if ( $this->roRARRCalculo->obRMONCredito->getCodEspecie() ) {
            $stFiltro .= " b.cod_especie = ".$this->roRARRCalculo->obRMONCredito->getCodEspecie()." and ";
        }

        if ( $this->roRARRCalculo->obRMONCredito->getCodCredito() ) {
            $stFiltro .= " b.cod_credito = ".$this->roRARRCalculo->obRMONCredito->getCodCredito()." and ";
        }

        if ( $this->roRARRCalculo->obRARRGrupo->getExercicio() ) {
            $stFiltro .= " b.exercicio = '".$this->roRARRCalculo->obRARRGrupo->getExercicio()."' and ";
        }

        if ($stFiltro) {
            $stFiltro = "\r\n\t WHERE \r\n".substr($stFiltro, 0, -4)." AND d.cod_lancamento != ".$this->inCodLancamento." ";
        }

        $obErro = $this->obTARRLancamento->recuperaLancamentoAnteriorCreditoCGM( $rsRecordSet, $stFiltro, $boTransacao );

        return $obErro;
    }

    public function buscaValorLancamentoAnteriorGrupoCGM(&$rsRecordSet , $boTransacao = "")
    {
        $stFiltro = "";

        if ( $this->obRCgm->getNumCGM () ) {
            $stFiltro = " ccgm.numcgm = ".$this->obRCgm->getNumCGM (). " AND ";
        }

        if ( $this->roRARRCalculo->obRMONCredito->getCodNatureza() ) {
            $stFiltro .= " calc.cod_genero = ".$this->roRARRCalculo->obRMONCredito->getCodNatureza()." AND ";
        }

        if ( $this->roRARRCalculo->obRMONCredito->getCodGenero() ) {
            $stFiltro .= " calc.cod_genero = ".$this->roRARRCalculo->obRMONCredito->getCodGenero()." AND ";
        }

        if ( $this->roRARRCalculo->obRMONCredito->getCodEspecie() ) {
            $stFiltro .= " calc.cod_especie = ".$this->roRARRCalculo->obRMONCredito->getCodEspecie()." AND ";
        }

        if ( $this->roRARRCalculo->obRMONCredito->getCodCredito() ) {
            $stFiltro .= " calc.cod_credito = ".$this->roRARRCalculo->obRMONCredito->getCodCredito()." and ";
        }

        if ( $this->roRARRCalculo->obRARRGrupo->getExercicio() ) {
            $stFiltro .= " calc.exercicio = '".$this->roRARRCalculo->obRARRGrupo->getExercicio()."' and ";
        }

        if ($stFiltro) {
            $stFiltro = "\r\n\t WHERE \r\n".substr($stFiltro, 0, -4)." AND al.cod_lancamento != ".$this->inCodLancamento." ";
        }

        $obErro = $this->obTARRLancamento->recuperaLancamentoAnteriorGrupoCGM ( $rsRecordSet, $stFiltro, $boTransacao );

        return $obErro;
    }

    public function buscaLancamentoAnterior(&$rsRecordSet , $boTransacao = "")
    {
        if ( $this->obRCIMImovel->getNumeroInscricao() ) {
            $stFiltro = $this->obRCIMImovel->getNumeroInscricao();
        }
        if ( $this->roRARRCalculo->obRARRGrupo->getExercicio() ) {
            $stFiltro .= ",'".$this->roRARRCalculo->obRARRGrupo->getExercicio()."'";
        }
        $obErro = $this->obTARRLancamento->recuperaLancamentoAnterior( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

        return $obErro;
    }

    /**
        * Recupera valores relativo a juro, multa e valor original da parcela de ISSQN
        * @access Public
        * @param  Object $rsRecordSet Retorna o RecordSet preenchido
        * @param  Object $boTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function atualizaLancamento($inCodLancamento, $stNumeracao, $nuValor, $boTransacao)
    {
        $stFiltro = '';
        if ($inCodLancamento) {
            $this->obTARRLancamento->setDado('cod_lancamento', $inCodLancamento);
        }
        if ($stNumeracao) {
            $this->obTARRLancamento->setDado('numeracao', $stNumeracao);
        }
        if ($nuValor) {
            $this->obTARRLancamento->setDado('valor', $nuValor);
        }
        $obErro = $this->obTARRLancamento->atualizaLancamento( $rsRecordSet, $boTransacao );

        return $obErro;
    }

    /**
        * Recupera valores relativo a juro, multa e valor original da parcela de ISSQN
        * @access Public
        * @param  Object $rsRecordSet Retorna o RecordSet preenchido
        * @param  Object $boTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function listaImoveisNaoLancados(&$rsRecordSet , $boTransacao = "")
    {
        include_once ( CAM_GT_ARR_MAPEAMENTO."TARRImovelVVenal.class.php"               );
        $obTARRImovelVVenal = new TARRImovelVVenal;

        $stFiltro = "";
        if ( $this->obRCIMImovel->getNumeroInscricao() ) {
            $stFiltro .= "\n\t ii.inscricao_municipal= ".$this->obRCIMImovel->getNumeroInscricao(). " and ";
        }
        if ( $this->obRCgm->getNumCgm() ) {
            $stFiltro .= "\n\t cgm.numcgm = ". $this->obRCgm->getNumCgm() . " and ";
        }

        if ($stFiltro) {
            $stFiltro = " WHERE ". substr ( $stFiltro, 0, strlen ($stFiltro) - 4 ) ;
        }

        $obErro = $obTARRImovelVVenal->recuperaImoveisNaoLancados( $rsRecordSet, $stFiltro, '',  $boTransacao );

        return $obErro;
    }

    public function listarRelatorioLancamento(&$rsRecordSet, $boTransacao = "")
    {
        $stFiltro = "";
        if ( $this->getCodLancamento() ) {
            $stFiltro .= " lancamento.cod_lancamento in (".$this->getCodLancamento().") AND ";
        }

        if ($stFiltro) {
            $stFiltro = " WHERE ". substr ( $stFiltro, 0, strlen ($stFiltro) - 4 ) ;
        }

        $obErro = $this->obTARRLancamento->recuperaRelatorioLancamento( $rsRecordSet, $stFiltro, $boTransacao, 'ORDER BY cod_lancamento, ordenacao' );

        return $obErro;
    }

    public function listarRelatorioLancamentoGeral(&$rsRecordSet, $boTransacao = "")
    {
        $stFiltro = "";
        $stOrdem = "";
        if ( $this->getCodLancamento() ) {
            $stFiltro .= " alan.cod_lancamento in (".$this->getCodLancamento().") ";
        }

        $stOrdem = "ORDER BY cod_lancamento";

        $obErro = $this->obTARRLancamento->recuperaRelatorioLancamentoGeral( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

        return $obErro;
    }

    /**
     *  @access public public
     *  @param numeric $nuDiffDesoneracaoCredito Valor a ser desonerado, caso exista
     *  @param numeric $nuValorCalculo Valor do Calculo
     *  Verifica desoneração e retorno valor do credito ja descontado
     *  se houver desoneração disponivel para uso.
    */
    public function usaDesoneracao(&$arDesoneracoes , &$nuDiffDesoneraCredito , &$nuValorCalculoD , $inCodLancamentoAnterior , $inImovel , $inCodCalculo , $boTransacao)
    {
        // buscar credito do calculo
        $obCon = new Conexao;
        $rsCre = new RecordSet;

        $stSql = "select * from arrecadacao.calculo where cod_calculo = " . $inCodCalculo;
        $obErro = $obCon->executaSQL ( $rsCre , $stSql , $boTransacao );

        $this->obRARRDesoneracao->obRMONCredito->inCodCredito = $rsCre->getCampo ( 'cod_credito' );
        $this->obRARRDesoneracao->obRMONCredito->inCodEspecie = $rsCre->getCampo ( 'cod_especie' );
        $this->obRARRDesoneracao->obRMONCredito->inCodGenero  = $rsCre->getCampo ( 'cod_genero' );
        $this->obRARRDesoneracao->obRMONCredito->inCodNatureza= $rsCre->getCampo ( 'cod_natureza' );

        $this->obRARRDesoneracao->inCodLancamento = $inCodLancamentoAnterior;
        unset ( $rsVerificaDesoneracao );
        $rsVerificaDesoneracao = new Recordset;
        $obErro = $this->obRARRDesoneracao->verificaConcessaoDesoneracaoLancamento( $rsVerificaDesoneracao, $boTransacao , $inImovel , $nuValorCalculoD);

        $nuValorAbono = 0 ;
        $arDesoneracoes = array();
        while ( !$rsVerificaDesoneracao->eof() ) {
            require_once ( CAM_GT_ARR_MAPEAMENTO . "TARRLancamentoUsaDesoneracao.class.php");
            if ( !is_object( $this->obTARRLancamentoUsaDesoneracao ) )
                $this->obTARRLancamentoUsaDesoneracao = new TARRLancamentoUsaDesoneracao;

            $nuValorAbono += $rsVerificaDesoneracao->getCampo( 'valor' );
            $arDesoneracoes[] =  array ( 'cod_desoneracao' => $rsVerificaDesoneracao->getCampo('cod_desoneracao')
                                        ,'numcgm' => $rsVerificaDesoneracao->getCampo('numcgm')
                                        ,'ocorrencia' => $rsVerificaDesoneracao->getCampo('ocorrencia') );
            $rsVerificaDesoneracao->proximo();
        }

        if ($nuValorCalculoD < $nuValorAbono) {
            $nuDiffDesoneraCredito = $nuValorAbono - $nuValorCalculoD;
            $nuValorCalculoD = 0;
        } else {
            $nuValorCalculoD = $nuValorCalculoD - $nuValorAbono;
        }

        $boUsaDesoneracao = TRUE;
    }

    public function usaDesoneracaoCadEco(&$arDesoneracoes , &$nuDiffDesoneraCredito , &$nuValorCalculoD , $inCodLancamentoAnterior , $inCadEco , $inCodCalculo , $boTransacao)
    {
        // buscar credito do calculo
        $obCon = new Conexao;
        $rsCre = new RecordSet;

        $stSql = "select * from arrecadacao.calculo where cod_calculo = " . $inCodCalculo;
        $obErro = $obCon->executaSQL ( $rsCre , $stSql , $boTransacao );

        $this->obRARRDesoneracao->obRMONCredito->inCodCredito = $rsCre->getCampo ( 'cod_credito' );
        $this->obRARRDesoneracao->obRMONCredito->inCodEspecie = $rsCre->getCampo ( 'cod_especie' );
        $this->obRARRDesoneracao->obRMONCredito->inCodGenero  = $rsCre->getCampo ( 'cod_genero' );
        $this->obRARRDesoneracao->obRMONCredito->inCodNatureza= $rsCre->getCampo ( 'cod_natureza' );

        $this->obRARRDesoneracao->inCodLancamento = $inCodLancamentoAnterior;
        unset ( $rsVerificaDesoneracao );
        $rsVerificaDesoneracao = new Recordset;
        $obErro = $this->obRARRDesoneracao->verificaConcessaoDesoneracaoLancamentoCadEco( $rsVerificaDesoneracao, $boTransacao , $inCadEco , $nuValorCalculoD);

        $nuValorAbono = 0 ;
        $arDesoneracoes = array();
        while ( !$rsVerificaDesoneracao->eof() ) {
            require_once ( CAM_GT_ARR_MAPEAMENTO . "TARRLancamentoUsaDesoneracao.class.php");
            if ( !is_object( $this->obTARRLancamentoUsaDesoneracao ) )
                $this->obTARRLancamentoUsaDesoneracao = new TARRLancamentoUsaDesoneracao;

            $nuValorAbono += $rsVerificaDesoneracao->getCampo( 'valor' );
            $arDesoneracoes[] =  array ( 'cod_desoneracao' => $rsVerificaDesoneracao->getCampo('cod_desoneracao')
                                        ,'numcgm' => $rsVerificaDesoneracao->getCampo('numcgm')
                                        ,'ocorrencia' => $rsVerificaDesoneracao->getCampo('ocorrencia') );
            $rsVerificaDesoneracao->proximo();
        }
        if ($nuValorCalculoD < $nuValorAbono) {
            $nuDiffDesoneraCredito = $nuValorAbono - $nuValorCalculoD;
            $nuValorCalculoD = 0;
        } else {
            $nuValorCalculoD = $nuValorCalculoD - $nuValorAbono;
        }

        $boUsaDesoneracao = TRUE;
    }

} // end of class
?>
