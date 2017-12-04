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

CREATE OR REPLACE FUNCTION stn.fn_rgf_anexo4_novo_mensal(varchar,varchar,varchar,varchar) RETURNS SETOF RECORD AS $$
DECLARE

    stExercicio     ALIAS FOR $1;
    stDtInicial     ALIAS FOR $2;
    stDtFinal       ALIAS FOR $3;
    stCodEntidades  ALIAS FOR $4;
    
    nuRCL                   NUMERIC;
    nu_I_no_mes    NUMERIC;
    nu_I_ate_mes   NUMERIC;
    nu_II_no_mes   NUMERIC;
    nu_II_ate_mes  NUMERIC;
    nuPercent               NUMERIC;
    nuPercent2              NUMERIC;
    nuPercent3              NUMERIC;
    nuPercent4              NUMERIC;
    flValorRCL              NUMERIC(14,2) := 0.00;
    crCursor                REFCURSOR;

    arEntidades         VARCHAR[];
    stEntidadesCamara   VARCHAR := '';
    inEntidadeCamara    INTEGER;
    inCount             INTEGER;

    stSql               VARCHAR   := '';
    stSqlAux            VARCHAR   := '';
    reRegistro          RECORD;

BEGIN   

stSql := '
-- # Tabela para os dados Balancete Receita Valores
    CREATE TEMPORARY TABLE tmp_balancete_valores (
        cod_estrutural              varchar,                                           
        receita                          integer,                                           
        recurso                         varchar,                                           
        descricao                      varchar,                                           
        valor_previsto               numeric,                                           
        valor_no_mes       numeric,                                           
        valor_ate_mes      numeric,                                           
        diferenca                      numeric                     
    )';
    EXECUTE stSql;

  stSql := '
    INSERT INTO tmp_balancete_valores (cod_estrutural,receita, recurso , descricao, valor_previsto,valor_no_mes, valor_ate_mes,diferenca )                     
       select * 
         from orcamento.fn_balancete_receita('''||stExercicio||''', '''','''||stDtInicial||''','''||stDtFinal||''','''||stCodEntidades||''','''','''','''','''','''','''','''') as retorno(                      
            cod_estrutural          varchar,                                           
            receita                      integer,                                           
            recurso                     varchar,                                           
            descricao                  varchar,                                           
            valor_previsto            numeric,                                           
            valor_no_mes    numeric,                                           
            valor_ate_mes   numeric,                                          
            diferenca                   numeric                                           
        )';
   EXECUTE stSql;

              
stSql := '
-- # Tabela para os dados Definitivos do Relatório
    CREATE TEMPORARY TABLE tmp_relatorio (
        nivel                         INTEGER,
        item                         VARCHAR,
        valor_no_mes  NUMERIC,
        valor_ate_mes NUMERIC,
        linha                        CHAR,
        tabela                      INTEGER
    )
    ';

EXECUTE stSql;

stSql :='
-- # Sujeitas ao Limite para Fins de Contratação Externas + Internas(I)
    SELECT coalesce(sum(valor_no_mes),0.00)              
    FROM tmp_balancete_valores 
    WHERE cod_estrutural like ''2.1%'';
    ';
    OPEN  crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO nu_I_no_mes;
     CLOSE crCursor;

stSql :='
    SELECT coalesce(sum(valor_ate_mes),0.00)              
    FROM tmp_balancete_valores 
    WHERE cod_estrutural like ''2.1%'';
    ';
    OPEN  crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO nu_I_ate_mes;
     CLOSE crCursor;

stSql := '
    INSERT INTO tmp_relatorio values(1,''SUJEITAS AO LIMITE PARA FINS DE CONTRATAÇÃO (I)'','||nu_I_no_mes||','||nu_I_ate_mes||',''N'',1);

-- #    Mobiliaria
    INSERT INTO tmp_relatorio values(2
                                                        ,''Mobiliaria''
                                                        ,(SELECT coalesce(sum(valor_no_mes),0.00) FROM tmp_balancete_valores WHERE cod_estrutural like ''2.1.1%'')
                                                        ,(SELECT coalesce(sum(valor_ate_mes),0.00) FROM tmp_balancete_valores WHERE cod_estrutural like ''2.1.1%'')
                                                        ,''N''
                                                        , 1);

-- #        Internas
    INSERT INTO tmp_relatorio values(3
                                                        ,''Interna''
                                                        ,NULL
                                                        ,NULL
                                                        ,''N''
                                                        , 1);

-- #            Identificação das Operações de Crédito Internas
 /*   INSERT INTO tmp_relatorio (nivel,item,valor_no_mes,valor_ate_mes,linha,tabela)
        SELECT
            3         as nivel,
            descricao as item,
            coalesce(valor_no_mes,0.00),
            coalesce(valor_ate_mes,0.00),
            ''N'' as linha,
            1 as tabela
        FROM tmp_balancete_valores
        WHERE cod_estrutural like ''2.1.1%'';
*/

-- #        Externas
    INSERT INTO tmp_relatorio values(3
                                                        , ''Externa''
                                                        ,NULL
                                                        ,NULL
                                                        ,''N''
                                                        , 1);    

-- #            Identificação das Operações de Crédito Externas
 /*   INSERT INTO tmp_relatorio (nivel,item,valor_no_mes,valor_ate_mes,linha, tabela)
        SELECT 
                3         as nivel,
                descricao as item,
                coalesce(valor_no_mes,0.00),
                coalesce(valor_ate_mes,0.00),
                ''N'' as linha
                1 as tabela
        FROM tmp_balancete_valores 
        WHERE cod_estrutural like ''2.1.2%''; */

 
--# Por enquanto os valores a seguir não tem no sistema os códigos estruturais então os valores ficarão 0,00 

-- #    Contratural
    INSERT INTO tmp_relatorio values(2
                                                        ,''Contratual''
                                                        ,0.00 
                                                        ,0.00
                                                        ,''N''
                                                        , 1);

-- #        Internas
    INSERT INTO tmp_relatorio values(3
                                                        ,''Interna''
                                                        ,0.00
                                                        ,0.00
                                                        ,''N''
                                                        , 1);
-- #            Abertura de Crédito
    INSERT INTO tmp_relatorio values(4
                                                        ,''Abertura de Crédito''
                                                        ,NULL
                                                        ,NULL
                                                        ,''N''
                                                        , 1);

-- #            Aquisicao Financiada de Bens e Arrendamento Mercantil Financeiro
    INSERT INTO tmp_relatorio values(4
                                                        ,''Aquisição Financiada de Bens e Arrendamento Mercantil Financeiro''
                                                        ,0.00
                                                        ,0.00
                                                        ,''N''
                                                        , 1);
-- #                Derivadas de PPP
    INSERT INTO tmp_relatorio values(5
                                                        ,''Derivadas de PPP''
                                                        ,NULL
                                                        ,NULL
                                                        ,''N''
                                                        , 1);

-- #                Demais Aquisições Financiadas
    INSERT INTO tmp_relatorio values(5
                                                        ,''Demais Aquisições Financiadas''
                                                        ,NULL
                                                        ,NULL
                                                        ,''N''
                                                        , 1);

-- #        Antecipação de Receita
    INSERT INTO tmp_relatorio values(4
                                                        ,''Antecipação de Receita''
                                                        ,0.00
                                                        ,0.00
                                                        ,''N''
                                                        , 1);

-- #                Pela Venda a Termo de Bens e Serviços
    INSERT INTO tmp_relatorio values(5
                                                        ,''Pela Venda a Termo de Bens e Serviços''
                                                        ,NULL
                                                        ,NULL
                                                        ,''N''
                                                        , 1);

-- #                Demais Antecipações de Receita
    INSERT INTO tmp_relatorio values(5
                                                        ,''Demais Antecipações de Receita''
                                                        ,NULL
                                                        ,NULL
                                                        ,''N''
                                                        , 1);
-- #        Assunção, Reconhecimento e Confissão  de Dividas(LRF, art. 29, )
    INSERT INTO tmp_relatorio values(4
                                                        ,''Assunção, Reconhecimento e Confissão  de Dividas(LRF, art. 29, )''
                                                        ,NULL
                                                        ,NULL
                                                        ,''N''
                                                        , 1);

-- #        Outras Operações de Crédito
    INSERT INTO tmp_relatorio values(4
                                                        ,''Outras Operações de Crédito''
                                                        ,NULL
                                                        ,NULL
                                                        ,''N''
                                                        , 1);

-- #        Externa
    INSERT INTO tmp_relatorio values(3
                                                        ,''Externa''
                                                        ,NULL
                                                        ,NULL
                                                        ,''S''
                                                        , 1);

-- # Não sujeitas ao limite para fins de contratação (II)
    INSERT INTO tmp_relatorio values(1
                                                        ,''NÃO SUJEITAS AO LIMITE PARA FINS DE CONTRATAÇÃO(II)''
                                                        ,0.00
                                                        ,0.00
                                                        ,''N''
                                                        , 1);

-- #    Parcelamento de Dividas 
    INSERT INTO tmp_relatorio values(2
                                                        ,''Parcelamentos de Dividas''
                                                        ,0.00
                                                        ,0.00
                                                        ,''N''
                                                        , 1);

-- #        De Tributos
    INSERT INTO tmp_relatorio values(3
                                                        ,''De Tributos''
                                                        ,NULL
                                                        ,NULL
                                                        ,''N''
                                                        , 1);


-- #        De Contribuições Sociais
    INSERT INTO tmp_relatorio values(3
                                                        ,''De Contribuições Sociais''
                                                        ,0.00
                                                        ,0.00
                                                        ,''N''
                                                        , 1);

-- #            Previdenciárias 
    INSERT INTO tmp_relatorio values(4
                                                        ,''Previdenciárias''
                                                        ,NULL
                                                        ,NULL
                                                        ,''N''
                                                        , 1);


-- #            Demais Contribuições Sociais
    INSERT INTO tmp_relatorio values(4
                                                        ,''Demais Contribuições Sociais''
                                                        ,NULL
                                                        ,NULL
                                                        ,''N''
                                                        , 1);

-- #        Do FGTS
    INSERT INTO tmp_relatorio values(3
                                                        ,''Do FGTS''
                                                        ,NULL
                                                        ,NULL
                                                        ,''N''
                                                        , 1);

-- #    Melhoria da Administração de Receitas e da Gestão Fiscal, Financeira e Patrimonial
    INSERT INTO tmp_relatorio values(2
                                                        ,''Melhoria da Administração de Receitas e da Gestão Fiscal, Financeira e Patrimonial''
                                                        ,NULL
                                                        ,NULL
                                                        ,''N''
                                                        , 1);

-- #    Programa de Iluminação Pública - RELUZ
    INSERT INTO tmp_relatorio values(2
                                                        ,''Programa de Iluminação Pública - RELUZ''
                                                        ,NULL
                                                        ,NULL
                                                        ,''N''
                                                        , 1);

-- #    Outras Operações de Crédito Não Sujeistas ao Limite
    INSERT INTO tmp_relatorio values(2
                                                        ,''Outras Operações de Crédito Não Sujeistas ao Limite''
                                                        ,NULL
                                                        ,NULL
                                                        ,''N''
                                                        , 1);
';
EXECUTE stSql;





stSql :='
-- # Não Sujeitas ao LimitePor Antecipação da Receita (II)
    SELECT coalesce(sum(valor_no_mes),0.00) 
    FROM tmp_balancete_valores 
    WHERE cod_estrutural like ''2.1.2.2.2.02.07%'';
    ';

    OPEN  crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO nu_II_no_mes;
    CLOSE crCursor;

stSql :='
-- # Por Antecipação da Receita (II)
    SELECT coalesce(sum(valor_ate_mes),0.00) 
    FROM tmp_balancete_valores 
    WHERE cod_estrutural like ''2.1.2.2.2.02.07%'';
    ';

    OPEN  crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO nu_II_ate_mes;
    CLOSE crCursor;

stSql :='
-- ## RCL do Bruce | Anexo III - RREO
    SELECT
        sum(cast( ( total_mes_1 + total_mes_2 + total_mes_3 + total_mes_4  + total_mes_5  + total_mes_6 
                   + total_mes_7 + total_mes_8 + total_mes_9 + total_mes_10 + total_mes_11 + total_mes_12 ) as numeric(14,2))) as valor
    FROM stn.pl_total_subcontas ('''||stDtFinal||''') as retorno (
                                                                  ordem      integer
                                                                 ,cod_conta      varchar
                                                                 ,descricao      varchar
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
    ';

    OPEN  crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO nuRCL;
    CLOSE crCursor;

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

    nuRCL := nuRCL + flValorRCL;

    stSql := 'INSERT INTO tmp_relatorio values(1,''RECEITA CORRENTE LÍQUIDA - RCL'','||nuRCL||',0.00,''S'',2)';
    EXECUTE stSql;

    nuPercent := round((nu_I_no_mes*100)/nuRCL,2);
    nuPercent2:= round((nu_II_no_mes*100)/nuRCL,2);

    nuPercent3:= round((nuRCL*16)/100,2);
    nuPercent4:= round((nuRCL*7)/100,2);


stSql :='
-- ## % das Operações de Crédito Internas e Externas sobre a RCL
    INSERT INTO tmp_relatorio values(1,''OPERAÇÕES VEDADAS(III)'',NULL,NULL,''S'',2);
';
IF stExercicio >= '2016' THEN
    stSql :=stSql||'    

    INSERT INTO tmp_relatorio values(2,''Do Período de Referência (III)'',0.00,0.00,''N'',2);

    INSERT INTO tmp_relatorio values(2,''De períodos Anteriores ao de Referência'',0.00,0.00,''N'',2);
    
    ';
END IF;

stSql :=stSql||'
    
    INSERT INTO tmp_relatorio values(1,''TOTAL CONSIDERADO PARA FINS DA APURAÇÃO DO CUMPRIMENTO DO LIMITE (IV) = (Ia +III)'',0.00,NULL,''S'', 2);

    INSERT INTO tmp_relatorio values(1,''LIMITE GERAL DEFINIDO POR RESOLUÇÃO DO SENADO FEDERAL PARA AS OPERAÇÕES DE CRÉDITO INTERNAS E EXTERNAS '','||nuPercent3||','||nu_II_no_mes||',''S'',2);
    
    INSERT INTO tmp_relatorio values(1,''LIMITE DE ALERTA (inciso III §1° do art.59 daLRF) - 90%'',NULL,NULL,''S'',2);

    INSERT INTO tmp_relatorio values(1,''OPERAÇÕES DE CRÉDITO POR ANTECIPAÇÃO DA RECEITA ORÇAMENTÁRIA'','||nuPercent2||','||nu_II_no_mes||',''S'', 2);

    INSERT INTO tmp_relatorio values(1,''LIMITE DEFINIDO POR RESOLUÇÃO DO SENADO FEDERAL PARA AS OPERAÇÕES DE CRÉDITO POR ANTECIPAÇÃO DA RECEITA ORÇAMENTÁRIA'','||nuPercent4||','||nu_II_no_mes||',''S'', 2);

    INSERT INTO tmp_relatorio values(1,'''',''0'',''0'',''S'', 2);

    INSERT INTO tmp_relatorio values(1,''TOTAL CONSIDERADO PARA CONTRATAÇÃO DE NOVAS OPERAÇÕES DE CRÉDITO (V) = (IV + IIa)'',0.00,0.00,''S'', 2);
   ';     

EXECUTE stSql; 

stSql := 'SELECT nivel, item, valor_no_mes, valor_ate_mes,linha, tabela from tmp_relatorio;';

FOR reRegistro IN EXECUTE stSql
LOOP
    RETURN next reRegistro;
END LOOP; 


drop table tmp_balancete_valores;
drop table tmp_relatorio;

RETURN;
END;

$$language 'plpgsql';

