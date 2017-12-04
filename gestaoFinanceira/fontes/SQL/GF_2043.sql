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
*
* Script de DDL e DML
*
* Versao 2.04.3
*
* Fabio Bertoldi - 20150925
*
*/

----------------
-- Ticket #23280
----------------

CREATE OR REPLACE FUNCTION limpa_nota_liquidacao() RETURNS VOID AS $$
DECLARE
    stSQL       VARCHAR;
    reRecord    RECORD;
    stSQL2      VARCHAR;
    reRecord2   RECORD;
BEGIN

    stSQL := '
                  SELECT nota_liquidacao.*
                    FROM empenho.nota_liquidacao
               LEFT JOIN empenho.nota_liquidacao_item
                      ON nota_liquidacao_item.exercicio       = nota_liquidacao.exercicio
                     AND nota_liquidacao_item.cod_entidade    = nota_liquidacao.cod_entidade
                     AND nota_liquidacao_item.cod_nota        = nota_liquidacao.cod_nota
               LEFT JOIN empenho.pagamento_liquidacao
                      ON pagamento_liquidacao.exercicio_liquidacao    = nota_liquidacao.exercicio
                     AND pagamento_liquidacao.cod_entidade        = nota_liquidacao.cod_entidade
                     AND pagamento_liquidacao.cod_nota            = nota_liquidacao.cod_nota
                   WHERE nota_liquidacao_item.cod_nota IS NULL
                     AND pagamento_liquidacao.cod_nota IS NULL
                ORDER BY nota_liquidacao.exercicio_empenho
                       , nota_liquidacao.cod_empenho
                       , nota_liquidacao.cod_nota
                       ;
             ';
    FOR reRecord IN EXECUTE stSQL LOOP
        stSQL2 := '
                    SELECT liquidacao.exercicio
                         , liquidacao.sequencia
                         , liquidacao.tipo
                         , liquidacao.cod_lote
                         , liquidacao.cod_entidade
                      FROM contabilidade.liquidacao
                     WHERE liquidacao.exercicio    = '|| quote_literal(reRecord.exercicio) ||'
                       AND liquidacao.cod_entidade = '|| reRecord.cod_entidade             ||'
                       AND liquidacao.cod_nota     = '|| reRecord.cod_nota                 ||'
                         ;
                  ';
        FOR reRecord2 IN EXECUTE stSQL2 LOOP
            DELETE
              FROM contabilidade.liquidacao
             WHERE liquidacao.exercicio    = reRecord.exercicio
               AND liquidacao.cod_entidade = reRecord.cod_entidade
               AND liquidacao.cod_nota     = reRecord.cod_nota
                 ;
            DELETE
              FROM contabilidade.lancamento_empenho
             WHERE lancamento_empenho.exercicio    = reRecord2.exercicio
--                AND lancamento_empenho.sequencia    = reRecord2.sequencia
               AND lancamento_empenho.tipo         = reRecord2.tipo
               AND lancamento_empenho.cod_lote     = reRecord2.cod_lote
               AND lancamento_empenho.cod_entidade = reRecord2.cod_entidade
                 ;
            DELETE
              FROM contabilidade.conta_credito
             WHERE conta_credito.exercicio    = reRecord2.exercicio
--                AND conta_credito.sequencia    = reRecord2.sequencia
               AND conta_credito.tipo         = reRecord2.tipo
               AND conta_credito.cod_lote     = reRecord2.cod_lote
               AND conta_credito.cod_entidade = reRecord2.cod_entidade
                 ;
            DELETE
              FROM contabilidade.conta_debito
             WHERE conta_debito.exercicio    = reRecord2.exercicio
--                AND conta_debito.sequencia    = reRecord2.sequencia
               AND conta_debito.tipo         = reRecord2.tipo
               AND conta_debito.cod_lote     = reRecord2.cod_lote
               AND conta_debito.cod_entidade = reRecord2.cod_entidade
                 ;
            DELETE
              FROM contabilidade.valor_lancamento
             WHERE valor_lancamento.exercicio    = reRecord2.exercicio
--                AND valor_lancamento.sequencia    = reRecord2.sequencia
               AND valor_lancamento.tipo         = reRecord2.tipo
               AND valor_lancamento.cod_lote     = reRecord2.cod_lote
               AND valor_lancamento.cod_entidade = reRecord2.cod_entidade
                 ;
            DELETE
              FROM contabilidade.lancamento
             WHERE lancamento.exercicio    = reRecord2.exercicio
--                AND lancamento.sequencia    = reRecord2.sequencia
               AND lancamento.tipo         = reRecord2.tipo
               AND lancamento.cod_lote     = reRecord2.cod_lote
               AND lancamento.cod_entidade = reRecord2.cod_entidade
                 ;
            DELETE
              FROM contabilidade.lote
             WHERE lote.exercicio    = reRecord2.exercicio
               AND lote.tipo         = reRecord2.tipo
               AND lote.cod_lote     = reRecord2.cod_lote
               AND lote.cod_entidade = reRecord2.cod_entidade
                 ;
        END LOOP;
        DELETE
          FROM empenho.atributo_liquidacao_valor
         WHERE atributo_liquidacao_valor.exercicio    = reRecord.exercicio
           AND atributo_liquidacao_valor.cod_entidade = reRecord.cod_entidade
           AND atributo_liquidacao_valor.cod_nota     = reRecord.cod_nota
             ;
        DELETE
          FROM empenho.nota_liquidacao_assinatura
         WHERE nota_liquidacao_assinatura.exercicio    = reRecord.exercicio
           AND nota_liquidacao_assinatura.cod_entidade = reRecord.cod_entidade
           AND nota_liquidacao_assinatura.cod_nota     = reRecord.cod_nota
             ;

        DELETE
          FROM tceal.documento
         WHERE documento.exercicio    = reRecord.exercicio
           AND documento.cod_entidade = reRecord.cod_entidade
           AND documento.cod_nota     = reRecord.cod_nota
             ;
        DELETE
          FROM tcepe.documento
         WHERE documento.exercicio    = reRecord.exercicio
           AND documento.cod_entidade = reRecord.cod_entidade
           AND documento.cod_nota     = reRecord.cod_nota
             ;
        DELETE
          FROM tceam.documento
         WHERE documento.exercicio    = reRecord.exercicio
           AND documento.cod_entidade = reRecord.cod_entidade
           AND documento.cod_nota     = reRecord.cod_nota
             ;

        DELETE
          FROM empenho.nota_liquidacao
         WHERE nota_liquidacao.exercicio    = reRecord.exercicio
           AND nota_liquidacao.cod_entidade = reRecord.cod_entidade
           AND nota_liquidacao.cod_nota     = reRecord.cod_nota
             ;
    END LOOP;
END;
$$ LANGUAGE 'plpgsql';

SELECT        limpa_nota_liquidacao();
DROP FUNCTION limpa_nota_liquidacao();

