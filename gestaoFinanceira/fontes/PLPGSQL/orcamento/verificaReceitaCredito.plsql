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
* Script de função PLPGSQL
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
 
 $Id: $

*/

CREATE OR REPLACE FUNCTION orcamento.fn_verifica_receita_credito( VARCHAR, VARCHAR ) RETURNS BOOLEAN AS $$
DECLARE 

    stExercicio         ALIAS FOR $1;
    stNumeracao         ALIAS FOR $2;
    stSql               VARCHAR := '';
    boVinculado         BOOLEAN := TRUE;
    inCredito      INTEGER;
    inAcrescimo    INTEGER;

BEGIN

    -----------------------------------------------------------
    -- Verifica se todos os creditos do carne estao vinculados
    -----------------------------------------------------------
    SELECT COUNT(1)
      INTO inCredito 
      FROM arrecadacao.carne 
INNER JOIN arrecadacao.parcela
        ON parcela.cod_parcela = carne.cod_parcela
INNER JOIN arrecadacao.lancamento
        ON lancamento.cod_lancamento = parcela.cod_lancamento
INNER JOIN arrecadacao.lancamento_calculo
        ON lancamento_calculo.cod_lancamento = lancamento.cod_lancamento
INNER JOIN arrecadacao.calculo
        ON calculo.cod_calculo = lancamento_calculo.cod_calculo
     WHERE carne.exercicio = ''||stExercicio||''
       AND carne.numeracao = ''||stNumeracao||''
       AND NOT EXISTS ( SELECT 1
                          FROM orcamento.receita_credito

                    INNER JOIN orcamento.receita_credito_desconto
                            ON receita_credito_desconto.cod_credito  = receita_credito.cod_credito
                           AND receita_credito_desconto.cod_especie  = receita_credito.cod_especie
                           AND receita_credito_desconto.cod_genero   = receita_credito.cod_genero
                           AND receita_credito_desconto.cod_natureza = receita_credito.cod_natureza
                           AND receita_credito_desconto.exercicio    = receita_credito.exercicio
                           AND receita_credito_desconto.cod_receita  = receita_credito.cod_receita
                           AND receita_credito_desconto.divida_ativa = receita_credito.divida_ativa

                         WHERE receita_credito.cod_credito  = calculo.cod_credito
                           AND receita_credito.cod_especie  = calculo.cod_especie
                           AND receita_credito.cod_genero   = calculo.cod_genero
                           AND receita_credito.cod_natureza = calculo.cod_natureza
                           AND receita_credito.exercicio    = stExercicio
                           AND receita_credito.divida_ativa = lancamento.divida
                      );

    -------------------------------------------------------------
    -- Verifica se todos os acrescimos do carne estao vinculados
    -------------------------------------------------------------                   
    SELECT COUNT(1)
      INTO inAcrescimo
      FROM arrecadacao.carne 
INNER JOIN arrecadacao.parcela
        ON parcela.cod_parcela = carne.cod_parcela
INNER JOIN arrecadacao.lancamento
        ON lancamento.cod_lancamento = parcela.cod_lancamento
INNER JOIN arrecadacao.lancamento_calculo
        ON lancamento_calculo.cod_lancamento = lancamento.cod_lancamento
INNER JOIN arrecadacao.calculo
        ON calculo.cod_calculo = lancamento_calculo.cod_calculo
INNER JOIN monetario.credito_acrescimo
        ON credito_acrescimo.cod_credito  = calculo.cod_credito
       AND credito_acrescimo.cod_especie  = calculo.cod_especie
       AND credito_acrescimo.cod_genero   = calculo.cod_genero
       AND credito_acrescimo.cod_natureza = calculo.cod_natureza
     WHERE carne.exercicio = ''||stExercicio||'' 
       AND carne.numeracao = ''||stNumeracao||''
       AND NOT EXISTS ( SELECT 1
                          FROM orcamento.receita_credito_acrescimo
                         WHERE receita_credito_acrescimo.cod_credito   = credito_acrescimo.cod_credito
                           AND receita_credito_acrescimo.cod_especie   = credito_acrescimo.cod_especie
                           AND receita_credito_acrescimo.cod_genero    = credito_acrescimo.cod_genero
                           AND receita_credito_acrescimo.cod_natureza  = credito_acrescimo.cod_natureza
                           AND receita_credito_acrescimo.cod_acrescimo = credito_acrescimo.cod_acrescimo
                           AND receita_credito_acrescimo.cod_tipo      = credito_acrescimo.cod_tipo
                           AND receita_credito_acrescimo.exercicio     = ''||stExercicio||''
                       );


    IF(inCredito>0 OR inAcrescimo>0) 
    THEN
        RETURN FALSE;
    ELSE 
        RETURN TRUE;
    END IF;

END;

$$ LANGUAGE 'plpgsql';

