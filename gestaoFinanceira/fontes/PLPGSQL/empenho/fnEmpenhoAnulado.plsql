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
CREATE OR REPLACE FUNCTION empenho.fn_empenho_anulado ( character varying, integer, integer, character varying, character varying ) RETURNS NUMERIC AS $$
 DECLARE
     stExercicio                 ALIAS FOR $1;
     stCodEmpenho                ALIAS FOR $2;
     stCodEntidades              ALIAS FOR $3;
     stDtInicial                 ALIAS FOR $4;
     stDtFinal                   ALIAS FOR $5;
     stSql                       VARCHAR   := '';
     nuSoma                      NUMERIC   := 0;
     crCursor                    REFCURSOR;
 
 BEGIN
         stSql := '
             SELECT coalesce(sum(eai.vl_anulado),0.00) as valor
             FROM
                 empenho.empenho     as e
               , empenho.empenho_anulado ea
               , empenho.empenho_anulado_item eai

             WHERE
                     e.exercicio         = ' || quote_literal(stExercicio) || '
                 AND e.cod_entidade      IN (' || stCodEntidades || ')
                 AND e.cod_empenho = ' || stCodEmpenho || '

                 AND to_date(to_char(ea.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'')
         ';
         IF stDtInicial = '' OR stDtInicial IS NULL THEN
               stSql := stSql || ' <= to_date(''' || stDtFinal || ''',''dd/mm/yyyy'') ';
         ELSE
               stSql := stSql || ' BETWEEN to_date(''' || stDtInicial || ''',''dd/mm/yyyy'') AND
                         to_date(''' || stDtFinal || ''',''dd/mm/yyyy'') ';
         END IF;
         stSql := stSql || '
                 AND e.cod_empenho   = ea.cod_empenho
                 AND e.exercicio     = ea.exercicio
                 AND e.cod_entidade  = ea.cod_entidade

                 AND ea.cod_empenho  = eai.cod_empenho
                 AND ea.exercicio    = eai.exercicio
                 AND ea.cod_entidade = eai.cod_entidade
                 AND ea.timestamp    = eai.timestamp';

     OPEN crCursor FOR EXECUTE stSql;
     FETCH crCursor INTO nuSoma;
     CLOSE crCursor;
     RETURN nuSoma;
 
 END;
$$ LANGUAGE 'plpgsql';
