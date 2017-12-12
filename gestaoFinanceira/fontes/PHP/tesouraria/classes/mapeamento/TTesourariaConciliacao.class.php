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
    * Classe de mapeamento da tabela TESOURARIA.CONCILIACAO
    * Data de Criação: 07/02/2006

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Cleisson da Silva Barboza

    $Id: TTesourariaConciliacao.class.php 65704 2016-06-09 14:33:25Z michel $

    * Casos de uso: uc-02.04.19
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTesourariaConciliacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function __construct ()
{
    parent::Persistente();
    $this->setTabela("tesouraria.conciliacao");

    $this->setCampoCod('cod_plano');
    $this->setComplementoChave('exercicio,mes');

    $this->AddCampo('cod_plano'             , 'integer'  , true, ''     , true  , true    );
    $this->AddCampo('exercicio'             , 'varchar'  , true, '04'   , true  , true    );
    $this->AddCampo('mes'                   , 'integer'  , true, ''     , true  , true    );
    $this->AddCampo('dt_extrato'            , 'date'     , true, ''     , false , false   );
    $this->AddCampo('vl_extrato'            , 'numeric'  , false,'14.2' , false , false   );
    $this->AddCampo('timestamp'             , 'timestamp', false, ''    , false , false   );
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaMovimentacao(&$rsRecordSet, $stCondicao = "", $stOrder = "",  $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if ($stOrder != "") {
        if( !strstr( $stOrder, "ORDER BY" ) )
            $stOrder = " ORDER BY ".$stOrder;
    }
    $stSql = $this->montaRecuperaMovimentacao().$stCondicao.$stOrder;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaMovimentacao()
{
    $stSql = "SELECT * FROM tesouraria.fn_conciliacao_movimentacao_corrente( '".$this->getDado('exercicio')."'
                                                                            ,'".$this->getDado('inCodEntidade')."'
                                                                            ,'".$this->getDado('stDtInicial')."'
                                                                            ,'".$this->getDado('stDtFinal')."'
                                                                            ,'".$this->getDado('stFiltro')."'
                                                                            ,'".$this->getDado('stFiltroArrecadacao')."'
                                                                            ,".$this->getDado('inCodPlano')."
                                                                            ,'".$this->getDado('inMes')."'
                                                                           ) AS
                                                                     retorno

               (
                      ordem                 VARCHAR,
                      dt_lancamento         VARCHAR,
                      dt_conciliacao        VARCHAR,
                      descricao             VARCHAR,
                      vl_lancamento         DECIMAL,
                      vl_original           DECIMAL,
                      tipo_valor            VARCHAR,
                      conciliar             VARCHAR,
                      cod_lote              INTEGER,
                      tipo                  VARCHAR,
                      sequencia             INTEGER,
                      cod_entidade          INTEGER,
                      tipo_movimentacao     VARCHAR,
                      cod_plano             INTEGER,
                      cod_arrecadacao       INTEGER,
                      cod_receita           INTEGER,
                      cod_bordero           INTEGER,
                      timestamp_arrecadacao VARCHAR,
                      timestamp_estornada   VARCHAR,
                      tipo_arrecadacao      VARCHAR,
                      mes                   VARCHAR,
                      id                    VARCHAR,
                      exercicio_conciliacao VARCHAR
               ) ";

    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaMovimentacaoTCMBA(&$rsRecordSet, $stCondicao = "", $stOrder = "",  $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if ($stOrder != "") {
        if( !strstr( $stOrder, "ORDER BY" ) )
            $stOrder = " ORDER BY ".$stOrder;
    }
    $stSql = $this->montaRecuperaMovimentacaoTCMBA().$stCondicao.$stOrder;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaMovimentacaoTCMBA()
{
    $stSql = "SELECT retorno.*
                   , CASE WHEN conciliacao_lancamento_contabil.cod_tipo_conciliacao IS NOT NULL THEN 
                               conciliacao_lancamento_contabil.cod_tipo_conciliacao
                          WHEN conciliacao_lancamento_arrecadacao.cod_tipo_conciliacao IS NOT NULL THEN
                               conciliacao_lancamento_arrecadacao.cod_tipo_conciliacao
                          WHEN conciliacao_lancamento_arrecadacao_estornada.cod_tipo_conciliacao IS NOT NULL THEN
                               conciliacao_lancamento_arrecadacao_estornada.cod_tipo_conciliacao
                          WHEN conciliacao_lancamento_manual.cod_tipo_conciliacao IS NOT NULL THEN
                               conciliacao_lancamento_manual.cod_tipo_conciliacao
                     END AS cod_tipo_conciliacao
                FROM tesouraria.fn_conciliacao_movimentacao_corrente( '".$this->getDado('exercicio')."'
                                                                    , '".$this->getDado('inCodEntidade')."'
                                                                    , '".$this->getDado('stDtInicial')."'
                                                                    , '".$this->getDado('stDtFinal')."'
                                                                    , '".$this->getDado('stFiltro')."'
                                                                    , '".$this->getDado('stFiltroArrecadacao')."'
                                                                    , ".$this->getDado('inCodPlano')."
                                                                    , '".$this->getDado('inMes')."'
                                                                    ) AS retorno
                                                                    ( ordem                 VARCHAR,
                                                                      dt_lancamento         VARCHAR,
                                                                      dt_conciliacao        VARCHAR,
                                                                      descricao             VARCHAR,
                                                                      vl_lancamento         DECIMAL,
                                                                      vl_original           DECIMAL,
                                                                      tipo_valor            VARCHAR,
                                                                      conciliar             VARCHAR,
                                                                      cod_lote              INTEGER,
                                                                      tipo                  VARCHAR,
                                                                      sequencia             INTEGER,
                                                                      cod_entidade          INTEGER,
                                                                      tipo_movimentacao     VARCHAR,
                                                                      cod_plano             INTEGER,
                                                                      cod_arrecadacao       INTEGER,
                                                                      cod_receita           INTEGER,
                                                                      cod_bordero           INTEGER,
                                                                      timestamp_arrecadacao VARCHAR,
                                                                      timestamp_estornada   VARCHAR,
                                                                      tipo_arrecadacao      VARCHAR,
                                                                      mes                   VARCHAR,
                                                                      id                    VARCHAR,
                                                                      exercicio_conciliacao VARCHAR
                                                                    )

           LEFT JOIN tcmba.conciliacao_lancamento_contabil
                  ON conciliacao_lancamento_contabil.cod_plano = retorno.cod_plano
                 AND conciliacao_lancamento_contabil.exercicio = '".$this->getDado('exercicio')."'
                 AND conciliacao_lancamento_contabil.cod_lote = retorno.cod_lote
                 AND conciliacao_lancamento_contabil.tipo = retorno.tipo
                 AND conciliacao_lancamento_contabil.sequencia = retorno.sequencia
                 AND conciliacao_lancamento_contabil.cod_entidade = retorno.cod_entidade
                 AND conciliacao_lancamento_contabil.tipo_valor = retorno.tipo_valor

           LEFT JOIN tcmba.conciliacao_lancamento_arrecadacao
                  ON conciliacao_lancamento_arrecadacao.cod_plano = retorno.cod_plano
                 AND conciliacao_lancamento_arrecadacao.exercicio = '".$this->getDado('exercicio')."'
                 AND conciliacao_lancamento_arrecadacao.tipo = retorno.tipo
                 AND conciliacao_lancamento_arrecadacao.cod_arrecadacao = retorno.cod_arrecadacao
                 AND conciliacao_lancamento_arrecadacao.timestamp_arrecadacao::varchar = retorno.timestamp_arrecadacao

           LEFT JOIN tcmba.conciliacao_lancamento_arrecadacao_estornada
                  ON conciliacao_lancamento_arrecadacao_estornada.cod_plano = retorno.cod_plano
                 AND conciliacao_lancamento_arrecadacao_estornada.exercicio = '".$this->getDado('exercicio')."'
                 AND conciliacao_lancamento_arrecadacao_estornada.tipo = retorno.tipo
                 AND conciliacao_lancamento_arrecadacao_estornada.cod_arrecadacao = retorno.cod_arrecadacao
                 AND conciliacao_lancamento_arrecadacao_estornada.timestamp_arrecadacao::varchar = retorno.timestamp_arrecadacao
                 AND conciliacao_lancamento_arrecadacao_estornada.timestamp_estornada::varchar = retorno.timestamp_estornada

           LEFT JOIN tcmba.conciliacao_lancamento_manual
                  ON conciliacao_lancamento_manual.cod_plano = retorno.cod_plano
                 AND conciliacao_lancamento_manual.exercicio = '".$this->getDado('exercicio')."'
                 AND conciliacao_lancamento_manual.sequencia = retorno.sequencia
                 AND TO_CHAR(conciliacao_lancamento_manual.dt_lancamento, 'dd/mm/yyyy') = retorno.dt_lancamento
                 AND conciliacao_lancamento_manual.tipo_valor = retorno.tipo_valor
          ";

    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaMovimentacaoPendente(&$rsRecordSet, $stCondicao = "", $stOrder = "",  $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if ($stOrder != "") {
        if( !strstr( $stOrder, "ORDER BY" ) )
            $stOrder = " ORDER BY ".$stOrder;
    }
    //$stSql = $this->montaRecuperaMovimentacaoPendente().$stCondicao.$stOrder;
    $stSql = $this->montaRecuperaMovimentacaoPendente();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaMovimentacaoPendente()
{
    $stSql = "SELECT * FROM tesouraria.fn_conciliacao_movimentacao_pendente( '".$this->getDado('exercicio')."'
                                                                            ,'".$this->getDado('inCodEntidade')."'
                                                                            ,'".$this->getDado('stDtInicial')."'
                                                                            ,'".$this->getDado('stDtFinal')."'
                                                                            ,'".$this->getDado('stFiltro')."'
                                                                            ,'".$this->getDado('stFiltroArrecadacao')."'
                                                                            ,".$this->getDado('inCodPlano')."
                                                                            ,'".$this->getDado('inMes')."'
                                                                           ) AS
                                                                     retorno

               (
                      ordem                 VARCHAR,
                      dt_lancamento         VARCHAR,
                      dt_conciliacao        VARCHAR,
                      descricao             VARCHAR,
                      vl_lancamento         DECIMAL,
                      vl_original           DECIMAL,
                      tipo_valor            VARCHAR,
                      conciliar             VARCHAR,
                      cod_lote              INTEGER,
                      tipo                  VARCHAR,
                      sequencia             INTEGER,
                      cod_entidade          INTEGER,
                      tipo_movimentacao     VARCHAR,
                      cod_plano             INTEGER,
                      cod_arrecadacao       INTEGER,
                      cod_receita           INTEGER,
                      cod_bordero           INTEGER,
                      timestamp_arrecadacao VARCHAR,
                      timestamp_estornada   VARCHAR,
                      tipo_arrecadacao      VARCHAR,
                      mes                   VARCHAR,
                      id                    VARCHAR,
                      exercicio_conciliacao VARCHAR
               );";

    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaCabecalhoConciliacao(&$rsRecordSet, $stCondicao = "", $stOrder = "",  $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if ($stOrder != "") {
        if( !strstr( $stOrder, "ORDER BY" ) )
            $stOrder = " ORDER BY ".$stOrder;
    }
    $stSql = $this->montaRecuperaCabecalhoConciliacao().$stCondicao;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaCabecalhoConciliacao()
{
    $stSql   = "SELECT                                                    \n";
    $stSql  .= "    CO.cod_plano,                                         \n";
    $stSql  .= "    CONTA.nom_conta,                                      \n";
    $stSql  .= "    ENT.cod_entidade,                                     \n";
    $stSql  .= "    ENT.nom_cgm as nom_entidade,                          \n";
    $stSql  .= "    CO.exercicio,                                         \n";
    $stSql  .= "    CO.mes,                                               \n";
    $stSql  .= "    to_char(CO.dt_extrato,'dd/mm/yyyy') as dt_extrato,    \n";
    $stSql  .= "    CO.vl_extrato                                         \n";
    $stSql  .= "FROM                                                      \n";
    $stSql  .= "    tesouraria.conciliacao  as CO,                        \n";
    $stSql  .= "    contabilidade.plano_banco  as PB                      \n";
    $stSql  .= "        LEFT OUTER JOIN (                                 \n";
    $stSql  .= "            SELECT                                        \n";
    $stSql  .= "                CGM.nom_cgm,                              \n";
    $stSql  .= "                OE.cod_entidade,                          \n";
    $stSql  .= "                OE.exercicio                              \n";
    $stSql  .= "            FROM                                          \n";
    $stSql  .= "                orcamento.entidade  as OE,                \n";
    $stSql  .= "                sw_cgm              as CGM                \n";
    $stSql  .= "            WHERE                                         \n";
    $stSql  .= "                OE.numcgm = CGM.numcgm                    \n";
    $stSql  .= "        ) as ENT ON (                                     \n";
    $stSql  .= "            PB.cod_entidade = ENT.cod_entidade   AND      \n";
    $stSql  .= "            PB.exercicio    = ENT.exercicio               \n";
    $stSql  .= "        )                                                 \n";
    $stSql  .= "        LEFT OUTER JOIN (                                 \n";
    $stSql  .= "            SELECT                                        \n";
    $stSql  .= "                PC.nom_conta,                             \n";
    $stSql  .= "                PB.cod_plano,                             \n";
    $stSql  .= "                PB.exercicio                              \n";
    $stSql  .= "            FROM                                          \n";
    $stSql  .= "                contabilidade.plano_banco       as PB,    \n";
    $stSql  .= "                contabilidade.plano_analitica   as PA,    \n";
    $stSql  .= "                contabilidade.plano_conta       as PC     \n";
    $stSql  .= "            WHERE                                         \n";
    $stSql  .= "                PB.cod_plano    = PA.cod_plano  AND       \n";
    $stSql  .= "                PB.exercicio    = PA.exercicio  AND       \n";
    $stSql  .= "                                                          \n";
    $stSql  .= "                PA.cod_conta    = PC.cod_conta  AND       \n";
    $stSql  .= "                PA.exercicio    = PC.exercicio            \n";
    $stSql  .= "        ) as CONTA ON (                                   \n";
    $stSql  .= "            PB.cod_plano    = CONTA.cod_plano   AND       \n";
    $stSql  .= "            PB.exercicio    = CONTA.exercicio             \n";
    $stSql  .= "        )                                                 \n";
    $stSql  .= "WHERE                                                     \n";
    $stSql  .= "    CO.cod_plano    = PB.cod_plano  AND                   \n";
    $stSql  .= "    CO.exercicio    = PB.exercicio  AND                   \n";

    $stSql  .= "    CO.cod_plano    = ".$this->getDado("cod_plano")."  AND \n";
    $stSql  .= "    CO.exercicio    = '".$this->getDado("exercicio")."'  AND \n";
    $stSql  .= "    CO.mes          = ".$this->getDado("mes")."            \n";

    return $stSql;

}

}
