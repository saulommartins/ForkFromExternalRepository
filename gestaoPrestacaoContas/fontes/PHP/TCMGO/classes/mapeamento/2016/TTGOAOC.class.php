
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
/**
    * Classe de mapeamento da tabela compras.compra_direta
    * Data de Criação:  27/04/2007
    
    
    * @author Analista: Gelson
    * @author Desenvolvedor: Vitor Hugo
    
    * @package URBEM
    * @subpackage Mapeamento
    
    $Id: TTGOAOC.class.php 65190 2016-04-29 19:36:51Z michel $
    
    * Casos de uso: uc-06.04.00
*/

include_once "../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php";
include_once CLA_PERSISTENTE;

class TTGOAOC extends Persistente{

    /**
    * Método Construtor
    * @access Private
    */
    
    function __construct(){
        parent::Persistente();
        $this->setDado('exercicio', Sessao::getExercicio() );
    }

    function recuperaAlteracoesOrcamentarias(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao=""){
        return $this->executaRecupera("montaRecuperaAlteracoesOrcamentarias",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    function montaRecuperaAlteracoesOrcamentarias(){
    $stSql = "
    SELECT
       '10' AS tipo_registro
        ,programa.num_programa
        ,despesa.num_orgao
        ,despesa.num_unidade
        ,despesa.cod_funcao
        ,despesa.cod_subfuncao
        ,substr(acao.num_acao::varchar,1,1) AS nat_acao
        ,substr(acao.num_acao::varchar,2,3) AS num_seqproj
        ,ROUND(SUM(( SELECT
                   ( COALESCE(SUM(odespesa.vl_original),0)
                     +
                     COALESCE(SUM(osuplementacao.valor),0)
                     -
                     COALESCE(SUM(oreducao.valor),0) 
                     -
                     COALESCE(SUM(oempenho.vl_empenho),0)
                     +
                     COALESCE(SUM(oempenho.vl_empenho_anulado),0)
                   )
               FROM
                   orcamento.despesa AS odespesa
               LEFT JOIN  (  SELECT
                                    suplementacao.dt_suplementacao
                                  , suplementacao_suplementada.cod_despesa
                                  , SUM(suplementacao_suplementada.valor) AS valor
                                  , suplementacao_suplementada.exercicio
                               FROM orcamento.suplementacao
                         INNER JOIN orcamento.suplementacao_suplementada
                                 ON suplementacao_suplementada.cod_suplementacao =
                                    suplementacao.cod_suplementacao  AND
                                    suplementacao_suplementada.exercicio = suplementacao.exercicio
                              WHERE NOT EXISTS
                                    (
                                        SELECT 1
                                          FROM orcamento.suplementacao_anulada
                                         WHERE suplementacao_anulada.exercicio                  = suplementacao.exercicio   
                                           AND suplementacao_anulada.cod_suplementacao_anulacao = suplementacao.cod_suplementacao
                                    )
                                AND NOT EXISTS
                                    (
                                        SELECT 1
                                          FROM orcamento.suplementacao_anulada
                                         WHERE suplementacao_anulada.exercicio         = suplementacao.exercicio   
                                           AND suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao
                                    )
                              GROUP BY
                                  dt_suplementacao
                                 ,cod_despesa
                                 ,suplementacao_suplementada.exercicio
                          )   AS osuplementacao
                              ON (
                                     osuplementacao.cod_despesa = odespesa.cod_despesa  AND
                                     osuplementacao.exercicio = odespesa.exercicio      AND
                                     osuplementacao.dt_suplementacao BETWEEN TO_DATE('01/01/".$this->getDado('exercicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') 
                                 )
               LEFT JOIN  (  SELECT  suplementacao.dt_suplementacao
                                    ,suplementacao_reducao.cod_despesa
                                    ,SUM(suplementacao_reducao.valor) AS valor
                                    ,suplementacao_reducao.exercicio
                             FROM  orcamento.suplementacao
                             INNER JOIN  orcamento.suplementacao_reducao
                                 ON  suplementacao_reducao.cod_suplementacao =
                                     suplementacao.cod_suplementacao       AND
                                     suplementacao_reducao.exercicio  = suplementacao.exercicio
                              WHERE NOT EXISTS
                                    (
                                        SELECT 1
                                          FROM orcamento.suplementacao_anulada
                                         WHERE suplementacao_anulada.exercicio                  = suplementacao.exercicio   
                                           AND suplementacao_anulada.cod_suplementacao_anulacao = suplementacao.cod_suplementacao
                                    )
                                AND NOT EXISTS
                                    (
                                        SELECT 1
                                          FROM orcamento.suplementacao_anulada
                                         WHERE suplementacao_anulada.exercicio         = suplementacao.exercicio   
                                           AND suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao
                                    )

                             GROUP BY
                                dt_suplementacao
                               ,cod_despesa
                               ,suplementacao_reducao.exercicio
                          )  AS  oreducao
                             ON  oreducao.cod_despesa = odespesa.cod_despesa  AND
                                 oreducao.exercicio = odespesa.exercicio      AND
                                 oreducao.dt_suplementacao BETWEEN TO_DATE('01/01/".$this->getDado('exercicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') 
               LEFT JOIN  (  SELECT  SUM(vl_total) AS vl_empenho
                                    ,SUM(vl_anulado) AS vl_empenho_anulado
                                    ,pre_empenho_despesa.cod_despesa
                                    ,pre_empenho_despesa.exercicio
                             FROM  empenho.pre_empenho_despesa
                            INNER JOIN  empenho.pre_empenho
                                    ON  pre_empenho.exercicio = pre_empenho_despesa.exercicio
                                   AND  pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
                            INNER JOIN  empenho.item_pre_empenho
                                    ON  item_pre_empenho.exercicio = pre_empenho.exercicio
                                   AND  item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                             LEFT JOIN  empenho.empenho_anulado_item
                                    ON  empenho_anulado_item.exercicio = item_pre_empenho.exercicio
                                   AND  empenho_anulado_item.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                            INNER JOIN  empenho.empenho
                                    ON  empenho.exercicio = pre_empenho.exercicio
                                   AND  empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                                 WHERE  empenho.dt_empenho BETWEEN TO_DATE('01/01/".$this->getDado('exercicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy')
                              GROUP BY  pre_empenho_despesa.exercicio, pre_empenho_despesa.cod_despesa
                          ) AS oempenho
                            ON oempenho.cod_despesa = odespesa.cod_despesa  AND
                               oempenho.exercicio = odespesa.exercicio
               WHERE  odespesa.exercicio = despesa.exercicio   AND
                      odespesa.cod_despesa = despesa.cod_despesa
                )),2)   AS  vl_saldo_anterior

         
        ,ROUND(SUM(( SELECT
                   ( COALESCE(SUM(odespesa.vl_original),0) + COALESCE(SUM(osuplementacao.valor),0) -
                     COALESCE(SUM(oreducao.valor),0) - COALESCE(SUM(oempenho.vl_empenho),0) +
                     COALESCE(SUM(oempenho.vl_empenho_anulado),0)
                   )
               FROM
                   orcamento.despesa AS odespesa
               LEFT JOIN  (  SELECT
                                 suplementacao.dt_suplementacao
                                ,suplementacao_suplementada.cod_despesa
                                ,SUM(suplementacao_suplementada.valor) AS valor
                                ,suplementacao_suplementada.exercicio
                             FROM  orcamento.suplementacao
                             INNER JOIN  orcamento.suplementacao_suplementada
                                 ON  suplementacao_suplementada.cod_suplementacao =
                                     suplementacao.cod_suplementacao  AND
                                     suplementacao_suplementada.exercicio = suplementacao.exercicio
                              WHERE NOT EXISTS
                                    (
                                        SELECT 1
                                          FROM orcamento.suplementacao_anulada
                                         WHERE suplementacao_anulada.exercicio                  = suplementacao.exercicio   
                                           AND suplementacao_anulada.cod_suplementacao_anulacao = suplementacao.cod_suplementacao
                                    )
                                AND NOT EXISTS
                                    (
                                        SELECT 1
                                          FROM orcamento.suplementacao_anulada
                                         WHERE suplementacao_anulada.exercicio         = suplementacao.exercicio   
                                           AND suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao
                                    )
                              GROUP BY
                                  dt_suplementacao
                                 ,cod_despesa
                                 ,suplementacao_suplementada.exercicio
                          )   AS osuplementacao
                              ON (
                                     osuplementacao.cod_despesa = odespesa.cod_despesa  AND
                                     osuplementacao.exercicio = odespesa.exercicio      AND
                                     osuplementacao.dt_suplementacao BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                                   
                                 )
               LEFT JOIN  (  SELECT  suplementacao.dt_suplementacao
                                    ,suplementacao_reducao.cod_despesa
                                    ,SUM(suplementacao_reducao.valor) AS valor
                                    ,suplementacao_reducao.exercicio
                             FROM  orcamento.suplementacao
                             INNER JOIN  orcamento.suplementacao_reducao
                                 ON  suplementacao_reducao.cod_suplementacao =
                                     suplementacao.cod_suplementacao       AND
                                     suplementacao_reducao.exercicio  = suplementacao.exercicio
                              WHERE NOT EXISTS
                                    (
                                        SELECT 1
                                          FROM orcamento.suplementacao_anulada
                                         WHERE suplementacao_anulada.exercicio                  = suplementacao.exercicio   
                                           AND suplementacao_anulada.cod_suplementacao_anulacao = suplementacao.cod_suplementacao
                                    )
                                AND NOT EXISTS
                                    (
                                        SELECT 1
                                          FROM orcamento.suplementacao_anulada
                                         WHERE suplementacao_anulada.exercicio         = suplementacao.exercicio   
                                           AND suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao
                                    )
                             GROUP BY
                                dt_suplementacao
                               ,cod_despesa
                               ,suplementacao_reducao.exercicio
                          )  AS  oreducao
                             ON  oreducao.cod_despesa = odespesa.cod_despesa  AND
                                 oreducao.exercicio = odespesa.exercicio      AND
                                 oreducao.dt_suplementacao BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')

               LEFT JOIN  (  SELECT  SUM(vl_total) AS vl_empenho
                                    ,SUM(vl_anulado) AS vl_empenho_anulado
                                    ,pre_empenho_despesa.cod_despesa
                                    ,pre_empenho_despesa.exercicio
                             FROM  empenho.pre_empenho_despesa
                            INNER JOIN  empenho.pre_empenho
                                    ON  pre_empenho.exercicio = pre_empenho_despesa.exercicio
                                   AND  pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
                            INNER JOIN  empenho.item_pre_empenho
                                    ON  item_pre_empenho.exercicio = pre_empenho.exercicio
                                   AND  item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                             LEFT JOIN  empenho.empenho_anulado_item
                                    ON  empenho_anulado_item.exercicio = item_pre_empenho.exercicio
                                   AND  empenho_anulado_item.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                            INNER JOIN  empenho.empenho
                                    ON  empenho.exercicio = pre_empenho.exercicio
                                   AND  empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                                 WHERE  empenho.dt_empenho BETWEEN TO_DATE('01/01/".$this->getDado('exercicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy')
                              GROUP BY  pre_empenho_despesa.exercicio, pre_empenho_despesa.cod_despesa
                          ) AS oempenho
                            ON oempenho.cod_despesa = odespesa.cod_despesa  AND
                               oempenho.exercicio = odespesa.exercicio
               WHERE  odespesa.exercicio = despesa.exercicio   AND
                      odespesa.cod_despesa = despesa.cod_despesa
                )),2)   AS  vl_saldo_atual
              , '' AS brancos
              , '0' AS nro_sequencial

    FROM
        orcamento.despesa
   JOIN (   
            SELECT suplementacao.exercicio
                 , suplementacao_suplementada.cod_despesa
                 , SUM(COALESCE(suplementacao_suplementada.valor,0)) AS valor
              FROM orcamento.suplementacao 
        INNER JOIN orcamento.suplementacao_suplementada
                ON suplementacao_suplementada.exercicio = suplementacao.exercicio
               AND suplementacao_suplementada.cod_suplementacao = suplementacao.cod_suplementacao
             WHERE suplementacao.exercicio = '".$this->getDado('exercicio')."'
               AND suplementacao.dt_suplementacao BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
               AND NOT EXISTS
                   (
                       SELECT 1
                         FROM orcamento.suplementacao_anulada
                        WHERE suplementacao_anulada.exercicio                  = suplementacao.exercicio   
                          AND suplementacao_anulada.cod_suplementacao_anulacao = suplementacao.cod_suplementacao
                   )
                AND NOT EXISTS
                    (
                        SELECT 1
                          FROM orcamento.suplementacao_anulada
                         WHERE suplementacao_anulada.exercicio         = suplementacao.exercicio   
                           AND suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao
                    )
          GROUP BY suplementacao.exercicio
                 , suplementacao_suplementada.cod_despesa
            
             UNION
        
            SELECT suplementacao.exercicio
                 , suplementacao_reducao.cod_despesa
                 , SUM(COALESCE(suplementacao_reducao.valor,0)) AS valor
              FROM orcamento.suplementacao 
        INNER JOIN orcamento.suplementacao_reducao
                ON suplementacao_reducao.exercicio = suplementacao.exercicio
               AND suplementacao_reducao.cod_suplementacao = suplementacao.cod_suplementacao
             WHERE suplementacao.exercicio = '".$this->getDado('exercicio')."'
               AND suplementacao.dt_suplementacao BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
               AND NOT EXISTS
                   (
                       SELECT 1
                         FROM orcamento.suplementacao_anulada
                        WHERE suplementacao_anulada.exercicio                  = suplementacao.exercicio   
                          AND suplementacao_anulada.cod_suplementacao_anulacao = suplementacao.cod_suplementacao
                   )
                AND NOT EXISTS
                    (
                        SELECT 1
                          FROM orcamento.suplementacao_anulada
                         WHERE suplementacao_anulada.exercicio         = suplementacao.exercicio   
                           AND suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao
                    )
          GROUP BY suplementacao.exercicio
                 , suplementacao_reducao.cod_despesa

         ) AS supl ON
             supl.exercicio   = orcamento.despesa.exercicio AND
             supl.cod_despesa = orcamento.despesa.cod_despesa
        
        JOIN orcamento.despesa_acao
          ON despesa_acao.exercicio_despesa = despesa.exercicio
         AND despesa_acao.cod_despesa = despesa.cod_despesa
        JOIN ppa.acao
          ON acao.cod_acao = despesa_acao.cod_acao
        JOIN ppa.programa
          ON programa.cod_programa = despesa.cod_programa
    WHERE
        despesa.exercicio = '".$this->getDado('exercicio')."' AND
        despesa.cod_entidade IN (".$this->getDado('stEntidades').")
    GROUP BY
        programa.num_programa
       ,despesa.num_orgao
       ,despesa.num_unidade
       ,despesa.cod_funcao
       ,despesa.cod_subfuncao
       ,despesa.num_pao
       ,acao.num_acao
    ";
        return $stSql;
    }

    function recuperaAlteracoesOrcamentariasPorElementoDespesa(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao=""){
        return $this->executaRecupera("montaRecuperaAlteracoesOrcamentariasPorElementoDespesa",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    function montaRecuperaAlteracoesOrcamentariasPorElementoDespesa(){
    $stSql = "
    SELECT
       '11' AS tipo_registro
        ,programa.num_programa
        ,despesa.num_orgao
        ,despesa.num_unidade
        ,despesa.cod_funcao
        ,despesa.cod_subfuncao
        ,substr(acao.num_acao::varchar,1,1) AS nat_acao
        ,substr(acao.num_acao::varchar,2,3) AS num_seqproj
        ,substr(replace(orcamento.conta_despesa.cod_estrutural,'.',''),1,6) AS cod_estrutural
        ,supl.dt_suplementacao
        ,supl.cod_suplementacao
        --Tipo de Alteração Orçamentária:   SIMWEB      TCM-GO
        ,CASE WHEN (supl.cod_tipo = 5  )  THEN '01'
              WHEN (supl.cod_tipo = 4  )  THEN '02'
              WHEN (supl.cod_tipo = 1 AND supl.reducao = true )  THEN '09'
              WHEN (supl.cod_tipo = 1 AND supl.reducao = false )  THEN '03'
              WHEN (supl.cod_tipo = 2  )  THEN '04'
              WHEN (supl.cod_tipo = 10 )  THEN '05'
              WHEN (supl.cod_tipo = 9  )  THEN '06'
              WHEN (supl.cod_tipo = 6  )  THEN '07'
              WHEN (supl.cod_tipo = 7  )  THEN '08'
              WHEN (supl.cod_tipo = 11 )  THEN '11'
              WHEN (supl.cod_tipo = 999 ) THEN '09'
              ELSE '99'
          END AS tipo_alteracao
        ,supl.cod_tipo
        ,supl.valor AS vl_alteracao
        ,despesa_anterior.saldo_anterior AS  vl_saldo_anterior        
        ,CASE WHEN (supl.cod_tipo = 1 AND supl.reducao = true ) THEN 
                    despesa_anterior.saldo_anterior - supl.valor   
              WHEN (supl.cod_tipo = 1 AND supl.reducao = false ) THEN
                    despesa_anterior.saldo_anterior + supl.valor   
         END AS  vl_saldo_atual
        , '' AS brancos
        , '0' AS nro_sequencial2

    FROM
        orcamento.conta_despesa,
        orcamento.despesa
   JOIN (   
            SELECT suplementacao.exercicio
                 , suplementacao_suplementada.cod_despesa
                 , SUM(COALESCE(suplementacao_suplementada.valor,0)) AS valor
                 , TO_CHAR(suplementacao.dt_suplementacao,'dd/mm/yyyy') as dt_suplementacao
                 , suplementacao.cod_suplementacao
                 , suplementacao.cod_tipo
                 , false AS reducao
              FROM orcamento.suplementacao 
        INNER JOIN orcamento.suplementacao_suplementada
                ON suplementacao_suplementada.exercicio = suplementacao.exercicio
               AND suplementacao_suplementada.cod_suplementacao = suplementacao.cod_suplementacao
             WHERE suplementacao.exercicio = '".$this->getDado('exercicio')."'
               AND suplementacao.dt_suplementacao BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                AND NOT EXISTS
                    (
                        SELECT 1
                          FROM orcamento.suplementacao_anulada
                         WHERE suplementacao_anulada.exercicio         = suplementacao.exercicio   
                           AND suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao
                    )
               AND NOT EXISTS
                   (
                        SELECT  1
                          FROM  orcamento.suplementacao_anulada
                         WHERE  suplementacao_anulada.cod_suplementacao_anulacao = suplementacao.cod_suplementacao
                           AND  suplementacao_anulada.exercicio                  = suplementacao.exercicio
                   )
          GROUP BY suplementacao.exercicio
                 , suplementacao_suplementada.cod_despesa
                 , suplementacao.dt_suplementacao
                 , suplementacao.cod_suplementacao
                 , suplementacao.cod_tipo
            
             UNION
        
            SELECT suplementacao.exercicio
                 , suplementacao_reducao.cod_despesa
                 , SUM(COALESCE(suplementacao_reducao.valor,0)) AS valor
                 , TO_CHAR(suplementacao.dt_suplementacao,'dd/mm/yyyy') as dt_suplementacao
                 , suplementacao.cod_suplementacao
                 , suplementacao.cod_tipo
                 , true AS reducao
              FROM orcamento.suplementacao 
        INNER JOIN orcamento.suplementacao_reducao
                ON suplementacao_reducao.exercicio = suplementacao.exercicio
               AND suplementacao_reducao.cod_suplementacao = suplementacao.cod_suplementacao
             WHERE suplementacao.exercicio = '".$this->getDado('exercicio')."'
               AND suplementacao.dt_suplementacao BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                AND NOT EXISTS
                    (
                        SELECT 1
                          FROM orcamento.suplementacao_anulada
                         WHERE suplementacao_anulada.exercicio         = suplementacao.exercicio   
                           AND suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao
                    )
               AND NOT EXISTS
                   (
                       SELECT 1
                         FROM orcamento.suplementacao_anulada
                        WHERE suplementacao_anulada.exercicio                  = suplementacao.exercicio   
                          AND suplementacao_anulada.cod_suplementacao_anulacao = suplementacao.cod_suplementacao
                   )
          GROUP BY suplementacao.exercicio
                 , suplementacao_reducao.cod_despesa
                 , suplementacao.dt_suplementacao
                 , suplementacao.cod_suplementacao
                 , suplementacao.cod_tipo

         ) AS supl ON
             supl.exercicio   = orcamento.despesa.exercicio AND
             supl.cod_despesa = orcamento.despesa.cod_despesa
        
        JOIN ( SELECT
                   ( COALESCE(SUM(odespesa.vl_original),0)
                     +
                     COALESCE(SUM(osuplementacao.valor),0)
                     -
                     COALESCE(SUM(oreducao.valor),0) 
                     -
                     COALESCE(SUM(oempenho.vl_empenho),0)
                     +
                     COALESCE(SUM(oempenho.vl_empenho_anulado),0)
                   )as saldo_anterior
                    ,odespesa.cod_despesa
                    ,odespesa.exercicio
               FROM
                   orcamento.despesa AS odespesa
               LEFT JOIN  (  SELECT
                                    suplementacao.dt_suplementacao
                                  , suplementacao_suplementada.cod_despesa
                                  , SUM(suplementacao_suplementada.valor) AS valor
                                  , suplementacao_suplementada.exercicio
                               FROM orcamento.suplementacao
                         INNER JOIN orcamento.suplementacao_suplementada
                                 ON suplementacao_suplementada.cod_suplementacao =
                                    suplementacao.cod_suplementacao  AND
                                    suplementacao_suplementada.exercicio = suplementacao.exercicio
                              WHERE NOT EXISTS
                                    (
                                        SELECT 1
                                          FROM orcamento.suplementacao_anulada
                                         WHERE suplementacao_anulada.exercicio                  = suplementacao.exercicio   
                                           AND suplementacao_anulada.cod_suplementacao_anulacao = suplementacao.cod_suplementacao
                                    )
                                AND NOT EXISTS
                                    (
                                        SELECT 1
                                          FROM orcamento.suplementacao_anulada
                                         WHERE suplementacao_anulada.exercicio         = suplementacao.exercicio   
                                           AND suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao
                                    )
                              GROUP BY
                                  dt_suplementacao
                                 ,cod_despesa
                                 ,suplementacao_suplementada.exercicio
                          )   AS osuplementacao
                              ON (
                                     osuplementacao.cod_despesa = odespesa.cod_despesa  AND
                                     osuplementacao.exercicio = odespesa.exercicio      AND
                                     osuplementacao.dt_suplementacao BETWEEN TO_DATE('01/01/".$this->getDado('exercicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy')
                                 )
               LEFT JOIN  (  SELECT  suplementacao.dt_suplementacao
                                    ,suplementacao_reducao.cod_despesa
                                    ,SUM(suplementacao_reducao.valor) AS valor
                                    ,suplementacao_reducao.exercicio
                             FROM  orcamento.suplementacao
                             INNER JOIN  orcamento.suplementacao_reducao
                                 ON  suplementacao_reducao.cod_suplementacao =
                                     suplementacao.cod_suplementacao       AND
                                     suplementacao_reducao.exercicio  = suplementacao.exercicio
                              WHERE NOT EXISTS
                                    (
                                        SELECT 1
                                          FROM orcamento.suplementacao_anulada
                                         WHERE suplementacao_anulada.exercicio                  = suplementacao.exercicio   
                                           AND suplementacao_anulada.cod_suplementacao_anulacao = suplementacao.cod_suplementacao
                                    )
                                AND NOT EXISTS
                                    (
                                        SELECT 1
                                          FROM orcamento.suplementacao_anulada
                                         WHERE suplementacao_anulada.exercicio         = suplementacao.exercicio   
                                           AND suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao
                                    )
                             GROUP BY
                                dt_suplementacao
                               ,cod_despesa
                               ,suplementacao_reducao.exercicio
                          )  AS  oreducao
                             ON  oreducao.cod_despesa = odespesa.cod_despesa  AND
                                 oreducao.exercicio = odespesa.exercicio      AND
                                 oreducao.dt_suplementacao BETWEEN TO_DATE('01/01/".$this->getDado('exercicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') 
               LEFT JOIN  (  SELECT  SUM(vl_total) AS vl_empenho
                                    ,SUM(vl_anulado) AS vl_empenho_anulado
                                    ,pre_empenho_despesa.cod_despesa
                                    ,pre_empenho_despesa.exercicio
                             FROM  empenho.pre_empenho_despesa
                            INNER JOIN  empenho.pre_empenho
                                    ON  pre_empenho.exercicio = pre_empenho_despesa.exercicio
                                   AND  pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
                            INNER JOIN  empenho.item_pre_empenho
                                    ON  item_pre_empenho.exercicio = pre_empenho.exercicio
                                   AND  item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                             LEFT JOIN  empenho.empenho_anulado_item
                                    ON  empenho_anulado_item.exercicio = item_pre_empenho.exercicio
                                   AND  empenho_anulado_item.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                            INNER JOIN  empenho.empenho
                                    ON  empenho.exercicio = pre_empenho.exercicio
                                   AND  empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                                 WHERE  empenho.dt_empenho BETWEEN TO_DATE('01/01/".$this->getDado('exercicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy')
                              GROUP BY  pre_empenho_despesa.exercicio, pre_empenho_despesa.cod_despesa
                          ) AS oempenho
                            ON oempenho.cod_despesa = odespesa.cod_despesa  AND
                               oempenho.exercicio = odespesa.exercicio
                    GROUP BY 
                            odespesa.cod_despesa
                            ,odespesa.exercicio
        ) as despesa_anterior
            ON despesa_anterior.exercicio = despesa.exercicio   
            AND despesa_anterior.cod_despesa = despesa.cod_despesa

        JOIN orcamento.despesa_acao
          ON despesa_acao.exercicio_despesa = despesa.exercicio
         AND despesa_acao.cod_despesa = despesa.cod_despesa
        JOIN ppa.acao
          ON acao.cod_acao = despesa_acao.cod_acao
        JOIN ppa.programa
          ON programa.cod_programa = despesa.cod_programa
    WHERE
        despesa.exercicio       = '".$this->getDado('exercicio')."'    AND
        despesa.cod_entidade IN (".$this->getDado('stEntidades').") AND
        conta_despesa.exercicio = orcamento.despesa.exercicio        AND
        conta_despesa.cod_conta = orcamento.despesa.cod_conta
    
        ";
        return $stSql;
    }

    function recuperaAlteracoesOrcamentariasPorRecurso(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao=""){
        return $this->executaRecupera("montaRecuperaAlteracoesOrcamentariasPorRecurso",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    function montaRecuperaAlteracoesOrcamentariasPorRecurso(){
        $stSql = "

    SELECT
        '12' AS tipo_registro
        ,programa.num_programa
        ,despesa.num_orgao
        ,despesa.num_unidade
        ,despesa.cod_funcao
        ,despesa.cod_subfuncao
        ,substr(acao.num_acao::varchar,1,1) AS nat_acao
        ,substr(acao.num_acao::varchar,2,3) AS num_seqproj
        ,substr(replace(orcamento.conta_despesa.cod_estrutural,'.',''),1,6) AS cod_estrutural
        ,supl.dt_suplementacao
        ,supl.cod_suplementacao
        ,CASE WHEN (supl.cod_tipo = 5  )  THEN '01'
              WHEN (supl.cod_tipo = 4  )  THEN '02'
              WHEN (supl.cod_tipo = 1 AND supl.reducao = true )  THEN '09'
              WHEN (supl.cod_tipo = 1 AND supl.reducao = false )  THEN '03'
              WHEN (supl.cod_tipo = 2  )  THEN '04'
              WHEN (supl.cod_tipo = 10 )  THEN '05'
              WHEN (supl.cod_tipo = 9  )  THEN '06'
              WHEN (supl.cod_tipo = 6  )  THEN '07'
              WHEN (supl.cod_tipo = 7  )  THEN '08'
              WHEN (supl.cod_tipo = 11 )  THEN '11'
              WHEN (supl.cod_tipo = 999 ) THEN '09'
              ELSE '99'
          END AS tipo_alteracao
        ,substr(recurso.cod_fonte::varchar, 1, 3) AS cod_fonte
        ,supl.valor AS vl_alteracao
        ,despesa_anterior.saldo_anterior AS  vl_saldo_anterior        
        ,CASE WHEN (supl.cod_tipo = 1 AND supl.reducao = true ) THEN 
                    despesa_anterior.saldo_anterior - supl.valor   
              WHEN (supl.cod_tipo = 1 AND supl.reducao = false ) THEN
                    despesa_anterior.saldo_anterior + supl.valor   
         END AS  vl_saldo_atual
        , '' AS brancos
        , '0' AS nro_sequencial

    FROM
        orcamento.conta_despesa,
        orcamento.recurso,
        orcamento.despesa

   JOIN (   
            SELECT suplementacao.exercicio
                 , suplementacao_suplementada.cod_despesa
                 , SUM(COALESCE(suplementacao_suplementada.valor,0)) AS valor
                 , TO_CHAR(suplementacao.dt_suplementacao,'dd/mm/yyyy') as dt_suplementacao
                 , suplementacao.cod_suplementacao
                 , suplementacao.cod_tipo
                 , false AS reducao
              FROM orcamento.suplementacao 
        INNER JOIN orcamento.suplementacao_suplementada
                ON suplementacao_suplementada.exercicio = suplementacao.exercicio
               AND suplementacao_suplementada.cod_suplementacao = suplementacao.cod_suplementacao
             WHERE suplementacao.exercicio = '".$this->getDado('exercicio')."'
               AND suplementacao.dt_suplementacao BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
               AND NOT EXISTS
                   (
                       SELECT 1
                         FROM orcamento.suplementacao_anulada
                        WHERE suplementacao_anulada.exercicio         = suplementacao.exercicio   
                          AND suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao
                   )
               AND NOT EXISTS
                   (
                       SELECT 1
                         FROM orcamento.suplementacao_anulada
                        WHERE suplementacao_anulada.exercicio                  = suplementacao.exercicio   
                          AND suplementacao_anulada.cod_suplementacao_anulacao = suplementacao.cod_suplementacao
                   )
          GROUP BY suplementacao.exercicio
                 , suplementacao_suplementada.cod_despesa
                 , suplementacao.dt_suplementacao
                 , suplementacao.cod_suplementacao
                 , suplementacao.cod_tipo
            
             UNION
        
            SELECT suplementacao.exercicio
                 , suplementacao_reducao.cod_despesa
                 , SUM(COALESCE(suplementacao_reducao.valor,0)) AS valor
                 , TO_CHAR(suplementacao.dt_suplementacao,'dd/mm/yyyy') as dt_suplementacao
                 , suplementacao.cod_suplementacao
                 , suplementacao.cod_tipo
                 , true AS reducao
              FROM orcamento.suplementacao 
        INNER JOIN orcamento.suplementacao_reducao
                ON suplementacao_reducao.exercicio = suplementacao.exercicio
               AND suplementacao_reducao.cod_suplementacao = suplementacao.cod_suplementacao
             WHERE suplementacao.exercicio = '".$this->getDado('exercicio')."'
               AND suplementacao.dt_suplementacao BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
               AND NOT EXISTS
                   (
                       SELECT 1
                         FROM orcamento.suplementacao_anulada
                        WHERE suplementacao_anulada.exercicio         = suplementacao.exercicio   
                          AND suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao
                   )
               AND NOT EXISTS
                   (
                       SELECT 1
                         FROM orcamento.suplementacao_anulada
                        WHERE suplementacao_anulada.exercicio                  = suplementacao.exercicio   
                          AND suplementacao_anulada.cod_suplementacao_anulacao = suplementacao.cod_suplementacao
                   )
          GROUP BY suplementacao.exercicio
                 , suplementacao_reducao.cod_despesa
                 , suplementacao.dt_suplementacao
                 , suplementacao.cod_suplementacao
                 , suplementacao.cod_tipo

         ) AS supl ON
             supl.exercicio   = orcamento.despesa.exercicio AND
             supl.cod_despesa = orcamento.despesa.cod_despesa
        JOIN ( SELECT
                   ( COALESCE(SUM(odespesa.vl_original),0)
                     +
                     COALESCE(SUM(osuplementacao.valor),0)
                     -
                     COALESCE(SUM(oreducao.valor),0) 
                     -
                     COALESCE(SUM(oempenho.vl_empenho),0)
                     +
                     COALESCE(SUM(oempenho.vl_empenho_anulado),0)
                   )as saldo_anterior
                    ,odespesa.cod_despesa
                    ,odespesa.exercicio
               FROM
                   orcamento.despesa AS odespesa
               LEFT JOIN  (  SELECT
                                    suplementacao.dt_suplementacao
                                  , suplementacao_suplementada.cod_despesa
                                  , SUM(suplementacao_suplementada.valor) AS valor
                                  , suplementacao_suplementada.exercicio
                               FROM orcamento.suplementacao
                         INNER JOIN orcamento.suplementacao_suplementada
                                 ON suplementacao_suplementada.cod_suplementacao =
                                    suplementacao.cod_suplementacao  AND
                                    suplementacao_suplementada.exercicio = suplementacao.exercicio
                              WHERE NOT EXISTS
                                    (
                                        SELECT 1
                                          FROM orcamento.suplementacao_anulada
                                         WHERE suplementacao_anulada.exercicio                  = suplementacao.exercicio   
                                           AND suplementacao_anulada.cod_suplementacao_anulacao = suplementacao.cod_suplementacao
                                    )
                                AND NOT EXISTS
                                    (
                                        SELECT 1
                                          FROM orcamento.suplementacao_anulada
                                         WHERE suplementacao_anulada.exercicio         = suplementacao.exercicio   
                                           AND suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao
                                    )
                              GROUP BY
                                  dt_suplementacao
                                 ,cod_despesa
                                 ,suplementacao_suplementada.exercicio
                          )   AS osuplementacao
                              ON (
                                     osuplementacao.cod_despesa = odespesa.cod_despesa  AND
                                     osuplementacao.exercicio = odespesa.exercicio      AND
                                     osuplementacao.dt_suplementacao BETWEEN TO_DATE('01/01/".$this->getDado('exercicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy')
                                 )
               LEFT JOIN  (  SELECT  suplementacao.dt_suplementacao
                                    ,suplementacao_reducao.cod_despesa
                                    ,SUM(suplementacao_reducao.valor) AS valor
                                    ,suplementacao_reducao.exercicio
                             FROM  orcamento.suplementacao
                             INNER JOIN  orcamento.suplementacao_reducao
                                 ON  suplementacao_reducao.cod_suplementacao =
                                     suplementacao.cod_suplementacao       AND
                                     suplementacao_reducao.exercicio  = suplementacao.exercicio
                              WHERE NOT EXISTS
                                    (
                                        SELECT 1
                                          FROM orcamento.suplementacao_anulada
                                         WHERE suplementacao_anulada.exercicio                  = suplementacao.exercicio   
                                           AND suplementacao_anulada.cod_suplementacao_anulacao = suplementacao.cod_suplementacao
                                    )
                                AND NOT EXISTS
                                    (
                                        SELECT 1
                                          FROM orcamento.suplementacao_anulada
                                         WHERE suplementacao_anulada.exercicio         = suplementacao.exercicio   
                                           AND suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao
                                    )
                             GROUP BY
                                dt_suplementacao
                               ,cod_despesa
                               ,suplementacao_reducao.exercicio
                          )  AS  oreducao
                             ON  oreducao.cod_despesa = odespesa.cod_despesa  AND
                                 oreducao.exercicio = odespesa.exercicio      AND
                                 oreducao.dt_suplementacao BETWEEN TO_DATE('01/01/".$this->getDado('exercicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') 
               LEFT JOIN  (  SELECT  SUM(vl_total) AS vl_empenho
                                    ,SUM(vl_anulado) AS vl_empenho_anulado
                                    ,pre_empenho_despesa.cod_despesa
                                    ,pre_empenho_despesa.exercicio
                             FROM  empenho.pre_empenho_despesa
                            INNER JOIN  empenho.pre_empenho
                                    ON  pre_empenho.exercicio = pre_empenho_despesa.exercicio
                                   AND  pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
                            INNER JOIN  empenho.item_pre_empenho
                                    ON  item_pre_empenho.exercicio = pre_empenho.exercicio
                                   AND  item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                             LEFT JOIN  empenho.empenho_anulado_item
                                    ON  empenho_anulado_item.exercicio = item_pre_empenho.exercicio
                                   AND  empenho_anulado_item.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                            INNER JOIN  empenho.empenho
                                    ON  empenho.exercicio = pre_empenho.exercicio
                                   AND  empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                                 WHERE  empenho.dt_empenho BETWEEN TO_DATE('01/01/".$this->getDado('exercicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy')
                              GROUP BY  pre_empenho_despesa.exercicio, pre_empenho_despesa.cod_despesa
                          ) AS oempenho
                            ON oempenho.cod_despesa = odespesa.cod_despesa  AND
                               oempenho.exercicio = odespesa.exercicio
                    GROUP BY 
                            odespesa.cod_despesa
                            ,odespesa.exercicio
        ) as despesa_anterior
            ON despesa_anterior.exercicio = despesa.exercicio   
            AND despesa_anterior.cod_despesa = despesa.cod_despesa

        JOIN orcamento.despesa_acao
          ON despesa_acao.exercicio_despesa = despesa.exercicio
         AND despesa_acao.cod_despesa = despesa.cod_despesa
        JOIN ppa.acao
          ON acao.cod_acao = despesa_acao.cod_acao
        JOIN ppa.programa
          ON programa.cod_programa = despesa.cod_programa
    WHERE
        despesa.exercicio = '".$this->getDado('exercicio')."'          AND
        despesa.cod_entidade IN (".$this->getDado('stEntidades').") AND
        recurso.exercicio   =  orcamento.despesa.exercicio           AND
        recurso.cod_recurso =  orcamento.despesa.cod_recurso         AND
        conta_despesa.exercicio =  orcamento.despesa.exercicio       AND
        conta_despesa.cod_conta =  orcamento.despesa.cod_conta
        
        ";      
        return $stSql;
    }
 //starttype90

    function recuperaRegistroLeiSuplementacao(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao=""){
        return $this->executaRecupera("montaRecuperaRegistroLeiSuplementacao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    function montaRecuperaRegistroLeiSuplementacao(){
        $stSql = "
            SELECT
       '90' AS tipo_registro
       , REPLACE(supl.num_norma, '.', '') AS num_norma
       ,supl.dt_assinatura
       ,ROUND(SUM(supl.valor),2) AS valor
       ,'' AS brancos
       , '0' AS nro_sequencial

    FROM
        orcamento.despesa
        JOIN (    SELECT
                       orcamento.suplementacao_suplementada.exercicio
                      ,orcamento.suplementacao_suplementada.cod_despesa
                      ,orcamento.suplementacao_suplementada.valor
                      ,normas.norma.num_norma
                      ,TO_CHAR(normas.norma.dt_assinatura,'dd/mm/yyyy') AS dt_assinatura
                  FROM
                       normas.norma,
                       orcamento.suplementacao
                  LEFT JOIN orcamento.suplementacao_suplementada ON (
                       suplementacao_suplementada.exercicio   = suplementacao.exercicio   AND
                       suplementacao_suplementada.cod_suplementacao = suplementacao.cod_suplementacao
                       )
                  WHERE
                       suplementacao_suplementada.exercicio = '".$this->getDado('exercicio')."'         AND
                       normas.norma.exercicio   =  suplementacao.exercicio AND
                       normas.norma.cod_norma =  suplementacao.cod_norma   AND
                       normas.norma.dt_assinatura BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                    AND NOT EXISTS
                        (
                          SELECT 1
                            FROM orcamento.suplementacao_anulada
                           WHERE suplementacao_anulada.exercicio         = suplementacao.exercicio   
                             AND suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao
                        )

                    AND NOT EXISTS
                        (
                          SELECT 1
                            FROM orcamento.suplementacao_anulada
                           WHERE suplementacao_anulada.exercicio                  = suplementacao.exercicio   
                             AND suplementacao_anulada.cod_suplementacao_anulacao = suplementacao.cod_suplementacao
                        )

             ) AS supl ON (
             supl.exercicio   = orcamento.despesa.exercicio AND
             supl.cod_despesa = orcamento.despesa.cod_despesa
             )
    WHERE
        orcamento.despesa.exercicio = '".$this->getDado('exercicio')."' 
    GROUP BY
        supl.num_norma
       ,supl.dt_assinatura
    ORDER BY
        supl.num_norma
        ";
        return $stSql;
    }

 //starttype91

    function recuperaRegistroLeiCreditoEspecial(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao=""){
        return $this->executaRecupera("montaRecuperaRegistroLeiCreditoEspecial",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    function montaRecuperaRegistroLeiCreditoEspecial(){
        $stSql = "
            SELECT '91' AS tipo_registro
                 , REPLACE(supl.num_norma, '.', '') AS num_norma
                 , supl.dt_assinatura
                 , ROUND(SUM(supl.valor),2) AS valor
                 , '' AS brancos
                 , '0' AS nro_sequencial
              FROM orcamento.despesa
        INNER JOIN ( SELECT suplementacao_suplementada.exercicio
                          , suplementacao_suplementada.cod_despesa
                          , suplementacao_suplementada.valor
                          , norma.num_norma
                          , TO_CHAR(normas.norma.dt_assinatura,'dd/mm/yyyy') as dt_assinatura
                       FROM orcamento.suplementacao
                 INNER JOIN orcamento.suplementacao_suplementada
                         ON suplementacao_suplementada.exercicio = suplementacao.exercicio
                        AND suplementacao_suplementada.cod_suplementacao = suplementacao.cod_suplementacao
                 INNER JOIN normas.norma
                         ON norma.exercicio = suplementacao.exercicio
                        AND norma.cod_norma = suplementacao.cod_norma
                        AND normas.norma.dt_assinatura BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                      WHERE suplementacao.cod_tipo BETWEEN 6 AND 10
                        AND NOT EXISTS
                            (
                                SELECT 1
                                  FROM orcamento.suplementacao_anulada
                                 WHERE suplementacao_anulada.exercicio         = suplementacao.exercicio   
                                   AND suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao
                            )
                        AND NOT EXISTS
                            (
                                SELECT 1
                                  FROM orcamento.suplementacao_anulada
                                 WHERE suplementacao_anulada.exercicio                  = suplementacao.exercicio   
                                   AND suplementacao_anulada.cod_suplementacao_anulacao = suplementacao.cod_suplementacao
                            )

/*                     SELECT suplementacao_suplementada.exercicio
                          , suplementacao_suplementada.cod_despesa
                          , suplementacao_suplementada.valor
                          , norma.num_norma
                          , TO_CHAR(normas.norma.dt_assinatura,'dd/mm/yyyy') as dt_assinatura
                       FROM normas.norma,
                            orcamento.suplementacao
                  LEFT JOIN orcamento.suplementacao_suplementada 
                         ON suplementacao_suplementada.exercicio   = suplementacao.exercicio   
                        AND suplementacao_suplementada.cod_suplementacao = suplementacao.cod_suplementacao
                      WHERE suplementacao_suplementada.exercicio = '".$this->getDado('exercicio')."'         
                        AND orcamento.suplementacao.cod_tipo in (6,7.8,9,10 )   
                        AND normas.norma.exercicio = suplementacao.exercicio 
                        AND normas.norma.cod_norma = suplementacao.cod_norma   
                        AND normas.norma.dt_assinatura BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')*/
                    ) AS supl 
                 ON supl.exercicio   = orcamento.despesa.exercicio 
                AND supl.cod_despesa = orcamento.despesa.cod_despesa
    WHERE
         orcamento.despesa.exercicio = '".$this->getDado('exercicio')."'
    GROUP BY
        supl.num_norma
       ,supl.dt_assinatura
    ORDER BY
        supl.num_norma
        ";
        return $stSql;
    }

    function recuperaRegistroAlteracaoPPA(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao=""){
        return $this->executaRecupera("montaRecuperaRegistroAlteracaoPPA",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    function montaRecuperaRegistroAlteracaoPPA(){
        $stSql = "
              SELECT '93' AS tipo_registro
                   , REPLACE(ppa.num_norma, '.', '') AS num_norma
                   , ppa.dt_assinatura
                   , '' AS brancos
                   , '0' AS nro_sequencial
                FROM ( SELECT norma.num_norma
                            , TO_CHAR(normas.norma.dt_assinatura,'dd/mm/yyyy') as dt_assinatura
                         FROM ldo.homologacao
                   INNER JOIN normas.norma
                           ON norma.cod_norma = homologacao.cod_norma
                          AND normas.norma.dt_assinatura BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                        WHERE timestamp IN ( SELECT timestamp 
                                               FROM ldo.homologacao AS homologacao_timestamp
                                              WHERE ( SELECT COUNT(cod_ppa)
                                                        FROM ldo.homologacao AS homologacao_count
                                                       WHERE homologacao_count.cod_ppa = homologacao_timestamp.cod_ppa
                                                         AND homologacao_count.ano     = homologacao_timestamp.ano) > 1
                                                AND homologacao_timestamp.cod_ppa = homologacao.cod_ppa
                                                AND homologacao_timestamp.ano     = homologacao.ano
                                             OFFSET 1 )
                    ) AS ppa
            GROUP BY ppa.num_norma
                   , ppa.dt_assinatura
            ORDER BY ppa.num_norma
        ";

        return $stSql;
    }

    function recuperaRegistroDecretosAberturaCreditosAdicionais(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao=""){
        return $this->executaRecupera("montaRecuperaRegistroDecretosAberturaCreditosAdicionais",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    function montaRecuperaRegistroDecretosAberturaCreditosAdicionais(){
        $stSql = "
            SELECT '94' AS tipo_registro
                 , REPLACE(supl.num_norma, '.', '') AS num_norma
                 , supl.dt_assinatura
                 , ROUND(SUM(supl.valor),2) AS valor
                 , tipo_credito
                 , '' AS brancos
                 , '0' AS nro_sequencial
              FROM orcamento.despesa
        INNER JOIN ( SELECT suplementacao_suplementada.exercicio
                          , suplementacao_suplementada.cod_despesa
                          , suplementacao_suplementada.valor
                          , norma.num_norma
                          , TO_CHAR(normas.norma.dt_assinatura,'dd/mm/yyyy') as dt_assinatura
                          , CASE WHEN suplementacao.cod_tipo BETWEEN 1 AND 5 THEN
                                    1
                                 WHEN suplementacao.cod_tipo BETWEEN 6 AND 10 THEN
                                    2
                                 WHEN suplementacao.cod_tipo = 11 THEN
                                    3
                            END AS tipo_credito
                       FROM orcamento.suplementacao
                 INNER JOIN orcamento.suplementacao_suplementada
                         ON suplementacao_suplementada.exercicio = suplementacao.exercicio
                        AND suplementacao_suplementada.cod_suplementacao = suplementacao.cod_suplementacao
                 INNER JOIN normas.norma
                         ON norma.exercicio = suplementacao.exercicio
                        AND norma.cod_norma = suplementacao.cod_norma
                        AND normas.norma.dt_assinatura BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                      WHERE norma.cod_tipo_norma = 3
                        AND NOT EXISTS
                            (
                                SELECT 1
                                  FROM orcamento.suplementacao_anulada
                                 WHERE suplementacao_anulada.exercicio         = suplementacao.exercicio
                                   AND suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao
                            )
                        AND NOT EXISTS
                            (
                                SELECT 1
                                  FROM orcamento.suplementacao_anulada
                                 WHERE suplementacao_anulada.exercicio                  = suplementacao.exercicio
                                   AND suplementacao_anulada.cod_suplementacao_anulacao = suplementacao.cod_suplementacao
                            )

                 ) AS supl
                ON supl.exercicio   = orcamento.despesa.exercicio
               AND supl.cod_despesa = orcamento.despesa.cod_despesa
             WHERE orcamento.despesa.exercicio = '".$this->getDado('exercicio')."'
          GROUP BY supl.num_norma
                 , supl.dt_assinatura
                 , supl.tipo_credito
          ORDER BY supl.num_norma
        ";

        return $stSql;
    }

}
?>
