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
CREATE OR REPLACE FUNCTION buscaParcelasPagasOrigem ( integer, varchar, varchar ) RETURNS NUMERIC AS $$

    DECLARE
        inCodLancamento ALIAS FOR $1;
        stDataINI       ALIAS FOR $2;
        stDataFIM       ALIAS FOR $3;
        stLaco3         VARCHAR;
        stLaco2         VARCHAR;
        stLaco          VARCHAR;
        reRecord         RECORD;
        reRecord2        RECORD;
        reRecord3        RECORD;
        codpar          INTEGER;
        valor           NUMERIC;
        valortotal      NUMERIC;

    BEGIN
    
        valortotal := 0.00;

        SELECT
            sum(parcela.valor) INTO valor

        FROM
            arrecadacao.carne

        LEFT JOIN
            arrecadacao.carne_devolucao
        ON
            carne_devolucao.numeracao = carne.numeracao

        LEFT JOIN
            arrecadacao.pagamento
        ON
            carne.numeracao = pagamento.numeracao
	LEFT JOIN
		arrecadacao.parcela
        ON parcela.cod_parcela = carne.cod_parcela
        WHERE
            pagamento.numeracao IS NOT NULL AND
            carne_devolucao IS NULL AND
            carne.cod_parcela = (
                SELECT
                    cod_parcela
                FROM
                    arrecadacao.parcela
                WHERE
                    parcela.vencimento BETWEEN stDataINI AND stDataFIM
                    AND parcela.nr_parcela = 0
                    AND parcela.cod_lancamento = inCodLancamento
                LIMIT 1
            );

        IF valor IS NOT NULL THEN

            valortotal := valortotal + valor;
        ELSE
            stLaco2 := '
                SELECT
                    cod_parcela
                FROM
                    arrecadacao.parcela
                WHERE
                    parcela.vencimento BETWEEN '''||stDataINI||''' AND '''||stDataFIM||'''
                    AND parcela.nr_parcela > 0
                    AND parcela.cod_lancamento = '||inCodLancamento;

            FOR reRecord2 IN EXECUTE stLaco2 LOOP
                SELECT
                    sum( parcela.valor ) INTO valor

                FROM
                    arrecadacao.carne

                LEFT JOIN
                    arrecadacao.carne_devolucao
                ON
                    carne_devolucao.numeracao = carne.numeracao

                LEFT JOIN
                    arrecadacao.pagamento
                ON
                    carne.numeracao = pagamento.numeracao
		LEFT JOIN arrecadacao.parcela
   		  ON parcela.cod_parcela  = carne.cod_parcela
                WHERE
                    pagamento.numeracao IS NOT NULL AND
                    carne_devolucao IS NULL AND
                    carne.cod_parcela = reRecord2.cod_parcela;

                IF valor IS NOT NULL THEN

                    valortotal := valortotal + valor;
                END IF;

            END LOOP;

        END IF;

        RETURN valortotal;

    END;
$$ LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION buscaParcelasPagas ( integer, varchar, varchar ) RETURNS NUMERIC AS $$

    DECLARE
        inCodLancamento ALIAS FOR $1;
        stDataINI       ALIAS FOR $2;
        stDataFIM       ALIAS FOR $3;
        stLaco3         VARCHAR;
        stLaco2         VARCHAR;
        stLaco          VARCHAR;
        reRecord         RECORD;
        reRecord2        RECORD;
        reRecord3        RECORD;
        codpar          INTEGER;
        valor           NUMERIC;
        valortotal      NUMERIC;

    BEGIN
    
        valortotal := 0.00;

        SELECT
            sum(pagamento.valor) INTO valor

        FROM
            arrecadacao.carne

        LEFT JOIN
            arrecadacao.carne_devolucao
        ON
            carne_devolucao.numeracao = carne.numeracao

        LEFT JOIN
            arrecadacao.pagamento
        ON
            carne.numeracao = pagamento.numeracao
        WHERE
            pagamento.numeracao IS NOT NULL AND
            carne_devolucao IS NULL AND
            carne.cod_parcela = (
                SELECT
                    cod_parcela
                FROM
                    arrecadacao.parcela
                WHERE
                    parcela.vencimento BETWEEN stDataINI AND stDataFIM
                    AND parcela.nr_parcela = 0
                    AND parcela.cod_lancamento = inCodLancamento
                LIMIT 1
            );

        IF valor IS NOT NULL THEN

            valortotal := valortotal + valor;
        ELSE
            stLaco2 := '
                SELECT
                    cod_parcela
                FROM
                    arrecadacao.parcela
                WHERE
                    parcela.vencimento BETWEEN '''||stDataINI||''' AND '''||stDataFIM||'''
                    AND parcela.nr_parcela > 0
                    AND parcela.cod_lancamento = '||inCodLancamento;

            FOR reRecord2 IN EXECUTE stLaco2 LOOP
                SELECT
                    sum( pagamento.valor ) INTO valor

                FROM
                    arrecadacao.carne

                LEFT JOIN
                    arrecadacao.carne_devolucao
                ON
                    carne_devolucao.numeracao = carne.numeracao

                LEFT JOIN
                    arrecadacao.pagamento
                ON
                    carne.numeracao = pagamento.numeracao
                WHERE
                    pagamento.numeracao IS NOT NULL AND
                    carne_devolucao IS NULL AND
                    carne.cod_parcela = reRecord2.cod_parcela;

                IF valor IS NOT NULL THEN

                    valortotal := valortotal + valor;
                END IF;

            END LOOP;

        END IF;

        RETURN valortotal;

    END;
$$ LANGUAGE 'plpgsql';
