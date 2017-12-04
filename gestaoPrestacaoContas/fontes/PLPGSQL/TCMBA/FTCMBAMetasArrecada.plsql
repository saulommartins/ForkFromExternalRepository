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
* $Id: $
* $Revision:$
* $Author:$
* $Date: $
*
*/
/*CREATE TYPE metas_arrecada AS
    (   cod_receita          INTEGER
        , exercicio            VARCHAR(4)
        , mes_1                INTEGER
        , valor_arrecada_1     NUMERIC(14,2)
        , mes_2                INTEGER
        , valor_arrecada_2     NUMERIC(14,2)
        , mes_3                INTEGER
        , valor_arrecada_3     NUMERIC(14,2)
        , mes_4                INTEGER
        , valor_arrecada_4     NUMERIC(14,2)
        , mes_5                INTEGER
        , valor_arrecada_5     NUMERIC(14,2)
        , mes_6                INTEGER
        , valor_arrecada_6     NUMERIC(14,2)
        , mes_7                INTEGER
        , valor_arrecada_7     NUMERIC(14,2)
        , mes_8                INTEGER
        , valor_arrecada_8     NUMERIC(14,2)
        , mes_9                INTEGER
        , valor_arrecada_9     NUMERIC(14,2)
        , mes_10               INTEGER
        , valor_arrecada_10    NUMERIC(14,2)
        , mes_11               INTEGER
        , valor_arrecada_11    NUMERIC(14,2)
        , mes_12               INTEGER
        , valor_arrecada_12    NUMERIC(14,2)
    );
*/
    
CREATE OR REPLACE FUNCTION tcmba.fn_metas_arrecada(VARCHAR, VARCHAR) RETURNS SETOF metas_arrecada AS $$ 
DECLARE
    stExercicio         ALIAS FOR $1;
    stEntidades         ALIAS FOR $2;
    stSql               VARCHAR   := '';
    stSqlComplemento    VARCHAR   := '';
    reRegistro          RECORD;
    arRetorno           NUMERIC[];
    inReceitas          INTEGER   := 0;
BEGIN
  CREATE TEMPORARY TABLE tmp_arquivo (
          cod_receita          INTEGER
        , estrutural           VARCHAR
        , exercicio            VARCHAR(4)
        , mes_1                INTEGER
        , valor_arrecada_1     NUMERIC(14,2)
        , mes_2                INTEGER
        , valor_arrecada_2     NUMERIC(14,2)
        , mes_3                INTEGER
        , valor_arrecada_3     NUMERIC(14,2)
        , mes_4                INTEGER
        , valor_arrecada_4     NUMERIC(14,2)
        , mes_5                INTEGER
        , valor_arrecada_5     NUMERIC(14,2)
        , mes_6                INTEGER
        , valor_arrecada_6     NUMERIC(14,2)
        , mes_7                INTEGER
        , valor_arrecada_7     NUMERIC(14,2)
        , mes_8                INTEGER
        , valor_arrecada_8     NUMERIC(14,2)
        , mes_9                INTEGER
        , valor_arrecada_9     NUMERIC(14,2)
        , mes_10               INTEGER
        , valor_arrecada_10    NUMERIC(14,2)
        , mes_11               INTEGER
        , valor_arrecada_11    NUMERIC(14,2)
        , mes_12               INTEGER
        , valor_arrecada_12    NUMERIC(14,2)
    );


    stSql := '
    CREATE TEMPORARY TABLE tmp_metas_arrecada_receita AS
             SELECT receita.exercicio 
                  , receita.cod_receita 
                  , COALESCE(periodo,''0'') as periodo
                  , COALESCE(vl_periodo, ''0.00'') as vl_periodo
                  , REPLACE(conta_receita.cod_estrutural, ''.'', '''') AS estrutural
               FROM orcamento.receita 
          LEFT JOIN orcamento.previsao_receita  
                 ON receita.cod_receita = previsao_receita.cod_receita
                AND receita.exercicio  = previsao_receita.exercicio
         INNER JOIN orcamento.conta_receita
                 ON conta_receita.cod_conta = receita.cod_conta
                AND conta_receita.exercicio = receita.exercicio
              WHERE receita.exercicio = '''||stExercicio||'''
                AND receita.cod_entidade in ('||stEntidades||')
           ORDER BY receita.cod_receita , periodo ';
    EXECUTE stSql;

    stSql := '
             SELECT DISTINCT
                    cod_receita
                  , estrutural
               FROM tmp_metas_arrecada_receita ';
    FOR reRegistro IN EXECUTE stSql
	LOOP
        INSERT INTO tmp_arquivo VALUES (  reRegistro.cod_receita
                                        , reRegistro.estrutural
                                        , stExercicio
                                        , 1
                                        , COALESCE((SELECT vl_periodo
                                                  FROM tmp_metas_arrecada_receita
                                                 WHERE cod_receita = reRegistro.cod_receita
                                                   AND periodo = 1),0.00)
                                        , 2
                                        , COALESCE((SELECT vl_periodo
                                                  FROM tmp_metas_arrecada_receita
                                                 WHERE cod_receita = reRegistro.cod_receita
                                                   AND periodo = 2),0.00)
                                        , 3
                                        , COALESCE((SELECT vl_periodo
                                                  FROM tmp_metas_arrecada_receita
                                                 WHERE cod_receita = reRegistro.cod_receita
                                                   AND periodo = 3),0.00)
                                        , 4
                                        , COALESCE((SELECT vl_periodo
                                                  FROM tmp_metas_arrecada_receita
                                                 WHERE cod_receita = reRegistro.cod_receita
                                                   AND periodo = 4),0.00)
                                        , 5
                                        , COALESCE((SELECT vl_periodo
                                                  FROM tmp_metas_arrecada_receita
                                                 WHERE cod_receita = reRegistro.cod_receita
                                                   AND periodo = 5),0.00)
                                        , 6
                                        , COALESCE((SELECT vl_periodo
                                                  FROM tmp_metas_arrecada_receita
                                                 WHERE cod_receita = reRegistro.cod_receita
                                                   AND periodo = 6),0.00)
                                        , 7
                                        , COALESCE((SELECT vl_periodo
                                                  FROM tmp_metas_arrecada_receita
                                                 WHERE cod_receita = reRegistro.cod_receita
                                                   AND periodo = 7),0.00)
                                        , 8
                                        , COALESCE((SELECT vl_periodo
                                                  FROM tmp_metas_arrecada_receita
                                                 WHERE cod_receita = reRegistro.cod_receita
                                                   AND periodo = 8),0.00)
                                        , 9
                                        , COALESCE((SELECT vl_periodo
                                                  FROM tmp_metas_arrecada_receita
                                                 WHERE cod_receita = reRegistro.cod_receita
                                                   AND periodo = 9),0.00)
                                        , 10
                                        , COALESCE((SELECT vl_periodo
                                                  FROM tmp_metas_arrecada_receita
                                                 WHERE cod_receita = reRegistro.cod_receita
                                                   AND periodo = 10),0.00)
                                        , 11
                                        , COALESCE((SELECT vl_periodo
                                                  FROM tmp_metas_arrecada_receita
                                                 WHERE cod_receita = reRegistro.cod_receita
                                                   AND periodo = 11),0.00)
                                        , 12
                                        , COALESCE((SELECT vl_periodo
                                                  FROM tmp_metas_arrecada_receita
                                                 WHERE cod_receita = reRegistro.cod_receita
                                                   AND periodo = 12),0.00)
                                    );
	END LOOP;
    
    stSql := 'SELECT * FROM tmp_arquivo ORDER BY cod_receita';                                                 

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;
    
    DROP TABLE tmp_metas_arrecada_receita;
    DROP TABLE tmp_arquivo;

    RETURN;
END;
$$ LANGUAGE 'plpgsql';
