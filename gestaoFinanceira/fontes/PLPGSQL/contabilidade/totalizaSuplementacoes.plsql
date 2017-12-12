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
* Casos de uso: uc-02.02.15
*/

/*
$Log$
Revision 1.7  2006/07/05 20:37:31  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION contabilidade.fn_totaliza_suplementacoes( VARCHAR ) RETURNS numeric[] AS '

DECLARE
    stMascaraReduzida     ALIAS FOR $1;
    stSql                 VARCHAR   := '''';
    stSqlComplemento      VARCHAR   := '''';
    nuTotalSuplementado   NUMERIC   := 0;
    nuTotalReduzido       NUMERIC   := 0;
    nuTotalValorOriginal  NUMERIC   := 0;
    nuCreditoOrcamentario NUMERIC   := 0;
    arRetorno             NUMERIC[] := array[0];
    crCursor              REFCURSOR;
    reRegistro            RECORD;


BEGIN

    --totaliza valor original da conta informada a partir do codigo estrutural
    stSql := '' SELECT
                    SUM( valor ) as valor_original
                FROM
                    tmp_valor_original
                WHERE
                    cod_estrutural LIKE '''''' || stMascaraReduzida || ''%''''
             '';

    OPEN  crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO nuTotalValorOriginal;
    CLOSE crCursor;


    --totaliza SUPLEMENTACAO AUMENTADA para caclcular CREDITOS ORCAMENTARIOS E SUPLEMENTACOES
    stSql := '' SELECT
                    SUM( valor ) as valor_suplementado
                FROM
                    tmp_suplementacao_suplementada
                WHERE
                        cod_tipo IN (1,2,3,4,5,12,13,14,15)
                    AND cod_estrutural LIKE '''''' || stMascaraReduzida || ''%''''
             '';

    OPEN  crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO nuTotalSuplementado;
    CLOSE crCursor;

    --totaliza SUPLEMENTACAO REDUZIDA para caclcular CREDITOS ORCAMENTARIOS E SUPLEMENTACOES
    stSql := '' SELECT
                    SUM( valor ) as valor_reduzido
                FROM
                    tmp_suplementacao_reduzida
                WHERE
                        cod_tipo IN (1,2,3,4,5,12,13,14,15)
                    AND cod_estrutural LIKE '''''' || stMascaraReduzida || ''%''''
             '';

    OPEN  crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO nuTotalReduzido;
    CLOSE crCursor;

    nuTotalSuplementado := coalesce( nuTotalSuplementado , 0.00);
    nuTotalReduzido     := coalesce( nuTotalReduzido , 0.00);

    --Preenche array de retorno
    arRetorno[1] := nuTotalSuplementado;
    arRetorno[2] := nuTotalReduzido;

    --totaliza SUPLEMENTACAO AUMENTADA para caclcular CREDITOS ESPECIAIS E EXTRAORDINARIOS
    stSql := '' SELECT
                    SUM( valor ) as valor_suplementado
                FROM
                    tmp_suplementacao_suplementada
                WHERE
                        cod_tipo IN (6,7,8,9,10,11)
                    AND cod_estrutural LIKE '''''' || stMascaraReduzida || ''%''''
             '';

    OPEN  crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO nuTotalSuplementado;
    CLOSE crCursor;

    --totaliza SUPLEMENTACAO REDUZIDA para caclcular CREDITOS ESPECIAIS E EXTRAORDINARIOS
    stSql := '' SELECT
                    SUM( valor ) as valor_reduzido
                FROM
                    tmp_suplementacao_reduzida
                WHERE
                        cod_tipo IN (6,7,8,9,10,11)
                    AND cod_estrutural LIKE '''''' || stMascaraReduzida || ''%''''
             '';

    OPEN  crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO nuTotalReduzido;
    CLOSE crCursor;

    nuTotalSuplementado := coalesce( nuTotalSuplementado , 0.00);
    nuTotalReduzido     := coalesce( nuTotalReduzido , 0.00);

    --Preenche array de retorno
    arRetorno[3] := nuTotalSuplementado;
    arRetorno[4] := nuTotalReduzido;
    arRetorno[5] := coalesce( nuTotalValorOriginal , 0.00);

    --totaliza valor total de liquidacao
    stSql := '' SELECT
                    SUM( coalesce(vl_total,0.00) ) as vl_total,
                    SUM( coalesce(vl_anulado,0.00) ) as vl_anulado
                FROM
                    tmp_total_liquidacao
                WHERE
                    cod_estrutural LIKE '''''' || stMascaraReduzida || ''%''''
             '';

    FOR reRegistro IN EXECUTE stSql LOOP
        arRetorno[6] := reRegistro.vl_total - reRegistro.vl_anulado;
        arRetorno[6] := coalesce( arRetorno[6] , 0.00);
    END LOOP;

    RETURN arRetorno;
END;
' LANGUAGE 'plpgsql'
