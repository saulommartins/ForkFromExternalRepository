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
    * Classe de Regra da Conciliacao Bancária
    * Data de Criação   : 06/02/2006

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Cleisson da Silva Barboza

    * @package URBEM
    * @subpackage Regra

    $Id: RTesourariaConciliacao.class.php 65762 2016-06-16 16:07:22Z michel $

    * Casos de uso: uc-02.04.19
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GF_CONT_NEGOCIO."RContabilidadePlanoBanco.class.php";
include_once CAM_GF_TES_NEGOCIO."RTesourariaConciliacaoContabil.class.php";
include_once CAM_GF_TES_NEGOCIO."RTesourariaConciliacaoArrecadacao.class.php";
include_once CAM_GF_TES_NEGOCIO."RTesourariaConciliacaoManual.class.php";
include_once CAM_GF_TES_NEGOCIO."RTesourariaAssinatura.class.php";
include_once CAM_GF_TES_NEGOCIO."RTesourariaSaldoTesouraria.class.php";
include_once CAM_GT_MON_NEGOCIO."RMONContaCorrente.class.php";

/**
    * Classe de Regra de Negócios Conciliação Bancária
    * @author Desenvolvedor: Cleisson Barboza
*/
class RTesourariaConciliacao
{
/**
    * @var String
    * @access Private
*/
var $dtDataExtrato;
/**
    * @var String
    * @access Private
*/
var $dtDataInicial;
/**
    * @var String
    * @access Private
*/
var $dtDataFinal;
/**
    * @var Numeric
    * @access Private
*/
var $nuValorExtrato;
/**
    * @var Array
    * @access Private
*/
var $arLancamentoManual;
/**
    * @var Array
    * @access Private
*/
var $arLancamentoContabil;
/**
    * @var Array
    * @access Private
*/
var $arLancamentoArrecadacao;
/**
    * @var Array
    * @access Private
*/
var $arAssinatura;
/*
    * @var Object
    * @access Private
*/
var $roUltimoLancamentoManual;
/*
    * @var Object
    * @access Private
*/
var $roUltimoLancamentoContabil;
/*
    * @var Object
    * @access Private
*/
var $roUltimoLancamentoArrecadacao;
/*
    * @var Object
    * @access Private
*/
var $obRContabilidadePlanoBanco;
/*
    * @var Object
    * @access Private
*/
var $obRTesourariaSaldoTesouraria;
/*
    * @var Object
    * @access Private
*/
var $obRMONContaCorrente;
/*
    * @var String
    * @access Private
*/
var $stTimestampConciliacao;
/*
    * @var String
    * @access Private
*/
var $inMes;
/*
    * @var Array
    * @access Private
*/
var $arMovimentacao;
/*
    * @var Array
    * @access Private
*/
var $arMovimentacaoPendente;
/*
    * @var Array
    * @access Private
*/
var $arMovimentacaoManual;

/**
     * @access Public
     * @param String $valor
*/
function setDataExtrato($valor) { $this->dtDataExtrato         = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setDataInicial($valor) { $this->dtDataInicial          = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setDataFinal($valor) { $this->dtDataFinal            = $valor; }

/**
     * @access Public
     * @param String $valor
*/
function setValorExtrato($valor) { $this->nuValorExtrato       = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setLancamentoManual($valor) { $this->arLancamentoManual   = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setLancamentoContabil($valor) { $this->arLancamentoContabil = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setLancamentoArrecadacao($valor) { $this->arLancamentoArrecadacao = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setAssinatura($valor) { $this->arAssinatura  = $valor; }
/*
    * @access Public
    * @param String
*/
function setTimestampConciliacao($valor) { $this->stTimestampConciliacao = $valor; }
/*
    * @access Public
    * @param String
*/
function setMes($valor) { $this->inMes = $valor; }
/*
    * @access Public
    * @param Array
*/
function setMovimentacao($valor) { $this->arMovimentacao = $valor; }
/*
    * @access Public
    * @param Array
*/
function setMovimentacaoPendente($valor) { $this->arMovimentacaoPendente = $valor; }
/*
    * @access Public
    * @param Array
*/
function setMovimentacaoManual($valor) { $this->arMovimentacaoManual = $valor; }

/*
    * @access Public
    * @return String
*/
function getDataExtrato() { return $this->dtDataExtrato;            }
/*
    * @access Public
    * @return String
*/
function getDataInicial() { return $this->dtDataInicial;            }
/*
    * @access Public
    * @return String
*/
function getDataFinal() { return $this->dtDataFinal;              }

/*
    * @access Public
    * @return String
*/
function getValorExtrato() { return $this->nuValorExtrato;           }
/*
    * @access Public
    * @return String
*/
function getLancamentoManual() { return $this->arLancamentoManual;       }
/*
    * @access Public
    * @return String
*/
function getLancamentoArrecadacao() { return $this->arLancamentoArrecadacao;  }
/*
    * @access Public
    * @return String
*/
function getAssinatura() { return $this->arAssinatura;       }
/*
    * @access Public
    * @return String
*/
function getTimestampConciliacao() { return $this->stTimestampConciliacao;   }
/*
    * @access Public
    * @return String
*/
function getMes() { return $this->inMes;   }
/*
    * @access Public
    * @return Array
*/
function getMovimentacao() { return $this->arMovimentacao;   }
/*
    * @access Public
    * @return Array
*/
function getMovimentacaoPendente() { return $this->arMovimentacaoPendente;   }
/*
    * @access Public
    * @return Array
*/
function getMovimentacaoManual() { return $this->arMovimentacaoManual;   }

/**
    * Método Construtor
    * @access Private
*/
function RTesourariaConciliacao()
{
    $this->obRContabilidadePlanoBanco   = new RContabilidadePlanoBanco;
    $this->obRTesourariaAssinatura      = new RTesourariaAssinatura;
    $this->obRTesourariaSaldoTesouraria = new RTesourariaSaldoTesouraria();
    $this->obRMONContaCorrente          = new RMONContaCorrente;
}

/*
    * Método para adicionar Lancamento Manual
    * @access Public
*/
function addLancamentoManual()
{
    $this->arLancamentoManual[] = new RTesourariaConciliacaoManual( $this );
    $this->roUltimoLancamentoManual = &$this->arLancamentoManual[ count( $this->arLancamentoManual ) -1 ];
}

/*
    * Método para adicionar Lancamento Manual
    * @access Public
*/
function addLancamentoContabil()
{
    $this->arLancamentoContabil[] = new RTesourariaConciliacaoContabil( $this );
    $this->roUltimoLancamentoContabil = &$this->arLancamentoContabil[ count( $this->arLancamentoContabil ) -1 ];
}

/*
    * Método para adicionar Lancamento Manual
    * @access Public
*/
function addLancamentoArrecadacao()
{
    $this->arLancamentoArrecadacao[] = new RTesourariaConciliacaoArrecadacao( $this );
    $this->roUltimoLancamentoArrecadacao = &$this->arLancamentoArrecadacao[ count( $this->arLancamentoArrecadacao ) -1 ];
}

/*
    * Método para adicionar Assinatura
    * @access Public
*/
function addAssinatura()
{
    $this->arAssinatura[] = new RTesourariaAssinatura();
    $this->roUltimaAssinatura = &$this->arAssinatura[ count( $this->arAssinatura ) -1 ];
}

/*
    * Método para incluir movimentações no banco de dados
    * @param Object $boTransacao
    * @return Object $obErro
*/
function salvarMovimentacoes($arConciliar, $boTransacao = "")
{
    include_once CAM_GF_TES_MAPEAMENTO."TTesourariaConciliacaoLancamentoContabil.class.php";
    include_once CAM_GF_TES_MAPEAMENTO."TTesourariaConciliacaoLancamentoArrecadacao.class.php";
    include_once CAM_GF_TES_MAPEAMENTO."TTesourariaConciliacaoLancamentoArrecadacaoEstornada.class.php";
    include_once CAM_GF_TES_MAPEAMENTO."TTesourariaConciliacaoLancamentoManual.class.php";

    include_once CAM_GPC_TCMBA_MAPEAMENTO."/TTCMBAConciliacaoLancamentoContabil.class.php";
    include_once CAM_GPC_TCMBA_MAPEAMENTO."/TTCMBAConciliacaoLancamentoArrecadacao.class.php";
    include_once CAM_GPC_TCMBA_MAPEAMENTO."/TTCMBAConciliacaoLancamentoArrecadacaoEstornada.class.php";
    include_once CAM_GPC_TCMBA_MAPEAMENTO."/TTCMBAConciliacaoLancamentoManual.class.php";

    include_once CAM_FW_BANCO_DADOS."Transacao.class.php";

    $obTTesourariaConciliacaoContabil    = new TTesourariaConciliacaoLancamentoContabil();
    $obTTesourariaConciliacaoArrecadacao = new TTesourariaConciliacaoLancamentoArrecadacao();
    $obTTesourariaConciliacaoArrecadacaoEstornada = new TTesourariaConciliacaoLancamentoArrecadacaoEstornada();
    $obTTesourariaConciliacaoManual      = new TTesourariaConciliacaoLancamentoManual();
    $obTransacao                         = new Transacao();

    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if (!$this->getTimestampConciliacao()) {
            $obErro = $this->incluir($boTransacao);
        } else {
            $obErro = $this->alterar($boTransacao);
        }
    }

    if ( !$obErro->ocorreu() ) {
        $obTTesourariaConciliacaoArrecadacao->setDado( "cod_plano" , $this->obRContabilidadePlanoBanco->getCodPlano()  );
        $obTTesourariaConciliacaoArrecadacao->setDado( "exercicio_conciliacao", $this->obRContabilidadePlanoBanco->getExercicio());
        $obTTesourariaConciliacaoArrecadacao->setDado( "exercicio" , $this->obRContabilidadePlanoBanco->getExercicio() );
        $obTTesourariaConciliacaoArrecadacao->setDado( "mes"       , $this->inMes );
        $obErro = $obTTesourariaConciliacaoArrecadacao->exclusao( $boTransacao );

        if ( !$obErro->ocorreu() ) {
            $obTTesourariaConciliacaoArrecadacaoEstornada->setDado( "cod_plano" , $this->obRContabilidadePlanoBanco->getCodPlano()  );
            $obTTesourariaConciliacaoArrecadacaoEstornada->setDado( "exercicio_conciliacao", $this->obRContabilidadePlanoBanco->getExercicio());
            $obTTesourariaConciliacaoArrecadacaoEstornada->setDado( "exercicio" , $this->obRContabilidadePlanoBanco->getExercicio() );
            $obTTesourariaConciliacaoArrecadacaoEstornada->setDado( "mes"       , $this->inMes );
            $obErro = $obTTesourariaConciliacaoArrecadacaoEstornada->exclusao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $obTTesourariaConciliacaoContabil->setDado( "cod_plano", $this->obRContabilidadePlanoBanco->getCodPlano()  );
                $obTTesourariaConciliacaoContabil->setDado( "exercicio_conciliacao", $this->obRContabilidadePlanoBanco->getExercicio() );
                $obTTesourariaConciliacaoContabil->setDado( "exercicio", $this->obRContabilidadePlanoBanco->getExercicio() );
                $obTTesourariaConciliacaoContabil->setDado( "mes"      , $this->inMes );
                $obErro = $obTTesourariaConciliacaoContabil->exclusao( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $obTTesourariaConciliacaoManual->setDado("cod_plano", $this->obRContabilidadePlanoBanco->getCodPlano()  );
                    $obTTesourariaConciliacaoManual->setDado("exercicio", $this->obRContabilidadePlanoBanco->getExercicio() );
                    $obTTesourariaConciliacaoManual->setDado("mes"      , $this->inMes );
                    $obErro = $obTTesourariaConciliacaoManual->exclusao( $boTransacao );

                    if(SistemaLegado::isTCMBA($boTransacao)) {
                        if( !$obErro->ocorreu() ) {
                            $obTTCMBAConciliacaoLancamentoContabil = new TTCMBAConciliacaoLancamentoContabil;
                            $obTTCMBAConciliacaoLancamentoContabil->setDado( "cod_plano"   , $this->obRContabilidadePlanoBanco->getCodPlano() );
                            $obTTCMBAConciliacaoLancamentoContabil->setDado( "exercicio_conciliacao", $this->obRContabilidadePlanoBanco->getExercicio());
                            $obTTCMBAConciliacaoLancamentoContabil->setDado( "exercicio"   , $this->obRContabilidadePlanoBanco->getExercicio() );
                            $obTTCMBAConciliacaoLancamentoContabil->setDado( "mes"         , $this->inMes );
                            $obErro = $obTTCMBAConciliacaoLancamentoContabil->exclusao( $boTransacao );

                            if ( !$obErro->ocorreu() ) {
                                $obTTCMBAConciliacaoLancamentoArrecadacao = new TTCMBAConciliacaoLancamentoArrecadacao;
                                $obTTCMBAConciliacaoLancamentoArrecadacao->setDado( "cod_plano"   , $this->obRContabilidadePlanoBanco->getCodPlano() );
                                $obTTCMBAConciliacaoLancamentoArrecadacao->setDado( "exercicio_conciliacao", $this->obRContabilidadePlanoBanco->getExercicio());
                                $obTTCMBAConciliacaoLancamentoArrecadacao->setDado( "exercicio"   , $this->obRContabilidadePlanoBanco->getExercicio() );
                                $obTTCMBAConciliacaoLancamentoArrecadacao->setDado( "mes"         , $this->inMes );
                                $obErro = $obTTCMBAConciliacaoLancamentoArrecadacao->exclusao( $boTransacao );
                            }

                            if ( !$obErro->ocorreu() ) {
                                $obTTCMBAConciliacaoLancamentoArrecadacaoEstornada = new TTCMBAConciliacaoLancamentoArrecadacaoEstornada;
                                $obTTCMBAConciliacaoLancamentoArrecadacaoEstornada->setDado( "cod_plano"             , $this->obRContabilidadePlanoBanco->getCodPlano() );
                                $obTTCMBAConciliacaoLancamentoArrecadacaoEstornada->setDado( "exercicio_conciliacao" , $this->obRContabilidadePlanoBanco->getExercicio() );
                                $obTTCMBAConciliacaoLancamentoArrecadacaoEstornada->setDado( "exercicio"             , $this->obRContabilidadePlanoBanco->getExercicio() );
                                $obTTCMBAConciliacaoLancamentoArrecadacaoEstornada->setDado( "mes"                   , $this->inMes );
                                $obErro = $obTTCMBAConciliacaoLancamentoArrecadacaoEstornada->exclusao( $boTransacao );
                            }

                            if ( !$obErro->ocorreu() ) {
                                $obTTCMBAConciliacaoLancamentoManual = new TTCMBAConciliacaoLancamentoManual;
                                $obTTCMBAConciliacaoLancamentoManual->setDado( "cod_plano"   , $this->obRContabilidadePlanoBanco->getCodPlano() );
                                $obTTCMBAConciliacaoLancamentoManual->setDado( "exercicio_conciliacao", $this->obRContabilidadePlanoBanco->getExercicio());
                                $obTTCMBAConciliacaoLancamentoManual->setDado( "exercicio"   , $this->obRContabilidadePlanoBanco->getExercicio() );
                                $obTTCMBAConciliacaoLancamentoManual->setDado( "mes"         , $this->inMes );
                                $obErro = $obTTCMBAConciliacaoLancamentoManual->exclusao( $boTransacao );
                            }
                        }
                    } // Fim do if isTCMBA
                } 
            }
        }

        if ( !$obErro->ocorreu() ) {
            if ( is_array($this->arMovimentacao) ) {
                foreach ($this->arMovimentacao as $key => $arMovimentacao) {
                    if (!is_null($arConciliar["boConciliar_".($key+1)])) {
                        if ($arMovimentacao['tipo'] == 'A') {
                            $stTipoValor = ( strstr( $arMovimentacao['descricao'], "Estorno de" ) ) ? 'C' : 'D';
                            if ($stTipoValor == 'D') {
                                $obTTesourariaConciliacaoArrecadacao->setDado( "cod_arrecadacao"      , $arMovimentacao['cod_arrecadacao']       );
                                $obTTesourariaConciliacaoArrecadacao->setDado( "timestamp_arrecadacao", $arMovimentacao['timestamp_arrecadacao'] );
                                $obTTesourariaConciliacaoArrecadacao->setDado( "tipo"                 , $arMovimentacao['tipo_arrecadacao']      );
                                $obErro = $obTTesourariaConciliacaoArrecadacao->inclusao( $boTransacao );
                            } else { // tipoValor = 'C' || ESTORNO
                                $obTTesourariaConciliacaoArrecadacaoEstornada->setDado( "cod_arrecadacao"      , $arMovimentacao['cod_arrecadacao']       );
                                $obTTesourariaConciliacaoArrecadacaoEstornada->setDado( "timestamp_arrecadacao", $arMovimentacao['timestamp_arrecadacao'] );
                                $obTTesourariaConciliacaoArrecadacaoEstornada->setDado( "timestamp_estornada"  , $arMovimentacao['timestamp_estornada']   );
                                $obTTesourariaConciliacaoArrecadacaoEstornada->setDado( "tipo"                 , $arMovimentacao['tipo_arrecadacao']      );
                                $obErro = $obTTesourariaConciliacaoArrecadacaoEstornada->inclusao( $boTransacao );
                            }
                        } else {
                            $arData = explode('/',$arMovimentacao['dt_lancamento']);
                            $stExercicio = $arData[2];
                            $obTTesourariaConciliacaoContabil->setDado( "cod_lote"    , $arMovimentacao['cod_lote']      );
                            $obTTesourariaConciliacaoContabil->setDado( "exercicio"   , $stExercicio                     );
                            $obTTesourariaConciliacaoContabil->setDado( "tipo"        , $arMovimentacao['tipo']          );
                            $obTTesourariaConciliacaoContabil->setDado( "sequencia"   , $arMovimentacao['sequencia']     );
                            $obTTesourariaConciliacaoContabil->setDado( "cod_entidade", $arMovimentacao['cod_entidade']  );
                            $obTTesourariaConciliacaoContabil->setDado( "tipo_valor"  , $arMovimentacao['tipo_valor']    );
                            $obErro = $obTTesourariaConciliacaoContabil->inclusao( $boTransacao );
                        }
                    }

                    if( $obErro->ocorreu() )
                        break;
                }
            }
        }

        if ( !$obErro->ocorreu() ) {
            if ( is_array($this->arMovimentacaoPendente)) {
                foreach ($this->arMovimentacaoPendente as $key => $arMovimentacao) {
                    $arData = explode('/',$arMovimentacao['dt_lancamento']);
                    $stExercicio = $arData[2];
                    if ($arMovimentacao['tipo_movimentacao'] == 'A') {
                        if ( $arConciliar["boPendencia_".($key+1)]=="on" ) {
                            if ($arMovimentacao['tipo'] == 'A') {
                                $stTipoValor = ( strstr( $arMovimentacao['descricao'], "Estorno de" ) ) ? 'C' : 'D';
                                if ($stTipoValor == 'D') {
                                    $obTTesourariaConciliacaoArrecadacao->setDado( "cod_arrecadacao"      , $arMovimentacao['cod_arrecadacao']       );
                                    $obTTesourariaConciliacaoArrecadacao->setDado( "timestamp_arrecadacao", $arMovimentacao['timestamp_arrecadacao'] );
                                    $obTTesourariaConciliacaoArrecadacao->setDado( "tipo"                 , $arMovimentacao['tipo_arrecadacao']      );
                                    $obTTesourariaConciliacaoArrecadacao->setDado( "exercicio_conciliacao", $this->obRContabilidadePlanoBanco->getExercicio());
                                    $obErro = $obTTesourariaConciliacaoArrecadacao->inclusao( $boTransacao );
                                } else { // tipoValor = 'C' || ESTORNO
                                    $obTTesourariaConciliacaoArrecadacaoEstornada->setDado( "cod_arrecadacao"      , $arMovimentacao['cod_arrecadacao']       );
                                    $obTTesourariaConciliacaoArrecadacaoEstornada->setDado( "timestamp_arrecadacao", $arMovimentacao['timestamp_arrecadacao'] );
                                    $obTTesourariaConciliacaoArrecadacaoEstornada->setDado( "exercicio_conciliacao", $this->obRContabilidadePlanoBanco->getExercicio());
                                    $obTTesourariaConciliacaoArrecadacaoEstornada->setDado( "timestamp_estornada"  , $arMovimentacao['timestamp_estornada']   );
                                    $obTTesourariaConciliacaoArrecadacaoEstornada->setDado( "tipo"                 , $arMovimentacao['tipo_arrecadacao']      );
                                    $obErro = $obTTesourariaConciliacaoArrecadacaoEstornada->inclusao( $boTransacao );
                                }
                            } else {
                                $obTTesourariaConciliacaoContabil->setDado( "cod_plano"             , $arMovimentacao['cod_plano']     );
                                $obTTesourariaConciliacaoContabil->setDado( "exercicio_conciliacao" , $this->obRContabilidadePlanoBanco->getExercicio());
                                $obTTesourariaConciliacaoContabil->setDado( "exercicio"             , $stExercicio);
                                $obTTesourariaConciliacaoContabil->setDado( "cod_lote"              , $arMovimentacao['cod_lote']      );
                                $obTTesourariaConciliacaoContabil->setDado( "tipo"                  , $arMovimentacao['tipo']          );
                                $obTTesourariaConciliacaoContabil->setDado( "sequencia"             , $arMovimentacao['sequencia']     );
                                $obTTesourariaConciliacaoContabil->setDado( "cod_entidade"          , $arMovimentacao['cod_entidade']  );
                                $obTTesourariaConciliacaoContabil->setDado( "tipo_valor"            , $arMovimentacao['tipo_valor']    );
                                $obTTesourariaConciliacaoContabil->setDado( "mes"                   , $this->inMes );
                                $obErro = $obTTesourariaConciliacaoContabil->exclusao( $boTransacao );
                                if ( !$obErro->ocorreu() )
                                    $obErro = $obTTesourariaConciliacaoContabil->inclusao( $boTransacao );
                            }
                        }
                    } elseif ($arMovimentacao['tipo_movimentacao'] == 'M') {
                        if ( $arConciliar["boPendencia_".($key+1)]=="on") {
                            $obTTesourariaConciliacaoManual->setDado("cod_plano"      , $arMovimentacao['cod_plano']     );
                            $obTTesourariaConciliacaoManual->setDado("exercicio"      , $stExercicio                     );                  
                            $obTTesourariaConciliacaoManual->setDado("mes"            , $arMovimentacao['mes']           );
                            $obTTesourariaConciliacaoManual->setDado("sequencia"      , $arMovimentacao['sequencia']     );
                            $obTTesourariaConciliacaoManual->setDado("dt_lancamento"  , $arMovimentacao['dt_lancamento'] );
                            $obTTesourariaConciliacaoManual->setDado("vl_lancamento"  , $arMovimentacao['vl_lancamento'] );
                            $obTTesourariaConciliacaoManual->setDado("tipo_valor"     , $arMovimentacao['tipo_valor']    );
                            $obTTesourariaConciliacaoManual->setDado("descricao"      , $arMovimentacao['descricao']     );
                            $obTTesourariaConciliacaoManual->setDado("conciliado"     , 'true'                           );
                            $obTTesourariaConciliacaoManual->setDado("dt_conciliacao" , $this->getDataExtrato()          );
                            $obErro = $obTTesourariaConciliacaoManual->alteracao( $boTransacao );                        
                        } else {                                                                                         
                            $obTTesourariaConciliacaoManual->setDado("cod_plano"      , $arMovimentacao['cod_plano']     );
                            $obTTesourariaConciliacaoManual->setDado("exercicio"      , $stExercicio                     );                  
                            $obTTesourariaConciliacaoManual->setDado("mes"            , $arMovimentacao['mes']           );
                            $obTTesourariaConciliacaoManual->setDado("sequencia"      , $arMovimentacao['sequencia']     );
                            $obTTesourariaConciliacaoManual->setDado("dt_lancamento"  , $arMovimentacao['dt_lancamento'] );
                            $obTTesourariaConciliacaoManual->setDado("vl_lancamento"  , $arMovimentacao['vl_lancamento'] );
                            $obTTesourariaConciliacaoManual->setDado("tipo_valor"     , $arMovimentacao['tipo_valor']    );
                            $obTTesourariaConciliacaoManual->setDado("descricao"      , $arMovimentacao['descricao']     );
                            $obTTesourariaConciliacaoManual->setDado("conciliado"     , 'false'                          );
                            $obTTesourariaConciliacaoManual->setDado("dt_conciliacao" , ''                               );
                            $obErro = $obTTesourariaConciliacaoManual->alteracao( $boTransacao );
                        }
                    }
                    if( $obErro->ocorreu() )
                        break;
                }
            }
        }

        $inSequencia = 0;

        if ( !$obErro->ocorreu() ) {
            if ( is_array($this->arMovimentacaoManual)) {
                foreach ($this->arMovimentacaoManual as $key => $arMovimentacao) {
                    $inSequencia = $inSequencia+1;

                    $arMovimentacao['sequencia'] = $inSequencia;
                    $this->arMovimentacaoManual[$key]['sequencia'] = $inSequencia;

                    $obTTesourariaConciliacaoManual->setDado("cod_plano", $this->obRContabilidadePlanoBanco->getCodPlano()  );
                    $obTTesourariaConciliacaoManual->setDado("exercicio", $this->obRContabilidadePlanoBanco->getExercicio() );
                    $obTTesourariaConciliacaoManual->setDado("sequencia"    , $arMovimentacao['sequencia']    );
                    $obTTesourariaConciliacaoManual->setDado("mes"          , $this->inMes                    );
                    $obTTesourariaConciliacaoManual->setDado("dt_lancamento", $this->dtDataExtrato            );
                    $obTTesourariaConciliacaoManual->setDado("tipo_valor"   , $arMovimentacao['tipo_valor']   );
                    $obTTesourariaConciliacaoManual->setDado("vl_lancamento", $arMovimentacao['vl_lancamento']);
                    $obTTesourariaConciliacaoManual->setDado("descricao"    , $arMovimentacao['descricao']    );
                    if ($arMovimentacao['conciliar'] == 1) {
                        if (trim($arMovimentacao['dt_conciliacao']) == '' OR $arMovimentacao['dt_conciliacao'] == '&nbsp;') {
                            $stDtConciliacao = $this->getDataExtrato();
                        } else {
                            $stDtConciliacao = $arMovimentacao['dt_conciliacao'];
                        }
                        $obTTesourariaConciliacaoManual->setDado("dt_conciliacao",$stDtConciliacao);
                        $obTTesourariaConciliacaoManual->setDado("conciliado", 'true');
                    } else {
                        $obTTesourariaConciliacaoManual->setDado("dt_conciliacao",'');
                        $obTTesourariaConciliacaoManual->setDado("conciliado", 'false');
                    }

                    $obErro = $obTTesourariaConciliacaoManual->inclusao( $boTransacao );
                    if( $obErro->ocorreu() )
                        break;
                }
            }
        }

        if ( !$obErro->ocorreu() ) {
            if(SistemaLegado::isTCMBA($boTransacao)) {
                if ( is_array($this->arMovimentacaoManual)) {
                    foreach ($this->arMovimentacaoManual as $arMovimentacaoManual) {
                        if(empty($arMovimentacaoManual['cod_tipo_conciliacao'])){
                            $obErro->setDescricao('Informe o Tipo de Conciliação em todas as movimentações da aba Novas Movimentações!');
                            break;
                        }

                        $boConciliadoManual = 'true';
                        if(!$arMovimentacaoManual['conciliar'])
                            $boConciliadoManual = 'false';

                        $obTTCMBAConciliacaoLancamentoManual = new TTCMBAConciliacaoLancamentoManual;
                        $obTTCMBAConciliacaoLancamentoManual->setDado( "cod_plano"     , $this->obRContabilidadePlanoBanco->getCodPlano()  );
                        $obTTCMBAConciliacaoLancamentoManual->setDado( "exercicio"     , $this->obRContabilidadePlanoBanco->getExercicio() );
                        $obTTCMBAConciliacaoLancamentoManual->setDado( "mes"           , $this->inMes );
                        $obTTCMBAConciliacaoLancamentoManual->setDado( "sequencia"     , $arMovimentacaoManual['sequencia']     );
                        $obTTCMBAConciliacaoLancamentoManual->setDado( "dt_lancamento" , $this->dtDataExtrato                   );
                        $obTTCMBAConciliacaoLancamentoManual->setDado( "tipo_valor"    , $arMovimentacaoManual['tipo_valor']    );
                        $obTTCMBAConciliacaoLancamentoManual->setDado( "vl_lancamento" , $arMovimentacaoManual['vl_lancamento'] );
                        $obTTCMBAConciliacaoLancamentoManual->setDado( "descricao"     , $arMovimentacaoManual['descricao']     );
                        $obTTCMBAConciliacaoLancamentoManual->setDado( "conciliado"    , $boConciliadoManual );
                        $obTTCMBAConciliacaoLancamentoManual->setDado( "cod_tipo_conciliacao", $arMovimentacaoManual['cod_tipo_conciliacao'] );

                        $obErro = $obTTCMBAConciliacaoLancamentoManual->inclusao($boTransacao);

                        if( $obErro->ocorreu() )
                            break;
                    }
                }

                if ( !$obErro->ocorreu() ) {
                    foreach ($this->arMovimentacao as $key => $arMovimentacao) {
                        if ($arMovimentacao['cod_tipo_conciliacao'] != '') {
                            if($arMovimentacao['tipo'] == 'A') {
                                $stTipoValor = ( strstr( $arMovimentacao['descricao'], "Estorno de" ) ) ? 'C' : 'D';
                                if ($stTipoValor == 'D') {
                                    $obTTCMBAConciliacaoLancamentoArrecadacao = new TTCMBAConciliacaoLancamentoArrecadacao;
                                    $obTTCMBAConciliacaoLancamentoArrecadacao->setDado( "cod_plano"            , $this->obRContabilidadePlanoBanco->getCodPlano());
                                    $obTTCMBAConciliacaoLancamentoArrecadacao->setDado( "exercicio_conciliacao", $this->obRContabilidadePlanoBanco->getExercicio());
                                    $obTTCMBAConciliacaoLancamentoArrecadacao->setDado( "exercicio"            , $this->obRContabilidadePlanoBanco->getExercicio());
                                    $obTTCMBAConciliacaoLancamentoArrecadacao->setDado( "mes"                  , $this->inMes );
                                    $obTTCMBAConciliacaoLancamentoArrecadacao->setDado( "cod_arrecadacao"      , $arMovimentacao['cod_arrecadacao']);
                                    $obTTCMBAConciliacaoLancamentoArrecadacao->setDado( "timestamp_arrecadacao", $arMovimentacao['timestamp_arrecadacao']);
                                    $obTTCMBAConciliacaoLancamentoArrecadacao->setDado( "tipo"                 , $arMovimentacao['tipo_arrecadacao']);
                                    $obTTCMBAConciliacaoLancamentoArrecadacao->setDado( "cod_tipo_conciliacao" , $arMovimentacao['cod_tipo_conciliacao']);
                                    $obErro = $obTTCMBAConciliacaoLancamentoArrecadacao->inclusao( $boTransacao );
                                } else { // tipoValor = 'C' || ESTORNO
                                    $obTTCMBAConciliacaoLancamentoArrecadacaoEstornada = new TTCMBAConciliacaoLancamentoArrecadacaoEstornada;
                                    $obTTCMBAConciliacaoLancamentoArrecadacaoEstornada->setDado( "cod_plano"            , $this->obRContabilidadePlanoBanco->getCodPlano());
                                    $obTTCMBAConciliacaoLancamentoArrecadacaoEstornada->setDado( "exercicio_conciliacao", $this->obRContabilidadePlanoBanco->getExercicio());
                                    $obTTCMBAConciliacaoLancamentoArrecadacaoEstornada->setDado( "exercicio"            , $this->obRContabilidadePlanoBanco->getExercicio());
                                    $obTTCMBAConciliacaoLancamentoArrecadacaoEstornada->setDado( "mes"                  , $this->inMes );
                                    $obTTCMBAConciliacaoLancamentoArrecadacaoEstornada->setDado( "cod_arrecadacao"      , $arMovimentacao['cod_arrecadacao']);
                                    $obTTCMBAConciliacaoLancamentoArrecadacaoEstornada->setDado( "timestamp_arrecadacao", $arMovimentacao['timestamp_arrecadacao']);
                                    $obTTCMBAConciliacaoLancamentoArrecadacaoEstornada->setDado( "timestamp_estornada"  , $arMovimentacao['timestamp_estornada']);
                                    $obTTCMBAConciliacaoLancamentoArrecadacaoEstornada->setDado( "tipo"                 , $arMovimentacao['tipo_arrecadacao']);
                                    $obTTCMBAConciliacaoLancamentoArrecadacaoEstornada->setDado( "cod_tipo_conciliacao" , $arMovimentacao['cod_tipo_conciliacao']);
                                    $obErro = $obTTCMBAConciliacaoLancamentoArrecadacaoEstornada->inclusao( $boTransacao );
                                }
                            } else {
                                $obTTCMBAConciliacaoLancamentoContabil = new TTCMBAConciliacaoLancamentoContabil;
                                $obTTCMBAConciliacaoLancamentoContabil->setDado( "cod_plano"   , $this->obRContabilidadePlanoBanco->getCodPlano());
                                $obTTCMBAConciliacaoLancamentoContabil->setDado( "exercicio_conciliacao", $this->obRContabilidadePlanoBanco->getExercicio());
                                $obTTCMBAConciliacaoLancamentoContabil->setDado( "exercicio"   , $this->obRContabilidadePlanoBanco->getExercicio());
                                $obTTCMBAConciliacaoLancamentoContabil->setDado( "mes"         , $this->inMes);
                                $obTTCMBAConciliacaoLancamentoContabil->setDado( "cod_lote"    , $arMovimentacao['cod_lote']);
                                $obTTCMBAConciliacaoLancamentoContabil->setDado( "tipo"        , $arMovimentacao['tipo']);
                                $obTTCMBAConciliacaoLancamentoContabil->setDado( "sequencia"   , $arMovimentacao['sequencia']);
                                $obTTCMBAConciliacaoLancamentoContabil->setDado( "cod_entidade", $arMovimentacao['cod_entidade']);
                                $obTTCMBAConciliacaoLancamentoContabil->setDado( "tipo_valor"  , $arMovimentacao['tipo_valor']);
                                $obTTCMBAConciliacaoLancamentoContabil->setDado( "cod_tipo_conciliacao", $arMovimentacao['cod_tipo_conciliacao']);
                                $obErro = $obTTCMBAConciliacaoLancamentoContabil->inclusao( $boTransacao );
                            }
                        }

                        if( $obErro->ocorreu() )
                            break;
                    }
                }
            }
        }
        
        if ( !$obErro->ocorreu() ) {
            $this->obRTesourariaAssinatura->obROrcamentoEntidade->setCodigoEntidade($this->obRContabilidadePlanoBanco->obROrcamentoEntidade->getCodigoEntidade());
            $this->obRTesourariaAssinatura->setExercicio($this->obRContabilidadePlanoBanco->getExercicio() );
            $this->obRTesourariaAssinatura->setTipo("CO");
            $obErro = $this->obRTesourariaAssinatura->excluir( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                if ($this->arAssinatura) {
                    foreach ($this->arAssinatura as $obAssinatura) {
                        $obErro = $obAssinatura->incluir( $boTransacao );
                        if ( $obErro->ocorreu() ) {
                           break;
                        }
                    }
                }
            }
        }
    }
    
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTTesourariaConciliacao );

    return $obErro;
}

function salvar($boTransacao = "")
{
    include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php" );
    $obErro = new Erro;
    $inCount = 0;
    $obTransacao      =  new Transacao;
    $obRTesourariaConciliacaoManual     = new RTesourariaConciliacaoManual( $this );
    $obRTesourariaConciliacaoContabil   = new RTesourariaConciliacaoContabil( $this );
    $obRTesourariaConciliacaoArrecadacao= new RTesourariaConciliacaoArrecadacao( $this );
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if (!$this->getTimestampConciliacao()) {
            $obErro = $this->incluir($boTransacao);
        } else {
            $obErro = $this->alterar($boTransacao);
        }
        if ( !$obErro->ocorreu() ) {
            $obErro = $obRTesourariaConciliacaoManual->excluir( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                if ($this->arLancamentoManual) {
                    foreach ($this->arLancamentoManual as $obConciliacaoManual) {
                        $obErro = $obConciliacaoManual->incluir( $boTransacao );
                        if ( $obErro->ocorreu() ) {
                           break;
                        }
                    }
                }
            }
            if ( !$obErro->ocorreu() ) {
                $obErro = $obRTesourariaConciliacaoContabil->excluir( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    if ($this->arLancamentoContabil) {
                        foreach ($this->arLancamentoContabil as $obConciliacaoContabil) {
                            $obErro = $obConciliacaoContabil->incluir( $boTransacao );
                            if ( $obErro->ocorreu() ) {
                               break;
                            }
                        }
                    }
                }
            }
            if ( !$obErro->ocorreu() ) {
                $obRTesourariaConciliacaoArrecadacao->excluir( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    if ($this->arLancamentoArrecadacao) {
                        foreach ($this->arLancamentoArrecadacao as $obConciliacaoArrecadacao) {
                            $obErro = $obConciliacaoArrecadacao->incluir( $boTransacao );
                            if ( $obErro->ocorreu() ) {
                               break;
                            }
                        }
                    }
                }
            }
        }
        if ( !$obErro->ocorreu() ) {
            $this->obRTesourariaAssinatura->obROrcamentoEntidade->setCodigoEntidade($this->obRContabilidadePlanoBanco->obROrcamentoEntidade->getCodigoEntidade());
            $this->obRTesourariaAssinatura->setExercicio($this->obRContabilidadePlanoBanco->getExercicio() );
            $this->obRTesourariaAssinatura->setTipo("CO");
            $obErro = $this->obRTesourariaAssinatura->excluir( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                if ($this->arAssinatura) {
                    foreach ($this->arAssinatura as $obAssinatura) {
                        $obErro = $obAssinatura->incluir( $boTransacao );
                        if ( $obErro->ocorreu() ) {
                           break;
                        }
                    }
                }
            }
        }
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTTesourariaTerminal );

    return $obErro;
}

/**
    * Salva os dados no banco de dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluir($boTransacao = "")
{
    include_once ( CAM_FW_BANCO_DADOS   ."Transacao.class.php"            );
    include_once ( CAM_GF_TES_MAPEAMENTO."TTesourariaConciliacao.class.php"  );
    $obTransacao      =  new Transacao;
    $obTTesourariaConciliacao  =  new TTesourariaConciliacao;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTTesourariaConciliacao->setDado( "cod_plano" , $this->obRContabilidadePlanoBanco->getCodPlano()  );
        $obTTesourariaConciliacao->setDado( "exercicio" , $this->obRContabilidadePlanoBanco->getExercicio() );
        $obTTesourariaConciliacao->setDado( "mes"       , $this->getMes()                                   );
        $obTTesourariaConciliacao->setDado( "dt_extrato", $this->getDataExtrato()                           );
        $obTTesourariaConciliacao->setDado( "vl_extrato", $this->getValorExtrato()                          );
        $obErro = $obTTesourariaConciliacao->inclusao( $boTransacao );
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTTesourariaConciliacao );

    return $obErro;
}

/**
    * Altera os dados no banco de dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterar($boTransacao = "")
{
    include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaConciliacao.class.php"         );
    $obTransacao                          =  new Transacao;
    $obTTesourariaConciliacao  =  new TTesourariaConciliacao;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTTesourariaConciliacao->setDado( "cod_plano" , $this->obRContabilidadePlanoBanco->getCodPlano()  );
        $obTTesourariaConciliacao->setDado( "exercicio" , $this->obRContabilidadePlanoBanco->getExercicio() );
        $obTTesourariaConciliacao->setDado( "mes"       , $this->getMes()                                   );
        $obTTesourariaConciliacao->setDado( "dt_extrato", $this->getDataExtrato()                           );
        $obTTesourariaConciliacao->setDado( "vl_extrato", $this->getValorExtrato()                          );
        $obTTesourariaConciliacao->setDado( "timestamp" , date ("Y-m-d H:i:s.") . str_pad(1, 3, "0", STR_PAD_LEFT)      );
        $obErro = $obTTesourariaConciliacao->alteracao( $boTransacao );
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTTesourariaConciliacao );

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
function listarMovimentacao(&$rsRecordSet, $stFiltro = "", $stOrder = " ORDER BY dt_lancamento ", $boTransacao = "")
{
    include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaConciliacao.class.php"         );
    $obTTesourariaConciliacao = new TTesourariaConciliacao();
    if ( $this->obRContabilidadePlanoBanco->getExercicio() ) {
        $stFiltro = " exercicio = '||quote_literal('".$this->obRContabilidadePlanoBanco->getExercicio()."')||' AND ";
        $stFiltroArrecadacao = " AND TB.exercicio = '||quote_literal('".$this->obRContabilidadePlanoBanco->getExercicio()."')||' ";
        $obTTesourariaConciliacao->setDado('exercicio', $this->obRContabilidadePlanoBanco->getExercicio() );
    }
    if ( $this->getDataFinal() ) {
        $stFiltro .= " TO_CHAR(dt_lancamento,'||quote_literal('mm')||') = TO_CHAR(TO_DATE( '||quote_literal('".$this->getDataFinal()."')||'::varchar,'||quote_literal('dd/mm/yyyy')||'),'||quote_literal('mm')||') AND ";
        $stFiltroArrecadacao .= " AND TO_CHAR(TB.dt_boletim,'||quote_literal('mm')||') = TO_CHAR(TO_DATE( '||quote_literal('".$this->getDataFinal()."')||'::varchar, '||quote_literal('dd/mm/yyyy')||'),'||quote_literal('mm')||') ";
        $obTTesourariaConciliacao->setDado('stDtFinal', $this->getDataFinal() );
    }
    if ( $this->obRContabilidadePlanoBanco->getCodPlano() ) {
        $stFiltro .= " cod_plano = ".$this->obRContabilidadePlanoBanco->getCodPlano()."  AND ";
        $obTTesourariaConciliacao->setDado('inCodPlano', $this->obRContabilidadePlanoBanco->getCodPlano() );
    }

    $obTTesourariaConciliacao->setDado('inMes' , $this->inMes);

    if ($this->obRTesourariaAssinatura->obROrcamentoEntidade->getCodigoEntidade()) {
        $obTTesourariaConciliacao->setDado('inCodEntidade', $this->obRTesourariaAssinatura->obROrcamentoEntidade->getCodigoEntidade() );
    }

    $stFiltro .= " ((mes >= '||quote_literal('".str_pad($this->inMes,2,'0',STR_PAD_LEFT)."')||' AND exercicio_conciliacao = '||quote_literal('". $this->obRContabilidadePlanoBanco->getExercicio()."')||') OR conciliar != '||quote_literal('true')||') AND ";

    $obTTesourariaConciliacao->setDado( "stFiltroArrecadacao", $stFiltroArrecadacao );
    $stFiltro = ( $stFiltro ) ? " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro )-4 ) : '';

    $obTTesourariaConciliacao->setDado('stFiltro', $stFiltro);

    if(SistemaLegado::isTCMBA($boTransacao)) {
        $obErro = $obTTesourariaConciliacao->recuperaMovimentacaoTCMBA( $rsRecordSet, '', '', $boTransacao );
    } else {
        $obErro = $obTTesourariaConciliacao->recuperaMovimentacao( $rsRecordSet, '', '', $boTransacao );    
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
function listarMovimentacaoPendente(&$rsRecordSet, $stFiltro = "", $stOrder = " ORDER BY dt_lancamento ", $boTransacao = "")
{
    include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaConciliacao.class.php"         );
    $obTTesourariaConciliacao = new TTesourariaConciliacao();
    //$this->setDataInicial('31/01/2008');

    if ( $this->obRContabilidadePlanoBanco->getExercicio() ) {
        $stFiltro = " exercicio <= '||quote_literal('".$this->obRContabilidadePlanoBanco->getExercicio()."')||' AND ";
        $stFiltroArrecadacao = " AND TB.exercicio <= '||quote_literal('".$this->obRContabilidadePlanoBanco->getExercicio()."')||' ";
        $obTTesourariaConciliacao->setDado('exercicio', $this->obRContabilidadePlanoBanco->getExercicio() );
    }
    if ( $this->getDataInicial() ) {
        $stFiltro .= " dt_lancamento < TO_DATE( '||quote_literal('".$this->getDataInicial()."')||', '||quote_literal('dd/mm/yyyy')||' ) AND ";
        $stFiltroArrecadacao .= " AND TB.dt_boletim <= TO_DATE( '||quote_literal('".$this->getDataInicial()."')||'::varchar, '||quote_literal('dd/mm/yyyy')||' ) ";
        $obTTesourariaConciliacao->setDado('stDtInicial', $this->getDataInicial() );
    }
    if ( $this->obRContabilidadePlanoBanco->getCodPlano() ) {
        $stFiltro .= " cod_plano = ".$this->obRContabilidadePlanoBanco->getCodPlano()."  AND ";
        $obTTesourariaConciliacao->setDado('inCodPlano', $this->obRContabilidadePlanoBanco->getCodPlano() );
    }
    if ($this->obRTesourariaAssinatura->obROrcamentoEntidade->getCodigoEntidade()) {
        $obTTesourariaConciliacao->setDado('inCodEntidade', $this->obRTesourariaAssinatura->obROrcamentoEntidade->getCodigoEntidade() );
    }

    $obTTesourariaConciliacao->setDado('inMes' , str_pad($this->inMes,2,'0',STR_PAD_LEFT));

    $stFiltro .= " ((CASE WHEN mes = '||quote_literal('')||' THEN false ELSE mes::integer >= ".$this->inMes." END AND exercicio_conciliacao = '||quote_literal('". $this->obRContabilidadePlanoBanco->getExercicio()."')||') OR conciliar != '||quote_literal('true')||') AND ";

    $obTTesourariaConciliacao->setDado( "stFiltroArrecadacao", $stFiltroArrecadacao );
    $stFiltro = ( $stFiltro ) ? " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro )-4 ) : '';

    $obTTesourariaConciliacao->setDado('stFiltro', $stFiltro);
    $stFiltro = '';

    $obErro = $obTTesourariaConciliacao->recuperaMovimentacaoPendente( $rsRecordSet, "", $stOrder, $boTransacao );

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
    include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaConciliacao.class.php"         );
    $obTTesourariaConciliacao = new TTesourariaConciliacao();
    $obTTesourariaConciliacao->setDado( "cod_plano" , $this->obRContabilidadePlanoBanco->getCodPlano()  );
    $obTTesourariaConciliacao->setDado( "exercicio" , $this->obRContabilidadePlanoBanco->getExercicio() );
    $obTTesourariaConciliacao->setDado( "mes"       , $this->getMes()                                   );
    $obErro = $obTTesourariaConciliacao->recuperaPorChave( $rsRecordSet, $boTransacao );

    return $obErro;
}

}
