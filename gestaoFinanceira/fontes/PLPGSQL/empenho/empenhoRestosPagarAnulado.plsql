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
* Casos de uso: uc-02.03.08
*/

/*
$Log$
Revision 1.6  2006/07/05 20:37:38  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION empenho.fn_empenho_restos_pagar_anulado(varchar,integer,varchar,varchar,varchar) RETURNS numeric(14,2) AS '
DECLARE
    stExercicio             ALIAS FOR $1;
    stCodEmpenho            ALIAS FOR $2;
    stCodEntidades          ALIAS FOR $3;
    dtInicial               ALIAS FOR $4;
    dtFinal                 ALIAS FOR $5;
    stSql                   VARCHAR   := '''';
    nuSoma                  NUMERIC   := 0;
    crCursor                REFCURSOR;

BEGIN
    stSql := ''
        SELECT
            coalesce(sum( eai.vl_anulado ),0.00) as soma
        FROM
            empenho.empenho e,
            empenho.empenho_anulado ea,
            empenho.empenho_anulado_item eai
        WHERE
            e.exercicio     = '''''' || stExercicio || '''''' AND
            e.cod_entidade  IN ( '' || stCodEntidades || '' ) AND
            e.cod_empenho   = '' || stCodEmpenho || '' AND
            e.dt_empenho    BETWEEN to_date('''''' || dtInicial || '''''',''''dd/mm/yyyy'''') AND to_date('''''' || dtFinal || '''''',''''dd/mm/yyyy'''') AND

            e.exercicio     = ea.exercicio      AND
            e.cod_entidade  = ea.cod_entidade   AND
            e.cod_empenho   = ea.cod_empenho    AND

            ea.exercicio    = eai.exercicio     AND
            ea.cod_entidade = eai.cod_entidade  AND
            ea.cod_empenho  = eai.cod_empenho
    '';

    OPEN crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO nuSoma;
    CLOSE crCursor;

    RETURN nuSoma;
END;
'language 'plpgsql';
