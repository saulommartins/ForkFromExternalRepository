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
* $Id: fn_aplica_acrescimos_por_credito.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.00
*/

/*
$Log$
Revision 1.1  2007/07/13 14:16:54  cercato
*** empty log message ***

*/

--funcao exclusiva da divida ativa
CREATE OR REPLACE FUNCTION aplica_acrescimos_por_credito(integer,integer,integer,integer,date,date,numeric,integer) returns numeric as '
declare
    inCodCredito    ALIAS FOR $1;
    inCodEspecie    ALIAS FOR $2;
    inCodGenero     ALIAS FOR $3;
    inCodNatureza   ALIAS FOR $4;
    dtVencimento    ALIAS FOR $5;
    dtAtual         ALIAS FOR $6;
    nuValor         ALIAS FOR $7;
    inCodTipo       ALIAS FOR $8;
    stSqlFuncoes    VARCHAR;
    stExecuta       VARCHAR;
    nuRetorno       NUMERIC := 0.00;
    reRecord        RECORD;
    reRecordFuncoes RECORD;
    reRecordExecuta RECORD;
begin

    stSqlFuncoes := ''
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
        INNER JOIN monetario.acrescimo as ACRE ON CREDACRE.cod_acrescimo = ACRE.cod_acrescimo
        INNER JOIN monetario.formula_acrescimo as FORM ON FORM.cod_acrescimo = ACRE.cod_acrescimo
        INNER JOIN administracao.funcao as FUNC ON FUNC.cod_funcao = FORM.cod_funcao
                                                AND FUNC.cod_modulo = FORM.cod_modulo
                                                AND FUNC.cod_biblioteca = FORM.cod_biblioteca

        WHERE
            CRED.cod_credito = ''||inCodCredito||''
            AND    CRED.cod_especie = ''||inCodEspecie||''
            AND    CRED.cod_natureza = ''||inCodNatureza||''
            AND    CRED.cod_genero = ''||inCodGenero||''
            AND    ACRE.cod_tipo = ''||inCodTipo||''
    '';

    -- executa 
    FOR reRecordFuncoes IN EXECUTE stSqlFuncoes LOOP
        stExecuta :=  ''SELECT ''||reRecordFuncoes.funcao||''(''''''||dtVencimento||'''''',''''''||dtAtual||'''''',''||nuValor||'', ''||reRecordFuncoes.cod_acrescimo||'' , ''||reRecordFuncoes.cod_tipo||'') as valor '';     
        FOR reRecordExecuta IN EXECUTE stExecuta LOOP                
            nuRetorno := nuRetorno + reRecordExecuta.valor;               
        END LOOP;           
    END LOOP;


    IF ( nuRetorno < 0.00 ) THEN
        nuRetorno := 0.00;
    END IF;

   return nuRetorno::numeric(14,2);
end;
'language 'plpgsql';
