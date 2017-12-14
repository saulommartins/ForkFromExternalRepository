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
* $Id: $
*
* Caso de uso: uc-05.03.00
*/

/*
$Log$
*/

/*
    Função de Calculo de Multa para Mariana
     - Dado data de vencimento e de calculo, a função deve retornor o percentual a ser aplicado
*/

CREATE OR REPLACE FUNCTION fn_multa_mariana(date,date,numeric,integer,integer) RETURNS numeric as $$

    DECLARE
        dtVencimento    ALIAS FOR $1;
        dtDataCalculo   ALIAS FOR $2;
        flCorrigido     ALIAS FOR $3;
        inCodAcrescimo  ALIAS FOR $4;
        inCodTipo       ALIAS FOR $5;
        flMulta         NUMERIC;
        inDiffDia       INTEGER;    
        inDiffMes       INTEGER;    
    BEGIN
        -- recupera diferença em dias das datas
        inDiffDia := diff_datas_em_dias( dtVencimento, dtDataCalculo );
        inDiffMes := diff_datas_em_meses( dtVencimento, dtDataCalculo );
        flMulta := 0.00;

        IF dtVencimento <= dtDataCalculo  THEN
            IF ( inDiffDia > 0 ) AND ( inDiffMes < 1 ) THEN
                flMulta := ( flCorrigido * 1 ) / 100;                               -- 1 até 30 dias, aplica 1%
            ELSE 
                IF ( inDiffMes >= 1 ) THEN
                    flMulta := ( flCorrigido * ( 2 + ( 1 * inDiffMes ) ) ) / 100;   -- fechou 1 mes, aplica 3% (+ 1% para cada mes subsequente)
                END IF;
            END IF;
        END IF;

        RETURN flMulta::numeric(14,2);
    END;
$$ LANGUAGE 'plpgsql';
