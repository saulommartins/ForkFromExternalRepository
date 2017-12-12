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
* $Revision: 27928 $
* $Name$
* $Author: diegovic $
* $Date: 2008-02-08 22:00:15 -0200 (Sex, 08 Fev 2008) $
*
* Casos de uso: uc-02.00.00
* Casos de uso: uc-02.08.07
*/

/*
$Log$
Revision 1.1  2007/09/26 14:43:57  gris
-- O módulo TCE-SC  funcionalidades deverá ir para a gestãp estação de contas.

Revision 1.7  2007/07/30 18:28:33  tonismar
alteração de uc-00.00.00 para uc-02.00.00

Revision 1.6  2006/07/05 20:37:45  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION tcern.fn_exportacao_receita(varchar,varchar,integer) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio             ALIAS FOR $1;
    stCodEntidades          ALIAS FOR $2;
    inBimestre              ALIAS FOR $3;

    dtInicioAno         VARCHAR   := '';
    dtFimAno            VARCHAR   := '';
    stSql               VARCHAR   := '';
    stMascClassReceita  VARCHAR   := '';
    stMascRecurso       VARCHAR   := '';
    arDataInicial       VARCHAR[] := array[0];
    arDataFinal         VARCHAR[] := array[0];
    inCount             INTEGER;
    stExecute           VARCHAR;
    nuRetorno           NUMERIC;
    reRegistro          RECORD;

BEGIN
    arDataInicial[1]  := '01/01' || stExercicio || '';
    arDataInicial[2]  := '01/02' || stExercicio || '';
    arDataInicial[3]  := '01/03' || stExercicio || '';
    arDataInicial[4]  := '01/04' || stExercicio || '';
    arDataInicial[5]  := '01/05' || stExercicio || '';
    arDataInicial[6]  := '01/06' || stExercicio || '';
    arDataInicial[7]  := '01/07' || stExercicio || '';
    arDataInicial[8]  := '01/08' || stExercicio || '';
    arDataInicial[9]  := '01/09' || stExercicio || '';
    arDataInicial[10] := '01/10' || stExercicio || '';
    arDataInicial[11] := '01/11' || stExercicio || '';
    arDataInicial[12] := '01/12' || stExercicio || '';

    arDataFinal[1]  := to_char( ( to_date(arDataInicial[3],'dd/mm/yyyy')-1 ), 'dd/mm/yyyy' );
    arDataFinal[2]  := '30/04/' || stExercicio || '';
    arDataFinal[3]  := '30/06/' || stExercicio || '';
    arDataFinal[4]  := '31/08/' || stExercicio || '';
    arDataFinal[5]  := '31/10/' || stExercicio || '';
    arDataFinal[6]  := '31/12/' || stExercicio || '';


        stSql := 'CREATE TEMPORARY TABLE tmp_valor AS (
            SELECT
                ocr.cod_estrutural as cod_estrutural, rtc.cod_tc, lote.dt_lote as data, vl.vl_lancamento as valor, vl.oid as primeira, ore.vl_original
            FROM
                contabilidade.valor_lancamento      as vl   ,
                orcamento.conta_receita             as ocr  ,
                orcamento.receita                   as ore  ,
                contabilidade.lancamento_receita    as lr   ,
                contabilidade.lancamento            as lan  ,
                contabilidade.lote                  as lote ,
                tcern.receita_tc                    as rtc
            WHERE
                    ore.cod_entidade    IN ('|| stCodEntidades ||')
                AND ore.exercicio       = '|| quote_literal(stExercicio) ||'
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

                AND ore.exercicio       = rtc.exercicio
                AND ore.cod_receita     = rtc.cod_receita

            UNION

            SELECT
                ocr.cod_estrutural as cod_estrutural, rtc.cod_tc, lote.dt_lote as data, vl.vl_lancamento as valor,vl.oid as segunda, ore.vl_original
            FROM
                contabilidade.valor_lancamento      as vl   ,
                orcamento.conta_receita             as ocr  ,
                orcamento.receita                   as ore  ,
                contabilidade.lancamento_receita    as lr   ,
                contabilidade.lancamento            as lan  ,
                contabilidade.lote                  as lote ,
                tcern.receita_tc                    as rtc

            WHERE
                    ore.cod_entidade    IN('|| stCodEntidades ||')
                AND ore.exercicio       = '|| quote_literal(stExercicio) ||'
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
                AND lote.tipo           = lan.tipo 

                AND ore.exercicio       = rtc.exercicio
                AND ore.cod_receita     = rtc.cod_receita

            UNION
            
                SELECT
                    ocr.cod_estrutural as cod_estrutural, rtc.cod_tc, '|| quote_literal(arDataInicial[1]) ||' , 0, 0, r.vl_original
                FROM
                    tcern.receita_tc                    as rtc  ,
                    orcamento.conta_receita ocr
                        LEFT OUTER JOIN orcamento.receita as r ON
                            ocr.exercicio = r.exercicio AND
                            ocr.cod_conta = r.cod_conta AND
                            r.cod_entidade    IN ('|| stCodEntidades ||') AND
                            r.exercicio       = '|| quote_literal(stExercicio) ||'
                        LEFT JOIN orcamento.recurso('|| quote_literal(stExercicio) ||') as rec ON
                            rec.cod_recurso = r.cod_recurso AND
                            rec.exercicio   = r.exercicio
                WHERE
                    ocr.cod_conta = ocr.cod_conta
                AND ocr.exercicio =  '|| quote_literal(stExercicio) ||'
                AND r.exercicio   = rtc.exercicio
                AND r.cod_receita = rtc.cod_receita
            )';

        
        
        EXECUTE stSql;
        
        create TEMP table tmp_valor_agrupado as SELECT cod_tc, sum(vl_original) as svo from ( SELECT distinct cod_tc,vl_original from tmp_valor) as super_tmp group by cod_tc;

  
        stSql := '
                    SELECT
                        cod_estrutural,
                        cod_tc,                        
                        sum(COALESCE(
                        (
                            SELECT SUM(valor)
                            FROM tmp_valor as tmp_valor_exercicio
                            WHERE tmp_valor_exercicio.cod_estrutural = tmp_valor.cod_estrutural
                            AND tmp_valor_exercicio.cod_tc = tmp_valor.cod_tc
                            AND tmp_valor_exercicio.data = tmp_valor.data
                            AND tmp_valor_exercicio.primeira = tmp_valor.primeira
                            AND tmp_valor_exercicio.data BETWEEN TO_DATE('|| quote_literal(arDataInicial[(inBimestre)]) ||' ,''dd/mm/yyyy'') AND TO_DATE('|| quote_literal(arDataFinal[(inBimestre)])  ||',''dd/mm/yyyy''))
                            ,0
                        ))
                        as valor,
                        sum(valor) as valor_exercicio,
                        --vl_original
                        (select svo from tmp_valor_agrupado as tva where tva.cod_tc = tmp_valor.cod_tc ) as vl_original                        
                    FROM
                        tmp_valor
                   WHERE data BETWEEN TO_DATE('|| quote_literal(arDataInicial[1]) ||' ,''dd/mm/yyyy'') AND TO_DATE('|| quote_literal(arDataFinal[(inBimestre)]) ||',''dd/mm/yyyy'')
                   group by cod_estrutural,cod_tc';
                   

    

       FOR reRegistro IN EXECUTE stSql
        LOOP
          if reRegistro.valor < 0 then  
            reRegistro.valor := reRegistro.valor * -1;
          end if;
          if reRegistro.valor_exercicio < 0 then  
            reRegistro.valor_exercicio := reRegistro.valor_exercicio * -1;
          end if;
          
          if reRegistro.vl_original < 0 then
            reRegistro.vl_original := reRegistro.vl_original * -1;
          end if;
          
          RETURN next reRegistro;
        END LOOP;


    DROP TABLE tmp_valor;
    DROP TABLE tmp_valor_agrupado;

    RETURN;
END;
$$ language 'plpgsql';
