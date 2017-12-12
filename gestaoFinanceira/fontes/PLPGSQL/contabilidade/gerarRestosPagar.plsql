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
 * funcao para gerar os restos a pagar
 * Data de Criação   : 22/12/2008 Nacional de Municípios

 * @author Analista      Tonismar Régis Bernardo
 * @author Desenvolvedor Henrique Boaventura
 
 * @package URBEM

 *  $Id: gerarRestosPagar.plsql 59612 2014-09-02 12:00:51Z gelson $
*/

CREATE OR REPLACE FUNCTION contabilidade.fn_gerar_restos_pagar(VARCHAR) RETURNS VARCHAR AS $$
DECLARE
    stExercicio             ALIAS FOR $1;

    stSql                   VARCHAR   := '';
    stCodEntidade           VARCHAR   := '';
    entidadeAtual           VARCHAR   := '';
    entidadeAnterior        VARCHAR   := '';
    inCodLote               INTEGER   := NULL;
    sequencia               VARCHAR   := '';
    flSomatorio             NUMERIC   := 0;
    reRegistro              RECORD;
    stSqlAux                VARCHAR   := '';

BEGIN

    -------------------------------------
    -- Retorna as entidades do sistema --
    -------------------------------------
    SELECT ARRAY_TO_STRING(ARRAY(SELECT CAST(entidade.cod_entidade AS VARCHAR) 
                                   FROM orcamento.entidade
                                  WHERE entidade.exercicio = stExercicio
                                    AND EXISTS ( SELECT 1
                                                   FROM contabilidade.conta_lancamento_rp
                                                  WHERE entidade.exercicio    = conta_lancamento_rp.exercicio
                                                    AND entidade.cod_entidade = conta_lancamento_rp.cod_entidade
                                               )
                           ),',')
      INTO stCodEntidade;

    -----------------------------------------------------------------------------
    -- Cria uma tabela temporaria com os valores nao processados e processados --
    -----------------------------------------------------------------------------
    CREATE TEMPORARY TABLE tmp_valores_insuficiencia AS
        SELECT retorno.*
             , buscaCodigoEstrutural(retorno.exercicio,retorno.cod_plano_debito) AS cod_estrutural
          FROM stn.fn_rel_rgf6_emp_liq_exercicio_recurso_empenho( stCodEntidade::varchar,
                                                                   stExercicio, 
                                                                   '01/01/' || stExercicio, 
                                                                   '31/12/' || stExercicio, 
                                                                   '31/12/' || stExercicio 
                                                                 ) as retorno
             ( cod_empenho integer,
               cod_recurso integer,
               nom_recurso varchar,
               cod_entidade integer, 
               exercicio varchar, 
               cod_plano_debito varchar,
               liquidados_nao_pagos numeric, 
               empenhados_nao_liquidados numeric
             )
             
        WHERE cod_estrutural LIKE '6.2.2.1.3.03%' OR cod_estrutural LIKE '6.2.2.1.3.01%';
        
        IF (stExercicio > '2013') THEN -- identifica se ja foi liquidado
            stSql := 'SELECT * FROM tmp_valores_insuficiencia
                               JOIN empenho.empenho
                                 ON empenho.exercicio = tmp_valores_insuficiencia.exercicio
                                AND empenho.cod_entidade = tmp_valores_insuficiencia.cod_entidade
                                AND empenho.cod_empenho = tmp_valores_insuficiencia.cod_empenho
                               JOIN empenho.pre_empenho
                                 ON pre_empenho.exercicio = empenho.exercicio
                                AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                               JOIN empenho.pre_empenho_despesa
                                 ON pre_empenho_despesa.exercicio = pre_empenho.exercicio
                                AND pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                               JOIN orcamento.conta_despesa
                                 ON conta_despesa.exercicio = pre_empenho_despesa.exercicio
                                AND conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
                               JOIN contabilidade.plano_conta
                                 ON plano_conta.cod_conta = conta_despesa.cod_conta
                                AND plano_conta.exercicio = conta_despesa.exercicio
                               JOIN contabilidade.plano_analitica
                                 ON plano_analitica.exercicio = plano_conta.exercicio
                                AND plano_analitica.cod_conta = plano_conta.cod_conta
                    ';
                    
            FOR reRegistro IN EXECUTE stSql
            LOOP
                UPDATE empenho.empenho SET restos_pagar = 'NP Liquidado' WHERE exercicio = reRegistro.exercicio AND cod_entidade = reRegistro.cod_entidade AND cod_empenho = reRegistro.cod_empenho AND reRegistro.cod_estrutural LIKE '6.2.2.1.3.01%';
            END LOOP;
        END IF;
        
        stSql := 'SELECT * FROM tmp_valores_insuficiencia';
        
        FOR reRegistro IN EXECUTE stSql
        LOOP
            UPDATE empenho.empenho SET restos_pagar = 'Processado' WHERE exercicio = reRegistro.exercicio AND cod_entidade = reRegistro.cod_entidade AND cod_empenho = reRegistro.cod_empenho AND reRegistro.cod_estrutural LIKE '6.2.2.1.3.03%';
            UPDATE empenho.empenho SET restos_pagar = 'NP a Liquidar' WHERE exercicio = reRegistro.exercicio AND cod_entidade = reRegistro.cod_entidade AND cod_empenho = reRegistro.cod_empenho AND reRegistro.cod_estrutural LIKE '6.2.2.1.3.01%';
        END LOOP;

    -------------------------------------------------------
    -- Faz lancamento dos restos a pagar nao processados --
    -------------------------------------------------------
    stSql := '
        SELECT tmp_valores_insuficiencia.exercicio
             , tmp_valores_insuficiencia.cod_entidade
             , buscaCodigoEstrutural(tmp_valores_insuficiencia.exercicio, contabilidade.fn_busca_conta_lancamento_rp(tmp_valores_insuficiencia.exercicio,tmp_valores_insuficiencia.cod_entidade,1)::VARCHAR) AS cod_estrutural_credito
             , buscaCodigoEstrutural(tmp_valores_insuficiencia.exercicio, contabilidade.fn_busca_conta_lancamento_rp(tmp_valores_insuficiencia.exercicio,tmp_valores_insuficiencia.cod_entidade,2)::VARCHAR) AS cod_estrutural_debito
             , tmp_valores_insuficiencia.empenhados_nao_liquidados AS valor_rp
             , CAST(''Valor ref. Inscrição de RP Não Processados.'' AS VARCHAR) AS complemento
          FROM (SELECT cod_recurso, nom_recurso, cod_entidade, exercicio, cod_plano_debito, SUM(liquidados_nao_pagos) AS liquidados_nao_pagos, SUM(empenhados_nao_liquidados) AS empenhados_nao_liquidados, cod_estrutural
                  FROM tmp_valores_insuficiencia
                GROUP BY cod_recurso, nom_recurso, cod_entidade, exercicio, cod_plano_debito, cod_estrutural) AS tmp_valores_insuficiencia

         UNION ALL
            
        SELECT entidade.exercicio
             , entidade.cod_entidade
             , buscaCodigoEstrutural(entidade.exercicio, contabilidade.fn_busca_conta_lancamento_rp(entidade.exercicio,entidade.cod_entidade,3)::VARCHAR) AS cod_estrutural_credito
             , buscaCodigoEstrutural(entidade.exercicio, contabilidade.fn_busca_conta_lancamento_rp(entidade.exercicio,entidade.cod_entidade,4)::VARCHAR) AS cod_estrutural_debito
             , contabilidade.fn_saldo_conta_analitica_entidade(entidade.exercicio,contabilidade.fn_busca_conta_lancamento_rp(entidade.exercicio,entidade.cod_entidade,3),entidade.cod_entidade) AS valor_rp
             , CAST(''Valor ref. Inscrição de RP Processados.'' AS VARCHAR) AS complemento
          FROM orcamento.entidade
         WHERE entidade.exercicio = ''' || stExercicio || '''
           AND entidade.cod_entidade IN ('|| stCodEntidade ||')
           
      ORDER BY cod_entidade
             , exercicio

    ';
    
    FOR reRegistro IN EXECUTE stSql
    LOOP
        entidadeAtual := reRegistro.cod_entidade;
        IF(entidadeAtual != entidadeAnterior)
        THEN
            inCodLote := contabilidade.fn_insere_lote( reRegistro.exercicio    -- stExercicio
                                                     , reRegistro.cod_entidade         -- inCodEntidade
                                                     , 'M'                                      -- stTipo
                                                     , 'ENCERRAMENTO DO EXERCÍCIO'              -- stNomeLote
                                                     , '31/12/' || stExercicio                  -- stDataLote
                                                     );
        END IF;
        
        stSqlAux := 'SELECT *
                        FROM contabilidade.lote
                        WHERE cod_entidade = ' || reRegistro.cod_entidade || '
                          AND exercicio = '''|| reRegistro.exercicio || '''
                          AND dt_lote = ''|| reRegistro.exercicio || ''-12-31''
                          AND tipo = ''M''
                          AND nom_lote = ''ENCERRAMENTO DO EXERCÍCIO''';
        
        IF(reRegistro.valor_rp <> 0)
        THEN
            sequencia := FazerLancamento(reRegistro.cod_estrutural_debito,reRegistro.cod_estrutural_credito,800,reRegistro.exercicio,abs(reRegistro.valor_rp),reRegistro.complemento,inCodLote,CAST('M' AS VARCHAR),reRegistro.cod_entidade);
        END IF;

    END LOOP;

    RETURN stCodEntidade;
   
    DROP TABLE tmp_valores_insuficiencia;

END;
$$ language 'plpgsql';
