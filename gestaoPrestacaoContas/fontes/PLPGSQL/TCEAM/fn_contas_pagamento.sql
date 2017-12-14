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

CREATE OR REPLACE FUNCTION fn_contas_pagamento( stCodEntidade   VARCHAR
                                              , stExercicio     VARCHAR
                                              ) RETURNS         SETOF tp_contas_pagamento AS $$
DECLARE
    stSQL       VARCHAR;
    crCursor    REFCURSOR;
    reRecord    RECORD;
    inCount     INTEGER := 0;
    inEmpenho   INTEGER := 0;
BEGIN

    stSQL := '     SELECT plano_banco.cod_plano
                        , plano_banco.cod_entidade      AS cod_entidade_plano
                        , nota_liquidacao.cod_empenho
                        , nota_liquidacao.cod_entidade
                        , nota_liquidacao.exercicio
                        , banco.num_banco               AS banco
                        , agencia.num_agencia           AS agencia
                        , plano_banco.conta_corrente    AS conta
                        , 0 AS row_number
                     FROM contabilidade.plano_banco
               INNER JOIN empenho.nota_liquidacao_conta_pagadora
                       ON nota_liquidacao_conta_pagadora.cod_plano = plano_banco.cod_plano
                      AND nota_liquidacao_conta_pagadora.exercicio = plano_banco.exercicio
               INNER JOIN empenho.nota_liquidacao_paga
                       ON nota_liquidacao_paga.cod_nota     = nota_liquidacao_conta_pagadora.cod_nota
                      AND nota_liquidacao_paga.cod_entidade = nota_liquidacao_conta_pagadora.cod_entidade
                      AND nota_liquidacao_paga.exercicio    = nota_liquidacao_conta_pagadora.exercicio_liquidacao
                      AND nota_liquidacao_paga.timestamp    = nota_liquidacao_conta_pagadora.timestamp
               INNER JOIN empenho.nota_liquidacao
                       ON nota_liquidacao.cod_nota     = nota_liquidacao_paga.cod_nota
                      AND nota_liquidacao.cod_entidade = nota_liquidacao_paga.cod_entidade
                      AND nota_liquidacao.exercicio    = nota_liquidacao_paga.exercicio
               INNER JOIN monetario.banco
                       ON banco.cod_banco = plano_banco.cod_banco
               INNER JOIN monetario.agencia
                       ON agencia.cod_agencia = plano_banco.cod_agencia
                      AND agencia.cod_banco   = banco.cod_banco
                    WHERE nota_liquidacao.cod_entidade IN ('|| stCodEntidade ||')
                      AND nota_liquidacao.exercicio    = '|| quote_literal(stExercicio) ||'
                 GROUP BY plano_banco.cod_plano
                        , plano_banco.cod_entidade
                        , nota_liquidacao.cod_empenho
                        , nota_liquidacao.cod_entidade
                        , nota_liquidacao.exercicio
                        , banco.num_banco
                        , agencia.num_agencia
                        , plano_banco.conta_corrente
                        , row_number
                 ORDER BY nota_liquidacao.cod_empenho
                        , plano_banco.cod_plano
                        ;
             ';
    OPEN crCursor FOR EXECUTE stSQL;
    LOOP
        FETCH crCursor INTO reRecord;
        EXIT WHEN NOT FOUND;

        IF reRecord.cod_empenho != inEmpenho THEN
            inEmpenho := reRecord.cod_empenho;
            inCount := 0;
        END IF;

        inCount := inCount + 1;
        reRecord.row_number := inCount;

        RETURN NEXT reRecord;

    END LOOP;
    CLOSE crCursor;
    RETURN;

END;
$$ LANGUAGE 'plpgsql';

