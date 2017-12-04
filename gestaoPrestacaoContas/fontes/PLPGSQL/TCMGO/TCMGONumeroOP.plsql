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
* Script de DDL e DML
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*/

--
-- Função que atribui sequencial para uma nota_liquidacao
--drop function tcmgo.numero_op ( character varying, integer, integer, integer, character varying, timestamp without time zone)
--DROP FUNCTION TCMGO.numero_op(VARCHAR,INTEGER, INTEGER, INTEGER, INTEGER, VARCHAR, TIMESTAMP );
CREATE OR REPLACE FUNCTION TCMGO.numero_op( varExercicio              VARCHAR
											, intCodEntidade          INTEGER
											, intCodOrdem             INTEGER
										    , intCodNota              INTEGER
											, varExercicioLiquidacao  VARCHAR
                                            , stTimestastamp          TIMESTAMP
                                          ) RETURNS                INTEGER AS $$
DECLARE
    recRegistro         RECORD;
    intRetorno          INTEGER;
BEGIN
    ALTER SEQUENCE tcmgo.seqnroop RESTART WITH 1;

    FOR recRegistro
        IN SELECT pagamento_liquidacao_nota_liquidacao_paga.timestamp
             FROM empenho.pagamento_liquidacao_nota_liquidacao_paga
       INNER JOIN empenho.nota_liquidacao_paga_anulada
               ON nota_liquidacao_paga_anulada.exercicio = pagamento_liquidacao_nota_liquidacao_paga.exercicio
              AND nota_liquidacao_paga_anulada.cod_nota = pagamento_liquidacao_nota_liquidacao_paga.cod_nota
              AND nota_liquidacao_paga_anulada.cod_entidade = pagamento_liquidacao_nota_liquidacao_paga.cod_entidade
              AND nota_liquidacao_paga_anulada.timestamp = pagamento_liquidacao_nota_liquidacao_paga.timestamp
            WHERE pagamento_liquidacao_nota_liquidacao_paga.exercicio_liquidacao = varExercicioLiquidacao
              AND pagamento_liquidacao_nota_liquidacao_paga.cod_nota = intCodNota
              AND pagamento_liquidacao_nota_liquidacao_paga.cod_entidade = intCodEntidade
              AND pagamento_liquidacao_nota_liquidacao_paga.cod_ordem = intCodOrdem
              AND pagamento_liquidacao_nota_liquidacao_paga.exercicio = varExercicio
              AND pagamento_liquidacao_nota_liquidacao_paga.timestamp = stTimestastamp
         ORDER BY pagamento_liquidacao_nota_liquidacao_paga.exercicio
                , pagamento_liquidacao_nota_liquidacao_paga.cod_entidade
                , pagamento_liquidacao_nota_liquidacao_paga.cod_nota
                , pagamento_liquidacao_nota_liquidacao_paga.cod_ordem
                , pagamento_liquidacao_nota_liquidacao_paga.exercicio_liquidacao
                , pagamento_liquidacao_nota_liquidacao_paga.timestamp
            
    LOOP
        intRetorno := nextval('tcmgo.seqnroop');
        IF recRegistro.timestamp = stTimestastamp
            THEN EXIT;
        END IF;
    END LOOP;

    RETURN intRetorno;

   END;

$$ LANGUAGE 'plpgsql' SECURITY DEFINER;
