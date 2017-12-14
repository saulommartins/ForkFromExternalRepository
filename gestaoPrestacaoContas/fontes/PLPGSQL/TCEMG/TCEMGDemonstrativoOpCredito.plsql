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
* $Id:$

*
*/

CREATE OR REPLACE FUNCTION tcemg.demonstrativo_op_credito(varchar,varchar,integer,varchar) RETURNS SETOF RECORD AS $$
DECLARE

  stExercicio            ALIAS FOR $1;
  stTipoPeriodo          ALIAS FOR $2;
  inPeriodo              ALIAS FOR $3;
  stCodEntidade          ALIAS FOR $4;

  dtInicial              VARCHAR := '';
  dtFinal                VARCHAR := '';
  stSql                  VARCHAR := '';
  stSqlConfiguracao      VARCHAR := '';
  stContasConfiguracao   VARCHAR := '';
  arFiltro               VARCHAR[];
  arDatas                VARCHAR[];
  inCondicao             INTEGER := 1;
  inCodEntidadeRPPS      INTEGER := NULL;

  valorMobiliariaInterna NUMERIC(14,4) := 0.0000;
  valorContratExter      NUMERIC(14,4) := 0.0000;
  valorAberturaCredito   NUMERIC(14,4) := 0.0000;
  valorDemAntecReceita   NUMERIC(14,4) := 0.0000;
  valorParcDivTrib       NUMERIC(14,4) := 0.0000;
  valorParcDivPrev       NUMERIC(14,4) := 0.0000;
  valorParcDivDemCS      NUMERIC(14,4) := 0.0000;
  valorParcDivFGTS       NUMERIC(14,4) := 0.0000;

  reConfiguracao        RECORD;
  reRegistro            RECORD;

BEGIN
  
  IF ( stTipoPeriodo = 'mes' ) THEN
    arDatas      := publico.mes( stExercicio, inPeriodo );
    dtInicial    := arDatas[0];    
    dtFinal      := arDatas[1];  
  ELSEIF ( stTipoPeriodo = 'bimestre' ) THEN
    arDatas      := publico.bimestre( stExercicio, inPeriodo );
    dtInicial    := arDatas[0];    
    dtFinal      := arDatas[1];
  END IF;

  --
  -- DESCOBRE A ENTIDADE RPPS
  --
  SELECT valor
    INTO inCodEntidadeRPPS
    FROM administracao.configuracao
   WHERE configuracao.exercicio = stExercicio
     AND parametro = 'cod_entidade_rpps';

 --CRIA UMA TABLE TEMPORARIA  
  stSql := '
    CREATE TEMPORARY TABLE tmp_valores(
         mes                          integer 
        ,vl_imobiliaria_interna       numeric(14,2)
        ,vl_contrat_externa           numeric(14,2)
        ,vl_abertura_credito          numeric(14,2)
        ,vl_dem_antec_receita         numeric(14,2)
        ,vl_parc_div_trib             numeric(14,2)
        ,vl_parc_div_prev             numeric(14,2)
        ,vl_parc_div_dem_cs           numeric(14,2)
        ,vl_parc_div_fgts             numeric(14,2)
    ) ';
  EXECUTE stSql;

  -------------------------------------- PEGA CONFIGURAÇÃO PARA A LINHA Dívida Mobiliária
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
                    AND vinculo_contas_rgf_2.cod_conta = 1
                    AND vinculo_contas_rgf_2.timestamp = (SELECT MAX(timestamp)
                                                            FROM stn.vinculo_contas_rgf_2 tbl
                                                           WHERE vinculo_contas_rgf_2.exercicio = tbl.exercicio);
  ';

  FOR reConfiguracao IN EXECUTE stSqlConfiguracao
  LOOP
    stContasConfiguracao := stContasConfiguracao || ' OR REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE '''''||reConfiguracao.estrutural||'%'''' ';
  END LOOP;
  
  IF stContasConfiguracao <> '' THEN
  -- DEFINE O FILTRO PARA CADA LINHA
    arFiltro[1] := '( '||SUBSTR(stContasConfiguracao, 5)||' ) AND lote.cod_entidade <> '||inCodEntidadeRPPS||' '; 
  ELSE
    arFiltro[1] := '';
  END IF;
  --------- FIM PEGA CONFIGURAÇÃO

  ----------------------------------- PEGA CONFIGURAÇÃO PARA A LINHA Dívida Contratual Externa
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
                    AND vinculo_contas_rgf_2.cod_conta = 3
                    AND vinculo_contas_rgf_2.timestamp = (SELECT MAX(timestamp)
                                                            FROM stn.vinculo_contas_rgf_2 tbl
                                                           WHERE vinculo_contas_rgf_2.exercicio = tbl.exercicio);
  ';

  FOR reConfiguracao IN EXECUTE stSqlConfiguracao
  LOOP
    stContasConfiguracao := stContasConfiguracao || ' OR REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE '''''||reConfiguracao.estrutural||'%'''' ';
  END LOOP;

  IF stContasConfiguracao <> '' THEN
    arFiltro[2] := '( '||SUBSTR(stContasConfiguracao, 5)||' ) AND lote.cod_entidade <> '||inCodEntidadeRPPS||' ';
  ELSE
    arFiltro[2] := '';
  END IF;
  --------- FIM PEGA CONFIGURAÇÃO
  
   ---------------------------------- PEGA CONFIGURAÇÃO PARA A LINHA valor Abertura Credito
  stContasConfiguracao := '';
  stSqlConfiguracao := '
                 SELECT REPLACE(publico.fn_mascarareduzida(cod_estrutural),''.'','''') as estrutural
                      , plano_analitica.exercicio
                   FROM contabilidade.plano_analitica      
             INNER JOIN contabilidade.plano_conta
                     ON plano_analitica.cod_conta = plano_conta.cod_conta
                    AND plano_analitica.exercicio = plano_conta.exercicio
                  WHERE plano_conta.cod_estrutural ilike ''2.1.2%''
                    AND plano_analitica.exercicio = '''||stExercicio||'''
                    AND plano_analitica.natureza_saldo = ''C''
                    AND NOT EXISTS( SELECT 1 FROM stn.vinculo_contas_rgf_2
                                     WHERE vinculo_contas_rgf_2.cod_plano = plano_analitica.cod_plano
				       AND vinculo_contas_rgf_2.exercicio = plano_analitica.exercicio )
  ';

  FOR reConfiguracao IN EXECUTE stSqlConfiguracao
  LOOP
    stContasConfiguracao := stContasConfiguracao || ' OR REPLACE(plano_conta.cod_estrutural,''''.'''','''''''') LIKE '''''||reConfiguracao.estrutural||'%'''' ';
  END LOOP;

  IF stContasConfiguracao <> '' THEN
    arFiltro[3] := '( '||SUBSTR(stContasConfiguracao, 5)||' ) AND lote.cod_entidade <> '||inCodEntidadeRPPS||' ';
  ELSE
    arFiltro[3] := '';
  END IF;
  --------- FIM PEGA CONFIGURAÇÃO
  
  ----------------------------------- PEGA CONFIGURAÇÃO PARA A LINHA ANTECIPAÇÕES DE RECEITA ORÇAMENTÁRIA - ARO
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
                    AND vinculo_contas_rgf_2.cod_conta = 17
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
  ELSE
    arFiltro[4] := '';
  END IF;
  --------- FIM PEGA CONFIGURAÇÃO
  
 ----------------------------------- PEGA CONFIGURAÇÃO PARA A LINHA Valor de parcelamento de dívidas de tributos
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
    arFiltro[5] := '( '||SUBSTR(stContasConfiguracao, 5)||' ) AND lote.cod_entidade <> '||inCodEntidadeRPPS||' ';
  ELSE
    arFiltro[5] := '';
  END IF;
  --------- FIM PEGA CONFIGURAÇÃO 
    
   ----------------------------------- PEGA CONFIGURAÇÃO PARA A LINHA Valor de parcelamento de dívidas com previdenciárias
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
  ELSE
    arFiltro[6] := '';
  END IF;
  --------- FIM PEGA CONFIGURAÇÃO  
  
    ----------------------------------- PEGA CONFIGURAÇÃO PARA A LINHA Valor de parcelamento de dívidas com demais contribuições sociais
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
  ELSE
    arFiltro[7] := '';
  END IF;
  --------- FIM PEGA CONFIGURAÇÃO    

  ----------------------------------- PEGA CONFIGURAÇÃO PARA A LINHA Valor de parcelamento de dívidas do FGTS
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
  ELSE
    arFiltro[8] := '';
  END IF;
  --------- FIM PEGA CONFIGURAÇÃO  

  -- LOOP PARA EXECUTAR AS CONSULTAS E INSERIR OS RESULTADOS NA TABELA TEMPORARIA
  FOR i IN 1..8 LOOP
    inCondicao := -1;
    IF(arFiltro[i] != '') THEN
      stSql := '
        SELECT 
              (  SELECT SUM( stn.pl_saldo_contas(  '''||stExercicio||'''
                                                  , '''||dtInicial||'''
                                                  , '''||dtFinal||'''
                                                  , '''||arFiltro[i]||'''
                                                  , '''||stCodEntidade||'''
                                                 ) * '||inCondicao||'
                             )  
               ) AS valor_mes
      ';
      
      FOR reRegistro IN EXECUTE stSql
      LOOP
         IF i = 1 THEN
            valorMobiliariaInterna := reRegistro.valor_mes;
         ELSEIF i = 2 THEN
            valorContratExter := reRegistro.valor_mes;
         ELSEIF i = 3 THEN
            valorAberturaCredito := reRegistro.valor_mes;
         ELSEIF i = 4 THEN
            valorDemAntecReceita := reRegistro.valor_mes;
         ELSEIF i = 5 THEN
            valorParcDivTrib  := reRegistro.valor_mes;
         ELSEIF i = 6 THEN
            valorParcDivPrev  := reRegistro.valor_mes;
         ELSEIF i = 7 THEN
            valorParcDivDemCS := reRegistro.valor_mes;
         ELSEIF i = 8 THEN
            valorParcDivFGTS  := reRegistro.valor_mes;
         END IF;

      END LOOP;
    END IF;
  END LOOP;

    stSql := ' INSERT INTO tmp_valores 
                    VALUES(  '||inPeriodo||'
                           , '||valorMobiliariaInterna||'
                           , '||valorContratExter||'
                           , '||valorAberturaCredito||'
                           , '||valorDemAntecReceita||'
                           , '||valorParcDivTrib||'
                           , '||valorParcDivPrev||'
                           , '||valorParcDivDemCS||'
                           , '||valorParcDivFGTS||'
                        )
           ';
    EXECUTE stSql;

  stSql := 'SELECT * FROM tmp_valores';

  FOR reRegistro IN EXECUTE stSql
  LOOP
      RETURN next reRegistro;
  END LOOP;

  DROP TABLE tmp_valores;

END;

$$ language 'plpgsql';