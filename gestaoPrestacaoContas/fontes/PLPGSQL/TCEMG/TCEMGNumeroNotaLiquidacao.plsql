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
--
CREATE OR REPLACE FUNCTION TCEMG.numero_nota_liquidacao( varExercicio           VARCHAR
                                                       , intCodEntidade         INTEGER
                                                       , intCodNota             INTEGER
                                                       , varExercicioEmpenho    VARCHAR
                                                       , intCodEmpenho          INTEGER
                                                       ) RETURNS                INTEGER AS $$
DECLARE
    recRegistro         RECORD;
    intRetorno          INTEGER;
BEGIN
    ALTER SEQUENCE tcemg.seqLiquidacao RESTART WITH 1;

    FOR recRegistro
        IN SELECT cod_nota
             FROM empenho.nota_liquidacao
            WHERE nota_liquidacao.exercicio_empenho = varExercicioEmpenho
              AND nota_liquidacao.cod_empenho = intCodEmpenho
              AND nota_liquidacao.cod_entidade = intCodEntidade
         ORDER BY nota_liquidacao.exercicio, nota_liquidacao.cod_entidade, nota_liquidacao.cod_nota
    LOOP
        intRetorno := nextval('tcemg.seqLiquidacao');
        IF recRegistro.cod_nota = intCodNota
            THEN EXIT;
        END IF;
    END LOOP;

    RETURN intRetorno;

   END;

$$ LANGUAGE 'plpgsql' SECURITY DEFINER;
