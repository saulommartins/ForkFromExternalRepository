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
/* recuperaServidorConsignado
 * Data de Criação : 26/10/2015
 * @author Analista : Dagiane Vieira
 * @author Desenvolvedor : Michel Teixeira
 * $Id: FTCMBARecuperaServidorConsignado.plsql 63946 2015-11-10 21:10:32Z michel $
*/

CREATE OR REPLACE FUNCTION tcmba.recuperaServidorConsignado(INTEGER, VARCHAR, VARCHAR, VARCHAR) RETURNS SETOF RECORD as $$
DECLARE
    inCodPeriodoMovimentacao ALIAS FOR $1;
    stEntidadeRh             ALIAS FOR $2;
    stExercicio              ALIAS FOR $3;
    inEntidade               ALIAS FOR $4;
    stSQL                    VARCHAR :='';
    stSQLTemp                VARCHAR :='';
    inCodContrato            INTEGER := 0;
    inCountBanco             INTEGER := 0;
    inBanco1                 INTEGER := 0;
    numBanco1                VARCHAR :='0';
    vlBanco1                 NUMERIC := 0.00;
    inBanco2                 INTEGER := 0;
    numBanco2                VARCHAR :='0';
    vlBanco2                 NUMERIC := 0.00;
    inBanco3                 INTEGER := 0;
    numBanco3                VARCHAR :='0';
    vlBanco3                 NUMERIC := 0.00;
    reRecord                 RECORD;
    reRecordTemp             RECORD;
BEGIN
    CREATE TEMPORARY TABLE tmp_consignado_banco_contrato_final
    (  cod_banco_1              VARCHAR
     , vl_banco_1               NUMERIC
     , cod_banco_2              VARCHAR
     , vl_banco_2               NUMERIC
     , cod_banco_3              VARCHAR
     , vl_banco_3               NUMERIC
     , cod_contrato             INTEGER
     , cod_periodo_movimentacao INTEGER
    );

    stSQL := 'CREATE TEMPORARY TABLE tmp_consignado_banco_contrato AS
                SELECT eventos.cod_periodo_movimentacao
                     , eventos.cod_contrato
                     , tcmba_emprestimo_consignado.cod_banco
                     , banco.num_banco
                     , sum(eventos.valor) as valor

                  FROM (
                         SELECT valor_evento.cod_periodo_movimentacao
                                   , valor_evento.cod_contrato
                                   , sum(valor_evento.valor) as valor
                                   , valor_evento.cod_evento
                                FROM (
                                       SELECT registro_evento_periodo.cod_periodo_movimentacao
                                            , registro_evento_periodo.cod_contrato
                                            , sum(evento_calculado.valor) as valor
                                            , ultimo_registro_evento.cod_evento
                                         FROM folhapagamento'||stEntidadeRh||'.registro_evento_periodo

                                   INNER JOIN folhapagamento'||stEntidadeRh||'.registro_evento
                                           ON registro_evento.cod_registro = registro_evento_periodo.cod_registro

                                   INNER JOIN folhapagamento'||stEntidadeRh||'.ultimo_registro_evento
                                           ON ultimo_registro_evento.cod_registro   = registro_evento.cod_registro
                                          AND ultimo_registro_evento.timestamp      = registro_evento.timestamp
                                          AND ultimo_registro_evento.cod_evento     = registro_evento.cod_evento

                                   INNER JOIN folhapagamento'||stEntidadeRh||'.evento_calculado
                                           ON evento_calculado.timestamp_registro   = ultimo_registro_evento.timestamp
                                          AND evento_calculado.cod_registro         = ultimo_registro_evento.cod_registro
                                          AND evento_calculado.cod_evento           = ultimo_registro_evento.cod_evento

                                        WHERE registro_evento_periodo.cod_periodo_movimentacao  = '||inCodPeriodoMovimentacao||'

                                     GROUP BY registro_evento_periodo.cod_periodo_movimentacao
                                            , registro_evento_periodo.cod_contrato
                                            , ultimo_registro_evento.cod_evento

                                        UNION

                                       SELECT contrato_servidor_complementar.cod_periodo_movimentacao
                                            , contrato_servidor_complementar.cod_contrato
                                            , sum(evento_complementar_calculado.valor) as valor
                                            , ultimo_registro_evento_complementar.cod_evento
                                         FROM folhapagamento'||stEntidadeRh||'.contrato_servidor_complementar

                                   INNER JOIN folhapagamento'||stEntidadeRh||'.registro_evento_complementar
                                           ON registro_evento_complementar.cod_periodo_movimentacao = contrato_servidor_complementar.cod_periodo_movimentacao
                                          AND registro_evento_complementar.cod_complementar         = contrato_servidor_complementar.cod_complementar
                                          AND registro_evento_complementar.cod_contrato             = contrato_servidor_complementar.cod_contrato

                                   INNER JOIN folhapagamento'||stEntidadeRh||'.ultimo_registro_evento_complementar
                                           ON ultimo_registro_evento_complementar.cod_registro      = registro_evento_complementar.cod_registro
                                          AND ultimo_registro_evento_complementar.timestamp         = registro_evento_complementar.timestamp
                                          AND ultimo_registro_evento_complementar.cod_evento        = registro_evento_complementar.cod_evento
                                          AND ultimo_registro_evento_complementar.cod_configuracao  = registro_evento_complementar.cod_configuracao

                                   INNER JOIN folhapagamento'||stEntidadeRh||'.evento_complementar_calculado
                                           ON evento_complementar_calculado.timestamp_registro  = ultimo_registro_evento_complementar.timestamp
                                          AND evento_complementar_calculado.cod_registro        = ultimo_registro_evento_complementar.cod_registro
                                          AND evento_complementar_calculado.cod_evento          = ultimo_registro_evento_complementar.cod_evento
                                          AND evento_complementar_calculado.cod_configuracao    = ultimo_registro_evento_complementar.cod_configuracao

                                        WHERE contrato_servidor_complementar.cod_periodo_movimentacao   = '||inCodPeriodoMovimentacao||'

                                     GROUP BY contrato_servidor_complementar.cod_periodo_movimentacao
                                            , contrato_servidor_complementar.cod_contrato
                                            , ultimo_registro_evento_complementar.cod_evento
                                     ) AS valor_evento

                             GROUP BY valor_evento.cod_periodo_movimentacao
                                    , valor_evento.cod_contrato
                                    , valor_evento.cod_evento
                       ) AS eventos
            INNER JOIN folhapagamento'||stEntidadeRh||'.tcmba_emprestimo_consignado
                    ON tcmba_emprestimo_consignado.cod_evento = eventos.cod_evento
                   AND tcmba_emprestimo_consignado.cod_entidade IN ('||inEntidade||')
                   AND tcmba_emprestimo_consignado.exercicio  = '''||stExercicio||'''

            INNER JOIN monetario.banco
                    ON banco.cod_banco = tcmba_emprestimo_consignado.cod_banco

              GROUP BY eventos.cod_periodo_movimentacao
                     , eventos.cod_contrato
                     , tcmba_emprestimo_consignado.cod_banco
                     , banco.num_banco ';
    EXECUTE stSQL;

    stSQLTemp := ' SELECT cod_periodo_movimentacao
                    , cod_contrato
                 FROM tmp_consignado_banco_contrato
             GROUP BY cod_periodo_movimentacao
                    , cod_contrato ';

    FOR reRecordTemp IN EXECUTE stSQLTemp
    LOOP

        inCountBanco    := 0;
        inBanco1        := 0;
        numBanco1       :='0';
        vlBanco1        := 0.00;
        inBanco2        := 0;
        numBanco2       :='0';
        vlBanco2        := 0.00;
        inBanco3        := 0;
        numBanco3       :='0';
        vlBanco3        := 0.00;
        inCodContrato   := reRecordTemp.cod_contrato;

        SELECT count(cod_banco)::integer
          INTO inCountBanco
          FROM tmp_consignado_banco_contrato
         WHERE tmp_consignado_banco_contrato.cod_contrato = inCodContrato
           AND tmp_consignado_banco_contrato.cod_periodo_movimentacao = inCodPeriodoMovimentacao;

        IF inCountBanco > 0 THEN

            SELECT cod_banco
                 , num_banco
              INTO inBanco1
                 , numBanco1
              FROM tmp_consignado_banco_contrato
             WHERE tmp_consignado_banco_contrato.cod_contrato = inCodContrato
               AND tmp_consignado_banco_contrato.cod_periodo_movimentacao = inCodPeriodoMovimentacao
          ORDER BY valor DESC
             LIMIT 1;

            SELECT valor
              INTO vlBanco1
              FROM tmp_consignado_banco_contrato
             WHERE cod_banco = inBanco1
               AND tmp_consignado_banco_contrato.cod_contrato = inCodContrato
               AND tmp_consignado_banco_contrato.cod_periodo_movimentacao = inCodPeriodoMovimentacao;

        END IF;

        IF inCountBanco > 1 THEN

            SELECT cod_banco
                 , num_banco
              INTO inBanco2
                 , numBanco2
              FROM tmp_consignado_banco_contrato
             WHERE cod_banco NOT IN ( inBanco1 )
               AND tmp_consignado_banco_contrato.cod_contrato = inCodContrato
               AND tmp_consignado_banco_contrato.cod_periodo_movimentacao = inCodPeriodoMovimentacao
          ORDER BY valor DESC
             LIMIT 1;

            SELECT valor
              INTO vlBanco2
              FROM tmp_consignado_banco_contrato
             WHERE cod_banco = inBanco2
               AND tmp_consignado_banco_contrato.cod_contrato = inCodContrato
               AND tmp_consignado_banco_contrato.cod_periodo_movimentacao = inCodPeriodoMovimentacao;

        END IF;

        IF inCountBanco = 3 THEN

            SELECT cod_banco
                 , num_banco
              INTO inBanco3
                 , numBanco3
              FROM tmp_consignado_banco_contrato
             WHERE cod_banco NOT IN ( inBanco1, inBanco2 )
               AND tmp_consignado_banco_contrato.cod_contrato = inCodContrato
               AND tmp_consignado_banco_contrato.cod_periodo_movimentacao = inCodPeriodoMovimentacao
          ORDER BY valor DESC
             LIMIT 1;

            SELECT valor
              INTO vlBanco3
              FROM tmp_consignado_banco_contrato
             WHERE cod_banco = inBanco3
               AND tmp_consignado_banco_contrato.cod_contrato = inCodContrato
               AND tmp_consignado_banco_contrato.cod_periodo_movimentacao = inCodPeriodoMovimentacao;

        END IF;

        IF inCountBanco > 3 THEN

            SELECT 9999 AS cod_banco
                 , '9999'::VARCHAR AS num_banco
              INTO inBanco3
                 , numBanco3
              FROM tmp_consignado_banco_contrato
             WHERE cod_banco NOT IN ( inBanco1, inBanco2 )
               AND tmp_consignado_banco_contrato.cod_contrato = inCodContrato
               AND tmp_consignado_banco_contrato.cod_periodo_movimentacao = inCodPeriodoMovimentacao
          ORDER BY valor DESC
             LIMIT 1;

            SELECT SUM(valor) AS valor
              INTO vlBanco3
              FROM tmp_consignado_banco_contrato
             WHERE cod_banco NOT IN ( inBanco1, inBanco2 )
               AND tmp_consignado_banco_contrato.cod_contrato = inCodContrato
               AND tmp_consignado_banco_contrato.cod_periodo_movimentacao = inCodPeriodoMovimentacao;

        END IF;

        stSQL := 'SELECT '''||numBanco1||'''::VARCHAR AS cod_banco_1
                       , '||vlBanco1||' AS vl_banco_1
                       , '''||numBanco2||'''::VARCHAR AS cod_banco_2
                       , '||vlBanco2||' AS vl_banco_2
                       , '''||numBanco3||'''::VARCHAR AS cod_banco_3
                       , '||vlBanco3||' AS vl_banco_3
                       , cod_periodo_movimentacao
                       , cod_contrato
                    FROM tmp_consignado_banco_contrato
                   WHERE tmp_consignado_banco_contrato.cod_contrato = '||inCodContrato||'
                     AND tmp_consignado_banco_contrato.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                GROUP BY cod_periodo_movimentacao
                       , cod_contrato ';

        FOR reRecord IN EXECUTE stSQL
        LOOP
            INSERT INTO tmp_consignado_banco_contrato_final
                 VALUES (  ''||reRecord.cod_banco_1||''
                         , reRecord.vl_banco_1
                         , ''||reRecord.cod_banco_2||''
                         , reRecord.vl_banco_2
                         , ''||reRecord.cod_banco_3||''
                         , reRecord.vl_banco_3
                         , reRecord.cod_contrato
                         , reRecord.cod_periodo_movimentacao
                        );
        END LOOP;

    END LOOP;

    stSQL := ' SELECT *
                 FROM tmp_consignado_banco_contrato_final ';

    FOR reRecord IN EXECUTE stSQL
    LOOP
        RETURN NEXT reRecord;
    END LOOP;

    DROP TABLE tmp_consignado_banco_contrato;
    DROP TABLE tmp_consignado_banco_contrato_final;
END;
$$ LANGUAGE 'plpgsql';