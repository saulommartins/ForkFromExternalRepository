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
    * Extensão da Classe de mapeamento
    * Data de Criação: 13/06/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTCMBARegulariza.class.php 65776 2016-06-16 19:43:13Z michel $

    * Casos de uso: uc-06.03.00
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCMBARegulariza extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function __construct()
{
    parent::Persistente();

    $this->setDado('exercicio', Sessao::getExercicio() );
}

function recuperaRegistro(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaRegistro().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRegistro()
{
    $stSql = "";

    list($inDiaIni, $inMesIni, $inAnoIni) = explode('/',$this->getDado('data_inicial'));

    if($inMesIni > 1){
        $stSql .= "
             --NECESSARIO GERAR MES ANTERIOR PARA GERAR COD_CONCILIACAO(SEQUENCIAL) E VALOR_CONCILIACAO
             SELECT 1
               FROM tcmba.fn_conciliacao_movimentacao_corrente( '".$this->getDado('exercicio')."'
                                                              , '".$this->getDado('entidades')."'
                                                              , TO_CHAR((TO_DATE('".$this->getDado('data_inicial')."','DD/MM/YYYY') - interval '1 month'),'DD/MM/YYYY')
                                                              , TO_CHAR((TO_DATE('".$this->getDado('data_inicial')."','DD/MM/YYYY')-1),'DD/MM/YYYY')
                                                              ) AS conciliacao
                                                              ( exercicio                VARCHAR,
                                                                competencia              TEXT,
                                                                cod_estrutural           VARCHAR,
                                                                cod_tipo_conciliacao     INTEGER,
                                                                descricao                TEXT,
                                                                dt_extrato               DATE,
                                                                timestamp                TIMESTAMP,
                                                                vl_lancamento            NUMERIC,
                                                                cod_tipo_pagamento       INTEGER,
                                                                num_documento            VARCHAR,
                                                                cod_plano                INTEGER,
                                                                cod_conciliacao          INTEGER
                                                              )
              LIMIT 1;
        ";
    }

    $stSql .= "
             SELECT 1 AS tipo_registro
                  , conciliacao.exercicio
                  , conciliacao.mes
                  , '".$this->getDado('unidade_gestora')."' AS unidade_gestora
                  , REPLACE(plano_conta.cod_estrutural,'.','') AS cod_estrutural
                  , conciliacao_regulariza.exercicio||LPAD(conciliacao_regulariza.mes::VARCHAR,2, '0') AS competencia_conciliacao
                  , 0 AS reservado
                  , regulariza.cod_tipo_conciliacao
                  , TO_CHAR(conciliacao_regulariza.timestamp,'dd/mm/yyyy') AS data_conciliacao
                  , TO_CHAR(conciliacao.timestamp,'dd/mm/yyyy') AS data_regularizacao
                  , arquivo_concilia.cod_conciliacao
                  , conciliacao.exercicio||LPAD(conciliacao.mes::VARCHAR,2, '0') AS competencia
                  , sem_acentos(arquivo_concilia.descricao) AS descricao
                  , arquivo_concilia.valor AS vl_lancamento

               FROM tcmba.conciliacao_lancamento_contabil AS regulariza

         INNER JOIN tesouraria.conciliacao_lancamento_contabil
                 ON conciliacao_lancamento_contabil.cod_plano    = regulariza.cod_plano
                AND conciliacao_lancamento_contabil.exercicio    = regulariza.exercicio    
                AND conciliacao_lancamento_contabil.cod_lote     = regulariza.cod_lote
                AND conciliacao_lancamento_contabil.tipo         = regulariza.tipo
                AND conciliacao_lancamento_contabil.sequencia    = regulariza.sequencia
                AND conciliacao_lancamento_contabil.cod_entidade = regulariza.cod_entidade
                AND conciliacao_lancamento_contabil.tipo_valor   = regulariza.tipo_valor

         INNER JOIN tesouraria.conciliacao AS conciliacao_regulariza
                 ON regulariza.cod_plano             = conciliacao_regulariza.cod_plano
                AND regulariza.exercicio_conciliacao = conciliacao_regulariza.exercicio
                AND regulariza.mes                   = conciliacao_regulariza.mes

         INNER JOIN tesouraria.conciliacao
                 ON conciliacao_lancamento_contabil.cod_plano             = conciliacao.cod_plano
                AND conciliacao_lancamento_contabil.exercicio_conciliacao = conciliacao.exercicio
                AND conciliacao_lancamento_contabil.mes                   = conciliacao.mes

         INNER JOIN contabilidade.plano_analitica
                 ON conciliacao_regulariza.cod_plano = plano_analitica.cod_plano
                AND conciliacao_regulariza.exercicio = plano_analitica.exercicio

         INNER JOIN contabilidade.plano_conta
                 ON plano_analitica.cod_conta = plano_conta.cod_conta
                AND plano_analitica.exercicio = plano_conta.exercicio

         INNER JOIN tcmba.arquivo_concilia
                 ON arquivo_concilia.exercicio         = regulariza.exercicio_conciliacao
                AND arquivo_concilia.mes               = regulariza.mes
                AND arquivo_concilia.chave_conciliacao = (regulariza.exercicio_conciliacao
                                                         ||regulariza.mes
                                                         ||regulariza.cod_lote
                                                         ||regulariza.tipo
                                                         ||regulariza.sequencia
                                                         ||regulariza.cod_entidade
                                                         ||regulariza.tipo_valor
                                                         ||regulariza.cod_plano
                                                         )

              WHERE conciliacao.exercicio = '".$this->getDado('exercicio')."'
                AND regulariza.cod_entidade IN ( ".$this->getDado('entidades')." )
                AND conciliacao.mes BETWEEN SPLIT_PART('".$this->getDado('data_inicial')."','/',2)::INTEGER
                                        AND SPLIT_PART('".$this->getDado('data_final')."','/',2)::INTEGER

              UNION

             SELECT 1 AS tipo_registro
                  , conciliacao.exercicio
                  , conciliacao.mes
                  , '".$this->getDado('unidade_gestora')."' AS unidade_gestora
                  , REPLACE(plano_conta.cod_estrutural,'.','') AS cod_estrutural
                  , conciliacao_regulariza.exercicio||LPAD(conciliacao_regulariza.mes::VARCHAR,2, '0') AS competencia_conciliacao
                  , 0 AS reservado
                  , regulariza.cod_tipo_conciliacao
                  , TO_CHAR(conciliacao_regulariza.timestamp,'dd/mm/yyyy') AS data_conciliacao
                  , TO_CHAR(conciliacao.timestamp,'dd/mm/yyyy') AS data_regularizacao
                  , arquivo_concilia.cod_conciliacao
                  , conciliacao.exercicio||LPAD(conciliacao.mes::VARCHAR,2, '0') AS competencia
                  , sem_acentos(arquivo_concilia.descricao) AS descricao
                  , arquivo_concilia.valor AS vl_lancamento

               FROM tcmba.conciliacao_lancamento_arrecadacao AS regulariza

         INNER JOIN tesouraria.conciliacao_lancamento_arrecadacao
                 ON conciliacao_lancamento_arrecadacao.cod_plano             = regulariza.cod_plano
                AND conciliacao_lancamento_arrecadacao.exercicio             = regulariza.exercicio    
                AND conciliacao_lancamento_arrecadacao.cod_arrecadacao       = regulariza.cod_arrecadacao
                AND conciliacao_lancamento_arrecadacao.timestamp_arrecadacao = regulariza.timestamp_arrecadacao
                AND conciliacao_lancamento_arrecadacao.tipo                  = regulariza.tipo

         INNER JOIN tesouraria.conciliacao AS conciliacao_regulariza
                 ON regulariza.cod_plano             = conciliacao_regulariza.cod_plano
                AND regulariza.exercicio_conciliacao = conciliacao_regulariza.exercicio
                AND regulariza.mes                   = conciliacao_regulariza.mes

         INNER JOIN tesouraria.conciliacao
                 ON conciliacao_lancamento_arrecadacao.cod_plano             = conciliacao.cod_plano
                AND conciliacao_lancamento_arrecadacao.exercicio_conciliacao = conciliacao.exercicio
                AND conciliacao_lancamento_arrecadacao.mes                   = conciliacao.mes

         INNER JOIN contabilidade.plano_analitica
                 ON conciliacao_regulariza.cod_plano = plano_analitica.cod_plano
                AND conciliacao_regulariza.exercicio = plano_analitica.exercicio

         INNER JOIN contabilidade.plano_conta
                 ON plano_analitica.cod_conta = plano_conta.cod_conta
                AND plano_analitica.exercicio = plano_conta.exercicio

         INNER JOIN tcmba.arquivo_concilia
                 ON arquivo_concilia.exercicio         = regulariza.exercicio_conciliacao
                AND arquivo_concilia.mes               = regulariza.mes
                AND arquivo_concilia.chave_conciliacao = (regulariza.exercicio
                                                         ||regulariza.mes
                                                         ||regulariza.cod_arrecadacao
                                                         ||REPLACE(REPLACE(REPLACE(REPLACE(TRIM(regulariza.timestamp_arrecadacao::TEXT), '.',''), ':',''), '-',''), ' ','')
                                                         )

              WHERE conciliacao.exercicio = '".$this->getDado('exercicio')."'
                AND conciliacao.mes BETWEEN SPLIT_PART('".$this->getDado('data_inicial')."','/',2)::INTEGER
                                        AND SPLIT_PART('".$this->getDado('data_final')."','/',2)::INTEGER

              UNION

             SELECT 1 AS tipo_registro
                  , conciliacao.exercicio
                  , conciliacao.mes
                  , '".$this->getDado('unidade_gestora')."' AS unidade_gestora
                  , REPLACE(plano_conta.cod_estrutural,'.','') AS cod_estrutural
                  , conciliacao_regulariza.exercicio||LPAD(conciliacao_regulariza.mes::VARCHAR,2, '0') AS competencia_conciliacao
                  , 0 AS reservado
                  , regulariza.cod_tipo_conciliacao
                  , TO_CHAR(conciliacao_regulariza.timestamp,'dd/mm/yyyy') AS data_conciliacao
                  , TO_CHAR(conciliacao.timestamp,'dd/mm/yyyy') AS data_regularizacao
                  , arquivo_concilia.cod_conciliacao
                  , conciliacao.exercicio||LPAD(conciliacao.mes::VARCHAR,2, '0') AS competencia
                  , sem_acentos(arquivo_concilia.descricao) AS descricao
                  , arquivo_concilia.valor AS vl_lancamento

               FROM tcmba.conciliacao_lancamento_arrecadacao_estornada AS regulariza

         INNER JOIN tesouraria.conciliacao_lancamento_arrecadacao_estornada
                 ON conciliacao_lancamento_arrecadacao_estornada.cod_plano             = regulariza.cod_plano
                AND conciliacao_lancamento_arrecadacao_estornada.exercicio             = regulariza.exercicio    
                AND conciliacao_lancamento_arrecadacao_estornada.cod_arrecadacao       = regulariza.cod_arrecadacao
                AND conciliacao_lancamento_arrecadacao_estornada.timestamp_arrecadacao = regulariza.timestamp_arrecadacao
                AND conciliacao_lancamento_arrecadacao_estornada.tipo                  = regulariza.tipo

         INNER JOIN tesouraria.conciliacao AS conciliacao_regulariza
                 ON regulariza.cod_plano             = conciliacao_regulariza.cod_plano
                AND regulariza.exercicio_conciliacao = conciliacao_regulariza.exercicio
                AND regulariza.mes                   = conciliacao_regulariza.mes

         INNER JOIN tesouraria.conciliacao
                 ON conciliacao_lancamento_arrecadacao_estornada.cod_plano             = conciliacao.cod_plano
                AND conciliacao_lancamento_arrecadacao_estornada.exercicio_conciliacao = conciliacao.exercicio
                AND conciliacao_lancamento_arrecadacao_estornada.mes                   = conciliacao.mes

         INNER JOIN contabilidade.plano_analitica
                 ON conciliacao_regulariza.cod_plano = plano_analitica.cod_plano
                AND conciliacao_regulariza.exercicio = plano_analitica.exercicio

         INNER JOIN contabilidade.plano_conta
                 ON plano_analitica.cod_conta = plano_conta.cod_conta
                AND plano_analitica.exercicio = plano_conta.exercicio

         INNER JOIN tcmba.arquivo_concilia
                 ON arquivo_concilia.exercicio         = regulariza.exercicio_conciliacao
                AND arquivo_concilia.mes               = regulariza.mes
                AND arquivo_concilia.chave_conciliacao = (regulariza.exercicio
                                                         ||regulariza.mes
                                                         ||regulariza.cod_arrecadacao
                                                         ||REPLACE(REPLACE(REPLACE(REPLACE(TRIM(regulariza.timestamp_arrecadacao::TEXT), '.',''), ':',''), '-',''), ' ','')
                                                         ||REPLACE(REPLACE(REPLACE(REPLACE(TRIM(regulariza.timestamp_estornada::TEXT), '.',''), ':',''), '-',''), ' ','')
                                                         )

              WHERE conciliacao.exercicio = '".$this->getDado('exercicio')."'
                AND conciliacao.mes BETWEEN SPLIT_PART('".$this->getDado('data_inicial')."','/',2)::INTEGER
                                        AND SPLIT_PART('".$this->getDado('data_final')."','/',2)::INTEGER

              UNION

             SELECT 1 AS tipo_registro
                  , conciliacao.exercicio
                  , conciliacao.mes
                  , '".$this->getDado('unidade_gestora')."' AS unidade_gestora
                  , REPLACE(plano_conta.cod_estrutural,'.','') AS cod_estrutural
                  , conciliacao_regulariza.exercicio||LPAD(conciliacao_regulariza.mes::VARCHAR,2, '0') AS competencia_conciliacao
                  , 0 AS reservado
                  , regulariza.cod_tipo_conciliacao
                  , TO_CHAR(conciliacao_regulariza.timestamp,'dd/mm/yyyy') AS data_conciliacao
                  , TO_CHAR(conciliacao.timestamp,'dd/mm/yyyy') AS data_regularizacao
                  , arquivo_concilia.cod_conciliacao
                  , conciliacao.exercicio||TO_CHAR(conciliacao_lancamento_manual.dt_conciliacao,'mm') AS competencia
                  , sem_acentos(arquivo_concilia.descricao) AS descricao
                  , arquivo_concilia.valor AS vl_lancamento

               FROM tcmba.conciliacao_lancamento_manual AS regulariza

         INNER JOIN tesouraria.conciliacao_lancamento_manual
                 ON conciliacao_lancamento_manual.cod_plano     = regulariza.cod_plano
                AND conciliacao_lancamento_manual.exercicio     = regulariza.exercicio
                AND conciliacao_lancamento_manual.mes           = regulariza.mes
                AND conciliacao_lancamento_manual.sequencia     = regulariza.sequencia
                AND conciliacao_lancamento_manual.dt_lancamento = regulariza.dt_lancamento
                AND conciliacao_lancamento_manual.tipo_valor    = regulariza.tipo_valor
                AND conciliacao_lancamento_manual.vl_lancamento = regulariza.vl_lancamento
                AND conciliacao_lancamento_manual.descricao     = regulariza.descricao

         INNER JOIN tesouraria.conciliacao AS conciliacao_regulariza
                 ON regulariza.cod_plano = conciliacao_regulariza.cod_plano
                AND regulariza.exercicio = conciliacao_regulariza.exercicio
                AND regulariza.mes       = conciliacao_regulariza.mes

         INNER JOIN tesouraria.conciliacao
                 ON conciliacao_lancamento_manual.cod_plano = conciliacao.cod_plano
                AND conciliacao_lancamento_manual.exercicio = conciliacao.exercicio
                AND conciliacao_lancamento_manual.mes       = conciliacao.mes

         INNER JOIN contabilidade.plano_analitica
                 ON conciliacao_regulariza.cod_plano = plano_analitica.cod_plano
                AND conciliacao_regulariza.exercicio = plano_analitica.exercicio

         INNER JOIN contabilidade.plano_conta
                 ON plano_analitica.cod_conta = plano_conta.cod_conta
                AND plano_analitica.exercicio = plano_conta.exercicio

         INNER JOIN tcmba.arquivo_concilia
                 ON arquivo_concilia.exercicio         = regulariza.exercicio
                AND arquivo_concilia.mes               = regulariza.mes
                AND arquivo_concilia.chave_conciliacao = (regulariza.exercicio
                                                         ||regulariza.mes
                                                         ||regulariza.sequencia
                                                         ||'M'
                                                         ||regulariza.cod_plano
                                                         ||REPLACE(REPLACE(REPLACE(REPLACE(TRIM(conciliacao_regulariza.timestamp::TEXT), '.',''), ':',''), '-',''), ' ','')
                                                         )

              WHERE conciliacao.exercicio = '".$this->getDado('exercicio')."'
                AND conciliacao_lancamento_manual.dt_conciliacao BETWEEN TO_DATE('".$this->getDado('data_inicial')."','dd/mm/yyyy')
                                                                     AND TO_DATE('".$this->getDado('data_final')."','dd/mm/yyyy')
                AND NOT regulariza.conciliado
                AND conciliacao_lancamento_manual.conciliado
    ";

    return $stSql;
}

}
