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
CREATE OR REPLACE FUNCTION contabilidade.fn_gerar_restos_pagar_2013(VARCHAR, INTEGER) RETURNS VARCHAR AS $$
DECLARE
    stExercicio             ALIAS FOR $1;
    inCodEntidade           ALIAS FOR $2;

    stSql                   VARCHAR   := '';
    entidadeAtual           VARCHAR   := '';
    entidadeAnterior        VARCHAR   := '';
    inCodLote               INTEGER   := NULL;
    sequencia               VARCHAR   := '';
    stDtInicial             VARCHAR   := '';
    stDtFinal               VARCHAR   := '';
    flSomatorio             NUMERIC   := 0;
    stFiltro                VARCHAR   := '';
    reRegistro              RECORD;

BEGIN
    
    stDtInicial := '01/01/' || stExercicio;
    stDtFinal := '31/12/' || stExercicio;
    
    ---------------------------------------------------------
    -- Cria a Tabela que detem a apuração das contas --------
    -- 6.2.2.1.3.03.00.00.00.00 e 6.2.2.1.3.01.00.00.00.00 --
    ---------------------------------------------------------
    
    stFiltro := ' cod_entidade IN (' || inCodEntidade || ') AND cod_estrutural BETWEEN ''6.2.2.1.3.01.00.00.00.00'' AND ''6.2.2.1.3.03.00.00.00.00'' ';
    --
    CREATE TEMPORARY TABLE tmp_apuracao_saldo AS
        SELECT * FROM contabilidade.fn_rl_balancete_verificacao(stExercicio, stFiltro, stDtInicial, stDtFinal, 'A'::CHAR) AS registro
                        (cod_estrutural         varchar,
                         nivel                  integer,
                         nom_conta              varchar,
                         cod_sistema            integer,
                         indicador_superavit    char(12),
                         vl_saldo_anterior      numeric,
                         vl_saldo_debitos       numeric,
                         vl_saldo_creditos     numeric,
                         vl_saldo_atual         numeric
                        );
                        
    ---------------------------------------------------
    -- Faz lancamento dos restos a pagar nos codigos --
    -- 6.2.2.1.3.03 -> 5.3.2.7 e 6.3.2.7 --------------
    -- 6.2.2.1.3.01 -> 5.3.1.7 e 6.3.1.7.1 ------------
    ---------------------------------------------------
    
    stSql := 'SELECT
                     ' || quote_literal(stExercicio) || ' as exercicio,
                     ' || inCodEntidade || ' as cod_entidade,
                      CASE WHEN tmp_apuracao_saldo.cod_estrutural = ''6.2.2.1.3.03.00.00.00.00'' THEN
                                (SELECT cod_estrutural FROM contabilidade.plano_conta WHERE exercicio = ' || quote_literal(stExercicio) || ' AND cod_estrutural like ''5.3.2.7%'')
                           ELSE
                                (SELECT cod_estrutural FROM contabilidade.plano_conta WHERE exercicio = ' || quote_literal(stExercicio) || ' AND cod_estrutural like ''5.3.1.7%'')
                      END AS cod_estrutural_debito,
                      
                      CASE WHEN tmp_apuracao_saldo.cod_estrutural = ''6.2.2.1.3.03.00.00.00.00'' THEN
                                (SELECT cod_estrutural FROM contabilidade.plano_conta WHERE exercicio = ' || quote_literal(stExercicio) || ' AND cod_estrutural like ''6.3.2.7%'')
                           ELSE
                                (SELECT cod_estrutural FROM contabilidade.plano_conta WHERE exercicio = ' || quote_literal(stExercicio) || ' AND cod_estrutural like ''6.3.1.7.1%'')
                      END AS cod_estrutural_credito,
      
                    CASE WHEN tmp_apuracao_saldo.cod_estrutural = ''6.2.2.1.3.01.00.00.00.00'' THEN
                        CAST(''RP Não Processados a Liquidar'' AS VARCHAR)
                    ELSE
                        CAST(''RP Processados'' AS VARCHAR)
                    END AS complemento,
            
                tmp_apuracao_saldo.vl_saldo_atual
      
                FROM tmp_apuracao_saldo
               WHERE tmp_apuracao_saldo.cod_estrutural IN (''6.2.2.1.3.01.00.00.00.00'',''6.2.2.1.3.03.00.00.00.00'')
    ';
    
    FOR reRegistro IN EXECUTE stSql
    LOOP
        entidadeAtual := reRegistro.cod_entidade;
        IF(entidadeAtual != entidadeAnterior) THEN
            inCodLote := contabilidade.fn_insere_lote(
                              reRegistro.exercicio::VARCHAR -- stExercicio
                            , reRegistro.cod_entidade       -- inCodEntidade
                            , 'M'                           -- stTipo
                            , 'ENCERRAMENTO DO EXERCÍCIO'   -- stNomeLote
                            , '31/12/'||stExercicio||''     -- stDataLote
                        );
        END IF; 

        IF(reRegistro.vl_saldo_atual <> 0)
        THEN
            sequencia := FazerLancamento(reRegistro.cod_estrutural_debito,reRegistro.cod_estrutural_credito,800,reRegistro.exercicio::VARCHAR,abs(reRegistro.vl_saldo_atual),reRegistro.complemento,inCodLote,CAST('M' AS VARCHAR),reRegistro.cod_entidade);
        END IF;

    END LOOP;

    DROP TABLE tmp_apuracao_saldo;
    
    RETURN inCodEntidade;

END;
$$ language 'plpgsql';