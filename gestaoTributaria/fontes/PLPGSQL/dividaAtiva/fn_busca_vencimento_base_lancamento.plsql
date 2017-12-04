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
* PL para busca do primeiro vencimento base para calculo de acrescimos do lancamento
* 
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: fn_busca_vencimento_base_lancamento.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.04.02
*/

/*
$Log:
*/


CREATE OR REPLACE FUNCTION arrecadacao.fn_busca_vencimento_base_lancamento( inCodLancamento     INTEGER
                                                                          , inExercicio         VARCHAR
                                                                          ) RETURNS             VARCHAR AS $$
DECLARE
    dtVencimento VARCHAR;
BEGIN

        SELECT COALESCE(reemissao.vencimento, parcela.vencimento) AS vencimento_original
          INTO dtVencimento
          FROM arrecadacao.parcela
    INNER JOIN arrecadacao.carne
            ON carne.cod_parcela = parcela.cod_parcela
     LEFT JOIN (
                   SELECT parcela_reemissao.cod_parcela
                        , parcela_reemissao.vencimento
                        , MIN(parcela_reemissao.timestamp)
                     FROM arrecadacao.parcela_reemissao
                     JOIN arrecadacao.parcela
                       ON parcela.cod_parcela = parcela_reemissao.cod_parcela
                      AND parcela.cod_lancamento = inCodLancamento
                 GROUP BY parcela_reemissao.cod_parcela
                        , parcela_reemissao.vencimento
               ) AS reemissao
            ON reemissao.cod_parcela = parcela.cod_parcela
     LEFT JOIN arrecadacao.carne_devolucao
            ON carne_devolucao.numeracao    = carne.numeracao
           AND carne_devolucao.cod_convenio = carne.cod_convenio
           AND carne_devolucao.cod_motivo  != 10
     LEFT JOIN arrecadacao.pagamento
            ON pagamento.numeracao = carne.numeracao
           AND pagamento.cod_convenio = carne.cod_convenio
         WHERE cod_lancamento  = inCodLancamento
           AND carne.exercicio = inExercicio
           AND carne_devolucao.numeracao is null
           AND pagamento.numeracao is null
      ORDER BY parcela.nr_parcela
         LIMIT 1
             ;

    RETURN dtVencimento;

END;
$$ LANGUAGE 'plpgsql';
