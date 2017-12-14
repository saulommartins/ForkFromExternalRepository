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
* $Revision:$
* $Name$
* $Author:$
* $Date:$
*
*/

CREATE OR REPLACE FUNCTION tcmba.fn_somatorio_demonstrativo_consolidado_receita_estorno(VARCHAR, VARCHAR, VARCHAR)  RETURNS numeric(14,2) AS $$

DECLARE
    stCodEstrutural ALIAS FOR $1;
    dtInicial       ALIAS FOR $2;
    dtFinal         ALIAS FOR $3;
    stSql           VARCHAR   := '';
    nuSoma          NUMERIC   := 0;
    crCursor        REFCURSOR;

BEGIN
     stSql := '
        SELECT  sum(valor_estorno)
          FROM  tmp_valor
         WHERE  cod_estrutural LIKE ''' || stCodEstrutural || '%''
           AND  data BETWEEN to_date('|| quote_literal(dtInicial) ||',''dd/mm/yyyy'') AND to_date('|| quote_literal(dtFinal) ||', ''dd/mm/yyyy'')' ;

    OPEN crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO nuSoma;
    CLOSE crCursor;

    RETURN nuSoma;
END;

$$ LANGUAGE 'plpgsql';
