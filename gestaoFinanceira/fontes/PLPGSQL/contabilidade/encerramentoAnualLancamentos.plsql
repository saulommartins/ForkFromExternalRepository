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
* Script de DDL e DML
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Revision: 27798 $
* $Name$
* $Author: tonismar $
* $Date: 2008-01-28 22:18:54 -0200 (Mon, 28 Jan 2008) $
*
* Caso de uso: uc-02.02.02
*/


--
-- REcriação das funções de lançamentos encerramento de exercicio.
--
--
-- Função de inclusão de lançamentos.
--
CREATE OR REPLACE function contabilidade.encerramentoAnualLancamentos( varExercicio      VARCHAR
                                                                      , intSeqIns         INTEGER
                                                                      , intCodlote        INTEGER
                                                                      , intCodEntidade    INTEGER
                                                                      , intCodHistorico   INTEGER
                                                                      , numSaldo          NUMERIC(14,2)
                                                                      , intCodPlanoDeb    INTEGER
                                                                      , intCodPlanoCre    INTEGER
                                                                      )
RETURNS VOID as $$
BEGIN
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
                                             , -(numSaldo));

   INSERT INTO contabilidade.valor_lancamento( cod_lote
                                             , tipo
                                             , sequencia
                                             , exercicio
                                             , tipo_valor
                                             , cod_entidade
                                             , vl_lancamento)
                                       VALUES( intCodlote
                                             , 'M'
                                             , intSeqIns
                                             , varExercicio
                                             , 'D'
                                             , intCodEntidade
                                             , numSaldo );


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
                                         , intCodPlanoDeb);

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
                                          , intCodPlanoCre);
   RETURN;

END;  $$ LANGUAGE plpgsql;


--
-- Função Encerramento Anual 2006 Receitas.
--
CREATE OR REPLACE function contabilidade.encerramentoAnualLancamentosReceita( varExercicio VARCHAR(4)
                                                                            , intCodEntidade INTEGER
                                                                            )
RETURNS VOID as $$
DECLARE
   recLancamento        Record;
   varAux               VARCHAR;
   intCodLote           INTEGER;
   intCodHistorico      INTEGER := 810;
   bolCriouLote         BOOLEAN := FALSE;

   intSeqIns            INTEGER := 0;
   numSaldo             NUMERIC(14,2);
   intCodPlanoDeb       INTEGER;
   intCodPlanoCre       INTEGER;

   intCodPlano6111     INTEGER :=0;
   intCodPlano6112     INTEGER :=0;
   intCodPlano6113     INTEGER :=0;
   intCodPlano6114     INTEGER :=0;
   intCodPlano6115     INTEGER :=0;


BEGIN

   IF NOT contabilidade.fezEncerramentoAnualLancamentosReceita( varExercicio, intCodEntidade) THEN
      INSERT INTO contabilidade.historico_contabil( cod_historico
                                                , exercicio
                                                , nom_historico
                                                , complemento)
                                          SELECT 810
                                                , varExercicio
                                                , 'Encerramento Contas Receita'
                                                , 'f'
                                            WHERE 0  = ( SELECT Count(1)
                                                           FROM contabilidade.historico_contabil
                                                          WHERE cod_historico = 810
                                                            AND exercicio     = varExercicio );

      SELECT plano_analitica.cod_plano
        INTO intCodPlano6111
        FROM contabilidade.plano_conta
            , contabilidade.plano_analitica
        WHERE plano_conta.exercicio = plano_analitica.exercicio
          AND plano_conta.cod_conta = plano_analitica.cod_conta
          AND plano_conta.exercicio = varExercicio
          AND plano_conta.cod_estrutural = '6.1.1.1.0.00.00.00.00.00';

      SELECT plano_analitica.cod_plano
        INTO intCodPlano6112
        FROM contabilidade.plano_conta
           , contabilidade.plano_analitica
       WHERE plano_conta.exercicio = plano_analitica.exercicio
         AND plano_conta.cod_conta = plano_analitica.cod_conta
         AND plano_conta.exercicio = varExercicio
         AND plano_conta.cod_estrutural = '6.1.1.2.0.00.00.00.00.00';

      --
      -- Receita e Despesas
      --
      For recLancamento IN SELECT plano_conta.cod_estrutural
                                , plano_analitica.cod_plano
                                , total_debito.valor    AS valor_deb
                                , total_credito.valor   AS valor_cre
                                , ( Coalesce(total_debito.valor,0) - (- Coalesce(total_credito.valor,0)) ) AS saldo
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
                                       AND conta_credito.cod_entidade =  intCodEntidade
                                  GROUP BY cod_plano,conta_credito.exercicio
                                ) AS total_credito    
                               ON contabilidade.plano_analitica.cod_plano = total_credito.cod_plano
                              AND contabilidade.plano_analitica.exercicio = total_credito.exercicio
                            WHERE plano_conta.cod_conta = plano_analitica.cod_conta
                              AND plano_conta.exercicio = plano_analitica.exercicio
                              AND plano_conta.exercicio = varExercicio
                              AND ( substr(cod_estrutural,01,03) IN ('4.1','4.2','4.9','4.7','4.8')
                                 OR substr(cod_estrutural,01,02) IN ('9.') )
                          AND NOT ( total_debito.valor IS NULL AND total_credito.valor IS NULL )
                         ORDER BY plano_conta.cod_estrutural
      LOOP

         IF recLancamento.saldo != 0 THEN
            IF NOT bolCriouLote       THEN
               intCodLote   := contabilidade.fn_insere_lote( varExercicio
                                                         , intCodEntidade
                                                         , 'M'
                                                         , 'Receitas'
                                                         , '31-12-' || varExercicio
                                                         );
               bolCriouLote := TRUE;
            END IF;

            varAux            := RecLancamento.cod_estrutural || ' Codigo plano => ' ||recLancamento.cod_plano;
            intCodPlanoDeb    :=0;
            intCodPlanoCre    :=0;
            intSeqIns         := intSeqIns  + 1;

            IF substr(recLancamento.cod_estrutural,1,3) = '4.1' THEN
               numSaldo        := -(recLancamento.saldo);
               intCodPlanoDeb  := recLancamento.cod_plano;
               intCodPlanoCre  := intCodPlano6111;
            END IF;

            IF substr(recLancamento.cod_estrutural,1,3) = '4.2' THEN
               numSaldo        := -(recLancamento.saldo);
               intCodPlanoDeb  := recLancamento.cod_plano;
               intCodPlanoCre  := intCodPlano6112;
            END IF;

            IF substr(recLancamento.cod_estrutural,1,3) = '4.9' THEN
               numSaldo        := recLancamento.saldo;
               intCodPlanoDeb  := intCodPlano6111;
               intCodPlanoCre  := recLancamento.cod_plano;
            END IF;

            IF substr(recLancamento.cod_estrutural,1,3) = '4.7' THEN
               numSaldo        := -(recLancamento.saldo);
               intCodPlanoCre  := intCodPlano6111;
               intCodPlanoDeb  := recLancamento.cod_plano;
            END IF;

            IF substr(recLancamento.cod_estrutural,1,3) = '4.8' THEN
               numSaldo        := -(recLancamento.saldo);
               intCodPlanoCre  := intCodPlano6111;
               intCodPlanoDeb  := recLancamento.cod_plano;
            END IF;

            IF substr(recLancamento.cod_estrutural,1,2) = '9.' THEN
               numSaldo        := recLancamento.saldo;
               intCodPlanoDeb  := intCodPlano6111;
               intCodPlanoCre  := recLancamento.cod_plano;
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
                                              , 'encer_rec_' || BTRIM(TO_CHAR(intCodEntidade, '9'))
                                              , 'TRUE');
   END IF;

   RETURN;

END;  $$ LANGUAGE plpgsql;


--
-- Função Encerramento Anual 2006 Despesas.
--
CREATE OR REPLACE function contabilidade.encerramentoAnualLancamentosDespesa( varExercicio VARCHAR(4)
                                                                            , intCodEntidade INTEGER
                                                                            )
RETURNS VOID as $$
DECLARE
   recLancamento        Record;
   varAux               VARCHAR;
   intCodLote           INTEGER;
   intCodHistorico      INTEGER := 810;
   bolCriouLote         BOOLEAN := FALSE;

   intSeqIns            INTEGER := 0;
   numSaldo             NUMERIC(14,2);
   intCodPlanoDeb       INTEGER;
   intCodPlanoCre       INTEGER;

   intCodPlano5111     INTEGER := 0;
   intCodPlano5112     INTEGER := 0;

BEGIN

   IF NOT contabilidade.fezEncerramentoAnualLancamentosDespesa(varExercicio, intCodEntidade)
      AND contabilidade.fezEncerramentoAnualLancamentosReceita(varExercicio, intCodEntidade) THEN

      INSERT INTO contabilidade.historico_contabil( cod_historico
                                                , exercicio
                                                , nom_historico
                                                , complemento)
                                          SELECT 810
                                                , varExercicio
                                                , 'Encerramento Contas Despesa'
                                                , 'f'
                                             WHERE 0  = ( SELECT Count(1)
                                                            FROM contabilidade.historico_contabil
                                                         WHERE cod_historico = 810
                                                            AND exercicio     = varExercicio);

      SELECT plano_analitica.cod_plano
      INTO intCodPlano5111
      FROM contabilidade.plano_conta
         , contabilidade.plano_analitica
      WHERE plano_conta.exercicio = plano_analitica.exercicio
         AND plano_conta.cod_conta = plano_analitica.cod_conta
         AND plano_conta.exercicio = varExercicio
         AND plano_conta.cod_estrutural = '5.1.1.1.0.00.00.00.00.00';

      SELECT plano_analitica.cod_plano
      INTO intCodPlano5112
      FROM contabilidade.plano_conta
         , contabilidade.plano_analitica
      WHERE plano_conta.exercicio = plano_analitica.exercicio
         AND plano_conta.cod_conta = plano_analitica.cod_conta
         AND plano_conta.exercicio = varExercicio
         AND plano_conta.cod_estrutural = '5.1.1.2.0.00.00.00.00.00';

      --
      -- Despesas
      --
      For recLancamento IN SELECT plano_conta.cod_estrutural
                                , plano_analitica.cod_plano
                                , total_debito.valor             AS valor_deb
                                , total_credito.valor            AS valor_cre
                                , ( COALESCE(abs(total_credito.valor),0) - COALESCE(total_debito.valor,0) ) AS saldo
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
                                )  AS total_debito     
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
                                )   AS total_credito    
                               ON contabilidade.plano_analitica.cod_plano = total_credito.cod_plano
                              AND contabilidade.plano_analitica.exercicio = total_credito.exercicio
                            WHERE plano_conta.cod_conta = plano_analitica.cod_conta
                              AND plano_conta.exercicio = plano_analitica.exercicio
                              AND plano_conta.exercicio = varExercicio
                              AND substr(cod_estrutural,01,03) IN ('3.3','3.4','3.9')
                          AND NOT ( total_debito.valor IS NULL AND total_credito.valor IS NULL )
                         ORDER BY plano_conta.cod_estrutural
      LOOP
         IF recLancamento.saldo != 0 THEN
            IF NOT bolCriouLote THEN
               intCodLote   := contabilidade.fn_insere_lote( varExercicio
                                                         , intCodEntidade
                                                         , 'M'
                                                         , 'Despesas'
                                                         , '31-12-' || varExercicio
                                                         );
               bolCriouLote := TRUE;
            END IF;

            varAux            := RecLancamento.cod_estrutural || ' Codigo plano => ' ||recLancamento.cod_plano;
            intCodPlanoDeb    :=0;
            intCodPlanoCre    :=0;
            intSeqIns         := intSeqIns  + 1;

            IF substr(recLancamento.cod_estrutural,1,3) = '3.3' OR
               substr(recLancamento.cod_estrutural,1,3) = '3.9' THEN
               numSaldo        := -(recLancamento.saldo);
               intCodPlanoDeb  := intCodPlano5111;
               intCodPlanoCre  := recLancamento.cod_plano;
            END IF;

            IF substr(recLancamento.cod_estrutural,1,3) = '3.4' THEN
               numSaldo        := -(recLancamento.saldo);
               intCodPlanoDeb  := intCodPlano5112;
               intCodPlanoCre  := recLancamento.cod_plano;
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
                                              , 'encer_desp_' || BTRIM(TO_CHAR(intCodEntidade, '9'))
                                              , 'TRUE');
   END IF;

   RETURN;

END;  $$ LANGUAGE plpgsql;


--
-- Função Encerramento Anual 2006 VAriações Patrimoniais.
--
CREATE OR REPLACE function contabilidade.encerramentoAnualLancamentosVariacoesPatri( varExercicio VARCHAR(4)
                                                                                   , intCodEntidade INTEGER
                                                                                   )
RETURNS VOID as $$
DECLARE
   recLancamento        Record;
   varAux               VARCHAR;
   intCodLote           INTEGER;
   intCodHistorico      INTEGER := 820;
   bolCriouLote         BOOLEAN := FALSE;

   intSeqIns            INTEGER := 0;
   numSaldo             NUMERIC(14,2);
   intCodPlanoDeb       INTEGER;
   intCodPlanoCre       INTEGER;

   intCodPlano6310     INTEGER := 0;

   bolEncerramentoReceita          BOOLEAN;
   bolEncerramentoDespesa          BOOLEAN;
   bolEncerramentoVariacoesPatri   BOOLEAN;
BEGIN

   IF NOT contabilidade.fezEncerramentoAnualLancamentosVariacoesPatri( varExercicio, intCodEntidade)
      AND contabilidade.fezEncerramentoAnualLancamentosDespesa(varExercicio, intCodEntidade)
      AND contabilidade.fezEncerramentoAnualLancamentosReceita(varExercicio, intCodEntidade) THEN

      IF bolEncerramentoVariacoesPatri THEN
         RAISE EXCEPTION 'Encerramento já realizado......';
      END IF;

      IF NOT bolEncerramentoReceita OR NOT bolEncerramentoDespesa THEN
         RAISE EXCEPTION 'Não foram realizadados encerramentos necessários anteriores......';
      END IF;

      INSERT INTO contabilidade.historico_contabil( cod_historico
                                                , exercicio
                                                , nom_historico
                                                , complemento)
                                             SELECT 820
                                                , varExercicio
                                                , 'Apuração Resultado do Exercicio'
                                                , 'f'
                                             WHERE 0  = ( SELECT Count(1)
                                                            FROM contabilidade.historico_contabil
                                                            WHERE cod_historico = 820
                                                            AND exercicio     = varExercicio);

      SELECT plano_analitica.cod_plano
      INTO intCodPlano6310
      FROM contabilidade.plano_conta
         , contabilidade.plano_analitica
      WHERE plano_conta.exercicio = plano_analitica.exercicio
         AND plano_conta.cod_conta = plano_analitica.cod_conta
         AND plano_conta.exercicio = varExercicio
         AND plano_conta.cod_estrutural = '6.3.1.0.0.00.00.00.00.00';

      For recLancamento IN SELECT plano_conta.cod_estrutural
                                , plano_analitica.cod_plano
                                , total_debito.valor             AS valor_deb
                                , total_credito.valor            AS valor_cre
                                , CASE WHEN substr(cod_estrutural,01,01) = 5::varchar
                                       THEN ( COALESCE(abs(total_credito.valor),0) - COALESCE(total_debito.valor,0) )
                                       ELSE ( Coalesce(total_debito.valor,0) - (- Coalesce(total_credito.valor,0)) )
                                       END AS saldo
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
                            WHERE plano_conta.cod_conta = plano_analitica.cod_conta
                              AND plano_conta.exercicio = plano_analitica.exercicio
                              AND plano_conta.exercicio = varExercicio
                              AND SUBSTR(cod_estrutural,01,03) IN ('5.1','5.2','6.1', '6.2')
                          AND NOT ( total_debito.valor IS NULL AND total_credito.valor IS NULL )
                         ORDER BY plano_conta.cod_estrutural
      LOOP

         IF recLancamento.saldo != 0 THEN
            IF NOT bolCriouLote  THEN
               intCodLote  := contabilidade.fn_insere_lote( varExercicio
                                                         , intCodEntidade
                                                         , 'M'
                                                         , 'Apuração do Resultado/' || varExercicio
                                                         , '31-12-' || varExercicio
                                                            );
               bolCriouLote := TRUE;
            END IF;


            varAux            := RecLancamento.cod_estrutural || ' Codigo plano => ' ||recLancamento.cod_plano;
            intCodPlanoDeb    :=0;
            intCodPlanoCre    :=0;
            intSeqIns         := intSeqIns  + 1;

            IF substr(recLancamento.cod_estrutural,1,3) = '5.1' THEN
               numSaldo        := -(recLancamento.saldo);
               intCodPlanoDeb  := intCodPlano6310;
               intCodPlanoCre  := recLancamento.cod_plano;
            END IF;

            IF substr(recLancamento.cod_estrutural,1,3) = '5.2' THEN
               numSaldo        := -(recLancamento.saldo);
               intCodPlanoDeb  := intCodPlano6310;
               intCodPlanoCre  := recLancamento.cod_plano;
            END IF;

            IF substr(recLancamento.cod_estrutural,1,3) = '6.1' THEN
               numSaldo        := -(recLancamento.saldo);
               intCodPlanoDeb  := recLancamento.cod_plano;
               intCodPlanoCre  := intCodPlano6310;
            END IF;

            IF substr(recLancamento.cod_estrutural,1,3) = '6.2' THEN
               numSaldo        := -(recLancamento.saldo);
               intCodPlanoDeb  := recLancamento.cod_plano;
               intCodPlanoCre  := intCodPlano6310;
            End if;

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
-- Função Encerramento Anual 2006 Orcamento Compensado.
--
CREATE OR REPLACE function contabilidade.encerramentoAnualLancamentosCompensado( varExercicio VARCHAR(4)
                                                                               , intCodEntidade INTEGER
                                                                               )
RETURNS VOID as $$
DECLARE
   recLancamento        Record;
   varAux               VARCHAR;
   intCodLote           INTEGER;
   intCodHistorico      INTEGER := 800;
   bolCriouLote         BOOLEAN := FALSE;

   intSeqIns            INTEGER := 0;

   bolEncerramentoReceita          BOOLEAN;
   bolEncerramentoDespesa          BOOLEAN;
   bolEncerramentoVariacoesPatri   BOOLEAN;
   bolEncerramentoOrcamento        BOOLEAN;
BEGIN

   IF  NOT  contabilidade.fezEncerramentoAnualLancamentosCompensado( varExercicio, intCodEntidade)
       AND contabilidade.fezEncerramentoAnualLancamentosVariacoesPatri(varExercicio, intCodEntidade)
       AND contabilidade.fezEncerramentoAnualLancamentosDespesa(varExercicio, intCodEntidade)
       AND contabilidade.fezEncerramentoAnualLancamentosReceita(varExercicio, intCodEntidade)   THEN

      IF bolEncerramentoOrcamento THEN
         RAISE EXCEPTION 'Encerramento já realizado......';
      END IF;

      IF NOT bolEncerramentoReceita OR NOT bolEncerramentoDespesa OR NOT bolEncerramentoVariacoesPatri THEN
         RAISE EXCEPTION 'Não foram realizadados encerramentos necessários anteriores......';
      END IF;

      INSERT INTO contabilidade.historico_contabil( cod_historico
                                                , exercicio
                                                , nom_historico
                                                , complemento)
                                          SELECT 800
                                                , varExercicio
                                                , 'Encerramento do Exercício'
                                                , 'f'
                                             WHERE 0  = ( SELECT Count(1)
                                                            FROM contabilidade.historico_contabil
                                                         WHERE cod_historico = 800
                                                            AND exercicio     = varExercicio);

      --
      --
      --
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
                              AND SUBSTR(cod_estrutural,01,03) IN ('1.9','2.9')
                          AND NOT ( total_debito.valor IS NULL AND total_credito.valor IS NULL )
                         ORDER BY plano_conta.cod_estrutural
      LOOP
         IF recLancamento.saldo != 0 THEN
            IF NOT bolCriouLote  THEN
               intCodLote  := contabilidade.fn_insere_lote( varExercicio
                                                         , intCodEntidade
                                                         , 'M'
                                                         , 'Apuração do Resultado/' || varExercicio
                                                         , '31-12-' || varExercicio
                                                            );
               bolCriouLote := TRUE;
            END IF;

            varAux            := RecLancamento.cod_estrutural || ' Codigo plano => ' ||recLancamento.cod_plano;

            --IF RecLancamento.saldo !=  0 THEN
            intSeqIns := intSeqIns  + 1;
            
            
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
	        --END IF;

            -- VERIFICANDO SE DEVE FAZER LANCAMENTO DE DEBITO OU DE CREDITO
            IF abs(RecLancamento.valor_cre) < abs(RecLancamento.valor_deb) THEN

            
		        -- Crédito
		        --IF RecLancamento.saldo < 0 THEN
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
		        --END IF;
		    END IF; 
		    -- TESTANDO CREDITO
		    
		    
		        -- Débito.
		        --IF RecLancamento.saldo > 0 THEN
		        
		    IF abs(RecLancamento.valor_cre) > abs(RecLancamento.valor_deb) THEN
		    
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
		        --END IF;
		    
		    END IF; 
		    -- TESTANDO DEBITO
         END IF;
      END LOOP;

      Insert Into administracao.configuracao ( exercicio
                                             , cod_modulo
                                             , parametro
                                             , valor)
                                      Values ( varExercicio
                                              , 9
                                              , 'encer_comp_' || BTRIM(TO_CHAR(intCodEntidade, '9'))
                                              , 'TRUE');

   END IF;

   RETURN;

END;  $$ LANGUAGE plpgsql;

--
-- Função Encerramento Anual 2006 Resultado Apurado.
--
CREATE OR REPLACE function contabilidade.encerramentoAnualLancamentosResultadoApurado( varExercicio VARCHAR(4)
                                                                                     , intCodEntidade INTEGER
                                                                                     )
RETURNS VOID as $$
DECLARE
   recLancamento        Record;
   varAux               VARCHAR;
   intCodLote           INTEGER;
   intCodHistorico      INTEGER := 890;
   bolCriouLote         BOOLEAN := FALSE;

   intSeqIns            INTEGER := 0;
   numSaldo             NUMERIC(14,2);
   intCodPlanoDeb       INTEGER;
   intCodPlanoCre       INTEGER;

   intCodPlano631      INTEGER :=0;
   intCodPlano2431     INTEGER :=0;

BEGIN

   IF NOT contabilidade.fezEncerramentoAnualLancamentosResultadoApurado( varExercicio, intCodEntidade)
      AND contabilidade.fezEncerramentoAnualLancamentosVariacoesPatri(varExercicio, intCodEntidade)
      AND contabilidade.fezEncerramentoAnualLancamentosDespesa(varExercicio, intCodEntidade)
      AND contabilidade.fezEncerramentoAnualLancamentosReceita(varExercicio, intCodEntidade)   THEN

      INSERT INTO contabilidade.historico_contabil( cod_historico
                                                , exercicio
                                                , nom_historico
                                                , complemento)
                                          SELECT 890
                                                , varExercicio
                                                , 'Vlr Ref. Resultado do Exercício'
                                                , 'f'
                                            WHERE 0  = ( SELECT Count(1)
                                                           FROM contabilidade.historico_contabil
                                                          WHERE cod_historico = 890
                                                            AND exercicio     = varExercicio );

      SELECT plano_analitica.cod_plano
        INTO intCodPlano631
        FROM contabilidade.plano_conta
            , contabilidade.plano_analitica
        WHERE plano_conta.exercicio = plano_analitica.exercicio
          AND plano_conta.cod_conta = plano_analitica.cod_conta
          AND plano_conta.exercicio = varExercicio
          AND plano_conta.cod_estrutural = '6.3.1.0.0.00.00.00.00.00';

      SELECT plano_analitica.cod_plano
        INTO intCodPlano2431
        FROM contabilidade.plano_conta
            , contabilidade.plano_analitica
        WHERE plano_conta.exercicio = plano_analitica.exercicio
          AND plano_conta.cod_conta = plano_analitica.cod_conta
          AND plano_conta.exercicio = varExercicio
          AND plano_conta.cod_estrutural = '2.4.3.1.0.00.00.00.00.00';


      --
      -- Receita e Despesas
      --
      For recLancamento IN SELECT plano_conta.cod_estrutural
                                , plano_analitica.cod_plano
                                , total_debito.valor    AS valor_deb
                                , total_credito.valor   AS valor_cre
                                , ( Coalesce(total_debito.valor,0) - (- Coalesce(total_credito.valor,0)) ) AS saldo
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
                                       AND conta_credito.cod_entidade =  intCodEntidade
                                  GROUP BY cod_plano,conta_credito.exercicio
                                ) AS total_credito    
                               ON contabilidade.plano_analitica.cod_plano = total_credito.cod_plano
                              AND contabilidade.plano_analitica.exercicio = total_credito.exercicio
                            WHERE plano_conta.cod_conta = plano_analitica.cod_conta
                              AND plano_conta.exercicio = plano_analitica.exercicio
                              AND plano_conta.exercicio = varExercicio
                              AND substr(cod_estrutural,01,05) = '6.3.1'
                          AND NOT ( total_debito.valor IS NULL AND total_credito.valor IS NULL )
                         ORDER BY plano_conta.cod_estrutural
      LOOP

         IF recLancamento.saldo != 0 THEN
            IF NOT bolCriouLote       THEN
               intCodLote   := contabilidade.fn_insere_lote( varExercicio
                                                         , intCodEntidade
                                                         , 'M'
                                                         , 'Resultado Apurado'
                                                         , '31-12-' || varExercicio
                                                         );
               bolCriouLote := TRUE;
            END IF;

            varAux            := RecLancamento.cod_estrutural || ' Codigo plano => ' ||recLancamento.cod_plano;
            intCodPlanoDeb    :=0;
            intCodPlanoCre    :=0;
            intSeqIns         := intSeqIns  + 1;

            IF recLancamento.saldo > 0 THEN
               numSaldo        := recLancamento.saldo;
               intCodPlanoDeb  := intCodPlano2431;
               intCodPlanoCre  := intCodPlano631;
            END IF;

            IF recLancamento.saldo < 0 THEN
               numSaldo        := -(recLancamento.saldo);
               intCodPlanoDeb  := intCodPlano631;
               intCodPlanoCre  := intCodPlano2431;
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
                                              , 'encer_res_apu_' || BTRIM(TO_CHAR(intCodEntidade, '9'))
                                              , 'TRUE');
   END IF;

   RETURN;

END;  $$ LANGUAGE plpgsql;

--
-- Verifica se houve lançamentos de receita
--
CREATE OR REPLACE function contabilidade.fezEncerramentoAnualLancamentosReceita( varExercicio VARCHAR(4)
                                                                               , intCodEntidade INTEGER
                                                                               )
RETURNS BOOLEAN as $$
DECLARE
   bolFezLancamto BOOLEAN := FALSE;
BEGIN

   PERFORM 1
      FROM administracao.configuracao
     WHERE exercicio =  varExercicio
      AND cod_modulo = 9
      AND parametro  =  'encer_rec_' || BTRIM(TO_CHAR(intCodEntidade, '9')) ;

   IF FOUND THEN
      bolFezLancamto := TRUE;
   END IF;

   RETURN bolFezLancamto;

END;  $$ LANGUAGE plpgsql;


--
-- Verifica se houve lançamentos de Despesas
--
CREATE OR REPLACE function contabilidade.fezEncerramentoAnualLancamentosDespesa( varExercicio VARCHAR(4)
                                                                               , intCodEntidade INTEGER
                                                                               )
RETURNS BOOLEAN as $$
DECLARE
   bolFezLancamto BOOLEAN := FALSE;
BEGIN

   PERFORM 1
      FROM administracao.configuracao
     WHERE exercicio =  varExercicio
      AND cod_modulo = 9
      AND parametro  =  'encer_desp_' || BTRIM(TO_CHAR(intCodEntidade, '9'));

   IF FOUND  THEN
      bolFezLancamto := TRUE;
   END IF;

   RETURN bolFezLancamto;

END;  $$ LANGUAGE plpgsql;

--
-- Verifica se houve lançamentos de VAriações Patrimoniais.
--
CREATE OR REPLACE function contabilidade.fezEncerramentoAnualLancamentosVariacoesPatri( varExercicio VARCHAR(4)
                                                                                      , intCodEntidade INTEGER
                                                                                      )
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
-- Verifica se houve lançamentos de Compensados.
--
CREATE OR REPLACE function contabilidade.fezEncerramentoAnualLancamentosCompensado( varExercicio VARCHAR(4)
                                                                                  , intCodEntidade INTEGER
                                                                                  )
RETURNS BOOLEAN as $$
DECLARE
   bolFezLancamto BOOLEAN := FALSE;
   numSaldo       NUMERIC(14,2);
BEGIN

   PERFORM 1
      FROM administracao.configuracao
     WHERE exercicio =  varExercicio
      AND cod_modulo = 9
      AND parametro  =  'encer_comp_' || BTRIM(TO_CHAR(intCodEntidade, '9'));

   IF FOUND  THEN
      bolFezLancamto := TRUE;
   END IF;

   RETURN bolFezLancamto;

END;  $$ LANGUAGE plpgsql;

--
-- Verifica se houve lançamentos de Resultado Apurado
--
CREATE OR REPLACE function contabilidade.fezEncerramentoAnualLancamentosResultadoApurado( varExercicio VARCHAR(4)
                                                                                        , intCodEntidade INTEGER
                                                                                        )
RETURNS BOOLEAN as $$
DECLARE
   bolFezLancamto BOOLEAN := FALSE;
BEGIN

   PERFORM 1
      FROM administracao.configuracao
     WHERE exercicio =  varExercicio
      AND cod_modulo = 9
      AND parametro  =  'encer_res_apu_' || BTRIM(TO_CHAR(intCodEntidade, '9')) ;

   IF FOUND THEN
      bolFezLancamto := TRUE;
   END IF;

   RETURN bolFezLancamto;

END;  $$ LANGUAGE plpgsql;

--
-- Exclui se houve lançamentos de receita
--
CREATE OR REPLACE function contabilidade.excluiEncerramentoAnualLancamentosReceita( varExercicio VARCHAR(4)
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
       AND lote.dt_lote             =  to_date(BTRIM(varExercicio::text) || '-12-31', 'yyyy-mm-dd')
       AND BTRIM(lote.nom_lote)     = 'Receitas'
       AND lote.exercicio           = lancamento.exercicio
       AND lote.cod_entidade        = lancamento.cod_entidade
       AND lote.tipo                = lancamento.tipo
       AND lote.cod_lote            = lancamento.cod_lote
       AND lancamento.cod_historico = 810
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
      AND parametro  = 'encer_rec_' || BTRIM(TO_CHAR(intCodEntidade, '9'));


   RETURN;

END;  $$ LANGUAGE plpgsql;

--
-- Verifica se houve lançamentos de Despesas
--
CREATE OR REPLACE function contabilidade.excluiEncerramentoAnualLancamentosDespesa( varExercicio VARCHAR(4)
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
       AND BTRIM(lote.nom_lote)     = 'Despesas'
       AND lote.exercicio           = lancamento.exercicio
       AND lote.cod_entidade        = lancamento.cod_entidade
       AND lote.tipo                = lancamento.tipo
       AND lote.cod_lote            = lancamento.cod_lote
       AND lancamento.cod_historico = 810
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
      AND parametro  = 'encer_desp_' || BTRIM(TO_CHAR(intCodEntidade, '9'));

   RETURN;


END;  $$ LANGUAGE plpgsql;

--
-- Verifica se houve lançamentos de VAriações Patrimoniais.
--
CREATE OR REPLACE function contabilidade.excluiEncerramentoAnualLancamentosVariacoesPatri( varExercicio VARCHAR(4)
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
       AND BTRIM(lote.nom_lote)     = 'Apuração do Resultado/' || varExercicio
       AND lote.exercicio           = lancamento.exercicio
       AND lote.cod_entidade        = lancamento.cod_entidade
       AND lote.tipo                = lancamento.tipo
       AND lote.cod_lote            = lancamento.cod_lote
       AND lancamento.cod_historico = 820
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


--
-- Verifica se houve lançamentos de Compensados.
--
CREATE OR REPLACE function contabilidade.excluiEncerramentoAnualLancamentosCompensado( varExercicio VARCHAR(4)
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
       AND BTRIM(lote.nom_lote)     = 'Apuração do Resultado/' || varExercicio
       AND lote.exercicio           = lancamento.exercicio
       AND lote.cod_entidade        = lancamento.cod_entidade
       AND lote.tipo                = lancamento.tipo
       AND lote.cod_lote            = lancamento.cod_lote
       AND lancamento.cod_historico = 800
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
      AND parametro  = 'encer_comp_' || BTRIM(TO_CHAR(intCodEntidade, '9'));

   RETURN;

END;  $$ LANGUAGE plpgsql;

--
-- Exclui se houve lançamentos de Resultado Apurado
--
CREATE OR REPLACE function contabilidade.excluiEncerramentoAnualLancamentosResultadoApurado( varExercicio VARCHAR(4)
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
       AND lote.dt_lote             =  to_date(BTRIM(varExercicio::text) || '-12-31', 'yyyy-mm-dd')
       AND BTRIM(lote.nom_lote)     = 'Resultado Apurado'
       AND lote.exercicio           = lancamento.exercicio
       AND lote.cod_entidade        = lancamento.cod_entidade
       AND lote.tipo                = lancamento.tipo
       AND lote.cod_lote            = lancamento.cod_lote
       AND lancamento.cod_historico = 890
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
      AND parametro  = 'encer_res_apu_' || BTRIM(TO_CHAR(intCodEntidade, '9'));


   RETURN;

END;  $$ LANGUAGE plpgsql;

-- remove lancamento de implantacao caso tenha feito saldo de balanço

CREATE OR REPLACE function contabilidade.excluiSaldosBalanco( varExercicio VARCHAR(4), intCodEntidade INTEGER )

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
       AND lote.tipo                = 'I'
       AND lote.dt_lote             = to_date( BTRIM( varExercicio::text )|| '-01-01', 'yyyy-mm-dd')
       AND lote.exercicio           = lancamento.exercicio
       AND lote.cod_entidade        = lancamento.cod_entidade
       AND lote.tipo                = lancamento.tipo
       AND lote.cod_lote            = lancamento.cod_lote
       AND lancamento.cod_historico = 1
     LIMIT 1 
   ;

   IF FOUND THEN
      Delete From contabilidade.empenhamento       where cod_lote = intCodlote And exercicio = varExercicio AND cod_entidade = intCodEntidade And tipo = 'I';
      Delete From contabilidade.lancamento_empenho where cod_lote = intCodlote And exercicio = varExercicio AND cod_entidade = intCodEntidade And tipo = 'I';
      Delete From contabilidade.conta_credito      where cod_lote = intCodlote And exercicio = varExercicio AND cod_entidade = intCodEntidade And tipo = 'I';
      Delete From contabilidade.conta_debito       where cod_lote = intCodlote And exercicio = varExercicio AND cod_entidade = intCodEntidade And tipo = 'I';
      Delete from contabilidade.valor_lancamento   where cod_lote = intCodlote And exercicio = varExercicio AND cod_entidade = intCodEntidade And tipo = 'I';
      Delete from contabilidade.lancamento         where cod_lote = intCodlote And exercicio = varExercicio AND cod_entidade = intCodEntidade And tipo = 'I';
      Delete from contabilidade.lote               where cod_lote = intCodlote And exercicio = varExercicio AND cod_entidade = intCodEntidade And tipo = 'I';
   END IF;

   RETURN;
   
END;  $$ LANGUAGE plpgsql;


   CREATE OR REPLACE function manutencao( varExercicio VARCHAR(4))
   RETURNS VOID as $$
   DECLARE
      recEntidade       RECORD;
      intCodEntidade    INTEGER;
   BEGIN
      FOR recEntidade
       IN SELECT cod_entidade
            FROM orcamento.entidade
           WHERE exercicio = ''|| varExercicio || ''
           ORDER BY 1
       LOOP
         intCodEntidade := recEntidade.cod_entidade;


         --
         --
         --
         PERFORM 1
           FROM contabilidade.lote
              , contabilidade.lancamento
          WHERE lote.exercicio           = varExercicio
            AND lote.cod_entidade        = intCodEntidade
            AND lote.tipo                = 'M'
            AND lote.dt_lote             =  to_date(BTRIM(varExercicio::text)||'-12-31', 'yyyy-mm-dd')
            AND BTRIM(lote.nom_lote)     = 'Apuração do Resultado/' || varExercicio
            AND lote.exercicio           = lancamento.exercicio
            AND lote.cod_entidade        = lancamento.cod_entidade
            AND lote.tipo                = lancamento.tipo
            AND lote.cod_lote            = lancamento.cod_lote
            AND lancamento.cod_historico = 800
          LIMIT 1
         ;

         IF FOUND THEN
            --AND parametro  = 'encer_comp_' || BTRIM(TO_CHAR(intCodEntidade, '9'));
            PERFORM 1 FROM administracao.configuracao  WHERE exercicio =  varExercicio AND cod_modulo = 9 AND parametro  =  'encer_comp_' || BTRIM(TO_CHAR(intCodEntidade, '9')) ;
            IF NOT FOUND THEN
               Insert Into administracao.configuracao ( exercicio
                                                      , cod_modulo
                                                      , parametro
                                                      , valor)
                                               Values ( varExercicio
                                                      , 9
                                                      , 'encer_comp_' || BTRIM(TO_CHAR(intCodEntidade, '9'))
                                                      , 'TRUE');
            END IF;
         END IF;

         --
         --
         --
         PERFORM 1
           FROM contabilidade.lote
              , contabilidade.lancamento
          WHERE lote.exercicio           = varExercicio
            AND lote.cod_entidade        = intCodEntidade
            AND lote.tipo                = 'M'
            AND lote.dt_lote             =  to_date(BTRIM(varExercicio::text) || '-12-31', 'yyyy-mm-dd')
            AND BTRIM(lote.nom_lote)     = 'Apuração do Resultado/' || varExercicio
            AND lote.exercicio           = lancamento.exercicio
            AND lote.cod_entidade        = lancamento.cod_entidade
            AND lote.tipo                = lancamento.tipo
            AND lote.cod_lote            = lancamento.cod_lote
            AND lancamento.cod_historico = 820
          LIMIT 1
         ;

         IF FOUND THEN
            --AND parametro  = 'encer_var_patri_' || BTRIM(TO_CHAR(intCodEntidade, '9'));
            PERFORM 1 FROM administracao.configuracao  WHERE exercicio =  varExercicio AND cod_modulo = 9 AND parametro  =  'encer_var_patri_' || BTRIM(TO_CHAR(intCodEntidade, '9')) ;
            IF NOT FOUND THEN
               Insert Into administracao.configuracao ( exercicio
                                                      , cod_modulo
                                                      , parametro
                                                      , valor)
                                               Values ( varExercicio
                                                      , 9
                                                      , 'encer_var_patri_' || BTRIM(TO_CHAR(intCodEntidade, '9'))
                                                      , 'TRUE');
            END IF;
         END IF;

         --
         --
         --
         PERFORM 1
            FROM contabilidade.lote
               , contabilidade.lancamento
           WHERE lote.exercicio           = varExercicio
             AND lote.cod_entidade        = intCodEntidade
             AND lote.tipo                = 'M'
             AND lote.dt_lote             = to_date(BTRIM(varExercicio::text) || '-12-31', 'yyyy-mm-dd' )
             AND BTRIM(lote.nom_lote)     = 'Despesas'
             AND lote.exercicio           = lancamento.exercicio
             AND lote.cod_entidade        = lancamento.cod_entidade
             AND lote.tipo                = lancamento.tipo
             AND lote.cod_lote            = lancamento.cod_lote
             AND lancamento.cod_historico = 810
           LIMIT 1
         ;

         IF FOUND THEN
            --AND parametro  = 'encer_desp_' || BTRIM(TO_CHAR(intCodEntidade, '9'));
            PERFORM 1 FROM administracao.configuracao  WHERE exercicio =  varExercicio::text AND cod_modulo = 9 AND parametro  =  'encer_desp_' || BTRIM(TO_CHAR(intCodEntidade, '9')) ;
            IF NOT FOUND THEN
               Insert Into administracao.configuracao ( exercicio
                                                      , cod_modulo
                                                      , parametro
                                                      , valor)
                                               Values ( varExercicio
                                                      , 9
                                                      , 'encer_desp_' || BTRIM(TO_CHAR(intCodEntidade, '9'))
                                                      , 'TRUE');
            END IF;
         END IF;

         --
         --
         --
         PERFORM 1
           FROM contabilidade.lote
              , contabilidade.lancamento
          WHERE lote.exercicio           = varExercicio
            AND lote.cod_entidade        = intCodEntidade
            AND lote.tipo                = 'M'
            AND lote.dt_lote             =  to_date(BTRIM(varExercicio::text) || '-12-31', 'yyyy-mm-dd' )
            AND BTRIM(lote.nom_lote)     = 'Receitas'
            AND lote.exercicio           = lancamento.exercicio
            AND lote.cod_entidade        = lancamento.cod_entidade
            AND lote.tipo                = lancamento.tipo
            AND lote.cod_lote            = lancamento.cod_lote
            AND lancamento.cod_historico = 810
          LIMIT 1
         ;

         IF FOUND THEN
            --AND parametro  = 'encer_rec_' || BTRIM(TO_CHAR(intCodEntidade, '9'));
            PERFORM 1 FROM administracao.configuracao  WHERE exercicio =  varExercicio AND cod_modulo = 9 AND parametro  =  'encer_rec_' || BTRIM(TO_CHAR(intCodEntidade, '9'));
            IF NOT FOUND THEN
               Insert Into administracao.configuracao ( exercicio
                                                      , cod_modulo
                                                      , parametro
                                                      , valor)
                                               Values ( varExercicio
                                                      , 9
                                                      , 'encer_rec_' || BTRIM(TO_CHAR(intCodEntidade, '9'))
                                                      , 'TRUE');
            END IF;
         END IF;


       END LOOP;

      RETURN;
   END;  $$ LANGUAGE plpgsql;

   SELECT manutencao('2007');
   DROP function manutencao(VARCHAR(4));





