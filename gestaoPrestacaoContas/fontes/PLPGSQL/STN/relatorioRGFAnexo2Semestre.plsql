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

CREATE OR REPLACE FUNCTION stn.fn_rgf_anexo2_semestre(varchar,varchar,integer,varchar) RETURNS SETOF RECORD AS $$
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
  inCodEntidadeRPPS   INTEGER := NULL;
  flValorRCL          NUMERIC(14,2) := 0.00;


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
  arDescricao[1] := 'DÍVIDA CONSOLIDADA - DC(I)';
  arDescricao[2] := 'Dívida Mobiliária';
  arDescricao[3] := 'Dívida Contratual';
  arDescricao[4] := 'Dívida Contratual de PPP';
  arDescricao[5] := 'Demais Dívidas Contratuais';
  arDescricao[6] := 'Precatórios posteriores a 05/05/2000 (inclusive)';
  arDescricao[7] := 'Operações de Crédito inferiores a 12 meses';
  arDescricao[8] := 'Parcelamento de Dívidas';
  arDescricao[9] := 'De Tributos';
  arDescricao[10] := 'De Contribuições Sociais';
  arDescricao[11] := 'Previdenciárias';
  arDescricao[12] := 'Demais Contribuições Sociais';
  arDescricao[13] := 'Do FGTS';
  arDescricao[14] := 'Outras Dívidas';
  arDescricao[15] := 'DEDUÇÕES (II)';
  arDescricao[16] := 'Ativo Disponível';
  arDescricao[17] := 'Haveres Financeiros';
  arDescricao[18] := '(-) Restos a Pagar Processados';
  arDescricao[19] := 'OBRIGAÇÕES NÃO INTEGRANTES DA DC';
  arDescricao[20] := 'Precatórios anteriores a 05/05/2000';
  arDescricao[21] := 'Insuficiência Financeira';
  arDescricao[22] := 'Outras Obrigações';
  arDescricao[23] := 'DÍVIDA CONSOLIDADA LÍQUIDA (DCL)(III) = (I-II)';
  arDescricao[24] := 'RECEITA CORRENTE LÍQUIDA - RCL';
  arDescricao[25] := '% da DC sobre a RCL (I/RCL)';
  arDescricao[26] := '% da DCL sobre a RCL (III/RCL)';
  arDescricao[27] := 'LIMITE DEFINIDO POR RESOLUÇÃO Nº 40/01 DO SENADO FEDERAL - 120%';

  -- DEFINE O FILTRO PARA CADA LINHA
  arFiltro[1] := '';
  arFiltro[2] := '( REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE ''''22211%'''' OR REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE ''''22221%'''') AND lote.cod_entidade <> '||inCodEntidadeRPPS||' '; 
  arFiltro[3] := ''; 
  arFiltro[4] := ''; 
  arFiltro[5] := '( REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE ''''22212%'''' OR REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE ''''22222%'''') AND lote.cod_entidade <> '||inCodEntidadeRPPS||' '; 
  arFiltro[6] := '( REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE ''''2121705%'''' ) AND lote.cod_entidade <> '||inCodEntidadeRPPS||' '; 
  arFiltro[7] := '( REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE ''''21231020203%'''' ) AND lote.cod_entidade <> '||inCodEntidadeRPPS||' '; 
  arFiltro[8] := ''; 
  arFiltro[9] := '( REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE ''''2223'''' )  AND lote.cod_entidade <> '||inCodEntidadeRPPS||' '; 
  arFiltro[10] := '';   
  arFiltro[11] := '( REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE ''''2224401%'''' )  AND lote.cod_entidade <> '||inCodEntidadeRPPS||' '; 
  arFiltro[12] := '( REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE ''''22244%'''' AND REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') NOT LIKE ''''2224401%'''' )  AND lote.cod_entidade <> '||inCodEntidadeRPPS||' ';
  arFiltro[13] := '( REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE ''''22249000002%'''' ) AND lote.cod_entidade <> '||inCodEntidadeRPPS||'  '; 
  arFiltro[14] := '( REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE ''''22%''''
                AND  REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') NOT LIKE ''''22211%''''                                 
                AND  REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') NOT LIKE ''''22221%''''                                 
                AND  REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') NOT LIKE ''''22212%''''                                 
                AND  REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') NOT LIKE ''''22222%''''                                 
                AND  REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') NOT LIKE ''''2121705%''''                                 
                AND  REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') NOT LIKE ''''21231020203%''''                                 
                AND  REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') NOT LIKE ''''2223%''''                                 
                AND  REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') NOT LIKE ''''2224401%''''                                 
                AND  REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') NOT LIKE ''''22244%''''                                 
                AND  REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') NOT LIKE ''''22249000002%''''                                 
                   )  AND lote.cod_entidade <> '||inCodEntidadeRPPS||'  ' ;

  arFiltro[15] := ''; 
  arFiltro[16] := '( REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE ''''111%'''' ) AND lote.cod_entidade <> '||inCodEntidadeRPPS||'  '; 
  arFiltro[17] := '( REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE ''''112%'''' ) AND lote.cod_entidade <> '||inCodEntidadeRPPS||'  '; 
  arFiltro[18] := '( REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE ''''2121102%''''
                  OR REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE ''''212110302%''''
                  OR REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE ''''2121202%''''
                  OR REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE ''''212120302%''''    
                  OR REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE ''''21213010002%''''
                  OR REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE ''''21213030002%''''
                  OR REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE ''''21213040002%''''
                   ) AND lote.cod_entidade <> '||inCodEntidadeRPPS||'  ';
  arFiltro[19] := ''; 
  arFiltro[20] := '( REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE ''''2121704%'''' ) AND lote.cod_entidade <> '||inCodEntidadeRPPS||'  '; 
  arFiltro[21] := ''; 
  arFiltro[22] := ''; 
  arFiltro[23] := ''; 
  arFiltro[24] := ''; 
  arFiltro[25] := ''; 
  arFiltro[26] := ''; 
  arFiltro[27] := ''; 

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
  FOR i IN 1..27 LOOP
    IF( i BETWEEN 16 AND 18 ) THEN
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

        stSql := stSql || '
               , CAST(0 AS numeric) AS valor_3
        ';

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
  -- 4 DÍVIDA CONTRATUAL DE PPP
  --

  INSERT INTO tmp_valores VALUES (arDescricao[4],4,0,0,0);
  --
  -- 3 DÍVIDA CONTRATUAL
  --
  INSERT INTO tmp_valores SELECT arDescricao[3]
                               , 3
                               , SUM(valor_exercicio_anterior)
                               , SUM(valor_1)
                               , SUM(valor_2)
                               , SUM(valor_3)
                            FROM tmp_valores
                           WHERE ordem IN (4,5);
  --
  -- 10 CONTRIBUICOES SOCIAIS
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
  -- 8 PARCELAMENTOS DE DIVIDAS
  --
  INSERT INTO tmp_valores SELECT arDescricao[8]
                               , 8
                               , SUM(valor_exercicio_anterior)
                               , SUM(valor_1)
                               , SUM(valor_2)
                               , SUM(valor_3)
                            FROM tmp_valores
                           WHERE ordem IN (9,10,13);

  --
  -- 1 DIVIDA CONSOLIDADA
  --
  INSERT INTO tmp_valores SELECT arDescricao[1]
                               , 1
                               , SUM(valor_exercicio_anterior)
                               , SUM(valor_1)
                               , SUM(valor_2)
                               , SUM(valor_3)
                            FROM tmp_valores
                           WHERE ordem IN(2,3,6,7,8,14);
  --
  -- 15 DEDUCOES
  --
  INSERT INTO tmp_valores SELECT arDescricao[15]
                               , 15
                               , SUM(valor_exercicio_anterior)
                               , SUM(valor_1)
                               , SUM(valor_2)
                               , SUM(valor_3)
                            FROM tmp_valores
                           WHERE ordem IN(16,17,18);

  --
  -- 21 INSUFICIENCIA FINANCEIRA
  --
  SELECT valor_exercicio_anterior
       , valor_1
       , valor_2
       , valor_3
    INTO vlExercicioAnterior,vlPeriodo1,vlPeriodo2,vlPeriodo3
    FROM tmp_valores
   WHERE ordem = 15;

  stSql := '
    INSERT INTO tmp_valores
    VALUES (  '''||arDescricao[21]||'''
            , 21
  ';
  IF( vlExercicioAnterior < 0 ) THEN
    stSql := stSql || '
            , '||vlExercicioAnterior*-1||'
    ';

    UPDATE tmp_valores SET valor_exercicio_anterior = NULL WHERE ordem = 15;
  ELSE 
    stSql := stSql||',0';
  END IF;

  IF( vlPeriodo1 < 0 ) THEN
    stSql := stSql || '
            , '||vlPeriodo1*-1||'
    ';            
    
    UPDATE tmp_valores SET valor_1 = NULL WHERE ordem = 15;
  ELSE 
    stSql := stSql||',0';
  END IF;

  IF( vlPeriodo2 < 0 ) THEN
    stSql := stSql || '
            , '||vlPeriodo2*-1||'
    ';

    UPDATE tmp_valores SET valor_2 = NULL WHERE ordem = 15;
  ELSE 
    stSql := stSql||',0';
  END IF;

  IF( vlPeriodo3 < 0 ) THEN
    stSql := stSql || '
            , '||vlPeriodo3*-1||'
    ';

    UPDATE tmp_valores SET valor_3 = NULL WHERE ordem = 15;
  ELSE 
    stSql := stSql||',0';
  END IF;

  stSql:= stSql || ' )';

  EXECUTE stSql;


  -- 
  -- 22 OUTRAS OBRIGACOES 
  -- 
  INSERT INTO tmp_valores VALUES (arDescricao[22],22,0,0,0,0);

  --
  -- 19 0BRIGACOES NAO INTEGRANTES DA DC
  --
  INSERT INTO tmp_valores SELECT arDescricao[19]
                               , 19
                               , SUM(valor_exercicio_anterior)
                               , SUM(valor_1)
                               , SUM(valor_2)
                               , SUM(valor_3)
                            FROM tmp_valores
                           WHERE ordem IN(20,21,22);

  --
  -- 23 DIVIDA CONSOLIDADA LIQUIDA
  --
  INSERT INTO tmp_valores VALUES (arDescricao[23]
                               , 23
                               , ( (SELECT valor_exercicio_anterior FROM tmp_valores WHERE ordem = 1) - (SELECT valor_exercicio_anterior FROM tmp_valores WHERE ordem = 15) )
                               , ( (SELECT valor_1 FROM tmp_valores WHERE ordem = 1) - (SELECT valor_1 FROM tmp_valores WHERE ordem = 15) )
                               , ( (SELECT valor_2 FROM tmp_valores WHERE ordem = 1) - (SELECT valor_2 FROM tmp_valores WHERE ordem = 15) )
                               , ( (SELECT valor_3 FROM tmp_valores WHERE ordem = 1) - (SELECT valor_3 FROM tmp_valores WHERE ordem = 15) )
                                 );

  --
  -- 24 RECEITA CORRRENTE LIQUIDA - RCL
  --
  -- ## RCL do Bruce | Anexo III - RREO
  SELECT
      sum(cast( ( total_mes_1 + total_mes_2 + total_mes_3 + total_mes_4  + total_mes_5  + total_mes_6
                 + total_mes_7 + total_mes_8 + total_mes_9 + total_mes_10 + total_mes_11 + total_mes_12 ) as numeric(14,2))) as valor
  INTO vlExercicioAnterior
  FROM stn.pl_total_subcontas ('31/12/'||stExercicioAnterior) as retorno (
                                                                ordem      integer
                                                               ,cod_conta      varchar
                                                               ,nom_conta      varchar
                                                               ,cod_estrutural varchar
                                                               ,mes_1      numeric
                                                               ,mes_2      numeric
                                                               ,mes_3      numeric
                                                               ,mes_4      numeric
                                                               ,mes_5      numeric
                                                               ,mes_6      numeric
                                                               ,mes_7      numeric
                                                               ,mes_8      numeric
                                                               ,mes_9      numeric
                                                               ,mes_10     numeric
                                                               ,mes_11     numeric
                                                               ,mes_12     numeric
                                                               ,total_mes_1  numeric
                                                               ,total_mes_2  numeric
                                                               ,total_mes_3  numeric
                                                               ,total_mes_4  numeric
                                                               ,total_mes_5  numeric
                                                               ,total_mes_6  numeric
                                                               ,total_mes_7  numeric
                                                               ,total_mes_8  numeric
                                                               ,total_mes_9  numeric
                                                               ,total_mes_10 numeric
                                                               ,total_mes_11 numeric
                                                               ,total_mes_12 numeric)
  WHERE ordem = 1;

  --
  -- Acrescenta o valor da rcl vinculada ao periodo
  --
  SELECT stn.fn_calcula_rcl_vinculada(stExercicio,'01/01/' || stExercicio,stCodEntidade)
    INTO flValorRCL;

  vlExercicioAnterior := vlExercicioAnterior + flValorRCL;

  SELECT
      sum(cast( ( total_mes_1 + total_mes_2 + total_mes_3 + total_mes_4  + total_mes_5  + total_mes_6
                 + total_mes_7 + total_mes_8 + total_mes_9 + total_mes_10 + total_mes_11 + total_mes_12 ) as numeric(14,2))) as valor
  INTO vlPeriodo1
  FROM stn.pl_total_subcontas (arDtFinal[1]) as retorno (
                                                                ordem      integer
                                                               ,cod_conta      varchar
                                                               ,nom_conta      varchar
                                                               ,cod_estrutural varchar
                                                               ,mes_1      numeric
                                                               ,mes_2      numeric
                                                               ,mes_3      numeric
                                                               ,mes_4      numeric
                                                               ,mes_5      numeric
                                                               ,mes_6      numeric
                                                               ,mes_7      numeric
                                                               ,mes_8      numeric
                                                               ,mes_9      numeric
                                                               ,mes_10     numeric
                                                               ,mes_11     numeric
                                                               ,mes_12     numeric
                                                               ,total_mes_1  numeric
                                                               ,total_mes_2  numeric
                                                               ,total_mes_3  numeric
                                                               ,total_mes_4  numeric
                                                               ,total_mes_5  numeric
                                                               ,total_mes_6  numeric
                                                               ,total_mes_7  numeric
                                                               ,total_mes_8  numeric
                                                               ,total_mes_9  numeric
                                                               ,total_mes_10 numeric
                                                               ,total_mes_11 numeric
                                                               ,total_mes_12 numeric)
  WHERE ordem = 1;

  --
  -- Acrescenta o valor da rcl vinculada ao periodo
  --
  SELECT stn.fn_calcula_rcl_vinculada(stExercicio,arDtFinal[1],stCodEntidade)
    INTO flValorRCL;

  vlPeriodo1 := vlPeriodo1 + flValorRCL;

  IF(inPeriodo>1) THEN
  SELECT
      sum(cast( ( total_mes_1 + total_mes_2 + total_mes_3 + total_mes_4  + total_mes_5  + total_mes_6
                 + total_mes_7 + total_mes_8 + total_mes_9 + total_mes_10 + total_mes_11 + total_mes_12 ) as numeric(14,2))) as valor
  INTO vlPeriodo2
  FROM stn.pl_total_subcontas (arDtFinal[2]) as retorno (
                                                                ordem      integer
                                                               ,cod_conta      varchar
                                                               ,nom_conta      varchar
                                                               ,cod_estrutural varchar
                                                               ,mes_1      numeric
                                                               ,mes_2      numeric
                                                               ,mes_3      numeric
                                                               ,mes_4      numeric
                                                               ,mes_5      numeric
                                                               ,mes_6      numeric
                                                               ,mes_7      numeric
                                                               ,mes_8      numeric
                                                               ,mes_9      numeric
                                                               ,mes_10     numeric
                                                               ,mes_11     numeric
                                                               ,mes_12     numeric
                                                               ,total_mes_1  numeric
                                                               ,total_mes_2  numeric
                                                               ,total_mes_3  numeric
                                                               ,total_mes_4  numeric
                                                               ,total_mes_5  numeric
                                                               ,total_mes_6  numeric
                                                               ,total_mes_7  numeric
                                                               ,total_mes_8  numeric
                                                               ,total_mes_9  numeric
                                                               ,total_mes_10 numeric
                                                               ,total_mes_11 numeric
                                                               ,total_mes_12 numeric)
  WHERE ordem = 1;

  --
  -- Acrescenta o valor da rcl vinculada ao periodo
  --
  SELECT stn.fn_calcula_rcl_vinculada(stExercicio,arDtFinal[2],stCodEntidade)
    INTO flValorRCL;

  vlPeriodo2 := vlPeriodo2 + flValorRCL;


  ELSE
    vlPeriodo2 := 0;
  END IF;

  IF(inPeriodo>2) THEN
  SELECT
      sum(cast( ( total_mes_1 + total_mes_2 + total_mes_3 + total_mes_4  + total_mes_5  + total_mes_6
                 + total_mes_7 + total_mes_8 + total_mes_9 + total_mes_10 + total_mes_11 + total_mes_12 ) as numeric(14,2))) as valor
  INTO vlPeriodo3
  FROM stn.pl_total_subcontas (arDtFinal[3]) as retorno (
                                                                ordem      integer
                                                               ,cod_conta      varchar
                                                               ,nom_conta      varchar
                                                               ,cod_estrutural varchar
                                                               ,mes_1      numeric
                                                               ,mes_2      numeric
                                                               ,mes_3      numeric
                                                               ,mes_4      numeric
                                                               ,mes_5      numeric
                                                               ,mes_6      numeric
                                                               ,mes_7      numeric
                                                               ,mes_8      numeric
                                                               ,mes_9      numeric
                                                               ,mes_10     numeric
                                                               ,mes_11     numeric
                                                               ,mes_12     numeric
                                                               ,total_mes_1  numeric
                                                               ,total_mes_2  numeric
                                                               ,total_mes_3  numeric
                                                               ,total_mes_4  numeric
                                                               ,total_mes_5  numeric
                                                               ,total_mes_6  numeric
                                                               ,total_mes_7  numeric
                                                               ,total_mes_8  numeric
                                                               ,total_mes_9  numeric
                                                               ,total_mes_10 numeric
                                                               ,total_mes_11 numeric
                                                               ,total_mes_12 numeric)
  WHERE ordem = 1;

  --
  -- Acrescenta o valor da rcl vinculada ao periodo
  --
  SELECT stn.fn_calcula_rcl_vinculada(stExercicio,arDtFinal[3],stCodEntidade)
    INTO flValorRCL;


  ELSE
    vlPeriodo3 := 0;

  END IF;

  INSERT INTO tmp_valores VALUES( arDescricao[24],24,vlExercicioAnterior,vlPeriodo1,vlPeriodo2,vlPeriodo3 );

  --
  -- 25 DC SOBRE A RCL
  --
  SELECT CASE WHEN(vlExercicioAnterior <> 0) THEN 
         ROUND( ( SELECT valor_exercicio_anterior FROM tmp_valores WHERE ordem = 1 )
           /
           ( SELECT valor_exercicio_anterior FROM tmp_valores WHERE ordem = 24 )
         ,2)*100 
         ELSE 0
         END AS valor_exercicio_anterior
       , CASE WHEN(vlPeriodo1 <> 0) THEN
         ROUND( ( SELECT valor_1 FROM tmp_valores WHERE ordem = 1 )
           /
           ( SELECT valor_1 FROM tmp_valores WHERE ordem = 24 )
         ,2)*100
         ELSE 0
         END AS valor_1
       , CASE WHEN(vlPeriodo2 <> 0) THEN
         ROUND( ( SELECT valor_2 FROM tmp_valores WHERE ordem = 1 )
           /
           ( SELECT valor_2 FROM tmp_valores WHERE ordem = 24 )
         ,2)*100
         ELSE 0
         END AS valor_2
       , CASE WHEN(vlPeriodo3 <> 0) THEN
         ROUND( ( SELECT valor_3 FROM tmp_valores WHERE ordem = 1 )
           /
           ( SELECT valor_3 FROM tmp_valores WHERE ordem = 24 )
         ,2)*100 
         ELSE 0
         END AS valor_3
    INTO vlExercicioAnterior, vlPeriodo1, vlPeriodo2, vlPeriodo3;

  INSERT INTO tmp_valores VALUES ('% da DC sobre a RCL (I/RCL)',25,vlExercicioAnterior,vlPeriodo1,vlPeriodo2,vlPeriodo3);

  --
  -- 26 DCL sobre a RCL (III/RCL)
  --
  SELECT CASE WHEN(vlExercicioAnterior <> 0) THEN
         ROUND( ( SELECT valor_exercicio_anterior FROM tmp_valores WHERE ordem = 23 )
           /
           ( SELECT valor_exercicio_anterior FROM tmp_valores WHERE ordem = 24 )
         ,2)*100 
         ELSE 0
         END AS valor_exercicio_anterior
       , CASE WHEN(vlPeriodo1 <> 0) THEN
         ROUND( ( SELECT valor_1 FROM tmp_valores WHERE ordem = 23 )
           /
           ( SELECT valor_1 FROM tmp_valores WHERE ordem = 24 )
         ,2)*100
         ELSE 0
         END AS valor_1
       , CASE WHEN(vlPeriodo2 <> 0) THEN
         ROUND( ( SELECT valor_2 FROM tmp_valores WHERE ordem = 23 )
           /
           ( SELECT valor_2 FROM tmp_valores WHERE ordem = 24 )
         ,2)*100 
         ELSE 0
         END AS valor_2
       , CASE WHEN(vlPeriodo3 <> 0) THEN
         ROUND( ( SELECT valor_3 FROM tmp_valores WHERE ordem = 23 )
           /
           ( SELECT valor_3 FROM tmp_valores WHERE ordem = 24 )
         ,2)*100
         ELSE 0
         END AS valor_3
    INTO vlExercicioAnterior, vlPeriodo1, vlPeriodo2, vlPeriodo3;

  INSERT INTO tmp_valores VALUES ('% da DCL sobre a RCL (III/RCL)',26,vlExercicioAnterior,vlPeriodo1,vlPeriodo2,vlPeriodo3);

  --
  -- 27 LIMITE DEFINIDO POR RESOLUCAO
  --
  INSERT INTO tmp_valores VALUES (  'LIMITE DEFINIDO POR RESOLUÇÃO Nº 40/01 DO SENADO FEDERAL - 120%'
                                  , 27
                                  , (SELECT ROUND((valor_exercicio_anterior*120)/100,2) FROM tmp_valores WHERE ordem = 24)
                                  , (SELECT ROUND((valor_1*120)/100,2) FROM tmp_valores WHERE ordem = 24)
                                  , (SELECT ROUND((valor_2*120)/100,2) FROM tmp_valores WHERE ordem = 24)
                                  , (SELECT ROUND((valor_3*120)/100,2) FROM tmp_valores WHERE ordem = 24)
                                 );

  --
  -- FAZ OS UPDATES DOS NIVEIS
  --
  UPDATE tmp_valores SET nivel = 0 WHERE ordem IN (1,15,19,23,24,25,26,27);
  UPDATE tmp_valores SET nivel = 1 WHERE ordem IN (2,3,6,7,8,14,16,17,18,20,21,22);
  UPDATE tmp_valores SET nivel = 2 WHERE ordem IN (4,5,9,10,13);
  UPDATE tmp_valores SET nivel = 3 WHERE ordem IN (4,5,11,12);

  stSql := 'SELECT * FROM tmp_valores ORDER BY ordem';

  FOR reRegistro IN EXECUTE stSql
  LOOP
      RETURN next reRegistro;
  END LOOP;

  DROP TABLE tmp_valores;

END;

$$ language 'plpgsql';

