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
* Script de DDL e DML
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
* $Id: $
* $Revision: $
* $Author: $
* $Date: $
*
* Caso de uso: uc-06.04.00
*/
CREATE OR REPLACE FUNCTION tcemg.recupera_ppa_programa(VARCHAR, BOOLEAN) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio ALIAS FOR $1;
    boReemissao ALIAS FOR $2;
    stSql VARCHAR := '';
    rsProgramas RECORD;
    var INTEGER := 0;
BEGIN
    stSql := '
    SELECT *
	    FROM (
            SELECT  programa.cod_programa AS cod_programa
                    , programa.num_programa AS num_programa
                    , programa_dados.identificacao AS nome_programa
                    , programa_dados.objetivo AS objetivo
                    , REPLACE(SUM(total_recursos.ano1)::VARCHAR, ''.'', '','')::VARCHAR AS total_recursos_ano_1
                    , REPLACE(SUM(total_recursos.ano2)::VARCHAR, ''.'', '','')::VARCHAR AS total_recursos_ano_2
                    , REPLACE(SUM(total_recursos.ano3)::VARCHAR, ''.'', '','')::VARCHAR AS total_recursos_ano_3
                    , REPLACE(SUM(total_recursos.ano4)::VARCHAR, ''.'', '','')::VARCHAR AS total_recursos_ano_4
              FROM ppa.programa
              JOIN ppa.programa_dados
                ON programa_dados.cod_programa = programa.cod_programa
               AND programa_dados.timestamp_programa_dados = programa.ultimo_timestamp_programa_dados
              JOIN ppa.acao
                ON acao.cod_programa = programa.cod_programa
              LEFT JOIN (
                    SELECT p_ar.cod_acao
                         , p_ar.timestamp_acao_dados
                         --, p_ar.exercicio_recurso
                         , COALESCE((SELECT COALESCE(ano_1.valor, ''0.00'')
                              FROM ppa.acao_recurso AS ano_1
                             WHERE ano_1.ano = ''1''
                               AND p_ar.cod_acao             = ano_1.cod_acao
                               AND p_ar.timestamp_acao_dados = ano_1.timestamp_acao_dados
                               AND p_ar.cod_recurso          = ano_1.cod_recurso),0.00) AS ano1
                         
                         , COALESCE((SELECT COALESCE(ano_2.valor, ''0.00'')
                              FROM ppa.acao_recurso AS ano_2
                             WHERE ano_2.ano = ''2''
                               AND p_ar.cod_acao             = ano_2.cod_acao
                               AND p_ar.timestamp_acao_dados = ano_2.timestamp_acao_dados
                               AND p_ar.cod_recurso          = ano_2.cod_recurso),0.00) AS ano2
                         
                         , COALESCE((SELECT COALESCE(ano_3.valor, ''0.00'')
                              FROM ppa.acao_recurso AS ano_3
                             WHERE ano_3.ano = ''3''
                               AND p_ar.cod_acao             = ano_3.cod_acao
                               AND p_ar.timestamp_acao_dados = ano_3.timestamp_acao_dados
                               AND p_ar.cod_recurso          = ano_3.cod_recurso),0.00) AS ano3
                         
                         , COALESCE((SELECT COALESCE(ano_4.valor, ''0.00'')
                              FROM ppa.acao_recurso AS ano_4
                             WHERE ano_4.ano = ''4''
                               AND p_ar.cod_acao             = ano_4.cod_acao
                               AND p_ar.timestamp_acao_dados = ano_4.timestamp_acao_dados
                               AND p_ar.cod_recurso          = ano_4.cod_recurso),0.00) AS ano4
                         
                      FROM ppa.acao_recurso AS p_ar
                     INNER JOIN orcamento.recurso(''' || stExercicio || ''') AS recurso
                        ON p_ar.cod_recurso   = recurso.cod_recurso
                     group by p_ar.cod_acao
                         , p_ar.timestamp_acao_dados
                         , p_ar.cod_recurso
                     order by p_ar.cod_acao
                   ) AS total_recursos
                ON total_recursos.cod_acao             = acao.cod_acao
               AND total_recursos.timestamp_acao_dados = acao.ultimo_timestamp_acao_dados
    ';
    IF boReemissao = true THEN
        stSql := stSql || '
              JOIN tcemg.registros_arquivo_programa AS t_rap
                ON t_rap.exercicio = '''||stExercicio||'''
               AND t_rap.cod_programa = programa.cod_programa
        ';
    END IF;
                
    stSql := stSql || '
             GROUP BY programa.cod_programa
                    , programa_dados.identificacao
                    , programa_dados.objetivo
             ORDER BY programa.num_programa
        ) AS tmp
        WHERE total_recursos_ano_1 IS NOT NULL
          AND total_recursos_ano_2 IS NOT NULL
          AND total_recursos_ano_3 IS NOT NULL
          AND total_recursos_ano_4 IS NOT NULL
             
        ORDER BY tmp.cod_programa;
    ';

    FOR rsProgramas IN EXECUTE stSql
    LOOP
        SELECT count(*) INTO var FROM tcemg.registros_arquivo_programa WHERE exercicio = stExercicio AND cod_programa = rsProgramas.cod_programa;
        IF var = 0 THEN
            INSERT INTO tcemg.registros_arquivo_programa (exercicio, cod_programa) VALUES (stExercicio, rsProgramas.cod_programa);
        END IF;
       
        IF rsProgramas.num_programa = 999 THEN
           rsProgramas.num_programa := 9999;
        ELSE
           rsProgramas.num_programa := rsProgramas.num_programa;
        END IF;
        
        RETURN next rsProgramas;
    END LOOP;
    
END;

$$ LANGUAGE 'plpgsql';
