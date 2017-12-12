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
* Caso de uso: uc-05.03.00
*/

/*
    Função de Calculo de Multa para Manaquiriã
     - Dado data de vencimento e de calculo, a função deve retornor o percentual a ser aplicado
*/

CREATE OR REPLACE FUNCTION fn_multa_mora_3_6_9_12(date,date,float,integer,integer) RETURNS numeric as $$

    DECLARE
        dtVencimento    ALIAS FOR $1;
        dtCorrecao      ALIAS FOR $2;
        flValorOrigem   ALIAS FOR $3;
        inCodAcrescimo  ALIAS FOR $4;
        inCodTipo       ALIAS FOR $5;
        flMulta         NUMERIC;
        inDiff          INTEGER;
        inAno           INTEGER;
        nuValorComAcrescimo NUMERIC;

    BEGIN
        -- recupera diferença em dias das datas
        inDiff := diff_datas_em_dias(dtVencimento,dtCorrecao);
        inAno  := date_part('year' , dtVencimento )::integer;

        IF dtVencimento <= dtCorrecao  THEN
            IF    ( inDiff >   0 and inDiff <= 30) THEN flMulta := ( flValorOrigem *  3) / 100; --  1 até 30 dias, aplica  3%
            ELSIF ( inDiff >= 31 and inDiff <= 60) THEN flMulta := ( flValorOrigem *  6) / 100; -- 31 até 60 dias, aplica  6%
            ELSIF ( inDiff >= 61 and inDiff <= 90) THEN flMulta := ( flValorOrigem *  9) / 100; -- 61 até 90 dias, aplica  9%
            ELSIF ( inDiff >= 91                 ) THEN flMulta := ( flValorOrigem * 12) / 100; --   após 90 dias, aplica 12%
            ELSE                                        flMulta := 0.00;
            END IF;
        END IF;

        RETURN flMulta::numeric(14,2);
    END;
$$ LANGUAGE 'plpgsql';
