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
* $Revision: 17524 $
* $Name$
* $Author: cako $
* $Date: 2006-11-09 13:34:45 -0200 (Qui, 09 Nov 2006) $
*
* Casos de uso: uc-02.02.22
                uc-02.08.07
*/

/*
$Log$
Revision 1.14  2006/11/09 15:34:45  cako
Bug #6787#

Revision 1.13  2006/10/27 17:28:23  cako
Bug #6787#

Revision 1.12  2006/07/18 20:02:10  eduardo
Bug #6556#

Revision 1.11  2006/07/14 17:58:30  andre.almeida
Bug #6556#

Alterado scripts de NOT IN para NOT EXISTS.

Revision 1.10  2006/07/05 20:37:31  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION contabilidade.fn_mutacao_patrimonio_liquido(VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR) RETURNS SETOF RECORD AS $$ 
DECLARE
    stExercicio         ALIAS FOR $1;
    stEntidade          ALIAS FOR $2;
    stDtInicial         ALIAS FOR $3;
    stDtFinal           ALIAS FOR $4;
    stExercicioAnterior ALIAS FOR $5;
    stSql               VARCHAR   := '';
    stSqlComplemento    VARCHAR   := '';
    reRegistro          RECORD;
    reRegistro2         RECORD;
    arRetorno           NUMERIC[];

BEGIN

    stSql := 'CREATE TEMPORARY TABLE tmp_debito AS
                SELECT *
                FROM (
                    SELECT
                         pc.cod_estrutural
                        ,pa.cod_plano
                        ,vl.tipo_valor
                        ,vl.vl_lancamento
                        ,vl.cod_entidade
                        ,lo.cod_lote
                        ,lo.dt_lote
                        ,lo.exercicio
                        ,lo.tipo
                        ,vl.sequencia
                        ,vl.oid as oid_temp
                        ,sc.cod_sistema
                        ,pc.escrituracao
                        ,pc.indicador_superavit
                    FROM
                         contabilidade.plano_conta            as pc
                        ,contabilidade.plano_analitica        as pa
                        ,contabilidade.conta_debito           as cd
                        ,contabilidade.valor_lancamento       as vl
                        ,contabilidade.lancamento             as la
                        ,contabilidade.lote                   as lo
                        ,contabilidade.sistema_contabil       as sc
                    WHERE   pc.cod_conta    = pa.cod_conta
                    AND     pc.exercicio    = pa.exercicio
                    AND     pa.cod_plano    = cd.cod_plano
                    AND     pa.exercicio    = cd.exercicio
                    AND     cd.cod_lote     = vl.cod_lote
                    AND     cd.tipo         = vl.tipo
                    AND     cd.sequencia    = vl.sequencia
                    AND     cd.exercicio    = vl.exercicio
                    AND     cd.tipo_valor   = vl.tipo_valor
                    AND     cd.cod_entidade = vl.cod_entidade
                    AND     vl.cod_lote     = la.cod_lote
                    AND     vl.tipo         = la.tipo
                    AND     vl.sequencia    = la.sequencia
                    AND     vl.exercicio    = la.exercicio
                    AND     vl.cod_entidade = la.cod_entidade
                    AND     vl.tipo_valor   = ' || quote_literal('D') || '
                    AND     la.cod_lote     = lo.cod_lote
                    AND     la.exercicio    = lo.exercicio
                    AND     la.tipo         = lo.tipo
                    AND     la.cod_entidade = lo.cod_entidade
                    AND     pa.exercicio IN (' || quote_literal(stExercicio) || ', ' || quote_literal(stExercicioAnterior) || ')
                    AND     sc.cod_sistema  = pc.cod_sistema
                    AND     sc.exercicio    = pc.exercicio
                    ORDER BY pc.cod_estrutural
                  ) as tabela
                 WHERE
                        cod_entidade IN (' || stEntidade || ')';
    EXECUTE stSql;

    stSql := 'CREATE TEMPORARY TABLE tmp_credito AS
                SELECT *
                FROM (
                    SELECT
                         pc.cod_estrutural
                        ,pa.cod_plano
                        ,vl.tipo_valor
                        ,vl.vl_lancamento
                        ,vl.cod_entidade
                        ,lo.cod_lote
                        ,lo.dt_lote
                        ,lo.exercicio
                        ,lo.tipo
                        ,vl.sequencia
                        ,vl.oid as oid_temp
                        ,sc.cod_sistema
                        ,pc.escrituracao
                        ,pc.indicador_superavit
                    FROM
                         contabilidade.plano_conta       as pc
                        ,contabilidade.plano_analitica   as pa
                        ,contabilidade.conta_credito     as cc
                        ,contabilidade.valor_lancamento  as vl
                        ,contabilidade.lancamento        as la
                        ,contabilidade.lote              as lo
                        ,contabilidade.sistema_contabil  as sc
                    WHERE   pc.cod_conta    = pa.cod_conta
                    AND     pc.exercicio    = pa.exercicio
                    AND     pa.cod_plano    = cc.cod_plano
                    AND     pa.exercicio    = cc.exercicio
                    AND     cc.cod_lote     = vl.cod_lote
                    AND     cc.tipo         = vl.tipo
                    AND     cc.sequencia    = vl.sequencia
                    AND     cc.exercicio    = vl.exercicio
                    AND     cc.tipo_valor   = vl.tipo_valor
                    AND     cc.cod_entidade = vl.cod_entidade
                    AND     vl.cod_lote     = la.cod_lote
                    AND     vl.tipo         = la.tipo
                    AND     vl.sequencia    = la.sequencia
                    AND     vl.exercicio    = la.exercicio
                    AND     vl.cod_entidade = la.cod_entidade
                    AND     vl.tipo_valor   = '|| quote_literal('C') ||'
                    AND     la.cod_lote     = lo.cod_lote
                    AND     la.exercicio    = lo.exercicio
                    AND     la.tipo         = lo.tipo
                    AND     la.cod_entidade = lo.cod_entidade
                    AND     pa.exercicio IN (' || quote_literal(stExercicio) || ', ' || quote_literal(stExercicioAnterior) || ')
                    AND     sc.cod_sistema  = pc.cod_sistema
                    AND     sc.exercicio    = pc.exercicio

                    ORDER BY pc.cod_estrutural
                  ) as tabela
                 WHERE
                        cod_entidade IN (' || stEntidade || ')';
    EXECUTE stSql;

    CREATE UNIQUE INDEX unq_debito              ON tmp_debito           (cod_estrutural varchar_pattern_ops, oid_temp);
    CREATE UNIQUE INDEX unq_credito             ON tmp_credito          (cod_estrutural varchar_pattern_ops, oid_temp);


    CREATE TEMPORARY TABLE tmp_totaliza_debito AS
        SELECT *
        FROM  tmp_debito
        WHERE tipo <> 'I';

    CREATE TEMPORARY TABLE tmp_totaliza_credito AS
        SELECT *
        FROM  tmp_credito
        WHERE tipo <> 'I';

    CREATE UNIQUE INDEX unq_totaliza_credito    ON tmp_totaliza_credito (cod_estrutural varchar_pattern_ops, oid_temp);
    CREATE UNIQUE INDEX unq_totaliza_debito     ON tmp_totaliza_debito  (cod_estrutural varchar_pattern_ops, oid_temp);

    IF substr(stDtInicial,1,5) =  '01/01' THEN
        stSqlComplemento := ' dt_lote = to_date( ' || quote_literal(stDtInicial) || '::varchar,' || quote_literal('dd/mm/yyyy') || ') ';
        stSqlComplemento := stSqlComplemento || ' AND tipo = ''I'' ';
    ELSE
        stSqlComplemento := 'dt_lote BETWEEN to_date( '|| quote_literal('01/01/')  || 'substr(to_char(to_date(' || stDtInicial || '::varchar,' || quote_literal('dd/mm/yyyy') || ') - 1,'|| quote_literal('dd/mm/yyyy') || '),7),'|| quote_literal('dd/mm/yyyy') || ') AND to_date( ' || quote_literal(stDtInicial) || '::varchar,' || quote_literal('dd/mm/yyyy') || ')-1';
    END IF;
    
    stSql := 'CREATE TEMPORARY TABLE tmp_totaliza AS
        SELECT * FROM tmp_debito
        WHERE
             ' || stSqlComplemento || '
       UNION
        SELECT * FROM tmp_credito
        WHERE
             ' || stSqlComplemento || '
    ';
    EXECUTE stSql;

    CREATE UNIQUE INDEX unq_totaliza            ON tmp_totaliza         (cod_estrutural varchar_pattern_ops, oid_temp);
    
        stSqlComplemento := ' dt_lote = to_date( ' || quote_literal(stDtInicial) || '::varchar,' || quote_literal('dd/mm/yyyy') || ') ';
        stSqlComplemento := stSqlComplemento || ' AND tipo = ''I'' ';
    
    stSql := 'CREATE TEMPORARY TABLE tmp_totaliza_anterior AS
    
                    SELECT * FROM tmp_debito                                                                                                                                                 
                    WHERE dt_lote = to_date( ''01/01/'|| stExercicioAnterior || ''',' || quote_literal('dd/mm/yyyy') || ') AND tipo = '||quote_literal('I')||'
                    
                    UNION SELECT * FROM tmp_credito                                                              
                    WHERE dt_lote = to_date( ''01/01/'|| stExercicioAnterior || ''',' || quote_literal('dd/mm/yyyy') || ') AND tipo = '||quote_literal('I')|| '';
                    
    EXECUTE stSql;
    
    CREATE UNIQUE INDEX unq_totaliza_anterior       ON tmp_totaliza_anterior    (cod_estrutural varchar_pattern_ops, oid_temp);
        
        stSql := 'SELECT
                            plano_conta.cod_estrutural
                            , 0.00 AS saldo_anterior
                            , 0.00 AS saldo_atualEx
                            , 0.00 AS saldo_inicial
                            , 0.00 AS saldo_atual
                            , 0.00 AS movimentacao_anterior
                            , 0.00 AS movimentacao_atual
                    FROM
                         contabilidade.plano_conta
                         
               LEFT JOIN contabilidade.plano_analitica
                      ON plano_analitica.cod_conta = plano_conta.cod_conta
                     AND plano_analitica.exercicio = plano_conta.exercicio
                     
                    JOIN contabilidade.sistema_contabil
                      ON sistema_contabil.cod_sistema = plano_conta.cod_sistema
                     AND sistema_contabil.exercicio = plano_conta.exercicio
                     
                   WHERE plano_conta.exercicio IN (' || quote_literal(stExercicio) || ')
                     AND plano_conta.cod_estrutural LIKE (''2.3%'')
                     
                    ORDER BY cod_estrutural ';
                    
    CREATE TEMPORARY TABLE tmp_mutacoes (
        grupo       INTEGER     NOT NULL,
        descricao   VARCHAR,
        cod_231     NUMERIC,
        cod_232     NUMERIC,
        cod_233     NUMERIC,
        cod_234     NUMERIC,
        cod_235     NUMERIC,
        cod_236     NUMERIC,
        cod_237     NUMERIC,
        cod_239     NUMERIC
        );
        
    INSERT INTO tmp_mutacoes (grupo, descricao, cod_231, cod_232, cod_233, cod_234, cod_235, cod_236, cod_237, cod_239) VALUES (1, 'Saldo Inicial Exercício Anterior', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00);
    
    INSERT INTO tmp_mutacoes (grupo, descricao, cod_231, cod_232, cod_233, cod_234, cod_235, cod_236, cod_237, cod_239) VALUES (2, 'Ajustes de Exercícios Anteriores', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00);
    
    INSERT INTO tmp_mutacoes (grupo, descricao, cod_231, cod_232, cod_233, cod_234, cod_235, cod_236, cod_237, cod_239) VALUES (3, 'Aumento de Capital', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00);
    
    INSERT INTO tmp_mutacoes (grupo, descricao, cod_231, cod_232, cod_233, cod_234, cod_235, cod_236, cod_237, cod_239) VALUES (4, 'Resultado do Exercício', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00);
    
    INSERT INTO tmp_mutacoes (grupo, descricao, cod_231, cod_232, cod_233, cod_234, cod_235, cod_236, cod_237, cod_239) VALUES (5, 'Constituição/Reversão de Reservas', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00);
    
    INSERT INTO tmp_mutacoes (grupo, descricao, cod_231, cod_232, cod_233, cod_234, cod_235, cod_236, cod_237, cod_239) VALUES (6, 'Dividendos', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00);
    
    INSERT INTO tmp_mutacoes (grupo, descricao, cod_231, cod_232, cod_233, cod_234, cod_235, cod_236, cod_237, cod_239) VALUES (7, 'Saldo Final Ex. Anterior', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00);
    
    INSERT INTO tmp_mutacoes (grupo, descricao, cod_231, cod_232, cod_233, cod_234, cod_235, cod_236, cod_237, cod_239) VALUES (8, 'Ajustes de Exercícios Anteriores', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00);
    
    INSERT INTO tmp_mutacoes (grupo, descricao, cod_231, cod_232, cod_233, cod_234, cod_235, cod_236, cod_237, cod_239) VALUES (9, 'Aumento de Capital', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00);
    
    INSERT INTO tmp_mutacoes (grupo, descricao, cod_231, cod_232, cod_233, cod_234, cod_235, cod_236, cod_237, cod_239) VALUES (10, 'Resultado do Exercício', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00);
    
    INSERT INTO tmp_mutacoes (grupo, descricao, cod_231, cod_232, cod_233, cod_234, cod_235, cod_236, cod_237, cod_239) VALUES (11, 'Constituição/Reversão de Reservas', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00);
    
    INSERT INTO tmp_mutacoes (grupo, descricao, cod_231, cod_232, cod_233, cod_234, cod_235, cod_236, cod_237, cod_239) VALUES (12, 'Dividendos', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00);
    
    FOR reRegistro IN EXECUTE stSql
    LOOP
        arRetorno := contabilidade.fn_totaliza_mutacao_patrimonio_liquido(publico.fn_mascarareduzida(reRegistro.cod_estrutural) , stDtInicial, stDtFinal, stExercicioAnterior);
        reRegistro.saldo_anterior        := coalesce(arRetorno[1],0.00); -- nuSaldoAnteriorEx
        reRegistro.saldo_atualEx         := coalesce(arRetorno[4],0.00); -- nuSaldoAtualEx
        reRegistro.saldo_inicial         := coalesce(arRetorno[5],0.00); -- nuSaldoAnterior
        reRegistro.saldo_atual           := coalesce(arRetorno[8],0.00); -- nuSaldoAtual
        reRegistro.movimentacao_anterior := coalesce(arRetorno[3],0.00) - coalesce(arRetorno[2],0.00); -- nuCreditoAnt - nuDebitoAnt
        reRegistro.movimentacao_atual    := coalesce(arRetorno[7],0.00) - coalesce(arRetorno[6],0.00); -- nuCredito - nuDebito
        
        IF reRegistro.saldo_anterior < 0 THEN reRegistro.saldo_anterior := reRegistro.saldo_anterior * -1; END IF;
        
        IF reRegistro.saldo_inicial < 0 THEN reRegistro.saldo_inicial := reRegistro.saldo_inicial * -1; END IF;
        
        IF ( reRegistro.saldo_inicial <> 0.00 ) OR
           ( arRetorno[6] <> 0.00 ) OR
           ( arRetorno[7] <> 0.00 ) OR
           ( reRegistro.saldo_atual <> 0.00)
        THEN
            IF (reRegistro.cod_estrutural LIKE '2.3.1.0.0.00%') THEN
                INSERT INTO tmp_mutacoes (grupo, descricao, cod_231) VALUES (1, 'Saldo Inicial Exercício Anterior', reRegistro.saldo_anterior);
                INSERT INTO tmp_mutacoes (grupo, descricao, cod_231) VALUES (3, 'Aumento de Capital', reRegistro.movimentacao_anterior);
                
                IF (stExercicio < '2014') THEN
                    INSERT INTO tmp_mutacoes (grupo, descricao, cod_231) VALUES (7, 'Saldo Final Ex. Anterior', reRegistro.saldo_inicial);
                ELSE
                    INSERT INTO tmp_mutacoes (grupo, descricao, cod_231) VALUES (7, 'Saldo Final Ex. Anterior', (reRegistro.saldo_anterior + reRegistro.movimentacao_anterior));
                END IF;
                
                INSERT INTO tmp_mutacoes (grupo, descricao, cod_231) VALUES (9, 'Aumento de Capital', reRegistro.movimentacao_atual);
                
            ELSE
                IF (reRegistro.cod_estrutural LIKE '2.3.2.0.0.00%') THEN
                    INSERT INTO tmp_mutacoes (grupo, descricao, cod_232) VALUES (1, 'Saldo Inicial Exercício Anterior', reRegistro.saldo_anterior);
                    INSERT INTO tmp_mutacoes (grupo, descricao, cod_232) VALUES (5, 'Constituição/Reversão de Reservas', reRegistro.movimentacao_anterior);
                        
                    IF (stExercicio < '2014') THEN
                        INSERT INTO tmp_mutacoes (grupo, descricao, cod_232) VALUES (7, 'Saldo Final Ex. Anterior', reRegistro.saldo_inicial);
                    ELSE
                        INSERT INTO tmp_mutacoes (grupo, descricao, cod_232) VALUES (7, 'Saldo Final Ex. Anterior', (reRegistro.saldo_anterior + reRegistro.movimentacao_anterior));
                    END IF;
                        
                    INSERT INTO tmp_mutacoes (grupo, descricao, cod_232) VALUES (11, 'Constituição/Reversão de Reservas', reRegistro.movimentacao_atual);
                    
                ELSE
                    IF (reRegistro.cod_estrutural LIKE '2.3.3.0.0.00%') THEN
                        INSERT INTO tmp_mutacoes (grupo, descricao, cod_233) VALUES (1, 'Saldo Inicial Exercício Anterior', reRegistro.saldo_anterior);
                        INSERT INTO tmp_mutacoes (grupo, descricao, cod_233) VALUES (5, 'Constituição/Reversão de Reservas', reRegistro.movimentacao_anterior);
                                
                        IF (stExercicio < '2014') THEN
                            INSERT INTO tmp_mutacoes (grupo, descricao, cod_233) VALUES (7, 'Saldo Final Ex. Anterior', reRegistro.saldo_inicial);
                        ELSE
                            INSERT INTO tmp_mutacoes (grupo, descricao, cod_233) VALUES (7, 'Saldo Final Ex. Anterior', (reRegistro.saldo_anterior + reRegistro.movimentacao_anterior));
                        END IF;
                                
                        INSERT INTO tmp_mutacoes (grupo, descricao, cod_233) VALUES (11, 'Constituição/Reversão de Reservas', reRegistro.movimentacao_atual);
                        
                    ELSE
                        IF (reRegistro.cod_estrutural LIKE '2.3.4.0.0.00%') THEN
                            INSERT INTO tmp_mutacoes (grupo, descricao, cod_234) VALUES (1, 'Saldo Inicial Exercício Anterior', reRegistro.saldo_anterior);
                                    
                            IF (stExercicio < '2014') THEN
                                INSERT INTO tmp_mutacoes (grupo, descricao, cod_234) VALUES (7, 'Saldo Final Ex. Anterior', reRegistro.saldo_inicial);
                            ELSE
                                INSERT INTO tmp_mutacoes (grupo, descricao, cod_234) VALUES (7, 'Saldo Final Ex. Anterior', (reRegistro.saldo_anterior + reRegistro.movimentacao_anterior));
                            END IF;
                            
                        ELSE
                            IF (reRegistro.cod_estrutural LIKE '2.3.5.0.0.00%') THEN
                                INSERT INTO tmp_mutacoes (grupo, descricao, cod_235) VALUES (1, 'Saldo Inicial Exercício Anterior', reRegistro.saldo_anterior);
                                INSERT INTO tmp_mutacoes (grupo, descricao, cod_235) VALUES (5, 'Constituição/Reversão de Reservas', reRegistro.movimentacao_anterior);
                                        
                                IF (stExercicio < '2014') THEN
                                    INSERT INTO tmp_mutacoes (grupo, descricao, cod_235) VALUES (7, 'Saldo Final Ex. Anterior', reRegistro.saldo_inicial);
                                    ELSE
                                        INSERT INTO tmp_mutacoes (grupo, descricao, cod_235) VALUES (7, 'Saldo Final Ex. Anterior', (reRegistro.saldo_anterior + reRegistro.movimentacao_anterior));
                                    END IF;
                                        
                                INSERT INTO tmp_mutacoes (grupo, descricao, cod_235) VALUES (11, 'Constituição/Reversão de Reservas', reRegistro.movimentacao_atual);
                                
                            ELSE
                                IF (reRegistro.cod_estrutural LIKE '2.3.6.0.0.00%') THEN
                                    INSERT INTO tmp_mutacoes (grupo, descricao, cod_236) VALUES (1, 'Saldo Inicial Exercício Anterior', reRegistro.saldo_anterior);
                                    INSERT INTO tmp_mutacoes (grupo, descricao, cod_236) VALUES (5, 'Constituição/Reversão de Reservas', reRegistro.movimentacao_anterior);
                                            
                                    IF (stExercicio < '2014') THEN
                                        INSERT INTO tmp_mutacoes (grupo, descricao, cod_236) VALUES (7, 'Saldo Final Ex. Anterior', reRegistro.saldo_inicial);
                                    ELSE
                                        INSERT INTO tmp_mutacoes (grupo, descricao, cod_236) VALUES (7, 'Saldo Final Ex. Anterior', (reRegistro.saldo_anterior + reRegistro.movimentacao_anterior));
                                    END IF;
                                            
                                    INSERT INTO tmp_mutacoes (grupo, descricao, cod_236) VALUES (11, 'Constituição/Reversão de Reservas', reRegistro.movimentacao_atual);
                                    
                                ELSE
                                    IF (reRegistro.cod_estrutural LIKE '2.3.7.0.0.00%') THEN
                                        INSERT INTO tmp_mutacoes (grupo, descricao, cod_237) VALUES (1, 'Saldo Inicial Exercício Anterior', reRegistro.saldo_anterior);
                                                
                                        IF (reRegistro.cod_estrutural LIKE '2.3.7.1.1.03%' OR                                                                                                                                                                                                   
                                            reRegistro.cod_estrutural LIKE '2.3.7.1.2.03%' OR                                                                                                                                                                                                   
                                            reRegistro.cod_estrutural LIKE '2.3.7.1.3.03%' OR                                                                                                                                                                                                   
                                            reRegistro.cod_estrutural LIKE '2.3.7.1.4.03%' OR                                                                                                                                                                                                   
                                            reRegistro.cod_estrutural LIKE '2.3.7.1.5.03%' OR                                                                                                                                                                                                   
                                            reRegistro.cod_estrutural LIKE '2.3.7.2.1.03%' OR                                                                                                                                                                                                   
                                            reRegistro.cod_estrutural LIKE '2.3.7.2.2.03%' OR                                                                                                                                                                                                   
                                            reRegistro.cod_estrutural LIKE '2.3.7.2.3.03%' OR
                                            reRegistro.cod_estrutural LIKE '2.3.7.2.4.03%' OR
                                            reRegistro.cod_estrutural LIKE '2.3.7.2.5.03%'                                                                                                                                                                                                      
                                        ) THEN                                                                                                                                                                                                                                                  
                                            INSERT INTO tmp_mutacoes (grupo, descricao, cod_237) VALUES (2, 'Ajustes de Exercícios Anteriores', reRegistro.movimentacao_anterior);                                                                                                              
                                            INSERT INTO tmp_mutacoes (grupo, descricao, cod_237) VALUES (4, 'Resultado do Exercício', reRegistro.movimentacao_anterior);                                                                                                                                                         
                                        END IF;
                                        
                                        IF (stExercicio < '2014') THEN
                                                INSERT INTO tmp_mutacoes (grupo, descricao, cod_237) VALUES (7, 'Saldo Final Ex. Anterior', reRegistro.saldo_inicial);                                                                                                                                                                   
                                        ELSE
                                            INSERT INTO tmp_mutacoes (grupo, descricao, cod_237) VALUES (7, 'Saldo Final Ex. Anterior', (reRegistro.saldo_anterior + reRegistro.movimentacao_anterior));
                                        END IF;
                                                
                                        INSERT INTO tmp_mutacoes (grupo, descricao, cod_237) VALUES (8, 'Ajustes de Exercícios Anteriores', reRegistro.movimentacao_atual);                                                                                                                                                 
                                        INSERT INTO tmp_mutacoes (grupo, descricao, cod_237) VALUES (10, 'Resultado do Exercício', reRegistro.movimentacao_atual);
                                        
                                    ELSE                                                                                                                                                                                                                                                                                    
                                        IF (reRegistro.cod_estrutural LIKE '2.3.9.0.0.00%') THEN                                                                                                                                                                                                                                   
                                            INSERT INTO tmp_mutacoes (grupo, descricao, cod_239) VALUES (1, 'Saldo Inicial Exercício Anterior', reRegistro.saldo_anterior);
                                                    
                                            IF (stExercicio < '2014') THEN
                                                INSERT INTO tmp_mutacoes (grupo, descricao, cod_239) VALUES (7, 'Saldo Final Ex. Anterior', reRegistro.saldo_inicial);
                                            ELSE
                                                INSERT INTO tmp_mutacoes (grupo, descricao, cod_237) VALUES (7, 'Saldo Final Ex. Anterior', (reRegistro.saldo_anterior + reRegistro.movimentacao_anterior));
                                            END IF;
                                        END IF;
                                    END IF;
                                END IF;
                            END IF;
                        END IF;
                    END IF;
                END IF;
            END IF;
        END IF;
    END LOOP;
    
    stSql := 'SELECT                                  
                grupo,                                 
                descricao,
                COALESCE(SUM(cod_231),0.00) AS cod_231,
                COALESCE(SUM(cod_232),0.00) AS cod_232,
                COALESCE(SUM(cod_233),0.00) AS cod_233,
                COALESCE(SUM(cod_234),0.00) AS cod_234,
                COALESCE(SUM(cod_235),0.00) AS cod_235,
                COALESCE(SUM(cod_236),0.00) AS cod_236,
                COALESCE(SUM(cod_237),0.00) AS cod_237,
                COALESCE(SUM(cod_239),0.00) AS cod_239
                
            FROM tmp_mutacoes                      
            GROUP BY grupo, descricao                  
            ORDER BY grupo';
            
    FOR reRegistro2 IN EXECUTE stSql              
    LOOP                                          
        RETURN NEXT reRegistro2;                  
    END LOOP;

    DROP INDEX unq_totaliza;
    DROP INDEX unq_totaliza_anterior;
    DROP INDEX unq_totaliza_debito;
    DROP INDEX unq_totaliza_credito;
    DROP INDEX unq_debito;
    DROP INDEX unq_credito;
    
    DROP TABLE tmp_totaliza;
    DROP TABLE tmp_totaliza_anterior;
    DROP TABLE tmp_debito;
    DROP TABLE tmp_credito;
    DROP TABLE tmp_totaliza_debito;
    DROP TABLE tmp_totaliza_credito;
    DROP TABLE tmp_mutacoes;

    RETURN;
END;
$$ LANGUAGE 'plpgsql';
