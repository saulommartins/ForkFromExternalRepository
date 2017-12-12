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
* $Id: FTCEMGBalanceteContabil10.plsql 62872 2015-07-01 20:16:55Z franver $
* $Revision: 62872 $
* $Author: franver $
* $Date: 2015-07-01 17:16:55 -0300 (Wed, 01 Jul 2015) $
*
*/
/*
CREATE TYPE balancete_contabil_registro_10
    AS ( tipo_registro          INTEGER
       , conta_contabil         VARCHAR
       , saldo_inicial          NUMERIC
       , natureza_saldo_inicial CHAR(1)
       , total_debitos          NUMERIC
       , total_creditos         NUMERIC
       , saldo_final            NUMERIC
       , natureza_saldo_final   CHAR(1)
    );
*/

CREATE OR REPLACE FUNCTION tcemg.fn_balancete_contabil_10(VARCHAR, VARCHAR, VARCHAR, VARCHAR) RETURNS SETOF balancete_contabil_registro_10 AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stFiltro            ALIAS FOR $2;
    stDtInicial         ALIAS FOR $3;
    stDtFinal           ALIAS FOR $4;
    stSql               VARCHAR   := '';
    stSqlComplemento    VARCHAR   := '';
    reRegistro          RECORD;
    arRetorno           NUMERIC[];
BEGIN
    stSql := '
   CREATE TEMPORARY TABLE tmp_debito AS
             SELECT *
               FROM (SELECT pc.cod_estrutural
                          , pa.cod_plano
                          , vl.tipo_valor
                          , vl.vl_lancamento
                          , vl.cod_entidade
                          , lo.cod_lote
                          , lo.dt_lote
                          , lo.exercicio
                          , lo.tipo
                          , vl.sequencia
                          , vl.oid as oid_temp
                          , sc.cod_sistema
                          , pc.escrituracao
                          , pc.indicador_superavit
                       FROM contabilidade.plano_conta      AS pc
                          , contabilidade.plano_analitica  AS pa
                          , contabilidade.conta_debito     AS cd
                          , contabilidade.valor_lancamento AS vl
                          , contabilidade.lancamento       AS la
                          , contabilidade.lote             AS lo
                          , contabilidade.sistema_contabil AS sc
                      WHERE pc.cod_conta    = pa.cod_conta
                        AND pc.exercicio    = pa.exercicio
                        AND pa.cod_plano    = cd.cod_plano
                        AND pa.exercicio    = cd.exercicio
                        AND cd.cod_lote     = vl.cod_lote
                        AND cd.tipo         = vl.tipo
                        AND cd.sequencia    = vl.sequencia
                        AND cd.exercicio    = vl.exercicio
                        AND cd.tipo_valor   = vl.tipo_valor
                        AND cd.cod_entidade = vl.cod_entidade
                        AND vl.cod_lote     = la.cod_lote
                        AND vl.tipo         = la.tipo
                        AND vl.sequencia    = la.sequencia
                        AND vl.exercicio    = la.exercicio
                        AND vl.cod_entidade = la.cod_entidade
                        AND vl.tipo_valor   = ''D''
                        AND la.cod_lote     = lo.cod_lote
                        AND la.exercicio    = lo.exercicio
                        AND la.tipo         = lo.tipo
                        AND la.cod_entidade = lo.cod_entidade
                        AND pa.exercicio    = '''||stExercicio||'''
                        AND sc.cod_sistema  = pc.cod_sistema
                        AND sc.exercicio    = pc.exercicio
                   ORDER BY pc.cod_estrutural
                  ) as tabela
                 WHERE
                ' || stFiltro ;
    EXECUTE stSql;

    stSql := '
   CREATE TEMPORARY TABLE tmp_credito AS
             SELECT *
               FROM (SELECT pc.cod_estrutural
                          , pa.cod_plano
                          , vl.tipo_valor
                          , vl.vl_lancamento
                          , vl.cod_entidade
                          , lo.cod_lote
                          , lo.dt_lote
                          , lo.exercicio
                          , lo.tipo
                          , vl.sequencia
                          , vl.oid as oid_temp
                          , sc.cod_sistema
                          , pc.escrituracao
                          , pc.indicador_superavit
                       FROM contabilidade.plano_conta       as pc
                          , contabilidade.plano_analitica   as pa
                          , contabilidade.conta_credito     as cc
                          , contabilidade.valor_lancamento  as vl
                          , contabilidade.lancamento        as la
                          , contabilidade.lote              as lo
                          , contabilidade.sistema_contabil  as sc
                      WHERE pc.cod_conta    = pa.cod_conta
                        AND pc.exercicio    = pa.exercicio
                        AND pa.cod_plano    = cc.cod_plano
                        AND pa.exercicio    = cc.exercicio
                        AND cc.cod_lote     = vl.cod_lote
                        AND cc.tipo         = vl.tipo
                        AND cc.sequencia    = vl.sequencia
                        AND cc.exercicio    = vl.exercicio
                        AND cc.tipo_valor   = vl.tipo_valor
                        AND cc.cod_entidade = vl.cod_entidade
                        AND vl.cod_lote     = la.cod_lote
                        AND vl.tipo         = la.tipo
                        AND vl.sequencia    = la.sequencia
                        AND vl.exercicio    = la.exercicio
                        AND vl.cod_entidade = la.cod_entidade
                        AND vl.tipo_valor   = ''C''
                        AND la.cod_lote     = lo.cod_lote
                        AND la.exercicio    = lo.exercicio
                        AND la.tipo         = lo.tipo
                        AND la.cod_entidade = lo.cod_entidade
                        AND pa.exercicio    = '''||stExercicio||'''
                        AND sc.cod_sistema  = pc.cod_sistema
                        AND sc.exercicio    = pc.exercicio

                   ORDER BY pc.cod_estrutural
                    ) as tabela
              WHERE '||stFiltro ;
    EXECUTE stSql;

    CREATE UNIQUE INDEX unq_debito  ON tmp_debito  (cod_estrutural varchar_pattern_ops, oid_temp);
    CREATE UNIQUE INDEX unq_credito ON tmp_credito (cod_estrutural varchar_pattern_ops, oid_temp);

    CREATE TEMPORARY TABLE tmp_totaliza_debito AS
      SELECT *
        FROM tmp_debito
       WHERE dt_lote BETWEEN TO_DATE(stDtInicial::VARCHAR, 'dd/mm/yyyy')
                         AND TO_DATE(stDtFinal::VARCHAR  , 'dd/mm/yyyy')
         AND tipo <> 'I';

    CREATE TEMPORARY TABLE tmp_totaliza_credito AS
      SELECT *
        FROM tmp_credito
       WHERE dt_lote BETWEEN TO_DATE(stDtInicial::VARCHAR , 'dd/mm/yyyy' )
                         AND TO_DATE(stDtFinal::VARCHAR   , 'dd/mm/yyyy' )
         AND tipo <> 'I';

    CREATE UNIQUE INDEX unq_totaliza_credito ON tmp_totaliza_credito (cod_estrutural varchar_pattern_ops, oid_temp);
    CREATE UNIQUE INDEX unq_totaliza_debito  ON tmp_totaliza_debito  (cod_estrutural varchar_pattern_ops, oid_temp);

    IF substr(stDtInicial,1,5) =  '01/01' THEN
        stSqlComplemento := ' dt_lote = TO_DATE('''||stDtInicial||''',''dd/mm/yyyy'') ';
        stSqlComplemento := stSqlComplemento || ' AND tipo = ''I'' ';
    ELSE
        stSqlComplemento := 'dt_lote BETWEEN TO_DATE(''01/01/''||SUBSTR(TO_CHAR(TO_DATE('''||stDtInicial||''',''dd/mm/yyyy'') - 1,''dd/mm/yyyy'') ,7) ,''dd/mm/yyyy'')
                                         AND TO_DATE('''||stDtInicial||''',''dd/mm/yyyy'')-1 ';
    END IF;
    stSql := '
   CREATE TEMPORARY TABLE tmp_totaliza AS
             SELECT *
               FROM tmp_debito
              WHERE '||stSqlComplemento||'
              UNION
             SELECT *
               FROM tmp_credito
              WHERE '||stSqlComplemento||'
    ';
    EXECUTE stSql;

    CREATE UNIQUE INDEX unq_totaliza ON tmp_totaliza (cod_estrutural varchar_pattern_ops, oid_temp);

    stSql := '
           SELECT 10::INTEGER AS tipo_registro
                , pc.cod_estrutural
                , 0.00 as vl_saldo_inicial
                , '' ''::CHAR(1) AS natureza_saldo_incial
                , 0.00 as vl_saldo_debitos
                , 0.00 as vl_saldo_creditos
                , 0.00 as vl_saldo_final
                , '' ''::CHAR(1) AS natureza_saldo_final
             FROM contabilidade.plano_conta as pc
            WHERE pc.exercicio = '''||stExercicio||'''
              AND pc.escrituracao_pcasp = ''S''
         ORDER BY cod_estrutural ASC';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        arRetorno := contabilidade.fn_totaliza_balancete_verificacao( publico.fn_mascarareduzida(reRegistro.cod_estrutural) , stDtInicial, stDtFinal);
        reRegistro.vl_saldo_inicial  := arRetorno[1];
        reRegistro.vl_saldo_debitos  := arRetorno[2];
        reRegistro.vl_saldo_creditos := arRetorno[3];
        reRegistro.vl_saldo_final    := arRetorno[4];

        IF arRetorno[1] > 0.00 THEN
            reRegistro.natureza_saldo_incial := 'D';
        ELSIF arRetorno[1] < 0.00 THEN
            reRegistro.natureza_saldo_incial := 'C';
        ELSE
            reRegistro.natureza_saldo_incial := reRegistro.natureza_saldo_incial;
        END IF;

        IF arRetorno[4] > 0.00 THEN
            reRegistro.natureza_saldo_final := 'D';
        ELSIF arRetorno[4] < 0.00 THEN
            reRegistro.natureza_saldo_final := 'C';
        ELSE
            reRegistro.natureza_saldo_final := reRegistro.natureza_saldo_final;
        END IF;
        
        IF  ( reRegistro.vl_saldo_inicial  = 0.00 ) AND
            ( reRegistro.vl_saldo_debitos  = 0.00 ) AND
            ( reRegistro.vl_saldo_creditos = 0.00 ) AND
            ( reRegistro.vl_saldo_final    = 0.00 )
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
