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
/**
    * Arquivo de consulta dos dados do relatório Meta de Arrecadação da Receita, período Mensal
    * Data de Criação   : 07/08/2009


    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Eduardo Paculski Schitz
    
    * @package URBEM
    * @subpackage 

    $Id:$
 */

CREATE OR REPLACE FUNCTION orcamento.metaArrecadacaoReceita(VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stCodEntidades      ALIAS FOR $2;
    stTipoRelatorio     ALIAS FOR $3;
    inCodRecurso        ALIAS FOR $4;
    inCodReceitaIni     ALIAS FOR $5;
    inCodReceitaFim     ALIAS FOR $6;
    stSql               VARCHAR   := '';
    stSqlAux            VARCHAR   := '';
    stFiltro            VARCHAR   := '';
    inIdentificador     INTEGER;
    reRegistro          RECORD;
BEGIN

    --verifica se a sequence meta_arrecadacao_receita existe
    IF((SELECT 1 FROM pg_catalog.pg_statio_user_sequences WHERE relname='meta_arrecadacao_receita') IS NOT NULL) THEN
        SELECT NEXTVAL('orcamento.meta_arrecadacao_receita')
          INTO inIdentificador;
    ELSE
        CREATE SEQUENCE orcamento.meta_arrecadacao_receita START 1;
        SELECT NEXTVAL('orcamento.meta_arrecadacao_receita')
          INTO inIdentificador;
    END IF;

    stSql := '
    CREATE TEMPORARY TABLE tmp_meta_retorno_'||inIdentificador||' (
          cod_estrutural        VARCHAR
        , descricao             VARCHAR
        , nivel                 INTEGER
        , valor1                NUMERIC(14,2) 
        , valor2                NUMERIC(14,2) 
        , valor3                NUMERIC(14,2) 
        , valor4                NUMERIC(14,2) 
        , valor5                NUMERIC(14,2) 
        , valor6                NUMERIC(14,2) 
        , valor7                NUMERIC(14,2) 
        , valor8                NUMERIC(14,2) 
        , valor9                NUMERIC(14,2) 
        , valor10               NUMERIC(14,2) 
        , valor11               NUMERIC(14,2) 
        , valor12               NUMERIC(14,2) 
    )';

    EXECUTE stSql;

    stFiltro := '';

    IF (inCodRecurso != '') THEN
        stFiltro := stFiltro || ' AND receita.cod_recurso = ' || inCodRecurso;
    END IF;

    IF (inCodReceitaIni != '') THEN
        stFiltro := stFiltro || ' AND conta_receita.cod_estrutural >= '''||inCodReceitaIni||''' ';
    END IF;

    IF (inCodReceitaFim != '') THEN
        stFiltro := stFiltro || ' AND conta_receita.cod_estrutural <= '''||inCodReceitaFim||''' ';
    END IF;

    stSql := ' CREATE TEMPORARY TABLE tmp_meta_receita_'||inIdentificador||' AS (';
    IF (stTipoRelatorio = 'S') THEN 
        stSql := stSql || '
             SELECT conta_receita.cod_estrutural
                  , conta_receita.descricao                                                                      
                  , conta_receita.cod_conta     
                  , previsoes.periodo 
                  , SUM(COALESCE(previsoes.vl_periodo, 0.00)) AS vl_periodo
               FROM orcamento.conta_receita                
         INNER JOIN ( SELECT conta_receita.cod_estrutural  
                           , previsao_receita.periodo      
                           , previsao_receita.vl_periodo   
                           , previsao_receita.exercicio    
                        FROM orcamento.conta_receita
                        JOIN orcamento.receita
                          ON receita.cod_conta = conta_receita.cod_conta
                         AND receita.exercicio = conta_receita.exercicio
                        JOIN orcamento.previsao_receita
                          ON previsao_receita.cod_receita = receita.cod_receita
                         AND previsao_receita.exercicio   = receita.exercicio
                        JOIN orcamento.recurso as recurso
                          ON recurso.cod_recurso = receita.cod_recurso 
                         AND recurso.exercicio = receita.exercicio 
                       WHERE receita.cod_entidade IN ('||stCodEntidades||')
                         AND receita.exercicio     = '''||stExercicio||'''
                         '||stFiltro||'
                  ) AS previsoes 
                 ON previsoes.cod_estrutural LIKE(publico.fn_mascarareduzida(conta_receita.cod_estrutural)||''%'')
              WHERE conta_receita.exercicio = '''||stExercicio||'''
           GROUP BY conta_receita.descricao     
                  , conta_receita.cod_conta     
                  , conta_receita.cod_estrutural
                  , previsoes.periodo           
           ORDER BY conta_receita.cod_estrutural
                  , previsoes.periodo
        ';
    ELSE
        stSql := stSql || '
            SELECT conta_receita.cod_estrutural
                 , conta_receita.descricao     
                 , receita.vl_original         
                 , previsao_receita.periodo    
                 , SUM(COALESCE(previsao_receita.vl_periodo, 0.00)) AS vl_periodo
              FROM orcamento.receita           
              JOIN orcamento.recurso as recurso
                ON recurso.cod_recurso = receita.cod_recurso    
               AND recurso.exercicio   = receita.exercicio
              JOIN orcamento.conta_receita
                ON conta_receita.cod_conta = receita.cod_conta
               AND conta_receita.exercicio = receita.exercicio
              JOIN orcamento.previsao_receita
                ON previsao_receita.cod_receita = receita.cod_receita
               AND previsao_receita.exercicio   = receita.exercicio
             WHERE receita.cod_entidade IN ('||stCodEntidades||')
               AND receita.exercicio     = '''||stExercicio||'''
               '||stFiltro||'
          GROUP BY conta_receita.cod_estrutural
                 , conta_receita.descricao
                 , receita.vl_original
                 , previsao_receita.periodo
          ORDER BY conta_receita.cod_estrutural 
                 , previsao_receita.periodo
        ';
    END IF;
    stSql := stSql || ')';

    EXECUTE stSql;

    stSql := '
        SELECT cod_estrutural
             , publico.fn_nivel(cod_estrutural) AS nivel
             , descricao
             , ARRAY( SELECT periodo 
                        FROM tmp_meta_receita_'||inIdentificador||' AS tmp1 
                       WHERE tmp1.cod_estrutural = tmp_meta_receita_'||inIdentificador||'.cod_estrutural ) 
               AS periodo
             , ARRAY( SELECT vl_periodo
                        FROM tmp_meta_receita_'||inIdentificador||' AS tmp2 
                       WHERE tmp2.cod_estrutural = tmp_meta_receita_'||inIdentificador||'.cod_estrutural ) 
               AS vl_periodo
          FROM tmp_meta_receita_'||inIdentificador||'
      GROUP BY descricao
             , cod_estrutural
    ';

    FOR reRegistro IN EXECUTE stSql LOOP
       stSqlAux := ' 
        INSERT INTO tmp_meta_retorno_'||inIdentificador||' ( cod_estrutural
                                                           , descricao
                                                           , nivel
                                                           , valor1
                                                           , valor2
                                                           , valor3
                                                           , valor4
                                                           , valor5
                                                           , valor6
                                                           , valor7
                                                           , valor8
                                                           , valor9
                                                           , valor10
                                                           , valor11
                                                           , valor12 )
                                                    VALUES ( '''||reRegistro.cod_estrutural||'''
                                                           , '''||reRegistro.descricao||'''
                                                           , '||reRegistro.nivel||'
                                                           , '||COALESCE(reRegistro.vl_periodo[1], 0)||'
                                                           , '||COALESCE(reRegistro.vl_periodo[2], 0)||'
                                                           , '||COALESCE(reRegistro.vl_periodo[3], 0)||'
                                                           , '||COALESCE(reRegistro.vl_periodo[4], 0)||'
                                                           , '||COALESCE(reRegistro.vl_periodo[5], 0)||'
                                                           , '||COALESCE(reRegistro.vl_periodo[6], 0)||'
                                                           , '||COALESCE(reRegistro.vl_periodo[7], 0)||'
                                                           , '||COALESCE(reRegistro.vl_periodo[8], 0)||'
                                                           , '||COALESCE(reRegistro.vl_periodo[9], 0)||'
                                                           , '||COALESCE(reRegistro.vl_periodo[10], 0)||'
                                                           , '||COALESCE(reRegistro.vl_periodo[11], 0)||'
                                                           , '||COALESCE(reRegistro.vl_periodo[12], 0)||' )';

        EXECUTE stSqlAux;
    END LOOP;

    stSql := '
        SELECT * 
          FROM tmp_meta_retorno_'||inIdentificador||'
      ORDER BY cod_estrutural
    ';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;

    EXECUTE 'DROP TABLE tmp_meta_retorno_'||inIdentificador;
    EXECUTE 'DROP TABLE tmp_meta_receita_'||inIdentificador;

    RETURN;
END;
$$ LANGUAGE plpgsql
