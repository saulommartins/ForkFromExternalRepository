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
* $Id: fn_desconto_credito_lancamento.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-5.3.19
* Caso de uso: uc-05.03.19
*/

/*
$Log$
Revision 1.9  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION arrecadacao.fn_desconto_credito_lancamento( INT,INT,INT,INT,INT,INT,INT,DATE,NUMERIC, INTEGER )  RETURNS NUMERIC(14,2) AS '
DECLARE
    inCodLancamento ALIAS FOR $1;   
    inCodParcela    ALIAS FOR $2;   
    inCodCalculo    ALIAS FOR $3;   
    inCodCredito    ALIAS FOR $4;  
    inCodEspecie    ALIAS FOR $5;   
    inCodGenero     ALIAS FOR $6;   
    inCodNatureza   ALIAS FOR $7;   
    dtDataBase      ALIAS FOR $8;   
    nuValor         ALIAS FOR $9;
    inExercicio     ALIAS FOR $10;
    boDesconto      BOOLEAN;
    boGrupo         BOOLEAN;
    nuValorCalc     NUMERIC;
    nuValorParc     NUMERIC;
    nuValorTotal    NUMERIC;
    nuProporcao     NUMERIC;
    nuValorDesc     NUMERIC;
    nuRetorno       NUMERIC := 0.00;

BEGIN
    --Verifica se o credito pertence a um grupo
        SELECT CASE WHEN (( SELECT 1
                              FROM arrecadacao.calculo_grupo_credito
                             WHERE calculo_grupo_credito.cod_calculo   = inCodCalculo
                               AND calculo_grupo_credito.ano_exercicio = ''''||inExercicio||''''
                          ) IS NOT NULL)
                    THEN TRUE
                    ELSE FALSE
               END
          INTO boGrupo;

    --Se pertencer a um grupo, faz os calculos baseados no grupo
    IF boGrupo IS TRUE THEN

            SELECT desconto, calc.valor
              INTO boDesconto, nuValorCalc
              FROM arrecadacao.calculo calc
        INNER JOIN arrecadacao.calculo_grupo_credito acgc
                ON acgc.cod_calculo = calc.cod_calculo
        INNER JOIN arrecadacao.grupo_credito agc
                ON agc.cod_grupo     = acgc.cod_grupo
               AND agc.ano_exercicio = acgc.ano_exercicio
        INNER JOIN arrecadacao.credito_grupo acg
                ON acg.cod_grupo    = agc.cod_grupo
               AND acg.cod_credito  = calc.cod_credito
               AND acg.cod_especie  = calc.cod_especie
               AND acg.cod_genero   = calc.cod_genero
               AND acg.cod_natureza = calc.cod_natureza
             WHERE calc.cod_calculo  = inCodCalculo
               AND calc.cod_credito  = inCodCredito
               AND calc.cod_especie  = inCodEspecie
               AND calc.cod_genero   = inCodGenero
               AND calc.cod_natureza = inCodNatureza
               AND agc.ano_exercicio = ''''||inExercicio||'''';

        SELECT SUM(ac.valor)
          INTO nuValorTotal
          FROM arrecadacao.calculo AS ac
    INNER JOIN  arrecadacao.lancamento_calculo AS alc
            ON alc.cod_calculo    = ac.cod_calculo 
           AND alc.cod_lancamento = inCodLancamento
    INNER JOIN arrecadacao.calculo_grupo_credito AS acgc 
            ON acgc.cod_calculo = alc.cod_calculo 
           AND ac.exercicio     = acgc.ano_exercicio
         WHERE ac.cod_credito IN ( SELECT cod_credito 
                                     FROM arrecadacao.credito_grupo AS acg
                                    WHERE acg.desconto       = TRUE 
                                      AND acg.cod_grupo      = acgc.cod_grupo 
                                      AND acgc.ano_exercicio = ''''||inExercicio||''''  
                                 )
           AND acgc.ano_exercicio = ''''||inExercicio||'''';
   
    --Se nao, calcula baseado no credito
    ELSE
        SELECT valor
          INTO nuValorCalc 
          FROM arrecadacao.calculo
         WHERE calculo.cod_calculo  = inCodCalculo
           AND calculo.cod_credito  = inCodCredito
           AND calculo.cod_especie  = inCodEspecie
           AND calculo.cod_genero   = inCodGenero
           AND calculo.cod_natureza = inCodNatureza
           AND calculo.exercicio    = ''''||inExercicio||'''';

        SELECT CASE WHEN (( SELECT 1
                              FROM arrecadacao.parcela_desconto
                             WHERE parcela_desconto.cod_parcela = inCodParcela 
                          ) IS NOT NULL)
                    THEN TRUE
                    ELSE FALSE
               END
          INTO boDesconto;
    
        nuValorTotal := nuValorCalc;

    END IF;

    SELECT valor INTO nuValorParc FROM arrecadacao.parcela WHERE cod_parcela = inCodParcela; 
    
    IF nuValorTotal > 0 THEN
        nuProporcao := (nuValorCalc * 100) / nuValorTotal;
    ELSE
        nuProporcao := 0;
    END IF;        

    nuValorDesc := fn_busca_desconto_parcela(inCodParcela,dtDataBase);
    
    IF boDesconto = true THEN
        nuRetorno :=  ( (nuValorParc - nuValorDesc) * ( nuProporcao/100))::NUMERIC(14,2);
        RETURN nuRetorno;
    ELSE
        return nuRetorno;
    END IF;

END;
' LANGUAGE 'plpgsql';
