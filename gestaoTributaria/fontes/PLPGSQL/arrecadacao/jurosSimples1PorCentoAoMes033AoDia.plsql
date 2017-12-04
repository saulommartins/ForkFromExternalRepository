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
* $Id: jurosSimples1PorCentoAoMes033AoDia.plsql 63561 2015-09-10 18:40:40Z fabio $
*
* Caso de uso: uc-05.03.00
*/
CREATE OR REPLACE FUNCTION fn_juros_simples_1_por_cento_ao_mes_033_ao_dia( dtVencimento      DATE
                                                                         , dtCalculo         DATE
                                                                         , nuValor           NUMERIC
                                                                         , inCodAcrescimo    INTEGER
                                                                         , inCodTipo         INTEGER
                                                                         ) RETURNS           NUMERIC AS $$
DECLARE
    nuRetorno           NUMERIC := 0.00;
    nuAux               NUMERIC := 0.00;
    inCount             INTEGER := 0;
    inDiff              INTEGER := 0;
    inDias              INTEGER := 0;
    inDiasInicio        INTEGER := 0;
    nuTaxaFracao        NUMERIC := 0.033/100;
    nuTaxa              NUMERIC := 0.01;
    ValorComJuros       NUMERIC := 0.00;
BEGIN

    inDiff := diff_datas_em_meses(dtVencimento,dtCalculo);
    IF ( inDiff = 0 ) THEN

        inDias := diff_datas_em_dias(dtVencimento,dtCalculo);
        IF ( inDias > 0 ) THEN
            WHILE ( inCount < inDias ) LOOP
                nuAux := nuAux + nuTaxaFracao ;
                inCount := inCount +1;
            END LOOP;
            ValorComJuros := ( nuValor * nuAux );--::numeric(14,2);
        ELSE
            ValorComJuros := 0.00;
        END IF;

        nuRetorno := ValorComJuros ;

    ELSE

        inDiasInicio := extract (day from dtVencimento);
        inDias       := extract (day from dtCalculo);

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

        WHILE ( inCount < inDias ) LOOP
            nuAux := nuAux + nuTaxaFracao ;
            inCount := inCount +1;
        END LOOP;
        nuAux := nuAux + (inDiff * nuTaxa);
        ValorComJuros := ( nuValor * nuAux );--::numeric(14,2);

        nuRetorno := ValorComJuros ;

    END IF;

    RETURN (nuRetorno)::numeric(14,2);
END;
$$ LANGUAGE 'plpgsql';
