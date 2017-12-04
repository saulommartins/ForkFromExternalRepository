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
* Casos de uso: uc-02.01.13, uc-02.01.14
*/

/*
$Log$
Revision 1.2  2006/07/05 20:38:05  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION orcamento.fn_totaliza_dotacao_pao_empenhado(varchar,varchar,varchar,integer) RETURNS numeric(14,2) AS $$
DECLARE
    stMascara           ALIAS FOR $1;
    stDigito            ALIAS FOR $2;
    stDotacao           ALIAS FOR $3;
    inPosicao           ALIAS FOR $4;
    stSql               VARCHAR   := '';
    nuSoma              NUMERIC   := 0;
    crCursor            REFCURSOR;

BEGIN
    stSql := 'SELECT
                coalesce(sum( valor ),0.00) as soma
                FROM    tmp_empenhado
                WHERE   dotacao_sem_ponto like '|| quote_literal(stDotacao||'%')||'
                AND     CAST(substr(sw_fn_mascara_dinamica( CAST('||stMascara||' as varchar) ,CAST(num_pao as varchar) ) , '||inPosicao||',1) as integer)::INTEGER IN ('||stDigito||')
                ';
    OPEN crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO nuSoma;
    CLOSE crCursor;

    RETURN nuSoma;
END;
$$ language 'plpgsql';
