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
* Caso de uso: uc-05.04.10
*/

CREATE OR REPLACE FUNCTION retorna_acrescimos_inscricao_relatorio_divida_ativa(integer,integer,integer,boolean,integer) returns varchar as $$
declare
    inCodInscricao      ALIAS FOR $1;
    inExercicio         ALIAS FOR $2;
    inNumParcelamento   ALIAS FOR $3;
    boCobranca          ALIAS FOR $4;
    inCodTipo           ALIAS FOR $5;
    stExecuta           VARCHAR;
    stRetorno           VARCHAR;
    stValores           VARCHAR := '';
    reRecordExecuta     RECORD;

begin
    IF ( boCobranca = false ) THEN
        stExecuta := '
            SELECT COALESCE( sum(divida_acrescimo.valor), 0.00 ) AS valor
                 , acrescimo.cod_tipo
                 , acrescimo.cod_acrescimo
                 , acrescimo.descricao_acrescimo
              FROM monetario.acrescimo
         LEFT JOIN divida.divida_acrescimo
                ON divida_acrescimo.cod_tipo = acrescimo.cod_tipo
               AND divida_acrescimo.cod_acrescimo = acrescimo.cod_acrescimo
               AND divida_acrescimo.cod_inscricao = '||inCodInscricao||' 
               AND divida_acrescimo.exercicio = '||quote_literal(inExercicio)||'
             WHERE acrescimo.cod_tipo = '||inCodTipo||'
          GROUP BY acrescimo.cod_tipo
                 , acrescimo.cod_acrescimo
                 , acrescimo.descricao_acrescimo
        ';
    ELSE
        stExecuta := '
            SELECT COALESCE ( sum(parcela_acrescimo.vlracrescimo), 0.00 ) AS valor
                 , acrescimo.cod_tipo
                 , acrescimo.cod_acrescimo
                 , acrescimo.descricao_acrescimo
              FROM monetario.acrescimo
         LEFT JOIN divida.parcela_acrescimo
                ON parcela_acrescimo.cod_tipo = acrescimo.cod_tipo
               AND parcela_acrescimo.cod_acrescimo = acrescimo.cod_acrescimo
               AND parcela_acrescimo.num_parcelamento = '||inNumParcelamento||'
             WHERE acrescimo.cod_tipo = '||inCodTipo||'
          GROUP BY acrescimo.cod_tipo
                 , acrescimo.cod_acrescimo
                 , acrescimo.descricao_acrescimo
        ';
    END IF;

    FOR reRecordExecuta IN EXECUTE stExecuta LOOP
        stValores := stValores ||reRecordExecuta.valor || ';' || reRecordExecuta.cod_acrescimo || ';' || reRecordExecuta.cod_tipo || ';' || reRecordExecuta.descricao_acrescimo|| '#';
    END LOOP;

    IF LENGTH(stValores) > 0 THEN
        --REMOVE O ULTIMO #
        stValores := SUBSTRING(stValores,0,LENGTH(stValores));
    END IF;

    stRetorno := stValores;

    return stRetorno;
end;
$$language 'plpgsql';
