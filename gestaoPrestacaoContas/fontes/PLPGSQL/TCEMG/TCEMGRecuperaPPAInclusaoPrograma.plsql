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

-- drop FUNCTION tcemg.recupera_ppa_inclusao_programa(VARCHAR);
CREATE OR REPLACE FUNCTION tcemg.recupera_ppa_inclusao_programa(VARCHAR, VARCHAR) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio ALIAS FOR $1;
    stDataFinal ALIAS FOR $2;
    stSql VARCHAR := '';
    rsProgramas RECORD;
    var INTEGER := 0;
BEGIN
    stSql := '
    SELECT *
	    FROM (
              SELECT pp.num_programa AS cod_programa
                   , pp.cod_programa AS ppa_cod_programa
                   , ppd.identificacao AS nom_programa
                   , ppd.objetivo
                   , REPLACE(SUM(COALESCE(total_recursos.ano1, 0.00))::VARCHAR,''.'','','')::VARCHAR AS total_recurso_1_ano
                   , REPLACE(SUM(COALESCE(total_recursos.ano2, 0.00))::VARCHAR,''.'','','')::VARCHAR AS total_recurso_2_ano
                   , REPLACE(SUM(COALESCE(total_recursos.ano3, 0.00))::VARCHAR,''.'','','')::VARCHAR AS total_recurso_3_ano
                   , REPLACE(SUM(COALESCE(total_recursos.ano4, 0.00))::VARCHAR,''.'','','')::VARCHAR AS total_recurso_4_ano
                   , normas_alteracoes.numero_lei
                   , normas_alteracoes.data_lei
                   , normas_alteracoes.data_publicacao_lei
                FROM ppa.programa AS pp
                JOIN ppa.programa_dados AS ppd
                  ON ppd.cod_programa = pp.cod_programa
                JOIN ppa.acao AS pa
                  ON pa.cod_programa = pp.cod_programa
                LEFT JOIN (
                     SELECT ano1.cod_acao
                          , ano1.timestamp_acao_dados
                          , ano1.exercicio_recurso
                          , COALESCE(ano1.valor, 0.00) AS ano1
                          , COALESCE(ano2.valor, 0.00) AS ano2
                          , COALESCE(ano3.valor, 0.00) AS ano3
                          , COALESCE(ano4.valor, 0.00) AS ano4
                       FROM ppa.acao_recurso AS ano1
                 INNER JOIN orcamento.recurso('''||stExercicio||''') AS recurso
                         ON ano1.cod_recurso   = recurso.cod_recurso
                  LEFT JOIN ppa.acao_recurso AS ano2
                         ON ano2.ano = ''2''
                        AND ano1.cod_acao             = ano2.cod_acao
                        AND ano1.timestamp_acao_dados = ano2.timestamp_acao_dados
                        AND ano1.cod_recurso          = ano2.cod_recurso
                  LEFT JOIN ppa.acao_recurso AS ano3
                         ON ano3.ano = ''3''
                        AND ano1.cod_acao             = ano3.cod_acao
                        AND ano1.timestamp_acao_dados = ano3.timestamp_acao_dados
                        AND ano1.cod_recurso          = ano3.cod_recurso
                  LEFT JOIN ppa.acao_recurso AS ano4
                         ON ano4.ano = ''4''
                        AND ano1.cod_acao             = ano4.cod_acao
                        AND ano1.timestamp_acao_dados = ano4.timestamp_acao_dados
                        AND ano1.cod_recurso          = ano4.cod_recurso
                      WHERE ano1.ano = ''1''
                   ) AS total_recursos
                  ON total_recursos.cod_acao = pa.cod_acao
                   , (SELECT nn.num_norma::INTEGER AS numero_lei
                           , nn.exercicio
                           , TO_CHAR(nn.dt_assinatura, ''DDMMYYYY'')::VARCHAR AS data_lei
                           , TO_CHAR(nn.dt_publicacao, ''DDMMYYYY'')::VARCHAR AS data_publicacao_lei
                        FROM tcemg.configuracao_leis_ppa
                  INNER JOIN normas.norma as nn
                          ON nn.cod_norma = configuracao_leis_ppa.cod_norma 
                       WHERE configuracao_leis_ppa.exercicio = '''||stExercicio||'''
                         AND configuracao_leis_ppa.tipo_configuracao = ''alteracao''
                         AND nn.dt_publicacao < TO_DATE('''||stDataFinal||''',''dd/mm/yyyy'')
                  ORDER BY nn.dt_publicacao DESC
                  LIMIT 1
                     ) AS normas_alteracoes
               WHERE ppd.timestamp_programa_dados = (SELECT MAX(programa_dados.timestamp_programa_dados) FROM ppa.programa_dados WHERE ppa.programa_dados.cod_programa = ppd.cod_programa)
                 AND pp.cod_programa NOT IN (
                              SELECT t_rap.cod_programa FROM tcemg.registros_arquivo_programa AS t_rap
                               WHERE t_rap.exercicio = '''||stExercicio||'''
                     )
                 AND pp.cod_programa NOT IN (
                              SELECT t_raip.cod_programa FROM tcemg.registros_arquivo_inclusao_programa AS t_raip
                               WHERE t_raip.exercicio = '''||stExercicio||''' 
                                 AND TO_CHAR(t_raip.timestamp, ''mm'') < TO_CHAR(TO_DATE('''||stDataFinal||''', ''dd/mm/yyyy''), ''mm'')
                     )
              
               GROUP BY pp.num_programa, ppa_cod_programa, ppd.objetivo, normas_alteracoes.numero_lei, normas_alteracoes.data_lei, normas_alteracoes.data_publicacao_lei, ppd.identificacao
               ORDER BY pp.num_programa ASC
        ) AS tmp
        ORDER BY tmp.cod_programa;
    ';

    FOR rsProgramas IN EXECUTE stSql
    LOOP
        SELECT count(*) INTO var FROM tcemg.registros_arquivo_inclusao_programa WHERE exercicio = stExercicio AND cod_programa = rsProgramas.ppa_cod_programa;
        IF var = 0 THEN
            INSERT INTO tcemg.registros_arquivo_inclusao_programa (exercicio, cod_programa) VALUES (stExercicio, rsProgramas.ppa_cod_programa);
        END IF;
       
        IF rsProgramas.cod_programa = 999 THEN
           rsProgramas.cod_programa := 9999;
        ELSE
           rsProgramas.cod_programa := rsProgramas.cod_programa;
        END IF;
        RETURN next rsProgramas;
    END LOOP;
    
END;

$$ LANGUAGE 'plpgsql';