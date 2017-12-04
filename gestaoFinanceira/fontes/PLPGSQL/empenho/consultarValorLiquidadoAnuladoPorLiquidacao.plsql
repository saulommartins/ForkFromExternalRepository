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
* @author Analista Gelson
* @author Desenvolvedor Vitor Hugo

*
* $Revision: 25070 $
* $Name$
* $Author: vitor $
* $Date: 2007-08-24 17:21:27 -0300 (Sex, 24 Ago 2007) $
*
* Casos de uso: uc-02.03.24
*/

/*
$Log$
Revision 1.1  2007/08/24 20:21:27  vitor
Bug#6005#


*/

CREATE OR REPLACE FUNCTION empenho.fn_consultar_valor_liquidado_anulado_por_liquidacao(VARCHAR,INTEGER,INTEGER,INTEGER,INTEGER) RETURNS SETOF RECORD AS '

DECLARE
    stExercicio                ALIAS FOR $1;
    inCodEmpenho               ALIAS FOR $2;
    inCodEntidade              ALIAS FOR $3;
    inCodNotaLiquidacao        ALIAS FOR $4;
    inNumItemNotaLiquidacao    ALIAS FOR $5;
    stSql                  VARCHAR   := '''';
    reRegistro             RECORD;

BEGIN

stSql := ''
    SELECT
        NL.cod_nota,
        cast(to_char(NL.dt_liquidacao,''''dd/mm/yyyy'''') as text), 
        LI.vl_total,
        IA.vl_anulado,
        cast(to_char(IA.timestamp,''''dd/mm/yyyy'''') as text)

    FROM     empenho.empenho                       AS  E
            ,empenho.nota_liquidacao               AS NL
            ,empenho.nota_liquidacao_item          AS LI
            ,empenho.nota_liquidacao_item_anulado  AS IA
    WHERE   E.cod_entidade       = ''||inCodEntidade||''
    AND     E.cod_empenho        = ''||inCodEmpenho||''
    AND     E.exercicio          = ''''''||stExercicio||''''''
    AND     NL.cod_nota          = ''||inCodNotaLiquidacao||''
    AND     LI.num_item          = ''||inNumItemNotaLiquidacao||''

    AND     NL.exercicio_empenho = E.exercicio
    AND     NL.cod_empenho       = E.cod_empenho
    AND     NL.cod_entidade      = E.cod_entidade

    AND     LI.exercicio         = NL.exercicio
    AND     LI.cod_nota          = NL.cod_nota
    AND     LI.cod_entidade      = NL.cod_entidade

    AND     IA.cod_entidade      = LI.cod_entidade
    AND     IA.cod_nota          = LI.cod_nota
    AND     IA.exercicio         = LI.exercicio
    AND     IA.num_item          = LI.num_item
    AND     IA.cod_pre_empenho   = LI.cod_pre_empenho
    AND     IA.exercicio_item    = LI.exercicio_item
'';


FOR reRegistro IN EXECUTE stSql
LOOP
    RETURN NEXT reRegistro;
END LOOP;

RETURN;

END;
'LANGUAGE 'plpgsql';
