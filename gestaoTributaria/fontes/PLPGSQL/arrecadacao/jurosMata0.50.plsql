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
* $Id: jurosMata0.50.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.00
* Calculo Valor de Juros para Mariana
*/ 
CREATE OR REPLACE FUNCTION fn_juros_0pt50_porcento(date,date,numeric,integer,integer) RETURNS numeric as '

    DECLARE
        dtVencimento    ALIAS FOR $1;
        dtDataCalculo   ALIAS FOR $2;
        nuValor         ALIAS FOR $3;
        inCodAcrescimo  ALIAS FOR $4;
        inCodTipo       ALIAS FOR $5;
        nuRetorno       NUMERIC = 0.00;
        nuAux           NUMERIC = 0.00;
        inDiff          INTEGER;
        inDias          INTEGER;
        inDiasInicio    INTEGER;
        i               INTEGER = 0;
        nuTaxaFracao    NUMERIC = 0.0166/100;
        nuTaxa          NUMERIC = 0.5/100;
        ValorComJuros   NUMERIC;
        inCount         INTEGER = 0;
        nuValorComAcrescimo NUMERIC;

    BEGIN
        SELECT
            fn_acrescimo_indice_mata( dtVencimento, dtDataCalculo, nuValor, 3, 1 )
        INTO
            nuValorComAcrescimo;

        --nuValorComAcrescimo := nuValorComAcrescimo + nuValor;
        nuValorComAcrescimo := nuValor;

        ValorComJuros := 0;
        inDiff := diff_datas_em_meses(dtVencimento,dtDataCalculo);

        if ( inDiff = 0 ) then

            inDias := diff_datas_em_dias(dtVencimento,dtDataCalculo);
            IF ( inDias > 0 ) THEN
                while ( i < inDias ) loop
                    nuAux := nuAux + nuTaxaFracao ;
                    i := i +1;
                end loop;

                ValorComJuros := ( nuValorComAcrescimo * nuAux );--::numeric(14,2);
            ELSE
                ValorComJuros := 0.00;
            END  IF;


        else
            inDiasInicio := extract (day from dtVencimento);
            inDias := extract (day from dtDataCalculo);

            IF ( inDias = 31 ) THEN
                inDias := 30;
            END IF;

            if ( inDiasInicio >= 1 ) then
                inDiff := inDiff - 1;
                if ( inDiasInicio > 30 ) then
                    inDias := inDias + (31 - inDiasInicio);
                else
                    inDias := inDias + (30 - inDiasInicio);
                end if;
            end if;
      
            WHILE ( inDias > 30 ) LOOP
                inDias:= inDias - 30;
                inDiff:= inDiff + 1;
            END LOOP;

            -- JUROS COMPOSTO

            while ( i < inDias ) loop
                nuAux := ( nuAux + nuTaxaFracao ) ;
                i := i +1;
            end loop;

            ValorComJuros := (nuValorComAcrescimo * nuAux);--::numeric(14,2);

            WHILE inCount < inDiff LOOP
                ValorComJuros := ( (( (ValorComJuros + nuValorComAcrescimo) * nuTaxa) ) + ValorComJuros );
                inCount:=inCount + 1;
            END LOOP;
            -- JUROS COMPOSTO

        end if;

        nuRetorno := ValorComJuros ;            

        RETURN (nuRetorno)::numeric(14,2);
    END;
'language 'plpgsql';
