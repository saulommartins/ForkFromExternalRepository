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
/* recuperarContratoServidor
 *
 * Data de Criação : 03/04/2009

 * @copyright CNM - Confederação Nacional de Municípios
 * @link http://www.cnm.org.br CNM

 * @author Analista : Dagiane Vieira
 * @author Desenvolvedor : Diego Lemos de Souza

 */

CREATE OR REPLACE FUNCTION recuperaAuditoriaFolha(VARCHAR,INTEGER,INTEGER,VARCHAR,VARCHAR,VARCHAR,INTEGER) RETURNS SETOF RECORD AS $$
DECLARE
    stEntidade                                  ALIAS FOR $1;
    inCodPeriodoMovimentacaoComparacao          ALIAS FOR $2;
    inCodPeriodoMovimentacaoAnalise             ALIAS FOR $3;
    stNaturezaFiltro                            ALIAS FOR $4;
    stTipoFiltro                                ALIAS FOR $5;
    stValoresFiltro                             ALIAS FOR $6;
    inCodContrato                               ALIAS FOR $7;
    reRegistro                                  RECORD;
    crCursor                                    REFCURSOR;
    reRegistroAux                               RECORD;
    reRegistroRe                                RECORD;
    stSql                                       VARCHAR := '';
    stSelect                                    VARCHAR := '';
    arNaturezaFiltro                            VARCHAR[];
    stFiltroNatureza                            VARCHAR:='';
    stAux                                       VARCHAR;
    inCount                                     INTEGER := 0;

BEGIN

-- Metodo para receber a natureza do filtro para incluisao no WHERE IN ()

IF stNaturezaFiltro <> '' THEN
    arNaturezaFiltro := string_to_array(stNaturezaFiltro, ',');
    FOREACH stAux IN ARRAY arNaturezaFiltro LOOP
        stFiltroNatureza := stFiltroNatureza || quote_literal(stAux) || ','  ;
    END LOOP;
    -- Removendo a ultima virgula
    stFiltroNatureza := SUBSTR(stFiltroNatureza, 1, LENGTH(stFiltroNatureza)-1);
END IF;

    stSql := 'CREATE TEMPORARY TABLE retorno AS
            (
                SELECT
                        registro_evento_periodo.cod_periodo_movimentacao,
                        '|| inCodPeriodoMovimentacaoComparacao ||' AS periodo_comparacao,
                        registro_evento_periodo.cod_contrato,
                        evento_calculado.valor AS valor_analise,
                        0.00 AS valor_comparacao,
                        evento.descricao,
                        evento.natureza,
                        evento.codigo,
                        evento.cod_evento,
                        CAST(COALESCE (evento_calculado.desdobramento, ''S'') as varchar) AS desdobramento,
                        CASE WHEN evento_calculado.desdobramento IS NULL
                                THEN  ''Salário''
                	        ELSE
                                    (
                                        SELECT
                                                abreviacao
                                        FROM folhapagamento.configuracao_desdobramento
                                        WHERE configuracao_desdobramento.desdobramento = evento_calculado.desdobramento
                                        AND configuracao_desdobramento.cod_configuracao = 1
                                    )
                        END AS desdobramento_2,
                        COALESCE (evento_calculado.valor, 0.00) AS dif
                FROM folhapagamento'|| stEntidade ||'.evento_calculado
        	JOIN folhapagamento'|| stEntidade ||'.registro_evento_periodo
          	  ON registro_evento_periodo.cod_registro = evento_calculado.cod_registro
         	 AND registro_evento_periodo.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacaoAnalise ||'
                 AND registro_evento_periodo.cod_contrato = ' || inCodContrato || '
        	JOIN folhapagamento'|| stEntidade ||'.evento
          	  ON evento.cod_evento = evento_calculado.cod_evento
        	JOIN folhapagamento'|| stEntidade ||'.ultimo_registro_evento
          	  ON ultimo_registro_evento.cod_registro = evento_calculado.cod_registro
         	 AND ultimo_registro_evento.timestamp = evento_calculado.timestamp_registro
         	 AND ultimo_registro_evento.cod_evento = evento_calculado.cod_evento';

    IF stNaturezaFiltro <> '' THEN
        stSql := stSql || '
               WHERE evento.natureza IN (' || stFiltroNatureza || ') ';
    END IF;

    IF stTipoFiltro = 'evento' THEN
        stSql := stSql || '
                AND evento.cod_evento IN ( ' || stValoresFiltro || ') ';
    END IF;

    stSql := stSql || '
            )';

    EXECUTE stSql;

    stSelect := 'SELECT
                        registro_evento_periodo.cod_periodo_movimentacao AS periodo_comparacao,
                        registro_evento_periodo.cod_contrato,
                        evento_calculado.valor AS valor_comparacao,
                        evento.descricao,
                        evento.natureza,
                        evento.codigo,
                        evento.cod_evento,
                        COALESCE (evento_calculado.desdobramento, ''S'') AS desdobramento,
                        CASE WHEN evento_calculado.desdobramento IS NULL
                                THEN ''Salário''
                	        ELSE
                                    (
                                        SELECT
                                                abreviacao
                                        FROM folhapagamento.configuracao_desdobramento
                                        WHERE configuracao_desdobramento.desdobramento = evento_calculado.desdobramento
                                        AND configuracao_desdobramento.cod_configuracao = 1
                                    )
                        END AS desdobramento_2
                FROM folhapagamento'|| stEntidade ||'.evento_calculado
        	JOIN folhapagamento'|| stEntidade ||'.registro_evento_periodo
          	  ON registro_evento_periodo.cod_registro = evento_calculado.cod_registro
         	 AND registro_evento_periodo.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacaoComparacao ||'
         	 AND registro_evento_periodo.cod_contrato = ' || inCodContrato || '
        	JOIN folhapagamento'|| stEntidade ||'.evento
          	  ON evento.cod_evento = evento_calculado.cod_evento
        	JOIN folhapagamento'|| stEntidade ||'.ultimo_registro_evento
          	  ON ultimo_registro_evento.cod_registro = evento_calculado.cod_registro
         	 AND ultimo_registro_evento.timestamp = evento_calculado.timestamp_registro
         	 AND ultimo_registro_evento.cod_evento = evento_calculado.cod_evento';

    IF stNaturezaFiltro <> '' THEN
        stSelect := stSelect || '
               WHERE evento.natureza IN ('|| stFiltroNatureza ||')';
    END IF;

    IF stTipoFiltro = 'evento' THEN
        stSelect := stSelect || '
                 AND evento.cod_evento IN ( ' || stValoresFiltro || ') ';
    END IF;

    OPEN crCursor FOR EXECUTE stSelect;
    LOOP
        FETCH crCursor INTO reRegistroAux;
        EXIT WHEN NOT FOUND;

        stSql := 'SELECT    cod_evento,
                            cod_contrato,
                            desdobramento,
                            valor_analise,
                            descricao,
                            natureza,
                            codigo,
                            desdobramento_2,
                            periodo_comparacao
                    FROM retorno
                    WHERE '|| reRegistroAux.cod_evento   ||' = cod_evento
                      AND '|| reRegistroAux.cod_contrato ||' = cod_contrato
                      AND '|| quote_literal(reRegistroAux.desdobramento) ||' = desdobramento';

        EXECUTE stSql INTO reRegistro;

        IF reRegistro.cod_evento IS NULL THEN
            stSql := 'INSERT INTO retorno (
                                            cod_periodo_movimentacao,
                                            periodo_comparacao,
                                            cod_evento,
                                            cod_contrato,
                                            valor_analise,
                                            valor_comparacao,
                                            descricao,
                                            natureza,
                                            codigo,
                                            desdobramento,
                                            desdobramento_2,
                                            dif
                                        ) VALUES (
                                                    '||inCodPeriodoMovimentacaoAnalise||',
                                                    '||reRegistroAux.periodo_comparacao||',
                                                    '||reRegistroAux.cod_evento||',
                                                    '||reRegistroAux.cod_contrato||',
                                                    '||0.00||',
                                                    '||reRegistroAux.valor_comparacao||',
                                                    '|| quote_literal(reRegistroAux.descricao)       ||',
                                                    '|| quote_literal(reRegistroAux.natureza)        ||',
                                                    '|| quote_literal(reRegistroAux.codigo)          ||',
                                                    '|| quote_literal(reRegistroAux.desdobramento)   ||',
                                                    '|| quote_literal(reRegistroAux.desdobramento_2) ||',
                                                    '||COALESCE ((0.00 - reRegistroAux.valor_comparacao), 0.00)||'
                                                )';

            EXECUTE stSql;
        ELSE
            stSql := 'UPDATE retorno SET
                                            periodo_comparacao = '||reRegistroAux.periodo_comparacao||',
                                            valor_comparacao = '||reRegistroAux.valor_comparacao||',
                                            dif = '||COALESCE (reRegistro.valor_analise - reRegistroAux.valor_comparacao, 0.00)||'
                        WHERE retorno.cod_contrato = ' || reRegistro.cod_contrato || '
                        AND retorno.cod_evento = ' || reRegistro.cod_evento || '
                        AND retorno.desdobramento = '|| quote_literal(reRegistro.desdobramento) ||' ' ;

            EXECUTE stSql;
        END IF;
    END LOOP;
    CLOSE crCursor;

stSql := 'SELECT cod_periodo_movimentacao, periodo_comparacao, cod_contrato, valor_analise, valor_comparacao, descricao,
                 natureza, codigo, cod_evento, desdobramento, desdobramento_2, dif FROM retorno';

--RAISE NOTICE 'Debug: %', stSql;

FOR reRegistro IN EXECUTE stSql
LOOP
    RETURN next reRegistro;
END LOOP;

DROP TABLE retorno;

RETURN;

END;

$$ LANGUAGE 'plpgsql';
