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
 * Casos de uso: uc-02.02.11
 */

/*

*/

CREATE OR REPLACE FUNCTION contabilidade.balanco_patrimonial_compensacao_passivo (varchar, varchar, varchar, varchar) RETURNS SETOF RECORD AS $$

DECLARE
    stExercicio         ALIAS FOR $1;
    stDtInicial         ALIAS FOR $2;
    stDtFinal           ALIAS FOR $3;
    stCodEntidades      ALIAS FOR $4;
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
                    FROM
                         contabilidade.plano_conta       as pc
                        ,contabilidade.plano_analitica   as pa
                        ,contabilidade.conta_debito      as cd
                        ,contabilidade.valor_lancamento  as vl
                        ,contabilidade.lancamento        as la
                        ,contabilidade.lote              as lo
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
                    AND     lo.cod_entidade IN (' || stCodEntidades || ')
                    AND     pa.exercicio    = ' || quote_literal(stExercicio) || '
                    ORDER BY pc.cod_estrutural
                  ) as tabela
                ';
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
                    FROM
                         contabilidade.plano_conta       as pc
                        ,contabilidade.plano_analitica   as pa
                        ,contabilidade.conta_credito     as cc
                        ,contabilidade.valor_lancamento  as vl
                        ,contabilidade.lancamento        as la
                        ,contabilidade.lote              as lo
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
                    AND     lo.cod_entidade IN (' || stCodEntidades || ')
                    AND     pa.exercicio = ' || quote_literal(stExercicio) || '
                    ORDER BY pc.cod_estrutural
                  ) as tabela
                ';
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

    CREATE UNIQUE INDEX unq_totaliza_credito ON tmp_totaliza_credito (cod_estrutural varchar_pattern_ops, oid_temp);
    CREATE UNIQUE INDEX unq_totaliza_debito  ON tmp_totaliza_debito  (cod_estrutural varchar_pattern_ops, oid_temp);

    IF substr(stDtInicial,1,5) = '01/01' THEN
        stSqlComplemento := ' dt_lote = to_date( ' || quote_literal(stDtInicial) || ',' || quote_literal('dd/mm/yyyy') || ') ';
        stSqlComplemento := stSqlComplemento || ' AND tipo = '||quote_literal('I')||' ';
    ELSE
        stSqlComplemento := ' dt_lote <= to_date( ' || quote_literal(stDtInicial) || ',' || quote_literal('dd/mm/yyyy') || ')-1 ';
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

    stSql := '
        SELECT CAST(cod_estrutural AS VARCHAR) as cod_estrutural
             , nivel
             , CAST(nom_conta AS VARCHAR) as nom_conta
             , SUM(valores[1] * multiplicador) as vl_saldo_anterior
             , SUM(valores[2] * multiplicador) as vl_saldo_debitos
             , SUM(valores[3] * multiplicador) as vl_saldo_creditos
             , SUM(valores[4] * multiplicador) as vl_saldo_atual
          FROM (
                --CONTA SALDOS DOS ATOS POTENCIAIS PASSIVOS
                SELECT ''8.1.2'' as cod_estrutural
                     , ARRAY[0.00,0.00,0.00,0.00] as valores
                     , 1 as nivel
                     , ''Saldo dos Atos Potenciais Passivos'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA EXECUÇÃO DOS ATOS POTENCIAIS PASSIVOS
                SELECT '||quote_literal('8.1.2.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('8.1.2.0.0.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('8.1.2.0.0.00.00')||') as nivel
                     , ''Execução Dos Atos Potenciais Passivos'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA EXECUÇÃO DE GARANTIAS E CONTRAGARANTIAS CONCEDIDAS A EXECUTAR
                SELECT '||quote_literal('8.1.2.1.1.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('8.1.2.1.1.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('8.1.2.0.0.00.00')||') as nivel
                     , ''Execução De Garantias e Contragarantias Concedidas a executar'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA EXECUÇÃO DE OBRIGAÇÕES CONVENIADAS E OUTROS INSTRUMENTOS CONGÊNERES A LIBERAR
                SELECT '||quote_literal('8.1.2.2.1.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('8.1.2.2.1.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('8.1.2.0.0.00.00')||') as nivel
                     , ''Execução De Obrigações Conveniadas E Outros Instrumentos Congêneres a liberar'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA EXECUÇÃO DE OBRIGAÇÕES CONTRATUAIS EM EXECUÇÃO
                SELECT '||quote_literal('8.1.2.3.1.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('8.1.2.3.1.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('8.1.2.0.0.00.00')||') as nivel
                     , ''Execução De Obrigações Contratuais em execução'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --CONTA EXECUÇÃO DE OUTROS ATOS POTENCIAIS PASSIVOS EM EXECUÇÃO
                SELECT '||quote_literal('8.1.2.9.1.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('8.1.2.9.1.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('8.1.2.0.0.00.00')||') as nivel
                     , ''Execução De Outros Atos Potenciais Passivos em execução'' as nom_conta
                     , 1 as  multiplicador
             ) as tabela
      GROUP BY cod_estrutural
             , nivel
             , nom_conta
    ';


    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
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
