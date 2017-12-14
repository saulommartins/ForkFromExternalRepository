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
/* recuperaServidorSalario2
 * Data de Criação : 09/11/2015
 * @author Analista : Dagiane Vieira
 * @author Desenvolvedor : Michel Teixeira
 * $Id: FTCMBARecuperaSalario2.plsql 63946 2015-11-10 21:10:32Z michel $
*/

/*
CREATE TYPE tcmba_servidor_salario2 AS (
    cod_periodo_movimentacao        INTEGER
  , cod_servidor_pensionista        INTEGER
  , tipo                            INTEGER
  , cod_contrato                    INTEGER
  , num_orgao                       INTEGER
  , cod_tipo_cargo                  INTEGER
  , funcao_atual                    INTEGER
  , classe                          INTEGER
  , numcgm                          INTEGER
  , nom_cgm                         VARCHAR
  , cpf                             VARCHAR
  , matricula                       INTEGER
  , cod_cargo                       INTEGER
  , nro_dias                        INTEGER
  , horas_mensais                   INTEGER
  , cod_funcao_temporario           INTEGER
  , folha                           INTEGER
  , cod_previdencia                 INTEGER
  , salario_base                    NUMERIC
  , salario_vantagens               NUMERIC
  , salario_gratificacao            NUMERIC
  , salario_familia                 NUMERIC
  , salario_ferias                  NUMERIC
  , salario_horas_extra             NUMERIC
  , salario_decimo                  NUMERIC
  , salario_descontos               NUMERIC
  , desconto_irrf                   NUMERIC
  , desconto_irrf_decimo            NUMERIC
  , desconto_consignado_1           NUMERIC
  , cod_banco_1                     VARCHAR
  , desconto_consignado_2           NUMERIC
  , cod_banco_2                     VARCHAR
  , desconto_consignado_3           NUMERIC
  , cod_banco_3                     VARCHAR
  , desconto_previdencia            NUMERIC
  , desconto_irrf_ferias            NUMERIC
  , desconto_previdencia_decimo     NUMERIC
  , desconto_previdencia_ferias     NUMERIC
  , desconto_pensao                 NUMERIC
  , desconto_plano_saude            NUMERIC
  , salario_liquido                 NUMERIC
);
*/

CREATE OR REPLACE FUNCTION tcmba.recuperaServidorSalario2(VARCHAR, VARCHAR, VARCHAR, VARCHAR) RETURNS SETOF tcmba_servidor_salario2 AS $$
DECLARE
    stExercicio             ALIAS FOR $1;
    stEntidade              ALIAS FOR $2;
    stEntidadeRh            ALIAS FOR $3;
    stCompetencia           ALIAS FOR $4;

    stSQL                       VARCHAR :='';
    inCodPeridoMovimentacao     INTEGER := 0;
    timestampPeridoMovimentacao TIMESTAMP;

    inCodContrato           INTEGER := 0;
    stContratos             TEXT :='';
    stEventos               TEXT :='';
    stSQLEventos            VARCHAR :='';

    crCursor                REFCURSOR;
    reRecord                RECORD;
    reRecordEventos         RECORD;
BEGIN
    stSQL := '
     SELECT cod_periodo_movimentacao
       FROM folhapagamento'||stEntidadeRh||'.periodo_movimentacao
      WHERE TO_CHAR(periodo_movimentacao.dt_inicial,''mmyyyy'') = '''||stCompetencia||''' ';

    OPEN crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO inCodPeridoMovimentacao;
    CLOSE crCursor;

    SELECT ultimoTimestampPeriodoMovimentacao( inCodPeridoMovimentacao, ''||stEntidadeRh||'')::timestamp
      INTO timestampPeridoMovimentacao;

    stSQL := 'CREATE TEMPORARY TABLE tmp_servidor_salario2 as
                    SELECT '||inCodPeridoMovimentacao||' AS cod_periodo_movimentacao
                         , contrato_servidor.cod_servidor_pensionista
                         , contrato_servidor.tipo
                         , contrato.cod_contrato
                         , de_para_lotacao_orgao.num_orgao
                         , de_para_tipo_cargo_tcmba.cod_tipo_cargo_tce AS cod_tipo_cargo
                         , tcmba_cargo_servidor.cod_tipo_funcao AS funcao_atual
                         , fonte_recurso_lotacao.cod_tipo_fonte AS classe
                         , contrato_servidor.numcgm
                         , sem_acentos(sw_cgm.nom_cgm) AS nom_cgm
                         , sw_cgm_pessoa_fisica.cpf
                         , contrato.registro AS matricula
                         , CASE WHEN de_para_tipo_cargo_tcmba.cod_tipo_cargo_tce NOT IN (4) THEN
                                        contrato_servidor.cod_cargo
                           END AS cod_cargo
                         , 30 AS nro_dias
                         , COALESCE(padrao.horas_mensais::integer, 0) AS horas_mensais
                         , CASE WHEN de_para_tipo_cargo_tcmba.cod_tipo_cargo_tce IN (4) THEN
                                        tcmba_cargo_servidor_temporario.cod_tipo_funcao
                           END AS cod_funcao_temporario
                         , contrato_servidor_periodo.folha
                         , servidor_previdencia.cod_previdencia

                    FROM pessoal'||stEntidadeRh||'.contrato

              INNER JOIN ( SELECT servidor.numcgm
                                , servidor.cod_servidor AS cod_servidor_pensionista
                                , 1 as tipo
                                , servidor_contrato_servidor.cod_contrato
                                , contrato_servidor.cod_cargo
                                , contrato_servidor.cod_sub_divisao
                                , contrato_servidor_orgao.cod_orgao
                             FROM pessoal'||stEntidadeRh||'.servidor
                       INNER JOIN pessoal'||stEntidadeRh||'.servidor_contrato_servidor
                               ON servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                       INNER JOIN pessoal'||stEntidadeRh||'.contrato_servidor
                               ON contrato_servidor.cod_contrato = servidor_contrato_servidor.cod_contrato
                       INNER JOIN (SELECT contrato_servidor_orgao.cod_contrato
                                        , contrato_servidor_orgao.cod_orgao
                                        , contrato_servidor_orgao.timestamp
                                     FROM pessoal'||stEntidadeRh||'.contrato_servidor_orgao
                                    WHERE contrato_servidor_orgao.timestamp = (
                                                                                SELECT max(CSO.timestamp) AS timestamp
                                                                                  FROM pessoal'||stEntidadeRh||'.contrato_servidor_orgao AS CSO
                                                                                 WHERE CSO.cod_contrato = contrato_servidor_orgao.cod_contrato
                                                                                   AND CSO.timestamp <= ('''||timestampPeridoMovimentacao||''')::TIMESTAMP
                                                                              )
                                  ) AS contrato_servidor_orgao
                               ON contrato_servidor_orgao.cod_contrato = contrato_servidor.cod_contrato

                            UNION

                           SELECT pensionista.numcgm
                                , pensionista.cod_pensionista AS cod_servidor_pensionista
                                , 2 as tipo
                                , contrato_pensionista.cod_contrato
                                , contrato_servidor.cod_cargo
                                , contrato_servidor.cod_sub_divisao
                                , contrato_pensionista_orgao.cod_orgao
                             FROM pessoal'||stEntidadeRh||'.pensionista
                       INNER JOIN pessoal'||stEntidadeRh||'.contrato_pensionista
                               ON contrato_pensionista.cod_pensionista = pensionista.cod_pensionista
                              AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente
                       INNER JOIN pessoal'||stEntidadeRh||'.contrato_servidor
                               ON contrato_servidor.cod_contrato = contrato_pensionista.cod_contrato_cedente
                       INNER JOIN (SELECT contrato_pensionista_orgao.*
                                     FROM pessoal'||stEntidadeRh||'.contrato_pensionista_orgao
                                    WHERE contrato_pensionista_orgao.timestamp = (
                                                                                    SELECT max(CPO.timestamp) AS timestamp
                                                                                      FROM pessoal'||stEntidadeRh||'.contrato_pensionista_orgao AS CPO
                                                                                     WHERE CPO.cod_contrato = contrato_pensionista_orgao.cod_contrato
                                                                                       AND CPO.timestamp <= ('''||timestampPeridoMovimentacao||''')::TIMESTAMP
                                                                                 )
                                  ) AS contrato_pensionista_orgao
                               ON contrato_pensionista_orgao.cod_contrato = contrato_pensionista.cod_contrato
                         ) AS contrato_servidor
                      ON contrato.cod_contrato = contrato_servidor.cod_contrato

               LEFT JOIN pessoal'||stEntidadeRh||'.de_para_lotacao_orgao
                      ON de_para_lotacao_orgao.cod_orgao = contrato_servidor.cod_orgao
                     AND de_para_lotacao_orgao.exercicio = '''||stExercicio||'''

               LEFT JOIN pessoal'||stEntidadeRh||'.de_para_tipo_cargo_tcmba
                      ON de_para_tipo_cargo_tcmba.cod_sub_divisao = contrato_servidor.cod_sub_divisao

               LEFT JOIN folhapagamento'||stEntidadeRh||'.tcmba_cargo_servidor
                      ON tcmba_cargo_servidor.cod_cargo = contrato_servidor.cod_cargo
                     AND tcmba_cargo_servidor.cod_entidade IN ('||stEntidade||')
                     AND tcmba_cargo_servidor.exercicio = '''||stExercicio||'''

               LEFT JOIN tcmba.fonte_recurso_lotacao
                      ON fonte_recurso_lotacao.cod_orgao = contrato_servidor.cod_orgao
                     AND fonte_recurso_lotacao.cod_entidade IN ('||stEntidade||')
                     AND fonte_recurso_lotacao.exercicio = '''||stExercicio||'''

              INNER JOIN sw_cgm
                      ON sw_cgm.numcgm = contrato_servidor.numcgm

              INNER JOIN sw_cgm_pessoa_fisica
                      ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm

              INNER JOIN folhapagamento'||stEntidadeRh||'.periodo_movimentacao
                      ON periodo_movimentacao.cod_periodo_movimentacao = '||inCodPeridoMovimentacao||'

              INNER JOIN ( SELECT periodo_movimentacao.cod_periodo_movimentacao
                                , contrato.cod_contrato
                                , CASE WHEN registro_evento_periodo.cod_contrato IS NOT NULL THEN
                                                0
                                       WHEN registro_evento_complementar.cod_contrato IS NOT NULL THEN
                                                registro_evento_complementar.cod_complementar
                                       ELSE
                                                0
                                       END
                                  AS folha
                             FROM pessoal'||stEntidadeRh||'.contrato

                       INNER JOIN folhapagamento'||stEntidadeRh||'.periodo_movimentacao
                               ON periodo_movimentacao.cod_periodo_movimentacao = '||inCodPeridoMovimentacao||'

                        LEFT JOIN folhapagamento'||stEntidadeRh||'.registro_evento_periodo
                               ON periodo_movimentacao.cod_periodo_movimentacao = registro_evento_periodo.cod_periodo_movimentacao
                              AND contrato.cod_contrato = registro_evento_periodo.cod_contrato 

                        LEFT JOIN ( select max(cod_complementar) as cod_complementar
                                         , cod_contrato
                                      from folhapagamento'||stEntidadeRh||'.registro_evento_complementar
                                     where registro_evento_complementar.cod_periodo_movimentacao = '||inCodPeridoMovimentacao||'
                                  group by registro_evento_complementar.cod_contrato
                                  ) AS registro_evento_complementar
                               ON contrato.cod_contrato = registro_evento_complementar.cod_contrato

                        LEFT JOIN folhapagamento'||stEntidadeRh||'.registro_evento_ferias
                               ON periodo_movimentacao.cod_periodo_movimentacao = registro_evento_ferias.cod_periodo_movimentacao
                              AND contrato.cod_contrato = registro_evento_ferias.cod_contrato

                        LEFT JOIN folhapagamento'||stEntidadeRh||'.registro_evento_decimo
                               ON periodo_movimentacao.cod_periodo_movimentacao = registro_evento_decimo.cod_periodo_movimentacao
                              AND contrato.cod_contrato = registro_evento_decimo.cod_contrato 

                            WHERE registro_evento_periodo.cod_contrato      IS NOT NULL
                               OR registro_evento_complementar.cod_contrato IS NOT NULL
                               OR registro_evento_decimo.cod_contrato       IS NOT NULL
                               OR registro_evento_ferias.cod_contrato       IS NOT NULL

                         GROUP BY periodo_movimentacao.cod_periodo_movimentacao
                                , contrato.cod_contrato
                                , folha
                         ) AS contrato_servidor_periodo
                      ON contrato_servidor_periodo.cod_contrato = contrato.cod_contrato
                     AND contrato_servidor_periodo.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao

               LEFT JOIN ( SELECT ultimo_contrato_servidor_previdencia.cod_contrato
                                , ultimo_contrato_servidor_previdencia.cod_previdencia
                             FROM ultimo_contrato_servidor_previdencia( '''||stEntidadeRh||'''
                                                                      , '||inCodPeridoMovimentacao||'
                                                                      )

                       INNER JOIN folhapagamento'||stEntidadeRh||'.previdencia
                               ON previdencia.cod_previdencia = ultimo_contrato_servidor_previdencia.cod_previdencia
                              AND previdencia.cod_regime_previdencia = 1

                         GROUP BY ultimo_contrato_servidor_previdencia.cod_contrato
                                , ultimo_contrato_servidor_previdencia.cod_previdencia
                         ) AS servidor_previdencia
                      ON servidor_previdencia.cod_contrato = contrato_servidor_periodo.cod_contrato

               LEFT JOIN pessoal'||stEntidadeRh||'.cargo_padrao
                      ON cargo_padrao.cod_cargo = contrato_servidor.cod_cargo
                     AND cargo_padrao.timestamp = (
                                                    select max(cp.timestamp) as timestamp
                                                      from pessoal'||stEntidadeRh||'.cargo_padrao as cp
                                                     where cp.cod_cargo = cargo_padrao.cod_cargo
                                                       and cp.timestamp <= ('''||timestampPeridoMovimentacao||''')::TIMESTAMP
                                                  )

               LEFT JOIN folhapagamento'||stEntidadeRh||'.padrao
                      ON padrao.cod_padrao = cargo_padrao.cod_padrao

               LEFT JOIN folhapagamento'||stEntidadeRh||'.tcmba_cargo_servidor_temporario
                      ON tcmba_cargo_servidor_temporario.exercicio = '''||stExercicio||'''
                     AND tcmba_cargo_servidor_temporario.cod_entidade IN ('||stEntidade||')
                     AND tcmba_cargo_servidor_temporario.cod_cargo = contrato_servidor.cod_cargo

                GROUP BY contrato_servidor.cod_servidor_pensionista
                       , contrato_servidor.tipo
                       , contrato.cod_contrato
                       , de_para_lotacao_orgao.num_orgao 
                       , de_para_tipo_cargo_tcmba.cod_tipo_cargo_tce
                       , tcmba_cargo_servidor.cod_tipo_funcao
                       , fonte_recurso_lotacao.cod_tipo_fonte
                       , contrato_servidor.numcgm
                       , sw_cgm.nom_cgm
                       , sw_cgm_pessoa_fisica.cpf
                       , contrato.registro
                       , contrato_servidor.cod_cargo
                       , padrao.horas_mensais
                       , cod_funcao_temporario
                       , contrato_servidor_periodo.folha
                       , servidor_previdencia.cod_previdencia

                ORDER BY sw_cgm.nom_cgm
                       , contrato.cod_contrato ';
    EXECUTE stSql;

    --LISTA DE CONTRATOS
    stSQL := ' SELECT COALESCE(array_to_string( publico.concatenar_array( cod_contrato ), '','' ), ''0'') AS contratos
                 FROM ( SELECT cod_contrato
                          FROM tmp_servidor_salario2
                      GROUP BY cod_contrato
                      ORDER BY cod_contrato
                      ) AS contratos ';

    OPEN crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO stContratos;
    CLOSE crCursor;

    --SALARIO BASE
    stSQL := ' SELECT COALESCE(array_to_string( publico.concatenar_array( cod_evento ), '','' ), ''0'') AS eventos
                 FROM folhapagamento'||stEntidadeRh||'.tcmba_salario_base
                WHERE cod_entidade IN ('||stEntidade||')
                  AND exercicio = '''||stExercicio||'''
             GROUP BY exercicio
                    , cod_entidade ';

    OPEN crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO stEventos;
    CLOSE crCursor;
    IF stEventos IS NULL THEN
        stEventos := '0';
    END IF;

    stSQLEventos := 'CREATE TEMPORARY TABLE tmp_tcmba_salario_base as
                          SELECT *
                            FROM tcmba.recuperaServidorValoresPorEvento(  '||inCodPeridoMovimentacao||'
                                                                        , '''||stEntidadeRh||'''
                                                                        , '''||stEventos||'''
                                                                       ) AS salario_base
                                                                       (  cod_periodo_movimentacao  INTEGER
                                                                        , cod_contrato              INTEGER
                                                                        , valor                     NUMERIC
                                                                       )
                           WHERE cod_contrato IN ( '||stContratos||' )';
    EXECUTE stSQLEventos;

    --SALARIO VANTAGENS
    stSQL := ' SELECT array_to_string( publico.concatenar_array( cod_evento ), '','' ) AS eventos
                 FROM folhapagamento'||stEntidadeRh||'.tcmba_vantagens_salariais
                WHERE cod_entidade IN ('||stEntidade||')
                  AND exercicio = '''||stExercicio||'''
             GROUP BY exercicio
                    , cod_entidade ';

    OPEN crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO stEventos;
    CLOSE crCursor;
    IF stEventos IS NULL THEN
        stEventos := '0';
    END IF;

    stSQLEventos := 'CREATE TEMPORARY TABLE tmp_tcmba_salario_vantagens as
                          SELECT *
                            FROM tcmba.recuperaServidorValoresPorEvento(  '||inCodPeridoMovimentacao||'
                                                                        , '''||stEntidadeRh||'''
                                                                        , '''||stEventos||'''
                                                                       ) AS salario_base
                                                                       (  cod_periodo_movimentacao  INTEGER
                                                                        , cod_contrato              INTEGER
                                                                        , valor                     NUMERIC
                                                                       )
                           WHERE cod_contrato IN ( '||stContratos||' )';
    EXECUTE stSQLEventos;

    --SALARIO GRATIFICAÇÃO 
    stSQL := ' SELECT array_to_string( publico.concatenar_array( cod_evento ), '','' ) AS eventos
                 FROM folhapagamento'||stEntidadeRh||'.tcmba_gratificacao_funcao
                WHERE cod_entidade IN ('||stEntidade||')
                  AND exercicio = '''||stExercicio||'''
             GROUP BY exercicio
                    , cod_entidade ';

    OPEN crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO stEventos;
    CLOSE crCursor;
    IF stEventos IS NULL THEN
        stEventos := '0';
    END IF;

    stSQLEventos := 'CREATE TEMPORARY TABLE tmp_tcmba_salario_gratificacao as
                          SELECT *
                            FROM tcmba.recuperaServidorValoresPorEvento(  '||inCodPeridoMovimentacao||'
                                                                        , '''||stEntidadeRh||'''
                                                                        , '''||stEventos||'''
                                                                       ) AS salario_base
                                                                       (  cod_periodo_movimentacao  INTEGER
                                                                        , cod_contrato              INTEGER
                                                                        , valor                     NUMERIC
                                                                       )
                           WHERE cod_contrato IN ( '||stContratos||' )';
    EXECUTE stSQLEventos;

    --SALARIO FAMILIA 
    stSQL := ' SELECT array_to_string( publico.concatenar_array( cod_evento ), '','' ) AS eventos
                 FROM folhapagamento'||stEntidadeRh||'.tcmba_salario_familia
                WHERE cod_entidade IN ('||stEntidade||')
                  AND exercicio = '''||stExercicio||'''
             GROUP BY exercicio
                    , cod_entidade ';

    OPEN crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO stEventos;
    CLOSE crCursor;
    IF stEventos IS NULL THEN
        stEventos := '0';
    END IF;

    stSQLEventos := 'CREATE TEMPORARY TABLE tmp_tcmba_salario_familia as
                          SELECT *
                            FROM tcmba.recuperaServidorValoresPorEvento(  '||inCodPeridoMovimentacao||'
                                                                        , '''||stEntidadeRh||'''
                                                                        , '''||stEventos||'''
                                                                       ) AS salario_base
                                                                       (  cod_periodo_movimentacao  INTEGER
                                                                        , cod_contrato              INTEGER
                                                                        , valor                     NUMERIC
                                                                       )
                           WHERE cod_contrato IN ( '||stContratos||' )';
    EXECUTE stSQLEventos;

    --SALARIO FERIAS
    stSQLEventos := 'CREATE TEMPORARY TABLE tmp_tcmba_salario_ferias as
                          SELECT *
                            FROM tcmba.recuperaServidorValoresPorEventoFerias(  '||inCodPeridoMovimentacao||'
                                                                              , '''||stEntidadeRh||'''
                                                                              , ''''
                                                                              , TRUE
                                                                              , FALSE
                                                                             ) AS salario_ferias
                                                                             (  cod_periodo_movimentacao  INTEGER
                                                                              , cod_contrato              INTEGER
                                                                              , valor                     NUMERIC
                                                                             )
                           WHERE cod_contrato IN ( '||stContratos||' )';
    EXECUTE stSQLEventos;

    --SALARIO HORAS EXTRA
    stSQL := ' SELECT array_to_string( publico.concatenar_array( cod_evento ), '','' ) AS eventos
                 FROM folhapagamento'||stEntidadeRh||'.tcmba_salario_horas_extras
                WHERE cod_entidade IN ('||stEntidade||')
                  AND exercicio = '''||stExercicio||'''
             GROUP BY exercicio
                    , cod_entidade ';

    OPEN crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO stEventos;
    CLOSE crCursor;
    IF stEventos IS NULL THEN
        stEventos := '0';
    END IF;

    stSQLEventos := 'CREATE TEMPORARY TABLE tmp_tcmba_salario_horas_extra as
                          SELECT *
                            FROM tcmba.recuperaServidorValoresPorEvento(  '||inCodPeridoMovimentacao||'
                                                                        , '''||stEntidadeRh||'''
                                                                        , '''||stEventos||'''
                                                                       ) AS salario_horas_extra 
                                                                       (  cod_periodo_movimentacao  INTEGER
                                                                        , cod_contrato              INTEGER
                                                                        , valor                     NUMERIC
                                                                       )
                           WHERE cod_contrato IN ( '||stContratos||' )';
    EXECUTE stSQLEventos;

    --SALARIO DECIMO
    stSQLEventos := 'CREATE TEMPORARY TABLE tmp_tcmba_salario_decimo as
                          SELECT *
                            FROM tcmba.recuperaServidorValoresPorEventoDecimo(  '||inCodPeridoMovimentacao||'
                                                                              , '''||stEntidadeRh||'''
                                                                              , ''''
                                                                              , TRUE
                                                                              , FALSE
                                                                             ) AS salario_decimo
                                                                             (  cod_periodo_movimentacao  INTEGER
                                                                              , cod_contrato              INTEGER
                                                                              , valor                     NUMERIC
                                                                             )
                           WHERE cod_contrato IN ( '||stContratos||' )';
    EXECUTE stSQLEventos;

    --SALARIO DESCONTOS
    stSQL := ' SELECT array_to_string( publico.concatenar_array( cod_evento ), '','' ) AS eventos
                 FROM folhapagamento'||stEntidadeRh||'.tcmba_salario_descontos
                WHERE cod_entidade IN ('||stEntidade||')
                  AND exercicio = '''||stExercicio||'''
             GROUP BY exercicio
                    , cod_entidade ';

    OPEN crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO stEventos;
    CLOSE crCursor;
    IF stEventos IS NULL THEN
        stEventos := '0';
    END IF;

    stSQLEventos := 'CREATE TEMPORARY TABLE tmp_tcmba_salario_descontos as
                          SELECT *
                            FROM tcmba.recuperaServidorValoresPorEvento(  '||inCodPeridoMovimentacao||'
                                                                        , '''||stEntidadeRh||'''
                                                                        , '''||stEventos||'''
                                                                       ) AS salario_descontos 
                                                                       (  cod_periodo_movimentacao  INTEGER
                                                                        , cod_contrato              INTEGER
                                                                        , valor                     NUMERIC
                                                                       )
                           WHERE cod_contrato IN ( '||stContratos||' )';
    EXECUTE stSQLEventos;

    --DESCONTOS IRRF
    stSQL := ' SELECT array_to_string( publico.concatenar_array( cod_evento ), '','' ) AS eventos
                 FROM folhapagamento'||stEntidadeRh||'.tabela_irrf_evento
                WHERE tabela_irrf_evento.cod_tipo IN (4, 5)
                  AND tabela_irrf_evento.timestamp = (
                                                        select max(irrf.timestamp) as timestamp
                                                          from folhapagamento'||stEntidadeRh||'.tabela_irrf_evento as irrf
                                                         where irrf.cod_tabela  = tabela_irrf_evento.cod_tabela
                                                           and irrf.cod_tipo    = tabela_irrf_evento.cod_tipo
                                                           and irrf.cod_evento  = tabela_irrf_evento.cod_evento
                                                           and irrf.timestamp  <= ('''||timestampPeridoMovimentacao||''')::TIMESTAMP
                                                     ) ';

    OPEN crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO stEventos;
    CLOSE crCursor;
    IF stEventos IS NULL THEN
        stEventos := '0';
    END IF;

    stSQLEventos := 'CREATE TEMPORARY TABLE tmp_tcmba_descontos_irrf as
                          SELECT *
                            FROM tcmba.recuperaServidorValoresPorEvento(  '||inCodPeridoMovimentacao||'
                                                                        , '''||stEntidadeRh||'''
                                                                        , '''||stEventos||'''
                                                                       ) AS desconto_irrf 
                                                                       (  cod_periodo_movimentacao  INTEGER
                                                                        , cod_contrato              INTEGER
                                                                        , valor                     NUMERIC
                                                                       )
                           WHERE cod_contrato IN ( '||stContratos||' )';
    EXECUTE stSQLEventos;

    --DESCONTOS IRRF DECIMO
    stSQLEventos := 'CREATE TEMPORARY TABLE tmp_tcmba_desconto_irrf_decimo as
                          SELECT *
                            FROM tcmba.recuperaServidorValoresPorEventoDecimo(  '||inCodPeridoMovimentacao||'
                                                                              , '''||stEntidadeRh||'''
                                                                              , '''||stEventos||'''
                                                                              , FALSE
                                                                              , TRUE
                                                                             ) AS desconto_irrf_decimo
                                                                             (  cod_periodo_movimentacao  INTEGER
                                                                              , cod_contrato              INTEGER
                                                                              , valor                     NUMERIC
                                                                             )
                           WHERE cod_contrato IN ( '||stContratos||' )';
    EXECUTE stSQLEventos;

    --DESCONTOS IRRF FERIAS
    stSQLEventos := 'CREATE TEMPORARY TABLE tmp_tcmba_desconto_irrf_ferias as
                          SELECT *
                            FROM tcmba.recuperaServidorValoresPorEventoFerias(  '||inCodPeridoMovimentacao||'
                                                                              , '''||stEntidadeRh||'''
                                                                              , '''||stEventos||'''
                                                                              , FALSE
                                                                              , TRUE
                                                                             ) AS salario_ferias
                                                                             (  cod_periodo_movimentacao  INTEGER
                                                                              , cod_contrato              INTEGER
                                                                              , valor                     NUMERIC
                                                                             )
                           WHERE cod_contrato IN ( '||stContratos||' )';
    EXECUTE stSQLEventos;

    --DESCONTOS CONSIGNADO
    stSQLEventos := 'CREATE TEMPORARY TABLE tmp_tcmba_descontos_consignado as
                          SELECT *
                            FROM tcmba.recuperaServidorConsignado(  '||inCodPeridoMovimentacao||'
                                                                  , '''||stEntidadeRh||'''
                                                                  , '''||stExercicio||'''
                                                                  , '''||stEntidade||'''
                                                                 ) AS emprestimos_contrato
                                                                 (  cod_banco_1                 VARCHAR
                                                                  , vl_banco_1                  NUMERIC
                                                                  , cod_banco_2                 VARCHAR
                                                                  , vl_banco_2                  NUMERIC
                                                                  , cod_banco_3                 VARCHAR
                                                                  , vl_banco_3                  NUMERIC
                                                                  , cod_contrato                INTEGER
                                                                  , cod_periodo_movimentacao    INTEGER
                                                                 )
                           WHERE cod_contrato IN ( '||stContratos||' )';
    EXECUTE stSQLEventos;

    --PREVIDENCIA
    CREATE TEMPORARY TABLE tmp_tcmba_descontos_previdencia
    (  cod_periodo_movimentacao INTEGER
     , cod_contrato             INTEGER
     , valor                    NUMERIC
     , cod_previdencia          INTEGER
    );

    CREATE TEMPORARY TABLE tmp_tcmba_descontos_previdencia_decimo
    (  cod_periodo_movimentacao INTEGER
     , cod_contrato             INTEGER
     , valor                    NUMERIC
     , cod_previdencia          INTEGER
    );

    CREATE TEMPORARY TABLE tmp_tcmba_descontos_previdencia_ferias
    (  cod_periodo_movimentacao INTEGER
     , cod_contrato             INTEGER
     , valor                    NUMERIC
     , cod_previdencia          INTEGER
    );

    stSQL := 'CREATE TEMPORARY TABLE tmp_eventos_previdencia as
               SELECT previdencia_evento.cod_previdencia
                    , array_to_string( publico.concatenar_array( previdencia_evento.cod_evento ), '','' ) AS eventos
                 FROM folhapagamento'||stEntidadeRh||'.previdencia_evento

           INNER JOIN folhapagamento'||stEntidadeRh||'.previdencia
                   ON previdencia.cod_previdencia = previdencia_evento.cod_previdencia
                  AND previdencia.cod_regime_previdencia = 1

                WHERE previdencia_evento.timestamp = ( select max(timestamp) as timestamp
                                                         from folhapagamento'||stEntidadeRh||'.previdencia_previdencia
                                                        where previdencia_previdencia.cod_previdencia = previdencia_evento.cod_previdencia
                                                          and previdencia_previdencia.timestamp <= ('''||timestampPeridoMovimentacao||''')::TIMESTAMP )

             GROUP BY previdencia_evento.cod_previdencia';
    EXECUTE stSql;

    stSQL := 'SELECT COALESCE(tmp_servidor_salario2.cod_previdencia, 0) AS cod_previdencia
                FROM tmp_servidor_salario2
            GROUP BY cod_previdencia';

    FOR reRecord IN EXECUTE stSQL
    LOOP

        stSQLEventos := 'SELECT tmp_eventos_previdencia.eventos
                           FROM tmp_eventos_previdencia
                          WHERE tmp_eventos_previdencia.cod_previdencia = '||reRecord.cod_previdencia||' ';

        OPEN crCursor FOR EXECUTE stSQLEventos;
        FETCH crCursor INTO stEventos;
        CLOSE crCursor;
        IF stEventos IS NULL THEN
            stEventos := '0';
        END IF;

        --DESCONTOS PREVIDENCIA
        stSQLEventos := 'SELECT *
                           FROM tcmba.recuperaServidorValoresPorEvento(  '||inCodPeridoMovimentacao||'
                                                                       , '''||stEntidadeRh||'''
                                                                       , '''||stEventos||'''
                                                                      ) AS desconto_previdencia
                                                                      (  cod_periodo_movimentacao  INTEGER
                                                                       , cod_contrato              INTEGER
                                                                       , valor                     NUMERIC
                                                                      )
                          WHERE cod_contrato IN ( '||stContratos||' )';

        FOR reRecordEventos IN EXECUTE stSQLEventos
        LOOP
            INSERT INTO tmp_tcmba_descontos_previdencia
                 VALUES (  reRecordEventos.cod_periodo_movimentacao
                         , reRecordEventos.cod_contrato
                         , reRecordEventos.valor
                         , reRecord.cod_previdencia
                        );
        END LOOP;

        --DESCONTOS PREVIDENCIA DECIMO
        stSQLEventos := 'SELECT *
                           FROM tcmba.recuperaServidorValoresPorEventoDecimo(  '||inCodPeridoMovimentacao||'
                                                                             , '''||stEntidadeRh||'''
                                                                             , '''||stEventos||'''
                                                                             , FALSE
                                                                             , TRUE
                                                                            ) AS desconto_previdencia_decimo
                                                                            (  cod_periodo_movimentacao  INTEGER
                                                                             , cod_contrato              INTEGER
                                                                             , valor                     NUMERIC
                                                                            )
                          WHERE cod_contrato IN ( '||stContratos||' )';

        FOR reRecordEventos IN EXECUTE stSQLEventos
        LOOP
            INSERT INTO tmp_tcmba_descontos_previdencia_decimo
                 VALUES (  reRecordEventos.cod_periodo_movimentacao
                         , reRecordEventos.cod_contrato
                         , reRecordEventos.valor
                         , reRecord.cod_previdencia
                        );
        END LOOP;

        --DESCONTOS PREVIDENCIA FERIAS
        stSQLEventos := 'SELECT *
                           FROM tcmba.recuperaServidorValoresPorEventoFerias(  '||inCodPeridoMovimentacao||'
                                                                             , '''||stEntidadeRh||'''
                                                                             , '''||stEventos||'''
                                                                             , FALSE
                                                                             , TRUE
                                                                            ) AS desconto_previdencia_ferias
                                                                            (  cod_periodo_movimentacao  INTEGER
                                                                             , cod_contrato              INTEGER
                                                                             , valor                     NUMERIC
                                                                            )
                          WHERE cod_contrato IN ( '||stContratos||' )';
        FOR reRecordEventos IN EXECUTE stSQLEventos
        LOOP
            INSERT INTO tmp_tcmba_descontos_previdencia_ferias
                 VALUES (  reRecordEventos.cod_periodo_movimentacao
                         , reRecordEventos.cod_contrato
                         , reRecordEventos.valor
                         , reRecord.cod_previdencia
                        );
        END LOOP;

    END LOOP;

    --DESCONTO PENSAO
    stSQL := ' SELECT array_to_string( publico.concatenar_array( cod_evento ), '','' ) AS eventos
                 FROM folhapagamento'||stEntidadeRh||'.pensao_evento
                WHERE pensao_evento.timestamp = (
                                                    select max(pe.timestamp) as timestamp
                                                      from folhapagamento'||stEntidadeRh||'.pensao_evento as pe
                                                     where pe.cod_tipo = pensao_evento.cod_tipo
                                                       and pe.cod_configuracao_pensao = pensao_evento.cod_configuracao_pensao
                                                       and pe.timestamp <= ('''||timestampPeridoMovimentacao||''')::TIMESTAMP
                                                )';

    OPEN crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO stEventos;
    CLOSE crCursor;
    IF stEventos IS NULL THEN
        stEventos := '0';
    END IF;

    stSQLEventos := 'CREATE TEMPORARY TABLE tmp_tcmba_descontos_pensao as
                          SELECT *
                            FROM tcmba.recuperaServidorValoresPorEventoPensao(  '||inCodPeridoMovimentacao||'
                                                                              , '''||stEntidadeRh||'''
                                                                              , '''||stEventos||'''
                                                                             ) AS descontos_pensao 
                                                                             (  cod_periodo_movimentacao  INTEGER
                                                                              , cod_contrato              INTEGER
                                                                              , valor                     NUMERIC
                                                                             )
                           WHERE cod_contrato IN ( '||stContratos||' )';
    EXECUTE stSQLEventos;

    --DESCONTO PLANO DE SAUDE
    stSQL := ' SELECT array_to_string( publico.concatenar_array( cod_evento ), '','' ) AS eventos
                 FROM folhapagamento'||stEntidadeRh||'.tcmba_plano_saude
                WHERE cod_entidade IN ('||stEntidade||')
                  AND exercicio = '''||stExercicio||'''
             GROUP BY exercicio
                    , cod_entidade';

    OPEN crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO stEventos;
    CLOSE crCursor;
    IF stEventos IS NULL THEN
        stEventos := '0';
    END IF;

    stSQLEventos := 'CREATE TEMPORARY TABLE tmp_tcmba_descontos_plano_saude as
                          SELECT *
                            FROM tcmba.recuperaServidorValoresPorEvento(  '||inCodPeridoMovimentacao||'
                                                                        , '''||stEntidadeRh||'''
                                                                        , '''||stEventos||'''
                                                                       ) AS descontos_plano_saude
                                                                       (  cod_periodo_movimentacao  INTEGER
                                                                        , cod_contrato              INTEGER
                                                                        , valor                     NUMERIC
                                                                       )
                           WHERE cod_contrato IN ( '||stContratos||' )';
    EXECUTE stSQLEventos;

    --SALARIO LIQUIDO
    stSQLEventos := 'CREATE TEMPORARY TABLE tmp_tcmba_salario_liquido as
                          SELECT SUM(valor) AS valor
                               , cod_contrato
                               , '||inCodPeridoMovimentacao||' AS cod_periodo_movimentacao
                            FROM (
                                    SELECT CASE WHEN natureza = ''P'' THEN
                                                        SUM(valor)
                                                ELSE
                                                        SUM(valor)*(-1)
                                           END as valor
                                         , cod_contrato
                                      FROM recuperarEventosCalculados(  1
                                                                      , '||inCodPeridoMovimentacao||'
                                                                      , 0
                                                                      , 0
                                                                      , '''||stEntidadeRh||'''
                                                                      , ''''
                                                                     )
                                     WHERE natureza in (''P'', ''D'')
                                  GROUP BY natureza
                                         , cod_contrato
                                 ) AS salario_liquido
                           WHERE salario_liquido.cod_contrato IN ( '||stContratos||' )
                        GROUP BY cod_contrato';
    EXECUTE stSQLEventos;

    --CONSULTA SALARIO2; RETORNO
    stSQL := ' SELECT tmp_servidor_salario2.*
                    , COALESCE(tmp_tcmba_salario_base.valor                 , 0.00  ) AS salario_base
                    , COALESCE(tmp_tcmba_salario_vantagens.valor            , 0.00  ) AS salario_vantagens
                    , COALESCE(tmp_tcmba_salario_gratificacao.valor         , 0.00  ) AS salario_gratificacao
                    , COALESCE(tmp_tcmba_salario_familia.valor              , 0.00  ) AS salario_familia
                    , COALESCE(tmp_tcmba_salario_ferias.valor               , 0.00  ) AS salario_ferias
                    , COALESCE(tmp_tcmba_salario_horas_extra.valor          , 0.00  ) AS salario_horas_extra
                    , COALESCE(tmp_tcmba_salario_decimo.valor               , 0.00  ) AS salario_decimo
                    , COALESCE(tmp_tcmba_salario_descontos.valor            , 0.00  ) AS salario_descontos
                    , COALESCE(tmp_tcmba_descontos_irrf.valor               , 0.00  ) AS desconto_irrf
                    , COALESCE(tmp_tcmba_desconto_irrf_decimo.valor         , 0.00  ) AS desconto_irrf_decimo
                    , COALESCE(tmp_tcmba_descontos_consignado.vl_banco_1    , 0.00  ) AS desconto_consignado_1
                    , COALESCE(tmp_tcmba_descontos_consignado.cod_banco_1   , ''0'' ) AS cod_banco_1
                    , COALESCE(tmp_tcmba_descontos_consignado.vl_banco_2    , 0.00  ) AS desconto_consignado_2
                    , COALESCE(tmp_tcmba_descontos_consignado.cod_banco_2   , ''0'' ) AS cod_banco_2
                    , COALESCE(tmp_tcmba_descontos_consignado.vl_banco_3    , 0.00  ) AS desconto_consignado_3
                    , COALESCE(tmp_tcmba_descontos_consignado.cod_banco_3   , ''0'' ) AS cod_banco_3
                    , COALESCE(tmp_tcmba_descontos_previdencia.valor        , 0.00  ) AS desconto_previdencia
                    , COALESCE(tmp_tcmba_desconto_irrf_ferias.valor         , 0.00  ) AS desconto_irrf_ferias
                    , COALESCE(tmp_tcmba_descontos_previdencia_decimo.valor , 0.00  ) AS desconto_previdencia_decimo
                    , COALESCE(tmp_tcmba_descontos_previdencia_ferias.valor , 0.00  ) AS desconto_previdencia_ferias
                    , COALESCE(tmp_tcmba_descontos_pensao.valor             , 0.00  ) AS desconto_pensao
                    , COALESCE(tmp_tcmba_descontos_plano_saude.valor        , 0.00  ) AS desconto_plano_saude
                    , COALESCE(tmp_tcmba_salario_liquido.valor              , 0.00  ) AS salario_liquido

                 FROM tmp_servidor_salario2

            LEFT JOIN tmp_tcmba_salario_base
                   ON tmp_tcmba_salario_base.cod_periodo_movimentacao = tmp_servidor_salario2.cod_periodo_movimentacao
                  AND tmp_tcmba_salario_base.cod_contrato = tmp_servidor_salario2.cod_contrato

            LEFT JOIN tmp_tcmba_salario_vantagens
                   ON tmp_tcmba_salario_vantagens.cod_periodo_movimentacao = tmp_servidor_salario2.cod_periodo_movimentacao
                  AND tmp_tcmba_salario_vantagens.cod_contrato = tmp_servidor_salario2.cod_contrato

            LEFT JOIN tmp_tcmba_salario_gratificacao
                   ON tmp_tcmba_salario_gratificacao.cod_periodo_movimentacao = tmp_servidor_salario2.cod_periodo_movimentacao
                  AND tmp_tcmba_salario_gratificacao.cod_contrato = tmp_servidor_salario2.cod_contrato

            LEFT JOIN tmp_tcmba_salario_familia
                   ON tmp_tcmba_salario_familia.cod_periodo_movimentacao = tmp_servidor_salario2.cod_periodo_movimentacao
                  AND tmp_tcmba_salario_familia.cod_contrato = tmp_servidor_salario2.cod_contrato

            LEFT JOIN tmp_tcmba_salario_ferias
                   ON tmp_tcmba_salario_ferias.cod_periodo_movimentacao = tmp_servidor_salario2.cod_periodo_movimentacao
                  AND tmp_tcmba_salario_ferias.cod_contrato = tmp_servidor_salario2.cod_contrato

            LEFT JOIN tmp_tcmba_salario_horas_extra
                   ON tmp_tcmba_salario_horas_extra.cod_periodo_movimentacao = tmp_servidor_salario2.cod_periodo_movimentacao
                  AND tmp_tcmba_salario_horas_extra.cod_contrato = tmp_servidor_salario2.cod_contrato

            LEFT JOIN tmp_tcmba_salario_decimo
                   ON tmp_tcmba_salario_decimo.cod_periodo_movimentacao = tmp_servidor_salario2.cod_periodo_movimentacao
                  AND tmp_tcmba_salario_decimo.cod_contrato = tmp_servidor_salario2.cod_contrato

            LEFT JOIN tmp_tcmba_salario_descontos
                   ON tmp_tcmba_salario_descontos.cod_periodo_movimentacao = tmp_servidor_salario2.cod_periodo_movimentacao
                  AND tmp_tcmba_salario_descontos.cod_contrato = tmp_servidor_salario2.cod_contrato

            LEFT JOIN tmp_tcmba_descontos_irrf
                   ON tmp_tcmba_descontos_irrf.cod_periodo_movimentacao = tmp_servidor_salario2.cod_periodo_movimentacao
                  AND tmp_tcmba_descontos_irrf.cod_contrato = tmp_servidor_salario2.cod_contrato

            LEFT JOIN tmp_tcmba_desconto_irrf_decimo
                   ON tmp_tcmba_desconto_irrf_decimo.cod_periodo_movimentacao = tmp_servidor_salario2.cod_periodo_movimentacao
                  AND tmp_tcmba_desconto_irrf_decimo.cod_contrato = tmp_servidor_salario2.cod_contrato

            LEFT JOIN tmp_tcmba_descontos_consignado
                   ON tmp_tcmba_descontos_consignado.cod_periodo_movimentacao = tmp_servidor_salario2.cod_periodo_movimentacao
                  AND tmp_tcmba_descontos_consignado.cod_contrato = tmp_servidor_salario2.cod_contrato

            LEFT JOIN tmp_tcmba_descontos_previdencia
                   ON tmp_tcmba_descontos_previdencia.cod_periodo_movimentacao = tmp_servidor_salario2.cod_periodo_movimentacao
                  AND tmp_tcmba_descontos_previdencia.cod_contrato = tmp_servidor_salario2.cod_contrato
                  AND tmp_tcmba_descontos_previdencia.cod_previdencia = tmp_servidor_salario2.cod_previdencia

            LEFT JOIN tmp_tcmba_desconto_irrf_ferias
                   ON tmp_tcmba_desconto_irrf_ferias.cod_periodo_movimentacao = tmp_servidor_salario2.cod_periodo_movimentacao
                  AND tmp_tcmba_desconto_irrf_ferias.cod_contrato = tmp_servidor_salario2.cod_contrato

            LEFT JOIN tmp_tcmba_descontos_previdencia_decimo
                   ON tmp_tcmba_descontos_previdencia_decimo.cod_periodo_movimentacao = tmp_servidor_salario2.cod_periodo_movimentacao
                  AND tmp_tcmba_descontos_previdencia_decimo.cod_contrato = tmp_servidor_salario2.cod_contrato
                  AND tmp_tcmba_descontos_previdencia_decimo.cod_previdencia = tmp_servidor_salario2.cod_previdencia

            LEFT JOIN tmp_tcmba_descontos_previdencia_ferias
                   ON tmp_tcmba_descontos_previdencia_ferias.cod_periodo_movimentacao = tmp_servidor_salario2.cod_periodo_movimentacao
                  AND tmp_tcmba_descontos_previdencia_ferias.cod_contrato = tmp_servidor_salario2.cod_contrato
                  AND tmp_tcmba_descontos_previdencia_ferias.cod_previdencia = tmp_servidor_salario2.cod_previdencia

            LEFT JOIN tmp_tcmba_descontos_pensao
                   ON tmp_tcmba_descontos_pensao.cod_periodo_movimentacao = tmp_servidor_salario2.cod_periodo_movimentacao
                  AND tmp_tcmba_descontos_pensao.cod_contrato = tmp_servidor_salario2.cod_contrato

            LEFT JOIN tmp_tcmba_descontos_plano_saude
                   ON tmp_tcmba_descontos_plano_saude.cod_periodo_movimentacao = tmp_servidor_salario2.cod_periodo_movimentacao
                  AND tmp_tcmba_descontos_plano_saude.cod_contrato = tmp_servidor_salario2.cod_contrato

            LEFT JOIN tmp_tcmba_salario_liquido
                   ON tmp_tcmba_salario_liquido.cod_periodo_movimentacao = tmp_servidor_salario2.cod_periodo_movimentacao
                  AND tmp_tcmba_salario_liquido.cod_contrato = tmp_servidor_salario2.cod_contrato

             ORDER BY tmp_servidor_salario2.nom_cgm
                    , tmp_servidor_salario2.cod_contrato ';

    FOR reRecord IN EXECUTE stSQL
    LOOP
        RETURN NEXT reRecord;
    END LOOP;

    --DROP TABLE TMP
    DROP TABLE tmp_tcmba_salario_liquido;
    DROP TABLE tmp_tcmba_descontos_plano_saude;
    DROP TABLE tmp_tcmba_descontos_pensao;
    DROP TABLE tmp_tcmba_descontos_previdencia_ferias;
    DROP TABLE tmp_tcmba_descontos_previdencia_decimo;
    DROP TABLE tmp_tcmba_desconto_irrf_ferias;
    DROP TABLE tmp_eventos_previdencia;
    DROP TABLE tmp_tcmba_descontos_previdencia;
    DROP TABLE tmp_tcmba_descontos_consignado;
    DROP TABLE tmp_tcmba_desconto_irrf_decimo;
    DROP TABLE tmp_tcmba_descontos_irrf;
    DROP TABLE tmp_tcmba_salario_descontos;
    DROP TABLE tmp_tcmba_salario_decimo;
    DROP TABLE tmp_tcmba_salario_horas_extra;
    DROP TABLE tmp_tcmba_salario_ferias;
    DROP TABLE tmp_tcmba_salario_familia;
    DROP TABLE tmp_tcmba_salario_gratificacao;
    DROP TABLE tmp_tcmba_salario_vantagens;
    DROP TABLE tmp_tcmba_salario_base;
    DROP TABLE tmp_servidor_salario2;

END;
$$ LANGUAGE 'plpgsql';