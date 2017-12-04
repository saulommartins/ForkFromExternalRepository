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
* Casos de uso: uc-02.01.09, uc-02.01.11, uc-02.01.23
*/

/*
$Log$
Revision 1.6  2006/07/05 20:38:04  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION orcamento.fn_consolidado_liquidado_estornado(VARCHAR, VARCHAR, VARCHAR, INTEGER, INTEGER)  RETURNS numeric(14,2) AS $$
DECLARE
    dtInicial                   ALIAS FOR $1;
    dtFinal                     ALIAS FOR $2;
    stCodEstrutural             ALIAS FOR $3;
    inOrgao                     ALIAS FOR $4;
    inUnidade                   ALIAS FOR $5;
    stSql                       VARCHAR   := '';
    nuSoma                      NUMERIC   := 0;
    crCursor                    REFCURSOR;

    dataInicio                  VARCHAR   := '';

BEGIN
dtInicial   :=  replace(dtInicial,'''','');
dtFinal     :=  replace(dtFinal,'''','');
     stSql := '
        SELECT   coalesce(sum(valor),0.00)
        FROM
                 tmp_liquidado_estornado
        WHERE
                cod_estrutural like ' || quote_literal(stCodEstrutural || '%') || ' AND
                dataConsulta BETWEEN to_date('|| quote_literal(dtInicial) ||',''dd/mm/yyyy'') AND to_date('|| quote_literal(dtFinal) ||',''dd/mm/yyyy'')
        ';
                if (inOrgao <> '0') then
                    stSql := stSql || ' AND num_orgao = ' || inOrgao || '  ';
                end if;
                if (inUnidade <> '0') then
                    stSql := stSql || ' AND num_unidade = ' || inUnidade || '  ';
                end if;

    OPEN crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO nuSoma;
    CLOSE crCursor;

    RETURN nuSoma;
END;
$$ LANGUAGE 'plpgsql';
