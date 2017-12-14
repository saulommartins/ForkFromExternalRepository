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
/* replicarEsquemasRH
 * 
 * Data de Criação   : 13/06/2007


 * @author Analista      Dagiane Vieira
 * @author Desenvolvedor Diego Lemos
 
 * @package URBEM
 * @subpackage 

 $Id: replicarEsquemasRH.plsql 65858 2016-06-22 14:20:46Z evandro $
 */

CREATE OR REPLACE FUNCTION replicarEsquemasRH(INTEGER,VARCHAR) RETURNS BOOLEAN AS $$
DECLARE
    inCodEntidade       ALIAS FOR $1;
    stExercicio         ALIAS FOR $2;
    inIndex             INTEGER:=1;
    inIndex2            INTEGER:=1;
    inCodPeriodoMovimentacao INTEGER := 1;
    inCodModulo         INTEGER;
    inCodBiblioteca     INTEGER;
    inCodEntidadePrefeitura     INTEGER;
    inIncluirEntidadePrefeitura INTEGER;
    inDias              INTEGER := 30;
    arEsquemas          VARCHAR[];
    arAlter             VARCHAR[];
    arTabelasSistema    VARCHAR[];
    arModulos           VARCHAR[];
    arPeriodo           VARCHAR[];
    stEsquema           VARCHAR;
    stSql               VARCHAR;
    stCreate            VARCHAR;
    stAlter             VARCHAR;
    stReference         VARCHAR;
    stNomePk            VARCHAR;
    stCamposPk          VARCHAR;
    stNomeEntidade      VARCHAR;
    stEsquemas          VARCHAR:='';
    stTabelas           VARCHAR:='';
    stPrimary           VARCHAR:='';
    stForeign           VARCHAR:='';
    stIndex             VARCHAR:='';
    stDefault           VARCHAR:='';
    stTabelaSistema     VARCHAR:='';
    stInsert            VARCHAR;
    stDelete            VARCHAR;
    reTabela            RECORD;
    reColuna            RECORD;
    rePK                RECORD;
    reRegistro          RECORD;
    reRegistro2         RECORD;
    reSequencia         RECORD;
    dtPeriodoInicial    DATE;
    dtPeriodoFinal      DATE;
    dtComparacao        DATE;    
    stTimestamp         TIMESTAMP;
BEGIN
    --arEsquemas := '{"ima",
    --                "folhapagamento",
    --                "pessoal",
    --                "estagio",
    --                "concurso",
    --                "beneficio",
    --                "calendario"}';
    
    --inIndex := 1;
    inCodEntidadePrefeitura := selectIntoInteger('SELECT valor FROM administracao.configuracao WHERE parametro = ''cod_entidade_prefeitura'' AND exercicio = '|| quote_literal(stExercicio) ||'');
    inIncluirEntidadePrefeitura := selectIntoInteger('SELECT count(*) FROM administracao.entidade_rh');
    stSql := 'SELECT * FROM administracao.schema_rh';
    FOR reRegistro IN EXECUTE stSql LOOP
        --INCLUSÃO DOS ESQUEMAS REFERENTES A ENTIDADE PREFEITURA
        IF inIncluirEntidadePrefeitura = 0 THEN
            EXECUTE 'INSERT INTO administracao.entidade_rh (exercicio,cod_entidade,schema_cod) VALUES ('|| quote_literal(stExercicio) ||','|| inCodEntidadePrefeitura ||','|| reRegistro.schema_cod ||');';
        END IF;
        --INCLUSÃO DOS ESQUEMAS REFERENTES A ENTIDADE PREFEITURA

        EXECUTE 'INSERT INTO administracao.entidade_rh (exercicio,cod_entidade,schema_cod) VALUES ('|| quote_literal(stExercicio) ||','|| inCodEntidade ||','|| reRegistro.schema_cod ||');';

    
        stEsquema := reRegistro.schema_nome ||'_'|| inCodEntidade;
        EXECUTE 'CREATE SCHEMA '|| stEsquema ||';';
        EXECUTE 'GRANT USAGE ON SCHEMA '|| stEsquema ||' TO urbem;';
        --stEsquemas := stEsquemas || 'CREATE SCHEMA '||stEsquema||';\n';

        --INÍCIO DAS SEQUENCIAS##############################################################
        stSql := 'SELECT relname
                    FROM pg_statio_all_sequences
                   WHERE schemaname = '|| quote_literal(reRegistro.schema_nome) ||'';
        FOR reSequencia IN EXECUTE stSql LOOP
            stCreate := 'CREATE SEQUENCE '|| stEsquema ||'.'|| reSequencia.relname ||';';
            EXECUTE stCreate;
        END LOOP;
        --FIM DAS SEQUENCIAS##############################################################    

        stSql := '   SELECT pg_namespace.nspname as Esquema
                          , pg_class.relname     as Nome
                       FROM pg_catalog.pg_class
                  LEFT JOIN pg_catalog.pg_namespace ON (pg_namespace.oid  = pg_class.relnamespace)
                      WHERE pg_class.relkind = ''r''
                        AND pg_namespace.nspname NOT IN ( ''pg_catalog'', ''pg_toast'', ''pg_temp_1'', ''information_schema'' )
                        AND Substr( pg_class.relname, 01, 03 ) != ''pga''
                        AND pg_namespace.nspname = '|| quote_literal(reRegistro.schema_nome) ||'
                   ORDER BY 1, 2';
        FOR reTabela IN EXECUTE stSql LOOP
            --INÍCIO DAS TABELAS##############################################################
            --CRIAÇÃO DOS SCRIPTS DE CRIAÇÃO DAS TABELAS
            stCreate := 'CREATE TABLE '|| stEsquema ||'.'|| reTabela.Nome ||'(';
            stSql := 'SELECT pg_namespace.nspname  as Esquema,
                             pg_class.relname      as Nome   ,
                             pg_class.oid          as id_tabela ,
                             pg_attribute.attname                                                  as coluna,
                             pg_attribute.attnum                                                   as id_coluna ,
                             pg_catalog.format_type(pg_attribute.atttypid, pg_attribute.atttypmod) as tipo,
                             pg_attribute.attnotnull                                               as notNull
                        FROM pg_catalog.pg_class
                             LEFT JOIN pg_catalog.pg_namespace ON (pg_namespace.oid  = pg_class.relnamespace),
                             pg_catalog.pg_attribute
                      WHERE pg_class.relkind = ''r''
                        AND pg_namespace.nspname NOT IN ( ''pg_catalog'', ''pg_toast'', ''pg_temp_1'', ''information_schema'' )
                        AND Substr( pg_class.relname, 01, 03 ) != ''pga''
                        AND pg_attribute.attrelid = pg_class.oid
                        AND pg_attribute.attnum > 0   -- colunas do sistema tem valor nedativo ( como oid ).
                        AND NOT pg_attribute.attisdropped
                        AND pg_class.relname = '|| quote_literal(reTabela.Nome) ||'
                        AND pg_namespace.nspname = '|| quote_literal(reTabela.Esquema) ||'
                   ORDER BY pg_class.oid'; 
            FOR reColuna IN EXECUTE stSql LOOP
                --IF reColuna.Nome = 'faixa_desconto' THEN                
                --END IF;
                stCreate := stCreate || reColuna.coluna::varchar ||' '|| reColuna.tipo::VARCHAR;
                SELECT INTO stDefault (SELECT adsrc
                                         FROM pg_attrdef
                                        WHERE adrelid = reColuna.id_tabela
                                          AND adnum = reColuna.id_coluna);                
                IF stDefault IS NOT NULL THEN
                    --Verifica a definição correda das sequencias.
                    SELECT * 
                      INTO reSequencia
                      FROM pg_statio_all_sequences 
                     WHERE schemaname = reRegistro.schema_nome
                       AND stDefault ilike '%'||relname||'%';
                    IF reSequencia.relid IS NOT NULL THEN
                        stDefault := replace(stDefault,reSequencia.relname,stEsquema ||'.'|| reSequencia.relname);
                    END IF;

                    stCreate := stCreate ||' DEFAULT '|| stDefault;
                END IF;
                IF reColuna.notNull IS TRUE THEN
                    stCreate := stCreate ||' NOT NULL,';
                ELSE 
                    stCreate := stCreate ||',';
                END IF;
            END LOOP;
            stCreate := SUBSTR(stCreate,0,char_length(stCreate))||');';
            EXECUTE stCreate;
            EXECUTE 'grant insert, delete, update, SELECT ON '|| stEsquema ||'.'|| reTabela.Nome ||' to group urbem;';
            --stTabelas := stTabelas || stCreate || '\n';
            --FIM DAS TABELAS#####################################################################
        END LOOP;
        inIndex := inIndex + 1;
    END LOOP;            

    --inIndex := 1;
    --WHILE arEsquemas[inIndex] IS NOT NULL LOOP
    stSql := 'SELECT * FROM administracao.schema_rh';
    FOR reRegistro IN EXECUTE stSql LOOP
        stEsquema := reRegistro.schema_nome ||'_'|| inCodEntidade;
        stSql := '   SELECT pg_namespace.nspname as Esquema
                          , pg_class.relname     as Nome
                       FROM pg_catalog.pg_class
                  LEFT JOIN pg_catalog.pg_namespace ON (pg_namespace.oid  = pg_class.relnamespace)
                      WHERE pg_class.relkind = ''r''
                        AND pg_namespace.nspname NOT IN ( ''pg_catalog'', ''pg_toast'', ''pg_temp_1'', ''information_schema'' )
                        AND Substr( pg_class.relname, 01, 03 ) != ''pga''
                        AND pg_namespace.nspname = '|| quote_literal(reRegistro.schema_nome) ||'
                   ORDER BY 1, 2';
        FOR reTabela IN EXECUTE stSql LOOP
            --INÍCIO DAS PRIMARY KEY##############################################################
            --CRIAÇÃO DOS SCRIPTS DE ALTERAÇÃO DAS TABELAS PARA A INCLUSÃO DAS PRIMARY KEY
            stSql := '   SELECT pg_namespace.nspname   as Esquema,
                                pg_class.relname       as Nome,
                                pg_index.indisprimary  as PK,
                                pg_index.indisunique   as UK,                                
                                pg_catalog.pg_get_indexdef(pg_index.indexrelid) as def
                           FROM pg_catalog.pg_class
                                LEFT JOIN pg_catalog.pg_namespace ON (pg_namespace.oid  = pg_class.relnamespace),
                                pg_catalog.pg_index
                          WHERE pg_class.relkind = ''r''
                            AND pg_namespace.nspname NOT IN ( ''pg_catalog'', ''pg_toast'', ''pg_temp_1'', ''information_schema'' )
                            AND Substr( pg_class.relname, 01, 03 ) != ''pga''
                            AND pg_class.oid     = pg_index.indrelid
                            AND pg_index.indisprimary
                            AND pg_class.relname = '|| quote_literal(reTabela.Nome) ||' 
                            AND pg_namespace.nspname = '|| quote_literal(reTabela.Esquema) ||'
                    ORDER BY 1, 2, 5;';
            FOR rePK IN EXECUTE stSql LOOP
                arAlter := string_to_array(rePk.def,' ');
                stNomePk := arAlter[4];
                arAlter := string_to_array(rePk.def,'btree');
                stCamposPk := arAlter[2];
                stAlter := 'ALTER TABLE ONLY '|| stEsquema ||'.'|| rePK.Nome ||' ADD CONSTRAINT '|| stNomePk ||' PRIMARY KEY '|| stCamposPk ||';';
                EXECUTE stAlter;
                --stPrimary := stPrimary || stAlter || '\n';
            END LOOP;
            --FIM DAS PRIMARY KEY################################################################
        END LOOP;
        inIndex := inIndex + 1;
    END LOOP;

    --inIndex := 1;
    --WHILE arEsquemas[inIndex] IS NOT NULL LOOP
    stSql := 'SELECT * FROM administracao.schema_rh';
    FOR reRegistro IN EXECUTE stSql LOOP
        stEsquema := reRegistro.schema_nome ||'_'|| inCodEntidade;
        stSql := '   SELECT pg_namespace.nspname as Esquema
                          , pg_class.relname     as Nome
                       FROM pg_catalog.pg_class
                  LEFT JOIN pg_catalog.pg_namespace ON (pg_namespace.oid  = pg_class.relnamespace)
                      WHERE pg_class.relkind = ''r''
                        AND pg_namespace.nspname NOT IN ( ''pg_catalog'', ''pg_toast'', ''pg_temp_1'', ''information_schema'' )
                        AND Substr( pg_class.relname, 01, 03 ) != ''pga''
                        AND pg_namespace.nspname = '|| quote_literal(reRegistro.schema_nome) ||'
                   ORDER BY 1, 2';
        FOR reTabela IN EXECUTE stSql LOOP
            --INÍCIO DOS INDICES#################################################################
            --CRIAÇÃO DOS SCRIPTS DE ALTERAÇÃO DAS TABELAS PARA A INCLUSÃO DOS INDICES
            stSql := '   SELECT pg_namespace.nspname  as Esquema,
                                pg_class.relname       as Nome,
                                pg_index.indisprimary  as PK,
                                pg_index.indisunique   as UK,
                                pg_catalog.pg_get_indexdef(pg_index.indexrelid) as def
                           FROM pg_catalog.pg_class
                                LEFT JOIN pg_catalog.pg_namespace ON (pg_namespace.oid  = pg_class.relnamespace),
                                pg_catalog.pg_index
                          WHERE pg_class.relkind = ''r''
                            AND pg_namespace.nspname NOT IN ( ''pg_catalog'', ''pg_toast'', ''pg_temp_1'', ''information_schema'' )
                            AND Substr( pg_class.relname, 01, 03 ) != ''pga''
                            AND pg_class.oid     = pg_index.indrelid
                            AND NOT pg_index.indisprimary
                            AND pg_class.relname = '|| quote_literal(reTabela.Nome) ||'
                            AND pg_namespace.nspname = '|| quote_literal(reTabela.Esquema) ||'
                       ORDER BY 1, 2, 5;';
            FOR rePK IN EXECUTE stSql LOOP
                arAlter := string_to_array(rePk.def,' ');
                IF arAlter[2] = 'INDEX' THEN
                    stAlter = replace(rePk.def,rePk.Esquema,stEsquema);
                ELSE
                    stAlter = 'ALTER TABLE ONLY '|| stEsquema ||'.'|| rePk.Nome ||' ADD CONSTRAINT '|| arAlter[4] ||' UNIQUE '|| arAlter[9] ||';';
                END IF;
                EXECUTE stAlter;
                --stIndex := stIndex || stAlter || '\n';
            END LOOP;
            --FIM DOS INDICES#################################################################
        END LOOP;
        inIndex := inIndex + 1;
    END LOOP;

    --inIndex := 1;
    --WHILE arEsquemas[inIndex] IS NOT NULL LOOP
    stSql := 'SELECT * FROM administracao.schema_rh';
    FOR reRegistro IN EXECUTE stSql LOOP
        stEsquema := reRegistro.schema_nome ||'_'|| inCodEntidade;
        stSql := '   SELECT pg_namespace.nspname as Esquema
                          , pg_class.relname     as Nome
                       FROM pg_catalog.pg_class
                  LEFT JOIN pg_catalog.pg_namespace ON (pg_namespace.oid  = pg_class.relnamespace)
                      WHERE pg_class.relkind = ''r''
                        AND pg_namespace.nspname NOT IN ( ''pg_catalog'', ''pg_toast'', ''pg_temp_1'', ''information_schema'' )
                        AND Substr( pg_class.relname, 01, 03 ) != ''pga''
                        AND pg_namespace.nspname = '|| quote_literal(reRegistro.schema_nome) ||'
                   ORDER BY 1, 2';
        FOR reTabela IN EXECUTE stSql LOOP
            --INÍCIO DAS FOREIGN KEY#############################################################
            --CRIAÇÃO DOS SCRIPTS DE ALTERAÇÃO DAS TABELAS PARA A INCLUSÃO DAS FOREIGN KEY
            stSql := 'SELECT pg_namespace.nspname as "Esquema",
                             pg_class.relname                                   as Nome,
                             conname                                            as CONSTRAINT,
                             pg_catalog.pg_get_constraintdef(pg_constraint.oid) as FK
                        FROM pg_catalog.pg_class
                             LEFT JOIN pg_catalog.pg_namespace ON (pg_namespace.oid  = pg_class.relnamespace),
                             pg_catalog.pg_constraint
                       WHERE pg_class.relkind = ''r''
                         AND pg_namespace.nspname NOT IN ( ''pg_catalog'', ''pg_toast'', ''pg_temp_1'', ''information_schema'' )
                         AND Substr( pg_class.relname, 01, 03 ) != ''pga''
                         AND pg_constraint.contype  = ''f''
                         AND pg_constraint.conrelid     = pg_class.oid
                         AND pg_class.relname = '|| quote_literal(reTabela.Nome) ||'
                         AND pg_namespace.nspname = '|| quote_literal(reTabela.Esquema) ||'
                       ORDER BY 1, 2, 3;';
            FOR rePK IN EXECUTE stSql LOOP
                inIndex2 := 1;
                stReference := rePk.FK;
                stSql := 'SELECT * FROM administracao.schema_rh';
                FOR reRegistro2 IN EXECUTE stSql LOOP
                    stReference := replace(stReference,reRegistro2.schema_nome ||'.',reRegistro2.schema_nome ||'_'|| inCodEntidade ||'.');
                    inIndex2 := inIndex2 + 1;
                END LOOP;
                stAlter = 'ALTER TABLE ONLY '|| stEsquema ||'.'|| rePk.Nome ||' ADD CONSTRAINT '|| rePk.CONSTRAINT ||' '|| stReference ||';';
                EXECUTE stAlter;
                --stForeign := stForeign || stAlter || '\n';
            END LOOP;
            --FIM DAS FOREIGN KEY################################################################
        END LOOP;       
        inIndex := inIndex + 1;
    END LOOP;  

    --INÍCIO DA CÓPIA DAS INFORMAÇÕES DE TABELAS DE SISTEMA
    stSql := 'SELECT tabelas_rh.nome_tabela
                 , schema_rh.schema_nome
              FROM administracao.tabelas_rh
                 , administracao.schema_rh
             WHERE tabelas_rh.schema_cod = schema_rh.schema_cod
          ORDER BY tabelas_rh.sequencia';
    FOR reRegistro IN EXECUTE stSql LOOP
        --stTabelaSistema := replace(arTabelasSistema[inIndex],'.','_'||inCodEntidade||'.');        
        stSql := 'INSERT INTO '|| reRegistro.schema_nome ||'_'|| inCodEntidade ||'.'|| reRegistro.nome_tabela ||' SELECT * FROM '|| reRegistro.schema_nome ||'.'|| reRegistro.nome_tabela;
        RAISE NOTICE 'stSql: %', stSql;
        EXECUTE stSql;
        --inIndex := inIndex + 1;
    END LOOP;
    --FIM DA CÓPIA DAS INFORMAÇÕES DE TABELAS DE SISTEMA


    --INÍCIO DA CRIAÇÃO DE UMA NOVA BIBLIOTECA PARA AS FUNÇÕES DO NOVO ESQUEMA
    inCodModulo     := 27;
    inCodBiblioteca := selectIntoInteger('SELECT max(cod_biblioteca)+1 as cod_biblioteca FROM administracao.biblioteca WHERE cod_modulo = '|| inCodModulo);
    stNomeEntidade  := selectIntoVarchar('SELECT sw_cgm.nom_cgm
                                            FROM orcamento.entidade
                                               , sw_cgm
                                           WHERE entidade.numcgm = sw_cgm.numcgm
                                             AND entidade.exercicio = '|| quote_literal(stExercicio) ||'
                                             AND entidade.cod_entidade = '|| inCodEntidade);
    stSql := 'INSERT INTO administracao.biblioteca (cod_modulo,cod_biblioteca,nom_biblioteca) VALUES
                                                   ('|| inCodModulo ||','|| inCodBiblioteca ||',substr('|| quote_literal(stNomeEntidade) ||',1,50))';    
    EXECUTE stSql;
    stSql := 'INSERT INTO administracao.biblioteca_entidade (cod_modulo,cod_biblioteca,exercicio,cod_entidade) VALUES
                                                            ('|| inCodModulo ||','|| inCodBiblioteca ||','|| quote_literal(stExercicio) ||','|| inCodEntidade ||')';
    EXECUTE stSql;
    --FIM DA CRIAÇÃO DE UMA NOVA BIBLIOTECA PARA AS FUNÇÕES DO NOVO ESQUEMA

    --INÍCIO DA CÓPIA DOS PARAMETROS DA TABELA administracao.configuracao
    --arModulos := '{27,40,17,22}';
    --inIndex := 1;
    --WHILE arModulos[inIndex] IS NOT NULL LOOP
    stSql := 'SELECT * FROM administracao.modulo WHERE cod_gestao = 4';
    FOR reRegistro IN EXECUTE stSql LOOP
        inCodModulo := reRegistro.cod_modulo;
        stSql := 'SELECT * 
                   FROM administracao.configuracao 
                  WHERE cod_modulo = '|| inCodModulo ||' 
                    AND exercicio = '|| quote_literal(stExercicio) ||'  
                    AND NOT EXISTS (SELECT *
                                      FROM administracao.configuracao_entidade
                                     WHERE cod_modulo = '|| inCodModulo ||'
                                       AND exercicio = '|| quote_literal(stExercicio) ||'
                                       AND cod_modulo = configuracao.cod_modulo
                                       AND exercicio = configuracao.exercicio
                                       AND parametro = configuracao.parametro
                                       AND valor = configuracao.valor)';
        FOR reRegistro IN EXECUTE stSql LOOP
            stSql := 'INSERT INTO administracao.configuracao (exercicio,cod_modulo,parametro,valor) VALUES ('|| quote_literal(stExercicio) ||','|| inCodModulo ||','|| quote_literal(reRegistro.parametro ||'_'|| inCodEntidade) ||','|| quote_literal(reRegistro.valor) ||');';
            EXECUTE stSql;
            stSql := 'INSERT INTO administracao.configuracao_entidade (exercicio,cod_modulo,parametro,cod_entidade,valor) VALUES ('|| quote_literal(stExercicio) ||','|| inCodModulo ||','|| quote_literal(reRegistro.parametro ||'_'|| inCodEntidade) ||','|| inCodEntidade ||','|| quote_literal(reRegistro.valor) ||');';
            EXECUTE stSql;
        END LOOP;
        --inIndex := inIndex + 1;
    END LOOP;
    --FIM DA CÓPIA DOS PARAMETROS DA TABELA administracao.configuracao

    --CRIAÇÃO DOS PERÍODO DE MOVIMENTAÇÃO 
    --Data inicial: 01/01/1950
    --Data final: data atual
    dtPeriodoInicial    := '1950-01-01';
    dtComparacao        := TO_DATE(TO_CHAR(now(),'yyyy-mm-dd'),'yyyy-mm-dd');
    stTimestamp         := now()::timestamp(3);
    WHILE dtPeriodoInicial < dtComparacao LOOP
        arPeriodo := string_to_array(dtPeriodoInicial::VARCHAR,'-');
        IF arPeriodo[2] = '01' or
           arPeriodo[2] = '03' or
           arPeriodo[2] = '05' or
           arPeriodo[2] = '07' or
           arPeriodo[2] = '08' or
           arPeriodo[2] = '10' or
           arPeriodo[2] = '12' THEN
            inDias := 30;
        ELSE
            IF arPeriodo[2] = '02' THEN
                IF arPeriodo[1]::integer%4 = 0 THEN
                    inDias := 28;
                ELSE
                    inDias := 27;
                END IF;
            ELSE
                inDias := 29;
            END IF;
        END IF;

        dtPeriodoFinal := dtPeriodoInicial + inDias;
        stInsert := 'INSERT INTO folhapagamento_'|| inCodEntidade ||'.periodo_movimentacao
                     (cod_periodo_movimentacao,dt_inicial,dt_final)
                     VALUES 
                     ('|| inCodPeriodoMovimentacao ||','|| quote_literal(dtPeriodoInicial) ||','|| quote_literal(dtPeriodoFinal) ||')';
        EXECUTE stInsert;
        stInsert := 'INSERT INTO folhapagamento_'|| inCodEntidade ||'.periodo_movimentacao_situacao 
                     (cod_periodo_movimentacao,timestamp,situacao)
                     VALUES 
                     ('|| inCodPeriodoMovimentacao ||','|| quote_literal(stTimestamp) ||',''a'')';
        EXECUTE stInsert;
        stTimestamp := stTimestamp + (time '00:00:01');
        stInsert := 'INSERT INTO folhapagamento_'|| inCodEntidade ||'.periodo_movimentacao_situacao 
                     (cod_periodo_movimentacao,timestamp,situacao)
                     VALUES 
                     ('|| inCodPeriodoMovimentacao ||','|| quote_literal(stTimestamp) ||',''f'')';
        EXECUTE stInsert;
        stTimestamp := stTimestamp + (time '00:00:01');
        inCodPeriodoMovimentacao := inCodPeriodoMovimentacao + 1;
        dtPeriodoInicial := to_date(dtPeriodoFinal::VARCHAR,'yyyy-mm-dd') + 1 ;
    END LOOP;
    stDelete := 'DELETE FROM folhapagamento_'|| inCodEntidade ||'.periodo_movimentacao_situacao 
                  WHERE cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao-1 ||' AND situacao = ''f''';
    EXECUTE stDelete;
    --CRIAÇÃO DOS PERÍODO DE MOVIMENTAÇÃO 
    
    -- CRIA TRIGGER DE CONTRATO SERVIDOR CONTA SALARIO / SALARIO HISTORICO
    stInsert := '   CREATE OR REPLACE FUNCTION pessoal_'|| inCodEntidade ||'.fn_contrato_servidor_conta_salario_historico() RETURNS TRIGGER AS $temp$
                    DECLARE
                        reContaSalario      RECORD;
                    BEGIN
                        If TG_OP=''INSERT'' THEN
                            INSERT INTO pessoal_'|| inCodEntidade ||'.contrato_servidor_conta_salario_historico
                            (cod_contrato,cod_banco,cod_agencia,nr_conta) VALUES 
                            (new.cod_contrato,new.cod_banco,new.cod_agencia,new.nr_conta);
                        ELSE
                             SELECT contrato_servidor_conta_salario.*
                               INTO reContaSalario
                               FROM pessoal_'|| inCodEntidade ||'.contrato_servidor_conta_salario
                              WHERE contrato_servidor_conta_salario.cod_contrato = new.cod_contrato
                                AND contrato_servidor_conta_salario.cod_banco    = new.cod_banco
                                AND contrato_servidor_conta_salario.cod_agencia  = new.cod_agencia
                                AND contrato_servidor_conta_salario.nr_conta     = new.nr_conta;
                    
                            IF reContaSalario.cod_contrato IS NULL THEN
                                INSERT INTO pessoal_'|| inCodEntidade ||'.contrato_servidor_conta_salario_historico
                                (cod_contrato,cod_banco,cod_agencia,nr_conta) VALUES 
                                (new.cod_contrato,new.cod_banco,new.cod_agencia,new.nr_conta);
                            END IF;
                        END IF;
                        Return new;
                    END;
                    $temp$ LANGUAGE plpgsql;';
                    RAISE NOTICE 'stInsert: %', stInsert;
    EXECUTE stInsert;
    
    --CRIANDO TRIGGERS RESTANTES
    stInsert := 'CREATE TRIGGER tr_contrato_servidor_conta_salario_historico BEFORE INSERT OR UPDATE ON pessoal_'|| inCodEntidade ||'.contrato_servidor_conta_salario FOR EACH ROW EXECUTE PROCEDURE pessoal_'|| inCodEntidade ||'.fn_contrato_servidor_conta_salario_historico();';
    EXECUTE stInsert;

    stInsert := 'CREATE TRIGGER trg_situacao_contrato_servidor_'|| inCodEntidade ||' BEFORE INSERT OR DELETE ON pessoal_'|| inCodEntidade ||'.contrato_servidor FOR EACH ROW EXECUTE PROCEDURE tr_situacao_contrato_servidor();';
    EXECUTE stInsert;

    stInsert := 'CREATE TRIGGER trg_situacao_contrato_pensionista_'|| inCodEntidade ||' BEFORE INSERT OR DELETE ON pessoal_'|| inCodEntidade ||'.contrato_pensionista FOR EACH ROW EXECUTE PROCEDURE tr_situacao_contrato_pensionista();';
    EXECUTE stInsert;    
    
    stInsert := 'CREATE TRIGGER trg_situacao_contrato_aposentadoria_'|| inCodEntidade ||' BEFORE INSERT OR DELETE ON pessoal_'|| inCodEntidade ||'.aposentadoria FOR EACH ROW EXECUTE PROCEDURE tr_situacao_contrato_aposentadoria();';
    EXECUTE stInsert;
    
    stInsert := 'CREATE TRIGGER trg_situacao_contrato_aposentadoria_excluida_'|| inCodEntidade ||' BEFORE INSERT OR DELETE ON pessoal_'|| inCodEntidade ||'.aposentadoria_excluida FOR EACH ROW EXECUTE PROCEDURE tr_situacao_contrato_aposentadoria_excluida();';
    EXECUTE stInsert;
    
    stInsert := 'CREATE TRIGGER trg_situacao_contrato_servidor_caso_causa_'|| inCodEntidade ||' BEFORE INSERT OR DELETE ON pessoal_'|| inCodEntidade ||'.contrato_servidor_caso_causa FOR EACH ROW EXECUTE PROCEDURE tr_situacao_contrato_servidor_caso_causa();';
    EXECUTE stInsert;
    
    stInsert := 'CREATE TRIGGER tr_configuracao_parametros_gerais_'|| inCodEntidade ||' BEFORE INSERT OR UPDATE ON ponto_'|| inCodEntidade ||'.configuracao_parametros_gerais FOR EACH ROW EXECUTE PROCEDURE ponto.fn_atualiza_ultimo_timestamp_conf_ponto();';
    EXECUTE stInsert;

    stInsert := 'CREATE TRIGGER tr_configuracao_banco_horas_'|| inCodEntidade ||' BEFORE INSERT OR UPDATE ON ponto_'|| inCodEntidade ||'.configuracao_banco_horas FOR EACH ROW EXECUTE PROCEDURE ponto.fn_atualiza_ultimo_timestamp_conf_ponto();';
    EXECUTE stInsert;

    stInsert := 'CREATE TRIGGER tr_configuracao_horas_extras_'|| inCodEntidade ||' BEFORE INSERT OR UPDATE ON ponto_'|| inCodEntidade ||'.configuracao_horas_extras_2 FOR EACH ROW EXECUTE PROCEDURE ponto.fn_atualiza_ultimo_timestamp_conf_ponto();';
    EXECUTE stInsert;

    stInsert := 'CREATE TRIGGER tr_atualiza_ultimo_timestamp_escala_'|| inCodEntidade ||' BEFORE INSERT OR UPDATE ON ponto_'|| inCodEntidade ||'.escala_turno FOR EACH ROW EXECUTE PROCEDURE ponto.fn_atualiza_ultimo_timestamp_escala();';
    EXECUTE stInsert;

    RETURN TRUE;
END
$$ LANGUAGE plpgsql;
