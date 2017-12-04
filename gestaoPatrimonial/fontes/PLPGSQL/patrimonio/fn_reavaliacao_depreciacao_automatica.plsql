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

CREATE TYPE reavaliacao_depreciacao_automatica AS (
      cod_bem                           INTEGER
    , descricao                         VARCHAR
    , dt_incorporacao                   DATE
    , dt_aquisicao                      DATE
    , competencia_incorporacao          TEXT
    , vl_bem                            NUMERIC
    , quota_depreciacao_anual           NUMERIC
    , quota_depreciacao_anual_acelerada NUMERIC
    , depreciacao_acelerada             BOOLEAN
    , cod_plano                         INTEGER
    , cod_reavaliacao                   INTEGER
    , dt_reavaliacao                    DATE
    , vida_util                         INTEGER
    , motivo                            VARCHAR
);

*/
CREATE OR REPLACE FUNCTION patrimonio.fn_reavaliacao_depreciacao_automatica( stExercicio   VARCHAR,
                                                                             stCompetencia VARCHAR,
                                                                             inCodNatureza INTEGER,
                                                                             inCodGrupo    INTEGER,
                                                                             inCodEspecie  INTEGER,
                                                                             stMotivo      VARCHAR ) RETURNS SETOF reavaliacao_depreciacao_automatica
AS $$ DECLARE
    
    stQuery                   VARCHAR;
    vlValorMinimoDepreciacao  NUMERIC(14,2);
    inCompetenciaDepreciacao  INTEGER;
    arDatas                   VARCHAR[];
    
    rcConfiguracao            RECORD; 
    rcReavaliacao             RECORD;
    
BEGIN
    
    -- Recupera a data final do mês
    arDatas := publico.mes(stExercicio, stCompetencia::INTEGER);
    
stQuery := 'SELECT parametro
                 , valor
              FROM administracao.configuracao
             WHERE parametro IN (''valor_minimo_depreciacao'',''competencia_depreciacao'',''substituir_depreciacao'')
               AND exercicio  = '|| quote_literal(stExercicio) ||'
               AND cod_modulo = 6';
               
EXECUTE stQuery INTO rcConfiguracao;

FOR rcConfiguracao IN EXECUTE stQuery LOOP
    IF rcConfiguracao.parametro = 'valor_minimo_depreciacao' THEN
        vlValorMinimoDepreciacao := rcConfiguracao.valor::NUMERIC;
    ELSEIF rcConfiguracao.parametro = 'competencia_depreciacao' THEN
        inCompetenciaDepreciacao := rcConfiguracao.valor::INTEGER;
    END IF;
END LOOP;

-- RECUPERA OS BENS QUE POSSUEM PLANO RELACIONADO
stQuery := '
CREATE TEMPORARY TABLE tmp_bem_depreciacao AS (
               SELECT  bem.cod_bem
                    ,  bem.descricao
                    ,  COALESCE(reavaliacao.dt_reavaliacao,bem.dt_incorporacao,bem.dt_aquisicao) AS dt_incorporacao
                    ,  bem.dt_aquisicao
                    ,  TO_CHAR(COALESCE(reavaliacao.dt_reavaliacao,bem.dt_incorporacao,bem.dt_aquisicao),''YYYYMM'' ) AS competencia_incorporacao
                    ,  COALESCE(reavaliacao.vl_reavaliacao, bem.vl_bem ) AS vl_bem
                    ,  CASE WHEN bem.quota_depreciacao_anual > 0
                            THEN bem.quota_depreciacao_anual
                            ELSE grupo.depreciacao
                       END AS quota_depreciacao_anual
                    ,  bem.quota_depreciacao_anual_acelerada
                    ,  bem.depreciacao_acelerada
                    ,  CASE WHEN bem_plano_depreciacao.cod_plano IS NOT NULL
                            THEN bem_plano_depreciacao.cod_plano
                            ELSE grupo_plano_depreciacao.cod_plano
                       END AS cod_plano
                    ,  reavaliacao.cod_reavaliacao
                    ,  reavaliacao.dt_reavaliacao
                    ,  reavaliacao.vida_util
                    ,  reavaliacao.motivo
                    
                 FROM patrimonio.bem

           INNER JOIN  patrimonio.especie
                   ON  especie.cod_natureza = bem.cod_natureza
                  AND  especie.cod_grupo    = bem.cod_grupo
                  AND  especie.cod_especie  = bem.cod_especie

           INNER JOIN  patrimonio.grupo
                   ON  grupo.cod_natureza = especie.cod_natureza
                  AND  grupo.cod_grupo    = especie.cod_grupo

           INNER JOIN  patrimonio.natureza
                   ON  natureza.cod_natureza = grupo.cod_natureza

            LEFT JOIN  patrimonio.grupo_plano_depreciacao
                   ON  grupo_plano_depreciacao.cod_grupo    = grupo.cod_grupo
                  AND  grupo_plano_depreciacao.cod_natureza = grupo.cod_natureza
                  AND  grupo_plano_depreciacao.exercicio = '|| quote_literal(stExercicio) ||'
                  
            LEFT JOIN  (  SELECT cod_bem
                               , MAX(timestamp) AS timestamp
                               , exercicio
                            FROM patrimonio.bem_plano_depreciacao
                           WHERE bem_plano_depreciacao.exercicio   = '|| quote_literal(stExercicio) ||'
                        GROUP BY cod_bem, exercicio
                       ) AS ultimo_plano_bem_depreciacao
                   ON  bem.cod_bem = ultimo_plano_bem_depreciacao.cod_bem

            LEFT JOIN  patrimonio.bem_plano_depreciacao
                   ON  ultimo_plano_bem_depreciacao.cod_bem   = bem_plano_depreciacao.cod_bem
                  AND  ultimo_plano_bem_depreciacao.timestamp = bem_plano_depreciacao.timestamp
                  AND  ultimo_plano_bem_depreciacao.exercicio = bem_plano_depreciacao.exercicio
                  AND  bem_plano_depreciacao.exercicio   = '|| quote_literal(stExercicio) ||'

            LEFT JOIN  (   SELECT MAX(cod_reavaliacao) AS cod_reavaliacao
                                , cod_bem
                             FROM patrimonio.reavaliacao
                         GROUP BY cod_bem
                       ) AS ultima_reavaliacao
                   ON  bem.cod_bem = ultima_reavaliacao.cod_bem

            LEFT JOIN  patrimonio.reavaliacao
                   ON  ultima_reavaliacao.cod_reavaliacao = reavaliacao.cod_reavaliacao
                  AND  ultima_reavaliacao.cod_bem         = reavaliacao.cod_bem

                WHERE  bem.depreciavel = true
                  AND  NOT EXISTS ( SELECT 1
                                      FROM patrimonio.bem_baixado
                                     WHERE bem_baixado.cod_bem = bem.cod_bem ) ';
    
    IF inCodNatureza IS NOT NULL THEN
        stQuery := stQuery||' AND bem.cod_natureza = '||inCodNatureza;
    END IF;

    IF inCodGrupo IS NOT NULL THEN
        stQuery := stQuery||' AND bem.cod_grupo = '||inCodGrupo;
    END IF;
    
    IF inCodEspecie IS NOT NULL THEN
        stQuery := stQuery||' AND bem.cod_especie = '||inCodEspecie;
    END IF;
    
    IF vlValorMinimoDepreciacao IS NOT NULL THEN
        stQuery := stQuery||' AND bem.vl_bem >= '||vlValorMinimoDepreciacao;
    END IF;
        
    stQuery := stQuery|| ' )';
    
    EXECUTE stQuery;

stQuery := '
    SELECT tmp_bem_depreciacao.*
      
      FROM tmp_bem_depreciacao
     WHERE TO_CHAR(tmp_bem_depreciacao.dt_aquisicao, ''YYYY'') < ' || quote_literal(stExercicio) || ' 
       AND NOT EXISTS (
			  SELECT 1
                            FROM patrimonio.reavaliacao
                          
                           WHERE reavaliacao.cod_bem = tmp_bem_depreciacao.cod_bem 
                             AND TO_CHAR(reavaliacao.dt_reavaliacao, ''YYYY'') = ' || quote_literal(stExercicio) || ' 
                             AND reavaliacao.dt_reavaliacao BETWEEN TO_DATE(''01/01/'|| stExercicio || ''', ''DD/MM/YYYY'') AND TO_DATE('|| quote_literal( arDatas[1] ) ||', ''DD/MM/YYYY'')
                             /* Recupera todos as reavaliações, inclusive dos meses anteriores e não somente a última, devido a sequencia dos meses depreciados.
                             AND reavaliacao.cod_reavaliacao = (SELECT MAX(reavaliacao.cod_reavaliacao)
                                                                  FROM patrimonio.reavaliacao
                                                                  WHERE reavaliacao.cod_bem = tmp_bem_depreciacao.cod_bem ) */
                   ) ';
        
    FOR rcReavaliacao IN EXECUTE stQuery
    LOOP
        RETURN next rcReavaliacao;
    END LOOP;

    DROP TABLE tmp_bem_depreciacao;

RETURN;
END;
$$ LANGUAGE 'plpgsql';