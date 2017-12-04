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
* $Revision: 17524 $
* $Name$
* $Author: cako $
* $Date: 2006-11-09 13:34:45 -0200 (Qui, 09 Nov 2006) $
*
* Casos de uso: uc-02.02.22
                uc-02.08.07
*/

CREATE OR REPLACE FUNCTION fn_relatorio_pct(VARCHAR, VARCHAR, VARCHAR, VARCHAR) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stEntidades	        ALIAS FOR $2;
    stDtInicial         ALIAS FOR $3;
    stDtFinal           ALIAS FOR $4;
    stSql               VARCHAR   := '';
    stSqlComplemento    VARCHAR   := '';
    reRegistro          RECORD;
    arRetorno           NUMERIC[];
    inMes               INTEGER;
    inCodConta          INTEGER;

BEGIN

    inMes := substr(stDtInicial,4,2)::INTEGER;

    stSql := 'CREATE TEMPORARY TABLE tmp_debito AS
                SELECT *
                FROM (
                    SELECT
                         pc.cod_estrutural
                        ,pa.cod_plano
                        ,vl.tipo_valor
                        ,vl.vl_lancamento
                        ,vl.cod_entidade
                        ,lo.cod_lote
                        ,lo.dt_lote
                        ,lo.exercicio
                        ,lo.tipo
                        ,vl.sequencia
                        ,vl.oid as oid_temp
                        ,sc.cod_sistema
                        ,pc.escrituracao
                        ,pc.indicador_superavit
                    FROM
                         contabilidade.plano_conta            as pc
                        ,contabilidade.plano_analitica        as pa
                        ,contabilidade.conta_debito           as cd
                        ,contabilidade.valor_lancamento       as vl
                        ,contabilidade.lancamento             as la
                        ,contabilidade.lote                   as lo
                        ,contabilidade.sistema_contabil       as sc
                    WHERE   pc.cod_conta    = pa.cod_conta
                    AND     pc.exercicio    = pa.exercicio
                    AND     pa.cod_plano    = cd.cod_plano
                    AND     pa.exercicio    = cd.exercicio
                    AND     cd.cod_lote     = vl.cod_lote
                    AND     cd.tipo         = vl.tipo
                    AND     cd.sequencia    = vl.sequencia
                    AND     cd.exercicio    = vl.exercicio
                    AND     cd.tipo_valor   = vl.tipo_valor
                    AND     cd.cod_entidade = vl.cod_entidade
                    AND     vl.cod_lote     = la.cod_lote
                    AND     vl.tipo         = la.tipo
                    AND     vl.sequencia    = la.sequencia
                    AND     vl.exercicio    = la.exercicio
                    AND     vl.cod_entidade = la.cod_entidade
                    AND     vl.tipo_valor   = ''D''
                    AND     la.cod_lote     = lo.cod_lote
                    AND     la.exercicio    = lo.exercicio
                    AND     la.tipo         = lo.tipo
                    AND     la.cod_entidade = lo.cod_entidade
                    AND     pa.exercicio = ''' || stExercicio || '''
                    AND     sc.cod_sistema  = pc.cod_sistema
                    AND     sc.exercicio    = pc.exercicio
                    ORDER BY pc.cod_estrutural
                  ) as tabela';

        IF (stEntidades != '') THEN
            stSql := stSql || ' WHERE cod_entidade IN ('||stEntidades||')';
        END IF;

    EXECUTE stSql;

    stSql := 'CREATE TEMPORARY TABLE tmp_credito AS
                SELECT *
                FROM (
                    SELECT
                         pc.cod_estrutural
                        ,pa.cod_plano
                        ,vl.tipo_valor
                        ,vl.vl_lancamento
                        ,vl.cod_entidade
                        ,lo.cod_lote
                        ,lo.dt_lote
                        ,lo.exercicio
                        ,lo.tipo
                        ,vl.sequencia
                        ,vl.oid as oid_temp
                        ,sc.cod_sistema
                        ,pc.escrituracao
                        ,pc.indicador_superavit
                    FROM
                         contabilidade.plano_conta       as pc
                        ,contabilidade.plano_analitica   as pa
                        ,contabilidade.conta_credito     as cc
                        ,contabilidade.valor_lancamento  as vl
                        ,contabilidade.lancamento        as la
                        ,contabilidade.lote              as lo
                        ,contabilidade.sistema_contabil  as sc
                    WHERE   pc.cod_conta    = pa.cod_conta
                    AND     pc.exercicio    = pa.exercicio
                    AND     pa.cod_plano    = cc.cod_plano
                    AND     pa.exercicio    = cc.exercicio
                    AND     cc.cod_lote     = vl.cod_lote
                    AND     cc.tipo         = vl.tipo
                    AND     cc.sequencia    = vl.sequencia
                    AND     cc.exercicio    = vl.exercicio
                    AND     cc.tipo_valor   = vl.tipo_valor
                    AND     cc.cod_entidade = vl.cod_entidade
                    AND     vl.cod_lote     = la.cod_lote
                    AND     vl.tipo         = la.tipo
                    AND     vl.sequencia    = la.sequencia
                    AND     vl.exercicio    = la.exercicio
                    AND     vl.cod_entidade = la.cod_entidade
                    AND     vl.tipo_valor   = ''C''
                    AND     la.cod_lote     = lo.cod_lote
                    AND     la.exercicio    = lo.exercicio
                    AND     la.tipo         = lo.tipo
                    AND     la.cod_entidade = lo.cod_entidade
                    AND     pa.exercicio = ''' || stExercicio || '''
                    AND     sc.cod_sistema  = pc.cod_sistema
                    AND     sc.exercicio    = pc.exercicio

                    ORDER BY pc.cod_estrutural
                  ) as tabela';

                 IF (stEntidades != '') THEN
            stSql := stSql || ' WHERE cod_entidade IN ('||stEntidades||')';
        END IF;

    EXECUTE stSql;

    CREATE UNIQUE INDEX unq_debito              ON tmp_debito           (cod_estrutural varchar_pattern_ops, oid_temp);
    CREATE UNIQUE INDEX unq_credito             ON tmp_credito          (cod_estrutural varchar_pattern_ops, oid_temp);


    CREATE TEMPORARY TABLE tmp_totaliza_debito AS
        SELECT *
        FROM  tmp_debito
        WHERE dt_lote BETWEEN to_date( stDtInicial , 'dd/mm/yyyy' ) AND   to_date( stDtFinal , 'dd/mm/yyyy' )
        AND   tipo <> 'I';

    CREATE TEMPORARY TABLE tmp_totaliza_credito AS
        SELECT *
        FROM  tmp_credito
        WHERE dt_lote BETWEEN to_date( stDtInicial , 'dd/mm/yyyy' ) AND   to_date( stDtFinal , 'dd/mm/yyyy' )
        AND   tipo <> 'I';

    CREATE UNIQUE INDEX unq_totaliza_credito    ON tmp_totaliza_credito (cod_estrutural varchar_pattern_ops, oid_temp);
    CREATE UNIQUE INDEX unq_totaliza_debito     ON tmp_totaliza_debito  (cod_estrutural varchar_pattern_ops, oid_temp);

    IF substr(stDtInicial,1,5) = '01/01' THEN
        stSqlComplemento := ' dt_lote = to_date( ' || quote_literal(stDtInicial) || ',' || quote_literal('dd/mm/yyyy') || ') ';
        stSqlComplemento := stSqlComplemento || ' AND tipo = '||quote_literal('I')||' ';
    ELSE
        stSqlComplemento := 'dt_lote BETWEEN to_date( ''01/01/'' || substr(to_char(to_date(''' || stDtInicial || ''',''dd/mm/yyyy'') - 1,''dd/mm/yyyy''),7),''dd/mm/yyyy'') AND to_date( ' || quote_literal(stDtFinal) || ',' || quote_literal('dd/mm/yyyy') || ')-1';
    END IF;
    stSql := 'CREATE TEMPORARY TABLE tmp_totaliza AS
        SELECT * FROM tmp_debito
        WHERE
             ' || stSqlComplemento || '
       UNION
        SELECT * FROM tmp_credito
        WHERE
             ' || stSqlComplemento || '
    ';
    EXECUTE stSql;

    CREATE UNIQUE INDEX unq_totaliza            ON tmp_totaliza         (cod_estrutural varchar_pattern_ops, oid_temp);

        stSql := ' SELECT 12::INTEGER                                                                                    AS tipo_registro
                        , (SELECT cod_tipo FROM tcmgo.orgao WHERE exercicio = ''' ||stExercicio|| ''')::INTEGER          AS tipo_unidade_orcamentaria
                        , plano_conta.cod_conta
                        , CASE WHEN plano_analitica.cod_conta IS NOT NULL THEN
                                CASE WHEN plano_conta.indicador_superavit = ''financeiro'' THEN 1::INTEGER
                                    WHEN plano_conta.indicador_superavit = ''permanente'' THEN 2::INTEGER
                                    ELSE 0::INTEGER
                                END
                          ELSE 0
                          END                                                                                            AS indicador_superavit
                        , publico.fn_nivel(plano_conta.cod_estrutural)::INTEGER                                          AS nivel
                        , CASE WHEN plano_conta.natureza_saldo IS NOT NULL THEN UPPER(plano_conta.natureza_saldo)::CHAR
                               ELSE UPPER(plano_analitica.natureza_saldo)::CHAR
                          END                                                                                            AS natureza_conta
                        , CASE WHEN plano_conta.escrituracao = ''sintetica'' THEN ''S''::VARCHAR
                                  WHEN plano_conta.escrituracao = ''analitica'' THEN ''A''::VARCHAR
                                  ELSE ''''::VARCHAR
                          END                                                                                            AS tipo_conta
                        , sem_acentos(plano_conta.nom_conta)::VARCHAR                                                                 AS descricao
                        , CASE WHEN plano_analitica.cod_conta IS NOT NULL THEN
                                CASE WHEN plano_conta.indicador_superavit = ''financeiro'' THEN 1::INTEGER
                                    WHEN plano_conta.indicador_superavit = ''permanente'' THEN 2::INTEGER
                                    ELSE 0::INTEGER
                                END
                          ELSE 0
                          END                                                                                            AS indicador_superavit_pcasp
                        , REPLACE(plano_conta.cod_estrutural, ''.'', '''')::VARCHAR                                      AS cod_conta_casp
                        , CASE WHEN publico.fn_nivel(plano_conta.cod_estrutural) = 1 THEN ''0''::VARCHAR
                               ELSE REPLACE(fn_conta_mae(plano_conta.cod_estrutural), ''.'', '''')::VARCHAR
                          END                                                                                            AS cod_conta_pai
                        , REPLACE(plano_contas_tcmgo.estrutural, ''.'', '''')::VARCHAR                                   AS cod_conta_pcasp
                        , 0.00                                                                                           AS vl_saldo_anterior
                        , 0.00                                                                                           AS vl_saldo_debitos
                        , 0.00                                                                                           AS vl_saldo_creditos
                        , 0.00                                                                                           AS vl_saldo_atual
                        , plano_conta.cod_estrutural::VARCHAR                                                            AS cod_estrutural
                        , plano_conta.obrigatorio_tcmgo
                     FROM contabilidade.plano_conta

                LEFT JOIN contabilidade.plano_analitica
                       ON plano_analitica.cod_conta = plano_conta.cod_conta
                      AND plano_analitica.exercicio = plano_conta.exercicio

                     JOIN contabilidade.sistema_contabil
                       ON plano_conta.exercicio = sistema_contabil.exercicio
                      AND plano_conta.cod_sistema = sistema_contabil.cod_sistema

                LEFT JOIN tcmgo.vinculo_plano_contas_tcmgo
                       ON vinculo_plano_contas_tcmgo.cod_conta = plano_conta.cod_conta
                      AND vinculo_plano_contas_tcmgo.exercicio = plano_conta.exercicio

                LEFT JOIN tcmgo.plano_contas_tcmgo
                       ON plano_contas_tcmgo.cod_plano = vinculo_plano_contas_tcmgo.cod_plano_tcmgo
                      AND plano_contas_tcmgo.exercicio = vinculo_plano_contas_tcmgo.exercicio

                   WHERE plano_conta.exercicio = ''' || stExercicio || '''

                     AND NOT EXISTS (
                          SELECT 1
                            FROM tcmgo.arquivo_pct
                           WHERE arquivo_pct.cod_conta = plano_conta.cod_conta
                             AND arquivo_pct.exercicio = plano_conta.exercicio
                             AND arquivo_pct.mes < ' || inMes || ' )

                ORDER BY cod_estrutural ';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        arRetorno := fn_totaliza_pct( publico.fn_mascarareduzida(reRegistro.cod_estrutural) , stDtInicial, stDtFinal);
        reRegistro.vl_saldo_anterior := arRetorno[1];
        reRegistro.vl_saldo_debitos  := arRetorno[2];
        reRegistro.vl_saldo_creditos := arRetorno[3];
        reRegistro.vl_saldo_atual    := arRetorno[4];
        IF (( reRegistro.vl_saldo_anterior <> 0.00 ) OR
            ( reRegistro.vl_saldo_debitos  <> 0.00 ) OR
            ( reRegistro.vl_saldo_creditos <> 0.00 )) OR reRegistro.obrigatorio_tcmgo IS TRUE
        THEN
            SELECT cod_conta
              INTO inCodConta
              FROM tcmgo.arquivo_pct
             WHERE arquivo_pct.cod_conta = reRegistro.cod_conta
               AND arquivo_pct.exercicio = stExercicio;

            IF (inCodConta IS NULL) THEN
                INSERT INTO tcmgo.arquivo_pct (cod_conta, exercicio, mes) VALUES (reRegistro.cod_conta, stExercicio, inMes);
            END IF;

            RETURN NEXT reRegistro;
        END IF;
    END LOOP;

    DROP INDEX unq_totaliza;
    DROP INDEX unq_totaliza_debito;
    DROP INDEX unq_totaliza_credito;
    DROP INDEX unq_debito;
    DROP INDEX unq_credito;

    DROP TABLE tmp_totaliza;
    DROP TABLE tmp_debito;
    DROP TABLE tmp_credito;
    DROP TABLE tmp_totaliza_debito;
    DROP TABLE tmp_totaliza_credito;

    RETURN;
END;
$$ LANGUAGE 'plpgsql';
