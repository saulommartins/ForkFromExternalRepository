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

CREATE OR REPLACE FUNCTION stn.fn_rgf_anexo2_detalhamento_divida(varchar,varchar,integer,varchar) RETURNS SETOF RECORD AS $$
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

  vlExercicioAnterior NUMERIC(14,4) := 0.0000;
  vlPeriodo1          NUMERIC(14,4) := 0.0000;
  vlPeriodo2          NUMERIC(14,4) := 0.0000;
  vlPeriodo3          NUMERIC(14,4) := 0.0000;
  inCodEntidadeRPPS   INTEGER := NULL;

  flValorRCL          NUMERIC(14,4) := 0.0000;

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

  --
  -- DESCOBRE A ENTIDADE RPPS
  --
  SELECT valor
    INTO inCodEntidadeRPPS
    FROM administracao.configuracao
   WHERE configuracao.exercicio = stExercicio
     AND parametro = 'cod_entidade_rpps';


  -- DEFINE A DESCRICAO DAS LINHAS
  arDescricao[1] := 'DÍVIDA CONTRATUAL (IV = V + VI + VII + VIII)';
  arDescricao[2] := 'DÍVIDA DE PPP(V)';
  arDescricao[3] := 'PARCELAMENTO DE DÍVIDAS (VI)';
  arDescricao[4] := 'De Tributos';
  arDescricao[5] := 'De Contribuições Sociais';
  arDescricao[6] := 'Previdenciárias';
  arDescricao[7] := 'Demais Contribuições Sociais';
  arDescricao[8] := 'Do FGTS';
  arDescricao[9] := 'Com Instituição Não Financeira';
  arDescricao[10] := 'DÍVIDA COM INSTITUIÇÃO FINANCEIRA (VII)';
  arDescricao[11] := 'Interna';
  arDescricao[12] := 'Externa';
  arDescricao[13] := 'DEMAIS DÍVIDAS CONTRATUAIS (VIII)';

  -- DEFINE O FILTRO PARA CADA LINHA
  arFiltro[1] := ''; 
  arBoFiltroVazio[1] := TRUE;
  arFiltro[2] := '';
  arBoFiltroVazio[2] := TRUE;
  arFiltro[3] := '';
  arBoFiltroVazio[3] := TRUE;

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
                    AND vinculo_contas_rgf_2.cod_conta = 7
                    AND vinculo_contas_rgf_2.timestamp = (SELECT MAX(timestamp)
                                                            FROM stn.vinculo_contas_rgf_2 tbl
                                                           WHERE vinculo_contas_rgf_2.exercicio = tbl.exercicio);
  ';

  FOR reConfiguracao IN EXECUTE stSqlConfiguracao
  LOOP
    stContasConfiguracao := stContasConfiguracao || ' OR REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE '''''||reConfiguracao.estrutural||'%'''' ';
  END LOOP;

  IF stContasConfiguracao <> '' THEN
    arFiltro[4] := '( '||SUBSTR(stContasConfiguracao, 5)||' ) AND lote.cod_entidade <> '||inCodEntidadeRPPS||' ';
    arBoFiltroVazio[4] := FALSE;
  ELSE
    arFiltro[4] := '';
    arBoFiltroVazio[4] := TRUE;
  END IF;
  --------- FIM PEGA CONFIGURAÇÃO

  arFiltro[5] := '';
  arBoFiltroVazio[5] := TRUE;

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
                    AND vinculo_contas_rgf_2.cod_conta = 8
                    AND vinculo_contas_rgf_2.timestamp = (SELECT MAX(timestamp)
                                                            FROM stn.vinculo_contas_rgf_2 tbl
                                                           WHERE vinculo_contas_rgf_2.exercicio = tbl.exercicio);
  ';   
      
  FOR reConfiguracao IN EXECUTE stSqlConfiguracao
  LOOP
    stContasConfiguracao := stContasConfiguracao || ' OR REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE '''''||reConfiguracao.estrutural||'%'''' ';
  END LOOP;

  IF stContasConfiguracao <> '' THEN
    arFiltro[6] := '( '||SUBSTR(stContasConfiguracao, 5)||' ) AND lote.cod_entidade <> '||inCodEntidadeRPPS||' ';
    arBoFiltroVazio[6] := FALSE;
  ELSE
    arFiltro[6] := '';
    arBoFiltroVazio[6] := TRUE;
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
                    AND vinculo_contas_rgf_2.cod_conta = 9
                    AND vinculo_contas_rgf_2.timestamp = (SELECT MAX(timestamp)
                                                            FROM stn.vinculo_contas_rgf_2 tbl
                                                           WHERE vinculo_contas_rgf_2.exercicio = tbl.exercicio);
  ';   
      
  FOR reConfiguracao IN EXECUTE stSqlConfiguracao
  LOOP
    stContasConfiguracao := stContasConfiguracao || ' OR REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE '''''||reConfiguracao.estrutural||'%'''' ';
  END LOOP;

  IF stContasConfiguracao <> '' THEN
    arFiltro[7] := '( '||SUBSTR(stContasConfiguracao, 5)||' ) AND lote.cod_entidade <> '||inCodEntidadeRPPS||' ';
    arBoFiltroVazio[7] := FALSE;
  ELSE
    arFiltro[7] := '';
    arBoFiltroVazio[7] := TRUE;
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
                    AND vinculo_contas_rgf_2.cod_conta = 10
                    AND vinculo_contas_rgf_2.timestamp = (SELECT MAX(timestamp)
                                                            FROM stn.vinculo_contas_rgf_2 tbl
                                                           WHERE vinculo_contas_rgf_2.exercicio = tbl.exercicio);
  ';   

  FOR reConfiguracao IN EXECUTE stSqlConfiguracao
  LOOP
    stContasConfiguracao := stContasConfiguracao || ' OR REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE '''''||reConfiguracao.estrutural||'%'''' ';
  END LOOP;

  IF stContasConfiguracao <> '' THEN
    arFiltro[8] := '( '||SUBSTR(stContasConfiguracao, 5)||' ) AND lote.cod_entidade <> '||inCodEntidadeRPPS||' ';
    arBoFiltroVazio[8] := FALSE;
  ELSE
    arFiltro[8] := '';
    arBoFiltroVazio[8] := TRUE;
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
                    AND vinculo_contas_rgf_2.cod_conta = 11
                    AND vinculo_contas_rgf_2.timestamp = (SELECT MAX(timestamp)
                                                            FROM stn.vinculo_contas_rgf_2 tbl
                                                           WHERE vinculo_contas_rgf_2.exercicio = tbl.exercicio);
  ';   

  FOR reConfiguracao IN EXECUTE stSqlConfiguracao
  LOOP
    stContasConfiguracao := stContasConfiguracao || ' OR REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE '''''||reConfiguracao.estrutural||'%'''' ';
  END LOOP;

  IF stContasConfiguracao <> '' THEN
    arFiltro[9] := '( '||SUBSTR(stContasConfiguracao, 5)||' ) AND lote.cod_entidade <> '||inCodEntidadeRPPS||' ';
    arBoFiltroVazio[9] := FALSE;
  ELSE
    arFiltro[9] := '';
    arBoFiltroVazio[9] := TRUE;
  END IF;
  --------- FIM PEGA CONFIGURAÇÃO

  arFiltro[10] := '';
  arBoFiltroVazio[10] := TRUE;

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
                    AND vinculo_contas_rgf_2.cod_conta = 12
                    AND vinculo_contas_rgf_2.timestamp = (SELECT MAX(timestamp)
                                                            FROM stn.vinculo_contas_rgf_2 tbl
                                                           WHERE vinculo_contas_rgf_2.exercicio = tbl.exercicio);
  ';   

  FOR reConfiguracao IN EXECUTE stSqlConfiguracao
  LOOP
    stContasConfiguracao := stContasConfiguracao || ' OR REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE '''''||reConfiguracao.estrutural||'%'''' ';
  END LOOP;

  IF stContasConfiguracao <> '' THEN
    arFiltro[11] := '( '||SUBSTR(stContasConfiguracao, 5)||' ) AND lote.cod_entidade <> '||inCodEntidadeRPPS||' ';
    arBoFiltroVazio[11] := FALSE;
  ELSE
    arFiltro[11] := '';
    arBoFiltroVazio[11] := TRUE;
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
                    AND vinculo_contas_rgf_2.cod_conta = 13
                    AND vinculo_contas_rgf_2.timestamp = (SELECT MAX(timestamp)
                                                            FROM stn.vinculo_contas_rgf_2 tbl
                                                           WHERE vinculo_contas_rgf_2.exercicio = tbl.exercicio);
  ';   

  FOR reConfiguracao IN EXECUTE stSqlConfiguracao
  LOOP
    stContasConfiguracao := stContasConfiguracao || ' OR REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE '''''||reConfiguracao.estrutural||'%'''' ';
  END LOOP;

  IF stContasConfiguracao <> '' THEN
    arFiltro[12] := '( '||SUBSTR(stContasConfiguracao, 5)||' ) AND lote.cod_entidade <> '||inCodEntidadeRPPS||' ';
    arBoFiltroVazio[12] := FALSE;
  ELSE
    arFiltro[12] := '';
    arBoFiltroVazio[12] := TRUE;
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
                    AND vinculo_contas_rgf_2.cod_conta = 14
                    AND vinculo_contas_rgf_2.timestamp = (SELECT MAX(timestamp)
                                                            FROM stn.vinculo_contas_rgf_2 tbl
                                                           WHERE vinculo_contas_rgf_2.exercicio = tbl.exercicio);
  ';   

  FOR reConfiguracao IN EXECUTE stSqlConfiguracao
  LOOP
    stContasConfiguracao := stContasConfiguracao || ' OR REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE '''''||reConfiguracao.estrutural||'%'''' ';
  END LOOP;

  IF stContasConfiguracao <> '' THEN
    arFiltro[13] := '( '||SUBSTR(stContasConfiguracao, 5)||' ) AND lote.cod_entidade <> '||inCodEntidadeRPPS||' ';
    arBoFiltroVazio[13] := FALSE;
  ELSE
    arFiltro[13] := '';
    arBoFiltroVazio[13] := TRUE;
  END IF;
  --------- FIM PEGA CONFIGURAÇÃO

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
  FOR i IN 1..13 LOOP
--    IF( i BETWEEN 16 AND 18 ) THEN
--      inCondicao := 1;
--    ELSE
      inCondicao := -1;
--    END IF;
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

  END LOOP;

  --
  -- 2 DÍVIDA CONTRATUAL DE PPP
  --
  IF arBoFiltroVazio[2] THEN
      INSERT INTO tmp_valores VALUES (arDescricao[2],2,0,0,0,0);
  END IF;

  IF arBoFiltroVazio[4] THEN
      INSERT INTO tmp_valores VALUES (arDescricao[4],4,0,0,0,0);
  END IF;

  IF arBoFiltroVazio[6] THEN
      INSERT INTO tmp_valores VALUES (arDescricao[6],6,0,0,0,0);
  END IF;

  IF arBoFiltroVazio[7] THEN
      INSERT INTO tmp_valores VALUES (arDescricao[7],7,0,0,0,0);
  END IF;

  IF arBoFiltroVazio[8] THEN
      INSERT INTO tmp_valores VALUES (arDescricao[8],8,0,0,0,0);
  END IF;

  IF arBoFiltroVazio[9] THEN
      INSERT INTO tmp_valores VALUES (arDescricao[9],9,0,0,0,0);
  END IF;

  IF arBoFiltroVazio[11] THEN
      INSERT INTO tmp_valores VALUES (arDescricao[11],11,0,0,0,0);
  END IF;

  IF arBoFiltroVazio[12] THEN
      INSERT INTO tmp_valores VALUES (arDescricao[12],12,0,0,0,0);
  END IF;

  IF arBoFiltroVazio[13] THEN
      INSERT INTO tmp_valores VALUES (arDescricao[13],13,0,0,0,0);
  END IF;

  --
  -- 5 CONTRIBUIÇÕES SOCIAIS
  --
  INSERT INTO tmp_valores SELECT arDescricao[5]
                               , 5
                               , SUM(valor_exercicio_anterior)
                               , SUM(valor_1)
                               , SUM(valor_2)
                               , SUM(valor_3)
                            FROM tmp_valores
                           WHERE ordem IN (6,7);
  --
  -- 3 PARCELAMENTO DE DÍVIDAS
  --
  INSERT INTO tmp_valores SELECT arDescricao[3]
                               , 3
                               , SUM(valor_exercicio_anterior)
                               , SUM(valor_1)
                               , SUM(valor_2)
                               , SUM(valor_3)
                            FROM tmp_valores
                           WHERE ordem IN (4,5,8,9);

  --
  -- 10 DÍVIDA COM INSTITUIÇÃO FINANCEIRA
  --
  INSERT INTO tmp_valores SELECT arDescricao[10]
                               , 10
                               , SUM(valor_exercicio_anterior)
                               , SUM(valor_1)
                               , SUM(valor_2)
                               , SUM(valor_3)
                            FROM tmp_valores
                           WHERE ordem IN (11,12);

  --
  -- 1 DIVIDA CONTRATUAL
  --
  INSERT INTO tmp_valores SELECT arDescricao[1]
                               , 1
                               , SUM(valor_exercicio_anterior)
                               , SUM(valor_1)
                               , SUM(valor_2)
                               , SUM(valor_3)
                            FROM tmp_valores
                           WHERE ordem IN(2,3,10,13);
--  --
--  -- 15 DEDUCOES
--  --
--  INSERT INTO tmp_valores SELECT arDescricao[15]
--                               , 15
--                               , SUM(valor_exercicio_anterior)
--                               , SUM(valor_1)
--                               , SUM(valor_2)
--                               , SUM(valor_3)
--                            FROM tmp_valores
--                           WHERE ordem IN(16,17,18);

--  --
--  -- 21 INSUFICIENCIA FINANCEIRA
--  --
--  SELECT valor_exercicio_anterior
--       , valor_1
--       , valor_2
--       , valor_3
--    INTO vlExercicioAnterior,vlPeriodo1,vlPeriodo2,vlPeriodo3
--    FROM tmp_valores
--   WHERE ordem = 15;
--
--  stSql := '
--    INSERT INTO tmp_valores
--    VALUES (  '''||arDescricao[21]||'''
--            , 21
--  ';
--  IF( vlExercicioAnterior < 0 ) THEN
--    stSql := stSql || '
--            , '||vlExercicioAnterior*-1||'
--    ';
--
--    UPDATE tmp_valores SET valor_exercicio_anterior = NULL WHERE ordem = 15;
--  ELSE
--    stSql := stSql || ',0';
--  END IF;
--
--  IF( vlPeriodo1 < 0 ) THEN
--    stSql := stSql || '
--            , '||vlPeriodo1*-1||'
--    ';            
--    
--    UPDATE tmp_valores SET valor_1 = NULL WHERE ordem = 15;
--  ELSE
--    stSql := stSql || ',0';
--  END IF;
--
--  IF( vlPeriodo2 < 0 ) THEN
--    stSql := stSql || '
--            , '||vlPeriodo2*-1||'
--    ';
--
--    UPDATE tmp_valores SET valor_2 = NULL WHERE ordem = 15;
--  ELSE
--    stSql := stSql || ',0';
--  END IF;
--
--  IF( vlPeriodo3 < 0 ) THEN
--    stSql := stSql || '
--            , '||vlPeriodo3*-1||'
--    ';
--
--    UPDATE tmp_valores SET valor_3 = NULL WHERE ordem = 15;
--  ELSE
--    stSql := stSql || ',0';
--  END IF;
--
--  stSql:= stSql || ' )';
--
--  EXECUTE stSql;


--  -- 
--  -- 22 OUTRAS OBRIGACOES 
--  -- 
--  INSERT INTO tmp_valores VALUES (arDescricao[22],22,0,0,0,0);
--
--  --
--  -- 19 0BRIGACOES NAO INTEGRANTES DA DC
--  --
--  INSERT INTO tmp_valores SELECT arDescricao[19]
--                               , 19
--                               , SUM(valor_exercicio_anterior)
--                               , SUM(valor_1)
--                               , SUM(valor_2)
--                               , SUM(valor_3)
--                            FROM tmp_valores
--                           WHERE ordem IN(20,21,22);
--
--  --
--  -- 23 DIVIDA CONSOLIDADA LIQUIDA
--  --
--  INSERT INTO tmp_valores VALUES (arDescricao[23]
--                               , 23
--                               , ( (SELECT valor_exercicio_anterior FROM tmp_valores WHERE ordem = 1) - (SELECT valor_exercicio_anterior FROM tmp_valores WHERE ordem = 15) )
--                               , ( (SELECT valor_1 FROM tmp_valores WHERE ordem = 1) - (SELECT valor_1 FROM tmp_valores WHERE ordem = 15) )
--                               , ( (SELECT valor_2 FROM tmp_valores WHERE ordem = 1) - (SELECT valor_2 FROM tmp_valores WHERE ordem = 15) )
--                               , ( (SELECT valor_3 FROM tmp_valores WHERE ordem = 1) - (SELECT valor_3 FROM tmp_valores WHERE ordem = 15) )
--                                 );

  --
  -- 24 RECEITA CORRRENTE LIQUIDA - RCL
  --
  -- ## RCL do Bruce | Anexo III - RREO
--  SELECT
--      sum(cast( ( total_mes_1 + total_mes_2 + total_mes_3 + total_mes_4  + total_mes_5  + total_mes_6
--                 + total_mes_7 + total_mes_8 + total_mes_9 + total_mes_10 + total_mes_11 + total_mes_12 ) as numeric(14,2))) as valor
--  INTO vlExercicioAnterior
--  FROM stn.pl_total_subcontas ('31/12/'||stExercicioAnterior) as retorno (
--                                                                ordem      integer
--                                                               ,cod_conta      varchar
--                                                               ,nom_conta      varchar
--                                                               ,cod_estrutural varchar
--                                                               ,mes_1      numeric
--                                                               ,mes_2      numeric
--                                                               ,mes_3      numeric
--                                                               ,mes_4      numeric
--                                                               ,mes_5      numeric
--                                                               ,mes_6      numeric
--                                                               ,mes_7      numeric
--                                                               ,mes_8      numeric
--                                                               ,mes_9      numeric
--                                                               ,mes_10     numeric
--                                                               ,mes_11     numeric
--                                                               ,mes_12     numeric
--                                                               ,total_mes_1  numeric
--                                                               ,total_mes_2  numeric
--                                                               ,total_mes_3  numeric
--                                                               ,total_mes_4  numeric
--                                                               ,total_mes_5  numeric
--                                                               ,total_mes_6  numeric
--                                                               ,total_mes_7  numeric
--                                                               ,total_mes_8  numeric
--                                                               ,total_mes_9  numeric
--                                                               ,total_mes_10 numeric
--                                                               ,total_mes_11 numeric
--                                                               ,total_mes_12 numeric)
--  WHERE ordem = 1;
--        
--  --
--  -- Acrescenta o valor da rcl vinculada ao periodo
--  --
--  SELECT stn.fn_calcula_rcl_vinculada(stExercicio,'01/01/' || stExercicio,stCodEntidade)
--    INTO flValorRCL;
--
--  vlExercicioAnterior := vlExercicioAnterior + flValorRCL;
--
--  SELECT
--      sum(cast( ( total_mes_1 + total_mes_2 + total_mes_3 + total_mes_4  + total_mes_5  + total_mes_6
--                 + total_mes_7 + total_mes_8 + total_mes_9 + total_mes_10 + total_mes_11 + total_mes_12 ) as numeric(14,2))) as valor
--  INTO vlPeriodo1
--  FROM stn.pl_total_subcontas (arDtFinal[1]) as retorno (
--                                                                ordem      integer
--                                                               ,cod_conta      varchar
--                                                               ,nom_conta      varchar
--                                                               ,cod_estrutural varchar
--                                                               ,mes_1      numeric
--                                                               ,mes_2      numeric
--                                                               ,mes_3      numeric
--                                                               ,mes_4      numeric
--                                                               ,mes_5      numeric
--                                                               ,mes_6      numeric
--                                                               ,mes_7      numeric
--                                                               ,mes_8      numeric
--                                                               ,mes_9      numeric
--                                                               ,mes_10     numeric
--                                                               ,mes_11     numeric
--                                                               ,mes_12     numeric
--                                                               ,total_mes_1  numeric
--                                                               ,total_mes_2  numeric
--                                                               ,total_mes_3  numeric
--                                                               ,total_mes_4  numeric
--                                                               ,total_mes_5  numeric
--                                                               ,total_mes_6  numeric
--                                                               ,total_mes_7  numeric
--                                                               ,total_mes_8  numeric
--                                                               ,total_mes_9  numeric
--                                                               ,total_mes_10 numeric
--                                                               ,total_mes_11 numeric
--                                                               ,total_mes_12 numeric)
--  WHERE ordem = 1;
--
--  --
--  -- Acrescenta o valor da rcl vinculada ao periodo
--  --
--  SELECT stn.fn_calcula_rcl_vinculada(stExercicio,arDtFinal[1],stCodEntidade)
--    INTO flValorRCL;
--
--  vlPeriodo1 := vlPeriodo1 + flValorRCL;
--  
--  IF(inPeriodo>1) THEN
--  SELECT
--      sum(cast( ( total_mes_1 + total_mes_2 + total_mes_3 + total_mes_4  + total_mes_5  + total_mes_6
--                 + total_mes_7 + total_mes_8 + total_mes_9 + total_mes_10 + total_mes_11 + total_mes_12 ) as numeric(14,2))) as valor
--  INTO vlPeriodo2
--  FROM stn.pl_total_subcontas (arDtFinal[2]) as retorno (
--                                                                ordem      integer
--                                                               ,cod_conta      varchar
--                                                               ,nom_conta      varchar
--                                                               ,cod_estrutural varchar
--                                                               ,mes_1      numeric
--                                                               ,mes_2      numeric
--                                                               ,mes_3      numeric
--                                                               ,mes_4      numeric
--                                                               ,mes_5      numeric
--                                                               ,mes_6      numeric
--                                                               ,mes_7      numeric
--                                                               ,mes_8      numeric
--                                                               ,mes_9      numeric
--                                                               ,mes_10     numeric
--                                                               ,mes_11     numeric
--                                                               ,mes_12     numeric
--                                                               ,total_mes_1  numeric
--                                                               ,total_mes_2  numeric
--                                                               ,total_mes_3  numeric
--                                                               ,total_mes_4  numeric
--                                                               ,total_mes_5  numeric
--                                                               ,total_mes_6  numeric
--                                                               ,total_mes_7  numeric
--                                                               ,total_mes_8  numeric
--                                                               ,total_mes_9  numeric
--                                                               ,total_mes_10 numeric
--                                                               ,total_mes_11 numeric
--                                                               ,total_mes_12 numeric)
--  WHERE ordem = 1;
--
--  --
--  -- Acrescenta o valor da rcl vinculada ao periodo
--  --
--  SELECT stn.fn_calcula_rcl_vinculada(stExercicio,arDtFinal[2],stCodEntidade)
--    INTO flValorRCL;
--
--  vlPeriodo2 := vlPeriodo2 + flValorRCL;
--
--  ELSE
--    vlPeriodo2 := 0;
--  END IF;
--
--  IF(inPeriodo>2) THEN
--  SELECT
--      sum(cast( ( total_mes_1 + total_mes_2 + total_mes_3 + total_mes_4  + total_mes_5  + total_mes_6
--                 + total_mes_7 + total_mes_8 + total_mes_9 + total_mes_10 + total_mes_11 + total_mes_12 ) as numeric(14,2))) as valor
--  INTO vlPeriodo3
--  FROM stn.pl_total_subcontas (arDtFinal[3]) as retorno (
--                                                                ordem      integer
--                                                               ,cod_conta      varchar
--                                                               ,nom_conta      varchar
--                                                               ,cod_estrutural varchar
--                                                               ,mes_1      numeric
--                                                               ,mes_2      numeric
--                                                               ,mes_3      numeric
--                                                               ,mes_4      numeric
--                                                               ,mes_5      numeric
--                                                               ,mes_6      numeric
--                                                               ,mes_7      numeric
--                                                               ,mes_8      numeric
--                                                               ,mes_9      numeric
--                                                               ,mes_10     numeric
--                                                               ,mes_11     numeric
--                                                               ,mes_12     numeric
--                                                               ,total_mes_1  numeric
--                                                               ,total_mes_2  numeric
--                                                               ,total_mes_3  numeric
--                                                               ,total_mes_4  numeric
--                                                               ,total_mes_5  numeric
--                                                               ,total_mes_6  numeric
--                                                               ,total_mes_7  numeric
--                                                               ,total_mes_8  numeric
--                                                               ,total_mes_9  numeric
--                                                               ,total_mes_10 numeric
--                                                               ,total_mes_11 numeric
--                                                               ,total_mes_12 numeric)
--  WHERE ordem = 1;
--
--  --
--  -- Acrescenta o valor da rcl vinculada ao periodo
--  --
--  SELECT stn.fn_calcula_rcl_vinculada(stExercicio,arDtFinal[3],stCodEntidade)
--    INTO flValorRCL;
--
--  vlPeriodo3 := vlPeriodo3 + flValorRCL;
--  ELSE
--    vlPeriodo3 := 0;
--
--  END IF;
--
--  INSERT INTO tmp_valores VALUES( arDescricao[24],24,vlExercicioAnterior,vlPeriodo1,vlPeriodo2,vlPeriodo3 );
--
--  --
--  -- 25 DC SOBRE A RCL
--  --
--  SELECT CASE WHEN(vlExercicioAnterior <> 0) THEN 
--         ROUND( ( SELECT valor_exercicio_anterior FROM tmp_valores WHERE ordem = 1 )
--           /
--           ( SELECT valor_exercicio_anterior FROM tmp_valores WHERE ordem = 24 )
--         ,4)*100 
--         ELSE 0
--         END AS valor_exercicio_anterior
--       , CASE WHEN(vlPeriodo1 <> 0) THEN
--         ROUND( ( SELECT valor_1 FROM tmp_valores WHERE ordem = 1 )
--           /
--           ( SELECT valor_1 FROM tmp_valores WHERE ordem = 24 )
--         ,4)*100
--         ELSE 0
--         END AS valor_1
--       , CASE WHEN(vlPeriodo2 <> 0) THEN
--         ROUND( ( SELECT valor_2 FROM tmp_valores WHERE ordem = 1 )
--           /
--           ( SELECT valor_2 FROM tmp_valores WHERE ordem = 24 )
--         ,4)*100
--         ELSE 0
--         END AS valor_2
--       , CASE WHEN(vlPeriodo3 <> 0) THEN
--         ROUND( ( SELECT valor_3 FROM tmp_valores WHERE ordem = 1 )
--           /
--           ( SELECT valor_3 FROM tmp_valores WHERE ordem = 24 )
--         ,4)*100 
--         ELSE 0
--         END AS valor_3
--    INTO vlExercicioAnterior, vlPeriodo1, vlPeriodo2, vlPeriodo3;
--
--  INSERT INTO tmp_valores VALUES ('% da DC sobre a RCL (I/RCL)',25,vlExercicioAnterior,vlPeriodo1,vlPeriodo2,vlPeriodo3);
--
--  --
--  -- 26 DCL sobre a RCL (III/RCL)
--  --
--  SELECT CASE WHEN(vlExercicioAnterior <> 0) THEN
--         ROUND( ( SELECT valor_exercicio_anterior FROM tmp_valores WHERE ordem = 23 )
--           /
--           ( SELECT valor_exercicio_anterior FROM tmp_valores WHERE ordem = 24 )
--         ,4)*100 
--         ELSE 0
--         END AS valor_exercicio_anterior
--       , CASE WHEN(vlPeriodo1 <> 0) THEN
--         ROUND( ( SELECT valor_1 FROM tmp_valores WHERE ordem = 23 )
--           /
--           ( SELECT valor_1 FROM tmp_valores WHERE ordem = 24 )
--         ,4)*100
--         ELSE 0
--         END AS valor_1
--       , CASE WHEN(vlPeriodo2 <> 0) THEN
--         ROUND( ( SELECT valor_2 FROM tmp_valores WHERE ordem = 23 )
--           /
--           ( SELECT valor_2 FROM tmp_valores WHERE ordem = 24 )
--         ,4)*100 
--         ELSE 0
--         END AS valor_2
--       , CASE WHEN(vlPeriodo3 <> 0) THEN
--         ROUND( ( SELECT valor_3 FROM tmp_valores WHERE ordem = 23 )
--           /
--           ( SELECT valor_3 FROM tmp_valores WHERE ordem = 24 )
--         ,4)*100
--         ELSE 0
--         END AS valor_3
--    INTO vlExercicioAnterior, vlPeriodo1, vlPeriodo2, vlPeriodo3;
--
--  INSERT INTO tmp_valores VALUES ('% da DCL sobre a RCL (III/RCL)',26,vlExercicioAnterior,vlPeriodo1,vlPeriodo2,vlPeriodo3);
--
--  --
--  -- 27 LIMITE DEFINIDO POR RESOLUCAO
--  --
--  INSERT INTO tmp_valores VALUES (  'LIMITE DEFINIDO POR RESOLUÇÃO Nº 40/01 DO SENADO FEDERAL - 120%'
--                                  , 27
--                                  , (SELECT ROUND((valor_exercicio_anterior*120)/100,2) FROM tmp_valores WHERE ordem = 24)
--                                  , (SELECT ROUND((valor_1*120)/100,4) FROM tmp_valores WHERE ordem = 24)
--                                  , (SELECT ROUND((valor_2*120)/100,4) FROM tmp_valores WHERE ordem = 24)
--                                  , (SELECT ROUND((valor_3*120)/100,4) FROM tmp_valores WHERE ordem = 24)
--                                 );
--
  --
  -- FAZ OS UPDATES DOS NIVEIS
  -- 
  UPDATE tmp_valores SET nivel = 0 WHERE ordem IN (1,2,3,10,13);
  UPDATE tmp_valores SET nivel = 1 WHERE ordem IN (4,5,8,9,11,12);
  UPDATE tmp_valores SET nivel = 2 WHERE ordem IN (6,7);

  stSql := 'SELECT * FROM tmp_valores ORDER BY ordem';

  FOR reRegistro IN EXECUTE stSql
  LOOP
      RETURN next reRegistro;
  END LOOP;

  DROP TABLE tmp_valores;

END;

$$ language 'plpgsql';

