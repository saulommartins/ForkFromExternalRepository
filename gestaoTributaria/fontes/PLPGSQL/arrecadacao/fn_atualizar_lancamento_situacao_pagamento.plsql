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
* $Id: $
*
* Caso de uso: uc-05.03.05
*/

CREATE OR REPLACE FUNCTION fn_atualizar_lancamento_situacao_pagamento()  RETURNS TRIGGER AS $$
DECLARE
    inCodLancamento     integer;
    inNroParcela        integer;
    inTotalPago         integer;
    inTotalAberto       integer;
    boPagamento         boolean;

BEGIN
    SELECT
        tipo_pagamento.pagamento
    INTO
        boPagamento
    FROM
        arrecadacao.tipo_pagamento
    WHERE
        tipo_pagamento.cod_tipo = new.cod_tipo;

    SELECT DISTINCT
        parcela.cod_lancamento,
        parcela.nr_parcela
    INTO
        inCodLancamento,
        inNroParcela
    FROM
        arrecadacao.carne
    INNER JOIN
        arrecadacao.parcela
    ON
        parcela.cod_parcela = carne.cod_parcela
    WHERE
        carne.numeracao = new.numeracao;

    IF ( boPagamento = false ) THEN --cancelamento
        UPDATE 
            arrecadacao.lancamento 
        SET
            situacao = 'C'
        WHERE
            lancamento.cod_lancamento = inCodLancamento;
    ELSE
        IF ( inNroParcela = 0 ) THEN --parcela unica paga
            UPDATE 
                arrecadacao.lancamento 
            SET 
                situacao = 'P'
            WHERE
                lancamento.cod_lancamento = inCodLancamento;
        ELSE
            SELECT
                count(parcela.cod_parcela)
            INTO
                inTotalAberto
            FROM
                arrecadacao.parcela
            INNER JOIN
                arrecadacao.carne
            ON
                carne.cod_parcela = parcela.cod_parcela
            LEFT JOIN
                arrecadacao.carne_devolucao
            ON
                carne_devolucao.numeracao = carne.numeracao
            WHERE
                parcela.nr_parcela <> 0
                AND carne_devolucao.numeracao IS NULL
                AND parcela.cod_lancamento = inCodLancamento;

            SELECT
                count(parcela.cod_parcela)
            INTO
                inTotalPago
            FROM
                arrecadacao.parcela
            INNER JOIN
                arrecadacao.carne
            ON
                carne.cod_parcela = parcela.cod_parcela
            INNER JOIN
                arrecadacao.pagamento
            ON
                pagamento.numeracao = carne.numeracao
            INNER JOIN
                arrecadacao.tipo_pagamento
            ON
                tipo_pagamento.cod_tipo = pagamento.cod_tipo
                AND tipo_pagamento.pagamento = true
            WHERE
                parcela.nr_parcela <> 0
                AND parcela.cod_lancamento = inCodLancamento;

            IF ( inTotalAberto = inTotalPago ) THEN
                UPDATE
                    arrecadacao.lancamento 
                SET
                    situacao = 'P'
                WHERE
                    lancamento.cod_lancamento = inCodLancamento;
            END IF;
        END IF;
    END IF;

    Return new;

END;
$$ LANGUAGE 'plpgsql';
