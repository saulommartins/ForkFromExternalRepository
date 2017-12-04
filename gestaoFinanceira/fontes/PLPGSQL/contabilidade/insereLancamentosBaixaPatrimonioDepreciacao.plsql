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
CREATE OR REPLACE FUNCTION contabilidade.fn_insere_lancamentos_baixa_patrimonio_depreciacao(VARCHAR, VARCHAR, INTEGER, DATE, INTEGER, VARCHAR, BOOLEAN) RETURNS VOID AS $$
DECLARE
    PstExercicio                ALIAS FOR $1;
    PstCodBem                   ALIAS FOR $2;
    pinTipoBaixa                ALIAS FOR $3;
    PdtDataBaixa                ALIAS FOR $4;
    PinCodHistorico             ALIAS FOR $5;
    PstTipo                     ALIAS FOR $6;
    PboEstorno                  ALIAS FOR $7;

    inCodEntidade               INTEGER := 0;
    inCodLote                   INTEGER := 0;
    inCodPlanoDeb               INTEGER := 0;
    inCodPlanoCred              INTEGER := 0;
    inSequencia                 INTEGER := 0;
    inCodLancBaixa              INTEGER := 0;
    stNomeLote                  VARCHAR := '';
    stComplemento               VARCHAR := '';
    stSql                       VARCHAR := '';
    stFiltro                    VARCHAR := '';
    reBaixaDepreciacao          RECORD;
BEGIN
    
    -- Recupera os cod_planos do bem, a serem usados para débito e crédito
    stSql := '
        SELECT bem_comprado.cod_entidade
            ,  SUM((
                    SELECT vl_acumulado
                      FROM patrimonio.fn_depreciacao_acumulada(bem.cod_bem)
                        AS retorno (  cod_bem            INTEGER
                                    , vl_acumulado       NUMERIC
                                    , vl_atualizado      NUMERIC
                                    , vl_bem             NUMERIC
                                    , min_competencia    VARCHAR
                                    , max_competencia    VARCHAR
                                   )
                     WHERE retorno.cod_bem = bem.cod_bem
			      )) AS vl_lancamento_contabil
                , CASE WHEN bem_plano_analitica.cod_plano IS NOT NULL
                       THEN bem_plano_analitica.cod_plano
                       ELSE grupo_plano_bem.cod_plano
                  END AS cod_plano_bem
                , CASE WHEN bem_plano_depreciacao.cod_plano IS NOT NULL
                      THEN bem_plano_depreciacao.cod_plano
                      ELSE grupo_plano_depreciacao.cod_plano
                  END AS cod_plano_depreciacao
                , tipo_natureza.cod_natureza
                , tipo_natureza.cod_grupo
                , tipo_natureza.codigo AS codigo_tipo_natureza
                , tipo_natureza.nom_natureza
                
            FROM patrimonio.bem
      
      INNER JOIN patrimonio.bem_comprado
              ON bem_comprado.cod_bem = bem.cod_bem

       LEFT JOIN (
               SELECT bem_plano_analitica.cod_bem
                    , bem_plano_analitica.cod_plano
                    , bem_plano_analitica.exercicio
                                           
                 FROM patrimonio.bem_plano_analitica
      
           INNER JOIN contabilidade.plano_analitica
                   ON plano_analitica.cod_plano = bem_plano_analitica.cod_plano
                  AND plano_analitica.exercicio = bem_plano_analitica.exercicio
              
                WHERE bem_plano_analitica.timestamp::timestamp = ( SELECT MAX(bem_plano.timestamp::timestamp) AS timestamp 
                                                                       FROM patrimonio.bem_plano_analitica AS bem_plano
                                                                      
                                                                      WHERE bem_plano_analitica.cod_bem   = bem_plano.cod_bem
                                                                        AND bem_plano_analitica.exercicio = bem_plano.exercicio
                                                                        AND bem_plano_analitica.exercicio   = '|| quote_literal(PstExercicio) ||'
                                                                   
                                                                   GROUP BY bem_plano.cod_bem
                                                                          , bem_plano.exercicio )
                  AND bem_plano_analitica.exercicio   = '|| quote_literal(PstExercicio) ||'
             ORDER BY timestamp DESC
             
            )AS bem_plano_analitica
           ON bem_plano_analitica.cod_bem = bem.cod_bem
      
    LEFT JOIN (
                SELECT bem.cod_bem
                     , grupo_plano_analitica.cod_plano
                     , grupo_plano_analitica.exercicio
             
                  FROM patrimonio.grupo_plano_analitica
        
            INNER JOIN patrimonio.grupo
                    ON grupo.cod_natureza = grupo_plano_analitica.cod_natureza
                   AND grupo.cod_grupo    = grupo_plano_analitica.cod_grupo
            
            INNER JOIN patrimonio.especie
                    ON especie.cod_grupo    = grupo.cod_grupo
                   AND especie.cod_natureza = grupo.cod_natureza
            
            INNER JOIN patrimonio.bem
                    ON bem.cod_especie  = especie.cod_especie
                   AND bem.cod_grupo    = especie.cod_grupo
                   AND bem.cod_natureza = especie.cod_natureza
                
                 WHERE grupo_plano_analitica.exercicio = '|| quote_literal(PstExercicio) ||'
                  
             ) AS grupo_plano_bem
            ON grupo_plano_bem.cod_bem = bem.cod_bem
      
     LEFT JOIN (
               SELECT bem_plano_depreciacao.cod_bem
                    , bem_plano_depreciacao.cod_plano
                    , bem_plano_depreciacao.exercicio
                                           
                 FROM patrimonio.bem_plano_depreciacao 
      
           INNER JOIN contabilidade.plano_analitica
                   ON plano_analitica.cod_plano = bem_plano_depreciacao.cod_plano
                  AND plano_analitica.exercicio = bem_plano_depreciacao.exercicio
      
                WHERE bem_plano_depreciacao.timestamp::timestamp = ( SELECT MAX(bem_plano.timestamp::timestamp) AS timestamp 
                                                                       FROM patrimonio.bem_plano_depreciacao AS bem_plano
                                                                      
                                                                      WHERE bem_plano_depreciacao.cod_bem   = bem_plano.cod_bem
                                                                        AND bem_plano_depreciacao.exercicio = bem_plano.exercicio
                                                                        AND bem_plano_depreciacao.exercicio = '|| quote_literal(PstExercicio) ||'
                                                                   
                                                                   GROUP BY bem_plano.cod_bem
                                                                          , bem_plano.exercicio )
                  AND bem_plano_depreciacao.exercicio   = '|| quote_literal(PstExercicio) ||'
             ORDER BY timestamp DESC
             
            )AS bem_plano_depreciacao
             ON bem_plano_depreciacao.cod_bem = bem.cod_bem
          
      LEFT JOIN (
                  SELECT bem.cod_bem
                       , grupo_plano_depreciacao.cod_plano
                       , grupo_plano_depreciacao.exercicio
             
                   FROM patrimonio.grupo_plano_depreciacao
        
             INNER JOIN patrimonio.grupo
                     ON grupo.cod_natureza = grupo_plano_depreciacao.cod_natureza
                    AND grupo.cod_grupo    = grupo_plano_depreciacao.cod_grupo
             
             INNER JOIN patrimonio.especie
                     ON especie.cod_grupo    = grupo.cod_grupo
                    AND especie.cod_natureza = grupo.cod_natureza
             
             INNER JOIN patrimonio.bem
                     ON bem.cod_especie  = especie.cod_especie
                    AND bem.cod_grupo    = especie.cod_grupo
                    AND bem.cod_natureza = especie.cod_natureza
                 
                  WHERE grupo_plano_depreciacao.exercicio = '|| quote_literal(PstExercicio) ||'
                  
             ) AS grupo_plano_depreciacao
            ON grupo_plano_depreciacao.cod_bem = bem.cod_bem

    INNER JOIN
             ( SELECT bem.cod_bem
                    , bem.cod_natureza
                    , tipo_natureza.codigo
                    , natureza.nom_natureza
                    , grupo.cod_grupo
                    
                 FROM patrimonio.bem

           INNER JOIN patrimonio.especie
                   ON especie.cod_especie  = bem.cod_especie
                  AND especie.cod_grupo    = bem.cod_grupo
                  AND especie.cod_natureza = bem.cod_natureza

           INNER JOIN patrimonio.grupo
                   ON grupo.cod_grupo    = especie.cod_grupo
                  AND grupo.cod_natureza = especie.cod_natureza
           
           INNER JOIN patrimonio.natureza
                   ON natureza.cod_natureza = grupo.cod_natureza

           INNER JOIN patrimonio.tipo_natureza
                   ON tipo_natureza.codigo = natureza.cod_tipo
            
            ) AS tipo_natureza
            ON tipo_natureza.cod_bem = bem.cod_bem
          
         WHERE EXISTS (
		    SELECT 1
			  FROM patrimonio.depreciacao
              
	    LEFT JOIN patrimonio.depreciacao_anulada
               ON depreciacao_anulada.cod_depreciacao = depreciacao.cod_depreciacao
              AND depreciacao_anulada.cod_bem         = depreciacao.cod_bem
              AND depreciacao_anulada.timestamp       = depreciacao.timestamp
                               
		    WHERE bem.cod_bem = depreciacao.cod_bem
			   AND depreciacao_anulada.cod_depreciacao IS NULL
               AND depreciacao_anulada.cod_bem         IS NULL
               AND depreciacao_anulada.timestamp       IS NULL
			
              )
            AND bem.cod_bem IN ('|| PstCodBem ||')

       GROUP BY bem_comprado.cod_entidade
              , cod_plano_bem
              , cod_plano_depreciacao
              , tipo_natureza.cod_natureza
              , tipo_natureza.cod_grupo
              , codigo_tipo_natureza
              , tipo_natureza.nom_natureza ';

    CREATE TABLE tmp_relacao_bem_depreciacao (
         sequencia               INTEGER        NOT NULL 
       , cod_plano_bem           INTEGER        NOT NULL
       , cod_plano_depreciacao   INTEGER        NOT NULL
       , nom_natureza            VARCHAR (60)   NOT NULL
       , cod_natureza            INTEGER        NOT NULL
       , cod_grupo               INTEGER        NOT NULL
       , codigo_tipo_natureza    INTEGER        NOT NULL
       , codigo_entidade         INTEGER        NOT NULL
       , PRIMARY KEY ( sequencia, cod_plano_bem, cod_plano_depreciacao, cod_natureza, cod_grupo, codigo_tipo_natureza )
    );
        
    FOR reBaixaDepreciacao IN EXECUTE stSql
    LOOP
    
        -- Conforme parametro passado verifica se é ou não Estorno, caso seja, inverte as contas de lançamento.
        IF PboEstorno = FALSE THEN
            inCodPlanoDeb  := reBaixaDepreciacao.cod_plano_depreciacao;
            inCodPlanoCred := reBaixaDepreciacao.cod_plano_bem;
            stNomeLote     := 'Lançamento do valor líquido contábil de depreciação de baixa patrimonial na data: ' || TO_CHAR(PdtDataBaixa, 'DD/MM/YYYY');
            stComplemento  := 'Lançamento do valor líquido contábil de depreciação de Bem na data: ' || TO_CHAR(PdtDataBaixa, 'DD/MM/YYYY');
        ELSE
            inCodPlanoDeb  := reBaixaDepreciacao.cod_plano_bem;
            inCodPlanoCred := reBaixaDepreciacao.cod_plano_depreciacao;
            stNomeLote     := 'Lançamento de Estorno do valor líquido contábil de baixa patrimonial de Bem na data: ' || TO_CHAR(PdtDataBaixa, 'DD/MM/YYYY');
            stComplemento  := 'Estorno do valor líquido contábil de depreciação de Bem na data: ' || TO_CHAR(PdtDataBaixa, 'DD/MM/YYYY');
        END IF;
        
        -- Caso o cod_entidade for diferente, é criado um novo lote
        IF inCodEntidade <> reBaixaDepreciacao.cod_entidade THEN
            inCodEntidade := reBaixaDepreciacao.cod_entidade;

            -- Recupera o último cod_lote a ser inserido na tabela contabilidade.lancamento
            stFiltro  :=            'WHERE exercicio    = ' || quote_literal(PstExercicio);
            stFiltro  := stFiltro || ' AND tipo         = ' || quote_literal(PstTipo);
            stFiltro  := stFiltro || ' AND cod_entidade = ' || reBaixaDepreciacao.cod_entidade;
            inCodLote := publico.fn_proximo_cod('cod_lote','contabilidade.lote', stFiltro);
            
            INSERT INTO contabilidade.lote
                (cod_lote, exercicio, tipo, cod_entidade, nom_lote, dt_lote)
            VALUES
                (inCodLote, PstExercicio, PstTipo, reBaixaDepreciacao.cod_entidade, stNomeLote, PdtDataBaixa);
                
        END IF;
        
         -- Recupera a ultima sequencia de contabilidade.lancamento. Será uma para cada lancamento conforme o último lote inserido.
        stFiltro    :=       '     WHERE exercicio    = ' || quote_literal(PstExercicio);
        stFiltro    := stFiltro || ' AND tipo         = ' || quote_literal(PstTipo);
        stFiltro    := stFiltro || ' AND cod_entidade = ' || reBaixaDepreciacao.cod_entidade;
        stFiltro    := stFiltro || ' AND cod_lote     = ' || inCodLote;
        inSequencia := publico.fn_proximo_cod('sequencia','contabilidade.lancamento', stFiltro);

        INSERT INTO contabilidade.lancamento
            (sequencia, cod_lote, tipo, exercicio, cod_entidade, cod_historico, complemento)
        VALUES
            (inSequencia, inCodLote, PstTipo, PstExercicio, reBaixaDepreciacao.cod_entidade, PinCodHistorico, stComplemento);
            
        -- São inseridos 2 registros um a débito (valor positivo) e outro a crédito (valor negativo)
        IF inCodPlanoDeb IS NOT NULL AND inCodPlanoCred IS NOT NULL THEN
            --Insere dados de Crédito
            INSERT INTO contabilidade.valor_lancamento
                (sequencia, exercicio, tipo, cod_lote, cod_entidade, tipo_valor, vl_lancamento)
            VALUES
                (inSequencia, PstExercicio, PstTipo, inCodLote, reBaixaDepreciacao.cod_entidade, 'C', (reBaixaDepreciacao.vl_lancamento_contabil * -1) );
            
            INSERT INTO contabilidade.conta_credito
                (sequencia, exercicio, tipo, cod_lote, cod_entidade, tipo_valor, cod_plano )
            VALUES
                (inSequencia, PstExercicio, PstTipo, inCodLote, reBaixaDepreciacao.cod_entidade, 'C', inCodPlanoCred );
            
            --Insere dados de Débito
            INSERT INTO contabilidade.valor_lancamento
                (sequencia, exercicio, tipo, cod_lote, cod_entidade, tipo_valor, vl_lancamento)
            VALUES
                (inSequencia, PstExercicio, PstTipo, inCodLote, reBaixaDepreciacao.cod_entidade,'D', (reBaixaDepreciacao.vl_lancamento_contabil) );
                
            INSERT INTO contabilidade.conta_debito
                (sequencia, exercicio, tipo, cod_lote, cod_entidade, tipo_valor, cod_plano )
            VALUES
                (inSequencia, PstExercicio, PstTipo, inCodLote, reBaixaDepreciacao.cod_entidade, 'D', inCodPlanoDeb );
        ELSE
            RAISE EXCEPTION 'Deve ser informada pelo menos uma conta de débito ou crédito, para o grupo : % - %', reBaixaDepreciacao.cod_grupo, reBaixaDepreciacao.nom_grupo;
        END IF;
        
        -- Adiciona os valores da consulta, juntamente com a sequencia, para relacionar os cod_planos com os bens.
        INSERT INTO tmp_relacao_bem_depreciacao (
            sequencia
          , cod_plano_bem
          , cod_plano_depreciacao
          , nom_natureza
          , cod_natureza
          , cod_grupo
          , codigo_tipo_natureza
          , codigo_entidade
        ) VALUES (
            inSequencia
          , reBaixaDepreciacao.cod_plano_bem
          , reBaixaDepreciacao.cod_plano_depreciacao
          , reBaixaDepreciacao.nom_natureza
          , reBaixaDepreciacao.cod_natureza
          , reBaixaDepreciacao.cod_grupo
          , reBaixaDepreciacao.codigo_tipo_natureza
          , reBaixaDepreciacao.cod_entidade
        );

    END LOOP;
    
    -- Recupera bem a bem e seus dados, para a inserção.
    stSql := '
        SELECT * 
          FROM
          (    SELECT bem.cod_bem
                    , bem_comprado.cod_entidade
                    ,  SUM((
                            SELECT vl_acumulado
                              FROM patrimonio.fn_depreciacao_acumulada(bem.cod_bem)
                                AS retorno (  cod_bem            INTEGER
                                            , vl_acumulado       NUMERIC
                                            , vl_atualizado      NUMERIC
                                            , vl_bem             NUMERIC
                                            , min_competencia    VARCHAR
                                            , max_competencia    VARCHAR
                                           )
                             WHERE retorno.cod_bem = bem.cod_bem
                          )) AS vl_lancamento_contabil
                        , CASE WHEN bem_plano_analitica.cod_plano IS NOT NULL
                               THEN bem_plano_analitica.cod_plano
                               ELSE grupo_plano_bem.cod_plano
                          END AS cod_plano_bem
                        , CASE WHEN bem_plano_depreciacao.cod_plano IS NOT NULL
                              THEN bem_plano_depreciacao.cod_plano
                              ELSE grupo_plano_depreciacao.cod_plano
                          END AS cod_plano_depreciacao
                        , tipo_natureza.cod_natureza
                        , tipo_natureza.cod_grupo
                        , tipo_natureza.codigo AS codigo_tipo_natureza
                        , tipo_natureza.nom_natureza
                        
                    FROM patrimonio.bem
              
              INNER JOIN patrimonio.bem_comprado
                      ON bem_comprado.cod_bem = bem.cod_bem
        
               LEFT JOIN (
                       SELECT bem_plano_analitica.cod_bem
                            , bem_plano_analitica.cod_plano
                            , bem_plano_analitica.exercicio
                                                   
                         FROM patrimonio.bem_plano_analitica
              
                   INNER JOIN contabilidade.plano_analitica
                           ON plano_analitica.cod_plano = bem_plano_analitica.cod_plano
                          AND plano_analitica.exercicio = bem_plano_analitica.exercicio
                      
                        WHERE bem_plano_analitica.timestamp::timestamp = ( SELECT MAX(bem_plano.timestamp::timestamp) AS timestamp 
                                                                               FROM patrimonio.bem_plano_analitica AS bem_plano
                                                                              
                                                                              WHERE bem_plano_analitica.cod_bem   = bem_plano.cod_bem
                                                                                AND bem_plano_analitica.exercicio = bem_plano.exercicio
                                                                                AND bem_plano_analitica.exercicio   = '|| quote_literal(PstExercicio) ||'
                                                                           
                                                                           GROUP BY bem_plano.cod_bem
                                                                                  , bem_plano.exercicio )
                          AND bem_plano_analitica.exercicio   = '|| quote_literal(PstExercicio) ||'
                     ORDER BY timestamp DESC
                     
                    )AS bem_plano_analitica
                   ON bem_plano_analitica.cod_bem = bem.cod_bem
              
            LEFT JOIN (
                        SELECT bem.cod_bem
                             , grupo_plano_analitica.cod_plano
                             , grupo_plano_analitica.exercicio
                     
                          FROM patrimonio.grupo_plano_analitica
                
                    INNER JOIN patrimonio.grupo
                            ON grupo.cod_natureza = grupo_plano_analitica.cod_natureza
                           AND grupo.cod_grupo    = grupo_plano_analitica.cod_grupo
                    
                    INNER JOIN patrimonio.especie
                            ON especie.cod_grupo    = grupo.cod_grupo
                           AND especie.cod_natureza = grupo.cod_natureza
                    
                    INNER JOIN patrimonio.bem
                            ON bem.cod_especie  = especie.cod_especie
                           AND bem.cod_grupo    = especie.cod_grupo
                           AND bem.cod_natureza = especie.cod_natureza
                        
                         WHERE grupo_plano_analitica.exercicio = '|| quote_literal(PstExercicio) ||'
                          
                     ) AS grupo_plano_bem
                    ON grupo_plano_bem.cod_bem = bem.cod_bem
              
             LEFT JOIN (
                       SELECT bem_plano_depreciacao.cod_bem
                            , bem_plano_depreciacao.cod_plano
                            , bem_plano_depreciacao.exercicio
                                                   
                         FROM patrimonio.bem_plano_depreciacao 
              
                   INNER JOIN contabilidade.plano_analitica
                           ON plano_analitica.cod_plano = bem_plano_depreciacao.cod_plano
                          AND plano_analitica.exercicio = bem_plano_depreciacao.exercicio
              
                        WHERE bem_plano_depreciacao.timestamp::timestamp = ( SELECT MAX(bem_plano.timestamp::timestamp) AS timestamp 
                                                                               FROM patrimonio.bem_plano_depreciacao AS bem_plano
                                                                              
                                                                              WHERE bem_plano_depreciacao.cod_bem   = bem_plano.cod_bem
                                                                                AND bem_plano_depreciacao.exercicio = bem_plano.exercicio
                                                                                AND bem_plano_depreciacao.exercicio = '|| quote_literal(PstExercicio) ||'
                                                                           
                                                                           GROUP BY bem_plano.cod_bem
                                                                                  , bem_plano.exercicio )
                          AND bem_plano_depreciacao.exercicio   = '|| quote_literal(PstExercicio) ||'
                     ORDER BY timestamp DESC
                     
                    )AS bem_plano_depreciacao
                     ON bem_plano_depreciacao.cod_bem = bem.cod_bem
                  
              LEFT JOIN (
                          SELECT bem.cod_bem
                               , grupo_plano_depreciacao.cod_plano
                               , grupo_plano_depreciacao.exercicio
                     
                           FROM patrimonio.grupo_plano_depreciacao
                
                     INNER JOIN patrimonio.grupo
                             ON grupo.cod_natureza = grupo_plano_depreciacao.cod_natureza
                            AND grupo.cod_grupo    = grupo_plano_depreciacao.cod_grupo
                     
                     INNER JOIN patrimonio.especie
                             ON especie.cod_grupo    = grupo.cod_grupo
                            AND especie.cod_natureza = grupo.cod_natureza
                     
                     INNER JOIN patrimonio.bem
                             ON bem.cod_especie  = especie.cod_especie
                            AND bem.cod_grupo    = especie.cod_grupo
                            AND bem.cod_natureza = especie.cod_natureza
                         
                          WHERE grupo_plano_depreciacao.exercicio = '|| quote_literal(PstExercicio) ||'
                          
                     ) AS grupo_plano_depreciacao
                    ON grupo_plano_depreciacao.cod_bem = bem.cod_bem
        
            INNER JOIN
                     ( SELECT bem.cod_bem
                            , bem.cod_natureza
                            , tipo_natureza.codigo
                            , natureza.nom_natureza
                            , grupo.cod_grupo
                            
                         FROM patrimonio.bem
        
                   INNER JOIN patrimonio.especie
                           ON especie.cod_especie  = bem.cod_especie
                          AND especie.cod_grupo    = bem.cod_grupo
                          AND especie.cod_natureza = bem.cod_natureza
        
                   INNER JOIN patrimonio.grupo
                           ON grupo.cod_grupo    = especie.cod_grupo
                          AND grupo.cod_natureza = especie.cod_natureza
                   
                   INNER JOIN patrimonio.natureza
                           ON natureza.cod_natureza = grupo.cod_natureza
        
                   INNER JOIN patrimonio.tipo_natureza
                           ON tipo_natureza.codigo = natureza.cod_tipo
                    
                    ) AS tipo_natureza
                    ON tipo_natureza.cod_bem = bem.cod_bem
                 
                 WHERE EXISTS (
                    SELECT 1
                      FROM patrimonio.depreciacao
                      
                LEFT JOIN patrimonio.depreciacao_anulada
                       ON depreciacao_anulada.cod_depreciacao = depreciacao.cod_depreciacao
                      AND depreciacao_anulada.cod_bem         = depreciacao.cod_bem
                      AND depreciacao_anulada.timestamp       = depreciacao.timestamp
                                       
                    WHERE bem.cod_bem = depreciacao.cod_bem
                       AND depreciacao_anulada.cod_depreciacao IS NULL
                       AND depreciacao_anulada.cod_bem         IS NULL
                       AND depreciacao_anulada.timestamp       IS NULL
                    
                     )
                   AND bem.cod_bem IN ('|| PstCodBem ||')
        
               GROUP BY  bem_comprado.cod_entidade
                       , cod_plano_bem
                       , cod_plano_depreciacao
                       , tipo_natureza.cod_natureza
                       , tipo_natureza.cod_grupo
                       , codigo_tipo_natureza
                       , tipo_natureza.nom_natureza
                       , bem.cod_bem
            ) AS tbl
        
         INNER JOIN tmp_relacao_bem_depreciacao
                 ON tmp_relacao_bem_depreciacao.cod_plano_bem         = tbl.cod_plano_bem
                AND tmp_relacao_bem_depreciacao.cod_plano_depreciacao = tbl.cod_plano_depreciacao
                AND tmp_relacao_bem_depreciacao.cod_natureza          = tbl.cod_natureza
                AND tmp_relacao_bem_depreciacao.cod_grupo             = tbl.cod_grupo
                AND tmp_relacao_bem_depreciacao.codigo_tipo_natureza  = tbl.codigo_tipo_natureza
          
           ORDER BY tbl.cod_bem ';

    FOR reBaixaDepreciacao IN EXECUTE stSql
    LOOP
        -- Recupera o último id de lançamento de baixa de patrimonio deprecicao
        inCodLancBaixa := publico.fn_proximo_cod('id','contabilidade.lancamento_baixa_patrimonio_depreciacao','');
        
        -- Relaciona a baixa com o bem.
        INSERT INTO contabilidade.lancamento_baixa_patrimonio_depreciacao
            (id, timestamp, exercicio, cod_entidade, tipo, cod_lote, sequencia, cod_bem, estorno )
        VALUES
            (inCodLancBaixa, ('now'::text)::timestamp(3), PstExercicio, reBaixaDepreciacao.cod_entidade, PstTipo, inCodLote, reBaixaDepreciacao.sequencia, reBaixaDepreciacao.cod_bem, PboEstorno);
    END LOOP;
    
    DROP TABLE tmp_relacao_bem_depreciacao;
    
END;
$$ LANGUAGE 'plpgsql';