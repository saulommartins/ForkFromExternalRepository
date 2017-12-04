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

    * Comentário sobre a finalidade do arquivo. 
    * Data de Criação: 13/03/2008


    * @author Alexandre Melo

    * Casos de uso: uc-02.03.09 

    $Id: empenhoRestosPagarPagamentoEstornoCredor.plsql 59612 2014-09-02 12:00:51Z gelson $

*/

/*
CREATE TYPE relatorio_restos_pagar_dotacao_credor AS (
    entidade            INTEGER
    , empenho           INTEGER
    , dt_empenho        VARCHAR
    , dt_liquidacao     VARCHAR
    , exercicio         CHAR(4)
    , credor            VARCHAR
    , cod_estrutural    VARCHAR
    , cod_recurso       INTEGER
    , cod_recurso_banco INTEGER
    , dotacao           VARCHAR
    , cod_nota          INTEGER
    , dt_pagamento      VARCHAR
    , conta             INTEGER
    , banco             VARCHAR
    , valor_pago        NUMERIC
);

*/
CREATE OR REPLACE FUNCTION tcemg.relatorio_restos_pagar_dotacao_credor(varchar,varchar,varchar,varchar,varchar,varchar, varchar, varchar, varchar, varchar,varchar,varchar,varchar,varchar,varchar,VARCHAR,VARCHAR)
RETURNS SETOF relatorio_restos_pagar_dotacao_credor AS $$
DECLARE
    stExercicio                 ALIAS FOR $1;
    stFiltro                    ALIAS FOR $2;
    stDtInicial                 ALIAS FOR $3;
    stDtFinal                   ALIAS FOR $4;
    stCodEntidades              ALIAS FOR $5;
    stCodOrgao                  ALIAS FOR $6;
    stCodUnidade                ALIAS FOR $7;
    stCodRecurso                ALIAS FOR $8;
    stDestinacaoRecurso         ALIAS FOR $9;
    stCodDetalhamento           ALIAS FOR $10;
    stCodElementoDispensa       ALIAS FOR $11;
    stCodElementoDispensaMasc   ALIAS FOR $12;
    stSituacao                  ALIAS FOR $13;
    stCodCredor                 ALIAS FOR $14;
    stBoTCEMS                   ALIAS FOR $15;
    stCodFuncao                 ALIAS FOR $16;
    stCodSubFuncao              ALIAS FOR $17;

    stSql               VARCHAR   := '';
    stSqlExercicio      VARCHAR   := '';
    stExercicioAtual    VARCHAR   := '';
    reRegistro          RECORD;
    reReg               RECORD;

BEGIN

    CREATE TEMPORARY TABLE tmp_empenhos (
        entidade            INTEGER
        , empenho           INTEGER
        , dt_empenho        VARCHAR
        , dt_liquidacao     VARCHAR
        , exercicio         CHAR(4)
        , credor            VARCHAR
        , cod_estrutural    VARCHAR
        , cod_recurso       INTEGER
        , cod_recurso_banco INTEGER
        , dotacao           VARCHAR
        , cod_nota          INTEGER
        , data_pagamento    VARCHAR
        , conta             INTEGER
        , banco             VARCHAR
        , valor             NUMERIC                              
    );

    if (stSituacao = '1') then
        stSql := 'CREATE TEMPORARY TABLE tmp_pago_rp AS (
            SELECT
                p.cod_entidade as cod_entidade,
                p.cod_nota as cod_nota,
                p.exercicio_liquidacao as exercicio_liquidacao,
                p.timestamp as timestamp,
                pa.cod_plano as cod_plano,
                pc.nom_conta as nom_conta,
                plano_recurso.cod_recurso as cod_recurso_banco
            
            FROM 
                contabilidade.pagamento p
                
                --Ligação PAGAMENTO : LANCAMENTO EMPENHO
                JOIN contabilidade.lancamento_empenho le
                    ON p.cod_entidade      IN (' || stCodEntidades || ')
                    AND p.cod_lote = le.cod_lote
                    AND p.tipo = le.tipo
                    AND p.sequencia = le.sequencia
                    AND p.exercicio = le.exercicio
                    AND p.cod_entidade = le.cod_entidade
                    AND le.estorno = false
                
                --Ligação LANCAMENTO EMPENHO : CONTA_CREDITO
                JOIN contabilidade.conta_credito cc
                    ON le.cod_lote = cc.cod_lote
                    AND le.tipo = cc.tipo
                    AND le.exercicio = cc.exercicio
                    AND le.cod_entidade = cc.cod_entidade

                --Ligação CONTA_CREDITO : PLANO ANALITICA
                JOIN contabilidade.plano_analitica pa
                    ON cc.cod_plano = pa.cod_plano
                    AND cc.exercicio = pa.exercicio
                ';
                IF (stBoTCEMS = 'true') THEN
                    stSql := stSql || 'AND cc.sequencia = 2 ';
                ELSE
                    stSql := stSql || 'AND cc.sequencia = 3 ';
                END IF;

        stSql := stSql ||'
               
               --Ligação PLANO ANALITICA : PLANO CONTA
                JOIN contabilidade.plano_conta pc
                    ON pa.cod_conta = pc.cod_conta
                    AND pa.exercicio = pc.exercicio
            
                JOIN contabilidade.plano_recurso
                    ON plano_recurso.exercicio = pa.exercicio
                    AND plano_recurso.cod_plano = pa.cod_plano
               
        )';
        EXECUTE stSql;
    end if;

    if (stSituacao = '2') then
        stSql := 'CREATE TEMPORARY TABLE tmp_estornado_rp AS (
            SELECT
                p.cod_entidade as cod_entidade,
                p.cod_nota as cod_nota,
                p.exercicio_liquidacao as exercicio_liquidacao,
                p.timestamp as timestamp,
                pa.cod_plano as cod_plano,
                pc.nom_conta as nom_conta,
                plano_recurso.cod_recurso as cod_recurso_banco
            
            FROM 
                contabilidade.pagamento p
                
                --Ligação PAGAMENTO : LANCAMENTO EMPENHO
                JOIN contabilidade.lancamento_empenho le
                    ON p.cod_entidade      IN (' || stCodEntidades || ')
                    AND p.cod_lote = le.cod_lote
                    AND p.tipo = le.tipo
                    AND p.sequencia = le.sequencia
                    AND p.exercicio = le.exercicio
                    AND p.cod_entidade = le.cod_entidade
                    AND le.estorno = false
                
                --Ligação LANCAMENTO EMPENHO : CONTA_DEBITO
                JOIN contabilidade.conta_debito cd
                    ON le.cod_lote = cd.cod_lote
                    AND le.tipo = cd.tipo
                    AND le.exercicio = cd.exercicio
                    AND le.cod_entidade = cd.cod_entidade


                --Ligação CONTA_CREDITO : PLANO ANALITICA
                JOIN contabilidade.plano_analitica pa
                    ON cd.cod_plano = pa.cod_plano
                    AND cd.exercicio = pa.exercicio
                ';
                IF (stBoTCEMS = 'true') THEN
                    stSql := stSql || 'AND cd.sequencia = 2 ';
                ELSE
                    stSql := stSql || 'AND cd.sequencia = 3 ';
                END IF;

        stSql := stSql ||'
               
               --Ligação PLANO ANALITICA : PLANO CONTA
                JOIN contabilidade.plano_conta pc
                    ON pa.cod_conta = pc.cod_conta
                    AND pa.exercicio = pc.exercicio
            
                JOIN contabilidade.plano_recurso
                    ON plano_recurso.exercicio = pa.exercicio
                    AND plano_recurso.cod_plano = pa.cod_plano

        )';
        EXECUTE stSql;
    end if;

    stExercicioAtual := to_char(to_date(stDtInicial, 'dd/mm/yyyy'), 'yyyy');

    IF (LENGTH(stExercicio) <> 4) THEN
        stSqlExercicio := 'SELECT DISTINCT exercicio FROM empenho.empenho WHERE empenho.exercicio <> ' || quote_literal(stExercicioAtual) || ' ';
    ELSE
        stSqlExercicio := 'SELECT DISTINCT exercicio FROM empenho.empenho WHERE empenho.exercicio = ' || quote_literal(stExercicio) || ' ';
    END IF;

    FOR reReg IN EXECUTE stSqlExercicio
    LOOP

    stSql := ' INSERT INTO tmp_empenhos
                SELECT entidade
                     , empenho
                     , dt_empenho
                     , dt_liquidacao
                     , exercicio
                     , credor
                     , cod_estrutural
                     , cod_recurso
                     , cod_recurso_banco
                     , dotacao
                     , cod_nota
                     , data as data_pagamento
                     , conta
                     , nome_conta as banco
                     , valor 
                  FROM ( SELECT e.cod_entidade as entidade
                                , e.cod_empenho as empenho
                                , e.exercicio as exercicio
                                , sw_cgm.nom_cgm as credor
                                , CASE WHEN (ped_d_cd.cod_estrutural is not null) THEN 
                                            ped_d_cd.cod_estrutural
                                        ELSE 
                                            rpe.cod_estrutural 
                                    END as cod_estrutural
                                , CASE WHEN (ped_d_cd.cod_recurso is not null) THEN 
                                            ped_d_cd.cod_recurso
                                        ELSE 
                                            rpe.recurso 
                                    END as cod_recurso
                                , tmp.cod_recurso_banco
                                , to_char(e.dt_empenho, ''dd/mm/yyyy'') AS dt_empenho
                                , to_char(nl.dt_liquidacao, ''dd/mm/yyyy'') AS dt_liquidacao

                                ';

            if (stSituacao = '1') then
                    stSql := stSql || ', to_char(nlp.timestamp,''dd/mm/yyyy'') as data 
                                       , nlp.cod_nota as cod_nota
                                       , nlp.vl_pago as valor
                                       , tmp.cod_plano as conta
                                       , tmp.nom_conta as nome_conta
                                       , CASE WHEN rpe.recurso IS NULL THEN
                                                LPAD(ped_d_cd.num_orgao::VARCHAR, 2, ''0'')||''.''||LPAD(ped_d_cd.num_unidade::VARCHAR, 2, ''0'')||''.''||ped_d_cd.cod_funcao||''.''||ped_d_cd.cod_subfuncao||''.''||ped_d_cd.cod_programa||''.''||LPAD(ped_d_cd.num_pao::VARCHAR, 4, ''0'')||''.''||REPLACE(ped_d_cd.cod_estrutural, ''.'', '''') 
                                            ELSE
                                                LPAD(rpe.num_orgao::VARCHAR, 2, ''0'')||''.''||LPAD(rpe.num_unidade::VARCHAR, 2, ''0'')||''.''||rpe.cod_funcao||''.''||rpe.cod_subfuncao||''.''||rpe.cod_programa||''.''||LPAD(rpe.num_pao::VARCHAR, 4, ''0'')||''.''||REPLACE(rpe.cod_estrutural, ''.'', '''') 
                                        END as dotacao
                    ';                                                

            end if; 
            
            if (stSituacao = '2') then
                    stSql := stSql || ',to_char(nlpa.timestamp_anulada,''dd/mm/yyyy'') as data
                                        , nlpa.cod_nota as cod_nota
                                        , nlpa.vl_anulado as valor
                                        , tmp.cod_plano as conta
                                        , tmp.nom_conta as nome_conta 
                                        , CASE WHEN rpe.recurso IS NULL THEN
                                                LPAD(ped_d_cd.num_orgao::VARCHAR, 2, ''0'')||''.''||LPAD(ped_d_cd.num_unidade::VARCHAR, 2, ''0'')||''.''||ped_d_cd.cod_funcao||''.''||ped_d_cd.cod_subfuncao||''.''||ped_d_cd.cod_programa||''.''||LPAD(ped_d_cd.num_pao::VARCHAR, 4, ''0'')||''.''||REPLACE(ped_d_cd.cod_estrutural, ''.'', '''') 
                                            ELSE
                                                LPAD(rpe.num_orgao::VARCHAR, 2, ''0'')||''.''||LPAD(rpe.num_unidade::VARCHAR, 2, ''0'')||''.''||rpe.cod_funcao||''.''||rpe.cod_subfuncao||''.''||rpe.cod_programa||''.''||LPAD(rpe.num_pao::VARCHAR, 4, ''0'')||''.''||REPLACE(rpe.cod_estrutural, ''.'', '''') 
                                        END as dotacao
                    ';
            end if;

            stSql := stSql || '
            FROM
                empenho.empenho     as e ';

                if (stSituacao = '1') then
                    stSql := stSql || '
                    , empenho.nota_liquidacao nl
                    , empenho.nota_liquidacao_paga nlp

                    , tmp_pago_rp tmp
                    ';
                end if;
                
                if (stSituacao = '2') then
                    stSql := stSql || '
                    , empenho.nota_liquidacao nl                     
                    , empenho.nota_liquidacao_paga nlp
                    , empenho.nota_liquidacao_paga_anulada nlpa
                    , tmp_estornado_rp tmp
                    ';
                end if;

             stSql := stSql || '
              , sw_cgm
              , empenho.pre_empenho as pe
                LEFT OUTER JOIN empenho.restos_pre_empenho as rpe 
                    ON pe.exercicio = rpe.exercicio 
                    AND pe.cod_pre_empenho = rpe.cod_pre_empenho
                LEFT OUTER JOIN (
                   SELECT ped.exercicio
                        , ped.cod_pre_empenho
                        , d.num_pao
                        , d.num_orgao
                        , d.num_unidade
                        , d.cod_recurso
                        , r.masc_recurso_red
                        , r.cod_detalhamento
                        , cd.cod_estrutural
                        , d.cod_funcao
                        , d.cod_subfuncao
                        , d.cod_programa
                    FROM empenho.pre_empenho_despesa as ped
                        , orcamento.despesa as d
                    JOIN orcamento.recurso(' || quote_literal(reReg.exercicio) || ') as r
                        ON d.cod_recurso = r.cod_recurso AND d.exercicio = r.exercicio
                        , orcamento.conta_despesa as cd
                    WHERE ped.cod_despesa = d.cod_despesa
                        AND ped.exercicio = d.exercicio
                        AND ped.cod_conta = cd.cod_conta
                        AND d.exercicio   = cd.exercicio
                ) as ped_d_cd 
                     ON pe.exercicio = ped_d_cd.exercicio 
                    AND pe.cod_pre_empenho = ped_d_cd.cod_pre_empenho

                WHERE   e.exercicio       = ' || quote_literal(reReg.exercicio) || '
                    AND e.exercicio         = pe.exercicio
                    AND e.cod_pre_empenho   = pe.cod_pre_empenho
                    AND e.cod_entidade      IN (' || stCodEntidades || ') 
                    AND pe.cgm_beneficiario = sw_cgm.numcgm ';                                        

                if (stSituacao = '1') then
                    stSql := stSql || '

                       --Ligação EMPENHO : NOTA LIQUIDAÇÃO
                        AND e.exercicio = nl.exercicio_empenho
                        AND e.cod_entidade = nl.cod_entidade
                        AND e.cod_empenho = nl.cod_empenho

                        --Ligação NOTA LIQUIDAÇÃO : NOTA LIQUIDAÇÃO PAGA
                        AND nl.exercicio = nlp.exercicio
                        AND nl.cod_nota = nlp.cod_nota
                        AND nl.cod_entidade = nlp.cod_entidade
                        AND to_date(to_char(nlp.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') BETWEEN to_date(' || quote_literal(stDtInicial) || ',''dd/mm/yyyy'') AND to_date(' || quote_literal(stDtFinal) || ',''dd/mm/yyyy'')

                        --Ligação NOTA LIQUIDAÇÃO PAGA : PAGAMENTO
                        AND nlp.cod_entidade = tmp.cod_entidade
                        AND nlp.cod_nota = tmp.cod_nota
                        AND nlp.exercicio = tmp.exercicio_liquidacao
                        AND nlp.timestamp = tmp.timestamp
                    ';
                end if;

                if (stSituacao = '2') then
                    stSql := stSql || '
                        --Ligação EMPENHO : NOTA LIQUIDAÇÃO
                        AND e.cod_empenho = nl.cod_empenho
                        AND e.exercicio = nl.exercicio_empenho
                        AND e.cod_entidade = nl.cod_entidade

                        --Ligação NOTA LIQUIDAÇÃO : NOTA LIQUIDAÇÃO ITEM
                        AND nl.exercicio = nlp.exercicio
                        AND nl.cod_nota = nlp.cod_nota
                        AND nl.cod_entidade = nlp.cod_entidade

                        --Ligação NOTA LIQUIDAÇÃO ITEM : NOTA LIQUIDAÇÃO ITEM ANULADO
                        AND nlp.exercicio = nlpa.exercicio
                        AND nlp.cod_nota = nlpa.cod_nota
                        AND nlp.cod_entidade = nlpa.cod_entidade
                        AND nlp.timestamp = nlpa.timestamp
                        AND to_date(to_char(nlpa.timestamp_anulada,''dd/mm/yyyy''),''dd/mm/yyyy'') BETWEEN to_date(' || quote_literal(stDtInicial) || ',''dd/mm/yyyy'') AND to_date(' || quote_literal(stDtFinal) || ',''dd/mm/yyyy'')

                        --Ligação NOTA LIQUIDAÇÃO PAGA : PAGAMENTO
                        AND nlp.exercicio = tmp.exercicio_liquidacao
                        AND nlp.cod_entidade = tmp.cod_entidade
                        AND nlp.cod_nota = tmp.cod_nota
                        AND nlp.timestamp = tmp.timestamp
                        
                    ';
                end if;

                if (stCodOrgao is not null and stCodOrgao<>'') then
                    stSql := stSql || ' AND CASE WHEN pe.implantado = true THEN rpe.num_orgao = '|| stCodOrgao ||' ELSE ped_d_cd.num_orgao = '|| stCodOrgao ||' END ';
                end if;

                if (stCodUnidade is not null and stCodUnidade<>'') then
                    stSql := stSql || ' AND CASE WHEN pe.implantado = true THEN rpe.num_unidade = '|| stCodUnidade ||' ELSE ped_d_cd.num_unidade = '|| stCodUnidade ||' END ';
                end if;

                if (stCodRecurso is not null and stCodRecurso<>'') then
                    stSql := stSql || ' AND CASE WHEN pe.implantado = true THEN rpe.recurso = '|| stCodRecurso ||' ELSE ped_d_cd.cod_recurso = '|| stCodRecurso ||' END ';
                end if;

                if (stDestinacaoRecurso is not null and stDestinacaoRecurso<>'') then
                    stSql := stSql || ' AND ped_d_cd.masc_recurso_red = '|| quote_literal(stDestinacaoRecurso) ||'  ';
                end if;

                if (stCodDetalhamento is not null and stCodDetalhamento <> '' ) then
                    stSql := stSql || ' AND ped_d_cd.cod_detalhamento = ' || stCodDetalhamento; 
                end if;
    
                if (stCodElementoDispensa is not null and stCodElementoDispensa<>'') then
                    stSql := stSql || ' AND CASE WHEN pe.implantado = true THEN rpe.cod_estrutural like rtrim(' || quote_literal(stCodElementoDispensa) || ',''0'') || ''%'' ELSE ped_d_cd.cod_estrutural like ''' || stCodElementoDispensaMasc || '%'' END ';
                end if;

                if (stCodCredor is not null and stCodCredor<>'') then
                    stSql := stSql || ' AND sw_cgm.numcgm = '|| stCodCredor;
                end if;
                
                if (stCodFuncao is not null and stCodFuncao<>'') then
                    stSql := stSql || '
                             AND CASE WHEN pe.implantado = true
                               THEN rpe.cod_funcao      = '|| stCodFuncao ||'
                               ELSE ped_d_cd.cod_funcao = '|| stCodFuncao ||'
                             END
                    ';
                end if;
                
                if (stCodSubFuncao is not null and stCodSubFuncao<>'') then
                    stSql := stSql || '
                             AND CASE WHEN pe.implantado = true
                               THEN rpe.cod_subfuncao      IN ('|| stCodSubFuncao ||')
                               ELSE ped_d_cd.cod_subfuncao IN ('|| stCodSubFuncao ||')
                             END
                    ';
                end if;
                
            stSql := stSql || ' ORDER BY ';

            if (stSituacao = '1') then
                    stSql := stSql || 'to_date(to_char(nlp.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy''),';
            end if;

            if (stSituacao = '2') then
                    stSql := stSql || 'to_date(to_char(nlpa.timestamp_anulada,''dd/mm/yyyy''),''dd/mm/yyyy''),';
            end if;

            stSql := stSql || 'e.cod_entidade , e.cod_empenho , e.exercicio ';

            if (stSituacao = '1') then
                    stSql := stSql || ',nlp.cod_nota, tmp.cod_plano, tmp.nom_conta';
            end if;

            if (stSituacao = '2') then
                    stSql := stSql || ' ,nlpa.cod_nota, tmp.cod_plano, tmp.nom_conta';
            end if;

            stSql := stSql || ') as tbl where valor <> ''0.00'' ';
            stSql := stSql || ' ORDER BY to_date(data,''dd/mm/yyyy''), entidade, empenho, exercicio, cod_nota, conta, nome_conta';
        EXECUTE stSql;

    END LOOP;

    stSql :=  ' SELECT  entidade            
                        , empenho           
                        , dt_empenho        
                        , dt_liquidacao     
                        , exercicio         
                        , credor            
                        , cod_estrutural    
                        , cod_recurso       
                        , cod_recurso_banco 
                        , dotacao           
                        , cod_nota          
                        , data_pagamento    
                        , conta             
                        , banco             
                        , valor
                FROM tmp_empenhos             
            ';
            
    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;

    if (stSituacao = '1') then
        DROP TABLE tmp_pago_rp;
    end if;

    if (stSituacao = '2') then
        DROP TABLE tmp_estornado_rp;
    end if;

    DROP TABLE tmp_empenhos;

    RETURN ;
END;
$$
LANGUAGE 'plpgsql';

