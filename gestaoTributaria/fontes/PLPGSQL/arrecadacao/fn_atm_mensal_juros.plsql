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
* Calculo Atualização mensal para o Valor de Juros Especial (Mata)
*
* Data de Criação   : 17/06/2010
*
* @author Analista      Fábio Bertoldi
* @author Desenvolvedor Eduardo Paculski Schitz
*
* @package URBEM
*
* $Id:$
*/

CREATE OR REPLACE FUNCTION fn_atm_mensal_juros(date,date,numeric,integer,integer) RETURNS numeric as $$
DECLARE
    dtVencimento    ALIAS FOR $1;
    dtDataCalculo   ALIAS FOR $2;
    nuValor         ALIAS FOR $3;
    inCodAcrescimo  ALIAS FOR $4;
    inCodTipo       ALIAS FOR $5;
    nuJuros         NUMERIC = 0.00;
    nuRetorno       NUMERIC = 0.00;
    nuValCalculado  NUMERIC = 0.00;
    inDiff          INTEGER;
    nuJuroCorrente  NUMERIC = 0.0;
    nuJuroAnterior  NUMERIC = 0.0;
    nuJuroTotal     NUMERIC = 0.0;
    inMesInicio     INTEGER;
    inMesFim        INTEGER;
    inAno           INTEGER;
    inTeste         INTEGER;
    inTotalMes      INTEGER;

BEGIN
   -- Calculo de Juros composto
    nuJuroTotal := 0.00;
    inDiff := diff_datas_em_meses(dtVencimento,dtDataCalculo);

    inMesInicio := date_part('month' , dtVencimento )::integer ;
    inMesFim := date_part('month' , dtVencimento )::integer + ( inDiff );

    inAno := date_part('year' , dtVencimento )::integer;

    inTotalMes := inMesInicio;
    nuValCalculado := nuValor;
    WHILE (inTotalMes <= inMesFim) LOOP

        SELECT valor
          INTO nuJuroCorrente
          FROM monetario.valor_acrescimo
         WHERE inicio_vigencia = ( SELECT MAX(inicio_vigencia)
                                     FROM monetario.valor_acrescimo
                                    WHERE valor_acrescimo.cod_acrescimo = inCodAcrescimo
                                      AND valor_acrescimo.cod_tipo = inCodTipo
                                      AND date_part('month' , valor_acrescimo.inicio_vigencia ) <= inMesInicio
                                      AND date_part('year' , valor_acrescimo.inicio_vigencia ) <= inAno );

        nuJuroCorrente := coalesce (nuJuroCorrente, nuJuroAnterior);
        nuJuroAnterior := nuJuroCorrente;

        nuValCalculado := ( nuValCalculado * nuJuroCorrente)/100 + nuValCalculado;

        IF ( inMesInicio >= 12 ) THEN
            inMesInicio := 1;
            inAno := inAno + 1;
        ELSE
            inMesInicio := inMesInicio + 1;
        END IF;

        inTotalMes := inTotalMes + 1;

    END loop;
        
    nuRetorno := nuValCalculado - nuValor;

    RETURN nuRetorno::numeric(14,2);
END;
$$ language 'plpgsql';
           
