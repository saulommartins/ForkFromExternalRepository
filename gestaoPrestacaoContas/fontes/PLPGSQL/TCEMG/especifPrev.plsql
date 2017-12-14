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
    * Arquivo para a função que busca os dados do arquivo especifPrev
    * Data de Criação   : 03/02/2009

    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor André Machado

    * @package URBEM
    * @subpackage

    $Id: especifPrev.plsql 62821 2015-06-24 14:24:21Z jean $
*/
CREATE OR REPLACE FUNCTION tcemg.especifPrev(stExercicio varchar, dtInicio varchar, dtFinal varchar, stEntidades varchar) RETURNS SETOF RECORD AS $$
DECLARE
	stSql varchar;
	reRegistro RECORD;

    BEGIN

    stSql := 'CREATE TEMPORARY TABLE tmp_balancete_verificacao AS (
                SELECT *

                  FROM contabilidade.fn_rl_balancete_verificacao('|| quote_literal(stExercicio) ||',
                                                                 ''cod_entidade IN (' || stEntidades || ')'',
                                                                 '|| quote_literal(dtInicio) ||',
                                                                 '|| quote_literal(dtFinal) ||',
                                                                 ''A''
                                                                ) AS balancete
                        (
                            cod_estrutural         VARCHAR,
                            nivel                  INTEGER,
                            nom_conta              VARCHAR,
                            cod_sistema            INTEGER,
                            indicador_superavit    CHAR(12),
                            vl_saldo_anterior      NUMERIC,
                            vl_saldo_debitos       NUMERIC,
                            vl_saldo_creditos      NUMERIC,
                            vl_saldo_atual         NUMERIC
                        )
            )
        ';

    EXECUTE stSql;

    stSql := ' SELECT
                (
                (SELECT COALESCE(SUM(vl_saldo_atual),0.00) FROM tmp_balancete_verificacao WHERE cod_estrutural ilike ''1.1.1.1.1.50.00.00.00.00'')
                +
                (SELECT COALESCE(SUM(vl_saldo_atual),0.00) FROM tmp_balancete_verificacao WHERE cod_estrutural ilike ''1.1.4.0.0.00.00.00.00.00'')
                ) AS aplicacoes_financeiras,
                
                (SELECT COALESCE(SUM(vl_saldo_atual),0.00) FROM tmp_balancete_verificacao WHERE cod_estrutural ilike ''1.1.1.1.1.00.00.00.00.00''
                ) AS caixa,

                (SELECT COALESCE(SUM(vl_saldo_atual),0.00) FROM tmp_balancete_verificacao WHERE cod_estrutural ilike ''1.1.1.1.1.06.00.00.00.00''
                ) AS banco
            ';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;


    DROP TABLE tmp_balancete_verificacao;

    RETURN;

END;
$$ LANGUAGE 'plpgsql';
