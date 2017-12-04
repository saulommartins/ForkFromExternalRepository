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
* $Id: GF_1940.sql 59612 2014-09-02 12:00:51Z gelson $
*
* Versão 1.94.0
*/

----------------
-- Ticket #14292
----------------

INSERT INTO administracao.relatorio
         ( cod_gestao
         , cod_modulo
         , cod_relatorio
         , nom_relatorio
         , arquivo )
    VALUES ( 2
         , 9
         , 5
         , 'Relatório de Insuficiência'
         , 'relatorioInsuficienciaDestinacaoRecurso.rptdesign'
         );


----------------
-- Ticket #15122
----------------

INSERT INTO administracao.relatorio
          ( cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo )
     VALUES ( 2
          , 9
          , 6
          , 'Razão do Credor'
          , 'razaoCredor.rptdesign'
          );


----------------
-- Ticket #15123
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2712
          , 274
          , 'FLRelacaoDespesaOrcamentaria.php'
          , 'consultar'
          , 11
          , ''
          , 'Relação de Despesa Orçamentária'
          );

INSERT INTO administracao.relatorio
          ( cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo )
     VALUES ( 2
          , 30
          , 5
          , 'Relação de Despesa Orçamentária'
          , 'relacaoDespesaOrcamentaria.rptdesign'
          );


----------------
-- Ticket #15197
----------------

INSERT INTO administracao.relatorio
          ( cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo )
     VALUES ( 2
          , 10
          , 9
          , 'Nota de Autorização de Empenho'
          , 'notaAutorizacaoEmpenho.rptdesign'
          );


----------------
-- Ticket #15261
----------------

CREATE OR REPLACE FUNCTION atualiza_lancamento_retencoes_restos() RETURNS INTEGER AS $$
DECLARE
    stSql       VARCHAR;
    stSqlUpdate VARCHAR;
    inSequencia INTEGER;
    reRegistro  RECORD;
BEGIN

    stSql := '
    SELECT pagamento.exercicio
         , pagamento.sequencia
         , pagamento.tipo
         , pagamento.cod_lote
         , pagamento.cod_entidade
         , pagamento.cod_nota
         , pagamento.timestamp
         , pagamento.exercicio_liquidacao
         , conta_debito.tipo_valor
      FROM contabilidade.pagamento 
      JOIN empenho.nota_liquidacao_paga
        ON nota_liquidacao_paga.exercicio    = pagamento.exercicio_liquidacao
       AND nota_liquidacao_paga.cod_entidade = pagamento.cod_entidade
       AND nota_liquidacao_paga.cod_nota     = pagamento.cod_nota
       AND nota_liquidacao_paga."timestamp"  = pagamento."timestamp"
      JOIN empenho.nota_liquidacao
        ON nota_liquidacao.exercicio    = nota_liquidacao_paga.exercicio
       AND nota_liquidacao.cod_entidade = nota_liquidacao_paga.cod_entidade
       AND nota_liquidacao.cod_nota     = nota_liquidacao_paga.cod_nota
      JOIN empenho.empenho
        ON empenho.exercicio    = nota_liquidacao.exercicio_empenho
       AND empenho.cod_entidade = nota_liquidacao.cod_entidade
       AND empenho.cod_empenho  = nota_liquidacao.cod_empenho
      JOIN contabilidade.conta_debito
        ON conta_debito.exercicio = pagamento.exercicio
       AND conta_debito.cod_entidade = pagamento.cod_entidade
       AND conta_debito.tipo = pagamento.tipo
       AND conta_debito.cod_lote = pagamento.cod_lote
       AND conta_debito.sequencia = pagamento.sequencia
       AND conta_debito.tipo_valor = ''D''
      JOIN contabilidade.plano_analitica 
        ON plano_analitica.exercicio = conta_debito.exercicio 
       AND plano_analitica.cod_plano = conta_debito.cod_plano 
      JOIN contabilidade.plano_conta 
        ON plano_conta.exercicio = plano_analitica.exercicio 
       AND plano_conta.cod_conta = plano_analitica.cod_conta
     WHERE empenho.exercicio < 2009
       AND pagamento.exercicio = 2009
       AND plano_conta.cod_estrutural NOT LIKE ''2.1.2.1.1%''
       AND EXISTS ( SELECT 1 
                      FROM empenho.pagamento_liquidacao
                     WHERE pagamento_liquidacao.cod_nota = pagamento.cod_nota 
                       AND pagamento_liquidacao.exercicio = pagamento.exercicio);
    ';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        SELECT conta_debito.sequencia
          INTO inSequencia
          FROM contabilidade.conta_debito 
          JOIN contabilidade.plano_analitica 
            ON conta_debito.exercicio = plano_analitica.exercicio 
           AND conta_debito.cod_plano = plano_analitica.cod_plano 
          JOIN contabilidade.plano_conta 
            ON plano_conta.exercicio = plano_analitica.exercicio 
           AND plano_conta.cod_conta = plano_analitica.cod_conta 
         WHERE plano_conta.cod_estrutural LIKE '2.1.2.1.1%'
           AND conta_debito.cod_lote = reRegistro.cod_lote
           AND conta_debito.tipo = reRegistro.tipo
           AND conta_debito.exercicio = reRegistro.exercicio
           AND conta_debito.tipo_valor = reRegistro.tipo_valor
           AND conta_debito.cod_entidade = reRegistro.cod_entidade;
       
        DELETE FROM contabilidade.pagamento
         WHERE cod_lote = reRegistro.cod_lote
           AND sequencia = reRegistro.sequencia
           AND tipo = reRegistro.tipo
           AND cod_nota = reRegistro.cod_nota
           AND exercicio = reRegistro.exercicio
           AND cod_entidade = reRegistro.cod_entidade
           AND timestamp = reRegistro.timestamp
           AND exercicio_liquidacao = reRegistro.exercicio_liquidacao;

        UPDATE contabilidade.lancamento_empenho
           SET sequencia = inSequencia
         WHERE cod_lote = reRegistro.cod_lote
           AND tipo = reRegistro.tipo
           AND exercicio = reRegistro.exercicio
           AND cod_entidade = reRegistro.cod_entidade ;

        INSERT INTO contabilidade.pagamento
             ( sequencia, cod_lote, tipo, cod_nota, exercicio, cod_entidade, timestamp, exercicio_liquidacao)
        VALUES ( inSequencia
               , reRegistro.cod_lote
               , reRegistro.tipo
               , reRegistro.cod_nota
               , reRegistro.exercicio
               , reRegistro.cod_entidade
               , reRegistro.timestamp
               , reRegistro.exercicio_liquidacao );

    END LOOP;

    RETURN 1;

END;
$$ LANGUAGE 'plpgsql';

SELECT * FROM atualiza_lancamento_retencoes_restos() ;
DROP FUNCTION atualiza_lancamento_retencoes_restos();

