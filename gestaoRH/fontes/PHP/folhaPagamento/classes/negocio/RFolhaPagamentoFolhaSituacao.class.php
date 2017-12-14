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
    * Classe de regra de negócio FolhaPagamentoFolhaSituacao
    * Data de Criação: 29/11/2004

    * @author Analista: Vandre Miguel Ramos
    * @author Desenvolvedor: Andre Almeida

    * @package URBEM
    * @subpackage Regra

      $Revision: 30711 $
      $Name$
      $Author: souzadl $
      $Date: 2007-06-05 17:06:51 -0300 (Ter, 05 Jun 2007) $

      Caso de uso: uc-04.05.12

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class RFolhaPagamentoFolhaSituacao
{
/**
    * @access Private
    * @var String
*/
var $stSituacao;
/**
    * @access Private
    * @var String
*/
var $stDataHora;
/**
    * @access Private
    * @var String
*/
var $stTimestamp;
/**
* @var Object
* @access Private
*/
var $roRFolhaPagamentoPeriodoMovimentacao;

/**
    * @access Public
    * @param String $Valor
*/
function setSituacao($valor)
{
    if ( (($valor == "Aberto") or ($valor == "aberto")) or (($valor == "a") or ($valor == "A")) ) {
        $this->stSituacao = "a";
    } else {
        $this->stSituacao = "f";
    }
}
/**
    * @access Public
    * @param String $Valor
*/
function setDataHora($valor) { $this->stDataHora = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setTimestamp($valor) { $this->stTimestamp = $valor; }
/**
* @access Public
* @param Object $valor
*/
function setRORFolhaPagamentoPeriodoMovimentacao(&$valor) { $this->roRFolhaPagamentoPeriodoMovimentacao = &$valor; }

/**
    * @access Public
    * @return String
*/
function getSituacao()
{
    if ($this->stSituacao == "a") {
        return "Aberto";
    } else {
        return "Fechado";
    }
}
/**
    * @access Public
    * @return String
*/
function getDataHora() { return $this->stDataHora; }
/**
    * @access Public
    * @return String
*/
function getTimestamp() { return $this->stTimestamp; }
/**
    * @access Public
    * @return Object
*/
function getRORFolhaPagamentoPeriodoMovimentacao() { return $this->roRFolhaPagamentoPeriodoMovimentacao; }

/**
 * Método construtor
 * @access Private
*/
function RFolhaPagamentoFolhaSituacao(&$obRFolhaPagamentoPeriodoMovimentacao)
{
    $this->setRORFolhaPagamentoPeriodoMovimentacao( $obRFolhaPagamentoPeriodoMovimentacao );
}

/**
    * Muda a situação da FolhaSituacao para Aberto
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function abrirFolha($boTransacao = "")
{
    include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoFolhaSituacao.class.php" );
    $boFlagTransacao = false;
    $obTransacao = new Transacao;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->roRFolhaPagamentoPeriodoMovimentacao->listarUltimaMovimentacao( $rsUltimaMovimentacao, $boTransacao );
        $obTFolhaSituacao = new TFolhaPagamentoFolhaSituacao;
        $obTFolhaSituacao->setDado( "cod_periodo_movimentacao" , $rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao") );
        $obTFolhaSituacao->setDado( "situacao"                 , "a"                                                         );
        $obErro = $obTFolhaSituacao->inclusao( $boTransacao );
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTFolhaSituacao );

    return $obErro;
}

/**
    * Muda a situação da FolhaSituacao para Fechado
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function fecharFolha($boTransacao = "")
{
    include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoFolhaSituacao.class.php" );
    $boFlagTransacao = false;
    $obTransacao = new Transacao;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->roRFolhaPagamentoPeriodoMovimentacao->listarUltimaMovimentacao( $rsUltimaMovimentacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->roRFolhaPagamentoPeriodoMovimentacao->addRFolhaPagamentoCalculoFolhaPagamento();
            $obErro = $this->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoCalculoFolhaPagamento->listarLogErroCalculo( $rsRecordSet , $boTransacao );
            if ( !$obErro->ocorreu() ) {
                //Não fecha a folha se existirem erros de cálculo
                if ( $rsRecordSet->getNumLinhas() <= 0 ) {
                    $this->roRFolhaPagamentoPeriodoMovimentacao->setCodPeriodoMovimentacao( $rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao") );
                    $this->roRFolhaPagamentoPeriodoMovimentacao->addRFolhaPagamentoFolhaComplementar();
                    $obErro = $this->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoFolhaComplementar->consultarFolhaComplementarAberta( $boTransacao );
                    if ( !$obErro->ocorreu() ) {
                        //Não fecha a folha se existir uma folha complementar em situação aberta
                        if ( !$this->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoFolhaComplementar->getCodComplementar() ) {
                            $this->roRFolhaPagamentoPeriodoMovimentacao->setCodPeriodoMovimentacao( $rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao") );
                            if ( !$obErro->ocorreu() ) {
                                $obTFolhaSituacao = new TFolhaPagamentoFolhaSituacao;
                                $obTFolhaSituacao->setDado( "cod_periodo_movimentacao" , $rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao") );
                                $obTFolhaSituacao->setDado( "situacao"                 , "f"                                                         );
                                $obErro = $obTFolhaSituacao->inclusao( $boTransacao );
                                if ( !$obErro->ocorreu() ) {
                                    $obErro = $this->incluirFolhaPagamentoSituacaoFechada( $boTransacao );
                                }

                            }
                        } else {
                            $obErro->setDescricao("Existe uma folha complementar em situação aberta. Feche a folha complementar para prosseguir com o fechamento da folha salário.");
                        }
                    }
                } else {
                    $obErro->setDescricao("A Folha Salário só poderá ser fechada se não existirem erros de cálculo.");
                }
            }
        }
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTFolhaSituacao );

    return $obErro;
}

/**
    * excluirFolhaSituacao
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirFolhaSituacao($boTransacao = "")
{
    include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoFolhaSituacao.class.php" );
    include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoComplementarSituacaoFechada.class.php" );
    $boFlagTransacao = false;
    $obTransacao = new Transacao;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    $obTFolhaPagamentoFolhaSituacao               = new TFolhaPagamentoFolhaSituacao;
    $obTFolhaPagamentoComplementarSituacaoFechada = new TFolhaPagamentoComplementarSituacaoFechada;
    if ( !$obErro->ocorreu() ) {
        $obTFolhaPagamentoComplementarSituacaoFechada->setDado("cod_periodo_movimentacao", $this->roRFolhaPagamentoPeriodoMovimentacao->getCodPeriodoMovimentacao() );
        $obErro = $obTFolhaPagamentoComplementarSituacaoFechada->exclusao( $boTransacao );
    }
    if ( !$obErro->ocorreu() ) {
        $obTFolhaPagamentoFolhaSituacao->setDado( "cod_periodo_movimentacao" , $this->roRFolhaPagamentoPeriodoMovimentacao->getCodPeriodoMovimentacao() );
        $obErro = $obTFolhaPagamentoFolhaSituacao->exclusao( $boTransacao );
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTFolhaPagamentoFolhaSituacao );

    return $obErro;
}

function incluirFolhaPagamentoSituacaoFechada($boTransacao = "")
{
    include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoComplementarSituacaoFechada.class.php" );
    $boFlagTransacao = false;
    $obTransacao = new Transacao;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->listarFolhaComplementarSituacaoFechadaNaoIncluida ($rsFechadasNaoIncluidas ,$boTransacao);
        $this->consultarFolha( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $rsFechadasNaoIncluidas->setPrimeiroElemento();
            $obTComplementarSituacaoFechada = new TFolhaPagamentoComplementarSituacaoFechada;
            if ( !$obErro->ocorreu() ) {
                while ( !$rsFechadasNaoIncluidas->eof() ) {
                    $obTComplementarSituacaoFechada->setDado( "timestamp"                      , $rsFechadasNaoIncluidas->getCampo("timestamp")                 );
                    $obTComplementarSituacaoFechada->setDado( "cod_periodo_movimentacao"       , $rsFechadasNaoIncluidas->getCampo("cod_periodo_movimentacao")  );
                    $obTComplementarSituacaoFechada->setDado( "cod_complementar"               , $rsFechadasNaoIncluidas->getCampo("cod_complementar")          );
                    $obTComplementarSituacaoFechada->setDado( "timestamp_folha"                , $this->getTimestamp()                                          );
                    $obTComplementarSituacaoFechada->setDado( "cod_periodo_movimentacao_folha" , $rsFechadasNaoIncluidas->getCampo("cod_periodo_movimentacao")  );
                    $obTComplementarSituacaoFechada->inclusao( $boTransacao );

                    $rsFechadasNaoIncluidas->proximo();
                }
            }
        }
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTFolhaSituacao );

    return $obErro;
}

function consultarFolha($boTransacao = "")
{
    include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoFolhaSituacao.class.php" );
    $obTFolhaSituacao = new TFolhaPagamentoFolhaSituacao;
    $obErro = $obTFolhaSituacao->recuperaUltimaFolhaSituacao( $rsUltimaFolha, $boTransacao,"" );
    if (!$obErro->ocorreu()) {
        $this->setSituacao ( $rsUltimaFolha->getCampo("situacao")  );
        $this->setDataHora ( $rsUltimaFolha->getCampo("data_hora") );
        $this->setTimestamp( $rsUltimaFolha->getCampo("timestamp") );        
    }

    return $obErro;
}

/**
    * Método listar
    * @access Private
*/
function listar(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoFolhaSituacao.class.php");
    $obTFolhaPagamentoFolhaSituacao = new TFolhaPagamentoFolhaSituacao;
    $obErro = $obTFolhaPagamentoFolhaSituacao->recuperaRelacionamento($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

/**
    * Método listarFolha
    * @access Public
*/
function listarFolha(&$rsRecordSet,$boTransacao="")
{
    if ( $this->roRFolhaPagamentoPeriodoMovimentacao->getCodPeriodoMovimentacao() ) {
        $stFiltro .= " AND folha_situacao.cod_periodo_movimentacao = ".$this->roRFolhaPagamentoPeriodoMovimentacao->getCodPeriodoMovimentacao();
    }
    $obErro = $this->listar($rsRecordSet,$stFiltro,$stOrdem,$boTransacao);

    return $obErro;
}

function listarFolhaComplementarSituacaoFechadaNaoIncluida(&$rsFolhasComplementaresFechadasNaoIncluida,$boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoComplementarSituacao.class.php");
    $obTComplementarSituacao = new TFolhaPagamentoComplementarSituacao;
    if ( $this->roRFolhaPagamentoPeriodoMovimentacao->getCodPeriodoMovimentacao() ) {
        $stFiltro = " AND fcs.cod_periodo_movimentacao = ".$this->roRFolhaPagamentoPeriodoMovimentacao->getCodPeriodoMovimentacao();
    }
    $obErro = $obTComplementarSituacao->recuperaFolhaComplementarSituacaoFechadaNaoIncluida( $rsFolhasComplementaresFechadasNaoIncluida, $stFiltro, "", $boTransacao );

    return $obErro;
}

function listarVezesFecharAbrirFolhaPagamento(&$rsRecordSet,$inCodPeriodoMovimentacao,$stSituacao,$boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoFolhaSituacao.class.php");
    $obTFolhaPagamentoFolhaSituacao = new TFolhaPagamentoFolhaSituacao;
    $stFiltro .= " AND folha_situacao.cod_periodo_movimentacao = ".$inCodPeriodoMovimentacao;
    $stFiltro .= " AND folha_situacao.situacao = '".$stSituacao."'";
    $obErro = $obTFolhaPagamentoFolhaSituacao->recuperaVezesFecharAbrirFolhaPagamento( $rsRecordSet, $stFiltro, $boTransacao );

    return $obErro;
}

/**
    * Método consultarCompetencia
    * @access Public
    * Método que retorna a competência junto com o período de movimentação aberto
    * Retorno: Competência: Mês/Ano
    *          Período de Movimentação: 00/00/0000 à 00/00/0000
*/
function consultarCompetencia()
{
    $arMes   = array ("Janeiro", "Fevereiro", "Mar&ccedil;o", "Abril",   "Maio",     "Junho",
                       "Julho",   "Agosto",    "Setembro",     "Outubro", "Novembro", "Dezembro");
    $boTransacao = "";
    include_once(CAM_GRH_ENT_MAPEAMENTO."TEntidade.class.php");
    $obTEntidade = new TEntidade();
    $stFiltro  = " AND exercicio = '".Sessao::getExercicio()."'";
    //$inCodEntidade = (sessao->stEntidade!="") ? sessao->stEntidade : SistemaLegado::pegaConfiguracao("cod_entidade_prefeitura",8,Sessao::getExercicio());
    $stFiltro .= " AND cod_entidade = ".Sessao::getCodEntidade($boTransacao);
    $obTEntidade->recuperaEntidades($rsEntidades,$stFiltro);
    $stRetorno  = "Entidade: ".$rsEntidades->getCampo("nom_cgm")."<br>";

    $this->roRFolhaPagamentoPeriodoMovimentacao->listarUltimaMovimentacao($rsUltimaMovimentacao,$boTransacao);
    if ( $rsUltimaMovimentacao->getNumLinhas() > 0 ) {
        $arData = explode("/",$rsUltimaMovimentacao->getCampo('dt_final'));
        $inMes     = (int) ($arData[1]-1);
        $stAno     = $arData[2];
        $stMesAno  = $arMes[$inMes]."/".$stAno;
        $stPeriodo = $rsUltimaMovimentacao->getCampo('dt_inicial')." a ".$rsUltimaMovimentacao->getCampo('dt_final');

        $this->roRFolhaPagamentoPeriodoMovimentacao->setDtFinal($arData[2]."-".$arData[1]."-".$arData[0]);
        $arData = explode("/",$rsUltimaMovimentacao->getCampo('dt_inicial'));
        $this->roRFolhaPagamentoPeriodoMovimentacao->setDtInicial($arData[2]."-".$arData[1]."-".$arData[0]);

        $stRetorno .= "Competência: $stMesAno<br>";
        $stRetorno .= "Período de Movimentação: $stPeriodo";
    } else {
        $stRetorno .= "Não há período de movimentação aberto.";
    }

    return $stRetorno;
}

}
