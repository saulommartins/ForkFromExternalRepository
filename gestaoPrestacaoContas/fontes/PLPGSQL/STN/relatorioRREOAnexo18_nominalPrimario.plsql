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
    * Script de função PLPGSQL - Relatório STN - RREO - Anexo 18.
    * Data de Criação: 20/05/2008


    * @author Henrique Boaventura

    * Casos de uso: uc-06.01.17

    $Id: $

*/

CREATE OR REPLACE FUNCTION stn.fn_rreo_anexo18_nominal_primario(varchar, integer ,varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    inBimestre          ALIAS FOR $2;
    stEntidades         ALIAS FOR $3;
    dtInicioAno         VARCHAR := '';
    stSql               VARCHAR := '';
    reRegistro          RECORD;
    inVlApurado         NUMERIC := 0;
    dtInicial           VARCHAR := '';
    dtFinal             VARCHAR := '';
    dtFinalAnterior     VARCHAR := '';
    stExercicioAnterior VARCHAR := '';
    arDatas             VARCHAR[] ;
    stEntidadesAuxiliar VARCHAR := '';

BEGIN
    --
    -- DESCOBRE A ENTIDADE RPPS
    --
    stEntidadesAuxiliar := selectIntoVarchar('SELECT array_to_string(ARRAY( SELECT valor
      FROM administracao.configuracao
     WHERE configuracao.exercicio = '||quote_literal(stExercicio)||'
       AND (parametro = ''cod_entidade_camara'' OR parametro = ''cod_entidade_prefeitura'') 
       AND valor IN ('||quote_literal(stEntidades)||')), '','') AS entidade');

    dtInicioAno := '01/01/' || stExercicio;
    arDatas := publico.bimestre ( stExercicio, inBimestre );
    dtInicial := arDatas [0];
    dtFinal   := arDatas [1];
    IF( inBimestre = 1 ) THEN
        dtFinalAnterior := dtFinal;
    ELSE
        arDatas := publico.bimestre ( stExercicio, inBimestre-1 );
        dtFinalAnterior := arDatas[1];        
    END IF;

    stExercicioAnterior :=  trim(to_char((to_number(stExercicio, '99999')-1), '99999'));

    -------------------------------------------------------
    -- Cria uma tabela temporaria para retornar os valores
    -------------------------------------------------------
    CREATE TEMPORARY TABLE tmp_retorno (
        grupo INTEGER,
        ordem INTEGER,
        descricao VARCHAR,
        vl_meta DECIMAL(14,2),
        vl_ate_periodo DECIMAL(14,2),
        porcentagem DECIMAL(14,2)
    );

    ---------------------------
    -- Retorna o valor nominal
    ---------------------------
    select cons.*                                                                                                                                                                                                 
        , ( ativo_exercicio_anterior + haveres_financeiros_exercicio_anterior + restos_exercicio_anterior   ) as deducoes_exercicio_anterior                                                                     
        , ( ativo_bimestre_anterior  + haveres_financeiros_bimestre_anterior  + restos_bimestre_anterior    ) as deducoes_bimestre_anterior                                                                      
        , ( ativo_saldo_bimestre     + haveres_financeiros_bimestre           + restos_bimestre             ) as deducoes_bimestre                                                                               
    INTO reRegistro                                                                                                                                                                                                                
   from                                                                                                                                                                                                          
   ( select                                                                                                                                                                                                      
            stn.pl_saldo_contas ( stExercicioAnterior                                                                                                                                             
                               , '01/01/' || stExercicioAnterior                                                                                                                                                                   
                               , '31/12/' || stExercicioAnterior                                                                                                                                                                   
                               , 'cod_estrutural like '''|| publico.fn_mascarareduzida( '1.1.1.0.0.00.00.00.00.00'  ) || '.%'' '                                                                                 
                               , stEntidadesAuxiliar ) as ativo_exercicio_anterior                                                                                                                                                
           , stn.pl_saldo_contas ( stExercicio 
                               , dtInicioAno 
                               , dtFinalAnterior 
                               , 'cod_estrutural like '''|| publico.fn_mascarareduzida( '1.1.1.0.0.00.00.00.00.00' ) || '.%'' '                                                                                  
                               , stEntidadesAuxiliar ) as ativo_bimestre_anterior                                                                                                                                                 
           , stn.pl_saldo_contas ( stExercicio 
                               , dtInicioAno 
                               , dtFinal
                               , 'cod_estrutural like '''|| publico.fn_mascarareduzida( '1.1.1.0.0.00.00.00.00.00'  ) || '.%'' '                                                                                 
                               , stEntidadesAuxiliar ) as ativo_saldo_bimestre                                                                                                                                                    
                                                                                                                                                                                                                 
           , stn.pl_saldo_contas ( stExercicioAnterior 
                               , '01/01/' || stExercicioAnterior                                                                                                                                                                   
                               , '31/12/' || stExercicioAnterior                                                                                                                                                                   
                               , 'cod_estrutural like ''1.1.2.%'''                                                                                                                                               
                               , stEntidadesAuxiliar ) as haveres_financeiros_exercicio_anterior                                                                                                                                  
                                                                                                                                                                                                                 
           , stn.pl_saldo_contas ( stExercicio 
                               , dtInicioAno
                               , dtFinalAnterior 
                               , 'cod_estrutural like  ''1.1.2.%'' '                                                                                                                                             
                               , stEntidadesAuxiliar ) as haveres_financeiros_bimestre_anterior                                                                                                                                   
           , stn.pl_saldo_contas ( stExercicio 
                               , dtInicioAno 
                               , dtFinal
                               , 'cod_estrutural like ''1.1.2.%'' '                                                                                                                                              
                               , stEntidadesAuxiliar ) as haveres_financeiros_bimestre                                                                                                                                            
                                                                                                                                                                                                                 
           , ( select sum(                                                                                                                                                                                       
                            stn.pl_saldo_contas ( stExercicioAnterior 
                                                 , '01/01/' || stExercicioAnterior                                                                                                                                                   
                                                 , '31/12/' || stExercicioAnterior                                                                                                                                                  
                                                 , 'cod_estrutural like '''|| publico.fn_mascarareduzida( plano_conta.cod_estrutural ) || '.%'' '                                                                
                                                 , stEntidadesAuxiliar ) )as saldo_exercicio_anterior                                                                                                                             
                 from contabilidade.plano_conta                                                                                                                                                                  
                where exercicio = stExercicioAnterior 
                  and cod_estrutural in( '2.1.2.1.1.02.00.00.00.00',                                                                                                                                             
                                         '2.1.2.1.1.03.02.00.00.00',                                                                                                                                             
                                         '2.1.2.1.2.02.00.00.00.00',                                                                                                                                             
                                         '2.1.2.1.2.03.02.00.00.00',                                                                                                                                             
                                         '2.1.2.1.3.01.00.02.00.00',                                                                                                                                             
                                         '2.1.2.1.3.03.00.02.00.00',                                                                                                                                             
                                         '2.1.2.1.3.04.00.02.00.00' ) ) as restos_exercicio_anterior                                                                                                             
                                                                                                                                                                                                                 
           , ( select sum ( stn.pl_saldo_contas (  plano_conta.exercicio                                                                                                                                         
                             , dtInicioAno 
                             , dtFinalAnterior 
                             , 'cod_estrutural like '''|| publico.fn_mascarareduzida( plano_conta.cod_estrutural ) || '.%'' '                                                                                    
                             , stEntidadesAuxiliar ) )as saldo_bimestre_anterior                                                                                                                                                  
                                                                                                                                                                                                                 
                   from contabilidade.plano_conta                                                                                                                                                                
                where exercicio = stExercicio 
                  and cod_estrutural in( '2.1.2.1.1.02.00.00.00.00',                                                                                                                                             
                                         '2.1.2.1.1.03.02.00.00.00',                                                                                                                                             
                                         '2.1.2.1.2.02.00.00.00.00',                                                                                                                                             
                                         '2.1.2.1.2.03.02.00.00.00',                                                                                                                                             
                                         '2.1.2.1.3.01.00.02.00.00',                                                                                                                                             
                                         '2.1.2.1.3.03.00.02.00.00',                                                                                                                                             
                                         '2.1.2.1.3.04.00.02.00.00' ) ) as restos_bimestre_anterior                                                                                                              
                                                                                                                                                                                                                 
           , ( select sum ( stn.pl_saldo_contas (  plano_conta.exercicio                                                                                                                                         
                             , dtInicioAno 
                             , dtFinal
                             , 'cod_estrutural like '''|| publico.fn_mascarareduzida( plano_conta.cod_estrutural ) || '.%'' '                                                                                    
                             , stEntidadesAuxiliar ) )as saldo_bimestre_anterior                                                                                                                                                  
                                                                                                                                                                                                                 
                   from contabilidade.plano_conta                                                                                                                                                                
                where exercicio = stExercicio 
                  and cod_estrutural in( '2.1.2.1.1.02.00.00.00.00',                                                                                                                                             
                                         '2.1.2.1.1.03.02.00.00.00',                                                                                                                                             
                                         '2.1.2.1.2.02.00.00.00.00',                                                                                                                                             
                                         '2.1.2.1.2.03.02.00.00.00',                                                                                                                                             
                                         '2.1.2.1.3.01.00.02.00.00',                                                                                                                                             
                                         '2.1.2.1.3.03.00.02.00.00',                                                                                                                                             
                                         '2.1.2.1.3.04.00.02.00.00' ) ) as restos_bimestre                                                                                                                       
                                                                                                                                                                                                                 
           ,(  select sum ( stn.pl_saldo_contas (  stExercicioAnterior 
                                           , '01/01/' || stExercicioAnterior                                                                                                                                                        
                                           , '31/12/' || stExercicioAnterior                                                                                                                                                        
                                           , ' (    REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''22211%''              OR REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''22221%''   OR REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''22212%''    OR REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''22222%''    OR REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''2121705%''    OR REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''21231020203%''     OR REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''2223''      OR REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''2224401%''    OR REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''22244%''   OR REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''22249000002%''    OR REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''22%'')           '
                                           , stEntidadesAuxiliar ) * -1 )) as divida_exercicio_anterior                                                                                                                                
                                                                                                                                                                                                                 
           ,(  select sum ( stn.pl_saldo_contas ( stExercicio 
                                           , dtInicioAno 
                                           , dtFinalAnterior 
                                           , ' (    REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''22211%''              OR REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''22221%''   OR REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''22212%''    OR REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''22222%''    OR REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''2121705%''    OR REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''21231020203%''     OR REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''2223''      OR REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''2224401%''    OR REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''22244%''   OR REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''22249000002%''    OR REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''22%'')           '
                                           , stEntidadesAuxiliar ) * -1 )) as divida_bimestre_anterior                                                                                                                                
                                                                                                                                                                                                                 
           ,(  select sum ( stn.pl_saldo_contas ( stExercicio 
                                           , dtInicioAno
                                           , dtFinal 
                                           , ' (    REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''22211%''              OR REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''22221%''   OR REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''22212%''    OR REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''22222%''    OR REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''2121705%''    OR REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''21231020203%''     OR REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''2223''      OR REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''2224401%''    OR REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''22244%''   OR REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''22249000002%''    OR REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''22%'')           '
                                           , stEntidadesAuxiliar ) * -1 )) as divida_bimestre                                                                                                                                
                                                                                                                                                                                                                 
      ) as cons;



    IF( inBimestre = 1 ) THEN
        inVlApurado := (reRegistro.divida_bimestre - reRegistro.deducoes_bimestre) - (reRegistro.divida_exercicio_anterior - reRegistro.deducoes_exercicio_anterior);
    ELSE
        inVlApurado := (reRegistro.divida_bimestre - reRegistro.deducoes_bimestre) - (reRegistro.divida_exercicio_anterior - reRegistro.deducoes_bimestre_anterior);
    END IF; 

    inVlApurado := (reRegistro.divida_bimestre - reRegistro.deducoes_bimestre) - (reRegistro.divida_exercicio_anterior - reRegistro.deducoes_exercicio_anterior); 

    -------------------------------
    -- Insere os valores na tabela
    -------------------------------
    INSERT INTO tmp_retorno VALUES (  1
                                    , 0
                                    , 'Resultado Nominal'
                                    , ( SELECT CASE WHEN valor = ''
                                                    THEN 00.00
                                                    ELSE TO_NUMBER(REPLACE(REPLACE(valor,'.',''),',','.'),'99999999999999D99')
                                                    END
                                          FROM administracao.configuracao
                                         WHERE configuracao.exercicio = stExercicio
                                           AND configuracao.parametro = 'meta_resultado_nominal_fixada'
                                           AND configuracao.cod_modulo = 36 )
                                    , inVlApurado
                                    , 0
                                   );

    IF( ( SELECT CASE WHEN valor = '' THEN 0.00 ELSE TO_NUMBER(REPLACE(REPLACE(valor,'.',''),',','.'),'99999999999999D99') END
            FROM administracao.configuracao
           WHERE configuracao.exercicio = stExercicio
             AND configuracao.parametro = 'meta_resultado_nominal_fixada'
             AND configuracao.cod_modulo = 36 ) > 0 ) THEN
        UPDATE tmp_retorno SET porcentagem = (vl_meta/vl_ate_periodo)*100 WHERE grupo = 1 AND ordem = 0;
    END IF;

    
    -----------------------------------------
    -- Retorna o valor do resultado primario
    -----------------------------------------

    stSql := '
    SELECT SUM(ate_bimestre) AS ate_bimestre
      FROM (
            SELECT SUM(ate_bimestre) AS ate_bimestre ';

    IF (stExercicio::integer > 2012) THEN
        stSql := stSql || ' FROM stn.fn_rreo_anexo7_receitas_novo('|| quote_literal(stExercicio) ||' , '||inBimestre||' , '||quote_literal(stEntidades)||') AS tbl ';
    ELSE 
        stSql := stSql || 'FROM stn.fn_rreo_anexo7_receitas('|| quote_literal(stExercicio) ||' , '||inBimestre||' , '||quote_literal(stEntidades)||') AS tbl ';
    END IF;

    stSql := stSql || '
                   (  ordem INTEGER
                    , grupo INTEGER
                    , cod_estrutural VARCHAR
                    , nivel INTEGER
                    , nom_conta VARCHAR
                    , previsao_atualizada NUMERIC
                    , no_bimestre NUMERIC
                    , ate_bimestre NUMERIC
                    , ate_bimestre_exercicio_anterior NUMERIC
                   ) ';

    IF (stExercicio::integer > 2012) THEN
        stSql := stSql || ' WHERE cod_estrutural IN ( ''1.0.0.0.00.00.00.00.00'', ''3.0.0.0.00.00.00.00.00'') ';
    ELSE
        stSql := stSql || ' WHERE cod_estrutural IN ( ''4.1.0.0.0.00.00.00.00.00'', ''4.3.0.0.0.00.00.00.00.00'') ';
    END IF;
    
    stSql := stSql || '
             UNION 
    
            SELECT SUM(ate_bimestre) * -1 AS ate_bimestre
              FROM stn.fn_rreo_anexo7_despesas( '|| quote_literal(stExercicio) ||' , '||inBimestre||' , '||quote_literal(stEntidades)||' ) AS tbl
                   (  grupo                           INTEGER
                    , cod_estrutural                  VARCHAR
                    , descricao                       VARCHAR
                    , nivel                           INTEGER
                    , dotacao_atualizada              NUMERIC(14,2)
                    , no_bimestre                     NUMERIC(14,2)
                    , ate_bimestre                    NUMERIC(14,2)
                    , ate_bimestre_exercicio_anterior NUMERIC(14,2)
                   )
             WHERE cod_estrutural IN ( ''4.9.0.0.00.00.00.00.00'', ''4.7.0.0.00.00.00.00.00'' , ''7.7.9.9.99.00.00.00.00'', ''9.9.9.9.99.00.00.00.00'' )
        ) AS tbl ';

    FOR reRegistro IN EXECUTE stSQL
    LOOP

    -------------------------------
    -- Insere os valores na tabela
    -------------------------------
    INSERT INTO tmp_retorno VALUES(  2
                                   , 0
                                   , 'Resultado Primário'
                                   , (SELECT CASE WHEN valor = ''
                                                    THEN 0.00
                                                    ELSE TO_NUMBER(REPLACE(REPLACE(valor,'.',''),',','.'),'99999999999999D99')
                                                    END
                                        FROM administracao.configuracao
                                       WHERE configuracao.exercicio = stExercicio
                                         AND configuracao.parametro = 'meta_resultado_primario_fixada'
                                         AND configuracao.cod_modulo = 36
                                     )
                                   , reRegistro.ate_bimestre
                                   , 0
                                  );

    IF( ( SELECT CASE WHEN valor = '' THEN 0.00 ELSE TO_NUMBER(REPLACE(REPLACE(valor,'.',''),',','.'),'99999999999999D99') END
            FROM administracao.configuracao
           WHERE configuracao.exercicio = stExercicio
             AND configuracao.parametro = 'meta_resultado_primario_fixada'
             AND configuracao.cod_modulo = 36 ) > 0 ) THEN
        IF stExercicio::integer > 2012 THEN
            UPDATE tmp_retorno SET porcentagem = (vl_ate_periodo/vl_meta) WHERE grupo = 2 AND ordem = 0;
        ELSE
            UPDATE tmp_retorno SET porcentagem = (vl_meta/vl_ate_periodo)*100 WHERE grupo = 2 AND ordem = 0;
        END IF;
    END IF;

    END LOOP;


    ----------------------------------------------------
    -- Retorna os valores da tabela temporaria
    ----------------------------------------------------
    stSql := '
        SELECT * 
          FROM tmp_retorno
      ORDER BY grupo
             , ordem
    ';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;

    DROP TABLE tmp_retorno ;

    RETURN;

END;
$$ LANGUAGE 'plpgsql';                                                                  
