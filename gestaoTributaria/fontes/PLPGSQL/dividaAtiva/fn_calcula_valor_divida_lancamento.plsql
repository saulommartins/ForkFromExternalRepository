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
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: fn_calcula_valor_divida_lancamento.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.04.02
*/

/*
$Log:
*/

CREATE OR REPLACE FUNCTION divida.fn_calcula_valor_divida_lancamento( inCodLancamento   INTEGER
                                                                    , dtDataBase        DATE
                                                                    ) RETURNS           NUMERIC AS $$
DECLARE
    valor       FLOAT;
BEGIN

    /* Buscamos parcela unica, caso só exista ela */
    PERFORM 1
       FROM arrecadacao.parcela
      WHERE parcela.cod_lancamento = inCodLancamento
        AND nr_parcela != 0
          ;

    IF NOT FOUND THEN
        /* SE O LANCAMENTO SÓ CONTIVER ESSA PARCELA ÚNICA, UTILIZA O VALOR DELA */
           SELECT COALESCE(parcela_reemissao.valor, parcela.valor) AS valor_parcela
             INTO valor
             FROM arrecadacao.parcela
        LEFT JOIN arrecadacao.parcela_reemissao
               ON parcela_reemissao.cod_parcela = parcela.cod_parcela
        LEFT JOIN (
                      SELECT MIN(timestamp) AS timestamp
                           , parcela_reemissao.cod_parcela
                        FROM arrecadacao.parcela_reemissao
                        JOIN arrecadacao.parcela
                          ON parcela.cod_parcela = parcela_reemissao.cod_parcela
                       WHERE parcela.cod_lancamento = inCodLancamento
                    GROUP BY parcela_reemissao.cod_parcela
                  ) AS min_reemissao
               ON min_reemissao.cod_parcela = parcela_reemissao.cod_parcela
              AND min_reemissao.timestamp   = parcela_reemissao.timestamp
       INNER JOIN arrecadacao.carne
               ON carne.cod_parcela = parcela.cod_parcela
        LEFT JOIN arrecadacao.carne_devolucao
               ON carne_devolucao.numeracao    = carne.numeracao
              AND carne_devolucao.cod_convenio = carne.cod_convenio
        LEFT JOIN (
                        SELECT parcela.cod_parcela
                          FROM arrecadacao.parcela
                    INNER JOIN arrecadacao.carne
                            ON carne.cod_parcela = parcela.cod_parcela
                    INNER JOIN arrecadacao.pagamento
                            ON pagamento.numeracao    = carne.numeracao
                           AND pagamento.cod_convenio = carne.cod_convenio
                         WHERE parcela.cod_lancamento        = inCodLancamento
                  ) AS pagamento
               ON pagamento.cod_parcela = parcela.cod_parcela
            WHERE parcela.cod_lancamento        = inCodLancamento
              AND parcela.nr_parcela            = 0
              AND pagamento.cod_parcela        IS NULL
              AND carne_devolucao.numeracao    IS NULL
              AND carne_devolucao.cod_convenio IS NULL
                ;
    ELSE
        SELECT COALESCE(SUM(tabela.valor_parcela), 0.00)::NUMERIC
          INTO valor
          FROM (
                     SELECT parcela.cod_parcela
                          , COALESCE(parcela_reemissao.valor, parcela.valor) AS valor_parcela
                       FROM arrecadacao.parcela
                  LEFT JOIN arrecadacao.parcela_reemissao
                         ON parcela_reemissao.cod_parcela = parcela.cod_parcela
                  LEFT JOIN (
                                SELECT MIN(timestamp) AS timestamp
                                     , parcela_reemissao.cod_parcela
                                  FROM arrecadacao.parcela_reemissao
                                  JOIN arrecadacao.parcela
                                    ON parcela.cod_parcela = parcela_reemissao.cod_parcela
                              WHERE parcela.cod_lancamento = inCodLancamento
                              GROUP BY parcela_reemissao.cod_parcela
                            ) AS min_reemissao
                         ON min_reemissao.cod_parcela = parcela_reemissao.cod_parcela
                        AND min_reemissao.timestamp   = parcela_reemissao.timestamp
                 INNER JOIN arrecadacao.carne
                         ON carne.cod_parcela = parcela.cod_parcela
                  LEFT JOIN arrecadacao.carne_devolucao
                         ON carne_devolucao.numeracao    = carne.numeracao
                        AND carne_devolucao.cod_convenio = carne.cod_convenio
                  LEFT JOIN (
                                  SELECT parcela.cod_parcela
                                    FROM arrecadacao.parcela
                              INNER JOIN arrecadacao.carne
                                      ON carne.cod_parcela = parcela.cod_parcela
                              INNER JOIN arrecadacao.pagamento
                                      ON pagamento.numeracao    = carne.numeracao
                                     AND pagamento.cod_convenio = carne.cod_convenio
                                   WHERE parcela.cod_lancamento        = inCodLancamento
                            ) AS pagamento
                         ON pagamento.cod_parcela = parcela.cod_parcela
                      WHERE parcela.cod_lancamento        = inCodLancamento
                        AND parcela.nr_parcela            > 0
                        AND pagamento.cod_parcela        IS NULL
                        AND carne_devolucao.numeracao    IS NULL
                        AND carne_devolucao.cod_convenio IS NULL
                   GROUP BY parcela.cod_parcela
                          , parcela_reemissao.valor
                          , parcela.valor
               ) AS tabela
             ;
    END IF;

    RETURN valor::NUMERIC(14,2);

END;
$$ LANGUAGE 'plpgsql';
