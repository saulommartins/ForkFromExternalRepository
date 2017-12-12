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
    * Classe de Regra de Negócio EmpenhoAutorização
    * Data de Criação   : 02/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Eduardo Martins

    * @package URBEM
    * @subpackage Regra

    $Revision: 32865 $
    $Name$
    $Autor:$
    $Date: 2008-01-29 09:52:43 -0200 (Ter, 29 Jan 2008) $

    * Casos de uso: uc-02.03.03
                    uc-02.03.17
                    uc-02.03.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_FW_BANCO_DADOS."Transacao.class.php";
include_once CAM_GF_EMP_NEGOCIO."REmpenhoEmpenho.class.php";
include_once CAM_GF_EMP_NEGOCIO."REmpenhoAutorizacaoEmpenho.class.php";

class REmpenhoEmpenhoAutorizacao
{
/*
    * @var Object
    * @access Private
*/
var $obTEmpenhoEmpenhoAutorizacao;
/*
    * @var Object
    * @access Private
*/
var $obTransacao;
/*
    * @var Object
    * @access Private
*/
var $obREmpenhoEmpenho;
/*
    * @var Object
    * @access Private
*/
var $obREmpenhoAutorizacaoEmpenho;

/*
    * @access Public
    * @param Object $valor
*/
function setTransacao($valor) { $this->obTransacao = $valor; }
/*
    * @access Public
    * @param Object $valor
*/
function setREmpenhoEmpenho($valor) { $this->obREmpenhoEmpenho = $valor; }
/*
    * @access Public
    * @param Object $valor
*/
function setREmpenhoAutorizacaoEmpenho($valor) { $this->obREmpenhoAutorizacaoEmpenho = $valor; }

/*
    * @access Public
    * @return Object
*/
function getTransacao() { return $this->obTransacao  ; }
/*
    * @access Public
    * @return Object
*/
function getREmpenhoEmpenho() { return $this->obREmpenhoEmpenho  ; }
/*
    * @access Public
    * @return Object
*/
function getREmpenhoAutorizacaoEmpenho() { return $this->obREmpenhoAutorizacaoEmpenho  ; }

/**
    * Método Construtor
    * @access Private
*/
function REmpenhoEmpenhoAutorizacao()
{
    $this->setTransacao                  ( new Transacao                  );
    $this->setREmpenhoEmpenho            ( new REmpenhoEmpenho            );
    $this->setREmpenhoAutorizacaoEmpenho ( new REmpenhoAutorizacaoEmpenho );
}

/**
    * Inclui dados no Banco
    * @access Private
    * @param Object $boTransacao
    * @return Object $obErro
*/
function incluir($boTransacao)
{
    include_once ( CAM_GF_EMP_MAPEAMENTO     ."TEmpenhoEmpenhoAutorizacao.class.php"  );
    $obTEmpenhoEmpenhoAutorizacao = new TEmpenhoEmpenhoAutorizacao;

    $obErro = $this->obREmpenhoEmpenho->incluir( $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTEmpenhoEmpenhoAutorizacao->setDado( "cod_empenho"     , $this->obREmpenhoEmpenho->getCodEmpenho()                           );
        $obTEmpenhoEmpenhoAutorizacao->setDado( "exercicio"       , $this->obREmpenhoEmpenho->getExercicio()                            );
        $obTEmpenhoEmpenhoAutorizacao->setDado( "cod_autorizacao" , $this->obREmpenhoAutorizacaoEmpenho->getCodAutorizacao()            );
        $obTEmpenhoEmpenhoAutorizacao->setDado( "cod_entidade"    , $this->obREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() );
        $obErro = $obTEmpenhoEmpenhoAutorizacao->inclusao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obREmpenhoAutorizacaoEmpenho->obROrcamentoReservaSaldos->setExercicio( $this->obREmpenhoEmpenho->getExercicio() );
            $obErro = $this->obREmpenhoAutorizacaoEmpenho->obROrcamentoReservaSaldos->consultar( $boTransacao );
            if ( !$obErro->ocorreu() and $this->obREmpenhoAutorizacaoEmpenho->obROrcamentoReservaSaldos->getCodReserva() ) {
//                $this->obREmpenhoAutorizacaoEmpenho->obROrcamentoReserva->setAnulada( true );
                $this->obREmpenhoAutorizacaoEmpenho->obROrcamentoReservaSaldos->setDtAnulacao( $this->obREmpenhoEmpenho->getDtEmpenho() );
                $this->obREmpenhoAutorizacaoEmpenho->obROrcamentoReservaSaldos->setMotivoAnulacao("Empenho ".$this->obREmpenhoEmpenho->getCodEmpenho()."/".$this->obREmpenhoEmpenho->getExercicio());
                $obErro = $this->obREmpenhoAutorizacaoEmpenho->obROrcamentoReservaSaldos->anular( $boTransacao );
            }
        }
    }

    return $obErro;
}

/**
    * @access Public
    * @param Object $boTransacao
    * @return Object Objeto Erro
*/
function autorizarEmpenho($boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO     ."TEmpenhoAutorizacaoEmpenho.class.php"  );
    include_once ( CAM_GF_EMP_MAPEAMENTO     ."TEmpenhoEmpenhoAutorizacao.class.php"  );
    $obTEmpenhoAutorizacaoEmpenho = new TEmpenhoAutorizacaoEmpenho;
    $obTEmpenhoEmpenhoAutorizacao = new TEmpenhoEmpenhoAutorizacao;

    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    $obErro = $this->obREmpenhoEmpenho->consultaSaldoAnteriorDataEmpenho( $nuSaldoAnterior, '', $boTransacao );
    
    $nuVlReserva = str_replace( '.','',$this->obREmpenhoAutorizacaoEmpenho->obROrcamentoReservaSaldos->getVlReserva() );
    $nuVlReserva = str_replace( ',','.',$nuVlReserva );
    $this->obREmpenhoAutorizacaoEmpenho->obROrcamentoReservaSaldos->setExercicio( $this->obREmpenhoEmpenho->getExercicio() );
    $obErro = $this->obREmpenhoAutorizacaoEmpenho->obROrcamentoReservaSaldos->consultar( $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $this->obREmpenhoAutorizacaoEmpenho->setExercicio( $this->obREmpenhoEmpenho->getExercicio() );
        $this->obREmpenhoAutorizacaoEmpenho->setCodPreEmpenho( $this->obREmpenhoEmpenho->getCodPreEmpenho() );
        $this->obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $this->obREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() );
        $obErro = $this->obREmpenhoAutorizacaoEmpenho->consultar( $boTransacao );
        if ( !$obErro->ocorreu() ) {

            $this->obREmpenhoEmpenho->obROrcamentoDespesa->setExercicio( $this->obREmpenhoEmpenho->getExercicio() );
            $this->obREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoEntidade->setCodigoEntidade( $this->obREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() );
            $this->obREmpenhoEmpenho->obROrcamentoDespesa->consultarDotacao( $rsDotacao, "", $boTransacao );

            if (!$this->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->getCodDespesa()) {
                $obTEmpenhoAutorizacaoEmpenho->setDado( "cod_pre_empenho", $this->obREmpenhoAutorizacaoEmpenho->getCodPreEmpenho()                        );
                $obTEmpenhoAutorizacaoEmpenho->setDado( "cod_autorizacao", $this->obREmpenhoAutorizacaoEmpenho->getCodAutorizacao()                       );
                $obTEmpenhoAutorizacaoEmpenho->setDado( "exercicio"      , $this->obREmpenhoAutorizacaoEmpenho->getExercicio()                            );
                $obTEmpenhoAutorizacaoEmpenho->setDado( "cod_entidade"   , $this->obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->getCodigoEntidade() );
                $obTEmpenhoAutorizacaoEmpenho->setDado( "dt_autorizacao" , $this->obREmpenhoAutorizacaoEmpenho->getDtAutorizacao()                        );
                $obTEmpenhoAutorizacaoEmpenho->setDado( "num_orgao"      , $rsDotacao->getCampo("num_orgao")                                              );
                $obTEmpenhoAutorizacaoEmpenho->setDado( "num_unidade"    , $rsDotacao->getCampo("num_unidade")                                            );
                $obTEmpenhoAutorizacaoEmpenho->setDado( "cod_categoria"  , $this->obREmpenhoEmpenho->getCodCategoria()                                    );

                $obErro = $obTEmpenhoAutorizacaoEmpenho->alteracao( $boTransacao );
            }

            if ( !$obErro->ocorreu() ) {

                $this->obREmpenhoEmpenho->setMaior(1);
                $obErro = $this->obREmpenhoEmpenho->listarMaiorData( $rsMaiorData, '', $boTransacao );

                $stMaiorData = $rsMaiorData->getCampo( "dataempenho" );

                if (SistemaLegado::comparaDatas($rsMaiorData->getCampo( "dataempenho" ),$this->obREmpenhoEmpenho->getDtEmpenho())) {
                    $obErro->setDescricao( "Data de Empenho deve ser maior que '".$rsMaiorData->getCampo( "dataempenho" )."'!" );
                }

                if (SistemaLegado::comparaDatas($this->obREmpenhoAutorizacaoEmpenho->getDtAutorizacao(),$this->obREmpenhoEmpenho->getDtEmpenho())) {
                   $obErro->setDescricao( "A data de Empenho deve ser igual ou posterior à data da Autorização de Empenho." );
                }

                if ( !$obErro->ocorreu() ) {
                    if ( ( $nuSaldoAnterior + str_replace(',','.',str_replace('.','',$this->obREmpenhoEmpenho->obROrcamentoReservaSaldos->getVlReserva())) ) < $nuVlReserva ) {
                        $obErro->setDescricao( "Valor da reserva é superior ao Saldo Anterior " );
                    }
                    if ( !$obErro->ocorreu() ) {
                        $this->obREmpenhoEmpenho->setVlSaldoAnterior( $nuSaldoAnterior );
                        $obErro = $this->incluir( $boTransacao );

                        if ( !$obErro->ocorreu() ) {
                            $obErro = $this->obREmpenhoEmpenho->alterar( $boTransacao );
                        }
                    }
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTEmpenhoEmpenhoAutorizacao );

    return $obErro;
}

/**
    * Método para realizar consulta do Empenho e da Autorizacao
    * @access Public
    * @param Object $boTransacao
    * @return Object $obErro
*/
function consultar($boTransacao = "")
{
    $obErro = $this->obREmpenhoEmpenho->consultar( $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obREmpenhoAutorizacaoEmpenho->setExercicio( $this->obREmpenhoEmpenho->getExercicio() );
        $this->obREmpenhoAutorizacaoEmpenho->setCodPreEmpenho( $this->obREmpenhoEmpenho->getCodPreEmpenho() );
        $this->obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $this->obREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() );
        $obErro = $this->obREmpenhoAutorizacaoEmpenho->consultar( $boTransacao );
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
function listarAutorizacao(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO     ."TEmpenhoEmpenhoAutorizacao.class.php"  );
    $obTEmpenhoEmpenhoAutorizacao = new TEmpenhoEmpenhoAutorizacao;

    if( $this->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->getCodDespesa() )
        $stFiltro  = " AND tabela.cod_despesa = ".$this->obROrcamentoDespesa->obREmpenhoAutorizacaoEmpenho->getCodDespesa()." ";

    if ( $this->obREmpenhoAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->getMascClassificacao() ) {
        if ( $this->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->getCodDespesa() ) {
            $stFiltro = " AND ( ".substr($stFiltro,4,strlen($stFiltro))." OR ";
        } else {
            $stFiltro .= " AND ";
        }
        $stFiltro .= " (publico.fn_mascarareduzida(CD.cod_estrutural) like publico.fn_mascarareduzida('".$this->obREmpenhoAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->getMascClassificacao()."')||'%' OR ";
        $stFiltro .= "publico.fn_mascarareduzida(tabela.cod_estrutural_rubrica) like publico.fn_mascarareduzida('".$this->obREmpenhoAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->getMascClassificacao()."')||'%') ";
        if( $this->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->getCodDespesa() )
            $stFiltro .= " ) ";
    }

    if( $this->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNumeroOrgao())
        $stFiltro .= " AND tabela.num_orgao = ".$this->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNumeroOrgao()." ";

    if( $this->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->getNumeroUnidade())
        $stFiltro .= " AND tabela.num_unidade = ".$this->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->getNumeroUnidade()." ";

    if( $this->obREmpenhoAutorizacaoEmpenho->inCodPreEmpenho )
        $stFiltro  .= " AND tabela.cod_pre_empenho = ".$this->obREmpenhoAutorizacaoEmpenho->inCodPreEmpenho." ";

    if( $this->obREmpenhoAutorizacaoEmpenho->inCodAutorizacao )
        $stFiltro  .= " AND tabela.cod_autorizacao = ".$this->obREmpenhoAutorizacaoEmpenho->inCodAutorizacao." ";

    if( $this->obREmpenhoAutorizacaoEmpenho->inCodAutorizacaoInicial )
        $stFiltro  .= " AND tabela.cod_autorizacao >= ".$this->obREmpenhoAutorizacaoEmpenho->inCodAutorizacaoInicial." ";

    if( $this->obREmpenhoAutorizacaoEmpenho->inCodAutorizacaoFinal )
        $stFiltro  .= " AND tabela.cod_autorizacao <= ".$this->obREmpenhoAutorizacaoEmpenho->inCodAutorizacaoFinal." ";

    if( $this->obREmpenhoEmpenho->getCodEmpenhoInicial() )
        $stFiltro  .= " AND tabela.cod_empenho >= ".$this->obREmpenhoEmpenho->getCodEmpenhoInicial()." ";

    if( $this->obREmpenhoEmpenho->getCodEmpenhoFinal() )
        $stFiltro  .= " AND tabela.cod_empenho <= ".$this->obREmpenhoEmpenho->getCodEmpenhoFinal()." ";

    if( $this->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->getCodEstrutural() )
        $stFiltro  .= " AND CD.cod_estrutural = '".$this->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->getCodEstrutural()."' ";

    if( $this->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->getCodRecurso() )
        $stFiltro  .= " AND D.cod_recurso = ".$this->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->getCodRecurso()." ";

    if( $this->obREmpenhoAutorizacaoEmpenho->obREmpenhoHistorico->getCodHistorico() )
        $stFiltro  .= " AND tabela.cod_historico = ".$this->obREmpenhoAutorizacaoEmpenho->obREmpenhoHistorico->getCodHistorico()." ";

    if( $this->obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->getCodigoEntidade() )
        $stFiltro  .= " AND tabela.cod_entidade IN (".$this->obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->getCodigoEntidade()." ) ";

    if( $this->obREmpenhoAutorizacaoEmpenho->obRCGM->getNumCGM() )
        $stFiltro  .= " AND tabela.credor = ".$this->obREmpenhoAutorizacaoEmpenho->obRCGM->getNumCGM()." ";

    if( $this->obREmpenhoAutorizacaoEmpenho->stExercicio )
        $stFiltro .= " AND tabela.exercicio = '".$this->obREmpenhoAutorizacaoEmpenho->stExercicio."' ";

    if( $this->obREmpenhoEmpenho->stExercicio )
        $stFiltro .= " AND tabela.exercicio_empenho = '".$this->obREmpenhoEmpenho->stExercicio."' ";

    if( $this->obREmpenhoEmpenho->inCodEmpenho )
        $stFiltro .= " AND tabela.cod_empenho = '".$this->obREmpenhoEmpenho->inCodEmpenho."' ";

    if( $this->obREmpenhoAutorizacaoEmpenho->stDtAutorizacao )
        $stFiltro .= " AND tabela.dt_autorizacao = '".$this->obREmpenhoAutorizacaoEmpenho->stDtAutorizacao."' ";

    if ($this->obREmpenhoAutorizacaoEmpenho->stDtAutorizacaoInicial or $this->obREmpenhoAutorizacaoEmpenho->stDtAutorizacaoFinal) {
        $this->obREmpenhoAutorizacaoEmpenho->stDtAutorizacaoInicial = ( $this->obREmpenhoAutorizacaoEmpenho->stDtAutorizacaoInicial ) ?$this->obREmpenhoAutorizacaoEmpenho->stDtAutorizacaoInicial : '01/01/'.$this->obREmpenhoAutorizacaoEmpenho->stExercicio;
        $this->obREmpenhoAutorizacaoEmpenho->stDtAutorizacaoFinal = ( $this->obREmpenhoAutorizacaoEmpenho->stDtAutorizacaoFinal ) ?$this->obREmpenhoAutorizacaoEmpenho->stDtAutorizacaoFinal : '31/12/'.$this->obREmpenhoAutorizacaoEmpenho->stExercicio;
        $stFiltro .= " AND  TO_DATE(dt_autorizacao,'dd/mm/yyyy' ) between ";
        $stFiltro .= "TO_DATE('".$this->obREmpenhoAutorizacaoEmpenho->stDtAutorizacaoInicial."','dd/mm/yyyy') AND TO_DATE('".$this->obREmpenhoAutorizacaoEmpenho->stDtAutorizacaoFinal."','dd/mm/yyyy') ";
    }

    if( $this->obREmpenhoAutorizacaoEmpenho->inSituacao == 1)
        $stFiltro  .= " AND tabela.situacao = 'Empenhada' ";
    if( $this->obREmpenhoAutorizacaoEmpenho->inSituacao == 2)
        $stFiltro  .= " AND tabela.situacao = 'Não Empenhada' ";
    if( $this->obREmpenhoAutorizacaoEmpenho->inSituacao == 3)
        $stFiltro  .= " AND tabela.situacao = 'Anulada' ";
    if( $this->obREmpenhoAutorizacaoEmpenho->inSituacao == 4)
        $stFiltro  .= " AND tabela.situacao <> 'Anulada' ";

/*    if( $this->obREmpenhoAutorizacaoEmpenho->obROrcamentoReserva->getAnulada() )
        $stFiltro .= " AND tabela.anulada = '".$this->obREmpenhoAutorizacaoEmpenho->obROrcamentoReserva->getAnulada()."' ";
    else
        $stFiltro .= " AND tabela.anulada = 'f' ";*/

    if ($this->obREmpenhoAutorizacaoEmpenho->boAlterar) {
        $stFiltro .= " AND NOT EXISTS ( SELECT 1                                           \n";
        $stFiltro .= "                    FROM empenho.empenho as ee                       \n";
        $stFiltro .= "                   WHERE ee.cod_pre_empenho = tabela.cod_pre_empenho \n";
        $stFiltro .= "                     AND ee.exercicio       = tabela.exercicio       \n";
        $stFiltro .= "                )                                                    \n";

//         $stFiltro .= " AND tabela.cod_pre_empenho NOT IN (
//                         SELECT
//                             cod_pre_empenho
//                         FROM
//                             empenho.empenho as EE
//                         WHERE
//                             EE.cod_pre_empenho = tabela.cod_pre_empenho and
//                             EE.exercicio = tabela.exercicio
//                         ) ";
    }

    if ($this->obREmpenhoAutorizacaoEmpenho->boAnuladaTotal) {
        $stFiltro .= " AND tabela.vl_empenhado = (
                        SELECT
                            sum(EAI.vl_anulado)
                        FROM
                            empenho.empenho_anulado_item as EAI
                        WHERE
                            EAI.cod_pre_empenho = tabela.cod_pre_empenho and
                            EAI.exercicio = tabela.exercicio
                        ) ";
    }

    $obTEmpenhoEmpenhoAutorizacao->setDado( "numcgm"    , $this->obREmpenhoAutorizacaoEmpenho->obRUsuario->obRCGM->getNumCGM() );
    $obTEmpenhoEmpenhoAutorizacao->setDado( "exercicio" , $this->obREmpenhoAutorizacaoEmpenho->stExercicio );

    $stFiltro = ($stFiltro) ? " AND " . substr($stFiltro, 4, strlen($stFiltro)) : "";
    $stOrder = ($stOrder) ? $stOrder : "tabela.cod_entidade,tabela.cod_autorizacao";
    $obErro = $obTEmpenhoEmpenhoAutorizacao->recuperaRelacionamentoAutorizacao( $rsRecordSet,$stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

}

?>