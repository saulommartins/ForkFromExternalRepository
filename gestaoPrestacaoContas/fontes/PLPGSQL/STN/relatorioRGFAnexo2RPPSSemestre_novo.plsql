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

* Casos de uso: uc-06.01.02
*/

CREATE OR REPLACE FUNCTION stn.fn_rgf_anexo2_rpps_semestre_novo(varchar,varchar,integer,varchar) RETURNS SETOF RECORD AS $$
DECLARE

  stExercicio         ALIAS FOR $1;
  stTipoPeriodo       ALIAS FOR $2;
  inPeriodo           ALIAS FOR $3;
  stCodEntidade       ALIAS FOR $4;

  dtInicial           VARCHAR := '';
  arDtFinal           VARCHAR[];
  stExercicioAnterior VARCHAR := '';
  stSql               VARCHAR := '';
  stSqlConfiguracao   VARCHAR := '';
  stContasConfiguracao VARCHAR := '';
  arDescricao         VARCHAR[];
  stOrdem             VARCHAR[];
  arFiltro            VARCHAR[];
  arBoFiltroVazio     BOOLEAN[];
  inCondicao          INTEGER := 1;

  vlExercicioAnterior NUMERIC := 0;
  vlPeriodo1          NUMERIC := 0;
  vlPeriodo2          NUMERIC := 0;
  vlPeriodo3          NUMERIC := 0;

  reConfiguracao      RECORD;
  reRegistro          RECORD;

BEGIN
  dtInicial := '01/01/'||stExercicio||'';

  IF( stTipoPeriodo = 'Quadrimestre' ) THEN
    arDtFinal[1] := '30/04/'||stExercicio||'';
    arDtFinal[2] := '31/08/'||stExercicio||'';
    arDtFinal[3] := '31/12/'||stExercicio||'';
  ELSE 
    arDtFinal[1] := '30/06/'||stExercicio||'';
    arDtFinal[2] := '31/12/'||stExercicio||'';
    arDtFinal[3] := '';
  END IF;

  stExercicioAnterior := trim(to_char((to_number(stExercicio,'9999')-1),'9999'));

  -- DEFINE A DESCRICAO DAS LINHAS
  arDescricao[1] := 'DÍVIDA CONSOLIDADA PREVIDENCIÁRIA (IX)';
  arDescricao[2] := 'Passivo Atuarial';
  arDescricao[3] := 'Demais Dívidas';
  arDescricao[4] := 'DEDUÇÕES (X)';
  arDescricao[5] := 'Disponibilidade de Caixa Bruta';
  arDescricao[6] := 'Investimentos';
  arDescricao[7] := 'Demais Haveres Financeiros';
  arDescricao[8] := '(-) Restos a Pagar Processados';
  arDescricao[9] := 'OBRIGAÇÕES NÃO INTEGRANTES DA DC';
  arDescricao[10] := 'DÍVIDA CONSOLIDADA LÍQUIDA PREVIDENCIÁRIA (XI) = (IX-X)';

  -- DEFINE O FILTRO PARA CADA LINHA
  arFiltro[1] := '';
  arBoFiltroVazio[1] := TRUE;
  
  -- PEGA CONFIGURAÇÃO PARA A LINHA
  stContasConfiguracao := '';
  stSqlConfiguracao := '
                 SELECT REPLACE(publico.fn_mascarareduzida(cod_estrutural),''.'','''') as estrutural
                      , vinculo_contas_rgf_2.exercicio
                   FROM stn.vinculo_contas_rgf_2
             INNER JOIN contabilidade.plano_analitica
                     ON vinculo_contas_rgf_2.cod_plano = plano_analitica.cod_plano
                    AND vinculo_contas_rgf_2.exercicio = plano_analitica.exercicio
             INNER JOIN contabilidade.plano_conta
                     ON plano_analitica.cod_conta = plano_conta.cod_conta
                    AND plano_analitica.exercicio = plano_conta.exercicio
                  WHERE vinculo_contas_rgf_2.exercicio = '''||stExercicio||'''
                    AND vinculo_contas_rgf_2.cod_conta = 18
                    AND vinculo_contas_rgf_2.timestamp = (SELECT MAX(timestamp)
                                                            FROM stn.vinculo_contas_rgf_2 tbl
                                                           WHERE vinculo_contas_rgf_2.exercicio = tbl.exercicio);
  ';

  FOR reConfiguracao IN EXECUTE stSqlConfiguracao
  LOOP
    stContasConfiguracao := stContasConfiguracao || ' OR REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE '''''||reConfiguracao.estrutural||'%'''' ';
  END LOOP;

  IF stContasConfiguracao <> '' THEN
    arFiltro[2] := '( '||SUBSTR(stContasConfiguracao, 5)||' ) ';
    arBoFiltroVazio[2] := FALSE;
  ELSE
    arFiltro[2] := '';
    arBoFiltroVazio[2] := TRUE;
  END IF;
  --------- FIM PEGA CONFIGURAÇÃO

  -- PEGA CONFIGURAÇÃO PARA A LINHA
  stContasConfiguracao := '';
  stSqlConfiguracao := '
                 SELECT REPLACE(publico.fn_mascarareduzida(cod_estrutural),''.'','''') as estrutural
                      , vinculo_contas_rgf_2.exercicio
                   FROM stn.vinculo_contas_rgf_2
             INNER JOIN contabilidade.plano_analitica
                     ON vinculo_contas_rgf_2.cod_plano = plano_analitica.cod_plano
                    AND vinculo_contas_rgf_2.exercicio = plano_analitica.exercicio
             INNER JOIN contabilidade.plano_conta
                     ON plano_analitica.cod_conta = plano_conta.cod_conta
                    AND plano_analitica.exercicio = plano_conta.exercicio
                  WHERE vinculo_contas_rgf_2.exercicio = '''||stExercicio||'''
                    AND vinculo_contas_rgf_2.cod_conta = 19
                    AND vinculo_contas_rgf_2.timestamp = (SELECT MAX(timestamp)
                                                            FROM stn.vinculo_contas_rgf_2 tbl
                                                           WHERE vinculo_contas_rgf_2.exercicio = tbl.exercicio);
  ';

  FOR reConfiguracao IN EXECUTE stSqlConfiguracao
  LOOP
    stContasConfiguracao := stContasConfiguracao || ' OR REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE '''''||reConfiguracao.estrutural||'%'''' ';
  END LOOP;

  IF stContasConfiguracao <> '' THEN
    arFiltro[3] := '( '||SUBSTR(stContasConfiguracao, 5)||' ) ';
    arBoFiltroVazio[3] := FALSE;
  ELSE
    arFiltro[3] := '';
    arBoFiltroVazio[3] := TRUE;
  END IF;
  --------- FIM PEGA CONFIGURAÇÃO

  arFiltro[4] := ''; 
  arBoFiltroVazio[4] := TRUE;
  
  arFiltro[5] := '( REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE ''''11111%'''' ) AND NOT ( REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE ''''1111150%'''' ) ';
  arBoFiltroVazio[5] := FALSE;
  
  arFiltro[6] := '( REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE ''''1111150%'''' ) OR ( REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE ''''114%'''' ) ';
  arBoFiltroVazio[6] := FALSE;
  
  arFiltro[7] := '( REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE ''''112%'''' ) '; 
  arBoFiltroVazio[7] := FALSE;
  
  arFiltro[8] := '';
  arBoFiltroVazio[8] := TRUE;

  arFiltro[9] := '';
  arBoFiltroVazio[9] := TRUE;

  arFiltro[10] := '';
  arBoFiltroVazio[10] := TRUE;

  --CRIA UMA TABLE TEMPORARIA  
  stSql := '
    CREATE TEMPORARY TABLE tmp_valores(
       descricao                    varchar(70)
      ,ordem                        integer
      ,valor_exercicio_anterior     numeric
      ,valor_1                      numeric 
      ,valor_2                      numeric
      ,valor_3                      numeric
      ,nivel                        integer
    ) ';

  EXECUTE stSql;

  -- LOOP PARA EXECUTAR AS CONSULTAS E INSERIR OS RESULTADOS NA TABELA TEMPORARIA
  FOR i IN 1..10 LOOP
    IF( stCodEntidade != '' ) THEN
      IF( i BETWEEN 5 AND 8 ) THEN
        inCondicao := 1;
      ELSE
        inCondicao := -1;
      END IF;
      
      IF(arFiltro[i] != '') THEN
        stSql := '
          SELECT 
                 (  SELECT SUM( stn.pl_saldo_contas(  '''||stExercicio||'''
                                                  , '''||dtInicial||'''
                                                  , '''||arDtFinal[1]||'''
                                                  , '''||arFiltro[i]||' AND lote.tipo = ''''I'''' '||'''
                                                  , '''||stCodEntidade||'''
                                                 ) * '||inCondicao||'
                             )
               ) AS exercicio_anterior
               , (  SELECT SUM( stn.pl_saldo_contas(  '''||stExercicio||'''
                                                    , '''||dtInicial||'''
                                                    , '''||arDtFinal[1]||'''
                                                    , '''||arFiltro[i]||'''
                                                    , '''||stCodEntidade||'''
                                                   ) * '||inCondicao||'
                               )  
                 ) AS valor_1
        ';
        
        IF(inPeriodo>=2 ) THEN
          stSql := stSql || '
                , (  SELECT SUM( stn.pl_saldo_contas(  '''||stExercicio||'''
                                                    , '''||dtInicial||'''
                                                    , '''||arDtFinal[2]||'''
                                                    , '''||arFiltro[i]||'''
                                                    , '''||stCodEntidade||'''
                                                   ) * '||inCondicao||' 
                               )  
                 ) AS valor_2
          ';
        ELSE
          stSql := stSql || '
                 , CAST(0 AS numeric) AS valor_2
          ';
        END IF;
  
        IF(inPeriodo>=3) THEN
          stSql := stSql || '
                , (  SELECT SUM( stn.pl_saldo_contas(  '''||stExercicio||'''
                                                    , '''||dtInicial||'''
                                                    , '''||arDtFinal[3]||'''
                                                    , '''||arFiltro[i]||'''
                                                    , '''||stCodEntidade||'''
                                                   ) * '||inCondicao||'
                               )  
                 ) AS valor_3
          ';
        ELSE
          stSql := stSql || '
                 , CAST(0 AS numeric) AS valor_3
          ';
        END IF;
        
        FOR reRegistro IN EXECUTE stSql
        LOOP
           stSql := ' INSERT INTO tmp_valores 
                      VALUES(  '''||arDescricao[i]||'''
                             , '||i||'
                             , '||reRegistro.exercicio_anterior||'
                             , '||reRegistro.valor_1||'
                             , '||reRegistro.valor_2||' 
                             ,'||reRegistro.valor_3||'
                             )
             ';
  
           EXECUTE stSql;
        END LOOP;
      END IF;
    
    ELSE
      INSERT INTO tmp_valores VALUES( arDescricao[i],i,0,0,0,0);
    END IF;

  END LOOP;

  IF( stCodEntidade != '' ) THEN
    IF arBoFiltroVazio[2] THEN
        INSERT INTO tmp_valores VALUES( arDescricao[2],2,0,0,0,0);
    END IF;

    IF arBoFiltroVazio[3] THEN
        INSERT INTO tmp_valores VALUES( arDescricao[3],3,0,0,0,0);
    END IF;

    IF arBoFiltroVazio[8] THEN
        
        stSql := '
            SELECT
                ( SELECT SUM(liquidadoapagar)
                         FROM empenho.fn_relatorio_situacao_restos_pagar ( '''||stExercicio||'''
                                                                         , '''||stCodEntidade||'''
                                                                         , '''||dtInicial||'''
                                                                         , '''||arDtFinal[1]||'''
                                                                         , ''''
                                                                         , ''''
                                                                         ) AS rp
                                                                         ( cod_empenho              INTEGER,
                                                                         cod_entidade               INTEGER,
                                                                         exercicio                  VARCHAR,
                                                                         credor                     VARCHAR,
                                                                         emissao                    TEXT,
                                                                         vencimento                 TEXT,
                                                                         empenhado                  NUMERIC(14,2),
                                                                         aliquidar                  NUMERIC(14,2),
                                                                         liquidadoapagar            NUMERIC(14,2),
                                                                         anulado                    NUMERIC(14,2),
                                                                         liquidado                  NUMERIC(14,2),
                                                                         pagamento                  NUMERIC(14,2),
                                                                         empenhado_saldo            NUMERIC(14,2),
                                                                         aliquidar_saldo            NUMERIC(14,2),
                                                                         liquidadoapagar_saldo      NUMERIC(14,2)) ) AS exercicio_anterior ';
        
        FOR i IN 1..3 LOOP
            IF(inPeriodo>=i ) THEN
                stSql := stSql || '
                       , ( SELECT SUM(liquidadoapagar_saldo)
                                 FROM empenho.fn_relatorio_situacao_restos_pagar ( '''||stExercicio||'''
                                                                                 , '''||stCodEntidade||'''
                                                                                 , '''||dtInicial||'''
                                                                                 , '''||arDtFinal[i]||'''
                                                                                 , ''''
                                                                                 , ''''
                                                                                 ) AS rp
                                                                                 ( cod_empenho              INTEGER,
                                                                                 cod_entidade               INTEGER,
                                                                                 exercicio                  VARCHAR,
                                                                                 credor                     VARCHAR,
                                                                                 emissao                    TEXT,
                                                                                 vencimento                 TEXT,
                                                                                 empenhado                  NUMERIC(14,2),
                                                                                 aliquidar                  NUMERIC(14,2),
                                                                                 liquidadoapagar            NUMERIC(14,2),
                                                                                 anulado                    NUMERIC(14,2),
                                                                                 liquidado                  NUMERIC(14,2),
                                                                                 pagamento                  NUMERIC(14,2),
                                                                                 empenhado_saldo            NUMERIC(14,2),
                                                                                 aliquidar_saldo            NUMERIC(14,2),
                                                                                 liquidadoapagar_saldo      NUMERIC(14,2))) AS valor_'||i::VARCHAR;
            ELSE
                 stSql := stSql || ' , 0.00 AS valor_'||i::VARCHAR;
            END IF;
        END LOOP;
        
        FOR reRegistro IN EXECUTE stSql
        LOOP
            stSql := ' INSERT INTO tmp_valores 
                        VALUES(  '''||arDescricao[8]||'''
                               , '||8||'
                               , '||reRegistro.exercicio_anterior||'
                               , '||reRegistro.valor_1||'
                               , '||reRegistro.valor_2||' 
                               , '||reRegistro.valor_3||'
                      )';
    
            EXECUTE stSql;
        END LOOP;
    END IF;

    IF arBoFiltroVazio[9] THEN
        stSql := '
            SELECT
              SUM(valor_exercicio_anterior) AS exercicio_anterior
            , SUM(valor_quadrimestre_1) AS valor_1
            , SUM(valor_quadrimestre_2) AS valor_2
            , SUM(valor_quadrimestre_3) AS valor_3
        
            FROM (
                SELECT descricao  
                    , ordem 
                    , valor_exercicio_anterior  
                    , valor_quadrimestre_1  
                    , valor_quadrimestre_2  
                    , valor_quadrimestre_3 
                    , nivel 
                FROM stn.fn_rgf_anexo2_valores_nao_integrantes_rpps('''||stExercicio||''', '''||stTipoPeriodo||''', '||inPeriodo||', '''||stCodEntidade||''') as tbl  
                    (  descricao varchar 
                        , ordem integer 
                        , valor_exercicio_anterior numeric 
                        , valor_quadrimestre_1 numeric 
                        , valor_quadrimestre_2 numeric 
                        , valor_quadrimestre_3 numeric 
                        , nivel integer 
                    ) 
        ) AS obrigacoes_nao_integrantes';
    
        FOR reRegistro IN EXECUTE stSql
        LOOP
           stSql := ' INSERT INTO tmp_valores 
                      VALUES(  '''||arDescricao[9]||'''
                             , '||9||'
                             , '||reRegistro.exercicio_anterior||'
                             , '||reRegistro.valor_1||'
                             , '||reRegistro.valor_2||' 
                             ,'||reRegistro.valor_3||'
                             )
             ';
  
           EXECUTE stSql;
        END LOOP;
    END IF;

    --
    -- 1 DIVIDA CONSOLIDADA PREVIDENCIÁRIA
    --
    INSERT INTO tmp_valores SELECT arDescricao[1]
                                 , 1
                                 , SUM(valor_exercicio_anterior)
                                 , SUM(valor_1)
                                 , SUM(valor_2)
                                 , SUM(valor_3)
                              FROM tmp_valores
                             WHERE ordem IN(2);
  
    --
    -- 4 DEDUCOES
    --
    INSERT INTO tmp_valores SELECT arDescricao[4]
                                 , 4
                                 , SUM(valor_exercicio_anterior)
                                 , SUM(valor_1)
                                 , SUM(valor_2)
                                 , SUM(valor_3)
                              FROM tmp_valores
                             WHERE ordem IN(5,6,7,8);
  
    --
    -- 10 DIVIDA CONSOLIDADA LIQUIDA PREVIDENCIARIA
    --
    INSERT INTO tmp_valores VALUES (arDescricao[10]
                               , 10
                               , ( (SELECT SUM(valor_exercicio_anterior) FROM tmp_valores WHERE ordem = 1) - (SELECT SUM(valor_exercicio_anterior) FROM tmp_valores WHERE ordem = 4) )
                               , ( (SELECT SUM(valor_1) FROM tmp_valores WHERE ordem = 1) - (SELECT SUM(valor_1) FROM tmp_valores WHERE ordem = 4) )
                               , ( (SELECT SUM(valor_2) FROM tmp_valores WHERE ordem = 1) - (SELECT SUM(valor_2) FROM tmp_valores WHERE ordem = 4) )
                               , ( (SELECT SUM(valor_3) FROM tmp_valores WHERE ordem = 1) - (SELECT SUM(valor_3) FROM tmp_valores WHERE ordem = 4) )
                                 );

  END If;
      
  --
  -- FAZ OS UPDATES DOS NIVEIS
  --
  UPDATE tmp_valores SET nivel = 0 WHERE ordem IN (1,4,9,10);
  UPDATE tmp_valores SET nivel = 1 WHERE ordem IN (2,3,5,6,7,8);

  stSql := 'SELECT * FROM tmp_valores ORDER BY ordem';

  FOR reRegistro IN EXECUTE stSql
  LOOP
      RETURN next reRegistro;
  END LOOP;

  DROP TABLE tmp_valores;

END;

$$ language 'plpgsql';

