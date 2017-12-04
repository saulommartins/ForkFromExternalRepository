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

    $Id: FTCEMGBalanceteContabil12.plsql 64050 2015-11-24 17:11:25Z franver $

* $Revision: 64050 $
* $Name$
* $Author: franver $
* $Date: 2015-11-24 15:11:25 -0200 (Tue, 24 Nov 2015) $
*
* Casos de uso: uc-02.02.22
*/
/*
CREATE TYPE balancete_contabil_registro_12
    AS ( tipo_registro             INTEGER
       , conta_contabil            VARCHAR
       , natureza_receita          VARCHAR
       , cod_fonte_recursos        INTEGER
       , saldo_inicial_cr          NUMERIC
       , natureza_saldo_inicial_cr CHAR(1)
       , total_debitos_cr          NUMERIC
       , total_creditos_cr         NUMERIC
       , saldo_final_cr            NUMERIC
       , natureza_saldo_final_cr   CHAR(1)
    );
*/
CREATE OR REPLACE FUNCTION tcemg.fn_balancete_contabil_12(VARCHAR, VARCHAR, VARCHAR, VARCHAR) RETURNS SETOF balancete_contabil_registro_12 AS $$
DECLARE
    stExercicio      ALIAS FOR $1;
    stFiltro         ALIAS FOR $2;
    stDtInicial      ALIAS FOR $3;
    stDtFinal        ALIAS FOR $4;
    stSql            VARCHAR := '';
    stSqlComplemento VARCHAR := '';
    reRegistro       RECORD;
    arRetorno        NUMERIC[];
BEGIN
    stSql := '
   CREATE TEMPORARY TABLE tmp_debito AS
             SELECT *
               FROM (
                     SELECT plano_conta.cod_estrutural
                          , plano_analitica.cod_plano
                          , REPLACE(ocr.cod_estrutural, ''.'','''')::VARCHAR AS natureza_receita
                          , COALESCE(recurso.cod_recurso, 0) AS cod_recurso
                          , vl.tipo_valor
                          , vl.vl_lancamento
                          , vl.cod_entidade
                          , lo.cod_lote
                          , lo.dt_lote
                          , lo.exercicio
                          , lo.tipo
                          , vl.sequencia
                          , vl.oid as oid_temp
                          , plano_conta.escrituracao
                          , plano_conta.indicador_superavit
                          , plano_conta.atributo_tcemg
                       FROM orcamento.receita as ore
                 INNER JOIN orcamento.conta_receita as ocr
                         ON ocr.cod_conta = ore.cod_conta
                        AND ocr.exercicio = ore.exercicio
                 INNER JOIN orcamento.recurso
                         ON recurso.cod_recurso = ore.cod_recurso
                        AND recurso.exercicio   = ore.exercicio
                 INNER JOIN contabilidade.lancamento_receita    as lr
                         ON lr.cod_receita = ore.cod_receita
                        AND lr.exercicio   = ore.exercicio
                 INNER JOIN contabilidade.lancamento            as lan
                         ON lan.cod_lote     = lr.cod_lote
                        AND lan.sequencia    = lr.sequencia
                        AND lan.exercicio    = lr.exercicio
                        AND lan.cod_entidade = lr.cod_entidade
                        AND lan.tipo         = lr.tipo
                 INNER JOIN contabilidade.valor_lancamento      as vl
                         ON vl.exercicio    = lan.exercicio
                        AND vl.sequencia    = lan.sequencia
                        AND vl.cod_entidade = lan.cod_entidade
                        AND vl.cod_lote     = lan.cod_lote
                        AND vl.tipo         = lan.tipo
                 INNER JOIN contabilidade.lote                  as lo
                         ON lo.cod_lote     = lan.cod_lote
                        AND lo.cod_entidade = lan.cod_entidade
                        AND lo.exercicio    = lan.exercicio
                        AND lo.tipo         = lan.tipo 
                  LEFT JOIN contabilidade.conta_debito
                         ON conta_debito.exercicio    = vl.exercicio
                        AND conta_debito.cod_entidade = vl.cod_entidade
                        AND conta_debito.tipo         = vl.tipo
                        AND conta_debito.cod_lote     = vl.cod_lote
                  LEFT JOIN contabilidade.plano_analitica
                         ON plano_analitica.exercicio = conta_debito.exercicio
                        AND plano_analitica.cod_plano = conta_debito.cod_plano
                  LEFT JOIN contabilidade.plano_conta
                         ON plano_conta.exercicio = plano_analitica.exercicio
                        AND plano_conta.cod_conta = plano_analitica.cod_conta
                      WHERE ore.exercicio = '''||stExercicio||'''
                        AND vl.tipo_valor = ''D''
                        AND plano_conta.cod_conta IN (SELECT plano_conta.cod_conta
                                FROM contabilidade.plano_conta
                          INNER JOIN (SELECT publico.fn_mascarareduzida(plano_conta.cod_estrutural)||''%'' AS cod_estrutural_reduzido
                                           , plano_conta.atributo_tcemg
                                           , plano_conta.exercicio
                                        FROM contabilidade.plano_conta
                                       WHERE plano_conta.escrituracao_pcasp = ''S''
                                         AND plano_conta.atributo_tcemg = 12
                                         AND plano_conta.exercicio = '''||stExercicio||'''
                                    GROUP BY cod_estrutural_reduzido
                                           , plano_conta.atributo_tcemg
                                           , plano_conta.exercicio
                                     ) AS plano_conta_reduzido
                                  ON plano_conta.cod_estrutural ILIKE plano_conta_reduzido.cod_estrutural_reduzido
                                 AND plano_conta.atributo_tcemg = plano_conta_reduzido.atributo_tcemg
                                 AND plano_conta.exercicio = plano_conta_reduzido.exercicio
                               WHERE plano_conta.exercicio = '''||stExercicio||''')

                   GROUP BY plano_conta.cod_estrutural
                          , plano_analitica.cod_plano
                          , natureza_receita
                          , recurso.cod_recurso
                          , vl.tipo_valor
                          , vl.vl_lancamento
                          , vl.cod_entidade
                          , lo.cod_lote
                          , lo.dt_lote
                          , lo.exercicio
                          , lo.tipo
                          , vl.sequencia
                          , oid_temp
                          , plano_conta.escrituracao
                          , plano_conta.indicador_superavit
                          , plano_conta.atributo_tcemg
                   ORDER BY plano_conta.cod_estrutural
                  ) as tabela
            WHERE ' || stFiltro ;
    EXECUTE stSql;
    
    stSql := '
   CREATE TEMPORARY TABLE tmp_credito AS
             SELECT *
               FROM (
                     SELECT plano_conta.cod_estrutural
                          , plano_analitica.cod_plano
                          , REPLACE(ocr.cod_estrutural, ''.'','''')::VARCHAR AS natureza_receita
                          , COALESCE(recurso.cod_recurso, 0) AS cod_recurso
                          , vl.tipo_valor
                          , vl.vl_lancamento
                          , vl.cod_entidade
                          , lo.cod_lote
                          , lo.dt_lote
                          , lo.exercicio
                          , lo.tipo
                          , vl.sequencia
                          , vl.oid as oid_temp
                          , plano_conta.escrituracao
                          , plano_conta.indicador_superavit
                          , plano_conta.atributo_tcemg
                       FROM orcamento.receita as ore
                 INNER JOIN orcamento.conta_receita as ocr
                         ON ocr.cod_conta = ore.cod_conta
                        AND ocr.exercicio = ore.exercicio
                 INNER JOIN orcamento.recurso
                         ON recurso.cod_recurso = ore.cod_recurso
                        AND recurso.exercicio   = ore.exercicio
                 INNER JOIN contabilidade.lancamento_receita    as lr
                         ON lr.cod_receita = ore.cod_receita
                        AND lr.exercicio   = ore.exercicio
                 INNER JOIN contabilidade.lancamento            as lan
                         ON lan.cod_lote     = lr.cod_lote
                        AND lan.sequencia    = lr.sequencia
                        AND lan.exercicio    = lr.exercicio
                        AND lan.cod_entidade = lr.cod_entidade
                        AND lan.tipo         = lr.tipo
                 INNER JOIN contabilidade.valor_lancamento      as vl
                         ON vl.exercicio    = lan.exercicio
                        AND vl.sequencia    = lan.sequencia
                        AND vl.cod_entidade = lan.cod_entidade
                        AND vl.cod_lote     = lan.cod_lote
                        AND vl.tipo         = lan.tipo
                 INNER JOIN contabilidade.lote                  as lo
                         ON lo.cod_lote     = lan.cod_lote
                        AND lo.cod_entidade = lan.cod_entidade
                        AND lo.exercicio    = lan.exercicio
                        AND lo.tipo         = lan.tipo 
                  LEFT JOIN contabilidade.conta_credito
                         ON conta_credito.exercicio    = vl.exercicio
                        AND conta_credito.cod_entidade = vl.cod_entidade
                        AND conta_credito.tipo         = vl.tipo
                        AND conta_credito.cod_lote     = vl.cod_lote
                  LEFT JOIN contabilidade.plano_analitica
                         ON plano_analitica.exercicio = conta_credito.exercicio
                        AND plano_analitica.cod_plano = conta_credito.cod_plano
                  LEFT JOIN contabilidade.plano_conta
                         ON plano_conta.exercicio = plano_analitica.exercicio
                        AND plano_conta.cod_conta = plano_analitica.cod_conta
                      WHERE ore.exercicio       = '''||stExercicio||'''
                        AND vl.tipo_valor   = ''C''
                        AND plano_conta.cod_conta IN (SELECT plano_conta.cod_conta
                                FROM contabilidade.plano_conta
                          INNER JOIN (SELECT publico.fn_mascarareduzida(plano_conta.cod_estrutural)||''%'' AS cod_estrutural_reduzido
                                           , plano_conta.atributo_tcemg
                                           , plano_conta.exercicio
                                        FROM contabilidade.plano_conta
                                       WHERE plano_conta.escrituracao_pcasp = ''S''
                                         AND plano_conta.atributo_tcemg = 12
                                         AND plano_conta.exercicio = '''||stExercicio||'''
                                    GROUP BY cod_estrutural_reduzido
                                           , plano_conta.atributo_tcemg
                                           , plano_conta.exercicio
                                     ) AS plano_conta_reduzido
                                  ON plano_conta.cod_estrutural ILIKE plano_conta_reduzido.cod_estrutural_reduzido
                                 AND plano_conta.atributo_tcemg = plano_conta_reduzido.atributo_tcemg
                                 AND plano_conta.exercicio = plano_conta_reduzido.exercicio
                               WHERE plano_conta.exercicio = '''||stExercicio||''')

                   GROUP BY plano_conta.cod_estrutural
                          , plano_analitica.cod_plano
                          , natureza_receita
                          , recurso.cod_recurso
                          , vl.tipo_valor
                          , vl.vl_lancamento
                          , vl.cod_entidade
                          , lo.cod_lote
                          , lo.dt_lote
                          , lo.exercicio
                          , lo.tipo
                          , vl.sequencia
                          , oid_temp
                          , plano_conta.escrituracao
                          , plano_conta.indicador_superavit
                          , plano_conta.atributo_tcemg
                   ORDER BY plano_conta.cod_estrutural
                    ) as tabela
              WHERE ' || stFiltro ;
    EXECUTE stSql;
    
    CREATE UNIQUE INDEX unq_debito  ON tmp_debito  (cod_estrutural varchar_pattern_ops, cod_recurso, natureza_receita, oid_temp);
    CREATE UNIQUE INDEX unq_credito ON tmp_credito (cod_estrutural varchar_pattern_ops, cod_recurso, natureza_receita, oid_temp);

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

    CREATE UNIQUE INDEX unq_totaliza_credito ON tmp_totaliza_credito (cod_estrutural varchar_pattern_ops, cod_recurso, natureza_receita, oid_temp);
    CREATE UNIQUE INDEX unq_totaliza_debito  ON tmp_totaliza_debito  (cod_estrutural varchar_pattern_ops, cod_recurso, natureza_receita, oid_temp);

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

    CREATE UNIQUE INDEX unq_totaliza ON tmp_totaliza (cod_estrutural varchar_pattern_ops, cod_recurso, natureza_receita, oid_temp);

    stSql := '
     CREATE TEMPORARY TABLE tmp_contas_utilizadas AS
               SELECT *
                 FROM tmp_debito
                UNION
               SELECT *
                 FROM tmp_credito
                UNION
               SELECT *
                 FROM tmp_totaliza
      ';
    EXECUTE stSql;

    CREATE UNIQUE INDEX unq_contas_utilizadas ON tmp_contas_utilizadas (cod_estrutural varchar_pattern_ops, cod_recurso, natureza_receita, oid_temp);

    stSql := '
           SELECT 12::INTEGER AS tipo_registro
                , plano_conta.cod_estrutural_contabil AS conta_contabil
                , RPAD(COALESCE(tmp_contas_utilizadas.natureza_receita, ''0'')::VARCHAR, 8, ''0'')::VARCHAR AS natureza_receita_reduzida
                , COALESCE(tmp_contas_utilizadas.cod_recurso, 0) AS cod_recurso
                , 0.00 as vl_saldo_inicial
                , '' ''::CHAR(1) AS natureza_saldo_incial
                , 0.00 as vl_saldo_debitos
                , 0.00 as vl_saldo_creditos
                , 0.00 as vl_saldo_final
                , '' ''::CHAR(1) AS natureza_saldo_final
             FROM (SELECT publico.fn_mascarareduzida(plano_conta.cod_estrutural)||''%'' AS cod_estrutural_reduzido
                        , plano_conta.cod_estrutural AS cod_estrutural_contabil
                        , plano_conta.atributo_tcemg
                     FROM contabilidade.plano_conta
                    WHERE plano_conta.escrituracao_pcasp = ''S''
                      AND plano_conta.atributo_tcemg = 12
                      AND plano_conta.exercicio = '''||stExercicio||'''
                  ) AS plano_conta
        LEFT JOIN tmp_contas_utilizadas
               ON tmp_contas_utilizadas.cod_estrutural ILIKE plano_conta.cod_estrutural_reduzido
              --AND tmp_contas_utilizadas.atributo_tcemg = plano_conta.atributo_tcemg 
         GROUP BY conta_contabil
                , natureza_receita_reduzida
                , tmp_contas_utilizadas.cod_recurso
          ORDER BY conta_contabil ASC';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        arRetorno := tcemg.fn_balancete_contabil_totaliza_receita( publico.fn_mascarareduzida(reRegistro.conta_contabil) , reRegistro.cod_recurso, reRegistro.natureza_receita_reduzida);
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
    DROP TABLE tmp_contas_utilizadas;

    RETURN;
END;
$$ LANGUAGE 'plpgsql';