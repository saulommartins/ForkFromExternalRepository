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
* $Id: relatorioRGFAnexo2RPPS_novo_mensal.plsql 62608 2015-05-22 19:35:48Z evandro $

* Casos de uso: uc-06.01.02
*/

CREATE OR REPLACE FUNCTION stn.fn_rgf_anexo2_rpps_novo_mensal(varchar,varchar,integer,varchar) RETURNS SETOF RECORD AS $$
DECLARE

  stExercicio         ALIAS FOR $1; 
  stTipoPeriodo       ALIAS FOR $2;
  inPeriodo           ALIAS FOR $3;
  stCodEntidade       ALIAS FOR $4;

  dtInicial            VARCHAR := '';
  arDtFinal            VARCHAR[];
  stExercicioAnterior  VARCHAR := '';
  stSql                VARCHAR := '';
  stSqlConfiguracao    VARCHAR := '';
  stContasConfiguracao VARCHAR := '';
  inCodEntidadeRPPS    INTEGER := 0;
  arDescricao          VARCHAR[];
  stOrdem              VARCHAR[];
  arFiltro             VARCHAR[];
  arBoFiltroVazio      BOOLEAN[];
  arDatas              VARCHAR[];
  inCondicao           INTEGER := 1;

  reConfiguracao      RECORD;
  reRegistro          RECORD;

BEGIN

  IF( stTipoPeriodo = 'Mes' ) THEN
    arDatas := publico.mes(stExercicio,inPeriodo);
    dtInicial := arDatas[0];    
    arDtFinal[1] := arDatas[1];
  ELSEIF (stTipoPeriodo = 'Ano') THEN
    dtInicial := '01/01/'||stExercicio||'';
    arDtFinal[1] := '31/12/'||stExercicio||'';
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
  arFiltro[5] := '( REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE ''''111%'''' ) ';
  arBoFiltroVazio[5] := FALSE;
  arFiltro[6] := '( REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE ''''141%'''' ) ';
  arBoFiltroVazio[6] := FALSE;
  arFiltro[7] := '( REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE ''''112%'''' ) '; 
  arBoFiltroVazio[7] := FALSE;

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
                    AND vinculo_contas_rgf_2.cod_conta = 20
                    AND vinculo_contas_rgf_2.timestamp = (SELECT MAX(timestamp)
                                                            FROM stn.vinculo_contas_rgf_2 tbl
                                                           WHERE vinculo_contas_rgf_2.exercicio = tbl.exercicio);
  ';

  FOR reConfiguracao IN EXECUTE stSqlConfiguracao
  LOOP
    stContasConfiguracao := stContasConfiguracao || ' OR REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE '''''||reConfiguracao.estrutural||'%'''' ';
  END LOOP;

  IF stContasConfiguracao <> '' THEN
    arFiltro[8] := '( '||SUBSTR(stContasConfiguracao, 5)||' ) ';
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
                    AND vinculo_contas_rgf_2.cod_conta = 21
                    AND vinculo_contas_rgf_2.timestamp = (SELECT MAX(timestamp)
                                                            FROM stn.vinculo_contas_rgf_2 tbl
                                                           WHERE vinculo_contas_rgf_2.exercicio = tbl.exercicio);
  ';

  FOR reConfiguracao IN EXECUTE stSqlConfiguracao
  LOOP
    stContasConfiguracao := stContasConfiguracao || ' OR REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE '''''||reConfiguracao.estrutural||'%'''' ';
  END LOOP;

  IF stContasConfiguracao <> '' THEN
    arFiltro[9] := '( '||SUBSTR(stContasConfiguracao, 5)||' ) ';
    arBoFiltroVazio[9] := FALSE;
  ELSE
    arFiltro[9] := '';
    arBoFiltroVazio[9] := TRUE;
  END IF;
  --------- FIM PEGA CONFIGURAÇÃO

  arFiltro[10] := '';
  arBoFiltroVazio[10] := TRUE;

  --CRIA UMA TABLE TEMPORARIA  
  stSql := '
    CREATE TEMPORARY TABLE tmp_valores(
       descricao                    varchar(70)
      ,ordem                        integer
      ,valor_exercicio_anterior     numeric
      ,valor_mes                    numeric 
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
                 ) AS valor_mes
        ';
        
  
        FOR reRegistro IN EXECUTE stSql
        LOOP
           stSql := ' INSERT INTO tmp_valores 
                      VALUES(  '''||arDescricao[i]||'''
                             , '||i||'
                             , '||reRegistro.exercicio_anterior||'
                             , '||reRegistro.valor_mes||'
                             )
             ';
  
           EXECUTE stSql;
        END LOOP;
      END IF;
    
    ELSE
      INSERT INTO tmp_valores VALUES( arDescricao[i],i,0,0);
    END IF;

  END LOOP;

  IF( stCodEntidade != '' ) THEN
    IF arBoFiltroVazio[2] THEN
        INSERT INTO tmp_valores VALUES( arDescricao[2],2,0,0);
    END IF;

    IF arBoFiltroVazio[3] THEN
        INSERT INTO tmp_valores VALUES( arDescricao[3],3,0,0);
    END IF;

    IF arBoFiltroVazio[8] THEN
        INSERT INTO tmp_valores VALUES( arDescricao[8],8,0,0);
    END IF;

    IF arBoFiltroVazio[9] THEN
        INSERT INTO tmp_valores VALUES( arDescricao[9],9,0,0);
    END IF;

    --
    -- 1 DIVIDA CONSOLIDADA PREVIDENCIÁRIA
    --
    INSERT INTO tmp_valores SELECT arDescricao[1]
                                 , 1
                                 , SUM(valor_exercicio_anterior)
                                 , SUM(valor_mes)
                              FROM tmp_valores
                             WHERE ordem IN(2);
  
    --
    -- 4 DEDUCOES
    --
    INSERT INTO tmp_valores SELECT arDescricao[4]
                                 , 4
                                 , SUM(valor_exercicio_anterior)
                                 , SUM(valor_mes)
                              FROM tmp_valores
                             WHERE ordem IN(5,6,7,8);
  
    --
    -- 10 DIVIDA CONSOLIDADA LIQUIDA PREVIDENCIARIA
    --
    INSERT INTO tmp_valores VALUES (arDescricao[10]
                               , 10
                               , ( (SELECT valor_exercicio_anterior FROM tmp_valores WHERE ordem = 1) - (SELECT valor_exercicio_anterior FROM tmp_valores WHERE ordem = 4) )
                               , ( (SELECT valor_mes FROM tmp_valores WHERE ordem = 1) - (SELECT valor_mes FROM tmp_valores WHERE ordem = 4) )
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

