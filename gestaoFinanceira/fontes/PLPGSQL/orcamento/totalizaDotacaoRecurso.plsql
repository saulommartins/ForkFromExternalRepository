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
* $Revision: 16394 $
* $Name$
* $Author: cako $
* $Date: 2006-10-04 14:07:01 -0300 (Qua, 04 Out 2006) $
*
* Casos de uso: uc-02.01.15
*/

/*
$Log$
Revision 1.7  2006/10/04 17:07:01  cako
Bug #7110#

Revision 1.6  2006/07/05 20:38:05  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION orcamento.fn_totaliza_dotacao_recurso(varchar,varchar) RETURNS numeric(14,2) AS $$
DECLARE
    stDotacao           ALIAS FOR $1;
    stTipoRecurso       ALIAS FOR $2;
    stSql               VARCHAR   := '';
    nuSoma              NUMERIC   := 0;
    crCursor            REFCURSOR;

BEGIN
    IF stTipoRecurso = 'ordinario' THEN
        stSql := 'SELECT
                    sum( vl_original ) as soma
                    FROM    tmp_despesa
                    WHERE   dotacao like ' || quote_literal(stDotacao||'%') || '
                    AND     tipo_recurso = ' || quote_literal('L');
                    
    ELSE
        stSql := 'SELECT
                    sum( vl_original ) as soma
                    FROM    tmp_despesa
                    WHERE   dotacao like ' || quote_literal(stDotacao||'%') || '
                    AND     tipo_recurso = ' || quote_literal('V');

    END IF;
    OPEN crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO nuSoma;
    CLOSE crCursor;

    RETURN nuSoma;
END;
$$language 'plpgsql';
