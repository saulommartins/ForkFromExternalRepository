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
    * Arquivo de consulta dos dados do relatório Meta de Excução da Despesa, período Mensal
    * Data de Criação   : 04/08/2009


    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Eduardo Paculski Schitz
    
    * @package URBEM
    * @subpackage 

    $Id:$
 */

CREATE OR REPLACE FUNCTION orcamento.metaExecucaoDespesa(VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stCodEntidades      ALIAS FOR $2;
    stTipoRelatorio     ALIAS FOR $3;
    inNumOrgao          ALIAS FOR $4;
    inNumUnidade        ALIAS FOR $5;
    inCodRecurso        ALIAS FOR $6;
    inCodDotacaoIni     ALIAS FOR $7;
    inCodDotacaoFim     ALIAS FOR $8;
    stSql               VARCHAR   := '';
    stSqlAux            VARCHAR   := '';
    stFiltro            VARCHAR   := '';
    inIdentificador     INTEGER;
    reRegistro          RECORD;
BEGIN

    --verifica se a sequence meta_execucao_despesa existe
    IF((SELECT 1 FROM pg_catalog.pg_statio_user_sequences WHERE relname='meta_execucao_despesa') IS NOT NULL) THEN
        SELECT NEXTVAL('orcamento.meta_execucao_despesa')
          INTO inIdentificador;
    ELSE
        CREATE SEQUENCE orcamento.meta_execucao_despesa START 1;
        SELECT NEXTVAL('orcamento.meta_execucao_despesa')
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

    IF (inNumOrgao != '' AND inNumOrgao != '00') THEN
        stFiltro := stFiltro || ' AND despesa.num_orgao = ' || inNumOrgao;
    END IF;

    IF (inNumUnidade != '' AND inNumUnidade != '00') THEN
        stFiltro := stFiltro || ' AND despesa.num_unidade = ' || inNumUnidade;
    END IF;

    IF (inCodRecurso != '') THEN
        stFiltro := stFiltro || ' AND despesa.cod_recurso = ' || inCodRecurso;
    END IF;

    IF (inCodDotacaoIni != '') THEN
        stFiltro := stFiltro || ' AND despesa.cod_despesa >= ' || inCodDotacaoIni;
    END IF;

    IF (inCodDotacaoFim != '') THEN
        stFiltro := stFiltro || ' AND despesa.cod_despesa <= ' || inCodDotacaoFim;
    END IF;

    stSql := ' CREATE TEMPORARY TABLE tmp_meta_despesa_'||inIdentificador||' AS (';
    IF (stTipoRelatorio = 'S') THEN 
        stSql := stSql || '
            SELECT conta_despesa.cod_estrutural
                 , conta_despesa.cod_conta                                                                         
                 , conta_despesa.descricao                                                                    
                 , previsoes.periodo                                                                               
                 , SUM(COALESCE(previsoes.vl_previsto, 0.00)) as vl_previsto                                                       
              FROM orcamento.conta_despesa                                                                         
        INNER JOIN ( SELECT conta_despesa.cod_estrutural        
                          , previsao_despesa.periodo 
                          , previsao_despesa.vl_previsto
                          , previsao_despesa.exercicio
                       FROM orcamento.conta_despesa    
                 INNER JOIN orcamento.despesa          
                         ON conta_despesa.cod_conta = despesa.cod_conta    
                        AND conta_despesa.exercicio = despesa.exercicio 
                       JOIN orcamento.recurso('''||stExercicio||''') as recurso
                         ON recurso.cod_recurso = despesa.cod_recurso 
                        AND recurso.exercicio = despesa.exercicio 
                 INNER JOIN orcamento.previsao_despesa                        
                         ON previsao_despesa.exercicio  = despesa.exercicio    
                        AND previsao_despesa.cod_despesa = despesa.cod_despesa 
                      WHERE despesa.cod_entidade IN ('||stCodEntidades||') 
                        AND despesa.exercicio = '''||stExercicio||'''
                        '||stFiltro||'
                 ) AS previsoes
                ON previsoes.cod_estrutural LIKE (publico.fn_mascarareduzida(conta_despesa.cod_estrutural) || ''%'') 
             WHERE conta_despesa.exercicio = '''||stExercicio||'''
          GROUP BY conta_despesa.descricao
                 , conta_despesa.cod_conta
                 , conta_despesa.cod_estrutural  
                 , previsoes.periodo             
          ORDER BY conta_despesa.cod_estrutural
                 , previsoes.periodo
        ';
    ELSE
        stSql := stSql || '
            SELECT conta_despesa.cod_estrutural                                
                 , conta_despesa.cod_conta                                     
                 , conta_despesa.descricao                                     
                 , previsao_despesa.periodo                                    
                 , SUM(COALESCE(previsao_despesa.vl_previsto, 0.00)) AS vl_previsto
              FROM orcamento.conta_despesa                                       
        INNER JOIN orcamento.despesa                                       
                ON conta_despesa.cod_conta = despesa.cod_conta               
               AND conta_despesa.exercicio = despesa.exercicio
              JOIN orcamento.recurso('''||stExercicio||''') as recurso                              
                ON recurso.cod_recurso = despesa.cod_recurso 
               AND recurso.exercicio = despesa.exercicio
        INNER JOIN orcamento.previsao_despesa                              
                ON previsao_despesa.exercicio  = despesa.exercicio           
               AND previsao_despesa.cod_despesa = despesa.cod_despesa
             WHERE despesa.cod_entidade IN ('||stCodEntidades||') 
               AND despesa.exercicio = '''||stExercicio||'''
               '||stFiltro||'
          GROUP BY conta_despesa.cod_estrutural
                 , conta_despesa.cod_conta
                 , conta_despesa.descricao
                 , previsao_despesa.periodo 
          ORDER BY conta_despesa.cod_estrutural
                 , previsao_despesa.periodo
        ';
    END IF;
    stSql := stSql || ')';

    EXECUTE stSql;

    stSql := '
        SELECT cod_estrutural
             , publico.fn_nivel(cod_estrutural) AS nivel
             , descricao
             , ARRAY( SELECT periodo 
                        FROM tmp_meta_despesa_'||inIdentificador||' AS tmp1 
                       WHERE tmp1.cod_estrutural = tmp_meta_despesa_'||inIdentificador||'.cod_estrutural ) 
               AS periodo
             , ARRAY( SELECT vl_previsto 
                        FROM tmp_meta_despesa_'||inIdentificador||' AS tmp2 
                       WHERE tmp2.cod_estrutural = tmp_meta_despesa_'||inIdentificador||'.cod_estrutural ) 
               AS vl_previsto
          FROM tmp_meta_despesa_'||inIdentificador||'
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
                                                           , '||COALESCE(reRegistro.vl_previsto[1], 0)||'
                                                           , '||COALESCE(reRegistro.vl_previsto[2], 0)||'
                                                           , '||COALESCE(reRegistro.vl_previsto[3], 0)||'
                                                           , '||COALESCE(reRegistro.vl_previsto[4], 0)||'
                                                           , '||COALESCE(reRegistro.vl_previsto[5], 0)||'
                                                           , '||COALESCE(reRegistro.vl_previsto[6], 0)||'
                                                           , '||COALESCE(reRegistro.vl_previsto[7], 0)||'
                                                           , '||COALESCE(reRegistro.vl_previsto[8], 0)||'
                                                           , '||COALESCE(reRegistro.vl_previsto[9], 0)||'
                                                           , '||COALESCE(reRegistro.vl_previsto[10], 0)||'
                                                           , '||COALESCE(reRegistro.vl_previsto[11], 0)||'
                                                           , '||COALESCE(reRegistro.vl_previsto[12], 0)||' )';

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
    EXECUTE 'DROP TABLE tmp_meta_despesa_'||inIdentificador;

    RETURN;
END;
$$ LANGUAGE plpgsql
