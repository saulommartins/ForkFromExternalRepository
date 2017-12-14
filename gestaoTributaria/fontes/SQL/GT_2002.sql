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
* $Id:$
*
* Versão 2.00.2
*/

----------------
-- Ticket #17827
----------------

CREATE INDEX ix_parcelamento_1 ON divida.parcelamento(numero_parcelamento, exercicio);


-----------------------------------------
-- CARNE IPTU/TFF 2012 - MATA DE SAO JOAO
-----------------------------------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN

    PERFORM 1
       FROM administracao.configuracao
      WHERE exercicio  = '2011'
        AND cod_modulo = 2
        AND parametro  = 'cnpj'
        AND valor      = '13805528000180'
          ;
    IF FOUND THEN
        INSERT INTO arrecadacao.modelo_carne VALUES ((SELECT MAX(cod_modelo) + 1 FROM arrecadacao.modelo_carne),'Carne I.P.T.U. 2012','RCarneIPTUMataSaoJoao2012.class.php',12,FALSE);
        INSERT INTO arrecadacao.acao_modelo_carne VALUES ((SELECT MAX(cod_modelo) FROM arrecadacao.modelo_carne),963);
        INSERT INTO arrecadacao.acao_modelo_carne VALUES ((SELECT MAX(cod_modelo) FROM arrecadacao.modelo_carne),964);
        INSERT INTO arrecadacao.acao_modelo_carne VALUES ((SELECT MAX(cod_modelo) FROM arrecadacao.modelo_carne),978);
        INSERT INTO arrecadacao.acao_modelo_carne VALUES ((SELECT MAX(cod_modelo) FROM arrecadacao.modelo_carne),979);

        INSERT INTO arrecadacao.modelo_carne VALUES ((SELECT MAX(cod_modelo) + 1 FROM arrecadacao.modelo_carne),'Carne T.F.F. 2012','RCarneTFFMataSaoJoao2012.class.php',14,FALSE);
        INSERT INTO arrecadacao.acao_modelo_carne VALUES ((SELECT MAX(cod_modelo) FROM arrecadacao.modelo_carne),963);
        INSERT INTO arrecadacao.acao_modelo_carne VALUES ((SELECT MAX(cod_modelo) FROM arrecadacao.modelo_carne),964);
        INSERT INTO arrecadacao.acao_modelo_carne VALUES ((SELECT MAX(cod_modelo) FROM arrecadacao.modelo_carne),978);
        INSERT INTO arrecadacao.acao_modelo_carne VALUES ((SELECT MAX(cod_modelo) FROM arrecadacao.modelo_carne),979);
        INSERT INTO arrecadacao.acao_modelo_carne VALUES ((SELECT MAX(cod_modelo) FROM arrecadacao.modelo_carne),980);
        INSERT INTO arrecadacao.acao_modelo_carne VALUES ((SELECT MAX(cod_modelo) FROM arrecadacao.modelo_carne),962);
        INSERT INTO arrecadacao.acao_modelo_carne VALUES ((SELECT MAX(cod_modelo) FROM arrecadacao.modelo_carne),1672);
        INSERT INTO arrecadacao.acao_modelo_carne VALUES ((SELECT MAX(cod_modelo) FROM arrecadacao.modelo_carne),1677);
        INSERT INTO arrecadacao.acao_modelo_carne VALUES ((SELECT MAX(cod_modelo) FROM arrecadacao.modelo_carne),1678);
    END IF;

END;
$$ LANGUAGE 'plpgsql';

SELECT manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #16959
----------------

ALTER TABLE divida.parcelamento ADD COLUMN judicial BOOLEAN NOT NULL DEFAULT FALSE;

CREATE OR REPLACE FUNCTION mantem() RETURNS VOID AS $$
DECLARE

BEGIN
    PERFORM 1
       FROM administracao.configuracao
      WHERE exercicio  = '2011'
        AND cod_modulo = 2
        AND parametro  = 'cnpj'
        AND valor      = '13805528000180'
          ;
    IF FOUND THEN
        UPDATE divida.cobranca_judicial SET timestamp = '0001-01-01 00:00:00 BC' WHERE cod_inscricao = 12494 AND exercicio = '2007';
        UPDATE divida.cobranca_judicial SET timestamp = '0001-01-01 00:00:00 BC' WHERE cod_inscricao = 10074 AND exercicio = '2001';
        UPDATE divida.cobranca_judicial SET timestamp = '0001-01-01 00:00:00 BC' WHERE cod_inscricao = 10293 AND exercicio = '2002';
        UPDATE divida.cobranca_judicial SET timestamp = '0001-01-01 00:00:00 BC' WHERE cod_inscricao = 10128 AND exercicio = '2003';
        UPDATE divida.cobranca_judicial SET timestamp = '0001-01-01 00:00:00 BC' WHERE cod_inscricao = 11834 AND exercicio = '2004';
        UPDATE divida.cobranca_judicial SET timestamp = '0001-01-01 00:00:00 BC' WHERE cod_inscricao = 11061 AND exercicio = '2005';
        UPDATE divida.cobranca_judicial SET timestamp = '0001-01-01 00:00:00 BC' WHERE cod_inscricao =  9755 AND exercicio = '2005';
        UPDATE divida.cobranca_judicial SET timestamp = '0001-01-01 00:00:00 BC' WHERE cod_inscricao =  8524 AND exercicio = '2006';
        UPDATE divida.cobranca_judicial SET timestamp = '0001-01-01 00:00:00 BC' WHERE cod_inscricao = 11406 AND exercicio = '2009';
        UPDATE divida.cobranca_judicial SET timestamp = '0001-01-01 00:00:00 BC' WHERE cod_inscricao = 11407 AND exercicio = '2009';
        UPDATE divida.cobranca_judicial SET timestamp = '0001-01-01 00:00:00 BC' WHERE cod_inscricao = 11408 AND exercicio = '2009';
    END IF;
END;
$$ LANGUAGE 'plpgsql';
SELECT        mantem();
DROP FUNCTION mantem();


CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
    stSQL       VARCHAR;
    reRecord    RECORD;
BEGIN
    stSQL := '  SELECT cod_inscricao
                     , exercicio
                  FROM divida.cobranca_judicial
                 WHERE timestamp = \'0001-01-01 00:00:00 BC\'
                     ;
             ';
    FOR reRecord IN EXECUTE stSQL LOOP
        UPDATE divida.cobranca_judicial
           SET timestamp = parcelamento.timestamp
          FROM divida.parcelamento
          JOIN (
                   SELECT cod_inscricao
                        , exercicio
                        , MAX(num_parcelamento) AS num_parcelamento
                     FROM divida.divida_parcelamento
                    WHERE divida_parcelamento.cod_inscricao = reRecord.cod_inscricao
                      AND divida_parcelamento.exercicio     = reRecord.exercicio
                 GROUP BY cod_inscricao
                        , exercicio
               ) AS divida_parcelamento
            ON divida_parcelamento.num_parcelamento = parcelamento.num_parcelamento
         WHERE divida_parcelamento.cod_inscricao = cobranca_judicial.cod_inscricao
           AND divida_parcelamento.cod_inscricao = reRecord.cod_inscricao
           AND divida_parcelamento.exercicio     = cobranca_judicial.exercicio
           AND divida_parcelamento.exercicio     = reRecord.exercicio
             ;

    END LOOP;

    stSQL := '   SELECT cobranca_judicial.cod_inscricao
                      , cobranca_judicial.exercicio
                      , cobranca_judicial.timestamp
                      , divida_parcelamento.num_parcelamento
                   FROM divida.cobranca_judicial
                   JOIN (
                            SELECT cod_inscricao
                                 , exercicio
                                 , MAX(num_parcelamento) AS num_parcelamento
                              FROM divida.divida_parcelamento
                          GROUP BY cod_inscricao
                                 , exercicio
                        ) AS divida_parcelamento
                     ON divida_parcelamento.cod_inscricao = cobranca_judicial.cod_inscricao
                    AND divida_parcelamento.exercicio     = cobranca_judicial.exercicio
               ORDER BY timestamp
                      , exercicio
                      , cod_inscricao
                      , num_parcelamento
                      ;
             ';
    FOR reRecord IN EXECUTE stSQL LOOP
        UPDATE divida.parcelamento
           SET judicial = TRUE
         WHERE parcelamento.num_parcelamento = reRecord.num_parcelamento
           AND parcelamento.timestamp        = reRecord.timestamp
             ;
    END LOOP;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();

