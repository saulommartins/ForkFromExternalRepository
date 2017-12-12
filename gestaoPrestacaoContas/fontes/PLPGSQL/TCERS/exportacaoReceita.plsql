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
* $Revision: 59612 $
* $Name$
* $Author: gelson $
* $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $
*
* Casos de uso: uc-02.00.00
* Casos de uso: uc-02.08.07

$Id: exportacaoReceita.plsql 59612 2014-09-02 12:00:51Z gelson $

*/

CREATE OR REPLACE FUNCTION tcers.fn_exportacao_receita(varchar,varchar,integer,integer) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stCodEntidades      ALIAS FOR $2;
    inPeriodo           ALIAS FOR $3;
    inTipoPeriodo       ALIAS FOR $4;

    dtInicioAno         VARCHAR   := '';
    dtFimAno            VARCHAR   := '';
    stSql               VARCHAR   := '';
    stMascClassReceita  VARCHAR   := '';
    stMascRecurso       VARCHAR   := '';
    arDataInicial       VARCHAR;
    arDataFinal         VARCHAR;
    inCount             INTEGER;
    stExecute           VARCHAR;
    nuRetorno           NUMERIC;
    reRegistro          RECORD;

BEGIN
        stSql := 'CREATE TEMPORARY TABLE tmp_valor AS (
            SELECT
                ocr.cod_estrutural as cod_estrutural, lote.dt_lote as data, vl.vl_lancamento as valor, vl.oid as primeira, ore.cod_recurso
            FROM
                contabilidade.valor_lancamento      as vl   ,
                orcamento.conta_receita             as ocr  ,
                orcamento.receita                   as ore  ,
                contabilidade.lancamento_receita    as lr   ,
                contabilidade.lancamento            as lan  ,
                contabilidade.lote                  as lote
            WHERE
                    ore.cod_entidade    IN (' || stCodEntidades || ')
                AND ore.exercicio       = ' || quote_literal(stExercicio) || '
                AND ocr.cod_conta       = ore.cod_conta
                AND ocr.exercicio       = ore.exercicio

                -- join lancamento receita
                AND lr.cod_receita      = ore.cod_receita
                AND lr.exercicio        = ore.exercicio
                AND lr.estorno          = true
                -- tipo de lancamento receita deve ser = A , de arrecadação
                AND lr.tipo             = ''A''

                -- join nas tabelas lancamento_receita e lancamento
                AND lan.cod_lote        = lr.cod_lote
                AND lan.sequencia       = lr.sequencia
                AND lan.exercicio       = lr.exercicio
                AND lan.cod_entidade    = lr.cod_entidade
                AND lan.tipo            = lr.tipo

                -- join nas tabelas lancamento e valor_lancamento
                AND vl.exercicio        = lan.exercicio
                AND vl.sequencia        = lan.sequencia
                AND vl.cod_entidade     = lan.cod_entidade
                AND vl.cod_lote         = lan.cod_lote
                AND vl.tipo             = lan.tipo
                -- na tabela valor lancamento  tipo_valor deve ser credito
                AND vl.tipo_valor       = ''D''

                AND lote.cod_lote       = lan.cod_lote
                AND lote.cod_entidade   = lan.cod_entidade
                AND lote.exercicio      = lan.exercicio
                AND lote.tipo           = lan.tipo

            UNION

            SELECT
                ocr.cod_estrutural as cod_estrutural, lote.dt_lote as data, vl.vl_lancamento as valor,vl.oid as segunda, ore.cod_recurso
            FROM
                contabilidade.valor_lancamento      as vl   ,
                orcamento.conta_receita             as ocr  ,
                orcamento.receita                   as ore  ,
                contabilidade.lancamento_receita    as lr   ,
                contabilidade.lancamento            as lan  ,
                contabilidade.lote                  as lote

            WHERE
                    ore.cod_entidade    IN(' || stCodEntidades || ')
                AND ore.exercicio       = ' || quote_literal(stExercicio) || '
                AND ocr.cod_conta       = ore.cod_conta
                AND ocr.exercicio       = ore.exercicio

                -- join lancamento receita
                AND lr.cod_receita      = ore.cod_receita
                AND lr.exercicio        = ore.exercicio
                AND lr.estorno          = false
                -- tipo de lancamento receita deve ser = A , de arrecadação
                AND lr.tipo             = ''A''

                -- join nas tabelas lancamento_receita e lancamento
                AND lan.cod_lote        = lr.cod_lote
                AND lan.sequencia       = lr.sequencia
                AND lan.exercicio       = lr.exercicio
                AND lan.cod_entidade    = lr.cod_entidade
                AND lan.tipo            = lr.tipo


                -- join nas tabelas lancamento e valor_lancamento
                AND vl.exercicio        = lan.exercicio
                AND vl.sequencia        = lan.sequencia
                AND vl.cod_entidade     = lan.cod_entidade
                AND vl.cod_lote         = lan.cod_lote
                AND vl.tipo             = lan.tipo

                -- na tabela valor lancamento  tipo_valor deve ser credito
                AND vl.tipo_valor       = ''C''

                -- Data Inicial e Data Final, antes iguala codigo do lote
                AND lote.cod_lote       = lan.cod_lote
                AND lote.cod_entidade   = lan.cod_entidade
                AND lote.exercicio      = lan.exercicio
                AND lote.tipo           = lan.tipo )';

        EXECUTE stSql;

        stSql := 'CREATE TEMPORARY TABLE tmp_valor_metas AS (
                    SELECT  cr.cod_estrutural
                            ,pr.vl_periodo
                            ,periodo
			    , re.cod_recurso
                    FROM    orcamento.conta_receita             as cr
                            ,orcamento.receita                  as re
                            ,orcamento.previsao_receita         as pr
                    WHERE   cr.exercicio                = re.exercicio
                    AND     cr.cod_conta                = re.cod_conta
                    AND     re.exercicio                = pr.exercicio
                    AND     re.cod_receita              = pr.cod_receita
                    AND     re.cod_entidade             IN('|| stCodEntidades ||')
                    AND     cr.exercicio                = '|| quote_literal(stExercicio) ||'
                    )
                  ';

        EXECUTE stSql;

        stSql := '
                SELECT
                    ocr.cod_estrutural as cod_estrutural
                  ' ;
                FOR inCount IN 1..(inPeriodo*inTipoPeriodo) LOOP
                    arDataInicial := '01/' || trim(to_char(inCount,'00')) || '/' || stExercicio;
                    arDataFinal   := TO_CHAR(TO_DATE('01/'|| (inCount + 1) ||'/'|| stExercicio,'dd/mm/yyyy') -1,'dd/mm/yyyy');
                    stSql := stSql || ' ,orcamento.fn_somatorio_balancete_receita(publico.fn_mascarareduzida(cod_estrutural), ' || quote_literal(arDataInicial) || ' , ' || quote_literal(arDataFinal) || ' ) as receita_mes' || inCount;
                END LOOP;
                FOR inCount IN ((inPeriodo*inTipoPeriodo)+1)..12 LOOP
                    stSql := stSql || ' ,null::numeric as receita_mes'|| inCount;
                END LOOP;
   
                FOR inCount IN 1..6 LOOP
                    stSql := stSql || ' ,tcers.totaliza_meta_arrecadacao(publico.fn_mascarareduzida(cod_estrutural), ' || quote_literal(stExercicio) || ',' || inCount || ' ) as meta_arrecadacao' || inCount;
                END LOOP;
                stSql := stSql || '
                    ,rcpr.cod_caracteristica
			, rec.cod_recurso
                FROM
                    orcamento.conta_receita ocr
                    LEFT JOIN orcamento.receita as rec
                    ON (    ocr.exercicio = rec.exercicio
                        AND ocr.cod_conta = rec.cod_conta
                    )
                    LEFT JOIN tcers.receita_carac_peculiar_receita as rcpr
                    ON (    rec.exercicio = rcpr.exercicio
                        AND rec.cod_receita = rcpr.cod_receita 
                    )
                WHERE ocr.exercicio = ' || quote_literal(stExercicio) || '
                ORDER BY cod_estrutural
                 ';

    FOR reRegistro IN EXECUTE stSql
    LOOP
       
        IF reRegistro.cod_caracteristica IS NULL THEN 
            reRegistro.cod_caracteristica := 000;
        END IF;

        IF  reRegistro.receita_mes1 IS NOT NULL
            OR  reRegistro.receita_mes2 IS NOT NULL
            OR  reRegistro.receita_mes3 IS NOT NULL
            OR  reRegistro.receita_mes4 IS NOT NULL
            OR  reRegistro.receita_mes5 IS NOT NULL
            OR  reRegistro.receita_mes6 IS NOT NULL
            OR  reRegistro.receita_mes7 IS NOT NULL
            OR  reRegistro.receita_mes8 IS NOT NULL
            OR  reRegistro.receita_mes9 IS NOT NULL
            OR  reRegistro.receita_mes10 IS NOT NULL
            OR  reRegistro.receita_mes11 IS NOT NULL
            OR  reRegistro.receita_mes12 IS NOT NULL
            OR  reRegistro.meta_arrecadacao1 <> 0.00
            OR  reRegistro.meta_arrecadacao2 <> 0.00
            OR  reRegistro.meta_arrecadacao3 <> 0.00
            OR  reRegistro.meta_arrecadacao4 <> 0.00
            OR  reRegistro.meta_arrecadacao5 <> 0.00
            OR  reRegistro.meta_arrecadacao6 <> 0.00
        THEN
            reRegistro.receita_mes1 := reRegistro.receita_mes1 * -1;
            reRegistro.receita_mes2 := reRegistro.receita_mes2 * -1;
            reRegistro.receita_mes3 := reRegistro.receita_mes3 * -1;
            reRegistro.receita_mes4 := reRegistro.receita_mes4 * -1;
            reRegistro.receita_mes5 := reRegistro.receita_mes5 * -1;
            reRegistro.receita_mes6 := reRegistro.receita_mes6 * -1;
            reRegistro.receita_mes7 := reRegistro.receita_mes7 * -1;
            reRegistro.receita_mes8 := reRegistro.receita_mes8 * -1;
            reRegistro.receita_mes9 := reRegistro.receita_mes9 * -1;
            reRegistro.receita_mes10 := reRegistro.receita_mes10 * -1;
            reRegistro.receita_mes11 := reRegistro.receita_mes11 * -1;
            reRegistro.receita_mes12 := reRegistro.receita_mes12 * -1;

            RETURN next reRegistro;
        END IF;
        
    END LOOP;

    DROP TABLE tmp_valor;
    DROP TABLE tmp_valor_metas;

    RETURN;
END;

$$ language 'plpgsql';
