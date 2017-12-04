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
 * diasUteisNoMes
 * Data de Criação   : 23/12/2009


 * @author Analista      Dagiane Vieira
 * @author Desenvolvedor Diego Mancilha

 * @package URBEM
 * @subpackage Calendario

    $Id:$
 */

CREATE OR REPLACE FUNCTION diasUteisNoMes(VARCHAR, VARCHAR, VARCHAR, INTEGER, INTEGER) RETURNS INTEGER AS $$
DECLARE
    stCodEntidade               ALIAS FOR $1;
    stNumeroMes                 ALIAS FOR $2;
    stExercicio                 ALIAS FOR $3;
    inCodCalendario             ALIAS FOR $4; -- Se passado nulo, não considera feriados/calendário
    inCodGrade                  ALIAS FOR $5; -- Se passado nulo considera de segunda a sexta como dias úteis.
    stSql                       VARCHAR := '';
    stDias                      VARCHAR := '';
    inQtdDias                   INTEGER := 0;
    inQtdFeriadosFixo           INTEGER := 0;
    inQtdFeriadosVariaveis      INTEGER := 0;
    inQtdDiasTrabalho           INTEGER := 0;
    boVerificaCalendario        BOOLEAN := true;
    reRegistro                  RECORD;
BEGIN

    IF inCodCalendario IS NULL THEN
        boVerificaCalendario := false;
    END IF;

    SELECT EXTRACT(DAY FROM LAST_DAY(TO_DATE('01/'||stNumeroMes||'/'||stExercicio,'DD/MM/YYYY'))) AS dias_mes INTO reRegistro;
    inQtdDias := reRegistro.dias_mes;

    IF inCodGrade IS NOT NULL THEN
        stSql := 'SELECT DISTINCT cod_dia-1 AS dia
                    FROM pessoal'||stCodEntidade||'.faixa_turno
                   WHERE cod_grade = '||inCodGrade||'
                     AND timestamp = (
                                      SELECT MAX(timestamp)
                                        FROM pessoal'||stCodEntidade||'.faixa_turno
                                       WHERE cod_grade = '||inCodGrade||'
                                     )';

        FOR reRegistro IN EXECUTE stSql LOOP
            IF reRegistro.dia < 7 THEN
                stDias := stDias || ', ' ||reRegistro.dia;
            END IF;

            IF reRegistro.dia = 7 THEN
                -- O dia 7 é feriado, logo estou fazendo que não sejam levados em conta os feriados.
                boVerificaCalendario := false;
            END IF;

        END LOOP;
        stDias := regexp_replace(stDias, '^, ', '');
    ELSE
        -- Se não passar uma grade será considerado como dias úteis de segunda a sexta.
        stDias := '1, 2, 3, 4, 5';
    END IF;

    IF boVerificaCalendario THEN
        -- Feriados Fixos
        stSql := 'SELECT COUNT(*) AS qtd_feriados_fixos
                    FROM calendario'||stCodEntidade||'.feriado
                   WHERE EXTRACT(MONTH FROM dt_feriado) = '||stNumeroMes||'
                     AND EXTRACT(DOW FROM DATE_TRUNC(''DAY'',dt_feriado)) IN ('||stDias||')
                     AND tipoferiado = ''F''';

        inQtdFeriadosFixo := selectintointeger(stSql);

        -- Feriados Variaveis
        stSql := 'SELECT COUNT(*) AS qtd_feriados_variaveis
                    FROM calendario'||stCodEntidade||'.feriado
              INNER JOIN calendario'||stCodEntidade||'.calendario_feriado_variavel
                      ON feriado.cod_feriado = calendario_feriado_variavel.cod_feriado
                   WHERE calendario_feriado_variavel.cod_calendar = '||inCodCalendario||'
                     AND EXTRACT(MONTH FROM dt_feriado)           = '||stNumeroMes||'
                     AND EXTRACT(YEAR FROM dt_feriado)            = '||stExercicio||'
                     AND EXTRACT(DOW FROM DATE_TRUNC(''DAY'',dt_feriado)) IN ('||stDias||')
                     AND tipoferiado = ''V''';

        inQtdFeriadosVariaveis := selectintointeger(stSql);
    END IF;

    stSql = 'SELECT count(*) as qtd_dias_trabalho
               FROM generate_series(0,('||inQtdDias::varchar||'-1)) as soma(dia)
              WHERE EXTRACT(DOW FROM TO_DATE(''01/'||stNumeroMes||'/'||stExercicio||''',''DD/MM/YYYY'') + soma.dia) IN ('||stDias||')';

    inQtdDiasTrabalho := selectintointeger(stSql);

    inQtdDiasTrabalho := inQtdDiasTrabalho - inQtdFeriadosFixo - inQtdFeriadosVariaveis;
    RETURN inQtdDiasTrabalho;
END;
$$
LANGUAGE 'plpgsql';
