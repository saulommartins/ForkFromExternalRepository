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
* Autor: Evandro Melos
* Data: 08/10/2013
*/

CREATE OR REPLACE FUNCTION tcemg.fn_deducao_receita(varchar,varchar,varchar,varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio             ALIAS FOR $1;
    dtInicial               ALIAS FOR $2;
    dtFinal                 ALIAS FOR $3;
    stCodEntidades          ALIAS FOR $4;
    inPeriodo               INTEGER;
    stSql                   VARCHAR := '';
    reRegistro              RECORD;
BEGIN

    inPeriodo := EXTRACT( month FROM TO_DATE(dtInicial,'dd/mm/yyyy') );
    
    stSql :='
          CREATE TEMPORARY TABLE tmp_deducoes AS
          SELECT *
            FROM orcamento.fn_balancete_receita( '||quote_literal(stExercicio)||'
                                               , ''''
                                               , '|| quote_literal(dtInicial) || '
                                               , '|| quote_literal(dtFinal) || '
                                               , '|| quote_literal(stCodEntidades) ||'
                                               , ''''
                                               , ''''
                                               , ''''
                                               , ''''
                                               , ''''
                                               , ''''
                                               ,'''') 
              AS retorno( cod_estrutural      VARCHAR
                        , receita             INTEGER
                        , recurso             VARCHAR
                        , descricao           VARCHAR
                        , valor_previsto      NUMERIC
                        , arrecadado_periodo  NUMERIC
                        , arrecadado_ano      NUMERIC
                        , diferenca           NUMERIC
                        )
                  WHERE cod_estrutural like ''9.1.7.0%''
    ';
    
    EXECUTE stSql;
    
    stSql := '
          SELECT *
            FROM (
    ';
    IF inPeriodo = 1 THEN
        stSql := stSql || '
                  SELECT '||inPeriodo||'::VARCHAR as mes
                       , ''01''::VARCHAR as cod_tipo
                       , ABS(sum(valor_previsto))::VARCHAR as valor_01
                    FROM tmp_deducoes
                   UNION 
        ';
    END IF;
    stSql := stSql || '
                  SELECT '||inPeriodo||'::VARCHAR as mes
                       , ''02''::VARCHAR as cod_tipo
                       , ABS(sum(valor_previsto))::VARCHAR as valor_02
                    FROM tmp_deducoes
                   UNION 
                  SELECT '||inPeriodo||'::VARCHAR as mes
                       , ''03''::VARCHAR as cod_tipo
                       , ABS(sum(valor_previsto))::VARCHAR as valor_03
                    FROM tmp_deducoes
                   UNION 
                  SELECT '||inPeriodo||'::VARCHAR as mes
                       , ''04''::VARCHAR as cod_tipo
                       , ABS(sum(arrecadado_periodo))::VARCHAR as valor_04
                    FROM tmp_deducoes
                 ) resultado
        ORDER BY mes
               , cod_tipo
    ';
    
    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;

    DROP TABLE tmp_deducoes;

END;
$$ language 'plpgsql';