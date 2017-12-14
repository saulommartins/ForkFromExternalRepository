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
*/

CREATE OR REPLACE FUNCTION tcemg.arquivo_divida_consolidada_rpps(varchar,varchar,varchar) RETURNS SETOF RECORD AS $$
DECLARE

stExercicio         ALIAS FOR $1;
stCodEntidade       ALIAS FOR $2;
stCodEntidadeRPPS   ALIAS FOR $3;
stDtInicial         VARCHAR := '';
stDtFinal           VARCHAR := '';
stSql               VARCHAR := '';
boIncluirRPPS       BOOLEAN;
boEntidadeRPPS      BOOLEAN;
arDatas             VARCHAR[];
reRegistro          RECORD;

BEGIN

stDtInicial := '01/01/'||stExercicio||'';
stDtFinal   := '31/12/'||stExercicio||'';

--Se cod_entidade RPPS nao e vazio
IF ( stCodEntidadeRPPS != '') THEN
    boIncluirRPPS := TRUE;
    --Se o cod entidade vier vazio significa que é a entidade RPPS
    IF ( stCodEntidade = '') THEN
        boEntidadeRPPS := TRUE;
    ELSE
        boEntidadeRPPS := FALSE;
    END IF;
ELSE
    boIncluirRPPS  := FALSE;
    boEntidadeRPPS := FALSE;
END IF;

IF ( boIncluirRPPS = TRUE ) THEN
    IF ( boEntidadeRPPS = TRUE ) THEN
        stSql := '  CREATE TEMPORARY TABLE tmp_divida_consolidada_rpps AS (
                        SELECT 
                                *
                        FROM stn.fn_rgf_anexo2_rpps_novo_mensal('''||stExercicio||''',''Ano'',0,'''||stCodEntidadeRPPS||''') AS tbl 
                        (  descricao varchar 
                         , ordem integer 
                         , valor_exercicio_anterior numeric 
                         , valor_mes numeric 
                         , nivel integer 
                        )
                    )
                ';    
        EXECUTE stSql;

        stSql :='   CREATE TEMPORARY TABLE tmp_balancete_verificacao_rpps AS (
                        SELECT
                                *
                        FROM contabilidade.fn_rl_balancete_verificacao('''||stExercicio||'''
                                                                        ,'' cod_entidade IN ('||stCodEntidade||')
                                                                            AND cod_estrutural ILIKE ''''2.1.2.1.1.03.10%'''' 
                                                                            OR cod_estrutural ILIKE ''''2.1.2.1.3.03.03%''''
                                                                            OR cod_estrutural ILIKE ''''2.1.2.2.1.03.10%'''' ''
                                                                        ,'''||stDtInicial||''','''||stDtFinal||''',''A''::CHAR)
                        as retorno( cod_estrutural varchar                                                      
                             ,nivel integer                                                               
                             ,nom_conta varchar                                                           
                             ,cod_sistema integer                                                         
                             ,indicador_superavit char(12)                                                
                             ,vl_saldo_anterior numeric                                                   
                             ,vl_saldo_debitos  numeric                                                   
                             ,vl_saldo_creditos numeric                                                   
                             ,vl_saldo_atual    numeric                                                   
                        )
                    )
                ';
        EXECUTE stSql;

        stSql := '  CREATE TEMPORARY TABLE tmp_divida_detalhamento_rpps AS (
                        SELECT 
                                *
                        FROM stn.fn_rgf_anexo2_detalhamento_divida_mensal('''||stExercicio||''',''Ano'',0,'''||stCodEntidadeRPPS||''') AS tbl 
                        (  descricao varchar 
                         , ordem integer 
                         , valor_exercicio_anterior numeric 
                         , valor_mes numeric 
                         , nivel integer 
                        )
                    )
                ';    
        EXECUTE stSql;

    --SE A ENTIDADE RPPS NAO FOR A UNICA SELECIONADA
    ELSE

        stSql := '  CREATE TEMPORARY TABLE tmp_divida_consolidada_rpps AS (
                        SELECT 
                                *
                        FROM stn.fn_rgf_anexo2_rpps_novo_mensal('''||stExercicio||''',''Ano'',0,'''||stCodEntidadeRPPS||''') AS tbl 
                        (  descricao varchar 
                         , ordem integer 
                         , valor_exercicio_anterior numeric 
                         , valor_mes numeric 
                         , nivel integer 
                        )
                    )
                ';    
        EXECUTE stSql;

        stSql :='   CREATE TEMPORARY TABLE tmp_balancete_verificacao_rpps AS (
                        SELECT
                                *
                        FROM contabilidade.fn_rl_balancete_verificacao('''||stExercicio||'''
                                                                        ,'' cod_entidade IN ('||stCodEntidade||')
                                                                            AND cod_estrutural ILIKE ''''2.1.2.1.1.03.10%'''' 
                                                                            OR cod_estrutural ILIKE ''''2.1.2.1.3.03.03%''''
                                                                            OR cod_estrutural ILIKE ''''2.1.2.2.1.03.10%'''' ''
                                                                        ,'''||stDtInicial||''','''||stDtFinal||''',''A''::CHAR)
                        as retorno( cod_estrutural varchar                                                      
                             ,nivel integer                                                               
                             ,nom_conta varchar                                                           
                             ,cod_sistema integer                                                         
                             ,indicador_superavit char(12)                                                
                             ,vl_saldo_anterior numeric                                                   
                             ,vl_saldo_debitos  numeric                                                   
                             ,vl_saldo_creditos numeric                                                   
                             ,vl_saldo_atual    numeric                                                   
                        )
                    )
                ';
        EXECUTE stSql;

        stSql := '  CREATE TEMPORARY TABLE tmp_divida_detalhamento_rpps AS (
                        SELECT 
                                *
                        FROM stn.fn_rgf_anexo2_detalhamento_divida_mensal('''||stExercicio||''',''Ano'',0,'''||stCodEntidadeRPPS||''') AS tbl 
                        (  descricao varchar 
                         , ordem integer 
                         , valor_exercicio_anterior numeric 
                         , valor_mes numeric 
                         , nivel integer 
                        )
                    )
                ';    
        EXECUTE stSql;

        stSql := '  CREATE TEMPORARY TABLE tmp_divida_consolidada AS (
                        SELECT 
                                *
                        FROM stn.fn_rgf_anexo2_novo_mensal('''||stExercicio||''',''Ano'',0,'''||stCodEntidade||''') AS tbl 
                        (  descricao varchar 
                         , ordem integer 
                         , valor_exercicio_anterior numeric 
                         , valor_mes numeric 
                         , nivel integer 
                        )
                    )
                ';    
        EXECUTE stSql;

        stSql :='   CREATE TEMPORARY TABLE tmp_balancete_verificacao AS (
                        SELECT
                                *
                        FROM contabilidade.fn_rl_balancete_verificacao('''||stExercicio||'''
                                                                        ,'' cod_entidade IN ('||stCodEntidade||')
                                                                            AND cod_estrutural ILIKE ''''2.1.2.1.1.03.10%'''' 
                                                                            OR cod_estrutural ILIKE ''''2.1.2.1.3.03.03%''''
                                                                            OR cod_estrutural ILIKE ''''2.1.2.2.1.03.10%'''' ''
                                                                        ,'''||stDtInicial||''','''||stDtFinal||''',''A''::CHAR)
                        as retorno( cod_estrutural varchar                                                      
                             ,nivel integer                                                               
                             ,nom_conta varchar                                                           
                             ,cod_sistema integer                                                         
                             ,indicador_superavit char(12)                                                
                             ,vl_saldo_anterior numeric                                                   
                             ,vl_saldo_debitos  numeric                                                   
                             ,vl_saldo_creditos numeric                                                   
                             ,vl_saldo_atual    numeric                                                   
                        )
                    )
                ';
        EXECUTE stSql;

        stSql := '  CREATE TEMPORARY TABLE tmp_divida_detalhamento AS (
                        SELECT 
                                *
                        FROM stn.fn_rgf_anexo2_detalhamento_divida_mensal('''||stExercicio||''',''Ano'',0,'''||stCodEntidade||''') AS tbl 
                        (  descricao varchar 
                         , ordem integer 
                         , valor_exercicio_anterior numeric 
                         , valor_mes numeric 
                         , nivel integer 
                        )
                    )
                ';    
        EXECUTE stSql;
    END IF;

--SE NAO HOUVER ENTIDADE RPPS
ELSE

    stSql := '  CREATE TEMPORARY TABLE tmp_divida_consolidada AS (
                        SELECT 
                                *
                        FROM stn.fn_rgf_anexo2_novo_mensal('''||stExercicio||''',''Ano'',0,'''||stCodEntidade||''') AS tbl 
                        (  descricao varchar 
                         , ordem integer 
                         , valor_exercicio_anterior numeric 
                         , valor_mes numeric 
                         , nivel integer 
                        )
                    )
                ';    
    EXECUTE stSql;

    stSql :='   CREATE TEMPORARY TABLE tmp_balancete_verificacao AS (
                        SELECT
                                *
                        FROM contabilidade.fn_rl_balancete_verificacao('''||stExercicio||'''
                                                                        ,'' cod_entidade IN ('||stCodEntidade||')
                                                                            AND cod_estrutural ILIKE ''''2.1.2.1.1.03.10%'''' 
                                                                            OR cod_estrutural ILIKE ''''2.1.2.1.3.03.03%''''
                                                                            OR cod_estrutural ILIKE ''''2.1.2.2.1.03.10%'''' ''
                                                                        ,'''||stDtInicial||''','''||stDtFinal||''',''A''::CHAR)
                        as retorno( cod_estrutural varchar                                                      
                             ,nivel integer                                                               
                             ,nom_conta varchar                                                           
                             ,cod_sistema integer                                                         
                             ,indicador_superavit char(12)                                                
                             ,vl_saldo_anterior numeric                                                   
                             ,vl_saldo_debitos  numeric                                                   
                             ,vl_saldo_creditos numeric                                                   
                             ,vl_saldo_atual    numeric                                                   
                        )
                    )
                ';
    EXECUTE stSql;

    stSql := '  CREATE TEMPORARY TABLE tmp_divida_detalhamento AS (
                        SELECT 
                                *
                        FROM stn.fn_rgf_anexo2_detalhamento_divida_mensal('''||stExercicio||''',''Ano'',0,'''||stCodEntidade||''') AS tbl 
                        (  descricao varchar 
                         , ordem integer 
                         , valor_exercicio_anterior numeric 
                         , valor_mes numeric 
                         , nivel integer 
                        )
                    )
                ';    
    EXECUTE stSql;
END IF;

--MOTANDO SELECT COM AS TABELAS TEMPORARIAS
IF ( boIncluirRPPS = TRUE ) THEN
    --SE A ENTIDADE RPPS FOR A UNICA SELECIONADA
    IF ( boEntidadeRPPS = TRUE ) THEN
        stSql :='   SELECT
                             CAST(0.00 AS NUMERIC(14,2)) as div_contratual_demais
                            ,CAST(0.00 AS NUMERIC(14,2)) as div_contratual_ppp
                            ,CAST(0.00 AS NUMERIC(14,2)) as div_mobiliaria
                            ,CAST(0.00 AS NUMERIC(14,2)) as op_credito_inf_12
                            ,CAST(0.00 AS NUMERIC(14,2)) as outras
                            ,CAST(0.00 AS NUMERIC(14,2)) as parc_contr_sociais_prev
                            ,CAST(0.00 AS NUMERIC(14,2)) as parc_contr_sociais_demais
                            ,CAST(0.00 AS NUMERIC(14,2)) as parc_tributos
                            ,CAST(0.00 AS NUMERIC(14,2)) as parc_fgts
                            ,CAST(0.00 AS NUMERIC(14,2)) as precatorios_post
                            ,(SELECT CAST(COALESCE(valor_mes,0.00) AS NUMERIC(14,2))     FROM tmp_divida_consolidada_rpps    WHERE descricao ILIKE ''%vida Contratual%'') as div_contratual_demais_rpps
                            ,CAST(0.00 AS NUMERIC(14,2)) as div_contratual_ppp_rpps
                            ,(SELECT CAST(COALESCE(valor_mes,0.00)  AS NUMERIC(14,2))    FROM tmp_divida_consolidada_rpps    WHERE descricao ILIKE ''%vida Mobili%'') as div_mobiliaria_rpps
                            ,(SELECT CAST(COALESCE(SUM(vl_saldo_atual),0.00) AS NUMERIC(14,2)) FROM tmp_balancete_verificacao_rpps ) as op_credito_inf_12_rpps
                            ,(SELECT CAST(COALESCE(valor_mes,0.00)  AS NUMERIC(14,2))    FROM tmp_divida_consolidada_rpps    WHERE descricao ILIKE ''Outras D%'') as outras_rpps
                            ,(SELECT CAST(COALESCE(valor_mes,0.00)  AS NUMERIC(14,2))    FROM tmp_divida_detalhamento_rpps   WHERE descricao ILIKE ''Previdenci%'') as parc_contr_sociais_prev_rpps
                            ,(SELECT CAST(COALESCE(valor_mes,0.00)  AS NUMERIC(14,2))    FROM tmp_divida_detalhamento_rpps   WHERE descricao ILIKE ''Demais Contribui%'') as parc_contr_sociais_demais_rpps
                            ,(SELECT CAST(COALESCE(valor_mes,0.00)  AS NUMERIC(14,2))    FROM tmp_divida_detalhamento_rpps   WHERE descricao ILIKE ''De Tributos%'') as parc_tributos_rpps
                            ,(SELECT CAST(COALESCE(valor_mes,0.00)  AS NUMERIC(14,2))    FROM tmp_divida_detalhamento_rpps   WHERE descricao ILIKE ''Do FGTS%'') as parc_fgts_rpps
                            ,(SELECT CAST(COALESCE(valor_mes,0.00)  AS NUMERIC(14,2))    FROM tmp_divida_consolidada_rpps    WHERE descricao ILIKE ''Precat%'') as precatorios_post_rpps
            ';
    --SE A ENTIDADE RPPS NAO FOR A UNICA SELECIONADA
    ELSE
        stSql :='   SELECT
                             (SELECT CAST(COALESCE(valor_mes,0.00) AS NUMERIC(14,2)) FROM tmp_divida_consolidada    WHERE descricao ILIKE ''%vida Contratual%'') as div_contratual_demais
                            ,CAST(0.00 AS NUMERIC(14,2)) as div_contratual_ppp
                            ,(SELECT CAST(COALESCE(valor_mes,0.00) AS NUMERIC(14,2)) FROM tmp_divida_consolidada    WHERE descricao ILIKE ''%vida Mobili%'') as div_mobiliaria
                            ,(SELECT CAST(COALESCE(SUM(vl_saldo_atual),0.00) AS NUMERIC(14,2)) FROM tmp_balancete_verificacao ) as op_credito_inf_12
                            ,(SELECT CAST(COALESCE(valor_mes,0.00) AS NUMERIC(14,2)) FROM tmp_divida_consolidada    WHERE descricao ILIKE ''Outras D%'') as outras
                            ,(SELECT CAST(COALESCE(valor_mes,0.00) AS NUMERIC(14,2)) FROM tmp_divida_detalhamento   WHERE descricao ILIKE ''Previdenci%'') as parc_contr_sociais_prev
                            ,(SELECT CAST(COALESCE(valor_mes,0.00) AS NUMERIC(14,2)) FROM tmp_divida_detalhamento   WHERE descricao ILIKE ''Demais Contribui%'') as parc_contr_sociais_demais
                            ,(SELECT CAST(COALESCE(valor_mes,0.00) AS NUMERIC(14,2)) FROM tmp_divida_detalhamento   WHERE descricao ILIKE ''De Tributos%'') as parc_tributos
                            ,(SELECT CAST(COALESCE(valor_mes,0.00) AS NUMERIC(14,2)) FROM tmp_divida_detalhamento   WHERE descricao ILIKE ''Do FGTS%'') as parc_fgts
                            ,(SELECT CAST(COALESCE(valor_mes,0.00) AS NUMERIC(14,2)) FROM tmp_divida_consolidada    WHERE descricao ILIKE ''Precat%'') as precatorios_post
                            --ENTIDADE RPPS
                            ,(SELECT CAST(COALESCE(valor_mes,0.00) AS NUMERIC(14,2))     FROM tmp_divida_consolidada_rpps    WHERE descricao ILIKE ''%vida Contratual%'') as div_contratual_demais_rpps
                            ,CAST(0.00 AS NUMERIC(14,2)) as div_contratual_ppp_rpps
                            ,(SELECT CAST(COALESCE(valor_mes,0.00)  AS NUMERIC(14,2))    FROM tmp_divida_consolidada_rpps    WHERE descricao ILIKE ''%vida Mobili%'') as div_mobiliaria_rpps
                            ,(SELECT CAST(COALESCE(SUM(vl_saldo_atual),0.00) AS NUMERIC(14,2)) FROM tmp_balancete_verificacao_rpps ) as op_credito_inf_12_rpps
                            ,(SELECT CAST(COALESCE(valor_mes,0.00)  AS NUMERIC(14,2))    FROM tmp_divida_consolidada_rpps    WHERE descricao ILIKE ''Outras D%'') as outras_rpps
                            ,(SELECT CAST(COALESCE(valor_mes,0.00)  AS NUMERIC(14,2))    FROM tmp_divida_detalhamento_rpps   WHERE descricao ILIKE ''Previdenci%'') as parc_contr_sociais_prev_rpps
                            ,(SELECT CAST(COALESCE(valor_mes,0.00)  AS NUMERIC(14,2))    FROM tmp_divida_detalhamento_rpps   WHERE descricao ILIKE ''Demais Contribui%'') as parc_contr_sociais_demais_rpps
                            ,(SELECT CAST(COALESCE(valor_mes,0.00)  AS NUMERIC(14,2))    FROM tmp_divida_detalhamento_rpps   WHERE descricao ILIKE ''De Tributos%'') as parc_tributos_rpps
                            ,(SELECT CAST(COALESCE(valor_mes,0.00)  AS NUMERIC(14,2))    FROM tmp_divida_detalhamento_rpps   WHERE descricao ILIKE ''Do FGTS%'') as parc_fgts_rpps
                            ,(SELECT CAST(COALESCE(valor_mes,0.00)  AS NUMERIC(14,2))    FROM tmp_divida_consolidada_rpps    WHERE descricao ILIKE ''Precat%'') as precatorios_post_rpps
            ';
    END IF;
--SE NAO HOUVER ENTIDADE RPPS
ELSE

    stSql :='   SELECT
                         (SELECT CAST(COALESCE(valor_mes,0.00) AS NUMERIC(14,2)) FROM tmp_divida_consolidada    WHERE descricao ILIKE ''%vida Contratual%'') as div_contratual_demais
                        ,CAST(0.00 AS NUMERIC(14,2)) as div_contratual_ppp
                        ,(SELECT CAST(COALESCE(valor_mes,0.00) AS NUMERIC(14,2)) FROM tmp_divida_consolidada    WHERE descricao ILIKE ''%vida Mobili%'') as div_mobiliaria
                        ,(SELECT CAST(COALESCE(SUM(vl_saldo_atual),0.00) AS NUMERIC(14,2)) FROM tmp_balancete_verificacao ) as op_credito_inf_12
                        ,(SELECT CAST(COALESCE(valor_mes,0.00) AS NUMERIC(14,2)) FROM tmp_divida_consolidada    WHERE descricao ILIKE ''Outras D%'') as outras
                        ,(SELECT CAST(COALESCE(valor_mes,0.00) AS NUMERIC(14,2)) FROM tmp_divida_detalhamento   WHERE descricao ILIKE ''Previdenci%'') as parc_contr_sociais_prev
                        ,(SELECT CAST(COALESCE(valor_mes,0.00) AS NUMERIC(14,2)) FROM tmp_divida_detalhamento   WHERE descricao ILIKE ''Demais Contribui%'') as parc_contr_sociais_demais
                        ,(SELECT CAST(COALESCE(valor_mes,0.00) AS NUMERIC(14,2)) FROM tmp_divida_detalhamento   WHERE descricao ILIKE ''De Tributos%'') as parc_tributos
                        ,(SELECT CAST(COALESCE(valor_mes,0.00) AS NUMERIC(14,2)) FROM tmp_divida_detalhamento   WHERE descricao ILIKE ''Do FGTS%'') as parc_fgts
                        ,(SELECT CAST(COALESCE(valor_mes,0.00) AS NUMERIC(14,2)) FROM tmp_divida_consolidada    WHERE descricao ILIKE ''Precat%'') as precatorios_post
                        --ENTIDADE RPPS
                        ,CAST(0.00 AS NUMERIC(14,2)) as div_contratual_demais_rpps
                        ,CAST(0.00 AS NUMERIC(14,2)) as div_contratual_ppp_rpps
                        ,CAST(0.00 AS NUMERIC(14,2)) as div_mobiliaria_rpps
                        ,CAST(0.00 AS NUMERIC(14,2)) as op_credito_inf_12_rpps
                        ,CAST(0.00 AS NUMERIC(14,2)) as outras_rpps
                        ,CAST(0.00 AS NUMERIC(14,2)) as parc_contr_sociais_prev_rpps
                        ,CAST(0.00 AS NUMERIC(14,2)) as parc_contr_sociais_demais_rpps
                        ,CAST(0.00 AS NUMERIC(14,2)) as parc_tributos_rpps
                        ,CAST(0.00 AS NUMERIC(14,2)) as parc_fgts_rpps
                        ,CAST(0.00 AS NUMERIC(14,2)) as precatorios_post_rpps
            ';
END IF;

FOR reRegistro IN EXECUTE stSql
LOOP
    RETURN next reRegistro;
END LOOP;

DROP TABLE IF EXISTS tmp_divida_consolidada;
DROP TABLE IF EXISTS tmp_balancete_verificacao;
DROP TABLE IF EXISTS tmp_divida_detalhamento;
DROP TABLE IF EXISTS tmp_divida_consolidada_rpps;
DROP TABLE IF EXISTS tmp_balancete_verificacao_rpps;
DROP TABLE IF EXISTS tmp_divida_detalhamento_rpps;

END;
$$ language 'plpgsql';