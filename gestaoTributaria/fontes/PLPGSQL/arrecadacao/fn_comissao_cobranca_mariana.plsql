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
* Caso de uso: uc-05.05.00
*/

/*
    Função de Calculo de Comissao de Cobrança para Mariana
     - Dado data de vencimento e de calculo, a função deve retornor o percentual a ser aplicado
*/

CREATE OR REPLACE FUNCTION fn_comissao_cobranca_mariana(date,date,numeric,integer,integer) RETURNS numeric as '

    DECLARE
        dtVencimento    ALIAS FOR $1;
        dtDataCalculo   ALIAS FOR $2;
        flCorrigido     ALIAS FOR $3;
        inCodAcrescimo  ALIAS FOR $4;
        inCodTipo       ALIAS FOR $5;
        flMulta         NUMERIC;
        flCorrecao      NUMERIC;  
        flMulta2        NUMERIC; 
        flJuro          NUMERIC; 
        inDiff          INTEGER;
        inDiffMes       INTEGER;
        inJudicial      INTEGER;
    BEGIN
        -- recupera diferença em dias das datas
        inDiffMes := diff_datas_em_meses(dtVencimento,dtDataCalculo);
        IF ( inDiffMes = 0 ) THEN
            inDiffMes := inDiffMes + 0;
        ELSE
            inDiffMes := inDiffMes + 1;
        END IF;

        flCorrecao  :=fn_correcao_mariana(dtVencimento,dtDataCalculo,flCorrigido,1,1);
        flMulta2    := fn_multa_2_porcento_mariana(dtVencimento,dtDataCalculo,flCorrigido,3,3);
        flJuro      :=fn_juros_mariana(dtVencimento,dtDataCalculo,flCorrigido,2 , 2);

 
        inDiff := diff_datas_em_dias( dtVencimento, dtDataCalculo );
        flMulta := 0.00;
        
        IF dtVencimento < ''01-01-2004'' THEN
            inDiffMes := diff_datas_em_meses(dtVencimento,''12-31-2003'');
            IF dtVencimento <= dtDataCalculo  THEN
                IF ( inDiff > 0 ) THEN
                    flMulta := ( (flCorrigido + flCorrecao + flMulta2 + flJuro) * inDiffMes ) / 100;
                END IF;
            END IF;
        END IF;

        RETURN flMulta::numeric(14,2);
    END;
'language 'plpgsql';
