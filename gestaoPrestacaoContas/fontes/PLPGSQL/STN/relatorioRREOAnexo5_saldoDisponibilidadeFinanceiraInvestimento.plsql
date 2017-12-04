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
    * Script de função PLPGSQL - Relatório STN - RREO - Anexo 5
    * Data de Criação   : 10/06/2008


    * @author Analista Alexandre Melo
    * @author Desenvolvedor Henrique Girardi dos Santos
    
    * @package URBEM
    * @subpackage 

    * @ignore

    * Casos de uso : uc-06.01.04

    $Id: OCGeraRREOAnexo5.php 28716 2008-03-27 15:28:33Z lbbarreiro $
*/


CREATE OR REPLACE FUNCTION stn.fn_rreo_anexo5_saldo_disponivel(varchar, varchar, varchar, varchar, varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio             ALIAS FOR $1;
    stCodEntidades          ALIAS FOR $2;
    stRelatorioNovo         ALIAS FOR $3;
    dtInicial               ALIAS FOR $4;
    dtFinal                 ALIAS FOR $5;
    dtInicioAno             VARCHAR := '';
    dtFimAno                VARCHAR := '';
    stSql                   VARCHAR := '';
    reRegistro              RECORD;
    stExercicioAnterior     VARCHAR := '';
    dtInicioAnoAnterior     VARCHAR := '';
    dtFimAnoAnterior        VARCHAR := '';
    dtFimBimestre           VARCHAR := '';
    stEstruturalBancosAnterior varchar := '';
    stEstruturalInvestimentosAnterior varchar := '';
    arDatas varchar[] ;
BEGIN
        
    stExercicioAnterior :=  trim(to_char((to_number(stExercicio, '99999')-1), '99999'));
    
    dtInicioAno := '01/01/' || stExercicio;
    dtFimAno := '31/12/' || stExercicio;
    
    dtInicioAnoAnterior := '01/01/' || stExercicioAnterior;
    dtFimAnoAnterior := dtInicial;

    IF stRelatorioNovo = 'sim' THEN

        IF stExercicioAnterior::INTEGER <= 2012 THEN
            stEstruturalInvestimentosAnterior := ' plano_conta.cod_estrutural like ''''1.1.1.1.3%'''' ';
            stEstruturalBancosAnterior := ' plano_conta.cod_estrutural like ''''1.1.1.1.2%'''' ';
        ELSE
            stEstruturalInvestimentosAnterior := ' (plano_conta.cod_estrutural like ''''1.1.1.1.1.50%'''' OR plano_conta.cod_estrutural like ''''1.1.4%'''') ';
            stEstruturalBancosAnterior := ' (plano_conta.cod_estrutural like ''''1.1.1.1.1.19%'''' OR plano_conta.cod_estrutural like ''''1.1.1.12.08%'''') ';
        END IF;
        
    	stSql := '
                
                SELECT  CAST(''CAIXA'' AS VARCHAR) AS descricao
                    , stn.pl_saldo_contas (     ''' || stExercicio || '''
                                            , ''' || dtInicioAno || '''
                                            , ''' || dtFinal || '''
                                            , '' plano_conta.cod_estrutural like ''''1.1.1.1.1.01%'''' ''
                                            , ''' || stCodEntidades|| '''
                                            , ''''
                    ) as vl_final_atual
                    , stn.pl_saldo_contas (     ''' || stExercicioAnterior || '''
                                            , ''' || dtInicioAnoAnterior || '''
                                            , ''' || dtFimAnoAnterior || '''
                                            , '' plano_conta.cod_estrutural like ''''1.1.1.1.1.01%'''' ''
                                            , ''' || stCodEntidades|| '''
                                            , ''''
                    ) as vl_final_anterior
                    , stn.pl_saldo_contas (     ''' || stExercicio || '''
                                            , ''' || dtInicioAno || '''
                                            , ''' || dtFinal || '''
                                            , '' plano_conta.cod_estrutural like ''''1.1.1.1.1.01%'''' ''
                                            , ''' || stCodEntidades|| '''
                                            , ''''
                    ) as vl_final_bimestre
                    
                UNION ALL
                
                SELECT  CAST(''BANCOS CONTA MOVIMENTO'' AS VARCHAR) AS descricao
                    , stn.pl_saldo_contas (     ''' || stExercicio || '''
                                            , ''' || dtInicioAno || '''
                                            , ''' || dtFinal || '''
                                            , '' (plano_conta.cod_estrutural like ''''1.1.1.1.1.19%'''' OR plano_conta.cod_estrutural like ''''1.1.1.12.08%'''') ''
                                            , ''' || stCodEntidades|| '''
                                            , ''''
                    ) as vl_final_atual
                    , stn.pl_saldo_contas (     ''' || stExercicioAnterior || '''
                                            , ''' || dtInicioAnoAnterior || '''
                                            , ''' || dtFimAnoAnterior || '''
                                            , '''||stEstruturalBancosAnterior||'''
                                            , ''' || stCodEntidades|| '''
                                            , ''''
                    ) as vl_final_anterior
                    , stn.pl_saldo_contas (     ''' || stExercicio || '''
                                            , ''' || dtInicioAno || '''
                                            , ''' || dtFinal || '''
                                            , '' (plano_conta.cod_estrutural like ''''1.1.1.1.1.19%'''' OR plano_conta.cod_estrutural like ''''1.1.1.1.1.05%'''') ''
                                            , ''' || stCodEntidades|| '''
                                            , ''''
                    ) as vl_final_bimestre
                    
                UNION ALL
                
                SELECT  CAST(''INVESTIMENTOS'' AS VARCHAR) AS descricao
                    , stn.pl_saldo_contas (     ''' || stExercicio || '''
                                            , ''' || dtInicioAno || '''
                                            , ''' || dtFinal || '''
                                            , '' (plano_conta.cod_estrutural like ''''1.1.1.1.1.50%'''' OR plano_conta.cod_estrutural like ''''1.1.4%'''') ''
                                            , ''' || stCodEntidades|| '''
                                            , ''''
                    ) as vl_final_atual
                    , stn.pl_saldo_contas (     ''' || stExercicioAnterior || '''
                                            , ''' || dtInicioAnoAnterior || '''
                                            , ''' || dtFimAnoAnterior || '''
                                            , ''' ||stEstruturalInvestimentosAnterior|| '''
                                            , ''' || stCodEntidades|| '''
                                            , ''''
                    ) as vl_final_anterior
                    , stn.pl_saldo_contas (     ''' || stExercicio || '''
                                            , ''' || dtInicioAno || '''
                                            , ''' || dtFinal || '''
                                            , '' (plano_conta.cod_estrutural like ''''1.1.1.1.1.50%'''' OR plano_conta.cod_estrutural like ''''1.1.4%'''') AND lancamento.tipo = ''''I'''' ''
                                            , ''' || stCodEntidades|| '''
                                            , ''''
                    ) as vl_final_bimestre
    
    	    UNION ALL
    
                    SELECT  CAST(''OUTROS BENS E DIREITOS'' AS VARCHAR) AS descricao
                         ,  CAST(0.00 AS NUMERIC) AS vl_final_atual
                         ,  CAST(0.00 AS NUMERIC) AS vl_final_anterior
                         ,  CAST(0.00 AS NUMERIC) AS vl_final_bimestre
            ';
    ELSE
        stSql := '
            
            SELECT  CAST(''Caixa'' AS VARCHAR) AS descricao
                , stn.pl_saldo_contas (     ''' || stExercicio || '''
                                        , ''' || dtInicioAno || '''
                                        , ''' || dtFinal || '''
                                        , '' plano_conta.cod_estrutural like ''''1.1.1.1.1%'''' ''
                                        , ''' || stCodEntidades|| '''
                                        , ''''
                ) as vl_final_atual
                , stn.pl_saldo_contas (     ''' || stExercicioAnterior || '''
                                        , ''' || dtInicioAnoAnterior || '''
                                        , ''' || dtFimAnoAnterior || '''
                                        , '' plano_conta.cod_estrutural like ''''1.1.1.1.1%'''' ''
                                        , ''' || stCodEntidades|| '''
                                        , ''''
                ) as vl_final_anterior
                , stn.pl_saldo_contas (     ''' || stExercicio || '''
                                        , ''' || dtInicioAno || '''
                                        , ''' || dtFinal || '''
                                        , '' plano_conta.cod_estrutural like ''''1.1.1.1.1%'''' ''
                                        , ''' || stCodEntidades|| '''
                                        , ''''
                ) as vl_final_bimestre
                
            UNION ALL
            
            SELECT  CAST(''Bancos Conta Movimento'' AS VARCHAR) AS descricao
                , stn.pl_saldo_contas (     ''' || stExercicio || '''
                                        , ''' || dtInicioAno || '''
                                        , ''' || dtFinal || '''
                                        , '' plano_conta.cod_estrutural like ''''1.1.1.1.2%'''' ''
                                        , ''' || stCodEntidades|| '''
                                        , ''''
                ) as vl_final_atual
                , stn.pl_saldo_contas (     ''' || stExercicioAnterior || '''
                                        , ''' || dtInicioAnoAnterior || '''
                                        , ''' || dtFimAnoAnterior || '''
                                        , '' plano_conta.cod_estrutural like ''''1.1.1.1.2%'''' ''
                                        , ''' || stCodEntidades|| '''
                                        , ''''
                ) as vl_final_anterior
                , stn.pl_saldo_contas (     ''' || stExercicio || '''
                                        , ''' || dtInicioAno || '''
                                        , ''' || dtFinal || '''
                                        , '' plano_conta.cod_estrutural like ''''1.1.1.1.2%'''' ''
                                        , ''' || stCodEntidades|| '''
                                        , ''''
                ) as vl_final_bimestre
                
            UNION ALL
            
            SELECT  CAST(''Investimentos'' AS VARCHAR) AS descricao
                , stn.pl_saldo_contas (     ''' || stExercicio || '''
                                        , ''' || dtInicioAno || '''
                                        , ''' || dtFinal || '''
                                        , '' plano_conta.cod_estrutural like ''''1.1.5%'''' ''
                                        , ''' || stCodEntidades|| '''
                                        , ''''
                ) as vl_final_atual
                , stn.pl_saldo_contas (     ''' || stExercicioAnterior || '''
                                        , ''' || dtInicioAnoAnterior || '''
                                        , ''' || dtFimAnoAnterior || '''
                                        , '' plano_conta.cod_estrutural like ''''1.1.5%'''' ''
                                        , ''' || stCodEntidades|| '''
                                        , ''''
                ) as vl_final_anterior
                , stn.pl_saldo_contas (     ''' || stExercicio || '''
                                        , ''' || dtInicioAno || '''
                                        , ''' || dtFinal || '''
                                        , '' plano_conta.cod_estrutural like ''''1.1.5%'''' ''
                                        , ''' || stCodEntidades|| '''
                                        , ''''
                ) as vl_final_bimestre
        ';
    END IF;
    
    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;


    RETURN;
END;
$$ language 'plpgsql';
