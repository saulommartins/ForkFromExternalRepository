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
* $Id: fn_aplica_multa.plsql 61622 2015-02-18 15:50:46Z evandro $
*
* Caso de uso: uc-05.03.00
*/

/*
$Log$
Revision 1.13  2007/09/21 18:46:30  fabio
Ticket#10227#

Revision 1.12  2007/01/31 11:01:08  dibueno
Melhoria: forçar valor 0.00 caso retorne valor negativo

Revision 1.11  2006/09/19 14:35:34  domluc
Adicionada chave completa de funcao

Revision 1.10  2006/09/19 09:40:03  domluc
Alterada chamada de funções para passar cod_acrescimo e cod_tipo

Revision 1.9  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION aplica_multa(varchar,integer,integer,date) returns numeric as $$
DECLARE
    stNumeracao     ALIAS FOR $1;
    inExercicio     ALIAS FOR $2;
    inCodParcela    ALIAS FOR $3;
    dtDataBase      ALIAS FOR $4;
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
                            , CASE WHEN arrecadacao.fn_atualiza_data_vencimento ( COALESCE( PARREE.vencimento, PAR.vencimento ) ) = ' || quote_literal(dtDataBase) || ' THEN
                                arrecadacao.fn_atualiza_data_vencimento ( COALESCE( PARREE.vencimento, PAR.vencimento ) ) 
                              ELSE
                                COALESCE( PARREE.vencimento, PAR.vencimento )
                              END as vencimento
                            , LC.valor
                        FROM 
                            arrecadacao.parcela AS PAR
                            INNER JOIN arrecadacao.lancamento LAN ON LAN.cod_lancamento = PAR.cod_lancamento
                            INNER JOIN arrecadacao.lancamento_calculo LC ON LC.cod_lancamento = LAN.cod_lancamento
                            INNER JOIN arrecadacao.calculo CAL ON CAL.cod_calculo = LC.cod_calculo
                            LEFT JOIN ( SELECT * 
                                    FROM arrecadacao.parcela_reemissao
                                    WHERE cod_parcela = '||inCodParcela||'
                                ORDER BY timestamp ASC limit 1
                            ) PARREE
                            ON PARREE.cod_parcela = PAR.cod_parcela
                       WHERE 
                         PAR.cod_parcela = '||inCodParcela||'
    ';

    FOR reRecord IN EXECUTE stSqlCreditos LOOP
        stSqlFuncoes := '
             SELECT
                FUNC.nom_funcao as funcao,
                ACRE.cod_acrescimo ,
                ACRE.cod_tipo
             FROM
                monetario.credito as CRED
                INNER JOIN monetario.credito_acrescimo as CREDACRE
                    ON      CRED.cod_credito = CREDACRE.cod_credito
                    AND    CRED.cod_especie = CREDACRE.cod_especie
                    AND    CRED.cod_genero  = CREDACRE.cod_genero
                    AND    CRED.cod_natureza= CREDACRE.cod_natureza
                INNER JOIN 
                    monetario.acrescimo as ACRE 
                ON 
                    CREDACRE.cod_acrescimo = ACRE.cod_acrescimo
                    AND ACRE.cod_tipo = CREDACRE.cod_tipo

                INNER JOIN monetario.formula_acrescimo as FORM ON FORM.cod_acrescimo = ACRE.cod_acrescimo
                                                               AND FORM.timestamp = (
                                                                                        SELECT MAX(timestamp)
                                                                                          FROM monetario.formula_acrescimo AS MAC
                                                                                         WHERE MAC.cod_acrescimo = ACRE.cod_acrescimo
                                                                                           AND MAC.cod_tipo = 3
                                                                                    )
                INNER JOIN administracao.funcao as FUNC ON FUNC.cod_funcao = FORM.cod_funcao
                                                       AND FUNC.cod_modulo = FORM.cod_modulo
                                                       AND FUNC.cod_biblioteca = FORM.cod_biblioteca
                
             WHERE
             CRED.cod_credito = '||reRecord.cod_credito||'
             AND    CRED.cod_especie = '||reRecord.cod_especie||'
             AND    CRED.cod_natureza = '||reRecord.cod_natureza||'
             AND    CRED.cod_genero = '||reRecord.cod_genero||'
             AND    ACRE.cod_tipo = 3
        ';
        -- executa 
        FOR reRecordFuncoes IN EXECUTE stSqlFuncoes LOOP
            SELECT
                arrecadacao.calculaproporcaoparcela( inCodParcela )
            INTO
                nuProp;

            nuProp := nuProp * reRecord.valor;

            stExecuta := 'SELECT '||reRecordFuncoes.funcao||'('''||reRecord.vencimento||''','''||dtDataBase||''','||nuProp||', '||reRecordFuncoes.cod_acrescimo||' , '||reRecordFuncoes.cod_tipo||') as valor ';
            FOR reRecordExecuta IN EXECUTE stExecuta LOOP                
                nuRetorno := nuRetorno + reRecordExecuta.valor;               
            END LOOP;           
        END LOOP;
        
    END LOOP;

    IF ( nuRetorno < 0.00 ) THEN
        nuRetorno := 0.00;
    END IF;

   return nuRetorno::numeric(14,2);
end;
$$ language 'plpgsql';
