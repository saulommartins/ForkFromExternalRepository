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

 *  $Id: gerarRestosPagar.plsql 36930 2009-01-08 10:43:03Z hboaventura $
*/

CREATE OR REPLACE FUNCTION contabilidade.fn_gerar_restos_pagar_destinacao_recurso(VARCHAR) RETURNS VARCHAR AS $$
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
      SELECT *
           , buscaCodigoEstrutural(retorno.exercicio,retorno.cod_plano_debito) AS cod_estrutural
        FROM contabilidade.totaliza_destinacao_recurso_liquidado( stCodEntidade,
                                                                  stExercicio, 
                                                                  '01/01/' || stExercicio, 
                                                                  '31/12/' || stExercicio,
                                                                  '31/12/' || stExercicio
                                                                ) as retorno
             ( num_recurso varchar,
               cod_recurso integer,
               nom_recurso varchar, 
               cod_entidade integer, 
               exercicio varchar, 
               cod_plano_debito varchar, 
               liquidados_nao_pagos numeric, 
               empenhados_nao_liquidados numeric);

    -------------------------------------------------------
    -- Faz lancamento dos restos a pagar nao processados --
    -------------------------------------------------------
    stSql := '
        SELECT tmp_valores_insuficiencia.exercicio
             , tmp_valores_insuficiencia.cod_entidade
--             , buscaCodigoEstrutural(conta_lancamento_rp.exercicio, conta_lancamento_rp.cod_plano::VARCHAR) AS cod_estrutural_credito
             , buscaCodigoEstrutural(tmp_valores_insuficiencia.exercicio, contabilidade.fn_busca_conta_lancamento_rp(tmp_valores_insuficiencia.exercicio,tmp_valores_insuficiencia.cod_entidade,0)::VARCHAR) AS cod_estrutural_credito
             , tmp_valores_insuficiencia.cod_estrutural AS cod_estrutural_debito
             , tmp_valores_insuficiencia.empenhados_nao_liquidados AS valor_rp
             , CAST(''Valor ref. Inscrição de RP Não Processados.'' AS VARCHAR) AS complemento
          FROM tmp_valores_insuficiencia

         UNION ALL
            
        SELECT entidade.exercicio
             , entidade.cod_entidade
             , CASE WHEN (contabilidade.fn_saldo_conta_analitica_entidade(entidade.exercicio,contabilidade.fn_busca_conta_lancamento_rp(entidade.exercicio,entidade.cod_entidade,1),entidade.cod_entidade) > 0)
                    THEN buscaCodigoEstrutural(entidade.exercicio, contabilidade.fn_busca_conta_lancamento_rp(entidade.exercicio,entidade.cod_entidade,1)::VARCHAR)
                    ELSE buscaCodigoEstrutural(entidade.exercicio, contabilidade.fn_busca_conta_lancamento_rp(entidade.exercicio,entidade.cod_entidade,6)::VARCHAR)
               END AS cod_estrutural_credito
             , CASE WHEN (contabilidade.fn_saldo_conta_analitica_entidade(entidade.exercicio,contabilidade.fn_busca_conta_lancamento_rp(entidade.exercicio,entidade.cod_entidade,1),entidade.cod_entidade) < 0)
                    THEN buscaCodigoEstrutural(entidade.exercicio, contabilidade.fn_busca_conta_lancamento_rp(entidade.exercicio,entidade.cod_entidade,1)::VARCHAR)
                    ELSE buscaCodigoEstrutural(entidade.exercicio, contabilidade.fn_busca_conta_lancamento_rp(entidade.exercicio,entidade.cod_entidade,6)::VARCHAR)
               END AS cod_estrutural_debito
             , contabilidade.fn_saldo_conta_analitica_entidade(entidade.exercicio,contabilidade.fn_busca_conta_lancamento_rp(entidade.exercicio,entidade.cod_entidade,1),entidade.cod_entidade) AS valor_rp
             , CAST(''Valor ref. Inscrição de RP Processados.'' AS VARCHAR) AS complemento
          FROM orcamento.entidade
         WHERE entidade.exercicio = ''' || stExercicio || '''
           AND entidade.cod_entidade IN ('|| stCodEntidade ||')

         UNION ALL
 
        SELECT entidade.exercicio
             , entidade.cod_entidade
             , CASE WHEN (contabilidade.fn_saldo_conta_analitica_entidade(entidade.exercicio,contabilidade.fn_busca_conta_lancamento_rp(entidade.exercicio,entidade.cod_entidade,2),entidade.cod_entidade) > 0)
                    THEN buscaCodigoEstrutural(entidade.exercicio, contabilidade.fn_busca_conta_lancamento_rp(entidade.exercicio,entidade.cod_entidade,2)::VARCHAR)
                    ELSE buscaCodigoEstrutural(entidade.exercicio, contabilidade.fn_busca_conta_lancamento_rp(entidade.exercicio,entidade.cod_entidade,7)::VARCHAR)
               END AS cod_estrutural_credito
             , CASE WHEN (contabilidade.fn_saldo_conta_analitica_entidade(entidade.exercicio,contabilidade.fn_busca_conta_lancamento_rp(entidade.exercicio,entidade.cod_entidade,2),entidade.cod_entidade) < 0)
                    THEN buscaCodigoEstrutural(entidade.exercicio, contabilidade.fn_busca_conta_lancamento_rp(entidade.exercicio,entidade.cod_entidade,2)::VARCHAR)
                    ELSE buscaCodigoEstrutural(entidade.exercicio, contabilidade.fn_busca_conta_lancamento_rp(entidade.exercicio,entidade.cod_entidade,7)::VARCHAR)
               END AS cod_estrutural_debito
             , contabilidade.fn_saldo_conta_analitica_entidade(entidade.exercicio,contabilidade.fn_busca_conta_lancamento_rp(entidade.exercicio,entidade.cod_entidade,2),entidade.cod_entidade) AS valor_rp
             , CAST(''Valor ref. Inscrição de RP Processados.'' AS VARCHAR) AS complemento
          FROM orcamento.entidade
         WHERE entidade.exercicio = ''' || stExercicio || '''
           AND entidade.cod_entidade IN ('|| stCodEntidade ||')

         UNION ALL
 
        SELECT entidade.exercicio
             , entidade.cod_entidade
             , CASE WHEN (contabilidade.fn_saldo_conta_analitica_entidade(entidade.exercicio,contabilidade.fn_busca_conta_lancamento_rp(entidade.exercicio,entidade.cod_entidade,3),entidade.cod_entidade) > 0)
                    THEN buscaCodigoEstrutural(entidade.exercicio, contabilidade.fn_busca_conta_lancamento_rp(entidade.exercicio,entidade.cod_entidade,3)::VARCHAR)
                    ELSE buscaCodigoEstrutural(entidade.exercicio, contabilidade.fn_busca_conta_lancamento_rp(entidade.exercicio,entidade.cod_entidade,8)::VARCHAR)
               END AS cod_estrutural_credito
             , CASE WHEN (contabilidade.fn_saldo_conta_analitica_entidade(entidade.exercicio,contabilidade.fn_busca_conta_lancamento_rp(entidade.exercicio,entidade.cod_entidade,3),entidade.cod_entidade) < 0)
                    THEN buscaCodigoEstrutural(entidade.exercicio, contabilidade.fn_busca_conta_lancamento_rp(entidade.exercicio,entidade.cod_entidade,3)::VARCHAR)
                    ELSE buscaCodigoEstrutural(entidade.exercicio, contabilidade.fn_busca_conta_lancamento_rp(entidade.exercicio,entidade.cod_entidade,8)::VARCHAR)
               END AS cod_estrutural_debito
             , contabilidade.fn_saldo_conta_analitica_entidade(entidade.exercicio,contabilidade.fn_busca_conta_lancamento_rp(entidade.exercicio,entidade.cod_entidade,3),entidade.cod_entidade) AS valor_rp
             , CAST(''Valor ref. Inscrição de RP Processados.'' AS VARCHAR) AS complemento
          FROM orcamento.entidade
         WHERE entidade.exercicio = ''' || stExercicio || '''
           AND entidade.cod_entidade IN ('|| stCodEntidade ||')

         UNION ALL
 
        SELECT entidade.exercicio
             , entidade.cod_entidade
             , CASE WHEN (contabilidade.fn_saldo_conta_analitica_entidade(entidade.exercicio,contabilidade.fn_busca_conta_lancamento_rp(entidade.exercicio,entidade.cod_entidade,4),entidade.cod_entidade) > 0)
                    THEN buscaCodigoEstrutural(entidade.exercicio, contabilidade.fn_busca_conta_lancamento_rp(entidade.exercicio,entidade.cod_entidade,4)::VARCHAR)
                    ELSE buscaCodigoEstrutural(entidade.exercicio, contabilidade.fn_busca_conta_lancamento_rp(entidade.exercicio,entidade.cod_entidade,9)::VARCHAR)
               END AS cod_estrutural_credito
             , CASE WHEN (contabilidade.fn_saldo_conta_analitica_entidade(entidade.exercicio,contabilidade.fn_busca_conta_lancamento_rp(entidade.exercicio,entidade.cod_entidade,4),entidade.cod_entidade) < 0)
                    THEN buscaCodigoEstrutural(entidade.exercicio, contabilidade.fn_busca_conta_lancamento_rp(entidade.exercicio,entidade.cod_entidade,4)::VARCHAR)
                    ELSE buscaCodigoEstrutural(entidade.exercicio, contabilidade.fn_busca_conta_lancamento_rp(entidade.exercicio,entidade.cod_entidade,9)::VARCHAR)
               END AS cod_estrutural_debito
             , contabilidade.fn_saldo_conta_analitica_entidade(entidade.exercicio,contabilidade.fn_busca_conta_lancamento_rp(entidade.exercicio,entidade.cod_entidade,4),entidade.cod_entidade) AS valor_rp
             , CAST(''Valor ref. Inscrição de RP Processados.'' AS VARCHAR) AS complemento
          FROM orcamento.entidade
         WHERE entidade.exercicio = ''' || stExercicio || '''
           AND entidade.cod_entidade IN ('|| stCodEntidade ||')

         UNION ALL
 
        SELECT entidade.exercicio
             , entidade.cod_entidade
             , CASE WHEN (contabilidade.fn_saldo_conta_analitica_entidade(entidade.exercicio,contabilidade.fn_busca_conta_lancamento_rp(entidade.exercicio,entidade.cod_entidade,5),entidade.cod_entidade) > 0)
                    THEN buscaCodigoEstrutural(entidade.exercicio, contabilidade.fn_busca_conta_lancamento_rp(entidade.exercicio,entidade.cod_entidade,5)::VARCHAR)
                    ELSE buscaCodigoEstrutural(entidade.exercicio, contabilidade.fn_busca_conta_lancamento_rp(entidade.exercicio,entidade.cod_entidade,10)::VARCHAR)
               END AS cod_estrutural_credito
             , CASE WHEN (contabilidade.fn_saldo_conta_analitica_entidade(entidade.exercicio,contabilidade.fn_busca_conta_lancamento_rp(entidade.exercicio,entidade.cod_entidade,5),entidade.cod_entidade) < 0)
                    THEN buscaCodigoEstrutural(entidade.exercicio, contabilidade.fn_busca_conta_lancamento_rp(entidade.exercicio,entidade.cod_entidade,5)::VARCHAR)
                    ELSE buscaCodigoEstrutural(entidade.exercicio, contabilidade.fn_busca_conta_lancamento_rp(entidade.exercicio,entidade.cod_entidade,10)::VARCHAR)
               END AS cod_estrutural_debito
             , contabilidade.fn_saldo_conta_analitica_entidade(entidade.exercicio,contabilidade.fn_busca_conta_lancamento_rp(entidade.exercicio,entidade.cod_entidade,5),entidade.cod_entidade) AS valor_rp
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

        IF(reRegistro.valor_rp <> 0)
        THEN
            sequencia := FazerLancamento(reRegistro.cod_estrutural_debito,reRegistro.cod_estrutural_credito,800,reRegistro.exercicio,abs(reRegistro.valor_rp),reRegistro.complemento,inCodLote,CAST('M' AS VARCHAR),reRegistro.cod_entidade);
        END IF;

    END LOOP;

    RETURN stCodEntidade;
   
    DROP TABLE tmp_valores_insuficiencia;

END;
$$ language 'plpgsql';
