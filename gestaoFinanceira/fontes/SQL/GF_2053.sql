
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
* Versao 2.05.3
*
* Fabio Bertoldi - 20160621
*
*/

----------------
-- Ticket #23735
----------------

UPDATE administracao.acao SET complemento_acao = '', nom_acao = 'Resumo Execução de Restos a Pagar' WHERE cod_acao = 3100;


----------------
-- Ticket #23899
----------------

ALTER TABLE tceal.pagamento_tipo_documento DROP CONSTRAINT pk_pagamento_tipo_documento;
ALTER TABLE tceal.pagamento_tipo_documento ADD  CONSTRAINT pk_pagamento_tipo_documento PRIMARY KEY (exercicio, cod_entidade, cod_nota, timestamp, cod_tipo_documento, num_documento);


----------------
-- Ticket #23912
----------------

ALTER TABLE contabilidade.lancamento_retencao DROP CONSTRAINT pk_retencao;
ALTER TABLE contabilidade.lancamento_retencao ADD CONSTRAINT  pk_retencao
                             PRIMARY KEY(cod_lote, cod_entidade, exercicio, tipo, sequencia, sequencial);


----------------
-- Ticket #23937
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
    reRecord    RECORD;
    stSQL       VARCHAR;
BEGIN
    stSQL := '
                SELECT exercicio
                  FROM contabilidade.historico_contabil
                 WHERE ( select hc.exercicio
                           from contabilidade.historico_contabil as hc
                          where hc.cod_historico in (925,926)
                            and hc.exercicio = historico_contabil.exercicio
                       group by hc.exercicio
                       ) IS NULL
              GROUP BY exercicio order by exercicio;
    ';
    FOR reRecord IN EXECUTE stSQL LOOP
         INSERT
           INTO contabilidade.historico_contabil
         VALUES ( 925
                , reRecord.exercicio
                , 'Vlr. Ref. Arrecadação Receita Dedutora'
                , TRUE
                , TRUE
                );
         INSERT
           INTO contabilidade.historico_contabil
         VALUES ( 926
                , reRecord.exercicio
                , 'Vlr. Ref. Estorno Arrecadação Receita Dedutora'
                , TRUE
                , TRUE
                );
    END LOOP;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #23950
----------------

CREATE TABLE contabilidade.lancamento_empenho_anulado(
    exercicio               CHAR(4)     NOT NULL,
    cod_lote                INTEGER     NOT NULL,
    tipo                    CHAR(1)     NOT NULL,
    sequencia               INTEGER     NOT NULL,
    cod_entidade            INTEGER     NOT NULL,
    exercicio_anulacao      CHAR(4)     NOT NULL,
    cod_empenho_anulacao    INTEGER     NOT NULL,
    timestamp_anulacao      TIMESTAMP   NOT NULL,
    CONSTRAINT pk_lancamento_empenho_anulado    PRIMARY KEY                                 (exercicio, cod_lote, tipo, sequencia, cod_entidade, exercicio_anulacao, cod_empenho_anulacao, timestamp_anulacao),
    CONSTRAINT fk_lancamento_empenho_anulado_1  FOREIGN KEY                                 (exercicio, cod_lote, tipo, sequencia, cod_entidade)
                                                REFERENCES contabilidade.lancamento_empenho (exercicio, cod_lote, tipo, sequencia, cod_entidade),
    CONSTRAINT fk_lancamento_empenho_anulado_2  FOREIGN KEY                                 (exercicio_anulacao, cod_empenho_anulacao, cod_entidade, timestamp_anulacao)
                                                REFERENCES empenho.empenho_anulado          (exercicio, cod_empenho, cod_entidade, timestamp)
);
GRANT ALL ON contabilidade.lancamento_empenho_anulado TO urbem;

    INSERT
      INTO contabilidade.lancamento_empenho_anulado
         ( exercicio
         , cod_lote
         , tipo
         , sequencia
         , cod_entidade
         , exercicio_anulacao
         , cod_empenho_anulacao
         , timestamp_anulacao
         )
    SELECT lancamento_empenho.exercicio
         , lancamento_empenho.cod_lote
         , lancamento_empenho.tipo
         , lancamento_empenho.sequencia
         , lancamento_empenho.cod_entidade
         , empenho_anulado.exercicio    AS exercicio_anulado
         , empenho_anulado.cod_empenho
         , empenho_anulado.timestamp    AS timestamp_anulado
      FROM contabilidade.valor_lancamento
      JOIN contabilidade.lancamento
        ON valor_lancamento.exercicio    = lancamento.exercicio
       AND valor_lancamento.cod_lote     = lancamento.cod_lote
       AND valor_lancamento.tipo         = lancamento.tipo
       AND valor_lancamento.sequencia    = lancamento.sequencia
       AND valor_lancamento.cod_entidade = lancamento.cod_entidade
      JOIN contabilidade.lancamento_empenho
        ON valor_lancamento.exercicio    = lancamento_empenho.exercicio
       AND valor_lancamento.cod_lote     = lancamento_empenho.cod_lote
       AND valor_lancamento.tipo         = lancamento_empenho.tipo
       AND valor_lancamento.sequencia    = lancamento_empenho.sequencia
       AND valor_lancamento.cod_entidade = lancamento_empenho.cod_entidade
      JOIN contabilidade.empenhamento
        ON lancamento_empenho.exercicio    = empenhamento.exercicio
       AND lancamento_empenho.cod_lote     = empenhamento.cod_lote
       AND lancamento_empenho.tipo         = empenhamento.tipo
       AND lancamento_empenho.sequencia    = empenhamento.sequencia
       AND lancamento_empenho.cod_entidade = empenhamento.cod_entidade
      JOIN empenho.empenho
        ON empenhamento.exercicio    = empenho.exercicio
       AND empenhamento.cod_entidade = empenho.cod_entidade
       AND empenhamento.cod_empenho  = empenho.cod_empenho
      JOIN empenho.empenho_anulado
        ON empenho.exercicio    = empenho_anulado.exercicio
       AND empenho.cod_entidade = empenho_anulado.cod_entidade
       AND empenho.cod_empenho  = empenho_anulado.cod_empenho
      JOIN contabilidade.lote
        ON lancamento.exercicio    = lote.exercicio
       AND lancamento.cod_entidade = lote.cod_entidade
       AND lancamento.tipo         = lote.tipo
       AND lancamento.cod_lote     = lote.cod_lote
      JOIN (
             SELECT empenho_anulado_item.exercicio
                  , empenho_anulado_item.cod_entidade
                  , empenho_anulado_item.cod_empenho
                  , empenho_anulado_item.timestamp
                  , SUM(empenho_anulado_item.vl_anulado) AS vl_anulado
               FROM empenho.empenho_anulado_item
               JOIN empenho.empenho_anulado
                 ON empenho_anulado_item.exercicio    = empenho_anulado.exercicio
                AND empenho_anulado_item.cod_entidade = empenho_anulado.cod_entidade
                AND empenho_anulado_item.cod_empenho  = empenho_anulado.cod_empenho
                AND empenho_anulado_item.timestamp    = empenho_anulado.timestamp
                GROUP BY empenho_anulado_item.exercicio
                  , empenho_anulado_item.cod_entidade
                  , empenho_anulado_item.cod_empenho
                  , empenho_anulado_item.timestamp
           ) AS anulacao
        ON empenho_anulado.exercicio    = anulacao.exercicio
       AND empenho_anulado.cod_entidade = anulacao.cod_entidade
       AND empenho_anulado.cod_empenho  = anulacao.cod_empenho
       AND empenho_anulado.timestamp    = anulacao.timestamp
     WHERE anulacao.vl_anulado             = valor_lancamento.vl_lancamento
       AND empenho_anulado.timestamp::DATE = lote.dt_lote
       AND lancamento.cod_historico        = 904
       AND lancamento_empenho.estorno      IS TRUE
       AND valor_lancamento.tipo_valor     ='D'
  GROUP BY lancamento_empenho.exercicio
         , lancamento_empenho.cod_lote
         , lancamento_empenho.tipo
         , lancamento_empenho.sequencia
         , lancamento_empenho.cod_entidade
         , empenho_anulado.exercicio
         , empenho_anulado.cod_empenho
         , empenho_anulado.timestamp
         ;


----------------
-- Ticket #24004
----------------

CREATE TABLE tcemg.transferencia_tipo_documento (
    cod_tipo_documento  INTEGER             NOT NULL,
    num_documento       varchar(15)         NOT NULL,
    cod_entidade        INTEGER             NOT NULL,
    exercicio           VARCHAR(4)          NOT NULL,
    cod_lote            INTEGER             NOT NULL,
    tipo                varchar(1)          NOT NULL,
    CONSTRAINT pk_transferencia_tipo_documento   PRIMARY KEY     (exercicio, cod_entidade, cod_lote, tipo, cod_tipo_documento),
    CONSTRAINT fk_transferencia_tipo_documento_1 FOREIGN KEY                         (cod_entidade, exercicio, cod_lote, tipo)
                                                 REFERENCES tesouraria.transferencia (cod_entidade, exercicio, cod_lote, tipo),
    CONSTRAINT fk_transferencia_tipo_documento_2 FOREIGN KEY                         (cod_tipo_documento)
                                                 REFERENCES tcemg.tipo_documento     (cod_tipo)
);
GRANT ALL ON tcemg.transferencia_tipo_documento TO GROUP urbem;


----------------
-- Ticket #24024
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

    reRecord    RECORD;
    stSQL       VARCHAR;
    stFiltroFK  VARCHAR;
    stTemp      VARCHAR;

BEGIN

    stSQL := '
                SELECT exercicio
                  FROM contabilidade.historico_contabil
                 WHERE ( select hc.exercicio
                           from contabilidade.historico_contabil as hc
                          where hc.cod_historico in (850)
                            and hc.exercicio = historico_contabil.exercicio
                       group by hc.exercicio
                       ) IS NULL
              GROUP BY exercicio order by exercicio;
    ';

    FOR reRecord IN EXECUTE stSQL LOOP

         INSERT
           INTO contabilidade.historico_contabil
         VALUES ( 850
                , reRecord.exercicio
                , 'Previsão de crédito tributário a receber'
                , FALSE
                , FALSE
                );

    END LOOP;
END;

$$ LANGUAGE 'plpgsql';

SELECT manutencao();
DROP FUNCTION manutencao();

