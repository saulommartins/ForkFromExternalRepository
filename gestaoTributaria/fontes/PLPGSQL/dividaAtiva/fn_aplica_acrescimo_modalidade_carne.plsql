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
* $Id: fn_aplica_acrescimo_modalidade.plsql 29207 2008-04-15 14:51:15Z fabio $
*
* Caso de uso: uc-05.04.00
*/

CREATE OR REPLACE FUNCTION aplica_acrescimo_modalidade_carne(varchar,integer,integer,integer,integer,integer,integer,numeric,date,date,text) returns varchar as $$
declare
    stNumeracao         ALIAS FOR $1;
    inCobrancaJudicial  ALIAS FOR $2;
    inCodInscricao      ALIAS FOR $3;
    inExercicio         ALIAS FOR $4;
    inCodModalidade     ALIAS FOR $5;
    inCodTipo           ALIAS FOR $6;
    inRegistro          ALIAS FOR $7;
    nuValor             ALIAS FOR $8;
    dtDataVencimento    ALIAS FOR $9;
    dtDataBase          ALIAS FOR $10;
    boIncidencia        ALIAS FOR $11;
    stSqlFuncoes        VARCHAR;
    stExecuta           VARCHAR;
    stRetorno           VARCHAR;
    stTabela            VARCHAR;
    stSqlCreditos       VARCHAR;
    stValores           VARCHAR := '';
    inValorTotal        NUMERIC := 0.00;
    inValorParcial      NUMERIC;
    nuProp              NUMERIC;
    reRecordFuncoes     RECORD;
    reRecordExecuta     RECORD;
    reRecordExecutaGambi     RECORD;
    boUtilizar          BOOLEAN;
    inTMP               INTEGER;
    stTMP               TEXT;

begin

    inTMP := criarbufferinteiro( 'inCodInscricao', COALESCE(inCodInscricao,0) );
    inTMP := criarbufferinteiro( 'inExercicio', COALESCE(inExercicio,0) );
    inTMP := criarbufferinteiro( 'inCodModalidade', inCodModalidade );
    inTMP := criarbufferinteiro( 'inRegistro', inRegistro );
    stTMP := criarbuffertexto( 'boIncidencia', boIncidencia );
    inTMP := criarbufferinteiro( 'judicial', inCobrancaJudicial );

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
            AND divida.modalidade_acrescimo.cod_tipo = '||inCodTipo||'
            AND divida.modalidade_acrescimo.pagamento = '||boIncidencia||'
        
        INNER JOIN
            (
                SELECT
                    tmp.*
                FROM
                   monetario.formula_acrescimo AS tmp,
                   (
                        SELECT
                            MAX(timestamp)AS timestamp,
                            cod_tipo,
                            cod_acrescimo
                        FROM
                            monetario.formula_acrescimo
                        GROUP BY
                            cod_tipo, cod_acrescimo
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
            divida.modalidade.cod_modalidade = '||inCodModalidade||'
    ';


    stSqlCreditos := '
                      SELECT  CAL.cod_calculo
                            , CAL.cod_credito
                            , CAL.cod_especie
                            , CAL.cod_genero
                            , CAL.cod_natureza
                            , PAR.cod_parcela
                            , LC.valor
                        FROM   arrecadacao.carne CAR
                            INNER JOIN arrecadacao.parcela PAR ON PAR.cod_parcela = CAR.cod_parcela
                            INNER JOIN arrecadacao.lancamento LAN ON LAN.cod_lancamento = PAR.cod_lancamento
                            INNER JOIN arrecadacao.lancamento_calculo LC ON LC.cod_lancamento = LAN.cod_lancamento
                            INNER JOIN arrecadacao.calculo CAL ON CAL.cod_calculo = LC.cod_calculo
                       WHERE 
                         CAR.numeracao = '''||stNumeracao||'''
        ';

    IF ( dtDataVencimento < dtDataBase ) THEN
        -- executa
        FOR reRecordFuncoes IN EXECUTE stSqlFuncoes LOOP
    
            stExecuta :=  'SELECT '||reRecordFuncoes.funcao_valida||'( '||inRegistro||' ) as utilizar ';
            FOR reRecordExecuta IN EXECUTE stExecuta LOOP
                boUtilizar := reRecordExecuta.utilizar;
            END LOOP;
    
            IF ( boUtilizar ) THEN 
                inValorParcial := 0.00;
                FOR reRecordExecutaGambi IN EXECUTE stSqlCreditos LOOP
                    SELECT
                        arrecadacao.calculaproporcaoparcela( reRecordExecutaGambi.cod_parcela )
                    INTO
                        nuProp;

                    nuProp := nuProp * reRecordExecutaGambi.valor;

                    stExecuta :=  'SELECT '||reRecordFuncoes.funcao||'('''||dtDataVencimento||''','''||dtDataBase||''','||nuProp||', '||reRecordFuncoes.cod_acrescimo||' , '||reRecordFuncoes.cod_tipo||') as valor ';     

                    FOR reRecordExecuta IN EXECUTE stExecuta LOOP                
                        inValorTotal := inValorTotal + reRecordExecuta.valor;
                        inValorParcial := inValorParcial + reRecordExecuta.valor;
                    END LOOP;
                END LOOP;

                stValores := stValores || ';' || inValorParcial || ';' || reRecordFuncoes.cod_acrescimo || ';' || reRecordFuncoes.cod_tipo;
            END IF;
        END LOOP;
    END IF;

    stRetorno := inValorTotal || stValores;
   return stRetorno;
end;
$$ language 'plpgsql';
