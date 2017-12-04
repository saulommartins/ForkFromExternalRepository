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
* Script de função PLPGSQL
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
*/

CREATE OR REPLACE FUNCTION contabilidade.fn_insere_lancamentos_depreciacao(VARCHAR,VARCHAR,INTEGER,INTEGER,VARCHAR,VARCHAR,BOOLEAN) RETURNS VOID AS $$
DECLARE
    
    PstExercicio                ALIAS FOR $1;
    PstMesCompetencia           ALIAS FOR $2;
    PinCodEntidade              ALIAS FOR $3;
    PinCodHistorico             ALIAS FOR $4;
    PstTipo                     ALIAS FOR $5;
    PstComplemento              ALIAS FOR $6;
    PboEstorno                  ALIAS FOR $7;
    
    inCodLote                   INTEGER := 0;
    inCodPlanoDeb               INTEGER := 0;
    inCodPlanoCred              INTEGER := 0;
    inCodPlanoEstrutural        INTEGER := 0;
    inSequencia                 INTEGER := 0;
    inCodLancDepreciacao        INTEGER := 0;
    inCodDepreciacao            INTEGER := 0;
    stDataLote                  DATE;
    chTipo                      CHAR    := '';
    stCodEstruturalDepreciacao  VARCHAR;
    stNomeLote                  VARCHAR := '';
    stSql                       VARCHAR := '';
    stFiltro                    VARCHAR := '';
    reRegistro                  RECORD;
    
BEGIN

    -- Verifica se existe depreciação na competência que não esteja anulada
    SELECT INTO inCodDepreciacao 
                cod_depreciacao
      FROM patrimonio.depreciacao
     WHERE competencia =  PstExercicio || PstMesCompetencia
       AND NOT EXISTS ( SELECT 1 
                         FROM patrimonio.depreciacao_anulada
                        WHERE depreciacao_anulada.cod_depreciacao = depreciacao.cod_depreciacao
                          AND depreciacao_anulada.cod_bem         = depreciacao.cod_bem
                          AND depreciacao_anulada.timestamp       = depreciacao.timestamp
                      );
    
    IF inCodDepreciacao IS NULL THEN
        RAISE EXCEPTION 'Não existem bens depreciados na competência: % !',  PstMesCompetencia || '/' || PstExercicio;
    END IF;

    -- Caso tenha informado uma string com mais de 1 caracter trunca
    chTipo := substr(trim(PstTipo),1,1);

    -- Se estiver no mês da competência, deve ser o dia atual, senão será o último dia do mês caso estiver em mês posterior
    IF TO_CHAR(CURRENT_DATE, 'MM') = PstMesCompetencia THEN
        stDataLote := CURRENT_DATE;
    ELSEIF TO_CHAR(CURRENT_DATE, 'MM') > PstMesCompetencia THEN
        stDataLote := PstExercicio || '-' || PstMesCompetencia || '-' || calculaUltimoDiaMes(PstExercicio::INTEGER , PstMesCompetencia::INTEGER);
    END IF;
    
    IF PboEstorno = false THEN
        stNomeLote := 'Lançamento de Depreciação no Mês: ' || PstMesCompetencia || '/' ||PstExercicio;
    ELSE
        stNomeLote := 'Lançamento de Estorno de Depreciação no Mês: ' || PstMesCompetencia || '/' || PstExercicio;
    END IF;
    
    -- Recupera o último cod_lote a ser inserido na tabela contabilidade.lancamento
    stFiltro  :=            'WHERE exercicio    = ' || quote_literal(PstExercicio);
    stFiltro  := stFiltro || ' AND tipo         = ' || quote_literal(chTipo);
    stFiltro  := stFiltro || ' AND cod_entidade = ' || PinCodEntidade;
    inCodLote := publico.fn_proximo_cod('cod_lote','contabilidade.lote',stFiltro);
    
    INSERT INTO contabilidade.lote
        (cod_lote, exercicio, tipo, cod_entidade, nom_lote, dt_lote)
    VALUES
        (inCodLote, PstExercicio, chTipo, PinCodEntidade, stNomeLote, stDataLote);

    -- Recupera as depreciações, e seus valores agrupados por cod_plano, agrupados por grupo ou bem.
    stSql := '
          SELECT  depreciacao.cod_depreciacao
                , COALESCE( SUM ( depreciacao.vl_depreciado ), 0.00) AS vl_depreciado
                , CASE WHEN bem_plano_depreciacao.cod_plano IS NOT NULL
                       THEN bem_plano_depreciacao.cod_plano
                       ELSE grupo_plano_depreciacao.cod_plano
                  END AS cod_plano_final                
                , tipo_natureza.cod_natureza
                , tipo_natureza.cod_grupo
                , tipo_natureza.codigo AS codigo_tipo_natureza
                , tipo_natureza.nom_natureza
      
             FROM patrimonio.depreciacao
       
       INNER JOIN patrimonio.bem_comprado
               ON bem_comprado.cod_bem = depreciacao.cod_bem
        
        LEFT JOIN (
                  SELECT bem_plano_depreciacao.cod_bem
                       , bem_plano_depreciacao.cod_plano 
                       , bem_plano_depreciacao.exercicio
                       , MAX(bem_plano_depreciacao.timestamp::timestamp) AS timestamp
                       , plano_conta.cod_estrutural
                       , plano_conta.nom_conta AS nom_conta_depreciacao
                       
                    FROM patrimonio.bem_plano_depreciacao 
        
               LEFT JOIN contabilidade.plano_analitica
                      ON plano_analitica.cod_plano = bem_plano_depreciacao.cod_plano
                     AND plano_analitica.exercicio = bem_plano_depreciacao.exercicio
        
               LEFT JOIN contabilidade.plano_conta
                      ON plano_conta.cod_conta = plano_analitica.cod_conta
                     AND plano_conta.exercicio = plano_analitica.exercicio
                       
                   WHERE bem_plano_depreciacao.timestamp::timestamp = ( SELECT MAX(bem_plano.timestamp::timestamp) AS timestamp 
                                                                          FROM patrimonio.bem_plano_depreciacao AS bem_plano
                                                                         
                                                                        WHERE bem_plano_depreciacao.cod_bem   = bem_plano.cod_bem
                                                                          AND bem_plano_depreciacao.exercicio = bem_plano.exercicio
                                                                          AND bem_plano_depreciacao.exercicio   = '|| quote_literal(PstExercicio) ||'
                                                                     
                                                                     GROUP BY bem_plano.cod_bem
                                                                            , bem_plano.exercicio )
                    AND bem_plano_depreciacao.exercicio = '|| quote_literal(PstExercicio) ||'
                    
                GROUP BY bem_plano_depreciacao.cod_bem
                       , bem_plano_depreciacao.cod_plano
                       , bem_plano_depreciacao.exercicio
                       , plano_conta.cod_estrutural
                       , plano_conta.nom_conta 
                
                ORDER BY timestamp DESC
                
              )AS bem_plano_depreciacao
               ON bem_plano_depreciacao.cod_bem = depreciacao.cod_bem
        
        LEFT JOIN ( SELECT grupo_plano_depreciacao.cod_plano
                         , bem.cod_bem
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
                 ON grupo_plano_depreciacao.cod_bem = depreciacao.cod_bem

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
                 ON tipo_natureza.cod_bem = depreciacao.cod_bem
        
          WHERE competencia = '|| quote_literal( PstExercicio || PstMesCompetencia) ||'
            AND bem_comprado.cod_entidade = '|| PinCodEntidade ||'
            AND NOT EXISTS ( SELECT 1 
                               FROM patrimonio.depreciacao_anulada
                              WHERE depreciacao_anulada.cod_depreciacao = depreciacao.cod_depreciacao
                                AND depreciacao_anulada.cod_bem         = depreciacao.cod_bem
                                AND depreciacao_anulada.timestamp       = depreciacao.timestamp
                            )

        GROUP BY depreciacao.cod_depreciacao
               , cod_plano_final
               , tipo_natureza.cod_natureza
               , tipo_natureza.codigo
               , tipo_natureza.nom_natureza
               , tipo_natureza.cod_grupo 
               
        ORDER BY tipo_natureza.cod_natureza
               , tipo_natureza.cod_grupo
               , codigo_tipo_natureza ';

    CREATE TABLE tmp_relacao_bem (
         cod_depreciacao         INTEGER        NOT NULL
       , vl_depreciado           NUMERIC        NOT NULL
       , cod_plano_final         INTEGER        NOT NULL
       , nom_natureza            VARCHAR (60)   NOT NULL
       , cod_natureza            INTEGER        NOT NULL
       , cod_grupo               INTEGER        NOT NULL
       , codigo_tipo_natureza    INTEGER        NOT NULL
       , sequencia               INTEGER        NOT NULL
       , PRIMARY KEY ( sequencia, cod_depreciacao, cod_plano_final, cod_natureza, cod_grupo, codigo_tipo_natureza )
    );
    
    FOR reRegistro IN EXECUTE stSql
    LOOP
    
        -- Verifica se está configurada um tipo de natureza para a natureza do Grupo
        IF reRegistro.codigo_tipo_natureza = 0 OR reRegistro.codigo_tipo_natureza != 1 AND reRegistro.codigo_tipo_natureza != 2
        THEN
            RAISE EXCEPTION 'Necessário configurar um Tipo de Natureza ( 1 - Bens móveis ou 2 - Bens imóveis ) para a Natureza: %', reRegistro.cod_natureza || ' - ' || reRegistro.nom_natureza;
        END IF;
        
        -- Verifica se o tipo de bem é Movel (1) ou Imóvel (2) para setar o cod_estrutural e buscar o cod_plano que será creditado ou debitado.
        IF reRegistro.codigo_tipo_natureza = 1 THEN
            stCodEstruturalDepreciacao = '3.3.3.1.1.01.01.00.00.00';
        ELSEIF reRegistro.codigo_tipo_natureza = 2 THEN
            stCodEstruturalDepreciacao = '3.3.3.1.1.01.02.00.00.00';
        END IF;

        -- Recupera cod_plano apartir do cod_estrutural (3.3.3.1.1.01.01.00.00.00), para depreciação de bens móveis ou (3.3.3.1.1.01.02.00.00.00) para bens imóveis.
        -- Quando não for estorno (estorno = false), insere o cod_plano na contabilidade.conta_debito
        -- Quando for estorno (estorno = true), insere o cod_plano na contabilidade.conta_credito
           SELECT INTO
                  inCodPlanoEstrutural
                  cod_plano
            FROM contabilidade.plano_conta 
      INNER JOIN contabilidade.plano_analitica
              ON plano_analitica.exercicio  = plano_conta.exercicio 
             AND plano_analitica.cod_conta  = plano_conta.cod_conta 
           WHERE plano_conta.cod_estrutural = stCodEstruturalDepreciacao
             AND plano_analitica.exercicio  = PstExercicio;

        IF inCodPlanoEstrutural IS NULL THEN
           RAISE EXCEPTION 'Conta ( % ) não é analítica ou não está cadastrada no plano de contas.', stCodEstruturalDepreciacao;
        END IF;
    
        -- Recupera a ultima sequencia de contabilidade.lancamento, a ser inseridas nas outras tabelas. Será uma para cada lancamento.
       stFiltro    :=           ' WHERE exercicio    = ' || quote_literal(PstExercicio);
       stFiltro    := stFiltro || ' AND tipo         = ' || quote_literal(chTipo);
       stFiltro    := stFiltro || ' AND cod_entidade = ' || PinCodEntidade;
       stFiltro    := stFiltro || ' AND cod_lote     = ' || inCodLote;
       inSequencia := publico.fn_proximo_cod('sequencia','contabilidade.lancamento',stFiltro);

       INSERT INTO contabilidade.lancamento
           (sequencia, exercicio, tipo, cod_lote, cod_entidade, cod_historico, complemento)
       VALUES
           (inSequencia, PstExercicio, chTipo, inCodLote, PinCodEntidade, PinCodHistorico, PstComplemento);
    
        IF PboEstorno = FALSE THEN
            inCodPlanoDeb  := inCodPlanoEstrutural;
            inCodPlanoCred := reRegistro.cod_plano_final;
        ELSE
            inCodPlanoDeb  := reRegistro.cod_plano_final;
            inCodPlanoCred := inCodPlanoEstrutural;
        END IF;
        
        -- São inseridos 2 registros um a débito (valor positivo) e outro a crédito (valor negativo)
        IF inCodPlanoDeb IS NOT NULL AND inCodPlanoCred IS NOT NULL THEN
            --Insere dados de Crédito
            INSERT INTO contabilidade.valor_lancamento
                (sequencia, exercicio, tipo, cod_lote, cod_entidade, tipo_valor, vl_lancamento)
            VALUES
                (inSequencia, PstExercicio, chTipo, inCodLote, PinCodEntidade, 'C', (reRegistro.vl_depreciado * -1) );
            
            INSERT INTO contabilidade.conta_credito
                (sequencia, exercicio, tipo, cod_lote, cod_entidade, tipo_valor, cod_plano )
            VALUES
                (inSequencia, PstExercicio, chTipo, inCodLote, PinCodEntidade, 'C', inCodPlanoCred );
            
            --Insere dados de Débito
            INSERT INTO contabilidade.valor_lancamento
                (sequencia, exercicio, tipo, cod_lote, cod_entidade, tipo_valor, vl_lancamento)
            VALUES
                (inSequencia, PstExercicio, chTipo, inCodLote, PinCodEntidade,'D', (reRegistro.vl_depreciado) );
                
            INSERT INTO contabilidade.conta_debito
                (sequencia, exercicio, tipo, cod_lote, cod_entidade, tipo_valor, cod_plano )
            VALUES
                (inSequencia, PstExercicio, chTipo, inCodLote, PinCodEntidade, 'D', inCodPlanoDeb );
                
        ELSE
            RAISE EXCEPTION 'Deve ser informada pelo menos uma conta de débito ou crédito.';
        END IF;
        
        -- Adiciona os valores da consulta, juntamente com a sequencia, para relacionar os cod_planos com os bens.
        INSERT INTO tmp_relacao_bem (
            cod_depreciacao
          , vl_depreciado
          , cod_plano_final
          , nom_natureza
          , cod_natureza
          , cod_grupo
          , codigo_tipo_natureza
          , sequencia
        ) VALUES (
            reRegistro.cod_depreciacao
          , reRegistro.vl_depreciado
          , reRegistro.cod_plano_final
          , reRegistro.nom_natureza
          , reRegistro.cod_natureza
          , reRegistro.cod_grupo
          , reRegistro.codigo_tipo_natureza
          , inSequencia
        );
    
    END LOOP;

    -- Recupera os cod_bem relacionados com a sequencia, atraves do cod_plano
    stSql := '
    
          SELECT tbl_bem_depreciado.codigo_tipo_natureza
                , tbl_bem_depreciado.cod_bem
                , tbl_bem_depreciado.cod_natureza
                , tmp_relacao_bem.cod_grupo
                , tmp_relacao_bem.nom_natureza
                , tmp_relacao_bem.sequencia
                , tmp_relacao_bem.cod_plano_final
                , tmp_relacao_bem.cod_depreciacao
                , tbl_bem_depreciado.timestamp
                
             FROM (
             
               SELECT  depreciacao.cod_depreciacao
                        , CASE WHEN bem_plano_depreciacao.cod_plano IS NOT NULL
                               THEN bem_plano_depreciacao.cod_plano
                               ELSE grupo_plano_depreciacao.cod_plano
                          END AS cod_plano_final 
                        , tipo_natureza.cod_natureza
                        , tipo_natureza.cod_grupo
                        , tipo_natureza.codigo AS codigo_tipo_natureza
                        , tipo_natureza.nom_natureza
                        , depreciacao.cod_bem
                        , depreciacao.timestamp
              
                     FROM patrimonio.depreciacao
               
               INNER JOIN patrimonio.bem_comprado
                       ON bem_comprado.cod_bem = depreciacao.cod_bem
                
                LEFT JOIN (
                          SELECT bem_plano_depreciacao.cod_bem
                               , bem_plano_depreciacao.cod_plano 
                               , bem_plano_depreciacao.exercicio
                               , MAX(bem_plano_depreciacao.timestamp::timestamp) AS timestamp
                               , plano_conta.cod_estrutural
                               , plano_conta.nom_conta AS nom_conta_depreciacao
                               
                            FROM patrimonio.bem_plano_depreciacao 
                
                       LEFT JOIN contabilidade.plano_analitica
                              ON plano_analitica.cod_plano = bem_plano_depreciacao.cod_plano
                             AND plano_analitica.exercicio = bem_plano_depreciacao.exercicio
                
                       LEFT JOIN contabilidade.plano_conta
                              ON plano_conta.cod_conta = plano_analitica.cod_conta
                             AND plano_conta.exercicio = plano_analitica.exercicio
                               
                           WHERE bem_plano_depreciacao.timestamp::timestamp = ( SELECT MAX(bem_plano.timestamp::timestamp) AS timestamp 
                                                                                  FROM patrimonio.bem_plano_depreciacao AS bem_plano
                                                                                 
                                                                                WHERE bem_plano_depreciacao.cod_bem   = bem_plano.cod_bem
                                                                                  AND bem_plano_depreciacao.exercicio = bem_plano.exercicio
                                                                                  AND bem_plano_depreciacao.exercicio   = '|| quote_literal(PstExercicio) ||'
                                                                             
                                                                             GROUP BY bem_plano.cod_bem
                                                                                    , bem_plano.exercicio )
                            AND bem_plano_depreciacao.exercicio = '|| quote_literal(PstExercicio) ||'
                            
                        GROUP BY bem_plano_depreciacao.cod_bem
                               , bem_plano_depreciacao.cod_plano
                               , bem_plano_depreciacao.exercicio
                               , plano_conta.cod_estrutural
                               , plano_conta.nom_conta 
                        
                        ORDER BY timestamp DESC
                        
                      )AS bem_plano_depreciacao
                       ON bem_plano_depreciacao.cod_bem = depreciacao.cod_bem
                              
                LEFT JOIN ( SELECT grupo_plano_depreciacao.cod_plano
                                 , bem.cod_bem
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
                         ON grupo_plano_depreciacao.cod_bem = depreciacao.cod_bem
                
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
                         ON tipo_natureza.cod_bem = depreciacao.cod_bem
                
                  WHERE competencia = '|| quote_literal( PstExercicio || PstMesCompetencia) ||'
                    AND bem_comprado.cod_entidade = '|| PinCodEntidade ||'
                    AND NOT EXISTS ( SELECT 1 
                                       FROM patrimonio.depreciacao_anulada
                                      WHERE depreciacao_anulada.cod_depreciacao = depreciacao.cod_depreciacao
                                        AND depreciacao_anulada.cod_bem         = depreciacao.cod_bem
                                        AND depreciacao_anulada.timestamp       = depreciacao.timestamp
                                    )
           
                GROUP BY depreciacao.cod_depreciacao
                       , cod_plano_final
                       , tipo_natureza.cod_natureza
                       , tipo_natureza.codigo
                       , tipo_natureza.nom_natureza
                       , tipo_natureza.cod_grupo 
                       , depreciacao.cod_bem
                       , depreciacao.timestamp
                       
                ORDER BY tipo_natureza.cod_natureza
                       , tipo_natureza.cod_grupo
                       , codigo_tipo_natureza

           ) AS tbl_bem_depreciado

  INNER JOIN tmp_relacao_bem
          ON tmp_relacao_bem.cod_depreciacao      = tbl_bem_depreciado.cod_depreciacao
         AND tmp_relacao_bem.cod_natureza         = tbl_bem_depreciado.cod_natureza 
         AND tmp_relacao_bem.cod_grupo            = tbl_bem_depreciado.cod_grupo
         AND tmp_relacao_bem.codigo_tipo_natureza = tbl_bem_depreciado.codigo_tipo_natureza
         AND tmp_relacao_bem.cod_plano_final      = tbl_bem_depreciado.cod_plano_final
           
    ORDER BY tmp_relacao_bem.sequencia
           , tbl_bem_depreciado.cod_bem ';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        -- Recupera o último id de lançamento para inserir na tabela de lancamento depreciação
        inCodLancDepreciacao := publico.fn_proximo_cod('id','contabilidade.lancamento_depreciacao','');
        
        -- Insere na tabela para criar o relacionamento com os lancementos 
        INSERT INTO contabilidade.lancamento_depreciacao
            (id, timestamp, exercicio, cod_entidade, tipo, cod_lote, sequencia, cod_depreciacao, cod_bem, timestamp_depreciacao, estorno )
        VALUES
            (inCodLancDepreciacao, ('now'::text)::timestamp(3), PstExercicio, PinCodEntidade, chTipo, inCodLote, reRegistro.sequencia, reRegistro.cod_depreciacao, reRegistro.cod_bem, reRegistro.timestamp, PboEstorno);
        
    END LOOP;
    
    DROP TABLE tmp_relacao_bem;
    
END;
$$ LANGUAGE 'plpgsql';