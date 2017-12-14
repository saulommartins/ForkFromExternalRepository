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
    * PL Lançamento Contábil de baixa de bens por Alienacao
    * Data de Criação: 20/04/2016

    * @author Analista:       Gelson Wolowski Gonçalves
    * @author Desenvolvedor: Arthur Cruz
    
    * @package URBEM
    * @subpackage 

    $Id: $
*/
CREATE OR REPLACE FUNCTION contabilidade.fn_insere_lancamentos_baixa_patrimonio_alienacao(INTEGER, INTEGER, INTEGER, VARCHAR, DATE, NUMERIC, INTEGER, INTEGER, VARCHAR, TIMESTAMP, INTEGER, VARCHAR, BOOLEAN) RETURNS VOID AS $$
DECLARE
    PinCodBem                   ALIAS FOR $1;
    PinCodPlanoCaixaEquivalente ALIAS FOR $2;
    PinCodEntidade              ALIAS FOR $3;
    PstExercicio                ALIAS FOR $4;
    PdtDataBaixa                ALIAS FOR $5;
    PnuValorAlienacao           ALIAS FOR $6;
    PinCodArrecadacao           ALIAS FOR $7;
    PinCodRecurso               ALIAS FOR $8;
    PstExercicioArrecadacao     ALIAS FOR $9;
    PstTimestampArrecadacao     ALIAS FOR $10;
    PinCodHistorico             ALIAS FOR $11;
    PstTipo                     ALIAS FOR $12;
    PboEstorno                  ALIAS FOR $13;
    
    inCodLote                   INTEGER := 0;
    inSequencia                 INTEGER := 0;
    inAuxCodPlano               INTEGER := 0;
    inCodLancBaixa              INTEGER := 0;
    inCodPlanoAlienacao         INTEGER := 0;
    inCodPlanoDebReceita        INTEGER := 0;
    inCodPlanoCredReceita       INTEGER := 0;
    inCodPlanoDebControle       INTEGER := 0;
    inCodPlanoCredControle      INTEGER := 0;
    inTipoGanhoAlienacao        INTEGER := 0;
    nuValorGanhoPerda           NUMERIC := 0.00;
    stNomeLote                  VARCHAR := '';
    stComplemento               VARCHAR := '';
    stSql                       VARCHAR := '';
    stFiltro                    VARCHAR := '';
    boGanho                     BOOLEAN;
    reBaixaBemAlienacao         RECORD;
BEGIN
    
    ---- Resgate das contas fixas de Cŕedito e Débito ----
    -- Receita a realizar
        SELECT INTO
               inCodPlanoDebReceita
               cod_plano
         FROM contabilidade.plano_conta 
   INNER JOIN contabilidade.plano_analitica
           ON plano_analitica.exercicio  = plano_conta.exercicio 
          AND plano_analitica.cod_conta  = plano_conta.cod_conta 
        WHERE plano_conta.cod_estrutural = '6.2.1.1.0.00.00.00.00.00'
          AND plano_analitica.exercicio  = PstExercicio;

     IF inCodPlanoDebReceita IS NULL THEN
        RAISE EXCEPTION 'Conta ( 6.2.1.1.0.00.00.00.00.00 ) não é analítica ou não está cadastrada no plano de contas.';
     END IF;
     
     -- Receita Realizada
        SELECT INTO
               inCodPlanoCredReceita
               cod_plano
         FROM contabilidade.plano_conta 
   INNER JOIN contabilidade.plano_analitica
           ON plano_analitica.exercicio  = plano_conta.exercicio 
          AND plano_analitica.cod_conta  = plano_conta.cod_conta 
        WHERE plano_conta.cod_estrutural = '6.2.1.2.0.00.00.00.00.00'
          AND plano_analitica.exercicio  = PstExercicio;

     IF inCodPlanoCredReceita IS NULL THEN
        RAISE EXCEPTION 'Conta ( 6.2.1.2.0.00.00.00.00.00 ) não é analítica ou não está cadastrada no plano de contas.';
     END IF;
     
     -- Controle Da Disponibilidade   
       SELECT INTO
              inCodPlanoDebControle
              plano_analitica.cod_plano
         
         FROM contabilidade.plano_conta                           
   
   INNER JOIN contabilidade.plano_analitica                       
           ON plano_analitica.cod_conta = plano_conta.cod_conta   
          AND plano_analitica.exercicio = plano_conta.exercicio   
   
   INNER JOIN contabilidade.plano_recurso                         
           ON plano_recurso.cod_plano = plano_analitica.cod_plano 
          AND plano_recurso.exercicio = plano_analitica.exercicio
          
        WHERE plano_conta.cod_estrutural LIKE '7.2.1.1.1.%' 
          AND plano_conta.exercicio      = PstExercicio 
          AND plano_recurso.cod_recurso  = PinCodRecurso;
              
   
     IF inCodPlanoDebControle IS NULL THEN
        RAISE EXCEPTION 'Conta ( % ) não é analítica ou não está cadastrada no plano de contas.', inCodPlanoDebControle;
     END IF;
     
     -- Disponibilidade por destinação
        SELECT INTO
              inCodPlanoCredControle
              plano_analitica.cod_plano
         
         FROM contabilidade.plano_conta                           
   
   INNER JOIN contabilidade.plano_analitica                       
           ON plano_analitica.cod_conta = plano_conta.cod_conta   
          AND plano_analitica.exercicio = plano_conta.exercicio   
   
   INNER JOIN contabilidade.plano_recurso                         
           ON plano_recurso.cod_plano = plano_analitica.cod_plano 
          AND plano_recurso.exercicio = plano_analitica.exercicio
          
        WHERE plano_conta.cod_estrutural LIKE '8.2.1.1.1.%' 
          AND plano_conta.exercicio      = PstExercicio 
          AND plano_recurso.cod_recurso  = PinCodRecurso;

     IF inCodPlanoCredControle IS NULL THEN
        RAISE EXCEPTION 'Conta ( % ) não é analítica ou não está cadastrada no plano de contas.', inCodPlanoCredControle;
     END IF;

    -- Recupera os dados do bem a ser alienado
    -- ver da possbilidade de faze essa consulta fora, e mandar somente os codPlanos pela pl...
    stSql := '
            SELECT bem.cod_bem
                 , bem.descricao
                 , grupo_plano_analitica.cod_plano
                 , grupo_plano_analitica.cod_plano_alienacao_ganho
                 , grupo_plano_analitica.cod_plano_alienacao_perda
                 , natureza.cod_tipo
                 , natureza.cod_natureza
                 , natureza.nom_natureza
                 , grupo.cod_grupo
                 , grupo.nom_grupo
                 , bem_comprado.cod_entidade
                 , SUM((
                    SELECT vl_atualizado
                      FROM patrimonio.fn_depreciacao_acumulada( bem.cod_bem )
                        AS retorno (  cod_bem            INTEGER
                                    , vl_acumulado       NUMERIC
                                    , vl_atualizado      NUMERIC
                                    , vl_bem             NUMERIC
                                    , min_competencia    VARCHAR
                                    , max_competencia    VARCHAR
                                   )
                     WHERE retorno.cod_bem = bem.cod_bem
                    ) ) AS vl_liquido_contabil
    
              FROM patrimonio.bem
              
        INNER JOIN patrimonio.bem_comprado
                ON bem_comprado.cod_bem = bem.cod_bem
    
        INNER JOIN patrimonio.especie
                ON especie.cod_natureza = bem.cod_natureza
               AND especie.cod_grupo    = bem.cod_grupo
               AND especie.cod_especie  = bem.cod_especie
    
        INNER JOIN patrimonio.grupo
                ON grupo.cod_natureza = especie.cod_natureza
               AND grupo.cod_grupo    = especie.cod_grupo
    
        INNER JOIN patrimonio.natureza
                ON natureza.cod_natureza = grupo.cod_natureza
    
        LEFT JOIN patrimonio.grupo_plano_analitica
                ON grupo_plano_analitica.cod_grupo    = grupo.cod_grupo
               AND grupo_plano_analitica.cod_natureza = grupo.cod_natureza
               AND grupo_plano_analitica.exercicio    = '|| quote_literal(PstExercicio) ||'

             WHERE bem.cod_bem = '|| PinCodBem ||'
               AND bem_comprado.cod_entidade = '|| PinCodEntidade ||'

          GROUP BY bem.cod_bem
                 , bem.descricao
                 , grupo_plano_analitica.cod_plano
                 , grupo_plano_analitica.cod_plano_alienacao_ganho
                 , grupo_plano_analitica.cod_plano_alienacao_perda
                 , natureza.cod_tipo
                 , natureza.cod_natureza
                 , natureza.nom_natureza
                 , grupo.cod_grupo
                 , grupo.nom_grupo
                 , bem_comprado.cod_entidade ';
    
    FOR reBaixaBemAlienacao IN EXECUTE stSql
    LOOP
        IF PboEstorno = FALSE THEN
            stNomeLote := 'Lançamento de Baixa Patrimonial por Alienação do bem: '|| reBaixaBemAlienacao.cod_bem;
        ELSE
            stNomeLote := 'Lançamento de Estorno de Baixa Patrimonial por Alienação do Bem: ' || reBaixaBemAlienacao.cod_bem;
        END IF;

        -- Recupera o último cod_lote a ser inserido na tabela contabilidade.lancamento
        stFiltro  :=           ' WHERE exercicio    = ' || quote_literal(PstExercicio); 
        stFiltro  := stFiltro || ' AND tipo         = ' || quote_literal(PstTipo);
        stFiltro  := stFiltro || ' AND cod_entidade = ' || PinCodEntidade;
        inCodLote := publico.fn_proximo_cod('cod_lote','contabilidade.lote', stFiltro);

        INSERT INTO contabilidade.lote
            (cod_lote, exercicio, tipo, cod_entidade, nom_lote, dt_lote)
        VALUES
            (inCodLote, PstExercicio, PstTipo, PinCodEntidade, stNomeLote, PdtDataBaixa);
               
        ---- Lançamento Patrimonial (Caixa e Equivalentes) ----
        IF PinCodPlanoCaixaEquivalente IS NOT NULL THEN
        
            stFiltro    :=           ' WHERE exercicio    = ' || quote_literal(PstExercicio);
            stFiltro    := stFiltro || ' AND tipo         = ' || quote_literal(PstTipo);
            stFiltro    := stFiltro || ' AND cod_entidade = ' || reBaixaBemAlienacao.cod_entidade;
            stFiltro    := stFiltro || ' AND cod_lote     = ' || inCodLote;
            inSequencia := publico.fn_proximo_cod('sequencia','contabilidade.lancamento', stFiltro);
            
            -- É inserido 1 registro a débito (estorno = false) ou a crédito (estorno = true)
            IF PboEstorno = FALSE THEN
            
                INSERT INTO contabilidade.lancamento
                    (sequencia, cod_lote, tipo, exercicio, cod_entidade, cod_historico, complemento)
                VALUES
                    (inSequencia, inCodLote, PstTipo, PstExercicio, reBaixaBemAlienacao.cod_entidade, PinCodHistorico, 'Baixa por Alienação (Lançamento Caixa e Equivalentes): ' || reBaixaBemAlienacao.cod_bem || ' - ' || reBaixaBemAlienacao.descricao);
                
                INSERT INTO contabilidade.valor_lancamento
                    (sequencia, exercicio, tipo, cod_lote, cod_entidade, tipo_valor, vl_lancamento)
                VALUES
                    (inSequencia, PstExercicio, PstTipo, inCodLote, reBaixaBemAlienacao.cod_entidade,'D', PnuValorAlienacao );
                    
                INSERT INTO contabilidade.conta_debito
                    (sequencia, exercicio, tipo, cod_lote, cod_entidade, tipo_valor, cod_plano )
                VALUES
                    (inSequencia, PstExercicio, PstTipo, inCodLote, reBaixaBemAlienacao.cod_entidade, 'D', PinCodPlanoCaixaEquivalente );

            ELSE
            
                INSERT INTO contabilidade.lancamento
                    (sequencia, cod_lote, tipo, exercicio, cod_entidade, cod_historico, complemento)
                VALUES
                    (inSequencia, inCodLote, PstTipo, PstExercicio, reBaixaBemAlienacao.cod_entidade, PinCodHistorico, 'Estorno de Baixa por Alienação (Lançamento Caixa e Equivalentes): ' || reBaixaBemAlienacao.cod_bem || ' - ' || reBaixaBemAlienacao.descricao);
            
                INSERT INTO contabilidade.valor_lancamento
                    (sequencia, exercicio, tipo, cod_lote, cod_entidade, tipo_valor, vl_lancamento)
                VALUES
                    (inSequencia, PstExercicio, PstTipo, inCodLote, reBaixaBemAlienacao.cod_entidade, 'C', (PnuValorAlienacao * -1) );
                
                INSERT INTO contabilidade.conta_credito
                    (sequencia, exercicio, tipo, cod_lote, cod_entidade, tipo_valor, cod_plano )
                VALUES
                    (inSequencia, PstExercicio, PstTipo, inCodLote, reBaixaBemAlienacao.cod_entidade, 'C', PinCodPlanoCaixaEquivalente );
            END IF;
        ELSE
            RAISE EXCEPTION 'Deve ser informada pelo menos uma conta de débito ou crédito, para o lançamento de Caixas e Equivalentes';
        END IF;

        ---- Lançamento Patrimonial (Bem) ----
        IF reBaixaBemAlienacao.cod_plano IS NOT NULL THEN
        
            stFiltro    :=           ' WHERE exercicio    = ' || quote_literal(PstExercicio);
            stFiltro    := stFiltro || ' AND tipo         = ' || quote_literal(PstTipo);
            stFiltro    := stFiltro || ' AND cod_entidade = ' || reBaixaBemAlienacao.cod_entidade;
            stFiltro    := stFiltro || ' AND cod_lote     = ' || inCodLote;
            inSequencia := publico.fn_proximo_cod('sequencia','contabilidade.lancamento', stFiltro);
            
            -- É inserido 1 registro a  crédito (estorno = false) ou a débito (estorno = true)
            IF PboEstorno = FALSE THEN
            
                INSERT INTO contabilidade.lancamento
                  (sequencia, cod_lote, tipo, exercicio, cod_entidade, cod_historico, complemento)
                VALUES
                  (inSequencia, inCodLote, PstTipo, PstExercicio, reBaixaBemAlienacao.cod_entidade, PinCodHistorico, 'Baixa por Alienação (Lançamento Bem): ' || reBaixaBemAlienacao.cod_bem || ' - ' || reBaixaBemAlienacao.descricao);
            
                INSERT INTO contabilidade.valor_lancamento
                    (sequencia, exercicio, tipo, cod_lote, cod_entidade, tipo_valor, vl_lancamento)
                VALUES
                    (inSequencia, PstExercicio, PstTipo, inCodLote, reBaixaBemAlienacao.cod_entidade, 'C', (reBaixaBemAlienacao.vl_liquido_contabil * -1) );
                
                INSERT INTO contabilidade.conta_credito
                    (sequencia, exercicio, tipo, cod_lote, cod_entidade, tipo_valor, cod_plano )
                VALUES
                    (inSequencia, PstExercicio, PstTipo, inCodLote, reBaixaBemAlienacao.cod_entidade, 'C', reBaixaBemAlienacao.cod_plano );

            ELSE
            
                INSERT INTO contabilidade.lancamento
                    (sequencia, cod_lote, tipo, exercicio, cod_entidade, cod_historico, complemento)
                VALUES
                    (inSequencia, inCodLote, PstTipo, PstExercicio, reBaixaBemAlienacao.cod_entidade, PinCodHistorico, 'Estorno de Baixa por Alienação (Lançamento Bem): ' || reBaixaBemAlienacao.cod_bem || ' - ' || reBaixaBemAlienacao.descricao);
            
                INSERT INTO contabilidade.valor_lancamento
                    (sequencia, exercicio, tipo, cod_lote, cod_entidade, tipo_valor, vl_lancamento)
                VALUES
                    (inSequencia, PstExercicio, PstTipo, inCodLote, reBaixaBemAlienacao.cod_entidade,'D', reBaixaBemAlienacao.vl_liquido_contabil );
                    
                INSERT INTO contabilidade.conta_debito
                    (sequencia, exercicio, tipo, cod_lote, cod_entidade, tipo_valor, cod_plano )
                VALUES
                    (inSequencia, PstExercicio, PstTipo, inCodLote, reBaixaBemAlienacao.cod_entidade, 'D', reBaixaBemAlienacao.cod_plano );

            END IF;            
        ELSE
            RAISE EXCEPTION 'Deve ser informada pelo menos uma conta de débito ou crédito, para o lançamento do bem';
        END IF;
        
        ---- Lançamento Patrimonial (Ganho/Perda Alienação) ----
        IF reBaixaBemAlienacao.cod_plano_alienacao_ganho IS NOT NULL OR reBaixaBemAlienacao.cod_plano_alienacao_perda IS NOT NULL THEN

            -- Caso o valor de Alienacao for maior que o VLC, houve um ganho, se não houve perda
            IF PnuValorAlienacao > reBaixaBemAlienacao.vl_liquido_contabil THEN
                nuValorGanhoPerda    := PnuValorAlienacao - reBaixaBemAlienacao.vl_liquido_contabil;
                inCodPlanoAlienacao  := reBaixaBemAlienacao.cod_plano_alienacao_ganho;
                boGanho              := TRUE;
                inTipoGanhoAlienacao := 7;
            ELSE
                nuValorGanhoPerda    := reBaixaBemAlienacao.vl_liquido_contabil - PnuValorAlienacao;
                inCodPlanoAlienacao  := reBaixaBemAlienacao.cod_plano_alienacao_perda;
                boGanho              := FALSE;
                inTipoGanhoAlienacao := 8;
            END IF;
        
            stFiltro    :=           ' WHERE exercicio    = ' || quote_literal(PstExercicio);
            stFiltro    := stFiltro || ' AND tipo         = ' || quote_literal(PstTipo);
            stFiltro    := stFiltro || ' AND cod_entidade = ' || reBaixaBemAlienacao.cod_entidade;
            stFiltro    := stFiltro || ' AND cod_lote     = ' || inCodLote;
            inSequencia := publico.fn_proximo_cod('sequencia','contabilidade.lancamento', stFiltro);
            
            IF PboEstorno = FALSE THEN
            
                INSERT INTO contabilidade.lancamento
                    (sequencia, cod_lote, tipo, exercicio, cod_entidade, cod_historico, complemento)
                VALUES
                    (inSequencia, inCodLote, PstTipo, PstExercicio, reBaixaBemAlienacao.cod_entidade, PinCodHistorico, 'Baixa por Alienação (Lançamento Ganho/Perda): ' || reBaixaBemAlienacao.cod_bem || ' - ' || reBaixaBemAlienacao.descricao);
            
                -- É inserido 1 registro a crédito ( boGanho = true ) ou a débito ( boGanho = false )
                IF boGanho = TRUE THEN
                    INSERT INTO contabilidade.valor_lancamento
                        (sequencia, exercicio, tipo, cod_lote, cod_entidade, tipo_valor, vl_lancamento)
                    VALUES
                        (inSequencia, PstExercicio, PstTipo, inCodLote, reBaixaBemAlienacao.cod_entidade, 'C', (nuValorGanhoPerda * -1) );
                    
                    INSERT INTO contabilidade.conta_credito
                        (sequencia, exercicio, tipo, cod_lote, cod_entidade, tipo_valor, cod_plano )
                    VALUES
                        (inSequencia, PstExercicio, PstTipo, inCodLote, reBaixaBemAlienacao.cod_entidade, 'C', inCodPlanoAlienacao );
                ELSE
                    INSERT INTO contabilidade.valor_lancamento
                        (sequencia, exercicio, tipo, cod_lote, cod_entidade, tipo_valor, vl_lancamento)
                    VALUES
                        (inSequencia, PstExercicio, PstTipo, inCodLote, reBaixaBemAlienacao.cod_entidade,'D', nuValorGanhoPerda );
                        
                    INSERT INTO contabilidade.conta_debito
                        (sequencia, exercicio, tipo, cod_lote, cod_entidade, tipo_valor, cod_plano )
                    VALUES
                        (inSequencia, PstExercicio, PstTipo, inCodLote, reBaixaBemAlienacao.cod_entidade, 'D', inCodPlanoAlienacao );
                END IF;
                
            ELSE
            
                INSERT INTO contabilidade.lancamento
                    (sequencia, cod_lote, tipo, exercicio, cod_entidade, cod_historico, complemento)
                VALUES
                    (inSequencia, inCodLote, PstTipo, PstExercicio, reBaixaBemAlienacao.cod_entidade, PinCodHistorico, 'Estorno de Baixa por Alienação (Lançamento Ganho/Perda): ' || reBaixaBemAlienacao.cod_bem || ' - ' || reBaixaBemAlienacao.descricao);

                -- É inserido 1 registro a débito ( boGanho = true ) ou a crédito ( boGanho = false )
                IF boGanho = TRUE THEN
                    INSERT INTO contabilidade.valor_lancamento
                        (sequencia, exercicio, tipo, cod_lote, cod_entidade, tipo_valor, vl_lancamento)
                    VALUES
                        (inSequencia, PstExercicio, PstTipo, inCodLote, reBaixaBemAlienacao.cod_entidade,'D', nuValorGanhoPerda );
                        
                    INSERT INTO contabilidade.conta_debito
                        (sequencia, exercicio, tipo, cod_lote, cod_entidade, tipo_valor, cod_plano )
                    VALUES
                        (inSequencia, PstExercicio, PstTipo, inCodLote, reBaixaBemAlienacao.cod_entidade, 'D', inCodPlanoAlienacao );
                ELSE
                    INSERT INTO contabilidade.valor_lancamento
                        (sequencia, exercicio, tipo, cod_lote, cod_entidade, tipo_valor, vl_lancamento)
                    VALUES
                        (inSequencia, PstExercicio, PstTipo, inCodLote, reBaixaBemAlienacao.cod_entidade, 'C', (nuValorGanhoPerda * -1) );
                    
                    INSERT INTO contabilidade.conta_credito
                        (sequencia, exercicio, tipo, cod_lote, cod_entidade, tipo_valor, cod_plano )
                    VALUES
                        (inSequencia, PstExercicio, PstTipo, inCodLote, reBaixaBemAlienacao.cod_entidade, 'C', inCodPlanoAlienacao );
                END IF;

            END IF;

        ELSE
            RAISE EXCEPTION 'Deve ser informada pelo menos uma conta de débito ou crédito, para o lançamento do bem';
        END IF;
        
        ---- Lançamento Orçamentrário ----
        IF inCodPlanoDebReceita IS NOT NULL AND inCodPlanoCredReceita IS NOT NULL THEN
        
            IF PboEstorno = FALSE THEN
                stComplemento := 'Baixa por Alienação (Lançamento Orçamentário): ' || reBaixaBemAlienacao.cod_bem || ' - ' || reBaixaBemAlienacao.descricao;
            ELSE
                stComplemento := 'Estorno de Baixa por Alienação (Lançamento Orçamentário): ' || reBaixaBemAlienacao.cod_bem || ' - ' || reBaixaBemAlienacao.descricao;
                -- Inverte as contas no estorno
                inAuxCodPlano         := inCodPlanoCredReceita;
                inCodPlanoCredReceita := inCodPlanoDebReceita;
                inCodPlanoDebReceita  := inAuxCodPlano;
            END IF;
            
            stFiltro    :=           ' WHERE exercicio    = ' || quote_literal(PstExercicio);
            stFiltro    := stFiltro || ' AND tipo         = ' || quote_literal(PstTipo);
            stFiltro    := stFiltro || ' AND cod_entidade = ' || reBaixaBemAlienacao.cod_entidade;
            stFiltro    := stFiltro || ' AND cod_lote     = ' || inCodLote;
            inSequencia := publico.fn_proximo_cod('sequencia','contabilidade.lancamento', stFiltro);
            
            INSERT INTO contabilidade.lancamento
                (sequencia, cod_lote, tipo, exercicio, cod_entidade, cod_historico, complemento)
            VALUES
                (inSequencia, inCodLote, PstTipo, PstExercicio, reBaixaBemAlienacao.cod_entidade, PinCodHistorico, stComplemento);

            -- São inseridos 2 registros um a débito (valor positivo) e outro a crédito (valor negativo)
            INSERT INTO contabilidade.valor_lancamento
                (sequencia, exercicio, tipo, cod_lote, cod_entidade, tipo_valor, vl_lancamento)
            VALUES
                (inSequencia, PstExercicio, PstTipo, inCodLote, reBaixaBemAlienacao.cod_entidade, 'C', (PnuValorAlienacao * -1) );
            
            INSERT INTO contabilidade.conta_credito
                (sequencia, exercicio, tipo, cod_lote, cod_entidade, tipo_valor, cod_plano )
            VALUES
                (inSequencia, PstExercicio, PstTipo, inCodLote, reBaixaBemAlienacao.cod_entidade, 'C', inCodPlanoCredReceita );
            
            INSERT INTO contabilidade.valor_lancamento
                (sequencia, exercicio, tipo, cod_lote, cod_entidade, tipo_valor, vl_lancamento)
            VALUES
                (inSequencia, PstExercicio, PstTipo, inCodLote, reBaixaBemAlienacao.cod_entidade,'D', PnuValorAlienacao );
             
            INSERT INTO contabilidade.conta_debito
                (sequencia, exercicio, tipo, cod_lote, cod_entidade, tipo_valor, cod_plano )
            VALUES
                (inSequencia, PstExercicio, PstTipo, inCodLote, reBaixaBemAlienacao.cod_entidade, 'D', inCodPlanoDebReceita );
            
        ELSE
            RAISE EXCEPTION 'Deve ser informada pelo menos uma conta de débito ou crédito, para o lançamento Orçamentário';
        END IF;
    
        ---- Lançamento Controle ----
        IF inCodPlanoDebControle IS NOT NULL AND inCodPlanoCredControle IS NOT NULL THEN
        
            IF PboEstorno = FALSE THEN
                stComplemento := 'Baixa por Alienação (Lançamento Controle): ' || reBaixaBemAlienacao.cod_bem || ' - ' || reBaixaBemAlienacao.descricao;
            ELSE
                stComplemento := 'Estorno de Baixa por Alienação (Lançamento Controle): ' || reBaixaBemAlienacao.cod_bem || ' - ' || reBaixaBemAlienacao.descricao;
                -- Inverte as contas no estorno
                inAuxCodPlano          := inCodPlanoCredControle;
                inCodPlanoCredControle := inCodPlanoDebControle;
                inCodPlanoDebControle  := inAuxCodPlano;
            END IF;
            
            stFiltro    :=           ' WHERE exercicio    = ' || quote_literal(PstExercicio);
            stFiltro    := stFiltro || ' AND tipo         = ' || quote_literal(PstTipo);
            stFiltro    := stFiltro || ' AND cod_entidade = ' || reBaixaBemAlienacao.cod_entidade;
            stFiltro    := stFiltro || ' AND cod_lote     = ' || inCodLote;
            inSequencia := publico.fn_proximo_cod('sequencia','contabilidade.lancamento', stFiltro);
            
            INSERT INTO contabilidade.lancamento
                (sequencia, cod_lote, tipo, exercicio, cod_entidade, cod_historico, complemento)
            VALUES
                (inSequencia, inCodLote, PstTipo, PstExercicio, reBaixaBemAlienacao.cod_entidade, PinCodHistorico, stComplemento);
            
            -- São inseridos 2 registros um a débito (valor positivo) e outro a crédito (valor negativo)
            INSERT INTO contabilidade.valor_lancamento
                (sequencia, exercicio, tipo, cod_lote, cod_entidade, tipo_valor, vl_lancamento)
            VALUES
                (inSequencia, PstExercicio, PstTipo, inCodLote, reBaixaBemAlienacao.cod_entidade, 'C', (PnuValorAlienacao * -1) );
            
            INSERT INTO contabilidade.conta_credito
                (sequencia, exercicio, tipo, cod_lote, cod_entidade, tipo_valor, cod_plano )
            VALUES
                (inSequencia, PstExercicio, PstTipo, inCodLote, reBaixaBemAlienacao.cod_entidade, 'C', inCodPlanoCredControle );
            
            INSERT INTO contabilidade.valor_lancamento
                (sequencia, exercicio, tipo, cod_lote, cod_entidade, tipo_valor, vl_lancamento)
            VALUES
                (inSequencia, PstExercicio, PstTipo, inCodLote, reBaixaBemAlienacao.cod_entidade,'D', PnuValorAlienacao );
                
            INSERT INTO contabilidade.conta_debito
                (sequencia, exercicio, tipo, cod_lote, cod_entidade, tipo_valor, cod_plano )
            VALUES
                (inSequencia, PstExercicio, PstTipo, inCodLote, reBaixaBemAlienacao.cod_entidade, 'D', inCodPlanoDebControle );
            
        ELSE
            RAISE EXCEPTION 'Deve ser informada pelo menos uma conta de débito ou crédito, para o lançamento de Controle';
        END IF;
        
        -- Recupera o último id de lançamento de baixa de patrimonio alienação
        inCodLancBaixa := publico.fn_proximo_cod('id','contabilidade.lancamento_baixa_patrimonio_alienacao','');
        --- de repetne ver se vouter que inerir 5 vezes na tabela....
        -- Relaciona a baixa com o bem.
        INSERT INTO contabilidade.lancamento_baixa_patrimonio_alienacao
            (id, timestamp, exercicio, cod_entidade, tipo, cod_lote, sequencia, cod_bem, cod_arrecadacao, exercicio_arrecadacao, timestamp_arrecadacao, estorno )
        VALUES
            (inCodLancBaixa, ('now'::text)::timestamp(3), PstExercicio, reBaixaBemAlienacao.cod_entidade, PstTipo, inCodLote, inSequencia, reBaixaBemAlienacao.cod_bem, PinCodArrecadacao, PstExercicioArrecadacao, PstTimestampArrecadacao, PboEstorno);

        -- Insere na tabela de baixa de bem
        IF PboEstorno = FALSE THEN
            INSERT INTO patrimonio.bem_baixado
                ( cod_bem, dt_baixa, motivo, tipo_baixa )
            VALUES
                ( reBaixaBemAlienacao.cod_bem, PdtDataBaixa, 'Baixa de Patrimônio por Alienação', inTipoGanhoAlienacao );
        ELSE
            DELETE FROM patrimonio.bem_baixado
             WHERE cod_bem = reBaixaBemAlienacao.cod_bem;
        END IF;
        
    END LOOP;
    
END;
$$ LANGUAGE 'plpgsql';