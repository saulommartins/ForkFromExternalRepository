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
* Casos de uso: uc-02.02.02
*/

/*
$Log$
Revision 1.6  2006/07/05 20:37:31  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION contabilidade.fn_consulta_plano(VARCHAR,INTEGER) RETURNS VARCHAR AS $$ 

DECLARE
    reRecord            RECORD;
    stOut               VARCHAR := '';
    stSql               VARCHAR := '';
    stExercicio         ALIAS FOR $1;
    inCodConta          ALIAS FOR $2;

BEGIN

    stSql := '
        SELECT
             cla.cod_classificacao
            ,pos.mascara
        FROM
             contabilidade.classificacao_plano  as cla
            ,contabilidade.posicao_plano        as pos
        WHERE   cla.exercicio   = pos.exercicio
        AND     cla.cod_posicao = pos.cod_posicao
        AND     cla.exercicio   = '|| quote_literal(stExercicio) ||'
        AND     cla.cod_conta   = '|| quote_literal(inCodConta) ||'
        ORDER BY cla.cod_posicao
        ';

    FOR reRecord IN EXECUTE stSql LOOP
        stOut := stOut||'.'||sw_fn_mascara_dinamica ( ( case when reRecord.mascara = '' then '0' else reRecord.mascara end ) , cast( reRecord.cod_classificacao as VARCHAR) );
    END LOOP;

    stOut := SUBSTR(stOut,2,LENGTH(stOut));

    RETURN stOut;

END;
$$ LANGUAGE 'plpgsql';
