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
--
-- Função Encerramento Anual 2006 VAriações Patrimoniais.
--

/* @package URBEM
 * @subpackage

 * $Id: encerramentoAnualLancamentos2013.plsql 66104 2016-07-19 20:10:13Z michel $
 */

CREATE OR REPLACE FUNCTION contabilidade.encerramentoAnualLancamentosVariacoesPatrimoniais2013( varExercicio VARCHAR(4), intCodEntidade INTEGER )
RETURNS VOID as $$
DECLARE
   recLancamento        Record;
   varAux               VARCHAR;
   intCodLote           INTEGER;
   intCodHistorico      INTEGER;
   bolCriouLote         BOOLEAN := FALSE;

   intSeqIns            INTEGER := 0;
   numSaldo             NUMERIC(14,2);
   intCodPlanoDeb       INTEGER;
   intCodPlanoCre       INTEGER;

   intCodPlano2371101     INTEGER := 0;

   bolEncerramentoVariacoesPatri   BOOLEAN;
BEGIN

   --utiliza a mesma função de validação pois não muda o parâmetro na configuração
   IF NOT contabilidade.fezEncerramentoAnualLancamentosVariacoesPatri( varExercicio, intCodEntidade) THEN

      IF bolEncerramentoVariacoesPatri THEN
         RAISE EXCEPTION 'Encerramento já realizado......';
      END IF;

      --Insere histórico 801 para as contas do grupo 3
      INSERT INTO contabilidade.historico_contabil( cod_historico
                                                , exercicio
                                                , nom_historico
                                                , complemento)
                                             SELECT 801
                                                , varExercicio
                                                , 'Encerramento do exercício – Despesa.'
                                                , 'f'
                                             WHERE 0  = ( SELECT Count(1)
                                                            FROM contabilidade.historico_contabil
                                                            WHERE cod_historico = 801
                                                            AND exercicio     = varExercicio);

      --Insere histórico 802 para as contas do grupo 4
      INSERT INTO contabilidade.historico_contabil( cod_historico
                                                , exercicio
                                                , nom_historico
                                                , complemento)
                                             SELECT 802
                                                , varExercicio
                                                , 'Encerramento do exercício – Receita.'
                                                , 'f'
                                             WHERE 0  = ( SELECT Count(1)
                                                            FROM contabilidade.historico_contabil
                                                            WHERE cod_historico = 802
                                                            AND exercicio     = varExercicio);

      SELECT plano_analitica.cod_plano
      INTO intCodPlano2371101
      FROM contabilidade.plano_conta
         , contabilidade.plano_analitica
      WHERE plano_conta.exercicio = plano_analitica.exercicio
         AND plano_conta.cod_conta = plano_analitica.cod_conta
         AND plano_conta.exercicio = varExercicio
         AND plano_conta.cod_estrutural = '2.3.7.1.1.01.00.00.00.00';

      For recLancamento IN SELECT plano_conta.cod_estrutural
                                , plano_analitica.cod_plano
                                , coalesce(total_credito.valor,0.00)            AS valor_cre
                                , coalesce(total_debito.valor,0.00)             AS valor_deb
                                , coalesce(( COALESCE(abs(-(total_credito.valor)),0) - COALESCE(total_debito.valor,0) ),0.00) AS saldo
                             FROM contabilidade.plano_conta
                                , contabilidade.plano_analitica
                        LEFT JOIN ( SELECT cod_plano, conta_debito.exercicio, SUM(vl_lancamento) AS valor
                                      FROM contabilidade.valor_lancamento
                                         , contabilidade.conta_debito
                                     WHERE conta_debito.cod_lote     = valor_lancamento.cod_lote
                                       AND conta_debito.tipo         = valor_lancamento.tipo
                                       AND conta_debito.sequencia    = valor_lancamento.sequencia
                                       AND conta_debito.exercicio    = valor_lancamento.exercicio
                                       AND conta_debito.tipo_valor   = valor_lancamento.tipo_valor
                                       AND conta_debito.cod_entidade = valor_lancamento.cod_entidade
                                       AND conta_debito.cod_entidade = intCodEntidade
                                  GROUP BY cod_plano,conta_debito.exercicio
                                ) AS total_debito     
                               ON contabilidade.plano_analitica.cod_plano = total_debito.cod_plano
                              AND contabilidade.plano_analitica.exercicio = total_debito.exercicio
                        LEFT JOIN ( SELECT cod_plano, conta_credito.exercicio, SUM(vl_lancamento) AS valor
                                      FROM contabilidade.valor_lancamento
                                         , contabilidade.conta_credito
                                     WHERE conta_credito.cod_lote     = valor_lancamento.cod_lote
                                       AND conta_credito.tipo         = valor_lancamento.tipo
                                       AND conta_credito.sequencia    = valor_lancamento.sequencia
                                       AND conta_credito.exercicio    = valor_lancamento.exercicio
                                       AND conta_credito.tipo_valor   = valor_lancamento.tipo_valor
                                       AND conta_credito.cod_entidade = valor_lancamento.cod_entidade
                                       AND conta_credito.cod_entidade = intCodEntidade
                                  GROUP BY cod_plano,conta_credito.exercicio
                                ) AS total_credito    
                               ON contabilidade.plano_analitica.cod_plano = total_credito.cod_plano
                              AND contabilidade.plano_analitica.exercicio = total_credito.exercicio
                            WHERE plano_conta.cod_conta     = plano_analitica.cod_conta
                              AND plano_conta.exercicio     = plano_analitica.exercicio
                              AND plano_conta.cod_sistema   = 1
                              AND plano_conta.exercicio     = varExercicio
                              AND SUBSTR(cod_estrutural,01,01) IN ('3','4')
                          AND NOT ( total_debito.valor IS NULL AND total_credito.valor IS NULL )
                         ORDER BY plano_conta.cod_estrutural
      LOOP

         IF recLancamento.saldo != 0 THEN
            IF NOT bolCriouLote  THEN
               intCodLote  := contabilidade.fn_insere_lote( varExercicio
                                                         , intCodEntidade
                                                         , 'M'
                                                         , 'Variações Patrimoniais/' || varExercicio
                                                         , '31-12-' || varExercicio
                                                            );
               bolCriouLote := TRUE;
            END IF;


            varAux            := RecLancamento.cod_estrutural || ' Codigo plano => ' ||recLancamento.cod_plano;
            intCodPlanoDeb    := 0;
            intCodPlanoCre    := 0;
            intSeqIns         := intSeqIns  + 1;

            IF substr(recLancamento.cod_estrutural,1,1) = '3' THEN
               IF recLancamento.saldo < 0 THEN
                  numSaldo        := ABS(recLancamento.saldo);
                  intCodPlanoDeb  := intCodPlano2371101;
                  intCodPlanoCre  := recLancamento.cod_plano;
                  intCodHistorico := 801;
               ELSE
                  numSaldo        := recLancamento.saldo;
                  intCodPlanoDeb  := recLancamento.cod_plano;
                  intCodPlanoCre  := intCodPlano2371101;
                  intCodHistorico := 801;
               END IF;
            END IF;

            IF substr(recLancamento.cod_estrutural,1,1) = '4' THEN
               IF recLancamento.saldo > 0 THEN
                  numSaldo        := recLancamento.saldo;
                  intCodPlanoDeb  := recLancamento.cod_plano;
                  intCodPlanoCre  := intCodPlano2371101;
                  intCodHistorico := 802;
               ELSE
                  numSaldo        := ABS(recLancamento.saldo);
                  intCodPlanoDeb  := intCodPlano2371101;
                  intCodPlanoCre  := recLancamento.cod_plano;
                  intCodHistorico := 802;
               END IF;
            END IF;

            PERFORM contabilidade.encerramentoAnualLancamentos( varExercicio
                                                               , intSeqIns
                                                               , intCodlote
                                                               , intCodEntidade
                                                               , intCodHistorico
                                                               , numSaldo
                                                               , intCodPlanoDeb
                                                               , intCodPlanoCre
                                                               );
         END IF;
      END LOOP;

      Insert Into administracao.configuracao ( exercicio
                                             , cod_modulo
                                             , parametro
                                             , valor)
                                      Values ( varExercicio
                                              , 9
                                              , 'encer_var_patri_' || BTRIM(TO_CHAR(intCodEntidade, '9'))
                                              , 'TRUE');

   END IF;

   RETURN;

END;  $$ LANGUAGE plpgsql;


--
-- Função Encerramento Anual 2013 Orcamento.
--
CREATE OR REPLACE function contabilidade.encerramentoAnualLancamentosOrcamentario2013( varExercicio VARCHAR(4), intCodEntidade INTEGER )
RETURNS VOID as $$
DECLARE
   recLancamento        Record;
   varAux               VARCHAR;
   intCodLote           INTEGER;
   intCodHistorico      INTEGER := 803;
   bolCriouLote         BOOLEAN := FALSE;

   intSeqIns            INTEGER := 0;
   stSql                VARCHAR := '';
   bolEncerramentoOrcamento        BOOLEAN;
BEGIN

    IF NOT contabilidade.fezEncerramentoAnualLancamentosOrcamentario2013( varExercicio, intCodEntidade ) THEN

        IF bolEncerramentoOrcamento THEN
           RAISE EXCEPTION 'Encerramento já realizado......';
        END IF;

        INSERT INTO contabilidade.historico_contabil( cod_historico
                                                     , exercicio
                                                     , nom_historico
                                                     , complemento)
                                                SELECT intCodHistorico
                                                     , varExercicio
                                                     , 'Encerramento do exercício – Sistema Orçamentário'
                                                     , 'f'
                                               WHERE 0  = ( SELECT Count(1)
                                                              FROM contabilidade.historico_contabil
                                                             WHERE cod_historico = intCodHistorico
                                                               AND exercicio     = varExercicio);

         -- Ticket #24022 pede para apurar o saldo e fazer o lancamento das contas  6.3.1.4, 5.3.1.2, 6.3.2.2, 5.3.2.2
         -- Ticket #24022 pede para apurar o saldo e fazer o lancamento das contas  6.3.1.9.1, 5.3.1.2, 6.3.2.9.9, 5.3.2.2
         IF ( varExercicio >= '2014' ) THEN
        --APURA O SALDO E FAZ O LANCAMENTO DAS CONTAS 6.3.1.4, 6.3.2.2, 6.3.1.9.1 E 6.3.2.9.9 PARA CREDITAR NAS CONTAS 5.3.1.2, 5.3.2.2, 5.3.1.2 E 5.3.2.2 RESPECTIVAMENTE
            stSql := ' SELECT plano_conta.cod_estrutural
                            , plano_analitica.cod_plano
                            , coalesce(total_credito.valor,0.00)            AS valor_cre
                            , coalesce(total_debito.valor,0.00)             AS valor_deb
                            , ABS(coalesce(( COALESCE(abs(-(total_credito.valor)),0) - COALESCE(total_debito.valor,0) ),0.00)) AS saldo
                            , CASE WHEN plano_conta.cod_estrutural LIKE ''6.3.1.4%''
                                   THEN 3
                                   WHEN plano_conta.cod_estrutural LIKE ''6.3.2.2%''
                                   THEN 4
                                   WHEN plano_conta.cod_estrutural LIKE ''6.3.1.9.1%''
                                   THEN 1
                                   WHEN plano_conta.cod_estrutural LIKE ''6.3.2.9.9%''
                                   THEN 2
                              END AS ordem
                        FROM contabilidade.plano_conta
                            , contabilidade.plano_analitica
                    LEFT JOIN ( SELECT cod_plano, conta_debito.exercicio, SUM(vl_lancamento) AS valor
                                FROM contabilidade.valor_lancamento
                                    , contabilidade.conta_debito
                                WHERE conta_debito.cod_lote     = valor_lancamento.cod_lote
                                AND conta_debito.tipo         = valor_lancamento.tipo
                                AND conta_debito.sequencia    = valor_lancamento.sequencia
                                AND conta_debito.exercicio    = valor_lancamento.exercicio
                                AND conta_debito.tipo_valor   = valor_lancamento.tipo_valor
                                AND conta_debito.cod_entidade = valor_lancamento.cod_entidade
                                AND conta_debito.cod_entidade =  ' || intCodEntidade || '
                            GROUP BY cod_plano,conta_debito.exercicio
                            ) AS total_debito     
                        ON contabilidade.plano_analitica.cod_plano = total_debito.cod_plano
                        AND contabilidade.plano_analitica.exercicio = total_debito.exercicio
                    LEFT JOIN ( SELECT cod_plano, conta_credito.exercicio, SUM(vl_lancamento) AS valor
                                FROM contabilidade.valor_lancamento
                                    , contabilidade.conta_credito
                                WHERE conta_credito.cod_lote     = valor_lancamento.cod_lote
                                AND conta_credito.tipo         = valor_lancamento.tipo
                                AND conta_credito.sequencia    = valor_lancamento.sequencia
                                AND conta_credito.exercicio    = valor_lancamento.exercicio
                                AND conta_credito.tipo_valor   = valor_lancamento.tipo_valor
                                AND conta_credito.cod_entidade = valor_lancamento.cod_entidade
                                AND conta_credito.cod_entidade =  ' || intCodEntidade || '
                            GROUP BY cod_plano,conta_credito.exercicio
                            ) AS total_credito    
                        ON contabilidade.plano_analitica.cod_plano = total_credito.cod_plano
                        AND contabilidade.plano_analitica.exercicio = total_credito.exercicio
                        WHERE plano_conta.cod_conta     = plano_analitica.cod_conta
                        AND plano_conta.exercicio     = plano_analitica.exercicio
                        AND plano_conta.cod_sistema   = 2
                        AND plano_conta.exercicio     =  ' || quote_literal(varExercicio) || '
                        AND (    plano_conta.cod_estrutural LIKE ''6.3.1.4%''
                              OR plano_conta.cod_estrutural LIKE ''6.3.2.2%''
                              OR plano_conta.cod_estrutural LIKE ''6.3.1.9.1%''
                              OR plano_conta.cod_estrutural LIKE ''6.3.2.9.9%''
                            )
                        AND NOT ( total_debito.valor IS NULL AND total_credito.valor IS NULL )
                    ORDER BY ordem '; 
            For recLancamento IN EXECUTE stSql
            LOOP
                IF recLancamento.saldo != 0 THEN
                    IF NOT bolCriouLote  THEN
                        intCodLote  := contabilidade.fn_insere_lote( varExercicio
                                                                , intCodEntidade
                                                                , 'M' 
                                                                , 'Orçamentário/' || varExercicio
                                                                , '31-12-' || varExercicio
                                                                );
                        bolCriouLote := TRUE;
                    END IF;
                    
                    IF substr(recLancamento.cod_estrutural,1,15) = '6.3.1.4.0.00.00' THEN 
                        intSeqIns := FazerLancamento('6.3.1.4.0.00.00.00.00.00','5.3.1.2.0.00.00.00.00.00',intCodHistorico,varExercicio,RecLancamento.saldo,'',intCodlote,CAST('M' AS VARCHAR),intCodEntidade);
                    ELSIF substr(recLancamento.cod_estrutural,1,15) = '6.3.2.2.0.00.00' THEN 
                        intSeqIns := FazerLancamento('6.3.2.2.0.00.00.00.00.00','5.3.2.2.0.00.00.00.00.00',intCodHistorico,varExercicio,RecLancamento.saldo,'',intCodlote,CAST('M' AS VARCHAR),intCodEntidade);
                    ELSIF substr(recLancamento.cod_estrutural,1,15) = '6.3.1.9.1.00.00' THEN 
                        intSeqIns := FazerLancamento('6.3.1.9.1.00.00.00.00.00','5.3.1.2.0.00.00.00.00.00',intCodHistorico,varExercicio,RecLancamento.saldo,'',intCodlote,CAST('M' AS VARCHAR),intCodEntidade);
                    ELSIF substr(recLancamento.cod_estrutural,1,15) = '6.3.2.9.9.00.00' THEN 
                        intSeqIns := FazerLancamento('6.3.2.9.9.00.00.00.00.00','5.3.2.2.0.00.00.00.00.00',intCodHistorico,varExercicio,RecLancamento.saldo,'',intCodlote,CAST('M' AS VARCHAR),intCodEntidade);
                    END IF;
                END IF;
            END LOOP; 
            
            --APURA O SALDO DAS CONTAS 5.3.1.2 E 5.3.2.2 JA COM OS LANCAMENTOS ACIMA REALIZADO, E FAZ OS LANCAMENTOS NECESSARIOS
            stSql := ' SELECT plano_conta.cod_estrutural
                            , plano_analitica.cod_plano
                            , coalesce(total_credito.valor,0.00)            AS valor_cre
                            , coalesce(total_debito.valor,0.00)             AS valor_deb
                            , ABS(coalesce(( COALESCE(abs(-(total_credito.valor)),0) - COALESCE(total_debito.valor,0) ),0.00)) AS saldo
                        FROM contabilidade.plano_conta
                            , contabilidade.plano_analitica
                    LEFT JOIN ( SELECT cod_plano, conta_debito.exercicio, SUM(vl_lancamento) AS valor
                                FROM contabilidade.valor_lancamento
                                    , contabilidade.conta_debito
                                WHERE conta_debito.cod_lote     = valor_lancamento.cod_lote
                                AND conta_debito.tipo         = valor_lancamento.tipo
                                AND conta_debito.sequencia    = valor_lancamento.sequencia
                                AND conta_debito.exercicio    = valor_lancamento.exercicio
                                AND conta_debito.tipo_valor   = valor_lancamento.tipo_valor
                                AND conta_debito.cod_entidade = valor_lancamento.cod_entidade
                                AND conta_debito.cod_entidade =  ' || intCodEntidade || '
                            GROUP BY cod_plano,conta_debito.exercicio
                            ) AS total_debito     
                        ON contabilidade.plano_analitica.cod_plano = total_debito.cod_plano
                        AND contabilidade.plano_analitica.exercicio = total_debito.exercicio
                    LEFT JOIN ( SELECT cod_plano, conta_credito.exercicio, SUM(vl_lancamento) AS valor
                                FROM contabilidade.valor_lancamento
                                    , contabilidade.conta_credito
                                WHERE conta_credito.cod_lote     = valor_lancamento.cod_lote
                                AND conta_credito.tipo         = valor_lancamento.tipo
                                AND conta_credito.sequencia    = valor_lancamento.sequencia
                                AND conta_credito.exercicio    = valor_lancamento.exercicio
                                AND conta_credito.tipo_valor   = valor_lancamento.tipo_valor
                                AND conta_credito.cod_entidade = valor_lancamento.cod_entidade
                                AND conta_credito.cod_entidade =  ' || intCodEntidade || '
                            GROUP BY cod_plano,conta_credito.exercicio
                            ) AS total_credito    
                        ON contabilidade.plano_analitica.cod_plano = total_credito.cod_plano
                        AND contabilidade.plano_analitica.exercicio = total_credito.exercicio
                        WHERE plano_conta.cod_conta     = plano_analitica.cod_conta
                        AND plano_conta.exercicio     = plano_analitica.exercicio
                        AND plano_conta.cod_sistema   = 2
                        AND plano_conta.exercicio     =  ' || quote_literal(varExercicio) || '
                        AND (  plano_conta.cod_estrutural LIKE ''5.3.1.1%''
                                OR plano_conta.cod_estrutural LIKE ''5.3.2.1%''
                            )
                        AND NOT ( total_debito.valor IS NULL AND total_credito.valor IS NULL )
                    ORDER BY plano_conta.cod_estrutural';
            
            For recLancamento IN EXECUTE stSql
            LOOP
                IF recLancamento.saldo != 0 THEN
                    IF NOT bolCriouLote  THEN
                        intCodLote  := contabilidade.fn_insere_lote( varExercicio
                                                                , intCodEntidade
                                                                , 'M' 
                                                                , 'Orçamentário/' || varExercicio
                                                                , '31-12-' || varExercicio
                                                                );
                        bolCriouLote := TRUE;
                    END IF;
    
                    IF substr(recLancamento.cod_estrutural,1,15) = '5.3.1.1.0.00.00' THEN 
                        intSeqIns := FazerLancamento('5.3.1.2.0.00.00.00.00.00','5.3.1.1.0.00.00.00.00.00',intCodHistorico,varExercicio,RecLancamento.saldo,'',intCodlote,CAST('M' AS VARCHAR),intCodEntidade);
                    ELSIF substr(recLancamento.cod_estrutural,1,15) = '5.3.2.1.0.00.00' THEN 
                        intSeqIns := FazerLancamento('5.3.2.2.0.00.00.00.00.00','5.3.2.1.0.00.00.00.00.00',intCodHistorico,varExercicio,RecLancamento.saldo,'',intCodlote,CAST('M' AS VARCHAR),intCodEntidade);
                    END IF;
                END IF;
            END LOOP;
        END IF; ---- FIM Ticket #24022, APURACAO DE SALDOS E LANCAMENTOS
        
        stSql := ' SELECT plano_conta.cod_estrutural
                        , plano_analitica.cod_plano
                        , coalesce(total_credito.valor,0.00)            AS valor_cre
                        , coalesce(total_debito.valor,0.00)             AS valor_deb
                        , (coalesce(( COALESCE(abs(-(total_credito.valor)),0) - COALESCE(total_debito.valor,0) ),0.00)) AS saldo
                     FROM contabilidade.plano_conta
                        , contabilidade.plano_analitica
                LEFT JOIN ( SELECT cod_plano, conta_debito.exercicio, SUM(vl_lancamento) AS valor
                              FROM contabilidade.valor_lancamento
                                 , contabilidade.conta_debito
                             WHERE conta_debito.cod_lote     = valor_lancamento.cod_lote
                               AND conta_debito.tipo         = valor_lancamento.tipo
                               AND conta_debito.sequencia    = valor_lancamento.sequencia
                               AND conta_debito.exercicio    = valor_lancamento.exercicio
                               AND conta_debito.tipo_valor   = valor_lancamento.tipo_valor
                               AND conta_debito.cod_entidade = valor_lancamento.cod_entidade
                               AND conta_debito.cod_entidade =  ' || intCodEntidade || '
                          GROUP BY cod_plano,conta_debito.exercicio
                        ) AS total_debito     
                       ON contabilidade.plano_analitica.cod_plano = total_debito.cod_plano
                      AND contabilidade.plano_analitica.exercicio = total_debito.exercicio
                LEFT JOIN ( SELECT cod_plano, conta_credito.exercicio, SUM(vl_lancamento) AS valor
                              FROM contabilidade.valor_lancamento
                                 , contabilidade.conta_credito
                             WHERE conta_credito.cod_lote     = valor_lancamento.cod_lote
                               AND conta_credito.tipo         = valor_lancamento.tipo
                               AND conta_credito.sequencia    = valor_lancamento.sequencia
                               AND conta_credito.exercicio    = valor_lancamento.exercicio
                               AND conta_credito.tipo_valor   = valor_lancamento.tipo_valor
                               AND conta_credito.cod_entidade = valor_lancamento.cod_entidade
                               AND conta_credito.cod_entidade =  ' || intCodEntidade || '
                          GROUP BY cod_plano,conta_credito.exercicio
                        ) AS total_credito    
                       ON contabilidade.plano_analitica.cod_plano = total_credito.cod_plano
                      AND contabilidade.plano_analitica.exercicio = total_credito.exercicio
                    WHERE plano_conta.cod_conta     = plano_analitica.cod_conta
                      AND plano_conta.exercicio     = plano_analitica.exercicio
                      AND plano_conta.cod_sistema   = 2
                      AND plano_conta.exercicio     =  ' || quote_literal(varExercicio) || '
                      AND SUBSTR(plano_conta.cod_estrutural,01,01) IN (''5'',''6'')';
                
                  IF ( varExercicio <= '2013' ) THEN    
                    -- Ticket #20198 pede para não zerar os saldos dessas contas específicas
                    stSql := stSql || '
                                      AND NOT ( plano_conta.cod_estrutural LIKE ''5.3.1.7%''
                                              OR plano_conta.cod_estrutural LIKE ''5.3.2.7%''
                                              OR plano_conta.cod_estrutural LIKE ''6.3.1.7%''
                                              OR plano_conta.cod_estrutural LIKE ''6.3.2.7%''
                                    )';
                ELSE
                    -- Ticket #22953 pede para não zerar os saldos dessas contas específicas, estas contas mantêm seus saldos para o próximo exercício
                    stSql := stSql || '
                                      AND NOT ( plano_conta.cod_estrutural LIKE ''5.3.1.2%''
                                               OR plano_conta.cod_estrutural LIKE ''5.3.1.3%''
                                               OR plano_conta.cod_estrutural LIKE ''5.3.1.6%''
                                               OR plano_conta.cod_estrutural LIKE ''5.3.2.2%''
                                               OR plano_conta.cod_estrutural LIKE ''5.3.1.7%''
                                               OR plano_conta.cod_estrutural LIKE ''5.3.2.7%''
                                               OR plano_conta.cod_estrutural LIKE ''6.3.1.1%''
                                               OR plano_conta.cod_estrutural LIKE ''6.3.1.2%''
                                               OR plano_conta.cod_estrutural LIKE ''6.3.1.3%''
                                               OR plano_conta.cod_estrutural LIKE ''6.3.1.5%''
                                               OR plano_conta.cod_estrutural LIKE ''6.3.1.6%''
                                               OR plano_conta.cod_estrutural LIKE ''6.3.1.7.1%''
                                               OR plano_conta.cod_estrutural LIKE ''6.3.1.7.2%''
                                               OR plano_conta.cod_estrutural LIKE ''6.3.2.1%''
                                               OR plano_conta.cod_estrutural LIKE ''6.3.2.7%''
                                            )';
                        END IF;
                    stSql := stSql || '
                    AND NOT ( total_debito.valor IS NULL AND total_credito.valor IS NULL )
                   ORDER BY plano_conta.cod_estrutural '; 
        For recLancamento IN EXECUTE stSql
        LOOP
            IF recLancamento.saldo != 0 THEN
                IF NOT bolCriouLote  THEN
                    intCodLote  := contabilidade.fn_insere_lote( varExercicio
                                                               , intCodEntidade
                                                               , 'M' 
                                                               , 'Orçamentário/' || varExercicio
                                                               , '31-12-' || varExercicio
                                                             );
                    bolCriouLote := TRUE;
                END IF;

                IF recLancamento.saldo > 0 THEN
                    intSeqIns := contabilidade.fn_insere_lancamentos(varExercicio, RecLancamento.cod_plano, 0,'', '', RecLancamento.saldo, intCodlote, intCodEntidade, intCodHistorico, CAST('M' AS VARCHAR), '');
                ELSE
                    intSeqIns := contabilidade.fn_insere_lancamentos(varExercicio, 0, RecLancamento.cod_plano,'', '', ABS(RecLancamento.saldo), intCodlote, intCodEntidade, intCodHistorico, CAST('M' AS VARCHAR), '');
                END IF;
            END IF;
        END LOOP;

      Insert Into administracao.configuracao ( exercicio
                                             , cod_modulo
                                             , parametro
                                             , valor)
                                      Values ( varExercicio
                                              , 9
                                              , 'encer_orc_' || BTRIM(TO_CHAR(intCodEntidade, '9'))
                                              , 'TRUE');

   END IF;
   RETURN;
END;  $$ LANGUAGE plpgsql;


CREATE OR REPLACE function contabilidade.encerramentoAnualLancamentosControle2013( varExercicio VARCHAR(4), intCodEntidade INTEGER )
RETURNS VOID as $$
DECLARE
   recLancamento        Record;
   varAux               VARCHAR;
   intCodLote           INTEGER;
   intCodHistorico      INTEGER := 804;
   bolCriouLote         BOOLEAN := FALSE;

   intSeqIns            INTEGER := 0;

   bolEncerramentoOrcamento        BOOLEAN;
BEGIN

   IF NOT contabilidade.fezEncerramentoAnualLancamentosControle2013( varExercicio, intCodEntidade ) THEN

      IF bolEncerramentoOrcamento THEN
         RAISE EXCEPTION 'Encerramento já realizado......';
      END IF;

      INSERT INTO contabilidade.historico_contabil( cod_historico
                                                  , exercicio
                                                  , nom_historico
                                                  , complemento)
                                             SELECT intCodHistorico
                                                  , varExercicio
                                                  , 'Encerramento do exercício – Sistema Controle'
                                                  , 'f'
                                               WHERE 0  = ( SELECT Count(1)
                                                              FROM contabilidade.historico_contabil
                                                             WHERE cod_historico = intCodHistorico
                                                               AND exercicio     = varExercicio);

      For recLancamento IN SELECT plano_conta.cod_estrutural
                                , plano_analitica.cod_plano
                                , coalesce(total_credito.valor,0.00)            AS valor_cre
                                , coalesce(total_debito.valor,0.00)             AS valor_deb
                                , coalesce(( COALESCE(abs(-(total_credito.valor)),0) - COALESCE(total_debito.valor,0) ),0.00) AS saldo
                             FROM contabilidade.plano_conta
                                , contabilidade.plano_analitica
                        LEFT JOIN ( SELECT cod_plano, conta_debito.exercicio, SUM(vl_lancamento) AS valor
                                      FROM contabilidade.valor_lancamento
                                         , contabilidade.conta_debito
                                     WHERE conta_debito.cod_lote     = valor_lancamento.cod_lote
                                       AND conta_debito.tipo         = valor_lancamento.tipo
                                       AND conta_debito.sequencia    = valor_lancamento.sequencia
                                       AND conta_debito.exercicio    = valor_lancamento.exercicio
                                       AND conta_debito.tipo_valor   = valor_lancamento.tipo_valor
                                       AND conta_debito.cod_entidade = valor_lancamento.cod_entidade
                                       AND conta_debito.cod_entidade =  intCodEntidade
                                  GROUP BY cod_plano,conta_debito.exercicio
                                ) AS total_debito     
                               ON contabilidade.plano_analitica.cod_plano = total_debito.cod_plano
                              AND contabilidade.plano_analitica.exercicio = total_debito.exercicio
                        LEFT JOIN ( SELECT cod_plano, conta_credito.exercicio, SUM(vl_lancamento) AS valor
                                      FROM contabilidade.valor_lancamento
                                         , contabilidade.conta_credito
                                     WHERE conta_credito.cod_lote     = valor_lancamento.cod_lote
                                       AND conta_credito.tipo         = valor_lancamento.tipo
                                       AND conta_credito.sequencia    = valor_lancamento.sequencia
                                       AND conta_credito.exercicio    = valor_lancamento.exercicio
                                       AND conta_credito.tipo_valor   = valor_lancamento.tipo_valor
                                       AND conta_credito.cod_entidade = valor_lancamento.cod_entidade
                                       AND conta_credito.cod_entidade = intCodEntidade
                                  GROUP BY cod_plano,conta_credito.exercicio
                                ) AS total_credito    
                               ON contabilidade.plano_analitica.cod_plano = total_credito.cod_plano
                              AND contabilidade.plano_analitica.exercicio = total_credito.exercicio
                            WHERE plano_conta.cod_conta     = plano_analitica.cod_conta
                              AND plano_conta.exercicio     = plano_analitica.exercicio
                              AND plano_conta.cod_sistema   = 3
                              AND plano_conta.exercicio     = varExercicio

                          AND NOT ( plano_conta.cod_estrutural LIKE '7.9%' OR plano_conta.cod_estrutural LIKE '8.9%' )                                 

                              -- AND ( plano_conta.cod_estrutural LIKE '7.2.3%'
                              --    OR plano_conta.cod_estrutural LIKE '7.2.4%'
                              --    OR plano_conta.cod_estrutural LIKE '8.2.1.1.4%'
                              --    OR plano_conta.cod_estrutural LIKE '8.2.2.1.4%'
                              --    OR plano_conta.cod_estrutural LIKE '8.2.3%'
                              --    OR plano_conta.cod_estrutural LIKE '8.2.4%'
                              --    OR plano_conta.cod_estrutural LIKE '8.3.1.2%'
                              --    OR plano_conta.cod_estrutural LIKE '8.3.1.3%'
                              --    OR plano_conta.cod_estrutural LIKE '8.3.2.4%'
                              --    OR plano_conta.cod_estrutural LIKE '8.3.2.5%'
                              --    OR plano_conta.cod_estrutural LIKE '8.4.1%'
                              --    OR plano_conta.cod_estrutural LIKE '8.4.2%' )

                          AND NOT ( total_debito.valor IS NULL AND total_credito.valor IS NULL )
                         ORDER BY plano_conta.cod_estrutural
      LOOP
         IF recLancamento.saldo != 0 THEN
            IF NOT bolCriouLote  THEN
               intCodLote  := contabilidade.fn_insere_lote( varExercicio
                                                         , intCodEntidade
                                                         , 'M'
                                                         , 'Controle/' || varExercicio
                                                         , '31-12-' || varExercicio
                                                            );
               bolCriouLote := TRUE;
            END IF;


            varAux            := RecLancamento.cod_estrutural || ' Codigo plano => ' ||recLancamento.cod_plano;
            intSeqIns         := intSeqIns  + 1;

            -- IF RecLancamento.cod_estrutural LIKE '8.2.2.1.4%' THEN
            --     PERFORM contabilidade.encerramentoAnualLancamentos( varExercicio
            --                                                       , intSeqIns
            --                                                       , intCodlote
            --                                                       , intCodEntidade
            --                                                       , intCodHistorico
            --                                                       , RecLancamento.saldo
            --                                                       , RecLancamento.cod_estrutural
            --                                                       , '7.2.2.1.0.00.00'
            --                                                       );
            -- ELSE
            INSERT INTO contabilidade.lancamento( sequencia
                                                , cod_lote
                                                , tipo
                                                , exercicio
                                                , cod_entidade
                                                , cod_historico
                                                , complemento)
                                         VALUES ( intSeqIns
                                                , intCodlote
                                                , 'M'
                                                , varExercicio
                                                , intCodEntidade
                                                , intCodHistorico
                                                , '');
       
            -- VERIFICANDO SE DEVE FAZER LANCAMENTO DE DEBITO OU DE CREDITO
            CASE SUBSTR(RecLancamento.cod_estrutural,01,01)
                WHEN '7' THEN 

                    IF (RecLancamento.saldo > 0) THEN

                        -- AS CONTAS DO GRUPO 7 DEVEM SER CREDITADAS
                        INSERT INTO contabilidade.valor_lancamento( cod_lote
                                                                  , tipo
                                                                  , sequencia
                                                                  , exercicio
                                                                  , tipo_valor
                                                                  , cod_entidade
                                                                  , vl_lancamento)
                                                           VALUES ( intCodlote
                                                                  , 'M'
                                                                  , intSeqIns
                                                                  , varExercicio
                                                                  , 'D'
                                                                  , intCodEntidade
                                                                  , RecLancamento.saldo);
  
                        INSERT INTO contabilidade.conta_debito ( cod_lote
                                                               , tipo
                                                               , sequencia
                                                               , exercicio
                                                               , tipo_valor
                                                               , cod_entidade
                                                               , cod_plano)
                                                        VALUES ( intCodlote
                                                               , 'M'
                                                               , intSeqIns
                                                               , varExercicio
                                                               , 'D'
                                                               , intCodEntidade
                                                               , RecLancamento.cod_plano);
                ELSE
                        -- AS CONTAS DO GRUPO 7 DEVEM SER CREDITADAS
                        INSERT INTO contabilidade.valor_lancamento( cod_lote
                                                                  , tipo
                                                                  , sequencia
                                                                  , exercicio
                                                                  , tipo_valor
                                                                  , cod_entidade
                                                                  , vl_lancamento)
                                                           VALUES ( intCodlote
                                                                  , 'M'
                                                                  , intSeqIns
                                                                  , varExercicio
                                                                  , 'C'
                                                                  , intCodEntidade
                                                                  , RecLancamento.saldo);
  
                        INSERT INTO contabilidade.conta_credito( cod_lote
                                                               , tipo
                                                               , sequencia
                                                               , exercicio
                                                               , tipo_valor
                                                               , cod_entidade
                                                               , cod_plano)
                                                        VALUES ( intCodlote
                                                               , 'M'
                                                               , intSeqIns
                                                               , varExercicio
                                                               , 'C'
                                                               , intCodEntidade
                                                               , RecLancamento.cod_plano);

                END IF;


                WHEN '8' THEN

                    IF (RecLancamento.saldo > 0) THEN

                        -- AS CONTAS DO GRUPO 8 DEVEM SER DEBITADAS
                        INSERT INTO contabilidade.valor_lancamento( cod_lote
                                                                  , tipo
                                                                  , sequencia
                                                                  , exercicio
                                                                  , tipo_valor
                                                                  , cod_entidade
                                                                  , vl_lancamento)
                                                           VALUES ( intCodlote
                                                                  , 'M'
                                                                  , intSeqIns
                                                                  , varExercicio
                                                                  , 'D'
                                                                  , intCodEntidade
                                                                  , RecLancamento.saldo);
        
                        INSERT INTO contabilidade.conta_debito( cod_lote
                                                            , tipo
                                                            , sequencia
                                                            , exercicio
                                                            , tipo_valor
                                                            , cod_entidade
                                                            , cod_plano)
                                                     VALUES ( intCodlote
                                                            , 'M'
                                                            , intSeqIns
                                                            , varExercicio
                                                            , 'D'
                                                            , intCodEntidade
                                                            , RecLancamento.cod_plano);
                ELSE
                        INSERT INTO contabilidade.valor_lancamento( cod_lote
                                                                  , tipo
                                                                  , sequencia
                                                                  , exercicio
                                                                  , tipo_valor
                                                                  , cod_entidade
                                                                  , vl_lancamento)
                                                           VALUES ( intCodlote
                                                                  , 'M'
                                                                  , intSeqIns
                                                                  , varExercicio
                                                                  , 'C'
                                                                  , intCodEntidade
                                                                  , RecLancamento.saldo);
        

                    IF RecLancamento.cod_estrutural LIKE '8.2.2.1.4%' THEN

                        INSERT INTO contabilidade.conta_credito( cod_lote
                                                            , tipo
                                                            , sequencia
                                                            , exercicio
                                                            , tipo_valor
                                                            , cod_entidade
                                                            , cod_plano)
                                                     VALUES ( intCodlote
                                                            , 'M'
                                                            , intSeqIns
                                                            , varExercicio
                                                            , 'C'
                                                            , intCodEntidade
                                                            , '7.2.2.1.0.00.00');
                    
                    ELSE 
                    
                        INSERT INTO contabilidade.conta_credito( cod_lote
                                                            , tipo
                                                            , sequencia
                                                            , exercicio
                                                            , tipo_valor
                                                            , cod_entidade
                                                            , cod_plano)
                                                     VALUES ( intCodlote
                                                            , 'M'
                                                            , intSeqIns
                                                            , varExercicio
                                                            , 'C'
                                                            , intCodEntidade
                                                            , RecLancamento.cod_plano);
                    END IF;
                END IF;
             ELSE 
            END CASE;

         END IF;
      END LOOP;

      Insert Into administracao.configuracao ( exercicio
                                             , cod_modulo
                                             , parametro
                                             , valor)
                                      Values ( varExercicio
                                              , 9
                                              , 'encer_ctrl_' || BTRIM(TO_CHAR(intCodEntidade, '9'))
                                              , 'TRUE');

   END IF;

   RETURN;

END;  $$ LANGUAGE plpgsql;

--
-- Verifica se houve lançamentos de Variações Patrimoniais.
--
CREATE OR REPLACE function contabilidade.fezEncerramentoAnualLancamentosVariacoesPatrimoniais2013( varExercicio VARCHAR(4), intCodEntidade INTEGER )
RETURNS BOOLEAN as $$
DECLARE
   bolFezLancamto BOOLEAN := FALSE;
BEGIN

   PERFORM 1
      FROM administracao.configuracao
     WHERE exercicio =  varExercicio
      AND cod_modulo = 9
      AND parametro  =  'encer_var_patri_' || BTRIM(TO_CHAR(intCodEntidade, '9'));

   IF FOUND  THEN
      bolFezLancamto := TRUE;
   END IF;

   RETURN bolFezLancamto;

END;  $$ LANGUAGE plpgsql;
--
-- Verifica se houve lançamento Orcamentário.
--
CREATE OR REPLACE function contabilidade.fezEncerramentoAnualLancamentosOrcamentario2013( varExercicio VARCHAR(4), intCodEntidade INTEGER )
RETURNS BOOLEAN as $$
DECLARE
   bolFezLancamto BOOLEAN := FALSE;
BEGIN

   PERFORM 1
      FROM administracao.configuracao
     WHERE exercicio =  varExercicio
      AND cod_modulo = 9
      AND parametro  =  'encer_orc_' || BTRIM(TO_CHAR(intCodEntidade, '9'));

   IF FOUND  THEN
      bolFezLancamto := TRUE;
   END IF;

   RETURN bolFezLancamto;

END;  $$ LANGUAGE plpgsql;
--
-- Verifica se houve lançamento de Controle.
--
CREATE OR REPLACE function contabilidade.fezEncerramentoAnualLancamentosControle2013( varExercicio VARCHAR(4), intCodEntidade INTEGER )
RETURNS BOOLEAN as $$
DECLARE
   bolFezLancamto BOOLEAN := FALSE;
BEGIN

   PERFORM 1
      FROM administracao.configuracao
     WHERE exercicio =  varExercicio
      AND cod_modulo = 9
      AND parametro  =  'encer_ctrl_' || BTRIM(TO_CHAR(intCodEntidade, '9'));

   IF FOUND  THEN
      bolFezLancamto := TRUE;
   END IF;

   RETURN bolFezLancamto;

END;  $$ LANGUAGE plpgsql;

/*
 * Funções para exclusão dos lançamentos de encerramento
 */

CREATE OR REPLACE function contabilidade.excluiEncerramentoAnualLancamentosVariacoesPatrimoniais2013( varExercicio VARCHAR(4)
                                                                                        , intCodEntidade INTEGER
                                                                                        )
RETURNS VOID as $$
DECLARE
   intCodLote INTEGER;
BEGIN

    SELECT lote.cod_lote
      INTO intCodLote
      FROM contabilidade.lote
         , contabilidade.lancamento
     WHERE lote.exercicio           = varExercicio
       AND lote.cod_entidade        = intCodEntidade
       AND lote.tipo                = 'M'
       AND lote.dt_lote             = to_date(BTRIM(varExercicio::text) || '-12-31', 'yyyy-mm-dd')
       AND BTRIM(lote.nom_lote)     = 'Variações Patrimoniais/' || varExercicio
       AND lote.exercicio           = lancamento.exercicio
       AND lote.cod_entidade        = lancamento.cod_entidade
       AND lote.tipo                = lancamento.tipo
       AND lote.cod_lote            = lancamento.cod_lote
       AND ( lancamento.cod_historico = 801
          OR lancamento.cod_historico = 802 )

     LIMIT 1
   ;

   IF FOUND THEN
      Delete From contabilidade.empenhamento       where cod_lote = intCodlote And exercicio = varExercicio AND cod_entidade = intCodEntidade And tipo = 'M';
      Delete From contabilidade.lancamento_empenho where cod_lote = intCodlote And exercicio = varExercicio AND cod_entidade = intCodEntidade And tipo = 'M';
      Delete From contabilidade.conta_credito      where cod_lote = intCodlote And exercicio = varExercicio AND cod_entidade = intCodEntidade And tipo = 'M';
      Delete From contabilidade.conta_debito       where cod_lote = intCodlote And exercicio = varExercicio AND cod_entidade = intCodEntidade And tipo = 'M';
      Delete from contabilidade.valor_lancamento   where cod_lote = intCodlote And exercicio = varExercicio AND cod_entidade = intCodEntidade And tipo = 'M';
      Delete from contabilidade.lancamento         where cod_lote = intCodlote And exercicio = varExercicio AND cod_entidade = intCodEntidade And tipo = 'M';
      Delete from contabilidade.lote               where cod_lote = intCodlote And exercicio = varExercicio AND cod_entidade = intCodEntidade And tipo = 'M';
   END IF;

   DELETE
     FROM administracao.configuracao
    WHERE exercicio  =  varExercicio
      AND cod_modulo = 9
      AND parametro  = 'encer_var_patri_' || BTRIM(TO_CHAR(intCodEntidade, '9'));


   RETURN;

END;  $$ LANGUAGE plpgsql;

CREATE OR REPLACE function contabilidade.excluiEncerramentoAnualLancamentosOrcamentario2013( varExercicio VARCHAR(4)
                                                                                  , intCodEntidade INTEGER
                                                                                  )
RETURNS VOID as $$
DECLARE
   intCodLote INTEGER;
BEGIN

    SELECT lote.cod_lote
      INTO intCodLote
      FROM contabilidade.lote
         , contabilidade.lancamento
     WHERE lote.exercicio           = varExercicio
       AND lote.cod_entidade        = intCodEntidade
       AND lote.tipo                = 'M'
       AND lote.dt_lote             = to_date(BTRIM(varExercicio::text) || '-12-31', 'yyyy-mm-dd')
       AND BTRIM(lote.nom_lote)     = 'Orçamentário/' || varExercicio
       AND lote.exercicio           = lancamento.exercicio
       AND lote.cod_entidade        = lancamento.cod_entidade
       AND lote.tipo                = lancamento.tipo
       AND lote.cod_lote            = lancamento.cod_lote
       AND lancamento.cod_historico = 803
     LIMIT 1
   ;

   IF FOUND THEN
      Delete From contabilidade.empenhamento       where cod_lote = intCodlote And exercicio = varExercicio AND cod_entidade = intCodEntidade And tipo = 'M';
      Delete From contabilidade.lancamento_empenho where cod_lote = intCodlote And exercicio = varExercicio AND cod_entidade = intCodEntidade And tipo = 'M';
      Delete From contabilidade.conta_credito      where cod_lote = intCodlote And exercicio = varExercicio AND cod_entidade = intCodEntidade And tipo = 'M';
      Delete From contabilidade.conta_debito       where cod_lote = intCodlote And exercicio = varExercicio AND cod_entidade = intCodEntidade And tipo = 'M';
      Delete from contabilidade.valor_lancamento   where cod_lote = intCodlote And exercicio = varExercicio AND cod_entidade = intCodEntidade And tipo = 'M';
      Delete from contabilidade.lancamento         where cod_lote = intCodlote And exercicio = varExercicio AND cod_entidade = intCodEntidade And tipo = 'M';
      Delete from contabilidade.lote               where cod_lote = intCodlote And exercicio = varExercicio AND cod_entidade = intCodEntidade And tipo = 'M';
   END IF;

   DELETE
     FROM administracao.configuracao
    WHERE exercicio  =  varExercicio
      AND cod_modulo = 9
      AND parametro  = 'encer_orc_' || BTRIM(TO_CHAR(intCodEntidade, '9'));

   RETURN;

END;  $$ LANGUAGE plpgsql;

CREATE OR REPLACE function contabilidade.excluiEncerramentoAnualLancamentosControle2013( varExercicio VARCHAR(4)
                                                                                  , intCodEntidade INTEGER
                                                                                  )
RETURNS VOID as $$
DECLARE
   intCodLote INTEGER;
BEGIN

    SELECT lote.cod_lote
      INTO intCodLote
      FROM contabilidade.lote
         , contabilidade.lancamento
     WHERE lote.exercicio           = varExercicio
       AND lote.cod_entidade        = intCodEntidade
       AND lote.tipo                = 'M'
       AND lote.dt_lote             = to_date(BTRIM(varExercicio::text) || '-12-31', 'yyyy-mm-dd')
       AND BTRIM(lote.nom_lote)     = 'Controle/' || varExercicio
       AND lote.exercicio           = lancamento.exercicio
       AND lote.cod_entidade        = lancamento.cod_entidade
       AND lote.tipo                = lancamento.tipo
       AND lote.cod_lote            = lancamento.cod_lote
       AND lancamento.cod_historico = 804
     LIMIT 1
   ;

   IF FOUND THEN
      Delete From contabilidade.empenhamento       where cod_lote = intCodlote And exercicio = varExercicio AND cod_entidade = intCodEntidade And tipo = 'M';
      Delete From contabilidade.lancamento_empenho where cod_lote = intCodlote And exercicio = varExercicio AND cod_entidade = intCodEntidade And tipo = 'M';
      Delete From contabilidade.conta_credito      where cod_lote = intCodlote And exercicio = varExercicio AND cod_entidade = intCodEntidade And tipo = 'M';
      Delete From contabilidade.conta_debito       where cod_lote = intCodlote And exercicio = varExercicio AND cod_entidade = intCodEntidade And tipo = 'M';
      Delete from contabilidade.valor_lancamento   where cod_lote = intCodlote And exercicio = varExercicio AND cod_entidade = intCodEntidade And tipo = 'M';
      Delete from contabilidade.lancamento         where cod_lote = intCodlote And exercicio = varExercicio AND cod_entidade = intCodEntidade And tipo = 'M';
      Delete from contabilidade.lote               where cod_lote = intCodlote And exercicio = varExercicio AND cod_entidade = intCodEntidade And tipo = 'M';
   END IF;

   DELETE
     FROM administracao.configuracao
    WHERE exercicio  =  varExercicio
      AND cod_modulo = 9
      AND parametro  = 'encer_ctrl_' || BTRIM(TO_CHAR(intCodEntidade, '9'));

   RETURN;

END;  $$ LANGUAGE plpgsql;