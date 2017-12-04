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
* $Id: somaPagAcrescimosLoteDetalhe.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.00
*/

/*
$Log$
Revision 1.1  2007/07/13 14:21:55  dibueno
Bug #9649#


*/

CREATE OR REPLACE FUNCTION arrecadacao.somaPagAcrescimosLoteDetalhe
    ( integer, integer, integer, integer, integer, integer, varchar )
returns varchar as '
declare
    inCodLote       ALIAS FOR $1;
    inExercicio     ALIAS FOR $2;
    inCodCredito    ALIAS FOR $3;
    inCodEspecie    ALIAS FOR $4;
    inCodGenero     ALIAS FOR $5;
    inCodNatureza   ALIAS FOR $6;
    boGrupo         ALIAS FOR $7;
    stSql           varchar;
    reRecord        record;
    inCont          integer := 0;
    stAuxiliar      varchar := '''';
    stRetorno       varchar := '''';
begin

    IF boGrupo = ''credito'' THEN
        stAuxiliar := ''
            not EXISTS (
                select cod_calculo
                from arrecadacao.calculo_grupo_credito
                where cod_calculo = c.cod_calculo )
            AND
        '';
    ELSIF boGrupo = ''grupo'' THEN
        stAuxiliar := ''
            EXISTS (
                select cod_calculo
                from arrecadacao.calculo_grupo_credito
                where cod_calculo = c.cod_calculo )
            AND
        '';
        
    END IF;


stSql := ''

    SELECT

        (
            CASE WHEN paga.cod_tipo = 2 THEN
                ''''JUROS''''
            WHEN paga.cod_tipo = 3 THEN
                ''''MULTA''''
            END
        ) as tipo
        , coalesce ( sum ( paga.valor), 0.00 )::varchar as valor

    FROM

        arrecadacao.calculo c
        , arrecadacao.pagamento_acrescimo paga
        , arrecadacao.pagamento pag
        , arrecadacao.pagamento_lote plote
        , arrecadacao.lote lote
        , monetario.credito mc

    WHERE
        ''|| stAuxiliar ||''

        mc.cod_credito = c.cod_credito
        and mc.cod_especie = c.cod_especie
        and mc.cod_genero = c.cod_genero
        and mc.cod_natureza = c.cod_natureza

        and paga.cod_calculo = c.cod_calculo
        and pag.numeracao = paga.numeracao
        and pag.ocorrencia_pagamento = paga.ocorrencia_pagamento
        and pag.cod_convenio = paga.cod_convenio

        and plote.numeracao = pag.numeracao
        and plote.ocorrencia_pagamento = pag.ocorrencia_pagamento
        and plote.cod_convenio = pag.cod_convenio

        and lote.cod_lote   = plote.cod_lote
        and lote.exercicio  = plote.exercicio
        and lote.cod_lote   = ''||inCodLote||''
        and lote.exercicio  = ''||inExercicio||''
        and c.cod_credito   = ''||inCodCredito||''
        and c.cod_especie   = ''||inCodEspecie||''
        and c.cod_genero    = ''||inCodGenero||''
        and c.cod_natureza  = ''||inCodNatureza||''

    group by
        paga.cod_tipo

    ORDER BY
        paga.cod_tipo
    '';


    
    FOR reRecord in execute stSql LOOP



        IF inCont = 0 THEN
            IF ( reRecord.tipo = ''JUROS'' ) THEN
                stRetorno := reRecord.valor;
            ELSE stRetorno := ''0.00'';
            END IF;
        END IF;
        IF inCont = 1 THEN
            IF ( reRecord.tipo = ''MULTA'' ) THEN
                stRetorno := stRetorno || reRecord.valor;
            ELSE stRetorno := ''0.00'';
            END IF;
        END IF;

        inCont := inCont + 1;

        stRetorno := stRetorno||''§'';

    END LOOP;

    IF stRetorno = '''' THEN
        stRetorno := ''0.00§0.00'';
    END IF;

    stRetorno := stRetorno||''§'';
    return stRetorno;

end;
'language 'plpgsql';
