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
* CNM Confederação Nacional de Municípios
* www.cnm.org.br
*
* $Revision: 16925 $
* $Name$
* $Author: tonismar $
* $Date: 2006-10-18 16:12:53 -0300 (Qua, 18 Out 2006) $
*
* Casos de uso: uc-02.03.34
*/

/*

$Log: 

*/

CREATE OR REPLACE FUNCTION empenho.fn_somatorio_razao_credor_autorizado_anulado(INTEGER, INTEGER, VARCHAR)  RETURNS numeric(14,2) AS '
DECLARE
    inAutorizacao               ALIAS FOR $1;
    inEntidade                  ALIAS FOR $2;
    stExercicio                 ALIAS FOR $3;
    stSql                       VARCHAR   := '''';
    nuSoma                      NUMERIC   := 0;
    crCursor                    REFCURSOR;

BEGIN
     stSql := ''
        SELECT   coalesce(sum(valor),0.00)
        FROM
                 tmp_autorizado_anulado
        WHERE
                autorizado   =  '' || quote_literal(inAutorizacao) || '' AND
                entidade     =  '' || quote_literal(inEntidade)    || '' AND
                exercicio    =  '' || quote_literal(stExercicio);

    OPEN crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO nuSoma;
    CLOSE crCursor;

    RETURN nuSoma;
END;
' LANGUAGE 'plpgsql';
