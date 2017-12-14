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
* $Id: fn_aplica_acrescimo_modalidade.plsql 63615 2015-09-18 14:11:12Z evandro $
*
* Caso de uso: uc-05.04.00
*/

CREATE OR REPLACE FUNCTION aplica_acrescimo_modalidade(integer,integer,integer,integer,integer,integer,numeric,date,date,text) returns varchar as $$
declare
    inCobrancaJudicial  ALIAS FOR $1;
    inCodInscricao      ALIAS FOR $2;
    inExercicio         ALIAS FOR $3;
    inCodModalidade     ALIAS FOR $4;
    inCodTipo           ALIAS FOR $5;
    inRegistro          ALIAS FOR $6;
    nuValor2            ALIAS FOR $7;
    nuValor         NUMERIC;
    dtDataVencimento    ALIAS FOR $8;
    dtDataBase          ALIAS FOR $9;
    boIncidencia        ALIAS FOR $10;
    stSqlFuncoes        VARCHAR;
    stExecuta           VARCHAR;
    stRetorno           VARCHAR;
    stTabela            VARCHAR;
    stValores           VARCHAR := '';
    inValorTotal        NUMERIC := 0.00;
    reRecordFuncoes     RECORD;
    reRecordExecuta     RECORD;
    boUtilizar          BOOLEAN;
    inTMP               INTEGER;
    dtTimestamp         TIMESTAMP;
    boUsaTimestamp      BOOLEAN;
    stTMP               TEXT;

begin
    inTMP := criarbufferinteiro( 'inCodInscricao', inCodInscricao );
    inTMP := criarbufferinteiro( 'inExercicio', inExercicio );
    inTMP := criarbufferinteiro( 'inCodModalidade', inCodModalidade );
    inTMP := criarbufferinteiro( 'inRegistro', inRegistro );
    stTMP := criarbuffertexto( 'boIncidencia', boIncidencia );
    inTMP := criarbufferinteiro( 'judicial', inCobrancaJudicial );
if nuValor2 IS NULL THEN nuValor := 0; ELSE nuValor = nuValor2;END IF;

    stSqlFuncoes := '
        SELECT
            administracao.funcao.nom_funcao as funcao
            , divida.modalidade_acrescimo.cod_acrescimo
            , divida.modalidade_acrescimo.cod_tipo
            , (
                SELECT
                    administracao.funcao.nom_funcao
                FROM
                    administracao.funcao
                WHERE
                    administracao.funcao.cod_funcao = divida.modalidade_acrescimo.cod_funcao
                    AND administracao.funcao.cod_modulo = divida.modalidade_acrescimo.cod_modulo
                    AND administracao.funcao.cod_biblioteca = divida.modalidade_acrescimo.cod_biblioteca
              )AS funcao_valida

        FROM
            divida.modalidade
        
        INNER JOIN
            divida.modalidade_vigencia
        ON
            divida.modalidade_vigencia.timestamp = divida.modalidade.ultimo_timestamp
            AND divida.modalidade_vigencia.cod_modalidade = divida.modalidade.cod_modalidade
        
        INNER JOIN
            divida.modalidade_acrescimo
        ON
            divida.modalidade_acrescimo.timestamp = divida.modalidade.ultimo_timestamp
            AND divida.modalidade_acrescimo.cod_modalidade = divida.modalidade.cod_modalidade
        ';
        IF inCodTipo != 0 THEN
            stSqlFuncoes := stSqlFuncoes || ' AND divida.modalidade_acrescimo.cod_tipo = '|| inCodTipo ||' ';
        END IF;
        
        stSqlFuncoes := stSqlFuncoes || ' 
            AND divida.modalidade_acrescimo.pagamento = '|| boIncidencia ||'
        
        INNER JOIN
            (
                SELECT
                    tmp.*
                FROM
                   monetario.formula_acrescimo AS tmp,
                   (
       ';
       --AJUSTE FEITO PARA RECUPERAR A FUNÇÃO QUE FOI UTILIZADA NO MOMENTO DA COBRANÇA
       IF COALESCE(recuperarbuffertexto('boConsulta'),'false') = 'true' THEN
            IF (inRegistro = 0) THEN
                dtTimestamp := now();
            ELSE
                SELECT timestamp INTO dtTimestamp FROM divida.parcelamento WHERE num_parcelamento = inRegistro;
            END IF;

            SELECT CASE WHEN timestamp IS NULL THEN
                        false
                   ELSE
                        true
                   END INTO boUsaTimestamp
              FROM monetario.formula_acrescimo
             WHERE timestamp <= dtTimestamp;

          IF boUsaTimestamp THEN
                stSqlFuncoes := stSqlFuncoes || ' SELECT MAX(timestamp)AS timestamp
                                                    , cod_tipo
                                                    , cod_acrescimo
                                                 FROM monetario.formula_acrescimo
                                                WHERE timestamp <= '|| quote_literal(dtTimestamp) ||' ';
          ELSE
               stSqlFuncoes := stSqlFuncoes || ' SELECT MIN(timestamp)AS timestamp
                                                    , cod_tipo
                                                    , cod_acrescimo
                                                 FROM monetario.formula_acrescimo';
          END IF;
       ELSE
            stSqlFuncoes := stSqlFuncoes || ' SELECT MAX(timestamp)AS timestamp
                                                    , cod_tipo
                                                    , cod_acrescimo
                                                 FROM monetario.formula_acrescimo';
       END IF;
       stSqlFuncoes := stSqlFuncoes || '
                        GROUP BY cod_tipo, cod_acrescimo
                   )AS tmp2
                WHERE
                    tmp.timestamp = tmp2.timestamp
                    AND tmp.cod_tipo = tmp2.cod_tipo
                    AND tmp.cod_acrescimo = tmp2.cod_acrescimo
            )AS mfa
        ON
            mfa.cod_acrescimo = divida.modalidade_acrescimo.cod_acrescimo
            AND mfa.cod_tipo = divida.modalidade_acrescimo.cod_tipo
        
        INNER JOIN
            administracao.funcao
        ON
            administracao.funcao.cod_funcao = mfa.cod_funcao
            AND administracao.funcao.cod_modulo = mfa.cod_modulo
            AND administracao.funcao.cod_biblioteca = mfa.cod_biblioteca
        
        WHERE
            divida.modalidade.cod_modalidade = '|| inCodModalidade ||'
    ';
    IF ( dtDataVencimento <= dtDataBase ) THEN
        -- executa
        FOR reRecordFuncoes IN EXECUTE stSqlFuncoes LOOP
            stExecuta :=  'SELECT '||reRecordFuncoes.funcao_valida||'( '||inRegistro||' ) as utilizar ';
            FOR reRecordExecuta IN EXECUTE stExecuta LOOP
                boUtilizar := reRecordExecuta.utilizar;
            END LOOP;
            IF ( boUtilizar ) THEN 
                stExecuta :=  'SELECT '||reRecordFuncoes.funcao||'('|| quote_literal(dtDataVencimento)||','|| quote_literal(dtDataBase)||','||nuValor||', '||reRecordFuncoes.cod_acrescimo||' , '||reRecordFuncoes.cod_tipo||') as valor ';
                FOR reRecordExecuta IN EXECUTE stExecuta LOOP                
                    inValorTotal := inValorTotal + reRecordExecuta.valor;
                    stValores := stValores || ';' || reRecordExecuta.valor || ';' || reRecordFuncoes.cod_acrescimo || ';' || reRecordFuncoes.cod_tipo;
                END LOOP;           
            END IF;
        END LOOP;
    END IF;

    stRetorno := inValorTotal || stValores;
   return stRetorno;
end;
$$ language 'plpgsql';
