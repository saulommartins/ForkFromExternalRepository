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

CREATE OR REPLACE FUNCTION contabilidade.fn_soma_valor_contabil(VARCHAR,INTEGER,VARCHAR,CHAR,VARCHAR,VARCHAR,VARCHAR,BOOLEAN) RETURNS NUMERIC AS $$
DECLARE
    reRecord                RECORD;
    crCursor                REFCURSOR;
    stExercicio             ALIAS FOR $1;
    inCodPlano              ALIAS FOR $2;
    stCodEstrutural         ALIAS FOR $3;
    chTipo                  ALIAS FOR $4;
    stDtInicial             ALIAS FOR $5;
    stDtFinal               ALIAS FOR $6;
    stEntidade              ALIAS FOR $7;
    boConsolidado           ALIAS FOR $8;
    boDtInicial             BOOLEAN := false;
    boDtFinal               BOOLEAN := false;
    dtInicial               VARCHAR := '';
    dtFinal                 VARCHAR := '';
    stFiltro                VARCHAR := '';
    stSql                   VARCHAR := '';
    inCount                 INTEGER := 1;
    nuOut                   NUMERIC := 0;
    nuDebito                NUMERIC := 0;
    nuCredito               NUMERIC := 0;

BEGIN
    boDtInicial := (trim(stDtInicial) != '');
    boDtFinal   := (trim(stDtFinal)   != '');

    stFiltro := ' AND vl.exercicio = '  || quote_literal(stExercicio);
    IF inCodPlano != 0 THEN
        stFiltro := stFiltro || ' AND pa.cod_plano = '  || inCodPlano;
    ELSE
        stFiltro := stFiltro || ' AND publico.fn_mascarareduzida( pc.cod_estrutural ) like '  || quote_literal( publico.fn_mascarareduzida( stCodEstrutural ) || '%');
    END IF;
    --Caso exista pelo menos uma vírgula, modifica a cláusula de filtro
    IF boConsolidado THEN
        IF stEntidade ~ '[,]' THEN
            stFiltro := stFiltro || ' AND la.cod_entidade IN (' || stEntidade || ') ';
        ELSE
            stFiltro := stFiltro || ' AND la.cod_entidade = ' || stEntidade;
        END IF;
    END IF;
    IF boDtInicial AND boDtFinal THEN
--      dtInicial   := to_date(stDtInicial,\'dd/mm/yyyy\');
--      dtFinal     := to_date(stDtFinal,\'dd/mm/yyyy\');
        dtInicial   := ' to_date( '|| quote_literal(stDtInicial) || ',' || quote_literal('dd/mm/yyyy') || ') ';
        dtFinal     := ' to_date( '|| quote_literal(stDtFinal)   || ',' || quote_literal('dd/mm/yyyy') || ') ';
    ELSE
        IF boDtInicial THEN
            dtInicial   := ' to_date( ' || quote_literal(stDtInicial)             || ',' || quote_literal('dd/mm/yyyy') || ') ';
            dtFinal     := ' to_date( ' || quote_literal('31/12/' || stExercicio) || ',' || quote_literal('dd/mm/yyyy') || ') ';
        ELSIF boDtFinal THEN
            dtInicial   := ' to_date( ' || quote_literal('01/01/'||stExercicio) || ',' || quote_literal('dd/mm/yyyy') || ') ';
            dtFinal     := ' to_date( ' || quote_literal(stDtFinal)             || ',' || quote_literal('dd/mm/yyyy') || ') ';
        END IF;
    END IF;
    IF boDtInicial OR boDtFinal THEN
        stFiltro := stFiltro || ' AND lo.dt_lote BETWEEN ' || dtInicial || ' AND ' || dtFinal;
    END IF;


    stSql := '
        SELECT
            coalesce(sum(vl.vl_lancamento),0)
        FROM
             contabilidade.plano_conta      as pc
            ,contabilidade.plano_analitica  as pa
            ,contabilidade.conta_debito     as cd
            ,contabilidade.valor_lancamento as vl

            ,contabilidade.lancamento       as la
            ,contabilidade.lote             as lo
        WHERE   pc.cod_conta = pa.cod_conta
        AND     pc.exercicio = pa.exercicio
        AND     pa.cod_plano = cd.cod_plano
        AND     pa.exercicio = cd.exercicio
        AND     cd.cod_lote  = vl.cod_lote
        AND     cd.tipo      = vl.tipo
        AND     cd.sequencia = vl.sequencia
        AND     cd.exercicio = vl.exercicio
        AND     cd.tipo_valor= vl.tipo_valor
        AND     cd.cod_entidade= vl.cod_entidade

        AND     vl.cod_lote  = la.cod_lote
        AND     vl.tipo      = la.tipo
        AND     vl.sequencia = la.sequencia
        AND     vl.exercicio = la.exercicio
        AND     vl.tipo      = la.tipo
        AND     vl.cod_entidade= la.cod_entidade
        AND     la.cod_lote  = lo.cod_lote
        AND     la.exercicio = lo.exercicio
        AND     la.tipo      = lo.tipo
        AND     la.cod_entidade=lo.cod_entidade
        '|| stFiltro ||'
        ';


    OPEN crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO nuDebito;
    CLOSE crCursor;

    stSql := '
        SELECT
            coalesce(sum(vl.vl_lancamento),0)
        FROM
             contabilidade.plano_conta      as pc
            ,contabilidade.plano_analitica  as pa
            ,contabilidade.conta_credito    as cc
            ,contabilidade.valor_lancamento as vl

            ,contabilidade.lancamento       as la
            ,contabilidade.lote             as lo
        WHERE   pc.cod_conta = pa.cod_conta
        AND     pc.exercicio = pa.exercicio
        AND     pa.cod_plano = cc.cod_plano
        AND     pa.exercicio = cc.exercicio
        AND     cc.cod_lote  = vl.cod_lote
        AND     cc.tipo      = vl.tipo
        AND     cc.sequencia = vl.sequencia
        AND     cc.exercicio = vl.exercicio
        AND     cc.tipo_valor= vl.tipo_valor
        AND     cc.cod_entidade= vl.cod_entidade

        AND     vl.cod_lote  = la.cod_lote
        AND     vl.tipo      = la.tipo
        AND     vl.sequencia = la.sequencia
        AND     vl.exercicio = la.exercicio
        AND     vl.tipo      = la.tipo
        AND     vl.cod_entidade= la.cod_entidade
        AND     la.cod_lote  = lo.cod_lote
        AND     la.exercicio = lo.exercicio
        AND     la.tipo      = lo.tipo
        AND     la.cod_entidade=lo.cod_entidade
        '|| stFiltro ||'
        ';


    OPEN crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO nuCredito;
    CLOSE crCursor;

    IF    trim(chTipo) = 'S' THEN
        nuOut := nuDebito + nuCredito;
    ELSIF trim(chTipo) = 'D' THEN
        nuOut := nuDebito;
    ELSIF trim(chTipo) = 'C' THEN
        nuOut := nuCredito;
    END IF;

    RETURN nuOut;
END;
$$ LANGUAGE 'plpgsql';
