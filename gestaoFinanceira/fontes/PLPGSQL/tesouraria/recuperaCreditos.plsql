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
* Casos de uso: uc-02.04.04
* Casos de uso: uc-02.04.04
*/

/*
$Log$
Revision 1.4  2006/07/05 20:38:11  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION tesouraria.fn_recupera_creditos(varchar, varchar, varchar, varchar, varchar, varchar, varchar, varchar) RETURNS SETOF RECORD AS '
DECLARE
    stEntidade          ALIAS FOR $1;
    stNumeracao         ALIAS FOR $2;
    stExercicio         ALIAS FOR $3;
    stPlanoBanco        ALIAS FOR $4;
    stDataArrecadacao   ALIAS FOR $5;
    stDataEstorno       ALIAS FOR $6;
    stCredito           ALIAS FOR $7;
    stSituacao          ALIAS FOR $8;

    stSql               VARCHAR   := '''';
    reRegistro          RECORD;

BEGIN

stSql := ''
    SELECT
        ca.cod_credito,
        ca.cod_especie,
        ca.cod_genero,
        ca.cod_natureza,
        ca.valor as vl_documento,
        de.valor as vl_desconto,
--      geradorcalculos.funcao(fa1.cod_funcao) as vl_multa,
--      geradorcalculos.funcao(fa2.cod_funcao) as vl_juro
    FROM
        tesouraria.arrecadacao          as a,
        arrecadacao.carne               as ca,
        arrecadacao.parcela             as pa,
        arrecadacao.lancamento          as la,
        arrecadacao.lancamento_calculo  as lc,
        arrecadacao.calculo             as c
            LEFT OUTER JOIN (
                SELECT
                    gv.cod_vencimento,
                    gv.cod_grupo,
                    de.valor
                FROM
                    arrecadacao.grupo_vencimento    as gv,
                    arrecadacao.desconto            as de
                WHERE
                    gv.cod_vencimento   = de.cod_vencimento AND
                    gv.cod_grupo        = de.cod_grupo
            ) as de ON (
                c.cod_vencimento    = de.cod_vencimento AND
                c.cod_grupo         = de.cod_grupo
            )
            LEFT OUTER JOIN (
                SELECT
                    cr.cod_credito,
                    cr.cod_natureza,
                    cr.cod_genero,
                    cr.cod_especie,
                    fa.cod_funcao
                FROM
                    monetario.credito           as cr,
                    monetario.credito_acrescimo as cra,
                    monetario.acrescimo         as a,
                    monetario.tipo_acrescimo    as ta,
                    monetario.formula_acrescimo as fa
                WHERE
                    cr.cod_credito      = cra.cod_credito       AND
                    cr.cod_natureza     = cra.cod_natureza      AND
                    cr.cod_genero       = cra.cod_genero        AND
                    cr.cod_especie      = cra.cod_especie       AND

                    cra.cod_acrescimo   = a.cod_acrescimo       AND

                    a.cod_tipo          = ta.cod_tipo           AND
                    ta.cod_tipo         = 1                     AND

                    a.cod_acrescimo     = fa.cod_acrescimo
            ) as fa1 ON (
                cr.cod_credito  = fa1.cod_credito   AND
                cr.cod_natureza = fa1.cod_natureza  AND
                cr.cod_genero   = fa1.cod_genero    AND
                cr.cod_especie  = fa1.cod_especie   AND
            )
            LEFT OUTER JOIN (
                SELECT
                    cr.cod_credito,
                    cr.cod_natureza,
                    cr.cod_genero,
                    cr.cod_especie,
                    fa.cod_funcao
                FROM
                    monetario.credito           as cr,
                    monetario.credito_acrescimo as cra,
                    monetario.acrescimo         as a,
                    monetario.tipo_acrescimo    as ta,
                    monetario.formula_acrescimo as fa
                WHERE
                    cr.cod_credito      = cra.cod_credito       AND
                    cr.cod_natureza     = cra.cod_natureza      AND
                    cr.cod_genero       = cra.cod_genero        AND
                    cr.cod_especie      = cra.cod_especie       AND

                    cra.cod_acrescimo   = a.cod_acrescimo       AND

                    a.cod_tipo          = ta.cod_tipo           AND
                    ta.cod_tipo         = 2                     AND

                    a.cod_acrescimo     = fa.cod_acrescimo
            ) as fa2 ON (
                cr.cod_credito  = fa1.cod_credito   AND
                cr.cod_natureza = fa1.cod_natureza  AND
                cr.cod_genero   = fa1.cod_genero    AND
                cr.cod_especie  = fa1.cod_especie   AND
            )
    WHERE
        a.numeracao         = ca.numeracao      AND
        ca.cod_parcela      = pa.cod_parcela    AND
        pa.cod_lancamento   = la.cod_lancamento AND
        la.cod_lancamento   = lc.cod_lancamento AND
        lc.cod_calculo      = c.cod_calculo
'';

if (stEntidade is not null and stEntidade <> '''') then
    stSql := stSql || ''
        AND a.cod_entidade  = '' || stEntidade || ''
    '';
end if;

if (stNumeracao is not null and stNumeracao <> '''') then
    stSql := stSql || ''
        AND a.numeracao     = '''''' || stNumeracao || ''''''
    '';
end if;

if (stExercicio is not null and stExercicio <> '''') then
    stSql := stSql || ''
        AND a.exercicio     = '''''' || stExercicio || ''''''
    '';
end if;

if (stPlanoBanco is not null and stPlanoBanco <> '''') then
    stSql := stSql || ''
        AND a.cod_plano  = '' || stPlanoBanco || ''
    '';
end if;

if (stDataArrecadacao is not null and stDataArrecadacao <> '''') then
    stSql := stSql || ''
        AND to_date(to_char(a.timestamp,''''dd/mm/yyyy''''),''''dd/mm/yyyy'''') = to_date('''''' || stDataArrecadacao || ''''''),''''dd/mm/yyyy'''')
    '';
end if;

if (stDataEstorno is not null and stDataEstorno <> '''') then
    stSql := stSql || ''
        AND to_date(to_char(a.timestamp_estornado,''''dd/mm/yyyy''''),''''dd/mm/yyyy'''') = to_date('''''' || stDataEstorno || ''''''),''''dd/mm/yyyy'''')
    '';
end if;

if (stCredito is not null and stCredito <> '''') then
    stSql := stSql || ''
        AND ca.cod_especie || ''''.'''' || ca.cod_genero || ''''.'''' || ca.cod_natureza || ''''.'''' || ca.cod_credito = '''''' || stCredito || ''''''
    '';
end if;


FOR reRegistro IN EXECUTE stSql
LOOP
    RETURN next reRegistro;
END LOOP;

RETURN;

END;

'language 'plpgsql';
