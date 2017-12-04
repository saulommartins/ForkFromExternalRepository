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
* Caso de uso: uc-05.04.02
*/

/*
$Log:
*/

CREATE OR REPLACE FUNCTION divida.fn_recupera_nro_parcelas_divida_lancamento( inCodLancamento   INTEGER
                                                                            ) RETURNS           INTEGER AS $$
DECLARE
    nro_parcelas        INTEGER;
BEGIN

    /* SE COTA UNICA DO LANCAMENTO ESTA PAGA, RETORNA 0 AUTOMATICAMENTE */
       PERFORM 1
          FROM arrecadacao.parcela
    INNER JOIN arrecadacao.carne
            ON carne.cod_parcela = parcela.cod_parcela
    INNER JOIN arrecadacao.pagamento
            ON pagamento.numeracao    = carne.numeracao
           AND pagamento.cod_convenio = carne.cod_convenio
    INNER JOIN arrecadacao.tipo_pagamento
            ON tipo_pagamento.cod_tipo  = pagamento.cod_tipo
           AND tipo_pagamento.pagamento = TRUE
           AND tipo_pagamento.cod_tipo != 5
         WHERE parcela.cod_lancamento = inCodLancamento
           AND parcela.nr_parcela = 0
             ;

    IF FOUND THEN

        nro_parcelas := 0;

    ELSE

        /* SE O LANCAMENTO SÓ CONTIVER PARCELA ÚNICA, RETORNA 1 */
        PERFORM 1
           FROM arrecadacao.parcela
          WHERE parcela.cod_lancamento = inCodLancamento
            AND parcela.nr_parcela != 0
              ;

        IF NOT FOUND THEN

            nro_parcelas := 1;

        ELSE

            /* SE LANCAMENTO CONTEM PARCELAS NORMAIS, RETORNA QUANTIDADE DE PARCELAS NORMAIS NAO PAGAS */
            SELECT COUNT(*)
              INTO nro_parcelas
              FROM (
                         SELECT COUNT(parcela.cod_parcela)
                           FROM arrecadacao.parcela
                     INNER JOIN arrecadacao.carne
                             ON carne.cod_parcela = parcela.cod_parcela
                      LEFT JOIN arrecadacao.pagamento
                             ON pagamento.numeracao    = carne.numeracao
                            AND pagamento.cod_convenio = carne.cod_convenio
                      LEFT JOIN arrecadacao.tipo_pagamento
                             ON tipo_pagamento.cod_tipo  = pagamento.cod_tipo
                            AND tipo_pagamento.pagamento = TRUE
                            AND tipo_pagamento.cod_tipo != 5
                          WHERE parcela.cod_lancamento = inCodLancamento
                            AND parcela.nr_parcela    != 0
                            AND pagamento.numeracao   IS NULL
                       GROUP BY parcela.cod_parcela
                   ) AS conta;

        END IF;

    END IF;

    RETURN nro_parcelas;

END;
$$ LANGUAGE 'plpgsql';
