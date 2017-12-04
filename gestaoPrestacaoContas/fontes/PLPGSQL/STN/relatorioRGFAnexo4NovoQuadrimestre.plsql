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
* $Revision: 54477 $
* $Name$
* $Author: carolina $
* $Date: 2013-04-02 14:35:56 -0300 (Tue, 02 Apr 2013) $
*
* Casos de uso: uc-06.01.23
*/

/*
$Log$
Revision 1.2  2007/10/15 14:11:43  tonismar
Relatório Anexo4

Revision 1.1  2006/09/26 10:15:54  cleisson
Inclusão

Revision 1.4  2006/08/14 18:54:48  cako
Ajustes para trazer somente valores diferentes de 0.00

Revision 1.3  2006/08/14 18:37:45  cako
Ajustes.

Revision 1.2  2006/08/09 19:47:39  cako
Melhoramentos.
Adição do campo ''linha'' para auxiliar no desenvolvimento do layout no agata

Revision 1.1  2006/08/09 17:15:17  cako
Versão inicial


*/

CREATE OR REPLACE FUNCTION stn.fn_rgf_anexo4_novo_quadrimestre(varchar,integer,varchar) RETURNS SETOF RECORD AS $$
DECLARE

    stExercicio           ALIAS FOR $1;
    inQuadrimestre        ALIAS FOR $2;
    stCodEntidades        ALIAS FOR $3;

    stDtInicial           VARCHAR := '';
    stDtFinal             VARCHAR := '';
    stSqlConfiguracao     VARCHAR := '';
    stContasConfiguracao  VARCHAR := '';
  
    nuRCL                   NUMERIC;
    nu_I_no_quadrimestre    NUMERIC := 0;
    nu_I_ate_quadrimestre   NUMERIC := 0;
    nu_II_no_quadrimestre   NUMERIC := 0;
    nu_II_ate_quadrimestre  NUMERIC := 0;
    nuPercent               NUMERIC;
    nuPercent2              NUMERIC;
    nuPercent3              NUMERIC;
    nuPercent4              NUMERIC;
    nuPercent5              NUMERIC;
    flValorRCL              NUMERIC(14,2) := 0.00;
    crCursor                REFCURSOR;

    arEntidades         VARCHAR[];
    stEntidadesCamara   VARCHAR := '';
    inEntidadeCamara    INTEGER;
    inCount             INTEGER;

    stSql               VARCHAR   := '';
    stSqlAux            VARCHAR   := '';
    reRegistro          RECORD;
    reConfiguracao      RECORD;
    
    stSaldoContaNoQuadrimestre   VARCHAR[];
    stSaldoContaAteQuadrimestre  VARCHAR[];

BEGIN

    IF inQuadrimestre  = 1 THEN
        stDtInicial := '01/01/'||stExercicio||'';
        stDtFinal := '30/04/'||stExercicio||'';
    END IF;
    
    IF inQuadrimestre  = 2 THEN
        stDtInicial := '01/05/'||stExercicio||'';
        stDtFinal := '31/08/'||stExercicio||'';
    END IF;

    IF inQuadrimestre  = 3 THEN
        stDtInicial := '01/09/'||stExercicio||'';
        stDtFinal := '31/12/'||stExercicio||'';
    END IF;

            
stSql := '
-- Tabela para os dados Definitivos do Relatório
    CREATE TEMPORARY TABLE tmp_relatorio (
        nivel                         INTEGER,
        item                         VARCHAR,
        valor_no_quadrimestre  NUMERIC,
        valor_ate_quadrimestre NUMERIC,
        linha                        CHAR,
        tabela                      INTEGER
    )
    ';

EXECUTE stSql;


-- Vincutar Contas RGF 2
-- DÍVIDA CONSOLIDADA: Dívida Mobiliária 
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
                                                         WHERE vinculo_contas_rgf_2.exercicio = tbl.exercicio); ';

FOR reConfiguracao IN EXECUTE stSqlConfiguracao
LOOP
  stContasConfiguracao := stContasConfiguracao || ' OR REPLACE(plano_conta.cod_estrutural, ''''.'''', '''''''') LIKE '''''||reConfiguracao.estrutural||'%'''' ';
END LOOP;

IF stContasConfiguracao <> '' THEN
  stSaldoContaNoQuadrimestre[1] := '(SELECT SUM(stn.pl_saldo_contas( '''||stExercicio||''', '''||stDtInicial||''', '''||stDtFinal||''', '''||'( '||SUBSTR(stContasConfiguracao, 5)||' )' ||''', '''||stCodEntidades||'''))) * -1';
  stSaldoContaAteQuadrimestre[1] := '(SELECT SUM(stn.pl_saldo_contas( '''||stExercicio||''', '''||stDtInicial||''', '''||stDtFinal||''', '''||'( '||SUBSTR(stContasConfiguracao, 5)||' )' ||''', '''||stCodEntidades||'''))) * -1';
ELSE
  stSaldoContaNoQuadrimestre[1] := '0.00';
  stSaldoContaAteQuadrimestre[1] := '0.00';
END IF;


-- Vincutar Contas RGF 2
-- PARCELAMENTO DE DÍVIDAS: De Contribuições Sociais - Previdenciárias 
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
                                                         WHERE vinculo_contas_rgf_2.exercicio = tbl.exercicio); ';

FOR reConfiguracao IN EXECUTE stSqlConfiguracao
LOOP
  stContasConfiguracao := stContasConfiguracao || ' OR REPLACE(plano_conta.cod_estrutural, ''''.'''', '''''''') LIKE '''''||reConfiguracao.estrutural||'%'''' ';
END LOOP;

IF stContasConfiguracao <> '' THEN
  stSaldoContaNoQuadrimestre[2] := '(SELECT SUM(stn.pl_saldo_contas( '''||stExercicio||''', '''||stDtInicial||''', '''||stDtFinal||''', '''||'( '||SUBSTR(stContasConfiguracao, 5)||' )' ||''', '''||stCodEntidades||'''))) * -1';
  stSaldoContaAteQuadrimestre[2] := '(SELECT SUM(stn.pl_saldo_contas( '''||stExercicio||''', '''||stDtInicial||''', '''||stDtFinal||''', '''||'( '||SUBSTR(stContasConfiguracao, 5)||' )' ||''', '''||stCodEntidades||'''))) * -1';
ELSE
  stSaldoContaNoQuadrimestre[2] := '0.00';
  stSaldoContaAteQuadrimestre[2] := '0.00';
END IF;


-- Vincutar Contas RGF 2
-- PARCELAMENTO DE DÍVIDAS: Do FGTS 
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
                                                         WHERE vinculo_contas_rgf_2.exercicio = tbl.exercicio); ';

FOR reConfiguracao IN EXECUTE stSqlConfiguracao
LOOP
  stContasConfiguracao := stContasConfiguracao || ' OR REPLACE(plano_conta.cod_estrutural, ''''.'''', '''''''') LIKE '''''||reConfiguracao.estrutural||'%'''' ';
END LOOP;

IF stContasConfiguracao <> '' THEN
  stSaldoContaNoQuadrimestre[3] := '(SELECT SUM(stn.pl_saldo_contas( '''||stExercicio||''', '''||stDtInicial||''', '''||stDtFinal||''', '''||'( '||SUBSTR(stContasConfiguracao, 5)||' )' ||''', '''||stCodEntidades||'''))) * -1';
  stSaldoContaAteQuadrimestre[3] := '(SELECT SUM(stn.pl_saldo_contas( '''||stExercicio||''', '''||stDtInicial||''', '''||stDtFinal||''', '''||'( '||SUBSTR(stContasConfiguracao, 5)||' )' ||''', '''||stCodEntidades||'''))) * -1';
ELSE
  stSaldoContaNoQuadrimestre[3] := '0.00';
  stSaldoContaAteQuadrimestre[3] := '0.00';
END IF;


-- Vincutar Contas RGF 2
-- DÍVIDA CONSOLIDADA: Outras Dívidas
-- DÍVIDA COM INSTITUIÇÃO FINANCEIRA: Interna
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
                  AND vinculo_contas_rgf_2.cod_conta IN (5,12)
                  AND vinculo_contas_rgf_2.timestamp = (SELECT MAX(timestamp)
                                                          FROM stn.vinculo_contas_rgf_2 tbl
                                                         WHERE vinculo_contas_rgf_2.exercicio = tbl.exercicio); ';

FOR reConfiguracao IN EXECUTE stSqlConfiguracao
LOOP
  stContasConfiguracao := stContasConfiguracao || ' OR REPLACE(plano_conta.cod_estrutural, ''''.'''', '''''''') LIKE '''''||reConfiguracao.estrutural||'%'''' ';
END LOOP;

IF stContasConfiguracao <> '' THEN
  stSaldoContaNoQuadrimestre[4] := '(SELECT SUM(stn.pl_saldo_contas( '''||stExercicio||''', '''||stDtInicial||''', '''||stDtFinal||''', '''||'( '||SUBSTR(stContasConfiguracao, 5)||' )' ||''', '''||stCodEntidades||'''))) * -1';
  stSaldoContaAteQuadrimestre[4] := '(SELECT SUM(stn.pl_saldo_contas( '''||stExercicio||''', '''||stDtInicial||''', '''||stDtFinal||''', '''||'( '||SUBSTR(stContasConfiguracao, 5)||' )' ||''', '''||stCodEntidades||'''))) * -1';
ELSE
  stSaldoContaNoQuadrimestre[4] := '0.00';
  stSaldoContaAteQuadrimestre[4] := '0.00';
END IF;


stSql := '
    INSERT INTO tmp_relatorio values( 1
                                    , ''SUJEITAS AO LIMITE PARA FINS DE CONTRATAÇÃO (I)''
                                    , '||stSaldoContaNoQuadrimestre[1]||'
                                    , '||stSaldoContaAteQuadrimestre[1]||'
                                    , ''N''
                                    , 1);

--Mobiliaria
    INSERT INTO tmp_relatorio values( 2
                                    , ''Mobiliaria''
                                    , '||stSaldoContaNoQuadrimestre[1]||'
                                    , '||stSaldoContaAteQuadrimestre[1]||'
                                    , ''N''
                                    , 1);

--Internas
    INSERT INTO tmp_relatorio values( 3
                                    , ''Interna''
                                    , '||stSaldoContaNoQuadrimestre[1]||'
                                    , '||stSaldoContaAteQuadrimestre[1]||'
                                    , ''N''
                                    ,  1);

--Externas
    INSERT INTO tmp_relatorio values(3
                                    , ''Externa''
                                    ,0.00
                                    ,0.00
                                    ,''N''
                                    , 1);    

--Contratural
    INSERT INTO tmp_relatorio values(2
                                    ,''Contratual''
                                    ,0.00 
                                    ,0.00
                                    ,''N''
                                    , 1);

--Internas
    INSERT INTO tmp_relatorio values(3
                                    ,''Interna''
                                    ,0.00
                                    ,0.00
                                    ,''N''
                                    , 1);
--Abertura de Crédito
    INSERT INTO tmp_relatorio values(4
                                    ,''Abertura de Crédito''
                                    ,0.00
                                    ,0.00
                                    ,''N''
                                    , 1);

--Aquisicao Financiada de Bens e Arrendamento Mercantil Financeiro
    INSERT INTO tmp_relatorio values(4
                                    ,''Aquisição Financiada de Bens e Arrendamento Mercantil Financeiro''
                                    ,0.00
                                    ,0.00
                                    ,''N''
                                    , 1);
--Derivadas de PPP
    INSERT INTO tmp_relatorio values(5
                                    ,''Derivadas de PPP''
                                    ,0.00
                                    ,0.00
                                    ,''N''
                                    , 1);

--Demais Aquisições Financiadas
    INSERT INTO tmp_relatorio values(5
                                    ,''Demais Aquisições Financiadas''
                                    ,0.00
                                    ,0.00
                                    ,''N''
                                    , 1);

--Antecipação de Receita
    INSERT INTO tmp_relatorio values(4
                                    ,''Antecipação de Receita''
                                    ,0.00
                                    ,0.00
                                    ,''N''
                                    , 1);

--Pela Venda a Termo de Bens e Serviços
    INSERT INTO tmp_relatorio values(5
                                    ,''Pela Venda a Termo de Bens e Serviços''
                                    ,0.00
                                    ,0.00
                                    ,''N''
                                    , 1);

--Demais Antecipações de Receita
    INSERT INTO tmp_relatorio values(5
                                    ,''Demais Antecipações de Receita''
                                    ,0.00
                                    ,0.00
                                    ,''N''
                                    , 1);
                                    
--Assunção, Reconhecimento e Confissão  de Dividas(LRF, art. 29, )
    INSERT INTO tmp_relatorio values(4
                                    ,''Assunção, Reconhecimento e Confissão  de Dividas(LRF, art. 29, )''
                                    ,0.00
                                    ,0.00
                                    ,''N''
                                    , 1);

--Outras Operações de Crédito
    INSERT INTO tmp_relatorio values(4
                                    ,''Outras Operações de Crédito''
                                    ,0.00
                                    ,0.00
                                    ,''N''
                                    , 1);

--Externa
    INSERT INTO tmp_relatorio values(3
                                    ,''Externa''
                                    ,0.00
                                    ,0.00
                                    ,''S''
                                    , 1);

-- Não sujeitas ao limite para fins de contratação (II)
    INSERT INTO tmp_relatorio values(1
                                    ,''NÃO SUJEITAS AO LIMITE PARA FINS DE CONTRATAÇÃO(II)''
                                    , ('||stSaldoContaNoQuadrimestre[2]||' + '||stSaldoContaNoQuadrimestre[3]||' + '||stSaldoContaNoQuadrimestre[4]||')
                                    , ('||stSaldoContaAteQuadrimestre[2]||' + '||stSaldoContaNoQuadrimestre[3]||' + '||stSaldoContaNoQuadrimestre[4]||')
                                    ,''N''
                                    , 1);

--Parcelamento de Dividas 
    INSERT INTO tmp_relatorio values(2
                                    ,''Parcelamentos de Dividas''
                                    ,0.00
                                    ,0.00
                                    ,''N''
                                    , 1);

--De Tributos
    INSERT INTO tmp_relatorio values(3
                                    ,''De Tributos''
                                    ,0.00
                                    ,0.00
                                    ,''N''
                                    , 1);


--De Contribuições Sociais
    INSERT INTO tmp_relatorio values(3
                                    ,''De Contribuições Sociais''
                                    ,0.00
                                    ,0.00
                                    ,''N''
                                    , 1);

--Previdenciárias 
    INSERT INTO tmp_relatorio values(4
                                    ,''Previdenciárias''
                                    , '||stSaldoContaNoQuadrimestre[2]||'
                                    , '||stSaldoContaAteQuadrimestre[2]||'
                                    ,''N''
                                    , 1);


--Demais Contribuições Sociais
    INSERT INTO tmp_relatorio values(4
                                    ,''Demais Contribuições Sociais''
                                    ,0.00
                                    ,0.00
                                    ,''N''
                                    , 1);

--Do FGTS
    INSERT INTO tmp_relatorio values(3
                                    ,''Do FGTS''
                                    , '||stSaldoContaNoQuadrimestre[3]||'
                                    , '||stSaldoContaAteQuadrimestre[3]||'
                                    ,''N''
                                    , 1);

--Melhoria da Administração de Receitas e da Gestão Fiscal, Financeira e Patrimonial
    INSERT INTO tmp_relatorio values(2
                                    ,''Melhoria da Administração de Receitas e da Gestão Fiscal, Financeira e Patrimonial''
                                    ,0.00
                                    ,0.00
                                    ,''N''
                                    , 1);

--Programa de Iluminação Pública - RELUZ
    INSERT INTO tmp_relatorio values(2
                                    ,''Programa de Iluminação Pública - RELUZ''
                                    ,0.00
                                    ,0.00
                                    ,''N''
                                    , 1);

--Outras Operações de Crédito Não Sujeistas ao Limite
    INSERT INTO tmp_relatorio values(2
                                    ,''Outras Operações de Crédito Não Sujeistas ao Limite''
                                    , '||stSaldoContaNoQuadrimestre[4]||'
                                    , '||stSaldoContaAteQuadrimestre[4]||'
                                    ,''N''
                                    , 1); ';

EXECUTE stSql;

-- Sujeitas ao Limite para Fins de Contratação Externas + Internas(I)
--SELECT coalesce(sum(valor_no_quadrimestre),0.00) INTO nu_I_no_quadrimestre              
--FROM tmp_balancete_valores 
--WHERE cod_estrutural LIKE '2.1%';
--
--SELECT coalesce(sum(valor_ate_quadrimestre),0.00) INTO nu_I_ate_quadrimestre              
--FROM tmp_balancete_valores 
--WHERE cod_estrutural LIKE '2.1%';
    

--stSql :='
---- Não Sujeitas ao LimitePor Antecipação da Receita (II)
--    SELECT coalesce(sum(valor_no_quadrimestre),0.00) 
--    FROM tmp_balancete_valores 
--    WHERE cod_estrutural like ''2.1.2.2.2.02.07%''; ';
--
--    OPEN  crCursor FOR EXECUTE stSql;
--    FETCH crCursor INTO nu_II_no_quadrimestre;
--    CLOSE crCursor;
--
--stSql :='
---- Por Antecipação da Receita (II)
--    SELECT coalesce(sum(valor_ate_quadrimestre),0.00) 
--    FROM tmp_balancete_valores 
--    WHERE cod_estrutural like ''2.1.2.2.2.02.07%''; ';
--
--    OPEN  crCursor FOR EXECUTE stSql;
--    FETCH crCursor INTO nu_II_ate_quadrimestre;
--    CLOSE crCursor;
--


    SELECT SUM( total_mes_1 + total_mes_2 + total_mes_3 + total_mes_4 + total_mes_5 + total_mes_6 + total_mes_7 + total_mes_8 + total_mes_9 + total_mes_10 + total_mes_11 + total_mes_12 ) INTO nuRCL
      FROM stn.pl_total_subcontas_entidade (stDtFinal, stCodEntidades) AS retorno 
           (  ordem          INTEGER
            , nivel          INTEGER
            , cod_conta      VARCHAR
            , nom_conta      VARCHAR
            , cod_estrutural VARCHAR
            , mes_1          NUMERIC
            , mes_2          NUMERIC
            , mes_3          NUMERIC
            , mes_4          NUMERIC
            , mes_5          NUMERIC
            , mes_6          NUMERIC
            , mes_7          NUMERIC
            , mes_8          NUMERIC
            , mes_9          NUMERIC
            , mes_10         NUMERIC                         
            , mes_11         NUMERIC                         
            , mes_12         NUMERIC                         
            , total_mes_1    NUMERIC                         
            , total_mes_2    NUMERIC                         
            , total_mes_3    NUMERIC                         
            , total_mes_4    NUMERIC                         
            , total_mes_5    NUMERIC                         
            , total_mes_6    NUMERIC                         
            , total_mes_7    NUMERIC                         
            , total_mes_8    NUMERIC                         
            , total_mes_9    NUMERIC                         
            , total_mes_10   NUMERIC                         
            , total_mes_11   NUMERIC                         
            , total_mes_12   NUMERIC );


    --------------------------------------------------------------------------
    -- Verifica se a entidade camara veio junto com outras entidades e separa --
    --------------------------------------------------------------------------
   SELECT string_to_array(stCodEntidades,',')
      INTO arEntidades;

    SELECT valor
      INTO inEntidadeCamara
      FROM administracao.configuracao
     WHERE cod_modulo = 8
       AND parametro = 'cod_entidade_camara'
       AND exercicio = stExercicio::varchar;

    IF(ARRAY_UPPER(arEntidades,1) > 1) THEN
        FOR inCount IN 1..(ARRAY_UPPER(arEntidades,1))
        LOOP
            IF(arEntidades[inCount] <> inEntidadeCamara::varchar) THEN
                stEntidadesCamara := stEntidadesCamara || arEntidades[inCount] || ',';
            END IF;
        END LOOP;
        stEntidadesCamara := SUBSTR(stEntidadesCamara,1,LENGTH(stEntidadesCamara) -1);
    ELSE
        stEntidadesCamara := arEntidades[1];
    END IF;

    --
    -- Acrescenta o valor da rcl vinculada ao periodo
    --
--    SELECT stn.fn_calcula_rcl_vinculada(stExercicio,stDtFinal,stEntidadesCamara)
--      INTO flValorRCL;
--
--    nuRCL := nuRCL + flValorRCL;

    stSql := 'INSERT INTO tmp_relatorio values(1,''RECEITA CORRENTE LÍQUIDA - RCL'','||nuRCL||', 100,''S'',2)';
    EXECUTE stSql;

    nuPercent := round((nu_I_no_quadrimestre*100)/nuRCL,2);
    nuPercent2:= round((nu_II_no_quadrimestre*100)/nuRCL,2);
    nuPercent3:= round((nuRCL*16)/100,2);
    nuPercent4:= round((nuRCL*7)/100,2);
    nuPercent5:= round((nuRCL*90)/100,2);


stSql :='
--# % das Operações de Crédito Internas e Externas sobre a RCL
    INSERT INTO tmp_relatorio values(1,''OPERAÇÕES VEDADAS'',0.00,0.00,''S'',2);
';

IF stExercicio >= '2016' THEN
    stSql := stSql || '    
    INSERT INTO tmp_relatorio values(2,''Do Período de Referência (III)'',0.00,0.00,''N'',2);
    INSERT INTO tmp_relatorio values(2,''De períodos Anteriores ao de Referência'',0.00,0.00,''N'',2); ';
END IF;

stSql := stSql || '
    INSERT INTO tmp_relatorio values(1,''TOTAL CONSIDERADO PARA FINS DA APURAÇÃO DO CUMPRIMENTO DO LIMITE (IV) = (Ia +III)'','||stSaldoContaAteQuadrimestre[1]||' + 0.00,(('||stSaldoContaAteQuadrimestre[1]||' / '||nuRCL||') * 100),''S'', 2);
    INSERT INTO tmp_relatorio values(1,''LIMITE GERAL DEFINIDO POR RESOLUÇÃO DO SENADO FEDERAL PARA AS OPERAÇÕES DE CRÉDITO INTERNAS E EXTERNAS '','||nuPercent3||','||round((nuPercent3/nuRCL)*100)||',''S'',2);
    INSERT INTO tmp_relatorio values(1,''LIMITE DE ALERTA (inciso III §1° do art.59 daLRF) - 90%'','||nuPercent5||','||round((nuPercent5/nuRCL)*100)||',''S'', 2);
    INSERT INTO tmp_relatorio values(1,''OPERAÇÕES DE CRÉDITO POR ANTECIPAÇÃO DA RECEITA ORÇAMENTÁRIA'','||nuPercent2||','||round((nuPercent2/nuRCL)*100)||',''S'', 2);
    INSERT INTO tmp_relatorio values(1,''LIMITE DEFINIDO POR RESOLUÇÃO DO SENADO FEDERAL PARA AS OPERAÇÕES DE CRÉDITO POR ANTECIPAÇÃO DA RECEITA ORÇAMENTÁRIA'','||nuPercent4||','||round((nuPercent4/nuRCL)*100)||',''S'', 2);
    INSERT INTO tmp_relatorio values(1,'''',''0'',''0'',''S'', 2);
    INSERT INTO tmp_relatorio values(1,''TOTAL CONSIDERADO PARA CONTRATAÇÃO DE NOVAS OPERAÇÕES DE CRÉDITO (V) = (IV + IIa)'',(('||stSaldoContaAteQuadrimestre[1]||' + 0.00) + ('||stSaldoContaAteQuadrimestre[2]||' + '||stSaldoContaNoQuadrimestre[3]||' + '||stSaldoContaNoQuadrimestre[4]||')), (('||stSaldoContaAteQuadrimestre[1]||' + 0.00) + ('||stSaldoContaAteQuadrimestre[2]||' + '||stSaldoContaNoQuadrimestre[3]||' + '||stSaldoContaNoQuadrimestre[4]||')) / ('||nuRCL||') *100,''S'', 2); ';
EXECUTE stSql;

stSql := 'SELECT nivel, item, valor_no_quadrimestre, valor_ate_quadrimestre,linha, tabela from tmp_relatorio;';

FOR reRegistro IN EXECUTE stSql
LOOP
    RETURN next reRegistro;
END LOOP; 


DROP TABLE tmp_relatorio;

RETURN;
END;

$$language 'plpgsql';