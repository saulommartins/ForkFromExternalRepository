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
*
*/



CREATE OR REPLACE FUNCTION stn.pl_recurso_descricao ( varchar, varchar , varchar, varchar, varchar, varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    data_ini            ALIAS FOR $2;
    data_fim            ALIAS FOR $3;
    stCondicao          ALIAS FOR $4;
    inCod_Entidade      ALIAS FOR $5;
    stRPPS              ALIAS FOR $6;
    reRegistro          RECORD;
    stRecurso           VARCHAR;
    stCondEntidades     VARCHAR := '';
    inCodEntidadeRPPS   INTEGER;
    stSql               VARCHAR := '';
    stSqlInsert         VARCHAR := '';
    inCod_EntidadeAux  VARCHAR := '';
       
BEGIN

inCodEntidadeRPPS := selectintointeger('SELECT valor FROM administracao.configuracao where parametro = ''cod_entidade_rpps'' AND cod_modulo = 8 AND exercicio = ''' || stExercicio || ''' ');

IF  ( stRPPS = 'false' ) THEN

    stCondEntidades := ' and valor_lancamento.cod_entidade in ( ' || inCod_Entidade || ' )  and valor_lancamento.cod_entidade not in ( ' || inCodEntidadeRPPS || ' ) ';

ELSE

    stCondEntidades := ' and valor_lancamento.cod_entidade in ( ' || inCodEntidadeRPPS || ' ) ';

END IF;


IF (StRPPS = 'false' ) THEN
    inCod_EntidadeAux := inCod_Entidade;
ELSE
    inCod_EntidadeAux := inCodEntidadeRPPS;
END IF;

stSqlInsert := '           
        CREATE TEMPORARY TABLE tmp_recurso_inicial AS
        SELECT cod_recurso
             , exercicio
             , nom_recurso
             , tipo_recurso
             , SUM(valor) AS valor
             , SUM(valor_consignacoes) AS valor_consignacoes
             --,cod_plano
        FROM (
                SELECT DISTINCT
                     recurso_direto.cod_recurso
                    ,recurso_direto.exercicio
                    ,recurso_direto.nom_recurso
                    , COALESCE( SUM(valor_lancamento.vl_lancamento) ,0.00) as valor
                    , 0.00 as valor_consignacoes
                    ,recurso_direto.tipo as tipo_recurso
                    --,plano_analitica.cod_plano
                from contabilidade.plano_conta
                    join contabilidade.plano_analitica
                     on ( plano_conta.exercicio = plano_analitica.exercicio 
                    and   plano_conta.cod_conta = plano_analitica.cod_conta ) 
                    join contabilidade.conta_credito
                     on ( plano_analitica.exercicio = conta_credito.exercicio
                    and   plano_analitica.cod_plano = conta_credito.cod_plano )
                   join contabilidade.valor_lancamento 
                     on ( conta_credito.exercicio    = valor_lancamento.exercicio 
                    and   conta_credito.cod_entidade = valor_lancamento.cod_entidade 
                    and   conta_credito.tipo         = valor_lancamento.tipo         
                    and   conta_credito.cod_lote     = valor_lancamento.cod_lote     
                    and   conta_credito.sequencia    = valor_lancamento.sequencia    
                    and   conta_credito.tipo_valor   = valor_lancamento.tipo_valor )
                   join contabilidade.lote 
                     on ( valor_lancamento.exercicio    = lote.exercicio     
                    and   valor_lancamento.cod_entidade = lote.cod_entidade  
                    and   valor_lancamento.tipo         = lote.tipo          
                    and   valor_lancamento.cod_lote     = lote.cod_lote )
                   join contabilidade.lancamento
                     on ( lancamento.cod_lote       = valor_lancamento.cod_lote
                    and   lancamento.tipo           = valor_lancamento.tipo
                    and   lancamento.exercicio      = valor_lancamento.exercicio
                    and   lancamento.sequencia      = valor_lancamento.sequencia
                    and   lancamento.cod_entidade   = valor_lancamento.cod_entidade 
                    and   lancamento.cod_historico  not between 800 and 899 )
                    join contabilidade.plano_recurso
                     on ( plano_recurso.exercicio = plano_analitica.exercicio
                    and   plano_recurso.cod_plano = plano_analitica.cod_plano )             
                    left join orcamento.recurso_direto
                     on ( recurso_direto.exercicio = plano_recurso.exercicio
                    and   recurso_direto.cod_recurso = plano_recurso.cod_recurso )
                   
                    WHERE   plano_conta.exercicio = '''|| stExercicio || '''
                        '|| stCondicao ||'
                        and lote.dt_lote between to_date( '''|| data_ini ||''' , ''dd/mm/yyyy'' ) 
                                       and   to_date( '''|| data_fim ||''' , ''dd/mm/yyyy'' )
                        '|| stCondEntidades ||'
                        AND valor_lancamento.tipo = ''I''
                        GROUP by recurso_direto.cod_recurso , recurso_direto.exercicio, nom_recurso , recurso_direto.tipo--,plano_analitica.cod_plano
                                                
                                  UNION ALL
                        
                        SELECT DISTINCT
                recurso_direto.cod_recurso
                ,recurso_direto.exercicio
                ,recurso_direto.nom_recurso
                , COALESCE(SUM(valor_lancamento.vl_lancamento),0.00) as valor
                , 0.00 as valor_consignacoes
                ,recurso_direto.tipo as tipo_recurso
                --,plano_analitica.cod_plano
            from contabilidade.plano_conta plano_conta
                    join contabilidade.plano_analitica
                      on ( plano_conta.exercicio = plano_analitica.exercicio 
                     and   plano_conta.cod_conta = plano_analitica.cod_conta ) 
                    
                     join contabilidade.conta_debito
                      on ( plano_analitica.exercicio = conta_debito.exercicio
                     and   plano_analitica.cod_plano = conta_debito.cod_plano )
                    join contabilidade.valor_lancamento 
                      on ( conta_debito.exercicio    = valor_lancamento.exercicio 
                     and   conta_debito.cod_entidade = valor_lancamento.cod_entidade 
                     and   conta_debito.tipo         = valor_lancamento.tipo         
                     and   conta_debito.cod_lote     = valor_lancamento.cod_lote     
                     and   conta_debito.sequencia    = valor_lancamento.sequencia    
                     and   conta_debito.tipo_valor   = valor_lancamento.tipo_valor )
                    join contabilidade.lote 
                      on ( valor_lancamento.exercicio    = lote.exercicio     
                     and   valor_lancamento.cod_entidade = lote.cod_entidade  
                     and   valor_lancamento.tipo         = lote.tipo          
                     and   valor_lancamento.cod_lote     = lote.cod_lote )
                   join contabilidade.lancamento
                     on ( lancamento.cod_lote       = valor_lancamento.cod_lote
                    and   lancamento.tipo           = valor_lancamento.tipo
                    and   lancamento.sequencia      = valor_lancamento.sequencia
                    and   lancamento.exercicio      = valor_lancamento.exercicio
                    and   lancamento.cod_entidade   = valor_lancamento.cod_entidade 
                    and   lancamento.cod_historico  not between 800 and 899 )
                   join contabilidade.plano_recurso
                      on ( plano_recurso.exercicio = plano_analitica.exercicio
                     and   plano_recurso.cod_plano = plano_analitica.cod_plano )             
                   left join orcamento.recurso_direto
                      on ( recurso_direto.exercicio = plano_recurso.exercicio
                     and   recurso_direto.cod_recurso = plano_recurso.cod_recurso )
                
                WHERE  plano_conta.exercicio = '''|| stExercicio ||'''
                        '|| stCondicao ||'
                        and lote.dt_lote between to_date( '''|| data_ini||''' , ''dd/mm/yyyy'' ) 
                                       and   to_date( '''|| data_fim ||''' , ''dd/mm/yyyy'' )
                        '|| stCondEntidades ||'
                        AND valor_lancamento.tipo = ''I''
                GROUP by recurso_direto.cod_recurso , recurso_direto.exercicio, nom_recurso , recurso_direto.tipo--,plano_analitica.cod_plano
                
                UNION ALL
                
                SELECT DISTINCT
                     recurso_direto.cod_recurso
                    ,recurso_direto.exercicio
                    ,recurso_direto.nom_recurso
                    , 0.00 as valor
                    , COALESCE( SUM(valor_lancamento.vl_lancamento) ,0.00) as valor_consignacoes
                    ,recurso_direto.tipo as tipo_recurso
                    --,plano_analitica.cod_plano
                from contabilidade.plano_conta
                    join contabilidade.plano_analitica
                     on ( plano_conta.exercicio = plano_analitica.exercicio 
                    and   plano_conta.cod_conta = plano_analitica.cod_conta ) 
                    join contabilidade.conta_credito
                     on ( plano_analitica.exercicio = conta_credito.exercicio
                    and   plano_analitica.cod_plano = conta_credito.cod_plano )
                   join contabilidade.valor_lancamento 
                     on ( conta_credito.exercicio    = valor_lancamento.exercicio 
                    and   conta_credito.cod_entidade = valor_lancamento.cod_entidade 
                    and   conta_credito.tipo         = valor_lancamento.tipo         
                    and   conta_credito.cod_lote     = valor_lancamento.cod_lote     
                    and   conta_credito.sequencia    = valor_lancamento.sequencia    
                    and   conta_credito.tipo_valor   = valor_lancamento.tipo_valor )
                   join contabilidade.lote 
                     on ( valor_lancamento.exercicio    = lote.exercicio     
                    and   valor_lancamento.cod_entidade = lote.cod_entidade  
                    and   valor_lancamento.tipo         = lote.tipo          
                    and   valor_lancamento.cod_lote     = lote.cod_lote )
                   join contabilidade.lancamento
                     on ( lancamento.cod_lote       = valor_lancamento.cod_lote
                    and   lancamento.tipo           = valor_lancamento.tipo
                    and   lancamento.exercicio      = valor_lancamento.exercicio
                    and   lancamento.sequencia      = valor_lancamento.sequencia
                    and   lancamento.cod_entidade   = valor_lancamento.cod_entidade 
                    and   lancamento.cod_historico  not between 800 and 899 )
                    join contabilidade.plano_recurso
                     on ( plano_recurso.exercicio = plano_analitica.exercicio
                    and   plano_recurso.cod_plano = plano_analitica.cod_plano )             
                    left join orcamento.recurso_direto
                     on ( recurso_direto.exercicio = plano_recurso.exercicio
                    and   recurso_direto.cod_recurso = plano_recurso.cod_recurso )
                   
                    WHERE   plano_conta.exercicio = '''|| stExercicio || '''
                        '|| stCondicao ||'
                        and lote.dt_lote between to_date( '''|| data_ini ||''' , ''dd/mm/yyyy'' ) 
                                       and   to_date( '''|| data_fim ||''' , ''dd/mm/yyyy'' )
                        '|| stCondEntidades ||'
                        AND valor_lancamento.tipo = ''I''
                        AND plano_conta.cod_estrutural LIKE ''2.1.8.8.1%''
                        GROUP by recurso_direto.cod_recurso , recurso_direto.exercicio, nom_recurso , recurso_direto.tipo--,plano_analitica.cod_plano
                                                
                                  UNION ALL
                        
                        SELECT DISTINCT
                recurso_direto.cod_recurso
                ,recurso_direto.exercicio
                ,recurso_direto.nom_recurso
                , 0.00 as valor
                , COALESCE( SUM(valor_lancamento.vl_lancamento) ,0.00) as valor_consignacoes
                ,recurso_direto.tipo as tipo_recurso
                --,plano_analitica.cod_plano
            from contabilidade.plano_conta plano_conta
                    join contabilidade.plano_analitica
                      on ( plano_conta.exercicio = plano_analitica.exercicio 
                     and   plano_conta.cod_conta = plano_analitica.cod_conta ) 
                    
                     join contabilidade.conta_debito
                      on ( plano_analitica.exercicio = conta_debito.exercicio
                     and   plano_analitica.cod_plano = conta_debito.cod_plano )
                    join contabilidade.valor_lancamento 
                      on ( conta_debito.exercicio    = valor_lancamento.exercicio 
                     and   conta_debito.cod_entidade = valor_lancamento.cod_entidade 
                     and   conta_debito.tipo         = valor_lancamento.tipo         
                     and   conta_debito.cod_lote     = valor_lancamento.cod_lote     
                     and   conta_debito.sequencia    = valor_lancamento.sequencia    
                     and   conta_debito.tipo_valor   = valor_lancamento.tipo_valor )
                    join contabilidade.lote 
                      on ( valor_lancamento.exercicio    = lote.exercicio     
                     and   valor_lancamento.cod_entidade = lote.cod_entidade  
                     and   valor_lancamento.tipo         = lote.tipo          
                     and   valor_lancamento.cod_lote     = lote.cod_lote )
                   join contabilidade.lancamento
                     on ( lancamento.cod_lote       = valor_lancamento.cod_lote
                    and   lancamento.tipo           = valor_lancamento.tipo
                    and   lancamento.sequencia      = valor_lancamento.sequencia
                    and   lancamento.exercicio      = valor_lancamento.exercicio
                    and   lancamento.cod_entidade   = valor_lancamento.cod_entidade 
                    and   lancamento.cod_historico  not between 800 and 899 )
                   join contabilidade.plano_recurso
                      on ( plano_recurso.exercicio = plano_analitica.exercicio
                     and   plano_recurso.cod_plano = plano_analitica.cod_plano )             
                   left join orcamento.recurso_direto
                      on ( recurso_direto.exercicio = plano_recurso.exercicio
                     and   recurso_direto.cod_recurso = plano_recurso.cod_recurso )
                
                WHERE  plano_conta.exercicio = '''|| stExercicio ||'''
                        '|| stCondicao ||'
                        and lote.dt_lote between to_date( '''|| data_ini||''' , ''dd/mm/yyyy'' ) 
                                       and   to_date( '''|| data_fim ||''' , ''dd/mm/yyyy'' )
                        '|| stCondEntidades ||'
                        AND valor_lancamento.tipo = ''I''
                        AND plano_conta.cod_estrutural LIKE ''2.1.8.8.1%''
                GROUP by recurso_direto.cod_recurso , recurso_direto.exercicio, nom_recurso , recurso_direto.tipo--,plano_analitica.cod_plano
                ) AS recursos
                GROUP by cod_recurso , exercicio, nom_recurso , tipo_recurso--,cod_plano
                ORDER BY nom_recurso ASC
                    ';
                 
EXECUTE stSqlInsert;

stSqlInsert :='';

stSqlInsert := '           
        CREATE TEMPORARY TABLE tmp_recurso_positivo AS
                SELECT DISTINCT
                     recurso_direto.cod_recurso
                    ,recurso_direto.exercicio
                    ,recurso_direto.nom_recurso
                    , ABS(COALESCE( SUM(valor_lancamento.vl_lancamento) ,0.00)) as valor_positivo
                    ,recurso_direto.tipo as tipo_recurso
                from contabilidade.plano_conta
                    join contabilidade.plano_analitica
                     on ( plano_conta.exercicio = plano_analitica.exercicio 
                    and   plano_conta.cod_conta = plano_analitica.cod_conta ) 
                    join contabilidade.conta_credito
                     on ( plano_analitica.exercicio = conta_credito.exercicio
                    and   plano_analitica.cod_plano = conta_credito.cod_plano )
                   join contabilidade.valor_lancamento 
                     on ( conta_credito.exercicio    = valor_lancamento.exercicio 
                    and   conta_credito.cod_entidade = valor_lancamento.cod_entidade 
                    and   conta_credito.tipo         = valor_lancamento.tipo         
                    and   conta_credito.cod_lote     = valor_lancamento.cod_lote     
                    and   conta_credito.sequencia    = valor_lancamento.sequencia    
                    and   conta_credito.tipo_valor   = valor_lancamento.tipo_valor )
                   join contabilidade.lote 
                     on ( valor_lancamento.exercicio    = lote.exercicio     
                    and   valor_lancamento.cod_entidade = lote.cod_entidade  
                    and   valor_lancamento.tipo         = lote.tipo          
                    and   valor_lancamento.cod_lote     = lote.cod_lote )
                   join contabilidade.lancamento
                     on ( lancamento.cod_lote       = valor_lancamento.cod_lote
                    and   lancamento.tipo           = valor_lancamento.tipo
                    and   lancamento.exercicio      = valor_lancamento.exercicio
                    and   lancamento.sequencia      = valor_lancamento.sequencia
                    and   lancamento.cod_entidade   = valor_lancamento.cod_entidade 
                    and   lancamento.cod_historico  not between 800 and 899 )
                    join contabilidade.plano_recurso
                     on ( plano_recurso.exercicio = plano_analitica.exercicio
                    and   plano_recurso.cod_plano = plano_analitica.cod_plano )             
                    left join orcamento.recurso_direto
                     on ( recurso_direto.exercicio = plano_recurso.exercicio
                    and   recurso_direto.cod_recurso = plano_recurso.cod_recurso )
                   
                    WHERE   plano_conta.exercicio = '''|| stExercicio || '''
                        '|| stCondicao ||'
                        and lote.dt_lote between to_date( '''|| data_ini ||''' , ''dd/mm/yyyy'' ) 
                                       and   to_date( '''|| data_fim ||''' , ''dd/mm/yyyy'' )
                        '|| stCondEntidades ||'
                        AND valor_lancamento.tipo <> ''I''
                       AND plano_conta.cod_estrutural NOT LIKE ''2.1.8.8.1%''
                        GROUP by recurso_direto.cod_recurso , recurso_direto.exercicio, nom_recurso , recurso_direto.tipo
                        ORDER BY nom_recurso ASC
                    ';
                    

EXECUTE stSqlInsert;

stSqlInsert :='';

stSqlInsert := '
        CREATE TEMPORARY TABLE tmp_recurso_negativo AS
            SELECT DISTINCT
                recurso_direto.cod_recurso
                ,recurso_direto.exercicio
                ,recurso_direto.nom_recurso
                , COALESCE(SUM(valor_lancamento.vl_lancamento),0.00)*(-1) as valor_negativo
                ,recurso_direto.tipo as tipo_recurso
            from contabilidade.plano_conta plano_conta
                    join contabilidade.plano_analitica
                      on ( plano_conta.exercicio = plano_analitica.exercicio 
                     and   plano_conta.cod_conta = plano_analitica.cod_conta ) 
                    
                     join contabilidade.conta_debito
                      on ( plano_analitica.exercicio = conta_debito.exercicio
                     and   plano_analitica.cod_plano = conta_debito.cod_plano )
                    join contabilidade.valor_lancamento 
                      on ( conta_debito.exercicio    = valor_lancamento.exercicio 
                     and   conta_debito.cod_entidade = valor_lancamento.cod_entidade 
                     and   conta_debito.tipo         = valor_lancamento.tipo         
                     and   conta_debito.cod_lote     = valor_lancamento.cod_lote     
                     and   conta_debito.sequencia    = valor_lancamento.sequencia    
                     and   conta_debito.tipo_valor   = valor_lancamento.tipo_valor )
                    join contabilidade.lote 
                      on ( valor_lancamento.exercicio    = lote.exercicio     
                     and   valor_lancamento.cod_entidade = lote.cod_entidade  
                     and   valor_lancamento.tipo         = lote.tipo          
                     and   valor_lancamento.cod_lote     = lote.cod_lote )
                   join contabilidade.lancamento
                     on ( lancamento.cod_lote       = valor_lancamento.cod_lote
                    and   lancamento.tipo           = valor_lancamento.tipo
                    and   lancamento.sequencia      = valor_lancamento.sequencia
                    and   lancamento.exercicio      = valor_lancamento.exercicio
                    and   lancamento.cod_entidade   = valor_lancamento.cod_entidade 
                    and   lancamento.cod_historico  not between 800 and 899 )
                   join contabilidade.plano_recurso
                      on ( plano_recurso.exercicio = plano_analitica.exercicio
                     and   plano_recurso.cod_plano = plano_analitica.cod_plano )             
                   left join orcamento.recurso_direto
                      on ( recurso_direto.exercicio = plano_recurso.exercicio
                     and   recurso_direto.cod_recurso = plano_recurso.cod_recurso )
                
                WHERE  plano_conta.exercicio = '''|| stExercicio ||'''
                        '|| stCondicao ||'
                        and lote.dt_lote between to_date( '''|| data_ini||''' , ''dd/mm/yyyy'' ) 
                                       and   to_date( '''|| data_fim ||''' , ''dd/mm/yyyy'' )
                        '|| stCondEntidades ||'
                        AND valor_lancamento.tipo <> ''I''
                        AND plano_conta.cod_estrutural NOT LIKE ''2.1.8.8.1%''
                GROUP by recurso_direto.cod_recurso , recurso_direto.exercicio, nom_recurso , recurso_direto.tipo
                ORDER BY nom_recurso ASC
                ';

EXECUTE stSqlInsert;

stSqlInsert :='';
stSqlInsert := '
         CREATE TEMPORARY TABLE tmp_recursos_apagar_exercicio AS
            SELECT cod_recurso
		  ,nom_recurso
		  ,exercicio
                 , COALESCE(sum(apagar),0.00) as a_pagar_exercicio 
            from empenho.fn_relatorio_empenhos_a_pagar(
                  ''''
                , '''|| inCod_EntidadeAux ||'''
                , '''|| stExercicio ||'''
                , '''|| data_ini ||'''
                , '''|| data_fim ||'''
                , ''''
                , ''''
                , ''''
                , ''''
                , ''''
                , ''''
                , ''''
                , ''''
                ) as retorno(                                                                              
                    cod_entidade                integer,                                                            
                    cod_empenho                 integer,                                                            
                    exercicio                   char(4),                                                            
                    dt_emissao                  text,                                                               
                    cgm                         integer,                                                            
                    credor                      varchar,                                                            
                    empenhado                   numeric,                                                            
                    liquidado                   numeric,                                                            
                    pago                        numeric,                                                            
                    apagar                      numeric,                                                            
                    apagarliquidado             numeric,                                                            
                    cod_recurso                 integer,                                                            
                    nom_recurso                 varchar,                                                          
                    masc_recurso_red            varchar
                    )
                
                
                GROUP BY cod_recurso, nom_recurso, exercicio
                ORDER BY nom_recurso
        ';

EXECUTE stSqlInsert;

stSqlInsert := '';
stSqlInsert := '
                CREATE TEMPORARY TABLE tmp_recurso_apagar_exercicios_anteriores AS
                    SELECT
                                      tb.cod_recurso
                                    , tb.tipo
                                    , rd.tipo as tipo_rd
                                    , sum(tb.total_processados_exercicios_anteriores) AS col1
                                    , sum(tb.total_processados_exercicio_anterior) AS col2
                                    , sum(tb.total_nao_processados_exercicios_anteriores) AS col5
                                    , sum(tb.total_nao_processados_exercicio_anterior) AS col6
                                    , sum(tb.liquidados_nao_pagos) as liquidados_nao_pagos
                                    , sum(tb.empenhados_nao_liquidados) as empenhados_nao_liquidados
                                    , sum(tb.empenhados_nao_liquidados_cancelados) as empenhados_nao_liquidados_cancelados
                                    , sum(tb.caixa_liquida) as caixa_liquida
                                FROM stn.fn_rgf_anexo6novo_recurso('''|| stExercicio ||''','''|| inCod_EntidadeAux ||''','''||data_fim||''')
                                AS tb
                                    (  cod_recurso integer
                                        , tipo varchar
                                        , cod_entidade integer
                                        , total_processados_exercicios_anteriores numeric
                                        , total_processados_exercicio_anterior numeric
                                        , total_nao_processados_exercicios_anteriores numeric
                                        , total_nao_processados_exercicio_anterior numeric
                                        , liquidados_nao_pagos numeric
                                        , empenhados_nao_liquidados numeric
                                        , empenhados_nao_liquidados_cancelados numeric
                                        , caixa_liquida numeric                                      
                                    )
                                JOIN orcamento.recurso_direto as rd
                                    ON rd.cod_recurso = tb.cod_recurso
                                AND rd.exercicio = '|| stExercicio ||'::varchar
                                ';
                                
            IF (StRPPS = 'false' ) THEN
                stSqlInsert := stSqlInsert || '
                                                AND tb.cod_entidade NOT IN ((SELECT valor::INTEGER
                                                                         FROM administracao.configuracao
                                                                            WHERE configuracao.parametro = ''cod_entidade_rpps''
                                                                    	   AND configuracao.exercicio = '''|| stExercicio ||'''))
                                            
                                            
                                GROUP BY tb.cod_recurso, tb.tipo, tipo_rd
                                ORDER BY tb.cod_recurso, tb.tipo
                                            
                                            
                            ';
            ELSE
                stSqlInsert := stSqlInsert || ' 
                                GROUP BY tb.cod_recurso, tb.tipo, tipo_rd
                                ORDER BY tb.cod_recurso, tb.tipo
                                ';
            END IF;
                                
                                

EXECUTE stSqlInsert;

stSqlInsert := '';
stSqlInsert := '
            CREATE TEMPORARY TABLE tmp_recurso_consignacoes AS                
                SELECT *
                            FROM   stn.pl_recurso_saldo('''|| stExercicio ||'''
                                                        ,''' || data_ini || '''
                                                        , '''|| data_fim|| '''
                                                        , ''AND plano_conta.cod_estrutural like ''''2.1.8.8.1%'''' ''
                                                        , ''' || inCod_EntidadeAux || '''
                                                        , '''|| stRPPS || ''')
                            as  (
                                     cod_recurso		integer
                                    ,exercicio		        char(4)
                                    ,nom_recurso		varchar
                                    ,tipo_recurso		char(1)
                                    ,valor_positivo             numeric
                                    ,valor_negativo             numeric
                                    
                                ) 
            ';

EXECUTE stSqlInsert;


stSqlInsert := '
            CREATE TEMPORARY TABLE tmp_recurso_saldos AS
                SELECT
                            cod_recurso
                            ,exercicio
                            ,nom_recurso
                            ,tipo_recurso
                            ,SUM(valor_positivo) as valor_positivo
                            ,SUM(valor_negativo) as valor_negativo
                            ,SUM(a_pagar_exercicio_anteriores) as a_pagar_exercicio_anteriores
                            ,SUM(caixa) as caixa
                            
                FROM(    
                    SELECT
                             tmp_recurso_positivo.cod_recurso
                            ,tmp_recurso_positivo.exercicio
                            ,tmp_recurso_positivo.nom_recurso
                            ,tmp_recurso_positivo.tipo_recurso
                            ,COALESCE( tmp_recurso_positivo.valor_positivo,0.00) as valor_positivo
                            ,0 as valor_negativo
                            ,0 as valor_positivo_consignacoes
                            ,0 as valor_negativo_consignacoes
                            ,0 as a_pagar_exercicio_anteriores
                            ,0 as caixa
                    FROM      tmp_recurso_positivo

                        UNION ALL

                    SELECT
                             tmp_recurso_negativo.cod_recurso
                            ,tmp_recurso_negativo.exercicio
                            ,tmp_recurso_negativo.nom_recurso
                            ,tmp_recurso_negativo.tipo_recurso
                            ,0 as valor_positivo
                            ,COALESCE( tmp_recurso_negativo.valor_negativo,0.00) as valor_negativo
                            ,0 as valor_positivo_consignacoes
                            ,0 as valor_negativo_consignacoes
                            ,0 as a_pagar_exercicio_anteriores
                            ,0 as caixa
                    FROM      tmp_recurso_negativo
                    
                        UNION ALL

                    SELECT
                             tmp_recurso_consignacoes.cod_recurso
                            ,tmp_recurso_consignacoes.exercicio
                            ,tmp_recurso_consignacoes.nom_recurso
                            ,tmp_recurso_consignacoes.tipo_recurso
                            ,0 as valor_positivo
                            ,0 as valor_negativo
                            ,COALESCE( tmp_recurso_consignacoes.valor_positivo,0.00) as valor_positivo_consignacoes
                            ,COALESCE( tmp_recurso_consignacoes.valor_negativo,0.00) as valor_negativo_consignacoes
                            ,0 as a_pagar_exercicio_anteriores
                            ,0 as caixa
                    FROM      tmp_recurso_consignacoes
                    
                        UNION ALL
                    
                    SELECT
                             tmp_recurso_apagar_exercicios_anteriores.cod_recurso
                            ,'''||stExercicio||''' as exercicio
                            ,tmp_recurso_apagar_exercicios_anteriores.tipo
                            ,tmp_recurso_apagar_exercicios_anteriores.tipo_rd
                            ,0 as valor_positivo
                            ,0 as valor_negativo
                            ,0 as valor_positivo_consignacoes
                            ,0 as valor_negativo_consignacoes
                            ,COALESCE( tmp_recurso_apagar_exercicios_anteriores.col1
                                        + tmp_recurso_apagar_exercicios_anteriores.col2
                                        + tmp_recurso_apagar_exercicios_anteriores.col5
                                        + tmp_recurso_apagar_exercicios_anteriores.col6,0.00)*(-1) as a_pagar_exercicio_anteriores
                            ,COALESCE(caixa_liquida,0.00) as caixa
                    FROM      tmp_recurso_apagar_exercicios_anteriores
                    
                    
                ) as saldos
                
                GROUP BY    cod_recurso
                            ,exercicio
                            ,nom_recurso
                            ,tipo_recurso
                            
                ';

EXECUTE stSqlInsert;

stSql := '';
stSql := '
        SELECT
                tmp_recurso_saldos.tipo_recurso
                , tmp_recurso_saldos.cod_recurso
		, CAST(tmp_recurso_saldos.exercicio AS VARCHAR) AS exercicio
		, tmp_recurso_saldos.nom_recurso
                , SUM(tmp_recurso_saldos.valor_positivo) AS valor_positivo
                , SUM(tmp_recurso_saldos.valor_negativo) AS valor_negativo
		, (COALESCE(sum(COALESCE(tmp_recurso_saldos.valor_positivo,0)),0) + COALESCE(SUM(COALESCE(tmp_recurso_saldos.valor_negativo,0)),0.00))*(-1) as saldo
		, SUM(COALESCE( tmp_recursos_apagar_exercicio.a_pagar_exercicio, 0.00))*(-1) as a_pagar_exercicio
                , COALESCE(tmp_recurso_saldos.a_pagar_exercicio_anteriores,0.00) as a_pagar_exercicio_anteriores
                , SUM(consignacoes.valor_positivo) as valor_credito
                , sum(consignacoes.valor_negativo) as valor_debito
                , COALESCE(SUM(COALESCE(tmp_recurso_inicial.valor_consignacoes)), 0.00) + (COALESCE(SUM(COALESCE(consignacoes.valor_positivo, 0.00))) + COALESCE(SUM(COALESCE(consignacoes.valor_negativo, 0.00)))) as consignacoes
                , tmp_recurso_saldos.caixa
        FROM tmp_recurso_saldos
        
        LEFT JOIN tmp_recurso_apagar_exercicios_anteriores as RAEA
          ON RAEA.cod_recurso = tmp_recurso_saldos.cod_recurso
        
        LEFT JOIN tmp_recursos_apagar_exercicio
            ON( RAEA.cod_recurso  = tmp_recursos_apagar_exercicio.cod_recurso )
        
        LEFT JOIN  tmp_recurso_consignacoes as consignacoes
            ON( consignacoes.cod_recurso = tmp_recurso_saldos.cod_recurso)
            
        LEFT JOIN  tmp_recurso_inicial
            ON( tmp_recurso_inicial.cod_recurso = tmp_recurso_saldos.cod_recurso)    
             
        WHERE tmp_recurso_saldos.nom_recurso IS NOT NULL
        
                   
        GROUP BY
               tmp_recurso_saldos.tipo_recurso
                , tmp_recurso_saldos.cod_recurso
		, tmp_recurso_saldos.exercicio	
		, tmp_recurso_saldos.nom_recurso
                ,tmp_recurso_saldos.a_pagar_exercicio_anteriores
                ,tmp_recurso_saldos.caixa
                
                
                                
	ORDER BY  tmp_recurso_saldos.nom_recurso

        ';

FOR reRegistro IN EXECUTE stSql
LOOP
    RETURN NEXT reRegistro;
END LOOP;



DROP TABLE tmp_recursos_apagar_exercicio;
DROP TABLE tmp_recurso_positivo;
DROP TABLE tmp_recurso_negativo;
DROP TABLE tmp_recurso_apagar_exercicios_anteriores;
DROP TABLE tmp_recurso_consignacoes;
DROP TABLE tmp_recurso_saldos;
DROP TABLE tmp_recurso_inicial;

END;
$$ LANGUAGE 'plpgsql';


