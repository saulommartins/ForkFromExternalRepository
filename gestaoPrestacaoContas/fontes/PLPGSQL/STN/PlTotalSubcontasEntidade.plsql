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
    * Data de Criação   : 08/10/2007

    * @author 

    * @ignore

    $Id: PlTotalSubcontasEntidade.plsql 66504 2016-09-05 21:48:38Z michel $

    * Casos de uso : uc-06.01.20
*/

CREATE OR REPLACE FUNCTION stn.pl_total_subcontas_entidade ( varchar, varchar ) RETURNS SETOF RECORD AS $$

DECLARE
    dtData                  ALIAS FOR $1;
    stCodEntidades          ALIAS FOR $2;
    inExercicio             INTEGER;
    reRegistro              RECORD;
    stSql                   VARCHAR :='';
    stSqlAux                VARCHAR :='';
    stPl                    VARCHAR :='';
    stConta                 VARCHAR :='4.';
    stContaDedutora         VARCHAR :='9.';
    inMes                   INTEGER;
    flTotalMes              FLOAT[] ;
    flTotalReceita          FLOAT[] ;
    flTotalMes_OTC          FLOAT[] ;
    flTotalReceita_OTC      FLOAT[] ;
    stDeduzirIRRF           VARCHAR := '';
    stMes                   varchar;
    arDatas                 varchar[];
    inAno                   integer;
    i                       integer;
    stDataIni               varchar;
    arValoresConfigurados   NUMERIC[];
    nuValorAux              NUMERIC;

BEGIN
        inExercicio :=  substr(dtData, 7, 4 ) ;
        inMes       :=  substr(dtData, 4, 2 ) ; 

        inAno := inExercicio;

        IF inExercicio > 2012 THEN
            stPl    := '_novo';
            stConta := '';
            stContaDedutora := '';

            --ALTERACOES PARA 2016 buscar as datas de acordo com o filtro
            IF inExercicio >= 2016 THEN
                i := 12;
                WHILE i >= 1 LOOP
                    IF ( inMes < 10 ) THEN
                        stMes := '0' || inMes;
                    ELSE
                        stMes := inMes;
                    END IF;

                    arDatas[i] :=  '01/' || stMes || '/'|| inAno;

                    i := i - 1;
                    inMes := inMes - 1;
                    IF ( inMes = 0 ) THEN
                        inAno := inAno -1;
                        inMes := 12;
                    END IF;
                END LOOP;
            END IF;--IF inExercicio >= 2016 
        END IF;--IF inExercicio > 2012

        --Dados para o relatorio
        stSql := 'CREATE TEMPORARY TABLE tmp_valor AS (
            SELECT
                  ocr.cod_estrutural as cod_estrutural
                , lote.dt_lote as data
                , vl.vl_lancamento as valor
                , vl.oid as primeira
            FROM
                contabilidade.valor_lancamento      as vl   
                ,orcamento.conta_receita             as ocr  
                ,contabilidade.lancamento_receita    as lr   
                ,contabilidade.lancamento            as lan  
                ,contabilidade.lote                  as lote
                ,orcamento.receita                   as ore                  

            WHERE
                   ore.exercicio        IN (  ' || quote_literal(inExercicio) || ', ' || quote_literal(inExercicio -1) || ')
                AND ore.cod_entidade    IN ('|| stCodEntidades ||')
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
                AND lan.cod_historico   != 800
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

            UNION
            SELECT
                  ocr.cod_estrutural as cod_estrutural
                , lote.dt_lote as data
                , vl.vl_lancamento as valor
                , vl.oid as segunda
            FROM
                contabilidade.valor_lancamento      as vl   
                ,orcamento.conta_receita             as ocr  
                ,contabilidade.lancamento_receita    as lr   
                ,contabilidade.lancamento            as lan  
                ,contabilidade.lote                  as lote 
                ,orcamento.receita                   as ore                  

            WHERE
                    ore.exercicio       in (  ' || quote_literal(inExercicio) || ', ' || quote_literal(inExercicio -1) || ')
                AND ore.cod_entidade    IN ('|| stCodEntidades ||')
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
                AND lan.cod_historico   != 800
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

                )';

        EXECUTE stSql;

        --- Linha   RECEITAS CORRENTES(I)
        stSql := ' SELECT 1 as ordem
                        , 1 AS nivel
                        , cast(retorno.cod_conta as varchar )                
                        , cast(trim(retorno.nom_conta) || '' (I)'' as varchar ) AS nom_conta
                        , cast(retorno.cod_estrutural  as varchar ) AS cod_estrutural        
                        , cast(retorno.mes_1   as numeric )         
                        , cast(retorno.mes_2   as numeric )        
                        , cast(retorno.mes_3   as numeric )        
                        , cast(retorno.mes_4   as numeric )        
                        , cast(retorno.mes_5   as numeric )        
                        , cast(retorno.mes_6   as numeric )        
                        , cast(retorno.mes_7   as numeric )        
                        , cast(retorno.mes_8   as numeric )        
                        , cast(retorno.mes_9   as numeric )        
                        , cast(retorno.mes_10  as numeric )        
                        , cast(retorno.mes_11  as numeric )        
                        , cast(retorno.mes_12  as numeric ) 
                        , cast(0 as numeric) as total_mes_1         
                        , cast(0 as numeric) as total_mes_2         
                        , cast(0 as numeric) as total_mes_3         
                        , cast(0 as numeric) as total_mes_4         
                        , cast(0 as numeric) as total_mes_5         
                        , cast(0 as numeric) as total_mes_6         
                        , cast(0 as numeric) as total_mes_7         
                        , cast(0 as numeric) as total_mes_8         
                        , cast(0 as numeric) as total_mes_9         
                        , cast(0 as numeric) as total_mes_10        
                        , cast(0 as numeric) as total_mes_11        
                        , cast(0 as numeric) as total_mes_12        
                    FROM stn.sub_consulta_rcl'||stPl||'('|| quote_literal(dtData) ||' ,''4.1'', 2)
                          as retorno (  cod_conta       varchar
                                       ,nom_conta      varchar
                                       ,cod_estrutural varchar
                                       ,mes_1          numeric
                                       ,mes_2          numeric
                                       ,mes_3          numeric
                                       ,mes_4          numeric
                                       ,mes_5          numeric
                                       ,mes_6          numeric
                                       ,mes_7          numeric
                                       ,mes_8          numeric
                                       ,mes_9          numeric
                                       ,mes_10         numeric
                                       ,mes_11         numeric
                                       ,mes_12         numeric)
                
                   UNION
            
                 --- Nivel 4.1 contas : Receita tributaria, Receita de Contribuições, Receita Patrimonial, Receita Agropecuaria .............

                  SELECT 2 as ordem
                       , 2 AS nivel
                       , cast(retorno.cod_conta as varchar )                 
                       , cast(''Receita Tributária'' as varchar ) AS nom_conta
                       , cast(retorno.cod_estrutural as varchar) AS cod_estrutural   
                       , cast(retorno.mes_1   as numeric)       
                       , cast(retorno.mes_2   as numeric)       
                       , cast(retorno.mes_3   as numeric)       
                       , cast(retorno.mes_4   as numeric)       
                       , cast(retorno.mes_5   as numeric)       
                       , cast(retorno.mes_6   as numeric)       
                       , cast(retorno.mes_7   as numeric)       
                       , cast(retorno.mes_8   as numeric)       
                       , cast(retorno.mes_9   as numeric)       
                       , cast(retorno.mes_10  as numeric)       
                       , cast(retorno.mes_11  as numeric)       
                       , cast(retorno.mes_12  as numeric) 
                       , cast(0 as numeric) as total_mes_1         
                       , cast(0 as numeric) as total_mes_2         
                       , cast(0 as numeric) as total_mes_3         
                       , cast(0 as numeric) as total_mes_4         
                       , cast(0 as numeric) as total_mes_5         
                       , cast(0 as numeric) as total_mes_6         
                       , cast(0 as numeric) as total_mes_7         
                       , cast(0 as numeric) as total_mes_8         
                       , cast(0 as numeric) as total_mes_9         
                       , cast(0 as numeric) as total_mes_10        
                       , cast(0 as numeric) as total_mes_11        
                       , cast(0 as numeric) as total_mes_12        
                    
                    FROM stn.sub_consulta_rcl'||stPl||'('|| quote_literal(dtData) ||' ,''4.1.1'', 3)
                          as retorno ( cod_conta       varchar
                                       ,nom_conta      varchar
                                       ,cod_estrutural varchar
                                       ,mes_1          numeric
                                       ,mes_2          numeric
                                       ,mes_3          numeric
                                       ,mes_4          numeric
                                       ,mes_5          numeric
                                       ,mes_6          numeric
                                       ,mes_7          numeric
                                       ,mes_8          numeric
                                       ,mes_9          numeric
                                       ,mes_10         numeric
                                       ,mes_11         numeric
                                       ,mes_12         numeric)
                
                   UNION
                   
                   --- Linha IPTU
                    
                   SELECT 3 as ordem
                        , 3 AS nivel
                        , cast(cod_conta as varchar ) 
                        , cast(''IPTU'' as varchar ) as nom_conta
                        , cast(retorno.cod_estrutural as varchar) AS cod_estrutural 
                        , cast( retorno.mes_1  as numeric )
                        , cast( retorno.mes_2  as numeric )
                        , cast( retorno.mes_3  as numeric )
                        , cast( retorno.mes_4  as numeric )
                        , cast( retorno.mes_5  as numeric )
                        , cast( retorno.mes_6  as numeric )
                        , cast( retorno.mes_7  as numeric )
                        , cast( retorno.mes_8  as numeric )
                        , cast( retorno.mes_9  as numeric )
                        , cast( retorno.mes_10 as numeric )
                        , cast( retorno.mes_11 as numeric )
                        , cast( retorno.mes_12 as numeric )
                        , cast(0 as numeric) as total_mes_1
                        , cast(0 as numeric) as total_mes_2
                        , cast(0 as numeric) as total_mes_3
                        , cast(0 as numeric) as total_mes_4
                        , cast(0 as numeric) as total_mes_5
                        , cast(0 as numeric) as total_mes_6
                        , cast(0 as numeric) as total_mes_7
                        , cast(0 as numeric) as total_mes_8
                        , cast(0 as numeric) as total_mes_9
                        , cast(0 as numeric) as total_mes_10
                        , cast(0 as numeric) as total_mes_11
                        , cast(0 as numeric) as total_mes_12
                     FROM stn.sub_consulta_rcl'||stPl||'('|| quote_literal(dtData) ||' ,''4.1.1.1.2.02'', 6)
                           as retorno ( cod_conta      varchar
                                       ,nom_conta      varchar
                                       ,cod_estrutural varchar
                                       ,mes_1          numeric
                                       ,mes_2          numeric
                                       ,mes_3          numeric
                                       ,mes_4          numeric
                                       ,mes_5          numeric
                                       ,mes_6          numeric
                                       ,mes_7          numeric
                                       ,mes_8          numeric
                                       ,mes_9          numeric
                                       ,mes_10         numeric
                                       ,mes_11         numeric
                                       ,mes_12         numeric )

                   UNION
                   
                   --- Linha ISS

                  SELECT 4 as ordem
                        , 3 AS nivel
                        , cast( retorno.cod_conta as varchar ) 
                        , cast( ''ISS'' as varchar ) as nom_conta
                        , cast( retorno.cod_estrutural as varchar ) as cod_estrutural
                        , cast( retorno.mes_1  as numeric )
                        , cast( retorno.mes_2  as numeric )
                        , cast( retorno.mes_3  as numeric )
                        , cast( retorno.mes_4  as numeric )
                        , cast( retorno.mes_5  as numeric )
                        , cast( retorno.mes_6  as numeric )
                        , cast( retorno.mes_7  as numeric )
                        , cast( retorno.mes_8  as numeric )
                        , cast( retorno.mes_9  as numeric )
                        , cast( retorno.mes_10 as numeric )
                        , cast( retorno.mes_11 as numeric )
                        , cast( retorno.mes_12 as numeric )
                        , cast(0 as numeric) as total_mes_1
                        , cast(0 as numeric) as total_mes_2
                        , cast(0 as numeric) as total_mes_3
                        , cast(0 as numeric) as total_mes_4
                        , cast(0 as numeric) as total_mes_5
                        , cast(0 as numeric) as total_mes_6
                        , cast(0 as numeric) as total_mes_7
                        , cast(0 as numeric) as total_mes_8
                        , cast(0 as numeric) as total_mes_9
                        , cast(0 as numeric) as total_mes_10
                        , cast(0 as numeric) as total_mes_11
                        , cast(0 as numeric) as total_mes_12
                    FROM stn.sub_consulta_rcl'||stPl||'('|| quote_literal(dtData) ||' ,''4.1.1.1.3.05'', 6)
                           as retorno ( cod_conta      varchar
                                       ,nom_conta      varchar
                                       ,cod_estrutural varchar
                                       ,mes_1          numeric
                                       ,mes_2          numeric
                                       ,mes_3          numeric
                                       ,mes_4          numeric
                                       ,mes_5          numeric
                                       ,mes_6          numeric
                                       ,mes_7          numeric
                                       ,mes_8          numeric
                                       ,mes_9          numeric
                                       ,mes_10         numeric
                                       ,mes_11         numeric
                                       ,mes_12         numeric )

                   UNION
           
                   --- Linha ITBI   

                  SELECT 5 as ordem
                       , 3 AS nivel
                       , cod_conta
                       , cast(''ITBI'' as varchar) as nom_conta
                       , cast(retorno.cod_estrutural as varchar) AS cod_estrutural 
                       , cast( retorno.mes_1  as numeric )
                       , cast( retorno.mes_2  as numeric )
                       , cast( retorno.mes_3  as numeric )
                       , cast( retorno.mes_4  as numeric )
                       , cast( retorno.mes_5  as numeric )
                       , cast( retorno.mes_6  as numeric )
                       , cast( retorno.mes_7  as numeric )
                       , cast( retorno.mes_8  as numeric )
                       , cast( retorno.mes_9  as numeric )
                       , cast( retorno.mes_10 as numeric )
                       , cast( retorno.mes_11 as numeric )
                       , cast( retorno.mes_12 as numeric )
                       , cast(0 as numeric) as total_mes_1
                       , cast(0 as numeric) as total_mes_2
                       , cast(0 as numeric) as total_mes_3
                       , cast(0 as numeric) as total_mes_4
                       , cast(0 as numeric) as total_mes_5
                       , cast(0 as numeric) as total_mes_6
                       , cast(0 as numeric) as total_mes_7
                       , cast(0 as numeric) as total_mes_8
                       , cast(0 as numeric) as total_mes_9
                       , cast(0 as numeric) as total_mes_10
                       , cast(0 as numeric) as total_mes_11
                       , cast(0 as numeric) as total_mes_12
                    FROM stn.sub_consulta_rcl'||stPl||'('|| quote_literal(dtData) ||' ,''4.1.1.1.2.08'', 6)
                           as retorno ( cod_conta      varchar
                                       ,nom_conta      varchar
                                       ,cod_estrutural varchar
                                       ,mes_1          numeric
                                       ,mes_2          numeric
                                       ,mes_3          numeric
                                       ,mes_4          numeric
                                       ,mes_5          numeric
                                       ,mes_6          numeric
                                       ,mes_7          numeric
                                       ,mes_8          numeric
                                       ,mes_9          numeric
                                       ,mes_10         numeric
                                       ,mes_11         numeric
                                       ,mes_12         numeric )

                   UNION

                   --- Linha IRRF

                  SELECT 6 as ordem
                       , 3 AS nivel
                       , cast( retorno.cod_conta as varchar )
                       , cast( ''IRRF''  as varchar ) as nom_conta
                       , cast( retorno.cod_estrutural as varchar ) as cod_estrutural
                       , cast( retorno.mes_1  as numeric )
                       , cast( retorno.mes_2  as numeric )
                       , cast( retorno.mes_3  as numeric )
                       , cast( retorno.mes_4  as numeric )
                       , cast( retorno.mes_5  as numeric )
                       , cast( retorno.mes_6  as numeric )
                       , cast( retorno.mes_7  as numeric )
                       , cast( retorno.mes_8  as numeric )
                       , cast( retorno.mes_9  as numeric )
                       , cast( retorno.mes_10 as numeric )
                       , cast( retorno.mes_11 as numeric )
                       , cast( retorno.mes_12 as numeric )
                       , cast(0 as numeric) as total_mes_1
                       , cast(0 as numeric) as total_mes_2
                       , cast(0 as numeric) as total_mes_3
                       , cast(0 as numeric) as total_mes_4
                       , cast(0 as numeric) as total_mes_5
                       , cast(0 as numeric) as total_mes_6
                       , cast(0 as numeric) as total_mes_7
                       , cast(0 as numeric) as total_mes_8
                       , cast(0 as numeric) as total_mes_9
                       , cast(0 as numeric) as total_mes_10
                       , cast(0 as numeric) as total_mes_11
                       , cast(0 as numeric) as total_mes_12
                    FROM stn.sub_consulta_rcl'||stPl||'('|| quote_literal(dtData) ||' ,''4.1.1.1.2.04'', 6)
                           as retorno ( cod_conta      varchar
                                       ,nom_conta      varchar
                                       ,cod_estrutural varchar
                                       ,mes_1          numeric
                                       ,mes_2          numeric
                                       ,mes_3          numeric
                                       ,mes_4          numeric
                                       ,mes_5          numeric
                                       ,mes_6          numeric
                                       ,mes_7          numeric
                                       ,mes_8          numeric
                                       ,mes_9          numeric
                                       ,mes_10         numeric
                                       ,mes_11         numeric
                                       ,mes_12         numeric )
                  
                   UNION
               
                   --Outras Receitas Tributárias

                  SELECT 7 as ordem
                       , 3 AS nivel
                       , cast(0  as varchar) as cod_conta   
                       , cast(''Outras Receitas Tributárias''  as varchar) as nom_conta     
                       , cast(''4.1.1.1.2.09.00.00.00.00''     as varchar) as cod_estrutural
                       , cast(0 as numeric) as mes_1         
                       , cast(0 as numeric) as mes_2         
                       , cast(0 as numeric) as mes_3         
                       , cast(0 as numeric) as mes_4         
                       , cast(0 as numeric) as mes_5         
                       , cast(0 as numeric) as mes_6         
                       , cast(0 as numeric) as mes_7         
                       , cast(0 as numeric) as mes_8         
                       , cast(0 as numeric) as mes_9         
                       , cast(0 as numeric) as mes_10        
                       , cast(0 as numeric) as mes_11        
                       , cast(0 as numeric) as mes_12        
                       , cast(0 as numeric) as total_mes_1         
                       , cast(0 as numeric) as total_mes_2         
                       , cast(0 as numeric) as total_mes_3         
                       , cast(0 as numeric) as total_mes_4         
                       , cast(0 as numeric) as total_mes_5         
                       , cast(0 as numeric) as total_mes_6         
                       , cast(0 as numeric) as total_mes_7         
                       , cast(0 as numeric) as total_mes_8         
                       , cast(0 as numeric) as total_mes_9         
                       , cast(0 as numeric) as total_mes_10        
                       , cast(0 as numeric) as total_mes_11        
                       , cast(0 as numeric) as total_mes_12

                   UNION
               
               --- Nivel 4.1 contas : Receita de Contribuições, Receita Patrimonial, Receita Agropecuaria .............

                  SELECT 8 as ordem
                       , 2 AS nivel
                       , cast( retorno.cod_conta as varchar )               
                       , cast( ''Receita de Contribuições'' as varchar ) AS nom_conta
                       , cast( retorno.cod_estrutural as varchar) AS cod_estrutural          
                       , cast( retorno.mes_1  as numeric )
                       , cast( retorno.mes_2  as numeric )
                       , cast( retorno.mes_3  as numeric )
                       , cast( retorno.mes_4  as numeric )
                       , cast( retorno.mes_5  as numeric )
                       , cast( retorno.mes_6  as numeric )
                       , cast( retorno.mes_7  as numeric )
                       , cast( retorno.mes_8  as numeric )
                       , cast( retorno.mes_9  as numeric )
                       , cast( retorno.mes_10 as numeric )
                       , cast( retorno.mes_11 as numeric )
                       , cast( retorno.mes_12 as numeric )
                       , cast(0 as numeric) as total_mes_1         
                       , cast(0 as numeric) as total_mes_2         
                       , cast(0 as numeric) as total_mes_3         
                       , cast(0 as numeric) as total_mes_4         
                       , cast(0 as numeric) as total_mes_5         
                       , cast(0 as numeric) as total_mes_6         
                       , cast(0 as numeric) as total_mes_7         
                       , cast(0 as numeric) as total_mes_8         
                       , cast(0 as numeric) as total_mes_9         
                       , cast(0 as numeric) as total_mes_10        
                       , cast(0 as numeric) as total_mes_11        
                       , cast(0 as numeric) as total_mes_12        
                    
                    FROM stn.sub_consulta_rcl'||stPl||'('|| quote_literal(dtData) ||' ,''4.1.2'', 3)
                          as retorno ( cod_conta       varchar
                                       ,nom_conta      varchar
                                       ,cod_estrutural varchar
                                       ,mes_1          numeric
                                       ,mes_2          numeric
                                       ,mes_3          numeric
                                       ,mes_4          numeric
                                       ,mes_5          numeric
                                       ,mes_6          numeric
                                       ,mes_7          numeric
                                       ,mes_8          numeric
                                       ,mes_9          numeric
                                       ,mes_10         numeric
                                       ,mes_11         numeric
                                       ,mes_12         numeric)
                                       
                   UNION                   
                                       
                   --- Nivel 4.1 contas : Receita Patrimonial

                  SELECT 9 as ordem
                       , 2 AS nivel
                       , cast( retorno.cod_conta as varchar )               
                       , cast(''Receita Patrimonial'' as varchar ) AS nom_conta
                       , cast(retorno.cod_estrutural as varchar) AS cod_estrutural      
                       , cast( retorno.mes_1  as numeric )
                       , cast( retorno.mes_2  as numeric )
                       , cast( retorno.mes_3  as numeric )
                       , cast( retorno.mes_4  as numeric )
                       , cast( retorno.mes_5  as numeric )
                       , cast( retorno.mes_6  as numeric )
                       , cast( retorno.mes_7  as numeric )
                       , cast( retorno.mes_8  as numeric )
                       , cast( retorno.mes_9  as numeric )
                       , cast( retorno.mes_10 as numeric )
                       , cast( retorno.mes_11 as numeric )
                       , cast( retorno.mes_12 as numeric )   
                       , cast(0 as numeric) as total_mes_1         
                       , cast(0 as numeric) as total_mes_2         
                       , cast(0 as numeric) as total_mes_3         
                       , cast(0 as numeric) as total_mes_4         
                       , cast(0 as numeric) as total_mes_5         
                       , cast(0 as numeric) as total_mes_6         
                       , cast(0 as numeric) as total_mes_7         
                       , cast(0 as numeric) as total_mes_8         
                       , cast(0 as numeric) as total_mes_9         
                       , cast(0 as numeric) as total_mes_10        
                       , cast(0 as numeric) as total_mes_11        
                       , cast(0 as numeric) as total_mes_12        
                    
                    FROM stn.sub_consulta_rcl'||stPl||'('|| quote_literal(dtData) ||' ,''4.1.3'', 3)
                          as retorno ( cod_conta       varchar
                                       ,nom_conta      varchar
                                       ,cod_estrutural varchar
                                       ,mes_1          numeric
                                       ,mes_2          numeric
                                       ,mes_3          numeric
                                       ,mes_4          numeric
                                       ,mes_5          numeric
                                       ,mes_6          numeric
                                       ,mes_7          numeric
                                       ,mes_8          numeric
                                       ,mes_9          numeric
                                       ,mes_10         numeric
                                       ,mes_11         numeric
                                       ,mes_12         numeric)
                                       
                   UNION                    
                   
                   --- Nivel 4.1 contas :  Receita Agropecuaria .............

                  SELECT 10 as ordem
                       , 2 AS nivel
                       , cast(retorno.cod_conta as varchar )               
                       , cast(''Receita Agropecuária'' as varchar ) AS nom_conta
                       , cast(retorno.cod_estrutural as varchar) AS cod_estrutural      
                       , cast( retorno.mes_1  as numeric )
                       , cast( retorno.mes_2  as numeric )
                       , cast( retorno.mes_3  as numeric )
                       , cast( retorno.mes_4  as numeric )
                       , cast( retorno.mes_5  as numeric )
                       , cast( retorno.mes_6  as numeric )
                       , cast( retorno.mes_7  as numeric )
                       , cast( retorno.mes_8  as numeric )
                       , cast( retorno.mes_9  as numeric )
                       , cast( retorno.mes_10 as numeric )
                       , cast( retorno.mes_11 as numeric )
                       , cast( retorno.mes_12 as numeric )    
                       , cast(0 as numeric) as total_mes_1         
                       , cast(0 as numeric) as total_mes_2         
                       , cast(0 as numeric) as total_mes_3         
                       , cast(0 as numeric) as total_mes_4         
                       , cast(0 as numeric) as total_mes_5         
                       , cast(0 as numeric) as total_mes_6         
                       , cast(0 as numeric) as total_mes_7         
                       , cast(0 as numeric) as total_mes_8         
                       , cast(0 as numeric) as total_mes_9         
                       , cast(0 as numeric) as total_mes_10        
                       , cast(0 as numeric) as total_mes_11        
                       , cast(0 as numeric) as total_mes_12        
                    
                    FROM stn.sub_consulta_rcl'||stPl||'('|| quote_literal(dtData) ||' ,''4.1.4'', 3)
                          as retorno ( cod_conta       varchar
                                       ,nom_conta      varchar
                                       ,cod_estrutural varchar
                                       ,mes_1          numeric
                                       ,mes_2          numeric
                                       ,mes_3          numeric
                                       ,mes_4          numeric
                                       ,mes_5          numeric
                                       ,mes_6          numeric
                                       ,mes_7          numeric
                                       ,mes_8          numeric
                                       ,mes_9          numeric
                                       ,mes_10         numeric
                                       ,mes_11         numeric
                                       ,mes_12         numeric)
                                       
                   UNION
                   
                   --- Nivel 4.1 contas : Receita Industrial

                  SELECT 11 as ordem
                       , 2 AS nivel
                       , cast(retorno.cod_conta as varchar )               
                       , cast(''Receita Industrial'' as varchar ) AS nom_conta
                       , cast(retorno.cod_estrutural as varchar) AS cod_estrutural        
                       , cast( retorno.mes_1  as numeric )
                       , cast( retorno.mes_2  as numeric )
                       , cast( retorno.mes_3  as numeric )
                       , cast( retorno.mes_4  as numeric )
                       , cast( retorno.mes_5  as numeric )
                       , cast( retorno.mes_6  as numeric )
                       , cast( retorno.mes_7  as numeric )
                       , cast( retorno.mes_8  as numeric )
                       , cast( retorno.mes_9  as numeric )
                       , cast( retorno.mes_10 as numeric )
                       , cast( retorno.mes_11 as numeric )
                       , cast( retorno.mes_12 as numeric )     
                       , cast(0 as numeric) as total_mes_1         
                       , cast(0 as numeric) as total_mes_2         
                       , cast(0 as numeric) as total_mes_3         
                       , cast(0 as numeric) as total_mes_4         
                       , cast(0 as numeric) as total_mes_5         
                       , cast(0 as numeric) as total_mes_6         
                       , cast(0 as numeric) as total_mes_7         
                       , cast(0 as numeric) as total_mes_8         
                       , cast(0 as numeric) as total_mes_9         
                       , cast(0 as numeric) as total_mes_10        
                       , cast(0 as numeric) as total_mes_11        
                       , cast(0 as numeric) as total_mes_12        
                    
                    FROM stn.sub_consulta_rcl'||stPl||'('|| quote_literal(dtData) ||' ,''4.1.5'', 3)
                          as retorno ( cod_conta       varchar
                                       ,nom_conta      varchar
                                       ,cod_estrutural varchar
                                       ,mes_1          numeric
                                       ,mes_2          numeric
                                       ,mes_3          numeric
                                       ,mes_4          numeric
                                       ,mes_5          numeric
                                       ,mes_6          numeric
                                       ,mes_7          numeric
                                       ,mes_8          numeric
                                       ,mes_9          numeric
                                       ,mes_10         numeric
                                       ,mes_11         numeric
                                       ,mes_12         numeric)
                                       
                   UNION
                   
                   --- Nivel 4.1 contas : Receita de Serviços

                  SELECT 12 as ordem
                       , 2 AS nivel
                       , cast(retorno.cod_conta as varchar )               
                       , cast(''Receita de Serviços'' as varchar ) AS nom_conta
                       , cast(retorno.cod_estrutural as varchar) AS cod_estrutural     
                       , cast( retorno.mes_1  as numeric )
                       , cast( retorno.mes_2  as numeric )
                       , cast( retorno.mes_3  as numeric )
                       , cast( retorno.mes_4  as numeric )
                       , cast( retorno.mes_5  as numeric )
                       , cast( retorno.mes_6  as numeric )
                       , cast( retorno.mes_7  as numeric )
                       , cast( retorno.mes_8  as numeric )
                       , cast( retorno.mes_9  as numeric )
                       , cast( retorno.mes_10 as numeric )
                       , cast( retorno.mes_11 as numeric )
                       , cast( retorno.mes_12 as numeric )   
                       , cast(0 as numeric) as total_mes_1         
                       , cast(0 as numeric) as total_mes_2         
                       , cast(0 as numeric) as total_mes_3         
                       , cast(0 as numeric) as total_mes_4         
                       , cast(0 as numeric) as total_mes_5         
                       , cast(0 as numeric) as total_mes_6         
                       , cast(0 as numeric) as total_mes_7         
                       , cast(0 as numeric) as total_mes_8         
                       , cast(0 as numeric) as total_mes_9         
                       , cast(0 as numeric) as total_mes_10        
                       , cast(0 as numeric) as total_mes_11        
                       , cast(0 as numeric) as total_mes_12        
                    
                    FROM stn.sub_consulta_rcl'||stPl||'('|| quote_literal(dtData) ||' ,''4.1.6'', 3)
                          as retorno ( cod_conta       varchar
                                       ,nom_conta      varchar
                                       ,cod_estrutural varchar
                                       ,mes_1          numeric
                                       ,mes_2          numeric
                                       ,mes_3          numeric
                                       ,mes_4          numeric
                                       ,mes_5          numeric
                                       ,mes_6          numeric
                                       ,mes_7          numeric
                                       ,mes_8          numeric
                                       ,mes_9          numeric
                                       ,mes_10         numeric
                                       ,mes_11         numeric
                                       ,mes_12         numeric)
                                       
                   UNION
                   
                   --- Nivel 4.1 contas :Transferências Correntes

                  SELECT 13 as ordem
                       , 2 AS nivel
                       , cast(retorno.cod_conta as varchar )                
                       , cast(''Transferências Correntes'' as varchar ) AS nom_conta
                       , cast(retorno.cod_estrutural as varchar) AS cod_estrutural     
                       , cast( retorno.mes_1  as numeric )
                       , cast( retorno.mes_2  as numeric )
                       , cast( retorno.mes_3  as numeric )
                       , cast( retorno.mes_4  as numeric )
                       , cast( retorno.mes_5  as numeric )
                       , cast( retorno.mes_6  as numeric )
                       , cast( retorno.mes_7  as numeric )
                       , cast( retorno.mes_8  as numeric )
                       , cast( retorno.mes_9  as numeric )
                       , cast( retorno.mes_10 as numeric )
                       , cast( retorno.mes_11 as numeric )
                       , cast( retorno.mes_12 as numeric )   
                       , cast(0 as numeric) as total_mes_1         
                       , cast(0 as numeric) as total_mes_2         
                       , cast(0 as numeric) as total_mes_3         
                       , cast(0 as numeric) as total_mes_4         
                       , cast(0 as numeric) as total_mes_5         
                       , cast(0 as numeric) as total_mes_6         
                       , cast(0 as numeric) as total_mes_7         
                       , cast(0 as numeric) as total_mes_8         
                       , cast(0 as numeric) as total_mes_9         
                       , cast(0 as numeric) as total_mes_10        
                       , cast(0 as numeric) as total_mes_11        
                       , cast(0 as numeric) as total_mes_12        
                    
                    FROM stn.sub_consulta_rcl'||stPl||'('|| quote_literal(dtData) ||' ,''4.1.7'', 3)
                          as retorno ( cod_conta       varchar
                                       ,nom_conta      varchar
                                       ,cod_estrutural varchar
                                       ,mes_1          numeric
                                       ,mes_2          numeric
                                       ,mes_3          numeric
                                       ,mes_4          numeric
                                       ,mes_5          numeric
                                       ,mes_6          numeric
                                       ,mes_7          numeric
                                       ,mes_8          numeric
                                       ,mes_9          numeric
                                       ,mes_10         numeric
                                       ,mes_11         numeric
                                       ,mes_12         numeric)
                                       
                  
                     --- Sub COnta de receita: COTA-PARTE DO FPM
                   UNION
                  
                  select  14 as ordem
                       , 3 AS nivel
                       , cast( retorno.cod_conta         as varchar  )
                       , cast( ''Cota-Parte do FPM'' as varchar )
                       , cast( retorno.cod_estrutural    as varchar )
                       , cast( retorno.mes_1             as numeric )
                       , cast( retorno.mes_2             as numeric )
                       , cast( retorno.mes_3             as numeric )
                       , cast( retorno.mes_4             as numeric )
                       , cast( retorno.mes_5             as numeric )
                       , cast( retorno.mes_6             as numeric )
                       , cast( retorno.mes_7             as numeric )
                       , cast( retorno.mes_8             as numeric )
                       , cast( retorno.mes_9             as numeric )
                       , cast( retorno.mes_10            as numeric )
                       , cast( retorno.mes_11            as numeric )
                       , cast( retorno.mes_12            as numeric )
                       , cast(0 as numeric) as total_mes_1
                       , cast(0 as numeric) as total_mes_2
                       , cast(0 as numeric) as total_mes_3
                       , cast(0 as numeric) as total_mes_4
                       , cast(0 as numeric) as total_mes_5
                       , cast(0 as numeric) as total_mes_6
                       , cast(0 as numeric) as total_mes_7
                       , cast(0 as numeric) as total_mes_8
                       , cast(0 as numeric) as total_mes_9
                       , cast(0 as numeric) as total_mes_10
                       , cast(0 as numeric) as total_mes_11
                       , cast(0 as numeric) as total_mes_12
                    FROM stn.sub_consulta_rcl'||stPl||'('|| quote_literal(dtData) ||' ,''4.1.7.2.1.01.02'', 7)
                           as retorno ( cod_conta      varchar
                                       ,nom_conta      varchar
                                       ,cod_estrutural varchar
                                       ,mes_1          numeric
                                       ,mes_2          numeric
                                       ,mes_3          numeric
                                       ,mes_4          numeric
                                       ,mes_5          numeric
                                       ,mes_6          numeric
                                       ,mes_7          numeric
                                       ,mes_8          numeric
                                       ,mes_9          numeric
                                       ,mes_10         numeric
                                       ,mes_11         numeric
                                       ,mes_12         numeric)
                  
                   UNION
                    -- sub contas de Transferencias correntes
                    -- Cota-Parte FPM, Cota-Parte ICMS, Cota-Parte IPVA, Transferencias do FUNDEF,
                    -- Outras Transferencias Correntes
                  
                  select 15 as ordem
                       , 3 AS nivel
                       , cast( retorno.cod_conta         as varchar )
                       , cast( ''Cota-Parte do ICMS''    as varchar )
                       , cast( retorno.cod_estrutural    as varchar )
                       , cast( retorno.mes_1             as numeric )
                       , cast( retorno.mes_2             as numeric )
                       , cast( retorno.mes_3             as numeric )
                       , cast( retorno.mes_4             as numeric )
                       , cast( retorno.mes_5             as numeric )
                       , cast( retorno.mes_6             as numeric )
                       , cast( retorno.mes_7             as numeric )
                       , cast( retorno.mes_8             as numeric )
                       , cast( retorno.mes_9             as numeric )
                       , cast( retorno.mes_10            as numeric )
                       , cast( retorno.mes_11            as numeric )
                       , cast( retorno.mes_12            as numeric )
                       , cast(0 as numeric) as total_mes_1
                       , cast(0 as numeric) as total_mes_2
                       , cast(0 as numeric) as total_mes_3
                       , cast(0 as numeric) as total_mes_4
                       , cast(0 as numeric) as total_mes_5
                       , cast(0 as numeric) as total_mes_6
                       , cast(0 as numeric) as total_mes_7
                       , cast(0 as numeric) as total_mes_8
                       , cast(0 as numeric) as total_mes_9
                       , cast(0 as numeric) as total_mes_10
                       , cast(0 as numeric) as total_mes_11
                       , cast(0 as numeric) as total_mes_12
                    FROM stn.sub_consulta_rcl'||stPl||'('|| quote_literal(dtData) ||' ,''4.1.7.2.2.01.01'', 7)
                           as retorno ( cod_conta      varchar
                                       ,nom_conta      varchar
                                       ,cod_estrutural varchar
                                       ,mes_1          numeric
                                       ,mes_2          numeric
                                       ,mes_3          numeric
                                       ,mes_4          numeric
                                       ,mes_5          numeric
                                       ,mes_6          numeric
                                       ,mes_7          numeric
                                       ,mes_8          numeric
                                       ,mes_9          numeric
                                       ,mes_10         numeric
                                       ,mes_11         numeric
                                       ,mes_12         numeric)
                     where cod_estrutural not in (''4.1.7.2.2.01.04.00.00.00'',
                                                  ''4.1.7.2.2.01.13.00.00.00'',
                                                  ''4.1.7.2.2.01.99.00.00.00'')
                                                  
                   UNION
                 
                  SELECT 16 as ordem
                       , 3 AS nivel
                       , cast( retorno.cod_conta         as varchar  )
                       , cast( ''Cota-Parte do IPVA'' as varchar )
                       , cast( retorno.cod_estrutural    as varchar )
                       , cast( retorno.mes_1  as numeric )
                       , cast( retorno.mes_2  as numeric )
                       , cast( retorno.mes_3  as numeric )
                       , cast( retorno.mes_4  as numeric )
                       , cast( retorno.mes_5  as numeric )
                       , cast( retorno.mes_6  as numeric )
                       , cast( retorno.mes_7  as numeric )
                       , cast( retorno.mes_8  as numeric )
                       , cast( retorno.mes_9  as numeric )
                       , cast( retorno.mes_10 as numeric )
                       , cast( retorno.mes_11 as numeric )
                       , cast( retorno.mes_12 as numeric )
                       , cast(0 as numeric) as total_mes_1
                       , cast(0 as numeric) as total_mes_2
                       , cast(0 as numeric) as total_mes_3
                       , cast(0 as numeric) as total_mes_4
                       , cast(0 as numeric) as total_mes_5
                       , cast(0 as numeric) as total_mes_6
                       , cast(0 as numeric) as total_mes_7
                       , cast(0 as numeric) as total_mes_8
                       , cast(0 as numeric) as total_mes_9
                       , cast(0 as numeric) as total_mes_10
                       , cast(0 as numeric) as total_mes_11
                       , cast(0 as numeric) as total_mes_12
                    FROM stn.sub_consulta_rcl'||stPl||'('|| quote_literal(dtData) ||' ,''4.1.7.2.2.01.02'', 7)
                           as retorno ( cod_conta      varchar
                                       ,nom_conta      varchar
                                       ,cod_estrutural varchar
                                       ,mes_1          numeric
                                       ,mes_2          numeric
                                       ,mes_3          numeric
                                       ,mes_4          numeric
                                       ,mes_5          numeric
                                       ,mes_6          numeric
                                       ,mes_7          numeric
                                       ,mes_8          numeric
                                       ,mes_9          numeric
                                       ,mes_10         numeric
                                       ,mes_11         numeric
                                       ,mes_12         numeric)
                     where cod_estrutural not in (''4.1.7.2.2.01.04.00.00.00'',
                                                  ''4.1.7.2.2.01.13.00.00.00'',
                                                  ''4.1.7.2.2.01.99.00.00.00'')

                                    
                  
                   UNION
                  
                  --Cota-Parte ITR
                  SELECT 17 as ordem
                        , 3 AS nivel
                        , cast(cod_conta as varchar)
                        , cast(''Cota-Parte do ITR'' as varchar) as nom_conta
                        , cast(retorno.cod_estrutural as varchar) AS cod_estrutural 
                        , cast( retorno.mes_1  as numeric )
                        , cast( retorno.mes_2  as numeric )
                        , cast( retorno.mes_3  as numeric )
                        , cast( retorno.mes_4  as numeric )
                        , cast( retorno.mes_5  as numeric )
                        , cast( retorno.mes_6  as numeric )
                        , cast( retorno.mes_7  as numeric )
                        , cast( retorno.mes_8  as numeric )
                        , cast( retorno.mes_9  as numeric )
                        , cast( retorno.mes_10 as numeric )
                        , cast( retorno.mes_11 as numeric )
                        , cast( retorno.mes_12 as numeric )
                        , cast(0 as numeric) as total_mes_1
                        , cast(0 as numeric) as total_mes_2
                        , cast(0 as numeric) as total_mes_3
                        , cast(0 as numeric) as total_mes_4
                        , cast(0 as numeric) as total_mes_5
                        , cast(0 as numeric) as total_mes_6
                        , cast(0 as numeric) as total_mes_7
                        , cast(0 as numeric) as total_mes_8
                        , cast(0 as numeric) as total_mes_9
                        , cast(0 as numeric) as total_mes_10
                        , cast(0 as numeric) as total_mes_11
                        , cast(0 as numeric) as total_mes_12
                    FROM stn.sub_consulta_rcl'||stPl||'('|| quote_literal(dtData) ||' ,''4.1.7.2.1.01.05'', 7)
                           as retorno ( cod_conta      varchar
                                       ,nom_conta      varchar
                                       ,cod_estrutural varchar
                                       ,mes_1          numeric
                                       ,mes_2          numeric
                                       ,mes_3          numeric
                                       ,mes_4          numeric
                                       ,mes_5          numeric
                                       ,mes_6          numeric
                                       ,mes_7          numeric
                                       ,mes_8          numeric
                                       ,mes_9          numeric
                                       ,mes_10         numeric
                                       ,mes_11         numeric
                                       ,mes_12         numeric)
                                       
                   UNION                 
                   
                   -- "TRANSFERENCIA FINANCEIRA DO ICMS - DESONERACAO - L.C. N° 87/96"
                  SELECT 18 as ordem
                       , 3 AS nivel
                       , cast(cod_conta as varchar)
                       , cast(''Transferências da LC 87/1996'' as varchar) as nom_conta
                       , cast(retorno.cod_estrutural as varchar) AS cod_estrutural 
                       , cast( retorno.mes_1  as numeric )
                       , cast( retorno.mes_2  as numeric )
                       , cast( retorno.mes_3  as numeric )
                       , cast( retorno.mes_4  as numeric )
                       , cast( retorno.mes_5  as numeric )
                       , cast( retorno.mes_6  as numeric )
                       , cast( retorno.mes_7  as numeric )
                       , cast( retorno.mes_8  as numeric )
                       , cast( retorno.mes_9  as numeric )
                       , cast( retorno.mes_10 as numeric )
                       , cast( retorno.mes_11 as numeric )
                       , cast( retorno.mes_12 as numeric )
                       , cast(0 as numeric) as total_mes_1
                       , cast(0 as numeric) as total_mes_2
                       , cast(0 as numeric) as total_mes_3
                       , cast(0 as numeric) as total_mes_4
                       , cast(0 as numeric) as total_mes_5
                       , cast(0 as numeric) as total_mes_6
                       , cast(0 as numeric) as total_mes_7
                       , cast(0 as numeric) as total_mes_8
                       , cast(0 as numeric) as total_mes_9
                       , cast(0 as numeric) as total_mes_10
                       , cast(0 as numeric) as total_mes_11
                       , cast(0 as numeric) as total_mes_12
                    FROM stn.sub_consulta_rcl'||stPl||'('|| quote_literal(dtData) ||' ,''4.1.7.2.1.36'', 6)
                           as retorno ( cod_conta      varchar
                                       ,nom_conta      varchar
                                       ,cod_estrutural varchar
                                       ,mes_1          numeric
                                       ,mes_2          numeric
                                       ,mes_3          numeric
                                       ,mes_4          numeric
                                       ,mes_5          numeric
                                       ,mes_6          numeric
                                       ,mes_7          numeric
                                       ,mes_8          numeric
                                       ,mes_9          numeric
                                       ,mes_10         numeric
                                       ,mes_11         numeric
                                       ,mes_12         numeric)
                   
                   UNION                 
                   
                   -- "TRANSFERENCIA FINANCEIRA DO IPI - DESONERACAO - L.C. N° 61/1989"
                  SELECT 19 as ordem
                       , 3 AS nivel
                       , cast(cod_conta as varchar)
                       , cast(''Transferências da LC 61/1989'' as varchar) as nom_conta
                       , cast(retorno.cod_estrutural as varchar) AS cod_estrutural 
                       , cast( retorno.mes_1  as numeric )
                       , cast( retorno.mes_2  as numeric )
                       , cast( retorno.mes_3  as numeric )
                       , cast( retorno.mes_4  as numeric )
                       , cast( retorno.mes_5  as numeric )
                       , cast( retorno.mes_6  as numeric )
                       , cast( retorno.mes_7  as numeric )
                       , cast( retorno.mes_8  as numeric )
                       , cast( retorno.mes_9  as numeric )
                       , cast( retorno.mes_10 as numeric )
                       , cast( retorno.mes_11 as numeric )
                       , cast( retorno.mes_12 as numeric )
                       , cast(0 as numeric) as total_mes_1
                       , cast(0 as numeric) as total_mes_2
                       , cast(0 as numeric) as total_mes_3
                       , cast(0 as numeric) as total_mes_4
                       , cast(0 as numeric) as total_mes_5
                       , cast(0 as numeric) as total_mes_6
                       , cast(0 as numeric) as total_mes_7
                       , cast(0 as numeric) as total_mes_8
                       , cast(0 as numeric) as total_mes_9
                       , cast(0 as numeric) as total_mes_10
                       , cast(0 as numeric) as total_mes_11
                       , cast(0 as numeric) as total_mes_12
                      from stn.sub_consulta_rcl'||stPl||'('|| quote_literal(dtData) ||' ,''4.1.7.2.2.01.04'', 7)
                           as retorno ( cod_conta      varchar
                                       ,nom_conta      varchar
                                       ,cod_estrutural varchar
                                       ,mes_1          numeric
                                       ,mes_2          numeric
                                       ,mes_3          numeric
                                       ,mes_4          numeric
                                       ,mes_5          numeric
                                       ,mes_6          numeric
                                       ,mes_7          numeric
                                       ,mes_8          numeric
                                       ,mes_9          numeric
                                       ,mes_10         numeric
                                       ,mes_11         numeric
                                       ,mes_12         numeric)
                  
                   UNION                 
                   
                   -- "TRANSFERENCIA FUNDEB"
                  SELECT 20 as ordem
                       , 3 AS nivel
                       , cast(cod_conta as varchar)
                       , cast(''Transferências do FUNDEB'' as varchar) as nom_conta
                       , cast(retorno.cod_estrutural as varchar) AS cod_estrutural 
                       , cast( retorno.mes_1  as numeric )
                       , cast( retorno.mes_2  as numeric )
                       , cast( retorno.mes_3  as numeric )
                       , cast( retorno.mes_4  as numeric )
                       , cast( retorno.mes_5  as numeric )
                       , cast( retorno.mes_6  as numeric )
                       , cast( retorno.mes_7  as numeric )
                       , cast( retorno.mes_8  as numeric )
                       , cast( retorno.mes_9  as numeric )
                       , cast( retorno.mes_10 as numeric )
                       , cast( retorno.mes_11 as numeric )
                       , cast( retorno.mes_12 as numeric )
                       , cast(0 as numeric) as total_mes_1
                       , cast(0 as numeric) as total_mes_2
                       , cast(0 as numeric) as total_mes_3
                       , cast(0 as numeric) as total_mes_4
                       , cast(0 as numeric) as total_mes_5
                       , cast(0 as numeric) as total_mes_6
                       , cast(0 as numeric) as total_mes_7
                       , cast(0 as numeric) as total_mes_8
                       , cast(0 as numeric) as total_mes_9
                       , cast(0 as numeric) as total_mes_10
                       , cast(0 as numeric) as total_mes_11
                       , cast(0 as numeric) as total_mes_12
                      from stn.sub_consulta_rcl'||stPl||'('|| quote_literal(dtData) ||' ,''4.1.7.2.4.01'', 6)
                           as retorno ( cod_conta      varchar
                                       ,nom_conta      varchar
                                       ,cod_estrutural varchar
                                       ,mes_1          numeric
                                       ,mes_2          numeric
                                       ,mes_3          numeric
                                       ,mes_4          numeric
                                       ,mes_5          numeric
                                       ,mes_6          numeric
                                       ,mes_7          numeric
                                       ,mes_8          numeric
                                       ,mes_9          numeric
                                       ,mes_10         numeric
                                       ,mes_11         numeric
                                       ,mes_12         numeric) 
                                    
                   UNION 
                    
                  SELECT 21 as ordem
                       , 3 AS nivel
                       , cast(0  as varchar) as cod_conta
                       , cast(''Outras Transferências Correntes'' as varchar) as nom_conta
                       , cast(''4.1.7.2.4.01.99.00.00.00''  as varchar) as cod_estrutural
                       , cast(0 as numeric) as mes_1
                       , cast(0 as numeric) as mes_2
                       , cast(0 as numeric) as mes_3
                       , cast(0 as numeric) as mes_4
                       , cast(0 as numeric) as mes_5
                       , cast(0 as numeric) as mes_6
                       , cast(0 as numeric) as mes_7
                       , cast(0 as numeric) as mes_8
                       , cast(0 as numeric) as mes_9
                       , cast(0 as numeric) as mes_10
                       , cast(0 as numeric) as mes_11
                       , cast(0 as numeric) as mes_12
                       , cast(0 as numeric) as total_mes_1
                       , cast(0 as numeric) as total_mes_2
                       , cast(0 as numeric) as total_mes_3
                       , cast(0 as numeric) as total_mes_4
                       , cast(0 as numeric) as total_mes_5
                       , cast(0 as numeric) as total_mes_6
                       , cast(0 as numeric) as total_mes_7
                       , cast(0 as numeric) as total_mes_8
                       , cast(0 as numeric) as total_mes_9
                       , cast(0 as numeric) as total_mes_10
                       , cast(0 as numeric) as total_mes_11
                       , cast(0 as numeric) as total_mes_12
         
                   UNION
                
                   --- Nivel 4.1 contas :Outras Receitas Correntes

                  SELECT 22 as ordem
                       , 2 AS nivel
                       , cast(retorno.cod_conta as varchar )               
                       , cast(''Outras Receitas Correntes'' as varchar ) AS nom_conta
                       , cast(retorno.cod_estrutural as varchar) AS cod_estrutural        
                       , cast( retorno.mes_1  as numeric )
                       , cast( retorno.mes_2  as numeric )
                       , cast( retorno.mes_3  as numeric )
                       , cast( retorno.mes_4  as numeric )
                       , cast( retorno.mes_5  as numeric )
                       , cast( retorno.mes_6  as numeric )
                       , cast( retorno.mes_7  as numeric )
                       , cast( retorno.mes_8  as numeric )
                       , cast( retorno.mes_9  as numeric )
                       , cast( retorno.mes_10 as numeric )
                       , cast( retorno.mes_11 as numeric )
                       , cast( retorno.mes_12 as numeric )     
                       , cast(0 as numeric) as total_mes_1         
                       , cast(0 as numeric) as total_mes_2         
                       , cast(0 as numeric) as total_mes_3         
                       , cast(0 as numeric) as total_mes_4         
                       , cast(0 as numeric) as total_mes_5         
                       , cast(0 as numeric) as total_mes_6         
                       , cast(0 as numeric) as total_mes_7         
                       , cast(0 as numeric) as total_mes_8         
                       , cast(0 as numeric) as total_mes_9         
                       , cast(0 as numeric) as total_mes_10        
                       , cast(0 as numeric) as total_mes_11        
                       , cast(0 as numeric) as total_mes_12        
                    
                    FROM stn.sub_consulta_rcl'||stPl||'('|| quote_literal(dtData) ||' ,''4.1.9'', 3)
                          as retorno ( cod_conta       varchar
                                       ,nom_conta      varchar
                                       ,cod_estrutural varchar
                                       ,mes_1          numeric
                                       ,mes_2          numeric
                                       ,mes_3          numeric
                                       ,mes_4          numeric
                                       ,mes_5          numeric
                                       ,mes_6          numeric
                                       ,mes_7          numeric
                                       ,mes_8          numeric
                                       ,mes_9          numeric
                                       ,mes_10         numeric
                                       ,mes_11         numeric
                                       ,mes_12         numeric)
                           
                 
                  UNION
                
                  -- DEDUCOES
                 
                  SELECT 23 AS ordem
                       , 1 AS nivel
                       , cod_conta
                       , nom_conta
                       , cod_estrutural
                       , SUM(tabela_total.mes_1) AS mes_1
                       , SUM(tabela_total.mes_2) AS mes_2
                       , SUM(tabela_total.mes_3) AS mes_3
                       , SUM(tabela_total.mes_4) AS mes_4
                       , SUM(tabela_total.mes_5) AS mes_5
                       , SUM(tabela_total.mes_6) AS mes_6
                       , SUM(tabela_total.mes_7) AS mes_7
                       , SUM(tabela_total.mes_8) AS mes_8
                       , SUM(tabela_total.mes_9) AS mes_9
                       , SUM(tabela_total.mes_10) AS mes_10
                       , SUM(tabela_total.mes_11) AS mes_11
                       , SUM(tabela_total.mes_12) AS mes_12
                       , cast(0 as numeric) as total_mes_1
                       , cast(0 as numeric) as total_mes_2
                       , cast(0 as numeric) as total_mes_3
                       , cast(0 as numeric) as total_mes_4
                       , cast(0 as numeric) as total_mes_5
                       , cast(0 as numeric) as total_mes_6
                       , cast(0 as numeric) as total_mes_7
                       , cast(0 as numeric) as total_mes_8
                       , cast(0 as numeric) as total_mes_9
                       , cast(0 as numeric) as total_mes_10
                       , cast(0 as numeric) as total_mes_11
                       , cast(0 as numeric) as total_mes_12
                    FROM (
                          SELECT 23 AS ordem
                               , cast( 0 as varchar ) AS cod_conta
                               , cast(''DEDUÇÕES (II)'' as varchar) as nom_conta
                               , cast(''4.9.0.0.0.00.00.00.00.00''  as varchar) as cod_estrutural
                               , cast( retorno.mes_1  as numeric )
                               , cast( retorno.mes_2  as numeric )
                               , cast( retorno.mes_3  as numeric )
                               , cast( retorno.mes_4  as numeric )
                               , cast( retorno.mes_5  as numeric )
                               , cast( retorno.mes_6  as numeric )
                               , cast( retorno.mes_7  as numeric )
                               , cast( retorno.mes_8  as numeric )
                               , cast( retorno.mes_9  as numeric )
                               , cast( retorno.mes_10 as numeric )
                               , cast( retorno.mes_11 as numeric )
                               , cast( retorno.mes_12 as numeric )
                               , cast(0 as numeric) as total_mes_1
                               , cast(0 as numeric) as total_mes_2
                               , cast(0 as numeric) as total_mes_3
                               , cast(0 as numeric) as total_mes_4
                               , cast(0 as numeric) as total_mes_5
                               , cast(0 as numeric) as total_mes_6
                               , cast(0 as numeric) as total_mes_7
                               , cast(0 as numeric) as total_mes_8
                               , cast(0 as numeric) as total_mes_9
                               , cast(0 as numeric) as total_mes_10
                               , cast(0 as numeric) as total_mes_11
                               , cast(0 as numeric) as total_mes_12
                            
                            FROM stn.sub_consulta_rcl_dedutora ('|| quote_literal(dtData) ||' ,''4.9'', 2)
                                as retorno ( cod_conta       varchar
                                             ,nom_conta      varchar
                                             ,cod_estrutural varchar
                                             ,mes_1          numeric
                                             ,mes_2          numeric
                                             ,mes_3          numeric
                                             ,mes_4          numeric
                                             ,mes_5          numeric
                                             ,mes_6          numeric
                                             ,mes_7          numeric
                                             ,mes_8          numeric
                                             ,mes_9          numeric
                                             ,mes_10         numeric
                                             ,mes_11         numeric
                                             ,mes_12         numeric
                                         )

                           UNION 
        
                          SELECT 23 AS ordem
                               , cast( 0 as varchar ) AS cod_conta
                               , cast(''DEDUÇÕES (II)'' as varchar) as nom_conta
                               , cast(''4.9.0.0.0.00.00.00.00.00''  as varchar) as cod_estrutural
                               , cast( retorno.mes_1  as numeric ) * -1  
                               , cast( retorno.mes_2  as numeric ) * -1 
                               , cast( retorno.mes_3  as numeric ) * -1 
                               , cast( retorno.mes_4  as numeric ) * -1 
                               , cast( retorno.mes_5  as numeric ) * -1 
                               , cast( retorno.mes_6  as numeric ) * -1 
                               , cast( retorno.mes_7  as numeric ) * -1 
                               , cast( retorno.mes_8  as numeric ) * -1 
                               , cast( retorno.mes_9  as numeric ) * -1 
                               , cast( retorno.mes_10 as numeric ) * -1 
                               , cast( retorno.mes_11 as numeric ) * -1 
                               , cast( retorno.mes_12 as numeric ) * -1 
                               , cast(0 as numeric) as total_mes_1
                               , cast(0 as numeric) as total_mes_2
                               , cast(0 as numeric) as total_mes_3
                               , cast(0 as numeric) as total_mes_4
                               , cast(0 as numeric) as total_mes_5
                               , cast(0 as numeric) as total_mes_6
                               , cast(0 as numeric) as total_mes_7
                               , cast(0 as numeric) as total_mes_8
                               , cast(0 as numeric) as total_mes_9
                               , cast(0 as numeric) as total_mes_10
                               , cast(0 as numeric) as total_mes_11
                               , cast(0 as numeric) as total_mes_12
                    
                            FROM stn.sub_consulta_rcl'||stPl||'('|| quote_literal(dtData) ||' ,''4.1.2.1.0.29'', 6)
                                    as retorno ( cod_conta       varchar
                                                ,nom_conta      varchar
                                                ,cod_estrutural varchar
                                                ,mes_1          numeric
                                                ,mes_2          numeric
                                                ,mes_3          numeric
                                                ,mes_4          numeric
                                                ,mes_5          numeric
                                                ,mes_6          numeric
                                                ,mes_7          numeric
                                                ,mes_8          numeric
                                                ,mes_9          numeric
                                                ,mes_10         numeric
                                                ,mes_11         numeric
                                                ,mes_12         numeric)
        ';
        
        --
        -- busca a configuracao do sistema para verificar se inclui ou nao  ded. do IRRF
        --
        SELECT valor
          INTO stDeduzirIRRF
          FROM administracao.configuracao
         WHERE exercicio  = quote_literal(inExercicio)
           AND cod_modulo = 36
           AND parametro = 'deduzir_irrf_anexo_3';
             
        IF stDeduzirIRRF = 'true' THEN
            stSql := stSql || '             
                    UNION
                   
                   SELECT 23 AS ordem
                        , cast( 0 as varchar ) AS cod_conta
                        , cast(''DEDUÇÕES (II)'' as varchar) as nom_conta
                        , cast(''4.9.0.0.0.00.00.00.00.00''  as varchar) as cod_estrutural
                        , cast( retorno.mes_1  as numeric ) * -1  
                        , cast( retorno.mes_2  as numeric ) * -1 
                        , cast( retorno.mes_3  as numeric ) * -1 
                        , cast( retorno.mes_4  as numeric ) * -1 
                        , cast( retorno.mes_5  as numeric ) * -1 
                        , cast( retorno.mes_6  as numeric ) * -1 
                        , cast( retorno.mes_7  as numeric ) * -1 
                        , cast( retorno.mes_8  as numeric ) * -1 
                        , cast( retorno.mes_9  as numeric ) * -1 
                        , cast( retorno.mes_10 as numeric ) * -1 
                        , cast( retorno.mes_11 as numeric ) * -1 
                        , cast( retorno.mes_12 as numeric ) * -1 
                        , cast(0 as numeric) as total_mes_1
                        , cast(0 as numeric) as total_mes_2
                        , cast(0 as numeric) as total_mes_3
                        , cast(0 as numeric) as total_mes_4
                        , cast(0 as numeric) as total_mes_5
                        , cast(0 as numeric) as total_mes_6
                        , cast(0 as numeric) as total_mes_7
                        , cast(0 as numeric) as total_mes_8
                        , cast(0 as numeric) as total_mes_9
                        , cast(0 as numeric) as total_mes_10
                        , cast(0 as numeric) as total_mes_11
                        , cast(0 as numeric) as total_mes_12
                     FROM stn.sub_consulta_rcl'||stPl||'('|| quote_literal(dtData) ||' ,''4.1.1.1.2.04'', 8)
                          as retorno ( cod_conta       varchar
                                       ,nom_conta      varchar
                                       ,cod_estrutural varchar
                                       ,mes_1          numeric
                                       ,mes_2          numeric
                                       ,mes_3          numeric
                                       ,mes_4          numeric
                                       ,mes_5          numeric
                                       ,mes_6          numeric
                                       ,mes_7          numeric
                                       ,mes_8          numeric
                                       ,mes_9          numeric
                                       ,mes_10         numeric
                                       ,mes_11         numeric
                                       ,mes_12         numeric)
            ';
            
            END IF;
       
        --
        -- recupera as receitas q devem ir na compensacao 
        --
        stSqlAux := '
                SELECT publico.fn_mascarareduzida(conta_receita.cod_estrutural) AS cod_reduzido
                     , publico.fn_nivel(conta_receita.cod_estrutural) AS nivel
                     , conta_receita.exercicio
                  FROM stn.vinculo_stn_receita
            INNER JOIN orcamento.receita
                    ON vinculo_stn_receita.cod_receita = receita.cod_receita
                   AND vinculo_stn_receita.exercicio   = receita.exercicio
            INNER JOIN orcamento.conta_receita
                    ON receita.cod_conta = conta_receita.cod_conta
                   AND receita.exercicio = conta_receita.exercicio
              GROUP BY conta_receita.cod_estrutural
                     , conta_receita.exercicio
        ';

        FOR reRegistro IN EXECUTE stSqlAux LOOP
            stSql := stSql || '
                UNION
                SELECT 23 AS ordem
                     , cast( 0 as varchar ) AS cod_conta
                     , cast(''DEDUÇÕES (II)'' as varchar) as nom_conta
                     , cast(''4.9.0.0.0.00.00.00.00.00''  as varchar) as cod_estrutural
                     , cast( retorno.mes_1  as numeric ) * -1   
                     , cast( retorno.mes_2  as numeric ) * -1 
                     , cast( retorno.mes_3  as numeric ) * -1 
                     , cast( retorno.mes_4  as numeric ) * -1 
                     , cast( retorno.mes_5  as numeric ) * -1 
                     , cast( retorno.mes_6  as numeric ) * -1 
                     , cast( retorno.mes_7  as numeric ) * -1 
                     , cast( retorno.mes_8  as numeric ) * -1 
                     , cast( retorno.mes_9  as numeric ) * -1 
                     , cast( retorno.mes_10 as numeric ) * -1 
                     , cast( retorno.mes_11 as numeric ) * -1 
                     , cast( retorno.mes_12 as numeric ) * -1 
                     , cast(0 as numeric) as total_mes_1
                     , cast(0 as numeric) as total_mes_2
                     , cast(0 as numeric) as total_mes_3
                     , cast(0 as numeric) as total_mes_4
                     , cast(0 as numeric) as total_mes_5
                     , cast(0 as numeric) as total_mes_6
                     , cast(0 as numeric) as total_mes_7
                     , cast(0 as numeric) as total_mes_8
                     , cast(0 as numeric) as total_mes_9
                     , cast(0 as numeric) as total_mes_10
                     , cast(0 as numeric) as total_mes_11
                     , cast(0 as numeric) as total_mes_12

                  FROM stn.sub_consulta_rcl'||stPl||'('|| quote_literal(dtData) ||' ,''4.' || reRegistro.cod_reduzido || ''', ' || (reRegistro.nivel + 1) || ')
                          as retorno ( cod_conta       varchar
                                       ,nom_conta      varchar
                                       ,cod_estrutural varchar
                                       ,mes_1          numeric
                                       ,mes_2          numeric
                                       ,mes_3          numeric
                                       ,mes_4          numeric
                                       ,mes_5          numeric
                                       ,mes_6          numeric
                                       ,mes_7          numeric
                                       ,mes_8          numeric
                                       ,mes_9          numeric
                                       ,mes_10         numeric
                                       ,mes_11         numeric
                                       ,mes_12         numeric)
    
            ';
        END LOOP;
 
        stSql := stSql || '                                       

                ) AS tabela_total
        GROUP BY ordem
               , cod_conta
               , nom_conta
               , cod_estrutural

        --------Contrib. do Servidor para o Plano de Previdência            
           
               UNION
              SELECT 24 as ordem
                   , 2 AS nivel
                   , cast( retorno.cod_conta         as varchar ) 
                   , cast(''Contrib. do Servidor para o Plano de Previdência'' as varchar) as nom_conta     
                   , cast(''4.9.5.0.0.00.00.00.00.00''  as varchar) as cod_estrutural
                   , cast( retorno.mes_1             as numeric ) * -1         
                   , cast( retorno.mes_2             as numeric ) * -1         
                   , cast( retorno.mes_3             as numeric ) * -1         
                   , cast( retorno.mes_4             as numeric ) * -1         
                   , cast( retorno.mes_5             as numeric ) * -1         
                   , cast( retorno.mes_6             as numeric ) * -1         
                   , cast( retorno.mes_7             as numeric ) * -1         
                   , cast( retorno.mes_8             as numeric ) * -1         
                   , cast( retorno.mes_9             as numeric ) * -1         
                   , cast( retorno.mes_10            as numeric ) * -1         
                   , cast( retorno.mes_11            as numeric ) * -1         
                   , cast( retorno.mes_12            as numeric ) * -1   
                   , cast(0 as numeric) as total_mes_1         
                   , cast(0 as numeric) as total_mes_2         
                   , cast(0 as numeric) as total_mes_3         
                   , cast(0 as numeric) as total_mes_4         
                   , cast(0 as numeric) as total_mes_5         
                   , cast(0 as numeric) as total_mes_6         
                   , cast(0 as numeric) as total_mes_7         
                   , cast(0 as numeric) as total_mes_8         
                   , cast(0 as numeric) as total_mes_9         
                   , cast(0 as numeric) as total_mes_10        
                   , cast(0 as numeric) as total_mes_11        
                   , cast(0 as numeric) as total_mes_12        
 
                FROM stn.sub_consulta_rcl'||stPl||'('|| quote_literal(dtData) ||' ,''4.1.2.1.0.29'', 6)
                          as retorno ( cod_conta       varchar
                                       ,nom_conta      varchar
                                       ,cod_estrutural varchar
                                       ,mes_1          numeric
                                       ,mes_2          numeric
                                       ,mes_3          numeric
                                       ,mes_4          numeric
                                       ,mes_5          numeric
                                       ,mes_6          numeric
                                       ,mes_7          numeric
                                       ,mes_8          numeric
                                       ,mes_9          numeric
                                       ,mes_10         numeric
                                       ,mes_11         numeric
                                       ,mes_12         numeric)  

                UNION
                   
                --Compensação Financ. entre Regimes Previdência
                  SELECT 25 as ordem
                       , 2 AS nivel
                       , cast(retorno.cod_conta as varchar )
                       , cast(''Compensação Financ. entre Regimes Previdência'' as varchar ) as nom_conta
                       , cast(''4.9.6.0.0.00.00.00.00.00''  as varchar) as cod_estrutural
                       , cast(retorno.mes_1  as numeric)                
                       , cast(retorno.mes_2  as numeric)                
                       , cast(retorno.mes_3  as numeric)                
                       , cast(retorno.mes_4  as numeric)                
                       , cast(retorno.mes_5  as numeric)                
                       , cast(retorno.mes_6  as numeric)                
                       , cast(retorno.mes_7  as numeric)                
                       , cast(retorno.mes_8  as numeric)                
                       , cast(retorno.mes_9  as numeric)                
                       , cast(retorno.mes_10 as numeric)                
                       , cast(retorno.mes_11 as numeric)                
                       , cast(retorno.mes_12 as numeric)         
                       , cast(0 as numeric) as total_mes_1         
                       , cast(0 as numeric) as total_mes_2         
                       , cast(0 as numeric) as total_mes_3         
                       , cast(0 as numeric) as total_mes_4         
                       , cast(0 as numeric) as total_mes_5         
                       , cast(0 as numeric) as total_mes_6         
                       , cast(0 as numeric) as total_mes_7         
                       , cast(0 as numeric) as total_mes_8         
                       , cast(0 as numeric) as total_mes_9         
                       , cast(0 as numeric) as total_mes_10        
                       , cast(0 as numeric) as total_mes_11        
                       , cast(0 as numeric) as total_mes_12        
 
                    FROM stn.sub_consulta_rcl'||stPl||'('|| quote_literal(dtData) ||' ,''4.1.2.1.0.99.00.10'', 8)
                          as retorno ( cod_conta       varchar
                                       ,nom_conta      varchar
                                       ,cod_estrutural varchar
                                       ,mes_1          numeric
                                       ,mes_2          numeric
                                       ,mes_3          numeric
                                       ,mes_4          numeric
                                       ,mes_5          numeric
                                       ,mes_6          numeric
                                       ,mes_7          numeric
                                       ,mes_8          numeric
                                       ,mes_9          numeric
                                       ,mes_10         numeric
                                       ,mes_11         numeric
                                       ,mes_12         numeric)  
    
            ';

        IF stDeduzirIRRF = 'true' THEN
            stSql := stSql || '
                    UNION
                    SELECT 6 as ordem
                         , 3 AS nivel
                         , cast( 0 as varchar )
                         , nom_conta
                         , cod_estrutural
                         , SUM(mes_1) 
                         , SUM(mes_2)  
                         , SUM(mes_3)  
                         , SUM(mes_4)  
                         , SUM(mes_5)  
                         , SUM(mes_6)  
                         , SUM(mes_7)  
                         , SUM(mes_8)  
                         , SUM(mes_9)  
                         , SUM(mes_10) 
                         , SUM(mes_11) 
                         , SUM(mes_12) 
                         , cast(0 as numeric) as total_mes_1
                         , cast(0 as numeric) as total_mes_2
                         , cast(0 as numeric) as total_mes_3
                         , cast(0 as numeric) as total_mes_4
                         , cast(0 as numeric) as total_mes_5
                         , cast(0 as numeric) as total_mes_6
                         , cast(0 as numeric) as total_mes_7
                         , cast(0 as numeric) as total_mes_8
                         , cast(0 as numeric) as total_mes_9
                         , cast(0 as numeric) as total_mes_10
                         , cast(0 as numeric) as total_mes_11
                         , cast(0 as numeric) as total_mes_12
 
                      FROM (
                        SELECT 6 as ordem  ,cast( retorno.cod_conta as varchar ) 
                                           ,cast( ''IRRF''  as varchar ) as nom_conta
                                           ,cast( ''4.9.6.5.0.00.00.00.00.00'' as varchar ) as cod_estrutural
                                           ,cast( retorno.mes_1  as numeric ) * -1 as mes_1      
                                           ,cast( retorno.mes_2  as numeric ) * -1 as mes_2      
                                           ,cast( retorno.mes_3  as numeric ) * -1 as mes_3      
                                           ,cast( retorno.mes_4  as numeric ) * -1 as mes_4      
                                           ,cast( retorno.mes_5  as numeric ) * -1 as mes_5      
                                           ,cast( retorno.mes_6  as numeric ) * -1 as mes_6      
                                           ,cast( retorno.mes_7  as numeric ) * -1 as mes_7      
                                           ,cast( retorno.mes_8  as numeric ) * -1 as mes_8      
                                           ,cast( retorno.mes_9  as numeric ) * -1 as mes_9      
                                           ,cast( retorno.mes_10 as numeric ) * -1 as mes_10      
                                           ,cast( retorno.mes_11 as numeric ) * -1 as mes_11      
                                           ,cast( retorno.mes_12 as numeric ) * -1 as mes_12  
                        from stn.sub_consulta_rcl'||stPl||'('|| quote_literal(dtData) ||' ,''4.1.1.1.2.04'', 8)
                              as retorno ( cod_conta       varchar
                                           ,nom_conta      varchar
                                           ,cod_estrutural varchar
                                           ,mes_1          numeric
                                           ,mes_2          numeric
                                           ,mes_3          numeric
                                           ,mes_4          numeric
                                           ,mes_5          numeric
                                           ,mes_6          numeric
                                           ,mes_7          numeric
                                           ,mes_8          numeric
                                           ,mes_9          numeric
                                           ,mes_10         numeric
                                           ,mes_11         numeric
                                           ,mes_12         numeric )                   
        
                        UNION                    
    
                        select 6 as ordem  ,cast( retorno.cod_conta as varchar ) 
                                           ,cast( ''IRRF''  as varchar ) as nom_conta
                                           ,cast( ''4.9.6.5.0.00.00.00.00.00'' as varchar ) as cod_estrutural
                                           ,cast( retorno.mes_1  as numeric ) * -1 as mes_1      
                                           ,cast( retorno.mes_2  as numeric ) * -1 as mes_2      
                                           ,cast( retorno.mes_3  as numeric ) * -1 as mes_3      
                                           ,cast( retorno.mes_4  as numeric ) * -1 as mes_4      
                                           ,cast( retorno.mes_5  as numeric ) * -1 as mes_5      
                                           ,cast( retorno.mes_6  as numeric ) * -1 as mes_6      
                                           ,cast( retorno.mes_7  as numeric ) * -1 as mes_7      
                                           ,cast( retorno.mes_8  as numeric ) * -1 as mes_8      
                                           ,cast( retorno.mes_9  as numeric ) * -1 as mes_9      
                                           ,cast( retorno.mes_10 as numeric ) * -1 as mes_10      
                                           ,cast( retorno.mes_11 as numeric ) * -1 as mes_11      
                                           ,cast( retorno.mes_12 as numeric ) * -1 as mes_12  
                        from stn.sub_consulta_rcl'||stPl||'('|| quote_literal(dtData) ||' ,''4.1.1.1.2.04.31.02'', 8)
                              as retorno ( cod_conta       varchar
                                           ,nom_conta      varchar
                                           ,cod_estrutural varchar
                                           ,mes_1          numeric
                                           ,mes_2          numeric
                                           ,mes_3          numeric
                                           ,mes_4          numeric
                                           ,mes_5          numeric
                                           ,mes_6          numeric
                                           ,mes_7          numeric
                                           ,mes_8          numeric
                                           ,mes_9          numeric
                                           ,mes_10         numeric
                                           ,mes_11         numeric
                                           ,mes_12         numeric )                   
                    ) as table_irrf

            ';    
        END IF;
        
        stSql := stSql || '
                    --Dedução da Receita para Formação do FUNDEB
                   UNION
                   SELECT ordem
                        , 2 AS nivel
                        , cast(1 as varchar) as cod_conta
                        , nom_conta
                        , cast(cod_estrutural as varchar) AS cod_estrutural 
                        , mes_1
                        , mes_2
                        , mes_3
                        , mes_4
                        , mes_5
                        , mes_6
                        , mes_7
                        , mes_8
                        , mes_9
                        , mes_10
                        , mes_11
                        , mes_12
                        , total_mes_1
                        , total_mes_2
                        , total_mes_3
                        , total_mes_4
                        , total_mes_5
                        , total_mes_6
                        , total_mes_7
                        , total_mes_8
                        , total_mes_9
                        , total_mes_10
                        , total_mes_11
                        , total_mes_12
                     FROM
                           ( select 26 as ordem
                                  , cast( retorno.cod_conta as varchar )
                                  , cast(''Dedução da Receita para Formação do FUNDEB'' as varchar) as nom_conta
                                  , cast( case when retorno.cod_estrutural like ''4.9.0.0.0.00.00.00.00.00''
                                               then ''4.9.7.0.0.00.00.00.00.00'' end as varchar ) as cod_estrutural
                                  , cast( retorno.mes_1  as numeric )
                                  , cast( retorno.mes_2  as numeric )
                                  , cast( retorno.mes_3  as numeric )
                                  , cast( retorno.mes_4  as numeric )
                                  , cast( retorno.mes_5  as numeric )
                                  , cast( retorno.mes_6  as numeric )
                                  , cast( retorno.mes_7  as numeric )
                                  , cast( retorno.mes_8  as numeric )
                                  , cast( retorno.mes_9  as numeric )
                                  , cast( retorno.mes_10 as numeric )
                                  , cast( retorno.mes_11 as numeric )
                                  , cast( retorno.mes_12 as numeric )
                                  , cast(0 as numeric) as total_mes_1
                                  , cast(0 as numeric) as total_mes_2
                                  , cast(0 as numeric) as total_mes_3
                                  , cast(0 as numeric) as total_mes_4
                                  , cast(0 as numeric) as total_mes_5
                                  , cast(0 as numeric) as total_mes_6
                                  , cast(0 as numeric) as total_mes_7
                                  , cast(0 as numeric) as total_mes_8
                                  , cast(0 as numeric) as total_mes_9
                                  , cast(0 as numeric) as total_mes_10
                                  , cast(0 as numeric) as total_mes_11
                                  , cast(0 as numeric) as total_mes_12
                               from stn.sub_consulta_rcl_dedutora ('|| quote_literal(dtData) ||' ,''4.9'', 2)
                                    as retorno ( cod_conta      varchar
                                                ,nom_conta      varchar
                                                ,cod_estrutural varchar
                                                ,mes_1          numeric
                                                ,mes_2          numeric
                                                ,mes_3          numeric
                                                ,mes_4          numeric
                                                ,mes_5          numeric
                                                ,mes_6          numeric
                                                ,mes_7          numeric
                                                ,mes_8          numeric
                                                ,mes_9          numeric
                                                ,mes_10         numeric
                                                ,mes_11         numeric
                                                ,mes_12         numeric)
                           ) as dedu
                 group by ordem
                        , nom_conta
                        , cod_estrutural
                        , mes_1
                        , mes_2
                        , mes_3
                        , mes_4
                        , mes_5
                        , mes_6
                        , mes_7
                        , mes_8
                        , mes_9
                        , mes_10
                        , mes_11
                        , mes_12
                        , total_mes_1
                        , total_mes_2
                        , total_mes_3
                        , total_mes_4
                        , total_mes_5
                        , total_mes_6
                        , total_mes_7
                        , total_mes_8
                        , total_mes_9
                        , total_mes_10
                        , total_mes_11
                        , total_mes_12

                 order by ordem
            ';

    flTotalMes[1]  := 0;
    flTotalMes[2]  := 0;
    flTotalMes[3]  := 0;
    flTotalMes[4]  := 0;
    flTotalMes[5]  := 0;
    flTotalMes[6]  := 0;
    flTotalMes[7]  := 0;
    flTotalMes[8]  := 0;
    flTotalMes[9]  := 0;
    flTotalMes[10] := 0;
    flTotalMes[11] := 0;
    flTotalMes[12] := 0;

    flTotalMes_OTC  [1]  := 0;
    flTotalMes_OTC  [2]  := 0;
    flTotalMes_OTC  [3]  := 0;
    flTotalMes_OTC  [4]  := 0;
    flTotalMes_OTC  [5]  := 0;
    flTotalMes_OTC  [6]  := 0;
    flTotalMes_OTC  [7]  := 0;
    flTotalMes_OTC  [8]  := 0;
    flTotalMes_OTC  [9]  := 0;
    flTotalMes_OTC  [10] := 0;
    flTotalMes_OTC  [11] := 0;
    flTotalMes_OTC  [12] := 0;

    FOR reRegistro IN EXECUTE stSql
    LOOP

        --AJUSTES DO DADOS PARA BUSCAR DE ACORDO COM A CONFIGURACAO A PARTIR DE 2016 SEM ALTERAR O QUE JA FAZ CASO NAO TENHA NADA CONFIGURADO
        arValoresConfigurados[1] := null;
        arValoresConfigurados[2] := null;
        arValoresConfigurados[3] := null;
        arValoresConfigurados[4] := null;
        arValoresConfigurados[5] := null;
        arValoresConfigurados[6] := null;
        arValoresConfigurados[7] := null;
        arValoresConfigurados[8] := null;
        arValoresConfigurados[9] := null;
        arValoresConfigurados[10] := null;
        arValoresConfigurados[11] := null;
        arValoresConfigurados[12] := null;

        i := 1;
        WHILE i <= 12 LOOP
            nuValorAux := 0.00;
            stDataIni := arDatas[i];

            IF (SELECT COUNT(*)
                FROM stn.receita_corrente_liquida
                WHERE mes = substr(stDataIni,4,2)::integer
                AND ano = ''||substr(stDataIni,7,4)::integer||''
            ) >= 1 THEN

                --'RECEITAS CORRENTES (I)'
                IF reRegistro.cod_estrutural = '1.0.0.0.00.00.00.00.00' THEN
                    SELECT ( SUM(COALESCE(valor_receita_tributaria,0.00))
                             --'Receita Tributaria'
                             +SUM(COALESCE(valor_iptu,0.00))
                             +SUM(COALESCE(valor_iss,0.00))
                             +SUM(COALESCE(valor_itbi,0.00))
                             +SUM(COALESCE(valor_irrf,0.00))
                             +SUM(COALESCE(valor_outras_receitas_tributarias,0.00))

                             +SUM(COALESCE(valor_receita_contribuicoes,0.00))
                             +SUM(COALESCE(valor_receita_patrimonial,0.00))
                             +SUM(COALESCE(valor_receita_agropecuaria,0.00))
                             +SUM(COALESCE(valor_receita_industrial,0.00))
                             +SUM(COALESCE(valor_receita_servicos,0.00))
                             +SUM(COALESCE(valor_transferencias_correntes,0.00))
                             --'Transferencias Correntes'
                             +SUM(COALESCE(valor_cota_parte_fpm,0.00))
                             +SUM(COALESCE(valor_cota_parte_icms,0.00))
                             +SUM(COALESCE(valor_cota_parte_ipva,0.00))
                             +SUM(COALESCE(valor_cota_parte_itr,0.00))
                             +SUM(COALESCE(valor_transferencias_lc_871996,0.00))
                             +SUM(COALESCE(valor_transferencias_lc_611989,0.00))
                             +SUM(COALESCE(valor_transferencias_fundeb,0.00))
                             +SUM(COALESCE(valor_outras_transferencias_correntes,0.00))

                             +SUM(COALESCE(valor_outras_receitas,0.00))
                           ) as valor
                        INTO nuValorAux
                    FROM stn.receita_corrente_liquida
                    WHERE mes = substr(stDataIni,4,2)::integer
                    AND ano = ''||substr(stDataIni,7,4)::integer||'';
                    arValoresConfigurados[i] := nuValorAux;
                END IF;

                --'Receita Tributaria'
                IF reRegistro.cod_estrutural = '1.1.0.0.00.00.00.00.00' THEN
                    SELECT ( SUM(COALESCE(valor_receita_tributaria,0.00))
                            +SUM(COALESCE(valor_iptu,0.00))
                            +SUM(COALESCE(valor_iss,0.00))
                            +SUM(COALESCE(valor_itbi,0.00))
                            +SUM(COALESCE(valor_irrf,0.00))
                            +SUM(COALESCE(valor_outras_receitas_tributarias,0.00))
                           ) as valor
                        INTO nuValorAux
                    FROM stn.receita_corrente_liquida
                    WHERE mes = substr(stDataIni,4,2)::integer
                    AND ano = ''||substr(stDataIni,7,4)::integer||'';
                    arValoresConfigurados[i] := nuValorAux;
                END IF;

                --'IPTU'
                IF reRegistro.cod_estrutural = '1.1.1.2.02.00.00.00.00' THEN
                    SELECT SUM(COALESCE(valor_iptu,0.00)) as valor
                      INTO nuValorAux
                      FROM stn.receita_corrente_liquida
                     WHERE mes = substr(stDataIni,4,2)::integer
                       AND ano = ''||substr(stDataIni,7,4)::integer||'';
                    arValoresConfigurados[i] := nuValorAux;
                END IF;

                --'ISS'
                IF reRegistro.cod_estrutural = '1.1.1.3.05.00.00.00.00' THEN
                    SELECT SUM(COALESCE(valor_iss,0.00)) as valor
                      INTO nuValorAux
                      FROM stn.receita_corrente_liquida
                     WHERE mes = substr(stDataIni,4,2)::integer
                       AND ano = ''||substr(stDataIni,7,4)::integer||'';
                    arValoresConfigurados[i] := nuValorAux;
                END IF;

                --'ITBI'
                IF reRegistro.cod_estrutural = '1.1.1.2.08.00.00.00.00' THEN
                    SELECT SUM(COALESCE(valor_itbi,0.00)) as valor
                      INTO nuValorAux
                      FROM stn.receita_corrente_liquida
                     WHERE mes = substr(stDataIni,4,2)::integer
                       AND ano = ''||substr(stDataIni,7,4)::integer||'';
                    arValoresConfigurados[i] := nuValorAux;
                END IF;

                --'IRRF'
                IF reRegistro.cod_estrutural = '1.1.1.2.04.00.00.00.00' THEN
                    SELECT SUM(COALESCE(valor_irrf,0.00)) as valor
                      INTO nuValorAux
                      FROM stn.receita_corrente_liquida
                     WHERE mes = substr(stDataIni,4,2)::integer
                       AND ano = ''||substr(stDataIni,7,4)::integer||'';
                    arValoresConfigurados[i] := nuValorAux;
                END IF;

                --'Outras Receitas Tributárias'
                IF reRegistro.cod_estrutural = '4.1.1.1.2.09.00.00.00.00' THEN
                    SELECT SUM(COALESCE(valor_outras_receitas_tributarias,0.00)) as valor
                      INTO nuValorAux
                      FROM stn.receita_corrente_liquida
                     WHERE mes = substr(stDataIni,4,2)::integer
                       AND ano = ''||substr(stDataIni,7,4)::integer||'';
                    arValoresConfigurados[i] := nuValorAux;
                END IF;

                --'Receita De Contribuicoes
                IF reRegistro.cod_estrutural = '1.2.0.0.00.00.00.00.00' THEN
                    SELECT SUM(COALESCE(valor_receita_contribuicoes,0.00)) as valor
                        INTO nuValorAux
                    FROM stn.receita_corrente_liquida
                    WHERE mes = substr(stDataIni,4,2)::integer
                    AND ano = ''||substr(stDataIni,7,4)::integer||'';
                    arValoresConfigurados[i] := nuValorAux;
                END IF;

                --'Receita Patrimonial'
                IF reRegistro.cod_estrutural = '1.3.0.0.00.00.00.00.00' THEN
                    SELECT SUM(COALESCE(valor_receita_patrimonial,0.00)) as valor
                        INTO nuValorAux
                    FROM stn.receita_corrente_liquida
                    WHERE mes = substr(stDataIni,4,2)::integer
                    AND ano = ''||substr(stDataIni,7,4)::integer||'';
                    arValoresConfigurados[i] := nuValorAux;
                END IF;

                --'Receita Agropecuaria'
                IF reRegistro.cod_estrutural = '1.4.0.0.00.00.00.00.00' THEN
                    SELECT SUM(COALESCE(valor_receita_agropecuaria,0.00)) as valor
                        INTO nuValorAux
                    FROM stn.receita_corrente_liquida
                    WHERE mes = substr(stDataIni,4,2)::integer
                    AND ano = ''||substr(stDataIni,7,4)::integer||'';
                    arValoresConfigurados[i] := nuValorAux;
                END IF;

                --'Receita Industrial'
                IF reRegistro.cod_estrutural = '1.5.0.0.00.00.00.00.00' THEN
                    SELECT SUM(COALESCE(valor_receita_industrial,0.00)) as valor
                        INTO nuValorAux
                    FROM stn.receita_corrente_liquida
                    WHERE mes = substr(stDataIni,4,2)::integer
                    AND ano = ''||substr(stDataIni,7,4)::integer||'';
                    arValoresConfigurados[i] := nuValorAux;
                END IF;

                --'Receita De Servicos'
                IF reRegistro.cod_estrutural = '1.6.0.0.00.00.00.00.00' THEN
                    SELECT SUM(COALESCE(valor_receita_servicos,0.00)) as valor
                        INTO nuValorAux
                    FROM stn.receita_corrente_liquida
                    WHERE mes = substr(stDataIni,4,2)::integer
                    AND ano = ''||substr(stDataIni,7,4)::integer||'';
                    arValoresConfigurados[i] := nuValorAux;
                END IF;

                --'Transferencias Correntes'
                IF reRegistro.cod_estrutural = '1.7.0.0.00.00.00.00.00' THEN
                    SELECT (SUM(COALESCE(valor_transferencias_correntes,0.00))
                            +SUM(COALESCE(valor_cota_parte_fpm,0.00))
                            +SUM(COALESCE(valor_cota_parte_icms,0.00))
                            +SUM(COALESCE(valor_cota_parte_ipva,0.00))
                            +SUM(COALESCE(valor_cota_parte_itr,0.00))
                            +SUM(COALESCE(valor_transferencias_lc_871996,0.00))
                            +SUM(COALESCE(valor_transferencias_lc_611989,0.00))
                            +SUM(COALESCE(valor_transferencias_fundeb,0.00))
                            +SUM(COALESCE(valor_outras_transferencias_correntes,0.00))
                           ) as valor
                        INTO nuValorAux
                    FROM stn.receita_corrente_liquida
                    WHERE mes = substr(stDataIni,4,2)::integer
                    AND ano = ''||substr(stDataIni,7,4)::integer||'';
                    arValoresConfigurados[i] := nuValorAux;
                END IF;

                --'Cota-Parte do FPM'
                IF reRegistro.cod_estrutural = '1.7.2.1.01.02.00.00.00' THEN
                    SELECT SUM(COALESCE(valor_cota_parte_fpm,0.00)) as valor
                      INTO nuValorAux
                      FROM stn.receita_corrente_liquida
                     WHERE mes = substr(stDataIni,4,2)::integer
                       AND ano = ''||substr(stDataIni,7,4)::integer||'';
                    arValoresConfigurados[i] := nuValorAux;
                END IF;

                --'Cota-Parte do ICMS'
                IF reRegistro.cod_estrutural = '1.7.2.2.01.01.00.00.00' THEN
                    SELECT SUM(COALESCE(valor_cota_parte_icms,0.00)) as valor
                      INTO nuValorAux
                      FROM stn.receita_corrente_liquida
                     WHERE mes = substr(stDataIni,4,2)::integer
                       AND ano = ''||substr(stDataIni,7,4)::integer||'';
                    arValoresConfigurados[i] := nuValorAux;
                END IF;

                --'Cota-Parte do IPVA'
                IF reRegistro.cod_estrutural = '1.7.2.2.01.02.00.00.00' THEN
                    SELECT SUM(COALESCE(valor_cota_parte_ipva,0.00)) as valor
                      INTO nuValorAux
                      FROM stn.receita_corrente_liquida
                     WHERE mes = substr(stDataIni,4,2)::integer
                       AND ano = ''||substr(stDataIni,7,4)::integer||'';
                    arValoresConfigurados[i] := nuValorAux;
                END IF;

                --'Cota-Parte do ITR'
                IF reRegistro.cod_estrutural = '1.7.2.1.01.05.00.00.00' THEN
                    SELECT SUM(COALESCE(valor_cota_parte_itr,0.00)) as valor
                      INTO nuValorAux
                      FROM stn.receita_corrente_liquida
                     WHERE mes = substr(stDataIni,4,2)::integer
                       AND ano = ''||substr(stDataIni,7,4)::integer||'';
                    arValoresConfigurados[i] := nuValorAux;
                END IF;

                --'Transferências da LC 87/1996'
                IF reRegistro.cod_estrutural = '1.7.2.1.36.00.00.00.00' THEN
                    SELECT SUM(COALESCE(valor_transferencias_lc_871996,0.00)) as valor
                      INTO nuValorAux
                      FROM stn.receita_corrente_liquida
                     WHERE mes = substr(stDataIni,4,2)::integer
                       AND ano = ''||substr(stDataIni,7,4)::integer||'';
                    arValoresConfigurados[i] := nuValorAux;
                END IF;

                --'Transferências da LC 61/1989'
                IF reRegistro.cod_estrutural = '1.7.2.2.01.04.00.00.00' THEN
                    SELECT SUM(COALESCE(valor_transferencias_lc_611989,0.00)) as valor
                      INTO nuValorAux
                      FROM stn.receita_corrente_liquida
                     WHERE mes = substr(stDataIni,4,2)::integer
                       AND ano = ''||substr(stDataIni,7,4)::integer||'';
                    arValoresConfigurados[i] := nuValorAux;
                END IF;

                --'Transferências do FUNDEB'
                IF reRegistro.cod_estrutural = '1.7.2.4.01.00.00.00.00' THEN
                    SELECT SUM(COALESCE(valor_transferencias_fundeb,0.00)) as valor
                      INTO nuValorAux
                      FROM stn.receita_corrente_liquida
                     WHERE mes = substr(stDataIni,4,2)::integer
                       AND ano = ''||substr(stDataIni,7,4)::integer||'';
                    arValoresConfigurados[i] := nuValorAux;
                END IF;

                --'Outras Transferências Correntes'
                IF reRegistro.cod_estrutural = '4.1.7.2.4.01.99.00.00.00' THEN
                    SELECT SUM(COALESCE(valor_outras_transferencias_correntes,0.00)) as valor
                      INTO nuValorAux
                      FROM stn.receita_corrente_liquida
                     WHERE mes = substr(stDataIni,4,2)::integer
                       AND ano = ''||substr(stDataIni,7,4)::integer||'';
                    arValoresConfigurados[i] := nuValorAux;
                END IF;

                --'Outras Receitas Correntes'
                IF reRegistro.cod_estrutural = '1.9.0.0.00.00.00.00.00' THEN
                    SELECT SUM(COALESCE(valor_outras_receitas,0.00)) as valor
                        INTO nuValorAux
                    FROM stn.receita_corrente_liquida
                    WHERE mes = substr(stDataIni,4,2)::integer
                    AND ano = ''||substr(stDataIni,7,4)::integer||'';
                    arValoresConfigurados[i] := nuValorAux;
                END IF;

                --'(R) DEDUCOES DA RECEITA CORRENTE'
                IF reRegistro.cod_estrutural = '4.9.0.0.0.00.00.00.00.00' THEN
                    SELECT (SUM(COALESCE(valor_contrib_plano_sss,0.00))
                             +SUM(COALESCE(valor_compensacao_financeira,0.00))
                             +SUM(COALESCE(valor_deducao_fundeb,0.00))
                           ) * -1 as valor
                        INTO nuValorAux
                    FROM stn.receita_corrente_liquida
                    WHERE mes = substr(stDataIni,4,2)::integer
                    AND ano = ''||substr(stDataIni,7,4)::integer||'';
                    arValoresConfigurados[i] := nuValorAux;
                END IF;

                --'Contrib. Plano Seg. Social Servidor'
                IF reRegistro.cod_estrutural = '4.9.5.0.0.00.00.00.00.00' THEN
                    SELECT SUM(COALESCE(valor_contrib_plano_sss,0.00)) * -1 as valor
                        INTO nuValorAux
                    FROM stn.receita_corrente_liquida
                    WHERE mes = substr(stDataIni,4,2)::integer
                    AND ano = ''||substr(stDataIni,7,4)::integer||'';
                    arValoresConfigurados[i] := nuValorAux;
                END IF;

                --'Compensação Financ. entre Regimes Previd.'
                IF reRegistro.cod_estrutural = '4.9.6.0.0.00.00.00.00.00' THEN
                    SELECT SUM(COALESCE(valor_compensacao_financeira,0.00)) * -1 as valor
                        INTO nuValorAux
                    FROM stn.receita_corrente_liquida
                    WHERE mes = substr(stDataIni,4,2)::integer
                    AND ano = ''||substr(stDataIni,7,4)::integer||'';
                    arValoresConfigurados[i] := nuValorAux;
                END IF;

                --'DEDUÇÃO DA RECEITA PARA FORMAÇÃO DO FUNDEB'
                IF reRegistro.cod_estrutural = '4.9.7.0.0.00.00.00.00.00' THEN
                    SELECT SUM(COALESCE(valor_deducao_fundeb,0.00)) * -1 as valor
                        INTO nuValorAux
                    FROM stn.receita_corrente_liquida
                    WHERE mes = substr(stDataIni,4,2)::integer
                    AND ano = ''||substr(stDataIni,7,4)::integer||'';
                    arValoresConfigurados[i] := nuValorAux;
                END IF;

            END IF;

            i := i + 1;
        END LOOP;

        reRegistro.mes_1  := COALESCE(arValoresConfigurados[1],reRegistro.mes_1);
        reRegistro.mes_2  := COALESCE(arValoresConfigurados[2],reRegistro.mes_2);
        reRegistro.mes_3  := COALESCE(arValoresConfigurados[3],reRegistro.mes_3);
        reRegistro.mes_4  := COALESCE(arValoresConfigurados[4],reRegistro.mes_4);
        reRegistro.mes_5  := COALESCE(arValoresConfigurados[5],reRegistro.mes_5);
        reRegistro.mes_6  := COALESCE(arValoresConfigurados[6],reRegistro.mes_6);
        reRegistro.mes_7  := COALESCE(arValoresConfigurados[7],reRegistro.mes_7);
        reRegistro.mes_8  := COALESCE(arValoresConfigurados[8],reRegistro.mes_8);
        reRegistro.mes_9  := COALESCE(arValoresConfigurados[9],reRegistro.mes_9);
        reRegistro.mes_10 := COALESCE(arValoresConfigurados[10],reRegistro.mes_10);
        reRegistro.mes_11 := COALESCE(arValoresConfigurados[11],reRegistro.mes_11);
        reRegistro.mes_12 := COALESCE(arValoresConfigurados[12],reRegistro.mes_12);

        -------- totalizando OUtras receitas tributárias
        if ( reRegistro.cod_estrutural = stConta||'1.1.0.0.00.00.00.00.00' ) then
            flTotalReceita[1]  := reRegistro.mes_1;
            flTotalReceita[2]  := reRegistro.mes_2;
            flTotalReceita[3]  := reRegistro.mes_3;
            flTotalReceita[4]  := reRegistro.mes_4;
            flTotalReceita[5]  := reRegistro.mes_5;
            flTotalReceita[6]  := reRegistro.mes_6;
            flTotalReceita[7]  := reRegistro.mes_7;
            flTotalReceita[8]  := reRegistro.mes_8;
            flTotalReceita[9]  := reRegistro.mes_9;
            flTotalReceita[10] := reRegistro.mes_10;
            flTotalReceita[11] := reRegistro.mes_11;
            flTotalReceita[12] := reRegistro.mes_12;
        end if;

        if ( (reRegistro.cod_estrutural = stConta||'1.1.1.2.02.00.00.00.00')
        or  (reRegistro.cod_estrutural = stConta||'1.1.1.2.04.00.00.00.00')
        or  (reRegistro.cod_estrutural = stConta||'1.1.1.2.08.00.00.00.00') )
       then
            flTotalMes[1]  := flTotalMes[1]  + reRegistro.mes_1;
            flTotalMes[2]  := flTotalMes[2]  + reRegistro.mes_2;
            flTotalMes[3]  := flTotalMes[3]  + reRegistro.mes_3;
            flTotalMes[4]  := flTotalMes[4]  + reRegistro.mes_4;
            flTotalMes[5]  := flTotalMes[5]  + reRegistro.mes_5;
            flTotalMes[6]  := flTotalMes[6]  + reRegistro.mes_6;
            flTotalMes[7]  := flTotalMes[7]  + reRegistro.mes_7;
            flTotalMes[8]  := flTotalMes[8]  + reRegistro.mes_8;
            flTotalMes[9]  := flTotalMes[9]  + reRegistro.mes_9;
            flTotalMes[10] := flTotalMes[10] + reRegistro.mes_10;
            flTotalMes[11] := flTotalMes[11] + reRegistro.mes_11;
            flTotalMes[12] := flTotalMes[12] + reRegistro.mes_12;
       end if;

       if ( reRegistro.cod_estrutural = stConta||'1.1.1.2.09.00.00.00.00' ) then
            reRegistro.mes_1  :=  flTotalReceita[1]  - flTotalMes[1];
            reRegistro.mes_2  :=  flTotalReceita[2]  - flTotalMes[2];
            reRegistro.mes_3  :=  flTotalReceita[3]  - flTotalMes[3];
            reRegistro.mes_4  :=  flTotalReceita[4]  - flTotalMes[4];
            reRegistro.mes_5  :=  flTotalReceita[5]  - flTotalMes[5];
            reRegistro.mes_6  :=  flTotalReceita[6]  - flTotalMes[6];
            reRegistro.mes_7  :=  flTotalReceita[7]  - flTotalMes[7];
            reRegistro.mes_8  :=  flTotalReceita[8]  - flTotalMes[8];
            reRegistro.mes_9  :=  flTotalReceita[9]  - flTotalMes[9];
            reRegistro.mes_10 :=  flTotalReceita[10] - flTotalMes[10];
            reRegistro.mes_11 :=  flTotalReceita[11] - flTotalMes[11];
            reRegistro.mes_12 :=  flTotalReceita[12] - flTotalMes[12];
       end if;

       --------FINAL totalizando OUtras receitas tributárias
       ------- Totalizando outras Tranferencias correntes
       if ( reRegistro.cod_estrutural = stConta||'1.7.0.0.00.00.00.00.00' ) then
            flTotalReceita_OTC[1]  := reRegistro.mes_1;
            flTotalReceita_OTC[2]  := reRegistro.mes_2;
            flTotalReceita_OTC[3]  := reRegistro.mes_3;
            flTotalReceita_OTC[4]  := reRegistro.mes_4;
            flTotalReceita_OTC[5]  := reRegistro.mes_5;
            flTotalReceita_OTC[6]  := reRegistro.mes_6;
            flTotalReceita_OTC[7]  := reRegistro.mes_7;
            flTotalReceita_OTC[8]  := reRegistro.mes_8;
            flTotalReceita_OTC[9]  := reRegistro.mes_9;
            flTotalReceita_OTC[10] := reRegistro.mes_10;
            flTotalReceita_OTC[11] := reRegistro.mes_11;
            flTotalReceita_OTC[12] := reRegistro.mes_12;
       end if;

       if ( (reRegistro.cod_estrutural = stConta||'1.7.2.1.01.02.00.00.00')
        or  (reRegistro.cod_estrutural = stConta||'1.7.2.2.01.01.00.00.00')
        or  (reRegistro.cod_estrutural = stConta||'1.7.2.2.01.02.00.00.00')
        or  (reRegistro.cod_estrutural = stConta||'1.7.2.1.36.00.00.00.00')
        or  (reRegistro.cod_estrutural = stConta||'1.7.2.1.01.05.00.00.00') )
       then
            flTotalMes_OTC[1]  := flTotalMes_OTC[1]  + reRegistro.mes_1;
            flTotalMes_OTC[2]  := flTotalMes_OTC[2]  + reRegistro.mes_2;
            flTotalMes_OTC[3]  := flTotalMes_OTC[3]  + reRegistro.mes_3;
            flTotalMes_OTC[4]  := flTotalMes_OTC[4]  + reRegistro.mes_4;
            flTotalMes_OTC[5]  := flTotalMes_OTC[5]  + reRegistro.mes_5;
            flTotalMes_OTC[6]  := flTotalMes_OTC[6]  + reRegistro.mes_6;
            flTotalMes_OTC[7]  := flTotalMes_OTC[7]  + reRegistro.mes_7;
            flTotalMes_OTC[8]  := flTotalMes_OTC[8]  + reRegistro.mes_8;
            flTotalMes_OTC[9]  := flTotalMes_OTC[9]  + reRegistro.mes_9;
            flTotalMes_OTC[10] := flTotalMes_OTC[10] + reRegistro.mes_10;
            flTotalMes_OTC[11] := flTotalMes_OTC[11] + reRegistro.mes_11;
            flTotalMes_OTC[12] := flTotalMes_OTC[12] + reRegistro.mes_12;
       end if;

       if ( reRegistro.cod_estrutural = stConta||'1.7.2.4.00.00.00.00.00' ) then
            flTotalMes_OTC[1]  := flTotalMes_OTC[1]  + reRegistro.mes_1;
            flTotalMes_OTC[2]  := flTotalMes_OTC[2]  + reRegistro.mes_2;
            flTotalMes_OTC[3]  := flTotalMes_OTC[3]  + reRegistro.mes_3;
            flTotalMes_OTC[4]  := flTotalMes_OTC[4]  + reRegistro.mes_4;
            flTotalMes_OTC[5]  := flTotalMes_OTC[5]  + reRegistro.mes_5;
            flTotalMes_OTC[6]  := flTotalMes_OTC[6]  + reRegistro.mes_6;
            flTotalMes_OTC[7]  := flTotalMes_OTC[7]  + reRegistro.mes_7;
            flTotalMes_OTC[8]  := flTotalMes_OTC[8]  + reRegistro.mes_8;
            flTotalMes_OTC[9]  := flTotalMes_OTC[9]  + reRegistro.mes_9;
            flTotalMes_OTC[10] := flTotalMes_OTC[10] + reRegistro.mes_10;
            flTotalMes_OTC[11] := flTotalMes_OTC[11] + reRegistro.mes_11;
            flTotalMes_OTC[12] := flTotalMes_OTC[12] + reRegistro.mes_12;
       end if;

       if ( reRegistro.cod_estrutural = stConta||'1.7.2.4.01.99.00.00.00' ) then
            reRegistro.cod_estrutural := cast (stConta||'1.7.2.2.01.99.00.00.00' as varchar);

            reRegistro.mes_1  := cast(( flTotalReceita_OTC[1]  - flTotalMes_OTC[1]  ) as numeric(14,2));
            reRegistro.mes_2  := cast(( flTotalReceita_OTC[2]  - flTotalMes_OTC[2]  ) as numeric(14,2));
            reRegistro.mes_3  := cast(( flTotalReceita_OTC[3]  - flTotalMes_OTC[3]  ) as numeric(14,2));
            reRegistro.mes_4  := cast(( flTotalReceita_OTC[4]  - flTotalMes_OTC[4]  ) as numeric(14,2));
            reRegistro.mes_5  := cast(( flTotalReceita_OTC[5]  - flTotalMes_OTC[5]  ) as numeric(14,2));
            reRegistro.mes_6  := cast(( flTotalReceita_OTC[6]  - flTotalMes_OTC[6]  ) as numeric(14,2));
            reRegistro.mes_7  := cast(( flTotalReceita_OTC[7]  - flTotalMes_OTC[7]  ) as numeric(14,2));
            reRegistro.mes_8  := cast(( flTotalReceita_OTC[8]  - flTotalMes_OTC[8]  ) as numeric(14,2));
            reRegistro.mes_9  := cast(( flTotalReceita_OTC[9]  - flTotalMes_OTC[9]  ) as numeric(14,2));
            reRegistro.mes_10 := cast(( flTotalReceita_OTC[10] - flTotalMes_OTC[10] ) as numeric(14,2));
            reRegistro.mes_11 := cast(( flTotalReceita_OTC[11] - flTotalMes_OTC[11] ) as numeric(14,2));
            reRegistro.mes_12 := cast(( flTotalReceita_OTC[12] - flTotalMes_OTC[12] ) as numeric(14,2));
       end if;

       ------- FINAL Totalizando outras Tranferencias correntes
       if ( reRegistro.cod_estrutural = stConta||'1.0.0.0.00.00.00.00.00' )
       then
          reRegistro.total_mes_1  := reRegistro.mes_1;
          reRegistro.total_mes_2  := reRegistro.mes_2;
          reRegistro.total_mes_3  := reRegistro.mes_3;
          reRegistro.total_mes_4  := reRegistro.mes_4;
          reRegistro.total_mes_5  := reRegistro.mes_5;
          reRegistro.total_mes_6  := reRegistro.mes_6;
          reRegistro.total_mes_7  := reRegistro.mes_7;
          reRegistro.total_mes_8  := reRegistro.mes_8;
          reRegistro.total_mes_9  := reRegistro.mes_9;
          reRegistro.total_mes_10 := reRegistro.mes_10;
          reRegistro.total_mes_11 := reRegistro.mes_11;
          reRegistro.total_mes_12 := reRegistro.mes_12;
       end if ;

       if (  reRegistro.cod_estrutural = stContaDedutora||'1.7.2.2.01.04.05.00.00' )
       then
          reRegistro.total_mes_1  := reRegistro.mes_1  * -1;
          reRegistro.total_mes_2  := reRegistro.mes_2  * -1;
          reRegistro.total_mes_3  := reRegistro.mes_3  * -1;
          reRegistro.total_mes_4  := reRegistro.mes_4  * -1;
          reRegistro.total_mes_5  := reRegistro.mes_5  * -1;
          reRegistro.total_mes_6  := reRegistro.mes_6  * -1;
          reRegistro.total_mes_7  := reRegistro.mes_7  * -1;
          reRegistro.total_mes_8  := reRegistro.mes_8  * -1;
          reRegistro.total_mes_9  := reRegistro.mes_9  * -1;
          reRegistro.total_mes_10 := reRegistro.mes_10 * -1;
          reRegistro.total_mes_11 := reRegistro.mes_11 * -1;
          reRegistro.total_mes_12 := reRegistro.mes_12 * -1;
       end if;

       if ( ( reRegistro.cod_estrutural = '4.9.0.0.0.00.00.00.00.00' )
         or ( reRegistro.cod_estrutural = stContaDedutora||'1.0.0.0.00.00.00.00.00' )
         or ( substring(reRegistro.cod_estrutural from  1 for 1) = '9' ) )
       then
          reRegistro.total_mes_1  := reRegistro.mes_1;
          reRegistro.total_mes_2  := reRegistro.mes_2;
          reRegistro.total_mes_3  := reRegistro.mes_3;
          reRegistro.total_mes_4  := reRegistro.mes_4;
          reRegistro.total_mes_5  := reRegistro.mes_5;
          reRegistro.total_mes_6  := reRegistro.mes_6;
          reRegistro.total_mes_7  := reRegistro.mes_7;
          reRegistro.total_mes_8  := reRegistro.mes_8;
          reRegistro.total_mes_9  := reRegistro.mes_9;
          reRegistro.total_mes_10 := reRegistro.mes_10;
          reRegistro.total_mes_11 := reRegistro.mes_11;
          reRegistro.total_mes_12 := reRegistro.mes_12;
       end if;

       RETURN next reRegistro;
    END LOOP;
    DROP TABLE tmp_valor;

RETURN;

END;
$$ LANGUAGE 'plpgsql';
