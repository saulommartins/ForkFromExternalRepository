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
    * PL Lançamento Contábil de baixa de bens
    * Data de Criação: 01/06/2015

    * @author Analista:       Gelson Wolowski Gonçalves
    * @author Desenvolvedor: Arthur Cruz
    
    * @package URBEM
    * @subpackage 

    $Id: $
*/
CREATE OR REPLACE FUNCTION contabilidade.fn_insere_lancamentos_baixa_patrimonio(VARCHAR, VARCHAR, INTEGER, DATE, INTEGER, VARCHAR, BOOLEAN) RETURNS VOID AS $$
DECLARE
    PstExercicio                ALIAS FOR $1;
    PstCodBem                   ALIAS FOR $2;
    PinTipoBaixa                ALIAS FOR $3;
    PdtDataBaixa                ALIAS FOR $4;
    PinCodHistorico             ALIAS FOR $5;
    PstTipo                     ALIAS FOR $6;
    PboEstorno                  ALIAS FOR $7;
    
    inIndice                    INTEGER := 0;
    inCodLote                   INTEGER := 0;
    inCodPlanoDeb               INTEGER := 0;
    inCodPlanoCred              INTEGER := 0;
    inSequencia                 INTEGER := 0;
    inCodLancBaixa              INTEGER := 0;
    inCodEntidade               INTEGER := 0;
    stCodEstruturalBaixa        VARCHAR := '';
    stNomeLote                  VARCHAR := '';
    stComplemento               VARCHAR := '';
    stSql                       VARCHAR := '';
    stFiltro                    VARCHAR := '';
    reCodPlano                  RECORD;
    reBaixaBem                  RECORD;
BEGIN

    stSql := '
            SELECT grupo_plano_analitica.cod_plano
                 , CASE WHEN '|| PinTipoBaixa || ' = 1 OR '|| PinTipoBaixa || ' = 2 THEN grupo_plano_analitica.cod_plano_doacao
                        WHEN '|| PinTipoBaixa || ' = 3 OR '|| PinTipoBaixa || ' = 4 THEN grupo_plano_analitica.cod_plano_perda_involuntaria
                        WHEN '|| PinTipoBaixa || ' = 5 OR '|| PinTipoBaixa || ' = 6 THEN grupo_plano_analitica.cod_plano_transferencia
                   END AS cod_plano_tipo_baixa
                 , natureza.cod_tipo
                 , natureza.cod_natureza
                 , natureza.nom_natureza
                 , grupo.cod_grupo
                 , grupo.nom_grupo
                 , bem_comprado.cod_entidade
                 , SUM((
                    SELECT vl_atualizado
                      FROM patrimonio.fn_depreciacao_acumulada( bem.cod_bem )
                        AS retorno (  cod_bem            INTEGER
                                    , vl_acumulado       NUMERIC
                                    , vl_atualizado      NUMERIC
                                    , vl_bem             NUMERIC
                                    , min_competencia    VARCHAR
                                    , max_competencia    VARCHAR
                                   )
                     WHERE retorno.cod_bem = bem.cod_bem
                    ) ) AS vl_lancamento_contabil
    
              FROM patrimonio.bem
              
        INNER JOIN patrimonio.bem_comprado
                ON bem_comprado.cod_bem = bem.cod_bem
    
        INNER JOIN patrimonio.especie
                ON especie.cod_natureza = bem.cod_natureza
               AND especie.cod_grupo    = bem.cod_grupo
               AND especie.cod_especie  = bem.cod_especie
    
        INNER JOIN patrimonio.grupo
                ON grupo.cod_natureza = especie.cod_natureza
               AND grupo.cod_grupo    = especie.cod_grupo
    
        INNER JOIN patrimonio.natureza
                ON natureza.cod_natureza = grupo.cod_natureza
    
         INNER JOIN patrimonio.grupo_plano_analitica
                ON grupo_plano_analitica.cod_grupo    = grupo.cod_grupo
               AND grupo_plano_analitica.cod_natureza = grupo.cod_natureza
               AND grupo_plano_analitica.exercicio    = '|| quote_literal(PstExercicio) ||'

             WHERE bem.cod_bem IN ( '|| PstCodBem ||' )

          GROUP BY grupo_plano_analitica.cod_plano
                 , grupo_plano_analitica.cod_plano_doacao
                 , grupo_plano_analitica.cod_plano_perda_involuntaria
                 , grupo_plano_analitica.cod_plano_transferencia
                 , natureza.cod_tipo
                 , natureza.cod_natureza
                 , natureza.nom_natureza
                 , grupo.cod_grupo
                 , grupo.nom_grupo
                 , bem_comprado.cod_entidade ';
                 
    CREATE TABLE tmp_relacao_bem_baixa (
         sequencia               INTEGER        NOT NULL
       , cod_lote                INTEGER        NOT NULL
       , cod_plano               INTEGER        NOT NULL
       , cod_plano_tipo_baixa    INTEGER        NOT NULL
       , nom_natureza            VARCHAR (60)   NOT NULL
       , cod_natureza            INTEGER        NOT NULL
       , cod_tipo                INTEGER        NOT NULL
       , cod_grupo               INTEGER        NOT NULL
       , cod_entidade            INTEGER        NOT NULL
       , PRIMARY KEY ( sequencia, cod_plano, cod_plano_tipo_baixa, cod_tipo, cod_natureza, cod_grupo, cod_entidade )
    );

    -- Para cada bem irá fazer os lançamentos contabéis
    FOR reBaixaBem IN EXECUTE stSql
    LOOP
    
        -- Conforme parametro passado verifica se é ou não Estorno, caso seja, inverte as contas de lançamento.
        IF PboEstorno = FALSE THEN
            inCodPlanoDeb  := reBaixaBem.cod_plano_tipo_baixa;
            inCodPlanoCred := reBaixaBem.cod_plano;
            stNomeLote     := 'Lançamento de Baixa Patrimonial de Bem na data: ' || TO_CHAR(PdtDataBaixa, 'DD/MM/YYYY');
            stComplemento  := 'Baixa de Bem na data: ' || TO_CHAR(PdtDataBaixa, 'DD/MM/YYYY');
        ELSE
            inCodPlanoDeb  := reBaixaBem.cod_plano;
            inCodPlanoCred := reBaixaBem.cod_plano_tipo_baixa;
            stNomeLote     := 'Lançamento de Estorno de Baixa Patrimonial de Bem: ' || TO_CHAR(PdtDataBaixa, 'DD/MM/YYYY');
            stComplemento  := 'Estorno de Baixa de Bem na data: ' || TO_CHAR(PdtDataBaixa, 'DD/MM/YYYY');
        END IF;

        IF inCodEntidade <> reBaixaBem.cod_entidade THEN
            inCodEntidade := reBaixaBem.cod_entidade;

            -- Recupera o último cod_lote a ser inserido na tabela contabilidade.lancamento
            stFiltro  :=           ' WHERE exercicio    = ' || quote_literal(PstExercicio); 
            stFiltro  := stFiltro || ' AND tipo         = ' || quote_literal(PstTipo);
            stFiltro  := stFiltro || ' AND cod_entidade = ' || reBaixaBem.cod_entidade;
            inCodLote := publico.fn_proximo_cod('cod_lote','contabilidade.lote', stFiltro);
            
            INSERT INTO contabilidade.lote
                (cod_lote, exercicio, tipo, cod_entidade, nom_lote, dt_lote)
            VALUES
                (inCodLote, PstExercicio, PstTipo, reBaixaBem.cod_entidade, stNomeLote, PdtDataBaixa);
        
        END IF;
        
         -- Recupera a ultima sequencia de contabilidade.lancamento. Será uma para cada lancamento conforme o último lote inserido.
        stFiltro    :=       'WHERE exercicio         = ' || quote_literal(PstExercicio);
        stFiltro    := stFiltro || ' AND tipo         = ' || quote_literal(PstTipo);
        stFiltro    := stFiltro || ' AND cod_entidade = ' || reBaixaBem.cod_entidade;
        stFiltro    := stFiltro || ' AND cod_lote     = ' || inCodLote;
        inSequencia := publico.fn_proximo_cod('sequencia','contabilidade.lancamento', stFiltro);
        
        INSERT INTO contabilidade.lancamento
            (sequencia, cod_lote, tipo, exercicio, cod_entidade, cod_historico, complemento)
        VALUES
            (inSequencia, inCodLote, PstTipo, PstExercicio, reBaixaBem.cod_entidade, PinCodHistorico, stComplemento);

        -- São inseridos 2 registros um a débito (valor positivo) e outro a crédito (valor negativo)
        IF inCodPlanoDeb IS NOT NULL AND inCodPlanoCred IS NOT NULL THEN
            --Insere dados de Crédito
            INSERT INTO contabilidade.valor_lancamento
                (sequencia, exercicio, tipo, cod_lote, cod_entidade, tipo_valor, vl_lancamento)
            VALUES
                (inSequencia, PstExercicio, PstTipo, inCodLote, reBaixaBem.cod_entidade, 'C', (reBaixaBem.vl_lancamento_contabil * -1) );
            
            INSERT INTO contabilidade.conta_credito
                (sequencia, exercicio, tipo, cod_lote, cod_entidade, tipo_valor, cod_plano )
            VALUES
                (inSequencia, PstExercicio, PstTipo, inCodLote, reBaixaBem.cod_entidade, 'C', inCodPlanoCred );
            
            --Insere dados de Débito
            INSERT INTO contabilidade.valor_lancamento
                (sequencia, exercicio, tipo, cod_lote, cod_entidade, tipo_valor, vl_lancamento)
            VALUES
                (inSequencia, PstExercicio, PstTipo, inCodLote, reBaixaBem.cod_entidade,'D', (reBaixaBem.vl_lancamento_contabil) );
                
            INSERT INTO contabilidade.conta_debito
                (sequencia, exercicio, tipo, cod_lote, cod_entidade, tipo_valor, cod_plano )
            VALUES
                (inSequencia, PstExercicio, PstTipo, inCodLote, reBaixaBem.cod_entidade, 'D', inCodPlanoDeb );
        ELSE
            RAISE EXCEPTION 'Deve ser informada pelo menos uma conta de débito ou crédito, para o grupo : % - %', reBaixaBem.cod_grupo, reBaixaBem.nom_grupo;
        END IF;
        
        -- Adiciona os valores da consulta, juntamente com a sequencia, para relacionar os cod_planos com os bens.
        INSERT INTO tmp_relacao_bem_baixa (
            sequencia
          , cod_lote
          , cod_plano           
          , cod_plano_tipo_baixa
          , nom_natureza        
          , cod_natureza        
          , cod_tipo            
          , cod_grupo           
          , cod_entidade        
        ) VALUES (
            inSequencia
          , inCodLote
          , reBaixaBem.cod_plano
          , reBaixaBem.cod_plano_tipo_baixa
          , reBaixaBem.nom_natureza
          , reBaixaBem.cod_natureza
          , reBaixaBem.cod_tipo
          , reBaixaBem.cod_grupo
          , reBaixaBem.cod_entidade
        );
        
    END LOOP;
    
    -- Recupera bem a bem, e outros dados, necessário devido a sequencia, caso seja de mesmo grupo, porem de natureza diferentes, assim como os cod_planos que tbm podem ser.
    stSql := '
        SELECT tbl.cod_bem
             , tmp_relacao_bem_baixa.sequencia
             , tmp_relacao_bem_baixa.cod_lote
             , tbl.cod_entidade
          FROM (
            SELECT bem.cod_bem
             , grupo_plano_analitica.cod_plano 
             , CASE WHEN '|| PinTipoBaixa || ' = 1 OR '|| PinTipoBaixa || ' = 2 THEN grupo_plano_analitica.cod_plano_doacao
                    WHEN '|| PinTipoBaixa || ' = 3 OR '|| PinTipoBaixa || ' = 4 THEN grupo_plano_analitica.cod_plano_perda_involuntaria
                    WHEN '|| PinTipoBaixa || ' = 5 OR '|| PinTipoBaixa || ' = 6 THEN grupo_plano_analitica.cod_plano_transferencia
               END AS cod_plano_tipo_baixa
             , natureza.cod_tipo
             , natureza.cod_natureza
             , natureza.nom_natureza
             , grupo.cod_grupo
             , grupo.nom_grupo
             , bem_comprado.cod_entidade
             , (
                SELECT vl_atualizado
                  FROM patrimonio.fn_depreciacao_acumulada( bem.cod_bem )
                AS retorno (  cod_bem            INTEGER
                        , vl_acumulado       NUMERIC
                        , vl_atualizado      NUMERIC
                        , vl_bem             NUMERIC
                        , min_competencia    VARCHAR
                        , max_competencia    VARCHAR
                       )
                 WHERE retorno.cod_bem = bem.cod_bem
                ) AS vl_lancamento_contabil
    
              FROM patrimonio.bem
              
        INNER JOIN patrimonio.bem_comprado
            ON bem_comprado.cod_bem = bem.cod_bem
    
        INNER JOIN patrimonio.especie
            ON especie.cod_natureza = bem.cod_natureza
               AND especie.cod_grupo    = bem.cod_grupo
               AND especie.cod_especie  = bem.cod_especie
    
        INNER JOIN patrimonio.grupo
            ON grupo.cod_natureza = especie.cod_natureza
               AND grupo.cod_grupo    = especie.cod_grupo
    
        INNER JOIN patrimonio.natureza
            ON natureza.cod_natureza = grupo.cod_natureza
    
         INNER JOIN patrimonio.grupo_plano_analitica
            ON grupo_plano_analitica.cod_grupo    = grupo.cod_grupo
               AND grupo_plano_analitica.cod_natureza = grupo.cod_natureza
               AND grupo_plano_analitica.exercicio    = '|| quote_literal(PstExercicio) ||'

             WHERE bem.cod_bem IN ( '|| PstCodBem ||' )
            ) AS tbl
    
    INNER JOIN tmp_relacao_bem_baixa
            ON tmp_relacao_bem_baixa.cod_plano             = tbl.cod_plano
           AND tmp_relacao_bem_baixa.cod_plano_tipo_baixa  = tbl.cod_plano_tipo_baixa
           AND tmp_relacao_bem_baixa.nom_natureza          = tbl.nom_natureza
           AND tmp_relacao_bem_baixa.cod_tipo              = tbl.cod_tipo
           AND tmp_relacao_bem_baixa.cod_grupo             = tbl.cod_grupo
           AND tmp_relacao_bem_baixa.cod_entidade          = tbl.cod_entidade ';


    FOR reBaixaBem IN EXECUTE stSql
    LOOP
        -- Recupera o último id de lançamento de baixa de patrimonio
        inCodLancBaixa := publico.fn_proximo_cod('id','contabilidade.lancamento_baixa_patrimonio','');
            
        -- Relaciona a baixa com o bem.
        INSERT INTO contabilidade.lancamento_baixa_patrimonio
            (id, timestamp, exercicio, cod_entidade, tipo, cod_lote, sequencia, cod_bem, estorno )
        VALUES
            (inCodLancBaixa, ('now'::text)::timestamp(3), PstExercicio, reBaixaBem.cod_entidade, PstTipo, reBaixaBem.cod_lote, reBaixaBem.sequencia, reBaixaBem.cod_bem, PboEstorno);
    END LOOP;
    
    DROP TABLE tmp_relacao_bem_baixa;
    
END;
$$ LANGUAGE 'plpgsql';