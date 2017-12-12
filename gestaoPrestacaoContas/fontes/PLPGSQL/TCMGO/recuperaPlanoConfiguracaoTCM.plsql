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
    * PL que retorna as contas disponíveis para configuração do plano do TCM
    * Data de Criação   : 12/05/2014

    * @author Eduardo Paculski Schitz 

    * @ignore

    * $Id: $
*/

CREATE OR REPLACE FUNCTION tcmgo.recupera_plano_configuracao_tcm(VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stEntidades		    ALIAS FOR $2;
    stDtInicial         ALIAS FOR $3;
    stDtFinal           ALIAS FOR $4;
    stGrupo             ALIAS FOR $5;
    stSql               VARCHAR   := '';
    stSqlComplemento    VARCHAR   := '';
    reRegistro          RECORD;
    arRetorno           NUMERIC[];

BEGIN

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
--        WHERE to_char(dt_lote,'mm') = mes
        AND   tipo <> 'I';

    CREATE TEMPORARY TABLE tmp_totaliza_credito AS
        SELECT *
        FROM  tmp_credito
        WHERE dt_lote BETWEEN to_date( stDtInicial , 'dd/mm/yyyy' ) AND   to_date( stDtFinal , 'dd/mm/yyyy' )
--        WHERE to_char(dt_lote,'mm') = mes
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

        stSql := ' SELECT plano_conta.cod_conta 
                        , plano_analitica.cod_plano
                        , plano_conta.cod_estrutural
                        , plano_conta.exercicio
                        , vinculo_plano_contas_tcmgo.cod_plano_tcmgo 
                        , plano_conta.nom_conta
                        , plano_conta.obrigatorio_tcmgo
                        , 0.00                                                                                      AS vl_saldo_anterior
                        , 0.00                                                                                      AS vl_saldo_debitos
                        , 0.00                                                                                      AS vl_saldo_creditos
                        , 0.00                                                                                      AS vl_saldo_atual
                     FROM contabilidade.plano_conta

                     JOIN contabilidade.sistema_contabil
                       ON plano_conta.exercicio = sistema_contabil.exercicio
                      AND plano_conta.cod_sistema = sistema_contabil.cod_sistema

                LEFT JOIN contabilidade.plano_analitica
                       ON plano_analitica.cod_conta = plano_conta.cod_conta
                      AND plano_analitica.exercicio = plano_conta.exercicio

                LEFT JOIN tcmgo.vinculo_plano_contas_tcmgo
                       ON vinculo_plano_contas_tcmgo.cod_conta = plano_conta.cod_conta
                      AND vinculo_plano_contas_tcmgo.exercicio = plano_conta.exercicio

                LEFT JOIN tcmgo.plano_contas_tcmgo
                       ON plano_contas_tcmgo.cod_plano = vinculo_plano_contas_tcmgo.cod_plano_tcmgo
                      AND plano_contas_tcmgo.exercicio = vinculo_plano_contas_tcmgo.exercicio

                    WHERE plano_conta.exercicio = ''' || stExercicio || ''' ';

    IF stGrupo <> '' THEN
        stSql := stSql || ' AND plano_conta.cod_estrutural LIKE ''' || stGrupo || '%'' ';
    END IF;

    stSql := stSql || ' ORDER BY plano_conta.cod_estrutural ';

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
