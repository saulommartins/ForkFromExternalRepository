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
    * PL Lançamento Contábil Contábil de Reavaliação de Bens
    * Data de Criação: 19/05/2016

    * @author Analista:      Gelson Wolowski Gonçalves
    * @author Desenvolvedor: Arthur Cruz

    * @package URBEM
    * @subpackage 

    $Id: insereLancamentoReavaliacao.plsql 66503 2016-09-05 15:02:31Z michel $
*/
CREATE OR REPLACE FUNCTION contabilidade.fn_insere_lancamentos_reavaliacao(VARCHAR,VARCHAR,DATE,DATE,INTEGER,INTEGER,VARCHAR,VARCHAR,BOOLEAN,VARCHAR) RETURNS VOID AS $$
DECLARE
    PstExercicio                ALIAS FOR $1;
    PstMesCompetencia           ALIAS FOR $2;
    PstDataInicial              ALIAS FOR $3;
    PstDataFinal                ALIAS FOR $4;
    PinCodEntidade              ALIAS FOR $5;
    PinCodHistorico             ALIAS FOR $6;
    PstTipo                     ALIAS FOR $7;
    PstComplemento              ALIAS FOR $8;
    PboEstorno                  ALIAS FOR $9;
    PstCodBem                   ALIAS FOR $10;

    inCodLote                   INTEGER := 0;
    inCodPlanoDeb               INTEGER := 0;
    inCodPlanoCred              INTEGER := 0;
    inCodPlanoEstrutural        INTEGER := 0;
    inSequencia                 INTEGER := 0;
    inCodLancReavaliacao        INTEGER := 0;
    inCodDepreciacao            INTEGER := 0;
    inDataReavaliacao           INTEGER := 0;
    inMesContabil               INTEGER := 0;
    stDataLote                  DATE;
    chTipo                      CHAR    := '';
    stCodEstruturalDepreciacao  VARCHAR := '';
    stNomeLote                  VARCHAR := '';
    stSql                       VARCHAR := '';
    stFiltro                    VARCHAR := '';
    stFiltroBem                 VARCHAR := '';

    vlBem                       NUMERIC := 0.00;
    vlLancamento                NUMERIC := 0.00;
    stTimestamp                 TIMESTAMP := ('now'::text)::timestamp(3);

    reRegistro                  RECORD;
BEGIN

    -- Caso tenha informado uma string com mais de 1 caracter trunca
    chTipo := substr(trim(PstTipo),1,1);

    -- Se estiver no mês da competência, deve ser o dia atual, senão será o último dia do mês caso estiver em mês posterior
    IF TO_CHAR(CURRENT_DATE, 'MM') = PstMesCompetencia THEN
        stDataLote := CURRENT_DATE;
    ELSEIF TO_CHAR(CURRENT_DATE, 'MM') > PstMesCompetencia THEN
        stDataLote := PstExercicio || '-' || PstMesCompetencia || '-' || calculaUltimoDiaMes(PstExercicio::INTEGER , PstMesCompetencia::INTEGER);
    END IF;

    IF PboEstorno = false THEN
        stNomeLote := 'Lançamento de Reavaliação no Mês: ' || PstMesCompetencia || '/' ||PstExercicio;
    ELSE
        stNomeLote := 'Lançamento de Estorno de Reavaliação no Mês: ' || PstMesCompetencia || '/' || PstExercicio;
    END IF;

    IF PstCodBem != '' THEN
        stFiltroBem := 'AND bem.cod_bem = '|| PstCodBem ||' ';
    END IF;

    -- Recupera as reavaliações, e seus valores agrupados por cod_plano, agrupados por grupo ou bem.
    stSql := '
           SELECT COALESCE( SUM( bem_atualizado.vl_bem) , 0.00) - COALESCE( SUM( reavaliacao.vl_reavaliacao ), 0.00) AS vl_diferenca_reavaliacao
                , CASE WHEN bem_plano_reavaliacao.cod_plano IS NOT NULL
                       THEN bem_plano_reavaliacao.cod_plano
                       ELSE grupo_plano_reavaliacao.cod_plano
                  END AS cod_plano_final
                , tipo_natureza.cod_natureza
                , tipo_natureza.cod_grupo
                , tipo_natureza.nom_grupo
                , tipo_natureza.codigo AS codigo_tipo_natureza
                , tipo_natureza.nom_natureza
                , reavaliacao.dt_reavaliacao
                , reavaliacao.cod_reavaliacao
                , bem.cod_bem
                , COALESCE( SUM(bem.vl_bem), 0.00) AS vl_bem_original
                , COALESCE( SUM(bem_atualizado.vl_bem), 0.00) AS vl_bem_atualizado
                , COALESCE( SUM(reavaliacao.vl_reavaliacao), 0.00) AS vl_bem_reavaliado
                , COALESCE( lancamento_reavaliacao.id, 0 ) AS id_estorno
                , COALESCE( vl_lancamento_reavaliacao.vl_lancamento, 0.00 ) AS vl_estorno
                , COALESCE( vl_lancamento_reavaliacao.cod_plano, 0 ) AS cod_plano_estorno
                , bem_atualizado.bo_estorno

             FROM patrimonio.reavaliacao

       INNER JOIN patrimonio.bem
               ON bem.cod_bem = reavaliacao.cod_bem

       INNER JOIN patrimonio.bem_comprado
               ON bem_comprado.cod_bem = bem.cod_bem

       INNER JOIN ( SELECT bem.cod_bem
                         , CASE WHEN reavaliacao.cod_bem IS NOT NULL
                                THEN reavaliacao.vl_reavaliacao
                                ELSE bem.vl_bem
                           END AS vl_bem
                         , '||PboEstorno||' AS bo_estorno
                      FROM patrimonio.bem
                 LEFT JOIN ( SELECT lancamento_reavaliacao.cod_bem
                                  , MAX(lancamento_reavaliacao.timestamp) AS timestamp
                               FROM contabilidade.lancamento_reavaliacao
                          LEFT JOIN contabilidade.lancamento_reavaliacao_estorno
                                 ON lancamento_reavaliacao_estorno.id = lancamento_reavaliacao.id
                         INNER JOIN patrimonio.reavaliacao
                                 ON lancamento_reavaliacao.cod_reavaliacao = reavaliacao.cod_reavaliacao
                                AND lancamento_reavaliacao.cod_bem         = reavaliacao.cod_bem
                              WHERE lancamento_reavaliacao.estorno IS FALSE
                                AND lancamento_reavaliacao_estorno.id IS NULL
                                --REAVALIAÇÃO ATÉ DATA YYYY-MM-DD
                                --AND reavaliacao.dt_reavaliacao < '|| quote_literal( PstDataInicial ) ||'
                          GROUP BY lancamento_reavaliacao.cod_bem
                           ) AS max_lancamento_reavaliacao
                        ON max_lancamento_reavaliacao.cod_bem = bem.cod_bem
                 LEFT JOIN contabilidade.lancamento_reavaliacao
                        ON lancamento_reavaliacao.cod_reavaliacao = ( SELECT MAX(LR.cod_reavaliacao)
                                                                        FROM contabilidade.lancamento_reavaliacao AS LR
                                                                       WHERE LR.timestamp = max_lancamento_reavaliacao.timestamp
                                                                         AND LR.cod_bem   = max_lancamento_reavaliacao.cod_bem
                                                                    )
                       AND lancamento_reavaliacao.cod_bem         = max_lancamento_reavaliacao.cod_bem
                       AND lancamento_reavaliacao.timestamp       = max_lancamento_reavaliacao.timestamp
                       AND lancamento_reavaliacao.estorno IS FALSE
                 LEFT JOIN patrimonio.reavaliacao
                        ON lancamento_reavaliacao.cod_reavaliacao = reavaliacao.cod_reavaliacao
                       AND lancamento_reavaliacao.cod_bem         = reavaliacao.cod_bem
                  ) AS bem_atualizado
               ON bem_atualizado.cod_bem = reavaliacao.cod_bem

        LEFT JOIN (
                  SELECT bem_plano_analitica.cod_bem
                       , bem_plano_analitica.cod_plano 
                       , bem_plano_analitica.exercicio
                       , MAX(bem_plano_analitica.timestamp::timestamp) AS timestamp

                    FROM patrimonio.bem_plano_analitica

                   WHERE bem_plano_analitica.timestamp::timestamp = ( SELECT MAX(bem_plano.timestamp::timestamp) AS timestamp 
                                                                        FROM patrimonio.bem_plano_analitica AS bem_plano
                                                                       WHERE bem_plano_analitica.cod_bem   = bem_plano.cod_bem
                                                                         AND bem_plano_analitica.exercicio = bem_plano.exercicio
                                                                         AND bem_plano_analitica.exercicio   = '|| quote_literal(PstExercicio) ||'
                                                                    GROUP BY bem_plano.cod_bem
                                                                           , bem_plano.exercicio )
                     AND bem_plano_analitica.exercicio = '|| quote_literal(PstExercicio) ||'

                GROUP BY bem_plano_analitica.cod_bem
                       , bem_plano_analitica.cod_plano
                       , bem_plano_analitica.exercicio

                ORDER BY timestamp DESC

                  )AS bem_plano_reavaliacao
               ON bem_plano_reavaliacao.cod_bem = reavaliacao.cod_bem

        LEFT JOIN ( SELECT bem.cod_bem
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

                  ) AS grupo_plano_reavaliacao
               ON grupo_plano_reavaliacao.cod_bem = reavaliacao.cod_bem

         INNER JOIN (
                    SELECT bem.cod_bem
                         , bem.cod_natureza
                         , tipo_natureza.codigo
                         , natureza.nom_natureza
                         , grupo.cod_grupo
                         , grupo.nom_grupo

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
               ON tipo_natureza.cod_bem = reavaliacao.cod_bem

        LEFT JOIN ( SELECT lancamento_reavaliacao.cod_reavaliacao
                         , lancamento_reavaliacao.cod_bem
                         , MAX(lancamento_reavaliacao.id) AS id
                      FROM contabilidade.lancamento_reavaliacao
                 LEFT JOIN contabilidade.lancamento_reavaliacao_estorno
                        ON lancamento_reavaliacao_estorno.id = lancamento_reavaliacao.id
                     WHERE lancamento_reavaliacao.estorno IS FALSE
                       AND lancamento_reavaliacao_estorno.id IS NULL
                  GROUP BY lancamento_reavaliacao.cod_reavaliacao
                         , lancamento_reavaliacao.cod_bem
                  ) AS lancamento_reavaliacao
               ON lancamento_reavaliacao.cod_bem = reavaliacao.cod_bem
              AND lancamento_reavaliacao.cod_reavaliacao = reavaliacao.cod_reavaliacao

        LEFT JOIN ( SELECT lancamento_reavaliacao.id
                         , SUM(valor_lancamento.vl_lancamento) AS vl_lancamento
                         , conta_debito.cod_plano
                      FROM contabilidade.lancamento_reavaliacao
                INNER JOIN contabilidade.valor_lancamento
                        ON valor_lancamento.exercicio = lancamento_reavaliacao.exercicio
                       AND valor_lancamento.cod_entidade = lancamento_reavaliacao.cod_entidade
                       AND valor_lancamento.tipo = lancamento_reavaliacao.tipo
                       AND valor_lancamento.cod_lote = lancamento_reavaliacao.cod_lote
                       AND valor_lancamento.sequencia = lancamento_reavaliacao.sequencia
                       AND valor_lancamento.tipo_valor = ''D''
                INNER JOIN contabilidade.conta_debito
                        ON valor_lancamento.exercicio    = conta_debito.exercicio
                       AND valor_lancamento.cod_entidade = conta_debito.cod_entidade
                       AND valor_lancamento.tipo         = conta_debito.tipo
                       AND valor_lancamento.cod_lote     = conta_debito.cod_lote
                       AND valor_lancamento.sequencia    = conta_debito.sequencia
                       AND valor_lancamento.tipo_valor   = conta_debito.tipo_valor
                     WHERE lancamento_reavaliacao.estorno IS FALSE
                  GROUP BY lancamento_reavaliacao.id
                         , conta_debito.cod_plano
                  ) AS vl_lancamento_reavaliacao
               ON vl_lancamento_reavaliacao.id = lancamento_reavaliacao.id

            WHERE reavaliacao.dt_reavaliacao BETWEEN '|| quote_literal( PstDataInicial ) ||' AND '|| quote_literal( PstDataFinal ) ||'
              AND bem_comprado.cod_entidade = '|| PinCodEntidade ||'
              AND NOT EXISTS ( SELECT 1
                                 FROM patrimonio.bem_baixado
                                WHERE bem_baixado.cod_bem = reavaliacao.cod_bem
                             )
              '||stFiltroBem||'

        GROUP BY cod_plano_final
               , tipo_natureza.cod_natureza
               , tipo_natureza.codigo
               , tipo_natureza.nom_natureza
               , tipo_natureza.cod_grupo
               , tipo_natureza.nom_grupo
               , reavaliacao.dt_reavaliacao
               , reavaliacao.cod_reavaliacao
               , bem.cod_bem
               , lancamento_reavaliacao.id
               , vl_lancamento_reavaliacao.vl_lancamento
               , bem_atualizado.bo_estorno
               , vl_lancamento_reavaliacao.cod_plano

          --LANÇAMENTO
          HAVING (     bem_atualizado.bo_estorno IS FALSE
                   AND COALESCE( SUM( bem_atualizado.vl_bem) , 0.00) - COALESCE( SUM( reavaliacao.vl_reavaliacao ), 0.00) <> 0
                   AND lancamento_reavaliacao.id IS NULL
                   AND ( SELECT MAX(LR.id) AS id
                           FROM contabilidade.lancamento_reavaliacao AS LR
                          WHERE LR.cod_reavaliacao = reavaliacao.cod_reavaliacao
                            AND LR.cod_bem         = bem.cod_bem
                       ) IS NULL
                 )
          --ESTORNO
              OR (     bem_atualizado.bo_estorno IS TRUE
                   AND COALESCE(vl_lancamento_reavaliacao.vl_lancamento, 0.00) <> 0
                   AND lancamento_reavaliacao.id IS NOT NULL
                 )

        ORDER BY reavaliacao.dt_reavaliacao
               , tipo_natureza.cod_natureza
               , tipo_natureza.cod_grupo
               , codigo_tipo_natureza
               , reavaliacao.cod_reavaliacao ';

    CREATE TABLE tmp_bem_lote (
         dt_reavaliacao    DATE      NOT NULL
       , cod_lote          INTEGER   NOT NULL
       , PRIMARY KEY ( dt_reavaliacao, cod_lote )
    );

    FOR reRegistro IN EXECUTE stSql
    LOOP

        SELECT INTO
               inDataReavaliacao
               COUNT(dt_reavaliacao)
          FROM tmp_bem_lote
         WHERE dt_reavaliacao = reRegistro.dt_reavaliacao;

        IF inDataReavaliacao = 0 THEN
            SELECT INTO
                   inMesContabil
                   valor::INTEGER
              FROM administracao.configuracao
             WHERE cod_modulo = 9
               AND parametro = 'mes_processamento'
               AND exercicio = PstExercicio;

            IF inMesContabil > (TO_CHAR(reRegistro.dt_reavaliacao::DATE, 'MM')::INTEGER) THEN
                RAISE EXCEPTION 'Mês de Processamento da Contabilidade é superior ao mês do Reavaliação de Bem!';
            END IF;

            -- Recupera o último cod_lote a ser inserido na tabela contabilidade.lancamento
            stFiltro  :=            'WHERE exercicio    = ' || quote_literal(PstExercicio);
            stFiltro  := stFiltro || ' AND tipo         = ' || quote_literal(chTipo);
            stFiltro  := stFiltro || ' AND cod_entidade = ' || PinCodEntidade;
            inCodLote := publico.fn_proximo_cod('cod_lote','contabilidade.lote',stFiltro);

            INSERT INTO contabilidade.lote
                (cod_lote, exercicio, tipo, cod_entidade, nom_lote, dt_lote)
            VALUES
                (inCodLote, PstExercicio, chTipo, PinCodEntidade, stNomeLote, reRegistro.dt_reavaliacao);

            INSERT INTO tmp_bem_lote
                (dt_reavaliacao, cod_lote)
            VALUES
                (reRegistro.dt_reavaliacao, inCodLote);
        END IF;

    END LOOP;

    FOR reRegistro IN EXECUTE stSql
    LOOP

        SELECT INTO
               inCodLote
               cod_lote
          FROM tmp_bem_lote
         WHERE dt_reavaliacao = reRegistro.dt_reavaliacao;

        -- Verifica se está configurada um tipo de natureza para a natureza do Grupo
        IF reRegistro.codigo_tipo_natureza = 0 OR reRegistro.codigo_tipo_natureza != 1 AND reRegistro.codigo_tipo_natureza != 2
        THEN
            RAISE EXCEPTION 'Necessário configurar um Tipo de Natureza ( 1 - Bens móveis ou 2 - Bens imóveis ) para a Natureza: %', reRegistro.cod_natureza || ' - ' || reRegistro.nom_natureza;
        END IF;

        -- Recupera cod_plano apartir do cod_estrutural (4.6.1.1.1.01%), para depreciação de bens móveis ou (4.6.1.1.1.02%) para bens imóveis.
        -- Quando não for estorno (estorno = false), insere o cod_plano na contabilidade.conta_debito
        -- Quando for estorno (estorno = true), insere o cod_plano na contabilidade.conta_credito
           SELECT INTO
                  inCodPlanoEstrutural
                  plano_analitica.cod_plano
            FROM contabilidade.plano_conta 
      INNER JOIN contabilidade.plano_analitica
              ON plano_analitica.exercicio  = plano_conta.exercicio
             AND plano_analitica.cod_conta  = plano_conta.cod_conta
      INNER JOIN patrimonio.grupo_plano_reavaliacao
              ON grupo_plano_reavaliacao.exercicio  = plano_analitica.exercicio
             AND grupo_plano_reavaliacao.cod_plano  = plano_analitica.cod_plano
           WHERE grupo_plano_reavaliacao.exercicio    = PstExercicio
             AND grupo_plano_reavaliacao.cod_natureza = reRegistro.cod_natureza
             AND grupo_plano_reavaliacao.cod_grupo    = reRegistro.cod_grupo;

        IF inCodPlanoEstrutural IS NULL THEN
           RAISE EXCEPTION 'Necessário configurar uma Conta Contábil de Reavaliação para o Grupo: %', reRegistro.cod_grupo || ' - ' || reRegistro.nom_grupo;
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
            IF PboEstorno = FALSE THEN
                --vlLancamento := reRegistro.vl_diferenca_reavaliacao;
                --VALOR ATUAL DO BEM
                SELECT INTO
                       vlBem
                       CASE WHEN reavaliacao.cod_bem IS NOT NULL
                            THEN reavaliacao.vl_reavaliacao
                            ELSE bem.vl_bem
                       END AS vl_bem
                  FROM patrimonio.bem
             LEFT JOIN ( SELECT lancamento_reavaliacao.cod_bem
                              , MAX(lancamento_reavaliacao.timestamp) AS timestamp
                           FROM contabilidade.lancamento_reavaliacao
                      LEFT JOIN contabilidade.lancamento_reavaliacao_estorno
                             ON lancamento_reavaliacao_estorno.id = lancamento_reavaliacao.id
                     INNER JOIN patrimonio.reavaliacao
                             ON lancamento_reavaliacao.cod_reavaliacao = reavaliacao.cod_reavaliacao
                            AND lancamento_reavaliacao.cod_bem         = reavaliacao.cod_bem
                          WHERE lancamento_reavaliacao.estorno IS FALSE
                            AND lancamento_reavaliacao_estorno.id IS NULL
                      GROUP BY lancamento_reavaliacao.cod_bem
                       ) AS max_lancamento_reavaliacao
                    ON max_lancamento_reavaliacao.cod_bem = bem.cod_bem
             LEFT JOIN contabilidade.lancamento_reavaliacao
                    ON lancamento_reavaliacao.cod_reavaliacao = ( SELECT MAX(LR.cod_reavaliacao)
                                                                    FROM contabilidade.lancamento_reavaliacao AS LR
                                                                   WHERE LR.timestamp = max_lancamento_reavaliacao.timestamp
                                                                     AND LR.cod_bem   = max_lancamento_reavaliacao.cod_bem
                                                                )
                   AND lancamento_reavaliacao.cod_bem         = max_lancamento_reavaliacao.cod_bem
                   AND lancamento_reavaliacao.timestamp       = max_lancamento_reavaliacao.timestamp
                   AND lancamento_reavaliacao.estorno IS FALSE
             LEFT JOIN patrimonio.reavaliacao
                    ON lancamento_reavaliacao.cod_reavaliacao = reavaliacao.cod_reavaliacao
                   AND lancamento_reavaliacao.cod_bem         = reavaliacao.cod_bem
                 WHERE bem.cod_bem = reRegistro.cod_bem;

                vlLancamento := vlBem - reRegistro.vl_bem_reavaliado;
                
                IF vlLancamento > 0 THEN
                    inCodPlanoDeb  := reRegistro.cod_plano_final;
                    inCodPlanoCred := inCodPlanoEstrutural;

                    vlLancamento := vlLancamento * -1;
                END IF;
            ELSE
                vlLancamento := reRegistro.vl_estorno;

                IF reRegistro.cod_plano_final = reRegistro.cod_plano_estorno THEN
                    inCodPlanoDeb  := inCodPlanoEstrutural;
                    inCodPlanoCred := reRegistro.cod_plano_final;
                END IF;
            END IF;

            IF vlLancamento <> 0 THEN
                --Insere dados de Crédito
                INSERT INTO contabilidade.valor_lancamento
                    (sequencia, exercicio, tipo, cod_lote, cod_entidade, tipo_valor, vl_lancamento)
                VALUES
                    (inSequencia, PstExercicio, chTipo, inCodLote, PinCodEntidade, 'C', (vlLancamento * -1) );

                INSERT INTO contabilidade.conta_credito
                    (sequencia, exercicio, tipo, cod_lote, cod_entidade, tipo_valor, cod_plano )
                VALUES
                    (inSequencia, PstExercicio, chTipo, inCodLote, PinCodEntidade, 'C', inCodPlanoCred );

                --Insere dados de Débito
                INSERT INTO contabilidade.valor_lancamento
                    (sequencia, exercicio, tipo, cod_lote, cod_entidade, tipo_valor, vl_lancamento)
                VALUES
                    (inSequencia, PstExercicio, chTipo, inCodLote, PinCodEntidade,'D', (vlLancamento) );

                INSERT INTO contabilidade.conta_debito
                    (sequencia, exercicio, tipo, cod_lote, cod_entidade, tipo_valor, cod_plano )
                VALUES
                    (inSequencia, PstExercicio, chTipo, inCodLote, PinCodEntidade, 'D', inCodPlanoDeb );

                stTimestamp := ('now'::text)::timestamp(3);

                IF PboEstorno = TRUE THEN
                    --Insere Estorno do Lançamento
                    INSERT INTO contabilidade.lancamento_reavaliacao_estorno
                        (id, timestamp)
                    VALUES
                        (reRegistro.id_estorno, stTimestamp );
                END IF;

                -- Recupera o último id de lançamento para inserir na tabela de lançamento reavaliação
                inCodLancReavaliacao := publico.fn_proximo_cod('id','contabilidade.lancamento_reavaliacao','');

                -- Insere na tabela para criar o relacionamento com os lancamentos 
                INSERT INTO contabilidade.lancamento_reavaliacao
                    (id, competencia, exercicio, cod_entidade, tipo, cod_lote, sequencia, cod_reavaliacao, cod_bem, estorno, timestamp)
                VALUES
                    (inCodLancReavaliacao, PstMesCompetencia, PstExercicio, PinCodEntidade, chTipo, inCodLote, inSequencia, reRegistro.cod_reavaliacao, reRegistro.cod_bem, PboEstorno, stTimestamp);
            END IF;
        ELSE
            RAISE EXCEPTION 'Deve ser informada pelo menos uma conta de débito ou crédito para: %.% - %', reRegistro.cod_natureza, reRegistro.cod_grupo, reRegistro.nom_natureza;
        END IF;
    END LOOP;

    DROP TABLE tmp_bem_lote;

END;
$$ LANGUAGE 'plpgsql';