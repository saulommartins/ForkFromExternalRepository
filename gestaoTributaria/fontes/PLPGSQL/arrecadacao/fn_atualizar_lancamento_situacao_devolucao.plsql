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
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: $
*
* Caso de uso: uc-05.03.05
*/

CREATE OR REPLACE FUNCTION fn_atualizar_lancamento_situacao_devolucao()  RETURNS TRIGGER AS $$
DECLARE
    inCodLancamento     integer;
BEGIN
    IF ( new.cod_motivo = 11 ) THEN --inscrito em divida ativa
        SELECT
            parcela.cod_lancamento
        INTO
            inCodLancamento
        FROM
            arrecadacao.parcela
        INNER JOIN
            arrecadacao.carne
        ON
            carne.cod_parcela = parcela.cod_parcela
        WHERE
            carne.numeracao = new.numeracao;

        UPDATE
            arrecadacao.lancamento 
        SET
            situacao = 'D'
        WHERE
            lancamento.cod_lancamento = inCodLancamento;
    ELSIF ( new.cod_motivo = 109 ) THEN --cancelado por recalculo
        SELECT
            parcela.cod_lancamento
        INTO
            inCodLancamento
        FROM
            arrecadacao.parcela
        INNER JOIN
            arrecadacao.carne
        ON
            carne.cod_parcela = parcela.cod_parcela
        WHERE
            carne.numeracao = new.numeracao;

        UPDATE
            arrecadacao.lancamento 
        SET
            situacao = 'C'
        WHERE
            lancamento.cod_lancamento = inCodLancamento;
    ELSIF ( new.cod_motivo = 14 ) THEN --remissao
        SELECT
            parcela.cod_lancamento
        INTO
            inCodLancamento
        FROM
            arrecadacao.parcela
        INNER JOIN
            arrecadacao.carne
        ON
            carne.cod_parcela = parcela.cod_parcela
        WHERE
            carne.numeracao = new.numeracao;

        UPDATE
            arrecadacao.lancamento 
        SET
            situacao = 'R'
        WHERE
            lancamento.cod_lancamento = inCodLancamento;
    END IF;

    Return new;
END;
$$ LANGUAGE 'plpgsql';
