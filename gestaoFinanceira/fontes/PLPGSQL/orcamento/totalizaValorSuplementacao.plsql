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
* $Id: totalizaValorSuplementacao.plsql 66074 2016-07-15 21:46:22Z michel $
*
* Casos de uso: uc-02.01.24, uc-02.01.07, uc-02.01.25
*/

CREATE OR REPLACE FUNCTION orcamento.fn_totaliza_suplementacao( VARCHAR,INTEGER ) RETURNS numeric(14,2) AS $$ 
DECLARE
    stExercicio         ALIAS FOR $1;
    inCodSuplementacao  ALIAS FOR $2;
    stSql               VARCHAR := '''';
    nuSoma              NUMERIC := 0;
    reRegistro          RECORD;
    crCursor            REFCURSOR;

BEGIN
    stSql := '
            SELECT
                (SELECT
                    sum(SS.valor) as suplementado
                FROM
                    orcamento.suplementacao S
                    LEFT JOIN orcamento.suplementacao_suplementada SS ON
                        SS.cod_suplementacao = S.cod_suplementacao AND
                        SS.exercicio = S.exercicio
                WHERE
                    S.exercicio = ' || quote_literal(stExercicio) || ' AND
                    S.cod_suplementacao = ' || inCodSuplementacao || '
                ) as suplementado
                ';

    OPEN crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO reRegistro;
    CLOSE crCursor;

    nuSoma := coalesce(reRegistro.suplementado,0.00);

    RETURN nuSoma;
END;
$$ language 'plpgsql';
