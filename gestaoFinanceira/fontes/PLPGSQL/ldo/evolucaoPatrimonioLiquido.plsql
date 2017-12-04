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
    * Retorna os valores para a rotina evolucao do patrimonio liquido
    * Data de Criação: 23/06/2009 
 
 
    * @author Analista:      Tonismar Bernardo <tonismar.bernardo@cnm.org.br> 
    * @author Desenvolvedor: Henrique Boaventura <henrique.boaventura@cnm.org.br> 
 
    * @package      URBEM 
    * @subpackage   LDO 
 
    * $Id: $ 
*/

CREATE OR REPLACE FUNCTION ldo.fn_evolucao_patrimonio_liquido(INTEGER,CHAR(4),BOOLEAN) RETURNS SETOF RECORD AS $$
DECLARE
    inCodPPA            ALIAS FOR $1;
    stExercicio         ALIAS FOR $2;
    boRPPS              ALIAS FOR $3;

    stExercicioInicial  VARCHAR;
    stExercicioFinal    VARCHAR;
    stSql               VARCHAR;
    stSqlAux            VARCHAR;
    
    nuValor1            NUMERIC(14,2);
    nuValor2            NUMERIC(14,2);
    nuValor3            NUMERIC(14,2);

    nuTotal1            NUMERIC(14,2) := 0;
    nuTotal2            NUMERIC(14,2) := 0;
    nuTotal3            NUMERIC(14,2) := 0;

    boOrcamento1        NUMERIC(1) := 0; 
    boOrcamento2        NUMERIC(1) := 0; 
    boOrcamento3        NUMERIC(1) := 0; 

    stTmpName           VARCHAR := '';

    reRecord            RECORD;

BEGIN
    stExercicioInicial := TRIM(TO_CHAR((TO_NUMBER(stExercicio,'9999') - 4),'9999'));
    stExercicioFinal   := TRIM(TO_CHAR((TO_NUMBER(stExercicio,'9999') - 2),'9999'));
    
    --verifica se a sequence evolucao_patrimonio_liquido_1 existe
    IF((SELECT 1 FROM pg_catalog.pg_statio_user_sequences WHERE relname='evolucao_patrimonio_liquido_1') IS NOT NULL) THEN
        SELECT 'tmp_retorno_' || NEXTVAL('stn.evolucao_patrimonio_liquido_1')
          INTO stTmpName;
    ELSE
        CREATE SEQUENCE stn.evolucao_patrimonio_liquido_1 START 1;
        SELECT 'tmp_retorno_' || NEXTVAL('stn.evolucao_patrimonio_liquido_1')
          INTO stTmpName;
    END IF;


    ---------------------------------------------------------------
    -- cria a tabela temporaria para guardar os dados de retorno --
    ---------------------------------------------------------------
    stSql := '
        CREATE TEMPORARY TABLE ' || stTmpName || ' (
            cod_tipo        INTEGER,
            descricao       VARCHAR(30),
            nivel           NUMERIC(1),
            rpps            NUMERIC(1),
            valor_1         NUMERIC(14,2),
            valor_2         NUMERIC(14,2),
            valor_3         NUMERIC(14,2),
            porcentagem_1   NUMERIC(5,2),
            porcentagem_2   NUMERIC(5,2),
            porcentagem_3   NUMERIC(5,2),
            orcamento_1     NUMERIC(1),
            orcamento_2     NUMERIC(1),
            orcamento_3     NUMERIC(1)
        )';
    EXECUTE stSql;

    stSql := 'SELECT * 
                FROM ldo.tipo_evolucao_patrimonio_liquido
               WHERE rpps = ' || CASE WHEN (boRPPS IS TRUE) THEN 'TRUE' ELSE 'FALSE' END || ''; 
    FOR reRecord IN EXECUTE stSql
    LOOP
        nuValor1 := (SELECT ldo.fn_calcula_evolucao_patrimonio_liquido(publico.fn_mascarareduzida(reRecord.cod_estrutural),stExercicioFinal,reRecord.rpps)) * -1;
        IF nuValor1 = 0
        THEN
            SELECT valor
              INTO nuValor1
              FROM ldo.configuracao_evolucao_patrimonio_liquido
             WHERE configuracao_evolucao_patrimonio_liquido.cod_tipo  = reRecord.cod_tipo
               AND configuracao_evolucao_patrimonio_liquido.cod_ppa   = inCodPPA
               AND configuracao_evolucao_patrimonio_liquido.rpps      = reRecord.rpps
               AND configuracao_evolucao_patrimonio_liquido.exercicio = stExercicio;
        ELSE 
            boOrcamento1 := 1;
        END IF;
        nuValor2 := (SELECT ldo.fn_calcula_evolucao_patrimonio_liquido(publico.fn_mascarareduzida(reRecord.cod_estrutural),TRIM(TO_CHAR((TO_NUMBER(stExercicioFinal,'9999') - 1),'9999')),reRecord.rpps)) * -1;
        IF nuValor2 = 0
        THEN
            SELECT valor
              INTO nuValor2
              FROM ldo.configuracao_evolucao_patrimonio_liquido
             WHERE configuracao_evolucao_patrimonio_liquido.cod_tipo  = reRecord.cod_tipo
               AND configuracao_evolucao_patrimonio_liquido.cod_ppa   = inCodPPA
               AND configuracao_evolucao_patrimonio_liquido.rpps      = reRecord.rpps
               AND configuracao_evolucao_patrimonio_liquido.exercicio = (TO_NUMBER(stExercicio::VARCHAR,'9999') + 1)::varchar;
        ELSE 
            boOrcamento2 := 1;
        END IF;
        nuValor3 := (SELECT ldo.fn_calcula_evolucao_patrimonio_liquido(publico.fn_mascarareduzida(reRecord.cod_estrutural),TRIM(TO_CHAR((TO_NUMBER(stExercicioFinal,'9999') - 2),'9999')),reRecord.rpps)) * -1;
        IF nuValor3 = 0
        THEN
            SELECT valor
              INTO nuValor3
              FROM ldo.configuracao_evolucao_patrimonio_liquido
             WHERE configuracao_evolucao_patrimonio_liquido.cod_tipo  = reRecord.cod_tipo
               AND configuracao_evolucao_patrimonio_liquido.cod_ppa   = inCodPPA
               AND configuracao_evolucao_patrimonio_liquido.rpps      = reRecord.rpps
               AND configuracao_evolucao_patrimonio_liquido.exercicio = (TO_NUMBER(stExercicio::VARCHAR,'9999') + 2)::varchar;
        ELSE 
            boOrcamento3 := 1;
        END IF;

        stSql := '
            INSERT INTO ' || stTmpName || ' VALUES ( ' || reRecord.cod_tipo || '
                                                    ,''' || reRecord.descricao || '''
        ';
        IF (reRecord.rpps IS TRUE)
        THEN    
            stSql := stSql || ', 1 ';
        ELSE 
            stSql := stSql || ', 0 ';
        END IF;
        stSql := stSql || '             ,1 
                                        ,' || COALESCE(nuValor1,0) || ' 
                                        ,' || COALESCE(nuValor2,0) || '
                                        ,' || COALESCE(nuValor3,0) || '
                                        ,0
                                        ,0
                                        ,0
                                        ,' || boOrcamento1 || ' 
                                        ,' || boOrcamento2 || '
                                        ,' || boOrcamento3 || ' )';
        EXECUTE stSql;
    END LOOP;

    ------------------------------------------------------
    -- totaliza o valor 3  e altera o valor 2 do tipo 1 --
    ------------------------------------------------------
    stSql := '
         SELECT SUM(COALESCE(valor_3,0)) AS valor_3
           FROM ' || stTmpName || ' ';
    FOR reRecord IN EXECUTE stSql
    LOOP
        nuTotal3 = reRecord.valor_3;
    END LOOP;

    stSql := '
        UPDATE ' || stTmpName || '
           SET valor_2 = ' || nuTotal3 || '
         WHERE cod_tipo = 1
    ';
    EXECUTE stSql;

    ------------------------------------------------------
    -- totaliza o valor 2  e altera o valor 1 do tipo 1 --
    ------------------------------------------------------
    stSql := '
         SELECT SUM(COALESCE(valor_2,0)) AS valor_2
           FROM ' || stTmpName || ' ';
    FOR reRecord IN EXECUTE stSql
    LOOP
        nuTotal2 = reRecord.valor_2;
    END LOOP;

    stSql := '
        UPDATE ' || stTmpName || '
           SET valor_1 = ' || nuTotal2 || '
         WHERE cod_tipo = 1
    ';
    EXECUTE stSql; 

    stSql := '
         SELECT SUM(COALESCE(valor_1,0)) AS valor_1
           FROM ' || stTmpName || ' ';
    FOR reRecord IN EXECUTE stSql
    LOOP
        nuTotal1 = reRecord.valor_1;
    END LOOP;

    --------------------------------------------
    -- recupera os dados para o retorno da pl --
    --------------------------------------------
    stSql := '
        SELECT * 
          FROM ' || stTmpName || '
      ORDER BY cod_tipo
    ';

    FOR reRecord IN EXECUTE stSql
    LOOP
        IF (reRecord.cod_tipo = 1 ) 
        THEN
            -- seta o valor como do orcamento
            reRecord.orcamento_1 := 1;
            reRecord.orcamento_2 := 1;
        ELSE
            reRecord.orcamento_1 := boOrcamento1;
            reRecord.orcamento_2 := boOrcamento2;
        END IF;
        reRecord.orcamento_3 := boOrcamento3;

        -----------------------------------------------------
        -- calcula a porcentagem para cada uma das colunas --
        -----------------------------------------------------
        IF nuTotal1 <> 0
        THEN
            reRecord.porcentagem_1 := ROUND((reRecord.valor_1 * 100) / nuTotal1,2);
        END IF;
        
        IF nuTotal2 <> 0
        THEN
            reRecord.porcentagem_2 := ROUND((reRecord.valor_2 * 100) / nuTotal2,2);
        END IF;

        IF nuTotal3 <> 0
        THEN
            reRecord.porcentagem_3 := ROUND((reRecord.valor_3 * 100) / nuTotal3,2);
        END IF;       

        RETURN NEXT reRecord;
    END LOOP;

    stSql := 'DROP TABLE ' || stTmpName;
    EXECUTE stSql;

END;

$$ LANGUAGE 'plpgsql'
