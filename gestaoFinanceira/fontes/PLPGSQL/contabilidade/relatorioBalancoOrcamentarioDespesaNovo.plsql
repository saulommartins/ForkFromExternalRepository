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
/*
* Script de função PLPGSQL - Relatório STN - RREO - Anexo 1
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Revision: 29316 $
* $Name$
* $Author: lbbarreiro $
* $Date: 2008-04-17 18:13:29 -0300 (Qui, 17 Abr 2008) $
*
* Casos de uso: uc-04.05.28
*/


/*$Log$
 *Revision 1.1  2006/09/26 17:33:42  bruce
 *Colocada a tag de log
 **/

CREATE OR REPLACE FUNCTION contabilidade.fn_relatorio_balanco_orcamentario_despesa_novo( varchar, varchar, varchar ,varchar, varchar ) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio     ALIAS FOR $1;
    dtInicial       ALIAS FOR $2;
    dtFinal         ALIAS FOR $3;
    stCodEntidades  ALIAS FOR $4;
    stCodRecursos   ALIAS FOR $5;

    dtIniExercicio  VARCHAR := '';
    
    arDatas         varchar[] ;
    reRegistro      record ;
    stSql           varchar := '';

BEGIN

    --arDatas := publico.bimestre ( stExercicio, inBimestre );   
    dtIniExercicio := '01/01/' || stExercicio;

    -- TABELAS TEMPORARIAS
    
    -- Total das Suplementações 
    -- Suplementado
    
    stSql := 'CREATE TEMPORARY TABLE tmp_suplementacao_suplementada AS
               SELECT   sups.cod_despesa
                       ,sum(sups.valor)  as vl_suplementado
               FROM     orcamento.suplementacao                as sup
                       ,orcamento.suplementacao_suplementada   as sups
                       ,orcamento.despesa                      as de
               WHERE   sup.exercicio           = '|| quote_literal(stExercicio) ||' 
                 AND   sup.dt_suplementacao BETWEEN to_date('|| quote_literal(dtIniExercicio) ||',''dd/mm/yyyy'') 
                                                AND to_date('|| quote_literal(dtFinal) ||',''dd/mm/yyyy'') ';
    
    if ( stCodEntidades != '' ) then
        stSql := stSql || 'AND   de.cod_entidade IN (' || stCodEntidades || ' )';
    end if;
    
    stSql := stSql|| ' AND   sup.exercicio           = sups.exercicio
            AND   sup.cod_suplementacao   = sups.cod_suplementacao
            AND   sups.exercicio          = de.exercicio
            AND   sups.cod_despesa        = de.cod_despesa
        GROUP BY sups.cod_despesa;
    CREATE INDEX unq_tmp_suplementacao_suplementada ON tmp_suplementacao_suplementada (cod_despesa);
    ';

    EXECUTE stSql;

    -- Reduzido
    
    stSql := '
    CREATE TEMPORARY TABLE tmp_suplementacao_reduzida AS
        SELECT 
            supr.cod_despesa, 
            sum(supr.valor) as vl_reduzido 
        FROM 
            orcamento.suplementacao as sup, 
            orcamento.suplementacao_reducao as supr, 
            orcamento.despesa as de 
        WHERE 
            sup.exercicio = '|| quote_literal(stExercicio) ||' AND 
            sup.dt_suplementacao BETWEEN    to_date('|| quote_literal(dtIniExercicio) ||', ''dd/mm/yyyy'') 
                                     AND    to_date('|| quote_literal(dtFinal) ||', ''dd/mm/yyyy'')
    ';
    if (stCodEntidades != '' ) then 
         stSql := stSql || ' AND de.cod_entidade IN ('|| stCodEntidades ||' ) ';
    end if ;

    stSql := stSql || 'AND sup.exercicio = supr.exercicio AND 
            sup.cod_suplementacao   = supr.cod_suplementacao AND 
            supr.exercicio          = de.exercicio AND 
            supr.cod_despesa        = de.cod_despesa 
        GROUP BY supr.cod_despesa ;
        
    CREATE INDEX unq_tmp_suplementacao_reduzida ON tmp_suplementacao_reduzida (cod_despesa);
    ';
    
    EXECUTE stSql;

    -- Total das Despesas
    
    stSql := '
    CREATE TEMPORARY TABLE tmp_despesa_totais AS
        SELECT de.exercicio, 
               de.cod_despesa, 
               de.vl_original 
    
          FROM orcamento.despesa de, 
               empenho.pre_empenho_despesa ped 
    
         WHERE de.exercicio    = '|| quote_literal(stExercicio) ||' 
           AND de.exercicio    = ped.exercicio 
           AND de.cod_despesa  = ped.cod_despesa 
      
      GROUP BY de.exercicio, 
               de.cod_despesa, 
               de.vl_original 
      
      ORDER BY de.exercicio, 
               de.cod_despesa ;
            
    CREATE INDEX unq_tmp_despesa_totais ON tmp_despesa_totais (exercicio,cod_despesa);
    ';

    EXECUTE stSql;

    
    -- --------------------------------------------
    -- Total por Despesa
    -- --------------------------------------------


    stSql := '
    CREATE TEMPORARY TABLE tmp_despesa AS 
        SELECT 
            de.cod_conta, 
            de.exercicio, 
            ocd.cod_estrutural, 
            sum(coalesce(de.vl_original,0.00)) as vl_original,          
            (sum(coalesce(sups.vl_suplementado,0.00)) - sum(coalesce(supr.vl_reduzido,0.00))) as vl_suplementacoes, 
            CAST(0.00 AS NUMERIC(14,2)) as vl_empenhado_bimestre, 
            CAST(0.00 AS NUMERIC(14,2)) as vl_liquidado_bimestre, 
            CAST(0.00 AS NUMERIC(14,2)) as vl_empenhado_total, 
            CAST(0.00 AS NUMERIC(14,2)) as vl_liquidado_total,
            CAST(0.00 AS NUMERIC(14,2)) as vl_pago_total 

        FROM orcamento.despesa de 

  INNER JOIN orcamento.conta_despesa ocd 
          ON ocd.exercicio = de.exercicio 
         AND ocd.cod_conta = de.cod_conta 
   
   LEFT JOIN tmp_despesa_totais tdt 
          ON tdt.exercicio = de.exercicio 
         AND tdt.cod_despesa = de.cod_despesa 

             --Suplementacoes
   LEFT JOIN tmp_suplementacao_suplementada sups 
          ON de.cod_despesa = sups.cod_despesa
   
   LEFT JOIN tmp_suplementacao_reduzida supr 
          ON de.cod_despesa = supr.cod_despesa 
       
       WHERE de.exercicio = '|| quote_literal(stExercicio) ||' 
         AND de.cod_entidade IN ('||  stCodEntidades ||' ) ';

 if (stCodRecursos != '' ) then 
        stSql := stSql || ' AND de.cod_recurso IN ('||  stCodRecursos ||' ) ';
    end if ;

stSql := stSql || '
    GROUP BY de.cod_conta, 
             de.exercicio, 
             ocd.cod_estrutural 
    
    ORDER BY de.cod_conta, 
             de.exercicio 
    ';
    
    EXECUTE stSql;
    
    --- FIM DAS TEMPORARIAS
    
    stSql := '
    CREATE TEMPORARY TABLE tmp_rreo_an1_despesa AS (
     SELECT 1 as grupo,
            CAST(''3.0.0.0.00.00.00.00.00'' as VARCHAR) as cod_estrutural,
            CAST(''DESPESAS CORRENTES'' as VARCHAR) as descricao,
            1 as nivel,
            0.00 as dotacao_inicial , 
            0.00 as creditos_adicionais , 
            0.00 as dotacao_atualizada , 
            0.00 AS vl_empenhado_bimestre , 
            0.00 AS vl_empenhado_total , 
            0.00 AS vl_liquidado_bimestre , 
            0.00 AS vl_liquidado_total ,
            0.00 AS vl_pago_total , 
            0.00 AS percentual , 
            0.00 AS saldo_liquidar  

      UNION ALL

            SELECT * FROM (
                SELECT
                    1 as grupo,
                    cast(''3.2.0.0.00.00.00.00.00'' as varchar) as cod_estrutural ,
                    cast(''JUROS E ENCARGOS DA DÍVIDA'' as varchar) as descricao ,
                    2 as nivel ,
                    CAST(SUM(tmp.vl_original) as numeric(14,2)) as dotacao_inicial , 
                    CAST(SUM(tmp.vl_suplementacoes) as numeric(14,2)) as creditos_adicionais , 
                    CAST(SUM((tmp.vl_original + tmp.vl_suplementacoes))as numeric(14,2)) as dotacao_atualizada , 
            COALESCE((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtInicial)      ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_empenhado_bimestre  ,
            COALESCE((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtIniExercicio) ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_empenhado_total     ,
            COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtInicial)      ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_liquidado_bimestre  ,
            COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtIniExercicio) ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_liquidado_total     ,
            COALESCE((SELECT * FROM contabilidade.fn_recupera_despesa_paga( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtIniExercicio) ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_pago_total ,
            CAST(0.00 AS NUMERIC(14,2)) AS percentual , 
            CAST(0.00 AS NUMERIC(14,2)) AS saldo_liquidar  
                FROM
                    orcamento.conta_despesa ocd
                    LEFT JOIN
                    tmp_despesa tmp ON
                        ocd.exercicio = tmp.exercicio AND
                        tmp.cod_estrutural LIKE publico.fn_mascarareduzida(ocd.cod_estrutural) ||''%'' 
                WHERE
                    publico.fn_mascarareduzida(ocd.cod_estrutural) like ''3.2%'' AND
                    publico.fn_nivel(ocd.cod_estrutural) >  1 AND  
                    publico.fn_nivel(ocd.cod_estrutural) < 3 AND
                    -- Exceto intra
                    ocd.exercicio = '|| quote_literal(stExercicio) ||'   AND 
            
            substring(ocd.cod_estrutural, 5, 3) <> ''9.1'' AND 
            substring(tmp.cod_estrutural, 5, 3) <> ''9.1'' 
                GROUP BY
                    ocd.descricao,
                    ocd.cod_estrutural
                ORDER BY
                    ocd.cod_estrutural
            ) as tbl

            UNION ALL
            SELECT * FROM ( 
                SELECT
                    1 as grupo,
                    cast(''3.1.0.0.00.00.00.00.00'' as varchar) as cod_estrutural , 
                    cast(''PESSOAL E ENCARGOS SOCIAIS'' as varchar) as descricao , 
                    2 as nivel , 
                    CAST(SUM(tmp.vl_original) as numeric(14,2)) as dotacao_inicial , 
                    CAST(SUM(tmp.vl_suplementacoes) as numeric(14,2)) as creditos_adicionais , 
                    CAST(SUM((tmp.vl_original + tmp.vl_suplementacoes))as numeric(14,2)) as dotacao_atualizada , 
            COALESCE((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtInicial)      ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_empenhado_bimestre  , 
            COALESCE((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtIniExercicio) ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_empenhado_total     , 
            COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtInicial)      ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_liquidado_bimestre  , 
            COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtIniExercicio) ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_liquidado_total     ,
            COALESCE((SELECT * FROM contabilidade.fn_recupera_despesa_paga( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtIniExercicio) ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_pago_total ,
            CAST(0.00 AS NUMERIC(14,2)) AS percentual , 
            CAST(0.00 AS NUMERIC(14,2)) AS saldo_liquidar  
                FROM 
                    orcamento.conta_despesa ocd 
                    LEFT JOIN 
                    tmp_despesa tmp ON 
                        ocd.exercicio = tmp.exercicio AND 
                        tmp.cod_estrutural LIKE publico.fn_mascarareduzida(ocd.cod_estrutural) ||''%'' 
                WHERE 
                    ocd.cod_estrutural = ''3.1.0.0.00.00.00.00.00'' AND
                    publico.fn_nivel(ocd.cod_estrutural) = 2 AND 
                    ocd.exercicio = '|| quote_literal(stExercicio) ||' AND 
            -- Exceto intra
            substring(ocd.cod_estrutural, 5, 3) <> ''9.1'' AND 
            substring(tmp.cod_estrutural, 5, 3) <> ''9.1'' 
                    
                GROUP BY
                    ocd.descricao,
                    ocd.cod_estrutural
                ORDER BY 
                    ocd.cod_estrutural 
            ) as tbl

            UNION ALL

            SELECT * FROM(
                SELECT
                    1 as grupo,
                    cast(''3.3.0.0.00.00.00.00.00'' as varchar) as cod_estrutural ,
                    cast(''OUTRAS DESPESAS CORRENTES'' as varchar) as descricao ,
                    2 as nivel ,
                    CAST(SUM(tmp.vl_original) as numeric(14,2)) as dotacao_inicial , 
                    CAST(SUM(tmp.vl_suplementacoes) as numeric(14,2)) as creditos_adicionais , 
                    CAST(SUM((tmp.vl_original + tmp.vl_suplementacoes))as numeric(14,2)) as dotacao_atualizada , 
            COALESCE((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtInicial)      ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_empenhado_bimestre , 
            COALESCE((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtIniExercicio) ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_empenhado_total    , 
            COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtInicial)      ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_liquidado_bimestre , 
            COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtIniExercicio) ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_liquidado_total    ,
            COALESCE((SELECT * FROM contabilidade.fn_recupera_despesa_paga( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtIniExercicio) ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_pago_total ,
            CAST(0.00 AS NUMERIC(14,2)) AS percentual , 
            CAST(0.00 AS NUMERIC(14,2)) AS saldo_liquidar  
                  FROM
                    orcamento.conta_despesa ocd
                    LEFT JOIN
                    tmp_despesa tmp ON
                        ocd.exercicio = tmp.exercicio AND
                        tmp.cod_estrutural LIKE publico.fn_mascarareduzida(ocd.cod_estrutural) ||''%'' 
                WHERE
                    publico.fn_mascarareduzida(ocd.cod_estrutural) like ''3.3%'' AND 
                    publico.fn_nivel(ocd.cod_estrutural) >  1 AND  
                    publico.fn_nivel(ocd.cod_estrutural) < 3 AND
                    -- Exceto intra
                    ocd.exercicio = '|| quote_literal(stExercicio) ||' AND 
        
            substring(ocd.cod_estrutural, 5, 3) <> ''9.1'' AND 
            substring(tmp.cod_estrutural, 5, 3) <> ''9.1'' 
                  
                GROUP BY
                    ocd.descricao,
                    ocd.cod_estrutural
                ORDER BY
                    ocd.cod_estrutural
            ) as tbl

    
    UNION ALL 

        SELECT
            2 as grupo,
            CAST(''4.0.0.0.00.00.00.00.00'' as VARCHAR) as cod_estrutural,
            CAST(''DESPESAS DE CAPITAL'' as VARCHAR) as descricao,
             1 as nivel,
             0.00 as dotacao_inicial , 
             0.00 as creditos_adicionais , 
             0.00 as dotacao_atualizada , 
             0.00 AS vl_empenhado_bimestre , 
             0.00 AS vl_empenhado_total , 
             0.00 AS vl_liquidado_bimestre , 
             0.00 AS vl_liquidado_total ,
             0.00 AS vl_pago_total,
             0.00 AS percentual , 
             0.00 AS saldo_liquidar  

    UNION ALL

    SELECT * FROM(
        SELECT
            2 as grupo,
            cast(''4.4.0.0.00.00.00.00.00'' as varchar) as cod_estrutural ,
            cast(''INVESTIMENTOS'' as varchar) as descricao ,
            2 as nivel ,
           CAST(SUM(tmp.vl_original) as numeric(14,2)) as dotacao_inicial , 
       CAST(SUM(tmp.vl_suplementacoes) as numeric(14,2)) as creditos_adicionais , 
       CAST(SUM((tmp.vl_original + tmp.vl_suplementacoes))as numeric(14,2)) as dotacao_atualizada , 
       COALESCE((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtInicial)      ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_empenhado_bimestre  , 
       COALESCE((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtIniExercicio) ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_empenhado_total     , 
       COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtInicial)      ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_liquidado_bimestre  , 
       COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtIniExercicio) ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_liquidado_total     ,
       COALESCE((SELECT * FROM contabilidade.fn_recupera_despesa_paga( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtIniExercicio) ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_pago_total ,
       CAST(0.00 AS NUMERIC(14,2)) AS percentual , 
       CAST(0.00 AS NUMERIC(14,2)) AS saldo_liquidar 
        FROM
            orcamento.conta_despesa ocd
            LEFT JOIN
            tmp_despesa tmp ON
                ocd.exercicio = tmp.exercicio AND
                tmp.cod_estrutural LIKE publico.fn_mascarareduzida(ocd.cod_estrutural) ||''%'' 
        WHERE
            publico.fn_mascarareduzida(ocd.cod_estrutural) like ''4.4%'' AND
            publico.fn_nivel(ocd.cod_estrutural) >  1 AND  
            publico.fn_nivel(ocd.cod_estrutural) < 3 AND
            -- Exceto intra
            ocd.exercicio = '|| quote_literal(stExercicio) ||' AND
            substring(ocd.cod_estrutural, 5, 3) <> ''9.1'' AND 
       substring(tmp.cod_estrutural, 5, 3) <> ''9.1'' 
        GROUP BY
            ocd.descricao,
            ocd.cod_estrutural
        ORDER BY
            ocd.cod_estrutural
    ) as tbl

    UNION ALL

    SELECT * FROM(
        SELECT
            2 as grupo,
            cast(''4.5.0.0.00.00.00.00.00'' as varchar) as cod_estrutural ,
            cast(''INVERSÕES FINANCEIRAS'' as varchar) as descricao ,
            2 as nivel ,
            CAST(SUM(tmp.vl_original) as numeric(14,2)) as dotacao_inicial , 
        CAST(SUM(tmp.vl_suplementacoes) as numeric(14,2)) as creditos_adicionais , 
        CAST(SUM((tmp.vl_original + tmp.vl_suplementacoes))as numeric(14,2)) as dotacao_atualizada , 
        COALESCE((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtInicial)      ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_empenhado_bimestre  , 
        COALESCE((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtIniExercicio) ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_empenhado_total     , 
        COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtInicial)      ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_liquidado_bimestre  , 
        COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtIniExercicio) ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_liquidado_total     ,
        COALESCE((SELECT * FROM contabilidade.fn_recupera_despesa_paga( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtIniExercicio) ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_pago_total ,
        CAST(0.00 AS NUMERIC(14,2)) AS percentual , 
        CAST(0.00 AS NUMERIC(14,2)) AS saldo_liquidar 
        FROM
            orcamento.conta_despesa ocd
            LEFT JOIN
            tmp_despesa tmp ON
                ocd.exercicio = tmp.exercicio AND
                tmp.cod_estrutural LIKE publico.fn_mascarareduzida(ocd.cod_estrutural) ||''%'' 
        WHERE
            publico.fn_mascarareduzida(ocd.cod_estrutural) LIKE ''4.5%'' AND
            publico.fn_nivel(ocd.cod_estrutural) >  1 AND
            publico.fn_nivel(ocd.cod_estrutural) < 3 AND
            -- Exceto intra
            ocd.exercicio = '|| quote_literal(stExercicio) ||' AND
            substring(ocd.cod_estrutural, 5, 3) <> ''9.1'' AND 
       substring(tmp.cod_estrutural, 5, 3) <> ''9.1'' 
        GROUP BY
            ocd.descricao,
            ocd.cod_estrutural
        ORDER BY
            ocd.cod_estrutural
    ) as tbl

    UNION ALL

    SELECT * FROM(
        SELECT
            2 as grupo,      
            cast(''4.6.0.0.00.00.00.00.00'' as varchar) as cod_estrutural ,
            cast(''AMORTIZAÇÃO DA DÍVIDA '' as varchar) as descricao ,
            2 as nivel ,
            CAST(SUM(tmp.vl_original) AS numeric(14,2)) AS dotacao_inicial , 
            CAST(SUM(tmp.vl_suplementacoes) AS numeric(14,2)) AS creditos_adicionais , 
        CAST(SUM((tmp.vl_original + tmp.vl_suplementacoes)) AS numeric(14,2)) as dotacao_atualizada , 
            COALESCE((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtInicial)      ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_empenhado_bimestre  , 
            COALESCE((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtIniExercicio) ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_empenhado_total     , 
            COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtInicial)      ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_liquidado_bimestre  , 
            COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtIniExercicio) ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_liquidado_total     ,
            COALESCE((SELECT * FROM contabilidade.fn_recupera_despesa_paga( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtIniExercicio) ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_pago_total ,
        CAST(0.00 AS NUMERIC(14,2)) AS percentual , 
        CAST(0.00 AS NUMERIC(14,2)) AS saldo_liquidar 
        FROM
            orcamento.conta_despesa ocd
            LEFT JOIN
            tmp_despesa tmp ON
                ocd.exercicio = tmp.exercicio AND
                tmp.cod_estrutural LIKE publico.fn_mascarareduzida(ocd.cod_estrutural) ||''%'' 
        WHERE
            publico.fn_mascarareduzida(ocd.cod_estrutural) like ''4.6%'' AND
            publico.fn_nivel(ocd.cod_estrutural) >  1 AND  
            publico.fn_nivel(ocd.cod_estrutural) < 3 AND
            -- Exceto intra
            ocd.exercicio = '|| quote_literal(stExercicio) ||' AND
           substring(ocd.cod_estrutural, 5, 3) <> ''9.1'' AND 
       substring(tmp.cod_estrutural, 5, 3) <> ''9.1'' 
        GROUP BY
            ocd.descricao,
            ocd.cod_estrutural
        ORDER BY
            ocd.cod_estrutural
    ) as tbl

    UNION ALL  

    SELECT * FROM 
        (SELECT 
            CAST(3 AS INTEGER) AS grupo , 
            ocd.cod_estrutural , 
            ocd.descricao ,  
            -- publico.fn_nivel(ocd.cod_estrutural) , 
            CAST(1 AS INTEGER) AS nivel , 
            CAST(SUM(tmp.vl_original) AS numeric(14,2)) AS dotacao_inicial , 
            CAST(SUM(tmp.vl_suplementacoes) AS numeric(14,2)) AS creditos_adicionais , 
            CAST(SUM((tmp.vl_original + tmp.vl_suplementacoes)) AS numeric(14,2)) AS dotacao_atualizada , 
                        COALESCE((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtInicial)      ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_empenhado_bimestre  , 
                        COALESCE((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtIniExercicio) ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_empenhado_total     , 
                        COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtInicial)      ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_liquidado_bimestre  , 
                        COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtIniExercicio) ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_liquidado_total     ,
                        COALESCE((SELECT * FROM contabilidade.fn_recupera_despesa_paga( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtIniExercicio) ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_pago_total ,
            CAST (0.00 AS NUMERIC(14,2)) AS percentual , 
            CAST (0.00 AS NUMERIC(14,2)) AS saldo_liquidar 
        FROM 
            orcamento.conta_despesa ocd 
            INNER JOIN tmp_despesa tmp ON 
                ocd.exercicio = tmp.exercicio AND 
                tmp.cod_estrutural LIKE publico.fn_mascarareduzida(ocd.cod_estrutural) ||''%'' 
        WHERE  
            ocd.cod_estrutural LIKE ''9.9.9.9.99%'' AND 
            publico.fn_nivel(ocd.cod_estrutural) > 4 AND 
            -- Exceto intra
            substring(ocd.cod_estrutural, 5, 3) <> ''9.1'' AND 
            substring(tmp.cod_estrutural, 5, 3) <> ''9.1'' 
        GROUP BY 
            ocd.cod_estrutural, 
            ocd.descricao 
        ORDER BY 
            ocd.cod_estrutural 
        ) AS tbl

    -- Reserva do RPPS
    UNION ALL 

    SELECT * FROM 
        (SELECT 
            CAST(2 AS INTEGER) AS grupo , 
            ''a''||ocd.cod_estrutural , 
            ocd.descricao , 
            --  publico.fn_nivel(ocd.cod_estrutural) , 
            1 AS nivel , 
            CAST(COALESCE(SUM(tmp.vl_original), 0.00) as numeric(14,2)) as dotacao_inicial , 
            CAST(COALESCE(SUM(tmp.vl_suplementacoes), 0.00) as numeric(14,2)) as creditos_adicionais , 
            CAST(COALESCE(SUM((tmp.vl_original + tmp.vl_suplementacoes)), 0.00) as numeric(14,2)) as dotacao_atualizada , 
                        COALESCE((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtInicial)      ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_empenhado_bimestre  , 
                        COALESCE((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtIniExercicio) ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_empenhado_total     , 
                        COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtInicial)      ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_liquidado_bimestre  , 
                        COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtIniExercicio) ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_liquidado_total     ,
                        COALESCE((SELECT * FROM contabilidade.fn_recupera_despesa_paga( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtIniExercicio) ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_pago_total ,
            CAST (0.00 AS NUMERIC(14,2)) AS percentual , 
            CAST (0.00 AS NUMERIC(14,2)) AS saldo_liquidar 
        FROM 
            orcamento.conta_despesa ocd 
            INNER JOIN tmp_despesa tmp ON 
                ocd.exercicio = tmp.exercicio AND 
                tmp.cod_estrutural LIKE publico.fn_mascarareduzida(ocd.cod_estrutural) ||''%'' 
        WHERE 
            ocd.cod_estrutural LIKE ''7.7.9.9.99.99%'' AND 
            publico.fn_nivel(ocd.cod_estrutural) > 5 AND 
            -- Exceto intra
            substring(ocd.cod_estrutural, 5, 3) <> ''9.1'' AND 
            substring(tmp.cod_estrutural, 5, 3) <> ''9.1'' 
        GROUP BY 
            ocd.cod_estrutural , 
            ocd.descricao 
        ORDER BY 
            ocd.cod_estrutural 
    ) AS tbl 

    UNION ALL 
        
    SELECT * FROM  
        (SELECT
            CAST(2 AS INTEGER) AS grupo , 
            ''a9''||ocd.cod_estrutural , 
            -- cast(''a9.6.9.0.76.01.00.00.00'' AS varchar) AS cod_estrutural , 
            ocd.descricao , 
            -- publico.fn_nivel(ocd.cod_estrutural) , 
            CAST(3 AS INTEGER) AS nivel , 
            CAST(SUM(tmp.vl_original) AS numeric(14,2)) as dotacao_inicial , 
            CAST(SUM(tmp.vl_suplementacoes) AS numeric(14,2)) AS creditos_adicionais , 
            CAST(SUM((tmp.vl_original+tmp.vl_suplementacoes)) AS numeric(14,2)) AS dotacao_atualizada , 
                        COALESCE((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtInicial)      ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_empenhado_bimestre  , 
                        COALESCE((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtIniExercicio) ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_empenhado_total     , 
                        COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtInicial)      ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_liquidado_bimestre  , 
                        COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtIniExercicio) ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_liquidado_total     ,
                        COALESCE((SELECT * FROM contabilidade.fn_recupera_despesa_paga( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtIniExercicio) ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_pago_total ,
            CAST (0.00 AS NUMERIC(14,2)) AS percentual , 
            CAST (0.00 AS NUMERIC(14,2)) AS saldo_liquidar 
        FROM 
            orcamento.conta_despesa ocd 
            INNER JOIN tmp_despesa tmp ON 
                ocd.exercicio = tmp.exercicio AND 
                tmp.cod_estrutural LIKE publico.fn_mascarareduzida(ocd.cod_estrutural) ||''%'' 
        WHERE 
            (ocd.cod_estrutural LIKE ''4.6.9.0.76%'' OR  ocd.cod_estrutural LIKE ''4.6.9.0.77%'') AND 
            publico.fn_nivel(ocd.cod_estrutural) > 5 AND 
            -- Exceto intra
            substring(ocd.cod_estrutural, 5, 3) <> ''9.1'' AND 
            substring(tmp.cod_estrutural, 5, 3) <> ''9.1'' 
        GROUP BY 
            ocd.cod_estrutural , 
            ocd.descricao 
        ORDER BY 
            ocd.cod_estrutural 
    ) AS tbl 
    

    
    UNION ALL

    SELECT 
        CAST(4 AS INTEGER) AS grupo , 
        ''7.0.0.0.00.00.00.00.00'' AS cod_estrutural , 
        CAST(''RESERVA DO RPPS'' AS VARCHAR(150)) AS descricao , 
        CAST(1 as INTEGER) AS nivel , 
                SUM( coalesce(dotacao_inicial,0.00) )AS dotacao_inicial , 
                SUM( CASE WHEN coalesce(creditos_adicionais,0.00) <= 0.00 THEN  0.00 END ) AS creditos_adicionais , 
                SUM( CASE WHEN coalesce(dotacao_atualizada,0.00) <= 0.00 THEN  0.00 END )AS dotacao_atualizada ,                 
                SUM( CASE WHEN coalesce(vl_empenhado_bimestre,0.00) <= 0.00 THEN  0.00 END ) AS vl_empenhado_bimestre, 
                SUM( CASE WHEN coalesce(vl_empenhado_total,0.00) <= 0.00 THEN  0.00 END ) AS vl_empenhado_total , 
                SUM( CASE WHEN coalesce(vl_liquidado_bimestre,0.00) <= 0.00 THEN  0.00 END ) AS vl_liquidado_bimestre,
                SUM( CASE WHEN coalesce(vl_liquidado_total,0.00) <= 0.00 THEN  0.00 END ) AS vl_liquidado_total ,
                SUM( CASE WHEN coalesce(vl_pago_total,0.00) <= 0.00 THEN  0.00 END ) AS vl_pago_total , 
                SUM( CASE WHEN coalesce(percentual,0.00) <= 0.00 THEN  0.00 END ) AS percentual , 
                SUM( CASE WHEN coalesce(saldo_liquidar,0.00) <= 0.00 THEN  0.00 END ) AS saldo_liquidar  

     FROM 
        (SELECT 
            CAST(4 AS INTEGER) AS grupo , 
            ocd.cod_estrutural , 
            ocd.descricao , 
            publico.fn_nivel(ocd.cod_estrutural) AS nivel , 
            --CAST(1 AS INTEGER) AS nivel , 
             CASE WHEN (CAST(SUM(tmp.vl_original) AS numeric(14,2)) <= 0.00) OR (CAST(SUM(tmp.vl_original) AS numeric(14,2)) IS NULL) THEN 0.00 END AS dotacao_inicial , 
            CAST(SUM(tmp.vl_suplementacoes) AS numeric(14,2)) AS creditos_adicionais , 
            CAST(SUM((tmp.vl_original + tmp.vl_suplementacoes)) AS numeric(14,2)) as dotacao_atualizada , 
                        COALESCE((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtInicial)      ||', '|| quote_literal(dtFinal) ||', true )), 0.00) AS vl_empenhado_bimestre  , 
                        COALESCE((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtIniExercicio) ||', '|| quote_literal(dtFinal) ||', true )), 0.00) AS vl_empenhado_total     , 
                        COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtInicial)      ||', '|| quote_literal(dtFinal) ||', true )), 0.00) AS vl_liquidado_bimestre  , 
                        COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtIniExercicio) ||', '|| quote_literal(dtFinal) ||', true )), 0.00) AS vl_liquidado_total     ,
                        COALESCE((SELECT * FROM contabilidade.fn_recupera_despesa_paga( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtIniExercicio) ||', '|| quote_literal(dtFinal) ||', false )), 0.00) AS vl_pago_total,
            CAST(0.00 AS NUMERIC(14,2)) AS percentual , 
            CAST(0.00 AS NUMERIC(14,2)) AS saldo_liquidar 
        FROM 
            orcamento.conta_despesa ocd 
            INNER JOIN tmp_despesa tmp ON 
                ocd.exercicio = tmp.exercicio AND 
                tmp.cod_estrutural LIKE publico.fn_mascarareduzida(ocd.cod_estrutural) ||''%'' 
        WHERE 
            --publico.fn_mascarareduzida(ocd.cod_estrutural) like ''4%'' AND 
            publico.fn_nivel(ocd.cod_estrutural) = 4 AND 
                        ocd.cod_estrutural LIKE ''7.%''
            --substring(ocd.cod_estrutural, 5, 3) = ''9.1'' --(Somente Intra-Orçamentarias)
        GROUP BY 
            ocd.cod_estrutural , 
            ocd.descricao 
        ORDER BY 
            ocd.cod_estrutural
        ) AS tbl
        
        UNION ALL

        -- Despesas Intra-Orçamentarias
    SELECT 
        CAST(5 AS INTEGER) AS grupo , 
        ''X.X.9.1.00.00.00.00'' AS cod_estrutural , 
        CAST(''DESPESAS (INTRA-ORÇAMENTÁRIAS)'' AS VARCHAR(150)) AS descricao , 
        CAST(1 as INTEGER) AS nivel , 
        SUM( dotacao_inicial) AS dotacao_inicial , 
        SUM( creditos_adicionais) AS creditos_adicionais , 
        SUM( dotacao_atualizada) AS dotacao_atualizada , 
        SUM( vl_empenhado_bimestre) AS vl_empenhado_bimestre , 
        SUM( vl_empenhado_total) AS vl_empenhado_total , 
        SUM( vl_liquidado_bimestre) AS vl_liquidado_bimestre , 
        SUM( vl_liquidado_total) AS vl_liquidado_total ,
                SUM( vl_pago_total) AS vl_pago_total , 
        SUM( percentual) AS percentual , 
        SUM( saldo_liquidar) AS saldo_liquidar  
     FROM 
        (SELECT 
            CAST(5 AS INTEGER) AS grupo , 
            ocd.cod_estrutural , 
            --CAST(''DESPESAS (INTRA-ORÇAMENTÁRIAS)'' AS VARCHAR(150)) AS descricao , 
            ocd.descricao , 
            publico.fn_nivel(ocd.cod_estrutural) AS nivel , 
            --CAST(1 AS INTEGER) AS nivel , 
            CAST(SUM(tmp.vl_original) AS numeric(14,2)) AS dotacao_inicial , 
            CAST(SUM(tmp.vl_suplementacoes) AS numeric(14,2)) AS creditos_adicionais , 
            CAST(SUM((tmp.vl_original + tmp.vl_suplementacoes)) AS numeric(14,2)) as dotacao_atualizada , 
                        COALESCE((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtInicial)      ||', '|| quote_literal(dtFinal) ||', true )), 0.00) AS vl_empenhado_bimestre , 
                        COALESCE((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtIniExercicio) ||', '|| quote_literal(dtFinal) ||', true )), 0.00) AS vl_empenhado_total    , 
                        COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtInicial)      ||', '|| quote_literal(dtFinal) ||', true )), 0.00) AS vl_liquidado_bimestre , 
                        COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtIniExercicio) ||', '|| quote_literal(dtFinal) ||', true )), 0.00) AS vl_liquidado_total    , 
            COALESCE((SELECT * FROM contabilidade.fn_recupera_despesa_paga( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(dtIniExercicio) ||', '|| quote_literal(dtFinal) ||', true )), 0.00) AS vl_pago_total,
                        CAST(0.00 AS NUMERIC(14,2)) AS percentual , 
            CAST(0.00 AS NUMERIC(14,2)) AS saldo_liquidar 
        FROM 
            orcamento.conta_despesa ocd 
            INNER JOIN tmp_despesa tmp ON 
                ocd.exercicio = tmp.exercicio AND 
                tmp.cod_estrutural LIKE publico.fn_mascarareduzida(ocd.cod_estrutural) ||''%'' 
        WHERE 
            publico.fn_nivel(ocd.cod_estrutural) = 4 
            AND ocd.cod_estrutural like ''%.9.1.00.00.00.00%'' --(Somente Intra-Orçamentarias)
                        
        GROUP BY 
            ocd.cod_estrutural , 
            ocd.descricao 
        ORDER BY 
            ocd.cod_estrutural
        ) AS tbl
        
        )';

    EXECUTE stSql;
    
    
    stSql := '
        UPDATE tmp_rreo_an1_despesa SET vl_liquidado_total=  0.00 WHERE vl_liquidado_total IS NULL;
    UPDATE tmp_rreo_an1_despesa 
    SET percentual = COALESCE(((vl_liquidado_total/dotacao_inicial)*100), 0.00) 
    WHERE vl_liquidado_total > 0 AND dotacao_inicial > 0 ; 
        
    UPDATE tmp_rreo_an1_despesa 
    SET saldo_liquidar = COALESCE((dotacao_atualizada - vl_liquidado_total), 0.00) ;
    
        UPDATE tmp_rreo_an1_despesa SET dotacao_inicial=  0.00 WHERE dotacao_inicial IS NULL;
        UPDATE tmp_rreo_an1_despesa SET creditos_adicionais=  0.00 WHERE creditos_adicionais IS NULL;
        UPDATE tmp_rreo_an1_despesa SET dotacao_atualizada=  0.00 WHERE dotacao_atualizada IS NULL;
        UPDATE tmp_rreo_an1_despesa SET vl_empenhado_bimestre=  0.00 WHERE vl_empenhado_bimestre IS NULL;       
        UPDATE tmp_rreo_an1_despesa SET vl_empenhado_total =  0.00 WHERE vl_empenhado_total  IS NULL;
        UPDATE tmp_rreo_an1_despesa SET vl_liquidado_bimestre=  0.00 WHERE vl_liquidado_bimestre IS NULL;
        UPDATE tmp_rreo_an1_despesa SET vl_liquidado_total=  0.00 WHERE vl_liquidado_total IS NULL;
        UPDATE tmp_rreo_an1_despesa SET vl_pago_total=  0.00 WHERE vl_pago_total IS NULL;
        UPDATE tmp_rreo_an1_despesa SET  percentual=  0.00 WHERE percentual  IS NULL;
        UPDATE tmp_rreo_an1_despesa SET  saldo_liquidar =  0.00 WHERE saldo_liquidar   IS NULL;
    

        UPDATE tmp_rreo_an1_despesa SET

            dotacao_inicial        = ((SELECT coalesce(sum(dotacao_inicial), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''3.1.0.0.00.00.00.00.00''  )
                                            + (SELECT coalesce(sum(dotacao_inicial), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''3.2.0.0.00.00.00.00.00'')
                                            + (SELECT coalesce(sum(dotacao_inicial), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''3.3.0.0.00.00.00.00.00'')),

            creditos_adicionais = ((SELECT coalesce(sum(creditos_adicionais), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''3.1.0.0.00.00.00.00.00''  )
                                            + (SELECT coalesce(sum(creditos_adicionais), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''3.2.0.0.00.00.00.00.00'')
                                            + (SELECT coalesce(sum(creditos_adicionais), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''3.3.0.0.00.00.00.00.00'')),

            dotacao_atualizada = ((SELECT coalesce(sum(dotacao_atualizada), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''3.1.0.0.00.00.00.00.00''  )
                                            + (SELECT coalesce(sum(dotacao_atualizada), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''3.2.0.0.00.00.00.00.00'')
                                            + (SELECT coalesce(sum(dotacao_atualizada), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''3.3.0.0.00.00.00.00.00'')),


            vl_empenhado_bimestre = ((SELECT coalesce(sum(vl_empenhado_bimestre), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''3.1.0.0.00.00.00.00.00''  )
                                                  + (SELECT coalesce(sum(vl_empenhado_bimestre), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''3.2.0.0.00.00.00.00.00'')
                                                  + (SELECT coalesce(sum(vl_empenhado_bimestre), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''3.3.0.0.00.00.00.00.00'')),

            vl_empenhado_total = ((SELECT coalesce(sum(vl_empenhado_total), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''3.1.0.0.00.00.00.00.00''  )
                                            + (SELECT coalesce(sum(vl_empenhado_total), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''3.2.0.0.00.00.00.00.00'')
                                            + (SELECT coalesce(sum(vl_empenhado_total), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''3.3.0.0.00.00.00.00.00'')),

           vl_liquidado_bimestre = ((SELECT coalesce(sum(vl_liquidado_bimestre), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''3.1.0.0.00.00.00.00.00''  )
                                            + (SELECT coalesce(sum(vl_liquidado_bimestre), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''3.2.0.0.00.00.00.00.00'')
                                            + (SELECT coalesce(sum(vl_liquidado_bimestre), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''3.3.0.0.00.00.00.00.00'')),

           vl_liquidado_total = ((SELECT coalesce(sum(vl_liquidado_total), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''3.1.0.0.00.00.00.00.00''  )
                                            + (SELECT coalesce(sum(vl_liquidado_total), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''3.2.0.0.00.00.00.00.00'')
                                            + (SELECT coalesce(sum(vl_liquidado_total), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''3.3.0.0.00.00.00.00.00'')),
                                            
           vl_pago_total = ((SELECT coalesce(sum(vl_pago_total), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''3.1.0.0.00.00.00.00.00''  )
                                            + (SELECT coalesce(sum(vl_pago_total), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''3.2.0.0.00.00.00.00.00'')
                                            + (SELECT coalesce(sum(vl_pago_total), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''3.3.0.0.00.00.00.00.00'')),
                                            
           saldo_liquidar = (((SELECT coalesce(sum(dotacao_atualizada), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''3.1.0.0.00.00.00.00.00''  )
                                            + (SELECT coalesce(sum(dotacao_atualizada), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''3.2.0.0.00.00.00.00.00'')
                                            + (SELECT coalesce(sum(dotacao_atualizada), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''3.3.0.0.00.00.00.00.00''))
                                            - ((SELECT coalesce(sum(vl_empenhado_bimestre), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''3.1.0.0.00.00.00.00.00'')
                                            + (SELECT coalesce(sum(vl_empenhado_bimestre), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''3.2.0.0.00.00.00.00.00'')
                                            + (SELECT coalesce(sum(vl_empenhado_bimestre), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''3.3.0.0.00.00.00.00.00'')))


        WHERE cod_estrutural = ''3.0.0.0.00.00.00.00.00'';
        
              UPDATE tmp_rreo_an1_despesa SET

            dotacao_inicial        = ((SELECT coalesce(sum(dotacao_inicial), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''4.4.0.0.00.00.00.00.00''  )
                                            + (SELECT coalesce(sum(dotacao_inicial), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''4.5.0.0.00.00.00.00.00'')
                                            + (SELECT coalesce(sum(dotacao_inicial), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''4.6.0.0.00.00.00.00.00'')),

            creditos_adicionais = ((SELECT coalesce(sum(creditos_adicionais), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''4.4.0.0.00.00.00.00.00''  )
                                            + (SELECT coalesce(sum(creditos_adicionais), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''4.5.0.0.00.00.00.00.00'')
                                            + (SELECT coalesce(sum(creditos_adicionais), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''4.6.0.0.00.00.00.00.00'')),

            dotacao_atualizada = ((SELECT coalesce(sum(dotacao_atualizada), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''4.4.0.0.00.00.00.00.00''  )
                                            + (SELECT coalesce(sum(dotacao_atualizada), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''4.5.0.0.00.00.00.00.00'')
                                            + (SELECT coalesce(sum(dotacao_atualizada), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''4.6.0.0.00.00.00.00.00'')),


            vl_empenhado_bimestre = ((SELECT coalesce(sum(vl_empenhado_bimestre), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''4.4.0.0.00.00.00.00.00''  )
                                                  + (SELECT coalesce(sum(vl_empenhado_bimestre), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''4.5.0.0.00.00.00.00.00'')
                                                  + (SELECT coalesce(sum(vl_empenhado_bimestre), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''4.6.0.0.00.00.00.00.00'')),

            vl_empenhado_total = ((SELECT coalesce(sum(vl_empenhado_total), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''4.4.0.0.00.00.00.00.00''  )
                                            + (SELECT coalesce(sum(vl_empenhado_total), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''4.5.0.0.00.00.00.00.00'')
                                            + (SELECT coalesce(sum(vl_empenhado_total), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''4.6.0.0.00.00.00.00.00'')),

           vl_liquidado_bimestre = ((SELECT coalesce(sum(vl_liquidado_bimestre), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''4.4.0.0.00.00.00.00.00''  )
                                            + (SELECT coalesce(sum(vl_liquidado_bimestre), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''4.5.0.0.00.00.00.00.00'')
                                            + (SELECT coalesce(sum(vl_liquidado_bimestre), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''4.6.0.0.00.00.00.00.00'')),

           vl_liquidado_total = ((SELECT coalesce(sum(vl_liquidado_total), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''4.4.0.0.00.00.00.00.00''  )
                                            + (SELECT coalesce(sum(vl_liquidado_total), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''4.5.0.0.00.00.00.00.00'')
                                            + (SELECT coalesce(sum(vl_liquidado_total), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''4.6.0.0.00.00.00.00.00'')),
                                            
           vl_pago_total = ((SELECT coalesce(sum(vl_pago_total), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''4.4.0.0.00.00.00.00.00''  )
                                            + (SELECT coalesce(sum(vl_pago_total), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''4.5.0.0.00.00.00.00.00'')
                                            + (SELECT coalesce(sum(vl_pago_total), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''4.6.0.0.00.00.00.00.00'')),
                                            
           saldo_liquidar = (((SELECT coalesce(sum(dotacao_atualizada), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''4.4.0.0.00.00.00.00.00''  )
                                            + (SELECT coalesce(sum(dotacao_atualizada), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''4.5.0.0.00.00.00.00.00'')
                                            + (SELECT coalesce(sum(dotacao_atualizada), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''4.6.0.0.00.00.00.00.00''))
                                            - ((SELECT coalesce(sum(vl_empenhado_bimestre), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''4.4.0.0.00.00.00.00.00'')
                                            + (SELECT coalesce(sum(vl_empenhado_bimestre), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''4.5.0.0.00.00.00.00.00'')
                                            + (SELECT coalesce(sum(vl_empenhado_bimestre), 0.00) FROM tmp_rreo_an1_despesa where cod_estrutural = ''4.6.0.0.00.00.00.00.00'')))

        WHERE cod_estrutural = ''4.0.0.0.00.00.00.00.00'';
   
    ';
    EXECUTE stSql;
    
    
    stSql := 'SELECT * FROM tmp_rreo_an1_despesa ORDER BY grupo, nivel';
    
  
    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;

    DROP TABLE tmp_suplementacao_suplementada ; 
    DROP TABLE tmp_suplementacao_reduzida ; 
    DROP TABLE tmp_despesa_totais ; 
    DROP TABLE tmp_despesa ; 
    DROP TABLE tmp_rreo_an1_despesa ; 

    RETURN;
 
END;

$$ language 'plpgsql';