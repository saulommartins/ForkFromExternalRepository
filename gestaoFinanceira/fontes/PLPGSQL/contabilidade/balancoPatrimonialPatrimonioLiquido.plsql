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
 $Id: balancoPatrimonialPatrimonioLiquido.plsql 64099 2015-12-02 17:14:05Z lisiane $
 *
 * Casos de uso: uc-02.02.11
 */

CREATE OR REPLACE FUNCTION contabilidade.balanco_patrimonial_patrimonio_liquido (varchar, varchar, varchar, varchar) RETURNS SETOF RECORD AS $$

DECLARE
    stExercicio         ALIAS FOR $1;
    stDtInicial         ALIAS FOR $2;
    stDtFinal           ALIAS FOR $3;
    stCodEntidades      ALIAS FOR $4;
    stSql               VARCHAR   := '';
    stSqlComplemento    VARCHAR   := '';
    reRegistro          RECORD;
    arRetorno           NUMERIC[];
    boUpdateRelatorio   BOOLEAN; 

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

    stSql := 'CREATE TEMPORARY TABLE tmp_relatorio AS
        SELECT CAST(cod_estrutural AS VARCHAR) as cod_estrutural
             , nivel
             , CAST(nom_conta AS VARCHAR) as nom_conta
             , SUM(valores[1] * multiplicador) AS vl_saldo_anterior
             , SUM(valores[2] * multiplicador * -1) as vl_saldo_debitos
             , SUM(valores[3] * multiplicador * -1) as vl_saldo_creditos
             , SUM(valores[4] * multiplicador) AS vl_saldo_atual
             , ''''::VARCHAR AS tipo_conta
          
          FROM (
            
            --CONTA PATRIMÔNIO LIQUIDO
                SELECT '||quote_literal('2.3.0.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.0.0.0.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.0.0.0.00.00')||') as nivel
                     , ''Patrimônio Liquido'' as nom_conta
                     , -1 as  multiplicador
            
            UNION ALL
            
                --CONTA PATRIMÔNIO SOCIAL E CAPITAL SOCIAL
                SELECT '||quote_literal('2.3.1.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.1.0.0.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.1.0.0.00.00')||') as nivel
                     , ''Patrimônio Social e Capital Social'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --DEDUZ DA CONTA PATRIMÔNIO SOCIAL E CAPITAL SOCIAL
                SELECT '||quote_literal('2.3.1.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.1.2.2.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.1.0.0.00.00')||') as nivel
                     , ''Patrimônio Social e Capital Social'' as nom_conta
                     , -1 as  multiplicador

            UNION ALL

                --CONTA ADIANTAMENTO PARA FUTURO AUMENTO DE CAPITAL
                SELECT '||quote_literal('2.3.2.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.2.0.0.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.2.0.0.00.00')||') as nivel
                     , ''Adiantamento para Futuro Aumento de Capital'' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --DEDUZ DA CONTA ADIANTAMENTO PARA FUTURO AUMENTO DE CAPITAL
                SELECT '||quote_literal('2.3.2.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.2.0.2.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.2.0.0.00.00')||') as nivel
                     , ''Adiantamento para Futuro Aumento de Capital'' as nom_conta
                     , -1 as  multiplicador

            UNION ALL

                --CONTA RESERVAS DE CAPITAL
                SELECT '||quote_literal('2.3.3.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.3.0.0.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.3.0.0.00.00')||') as nivel
                     , ''Adiantamento para Futuro Aumento de Capital'' as nom_conta
                     , 1 as  multiplicador
                     
            UNION ALL

                --DEDUZ DA CONTA RESERVAS DE CAPITAL
                SELECT '||quote_literal('2.3.3.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.3.1.2.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.3.0.0.00.00')||') as nivel
                     , ''Adiantamento para Futuro Aumento de Capital'' as nom_conta
                     , -1 as  multiplicador
                     
            UNION ALL

                --DEDUZ DA CONTA RESERVAS DE CAPITAL
                SELECT '||quote_literal('2.3.3.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.3.2.2.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.3.0.0.00.00')||') as nivel
                     , ''Adiantamento para Futuro Aumento de Capital'' as nom_conta
                     , -1 as  multiplicador
                     
            UNION ALL

                --DEDUZ DA CONTA RESERVAS DE CAPITAL
                SELECT '||quote_literal('2.3.3.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.3.3.2.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.3.0.0.00.00')||') as nivel
                     , ''Adiantamento para Futuro Aumento de Capital'' as nom_conta
                     , -1 as  multiplicador
                     
            UNION ALL

                --DEDUZ DA CONTA RESERVAS DE CAPITAL
                SELECT '||quote_literal('2.3.3.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.3.4.2.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.3.0.0.00.00')||') as nivel
                     , ''Adiantamento para Futuro Aumento de Capital'' as nom_conta
                     , -1 as  multiplicador
                     
            UNION ALL

                --DEDUZ DA CONTA RESERVAS DE CAPITAL
                SELECT '||quote_literal('2.3.3.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.3.9.2.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.3.0.0.00.00')||') as nivel
                     , ''Adiantamento para Futuro Aumento de Capital'' as nom_conta
                     , -1 as  multiplicador
                     
            UNION ALL

                --CONTA AJUSTES DE AVALIAÇÃO PATRIMONIAL
                SELECT '||quote_literal('2.3.4.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.4.0.0.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.4.0.0.00.00')||') as nivel
                     , ''Ajustes de Avaliação Patrimonial'' as nom_conta
                     , 1 as  multiplicador
                     
            UNION ALL

                --CONTA RESERVA DE LUCROS
                SELECT '||quote_literal('2.3.5.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.5.0.0.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.5.0.0.00.00')||') as nivel
                     , ''Reserva de Lucros'' as nom_conta
                     , 1 as  multiplicador
                     
            UNION ALL

                --DEDUZ DA CONTA RESERVA DE LUCROS
                SELECT '||quote_literal('2.3.5.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.5.1.2.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.5.0.0.00.00')||') as nivel
                     , ''Reserva de Lucros'' as nom_conta
                     , -1 as  multiplicador
                     
            UNION ALL

                --DEDUZ DA CONTA RESERVA DE LUCROS
                SELECT '||quote_literal('2.3.5.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.5.2.2.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.5.0.0.00.00')||') as nivel
                     , ''Reserva de Lucros'' as nom_conta
                     , -1 as  multiplicador
                     
            UNION ALL

                --DEDUZ DA CONTA RESERVA DE LUCROS
                SELECT '||quote_literal('2.3.5.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.5.3.2.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.5.0.0.00.00')||') as nivel
                     , ''Reserva de Lucros'' as nom_conta
                     , -1 as  multiplicador
                    
            UNION ALL

                --DEDUZ DA CONTA RESERVA DE LUCROS
                SELECT '||quote_literal('2.3.5.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.5.4.2.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.5.0.0.00.00')||') as nivel
                     , ''Reserva de Lucros'' as nom_conta
                     , -1 as  multiplicador
                     
            UNION ALL

                --DEDUZ DA CONTA RESERVA DE LUCROS
                SELECT '||quote_literal('2.3.5.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.5.5.2.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.5.0.0.00.00')||') as nivel
                     , ''Reserva de Lucros'' as nom_conta
                     , -1 as  multiplicador
                     
            UNION ALL

                --DEDUZ DA CONTA RESERVA DE LUCROS
                SELECT '||quote_literal('2.3.5.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.5.7.2.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.5.0.0.00.00')||') as nivel
                     , ''Reserva de Lucros'' as nom_conta
                     , -1 as  multiplicador
                     
            UNION ALL

                --DEDUZ DA CONTA RESERVA DE LUCROS
                SELECT '||quote_literal('2.3.5.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.5.9.2.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.5.0.0.00.00')||') as nivel
                     , ''Reserva de Lucros'' as nom_conta
                     , -1 as  multiplicador

            UNION ALL

                --CONTA DEMAIS RESERVAS
                SELECT '||quote_literal('2.3.6.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.6.0.0.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.6.0.0.00.00')||') as nivel
                     , ''Demais Reservas'' as nom_conta
                     , 1 as  multiplicador
                     
            UNION ALL

                --DEDUZ DA CONTA DEMAIS RESERVAS
                SELECT '||quote_literal('2.3.6.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.6.1.2.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.6.0.0.00.00')||') as nivel
                     , ''Demais Reservas'' as nom_conta
                     , -1 as  multiplicador
                     
            UNION ALL

                --DEDUZ DA CONTA DEMAIS RESERVAS
                SELECT '||quote_literal('2.3.6.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.6.9.2.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.6.0.0.00.00')||') as nivel
                     , ''Demais Reservas'' as nom_conta
                     , -1 as  multiplicador
                    
    ';

IF ( (stDtInicial = '01/01/'||stExercicio ) AND (stDtFinal = '31/12/'||stExercicio) ) THEN
--regra do relatorio para quando o relatorio for emitido para todo o exercicio
        stSql := stSql || '
                UNION ALL
                
                --CONTA RESULTADOS ACUMULADOS
                SELECT '||quote_literal('2.3.7.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.7.0.0.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.7.0.0.00.00')||') as nivel
                     , ''Resultados Acumulados'' as nom_conta
                     , -1 as  multiplicador

            UNION ALL

                --DEDUZ DA CONTA RESULTADOS ACUMULADOS
                SELECT '||quote_literal('2.3.7.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.7.1.2.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.7.0.0.00.00')||') as nivel
                     , ''Resultados Acumulados'' as nom_conta
                     , -1 as  multiplicador
                     
            UNION ALL

                --DEDUZ DA CONTA RESULTADOS ACUMULADOS
                SELECT '||quote_literal('2.3.7.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.7.2.2.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.7.0.0.00.00')||') as nivel
                     , ''Resultados Acumulados'' as nom_conta
                     , -1 as  multiplicador
                     
            UNION ALL

                --CONTA RESULTADO DO EXERCICIO
                SELECT '||quote_literal('2.3.7.0.0.01.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.7.1.1.01.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.7.0.0.01.00')||') as nivel
                     , ''Resultado do Exercício'' as nom_conta
                     , -1 as  multiplicador
                     
            UNION ALL

                --CONTA RESULTADO DO EXERCICIO
                SELECT '||quote_literal('2.3.7.0.0.01.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.7.1.3.01.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.7.0.0.01.00')||') as nivel
                     , ''Resultado do Exercício'' as nom_conta
                     , -1 as  multiplicador
                    
            UNION ALL

                --CONTA RESULTADO DO EXERCICIO
                SELECT '||quote_literal('2.3.7.0.0.01.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.7.1.4.01.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.7.0.0.01.00')||') as nivel
                     , ''Resultado do Exercício'' as nom_conta
                     , -1 as  multiplicador
                     
            UNION ALL

                --CONTA RESULTADO DO EXERCICIO
                SELECT '||quote_literal('2.3.7.0.0.01.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.7.1.5.01.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.7.0.0.01.00')||') as nivel
                     , ''Resultado do Exercício'' as nom_conta
                     , -1 as  multiplicador
                     
            UNION ALL

                --CONTA RESULTADO DO EXERCICIO
                SELECT '||quote_literal('2.3.7.0.0.01.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.7.2.1.01.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.7.0.0.01.00')||') as nivel
                     , ''Resultado do Exercício'' as nom_conta
                     , -1 as  multiplicador
                     
            UNION ALL

                --CONTA RESULTADO DO EXERCICIO
                SELECT '||quote_literal('2.3.7.0.0.01.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.7.2.3.01.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.7.0.0.01.00')||') as nivel
                     , ''Resultado do Exercício'' as nom_conta
                     , -1 as  multiplicador

            UNION ALL

                --CONTA RESULTADO DO EXERCICIO
                SELECT '||quote_literal('2.3.7.0.0.01.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.7.2.4.01.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.7.0.0.01.00')||') as nivel
                     , ''Resultado do Exercício'' as nom_conta
                     , -1 as  multiplicador
                    
            UNION ALL

                --CONTA RESULTADO DO EXERCICIO
                SELECT '||quote_literal('2.3.7.0.0.01.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.7.2.5.01.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.7.0.0.01.00')||') as nivel
                     , ''Resultado do Exercício'' as nom_conta
                     , -1 as  multiplicador
                     
            ';
ELSE
--regra do relatorio para quando o relatorio for emitido durante o exercicio
    boUpdateRelatorio := true;
    stSql := stSql ||'
            
            UNION ALL

                --CONTA RESULTADO DO EXERCICIO
                SELECT '||quote_literal('2.3.7.0.0.01.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('4.0.0.0.0.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.7.0.0.01.00')||') as nivel
                     , ''Resultado do Exercício'' as nom_conta
                     , -1 as  multiplicador
                     
            UNION ALL
            
                --CONTA RESULTADO DO EXERCICIO
                SELECT '||quote_literal('2.3.7.0.0.01.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('3.0.0.0.0.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.7.0.0.01.00')||') as nivel
                     , ''Resultado do Exercício'' as nom_conta
                     , -1 as  multiplicador
                     
            UNION ALL
            
                --CONTA RESULTADO DO EXERCICIO
                SELECT '||quote_literal('2.3.7.0.0.01.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.7.0.0.01.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.7.0.0.01.00')||') as nivel
                     , ''Resultado do Exercício'' as nom_conta
                     , -1 as  multiplicador
                     
            UNION ALL         
                
                --CONTA RESULTADO DO EXERCICIO
                SELECT '||quote_literal('2.3.7.0.0.01.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.7.1.1.01.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.7.0.0.01.00')||') as nivel
                     , ''Resultado do Exercício'' as nom_conta
                     , -1 as  multiplicador

            UNION ALL
                
                --CONTA RESULTADOS ACUMULADOS
                SELECT '||quote_literal('2.3.7.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.7.0.0.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.7.0.0.00.00')||') as nivel
                     , ''Resultados Acumulados'' as nom_conta
                     , 1 as  multiplicador
                    
    ';
END IF;

    stSql := stSql || '
            UNION ALL

                --CONTA RESULTADO DE EXERCÍCIOS ANTERIORES
                SELECT '||quote_literal('2.3.7.0.0.02.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.7.1.1.02.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.7.0.0.02.00')||') as nivel
                     , ''Resultado de Exercícios Anteriores'' as nom_conta
                     , -1 as  multiplicador
                     
            UNION ALL

                --CONTA RESULTADO DE EXERCÍCIOS ANTERIORES
                SELECT '||quote_literal('2.3.7.0.0.02.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.7.1.3.02.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.7.0.0.02.00')||') as nivel
                     , ''Resultado de Exercícios Anteriores'' as nom_conta
                     , -1 as  multiplicador
                     
            UNION ALL

                --CONTA RESULTADO DE EXERCÍCIOS ANTERIORES
                SELECT '||quote_literal('2.3.7.0.0.02.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.7.1.4.02.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.7.0.0.02.00')||') as nivel
                     , ''Resultado de Exercícios Anteriores'' as nom_conta
                     , -1 as  multiplicador
                     
            UNION ALL

                --CONTA RESULTADO DE EXERCÍCIOS ANTERIORES
                SELECT '||quote_literal('2.3.7.0.0.02.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.7.1.5.02.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.7.0.0.02.00')||') as nivel
                     , ''Resultado de Exercícios Anteriores'' as nom_conta
                     , -1 as  multiplicador
                     
            UNION ALL

                --CONTA RESULTADO DE EXERCÍCIOS ANTERIORES
                SELECT '||quote_literal('2.3.7.0.0.02.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.7.2.1.02.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.7.0.0.02.00')||') as nivel
                     , ''Resultado de Exercícios Anteriores'' as nom_conta
                     , -1 as  multiplicador
                    
            UNION ALL

                --CONTA RESULTADO DE EXERCÍCIOS ANTERIORES
                SELECT '||quote_literal('2.3.7.0.0.02.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.7.2.3.02.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.7.0.0.02.00')||') as nivel
                     , ''Resultado de Exercícios Anteriores'' as nom_conta
                     , -1 as  multiplicador
                     
            UNION ALL

                --CONTA RESULTADO DE EXERCÍCIOS ANTERIORES
                SELECT '||quote_literal('2.3.7.0.0.02.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.7.2.4.02.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.7.0.0.02.00')||') as nivel
                     , ''Resultado de Exercícios Anteriores'' as nom_conta
                     , -1 as  multiplicador

            UNION ALL

                --CONTA AJUSTES DE EXERCÍCIOS ANTERIORES
                SELECT '||quote_literal('2.3.7.0.0.03.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.7.1.1.03.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.7.0.0.03.00')||') as nivel
                     , ''Ajustes Exercícios Anteriores'' as nom_conta
                     , -1 as  multiplicador
                     
            UNION ALL

                --CONTA AJUSTES DE EXERCÍCIOS ANTERIORES
                SELECT '||quote_literal('2.3.7.0.0.03.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.7.1.3.03.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.7.0.0.03.00')||') as nivel
                     , ''Ajustes Exercícios Anteriores'' as nom_conta
                     , -1 as  multiplicador
                     
            UNION ALL

                --CONTA AJUSTES DE EXERCÍCIOS ANTERIORES
                SELECT '||quote_literal('2.3.7.0.0.03.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.7.1.4.03.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.7.0.0.03.00')||') as nivel
                     , ''Ajustes Exercícios Anteriores'' as nom_conta
                     , -1 as  multiplicador
                     
            UNION ALL

                --CONTA AJUSTES DE EXERCÍCIOS ANTERIORES
                SELECT '||quote_literal('2.3.7.0.0.03.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.7.1.5.03.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.7.0.0.03.00')||') as nivel
                     , ''Ajustes Exercícios Anteriores'' as nom_conta
                     , -1 as  multiplicador

            UNION ALL

                --CONTA AJUSTES DE EXERCÍCIOS ANTERIORES
                SELECT '||quote_literal('2.3.7.0.0.03.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.7.2.1.03.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.7.0.0.03.00')||') as nivel
                     , ''Ajustes Exercícios Anteriores'' as nom_conta
                     , -1 as  multiplicador

            UNION ALL

                --CONTA AJUSTES DE EXERCÍCIOS ANTERIORES
                SELECT '||quote_literal('2.3.7.0.0.03.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.7.2.3.03.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.7.0.0.03.00')||') as nivel
                     , ''Ajustes Exercícios Anteriores'' as nom_conta
                     , -1 as  multiplicador
                     
            UNION ALL

                --CONTA AJUSTES DE EXERCÍCIOS ANTERIORES
                SELECT '||quote_literal('2.3.7.0.0.03.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.7.2.4.03.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.7.0.0.03.00')||') as nivel
                     , ''Ajustes Exercícios Anteriores'' as nom_conta
                     , -1 as  multiplicador
                     
            UNION ALL

                --CONTA AJUSTES DE EXERCÍCIOS ANTERIORES
                SELECT '||quote_literal('2.3.7.0.0.03.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.7.2.5.03.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.7.0.0.03.00')||') as nivel
                     , ''Ajustes Exercícios Anteriores'' as nom_conta
                     , -1 as  multiplicador
                     
            UNION ALL

                --CONTA OUTROS RESULTADOS
                SELECT '||quote_literal('2.3.7.0.0.04.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.7.1.1.04.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.7.0.0.04.00')||') as nivel
                     , ''Outros Resultados'' as nom_conta
                     , -1 as  multiplicador
                     
            UNION ALL

                --CONTA OUTROS RESULTADOS
                SELECT '||quote_literal('2.3.7.0.0.04.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.7.1.3.04.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.7.0.0.04.00')||') as nivel
                     , ''Outros Resultados'' as nom_conta
                     , -1 as  multiplicador
                     
            UNION ALL

                --CONTA OUTROS RESULTADOS
                SELECT '||quote_literal('2.3.7.0.0.04.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.7.1.4.04.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.7.0.0.04.00')||') as nivel
                     , ''Outros Resultados'' as nom_conta
                     , -1 as  multiplicador
                     
            UNION ALL

                --CONTA OUTROS RESULTADOS
                SELECT '||quote_literal('2.3.7.0.0.04.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.7.1.5.04.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.7.0.0.04.00')||') as nivel
                     , ''Outros Resultados'' as nom_conta
                     , -1 as  multiplicador
                     
            UNION ALL

                --CONTA OUTROS RESULTADOS
                SELECT '||quote_literal('2.3.7.0.0.04.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.7.2.1.04.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.7.0.0.04.00')||') as nivel
                     , ''Outros Resultados'' as nom_conta
                     , -1 as  multiplicador
                     
            UNION ALL

                --CONTA OUTROS RESULTADOS
                SELECT '||quote_literal('2.3.7.0.0.04.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.7.2.3.04.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.7.0.0.04.00')||') as nivel
                     , ''Outros Resultados'' as nom_conta
                     , -1 as  multiplicador

            UNION ALL

                --CONTA OUTROS RESULTADOS
                SELECT '||quote_literal('2.3.7.0.0.04.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.7.2.4.04.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.7.0.0.04.00')||') as nivel
                     , ''Outros Resultados'' as nom_conta
                     , -1 as  multiplicador
                     
            UNION ALL

                --CONTA OUTROS RESULTADOS
                SELECT '||quote_literal('2.3.7.0.0.04.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.7.2.5.04.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.7.0.0.04.00')||') as nivel
                     , ''Outros Resultados'' as nom_conta
                     , -1 as  multiplicador
                     
            UNION ALL

                --CONTA OUTROS RESULTADOS
                SELECT '||quote_literal('2.3.7.0.0.04.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.7.2.1.05.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.7.0.0.04.00')||') as nivel
                     , ''Outros Resultados'' as nom_conta
                     , -1 as  multiplicador
                     
            UNION ALL

                --CONTA OUTROS RESULTADOS
                SELECT '||quote_literal('2.3.7.0.0.04.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.7.2.3.05.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.7.0.0.04.00')||') as nivel
                     , ''Outros Resultados'' as nom_conta
                     , -1 as  multiplicador
                     
            UNION ALL

                --CONTA OUTROS RESULTADOS
                SELECT '||quote_literal('2.3.7.0.0.04.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.7.2.4.05.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.7.0.0.04.00')||') as nivel
                     , ''Outros Resultados'' as nom_conta
                     , -1 as  multiplicador

            UNION ALL

                --CONTA OUTROS RESULTADOS
                SELECT '||quote_literal('2.3.7.0.0.04.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.7.2.5.05.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.7.0.0.04.00')||') as nivel
                     , ''Outros Resultados'' as nom_conta
                     , -1 as  multiplicador
                     
            UNION ALL

                --CONTA OUTROS RESULTADOS
                SELECT '||quote_literal('2.3.7.0.0.04.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.7.2.1.06.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.7.0.0.04.00')||') as nivel
                     , ''Outros Resultados'' as nom_conta
                     , -1 as  multiplicador
                     
            UNION ALL

                --CONTA OUTROS RESULTADOS
                SELECT '||quote_literal('2.3.7.0.0.04.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.7.2.3.06.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.7.0.0.04.00')||') as nivel
                     , ''Outros Resultados'' as nom_conta
                     , -1 as  multiplicador
                     
            UNION ALL

                --CONTA OUTROS RESULTADOS
                SELECT '||quote_literal('2.3.7.0.0.04.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.7.2.4.06.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.7.0.0.04.00')||') as nivel
                     , ''Outros Resultados'' as nom_conta
                     , -1 as  multiplicador
                     
            UNION ALL

                --CONTA OUTROS RESULTADOS
                SELECT '||quote_literal('2.3.7.0.0.04.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.7.2.5.06.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.7.0.0.04.00')||') as nivel
                     , ''Outros Resultados'' as nom_conta
                     , -1 as  multiplicador
            
            UNION ALL

                --CONTA AÇÕES/COTAS EM TESOURARIA
                SELECT '||quote_literal('2.3.9.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.9.0.0.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.9.0.0.00.00')||') as nivel
                     , ''Ações/Cotas em Tesouraria'' as nom_conta
                     , 1 as  multiplicador
                     
            UNION ALL

                --DEDUZ DA CONTA AÇÕES/COTAS EM TESOURARIA
                SELECT '||quote_literal('2.3.9.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.9.1.2.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.9.0.0.00.00')||') as nivel
                     , ''Ações/Cotas em Tesouraria'' as nom_conta
                     , -1 as  multiplicador

            UNION ALL

                --DEDUZ DA CONTA AÇÕES/COTAS EM TESOURARIA
                SELECT '||quote_literal('2.3.9.0.0.00.00')||' as cod_estrutural
                     , contabilidade.totaliza_balanco_patrimonial( publico.fn_mascarareduzida('||quote_literal('2.3.9.2.2.00.00')||') ) as valores
                     , publico.fn_nivel('||quote_literal('2.3.9.0.0.00.00')||') as nivel
                     , ''Ações/Cotas em Tesouraria'' as nom_conta
                     , -1 as  multiplicador

            --ADICIONADO LINHAS PARA ALINHAR O RELATÓRIO

            UNION ALL

                --LINHA PARA COMPENSAR TAMANHO DE TABELAS
                SELECT '||quote_literal('2.9')||' as cod_estrutural
                     , ARRAY[0.00,0.00,0.00,0.00] as valores
                     , publico.fn_nivel('||quote_literal('2.9')||') as nivel
                     , '''' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --LINHA PARA COMPENSAR TAMANHO DE TABELAS
                SELECT '||quote_literal('2.99')||' as cod_estrutural
                     , ARRAY[0.00,0.00,0.00,0.00] as valores
                     , publico.fn_nivel('||quote_literal('2.99')||') as nivel
                     , '''' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --LINHA PARA COMPENSAR TAMANHO DE TABELAS
                SELECT '||quote_literal('2.999')||' as cod_estrutural
                     , ARRAY[0.00,0.00,0.00,0.00] as valores
                     , publico.fn_nivel('||quote_literal('2.999')||') as nivel
                     , '''' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --LINHA PARA COMPENSAR TAMANHO DE TABELAS
                SELECT '||quote_literal('2.9999')||' as cod_estrutural
                     , ARRAY[0.00,0.00,0.00,0.00] as valores
                     , publico.fn_nivel('||quote_literal('2.9999')||') as nivel
                     , '''' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --LINHA PARA COMPENSAR TAMANHO DE TABELAS
                SELECT '||quote_literal('2.99999')||' as cod_estrutural
                     , ARRAY[0.00,0.00,0.00,0.00] as valores
                     , publico.fn_nivel('||quote_literal('2.99999')||') as nivel
                     , '''' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --LINHA PARA COMPENSAR TAMANHO DE TABELAS
                SELECT '||quote_literal('2.999999')||' as cod_estrutural
                     , ARRAY[0.00,0.00,0.00,0.00] as valores
                     , publico.fn_nivel('||quote_literal('2.999999')||') as nivel
                     , '''' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --LINHA PARA COMPENSAR TAMANHO DE TABELAS
                SELECT '||quote_literal('2.9999999')||' as cod_estrutural
                     , ARRAY[0.00,0.00,0.00,0.00] as valores
                     , publico.fn_nivel('||quote_literal('2.9999999')||') as nivel
                     , '''' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --LINHA PARA COMPENSAR TAMANHO DE TABELAS
                SELECT '||quote_literal('2.99999999')||' as cod_estrutural
                     , ARRAY[0.00,0.00,0.00,0.00] as valores
                     , publico.fn_nivel('||quote_literal('2.99999999')||') as nivel
                     , '''' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --LINHA PARA COMPENSAR TAMANHO DE TABELAS
                SELECT '||quote_literal('2.999999999')||' as cod_estrutural
                     , ARRAY[0.00,0.00,0.00,0.00] as valores
                     , publico.fn_nivel('||quote_literal('2.999999999')||') as nivel
                     , '''' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --LINHA PARA COMPENSAR TAMANHO DE TABELAS
                SELECT '||quote_literal('2.9999999999')||' as cod_estrutural
                     , ARRAY[0.00,0.00,0.00,0.00] as valores
                     , publico.fn_nivel('||quote_literal('2.9999999999')||') as nivel
                     , '''' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --LINHA PARA COMPENSAR TAMANHO DE TABELAS
                SELECT '||quote_literal('2.99999999999')||' as cod_estrutural
                     , ARRAY[0.00,0.00,0.00,0.00] as valores
                     , publico.fn_nivel('||quote_literal('2.99999999999')||') as nivel
                     , '''' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --LINHA PARA COMPENSAR TAMANHO DE TABELAS
                SELECT '||quote_literal('2.999999999999')||' as cod_estrutural
                     , ARRAY[0.00,0.00,0.00,0.00] as valores
                     , publico.fn_nivel('||quote_literal('2.999999999999')||') as nivel
                     , '''' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --LINHA PARA COMPENSAR TAMANHO DE TABELAS
                SELECT '||quote_literal('2.9999999999999')||' as cod_estrutural
                     , ARRAY[0.00,0.00,0.00,0.00] as valores
                     , publico.fn_nivel('||quote_literal('2.9999999999999')||') as nivel
                     , '''' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --LINHA PARA COMPENSAR TAMANHO DE TABELAS
                SELECT '||quote_literal('2.99999999999999')||' as cod_estrutural
                     , ARRAY[0.00,0.00,0.00,0.00] as valores
                     , publico.fn_nivel('||quote_literal('2.99999999999999')||') as nivel
                     , '''' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --LINHA PARA COMPENSAR TAMANHO DE TABELAS
                SELECT '||quote_literal('2.999999999999999')||' as cod_estrutural
                     , ARRAY[0.00,0.00,0.00,0.00] as valores
                     , publico.fn_nivel('||quote_literal('2.999999999999999')||') as nivel
                     , '''' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --LINHA PARA COMPENSAR TAMANHO DE TABELAS
                SELECT '||quote_literal('2.9999999999999999')||' as cod_estrutural
                     , ARRAY[0.00,0.00,0.00,0.00] as valores
                     , publico.fn_nivel('||quote_literal('2.9999999999999999')||') as nivel
                     , '''' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --LINHA PARA COMPENSAR TAMANHO DE TABELAS
                SELECT '||quote_literal('2.99999999999999999')||' as cod_estrutural
                     , ARRAY[0.00,0.00,0.00,0.00] as valores
                     , publico.fn_nivel('||quote_literal('2.99999999999999999')||') as nivel
                     , '''' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --LINHA PARA COMPENSAR TAMANHO DE TABELAS
                SELECT '||quote_literal('2.999999999999999999')||' as cod_estrutural
                     , ARRAY[0.00,0.00,0.00,0.00] as valores
                     , publico.fn_nivel('||quote_literal('2.999999999999999999')||') as nivel
                     , '''' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --LINHA PARA COMPENSAR TAMANHO DE TABELAS
                SELECT '||quote_literal('2.9999999999999999999')||' as cod_estrutural
                     , ARRAY[0.00,0.00,0.00,0.00] as valores
                     , publico.fn_nivel('||quote_literal('2.9999999999999999999')||') as nivel
                     , '''' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --LINHA PARA COMPENSAR TAMANHO DE TABELAS
                SELECT '||quote_literal('2.99999999999999999999')||' as cod_estrutural
                     , ARRAY[0.00,0.00,0.00,0.00] as valores
                     , publico.fn_nivel('||quote_literal('2.99999999999999999999')||') as nivel
                     , '''' as nom_conta
                     , 1 as  multiplicador

            UNION ALL

                --LINHA PARA COMPENSAR TAMANHO DE TABELAS
                SELECT '||quote_literal('2.999999999999999999999')||' as cod_estrutural
                     , ARRAY[0.00,0.00,0.00,0.00] as valores
                     , publico.fn_nivel('||quote_literal('2.999999999999999999999')||') as nivel
                     , '''' as nom_conta
                     , 1 as  multiplicador
            
             UNION ALL

                --LINHA PARA COMPENSAR TAMANHO DE TABELAS
                SELECT '||quote_literal('2.9999999999999999999999')||' as cod_estrutural
                     , ARRAY[0.00,0.00,0.00,0.00] as valores
                     , publico.fn_nivel('||quote_literal('2.9999999999999999999999')||') as nivel
                     , '''' as nom_conta
                     , 1 as  multiplicador
                     
             UNION ALL

                --LINHA PARA COMPENSAR TAMANHO DE TABELAS
                SELECT '||quote_literal('2.99999999999999999999999')||' as cod_estrutural
                     , ARRAY[0.00,0.00,0.00,0.00] as valores
                     , publico.fn_nivel('||quote_literal('2.99999999999999999999999')||') as nivel
                     , '''' as nom_conta
                     , 1 as  multiplicador
                                  
               ) as tabela
      GROUP BY cod_estrutural
             , nivel
             , nom_conta
    ';

EXECUTE stSql;

IF (boUpdateRelatorio = true) THEN

        UPDATE tmp_relatorio
        SET vl_saldo_atual   = (SELECT SUM(vl_saldo_atual) FROM tmp_relatorio WHERE nivel = 6)
        WHERE cod_estrutural = '2.3.7.0.0.00.00';
        
        UPDATE tmp_relatorio
        SET vl_saldo_anterior   = (SELECT SUM(vl_saldo_anterior) FROM tmp_relatorio WHERE nivel = 6)
        WHERE cod_estrutural = '2.3.7.0.0.00.00';
        
        UPDATE tmp_relatorio
        SET vl_saldo_atual     =( (SELECT vl_saldo_atual FROM tmp_relatorio WHERE cod_estrutural = '2.3.1.0.0.00.00')
                                +
                                  (SELECT vl_saldo_atual FROM tmp_relatorio WHERE cod_estrutural = '2.3.7.0.0.00.00')
                                )
            ,vl_saldo_anterior =( (SELECT vl_saldo_anterior FROM tmp_relatorio WHERE cod_estrutural = '2.3.1.0.0.00.00')
                                +
                                  (SELECT vl_saldo_anterior FROM tmp_relatorio WHERE cod_estrutural = '2.3.7.0.0.00.00')
                                )
        WHERE cod_estrutural = '2.3.0.0.0.00.00';
        
END IF;

stSql := 'SELECT * FROM tmp_relatorio';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;

    DROP INDEX unq_totaliza;
    DROP INDEX unq_totaliza_debito;
    DROP INDEX unq_totaliza_credito;
    DROP INDEX unq_debito;
    DROP INDEX unq_credito;
    
    DROP TABLE tmp_relatorio;
    DROP TABLE tmp_totaliza;
    DROP TABLE tmp_debito;
    DROP TABLE tmp_credito;
    DROP TABLE tmp_totaliza_debito;
    DROP TABLE tmp_totaliza_credito;

    RETURN;
END;
$$ LANGUAGE 'plpgsql';