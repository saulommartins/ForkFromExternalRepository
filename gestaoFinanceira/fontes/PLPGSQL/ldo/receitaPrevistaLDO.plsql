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
    * Retorna a lista de receitas previstas 
    * Data de Criação: 03/07/2009 
 
 
    * @author Analista:      Tonismar Bernardo <tonismar.bernardo@cnm.org.br> 
    * @author Desenvolvedor: Henrique Boaventura <henrique.boaventura@cnm.org.br> 
 
    * @package      URBEM 
    * @subpackage   LDO 
 
    * $Id: $ 
*/ 
 
CREATE OR REPLACE FUNCTION ldo.fn_receita_prevista_configuracao(INTEGER,CHAR(4)) RETURNS SETOF RECORD AS $$ 
DECLARE
    inCodPPA            ALIAS FOR $1;
    stExercicio         ALIAS FOR $2;
   
    stExercicioInicial  VARCHAR;
    stSql               VARCHAR;
    stSqlAux            VARCHAR;
    stAnoAtual          VARCHAR;
    stAno               VARCHAR;

    reRecord            RECORD;

    flValor_1           NUMERIC(14,2);
    flValor_2           NUMERIC(14,2);
    flValor_3           NUMERIC(14,2);
    flValor_4           NUMERIC(14,2);
    
    boOrcamento_1       NUMERIC(1);
    boOrcamento_2       NUMERIC(1);
    boOrcamento_3       NUMERIC(1);
    boOrcamento_4       NUMERIC(1);

BEGIN
    stExercicioInicial := TRIM(TO_CHAR((TO_NUMBER(stExercicio,'9999') - 4),'9999'));
    
    -----------------------------------------
    -- cria a tabela temporaria de retorno --
    -----------------------------------------
    CREATE TEMPORARY TABLE tmp_retorno (
        cod_tipo         INTEGER,
        exercicio        VARCHAR(4),
        cod_estrutural   VARCHAR,
        descricao        VARCHAR,
        tipo             CHAR(1),
        nivel            NUMERIC(1),
        rpps             NUMERIC(1),
        orcamento_1      NUMERIC(1),
        orcamento_2      NUMERIC(1),
        orcamento_3      NUMERIC(1),
        orcamento_4      NUMERIC(1),
        valor_1          NUMERIC(14,2),
        valor_2          NUMERIC(14,2),
        valor_3          NUMERIC(14,2),
        valor_4          NUMERIC(14,2)
    );
    
    
    ---------------------------------------------
    -- cria a tabela temporaria com os valores --
    ---------------------------------------------
    CREATE TEMPORARY TABLE tmp_valor AS
    SELECT conta_receita.cod_estrutural as cod_estrutural
         , CASE WHEN(receita.cod_recurso = 50)
                THEN true
                ELSE false
           END AS rpps
         , receita.exercicio AS data
         , receita.vl_original AS valor
      FROM orcamento.receita
INNER JOIN orcamento.conta_receita
        ON receita.cod_conta = conta_receita.cod_conta
       AND receita.exercicio = conta_receita.exercicio
     WHERE receita.exercicio BETWEEN stExercicioInicial AND stExercicio;

    ---------------------------------------------------
    -- verifica se existe movimentacao para cada ano --
    ---------------------------------------------------
    SELECT CASE WHEN EXISTS ( SELECT 1
                                FROM tmp_valor
                               WHERE data = stExercicioInicial )
                THEN 1
                ELSE 0
           END
      INTO boOrcamento_1;

    SELECT CASE WHEN EXISTS ( SELECT 1
                                FROM tmp_valor
                               WHERE data = TRIM(TO_CHAR((TO_NUMBER(stExercicioInicial,'9999') + 1),'9999')))
                THEN 1
                ELSE 0
           END
      INTO boOrcamento_2;

    SELECT CASE WHEN EXISTS ( SELECT 1
                                FROM tmp_valor
                               WHERE data = TRIM(TO_CHAR((TO_NUMBER(stExercicioInicial,'9999') + 2),'9999')))
                THEN 1
                ELSE 0
           END
      INTO boOrcamento_3;

    SELECT CASE WHEN EXISTS ( SELECT 1
                                FROM tmp_valor
                               WHERE data = TRIM(TO_CHAR((TO_NUMBER(stExercicioInicial,'9999') + 3),'9999')))
                THEN 1
                ELSE 0
           END
      INTO boOrcamento_4;
    -------------------------------------------------------
    -- recupera os tipos de receita que vao no relatorio --
    -------------------------------------------------------
    stSql := 'SELECT *
                   , publico.fn_mascarareduzida(cod_estrutural) AS estrutural_reduzido 
                FROM ldo.tipo_receita_despesa
               WHERE tipo = ''R'' 
                 AND nivel = 1
            ORDER BY cod_tipo ';

    FOR reRecord IN EXECUTE stSql
    LOOP
        SELECT SUM(valor)
          INTO flValor_1
          FROM tmp_valor
         WHERE CASE WHEN reRecord.cod_estrutural = '9.7.0.0.00.00.00.00.00'
                    THEN cod_estrutural LIKE '9.%'
                    WHEN reRecord.cod_estrutural = '1.3.9.0.00.00.00.00.00'
                    THEN (cod_estrutural LIKE '1.3.%' AND cod_estrutural NOT LIKE '1.3.2.%')
                    ELSE cod_estrutural LIKE '' || reRecord.estrutural_reduzido || '%'
               END
           AND data = stExercicioInicial
           AND rpps = reRecord.rpps;
           

        SELECT SUM(valor)
          INTO flValor_2
          FROM tmp_valor
         WHERE CASE WHEN reRecord.cod_estrutural = '9.7.0.0.00.00.00.00.00'
                    THEN cod_estrutural LIKE '9.%'
                    WHEN reRecord.cod_estrutural = '1.3.9.0.00.00.00.00.00'
                    THEN (cod_estrutural LIKE '1.3.%' AND cod_estrutural NOT LIKE '1.3.2.%')
                    ELSE cod_estrutural LIKE '' || reRecord.estrutural_reduzido || '%'
               END
           AND data = TRIM(TO_CHAR((TO_NUMBER(stExercicioInicial,'9999') + 1),'9999'))
           AND rpps = reRecord.rpps;

        SELECT SUM(valor)
          INTO flValor_3
          FROM tmp_valor
         WHERE CASE WHEN reRecord.cod_estrutural = '9.7.0.0.00.00.00.00.00'
                    THEN cod_estrutural LIKE '9.%'
                    WHEN reRecord.cod_estrutural = '1.3.9.0.00.00.00.00.00'
                    THEN (cod_estrutural LIKE '1.3.%' AND cod_estrutural NOT LIKE '1.3.2.%')
                    ELSE cod_estrutural LIKE '' || reRecord.estrutural_reduzido || '%'
               END
           AND data = TRIM(TO_CHAR((TO_NUMBER(stExercicioInicial,'9999') + 2),'9999'))
           AND rpps = reRecord.rpps;

        SELECT SUM(valor)
          INTO flValor_4
          FROM tmp_valor
         WHERE CASE WHEN reRecord.cod_estrutural = '9.7.0.0.00.00.00.00.00'
                    THEN cod_estrutural LIKE '9.%'
                    WHEN reRecord.cod_estrutural = '1.3.9.0.00.00.00.00.00'
                    THEN (cod_estrutural LIKE '1.3.%' AND cod_estrutural NOT LIKE '1.3.2.%')
                    ELSE cod_estrutural LIKE '' || reRecord.estrutural_reduzido || '%'
               END
           AND data = TRIM(TO_CHAR((TO_NUMBER(stExercicioInicial,'9999') + 3),'9999'))
           AND rpps = reRecord.rpps;

        IF flValor_1 IS NULL THEN 
            SELECT COALESCE(vl_previsto_fixado,0) 
              INTO flValor_1
              FROM ldo.configuracao_receita_despesa  
             WHERE cod_tipo  = reRecord.cod_tipo
               AND tipo      = reRecord.tipo
               AND cod_ppa   = inCodPPA
               AND exercicio = stExercicioInicial;
        END IF;
       
        IF flValor_2 IS NULL THEN 
            SELECT COALESCE(vl_previsto_fixado,0) 
              INTO flValor_2
              FROM ldo.configuracao_receita_despesa  
             WHERE cod_tipo  = reRecord.cod_tipo
               AND tipo      = reRecord.tipo
               AND cod_ppa   = inCodPPA
               AND exercicio = (TO_NUMBER(stExercicioInicial,'9999') + 1)::varchar;
        END IF;

        IF flValor_3 IS NULL THEN 
            SELECT COALESCE(vl_previsto_fixado,0) 
              INTO flValor_3
              FROM ldo.configuracao_receita_despesa  
             WHERE cod_tipo  = reRecord.cod_tipo
               AND tipo      = reRecord.tipo
               AND cod_ppa   = inCodPPA
               AND exercicio = (TO_NUMBER(stExercicioInicial,'9999') + 2)::varchar ;
        END IF;

        IF flValor_4 IS NULL THEN
            SELECT COALESCE(vl_previsto_fixado,0) 
              INTO flValor_4
              FROM ldo.configuracao_receita_despesa  
             WHERE cod_tipo  = reRecord.cod_tipo
               AND tipo      = reRecord.tipo
               AND cod_ppa   = inCodPPA
               AND exercicio = (TO_NUMBER(stExercicioInicial,'9999') + 3)::varchar;
        END IF;
        -------------------------------------------------------------------------------------
        -- insere na tabela de retorno o somatorio do valor dos estruturais para os 4 anos --
        -------------------------------------------------------------------------------------
        INSERT INTO tmp_retorno VALUES( reRecord.cod_tipo
                                       ,stExercicio
                                       ,reRecord.cod_estrutural
                                       ,reRecord.descricao
                                       ,reRecord.tipo
                                       ,reRecord.nivel
                                       ,CASE WHEN (reRecord.rpps IS TRUE)
                                             THEN 1
                                             ELSE 0
                                        END
                                       ,boOrcamento_1
                                       ,boOrcamento_2
                                       ,boOrcamento_3
                                       ,boOrcamento_4
                                       ,COALESCE(flValor_1,0) 
                                       ,COALESCE(flValor_2,0) 
                                       ,COALESCE(flValor_3,0) 
                                       ,COALESCE(flValor_4,0) );


    END LOOP;
   
    ----------------------------------------------------------
    -- insere na tabela de retorno o somatorio dos niveis 0 --
    ----------------------------------------------------------
    stSql := ' SELECT publico.fn_mascarareduzida(cod_estrutural) AS estrutural_reduzido
                    , *
                 FROM ldo.tipo_receita_despesa
                WHERE nivel = 0
                  AND tipo = ''R''
             ORDER BY cod_tipo'; 
 
    FOR reRecord IN EXECUTE stSql
    LOOP
        SELECT SUM(valor_1) 
          INTO flValor_1
          FROM tmp_retorno
         WHERE cod_estrutural LIKE reRecord.estrutural_reduzido || '%' 
           AND nivel = 1;

         SELECT SUM(valor_2) 
          INTO flValor_2
          FROM tmp_retorno
         WHERE cod_estrutural LIKE reRecord.estrutural_reduzido || '%' 
           AND nivel = 1;  

        SELECT SUM(valor_3) 
          INTO flValor_3
          FROM tmp_retorno
         WHERE cod_estrutural LIKE reRecord.estrutural_reduzido || '%' 
           AND nivel = 1;

        SELECT SUM(valor_4) 
          INTO flValor_4
          FROM tmp_retorno
         WHERE cod_estrutural LIKE reRecord.estrutural_reduzido || '%' 
           AND nivel = 1;

        INSERT INTO tmp_retorno ( SELECT reRecord.cod_tipo
                                       , stExercicio
                                       , reRecord.cod_estrutural
                                       , reRecord.descricao
                                       , reRecord.tipo
                                       , reRecord.nivel
                                       , CASE WHEN (reRecord.rpps IS TRUE)
                                             THEN 1
                                             ELSE 0
                                         END
                                       , boOrcamento_1
                                       , boOrcamento_2
                                       , boOrcamento_3
                                       , boOrcamento_4
                                       , flValor_1
                                       , flValor_2
                                       , flValor_3
                                       , flValor_4);
    END LOOP;
 
    stSql := ' SELECT * 
                 FROM tmp_retorno
             ORDER BY cod_tipo';

    FOR reRecord IN EXECUTE stSql
    LOOP
        RETURN NEXT reRecord;
    END LOOP;
        
    DROP TABLE tmp_valor;
    DROP TABLE tmp_retorno;

END;

$$ LANGUAGE 'plpgsql';
 
