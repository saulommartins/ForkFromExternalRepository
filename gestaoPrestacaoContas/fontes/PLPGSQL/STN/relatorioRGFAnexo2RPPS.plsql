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

CREATE OR REPLACE FUNCTION stn.fn_rgf_anexo2_rpps(varchar,varchar,integer,varchar) RETURNS SETOF RECORD AS $$
DECLARE

  stExercicio         ALIAS FOR $1;
  stTipoPeriodo       ALIAS FOR $2;
  inPeriodo           ALIAS FOR $3;
  stCodEntidade       ALIAS FOR $4;

  dtInicial           VARCHAR := '';
  arDtFinal           VARCHAR[];
  stExercicioAnterior VARCHAR := '';
  stSql               VARCHAR := '';
  arDescricao         VARCHAR[];
  stOrdem             VARCHAR[];
  arFiltro            VARCHAR[];
  inCondicao          INTEGER := 1;

  vlExercicioAnterior NUMERIC := 0;
  vlPeriodo1          NUMERIC := 0;
  vlPeriodo2          NUMERIC := 0;
  vlPeriodo3          NUMERIC := 0;

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
  arDescricao[1] := 'DÍVIDA CONSOLIDADA PREVIDENCIÁRIA (IV)';
  arDescricao[2] := 'Passivo Atuarial';
  arDescricao[3] := 'Demais Dívidas';
  arDescricao[4] := 'DEDUÇÕES (V)';
  arDescricao[5] := 'Ativo Disponível';
  arDescricao[6] := 'Investimentos';
  arDescricao[7] := 'Haveres Financeiros';
  arDescricao[8] := '(-) Restos a Pagar Processados';
  arDescricao[9] := 'OBRIGAÇÕES NÃO INTEGRANTES DA DC';
  arDescricao[10] := 'DÍVIDA CONSOLIDADA LÍQUIDA PREVIDENCIÁRIA (VI) = (IV-V)';

  -- DEFINE O FILTRO PARA CADA LINHA
  arFiltro[1] := '';
  arFiltro[2] := '( REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE ''''22211%'''' 
                 OR REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE ''''22221%''''
                 OR REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE ''''22212%'''' 
                 OR REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE ''''22222%'''' 
                 OR REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE ''''2121705%'''' 
                 OR REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE ''''21231020203%''''  
                 OR REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE ''''2223''''   
                 OR REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE ''''2224401%'''' 
                 OR REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE ''''22244%''''
                 OR REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE ''''22249000002%'''' 
                 OR REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE ''''22%''''
                    )' ;
  arFiltro[3] := ''; 
  arFiltro[4] := ''; 
  arFiltro[5] := '( REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE ''''111%'''' ) ';
  arFiltro[6] := '( REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE ''''141%'''' ) ';
  arFiltro[7] := '( REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE ''''112%'''' ) '; 
  arFiltro[8] := '( REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE ''''2121102%''''
                  OR REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE ''''212110302%''''
                  OR REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE ''''2121202%''''
                  OR REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE ''''212120302%''''
                  OR REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE ''''21213010002%''''
                  OR REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE ''''21213030002%''''
                  OR REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE ''''21213040002%''''
                   ) ';
  arFiltro[9] := '( REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE ''''2121704%'''' )'; 
  arFiltro[10] := ''; 

  --CRIA UMA TABLE TEMPORARIA  
  stSql := '
    CREATE TEMPORARY TABLE tmp_valores(
       descricao                    varchar(70)
      ,ordem                        integer
      ,valor_exercicio_anterior     numeric(14,2)
      ,valor_1                      numeric(14,2) 
      ,valor_2                      numeric(14,2)
      ,valor_3                      numeric(14,2)
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
                 (  SELECT SUM( stn.pl_saldo_contas(  '''||stExercicioAnterior||'''
                                                    , ''01/01/'||stExercicioAnterior||'''
                                                    , ''31/12/'||stExercicioAnterior||'''
                                                    , '''||arFiltro[i]||'''
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
    -- 3 DEMAIS DÍVIDAS 
    -- 
    INSERT INTO tmp_valores VALUES (arDescricao[3],3,0,0,0,0);

    --
    -- 10 DIVIDA CONSOLIDADA LIQUIDA PREVIDENCIARIA
    --
    INSERT INTO tmp_valores VALUES (arDescricao[10]
                               , 10
                               , ( (SELECT valor_exercicio_anterior FROM tmp_valores WHERE ordem = 1) - (SELECT valor_exercicio_anterior FROM tmp_valores WHERE ordem = 4) )
                               , ( (SELECT valor_1 FROM tmp_valores WHERE ordem = 1) - (SELECT valor_1 FROM tmp_valores WHERE ordem = 4) )
                               , ( (SELECT valor_2 FROM tmp_valores WHERE ordem = 1) - (SELECT valor_2 FROM tmp_valores WHERE ordem = 4) )
                               , ( (SELECT valor_3 FROM tmp_valores WHERE ordem = 1) - (SELECT valor_3 FROM tmp_valores WHERE ordem = 4) )
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

