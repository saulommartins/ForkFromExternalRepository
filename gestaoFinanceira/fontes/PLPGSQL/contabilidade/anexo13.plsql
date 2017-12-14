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
 * Arquivo que busca os dados do relatório
 *
 * @category   Urbem
 * @package    Framework
 * @author     Analista Tonismar Bernardo <tonismar.bernardo@cnm.org.br>
 * @author     Desenvolvedor Eduardo Schitz <eduardo.schitz@cnm.org.br>
 * $Id:$
 */

CREATE OR REPLACE FUNCTION orcamento.fn_anexo13 (varchar, varchar, varchar, varchar, varchar, varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stCodEntidades      ALIAS FOR $2;
    stDataInicial       ALIAS FOR $3;
    stDataFinal         ALIAS FOR $4;
    stDemonstrarDespesa ALIAS FOR $5;
    stTipoRelatorio     ALIAS FOR $6;
    stSql               VARCHAR   := '';
    stSqlAux            VARCHAR   := '';
    stSqlGrupo          VARCHAR   := '';
    stSqlEstrutural     VARCHAR   := '';
    stCodEstrutural     VARCHAR   := '';
    stDataInicialSaldo  VARCHAR   := '';
    stDataFinalSaldo    VARCHAR   := '';
    stExercicioAnterior VARCHAR   := '';
    inCount             INTEGER;
    inCodEntidadeRPPS   INTEGER;
    inLoop              INTEGER;
    reRegistro          RECORD;
    reReg               RECORD;
    reRegAux            RECORD;
    reRegGrupo          RECORD;
BEGIN
    SELECT valor
      INTO inCodEntidadeRPPS
      FROM administracao.configuracao
     WHERE parametro = 'cod_entidade_rpps'
       AND exercicio = stExercicio;

    stExercicioAnterior := trim(to_char((to_number(stExercicio,'9999')-1),'9999'));

    stSql := 'CREATE TEMPORARY TABLE tmp_receita AS (
    SELECT CAST(cod_estrutural as VARCHAR)
         , CAST(nivel as INTEGER)
         , CAST(nom_conta as VARCHAR)
         , CAST(nom_sistema_debito as VARCHAR)
         , CAST(nom_sistema_credito as VARCHAR)
         , SUM(vl_arrecadado) as vl_arrecadado
         , SUM(vl_arrecadado_credito) as vl_arrecadado_credito
         , SUM(vl_arrecadado_debito) as vl_arrecadado_debito
    FROM ( ( SELECT tbl.cod_estrutural
                  , publico.fn_nivel(tbl.cod_estrutural) as nivel
                  , sum( coalesce(tbl.vl_arrecadado_debito,0.00) ) + sum( coalesce(tbl.vl_arrecadado_credito,0.00) )  as vl_arrecadado
                  , sum(coalesce(tbl.vl_arrecadado_credito,0.00)) as vl_arrecadado_credito
                  , sum(coalesce(tbl.vl_arrecadado_debito,0.00)) as vl_arrecadado_debito           
                  , OCR.nom_conta                                                     
                  , tbl.nom_sistema_debito                                                                                  
                  , tbl.nom_sistema_credito                                                                                  
               FROM ( SELECT substr( OPC.cod_estrutural, 1,15 ) AS cod_estrutural                                                
                           , OPC.exercicio                                                                                       
                           , sum( coalesce( CCD.vl_lancamento, 0.00 ) ) * ( -1 ) AS vl_arrecadado_debito                         
                           , sum( coalesce( CCC.vl_lancamento, 0.00 ) ) * ( -1 ) AS vl_arrecadado_credito                        
                           , CCD.nom_sistema as nom_sistema_debito                                                               
                           , CCC.nom_sistema as nom_sistema_credito                                                               
                        FROM contabilidade.plano_conta      AS OPC                                                            
                   LEFT JOIN contabilidade.plano_analitica AS OCA 
                          ON OPC.cod_conta = OCA.cod_conta 
                         AND OPC.exercicio = OCA.exercicio                                                                                                
                   LEFT JOIN ( SELECT CCD.cod_plano                                                                           
                                    , CCD.exercicio                                                                           
                                    , sum( vl_lancamento ) as vl_lancamento                                                   
                                    , CSC.nom_sistema                                                                          
                                 FROM contabilidade.plano_conta      AS CPC 
                                    , contabilidade.plano_analitica  AS CPA      
                                    , contabilidade.conta_debito     AS CCD      
                                    , contabilidade.valor_lancamento AS CVLD     
                                    , contabilidade.lancamento       AS CLA      
                                    , contabilidade.lote             AS CLO      
                                    , contabilidade.sistema_contabil AS CSC       
                                WHERE CPC.cod_conta      = CPA.cod_conta      
                                  AND CPC.exercicio      = CPA.exercicio      
                                  AND CPC.cod_sistema    != 2                 
                                  AND CPA.cod_plano      = CCD.cod_plano      
                                  AND CPA.exercicio      = CCD.exercicio      
                                  AND CCD.cod_lote       = CVLD.cod_lote      
                                  AND CCD.tipo           = CVLD.tipo          
                                  AND CCD.sequencia      = CVLD.sequencia     
                                  AND CCD.exercicio      = CVLD.exercicio     
                                  AND CCD.tipo_valor     = CVLD.tipo_valor    
                                  AND CCD.cod_entidade   = CVLD.cod_entidade  
                                  AND CVLD.tipo_valor    = ' || quote_literal('D') || '                 
                                  AND CVLD.cod_lote      = CLA.cod_lote       
                                  AND CVLD.tipo          = CLA.tipo           
                                  AND CVLD.cod_entidade  = CLA.cod_entidade   
                                  AND CVLD.exercicio     = CLA.exercicio      
                                  AND CVLD.sequencia     = CLA.sequencia      
                                  AND CLA.cod_lote      = CLO.cod_lote        
                                  AND CLA.tipo          = CLO.tipo            
                                  AND CLA.cod_entidade  = CLO.cod_entidade    
                                  AND CLA.exercicio     = CLO.exercicio       
                                  AND CSC.exercicio     = CPC.exercicio       
                                  AND CSC.cod_sistema   = CPC.cod_sistema     
                                  AND CCD.exercicio      = ' || quote_literal(stExercicio) || '             
                                  AND CVLD.cod_entidade  IN(' || stCodEntidades || ')        
                                  AND CLO.dt_lote BETWEEN TO_DATE( ''' || stDataInicial || '''::varchar, ''dd/mm/yyyy'' )      
                                                      AND TO_DATE( ''' || stDataFinal || '''::varchar, ''dd/mm/yyyy'' )      
                                  AND CLO.tipo != ''I''                                                                      
                                  AND CLA.cod_historico not between 800 and 899                                          
                             GROUP BY CCD.cod_plano                          
                                    , CCD.exercicio                          
                                    , CSC.nom_sistema                         
                             ORDER BY CCD.cod_plano                          
                                    , CCD.exercicio                           
                             ) AS CCD 
                          ON OCA.cod_plano = CCD.cod_plano 
                         AND OCA.exercicio = CCD.exercicio                                              
                   LEFT JOIN ( SELECT CCC.cod_plano                          
                                    , CCC.exercicio                          
                                    , sum(vl_lancamento) as vl_lancamento    
                                    , CSC.nom_sistema                         
                                 FROM contabilidade.plano_conta      AS CPC  
                                    , contabilidade.plano_analitica  AS CPA  
                                    , contabilidade.conta_credito    AS CCC  
                                    , contabilidade.valor_lancamento AS CVLC 
                                    , contabilidade.lancamento       AS CLA  
                                    , contabilidade.lote             AS CLO  
                                    , contabilidade.sistema_contabil AS CSC   
                                WHERE CPC.cod_conta     = CPA.cod_conta  
                                  AND CPC.exercicio     = CPA.exercicio  
                                  AND CPC.cod_sistema   != 2             
                                  AND CPA.cod_plano     = CCC.cod_plano  
                                  AND CPA.exercicio     = CCC.exercicio  
                                  AND CCC.cod_lote      = CVLC.cod_lote  
                                  AND CCC.tipo          = CVLC.tipo      
                                  AND CCC.sequencia     = CVLC.sequencia 
                                  AND CCC.exercicio     = CVLC.exercicio 
                                  AND CCC.tipo_valor    = CVLC.tipo_valor   
                                  AND CCC.cod_entidade  = CVLC.cod_entidade 
                                  AND CVLC.tipo_valor   = ' || quote_literal('C') || '               
                                  AND CVLC.cod_lote     = CLA.cod_lote      
                                  AND CVLC.tipo         = CLA.tipo          
                                  AND CVLC.cod_entidade = CLA.cod_entidade  
                                  AND CVLC.exercicio    = CLA.exercicio     
                                  AND CVLC.sequencia    = CLA.sequencia     
                                  AND CLA.cod_lote      = CLO.cod_lote       
                                  AND CLA.tipo          = CLO.tipo           
                                  AND CLA.cod_entidade  = CLO.cod_entidade   
                                  AND CLA.exercicio     = CLO.exercicio      
                                  AND CSC.exercicio     = CPC.exercicio      
                                  AND CSC.cod_sistema   = CPC.cod_sistema    
                                  AND CCC.exercicio     = ' || quote_literal(stExercicio) || ' 
                                  AND CVLC.cod_entidade IN(' || stCodEntidades || ' )       
                                  AND CLO.dt_lote BETWEEN TO_DATE( ''' || stDataInicial || '''::varchar, ''dd/mm/yyyy'' )      
                                                      AND TO_DATE( ''' || stDataFinal || '''::varchar, ''dd/mm/yyyy'' )      
                                  AND CLO.tipo != ''I''                                                                      
                                  AND CLA.cod_historico not between 800 and 899 
                             GROUP BY CCC.cod_plano                 
                                    , CCC.exercicio                 
                                    , CSC.nom_sistema                
                             ORDER BY CCC.cod_plano                 
                                    , CCC.exercicio                  
                             ) AS CCC 
                          ON OCA.cod_plano = CCC.cod_plano   
                         AND OCA.exercicio = CCC.exercicio                                      
             
                       WHERE OPC.exercicio = ' || quote_literal(stExercicio) || ' --AND       
                         AND ( OPC.cod_estrutural  like  ' || quote_literal('1.1.1.1.1%') || '       
                          OR OPC.cod_estrutural    like  ' || quote_literal('1.1.1.1.2%') || '       
                          OR OPC.cod_estrutural    like  ' || quote_literal('1.1.1.1.3%') || '       
                          OR OPC.cod_estrutural    like  ' || quote_literal('1.1.2%') || '           
                          OR OPC.cod_estrutural    like  ' || quote_literal('1.1.5%') || '           
                          OR OPC.cod_estrutural    like  ' || quote_literal('2.1.1%') ||' ';

                        IF (inCodEntidadeRPPS::varchar = stCodEntidades) THEN
                            stSql := stSql || '
                             OR ( OPC.cod_estrutural    like  ' || quote_literal('2.1.2.1.9%') || '
                              AND OPC.cod_estrutural NOT like ' || quote_literal('2.1.2.1.9.08%') ||' ) ';
                        ELSE
                            stSql := stSql || '
                             OR OPC.cod_estrutural    like  ' || quote_literal('2.1.2.1.9%') || ' ';
                        END IF;

                        stSql := stSql || '
                          OR OPC.cod_estrutural    like  ' || quote_literal('2.2.1%') || '           
                          OR OPC.cod_estrutural    like  ' || quote_literal('2.9.2.4.1.04.01%') || ' 
                          OR OPC.cod_estrutural    like  ' || quote_literal('2.9.2.4.1.04.02%') || ' 
                          OR OPC.cod_estrutural    like  ' || quote_literal('2.9.5.2%') || '         
                          OR OPC.cod_estrutural    like  ' || quote_literal('3.3%') || '             
                          OR OPC.cod_estrutural    like  ' || quote_literal('3.4%') || '             
                          OR OPC.cod_estrutural    like  ' || quote_literal('3.9%') || '             
                          OR OPC.cod_estrutural    like  ' || quote_literal('4.1%') || '                                                           
                          OR OPC.cod_estrutural    like  ' || quote_literal('4.2%') || '              
                          OR OPC.cod_estrutural    like  ' || quote_literal('4.7%') || '              
                          OR OPC.cod_estrutural    like  ' || quote_literal('4.8%') || '              
                          OR OPC.cod_estrutural    like  ' || quote_literal('5.2.1.9%') || '          
                          OR OPC.cod_estrutural    like  ' || quote_literal('5.2.2.2%') || '          
                          OR OPC.cod_estrutural    like  ' || quote_literal('5.1.2.1%') || '          
                          OR OPC.cod_estrutural    like  ' || quote_literal('4.9%') || '              
                          OR OPC.cod_estrutural    like  ' || quote_literal('9%') || '  -- Se for 2008
                          OR OPC.cod_estrutural    like  ' || quote_literal('6.2.1.9%') || '          
                          OR OPC.cod_estrutural    like  ' || quote_literal('6.1.2.1%') || '          
                          OR OPC.cod_estrutural    like  ' || quote_literal('6.2.2.2%') || ')         
                    GROUP BY                                                  
                         OPC.cod_estrutural,                                 
                         OPC.exercicio,                                      
                         CCD.nom_sistema,                                    
                         CCC.nom_sistema                                     
                   ORDER BY                                                  
                         OPC.cod_estrutural,                                 
                         OPC.exercicio                                       
                 ) AS tbl,                                                   
                 contabilidade.plano_conta AS OCR                            
             WHERE tbl.cod_estrutural = substr( OCR.cod_estrutural, 1, 15 ) 
               AND ( length( publico.fn_mascarareduzida( OCR.cod_estrutural ) ) <= 15 
                     OR ( OCR.cod_estrutural = ' || quote_literal('4.9.7.2.1.01.05.04.00.00') || '                                              
                          OR OCR.cod_estrutural = ' || quote_literal('4.9.7.2.2.01.02.04.00.00') || '                                              
                        )
                   ) 
               AND tbl.exercicio = OCR.exercicio                                                                       
          GROUP BY tbl.cod_estrutural                                                                                      
                 , OCR.nom_conta                                                                                           
                 , tbl.nom_sistema_debito                                                                                  
                 , tbl.nom_sistema_credito                                                                                  
          ORDER BY tbl.cod_estrutural                                                                                      
                 , OCR.nom_conta
        ) ';
    

    stSql := stSql || '
    ) as tbl
    GROUP BY cod_estrutural
           , nivel
           , nom_conta 
           , nom_sistema_debito
           , nom_sistema_credito
    ORDER BY cod_estrutural
    )';

    EXECUTE stSql;


    IF (stTipoRelatorio = 1::varchar) THEN
        stSql := 'CREATE TEMPORARY TABLE tmp_despesa AS (
            SELECT primeiro.*
              FROM ( SELECT *
                       FROM orcamento.despesaFuncao( ''' || stExercicio || '''
                                                   , ''' || stCodEntidades || '''
                                                   , ''' || stDataInicial || '''
                                                   , ''' || stDataFinal || '''
                                                   , ''' || stDemonstrarDespesa || ''' 
                                                   
                                                   ) as retorno (
                                                        cod_estrutural        VARCHAR
                                                      , nom_conta             VARCHAR
                                                      , vl_total              NUMERIC 
                                                   )
             ) as primeiro
        )';

        EXECUTE stSql;

    ELSEIF (stTipoRelatorio = 2::varchar) THEN
        stSql := 'CREATE TEMPORARY TABLE tmp_despesa AS (
        SELECT primeiro.*
          FROM ( SELECT *
                   FROM orcamento.despesaCategoriaEconomica( ''' || stExercicio || '''
                                                           , ''' || stCodEntidades || '''
                                                           , ''' || stDataInicial || '''
                                                           , ''' || stDataFinal || '''
                                                           , ''' || stDemonstrarDespesa || ''' 
                                                           ) as retorno (
                                                                cod_estrutural        VARCHAR
                                                              , nom_conta             VARCHAR
                                                              , vl_total              NUMERIC)
                                                           ) as primeiro
        )';    

        EXECUTE stSql;
    
    END IF;

    IF (SUBSTR(stDataInicial, 1, 5) = '01/01') THEN
        stDataInicialSaldo = '31/12/' || stExercicioAnterior;
        stDataFinalSaldo   = '01/01/' || stExercicio;
    ELSE 
        stDataInicialSaldo = '01/01/' || stExercicio;
        stDataFinalSaldo   = stDataFinal;
    END IF;

    stSql := 'CREATE TEMPORARY TABLE tmp_saldo AS (
    SELECT * 
      FROM orcamento.saldoReceita(''' || stExercicio || '''
                                , ''' || stCodEntidades || '''
                                , ''' || stDataInicialSaldo || ''' 
                                , ''' || stDataFinalSaldo || '''
                                  ) as retorno (
                                       cod_estrutural        VARCHAR
                                     , nivel                 INTEGER
                                     , vl_arrecadado_credito NUMERIC
                                     , vl_arrecadado_debito  NUMERIC
                                     , vl_arrecadado         NUMERIC
                                     , nom_conta             VARCHAR
                                  ) 
    )';

    EXECUTE stSql;

    stSql := 'CREATE TEMPORARY TABLE tmp_variacao AS (
    SELECT *
      FROM orcamento.saldoVariacao(''' || stExercicio || '''
                                 , ''' || stCodEntidades || '''
                                 , ''' || stDataInicial || '''
                                 , ''' || stDataFinal || '''
                                   ) as retorno (
                                      vl_variacao_receita         NUMERIC
                                    , vl_variacao_despesa         NUMERIC
                                   ) 
    )';

    EXECUTE stSql;


    stSql := ' CREATE TEMPORARY TABLE tmp_receita_despesa (
                cod_estrutural              VARCHAR
              , nom_conta_receita           VARCHAR
              , nom_conta_despesa           VARCHAR
              , grupo                       INTEGER
              , subgrupo                    INTEGER
              , vl_arrecadado               NUMERIC
              , vl_despesa                  NUMERIC
             )';

    EXECUTE stSql;

    stSql := ' INSERT INTO tmp_receita_despesa 
                         ( cod_estrutural
                         , nom_conta_receita
                         , nom_conta_despesa
                         , grupo
                         , subgrupo
                         , vl_arrecadado
                         , vl_despesa )
                    VALUES ( ' || quote_literal('2.1.1.1.1.11.01') || '
                         , ' || quote_literal('CREDITOS EM CIRCULACAO') || '
                         , ' || quote_literal('CREDITOS EM CIRCULACAO') || '
                         , 2
                         , 0
                         , ( SELECT SUM(COALESCE(ABS(vl_arrecadado_credito), 0.00)) FROM tmp_receita WHERE cod_estrutural like ' || quote_literal('1.1.2%') ||' )
                         , ( SELECT SUM(COALESCE(ABS(vl_arrecadado_debito), 0.00)) FROM tmp_receita WHERE cod_estrutural like ' || quote_literal('1.1.2%') ||' )
                           )';
    EXECUTE stSql;

    stSql := ' INSERT INTO tmp_receita_despesa 
                         ( cod_estrutural
                         , nom_conta_receita
                         , nom_conta_despesa
                         , grupo
                         , subgrupo
                         , vl_arrecadado
                         , vl_despesa )
                    VALUES ( ' || quote_literal('2.1.1.1.1.11.02') || '
                         , ' || quote_literal('RECEITAS DE OUTRAS ENTIDADES') || '
                         , ' || quote_literal('DESPESAS DE OUTRAS ENTIDADES') || '
                         , 2
                         , 0
                         , ( SELECT SUM(COALESCE(vl_arrecadado, 0.00)) FROM tmp_receita WHERE cod_estrutural like ' || quote_literal('6.2.1.9%') || ' )
                         , ( SELECT SUM(COALESCE(vl_arrecadado, 0.00)) FROM tmp_receita WHERE cod_estrutural like ' || quote_literal('5.2.1.9%') || ' ) 
                           )';

    EXECUTE stSql;

    stSql := ' INSERT INTO tmp_receita_despesa 
                         ( cod_estrutural
                         , nom_conta_receita
                         , nom_conta_despesa
                         , grupo
                         , subgrupo
                         , vl_arrecadado
                         , vl_despesa )
                    VALUES ( ' || quote_literal('2.1.1.1.1.11.03') || '
                         , ' || quote_literal('TRANSFERENCIAS FINANCEIRAS RECEBIDAS') || '
                         , ' || quote_literal('TRANSFERENCIAS FINANCEIRAS CONCEDIDAS') || '
                         , 2
                         , 0
                         , ( SELECT SUM(COALESCE(vl_arrecadado, 0.00)) FROM tmp_receita WHERE cod_estrutural like ' || quote_literal('6.2.2.2%') || ' OR cod_estrutural like ' || quote_literal('6.1.2.1%') ||' )
                         , ( SELECT SUM(COALESCE(ABS(vl_arrecadado), 0.00)) FROM tmp_receita WHERE cod_estrutural like '|| quote_literal('5.2.2.2%') || ' OR cod_estrutural like ' || quote_literal('5.1.2.1%') ||' )
                           )';

    EXECUTE stSql;

    stSql := ' INSERT INTO tmp_receita_despesa 
                         ( cod_estrutural
                         , nom_conta_receita
                         , nom_conta_despesa
                         , grupo
                         , subgrupo
                         , vl_arrecadado
                         , vl_despesa )
                    VALUES ( ' || quote_literal('2.1.1.1.1.11.04') || '
                         , ' || quote_literal('RESTOS A PAGAR') || '
                         , ' || quote_literal('RESTOS A PAGAR PAGOS') || '
                         , 2
                         , 0 ';
        IF (stDemonstrarDespesa = 'E') THEN
        stSql := stSql || '
                         , ( SELECT SUM(COALESCE(vl_arrecadado, 0.00)) FROM tmp_receita WHERE ( cod_estrutural like ' || quote_literal('2.9.2.4.1.04.01%') ||' OR cod_estrutural like ' || quote_literal('2.9.2.4.1.04.02%') ||' ) ) ';
        ELSEIF (stDemonstrarDespesa = 'L') THEN
        stSql := stSql || '
                         , ( SELECT SUM(COALESCE(vl_arrecadado, 0.00)) FROM tmp_receita WHERE cod_estrutural like '|| quote_literal('2.9.2.4.1.04.02%') || ' ) ';
        ELSE
        stSql := stSql || '
                         , 0.00   ';
        END IF;
        stSql := stSql || '
                         , ( SELECT SUM(COALESCE(vl_arrecadado, 0.00)) FROM tmp_receita WHERE nivel = 4 AND cod_estrutural like '|| quote_literal('2.9.5.2%') ||' )
                           )';

    EXECUTE stSql;


    stSql := ' INSERT INTO tmp_receita_despesa 
                         ( cod_estrutural
                         , nom_conta_receita
                         , nom_conta_despesa
                         , grupo
                         , subgrupo
                         , vl_arrecadado
                         , vl_despesa )
                    VALUES ( ' || quote_literal('2.1.1.1.1.11.05') || '
                         , ' || quote_literal('DEPOSITOS') || '
                         , ' || quote_literal('DEPOSITOS') || '
                         , 2
                         , 0 
                         , ( SELECT SUM(COALESCE(vl_arrecadado_credito, 0.00)) FROM tmp_receita WHERE cod_estrutural like ' || quote_literal('2.1.1%') || ' OR cod_estrutural like ' || quote_literal('2.2.1%') || ' )
                         , ( SELECT SUM(COALESCE(ABS(vl_arrecadado_debito), 0.00)) FROM tmp_receita WHERE cod_estrutural like ' || quote_literal('2.1.1%') || ' OR cod_estrutural like ' || quote_literal('2.2.1%') || ' )
                           )';

    EXECUTE stSql;

    stSql := ' INSERT INTO tmp_receita_despesa 
                         ( cod_estrutural
                         , nom_conta_receita
                         , nom_conta_despesa
                         , grupo
                         , subgrupo
                         , vl_arrecadado
                         , vl_despesa )
                    VALUES ( ' || quote_literal('2.1.1.1.1.11.06') || '
                         , ' || quote_literal('OUTRAS OBRIGAÇÕES') || '
                         , ' || quote_literal('OUTRAS OBRIGAÇÕES') || '
                         , 2
                         , 0
                         , ( SELECT SUM(COALESCE(vl_arrecadado_credito, 0.00)) FROM tmp_receita WHERE cod_estrutural like ' || quote_literal('2.1.2.1.9%') || ' )
                         , ( SELECT SUM(COALESCE(ABS(vl_arrecadado_debito), 0.00)) FROM tmp_receita WHERE cod_estrutural like ' || quote_literal('2.1.2.1.9%') || ' )
                           )';

    EXECUTE stSql;

    stSql := ' INSERT INTO tmp_receita_despesa 
                         ( cod_estrutural
                         , nom_conta_receita
                         , nom_conta_despesa
                         , grupo
                         , subgrupo
                         , vl_arrecadado
                         , vl_despesa )
                    VALUES ( ''2.1.1.1.1.11.07''
                         , ''VARIAÇÃO FINANCEIRA''
                         , ''VARIAÇÃO FINANCEIRA''
                         , 2
                         , 0
                         , ( SELECT vl_variacao_receita FROM tmp_variacao ) 
                         , ( SELECT vl_variacao_despesa FROM tmp_variacao ) 
                           )';

    EXECUTE stSql;

    stSql := ' INSERT INTO tmp_receita_despesa 
                         ( cod_estrutural
                         , nom_conta_receita
                         , nom_conta_despesa
                         , grupo
                         , subgrupo
                         , vl_arrecadado
                         , vl_despesa )
                    VALUES ( ''2.1.1.1.1.11.08''
                         , ''CAIXA''
                         , ''CAIXA''
                         , 3
                         , 3
                         , ( SELECT SUM(COALESCE(vl_arrecadado_credito, 0.00)) FROM tmp_saldo WHERE cod_estrutural like ''1.1.1.1.1%'' )
                         , ( SELECT ((SELECT SUM(COALESCE(vl_arrecadado, 0.00)) FROM tmp_receita WHERE cod_estrutural like ''1.1.1.1.1%'' ) * -1) + (SELECT SUM(COALESCE(vl_arrecadado_credito, 0.00)) FROM tmp_saldo WHERE cod_estrutural like ''1.1.1.1.1%'') )
                           )';

    EXECUTE stSql;

    stSql := ' INSERT INTO tmp_receita_despesa 
                         ( cod_estrutural
                         , nom_conta_receita
                         , nom_conta_despesa
                         , grupo
                         , subgrupo
                         , vl_arrecadado
                         , vl_despesa )
                    VALUES ( ''2.1.1.1.1.11.09''
                         , ''BANCOS CONTA MOVIMENTO''
                         , ''BANCOS CONTA MOVIMENTO''
                         , 3
                         , 3
                         , ( SELECT SUM(COALESCE(vl_arrecadado_credito, 0.00)) FROM tmp_saldo WHERE cod_estrutural like ''1.1.1.1.2%'' )
                         , ( SELECT ((SELECT SUM(COALESCE(vl_arrecadado, 0.00)) FROM tmp_receita WHERE cod_estrutural like ''1.1.1.1.2%'' ) * -1) + (SELECT SUM(COALESCE(vl_arrecadado_credito, 0.00)) FROM tmp_saldo WHERE cod_estrutural like ''1.1.1.1.2%'') )
                           )';

    EXECUTE stSql;

    stSql := ' INSERT INTO tmp_receita_despesa 
                         ( cod_estrutural
                         , nom_conta_receita
                         , nom_conta_despesa
                         , grupo
                         , subgrupo
                         , vl_arrecadado
                         , vl_despesa )
                    VALUES ( ''2.1.1.1.1.11.10''
                         , ''APLICACOES FINANCEIRAS''
                         , ''APLICACOES FINANCEIRAS''
                         , 3
                         , 3
                         , ( SELECT SUM(COALESCE(vl_arrecadado_credito, 0.00)) FROM tmp_saldo WHERE cod_estrutural like ''1.1.1.1.3%'' )
                         , ( SELECT ((SELECT SUM(COALESCE(vl_arrecadado, 0.00)) FROM tmp_receita WHERE cod_estrutural like ''1.1.1.1.3%'' ) * -1) + (SELECT SUM(COALESCE(vl_arrecadado_credito, 0.00)) FROM tmp_saldo WHERE cod_estrutural like ''1.1.1.1.3%'') )
                           )';

    EXECUTE stSql;

    stSql := ' INSERT INTO tmp_receita_despesa 
                         ( cod_estrutural
                         , nom_conta_receita
                         , nom_conta_despesa
                         , grupo
                         , subgrupo
                         , vl_arrecadado
                         , vl_despesa )
                    VALUES ( ''2.1.1.1.1.11.11''
                         , ''INVESTIMENTOS DOS REGIMES PROPRIOS DE PREVIDENCIA''
                         , ''INVESTIMENTOS DOS REGIMES PROPRIOS DE PREVIDENCIA''
                         , 3
                         , 3
                         , ( SELECT SUM(COALESCE(vl_arrecadado_credito, 0.00)) FROM tmp_saldo WHERE cod_estrutural like ''1.1.5%'' )
                         , ( SELECT ((SELECT SUM(COALESCE(vl_arrecadado, 0.00)) FROM tmp_receita WHERE cod_estrutural like ''1.1.5%'' ) * -1) + (SELECT SUM(COALESCE(vl_arrecadado_credito, 0.00)) FROM tmp_saldo WHERE cod_estrutural like ''1.1.5%'') )
                           )';

    EXECUTE stSql;

    stSql := ' CREATE TEMPORARY TABLE tmp_retorno AS (

    -- RECEITAS CORRENTES
      ( SELECT cod_estrutural
             , nom_conta as nom_conta_receita
             , CAST('''' as VARCHAR) as nom_conta_despesa
             , 1 as grupo
             , 1 as subgrupo
             , vl_arrecadado
             , 0.00 as vl_despesa
          FROM tmp_receita 
         WHERE ( nivel = 3
           AND cod_estrutural like ''4.1%'' )
            OR ( nivel = 2
           AND cod_estrutural like ''4.7%'' )
      )

    UNION ALL

    -- RECEITAS DEDUTORAS
      ( SELECT cod_estrutural
             , nom_conta as nom_conta_receita
             , CAST('''' as VARCHAR) as nom_conta_despesa
             , 1 as grupo
             , 1 as subgrupo
             , vl_arrecadado
             , 0.00 as vl_despesa
          FROM tmp_receita
         WHERE nivel = 2
           AND (    cod_estrutural like ''9%''
                 OR cod_estrutural like ''4.9%''
               )
      )

    UNION ALL

    -- RECEITAS DE CAPITAL
     ( SELECT cod_estrutural
            , nom_conta as nom_conta_receita
            , CAST('''' as VARCHAR) as nom_conta_despesa
            , 1 as grupo
            , 2 as subgrupo
            , vl_arrecadado
            , 0.00 as vl_despesa
         FROM tmp_receita
         WHERE ( nivel = 3
           AND cod_estrutural like ''4.2%'' )
            OR ( nivel = 2
           AND cod_estrutural like ''4.8%'' )
      )

    UNION ALL

    -- GRUPO 2 E 3
     ( SELECT cod_estrutural
            , nom_conta_receita
            , nom_conta_despesa
            , grupo
            , subgrupo
            , vl_arrecadado
            , vl_despesa
         FROM tmp_receita_despesa
      )

    )';

    EXECUTE stSql;

    IF (stTipoRelatorio = 1::varchar) THEN
        stSql := 'DELETE FROM tmp_despesa WHERE vl_total = 0 ';

        EXECUTE stSql;
      
        SELECT ( ( SELECT COUNT(cod_estrutural) FROM tmp_retorno WHERE grupo = 1 AND subgrupo = 1) - 
               ( SELECT COUNT(cod_estrutural) FROM tmp_despesa) ) INTO inCount;
   
        -- INSERE VALORES EM BRANCO PARA INSERIR AS DESPESAS NA MESMA LINHA DAS RECEITAS 
        IF (inCount < 0) THEN
            FOR inLoop IN 1..ABS(inCount) LOOP
                stSql := ' INSERT INTO tmp_retorno
                             ( cod_estrutural
                             , nom_conta_receita
                             , nom_conta_despesa
                             , grupo
                             , subgrupo
                             , vl_arrecadado
                             , vl_despesa )
                        VALUES ( ''4.3.' || inLoop || '''
                             , ''''
                             , ''''
                             , 1
                             , 1
                             , 0.00 
                             , 0.00  ) ';
       
                EXECUTE stSql;            
       
            END LOOP;
        END IF;

        stSql := 'SELECT * FROM tmp_despesa';
   
        -- INSERE AS DESPESAS NA MESMA LINHA DAS RECEITAS GRUPO 1 DA TABELA DE RETORNO 
        FOR reRegAux IN EXECUTE stSql
        LOOP
            SELECT cod_estrutural INTO stCodEstrutural 
              FROM tmp_retorno 
             WHERE nom_conta_despesa = '' 
               AND grupo = 1 
               AND subgrupo = 1
          ORDER BY cod_estrutural LIMIT 1;
    
            stSqlAux := 'UPDATE tmp_retorno 
                            SET vl_despesa = ' || reRegAux.vl_total || '
                              , nom_conta_despesa = ''' || reRegAux.nom_conta || '''
                          WHERE cod_estrutural = ''' || stCodEstrutural || ''' '; 
    
            EXECUTE stSqlAux;
        END LOOP;
    ELSEIF (stTipoRelatorio = 2::varchar) THEN
        stSqlGrupo := 'SELECT DISTINCT subgrupo FROM tmp_retorno WHERE grupo = 1';

        FOR reRegGrupo IN EXECUTE stSqlGrupo LOOP
        
            SELECT ( ( SELECT COUNT(cod_estrutural) FROM tmp_retorno WHERE grupo = 1 AND subgrupo = reRegGrupo.subgrupo) - 
                   ( SELECT COUNT(cod_estrutural) FROM tmp_despesa) ) INTO inCount;
   
            -- INSERE VALORES EM BRANCO PARA INSERIR AS DESPESAS NA MESMA LINHA DAS RECEITAS 
            IF (inCount < 0) THEN
                FOR inLoop IN 1..ABS(inCount) LOOP
                    stSql := ' INSERT INTO tmp_retorno
                                 ( cod_estrutural
                                 , nom_conta_receita
                                 , nom_conta_despesa
                                 , grupo
                                 , subgrupo
                                 , vl_arrecadado
                                 , vl_despesa )
                            VALUES ( ''4.3.' || inLoop || '''
                                 , ''''
                                 , ''''
                                 , 1
                                 , ' || reRegGrupo.subgrupo || '
                                 , ''
                                 , '' ) ';
        
                    EXECUTE stSql;            
        
                END LOOP;
            END IF;

            IF (reRegGrupo.subgrupo = 1) THEN        
                stSql := 'SELECT * FROM tmp_despesa WHERE cod_estrutural like ''3.%'' ';
            ELSEIF (reRegGrupo.subgrupo = 2) THEN
                stSql := 'SELECT * FROM tmp_despesa WHERE cod_estrutural like ''4.%'' ';
            END IF;
   
            -- INSERE AS DESPESAS NA MESMA LINHA DAS RECEITAS GRUPO 1 DA TABELA DE RETORNO 
            FOR reRegAux IN EXECUTE stSql
            LOOP
                SELECT cod_estrutural INTO stCodEstrutural FROM tmp_retorno 
                 WHERE nom_conta_despesa = '' 
                   AND grupo = 1 
                   AND subgrupo = reRegGrupo.subgrupo 
              ORDER BY cod_estrutural LIMIT 1;
    
                stSqlAux := 'UPDATE tmp_retorno 
                                SET vl_despesa = ' || reRegAux.vl_total || '
                                  , nom_conta_despesa = ''' || reRegAux.nom_conta || '''
                              WHERE cod_estrutural = ''' || stCodEstrutural || ''' '; 
    
                EXECUTE stSqlAux;
            END LOOP;

        END LOOP;

    END IF;

    stSqlEstrutural := ' SELECT substr(cod_estrutural, 1, 5) AS cod_estrutural 
                           FROM tmp_retorno 
                          WHERE cod_estrutural NOT LIKE ''2.1.1.1.1.11.%''  
                       ';    

    FOR reReg IN EXECUTE stSqlEstrutural
    LOOP

        IF (reReg.cod_estrutural = '4.7.0') THEN
            stSqlAux := ' 
                          UPDATE tmp_retorno 
                             SET vl_arrecadado = ( SELECT sum(vl_arrecadado) 
                                                     FROM tmp_receita 
                                                    WHERE substr(cod_estrutural, 1, 3) = substr(''' || reReg.cod_estrutural || ''', 1, 3)
                                                 ) 
                           WHERE cod_estrutural not like ''9.%'' AND substr(cod_estrutural, 1, 3) = substr(''' || reReg.cod_estrutural || ''', 1, 3)
                        ';

        ELSE

            stSqlAux := ' 
                          UPDATE tmp_retorno 
                             SET vl_arrecadado = ( SELECT sum(vl_arrecadado) 
                                                     FROM tmp_receita 
                                                    WHERE substr(cod_estrutural, 1, 5) = ''' || reReg.cod_estrutural || ''' 
                                                 ) 
                           WHERE cod_estrutural not like ''9.%'' AND substr(cod_estrutural, 1, 5) = ''' || reReg.cod_estrutural || '''
                        ';

        END IF;

        EXECUTE stSqlAux;

        stSqlAux := ' 
                      UPDATE tmp_retorno
                         SET vl_arrecadado = ( SELECT sum(vl_arrecadado)
                                                 FROM tmp_receita
                                                WHERE cod_estrutural like ''9.%'' OR cod_estrutural like ''4.9%''
                                             )
                       WHERE cod_estrutural like ''9.%'' OR cod_estrutural like ''4.9%''
                    ';

        EXECUTE stSqlAux;
    
    END LOOP;


    stSql := 'SELECT * FROM tmp_retorno ORDER BY grupo, subgrupo, cod_estrutural ;';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;

    DROP TABLE tmp_receita;
    DROP TABLE tmp_retorno;
    DROP TABLE tmp_receita_despesa;
    DROP TABLE tmp_saldo;
    DROP TABLE tmp_variacao;
    DROP TABLE tmp_despesa;

    RETURN;
END;
$$ LANGUAGE plpgsql
