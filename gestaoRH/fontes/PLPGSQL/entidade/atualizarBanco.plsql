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
--/**
--    * Função PLSQL
--    * Data de Criação: 09/07/2007
--
--
--    * @author Desenvolvedor: Diego Lemos de Souza
--
--    * Casos de uso: uc-04.00.00
--
--    $Id: atualizarBanco.sql 31697 2008-08-04 19:33:31Z souzadl $
--*/
CREATE OR REPLACE FUNCTION atualizarBanco(VARCHAR) RETURNS BOOLEAN as $$
DECLARE
    stSqlParametro              ALIAS FOR $1;
    inExercicio                 INTEGER;
    inCodEntidadePrefeitura     INTEGER;    
    stSql                       VARCHAR;
    stInsert                    VARCHAR;
    stBanco                     VARCHAR;
    stEntidade                  VARCHAR;
    stNomeSchema                VARCHAR;
    stNomeTriger                VARCHAR;
    stArray                     VARCHAR[];
    boEsquema                   BOOLEAN:=FALSE;
    boTrigger                   BOOLEAN:=FALSE;
    boGranteEsquema             BOOLEAN:=FALSE;
    boRetorno                   BOOLEAN;
    reRegistro                  RECORD;
    reSchema                    RECORD;
BEGIN
    EXECUTE stSqlParametro;

    inExercicio := selectIntoInteger('SELECT valor FROM administracao.configuracao WHERE parametro = ''ano_exercicio'' ORDER BY exercicio desc LIMIT 1');
    inCodEntidadePrefeitura := selectIntoInteger('SELECT valor::integer as valor
                                                    FROM administracao.configuracao
                                                   WHERE parametro = ''cod_entidade_prefeitura''
                                                     AND exercicio = '|| quote_literal(inExercicio) ||' ');


    IF strpos(trim(upper(stSqlParametro)),upper('CREATE SCHEMA')) > 0 THEN
        boEsquema    := TRUE;
        stNomeSchema := trim(translate(stSqlParametro,'CREATE SCHEMA ;',''));
        stInsert     := 'INSERT INTO administracao.schema_rh (schema_cod,schema_nome) VALUES ((SELECT max(schema_cod) FROM administracao.schema_rh)+1,'|| quote_literal(stNomeSchema) ||')';      
        EXECUTE stInsert;
        
        stSql := 'SELECT TRUE as retorno
                    FROM administracao.entidade_rh
                   WHERE cod_entidade = '|| inCodEntidadePrefeitura ||'
                   LIMIT 1';
        boRetorno := selectIntoBoolean(stSql);
        IF boRetorno IS TRUE THEN
            stInsert := 'INSERT INTO administracao.entidade_rh (exercicio,cod_entidade,schema_cod) 
                         VALUES
                         ('|| quote_literal(inExercicio) ||','|| inCodEntidadePrefeitura ||',(SELECT max(schema_cod) FROM administracao.schema_rh))';
            EXECUTE stInsert;
        END IF;
    END IF;
    IF strpos(trim(upper(stSqlParametro)),upper('CREATE TRIGGER')) > 0 THEN
        boTrigger    := TRUE;
        stArray      := string_to_array( stSqlParametro, ' ');
        stNomeTriger := stArray[3];
    END IF;
    IF strpos(trim(upper(stSqlParametro)),upper('GRANT ALL ON SCHEMA')) > 0 THEN
        boGranteEsquema := TRUE;
        stArray         := string_to_array( stSqlParametro, ' ');
        stNomeSchema    := stArray[5];
    END IF;

    stSql := '  SELECT entidade.cod_entidade
                  FROM orcamento.entidade
                 WHERE exercicio = '|| quote_literal(inExercicio) ||'
                   AND cod_entidade IN (SELECT cod_entidade
                                          FROM administracao.entidade_rh
                                         WHERE exercicio = '|| quote_literal(inExercicio) ||'
                                        GROUP BY cod_entidade)
                   AND cod_entidade != ('|| inCodEntidadePrefeitura ||')';
    FOR reRegistro IN EXECUTE stSql LOOP
        stBanco := stSqlParametro;        
        IF boEsquema THEN
            stBanco := trim(replace(stSqlParametro,';','')) ||'_'|| reRegistro.cod_entidade ||';';

            stInsert := 'INSERT INTO administracao.entidade_rh (exercicio,cod_entidade,schema_cod) 
                         VALUES
                         ('|| quote_literal(inExercicio) ||','|| reRegistro.cod_entidade ||',(SELECT max(schema_cod) FROM administracao.schema_rh))';
            EXECUTE stInsert;
        ELSIF boTrigger THEN
            stBanco := trim(replace(stSqlParametro,stNomeTriger,stNomeTriger ||'_'|| reRegistro.cod_entidade));
            stSql := 'SELECT * FROM administracao.schema_rh';
            FOR reSchema IN EXECUTE stSql LOOP
                stBanco := replace(stBanco,' '|| reSchema.schema_nome ||'.',' '|| reSchema.schema_nome ||'_'|| reRegistro.cod_entidade ||'.');
            END LOOP;
        ELSIF boGranteEsquema THEN
            stBanco := replace(stBanco, stNomeSchema,' '|| stNomeSchema ||'_'|| reRegistro.cod_entidade);
        ELSE 
            stSql := 'SELECT * FROM administracao.schema_rh';
            FOR reSchema IN EXECUTE stSql LOOP
                stBanco := replace(stBanco,' '|| reSchema.schema_nome ||'.',' '|| reSchema.schema_nome ||'_'|| reRegistro.cod_entidade ||'.');
            END LOOP;
        END IF;
        EXECUTE stBanco;
    END LOOP;
    RETURN TRUE;
END;
$$ LANGUAGE 'plpgsql';
