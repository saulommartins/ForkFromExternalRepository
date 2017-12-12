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
* $Id: multaMata.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.00
*/

/*
    Função de Calculo de Multa para Mata de São João/BA
     - Dado data de vencimento e de calculo, a função deve retornor o percentual a ser aplicado
*/

CREATE OR REPLACE FUNCTION fn_multa_mora(date,date,float,integer,integer) RETURNS numeric as '

    DECLARE
        dtVencimento    ALIAS FOR $1;
        dtDataCalculo   ALIAS FOR $2;
        flCorrigido     ALIAS FOR $3;
        inCodAcrescimo  ALIAS FOR $4;
        inCodTipo       ALIAS FOR $5;
        flMulta         NUMERIC;
        inDiff          INTEGER;
        inAno           INTEGER;
        nuValorComAcrescimo NUMERIC;

    BEGIN
        SELECT
            fn_acrescimo_indice_mata( dtVencimento, dtDataCalculo, flCorrigido::numeric, 3, 1 )
        INTO
            nuValorComAcrescimo;

        --nuValorComAcrescimo := nuValorComAcrescimo + flCorrigido::numeric;
        nuValorComAcrescimo := flCorrigido::numeric;

        -- recupera diferença em dias das datas
        inDiff := diff_datas_em_dias(dtVencimento,dtDataCalculo);
        inAno  := date_part(''year'' , dtVencimento )::integer;

        IF dtVencimento <= dtDataCalculo  THEN

            IF ( inAno > 2006 ) THEN                       -- Multa p/ 2007 em diante
                IF ( inDiff > 0 and inDiff <= 30 ) THEN
                    flMulta := ( nuValorComAcrescimo * 5) / 100 ;  -- 1 até 30 dias, aplica 5%
                ELSIF ( inDiff >=31  and inDiff <=60 ) THEN
                    flMulta := ( nuValorComAcrescimo * 10) / 100 ; -- 31 até 60 dias , aplica 10 %
                ELSIF ( inDiff >= 61 ) THEN
                    flMulta := ( nuValorComAcrescimo * 15) / 100 ; -- maior que 60 dias, aplica 15%
                ELSE
                    flMulta := 0.00;
                END IF;
            ELSE                                           -- Multa anterior a 2007
                IF ( inDiff > 0 and inDiff <= 30 ) THEN
                    flMulta := ( nuValorComAcrescimo * 5) / 100 ;  -- 1 até 30 dias, aplica 5%
                ELSIF ( inDiff >=31  and inDiff <=90 ) THEN
                    flMulta := ( nuValorComAcrescimo * 10) / 100 ; -- 31 até 90 dias , aplica 10 %
                ELSIF ( inDiff >= 91 ) THEN
                    flMulta := ( nuValorComAcrescimo * 15) / 100 ; -- maior que 90 dias, aplica 15%
                ELSE
                    flMulta := 0.00;
                END IF;
            END IF;
        END IF;

        RETURN flMulta::numeric(14,2);
    END;
'language 'plpgsql';
