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
* $Id: fn_aplica_correcao_credito_parcela.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.00
*/

/*
$Log$

*/

CREATE OR REPLACE FUNCTION aplica_correcao_credito_parcela(varchar,integer,integer,date,integer,integer,integer,integer) returns numeric as $$
declare
    stNumeracao     ALIAS FOR $1;
    inExercicio     ALIAS FOR $2;
    inCodParcela    ALIAS FOR $3;
    dtDataBase      ALIAS FOR $4;
    inCodCredito    ALIAS FOR $5;
    inCodEspecie    ALIAS FOR $6;
    inCodGenero     ALIAS FOR $7;
    inCodNatureza   ALIAS FOR $8;
    stFuncao        VARCHAR;
    stSqlCreditos   VARCHAR;
    stSqlFuncoes    VARCHAR;
    stExecuta       VARCHAR;
    nuRetorno       NUMERIC := 0.00;
    nuProp          NUMERIC;
    reRecord        RECORD;
    reRecordFuncoes RECORD;
    reRecordExecuta RECORD;
begin
    stFuncao := '';
   -- pegar calculos/creditos para o lancamento da parcela
   stSqlCreditos := '
                      SELECT  CAL.cod_calculo
                            , CAL.cod_credito
                            , CAL.cod_especie
                            , CAL.cod_genero
                            , CAL.cod_natureza
                            , case 
                                when PARREE.cod_parcela is not null then PARREE.vencimento
                                else PAR.vencimento
                              end as vencimento
                            , LC.valor
                        FROM  
                        arrecadacao.carne CAR
                        INNER JOIN arrecadacao.parcela PAR ON PAR.cod_parcela = CAR.cod_parcela
                        INNER JOIN arrecadacao.lancamento LAN ON LAN.cod_lancamento = PAR.cod_lancamento
                        INNER JOIN arrecadacao.lancamento_calculo LC ON LC.cod_lancamento = LAN.cod_lancamento
                        INNER JOIN arrecadacao.calculo CAL ON CAL.cod_calculo = LC.cod_calculo
                        LEFT JOIN ( SELECT * 
                                    FROM arrecadacao.parcela_reemissao
                                    WHERE cod_parcela = '||inCodParcela||'
                                    ORDER BY timestamp ASC LIMIT 1
                        ) PARREE
                        ON PARREE.cod_parcela = PAR.cod_parcela

                       WHERE CAR.cod_parcela    = '||inCodParcela||'      
                         AND  CAR.numeracao     = '||quote_literal(stNumeracao)||'   
                         AND  CAR.exercicio     = '||quote_literal(inExercicio)||'
                         AND  CAL.cod_credito   = '||inCodCredito||'  
                         AND  CAL.cod_especie   = '||inCodEspecie||'  
                         AND  CAL.cod_genero    = '||inCodGenero||'   
                         AND  CAL.cod_natureza  = '||inCodNatureza||' ';

    FOR reRecord IN EXECUTE stSqlCreditos LOOP
        stSqlFuncoes := ' 
             SELECT
                FUNC.nom_funcao as funcao,
                ACRE.cod_acrescimo ,                                                                                                                           ACRE.cod_tipo
             FROM
                monetario.credito as CRED

             INNER JOIN 
                monetario.credito_acrescimo as CREDACRE
             ON
                CRED.cod_credito = CREDACRE.cod_credito
                AND CRED.cod_especie = CREDACRE.cod_especie
                AND CRED.cod_genero = CREDACRE.cod_genero
                AND CRED.cod_natureza = CREDACRE.cod_natureza

             INNER JOIN 
                monetario.acrescimo as ACRE 
             ON 
                CREDACRE.cod_acrescimo = ACRE.cod_acrescimo
                AND ACRE.cod_tipo = CREDACRE.cod_tipo

            INNER JOIN 
                (
                    SELECT
                        formula_acrescimo.*
                    FROM
                        monetario.formula_acrescimo

                    INNER JOIN
                        (
                            SELECT
                                cod_acrescimo,
                                cod_tipo,
                                max(timestamp) AS timestamp
                            FROM
                                monetario.formula_acrescimo
                            GROUP BY
                                cod_acrescimo,
                                cod_tipo
                        )AS tmp
                    ON
                        formula_acrescimo.cod_acrescimo = tmp.cod_acrescimo
                        AND formula_acrescimo.cod_tipo = tmp.cod_tipo
                        AND formula_acrescimo.timestamp = tmp.timestamp
                ) as FORM 
             ON 
                FORM.cod_acrescimo = ACRE.cod_acrescimo
                AND FORM.cod_tipo = ACRE.cod_tipo

             INNER JOIN 
                administracao.funcao as FUNC 
             ON 
                FUNC.cod_funcao = FORM.cod_funcao
                AND FUNC.cod_modulo = FORM.cod_modulo
                AND FUNC.cod_biblioteca = FORM.cod_biblioteca

             WHERE
                 CRED.cod_credito      = '||reRecord.cod_credito||'
                 AND CRED.cod_especie  = '||reRecord.cod_especie||'
                 AND CRED.cod_natureza = '||reRecord.cod_natureza||'
                 AND CRED.cod_genero   = '||reRecord.cod_genero||'
                 AND ACRE.cod_tipo     = 1
        ';

        FOR reRecordFuncoes IN EXECUTE stSqlFuncoes LOOP
            SELECT
                arrecadacao.calculaproporcaoparcela( inCodParcela )
            INTO
                nuProp;

            nuProp := nuProp * reRecord.valor;

            stExecuta :=  'SELECT '||reRecordFuncoes.funcao||'('||quote_literal(reRecord.vencimento)||','||quote_literal(dtDataBase)||','||nuProp||', '||reRecordFuncoes.cod_acrescimo||' , '||reRecordFuncoes.cod_tipo||') as valor ';     
            FOR reRecordExecuta IN EXECUTE stExecuta LOOP                
                nuRetorno := nuRetorno + reRecordExecuta.valor;               
            END LOOP;           
        END LOOP;
        
    END LOOP;


   return nuRetorno::numeric(14,2);
end;
$$language 'plpgsql';
