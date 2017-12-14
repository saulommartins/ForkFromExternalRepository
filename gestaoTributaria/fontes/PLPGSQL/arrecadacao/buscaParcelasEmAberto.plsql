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
CREATE OR REPLACE FUNCTION buscaParcelasEmAberto(integer, varchar, varchar, boolean ) RETURNS NUMERIC AS $$

    DECLARE
        inCodLancamento ALIAS FOR $1;
        stDataINI       ALIAS FOR $2;
        stDataFIM       ALIAS FOR $3;
        boVencida       ALIAS FOR $4;
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
            carne.cod_parcela INTO codpar

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
            pagamento.numeracao IS NULL AND
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

        IF codpar IS NULL THEN --nem todo lancamento tem parcela zero
            SELECT
                cod_parcela INTO codpar
            FROM
                arrecadacao.parcela
            WHERE
                parcela.vencimento BETWEEN stDataINI AND stDataFIM
                AND parcela.nr_parcela = 0
                AND parcela.cod_lancamento = inCodLancamento;
            IF codpar IS NULL THEN
                codpar := 1;
--            ELSE
  --              codpar := NULL;
            END IF;
        END IF;

        IF codpar IS NOT NULL THEN
            IF boVencida = true THEN
                stLaco2 := '
                    SELECT
                        cod_parcela
                    FROM
                        arrecadacao.parcela
                    WHERE
                        now()::date > parcela.vencimento
                        AND parcela.vencimento BETWEEN '''||stDataINI||''' AND '''||stDataFIM||'''
                        AND parcela.nr_parcela > 0
                        AND parcela.cod_lancamento = '||inCodLancamento;
            ELSE
                stLaco2 := '
                    SELECT
                        cod_parcela
                    FROM
                        arrecadacao.parcela
                    WHERE
                        now()::date <= parcela.vencimento
                        AND parcela.vencimento BETWEEN '''||stDataINI||''' AND '''||stDataFIM||'''
                        AND parcela.nr_parcela > 0
                        AND parcela.cod_lancamento = '||inCodLancamento;
            END IF;

            FOR reRecord2 IN EXECUTE stLaco2 LOOP
                stLaco3 := '
                    SELECT DISTINCT
                        carne.cod_parcela

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
                        pagamento.numeracao IS NULL AND
                        carne_devolucao IS NULL AND
                        carne.cod_parcela = '|| reRecord2.cod_parcela;


                FOR reRecord3 IN EXECUTE stLaco3 LOOP

                    SELECT
                        parcela.valor into valor

                    FROM
                        arrecadacao.parcela

                    WHERE
                        parcela.cod_parcela = reRecord3.cod_parcela;

                    IF valor IS NOT NULL THEN
                        valortotal := valortotal + valor;
                    END IF;

                END LOOP;

            END LOOP;

        END IF;

        RETURN valortotal;

    END;
$$ LANGUAGE 'plpgsql';
