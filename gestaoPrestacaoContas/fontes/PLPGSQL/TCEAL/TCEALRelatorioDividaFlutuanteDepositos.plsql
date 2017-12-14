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
* $Id: TCEALRelatorioDividaFlutuanteDepositos.plsql 64655 2016-03-18 16:50:13Z michel $
*/

CREATE OR REPLACE FUNCTION tceal.relatorio_divida_flutuante_depositos(VARCHAR, VARCHAR, VARCHAR, VARCHAR, CHAR) RETURNS SETOF RECORD AS $$ 
DECLARE
    stExercicio         ALIAS FOR $1;
    stFiltro            ALIAS FOR $2;
    stDtInicial         ALIAS FOR $3;
    stDtFinal           ALIAS FOR $4;
    chEstilo            ALIAS FOR $5;
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
                        ,pc.nom_conta
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
                    AND     pa.exercicio    = ' || quote_literal(stExercicio) || '
                    AND     sc.cod_sistema  = pc.cod_sistema
                    AND     sc.exercicio    = pc.exercicio
                    ORDER BY pc.cod_estrutural
                  ) as tabela
                 WHERE dt_lote
                       BETWEEN to_date( ''01/01/''||substr(to_char(to_date(''' || stDtInicial || ''',''dd/mm/yyyy''),''dd/mm/yyyy'') ,7) ,''dd/mm/yyyy'')
                           AND to_date( ' || quote_literal(stDtFinal) || ',''dd/mm/yyyy'')
                ' || stFiltro ;
    EXECUTE stSql;

    stSql := 'CREATE TEMPORARY TABLE tmp_credito AS
                SELECT *
                FROM (
                    SELECT
                         pc.cod_estrutural
                        ,pa.cod_plano
                        ,pc.nom_conta
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
                    AND     pa.exercicio    = ' || quote_literal(stExercicio) || '
                    AND     sc.cod_sistema  = pc.cod_sistema
                    AND     sc.exercicio    = pc.exercicio

                    ORDER BY pc.cod_estrutural
                  ) as tabela
                 WHERE dt_lote
                       BETWEEN to_date( ''01/01/''||substr(to_char(to_date(''' || stDtInicial || ''',''dd/mm/yyyy''),''dd/mm/yyyy'') ,7) ,''dd/mm/yyyy'')
                           AND to_date( ' || quote_literal(stDtFinal) || ',''dd/mm/yyyy'')
                ' || stFiltro ;
    EXECUTE stSql;

    CREATE UNIQUE INDEX unq_debito              ON tmp_debito           (cod_estrutural varchar_pattern_ops, oid_temp);
    CREATE UNIQUE INDEX unq_credito             ON tmp_credito          (cod_estrutural varchar_pattern_ops, oid_temp);

    CREATE TEMPORARY TABLE tmp_totaliza_debito AS
        SELECT *
          FROM tmp_debito
         WHERE dt_lote BETWEEN to_date( stDtInicial::varchar , 'dd/mm/yyyy' ) AND   to_date( stDtFinal::varchar , 'dd/mm/yyyy' )
           AND tipo <> 'I';

    CREATE TEMPORARY TABLE tmp_totaliza_credito AS
        SELECT *
          FROM tmp_credito
         WHERE dt_lote BETWEEN to_date( stDtInicial::varchar , 'dd/mm/yyyy' ) AND   to_date( stDtFinal::varchar , 'dd/mm/yyyy' )
           AND tipo <> 'I';

    CREATE UNIQUE INDEX unq_totaliza_credito    ON tmp_totaliza_credito (cod_estrutural varchar_pattern_ops, oid_temp);
    CREATE UNIQUE INDEX unq_totaliza_debito     ON tmp_totaliza_debito  (cod_estrutural varchar_pattern_ops, oid_temp);

    stSql := 'CREATE TEMPORARY TABLE tmp_totaliza AS
        SELECT *
                FROM tmp_debito
        UNION
        SELECT *
                FROM tmp_credito
    ';
    EXECUTE stSql;

    CREATE UNIQUE INDEX unq_totaliza            ON tmp_totaliza         (cod_estrutural varchar_pattern_ops, oid_temp);

        stSql := '
                    SELECT totaliza_planos.cod_estrutural
                         , totaliza_planos.cod_plano
                         , totaliza_planos.nom_conta
                         , totaliza_planos.exercicio
                         , sw_cgm.nom_cgm
                         , totaliza_planos.cod_entidade
                         , SUM(totaliza_planos.vl_saldo_anterior)*(-1) AS vl_saldo_anterior
                         , ABS(SUM(totaliza_planos.vl_saldo_debitos)) AS vl_saldo_debitos
                         , ABS(SUM(totaliza_planos.vl_saldo_creditos)) AS vl_saldo_creditos
                         , ((SUM(totaliza_planos.vl_saldo_anterior) + SUM(ABS(totaliza_planos.vl_saldo_debitos))) - SUM(ABS(totaliza_planos.vl_saldo_creditos)))*(-1) AS vl_saldo_atual
                      FROM (
                             SELECT cod_estrutural
                                  , cod_plano
                                  , nom_conta
                                  , exercicio
                                  , cod_entidade
                                  , CASE WHEN tipo = ''I''
                                         THEN SUM(vl_lancamento)
                                         ELSE 0.00
                                    END AS vl_saldo_anterior
                                  , CASE WHEN tipo = ''T'' AND tipo_valor = ''D''
                                         THEN SUM(vl_lancamento)
                                         ELSE 0.00
                                    END AS vl_saldo_debitos
                                  , CASE WHEN tipo = ''T'' AND tipo_valor = ''C''
                                         THEN SUM(vl_lancamento)
                                         ELSE 0.00
                                    END AS vl_saldo_creditos
                               FROM tmp_totaliza
                              WHERE exercicio = '||quote_literal(stExercicio)||'
                                ' || stFiltro || '
                                AND publico.fn_nivel(cod_estrutural) >= 5
                                AND cod_estrutural ILIKE ''2.1.8.8%''
                           GROUP BY cod_estrutural
                                  , cod_plano
                                  , nom_conta
                                  , exercicio
                                  , cod_entidade
                                  , tipo
                                  , tipo_valor
                           ) AS totaliza_planos
                      JOIN orcamento.entidade
                        ON entidade.cod_entidade = totaliza_planos.cod_entidade
                       AND entidade.exercicio   = totaliza_planos.exercicio
                      JOIN sw_cgm
                        ON sw_cgm.numcgm = entidade.numcgm
                  GROUP BY totaliza_planos.cod_estrutural
                         , totaliza_planos.cod_plano
                         , totaliza_planos.nom_conta
                         , totaliza_planos.exercicio
                         , totaliza_planos.cod_entidade
                         , sw_cgm.nom_cgm
                  ORDER BY totaliza_planos.cod_entidade
                         , totaliza_planos.cod_estrutural
                         , totaliza_planos.cod_plano ';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        IF  ( reRegistro.vl_saldo_anterior = 0.00 ) AND
            ( reRegistro.vl_saldo_debitos  = 0.00 ) AND
            ( reRegistro.vl_saldo_creditos = 0.00 ) AND
            ( reRegistro.vl_saldo_atual    = 0.00 )
        THEN

        ELSE
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
