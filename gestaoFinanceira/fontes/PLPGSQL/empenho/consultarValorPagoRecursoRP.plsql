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
* Script de função PLPGSQL
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Revision: 12203 $
* $Name$
* $Author: cleisson $
* $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $
*
* Casos de uso: uc-02.01.29
*/

/*
$Log$
Revision 1.5  2006/07/05 20:37:38  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION empenho.fn_consultar_valor_pago_recurso_rp(VARCHAR,VARCHAR,INTEGER,VARCHAR,VARCHAR) RETURNS NUMERIC AS '

DECLARE
    stExercicio                ALIAS FOR $1;
    stCodEntidade              ALIAS FOR $2;
    inCodRecurso               ALIAS FOR $3;
    stDtInicial                ALIAS FOR $4;
    stDtFinal                  ALIAS FOR $5;
    stSql                      VARCHAR := '''';
    crCursor                   REFCURSOR;
    nuValor                    NUMERIC := 0;
    nuValorImplantado          NUMERIC := 0;

BEGIN

    -- Pega valor de resto NÃO implantado
    SELECT empenho.fn_consultar_valor_pago_recurso(stExercicio,stCodEntidade,inCodRecurso,stDtInicial,stDtFinal,''R'') INTO nuValor;

    -- Pega valor de resto implantado
     stSql := ''SELECT
                    coalesce(sum(NLP.vl_pago),0.00)
                FROM    empenho.restos_pre_empenho   AS ERPE
                       ,empenho.pre_empenho          AS EPE
                       ,empenho.empenho              AS EE
                       ,empenho.nota_liquidacao      AS NL
                       ,empenho.nota_liquidacao_paga AS NLP
                WHERE   NLP.cod_nota         = NL.cod_nota
                AND     NLP.exercicio        = NL.exercicio
                AND     NLP.cod_entidade     = NL.cod_entidade
                AND     NL.exercicio_empenho = EE.exercicio
                AND     NL.cod_entidade      = EE.cod_entidade
                AND     NL.cod_empenho       = EE.cod_empenho
                AND     EE.cod_pre_empenho   = EPE.cod_pre_empenho
                AND     EE.exercicio         = EPE.exercicio
                AND     EPE.cod_pre_empenho  = ERPE.cod_pre_empenho
                AND     EPE.exercicio        = ERPE.exercicio
                AND     ERPE.recurso         = '' || inCodRecurso || ''
                AND     EE.exercicio         < '''''' || stExercicio || ''''''
                AND     EE.cod_entidade      IN ( '' || stCodEntidade || '' )
                AND     TO_DATE( NLP.timestamp, ''''yyyy-mm-dd'''' ) BETWEEN
                                    TO_DATE( '''''' || stDtInicial || '''''', ''''dd/mm/yyyy'''' )
                                AND TO_DATE( '''''' || stDtFinal   || '''''', ''''dd/mm/yyyy'''' )
    '';


    OPEN crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO nuValorImplantado;
    CLOSE crCursor;

    IF nuValorImplantado IS NULL THEN
        nuValorImplantado := 0.00;
    END IF;

    RETURN nuValor + nuValorImplantado;

END;
'LANGUAGE 'plpgsql';
