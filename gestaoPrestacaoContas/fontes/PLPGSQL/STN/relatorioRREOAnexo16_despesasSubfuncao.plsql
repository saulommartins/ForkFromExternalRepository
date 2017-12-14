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
* Script de função PLPGSQL - Relatório STN - RREO - Anexo 16
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Revision: 29316 $
* $Name$
* $Author: lbbarreiro $
* $Date: 2008-04-17 18:13:29 -0300 (Qui, 17 Abr 2008) $
*
* Casos de uso: uc-04.05.28
*/


CREATE OR REPLACE FUNCTION stn.fn_rreo_anexo16_despesas_subfuncao( varchar, integer,varchar ) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio    	ALIAS FOR $1;
    inBimestre     	ALIAS FOR $2;
    stCodEntidades 	ALIAS FOR $3;

    dtInicial  		varchar := '';
    dtFinal    		varchar := '';
    dtIniExercicio 	VARCHAR := '';
    
    arDatas 		varchar[] ;
    reRegistro 		record ;
    stSql 		varchar := '';

BEGIN

    arDatas := publico.bimestre ( stExercicio, inBimestre );   
    dtInicial := arDatas [ 0 ];
    dtFinal   := arDatas [ 1 ];
    
    dtIniExercicio := '01/01/' || stExercicio;

    -- --------------------
	-- TABELAS TEMPORARIAS
    -- --------------------

    --
    -- Tabela com os valores a serem retornados
    --
    CREATE TEMPORARY TABLE tmp_retorno (
        grupo INTEGER,
        subgrupo INTEGER,
        descricao VARCHAR,
        dotacao_inicial NUMERIC(14,2) DEFAULT 0.00,
        dotacao_atualizada NUMERIC(14,2) DEFAULT 0.00,
        despesa_liquidada NUMERIC(14,2) DEFAULT 0.00,
        porcentagem NUMERIC(14,2) DEFAULT 0.00
    );        
	
    --
	-- Saldo Inicial e atualizado das Despesas
    --
    stSql := '
        CREATE TEMPORARY TABLE tmp_despesa AS
            SELECT despesa.cod_subfuncao 
                 , despesa.exercicio
                 , SUM(vl_original) AS vl_original
                 , (SUM(COALESCE(suplementacao.valor,0)) - SUM(COALESCE(reducao.valor,0))) AS vl_suplementacoes
              FROM orcamento.despesa
        INNER JOIN orcamento.conta_despesa
                ON conta_despesa.cod_conta = despesa.cod_conta
               AND conta_despesa.exercicio = despesa.exercicio
         LEFT JOIN (    SELECT suplementacao_suplementada.exercicio
                             , suplementacao_suplementada.cod_despesa
                             , SUM(COALESCE(suplementacao_suplementada.valor,0)) AS valor
                          FROM orcamento.suplementacao_suplementada
                    INNER JOIN orcamento.suplementacao
                            ON suplementacao.exercicio = suplementacao_suplementada.exercicio
                           AND suplementacao.cod_suplementacao = suplementacao_suplementada.cod_suplementacao
                         WHERE TO_DATE(suplementacao.dt_suplementacao::VARCHAR,''yyyy-mm-dd'') BETWEEN TO_DATE('''||dtIniExercicio||''',''dd/mm/yyyy'') AND TO_DATE('''||dtFinal||''',''dd/mm/yyyy'')
                      GROUP BY suplementacao_suplementada.cod_despesa
                             , suplementacao_suplementada.exercicio
                   ) AS suplementacao
                ON suplementacao.exercicio = despesa.exercicio
               AND suplementacao.cod_despesa = despesa.cod_despesa
         LEFT JOIN (    SELECT suplementacao_reducao.exercicio
                             , suplementacao_reducao.cod_despesa
                             , SUM(COALESCE(suplementacao_reducao.valor,0)) AS valor
                          FROM orcamento.suplementacao_reducao
                    INNER JOIN orcamento.suplementacao
                            ON suplementacao.exercicio = suplementacao_reducao.exercicio
                           AND suplementacao.cod_suplementacao = suplementacao_reducao.cod_suplementacao
                         WHERE TO_DATE(suplementacao.dt_suplementacao::VARCHAR,''yyyy-mm-dd'') BETWEEN TO_DATE('''||dtIniExercicio||''',''dd/mm/yyyy'') AND TO_DATE('''||dtFinal||''',''dd/mm/yyyy'')
                      GROUP BY suplementacao_reducao.cod_despesa
                             , suplementacao_reducao.exercicio
                   ) AS reducao
                ON reducao.exercicio = despesa.exercicio
               AND reducao.cod_despesa = despesa.cod_despesa
             WHERE (    conta_despesa.cod_estrutural LIKE ''3.1%''
                     OR conta_despesa.cod_estrutural LIKE ''3.2%''
                     OR conta_despesa.cod_estrutural LIKE ''3.3%''
                     OR conta_despesa.cod_estrutural LIKE ''4.4%''
                     OR conta_despesa.cod_estrutural LIKE ''4.5%''
                     OR conta_despesa.cod_estrutural LIKE ''4.6%''
                   )
               AND conta_despesa.exercicio = '''||stExercicio||'''
               AND despesa.cod_funcao = 10
               AND despesa.cod_entidade IN ( '||stCodEntidades||' )
         GROUP BY despesa.cod_subfuncao
                , despesa.exercicio
    ';
    
    EXECUTE stSql;

    --
    -- Valores liquidados para as despesas
    --
    stSql := '
    CREATE TEMPORARY TABLE tmp_despesa_liquidada AS
        SELECT nota_liquidacao.vl_total
             , despesa.cod_subfuncao
          FROM ( SELECT nota_liquidacao.exercicio_empenho
                      , nota_liquidacao.cod_empenho
                      , nota_liquidacao.cod_entidade
                      , SUM(nota_liquidacao_item.vl_total) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado,0)) AS vl_total
                   FROM empenho.nota_liquidacao
             INNER JOIN ( SELECT exercicio_item
                               , cod_pre_empenho
                               , num_item
                               , exercicio
                               , cod_entidade
                               , cod_nota
                               , SUM(vl_total) AS vl_total
                            FROM empenho.nota_liquidacao_item
                        GROUP BY exercicio_item
                               , cod_pre_empenho
                               , num_item
                               , exercicio
                               , cod_entidade
                               , cod_nota
                        ) AS nota_liquidacao_item
                     ON nota_liquidacao_item.exercicio = nota_liquidacao.exercicio
                    AND nota_liquidacao_item.cod_entidade = nota_liquidacao.cod_entidade
                    AND nota_liquidacao_item.cod_nota = nota_liquidacao.cod_nota
              LEFT JOIN ( SELECT exercicio_item
                               , cod_pre_empenho
                               , num_item
                               , exercicio
                               , cod_entidade
                               , cod_nota
                               , SUM(vl_anulado) AS vl_anulado
                            FROM empenho.nota_liquidacao_item_anulado
                           WHERE TO_DATE(timestamp::VARCHAR,''yyyy-mm-dd'') BETWEEN TO_DATE('''||dtIniExercicio||''',''dd/mm/yyyy'') AND TO_DATE('''||dtFinal||''',''dd/mm/yyyy'')
                        GROUP BY exercicio_item
                               , cod_pre_empenho
                               , num_item
                               , exercicio
                               , cod_entidade
                               , cod_nota
                        ) AS nota_liquidacao_item_anulado
                     ON nota_liquidacao_item_anulado.exercicio_item = nota_liquidacao_item.exercicio_item
                    AND nota_liquidacao_item_anulado.cod_pre_empenho = nota_liquidacao_item.cod_pre_empenho
                    AND nota_liquidacao_item_anulado.num_item = nota_liquidacao_item.num_item
                    AND nota_liquidacao_item_anulado.exercicio = nota_liquidacao_item.exercicio
                    AND nota_liquidacao_item_anulado.cod_entidade = nota_liquidacao_item.cod_entidade
                    AND nota_liquidacao_item_anulado.cod_nota = nota_liquidacao_item.cod_nota
                  WHERE nota_liquidacao.dt_liquidacao BETWEEN TO_DATE('''||dtIniExercicio||''',''dd/mm/yyyy'') AND TO_DATE('''||dtFinal||''',''dd/mm/yyyy'')
               GROUP BY nota_liquidacao.exercicio_empenho
                      , nota_liquidacao.cod_empenho
                      , nota_liquidacao.cod_entidade
               ) AS nota_liquidacao
    INNER JOIN empenho.empenho
            ON empenho.exercicio = nota_liquidacao.exercicio_empenho
           AND empenho.cod_empenho = nota_liquidacao.cod_empenho
           AND empenho.cod_entidade = nota_liquidacao.cod_entidade
    INNER JOIN empenho.pre_empenho_despesa
            ON pre_empenho_despesa.exercicio = empenho.exercicio
           AND pre_empenho_despesa.cod_pre_empenho = empenho.cod_pre_empenho
    INNER JOIN orcamento.conta_despesa
            ON conta_despesa.exercicio = pre_empenho_despesa.exercicio
           AND conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
    INNER JOIN orcamento.despesa
            ON despesa.exercicio = pre_empenho_despesa.exercicio
           AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
         WHERE despesa.cod_funcao = 10
           AND despesa.cod_entidade IN ('||stCodEntidades||')
           AND nota_liquidacao.cod_entidade IN ('||stCodEntidades||')
           AND empenho.dt_empenho BETWEEN TO_DATE('''||dtIniExercicio||''',''dd/mm/yyyy'') AND TO_DATE('''||dtFinal||''',''dd/mm/yyyy'')
           AND (    conta_despesa.cod_estrutural LIKE ''3.1%''
                 OR conta_despesa.cod_estrutural LIKE ''3.2%''
                 OR conta_despesa.cod_estrutural LIKE ''3.3%''
                 OR conta_despesa.cod_estrutural LIKE ''4.4%''
                 OR conta_despesa.cod_estrutural LIKE ''4.5%''
                 OR conta_despesa.cod_estrutural LIKE ''4.6%''
               )
    ';

    EXECUTE stSql;
    
    -- --------------------------------------
    -- Povoa a tabela temporaria tmp_retorno
    -- --------------------------------------
    INSERT INTO tmp_retorno VALUES (   1
                                     , 1
                                     , 'Atenção Básica'
                                     , ( SELECT SUM(COALESCE(vl_original,0)) FROM tmp_despesa WHERE cod_subfuncao = 301 )
                                     , ( SELECT SUM(COALESCE(vl_original,0)) + SUM(COALESCE(vl_suplementacoes,0)) FROM tmp_despesa WHERE cod_subfuncao = 301 )
                                     , ( SELECT SUM(COALESCE(vl_total,0)) FROM tmp_despesa_liquidada WHERE cod_subfuncao = 301 )
                                    , 0
                                   ) ;

    INSERT INTO tmp_retorno VALUES (   2
                                     , 1
                                     , 'Assistência Hospitalar e Ambulatorial'
                                     , ( SELECT SUM(COALESCE(vl_original,0)) FROM tmp_despesa WHERE cod_subfuncao = 302 )
                                     , ( SELECT SUM(COALESCE(vl_original,0)) + SUM(COALESCE(vl_suplementacoes,0)) FROM tmp_despesa WHERE cod_subfuncao = 302 )
                                     , ( SELECT SUM(COALESCE(vl_total,0)) FROM tmp_despesa_liquidada WHERE cod_subfuncao = 302 )
                                    , 0
                                   ) ;

    INSERT INTO tmp_retorno VALUES (   3
                                     , 1
                                     , 'Suporte Profiático e Terapêutico'
                                     , ( SELECT SUM(COALESCE(vl_original,0)) FROM tmp_despesa WHERE cod_subfuncao = 303 )
                                     , ( SELECT SUM(COALESCE(vl_original,0)) + SUM(COALESCE(vl_suplementacoes,0)) FROM tmp_despesa WHERE cod_subfuncao = 303 )
                                     , ( SELECT SUM(COALESCE(vl_total,0)) FROM tmp_despesa_liquidada WHERE cod_subfuncao = 303 )
                                    , 0
                                   ) ;

    INSERT INTO tmp_retorno VALUES (   4
                                     , 1
                                     , 'Vigilância Sanitária'
                                     , ( SELECT SUM(COALESCE(vl_original,0)) FROM tmp_despesa WHERE cod_subfuncao = 304 )
                                     , ( SELECT SUM(COALESCE(vl_original,0)) + SUM(COALESCE(vl_suplementacoes,0)) FROM tmp_despesa WHERE cod_subfuncao = 304 )
                                     , ( SELECT SUM(COALESCE(vl_total,0)) FROM tmp_despesa_liquidada WHERE cod_subfuncao = 304 )
                                    , 0
                                   ) ;

    INSERT INTO tmp_retorno VALUES (   5
                                     , 1
                                     , 'Vigilância Epidemiológica'
                                     , ( SELECT SUM(COALESCE(vl_original,0)) FROM tmp_despesa WHERE cod_subfuncao = 305 )
                                     , ( SELECT SUM(COALESCE(vl_original,0)) + SUM(COALESCE(vl_suplementacoes,0)) FROM tmp_despesa WHERE cod_subfuncao = 305 )
                                     , ( SELECT SUM(COALESCE(vl_total,0)) FROM tmp_despesa_liquidada WHERE cod_subfuncao = 305 )
                                    , 0
                                   ) ;

    INSERT INTO tmp_retorno VALUES (   6
                                     , 1
                                     , 'Alimentação e Nutrição'
                                     , ( SELECT SUM(COALESCE(vl_original,0)) FROM tmp_despesa WHERE cod_subfuncao = 306 )
                                     , ( SELECT SUM(COALESCE(vl_original,0)) + SUM(COALESCE(vl_suplementacoes,0)) FROM tmp_despesa WHERE cod_subfuncao = 306 )
                                     , ( SELECT SUM(COALESCE(vl_total,0)) FROM tmp_despesa_liquidada WHERE cod_subfuncao = 306 )
                                    , 0
                                   ) ;

    INSERT INTO tmp_retorno VALUES (   7
                                     , 1
                                     , 'Outras Subfunções'
                                     , ( SELECT SUM(COALESCE(vl_original,0)) FROM tmp_despesa WHERE cod_subfuncao NOT IN (301,302,303,304,305,306) )
                                     , ( SELECT SUM(COALESCE(vl_original,0)) + SUM(COALESCE(vl_suplementacoes,0)) FROM tmp_despesa WHERE cod_subfuncao NOT IN (301,302,303,304,305,306) )
                                     , ( SELECT SUM(COALESCE(vl_total,0)) FROM tmp_despesa_liquidada WHERE cod_subfuncao NOT IN (301,302,303,304,305,306) )
                                    , 0
                                   ) ;
                                
    UPDATE tmp_retorno SET porcentagem = ROUND((despesa_liquidada/( SELECT SUM(despesa_liquidada) FROM tmp_retorno ))*100,2);

    stSql := '
        SELECT grupo
             , subgrupo
             , descricao 
             , COALESCE(dotacao_inicial,0) AS dotacao_inicial
             , COALESCE(dotacao_atualizada,0) AS dotacao_atualizada
             , COALESCE(despesa_liquidada,0) AS despesa_liquidada
             , COALESCE(porcentagem,0) AS porcentagem
          FROM tmp_retorno
      ORDER BY grupo, subgrupo
    ';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;

    --
    -- Remove as tabelas temporarias utilizadas
    -- 
    DROP TABLE tmp_retorno;
    DROP TABLE tmp_despesa;
    DROP TABLE tmp_despesa_liquidada;

    RETURN;
 
END;

$$ language 'plpgsql';
