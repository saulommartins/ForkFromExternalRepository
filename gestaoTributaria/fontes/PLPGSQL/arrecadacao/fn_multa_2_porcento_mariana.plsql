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
* $Id: multaSimplesDoisPorCento.plsql 29203 2008-04-15 14:45:04Z fabio $
*
* Caso de uso: uc-05.03.00
*/

/*
$Log$
*/

/*
    Função de Calculo de Multa 
     - Dado data de vencimento e de calculo, a função deve retornor o percentual a ser aplicado
*/

CREATE OR REPLACE FUNCTION fn_multa_2_porcento_mariana(date,date,numeric,integer,integer) RETURNS numeric as $$

    DECLARE
        dtVencimento    ALIAS FOR $1;
        dtDataCalculo   ALIAS FOR $2;
        flCorrigido     ALIAS FOR $3;
        inCodAcrescimo  ALIAS FOR $4;
        inCodTipo       ALIAS FOR $5;
        flCorrecao      NUMERIC;
        flMulta         NUMERIC;
        inDiff          INTEGER;
        inDiffMes       INTEGER;
    BEGIN

        flCorrecao:=fn_correcao_mariana(dtVencimento,dtDataCalculo,flCorrigido,5,1);
        -- recupera diferença em dias das datas
        inDiffMes := diff_datas_em_meses(dtVencimento,dtDataCalculo);
        IF ( inDiffMes = 0 ) THEN
            inDiffMes := inDiffMes + 0;
        ELSE
            inDiffMes := inDiffMes + 1;
        END IF;

--caso o vencimento seja anterior a 2004 a multa passa a ser de 2 por cento ao mes até o fonal de 2003
        IF (dtVencimento < '01-01-2004') THEN
           --inDiffMes := inDiffMes*2;
           inDiffMes := (diff_datas_em_meses(dtVencimento,'12-31-2003')*2) + diff_datas_em_meses('12-31-2003',dtDataCalculo);
        END IF;
 
        inDiff := diff_datas_em_dias( dtVencimento, dtDataCalculo );
        flMulta := 0.00;
        
        IF dtVencimento <= dtDataCalculo::date  THEN
            IF ( inDiff > 0 ) THEN
                flMulta := ( (flCorrigido + flCorrecao) * inDiffMes ) / 100; 
            END IF;
        END IF;

        RETURN flMulta::numeric(14,2);
    END;
$$ LANGUAGE 'plpgsql';
